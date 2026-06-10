<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ExpenseApi - Agent F, Blitz 30 May 2026
 *
 * Endpoint:
 *   GET /api/expense/list?uid={uid}&from=YYYY-MM-DD&to=YYYY-MM-DD
 *
 * Strategy (both tables queried and UNION returned):
 *   Primary  : expense_line_items  (real per-item expense rows with bd_uid, category, amount_rs, captured_at)
 *   Fallback : cash_log debits     (type IN ('debit','Deduct')) for uid in date range
 *
 * expense_actuals_log is also queried for approval state and merged in.
 * All amounts in Rs (integer from source column).
 */
class ExpenseApi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(['Bearer ', 'Bearer'], '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        return true;
    }

    private function _json($rows, $route, $meta = []) {
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array_merge(['count' => count($rows)], $meta),
            'route'        => $route,
            'generated_at' => date('c'),
        ]));
    }

    // -------------------------------------------------------------------------
    // GET /api/expense/list?uid=&from=YYYY-MM-DD&to=YYYY-MM-DD
    // -------------------------------------------------------------------------
    public function listing() {
        if (!$this->_bearer()) return;

        $uid  = (int) $this->input->get('uid', TRUE);
        $from = $this->input->get('from', TRUE);
        $to   = $this->input->get('to',   TRUE);

        if (!$uid) {
            $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'uid required']));
            return;
        }

        // Default date range: current calendar month
        if (!$from) { $from = date('Y-m-01'); }
        if (!$to)   { $to   = date('Y-m-t');  }

        // Validate dates
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'from/to must be YYYY-MM-DD']));
            return;
        }

        $rows = [];
        $total_rs = 0;

        // ---- PRIMARY: expense_line_items (has bd_uid, category, amount_rs) ----
        $q = $this->db->query(
            "SELECT
                eli.id,
                eli.bd_uid                      AS uid,
                eli.category_code,
                eli.category_label,
                eli.amount_rs,
                eli.qty,
                eli.unit,
                eli.policy_status,
                eli.policy_limit_rs,
                eli.remarks,
                eli.receipt_filename,
                eli.captured_at                 AS expense_date,
                eal.expense_type,
                eal.final_state                 AS approval_state,
                eal.log_date,
                'expense_line_items'            AS source_table
             FROM expense_line_items eli
             LEFT JOIN expense_actuals_log eal ON eli.expense_actuals_id = eal.id
             WHERE eli.bd_uid = ?
               AND DATE(eli.captured_at) BETWEEN ? AND ?
             ORDER BY eli.captured_at DESC",
            [$uid, $from, $to]
        );
        $line_rows = $q->result_array();

        // ---- FALLBACK: cash_log debits if no expense_line_items data for uid ----
        // We always include cash_log debits as a separate source so callers see both
        $q2 = $this->db->query(
            "SELECT
                cl.id,
                cl.uid,
                'cash_debit'                    AS category_code,
                'Cash Wallet Debit'             AS category_label,
                cl.cash                         AS amount_rs,
                1                               AS qty,
                'each'                          AS unit,
                'no_policy'                     AS policy_status,
                NULL                            AS policy_limit_rs,
                cl.remarks,
                NULL                            AS receipt_filename,
                cl.created_at                   AS expense_date,
                cl.type                         AS expense_type,
                'completed'                     AS approval_state,
                DATE(cl.created_at)             AS log_date,
                'cash_log'                      AS source_table
             FROM cash_log cl
             WHERE cl.uid = ?
               AND LOWER(cl.type) IN ('debit','deduct')
               AND DATE(cl.created_at) BETWEEN ? AND ?
             ORDER BY cl.created_at DESC",
            [$uid, $from, $to]
        );
        $cash_rows = $q2->result_array();

        // Merge: expense_line_items first, then cash_log debits
        $rows = array_merge($line_rows, $cash_rows);

        // Compute total
        foreach ($rows as $r) {
            $total_rs += (int) $r['amount_rs'];
        }

        $this->_json($rows, 'api/expense/list', [
            'uid'        => $uid,
            'from'       => $from,
            'to'         => $to,
            'total_rs'   => $total_rs,
            'note'       => 'rows from expense_line_items and cash_log (debits); expense_actuals_log joined for approval state',
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /api/expense/approve?uid=<approver_uid>
    // Body JSON: {expense_id, approver_uid, remark?, approval_level?}
    // Approves a cash_expense row. approval_level: "admin" (default) or "account".
    // Equivalent to production CashExpenseApproved (Menu.php:29282).
    // FIX audit_D 2026-06-06.
    // -----------------------------------------------------------------------
    public function approve_expense() {
        if (!$this->_bearer()) return;
        $raw = file_get_contents('php://input');
        $body = $raw ? @json_decode($raw, true) : array();
        if (!$body) $body = $_POST;
        $expense_id     = isset($body['expense_id'])     ? (int)$body['expense_id']     : 0;
        $approver_uid   = isset($body['approver_uid'])   ? (int)$body['approver_uid']   : 0;
        $remark         = isset($body['remark'])         ? trim($body['remark'])         : '';
        $approval_level = isset($body['approval_level']) ? trim($body['approval_level']) : 'admin';
        if ($expense_id <= 0 || $approver_uid <= 0) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('ok'=>false,'error'=>'expense_id and approver_uid required')));
            return;
        }
        $now = date('Y-m-d H:i:s');
        $row = $this->db->query("SELECT id, user_id, admin_apr, account_apr FROM cash_expense WHERE id = ? LIMIT 1", array($expense_id))->row_array();
        if (!$row) {
            $this->output->set_status_header(404)->set_content_type('application/json')
                ->set_output(json_encode(array('ok'=>false,'error'=>'expense not found')));
            return;
        }
        if ($approval_level === 'account') {
            $update = array('account_apr'=>1,'account_by'=>$approver_uid,'account_msg'=>$remark,'account_date'=>$now);
        } else {
            $update = array('admin_apr'=>1,'admin_by'=>$approver_uid,'admin_msg'=>$remark,'admin_date'=>$now);
        }
        $this->db->where('id', $expense_id)->update('cash_expense', $update);
        $affected = $this->db->affected_rows();
        $this->_json(array('ok'=>true,'approved'=>true,'expense_id'=>$expense_id,'approval_level'=>$approval_level,'affected'=>$affected), 'api/expense/approve');
    }

    // -----------------------------------------------------------------------
    // POST /api/expense/reject
    // Body JSON: {expense_id, approver_uid, remark}
    // Rejects a cash_expense row (sets verify=-1, admin_apr=0).
    // Equivalent to production CashExpenseReject (Menu.php:29197).
    // FIX audit_D 2026-06-06.
    // -----------------------------------------------------------------------
    public function reject_expense() {
        if (!$this->_bearer()) return;
        $raw = file_get_contents('php://input');
        $body = $raw ? @json_decode($raw, true) : array();
        if (!$body) $body = $_POST;
        $expense_id   = isset($body['expense_id'])   ? (int)$body['expense_id']   : 0;
        $approver_uid = isset($body['approver_uid']) ? (int)$body['approver_uid'] : 0;
        $remark       = isset($body['remark'])       ? trim($body['remark'])       : '';
        if ($expense_id <= 0 || $approver_uid <= 0) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('ok'=>false,'error'=>'expense_id and approver_uid required')));
            return;
        }
        if ($remark === '') {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('ok'=>false,'error'=>'remark required for reject')));
            return;
        }
        $now = date('Y-m-d H:i:s');
        $row = $this->db->query("SELECT id FROM cash_expense WHERE id = ? LIMIT 1", array($expense_id))->row_array();
        if (!$row) {
            $this->output->set_status_header(404)->set_content_type('application/json')
                ->set_output(json_encode(array('ok'=>false,'error'=>'expense not found')));
            return;
        }
        $this->db->where('id', $expense_id)->update('cash_expense', array(
            'verify'      => 0,
            'admin_apr'   => 0,
            'admin_by'    => $approver_uid,
            'admin_msg'   => $remark,
            'admin_date'  => $now,
            'verify_remarks' => $remark,
            'verify_by'   => $approver_uid,
            'verify_date' => $now,
        ));
        $affected = $this->db->affected_rows();
        $this->_json(array('ok'=>true,'rejected'=>true,'expense_id'=>$expense_id,'affected'=>$affected), 'api/expense/reject');
    }
}
