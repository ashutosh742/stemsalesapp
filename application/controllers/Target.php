<?php
/**
 * stem_target_controller.php
 *
 * Migration 028 - Cascade Target Setting + Discipline API
 *
 * Endpoints:
 *
 *   GET  /api/target/probe                          - liveness probe (used by cron probe pattern)
 *   POST /api/target/set_rm_total                   - RM enters quarterly total, system cascades
 *   POST /api/target/cascade_preview                - preview cascade without writing
 *   POST /api/target/override                       - override a single allocation row
 *   POST /api/target/lock_cascade                   - lock the quarter (G2)
 *   GET  /api/target/burndown                       - live burn-down per uid + axis
 *   POST /api/target/checkin                        - submit weekly check-in
 *   POST /api/target/checkin/review                 - manager reviews check-in
 *   POST /api/target/signoff                        - end-of-quarter signoff with variance reason
 *   GET  /api/target/war_points                     - 200 cr FY27 master grid view
 *   POST /api/target/rebalance_on_departure         - BD departure triggers sibling rebalance
 *   GET  /api/target/discipline_score               - K17 score for uid + quarter
 *
 * Auth: Bearer STEM_DIGEST_TOKEN for cron callers, session for in-app callers.
 *
 * Migration: 028
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class TargetController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('stem_target_cascade_agent', 'cascade');
        $this->load->model('stem_target_discipline_scorer', 'discipline');
        $this->load->helper(['url','date']);
        $this->config->load('rest', TRUE);
    }

    /* =================================================================
     * SECURITY HELPERS
     * ================================================================= */

    private function auth_check() {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $token = $this->session->userdata('access_token');
            if (!$token) $this->fail(401, 'auth required');
            return $this->session->userdata('uid');
        }
        $expected = $this->config->item('STEM_DIGEST_TOKEN');
        $given    = trim(substr($hdr, 7));
        if (!hash_equals($expected, $given)) $this->fail(401, 'bad token');
        return null; // system caller, uid resolved per request
    }

    private function ok($payload) {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(['status'=>'ok'] + $payload));
        exit;
    }

    private function fail($code, $msg, $extra=[]) {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($code)
            ->set_output(json_encode(['status'=>'error','message'=>$msg] + $extra));
        exit;
    }

    private function resolve_actor($req) {
        // POST'd actor_uid wins if system caller; otherwise session uid.
        $session_uid = $this->session->userdata('uid');
        if ($session_uid) return (int)$session_uid;
        return isset($req['actor_uid']) ? (int)$req['actor_uid'] : 0;
    }

    /* =================================================================
     * 1. PROBE
     * ================================================================= */
    public function probe() {
        // Cheap liveness probe used by cron probe pattern (returns 200 when 028 deployed).
        $this->ok(['migration'=>'028','deployed'=>true,'time'=>date('c')]);
    }

    /* =================================================================
     * 2. SET RM TOTAL + CASCADE
     * ================================================================= */
    public function set_rm_total() {
        $this->auth_check();
        $req = $this->input->post();

        $quarter_id = (int)($req['target_quarter_id'] ?? 0);
        $rm_uid     = (int)($req['rm_uid'] ?? 0);
        $values_raw = $req['values'] ?? null;
        if (!$quarter_id || !$rm_uid || !$values_raw) $this->fail(400, 'missing target_quarter_id, rm_uid, or values');
        $values = is_array($values_raw) ? $values_raw : json_decode($values_raw, true);
        if (!is_array($values)) $this->fail(400, 'values must be axis=>value map');
        $actor = $this->resolve_actor($req) ?: $rm_uid;

        $result = $this->cascade->set_rm_total_and_cascade($quarter_id, $rm_uid, $values, $actor);
        if ($result['status'] !== 'ok') $this->fail(400, $result['message'] ?? 'cascade failed', $result);

        // G1 pass: target was set by Day 1
        $this->discipline->record_gate_outcome($quarter_id, $rm_uid, 'G1_set', 'pass', 'RM set total');

        $this->ok($result);
    }

    /* =================================================================
     * 3. CASCADE PREVIEW
     * ================================================================= */
    public function cascade_preview() {
        $this->auth_check();
        $req = $this->input->post();
        $quarter_id = (int)($req['target_quarter_id'] ?? 0);
        $rm_uid     = (int)($req['rm_uid'] ?? 0);
        $values_raw = $req['values'] ?? null;
        if (!$quarter_id || !$rm_uid || !$values_raw) $this->fail(400, 'missing target_quarter_id, rm_uid, or values');
        $values = is_array($values_raw) ? $values_raw : json_decode($values_raw, true);
        $result = $this->cascade->preview_cascade($quarter_id, $rm_uid, $values);
        $this->ok($result);
    }

    /* =================================================================
     * 4. OVERRIDE
     * ================================================================= */
    public function override() {
        $this->auth_check();
        $req = $this->input->post();
        $alloc_id  = (int)($req['allocation_id'] ?? 0);
        $new_value = isset($req['new_value']) ? floatval($req['new_value']) : null;
        $reason    = $req['reason'] ?? 'manager override';
        if (!$alloc_id || $new_value === null) $this->fail(400, 'allocation_id and new_value required');
        $actor = $this->resolve_actor($req);
        if (!$actor) $this->fail(401, 'actor required');

        $result = $this->cascade->override_and_recascade($alloc_id, $new_value, $actor, $reason);
        if ($result['status'] !== 'ok') $this->fail(409, $result['message'] ?? 'override failed', $result);
        $this->ok($result);
    }

    /* =================================================================
     * 5. LOCK CASCADE
     * ================================================================= */
    public function lock_cascade() {
        $this->auth_check();
        $req = $this->input->post();
        $quarter_id = (int)($req['target_quarter_id'] ?? 0);
        if (!$quarter_id) $this->fail(400, 'target_quarter_id required');
        $actor = $this->resolve_actor($req);

        $result = $this->cascade->lock_cascade($quarter_id, $actor);
        if ($result['status'] === 'error') $this->fail(400, $result['message'], $result);

        // G2 pass: locked
        $q = $this->db->get_where('target_quarter', ['id'=>$quarter_id])->row();
        if ($q) $this->discipline->record_gate_outcome($quarter_id, $q->rm_uid, 'G2_locked', 'pass', 'cascade locked');

        $this->ok($result);
    }

    /* =================================================================
     * 6. BURN-DOWN
     * ================================================================= */
    public function burndown() {
        $this->auth_check();
        $uid      = (int)$this->input->get('uid');
        $quarter  = $this->input->get('quarter'); // optional, defaults to current
        $axis     = $this->input->get('axis');    // optional filter
        $level    = $this->input->get('level');   // optional filter

        $where = [];
        if ($uid)     $where['b.uid']    = $uid;
        if ($axis)    $where['b.axis']   = $axis;
        if ($level)   $where['b.level']  = $level;
        if ($quarter) $where['q.quarter']= $quarter;

        $this->db->select('b.*, q.quarter, q.cluster_id, q.status AS quarter_status, u.name AS uid_name');
        $this->db->from('v_target_burndown b');
        $this->db->join('target_quarter q', 'q.id = b.target_quarter_id', 'left');
        $this->db->join('user u', 'u.uid = b.uid', 'left');
        foreach ($where as $k=>$v) $this->db->where($k, $v);
        $this->db->order_by('b.level', 'asc');
        $rows = $this->db->get()->result();

        $this->ok(['rows'=>$rows,'count'=>count($rows)]);
    }

    /* =================================================================
     * 7. CHECK-IN (weekly)
     * ================================================================= */
    public function checkin() {
        $this->auth_check();
        $req = $this->input->post();
        $quarter_id = (int)($req['target_quarter_id'] ?? 0);
        $uid        = (int)($req['uid'] ?? 0);
        $week_no    = (int)($req['week_no'] ?? 0);
        $week_start = $req['week_start_date'] ?? date('Y-m-d', strtotime('monday this week'));
        $achieved   = $req['achieved_last_week'] ?? '{}';
        $confidence = isset($req['confidence_next_week']) ? (int)$req['confidence_next_week'] : null;
        $blocker    = mb_substr($req['top_blocker'] ?? '', 0, 120);
        $help_needed= !empty($req['help_needed']) ? 1 : 0;
        $help_text  = $req['help_text'] ?? null;

        if (!$quarter_id || !$uid || !$week_no) $this->fail(400, 'target_quarter_id, uid, week_no required');
        if ($week_no < 1 || $week_no > 13) $this->fail(400, 'week_no must be 1 to 13');

        $existing = $this->db->get_where('target_checkin', [
            'target_quarter_id' => $quarter_id,
            'uid'               => $uid,
            'week_no'           => $week_no
        ])->row();

        $data = [
            'achieved_last_week'    => is_array($achieved) ? json_encode($achieved) : $achieved,
            'confidence_next_week'  => $confidence,
            'top_blocker'           => $blocker,
            'help_needed'           => $help_needed,
            'help_text'             => $help_text,
            'submitted_at'          => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $this->db->update('target_checkin', $data, ['id'=>$existing->id]);
            $checkin_id = $existing->id;
        } else {
            $data += [
                'target_quarter_id' => $quarter_id,
                'uid'               => $uid,
                'week_no'           => $week_no,
                'week_start_date'   => $week_start
            ];
            $this->db->insert('target_checkin', $data);
            $checkin_id = $this->db->insert_id();
        }

        // G3 pass for this week
        $this->discipline->record_gate_outcome($quarter_id, $uid, 'G3_weekly_checkin', 'pass', 'check-in submitted', $week_no);

        $this->ok(['checkin_id'=>$checkin_id]);
    }

    public function checkin_review() {
        $this->auth_check();
        $req = $this->input->post();
        $id      = (int)($req['checkin_id'] ?? 0);
        $status  = $req['review_status'] ?? 'acknowledged';
        $notes   = $req['review_notes'] ?? null;
        $reviewer= $this->resolve_actor($req);
        if (!$id || !$reviewer) $this->fail(400, 'checkin_id and reviewer required');
        if (!in_array($status, ['acknowledged','revision_requested','escalated'])) $this->fail(400, 'invalid review_status');

        $this->db->update('target_checkin', [
            'reviewed_by_uid'=>$reviewer,
            'reviewed_at'    =>date('Y-m-d H:i:s'),
            'review_status'  =>$status,
            'review_notes'   =>$notes
        ], ['id'=>$id]);

        $this->ok(['checkin_id'=>$id,'review_status'=>$status]);
    }

    /* =================================================================
     * 8. SIGNOFF (end of quarter)
     * ================================================================= */
    public function signoff() {
        $this->auth_check();
        $req = $this->input->post();
        $quarter_id    = (int)($req['target_quarter_id'] ?? 0);
        $uid           = (int)($req['uid'] ?? 0);
        $axis          = $req['axis'] ?? null;
        $final_actual  = isset($req['final_actual']) ? floatval($req['final_actual']) : null;
        $variance_reason = $req['variance_reason'] ?? null;
        if (!$quarter_id || !$uid || !$axis || $final_actual === null) $this->fail(400, 'quarter, uid, axis, final_actual required');
        $actor = $this->resolve_actor($req);
        if (!$actor) $this->fail(401, 'actor required');

        // pull final_target from allocation
        $alloc = $this->db->get_where('target_allocation', [
            'target_quarter_id'=>$quarter_id, 'uid'=>$uid, 'axis'=>$axis
        ])->row();
        if (!$alloc) $this->fail(404, 'no allocation for uid+axis');

        $final_target = floatval($alloc->final_value);
        $variance_pct = ($final_target == 0) ? 0 : (($final_actual - $final_target) / $final_target) * 100;

        if (abs($variance_pct) > 10 && empty($variance_reason)) {
            $this->fail(400, 'variance over 10 percent requires variance_reason');
        }

        $existing = $this->db->get_where('target_signoff', [
            'target_quarter_id'=>$quarter_id, 'uid'=>$uid, 'axis'=>$axis
        ])->row();

        $data = [
            'final_actual'      => $final_actual,
            'final_target'      => $final_target,
            'variance_pct'      => round($variance_pct, 2),
            'variance_reason'   => $variance_reason,
            'signed_off_by_uid' => $actor,
            'signed_off_at'     => date('Y-m-d H:i:s'),
            'director_review'   => (abs($variance_pct) > 20) ? 'pending' : 'not_required'
        ];

        if ($existing) {
            $this->db->update('target_signoff', $data, ['id'=>$existing->id]);
            $signoff_id = $existing->id;
        } else {
            $data += ['target_quarter_id'=>$quarter_id,'uid'=>$uid,'axis'=>$axis];
            $this->db->insert('target_signoff', $data);
            $signoff_id = $this->db->insert_id();
        }

        // G4 pass once all axes signed off for this uid
        $alloc_count   = $this->db->where(['target_quarter_id'=>$quarter_id,'uid'=>$uid])
                                  ->count_all_results('target_allocation');
        $signoff_count = $this->db->where(['target_quarter_id'=>$quarter_id,'uid'=>$uid])
                                  ->count_all_results('target_signoff');
        if ($alloc_count > 0 && $signoff_count >= $alloc_count) {
            $this->discipline->record_gate_outcome($quarter_id, $uid, 'G4_signoff', 'pass', 'all axes signed off');
        }

        $this->ok(['signoff_id'=>$signoff_id, 'variance_pct'=>$data['variance_pct'], 'director_review'=>$data['director_review']]);
    }

    /* =================================================================
     * 9. WAR POINTS - 200 cr FY27 master grid
     * ================================================================= */
    public function war_points() {
        $this->auth_check();
        $fy      = $this->input->get('fy') ?: 'FY27';
        $quarter = $this->input->get('quarter');   // FY27_Q1 etc, optional

        $this->db->select('w.*, q.id AS target_quarter_id, q.status AS quarter_status, q.rm_uid');
        $this->db->from('v_target_war_points w');
        $this->db->join('target_quarter q',
                        "q.cluster_id = w.cluster_id AND q.quarter = w.quarter", 'left');
        if ($fy)      $this->db->like('w.quarter', $fy, 'after');
        if ($quarter) $this->db->where('w.quarter', $quarter);
        $rows = $this->db->get()->result();

        // Headline totals
        $total_target = 0; $total_actual = 0;
        foreach ($rows as $r) {
            $total_target += floatval($r->target_rs_cr ?? 0);
            $total_actual += floatval($r->actual_rs_cr ?? 0);
        }
        $achieved_pct = $total_target > 0 ? round(($total_actual / $total_target) * 100, 2) : 0;
        $headline = [
            'fy'               => $fy,
            'total_target_rs_cr'=> round($total_target, 2),
            'total_actual_rs_cr'=> round($total_actual, 2),
            'achieved_pct'     => $achieved_pct,
            'pacing'           => $this->compute_pacing_band($achieved_pct, $fy)
        ];

        $this->ok(['headline'=>$headline, 'cells'=>$rows]);
    }

    private function compute_pacing_band($achieved_pct, $fy) {
        // Rough elapsed pct: FY starts 1 Apr. Compute today vs FY end.
        $year = (int)substr($fy, 2, 2) + 2000;        // FY27 -> 2026
        $fy_start = ($year - 1).'-04-01';
        $fy_end   = $year.'-03-31';
        $elapsed_days = (strtotime(date('Y-m-d')) - strtotime($fy_start)) / 86400;
        $total_days   = (strtotime($fy_end) - strtotime($fy_start)) / 86400;
        $elapsed_pct  = $total_days > 0 ? ($elapsed_days / $total_days) * 100 : 0;

        $gap = $achieved_pct - $elapsed_pct;
        if ($gap >= -5)  return 'on_pace';
        if ($gap >= -15) return 'behind';
        return 'critical';
    }

    /* =================================================================
     * 10. REBALANCE ON DEPARTURE
     * ================================================================= */
    public function rebalance_on_departure() {
        $this->auth_check();
        $req = $this->input->post();
        $quarter_id = (int)($req['target_quarter_id'] ?? 0);
        $bd_uid     = (int)($req['departed_bd_uid'] ?? 0);
        if (!$quarter_id || !$bd_uid) $this->fail(400, 'target_quarter_id and departed_bd_uid required');
        $actor = $this->resolve_actor($req);
        if (!$actor) $this->fail(401, 'actor required');

        $result = $this->cascade->rebalance_on_departure($quarter_id, $bd_uid, $actor);
        if ($result['status'] === 'error') $this->fail(400, $result['message'], $result);
        $this->ok($result);
    }

    /* =================================================================
     * 11. DISCIPLINE SCORE (K17 contribution)
     * ================================================================= */
    public function discipline_score() {
        $this->auth_check();
        $uid     = (int)$this->input->get('uid');
        $quarter = $this->input->get('quarter');
        if (!$uid || !$quarter) $this->fail(400, 'uid and quarter required');

        $score = $this->discipline->compute_score($uid, $quarter);
        $this->ok($score);
    }

    /* =================================================================
     * 12. CRITICAL GAPS (consumed by 7:30 audit cron)
     * ================================================================= */
    public function critical_gaps() {
        $this->auth_check();
        // Top 10 cells in current FY where achieved_pct lags elapsed_pct by 15+ pts
        $rows = $this->db->query("
            SELECT w.cluster_id, w.category, w.target_rs_cr, w.actual_rs_cr,
                   ROUND((w.actual_rs_cr / NULLIF(w.target_rs_cr,0)) * 100, 2) AS achieved_pct,
                   w.elapsed_pct
              FROM v_target_war_points w
             WHERE w.target_rs_cr > 0
               AND ((w.actual_rs_cr / NULLIF(w.target_rs_cr,0)) * 100) < (w.elapsed_pct - 15)
             ORDER BY (w.elapsed_pct - (w.actual_rs_cr / NULLIF(w.target_rs_cr,0)) * 100) DESC
             LIMIT 10
        ")->result();
        $this->ok(['cells'=>$rows]);
    }

    /* =================================================================
     * 13. HEADLINE (slim variant for cron headline string)
     * ================================================================= */
    public function headline() {
        $this->auth_check();
        $fy = $this->input->get('fy') ?: 'FY27';
        // Reuse war_points logic but return only headline
        $this->db->select('SUM(target_rs_cr) AS t, SUM(actual_rs_cr) AS a')
                 ->from('v_target_war_points')
                 ->like('quarter', $fy, 'after');
        $r = $this->db->get()->row();
        $t = floatval($r->t ?? 0); $a = floatval($r->a ?? 0);
        $achieved = $t > 0 ? round(($a/$t)*100, 2) : 0;
        $critical = $this->db->query("
            SELECT COUNT(*) AS c FROM v_target_war_points
             WHERE target_rs_cr > 0
               AND ((actual_rs_cr / NULLIF(target_rs_cr,0)) * 100) < (elapsed_pct - 15)
               AND quarter LIKE ?", [$fy.'_%'])->row();
        $this->ok([
            'fy'                  => $fy,
            'total_target_rs_cr'  => round($t,2),
            'total_actual_rs_cr'  => round($a,2),
            'achieved_pct'        => $achieved,
            'pacing'              => $this->compute_pacing_band($achieved, $fy),
            'critical_cells'      => (int)($critical->c ?? 0)
        ]);
    }
}
/* End of file stem_target_controller.php */
