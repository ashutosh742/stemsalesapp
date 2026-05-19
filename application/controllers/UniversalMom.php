<?php
// =====================================================================
// STEM CRM - Migration 025: Universal MoM Controller (v3)
// File: application/controllers/UniversalMom.php
// =====================================================================
// This is the v3 MoM controller. It supersedes the older /api/mom_v2
// submit endpoint. Key differences:
//
//   1) actor_role aware - the actor (BD/CM/RM/SH/Director) is the
//      author of the MoM. mom_data.uid = actor_uid, actor_role enum
//      stamped. Approval flow routes based on actor_role.
//
//   2) Draft / submit are physically separate:
//        mom_draft (agent fills during meeting, editable)
//        mom_data  (only created on submit, immutable except approval)
//      Submit copies finalized draft -> mom_data, draft stays for audit.
//
//   3) Gate 0: callevent_id NOT NULL on submit. No orphan MoMs.
//
//   4) Got-details cap: if mid_meeting_classification = got_details_only
//      and dm_met = 0, MoM submit is allowed but quality grade cannot
//      exceed C. Travel cluster cannot exceed D and triggers warning.
//
//   5) Upsell auto-detect: if cstatus = 12, MoM submit triggers
//      lane assignment in rm_upsell_pipeline.
//
// Endpoints:
//
//   POST /api/v3/mom/draft_update    - patch the in-flight draft
//   POST /api/v3/mom/submit          - finalize draft -> mom_data
//   POST /api/v3/mom/approve         - manager approves the submitted MoM
//   POST /api/v3/mom/reject          - manager rejects with reason
//   GET  /api/v3/mom/draft?callevent_id=
//   GET  /api/v3/mom/pending_for_manager?manager_uid=
// =====================================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class UniversalMom extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->_require_bearer();
    }

    private function _require_bearer() {
        $hdr = $this->input->get_request_header('Authorization', true);
        if (strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(array('error' => 'unauthorized'), 401); exit;
        }
    }

    private function _json($data, $status = 200) {
        $this->output->set_status_header($status)
                     ->set_content_type('application/json')
                     ->set_output(json_encode($data));
    }

    // -----------------------------------------------------------------
    // POST /api/v3/mom/draft_update
    // Updates the in-flight mom_draft row. Called by phone every 30 s
    // while the meeting is in STARTED or CLASSIFIED state, and by the
    // extraction agent after Whisper completes.
    // -----------------------------------------------------------------
    public function draft_update() {
        $callevent_id = (int)$this->input->post('callevent_id');
        $author_uid = (int)$this->input->post('author_uid');
        $patch_json = $this->input->post('patch_json');

        if (!$callevent_id || !$author_uid) {
            return $this->_json(array('error' => 'missing_params'), 400);
        }

        $patch = json_decode($patch_json, true);
        if (!is_array($patch)) {
            return $this->_json(array('error' => 'invalid_patch_json'), 400);
        }

        // Whitelist patchable fields
        $allowed = array('dm_name','dm_designation','dm_mobile','dm_email',
                         'agenda_text','discussion_text','objections_text',
                         'next_steps_text','fund_sanstion_limit',
                         'approving_autorities','expected_close_date',
                         'competition_text','requirements_text',
                         'rp_details_text','meeting_outcome_summary');
        $update = array();
        foreach ($allowed as $k) {
            if (array_key_exists($k, $patch)) $update[$k] = $patch[$k];
        }

        if (empty($update)) {
            return $this->_json(array('error' => 'no_allowed_fields_in_patch'), 400);
        }

        $update['last_edited_at'] = date('Y-m-d H:i:s');
        $update['last_edited_by'] = $author_uid;

        $this->db->where('callevent_id', $callevent_id)
                 ->where('status', 'draft')
                 ->update('mom_draft', $update);

        $row = $this->db->where('callevent_id', $callevent_id)
                        ->order_by('id','DESC')->limit(1)
                        ->get('mom_draft')->row_array();

        return $this->_json(array('ok' => true, 'draft' => $row));
    }

    // -----------------------------------------------------------------
    // POST /api/v3/mom/submit
    // Finalize the draft and create the immutable mom_data row.
    // -----------------------------------------------------------------
    public function submit() {
        $callevent_id = (int)$this->input->post('callevent_id');
        $cid_id = (int)$this->input->post('cid_id');
        $actor_uid = (int)$this->input->post('actor_uid');
        $actor_role = $this->input->post('actor_role');

        if (!$callevent_id || !$cid_id || !$actor_uid || !$actor_role) {
            return $this->_json(array('error' => 'missing_params'), 400);
        }

        // GATE 0: callevent_id must be a real, ended meeting
        $ce = $this->db->where('id', $callevent_id)->get('tblcallevents')->row_array();
        if (!$ce) {
            return $this->_json(array('error' => 'gate0_callevent_missing'), 422);
        }
        if (empty($ce['actual_end_time'])) {
            return $this->_json(array('error' => 'gate0_meeting_not_ended'), 422);
        }

        // Pull final draft
        $draft = $this->db->where('callevent_id', $callevent_id)
                          ->where('status', 'draft')
                          ->order_by('id','DESC')->limit(1)
                          ->get('mom_draft')->row_array();
        if (!$draft) {
            return $this->_json(array('error' => 'no_draft_found'), 404);
        }

        // GATE 1: DM block mandatory if classification implies DM met
        $cls = $ce['mid_meeting_classification'] ?? 'unknown';
        $dm_met = (int)($ce['dm_met'] ?? 0);
        if (in_array($cls, array('rp_positive','rp_with_objection','closure_ready')) && empty($draft['dm_name'])) {
            return $this->_json(array('error' => 'gate1_dm_block_missing',
                                       'required' => 'dm_name dm_designation dm_mobile'), 422);
        }

        // GATE 2: travel cluster meetings must have RP-grade classification
        if ($ce['is_travel_cluster'] == 1 && $cls === 'got_details_only') {
            // Allow submit but warn and trigger double penalty path
            $warn_travel_double = true;
        }

        // GATE 3: cstatus >= 9 needs CM signoff (migration 022)
        $ic = $this->db->where('id', $cid_id)->get('init_call')->row_array();
        if (!empty($ic) && (int)$ic['cstatus'] >= 9 && $actor_role === 'BD') {
            // Check CM signoff exists in lead_signoff (migration 022)
            $sg = $this->db->where('cid_id', $cid_id)
                           ->where('gate', 'G3')
                           ->where('status', 'approved')
                           ->get('lead_signoff')->row();
            if (!$sg) {
                return $this->_json(array('error' => 'gate3_cm_signoff_missing',
                                          'detail' => 'cstatus 9+ MoM by BD requires G3 signoff'), 422);
            }
        }

        // Determine approver based on actor_role
        $approver_uid = $this->_determine_approver($actor_uid, $actor_role, $cid_id);

        // Compose mom_data row
        $now = date('Y-m-d H:i:s');
        $mom_row = array(
            'callevent_id' => $callevent_id,
            'cid_id' => $cid_id,
            'uid' => $actor_uid,
            'actor_role' => $actor_role,
            'mid_meeting_classification' => $cls,
            'dm_met' => $dm_met,
            'dm_name' => $draft['dm_name'],
            'dm_designation' => $draft['dm_designation'],
            'dm_mobile' => $draft['dm_mobile'],
            'dm_email' => $draft['dm_email'],
            'agenda_text' => $draft['agenda_text'],
            'discussion_text' => $draft['discussion_text'],
            'objections_text' => $draft['objections_text'],
            'next_steps_text' => $draft['next_steps_text'],
            'fund_sanstion_limit' => $draft['fund_sanstion_limit'],
            'approving_autorities' => $draft['approving_autorities'],
            'expected_close_date' => $draft['expected_close_date'],
            'competition_text' => $draft['competition_text'],
            'requirements_text' => $draft['requirements_text'],
            'rp_details_text' => $draft['rp_details_text'],
            'meeting_outcome_summary' => $draft['meeting_outcome_summary'],
            'submitted_at' => $now,
            'approved_status' => null,
            'approver_uid' => $approver_uid,
            'is_travel_cluster' => $ce['is_travel_cluster'],
            'mom_version' => 'v3',
            'audio_log_id' => $this->_audio_log_id($callevent_id),
            'mom_draft_id' => $draft['id']
        );

        // DM details captured once on init_call (forever, not re-typed)
        if (!empty($draft['dm_name']) && $dm_met) {
            $this->db->where('id', $cid_id)->update('init_call', array(
                'dm_name' => $draft['dm_name'],
                'dm_designation' => $draft['dm_designation'],
                'dm_mobile' => $draft['dm_mobile'],
                'dm_email' => $draft['dm_email'],
                'dm_captured_at' => $now,
                'dm_captured_by_uid' => $actor_uid,
                'dm_captured_role' => $actor_role
            ));

            // Snapshot to init_call_contact_history
            $this->db->insert('init_call_contact_history', array(
                'cid_id' => $cid_id,
                'captured_at' => $now,
                'captured_by_uid' => $actor_uid,
                'captured_by_role' => $actor_role,
                'dm_name' => $draft['dm_name'],
                'dm_designation' => $draft['dm_designation'],
                'dm_mobile' => $draft['dm_mobile'],
                'dm_email' => $draft['dm_email'],
                'callevent_id' => $callevent_id
            ));
        }

        $this->db->insert('mom_data', $mom_row);
        $mom_id = $this->db->insert_id();

        // Mark draft submitted (but keep the row for audit trail)
        $this->db->where('id', $draft['id'])->update('mom_draft', array(
            'status' => 'submitted',
            'submitted_at' => $now,
            'mom_data_id' => $mom_id
        ));

        // Update tblcallevents lifecycle state
        $this->db->where('id', $callevent_id)->update('tblcallevents', array(
            'lifecycle_state' => 'MOM_SUBMITTED',
            'mom_submitted_at' => $now
        ));

        // Upsell auto-detect if cstatus = 12
        $upsell = null;
        if (!empty($ic) && (int)$ic['cstatus'] == 12) {
            $this->load->model('AIAgents/LeadFollowupTracker_model', 'followup');
            $upsell = $this->followup->auto_categorise_closed_as_upsell();
        }

        return $this->_json(array(
            'ok' => true,
            'mom_id' => $mom_id,
            'approver_uid' => $approver_uid,
            'approval_status' => 'pending',
            'travel_cluster_warning' => isset($warn_travel_double) ? 'Got-details on travel cluster meeting triggers double penalty if no DM followup in 7 days' : null,
            'upsell_auto_assigned' => $upsell
        ));
    }

    private function _determine_approver($actor_uid, $actor_role, $cid_id) {
        // Routing rules:
        //   BD MoM -> CM (cluster manager)
        //   CM MoM -> RM (regional manager) or SH if no RM
        //   RM MoM -> SH (state head) or Director
        //   SH MoM -> Director
        //   Director MoM -> self (auto-approved)
        switch ($actor_role) {
            case 'BD':
                $row = $this->db->select('cluster_id')->from('user')->where('uid', $actor_uid)->get()->row();
                if (!$row) return null;
                $cm = $this->db->select('uid')->from('user')
                               ->where('type_id', 13)
                               ->where('cluster_id', $row->cluster_id)
                               ->limit(1)->get()->row();
                return $cm ? $cm->uid : null;
            case 'CM':
                $row = $this->db->select('region_id')->from('user')->where('uid', $actor_uid)->get()->row();
                if (!$row) return null;
                $rm = $this->db->select('uid')->from('user')
                               ->where('type_id', 28)
                               ->where('region_id', $row->region_id)
                               ->limit(1)->get()->row();
                return $rm ? $rm->uid : null;
            case 'RM':
                $sh = $this->db->select('uid')->from('user')
                               ->where('type_id', 29)
                               ->limit(1)->get()->row();
                return $sh ? $sh->uid : null;
            case 'SH':
                $dir = $this->db->select('uid')->from('user')
                                ->where('type_id', 30)
                                ->limit(1)->get()->row();
                return $dir ? $dir->uid : null;
            case 'Director':
                return $actor_uid;  // self approve
        }
        return null;
    }

    private function _audio_log_id($callevent_id) {
        $r = $this->db->select('id')->from('meeting_audio_log')
                      ->where('callevent_id', $callevent_id)
                      ->order_by('id','DESC')->limit(1)->get()->row();
        return $r ? $r->id : null;
    }

    // -----------------------------------------------------------------
    // POST /api/v3/mom/approve
    // -----------------------------------------------------------------
    public function approve() {
        $mom_id = (int)$this->input->post('mom_id');
        $approver_uid = (int)$this->input->post('approver_uid');
        $comment = $this->input->post('comment') ?: null;

        $mom = $this->db->where('id', $mom_id)->get('mom_data')->row_array();
        if (!$mom) return $this->_json(array('error' => 'not_found'), 404);
        if ((int)$mom['approver_uid'] !== $approver_uid) {
            return $this->_json(array('error' => 'not_your_to_approve'), 403);
        }

        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $mom_id)->update('mom_data', array(
            'approved_status' => '1',
            'approved_at' => $now,
            'approver_comment' => $comment
        ));
        return $this->_json(array('ok' => true, 'approved_at' => $now));
    }

    // -----------------------------------------------------------------
    // POST /api/v3/mom/reject
    // -----------------------------------------------------------------
    public function reject() {
        $mom_id = (int)$this->input->post('mom_id');
        $approver_uid = (int)$this->input->post('approver_uid');
        $reason = $this->input->post('reason');

        if (empty($reason)) {
            return $this->_json(array('error' => 'reason_required'), 400);
        }

        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $mom_id)->where('approver_uid', $approver_uid)
                 ->update('mom_data', array(
                    'approved_status' => 'NO RP',
                    'rejected_at' => $now,
                    'approver_comment' => $reason
                 ));
        return $this->_json(array('ok' => true, 'rejected_at' => $now, 'reason' => $reason));
    }

    // -----------------------------------------------------------------
    // GET /api/v3/mom/draft?callevent_id=
    // -----------------------------------------------------------------
    public function draft() {
        $callevent_id = (int)$this->input->get('callevent_id');
        $row = $this->db->where('callevent_id', $callevent_id)
                        ->order_by('id','DESC')->limit(1)
                        ->get('mom_draft')->row_array();
        return $this->_json(array('ok' => true, 'draft' => $row));
    }

    // -----------------------------------------------------------------
    // GET /api/v3/mom/pending_for_manager
    // -----------------------------------------------------------------
    public function pending_for_manager() {
        $manager_uid = (int)$this->input->get('manager_uid');
        $rows = $this->db->select('mom_data.*, init_call.compny_nm AS school_name, init_call.cstatus')
                         ->from('mom_data')
                         ->join('init_call', 'init_call.id = mom_data.cid_id', 'left')
                         ->where('mom_data.approver_uid', $manager_uid)
                         ->where('mom_data.approved_status IS NULL', null, false)
                         ->order_by('mom_data.submitted_at ASC')
                         ->limit(50)
                         ->get()->result_array();
        return $this->_json(array('ok' => true, 'count' => count($rows), 'rows' => $rows));
    }
}
// END UniversalMom controller
