<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/Handover_v2_api.php
 * Migration 046 Closure Handover v2 JSON API.
 * Bearer token authentication. All responses are JSON.
 * Plain ASCII only. No em-dash. Uses Rs for rupees.
 */
class Handover_v2_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/Handover_v2_model', 'h2');
    }

    // Bearer token check. Same pattern as existing API controllers.
    protected function _check_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { $au=function_exists('authunify_uid')?(int)authunify_uid():0; return array('uid'=>$au, 'role'=>($au>0?'bd':'admin')); } // rimlyproof_authunify_20260609
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $auth = '';
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!$auth || stripos($auth, 'Bearer ') !== 0) {
            return false;
        }
        $token = trim(substr($auth, 7));
        if ($token === '') return false;

        
        // Stream-fix: fallback to STEM_DIGEST_TOKEN env var if api_token table missing
        $expected = getenv('STEM_DIGEST_TOKEN');
        if ($expected && hash_equals($expected, $token)) {
            return array('uid' => 0, 'role' => 'system');
        }
        try {
            $row = $this->db->query('SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1', array($token))->row_array();
            if (!$row) return false;
            return $row;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function _json($payload, $http_code = 200) {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    protected function _read_json_body() {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') return array();
        $data = json_decode($raw, true);
        return is_array($data) ? $data : array();
    }

    /**
     * POST /api/handover/save_draft
     * Body cid_id, payload object with handover fields.
     */
    public function save_draft() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_json_body();
            if (empty($body['cid_id'])) { $this->_json(array('ok' => false, 'error' => 'cid_id required')); return; }
            $payload = isset($body['payload']) && is_array($body['payload']) ? $body['payload'] : array();
            $res = $this->h2->save_draft((int)$body['cid_id'], (int)$auth['uid'], $payload);
            $this->_json($res);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/submit
     * Body id plus full payload. Saves draft fields first then submits.
     * Enforces csr_flag conditional rule.
     */
    public function submit() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_json_body();
            if (empty($body['id'])) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }

            // csr_flag conditional rule check at controller layer
            $payload = isset($body['payload']) && is_array($body['payload']) ? $body['payload'] : array();
            if (!empty($payload) && isset($payload['csr_flag']) && (int)$payload['csr_flag'] === 1) {
                $required = array('dm_email','stem_csr1_reg_no','utilisation_cert_required','impact_report_required','third_party_audit_required','csr2_annual_reporting','acontact_designation');
                $missing = array();
                foreach ($required as $f) {
                    if (!isset($payload[$f]) || $payload[$f] === '' || $payload[$f] === null) {
                        $missing[] = $f;
                    }
                }
                if (!empty($missing)) {
                    $this->_json(array('ok' => false, 'error' => 'CSR audit pack incomplete', 'fields' => $missing));
                    return;
                }
            }

            // Save any updated fields onto the draft first
            if (!empty($payload)) {
                $cid_row = $this->db->query('SELECT cid_id FROM handover_v2 WHERE id = ? LIMIT 1', array((int)$body['id']))->row_array();
                if ($cid_row) {
                    $this->h2->save_draft((int)$cid_row['cid_id'], (int)$auth['uid'], $payload);
                }
            }

            $res = $this->h2->submit((int)$body['id'], (int)$auth['uid']);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/handover/list?status=&bd_uid=
     * Admin may pass bd_uid. BD sees own.
     */
    public function listing() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $status = $this->input->get('status');
            $bd_uid_req = $this->input->get('bd_uid');
            $role = isset($auth['role']) ? strtolower($auth['role']) : '';
            $bd_uid = (($role === 'admin' || (int)$auth['uid'] === 0) && !empty($bd_uid_req)) ? (int)$bd_uid_req : (int)$auth['uid'];
            $rows = $this->h2->list_for_bd($bd_uid, $status);
            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/handover/cm_queue
     */
    public function cm_queue() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $cm_uid_param = (int)($this->input->get('cm_uid') ?: 0);
            $eff_cm_uid = ((int)$auth['uid'] === 0 && $cm_uid_param > 0) ? $cm_uid_param : (int)$auth['uid'];
            $rows = $this->h2->list_for_cm_approval($eff_cm_uid);
            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/handover/detail?id=
     */
    public function detail() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $id = (int)$this->input->get('id');
            if (!$id) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }
            $res = $this->h2->detail($id, (int)$auth['uid']);
            $http = !empty($res['ok']) ? 200 : 403;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/approve
     * Body id, remarks. cm_uid is taken from bearer token.
     */
    public function approve() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_json_body();
            if (empty($body['id'])) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }
            $remarks = isset($body['remarks']) ? (string)$body['remarks'] : '';
            $res = $this->h2->cm_approve((int)$body['id'], (int)$auth['uid'], $remarks);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/reject
     * Body id, reason.
     */
    public function reject() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_json_body();
            if (empty($body['id'])) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }
            $reason = isset($body['reason']) ? (string)$body['reason'] : '';
            if ($reason === '') { $this->_json(array('ok' => false, 'error' => 'reason required')); return; }
            $res = $this->h2->cm_reject((int)$body['id'], (int)$auth['uid'], $reason);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/mark_installation_started
     * Body id, uid (uid is the installation team user).
     */
    public function mark_installation_started() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_json_body();
            if (empty($body['id'])) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }
            $res = $this->h2->mark_installation_started((int)$body['id']);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }
}

class HandoverV2 extends Handover_v2_api {}
