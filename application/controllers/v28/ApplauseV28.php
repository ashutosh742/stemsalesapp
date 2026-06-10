<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ApplauseV28 Controller
 *
 * Routes:
 *   GET  /api/applause/feed
 *   GET  /api/applause/leaderboard
 *   GET  /api/applause/my_received
 *   GET  /api/applause/probe
 *   POST /api/applause/send
 *
 * Real table: applause_log
 * Columns: id, from_uid, to_uid, applause_type, message, lead_id, visibility, created_at
 */
class ApplauseV28 extends CI_Controller {

    private $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->output->set_content_type('application/json');
    }

    private function auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || trim(str_replace('Bearer', '', $h)) !== $this->token) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    private function json_out($data, $status = 200)
    {
        $this->output->set_status_header($status)
                     ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * GET /api/applause/feed
     * Recent applause visible to all, newest first, limit 50.
     */
    public function feed()
    {
        if (!$this->auth()) return;
        $rows = $this->db->select('a.id, a.from_uid, a.to_uid, a.applause_type, a.message, a.visibility, a.created_at,
                                   uf.name AS from_name, ut.name AS to_name')
                         ->from('applause_log a')
                         ->join('user uf', 'uf.uid = a.from_uid', 'left')
                         ->join('user ut', 'ut.uid = a.to_uid', 'left')
                         ->where_in('a.visibility', ['team', 'cluster', 'all'])
                         ->order_by('a.created_at', 'DESC')
                         ->limit(50)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/applause/leaderboard
     * Top recipients in the last 30 days.
     */
    public function leaderboard()
    {
        if (!$this->auth()) return;
        $since = date('Y-m-d', strtotime('-30 days'));
        $rows = $this->db->select('a.to_uid, u.name AS to_name, COUNT(*) AS total_received')
                         ->from('applause_log a')
                         ->join('user u', 'u.uid = a.to_uid', 'left')
                         ->where('a.created_at >=', $since)
                         ->group_by('a.to_uid')
                         ->order_by('total_received', 'DESC')
                         ->limit(20)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/applause/my_received?uid=<uid>
     * Applause received by a specific user.
     */
    public function my_received()
    {
        if (!$this->auth()) return;
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
            return;
        }
        $rows = $this->db->select('a.id, a.from_uid, uf.name AS from_name, a.applause_type, a.message, a.created_at')
                         ->from('applause_log a')
                         ->join('user uf', 'uf.uid = a.from_uid', 'left')
                         ->where('a.to_uid', $uid)
                         ->order_by('a.created_at', 'DESC')
                         ->limit(50)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/applause/probe
     * Health check.
     */
    public function probe()
    {
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'ApplauseV28 online']);
    }

    /**
     * POST /api/applause/send
     * Body JSON: from_uid, to_uid, applause_type, message, lead_id (opt), visibility (opt)
     */
    public function send()
    {
        if (!$this->auth()) return;
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            $body = $this->input->post();
        }
        $from_uid     = (int) ($body['from_uid'] ?? 0);
        $to_uid       = (int) ($body['to_uid'] ?? 0);
        $atype        = $body['applause_type'] ?? 'team_effort';
        $message      = substr(trim($body['message'] ?? ''), 0, 300);
        $lead_id      = isset($body['lead_id']) ? (int) $body['lead_id'] : null;
        $visibility   = $body['visibility'] ?? 'team';

        if ($from_uid <= 0 || $to_uid <= 0) {
            $this->json_out(['ok' => false, 'error' => 'from_uid and to_uid required'], 400);
            return;
        }

        $allowed_types = ['deal_won', 'good_mom', 'plan_streak', 'team_effort', 'milestone'];
        if (!in_array($atype, $allowed_types)) {
            $atype = 'team_effort';
        }

        $insert = [
            'from_uid'     => $from_uid,
            'to_uid'       => $to_uid,
            'applause_type'=> $atype,
            'message'      => $message,
            'lead_id'      => $lead_id,
            'visibility'   => $visibility,
            'created_at'   => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('applause_log', $insert);
        $new_id = $this->db->insert_id();
        $this->json_out(['ok' => true, 'success' => true, 'id' => $new_id, 'note' => 'applause recorded']);
    }
}
