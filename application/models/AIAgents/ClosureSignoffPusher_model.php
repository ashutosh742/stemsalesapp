<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ClosureSignoffPusher_model - Agent (additive, 2026-06-06)
 *
 * Finds stalled closure-stage leads (init_call.cstatus IN 7,9,13) and builds a
 * Cluster Manager / manager sign-off push list. On commit it queues a nudge row
 * into comm_outbox (status 'queued') addressed to the BD manager so the manager
 * acts on the stalled closure.
 *
 * Real data only. No LLM. ASCII only. "Rs" for rupees. "percent" spelled out.
 * Safe by default: preview unless commit = true.
 */
class ClosureSignoffPusher_model extends CI_Model {

    // Closure-stage codes that can stall waiting for manager sign-off.
    private $closure_stages = array(7, 9, 13);

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function manifest() {
        $stalled = (int)$this->db->query("
            SELECT COUNT(*) c FROM init_call
            WHERE cstatus IN (7,9,13) AND mainbd > 0
              AND DATEDIFF(NOW(), COALESCE(updated_at, createDate)) >= 7"
        )->row()->c;
        return array(
            'feature'        => 'closure_signoff_pusher',
            'mode'           => 'rule_based',
            'source_tables'  => array('init_call','company_master','user','comm_outbox'),
            'stalled_closures'=> $stalled,
            'min_idle_days'  => 7,
            'deployed_at'    => '2026-06-06',
        );
    }

    /**
     * Build the sign-off push list. If $commit is true, queue rows into comm_outbox.
     * $manager_uid optional filter; $min_idle days threshold (default 7).
     */
    public function push_list($manager_uid = 0, $min_idle = 7, $limit = 50, $commit = false) {
        $manager_uid = (int)$manager_uid; $min_idle = (int)$min_idle; $limit = (int)$limit;

        $where_mgr = $manager_uid > 0 ? ' AND mgr.uid = ' . $manager_uid : '';
        $rows = $this->db->query("
            SELECT ic.id, ic.mainbd, ic.cstatus, ic.proposal_amt, ic.dm_contact_email,
                   ic.dm_contact_name,
                   DATEDIFF(NOW(), COALESCE(ic.updated_at, ic.createDate)) AS days_idle,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   bd.name AS bd_name, bd.admin_id AS manager_uid,
                   mgr.name AS manager_name
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN user bd  ON bd.uid = ic.mainbd
            LEFT JOIN user mgr ON mgr.uid = bd.admin_id
            WHERE ic.cstatus IN (7,9,13) AND ic.mainbd > 0
              AND DATEDIFF(NOW(), COALESCE(ic.updated_at, ic.createDate)) >= {$min_idle}
              {$where_mgr}
            ORDER BY days_idle DESC
            LIMIT {$limit}
        ")->result_array();

        $items = array();
        $queued = 0;
        foreach ($rows as $r) {
            $stage = $this->_stage_label($r['cstatus']);
            $subject = 'Sign-off needed: ' . $r['company'] . ' (' . $stage . ', idle ' . (int)$r['days_idle'] . ' days)';
            $body = $this->_body($r, $stage);
            $to_email = $this->_manager_email($r['manager_uid']);

            $item = array(
                'lead_id'      => (int)$r['id'],
                'company'      => $r['company'],
                'stage'        => $stage,
                'days_idle'    => (int)$r['days_idle'],
                'bd_name'      => $r['bd_name'],
                'manager_uid'  => (int)$r['manager_uid'],
                'manager_name' => $r['manager_name'],
                'subject'      => $subject,
                'queued'       => false,
            );

            if ($commit && $to_email) {
                $this->db->insert('comm_outbox', array(
                    'from_uid'  => (int)$r['mainbd'],
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
            $items[] = $item;
        }

        return array(
            'min_idle_days' => $min_idle,
            'committed'     => (bool)$commit,
            'queued_count'  => $queued,
            'count'         => count($items),
            'items'         => $items,
        );
    }

    private function _body($r, $stage) {
        $amt = $this->_parse_amount($r['proposal_amt']);
        $amt_str = $amt > 0 ? ('Rs ' . number_format($amt)) : 'amount not quoted';
        $lines = array();
        $lines[] = 'Closure sign-off reminder from STEM CRM.';
        $lines[] = '';
        $lines[] = 'Company: ' . $r['company'];
        $lines[] = 'Stage: ' . $stage;
        $lines[] = 'Days idle: ' . (int)$r['days_idle'];
        $lines[] = 'Proposal amount: ' . $amt_str;
        $lines[] = 'Owning BD: ' . $r['bd_name'];
        if (!empty($r['dm_contact_name'])) {
            $lines[] = 'Decision maker: ' . $r['dm_contact_name'];
        }
        $lines[] = '';
        $lines[] = 'This deal is in a closure stage and has not moved for ' . (int)$r['days_idle'] . ' days.';
        $lines[] = 'Please review and provide sign-off or coach the BD on the next step.';
        return implode("\n", $lines);
    }

    private function _manager_email($manager_uid) {
        $manager_uid = (int)$manager_uid;
        if ($manager_uid <= 0) return '';
        $row = $this->db->query("SELECT email FROM user WHERE uid = ? LIMIT 1", array($manager_uid))->row_array();
        if ($row && !empty($row['email'])) return $row['email'];
        // Fall back to username if it looks like an email.
        $row2 = $this->db->query("SELECT username FROM user WHERE uid = ? LIMIT 1", array($manager_uid))->row_array();
        if ($row2 && !empty($row2['username']) && strpos($row2['username'], '@') !== false) return $row2['username'];
        return '';
    }

    private function _stage_label($cstatus) {
        $map = array(7 => 'closure', 9 => 'very positive', 13 => 'closure pipeline');
        return isset($map[(int)$cstatus]) ? $map[(int)$cstatus] : ('status ' . (int)$cstatus);
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
}
