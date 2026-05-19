<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Daily Rhythm Controller
 * Migration 035 (Daily Rhythm Standardisation)
 *
 * Exposes REST endpoints under /api/rhythm/*
 *
 * Endpoints:
 *   GET  /api/rhythm/probe                      deployment health check
 *   GET  /api/rhythm/today                      checkpoints + flag counts for today
 *   GET  /api/rhythm/touchpoints                5 touchpoint definitions
 *   GET  /api/rhythm/flags?status=open&owner_uid=X   open flag events
 *   POST /api/rhythm/flag/ack                   acknowledge a flag event
 *   POST /api/rhythm/flag/resolve               resolve a flag event
 *   POST /api/rhythm/huddle/save                save huddle MoM draft
 *   POST /api/rhythm/huddle/sign                CM signs MoM
 *   POST /api/rhythm/midday/sweep               SC submits midday sweep
 *   GET  /api/rhythm/chain?uid=X                manager chain for a user
 *   POST /api/rhythm/run                        internal cron trigger
 *
 * Auth: Bearer STEM_DIGEST_TOKEN (same pattern as Proposal_sla and FunnelHygiene).
 *
 * Migration 035. Author: STEM ops.
 */
class Rhythm extends CI_Controller
{
    /** @var Rhythm_orchestrator_agent */
    protected $orchestrator;

    /** @var Red_flag_agent */
    protected $red_flag;

    // ------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');

        // Load agents
        $this->load->library('Rhythm_orchestrator_agent', null, 'orchestrator');
        $this->load->library('Red_flag_agent', null, 'red_flag');

        // Auth check on every request (probe is the only open endpoint)
        $skip_auth = ($this->router->fetch_method() === 'probe');
        if (!$skip_auth) {
            $this->_auth_bearer();
        }
    }

    // ------------------------------------------------------------------
    // AUTH HELPER
    // ------------------------------------------------------------------

    /**
     * Verify the Bearer token against STEM_DIGEST_TOKEN env var.
     * Exits with 401 JSON if invalid.
     *
     * @return void
     */
    protected function _auth_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized', 'detail' => 'missing_bearer_header'], 401);
        }
        $token    = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (!$expected || $token !== $expected) {
            $this->_json(['error' => 'unauthorized', 'detail' => 'invalid_token'], 401);
        }
    }

    // ------------------------------------------------------------------
    // RESPONSE HELPER
    // ------------------------------------------------------------------

    /**
     * Emit a JSON response and halt.
     *
     * @param  array $data
     * @param  int   $code  HTTP status code
     * @return void
     */
    protected function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
        exit;
    }

    // ------------------------------------------------------------------
    // GET /api/rhythm/probe
    // ------------------------------------------------------------------

    /**
     * Deployment health check. No auth required.
     * Returns migration id, deployed flag, and current feature_flag value.
     *
     * @return void
     */
    public function probe()
    {
        $info = $this->orchestrator->probe();
        $this->_json([
            'migration'    => $info['migration'],
            'deployed'     => $info['deployed'],
            'feature_flag' => $info['feature_flag'],
            'now'          => $info['now'],
        ], 200);
    }

    // ------------------------------------------------------------------
    // GET /api/rhythm/today
    // ------------------------------------------------------------------

    /**
     * Return today's checkpoints and flag event counts.
     *
     * @return void
     */
    public function today()
    {
        $checkpoints = $this->db->query("
            SELECT id, touchpoint_code, scope, status,
                   started_at, completed_at, result_json
              FROM daily_rhythm_checkpoint
             WHERE run_date = CURDATE()
             ORDER BY started_at ASC
        ")->result_array();

        $flag_counts = $this->red_flag->get_today_flag_counts();

        // Normalise flag counts to a keyed map
        $counts_map = [];
        foreach ($flag_counts as $fc) {
            $counts_map[$fc['status']] = (int)$fc['cnt'];
        }

        $this->_json([
            'date'             => date('Y-m-d'),
            'checkpoints'      => $checkpoints,
            'checkpoint_count' => count($checkpoints),
            'flag_counts'      => $counts_map,
            'fetched_at'       => date('Y-m-d H:i:s'),
        ], 200);
    }

    // ------------------------------------------------------------------
    // GET /api/rhythm/touchpoints
    // ------------------------------------------------------------------

    /**
     * Return the static definitions of all 5 daily touchpoints.
     *
     * @return void
     */
    public function touchpoints()
    {
        $defs = $this->orchestrator->get_touchpoint_definitions();
        $this->_json(['touchpoints' => $defs, 'count' => count($defs)], 200);
    }

    // ------------------------------------------------------------------
    // GET /api/rhythm/flags?status=open&owner_uid=X
    // ------------------------------------------------------------------

    /**
     * Return red flag events filtered by status and/or owner uid.
     * Defaults to status=open if not provided.
     *
     * @return void
     */
    public function flags()
    {
        $status    = $this->input->get('status') ?: 'open';
        $owner_uid = (int)$this->input->get('owner_uid');

        $allowed_statuses = ['open', 'acknowledged', 'escalated', 'resolved'];
        if (!in_array($status, $allowed_statuses)) {
            $this->_json(['error' => 'invalid_status', 'allowed' => $allowed_statuses], 400);
        }

        $rows = $this->red_flag->get_flag_events($status, $owner_uid ?: null);
        $this->_json([
            'status'     => $status,
            'owner_uid'  => $owner_uid ?: null,
            'rows'       => $rows,
            'count'      => count($rows),
            'fetched_at' => date('Y-m-d H:i:s'),
        ], 200);
    }

    // ------------------------------------------------------------------
    // POST /api/rhythm/flag/ack
    // ------------------------------------------------------------------

    /**
     * Acknowledge a flag event.
     * Params: flag_event_id (int, required), note (string, optional)
     *
     * @return void
     */
    public function flag_ack()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $flag_event_id = (int)$this->input->post('flag_event_id');
        $note          = trim((string)$this->input->post('note'));

        if ($flag_event_id <= 0) {
            $this->_json(['error' => 'flag_event_id_required'], 400);
        }

        $res = $this->red_flag->acknowledge_event($flag_event_id, $note);
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    // ------------------------------------------------------------------
    // POST /api/rhythm/flag/resolve
    // ------------------------------------------------------------------

    /**
     * Resolve a flag event.
     * Params: flag_event_id (int, required), resolution_note (string, optional)
     *
     * @return void
     */
    public function flag_resolve()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $flag_event_id   = (int)$this->input->post('flag_event_id');
        $resolution_note = trim((string)$this->input->post('resolution_note'));

        if ($flag_event_id <= 0) {
            $this->_json(['error' => 'flag_event_id_required'], 400);
        }

        $res = $this->red_flag->resolve_event($flag_event_id, $resolution_note);
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    // ------------------------------------------------------------------
    // POST /api/rhythm/huddle/save
    // ------------------------------------------------------------------

    /**
     * Save or update a daily huddle MoM draft.
     * Params:
     *   cluster_id         (int, required)
     *   agenda_theme_code  (string, optional)
     *   sections           (JSON string, required - the 8-section object)
     *
     * @return void
     */
    public function huddle_save()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $cluster_id        = (int)$this->input->post('cluster_id');
        $agenda_theme_code = trim((string)$this->input->post('agenda_theme_code'));
        $sections_raw      = $this->input->post('sections');

        if ($cluster_id <= 0) {
            $this->_json(['error' => 'cluster_id_required'], 400);
        }
        if (empty($sections_raw)) {
            $this->_json(['error' => 'sections_required'], 400);
        }

        // Validate sections is valid JSON
        $sections = json_decode($sections_raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->_json(['error' => 'sections_invalid_json'], 400);
        }

        // Upsert the MoM row for today
        $existing = $this->db->query("
            SELECT id FROM daily_huddle_mom
             WHERE cluster_id = ? AND huddle_date = CURDATE()
             LIMIT 1
        ", [$cluster_id])->row_array();

        if ($existing) {
            $this->db->query("
                UPDATE daily_huddle_mom
                   SET sections_json      = ?,
                       agenda_theme_code  = ?,
                       draft_status       = 'draft',
                       updated_at         = NOW()
                 WHERE id = ?
            ", [json_encode($sections), $agenda_theme_code, (int)$existing['id']]);

            $this->_json(['ok' => true, 'huddle_id' => (int)$existing['id'],
                          'action' => 'updated'], 200);
        } else {
            $this->db->insert('daily_huddle_mom', [
                'cluster_id'        => $cluster_id,
                'huddle_date'       => date('Y-m-d'),
                'agenda_theme_code' => $agenda_theme_code,
                'sections_json'     => json_encode($sections),
                'draft_status'      => 'draft',
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
            $huddle_id = (int)$this->db->insert_id();
            $this->_json(['ok' => true, 'huddle_id' => $huddle_id,
                          'action' => 'created'], 200);
        }
    }

    // ------------------------------------------------------------------
    // POST /api/rhythm/huddle/sign
    // ------------------------------------------------------------------

    /**
     * CM signs a huddle MoM.
     * Params: huddle_id (int, required), cm_uid (int, required)
     *
     * @return void
     */
    public function huddle_sign()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $huddle_id = (int)$this->input->post('huddle_id');
        $cm_uid    = (int)$this->input->post('cm_uid');

        if ($huddle_id <= 0 || $cm_uid <= 0) {
            $this->_json(['error' => 'huddle_id_and_cm_uid_required'], 400);
        }

        $mom = $this->db->query("
            SELECT id, draft_status, signed_at
              FROM daily_huddle_mom WHERE id = ? LIMIT 1
        ", [$huddle_id])->row_array();

        if (!$mom) {
            $this->_json(['error' => 'huddle_not_found'], 404);
        }
        if ($mom['signed_at']) {
            $this->_json(['error' => 'already_signed', 'signed_at' => $mom['signed_at']], 400);
        }

        $this->db->query("
            UPDATE daily_huddle_mom
               SET cm_uid        = ?,
                   signed_at     = NOW(),
                   draft_status  = 'signed'
             WHERE id = ?
        ", [$cm_uid, $huddle_id]);

        log_message('info', '[rhythm] huddle_sign huddle_id=' . $huddle_id . ' cm_uid=' . $cm_uid);
        $this->_json(['ok' => true, 'huddle_id' => $huddle_id, 'signed_by' => $cm_uid,
                      'signed_at' => date('Y-m-d H:i:s')], 200);
    }

    // ------------------------------------------------------------------
    // POST /api/rhythm/midday/sweep
    // ------------------------------------------------------------------

    /**
     * SC submits a midday sweep for a cluster.
     * Params:
     *   sc_uid      (int, required)
     *   cluster_id  (int, required)
     *   counts      (JSON string, required - e.g. {"zero_rp":3,"total_bds":10})
     *
     * @return void
     */
    public function midday_sweep()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $sc_uid     = (int)$this->input->post('sc_uid');
        $cluster_id = (int)$this->input->post('cluster_id');
        $counts_raw = $this->input->post('counts');

        if ($sc_uid <= 0 || $cluster_id <= 0) {
            $this->_json(['error' => 'sc_uid_and_cluster_id_required'], 400);
        }
        if (empty($counts_raw)) {
            $this->_json(['error' => 'counts_required'], 400);
        }

        $counts = json_decode($counts_raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->_json(['error' => 'counts_invalid_json'], 400);
        }

        $this->db->query("
            INSERT INTO midday_pulse_sweep
                (sc_uid, cluster_id, sweep_date, counts_json, submitted_at)
            VALUES (?, ?, CURDATE(), ?, NOW())
            ON DUPLICATE KEY UPDATE
                counts_json  = VALUES(counts_json),
                submitted_at = NOW()
        ", [$sc_uid, $cluster_id, json_encode($counts)]);

        log_message('info', '[rhythm] midday_sweep sc_uid=' . $sc_uid . ' cluster_id=' . $cluster_id);
        $this->_json(['ok' => true, 'sc_uid' => $sc_uid, 'cluster_id' => $cluster_id,
                      'sweep_date' => date('Y-m-d')], 200);
    }

    // ------------------------------------------------------------------
    // GET /api/rhythm/chain?uid=X
    // ------------------------------------------------------------------

    /**
     * Return the manager chain for a given user uid.
     *
     * @return void
     */
    public function chain()
    {
        $uid = (int)$this->input->get('uid');
        if ($uid <= 0) {
            $this->_json(['error' => 'uid_required'], 400);
        }

        $chain = $this->db->query("
            SELECT lmc.chain_uid, lmc.chain_role, lmc.chain_level,
                   u.firstName AS chain_user_name
              FROM line_manager_chain lmc
              LEFT JOIN user u ON u.uid = lmc.chain_uid
             WHERE lmc.employee_uid = ?
               AND lmc.active = 1
             ORDER BY lmc.chain_level ASC
        ", [$uid])->result_array();

        $this->_json(['uid' => $uid, 'chain' => $chain, 'depth' => count($chain)], 200);
    }

    // ------------------------------------------------------------------
    // POST /api/rhythm/run
    // ------------------------------------------------------------------

    /**
     * Internal trigger for cron. Fires a single touchpoint.
     * Params: touchpoint_code (string, required)
     *
     * @return void
     */
    public function run()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $touchpoint_code = trim((string)$this->input->post('touchpoint_code'));

        $valid_codes = ['morning_brief', 'daily_huddle', 'midday_pulse',
                        'bd_day_close', 'evening_review'];

        if (empty($touchpoint_code) || !in_array($touchpoint_code, $valid_codes)) {
            $this->_json([
                'error'       => 'invalid_touchpoint_code',
                'valid_codes' => $valid_codes,
            ], 400);
        }

        $result = $this->orchestrator->run_daily_rhythm($touchpoint_code);
        $this->_json($result, $result['ok'] ? 200 : 500);
    }
}
// END Rhythm controller
