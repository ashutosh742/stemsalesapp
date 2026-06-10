<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 022 - Escalation Ticket Controller
 *
 * Endpoints:
 *   POST /api/escalation/open
 *   POST /api/escalation/handover
 *   POST /api/escalation/resolve
 *   GET  /api/escalation/queue
 *   GET  /api/escalation/breached
 *   POST /api/escalation/scan_breached      (cron hook)
 *   GET  /api/escalation/probe
 */
class EscalationTicket_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('EscalationTicket_model', 'esc');
        header('Content-Type: application/json');
    }

    private function _auth() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $h = $this->input->request_headers();
        $a = isset($h['Authorization']) ? $h['Authorization'] : '';
        $exp = 'Bearer ' . (defined('STEM_DIGEST_TOKEN') ? STEM_DIGEST_TOKEN : getenv('STEM_DIGEST_TOKEN'));
        if ($a !== $exp) { http_response_code(401); echo json_encode(array('error'=>'unauthorized')); exit; }
    }

    private function _json_input() {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) { $d = json_decode($raw, true); if (is_array($d)) return $d; }
        return $this->input->post();
    }

    public function open_ticket() {
        $this->_auth();
        $in = $this->_json_input();
        foreach (array('lead_id','opened_by_uid','reason_code') as $f) {
            if (empty($in[$f])) { http_response_code(400); echo json_encode(array('error'=>'missing '.$f)); return; }
        }
        $r = $this->esc->open_ticket(
            (int)$in['lead_id'],
            (int)$in['opened_by_uid'],
            $in['reason_code'],
            isset($in['note']) ? $in['note'] : '',
            isset($in['assignee_uid']) ? (int)$in['assignee_uid'] : null
        );
        echo json_encode($r);
    }

    public function handover() {
        $this->_auth();
        $in = $this->_json_input();
        foreach (array('ticket_id','new_assignee_uid','by_uid') as $f) {
            if (empty($in[$f])) { http_response_code(400); echo json_encode(array('error'=>'missing '.$f)); return; }
        }
        $r = $this->esc->handover((int)$in['ticket_id'], (int)$in['new_assignee_uid'], (int)$in['by_uid'], isset($in['note'])?$in['note']:'');
        if (!empty($r['error'])) http_response_code(422);
        echo json_encode($r);
    }

    public function resolve() {
        $this->_auth();
        $in = $this->_json_input();
        foreach (array('ticket_id','by_uid','resolution_note') as $f) {
            if (empty($in[$f])) { http_response_code(400); echo json_encode(array('error'=>'missing '.$f)); return; }
        }
        $r = $this->esc->resolve((int)$in['ticket_id'], (int)$in['by_uid'], $in['resolution_note']);
        if (!empty($r['error'])) http_response_code(422);
        echo json_encode($r);
    }

    public function queue() {
        $this->_auth();
        $a = $this->input->get('assignee_uid');
        $s = $this->input->get('status') ?: 'open';
        $l = (int)($this->input->get('limit') ?: 50);
        $rows = $this->esc->queue($a ? (int)$a : null, $s, $l);
        echo json_encode(array('rows'=>$rows,'count'=>count($rows)));
    }

    public function breached() {
        $this->_auth();
        $rows = $this->esc->queue(null, 'all', 200);
        $now = time();
        $out = array();
        foreach ($rows as $r) {
            if ($r['status'] === 'resolved') continue;
            if (strtotime($r['sla_deadline']) < $now) $out[] = $r;
        }
        echo json_encode(array('rows'=>$out,'count'=>count($out)));
    }

    public function scan_breached() {
        $this->_auth();
        echo json_encode($this->esc->scan_breached());
    }

    public function probe() {
        $tbl = $this->db->table_exists('escalation_ticket');
        if (!$tbl) { http_response_code(404); echo json_encode(array('deployed'=>false)); return; }
        echo json_encode(array('deployed'=>true,'migration'=>'022'));
    }
}
