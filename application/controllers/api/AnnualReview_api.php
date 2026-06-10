<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/AnnualReview_api.php
 *
 * Mobile API for the Annual Review approval workflow (manager review of
 * per-company annual reviews submitted by BDs).
 *
 * Backs the web flow that uses table annual_main_review (amr):
 *   - amr.inid           -> init_call.id (the company/lead record)
 *   - init_call.cmpid_id -> company_master.id (company name)
 *   - amr.by_uid         -> reviewer user id
 *   - amr.review_apr_status: 0 = pending, 1 = approved, 2 = rejected
 *
 * Endpoints (all Bearer protected):
 *   GET  api/annual_review/probe
 *   GET  api/annual_review/pending?reviewer_uid=<uid>&fy=<2025-26>&limit=50
 *   GET  api/annual_review/detail?id=<amr_id>
 *   POST api/annual_review/approve   body: {id, by_uid, remarks?}
 *   POST api/annual_review/reject    body: {id, by_uid, remarks}
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * ASCII only. No em-dashes. Rs not currency symbol.
 * Reads params via $_GET / php://input directly (CI3 input quirk on this server).
 * STAGING ONLY. Additive. Does NOT touch production.
 */
class AnnualReview_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
        $this->load->library('BearerAuth');
    }

    private function _bearer_ok() {
        $auth = $this->bearerauth->resolve();
        return !empty($auth['ok']);
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function _body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $j = json_decode($raw, true);
            if (is_array($j)) return $j;
        }
        return $_POST;
    }

    /** GET api/annual_review/probe */
    public function probe() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        $total   = (int)$this->db->query("SELECT COUNT(*) c FROM annual_main_review")->row()->c;
        $pending = (int)$this->db->query("SELECT COUNT(*) c FROM annual_main_review WHERE review_apr_status = 0")->row()->c;
        $approved = (int)$this->db->query("SELECT COUNT(*) c FROM annual_main_review WHERE review_apr_status = 1")->row()->c;
        $rejected = (int)$this->db->query("SELECT COUNT(*) c FROM annual_main_review WHERE review_apr_status = 2")->row()->c;
        $this->_json(array(
            'ok'              => true,
            'feature'         => 'annual_review',
            'table'           => 'annual_main_review',
            'total_reviews'   => $total,
            'pending_reviews' => $pending,
            'approved_reviews'=> $approved,
            'rejected_reviews'=> $rejected,
            'statuses'        => array('0' => 'pending', '1' => 'approved', '2' => 'rejected_redo')
        ));
    }

    /**
     * GET api/annual_review/pending?reviewer_uid=<uid>&fy=<2025-26>&limit=50
     * Lists annual reviews awaiting approval.
     * reviewer_uid optional (filters by amr.by_uid).
     * fy optional (filters by amr.financial_year).
     */
    public function pending() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);

        $reviewer  = isset($_GET['reviewer_uid']) ? (int)$_GET['reviewer_uid'] : 0;
        $fy        = isset($_GET['fy']) && $_GET['fy'] !== '' ? trim($_GET['fy']) : '';
        $limit_raw = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $limit     = ($limit_raw > 0 && $limit_raw <= 200) ? $limit_raw : 50;

        $where = "amr.review_apr_status = 0";
        if ($reviewer > 0) $where .= " AND amr.by_uid = " . (int)$reviewer;
        if ($fy !== '') $where .= " AND amr.financial_year = " . $this->db->escape($fy);

        $sql = "SELECT amr.id, amr.inid, amr.by_uid, amr.financial_year, amr.sdate,
                       amr.review_apr_status, amr.keep_company, amr.annaul_revenue,
                       amr.current_year_focus_funnel, amr.remarks,
                       ic.cmpid_id AS company_id, cm.compname AS company_name,
                       cm.city, cm.state, u.name AS reviewer_name
                FROM annual_main_review amr
                LEFT JOIN init_call ic ON ic.id = amr.inid
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN user_details u ON u.user_id = amr.by_uid
                WHERE $where
                ORDER BY amr.sdate DESC, amr.id DESC
                LIMIT $limit";

        $rows = $this->db->query($sql)->result_array();
        if (empty($rows)) {
            $this->_json(array('ok' => true, 'count' => 0, 'reason' => 'no_rows', 'reviews' => array()));
        }
        $this->_json(array('ok' => true, 'count' => count($rows), 'reviews' => $rows));
    }

    /** GET api/annual_review/detail?id=<amr_id> */
    public function detail() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) $this->_json(array('ok' => false, 'error' => 'id param required'), 400);

        $sql = "SELECT amr.*, ic.cmpid_id AS company_id, cm.compname AS company_name,
                       cm.city, cm.state, cm.district, u.name AS reviewer_name
                FROM annual_main_review amr
                LEFT JOIN init_call ic ON ic.id = amr.inid
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN user_details u ON u.user_id = amr.by_uid
                WHERE amr.id = " . (int)$id . " LIMIT 1";
        $row = $this->db->query($sql)->row_array();
        if (!$row) $this->_json(array('ok' => false, 'error' => 'not_found', 'reason' => 'no_rows'), 404);
        $this->_json(array('ok' => true, 'review' => $row));
    }

    /** POST api/annual_review/approve  body {id, by_uid, remarks?} */
    public function approve() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        $b = $this->_body();
        $id      = isset($b['id'])     ? (int)$b['id']     : 0;
        $by_uid  = isset($b['by_uid']) ? (int)$b['by_uid'] : 0;
        $remarks = isset($b['remarks']) ? trim($b['remarks']) : '';
        if ($id <= 0 || $by_uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'id and by_uid required'), 400);
        }

        $cur = $this->db->query(
            "SELECT id, review_apr_status FROM annual_main_review WHERE id = " . (int)$id . " LIMIT 1"
        )->row();
        if (!$cur) $this->_json(array('ok' => false, 'error' => 'not_found'), 404);

        $this->db->where('id', $id)->update('annual_main_review', array(
            'review_apr_status'  => 1,
            'review_apr_by'      => $by_uid,
            'review_apr_remarks' => $remarks
        ));
        $this->_json(array('ok' => true, 'id' => $id, 'new_status' => 1, 'label' => 'approved'));
    }

    /** POST api/annual_review/reject  body {id, by_uid, remarks} */
    public function reject() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        $b = $this->_body();
        $id      = isset($b['id'])     ? (int)$b['id']     : 0;
        $by_uid  = isset($b['by_uid']) ? (int)$b['by_uid'] : 0;
        $remarks = isset($b['remarks']) ? trim($b['remarks']) : '';
        if ($id <= 0 || $by_uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'id and by_uid required'), 400);
        }
        if ($remarks === '') {
            $this->_json(array('ok' => false, 'error' => 'remarks required for reject'), 400);
        }

        $cur = $this->db->query(
            "SELECT id, review_apr_status FROM annual_main_review WHERE id = " . (int)$id . " LIMIT 1"
        )->row();
        if (!$cur) $this->_json(array('ok' => false, 'error' => 'not_found'), 404);

        $this->db->where('id', $id)->update('annual_main_review', array(
            'review_apr_status'  => 2,
            'review_apr_by'      => $by_uid,
            'review_apr_remarks' => $remarks
        ));
        $this->_json(array('ok' => true, 'id' => $id, 'new_status' => 2, 'label' => 'rejected_redo'));
    }

    /**
     * POST api/annual_review/start
     * Body: {user_id}
     * Inserts a row into startannualreview to mark that a BD has started their annual review.
     * Idempotent: if already started this calendar year, returns existing record.
     * FIX audit_D 2026-06-06: missing startAnnualReview equivalent from production.
     */
    public function start() {
        if (!$this->_bearer_ok()) $this->_json(array("ok" => false, "error" => "Unauthorized"), 401);
        $b = $this->_body();
        $user_id = isset($b["user_id"]) ? (int)$b["user_id"] : 0;
        if ($user_id <= 0) {
            $this->_json(array("ok" => false, "error" => "user_id required"), 400);
        }
        $current_year = date("Y");
        $existing = $this->db->query(
            "SELECT id, start, createdat FROM startannualreview WHERE user_id = ? AND YEAR(start) = ? LIMIT 1",
            array($user_id, $current_year)
        )->row_array();
        if ($existing) {
            $this->_json(array(
                "ok"       => true,
                "started"  => true,
                "existing" => true,
                "id"       => $existing["id"],
                "start"    => $existing["start"],
                "message"  => "Annual review already started for this year",
            ));
        }
        $now = date("Y-m-d H:i:s");
        $this->db->insert("startannualreview", array(
            "start"     => date("Y-m-d"),
            "user_id"   => $user_id,
            "createdat" => $now,
            "updatedat" => $now,
        ));
        $new_id = (int)$this->db->insert_id();
        $this->_json(array(
            "ok"      => true,
            "started" => true,
            "id"      => $new_id,
            "user_id" => $user_id,
            "start"   => date("Y-m-d"),
            "message" => "Annual review started",
        ));
    }
}
