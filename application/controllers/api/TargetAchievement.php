<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/TargetAchievement.php
 *
 * Target vs Achievement + Target-to-Review linkage backend.
 * WS-T build 2026-06-07.
 *
 * ENDPOINTS:
 *   GET target/vs_achievement?user_id=&quarter=
 *   GET target/review_link?user_id=
 *   GET target/leaderboard?quarter=
 *   GET target/export_pdf?user_id=&quarter=   (bearer, streams PDF)
 *   GET target/export_excel?user_id=&quarter= (bearer, streams XLSX)
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo (master) or
 *       api_token table row (same _check_bearer as Export.php).
 *
 * Rules: ASCII only. "Rs" for rupees. "percent" spelled out.
 *        No em-dash/en-dash. Empty -> {ok:true,empty:true}.
 *        Additive only. Never break existing features.
 *        After edits reset opcache.
 */
class TargetAchievement extends CI_Controller
{
    // Master bearer token (matches cluster_build_spec.md)
    const MASTER_TOKEN = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Axis label map: target_actuals.axis -> display label
    private static $AXIS_LABELS = array(
        'revenue_rs_cr'         => 'Revenue (Rs Cr)',
        'rp_meetings'           => 'Meetings',
        'barg_in'               => 'Barg-in',
        'out_station'           => 'Out-station',
        'local_station'         => 'Local',
        'dmft_activation'       => 'DMFT Activation',
        'new_lead_funnel'       => 'New Lead Funnel',
        'twenty_closure_funnel' => 'Closures',
        'anchor_meetings'       => 'Anchor',
        'proposal_rs_cr'        => 'Proposal (Rs Cr)',
        'upsell_rm_coverage_pct'=> 'Upsell RM Coverage percent',
    );

    // Axis -> target_vs_achievement column mapping
    // Used to pull target values from the target_vs_achievement row.
    private static $AXIS_TO_TVA_COL = array(
        'revenue_rs_cr'         => 'revenue',
        'rp_meetings'           => 'no_of_meeting',
        'barg_in'               => 'num_of_barg_meeting',
        'out_station'           => 'out_station_meeting',
        'local_station'         => 'local_station_meeting',
        'dmft_activation'       => 'school',
        'new_lead_funnel'       => 'fifity_new_lead_funnel',
        'twenty_closure_funnel' => 'twetenty_closure_funnel',
        'anchor_meetings'       => 'anchor_client_meeting',
        'proposal_rs_cr'        => 'no_of_proposal',
        'upsell_rm_coverage_pct'=> null,
    );

    // Canonical axis order for the grouped-bar chart
    private static $AXIS_ORDER = array(
        'rp_meetings',
        'proposal_rs_cr',
        'revenue_rs_cr',
        'barg_in',
        'out_station',
        'local_station',
        'twenty_closure_funnel',
        'anchor_meetings',
    );

    // dompdf autoload path
    const DOMPDF_AUTOLOAD = APPPATH . '../application/third_party/dompdf/vendor/autoload.php';

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    // -------------------------------------------------------------------------
    // Auth: copy of Export.php _check_bearer + master token support
    // -------------------------------------------------------------------------
    protected function _check_bearer()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $auth = '';
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $auth = $headers['authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!$auth || stripos($auth, 'Bearer ') !== 0) return false;
        $token = trim(substr($auth, 7));
        if ($token === '') return false;

        // Accept master token directly
        if ($token === self::MASTER_TOKEN) {
            return array('uid' => 0, 'role' => 'admin');
        }

        // Fall back to api_token table (same as Export.php)
        try {
            $row = $this->db->query(
                'SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1',
                array($token)
            )->row_array();
            if ($row) return $row;
        } catch (Exception $e) {
            // api_token table may not have all columns - ignore
        }
        return false;
    }

    protected function _json($payload, $http_code = 200)
    {
        // serialize_precision=100 causes float noise; 14 gives clean output
        $old_sp = ini_get('serialize_precision');
        ini_set('serialize_precision', 14);
        $encoded = json_encode($payload);
        ini_set('serialize_precision', $old_sp);
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output($encoded);
    }

    // Sanitize a DB string to ASCII only, strip em/en dashes
    protected function _ascii($s)
    {
        if ($s === null) return '';
        $s = str_replace(array("\xe2\x80\x93", "\xe2\x80\x94", chr(150), chr(151)), '-', $s);
        return preg_replace('/[^\x20-\x7E]/', '', (string)$s);
    }

    // Safe numeric cast
    protected function _num($v)
    {
        if ($v === null || $v === '') return 0;
        return (float)preg_replace('/[^0-9.\-]/', '', (string)$v);
    }

    // =========================================================================
    // 1. GET target/vs_achievement?user_id=&quarter=
    //    Grouped-bar chart: Target vs Achieved per axis.
    // =========================================================================
    public function vs_achievement()
    {
        $auth = $this->_check_bearer();
        if (!$auth) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
            return;
        }

        $user_id = (int)$this->input->get('user_id');
        $quarter  = $this->_ascii($this->input->get('quarter'));

        if ($user_id <= 0) {
            $this->_json(array('ok' => false, 'error' => 'user_id is required'), 400);
            return;
        }

        try {
            // Pull target_vs_achievement rows for this user
            $where_q = '';
            $params  = array($user_id);
            if ($quarter !== '') {
                $where_q = ' AND tva.currentQuarter = ?';
                $params[] = $quarter;
            }

            $tva_rows = $this->db->query(
                'SELECT tva.id, tva.currentQuarter, tva.user_id,
                        tva.no_of_meeting, tva.no_of_proposal, tva.revenue,
                        tva.school, tva.out_station_meeting, tva.local_station_meeting,
                        tva.twetenty_closure_funnel, tva.anchor_client_meeting,
                        tva.num_of_barg_meeting, tva.fifity_new_lead_funnel,
                        tva.start_date, tva.end_date, tva.review_id
                 FROM target_vs_achievement tva
                 WHERE tva.user_id = ?' . $where_q . '
                 ORDER BY tva.start_date DESC
                 LIMIT 1',
                $params
            )->row_array();

            // Get user name
            $uname_row = $this->db->query(
                'SELECT name FROM user WHERE uid = ? LIMIT 1',
                array($user_id)
            )->row_array();
            $user_name = $uname_row ? $this->_ascii($uname_row['name']) : 'User ' . $user_id;

            if (empty($tva_rows)) {
                return $this->_json(array(
                    'ok'     => true,
                    'empty'  => true,
                    'chart'  => 'target_vs_achievement',
                    'type'   => 'grouped_bar',
                    'title'  => 'Target vs Achievement',
                    'labels' => array(),
                    'series' => array(
                        array('name' => 'Target',   'color' => '#1B474D', 'data' => array()),
                        array('name' => 'Achieved',  'color' => '#20808D', 'data' => array()),
                    ),
                    'meta'   => array(
                        'user_id'   => $user_id,
                        'user_name' => $user_name,
                        'quarter'   => $quarter,
                        'review_id' => null,
                    ),
                ));
            }

            $tva        = $tva_rows;
            $quarter_q  = $this->_ascii($tva['currentQuarter']);
            $start_date = $tva['start_date'];
            $end_date   = $tva['end_date'];
            $review_id  = $tva['review_id'];

            // Pull latest actual per axis from target_actuals for this user
            // Join via target_quarter to find matching cluster quarter for this RM
            // OR directly by uid=user_id in target_actuals (actuals are tracked by uid=user_id)
            $actuals_rows = $this->db->query(
                'SELECT ta.axis, ta.actual_cumulative, ta.pace_target, ta.variance_pct, ta.snapshot_date
                 FROM target_actuals ta
                 WHERE ta.uid = ?
                 AND ta.snapshot_date = (
                     SELECT MAX(ta2.snapshot_date)
                     FROM target_actuals ta2
                     WHERE ta2.uid = ta.uid AND ta2.axis = ta.axis
                 )
                 ORDER BY ta.axis',
                array($user_id)
            )->result_array();

            // Also try joining by target_quarter (user may be rm_uid)
            // If direct lookup yielded nothing, try via target_quarter
            if (empty($actuals_rows)) {
                $actuals_rows = $this->db->query(
                    'SELECT ta.axis, ta.actual_cumulative, ta.pace_target, ta.variance_pct, ta.snapshot_date
                     FROM target_actuals ta
                     INNER JOIN target_quarter tq ON tq.id = ta.target_quarter_id
                     WHERE tq.rm_uid = ?
                     AND ta.snapshot_date = (
                         SELECT MAX(ta2.snapshot_date)
                         FROM target_actuals ta2
                         WHERE ta2.target_quarter_id = ta.target_quarter_id AND ta2.axis = ta.axis
                     )
                     ORDER BY ta.axis',
                    array($user_id)
                )->result_array();
            }

            // Build achieved lookup keyed by axis
            $achieved_by_axis = array();
            foreach ($actuals_rows as $ar) {
                $achieved_by_axis[$ar['axis']] = $this->_num($ar['actual_cumulative']);
            }

            // Build labels, target data, achieved data using canonical axis order
            $labels        = array();
            $target_data   = array();
            $achieved_data = array();

            foreach (self::$AXIS_ORDER as $axis) {
                $labels[]    = self::$AXIS_LABELS[$axis];
                $tva_col     = self::$AXIS_TO_TVA_COL[$axis];
                $target_val  = $tva_col ? $this->_num($tva[$tva_col]) : 0;
                // revenue column is stored in rupees; axis is Rs Cr, so convert to Cr
                if ($axis === 'revenue_rs_cr' && $target_val > 0) {
                    $target_val = (float)number_format($target_val / 10000000, 2, '.', '');
                }
                $achiev_val  = isset($achieved_by_axis[$axis]) ? (float)number_format((float)$achieved_by_axis[$axis], 4, '.', '') : 0;
                $target_data[]   = $target_val;
                $achieved_data[] = $achiev_val;
            }

            $this->_json(array(
                'ok'     => true,
                'empty'  => false,
                'chart'  => 'target_vs_achievement',
                'type'   => 'grouped_bar',
                'title'  => 'Target vs Achievement - ' . $user_name . ' (' . $quarter_q . ')',
                'labels' => $labels,
                'series' => array(
                    array('name' => 'Target',   'color' => '#1B474D', 'data' => $target_data),
                    array('name' => 'Achieved',  'color' => '#20808D', 'data' => $achieved_data),
                ),
                'meta'   => array(
                    'user_id'    => $user_id,
                    'user_name'  => $user_name,
                    'quarter'    => $quarter_q,
                    'start_date' => $start_date,
                    'end_date'   => $end_date,
                    'review_id'  => $review_id,
                ),
            ));

        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    // =========================================================================
    // 2. GET target/review_link?user_id=
    //    Target-to-Review linkage list.
    // =========================================================================
    public function review_link()
    {
        $auth = $this->_check_bearer();
        if (!$auth) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
            return;
        }

        $user_id = (int)$this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_json(array('ok' => false, 'error' => 'user_id is required'), 400);
            return;
        }

        try {
            // Pull all target rows for this user with review join
            $rows = $this->db->query(
                'SELECT tva.id AS target_id,
                        tva.currentQuarter AS quarter,
                        tva.start_date,
                        tva.end_date,
                        tva.review_id,
                        tva.no_of_meeting,
                        tva.no_of_proposal,
                        tva.revenue,
                        tva.school,
                        tva.out_station_meeting,
                        tva.local_station_meeting,
                        tva.twetenty_closure_funnel,
                        tva.anchor_client_meeting,
                        tva.num_of_barg_meeting,
                        tva.fifity_new_lead_funnel,
                        mr.id AS mr_id,
                        mr.rtype AS review_type,
                        mr.sdate AS review_date,
                        mr.for_uid AS review_for_uid,
                        mr.Q_category AS review_q_category
                 FROM target_vs_achievement tva
                 LEFT JOIN main_review mr ON mr.id = tva.review_id
                 WHERE tva.user_id = ?
                 ORDER BY tva.start_date DESC',
                array($user_id)
            )->result_array();

            if (empty($rows)) {
                return $this->_json(array(
                    'ok'      => true,
                    'empty'   => true,
                    'user_id' => $user_id,
                    'links'   => array(),
                    'meta'    => array('total' => 0),
                ));
            }

            // Pull latest actuals per axis for this user (for summary)
            $actuals_rows = $this->db->query(
                'SELECT ta.axis, ta.actual_cumulative, ta.snapshot_date
                 FROM target_actuals ta
                 WHERE ta.uid = ?
                 AND ta.snapshot_date = (
                     SELECT MAX(ta2.snapshot_date)
                     FROM target_actuals ta2
                     WHERE ta2.uid = ta.uid AND ta2.axis = ta.axis
                 )',
                array($user_id)
            )->result_array();

            // If no direct actuals, try via target_quarter rm_uid
            if (empty($actuals_rows)) {
                $actuals_rows = $this->db->query(
                    'SELECT ta.axis, ta.actual_cumulative, ta.snapshot_date
                     FROM target_actuals ta
                     INNER JOIN target_quarter tq ON tq.id = ta.target_quarter_id
                     WHERE tq.rm_uid = ?
                     AND ta.snapshot_date = (
                         SELECT MAX(ta2.snapshot_date)
                         FROM target_actuals ta2
                         WHERE ta2.target_quarter_id = ta.target_quarter_id AND ta2.axis = ta.axis
                     )',
                    array($user_id)
                )->result_array();
            }

            $achieved_by_axis = array();
            foreach ($actuals_rows as $ar) {
                $achieved_by_axis[$ar['axis']] = $this->_num($ar['actual_cumulative']);
            }

            $links = array();
            foreach ($rows as $row) {
                // Target summary (key axes)
                $target_summary = array(
                    'meetings'        => (int)$row['no_of_meeting'],
                    'proposals'       => (int)$row['no_of_proposal'],
                    'revenue_rs_cr'   => (float)number_format($this->_num($row['revenue']) / 10000000, 2, '.', ''),
                    'schools'         => (int)$row['school'],
                    'out_station'     => (int)$row['out_station_meeting'],
                    'local_station'   => (int)$row['local_station_meeting'],
                    'closures'        => $this->_num($row['twetenty_closure_funnel']),
                    'anchor_meetings' => $this->_num($row['anchor_client_meeting']),
                    'barg_in'         => (int)$row['num_of_barg_meeting'],
                    'new_lead_funnel' => $this->_num($row['fifity_new_lead_funnel']),
                );

                // Achieved summary from latest actuals
                $achieved_summary = array(
                    'revenue_rs_cr' => isset($achieved_by_axis['revenue_rs_cr']) ? round((float)$achieved_by_axis['revenue_rs_cr'], 4) : 0,
                    'meetings'      => isset($achieved_by_axis['rp_meetings']) ? round((float)$achieved_by_axis['rp_meetings'], 4) : 0,
                    'barg_in'       => isset($achieved_by_axis['barg_in']) ? round((float)$achieved_by_axis['barg_in'], 4) : 0,
                    'out_station'   => isset($achieved_by_axis['out_station']) ? round((float)$achieved_by_axis['out_station'], 4) : 0,
                    'local_station' => isset($achieved_by_axis['local_station']) ? round((float)$achieved_by_axis['local_station'], 4) : 0,
                    'closures'      => isset($achieved_by_axis['twenty_closure_funnel']) ? round((float)$achieved_by_axis['twenty_closure_funnel'], 4) : 0,
                    'anchor_meetings'=> isset($achieved_by_axis['anchor_meetings']) ? round((float)$achieved_by_axis['anchor_meetings'], 4) : 0,
                    'proposals'     => isset($achieved_by_axis['proposal_rs_cr']) ? round((float)$achieved_by_axis['proposal_rs_cr'], 4) : 0,
                );

                // Review info
                $review_info = null;
                if ($row['review_id']) {
                    $review_info = array(
                        'id'           => (int)$row['mr_id'],
                        'type'         => $this->_ascii($row['review_type']),
                        'date'         => $row['review_date'],
                        'for_uid'      => (int)$row['review_for_uid'],
                        'q_category'   => $this->_ascii($row['review_q_category']),
                    );
                }

                $links[] = array(
                    'target_id'        => (int)$row['target_id'],
                    'quarter'          => $this->_ascii($row['quarter']),
                    'start_date'       => $row['start_date'],
                    'end_date'         => $row['end_date'],
                    'review_id'        => $row['review_id'] ? (int)$row['review_id'] : null,
                    'review_info'      => $review_info,
                    'target_summary'   => $target_summary,
                    'achieved_summary' => $achieved_summary,
                );
            }

            $this->_json(array(
                'ok'      => true,
                'empty'   => false,
                'user_id' => $user_id,
                'links'   => $links,
                'meta'    => array('total' => count($links)),
            ));

        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    // =========================================================================
    // 3. GET target/leaderboard?quarter=
    //    Rank all users in target_vs_achievement for a quarter by achievement.
    // =========================================================================
    public function leaderboard()
    {
        $auth = $this->_check_bearer();
        if (!$auth) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
            return;
        }

        $quarter = $this->_ascii($this->input->get('quarter'));

        try {
            $where_q = '';
            $params  = array();
            if ($quarter !== '') {
                $where_q  = ' WHERE tva.currentQuarter = ?';
                $params[] = $quarter;
            }

            // Fetch all target rows for the quarter (latest per user)
            $tva_rows = $this->db->query(
                'SELECT tva.user_id, tva.currentQuarter, tva.revenue,
                        tva.no_of_meeting, tva.no_of_proposal,
                        tva.twetenty_closure_funnel, tva.anchor_client_meeting,
                        u.name AS user_name
                 FROM target_vs_achievement tva
                 LEFT JOIN user u ON u.uid = tva.user_id
                 ' . $where_q . '
                 ORDER BY tva.user_id, tva.start_date DESC',
                $params
            )->result_array();

            if (empty($tva_rows)) {
                return $this->_json(array(
                    'ok'      => true,
                    'empty'   => true,
                    'chart'   => 'target_leaderboard',
                    'type'    => 'leaderboard',
                    'title'   => 'Target Achievement Leaderboard' . ($quarter ? ' - ' . $quarter : ''),
                    'quarter' => $quarter,
                    'leaders' => array(),
                    'meta'    => array('total_users' => 0),
                ));
            }

            // Deduplicate to one row per user (latest)
            $user_rows = array();
            foreach ($tva_rows as $row) {
                $uid = (int)$row['user_id'];
                if (!isset($user_rows[$uid])) {
                    $user_rows[$uid] = $row;
                }
            }

            // Pull latest actuals per user (revenue_rs_cr axis as primary measure)
            $uid_list = implode(',', array_map('intval', array_keys($user_rows)));
            $actuals  = array();
            if ($uid_list !== '') {
                $act_rows = $this->db->query(
                    'SELECT ta.uid, ta.axis, ta.actual_cumulative
                     FROM target_actuals ta
                     WHERE ta.uid IN (' . $uid_list . ')
                     AND ta.snapshot_date = (
                         SELECT MAX(ta2.snapshot_date)
                         FROM target_actuals ta2
                         WHERE ta2.uid = ta.uid AND ta2.axis = ta.axis
                     )
                     AND ta.axis = \'revenue_rs_cr\''
                )->result_array();
                foreach ($act_rows as $ar) {
                    $actuals[(int)$ar['uid']] = $this->_num($ar['actual_cumulative']);
                }
            }

            // Build leaderboard rows with achievement percent
            $leaders = array();
            foreach ($user_rows as $uid => $row) {
                $target_rev  = (float)number_format($this->_num($row['revenue']) / 10000000, 2, '.', ''); // rupees -> Rs Cr
                $achieved_rev = isset($actuals[$uid]) ? round((float)$actuals[$uid], 4) : 0;

                // Achievement percent: safe division; use 0 if target is 0
                $ach_pct = ($target_rev > 0) ? round(($achieved_rev / $target_rev) * 100, 2) : 0;

                $leaders[] = array(
                    'user_id'          => $uid,
                    'user_name'        => $this->_ascii($row['user_name']),
                    'quarter'          => $this->_ascii($row['currentQuarter']),
                    'target_revenue'   => $target_rev,
                    'achieved_revenue' => $achieved_rev,
                    'achievement_pct'  => $ach_pct,
                );
            }

            // Sort descending by achievement_pct
            usort($leaders, function($a, $b) {
                return $b['achievement_pct'] <=> $a['achievement_pct'];
            });

            // Add rank
            $rank = 1;
            foreach ($leaders as &$l) {
                $l['rank'] = $rank++;
            }
            unset($l);

            // Top 25
            $leaders = array_slice($leaders, 0, 25);

            $this->_json(array(
                'ok'      => true,
                'empty'   => false,
                'chart'   => 'target_leaderboard',
                'type'    => 'leaderboard',
                'title'   => 'Target Achievement Leaderboard' . ($quarter ? ' - ' . $quarter : ''),
                'quarter' => $quarter,
                'leaders' => $leaders,
                'meta'    => array('total_users' => count($user_rows)),
            ));

        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    // =========================================================================
    // 4. GET target/export_pdf?user_id=&quarter=
    //    Bearer-protected PDF download of user's target vs achievement.
    //    Matches FunnelExportController._build_pdf pattern (dompdf).
    // =========================================================================
    public function export_pdf()
    {
        $auth = $this->_check_bearer();
        if (!$auth) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'Unauthorized'));
            return;
        }

        $user_id = (int)$this->input->get('user_id');
        $quarter  = $this->_ascii($this->input->get('quarter'));

        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'user_id is required'));
            return;
        }

        try {
            // Load dompdf (same as FunnelExportController)
            if (file_exists(self::DOMPDF_AUTOLOAD) && !class_exists('Dompdf\Dompdf')) {
                require_once(self::DOMPDF_AUTOLOAD);
            }

            // Gather data
            $data = $this->_gather_tva_data($user_id, $quarter);
            $user_name = $data['user_name'];
            $q_label   = $data['quarter'];
            $rows      = $data['rows'];

            $cdate    = date('Ymd_His');
            $filename = 'target_vs_achievement_' . $user_id . '_' . $cdate . '.pdf';

            $content  = $this->_build_pdf($rows, $user_name, $user_id, $q_label);

            // Stream: same headers as FunnelExportController.export_pdf
            header_remove('Content-Type');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($content));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $content;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => $e->getMessage()));
        }
    }

    // =========================================================================
    // 5. GET target/export_excel?user_id=&quarter=
    //    Bearer-protected XLSX download of user's target vs achievement.
    //    Matches FunnelExportController._build_xlsx pattern.
    // =========================================================================
    public function export_excel()
    {
        $auth = $this->_check_bearer();
        if (!$auth) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'Unauthorized'));
            return;
        }

        $user_id = (int)$this->input->get('user_id');
        $quarter  = $this->_ascii($this->input->get('quarter'));

        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'user_id is required'));
            return;
        }

        try {
            // Gather data
            $data      = $this->_gather_tva_data($user_id, $quarter);
            $user_name = $data['user_name'];
            $q_label   = $data['quarter'];
            $rows      = $data['rows'];

            $cdate    = date('Ymd_His');
            $filename = 'target_vs_achievement_' . $user_id . '_' . $cdate . '.xlsx';

            $content  = $this->_build_xlsx($rows, $user_name, $user_id, $q_label);

            // Stream: same headers as FunnelExportController.export_xlsx
            header_remove('Content-Type');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($content));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $content;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => $e->getMessage()));
        }
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    /**
     * Gather target vs achievement data for a user/quarter.
     * Returns array('user_name', 'quarter', 'rows').
     * rows: array of [Axis, Target, Achieved, Variance percent] for export.
     */
    protected function _gather_tva_data($user_id, $quarter)
    {
        // User name
        $uname_row = $this->db->query(
            'SELECT name FROM user WHERE uid = ? LIMIT 1',
            array($user_id)
        )->row_array();
        $user_name = $uname_row ? $this->_ascii($uname_row['name']) : 'User ' . $user_id;

        // Target row
        $where_q = '';
        $params  = array($user_id);
        if ($quarter !== '') {
            $where_q  = ' AND tva.currentQuarter = ?';
            $params[] = $quarter;
        }

        $tva = $this->db->query(
            'SELECT tva.currentQuarter, tva.no_of_meeting, tva.no_of_proposal, tva.revenue,
                    tva.school, tva.out_station_meeting, tva.local_station_meeting,
                    tva.twetenty_closure_funnel, tva.anchor_client_meeting,
                    tva.num_of_barg_meeting, tva.fifity_new_lead_funnel
             FROM target_vs_achievement tva
             WHERE tva.user_id = ?' . $where_q . '
             ORDER BY tva.start_date DESC LIMIT 1',
            $params
        )->row_array();

        $q_label = $tva ? $this->_ascii($tva['currentQuarter']) : ($quarter ?: 'All');

        // Latest actuals per axis
        $actuals_rows = $this->db->query(
            'SELECT ta.axis, ta.actual_cumulative, ta.variance_pct
             FROM target_actuals ta
             WHERE ta.uid = ?
             AND ta.snapshot_date = (
                 SELECT MAX(ta2.snapshot_date)
                 FROM target_actuals ta2
                 WHERE ta2.uid = ta.uid AND ta2.axis = ta.axis
             )',
            array($user_id)
        )->result_array();

        if (empty($actuals_rows)) {
            $actuals_rows = $this->db->query(
                'SELECT ta.axis, ta.actual_cumulative, ta.variance_pct
                 FROM target_actuals ta
                 INNER JOIN target_quarter tq ON tq.id = ta.target_quarter_id
                 WHERE tq.rm_uid = ?
                 AND ta.snapshot_date = (
                     SELECT MAX(ta2.snapshot_date)
                     FROM target_actuals ta2
                     WHERE ta2.target_quarter_id = ta.target_quarter_id AND ta2.axis = ta.axis
                 )',
                array($user_id)
            )->result_array();
        }

        $achieved_by_axis  = array();
        $variance_by_axis  = array();
        foreach ($actuals_rows as $ar) {
            $achieved_by_axis[$ar['axis']] = $this->_num($ar['actual_cumulative']);
            $variance_by_axis[$ar['axis']] = $ar['variance_pct'] !== null ? (float)$ar['variance_pct'] : null;
        }

        // Build export rows: Axis | Target | Achieved | Variance percent
        $rows = array();
        foreach (self::$AXIS_ORDER as $axis) {
            $label      = self::$AXIS_LABELS[$axis];
            $tva_col    = self::$AXIS_TO_TVA_COL[$axis];
            $target_val = ($tva && $tva_col) ? $this->_num($tva[$tva_col]) : 0;
            // revenue column is stored in rupees; axis is Rs Cr, so convert to Cr
            if ($axis === 'revenue_rs_cr' && $target_val > 0) {
                $target_val = (float)number_format($target_val / 10000000, 2, '.', '');
            }
            $achiev_val = isset($achieved_by_axis[$axis]) ? round((float)$achieved_by_axis[$axis], 4) : 0;
            $var_pct    = isset($variance_by_axis[$axis]) ? $variance_by_axis[$axis] : null;

            // Compute variance percent if not already computed
            if ($var_pct === null) {
                $var_pct = ($target_val > 0) ? round((($achiev_val - $target_val) / $target_val) * 100, 2) : 0;
            }

            $rows[] = array(
                'Axis'             => $label,
                'Target'           => $target_val,
                'Achieved'         => $achiev_val,
                'Variance percent' => $var_pct,
            );
        }

        return array(
            'user_name' => $user_name,
            'quarter'   => $q_label,
            'rows'      => $rows,
        );
    }

    /**
     * Build PDF. Mirrors FunnelExportController._build_pdf exactly.
     * Uses dompdf (same as FunnelExportController).
     */
    protected function _build_pdf($rows, $user_name, $user_id, $quarter)
    {
        $now = date('d M Y H:i') . ' IST';

        $html  = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Calibri, Arial, sans-serif; font-size: 9pt; color: #222; margin: 20px; }';
        $html .= 'h1 { font-size: 14pt; color: #1B474D; margin: 0 0 4px 0; }';
        $html .= '.meta { font-size: 8pt; color: #666; margin-bottom: 12px; }';
        $html .= 'table { width: 100%; border-collapse: collapse; }';
        $html .= 'th { background: #1B474D; color: #fff; padding: 4px 6px; font-size: 8pt; text-align: left; border: 1px solid #1B474D; }';
        $html .= 'td { padding: 3px 6px; font-size: 8pt; border: 1px solid #ddd; }';
        $html .= 'tr:nth-child(even) td { background: #f6f8fa; }';
        $html .= '.pos { color: #437A22; } .neg { color: #A13544; }';
        $html .= 'p.note { font-size: 7pt; color: #888; margin-top: 12px; }';
        $html .= '</style></head><body>';
        $html .= '<h1>STEM CRM - Target vs Achievement</h1>';
        $html .= '<div class="meta">User: ' . htmlspecialchars($user_name) . ' (ID: ' . (int)$user_id . ')';
        $html .= ' | Quarter: ' . htmlspecialchars($quarter) . ' | Generated: ' . $now . '</div>';

        if (empty($rows)) {
            $html .= '<p>No data available.</p>';
        } else {
            $html .= '<table><thead><tr>';
            $html .= '<th>Axis</th><th>Target</th><th>Achieved</th><th>Variance percent</th>';
            $html .= '</tr></thead><tbody>';
            foreach ($rows as $row) {
                $var_cls = ($row['Variance percent'] >= 0) ? 'pos' : 'neg';
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars((string)$row['Axis']) . '</td>';
                $html .= '<td>' . htmlspecialchars((string)$row['Target']) . '</td>';
                $html .= '<td>' . htmlspecialchars((string)$row['Achieved']) . '</td>';
                $html .= '<td class="' . $var_cls . '">' . htmlspecialchars((string)$row['Variance percent']) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }

        $html .= '<p class="note">Source: selfstagingstemapp.in target/export_pdf. Rs for rupees. ASCII only.</p>';
        $html .= '</body></html>';

        if (class_exists('Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf(array('isRemoteEnabled' => false));
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return $dompdf->output();
        }

        // Fallback: minimal PDF (same as Export.php _minimal_pdf)
        return $this->_minimal_pdf(
            'Target vs Achievement - ' . $user_name . ' (' . $quarter . ')',
            "STEM CRM Export\nUser: $user_name (ID: $user_id)\nQuarter: $quarter\nGenerated: " . date('d M Y H:i:s') . "\n\nRows: " . count($rows)
        );
    }

    /**
     * Build XLSX. Mirrors FunnelExportController._build_xlsx exactly.
     * PhpSpreadsheet tried first; CSV fallback on failure.
     */
    protected function _build_xlsx($rows, $user_name, $user_id, $quarter)
    {
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Target vs Achievement');

            if (empty($rows)) {
                $sheet->setCellValue('A1', 'no_data');
            } else {
                $headers = array_keys($rows[0]);
                $col = 1;
                foreach ($headers as $h) {
                    $sheet->setCellValueByColumnAndRow($col, 1, $h);
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                    $col++;
                }
                $last_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
                $sheet->getStyle("A1:{$last_col}1")->getFont()->setBold(true);

                $row_idx = 2;
                foreach ($rows as $row) {
                    $col = 1;
                    foreach (array_values($row) as $cell) {
                        $sheet->setCellValueByColumnAndRow($col, $row_idx, $cell ?? '');
                        $col++;
                    }
                    $row_idx++;
                }
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            return ob_get_clean();
        }

        // CSV fallback (same as FunnelExportController._build_csv)
        return $this->_build_csv($rows, 'Note: PhpSpreadsheet not installed. This is a CSV file with an .xlsx extension.');
    }

    /**
     * Build CSV fallback - same pattern as FunnelExportController._build_csv.
     */
    protected function _build_csv($rows, $note = '')
    {
        $out = "\xEF\xBB\xBF"; // UTF-8 BOM
        if (!empty($note)) {
            $out .= $this->_csv_line(array($note));
        }
        if (empty($rows)) {
            $out .= $this->_csv_line(array('no_data'));
            return $out;
        }
        $out .= $this->_csv_line(array_keys($rows[0]));
        foreach ($rows as $row) {
            $out .= $this->_csv_line(array_values($row));
        }
        return $out;
    }

    protected function _csv_line($values)
    {
        $cells = array();
        foreach ($values as $v) {
            $s = (string)($v ?? '');
            if (strpbrk($s, ',"' . "\n\r") !== false) {
                $s = '"' . str_replace('"', '""', $s) . '"';
            }
            $cells[] = $s;
        }
        return implode(',', $cells) . "\r\n";
    }

    /**
     * Minimal PDF fallback - same as Export.php _minimal_pdf.
     */
    protected function _minimal_pdf($title, $body_text)
    {
        $lines = explode("\n", wordwrap($body_text, 80));
        $text_stream = '';
        $y = 750;
        foreach ($lines as $line) {
            $safe = str_replace(array('(', ')', '\\'), array('\\(', '\\)', '\\\\'), $line);
            $text_stream .= "BT /F1 12 Tf 50 $y Td ($safe) Tj ET\n";
            $y -= 16;
            if ($y < 50) break;
        }

        $objects = array();
        $objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[2] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[3] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        $objects[4] = "4 0 obj\n<< /Length " . strlen($text_stream) . " >>\nstream\n" . $text_stream . "endstream\nendobj\n";
        $objects[5] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $out  = "%PDF-1.4\n";
        $xref = array();
        foreach ($objects as $n => $obj) {
            $xref[$n] = strlen($out);
            $out .= $obj;
        }
        $xref_offset = strlen($out);
        $out .= "xref\n0 " . (count($objects) + 1) . "\n";
        $out .= "0000000000 65535 f \n";
        foreach ($xref as $offset) {
            $out .= str_pad($offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $out .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref_offset\n%%EOF\n";
        return $out;
    }
}
