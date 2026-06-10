<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BlitzAi_api
 * Endpoint: GET /api/ai/win_probability?cid_id={id}
 *
 * Real DB implementation. Replaces stub that returned ok/success/stub keys only.
 * Scoring formula (0-100 total):
 *   cstatus_score (max 35) + recency_score (max 30) + momentum_score (max 20) + budget_score (max 15)
 *
 * If ai_lead_score has a today row for cid_id the stored value is returned directly.
 *
 * Route: routes_blitz_30may_d.php maps api/ai/win_probability -> BlitzAi_api/win_probability
 */
class BlitzAi_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected) {
            $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        if (!hash_equals($expected, $tok)) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'bad_token')));
            return false;
        }
        return true;
    }

    private function _json($payload) {
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function _cstatus_score($cstatus) {
        $map = array(
            1  =>  5,  2  =>  8,  3  => 12,  4  =>  6,  5  =>  0,
            6  => 20,  7  => 32,  8  => 15,  9  => 30, 10  =>  4,
            11 =>  3,  12 => 18,  13 => 28,
        );
        $c = (int) $cstatus;
        return isset($map[$c]) ? $map[$c] : 0;
    }

    public function win_probability() {
        if (!$this->_bearer()) return;

        $cid_id = (int) $this->input->get('cid_id');
        if ($cid_id <= 0) {
            return $this->_json(array(
                'ok'           => false,
                'success'      => false,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0),
                'route'        => 'api/ai/win_probability',
                'generated_at' => date('c'),
                'error'        => 'cid_id is required and must be a positive integer',
            ));
        }

        $today = date('Y-m-d');

        // Check pre-computed score from ai_lead_score for today
        $cached = $this->db->query(
            "SELECT win_probability, features_json, top_positive_signal,
                    top_negative_signal, next_best_action, confidence_band
             FROM ai_lead_score
             WHERE cid_id = ?
               AND score_run_date = ?
             ORDER BY created_at DESC
             LIMIT 1",
            array($cid_id, $today)
        )->row();

        // Load lead row
        $lead = $this->db->query(
            "SELECT ic.id, ic.cstatus, CAST(ic.fbudget AS UNSIGNED) AS fbudget_rs
             FROM init_call ic
             WHERE ic.id = ?",
            array($cid_id)
        )->row();

        if (!$lead) {
            return $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0),
                'route'        => 'api/ai/win_probability',
                'generated_at' => date('c'),
                'note'         => 'lead_not_found',
                'cid_id'       => $cid_id,
            ));
        }

        // Last touch from tblcallevents
        $touch_row = $this->db->query(
            "SELECT MAX(date) AS last_touch FROM tblcallevents WHERE cid_id = ?",
            array($cid_id)
        )->row();

        $last_touch_date = ($touch_row && $touch_row->last_touch) ? $touch_row->last_touch : null;
        if ($last_touch_date) {
            $days_since = (int) floor((strtotime($today) - strtotime(date('Y-m-d', strtotime($last_touch_date)))) / 86400);
        } else {
            $days_since = 9999;
        }

        // Recency score
        if ($days_since === 0) { $recency_score = 30; }
        elseif ($days_since <= 3) { $recency_score = 25; }
        elseif ($days_since <= 7) { $recency_score = 18; }
        elseif ($days_since <= 14) { $recency_score = 10; }
        elseif ($days_since <= 30) { $recency_score = 5; }
        else { $recency_score = 0; }

        // Momentum score: scheduled meetings with purpose achieved
        $momentum_row = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM tblcallevents
             WHERE cid_id = ?
               AND actiontype_id = 3
               AND purpose_achieved = 'yes'",
            array($cid_id)
        )->row();
        $momentum_cnt = $momentum_row ? (int) $momentum_row->cnt : 0;
        if ($momentum_cnt === 0) { $momentum_score = 0; }
        elseif ($momentum_cnt === 1) { $momentum_score = 8; }
        elseif ($momentum_cnt === 2) { $momentum_score = 14; }
        else { $momentum_score = 20; }

        // Budget score
        $fbudget = (int) $lead->fbudget_rs;
        if ($fbudget === 0) { $budget_score = 2; }
        elseif ($fbudget < 500000) { $budget_score = 5; }
        elseif ($fbudget < 2000000) { $budget_score = 8; }
        elseif ($fbudget < 10000000) { $budget_score = 12; }
        else { $budget_score = 15; }

        $cstatus_score = $this->_cstatus_score($lead->cstatus);
        $computed_score = $cstatus_score + $recency_score + $momentum_score + $budget_score;
        $computed_score = max(0, min(100, $computed_score));

        // Use cached if available
        $final_score = $cached ? (int) $cached->win_probability : $computed_score;
        $source      = $cached ? 'ai_lead_score_cache' : 'computed';

        $row = array(
            'cid_id'              => $cid_id,
            'win_probability'     => $final_score,
            'score_source'        => $source,
            'cstatus'             => (int) $lead->cstatus,
            'days_since_touch'    => $days_since === 9999 ? null : $days_since,
            'momentum_meetings'   => $momentum_cnt,
            'fbudget_rs'          => $fbudget,
            'score_breakdown'     => array(
                'cstatus_score'   => $cstatus_score,
                'recency_score'   => $recency_score,
                'momentum_score'  => $momentum_score,
                'budget_score'    => $budget_score,
            ),
        );
        if ($cached) {
            $row['top_positive_signal'] = $cached->top_positive_signal;
            $row['top_negative_signal'] = $cached->top_negative_signal;
            $row['next_best_action']    = $cached->next_best_action;
            $row['confidence_band']     = $cached->confidence_band;
        }

        return $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => array($row),
            'data'         => array('count' => 1, 'cid_id' => $cid_id),
            'route'        => 'api/ai/win_probability',
            'generated_at' => date('c'),
        ));
    }
}
