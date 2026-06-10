<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ReviewTargetBridgeV28
 * Wires the TargetMonitor agent into the Review (v2 + schedule + monthly) surfaces
 * so every review pulls live target, pace, RED/AMBER/GREEN band, gap leads,
 * and coaching nudges from TargetMonitorAgentV28 — no separate fetch needed
 * on the mobile client.
 *
 * Endpoints:
 *   /api/review/session?bd_uid=...        -> review session pack (target-linked)
 *   /api/review/self_assessment?bd_uid=... -> BD self-assessment pre-fill
 *   /api/review/schedule_with_target      -> upcoming reviews + target band
 *   /api/review/monthly/with_target?bd_uid&month
 *   /api/review/target_link/probe          -> probe
 *
 * Reads target via internal CURL to /api/target_monitor/* so logic stays single-sourced.
 */
class ReviewTargetBridgeV28 extends CI_Controller {

    private $token;
    private $base;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('url'));
        $this->token = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $this->base  = 'https://selfstagingstemapp.in';
    }

    private function out($a){ header('Content-Type: application/json'); echo json_encode($a); exit; }
    private function ok($extra=array()){ return $this->out(array_merge(array('ok'=>true,'success'=>true,'ts'=>gmdate('c')),$extra)); }
    private function err($msg,$code=400){ http_response_code($code); return $this->out(array('ok'=>false,'success'=>false,'error'=>$msg)); }

    private function auth(){
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609
        $h = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (strpos($h,'Bearer ') === 0) {
            $t = substr($h,7);
            if ($t === $this->token) return true;
        }
        return false;
    }

    private function fetch_internal($path){
        $url = $this->base . $path;
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => array('Authorization: Bearer '.$this->token),
            CURLOPT_SSL_VERIFYPEER => 0,
        ));
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300) return null;
        $d = json_decode($body, true);
        return is_array($d) ? $d : null;
    }

    public function probe(){
        if (!$this->auth()) return $this->err('unauthorized',401);
        return $this->ok(array(
            'agent' => 'review_target_bridge_v28',
            'links' => array('target_monitor_v28','review_v2','review_schedule','monthly_lead_review'),
            'endpoints' => array(
                '/api/review/session?bd_uid=...',
                '/api/review/self_assessment?bd_uid=...',
                '/api/review/schedule_with_target',
                '/api/review/monthly/with_target?bd_uid=...&month=...',
            ),
        ));
    }

    /* ============ /api/review/session ============ */
    public function session(){
        if (!$this->auth()) return $this->err('unauthorized',401);
        $bd = (int)$this->input->get('bd_uid');
        if (!$bd) return $this->err('bd_uid required');

        $tm_month   = $this->fetch_internal('/api/target_monitor/bd/'.$bd);
        $tm_quarter = $this->fetch_internal('/api/target_monitor/bd/'.$bd.'/quarter');
        $tm_fy      = $this->fetch_internal('/api/target_monitor/bd/'.$bd.'/fy');

        if (!$tm_month) return $this->err('bd not found or target monitor unreachable',404);

        // Recent meetings count (last 14 days)
        $row = $this->db->query("SELECT COUNT(*) c FROM tblcallevents WHERE user_id=? AND date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)", array($bd))->row();
        $meetings_14d = $row ? (int)$row->c : 0;

        // MoM approval ratio last 14 days
        $row2 = $this->db->query("SELECT SUM(CASE WHEN mom_approved=1 THEN 1 ELSE 0 END) approved, COUNT(*) total FROM tblcallevents WHERE user_id=? AND date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND mom IS NOT NULL", array($bd))->row();
        $mom_pct = ($row2 && (int)$row2->total > 0) ? round(((int)$row2->approved / (int)$row2->total) * 100, 1) : 0;

        $coaching = array();
        if (($tm_month['band'] ?? '') === 'RED') $coaching[] = 'Target band RED for month. Focus today on top gap-closing leads.';
        if (($tm_quarter['band'] ?? '') === 'RED') $coaching[] = 'Quarter pacing critical. Review pipeline coverage.';
        if ($meetings_14d < 10) $coaching[] = 'Meeting cadence low ('.$meetings_14d.' in 14 days). Push planner discipline.';
        if ($mom_pct < 60) $coaching[] = 'MoM approval ratio '.$mom_pct.' percent. Coach on MoM quality.';
        if (empty($coaching)) $coaching[] = 'On track. Keep cadence steady.';

        return $this->ok(array(
            'bd_uid' => $bd,
            'bd' => $tm_month['user'] ?? null,
            'target_link' => array(
                'month'   => $tm_month   ? array('target_rs'=>$tm_month['target_rs'],   'achieved_rs'=>$tm_month['achieved_rs'],   'achieved_pct'=>$tm_month['achieved_pct'],   'pace_gap_pp'=>$tm_month['pace_gap_pp'],   'band'=>$tm_month['band'])   : null,
                'quarter' => $tm_quarter ? array('target_rs'=>$tm_quarter['target_rs'], 'achieved_rs'=>$tm_quarter['achieved_rs'], 'achieved_pct'=>$tm_quarter['achieved_pct'], 'pace_gap_pp'=>$tm_quarter['pace_gap_pp'], 'band'=>$tm_quarter['band']) : null,
                'fy'      => $tm_fy      ? array('target_rs'=>$tm_fy['target_rs'],      'achieved_rs'=>$tm_fy['achieved_rs'],      'achieved_pct'=>$tm_fy['achieved_pct'],      'pace_gap_pp'=>$tm_fy['pace_gap_pp'],      'band'=>$tm_fy['band'])      : null,
            ),
            'top_gap_closers' => $tm_month['top_gap_closers'] ?? array(),
            'discipline' => array(
                'meetings_last_14d' => $meetings_14d,
                'mom_approval_pct'  => $mom_pct,
            ),
            'coaching_nudges' => $coaching,
            'next_review_date' => date('Y-m-d', strtotime('next monday')),
        ));
    }

    /* ============ /api/review/self_assessment ============ */
    public function self_assessment(){
        if (!$this->auth()) return $this->err('unauthorized',401);
        $bd = (int)$this->input->get('bd_uid');
        if (!$bd) return $this->err('bd_uid required');

        $tm = $this->fetch_internal('/api/target_monitor/bd/'.$bd);
        if (!$tm) return $this->err('bd not found',404);

        // Pre-fill scorecard
        $self = array(
            'target_awareness' => array(
                'do_you_know_your_target_rs' => $tm['target_rs'],
                'where_are_you_today_rs'     => $tm['achieved_rs'],
                'pace_gap_pp'                => $tm['pace_gap_pp'],
                'band'                       => $tm['band'],
            ),
            'pipeline_coverage' => array(
                'open_pipeline_rs'    => $tm['open_pipeline_rs'] ?? 0,
                'weighted_pipeline_rs'=> $tm['weighted_pipeline_rs'] ?? 0,
                'gap_to_target_rs'    => $tm['gap_to_target_rs'] ?? 0,
                'coverage_ratio'      => $tm['gap_to_target_rs'] > 0 ? round((($tm['weighted_pipeline_rs'] ?? 0) / $tm['gap_to_target_rs']),2) : null,
            ),
            'questions_to_answer' => array(
                'Q1' => 'Which 3 leads will close this month to hit target?',
                'Q2' => 'What is blocking your RED band leads?',
                'Q3' => 'What field coverage will you commit this week?',
                'Q4' => 'Any escalation you need from CM?',
            ),
            'recommended_leads' => array_slice($tm['top_gap_closers'] ?? array(), 0, 5),
        );

        return $this->ok(array('bd_uid'=>$bd,'self_assessment'=>$self));
    }

    /* ============ /api/review/schedule_with_target ============ */
    public function schedule_with_target(){
        if (!$this->auth()) return $this->err('unauthorized',401);
        $sweep = $this->fetch_internal('/api/target_monitor/sweep');
        if (!$sweep) return $this->err('target sweep unreachable',502);

        // Each row gets a recommended_review_priority
        $rows = array();
        foreach (($sweep['rows'] ?? array()) as $r) {
            $priority = ($r['band'] === 'RED') ? 'urgent' : (($r['band'] === 'AMBER') ? 'standard' : 'light');
            $rows[] = array_merge($r, array(
                'recommended_review_priority' => $priority,
                'next_review_slot' => date('Y-m-d', strtotime('next monday')),
            ));
        }
        return $this->ok(array(
            'period' => $sweep['period'] ?? null,
            'totals' => $sweep['totals'] ?? null,
            'reviews' => $rows,
        ));
    }

    /* ============ /api/review/monthly/with_target ============ */
    public function monthly_with_target(){
        if (!$this->auth()) return $this->err('unauthorized',401);
        $bd = (int)$this->input->get('bd_uid');
        $month = $this->input->get('month') ?: date('Y-m');
        if (!$bd) return $this->err('bd_uid required');

        $tm = $this->fetch_internal('/api/target_monitor/bd/'.$bd);
        if (!$tm) return $this->err('bd not found',404);

        // Last-month won total
        $row = $this->db->query("SELECT COUNT(*) won_count, COALESCE(SUM(fbudget),0) won_rs FROM init_call WHERE mainbd=? AND cstatus=12 AND DATE_FORMAT(updated_at,'%Y-%m')=?", array($bd,$month))->row();

        return $this->ok(array(
            'bd_uid' => $bd,
            'month' => $month,
            'monthly_review' => array(
                'target_rs'    => $tm['target_rs'],
                'achieved_rs'  => $tm['achieved_rs'],
                'won_count'    => $row ? (int)$row->won_count : 0,
                'won_rs'       => $row ? (float)$row->won_rs : 0,
                'band'         => $tm['band'],
                'pace_gap_pp'  => $tm['pace_gap_pp'],
            ),
            'top_gap_closers' => $tm['top_gap_closers'] ?? array(),
            'next_month_focus' => ($tm['band']==='RED' || $tm['band']==='AMBER')
                ? 'Lock 3 high-AI-score leads from gap closers. Daily progression check.'
                : 'Maintain cadence. Push for stretch.',
        ));
    }
}
