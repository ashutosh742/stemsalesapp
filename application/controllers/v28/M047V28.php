<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M047V28 Controller
 *
 * Migration 047 - Calendar Task Execution Gap.
 * Exposes dashboard and today task routes backed by real tables.
 *
 * Table availability note:
 *   planner_day_plan_tasks - does NOT exist on this DB (awaits migration 047).
 *   Falling back to tblcallevents (plan=1, date = today) for task data.
 *   daily_planner used for day shape / planner status.
 *
 * Routes served:
 *   GET /api/m047/dashboard
 *   GET /api/m047/today
 *   GET /api/m047/task/today
 *
 * Bearer token (staging smoke): 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 *
 * Expected schema for planner_day_plan_tasks (when migration 047 runs):
 *   id, user_id, plan_date, task_ref_id, action_type_id, planned_minutes,
 *   initiated_at, completed_at, gap_minutes, gap_reason, created_at
 */
class M047V28 extends CI_Controller
{
    /** Staging bearer token */
    private $BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    /** Day budget in minutes */
    private $DAY_BUDGET_MIN = 540;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    // -----------------------------------------------------------------------
    // PRIVATE HELPERS
    // -----------------------------------------------------------------------

    private function _json(array $data, int $status = 200): void
    {
        $this->output->set_status_header($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function _check_bearer(): bool
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $auth = $this->input->get_request_header('Authorization');
        if (!$auth) {
            $this->_json(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        $token = $auth;
        if (stripos($auth, 'Bearer ') === 0) {
            $token = substr($auth, 7);
        }
        $token = trim($token);
        if (!hash_equals($this->BEARER, $token)) {
            $this->_json(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    /**
     * Resolve uid from GET or POST param.
     */
    private function _resolve_uid(): int
    {
        return (int)($this->input->get('uid') ?: $this->input->post('uid'));
    }

    /**
     * Resolve date from ?date= param or today.
     */
    private function _resolve_date(): string
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return date('Y-m-d');
    }

    /**
     * Current IST day shape based on server UTC time.
     */
    private function _day_shape(): string
    {
        $ist_offset = 5 * 3600 + 30 * 60;
        $ist_time   = time() + $ist_offset;
        $hhmm = (int)gmdate('H', $ist_time) * 60 + (int)gmdate('i', $ist_time);

        if ($hhmm >= 600 && $hhmm < 900)  { return 'manual'; }
        if ($hhmm >= 900 && $hhmm < 1050) { return 'auto'; }
        if ($hhmm >= 1050 && $hhmm < 1110) { return 'plan_window'; }
        return 'closed';
    }

    /**
     * Fetch tblcallevents rows for a user on a given date (plan=1).
     * Uses verified columns: id, user_id, date, plan_time, initiate_time,
     * complete_time, actiontype_id, cid_id, purpose_id, approved_status,
     * mom_received, mom_approved.
     */
    private function _fetch_tasks(int $uid, string $for_date): array
    {
        if ($uid <= 0) {
            return [];
        }

        $sql = "
            SELECT
                t.id,
                t.actiontype_id,
                t.cid_id,
                t.purpose_id,
                t.approved_status,
                t.mom_received,
                t.mom_approved,
                t.plan_time,
                t.initiate_time,
                t.complete_time,
                t.date AS task_date,
                COALESCE(c.compname, 'Unknown Company') AS company_name
            FROM tblcallevents t
            LEFT JOIN init_call ic ON ic.id = t.cid_id
            LEFT JOIN company_master c ON c.id = ic.cmpid_id
            WHERE t.user_id = ?
              AND DATE(t.date) = ?
              AND t.plan = 1
            ORDER BY t.plan_time ASC, t.id ASC
            LIMIT 100
        ";

        $q = $this->db->query($sql, [$uid, $for_date]);
        if (!$q || $q->num_rows() === 0) {
            return [];
        }
        return $q->result_array();
    }

    /**
     * Compute execution gap summary for a list of tasks.
     * Gap = tasks that have plan_time set but no initiate_time (not started).
     */
    private function _gap_summary(array $tasks): array
    {
        $total     = count($tasks);
        $completed = 0;
        $initiated = 0;
        $gap       = 0;

        foreach ($tasks as $t) {
            if (!empty($t['complete_time'])) {
                $completed++;
            } elseif (!empty($t['initiate_time'])) {
                $initiated++;
            } else {
                $gap++;
            }
        }

        return [
            'total'     => $total,
            'completed' => $completed,
            'initiated' => $initiated,
            'gap'       => $gap,
        ];
    }

    /**
     * Fetch daily_planner row for a user on a given date.
     */
    private function _planner_row(int $uid, string $for_date): ?array
    {
        if ($uid <= 0) {
            return null;
        }
        $q = $this->db->select(
            'id, record_date, planner_approvel_status, day_start_time, end_time, ' .
            'autoTaskStartTime, autoTaskEndTime, task_subtype'
        )
            ->from('daily_planner')
            ->where('userID', $uid)
            ->where('record_date', $for_date)
            ->limit(1)
            ->get();

        if (!$q || $q->num_rows() === 0) {
            return null;
        }
        return $q->row_array();
    }

    // -----------------------------------------------------------------------
    // ENDPOINTS
    // -----------------------------------------------------------------------

    /**
     * dashboard
     * GET /api/m047/dashboard?uid=<uid>[&date=YYYY-MM-DD]
     *
     * Returns a summary of the calendar task execution gap for a user.
     * Uses tblcallevents for task data (planner_day_plan_tasks awaits migration 047).
     */
    public function dashboard()
    {
        if (!$this->_check_bearer()) {
            return;
        }

        $uid      = $this->_resolve_uid();
        $for_date = $this->_resolve_date();
        $tasks    = $this->_fetch_tasks($uid, $for_date);
        $gap      = $this->_gap_summary($tasks);
        $planner  = $this->_planner_row($uid, $for_date);

        $planner_status = $planner ? ($planner['planner_approvel_status'] ?? 'unknown') : 'not_set';
        $day_shape      = $this->_day_shape();

        $this->_json([
            'ok'             => true,
            'success'        => true,
            'date'           => $for_date,
            'day_shape'      => $day_shape,
            'planner_status' => $planner_status,
            'gap_summary'    => $gap,
            'budget_min'     => $this->DAY_BUDGET_MIN,
            'note'           => 'task_data_from_tblcallevents_awaits_migration_047',
            'rows'           => $tasks,
            'count'          => count($tasks),
        ]);
    }

    /**
     * today
     * GET /api/m047/today?uid=<uid>
     *
     * Returns today's calendar tasks with execution gap indicators.
     * Mirror of task/today with a flatter envelope for mobile dashboard tiles.
     */
    public function today()
    {
        if (!$this->_check_bearer()) {
            return;
        }

        $uid      = $this->_resolve_uid();
        $for_date = date('Y-m-d');
        $tasks    = $this->_fetch_tasks($uid, $for_date);
        $gap      = $this->_gap_summary($tasks);
        $day_shape = $this->_day_shape();

        $this->_json([
            'ok'          => true,
            'success'     => true,
            'date'        => $for_date,
            'day_shape'   => $day_shape,
            'gap_summary' => $gap,
            'budget_min'  => $this->DAY_BUDGET_MIN,
            'rows'        => $tasks,
            'count'       => count($tasks),
            'note'        => 'task_data_from_tblcallevents_awaits_migration_047',
        ]);
    }

    /**
     * task_today
     * GET /api/m047/task/today?uid=<uid>
     *
     * Returns today's planned tasks with full execution state per task.
     * Equivalent to /api/m047/today but nested under /task/ path.
     */
    public function task_today()
    {
        if (!$this->_check_bearer()) {
            return;
        }

        $uid      = $this->_resolve_uid();
        $for_date = date('Y-m-d');
        $tasks    = $this->_fetch_tasks($uid, $for_date);
        $day_shape = $this->_day_shape();

        // Enrich each task with execution state
        $enriched = [];
        foreach ($tasks as $t) {
            $state = 'pending';
            if (!empty($t['complete_time'])) {
                $state = 'completed';
            } elseif (!empty($t['initiate_time'])) {
                $state = 'initiated';
            }

            $enriched[] = [
                'id'             => (int)$t['id'],
                'company_name'   => $t['company_name'],
                'actiontype_id'  => (int)$t['actiontype_id'],
                'purpose_id'     => (int)$t['purpose_id'],
                'approved_status'=> $t['approved_status'],
                'mom_received'   => $t['mom_received'],
                'mom_approved'   => $t['mom_approved'],
                'plan_time'      => $t['plan_time'],
                'initiate_time'  => $t['initiate_time'],
                'complete_time'  => $t['complete_time'],
                'execution_state'=> $state,
            ];
        }

        $this->_json([
            'ok'        => true,
            'success'   => true,
            'date'      => $for_date,
            'day_shape' => $day_shape,
            'rows'      => $enriched,
            'count'     => count($enriched),
            'note'      => 'task_data_from_tblcallevents_awaits_migration_047',
        ]);
    }
}

/* End of M047V28.php */
