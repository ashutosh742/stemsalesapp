<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MonthlyLeadReviewController - Migration 020.1
 *
 * Endpoints for end-of-month per-lead deep review.
 * Staging only until Mon 18 May 2026 GitHub access.
 */
class MonthlyLeadReviewController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MonthlyLeadReview_model', 'mlr');
        $this->load->helper(['url']);
        $this->_require_bearer();
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


    private function _require_bearer()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization', true);
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
            exit;
        }
        // STEM_DIGEST_TOKEN check happens in API gateway; controller trusts it here.
    }

    private function _json($payload, $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * POST /api/review/monthly/generate?month=YYYY-MM
     * Snapshots every eligible lead for the month.
     * Returns counts; PDF compilation is kicked off separately by cron.
     */
    public function generate()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['error' => 'method_not_allowed'], 405);
        }
        $month = $this->input->get_post('month');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $this->_json(['error' => 'bad_month'], 400);
        }
        try {
            $out = $this->mlr->snapshot_month($month);
            return $this->_json(array_merge($out, ['status' => 'ok']));
        } catch (Exception $e) {
            log_message('error', 'monthly review snapshot failed: ' . $e->getMessage());
            return $this->_json(['error' => 'snapshot_failed', 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/review/monthly/manifest?month=YYYY-MM
     * Lists all PDFs generated for the month.
     */
    public function probe()
    {
        $this->output->set_content_type("application/json")->set_output(json_encode(["ok"=>true,"migration"=>"020.1","component"=>"monthly_lead_review"]));
    }

    public function manifest()
    {
        $month = $this->input->get('month') ?: date('Y-m', strtotime('last month'));
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $this->_json(['error' => 'bad_month'], 400);
        }
        return $this->_json($this->mlr->manifest($month));
    }

    /**
     * GET /api/review/monthly/lead/<lead_id>?month=YYYY-MM
     * Returns one-pager JSON for a single lead.
     */
    public function lead_onepager($lead_id = null)
    {
        $month = $this->input->get('month') ?: date('Y-m', strtotime('last month'));
        if (!$lead_id || !is_numeric($lead_id)) {
            return $this->_json(['error' => 'bad_lead'], 400);
        }
        $row = $this->mlr->get_one_pager($month, (int)$lead_id);
        if (!$row) {
            return $this->_json(['error' => 'not_found'], 404);
        }
        // Inflate JSON columns
        $row['stage_journey'] = json_decode($row['stage_journey'] ?: '[]', true);
        $row['activity_this_month'] = json_decode($row['activity_this_month'] ?: '[]', true);
        $row['auto_flags'] = json_decode($row['auto_flags'] ?: '{}', true);
        return $this->_json($row);
    }

    /**
     * GET /api/review/monthly/bd/<uid>?month=YYYY-MM
     * Returns BD compiled PDF binary, or 404 if not generated yet.
     */
    public function bd_pdf($bd_uid = null)
    {
        $month = $this->input->get('month') ?: date('Y-m', strtotime('last month'));
        if (!$bd_uid || !is_numeric($bd_uid)) {
            return $this->_json(['error' => 'bad_uid'], 400);
        }
        $row = $this->db->where(['month' => $month, 'bd_uid' => (int)$bd_uid])
            ->get('monthly_lead_review_bd_pdf')->row_array();
        if (!$row || !file_exists($row['pdf_path'])) {
            return $this->_json(['error' => 'pdf_not_ready'], 404);
        }
        $this->output
            ->set_content_type('application/pdf')
            ->set_header('Content-Disposition: inline; filename="' . basename($row['pdf_path']) . '"')
            ->set_output(file_get_contents($row['pdf_path']));
    }

    /**
     * GET /api/review/monthly/cm/<uid>?month=YYYY-MM
     * Returns CM compiled PDF binary.
     */
    public function cm_pdf($cm_uid = null)
    {
        $month = $this->input->get('month') ?: date('Y-m', strtotime('last month'));
        if (!$cm_uid || !is_numeric($cm_uid)) {
            return $this->_json(['error' => 'bad_uid'], 400);
        }
        $row = $this->db->where(['month' => $month, 'cm_uid' => (int)$cm_uid])
            ->get('monthly_lead_review_cm_pdf')->row_array();
        if (!$row || !file_exists($row['pdf_path'])) {
            return $this->_json(['error' => 'pdf_not_ready'], 404);
        }
        $this->output
            ->set_content_type('application/pdf')
            ->set_header('Content-Disposition: inline; filename="' . basename($row['pdf_path']) . '"')
            ->set_output(file_get_contents($row['pdf_path']));
    }

    /**
     * GET /api/review/monthly/list?month=YYYY-MM&audience=bd|cm
     * Lightweight list of leads for one audience, used by mobile app.
     */
    public function list_for_audience()
    {
        $month = $this->input->get('month') ?: date('Y-m', strtotime('last month'));
        $audience = $this->input->get('audience');
        $uid = (int)$this->input->get('uid');
        if (!in_array($audience, ['bd', 'cm'], true) || !$uid) {
            return $this->_json(['error' => 'bad_params'], 400);
        }
        $rows = $audience === 'bd'
            ? $this->mlr->get_leads_for_bd($month, $uid)
            : $this->mlr->get_leads_for_cm($month, $uid);
        return $this->_json([
            'month' => $month,
            'audience' => $audience,
            'uid' => $uid,
            'count' => count($rows),
            'rows' => $rows,
        ]);
    }
}
