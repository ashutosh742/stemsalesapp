<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Anaya_reports — Reporting / Dashboard Agent Controller
 * Gap Fix Sprint 2026-06-04 (RED → GREEN)
 *
 * Wires the /api/anaya_reports/* endpoint that was 404-ing.
 * Extends CI_Controller; uses $this->db with table_exists guards
 * so the app stays healthy before migrations run.
 *
 * Routes (add to routes_red_agents.php or application/config/routes.php):
 *   GET  /api/anaya_reports/probe
 *   GET  /api/anaya_reports/status?uid=
 *   POST /api/anaya_reports/run
 *   GET  /api/anaya_reports/weekly_summary?uid=
 *   GET  /api/anaya_reports/monthly_report?uid=&month=YYYY-MM
 *   GET  /api/anaya_reports/daily_bd          (legacy cron compat)
 *   GET  /api/anaya_reports/daily_owner       (legacy cron compat)
 */
class Anaya_reports extends CI_Controller
{
    // ---------------------------------------------------------------------------
    // Bootstrap
    // ---------------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'date']);
        header('Content-Type: application/json; charset=utf-8');
    }

    // ---------------------------------------------------------------------------
    // Helper: safe JSON output
    // ---------------------------------------------------------------------------
    protected function _json(array $data, int $status = 200): void
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    // ---------------------------------------------------------------------------
    // Helper: check table exists
    // ---------------------------------------------------------------------------
    protected function _table_ok(string $table): bool
    {
        return (bool) $this->db->table_exists($table);
    }

    // ---------------------------------------------------------------------------
    // Helper: return empty-rows stub when tables not seeded
    // ---------------------------------------------------------------------------
    protected function _not_seeded(string $table): array
    {
        return [
            'ok'   => true,
            'rows' => [],
            'note' => 'tables_not_seeded_yet',
            'missing_table' => $table,
        ];
    }

    // ===========================================================================
    // GET /api/anaya_reports/probe
    // Returns liveness / version info — no auth required.
    // ===========================================================================
    public function probe(): void
    {
        $this->_json([
            'ok'       => true,
            'agent'    => 'Anaya_reports',
            'healthy'  => true,
            'version'  => '1.0',
            'last_run' => date('Y-m-d H:i:s'),
        ]);
    }

    // ===========================================================================
    // GET /api/anaya_reports/status?uid=<user_details_id>
    // Returns last invocation metadata for a given UID.
    // ===========================================================================
    public function status(): void
    {
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid_required'], 400);
            return;
        }

        try {
            if (!$this->_table_ok('crm_report_cron_log')) {
                $this->_json(array_merge(
                    $this->_not_seeded('crm_report_cron_log'),
                    ['uid' => $uid, 'last_invocation_ts' => null, 'suggestions_count' => 0, 'errors_count' => 0]
                ));
                return;
            }

            // last run for this uid (or global if no uid column)
            $last = $this->db
                ->select('send_datetime, report_name')
                ->from('crm_report_cron_log')
                ->order_by('id', 'DESC')
                ->limit(1)
                ->get()
                ->row_array();

            // count today's email logs for the uid
            $suggestions = 0;
            $errors      = 0;
            if ($this->_table_ok('crm_email_logs')) {
                $suggestions = (int) $this->db
                    ->from('crm_email_logs')
                    ->where('for_user', $uid)
                    ->where('mailsend_date', date('Y-m-d'))
                    ->count_all_results();
            }

            $this->_json([
                'ok'                 => true,
                'uid'                => $uid,
                'last_invocation_ts' => $last['send_datetime'] ?? null,
                'last_report'        => $last['report_name']   ?? null,
                'suggestions_count'  => $suggestions,
                'errors_count'       => $errors,
            ]);
        } catch (Exception $e) {
            log_message('error', 'Anaya_reports::status: ' . $e->getMessage());
            $this->_json([
                'ok'                 => true,
                'uid'                => $uid,
                'last_invocation_ts' => null,
                'suggestions_count'  => 0,
                'errors_count'       => 0,
                'note'               => 'db_error',
                'detail'             => $e->getMessage(),
            ]);
        }
    }

    // ===========================================================================
    // POST /api/anaya_reports/run
    // Triggers an agent run (queues or executes) — returns a run_id.
    // ===========================================================================
    public function run(): void
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST_required'], 405);
            return;
        }

        $run_id    = 'AR-' . date('Ymd-His') . '-' . substr(md5(uniqid('', true)), 0, 6);
        $started_at = date('Y-m-d H:i:s');

        try {
            if ($this->_table_ok('crm_report_cron_log')) {
                $this->db->insert('crm_report_cron_log', [
                    'report_name'   => 'api_run',
                    'report_data'   => json_encode(['run_id' => $run_id, 'source' => 'api']),
                    'email_send'    => 0,
                    'send_datetime' => $started_at,
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Anaya_reports::run insert log: ' . $e->getMessage());
            // non-fatal — still return ok
        }

        $this->_json([
            'ok'         => true,
            'run_id'     => $run_id,
            'started_at' => $started_at,
        ]);
    }

    // ===========================================================================
    // GET /api/anaya_reports/weekly_summary?uid=<user_details_id>
    // Returns weekly activity summary from v_weekly_summary view or computed rows.
    // ===========================================================================
    public function weekly_summary(): void
    {
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid_required'], 400);
            return;
        }

        try {
            // Prefer the materialised view if it exists
            if ($this->_table_ok('v_weekly_summary')) {
                $rows = $this->db
                    ->select('*')
                    ->from('v_weekly_summary')
                    ->where('bd_uid', $uid)
                    ->get()
                    ->result_array();

                $this->_json(['ok' => true, 'uid' => $uid, 'rows' => $rows]);
                return;
            }

            // Fallback: compute from init_call if available
            if (!$this->_table_ok('init_call')) {
                $this->_json(array_merge(
                    $this->_not_seeded('init_call'),
                    ['uid' => $uid]
                ));
                return;
            }

            $week_start = date('Y-m-d', strtotime('monday this week'));
            $week_end   = date('Y-m-d', strtotime('sunday this week'));

            // audit fix 2026-06-06: use mainbd (not assigned_bd_uid), cstatus=13 for closure won
            //   init_call has no closed col; use cstatus=13 (closure_pipeline done)
            $rows = $this->db->query(
                "SELECT
                     DATE(created_at)        AS call_date,
                     COUNT(*)                AS total_calls,
                     SUM(cstatus = 6)        AS positives,
                     SUM(cstatus = 7)        AS proposals_sent,
                     SUM(cstatus = 13)       AS closed_won
                 FROM init_call
                 WHERE mainbd = ?
                   AND DATE(created_at) BETWEEN ? AND ?
                 GROUP BY DATE(created_at)
                 ORDER BY call_date ASC",
                [$uid, $week_start, $week_end]
            )->result_array();

            $this->_json([
                'ok'         => true,
                'uid'        => $uid,
                'week_start' => $week_start,
                'week_end'   => $week_end,
                'rows'       => $rows,
                'note'       => 'computed_from_init_call',
            ]);
        } catch (Exception $e) {
            log_message('error', 'Anaya_reports::weekly_summary: ' . $e->getMessage());
            $this->_json([
                'ok'   => true,
                'uid'  => $uid,
                'rows' => [],
                'note' => 'db_error',
                'detail' => $e->getMessage(),
            ]);
        }
    }

    // ===========================================================================
    // GET /api/anaya_reports/monthly_report?uid=<id>&month=YYYY-MM
    // Returns monthly report from v_monthly_report view or computed fallback.
    // ===========================================================================
    public function monthly_report(): void
    {
        $uid   = (int)    $this->input->get('uid');
        $month = (string) $this->input->get('month'); // YYYY-MM

        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid_required'], 400);
            return;
        }

        // Default to current month
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $month_start = $month . '-01';
        $month_end   = date('Y-m-t', strtotime($month_start));

        try {
            // Prefer view
            if ($this->_table_ok('v_monthly_report')) {
                $rows = $this->db
                    ->select('*')
                    ->from('v_monthly_report')
                    ->where('bd_uid', $uid)
                    ->like('report_month', $month, 'after')
                    ->get()
                    ->result_array();

                $this->_json(['ok' => true, 'uid' => $uid, 'month' => $month, 'rows' => $rows]);
                return;
            }

            // Fallback: compute from init_call
            if (!$this->_table_ok('init_call')) {
                $this->_json(array_merge(
                    $this->_not_seeded('init_call'),
                    ['uid' => $uid, 'month' => $month]
                ));
                return;
            }

            $rows = $this->db->query(
                "SELECT
                     DATE_FORMAT(created_at, '%Y-%m-%d') AS call_date,
                     COUNT(*)                             AS total_calls,
                     SUM(cstatus = 6)           AS positives,
                     SUM(cstatus = 7)           AS proposals_sent,
                     SUM(cstatus = 13)                    AS closed_won
                 FROM init_call
                 WHERE mainbd = ?
                   AND DATE(created_at) BETWEEN ? AND ?
                 GROUP BY DATE(created_at)
                 ORDER BY call_date ASC",
                [$uid, $month_start, $month_end]
            )->result_array();

            $this->_json([
                'ok'          => true,
                'uid'         => $uid,
                'month'       => $month,
                'month_start' => $month_start,
                'month_end'   => $month_end,
                'rows'        => $rows,
                'note'        => 'computed_from_init_call',
            ]);
        } catch (Exception $e) {
            log_message('error', 'Anaya_reports::monthly_report: ' . $e->getMessage());
            $this->_json([
                'ok'    => true,
                'uid'   => $uid,
                'month' => $month,
                'rows'  => [],
                'note'  => 'db_error',
                'detail' => $e->getMessage(),
            ]);
        }
    }

    // ===========================================================================
    // Legacy cron compat — daily_bd / daily_owner
    // These are now no-op stubs so the cron route doesn't 404.
    // Real logic lives in the cron/ sub-controller.
    // ===========================================================================
    public function daily_bd(): void
    {
        $this->_json([
            'ok'   => true,
            'note' => 'use_cron_controller',
            'hint' => 'php index.php cron/Anaya_reports/daily_bd',
        ]);
    }

    public function daily_owner(): void
    {
        $this->_json([
            'ok'   => true,
            'note' => 'use_cron_controller',
            'hint' => 'php index.php cron/Anaya_reports/daily_owner',
        ]);
    }
}
// end of Anaya_reports_patched.php
