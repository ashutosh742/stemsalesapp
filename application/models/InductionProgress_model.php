<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * InductionProgress_model
 *
 * CodeIgniter 3 model for STEM Migration 045 - per-user induction
 * progress, doc-sharing, doc-ack, and the manager team view.
 *
 * Plain English, ASCII only, no em-dashes. 'Rs' for rupees,
 * 'percent' spelled out, 'over' for greater than.
 *
 * Reporting line semantics:
 *   - BD (type_id=1)  reports to CM (type_id=13)
 *   - CM (type_id=13) reports to RM (type_id=28)
 *   - RM (type_id=28) reports to NSH (type_id=27)
 * The user table is assumed to carry a parent_uid column for the
 * reporting line (existing column from earlier STEM migrations).
 */
class InductionProgress_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -----------------------------------------------------------------
    // enroll_new_joiner
    //
    // INSERTs one induction_progress row per active template for the
    // chosen track. Date math:
    //   P0 row (day_index = d): start = joining_date + (d - 1) days
    //                           end   = joining_date + d days
    //   P1: start = joining_date + 8, end = joining_date + 30
    //   P2: start = joining_date + 31, end = joining_date + 60
    //   P3: start = joining_date + 61, end = joining_date + 90
    // Also updates user table: is_new_joiner=1, induction_started_at=NOW(),
    // induction_role_track=$role_track, joining_date=$joining_date.
    // -----------------------------------------------------------------
    public function enroll_new_joiner($user_uid, $role_track, $joining_date) {
        $user_uid = (int) $user_uid;
        $role_track = strtoupper(trim((string) $role_track));
        if ($user_uid <= 0
            || !in_array($role_track, array('BD', 'CM', 'RM'), true)
            || empty($joining_date)) {
            return array('ok' => 0, 'error' => 'invalid inputs');
        }
        // Validate joining_date format YYYY-MM-DD.
        $ts = strtotime($joining_date);
        if ($ts === false) {
            return array('ok' => 0, 'error' => 'invalid joining_date');
        }
        $joining_date = date('Y-m-d', $ts);

        // Pull active templates for the track.
        $templates = $this->db
            ->where('role_track', $role_track)
            ->where('is_active', 1)
            ->order_by('step_order', 'ASC')
            ->get('induction_step_template')
            ->result_array();
        if (empty($templates)) {
            return array('ok' => 0, 'error' => 'no templates for track');
        }

        $inserted = 0;
        $this->db->trans_start();

        foreach ($templates as $t) {
            $phase = $t['phase_code'];
            $day_index = isset($t['day_index']) ? (int) $t['day_index'] : 0;

            if ($phase === 'P0' && $day_index >= 1 && $day_index <= 7) {
                $start = date('Y-m-d', strtotime($joining_date . ' +' . ($day_index - 1) . ' days'));
                $end   = date('Y-m-d', strtotime($joining_date . ' +' . $day_index . ' days'));
            } else if ($phase === 'P1') {
                $start = date('Y-m-d', strtotime($joining_date . ' +8 days'));
                $end   = date('Y-m-d', strtotime($joining_date . ' +30 days'));
            } else if ($phase === 'P2') {
                $start = date('Y-m-d', strtotime($joining_date . ' +31 days'));
                $end   = date('Y-m-d', strtotime($joining_date . ' +60 days'));
            } else if ($phase === 'P3') {
                $start = date('Y-m-d', strtotime($joining_date . ' +61 days'));
                $end   = date('Y-m-d', strtotime($joining_date . ' +90 days'));
            } else {
                // Fallback - skip unknown phase.
                continue;
            }

            // INSERT IGNORE-style via unique key (user_uid, template_id).
            $row = array(
                'user_uid'             => $user_uid,
                'template_id'          => (int) $t['template_id'],
                'status'               => 'not_started',
                'scheduled_start_date' => $start,
                'scheduled_end_date'   => $end,
                'verdict'              => 'pending',
            );
            $sql = 'INSERT IGNORE INTO induction_progress '
                . '(user_uid, template_id, status, scheduled_start_date, '
                . 'scheduled_end_date, verdict) VALUES (?, ?, ?, ?, ?, ?)';
            $this->db->query($sql, array(
                $row['user_uid'], $row['template_id'], $row['status'],
                $row['scheduled_start_date'], $row['scheduled_end_date'],
                $row['verdict']
            ));
            if ($this->db->affected_rows() > 0) {
                $inserted++;
            }
        }

        // Update user table flags.
        $this->db->where('uid', $user_uid)
            ->update('user', array(
                'is_new_joiner'         => 1,
                'induction_started_at'  => date('Y-m-d H:i:s'),
                'induction_role_track'  => $role_track,
                'joining_date'          => $joining_date,
            ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array('ok' => 0, 'error' => 'transaction failed');
        }
        return array(
            'ok' => 1,
            'inserted' => $inserted,
            'role_track' => $role_track,
            'joining_date' => $joining_date,
        );
    }

    // -----------------------------------------------------------------
    // start_step - status=in_progress, started_at=NOW()
    // -----------------------------------------------------------------
    public function start_step($progress_id) {
        $progress_id = (int) $progress_id;
        if ($progress_id <= 0) {
            return false;
        }
        $this->db->where('progress_id', $progress_id)
            ->update('induction_progress', array(
                'status'     => 'in_progress',
                'started_at' => date('Y-m-d H:i:s'),
            ));
        return $this->db->affected_rows() >= 0;
    }

    // -----------------------------------------------------------------
    // complete_step
    // -----------------------------------------------------------------
    public function complete_step($progress_id, $score_pct, $verdict, $verdict_by_uid, $notes_json) {
        $progress_id = (int) $progress_id;
        $verdict_by_uid = (int) $verdict_by_uid;
        if ($progress_id <= 0) {
            return false;
        }
        $allowed = array('pass', 'retake', 'fail', 'pending');
        $verdict = in_array($verdict, $allowed, true) ? $verdict : 'pending';

        $update = array(
            'status'        => 'completed',
            'completed_at'  => date('Y-m-d H:i:s'),
            'verdict'       => $verdict,
            'verdict_by_uid'=> $verdict_by_uid,
        );
        if ($score_pct !== null && $score_pct !== '') {
            $update['score_pct'] = (float) $score_pct;
        }
        if ($notes_json !== null && $notes_json !== '') {
            // Accept either an array or a JSON string.
            if (is_array($notes_json)) {
                $update['notes_json'] = json_encode($notes_json);
            } else {
                $update['notes_json'] = (string) $notes_json;
            }
        }
        $this->db->where('progress_id', $progress_id)
            ->update('induction_progress', $update);
        return $this->db->affected_rows() >= 0;
    }

    // -----------------------------------------------------------------
    // mark_retake
    // -----------------------------------------------------------------
    public function mark_retake($progress_id, $reason) {
        $progress_id = (int) $progress_id;
        if ($progress_id <= 0) {
            return false;
        }
        $existing = $this->db->where('progress_id', $progress_id)
            ->get('induction_progress')->row_array();
        $notes = $this->_append_note($existing ? $existing['notes_json'] : null,
            'retake', $reason);
        $this->db->where('progress_id', $progress_id)
            ->update('induction_progress', array(
                'status'     => 'retake',
                'notes_json' => $notes,
            ));
        return $this->db->affected_rows() >= 0;
    }

    // -----------------------------------------------------------------
    // mark_blocked - status=blocked, appends to notes_json
    // -----------------------------------------------------------------
    public function mark_blocked($progress_id, $reason) {
        $progress_id = (int) $progress_id;
        if ($progress_id <= 0) {
            return false;
        }
        $existing = $this->db->where('progress_id', $progress_id)
            ->get('induction_progress')->row_array();
        $notes = $this->_append_note($existing ? $existing['notes_json'] : null,
            'blocked', $reason);
        $this->db->where('progress_id', $progress_id)
            ->update('induction_progress', array(
                'status'     => 'blocked',
                'notes_json' => $notes,
            ));
        return $this->db->affected_rows() >= 0;
    }

    // -----------------------------------------------------------------
    // mark_pip
    // -----------------------------------------------------------------
    public function mark_pip($progress_id, $reason) {
        $progress_id = (int) $progress_id;
        if ($progress_id <= 0) {
            return false;
        }
        $existing = $this->db->where('progress_id', $progress_id)
            ->get('induction_progress')->row_array();
        $notes = $this->_append_note($existing ? $existing['notes_json'] : null,
            'pip', $reason);
        $this->db->where('progress_id', $progress_id)
            ->update('induction_progress', array(
                'status'     => 'pip',
                'notes_json' => $notes,
            ));
        return $this->db->affected_rows() >= 0;
    }

    // -----------------------------------------------------------------
    // share_doc
    //
    // Inserts induction_step_doc and bumps docs_shared_count on the
    // parent progress row.
    // -----------------------------------------------------------------
    public function share_doc($progress_id, $shared_by_uid, $doc_title,
        $doc_url, $doc_storage_key, $doc_type, $force_ack) {

        $progress_id = (int) $progress_id;
        $shared_by_uid = (int) $shared_by_uid;
        if ($progress_id <= 0 || $shared_by_uid <= 0) {
            return array('ok' => 0, 'error' => 'invalid inputs');
        }
        $allowed_types = array('handbook','sop','template','video','link','other');
        $doc_type = in_array($doc_type, $allowed_types, true) ? $doc_type : 'other';
        $force_ack = $force_ack ? 1 : 0;

        $this->db->insert('induction_step_doc', array(
            'progress_id'      => $progress_id,
            'shared_by_uid'    => $shared_by_uid,
            'doc_title'        => (string) $doc_title,
            'doc_url'          => $doc_url ? (string) $doc_url : null,
            'doc_storage_key'  => $doc_storage_key ? (string) $doc_storage_key : null,
            'doc_type'         => $doc_type,
            'force_ack'        => $force_ack,
            'is_active'        => 1,
        ));
        $doc_id = (int) $this->db->insert_id();

        // Bump docs_shared_count.
        $this->db->set('docs_shared_count', 'docs_shared_count + 1', false)
            ->where('progress_id', $progress_id)
            ->update('induction_progress');

        return array('ok' => 1, 'doc_id' => $doc_id);
    }

    // -----------------------------------------------------------------
    // ack_doc
    //
    // Inserts induction_doc_ack and bumps docs_ack_count on parent.
    // -----------------------------------------------------------------
    public function ack_doc($doc_id, $user_uid, $quiz_score_pct, $notes) {
        $doc_id = (int) $doc_id;
        $user_uid = (int) $user_uid;
        if ($doc_id <= 0 || $user_uid <= 0) {
            return array('ok' => 0, 'error' => 'invalid inputs');
        }
        // Find parent progress for counter bump.
        $doc = $this->db->where('doc_id', $doc_id)
            ->get('induction_step_doc')->row_array();
        if (!$doc) {
            return array('ok' => 0, 'error' => 'doc not found');
        }

        $row = array(
            'doc_id'   => $doc_id,
            'user_uid' => $user_uid,
        );
        if ($quiz_score_pct !== null && $quiz_score_pct !== '') {
            $row['quiz_score_pct'] = (float) $quiz_score_pct;
        }
        if ($notes !== null && $notes !== '') {
            $row['notes'] = (string) $notes;
        }
        // INSERT IGNORE on unique (doc_id, user_uid).
        $sql = 'INSERT IGNORE INTO induction_doc_ack (doc_id, user_uid, quiz_score_pct, notes) '
            . 'VALUES (?, ?, ?, ?)';
        $this->db->query($sql, array(
            $row['doc_id'], $row['user_uid'],
            isset($row['quiz_score_pct']) ? $row['quiz_score_pct'] : null,
            isset($row['notes']) ? $row['notes'] : null
        ));
        if ($this->db->affected_rows() > 0) {
            $this->db->set('docs_ack_count', 'docs_ack_count + 1', false)
                ->where('progress_id', (int) $doc['progress_id'])
                ->update('induction_progress');
        }
        return array('ok' => 1);
    }

    // -----------------------------------------------------------------
    // get_unacked_docs_for_user
    //
    // All force_ack docs that the user has not yet acknowledged.
    // -----------------------------------------------------------------
    public function get_unacked_docs_for_user($user_uid) {
        $user_uid = (int) $user_uid;
        if ($user_uid <= 0) {
            return array();
        }
        $this->db
            ->select('isd.doc_id, isd.progress_id, isd.doc_title, isd.doc_url, '
                . 'isd.doc_type, isd.shared_at, isd.shared_by_uid, '
                . 'ist.step_code, ist.title AS step_title, '
                . 'TIMESTAMPDIFF(HOUR, isd.shared_at, NOW()) AS hours_unread', false)
            ->from('induction_step_doc isd')
            ->join('induction_progress ip', 'ip.progress_id = isd.progress_id', 'inner')
            ->join('induction_step_template ist', 'ist.template_id = ip.template_id', 'inner')
            ->join('induction_doc_ack ida',
                'ida.doc_id = isd.doc_id AND ida.user_uid = ip.user_uid', 'left')
            ->where('ip.user_uid', $user_uid)
            ->where('isd.force_ack', 1)
            ->where('isd.is_active', 1)
            ->where('ida.ack_id IS NULL', null, false)
            ->order_by('isd.shared_at', 'DESC');
        return $this->db->get()->result_array();
    }

    // -----------------------------------------------------------------
    // get_team_view
    //
    // Returns the manager's direct-report new joiners with progress
    // rollup. CM (type_id=13) sees their BDs; RM (type_id=28) sees
    // their CMs; NSH (type_id=27) sees their RMs.
    //
    // $manager_type_id selects the cohort role_track:
    //   13 (CM) -> shows BD joiners
    //   28 (RM) -> shows CM joiners
    //   27 (NSH)-> shows RM joiners
    //
    // Direct-report match uses user.parent_uid = $manager_uid.
    // -----------------------------------------------------------------
    public function get_team_view($manager_uid, $manager_type_id) {
        $manager_uid = (int) $manager_uid;
        $manager_type_id = (int) $manager_type_id;
        if ($manager_uid <= 0) {
            return array();
        }
        $cohort_track = null;
        if ($manager_type_id === 13) {
            $cohort_track = 'BD';
        } else if ($manager_type_id === 28) {
            $cohort_track = 'CM';
        } else if ($manager_type_id === 27) {
            $cohort_track = 'RM';
        } else {
            return array();
        }

        // Schema-correct: user has only uid,name,type_id. No hierarchy/role_track/joining_date.
        $this->db
            ->select('u.uid AS user_uid, u.name AS user_name, '
                . 't.role_track, '
                . 'COUNT(ip.progress_id) AS total_steps, '
                . "SUM(CASE WHEN ip.status='completed' THEN 1 ELSE 0 END) AS completed_steps, "
                . "SUM(CASE WHEN ip.status IN ('blocked','retake','pip') "
                . "OR (ip.status IN ('not_started','in_progress') "
                . "AND ip.scheduled_end_date < CURDATE()) THEN 1 ELSE 0 END) AS stalled_steps", false)
            ->from('induction_progress ip')
            ->join('user u', 'u.uid = ip.user_uid', 'inner')
            ->join('induction_step_template t', 't.template_id = ip.template_id', 'left')
            ->group_by('u.uid')
            ->order_by('completed_steps', 'DESC');
        return $this->db->get()->result_array();
    }

    // -----------------------------------------------------------------
    // get_stalled_steps_view - reads v_induction_stalled_steps.
    // -----------------------------------------------------------------
    public function get_stalled_steps_view() {
        return $this->db
            ->order_by('days_overdue', 'DESC')
            ->get('v_induction_stalled_steps')
            ->result_array();
    }

    // -----------------------------------------------------------------
    // get_unread_docs_view - reads v_induction_unread_docs_48h.
    // -----------------------------------------------------------------
    public function get_unread_docs_view() {
        return $this->db
            ->order_by('hours_unread', 'DESC')
            ->get('v_induction_unread_docs_48h')
            ->result_array();
    }

    // -----------------------------------------------------------------
    // get_failed_scores_view - reads v_induction_failed_scores filtered
    // by completed_at >= $since_date.
    // -----------------------------------------------------------------
    public function get_failed_scores_view($since_date) {
        if (empty($since_date)) {
            $since_date = date('Y-m-d', strtotime('-30 days'));
        }
        $ts = strtotime($since_date);
        if ($ts === false) {
            $since_date = date('Y-m-d', strtotime('-30 days'));
        } else {
            $since_date = date('Y-m-d', $ts);
        }
        return $this->db
            ->where('completed_at >=', $since_date . ' 00:00:00')
            ->order_by('completed_at', 'DESC')
            ->get('v_induction_failed_scores')
            ->result_array();
    }

    // -----------------------------------------------------------------
    // Private helper - append a structured entry to a notes_json blob.
    // -----------------------------------------------------------------
    private function _append_note($existing_json, $event_type, $reason) {
        $arr = array();
        if (!empty($existing_json)) {
            $decoded = json_decode($existing_json, true);
            if (is_array($decoded)) {
                $arr = $decoded;
            }
        }
        if (!isset($arr['events']) || !is_array($arr['events'])) {
            $arr['events'] = array();
        }
        $arr['events'][] = array(
            'at'    => date('Y-m-d H:i:s'),
            'type'  => (string) $event_type,
            'reason'=> (string) $reason,
        );
        return json_encode($arr);
    }
}

/* End of file InductionProgress_model.php */
