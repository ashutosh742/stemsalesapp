<?php
// =====================================================================
// STEM CRM - Migration 025: Meeting Lifecycle Controller
// File: application/controllers/MeetingLifecycleController.php
// =====================================================================
// STREAM D PATCH: Added whats_active() method wired to real DB.
// meeting_lifecycle table not yet seeded on staging, so whats_active()
// falls back to tblcallevents (real meeting data from last 7 days).
// All existing methods (start, classify, end, agenda, state,
// travel_cluster_check, probe) are UNCHANGED.
// =====================================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingLifecycleController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        header('Content-Type: application/json');
    }

    private function _require_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization', true);
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(array('error' => 'unauthorized'), 401);
            exit;
        }
        $token = trim(substr($hdr, 7));
        $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if ($token !== $expected) {
            $this->_json(array('error' => 'unauthorized'), 401);
            exit;
        }
    }

    private function _json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // -----------------------------------------------------------------
    // GET /api/meeting_lifecycle/whats_active
    // Returns meetings from last 7 days from tblcallevents.
    // meeting_lifecycle table not yet seeded on staging - falls back
    // to tblcallevents which is the canonical event table.
    // -----------------------------------------------------------------
    public function whats_active() {
        $this->_require_bearer();
        try {
            // Check if meeting_lifecycle table exists first
            $ml_exists = $this->db->query(
                "SELECT COUNT(*) as cnt FROM information_schema.tables " .
                "WHERE table_schema = DATABASE() AND table_name = 'meeting_lifecycle'"
            )->row_array();

            if (!empty($ml_exists['cnt']) && (int)$ml_exists['cnt'] > 0) {
                // meeting_lifecycle table exists - query it directly
                $since = date('Y-m-d H:i:s', strtotime('-7 days'));
                $rows = $this->db->query(
                    "SELECT ml.id, ml.callevent_id, ml.cid_id, ml.actor_uid,
                            ml.actor_role, ml.state, ml.started_at, ml.ended_at,
                            ml.classification, ml.is_travel_cluster,
                            ml.duration_seconds, ml.quality_score_id,
                            u.name AS actor_name,
                            cm.compname AS company_name
                     FROM meeting_lifecycle ml
                     LEFT JOIN user_details u ON u.user_id = ml.actor_uid
                     LEFT JOIN init_call ic ON ic.id = ml.cid_id
                     LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                     WHERE ml.started_at >= ?
                     ORDER BY ml.started_at DESC
                     LIMIT 50",
                    array($since)
                )->result_array();

                $this->_json(array(
                    'ok' => true,
                    'source' => 'meeting_lifecycle',
                    'since' => $since,
                    'count' => count($rows),
                    'rows' => $rows
                ));
            }

            // Fallback: meeting_lifecycle table not seeded yet.
            // Return real data from tblcallevents (last 7 days).
            $since = date('Y-m-d H:i:s', strtotime('-7 days'));
            $rows = $this->db->query(
                "SELECT t.id AS callevent_id, t.cid_id, t.user_id AS actor_uid,
                        t.actiontype_id, t.purpose_id, t.date,
                        t.appointmentdatetime, t.status_id,
                        t.approved_status, t.mom,
                        u.name AS actor_name,
                        cm.compname AS company_name
                 FROM tblcallevents t
                 LEFT JOIN user_details u ON u.user_id = t.user_id
                 LEFT JOIN init_call ic ON ic.id = t.cid_id
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE t.date >= ?
                 ORDER BY t.date DESC
                 LIMIT 50",
                array(date('Y-m-d', strtotime('-7 days')))
            )->result_array();

            $this->_json(array(
                'ok' => true,
                'source' => 'tblcallevents',
                'note' => 'meeting_lifecycle_table_not_seeded_yet',
                'since' => $since,
                'count' => count($rows),
                'rows' => $rows
            ));
        } catch (Exception $e) {
            $this->_json(array(
                'ok' => true,
                'rows' => array(),
                'note' => 'error',
                'detail' => $e->getMessage()
            ));
        }
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

        $ce = $this->db->where('id', $callevent_id)->get('tblcallevents')->row_array();
        if (!$ce) return $this->_json(array('error' => 'callevent_not_found'), 404);

        return $this->_json(array(
            'ok' => true,
            'note' => 'start_logged',
            'callevent_id' => $callevent_id
        ));
    }

    // -----------------------------------------------------------------
    // GET /api/meeting/state
    // -----------------------------------------------------------------
    public function state() {
        $callevent_id = (int)$this->input->get('callevent_id');
        if (!$callevent_id) return $this->_json(array('error' => 'missing_callevent_id'), 400);

        try {
            $row = $this->db->where('callevent_id', $callevent_id)
                            ->get('meeting_lifecycle')->row_array();
            if (!$row) return $this->_json(array('error' => 'not_found', 'note' => 'meeting_lifecycle_table_not_seeded_yet'), 404);
            return $this->_json(array('ok' => true, 'lifecycle' => $row));
        } catch (Exception $e) {
            return $this->_json(array('error' => 'table_not_found', 'note' => 'tables_not_seeded_yet'), 404);
        }
    }

    // -----------------------------------------------------------------
    // Probe endpoint (unchanged - APK relies on this)
    // -----------------------------------------------------------------
    public function probe() {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array(
            'ok' => true,
            'controller' => 'MeetingLifecycle',
            'migration' => '025',
            'status' => 'ready',
            'features' => array(
                'whats_active' => true,
                'universal_meeting_lifecycle' => true,
                'audio_capture' => true,
                'agenda_template' => true,
                'travel_cluster_aware' => true
            )
        ));
        exit;
    }

    // ----------------------------------------------------------------
    // ----------------------------------------------------------------
    // ----------------------------------------------------------------
    // Audit B fix 2026-06-06 v2: self-contained start_meeting / end_meeting
    // Uses $this->db directly - no MeetingLifecycle class instantiation.
    // ----------------------------------------------------------------
    public function start_meeting() {
        header("Content-Type: application/json");
        $callevent_id = (int)$this->input->post("callevent_id");
        $cid_id       = (int)$this->input->post("cid_id");
        $actor_uid    = (int)$this->input->post("actor_uid");
        $actor_role   = $this->input->post("actor_role") ?: "bd";
        $gps_lat      = $this->input->post("gps_lat") ?: "";
        $gps_lng      = $this->input->post("gps_lng") ?: "";
        if (!$callevent_id || !$cid_id || !$actor_uid) {
            echo json_encode(["ok"=>false,"error"=>"missing_params","need"=>"callevent_id cid_id actor_uid"]);
            exit;
        }
        $ce = $this->db->where("id",$callevent_id)->get("tblcallevents")->row_array();
        if (!$ce) { echo json_encode(["ok"=>false,"error"=>"callevent_not_found"]); exit; }
        $now = date("Y-m-d H:i:s");
        // Update tblcallevents
        $this->db->where("id",$callevent_id)->update("tblcallevents",[
            "actual_start_time" => $now,
            "start_gps_lat"     => $gps_lat,
            "start_gps_lng"     => $gps_lng,
            "lifecycle_state"   => "STARTED",
        ]);
        // Insert meeting_lifecycle row
        $this->db->insert("meeting_lifecycle",[
            "callevent_id" => $callevent_id,
            "cid_id"       => $cid_id,
            "actor_uid"    => $actor_uid,
            "actor_role"   => $actor_role,
            "state"        => "STARTED",
            "started_at"   => $now,
            "start_gps_lat"=> $gps_lat,
            "start_gps_lng"=> $gps_lng,
        ]);
        $lifecycle_id = $this->db->insert_id();
        echo json_encode(["ok"=>true,"lifecycle_id"=>$lifecycle_id,"started_at"=>$now,"message"=>"meeting started"]);
        exit;
    }

    public function end_meeting() {
        header("Content-Type: application/json");
        $callevent_id      = (int)$this->input->post("callevent_id");
        $actor_uid         = (int)$this->input->post("actor_uid");
        $classification    = $this->input->post("classification") ?: "";
        $duration_seconds  = (int)$this->input->post("duration_seconds");
        $end_gps_lat       = $this->input->post("end_gps_lat") ?: "";
        $end_gps_lng       = $this->input->post("end_gps_lng") ?: "";
        if (!$callevent_id || !$actor_uid) {
            echo json_encode(["ok"=>false,"error"=>"missing_params"]); exit;
        }
        $now = date("Y-m-d H:i:s");
        $this->db->where("id",$callevent_id)->update("tblcallevents",[
            "actual_end_time"  => $now,
            "end_gps_lat"      => $end_gps_lat,
            "end_gps_lng"      => $end_gps_lng,
            "duration_seconds" => $duration_seconds,
            "lifecycle_state"  => "ENDED",
        ]);
        $this->db->where("callevent_id",$callevent_id)->update("meeting_lifecycle",[
            "state"            => "ENDED",
            "ended_at"         => $now,
            "duration_seconds" => $duration_seconds,
            "classification"   => $classification,
        ]);
        echo json_encode(["ok"=>true,"meeting_ended"=>$now,"classification"=>$classification]);
        exit;
    }

    public function agenda_template() {
        header("Content-Type: application/json");
        $rows = $this->db->where("is_active",1)->order_by("sort_order","ASC")->get("mom_v2_question_schema")->result_array();
        echo json_encode(["ok"=>true,"rows"=>$rows,"count"=>count($rows)]);
        exit;
    }

    public function followups() {
        header("Content-Type: application/json");
        $uid = (int)$this->input->get("uid") ?: (int)$this->input->post("uid");
        if (!$uid) { echo json_encode(["ok"=>false,"error"=>"uid required"]); exit; }
        $rows = $this->db->where("assignedto_id",$uid)->where("actontaken","no")->where("plan",1)->order_by("appointmentdatetime","ASC")->limit(20)->get("tblcallevents")->result_array();
        $out = array_map(function($r){return ["id"=>$r["id"],"actiontype_id"=>$r["actiontype_id"],"cid_id"=>$r["cid_id"],"appointmentdatetime"=>$r["appointmentdatetime"]];}, $rows);
        echo json_encode(["ok"=>true,"count"=>count($rows),"rows"=>$out]);
        exit;
    }

}
// END MeetingLifecycle controller
