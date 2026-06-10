<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Applause_api
 * Endpoint: GET /api/applause/today?date=YYYY-MM-DD&lookback_days=N
 *
 * Real DB implementation. Reads from applause_log and lead_progression_log.
 *
 * applause_log columns: id, from_uid, to_uid, applause_type, message, lead_id,
 *                       visibility, created_at, updated_at
 * lead_progression_log: looked up by bd_uid and to_status >= 6 for positive moves.
 *
 * lookback_days (default 3): include rows from (date - lookback_days) to date.
 * This ensures non-empty results even when no row landed exactly today.
 *
 * Route: routes_cron_endpoints.php -> Applause_api/today
 */
class Applause_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    private function _out($p) {
        echo json_encode($p);
        exit;
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(401);
            $this->_out(array('ok' => false, 'error' => 'unauthorized'));
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected) {
            $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        if (!hash_equals($expected, $tok)) {
            $this->output->set_status_header(401);
            $this->_out(array('ok' => false, 'error' => 'bad_token'));
        }
        return true;
    }

    /**
     * GET /api/applause/today?date=YYYY-MM-DD&lookback_days=N
     *
     * Returns applause events from:
     *   1. applause_log (real applause events written by system or staff)
     *   2. lead_progression_log WHERE to_status >= 6 (positive funnel movements)
     *
     * lookback_days (default 3): fetches from (date - N days) to date inclusive.
     * Result is merged, deduped, and sorted by created_at DESC.
     */
    public function today() {
        $this->_bearer();

        $date_param    = $this->input->get('date') ?: date('Y-m-d');
        $lookback_days = max(0, min(30, (int)($this->input->get('lookback_days') ?: 3)));

        $date_to   = date('Y-m-d', strtotime($date_param));
        $date_from = date('Y-m-d', strtotime($date_param . ' -' . $lookback_days . ' days'));

        // Source A: applause_log with user_details join for names
        $sql_a = "SELECT
                    a.id              AS applause_id,
                    a.applause_type   AS event_type,
                    a.to_uid          AS bd_uid,
                    ud.name           AS bd_name,
                    a.lead_id,
                    a.message,
                    a.visibility,
                    a.created_at,
                    'applause_log'    AS source
                  FROM applause_log a
                  LEFT JOIN user_details ud ON ud.user_id = a.to_uid
                  WHERE DATE(a.created_at) BETWEEN ? AND ?
                  ORDER BY a.created_at DESC
                  LIMIT 200";

        $al_rows = $this->db->query($sql_a, array($date_from, $date_to))->result_array();

        // Source B: lead_progression_log positive moves (to_status >= 6)
        $sql_b = "SELECT
                    lp.id             AS applause_id,
                    CASE
                        WHEN lp.to_status = 12 THEN 'deal_won'
                        WHEN lp.to_status >= 9  THEN 'milestone'
                        ELSE 'team_effort'
                    END               AS event_type,
                    lp.bd_uid,
                    ud.name           AS bd_name,
                    lp.lead_id,
                    CONCAT('Positive funnel move to status ', lp.to_status) AS message,
                    'all'             AS visibility,
                    lp.created_at,
                    'lead_progression_log' AS source
                  FROM lead_progression_log lp
                  LEFT JOIN user_details ud ON ud.user_id = lp.bd_uid
                  WHERE lp.to_status >= 6
                    AND DATE(lp.created_at) BETWEEN ? AND ?
                  ORDER BY lp.created_at DESC
                  LIMIT 200";

        $lp_rows = $this->db->query($sql_b, array($date_from, $date_to))->result_array();

        // Merge and sort by created_at DESC
        $all_rows = array_merge($al_rows, $lp_rows);
        usort($all_rows, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        // Cap at 200 total
        $all_rows = array_slice($all_rows, 0, 200);

        if (empty($all_rows)) {
            $this->_out(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'         => 0,
                    'date_from'     => $date_from,
                    'date_to'       => $date_to,
                    'lookback_days' => $lookback_days,
                    'reason'        => 'no_rows',
                ),
                'route'        => 'api/applause/today',
                'generated_at' => date('c'),
            ));
        }

        $this->_out(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $all_rows,
            'data'         => array(
                'count'         => count($all_rows),
                'date_from'     => $date_from,
                'date_to'       => $date_to,
                'lookback_days' => $lookback_days,
            ),
            'route'        => 'api/applause/today',
            'generated_at' => date('c'),
        ));
    }

    /**
     * GET /api/wdl/list?status=&bd_uid=&limit=
     * Real WDL (work-discipline-log) requests from wdl_request table.
     */
    public function wdl_list() {
        $this->_bearer();
        $status = $this->input->get('status');
        $bd_uid = (int) $this->input->get('bd_uid');
        $limit  = max(1, min(500, (int) ($this->input->get('limit') ?: 100)));

        $this->db->select('w.id, w.bd_uid, u.name AS bd_name, w.request_date, '
                        . 'w.request_type, w.status, w.requested_reason, '
                        . 'w.approved_by_uid, w.approved_at, w.created_at', false)
                 ->from('wdl_request w')
                 ->join('user u', 'u.uid = w.bd_uid', 'left')
                 ->order_by('w.request_date', 'DESC')
                 ->limit($limit);
        if ($status) { $this->db->where('w.status', $status); }
        if ($bd_uid > 0) { $this->db->where('w.bd_uid', $bd_uid); }
        $rows = $this->db->get()->result_array();

        $this->_out(array(
            'ok'    => true,
            'count' => is_array($rows) ? count($rows) : 0,
            'rows'  => is_array($rows) ? $rows : array(),
        ));
    }

}
