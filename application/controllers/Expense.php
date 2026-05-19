<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ExpenseAccountability controller
 * Routes mounted under /api/discipline/expense/* and /api/discipline/cancel/*
 * (see application/config/routes_discipline.php)
 *
 * All endpoints respond with JSON: {ok, ...}
 * Auth: relies on session token set by Menu/login. type_id checked per endpoint.
 */
class ExpenseAccountability extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/ExpenseAccountability_model', 'eam');
        $this->load->model('AIAgents/SalesDiscipline_model', 'sd');
        $this->load->library('session');
        header('Content-Type: application/json');
    }

    private function _uid()
    {
        $u = $this->session->userdata('user');
        return $u['user_id'] ?? null;
    }

    private function _need_post()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            echo json_encode(['ok' => false, 'error' => 'POST_required']);
            exit;
        }
    }

    // ============================================================
    // CANCELLATION ENDPOINTS
    // ============================================================

    // POST /api/discipline/cancel/meeting
    // body: {event_id, reason, category, disposition, rolled_to_event_id?}
    public function cancel_meeting()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok' => false, 'error' => 'auth_required']); return; }

        $body = json_decode($this->input->raw_input_stream, true) ?: $_POST;
        $r = $this->eam->cancel_meeting(
            (int)($body['event_id'] ?? 0),
            $uid,
            $body['reason'] ?? '',
            $body['category'] ?? 'other',
            $body['disposition'] ?? 'pending_decision',
            $body['rolled_to_event_id'] ?? null
        );
        echo json_encode($r);
    }

    // GET /api/discipline/cancel/categories
    public function categories()
    {
        $rows = $this->db->query("SELECT * FROM cancellation_category_ref ORDER BY label")->result();
        echo json_encode(['ok' => true, 'categories' => $rows]);
    }

    // GET /api/discipline/cancel/audit?days=7
    public function cancel_audit()
    {
        $days = (int)($this->input->get('days') ?: 7);
        $rows = $this->db->query(
            "SELECT * FROM cancellation_audit
             WHERE cancelled_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY cancelled_at DESC LIMIT 50",
            [$days]
        )->result();
        echo json_encode(['ok' => true, 'rows' => $rows]);
    }

    // GET /api/discipline/cancel/unreturned_advances?days=7
    public function unreturned_advances()
    {
        $days = (int)($this->input->get('days') ?: 7);
        $rows = $this->eam->find_unreturned_advances($days);
        echo json_encode(['ok' => true, 'rows' => $rows]);
    }


    // ============================================================
    // EXPENSE SUBMISSION ENDPOINTS
    // ============================================================

    // POST /api/discipline/expense/submit
    // multipart: event_id, actual_cost, breakdown(json), receipt(file)
    public function submit_expense()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok' => false, 'error' => 'auth_required']); return; }

        $event_id = (int)$this->input->post('event_id');
        $actual   = (int)$this->input->post('actual_cost');
        $breakdown = json_decode($this->input->post('breakdown') ?: '[]', true);

        // Receipt upload
        $receipt_filename = '';
        if (!empty($_FILES['receipt']['name'])) {
            $upload_path = FCPATH . 'uploads/receipts/';
            if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);
            $ext  = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
            $name = "receipt_{$uid}_{$event_id}_" . time() . ".$ext";
            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $upload_path . $name)) {
                $receipt_filename = "uploads/receipts/$name";
            }
        }
        if (empty($receipt_filename)) {
            echo json_encode(['ok' => false, 'error' => 'receipt_required']);
            return;
        }

        $r = $this->eam->submit_actuals($event_id, $uid, $actual, $receipt_filename, $breakdown);
        echo json_encode($r);
    }

    // GET /api/discipline/expense/cm_queue
    public function cm_queue()
    {
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok' => false, 'error' => 'auth_required']); return; }
        echo json_encode(['ok' => true, 'rows' => $this->eam->get_cm_queue($uid)]);
    }

    // GET /api/discipline/expense/ao_queue
    public function ao_queue()
    {
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok' => false, 'error' => 'auth_required']); return; }
        $u = $this->db->query("SELECT type_id FROM user_details WHERE user_id=?", [$uid])->row();
        if (!$u || (int)$u->type_id !== 27) {
            echo json_encode(['ok' => false, 'error' => 'not_accounts_officer']);
            return;
        }
        echo json_encode(['ok' => true, 'rows' => $this->eam->get_ao_queue()]);
    }

    // POST /api/discipline/expense/cm_approve
    // body: {log_id, remarks}
    public function cm_approve()
    {
        $this->_need_post();
        $uid = $this->_uid();
        $body = json_decode($this->input->raw_input_stream, true) ?: $_POST;
        echo json_encode($this->eam->cm_approve_expense(
            (int)($body['log_id'] ?? 0), $uid, $body['remarks'] ?? ''
        ));
    }

    // POST /api/discipline/expense/ao_approve
    public function ao_approve()
    {
        $this->_need_post();
        $uid = $this->_uid();
        $body = json_decode($this->input->raw_input_stream, true) ?: $_POST;
        echo json_encode($this->eam->ao_approve_expense(
            (int)($body['log_id'] ?? 0), $uid, $body['remarks'] ?? ''
        ));
    }


    // ============================================================
    // PLAN-SUBMIT GATE (called by /api/discipline/submit_plan at 18:30)
    // ============================================================

    // GET /api/discipline/expense/gate_check?plan_date=YYYY-MM-DD
    public function gate_check()
    {
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok' => false, 'error' => 'auth_required']); return; }
        $plan_date = $this->input->get('plan_date') ?: null;
        echo json_encode($this->eam->check_plan_submit_gate($uid, $plan_date));
    }


    // ============================================================
    // DAILY SWEEP (called by 7:30 IST cron 0c647bbd)
    // ============================================================

    // GET /api/discipline/expense/sweep?days=7
    public function sweep()
    {
        $days = (int)($this->input->get('days') ?: 7);
        echo json_encode([
            'ok' => true,
            'stale_cash_allotments' => $this->eam->find_stale_cash_allotments($days),
            'unreturned_advances'   => $this->eam->find_unreturned_advances($days),
            'variance_breaches'     => $this->eam->find_variance_breaches(1),
        ]);
    }

    // ============================================================
    // ADVANCE MANAGEMENT (mounted at /api/discipline/advance/*)
    // ============================================================

    // POST /api/discipline/advance/request   {event_id, amount, purpose}
    public function request()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $b = json_decode($this->input->raw_input_stream, true) ?: $_POST;
        echo json_encode($this->eam->request_advance(
            $uid,
            (int)($b['event_id'] ?? 0),
            (float)($b['amount']   ?? 0),
            $b['purpose'] ?? ''
        ));
    }

    // POST /api/discipline/advance/approve   {advance_id, role, action, remarks}
    public function approve()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $b = json_decode($this->input->raw_input_stream, true) ?: $_POST;
        $role = $b['role'] ?? 'cluster';

        // Role guard: account role requires type_id=27 Accounts Officer.
        if ($role === 'account') {
            $u = $this->db->get_where('user', ['uid' => $uid])->row();
            if (!$u || (int)$u->type_id !== 27) {
                echo json_encode(['ok'=>false,'error'=>'not_accounts_officer']);
                return;
            }
        }
        echo json_encode($this->eam->approve_advance(
            (int)($b['advance_id'] ?? 0),
            $uid,
            $role,
            (int)($b['action'] ?? 1),
            $b['remarks'] ?? ''
        ));
    }

    // POST /api/discipline/advance/consume   {advance_id, actual_spent}
    public function consume()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $b = json_decode($this->input->raw_input_stream, true) ?: $_POST;
        echo json_encode($this->eam->mark_advance_consumed(
            (int)($b['advance_id'] ?? 0), $uid, (float)($b['actual_spent'] ?? 0)
        ));
    }

    // POST /api/discipline/advance/return    {advance_id, reason}
    public function return_full()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $b = json_decode($this->input->raw_input_stream, true) ?: $_POST;
        echo json_encode($this->eam->return_advance(
            (int)($b['advance_id'] ?? 0), $uid, $b['reason'] ?? ''
        ));
    }

    // GET /api/discipline/advance/my?status=all&days=30
    public function my_advances()
    {
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $status = $this->input->get('status') ?: 'all';
        $days   = (int)($this->input->get('days') ?: 30);
        echo json_encode(['ok'=>true, 'rows' => $this->eam->list_my_advances($uid, $status, $days)]);
    }

    // GET /api/discipline/advance/queue?role=cluster|admin|account
    public function queue()
    {
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $role = $this->input->get('role') ?: 'cluster';
        echo json_encode(['ok'=>true, 'rows' => $this->eam->list_approval_queue($role)]);
    }

    // ============================================================
    // BATCHED EXPENSE SUBMIT (production parity)
    // Mirrors Menu::AddCashSpentInMeetings on stemapp.in
    // ============================================================

    // GET /api/discipline/expense/pending_meetings
    // Returns today's closed meetings that still need expense submission for this BD.
    public function pending_meetings()
    {
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $rows = $this->eam->get_pending_meetings_for_today($uid);
        echo json_encode(['ok' => true, 'meetings' => $rows]);
    }

    // POST /api/discipline/expense/submit_batch
    // multipart fields (arrays):
    //   meetingid[]            mom_data.id per meeting
    //   expensecash[]          integer rupees per meeting
    //   expense_remarks[]      per-meeting remarks
    //   travel_expense_type    comma-joined string (Cab,Toll,Fuel,...)
    //   images1[], images2[]   one file bucket per meeting (1-indexed), images + pdf
    public function submit_batch()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok' => false, 'error' => 'auth_required']); return; }

        $meetids = $this->input->post('meetingid') ?: [];
        $amounts = $this->input->post('expensecash') ?: [];
        $remarks = $this->input->post('expense_remarks') ?: [];
        $travel  = $this->input->post('travel_expense_type') ?: '';

        if (!is_array($meetids) || empty($meetids)) {
            echo json_encode(['ok' => false, 'error' => 'no_meetings']); return;
        }
        if (empty($travel)) {
            echo json_encode(['ok' => false, 'error' => 'travel_expense_type_required']); return;
        }

        $upload_path = FCPATH . 'uploads/receipts/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

        $rows = [];
        foreach ($meetids as $idx => $meetid) {
            $meetid_int = (int)$meetid;
            $amt   = isset($amounts[$idx]) ? (int)$amounts[$idx] : 0;
            $rem   = isset($remarks[$idx]) ? trim($remarks[$idx]) : '';

            // Upload bills for images{idx+1}[]
            $bucket = 'images' . ($idx + 1);
            $saved = [];
            if (!empty($_FILES[$bucket]) && is_array($_FILES[$bucket]['name'])) {
                $count = count($_FILES[$bucket]['name']);
                for ($i = 0; $i < $count; $i++) {
                    if ($_FILES[$bucket]['error'][$i] !== UPLOAD_ERR_OK) continue;
                    $orig = $_FILES[$bucket]['name'][$i];
                    $ext  = pathinfo($orig, PATHINFO_EXTENSION);
                    $name = "bill_{$uid}_{$meetid_int}_" . time() . "_$i.$ext";
                    if (move_uploaded_file($_FILES[$bucket]['tmp_name'][$i], $upload_path . $name)) {
                        $saved[] = "uploads/receipts/$name";
                    }
                }
            }

            $rows[] = [
                'meetid'     => $meetid_int,
                'expense'    => $amt,
                'remarks'    => $rem,
                'bills_json' => json_encode($saved),
            ];
        }

        $r = $this->eam->submit_actuals_batch($uid, $rows, $travel);
        echo json_encode($r);
    }

    // ============================================================
    // ADVANCE SETTLEMENT (BD submits actual spend against a disbursed advance)
    // ============================================================

    // GET /api/discipline/advance/unsettled
    // Returns disbursed advances awaiting actual-spend reconciliation.
    public function unsettled()
    {
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }
        $rows = $this->eam->list_disbursed_unsettled_advances($uid);
        echo json_encode(['ok' => true, 'advances' => $rows]);
    }

    // POST /api/discipline/advance/settle  (multipart)
    // Fields: advance_id, actual_spent, expense_remarks, travel_expense_type, bills[]
    public function settle()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if (!$uid) { echo json_encode(['ok' => false, 'error' => 'auth_required']); return; }

        $advance_id     = (int)$this->input->post('advance_id');
        $actual_spent   = (int)$this->input->post('actual_spent');
        $expense_remarks= trim($this->input->post('expense_remarks') ?: '');
        $travel_type    = trim($this->input->post('travel_expense_type') ?: '');

        if ($advance_id <= 0)       { echo json_encode(['ok'=>false,'error'=>'advance_id_required']); return; }
        if ($actual_spent < 0)      { echo json_encode(['ok'=>false,'error'=>'negative_amount']); return; }
        if (empty($travel_type))    { echo json_encode(['ok'=>false,'error'=>'travel_expense_type_required']); return; }

        $upload_path = FCPATH . 'uploads/receipts/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

        $saved = [];
        if (!empty($_FILES['bills']) && is_array($_FILES['bills']['name'])) {
            $count = count($_FILES['bills']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['bills']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $orig = $_FILES['bills']['name'][$i];
                $ext  = pathinfo($orig, PATHINFO_EXTENSION);
                $name = "settle_{$uid}_{$advance_id}_" . time() . "_$i.$ext";
                if (move_uploaded_file($_FILES['bills']['tmp_name'][$i], $upload_path . $name)) {
                    $saved[] = "uploads/receipts/$name";
                }
            }
        } elseif (!empty($_FILES['bills']['name'])) {
            // single-file fallback
            $orig = $_FILES['bills']['name'];
            $ext  = pathinfo($orig, PATHINFO_EXTENSION);
            $name = "settle_{$uid}_{$advance_id}_" . time() . ".$ext";
            if (move_uploaded_file($_FILES['bills']['tmp_name'], $upload_path . $name)) {
                $saved[] = "uploads/receipts/$name";
            }
        }

        $r = $this->eam->settle_advance(
            $advance_id, $uid, $actual_spent, json_encode($saved),
            $expense_remarks, $travel_type
        );
        echo json_encode($r);
    }
}
