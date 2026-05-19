<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 022 - Escalation Ticket Model
 *
 * Backs the escalation_ticket table seeded in stem_migration_022_sql.sql.
 * SLA windows are read from escalation_reason_sla. Eight reason codes seeded:
 *   missing_proposal, r2b_blocked, csr_doubtful, csr_not_csr, work_order_stuck,
 *   payment_terms_stuck, signoff_stuck_48h, manual_other.
 *
 * Lifecycle:
 *   open -> in_progress -> resolved
 *                       \-> escalated_to_rm (auto on SLA breach)
 *                       \-> escalated_to_director (auto on second SLA breach)
 *
 * Plain English. 'Rs' for rupees. No em-dashes.
 */
class EscalationTicket_model extends CI_Model {

    private $T_TICKET = 'escalation_ticket';
    private $T_SLA    = 'escalation_reason_sla';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* ---------- helpers ---------- */
    private function _sla_for($reason_code) {
        $row = $this->db->get_where($this->T_SLA, array('reason_code' => $reason_code))->row_array();
        if (empty($row)) {
            return array('sla_hours' => 24, 'auto_escalate_to' => 'rm');
        }
        return $row;
    }

    private function _now() { return date('Y-m-d H:i:s'); }

    /* ---------- open ---------- */
    public function open_ticket($lead_id, $opened_by_uid, $reason_code, $note = '', $assignee_uid = null) {
        $sla = $this->_sla_for($reason_code);
        $sla_hours = (int)$sla['sla_hours'];

        $row = array(
            'lead_id'       => (int)$lead_id,
            'opened_by_uid' => (int)$opened_by_uid,
            'reason_code'   => $reason_code,
            'note'          => $note,
            'assignee_uid'  => $assignee_uid ? (int)$assignee_uid : null,
            'status'        => 'open',
            'sla_hours'     => $sla_hours,
            'opened_at'     => $this->_now(),
            'sla_deadline'  => date('Y-m-d H:i:s', strtotime('+' . $sla_hours . ' hours'))
        );
        $this->db->insert($this->T_TICKET, $row);
        $id = $this->db->insert_id();
        return array('ok' => true, 'ticket_id' => $id, 'sla_deadline' => $row['sla_deadline']);
    }

    /* ---------- handover ---------- */
    public function handover($ticket_id, $new_assignee_uid, $by_uid, $note = '') {
        $t = $this->db->get_where($this->T_TICKET, array('id' => $ticket_id))->row_array();
        if (empty($t)) return array('error' => 'ticket not found');
        if ($t['status'] === 'resolved') return array('error' => 'ticket already resolved');

        $this->db->where('id', $ticket_id)->update($this->T_TICKET, array(
            'assignee_uid'  => (int)$new_assignee_uid,
            'status'        => 'in_progress',
            'last_action_by'=> (int)$by_uid,
            'last_action_at'=> $this->_now(),
            'handover_note' => $note
        ));
        return array('ok' => true);
    }

    /* ---------- resolve ---------- */
    public function resolve($ticket_id, $by_uid, $resolution_note) {
        $t = $this->db->get_where($this->T_TICKET, array('id' => $ticket_id))->row_array();
        if (empty($t)) return array('error' => 'ticket not found');
        if ($t['status'] === 'resolved') return array('error' => 'already resolved');

        $resolved_at = $this->_now();
        $breached = (strtotime($resolved_at) > strtotime($t['sla_deadline'])) ? 1 : 0;

        $this->db->where('id', $ticket_id)->update($this->T_TICKET, array(
            'status'          => 'resolved',
            'resolved_by_uid' => (int)$by_uid,
            'resolution_note' => $resolution_note,
            'resolved_at'     => $resolved_at,
            'sla_breached'    => $breached
        ));
        return array('ok' => true, 'sla_breached' => $breached);
    }

    /* ---------- queue ---------- */
    public function queue($assignee_uid = null, $status = 'open', $limit = 50) {
        $q = $this->db->select('et.*, ic.school_name, ic.compny_nm, u.first_name, u.last_name');
        $q = $this->db->from($this->T_TICKET . ' et');
        $this->db->join('init_call ic', 'ic.id = et.lead_id', 'left');
        $this->db->join('user u', 'u.uid = et.opened_by_uid', 'left');
        if ($status !== 'all') $this->db->where('et.status', $status);
        if (!empty($assignee_uid)) $this->db->where('et.assignee_uid', (int)$assignee_uid);
        $this->db->order_by('et.sla_deadline', 'ASC')->limit($limit);
        return $this->db->get()->result_array();
    }

    /* ---------- breached scan ---------- */
    public function scan_breached() {
        $now = $this->_now();
        $rows = $this->db->where('status !=', 'resolved')
            ->where('sla_deadline <', $now)
            ->where('auto_escalated', 0)
            ->get($this->T_TICKET)->result_array();

        $escalated = 0;
        foreach ($rows as $t) {
            $sla = $this->_sla_for($t['reason_code']);
            $next_role = $sla['auto_escalate_to'];

            $upd = array(
                'auto_escalated'    => 1,
                'escalated_to_role' => $next_role,
                'escalated_at'      => $now,
                'status'            => ($next_role === 'rm') ? 'escalated_to_rm' : 'escalated_to_director'
            );
            $this->db->where('id', $t['id'])->update($this->T_TICKET, $upd);
            $escalated++;
        }
        return array('escalated' => $escalated, 'scanned_at' => $now);
    }
}
