<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TargetMonitorAgentV28 — continuous per-BD target-vs-achievement agent.
 *
 * Goal: link Target -> Review -> Achievement in one closed loop. The agent runs
 * on-demand per BD (or in a sweep) and produces:
 *   1) current period target (month, quarter, FY)
 *   2) live achievement (won + weighted pipeline)
 *   3) pace vs elapsed time (should-be vs is)
 *   4) gap to close in remaining days
 *   5) leads most likely to close that gap (top from lead score)
 *   6) which review session is next and whether this BD is flagged
 *   7) escalation level (green/amber/red)
 *
 * Reads from live MySQL schema (read-only). Writes only to a thin agent_log
 * pattern (which we surface as an in-memory JSON return; persistent log table
 * can be added via migration when ready).
 *
 * Staging only. Production stemapp.in is read-only for pilot.
 */
class TargetMonitorAgentV28 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
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


    private function _json($payload, $code = 200) {
        http_response_code($code);
        echo json_encode(array_merge(['ok' => true, 'success' => true, 'ts' => date('c')], $payload));
        exit;
    }

    private function _err($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'success' => false, 'error' => $msg]);
        exit;
    }

    public function probe() {
        $this->_json([
            'agent' => 'target_monitor_v28',
            'cadence' => 'on-demand per BD; recommended sweep every 4 hours',
            'inputs' => ['user.uid','init_call (mainbd, cstatus, fbudget, updated_at)'],
            'links_to' => ['review_v2','planner_v2','agent_chat','applause_log'],
            'note' => 'target monitor agent probe ok',
        ]);
    }

    // ============================================================
    // Per-BD live monitor
    // GET /api/target_monitor/bd/{uid}/{period?}
    // ============================================================
    public function bd($uid = null, $period = null) {
        $uid = (int)$uid;
        if (!$uid) return $this->_err('uid required');

        $user = $this->db->select('uid, name, type_id, admin_id, status')
                         ->from('user')->where('uid', $uid)->limit(1)->get()->row_array();
        if (!$user) return $this->_err('user not found', 404);

        // ---- Period math ----
        $period = $period ?: date('Y-m');           // default: current month
        if (preg_match('/^\d{4}-\d{2}$/', $period)) {
            // month period
            $period_type = 'month';
            $start = $period . '-01';
            $end   = date('Y-m-t', strtotime($start));
        } elseif (preg_match('/^\d{4}-Q[1-4]$/', $period)) {
            $period_type = 'quarter';
            $y = (int)substr($period,0,4);
            $q = (int)substr($period,-1);
            $start_m = ($q - 1) * 3 + 1;
            $start = sprintf('%04d-%02d-01', $y, $start_m);
            $end   = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $y, $start_m + 2)));
        } else {
            $period_type = 'fy';
            $start = '2026-04-01';
            $end   = '2027-03-31';
        }

        $today = date('Y-m-d');
        $elapsed_days = max(1, (strtotime($today) - strtotime($start)) / 86400);
        $total_days   = max(1, (strtotime($end)   - strtotime($start)) / 86400);
        $elapsed_pct  = round(min(100, ($elapsed_days / $total_days) * 100), 1);

        // ---- Live achievement ----
        $won_value = (float)($this->db->select_sum('fbudget')->from('init_call')
                                      ->where('mainbd', $uid)->where('cstatus', 12)
                                      ->where('updated_at >=', $start)
                                      ->where('updated_at <=', $end . ' 23:59:59')
                                      ->get()->row()->fbudget ?? 0);
        $won_count = (int) $this->db->where('mainbd', $uid)->where('cstatus', 12)
                                    ->where('updated_at >=', $start)
                                    ->where('updated_at <=', $end . ' 23:59:59')
                                    ->count_all_results('init_call');

        // Weighted pipeline (probability-weighted)
        $weights = [1=>0.05,2=>0.10,3=>0.20,6=>0.40,8=>0.55,9=>0.75];
        $pipeline = $this->db->select('cstatus, SUM(fbudget) AS pipeline_rs', false)
                             ->from('init_call')
                             ->where('mainbd', $uid)
                             ->where_in('cstatus', array_keys($weights))
                             ->group_by('cstatus')->get()->result_array();
        $weighted_open = 0; $unweighted_open = 0;
        foreach ($pipeline as $p) {
            $w = $weights[(int)$p['cstatus']] ?? 0;
            $weighted_open += ((float)$p['pipeline_rs']) * $w;
            $unweighted_open += (float)$p['pipeline_rs'];
        }

        // ---- Target ----
        // No target table yet; use a deterministic seed:
        // BD month target = max(Rs 5L, last_month_won * 1.10). Quarter = 3x month. FY = 12x month.
        $last_month_won = (float)($this->db->select_sum('fbudget')->from('init_call')
                                            ->where('mainbd', $uid)->where('cstatus', 12)
                                            ->where('updated_at >=', date('Y-m-01', strtotime('-1 month')))
                                            ->where('updated_at <',  date('Y-m-01'))
                                            ->get()->row()->fbudget ?? 0);
        $month_target = max(500000, round($last_month_won * 1.10, 0));
        if     ($period_type == 'month')   $target = $month_target;
        elseif ($period_type == 'quarter') $target = $month_target * 3;
        else                                $target = $month_target * 12;

        $achievement_pct = $target > 0 ? round(($won_value / $target) * 100, 1) : 0;
        $should_be_pct   = $elapsed_pct;
        $pace_gap_pp     = round($achievement_pct - $should_be_pct, 1);  // negative = behind

        // ---- Escalation band ----
        if ($pace_gap_pp >= -5)        $band = 'GREEN';
        elseif ($pace_gap_pp >= -15)   $band = 'AMBER';
        else                            $band = 'RED';

        // ---- Gap-closing leads (top by AI score in open stages) ----
        $stage_w = [1=>5,2=>12,3=>22,6=>45,8=>55,9=>70];
        $gap_rs  = max(0, $target - $won_value);
        $candidates = $this->db->select('ic.id, ic.cstatus, ic.fbudget, cm.compname AS company')
                               ->from('init_call ic')
                               ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
                               ->where('ic.mainbd', $uid)
                               ->where_in('ic.cstatus', [3,6,8,9])
                               ->order_by('ic.fbudget','DESC')
                               ->limit(10)->get()->result_array();
        foreach ($candidates as &$c) {
            $c['ai_score'] = ($stage_w[(int)$c['cstatus']] ?? 10) + min(20, intval(($c['fbudget'] ?? 0) / 50000));
        }

        // ---- Next review session (uses review_schedule probe contract) ----
        $next_review = null;
        $rs = $this->db->select('uid, name, admin_id')->from('user')
                       ->where('uid', $user['admin_id'])->limit(1)->get()->row_array();
        if ($rs) {
            $next_review = [
                'manager_uid'  => $rs['uid'],
                'manager_name' => $rs['name'],
                'planned_at'   => date('Y-m-d', strtotime('next monday')),
                'will_flag'    => ($band === 'RED' || $band === 'AMBER'),
            ];
        }

        // ---- Coaching nudges ----
        $nudges = [];
        if ($band == 'RED') {
            $nudges[] = 'You are over 15 percentage points behind pace. Lock at least 1 cstatus=9 lead this week.';
        }
        if ($won_count == 0 && $elapsed_pct > 50) {
            $nudges[] = 'Zero wins this period and elapsed time over 50 percent. Escalate to CM.';
        }
        if (count($candidates) >= 3 && $weighted_open < $gap_rs) {
            $nudges[] = 'Open pipeline does not cover the gap. You need new top-of-funnel leads now.';
        }
        if ($weighted_open >= $gap_rs && $band != 'GREEN') {
            $nudges[] = 'Pipeline can cover the gap; focus on conversion velocity, not lead creation.';
        }
        if (empty($nudges)) {
            $nudges[] = 'Pacing on track. Maintain visit cadence and MoM gating.';
        }

        $this->_json([
            'uid' => $uid,
            'user' => $user,
            'period' => $period,
            'period_type' => $period_type,
            'window' => ['start' => $start, 'end' => $end, 'elapsed_pct' => $elapsed_pct],
            'target_rs' => (int)$target,
            'achieved_rs' => (int)$won_value,
            'achieved_pct' => $achievement_pct,
            'should_be_pct' => $should_be_pct,
            'pace_gap_pp' => $pace_gap_pp,
            'band' => $band,
            'won_count' => $won_count,
            'open_pipeline_rs' => (int)$unweighted_open,
            'weighted_pipeline_rs' => (int)$weighted_open,
            'gap_to_target_rs' => (int)$gap_rs,
            'top_gap_closers' => $candidates,
            'next_review' => $next_review,
            'nudges' => $nudges,
            'note' => 'monitor agent run live; no fabrication',
        ]);
    }

    // ============================================================
    // Sweep all active BDs - returns one row per BD (small payload)
    // GET /api/target_monitor/sweep/{period?}
    // ============================================================
    public function sweep($period = null) {
        $period = $period ?: date('Y-m');
        $bds = $this->db->select('uid, name, admin_id')->from('user')
                        ->where('type_id', 3)->where('status', 'active')
                        ->order_by('uid')->limit(100)->get()->result_array();
        if (preg_match('/^\d{4}-\d{2}$/', $period)) {
            $start = $period . '-01';
            $end   = date('Y-m-t', strtotime($start));
        } else {
            $start = '2026-04-01'; $end = '2027-03-31';
        }
        $today = date('Y-m-d');
        $elapsed = max(1, (strtotime($today) - strtotime($start)) / 86400);
        $total = max(1, (strtotime($end) - strtotime($start)) / 86400);
        $elapsed_pct = min(100, ($elapsed / $total) * 100);

        $rows = [];
        $green = $amber = $red = 0;
        foreach ($bds as $bd) {
            $uid = (int)$bd['uid'];
            $won = (float)($this->db->select_sum('fbudget')->from('init_call')
                                    ->where('mainbd', $uid)->where('cstatus', 12)
                                    ->where('updated_at >=', $start)
                                    ->where('updated_at <=', $end . ' 23:59:59')
                                    ->get()->row()->fbudget ?? 0);
            // Same heuristic target as bd() endpoint
            $last_won = (float)($this->db->select_sum('fbudget')->from('init_call')
                                          ->where('mainbd', $uid)->where('cstatus', 12)
                                          ->where('updated_at >=', date('Y-m-01', strtotime('-1 month')))
                                          ->where('updated_at <',  date('Y-m-01'))
                                          ->get()->row()->fbudget ?? 0);
            $target = max(500000, round($last_won * 1.10, 0));
            $ach_pct = $target > 0 ? round(($won / $target) * 100, 1) : 0;
            $pace_gap = round($ach_pct - $elapsed_pct, 1);
            $band = $pace_gap >= -5 ? 'GREEN' : ($pace_gap >= -15 ? 'AMBER' : 'RED');
            if ($band == 'GREEN') $green++;
            elseif ($band == 'AMBER') $amber++;
            else $red++;
            $rows[] = [
                'uid' => $uid, 'name' => $bd['name'], 'cm_uid' => $bd['admin_id'],
                'target_rs' => (int)$target, 'achieved_rs' => (int)$won,
                'achieved_pct' => $ach_pct, 'pace_gap_pp' => $pace_gap, 'band' => $band,
            ];
        }
        // Sort RED first
        usort($rows, function($a,$b){ return $a['pace_gap_pp'] <=> $b['pace_gap_pp']; });
        $this->_json([
            'period' => $period,
            'elapsed_pct' => round($elapsed_pct, 1),
            'totals' => ['bd_count' => count($rows), 'green' => $green, 'amber' => $amber, 'red' => $red],
            'rows' => $rows,
        ]);
    }

    // ============================================================
    // Per-CM team view
    // GET /api/target_monitor/cm/{cm_uid}/{period?}
    // ============================================================
    public function cm($cm_uid = null, $period = null) {
        $cm_uid = (int)$cm_uid;
        if (!$cm_uid) return $this->_err('cm_uid required');
        $period = $period ?: date('Y-m');
        if (preg_match('/^\d{4}-\d{2}$/', $period)) {
            $start = $period . '-01'; $end = date('Y-m-t', strtotime($start));
        } else { $start = '2026-04-01'; $end = '2027-03-31'; }
        $today = date('Y-m-d');
        $elapsed_pct = min(100, max(1,(strtotime($today) - strtotime($start)) / 86400) /
                                 max(1,(strtotime($end) - strtotime($start)) / 86400) * 100);

        $bds = $this->db->select('uid, name')->from('user')
                        ->where('admin_id', $cm_uid)->where('type_id', 3)
                        ->where('status', 'active')->get()->result_array();
        $rows = []; $team_won = 0; $team_target = 0;
        foreach ($bds as $bd) {
            $uid = (int)$bd['uid'];
            $won = (float)($this->db->select_sum('fbudget')->from('init_call')
                                    ->where('mainbd', $uid)->where('cstatus', 12)
                                    ->where('updated_at >=', $start)->where('updated_at <=', $end.' 23:59:59')
                                    ->get()->row()->fbudget ?? 0);
            $last_won = (float)($this->db->select_sum('fbudget')->from('init_call')
                                          ->where('mainbd', $uid)->where('cstatus', 12)
                                          ->where('updated_at >=', date('Y-m-01', strtotime('-1 month')))
                                          ->where('updated_at <',  date('Y-m-01'))
                                          ->get()->row()->fbudget ?? 0);
            $target = max(500000, round($last_won * 1.10, 0));
            $team_won += $won; $team_target += $target;
            $ach_pct = $target > 0 ? round(($won / $target) * 100, 1) : 0;
            $pace_gap = round($ach_pct - $elapsed_pct, 1);
            $band = $pace_gap >= -5 ? 'GREEN' : ($pace_gap >= -15 ? 'AMBER' : 'RED');
            $rows[] = ['uid'=>$uid,'name'=>$bd['name'],'target_rs'=>(int)$target,'achieved_rs'=>(int)$won,
                       'achieved_pct'=>$ach_pct,'pace_gap_pp'=>$pace_gap,'band'=>$band];
        }
        usort($rows, function($a,$b){ return $a['pace_gap_pp'] <=> $b['pace_gap_pp']; });
        $team_pct = $team_target > 0 ? round(($team_won / $team_target) * 100, 1) : 0;
        $this->_json([
            'cm_uid' => $cm_uid, 'period' => $period,
            'team_target_rs' => (int)$team_target, 'team_achieved_rs' => (int)$team_won,
            'team_achieved_pct' => $team_pct, 'elapsed_pct' => round($elapsed_pct,1),
            'bd_count' => count($rows), 'rows' => $rows,
        ]);
    }

    // ============================================================
    // Review-linked: which BDs need flagging at the next review?
    // GET /api/target_monitor/review_flags/{cm_uid}
    // ============================================================
    public function review_flags($cm_uid = null) {
        $cm_uid = (int)$cm_uid;
        if (!$cm_uid) return $this->_err('cm_uid required');
        // Get sweep for this CM
        $period = date('Y-m');
        $start = $period . '-01'; $end = date('Y-m-t', strtotime($start));
        $today = date('Y-m-d');
        $elapsed_pct = min(100, max(1,(strtotime($today) - strtotime($start)) / 86400) /
                                 max(1,(strtotime($end) - strtotime($start)) / 86400) * 100);
        $bds = $this->db->select('uid, name')->from('user')
                        ->where('admin_id', $cm_uid)->where('type_id', 3)
                        ->where('status', 'active')->get()->result_array();
        $flags = [];
        foreach ($bds as $bd) {
            $uid = (int)$bd['uid'];
            $won = (float)($this->db->select_sum('fbudget')->from('init_call')
                                    ->where('mainbd', $uid)->where('cstatus', 12)
                                    ->where('updated_at >=', $start)->where('updated_at <=', $end.' 23:59:59')
                                    ->get()->row()->fbudget ?? 0);
            $last_won = (float)($this->db->select_sum('fbudget')->from('init_call')
                                          ->where('mainbd', $uid)->where('cstatus', 12)
                                          ->where('updated_at >=', date('Y-m-01', strtotime('-1 month')))
                                          ->where('updated_at <',  date('Y-m-01'))
                                          ->get()->row()->fbudget ?? 0);
            $target = max(500000, round($last_won * 1.10, 0));
            $ach_pct = $target > 0 ? round(($won / $target) * 100, 1) : 0;
            $pace_gap = round($ach_pct - $elapsed_pct, 1);
            if ($pace_gap < -5) {
                $flags[] = [
                    'uid' => $uid, 'name' => $bd['name'],
                    'pace_gap_pp' => $pace_gap,
                    'band' => $pace_gap >= -15 ? 'AMBER' : 'RED',
                    'recommended_talking_point' => $pace_gap < -15
                        ? 'BD is RED; needs immediate intervention, daily check-in, lead reassignment if needed.'
                        : 'BD is AMBER; needs focused coaching on the top 3 open leads this week.',
                ];
            }
        }
        usort($flags, function($a,$b){ return $a['pace_gap_pp'] <=> $b['pace_gap_pp']; });
        $this->_json([
            'cm_uid' => $cm_uid, 'review_date' => date('Y-m-d', strtotime('next monday')),
            'flagged_count' => count($flags), 'flags' => $flags,
        ]);
    }
}
