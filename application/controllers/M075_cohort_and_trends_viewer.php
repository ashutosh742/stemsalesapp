<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M075 Cohort and Trends Viewer Controller
 * Routes (no /api/ prefix):
 *   POST /m075_cohort_and_trends_viewer/create
 *   GET  /m075_cohort_and_trends_viewer/list
 *   GET  /m075_cohort_and_trends_viewer/members
 *   POST /m075_cohort_and_trends_viewer/snapshot_run
 *   GET  /m075_cohort_and_trends_viewer/series
 *   POST /m075_cohort_and_trends_viewer/refresh
 *   GET  /m075_cohort_and_trends_viewer/dashboard
 *
 * The original file had two classes (M075_cohort_and_trends_viewer + Trends).
 * CI3 only loads the first class, so the Trends methods were unreachable.
 * All Trends methods have been merged into this class. The second class block
 * has been removed.
 */

class M075_cohort_and_trends_viewer extends CI_Controller
{
    private $_bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Metrics used by the trends dashboard (30-day demo set).
    private $_metrics_30d_demo = array(
        'leads_added_daily'  => array('unit' => 'count',   'label' => 'Leads Added Daily'),
        'mom_count_daily'    => array('unit' => 'count',   'label' => 'MOMs Recorded Daily'),
        'won_rs_daily'       => array('unit' => 'Rs',      'label' => 'Won Rs Daily'),
        'pipeline_rs_total'  => array('unit' => 'Rs',      'label' => 'Pipeline Rs Total'),
        'bd_grade_a_count'   => array('unit' => 'count',   'label' => 'BD Grade A Count'),
        'sla_breach_count'   => array('unit' => 'count',   'label' => 'SLA Breach Count'),
        'avg_days_to_won'    => array('unit' => 'days',    'label' => 'Avg Days to Won'),
        'csr_quota_used'     => array('unit' => 'percent', 'label' => 'CSR Quota Used Percent'),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->_check_auth();
    }

    // ---- per-user JWT validator (copied from Mobile_read_api 28 May 2026) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _check_auth()
    {
        $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$header) {
            $header = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '';
        }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        if (strpos($header, 'Bearer ') !== 0) {
            $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401);
            exit;
        }
        $token = trim(substr($header, 7));
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        // Accept admin token
        if (hash_equals($secret, $token)) return;
        if (hash_equals($this->_bearer, $token)) return;
        // Accept per-user JWT
        if ($this->_jwt_token_valid($token)) return;
        $this->_json(array('ok' => false, 'error' => 'forbidden'), 403);
        exit;
    }

    // ------------------------------------------------------------------ GET /api/cohort/probe

    public function probe()
    {
        $this->_json(array(
            'ok'        => true,
            'migration' => '075',
            'component' => 'cohort_and_trends_viewer',
        ));
    }

    // ------------------------------------------------------------------ GET /api/cohort/trends

    public function trends()
    {
        $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-30 days'));
        $to   = $this->input->get('to')   ?: date('Y-m-d');
        $uid  = (int)($this->input->get('uid') ?: 0);

        $metrics_meta = array();
        foreach ($this->_metrics_30d_demo as $key => $meta) {
            $metrics_meta[] = array_merge(array('key' => $key), $meta);
        }
        $this->_json(array(
            'ok'           => true,
            'from'         => $from,
            'to'           => $to,
            'uid'          => $uid,
            'metrics_meta' => $metrics_meta,
            'note'         => 'Trend series data requires snapshot_run to populate cohort_trend_series table.',
        ));
    }

    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    // ------------------------------------------------------------------ POST /m075_cohort_and_trends_viewer/create

    public function create()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $name        = trim((string)$this->input->post('name'));
        $description = trim((string)$this->input->post('description'));
        $filter_json = trim((string)$this->input->post('filter_json')) ?: '{}';
        $uid         = (int)$this->input->post('uid');
        $is_shared   = (int)$this->input->post('is_shared');

        if (!$name) {
            $this->_json(array('ok' => false, 'error' => 'missing_name'), 400);
            return;
        }

        $filter_decoded = json_decode($filter_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->_json(array('ok' => false, 'error' => 'invalid_filter_json'), 400);
            return;
        }

        $row = array(
            'name'            => $name,
            'description'     => $description,
            'filter_json'     => $filter_json,
            'created_by_uid'  => $uid,
            'created_at'      => date('Y-m-d H:i:s'),
            'is_shared'       => $is_shared ? 1 : 0,
        );
        $this->db->insert('cohort_definition', $row);
        $cohort_id = $this->db->insert_id();

        $this->_json(array('ok' => true, 'cohort_id' => $cohort_id, 'message' => 'Cohort created.'));
    }

    // ------------------------------------------------------------------ GET /m075_cohort_and_trends_viewer/list
    // Method renamed from listing() to list() to match the smoke test route.

    public function list()
    {
        $uid = (int)($this->input->get('uid') ?: 0);

        $this->db->group_start();
        $this->db->where('is_shared', 1);
        if ($uid) {
            $this->db->or_where('created_by_uid', $uid);
        }
        $this->db->group_end();

        $rows = $this->db->order_by('created_at', 'DESC')->get('cohort_definition')->result_array();
        $this->_json(array('ok' => true, 'cohorts' => $rows ?: array()));
    }

    // ------------------------------------------------------------------ GET /m075_cohort_and_trends_viewer/members

    public function members()
    {
        $cohort_id = (int)($this->input->get('cohort_id') ?: 0);
        if (!$cohort_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_cohort_id'), 400);
            return;
        }

        $cohort = $this->db->get_where('cohort_definition', array('id' => $cohort_id))->row_array();
        if (!$cohort) {
            $this->_json(array('ok' => false, 'error' => 'cohort_not_found'), 404);
            return;
        }

        $filter = json_decode($cohort['filter_json'], true) ?: array();
        $this->db->select('cid_id, uid, cname, cstatus, bd_grade, cluster_id, fbudget_rs');

        if (!empty($filter['cstatus_in']) && is_array($filter['cstatus_in'])) {
            $this->db->where_in('cstatus', $filter['cstatus_in']);
        }
        if (!empty($filter['bd_grade_in']) && is_array($filter['bd_grade_in'])) {
            $this->db->where_in('bd_grade', $filter['bd_grade_in']);
        }
        if (!empty($filter['cluster_id_in']) && is_array($filter['cluster_id_in'])) {
            $this->db->where_in('cluster_id', $filter['cluster_id_in']);
        }
        if (!empty($filter['uid_in']) && is_array($filter['uid_in'])) {
            $this->db->where_in('uid', $filter['uid_in']);
        }
        if (isset($filter['fbudget_rs_min'])) {
            $this->db->where('fbudget_rs >=', (float)$filter['fbudget_rs_min']);
        }
        if (isset($filter['fbudget_rs_max'])) {
            $this->db->where('fbudget_rs <=', (float)$filter['fbudget_rs_max']);
        }
        if (isset($filter['days_created_max'])) {
            $cutoff = date('Y-m-d', strtotime('-' . (int)$filter['days_created_max'] . ' days'));
            $this->db->where('created_at >=', $cutoff);
        }

        $members = $this->db->get('init_call')->result_array();
        $this->_json(array(
            'ok'           => true,
            'cohort_id'    => $cohort_id,
            'cohort_name'  => $cohort['name'],
            'member_count' => count($members),
            'members'      => $members,
        ));
    }

    // ------------------------------------------------------------------ POST /m075_cohort_and_trends_viewer/snapshot_run

    public function snapshot_run()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $cohort_id = (int)$this->input->post('cohort_id');
        if (!$cohort_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_cohort_id'), 400);
            return;
        }

        $cohort = $this->db->get_where('cohort_definition', array('id' => $cohort_id))->row_array();
        if (!$cohort) {
            $this->_json(array('ok' => false, 'error' => 'cohort_not_found'), 404);
            return;
        }

        $filter = json_decode($cohort['filter_json'], true) ?: array();
        $q = $this->db->select('COUNT(*) AS cnt');
        if (!empty($filter['cstatus_in']))   $q->where_in('cstatus', $filter['cstatus_in']);
        if (!empty($filter['bd_grade_in']))   $q->where_in('bd_grade', $filter['bd_grade_in']);
        if (!empty($filter['cluster_id_in'])) $q->where_in('cluster_id', $filter['cluster_id_in']);
        if (!empty($filter['uid_in']))        $q->where_in('uid', $filter['uid_in']);
        if (isset($filter['fbudget_rs_min'])) $q->where('fbudget_rs >=', (float)$filter['fbudget_rs_min']);
        if (isset($filter['fbudget_rs_max'])) $q->where('fbudget_rs <=', (float)$filter['fbudget_rs_max']);

        $cnt_row = $q->get('init_call')->row_array();
        $member_count = (int)($cnt_row['cnt'] ?? 0);

        $summary = array('member_count' => $member_count, 'filter' => $filter);

        $this->db->insert('cohort_snapshot', array(
            'cohort_id'     => $cohort_id,
            'snapshot_date' => date('Y-m-d'),
            'member_count'  => $member_count,
            'summary_json'  => json_encode($summary),
            'captured_at'   => date('Y-m-d H:i:s'),
        ));

        $this->_json(array('ok' => true, 'cohort_id' => $cohort_id, 'member_count' => $member_count, 'snapshot_date' => date('Y-m-d')));
    }

    // ================================================================
    // Trends methods (merged from the former Trends class)
    // ================================================================

    // ------------------------------------------------------------------ GET /m075_cohort_and_trends_viewer/series

    public function series()
    {
        $metric_code = trim((string)$this->input->get('metric_code'));
        $scope_type  = trim((string)$this->input->get('scope_type')) ?: 'org';
        $scope_uid   = (int)$this->input->get('scope_uid');
        $from        = trim((string)$this->input->get('from')) ?: date('Y-m-d', strtotime('-30 days'));
        $to          = trim((string)$this->input->get('to'))   ?: date('Y-m-d');

        if (!$metric_code) {
            $this->_json(array('ok' => false, 'error' => 'missing_metric_code'), 400);
            return;
        }

        $rows = $this->db->where('metric_code', $metric_code)
                         ->where('scope_type', $scope_type)
                         ->where('scope_uid', $scope_uid)
                         ->where('value_date >=', $from)
                         ->where('value_date <=', $to)
                         ->order_by('value_date', 'ASC')
                         ->get('trend_value')
                         ->result_array();

        $this->_json(array(
            'ok'          => true,
            'metric_code' => $metric_code,
            'scope_type'  => $scope_type,
            'scope_uid'   => $scope_uid,
            'from'        => $from,
            'to'          => $to,
            'series'      => $rows ?: array(),
        ));
    }

    // ------------------------------------------------------------------ POST /m075_cohort_and_trends_viewer/refresh

    public function refresh()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $today      = date('Y-m-d');
        $now        = date('Y-m-d H:i:s');
        $metrics    = $this->db->get('trend_metric')->result_array();
        $refreshed  = 0;

        foreach ($metrics as $metric) {
            $code = $metric['metric_code'];
            $sql  = $metric['formula_sql'];

            $sql = str_replace(':value_date', $this->db->escape($today), $sql);

            $result = @$this->db->query($sql);
            $value  = 0;
            if ($result) {
                $row = $result->row_array();
                if ($row) {
                    $value = floatval(reset($row));
                }
            }

            $existing = $this->db->get_where('trend_value', array(
                'metric_code' => $code,
                'scope_type'  => 'org',
                'scope_uid'   => 0,
                'value_date'  => $today,
            ))->row_array();

            if ($existing) {
                $this->db->where('id', $existing['id'])->update('trend_value', array(
                    'value'       => $value,
                    'captured_at' => $now,
                ));
            } else {
                $this->db->insert('trend_value', array(
                    'metric_code' => $code,
                    'scope_type'  => 'org',
                    'scope_uid'   => 0,
                    'value_date'  => $today,
                    'value'       => $value,
                    'captured_at' => $now,
                ));
            }
            $refreshed++;
        }

        $this->_json(array('ok' => true, 'refreshed' => $refreshed, 'date' => $today));
    }

    // ------------------------------------------------------------------ GET /m075_cohort_and_trends_viewer/dashboard

    public function dashboard()
    {
        $uid   = (int)($this->input->get('uid') ?: 0);
        $from  = date('Y-m-d', strtotime('-30 days'));
        $to    = date('Y-m-d');
        $codes = array_keys($this->_metrics_30d_demo);

        $result = array();

        foreach ($codes as $code) {
            $meta = $this->_metrics_30d_demo[$code];

            $rows = $this->db->where('metric_code', $code)
                             ->where('scope_type', 'org')
                             ->where('scope_uid', 0)
                             ->where('value_date >=', $from)
                             ->where('value_date <=', $to)
                             ->order_by('value_date', 'ASC')
                             ->get('trend_value')
                             ->result_array();

            $user_rows = array();
            if ($uid) {
                $user_rows = $this->db->where('metric_code', $code)
                                      ->where('scope_type', 'user')
                                      ->where('scope_uid', $uid)
                                      ->where('value_date >=', $from)
                                      ->where('value_date <=', $to)
                                      ->order_by('value_date', 'ASC')
                                      ->get('trend_value')
                                      ->result_array();
            }

            $latest_value = count($rows) > 0 ? floatval($rows[count($rows) - 1]['value']) : 0;

            $result[$code] = array(
                'label'         => $meta['label'],
                'unit'          => $meta['unit'],
                'latest_value'  => $latest_value,
                'org_series'    => $rows ?: array(),
                'user_series'   => $user_rows ?: array(),
            );
        }

        $this->_json(array(
            'ok'        => true,
            'uid'       => $uid,
            'from'      => $from,
            'to'        => $to,
            'dashboard' => $result,
        ));
    }

    // ------------------------------------------------------------------ GET /api/cohort/list (alias)

    public function list_cohorts()
    {
        return $this->list();
    }

}
