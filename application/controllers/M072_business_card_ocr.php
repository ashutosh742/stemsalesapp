<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M072 Business Card OCR Controller
 * Routes (no /api/ prefix):
 *   POST /ocr/upload
 *   GET  /ocr/status
 *   GET  /ocr/result
 *   POST /ocr/create_lead_from_scan
 *   GET  /ocr/scans_for_user
 */
class M072_business_card_ocr extends CI_Controller
{
    private $_bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->_check_auth();
    }
    private function _auth()
    {
        // Load custom config if not loaded
        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(array('ok'=>false,'error'=>'unauthorised')));
            return false;
        }
        return true;
    }



    // ------------------------------------------------------------------ auth


    // ---- per-user JWT validator (copied from Mobile_read_api 28 May 2026) ----
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

    private function _check_auth()
    {
        $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$header) {
            $header = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '';
        }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        if (strpos($header, 'Bearer ') !== 0) {
            $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401);
            exit;
        }
        $token = trim(substr($header, 7));
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        // Accept admin token
        if (hash_equals($secret, $token)) return;
        if (hash_equals($this->_bearer, $token)) return;
        // Accept per-user JWT
        if ($this->_jwt_token_valid($token)) return;
        $this->_json(array('ok' => false, 'error' => 'forbidden'), 403);
        exit;
    }

    // ------------------------------------------------------------------ GET /api/ocr/probe

    public function probe()
    {
        $this->_json(array(
            'ok'        => true,
            'migration' => '072',
            'component' => 'business_card_ocr',
        ));
    }

    // ------------------------------------------------------------------ GET /api/ocr/runs

    public function runs()
    {
        $uid = (int)($this->input->get('uid') ?: 0);
        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }
        $rows = $this->db
                     ->where('uid', $uid)
                     ->order_by('created_at', 'DESC')
                     ->limit(50)
                     ->get('ocr_scan')
                     ->result_array();
        $this->_json(array('ok' => true, 'uid' => $uid, 'count' => count($rows), 'runs' => $rows ?: array()));
    }


    // ------------------------------------------------------------------ helpers

    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function _feature_flag($flag)
    {
        $row = $this->db->get_where('feature_flag', array('flag_name' => $flag, 'enabled' => 1))->row_array();
        return !empty($row);
    }

    // ------------------------------------------------------------------ demo parsed result

    private function _demo_parsed()
    {
        return array(
            'parsed_name'        => 'Demo Contact',
            'parsed_designation' => 'Principal',
            'parsed_company'     => 'Demo Public School',
            'parsed_phone'       => '+91 9876543210',
            'parsed_email'       => 'demo@example.com',
            'parsed_address'     => '',
            'confidence'         => 0.87,
            'raw_text'           => 'Demo Contact | Principal | Demo Public School | +91 9876543210 | demo@example.com',
        );
    }

    // ------------------------------------------------------------------ POST /ocr/upload

    public function upload()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $uid = (int)$this->input->post('uid');
        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }

        // Accept base64 or multipart file URL
        $image_url = '';
        $base64    = trim((string)$this->input->post('image_base64'));
        $file_url  = trim((string)$this->input->post('file_url'));

        if ($base64) {
            // In production: decode and save to disk/S3 and store real URL.
            // Here we store a placeholder path.
            $image_url = 'uploads/ocr/' . $uid . '_' . time() . '.jpg';
        } elseif ($file_url) {
            $image_url = $file_url;
        } else {
            $this->_json(array('ok' => false, 'error' => 'missing_image_base64_or_file_url'), 400);
            return;
        }

        // Insert scan record
        $row = array(
            'uid'        => $uid,
            'image_url'  => $image_url,
            'ocr_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('ocr_scan', $row);
        $scan_id = $this->db->insert_id();

        // Live OCR gate
        if ($this->_feature_flag('ocr_live')) {
            // Production: select enabled provider and call external API.
            // Mark processing and queue async job. Not implemented here.
            $this->db->where('id', $scan_id)->update('ocr_scan', array('ocr_status' => 'processing'));
            $this->_json(array('ok' => true, 'scan_id' => $scan_id, 'status' => 'processing'));
        } else {
            // Demo mode: apply demo parsed result immediately.
            $demo = $this->_demo_parsed();
            $update = array(
                'ocr_status'         => 'complete',
                'ocr_provider'       => 'demo',
                'raw_text'           => $demo['raw_text'],
                'parsed_json'        => json_encode($demo),
                'parsed_name'        => $demo['parsed_name'],
                'parsed_designation' => $demo['parsed_designation'],
                'parsed_company'     => $demo['parsed_company'],
                'parsed_phone'       => $demo['parsed_phone'],
                'parsed_email'       => $demo['parsed_email'],
                'parsed_address'     => $demo['parsed_address'],
                'confidence'         => $demo['confidence'],
                'completed_at'       => date('Y-m-d H:i:s'),
            );
            $this->db->where('id', $scan_id)->update('ocr_scan', $update);
            $this->_json(array(
                'ok'      => true,
                'scan_id' => $scan_id,
                'status'  => 'complete',
                'mode'    => 'demo',
                'parsed'  => $demo,
            ));
        }
    }

    // ------------------------------------------------------------------ GET /ocr/status

    public function status()
    {
        $scan_id = (int)($this->input->get('scan_id') ?: 0);
        if (!$scan_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_scan_id'), 400);
            return;
        }
        $row = $this->db->select('id, uid, ocr_status, ocr_provider, confidence, created_at, completed_at')
                        ->get_where('ocr_scan', array('id' => $scan_id))->row_array();
        if (!$row) {
            $this->_json(array('ok' => false, 'error' => 'not_found'), 404);
            return;
        }
        $this->_json(array('ok' => true, 'scan' => $row));
    }

    // ------------------------------------------------------------------ GET /ocr/result

    public function result()
    {
        $scan_id = (int)($this->input->get('scan_id') ?: 0);
        if (!$scan_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_scan_id'), 400);
            return;
        }
        $row = $this->db->get_where('ocr_scan', array('id' => $scan_id))->row_array();
        if (!$row) {
            $this->_json(array('ok' => false, 'error' => 'not_found'), 404);
            return;
        }
        if ($row['ocr_status'] !== 'complete') {
            $this->_json(array('ok' => false, 'error' => 'scan_not_complete', 'status' => $row['ocr_status']));
            return;
        }
        $this->_json(array(
            'ok'     => true,
            'scan_id' => $scan_id,
            'fields' => array(
                'name'        => $row['parsed_name'],
                'designation' => $row['parsed_designation'],
                'company'     => $row['parsed_company'],
                'phone'       => $row['parsed_phone'],
                'email'       => $row['parsed_email'],
                'address'     => $row['parsed_address'],
                'confidence'  => $row['confidence'],
            ),
            'raw_text' => $row['raw_text'],
        ));
    }

    // ------------------------------------------------------------------ POST /ocr/create_lead_from_scan

    public function create_lead_from_scan()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $scan_id      = (int)$this->input->post('scan_id');
        $uid          = (int)$this->input->post('uid');
        if (!$scan_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_scan_id'), 400);
            return;
        }

        $scan = $this->db->get_where('ocr_scan', array('id' => $scan_id))->row_array();
        if (!$scan) {
            $this->_json(array('ok' => false, 'error' => 'scan_not_found'), 404);
            return;
        }
        if ($scan['ocr_status'] !== 'complete') {
            $this->_json(array('ok' => false, 'error' => 'scan_not_complete'), 400);
            return;
        }

        // Allow caller to override parsed fields
        $name        = trim((string)($this->input->post('name')        ?: $scan['parsed_name']));
        $designation = trim((string)($this->input->post('designation') ?: $scan['parsed_designation']));
        $company     = trim((string)($this->input->post('company')     ?: $scan['parsed_company']));
        $phone       = trim((string)($this->input->post('phone')       ?: $scan['parsed_phone']));
        $email       = trim((string)($this->input->post('email')       ?: $scan['parsed_email']));
        $address     = trim((string)($this->input->post('address')     ?: $scan['parsed_address']));

        // Insert into init_call (lead table)
        $lead = array(
            'cname'       => $name,
            'designation' => $designation,
            'school_name' => $company,
            'mobile'      => $phone,
            'email'       => $email,
            'address'     => $address,
            'uid'         => $uid ?: $scan['uid'],
            'cstatus'     => 1,
            'source'      => 'ocr_scan',
            'created_at'  => date('Y-m-d H:i:s'),
        );
        $this->db->insert('init_call', $lead);
        $cid_id = $this->db->insert_id();

        // Link scan to cid_id
        $this->db->where('id', $scan_id)->update('ocr_scan', array('linked_cid_id' => $cid_id));

        $this->_json(array(
            'ok'     => true,
            'cid_id' => $cid_id,
            'scan_id' => $scan_id,
            'message' => 'Lead created from OCR scan.',
        ));
    }

    // ------------------------------------------------------------------ GET /ocr/scans_for_user

    public function scans_for_user()
    {
        $uid = (int)($this->input->get('uid') ?: 0);
        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }
        $rows = $this->db->select('id, image_url, ocr_status, parsed_name, parsed_company, confidence, created_at, completed_at, linked_cid_id')
                         ->get_where('ocr_scan', array('uid' => $uid))->result_array();
        $this->_json(array('ok' => true, 'scans' => $rows ?: array()));
    }
}
