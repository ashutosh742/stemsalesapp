<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Path_c_api
 * Migration 059 surface.
 * GET /api/path_c/eligibility?bd_uid=<uid>
 */
class Path_c_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Content-Type: application/json');
    }

    public function eligibility() {
        $bd_uid = (int) $this->input->get('bd_uid');
        if ($bd_uid <= 0) {
            echo json_encode(array('ok' => false, 'reason' => 'bd_uid required'));
            return;
        }
        $sql = "SELECT bd_uid, bd_name, bd_email, home_cluster_id,
                       research_events_30d, successful_barge_30d,
                       last_qualifying_event_at, is_eligible
                FROM v_path_c_eligible_bds
                WHERE bd_uid = ?
                LIMIT 1";
        $q = $this->db->query($sql, array($bd_uid));
        $row = $q->row_array();
        if (!$row) {
            echo json_encode(array(
                'ok'         => true,
                'is_eligible'=> false,
                'reason'     => 'BD not in v_path_c_eligible_bds (zero qualifying events in last 30 days)',
                'bd_uid'     => $bd_uid,
            ));
            return;
        }
        $row['ok']                       = true;
        $row['research_count_30d']       = (int)$row['research_events_30d'];
        $row['qualifying_barges_30d']    = (int)$row['successful_barge_30d'];
        $row['is_eligible']              = (int)$row['is_eligible'] === 1;
        $row['reason']                   = $row['is_eligible'] ? null : 'Below threshold (need 5 research + 1 qualifying barge in 30d)';
        echo json_encode($row);
    }

    // GET /api/path_c?uid=X  (AgentC 28 May 2026 - alias for eligibility)
    // Routes /api/path_c to eligibility() using uid from JWT or query string.
    private function _bearer_ok_pc() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers(); if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token  = trim(substr($hdr, 7));
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($secret, $token)) return true;
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
        }
        foreach (array_keys($candidates) as $cuid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$cuid.'|'.$d), $token)) return (int)$cuid;
            }
        }
        $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
        foreach ($rows as $r) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$r->uid.'|'.$d), $token)) return (int)$r->uid;
            }
        }
        return false;
    }

    public function index()
    {
        $uid_from_jwt = $this->_bearer_ok_pc();
        // Resolve bd_uid: from query string first, then from JWT
        $bd_uid = (int)$this->input->get('bd_uid');
        if ($bd_uid <= 0) $bd_uid = (int)$this->input->get('uid');
        if ($bd_uid <= 0 && $uid_from_jwt) $bd_uid = (int)$uid_from_jwt;
        if ($bd_uid <= 0) {
            echo json_encode(array('ok'=>false,'reason'=>'bd_uid or uid required'));
            return;
        }
        // Delegate to eligibility()
        $_GET['bd_uid'] = $bd_uid;
        return $this->eligibility();
    }

}