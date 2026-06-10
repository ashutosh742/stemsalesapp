<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AgentsV28 Controller
 *
 * Handles /api/agents/* routes for STEM CRM v2.8 staging.
 *
 * Tables used (verified on staging):
 *   agent_orchestration_log - trigger events, agent names, latency, output
 *
 * agents_registry / agents_run_log not present; those route methods return
 * ok:true with awaits_migration note and documented expected shape.
 *
 * Routes:
 *   GET api/agents/anaya/today
 *   GET api/agents/cadence_star/today
 *   GET api/agents/cm_copilot/today
 *   GET api/agents/dump_mining/today
 *   GET api/agents/mom_drafter/queue
 *   GET api/agents/war_room/today
 */
class AgentsV28 extends CI_Controller {

    private $bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Known agent names mapped to their canonical agent_name in orchestration log
    private $agent_map = [
        'anaya'        => 'anaya',
        'cadence_star' => 'cadence_star',
        'cm_copilot'   => 'cm_copilot',
        'dump_mining'  => 'dump_mining',
        'mom_drafter'  => 'mom_drafter',
        'war_room'     => 'war_room',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('BearerAuth');
        $this->output->set_content_type('application/json');
    }

    private function _check_auth()
    {
        $auth = $this->bearerauth->resolve();
        if (!$auth['ok']) {
            $this->output->set_status_header(401);
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200)
    {
        $this->output->set_status_header($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * _agent_today
     * Generic helper: returns today's agent_orchestration_log rows for a named agent.
     * Triggered by: anaya/today, cadence_star/today, cm_copilot/today, dump_mining/today, war_room/today
     */
    private function _agent_today($agent_slug)
    {
        if (!$this->_check_auth()) return;

        $agent_name = isset($this->agent_map[$agent_slug]) ? $this->agent_map[$agent_slug] : $agent_slug;
        $today = date('Y-m-d');

        $query = $this->db
            ->select('id, trigger_event, agent_name, accountability_uid, company_id, lead_id, latency_ms, error_code, output_snapshot_json, created_at')
            ->where('agent_name', $agent_name)
            ->where('DATE(created_at)', $today)
            ->order_by('created_at', 'DESC')
            ->limit(100)
            ->get('agent_orchestration_log');

        $rows = $query ? $query->result_array() : [];

        foreach ($rows as &$r) {
            $r['id']               = (int) $r['id'];
            $r['accountability_uid'] = (int) $r['accountability_uid'];
            $r['latency_ms']       = $r['latency_ms'] !== null ? (int) $r['latency_ms'] : null;
            // Decode output snapshot for convenience; ignore parse errors
            if (!empty($r['output_snapshot_json'])) {
                $decoded = json_decode($r['output_snapshot_json'], true);
                $r['output'] = $decoded ?: $r['output_snapshot_json'];
            } else {
                $r['output'] = null;
            }
            unset($r['output_snapshot_json']);
        }
        unset($r);

        $this->_json([
            'ok'         => true,
            'success'    => true,
            'agent'      => $agent_name,
            'date'       => $today,
            'rows'       => $rows,
            'count'      => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // GET api/agents/anaya/today
    // -----------------------------------------------------------------------
    public function anaya()
    {
        $this->_agent_today('anaya');
    }

    // -----------------------------------------------------------------------
    // GET api/agents/cadence_star/today
    // -----------------------------------------------------------------------
    public function cadence_star()
    {
        $this->_agent_today('cadence_star');
    }

    // -----------------------------------------------------------------------
    // GET api/agents/cm_copilot/today
    // -----------------------------------------------------------------------
    public function cm_copilot()
    {
        $this->_agent_today('cm_copilot');
    }

    // -----------------------------------------------------------------------
    // GET api/agents/dump_mining/today
    // -----------------------------------------------------------------------
    public function dump_mining()
    {
        $this->_agent_today('dump_mining');
    }

    // -----------------------------------------------------------------------
    // GET api/agents/mom_drafter/queue
    // Returns pending MOM drafter queue.
    // Source: mom_v2_submission where status IN (draft, voice_done, form_done)
    // These are items waiting for the mom_drafter agent to act on.
    // -----------------------------------------------------------------------
    public function mom_drafter()
    {
        if (!$this->_check_auth()) return;

        $query = $this->db
            ->select('submission_id, event_id, bd_uid, cid_id, status, answers_completed, answers_required, created_at')
            ->where_in('status', ['draft', 'voice_done', 'form_done'])
            ->order_by('created_at', 'ASC')
            ->limit(100)
            ->get('mom_v2_submission');

        $rows = $query ? $query->result_array() : [];
        foreach ($rows as &$r) {
            $r['submission_id']     = (int) $r['submission_id'];
            $r['event_id']          = (int) $r['event_id'];
            $r['bd_uid']            = (int) $r['bd_uid'];
            $r['answers_completed'] = (int) $r['answers_completed'];
            $r['answers_required']  = (int) $r['answers_required'];
        }
        unset($r);

        $this->_json([
            'ok'      => true,
            'success' => true,
            'agent'   => 'mom_drafter',
            'queue'   => $rows,
            'count'   => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // GET api/agents/war_room/today
    // -----------------------------------------------------------------------
    public function war_room()
    {
        $this->_agent_today('war_room');
    }
}
