<?php
/**
 * STEM CRM - Migration 027 - Comm Orchestrator Controller
 *
 * REST endpoints for the 360-degree comm agent.
 *
 * Routes (all under /api/comm/):
 *   GET  /api/comm/orchestrator/probe                  - phase + queue status
 *   POST /api/comm/event/ingest                         - external event push
 *   GET  /api/comm/draft/list?bd_uid=&status=           - list drafts for BD
 *   GET  /api/comm/draft/<id>                           - fetch one draft
 *   POST /api/comm/draft/<id>/update                    - BD edits body/subject
 *   POST /api/comm/draft/<id>/send                      - hand off to migration 026 send pipe
 *   POST /api/comm/draft/<id>/discard                   - BD rejects draft
 *   POST /api/comm/draft/<id>/regenerate                - re-run drafter with same context
 *   POST /api/comm/orchestrator/process_pending         - cron entry point
 *
 * Auth: Bearer STEM_DIGEST_TOKEN for backend, JWT for app.
 *
 * Plain English. No em-dashes. No non-ASCII.
 *
 * Author: STEM Learning ops
 * Date: 17 May 2026
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class CommOrchestratorApi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Comm_orchestrator_agent');
        $this->load->model('Comm_drafter_agent');
        $this->load->library('Auth_guard');
        $this->load->library('Migration_026_email_send_pipe'); // for actual send
        header('Content-Type: application/json');
    }

    // ========================================================================
    // PROBE - migration 027 deployment check
    // ========================================================================

    public function probe() {
        if (!$this->auth_guard->bearer_ok()) {
            return $this->reject(401, 'unauthorised');
        }
        $info = $this->Comm_orchestrator_agent->probe();
        echo json_encode($info);
    }

    // ========================================================================
    // EVENT INGEST - external trigger pipe
    // ========================================================================

    public function event_ingest() {
        if (!$this->auth_guard->bearer_ok()) {
            return $this->reject(401, 'unauthorised');
        }

        $event_type = $this->input->post('event_type');
        $cid_id     = (int) $this->input->post('cid_id');
        $bd_uid     = (int) $this->input->post('bd_uid');
        $source_id  = (int) $this->input->post('source_id');
        $payload    = $this->input->post('payload_json');

        if (empty($event_type) || $cid_id <= 0 || $bd_uid <= 0) {
            return $this->reject(400, 'missing required fields: event_type, cid_id, bd_uid');
        }

        $allowed = array(
            'call_unanswered', 'call_dropped', 'meeting_completed',
            'proposal_sent', 'query_raised', 'query_resolved',
            'stage_progressed', 'dormant_re_engage',
        );
        if (!in_array($event_type, $allowed)) {
            return $this->reject(400, 'unknown event_type: ' . $event_type);
        }

        $payload_arr = !empty($payload) ? json_decode($payload, true) : array();
        if (!is_array($payload_arr)) $payload_arr = array();

        $event_log_id = $this->Comm_orchestrator_agent->ingest_event(
            $event_type, $cid_id, $bd_uid, $source_id, $payload_arr
        );

        if ($event_log_id) {
            echo json_encode(array('ok' => true, 'event_log_id' => $event_log_id));
        } else {
            echo json_encode(array('ok' => false, 'reason' => 'rejected by orchestrator'));
        }
    }

    // ========================================================================
    // DRAFT LIST
    // ========================================================================

    public function draft_list() {
        if (!$this->auth_guard->bearer_ok() && !$this->auth_guard->jwt_ok()) {
            return $this->reject(401, 'unauthorised');
        }

        $bd_uid = (int) $this->input->get('bd_uid');
        $status_filter = $this->input->get('status'); // pending_review, needs_input, sent, discarded
        $cid_id = (int) $this->input->get('cid_id'); // optional

        $this->db->select('d.*, t.event_type, t.template_name, l.school_name')
            ->from('comm_draft_queue d')
            ->join('comm_template_v2 t', 'd.template_id = t.id', 'left')
            ->join('init_call l', 'd.cid_id = l.id', 'left')
            ->where('d.expires_at >=', date('Y-m-d H:i:s'))
            ->order_by('d.created_at', 'DESC')
            ->limit(100);

        if ($bd_uid > 0) $this->db->where('d.bd_uid', $bd_uid);
        if (!empty($status_filter)) $this->db->where('d.status', $status_filter);
        if ($cid_id > 0) $this->db->where('d.cid_id', $cid_id);

        $rows = $this->db->get()->result_array();
        echo json_encode(array('ok' => true, 'count' => count($rows), 'drafts' => $rows));
    }

    public function draft_get($draft_id) {
        if (!$this->auth_guard->bearer_ok() && !$this->auth_guard->jwt_ok()) {
            return $this->reject(401, 'unauthorised');
        }
        $row = $this->db->get_where('comm_draft_queue', array('id' => (int) $draft_id))->row_array();
        if (empty($row)) return $this->reject(404, 'draft not found');
        echo json_encode(array('ok' => true, 'draft' => $row));
    }

    // ========================================================================
    // DRAFT EDIT
    // ========================================================================

    public function draft_update($draft_id) {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');

        $draft = $this->db->get_where('comm_draft_queue', array('id' => (int) $draft_id))->row_array();
        if (empty($draft)) return $this->reject(404, 'draft not found');
        if (!in_array($draft['status'], array('pending_review', 'needs_input'))) {
            return $this->reject(400, 'cannot edit draft in status ' . $draft['status']);
        }

        $update = array();
        if ($this->input->post('subject') !== null)    $update['subject']    = $this->input->post('subject');
        if ($this->input->post('body_plain') !== null) $update['body_plain'] = $this->input->post('body_plain');
        if ($this->input->post('body_html') !== null)  $update['body_html']  = $this->input->post('body_html');
        if ($this->input->post('recipient_to') !== null) $update['recipient_to'] = $this->input->post('recipient_to');

        if (!empty($update)) {
            $update['edited_at'] = date('Y-m-d H:i:s');
            $update['edited_by'] = $this->auth_guard->current_uid();
            $update['status'] = 'pending_review'; // promote from needs_input if any
            $this->db->where('id', $draft_id)->update('comm_draft_queue', $update);
        }

        echo json_encode(array('ok' => true, 'draft_id' => (int) $draft_id));
    }

    // ========================================================================
    // SEND - hand off to migration 026 Gmail pipe
    // ========================================================================

    public function draft_send($draft_id) {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');

        $draft = $this->db->get_where('comm_draft_queue', array('id' => (int) $draft_id))->row_array();
        if (empty($draft)) return $this->reject(404, 'draft not found');
        if ($draft['status'] !== 'pending_review') {
            return $this->reject(400, 'cannot send draft in status ' . $draft['status']);
        }

        // Hand to migration 026 send pipe
        try {
            $send_result = $this->migration_026_email_send_pipe->send_email(array(
                'from_uid'     => (int) $draft['bd_uid'],
                'to'           => $draft['recipient_to'],
                'cc'           => !empty($draft['recipient_cc']) ? json_decode($draft['recipient_cc'], true) : array(),
                'subject'      => $draft['subject'],
                'body_plain'   => $draft['body_plain'],
                'body_html'    => $draft['body_html'],
                'attachment_path' => $draft['attachment_path'],
                'source_module'=> 'm027_comm_orchestrator',
                'source_id'    => (int) $draft_id,
            ));

            if (empty($send_result) || empty($send_result['ok'])) {
                $reason = isset($send_result['reason']) ? $send_result['reason'] : 'send failed';
                $this->db->where('id', $draft_id)->update('comm_draft_queue', array(
                    'status' => 'send_failed',
                    'send_error' => $reason,
                ));
                return $this->reject(500, $reason);
            }

            // Log send
            $this->db->insert('comm_send_log', array(
                'draft_id'         => (int) $draft_id,
                'event_log_id'     => (int) $draft['event_log_id'],
                'cid_id'           => (int) $draft['cid_id'],
                'bd_uid'           => (int) $draft['bd_uid'],
                'stakeholder_email'=> $draft['recipient_to'],
                'cc_emails_json'   => $draft['recipient_cc'],
                'subject'          => $draft['subject'],
                'body_plain'       => $draft['body_plain'],
                'gmail_message_id' => $send_result['gmail_message_id'],
                'gmail_thread_id'  => $send_result['gmail_thread_id'],
                'channel'          => 'email',
                'manual_send_flag' => 0,
                'sent_at'          => date('Y-m-d H:i:s'),
            ));

            $this->db->where('id', $draft_id)->update('comm_draft_queue', array(
                'status' => 'sent',
                'sent_at'=> date('Y-m-d H:i:s'),
            ));

            $this->db->where('id', $draft['event_log_id'])->update('comm_event_log', array(
                'status' => 'sent',
                'processed_at' => date('Y-m-d H:i:s'),
            ));

            echo json_encode(array('ok' => true, 'gmail_message_id' => $send_result['gmail_message_id']));
        } catch (Exception $e) {
            $this->db->where('id', $draft_id)->update('comm_draft_queue', array(
                'status' => 'send_failed',
                'send_error' => $e->getMessage(),
            ));
            log_message('error', "[m027 controller] send exception draft=$draft_id: " . $e->getMessage());
            return $this->reject(500, 'send exception: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // DISCARD - BD rejects draft
    // ========================================================================

    public function draft_discard($draft_id) {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');

        $reason = $this->input->post('reason');
        $draft = $this->db->get_where('comm_draft_queue', array('id' => (int) $draft_id))->row_array();
        if (empty($draft)) return $this->reject(404, 'draft not found');

        $this->db->where('id', $draft_id)->update('comm_draft_queue', array(
            'status' => 'discarded',
            'discard_reason' => $reason,
            'discarded_at' => date('Y-m-d H:i:s'),
            'discarded_by' => $this->auth_guard->current_uid(),
        ));

        $this->db->where('id', $draft['event_log_id'])->update('comm_event_log', array(
            'status' => 'discarded',
            'status_note' => 'BD rejected: ' . $reason,
            'processed_at' => date('Y-m-d H:i:s'),
        ));

        echo json_encode(array('ok' => true));
    }

    // ========================================================================
    // REGENERATE - re-run drafter with same event context
    // ========================================================================

    public function draft_regenerate($draft_id) {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');

        $old = $this->db->get_where('comm_draft_queue', array('id' => (int) $draft_id))->row_array();
        if (empty($old)) return $this->reject(404, 'draft not found');

        // Mark old as superseded
        $this->db->where('id', $draft_id)->update('comm_draft_queue', array(
            'status' => 'superseded',
            'superseded_at' => date('Y-m-d H:i:s'),
        ));

        // Re-run drafter from event_log_id
        $event_log_id = (int) $old['event_log_id'];
        $event = $this->db->get_where('comm_event_log', array('id' => $event_log_id))->row_array();
        if (empty($event)) return $this->reject(404, 'event_log row missing');

        $template = $this->db->get_where('comm_template_v2', array('id' => (int) $old['template_id']))->row_array();
        $recipients = array(
            'to'      => $old['recipient_to'],
            'to_name' => $old['recipient_to_name'],
            'cc'      => !empty($old['recipient_cc']) ? json_decode($old['recipient_cc'], true) : array(),
        );

        $new_draft_id = $this->Comm_drafter_agent->draft_email(
            $event_log_id, $template, $recipients, json_decode($event['payload_json'], true)
        );

        if ($new_draft_id) {
            echo json_encode(array('ok' => true, 'new_draft_id' => $new_draft_id));
        } else {
            $this->reject(500, 'regeneration failed');
        }
    }

    // ========================================================================
    // CRON HOOK
    // ========================================================================

    public function process_pending() {
        if (!$this->auth_guard->bearer_ok()) return $this->reject(401, 'unauthorised');
        $result = $this->Comm_orchestrator_agent->process_pending_events(100);
        echo json_encode($result);
    }

    // ========================================================================
    // HELPERS
    // ========================================================================

    private function reject($http_code, $reason) {
        http_response_code($http_code);
        echo json_encode(array('ok' => false, 'reason' => $reason));
        return;
    }
}
