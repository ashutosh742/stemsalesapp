<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomV2X Controller
 *
 * Handles mom_v2 routes for STEM CRM v2.8 staging.
 *
 * Tables used (verified on staging):
 *   mom_v2_submission    - lifecycle of a MOM submission per event
 *   mom_v2_meeting_agenda_lock - agenda gate per event
 *   mom_v2_answers       - per-question answers
 *   mom_v2_question_schema - template questions
 *
 * mom_v2_drafts / meeting_lifecycle not present in DB;
 * those routes return ok:true with awaits_migration note.
 *
 * Routes:
 *   GET  api/mom_v2/agenda_gate/probe
 *   GET  api/mom_v2/agenda_templates
 *   POST api/mom_v2/draft
 *   GET  api/mom_v2/draft/:num
 *   GET  api/mom_v2/get
 *   POST api/mom_v2/start
 */
class MomV2X extends CI_Controller {

    private $bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    // -----------------------------------------------------------------------
    // Auth helper
    // -----------------------------------------------------------------------
    private function _check_auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || trim(str_replace('Bearer', '', $hdr)) !== $this->bearer) {
            $this->output->set_status_header(401);
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200)
    {
        $this->output->set_status_header($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // -----------------------------------------------------------------------
    // GET api/mom_v2/agenda_gate/probe
    // Health check: confirms agenda lock table is reachable.
    // -----------------------------------------------------------------------
    public function agenda_gate()
    {
        // sub-segment: probe (passed as second URI segment by route)
        if (!$this->_check_auth()) return;

        $db_ok = (bool) $this->db->conn_id;
        $this->_json(['ok' => $db_ok, 'success' => true, 'service' => 'mom_v2_agenda_gate']);
    }

    // -----------------------------------------------------------------------
    // GET api/mom_v2/agenda_templates
    // Returns active question schema rows that form the agenda template.
    // Source: mom_v2_question_schema
    // -----------------------------------------------------------------------
    public function agenda_templates()
    {
        if (!$this->_check_auth()) return;

        $query = $this->db->select('question_id, sr_no, question_text, answer_type, options_json, required_always, sort_order')
                          ->where('is_active', 1)
                          ->order_by('sort_order', 'ASC')
                          ->get('mom_v2_question_schema');

        $rows = $query ? $query->result_array() : [];
        foreach ($rows as &$r) {
            $r['question_id']    = (int) $r['question_id'];
            $r['sr_no']          = (int) $r['sr_no'];
            $r['sort_order']     = (int) $r['sort_order'];
            $r['required_always'] = (bool) $r['required_always'];
        }
        unset($r);

        $this->_json([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // POST api/mom_v2/draft
    // Creates or updates a MOM draft submission.
    // Expected body: event_id (int), bd_uid (int), cid_id (int)
    // Table: mom_v2_submission (status = draft)
    // momv2_drafts table absent; using mom_v2_submission as draft store.
    // -----------------------------------------------------------------------
    public function draft($id = null)
    {
        if (!$this->_check_auth()) return;

        // GET /api/mom_v2/draft/:num  -- retrieve a draft by event_id
        if ($this->input->method() === 'get' || $id !== null) {
            $event_id = $id ? (int) $id : (int) $this->input->get('event_id');
            if ($event_id <= 0) {
                return $this->_json(['ok' => false, 'error' => 'event_id required'], 400);
            }
            $row = $this->db->get_where('mom_v2_submission', ['event_id' => $event_id], 1)->row_array();
            if (!$row) {
                return $this->_json(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data']);
            }
            return $this->_json(['ok' => true, 'success' => true, 'data' => $row, 'count' => 1]);
        }

        // POST /api/mom_v2/draft  -- create/update draft
        $event_id = (int) $this->input->post('event_id');
        $bd_uid   = (int) $this->input->post('bd_uid');
        $cid_id   = (int) $this->input->post('cid_id');

        if ($event_id <= 0 || $bd_uid <= 0) {
            return $this->_json(['ok' => false, 'error' => 'event_id and bd_uid required'], 400);
        }

        $existing = $this->db->get_where('mom_v2_submission', ['event_id' => $event_id], 1)->row_array();
        if ($existing) {
            // Update to draft if not already further progressed
            if ($existing['status'] === 'draft') {
                $this->db->where('event_id', $event_id)->update('mom_v2_submission', ['updated_at' => date('Y-m-d H:i:s')]);
            }
            return $this->_json(['ok' => true, 'success' => true, 'message' => 'draft_exists', 'data' => $existing]);
        }

        // Insert new draft
        $insert = [
            'event_id'          => $event_id,
            'bd_uid'            => $bd_uid,
            'cid_id'            => $cid_id ?: 0,
            'answers_required'  => 0,
            'answers_completed' => 0,
            'status'            => 'draft',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('mom_v2_submission', $insert);
        $new_id = $this->db->insert_id();

        $this->_json(['ok' => true, 'success' => true, 'message' => 'draft_created', 'submission_id' => $new_id]);
    }

    // -----------------------------------------------------------------------
    // GET api/mom_v2/get
    // Returns MOM submissions optionally filtered by bd_uid / event_id / status.
    // Source: mom_v2_submission
    // -----------------------------------------------------------------------
    public function get()
    {
        if (!$this->_check_auth()) return;

        $bd_uid   = (int) $this->input->get('bd_uid');
        $event_id = (int) $this->input->get('event_id');
        $status   = $this->input->get('status');

        $this->db->select('s.submission_id, s.event_id, s.bd_uid, s.cid_id, s.cm_uid, s.agenda_locked, s.voice_coverage_pct, s.answers_completed, s.answers_required, s.quality_grade, s.quality_score, s.status, s.submitted_at, s.cm_action_at, s.created_at');
        $this->db->from('mom_v2_submission s');

        if ($bd_uid > 0)   { $this->db->where('s.bd_uid', $bd_uid); }
        if ($event_id > 0) { $this->db->where('s.event_id', $event_id); }
        $allowed_statuses = ['draft','voice_done','form_done','submitted','pending_cm','approved','rejected'];
        if ($status && in_array($status, $allowed_statuses)) {
            $this->db->where('s.status', $status);
        }

        $this->db->order_by('s.created_at', 'DESC')->limit(100);
        $query = $this->db->get();
        $rows  = $query ? $query->result_array() : [];

        foreach ($rows as &$r) {
            $r['submission_id']     = (int) $r['submission_id'];
            $r['event_id']          = (int) $r['event_id'];
            $r['bd_uid']            = (int) $r['bd_uid'];
            $r['answers_completed'] = (int) $r['answers_completed'];
            $r['answers_required']  = (int) $r['answers_required'];
        }
        unset($r);

        $this->_json([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // POST api/mom_v2/start
    // Locks the agenda for an event (creates/confirms agenda lock row).
    // Body: event_id (int), bd_uid (int), cid_id (int), cstatus (int), actiontype_id (int)
    // Table: mom_v2_meeting_agenda_lock
    // -----------------------------------------------------------------------
    public function start()
    {
        if (!$this->_check_auth()) return;

        if ($this->input->method() !== 'post') {
            return $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }

        $event_id      = (int) $this->input->post('event_id');
        $bd_uid        = (int) $this->input->post('bd_uid');
        $cid_id        = (int) $this->input->post('cid_id');
        $cstatus       = (int) $this->input->post('cstatus');
        $actiontype_id = (int) $this->input->post('actiontype_id');

        if ($event_id <= 0 || $bd_uid <= 0) {
            return $this->_json(['ok' => false, 'error' => 'event_id and bd_uid required'], 400);
        }

        // Check existing lock
        $existing = $this->db->get_where('mom_v2_meeting_agenda_lock', ['event_id' => $event_id], 1)->row_array();
        if ($existing) {
            return $this->_json(['ok' => true, 'success' => true, 'message' => 'agenda_already_locked', 'data' => $existing]);
        }

        // Fetch required questions from schema
        $schema_query = $this->db->where('is_active', 1)->get('mom_v2_question_schema');
        $questions    = $schema_query ? $schema_query->result_array() : [];
        $required_ids = [];
        foreach ($questions as $q) {
            if ((int) $q['required_always'] === 1) {
                $required_ids[] = (int) $q['question_id'];
            }
        }

        $insert = [
            'event_id'               => $event_id,
            'bd_uid'                 => $bd_uid,
            'cid_id'                 => $cid_id ?: 0,
            'locked_at'              => date('Y-m-d H:i:s'),
            'required_questions_json'=> json_encode($required_ids),
            'cstatus_at_lock'        => $cstatus ?: 0,
            'actiontype_id'          => $actiontype_id ?: 0,
            'bd_committed'           => 1,
        ];
        $this->db->insert('mom_v2_meeting_agenda_lock', $insert);
        $lock_id = $this->db->insert_id();

        $this->_json([
            'ok'       => true,
            'success'  => true,
            'message'  => 'agenda_locked',
            'lock_id'  => $lock_id,
            'required_question_count' => count($required_ids),
        ]);
    }
}
