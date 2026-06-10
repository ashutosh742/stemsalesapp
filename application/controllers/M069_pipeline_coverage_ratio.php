<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M069 Pipeline Coverage Ratio
 * File: application/controllers/Coverage.php
 * CodeIgniter 3 controller.
 * Routes (no /api/ prefix):
 *   GET  /coverage/widget_for_user
 *   GET  /coverage/widget_for_team
 *   POST /coverage/refresh_snapshot
 *   GET  /coverage/history
 *   POST /coverage/config_set
 */
class M069_pipeline_coverage_ratio extends CI_Controller
{
    private $_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------
    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }
    private function _auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        // Load custom config if not loaded
        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(array('ok'=>false,'error'=>'unauthorised')));
            return false;
        }
        return true;
    }



    // ------------------------------------------------------------------
    // Helper: compute coverage metrics for one scope
    // ------------------------------------------------------------------
    private function _compute_coverage($scope_type, $scope_uid)
    {
        // Get config for scope; fall back to org-wide
        $cfg = $this->db->get_where('pipeline_coverage_config',
            array('scope_type' => $scope_type, 'scope_uid' => $scope_uid)
        )->row_array();

        if (!$cfg) {
            $cfg = $this->db->get_where('pipeline_coverage_config',
                array('scope_type' => 'org', 'scope_uid' => 0)
            )->row_array();
        }

        $target_rs  = $cfg ? (float)$cfg['target_rs']         : 50000000;
        $ratio_min  = $cfg ? (float)$cfg['healthy_ratio_min'] : 3.00;
        $ratio_max  = $cfg ? (float)$cfg['healthy_ratio_max'] : 5.00;
        $period_s   = $cfg ? $cfg['period_start']             : date('Y-m-d', strtotime('-30 days'));
        $period_e   = $cfg ? $cfg['period_end']               : date('Y-m-d', strtotime('+60 days'));

        // Sum pipeline from lead_forecast_tag joined to potential_rs
        if ($scope_type === 'org') {
            $sql = "
                SELECT COALESCE(SUM(ic.fbudget), 0) AS pipeline_rs
                FROM lead_forecast_tag lft
                JOIN init_call ic ON ic.id = lft.cid_id
                WHERE lft.bucket IN ('worst','commit','best','pipeline')
                  AND (lft.expected_close_date IS NULL
                       OR lft.expected_close_date BETWEEN ? AND ?)
            ";
            $row = $this->db->query($sql, array($period_s, $period_e))->row_array();
        } else {
            $sql = "
                SELECT COALESCE(SUM(ic.fbudget), 0) AS pipeline_rs
                FROM lead_forecast_tag lft
                JOIN init_call ic ON ic.id = lft.cid_id
                WHERE lft.tagged_by_uid = ?
                  AND lft.bucket IN ('worst','commit','best','pipeline')
                  AND (lft.expected_close_date IS NULL
                       OR lft.expected_close_date BETWEEN ? AND ?)
            ";
            $row = $this->db->query($sql, array($scope_uid, $period_s, $period_e))->row_array();
        }

        $pipeline_rs = (float)$row['pipeline_rs'];
        $ratio       = $target_rs > 0 ? round($pipeline_rs / $target_rs, 2) : 0;

        if ($ratio < $ratio_min)      $band = 'thin';
        elseif ($ratio > $ratio_max)  $band = 'padded';
        else                          $band = 'healthy';

        return array(
            'scope_type'  => $scope_type,
            'scope_uid'   => $scope_uid,
            'pipeline_rs' => $pipeline_rs,
            'target_rs'   => $target_rs,
            'ratio'       => $ratio,
            'band'        => $band,
            'period_start'=> $period_s,
            'period_end'  => $period_e,
        );
    }

    // ------------------------------------------------------------------
    // GET /coverage/widget_for_user?uid=X
    // Returns ratio + band + Rs totals + delta-vs-yesterday.
    // ------------------------------------------------------------------
    public function widget_for_user()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $uid = (int)$this->input->get('uid');
        if (!$uid) { $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400); return; }

        // Determine scope_type from user table
        $user_row   = $this->db->get_where('user', array('uid' => $uid))->row_array();
        $scope_type = 'bd';
        if ($user_row) {
            $t = (int)$user_row['type_id'];
            if ($t === 4)     $scope_type = 'cm';
            elseif ($t === 5) $scope_type = 'rm';
        }

        $current = $this->_compute_coverage($scope_type, $uid);

        // Delta vs yesterday from snapshot
        $yesterday  = date('Y-m-d', strtotime('-1 day'));
        $snap_prev  = $this->db->select('ratio')
                               ->where('scope_type', $scope_type)
                               ->where('scope_uid', $uid)
                               ->where('snapshot_date', $yesterday)
                               ->get('pipeline_coverage_snapshot')->row_array();

        $delta_ratio = $snap_prev ? round($current['ratio'] - (float)$snap_prev['ratio'], 2) : null;

        $current['delta_ratio_vs_yesterday'] = $delta_ratio;
        $this->_json(array('ok' => true, 'widget' => $current));
    }

    // ------------------------------------------------------------------
    // GET /coverage/widget_for_team?cm_uid=X
    // Aggregates pipeline coverage for all BD reports of a CM.
    // ------------------------------------------------------------------
    public function widget_for_team()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $cm_uid = (int)$this->input->get('cm_uid');
        if (!$cm_uid) { $this->_json(array('ok' => false, 'error' => 'missing_cm_uid'), 400); return; }

        // user table has no cluster_id; cluster_master_id is the correct column.
        $reports = $this->db->get_where('user', array('cluster_master_id' => $cm_uid, 'type_id' => 3))->result_array();

        $team_pipeline_rs = 0;
        $members = array();
        foreach ($reports as $r) {
            $w = $this->_compute_coverage('bd', $r['uid']);
            $team_pipeline_rs += $w['pipeline_rs'];
            $members[] = array(
                'uid'         => (int)$r['uid'],
                'name'        => trim($r['firstname'] . ' ' . $r['lastname']),
                'pipeline_rs' => $w['pipeline_rs'],
                'target_rs'   => $w['target_rs'],
                'ratio'       => $w['ratio'],
                'band'        => $w['band'],
            );
        }

        $this->_json(array(
            'ok'               => true,
            'cm_uid'           => $cm_uid,
            'team_pipeline_rs' => $team_pipeline_rs,
            'members'          => $members,
        ));
    }

    // ------------------------------------------------------------------
    // POST /coverage/refresh_snapshot
    // Cron: recomputes all scopes and writes pipeline_coverage_snapshot.
    // ------------------------------------------------------------------
    public function refresh_snapshot()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }
        if ($this->input->method() !== 'post') { $this->_json(array('ok' => false, 'error' => 'method_not_allowed'), 405); return; }

        $today   = date('Y-m-d');
        $saved   = 0;

        // Get all distinct scope combos from config
        $configs = $this->db->get('pipeline_coverage_config')->result_array();

        foreach ($configs as $cfg) {
            $c = $this->_compute_coverage($cfg['scope_type'], (int)$cfg['scope_uid']);
            $snap = array(
                'scope_type'    => $c['scope_type'],
                'scope_uid'     => $c['scope_uid'],
                'snapshot_date' => $today,
                'pipeline_rs'   => $c['pipeline_rs'],
                'target_rs'     => $c['target_rs'],
                'ratio'         => $c['ratio'],
                'band'          => $c['band'],
                'captured_at'   => date('Y-m-d H:i:s'),
            );
            $this->db->insert('pipeline_coverage_snapshot', $snap);
            $saved++;
        }

        // Also snapshot each active BD/CM user
        $all_users = $this->db->select('uid, type_id')->where_in('type_id', array(3, 4, 5))->get('user')->result_array();
        foreach ($all_users as $u) {
            $type_map   = array(3 => 'bd', 4 => 'cm', 5 => 'rm');
            $scope_type = $type_map[(int)$u['type_id']] ?? 'bd';
            $c          = $this->_compute_coverage($scope_type, (int)$u['uid']);
            $snap       = array(
                'scope_type'    => $c['scope_type'],
                'scope_uid'     => $c['scope_uid'],
                'snapshot_date' => $today,
                'pipeline_rs'   => $c['pipeline_rs'],
                'target_rs'     => $c['target_rs'],
                'ratio'         => $c['ratio'],
                'band'          => $c['band'],
                'captured_at'   => date('Y-m-d H:i:s'),
            );
            $this->db->insert('pipeline_coverage_snapshot', $snap);
            $saved++;
        }

        $this->_json(array('ok' => true, 'snapshot_date' => $today, 'rows_written' => $saved));
    }

    // ------------------------------------------------------------------
    // GET /coverage/history?scope_uid=X&last_n_days=N
    // Returns ratio trend for the last N days.
    // ------------------------------------------------------------------
    public function history()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $scope_uid  = (int)$this->input->get('scope_uid');
        $last_n     = max(1, min(365, (int)($this->input->get('last_n_days') ?: 14)));

        if (!$scope_uid && $scope_uid !== 0) { $this->_json(array('ok' => false, 'error' => 'missing_scope_uid'), 400); return; }

        $rows = $this->db->select('snapshot_date, pipeline_rs, target_rs, ratio, band')
                         ->where('scope_uid', $scope_uid)
                         ->order_by('snapshot_date', 'DESC')
                         ->limit($last_n)
                         ->get('pipeline_coverage_snapshot')->result_array();

        $trend = array_map(function($r) {
            return array(
                'date'        => $r['snapshot_date'],
                'pipeline_rs' => (float)$r['pipeline_rs'],
                'target_rs'   => (float)$r['target_rs'],
                'ratio'       => (float)$r['ratio'],
                'band'        => $r['band'],
            );
        }, array_reverse($rows));

        $this->_json(array('ok' => true, 'scope_uid' => $scope_uid, 'days_returned' => count($trend), 'trend' => $trend));
    }

    // ------------------------------------------------------------------
    // POST /coverage/config_set
    // Admin only: set target_rs + period for a scope.
    // Body: scope_uid, scope_type, target_rs, period_start, period_end
    // ------------------------------------------------------------------
    public function config_set()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }
        if ($this->input->method() !== 'post') { $this->_json(array('ok' => false, 'error' => 'method_not_allowed'), 405); return; }

        $admin_uid    = (int)$this->input->post('admin_uid');
        $scope_type   = trim((string)$this->input->post('scope_type'));
        $scope_uid    = (int)$this->input->post('scope_uid');
        $target_rs    = (float)$this->input->post('target_rs');
        $period_start = trim((string)$this->input->post('period_start'));
        $period_end   = trim((string)$this->input->post('period_end'));

        // Admin check: type_id 1 (Super Admin)
        if ($admin_uid) {
            $u = $this->db->get_where('user', array('uid' => $admin_uid))->row_array();
            if (!$u || (int)$u['type_id'] !== 1) {
                $this->_json(array('ok' => false, 'error' => 'not_authorised'), 403); return;
            }
        }

        $valid_scopes = array('bd', 'cm', 'rm', 'org');
        if (!in_array($scope_type, $valid_scopes)) { $this->_json(array('ok' => false, 'error' => 'invalid_scope_type'), 400); return; }
        if ($target_rs <= 0)                       { $this->_json(array('ok' => false, 'error' => 'invalid_target_rs'), 400); return; }

        $data = array(
            'scope_type'   => $scope_type,
            'scope_uid'    => $scope_uid,
            'target_rs'    => $target_rs,
            'period_start' => $period_start ?: date('Y-m-d'),
            'period_end'   => $period_end   ?: date('Y-m-d', strtotime('+90 days')),
        );

        $existing = $this->db->get_where('pipeline_coverage_config',
            array('scope_type' => $scope_type, 'scope_uid' => $scope_uid)
        )->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('pipeline_coverage_config', $data);
            $cfg_id = $existing['id'];
            $action = 'updated';
        } else {
            $this->db->insert('pipeline_coverage_config', $data);
            $cfg_id = $this->db->insert_id();
            $action = 'created';
        }

        $this->_json(array('ok' => true, 'config_id' => $cfg_id, 'action' => $action));
    }
}
/* End of Coverage.php */
