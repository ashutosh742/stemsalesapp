<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Live Day Check + Live Review Check.
 * sno 99 (Live Review Check), sno 102 (Live Day Check)
 *
 * Live Day Check  : show today's tblcallevents per BD that are planned but not completed.
 * Live Review Check: show today's reviews in allreview that have not closed yet.
 */
class LiveCheckV28 extends CI_Controller {
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
            ->set_output(json_encode(['ok' => true, 'controller' => 'LiveCheckV28']));
    }

    public function live_day_check() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return;
        }
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        $today  = date('Y-m-d');

        $sql = "SELECT
                    t.id              AS task_id,
                    t.user_id         AS bd_uid,
                    u.name            AS bd_name,
                    t.cid_id,
                    cm.compname       AS company_name,
                    t.actiontype_id,
                    t.purpose_id,
                    t.date            AS task_date,
                    t.plan_time,
                    t.initiate_time,
                    t.update_time,
                    t.complete_time,
                    t.status_id
                FROM tblcallevents t
                LEFT JOIN user            u  ON u.uid = t.user_id
                LEFT JOIN init_call       ic ON ic.id = t.cid_id
                LEFT JOIN company_master  cm ON cm.id = ic.cmpid_id
                WHERE DATE(t.date) = ?
                  AND t.complete_time IS NULL ";
        $bind = [$today];
        if ($bd_uid > 0) { $sql .= " AND t.user_id = ?"; $bind[] = $bd_uid; }
        $sql .= " ORDER BY t.date ASC LIMIT 500";

        $q = $this->db->query($sql, $bind);
        echo json_encode([
            'ok' => true,
            'as_of' => date('c'),
            'rows'  => $q ? $q->result_array() : [],
        ]);
    }

    public function live_review_check() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return;
        }
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        $today  = date('Y-m-d');

        $sql = "SELECT
                    r.id              AS review_id,
                    r.uid             AS reviewer_uid,
                    u.name            AS reviewer_name,
                    r.bdid            AS bd_uid,
                    r.reviewtype,
                    r.sdatet          AS schedule_dt,
                    r.startt          AS started_at,
                    r.closet          AS closed_at,
                    r.review_close_time
                FROM allreview r
                LEFT JOIN user u ON u.uid = r.uid
                WHERE DATE(r.sdatet) = ?
                  AND r.closet IS NULL ";
        $bind = [$today];
        if ($bd_uid > 0) { $sql .= " AND r.bdid = ?"; $bind[] = $bd_uid; }
        $sql .= " ORDER BY r.sdatet ASC LIMIT 500";

        $q = $this->db->query($sql, $bind);
        echo json_encode([
            'ok' => true,
            'as_of' => date('c'),
            'rows'  => $q ? $q->result_array() : [],
        ]);
    }
}
