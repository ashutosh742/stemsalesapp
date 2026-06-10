<?php
/**
 * User_model
 * Minimal stub for controllers that call $this->User_model->get_user_by_token()
 * Resolves against:
 *   1. Master bearer token (returns system/admin actor)
 *   2. api_token table (active, not expired)
 *   3. user table api_token field (legacy)
 * Added 2026-06-06 GROUP C fix (StakeholderMap needs this)
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    /**
     * get_user_by_token($token)
     * Used by StakeholderMapController::_require_auth()
     * Returns user-like array with uid, type_id, first_name, last_name
     * or null if token is invalid.
     */
    public function get_user_by_token($token)
    {
        if (empty($token)) return null;

        // 1. Master bearer / known token check
        $env_token   = getenv('STEM_DIGEST_TOKEN');
        $known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (($env_token && hash_equals($env_token, $token)) || hash_equals($known_token, $token)) {
            return array(
                'uid'        => 0,
                'type_id'    => 1,
                'first_name' => 'System',
                'last_name'  => 'Bearer',
                'role'       => 'superadmin',
            );
        }

        // 2. api_token table join with user_details (which has 'name' col, no first/last)
        try {
            $row = $this->db->query(
                'SELECT at.uid, at.role,
                        COALESCE(SUBSTRING_INDEX(ud.name," ",1),"API")  AS first_name,
                        COALESCE(TRIM(SUBSTR(ud.name,INSTR(ud.name," ")+1)),"User") AS last_name,
                        COALESCE(ud.type_id, 3) AS type_id
                 FROM api_token at
                 LEFT JOIN user_details ud ON ud.user_id = at.uid
                 WHERE at.token = ? AND at.active = 1
                   AND (at.expires_at IS NULL OR at.expires_at > NOW())
                 LIMIT 1',
                array($token)
            )->row_array();

            if ($row) return $row;
        } catch (Exception $e) {
            log_message('error', 'User_model::get_user_by_token api_token error: ' . $e->getMessage());
        }

        // 3. user table api_token field fallback (legacy some controllers use this)
        // GUARD 2026-06-06: CI3 db_debug=TRUE turns a missing-column error into a fatal
        // exit() that bypasses try/catch. Only run this branch if the column exists.
        try {
            if (!$this->db->field_exists('api_token', 'user')) {
                return null;
            }
            $urow = $this->db->where('api_token', $token)->get('user')->row_array();
            if ($urow) {
                $name  = isset($urow['name']) ? $urow['name'] : '';
                $parts = explode(' ', trim($name), 2);
                return array(
                    'uid'        => (int)$urow['uid'],
                    'type_id'    => (int)$urow['type_id'],
                    'first_name' => $parts[0],
                    'last_name'  => isset($parts[1]) ? $parts[1] : '',
                    'role'       => 'BD',
                );
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_user_by_token user table error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * get_user($uid) - basic uid lookup
     */
    public function get_user($uid)
    {
        try {
            return $this->db->where('uid', (int)$uid)->get('user')->row_array() ?: null;
        } catch (Exception $e) {
            return null;
        }
    }
}
