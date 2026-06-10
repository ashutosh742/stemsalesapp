<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M070 Multi-Touch Conversion Attribution
 * File: application/controllers/Attribution.php
 * CodeIgniter 3 controller.
 * Routes (no /api/ prefix):
 *   GET  /attribution/touches_for_lead
 *   GET  /attribution/credit_for_user
 *   POST /attribution/recompute_lead
 *   GET  /attribution/leaderboard
 *   GET  /attribution/model_compare
 */
class M070_multi_touch_attribution extends CI_Controller
{
    private $_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Valid attribution model codes
    private $_models = array('first_touch','last_touch','linear','time_decay','u_shape','w_shape');

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
    // Helper: compute attribution weights for all touches of a lead.
    // Returns array of touches with all 6 weight columns populated.
    // ------------------------------------------------------------------
    private function _compute_weights($cid_id)
    {
        $touches = $this->db->select('*')
                            ->where('cid_id', $cid_id)
                            ->order_by('touch_at', 'ASC')
                            ->get('attribution_touch')->result_array();

        $n = count($touches);
        if ($n === 0) return array();

        // -- First Touch --
        // -- Last Touch  --
        // -- Linear      --  1/n each
        // -- Time Decay  --  weight proportional to days from first touch (closer = higher)
        // -- U-Shape     --  40% first, 40% last, 20% split middle
        // -- W-Shape     --  30% first, 30% mid, 30% last, 10% rest

        $first_ts = strtotime($touches[0]['touch_at']);
        $last_ts  = strtotime($touches[$n - 1]['touch_at']);
        $span     = max(1, $last_ts - $first_ts);

        // Time-decay denominator: sum of positional weights (closer to last = higher)
        $decay_weights = array();
        foreach ($touches as $i => $t) {
            // days since first touch
            $days_from_first = ($last_ts - strtotime($t['touch_at'])) / 86400;
            // Exponential decay: e^(-0.1 * days_from_last)
            $days_from_last  = ($last_ts - strtotime($t['touch_at'])) / 86400;
            $decay_weights[$i] = exp(-0.1 * $days_from_last);
        }
        $decay_sum = array_sum($decay_weights);

        // Mid-point index for W-shape
        $mid_idx = (int)floor(($n - 1) / 2);

        foreach ($touches as $i => &$t) {
            // First touch
            $t['weight_first'] = ($i === 0) ? 1.0000 : 0.0000;

            // Last touch
            $t['weight_last']  = ($i === $n - 1) ? 1.0000 : 0.0000;

            // Linear
            $t['weight_linear'] = $n > 0 ? round(1 / $n, 4) : 0.0000;

            // Time decay
            $t['weight_time_decay'] = $decay_sum > 0 ? round($decay_weights[$i] / $decay_sum, 4) : 0.0000;

            // U-shape
            if ($n === 1) {
                $t['weight_u'] = 1.0000;
            } elseif ($n === 2) {
                $t['weight_u'] = 0.5000;
            } else {
                $middle_n    = $n - 2;
                $middle_each = $middle_n > 0 ? round(0.20 / $middle_n, 4) : 0;
                if ($i === 0)             $t['weight_u'] = 0.4000;
                elseif ($i === $n - 1)   $t['weight_u'] = 0.4000;
                else                     $t['weight_u'] = $middle_each;
            }

            // W-shape
            if ($n === 1) {
                $t['weight_w'] = 1.0000;
            } elseif ($n === 2) {
                $t['weight_w'] = 0.5000;
            } elseif ($n === 3) {
                $t['weight_w'] = round(1/3, 4);
            } else {
                $anchor_n   = ($mid_idx !== 0 && $mid_idx !== $n - 1) ? 3 : 2;
                $rest_n     = $n - $anchor_n;
                $rest_each  = $rest_n > 0 ? round(0.10 / $rest_n, 4) : 0;
                if ($i === 0)           $t['weight_w'] = 0.3000;
                elseif ($i === $n - 1) $t['weight_w'] = 0.3000;
                elseif ($i === $mid_idx) $t['weight_w'] = 0.3000;
                else                   $t['weight_w'] = $rest_each;
            }
        }
        unset($t);

        return $touches;
    }

    // ------------------------------------------------------------------
    // Helper: recompute attribution_credit rows for one lead.
    // Assumes lead is closed (has a deal_value / potential_rs).
    // ------------------------------------------------------------------
    private function _save_credits($cid_id, $touches)
    {
        if (empty($touches)) return;

        // Get lead value
        $ic  = $this->db->get_where('init_call', array('id' => $cid_id))->row_array();
        $deal_rs = $ic ? (float)$ic['fbudget'] : 0;

        // Delete old credits for this lead
        $this->db->where('cid_id', $cid_id)->delete('attribution_credit');

        $model_weight_map = array(
            'first_touch' => 'weight_first',
            'last_touch'  => 'weight_last',
            'linear'      => 'weight_linear',
            'time_decay'  => 'weight_time_decay',
            'u_shape'     => 'weight_u',
            'w_shape'     => 'weight_w',
        );

        $now = date('Y-m-d H:i:s');

        foreach ($this->_models as $model) {
            $wk = $model_weight_map[$model];
            // Aggregate credit per uid
            $uid_credits = array();
            foreach ($touches as $t) {
                $w   = (float)$t[$wk];
                $uid = (int)$t['uid'];
                if (!isset($uid_credits[$uid])) $uid_credits[$uid] = 0;
                $uid_credits[$uid] += $w;
            }
            foreach ($uid_credits as $uid => $weight_total) {
                $credit_rs = round($deal_rs * $weight_total, 2);
                $this->db->insert('attribution_credit', array(
                    'cid_id'      => $cid_id,
                    'uid'         => $uid,
                    'model_code'  => $model,
                    'credit_rs'   => $credit_rs,
                    'computed_at' => $now,
                ));
            }
        }
    }

    // ------------------------------------------------------------------
    // GET /attribution/touches_for_lead?cid_id=X
    // Returns chronological touches with all 6 weight columns.
    // ------------------------------------------------------------------
    public function touches_for_lead()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $cid_id = (int)$this->input->get('cid_id');
        if (!$cid_id) { $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400); return; }

        $touches = $this->_compute_weights($cid_id);

        // Save updated weights back to DB
        foreach ($touches as $t) {
            $this->db->where('id', $t['id'])->update('attribution_touch', array(
                'weight_first'      => $t['weight_first'],
                'weight_last'       => $t['weight_last'],
                'weight_linear'     => $t['weight_linear'],
                'weight_time_decay' => $t['weight_time_decay'],
                'weight_u'          => $t['weight_u'],
                'weight_w'          => $t['weight_w'],
            ));
        }

        $this->_json(array('ok' => true, 'cid_id' => $cid_id, 'touch_count' => count($touches), 'touches' => $touches));
    }

    // ------------------------------------------------------------------
    // GET /attribution/credit_for_user?uid=X&model=MODEL&period_start=DATE&period_end=DATE
    // Returns total Rs credit for user under one model in a period.
    // ------------------------------------------------------------------
    public function credit_for_user()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $uid    = (int)$this->input->get('uid');
        $model  = trim((string)$this->input->get('model'));
        $p_start= trim((string)$this->input->get('period_start')) ?: date('Y-m-01');
        $p_end  = trim((string)$this->input->get('period_end'))   ?: date('Y-m-d');

        if (!$uid)                            { $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400); return; }
        if (!in_array($model, $this->_models)){ $this->_json(array('ok' => false, 'error' => 'invalid_model'), 400); return; }

        $row = $this->db->select('SUM(credit_rs) AS total_credit_rs, COUNT(DISTINCT cid_id) AS lead_count')
                        ->where('uid', $uid)
                        ->where('model_code', $model)
                        ->where('computed_at >=', $p_start . ' 00:00:00')
                        ->where('computed_at <=', $p_end   . ' 23:59:59')
                        ->get('attribution_credit')->row_array();

        $this->_json(array(
            'ok'              => true,
            'uid'             => $uid,
            'model'           => $model,
            'period_start'    => $p_start,
            'period_end'      => $p_end,
            'total_credit_rs' => (float)($row['total_credit_rs'] ?? 0),
            'lead_count'      => (int)($row['lead_count'] ?? 0),
        ));
    }

    // ------------------------------------------------------------------
    // POST /attribution/recompute_lead
    // Re-runs weights when a new touch is added or lead closes.
    // Body: cid_id
    // ------------------------------------------------------------------
    public function recompute_lead()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }
        if ($this->input->method() !== 'post') { $this->_json(array('ok' => false, 'error' => 'method_not_allowed'), 405); return; }

        $cid_id = (int)$this->input->post('cid_id');
        if (!$cid_id) { $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400); return; }

        $touches = $this->_compute_weights($cid_id);

        // Persist weight columns
        foreach ($touches as $t) {
            $this->db->where('id', $t['id'])->update('attribution_touch', array(
                'weight_first'      => $t['weight_first'],
                'weight_last'       => $t['weight_last'],
                'weight_linear'     => $t['weight_linear'],
                'weight_time_decay' => $t['weight_time_decay'],
                'weight_u'          => $t['weight_u'],
                'weight_w'          => $t['weight_w'],
            ));
        }

        // Recompute and save credits
        $this->_save_credits($cid_id, $touches);

        $this->_json(array('ok' => true, 'cid_id' => $cid_id, 'touches_processed' => count($touches)));
    }

    // ------------------------------------------------------------------
    // GET /attribution/leaderboard?model=MODEL&period_start=DATE&period_end=DATE&limit=N
    // Top BDs by credited Rs under a model.
    // ------------------------------------------------------------------
    public function leaderboard()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $model  = trim((string)$this->input->get('model'));
        $p_start= trim((string)$this->input->get('period_start')) ?: date('Y-m-01');
        $p_end  = trim((string)$this->input->get('period_end'))   ?: date('Y-m-d');
        $limit  = max(1, min(100, (int)($this->input->get('limit') ?: 20)));

        if (!in_array($model, $this->_models)){ $this->_json(array('ok' => false, 'error' => 'invalid_model'), 400); return; }

        $rows = $this->db->query("
            SELECT ac.uid,
                   u.name AS name,
                   SUM(ac.credit_rs)                  AS total_credit_rs,
                   COUNT(DISTINCT ac.cid_id)           AS lead_count
            FROM attribution_credit ac
            LEFT JOIN user u ON u.uid = ac.uid
            WHERE ac.model_code = ?
              AND ac.computed_at >= ?
              AND ac.computed_at <= ?
            GROUP BY ac.uid
            ORDER BY total_credit_rs DESC
            LIMIT ?
        ", array($model, $p_start . ' 00:00:00', $p_end . ' 23:59:59', $limit))->result_array();

        $board = array_map(function($r) {
            return array(
                'uid'             => (int)$r['uid'],
                'name'            => $r['name'],
                'total_credit_rs' => (float)$r['total_credit_rs'],
                'lead_count'      => (int)$r['lead_count'],
            );
        }, $rows);

        $this->_json(array('ok' => true, 'model' => $model, 'period_start' => $p_start, 'period_end' => $p_end, 'leaderboard' => $board));
    }

    // ------------------------------------------------------------------
    // GET /attribution/model_compare?cid_id=X
    // Side-by-side: how each model splits credit for a lead.
    // ------------------------------------------------------------------
    public function model_compare()
    {
        if (!$this->_auth()) { $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401); return; }

        $cid_id = (int)$this->input->get('cid_id');
        if (!$cid_id) { $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400); return; }

        $ic       = $this->db->get_where('init_call', array('id' => $cid_id))->row_array();
        $deal_rs  = $ic ? (float)$ic['fbudget'] : 0;

        $touches  = $this->_compute_weights($cid_id);

        $model_weight_map = array(
            'first_touch' => 'weight_first',
            'last_touch'  => 'weight_last',
            'linear'      => 'weight_linear',
            'time_decay'  => 'weight_time_decay',
            'u_shape'     => 'weight_u',
            'w_shape'     => 'weight_w',
        );

        $compare = array();
        foreach ($this->_models as $model) {
            $wk     = $model_weight_map[$model];
            $splits = array();
            foreach ($touches as $t) {
                $splits[] = array(
                    'uid'       => (int)$t['uid'],
                    'touch_at'  => $t['touch_at'],
                    'touch_type'=> $t['touch_type'],
                    'weight'    => (float)$t[$wk],
                    'credit_rs' => round($deal_rs * (float)$t[$wk], 2),
                );
            }
            $compare[$model] = array(
                'splits'   => $splits,
                'deal_rs'  => $deal_rs,
            );
        }

        $this->_json(array('ok' => true, 'cid_id' => $cid_id, 'deal_rs' => $deal_rs, 'models' => $compare));
    }
}
/* End of Attribution.php */
