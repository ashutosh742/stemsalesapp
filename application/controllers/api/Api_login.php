<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_login
 *
 * JSON wrappers around the existing Menu/login flow plus a session-info
 * helper. The role chip the mobile app reads from these endpoints drives
 * which stub landing screen renders.
 *
 * Consumed by mobile stub routes (role-based landings):
 *   - /sc-plan-monitoring         (needs role = SC)
 *   - /sc-notifications           (needs role = SC)
 *   - /rm-early-planner-request   (needs role = RM)
 *   - All BD and CM stubs read session() on mount.
 *
 * Plain English. No em-dashes.
 */
class Api_login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model');
    }

    /**
     * POST userid + password (form-encoded). Sets PHPSESSID cookie, returns
     * the same user object the mobile app caches.
     */
    public function login() {
        $userid   = $this->input->post('userid');
        $password = $this->input->post('password');
        if (!$userid || !$password) {
            return $this->_json(['error' => 'missing credentials'], 400);
        }
        $user = $this->Menu_model->authenticate($userid, $password);
        if (!$user) {
            return $this->_json(['error' => 'invalid credentials'], 401);
        }
        // Mirror the production Menu/login session payload.
        $this->session->set_userdata('user', [
            'user_id'   => (int) $user['user_id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'type_id'   => (int) $user['type_id'],
            'role_name' => $user['role_name'],
            'cluster_id'=> isset($user['cluster_id']) ? (int) $user['cluster_id'] : null,
        ]);
        $this->_json([
            'user' => $this->session->userdata('user'),
            'note' => 'PHPSESSID cookie is set. Send it on every subsequent request.',
        ]);
    }

    /**
     * GET session info as JSON. Used by every stub mobile screen on mount
     * so the right role-specific sidebar and landing renders.
     */
    public function session_info() {
        $user = $this->session->userdata('user');
        if (!$user) return $this->_json(['error' => 'no session'], 401);
        $this->_json(['user' => $user]);
    }

    /**
     * POST logout. Destroys session. Mobile app then routes back to /login.
     */
    public function logout() {
        $this->session->sess_destroy();
        $this->_json(['ok' => true]);
    }

    private function _json($data, $status = 200) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
