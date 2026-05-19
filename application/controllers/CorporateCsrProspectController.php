<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorporateCsrProspectController
 *
 * Migration 041. Routes /api/csr_prospect/*
 *
 * Auth: Bearer api_token OR STEM_DIGEST_TOKEN for admin endpoints.
 */
class CorporateCsrProspectController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/CorporateCsrProspect_model', 'csr');
        $this->_require_bearer();
    }

    private function _require_bearer() {
        $h = $this->input->get_request_header('Authorization', true);
        if (!$h || !preg_match('/^Bearer\s+(.+)$/i', $h, $m)) {
            return $this->_json(['error' => 'unauthorized'], 401);
        }
        $token = trim($m[1]);
        $digest = getenv('STEM_DIGEST_TOKEN');
        if ($digest && hash_equals($digest, $token)) return;

        $u = $this->db->where('api_token', $token)->get('user')->row_array();
        if (!$u) return $this->_json(['error' => 'invalid_token'], 401);
        $this->_uid = (int)$u['uid'];
    }

    private function _json($data, $code = 200) {
        $this->output->set_status_header($code)
                     ->set_content_type('application/json')
                     ->set_output(json_encode($data));
        exit;
    }

    // ============================================================
    // PROBE
    // ============================================================
    public function probe() {
        $r = $this->csr->probe();
        $this->_json($r, $r['ok'] ? 200 : 503);
    }

    // ============================================================
    // REFRESH suggestions for one BD
    // ============================================================
    public function refresh_for_bd() {
        $bd_uid = (int)($this->input->post('bd_uid') ?: $this->input->get('bd_uid'));
        $tpd    = $this->input->post('target_plan_date') ?: $this->input->get('target_plan_date');
        if (!$bd_uid) return $this->_json(['error' => 'bd_uid required'], 422);

        $r = $this->csr->refresh_for_bd($bd_uid, $tpd);
        $this->_json($r);
    }

    // ============================================================
    // TODAY for BD
    // ============================================================
    public function today_for_bd() {
        $bd_uid = (int)$this->input->get('bd_uid');
        $tpd    = $this->input->get('plan_date');
        if (!$bd_uid) return $this->_json(['error' => 'bd_uid required'], 422);
        $this->_json(['suggestions' => $this->csr->today_for_bd($bd_uid, $tpd)]);
    }

    public function today_summary() {
        $this->_json($this->csr->today_org_summary());
    }

    // ============================================================
    // ACCEPT and seed
    // ============================================================
    public function accept_and_seed() {
        $sid    = (int)$this->input->post('suggestion_id');
        $bd_uid = (int)$this->input->post('bd_uid');
        $tpd    = $this->input->post('target_plan_date');
        if (!$sid || !$bd_uid) return $this->_json(['error' => 'suggestion_id and bd_uid required'], 422);

        $this->_json($this->csr->accept_and_seed($sid, $bd_uid, $tpd));
    }

    public function dismiss() {
        $sid    = (int)$this->input->post('suggestion_id');
        $bd_uid = (int)$this->input->post('bd_uid');
        $reason = (string)$this->input->post('reason');
        if (!$sid || !$bd_uid) return $this->_json(['error' => 'suggestion_id and bd_uid required'], 422);
        $this->_json($this->csr->dismiss($sid, $bd_uid, $reason));
    }

    // ============================================================
    // SYNCS
    // ============================================================
    public function sync_csr_gov() {
        $cin = $this->input->post('cin');
        $this->_json($this->csr->sync_csr_gov($cin));
    }

    public function sync_apollo() {
        $corp_id = (int)$this->input->get('corp_id');
        if (!$corp_id) return $this->_json(['error' => 'corp_id required'], 422);

        // Force enrich one corporate
        $apollo_calls = 0;
        $dm_id = $this->csr->_ensure_decision_maker_public($corp_id, $apollo_calls);
        $this->_json(['dm_id' => $dm_id, 'apollo_calls' => $apollo_calls]);
    }

    // ============================================================
    // LOOKUP helpers
    // ============================================================
    public function corporate() {
        $id = (int)$this->uri->segment(4);
        if (!$id) return $this->_json(['error' => 'id required'], 422);

        $corp = $this->db->where('csr_corporate_id', $id)->get('csr_corporate_master_v2')->row_array();
        if (!$corp) return $this->_json(['error' => 'not found'], 404);

        $projects = $this->db->where('csr_corporate_id', $id)->get('csr_project_v2')->result_array();
        $dms = $this->db->where('csr_corporate_id', $id)->where('active', 1)->get('csr_decision_maker_v2')->result_array();

        $this->_json(['corporate' => $corp, 'projects' => $projects, 'decision_makers' => $dms]);
    }

    public function influencers() {
        $district = $this->input->get('district');
        $state    = $this->input->get('state');
        $role     = $this->input->get('role');

        $this->db->where('active', 1);
        if ($district) $this->db->where('district', $district);
        if ($state)    $this->db->where('state', $state);
        if ($role)     $this->db->where('role', $role);
        $rows = $this->db->order_by('role')->limit(100)->get('political_influencer_master_v2')->result_array();
        $this->_json(['influencers' => $rows]);
    }
}
