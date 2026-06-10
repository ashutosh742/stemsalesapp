<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Closure pipeline summary (sno 58).
 * Sum fbudget at cstatus 6/8/9 (Positive / Open RPEM / Very Positive).
 */
class ClosurePipelineV28 extends CI_Controller {
    public function __construct() {
        parent::__construct();
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


    public function probe() {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'controller' => 'ClosurePipelineV28']));
    }

    public function pipeline_summary() {
        $this->output->set_content_type('application/json');
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return;
        }
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);

        $sql = "SELECT
                    ic.id   AS cid_id,
                    cm.compname AS company_name,
                    ic.cstatus,
                    ic.fbudget,
                    ic.mainbd AS bd_uid,
                    u.name    AS bd_name,
                    ic.createDate,
                    ic.updated_at
                FROM init_call ic
                LEFT JOIN company_master cm ON cm.id  = ic.cmpid_id
                LEFT JOIN user           u  ON u.uid = ic.mainbd
                WHERE ic.cstatus IN (6, 8, 9) ";
        $bind = [];
        if ($bd_uid > 0) { $sql .= " AND ic.mainbd = ? "; $bind[] = $bd_uid; }
        $sql .= " ORDER BY ic.updated_at DESC LIMIT 500";

        $q = $this->db->query($sql, $bind);
        $rows = $q ? $q->result_array() : [];

        // Aggregate counts and budget totals by stage
        $by_stage = [];
        foreach ($rows as $r) {
            $s = (int) $r['cstatus'];
            if (!isset($by_stage[$s])) $by_stage[$s] = ['cnt' => 0, 'budget_sum' => 0.0];
            $by_stage[$s]['cnt']++;
            $by_stage[$s]['budget_sum'] += (float) preg_replace('/[^0-9.]/', '', (string)$r['fbudget']);
        }

        echo json_encode([
            'ok'        => true,
            'rows'      => $rows,
            'by_stage'  => $by_stage,
            'total_cnt' => count($rows),
        ]);
    }
}
