<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * LiveMonitorAgent.php
 * STEM CRM - WS-C Live Monitoring AI Agent (2026-06-07)
 *
 * Detects planned-but-not-initiated, plan-not-completed, late-start/idle BDs.
 * Writes alerts to live_monitor_alert_log and notification (in-app).
 * ASCII output only. No email/SMS/push. Additive - never breaks existing features.
 *
 * Routes (class-name-only CI3 convention):
 *   GET|POST /api/livemonitor/scan?date=YYYY-MM-DD
 *   GET      /api/livemonitor/efficiency?from=YYYY-MM-DD&to=YYYY-MM-DD
 *   POST     /api/livemonitor/raise_alerts?date=YYYY-MM-DD
 *
 * LM resolution: user_details.aadmin for type_id 3 (BD),
 *                user_details.admin_id for type_id 4 (PST/CM),
 *                user_details.pst_co for type_id 13 (CM),
 *                else user_details.aadmin.
 *                Falls back to approver_override if set.
 *
 * Alert channels (user-mandated):
 *   (a) DB alert log: live_monitor_alert_log
 *   (b) In-app: notification table (user=bd_uid and user=lm_uid)
 *
 * DB tables touched (additive):
 *   READ:  task_plan_for_today, planner_coach_execution, user_details, user,
 *          approver_override, notification (read for idempotency)
 *   WRITE: live_monitor_alert_log (created IF NOT EXISTS), notification, pbni_alert
 */
class LiveMonitorAgent extends CI_Controller {

    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Thresholds
    const IDLE_THRESHOLD_MINUTES = 30;   // minutes_idle >= this => idle_high flag
    const LOW_COMPLETION_PCT     = 80;   // completion_pct below this => not_completed flag

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
        $this->_ensure_alert_log_table();
    }

    // -------------------------------------------------------------------------
    // Bearer guard
    // -------------------------------------------------------------------------
    private function _auth() {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        if (empty($hdr) || strpos($hdr, 'Bearer ') !== 0) {
            return false;
        }
        $tok = trim(substr($hdr, 7));
        return ($tok === self::BEARER);
    }

    private function _ok($data) {
        echo json_encode(array_merge(['ok' => true], $data));
        exit;
    }

    private function _err($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'error' => $msg]);
        exit;
    }

    // -------------------------------------------------------------------------
    // Ensure live_monitor_alert_log table exists (CREATE IF NOT EXISTS)
    // -------------------------------------------------------------------------
    private function _ensure_alert_log_table() {
        $sql = "CREATE TABLE IF NOT EXISTS live_monitor_alert_log (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            bd_uid INT NOT NULL,
            lm_uid INT NOT NULL DEFAULT 0,
            plan_date DATE NOT NULL,
            alert_type VARCHAR(64) NOT NULL,
            detail TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_bd_date_type (bd_uid, plan_date, alert_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->db->query($sql);
    }

    // -------------------------------------------------------------------------
    // LM resolution: mirrors DisciplineState_model::get_line_manager
    // Returns ['uid'=>int, 'name'=>string] or ['uid'=>0, 'name'=>'Unknown']
    // -------------------------------------------------------------------------
    private function _resolve_lm($bd_uid) {
        $bd_uid = (int)$bd_uid;

        // Step 1: approver_override
        $ov = $this->db->query(
            "SELECT override_to_uid FROM approver_override WHERE uid = $bd_uid LIMIT 1"
        );
        if ($ov && $ov->num_rows() > 0) {
            $lm_uid = (int)$ov->row()->override_to_uid;
        } else {
            // Step 2: user_details hierarchy
            $ud = $this->db->query(
                "SELECT type_id, admin_id, aadmin, pst_co
                 FROM user_details WHERE user_id = $bd_uid LIMIT 1"
            );
            if (!$ud || $ud->num_rows() == 0) {
                return ['uid' => 0, 'name' => 'Unknown'];
            }
            $r = $ud->row();
            $type_id = (int)$r->type_id;
            if ($type_id === 4) {
                $lm_uid = (int)$r->admin_id;
            } elseif ($type_id === 13) {
                $lm_uid = (int)$r->pst_co;
            } else {
                // type_id 3 (BD), 5, others
                $lm_uid = (int)$r->aadmin;
            }
        }

        if ($lm_uid <= 0) {
            return ['uid' => 0, 'name' => 'Unknown'];
        }

        // Step 3: fetch LM name
        $lm = $this->db->query(
            "SELECT ud.name FROM user_details ud WHERE ud.user_id = $lm_uid LIMIT 1"
        );
        $lm_name = ($lm && $lm->num_rows() > 0) ? $lm->row()->name : 'Unknown';
        $lm_name = $this->_ascii($lm_name);

        return ['uid' => $lm_uid, 'name' => $lm_name];
    }

    // -------------------------------------------------------------------------
    // Sanitize string to ASCII only (no em/en dashes, no special chars)
    // -------------------------------------------------------------------------
    private function _ascii($str) {
        if (!is_string($str)) return '';
        // Replace common non-ASCII
        $str = str_replace(["\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\x99"], ['-', '-', "'"], $str);
        // Strip remaining non-ASCII
        $str = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $str);
        return trim($str);
    }

    // -------------------------------------------------------------------------
    // Build scan data for a given date
    // Returns array of flagged BD rows
    // -------------------------------------------------------------------------
    private function _run_scan($date) {
        $date_esc = $this->db->escape_str($date);

        // Pull all BDs who have a plan on this date from task_plan_for_today
        $sql_tpt = "
            SELECT tpt.user_id,
                   ud.name        AS bd_name,
                   ud.type_id,
                   tpt.approvel_status,
                   tpt.taskcnt    AS tpt_task_count
            FROM task_plan_for_today tpt
            JOIN user_details ud ON ud.user_id = tpt.user_id
            WHERE tpt.date = '$date_esc'
            GROUP BY tpt.user_id
        ";
        $q_tpt = $this->db->query($sql_tpt);
        if (!$q_tpt || $q_tpt->num_rows() == 0) {
            return [];
        }
        $planned_bds = $q_tpt->result_array();

        // Pull planner_coach_execution rows for this date (may be empty)
        $sql_pce = "
            SELECT bd_uid, tasks_planned, tasks_started, tasks_completed,
                   tasks_cancelled, completion_pct, minutes_idle, late_start_flag
            FROM planner_coach_execution
            WHERE plan_date = '$date_esc'
        ";
        $q_pce = $this->db->query($sql_pce);
        $pce_map = [];
        if ($q_pce && $q_pce->num_rows() > 0) {
            foreach ($q_pce->result_array() as $row) {
                $pce_map[$row['bd_uid']] = $row;
            }
        }

        $flagged = [];
        foreach ($planned_bds as $bd) {
            $bd_uid  = (int)$bd['user_id'];
            $bd_name = $this->_ascii($bd['bd_name']);
            $lm      = $this->_resolve_lm($bd_uid);
            $lm_uid  = $lm['uid'];
            $lm_name = $lm['name'];

            // Get execution data from planner_coach_execution if available
            $pce = isset($pce_map[$bd_uid]) ? $pce_map[$bd_uid] : null;

            if ($pce) {
                $tasks_planned    = (int)$pce['tasks_planned'];
                $tasks_started    = (int)$pce['tasks_started'];
                $tasks_completed  = (int)$pce['tasks_completed'];
                $completion_pct   = round((float)$pce['completion_pct'], 1);
                $minutes_idle     = (int)$pce['minutes_idle'];
                $late_start_flag  = (int)$pce['late_start_flag'];
            } else {
                // No execution row - use tpt data, infer defaults
                $tasks_planned    = isset($bd['tpt_task_count']) ? (int)$bd['tpt_task_count'] : 1;
                $tasks_started    = 0;
                $tasks_completed  = 0;
                $completion_pct   = 0.0;
                $minutes_idle     = 0;
                $late_start_flag  = 0;
            }

            // Efficiency percent = completed/planned * 100
            $efficiency_percent = ($tasks_planned > 0)
                ? round(($tasks_completed / $tasks_planned) * 100.0, 1)
                : 0.0;

            // Detect flags
            $flags = [];

            // PLANNED BUT NOT INITIATED: plan exists, nothing started
            if ($tasks_planned > 0 && $tasks_started == 0) {
                $flags[] = 'planned_not_initiated';
            }

            // PLAN NOT COMPLETED: planned > completed and low completion pct
            if ($tasks_planned > $tasks_completed && $completion_pct < self::LOW_COMPLETION_PCT) {
                if (!in_array('planned_not_initiated', $flags)) {
                    $flags[] = 'not_completed';
                }
            }

            // LATE START
            if ($late_start_flag == 1) {
                $flags[] = 'late_start';
            }

            // HIGH IDLE
            if ($minutes_idle >= self::IDLE_THRESHOLD_MINUTES) {
                $flags[] = 'idle_high';
            }

            if (empty($flags)) continue; // BD is performing fine

            $flagged[] = [
                'bd_uid'             => $bd_uid,
                'bd_name'            => $bd_name,
                'lm_uid'             => $lm_uid,
                'lm_name'            => $lm_name,
                'tasks_planned'      => $tasks_planned,
                'tasks_started'      => $tasks_started,
                'tasks_completed'    => $tasks_completed,
                'completion_pct'     => $completion_pct,
                'efficiency_percent' => $efficiency_percent,
                'minutes_idle'       => $minutes_idle,
                'late_start_flag'    => $late_start_flag,
                'flags'              => $flags,
            ];
        }

        return $flagged;
    }

    // -------------------------------------------------------------------------
    // GET|POST /api/livemonitor/scan?date=YYYY-MM-DD
    // -------------------------------------------------------------------------
    public function scan() {
        if (!$this->_auth()) $this->_err('Unauthorized', 401);

        $date = $this->input->get('date');
        if (empty($date)) $date = $this->input->post('date');
        if (empty($date)) $date = date('Y-m-d');

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->_err('Invalid date format. Use YYYY-MM-DD.');
        }

        $flagged = $this->_run_scan($date);

        if (empty($flagged)) {
            $this->_ok([
                'date'    => $date,
                'empty'   => true,
                'flagged' => [],
                'summary' => [
                    'total_bds'              => 0,
                    'planned_not_initiated'  => 0,
                    'not_completed'          => 0,
                    'avg_efficiency_percent' => 0.0,
                ],
            ]);
        }

        // Summary counts
        $cnt_pni       = 0;
        $cnt_nc        = 0;
        $sum_eff       = 0.0;
        foreach ($flagged as $f) {
            if (in_array('planned_not_initiated', $f['flags'])) $cnt_pni++;
            if (in_array('not_completed', $f['flags']))         $cnt_nc++;
            $sum_eff += $f['efficiency_percent'];
        }
        $total_bds = count($flagged);
        $avg_eff   = ($total_bds > 0) ? round($sum_eff / $total_bds, 1) : 0.0;

        $this->_ok([
            'date'    => $date,
            'empty'   => false,
            'flagged' => $flagged,
            'summary' => [
                'total_bds'              => $total_bds,
                'planned_not_initiated'  => $cnt_pni,
                'not_completed'          => $cnt_nc,
                'avg_efficiency_percent' => $avg_eff,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/livemonitor/efficiency?from=YYYY-MM-DD&to=YYYY-MM-DD
    // Plan-to-execution efficiency rollup over a date range
    // -------------------------------------------------------------------------
    public function efficiency() {
        if (!$this->_auth()) $this->_err('Unauthorized', 401);

        $from = $this->input->get('from');
        $to   = $this->input->get('to');
        if (empty($from)) $from = date('Y-m-d', strtotime('-7 days'));
        if (empty($to))   $to   = date('Y-m-d');

        $from_esc = $this->db->escape_str($from);
        $to_esc   = $this->db->escape_str($to);

        // Primary source: planner_coach_execution (if data exists)
        $sql_pce = "
            SELECT pce.bd_uid,
                   ud.name AS bd_name,
                   COUNT(*)                              AS days_with_data,
                   SUM(pce.tasks_planned)                AS total_planned,
                   SUM(pce.tasks_completed)              AS total_completed,
                   ROUND(AVG(pce.completion_pct), 1)     AS avg_completion_pct,
                   SUM(pce.late_start_flag)              AS late_start_days,
                   MIN(pce.plan_date)                    AS first_date,
                   MAX(pce.plan_date)                    AS last_date
            FROM planner_coach_execution pce
            LEFT JOIN user_details ud ON ud.user_id = pce.bd_uid
            WHERE pce.plan_date BETWEEN '$from_esc' AND '$to_esc'
            GROUP BY pce.bd_uid
            ORDER BY avg_completion_pct ASC
        ";
        $q_pce = $this->db->query($sql_pce);
        $pce_rows = ($q_pce && $q_pce->num_rows() > 0) ? $q_pce->result_array() : [];

        // Fallback: use task_plan_for_today if pce is empty
        $sql_tpt = "
            SELECT tpt.user_id AS bd_uid,
                   ud.name AS bd_name,
                   COUNT(*)                    AS days_with_plan,
                   SUM(tpt.taskcnt)            AS total_planned,
                   0                           AS total_completed,
                   0.0                         AS avg_completion_pct,
                   MIN(tpt.date)               AS first_date,
                   MAX(tpt.date)               AS last_date
            FROM task_plan_for_today tpt
            LEFT JOIN user_details ud ON ud.user_id = tpt.user_id
            WHERE tpt.date BETWEEN '$from_esc' AND '$to_esc'
            GROUP BY tpt.user_id
            ORDER BY total_planned DESC
        ";

        $rows = [];
        if (!empty($pce_rows)) {
            foreach ($pce_rows as $r) {
                $total_planned   = (int)$r['total_planned'];
                $total_completed = (int)$r['total_completed'];
                $eff_pct = ($total_planned > 0)
                    ? round(($total_completed / $total_planned) * 100.0, 1)
                    : 0.0;
                $rows[] = [
                    'bd_uid'             => (int)$r['bd_uid'],
                    'bd_name'            => $this->_ascii($r['bd_name']),
                    'days_with_data'     => (int)$r['days_with_data'],
                    'total_planned'      => $total_planned,
                    'total_completed'    => $total_completed,
                    'avg_completion_pct' => (float)$r['avg_completion_pct'],
                    'efficiency_percent' => $eff_pct,
                    'late_start_days'    => (int)$r['late_start_days'],
                    'first_date'         => $r['first_date'],
                    'last_date'          => $r['last_date'],
                    'data_source'        => 'planner_coach_execution',
                    'trend'              => ($eff_pct >= 80) ? 'on_track' : (($eff_pct >= 50) ? 'at_risk' : 'behind'),
                ];
            }
        } else {
            // planner_coach_execution empty - use task_plan_for_today
            $q_tpt = $this->db->query($sql_tpt);
            if ($q_tpt && $q_tpt->num_rows() > 0) {
                foreach ($q_tpt->result_array() as $r) {
                    $rows[] = [
                        'bd_uid'             => (int)$r['bd_uid'],
                        'bd_name'            => $this->_ascii($r['bd_name']),
                        'days_with_data'     => (int)$r['days_with_plan'],
                        'total_planned'      => (int)$r['total_planned'],
                        'total_completed'    => 0,
                        'avg_completion_pct' => 0.0,
                        'efficiency_percent' => 0.0,
                        'late_start_days'    => 0,
                        'first_date'         => $r['first_date'],
                        'last_date'          => $r['last_date'],
                        'data_source'        => 'task_plan_for_today',
                        'trend'              => 'behind',
                    ];
                }
            }
        }

        if (empty($rows)) {
            $this->_ok([
                'from'  => $from,
                'to'    => $to,
                'empty' => true,
                'bds'   => [],
                'summary' => [
                    'total_bds'              => 0,
                    'total_planned'          => 0,
                    'total_completed'        => 0,
                    'avg_efficiency_percent' => 0.0,
                ],
            ]);
        }

        $sum_plan = 0; $sum_comp = 0; $sum_eff = 0.0;
        foreach ($rows as $r) {
            $sum_plan += $r['total_planned'];
            $sum_comp += $r['total_completed'];
            $sum_eff  += $r['efficiency_percent'];
        }
        $cnt = count($rows);
        $avg_eff = ($cnt > 0) ? round($sum_eff / $cnt, 1) : 0.0;
        $overall_eff = ($sum_plan > 0) ? round(($sum_comp / $sum_plan) * 100.0, 1) : 0.0;

        $this->_ok([
            'from'  => $from,
            'to'    => $to,
            'empty' => false,
            'bds'   => $rows,
            'summary' => [
                'total_bds'              => $cnt,
                'total_planned'          => $sum_plan,
                'total_completed'        => $sum_comp,
                'avg_efficiency_percent' => $avg_eff,
                'overall_efficiency_pct' => $overall_eff,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/livemonitor/raise_alerts?date=YYYY-MM-DD
    // Write alerts for all flagged BDs to live_monitor_alert_log + notification
    // -------------------------------------------------------------------------
    public function raise_alerts() {
        if (!$this->_auth()) $this->_err('Unauthorized', 401);

        $date = $this->input->get('date');
        if (empty($date)) $date = $this->input->post('date');
        if (empty($date)) $date = date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->_err('Invalid date format. Use YYYY-MM-DD.');
        }

        $flagged = $this->_run_scan($date);

        if (empty($flagged)) {
            $this->_ok([
                'date'                 => $date,
                'alerts_logged'        => 0,
                'notifications_created'=> 0,
                'recipients'           => [],
                'message'              => 'No flagged BDs for this date. No alerts raised.',
            ]);
        }

        $date_esc     = $this->db->escape_str($date);
        $now          = date('Y-m-d H:i:s');
        $now_esc      = $this->db->escape_str($now);
        $alerts_logged = 0;
        $notifs_created = 0;
        $recipients    = [];

        // Get current max notification id for manual increment
        $max_id_q = $this->db->query("SELECT COALESCE(MAX(id), 0) AS max_id FROM notification");
        $next_notif_id = ($max_id_q ? (int)$max_id_q->row()->max_id : 0) + 1;

        foreach ($flagged as $bd) {
            $bd_uid  = (int)$bd['bd_uid'];
            $lm_uid  = (int)$bd['lm_uid'];
            $bd_name = $this->_ascii($bd['bd_name']);
            $flags   = $bd['flags'];
            $tasks_planned   = (int)$bd['tasks_planned'];
            $tasks_started   = (int)$bd['tasks_started'];
            $tasks_completed = (int)$bd['tasks_completed'];
            $eff_pct         = (float)$bd['efficiency_percent'];

            $recipient_entry = [
                'bd_uid'   => $bd_uid,
                'bd_name'  => $bd_name,
                'lm_uid'   => $lm_uid,
                'flags'    => $flags,
                'alert_rows_written' => 0,
                'notifs_written'     => 0,
            ];

            foreach ($flags as $alert_type) {
                $alert_type_esc = $this->db->escape_str($alert_type);

                // IDEMPOTENCY: check if alert already logged today
                $dup = $this->db->query(
                    "SELECT id FROM live_monitor_alert_log
                     WHERE bd_uid = $bd_uid AND plan_date = '$date_esc' AND alert_type = '$alert_type_esc'
                     LIMIT 1"
                );
                if ($dup && $dup->num_rows() > 0) {
                    continue; // already logged, skip
                }

                // Build detail string (ASCII only)
                $detail = $this->_build_detail($alert_type, $bd, $date);
                $detail_esc = $this->db->escape_str($detail);
                $bd_uid_int = $bd_uid;
                $lm_uid_int = $lm_uid;

                // (a) Write to live_monitor_alert_log
                $this->db->query(
                    "INSERT INTO live_monitor_alert_log
                        (bd_uid, lm_uid, plan_date, alert_type, detail, created_at)
                     VALUES
                        ($bd_uid_int, $lm_uid_int, '$date_esc', '$alert_type_esc', '$detail_esc', '$now_esc')"
                );
                $alerts_logged++;
                $recipient_entry['alert_rows_written']++;

                // (b) In-app notification to BD
                $msg_bd = $this->_ascii(
                    "Live Monitor Alert [$alert_type] for $bd_name on $date. " .
                    "Tasks planned: $tasks_planned, started: $tasks_started, completed: $tasks_completed. " .
                    "Efficiency: $eff_pct percent. Please action."
                );
                $msg_bd_esc = $this->db->escape_str($msg_bd);
                $this->db->query(
                    "INSERT INTO notification (id, msg, user, company_id, date, status)
                     VALUES ($next_notif_id, '$msg_bd_esc', '$bd_uid', '0', '$now_esc', 'pending')"
                );
                $next_notif_id++;
                $notifs_created++;
                $recipient_entry['notifs_written']++;

                // (b) In-app notification to LM (if LM uid is valid)
                if ($lm_uid > 0) {
                    $lm_name = $this->_ascii($bd['lm_name']);
                    $msg_lm = $this->_ascii(
                        "Live Monitor Alert: BD $bd_name ($bd_uid) flag [$alert_type] on $date. " .
                        "Tasks planned: $tasks_planned, started: $tasks_started, completed: $tasks_completed. " .
                        "Efficiency: $eff_pct percent. Action required."
                    );
                    $msg_lm_esc = $this->db->escape_str($msg_lm);
                    $this->db->query(
                        "INSERT INTO notification (id, msg, user, company_id, date, status)
                         VALUES ($next_notif_id, '$msg_lm_esc', '$lm_uid', '0', '$now_esc', 'pending')"
                    );
                    $next_notif_id++;
                    $notifs_created++;
                    $recipient_entry['notifs_written']++;
                }

                // (c) Optionally insert into pbni_alert for planned_not_initiated
                if ($alert_type === 'planned_not_initiated') {
                    // pbni_alert schema: user_id, pbni_count, lm_uid, notified_at, approval_status
                    // Idempotency: check existing pending row for same bd today
                    $dup_pbni = $this->db->query(
                        "SELECT id FROM pbni_alert
                         WHERE user_id = $bd_uid
                           AND DATE(notified_at) = '$date_esc'
                           AND approval_status = 'Pending'
                         LIMIT 1"
                    );
                    if (!$dup_pbni || $dup_pbni->num_rows() == 0) {
                        $pbni_count = $tasks_planned;
                        $this->db->query(
                            "INSERT INTO pbni_alert (user_id, pbni_count, lm_uid, notified_at, approval_status)
                             VALUES ($bd_uid, $pbni_count, $lm_uid, '$now_esc', 'Pending')"
                        );
                    }
                }
            }

            $recipients[] = $recipient_entry;
        }

        $this->_ok([
            'date'                  => $date,
            'alerts_logged'         => $alerts_logged,
            'notifications_created' => $notifs_created,
            'recipients'            => $recipients,
        ]);
    }

    // -------------------------------------------------------------------------
    // Build human-readable ASCII detail string for alert log
    // -------------------------------------------------------------------------
    private function _build_detail($alert_type, $bd, $date) {
        $bd_name = $this->_ascii($bd['bd_name']);
        $tp = (int)$bd['tasks_planned'];
        $ts = (int)$bd['tasks_started'];
        $tc = (int)$bd['tasks_completed'];
        $ep = (float)$bd['efficiency_percent'];
        $mi = (int)$bd['minutes_idle'];

        switch ($alert_type) {
            case 'planned_not_initiated':
                return "BD $bd_name ($bd[bd_uid]) has $tp task(s) planned on $date but 0 initiated. Plan-to-execution efficiency: $ep percent.";
            case 'not_completed':
                return "BD $bd_name ($bd[bd_uid]) planned $tp task(s) on $date, completed only $tc. Efficiency: $ep percent.";
            case 'late_start':
                return "BD $bd_name ($bd[bd_uid]) had a late start on $date. Tasks planned: $tp, started: $ts.";
            case 'idle_high':
                return "BD $bd_name ($bd[bd_uid]) idle for $mi minute(s) on $date (threshold: " . self::IDLE_THRESHOLD_MINUTES . " min). Tasks planned: $tp, completed: $tc.";
            default:
                return "BD $bd_name ($bd[bd_uid]) flagged [$alert_type] on $date. Tasks planned: $tp, completed: $tc.";
        }
    }
}
