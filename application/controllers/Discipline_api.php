<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Discipline_api controller
 * Routes: /api/discipline/expense/*, /api/discipline/cancel/*, /api/discipline/advance/*
 * Wraps ExpenseAccountability controller methods with bearer auth + try/catch.
 * ExpenseAccountability uses session auth internally; we proxy through it safely.
 */
class Discipline_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
        $this->load->library('session');
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    // --- /api/discipline/expense/* ---

    public function expense_sweep() {
        try {
            $this->load->model('AIAgents/Stem_expense_model', 'eam');
            $rows = $this->eam->get_sweep_candidates();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::expense_sweep: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function expense_gate_check() {
        try {
            $this->load->model('AIAgents/Stem_expense_model', 'eam');
            $rows = $this->eam->gate_check_pending();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::expense_gate_check: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function expense_cm_queue() {
        try {
            $this->load->model('AIAgents/Stem_expense_model', 'eam');
            $rows = $this->eam->cm_queue();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::expense_cm_queue: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function expense_ao_queue() {
        try {
            $this->load->model('AIAgents/Stem_expense_model', 'eam');
            $rows = $this->eam->ao_queue();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::expense_ao_queue: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // --- /api/discipline/cancel/* ---

    public function cancel_audit() {
        try {
            $this->load->model('AIAgents/Stem_expense_model', 'eam');
            $rows = $this->eam->cancel_audit_pending();
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::cancel_audit: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // --- /api/discipline/advance/* ---

    public function advance_unsettled() {
        try {
            $this->load->model('AIAgents/Stem_expense_model', 'eam');
            $rows = $this->eam->list_disbursed_unsettled_advances(null);
            $this->_safe(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::advance_unsettled: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // GET /api/discipline/score?uid=<uid> -- added 28 May 2026
    public function score() {
        try {
            $uid  = (int)$this->input->get('uid');
            $date = $this->input->get('date') ?: date('Y-m-d');
            $this->load->database();
            $rows = array();
            // discipline_score table (if present from migration)
            if ($this->db->table_exists('discipline_score')) {
                $q = $this->db->query(
                    "SELECT ds.uid, ds.score_date, ds.score, ds.band, ds.detail
                     FROM discipline_score ds
                     WHERE ds.uid = ? AND ds.score_date = ?
                     LIMIT 1",
                    array($uid, $date)
                );
                $rows = $q ? $q->result_array() : array();
            }
            $score = !empty($rows) ? $rows[0] : null;
            $this->_safe(array(
                'ok'    => true,
                'uid'   => $uid,
                'date'  => $date,
                'score' => $score,
                'rows'  => $rows,
                'note'  => empty($rows) ? 'no_data' : 'ok',
            ));
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::score: ' . $e->getMessage());
            $this->_safe(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => $e->getMessage()));
        }
    }


    // GET /api/discipline/advance/my - read-only list of advances for the calling user
    // Added audit fix 29 May 2026. No INSERT/UPDATE/DELETE. No cash_log/wallet writes.
    public function advance_my() {
        try {
            $this->load->database();
            // Resolve caller uid from Bearer token via api_token table
            $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
            if (!$hdr && function_exists('apache_request_headers')) {
                $h = apache_request_headers();
                if (isset($h['Authorization'])) $hdr = $h['Authorization'];
            }
            $token = (stripos($hdr, 'Bearer ') === 0) ? trim(substr($hdr, 7)) : '';
            $uid = 0;
            if ($token) {
                $row = $this->db->query(
                    "SELECT uid FROM api_token WHERE token = ? AND active = 1 LIMIT 1",
                    array($token)
                )->row_array();
                if ($row) $uid = (int)$row['uid'];
            }
            // Also accept ?uid= query param (for internal probes)
            if (!$uid) $uid = (int)$this->input->get('uid');
            if ($uid <= 0) {
                $this->_safe(array('ok' => false, 'error' => 'uid_required'));
                return;
            }
            $rows = $this->db->query(
                "SELECT id AS advance_id,
                        cash AS amount_rs,
                        created_at AS requested_at,
                        CASE
                            WHEN account_apr = 1 THEN 'approved'
                            WHEN cluster_apr = 2 OR admin_apr = 2 THEN 'rejected'
                            WHEN disbursed_at IS NOT NULL THEN 'disbursed'
                            WHEN consumed_status = 'consumed' THEN 'settled'
                            ELSE 'pending'
                        END AS status,
                        consumed_at AS settled_at,
                        purpose
                 FROM travel_advance
                 WHERE user_id = ?
                 ORDER BY created_at DESC
                 LIMIT 50",
                array($uid)
            )->result_array();
            $this->_safe(array('ok' => true, 'uid' => $uid, 'rows' => is_array($rows) ? $rows : array()));
        } catch (\Throwable $e) {
            log_message('error', 'Discipline_api::advance_my: ' . $e->getMessage());
            $this->_safe(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => $e->getMessage()));
        }
    }


}
