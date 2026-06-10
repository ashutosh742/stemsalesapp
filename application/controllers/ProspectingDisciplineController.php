<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProspectingDisciplineController
 * Migration 029 - Prospecting Discipline Audit
 *
 * Routes (add to application/config/routes.php):
 *   GET  /api/prospecting_discipline/probe
 *   POST /api/prospecting_discipline/refresh_daily?date=YYYY-MM-DD
 *   GET  /api/prospecting_discipline/yesterday
 *   GET  /api/prospecting_discipline/event_audit?bd_uid=&date=
 *   GET  /api/prospecting_discipline/weekly?from=&to=
 *   GET  /api/prospecting_discipline/spoof_log?days=7
 *
 * Auth: Bearer STEM_DIGEST_TOKEN. Same pattern as migrations 022-028.
 *
 * Place at: application/controllers/ProspectingDisciplineController.php
 */
class ProspectingDisciplineController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/Stem_prospecting_discipline_scorer', 'scorer');
        $this->load->helper('url');
        $this->_rp_guard();
    }

    // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This controller
    // returned live business data with NO token check (fail-open). Allow only
    // liveness/probe methods; require a valid digest OR per-user login token for
    // every data method via the shared authunify_ok(). Additive: valid callers
    // unchanged; only missing/garbage tokens are now rejected.
    private $_rp_public = array('probe', 'status');
    private function _rp_guard() {
        $m = $this->router->fetch_method();
        if (in_array($m, $this->_rp_public, true)) { return; }
        if (substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }


    // ----------------------------------------------------------------- probe

    public function probe()
    {
        $this->_check_auth(true); // probe allowed without strict token
        $this->_json(['ok' => true, 'mig' => '029', 'service' => 'prospecting_discipline']);
    }

    // ------------------------------------------------------------- refresh_daily

    public function refresh_daily()
    {
        $this->_check_auth();
        $date = $this->input->get('date');
        if (!$date) $date = date('Y-m-d', strtotime('yesterday'));
        $result = $this->scorer->score_for_date($date);
        $this->_json($result);
    }

    // ----------------------------------------------------------- yesterday view

    public function yesterday()
    {
        $this->_check_auth();
        $rows = $this->scorer->get_yesterday();
        $this->_json(['date' => date('Y-m-d', strtotime('yesterday')), 'rows' => $rows, 'count' => count($rows)]);
    }

    // ----------------------------------------------------------- event audit

    public function event_audit()
    {
        $this->_check_auth();
        $bd_uid = (int)$this->input->get('bd_uid');
        $date = $this->input->get('date');
        if (!$bd_uid || !$date) {
            $this->_json(['error' => 'bd_uid and date required'], 400);
            return;
        }
        $rows = $this->scorer->get_event_audit($bd_uid, $date);
        $this->_json(['bd_uid' => $bd_uid, 'date' => $date, 'rows' => $rows, 'count' => count($rows)]);
    }

    // -------------------------------------------------------------- weekly

    public function weekly()
    {
        $this->_check_auth();
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        if (!$from) $from = date('Y-m-d', strtotime('monday this week'));
        if (!$to)   $to   = date('Y-m-d');
        $rows = $this->scorer->get_weekly($from, $to);
        $this->_json(['from' => $from, 'to' => $to, 'rows' => $rows, 'count' => count($rows)]);
    }

    // ----------------------------------------------------------- spoof_log

    public function spoof_log()
    {
        $this->_check_auth();
        $days = max(1, min(30, (int)$this->input->get('days')));
        if (!$days) $days = 7;
        $rows = $this->scorer->get_spoof_log($days);
        $this->_json(['days' => $days, 'rows' => $rows, 'count' => count($rows)]);
    }

    // ----------------------------------------------------------- helpers

    private function _check_auth($allow_probe = false)
    {
        $hdr = $this->input->get_request_header('Authorization', true);
        if (!$hdr) {
            if ($allow_probe) return; // probe is open
            $this->_json(['error' => 'missing bearer'], 401);
            exit;
        }
        // Compare against STEM_DIGEST_TOKEN in config/config.php if defined.
        // If not defined we accept any non-empty bearer (staging-safe default).
        if (defined('STEM_DIGEST_TOKEN') && STEM_DIGEST_TOKEN) {
            $expected = 'Bearer ' . STEM_DIGEST_TOKEN;
            if (trim($hdr) !== $expected) {
                $this->_json(['error' => 'invalid bearer'], 401);
                exit;
            }
        }
    }

    private function _json($body, $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
