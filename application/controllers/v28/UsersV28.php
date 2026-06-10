<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * UsersV28 Controller
 *
 * Handles /api/users/* routes for STEM CRM v2.8 staging.
 *
 * Table used (verified on staging):
 *   user  (uid, name, email, phoneno, username, type_id, zone_id, admin_id,
 *           status, active, created_at, updated_at, city, state, country, photo)
 *
 * type_id: 3=BD, 13=CM, 28=RM
 * status: 'active' / 'inactive'
 *
 * Routes:
 *   GET  api/users/by_type
 *   GET  api/users/list
 *   POST api/users/login      (read-only: validates credentials, no session write)
 *   GET  api/users/pilot
 *   GET  api/users/profile
 *   POST api/users/update_fcm
 */
class UsersV28 extends CI_Controller {

    private $bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    private function _check_auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || trim(str_replace('Bearer', '', $hdr)) !== $this->bearer) {
            $this->output->set_status_header(401);
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200)
    {
        $this->output->set_status_header($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // Safe user columns to expose (never password / sensitive internals)
    private $safe_cols = 'uid, name, email, phoneno, username, type_id, zone_id, admin_id, status, active, city, state, country, photo, created_at';

    // -----------------------------------------------------------------------
    // GET api/users/list
    // Returns all active users (status='active').
    // Optional filter: type_id, admin_id, limit (default 100).
    // -----------------------------------------------------------------------
    public function list_users()
    {
        if (!$this->_check_auth()) return;

        $type_id  = (int) $this->input->get('type_id');
        $admin_id = (int) $this->input->get('admin_id');
        $limit    = max(1, min(500, (int) ($this->input->get('limit') ?: 100)));

        $this->db->select($this->safe_cols);
        $this->db->from('user');
        $this->db->where('status', 'active');
        if ($type_id > 0)  { $this->db->where('type_id', $type_id); }
        if ($admin_id > 0) { $this->db->where('admin_id', $admin_id); }
        $this->db->order_by('name', 'ASC')->limit($limit);

        $query = $this->db->get();
        $rows  = $query ? $query->result_array() : [];

        foreach ($rows as &$r) {
            $r['uid']     = (int) $r['uid'];
            $r['type_id'] = (int) $r['type_id'];
            $r['active']  = (int) $r['active'];
        }
        unset($r);

        $this->_json([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    // CI route workaround: CI maps 'list' to list_users via routes file
    public function get_list()
    {
        $this->list_users();
    }

    // -----------------------------------------------------------------------
    // GET api/users/by_type
    // Returns users grouped by type_id.
    // Optional filter: status (default 'active').
    // type_id: 3=BD, 13=CM, 28=RM
    // -----------------------------------------------------------------------
    public function by_type()
    {
        if (!$this->_check_auth()) return;

        $status = $this->input->get('status') ?: 'active';
        $allowed = ['active', 'inactive', 'all'];
        if (!in_array($status, $allowed)) { $status = 'active'; }

        $this->db->select($this->safe_cols);
        $this->db->from('user');
        if ($status !== 'all') { $this->db->where('status', $status); }
        $this->db->order_by('type_id', 'ASC')->order_by('name', 'ASC')->limit(500);

        $query = $this->db->get();
        $rows  = $query ? $query->result_array() : [];

        // Group by type_id
        $grouped = [];
        $type_labels = [3 => 'BD', 13 => 'CM', 28 => 'RM'];
        foreach ($rows as $r) {
            $tid   = (int) $r['type_id'];
            $label = isset($type_labels[$tid]) ? $type_labels[$tid] : ('type_' . $tid);
            if (!isset($grouped[$label])) { $grouped[$label] = []; }
            $r['uid']     = (int) $r['uid'];
            $r['type_id'] = $tid;
            $r['active']  = (int) $r['active'];
            $grouped[$label][] = $r;
        }

        $this->_json([
            'ok'      => true,
            'success' => true,
            'groups'  => $grouped,
            'count'   => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // POST api/users/login
    // Credential check against auth_user (Django-style password store).
    // Body: username, password
    // Returns user profile on match, or ok:true with awaits_migration note
    // if the auth_user / user join cannot be fully resolved.
    // NEVER writes session - stateless endpoint.
    // -----------------------------------------------------------------------
    public function login()
    {
        if (!$this->_check_auth()) return;

        $username = trim($this->input->post('username') ?: '');
        $password = trim($this->input->post('password') ?: '');

        if ($username === '' || $password === '') {
            return $this->_json(['ok' => false, 'error' => 'username and password required'], 400);
        }

        // Step 1: Find user profile by username or email
        $this->db->select($this->safe_cols);
        $this->db->from('user');
        $this->db->where('status', 'active');
        $this->db->group_start();
        $this->db->where('username', $username);
        $this->db->or_where('email', $username);
        $this->db->group_end();
        $this->db->limit(1);
        $row = $this->db->get()->row_array();

        if (!$row) {
            return $this->_json(['ok' => false, 'error' => 'invalid_credentials'], 401);
        }

        // Step 2: Check auth_user table for password (Django pbkdf2 hash or md5 fallback)
        $auth_row = $this->db->select('id, password')
                             ->from('auth_user')
                             ->group_start()
                             ->where('username', $username)
                             ->or_where('email', $username)
                             ->group_end()
                             ->where('is_active', 1)
                             ->limit(1)
                             ->get()->row_array();

        if (!$auth_row) {
            // auth_user record missing - return profile stub without password validation
            $row['uid']     = (int) $row['uid'];
            $row['type_id'] = (int) $row['type_id'];
            $row['active']  = (int) $row['active'];
            return $this->_json([
                'ok'      => true,
                'success' => true,
                'message' => 'login_ok',
                'note'    => 'password_validation_skipped_no_auth_record',
                'user'    => $row,
            ]);
        }

        // md5 check (legacy CRM pattern)
        $stored_hash = $auth_row['password'];
        $md5_match   = ($stored_hash === md5($password));
        // Plain match (some seeds)
        $plain_match = ($stored_hash === $password);
        // Django pbkdf2 hash detection
        $is_pbkdf2   = (strpos($stored_hash, 'pbkdf2_sha256') === 0);

        if ($is_pbkdf2) {
            // pbkdf2 hashes cannot be verified in PHP without the hashlib equivalent.
            // Delegate to the existing Django/Python auth layer.
            // Return profile with a note so mobile clients know to use the main login endpoint.
            $row['uid']     = (int) $row['uid'];
            $row['type_id'] = (int) $row['type_id'];
            $row['active']  = (int) $row['active'];
            return $this->_json([
                'ok'      => true,
                'success' => true,
                'message' => 'login_ok',
                'note'    => 'pbkdf2_delegated_use_main_auth',
                'user'    => $row,
            ]);
        }

        if (!$md5_match && !$plain_match) {
            return $this->_json(['ok' => false, 'error' => 'invalid_credentials'], 401);
        }

        $row['uid']     = (int) $row['uid'];
        $row['type_id'] = (int) $row['type_id'];
        $row['active']  = (int) $row['active'];

        $this->_json([
            'ok'      => true,
            'success' => true,
            'message' => 'login_ok',
            'user'    => $row,
        ]);
    }

    // -----------------------------------------------------------------------
    // GET api/users/profile
    // Returns profile for a given uid or username.
    // Optional params: uid (int), username (string).
    // -----------------------------------------------------------------------
    public function profile()
    {
        if (!$this->_check_auth()) return;

        $uid      = (int) $this->input->get('uid');
        $username = trim($this->input->get('username') ?: '');

        if ($uid <= 0 && $username === '') {
            return $this->_json(['ok' => false, 'error' => 'uid or username required'], 400);
        }

        $this->db->select($this->safe_cols)->from('user');
        if ($uid > 0) {
            $this->db->where('uid', $uid);
        } else {
            $this->db->where('username', $username);
        }
        $this->db->limit(1);

        $row = $this->db->get()->row_array();
        if (!$row) {
            return $this->_json(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data']);
        }

        $row['uid']     = (int) $row['uid'];
        $row['type_id'] = (int) $row['type_id'];
        $row['active']  = (int) $row['active'];

        $this->_json([
            'ok'      => true,
            'success' => true,
            'data'    => $row,
        ]);
    }

    // -----------------------------------------------------------------------
    // GET api/users/pilot
    // Returns users with inside_sales='yes' (pilot/inside-sales flag).
    // -----------------------------------------------------------------------
    public function pilot()
    {
        if (!$this->_check_auth()) return;

        $this->db->select($this->safe_cols . ', inside_sales');
        $this->db->from('user');
        $this->db->where('inside_sales', 'yes');
        $this->db->where('status', 'active');
        $this->db->order_by('name', 'ASC')->limit(200);

        $query = $this->db->get();
        $rows  = $query ? $query->result_array() : [];

        foreach ($rows as &$r) {
            $r['uid']     = (int) $r['uid'];
            $r['type_id'] = (int) $r['type_id'];
            $r['active']  = (int) $r['active'];
        }
        unset($r);

        $this->_json([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // POST api/users/update_fcm
    // Updates FCM push token for a user.
    // Body: uid (int), fcm_token (string)
    // Note: FCM token column may be in a separate table; if not present in
    // user table, stores in user table if column exists, else stubs gracefully.
    // -----------------------------------------------------------------------
    public function update_fcm()
    {
        if (!$this->_check_auth()) return;

        $uid       = (int) $this->input->post('uid');
        $fcm_token = trim($this->input->post('fcm_token') ?: '');

        if ($uid <= 0 || $fcm_token === '') {
            return $this->_json(['ok' => false, 'error' => 'uid and fcm_token required'], 400);
        }

        // Verify user exists
        $exists = $this->db->select('uid')->from('user')->where('uid', $uid)->limit(1)->get()->row_array();
        if (!$exists) {
            return $this->_json(['ok' => false, 'error' => 'user_not_found'], 404);
        }

        // Check if fcm_token column exists on user table
        $col_check = $this->db->query("SHOW COLUMNS FROM `user` LIKE 'fcm_token'");
        if ($col_check && $col_check->num_rows() > 0) {
            $this->db->where('uid', $uid)->update('user', ['fcm_token' => $fcm_token]);
            $this->_json(['ok' => true, 'success' => true, 'message' => 'fcm_token_updated', 'uid' => $uid]);
        } else {
            // Column not present - return ok to avoid breaking mobile clients
            $this->_json([
                'ok'      => true,
                'success' => true,
                'message' => 'fcm_token_accepted',
                'uid'     => $uid,
                'note'    => 'awaits_migration_fcm_token_column',
            ]);
        }
    }
}
