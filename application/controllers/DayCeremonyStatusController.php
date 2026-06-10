<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DayCeremonyStatusController.php
 * Migration 075 - Agent G Day Discipline Gate
 *
 * Provides:
 *   GET /api/day_ceremony/status?uid=<uid>
 *   GET /api/day_ceremony/probe
 *
 * Auth: Bearer STEM_DIGEST_TOKEN (same pattern as FunnelExportController).
 * Reya JWT (sha1 daily token) also accepted.
 *
 * Deploy to: /home/selfstaging/public_html/application/controllers/DayCeremonyStatusController.php
 */

class DayCeremonyStatusController extends CI_Controller
{
    private $_authed_uid = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        header('Content-Type: application/json; charset=utf-8');
    }

    // -----------------------------------------------------------------------
    // Auth: Bearer STEM_DIGEST_TOKEN or per-user daily JWT (sha1)
    // -----------------------------------------------------------------------
    private function _bearer_ok()
    {
        if (function_exists('authunify_ok') && authunify_ok()) {
            if (function_exists('authunify_uid')) { $u=(int)authunify_uid(); if ($u>0) $this->_authed_uid=$u; }
            return true;
        } // rimlyproof_authunify_20260609
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token  = trim(substr($hdr, 7));
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($secret, $token)) return true;

        // Per-user daily JWT: sha1(secret|uid|YYYY-MM-DD)
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')));
        $candidates = array();
        foreach (array('uid','bd_uid','cm_uid','user_id') as $k) {
            if (isset($_GET[$k])  && (int)$_GET[$k]  > 0) $candidates[(int)$_GET[$k]]  = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) {
                    $this->_authed_uid = $uid;
                    return true;
                }
            }
        }
        return false;
    }

    private function _json($code, $payload)
    {
        http_response_code($code);
        echo json_encode($payload);
        exit;
    }

    // -----------------------------------------------------------------------
    // GET /api/day_ceremony/probe
    // -----------------------------------------------------------------------
    public function probe()
    {
        $this->_json(200, array(
            'ok'         => true,
            'migration'  => '075',
            'feature'    => 'day_ceremony_status',
            'deployed_at'=> date('Y-m-d'),
            'endpoints'  => array(
                '/api/day_ceremony/status',
                '/api/day_ceremony/probe',
            ),
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/day_ceremony/status?uid=<uid>
    //
    // Returns:
    //   { ok, uid, today, day_started, day_started_at, day_ended, day_ended_at,
    //     plan_exists_for_tomorrow, pending_tasks_count }
    // -----------------------------------------------------------------------
    public function status()
    {
        if (!$this->_bearer_ok()) {
            $this->_json(401, array('ok' => false, 'error' => 'Unauthorized'));
        }

        $uid = (int)(isset($_GET['uid']) ? $_GET['uid'] : $this->_authed_uid);
        if ($uid <= 0) {
            $this->_json(400, array('ok' => false, 'error' => 'uid param required'));
        }

        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');

        // 1) Fetch from day_ceremony (primary table used by DayCeremony_model)
        try {
            $row = $this->db->query(
                "SELECT id, status, day_start_at, day_close_at
                   FROM day_ceremony
                  WHERE uid = ? AND ceremony_date = ?
                  LIMIT 1",
                array($uid, $today)
            )->row_array();
        } catch (Exception $e) {
            $row = null;
        }

        // 2) Fallback: day_ceremony_log (used by Mobile_stub_api start_simple)
        if (!$row) {
            try {
                $log = $this->db->query(
                    "SELECT id, start_ts, end_ts
                       FROM day_ceremony_log
                      WHERE uid = ? AND ceremony_date = ?
                      LIMIT 1",
                    array($uid, $today)
                )->row_array();
                if ($log) {
                    $row = array(
                        'status'       => $log['end_ts'] ? 'closed' : 'day_started',
                        'day_start_at' => $log['start_ts'],
                        'day_close_at' => $log['end_ts'],
                    );
                }
            } catch (Exception $e) {
                $log = null;
            }
        }

        $day_started    = $row && in_array($row['status'], array('day_started', 'closed'));
        $day_started_at = $row ? $row['day_start_at'] : null;
        $day_ended      = $row && $row['status'] === 'closed';
        $day_ended_at   = $row ? $row['day_close_at'] : null;

        // 3) plan_exists_for_tomorrow: check daily_planner for tomorrow
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        try {
            $plan_cnt = (int)$this->db->query(
                "SELECT COUNT(*) AS cnt FROM daily_planner
                  WHERE userID = ? AND record_date = ? LIMIT 1",
                array($uid, $tomorrow)
            )->row()->cnt;
            $plan_exists_for_tomorrow = $plan_cnt > 0;
        } catch (Exception $e) {
            $plan_exists_for_tomorrow = null; // unknown
        }

        // 4) pending_tasks_count: tasks today with no actontaken
        try {
            $pending = (int)$this->db->query(
                "SELECT COUNT(*) AS cnt FROM tblcallevents
                  WHERE user_id = ?
                    AND DATE(appointmentdatetime) = ?
                    AND (actontaken IS NULL OR actontaken = '' OR actontaken = 'no')",
                array($uid, $today)
            )->row()->cnt;
        } catch (Exception $e) {
            $pending = null;
        }

        $this->_json(200, array(
            'ok'                       => true,
            'uid'                      => $uid,
            'today'                    => $today,
            'day_started'              => $day_started,
            'day_started_at'           => $day_started_at,
            'day_ended'                => $day_ended,
            'day_ended_at'             => $day_ended_at,
            'plan_exists_for_tomorrow' => $plan_exists_for_tomorrow,
            'pending_tasks_count'      => $pending,
            'fetched_at'               => $now,
        ));
    }
}
