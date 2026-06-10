<?php
/**
 * STEM CRM - Migration 027 - Call Event Hook Patch
 *
 * Drop-in patch that wires existing controllers into the migration 027
 * orchestrator. Five injection points covered:
 *
 *   1. tblcallevents.insert       -> Call_event_listener_agent::on_insert()
 *      Detects call_unanswered and call_dropped events.
 *
 *   2. mom_data.approved_status='1' -> ingest_event('meeting_completed')
 *      Fires when CM approves a MoM. The meeting_id (mom_id) is the source.
 *
 *   3. proposal_sla_tracker transition to 'closed' (migration 026)
 *      -> ingest_event('proposal_sent')
 *
 *   4. lead_query_checklist new row / resolved
 *      -> ingest_event('query_raised') or 'query_resolved'
 *
 *   5. lead_progression_log cstatus jump
 *      -> ingest_event('stage_progressed') when jump matches:
 *         3->6, 6->8, 8->9, 9->12
 *
 * Each patch block is shown with INSERT POINT comments so deploy team can
 * apply it precisely to the existing controllers.
 *
 * Plain English. No em-dashes. No non-ASCII.
 *
 * Author: STEM Learning ops
 * Date: 17 May 2026
 */

// ============================================================================
// PATCH BLOCK 1: tblcallevents controller insert path
// ============================================================================
// File: application/controllers/CallEventApi.php
// Method: insert_callevent($data) - AFTER $this->db->insert('tblcallevents', $data)
// INSERT POINT: immediately after $callevent_id = $this->db->insert_id();

/* ========== PATCH START tblcallevents ========== */
/*
public function insert_callevent($data) {
    // ... existing code that builds $data array ...

    $this->db->insert('tblcallevents', $data);
    $callevent_id = $this->db->insert_id();

    // ----- BEGIN migration 027 hook -----
    if ($this->m027_enabled()) {
        $this->load->model('Call_event_listener_agent');
        try {
            $this->Call_event_listener_agent->on_insert($callevent_id);
        } catch (Exception $e) {
            // Never fail the original insert because of comm agent
            log_message('error', '[m027 hook] tblcallevents insert hook failed: ' . $e->getMessage());
        }
    }
    // ----- END migration 027 hook -----

    // ... existing response code ...
    return $callevent_id;
}

private function m027_enabled() {
    static $cache = null;
    if ($cache !== null) return $cache;
    $row = $this->db->get_where('app_config', array('config_key' => 'm027_enabled'))->row();
    $cache = !empty($row) && $row->config_value === '1';
    return $cache;
}
*/
/* ========== PATCH END tblcallevents ========== */


// ============================================================================
// PATCH BLOCK 2: mom_data approval handler
// ============================================================================
// File: application/controllers/MomApi.php (or MomV2Api.php after migration 021)
// Method: approve_mom() AFTER $this->db->update('mom_data', $update)
// where approved_status changed to '1'

/* ========== PATCH START mom_approval ========== */
/*
public function approve_mom() {
    // ... existing CM approval logic that sets approved_status = '1' ...

    $mom_id = (int) $this->input->post('mom_id');
    $this->db->where('id', $mom_id)->update('mom_data', array(
        'approved_status' => '1',
        'approved_by' => $cm_uid,
        'approved_at' => date('Y-m-d H:i:s'),
    ));

    // ----- BEGIN migration 027 hook -----
    if ($this->m027_enabled()) {
        $mom = $this->db->get_where('mom_data', array('id' => $mom_id))->row_array();
        if (!empty($mom)) {
            $this->load->model('Comm_orchestrator_agent');
            try {
                $this->Comm_orchestrator_agent->ingest_event(
                    'meeting_completed',
                    (int) $mom['cid_id'],
                    (int) $mom['user_id'],   // BD who logged the meeting
                    $mom_id,
                    array(
                        'mom_id' => $mom_id,
                        'meeting_type' => 'mom_approved',
                        'action_items_text' => isset($mom['action_items_text']) ? $mom['action_items_text'] : '',
                        'approved_by_cm' => $cm_uid,
                    )
                );
            } catch (Exception $e) {
                log_message('error', '[m027 hook] mom_approval hook failed: ' . $e->getMessage());
            }
        }
    }
    // ----- END migration 027 hook -----

    // ... existing response ...
}
*/
/* ========== PATCH END mom_approval ========== */


// ============================================================================
// PATCH BLOCK 3: proposal_sla_tracker close transition
// ============================================================================
// File: application/controllers/ProposalSlaApi.php (migration 026)
// Method: mark_proposal_sent() AFTER status transitions to 'closed'

/* ========== PATCH START proposal_sent ========== */
/*
public function mark_proposal_sent() {
    $proposal_id = (int) $this->input->post('proposal_id');

    // ... existing transition logic that sets status='closed' ...

    $this->db->where('id', $proposal_id)->update('proposal_sla_tracker', array(
        'status' => 'closed',
        'closed_at' => date('Y-m-d H:i:s'),
    ));

    // ----- BEGIN migration 027 hook -----
    if ($this->m027_enabled()) {
        $proposal = $this->db->get_where('proposal_sla_tracker', array('id' => $proposal_id))->row_array();
        if (!empty($proposal)) {
            $this->load->model('Comm_orchestrator_agent');
            $hours_since_send = isset($proposal['proposal_sent_at'])
                ? (int) ((time() - strtotime($proposal['proposal_sent_at'])) / 3600)
                : 0;
            try {
                $this->Comm_orchestrator_agent->ingest_event(
                    'proposal_sent',
                    (int) $proposal['cid_id'],
                    (int) $proposal['bd_uid'],
                    $proposal_id,
                    array(
                        'proposal_id' => $proposal_id,
                        'proposal_amount_rs' => $proposal['proposal_amount_rs'],
                        'programme_scope'    => $proposal['programme_scope'],
                        'programme_timeline' => $proposal['programme_timeline'],
                        'proposal_sent_date' => $proposal['proposal_sent_at'],
                        'hours_since_send'   => $hours_since_send,
                        'attachment_path'    => $proposal['proposal_pdf_path'],
                    )
                );
            } catch (Exception $e) {
                log_message('error', '[m027 hook] proposal_sent hook failed: ' . $e->getMessage());
            }
        }
    }
    // ----- END migration 027 hook -----
}
*/
/* ========== PATCH END proposal_sent ========== */


// ============================================================================
// PATCH BLOCK 4: lead_query_checklist hooks
// ============================================================================
// File: application/controllers/LeadQueryApi.php (migration 026)
// Methods: raise_query() AFTER insert, resolve_query() AFTER update

/* ========== PATCH START query_raised ========== */
/*
public function raise_query() {
    // ... existing logic that inserts into lead_query_checklist ...
    $this->db->insert('lead_query_checklist', $row);
    $query_id = $this->db->insert_id();

    // ----- BEGIN migration 027 hook -----
    if ($this->m027_enabled()) {
        $this->load->model('Comm_orchestrator_agent');
        try {
            $this->Comm_orchestrator_agent->ingest_event(
                'query_raised',
                (int) $row['cid_id'],
                (int) $row['bd_uid'],
                $query_id,
                array(
                    'query_id'   => $query_id,
                    'query_text' => $row['query_text'],
                    'expected_resolution_date' => date('Y-m-d', strtotime('+2 days')),
                )
            );
        } catch (Exception $e) {
            log_message('error', '[m027 hook] query_raised hook failed: ' . $e->getMessage());
        }
    }
    // ----- END migration 027 hook -----
}
*/
/* ========== PATCH END query_raised ========== */

/* ========== PATCH START query_resolved ========== */
/*
public function resolve_query() {
    $query_id = (int) $this->input->post('query_id');
    $resolution_summary = $this->input->post('resolution_summary');

    $this->db->where('id', $query_id)->update('lead_query_checklist', array(
        'status' => 'resolved',
        'resolution_summary' => $resolution_summary,
        'resolved_at' => date('Y-m-d H:i:s'),
    ));

    // ----- BEGIN migration 027 hook -----
    if ($this->m027_enabled()) {
        $q = $this->db->get_where('lead_query_checklist', array('id' => $query_id))->row_array();
        if (!empty($q)) {
            $this->load->model('Comm_orchestrator_agent');
            try {
                $this->Comm_orchestrator_agent->ingest_event(
                    'query_resolved',
                    (int) $q['cid_id'],
                    (int) $q['bd_uid'],
                    $query_id,
                    array(
                        'query_id'   => $query_id,
                        'query_text' => $q['query_text'],
                        'resolution_summary' => $resolution_summary,
                    )
                );
            } catch (Exception $e) {
                log_message('error', '[m027 hook] query_resolved hook failed: ' . $e->getMessage());
            }
        }
    }
    // ----- END migration 027 hook -----
}
*/
/* ========== PATCH END query_resolved ========== */


// ============================================================================
// PATCH BLOCK 5: lead_progression_log stage jumps
// ============================================================================
// File: application/controllers/ProgressionApi.php (migration 012)
// Hook: after_insert_progression_log() - already exists as observer

/* ========== PATCH START stage_progressed ========== */
/*
public function after_insert_progression_log($log_row) {
    // ... existing 012/012.1/012.2 logic ...

    // ----- BEGIN migration 027 hook -----
    if ($this->m027_enabled()) {
        $from = (int) $log_row['from_cstatus'];
        $to   = (int) $log_row['to_cstatus'];

        // Only celebrate big jumps that warrant a client-facing comm
        $celebrate = array(
            '3=>6'  => true,  // Tentative to Positive
            '6=>8'  => true,  // Positive to Open RPEM
            '8=>9'  => true,  // Open RPEM to Very Positive
            '9=>12' => true,  // Very Positive to Won (the big one)
        );
        $key = $from . '=>' . $to;

        if (isset($celebrate[$key])) {
            $this->load->model('Comm_orchestrator_agent');
            try {
                $this->Comm_orchestrator_agent->ingest_event(
                    'stage_progressed',
                    (int) $log_row['cid_id'],
                    (int) $log_row['bd_uid'],
                    (int) $log_row['id'],
                    array(
                        'progression_log_id' => (int) $log_row['id'],
                        'from_cstatus' => $from,
                        'to_cstatus'   => $to,
                        'transition_key' => $key,
                        'closed_value_rs' => isset($log_row['closed_value_rs']) ? $log_row['closed_value_rs'] : null,
                    )
                );
            } catch (Exception $e) {
                log_message('error', '[m027 hook] stage_progressed hook failed: ' . $e->getMessage());
            }
        }
    }
    // ----- END migration 027 hook -----
}
*/
/* ========== PATCH END stage_progressed ========== */


// ============================================================================
// PATCH BLOCK 6: Dormant re-engagement scanner (cron-driven, not hook)
// ============================================================================
// New file: application/models/AIAgents/Dormant_scanner_agent.php
// Runs daily at 06:00 IST via dedicated cron (added in cron amendment 027).

/* ========== PATCH START dormant_scanner ========== */
/*
class Dormant_scanner_agent extends CI_Model {

    public function scan_and_emit() {
        if (!$this->m027_enabled()) return array('skipped' => 'disabled');

        // Find leads at cstatus 6, 7, 8, 9 with no touch in 14 days
        $cutoff_14d = date('Y-m-d H:i:s', strtotime('-14 days'));
        $cutoff_30d = date('Y-m-d H:i:s', strtotime('-30 days'));

        $candidates = $this->db->query("
            SELECT
                ic.id AS cid_id,
                ic.mainbd AS bd_uid,
                ic.cstatus,
                MAX(tc.createDate) AS last_touch
            FROM init_call ic
            LEFT JOIN tblcallevents tc ON tc.cid_id = ic.id
            WHERE ic.cstatus IN (6, 7, 8, 9)
              AND ic.cstatus NOT IN (12,13,14)
            GROUP BY ic.id
            HAVING last_touch < ? OR last_touch IS NULL
        ", array($cutoff_14d))->result_array();

        $this->load->model('Comm_orchestrator_agent');
        $emitted = 0;

        foreach ($candidates as $c) {
            $last_touch = $c['last_touch'];
            $days_dormant = !empty($last_touch)
                ? floor((time() - strtotime($last_touch)) / 86400)
                : 30;

            // Skip if very recent dormant_re_engage emission for same lead
            $recent = $this->db->where('cid_id', $c['cid_id'])
                ->where('event_type', 'dormant_re_engage')
                ->where('created_at >', $cutoff_14d)
                ->count_all_results('comm_event_log');
            if ($recent > 0) continue;

            $event_log_id = $this->Comm_orchestrator_agent->ingest_event(
                'dormant_re_engage',
                (int) $c['cid_id'],
                (int) $c['bd_uid'],
                0, // no source_id, scanner-emitted
                array(
                    'days_dormant' => $days_dormant,
                    'last_touch_iso' => $last_touch,
                    'cstatus' => (int) $c['cstatus'],
                )
            );
            if ($event_log_id) $emitted++;
        }

        return array('candidates' => count($candidates), 'emitted' => $emitted);
    }

    private function m027_enabled() {
        $row = $this->db->get_where('app_config', array('config_key' => 'm027_enabled'))->row();
        return !empty($row) && $row->config_value === '1';
    }
}
*/
/* ========== PATCH END dormant_scanner ========== */


// ============================================================================
// DEPLOYMENT NOTES
// ============================================================================
//
// 1) All 6 hooks are wrapped in m027_enabled() check. Set
//    app_config.m027_enabled='0' to disable agent entirely without code change.
//
// 2) Each hook wraps the orchestrator call in try/catch. If the orchestrator
//    throws, the original controller flow continues. This is intentional - the
//    comm agent must never break a tblcallevents insert or MoM approval.
//
// 3) Phase gating happens inside Comm_orchestrator_agent::ingest_event() via
//    is_event_in_phase() and is_bd_in_pilot(). Hooks always fire, orchestrator
//    decides whether to act.
//
// 4) The dormant scanner is a separate cron, not a hook. Spec it in the
//    cron amendment (file 16).
//
// 5) Smoke test after deploy:
//      - Insert a tblcallevents row with duration=10 -> check comm_event_log
//        has a new call_unanswered row
//      - Approve a MoM -> check comm_event_log has meeting_completed
//      - Insert a lead_progression_log with from=9, to=12 -> check
//        comm_event_log has stage_progressed
//
// END migration 027 hook patch
