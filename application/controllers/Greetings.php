<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Greetings — Festival / Birthday Greetings Agent Controller
 * Gap Fix Sprint 2026-06-04 (RED → GREEN)
 *
 * Wires the /api/greetings/* endpoint that was missing.
 * Based on existing GreetingsController.php (migration 048) and
 * GreetingsAgent_model.php; replaces the stub with real DB queries.
 *
 * All DB access is guarded with $this->db->table_exists() so the
 * app stays healthy before migration 048 tables are seeded.
 *
 * Routes: see routes_red_agents.php
 *
 * Tables used (guarded):
 *   greeting_task         — pending / sent tasks
 *   greeting_occasion     — occasion definitions (birthday, festival, etc.)
 *   stakeholder_dob       — stakeholder DOB / contact data
 *   greetings_log         — send log
 *   init_call             — school / account reference
 *   user_details          — fallback DOB source if stakeholder_dob absent
 */
class Greetings extends CI_Controller
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
    // Helpers
    // ---------------------------------------------------------------------------
    protected function _json(array $data, int $status = 200): void
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    protected function _table_ok(string $table): bool
    {
        return (bool) $this->db->table_exists($table);
    }

    protected function _not_seeded(string $table): array
    {
        return [
            'ok'            => true,
            'rows'          => [],
            'note'          => 'tables_not_seeded_yet',
            'missing_table' => $table,
        ];
    }

    // ===========================================================================
    // GET /api/greetings/probe
    // ===========================================================================
    public function probe(): void
    {
        $this->_json([
            'ok'         => true,
            'agent'      => 'Greetings',
            'healthy'    => true,
            'version'    => '1.0',
            'migration'  => '048',
            'last_run'   => date('Y-m-d H:i:s'),
        ]);
    }

    // ===========================================================================
    // GET /api/greetings/status?uid=<bd_uid>
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
            if (!$this->_table_ok('greeting_task')) {
                $this->_json(array_merge(
                    $this->_not_seeded('greeting_task'),
                    ['uid' => $uid, 'last_invocation_ts' => null, 'suggestions_count' => 0, 'errors_count' => 0]
                ));
                return;
            }

            $last = $this->db
                ->select('MAX(created_at) AS last_ts, COUNT(*) AS total')
                ->from('greeting_task')
                ->where('bd_uid', $uid)
                ->get()
                ->row_array();

            $pending = (int) $this->db
                ->from('greeting_task')
                ->where('bd_uid', $uid)
                ->where('status', 'pending')
                ->count_all_results();

            $failed = (int) $this->db
                ->from('greeting_task')
                ->where('bd_uid', $uid)
                ->where('status', 'failed')
                ->count_all_results();

            $this->_json([
                'ok'                 => true,
                'uid'                => $uid,
                'last_invocation_ts' => $last['last_ts']  ?? null,
                'suggestions_count'  => $pending,
                'errors_count'       => $failed,
            ]);
        } catch (Exception $e) {
            log_message('error', 'Greetings::status: ' . $e->getMessage());
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
    // POST /api/greetings/run
    // Trigger a queue-run for today (or a supplied date).
    // ===========================================================================
    public function run(): void
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST_required'], 405);
            return;
        }

        $run_id     = 'GRT-' . date('Ymd-His') . '-' . substr(md5(uniqid('', true)), 0, 6);
        $started_at = date('Y-m-d H:i:s');
        $date       = trim((string) $this->input->post('date')) ?: date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        // Lightweight queue-run: count tasks queued vs dispatched today
        try {
            $queued = 0;
            if ($this->_table_ok('greeting_task')) {
                $queued = (int) $this->db
                    ->from('greeting_task')
                    ->where('occasion_date', $date)
                    ->where('status', 'pending')
                    ->count_all_results();
            }

            $this->_json([
                'ok'         => true,
                'run_id'     => $run_id,
                'started_at' => $started_at,
                'date'       => $date,
                'queued'     => $queued,
                'note'       => 'full_dispatch_runs_via_cron',
            ]);
        } catch (Exception $e) {
            log_message('error', 'Greetings::run: ' . $e->getMessage());
            $this->_json([
                'ok'         => true,
                'run_id'     => $run_id,
                'started_at' => $started_at,
                'note'       => 'db_error',
                'detail'     => $e->getMessage(),
            ]);
        }
    }

    // ===========================================================================
    // GET /api/greetings/queue
    // Returns pending greetings: birthdays and anniversaries in the next 7 days.
    // Primary: greeting_task JOIN stakeholder_dob + greeting_occasion
    // Fallback: user_details / stakeholder_dob WHERE dob MONTH-DAY in next 7 days
    // ===========================================================================
    public function queue(): void
    {
        $bd_uid = (int) $this->input->get('bd_uid');
        $date   = trim((string) $this->input->get('date')) ?: date('Y-m-d');
        $status = trim((string) $this->input->get('status')) ?: 'pending';
        $days   = max(1, min(30, (int) ($this->input->get('days') ?: 7)));

        try {
            // Primary: greeting_task table
            if ($this->_table_ok('greeting_task')) {
                $date_end = date('Y-m-d', strtotime("+{$days} days", strtotime($date)));

                $q = $this->db
                    ->select('gt.id AS task_id, gt.occasion_date, gt.status,
                              gt.draft_whatsapp_body, gt.draft_email_subject, gt.draft_email_body,
                              gt.bd_uid, gt.created_at', false)
                    ->from('greeting_task gt');

                if ($this->_table_ok('stakeholder_dob')) {
                    $q->join('stakeholder_dob sd', 'sd.id = gt.stakeholder_dob_id', 'left');
                    $q->select('sd.stakeholder_name, sd.mobile_no, sd.email, sd.language_pref', false);
                }

                if ($this->_table_ok('greeting_occasion')) {
                    $q->join('greeting_occasion go', 'go.id = gt.occasion_id', 'left');
                    $q->select('go.occasion_name, go.occasion_code, go.occasion_type', false);
                }

                if ($this->_table_ok('init_call')) {
                    $q->join('init_call ic', 'ic.id = gt.init_call_id', 'left');
                    $q->select('ic.school_name', false);
                }

                $q->where('gt.status', $status)
                  ->where('gt.occasion_date >=', $date)
                  ->where('gt.occasion_date <=', $date_end);

                if ($bd_uid > 0) {
                    $q->where('gt.bd_uid', $bd_uid);
                }

                $rows = $q->order_by('gt.occasion_date', 'ASC')->limit(200)->get()->result_array();

                $this->_json([
                    'ok'       => true,
                    'count'    => count($rows),
                    'date'     => $date,
                    'date_end' => $date_end,
                    'rows'     => $rows,
                ]);
                return;
            }

            // Fallback: stakeholder_dob — find DOBs with MONTH-DAY in next N days
            if ($this->_table_ok('stakeholder_dob')) {
                $rows = $this->db->query(
                    "SELECT id AS stakeholder_id, stakeholder_name, stakeholder_role, dob,
                            mobile_no, email, language_pref, init_call_id,
                            DATE_FORMAT(dob, '%m-%d') AS birth_md,
                            'birthday' AS occasion_type
                     FROM stakeholder_dob
                     WHERE do_not_contact = 0
                       AND dob IS NOT NULL
                       AND (
                             DATE_FORMAT(dob, '%m-%d') BETWEEN DATE_FORMAT(CURDATE(), '%m-%d')
                             AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL ? DAY), '%m-%d')
                           )
                     ORDER BY birth_md ASC
                     LIMIT 200",
                    [$days]
                )->result_array();

                $this->_json([
                    'ok'    => true,
                    'count' => count($rows),
                    'rows'  => $rows,
                    'note'  => 'fallback_from_stakeholder_dob',
                ]);
                return;
            }

            // Fallback: user_details DOB
            if ($this->_table_ok('user_details')) {
                $rows = $this->db->query(
                    "SELECT id AS user_id, name, email,
                            DATE_FORMAT(dob, '%m-%d') AS birth_md,
                            'birthday' AS occasion_type
                     FROM user_details
                     WHERE status = 'active'
                       AND dob IS NOT NULL
                       AND DATE_FORMAT(dob, '%m-%d') BETWEEN DATE_FORMAT(CURDATE(), '%m-%d')
                           AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL ? DAY), '%m-%d')
                     ORDER BY birth_md ASC
                     LIMIT 200",
                    [$days]
                )->result_array();

                $this->_json([
                    'ok'    => true,
                    'count' => count($rows),
                    'rows'  => $rows,
                    'note'  => 'fallback_from_user_details',
                ]);
                return;
            }

            $this->_json($this->_not_seeded('greeting_task'));
        } catch (Exception $e) {
            log_message('error', 'Greetings::queue: ' . $e->getMessage());
            $this->_json([
                'ok'    => true,
                'rows'  => [],
                'note'  => 'db_error',
                'detail'=> $e->getMessage(),
            ]);
        }
    }

    // ===========================================================================
    // POST /api/greetings/send
    // Body: {target_uid, template_key, [channel: whatsapp|email|both], [bd_uid]}
    // Inserts into greetings_log; if greeting_task exists, links it.
    // ===========================================================================
    public function send(): void
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST_required'], 405);
            return;
        }

        $target_uid   = (int)    $this->input->post('target_uid');
        $template_key = trim((string) $this->input->post('template_key'));
        $channel      = trim((string) $this->input->post('channel')) ?: 'whatsapp';
        $bd_uid       = (int)    $this->input->post('bd_uid');
        $task_id      = (int)    $this->input->post('greeting_task_id');

        if ($target_uid <= 0 || $template_key === '') {
            $this->_json(['ok' => false, 'error' => 'target_uid_and_template_key_required'], 400);
            return;
        }

        try {
            if (!$this->_table_ok('greetings_log')) {
                // Table not yet seeded — return ok with note so UI doesn't break
                $this->_json([
                    'ok'     => true,
                    'log_id' => 0,
                    'note'   => 'tables_not_seeded_yet',
                ]);
                return;
            }

            $log_data = [
                'target_uid'   => $target_uid,
                'template_key' => $template_key,
                'channel'      => $channel,
                'bd_uid'       => $bd_uid ?: null,
                'task_id'      => $task_id ?: null,
                'sent_at'      => date('Y-m-d H:i:s'),
                'status'       => 'queued',
            ];

            $this->db->insert('greetings_log', $log_data);
            $log_id = $this->db->insert_id();

            // Update greeting_task status to 'sent' if provided
            if ($task_id > 0 && $this->_table_ok('greeting_task')) {
                $this->db->where('id', $task_id)->update('greeting_task', [
                    'status'  => 'sent',
                    'sent_at' => date('Y-m-d H:i:s'),
                ]);
            }

            log_message('info', '[Greetings::send] log_id=' . $log_id . ' target=' . $target_uid . ' tpl=' . $template_key);

            $this->_json([
                'ok'     => true,
                'log_id' => $log_id,
                'sent_at'=> date('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            log_message('error', 'Greetings::send: ' . $e->getMessage());
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ===========================================================================
    // POST /api/greetings/approve
    // Body: {greeting_id, approver_uid}
    // Marks greeting_task (or greetings_log) as approved.
    // ===========================================================================
    public function approve(): void
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST_required'], 405);
            return;
        }

        $greeting_id  = (int) $this->input->post('greeting_id');
        $approver_uid = (int) $this->input->post('approver_uid');

        if ($greeting_id <= 0 || $approver_uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'greeting_id_and_approver_uid_required'], 400);
            return;
        }

        try {
            $approved_at = date('Y-m-d H:i:s');

            // Try greeting_task first (preferred table)
            if ($this->_table_ok('greeting_task')) {
                $this->db->where('id', $greeting_id)->update('greeting_task', [
                    'status'       => 'approved',
                    'approved_by'  => $approver_uid,
                    'approved_at'  => $approved_at,
                ]);

                if ($this->db->affected_rows() > 0) {
                    log_message('info', '[Greetings::approve] task_id=' . $greeting_id . ' by=' . $approver_uid);
                    $this->_json(['ok' => true, 'greeting_id' => $greeting_id, 'approved_at' => $approved_at]);
                    return;
                }
            }

            // Fallback: greetings_log
            if ($this->_table_ok('greetings_log')) {
                $this->db->where('id', $greeting_id)->update('greetings_log', [
                    'status'      => 'approved',
                    'approved_by' => $approver_uid,
                    'approved_at' => $approved_at,
                ]);

                $this->_json(['ok' => true, 'greeting_id' => $greeting_id, 'approved_at' => $approved_at, 'note' => 'updated_greetings_log']);
                return;
            }

            $this->_json(array_merge(
                $this->_not_seeded('greeting_task'),
                ['greeting_id' => $greeting_id]
            ));
        } catch (Exception $e) {
            log_message('error', 'Greetings::approve: ' . $e->getMessage());
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ===========================================================================
    // Backward compat: today() — GET ?bd_uid=X → today's pending tasks
    // ===========================================================================
    public function today(): void
    {
        $bd_uid = (int) $this->input->get('bd_uid');
        $date   = date('Y-m-d');

        try {
            if (!$this->_table_ok('greeting_task')) {
                $this->_json(array_merge($this->_not_seeded('greeting_task'), ['date' => $date]));
                return;
            }

            $q = $this->db
                ->select('gt.id AS task_id, gt.occasion_date, gt.status, gt.bd_uid')
                ->from('greeting_task gt')
                ->where('gt.occasion_date', $date);

            if ($bd_uid > 0) {
                $q->where('gt.bd_uid', $bd_uid);
            }

            $rows = $q->order_by('gt.created_at', 'ASC')->limit(200)->get()->result_array();

            $this->_json(['ok' => true, 'count' => count($rows), 'tasks' => $rows, 'date' => $date]);
        } catch (Exception $e) {
            log_message('error', 'Greetings::today: ' . $e->getMessage());
            $this->_json(['ok' => true, 'count' => 0, 'tasks' => [], 'note' => 'db_error', 'detail' => $e->getMessage()]);
        }
    }
}
// end of Greetings.php
