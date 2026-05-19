<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DayCeremonyController
 * Migration 038 - Day Ceremony v2 strict gate API.
 * Bearer-authed staging endpoints. Never writes to production user_day or daily_planner.
 *
 * Routes (add to config/routes.php):
 *   $route['api/day_ceremony/start_check']  = 'DayCeremonyController/start_check';
 *   $route['api/day_ceremony/start_commit'] = 'DayCeremonyController/start_commit';
 *   $route['api/day_ceremony/close_check']  = 'DayCeremonyController/close_check';
 *   $route['api/day_ceremony/close_commit'] = 'DayCeremonyController/close_commit';
 *   $route['api/day_ceremony/anchor']       = 'DayCeremonyController/anchor';
 *   $route['api/day_ceremony/probe']        = 'DayCeremonyController/probe';
 */
class DayCeremonyController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/DayCeremonyGuard_model', 'guard');
        header('Content-Type: application/json');
        $this->_auth();
    }

    private function _auth() {
        $hdrs = getallheaders();
        $auth = isset($hdrs['Authorization']) ? $hdrs['Authorization'] : (isset($hdrs['authorization']) ? $hdrs['authorization'] : '');
        $expected = getenv('STEM_DIGEST_TOKEN') ?: 'replace-me';
        if ('Bearer ' . $expected !== $auth) {
            http_response_code(401);
            echo json_encode(array('error' => 'unauthorized'));
            exit;
        }
    }

    public function probe() {
        echo json_encode(array('ok' => true, 'migration' => '038', 'name' => 'day_ceremony_v2'));
    }

    public function start_check() {
        $uid = intval($this->input->post('user_id'));
        $lat = floatval($this->input->post('lat'));
        $lng = floatval($this->input->post('lng'));
        $exif = $this->input->post('photo_exif_taken_at');
        echo json_encode($this->guard->day_start_check($uid, $lat, $lng, $exif));
    }

    public function start_commit() {
        $uid = intval($this->input->post('user_id'));
        $lat = floatval($this->input->post('lat'));
        $lng = floatval($this->input->post('lng'));
        $url = $this->input->post('photo_url');
        $exif = $this->input->post('photo_exif_taken_at');
        echo json_encode($this->guard->day_start_commit($uid, $lat, $lng, $url, $exif));
    }

    public function close_check() {
        $uid = intval($this->input->post('user_id') ?: $this->input->get('user_id'));
        echo json_encode($this->guard->day_close_check($uid));
    }

    public function close_commit() {
        $uid = intval($this->input->post('user_id'));
        $lat = floatval($this->input->post('lat'));
        $lng = floatval($this->input->post('lng'));
        $url = $this->input->post('photo_url');
        echo json_encode($this->guard->day_close_commit($uid, $lat, $lng, $url));
    }

    public function anchor() {
        $uid = intval($this->input->post('user_id'));
        $lat = floatval($this->input->post('lat'));
        $lng = floatval($this->input->post('lng'));
        $radius = floatval($this->input->post('radius_km') ?: 5.0);
        $label = $this->input->post('anchor_label') ?: 'home';
        $this->db->query(
            "INSERT INTO day_start_home_anchor_v2 (user_id, anchor_label, lat, lng, radius_km, active) VALUES (?,?,?,?,?,1)
             ON DUPLICATE KEY UPDATE lat=VALUES(lat), lng=VALUES(lng), radius_km=VALUES(radius_km), active=1",
            array($uid, $label, $lat, $lng, $radius)
        );
        echo json_encode(array('ok' => true));
    }
}
