<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Meeting-to-Money (M2M) Assurance - Guarded MoM approve
 * Additive build 2026-06-16. New controller; existing approve endpoints
 * (MomV2Controller::approve, /api/mom/v2/approve, etc.) stay byte-for-byte
 * unchanged so the current app keeps working with no regression.
 *
 * Route:
 *   POST /api/m2m/mom/approve_guarded
 *
 * Behavior: runs the Gate A mandatory-field check on the MoM first. If any
 * mandatory field is missing, returns HTTP 200 {ok:false, blocked:true,
 * missing:[...]} WITHOUT approving (no HTTP error - existing app safe). If
 * clear, delegates to the existing MomV2_model::approve and returns its result.
 *
 * Mandatory fields (Gate A): rp_present, prospect_funded, next_step_text,
 * next_step_date, and proposal_committed_date when a proposal was promised
 * (client_commitment hard/soft).
 *
 * ASCII only. "percent" spelled out. Rupees "Rs". Nothing hardcoded.
 */
class M2mMomGuard extends CI_Controller
{
    protected $token;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('Bearer_auth');
        $this->load->helper('url');
        $this->token = $this->bearer_auth->get_bearer_token();
    }

    /**
     * POST /api/m2m/mom/approve_guarded
     * Body: mom_id, manager_uid, manager_role, coaching_note?
     */
    public function approve_guarded()
    {
        if (!$this->bearer_auth->verify($this->token)) {
            return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        $mom_id       = (int)$this->input->post('mom_id');
        $manager_uid  = (int)$this->input->post('manager_uid');
        $manager_role = (string)$this->input->post('manager_role');
        $coaching     = $this->input->post('coaching_note');

        if ($mom_id <= 0) {
            // Structured 200 so the calling screen never crashes on a hard error.
            return $this->_json(['ok' => false, 'blocked' => true, 'missing' => ['mom_id']], 200);
        }

        $gate = $this->_gate_a_check($mom_id);
        if ($gate['blocked']) {
            return $this->_json([
                'ok'      => false,
                'blocked' => true,
                'mom_id'  => $mom_id,
                'missing' => $gate['missing'],
                'reason'  => 'gate_a_mandatory_fields_missing',
                'ts'      => date('Y-m-d H:i:s'),
            ], 200);
        }

        // Gate A clear: delegate to the existing, unchanged approve logic.
        if (!$manager_uid || $manager_role === '') {
            return $this->_json(['ok' => false, 'blocked' => false, 'error' => 'missing_manager_params'], 200);
        }

        $this->load->model('MomV2_model');
        $result = $this->MomV2_model->approve($mom_id, $manager_uid, $manager_role, $coaching);
        if (!is_array($result)) $result = ['ok' => true, 'delegated' => true];
        $result['gate_a'] = 'clear';
        $result['guarded'] = true;
        return $this->_json($result, 200);
    }

    // Inlined Gate A mandatory-field check (same rules as M2m_gate_a::check),
    // kept local so the guard has no network self-call dependency.
    private function _gate_a_check($mom_id)
    {
        $mom = $this->db->select('id, rp_present, prospect_funded, next_step_text,
                next_step_date, client_commitment, proposal_committed_date')
            ->from('mom_data')->where('id', $mom_id)->get()->row_array();

        if (!$mom) {
            return ['blocked' => true, 'missing' => ['mom_row']];
        }

        $missing = [];
        if ($mom['rp_present'] === null || $mom['rp_present'] === '')           $missing[] = 'rp_present';
        if ($mom['prospect_funded'] === null || $mom['prospect_funded'] === '') $missing[] = 'prospect_funded';
        if (trim((string)$mom['next_step_text']) === '')                        $missing[] = 'next_step_text';
        if ($mom['next_step_date'] === null || $mom['next_step_date'] === '' || $mom['next_step_date'] === '0000-00-00') {
            $missing[] = 'next_step_date';
        }
        $commitment = strtolower((string)$mom['client_commitment']);
        if (in_array($commitment, ['hard', 'soft'], true)) {
            if ($mom['proposal_committed_date'] === null || $mom['proposal_committed_date'] === '' || $mom['proposal_committed_date'] === '0000-00-00') {
                $missing[] = 'proposal_committed_date';
            }
        }

        return ['blocked' => !empty($missing), 'missing' => $missing];
    }

    protected function _json($data, $code)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
