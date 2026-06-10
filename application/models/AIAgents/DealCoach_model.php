<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DealCoach_model - Agent (additive, 2026-06-06)
 *
 * Gives stage-specific coaching tips for a single deal (init_call lead) and a
 * ranked list of coachable deals for a BD. Derived from real init_call data
 * plus the proposal table. No mock data.
 *
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class DealCoach_model extends CI_Model {

    public function manifest() {
        $pos = (int)$this->db->query("SELECT COUNT(*) c FROM init_call WHERE positive = 1 AND open <> '' AND open <> 'NA' AND open <> '0'")->row()->c;
        return array(
            'feature'       => 'deal_coach',
            'source_tables' => array('init_call','proposal'),
            'positive_open' => $pos,
            'deployed_at'   => '2026-06-06',
        );
    }

    /** Coaching for one deal. Returns null if lead not found. */
    public function coach_deal($lead_id) {
        $lead_id = (int)$lead_id;
        $r = $this->db->query("
            SELECT ic.id, COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   ic.cstatus, ic.positive, ic.verypositive, ic.closure, ic.closure_pipeline,
                   ic.proposal_to_be_sent_target, COALESCE(ic.proposal_amt,0) AS proposal_amt,
                   DATEDIFF(NOW(), COALESCE(ic.updated_at, ic.createDate)) AS days_idle,
                   (SELECT COUNT(*) FROM proposal p WHERE p.init_id = ic.id) AS proposal_count
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.id = ? LIMIT 1", array($lead_id))->row_array();
        if (!$r) return null;

        $stage = $this->_stage($r);
        return array(
            'lead_id'        => (int)$r['id'],
            'company'        => $r['company'],
            'stage'          => $stage,
            'days_idle'      => (int)$r['days_idle'],
            'proposal_amt'   => (float)$r['proposal_amt'],
            'proposal_count' => (int)$r['proposal_count'],
            'tips'           => $this->_tips($stage, $r),
        );
    }

    /** Top coachable deals for a BD (positive/proposal/closure, open). */
    public function for_bd($bd_uid, $limit = 20) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        $params = array();
        $bd_filter = '';
        if ($bd_uid > 0) { $bd_filter = " AND (ic.mainbd = ? OR ic.insidebd = ?)"; $params = array($bd_uid,$bd_uid); }
        $sql = "
            SELECT ic.id, COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   ic.positive, ic.verypositive, ic.closure, ic.closure_pipeline,
                   ic.proposal_to_be_sent_target, COALESCE(ic.proposal_amt,0) AS proposal_amt,
                   DATEDIFF(NOW(), COALESCE(ic.updated_at, ic.createDate)) AS days_idle,
                   (SELECT COUNT(*) FROM proposal p WHERE p.init_id = ic.id) AS proposal_count
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE (ic.open <> '' AND ic.open <> 'NA' AND ic.open <> '0') AND (ic.positive = 1 OR ic.verypositive = 1 OR ic.closure = 1)
              $bd_filter
            ORDER BY ic.verypositive DESC, ic.closure DESC, ic.positive DESC, ic.proposal_amt DESC
            LIMIT ?";
        $params[] = $limit;
        $rows = $this->db->query($sql, $params)->result_array();
        $out = array();
        foreach ($rows as $r) {
            $stage = $this->_stage($r);
            $out[] = array(
                'lead_id'      => (int)$r['id'],
                'company'      => $r['company'],
                'stage'        => $stage,
                'proposal_amt' => (float)$r['proposal_amt'],
                'days_idle'    => (int)$r['days_idle'],
                'top_tip'      => $this->_tips($stage, $r)[0],
            );
        }
        return $out;
    }

    private function _stage($r) {
        if ((int)$r['closure'] === 1 || (int)$r['closure_pipeline'] === 1) return 'closure';
        if ((int)$r['verypositive'] === 1) return 'late_stage';
        if ((int)$r['proposal_to_be_sent_target'] === 1 || (int)$r['proposal_count'] > 0) return 'proposal';
        if ((int)$r['positive'] === 1) return 'qualification';
        return 'discovery';
    }

    private function _tips($stage, $r) {
        $idle = (int)$r['days_idle'];
        $base = array(
            'discovery'     => array('Confirm the decision maker and budget range.', 'Book a needs-assessment meeting this week.'),
            'qualification' => array('Quantify the impact for the company in Rs terms.', 'Agree on a proposal timeline with the client.'),
            'proposal'      => array('Submit or revise the proposal; attach the budget.', 'Set a review date for proposal feedback.'),
            'late_stage'    => array('Lock the closing meeting with all stakeholders.', 'Pre-empt objections on price and timeline.'),
            'closure'       => array('Drive the sign-off and work order.', 'Confirm contract value and start date.'),
        );
        $tips = isset($base[$stage]) ? $base[$stage] : $base['discovery'];
        if ($idle >= 30) array_unshift($tips, "Deal has been idle for {$idle} days; re-engage before it cools.");
        return array_values($tips);
    }
}
