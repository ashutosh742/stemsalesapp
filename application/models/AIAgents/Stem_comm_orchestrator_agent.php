<?php
/**
 * STEM CRM - Migration 027 - Comm Orchestrator Agent
 *
 * The brain of the 360-degree communication agent.
 *
 * Responsibilities:
 *   1. ingest_event()           - record an event from any source (call hook,
 *                                  mom approval, proposal SLA, stage transition,
 *                                  dormant scanner) into comm_event_log
 *   2. process_pending_events() - main loop, runs every 5 min via cron failover
 *                                  + invoked inline via shutdown hook for near
 *                                  real time
 *   3. check_dedup()            - per event_type window check
 *   4. check_frequency_cap()    - 1/day, 4/week per stakeholder_email + cid_id
 *   5. resolve_recipients()     - look up stakeholder_contact_book for role
 *                                  named in template
 *   6. trigger_drafter()        - hand off to Comm_drafter_agent which calls
 *                                  GPT-4o-mini and writes to comm_draft_queue
 *
 * Phasing gates via config m027_phase:
 *   1 = call_unanswered, call_dropped, meeting_completed
 *   2 = + proposal_sent, query_raised, query_resolved
 *   3 = + stage_progressed, dormant_re_engage
 *
 * Pilot uids gate (m027_pilot_uids) until org rollout 1 Nov 2026.
 *
 * Plain English. No em-dashes. No non-ASCII. Rs for rupees.
 *
 * Author: STEM Learning ops
 * Date: 17 May 2026
 * Production phase 1 target: Mon 1 Aug 2026
 */

class Comm_orchestrator_agent extends CI_Model {

    // ----- Phase gating -----
    private $phase_events = array(
        1 => array('call_unanswered', 'call_dropped', 'meeting_completed'),
        2 => array('call_unanswered', 'call_dropped', 'meeting_completed',
                   'proposal_sent', 'query_raised', 'query_resolved'),
        3 => array('call_unanswered', 'call_dropped', 'meeting_completed',
                   'proposal_sent', 'query_raised', 'query_resolved',
                   'stage_progressed', 'dormant_re_engage'),
    );

    // ----- Dedup windows in seconds -----
    private $dedup_windows = array(
        'call_unanswered'  => 86400,    // 24 hours
        'call_dropped'     => 14400,    // 4 hours
        'meeting_completed'=> 604800,   // 7 days (per meeting_id)
        'proposal_sent'    => 0,        // once per proposal_id, special handling
        'query_raised'     => 0,        // once per query_id
        'query_resolved'   => 0,        // once per query_id
        'stage_progressed' => 0,        // once per progression_log_id
        'dormant_re_engage'=> 1209600,  // 14 days re-trigger window, also 30d
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Comm_drafter_agent');
        $this->load->model('Stakeholder_contact_book_agent');
    }

    // ========================================================================
    // PUBLIC ENTRY POINTS
    // ========================================================================

    /**
     * ingest_event - called by Call_event_listener_agent, MoM hook, proposal
     * SLA tracker, stage progression hook, dormant scanner.
     *
     * @param string $event_type   one of the 8 event types
     * @param int    $cid_id       init_call id (lead)
     * @param int    $bd_uid       BD user id
     * @param int    $source_id    foreign id (callevent id, mom_id, etc)
     * @param array  $payload      event-specific context (purpose, duration, etc)
     * @return int|false event_log_id on success, false if rejected
     */
    public function ingest_event($event_type, $cid_id, $bd_uid, $source_id, $payload = array()) {

        if (!$this->is_enabled()) {
            log_message('debug', "[m027] orchestrator disabled, skipping event $event_type for cid $cid_id");
            return false;
        }

        if (!$this->is_event_in_phase($event_type)) {
            log_message('debug', "[m027] event $event_type not in current phase, skipping");
            return false;
        }

        if (!$this->is_bd_in_pilot($bd_uid)) {
            log_message('debug', "[m027] BD $bd_uid not in pilot uids, skipping");
            return false;
        }

        // Insert into comm_event_log with status='new'
        $row = array(
            'event_type'    => $event_type,
            'cid_id'        => $cid_id,
            'bd_uid'        => $bd_uid,
            'source_id'     => $source_id,
            'payload_json'  => json_encode($payload),
            'status'        => 'new',
            'created_at'    => date('Y-m-d H:i:s'),
        );

        $this->db->insert('comm_event_log', $row);
        $event_log_id = $this->db->insert_id();

        log_message('info', "[m027] ingested event $event_type id=$event_log_id cid=$cid_id bd=$bd_uid");

        // Try to process immediately via shutdown hook for low latency
        // If this fails (e.g. drafter timeout), the 5-min cron sweep picks it up.
        register_shutdown_function(array($this, 'process_one_event'), $event_log_id);

        return $event_log_id;
    }

    /**
     * process_pending_events - main loop, called by cron every 5 min.
     * Picks up any 'new' rows in comm_event_log and processes them.
     */
    public function process_pending_events($limit = 50) {
        if (!$this->is_enabled()) {
            return array('processed' => 0, 'skipped' => 'disabled');
        }

        $events = $this->db->select('*')
            ->from('comm_event_log')
            ->where('status', 'new')
            ->order_by('created_at', 'ASC')
            ->limit($limit)
            ->get()->result_array();

        $processed = 0;
        $drafted = 0;
        $capped = 0;
        $deduped = 0;
        $errored = 0;

        foreach ($events as $ev) {
            $result = $this->process_one_event($ev['id'], $ev);
            $processed++;
            if ($result === 'drafted')      $drafted++;
            elseif ($result === 'capped')   $capped++;
            elseif ($result === 'deduped')  $deduped++;
            elseif ($result === 'errored')  $errored++;
        }

        return array(
            'processed' => $processed,
            'drafted'   => $drafted,
            'capped'    => $capped,
            'deduped'   => $deduped,
            'errored'   => $errored,
        );
    }

    /**
     * process_one_event - handle a single event_log row.
     *
     * @param int   $event_log_id
     * @param array $event_row optional pre-fetched row to save a query
     * @return string one of: drafted, capped, deduped, errored, discarded
     */
    public function process_one_event($event_log_id, $event_row = null) {

        if ($event_row === null) {
            $event_row = $this->db->get_where('comm_event_log', array('id' => $event_log_id))->row_array();
            if (empty($event_row) || $event_row['status'] !== 'new') {
                return 'discarded';
            }
        }

        // 1) Dedup check
        if ($this->check_dedup($event_row)) {
            $this->update_event_status($event_log_id, 'deduped', 'duplicate within window');
            log_message('info', "[m027] event $event_log_id deduped");
            return 'deduped';
        }

        // 2) Find the right template
        $template = $this->select_template($event_row);
        if (empty($template)) {
            $this->update_event_status($event_log_id, 'errored', 'no template matched');
            log_message('error', "[m027] event $event_log_id no template matched for {$event_row['event_type']}");
            return 'errored';
        }

        // 3) Resolve recipients
        $recipients = $this->resolve_recipients($event_row['cid_id'], $template);
        if (empty($recipients['to'])) {
            $this->update_event_status($event_log_id, 'errored', 'no recipient resolved');
            log_message('error', "[m027] event $event_log_id no primary recipient");
            return 'errored';
        }

        // 4) Frequency cap on each recipient
        if ($this->check_frequency_cap($recipients['to'], $event_row['cid_id'])) {
            $this->update_event_status($event_log_id, 'capped', 'frequency cap hit');
            log_message('info', "[m027] event $event_log_id capped, recipient {$recipients['to']} at limit");
            return 'capped';
        }

        // 5) Hand off to drafter
        try {
            $draft_id = $this->Comm_drafter_agent->draft_email(
                $event_log_id,
                $template,
                $recipients,
                json_decode($event_row['payload_json'], true)
            );

            if ($draft_id) {
                $this->update_event_status($event_log_id, 'drafted', "draft_id=$draft_id");
                $this->increment_frequency_counter($recipients['to'], $event_row['cid_id']);
                log_message('info', "[m027] event $event_log_id drafted as $draft_id");
                return 'drafted';
            } else {
                $this->update_event_status($event_log_id, 'errored', 'drafter returned null');
                return 'errored';
            }
        } catch (Exception $e) {
            $this->update_event_status($event_log_id, 'errored', 'drafter exception: ' . $e->getMessage());
            log_message('error', "[m027] event $event_log_id drafter threw: " . $e->getMessage());
            return 'errored';
        }
    }

    // ========================================================================
    // INTERNALS
    // ========================================================================

    private function is_enabled() {
        $row = $this->db->get_where('app_config', array('config_key' => 'm027_enabled'))->row();
        return !empty($row) && $row->config_value === '1';
    }

    private function is_event_in_phase($event_type) {
        $row = $this->db->get_where('app_config', array('config_key' => 'm027_phase'))->row();
        $phase = !empty($row) ? (int) $row->config_value : 0;
        if ($phase < 1 || $phase > 3) return false;
        return in_array($event_type, $this->phase_events[$phase]);
    }

    private function is_bd_in_pilot($bd_uid) {
        $row = $this->db->get_where('app_config', array('config_key' => 'm027_pilot_uids'))->row();
        if (empty($row) || empty($row->config_value)) return false;
        if ($row->config_value === 'ALL') return true;
        $pilot_uids = explode(',', $row->config_value);
        return in_array((string) $bd_uid, $pilot_uids);
    }

    /**
     * check_dedup - returns true if a duplicate exists within the window.
     */
    private function check_dedup($event_row) {
        $event_type = $event_row['event_type'];

        // Special cases keyed on source_id (once-per-source)
        if (in_array($event_type, array('proposal_sent', 'query_raised', 'query_resolved', 'stage_progressed'))) {
            $exists = $this->db->select('id')
                ->from('comm_event_log')
                ->where('event_type', $event_type)
                ->where('source_id', $event_row['source_id'])
                ->where('id !=', $event_row['id'])
                ->where_in('status', array('drafted', 'sent'))
                ->limit(1)
                ->get()->row();
            return !empty($exists);
        }

        // Window-based dedup
        $window_seconds = isset($this->dedup_windows[$event_type]) ? $this->dedup_windows[$event_type] : 0;
        if ($window_seconds <= 0) return false;

        $cutoff = date('Y-m-d H:i:s', strtotime("-{$window_seconds} seconds"));

        $exists = $this->db->select('id')
            ->from('comm_event_log')
            ->where('event_type', $event_type)
            ->where('cid_id', $event_row['cid_id'])
            ->where('created_at >=', $cutoff)
            ->where('id !=', $event_row['id'])
            ->where_in('status', array('drafted', 'sent'))
            ->limit(1)
            ->get()->row();

        return !empty($exists);
    }

    /**
     * select_template - pick the best comm_template_v2 row for this event +
     * cstatus combo. Inherited rows defer to email_template body.
     */
    private function select_template($event_row) {
        $cstatus = $this->get_lead_cstatus($event_row['cid_id']);

        // Special routing for dormant_re_engage 14d vs 30d
        if ($event_row['event_type'] === 'dormant_re_engage') {
            $payload = json_decode($event_row['payload_json'], true);
            $days_dormant = isset($payload['days_dormant']) ? (int) $payload['days_dormant'] : 14;
            $template_code = $days_dormant >= 30 ? 'dormant_re_engage_30d' : 'dormant_re_engage_14d';
            return $this->db->get_where('comm_template_v2',
                array('template_code' => $template_code, 'active' => 1))->row_array();
        }

        // Special routing for proposal_sent: cover note first time, nudge if SLA breached
        if ($event_row['event_type'] === 'proposal_sent') {
            $payload = json_decode($event_row['payload_json'], true);
            $hours_since_send = isset($payload['hours_since_send']) ? (int) $payload['hours_since_send'] : 0;
            $template_code = $hours_since_send >= 72 ? 'proposal_nudge_72h' : 'proposal_send_cover';
            return $this->db->get_where('comm_template_v2',
                array('template_code' => $template_code, 'active' => 1))->row_array();
        }

        // Default: first active template for this event_type whose cstatus band matches
        $tpl = $this->db->select('*')
            ->from('comm_template_v2')
            ->where('event_type', $event_row['event_type'])
            ->where('active', 1)
            ->where('applicable_cstatus_min <=', $cstatus)
            ->where('applicable_cstatus_max >=', $cstatus)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()->row_array();

        return $tpl;
    }

    /**
     * resolve_recipients - look up stakeholder_contact_book for roles named
     * in template recipient_to_role and recipient_cc_roles.
     */
    private function resolve_recipients($cid_id, $template) {
        $to = null;
        $cc = array();

        // Primary recipient
        $to_role = $template['recipient_to_role'];
        $to_row = $this->db->get_where('stakeholder_contact_book', array(
            'cid_id' => $cid_id,
            'role'   => $to_role,
            'active' => 1,
            'bounce_flag' => 0,
        ))->row_array();

        if (!empty($to_row) && !empty($to_row['email'])) {
            $to = $to_row['email'];
            $to_name = $to_row['name'];
            $to_first_name = $this->first_name($to_row['name']);
        }

        // CC recipients
        if (!empty($template['recipient_cc_roles'])) {
            $cc_roles = json_decode($template['recipient_cc_roles'], true);
            if (is_array($cc_roles)) {
                foreach ($cc_roles as $role) {
                    $cc_row = $this->db->get_where('stakeholder_contact_book', array(
                        'cid_id' => $cid_id,
                        'role'   => $role,
                        'active' => 1,
                        'bounce_flag' => 0,
                    ))->row_array();
                    if (!empty($cc_row) && !empty($cc_row['email'])) {
                        $cc[] = $cc_row['email'];
                    }
                }
            }
        }

        return array(
            'to'         => $to,
            'to_name'    => isset($to_name) ? $to_name : null,
            'to_first_name' => isset($to_first_name) ? $to_first_name : null,
            'cc'         => $cc,
        );
    }

    /**
     * check_frequency_cap - true if this stakeholder hit 1/day or 4/week
     * for this cid_id. Manual sends excluded.
     */
    private function check_frequency_cap($email, $cid_id) {
        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('-6 days'));

        // Override check
        $override = $this->db->get_where('comm_frequency_cap', array(
            'stakeholder_email' => $email,
            'cid_id'  => $cid_id,
            'override_flag' => 1,
        ))->row();
        if (!empty($override)) return false;

        // Daily count
        $daily_cap = $this->config_int('m027_frequency_cap_daily', 1);
        $daily_count = $this->db->select('COUNT(*) AS n')
            ->from('comm_send_log')
            ->where('stakeholder_email', $email)
            ->where('cid_id', $cid_id)
            ->where('manual_send_flag', 0)
            ->where('DATE(sent_at) =', $today)
            ->get()->row()->n;
        if ((int) $daily_count >= $daily_cap) return true;

        // Weekly count
        $weekly_cap = $this->config_int('m027_frequency_cap_weekly', 4);
        $weekly_count = $this->db->select('COUNT(*) AS n')
            ->from('comm_send_log')
            ->where('stakeholder_email', $email)
            ->where('cid_id', $cid_id)
            ->where('manual_send_flag', 0)
            ->where('DATE(sent_at) >=', $week_start)
            ->get()->row()->n;
        if ((int) $weekly_count >= $weekly_cap) return true;

        return false;
    }

    private function increment_frequency_counter($email, $cid_id) {
        // No-op for now; counters are computed live from comm_send_log at send time.
        // Reserve for future hot-cache table if perf becomes a concern.
        return true;
    }

    private function update_event_status($event_log_id, $status, $note = null) {
        $row = array(
            'status'       => $status,
            'status_note'  => $note,
            'processed_at' => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $event_log_id)->update('comm_event_log', $row);
    }

    private function get_lead_cstatus($cid_id) {
        $row = $this->db->select('cstatus')->from('init_call')
            ->where('id', $cid_id)->limit(1)->get()->row();
        return !empty($row) ? (int) $row->cstatus : 0;
    }

    private function config_int($key, $default) {
        $row = $this->db->get_where('app_config', array('config_key' => $key))->row();
        return !empty($row) ? (int) $row->config_value : (int) $default;
    }

    private function first_name($full_name) {
        $parts = explode(' ', trim($full_name));
        return $parts[0];
    }

    // ========================================================================
    // PROBE
    // ========================================================================

    public function probe() {
        return array(
            'migration'  => '027',
            'status'     => $this->is_enabled() ? 'enabled' : 'disabled',
            'phase'      => $this->config_int('m027_phase', 0),
            'pilot_uids' => $this->get_config_string('m027_pilot_uids'),
            'queue_size' => (int) $this->db->select('COUNT(*) AS n')
                ->from('comm_event_log')->where('status', 'new')->get()->row()->n,
        );
    }

    private function get_config_string($key) {
        $row = $this->db->get_where('app_config', array('config_key' => $key))->row();
        return !empty($row) ? $row->config_value : '';
    }
}
