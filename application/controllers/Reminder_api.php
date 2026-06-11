<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reminder_api
 *
 * Additive mobile reminder surface for the v2144 parity build. Provides a
 * small self-service reminder list backed by the app_reminder table. This
 * controller is strictly additive: it touches only app_reminder and shares
 * the same bearer/JWT auth contract as Mobile_write_api so all mobile
 * endpoints accept one token scheme.
 *
 * Endpoints:
 *   GET  /api/reminder/list?uid=<uid> -> list_reminders
 *   POST /api/reminder/create         -> create_reminder
 *   POST /api/reminder/ack            -> ack_reminder
 *
 * Response shape matches the existing mobile APIs: { ok: true, ... } on
 * success, { ok: false, error: "..." } with HTTP 200 on a clean validation
 * failure. Real server faults still surface as 5xx, but a bad/empty input is
 * never a 500.
 */
class Reminder_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid = 0;

    private $_valid_types = array('Call','Meeting','Follow-up','Proposal','Personal','Other');
    private $_valid_bands = array('9-11','11-1','1-3','3-5','5-7','7-9');

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->output->set_content_type('application/json');
    }

    /* ===================== auth (mirrors Mobile_write_api) ===================== */

    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _bearer_ok() {
        $hdr = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $hdr = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (stripos($hdr, 'Bearer ') !== 0) return false;
        $tok = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $tok)) return true;
        if (hash_equals($this->_known_token, $tok)) return true;
        $uid = $this->_jwt_token_valid($tok);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        return false;
    }

    private function _deny($code, $msg, $extra = null) {
        $this->output->set_status_header($code);
        $resp = array('ok' => false, 'error' => $msg);
        if ($extra !== null) $resp['detail'] = $extra;
        $this->output->set_output(json_encode($resp));
        return;
    }

    /* Clean validation failure: HTTP 200 with ok:false (never a 5xx). */
    private function _fail($msg) {
        $this->output->set_output(json_encode(array('ok' => false, 'error' => $msg)));
        return;
    }

    private function _ok($payload) {
        $payload['ok'] = true;
        $this->output->set_output(json_encode($payload));
        return;
    }

    private function _post($key, $default = null) {
        if (isset($_POST[$key]) && $_POST[$key] !== '') return $_POST[$key];
        static $json = null;
        if ($json === null) {
            $raw = file_get_contents('php://input');
            $json = $raw ? json_decode($raw, true) : array();
            if (!is_array($json)) $json = array();
        }
        if (isset($json[$key]) && $json[$key] !== '') return $json[$key];
        return $default;
    }

    /* Resolve the effective uid: explicit positive uid wins, else JWT uid. */
    private function _effective_uid($raw) {
        $uid = (int)$raw;
        if ($uid > 0) return $uid;
        return (int)$this->_authed_uid;
    }

    /*
     * v2150 (A4): the mobile app speaks title/rtype/rdate/band/message and reads
     * a "reminders" array. The storage columns stay reminder_type/reminder_date/
     * time_band/note (additive, no schema churn). Each row is emitted with BOTH
     * the app field names and the legacy names so old and new clients both work.
     */
    private function _row_out($r) {
        $title = (isset($r['title']) && $r['title'] !== null && $r['title'] !== '')
            ? $r['title'] : $r['reminder_type'];
        $message = ($r['note'] === null) ? '' : $r['note'];
        return array(
            'id'            => (int)$r['id'],
            'uid'           => (int)$r['uid'],
            // app field names (v2150)
            'title'         => ($title === null) ? '' : $title,
            'rtype'         => $r['reminder_type'],
            'rdate'         => $r['reminder_date'],
            'band'          => $r['time_band'],
            'message'       => $message,
            'acknowledged'  => ((int)$r['acknowledged']) ? true : false,
            // legacy field names (back-compat)
            'reminder_type' => $r['reminder_type'],
            'reminder_date' => $r['reminder_date'],
            'time_band'     => $r['time_band'],
            'note'          => $message,
            'created_at'    => $r['created_at'],
        );
    }

    /*
     * Read a field accepting the new app name first, then the legacy name as a
     * fallback. e.g. _field('rtype','reminder_type').
     */
    private function _field($new_key, $old_key, $default = '') {
        $v = $this->_post($new_key, null);
        if ($v !== null && $v !== '') return $v;
        $v = $this->_post($old_key, null);
        if ($v !== null && $v !== '') return $v;
        return $default;
    }

    /* ===================== GET /api/reminder/list ===================== */
    public function list_reminders() {
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $uid = $this->_effective_uid(isset($_GET['uid']) ? $_GET['uid'] : 0);
        if ($uid <= 0) return $this->_fail('uid required');

        $rows = $this->db->query(
            "SELECT id, uid, reminder_type, reminder_date, time_band, note, acknowledged, created_at, title
             FROM app_reminder WHERE uid = ? ORDER BY id DESC",
            array($uid)
        )->result_array();

        $out = array();
        foreach ($rows as $r) $out[] = $this->_row_out($r);
        // v2150 (A4): app reads "reminders"; keep "rows" for back-compat.
        return $this->_ok(array(
            'uid'       => $uid,
            'count'     => count($out),
            'reminders' => $out,
            'rows'      => $out,
        ));
    }

    /* ===================== POST /api/reminder/create ===================== */
    public function create_reminder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $uid = $this->_effective_uid($this->_post('uid', 0));
        if ($uid <= 0) return $this->_fail('uid required');

        // v2150 (A4): accept app field names first (title/rtype/rdate/band/message),
        // fall back to the legacy names (reminder_type/reminder_date/time_band/note).
        $title = trim((string)$this->_field('title', 'reminder_type', ''));
        $type  = trim((string)$this->_field('rtype', 'reminder_type', ''));
        $date  = trim((string)$this->_field('rdate', 'reminder_date', ''));
        $band  = trim((string)$this->_field('band',  'time_band', ''));
        $msg   = (string)$this->_field('message', 'note', '');

        // rtype defaults to title when only a free-text title was sent.
        if ($type === '' && $title !== '') $type = $title;
        if ($title === '' && $type !== '') $title = $type;

        if ($title === '' && $type === '') return $this->_fail('title required');
        if ($date === '') return $this->_fail('rdate required');
        $d = date_create_from_format('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            return $this->_fail('invalid rdate, expected YYYY-MM-DD');
        }
        if ($band === '') return $this->_fail('band required');

        $this->db->insert('app_reminder', array(
            'uid'           => $uid,
            'reminder_type' => ($type === '') ? $title : $type,
            'reminder_date' => $date,
            'time_band'     => $band,
            'note'          => ($msg === '') ? null : $msg,
            'acknowledged'  => 0,
            'title'         => ($title === '') ? null : $title,
        ));
        $id = (int)$this->db->insert_id();
        if ($id <= 0) {
            $err = $this->db->error();
            return $this->_deny(500, 'reminder insert failed', $err);
        }

        $r = $this->db->query(
            "SELECT id, uid, reminder_type, reminder_date, time_band, note, acknowledged, created_at, title
             FROM app_reminder WHERE id = ? LIMIT 1",
            array($id)
        )->row_array();

        return $this->_ok(array('id' => $id, 'reminder' => $this->_row_out($r)));
    }

    /* ===================== POST /api/reminder/ack ===================== */
    public function ack_reminder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_deny(405, 'POST only');
        if (!$this->_bearer_ok()) return $this->_deny(401, 'bad token');

        $uid = $this->_effective_uid($this->_post('uid', 0));
        if ($uid <= 0) return $this->_fail('uid required');

        $id = (int)$this->_post('id', 0);
        if ($id <= 0) return $this->_fail('id required');

        $r = $this->db->query(
            "SELECT id, uid FROM app_reminder WHERE id = ? LIMIT 1",
            array($id)
        )->row_array();
        if (!$r) return $this->_fail('reminder not found');
        if ((int)$r['uid'] !== $uid) return $this->_fail('reminder does not belong to caller');

        $this->db->where('id', $id)->where('uid', $uid)->update('app_reminder', array('acknowledged' => 1));

        return $this->_ok(array('id' => $id, 'acknowledged' => 1));
    }
}
