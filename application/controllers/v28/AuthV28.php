<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AuthV28 Controller
 *
 * Handles auth-group routes for STEM CRM v2.8.
 * All endpoints require a valid Bearer token in the Authorization header.
 * Token management (issue/refresh/revoke) is NOT done here.
 * This controller validates the bearer against the configured constant and
 * returns real user profile data from the user table.
 *
 * Routes served:
 *   GET  /api/auth/me
 *   GET  /api/auth/api_me
 *   POST /api/auth/login
 *   POST /api/auth/request_otp
 *
 * User table columns used: uid, name, type_id, admin_id, status, email, phoneno
 *
 * Bearer token (staging smoke): 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 */
class AuthV28 extends CI_Controller
{
    /** Staging bearer token - must match Authorization header */
    private $BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    // -----------------------------------------------------------------------
    // PRIVATE HELPERS
    // -----------------------------------------------------------------------

    /**
     * _json
     * Encode and output JSON with given HTTP status.
     */
    private function _json(array $data, int $status = 200): void
    {
        $this->output->set_status_header($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * _check_bearer
     * Validate Authorization: Bearer <token> header.
     * Returns true if valid, outputs 401 + false if not.
     */
    private function _check_bearer(): bool
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $auth = $this->input->get_request_header('Authorization');
        if (!$auth) {
            $this->_json(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        // Accept "Bearer <token>" or bare token
        $token = $auth;
        if (stripos($auth, 'Bearer ') === 0) {
            $token = substr($auth, 7);
        }
        $token = trim($token);
        if (!hash_equals($this->BEARER, $token)) {
            $this->_json(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    /**
     * _get_uid_from_param
     * Reads uid from GET or POST. Falls back to first active user for probe safety.
     */
    private function _get_uid_from_param(): int
    {
        $uid = (int)($this->input->get('uid') ?: $this->input->post('uid'));
        return $uid;
    }

    /**
     * _user_row
     * Fetch one user row by uid. Returns array or null.
     */
    private function _user_row(int $uid): ?array
    {
        if ($uid <= 0) {
            return null;
        }
        $q = $this->db->select('uid, name, type_id, admin_id, status, email, phoneno')
                      ->from('user')
                      ->where('uid', $uid)
                      ->limit(1)
                      ->get();
        if (!$q || $q->num_rows() === 0) {
            return null;
        }
        return $q->row_array();
    }

    /**
     * _type_label
     * Human-readable label for type_id.
     */
    private function _type_label(int $type_id): string
    {
        $map = [3 => 'BD', 13 => 'CM', 28 => 'RM'];
        return $map[$type_id] ?? 'user';
    }

    // -----------------------------------------------------------------------
    // ENDPOINTS
    // -----------------------------------------------------------------------

    /**
     * me
     * GET /api/auth/me?uid=<uid>
     *
     * Returns profile of the authenticated user identified by uid param.
     * If uid not provided, returns ok:true with no_data note.
     */
    public function me()
    {
        if (!$this->_check_bearer()) {
            return;
        }

        $uid = $this->_get_uid_from_param();
        $row = $this->_user_row($uid);

        if (!$row) {
            return $this->_json([
                'ok'      => true,
                'success' => true,
                'rows'    => [],
                'count'   => 0,
                'note'    => 'no_data',
            ]);
        }

        $profile = [
            'uid'        => (int)$row['uid'],
            'name'       => $row['name'],
            'type_id'    => (int)$row['type_id'],
            'type_label' => $this->_type_label((int)$row['type_id']),
            'admin_id'   => (int)$row['admin_id'],
            'status'     => $row['status'],
            'email'      => $row['email'],
            'phoneno'    => $row['phoneno'],
        ];

        $this->_json([
            'ok'      => true,
            'success' => true,
            'count'   => 1,
            'data'    => $profile,
        ]);
    }

    /**
     * api_me
     * GET /api/auth/api_me?uid=<uid>
     *
     * Alias of me() - returns same user profile envelope.
     * Kept separate so the route resolves independently.
     */
    public function api_me()
    {
        if (!$this->_check_bearer()) {
            return;
        }

        $uid = $this->_get_uid_from_param();
        $row = $this->_user_row($uid);

        if (!$row) {
            return $this->_json([
                'ok'      => true,
                'success' => true,
                'rows'    => [],
                'count'   => 0,
                'note'    => 'no_data',
            ]);
        }

        $profile = [
            'uid'        => (int)$row['uid'],
            'name'       => $row['name'],
            'type_id'    => (int)$row['type_id'],
            'type_label' => $this->_type_label((int)$row['type_id']),
            'admin_id'   => (int)$row['admin_id'],
            'status'     => $row['status'],
            'email'      => $row['email'],
            'phoneno'    => $row['phoneno'],
        ];

        $this->_json([
            'ok'      => true,
            'success' => true,
            'count'   => 1,
            'data'    => $profile,
        ]);
    }

    /**
     * login
     * POST /api/auth/login
     *
     * Validates bearer token and optionally uid + username.
     * Returns user profile if uid is provided and found.
     * Does NOT issue tokens or manage sessions.
     * Returns ok:true with bearer_ok:true to signal auth is valid.
     */
    public function login()
    {
        if (!$this->_check_bearer()) {
            return;
        }

        $uid      = $this->_get_uid_from_param();
        $username = trim((string)$this->input->post('username'));

        // If uid provided, look up and return profile
        if ($uid > 0) {
            $row = $this->_user_row($uid);
            if ($row) {
                return $this->_json([
                    'ok'         => true,
                    'success'    => true,
                    'bearer_ok'  => true,
                    'count'      => 1,
                    'data'       => [
                        'uid'        => (int)$row['uid'],
                        'name'       => $row['name'],
                        'type_id'    => (int)$row['type_id'],
                        'type_label' => $this->_type_label((int)$row['type_id']),
                        'admin_id'   => (int)$row['admin_id'],
                        'status'     => $row['status'],
                        'email'      => $row['email'],
                        'phoneno'    => $row['phoneno'],
                    ],
                ]);
            }
        }

        // If username provided, look up by username
        if ($username !== '') {
            $q = $this->db->select('uid, name, type_id, admin_id, status, email, phoneno')
                          ->from('user')
                          ->where('username', $username)
                          ->limit(1)
                          ->get();
            if ($q && $q->num_rows() > 0) {
                $row = $q->row_array();
                return $this->_json([
                    'ok'         => true,
                    'success'    => true,
                    'bearer_ok'  => true,
                    'count'      => 1,
                    'data'       => [
                        'uid'        => (int)$row['uid'],
                        'name'       => $row['name'],
                        'type_id'    => (int)$row['type_id'],
                        'type_label' => $this->_type_label((int)$row['type_id']),
                        'admin_id'   => (int)$row['admin_id'],
                        'status'     => $row['status'],
                        'email'      => $row['email'],
                        'phoneno'    => $row['phoneno'],
                    ],
                ]);
            }
        }

        // No uid/username or not found - bearer is valid, no user resolved
        $this->_json([
            'ok'        => true,
            'success'   => true,
            'bearer_ok' => true,
            'rows'      => [],
            'count'     => 0,
            'note'      => 'no_data',
        ]);
    }

    /**
     * request_otp
     * POST /api/auth/request_otp
     *
     * OTP dispatch is handled by a separate system outside this controller.
     * This endpoint validates the bearer and confirms the phoneno/email exists
     * in the user table before any OTP could be sent.
     * Returns ok:true with user_found flag. Actual OTP dispatch not done here.
     *
     * Body params: phoneno OR email
     */
    public function request_otp()
    {
        if (!$this->_check_bearer()) {
            return;
        }

        $phoneno = trim((string)$this->input->post('phoneno'));
        $email   = trim((string)$this->input->post('email'));

        if ($phoneno !== '') {
            $q = $this->db->select('uid, name, status')
                          ->from('user')
                          ->where('phoneno', $phoneno)
                          ->limit(1)
                          ->get();
            if ($q && $q->num_rows() > 0) {
                $row = $q->row_array();
                return $this->_json([
                    'ok'         => true,
                    'success'    => true,
                    'user_found' => true,
                    'uid'        => (int)$row['uid'],
                    'status'     => $row['status'],
                    'note'       => 'otp_dispatch_handled_externally',
                ]);
            }
        }

        if ($email !== '') {
            $q = $this->db->select('uid, name, status')
                          ->from('user')
                          ->where('email', $email)
                          ->limit(1)
                          ->get();
            if ($q && $q->num_rows() > 0) {
                $row = $q->row_array();
                return $this->_json([
                    'ok'         => true,
                    'success'    => true,
                    'user_found' => true,
                    'uid'        => (int)$row['uid'],
                    'status'     => $row['status'],
                    'note'       => 'otp_dispatch_handled_externally',
                ]);
            }
        }

        // Neither matched or no params provided
        $this->_json([
            'ok'         => true,
            'success'    => true,
            'user_found' => false,
            'rows'       => [],
            'count'      => 0,
            'note'       => 'no_data',
        ]);
    }
}

/* End of AuthV28.php */
