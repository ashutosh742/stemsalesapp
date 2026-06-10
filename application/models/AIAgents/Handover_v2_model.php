<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/models/AIAgents/Handover_v2_model.php
 * Migration 046 Closure Handover v2 model.
 * Plain ASCII only. No em-dash. Uses Rs for rupees.
 * All queries are prepared. Never concat user input.
 */
class Handover_v2_model extends CI_Model {

    // Required fields per section. csr fields only required when csr_flag is 1.
    private $section_a = array('project_code','compname','designation','dispatch_address','dispatch_pincode','dispatch_state','dispatch_city','expected_install_date');
    private $section_b = array('artwork_required');
    private $section_c = array('billing_entity','billing_address','billing_pincode','payment_terms');
    private $section_d_csr = array('dm_email','stem_csr1_reg_no','utilisation_cert_required','impact_report_required','third_party_audit_required','csr2_annual_reporting','acontact_designation');
    private $section_e = array('travel_cluster_at_close_json');
    private $section_f = array('bd_signoff_at');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Upsert a draft handover.
     * If a draft already exists for cid_id and closing_bd_uid update it, else insert.
     */
    public function save_draft($cid_id, $bd_uid, $payload) {
        $cid_id = (int)$cid_id;
        $bd_uid = (int)$bd_uid;

        $existing = $this->db->query(
            'SELECT id FROM handover_v2 WHERE cid_id = ? AND closing_bd_uid = ? AND status = ? LIMIT 1',
            array($cid_id, $bd_uid, 'draft')
        )->row();

        $fields = $this->_allowed_fields();
        $set_sql = array();
        $params = array();
        foreach ($fields as $f) {
            if (array_key_exists($f, $payload)) {
                $set_sql[] = $f . ' = ?';
                $params[] = $payload[$f];
            }
        }

        if ($existing) {
            if (empty($set_sql)) {
                return array('ok' => true, 'id' => (int)$existing->id, 'updated' => 0);
            }
            $sql = 'UPDATE handover_v2 SET ' . implode(', ', $set_sql) . ', status = ? WHERE id = ?';
            $params[] = 'draft';
            $params[] = (int)$existing->id;
            $this->db->query($sql, $params);
            return array('ok' => true, 'id' => (int)$existing->id, 'updated' => 1);
        }

        $cols = array('cid_id','closing_bd_uid','status');
        $vals = array($cid_id, $bd_uid, 'draft');
        $placeholders = array('?','?','?');
        foreach ($fields as $f) {
            if (array_key_exists($f, $payload)) {
                $cols[] = $f;
                $vals[] = $payload[$f];
                $placeholders[] = '?';
            }
        }
        $sql = 'INSERT INTO handover_v2 (' . implode(',', $cols) . ') VALUES (' . implode(',', $placeholders) . ')';
        $this->db->query($sql, $vals);
        $new_id = (int)$this->db->insert_id();
        return array('ok' => true, 'id' => $new_id, 'inserted' => 1);
    }

    /**
     * Submit handover. Validates required sections and csr conditional rule.
     * Snapshots travel cluster from user.travel_cluster_json into the handover.
     */
    public function submit($id, $bd_uid) {
        $id = (int)$id;
        $bd_uid = (int)$bd_uid;

        $row = $this->db->query('SELECT * FROM handover_v2 WHERE id = ? AND closing_bd_uid = ? LIMIT 1', array($id, $bd_uid))->row_array();
        if (!$row) {
            return array('ok' => false, 'error' => 'Handover not found or not owned by this BD');
        }
        if ($row['status'] !== 'draft') {
            return array('ok' => false, 'error' => 'Only draft handovers can be submitted. Current status ' . $row['status']);
        }

        $missing = $this->_validate_required($row);
        if (!empty($missing)) {
            return array('ok' => false, 'error' => 'Missing required fields', 'fields' => $missing);
        }

        // Snapshot travel cluster from user record
        $user_row = $this->db->query('SELECT travel_cluster_json FROM user WHERE uid = ? LIMIT 1', array($bd_uid))->row_array();
        $snapshot = isset($user_row['travel_cluster_json']) ? $user_row['travel_cluster_json'] : '[]';

        $this->db->query(
            'UPDATE handover_v2 SET status = ?, submitted_at = NOW(), travel_cluster_at_close_json = ? WHERE id = ?',
            array('submitted', $snapshot, $id)
        );
        return array('ok' => true, 'id' => $id, 'status' => 'submitted');
    }

    /**
     * List handovers for a BD from the v_handover_pending view plus their drafts.
     */
    public function list_for_bd($bd_uid, $status_filter = null) {
        $bd_uid = (int)$bd_uid;
        if ($status_filter !== null && $status_filter !== '') {
            $sql = 'SELECT * FROM v_handover_pending WHERE closing_bd_uid = ? AND status = ? ORDER BY submitted_at DESC';
            return $this->db->query($sql, array($bd_uid, $status_filter))->result_array();
        }
        $sql = 'SELECT * FROM v_handover_pending WHERE closing_bd_uid = ? ORDER BY submitted_at DESC';
        return $this->db->query($sql, array($bd_uid))->result_array();
    }

    /**
     * List handovers awaiting CM approval. Matches by direct cm_uid or via reporting chain on the closing BD.
     */
    public function list_for_cm_approval($cm_uid) {
        $cm_uid = (int)$cm_uid;
        if ($cm_uid <= 0) return array();
        // Match by explicit cm_uid on handover OR by BD's aadmin mapping (real CM->BD linkage)
        $sql = 'SELECT vp.* FROM v_handover_pending vp '
             . 'LEFT JOIN user_details ud ON ud.user_id = vp.closing_bd_uid '
             . 'WHERE vp.status = ? AND (vp.cm_uid = ? OR ud.aadmin = ?) '
             . 'ORDER BY vp.submitted_at ASC';
        return $this->db->query($sql, array('submitted', $cm_uid, $cm_uid))->result_array();
    }

    /**
     * Full detail with RBAC. BD sees own. CM sees subordinates. Admin sees all.
     */
    public function detail($id, $requesting_uid) {
        $id = (int)$id;
        $requesting_uid = (int)$requesting_uid;

        $row = $this->db->query('SELECT * FROM handover_v2 WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) {
            return array('ok' => false, 'error' => 'Handover not found');
        }

        $req_user = $this->db->query('SELECT uid, type_id, admin_id FROM user WHERE uid = ? LIMIT 1', array($requesting_uid))->row_array();
        if (!$req_user) {
            return array('ok' => false, 'error' => 'Requesting user not found');
        }
        $role = (isset($req_user['type_id']) && (int)$req_user['type_id'] === 1) ? 'admin' : 'bd';

        $allowed = false;
        if ($role === 'admin') {
            $allowed = true;
        } else if ((int)$row['closing_bd_uid'] === $requesting_uid) {
            $allowed = true;
        } else {
            $bd = $this->db->query('SELECT admin_id FROM user WHERE uid = ? LIMIT 1', array((int)$row['closing_bd_uid']))->row_array();
            if ($bd && (int)$bd['admin_id'] === $requesting_uid) {
                $allowed = true;
            }
            if (!$allowed && (int)$row['cm_uid'] === $requesting_uid) {
                $allowed = true;
            }
        }
        if (!$allowed) {
            return array('ok' => false, 'error' => 'Access denied');
        }
        return array('ok' => true, 'data' => $row);
    }

    /**
     * CM approve. Sets status cm_approved and stamps approved_at.
     */
    public function cm_approve($id, $cm_uid, $remarks) {
        $id = (int)$id;
        $cm_uid = (int)$cm_uid;
        $row = $this->db->query('SELECT status FROM handover_v2 WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) {
            return array('ok' => false, 'error' => 'Handover not found');
        }
        if ($row['status'] !== 'submitted') {
            return array('ok' => false, 'error' => 'Only submitted handovers can be approved');
        }
        $this->db->query(
            'UPDATE handover_v2 SET status = ?, approved_at = NOW(), cm_approval_at = NOW(), cm_uid = ?, cm_remarks = ? WHERE id = ?',
            array('cm_approved', $cm_uid, $remarks, $id)
        );
        $this->_notify_installation_team($id);
        return array('ok' => true, 'id' => $id, 'status' => 'cm_approved');
    }

    /**
     * CM reject. Sets status rejected and records reason.
     */
    public function cm_reject($id, $cm_uid, $reason) {
        $id = (int)$id;
        $cm_uid = (int)$cm_uid;
        $row = $this->db->query('SELECT status FROM handover_v2 WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) {
            return array('ok' => false, 'error' => 'Handover not found');
        }
        if ($row['status'] !== 'submitted') {
            return array('ok' => false, 'error' => 'Only submitted handovers can be rejected');
        }
        $this->db->query(
            'UPDATE handover_v2 SET status = ?, cm_uid = ?, rejection_reason = ? WHERE id = ?',
            array('rejected', $cm_uid, $reason, $id)
        );
        return array('ok' => true, 'id' => $id, 'status' => 'rejected');
    }

    public function mark_installation_started($id) {
        $id = (int)$id;
        $row = $this->db->query('SELECT status FROM handover_v2 WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) {
            return array('ok' => false, 'error' => 'Handover not found');
        }
        if ($row['status'] !== 'cm_approved') {
            return array('ok' => false, 'error' => 'Installation can start only after CM approval');
        }
        $this->db->query('UPDATE handover_v2 SET status = ? WHERE id = ?', array('installation_started', $id));
        return array('ok' => true, 'id' => $id, 'status' => 'installation_started');
    }

    public function mark_complete($id) {
        $id = (int)$id;
        $row = $this->db->query('SELECT status FROM handover_v2 WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) {
            return array('ok' => false, 'error' => 'Handover not found');
        }
        if ($row['status'] !== 'installation_started') {
            return array('ok' => false, 'error' => 'Mark complete requires installation_started status');
        }
        $this->db->query('UPDATE handover_v2 SET status = ? WHERE id = ?', array('complete', $id));
        return array('ok' => true, 'id' => $id, 'status' => 'complete');
    }

    /**
     * Cron job. Finds Won deals where 5 days have passed without a submitted handover.
     * Inserts breach rows into handover_sla_log and flips sla_breach_flag on handover_v2 if a row exists.
     */
    public function compute_sla_breaches() {
        $sql = 'SELECT ic.id AS cid_id, ic.mainbd AS closing_bd_uid, ic.won_at '
             . 'FROM init_call ic '
             . 'LEFT JOIN handover_v2 h ON h.cid_id = ic.id AND h.status IN (?,?,?,?) '
             . 'WHERE ic.cstatus = 12 AND ic.won_at IS NOT NULL '
             . 'AND ic.won_at < DATE_SUB(NOW(), INTERVAL 5 DAY) '
             . 'AND h.id IS NULL';
        $rows = $this->db->query($sql, array('submitted','cm_approved','installation_started','complete'))->result_array();

        $count = 0;
        foreach ($rows as $r) {
            $already = $this->db->query(
                'SELECT id FROM handover_sla_log WHERE cid_id = ? AND breach = 1 LIMIT 1',
                array((int)$r['cid_id'])
            )->row();
            if ($already) continue;

            $this->db->query(
                'INSERT INTO handover_sla_log (cid_id, closing_bd_uid, won_at, due_at, submitted_at, days_to_submit, breach) '
                . 'VALUES (?, ?, ?, DATE_ADD(?, INTERVAL 5 DAY), NULL, NULL, 1)',
                array((int)$r['cid_id'], (int)$r['closing_bd_uid'], $r['won_at'], $r['won_at'])
            );

            $this->db->query(
                'UPDATE handover_v2 SET sla_breach_flag = 1 WHERE cid_id = ? AND status = ?',
                array((int)$r['cid_id'], 'draft')
            );
            $count++;
        }
        return array('ok' => true, 'breaches_logged' => $count);
    }

    // Private helpers

    private function _allowed_fields() {
        return array(
            'project_code','compname','ctype','fbudget','designation','dispatch_address','dispatch_pincode','dispatch_state','dispatch_city','expected_install_date',
            'artwork_required','artwork_brief','lab_name_plate_text','naming_rights_donor_name',
            'billing_entity','gst_number','pan_number','billing_address','billing_pincode','payment_terms',
            'csr_flag','dm_email','stem_csr1_reg_no','utilisation_cert_required','impact_report_required','third_party_audit_required','csr2_annual_reporting','acontact_designation',
            'travel_cluster_at_close_json',
            'bd_signoff_at','bd_remarks'
        );
    }

    private function _validate_required($row) {
        $missing = array();
        foreach ($this->section_a as $f) { if ($this->_blank($row, $f)) $missing[] = $f; }
        foreach ($this->section_b as $f) { if (!isset($row[$f]) || $row[$f] === null || $row[$f] === '') $missing[] = $f; }
        foreach ($this->section_c as $f) { if ($this->_blank($row, $f)) $missing[] = $f; }
        if (!empty($row['csr_flag']) && (int)$row['csr_flag'] === 1) {
            foreach ($this->section_d_csr as $f) { if ($this->_blank($row, $f)) $missing[] = $f; }
        }
        foreach ($this->section_f as $f) { if ($this->_blank($row, $f)) $missing[] = $f; }
        return $missing;
    }

    private function _blank($row, $key) {
        return !isset($row[$key]) || $row[$key] === null || $row[$key] === '';
    }

    private function _notify_installation_team($handover_id) {
        // Hook point for installation queue insert or push notification.
        // Kept lightweight here. Real notify call is wired by the installation module.
        return true;
    }
}
