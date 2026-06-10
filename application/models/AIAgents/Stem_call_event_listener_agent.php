<?php
/**
 * STEM CRM - Migration 027 - Call Event Listener Agent
 *
 * Hooks the tblcallevents insert path. Detects two patterns and routes them
 * to Comm_orchestrator_agent:
 *
 *   call_unanswered: actiontype_id=1 (call) AND (call_status='not_picked' OR
 *                    duration < 30 seconds)
 *   call_dropped   : actiontype_id=1 (call) AND duration BETWEEN 30 AND 120
 *                    AND no mom_data row exists for this call within 1 hour
 *                    (checked by a 1-hour delayed re-evaluation)
 *
 * The hook is fired by Migration 027 patch in tblcallevents controller insert
 * path (see stem_call_event_hook_patch.php).
 *
 * For 'dropped' detection, the agent inserts a pending evaluation that is
 * re-checked after 60 minutes. If a MoM has been logged in the interim, the
 * pending dropped event is discarded (BD did finish the call properly).
 *
 * Plain English. No em-dashes. No non-ASCII.
 *
 * Author: STEM Learning ops
 * Date: 17 May 2026
 */

class Call_event_listener_agent extends CI_Model {

    // Tuning constants
    const UNANSWERED_DURATION_THRESHOLD = 30;   // seconds
    const DROPPED_DURATION_MIN          = 30;
    const DROPPED_DURATION_MAX          = 120;
    const DROPPED_MOM_CHECK_DELAY_SEC   = 3600; // 1 hour

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Comm_orchestrator_agent');
    }

    // ========================================================================
    // ENTRY POINT - called from tblcallevents insert patch
    // ========================================================================

    /**
     * on_insert - run synchronously after a tblcallevents row is inserted.
     * Decides whether the call qualifies as unanswered or schedules a dropped
     * evaluation.
     *
     * @param int $callevent_id new tblcallevents row id
     * @return string|false 'unanswered', 'dropped_pending', or false if no
     *                       action taken
     */
    public function on_insert($callevent_id) {
        $row = $this->db->get_where('tblcallevents', array('id' => $callevent_id))->row_array();
        if (empty($row)) {
            log_message('error', "[m027 listener] callevent $callevent_id not found");
            return false;
        }

        // Only actiontype 1 (call) is in scope
        if ((int) $row['actiontype_id'] !== 1) {
            return false;
        }

        $cid_id = (int) $row['cid_id'];
        $bd_uid = (int) $row['user_id'];
        $duration = isset($row['duration']) ? (int) $row['duration'] : 0;
        $call_status = isset($row['call_status']) ? trim($row['call_status']) : '';

        // ---- Unanswered branch ----
        if ($call_status === 'not_picked' || $duration < self::UNANSWERED_DURATION_THRESHOLD) {
            return $this->emit_unanswered($callevent_id, $cid_id, $bd_uid, $row);
        }

        // ---- Dropped branch (deferred) ----
        if ($duration >= self::DROPPED_DURATION_MIN && $duration <= self::DROPPED_DURATION_MAX) {
            return $this->schedule_dropped_check($callevent_id, $cid_id, $bd_uid, $row);
        }

        // Normal call, no event needed
        return false;
    }

    /**
     * evaluate_pending_dropped - called by cron every 5 min. Looks at
     * call_event_pending rows older than 1 hour and either emits the dropped
     * event or discards it if a MoM was logged.
     */
    public function evaluate_pending_dropped() {
        $cutoff = date('Y-m-d H:i:s', time() - self::DROPPED_MOM_CHECK_DELAY_SEC);

        $pending = $this->db->select('*')
            ->from('call_event_pending')
            ->where('status', 'awaiting_mom_check')
            ->where('created_at <=', $cutoff)
            ->order_by('id', 'ASC')
            ->limit(100)
            ->get()->result_array();

        $emitted = 0;
        $discarded = 0;

        foreach ($pending as $p) {
            $has_mom = $this->mom_exists_for_call($p['callevent_id']);

            if ($has_mom) {
                $this->db->where('id', $p['id'])
                    ->update('call_event_pending', array(
                        'status' => 'discarded_mom_exists',
                        'resolved_at' => date('Y-m-d H:i:s'),
                    ));
                $discarded++;
                log_message('debug', "[m027 listener] dropped event for callevent {$p['callevent_id']} discarded, MoM exists");
                continue;
            }

            // Emit dropped event
            $row = json_decode($p['row_snapshot_json'], true);
            $event_log_id = $this->Comm_orchestrator_agent->ingest_event(
                'call_dropped',
                (int) $p['cid_id'],
                (int) $p['bd_uid'],
                (int) $p['callevent_id'],
                $this->build_dropped_payload($row)
            );

            $this->db->where('id', $p['id'])
                ->update('call_event_pending', array(
                    'status' => $event_log_id ? 'emitted' : 'rejected_by_orchestrator',
                    'event_log_id' => $event_log_id ? $event_log_id : null,
                    'resolved_at' => date('Y-m-d H:i:s'),
                ));

            $emitted++;
        }

        return array('emitted' => $emitted, 'discarded' => $discarded);
    }

    // ========================================================================
    // INTERNALS
    // ========================================================================

    private function emit_unanswered($callevent_id, $cid_id, $bd_uid, $row) {
        $payload = $this->build_unanswered_payload($row);

        $event_log_id = $this->Comm_orchestrator_agent->ingest_event(
            'call_unanswered',
            $cid_id,
            $bd_uid,
            $callevent_id,
            $payload
        );

        if ($event_log_id) {
            log_message('info', "[m027 listener] emitted call_unanswered event=$event_log_id callevent=$callevent_id");
            return 'unanswered';
        }
        return false;
    }

    private function schedule_dropped_check($callevent_id, $cid_id, $bd_uid, $row) {
        $insert = array(
            'callevent_id'      => $callevent_id,
            'cid_id'            => $cid_id,
            'bd_uid'            => $bd_uid,
            'row_snapshot_json' => json_encode($row),
            'status'            => 'awaiting_mom_check',
            'created_at'        => date('Y-m-d H:i:s'),
        );
        $this->db->insert('call_event_pending', $insert);
        log_message('debug', "[m027 listener] scheduled dropped check for callevent $callevent_id");
        return 'dropped_pending';
    }

    private function mom_exists_for_call($callevent_id) {
        // Tightly correlated: mom_data.callevent_id OR a meeting in tblcallevents
        // within 1h of the call on same cid by same BD with actiontype 3 or 4.
        // First check direct linkage
        $direct = $this->db->select('id')->from('mom_data')
            ->where('callevent_id', $callevent_id)
            ->limit(1)->get()->row();
        if (!empty($direct)) return true;

        // Indirect: any mom_data for same cid by same bd created after the call within 1h
        $callrow = $this->db->get_where('tblcallevents', array('id' => $callevent_id))->row_array();
        if (empty($callrow)) return false;

        $window_start = $callrow['createDate'];
        $window_end = date('Y-m-d H:i:s', strtotime($window_start) + 3600);

        $indirect = $this->db->select('id')->from('mom_data')
            ->where('cid_id', $callrow['cid_id'])
            ->where('user_id', $callrow['user_id'])
            ->where('createDate >=', $window_start)
            ->where('createDate <=', $window_end)
            ->limit(1)->get()->row();

        return !empty($indirect);
    }

    private function build_unanswered_payload($row) {
        $purpose_label = $this->lookup_purpose_label($row['purpose_id']);
        return array(
            'callevent_id'       => (int) $row['id'],
            'duration_seconds'   => isset($row['duration']) ? (int) $row['duration'] : 0,
            'call_status'        => isset($row['call_status']) ? $row['call_status'] : null,
            'purpose_id'         => (int) $row['purpose_id'],
            'purpose_label'      => $purpose_label,
            'call_time_iso'      => $row['createDate'],
            'call_topic_one_line'=> $this->infer_topic_from_purpose($purpose_label),
        );
    }

    private function build_dropped_payload($row) {
        $purpose_label = $this->lookup_purpose_label($row['purpose_id']);
        return array(
            'callevent_id'       => (int) $row['id'],
            'duration_seconds'   => isset($row['duration']) ? (int) $row['duration'] : 0,
            'purpose_id'         => (int) $row['purpose_id'],
            'purpose_label'      => $purpose_label,
            'call_time_iso'      => $row['createDate'],
            'call_topic_one_line'=> $this->infer_topic_from_purpose($purpose_label),
            'detected_as'        => 'dropped',
        );
    }

    private function lookup_purpose_label($purpose_id) {
        $row = $this->db->select('purpose_name')
            ->from('purpose_master')
            ->where('id', $purpose_id)
            ->limit(1)->get()->row();
        return !empty($row) ? $row->purpose_name : 'general discussion';
    }

    private function infer_topic_from_purpose($purpose_label) {
        $map = array(
            'tentative_followup'    => 'the STEM Learning programme fit for your school',
            'positive_followup'     => 'the proposal status and next steps',
            'open_rpem_meeting'     => 'scheduling the principal meeting',
            'mom_followup'          => 'the action items from our last meeting',
            'proposal_discussion'   => 'the proposal and any questions you have',
        );
        $key = strtolower(trim($purpose_label));
        return isset($map[$key]) ? $map[$key] : ('our conversation on ' . $purpose_label);
    }
}
