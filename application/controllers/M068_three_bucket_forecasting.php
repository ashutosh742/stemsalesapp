<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M068 Three-Bucket Forecasting
 * File: application/controllers/Forecast.php
 * CodeIgniter 3 controller.
 * Routes (no /api/ prefix):
 *   POST /forecast/tag_lead
 *   GET  /forecast/summary_for_user
 *   GET  /forecast/team_rollup
 *   POST /forecast/snapshot_weekly_run
 *   GET  /forecast/history
 */
class M068_three_bucket_forecasting extends CI_Controller
{
    // Valid bearer token
    private $_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------
    // Helper: emit JSON and stop
    // ------------------------------------------------------------------
    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }

    // ------------------------------------------------------------------
    // Helper: check Authorization: Bearer header
    // ------------------------------------------------------------------
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
    // Helper: get Rs totals and lead lists for one uid in one week
    // ------------------------------------------------------------------
    private function _user_week_summary($uid, $week_date)
    {
        // week_date: any date; normalize to the Friday of that week
        $friday = date('Y-m-d', strtotime('friday', strtotime($week_date)));

        // All tagged leads for this user with their fee
        // NOTE: init_call PK is 'id', not 'cid_id'. school_name, cid_name, potential_rs
        // are not columns on init_call; using empty strings and ic.fbudget instead.
        $sql = "
            SELECT
                lft.cid_id,
                lft.bucket,
                lft.expected_close_date,
                lft.notes,
                '' AS school_name,
                '' AS cid_name,
                COALESCE(ic.fbudget, 0)      AS potential_rs
            FROM lead_forecast_tag lft
            LEFT JOIN init_call ic ON ic.id = lft.cid_id
            WHERE lft.tagged_by_uid = ?
              AND (lft.expected_close_date IS NULL OR lft.expected_close_date >= ?)
        ";
        $rows = $this->db->query($sql, array($uid, $friday))->result_array();

        $buckets = array(
            'worst'    => array('total_rs' => 0, 'leads' => array()),
            'commit'   => array('total_rs' => 0, 'leads' => array()),
            'best'     => array('total_rs' => 0, 'leads' => array()),
            'pipeline' => array('total_rs' => 0, 'leads' => array()),
        );

        foreach ($rows as $r) {
            $b = isset($buckets[$r['bucket']]) ? $r['bucket'] : 'pipeline';
            $buckets[$b]['total_rs'] += (float)$r['potential_rs'];
            $buckets[$b]['leads'][]   = array(
                'cid_id'              => (int)$r['cid_id'],
                'cid_name'            => $r['cid_name'],
                'school_name'         => $r['school_name'],
                'potential_rs'        => (float)$r['potential_rs'],
                'expected_close_date' => $r['expected_close_date'],
                'notes'               => $r['notes'],
            );
        }

        return array(
            'uid'         => (int)$uid,
            'week_date'   => $friday,
            'worst_rs'    => $buckets['worst']['total_rs'],
            'commit_rs'   => $buckets['commit']['total_rs'],
            'best_rs'     => $buckets['best']['total_rs'],
            'pipeline_rs' => $buckets['pipeline']['total_rs'],
            'buckets'     => $buckets,
        );
    }

    // ------------------------------------------------------------------
    // POST /forecast/tag_lead
    // Upserts a forecast bucket tag for a lead.
    // Body: cid_id, bucket, expected_close (Y-m-d), notes
    // ------------------------------------------------------------------
    public function tag_lead()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }
        if ($this->input->method() !== 'post') { $this->_json(array('ok' => false, 'error' => 'method_not_allowed'), 405); return; }

        $cid_id   = (int)$this->input->post('cid_id');
        $bucket   = trim((string)$this->input->post('bucket'));
        $tagger   = (int)$this->input->post('tagged_by_uid');
        $close    = $this->input->post('expected_close') ?: null;
        $notes    = trim((string)$this->input->post('notes'));

        $valid_buckets = array('worst', 'commit', 'best', 'pipeline', 'omitted');
        if (!$cid_id)                             { $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400); return; }
        if (!in_array($bucket, $valid_buckets))   { $this->_json(array('ok' => false, 'error' => 'invalid_bucket'), 400); return; }
        if (!$tagger)                             { $this->_json(array('ok' => false, 'error' => 'missing_tagged_by_uid'), 400); return; }

        $existing = $this->db->get_where('lead_forecast_tag', array('cid_id' => $cid_id))->row_array();

        $data = array(
            'cid_id'               => $cid_id,
            'bucket'               => $bucket,
            'tagged_by_uid'        => $tagger,
            'tagged_at'            => date('Y-m-d H:i:s'),
            'expected_close_date'  => $close,
            'notes'                => $notes ?: null,
        );

        if ($existing) {
            $this->db->where('cid_id', $cid_id)->update('lead_forecast_tag', $data);
            $tag_id = $existing['id'];
            $action = 'updated';
        } else {
            $this->db->insert('lead_forecast_tag', $data);
            $tag_id = $this->db->insert_id();
            $action = 'created';
        }

        $this->_json(array('ok' => true, 'tag_id' => $tag_id, 'action' => $action, 'bucket' => $bucket));
    }

    // ------------------------------------------------------------------
    // GET /forecast/summary_for_user?uid=X&week=YYYY-MM-DD
    // Returns worst/commit/best/pipeline Rs totals + lead lists.
    // ------------------------------------------------------------------
    public function summary_for_user()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $uid  = (int)$this->input->get('uid');
        $week = trim((string)$this->input->get('week')) ?: date('Y-m-d');

        if (!$uid) { $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400); return; }

        $summary = $this->_user_week_summary($uid, $week);
        $this->_json(array('ok' => true, 'summary' => $summary));
    }

    // ------------------------------------------------------------------
    // GET /forecast/team_rollup?cm_uid=X  OR  ?rm_uid=X
    // Aggregates forecast summary for all direct reports.
    // ------------------------------------------------------------------
    public function team_rollup()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $cm_uid = (int)$this->input->get('cm_uid');
        $rm_uid = (int)$this->input->get('rm_uid');
        $week   = trim((string)$this->input->get('week')) ?: date('Y-m-d');

        if (!$cm_uid && !$rm_uid) { $this->_json(array('ok' => false, 'error' => 'missing_cm_uid_or_rm_uid'), 400); return; }

        // Determine report type
        if ($cm_uid) {
            // user table has no cluster_id; cluster_master_id is the correct column.
            $reports = $this->db->get_where('user', array('cluster_master_id' => $cm_uid, 'type_id' => 3))->result_array();
            $manager_uid  = $cm_uid;
            $manager_role = 'cm';
        } else {
            $reports = $this->db->get_where('user', array('rm_uid' => $rm_uid))->result_array();
            $manager_uid  = $rm_uid;
            $manager_role = 'rm';
        }

        $rollup = array(
            'manager_uid'  => $manager_uid,
            'manager_role' => $manager_role,
            'week_date'    => $week,
            'team_worst_rs'    => 0,
            'team_commit_rs'   => 0,
            'team_best_rs'     => 0,
            'team_pipeline_rs' => 0,
            'members'          => array(),
        );

        foreach ($reports as $r) {
            $s = $this->_user_week_summary($r['uid'], $week);
            $rollup['team_worst_rs']    += $s['worst_rs'];
            $rollup['team_commit_rs']   += $s['commit_rs'];
            $rollup['team_best_rs']     += $s['best_rs'];
            $rollup['team_pipeline_rs'] += $s['pipeline_rs'];
            $rollup['members'][] = array(
                'uid'         => $s['uid'],
                'name'        => trim($r['name']),
                'worst_rs'    => $s['worst_rs'],
                'commit_rs'   => $s['commit_rs'],
                'best_rs'     => $s['best_rs'],
                'pipeline_rs' => $s['pipeline_rs'],
            );
        }

        $this->_json(array('ok' => true, 'rollup' => $rollup));
    }

    // ------------------------------------------------------------------
    // POST /forecast/snapshot_weekly_run
    // Cron endpoint: snapshots Friday EOD data into forecast_snapshot_weekly.
    // ------------------------------------------------------------------
    public function snapshot_weekly_run()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }
        if ($this->input->method() !== 'post') { $this->_json(array('ok' => false, 'error' => 'method_not_allowed'), 405); return; }

        $friday = date('Y-m-d', strtotime('last friday'));

        // Gather all users with forecast tags
        $uids = $this->db->select('DISTINCT tagged_by_uid AS uid')->get('lead_forecast_tag')->result_array();

        $saved = 0;
        foreach ($uids as $u) {
            $s = $this->_user_week_summary($u['uid'], $friday);

            // Determine scope_type from user table
            $user_row = $this->db->get_where('user', array('uid' => $u['uid']))->row_array();
            $scope_type = 'bd';
            if ($user_row) {
                $t = (int)$user_row['type_id'];
                if ($t === 4)      $scope_type = 'cm';
                elseif ($t === 5)  $scope_type = 'rm';
            }

            $snap = array(
                'snapshot_week' => $friday,
                'scope_type'    => $scope_type,
                'scope_uid'     => $u['uid'],
                'worst_rs'      => $s['worst_rs'],
                'commit_rs'     => $s['commit_rs'],
                'best_rs'       => $s['best_rs'],
                'pipeline_rs'   => $s['pipeline_rs'],
                'captured_at'   => date('Y-m-d H:i:s'),
            );

            $exists = $this->db->get_where('forecast_snapshot_weekly',
                array('snapshot_week' => $friday, 'scope_type' => $scope_type, 'scope_uid' => $u['uid'])
            )->row_array();

            if ($exists) {
                $this->db->where('id', $exists['id'])->update('forecast_snapshot_weekly', $snap);
            } else {
                $this->db->insert('forecast_snapshot_weekly', $snap);
            }
            $saved++;
        }

        // Org-wide rollup
        $org_totals = $this->db->select('SUM(worst_rs) AS worst_rs, SUM(commit_rs) AS commit_rs, SUM(best_rs) AS best_rs, SUM(pipeline_rs) AS pipeline_rs')
                               ->where('snapshot_week', $friday)
                               ->where('scope_type !=', 'org')
                               ->get('forecast_snapshot_weekly')->row_array();

        $org_snap = array(
            'snapshot_week' => $friday,
            'scope_type'    => 'org',
            'scope_uid'     => 0,
            'worst_rs'      => (float)$org_totals['worst_rs'],
            'commit_rs'     => (float)$org_totals['commit_rs'],
            'best_rs'       => (float)$org_totals['best_rs'],
            'pipeline_rs'   => (float)$org_totals['pipeline_rs'],
            'captured_at'   => date('Y-m-d H:i:s'),
        );

        $org_exists = $this->db->get_where('forecast_snapshot_weekly',
            array('snapshot_week' => $friday, 'scope_type' => 'org', 'scope_uid' => 0)
        )->row_array();

        if ($org_exists) {
            $this->db->where('id', $org_exists['id'])->update('forecast_snapshot_weekly', $org_snap);
        } else {
            $this->db->insert('forecast_snapshot_weekly', $org_snap);
        }

        $this->_json(array('ok' => true, 'snapshot_week' => $friday, 'users_snapshotted' => $saved));
    }

    // ------------------------------------------------------------------
    // GET /forecast/history?uid=X&last_n_weeks=N
    // Returns trend of buckets over the last N weekly snapshots.
    // ------------------------------------------------------------------
    public function history()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $uid   = (int)$this->input->get('uid');
        $weeks = max(1, min(52, (int)($this->input->get('last_n_weeks') ?: 8)));

        if (!$uid) { $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400); return; }

        $rows = $this->db->select('snapshot_week, worst_rs, commit_rs, best_rs, pipeline_rs')
                         ->where('scope_uid', $uid)
                         ->where('scope_type !=', 'org')
                         ->order_by('snapshot_week', 'DESC')
                         ->limit($weeks)
                         ->get('forecast_snapshot_weekly')->result_array();

        $trend = array_map(function($r) {
            return array(
                'week'        => $r['snapshot_week'],
                'worst_rs'    => (float)$r['worst_rs'],
                'commit_rs'   => (float)$r['commit_rs'],
                'best_rs'     => (float)$r['best_rs'],
                'pipeline_rs' => (float)$r['pipeline_rs'],
            );
        }, array_reverse($rows));

        $this->_json(array('ok' => true, 'uid' => $uid, 'weeks_returned' => count($trend), 'trend' => $trend));
    }
}
/* End of Forecast.php */
