<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GovDirectory_api.php  (Phase 2 - Agent F - 2026-06-08)
 *
 * G3 Minister / PSU / DMFT District Directory
 *
 * Reference layer for government contacts relevant to CSR / STEM outreach.
 * Starter dataset seeded on table creation (editable, not authoritative).
 *
 * Table: gov_directory (id, name, portfolio, body_type ENUM, state, district, notes)
 *
 * Endpoints:
 *   GET  /api/govdir/list?body_type=&state=&q=   Search/filter
 *   GET  /api/govdir/get?id=                     Single record
 *   POST /api/govdir/save                        Add or edit
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Output: ASCII only. No em/en-dashes.
 *
 * Author: STEM Phase 2 Agent F  2026-06-08
 */
class GovDirectory_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid  = 0;

    // Starter seed data - public domain / well known information only
    private $_seed = array(
        // ---- UNION MINISTERS (as of 2024-25 NDA cabinet) ----
        // body_type = MINISTER, state = central
        array('MINISTER', 'Narendra Modi',           'Prime Minister and Minister of Finance (additional charge)', 'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Rajnath Singh',            'Defence',                                                    'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Amit Shah',                'Home Affairs and Cooperation',                               'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Nirmala Sitharaman',       'Finance and Corporate Affairs',                              'Central', '', 'Cabinet rank; presents Union Budget'),
        array('MINISTER', 'S. Jaishankar',            'External Affairs',                                           'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Nitin Gadkari',            'Road Transport and Highways',                                'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Piyush Goyal',             'Commerce and Industry',                                      'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Dharmendra Pradhan',       'Education',                                                  'Central', '', 'Cabinet rank; key for STEM outreach'),
        array('MINISTER', 'Jyotiraditya Scindia',     'Civil Aviation',                                             'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Ashwini Vaishnaw',         'Railways, Electronics and IT',                               'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Manohar Lal Khattar',      'Housing and Urban Affairs, Power',                           'Central', '', 'Cabinet rank'),
        array('MINISTER', 'G. Kishan Reddy',          'Coal and Mines',                                             'Central', '', 'Cabinet rank; relevant for DMFT states'),
        array('MINISTER', 'H.D. Kumaraswamy',         'Heavy Industries and Steel',                                 'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Giriraj Singh',            'Textiles',                                                   'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Sarbananda Sonowal',       'Ports, Shipping and Waterways, Ayush',                       'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Kiren Rijiju',             'Parliamentary Affairs, Minority Affairs',                    'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Annpurna Devi',            'Women and Child Development',                                 'Central', '', 'Cabinet rank'),
        array('MINISTER', 'Shivraj Singh Chouhan',    'Agriculture and Farmers Welfare',                            'Central', '', 'Cabinet rank'),

        // ---- CENTRAL PSUs ----
        array('PSU', 'ONGC (Oil and Natural Gas Corporation)',             'Energy - Oil and Gas Exploration',      'Central', '', 'Schedule A; CSR obligation significant'),
        array('PSU', 'NTPC Limited',                                       'Energy - Power Generation',             'Central', '', 'Schedule A; large CSR spender'),
        array('PSU', 'SAIL (Steel Authority of India Limited)',             'Steel Manufacturing',                   'Central', '', 'Schedule A; major mining state presence'),
        array('PSU', 'BHEL (Bharat Heavy Electricals Limited)',            'Capital Goods - Power Equipment',       'Central', '', 'Schedule A'),
        array('PSU', 'GAIL (India) Limited',                               'Energy - Natural Gas Transmission',     'Central', '', 'Schedule A; Navratna'),
        array('PSU', 'IOCL (Indian Oil Corporation Limited)',              'Energy - Oil Refining and Marketing',   'Central', '', 'Schedule A; Maharatna'),
        array('PSU', 'Coal India Limited',                                  'Mining - Coal',                         'Central', '', 'Schedule A; Maharatna; DMFT districts'),
        array('PSU', 'PFC (Power Finance Corporation)',                     'Finance - Power Sector Lending',        'Central', '', 'Schedule A; Navratna'),
        array('PSU', 'REC Limited',                                         'Finance - Rural Electrification',       'Central', '', 'Schedule A; Navratna'),
        array('PSU', 'Hindustan Zinc Limited',                              'Mining - Zinc and Lead',                'Rajasthan','', 'Majority govt through HZL'),
        array('PSU', 'NMDC Limited',                                        'Mining - Iron Ore',                     'Chhattisgarh','', 'Schedule A; large DMFT presence Odisha/CG'),
        array('PSU', 'NALCO (National Aluminium Company)',                  'Mining - Aluminium',                    'Odisha',   '', 'Navratna; strong Odisha presence'),
        array('PSU', 'Mahanadi Coalfields Limited',                         'Mining - Coal',                         'Odisha',   '', 'Subsidiary of Coal India; DMFT district'),
        array('PSU', 'HPCL (Hindustan Petroleum Corporation Limited)',      'Energy - Oil Refining and Marketing',   'Central', '', 'Schedule A; Navratna'),
        array('PSU', 'BPCL (Bharat Petroleum Corporation Limited)',         'Energy - Oil Refining and Marketing',   'Central', '', 'Schedule A; Navratna'),

        // ---- DMFT DISTRICTS (key mining states) ----
        // Odisha
        array('DMFT_DISTRICT', 'Odisha - Keonjhar (Kendujhar)',   'DMFT - Iron Ore Mining District', 'Odisha',       'Keonjhar',    'Major iron ore; NMDC, Tata Steel operations'),
        array('DMFT_DISTRICT', 'Odisha - Sundargarh',             'DMFT - Coal and Iron Ore',        'Odisha',       'Sundargarh',  'MCL operations; large tribal population'),
        array('DMFT_DISTRICT', 'Odisha - Jharsuguda',             'DMFT - Coal Mining',              'Odisha',       'Jharsuguda',  'MCL; Vedanta smelter; education priority'),
        array('DMFT_DISTRICT', 'Odisha - Angul',                  'DMFT - Coal and Aluminium',       'Odisha',       'Angul',       'NALCO, MCL; significant DMFT corpus'),
        array('DMFT_DISTRICT', 'Odisha - Jajpur',                 'DMFT - Chrome and Iron',          'Odisha',       'Jajpur',      'TISCO, IMFA; CSR opportunity'),
        // Jharkhand
        array('DMFT_DISTRICT', 'Jharkhand - East Singhbhum',      'DMFT - Iron and Coal Mining',     'Jharkhand',    'East Singhbhum', 'Tata Steel, SAIL; education CSR area'),
        array('DMFT_DISTRICT', 'Jharkhand - Dhanbad',             'DMFT - Coal Mining',              'Jharkhand',    'Dhanbad',        'Coal India; large mining workforce'),
        array('DMFT_DISTRICT', 'Jharkhand - Ramgarh',             'DMFT - Coal Mining',              'Jharkhand',    'Ramgarh',        'MCL, BCCL; education needs'),
        array('DMFT_DISTRICT', 'Jharkhand - West Singhbhum',      'DMFT - Iron Ore',                 'Jharkhand',    'West Singhbhum', 'SAIL mines; tribal education outreach'),
        // Chhattisgarh
        array('DMFT_DISTRICT', 'CG - Durg',                       'DMFT - Iron and Steel',           'Chhattisgarh', 'Durg',           'BSP-SAIL; significant DMFT fund'),
        array('DMFT_DISTRICT', 'CG - Korba',                      'DMFT - Coal and Aluminium',       'Chhattisgarh', 'Korba',          'SECL, NALCO; education deficit'),
        array('DMFT_DISTRICT', 'CG - Raigarh',                    'DMFT - Coal Mining',              'Chhattisgarh', 'Raigarh',        'SECL; tribal blocks; high priority'),
        array('DMFT_DISTRICT', 'CG - Bastar',                     'DMFT - Iron Ore',                 'Chhattisgarh', 'Bastar',         'NMDC Bailadila; LWE affected; CSR priority'),
        array('DMFT_DISTRICT', 'CG - Bilaspur',                   'DMFT - Coal and Power',           'Chhattisgarh', 'Bilaspur',       'NTPC Sipat; SECL; STEM opportunity'),
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
        $this->_ensure_table();
    }

    // -------------------------------------------------------------------------
    // TABLE BOOTSTRAP + SEED
    // -------------------------------------------------------------------------
    private function _ensure_table() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS gov_directory (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name       VARCHAR(255) NOT NULL,
                portfolio  VARCHAR(500) NOT NULL,
                body_type  ENUM('MINISTER','PSU','DMFT_DISTRICT') NOT NULL,
                state      VARCHAR(80)  NOT NULL DEFAULT '',
                district   VARCHAR(120) NOT NULL DEFAULT '',
                notes      TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_body_type (body_type),
                INDEX idx_state (state)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Seed only if table is empty
        $cnt = $this->db->count_all('gov_directory');
        if ($cnt == 0) {
            foreach ($this->_seed as $row) {
                $this->db->insert('gov_directory', array(
                    'body_type' => $row[0],
                    'name'      => $row[1],
                    'portfolio' => $row[2],
                    'state'     => $row[3],
                    'district'  => $row[4],
                    'notes'     => $row[5],
                ));
            }
        }
    }

    // -------------------------------------------------------------------------
    // GET /api/govdir/list?body_type=&state=&q=
    // -------------------------------------------------------------------------
    public function list_index() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->_json(array('ok' => false, 'error' => 'GET required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $body_type = trim($this->input->get('body_type') ?: '');
        $state     = trim($this->input->get('state')     ?: '');
        $q         = trim($this->input->get('q')         ?: '');

        $allowed_types = array('MINISTER', 'PSU', 'DMFT_DISTRICT');

        $this->db->from('gov_directory');

        if (!empty($body_type)) {
            if (!in_array(strtoupper($body_type), $allowed_types)) {
                return $this->_json(array('ok' => false, 'error' => 'body_type must be MINISTER, PSU, or DMFT_DISTRICT'), 422);
            }
            $this->db->where('body_type', strtoupper($body_type));
        }
        if (!empty($state)) {
            $this->db->like('state', $state);
        }
        if (!empty($q)) {
            $this->db->group_start()
                ->like('name', $q)
                ->or_like('portfolio', $q)
                ->or_like('notes', $q)
                ->group_end();
        }

        $this->db->order_by('body_type', 'ASC')->order_by('name', 'ASC');
        $rows = $this->db->get()->result_array();

        if (empty($rows)) {
            return $this->_json(array('ok' => true, 'empty' => true, 'count' => 0, 'entries' => array()));
        }

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'        => (int) $r['id'],
                'name'      => $r['name'],
                'portfolio' => $r['portfolio'],
                'body_type' => $r['body_type'],
                'state'     => $r['state'],
                'district'  => $r['district'],
                'notes'     => $r['notes'],
            );
        }
        $this->_json(array('ok' => true, 'count' => count($out), 'entries' => $out));
    }

    // -------------------------------------------------------------------------
    // GET /api/govdir/get?id=
    // -------------------------------------------------------------------------
    public function get() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->_json(array('ok' => false, 'error' => 'GET required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $id = (int) $this->input->get('id');
        if ($id <= 0) {
            return $this->_json(array('ok' => false, 'error' => 'id is required'), 422);
        }

        $row = $this->db->get_where('gov_directory', array('id' => $id))->row_array();
        if (empty($row)) {
            return $this->_json(array('ok' => false, 'error' => 'entry not found'), 404);
        }

        $this->_json(array(
            'ok'    => true,
            'entry' => array(
                'id'        => (int) $row['id'],
                'name'      => $row['name'],
                'portfolio' => $row['portfolio'],
                'body_type' => $row['body_type'],
                'state'     => $row['state'],
                'district'  => $row['district'],
                'notes'     => $row['notes'],
            ),
        ));
    }

    // -------------------------------------------------------------------------
    // POST /api/govdir/save
    // Add or edit an entry. id in body = update; omit = create.
    // Required: name, portfolio, body_type
    // -------------------------------------------------------------------------
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->_json(array('ok' => false, 'error' => 'POST required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $body = $this->_post_body();

        $name      = isset($body['name'])      ? trim($body['name'])      : '';
        $portfolio = isset($body['portfolio']) ? trim($body['portfolio']) : '';
        $body_type = isset($body['body_type']) ? strtoupper(trim($body['body_type'])) : '';
        $state     = isset($body['state'])     ? trim($body['state'])     : '';
        $district  = isset($body['district'])  ? trim($body['district'])  : '';
        $notes     = isset($body['notes'])     ? trim($body['notes'])     : '';

        $allowed_types = array('MINISTER', 'PSU', 'DMFT_DISTRICT');

        if (empty($name)) {
            return $this->_json(array('ok' => false, 'error' => 'name is required'), 422);
        }
        if (empty($portfolio)) {
            return $this->_json(array('ok' => false, 'error' => 'portfolio is required'), 422);
        }
        if (!in_array($body_type, $allowed_types)) {
            return $this->_json(array('ok' => false, 'error' => 'body_type must be MINISTER, PSU, or DMFT_DISTRICT'), 422);
        }

        $data = array(
            'name'      => $name,
            'portfolio' => $portfolio,
            'body_type' => $body_type,
            'state'     => $state,
            'district'  => $district,
            'notes'     => $notes,
        );

        $id = isset($body['id']) ? (int) $body['id'] : 0;
        if ($id > 0) {
            $exists = $this->db->get_where('gov_directory', array('id' => $id))->row_array();
            if (empty($exists)) {
                return $this->_json(array('ok' => false, 'error' => 'entry not found'), 404);
            }
            $this->db->where('id', $id)->update('gov_directory', $data);
            $this->_json(array('ok' => true, 'action' => 'updated', 'id' => $id));
        } else {
            $this->db->insert('gov_directory', $data);
            $new_id = $this->db->insert_id();
            $this->_json(array('ok' => true, 'action' => 'created', 'id' => $new_id));
        }
    }

    // -------------------------------------------------------------------------
    // Auth helpers
    // -------------------------------------------------------------------------
    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env   = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        $uid = $this->_jwt_valid($token);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        // rimlyproof_bearerdelegate_20260608: also accept per-user login token via shared BearerAuth library (additive)
        try {
            $CI =& get_instance();
            if (!isset($CI->bearerauth)) { $CI->load->library('BearerAuth'); }
            $___ba = $CI->bearerauth->resolve();
            if (!empty($___ba['ok']) && !empty($___ba['uid'])) {
                if (property_exists($this, '_authed_uid')) { $this->_authed_uid = (int)$___ba['uid']; }
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    private function _jwt_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $days   = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $cands  = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','by_uid','user_id') as $k) {
            if (!empty($_GET[$k]))  $cands[(int)$_GET[$k]]  = 1;
            if (!empty($_POST[$k])) $cands[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($cands) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all = null;
        if ($all === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all  = array();
            foreach ($rows as $r) $all[] = (int)$r->uid;
        }
        foreach ($all as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function _post_body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $d = json_decode($raw, true);
            if (is_array($d)) return $d;
        }
        return $_POST;
    }
}
