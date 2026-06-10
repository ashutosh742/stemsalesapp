<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * InductionStep_model
 *
 * CodeIgniter 3 model for STEM Migration 045 - Sales Coach Hub.
 * Reads from induction_step_template (the canonical 33 seed rows -
 * 11 per role track BD, CM, RM) and joins with induction_progress
 * for the per-user journey view.
 *
 * Plain English, ASCII only, no em-dashes. Uses 'Rs' for rupees,
 * 'percent' spelled out, 'over' for greater than.
 *
 * Reference schema: stem_migration_045_sql.sql (FROZEN).
 */
class InductionStep_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -----------------------------------------------------------------
    // get_template_by_id
    //
    // Single template row by primary key.
    // -----------------------------------------------------------------
    public function get_template_by_id($template_id) {
        $template_id = (int) $template_id;
        if ($template_id <= 0) {
            return null;
        }
        $row = $this->db
            ->where('template_id', $template_id)
            ->where('is_active', 1)
            ->get('induction_step_template')
            ->row_array();
        return $row ? $row : null;
    }

    // -----------------------------------------------------------------
    // get_templates_by_track
    //
    // All 11 active templates for a role_track (BD / CM / RM),
    // ordered by step_order.
    // -----------------------------------------------------------------
    public function get_templates_by_track($role_track) {
        $role_track = strtoupper(trim((string) $role_track));
        if (!in_array($role_track, array('BD', 'CM', 'RM'), true)) {
            return array();
        }
        return $this->db
            ->where('role_track', $role_track)
            ->where('is_active', 1)
            ->order_by('step_order', 'ASC')
            ->get('induction_step_template')
            ->result_array();
    }

    // -----------------------------------------------------------------
    // get_p0_day_modules
    //
    // The 7 P0 day-by-day rows for a track (day_index 1..7).
    // -----------------------------------------------------------------
    public function get_p0_day_modules($role_track) {
        $role_track = strtoupper(trim((string) $role_track));
        if (!in_array($role_track, array('BD', 'CM', 'RM'), true)) {
            return array();
        }
        return $this->db
            ->where('role_track', $role_track)
            ->where('phase_code', 'P0')
            ->where('is_active', 1)
            ->order_by('day_index', 'ASC')
            ->get('induction_step_template')
            ->result_array();
    }

    // -----------------------------------------------------------------
    // get_phase_rows
    //
    // Returns the 1 row for P1 / P2 / P3, or all 7 rows for P0.
    // -----------------------------------------------------------------
    public function get_phase_rows($role_track, $phase_code) {
        $role_track = strtoupper(trim((string) $role_track));
        $phase_code = strtoupper(trim((string) $phase_code));
        if (!in_array($role_track, array('BD', 'CM', 'RM'), true)) {
            return array();
        }
        if (!in_array($phase_code, array('P0', 'P1', 'P2', 'P3'), true)) {
            return array();
        }
        $q = $this->db
            ->where('role_track', $role_track)
            ->where('phase_code', $phase_code)
            ->where('is_active', 1);
        if ($phase_code === 'P0') {
            $q->order_by('day_index', 'ASC');
        } else {
            $q->order_by('step_order', 'ASC');
        }
        return $q->get('induction_step_template')->result_array();
    }

    // -----------------------------------------------------------------
    // get_active_templates
    //
    // Every active template across all tracks (used by admin views).
    // -----------------------------------------------------------------
    public function get_active_templates() {
        return $this->db
            ->where('is_active', 1)
            ->order_by('role_track', 'ASC')
            ->order_by('step_order', 'ASC')
            ->get('induction_step_template')
            ->result_array();
    }

    // -----------------------------------------------------------------
    // get_progress_for_user
    //
    // JOIN induction_progress with induction_step_template for one
    // user, ordered by step_order. Returns the full journey snapshot.
    // -----------------------------------------------------------------
    public function get_progress_for_user($user_uid) {
        $user_uid = (int) $user_uid;
        if ($user_uid <= 0) {
            return array();
        }
        $this->db
            ->select('ip.progress_id, ip.user_uid, ip.template_id, ip.status, '
                . 'ip.scheduled_start_date, ip.scheduled_end_date, '
                . 'ip.started_at, ip.completed_at, ip.score_pct, ip.verdict, '
                . 'ip.verdict_by_uid, ip.docs_shared_count, ip.docs_ack_count, '
                . 'ip.notes_json, ip.last_event_at, '
                . 'ist.role_track, ist.phase_code, ist.day_index, ist.step_order, '
                . 'ist.step_code, ist.title, ist.description, '
                . 'ist.primary_owner_type, ist.secondary_owner_type, '
                . 'ist.target_days_from_join, ist.exit_gate_json, '
                . 'ist.pip_trigger_json, '
                . 'DATEDIFF(ip.scheduled_end_date, CURDATE()) AS days_to_end', false)
            ->from('induction_progress ip')
            ->join('induction_step_template ist', 'ist.template_id = ip.template_id', 'inner')
            ->where('ip.user_uid', $user_uid)
            ->order_by('ist.step_order', 'ASC');
        return $this->db->get()->result_array();
    }

    // -----------------------------------------------------------------
    // get_step_doc_count
    //
    // Returns assoc array {shared, acked} for a progress_id.
    // shared = active induction_step_doc rows for that step.
    // acked  = distinct induction_doc_ack rows by the joiner.
    // -----------------------------------------------------------------
    public function get_step_doc_count($progress_id) {
        $progress_id = (int) $progress_id;
        if ($progress_id <= 0) {
            return array('shared' => 0, 'acked' => 0);
        }
        // shared count
        $shared_row = $this->db
            ->select('COUNT(*) AS c', false)
            ->from('induction_step_doc')
            ->where('progress_id', $progress_id)
            ->where('is_active', 1)
            ->get()->row_array();
        $shared = $shared_row ? (int) $shared_row['c'] : 0;

        // ack count - tied to that progress row's joiner user_uid via induction_progress
        $acked_row = $this->db
            ->select('COUNT(DISTINCT ida.ack_id) AS c', false)
            ->from('induction_step_doc isd')
            ->join('induction_progress ip', 'ip.progress_id = isd.progress_id', 'inner')
            ->join('induction_doc_ack ida',
                'ida.doc_id = isd.doc_id AND ida.user_uid = ip.user_uid', 'left')
            ->where('isd.progress_id', $progress_id)
            ->where('isd.is_active', 1)
            ->get()->row_array();
        $acked = $acked_row ? (int) $acked_row['c'] : 0;

        return array('shared' => $shared, 'acked' => $acked);
    }
}

/* End of file InductionStep_model.php */
