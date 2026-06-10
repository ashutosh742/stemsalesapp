<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomV2Controller
 *
 * Migration 021 - MoM v2 endpoints
 * Date: 16 May 2026
 * Staging only until 18 May 2026.
 *
 * Routes (config/routes.php to add):
 *   GET    api/mom/v2/draft/(:num)              -> MomV2Controller/draft/$1
 *   POST   api/mom/v2/submit                    -> MomV2Controller/submit
 *   POST   api/mom/v2/save_draft                -> MomV2Controller/save_draft
 *   GET    api/mom/v2/approval_queue            -> MomV2Controller/approval_queue
 *   POST   api/mom/v2/approve                   -> MomV2Controller/approve
 *   POST   api/mom/v2/reject                    -> MomV2Controller/reject
 *   POST   api/mom/v2/request_edit              -> MomV2Controller/request_edit
 *   GET    api/lead/(:num)/contact_history      -> MomV2Controller/contact_history/$1
 *   POST   api/lead/(:num)/promote_cstatus      -> MomV2Controller/promote_cstatus/$1
 *
 * Auth: Bearer STEM_DIGEST_TOKEN or session uid.
 * All responses JSON.
 */
class MomV2Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('MomV2_model');
        $this->load->model('LinkedinCsr_model');
        $this->load->helper('url');
        $this->_check_auth();
    }

    private function _check_auth() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $session_uid = $this->session->userdata('uid');
            if (!$session_uid) $this->_resp(['ok' => false, 'error' => 'unauthorized'], 401);
        }
    }

    private function _resp($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function _uid() {
        $uid = (int)$this->input->post('uid');
        if (!$uid) $uid = (int)$this->session->userdata('uid');
        return $uid;
    }

    // ============================================================
    // ENDPOINTS
    // ============================================================

    /**
     * GET /api/mom/v2/draft/{cid_id}
     * Returns lead + DM prefill + any existing draft mom_data row.
     */
    public function draft($cid_id) {
        $uid = $this->_uid();
        $result = $this->MomV2_model->get_draft((int)$cid_id, $uid);
        $this->_resp($result, $result['ok'] ? 200 : 404);
    }

    /**
     * POST /api/mom/v2/save_draft
     * Body: full payload, no gate check, persists to mom_data with approved_status NULL.
     */
    public function save_draft() {
        $payload = $this->_payload();
        // Skip gates for draft, just persist
        $this->db = $this->load->database('default', TRUE);
        $row = [
            'cid_id' => (int)$payload['cid_id'],
            'uid'    => (int)$payload['uid'],
            'meeting_purpose_v2' => $payload['meeting_purpose'] ?? null,
            'meeting_with'       => $payload['meeting_with']    ?? null,
            'dm_name'            => $payload['dm_name']         ?? null,
            'dm_designation'     => $payload['dm_designation']  ?? null,
            'dm_phone'           => $payload['dm_phone']        ?? null,
            'dm_email'           => $payload['dm_email']        ?? null,
            'dm_org_type'        => $payload['dm_org_type']     ?? null,
            'rpmmom'             => $payload['rpmmom']          ?? null
            // Drafts only carry partial state; full persistence happens at submit
        ];
        if (!empty($payload['mom_id'])) {
            $this->db->where('id', (int)$payload['mom_id']);
            $this->db->update('mom_data', $row);
            $mom_id = (int)$payload['mom_id'];
        } else {
            $this->db->insert('mom_data', $row);
            $mom_id = $this->db->insert_id();
        }
        $this->_resp(['ok' => true, 'mom_id' => $mom_id]);
    }

    /**
     * POST /api/mom/v2/submit
     * Full submit. Runs 10 gates, scores, persists, fires CSR agent.
     */
    public function submit() {
        $payload = $this->_payload();
        $result = $this->MomV2_model->submit($payload);
        $this->_resp($result, $result['ok'] ? 200 : 422);
    }

    /**
     * GET /api/mom/v2/approval_queue?manager_uid=<uid>&cluster=<id>
     */
    public function approval_queue() {
        $manager_uid = (int)$this->input->get('manager_uid');
        $cluster     = $this->input->get('cluster');
        $limit       = (int)($this->input->get('limit') ?: 50);
        $rows = $this->MomV2_model->approval_queue($manager_uid, $cluster, $limit);
        $this->_resp(['ok' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * POST /api/mom/v2/approve
     * Body: mom_id, manager_uid, manager_role, coaching_note?
     */
    public function approve() {
        $mom_id        = (int)$this->input->post('mom_id');
        $manager_uid   = (int)$this->input->post('manager_uid');
        $manager_role  = $this->input->post('manager_role');
        $coaching_note = $this->input->post('coaching_note');
        if (!$mom_id || !$manager_uid || !$manager_role) {
            $this->_resp(['ok' => false, 'error' => 'missing_params'], 422);
        }
        $result = $this->MomV2_model->approve($mom_id, $manager_uid, $manager_role, $coaching_note);
        $this->_resp($result);
    }

    /**
     * POST /api/mom/v2/reject
     * Body: mom_id, manager_uid, manager_role, reject_reason_code, coaching_note?
     */
    public function reject() {
        $mom_id        = (int)$this->input->post('mom_id');
        $manager_uid   = (int)$this->input->post('manager_uid');
        $manager_role  = $this->input->post('manager_role');
        $reason        = $this->input->post('reject_reason_code');
        $coaching_note = $this->input->post('coaching_note');
        if (!$mom_id || !$manager_uid || !$manager_role || !$reason) {
            $this->_resp(['ok' => false, 'error' => 'missing_params'], 422);
        }
        $result = $this->MomV2_model->reject($mom_id, $manager_uid, $manager_role, $reason, $coaching_note);
        $this->_resp($result);
    }

    /**
     * POST /api/mom/v2/request_edit
     */
    public function request_edit() {
        $mom_id        = (int)$this->input->post('mom_id');
        $manager_uid   = (int)$this->input->post('manager_uid');
        $manager_role  = $this->input->post('manager_role');
        $note          = $this->input->post('coaching_note');
        $cid = $this->db->select('cid_id')->where('id', $mom_id)->get('mom_data')->row()->cid_id;
        $this->db->insert('mom_line_manager_review', [
            'mom_id' => $mom_id,
            'cid_id' => $cid,
            'manager_uid' => $manager_uid,
            'manager_role' => $manager_role,
            'action' => 'request_edit',
            'coaching_note' => $note
        ]);
        $this->_resp(['ok' => true]);
    }

    /**
     * GET /api/lead/{cid_id}/contact_history
     */
    public function contact_history($cid_id) {
        $this->db->where('cid_id', (int)$cid_id);
        $this->db->order_by('changed_at', 'DESC');
        $rows = $this->db->get('init_call_contact_history')->result_array();
        $this->_resp(['ok' => true, 'rows' => $rows]);
    }

    /**
     * POST /api/lead/{cid_id}/promote_cstatus
     * Body: from_cstatus, to_cstatus, bd_uid
     * Enforces Checkpoint 3: cannot promote to cstatus 6 without complete DM block.
     */
    public function promote_cstatus($cid_id) {
        $to    = (int)$this->input->post('to_cstatus');
        $from  = (int)$this->input->post('from_cstatus');
        $bduid = (int)$this->input->post('bd_uid');

        $lead = $this->db->where('id', $cid_id)->get('init_call')->row_array();
        if (!$lead) $this->_resp(['ok' => false, 'error' => 'lead_not_found'], 404);

        // Checkpoint 3: promotion to cstatus 6 requires DM block
        if ($to >= 6) {
            $missing = [];
            if (empty($lead['dm_contact_name']))        $missing[] = 'dm_name';
            if (empty($lead['dm_contact_designation'])) $missing[] = 'dm_designation';
            if (empty($lead['dm_contact_phone']) && empty($lead['dm_contact_email'])) $missing[] = 'dm_phone_or_email';
            if (!empty($missing)) {
                $this->_resp([
                    'ok' => false,
                    'error' => 'dm_contact_incomplete',
                    'missing' => $missing,
                    'message' => 'Cannot promote to Positive. DM contact block on the lead is incomplete. Edit Lead Detail to fill ' . implode(', ', $missing) . '.'
                ], 422);
            }
        }

        // Special check: promotion to 12 (Won) requires lead_stage_signoff approved
        if ($to === 12) {
            $signoff = $this->db->where(['cid_id' => $cid_id, 'to_cstatus' => 12, 'manager_action' => 'approved'])
                                ->order_by('id', 'DESC')->limit(1)->get('lead_stage_signoff')->row_array();
            if (!$signoff) {
                $this->_resp([
                    'ok' => false,
                    'error' => 'won_signoff_required',
                    'message' => 'Cannot mark Won without approved Won signoff from CM and Accounts Officer.'
                ], 422);
            }
        }

        $this->db->where('id', $cid_id);
        $this->db->update('init_call', ['cstatus' => $to]);

        $this->db->insert('lead_progression_log', [
            'cid_id' => $cid_id,
            'from_cstatus' => $from,
            'to_cstatus' => $to,
            'changed_by' => $bduid,
            'changed_at' => date('Y-m-d H:i:s'),
            'creation_path_hint' => 'mom_v2_promote'
        ]);

        $this->_resp(['ok' => true, 'cid_id' => $cid_id, 'cstatus' => $to]);
    }

    // ============================================================
    // PAYLOAD HELPER (accepts both POST form and JSON body)
    // ============================================================
    private function _payload() {
        $raw = file_get_contents('php://input');
        if ($raw && strpos($raw, '{') === 0) {
            $json = json_decode($raw, true);
            if (is_array($json)) return $json;
        }
        return $_POST;
    }
}
