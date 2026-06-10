<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M073Expense - Concur-class Advance Settlement endpoints
 * Dedicated CI3 controller (filename == class name).
 * All endpoints respond with JSON: {ok, ...}
 * Bearer-token gated via STEM_DIGEST_TOKEN or session uid.
 */
class M073Expense extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/Stem_expense_model', 'sem');
        $this->load->library('session');
        header('Content-Type: application/json');
    }

    private function _bearer_ok()
    {
        $hdr = $this->input->get_request_header('Authorization', true);
        if (!$hdr) return false;
        if (stripos($hdr, 'Bearer ') !== 0) return false;
        $tok = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (!$expected) $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        return hash_equals($expected, $tok);
    }

    private function _uid()
    {
        // Prefer session uid, then header X-Actor-Uid, then bearer-token fallback (uid=0 = system)
        $sess = $this->session->userdata('uid');
        if ($sess) return (int)$sess;
        $hdr_uid = $this->input->get_request_header('X-Actor-Uid', true);
        if ($hdr_uid) return (int)$hdr_uid;
        if ($this->_bearer_ok()) return 0; // system caller
        return null;
    }

    private function _need_post()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            echo json_encode(['ok' => false, 'error' => 'post_required']);
            exit;
        }
    }

    // GET /api/discipline/policy/categories
    public function policy_categories()
    {
        if (!$this->_bearer_ok() && !$this->_uid()) {
            echo json_encode(['ok'=>false,'error'=>'auth_required']); return;
        }
        $rows = $this->sem->get_expense_policies();
        echo json_encode(['ok' => true, 'categories' => $rows]);
    }

    // POST /api/discipline/advance/settle_v2 (JSON body or form items_json)
    public function settle_v2()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if ($uid === null) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }

        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);
        if (!is_array($body)) {
            $items_json   = $this->input->post('items_json');
            $mileage_json = $this->input->post('mileage_json');
            $body = [
                'advance_id'      => (int)$this->input->post('advance_id'),
                'items'           => $items_json   ? json_decode($items_json, true)   : [],
                'mileage'         => $mileage_json ? json_decode($mileage_json, true) : [],
                'expense_remarks' => (string)$this->input->post('expense_remarks'),
                'bd_uid'          => (int)$this->input->post('bd_uid'),
            ];
        }

        $advance_id = (int)($body['advance_id'] ?? 0);
        $bd_uid     = (int)($body['bd_uid']     ?? $uid);
        if ($bd_uid <= 0) $bd_uid = $uid ?: 0;
        $items      = is_array($body['items']    ?? null) ? $body['items']    : [];
        $mileage    = is_array($body['mileage']  ?? null) ? $body['mileage']  : [];
        $remarks    = (string)($body['expense_remarks'] ?? '');

        if ($advance_id <= 0) { echo json_encode(['ok'=>false,'error'=>'advance_id_required']); return; }
        if (empty($items))    { echo json_encode(['ok'=>false,'error'=>'items_required']); return; }

        $r = $this->sem->settle_advance_v2($advance_id, $bd_uid, $items, $mileage, $remarks);
        echo json_encode($r);
    }

    // POST /api/discipline/receipt/ocr_scan
    public function ocr_scan()
    {
        $this->_need_post();
        $uid = $this->_uid();
        if ($uid === null) { echo json_encode(['ok'=>false,'error'=>'auth_required']); return; }

        $filename = (string)$this->input->post('receipt_filename');
        $adv_id   = (int)$this->input->post('travel_advance_id');
        if (empty($filename)) { echo json_encode(['ok'=>false,'error'=>'receipt_filename_required']); return; }

        $r = $this->sem->ocr_scan_receipt($filename, $uid, $adv_id ?: null);
        echo json_encode($r);
    }

    // GET /api/discipline/accounting/sync_pending
    public function sync_pending()
    {
        if (!$this->_bearer_ok() && !$this->_uid()) {
            echo json_encode(['ok'=>false,'error'=>'auth_required']); return;
        }
        $rows = $this->sem->get_accounting_sync_pending(100);
        echo json_encode(['ok' => true, 'pending' => $rows]);
    }

    // POST /api/discipline/accounting/sync_retry
    public function sync_retry()
    {
        $this->_need_post();
        if (!$this->_bearer_ok() && !$this->_uid()) {
            echo json_encode(['ok'=>false,'error'=>'auth_required']); return;
        }
        $qid = (int)$this->input->post('queue_id');
        if ($qid <= 0) { echo json_encode(['ok'=>false,'error'=>'queue_id_required']); return; }
        echo json_encode($this->sem->retry_accounting_sync($qid));
    }
}
