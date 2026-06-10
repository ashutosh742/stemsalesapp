<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MobileGapFix2_api - ADDITIVE 2026-06-07
 *
 * Implements 4 endpoints the v2.11.0 app calls but staging never had a method
 * for. All real-data, never mock; empty tables return correct-shape empties.
 * Auth via BearerAuth (same as every other mobile read/write controller).
 * Production untouched.
 *
 *   GET  /api/review_schedule/planner_blocks   ?plan_date=&bd_uid=
 *   GET  /api/csr_prospect/apollo/quota_status
 *   POST /api/district_intel/accept_corporate   {run_id,corporate_id,actor_uid,notes}
 *   POST /api/coach/knowledge/upload_artifact   multipart: file + title + ...
 */
class MobileGapFix2_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function json_out($data, $status = 200) {
        $this->output->set_status_header($status)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($data));
    }

    private function auth_check() {
        $a = $this->bearerauth->resolve();
        if (!$a || empty($a['ok'])) {
            $this->json_out(array('ok' => false, 'error' => 'unauthorized'), 401);
            return false;
        }
        return true;
    }

    // ---------------------------------------------------------------------
    // GET /api/review_schedule/planner_blocks?plan_date=YYYY-MM-DD&bd_uid=
    // App expects { blocks:[{ ..., block_reason }] } where block_reason in
    // review_scheduled | review_in_progress | review_overdue.
    // ---------------------------------------------------------------------
    public function planner_blocks() {
        if (!$this->auth_check()) return;

        $plan_date = $this->input->get('plan_date');
        $bd_uid    = (int) $this->input->get('bd_uid');

        if (!$plan_date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $plan_date)) {
            $plan_date = date('Y-m-d');
        }
        if ($bd_uid <= 0) {
            return $this->json_out(array('ok' => true, 'empty' => true, 'blocks' => array()));
        }

        // Reviews on/before the plan date that are not finished -> planner blocks.
        $rows = $this->db->query(
            "SELECT id, bd_uid, manager_uid, rm_uid, review_type_id,
                    scheduled_date, scheduled_start_time, scheduled_end_time,
                    min_duration_minutes, status, missed_reason
             FROM review_schedule
             WHERE bd_uid = ?
               AND scheduled_date <= ?
               AND status IN ('pending','in_progress','missed')
             ORDER BY scheduled_date ASC, scheduled_start_time ASC",
            array($bd_uid, $plan_date)
        )->result_array();

        $blocks = array();
        foreach ($rows as $r) {
            $reason = 'review_scheduled';
            if ($r['status'] === 'in_progress') {
                $reason = 'review_in_progress';
            } elseif ($r['status'] === 'missed' || $r['scheduled_date'] < $plan_date) {
                $reason = 'review_overdue';
            }
            $blocks[] = array(
                'block_id'        => (int) $r['id'],
                'bd_uid'          => (int) $r['bd_uid'],
                'manager_uid'     => (int) $r['manager_uid'],
                'rm_uid'          => (int) $r['rm_uid'],
                'review_type_id'  => (int) $r['review_type_id'],
                'scheduled_date'  => $r['scheduled_date'],
                'start_time'      => $r['scheduled_start_time'],
                'end_time'        => $r['scheduled_end_time'],
                'min_minutes'     => (int) $r['min_duration_minutes'],
                'status'          => $r['status'],
                'missed_reason'   => $r['missed_reason'],
                'block_reason'    => $reason,
            );
        }

        $this->json_out(array(
            'ok'        => true,
            'plan_date' => $plan_date,
            'bd_uid'    => $bd_uid,
            'blocks'    => $blocks,
            'count'     => count($blocks),
            'empty'     => count($blocks) === 0,
        ));
    }

    // ---------------------------------------------------------------------
    // GET /api/csr_prospect/apollo/quota_status
    // App expects { quota: { used, cap, ... } }. Reads v_apollo_quota_today.
    // ---------------------------------------------------------------------
    public function apollo_quota_status() {
        if (!$this->auth_check()) return;

        $row = $this->db->query("SELECT * FROM v_apollo_quota_today LIMIT 1")->row_array();

        if (!$row) {
            // No quota row yet today -> correct-shape zero quota.
            return $this->json_out(array(
                'ok'    => true,
                'empty' => true,
                'quota' => array(
                    'quota_date'      => date('Y-m-d'),
                    'used'            => 0,
                    'credits_used'    => 0,
                    'cap'             => 0,
                    'remaining'       => 0,
                    'pct_used'        => 0,
                    'quota_status'    => 'unknown',
                ),
            ));
        }

        $this->json_out(array(
            'ok'    => true,
            'quota' => array(
                'quota_date'   => $row['quota_date'],
                'used'         => (int) $row['calls_made'],
                'credits_used' => (int) $row['credits_used'],
                'cap'          => (int) $row['daily_cap'],
                'remaining'    => (int) $row['calls_remaining'],
                'pct_used'     => (float) $row['pct_used'],
                'quota_status' => $row['quota_status'],
            ),
        ));
    }

    // ---------------------------------------------------------------------
    // POST /api/district_intel/accept_corporate {run_id,corporate_id,actor_uid,notes}
    // Logs an "accept_corporate" action against a district-intel run.
    // ---------------------------------------------------------------------
    public function accept_corporate() {
        if (!$this->auth_check()) return;

        $run_id       = (int) ($this->input->post('run_id') ?: 0);
        $corporate_id = (int) ($this->input->post('corporate_id') ?: 0);
        $actor_uid    = (int) ($this->input->post('actor_uid') ?: 0);
        $notes        = trim((string) $this->input->post('notes'));

        if (!$run_id || !$corporate_id) {
            return $this->json_out(array('ok' => false, 'error' => 'run_id_and_corporate_id_required'), 400);
        }
        // actor_uid is NOT NULL in district_intel_action_log; fall back to run owner.
        // (resolved after we fetch the run below)

        // Validate the run exists (defensive; never fatal).
        $run = $this->db->query(
            "SELECT run_id, bd_uid, district_id FROM district_intel_run_log WHERE run_id = ? LIMIT 1",
            array($run_id)
        )->row_array();
        if (!$run) {
            return $this->json_out(array('ok' => false, 'error' => 'run_not_found'), 404);
        }
        if ($actor_uid <= 0) {
            $actor_uid = (int) $run['bd_uid'];   // NOT NULL column: default to run owner
        }

        // Idempotency: do not double-insert the same accept for the same corporate.
        $existing = $this->db->query(
            "SELECT action_id FROM district_intel_action_log
             WHERE run_id = ? AND action_type = 'accept_corporate'
               AND target_kind = 'corporate' AND target_id = ? LIMIT 1",
            array($run_id, $corporate_id)
        )->row_array();

        if ($existing) {
            return $this->json_out(array(
                'ok'        => true,
                'accepted'  => true,
                'action_id' => (int) $existing['action_id'],
                'duplicate' => true,
            ));
        }

        $this->db->query(
            "INSERT INTO district_intel_action_log
                (run_id, actor_uid, action_type, target_kind, target_id, notes, created_at)
             VALUES (?, ?, 'accept_corporate', 'corporate', ?, ?, NOW())",
            array($run_id, $actor_uid, $corporate_id, $notes ?: null)
        );
        $action_id = (int) $this->db->insert_id();

        $this->json_out(array(
            'ok'           => true,
            'accepted'     => true,
            'action_id'    => $action_id,
            'run_id'       => $run_id,
            'corporate_id' => $corporate_id,
            'duplicate'    => false,
        ));
    }

    // ---------------------------------------------------------------------
    // POST /api/coach/knowledge/upload_artifact  (multipart)
    // Fields: title (required), artifact_type, category, tags, body,
    //   target_segment, force_ack, expiry_date, uploaded_by_uid,
    //   uploaded_by_name; optional file upload -> stored, file_url saved.
    // ---------------------------------------------------------------------
    public function upload_artifact() {
        if (!$this->auth_check()) return;

        $title = trim((string) $this->input->post('title'));
        if ($title === '') {
            return $this->json_out(array('ok' => false, 'error' => 'title_required'), 400);
        }

        $artifact_type   = trim((string) $this->input->post('artifact_type')) ?: 'document';
        $category        = trim((string) $this->input->post('category')) ?: 'general';
        $tags            = trim((string) $this->input->post('tags'));
        $body            = (string) $this->input->post('body');
        $target_segment  = trim((string) $this->input->post('target_segment'));
        if ($target_segment === '') {
            // app may send target_seniorities CSV instead
            $target_segment = trim((string) $this->input->post('target_seniorities'));
        }
        $force_ack       = (int) ($this->input->post('force_ack') ?: 0) ? 1 : 0;
        $expiry_date     = trim((string) $this->input->post('expiry_date'));
        if ($expiry_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
            $expiry_date = '';
        }
        $uploaded_by_uid  = (int) ($this->input->post('uploaded_by_uid') ?: 0);
        $uploaded_by_name = trim((string) $this->input->post('uploaded_by_name'));

        // Handle optional file upload.
        $file_url = '';
        if (isset($_FILES['file']) && !empty($_FILES['file']['name']) && empty($_FILES['file']['error'])) {
            $upload_dir = FCPATH . 'uploads/knowledge_artifacts/';
            if (!is_dir($upload_dir)) { @mkdir($upload_dir, 0755, true); }
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['file']['name']));
            $fname = time() . '_' . $safe;
            $dest  = $upload_dir . $fname;
            if (@move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
                $file_url = base_url('uploads/knowledge_artifacts/' . $fname);
            }
        }

        $this->db->query(
            "INSERT INTO knowledge_artifact
                (artifact_type, title, body, file_url, target_segment, force_ack,
                 expiry_date, status, category, tags, version,
                 uploaded_by_uid, uploaded_by_name, uploaded_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'live', ?, ?, 1, ?, ?, NOW(), NOW())",
            array(
                $artifact_type, $title, $body, $file_url, $target_segment, $force_ack,
                $expiry_date ?: null, $category, $tags,
                $uploaded_by_uid ?: null, $uploaded_by_name ?: null
            )
        );
        $id = (int) $this->db->insert_id();

        $this->json_out(array(
            'ok'          => true,
            'published'   => true,
            'artifact_id' => $id,
            'title'       => $title,
            'file_url'    => $file_url,
            'status'      => 'live',
        ));
    }
}
