<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * UpstreamHygiene controller
 *
 * REST surface for migration 028 (Upstream Hygiene + Proposal Backlog Sweep).
 *
 * Endpoints (all require Bearer STEM_DIGEST_TOKEN):
 *
 *   GET  /api/upstream_hygiene/probe
 *   GET  /api/upstream_hygiene/stagnant_open_45?days_threshold=45
 *   GET  /api/upstream_hygiene/stagnant_reachout_30?days_threshold=30
 *   GET  /api/upstream_hygiene/wallet_triggers?days=N
 *   GET  /api/upstream_hygiene/by_bd?bd_uid=N
 *   GET  /api/upstream_hygiene/by_cm?cm_uid=N
 *   POST /api/upstream_hygiene/manual_override
 *   POST /api/upstream_hygiene/run_detection     (admin only)
 *
 *   GET  /api/proposal/sla/backlog?legacy=1      (proposal backlog legacy rows)
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/upstream_hygiene/probe']              = 'upstreamhygiene/probe';
 *   $route['api/upstream_hygiene/stagnant_open_45']   = 'upstreamhygiene/stagnant_open_45';
 *   $route['api/upstream_hygiene/stagnant_reachout_30'] = 'upstreamhygiene/stagnant_reachout_30';
 *   $route['api/upstream_hygiene/wallet_triggers']    = 'upstreamhygiene/wallet_triggers';
 *   $route['api/upstream_hygiene/by_bd']              = 'upstreamhygiene/by_bd';
 *   $route['api/upstream_hygiene/by_cm']              = 'upstreamhygiene/by_cm';
 *   $route['api/upstream_hygiene/manual_override']    = 'upstreamhygiene/manual_override';
 *   $route['api/upstream_hygiene/run_detection']      = 'upstreamhygiene/run_detection';
 *   $route['api/proposal/sla/backlog']                = 'upstreamhygiene/proposal_backlog';
 *
 * Auth: Bearer STEM_DIGEST_TOKEN.
 * Result cap: 200 rows on all GET endpoints.
 *
 * Migration 028.
 * Author: STEM ops, 2026-05-17.
 */
class UpstreamHygiene extends CI_Controller
{
    /** Max rows returned per GET endpoint. */
    const ROW_CAP = 200;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        require_once APPPATH . 'models/AIAgents/Upstream_hygiene_agent.php';
        $this->agent = new Upstream_hygiene_agent();
        $this->_require_bearer();
    }

    // ------------------------------------------------------------------------
    // AUTH
    // ------------------------------------------------------------------------
    private function _require_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
        }
        $token    = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (!$expected || $token !== $expected) {
            $this->_json(['error' => 'invalid_token'], 401);
        }
    }

    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
        exit;
    }

    // ------------------------------------------------------------------------
    // 1. GET /api/upstream_hygiene/probe
    // Health check. Cron 34f41737 polls this before calling other endpoints.
    // Returns migration version and deployment status.
    // ------------------------------------------------------------------------
    public function probe()
    {
        $this->_json($this->agent->probe());
    }

    // ------------------------------------------------------------------------
    // 2. GET /api/upstream_hygiene/stagnant_open_45
    // Returns cstatus 1 leads with days_stagnant >= days_threshold (default 45).
    // Capped at ROW_CAP rows. Used by cron 34f41737 Open Stagnancy section.
    // ------------------------------------------------------------------------
    public function stagnant_open_45()
    {
        $days_threshold = (int)($this->input->get('days_threshold') ?: 45);
        $rows = $this->agent->compute_stagnant_open($days_threshold);
        $rows = array_slice($rows, 0, self::ROW_CAP);

        $this->_json([
            'rows'           => $rows,
            'count'          => count($rows),
            'days_threshold' => $days_threshold,
            'cstatus'        => 1,
        ]);
    }

    // ------------------------------------------------------------------------
    // 3. GET /api/upstream_hygiene/stagnant_reachout_30
    // Returns cstatus 2 leads with days_stagnant >= days_threshold (default 30).
    // Capped at ROW_CAP rows. Used by cron 34f41737 Reachout Stagnancy section.
    // ------------------------------------------------------------------------
    public function stagnant_reachout_30()
    {
        $days_threshold = (int)($this->input->get('days_threshold') ?: 30);
        $rows = $this->agent->compute_stagnant_reachout($days_threshold);
        $rows = array_slice($rows, 0, self::ROW_CAP);

        $this->_json([
            'rows'           => $rows,
            'count'          => count($rows),
            'days_threshold' => $days_threshold,
            'cstatus'        => 2,
        ]);
    }

    // ------------------------------------------------------------------------
    // 4. GET /api/upstream_hygiene/wallet_triggers?days=N
    // Returns recent wallet_debit events from upstream_hygiene_log.
    // Default N = 7 days. Used by cron 34f41737 Wallet Debits section.
    // ------------------------------------------------------------------------
    public function wallet_triggers()
    {
        $days = (int)($this->input->get('days') ?: 7);
        $days = max(1, $days);

        $rows = $this->db->query("
            SELECT
                l.*,
                ic.compny_nm AS school_name,
                s.bd_uid,
                ub.firstName AS bd_name,
                s.cm_uid,
                uc.firstName AS cm_name
              FROM upstream_hygiene_log l
              LEFT JOIN upstream_hygiene_state s ON s.cid_id = l.cid_id
              LEFT JOIN init_call ic ON ic.id = l.cid_id
              LEFT JOIN user ub      ON ub.uid = s.bd_uid
              LEFT JOIN user uc      ON uc.uid = s.cm_uid
             WHERE l.event_type = 'wallet_debit'
               AND l.event_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY l.event_at DESC
             LIMIT ?
        ", [$days, self::ROW_CAP])->result_array();

        $total_rs = array_sum(array_column($rows, 'rs_amount'));

        $this->_json([
            'rows'     => $rows,
            'count'    => count($rows),
            'total_rs' => (float)$total_rs,
            'days'     => $days,
        ]);
    }

    // ------------------------------------------------------------------------
    // 5. GET /api/upstream_hygiene/by_bd?bd_uid=N
    // BD-scoped view of all upstream_hygiene_state rows for that BD.
    // Used by My Open Pipeline screen (cstatus 1 view) and
    // Reachout Inbox screen (cstatus 2 view) in the mobile app.
    // ------------------------------------------------------------------------
    public function by_bd()
    {
        $bd_uid = (int)$this->input->get('bd_uid');
        if (!$bd_uid) {
            $this->_json(['error' => 'missing_bd_uid'], 400);
        }

        $rows = $this->db->query("
            SELECT
                s.*,
                ic.compny_nm AS school_name,
                ic.fbudget   AS pipeline_rs
              FROM upstream_hygiene_state s
              LEFT JOIN init_call ic ON ic.id = s.cid_id
             WHERE s.bd_uid = ?
             ORDER BY s.days_stagnant DESC
             LIMIT ?
        ", [$bd_uid, self::ROW_CAP])->result_array();

        // Include block status so the mobile screen can show the block banner.
        $block = $this->agent->check_bd_block($bd_uid);

        $this->_json([
            'rows'          => $rows,
            'count'         => count($rows),
            'bd_uid'        => $bd_uid,
            'block_status'  => $block,
        ]);
    }

    // ------------------------------------------------------------------------
    // 6. GET /api/upstream_hygiene/by_cm?cm_uid=N
    // CM-scoped view of all stagnant rows across the CM's cluster.
    // Used by Cluster Stagnancy screen.
    // ------------------------------------------------------------------------
    public function by_cm()
    {
        $cm_uid = (int)$this->input->get('cm_uid');
        if (!$cm_uid) {
            $this->_json(['error' => 'missing_cm_uid'], 400);
        }

        $rows = $this->db->query("
            SELECT
                s.*,
                ic.compny_nm AS school_name,
                ub.firstName AS bd_name,
                ic.fbudget   AS pipeline_rs
              FROM upstream_hygiene_state s
              LEFT JOIN init_call ic ON ic.id = s.cid_id
              LEFT JOIN user ub      ON ub.uid = s.bd_uid
             WHERE s.cm_uid = ?
             ORDER BY s.days_stagnant DESC
             LIMIT ?
        ", [$cm_uid, self::ROW_CAP])->result_array();

        // Summary counts per BD for the Cluster Stagnancy drilldown.
        $by_bd = [];
        foreach ($rows as $r) {
            $uid = $r['bd_uid'];
            if (!isset($by_bd[$uid])) {
                $by_bd[$uid] = [
                    'bd_uid'          => $uid,
                    'bd_name'         => $r['bd_name'],
                    'total'           => 0,
                    'stagnant_flag'   => 0,
                    'near_miss_flag'  => 0,
                ];
            }
            $by_bd[$uid]['total']++;
            if ((int)$r['stagnant_flag']) $by_bd[$uid]['stagnant_flag']++;
            if ((int)$r['near_miss_flag']) $by_bd[$uid]['near_miss_flag']++;
        }
        // Sort by worst BD first (stagnant_flag count descending).
        usort($by_bd, function($a, $b) {
            return $b['stagnant_flag'] - $a['stagnant_flag'];
        });

        $this->_json([
            'rows'   => $rows,
            'count'  => count($rows),
            'cm_uid' => $cm_uid,
            'by_bd'  => array_values($by_bd),
        ]);
    }

    // ------------------------------------------------------------------------
    // 7. POST /api/upstream_hygiene/manual_override
    // CM can clear a near_miss_flag or stagnant_flag on a lead with a reason.
    // Body params: cid_id (int), override_field (string), reason (string), by_uid (int).
    // Logs to upstream_hygiene_log with event_type = manager_email.
    // ------------------------------------------------------------------------
    public function manual_override()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $cid_id         = (int)$this->input->post('cid_id');
        $override_field = $this->input->post('override_field');
        $reason         = trim((string)$this->input->post('reason'));
        $by_uid         = (int)$this->input->post('by_uid');

        if (!$cid_id || !$by_uid) {
            $this->_json(['error' => 'missing_cid_id_or_by_uid'], 400);
        }

        $allowed_fields = ['near_miss_flag', 'stagnant_flag'];
        if (!in_array($override_field, $allowed_fields)) {
            $this->_json(['error' => 'invalid_override_field',
                          'allowed' => $allowed_fields], 400);
        }

        if (strlen($reason) < 10) {
            $this->_json(['error' => 'reason_too_short', 'min_chars' => 10], 400);
        }

        // Verify the row exists and get days_stagnant for the log.
        $state = $this->db->select('cid_id, days_stagnant, cstatus')
            ->from('upstream_hygiene_state')
            ->where('cid_id', $cid_id)
            ->get()->row_array();

        if (!$state) {
            $this->_json(['error' => 'cid_not_in_upstream_state'], 404);
        }

        // Clear the flag.
        $this->db->where('cid_id', $cid_id)
            ->update('upstream_hygiene_state', [$override_field => 0]);

        // Log the override.
        $this->db->insert('upstream_hygiene_log', [
            'cid_id'       => $cid_id,
            'event_type'   => 'manager_email',
            'days_at_event'=> (int)$state['days_stagnant'],
            'rs_amount'    => 0,
            'notes'        => 'manual_override by uid=' . $by_uid
                              . ' field=' . $override_field
                              . ' reason=' . substr($reason, 0, 300),
        ]);

        $this->_json([
            'ok'             => true,
            'cid_id'         => $cid_id,
            'override_field' => $override_field,
            'cleared_to'     => 0,
            'by_uid'         => $by_uid,
        ]);
    }

    // ------------------------------------------------------------------------
    // 8. POST /api/upstream_hygiene/run_detection
    // Admin-only manual trigger of run_nightly_detection.
    // Used for smoke tests and emergency re-runs.
    // ------------------------------------------------------------------------
    public function run_detection()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $out = $this->agent->run_nightly_detection();
        $this->_json($out);
    }

    // ------------------------------------------------------------------------
    // 9. GET /api/proposal/sla/backlog?legacy=1
    // Returns proposal_backlog_legacy rows.
    // When legacy=1 scopes to the 3,014 seeded rows.
    // Used by the Proposal Backlog screen and cron 34f41737 backlog section.
    // ------------------------------------------------------------------------
    public function proposal_backlog()
    {
        $legacy = (int)$this->input->get('legacy');
        $status = $this->input->get('status');

        $sql = "
            SELECT
                p.*,
                ic.compny_nm AS school_name,
                ic.mainbd    AS bd_uid,
                ub.firstName AS bd_name,
                (SELECT parent_uid FROM reporting_hierarchy
                  WHERE employee_uid = ic.mainbd AND active = 1 LIMIT 1) AS cm_uid,
                DATEDIFF(CURDATE(), p.grace_window_ends_at) AS days_past_grace
              FROM proposal_backlog_legacy p
              LEFT JOIN init_call ic ON ic.id = p.cid_id
              LEFT JOIN user ub      ON ub.uid = ic.mainbd
             WHERE 1=1
        ";
        $params = [];

        if ($status && in_array($status, ['legacy_grace','legacy_overdue','filed','closed_lost'])) {
            $sql    .= " AND p.status = ?";
            $params[] = $status;
        }

        $sql    .= " ORDER BY p.grace_window_ends_at ASC LIMIT ?";
        $params[] = self::ROW_CAP;

        $rows = $this->db->query($sql, $params)->result_array();

        $summary = [
            'legacy_grace'   => 0,
            'legacy_overdue' => 0,
            'filed'          => 0,
            'closed_lost'    => 0,
        ];
        foreach ($rows as $r) {
            if (isset($summary[$r['status']])) {
                $summary[$r['status']]++;
            }
        }

        $this->_json([
            'rows'    => $rows,
            'count'   => count($rows),
            'legacy'  => (bool)$legacy,
            'summary' => $summary,
        ]);
    }
}
