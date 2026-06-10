<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/BdRequest.php
 * BD Request v2 JSON API - Migration 046.
 * Wraps AIAgents/BDRequest_model. Bearer token auth. All responses JSON.
 * Plain ASCII only. No em-dash. Uses Rs for rupees.
 */
class BdRequest extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/BDRequest_model', 'brm');
    }

    protected function _check_bearer() {
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $auth = '';
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!$auth || stripos($auth, 'Bearer ') !== 0) return false;
        $token = trim(substr($auth, 7));
        if ($token === '') return false;
        $row = $this->db->query(
            'SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1',
            array($token)
        )->row_array();
        if (!$row) return false;
        return $row;
    }

    protected function _json($payload, $http_code = 200) {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    protected function _read_body() {
        // Try JSON body first, then fall back to POST form data.
        $raw = file_get_contents('php://input');
        if ($raw !== false && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) return $decoded;
        }
        // Fall back to $_POST.
        return $_POST ?: array();
    }

    /**
     * GET /api/bd_request/probe
     */
    public function probe() {
        $this->_json(array(
            'ok'          => true,
            'endpoint'    => 'bd_request',
            'migration'   => '046',
            'status'      => 'ready',
            'server_time' => date('c')
        ));
    }

    /**
     * GET /api/bd_request/list
     * Returns requests for the token user (as requestor).
     */
    public function list() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $rows = $this->brm->inbox_for_requestor((int)$auth['uid']);
            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/bd_request/create
     * Body: school_name, school_pincode, reason and optional fields.
     * Supports dry_run=1 to echo payload without insert.
     */
    public function create() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_body();
            $dry_run = !empty($body['dry_run']) ? (int)$body['dry_run'] : 0;

            if ($dry_run) {
                $this->_json(array('ok' => true, 'dry_run' => true, 'payload' => $body));
                return;
            }

            $body['requestor_uid'] = (int)$auth['uid'];
            $res = $this->brm->create_request($body);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/bd_request/inbox?status=
     * CM inbox. uid from bearer token.
     */
    public function inbox() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $status = $this->input->get('status');
            $rows = $this->brm->inbox_for_cm((int)$auth['uid'], $status);
            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/bd_request/action
     * Body: id, action (approve|reject), remarks.
     */
    public function action() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body    = $this->_read_body();
            $id      = isset($body['id']) ? (int)$body['id'] : 0;
            $action  = isset($body['action']) ? trim($body['action']) : '';
            $remarks = isset($body['remarks']) ? trim($body['remarks']) : '';

            if (!$id || !$action) {
                $this->_json(array('ok' => false, 'error' => 'id and action are required')); return;
            }

            if ($action === 'approve') {
                $res = $this->brm->approve($id, (int)$auth['uid'], $remarks);
            } else if ($action === 'reject') {
                if ($remarks === '') {
                    $this->_json(array('ok' => false, 'error' => 'remarks required for rejection')); return;
                }
                $res = $this->brm->reject($id, (int)$auth['uid'], $remarks);
            } else {
                $this->_json(array('ok' => false, 'error' => 'Invalid action. Use approve or reject')); return;
            }

            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/bd_request/summary
     * Summary counts by status for the token user's requests.
     */
    public function summary() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $uid = (int)$auth['uid'];

            $rows = $this->db->query(
                'SELECT status, COUNT(*) AS cnt FROM bd_request WHERE requestor_uid = ? GROUP BY status',
                array($uid)
            )->result_array();

            $summary = array('pending' => 0, 'approved' => 0, 'rejected' => 0, 'init_call_created' => 0, 'escalated' => 0, 'total' => 0);
            foreach ($rows as $r) {
                $s = $r['status'];
                if (array_key_exists($s, $summary)) {
                    $summary[$s] = (int)$r['cnt'];
                }
                $summary['total'] += (int)$r['cnt'];
            }

            $this->_json(array('ok' => true, 'summary' => $summary));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/bd_request/approve
     * Direct approve endpoint (mobile alias). Body: req_id or id.
     */
    public function approve_direct() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_body();
            $id      = isset($body['req_id']) ? (int)$body['req_id'] : (isset($body['id']) ? (int)$body['id'] : 0);
            $remarks = isset($body['remarks']) ? trim($body['remarks']) : '';
            if (!$id) { $this->_json(array('ok' => false, 'error' => 'req_id required')); return; }
            $res  = $this->brm->approve($id, (int)$auth['uid'], $remarks);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/bd_request/reject
     * Direct reject endpoint (mobile alias). Body: req_id or id, remarks.
     */
    public function reject_direct() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body    = $this->_read_body();
            $id      = isset($body['req_id']) ? (int)$body['req_id'] : (isset($body['id']) ? (int)$body['id'] : 0);
            $remarks = isset($body['remarks']) ? trim($body['remarks']) : '';
            if (!$id) { $this->_json(array('ok' => false, 'error' => 'req_id required')); return; }
            if ($remarks === '') { $this->_json(array('ok' => false, 'error' => 'remarks required for rejection')); return; }
            $res  = $this->brm->reject($id, (int)$auth['uid'], $remarks);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/bd_request/lead_context?req_id=
     * Returns detail of a BD request for mobile context panel.
     */
    public function lead_context() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $req_id = (int)$this->input->get('req_id');
            if (!$req_id) { $req_id = (int)$this->input->get('id'); }
            if (!$req_id) { $this->_json(array('ok' => false, 'error' => 'req_id required')); return; }
            $res  = $this->brm->detail($req_id, (int)$auth['uid']);
            $http = !empty($res['ok']) ? 200 : 403;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/bd_request/logs?request_id=
     */
    public function logs() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $request_id = (int)$this->input->get('request_id');
            if (!$request_id) { $this->_json(array('ok' => false, 'error' => 'request_id required')); return; }

            $detail = $this->brm->detail($request_id, (int)$auth['uid']);
            if (empty($detail['ok'])) {
                $this->_json($detail, 403); return;
            }
            $this->_json(array('ok' => true, 'data' => isset($detail['logs']) ? $detail['logs'] : array()));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }
}
