<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Escalation Ticket Model
 *
 * Schema-aligned 2026-06-08: rewritten to match the REAL escalation_ticket table.
 * Real columns: id, init_call_id, mom_id, opened_by_uid, reason_code (enum),
 *   reason_note, current_handler_uid, current_handler_role (enum CM/RM/SH/PST),
 *   handover_chain_json, sla_hours, opened_at, breach_at (GENERATED), resolved_at,
 *   resolution_note, status (enum open/in_progress/resolved/escalated_up/breached).
 * SLA windows from escalation_reason_sla: reason_code, default_handler_role, default_sla_hours.
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
            return array('default_sla_hours' => 24, 'default_handler_role' => 'CM');
        }
        return $row;
    }

    private function _now() { return date('Y-m-d H:i:s'); }

    /* ---------- open ----------
     * Accepts $init_call_id (a.k.a. lead_id from the caller's perspective).
     * Maps the caller's note -> reason_note, assignee -> current_handler_uid.
     * breach_at is a STORED GENERATED column, so we never insert it.
     */
    public function open_ticket($init_call_id, $opened_by_uid, $reason_code, $note = '', $assignee_uid = null) {
        $sla = $this->_sla_for($reason_code);
        $sla_hours    = (int)$sla['default_sla_hours'];
        $handler_role = isset($sla['default_handler_role']) ? $sla['default_handler_role'] : 'CM';

        $handler_uid = $assignee_uid ? (int)$assignee_uid : (int)$opened_by_uid;

        $row = array(
            'init_call_id'         => (int)$init_call_id,
            'opened_by_uid'        => (int)$opened_by_uid,
            'reason_code'          => $reason_code,
            'reason_note'          => $note,
            'current_handler_uid'  => $handler_uid,
            'current_handler_role' => $handler_role,
            'sla_hours'            => $sla_hours,
            'status'               => 'open',
            'opened_at'            => $this->_now()
        );
        $this->db->insert($this->T_TICKET, $row);
        $id = $this->db->insert_id();

        // Read back the generated breach_at for the caller.
        $t = $this->db->select('breach_at')->get_where($this->T_TICKET, array('id' => $id))->row_array();
        $breach = isset($t['breach_at']) ? $t['breach_at'] : null;

        return array('ok' => true, 'ticket_id' => (int)$id, 'sla_hours' => $sla_hours,
                     'handler_role' => $handler_role, 'breach_at' => $breach);
    }

    /* ---------- handover ---------- */
    public function handover($ticket_id, $new_assignee_uid, $by_uid, $note = '') {
        $t = $this->db->get_where($this->T_TICKET, array('id' => (int)$ticket_id))->row_array();
        if (empty($t)) return array('error' => 'ticket not found');
        if ($t['status'] === 'resolved') return array('error' => 'ticket already resolved');

        // Append to the handover chain (JSON list of moves).
        $chain = array();
        if (!empty($t['handover_chain_json'])) {
            $tmp = json_decode($t['handover_chain_json'], true);
            if (is_array($tmp)) $chain = $tmp;
        }
        $chain[] = array(
            'from_uid' => isset($t['current_handler_uid']) ? (int)$t['current_handler_uid'] : null,
            'to_uid'   => (int)$new_assignee_uid,
            'by_uid'   => (int)$by_uid,
            'note'     => $note,
            'at'       => $this->_now()
        );

        $this->db->where('id', (int)$ticket_id)->update($this->T_TICKET, array(
            'current_handler_uid' => (int)$new_assignee_uid,
            'status'              => 'in_progress',
            'handover_chain_json' => json_encode($chain),
            'updated_at'          => $this->_now()
        ));
        return array('ok' => true, 'ticket_id' => (int)$ticket_id);
    }

    /* ---------- resolve ---------- */
    public function resolve($ticket_id, $by_uid, $resolution_note) {
        $t = $this->db->get_where($this->T_TICKET, array('id' => (int)$ticket_id))->row_array();
        if (empty($t)) return array('error' => 'ticket not found');
        if ($t['status'] === 'resolved') return array('error' => 'already resolved');

        $resolved_at = $this->_now();
        $breached = (!empty($t['breach_at']) && strtotime($resolved_at) > strtotime($t['breach_at'])) ? 1 : 0;

        $this->db->where('id', (int)$ticket_id)->update($this->T_TICKET, array(
            'status'          => 'resolved',
            'resolution_note' => $resolution_note,
            'resolved_at'     => $resolved_at,
            'updated_at'      => $this->_now()
        ));
        return array('ok' => true, 'ticket_id' => (int)$ticket_id, 'sla_breached' => $breached);
    }

    /* ---------- queue ----------
     * Joins to the company via init_call.cmpid_id and to user via single name column.
     */
    public function queue($assignee_uid = null, $status = 'open', $limit = 50) {
        $this->db->select('et.*, cm.compname AS company_name, u.name AS handler_name, o.name AS opened_by_name');
        $this->db->from($this->T_TICKET . ' et');
        $this->db->join('init_call ic', 'ic.id = et.init_call_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->join('user u', 'u.uid = et.current_handler_uid', 'left');
        $this->db->join('user o', 'o.uid = et.opened_by_uid', 'left');
        if ($status !== 'all') $this->db->where('et.status', $status);
        if (!empty($assignee_uid)) $this->db->where('et.current_handler_uid', (int)$assignee_uid);
        $this->db->order_by('et.breach_at', 'ASC')->limit((int)$limit);
        return $this->db->get()->result_array();
    }

    /* ---------- breached scan ----------
     * A ticket is breached when it is past breach_at and not resolved.
     * Promotes status to 'breached' so dashboards and the queue surface it.
     */
    public function scan_breached() {
        $now = $this->_now();
        $rows = $this->db
            ->where_not_in('status', array('resolved', 'breached'))
            ->where('breach_at <', $now)
            ->where('breach_at IS NOT NULL', null, false)
            ->get($this->T_TICKET)->result_array();

        $escalated = 0;
        foreach ($rows as $t) {
            $this->db->where('id', (int)$t['id'])->update($this->T_TICKET, array(
                'status'     => 'breached',
                'updated_at' => $now
            ));
            $escalated++;
        }
        return array('ok' => true, 'breached' => $escalated, 'scanned_at' => $now);
    }
}
