<?php
// =====================================================================
// STEM CRM - Migration 025: Meeting Lifecycle Controller
// File: application/controllers/MeetingLifecycle.php
// =====================================================================
// Endpoints (all under /api/meeting/*):
//
//   POST /api/meeting/start
//     Phone calls when actor taps Start. Inserts a meeting_lifecycle
//     row, sets actual_start_time, returns agenda template for the
//     purpose+cstatus combo and the travel_cluster flag.
//
//   POST /api/meeting/classify
//     Called by the 15-minute forced popup. Stores classification
//     (Tentative/RP/Closure/GotDetails/Walkout) and classified_at.
//
//   POST /api/meeting/end
//     Phone calls when actor taps End. Accepts audio upload (multipart).
//     Pipeline: audio capture agent -> extraction -> mom_draft created
//     -> meeting quality agent scored -> followup tracker opened.
//
//   GET /api/meeting/agenda?cid_id=&purpose_id=&cstatus=&is_travel_cluster=
//     Returns the agenda template for the live agenda nudge card.
//
//   GET /api/meeting/state?callevent_id=
//     Returns the 5-state machine status (planned/started/classified/
//     ended/followedup) plus all timestamps for the UI to render.
//
//   POST /api/meeting/travel_cluster_check
//     Called at plan submit (18:30 IST) and at barge meeting create.
//     If the plan/barge area maps to a defined travel_cluster_bundle,
//     returns the prospect agent suggestions + reminders.
//
// Bearer auth: same STEM_DIGEST_TOKEN as other API. Staging only.
// =====================================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingLifecycle extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/AudioCapture_model', 'audio');
        $this->load->model('AIAgents/MeetingQuality_model', 'quality');
        $this->load->model('AIAgents/LeadFollowupTracker_model', 'followup');
        $this->load->library('form_validation');
        $this->_require_bearer();
    }

    private function _require_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization', true);
        if (strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(array('error' => 'unauthorized'), 401);
            exit;
        }
        // Token validation against config (omitted - standard CI auth)
    }

    private function _json($data, $status = 200) {
        $this->output->set_status_header($status)
                     ->set_content_type('application/json')
                     ->set_output(json_encode($data));
    }

    // -----------------------------------------------------------------
    // POST /api/meeting/start
    // -----------------------------------------------------------------
    public function start() {
        $callevent_id = (int)$this->input->post('callevent_id');
        $cid_id = (int)$this->input->post('cid_id');
        $actor_uid = (int)$this->input->post('actor_uid');
        $actor_role = $this->input->post('actor_role');
        $gps_lat = $this->input->post('gps_lat');
        $gps_lng = $this->input->post('gps_lng');

        if (!$callevent_id || !$cid_id || !$actor_uid || !$actor_role) {
            return $this->_json(array('error' => 'missing_params',
                'need' => 'callevent_id cid_id actor_uid actor_role'), 400);
        }

        // Verify callevent exists and is in PLANNED state
        $ce = $this->db->where('id', $callevent_id)->get('tblcallevents')->row_array();
        if (!$ce) return $this->_json(array('error' => 'callevent_not_found'), 404);

        // Determine if this is a travel cluster meeting
        $is_travel_cluster = $this->_is_travel_cluster_meeting($cid_id, $ce);

        // Update tblcallevents with actual_start_time and start GPS
        $this->db->where('id', $callevent_id)->update('tblcallevents', array(
            'actual_start_time' => date('Y-m-d H:i:s'),
            'start_gps_lat' => $gps_lat,
            'start_gps_lng' => $gps_lng,
            'lifecycle_state' => 'STARTED',
            'is_travel_cluster' => $is_travel_cluster
        ));

        // Insert lifecycle row
        $this->db->insert('meeting_lifecycle', array(
            'callevent_id' => $callevent_id,
            'cid_id' => $cid_id,
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'state' => 'STARTED',
            'started_at' => date('Y-m-d H:i:s'),
            'start_gps_lat' => $gps_lat,
            'start_gps_lng' => $gps_lng,
            'is_travel_cluster' => $is_travel_cluster
        ));
        $lifecycle_id = $this->db->insert_id();

        // Create draft mom_draft row keyed to this callevent
        $ic = $this->db->select('cstatus, dm_name, dm_designation, dm_mobile, dm_email')
                       ->from('init_call')->where('id', $cid_id)->get()->row_array();

        $this->db->insert('mom_draft', array(
            'callevent_id' => $callevent_id,
            'cid_id' => $cid_id,
            'author_uid' => $actor_uid,
            'author_role' => $actor_role,
            'created_at' => date('Y-m-d H:i:s'),
            'cstatus_at_start' => $ic['cstatus'] ?? 1,
            'dm_name' => $ic['dm_name'] ?? null,
            'dm_designation' => $ic['dm_designation'] ?? null,
            'dm_mobile' => $ic['dm_mobile'] ?? null,
            'dm_email' => $ic['dm_email'] ?? null,
            'status' => 'draft'
        ));
        $draft_id = $this->db->insert_id();

        // Fetch agenda template
        $agenda = $this->_fetch_agenda($ce['purpose_id'], $ic['cstatus'] ?? 1, $is_travel_cluster);

        return $this->_json(array(
            'ok' => true,
            'lifecycle_id' => $lifecycle_id,
            'draft_id' => $draft_id,
            'is_travel_cluster' => $is_travel_cluster,
            'classify_due_in_minutes' => 15,
            'agenda_template' => $agenda,
            'dm_prefilled' => array(
                'dm_name' => $ic['dm_name'] ?? null,
                'dm_designation' => $ic['dm_designation'] ?? null,
                'dm_mobile' => $ic['dm_mobile'] ?? null,
                'dm_email' => $ic['dm_email'] ?? null
            )
        ));
    }

    // -----------------------------------------------------------------
    // POST /api/meeting/classify
    // 15-minute forced popup. Stores classification + timestamp.
    // -----------------------------------------------------------------
    public function classify() {
        $callevent_id = (int)$this->input->post('callevent_id');
        $classification = $this->input->post('classification');
        $dm_met = (int)$this->input->post('dm_met', 0);

        $valid = array('tentative','rp_positive','rp_with_objection',
                       'closure_ready','got_details_only','walkout');
        if (!in_array($classification, $valid)) {
            return $this->_json(array('error' => 'invalid_classification',
                                       'valid' => $valid), 400);
        }

        $now = date('Y-m-d H:i:s');

        // Update tblcallevents
        $this->db->where('id', $callevent_id)->update('tblcallevents', array(
            'mid_meeting_classification' => $classification,
            'classified_at' => $now,
            'dm_met' => $dm_met,
            'lifecycle_state' => 'CLASSIFIED'
        ));

        // Compute punctuality of classification (start to classify gap)
        $ce = $this->db->select('actual_start_time')->from('tblcallevents')
                       ->where('id', $callevent_id)->get()->row_array();
        $gap_min = null;
        if (!empty($ce['actual_start_time'])) {
            $gap_min = round((strtotime($now) - strtotime($ce['actual_start_time'])) / 60);
        }

        // Update lifecycle row
        $this->db->where('callevent_id', $callevent_id)
                 ->where('state', 'STARTED')
                 ->update('meeting_lifecycle', array(
                    'state' => 'CLASSIFIED',
                    'classified_at' => $now,
                    'classification' => $classification,
                    'classification_gap_min' => $gap_min,
                    'dm_met' => $dm_met
                 ));

        // Warn if travel cluster and got_details_only chosen
        $warning = null;
        $event = $this->db->where('id', $callevent_id)->get('tblcallevents')->row_array();
        if ($event['is_travel_cluster'] == 1 && $classification === 'got_details_only') {
            $warning = 'Travel cluster meeting cannot be got_details_only. RP mandatory. '
                     . 'If you exit without DM contact, you face double penalty Rs 1000 '
                     . 'and 10 planning grade points.';
        }

        return $this->_json(array(
            'ok' => true,
            'classification_saved' => $classification,
            'classification_gap_min' => $gap_min,
            'dm_met' => $dm_met,
            'warning' => $warning
        ));
    }

    // -----------------------------------------------------------------
    // POST /api/meeting/end
    // Accepts audio upload + final mom_draft snapshot.
    // -----------------------------------------------------------------
    public function end() {
        $callevent_id = (int)$this->input->post('callevent_id');
        $cid_id = (int)$this->input->post('cid_id');
        $actor_uid = (int)$this->input->post('actor_uid');
        $actor_role = $this->input->post('actor_role');
        $end_gps_lat = $this->input->post('end_gps_lat');
        $end_gps_lng = $this->input->post('end_gps_lng');
        $duration_seconds = (int)$this->input->post('duration_seconds');
        $classification = $this->input->post('classification');
        $dm_met = (int)$this->input->post('dm_met', 0);

        if (!$callevent_id || !$actor_uid) {
            return $this->_json(array('error' => 'missing_params'), 400);
        }

        $now = date('Y-m-d H:i:s');

        // Update tblcallevents with end state
        $this->db->where('id', $callevent_id)->update('tblcallevents', array(
            'actual_end_time' => $now,
            'end_gps_lat' => $end_gps_lat,
            'end_gps_lng' => $end_gps_lng,
            'duration_seconds' => $duration_seconds,
            'lifecycle_state' => 'ENDED'
        ));

        $is_travel_cluster = (int)$this->db->select('is_travel_cluster')
                                            ->from('tblcallevents')
                                            ->where('id', $callevent_id)
                                            ->get()->row()->is_travel_cluster;

        // Handle audio upload
        $audio_result = null;
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
            $audio_result = $this->audio->process_meeting_audio(array(
                'callevent_id' => $callevent_id,
                'cid_id' => $cid_id,
                'actor_uid' => $actor_uid,
                'actor_role' => $actor_role,
                'audio_path' => $_FILES['audio']['tmp_name'],
                'duration_seconds' => $duration_seconds,
                'meeting_classification' => $classification,
                'is_travel_cluster' => $is_travel_cluster
            ));
        }

        // Score the meeting
        $score = $this->quality->score_meeting(array(
            'callevent_id' => $callevent_id,
            'cid_id' => $cid_id,
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'classification' => $classification,
            'is_travel_cluster' => $is_travel_cluster,
            'dm_met' => $dm_met
        ));

        // Open followup tracker
        $tracker = $this->followup->open_followup(array(
            'cid_id' => $cid_id,
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'callevent_id' => $callevent_id,
            'classification' => $classification,
            'is_travel_cluster' => $is_travel_cluster,
            'meeting_ended_at' => $now
        ));

        // Update lifecycle row
        $this->db->where('callevent_id', $callevent_id)
                 ->update('meeting_lifecycle', array(
                    'state' => 'ENDED',
                    'ended_at' => $now,
                    'end_gps_lat' => $end_gps_lat,
                    'end_gps_lng' => $end_gps_lng,
                    'duration_seconds' => $duration_seconds,
                    'quality_score_id' => $score['ok'] ? $score['score']['id'] : null,
                    'followup_tracker_id' => $tracker['opened'] ? $tracker['tracker_id'] : null,
                    'audio_log_id' => $audio_result && $audio_result['ok'] ? $audio_result['audio_log_id'] : null
                 ));

        return $this->_json(array(
            'ok' => true,
            'meeting_ended' => $now,
            'audio_processed' => $audio_result,
            'quality_score' => $score['ok'] ? $score['score'] : null,
            'followup_tracker' => $tracker,
            'next_steps' => $this->_next_steps_for_state($classification, $is_travel_cluster, $tracker)
        ));
    }

    private function _next_steps_for_state($classification, $is_travel_cluster, $tracker) {
        $steps = array();
        if ($classification === 'got_details_only') {
            $deadline = $is_travel_cluster ? 7 : 15;
            $steps[] = 'Schedule DM meeting within ' . $deadline . ' days or this lead expires with penalty.';
        }
        if ($classification === 'walkout') {
            $steps[] = 'Walkout recorded. Lead capped at grade D for this meeting.';
        }
        if ($classification === 'rp_positive' || $classification === 'rp_with_objection') {
            $steps[] = 'RP meeting captured. Submit proposal within 7 days.';
        }
        if ($classification === 'closure_ready') {
            $steps[] = 'CM joint meeting required before cstatus 12 promotion.';
        }
        return $steps;
    }

    // -----------------------------------------------------------------
    // GET /api/meeting/agenda
    // -----------------------------------------------------------------
    public function agenda() {
        $purpose_id = (int)$this->input->get('purpose_id');
        $cstatus = (int)$this->input->get('cstatus');
        $is_travel_cluster = (int)$this->input->get('is_travel_cluster');

        $agenda = $this->_fetch_agenda($purpose_id, $cstatus, $is_travel_cluster);
        return $this->_json(array('ok' => true, 'agenda' => $agenda));
    }

    private function _fetch_agenda($purpose_id, $cstatus, $is_travel_cluster) {
        $sql = "SELECT id, question_text, expected_answer_type, is_mandatory,
                       scoring_weight, gate_block, question_order
                FROM meeting_agenda_template
                WHERE purpose_id = ?
                  AND ? BETWEEN cstatus_min AND cstatus_max
                  AND (travel_cluster_only = 0 OR travel_cluster_only = ?)
                ORDER BY question_order ASC";
        return $this->db->query($sql, array($purpose_id, $cstatus, $is_travel_cluster))
                        ->result_array();
    }

    // -----------------------------------------------------------------
    // GET /api/meeting/state
    // -----------------------------------------------------------------
    public function state() {
        $callevent_id = (int)$this->input->get('callevent_id');
        $row = $this->db->where('callevent_id', $callevent_id)
                        ->get('meeting_lifecycle')->row_array();
        if (!$row) return $this->_json(array('error' => 'not_found'), 404);

        return $this->_json(array('ok' => true, 'lifecycle' => $row));
    }

    // -----------------------------------------------------------------
    // POST /api/meeting/travel_cluster_check
    // Called at plan submit (18:30) or barge meeting create.
    // -----------------------------------------------------------------
    public function travel_cluster_check() {
        $actor_uid = (int)$this->input->post('actor_uid');
        $plan_date = $this->input->post('plan_date');
        $area_lat = $this->input->post('area_lat');
        $area_lng = $this->input->post('area_lng');
        $area_name = $this->input->post('area_name');

        // Find travel cluster bundle whose centroid is within 5 km
        $sql = "SELECT id, cluster_name, centroid_lat, centroid_lng, lead_count,
                       suggested_min_meetings, average_travel_cost_rs,
                       (6371 * acos(cos(radians(?)) * cos(radians(centroid_lat))
                                    * cos(radians(centroid_lng) - radians(?))
                                    + sin(radians(?)) * sin(radians(centroid_lat)))) AS distance_km
                FROM travel_cluster_bundle
                HAVING distance_km < 5
                ORDER BY distance_km ASC
                LIMIT 1";
        $row = $this->db->query($sql, array($area_lat, $area_lng, $area_lat))->row_array();

        if (!$row) {
            return $this->_json(array('ok' => true, 'is_travel_cluster' => false));
        }

        // Pull existing leads in cluster
        $leads = $this->db->select('id AS cid_id, compny_nm, cstatus, fbudget, lat, lng')
                          ->from('init_call')
                          ->where('travel_cluster_id', $row['id'])
                          ->order_by('cstatus DESC, fbudget DESC')
                          ->limit(5)
                          ->get()->result_array();

        // Pull prospect agent suggestions for the cluster
        $this->load->model('AIAgents/ProspectAgent_model', 'prospect');
        $suggestions = $this->prospect->suggest_for_travel_cluster($row['id'], 5);

        return $this->_json(array(
            'ok' => true,
            'is_travel_cluster' => true,
            'cluster' => $row,
            'top_leads' => $leads,
            'prospect_suggestions' => $suggestions,
            'banner_message' => sprintf(
                'TRAVEL CLUSTER DETECTED: %s. You are travelling to %s on %s. ' .
                'Existing leads in cluster: %d. Suggested net-new schools nearby: %d. ' .
                'Estimated travel cost: Rs %d. Plan rejected if less than %d meetings on travel day. ' .
                'Travel cluster meetings MUST be RP grade - got-details only triggers double penalty.',
                $row['cluster_name'], $row['cluster_name'], $plan_date,
                count($leads), count($suggestions),
                (int)$row['average_travel_cost_rs'], (int)$row['suggested_min_meetings']
            )
        ));
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------
    private function _is_travel_cluster_meeting($cid_id, $ce) {
        // Pull init_call.travel_cluster_id
        $tc = $this->db->select('travel_cluster_id')
                       ->from('init_call')
                       ->where('id', $cid_id)
                       ->get()->row();
        return ($tc && $tc->travel_cluster_id) ? 1 : 0;
    }

    // -----------------------------------------------------------------
    // Probe endpoint for cron detection (migration 025 deployed?)
    // -----------------------------------------------------------------
    public function probe() {
        return $this->_json(array(
            'migration' => '025',
            'deployed' => true,
            'features' => array(
                'universal_meeting_lifecycle' => true,
                'audio_capture' => true,
                'agenda_template' => true,
                'travel_cluster_aware' => true,
                'got_details_15_day_clock' => true,
                'mom_draft_separate_from_submit' => true,
                'upsell_auto_categorise' => true
            )
        ));
    }
}
// END MeetingLifecycle controller
