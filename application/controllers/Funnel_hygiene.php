<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Funnel_hygiene - M024
 * /api/funnel_hygiene/dm_verify_queue  - leads at cstatus 6+ awaiting DM contact verification
 * /api/funnel_hygiene/probe            - liveness check
 */
class Funnel_hygiene extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
        $this->_rp_guard();
    }

    // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This controller
    // returned live business data with NO token check (fail-open). Allow only
    // liveness/probe methods; require a valid digest OR per-user login token for
    // every data method via the shared authunify_ok(). Additive: valid callers
    // unchanged; only missing/garbage tokens are now rejected.
    private $_rp_public = array('probe', 'status');
    private function _rp_guard() {
        $m = $this->router->fetch_method();
        if (in_array($m, $this->_rp_public, true)) { return; }
        if (substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }


    private function _out($p) { echo json_encode($p); exit; }

    // GET /api/funnel_hygiene/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'Funnel_hygiene',
            'migration'   => 'M024',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }

    // GET /api/funnel_hygiene/dm_verify_queue
    // Returns leads at cstatus 6-9 that have an apst (appointment status) set,
    // meaning they are awaiting DM contact verification.
    public function dm_verify_queue() {
        try {
            $rows = $this->db->query(
                "SELECT ic.id AS lead_id,
                        ic.mainbd,
                        ic.cstatus,
                        ic.apst,
                        ic.createDate,
                        ic.proposal_amt,
                        u.name AS bd_name,
                        cm.compname AS school
                 FROM init_call ic
                 LEFT JOIN user_details u  ON u.user_id = ic.mainbd
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE ic.cstatus IN (6,7,8,9)
                   AND ic.apst IS NOT NULL
                 ORDER BY ic.id DESC
                 LIMIT 50"
            )->result_array();

            $this->_out([
                'ok'    => true,
                'rows'  => $rows,
                'count' => count($rows),
            ]);
        } catch (Exception $e) {
            $this->_out([
                'ok'     => true,
                'rows'   => [],
                'note'   => 'error',
                'detail' => $e->getMessage(),
            ]);
        }
    }

    // rimlyproof_hygiene_v2_20260608
    // POST /api/funnel_hygiene/resolve  - mark a dm_verification row resolved with a verdict.
    // params (POST): id (int, required), verdict in [verified,doubtful,not_csr] (required), reason (optional), by_uid (optional)
    public function resolve() {
        if (strtolower($this->input->method()) !== 'post') { $this->_out(['ok'=>false,'error'=>'post_only']); }
        $id      = (int)$this->input->post('id');
        $verdict = trim((string)$this->input->post('verdict'));
        $reason  = (string)$this->input->post('reason');
        $by_uid  = (int)$this->input->post('by_uid');
        $allowed = ['verified','doubtful','not_csr'];
        if ($id <= 0 || !in_array($verdict, $allowed, true)) {
            $this->_out(['ok'=>false,'error'=>'invalid_input','allowed_verdict'=>$allowed]);
        }
        // only update rows that actually exist; never error a real user with 5xx
        $exists = $this->db->query('SELECT id FROM dm_verification WHERE id = ? LIMIT 1', [$id])->row();
        if (!$exists) { $this->_out(['ok'=>false,'error'=>'not_found','id'=>$id]); }
        $this->db->query(
            'UPDATE dm_verification SET verdict = ?, verdict_reason = ?, verdict_at = NOW(), verdict_by = ? WHERE id = ?',
            [$verdict, $reason, ($by_uid > 0 ? ('uid:'.$by_uid) : 'manual'), $id]
        );
        $this->_out(['ok'=>true,'id'=>$id,'verdict'=>$verdict,'by_uid'=>$by_uid]);
    }
}
