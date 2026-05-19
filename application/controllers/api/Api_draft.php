<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_draft
 *
 * Draft / comment / new-funnel staging endpoints. Used by the BD and CM
 * stub screens that show a list to-action plus a draft form.
 *
 * Consumed by mobile stub routes:
 *   - /special-comment-pending       -> special_comment_pending() + save_comment()
 *   - /thanks-comment-complete       -> thanks_comment_complete()
 *   - /new-funnel-added              -> new_funnel_added()
 *   - /no-primary-contact-companies  -> no_primary_contact()
 *   - /special-leave-request         -> special_leave_request() + save_leave()
 *   - /cm-check-add-new-lead         -> cm_check_new_lead() + cm_approve_lead()
 *   - /cm-bd-assign-request          -> cm_bd_assign_request() + cm_assign()
 *   - /cm-handover-installation      -> cm_handover_installation()
 *
 * Plain English. No em-dashes. Rs for rupees.
 */
class Api_draft extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->_check_auth();
    }

    // -- BD draft surfaces --------------------------------------------------

    public function special_comment_pending() {
        $uid  = $this->session->userdata('user')['user_id'];
        $rows = $this->Menu_model->special_comment_pending($uid);
        $this->_json(['rows' => $rows]);
    }

    public function save_comment() {
        $uid  = $this->session->userdata('user')['user_id'];
        $body = json_decode($this->input->raw_input_stream, true);
        $ok   = $this->Menu_model->save_special_comment($uid, $body);
        $this->_json(['saved' => (bool) $ok]);
    }

    public function thanks_comment_complete() {
        $uid  = $this->session->userdata('user')['user_id'];
        $rows = $this->Menu_model->thanks_comment_complete($uid);
        $this->_json(['rows' => $rows]);
    }

    public function new_funnel_added() {
        $uid  = $this->session->userdata('user')['user_id'];
        $days = (int) ($this->input->get('days') ?: 7);
        $rows = $this->Menu_model->new_funnel_added($uid, $days);
        $this->_json(['days' => $days, 'rows' => $rows]);
    }

    public function no_primary_contact() {
        $uid  = $this->session->userdata('user')['user_id'];
        $rows = $this->Menu_model->companies_without_primary_contact($uid);
        $this->_json(['rows' => $rows]);
    }

    public function special_leave_request() {
        $uid  = $this->session->userdata('user')['user_id'];
        $rows = $this->Menu_model->special_leave_requests($uid);
        $this->_json(['rows' => $rows]);
    }

    public function save_leave() {
        $uid  = $this->session->userdata('user')['user_id'];
        $body = json_decode($this->input->raw_input_stream, true);
        $id   = $this->Menu_model->save_leave_request($uid, $body);
        $this->_json(['leave_id' => $id]);
    }

    // -- CM draft surfaces --------------------------------------------------

    public function cm_check_new_lead() {
        $cm_uid = $this->session->userdata('user')['user_id'];
        $rows   = $this->Menu_model->cm_new_lead_queue($cm_uid);
        $this->_json(['rows' => $rows]);
    }

    public function cm_approve_lead() {
        $cm_uid  = $this->session->userdata('user')['user_id'];
        $body    = json_decode($this->input->raw_input_stream, true);
        $lead_id = (int) ($body['lead_id'] ?? 0);
        $action  = $body['action'] ?? 'approve'; // approve|reject
        $reason  = $body['reason'] ?? '';
        $ok      = $this->Menu_model->cm_action_on_lead($cm_uid, $lead_id, $action, $reason);
        $this->_json(['ok' => (bool) $ok, 'lead_id' => $lead_id, 'action' => $action]);
    }

    public function cm_bd_assign_request() {
        $cm_uid = $this->session->userdata('user')['user_id'];
        $rows   = $this->Menu_model->cm_bd_assign_queue($cm_uid);
        $this->_json(['rows' => $rows]);
    }

    public function cm_assign() {
        $cm_uid = $this->session->userdata('user')['user_id'];
        $body   = json_decode($this->input->raw_input_stream, true);
        $ok     = $this->Menu_model->cm_assign_lead_to_bd($cm_uid, $body);
        $this->_json(['ok' => (bool) $ok]);
    }

    public function cm_handover_installation() {
        $cm_uid = $this->session->userdata('user')['user_id'];
        $rows   = $this->Menu_model->cm_handover_installation_queue($cm_uid);
        $this->_json(['rows' => $rows]);
    }

    // -- helpers ------------------------------------------------------------

    private function _check_auth() {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
            exit;
        }
    }

    private function _json($data, $status = 200) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
