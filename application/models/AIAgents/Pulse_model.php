<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pulse_model - Migration 056 Pulse Reports Hub
 *
 * Central data access layer for all 15 Pulse reports.
 * Provides a generic dispatcher (get_report), one dedicated method per
 * report, snapshot refresh, download logging, and pilot restriction.
 *
 * Hybrid data strategy:
 *   - If the requested date range ends before today, use the snap table
 *     (faster, pre-aggregated, consistent).
 *   - If the date range includes today, query the live view directly
 *     (real-time accuracy at the cost of a slightly heavier query).
 *   - Default when no date range given: use snap table.
 *
 * Pilot restriction (flag_value = 1):
 *   Restricts all report output to the 5 WB pilot uids:
 *     1000289 BD Avishek, 1000351 BD Rimly, 1000305 CM Nilanjan,
 *     1000269 RM Mehak, 1000356 SC Debabrata.
 *
 * All user-supplied filter inputs are sanitised before use in queries.
 * Numeric ids are cast to int. String values are escaped.
 * No raw user string is ever interpolated into SQL without sanitisation.
 *
 * Standing rules: plain English, no em-dashes, no non-ASCII.
 * "Rs" for rupees, "percent" spelled out, "over" for greater than.
 *
 * Deploy path: application/models/AIAgents/Pulse_model.php
 */
class Pulse_model extends CI_Model
{
    const FEATURE_FLAG = 'pulse_056_enabled';

    // WB pilot uids - restrict scope when feature flag is 1
    const PILOT_UIDS = [1000289, 1000351, 1000305, 1000269, 1000356];

    // Maps report_code to the live view name, snap table name, display name, and group
    const REPORT_REGISTRY = [
        'pipeline_by_stage' => [
            'view'    => 'v_pulse_pipeline_by_stage',
            'snap'    => 'pulse_snap_pipeline_by_stage',
            'label'   => 'Pipeline by Stage',
            'group'   => 'Pipeline',
        ],
        'pipeline_by_cluster_category' => [
            'view'    => 'v_pulse_pipeline_by_cluster_category',
            'snap'    => 'pulse_snap_pipeline_by_cluster_category',
            'label'   => 'Pipeline by Cluster and Category',
            'group'   => 'Pipeline',
        ],
        'stuck_leads_bucket' => [
            'view'    => 'v_pulse_stuck_leads_bucket',
            'snap'    => 'pulse_snap_stuck_leads_bucket',
            'label'   => 'Stuck Leads Bucket',
            'group'   => 'Pipeline',
        ],
        'creation_to_won_funnel' => [
            'view'    => 'v_pulse_creation_to_won_funnel',
            'snap'    => 'pulse_snap_creation_to_won_funnel',
            'label'   => 'Creation to Won Funnel',
            'group'   => 'Funnel',
        ],
        'stage_transition_matrix' => [
            'view'    => 'v_pulse_stage_transition_matrix',
            'snap'    => 'pulse_snap_stage_transition_matrix',
            'label'   => 'Stage Transition Matrix',
            'group'   => 'Funnel',
        ],
        'cohort_conversion' => [
            'view'    => 'v_pulse_cohort_conversion',
            'snap'    => 'pulse_snap_cohort_conversion',
            'label'   => 'Cohort Conversion',
            'group'   => 'Funnel',
        ],
        'wins_ledger' => [
            'view'    => 'v_pulse_wins_ledger',
            'snap'    => 'pulse_snap_wins_ledger',
            'label'   => 'Wins Ledger',
            'group'   => 'Closures',
        ],
        'losses_ledger' => [
            'view'    => 'v_pulse_losses_ledger',
            'snap'    => 'pulse_snap_losses_ledger',
            'label'   => 'Losses Ledger',
            'group'   => 'Closures',
        ],
        'win_loss_reason_mix' => [
            'view'    => 'v_pulse_win_loss_reason_mix',
            'snap'    => 'pulse_snap_win_loss_reason_mix',
            'label'   => 'Win/Loss Reason Mix',
            'group'   => 'Closures',
        ],
        'task_performance_daily' => [
            'view'    => 'v_pulse_task_performance_daily',
            'snap'    => 'pulse_snap_task_performance_daily',
            'label'   => 'Task Performance Daily',
            'group'   => 'Activity and Tasks',
        ],
        'mom_compliance' => [
            'view'    => 'v_pulse_mom_compliance',
            'snap'    => 'pulse_snap_mom_compliance',
            'label'   => 'MoM Compliance',
            'group'   => 'Activity and Tasks',
        ],
        'meeting_economics_mix' => [
            'view'    => 'v_pulse_meeting_economics_mix',
            'snap'    => 'pulse_snap_meeting_economics_mix',
            'label'   => 'Meeting Economics Mix',
            'group'   => 'Activity and Tasks',
        ],
        'bd_scorecard' => [
            'view'    => 'v_pulse_bd_scorecard',
            'snap'    => 'pulse_snap_bd_scorecard',
            'label'   => 'BD Scorecard',
            'group'   => 'People and Money',
        ],
        'cm_rm_scorecard' => [
            'view'    => 'v_pulse_cm_rm_scorecard',
            'snap'    => 'pulse_snap_cm_rm_scorecard',
            'label'   => 'CM and RM Scorecard',
            'group'   => 'People and Money',
        ],
        'wallet_expense_ledger' => [
            'view'    => 'v_pulse_wallet_expense_ledger',
            'snap'    => 'pulse_snap_wallet_expense_ledger',
            'label'   => 'Wallet Expense Ledger',
            'group'   => 'People and Money',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------
    // probe
    // ------------------------------------------------------------------

    /**
     * Return the current feature flag state for this migration.
     * Used by the controller /probe endpoint and by other migrations
     * that want to know if Pulse 056 is deployed.
     *
     * @return array { flag_value, flag_label, deployed }
     */
    public function probe()
    {
        $row = $this->db
            ->where('flag_code', self::FEATURE_FLAG)
            ->get('feature_flag')
            ->row();

        $raw   = $row ? (int)$row->flag_value : 0;
        $label = ['0' => 'off', '1' => 'pilot', '2' => 'org_wide'][(string)$raw] ?? 'unknown';

        return [
            'deployed'    => TRUE,
            'flag_value'  => $raw,
            'flag_label'  => $label,
            'migration'   => '056',
            'report_count'=> count(self::REPORT_REGISTRY),
        ];
    }

    // ------------------------------------------------------------------
    // get_report - generic dispatcher
    // ------------------------------------------------------------------

    /**
     * Generic entry point. Dispatches to the appropriate dedicated method.
     * Reports that include today in their date range use the live view.
     * Reports with a fully historical date range use the snap table.
     * Default (no date range): use snap table.
     *
     * Accepted filter keys:
     *   date_from     - Y-m-d start of date range
     *   date_to       - Y-m-d end of date range
     *   bd_uid        - int BD user uid
     *   cm_uid        - int CM user uid
     *   rm_uid        - int RM user uid
     *   cluster_id    - int cluster id
     *   category      - string category_code (PSU / DMFT / ANCHOR / STANDARD)
     *   cstatus       - int or array of cstatus values
     *   creation_path - string creation_path_hint
     *   pilot_only    - bool, force pilot uid restriction regardless of flag
     *   limit         - int max rows (default 500, max 5000)
     *   offset        - int row offset for pagination
     *
     * @param string $report_code
     * @param array  $filters
     * @param bool   $use_snapshot  caller hint; hybrid logic may override
     * @return array { rows, count, source, report_code, filters_applied }
     */
    public function get_report($report_code, $filters = [], $use_snapshot = TRUE)
    {
        if ( ! array_key_exists($report_code, self::REPORT_REGISTRY)) {
            return ['error' => 'unknown_report_code', 'report_code' => $report_code];
        }

        // Sanitise all inputs before dispatching
        $f = $this->sanitise_filters($filters);

        // Determine whether to use live view or snap table
        $use_snap = $this->should_use_snap($f, $use_snapshot);

        // Dispatch to the dedicated method
        switch ($report_code) {
            case 'pipeline_by_stage':
                $rows = $this->get_pipeline_by_stage($f, $use_snap);
                break;
            case 'pipeline_by_cluster_category':
                $rows = $this->get_pipeline_by_cluster_category($f, $use_snap);
                break;
            case 'stuck_leads_bucket':
                $rows = $this->get_stuck_leads_bucket($f, $use_snap);
                break;
            case 'creation_to_won_funnel':
                $rows = $this->get_creation_to_won_funnel($f, $use_snap);
                break;
            case 'stage_transition_matrix':
                $rows = $this->get_stage_transition_matrix($f, $use_snap);
                break;
            case 'cohort_conversion':
                $rows = $this->get_cohort_conversion($f, $use_snap);
                break;
            case 'wins_ledger':
                $rows = $this->get_wins_ledger($f, $use_snap);
                break;
            case 'losses_ledger':
                $rows = $this->get_losses_ledger($f, $use_snap);
                break;
            case 'win_loss_reason_mix':
                $rows = $this->get_win_loss_reason_mix($f, $use_snap);
                break;
            case 'task_performance_daily':
                $rows = $this->get_task_performance_daily($f, $use_snap);
                break;
            case 'mom_compliance':
                $rows = $this->get_mom_compliance($f, $use_snap);
                break;
            case 'meeting_economics_mix':
                $rows = $this->get_meeting_economics_mix($f, $use_snap);
                break;
            case 'bd_scorecard':
                $rows = $this->get_bd_scorecard($f, $use_snap);
                break;
            case 'cm_rm_scorecard':
                $rows = $this->get_cm_rm_scorecard($f, $use_snap);
                break;
            case 'wallet_expense_ledger':
                $rows = $this->get_wallet_expense_ledger($f, $use_snap);
                break;
            default:
                return ['error' => 'dispatch_failed', 'report_code' => $report_code];
        }

        return [
            'report_code'     => $report_code,
            'source'          => $use_snap ? 'snapshot' : 'live_view',
            'rows'            => $rows,
            'count'           => count($rows),
            'filters_applied' => $f,
        ];
    }

    // ------------------------------------------------------------------
    // A1. pipeline_by_stage
    // ------------------------------------------------------------------

    /**
     * Pipeline leads grouped by cstatus with count and sum of fbudget.
     * Filterable by bd_uid, cm_uid, cluster_id, category, cstatus,
     * and date range (createDate for live; snap_date for snapshot).
     *
     * @param array $f  sanitised filter array
     * @param bool  $snap  use snap table
     * @return array of result rows
     */
    public function get_pipeline_by_stage($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['pipeline_by_stage'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl);
        $this->apply_pilot_filter($q, $f, 'bd_uid');
        $this->apply_common_filters($q, $f, $snap, 'bd_uid', 'cm_uid', 'cluster_id', 'category_code', 'cstatus');

        if ($snap) {
            $this->apply_snap_date($q, $f);
        } else {
            // For live view, date range filters are applied in the view itself
            // via the aggregation over createDate; we just add WHERE on bd/cm/cluster
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // A2. pipeline_by_cluster_category
    // ------------------------------------------------------------------

    /**
     * Heatmap matrix of cluster x category with count and fbudget.
     * Filterable by cluster_id, category, cstatus, cm_uid.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_pipeline_by_cluster_category($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['pipeline_by_cluster_category'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl);
        $this->apply_pilot_filter($q, $f, 'cm_uid');
        $this->apply_common_filters($q, $f, $snap, 'cm_uid', 'cluster_id', 'category_code', 'cstatus');

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // A3. stuck_leads_bucket
    // ------------------------------------------------------------------

    /**
     * Leads stuck over the expected threshold per cstatus.
     * Filterable by bd_uid, cm_uid, cluster_id, category, cstatus.
     * Returns ordered by days_over_threshold descending.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_stuck_leads_bucket($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['stuck_leads_bucket'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('days_over_threshold', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');
        $this->apply_common_filters($q, $f, $snap, 'bd_uid', 'cm_uid', 'cluster_id', 'category_code', 'cstatus');

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // B4. creation_to_won_funnel
    // ------------------------------------------------------------------

    /**
     * Count of leads at each cstatus for each creation-month cohort.
     * Filterable by bd_uid, cm_uid, cluster_id, category, date_from/to.
     * date_from and date_to filter on creation_month in snap mode,
     * or on createDate in live view mode.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_creation_to_won_funnel($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['creation_to_won_funnel'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('creation_month', 'ASC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');
        $this->apply_common_filters($q, $f, $snap, 'bd_uid', 'cm_uid', 'cluster_id', 'category_code', 'cstatus');

        if ($snap) {
            $this->apply_snap_date($q, $f);
            if ( ! empty($f['date_from'])) {
                $q->where("creation_month >= DATE_FORMAT('{$f['date_from']}', '%Y-%m')");
            }
            if ( ! empty($f['date_to'])) {
                $q->where("creation_month <= DATE_FORMAT('{$f['date_to']}', '%Y-%m')");
            }
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // B5. stage_transition_matrix
    // ------------------------------------------------------------------

    /**
     * Count of from_cstatus to to_cstatus transitions.
     * Filterable by bd_uid, cm_uid, cluster_id, category, date range.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_stage_transition_matrix($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['stage_transition_matrix'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('transition_count', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');
        $this->apply_common_filters($q, $f, $snap, 'bd_uid', 'cm_uid', 'cluster_id', 'category_code');

        if ($snap) {
            $this->apply_snap_date($q, $f);
            $this->apply_date_range($q, 'transition_date', $f);
        } else {
            $this->apply_date_range($q, 'transition_date', $f);
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // B6. cohort_conversion
    // ------------------------------------------------------------------

    /**
     * All leads with current cstatus, age, and age bucket.
     * Filterable by bd_uid, cm_uid, cluster_id, category, age_bucket,
     * and cstatus_now.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_cohort_conversion($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['cohort_conversion'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('age_days', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');
        $this->apply_common_filters($q, $f, $snap, 'bd_uid', 'cm_uid', 'cluster_id', 'category_code');

        // Filter by cstatus_now instead of cstatus for this report
        if ( ! empty($f['cstatus'])) {
            if (is_array($f['cstatus'])) {
                $q->where_in('cstatus_now', $f['cstatus']);
            } else {
                $q->where('cstatus_now', (int)$f['cstatus']);
            }
        }

        if ( ! empty($f['age_bucket'])) {
            $q->where('age_bucket', $this->db->escape_str($f['age_bucket']));
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // C7. wins_ledger
    // ------------------------------------------------------------------

    /**
     * Won leads with BD, school, closed value, days to close, creation path.
     * Filterable by bd_uid, cm_uid, cluster_id, category, date range
     * (date range applies to closed_date).
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_wins_ledger($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['wins_ledger'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('closed_date', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');
        $this->apply_common_filters($q, $f, $snap, 'bd_uid', 'cm_uid', 'cluster_id', 'category_code');

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_date_range($q, 'closed_date', $f);
        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // C8. losses_ledger
    // ------------------------------------------------------------------

    /**
     * Lost leads with BD, school, closed value, days in pipeline, loss reason.
     * Filterable by bd_uid, cm_uid, cluster_id, category, loss_reason_code,
     * and date range (applies to lost_date).
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_losses_ledger($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['losses_ledger'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('lost_date', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');
        $this->apply_common_filters($q, $f, $snap, 'bd_uid', 'cm_uid', 'cluster_id', 'category_code');

        if ( ! empty($f['loss_reason_code'])) {
            $q->where('loss_reason_code', $this->db->escape_str($f['loss_reason_code']));
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_date_range($q, 'lost_date', $f);
        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // C9. win_loss_reason_mix
    // ------------------------------------------------------------------

    /**
     * Win and loss counts by reason with percent.
     * Filterable by minimum total count.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_win_loss_reason_mix($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['win_loss_reason_mix'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('total_count', 'DESC');

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // D10. task_performance_daily
    // ------------------------------------------------------------------

    /**
     * Per BD per day: tasks planned, tasks done, completion percent.
     * Filterable by bd_uid and date range (applies to plan_date).
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_task_performance_daily($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['task_performance_daily'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('plan_date', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');

        if ( ! empty($f['bd_uid'])) {
            $q->where('bd_uid', (int)$f['bd_uid']);
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_date_range($q, 'plan_date', $f);
        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // D11. mom_compliance
    // ------------------------------------------------------------------

    /**
     * Per BD per ISO week: meetings held, MoMs written, compliance percent.
     * Filterable by bd_uid and date range (applies to week_start_date).
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_mom_compliance($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['mom_compliance'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('iso_yearweek', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');

        if ( ! empty($f['bd_uid'])) {
            $q->where('bd_uid', (int)$f['bd_uid']);
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_date_range($q, 'week_start_date', $f);
        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // D12. meeting_economics_mix
    // ------------------------------------------------------------------

    /**
     * Fresh vs RP vs NO-RP vs detail-only meeting counts per BD per week.
     * Filterable by bd_uid, cluster_id, and date range (event_date).
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_meeting_economics_mix($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['meeting_economics_mix'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('iso_yearweek', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');

        if ( ! empty($f['bd_uid'])) {
            $q->where('bd_uid', (int)$f['bd_uid']);
        }

        if ( ! empty($f['cluster_id'])) {
            $q->where('cluster_id', (int)$f['cluster_id']);
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_date_range($q, 'event_date', $f);
        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // E13. bd_scorecard
    // ------------------------------------------------------------------

    /**
     * Per BD: planning grade, transitions, wallet spend, wins, losses,
     * conversion score.
     * Filterable by bd_uid. Live view computes over last 90 days dynamically;
     * snap table uses data as of the last nightly refresh.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_bd_scorecard($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['bd_scorecard'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('conversion_score_pct', 'DESC');
        $this->apply_pilot_filter($q, $f, 'uid');

        if ( ! empty($f['bd_uid'])) {
            $q->where('uid', (int)$f['bd_uid']);
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // E14. cm_rm_scorecard
    // ------------------------------------------------------------------

    /**
     * Per CM or RM: K1-K7 KPIs and signoff SLA compliance.
     * Filterable by cm_uid (or any manager uid), cluster_id, and role.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_cm_rm_scorecard($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['cm_rm_scorecard'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('day_score', 'DESC');
        $this->apply_pilot_filter($q, $f, 'uid');

        if ( ! empty($f['cm_uid'])) {
            $q->where('uid', (int)$f['cm_uid']);
        }

        if ( ! empty($f['rm_uid'])) {
            $q->where('uid', (int)$f['rm_uid']);
        }

        if ( ! empty($f['cluster_id'])) {
            $q->where('cluster_id', (int)$f['cluster_id']);
        }

        if ( ! empty($f['role'])) {
            $safe_role = $this->db->escape_str($f['role']);
            $q->where('role', $safe_role);
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // E15. wallet_expense_ledger
    // ------------------------------------------------------------------

    /**
     * Travel advance and cash expense records with aging and variance breach flag.
     * Filterable by bd_uid, date range (record_created_at), variance_breach flag.
     *
     * @param array $f sanitised filters
     * @param bool  $snap
     * @return array
     */
    public function get_wallet_expense_ledger($f, $snap = TRUE)
    {
        $reg = self::REPORT_REGISTRY['wallet_expense_ledger'];
        $tbl = $snap ? $reg['snap'] : $reg['view'];

        $q = $this->db->from($tbl)->order_by('aging_days', 'DESC');
        $this->apply_pilot_filter($q, $f, 'bd_uid');

        if ( ! empty($f['bd_uid'])) {
            $q->where('bd_uid', (int)$f['bd_uid']);
        }

        if (isset($f['variance_breach_only']) && (bool)$f['variance_breach_only']) {
            $q->where('is_variance_breach', 1);
        }

        if ($snap) {
            $this->apply_snap_date($q, $f);
        }

        $this->apply_date_range($q, 'record_created_at', $f);
        $this->apply_limit($q, $f);
        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // refresh_snapshots
    // ------------------------------------------------------------------

    /**
     * Call the stored procedure sp_pulse_refresh_snapshots().
     * Normally invoked by the nightly cron via the admin-only endpoint.
     * Returns the outcome of the procedure call.
     *
     * @return array { ok, message, executed_at }
     */
    public function refresh_snapshots()
    {
        $result = $this->db->query('CALL sp_pulse_refresh_snapshots()');
        return [
            'ok'          => (bool)$result,
            'message'     => $result ? 'All 15 snap tables refreshed' : 'Procedure call failed - check DB error log',
            'executed_at' => date('c'),
        ];
    }

    // ------------------------------------------------------------------
    // log_download
    // ------------------------------------------------------------------

    /**
     * Record a download request in pulse_download_log.
     * Called by the controller for every CSV, Excel, or PDF export.
     *
     * @param int    $user_uid
     * @param string $report_code
     * @param string $format       csv|xlsx|pdf
     * @param string $filters_json JSON string of applied filters
     * @param int    $byte_size    file size in bytes
     * @param string $ip           requester IP
     * @return int   inserted row id
     */
    public function log_download($user_uid, $report_code, $format, $filters_json, $byte_size, $ip)
    {
        $allowed_formats = ['csv', 'xlsx', 'pdf'];
        $safe_format = in_array($format, $allowed_formats) ? $format : 'csv';

        $data = [
            'user_uid'    => (int)$user_uid,
            'report_code' => substr($this->db->escape_str($report_code), 0, 64),
            'format'      => $safe_format,
            'filter_json' => $filters_json,
            'byte_size'   => (int)$byte_size,
            'ip'          => substr($this->db->escape_str($ip), 0, 45),
        ];

        $this->db->insert('pulse_download_log', $data);
        return (int)$this->db->insert_id();
    }

    // ------------------------------------------------------------------
    // get_filter_options
    // ------------------------------------------------------------------

    /**
     * Return dropdown option lists for all filter controls.
     * BD list, CM list, cluster list, category list, cstatus list.
     * Pilot restriction applies: when flag=1, only WB pilot uids shown.
     *
     * @return array { bd_list, cm_list, cluster_list, category_list, cstatus_list }
     */
    public function get_filter_options()
    {
        $flag  = $this->get_flag_value();
        $pilot = ($flag === 1);

        // BD list
        $bd_q = $this->db
            ->select('uid, fname AS name')
            ->where('type_id', 1)
            ->order_by('fname', 'ASC');
        if ($pilot) {
            $bd_q->where_in('uid', self::PILOT_UIDS);
        }
        $bd_list = $bd_q->get('user')->result_array();

        // CM list (type_id 13 = CM)
        $cm_q = $this->db
            ->select('uid, fname AS name')
            ->where('type_id', 13)
            ->order_by('fname', 'ASC');
        if ($pilot) {
            $cm_q->where_in('uid', self::PILOT_UIDS);
        }
        $cm_list = $cm_q->get('user')->result_array();

        // Cluster list from init_call distinct values
        $cluster_list = $this->db
            ->select('DISTINCT cluster_id')
            ->where('cluster_id IS NOT NULL', NULL, FALSE)
            ->order_by('cluster_id', 'ASC')
            ->get('init_call')
            ->result_array();

        // Category list
        $category_list = ['PSU', 'DMFT', 'ANCHOR', 'STANDARD'];

        // cstatus list
        $cstatus_list = [
            ['cstatus' => 1,  'label' => 'Open'],
            ['cstatus' => 2,  'label' => 'Reachout'],
            ['cstatus' => 3,  'label' => 'Tentative'],
            ['cstatus' => 6,  'label' => 'Positive'],
            ['cstatus' => 7,  'label' => 'Proposal sent'],
            ['cstatus' => 8,  'label' => 'Open RPEM'],
            ['cstatus' => 9,  'label' => 'Very Positive'],
            ['cstatus' => 12, 'label' => 'Won'],
            ['cstatus' => 13, 'label' => 'Lost'],
        ];

        return [
            'bd_list'       => $bd_list,
            'cm_list'       => $cm_list,
            'cluster_list'  => $cluster_list,
            'category_list' => $category_list,
            'cstatus_list'  => $cstatus_list,
        ];
    }

    // ------------------------------------------------------------------
    // get_report_registry
    // ------------------------------------------------------------------

    /**
     * Return the registry of all 15 report codes with display names and groups.
     * Used by the /list_reports endpoint.
     *
     * @return array
     */
    public function get_report_registry()
    {
        $out = [];
        foreach (self::REPORT_REGISTRY as $code => $meta) {
            $out[] = [
                'report_code' => $code,
                'label'       => $meta['label'],
                'group'       => $meta['group'],
                'view'        => $meta['view'],
                'snap_table'  => $meta['snap'],
            ];
        }
        return $out;
    }

    // ------------------------------------------------------------------
    // Internal helpers
    // ------------------------------------------------------------------

    /**
     * Return the raw integer feature flag value.
     * 0=off, 1=pilot, 2=org-wide.
     *
     * @return int
     */
    public function get_flag_value()
    {
        $row = $this->db
            ->where('flag_code', self::FEATURE_FLAG)
            ->get('feature_flag')
            ->row();
        return $row ? (int)$row->flag_value : 0;
    }

    /**
     * Sanitise all user-supplied filter values.
     * Cast numeric ids to int. Escape string values.
     * Reject any key not in the allowed list.
     *
     * @param array $raw
     * @return array
     */
    private function sanitise_filters($raw)
    {
        $allowed_ints    = ['bd_uid','cm_uid','rm_uid','cluster_id','cstatus','limit','offset'];
        $allowed_dates   = ['date_from','date_to'];
        $allowed_strings = ['category','loss_reason_code','creation_path','role','age_bucket'];
        $allowed_bools   = ['pilot_only','variance_breach_only'];

        $out = [];

        foreach ($allowed_ints as $k) {
            if (isset($raw[$k])) {
                if (is_array($raw[$k])) {
                    $out[$k] = array_map('intval', $raw[$k]);
                } else {
                    $out[$k] = (int)$raw[$k];
                }
            }
        }

        foreach ($allowed_dates as $k) {
            if ( ! empty($raw[$k])) {
                // Validate YYYY-MM-DD format
                $d = $raw[$k];
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
                    $out[$k] = $d;
                }
            }
        }

        foreach ($allowed_strings as $k) {
            if ( ! empty($raw[$k])) {
                $out[$k] = $this->db->escape_str(substr((string)$raw[$k], 0, 128));
            }
        }

        foreach ($allowed_bools as $k) {
            if (isset($raw[$k])) {
                $out[$k] = (bool)$raw[$k];
            }
        }

        // category is also stored as category_code in DB, normalise key
        if ( ! empty($out['category'])) {
            $out['category_code'] = $out['category'];
        }

        return $out;
    }

    /**
     * Decide whether to use the snap table or the live view.
     * Returns TRUE (snap) unless date_to includes today or is empty.
     *
     * @param array $f
     * @param bool  $caller_hint  caller preference
     * @return bool TRUE = use snap, FALSE = use live view
     */
    private function should_use_snap($f, $caller_hint)
    {
        $today = date('Y-m-d');

        // If date_to is set and is today or in the future, use live view
        if ( ! empty($f['date_to']) && $f['date_to'] >= $today) {
            return FALSE;
        }

        // If no date range at all, respect caller hint (default snap)
        return $caller_hint;
    }

    /**
     * Apply pilot uid restriction when feature flag is 1 OR pilot_only=true.
     * Restricts on the given uid column name.
     *
     * @param object $q         CI query builder reference
     * @param array  $f         sanitised filters
     * @param string $uid_col   column name to restrict (bd_uid, cm_uid, uid)
     */
    private function apply_pilot_filter($q, $f, $uid_col)
    {
        $flag = $this->get_flag_value();
        $pilot_forced = ! empty($f['pilot_only']) && (bool)$f['pilot_only'];

        if ($flag === 1 || $pilot_forced) {
            $q->where_in($uid_col, self::PILOT_UIDS);
        }
    }

    /**
     * Apply common dimension filters to a query builder instance.
     * Only applies the filter if the filter key is in $cols and is set.
     *
     * @param object $q
     * @param array  $f
     * @param bool   $snap     true = snap table mode
     * @param string ...$cols  list of column names to check
     */
    private function apply_common_filters($q, $f, $snap, ...$cols)
    {
        foreach ($cols as $col) {
            if ( ! empty($f[$col])) {
                if (is_array($f[$col])) {
                    $q->where_in($col, $f[$col]);
                } else {
                    $q->where($col, $f[$col]);
                }
            }
        }
    }

    /**
     * Apply snap_date filter to select the most recent snapshot.
     * If a specific snap_date is not requested, defaults to the most
     * recent snap_date available in the table.
     *
     * @param object $q
     * @param array  $f
     */
    private function apply_snap_date($q, $f)
    {
        if ( ! empty($f['snap_date'])) {
            // Specific snap date requested - validate format
            $sd = $f['snap_date'];
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $sd)) {
                $q->where('snap_date', $sd);
            }
        } else {
            // Use the most recent snap_date in the table
            // This sub-select is handled by ordering and limiting
            // For tables with snap_date, we filter to the latest
            $q->where("snap_date = (SELECT MAX(snap_date) FROM {$q->ar_from[0]} LIMIT 1)", NULL, FALSE);
        }
    }

    /**
     * Apply a date range filter to a named column.
     *
     * @param object $q
     * @param string $col
     * @param array  $f
     */
    private function apply_date_range($q, $col, $f)
    {
        if ( ! empty($f['date_from'])) {
            $q->where("{$col} >= '{$f['date_from']}'", NULL, FALSE);
        }
        if ( ! empty($f['date_to'])) {
            $q->where("{$col} <= '{$f['date_to']}'", NULL, FALSE);
        }
    }

    /**
     * Apply limit and offset for pagination.
     * Default limit 500, maximum 5000.
     *
     * @param object $q
     * @param array  $f
     */
    private function apply_limit($q, $f)
    {
        $limit  = isset($f['limit'])  ? min((int)$f['limit'], 5000)  : 500;
        $offset = isset($f['offset']) ? max((int)$f['offset'], 0)    : 0;
        $q->limit($limit, $offset);
    }
}
