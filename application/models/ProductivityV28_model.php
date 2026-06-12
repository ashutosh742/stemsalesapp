<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProductivityV28_model
 *
 * BD/CM productivity scoring model for STEM CRM v2.8.
 * Budget baseline: 540 minutes per working day (9 hours).
 *
 * Verified schema (29 May 2026):
 *   tblcallevents       user_id, cid_id, date, actiontype_id, purpose_id,
 *                       plan_time, initiate_time, complete_time, approved_status
 *   cron_action_minutes action_id (PK), minutes
 *   init_call           id, mainbd, cstatus, createDate, updated_at, cmpid_id
 *   company_master      id, compname
 *   user                uid (PK), name, type_id, admin_id, status
 *   stuck_threshold     cstatus (PK), days
 *   stuck_leads_daily   id, for_date, cid_id, bd_uid, cstatus, days_in_stage,
 *                       threshold_days, last_touch_date, created_at
 *   bd_productivity_daily  id, bd_uid, for_date, planned_min, executed_min,
 *                          idle_min, budget_min, score_pct, tasks_planned,
 *                          tasks_completed, tasks_skipped, created_at
 *   cm_productivity_daily  id, cm_uid, for_date, review_touches,
 *                          approvals_given, rejections, mom_signoffs,
 *                          bd_coverage_pct, score_pct, created_at
 */
class ProductivityV28_model extends CI_Model {

    const BUDGET_MIN = 540;

    public function __construct()
    {
        parent::__construct();
    }

    // -------------------------------------------------------------------------
    // BD SCORING
    // -------------------------------------------------------------------------

    public function score_bd_day($bd_uid, $for_date)
    {
        $bd_uid   = (int) $bd_uid;
        $for_date = $this->db->escape_str($for_date);

        // Planned tasks: rows whose plan_time (or date) falls on $for_date.
        $planned_sql = "
            SELECT
                COALESCE(SUM(cam.minutes), 0) AS planned_min,
                COUNT(ce.id)                  AS tasks_planned
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            WHERE ce.user_id = {$bd_uid}
              AND (
                    DATE(ce.plan_time) = '{$for_date}'
                 OR DATE(ce.date)      = '{$for_date}'
              )
        ";
        $row = $this->db->query($planned_sql)->row();
        $planned_min   = (int) ($row->planned_min   ?? 0);
        $tasks_planned = (int) ($row->tasks_planned ?? 0);

        // Executed: rows completed on $for_date.
        $exec_sql = "
            SELECT
                COALESCE(SUM(
                    GREATEST(0, TIMESTAMPDIFF(MINUTE, ce.initiate_time, ce.complete_time))
                ), 0) AS executed_min,
                COUNT(ce.id) AS tasks_completed
            FROM tblcallevents ce
            WHERE ce.user_id = {$bd_uid}
              AND ce.complete_time IS NOT NULL
              AND DATE(ce.complete_time) = '{$for_date}'
        ";
        $row = $this->db->query($exec_sql)->row();
        $executed_min    = (int) ($row->executed_min    ?? 0);
        $tasks_completed = (int) ($row->tasks_completed ?? 0);

        $tasks_skipped = max(0, $tasks_planned - $tasks_completed);
        $budget_min    = self::BUDGET_MIN;
        $idle_min      = max(0, $budget_min - $executed_min);
        $score_pct     = ($budget_min > 0)
                         ? round(($executed_min / $budget_min) * 100, 2)
                         : 0.00;

        return [
            'bd_uid'          => $bd_uid,
            'for_date'        => $for_date,
            'budget_min'      => $budget_min,
            'planned_min'     => $planned_min,
            'executed_min'    => $executed_min,
            'idle_min'        => $idle_min,
            'score_pct'       => $score_pct,
            'tasks_planned'   => $tasks_planned,
            'tasks_completed' => $tasks_completed,
            'tasks_skipped'   => $tasks_skipped,
        ];
    }

    // -------------------------------------------------------------------------
    // CM SCORING
    // -------------------------------------------------------------------------

    /**
     * CM productivity is measured from tblcallevents.approved_status column
     * (NOT a separate approved_status table). approved_status is varchar
     * holding values like 'approved', 'rejected', '' for pending.
     * mom_received and mom_approved are separate signals on the same table.
     */
    public function score_cm_day($cm_uid, $for_date)
    {
        $cm_uid   = (int) $cm_uid;
        $for_date = $this->db->escape_str($for_date);

        // Touches: any tblcallevents row updated by this CM today (we use the
        // approved_by varchar to scope - fallback: count rows whose approved
        // datetime is today and approved_by matches the CM uid as string).
        $touch_sql = "
            SELECT
                COUNT(*) AS total_touches,
                SUM(CASE WHEN ce.approved_status = 'approved' THEN 1 ELSE 0 END) AS approvals,
                SUM(CASE WHEN ce.approved_status = 'rejected' THEN 1 ELSE 0 END) AS rejections,
                SUM(CASE WHEN ce.mom_approved = 1 THEN 1 ELSE 0 END) AS mom_signoffs,
                COUNT(DISTINCT ce.cid_id) AS leads_touched
            FROM tblcallevents ce
            WHERE ce.approved_by = '{$cm_uid}'
              AND DATE(ce.approved_date) = '{$for_date}'
        ";
        $row = $this->db->query($touch_sql)->row();
        $total_touches = (int) ($row->total_touches ?? 0);
        $approvals     = (int) ($row->approvals     ?? 0);
        $rejections    = (int) ($row->rejections    ?? 0);
        $mom_signoffs  = (int) ($row->mom_signoffs  ?? 0);
        $leads_touched = (int) ($row->leads_touched ?? 0);

        // CM coverage denominator: open leads under any BD whose admin_id is this CM
        $open_sql = "
            SELECT COUNT(*) AS open_count
            FROM init_call l
            WHERE l.cstatus NOT IN (12, 13)
              AND l.mainbd IN (SELECT uid FROM user WHERE admin_id = {$cm_uid})
        ";
        $row = $this->db->query($open_sql)->row();
        $open_count = (int) ($row->open_count ?? 0);

        $denom         = max(1, $total_touches);
        $score_pct     = round((($approvals + $mom_signoffs) / $denom) * 100, 2);
        $denom_open    = max(1, $open_count);
        $bd_coverage_pct = round(($leads_touched / $denom_open) * 100, 2);

        return [
            'cm_uid'           => $cm_uid,
            'for_date'         => $for_date,
            'review_touches'   => $total_touches,
            'approvals_given'  => $approvals,
            'rejections'       => $rejections,
            'mom_signoffs'     => $mom_signoffs,
            'bd_coverage_pct'  => $bd_coverage_pct,
            'score_pct'        => $score_pct,
        ];
    }

    // -------------------------------------------------------------------------
    // STUCK LEADS DETECTION
    // -------------------------------------------------------------------------

    public function detect_stuck_leads($for_date)
    {
        $for_date = $this->db->escape_str($for_date);

        // Set-based, idempotent snapshot builder. Replaces the prior full-scan
        // HAVING SELECT plus per-row N+1 PHP write loop with two statements.
        //
        // days_in_stage = DATEDIFF(for_date, last touch), where last touch is
        // init_call.updated_at (stage-entry proxy) falling back to createDate.
        // The DATEDIFF filter MUST stay in WHERE (not rewritten as an INTERVAL
        // subtraction): updated_at carries a time component, so the INTERVAL
        // form is off-by-rows. DATEDIFF in WHERE is the result-equivalent form.
        // bd_uid uses COALESCE(l.mainbd, 0): stuck_leads_daily.bd_uid is NOT
        // NULL and init_call.mainbd can be NULL. The prior PHP loop cast it via
        // (int) which mapped NULL to 0, so COALESCE(..,0) preserves that exact
        // behavior (and the 54000-row result-equivalence).

        // Step A: clear this day's snapshot so re-runs are idempotent.
        $this->db->query("
            DELETE FROM stuck_leads_daily
            WHERE for_date = '{$for_date}'
        ");

        // Step B: insert the stuck rows in a single set-based pass.
        $this->db->query("
            INSERT INTO stuck_leads_daily
                (for_date, cid_id, bd_uid, cstatus, days_in_stage,
                 threshold_days, last_touch_date)
            SELECT
                '{$for_date}',
                l.id,
                COALESCE(l.mainbd, 0),
                l.cstatus,
                DATEDIFF('{$for_date}', COALESCE(l.updated_at, l.createDate)),
                COALESCE(st.days, 14),
                DATE(COALESCE(l.updated_at, l.createDate))
            FROM init_call l
            LEFT JOIN stuck_threshold st ON st.cstatus = l.cstatus
            WHERE l.cstatus NOT IN (12, 13)
              AND DATEDIFF('{$for_date}', COALESCE(l.updated_at, l.createDate))
                  >= COALESCE(st.days, 14)
        ");

        return (int) $this->db->affected_rows();
    }

    // -------------------------------------------------------------------------
    // UPSERT HELPERS
    // -------------------------------------------------------------------------

    public function upsert_bd_daily($row)
    {
        $sql = "
            SELECT id FROM bd_productivity_daily
            WHERE bd_uid = " . (int) $row['bd_uid'] . "
              AND for_date = '" . $this->db->escape_str($row['for_date']) . "'
            LIMIT 1
        ";
        $existing = $this->db->query($sql)->row();

        if ($existing) {
            $this->db->where('id', $existing->id);
            return $this->db->update('bd_productivity_daily', $row);
        }
        return $this->db->insert('bd_productivity_daily', $row);
    }

    public function upsert_cm_daily($row)
    {
        $sql = "
            SELECT id FROM cm_productivity_daily
            WHERE cm_uid = " . (int) $row['cm_uid'] . "
              AND for_date = '" . $this->db->escape_str($row['for_date']) . "'
            LIMIT 1
        ";
        $existing = $this->db->query($sql)->row();

        if ($existing) {
            $this->db->where('id', $existing->id);
            return $this->db->update('cm_productivity_daily', $row);
        }
        return $this->db->insert('cm_productivity_daily', $row);
    }

    // -------------------------------------------------------------------------
    // READ HELPERS
    // -------------------------------------------------------------------------

    public function get_bd_daily_row($bd_uid, $for_date)
    {
        $sql = "
            SELECT * FROM bd_productivity_daily
            WHERE bd_uid = " . (int) $bd_uid . "
              AND for_date = '" . $this->db->escape_str($for_date) . "'
            LIMIT 1
        ";
        $r = $this->db->query($sql)->row_array();
        return $r ?: null;
    }

    public function get_cm_daily_row($cm_uid, $for_date)
    {
        $sql = "
            SELECT * FROM cm_productivity_daily
            WHERE cm_uid = " . (int) $cm_uid . "
              AND for_date = '" . $this->db->escape_str($for_date) . "'
            LIMIT 1
        ";
        $r = $this->db->query($sql)->row_array();
        return $r ?: null;
    }

    public function get_stuck_leads_for_date($for_date, $bd_uid = null)
    {
        $for_date = $this->db->escape_str($for_date);

        // Graceful fallback: if the requested date has no snapshot yet (nightly
        // builder not run for that day), serve the most recent populated
        // snapshot instead of returning an empty list. This is a tiny indexed
        // lookup on stuck_leads_daily (idx_for_date_bd); it never scans
        // init_call live. A slightly stale snapshot is far better than total:0.
        $have = $this->db->query(
            "SELECT 1 FROM stuck_leads_daily WHERE for_date = '{$for_date}' LIMIT 1"
        )->row();
        if ( ! $have) {
            $latest = $this->db->query(
                "SELECT MAX(for_date) AS d FROM stuck_leads_daily"
            )->row();
            if ($latest && $latest->d) {
                $for_date = $this->db->escape_str($latest->d);
            }
        }

        $sql = "
            SELECT s.*, COALESCE(cm.compname,'') AS company_name,
                   COALESCE(u.name,'') AS bd_name
            FROM stuck_leads_daily s
            LEFT JOIN init_call l       ON l.id = s.cid_id
            LEFT JOIN company_master cm ON cm.id = l.cmpid_id
            LEFT JOIN user u            ON u.uid = s.bd_uid
            WHERE s.for_date = '{$for_date}'
        ";
        if ($bd_uid !== null) {
            $sql .= " AND s.bd_uid = " . (int) $bd_uid;
        }
        $sql .= " ORDER BY s.days_in_stage DESC LIMIT 500";
        return $this->db->query($sql)->result_array();
    }

    public function get_all_active_users()
    {
        // status column is varchar ('active'/'inactive'), NOT 1/0
        $sql = "
            SELECT uid, type_id, name
            FROM user
            WHERE type_id IN (3, 13, 28)
              AND status = 'active'
            ORDER BY type_id, uid
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_worst_bd_scores($for_date, $limit = 5)
    {
        $for_date = $this->db->escape_str($for_date);
        $limit    = (int) $limit;
        $sql = "
            SELECT b.*, COALESCE(u.name,'') AS bd_name
            FROM bd_productivity_daily b
            LEFT JOIN user u ON u.uid = b.bd_uid
            WHERE b.for_date = '{$for_date}'
            ORDER BY b.score_pct ASC
            LIMIT {$limit}
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_most_stuck_leads($for_date, $limit = 5)
    {
        $for_date = $this->db->escape_str($for_date);
        $limit    = (int) $limit;
        $sql = "
            SELECT s.*, COALESCE(cm.compname,'') AS company_name
            FROM stuck_leads_daily s
            LEFT JOIN init_call l       ON l.id = s.cid_id
            LEFT JOIN company_master cm ON cm.id = l.cmpid_id
            WHERE s.for_date = '{$for_date}'
            ORDER BY s.days_in_stage DESC
            LIMIT {$limit}
        ";
        return $this->db->query($sql)->result_array();
    }
}

