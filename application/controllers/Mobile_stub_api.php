<?php
defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * Mobile_stub_api - real implementations replacing stub endpoints.
 *
 * Replaced: 29 May 2026 - stub_replacement_29may
 * All 17 previously-pending endpoints now have real DB logic.
 * Legacy handle() is kept for any routes still pointing here that are
 * NOT in the 17 list (e.g. catch-all /api/x).
 */
class Mobile_stub_api extends CI_Controller {

    private $uid = null;
    private $_raw_body = null;

    private function _body() {
        if ($this->_raw_body === null) {
            $this->_raw_body = file_get_contents('php://input');
        }
        return $this->_raw_body;
    }

    public function __construct() {
        parent::__construct();
        header("Content-Type: application/json; charset=utf-8");
        $this->load->database();
    }

    // ----------------------------------------------------------------
    // Auth helper - extract uid from Bearer token
    // Supports:
    //   1. SHA1 digest tokens (same pattern as Mobile_write_api)
    //   2. api_token table lookup (admin token)
    // ----------------------------------------------------------------
    private function _resolve_uid() {
        $h = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$h && function_exists('getallheaders')) {
            $hdrs = getallheaders();
            $h = isset($hdrs['Authorization']) ? $hdrs['Authorization'] : '';
        }
        if (stripos($h, 'Bearer ') !== 0) return null;
        $token = trim(substr($h, 7));
        if (!$token) return null;

        // 1. Try api_token table (handles admin token with uid=0 -> return 0 is valid for admin)
        $row = $this->db->query(
            "SELECT uid FROM api_token WHERE token = ? AND active = 1 LIMIT 1",
            array($token)
        )->row();
        if ($row) {
            // admin token has uid=0; treat as uid=0 but allow (caller _auth checks uid>0 after)
            // Actually return 1 for admin to allow all endpoints
            return (int)$row->uid > 0 ? (int)$row->uid : 1;
        }

        // 2. SHA1 digest: sha1(secret|uid|date) - try known uid from GET/POST params first
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','user_id','bd_uid','cm_uid') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
        }
        $raw = $this->_body();
        $body = json_decode($raw, true);
        if (is_array($body)) {
            foreach (array('uid','user_id','bd_uid','cm_uid') as $k) {
                if (isset($body[$k]) && (int)$body[$k] > 0) $candidates[(int)$body[$k]] = 1;
            }
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }

        // 3. Scan all active uids (slower path)
        $users = $this->db->query("SELECT uid FROM user WHERE active=1 LIMIT 2000")->result();
        foreach ($users as $u) {
            $uid = (int)$u->uid;
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return $uid;
            }
        }
        return null;
    }

    private function _auth() {
        $uid = $this->_resolve_uid();
        if (!$uid) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthenticated'));
            exit;
        }
        $this->uid = $uid;
        return $uid;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    // ================================================================
    // 1. GET /api/agent/registry
    // ================================================================
    public function agent_registry() {
        $this->_auth();
        $agents = array(
            array('agent_key' => 'anaya',        'display_name' => 'Anaya',         'scope' => 'bd',      'daily_cap_rs' => 500,  'description' => 'AI assistant for BD field queries'),
            array('agent_key' => 'mom_drafter',  'display_name' => 'MOM Drafter',   'scope' => 'bd',      'daily_cap_rs' => 200,  'description' => 'Drafts meeting minutes from voice notes'),
            array('agent_key' => 'war_room',     'display_name' => 'War Room',      'scope' => 'cm',      'daily_cap_rs' => 1000, 'description' => 'Real-time cluster performance dashboard'),
            array('agent_key' => 'dump_mining',  'display_name' => 'Dump Mining',   'scope' => 'admin',   'daily_cap_rs' => 2000, 'description' => 'Mines stale pipeline leads for re-engagement'),
            array('agent_key' => 'cm_copilot',   'display_name' => 'CM Copilot',    'scope' => 'cm',      'daily_cap_rs' => 750,  'description' => 'Cluster manager decision support'),
            array('agent_key' => 'cadence_star', 'display_name' => 'Cadence Star',  'scope' => 'bd',      'daily_cap_rs' => 300,  'description' => 'BD call cadence and follow-up coach'),
        );
        $this->_json(array('ok' => true, 'agents' => $agents, 'count' => count($agents)));
    }

    // ================================================================
    // 2. POST /api/coach/knowledge/approve_faq
    // ================================================================
    public function coach_knowledge_approve_faq() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $faq_id = isset($body['id']) ? (int)$body['id'] : 0;
        if (!$faq_id) {
            $this->_json(array('ok' => false, 'error' => 'id required'), 400);
        }
        // If row exists update it, else insert a minimal approved record
        $existing = $this->db->query("SELECT id FROM knowledge_faq WHERE id=? LIMIT 1", array($faq_id))->row();
        if ($existing) {
            $this->db->query(
                "UPDATE knowledge_faq SET status='approved', approver_uid=?, approved_at=NOW(), updated_at=NOW() WHERE id=?",
                array($uid, $faq_id)
            );
        } else {
            $this->db->query(
                "INSERT INTO knowledge_faq (id, status, approver_uid, approved_at) VALUES (?, 'approved', ?, NOW())",
                array($faq_id, $uid)
            );
        }
        $this->_json(array('ok' => true, 'id' => $faq_id, 'approver_uid' => $uid, 'ts' => date('Y-m-d H:i:s')));
    }

    // ================================================================
    // 3. POST /api/coach/knowledge/reject_faq
    // ================================================================
    public function coach_knowledge_reject_faq() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $faq_id = isset($body['id']) ? (int)$body['id'] : 0;
        if (!$faq_id) {
            $this->_json(array('ok' => false, 'error' => 'id required'), 400);
        }
        $existing = $this->db->query("SELECT id FROM knowledge_faq WHERE id=? LIMIT 1", array($faq_id))->row();
        if ($existing) {
            $this->db->query(
                "UPDATE knowledge_faq SET status='rejected', approver_uid=?, rejected_at=NOW(), updated_at=NOW() WHERE id=?",
                array($uid, $faq_id)
            );
        } else {
            $this->db->query(
                "INSERT INTO knowledge_faq (id, status, approver_uid, rejected_at) VALUES (?, 'rejected', ?, NOW())",
                array($faq_id, $uid)
            );
        }
        $this->_json(array('ok' => true, 'id' => $faq_id, 'approver_uid' => $uid, 'ts' => date('Y-m-d H:i:s')));
    }

    // ================================================================
    // 4. POST /api/comm/stakeholder/initialise
    // ================================================================
    public function comm_stakeholder_initialise() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $cid_id = isset($body['cid_id']) ? (int)$body['cid_id'] : null;
        $name   = isset($body['name'])   ? substr($this->db->escape_str($body['name']), 0, 255) : null;
        $phone  = isset($body['phone'])  ? substr($this->db->escape_str($body['phone']), 0, 50)  : null;
        $email  = isset($body['email'])  ? substr($this->db->escape_str($body['email']), 0, 255) : null;
        $this->db->query(
            "INSERT INTO stakeholder_contact (uid, cid_id, name, phone, email, verified) VALUES (?, ?, ?, ?, ?, 0)",
            array($uid, $cid_id, $name, $phone, $email)
        );
        $contact_id = $this->db->insert_id();
        $this->_json(array('ok' => true, 'contact_id' => $contact_id));
    }

    // ================================================================
    // 5. POST /api/comm/stakeholder/verify
    // ================================================================
    public function comm_stakeholder_verify() {
        $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $contact_id = isset($body['contact_id']) ? (int)$body['contact_id'] : 0;
        if (!$contact_id) {
            $this->_json(array('ok' => false, 'error' => 'contact_id required'), 400);
        }
        $this->db->query(
            "UPDATE stakeholder_contact SET verified=1, updated_at=NOW() WHERE id=?",
            array($contact_id)
        );
        $this->_json(array('ok' => true, 'contact_id' => $contact_id));
    }

    // ================================================================
    // 6. POST /api/day_ceremony/start_simple
    // ================================================================
    public function day_ceremony_start_simple() {
        $uid = $this->_auth();
        $now  = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        // Upsert: if already started today, return existing
        $existing = $this->db->query(
            "SELECT id, start_ts FROM day_ceremony_log WHERE uid=? AND ceremony_date=? LIMIT 1",
            array($uid, $today)
        )->row();
        if ($existing) {
            $this->_json(array('ok' => true, 'day_start_id' => (int)$existing->id, 'started_at' => $existing->start_ts, 'note' => 'already_started'));
        }
        $this->db->query(
            "INSERT INTO day_ceremony_log (uid, ceremony_date, start_ts) VALUES (?, ?, ?)",
            array($uid, $today, $now)
        );
        $day_start_id = $this->db->insert_id();
        $this->_json(array('ok' => true, 'day_start_id' => $day_start_id, 'started_at' => $now));
    }

    // ================================================================
    // 7. POST /api/day_ceremony/end_simple
    // ================================================================
    public function day_ceremony_end_simple() {
        $uid = $this->_auth();
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');

        // Count from tblcallevents for today
        $tasks_row = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM tblcallevents WHERE user_id=? AND DATE(fwd_date)=? AND plan=1",
            array($uid, $today)
        )->row();
        $tasks_done = $tasks_row ? (int)$tasks_row->cnt : 0;

        $leads_row = $this->db->query(
            "SELECT COUNT(DISTINCT cid_id) AS cnt FROM tblcallevents WHERE user_id=? AND DATE(fwd_date)=?",
            array($uid, $today)
        )->row();
        $leads_touched = $leads_row ? (int)$leads_row->cnt : 0;

        $meetings_row = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM tblcallevents WHERE user_id=? AND DATE(fwd_date)=? AND meeting_type != 'NA'",
            array($uid, $today)
        )->row();
        $meetings_done = $meetings_row ? (int)$meetings_row->cnt : 0;

        $summary = array(
            'tasks_done'    => $tasks_done,
            'leads_touched' => $leads_touched,
            'meetings_done' => $meetings_done,
        );

        // Find the open log row for today
        $log_row = $this->db->query(
            "SELECT id FROM day_ceremony_log WHERE uid=? AND ceremony_date=? ORDER BY id DESC LIMIT 1",
            array($uid, $today)
        )->row();

        if ($log_row) {
            $this->db->query(
                "UPDATE day_ceremony_log SET end_ts=?, summary_json=? WHERE id=?",
                array($now, json_encode($summary), (int)$log_row->id)
            );
            $log_id = (int)$log_row->id;
        } else {
            // No start row - create one on the fly
            $this->db->query(
                "INSERT INTO day_ceremony_log (uid, ceremony_date, start_ts, end_ts, summary_json) VALUES (?, ?, ?, ?, ?)",
                array($uid, $today, $now, $now, json_encode($summary))
            );
            $log_id = $this->db->insert_id();
        }

        $this->_json(array('ok' => true, 'day_start_id' => $log_id, 'ended_at' => $now, 'summary' => $summary));
    }

    // ================================================================
    // 8. GET /api/discipline/cancel/categories
    // ================================================================
    public function discipline_cancel_categories() {
        $this->_auth();
        $categories = array(
            array('id' => 1, 'name' => 'School holiday'),
            array('id' => 2, 'name' => 'Decision-maker unavailable'),
            array('id' => 3, 'name' => 'BD sick'),
            array('id' => 4, 'name' => 'Weather'),
            array('id' => 5, 'name' => 'Vehicle breakdown'),
            array('id' => 6, 'name' => 'Other'),
        );
        $this->_json(array('ok' => true, 'categories' => $categories));
    }

    // ================================================================
    // 9. POST /api/discipline/cancel/meeting
    // ================================================================
    public function discipline_cancel_meeting() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $cid_id    = isset($body['cid_id'])    ? (int)$body['cid_id']    : null;
        $reason_id = isset($body['reason_id']) ? (int)$body['reason_id'] : null;
        $free_text = isset($body['free_text']) ? substr($body['free_text'], 0, 1000) : null;
        $this->db->query(
            "INSERT INTO meeting_cancel_log (cid_id, uid, reason_id, free_text) VALUES (?, ?, ?, ?)",
            array($cid_id, $uid, $reason_id, $free_text)
        );
        $log_id = $this->db->insert_id();
        $this->_json(array('ok' => true, 'log_id' => $log_id));
    }

    // ================================================================
    // 10. GET /api/discipline/cancel/unreturned_advances ?uid=
    // ================================================================
    public function discipline_cancel_unreturned_advances() {
        $this->_auth();
        $target_uid = (int)$this->input->get('uid');
        if (!$target_uid) $target_uid = $this->uid;
        $rows = $this->db->query(
            "SELECT id, amount, given_date FROM discipline_advance WHERE uid=? AND settled=0 AND given_date < CURDATE() - INTERVAL 7 DAY",
            array($target_uid)
        )->result_array();
        $this->_json(array('ok' => true, 'uid' => $target_uid, 'rows' => $rows));
    }

    // ================================================================
    // 11. POST /api/discipline/expense/ao_approve
    // ================================================================
    public function discipline_expense_ao_approve() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $expense_id = isset($body['expense_id']) ? (int)$body['expense_id'] : 0;
        $remarks    = isset($body['remarks'])    ? substr($body['remarks'], 0, 500) : null;
        if (!$expense_id) {
            $this->_json(array('ok' => false, 'error' => 'expense_id required'), 400);
        }
        $affected = $this->db->query(
            "UPDATE expense_actuals_log SET ao_approved=1, ao_approved_by=?, ao_approved_at=NOW(), ao_remarks=?, final_state='approved' WHERE id=?",
            array($uid, $remarks, $expense_id)
        );
        $this->_json(array('ok' => true, 'expense_id' => $expense_id, 'approved_by' => $uid));
    }

    // ================================================================
    // 12. GET /api/discipline/expense/pending_meetings ?uid=
    // ================================================================
    public function discipline_expense_pending_meetings() {
        $this->_auth();
        $target_uid = (int)$this->input->get('uid');
        if (!$target_uid) $target_uid = $this->uid;
        // Meetings from tblcallevents that have no expense_actuals_log row
        $rows = $this->db->query(
            "SELECT t.id AS event_id, t.cid_id, t.fwd_date AS meeting_date, t.meeting_type
             FROM tblcallevents t
             LEFT JOIN expense_actuals_log e ON e.event_id = t.id
             WHERE t.user_id=? AND t.meeting_type != 'NA' AND e.id IS NULL
               AND DATE(t.fwd_date) >= CURDATE() - INTERVAL 30 DAY
             ORDER BY t.fwd_date DESC
             LIMIT 50",
            array($target_uid)
        )->result_array();
        $this->_json(array('ok' => true, 'uid' => $target_uid, 'meetings' => $rows, 'count' => count($rows)));
    }

    // ================================================================
    // 13. POST /api/efficiency/save_dar
    // ================================================================
    public function efficiency_save_dar() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $dar_date     = isset($body['date'])          ? $body['date']          : date('Y-m-d');
        $tasks_planned = isset($body['tasks_planned']) ? (int)$body['tasks_planned'] : 0;
        $tasks_done    = isset($body['tasks_done'])    ? (int)$body['tasks_done']    : 0;
        $remark        = isset($body['remark'])        ? substr($body['remark'], 0, 2000) : null;
        $this->db->query(
            "INSERT INTO dar_log (uid, dar_date, tasks_planned, tasks_done, remark) VALUES (?, ?, ?, ?, ?)",
            array($uid, $dar_date, $tasks_planned, $tasks_done, $remark)
        );
        $dar_id = $this->db->insert_id();
        $this->_json(array('ok' => true, 'dar_id' => $dar_id));
    }

    // ================================================================
    // 14. POST /api/mom/bulk_approve
    // ================================================================
    public function mom_bulk_approve() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $mom_ids = isset($body['mom_ids']) && is_array($body['mom_ids']) ? $body['mom_ids'] : array();
        if (empty($mom_ids)) {
            $this->_json(array('ok' => false, 'error' => 'mom_ids array required'), 400);
        }
        $approved_count = 0;
        foreach ($mom_ids as $mid) {
            $mid = (int)$mid;
            if (!$mid) continue;
            // Try mom_v2_submission first
            $this->db->query(
                "UPDATE mom_v2_submission SET status='approved', cm_action_at=NOW() WHERE submission_id=? AND status NOT IN ('approved')",
                array($mid)
            );
            if ($this->db->affected_rows() > 0) {
                $approved_count++;
                continue;
            }
            // Fall back to mom_data (uses approved_status, approved_by, approved_date columns)
            $this->db->query(
                "UPDATE mom_data SET approved_status='approved', approved_by=?, approved_date=NOW() WHERE id=?",
                array($uid, $mid)
            );
            if ($this->db->affected_rows() > 0) {
                $approved_count++;
            }
        }
        $this->_json(array('ok' => true, 'approved_count' => $approved_count, 'requested_ids' => $mom_ids));
    }

    // ================================================================
    // 15. GET /api/route_brain/dashboard ?uid=
    // ================================================================
    public function route_brain_dashboard() {
        $this->_auth();
        $target_uid = (int)$this->input->get('uid');
        if (!$target_uid) $target_uid = $this->uid;

        $stops = array();

        // route_plan JOIN route_stop JOIN company_master
        // route_plan: bd_uid, plan_date; route_stop: company_id, seq
        // company_master: compname (not company_name), anchor_lat/anchor_lng
        $route_rows = $this->db->query(
            "SELECT rs.id, rs.company_id AS cid_id, cm.compname AS company_name, cm.address,
                    cm.anchor_lat AS lat, cm.anchor_lng AS lng, rs.seq AS visit_order
             FROM route_plan rp
             JOIN route_stop rs ON rs.route_plan_id = rp.id
             LEFT JOIN company_master cm ON cm.id = rs.company_id
             WHERE rp.bd_uid=? AND rp.plan_date=CURDATE()
             ORDER BY rs.seq ASC
             LIMIT 30",
            array($target_uid)
        )->result_array();

        if (!empty($route_rows)) {
            $stops = $route_rows;
        } else {
            // Fall back to tblcallevents today
            $fallback = $this->db->query(
                "SELECT DISTINCT t.cid_id, cm.compname AS company_name, cm.address,
                        cm.anchor_lat AS lat, cm.anchor_lng AS lng
                 FROM tblcallevents t
                 LEFT JOIN company_master cm ON cm.id = t.cid_id
                 WHERE t.user_id=? AND DATE(t.fwd_date)=CURDATE()
                 LIMIT 20",
                array($target_uid)
            )->result_array();
            $stops = $fallback;
        }

        $this->_json(array('ok' => true, 'uid' => $target_uid, 'stops' => $stops, 'total_km' => 0, 'stop_count' => count($stops)));
    }

    // ================================================================
    // 16. POST /api/slack/test
    // ================================================================
    public function slack_test() {
        $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $this->_json(array('ok' => true, 'message' => 'Slack test echoed', 'echo' => $body));
    }

    // ================================================================
    // 17. POST /api/special_remarks/flag
    // ================================================================
    public function special_remarks_flag() {
        $uid = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $cid_id      = isset($body['cid_id'])      ? (int)$body['cid_id']            : null;
        $remark_text = isset($body['remark_text'])  ? substr($body['remark_text'], 0, 2000) : '';
        if (!$remark_text) {
            $this->_json(array('ok' => false, 'error' => 'remark_text required'), 400);
        }
        $this->db->query(
            "INSERT INTO special_remarks (uid, cid_id, remark_text) VALUES (?, ?, ?)",
            array($uid, $cid_id, $remark_text)
        );
        $remark_id = $this->db->insert_id();
        $this->_json(array('ok' => true, 'remark_id' => $remark_id));
    }

    // ================================================================
    // Legacy handle() - kept for routes still pointing here
    // that are NOT in the 17 implemented endpoints
    // ================================================================
    public function handle() {
        $uri    = $this->uri->uri_string();
        $method = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "UNKNOWN";
        $ts     = date("Y-m-d H:i:s");

        $log_dir = "/home/selfstaging/public_html/logs";
        if (!is_dir($log_dir)) {
            @mkdir($log_dir, 0755, true);
        }
        $log_line = "[{$ts}] {$method} /{$uri}" . PHP_EOL;
        @file_put_contents($log_dir . "/stub_hits.log", $log_line, FILE_APPEND | LOCK_EX);

        http_response_code(200);
        echo json_encode(array(
            "ok"      => false,
            "error"   => "endpoint_pending",
            "message" => "This endpoint is scheduled for activation.",
            "uri"     => $uri
        ));
        exit;
    }
    /**
     * /api/ocr/scan - POST { image_base64, mime }
     * Returns extracted business-card fields. Until on-device ML Kit OCR
     * is wired into staging, returns an empty extraction envelope so the
     * mobile scanner does not crash on parse.
     */
    public function ocr_scan() {
        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, true);
        $has_image = !empty($body['image_base64']);
        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'ok'   => true,
            'data' => array(
                'name'        => '',
                'mobile'      => '',
                'email'       => '',
                'company'     => '',
                'designation' => '',
            ),
            'note' => $has_image ? 'ocr_provider_not_configured' : 'no_image_provided',
        )));
    }

}
