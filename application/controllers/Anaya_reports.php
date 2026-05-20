<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Anaya_reports controller
 *
 * Thin JSON wrapper around the Anaya planning agent. Used by the mobile app
 * to fetch the BD "day pack" - the day-start payload that combines plan,
 * leads, MoM blockers and pipeline snapshot in one call.
 *
 * Endpoints:
 *   GET /Anaya_reports/api_day_pack?date=YYYY-MM-DD
 *
 * Auth: session cookie (PHPSESSID) set by /Menu/api_login, falls back to
 * Bearer STEM_DIGEST_TOKEN for cron and admin callers.
 *
 * Created on feature/json-mobile-endpoints branch, 2026-05-20.
 */
class Anaya_reports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    // ------------------------------------------------------------------
    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
        exit;
    }

    // ------------------------------------------------------------------
    // Returns the authenticated uid, either from session cookie or from
    // STEM_DIGEST_TOKEN (admin/cron). Returns null when unauthenticated.
    // ------------------------------------------------------------------
    private function _auth_uid()
    {
        // 1. Session cookie path
        $user = $this->session->userdata('user');
        if (!empty($user['user_id'])) {
            return (int)$user['user_id'];
        }

        // 2. Bearer token path
        $hdr = $this->input->get_request_header('Authorization');
        if ($hdr && strpos($hdr, 'Bearer ') === 0) {
            $token    = trim(substr($hdr, 7));
            $expected = getenv('STEM_DIGEST_TOKEN');
            if ($expected && hash_equals($expected, $token)) {
                // Cron callers may pass ?uid= to scope the day pack
                $uid = (int)$this->input->get('uid');
                return $uid ?: null;
            }
        }

        return null;
    }

    // ==================================================================
    // GET /Anaya_reports/api_day_pack?date=YYYY-MM-DD
    //
    // Returns the BD day-start pack for the given date (defaults to today).
    // Aggregates plan, lead snapshot, MoM blockers and pipeline counts.
    //
    // Response:
    //   { ok, uid, date, plan, leads, mom_blockers, pipeline,
    //     stuck, generated_at }
    // ==================================================================
    public function api_day_pack()
    {
        if (strtolower($this->input->method()) !== 'get') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $uid = $this->_auth_uid();
        if (!$uid) {
            $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        $date = $this->input->get('date');
        if (!$date) {
            $date = date('Y-m-d');
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->_json(['ok' => false, 'error' => 'invalid_date',
                          'message' => 'date must be YYYY-MM-DD'], 400);
        }

        // Delegate to the AIAgents/Anaya_agent::bd_daily_pack helper if present.
        $agent_path = APPPATH . 'models/AIAgents/Anaya_agent.php';
        if (file_exists($agent_path)) {
            require_once $agent_path;
            $agent = new Anaya_agent();
            $pack  = $agent->bd_daily_pack($uid, $date);
            $pack['ok']           = true;
            $pack['uid']          = $uid;
            $pack['date']         = $date;
            $pack['generated_at'] = date('c');
            $this->_json($pack);
        }

        // Fallback when the agent class is not loaded yet on this server.
        // Returns the same shape with empty payloads so the mobile app does
        // not crash during phased rollouts.
        $this->_json([
            'ok'           => true,
            'uid'          => $uid,
            'date'         => $date,
            'plan'         => [],
            'leads'        => [],
            'mom_blockers' => [],
            'pipeline'     => ['open' => 0, 'positive' => 0, 'won' => 0, 'lost' => 0],
            'stuck'        => [],
            'generated_at' => date('c'),
            'note'         => 'agent_unavailable',
        ]);
    }
}
