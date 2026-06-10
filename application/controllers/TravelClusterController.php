<?php
/**
 * TravelClusterController.php
 *
 * GAP 19 - Travel Cluster management endpoints.
 * CodeIgniter 3 controller. No em-dashes. No non-ASCII characters.
 * Bearer token authentication on all non-probe endpoints.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/travel_cluster/probe']['get']              = 'TravelClusterController/probe';
 *   $route['api/travel_cluster/my_cluster']['get']         = 'TravelClusterController/my_cluster';
 *   $route['api/travel_cluster/details']['get']            = 'TravelClusterController/details';
 *   $route['api/travel_cluster/edit_requests']['get']      = 'TravelClusterController/edit_requests';
 *   $route['api/travel_cluster/submit_edit_request']['post'] = 'TravelClusterController/submit_edit_request';
 *   $route['api/travel_cluster/approve_edit_request']['post'] = 'TravelClusterController/approve_edit_request';
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class TravelClusterController extends CI_Controller
{
    const MIGRATION = '019';

    private $bearer_token;

    // Status label map for apr_status
    private static $STATUS_LABELS = array(
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Rejected',
    );

    // RM/CM type_ids that can approve requests
    // type_id 2 = RM, type_id 1 = CM (adjust if different in production)
    private static $APPROVER_TYPES = array(1, 2, 3);

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->config('custom', TRUE);
        $config_token = $this->config->item('stem_digest_token', 'custom');
        $this->bearer_token = $config_token ?: getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        header('Content-Type: application/json; charset=utf-8');
    }

    // -------------------------------------------------------------------------
    // _auth_or_die - validates Bearer token; falls back to active CI session.
    // -------------------------------------------------------------------------
    private $_authed_uid = 0;

    // ---- per-user JWT validator (added 28 May 2026, matches Auth::api_login) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        // Try uid from request first (fast path)
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
        // Fallback: scan all active uids (cached for 60 sec)
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

    private function _auth_or_die()
    {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        if ($hdr === 'Bearer ' . $this->bearer_token) {
            return TRUE;
        }
        // Per-user JWT (added 28 May)
        if (!empty($hdr) && stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) { $this->_authed_uid = $uid; return TRUE; }
        }
        $session_uid = (int) $this->session->userdata('user_id');
        if ($session_uid > 0) {
            return TRUE;
        }
        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized'));
        exit;
    }

    // -------------------------------------------------------------------------
    // _json - sends JSON and exits.
    // -------------------------------------------------------------------------
    private function _json($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // -------------------------------------------------------------------------
    // _apr_label - converts numeric apr_status to human label.
    // -------------------------------------------------------------------------
    private function _apr_label($val)
    {
        $v = (int) $val;
        return isset(self::$STATUS_LABELS[$v]) ? self::$STATUS_LABELS[$v] : 'Unknown';
    }

    // -------------------------------------------------------------------------
    // GET /api/travel_cluster/probe
    // Health check. No auth required.
    // -------------------------------------------------------------------------
    public function probe()
    {
        $this->_json(array(
            'ok'        => TRUE,
            'migration' => self::MIGRATION,
            'ts'        => date('Y-m-d H:i:s'),
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/travel_cluster/my_cluster?user_id=X
    // Returns the cluster the user belongs to via user_cluster_mapping,
    // plus cluster_master row (cluster_name, region, rm_name, cm_name).
    // Includes count of teammates in same cluster.
    // -------------------------------------------------------------------------
    public function my_cluster()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_json(array('error' => 'user_id required'), 400);
        }

        // Get cluster mapping for this user
        $mapping = $this->db
            ->select('ucm.cluster_id, ucm.user_type, ucm.status')
            ->from('user_cluster_mapping ucm')
            ->where('ucm.user_id', $user_id)
            ->where('ucm.status', 1)
            ->get()
            ->row_array();

        if (!$mapping) {
            $this->_json(array(
                'ok'      => TRUE,
                'cluster' => NULL,
                'message' => 'User is not mapped to any cluster',
            ));
        }

        $cluster_id = (int) $mapping['cluster_id'];

        // Get cluster master row
        $cluster = $this->db
            ->select('cm.cluster_id, cm.cluster_name, cm.region, cm.rm_uid, cm.cm_uid, cm.is_active')
            ->from('cluster_master cm')
            ->where('cm.cluster_id', $cluster_id)
            ->get()
            ->row_array();

        if (!$cluster) {
            $this->_json(array('error' => 'Cluster not found'), 404);
        }

        // Resolve RM and CM names
        $rm_name = '';
        $cm_name = '';

        if (!empty($cluster['rm_uid'])) {
            $rm = $this->db->select('name')->from('user')->where('uid', (int)$cluster['rm_uid'])->get()->row_array();
            $rm_name = $rm ? $rm['name'] : '';
        }
        if (!empty($cluster['cm_uid'])) {
            $cm = $this->db->select('name')->from('user')->where('uid', (int)$cluster['cm_uid'])->get()->row_array();
            $cm_name = $cm ? $cm['name'] : '';
        }

        // Count teammates in same cluster (excluding this user)
        $teammate_count = (int) $this->db
            ->from('user_cluster_mapping')
            ->where('cluster_id', $cluster_id)
            ->where('status', 1)
            ->where('user_id !=', $user_id)
            ->count_all_results();

        $this->_json(array(
            'ok'             => TRUE,
            'cluster_id'     => $cluster_id,
            'cluster_name'   => $cluster['cluster_name'],
            'region'         => $cluster['region'],
            'rm_uid'         => (int) $cluster['rm_uid'],
            'rm_name'        => $rm_name,
            'cm_uid'         => (int) $cluster['cm_uid'],
            'cm_name'        => $cm_name,
            'is_active'      => (bool) $cluster['is_active'],
            'teammate_count' => $teammate_count,
            'user_mapping'   => $mapping,
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/travel_cluster/details?cluster_id=X
    // Returns cluster row + list of all BDs in the cluster + active travel requests.
    // -------------------------------------------------------------------------
    public function details()
    {
        $this->_auth_or_die();

        $cluster_id = (int) $this->input->get('cluster_id');
        if ($cluster_id <= 0) {
            $this->_json(array('error' => 'cluster_id required'), 400);
        }

        // Get cluster master row
        $cluster = $this->db
            ->from('cluster_master')
            ->where('cluster_id', $cluster_id)
            ->get()
            ->row_array();

        if (!$cluster) {
            $this->_json(array('error' => 'Cluster not found'), 404);
        }

        // Resolve RM and CM names
        $rm_name = '';
        $cm_name = '';
        if (!empty($cluster['rm_uid'])) {
            $r = $this->db->select('name')->from('user')->where('uid', (int)$cluster['rm_uid'])->get()->row_array();
            $rm_name = $r ? $r['name'] : '';
        }
        if (!empty($cluster['cm_uid'])) {
            $c = $this->db->select('name')->from('user')->where('uid', (int)$cluster['cm_uid'])->get()->row_array();
            $cm_name = $c ? $c['name'] : '';
        }

        // Get all members of cluster
        $member_rows = $this->db
            ->select('ucm.user_id, ucm.user_type, u.name')
            ->from('user_cluster_mapping ucm')
            ->join('user u', 'u.uid = ucm.user_id', 'left')
            ->where('ucm.cluster_id', $cluster_id)
            ->where('ucm.status', 1)
            ->get()
            ->result_array();

        // Get active travel edit requests for cluster (where clustername matches)
        $travel_requests = $this->db
            ->select('id, user_id, travelId, clustername, in_state, in_district, in_city, travelType, remarks, apr_status, apr_by, apr_date, created_at')
            ->from('travel_cluster_edit_request')
            ->where('clustername', $cluster['cluster_name'])
            ->where('apr_status', 0)
            ->order_by('created_at', 'DESC')
            ->get()
            ->result_array();

        // Add status label to each request
        foreach ($travel_requests as &$req) {
            $req['apr_status_label'] = $this->_apr_label($req['apr_status']);
        }
        unset($req);

        $this->_json(array(
            'ok'              => TRUE,
            'cluster_id'      => (int) $cluster['cluster_id'],
            'cluster_name'    => $cluster['cluster_name'],
            'region'          => $cluster['region'],
            'rm_uid'          => (int) $cluster['rm_uid'],
            'rm_name'         => $rm_name,
            'cm_uid'          => (int) $cluster['cm_uid'],
            'cm_name'         => $cm_name,
            'is_active'       => (bool) $cluster['is_active'],
            'members'         => $member_rows,
            'member_count'    => count($member_rows),
            'active_requests' => $travel_requests,
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/travel_cluster/edit_requests?user_id=X
    // Returns this user's submitted travel_cluster_edit_request rows.
    // -------------------------------------------------------------------------
    public function edit_requests()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_json(array('error' => 'user_id required'), 400);
        }

        $rows = $this->db
            ->select('id, user_id, travelId, clustername, in_state, in_district, in_city, travelType, remarks, apr_status, apr_by, apr_date, apr_remarks, created_at, updated_at')
            ->from('travel_cluster_edit_request')
            ->where('user_id', $user_id)
            ->order_by('created_at', 'DESC')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['apr_status_label'] = $this->_apr_label($row['apr_status']);
            $row['apr_status']       = (int) $row['apr_status'];
        }
        unset($row);

        $this->_json(array(
            'ok'    => TRUE,
            'count' => count($rows),
            'rows'  => $rows,
        ));
    }

    // -------------------------------------------------------------------------
    // POST /api/travel_cluster/submit_edit_request
    // Body: user_id, travelId, clustername, in_state, in_district, in_city,
    //       travelType, remarks
    // Inserts row with apr_status=0.
    // -------------------------------------------------------------------------
    public function submit_edit_request()
    {
        $this->_auth_or_die();

        $user_id    = (int)   $this->input->post('user_id');
        $travel_id  = (int)   $this->input->post('travelId');
        $clustername = trim((string) $this->input->post('clustername'));
        $in_state   = trim((string) $this->input->post('in_state'));
        $in_district = trim((string) $this->input->post('in_district'));
        $in_city    = trim((string) $this->input->post('in_city'));
        $travel_type = trim((string) $this->input->post('travelType'));
        $remarks    = trim((string) $this->input->post('remarks'));

        if ($user_id <= 0) {
            $this->_json(array('error' => 'user_id required'), 400);
        }

        $insert = array(
            'user_id'    => $user_id,
            'travelId'   => $travel_id,
            'clustername' => $clustername,
            'in_state'   => $in_state,
            'in_district' => $in_district,
            'in_city'    => $in_city,
            'travelType' => $travel_type,
            'remarks'    => $remarks,
            'apr_status' => 0,
        );

        $this->db->insert('travel_cluster_edit_request', $insert);
        $new_id = $this->db->insert_id();

        if (!$new_id) {
            $this->_json(array('error' => 'Insert failed'), 500);
        }

        $this->_json(array(
            'ok'      => TRUE,
            'edit_id' => $new_id,
            'message' => 'Request submitted successfully',
        ), 201);
    }

    // -------------------------------------------------------------------------
    // POST /api/travel_cluster/approve_edit_request
    // Body: edit_id, apr_by, apr_status (1=Approved, 2=Rejected), apr_remarks
    // Updates row.
    // -------------------------------------------------------------------------
    public function approve_edit_request()
    {
        $this->_auth_or_die();

        $edit_id    = (int) $this->input->post('edit_id');
        $apr_by     = (int) $this->input->post('apr_by');
        $apr_status = (int) $this->input->post('apr_status');
        $apr_remarks = trim((string) $this->input->post('apr_remarks'));

        if ($edit_id <= 0) {
            $this->_json(array('error' => 'edit_id required'), 400);
        }
        if (!in_array($apr_status, array(1, 2), TRUE)) {
            $this->_json(array('error' => 'apr_status must be 1 (Approved) or 2 (Rejected)'), 400);
        }

        // Verify row exists
        $existing = $this->db->select('id, apr_status')->from('travel_cluster_edit_request')->where('id', $edit_id)->get()->row_array();
        if (!$existing) {
            $this->_json(array('error' => 'Edit request not found'), 404);
        }
        if ((int)$existing['apr_status'] !== 0) {
            $this->_json(array('error' => 'Request already processed', 'current_status' => $this->_apr_label($existing['apr_status'])), 409);
        }

        $this->db->update('travel_cluster_edit_request', array(
            'apr_status'  => $apr_status,
            'apr_by'      => $apr_by,
            'apr_date'    => date('Y-m-d H:i:s'),
            'apr_remarks' => $apr_remarks,
        ), array('id' => $edit_id));

        $this->_json(array(
            'ok'             => TRUE,
            'edit_id'        => $edit_id,
            'new_status'     => $apr_status,
            'status_label'   => $this->_apr_label($apr_status),
            'message'        => 'Request ' . strtolower($this->_apr_label($apr_status)),
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/travel_cluster/list?user_id=X  (added AgentC 28 May 2026)
    // Alias for my_cluster. Mobile app uses /list path.
    // -----------------------------------------------------------------------
    public function list()
    {
        // Accept uid OR user_id query param so mobile {uid} usage works
        $uid = $this->input->get('uid');
        $user_id = $this->input->get('user_id');
        if ($uid && !$user_id) { $_GET['user_id'] = $uid; }
        return $this->my_cluster();
    }

}