<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QuarterReviewGate_model
 *
 * Implements the blocking target gate inside the Review v2 quarter close
 * session. A manager cannot submit their quarter review until every direct
 * report has next-quarter targets filled in for every required category.
 *
 * Flow:
 *   1. Manager opens their quarter review (review_session row, type='quarter').
 *   2. Backend stamps target_gate_required=1 and next_quarter_config_id.
 *   3. UI fetches /api/review/target_gate/pending?review_session_id=...
 *      and renders one tile per direct report with empty target inputs.
 *   4. Each save calls /api/review/target_gate/set which writes one
 *      quarter_target_audit row. AFTER INSERT trigger upserts
 *      revenue_target_matrix.
 *   5. Once target_gate_count_set == target_gate_count_required, gate is
 *      marked satisfied and the Submit button unlocks.
 *   6. trg_review_target_gate_check trigger on review_session UPDATE blocks
 *      any submitted status change while gate is still 0.
 *
 * Required categories per role (matches incentive_cadence_master scope):
 *   BD          -> PSU, GENERAL, MFT
 *   ACM         -> PSU, GENERAL, MFT, DMFT (Q3 only)
 *   CM          -> PSU, ANCHOR, DMFT (Q3 only), TOP_CLOSURE (Q3-Q4)
 *   RM          -> PSU, DMFT (Q3 only), ANCHOR, UPSELL
 *   SC          -> CSR, GENERAL
 *   Director    -> not gated, sets via direct admin screen
 */
class QuarterReviewGate_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('ReportingHierarchy_model', 'rh');
    }

    // ---- Configuration: which categories each role must have a target for ----

    private function required_categories($role, $next_quarter_cfg)
    {
        $is_q3 = ((int)$next_quarter_cfg['quarter'] === 3);
        $is_q3_or_q4 = (in_array((int)$next_quarter_cfg['quarter'], [3, 4]));

        switch ($role) {
            case 'BD':
                return ['PSU', 'GENERAL', 'MFT'];
            case 'ACM':
                $cats = ['PSU', 'GENERAL', 'MFT'];
                if ($is_q3) $cats[] = 'DMFT';
                return $cats;
            case 'CM':
                $cats = ['PSU', 'ANCHOR'];
                if ($is_q3) $cats[] = 'DMFT';
                if ($is_q3_or_q4) $cats[] = 'TOP_CLOSURE';
                return $cats;
            case 'RM':
                $cats = ['PSU', 'ANCHOR', 'UPSELL'];
                if ($is_q3) $cats[] = 'DMFT';
                return $cats;
            case 'SC':
                return ['CSR', 'GENERAL'];
            default:
                return ['GENERAL'];
        }
    }

    // -------------------------------------------------------------------
    // OPEN A QUARTER REVIEW WITH TARGET GATE
    // -------------------------------------------------------------------

    /**
     * Called when a manager begins a quarter review.
     * Stamps target_gate_required, computes target_gate_count_required.
     * Returns: ['review_session_id'=>int, 'next_quarter'=>..., 'tasks'=>[...]]
     */
    public function open_quarter_review($manager_uid, $review_session_id)
    {
        $current = $this->rh->current_quarter();
        $next    = $this->rh->next_quarter();
        if (!$current || !$next) {
            return ['ok' => false, 'error' => 'No quarter_config row covers today or future quarter.'];
        }

        $manager = $this->rh->get_employee($manager_uid);
        if (!$manager) {
            return ['ok' => false, 'error' => 'Manager not found in hierarchy.'];
        }

        $direct_reports = $this->rh->direct_reports($manager_uid);
        if (empty($direct_reports)) {
            // Director has no direct manager but does have direct reports.
            // SC, BD usually have no reports - their review is not gated.
            $this->db->where('id', $review_session_id)->update('review_session', [
                'target_gate_required' => 0,
                'target_gate_satisfied' => 1,
                'quarter_config_id' => $current['id'],
                'next_quarter_config_id' => $next['id'],
            ]);
            return ['ok' => true, 'gate' => 'not_required', 'direct_reports' => []];
        }

        // Compute total targets needed across all direct reports
        $count_required = 0;
        $tasks = [];
        foreach ($direct_reports as $dr) {
            $cats = $this->required_categories($dr['role'], $next);
            foreach ($cats as $cat) {
                $count_required++;
                $tasks[] = [
                    'employee_uid' => (int)$dr['employee_uid'],
                    'employee_name' => $dr['employee_name'],
                    'role' => $dr['role'],
                    'cluster_id' => (int)$dr['cluster_id'],
                    'cluster_name' => $dr['cluster_text'],
                    'category' => $cat,
                    'status' => 'pending',
                ];
            }
        }

        // Update review_session with gate metadata
        $this->db->where('id', $review_session_id)->update('review_session', [
            'target_gate_required' => 1,
            'target_gate_satisfied' => 0,
            'target_gate_count_required' => $count_required,
            'target_gate_count_set' => 0,
            'quarter_config_id' => $current['id'],
            'next_quarter_config_id' => $next['id'],
        ]);

        return [
            'ok' => true,
            'gate' => 'required',
            'review_session_id' => (int)$review_session_id,
            'manager' => [
                'uid' => (int)$manager['employee_uid'],
                'name' => $manager['employee_name'],
                'role' => $manager['role'],
            ],
            'current_quarter' => $current,
            'next_quarter' => $next,
            'count_required' => $count_required,
            'count_set' => 0,
            'tasks' => $tasks,
        ];
    }

    // -------------------------------------------------------------------
    // FETCH PENDING TASKS (UI calls this on every load)
    // -------------------------------------------------------------------

    public function pending_tasks($review_session_id)
    {
        $rs = $this->db->where('id', $review_session_id)
                       ->get('review_session')->row_array();
        if (!$rs) return ['ok' => false, 'error' => 'Review session not found.'];

        if (!$rs['target_gate_required']) {
            return ['ok' => true, 'gate' => 'not_required', 'tasks' => []];
        }

        $next = $this->db->where('id', $rs['next_quarter_config_id'])
                         ->get('quarter_config')->row_array();
        if (!$next) return ['ok' => false, 'error' => 'next_quarter_config_id missing.'];

        $direct_reports = $this->rh->direct_reports($rs['manager_uid']);

        // Find which audit rows already exist for this review
        $existing = $this->db->where('review_session_id', $review_session_id)
                             ->where('overridden_at', NULL)
                             ->get('quarter_target_audit')->result_array();
        $set_keys = [];
        foreach ($existing as $row) {
            $key = $row['set_for_uid'] . '|' . $row['category'];
            $set_keys[$key] = $row;
        }

        $tasks = [];
        $count_set = 0;
        foreach ($direct_reports as $dr) {
            $cats = $this->required_categories($dr['role'], $next);
            foreach ($cats as $cat) {
                $key = $dr['employee_uid'] . '|' . $cat;
                $status = isset($set_keys[$key]) ? 'set' : 'pending';
                if ($status === 'set') $count_set++;
                $tasks[] = [
                    'employee_uid' => (int)$dr['employee_uid'],
                    'employee_name' => $dr['employee_name'],
                    'role' => $dr['role'],
                    'cluster_id' => (int)$dr['cluster_id'],
                    'cluster_name' => $dr['cluster_text'],
                    'category' => $cat,
                    'status' => $status,
                    'target_rs_lakh' => isset($set_keys[$key]) ? (float)$set_keys[$key]['target_rs_lakh'] : NULL,
                    'rationale_text' => isset($set_keys[$key]) ? $set_keys[$key]['rationale_text'] : NULL,
                ];
            }
        }

        $count_required = count($tasks);
        $satisfied = ($count_set >= $count_required && $count_required > 0) ? 1 : 0;

        // Keep review_session counters in sync
        if ((int)$rs['target_gate_count_set'] !== $count_set ||
            (int)$rs['target_gate_count_required'] !== $count_required ||
            (int)$rs['target_gate_satisfied'] !== $satisfied) {
            $update = [
                'target_gate_count_set' => $count_set,
                'target_gate_count_required' => $count_required,
                'target_gate_satisfied' => $satisfied,
            ];
            if ($satisfied && empty($rs['target_gate_satisfied_at'])) {
                $update['target_gate_satisfied_at'] = date('Y-m-d H:i:s');
            }
            $this->db->where('id', $review_session_id)->update('review_session', $update);
        }

        return [
            'ok' => true,
            'gate' => 'required',
            'next_quarter' => $next,
            'count_required' => $count_required,
            'count_set' => $count_set,
            'satisfied' => (bool)$satisfied,
            'tasks' => $tasks,
        ];
    }

    // -------------------------------------------------------------------
    // SAVE ONE TARGET (UI calls per save)
    // -------------------------------------------------------------------

    public function set_target($review_session_id, $set_by_uid, $set_for_uid, $cluster_id, $category, $target_rs_lakh, $rationale_text = NULL, $prev_actual = NULL)
    {
        $target_rs_lakh = (float)$target_rs_lakh;
        if ($target_rs_lakh <= 0) {
            return ['ok' => false, 'error' => 'Target must be greater than zero.'];
        }

        $rs = $this->db->where('id', $review_session_id)
                       ->get('review_session')->row_array();
        if (!$rs) return ['ok' => false, 'error' => 'Review session not found.'];
        if (!$rs['target_gate_required']) {
            return ['ok' => false, 'error' => 'This review does not require target setting.'];
        }
        if ($rs['status'] === 'submitted') {
            return ['ok' => false, 'error' => 'Cannot edit targets after review submission.'];
        }
        if ((int)$rs['manager_uid'] !== (int)$set_by_uid) {
            return ['ok' => false, 'error' => 'Only the reviewing manager can set targets.'];
        }

        // Confirm set_for_uid is actually a direct report of this manager
        $direct = $this->rh->direct_reports($set_by_uid);
        $is_direct = false;
        foreach ($direct as $d) {
            if ((int)$d['employee_uid'] === (int)$set_for_uid) { $is_direct = true; break; }
        }
        if (!$is_direct) {
            return ['ok' => false, 'error' => 'Target can only be set for a direct report.'];
        }

        // Override any prior unoverridden audit row for the same (review, employee, category)
        $this->db->where('review_session_id', $review_session_id)
                 ->where('set_for_uid', $set_for_uid)
                 ->where('category', $category)
                 ->where('overridden_at', NULL)
                 ->update('quarter_target_audit', [
                     'overridden_at' => date('Y-m-d H:i:s'),
                     'overridden_by_uid' => $set_by_uid,
                 ]);

        // Insert fresh audit row (trigger upserts revenue_target_matrix)
        $this->db->insert('quarter_target_audit', [
            'review_session_id' => $review_session_id,
            'next_quarter_config_id' => $rs['next_quarter_config_id'],
            'set_by_uid' => $set_by_uid,
            'set_for_uid' => $set_for_uid,
            'cluster_id' => $cluster_id,
            'category' => $category,
            'target_rs_lakh' => $target_rs_lakh,
            'prev_quarter_actual_rs_lakh' => $prev_actual,
            'rationale_text' => $rationale_text,
            'set_at' => date('Y-m-d H:i:s'),
        ]);
        $audit_id = $this->db->insert_id();

        // Recompute count via pending_tasks
        $refresh = $this->pending_tasks($review_session_id);

        return [
            'ok' => true,
            'audit_id' => $audit_id,
            'count_required' => $refresh['count_required'],
            'count_set' => $refresh['count_set'],
            'satisfied' => $refresh['satisfied'],
        ];
    }

    // -------------------------------------------------------------------
    // BLOCKING CHECK CALLED BY REVIEW SUBMIT ENDPOINT
    // -------------------------------------------------------------------

    public function check_can_submit($review_session_id)
    {
        $rs = $this->db->where('id', $review_session_id)
                       ->get('review_session')->row_array();
        if (!$rs) return ['ok' => false, 'error' => 'Review session not found.'];

        if (!$rs['target_gate_required']) {
            return ['ok' => true, 'message' => 'Gate not required for this review.'];
        }
        // Re-run pending_tasks to make sure counter is fresh
        $refresh = $this->pending_tasks($review_session_id);
        if (!$refresh['satisfied']) {
            $missing = $refresh['count_required'] - $refresh['count_set'];
            return [
                'ok' => false,
                'error' => sprintf('Cannot submit. %d direct report targets still missing for %s.',
                                    $missing, $refresh['next_quarter']['quarter_label']),
                'count_required' => $refresh['count_required'],
                'count_set' => $refresh['count_set'],
            ];
        }
        return ['ok' => true];
    }

    // -------------------------------------------------------------------
    // SUMMARY for cron
    // -------------------------------------------------------------------

    public function v_target_gate_pending()
    {
        return $this->db->get('v_target_gate_pending')->result_array();
    }
}
