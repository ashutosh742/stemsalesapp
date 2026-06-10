<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProposalPitchAdvisor_model - Agent (additive, 2026-06-06)
 *
 * Builds a rule-based pitch brief for a single deal (init_call lead) so the BD
 * walks into the proposal meeting prepared. Pulls REAL data only:
 *   - company_master  : company background, budget band, potential flags
 *   - init_call       : proposal_type, proposal_amt, fbudget, noofschools, kcremark
 *   - types_of_proposal : human label for the proposal type
 *   - tblcallevents   : last 3 meetings (date, type, purpose, mom/remarks)
 *   - kcremark + meeting remarks : objection signals
 *
 * No LLM. No mock data. ASCII only. "Rs" for rupees. "percent" spelled out.
 */
class ProposalPitchAdvisor_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function manifest() {
        $pitchable = (int)$this->db->query(
            "SELECT COUNT(*) c FROM init_call
             WHERE cstatus IN (6,7,9) AND mainbd > 0"
        )->row()->c;
        return array(
            'feature'        => 'proposal_pitch_advisor',
            'mode'           => 'rule_based',
            'source_tables'  => array('company_master','init_call','types_of_proposal','tblcallevents'),
            'pitchable_open' => $pitchable,
            'deployed_at'    => '2026-06-06',
        );
    }

    /** Full pitch brief for one deal. Returns null if lead not found. */
    public function advise($lead_id) {
        $lead_id = (int)$lead_id;
        $r = $this->db->query("
            SELECT ic.id, ic.cmpid_id, ic.mainbd, ic.cstatus, ic.lead_source,
                   ic.proposal_type, ic.proposal_amt, ic.fbudget, ic.noofschools,
                   ic.kcremark, ic.focus_funnel,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   cm.budget AS company_budget, cm.city, cm.state, cm.district,
                   cm.comp_business_potential, cm.comp_top_spender, cm.comp_key_company,
                   cm.partnerType_id
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.id = ? LIMIT 1", array($lead_id))->row_array();
        if (!$r) return null;

        $proposal_label = $this->_proposal_type_label($r['proposal_type']);
        $amt            = $this->_parse_amount($r['proposal_amt']);
        $budget_band    = $this->_budget_band($amt, $r['fbudget'], $r['company_budget']);
        $meetings       = $this->_last_meetings($lead_id, 3);
        $objections     = $this->_objection_signals($r, $meetings);
        $talking_points = $this->_talking_points($r, $proposal_label, $budget_band, $objections);

        return array(
            'lead_id'        => (int)$r['id'],
            'company'        => $r['company'],
            'location'       => trim(implode(', ', array_filter(array($r['city'], $r['district'], $r['state'])))),
            'stage'          => $this->_stage_label($r['cstatus']),
            'lead_source'    => $r['lead_source'],
            'proposal_type'  => $proposal_label,
            'proposal_amount'=> $amt > 0 ? ('Rs ' . number_format($amt)) : 'not quoted yet',
            'budget_band'    => $budget_band,
            'no_of_schools'  => (int)$r['noofschools'],
            'company_flags'  => $this->_company_flags($r),
            'last_meetings'  => $meetings,
            'objections'     => $objections,
            'talking_points' => $talking_points,
        );
    }

    /** Ranked list of pitchable deals for a BD. */
    public function for_bd($bd_uid, $limit = 20) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        $rows = $this->db->query("
            SELECT ic.id,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   ic.cstatus, ic.proposal_type, ic.proposal_amt, ic.noofschools,
                   (SELECT COUNT(*) FROM tblcallevents t WHERE t.cid_id = ic.id) AS meeting_count,
                   (SELECT MAX(t2.date) FROM tblcallevents t2 WHERE t2.cid_id = ic.id) AS last_meeting_at
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.mainbd = ? AND ic.cstatus IN (6,7,9)
            ORDER BY ic.cstatus DESC, last_meeting_at DESC
            LIMIT ?", array($bd_uid, $limit))->result_array();

        $out = array();
        foreach ($rows as $row) {
            $amt = $this->_parse_amount($row['proposal_amt']);
            $out[] = array(
                'lead_id'         => (int)$row['id'],
                'company'         => $row['company'],
                'stage'           => $this->_stage_label($row['cstatus']),
                'proposal_type'   => $this->_proposal_type_label($row['proposal_type']),
                'proposal_amount' => $amt > 0 ? ('Rs ' . number_format($amt)) : 'not quoted yet',
                'no_of_schools'   => (int)$row['noofschools'],
                'meeting_count'   => (int)$row['meeting_count'],
                'last_meeting_at' => $row['last_meeting_at'],
                'readiness'       => $this->_readiness($row['meeting_count'], $amt),
            );
        }
        return $out;
    }

    // ---------------- helpers ----------------

    private function _last_meetings($lead_id, $n) {
        $rows = $this->db->query("
            SELECT t.id, t.date, t.meeting_type, t.mtype, t.purpose_achieved,
                   t.next_step_confirmation, t.nextaction, t.remarks, t.special_remarks
            FROM tblcallevents t
            WHERE t.cid_id = ?
            ORDER BY t.date DESC
            LIMIT ?", array((int)$lead_id, (int)$n))->result_array();
        $out = array();
        foreach ($rows as $m) {
            $out[] = array(
                'date'            => $m['date'],
                'type'            => $m['meeting_type'] ?: ($m['mtype'] ?: 'meeting'),
                'purpose_achieved'=> $m['purpose_achieved'],
                'next_step'       => $this->_clip($m['nextaction'] ?: $m['next_step_confirmation'], 160),
                'notes'           => $this->_clip($m['special_remarks'] ?: $m['remarks'], 200),
            );
        }
        return $out;
    }

    /** Heuristic objection detection from kcremark + meeting notes. */
    private function _objection_signals($lead, $meetings) {
        $blob = strtolower(' ' . (string)$lead['kcremark']);
        foreach ($meetings as $m) {
            $blob .= ' ' . strtolower((string)$m['notes'] . ' ' . (string)$m['next_step']);
        }
        $themes = array(
            'budget'      => array('budget','costly','expensive','price','cheaper','afford','funds'),
            'timing'      => array('next year','next quarter','later','not now','after exam','postpone','defer'),
            'authority'   => array('principal','trust','committee','approval','decision maker','board','sanction'),
            'competition' => array('other vendor','competitor','already using','existing supplier','another company'),
            'value_doubt' => array('not sure','benefit','roi','why','proof','convince','outcome'),
            'logistics'   => array('space','room','training','maintenance','technician','install'),
            'procurement' => array('tender','gem','quotation','three quotes','procurement','process'),
        );
        $hits = array();
        foreach ($themes as $code => $words) {
            foreach ($words as $w) {
                if (strpos($blob, $w) !== false) { $hits[] = $code; break; }
            }
        }
        return array_values(array_unique($hits));
    }

    private function _talking_points($lead, $proposal_label, $budget_band, $objections) {
        $tp = array();
        $tp[] = 'Lead with the outcome: position the ' . $proposal_label . ' around measurable student learning, not features.';
        if ($budget_band !== 'unknown') {
            $tp[] = 'Budget read is ' . $budget_band . '. Frame price as cost per student per year and offer a phased rollout instead of a flat discount.';
        }
        if ((int)$lead['noofschools'] > 1) {
            $tp[] = 'Multi-site opportunity (' . (int)$lead['noofschools'] . ' schools). Propose a pilot at one site with a clear scale-up plan.';
        }
        if (in_array('authority', $objections, true)) {
            $tp[] = 'Decision sits higher up. Ask who signs off and aim to get the real approver into the next meeting.';
        }
        if (in_array('competition', $objections, true)) {
            $tp[] = 'Incumbent vendor in play. Ask about gaps in the current setup and offer a side-by-side trial module.';
        }
        if (in_array('procurement', $objections, true)) {
            $tp[] = 'Procurement or tender route. Offer to be one of the compliant quotes and align specs to favour the STEM package.';
        }
        if (in_array('timing', $objections, true)) {
            $tp[] = 'Buyer wants to defer. Surface the cost of delay and propose a small commitment this term.';
        }
        if (in_array('logistics', $objections, true)) {
            $tp[] = 'Space or training worry. Offer a compact or mobile lab option plus teacher training and ongoing support.';
        }
        if (empty($objections)) {
            $tp[] = 'No strong objection on record. Confirm decision criteria and timeline, then ask for the close.';
        }
        return $tp;
    }

    private function _company_flags($r) {
        $f = array();
        if ($r['comp_top_spender'] == 1 || $r['comp_top_spender'] === '1') $f[] = 'top_spender';
        if ($r['comp_key_company'] == 1 || $r['comp_key_company'] === '1') $f[] = 'key_company';
        if ($r['comp_business_potential'] == 1 || $r['comp_business_potential'] === '1') $f[] = 'high_potential';
        return $f;
    }

    private function _proposal_type_label($type) {
        $type = trim((string)$type);
        if ($type === '' || strtoupper($type) === 'NA') return 'STEM proposal';
        if (is_numeric($type)) {
            $row = $this->db->query("SELECT type FROM types_of_proposal WHERE id = ? LIMIT 1", array((int)$type))->row_array();
            if ($row && !empty($row['type'])) return $row['type'];
        }
        return $type;
    }

    private function _parse_amount($raw) {
        $raw = strtoupper(trim((string)$raw));
        if ($raw === '' || $raw === 'NA' || $raw === '0') return 0;
        $num = (float)preg_replace('/[^0-9.]/', '', $raw);
        if ($num <= 0) return 0;
        if (strpos($raw, 'CR') !== false) return (int)round($num * 10000000);
        if (strpos($raw, 'LK') !== false || strpos($raw, 'LAC') !== false || strpos($raw, 'LAKH') !== false) return (int)round($num * 100000);
        return (int)round($num);
    }

    private function _budget_band($amt, $fbudget, $company_budget) {
        if ($amt <= 0) {
            $fb = $this->_parse_amount($fbudget ?: $company_budget);
            $amt = $fb;
        }
        if ($amt <= 0) return 'unknown';
        if ($amt < 200000)   return 'small (under Rs 2 lakh)';
        if ($amt < 1000000)  return 'mid (Rs 2 to 10 lakh)';
        if ($amt < 5000000)  return 'large (Rs 10 to 50 lakh)';
        return 'strategic (Rs 50 lakh plus)';
    }

    private function _readiness($meeting_count, $amt) {
        if ($meeting_count >= 2 && $amt > 0) return 'ready_to_pitch';
        if ($meeting_count >= 1)             return 'needs_one_more_meeting';
        return 'early_discovery';
    }

    private function _stage_label($cstatus) {
        $map = array(6 => 'positive', 7 => 'closure', 9 => 'very positive');
        return isset($map[(int)$cstatus]) ? $map[(int)$cstatus] : ('status ' . (int)$cstatus);
    }

    private function _clip($s, $n) {
        $s = trim((string)$s);
        if ($s === '') return '';
        return strlen($s) > $n ? substr($s, 0, $n) . '...' : $s;
    }
}
