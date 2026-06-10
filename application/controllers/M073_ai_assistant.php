<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M073 AI Assistant Controller (lead scoring + deal health)
 * Routes (no /api/ prefix):
 *   POST /ai/score_lead
 *   POST /ai/deal_health
 *   GET  /ai/recommendations_for_user
 *   POST /ai/refresh_all_for_user
 *   GET  /ai/explain
 */
class M073_ai_assistant extends CI_Controller
{
    private $_bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Scoring weights
    private $_weights = array(
        'days_since_last_touch' => -0.8,   // negative: more days = lower score
        'meetings_count'        =>  6.0,
        'mom_count'             =>  4.0,
        'fbudget_rs_lakh'       =>  0.5,   // per lakh
        'is_active_cstatus'     => 15.0,
        'has_dm_designation'    => 10.0,
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->_check_auth();
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



    // ------------------------------------------------------------------ auth

    private function _check_auth()
    {
        $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$header) {
            $header = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '';
        }
        if (strpos($header, 'Bearer ') !== 0) {
            $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401);
            exit;
        }
        $token = trim(substr($header, 7));
        if ($token !== $this->_bearer) {
            $this->_json(array('ok' => false, 'error' => 'forbidden'), 403);
            exit;
        }
    }

    // ------------------------------------------------------------------ helpers

    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    // ------------------------------------------------------------------ lead data loader

    private function _load_lead($cid_id)
    {
        return $this->db->get_where('init_call', array('cid_id' => $cid_id))->row_array();
    }

    // ------------------------------------------------------------------ POST /ai/score_lead

    public function score_lead()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }
        $cid_id = (int)$this->input->post('cid_id');
        if (!$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400);
            return;
        }

        // Check 24h cache
        $cached = $this->db->get_where('ai_lead_score', array('cid_id' => $cid_id))->row_array();
        if ($cached && strtotime($cached['valid_until']) > time()) {
            $cached['cached'] = true;
            $this->_json(array('ok' => true, 'lead_score' => $cached));
            return;
        }

        $lead = $this->_load_lead($cid_id);
        if (!$lead) {
            $this->_json(array('ok' => false, 'error' => 'lead_not_found'), 404);
            return;
        }

        // Gather factors
        $last_touch = $this->db->select('MAX(created_at) AS lt')
                               ->get_where('mom', array('cid_id' => $cid_id))->row_array();
        $last_touch_date = !empty($last_touch['lt']) ? $last_touch['lt'] : $lead['created_at'];
        $days_since = max(0, floor((time() - strtotime($last_touch_date)) / 86400));

        $meetings = (int)$this->db->where('cid_id', $cid_id)->count_all_results('meeting');
        $moms     = (int)$this->db->where('cid_id', $cid_id)->count_all_results('mom');

        $fbudget_lakh = floatval($lead['fbudget_rs'] ?? 0) / 100000;
        $cstatus      = (int)($lead['cstatus'] ?? 0);
        $is_active    = ($cstatus >= 2 && $cstatus <= 10) ? 1 : 0;

        $dm_keywords  = array('principal', 'director', 'head', 'chairman', 'ceo', 'coo', 'owner');
        $designation  = strtolower($lead['designation'] ?? '');
        $has_dm       = 0;
        foreach ($dm_keywords as $kw) {
            if (strpos($designation, $kw) !== false) { $has_dm = 1; break; }
        }

        // Compute score (0-100 clamp)
        $raw = 50
             + ($days_since  * $this->_weights['days_since_last_touch'])
             + ($meetings    * $this->_weights['meetings_count'])
             + ($moms        * $this->_weights['mom_count'])
             + ($fbudget_lakh * $this->_weights['fbudget_rs_lakh'])
             + ($is_active   * $this->_weights['is_active_cstatus'])
             + ($has_dm      * $this->_weights['has_dm_designation']);

        $score = (int)max(0, min(100, round($raw)));

        if ($score >= 75)      $band = 'hot';
        elseif ($score >= 50)  $band = 'warm';
        elseif ($score >= 25)  $band = 'cool';
        else                   $band = 'cold';

        $factors = array(
            'days_since_last_touch' => $days_since,
            'meetings_count'        => $meetings,
            'mom_count'             => $moms,
            'fbudget_rs'            => $lead['fbudget_rs'] ?? 0,
            'cstatus'               => $cstatus,
            'has_dm_designation'    => $has_dm,
        );

        $reasoning = "Score: {$score}/100 ({$band}). "
                   . "Days since last touch: {$days_since} (penalty). "
                   . "Meetings: {$meetings}, MOMs: {$moms} (rewards). "
                   . "Budget Rs {$lead['fbudget_rs']}. "
                   . "DM designation: " . ($has_dm ? 'yes' : 'no') . ".";

        $now         = date('Y-m-d H:i:s');
        $valid_until = date('Y-m-d H:i:s', time() + 86400);

        $record = array(
            'cid_id'        => $cid_id,
            'score'         => $score,
            'score_band'    => $band,
            'reasoning'     => $reasoning,
            'factors_json'  => json_encode($factors),
            'model_version' => 'rule_v1',
            'computed_at'   => $now,
            'valid_until'   => $valid_until,
        );

        // Upsert
        if ($cached) {
            $this->db->where('cid_id', $cid_id)->update('ai_lead_score', $record);
        } else {
            $this->db->insert('ai_lead_score', $record);
        }

        $this->_json(array('ok' => true, 'lead_score' => $record));
    }

    // ------------------------------------------------------------------ POST /ai/deal_health

    public function deal_health()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }
        $cid_id = (int)$this->input->post('cid_id');
        if (!$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400);
            return;
        }

        $lead = $this->_load_lead($cid_id);
        if (!$lead) {
            $this->_json(array('ok' => false, 'error' => 'lead_not_found'), 404);
            return;
        }

        $cstatus = (int)($lead['cstatus'] ?? 0);

        // Last touch
        $lt = $this->db->select('MAX(created_at) AS lt')->get_where('mom', array('cid_id' => $cid_id))->row_array();
        $last_touch_date = !empty($lt['lt']) ? $lt['lt'] : $lead['created_at'];
        $days_no_touch   = max(0, floor((time() - strtotime($last_touch_date)) / 86400));

        // Pending MOMs
        $mom_pending = (int)$this->db->where('cid_id', $cid_id)
                                     ->where('status', 'pending')
                                     ->count_all_results('mom');

        $risk_factors  = array();
        $actions       = array();
        $days_estimate = null;
        $win_pct       = null;

        if ($cstatus >= 6 && ($days_no_touch > 14 || $mom_pending > 0)) {
            $band = 'red';
            if ($days_no_touch > 14) {
                $risk_factors[] = "No touch in {$days_no_touch} days.";
                $actions[]      = 'Call or visit the contact immediately.';
            }
            if ($mom_pending > 0) {
                $risk_factors[] = "{$mom_pending} MOM(s) still pending.";
                $actions[]      = 'Close pending MOMs before next follow-up.';
            }
            $win_pct       = 20.0;
            $days_estimate = 30;
        } elseif ($days_no_touch >= 7) {
            $band           = 'amber';
            $risk_factors[] = "No touch in {$days_no_touch} days.";
            $actions[]      = 'Schedule a follow-up call this week.';
            $win_pct        = 45.0;
            $days_estimate  = 14;
        } else {
            $band          = 'green';
            $win_pct       = 72.0;
            $days_estimate = 7;
        }

        $now = date('Y-m-d H:i:s');
        $record = array(
            'cid_id'                  => $cid_id,
            'health_band'             => $band,
            'risk_factors'            => implode(' ', $risk_factors),
            'recommended_actions'     => implode(' ', $actions),
            'days_to_close_estimate'  => $days_estimate,
            'win_probability_pct'     => $win_pct,
            'computed_at'             => $now,
        );

        $exists = $this->db->get_where('ai_deal_health', array('cid_id' => $cid_id))->row_array();
        if ($exists) {
            $this->db->where('cid_id', $cid_id)->update('ai_deal_health', $record);
        } else {
            $this->db->insert('ai_deal_health', $record);
        }

        $this->_json(array('ok' => true, 'deal_health' => $record));
    }

    // ------------------------------------------------------------------ GET /ai/recommendations_for_user

    public function recommendations_for_user()
    {
        $uid = (int)($this->input->get('uid') ?: 0);
        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }
        $rows = $this->db->where('uid', $uid)
                         ->where_in('status', array('new', 'seen'))
                         ->order_by('priority', 'ASC')
                         ->order_by('created_at', 'DESC')
                         ->limit(10)
                         ->get('ai_recommendation')
                         ->result_array();
        $this->_json(array('ok' => true, 'recommendations' => $rows ?: array()));
    }

    // ------------------------------------------------------------------ POST /ai/refresh_all_for_user  (cron)

    public function refresh_all_for_user()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }
        $uid = (int)$this->input->post('uid');
        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }

        // Get all leads for user
        $leads = $this->db->select('cid_id, cstatus')->get_where('init_call', array('uid' => $uid))->result_array();
        $refreshed = 0;
        $red_count = 0;

        // Clear old new recommendations for this user
        $this->db->where('uid', $uid)->where('status', 'new')->delete('ai_recommendation');

        foreach ($leads as $lead) {
            $cid_id = (int)$lead['cid_id'];

            // Score
            $_POST['cid_id'] = $cid_id;
            // Inline score computation (reuse logic to avoid HTTP overhead)
            $last_touch = $this->db->select('MAX(created_at) AS lt')->get_where('mom', array('cid_id' => $cid_id))->row_array();
            $last_touch_date = !empty($last_touch['lt']) ? $last_touch['lt'] : date('Y-m-d H:i:s');
            $days_since = max(0, floor((time() - strtotime($last_touch_date)) / 86400));
            $meetings   = (int)$this->db->where('cid_id', $cid_id)->count_all_results('meeting');
            $moms       = (int)$this->db->where('cid_id', $cid_id)->count_all_results('mom');
            $full_lead  = $this->db->get_where('init_call', array('cid_id' => $cid_id))->row_array();
            $fbudget_lakh = floatval($full_lead['fbudget_rs'] ?? 0) / 100000;
            $cstatus      = (int)($full_lead['cstatus'] ?? 0);
            $is_active    = ($cstatus >= 2 && $cstatus <= 10) ? 1 : 0;
            $designation  = strtolower($full_lead['designation'] ?? '');
            $has_dm       = 0;
            foreach (array('principal','director','head','chairman','ceo','coo','owner') as $kw) {
                if (strpos($designation, $kw) !== false) { $has_dm = 1; break; }
            }
            $raw   = 50 + ($days_since * -0.8) + ($meetings * 6) + ($moms * 4) + ($fbudget_lakh * 0.5) + ($is_active * 15) + ($has_dm * 10);
            $score = (int)max(0, min(100, round($raw)));
            $band  = ($score >= 75) ? 'hot' : (($score >= 50) ? 'warm' : (($score >= 25) ? 'cool' : 'cold'));

            // Deal health
            $mom_pending = (int)$this->db->where('cid_id', $cid_id)->where('status', 'pending')->count_all_results('mom');
            $health = 'green';
            if ($cstatus >= 6 && ($days_since > 14 || $mom_pending > 0)) $health = 'red';
            elseif ($days_since >= 7) $health = 'amber';

            // Upsert score
            $score_rec = array(
                'cid_id'        => $cid_id,
                'score'         => $score,
                'score_band'    => $band,
                'model_version' => 'rule_v1',
                'computed_at'   => date('Y-m-d H:i:s'),
                'valid_until'   => date('Y-m-d H:i:s', time() + 86400),
            );
            $exists = $this->db->get_where('ai_lead_score', array('cid_id' => $cid_id))->row_array();
            if ($exists) { $this->db->where('cid_id', $cid_id)->update('ai_lead_score', $score_rec); }
            else         { $this->db->insert('ai_lead_score', $score_rec); }

            // Build recommendations
            if ($health === 'red') {
                $red_count++;
                $this->db->insert('ai_recommendation', array(
                    'uid'                 => $uid,
                    'recommendation_type' => 'deal_at_risk',
                    'cid_id'              => $cid_id,
                    'payload_json'        => json_encode(array('reason' => 'Red health: no touch over 14 days or MOM pending', 'health' => $health)),
                    'priority'            => 1,
                    'status'              => 'new',
                    'created_at'          => date('Y-m-d H:i:s'),
                ));
            }
            if ($days_since >= 5 && $days_since < 14) {
                $this->db->insert('ai_recommendation', array(
                    'uid'                 => $uid,
                    'recommendation_type' => 'followup_overdue',
                    'cid_id'              => $cid_id,
                    'payload_json'        => json_encode(array('days_since' => $days_since)),
                    'priority'            => 2,
                    'status'              => 'new',
                    'created_at'          => date('Y-m-d H:i:s'),
                ));
            }
            if ($band === 'hot') {
                $this->db->insert('ai_recommendation', array(
                    'uid'                 => $uid,
                    'recommendation_type' => 'lead_to_focus',
                    'cid_id'              => $cid_id,
                    'payload_json'        => json_encode(array('score' => $score, 'band' => $band)),
                    'priority'            => 3,
                    'status'              => 'new',
                    'created_at'          => date('Y-m-d H:i:s'),
                ));
            }

            $refreshed++;
        }

        $this->_json(array(
            'ok'        => true,
            'refreshed' => $refreshed,
            'red_deals' => $red_count,
            'message'   => "Refreshed {$refreshed} leads for user {$uid}.",
        ));
    }

    // ------------------------------------------------------------------ GET /ai/explain

    public function explain()
    {
        $cid_id = (int)($this->input->get('cid_id') ?: 0);
        if (!$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400);
            return;
        }
        $score_row  = $this->db->get_where('ai_lead_score',  array('cid_id' => $cid_id))->row_array();
        $health_row = $this->db->get_where('ai_deal_health', array('cid_id' => $cid_id))->row_array();

        if (!$score_row && !$health_row) {
            $this->_json(array('ok' => false, 'error' => 'no_data_run_score_first'), 404);
            return;
        }

        $factors = array();
        if (!empty($score_row['factors_json'])) {
            $factors = json_decode($score_row['factors_json'], true) ?: array();
        }

        $this->_json(array(
            'ok'           => true,
            'cid_id'       => $cid_id,
            'score'        => $score_row['score']       ?? null,
            'band'         => $score_row['score_band']  ?? null,
            'reasoning'    => $score_row['reasoning']   ?? '',
            'factors'      => $factors,
            'health_band'  => $health_row['health_band']         ?? null,
            'risk_factors' => $health_row['risk_factors']        ?? '',
            'actions'      => $health_row['recommended_actions'] ?? '',
            'win_pct'      => $health_row['win_probability_pct'] ?? null,
        ));
    }
}
