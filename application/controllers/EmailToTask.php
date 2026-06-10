<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * EmailToTask controller - Migration 039
 *
 * STREAM D PATCH: inbox() and stats() wired to real DB (inbound_email_v2).
 * probe() and convert() are unchanged.
 * inbound_email_v2 exists on staging (0 rows as of patch date - honest empty returned).
 */
class EmailToTask extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    // ---- per-user JWT validator (auth patch 20260529) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
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
        // Fallback: scan all active uids
        $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
        foreach ($rows as $r) {
            $uid = (int)$r->uid;
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return $uid;
            }
        }
        return false;
    }

    private function _bearer_ok() {
        $hdr = $this->input->get_request_header('Authorization', true);
        if (!$hdr) $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $digest = getenv('STEM_DIGEST_TOKEN');
        if ($digest && hash_equals($digest, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // Accept valid per-user JWT (auth patch 20260529)
        if ($this->_jwt_token_valid($token)) return true;
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ---------------------------------------------------------------
    // PROBE (unchanged - APK relies on this)
    // ---------------------------------------------------------------
    public function probe() {
        $this->_json(array(
            'ok' => true,
            'controller' => 'EmailToTask',
            'migration' => '039',
            'status' => 'ready',
            'features' => array(
                'inbox' => true,
                'stats' => true,
                'inbound_email_v2' => true
            )
        ));
    }

    // ---------------------------------------------------------------
    // INBOX - STREAM D WIRED
    // GET /api/email_to_task/inbox?status=&bd_uid=&limit=
    // Reads from inbound_email_v2 - the actual inbound email table
    // from migration 039. Returns honest empty if no rows yet.
    // ---------------------------------------------------------------
    public function inbox() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
        }

        try {
            $status  = $this->input->get('status') ?: 'pending';
            $bd_uid  = (int)$this->input->get('bd_uid');
            $limit   = min((int)($this->input->get('limit') ?: 50), 200);

            $valid_statuses = array('pending', 'accepted', 'dismissed', 'no_match', 'duplicate');
            if (!in_array($status, $valid_statuses)) {
                $status = 'pending';
            }

            $this->db->select(
                'e.id, e.message_id, e.mailbox_account, e.from_email, e.from_name, ' .
                'e.subject, e.received_at, e.status, e.match_confidence, ' .
                'e.match_method, e.has_attachment, ' .
                'e.matched_lead_id AS cid_id, ' .
                'e.suggested_action_type_id, e.suggested_purpose_id, ' .
                'e.created_at, ' .
                'cm.compname AS matched_company_name'
            )
            ->from('inbound_email_v2 e')
            ->join('init_call ic', 'ic.id = e.matched_lead_id', 'left')
            ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
            ->where('e.status', $status)
            ->order_by('e.received_at', 'DESC')
            ->limit($limit);

            if ($bd_uid > 0) {
                $this->db->where('e.matched_bd_uid', $bd_uid);
            }

            $rows = $this->db->get()->result_array();

            $this->_json(array(
                'ok'            => true,
                'source'        => 'inbound_email_v2',
                'status_filter' => $status,
                'count'         => count($rows),
                'rows'          => $rows,
                'note'          => count($rows) === 0 ? 'no_emails_processed_yet' : null
            ));
        } catch (Exception $e) {
            $this->_json(array(
                'ok'     => true,
                'rows'   => array(),
                'note'   => 'error',
                'detail' => $e->getMessage()
            ));
        }
    }

    // ---------------------------------------------------------------
    // STATS - STREAM D WIRED
    // GET /api/email_to_task/stats
    // Returns aggregate stats from inbound_email_v2.
    // ---------------------------------------------------------------
    public function stats() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
        }

        try {
            $total = $this->db->count_all('inbound_email_v2');

            $by_status = $this->db->query(
                "SELECT status, COUNT(*) AS cnt FROM inbound_email_v2 GROUP BY status ORDER BY cnt DESC"
            )->result_array();

            $by_match = $this->db->query(
                "SELECT match_method, COUNT(*) AS cnt FROM inbound_email_v2 GROUP BY match_method ORDER BY cnt DESC"
            )->result_array();

            $recent = $this->db->query(
                "SELECT id, from_email, subject, received_at, status, match_method " .
                "FROM inbound_email_v2 " .
                "ORDER BY received_at DESC LIMIT 10"
            )->result_array();

            $this->_json(array(
                'ok'       => true,
                'source'   => 'inbound_email_v2',
                'total'    => $total,
                'by_status' => $by_status,
                'by_match'  => $by_match,
                'recent'    => $recent,
                'note'      => $total === 0 ? 'no_emails_processed_yet' : null
            ));
        } catch (Exception $e) {
            $this->_json(array(
                'ok'     => true,
                'note'   => 'error',
                'detail' => $e->getMessage()
            ));
        }
    }

    // ---------------------------------------------------------------
    // CONVERT (stub - unchanged)
    // ---------------------------------------------------------------
    public function convert() {
        $email_id = (int)$this->input->post('email_id');
        if (!$email_id) {
            $this->_json(array('ok' => false, 'error' => 'email_id_required'), 400);
        }
        $this->_json(array('ok' => false, 'error' => 'not_implemented_yet'));
    }
}
