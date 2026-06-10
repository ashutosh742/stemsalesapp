<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ExpenseV28 Controller
 * FIXED 2026-06-06 audit_D: bd_summary now queries cash_expense (user_id) as primary
 * source because expense_line_items has only 1 row in staging. Fallback to
 * expense_line_items (bd_uid) for newly submitted items.
 *
 * Routes:
 *   GET  /api/expense/bd_summary?bd_uid=<uid>[&month=YYYY-MM]
 *   GET  /api/expense/probe
 *   POST /api/expense/submit
 *
 * Real tables:
 *   cash_expense: id, user_id, meetid, expense_type, expense, expense_remarks, verify,
 *                 admin_apr, account_apr, created_at
 *   expense_line_items: id, bd_uid, category_code, category_label, amount_rs, captured_at
 */
class ExpenseV28 extends CI_Controller {

    private $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->output->set_content_type('application/json');
    }

    private function auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || trim(str_replace('Bearer', '', $h)) !== $this->token) {
            $this->json_out(array('ok' => false, 'error' => 'unauthorized'), 401);
            return false;
        }
        return true;
    }

    private function json_out($data, $status = 200)
    {
        $this->output->set_status_header($status)
                     ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * GET /api/expense/bd_summary?bd_uid=<uid>[&month=YYYY-MM]
     *
     * FIX 2026-06-06: Queries cash_expense (primary, user_id col, 22432 rows) +
     * expense_line_items (secondary, bd_uid col). Returns merged category summary.
     * approval_label: 0=pending, 1=admin_approved, 2=account_approved.
     */
    public function bd_summary()
    {
        if (!$this->auth()) return;
        $bd_uid = (int) $this->input->get('bd_uid');
        $month  = $this->input->get('month');
        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }
        $month_start = $month . '-01';
        $month_end   = date('Y-m-t', strtotime($month_start));

        $rows = array();
        $total_rs = 0;

        if ($bd_uid > 0) {
            // --- PRIMARY: cash_expense (user_id = bd_uid) ---
            $ce_rows = $this->db->query(
                "SELECT
                    ce.id,
                    ce.user_id AS bd_uid,
                    COALESCE(NULLIF(ce.expense_type,''),'General') AS category_code,
                    COALESCE(NULLIF(ce.expense_type,''),'General') AS category_label,
                    ce.expense AS amount_rs,
                    ce.expense_remarks AS remarks,
                    CASE
                        WHEN ce.account_apr = 1 THEN 'account_approved'
                        WHEN ce.admin_apr = 1    THEN 'admin_approved'
                        ELSE 'pending'
                    END AS approval_state,
                    ce.verify,
                    DATE(ce.created_at) AS expense_date,
                    'cash_expense' AS source_table
                 FROM cash_expense ce
                 WHERE ce.user_id = ?
                   AND DATE(ce.created_at) BETWEEN ? AND ?
                 ORDER BY ce.created_at DESC
                 LIMIT 200",
                array($bd_uid, $month_start, $month_end)
            )->result_array();
            $rows = array_merge($rows, $ce_rows);

            // --- SECONDARY: expense_line_items (bd_uid col) ---
            $eli_rows = $this->db->query(
                "SELECT
                    eli.id,
                    eli.bd_uid,
                    eli.category_code,
                    eli.category_label,
                    eli.amount_rs,
                    eli.remarks,
                    COALESCE(eli.policy_status,'no_policy') AS approval_state,
                    0 AS verify,
                    DATE(eli.captured_at) AS expense_date,
                    'expense_line_items' AS source_table
                 FROM expense_line_items eli
                 WHERE eli.bd_uid = ?
                   AND DATE(eli.captured_at) BETWEEN ? AND ?
                 ORDER BY eli.captured_at DESC
                 LIMIT 200",
                array($bd_uid, $month_start, $month_end)
            )->result_array();
            $rows = array_merge($rows, $eli_rows);

            foreach ($rows as $r) {
                $total_rs += (int)$r['amount_rs'];
            }
        } else {
            // Team-wide: top BDs by total expense (cash_expense only)
            $rows = $this->db->query(
                "SELECT
                    ce.user_id AS bd_uid,
                    u.name AS bd_name,
                    SUM(ce.expense) AS total_rs,
                    COUNT(*) AS items,
                    SUM(CASE WHEN ce.admin_apr=1 THEN 1 ELSE 0 END) AS approved_count
                 FROM cash_expense ce
                 LEFT JOIN user u ON u.uid = ce.user_id
                 WHERE DATE(ce.created_at) BETWEEN ? AND ?
                 GROUP BY ce.user_id
                 ORDER BY total_rs DESC
                 LIMIT 50",
                array($month_start, $month_end)
            )->result_array();
            foreach ($rows as $r) { $total_rs += (int)$r['total_rs']; }
        }

        $this->json_out(array(
            'ok'       => true,
            'success'  => true,
            'month'    => $month,
            'bd_uid'   => $bd_uid ?: null,
            'total_rs' => $total_rs,
            'count'    => count($rows),
            'rows'     => $rows,
            'source'   => 'cash_expense+expense_line_items',
            'fix'      => 'audit_D_20260606',
        ));
    }

    /**
     * GET /api/expense/probe
     */
    public function probe()
    {
        $this->json_out(array('ok' => true, 'success' => true, 'note' => 'ExpenseV28 online'));
    }

    /**
     * POST /api/expense/submit
     * Body JSON: bd_uid, travel_advance_id, category_code, category_label, amount_rs,
     *            qty (opt), unit (opt), remarks (opt)
     */
    public function submit()
    {
        if (!$this->auth()) return;
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            $body = $this->input->post();
        }
        $bd_uid            = (int) ($body['bd_uid'] ?? 0);
        $travel_advance_id = (int) ($body['travel_advance_id'] ?? 0);
        $category_code     = trim($body['category_code'] ?? '');
        $category_label    = trim($body['category_label'] ?? $category_code);
        $amount_rs         = (int) ($body['amount_rs'] ?? 0);

        if ($bd_uid <= 0 || !$category_code || $amount_rs <= 0) {
            $this->json_out(array('ok' => false, 'error' => 'bd_uid, category_code, amount_rs required'), 400);
            return;
        }

        $insert = array(
            'travel_advance_id' => $travel_advance_id ?: null,
            'bd_uid'            => $bd_uid,
            'category_code'     => $category_code,
            'category_label'    => $category_label,
            'amount_rs'         => $amount_rs,
            'qty'               => (float) ($body['qty'] ?? 1.0),
            'unit'              => isset($body['unit']) ? $body['unit'] : null,
            'remarks'           => isset($body['remarks']) ? $body['remarks'] : null,
            'policy_status'     => 'no_policy',
            'captured_at'       => date('Y-m-d H:i:s'),
            'created_at'        => date('Y-m-d H:i:s'),
        );
        $this->db->insert('expense_line_items', $insert);
        $new_id = $this->db->insert_id();
        $this->json_out(array('ok' => true, 'success' => true, 'id' => $new_id, 'note' => 'expense submitted'));
    }
}
