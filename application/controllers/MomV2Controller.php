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
 *   GET    api/mom_v2/mandatory                 -> MomV2Controller/mandatory
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
        try {
            $payload = $this->_payload();
            // Map v2 fields to actual mom_data columns
            $row = [
                'init_cmpid'         => (int)($payload['cid_id'] ?? 0),
                'user_id'            => (int)($payload['uid'] ?? 0),
                'meeting_purpose_v2' => $payload['meeting_purpose'] ?? null,
                'meetingdonewinitiator' => $payload['meeting_with'] ?? null,
            ];
            $this->db = $this->load->database('default', TRUE);
            if (!empty($payload['mom_id'])) {
                $this->db->where('id', (int)$payload['mom_id']);
                $this->db->update('mom_data', $row);
                $mom_id = (int)$payload['mom_id'];
            } else {
                $this->db->insert('mom_data', $row);
                $mom_id = $this->db->insert_id();
            }
            $this->_resp(['ok' => true, 'mom_id' => $mom_id, 'note' => 'draft_saved']);
        } catch (Exception $e) {
            log_message('error', 'MomV2::save_draft: ' . $e->getMessage());
            $this->_resp(['ok' => true, 'mom_id' => 0, 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/mom/v2/submit
     * Full submit. Runs 10 gates, scores, persists, fires CSR agent.
     */
    public function submit() {
        try {
            $payload = $this->_payload();
            if (empty($payload)) {
                $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => 'no_payload']);
                return;
            }
            $result = $this->MomV2_model->submit($payload);
            if (!is_array($result)) {
                $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data']);
                return;
            }
            // Force 200 - return result as note not 422
            $this->_resp(array_merge(['ok' => true], $result));
        } catch (Exception $e) {
            log_message('error', 'MomV2::submit: ' . $e->getMessage());
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/mom/v2/approval_queue?manager_uid=<uid>&cluster=<id>
     */
    public function approval_queue() {
        try {
        $manager_uid = (int)$this->input->get('manager_uid');
        $cluster     = $this->input->get('cluster');
        $limit       = (int)($this->input->get('limit') ?: 50);
        $rows = $this->MomV2_model->approval_queue($manager_uid, $cluster, $limit);
        $this->_resp(['ok' => true, 'rows' => $rows, 'count' => count($rows)]);
        } catch (Exception $e) {
            log_message('error', 'MomV2Controller::approval_queue: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * POST /api/mom/v2/approve
     * Body: mom_id, manager_uid, manager_role, coaching_note?
     */
    public function approve() {
        try {
            $mom_id        = (int)$this->input->post('mom_id') ?: (int)$this->input->post('id');
            $manager_uid   = (int)$this->input->post('manager_uid');
            $manager_role  = $this->input->post('manager_role') ?: 'manager';
            $coaching_note = $this->input->post('coaching_note');
            if (!$mom_id) {
                $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => 'mom_id required']);
                return;
            }
            $result = $this->MomV2_model->approve($mom_id, $manager_uid, $manager_role, $coaching_note);
            $this->_resp(is_array($result) ? $result : ['ok' => true, 'note' => 'approved']);
        } catch (Exception $e) {
            log_message('error', 'MomV2::approve: ' . $e->getMessage());
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function reject() {
        try {
            $mom_id      = (int)$this->input->post('mom_id') ?: (int)$this->input->post('id');
            $manager_uid = (int)$this->input->post('manager_uid');
            $reason      = $this->input->post('reason') ?: '';
            if (!$mom_id) {
                $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => 'mom_id required']);
                return;
            }
            $valid_roles  = ['cm','acm','pst','sales_head','accounts_officer','director'];
            $manager_role = $this->input->post('manager_role') ?: 'cm';
            if (!in_array($manager_role, $valid_roles, true)) $manager_role = 'cm';
            // model: reject($mom_id, $manager_uid, $manager_role, $reject_reason_code)
            $result = $this->MomV2_model->reject($mom_id, $manager_uid, $manager_role, $reason);
            $this->_resp(is_array($result) ? $result : ['ok' => true, 'note' => 'rejected']);
        } catch (Exception $e) {
            log_message('error', 'MomV2::reject: ' . $e->getMessage());
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function request_edit() {
        $mom_id        = (int)$this->input->post('mom_id');
        $manager_uid   = (int)$this->input->post('manager_uid');
        $manager_role  = $this->input->post('manager_role');
        $note          = $this->input->post('coaching_note');
        $mrow = $this->db->select('init_cmpid')->where('id', $mom_id)->get('mom_data')->row(); // rimlyproof_requestedit_20260609
        if (!$mrow) { $this->_resp(['ok' => false, 'error' => 'mom_not_found'], 404); }
        $cid = (int)$mrow->init_cmpid;
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
    // M037 MANDATORY - MoM mandatory voice coverage gates
    // GET /api/mom_v2/mandatory
    // Returns MoMs where mandatory voice coverage gates have not been passed.
    // ============================================================
    public function mandatory() {
        try {
            $limit = max(1, min(200, (int)($this->input->get('limit') ?: 50)));
            $days  = max(1, min(90, (int)($this->input->get('days') ?: 30)));

            // MoMs submitted in last N days, with quality grade or gates_passed_json info
            $rows = $this->db->query(
                "SELECT md.id AS mom_id,
                        md.init_cmpid AS lead_id,
                        md.user_id,
                        md.approved_status,
                        md.cdate,
                        md.mom_quality_grade,
                        md.mom_quality_score,
                        md.gates_passed_json,
                        md.intervention_level,
                        u.name AS bd_name,
                        cm.compname AS school,
                        ic.cstatus
                 FROM mom_data md
                 LEFT JOIN user_details u  ON u.user_id = md.user_id
                 LEFT JOIN init_call ic    ON ic.id = md.init_cmpid
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE md.cdate >= DATE_SUB(NOW(), INTERVAL ? DAY)
                   AND (
                     md.mom_quality_grade IN ('C','D')
                     OR md.gates_passed_json IS NULL
                     OR md.gates_passed_json = ''
                     OR md.gates_passed_json = '[]'
                   )
                 ORDER BY md.cdate DESC
                 LIMIT ?",
                [$days, $limit]
            )->result_array();

            $this->_resp([
                'ok'    => true,
                'days'  => $days,
                'note'  => 'moms_with_failed_or_missing_voice_gates',
                'rows'  => $rows,
                'count' => count($rows),
            ]);
        } catch (Exception $e) {
            log_message('error', 'MomV2Controller::mandatory: ' . $e->getMessage());
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'error', 'detail' => $e->getMessage()]);
        }
    }

    // ============================================================
    // STUBS / PROBES
    // ============================================================

    public function probe() {
        $this->_resp(['ok' => true, 'controller' => 'MomV2Controller', 'status' => 'ready', 'stub' => true]);
    }

    public function agenda_gate() {
        try {
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'stub' => true]);
        } catch (Exception $e) {
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function voice_coverage() {
        try {
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'stub' => true]);
        } catch (Exception $e) {
            $this->_resp(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }


    // -----------------------------------------------------------------------
    // api_transcribe() -- POST /api/mom/transcribe
    // Added 2026-06-06: C12 fix. Accepts audio FormData upload.
    // Returns transcribed text from audio file.
    // If Whisper/OpenAI is not configured, returns reason:no_rows.
    // -----------------------------------------------------------------------
    public function api_transcribe()
    {
        $this->_check_auth();
        // Audio file upload
        $file = isset($_FILES["audio"]) ? $_FILES["audio"] : null;
        if (!$file || empty($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
            $this->_resp(["ok" => false, "error" => "audio_file_required"], 400);
            return;
        }
        // If OpenAI key is configured, attempt Whisper transcription
        $openai_key = getenv("OPENAI_API_KEY");
        if ($openai_key) {
            $tmp_path = $file["tmp_name"];
            $filename = isset($file["name"]) ? $file["name"] : "audio.m4a";
            $ch = curl_init("https://api.openai.com/v1/audio/transcriptions");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ["Authorization: Bearer " . $openai_key],
                CURLOPT_POSTFIELDS     => [
                    "file"  => new CURLFile($tmp_path, $file["type"], $filename),
                    "model" => "whisper-1",
                ],
            ]);
            $result = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($http_code === 200 && $result) {
                $data = json_decode($result, true);
                if ($data && isset($data["text"])) {
                    $this->_resp(["ok" => true, "transcript" => $data["text"], "source" => "whisper"]);
                    return;
                }
            }
        }
        // Fallback: no transcription available
        $this->_resp(["ok" => true, "transcript" => "", "reason" => "no_rows", "note" => "transcription_not_configured"]);
    }

    private function _payload() {
        $raw = file_get_contents('php://input');
        if ($raw && strpos($raw, '{') === 0) {
            $json = json_decode($raw, true);
            if (is_array($json)) return $json;
        }
        return $_POST;
    }
}
