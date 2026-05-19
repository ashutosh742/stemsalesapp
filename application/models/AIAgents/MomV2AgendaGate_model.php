<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomV2_Agenda_Gate_model
 *
 * Migration 037 - MoM v2 Mandatory Schema + Brian Tracy Agenda Gate
 * Date: 2026-05-19
 *
 * Responsibilities:
 *   1. Lock the meeting agenda before the BD enters a meeting (inserts into
 *      mom_v2_meeting_agenda_lock).
 *   2. Compute which questions from mom_v2_question_schema are required for
 *      this specific event based on cstatus, actiontype, and partner type.
 *   3. Expose helpers so the controller can check lock state quickly.
 *
 * PARALLEL DEMO ONLY. Does NOT read or write to the production mom_data table.
 */
class MomV2_Agenda_Gate_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Lock the agenda for an event.
     *
     * Looks up the meeting context from tblcallevents and init_call, works out
     * which questions apply, then inserts a row into mom_v2_meeting_agenda_lock.
     *
     * Returns the new lock_id on success, or false if the event is already
     * locked (unique key on event_id prevents double-locking).
     *
     * @param int      $event_id  tblcallevents.id
     * @param int      $bd_uid    BD user id
     * @param int      $cid_id    init_call.id
     * @return int|false
     */
    public function lock_agenda($event_id, $bd_uid, $cid_id) {
        $event_id = (int)$event_id;
        $bd_uid   = (int)$bd_uid;
        $cid_id   = (int)$cid_id;

        // If this event already has a lock, bail out.
        if ($this->is_agenda_locked($event_id)) {
            log_message('info', 'mom_v2 lock_agenda: event_id ' . $event_id . ' already locked');
            return false;
        }

        // Pull the meeting type from tblcallevents.
        $this->db->select('id, actiontype_id, cid_id');
        $this->db->where('id', $event_id);
        $event = $this->db->get('tblcallevents')->row_array();
        if (empty($event)) {
            log_message('error', 'mom_v2 lock_agenda: event_id ' . $event_id . ' not found in tblcallevents');
            return false;
        }
        $actiontype_id = (int)($event['actiontype_id'] ?? 0);

        // Pull current_status_id and partner_type from init_call.
        $this->db->select('id, current_status_id, partner_type');
        $this->db->where('id', $cid_id);
        $lead = $this->db->get('init_call')->row_array();
        if (empty($lead)) {
            log_message('error', 'mom_v2 lock_agenda: cid_id ' . $cid_id . ' not found in init_call');
            return false;
        }
        $cstatus_at_lock = (int)($lead['current_status_id'] ?? 0);
        $partner_type    = $lead['partner_type'] ?? null;

        // Compute required questions for this meeting context.
        $required_questions = $this->get_required_questions($cid_id, $actiontype_id, $partner_type, $cstatus_at_lock);
        $required_ids = array_column($required_questions, 'question_id');

        // Insert the agenda lock row.
        $row = [
            'event_id'               => $event_id,
            'bd_uid'                 => $bd_uid,
            'cid_id'                 => $cid_id,
            'required_questions_json'=> json_encode($required_ids),
            'cstatus_at_lock'        => $cstatus_at_lock,
            'actiontype_id'          => $actiontype_id,
            'bd_committed'           => 1
        ];

        $this->db->insert('mom_v2_meeting_agenda_lock', $row);
        $lock_id = $this->db->insert_id();

        if (!$lock_id) {
            log_message('error', 'mom_v2 lock_agenda: insert failed for event_id ' . $event_id);
            return false;
        }

        log_message('info', 'mom_v2 lock_agenda: locked event_id=' . $event_id . ' lock_id=' . $lock_id . ' required_count=' . count($required_ids));
        return (int)$lock_id;
    }

    /**
     * Return all question rows from mom_v2_question_schema that apply to this
     * specific meeting context.
     *
     * Required question rules from migration 037 seed:
     *   required_always = 1             -> always included
     *   required_cstatus_min            -> included if current_status_id >= that value
     *   required_rp_only = 1            -> included only when actiontype_id = 4 (RP)
     *   required_partner_non_direct = 1 -> included when partner_type is set and is NOT 'Direct'
     *
     * @param int      $cid_id         init_call.id (used if cstatus_val is not passed directly)
     * @param int      $actiontype_id  meeting type from tblcallevents
     * @param string|null $partner_type  e.g. 'NGO', 'Direct', null
     * @param int      $cstatus_val    current_status_id; pass 0 to let the method look it up
     * @return array   rows from mom_v2_question_schema
     */
    public function get_required_questions($cid_id, $actiontype_id, $partner_type = null, $cstatus_val = 0) {
        $cid_id        = (int)$cid_id;
        $actiontype_id = (int)$actiontype_id;
        $cstatus_val   = (int)$cstatus_val;

        // If caller did not supply cstatus_val, look it up now.
        if ($cstatus_val === 0 && $cid_id > 0) {
            $this->db->select('current_status_id');
            $this->db->where('id', $cid_id);
            $lead = $this->db->get('init_call')->row_array();
            $cstatus_val = (int)($lead['current_status_id'] ?? 0);
        }

        $this->db->select('question_id, sr_no, question_text, answer_type, options_json,
                           required_always, required_cstatus_min, required_rp_only,
                           required_partner_non_direct, voice_keywords_json, sort_order');
        $this->db->where('is_active', 1);
        $this->db->order_by('sort_order', 'ASC');
        $all_questions = $this->db->get('mom_v2_question_schema')->result_array();

        $required = [];
        foreach ($all_questions as $q) {
            if ($this->_question_applies($q, $cstatus_val, $actiontype_id, $partner_type)) {
                $required[] = $q;
            }
        }

        return $required;
    }

    /**
     * Check whether an agenda lock exists for the given event.
     *
     * @param int $event_id
     * @return bool
     */
    public function is_agenda_locked($event_id) {
        $this->db->where('event_id', (int)$event_id);
        $count = $this->db->count_all_results('mom_v2_meeting_agenda_lock');
        return $count > 0;
    }

    /**
     * Return the lock row for an event, or null if none exists.
     *
     * @param int $event_id
     * @return array|null
     */
    public function get_lock($event_id) {
        $this->db->where('event_id', (int)$event_id);
        $row = $this->db->get('mom_v2_meeting_agenda_lock')->row_array();
        return $row ?: null;
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Decide whether a single question row applies to this meeting context.
     *
     * Each rule is evaluated independently; a question is included if ANY rule
     * that fires evaluates to true.
     *
     * @param array       $q              row from mom_v2_question_schema
     * @param int         $cstatus_val    current_status_id of the lead
     * @param int         $actiontype_id  meeting type
     * @param string|null $partner_type
     * @return bool
     */
    private function _question_applies($q, $cstatus_val, $actiontype_id, $partner_type) {
        // Rule 1: required_always = 1 means this question is always included.
        if ((int)$q['required_always'] === 1) {
            return true;
        }

        // Rule 2: required_cstatus_min - include when the lead's current status
        // has reached at least this threshold.
        if (!empty($q['required_cstatus_min']) && $cstatus_val >= (int)$q['required_cstatus_min']) {
            return true;
        }

        // Rule 3: required_rp_only = 1 - include only for RP meetings (actiontype_id = 4).
        if ((int)$q['required_rp_only'] === 1 && $actiontype_id === 4) {
            return true;
        }

        // Rule 4: required_partner_non_direct = 1 - include when a partner_type
        // is set and that partner type is not 'Direct'.
        if ((int)$q['required_partner_non_direct'] === 1
            && !empty($partner_type)
            && strtolower($partner_type) !== 'direct') {
            return true;
        }

        return false;
    }
}
