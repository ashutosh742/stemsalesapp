<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RolePlayV28 Controller
 *
 * Routes:
 *   GET  /api/role_play/list_scenarios
 *   GET  /api/role_play/sessions
 *   POST /api/role_play/start
 *
 * Real tables: role_play_scenario, role_play_session
 * role_play_scenario: scenario_code, scenario_name, persona_role, persona_traits,
 *   starting_context, expected_objections_json, success_criteria,
 *   is_seed, is_induction_required, display_order, is_active
 * role_play_session: id, bd_uid, scenario_code, mode, cid_id, event_id,
 *   persona_role_used, persona_name_used, school_name_snapshot, status,
 *   started_at, ended_at, turn_count, total_cost_rs, bd_satisfaction_stars
 */
class RolePlayV28 extends CI_Controller {

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
     * GET /api/role_play/list_scenarios
     * List all active role play scenarios ordered by display_order.
     */
    public function list_scenarios()
    {
        if (!$this->auth()) return;
        $rows = $this->db->select('scenario_code, scenario_name, persona_role, persona_traits,
                                   starting_context, success_criteria, is_seed,
                                   is_induction_required, display_order')
                         ->from('role_play_scenario')
                         ->where('is_active', 1)
                         ->order_by('display_order', 'ASC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/role_play/sessions?bd_uid=<uid>[&status=<status>]
     * Sessions for a BD, optionally filtered by status.
     */
    public function sessions()
    {
        if (!$this->auth()) return;
        $bd_uid = (int) $this->input->get('bd_uid');
        $status = $this->input->get('status');

        $this->db->select('s.id, s.bd_uid, u.name AS bd_name, s.scenario_code,
                           sc.scenario_name, s.mode, s.status, s.started_at, s.ended_at,
                           s.turn_count, s.total_cost_rs, s.bd_satisfaction_stars,
                           s.persona_role_used, s.persona_name_used')
                 ->from('role_play_session s')
                 ->join('user u', 'u.uid = s.bd_uid', 'left')
                 ->join('role_play_scenario sc', 'sc.scenario_code = s.scenario_code', 'left');

        if ($bd_uid > 0) {
            $this->db->where('s.bd_uid', $bd_uid);
        }
        if ($status) {
            $this->db->where('s.status', $status);
        }

        $rows = $this->db->order_by('s.started_at', 'DESC')
                         ->limit(50)
                         ->get()->result_array();

        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * POST /api/role_play/start
     * Start a new role play session.
     * Body JSON: bd_uid, scenario_code, mode (opt, default=drill), cid_id (opt)
     */
    public function start()
    {
        if (!$this->auth()) return;
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            $body = $this->input->post();
        }
        $bd_uid        = (int) ($body['bd_uid'] ?? 0);
        $scenario_code = trim($body['scenario_code'] ?? '');
        $mode          = $body['mode'] ?? 'drill';
        $cid_id        = isset($body['cid_id']) ? (int) $body['cid_id'] : null;

        if ($bd_uid <= 0 || !$scenario_code) {
            $this->json_out(['ok' => false, 'error' => 'bd_uid and scenario_code required'], 400);
            return;
        }

        $scenario = $this->db->where('scenario_code', $scenario_code)
                             ->where('is_active', 1)
                             ->limit(1)
                             ->get('role_play_scenario')->row_array();

        if (!$scenario) {
            $this->json_out(['ok' => false, 'error' => 'scenario not found or inactive'], 404);
            return;
        }

        $insert = [
            'bd_uid'             => $bd_uid,
            'scenario_code'      => $scenario_code,
            'mode'               => $mode,
            'cid_id'             => $cid_id,
            'persona_role_used'  => $scenario['persona_role'],
            'persona_name_used'  => '',
            'school_name_snapshot' => '',
            'status'             => 'in_progress',
            'started_at'         => date('Y-m-d H:i:s'),
            'turn_count'         => 0,
            'total_tokens_in'    => 0,
            'total_tokens_out'   => 0,
            'total_cost_usd'     => 0.0,
            'total_cost_rs'      => 0.0,
            'llm_model'          => 'gpt-4o-mini',
        ];
        $this->db->insert('role_play_session', $insert);
        $session_id = $this->db->insert_id();

        $this->json_out(['ok' => true, 'success' => true, 'session_id' => $session_id,
                         'scenario' => $scenario, 'note' => 'session started']);
    }
}
