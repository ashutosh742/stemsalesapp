<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration 039: Email-to-task controller
 *
 * Routes (parallel surface, /api/email_to_task/*):
 *   POST /api/email_to_task/poll            - trigger one polling cycle (Bearer admin only)
 *   GET  /api/email_to_task/inbox?bd_uid=N  - list pending inbox for a BD
 *   POST /api/email_to_task/accept          - accept inbox row, mint a tblcallevents row
 *   POST /api/email_to_task/dismiss         - dismiss inbox row
 *   GET  /api/email_to_task/probe           - migration deployed check
 *
 * Production /api/menu/* untouched.
 */
class Email_to_task extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('EmailToTask_agent', 'agent');
        header('Content-Type: application/json; charset=utf-8');
    }

    public function probe() {
        // Migration deployed check - 200 means tables exist
        $ok = $this->db->table_exists('inbound_email_v2')
              && $this->db->table_exists('inbound_email_poll_log_v2');
        if ($ok) {
            echo json_encode(array('ok' => true, 'migration' => '039', 'deployed' => true));
        } else {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'migration' => '039', 'deployed' => false));
        }
    }

    public function poll() {
        if (!$this->_check_bearer_admin()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $mailbox = $this->input->post('mailbox_account');
        if (!$mailbox) $mailbox = 'stemlearning@gmail.com';

        $res = $this->agent->poll_mailbox($mailbox);
        echo json_encode(array('ok' => empty($res['error']), 'result' => $res));
    }

    public function inbox() {
        $bd_uid = (int)$this->_bearer_uid();
        if (!$bd_uid) {
            $bd_uid = (int)$this->input->get('bd_uid'); // admin path
            if (!$this->_check_bearer_admin() || !$bd_uid) {
                http_response_code(401);
                echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
                return;
            }
        }
        $limit = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 200) $limit = 50;

        $rows = $this->agent->inbox_for_bd($bd_uid, $limit);
        echo json_encode(array('ok' => true, 'bd_uid' => $bd_uid, 'count' => count($rows), 'rows' => $rows));
    }

    public function accept() {
        $bd_uid = (int)$this->_bearer_uid();
        if (!$bd_uid) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $id = (int)$this->input->post('inbound_email_id');
        if (!$id) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'inbound_email_id_required'));
            return;
        }

        $payload = $this->agent->build_accept_payload($id, $bd_uid);
        if (!empty($payload['error'])) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => $payload['error']));
            return;
        }

        // Write a tblcallevents row via the standard submit path. Production logic untouched.
        $this->load->model('Menu_model');
        $event_id = null;
        if (method_exists($this->Menu_model, 'submit_task_v2_inline')) {
            $event_id = $this->Menu_model->submit_task_v2_inline(array(
                'uid'           => $bd_uid,
                'cid_id'        => $payload['cid_id'],
                'actiontype_id' => $payload['actiontype_id'],
                'purpose_id'    => $payload['purpose_id'],
                'remarks'       => $payload['remarks'],
                'origin'        => 'email_to_task_v2',
            ));
        } else {
            // Fallback: direct insert of an approved completed email event
            $this->db->insert('tblcallevents', array(
                'cid_id'           => $payload['cid_id'],
                'uid'              => $bd_uid,
                'actiontype_id'    => $payload['actiontype_id'],
                'purpose_id'       => $payload['purpose_id'],
                'remarks'          => $payload['remarks'],
                'appointmentdatetime' => date('Y-m-d H:i:s'),
                'event_date'       => date('Y-m-d'),
                'plan'             => 0,
                'is_auto'          => 0,
                'approved_status'  => 1,
                'nextCFID'         => 0,
                'origin_v2'        => 'email_to_task_v2',
                'createDate'       => date('Y-m-d H:i:s'),
            ));
            $event_id = $this->db->insert_id();
        }

        $this->agent->mark_accepted($id, $bd_uid, $event_id);

        echo json_encode(array('ok' => true, 'event_id' => $event_id, 'inbound_email_id' => $id));
    }

    public function dismiss() {
        $bd_uid = (int)$this->_bearer_uid();
        if (!$bd_uid) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $id = (int)$this->input->post('inbound_email_id');
        $reason = $this->input->post('reason');
        if (!$id) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'inbound_email_id_required'));
            return;
        }
        $ok = $this->agent->mark_dismissed($id, $bd_uid, $reason);
        echo json_encode(array('ok' => $ok, 'inbound_email_id' => $id));
    }

    /* ====================== Bearer helpers ====================== */
    private function _bearer_token() {
        $h = $this->input->get_request_header('Authorization', true);
        if (!$h) return null;
        if (stripos($h, 'Bearer ') !== 0) return null;
        return trim(substr($h, 7));
    }

    private function _bearer_uid() {
        $tok = $this->_bearer_token();
        if (!$tok) return null;
        // Look up uid from user.api_token or session_token. Production has this mapping.
        $u = $this->db->query("SELECT uid FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1", array($tok))->row();
        return $u ? (int)$u->uid : null;
    }

    private function _check_bearer_admin() {
        $tok = $this->_bearer_token();
        if (!$tok) return false;
        $expected = getenv('STEM_DIGEST_TOKEN');
        if ($expected && hash_equals($expected, $tok)) return true;
        // Or admin user token
        $u = $this->db->query("SELECT t.uid, u.type_id FROM api_token t JOIN user u ON u.uid = t.uid WHERE t.token = ? AND t.active = 1 AND (t.expires_at IS NULL OR t.expires_at > NOW()) LIMIT 1", array($tok))->row();
        return $u && in_array((int)$u->type_id, array(1, 2));
    }
}
