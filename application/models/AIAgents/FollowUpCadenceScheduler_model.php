<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FollowUpCadenceScheduler_model - Agent (additive, 2026-06-06)
 *
 * For a BD's open deals, derives a risk tier and proposes a timed follow-up
 * cadence (touch plan). Risk comes from stall_risk_score when present, otherwise
 * from a fallback based on days idle and stage. On commit it can queue the first
 * touch into comm_outbox.
 *
 * Real data only. No LLM. ASCII only. "Rs" for rupees. "percent" spelled out.
 * Safe by default: preview unless commit = true.
 */
class FollowUpCadenceScheduler_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function manifest() {
        $scored = (int)$this->db->query("SELECT COUNT(*) c FROM stall_risk_score")->row()->c;
        $open   = (int)$this->db->query("
            SELECT COUNT(*) c FROM init_call
            WHERE cstatus IN (6,7,9,13) AND mainbd > 0"
        )->row()->c;
        return array(
            'feature'        => 'follow_up_cadence_scheduler',
            'mode'           => 'rule_based',
            'source_tables'  => array('init_call','stall_risk_score','comm_outbox','crm_email_logs','whatsapp_send_v2'),
            'risk_scores'    => $scored,
            'open_deals'     => $open,
            'deployed_at'    => '2026-06-06',
        );
    }

    /**
     * Build a follow-up cadence plan for one BD's open deals.
     * If $commit is true, queue the first due touch into comm_outbox.
     */
    public function plan_for_bd($bd_uid, $limit = 20, $commit = false) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        $rows = $this->db->query("
            SELECT ic.id, ic.cstatus, ic.proposal_amt, ic.dm_contact_email, ic.dm_contact_name,
                   DATEDIFF(NOW(), COALESCE(ic.updated_at, ic.createDate)) AS days_idle,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   srs.bucket AS risk_bucket, srs.score_total
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN stall_risk_score srs ON srs.cid_id = ic.id
            WHERE ic.mainbd = ? AND ic.cstatus IN (6,7,9,13)
            ORDER BY ic.cstatus DESC, days_idle DESC
            LIMIT ?", array($bd_uid, $limit))->result_array();

        $items = array();
        $queued = 0;
        foreach ($rows as $r) {
            $risk = $this->_risk_tier($r['risk_bucket'], (int)$r['days_idle'], (int)$r['cstatus']);
            $cadence = $this->_cadence($risk);
            $touches = $this->_touches($cadence);
            $last_contact = $this->_last_contact((int)$r['id'], $r['dm_contact_email']);

            $item = array(
                'lead_id'        => (int)$r['id'],
                'company'        => $r['company'],
                'stage'          => $this->_stage_label($r['cstatus']),
                'days_idle'      => (int)$r['days_idle'],
                'risk_tier'      => $risk,
                'cadence_days'   => $cadence,
                'next_touches'   => $touches,
                'last_contact'   => $last_contact,
                'queued'         => false,
            );

            if ($commit) {
                $to_email = !empty($r['dm_contact_email']) ? $r['dm_contact_email'] : '';
                if ($to_email && filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                    $subject = 'Following up on ' . $r['company'];
                    $body = $this->_touch_body($r, $risk, $touches[0]);
                    $this->db->insert('comm_outbox', array(
                        'from_uid'  => $bd_uid,
                        'to_email'  => $to_email,
                        'subject'   => $subject,
                        'body_text' => $body,
                        'cid_id'    => (int)$r['id'],
                        'status'    => 'queued',
                    ));
                    $item['queued'] = true;
                    $item['outbox_id'] = (int)$this->db->insert_id();
                    $queued++;
                }
            }
            $items[] = $item;
        }

        return array(
            'bd_uid'       => $bd_uid,
            'committed'    => (bool)$commit,
            'queued_count' => $queued,
            'count'        => count($items),
            'items'        => $items,
        );
    }

    // ---------------- helpers ----------------

    private function _risk_tier($bucket, $days_idle, $cstatus) {
        $bucket = strtoupper((string)$bucket);
        if ($bucket === 'CRITICAL') return 'critical';
        if ($bucket === 'AT_RISK')  return 'high';
        if ($bucket === 'WATCH')    return 'medium';
        if ($bucket === 'HEALTHY')  return 'low';
        // Fallback when no churn/stall score exists.
        if ($days_idle >= 30) return 'high';
        if ($days_idle >= 14) return 'medium';
        return 'low';
    }

    /** Days between touches by risk tier. */
    private function _cadence($risk) {
        $map = array(
            'critical' => 1,
            'high'     => 3,
            'medium'   => 7,
            'low'      => 14,
        );
        return isset($map[$risk]) ? $map[$risk] : 7;
    }

    /** Build the next three timed touches with channel suggestions. */
    private function _touches($cadence_days) {
        $channels = array('call', 'whatsapp', 'email');
        $touches = array();
        for ($i = 1; $i <= 3; $i++) {
            $due = date('Y-m-d', strtotime('+' . ($cadence_days * $i) . ' days'));
            $touches[] = array(
                'touch'   => $i,
                'due_date'=> $due,
                'channel' => $channels[($i - 1) % count($channels)],
            );
        }
        return $touches;
    }

    private function _last_contact($lead_id, $dm_email) {
        $lead_id = (int)$lead_id;
        $last_meeting = $this->db->query("
            SELECT MAX(date) d FROM tblcallevents WHERE cid_id = ?", array($lead_id))->row_array();
        $last_wa = $this->db->query("
            SELECT MAX(queued_at) d FROM whatsapp_send_v2 WHERE to_lead_id = ?", array($lead_id))->row_array();
        return array(
            'last_meeting_at'  => $last_meeting ? $last_meeting['d'] : null,
            'last_whatsapp_at' => $last_wa ? $last_wa['d'] : null,
            'dm_email_on_file' => !empty($dm_email) ? 'yes' : 'no',
        );
    }

    private function _touch_body($r, $risk, $touch) {
        $lines = array();
        $lines[] = 'Follow-up touch (' . $risk . ' risk) for ' . $r['company'] . '.';
        $lines[] = '';
        if (!empty($r['dm_contact_name'])) $lines[] = 'Dear ' . $r['dm_contact_name'] . ',';
        $lines[] = 'Following up on our STEM proposal discussion. Happy to answer any open questions and help move this forward.';
        $lines[] = '';
        $lines[] = 'Suggested channel for this touch: ' . $touch['channel'] . '. Due by ' . $touch['due_date'] . '.';
        return implode("\n", $lines);
    }

    private function _stage_label($cstatus) {
        $map = array(6 => 'positive', 7 => 'closure', 9 => 'very positive', 13 => 'closure pipeline');
        return isset($map[(int)$cstatus]) ? $map[(int)$cstatus] : ('status ' . (int)$cstatus);
    }
}
