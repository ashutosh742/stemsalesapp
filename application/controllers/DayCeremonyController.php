<?php
/**
 * DayCeremonyController.php
 * Deploy to: application/controllers/DayCeremonyController.php
 *
 * M055 Day Management controller.
 * All endpoints require Authorization: Bearer <STEM_DIGEST_TOKEN>.
 * All responses: JSON {success, data, error}.
 * 200 on success, 400 on bad input, 401 on missing or bad token.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class DayCeremonyController extends CI_Controller
{
    // STEM_DIGEST_TOKEN env variable name or config key.
    // Loaded from environment; falls back to CI config 'stem_digest_token'.
    private $valid_token = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/DayCeremony_model', 'day_ceremony');
        $this->load->helper(['url']);
        $this->output->set_content_type('application/json');

        // Resolve token: env, config, or hardcoded fallback (matches DayCeremonyStatusController).
        $env_token = getenv('STEM_DIGEST_TOKEN');
        $cfg_token = $this->config->item('stem_digest_token');
        $this->valid_token = $env_token ?: ($cfg_token ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo');

        // Enforce Bearer auth on every request.
        $this->_require_auth();
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _require_auth()
    // Reads Authorization header. Returns 401 and exits if invalid.
    // -------------------------------------------------------------------------
    private function _require_auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $header = $this->input->get_request_header('Authorization', TRUE);
        if (empty($header)) {
            // Fallback: PHP-FPM sometimes drops the header from CI's input handler.
            $header = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        }
        if (empty($header)) {
            $this->_json_exit(401, false, null, 'Authorization header missing.');
        }

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            $this->_json_exit(401, false, null, 'Authorization header must use Bearer scheme.');
        }

        $token = $matches[1];

        if (empty($this->valid_token) || !hash_equals($this->valid_token, $token)) {
            $this->_json_exit(401, false, null, 'Invalid or expired token.');
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _json_exit($http_code, $success, $data, $error)
    // Sets status, outputs JSON, and exits.
    // -------------------------------------------------------------------------
    private function _json_exit($http_code, $success, $data = null, $error = null)
    {
        http_response_code($http_code);
        $payload = [
            'success' => (bool) $success,
            'data'    => $data,
            'error'   => $error,
        ];
        echo json_encode($payload);
        exit;
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _ok($data)
    // -------------------------------------------------------------------------
    private function _ok($data)
    {
        $this->_json_exit(200, true, $data, null);
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _bad($error)
    // -------------------------------------------------------------------------
    private function _bad($error)
    {
        $this->_json_exit(400, false, null, $error);
    }

    // -------------------------------------------------------------------------
    // GET /api/day_ceremony/probe
    // Returns flag status and today count.
    // -------------------------------------------------------------------------
    public function probe()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET method required.');
        }

        $result = $this->day_ceremony->probe();
        $this->_ok($result);
    }

    // -------------------------------------------------------------------------
    // POST /api/day_ceremony/start
    // Body: uid, lat, lng, gps_accuracy_m
    // Starts the day ceremony with GPS check-in.
    // -------------------------------------------------------------------------
    public function start()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_bad('POST method required.');
        }

        $uid          = $this->input->post('uid');
        $lat          = $this->input->post('lat');
        $lng          = $this->input->post('lng');
        $gps_accuracy = $this->input->post('gps_accuracy_m');

        if (empty($uid)) {
            $this->_bad('uid is required.');
        }
        if ($lat === null || $lat === '') {
            $this->_bad('lat is required.');
        }
        if ($lng === null || $lng === '') {
            $this->_bad('lng is required.');
        }

        // === MIGRATION 087.1 - day-start discipline gate ===
        // parity_functional_fix_20260611: production ALWAYS starts the day - it
        // does not require a selfie/EXIF/freshness/geofence. The selfie + EXIF +
        // freshness + anchor-radius checks below are now ADVISORY by default and
        // only hard-block when the config flag 'day_start_discipline_enforce' in
        // day_ceremony_config_v2 is set to '1'. Absent row or '0'/empty => OFF
        // (production parity: day always starts). selfie_url/exif are still
        // accepted and stored when provided, but never required when enforce is OFF.
        $this->load->database();
        $selfie_url   = $this->input->post('selfie_url');
        $photo_exif   = $this->input->post('photo_exif_taken_at');

        // Null-safe read of the enforcement flag (default OFF).
        $enf_row = $this->db->select('config_value')->from('day_ceremony_config_v2')
                        ->where('config_key','day_start_discipline_enforce')->get()->row();
        $enforce = ($enf_row && isset($enf_row->config_value) && (string)$enf_row->config_value === '1');

        // PER-ROLE STRICT GATE (2026-06-12): parity downgraded the gate globally,
        // but strict_gate_role_ids in day_ceremony_config_v2 names the roles that
        // MUST stay gated (BD=3, CM=13, etc.). Enforce for those roles regardless
        // of the global flag; all other roles keep production-parity behaviour.
        try {
            $sg_row = $this->db->select('config_value')->from('day_ceremony_config_v2')
                            ->where('config_key','strict_gate_role_ids')->get()->row();
            if ($sg_row && isset($sg_row->config_value) && trim((string)$sg_row->config_value) !== '') {
                $strict_ids = array_filter(array_map('intval', explode(',', (string)$sg_row->config_value)));
                $u_row = $this->db->select('type_id')->from('user')->where('uid', (int)$uid)->get()->row();
                $u_type = ($u_row && isset($u_row->type_id)) ? (int)$u_row->type_id : 0;
                if ($u_type && in_array($u_type, $strict_ids, true)) { $enforce = true; }
            }
        } catch (\Throwable $t) { /* fail-open to global flag; never block on a config read error */ }

        if ($enforce) {
            if (empty($selfie_url))  { $this->_bad('selfie_url is required (capture a selfie).'); }
            if (empty($photo_exif))  { $this->_bad('photo_exif_taken_at is required.'); }
            // Photo freshness - must be within N minutes per day_ceremony_config_v2.
            // Null-safe read of freshness minutes (fallback 5) - fixes latent null-deref.
            $fresh_row = $this->db->select('config_value')->from('day_ceremony_config_v2')
                            ->where('config_key','day_start_photo_freshness_minutes')->get()->row();
            $fresh_minutes = ($fresh_row && isset($fresh_row->config_value)) ? (int)$fresh_row->config_value : 5;
            if (!$fresh_minutes) { $fresh_minutes = 5; }
            $exif_ts = strtotime($photo_exif); $now_ts = time();
            if ($exif_ts === false || ($now_ts - $exif_ts) > ($fresh_minutes * 60) || ($exif_ts - $now_ts) > 120) {
                $this->_bad('Selfie not fresh - must be within '.$fresh_minutes.' minutes.');
            }
            // Anchor radius check - haversine vs home/office anchor.
            $anchor = $this->db->select('lat,lng,radius_km')->from('day_start_home_anchor_v2')->where('user_id',$uid)->where('active',1)->get()->row();
            if ($anchor) {
                $R = 6371.0; $dLat = deg2rad($lat - $anchor->lat); $dLng = deg2rad($lng - $anchor->lng);
                $a = sin($dLat/2)*sin($dLat/2) + cos(deg2rad($anchor->lat))*cos(deg2rad($lat))*sin($dLng/2)*sin($dLng/2);
                $dist_km = 2 * $R * asin(sqrt($a));
                if ($dist_km > $anchor->radius_km) {
                    $this->_bad('Not within home/office anchor radius ('.round($dist_km,2).' km > '.$anchor->radius_km.' km).');
                }
            }
        }
        // === END MIGRATION 087.1 enforcement ===

        $result = $this->day_ceremony->start_day($uid, $lat, $lng, $gps_accuracy);

        if (!$result['success']) {
            $this->_bad($result['error']);
        }

        // === PRODUCTION MIRROR (additive, staging-only) =====================
        // The advanced day_ceremony record above is preserved untouched.
        // We ALSO write the production user_day row so a day started in the
        // mobile app is visible everywhere production looks (dashboards,
        // reports, manager day-start reviews). Never blocks the enhanced flow.
        try {
            $this->_mirror_user_day_start($uid, $lat, $lng, $selfie_url);
        } catch (\Throwable $t) {
            error_log('[DayCeremonyController] user_day mirror failed: ' . $t->getMessage());
        }
        // === END PRODUCTION MIRROR =========================================

        $this->_ok($result);
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _mirror_user_day_start($uid, $lat, $lng, $selfie_url)
    // Mirrors the mobile day-start into the production user_day table so the
    // started day shows up in every production screen/report. ADDITIVE ONLY.
    //   wffo: numeric 1=Office, 2=Field, 3=Field+Office (matches production).
    //   usimg: a real path under uploads/day/ when a selfie file is uploaded;
    //          otherwise we store whatever selfie reference was provided.
    // Idempotent: skips if a user_day row already exists for this uid today.
    // -------------------------------------------------------------------------
    private function _mirror_user_day_start($uid, $lat, $lng, $selfie_url)
    {
        $this->load->database();
        $uid = (int) $uid;
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');

        // Idempotency: do not create a second start for the same day.
        $existing = $this->db->query(
            "SELECT id FROM user_day WHERE user_id = ? AND CAST(sdatet AS DATE) = ? LIMIT 1",
            [$uid, $today]
        )->row();
        if ($existing) { return; }

        // Map wffo string -> production numeric. Default 2 (Field) for field reps.
        $wffo_raw = strtolower((string) $this->input->post('wffo'));
        $wffo_map = [
            'office'          => 1, '1' => 1,
            'field'           => 2, '2' => 2,
            'field_+_office'  => 3, 'field+office' => 3, 'field_office' => 3, '3' => 3,
        ];
        $wffo = isset($wffo_map[$wffo_raw]) ? $wffo_map[$wffo_raw] : 2;

        // Persist the selfie into uploads/day/ when an actual file is uploaded.
        $usimg = $this->_save_day_selfie();
        if ($usimg === null) {
            // No multipart file: store the provided reference (kept ASCII-safe).
            $usimg = is_string($selfie_url) ? substr($selfie_url, 0, 255) : '';
        }

        $this->db->query(
            "INSERT INTO user_day (sdatet, user_id, ustart, usimg, slatitude, slongitude, wffo)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$now, $uid, $now, $usimg, (string) $lat, (string) $lng, $wffo]
        );
        $new_id = $this->db->insert_id();
        if ($new_id) {
            $this->db->query(
                "INSERT INTO notify (uid, type, sms) VALUES (?, '1', ?)",
                [$uid, 'You Are Started Your Day at ' . $now]
            );
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _save_day_selfie()
    // Saves an uploaded selfie (multipart field 'selfie' or 'filname') into
    // uploads/day/ and returns its relative path, or null if no file present.
    // -------------------------------------------------------------------------
    private function _save_day_selfie()
    {
        $field = null;
        if (!empty($_FILES['selfie']['name']))  { $field = 'selfie'; }
        elseif (!empty($_FILES['filname']['name'])) { $field = 'filname'; }
        if ($field === null) { return null; }

        $allowed = ['image/jpeg','image/png','image/jpg','image/gif','image/webp'];
        $type = $_FILES[$field]['type'] ?? '';
        if (!in_array($type, $allowed, true)) { return null; }

        $dir = FCPATH . 'uploads/day/';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
        $ext = preg_replace('/[^A-Za-z0-9]/', '', (string) $ext);
        if ($ext === '') { $ext = 'jpg'; }
        $fname = 'daystart_' . (int) $this->input->post('uid') . '_' . time() . '.' . $ext;
        $dest  = $dir . $fname;

        if (@move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
            return 'uploads/day/' . $fname;
        }
        return null;
    }

    // -------------------------------------------------------------------------
    // POST /api/day_ceremony/close
    // Body: uid, tasks_planned, tasks_done, kpi_meetings_completed,
    //   kpi_moms_written, kpi_leads_progressed, blockers_text,
    //   achievements_text, lat, lng
    // Closes the day ceremony.
    // -------------------------------------------------------------------------
    public function close()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_bad('POST method required.');
        }

        $uid = $this->input->post('uid');
        if (empty($uid)) {
            $this->_bad('uid is required.');
        }

        // KPI fields are OPTIONAL - default to 0 if not provided.
        // Only selfie_url + lat + lng are mandatory for a minimum viable day close.

        // === MIGRATION 087.1 close enforcement ===
        $close_selfie = $this->input->post('selfie_url');
        if (empty($close_selfie)) { $this->_bad('selfie_url is required for Day Close.'); }
        $close_lat = $this->input->post('lat'); $close_lng = $this->input->post('lng');
        if ($close_lat === null || $close_lat === '') { $this->_bad('lat is required for Day Close.'); }
        if ($close_lng === null || $close_lng === '') { $this->_bad('lng is required for Day Close.'); }
        // === END MIGRATION 087.1 close enforcement ===

        $payload = [
            'tasks_planned'          => $this->input->post('tasks_planned'),
            'tasks_done'             => $this->input->post('tasks_done'),
            'kpi_meetings_completed' => $this->input->post('kpi_meetings_completed'),
            'kpi_moms_written'       => $this->input->post('kpi_moms_written'),
            'kpi_leads_progressed'   => $this->input->post('kpi_leads_progressed'),
            'blockers_text'          => $this->input->post('blockers_text'),
            'achievements_text'      => $this->input->post('achievements_text'),
            'lat'                    => $this->input->post('lat'),
            'lng'                    => $this->input->post('lng'),
        ];

        $result = $this->day_ceremony->close_day($uid, $payload);

        if (!$result['success']) {
            $this->_bad($result['error']);
        }

        $this->_ok($result);
    }

    // -------------------------------------------------------------------------
    // GET /api/day_ceremony/today_status?uid=...
    // Returns the current day_ceremony row for the given uid and today.
    // -------------------------------------------------------------------------
    public function today_status()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET method required.');
        }

        $uid = $this->input->get('uid');
        if (empty($uid)) {
            $this->_bad('uid query parameter is required.');
        }

        $row = $this->day_ceremony->get_today_status($uid);

        // row may be null (no ceremony yet today, or feature off for user).
        $this->_ok(['ceremony' => $row]);
    }

    // -------------------------------------------------------------------------
    // GET /api/day_ceremony/rollup?date=YYYY-MM-DD
    // Returns compliance rollup for the given date.
    // Used by the 6 AM IST compliance brief cron.
    // -------------------------------------------------------------------------
    public function rollup()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET method required.');
        }

        $date = $this->input->get('date');
        // Default to today if not supplied.
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->_bad('date must be in YYYY-MM-DD format.');
        }

        $result = $this->day_ceremony->get_rollup($date);
        $this->_ok($result);
    }

    // -------------------------------------------------------------------------
    // POST /api/day_ceremony/leave_hr_email
    // Body: uid, date (YYYY-MM-DD)
    // When BD has approved leave for the date, sends HR email.
    // -------------------------------------------------------------------------
    public function leave_hr_email()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_bad('POST method required.');
        }

        $uid  = $this->input->post('uid');
        $date = $this->input->post('date');

        if (empty($uid)) {
            $this->_bad('uid is required.');
        }
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->_bad('date must be in YYYY-MM-DD format.');
        }

        $result = $this->day_ceremony->mark_hr_emailed_for_leave($uid, $date);

        if (!$result['success']) {
            $this->_bad($result['error']);
        }

        $this->_ok($result);
    }
}

// CI3 routing compatibility: file=DayCeremony.php, class=DayCeremonyController
// Guard ensures alias is only created once even if file is included multiple times.
if (!class_exists('Dayceremony', false)) { class_alias('DayCeremonyController', 'Dayceremony'); }
