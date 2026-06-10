<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * DayMgmtExtra_api
 *
 * Day Management parity endpoints (Agent H, 2026-06-06).
 *
 * Routes (routes_parity.php):
 *   GET  /api/day_management/yesterday_close_status   -> yesterday_close_status()
 *   POST /api/day_management/yesterday_close_request  -> yesterday_close_request()
 *   POST /api/day_management/yesterday_day_close      -> yesterday_day_close()
 *   POST /api/day_management/change_start_request     -> change_start_request()
 *   GET  /api/day_management/probe                    -> probe()
 *
 * File lives in BOTH:
 *   application/controllers/DayMgmtExtra_api.php
 *   application/controllers/api/DayMgmtExtra_api.php
 *
 * Parity source: DayManagement-1.php lines 134-380 (yesterday block),
 *               Menu/dayscRequest, Menu/YesterdayDayClose,
 *               Management/SendRequestForDayStartChnage
 *
 * DB tables used:
 *   user_day              - uclose, ustart, user_id
 *   close_your_day_request - yesterday close requests
 *   change_user_day_request - day start change requests
 *   task_plan_for_today   - pending tasks count
 */
class DayMgmtExtra_api extends CI_Controller {

    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('BearerAuth');
        header('Content-Type: application/json');
    }

    // -------------------------------------------------------------------------
    // Auth
    // -------------------------------------------------------------------------
    private function _auth() {
        $auth = $this->bearerauth->resolve();
        return $auth['ok'];
    }

    private function _ok($data = [])  { echo json_encode(array_merge(['ok' => true], $data)); exit; }
    private function _err($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'error' => $msg, 'reason' => 'no_rows']);
        exit;
    }

    private function _body() {
        $raw  = file_get_contents('php://input');
        $body = @json_decode($raw, true);
        if (!$body) {
            parse_str($raw, $body);
        }
        if (!$body) $body = [];
        // Also check CI post
        $ci_fields = ['uid','req_id','would_you_want','requestForTodaysTaskPlan',
                      'autotasktimeisset','startautotasktime','endautotasktime',
                      'start_tttpft','end_tttpft','selfie_url','lat','lng',
                      'photo_exif_taken_at','user_want_start','message'];
        foreach ($ci_fields as $f) {
            $v = $this->input->post($f);
            if ($v !== null && $v !== false && !isset($body[$f])) {
                $body[$f] = $v;
            }
        }
        return $body;
    }

    // -------------------------------------------------------------------------
    // GET /api/day_management/yesterday_close_status?uid=X
    //
    // Returns whether yesterday's user_day row is open (uclose IS NULL/empty)
    // plus any existing close_your_day_request row.
    //
    // Mirrors DayManagement-1.php:134-154 (sizeof($yestdata)==1 check) and
    // Menu_model/GetDayCloseRequest.
    //
    // Response:
    //   {ok, has_yesterday_open:bool, yesterday_row:{...}, pending_task_count:N,
    //    close_request:{...}|null}
    // -------------------------------------------------------------------------
    public function yesterday_close_status() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }

        $uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
        if ($uid <= 0) { $this->_err('uid is required'); }

        // Yesterday's date (IST)
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // user_day row for yesterday where uclose IS NULL or empty
        // user_day.sdatet is a timestamp; user_day.id is the primary key
        $yest_row = $this->db->query(
            "SELECT id, user_id, sdatet, ustart, uclose, wffo, planner_initiate_time
             FROM user_day
             WHERE user_id = '$uid'
               AND DATE(sdatet) = '$yesterday'
             ORDER BY id DESC LIMIT 1"
        )->row();

        $has_yesterday_open = false;
        if ($yest_row && (empty($yest_row->uclose) || $yest_row->uclose === '0000-00-00 00:00:00')) {
            $has_yesterday_open = true;
        }

        // Pending task count from task_plan_for_today on yesterday
        $pending_row = $this->db->query(
            "SELECT taskcnt FROM task_plan_for_today
             WHERE user_id = '$uid'
               AND DATE(created_at) = '$yesterday'
             ORDER BY id DESC LIMIT 1"
        )->row();
        $pending_count = $pending_row ? (int)$pending_row->taskcnt : 0;

        // Existing close_your_day_request for this user (most recent)
        $close_req = null;
        if ($has_yesterday_open && $yest_row) {
            $req_id    = (int)$yest_row->id;
            $close_req_row = $this->db->query(
                "SELECT id, user_id, req_id, req_date, why_did_you,
                        req_remarks, approved_status, approved_by,
                        approved_remarks, approved_date, created_at
                 FROM close_your_day_request
                 WHERE user_id = '$uid'
                 ORDER BY id DESC LIMIT 1"
            )->row();
            if ($close_req_row) {
                $close_req = (array)$close_req_row;
            }
        }

        if (!$has_yesterday_open) {
            $this->_ok([
                'has_yesterday_open'  => false,
                'yesterday_row'       => null,
                'pending_task_count'  => 0,
                'close_request'       => null,
                'message'             => 'No open yesterday day found',
            ]);
        }

        $this->_ok([
            'has_yesterday_open'  => $has_yesterday_open,
            'yesterday_row'       => $yest_row ? [
                'id'                   => (int)$yest_row->id,
                'user_id'              => (int)$yest_row->user_id,
                'sdatet'               => $yest_row->sdatet,
                'ustart'               => $yest_row->ustart,
                'uclose'               => $yest_row->uclose,
                'wffo'                 => $yest_row->wffo,
                'planner_initiate_time'=> $yest_row->planner_initiate_time,
            ] : null,
            'pending_task_count'  => $pending_count,
            'close_request'       => $close_req,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/day_management/yesterday_close_request
    //
    // Inserts a row into close_your_day_request.
    // Mirrors Menu/dayscRequest (DayManagement-1.php).
    //
    // Body: {uid, req_id, would_you_want, requestForTodaysTaskPlan,
    //        autotasktimeisset?, startautotasktime?, endautotasktime?,
    //        start_tttpft?, end_tttpft?, taskcnt?}
    //
    // close_your_day_request columns:
    //   id(auto), user_id, req_id, req_date(auto), why_did_you,
    //   req_remarks, approved_status='', approved_by=0, approved_remarks='',
    //   approved_date=null, autotasktimeisset, startautotasktime,
    //   endautotasktime, start_tttpft, end_tttpft
    // -------------------------------------------------------------------------
    public function yesterday_close_request() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->_err('POST required'); }

        $body = $this->_body();

        $uid       = isset($body['uid'])       ? (int)$body['uid']         : 0;
        $req_id    = isset($body['req_id'])     ? (int)$body['req_id']      : 0;
        $why       = isset($body['would_you_want']) ? trim($body['would_you_want']) : '';
        $remarks   = isset($body['requestForTodaysTaskPlan']) ? trim($body['requestForTodaysTaskPlan']) : '';
        $taskcnt   = isset($body['taskcnt'])    ? (int)$body['taskcnt']     : 0;

        // Optional autotask time fields
        $atis      = isset($body['autotasktimeisset'])   ? (int)$body['autotasktimeisset']              : 0;
        $sat       = isset($body['startautotasktime'])   ? trim($body['startautotasktime'])             : null;
        $eat       = isset($body['endautotasktime'])     ? trim($body['endautotasktime'])               : null;
        $stp       = isset($body['start_tttpft'])        ? trim($body['start_tttpft'])                  : null;
        $etp       = isset($body['end_tttpft'])          ? trim($body['end_tttpft'])                    : null;

        if ($uid <= 0 || !$why) {
            $this->_err('uid and would_you_want are required');
        }

        $w_esc   = $this->db->escape_str($why);
        $r_esc   = $this->db->escape_str($remarks);
        $sat_sql = $sat ? "'" . $this->db->escape_str($sat) . "'" : 'NULL';
        $eat_sql = $eat ? "'" . $this->db->escape_str($eat) . "'" : 'NULL';
        $stp_sql = $stp ? "'" . $this->db->escape_str($stp) . "'" : 'NULL';
        $etp_sql = $etp ? "'" . $this->db->escape_str($etp) . "'" : 'NULL';
        $now     = date('Y-m-d H:i:s');

        // close_your_day_request.id is NOT auto_increment - compute next id
        $max_row = $this->db->query("SELECT COALESCE(MAX(id),0)+1 AS next_id FROM close_your_day_request")->row();
        $new_id  = $max_row ? (int)$max_row->next_id : 1;

        $this->db->query(
            "INSERT INTO close_your_day_request
               (id, user_id, req_id, req_date, why_did_you, req_remarks,
                approved_status, approved_by, approved_remarks, approved_date,
                autotasktimeisset, startautotasktime, endautotasktime,
                start_tttpft, end_tttpft)
             VALUES
               ('$new_id', '$uid', '$req_id', '$now', '$w_esc', '$r_esc',
                '', 0, '', NULL,
                '$atis', $sat_sql, $eat_sql, $stp_sql, $etp_sql)"
        );
        $affected = $this->db->affected_rows();
        if (!$affected) { $new_id = 0; }

        if (!$new_id) {
            $this->_err('database insert failed', 500);
        }

        $this->_ok([
            'request_id' => $new_id,
            'message'    => 'Close request submitted. Awaiting CM approval.',
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/day_management/yesterday_day_close
    //
    // Performs the actual close of yesterday's user_day row (sets uclose).
    // Mirrors Menu/YesterdayDayClose.
    //
    // Body: {uid, req_id, selfie_url, lat, lng, photo_exif_taken_at?}
    //
    // user_day columns used: uclose, ucimg, clatitude, clongitude
    // -------------------------------------------------------------------------
    public function yesterday_day_close() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->_err('POST required'); }

        $body = $this->_body();

        $uid        = isset($body['uid'])       ? (int)$body['uid']          : 0;
        $req_id     = isset($body['req_id'])     ? (int)$body['req_id']       : 0;
        $selfie     = isset($body['selfie_url']) ? trim($body['selfie_url'])  : '';
        $lat        = isset($body['lat'])        ? trim($body['lat'])         : '';
        $lng        = isset($body['lng'])        ? trim($body['lng'])         : '';

        if ($uid <= 0) { $this->_err('uid is required'); }

        // Verify the close_your_day_request is approved
        if ($req_id > 0) {
            $req_row = $this->db->query(
                "SELECT id, approved_status
                 FROM close_your_day_request
                 WHERE id = '$req_id' AND user_id = '$uid'
                 LIMIT 1"
            )->row();
            if ($req_row && $req_row->approved_status !== 'Approved') {
                $this->_err('Close request not yet approved by CM.', 403);
            }
        }

        $yesterday  = date('Y-m-d', strtotime('-1 day'));
        $now        = date('Y-m-d H:i:s');
        $lat_esc    = $this->db->escape_str($lat);
        $lng_esc    = $this->db->escape_str($lng);
        $selfie_esc = $this->db->escape_str($selfie);

        // Update user_day.uclose for yesterday
        $this->db->query(
            "UPDATE user_day
             SET uclose     = '$now',
                 ucimg      = '$selfie_esc',
                 clatitude  = '$lat_esc',
                 clongitude = '$lng_esc'
             WHERE user_id = '$uid'
               AND DATE(sdatet) = '$yesterday'
               AND (uclose IS NULL OR uclose = '' OR uclose = '0000-00-00 00:00:00')
             ORDER BY id DESC
             LIMIT 1"
        );
        $affected = $this->db->affected_rows();

        if ($affected === 0) {
            // Already closed or no row - still return ok so client can proceed
            $this->_ok([
                'message'  => 'Yesterday day already closed or no row found',
                'affected' => 0,
            ]);
        }

        $this->_ok([
            'message'  => 'Yesterday day closed successfully.',
            'affected' => $affected,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/day_management/change_start_request
    //
    // Inserts a row into change_user_day_request.
    // Mirrors Management/SendRequestForDayStartChnage.
    //
    // change_user_day_request columns:
    //   id(auto), user_id, user_want_start, date, message,
    //   apr_by=0, apr_status=0, amessage='', created_at, updated_at
    //
    // Body: {uid, user_want_start, message}
    //   user_want_start: integer (wffo option id that user wanted to start with)
    // -------------------------------------------------------------------------
    public function change_start_request() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->_err('POST required'); }

        $body = $this->_body();

        $uid         = isset($body['uid'])              ? (int)$body['uid']               : 0;
        $want_start  = isset($body['user_want_start'])  ? (int)$body['user_want_start']   : 0;
        $message     = isset($body['message'])          ? trim($body['message'])           : '';

        if ($uid <= 0 || $want_start <= 0) {
            $this->_err('uid and user_want_start are required');
        }

        $msg_esc = $this->db->escape_str($message);
        $now     = date('Y-m-d H:i:s');

        // change_user_day_request.id is NOT auto_increment - compute next id
        $max_row = $this->db->query("SELECT COALESCE(MAX(id),0)+1 AS next_id FROM change_user_day_request")->row();
        $new_id  = $max_row ? (int)$max_row->next_id : 1;

        $this->db->query(
            "INSERT INTO change_user_day_request
               (id, user_id, user_want_start, date, message, apr_by, apr_status, amessage)
             VALUES
               ('$new_id', '$uid', '$want_start', '$now', '$msg_esc', 0, 0, '')"
        );
        $affected = $this->db->affected_rows();
        if (!$affected) { $new_id = 0; }

        if (!$new_id) {
            $this->_err('database insert failed', 500);
        }

        $this->_ok([
            'request_id' => $new_id,
            'message'    => 'Day start change request submitted.',
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/day_management/probe
    // -------------------------------------------------------------------------
    public function probe() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }
        $this->_ok(['msg' => 'DayMgmtExtra_api OK', 'ts' => date('c')]);
    }
}
