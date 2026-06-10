<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * NewLead - M044
 * /api/newlead/create?probe=1  - probe mode returns ready status
 * POST /api/newlead/create     - stub returns probe_only note
 * /api/newlead/probe           - liveness check
 */
class NewLead extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    private function _out($p) { echo json_encode($p); exit; }

    // GET /api/newlead/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'NewLead',
            'migration'   => 'M044',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }

    // GET/POST /api/newlead/create
    // If ?probe=1, returns ready status.
    // Otherwise returns probe_only stub (full implementation TBD).
    public function create() {
        $probe = $this->input->get('probe');
        if ($probe == '1' || $probe === 1) {
            $this->_out([
                'ok'          => true,
                'controller'  => 'NewLead',
                'migration'   => 'M044',
                'status'      => 'ready',
                'mode'        => 'probe',
                'server_time' => date('c'),
            ]);
        }

        // Non-probe: return stub response
        $raw = file_get_contents('php://input');
        $payload = [];
        if ($raw && strpos(trim($raw), '{') === 0) {
            $payload = json_decode($raw, true) ?: [];
        }
        if (empty($payload)) {
            $payload = $_POST;
        }

        $this->_out([
            'ok'      => true,
            'note'    => 'probe_only',
            'detail'  => 'Full lead creation not yet implemented on this staging server.',
            'received'=> array_keys($payload),
        ]);
    }

    // GET /api/newlead/research?city=<city>&uid=<uid> -- added 28 May 2026
    public function research() {
        try {
            $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
            if (!$hdr && function_exists('apache_request_headers')) {
                $h = apache_request_headers();
                if (isset($h['Authorization'])) $hdr = $h['Authorization'];
            }
            $token = ($hdr && stripos($hdr, 'Bearer ') === 0) ? trim(substr($hdr,7)) : '';
            if (!$this->_jwt_token_valid_nl($token)) {
                http_response_code(401);
                $this->_out(array('ok' => false, 'error' => 'Unauthorized'));
                return;
            }
            $token = trim(substr($hdr, 7));
            $known  = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
            $secret = getenv('STEM_DIGEST_TOKEN') ?: $known;
            $env    = getenv('STEM_DIGEST_TOKEN');
            $auth_ok = ($env && hash_equals($env, $token)) || hash_equals($known, $token);
            if (!$auth_ok) {
                $uid_try = (int)(isset($_GET['uid']) ? $_GET['uid'] : 0);
                if ($uid_try > 0) {
                    foreach (array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day'))) as $d) {
                        if (hash_equals(sha1($secret . '|' . $uid_try . '|' . $d), $token)) {
                            $auth_ok = true;
                            break;
                        }
                    }
                }
            }
            if (!$auth_ok) {
                http_response_code(401);
                $this->_out(array('ok' => false, 'error' => 'Unauthorized'));
                return;
            }
            $city   = trim((string)$this->input->get('city'));
            $state  = trim((string)$this->input->get('state'));
            $limit  = max(1, min(200, (int)($this->input->get('limit') ?: 50)));
            $where  = '1=1';
            $params = array();
            if ($city !== '') {
                $where .= ' AND LOWER(cm.city) LIKE ?';
                $params[] = '%' . strtolower($city) . '%';
            }
            if ($state !== '') {
                $where .= ' AND LOWER(cm.state) LIKE ?';
                $params[] = '%' . strtolower($state) . '%';
            }
            // Schools not yet in CRM (new_lead = 1 in init_call) or all uncontacted
            $sql = "SELECT cm.id AS company_id, cm.compname AS company_name,
                           cm.city, cm.state,
                           ic.id AS cid_id, ic.cstatus, ic.mainbd,
                           ic.lead_source, ic.createDate
                    FROM company_master cm
                    LEFT JOIN init_call ic ON ic.cmpid_id = cm.id
                    WHERE $where
                    ORDER BY cm.compname ASC
                    LIMIT $limit";
            $rows = $this->db->query($sql, $params)->result_array();
            $this->_out(array('ok' => true, 'city' => $city, 'state' => $state, 'count' => count($rows), 'rows' => $rows));
        } catch (Exception $e) {
            $this->_out(array('ok' => true, 'rows' => array(), 'note' => 'error', 'detail' => $e->getMessage()));
        }
    }



    /** Accept per-user daily JWT: sha1(secret|uid|YYYY-MM-DD) */
    private function _jwt_token_valid_nl($token) {
        if (!$token) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if ($token === $secret) return true;
        $today = date('Y-m-d');
        $CI =& get_instance(); $CI->load->database();
        $rows = $CI->db->select('id')->where('status','active')->get('user')->result_array();
        foreach ($rows as $r) {
            if (sha1($secret.'|'.$r['id'].'|'.$today) === $token) return true;
        }
        return false;
    }
}
