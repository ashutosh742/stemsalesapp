<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProductivityController
 * Blitz 30 May 2026 - Agent C
 *
 * Endpoints:
 *   GET /api/productivity/bd_today?uid={uid}
 *   GET /api/productivity/cm_today?uid={uid}
 *
 * ----------------------------------------------------------------
 * BD TODAY  (bd_today):
 *   Source: tblcallevents (real event log)
 *   - planned_task_count : rows WHERE user_id=uid AND DATE(date)=today AND plan=1
 *   - done_count         : rows WHERE user_id=uid AND DATE(date)=today AND actontaken='yes'
 *   - meetings_count     : rows WHERE user_id=uid AND DATE(date)=today AND actiontype_id IN (3,4)
 *   - positive_conversions_today : lead_progression_log rows WHERE bd_uid=uid
 *                                  AND to_status >= 6 AND DATE(created_at)=today
 *   - wallet_spent_rs    : cash_log SUM(cash) WHERE uid=uid AND type='debit'
 *                          AND DATE(created_at)=today
 *
 * CM TODAY  (cm_today):
 *   Source:
 *   - BDs supervised: user_cluster_mapping (same cluster_id as CM, user_type=3)
 *   - approvals_pending: planner_approved WHERE approved_status IS NULL
 *                        AND user_id IN (cluster BDs)
 *   - moms_approved_today: tblcallevents WHERE mom_approved='yes'
 *                          AND DATE(approved_date)=today
 *                          AND user_id IN (cluster BDs)
 *   - team aggregate planned/done: tblcallevents for cluster BDs today
 *   - cm_productivity_daily for today's signoff data if present
 *
 * Supervision chain evidence:
 *   user_cluster_mapping confirmed: CM uid 100070 is cluster_id=2
 *   BDs in cluster 2: 100177, 100191, 100194, 100207, 1000209
 *   No direct admin_id chain from BD to CM found (BDs have admin_id=2/45,
 *   which are Admin-type, not CM-type). Cluster is the correct relationship.
 */
class ProductivityController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* ------------------------------------------------------------------ */
    /* Bearer auth                                                         */
    /* ------------------------------------------------------------------ */
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected || !hash_equals($expected, $tok)) {
            $this->output->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'bad_token']));
            return false;
        }
        return true;
    }

    private function _json($payload) {
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /* ------------------------------------------------------------------ */
    /* GET /api/productivity/bd_today?uid={uid}                           */
    /* ------------------------------------------------------------------ */
    public function bd_today() {
        if (!$this->_bearer()) return;

        $uid = (int)$this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json([
                'ok'      => false,
                'success' => false,
                'stub'    => false,
                'error'   => 'uid query param required',
                'route'   => 'api/productivity/bd_today',
                'generated_at' => date('c'),
            ]);
        }

        $today = date('Y-m-d');

        /* -- Verify user exists ---------------------------------------- */
        $user = $this->db->query(
            'SELECT user_id, name, type_id FROM user_details WHERE user_id = ?',
            [$uid]
        )->row();

        if (!$user) {
            return $this->_json([
                'ok'      => false,
                'success' => false,
                'stub'    => false,
                'error'   => 'uid not found in user_details',
                'route'   => 'api/productivity/bd_today',
                'generated_at' => date('c'),
            ]);
        }

        /* -- Task counts from tblcallevents (plan=1 = planned task) ---- */
        $task_row = $this->db->query(
            'SELECT
                COUNT(*) AS total_events,
                SUM(CASE WHEN plan = 1 THEN 1 ELSE 0 END) AS planned_task_count,
                SUM(CASE WHEN actontaken = ? THEN 1 ELSE 0 END) AS done_count,
                SUM(CASE WHEN actiontype_id IN (3, 4) THEN 1 ELSE 0 END) AS meetings_count
             FROM tblcallevents
             WHERE user_id = ?
               AND DATE(date) = ?',
            ['yes', $uid, $today]
        )->row();

        $planned_task_count = (int)($task_row ? $task_row->planned_task_count : 0);
        $done_count         = (int)($task_row ? $task_row->done_count         : 0);
        $meetings_count     = (int)($task_row ? $task_row->meetings_count     : 0);

        /* -- Positive conversions today: lead_progression_log to_status >= 6 */
        /* cstatus scale 1-13; 6+ = Tentative, Positive, Very Positive,      */
        /* Proposal, In-Review, Won, etc.                                      */
        $conv_row = $this->db->query(
            'SELECT COUNT(*) AS positive_conversions
             FROM lead_progression_log
             WHERE bd_uid = ?
               AND to_status >= 6
               AND DATE(created_at) = ?',
            [$uid, $today]
        )->row();
        $positive_conversions_today = (int)($conv_row ? $conv_row->positive_conversions : 0);

        /* -- Wallet spent today (cash_log debit rows) ------------------ */
        $wallet_row = $this->db->query(
            'SELECT COALESCE(SUM(cash), 0) AS wallet_spent_rs
             FROM cash_log
             WHERE uid = ?
               AND type = ?
               AND DATE(created_at) = ?',
            [$uid, 'debit', $today]
        )->row();
        $wallet_spent_rs = (int)($wallet_row ? $wallet_row->wallet_spent_rs : 0);

        return $this->_json([
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'rows'    => [[
                'uid'                        => $uid,
                'name'                       => $user->name,
                'date'                       => $today,
                'planned_task_count'         => $planned_task_count,
                'done_count'                 => $done_count,
                'meetings_count'             => $meetings_count,
                'positive_conversions_today' => $positive_conversions_today,
                'wallet_spent_rs'            => $wallet_spent_rs,
            ]],
            'data'    => ['count' => 1],
            'route'   => 'api/productivity/bd_today',
            'generated_at' => date('c'),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* GET /api/productivity/cm_today?uid={uid}                           */
    /* ------------------------------------------------------------------ */
    public function cm_today() {
        if (!$this->_bearer()) return;

        $uid = (int)$this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json([
                'ok'      => false,
                'success' => false,
                'stub'    => false,
                'error'   => 'uid query param required',
                'route'   => 'api/productivity/cm_today',
                'generated_at' => date('c'),
            ]);
        }

        $today = date('Y-m-d');

        /* -- Verify user ------------------------------------------------ */
        $user = $this->db->query(
            'SELECT user_id, name, type_id FROM user_details WHERE user_id = ?',
            [$uid]
        )->row();

        if (!$user) {
            return $this->_json([
                'ok'      => false,
                'success' => false,
                'stub'    => false,
                'error'   => 'uid not found in user_details',
                'route'   => 'api/productivity/cm_today',
                'generated_at' => date('c'),
            ]);
        }

        /* -- Find CM's cluster_id(s) ------------------------------------ */
        /* user_cluster_mapping: CM uid 100070 -> cluster_id 2             */
        $cluster_rows = $this->db->query(
            'SELECT cluster_id FROM user_cluster_mapping
             WHERE user_id = ? AND user_type = 13',
            [$uid]
        )->result();

        $cluster_ids = [];
        foreach ($cluster_rows as $cr) {
            $cluster_ids[] = (int)$cr->cluster_id;
        }

        /* -- BDs in those clusters ------------------------------------- */
        $bd_uids = [];
        if (!empty($cluster_ids)) {
            /* build IN clause safely with placeholders */
            $placeholders = implode(',', array_fill(0, count($cluster_ids), '?'));
            $bd_rows = $this->db->query(
                "SELECT DISTINCT user_id FROM user_cluster_mapping
                 WHERE cluster_id IN ({$placeholders})
                   AND user_type = 3",
                $cluster_ids
            )->result();
            foreach ($bd_rows as $br) {
                $bd_uids[] = (int)$br->user_id;
            }
        }

        $bds_supervised_count = count($bd_uids);

        /* -- Approvals pending: planner_approved for cluster BDs ------- */
        $approvals_pending = 0;
        if (!empty($bd_uids)) {
            $ph2 = implode(',', array_fill(0, count($bd_uids), '?'));
            $ap_row = $this->db->query(
                "SELECT COUNT(*) AS pending_cnt
                 FROM planner_approved
                 WHERE approved_status IS NULL
                   AND user_id IN ({$ph2})",
                $bd_uids
            )->row();
            $approvals_pending = (int)($ap_row ? $ap_row->pending_cnt : 0);
        }

        /* -- Also count leave_requests pending for cluster BDs --------- */
        $leave_pending = 0;
        if (!empty($bd_uids)) {
            $ph3 = implode(',', array_fill(0, count($bd_uids), '?'));
            $lp_row = $this->db->query(
                "SELECT COUNT(*) AS leave_cnt
                 FROM leave_requests
                 WHERE status IN ('pending_manager', 'pending_admin')
                   AND user_id IN ({$ph3})",
                $bd_uids
            )->row();
            $leave_pending = (int)($lp_row ? $lp_row->leave_cnt : 0);
        }

        /* -- MoMs approved today by this CM (tblcallevents) ------------ */
        /* mom_approved field: 'yes' once CM signs off.                   */
        /* approved_by stores the UID who approved (string or int).       */
        $moms_approved_today_row = $this->db->query(
            "SELECT COUNT(*) AS moms_cnt
             FROM tblcallevents
             WHERE mom_approved = 'yes'
               AND DATE(approved_date) = ?
               AND approved_by = ?",
            [$today, (string)$uid]
        )->row();
        $moms_approved_today = (int)($moms_approved_today_row ? $moms_approved_today_row->moms_cnt : 0);

        /* -- Team aggregate planned/done from tblcallevents ------------ */
        $team_planned = 0;
        $team_done    = 0;
        $bd_details   = [];

        if (!empty($bd_uids)) {
            $ph4 = implode(',', array_fill(0, count($bd_uids), '?'));
            $params4 = array_merge($bd_uids, [$today]);
            $team_rows = $this->db->query(
                "SELECT
                    t.user_id,
                    ud.name,
                    SUM(CASE WHEN t.plan = 1 THEN 1 ELSE 0 END) AS planned,
                    SUM(CASE WHEN t.actontaken = 'yes' THEN 1 ELSE 0 END) AS done,
                    SUM(CASE WHEN t.actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings
                 FROM tblcallevents t
                 JOIN user_details ud ON ud.user_id = t.user_id
                 WHERE t.user_id IN ({$ph4})
                   AND DATE(t.date) = ?
                 GROUP BY t.user_id, ud.name",
                $params4
            )->result_array();

            foreach ($team_rows as $tr) {
                $team_planned += (int)$tr['planned'];
                $team_done    += (int)$tr['done'];
                $bd_details[]  = [
                    'bd_uid'   => (int)$tr['user_id'],
                    'bd_name'  => $tr['name'],
                    'planned'  => (int)$tr['planned'],
                    'done'     => (int)$tr['done'],
                    'meetings' => (int)$tr['meetings'],
                ];
            }
        }

        /* -- cm_productivity_daily for today --------------------------- */
        $cpd = $this->db->query(
            'SELECT review_touches, approvals_given, rejections,
                    mom_signoffs, bd_coverage_pct, score_pct
             FROM cm_productivity_daily
             WHERE cm_uid = ? AND for_date = ?',
            [$uid, $today]
        )->row();

        return $this->_json([
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'rows'    => [[
                'uid'                  => $uid,
                'name'                 => $user->name,
                'date'                 => $today,
                'cluster_ids'          => $cluster_ids,
                'bds_supervised_count' => $bds_supervised_count,
                'approvals_pending'    => $approvals_pending,
                'leave_requests_pending' => $leave_pending,
                'moms_approved_today'  => $moms_approved_today,
                'team_planned'         => $team_planned,
                'team_done'            => $team_done,
                'bd_breakdown'         => $bd_details,
                'cm_productivity_daily' => $cpd ? [
                    'review_touches'   => (int)$cpd->review_touches,
                    'approvals_given'  => (int)$cpd->approvals_given,
                    'rejections'       => (int)$cpd->rejections,
                    'mom_signoffs'     => (int)$cpd->mom_signoffs,
                    'bd_coverage_pct'  => (float)$cpd->bd_coverage_pct,
                    'score_pct'        => (float)$cpd->score_pct,
                ] : null,
            ]],
            'data'    => ['count' => 1],
            'route'   => 'api/productivity/cm_today',
            'generated_at' => date('c'),
        ]);
    }
}
