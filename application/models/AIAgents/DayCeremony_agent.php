<?php
/**
 * DayCeremony_model.php
 * Deploy to: application/models/AIAgents/DayCeremony_model.php
 *
 * M055 Day Management - day start GPS check-in and day close ceremony.
 * All SQL uses parameterized queries via CodeIgniter Active Record / query bindings.
 * Pilot WB-5: when flag_value=1, only WB-5 uids are allowed.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class DayCeremony_model extends CI_Model
{
    // Feature flag code for this feature.
    const FLAG_CODE = 'day_ceremony_055_enabled';

    // Pilot WB-5 user IDs (flag_value=1 scope).
    const PILOT_UIDS = [1000289, 1000351, 1000305, 1000269, 1000356];

    // HR email address.
    const HR_EMAIL = 'hr@stemcrm.local';

    // HR email sender.
    const FROM_EMAIL = 'noreply@stemcrm.local';

    public function __construct()
    {
        parent::__construct();
        // M070: shared geofence library (loaded once per request).
        if (!class_exists('Geofence_helper')) {
            $this->load->library('Geofence_helper');
        } elseif (!isset($this->geofence_helper)) {
            $this->load->library('Geofence_helper');
        }
    }

    // -------------------------------------------------------------------------
    // M070 INTERNAL: load active home anchor for a user.
    // Returns ['label','lat','lng','radius_km'] or null when unset.
    // -------------------------------------------------------------------------
    private function _load_home_anchor($uid)
    {
        $row = $this->db
            ->where(['user_id' => (int)$uid, 'anchor_label' => 'home', 'active' => 1])
            ->order_by('id', 'desc')->limit(1)
            ->get('day_start_home_anchor_v2')->row_array();
        if (!$row) { return null; }
        return [
            'label'     => 'home',
            'lat'       => isset($row['lat']) ? (double)$row['lat'] : null,
            'lng'       => isset($row['lng']) ? (double)$row['lng'] : null,
            'radius_km' => isset($row['radius_km']) ? (double)$row['radius_km'] : 5.0,
        ];
    }

    // -------------------------------------------------------------------------
    // INTERNAL: get current flag value (0=off, 1=pilot, 2=org-wide)
    // -------------------------------------------------------------------------
    private function get_flag_value()
    {
        $row = $this->db->get_where('feature_flag', ['flag_key' => self::FLAG_CODE])->row_array();
        return $row ? (int) $row['flag_value'] : 0;
    }

    // -------------------------------------------------------------------------
    // INTERNAL: enforce pilot guard.
    // Returns true if the uid is allowed to use this feature.
    // flag_value=0: nobody. flag_value=1: pilot list only. flag_value=2: everyone.
    // -------------------------------------------------------------------------
    private function is_uid_allowed($uid, $flag_value)
    {
        if ($flag_value === 0) {
            return false;
        }
        if ($flag_value === 1) {
            return in_array((int) $uid, self::PILOT_UIDS, true);
        }
        // flag_value=2: org-wide
        return true;
    }

    // -------------------------------------------------------------------------
    // INTERNAL: sanitize integer
    // -------------------------------------------------------------------------
    private function safe_int($val, $default = null)
    {
        if ($val === null || $val === '') {
            return $default;
        }
        return (int) $val;
    }

    // -------------------------------------------------------------------------
    // INTERNAL: sanitize string (strip tags, trim)
    // -------------------------------------------------------------------------
    private function safe_str($val)
    {
        if ($val === null) {
            return null;
        }
        return trim(strip_tags((string) $val));
    }

    // -------------------------------------------------------------------------
    // INTERNAL: sanitize decimal coordinate
    // -------------------------------------------------------------------------
    private function safe_decimal($val)
    {
        if ($val === null || $val === '') {
            return null;
        }
        return round((float) $val, 7);
    }

    // -------------------------------------------------------------------------
    // probe()
    // Returns: ['ok' => true, 'flag_enabled' => 0|1|2, 'today_count' => N]
    // -------------------------------------------------------------------------
    public function probe()
    {
        $flag_value = $this->get_flag_value();
        $today_count = $this->db
            ->where('ceremony_date', date('Y-m-d'))
            ->count_all_results('day_ceremony');

        return [
            'ok'           => true,
            'flag_enabled' => $flag_value,
            'today_count'  => (int) $today_count,
        ];
    }

    // -------------------------------------------------------------------------
    // start_day($uid, $lat, $lng, $gps_accuracy_m)
    // Inserts day_ceremony row with status='day_started'.
    // Returns: ['success' => true, 'ceremony_id' => N] or ['success' => false, 'error' => msg]
    // -------------------------------------------------------------------------
    public function start_day($uid, $lat, $lng, $gps_accuracy_m)
    {
        $uid           = $this->safe_int($uid);
        $lat           = $this->safe_decimal($lat);
        $lng           = $this->safe_decimal($lng);
        $gps_accuracy  = $this->safe_int($gps_accuracy_m);
        $today          = date('Y-m-d');
        $now            = date('Y-m-d H:i:s');

        $flag_value = $this->get_flag_value();
        if (!$this->is_uid_allowed($uid, $flag_value)) {
            return ['success' => false, 'error' => 'Feature not enabled for this user.'];
        }

        // Check no existing row for today for this uid.
        $existing = $this->db
            ->get_where('day_ceremony', ['uid' => $uid, 'ceremony_date' => $today])
            ->row_array();

        if ($existing) {
            return ['success' => false, 'error' => 'Day already started for today.'];
        }

        $data = [
            'uid'                       => $uid,
            'ceremony_date'             => $today,
            'day_start_at'              => $now,
            'day_start_lat'             => $lat,
            'day_start_lng'             => $lng,
            'day_start_gps_accuracy_m'  => $gps_accuracy,
            'status'                    => 'day_started',
        ];

        $this->db->insert('day_ceremony', $data);
        $ceremony_id = $this->db->insert_id();

        if (!$ceremony_id) {
            return ['success' => false, 'error' => 'Failed to create day ceremony record.'];
        }

        $this->_audit($ceremony_id, $uid, 'start', $data);

        // M070 geofence gate evaluation (non-blocking: always records, never aborts start_day)
        try {
            $anchor = $this->_load_home_anchor($uid);
            $gate = $this->geofence_helper->evaluate_gate($lat, $lng, $gps_accuracy, null, $anchor);
            $this->geofence_helper->log_gate($this, $uid, 'day_start', [
                'ref_table'        => 'day_ceremony',
                'ref_id'           => (int)$ceremony_id,
                'lat'              => $lat,
                'lng'              => $lng,
                'accuracy_m'       => $gps_accuracy,
                'anchor_label'     => $anchor['label']     ?? null,
                'anchor_lat'       => $anchor['lat']       ?? null,
                'anchor_lng'       => $anchor['lng']       ?? null,
                'anchor_radius_km' => $anchor['radius_km'] ?? null,
                'distance_m'       => $gate['distance_m'],
                'gate_status'      => $gate['status'],
                'is_mock'          => $gate['is_mock'],
            ]);
            // Mirror into day_ceremony_v2 (radius_ok + blocked_reason) so cron 58f84d08 sees it.
            $today_str = date('Y-m-d');
            $existing_v2 = $this->db->get_where('day_ceremony_v2', ['user_id'=>$uid,'ceremony_date'=>$today_str])->row_array();
            $v2 = [
                'ustart'                => $now,
                'ustart_lat'            => $lat,
                'ustart_lng'            => $lng,
                'ustart_radius_ok'      => ($gate['status']==='pass') ? 1 : 0,
                'ustart_blocked_reason' => ($gate['status']==='pass') ? null : $gate['status'],
            ];
            if ($existing_v2) {
                $this->db->where(['user_id'=>$uid,'ceremony_date'=>$today_str])->update('day_ceremony_v2', $v2);
            } else {
                $this->db->insert('day_ceremony_v2', array_merge($v2, ['user_id'=>$uid,'ceremony_date'=>$today_str]));
            }
            return ['success'=>true,'ceremony_id'=>(int)$ceremony_id,'gate'=>$gate,'anchor'=>$anchor];
        } catch (Throwable $t) {
            error_log('[DayCeremony_agent] geofence gate failed: ' . $t->getMessage());
            return ['success' => true, 'ceremony_id' => (int) $ceremony_id, 'gate' => ['status'=>'missing','distance_m'=>null,'is_mock'=>0]];
        }
    }

    // -------------------------------------------------------------------------
    // close_day($uid, $payload)
    // payload keys: tasks_planned, tasks_done, kpi_meetings_completed,
    //   kpi_moms_written, kpi_leads_progressed, blockers_text,
    //   achievements_text, lat, lng
    // Returns: ['success' => true] or ['success' => false, 'error' => msg]
    // -------------------------------------------------------------------------
    public function close_day($uid, $payload)
    {
        $uid   = $this->safe_int($uid);
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');

        $flag_value = $this->get_flag_value();
        if (!$this->is_uid_allowed($uid, $flag_value)) {
            return ['success' => false, 'error' => 'Feature not enabled for this user.'];
        }

        // Find the open/day_started row for today.
        $row = $this->db
            ->where('uid', $uid)
            ->where('ceremony_date', $today)
            ->where_in('status', ['open', 'day_started'])
            ->get('day_ceremony')
            ->row_array();

        if (!$row) {
            return ['success' => false, 'error' => 'No open ceremony found for today. Start the day first.'];
        }

        $ceremony_id = (int) $row['id'];

        $update = [
            'status'                => 'closed',
            'day_close_at'          => $now,
            'day_close_lat'         => $this->safe_decimal($payload['lat'] ?? null),
            'day_close_lng'         => $this->safe_decimal($payload['lng'] ?? null),
            'tasks_planned'         => $this->safe_int($payload['tasks_planned'] ?? 0, 0),
            'tasks_done'            => $this->safe_int($payload['tasks_done'] ?? 0, 0),
            'kpi_meetings_completed'=> $this->safe_int($payload['kpi_meetings_completed'] ?? 0, 0),
            'kpi_moms_written'      => $this->safe_int($payload['kpi_moms_written'] ?? 0, 0),
            'kpi_leads_progressed'  => $this->safe_int($payload['kpi_leads_progressed'] ?? 0, 0),
            'blockers_text'         => $this->safe_str($payload['blockers_text'] ?? null),
            'achievements_text'     => $this->safe_str($payload['achievements_text'] ?? null),
        ];

        $this->db->where('id', $ceremony_id)->update('day_ceremony', $update);

        $this->_audit($ceremony_id, $uid, 'close', $update);

        // Send HR email.
        $this->send_hr_email($ceremony_id);

        // M070 geofence gate at close (non-blocking)
        $close_lat = $this->safe_decimal($payload['lat'] ?? null);
        $close_lng = $this->safe_decimal($payload['lng'] ?? null);
        $close_acc = $this->safe_int($payload['gps_accuracy_m'] ?? null);
        $gate_out = ['status'=>'missing','distance_m'=>null,'is_mock'=>0];
        try {
            $anchor = $this->_load_home_anchor($uid);
            $gate = $this->geofence_helper->evaluate_gate($close_lat, $close_lng, $close_acc, null, $anchor);
            $gate_out = $gate;
            $this->geofence_helper->log_gate($this, $uid, 'day_close', [
                'ref_table'        => 'day_ceremony',
                'ref_id'           => (int)$ceremony_id,
                'lat'              => $close_lat,
                'lng'              => $close_lng,
                'accuracy_m'       => $close_acc,
                'anchor_label'     => $anchor['label']     ?? null,
                'anchor_lat'       => $anchor['lat']       ?? null,
                'anchor_lng'       => $anchor['lng']       ?? null,
                'anchor_radius_km' => $anchor['radius_km'] ?? null,
                'distance_m'       => $gate['distance_m'],
                'gate_status'      => $gate['status'],
                'is_mock'          => $gate['is_mock'],
            ]);
            $existing_v2 = $this->db->get_where('day_ceremony_v2', ['user_id'=>$uid,'ceremony_date'=>$today])->row_array();
            $v2 = [
                'uclose'                => $now,
                'uclose_lat'            => $close_lat,
                'uclose_lng'            => $close_lng,
                'uclose_blocked_reason' => ($gate['status']==='pass') ? null : $gate['status'],
            ];
            if ($existing_v2) {
                $this->db->where(['user_id'=>$uid,'ceremony_date'=>$today])->update('day_ceremony_v2', $v2);
            } else {
                $this->db->insert('day_ceremony_v2', array_merge($v2, ['user_id'=>$uid,'ceremony_date'=>$today]));
            }
        } catch (Throwable $t) {
            error_log('[DayCeremony_agent] close geofence gate failed: ' . $t->getMessage());
        }

        return ['success' => true, 'ceremony_id' => $ceremony_id, 'gate' => $gate_out];
    }

    // -------------------------------------------------------------------------
    // get_today_status($uid)
    // Returns current day_ceremony row for today or null.
    // -------------------------------------------------------------------------
    public function get_today_status($uid)
    {
        $uid   = $this->safe_int($uid);
        $today = date('Y-m-d');

        $flag_value = $this->get_flag_value();
        if (!$this->is_uid_allowed($uid, $flag_value)) {
            return null;
        }

        return $this->db
            ->get_where('day_ceremony', ['uid' => $uid, 'ceremony_date' => $today])
            ->row_array() ?: null;
    }

    // -------------------------------------------------------------------------
    // send_hr_email($ceremony_id)
    // Builds email body, queues to stem_outbound_email, audits.
    // Falls back to file log if queue table unavailable.
    // -------------------------------------------------------------------------
    public function send_hr_email($ceremony_id)
    {
        $ceremony_id = $this->safe_int($ceremony_id);

        $row = $this->db
            ->get_where('day_ceremony', ['id' => $ceremony_id])
            ->row_array();

        if (!$row) {
            $this->_log_error("send_hr_email: ceremony_id $ceremony_id not found.");
            return false;
        }

        $uid  = (int) $row['uid'];
        $date = $row['ceremony_date'];

        // Fetch user name.
        $user = $this->db->get_where('user', ['uid' => $uid])->row_array();
        $name = $user ? ($user['name'] ?? "UID $uid") : "UID $uid";

        $tasks_done   = (int) $row['tasks_done'];
        $tasks_planned = (int) $row['tasks_planned'];
        $completion   = $tasks_planned > 0
            ? round(($tasks_done / $tasks_planned) * 100) . ' percent'
            : 'N/A';

        $subject = "Day Ceremony Summary - $name - $date";

        $body  = "Day Ceremony Summary\n";
        $body .= "====================\n";
        $body .= "User       : $name (UID $uid)\n";
        $body .= "Date       : $date\n";
        $body .= "Status     : {$row['status']}\n";
        $body .= "Day Start  : " . ($row['day_start_at'] ?? 'Not recorded') . "\n";
        $body .= "Day Close  : " . ($row['day_close_at'] ?? 'Not recorded') . "\n";
        $body .= "\nKPI Summary\n";
        $body .= "-----------\n";
        $body .= "Tasks Planned         : {$row['tasks_planned']}\n";
        $body .= "Tasks Done            : {$row['tasks_done']} ($completion)\n";
        $body .= "Meetings Completed    : {$row['kpi_meetings_completed']}\n";
        $body .= "MoMs Written          : {$row['kpi_moms_written']}\n";
        $body .= "Leads Progressed      : {$row['kpi_leads_progressed']}\n";
        $body .= "\nBlockers:\n" . ($row['blockers_text'] ?? 'None') . "\n";
        $body .= "\nAchievements:\n" . ($row['achievements_text'] ?? 'None') . "\n";

        $queued = $this->_queue_email(self::HR_EMAIL, self::FROM_EMAIL, $subject, $body, 'day_ceremony', $ceremony_id);

        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $ceremony_id)->update('day_ceremony', ['hr_email_sent_at' => $now]);

        $this->_audit($ceremony_id, $uid, 'hr_email', ['queued' => $queued, 'to' => self::HR_EMAIL]);

        return $queued;
    }

    // -------------------------------------------------------------------------
    // get_rollup($date)
    // Returns compliance metrics array per uid for the given date.
    // Used by 6 AM IST compliance brief cron.
    // Falls back to querying day_ceremony directly if date != CURDATE().
    // -------------------------------------------------------------------------
    public function get_rollup($date)
    {
        $date = date('Y-m-d', strtotime($this->safe_str($date) ?: date('Y-m-d')));

        // Query day_ceremony table directly (v_day_ceremony_today view not available)
        $rows = $this->db
            ->select('dc.*, u.name AS user_name, u.type_id')
            ->from('day_ceremony dc')
            ->join('user u', 'u.uid = dc.uid', 'left')
            ->where('dc.ceremony_date', $date)
            ->get()
            ->result_array();

        $total    = count($rows);
        $started  = 0;
        $closed   = 0;
        $no_start = 0;

        foreach ($rows as $r) {
            $status = $r['status'] ?? 'open';
            if ($status === 'day_started') {
                $started++;
            } elseif ($status === 'closed') {
                $closed++;
            } else {
                $no_start++;
            }
        }

        return [
            'date'       => $date,
            'total'      => $total,
            'closed'     => $closed,
            'started'    => $started,
            'no_start'   => $no_start,
            'records'    => $rows,
        ];
    }

    // -------------------------------------------------------------------------
    // get_leave_status($uid, $date)
    // Queries leave_request for approved leave matching uid + date.
    // Returns null or ['leave_type' => ..., 'status' => ...].
    // -------------------------------------------------------------------------
    public function get_leave_status($uid, $date)
    {
        $uid  = $this->safe_int($uid);
        $date = date('Y-m-d', strtotime($this->safe_str($date) ?: date('Y-m-d')));

        $row = $this->db
            ->select('leave_type, status')
            ->where('uid', $uid)
            ->where('status', 'approved')
            ->where('leave_date <=', $date)
            ->where('leave_end_date >=', $date)
            ->get('leave_request')
            ->row_array();

        return $row ?: null;
    }

    // -------------------------------------------------------------------------
    // mark_hr_emailed_for_leave($uid, $date)
    // Sends HR a "BD on leave" email and audits it.
    // Returns ['success' => true|false, 'error' => msg (if false)]
    // -------------------------------------------------------------------------
    public function mark_hr_emailed_for_leave($uid, $date)
    {
        $uid  = $this->safe_int($uid);
        $date = date('Y-m-d', strtotime($this->safe_str($date) ?: date('Y-m-d')));

        $leave = $this->get_leave_status($uid, $date);
        if (!$leave) {
            return ['success' => false, 'error' => 'No approved leave found for uid on that date.'];
        }

        $flag_value = $this->get_flag_value();
        if (!$this->is_uid_allowed($uid, $flag_value)) {
            return ['success' => false, 'error' => 'Feature not enabled for this user.'];
        }

        $user = $this->db->get_where('user', ['uid' => $uid])->row_array();
        $name = $user ? ($user['name'] ?? "UID $uid") : "UID $uid";

        $subject = "BD on Leave - $name - $date";
        $body    = "This is an automated notification.\n\n";
        $body   .= "User: $name (UID $uid)\n";
        $body   .= "Date: $date\n";
        $body   .= "Leave Type: {$leave['leave_type']}\n";
        $body   .= "Leave Status: {$leave['status']}\n";
        $body   .= "\nNo day ceremony will be required for this user on this date.";

        $queued = $this->_queue_email(self::HR_EMAIL, self::FROM_EMAIL, $subject, $body, 'leave_hr_email', $uid);

        // Audit against a synthetic ceremony_id of 0 for leave-only events.
        $this->_audit(0, $uid, 'hr_email', [
            'leave_type'   => $leave['leave_type'],
            'leave_date'   => $date,
            'queued_email' => $queued,
        ]);

        return ['success' => true, 'queued' => $queued];
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _audit($ceremony_id, $uid, $action, $payload)
    // Inserts into day_ceremony_audit.
    // -------------------------------------------------------------------------
    private function _audit($ceremony_id, $uid, $action, $payload = [])
    {
        $this->db->insert('day_ceremony_audit', [
            'ceremony_id'  => (int) $ceremony_id,
            'uid'          => (int) $uid,
            'action'       => $action,
            'action_at'    => date('Y-m-d H:i:s'),
            'payload_json' => json_encode($payload),
        ]);
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _queue_email($to, $from, $subject, $body, $ref_type, $ref_id)
    // Queues email via stem_outbound_email table.
    // Falls back to file log if table does not exist.
    // -------------------------------------------------------------------------
    private function _queue_email($to, $from, $subject, $body, $ref_type = '', $ref_id = 0)
    {
        // Check if the queue table exists before inserting.
        $table_exists = $this->db->table_exists('stem_outbound_email');

        if ($table_exists) {
            $this->db->insert('stem_outbound_email', [
                'to_email'   => $to,
                'from_email' => $from,
                'subject'    => $subject,
                'body'       => $body,
                'ref_type'   => $ref_type,
                'ref_id'     => (int) $ref_id,
                'status'     => 'queued',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            return true;
        }

        // Fallback: write to log file.
        $this->_log_error("[DAY_CEREMONY EMAIL] to=$to subject=$subject\n$body");
        return false;
    }

    // -------------------------------------------------------------------------
    // PRIVATE: _log_error($message)
    // Writes error/fallback messages to a log file.
    // -------------------------------------------------------------------------
    private function _log_error($message)
    {
        $log_path = APPPATH . 'logs/day_ceremony_' . date('Y-m-d') . '.log';
        $entry    = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        @file_put_contents($log_path, $entry, FILE_APPEND | LOCK_EX);
    }
}
