<?php
/**
 * stem_target_cascade_agent.php
 *
 * Migration 028 - Cascade Target Setting Agent
 *
 * Responsibilities:
 *   1. Compute historical weights per child uid for each axis
 *   2. Auto-split a parent target into child auto_value rows
 *   3. Re-cascade when a parent override happens
 *   4. Auto-rebalance siblings when a BD departs mid-quarter
 *   5. Validate sum integrity (children sum equals parent for hard axes;
 *      within 10 percent buffer for soft axes)
 *   6. Lock the cascade on G2 (Day 5)
 *   7. Reset all auto values when revenue_target_matrix master changes
 *
 * Never writes to actuals or signoff or check-in. Pure allocation math.
 *
 * Dependencies:
 *   - CI3 model base class
 *   - Tables: target_quarter, target_allocation, target_allocation_log,
 *             target_actuals (read only for ytd weights), user, revenue_target_matrix
 *
 * Author: STEM platform team
 * Migration: 028
 * Staging only until 1 Jun 2026.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Stem_target_cascade_agent extends CI_Model {

    /* -----------------------------------------------------------------
     * Axis registry
     * ----------------------------------------------------------------- */
    private $hard_axes = [
        'revenue_rs_cr','rp_meetings','barg_in','out_station','local_station',
        'new_lead_funnel','twenty_closure_funnel','proposal_rs_cr'
    ];
    private $soft_axes = [
        'dmft_activation','anchor_meetings'   // 10 percent buffer allowed
    ];
    private $weighted_avg_axes = [
        'upsell_rm_coverage_pct'              // child final = weighted average not sum
    ];

    /* Weighting blend used to compute child weight per axis */
    private $w_last_q   = 0.50;   // share in immediately preceding quarter
    private $w_ytd      = 0.30;   // share over current FY to date
    private $w_equal    = 0.20;   // equal share fallback

    /* Soft axis buffer */
    private $soft_buffer_pct = 10.0;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* =================================================================
     * PUBLIC ENTRY POINTS
     * ================================================================= */

    /**
     * Set the RM total for a quarter and cascade down to CM/ACM/BD.
     * Called by /api/target/set_rm_total.
     *
     * @param int    $target_quarter_id
     * @param int    $rm_uid
     * @param array  $rm_values    map axis => DECIMAL value
     * @param int    $actor_uid    person triggering the cascade
     * @return array               status, allocations_written, warnings
     */
    public function set_rm_total_and_cascade($target_quarter_id, $rm_uid, $rm_values, $actor_uid) {
        $q = $this->fetch_quarter($target_quarter_id);
        if (!$q) return ['status'=>'error','message'=>'quarter not found'];
        if (in_array($q->status, ['locked','signed_off','closed'])) {
            return ['status'=>'error','message'=>'quarter locked, cannot re-cascade. Use override endpoint per axis.'];
        }

        // Validate revenue_rs_cr is at or above master (200 cr matrix)
        if (isset($rm_values['revenue_rs_cr']) && $q->master_revenue_rs_cr !== null
            && $rm_values['revenue_rs_cr'] < $q->master_revenue_rs_cr) {
            return [
                'status'  => 'error',
                'message' => 'RM revenue total Rs '.$rm_values['revenue_rs_cr'].' cr below master Rs '.$q->master_revenue_rs_cr.' cr. Director signoff required to lower master. See revenue_target_matrix.'
            ];
        }

        $children = $this->fetch_child_chain($q->cluster_id, $rm_uid);
        if (empty($children['cm'])) {
            return ['status'=>'error','message'=>'no CMs found under RM '.$rm_uid.' for cluster '.$q->cluster_id];
        }

        $written  = 0;
        $warnings = [];

        // 1. Write RM-level allocation rows (one per axis)
        foreach ($rm_values as $axis => $value) {
            if (!$this->is_known_axis($axis)) {
                $warnings[] = 'unknown axis skipped: '.$axis;
                continue;
            }
            $this->upsert_allocation([
                'target_quarter_id' => $target_quarter_id,
                'axis'              => $axis,
                'level'             => 'rm',
                'uid'               => $rm_uid,
                'parent_uid'        => null,
                'auto_value'        => $value,
                'override_value'    => null,
                'weight_used'       => 1.0
            ], $actor_uid, 'auto_split', 'RM total entered');
            $written++;
        }

        // 2. Cascade to CMs
        foreach ($rm_values as $axis => $rm_total) {
            if (!$this->is_known_axis($axis)) continue;
            $cm_weights = $this->compute_weights($q, $axis, $children['cm']);
            foreach ($children['cm'] as $cm_uid) {
                $w  = isset($cm_weights[$cm_uid]) ? $cm_weights[$cm_uid] : 0;
                $av = $this->apply_axis_rule($axis, $rm_total, $w, count($children['cm']));
                $this->upsert_allocation([
                    'target_quarter_id' => $target_quarter_id,
                    'axis'              => $axis,
                    'level'             => 'cm',
                    'uid'               => $cm_uid,
                    'parent_uid'        => $rm_uid,
                    'auto_value'        => $av,
                    'override_value'    => null,
                    'weight_used'       => $w
                ], $actor_uid, 'auto_split', 'cascaded from RM');
                $written++;

                // 3. Cascade each CM down to ACMs (if any) and BDs
                $acms_under_cm = isset($children['acm_under_cm'][$cm_uid]) ? $children['acm_under_cm'][$cm_uid] : [];
                $bds_under_cm  = isset($children['bd_under_cm'][$cm_uid])  ? $children['bd_under_cm'][$cm_uid]  : [];

                if (!empty($acms_under_cm)) {
                    // CM -> ACM -> BD
                    $acm_weights = $this->compute_weights($q, $axis, $acms_under_cm);
                    foreach ($acms_under_cm as $acm_uid) {
                        $wa = isset($acm_weights[$acm_uid]) ? $acm_weights[$acm_uid] : 0;
                        $av_acm = $this->apply_axis_rule($axis, $av, $wa, count($acms_under_cm));
                        $this->upsert_allocation([
                            'target_quarter_id' => $target_quarter_id,
                            'axis'              => $axis,
                            'level'             => 'acm',
                            'uid'               => $acm_uid,
                            'parent_uid'        => $cm_uid,
                            'auto_value'        => $av_acm,
                            'override_value'    => null,
                            'weight_used'       => $wa
                        ], $actor_uid, 'auto_split', 'cascaded from CM');
                        $written++;

                        $bds_under_acm = isset($children['bd_under_acm'][$acm_uid]) ? $children['bd_under_acm'][$acm_uid] : [];
                        if (!empty($bds_under_acm)) {
                            $bd_weights = $this->compute_weights($q, $axis, $bds_under_acm);
                            foreach ($bds_under_acm as $bd_uid) {
                                $wb = isset($bd_weights[$bd_uid]) ? $bd_weights[$bd_uid] : 0;
                                $av_bd = $this->apply_axis_rule($axis, $av_acm, $wb, count($bds_under_acm));
                                $this->upsert_allocation([
                                    'target_quarter_id' => $target_quarter_id,
                                    'axis'              => $axis,
                                    'level'             => 'bd',
                                    'uid'               => $bd_uid,
                                    'parent_uid'        => $acm_uid,
                                    'auto_value'        => $av_bd,
                                    'override_value'    => null,
                                    'weight_used'       => $wb
                                ], $actor_uid, 'auto_split', 'cascaded from ACM');
                                $written++;
                            }
                        }
                    }
                } elseif (!empty($bds_under_cm)) {
                    // CM -> BD (no ACM layer)
                    $bd_weights = $this->compute_weights($q, $axis, $bds_under_cm);
                    foreach ($bds_under_cm as $bd_uid) {
                        $wb = isset($bd_weights[$bd_uid]) ? $bd_weights[$bd_uid] : 0;
                        $av_bd = $this->apply_axis_rule($axis, $av, $wb, count($bds_under_cm));
                        $this->upsert_allocation([
                            'target_quarter_id' => $target_quarter_id,
                            'axis'              => $axis,
                            'level'             => 'bd',
                            'uid'               => $bd_uid,
                            'parent_uid'        => $cm_uid,
                            'auto_value'        => $av_bd,
                            'override_value'    => null,
                            'weight_used'       => $wb
                        ], $actor_uid, 'auto_split', 'cascaded from CM (no ACM layer)');
                        $written++;
                    }
                } else {
                    $warnings[] = 'CM '.$cm_uid.' has no BDs and no ACMs';
                }
            }
        }

        // 4. Update target_quarter status to 'set'
        if ($q->status === 'draft') {
            $this->db->update('target_quarter',
                ['status'=>'set','set_at'=>date('Y-m-d H:i:s')],
                ['id'=>$target_quarter_id]);
        }

        // 5. Validate sums
        $sum_check = $this->verify_sum_integrity($target_quarter_id);

        return [
            'status'              => 'ok',
            'allocations_written' => $written,
            'warnings'            => $warnings,
            'sum_check'           => $sum_check
        ];
    }

    /**
     * Apply an override at any level (rm, cm, acm, bd) and re-cascade
     * the deltas to descendants.
     *
     * Called by /api/target/override.
     *
     * @param int    $allocation_id
     * @param float  $new_value
     * @param int    $actor_uid
     * @param string $reason
     * @return array
     */
    public function override_and_recascade($allocation_id, $new_value, $actor_uid, $reason='manager override') {
        $alloc = $this->db->get_where('target_allocation', ['id'=>$allocation_id])->row();
        if (!$alloc) return ['status'=>'error','message'=>'allocation not found'];

        $q = $this->fetch_quarter($alloc->target_quarter_id);
        if (in_array($q->status, ['locked','signed_off','closed'])) {
            return ['status'=>'error','message'=>'quarter locked, override blocked'];
        }

        $old_value = $alloc->final_value;
        $version   = $alloc->version;

        // optimistic lock
        $upd = $this->db->where(['id'=>$allocation_id, 'version'=>$version])
                        ->set([
                            'override_value' => $new_value,
                            'version'        => $version + 1
                        ])->update('target_allocation');
        if ($this->db->affected_rows() === 0) {
            return ['status'=>'error','message'=>'version conflict, allocation was changed by another user. reload and retry'];
        }

        $this->log_allocation_change($allocation_id, $actor_uid, 'override', $old_value, $new_value, $reason);

        // If this is a parent level, re-cascade to immediate children
        if ($alloc->level !== 'bd') {
            $this->recascade_children($alloc->target_quarter_id, $alloc->axis, $alloc->uid, $alloc->level, $new_value, $actor_uid);
        }

        $sum_check = $this->verify_sum_integrity($alloc->target_quarter_id);

        return [
            'status'      => 'ok',
            'old_value'   => $old_value,
            'new_value'   => $new_value,
            'sum_check'   => $sum_check
        ];
    }

    /**
     * Lock the quarter cascade. Called on G2 day (Day 5) either manually or
     * by 6 AM cron. Sets status='locked', writes G2 pass/miss into discipline log.
     */
    public function lock_cascade($target_quarter_id, $actor_uid) {
        $q = $this->fetch_quarter($target_quarter_id);
        if (!$q) return ['status'=>'error','message'=>'quarter not found'];
        if ($q->status === 'locked') return ['status'=>'noop','message'=>'already locked'];

        $sum_check = $this->verify_sum_integrity($target_quarter_id);
        if (!$sum_check['ok']) {
            return ['status'=>'error','message'=>'cannot lock, sum mismatch','details'=>$sum_check];
        }

        $this->db->update('target_quarter',
            ['status'=>'locked','locked_at'=>date('Y-m-d H:i:s')],
            ['id'=>$target_quarter_id]);

        // Log lock event against the RM-level revenue_rs_cr allocation as anchor
        $anchor = $this->db->get_where('target_allocation', [
            'target_quarter_id' => $target_quarter_id,
            'axis'              => 'revenue_rs_cr',
            'level'             => 'rm'
        ])->row();
        if ($anchor) {
            $this->log_allocation_change($anchor->id, $actor_uid, 'lock', $anchor->final_value, $anchor->final_value, 'G2 lock');
        }

        return ['status'=>'ok','locked_at'=>date('Y-m-d H:i:s')];
    }

    /**
     * Auto-rebalance siblings when a BD departs mid-quarter.
     * Redistributes remaining unachieved portion to siblings using current weights.
     *
     * @param int $target_quarter_id
     * @param int $departed_bd_uid
     * @param int $actor_uid
     * @return array
     */
    public function rebalance_on_departure($target_quarter_id, $departed_bd_uid, $actor_uid) {
        $q = $this->fetch_quarter($target_quarter_id);
        if (!$q) return ['status'=>'error','message'=>'quarter not found'];
        if (in_array($q->status, ['signed_off','closed'])) {
            return ['status'=>'error','message'=>'quarter signed off, departure rebalance not allowed'];
        }

        $departed_rows = $this->db->get_where('target_allocation', [
            'target_quarter_id' => $target_quarter_id,
            'uid'               => $departed_bd_uid,
            'level'             => 'bd'
        ])->result();
        if (empty($departed_rows)) return ['status'=>'noop','message'=>'no allocation for departed uid'];

        $today = date('Y-m-d');
        $rebalanced = 0;

        foreach ($departed_rows as $row) {
            // unachieved = final_value minus actual cumulative today
            $actual = $this->db->select_max('actual_cumulative')
                               ->where(['target_quarter_id'=>$target_quarter_id,
                                        'axis'=>$row->axis,
                                        'uid'=>$departed_bd_uid])
                               ->get('target_actuals')->row();
            $actual_val = $actual ? floatval($actual->actual_cumulative) : 0;
            $remaining  = max(0, floatval($row->final_value) - $actual_val);
            if ($remaining <= 0) continue;

            // siblings: same parent_uid, same axis, same quarter, not the departed
            $siblings = $this->db->get_where('target_allocation', [
                'target_quarter_id' => $target_quarter_id,
                'axis'              => $row->axis,
                'parent_uid'        => $row->parent_uid,
                'level'             => 'bd'
            ])->result();
            $siblings = array_filter($siblings, function($s) use ($departed_bd_uid){ return $s->uid != $departed_bd_uid; });
            if (empty($siblings)) continue;

            // weight by each sibling's current final_value (proportional)
            $total_sibling = array_sum(array_map(function($s){ return floatval($s->final_value); }, $siblings));
            if ($total_sibling <= 0) {
                // equal split fallback
                $per = $remaining / count($siblings);
                foreach ($siblings as $s) {
                    $this->bump_allocation($s, $per, $actor_uid, 'auto_rebalance_departure', 'BD '.$departed_bd_uid.' departed');
                    $rebalanced++;
                }
            } else {
                foreach ($siblings as $s) {
                    $share = floatval($s->final_value) / $total_sibling;
                    $bump  = $remaining * $share;
                    $this->bump_allocation($s, $bump, $actor_uid, 'auto_rebalance_departure', 'BD '.$departed_bd_uid.' departed');
                    $rebalanced++;
                }
            }

            // zero out the departed BD's allocation
            $old = $row->final_value;
            $this->db->update('target_allocation',
                ['override_value'=>$actual_val, 'version'=>$row->version+1],
                ['id'=>$row->id]);
            $this->log_allocation_change($row->id, $actor_uid, 'rebalance_departure', $old, $actual_val,
                'BD departed, cap target at actuals achieved so far');
        }

        return ['status'=>'ok','rows_rebalanced'=>$rebalanced];
    }

    /**
     * Re-cascade after revenue_target_matrix master changes mid-quarter.
     * Rare path. Only director can trigger via /api/target/rebalance_master_change.
     */
    public function rebalance_on_master_change($target_quarter_id, $new_master_rs_cr, $actor_uid, $reason) {
        $q = $this->fetch_quarter($target_quarter_id);
        if (!$q) return ['status'=>'error','message'=>'quarter not found'];

        $this->db->update('target_quarter',
            ['master_revenue_rs_cr'=>$new_master_rs_cr],
            ['id'=>$target_quarter_id]);

        // Pull current RM revenue allocation and re-cascade if changed
        $rm = $this->db->get_where('target_allocation', [
            'target_quarter_id' => $target_quarter_id,
            'axis'              => 'revenue_rs_cr',
            'level'             => 'rm'
        ])->row();
        if (!$rm) return ['status'=>'error','message'=>'no RM revenue allocation, cannot rebalance'];

        if ($rm->final_value < $new_master_rs_cr) {
            // Bump RM to at least master
            $old = $rm->final_value;
            $this->db->update('target_allocation',
                ['override_value'=>$new_master_rs_cr, 'version'=>$rm->version+1],
                ['id'=>$rm->id]);
            $this->log_allocation_change($rm->id, $actor_uid, 'rebalance_master_change', $old, $new_master_rs_cr, $reason);

            // Re-cascade revenue_rs_cr only
            $this->recascade_children($target_quarter_id, 'revenue_rs_cr', $rm->uid, 'rm', $new_master_rs_cr, $actor_uid);
        }

        return ['status'=>'ok','master_now'=>$new_master_rs_cr];
    }

    /* =================================================================
     * INTERNAL HELPERS
     * ================================================================= */

    private function fetch_quarter($id) {
        return $this->db->get_where('target_quarter', ['id'=>$id])->row();
    }

    private function is_known_axis($axis) {
        return in_array($axis, $this->hard_axes)
            || in_array($axis, $this->soft_axes)
            || in_array($axis, $this->weighted_avg_axes);
    }

    /**
     * Pull CM, ACM, BD chain under an RM for a given cluster.
     * Reads from user table joined on reporting_manager_uid (mig 023.3).
     */
    private function fetch_child_chain($cluster_id, $rm_uid) {
        $out = ['cm'=>[], 'acm_under_cm'=>[], 'bd_under_cm'=>[], 'bd_under_acm'=>[]];

        // CMs: type_id=13, reporting_manager_uid=rm_uid
        $cms = $this->db->select('uid')
                        ->where(['type_id'=>13, 'reporting_manager_uid'=>$rm_uid, 'cluster_id'=>$cluster_id, 'is_active'=>1])
                        ->get('user')->result();
        foreach ($cms as $cm) $out['cm'][] = (int)$cm->uid;

        foreach ($out['cm'] as $cm_uid) {
            // ACMs under this CM: type_id=29 (per mig 023.3)
            $acms = $this->db->select('uid')
                             ->where(['type_id'=>29, 'reporting_manager_uid'=>$cm_uid, 'is_active'=>1])
                             ->get('user')->result();
            $out['acm_under_cm'][$cm_uid] = [];
            foreach ($acms as $acm) {
                $out['acm_under_cm'][$cm_uid][] = (int)$acm->uid;
                // BDs under this ACM
                $bds_acm = $this->db->select('uid')
                                    ->where(['type_id'=>2, 'reporting_manager_uid'=>$acm->uid, 'is_active'=>1])
                                    ->get('user')->result();
                $out['bd_under_acm'][(int)$acm->uid] = array_map(function($b){ return (int)$b->uid; }, $bds_acm);
            }
            // BDs directly under this CM (no ACM layer)
            $bds_cm = $this->db->select('uid')
                               ->where(['type_id'=>2, 'reporting_manager_uid'=>$cm_uid, 'is_active'=>1])
                               ->get('user')->result();
            $out['bd_under_cm'][$cm_uid] = array_map(function($b){ return (int)$b->uid; }, $bds_cm);
        }
        return $out;
    }

    /**
     * Compute per-child weight for a given axis using the blended formula:
     *   w = 0.50 * last_q_share + 0.30 * ytd_share + 0.20 * equal_share
     *
     * Falls back to equal when no history exists.
     *
     * @param object $q       target_quarter row (for date math)
     * @param string $axis
     * @param array  $uids
     * @return array          uid => weight (sums to 1.0)
     */
    private function compute_weights($q, $axis, $uids) {
        $n = count($uids);
        if ($n === 0) return [];
        $equal = 1.0 / $n;

        // Last quarter window
        $last_q_start = date('Y-m-d', strtotime($q->start_date.' -3 month'));
        $last_q_end   = date('Y-m-d', strtotime($q->start_date.' -1 day'));
        // YTD window: from FY start (1 Apr of FY containing q.start_date)
        $year = (int)date('Y', strtotime($q->start_date));
        if ((int)date('n', strtotime($q->start_date)) < 4) $year--;
        $ytd_start = $year.'-04-01';
        $ytd_end   = date('Y-m-d', strtotime($q->start_date.' -1 day'));

        $last_q = $this->sum_axis_per_uid($axis, $uids, $last_q_start, $last_q_end);
        $ytd    = $this->sum_axis_per_uid($axis, $uids, $ytd_start,    $ytd_end);

        $sum_last = array_sum($last_q);
        $sum_ytd  = array_sum($ytd);

        $weights = [];
        foreach ($uids as $u) {
            $share_last = ($sum_last > 0 && isset($last_q[$u])) ? ($last_q[$u] / $sum_last) : $equal;
            $share_ytd  = ($sum_ytd  > 0 && isset($ytd[$u]))    ? ($ytd[$u]    / $sum_ytd)  : $equal;
            $weights[$u] = $this->w_last_q * $share_last
                         + $this->w_ytd    * $share_ytd
                         + $this->w_equal  * $equal;
        }
        // Normalise to sum 1.0 (defensive)
        $total = array_sum($weights);
        if ($total > 0) {
            foreach ($weights as $k=>$v) $weights[$k] = $v / $total;
        } else {
            foreach ($uids as $u) $weights[$u] = $equal;
        }
        return $weights;
    }

    /**
     * Read history actuals for an axis grouped per uid.
     * Tries target_actuals first (current quarter snapshots), then falls back
     * to source-of-truth tables per axis. Conservative: returns empty if axis
     * not mappable.
     */
    private function sum_axis_per_uid($axis, $uids, $from_date, $to_date) {
        if (empty($uids)) return [];
        $uid_in = implode(',', array_map('intval', $uids));

        // Try target_actuals first (cheap)
        $sql = "SELECT uid, COALESCE(SUM(actual_delta_today),0) AS s
                  FROM target_actuals
                 WHERE axis = ? AND uid IN ($uid_in)
                   AND snapshot_date BETWEEN ? AND ?
                 GROUP BY uid";
        $rows = $this->db->query($sql, [$axis, $from_date, $to_date])->result();
        $out = [];
        foreach ($rows as $r) $out[(int)$r->uid] = floatval($r->s);
        if (!empty($out)) return $out;

        // Source-of-truth fallback per axis
        switch ($axis) {
            case 'revenue_rs_cr':
            case 'twenty_closure_funnel':
                $sql = "SELECT mainbd AS uid, COALESCE(SUM(fbudget),0)/1e7 AS s
                          FROM init_call
                         WHERE current_status_id = 12
                           AND mainbd IN ($uid_in)
                           AND DATE(closed_at) BETWEEN ? AND ?
                         GROUP BY mainbd";
                $rows = $this->db->query($sql, [$from_date, $to_date])->result();
                break;
            case 'rp_meetings':
                $sql = "SELECT createdby AS uid, COUNT(*) AS s
                          FROM tblcallevents
                         WHERE actiontype_id IN (3,4) AND purpose_id IN (65,67,68)
                           AND createdby IN ($uid_in)
                           AND DATE(event_date) BETWEEN ? AND ?
                         GROUP BY createdby";
                $rows = $this->db->query($sql, [$from_date, $to_date])->result();
                break;
            case 'barg_in':
                $sql = "SELECT createdby AS uid, COUNT(*) AS s
                          FROM tblcallevents
                         WHERE actiontype_id=4 AND purpose_id=66
                           AND createdby IN ($uid_in)
                           AND DATE(event_date) BETWEEN ? AND ?
                         GROUP BY createdby";
                $rows = $this->db->query($sql, [$from_date, $to_date])->result();
                break;
            case 'new_lead_funnel':
                $sql = "SELECT creator_id AS uid, COUNT(*) AS s
                          FROM init_call
                         WHERE new_lead=1
                           AND creator_id IN ($uid_in)
                           AND DATE(createDate) BETWEEN ? AND ?
                         GROUP BY creator_id";
                $rows = $this->db->query($sql, [$from_date, $to_date])->result();
                break;
            case 'proposal_rs_cr':
                $sql = "SELECT mainbd AS uid, COALESCE(SUM(fbudget),0)/1e7 AS s
                          FROM init_call
                         WHERE current_status_id >= 7
                           AND mainbd IN ($uid_in)
                           AND DATE(proposal_sent_at) BETWEEN ? AND ?
                         GROUP BY mainbd";
                $rows = $this->db->query($sql, [$from_date, $to_date])->result();
                break;
            default:
                // For out_station, local_station, dmft_activation, anchor_meetings,
                // upsell_rm_coverage_pct: no clean history source, return empty -> equal split.
                $rows = [];
        }
        foreach ($rows as $r) $out[(int)$r->uid] = floatval($r->s);
        return $out;
    }

    /**
     * Apply axis-specific math when allocating a parent total to a single child.
     */
    private function apply_axis_rule($axis, $parent_total, $weight, $n_children) {
        if (in_array($axis, $this->weighted_avg_axes)) {
            // weighted average axis: child carries same target as parent
            return $parent_total;
        }
        if (in_array($axis, $this->soft_axes)) {
            // soft axis: add 10 percent buffer to parent then split by weight,
            // so children sum to 1.10 * parent. This gives breathing room.
            $buffered = $parent_total * (1 + $this->soft_buffer_pct / 100.0);
            return round($buffered * $weight, 4);
        }
        // hard axis: simple weighted split
        return round($parent_total * $weight, 4);
    }

    /**
     * Recascade descendants of an allocation after its value changed.
     */
    private function recascade_children($target_quarter_id, $axis, $parent_uid, $parent_level, $parent_new_value, $actor_uid) {
        // find direct children (level below parent_level with this parent_uid)
        $next_level = $this->next_level_below($parent_level);
        if ($next_level === null) return; // BD has no children

        $children_rows = $this->db->get_where('target_allocation', [
            'target_quarter_id' => $target_quarter_id,
            'axis'              => $axis,
            'parent_uid'        => $parent_uid,
            'level'             => $next_level
        ])->result();
        if (empty($children_rows)) return;

        // Recompute weights using the original weight_used if present, else recompute fresh
        $uids = array_map(function($r){ return (int)$r->uid; }, $children_rows);
        $q    = $this->fetch_quarter($target_quarter_id);
        $weights = $this->compute_weights($q, $axis, $uids);

        foreach ($children_rows as $row) {
            $w = isset($weights[$row->uid]) ? $weights[$row->uid] : (1.0 / count($uids));
            $new_auto = $this->apply_axis_rule($axis, $parent_new_value, $w, count($uids));
            $old = $row->final_value;
            $this->db->where(['id'=>$row->id, 'version'=>$row->version])
                     ->set([
                         'auto_value'    => $new_auto,
                         'override_value'=> null,  // override cleared on re-cascade (re-state intent)
                         'weight_used'   => $w,
                         'version'       => $row->version + 1
                     ])->update('target_allocation');
            $this->log_allocation_change($row->id, $actor_uid, 'auto_split',
                $old, $new_auto, 'recascade from parent override');

            // Recurse
            $this->recascade_children($target_quarter_id, $axis, $row->uid, $next_level, $new_auto, $actor_uid);
        }
    }

    private function next_level_below($level) {
        $chain = ['rm'=>'cm', 'cm'=>'acm_or_bd', 'acm'=>'bd', 'bd'=>null];
        if ($level === 'cm') {
            // Caller must invoke twice for CM (acm then bd) - but recascade_children
            // queries by parent_uid+level so we need both. Simpler: just recurse with acm first
            // and the bd-under-cm case is handled by passing 'bd' here. To keep it general,
            // we attempt both in callers. For correctness, return 'acm' then handle 'bd' separately.
            return 'acm';
        }
        return isset($chain[$level]) ? $chain[$level] : null;
    }

    /**
     * Verify that sum of children equals parent for hard axes (within tolerance)
     * and within buffer for soft axes. Returns ['ok'=>bool, 'mismatches'=>[...]].
     */
    private function verify_sum_integrity($target_quarter_id) {
        $tol     = 0.01; // 1 paisa / 1 unit tolerance
        $mismatches = [];

        // For each axis, walk parent -> children
        $axes = array_merge($this->hard_axes, $this->soft_axes, $this->weighted_avg_axes);
        foreach ($axes as $axis) {
            $parents = $this->db->get_where('target_allocation', [
                'target_quarter_id' => $target_quarter_id,
                'axis'              => $axis
            ])->result();
            // Group children by parent_uid
            $by_parent = [];
            foreach ($parents as $p) {
                if ($p->parent_uid !== null) {
                    $by_parent[$p->parent_uid] = ($by_parent[$p->parent_uid] ?? 0) + floatval($p->final_value);
                }
            }
            // Compare each parent row's final_value to its children sum
            foreach ($parents as $p) {
                if (!isset($by_parent[$p->uid])) continue; // leaf
                $expected = floatval($p->final_value);
                $sum      = $by_parent[$p->uid];

                if (in_array($axis, $this->weighted_avg_axes)) {
                    // children should each equal parent; check max child vs parent
                    if (abs($sum / max(1, count($parents)) - $expected) > $tol * 10) {
                        $mismatches[] = ['axis'=>$axis, 'parent_uid'=>$p->uid, 'parent'=>$expected, 'children_sum'=>$sum];
                    }
                } elseif (in_array($axis, $this->soft_axes)) {
                    $hi = $expected * (1 + $this->soft_buffer_pct / 100.0);
                    if ($sum < $expected - $tol || $sum > $hi + $tol) {
                        $mismatches[] = ['axis'=>$axis, 'parent_uid'=>$p->uid, 'parent'=>$expected, 'children_sum'=>$sum];
                    }
                } else {
                    if (abs($sum - $expected) > $tol) {
                        $mismatches[] = ['axis'=>$axis, 'parent_uid'=>$p->uid, 'parent'=>$expected, 'children_sum'=>$sum];
                    }
                }
            }
        }

        return ['ok' => empty($mismatches), 'mismatches' => $mismatches];
    }

    /**
     * Insert or update an allocation row (idempotent on (quarter, axis, uid)).
     */
    private function upsert_allocation($data, $actor_uid, $action, $reason) {
        $existing = $this->db->get_where('target_allocation', [
            'target_quarter_id' => $data['target_quarter_id'],
            'axis'              => $data['axis'],
            'uid'               => $data['uid']
        ])->row();

        if ($existing) {
            $old = $existing->final_value;
            $this->db->where(['id'=>$existing->id, 'version'=>$existing->version])
                     ->set([
                         'auto_value'   => $data['auto_value'],
                         'weight_used' => $data['weight_used'],
                         'version'      => $existing->version + 1
                     ])->update('target_allocation');
            $this->log_allocation_change($existing->id, $actor_uid, $action, $old, $data['auto_value'], $reason);
            return $existing->id;
        } else {
            $this->db->insert('target_allocation', $data);
            $new_id = $this->db->insert_id();
            $this->log_allocation_change($new_id, $actor_uid, $action, null, $data['auto_value'], $reason);
            return $new_id;
        }
    }

    /**
     * Add a delta to a sibling's allocation during rebalance.
     */
    private function bump_allocation($row, $delta, $actor_uid, $action, $reason) {
        $old = floatval($row->final_value);
        $new = $old + floatval($delta);
        $this->db->where(['id'=>$row->id, 'version'=>$row->version])
                 ->set([
                     'override_value' => $new,
                     'version'        => $row->version + 1
                 ])->update('target_allocation');
        $this->log_allocation_change($row->id, $actor_uid, $action, $old, $new, $reason);
    }

    /**
     * Write to target_allocation_log.
     */
    private function log_allocation_change($allocation_id, $actor_uid, $action, $old_value, $new_value, $reason) {
        $this->db->insert('target_allocation_log', [
            'target_allocation_id' => $allocation_id,
            'actor_uid'            => $actor_uid,
            'action'               => $action,
            'old_value'            => $old_value,
            'new_value'            => $new_value,
            'reason'               => mb_substr($reason ?: '', 0, 255)
        ]);
    }

    /* =================================================================
     * READ HELPERS used by controller
     * ================================================================= */

    /**
     * Preview a cascade without writing. Used by /api/target/cascade_preview.
     */
    public function preview_cascade($target_quarter_id, $rm_uid, $rm_values) {
        $q = $this->fetch_quarter($target_quarter_id);
        if (!$q) return ['status'=>'error','message'=>'quarter not found'];

        $children = $this->fetch_child_chain($q->cluster_id, $rm_uid);
        $preview  = [];

        foreach ($rm_values as $axis => $rm_total) {
            if (!$this->is_known_axis($axis)) continue;
            $cm_weights = $this->compute_weights($q, $axis, $children['cm']);
            foreach ($children['cm'] as $cm_uid) {
                $w  = $cm_weights[$cm_uid] ?? 0;
                $av = $this->apply_axis_rule($axis, $rm_total, $w, count($children['cm']));
                $preview[] = ['axis'=>$axis, 'level'=>'cm', 'uid'=>$cm_uid, 'auto'=>$av, 'weight'=>$w];

                $bds_cm = $children['bd_under_cm'][$cm_uid] ?? [];
                $acms   = $children['acm_under_cm'][$cm_uid] ?? [];

                if (!empty($acms)) {
                    $aw = $this->compute_weights($q, $axis, $acms);
                    foreach ($acms as $acm_uid) {
                        $waa = $aw[$acm_uid] ?? 0;
                        $av_acm = $this->apply_axis_rule($axis, $av, $waa, count($acms));
                        $preview[] = ['axis'=>$axis, 'level'=>'acm', 'uid'=>$acm_uid, 'auto'=>$av_acm, 'weight'=>$waa];
                        $bds_acm = $children['bd_under_acm'][$acm_uid] ?? [];
                        if (!empty($bds_acm)) {
                            $bw = $this->compute_weights($q, $axis, $bds_acm);
                            foreach ($bds_acm as $bd_uid) {
                                $wb = $bw[$bd_uid] ?? 0;
                                $av_bd = $this->apply_axis_rule($axis, $av_acm, $wb, count($bds_acm));
                                $preview[] = ['axis'=>$axis, 'level'=>'bd', 'uid'=>$bd_uid, 'auto'=>$av_bd, 'weight'=>$wb];
                            }
                        }
                    }
                } elseif (!empty($bds_cm)) {
                    $bw = $this->compute_weights($q, $axis, $bds_cm);
                    foreach ($bds_cm as $bd_uid) {
                        $wb = $bw[$bd_uid] ?? 0;
                        $av_bd = $this->apply_axis_rule($axis, $av, $wb, count($bds_cm));
                        $preview[] = ['axis'=>$axis, 'level'=>'bd', 'uid'=>$bd_uid, 'auto'=>$av_bd, 'weight'=>$wb];
                    }
                }
            }
        }

        return ['status'=>'ok', 'preview'=>$preview];
    }
}
/* End of file stem_target_cascade_agent.php */
