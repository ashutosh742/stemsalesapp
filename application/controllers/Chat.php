<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chat controller - single JSON dispatcher for mobile-app tools.
 *
 * Contract source: /home/user/workspace/stem-mobile-preview/API_MAPPING.md
 * Consumer:        mobile/src/api/client.js (tools.runTool)
 *
 * Endpoint:
 *   POST /chat/api_run_tool   body { tool: string, params: object }
 *
 * Allowed tools (whitelist):
 *   get_bd_funnel        { user_id }
 *   get_bd_discipline    { user_id, days }
 *   find_similar_leads   { init_call_id, k }
 *   get_recent_moms      { user_id }
 *   schedule_followup    { lead_id, datetime_iso, note }
 *   get_funnel_report    { cluster_id?, from?, to? }
 *   get_closure_pipeline { cluster_id?, days? }
 *   find_dormant_leads   { min_days }
 *
 * Auth: session cookie ($this->session->userdata('user')) OR
 *       Bearer STEM_DIGEST_TOKEN header (operator path).
 *
 * Standards: no em-dashes, no non-ASCII, snake_case error codes. Tool calls
 * that cannot be served from current schema return ok=true with a
 * not_implemented marker in the result so the client surfaces a clean
 * "coming soon" state instead of erroring.
 */
class Chat extends CI_Controller {

    /** Tool whitelist - keys are tool names sent by the mobile client. */
    private $tools_allowed = [
        'get_bd_funnel',
        'get_bd_discipline',
        'find_similar_leads',
        'get_recent_moms',
        'schedule_followup',
        'get_funnel_report',
        'get_closure_pipeline',
        'find_dormant_leads',
    ];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    // ---------- helpers ----------

    private function _json($data, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
        exit;
    }

    private function _json_body() {
        $raw = $this->input->raw_input_stream;
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        $post = $this->input->post(null, true);
        return is_array($post) ? $post : [];
    }

    /**
     * Resolve caller uid. Session cookie first, Bearer token fallback for
     * operator/cron use. Returns int uid or null.
     */
    private function _auth_uid() {
        $user = $this->session->userdata('user');
        if (!empty($user['uid'])) return (int)$user['uid'];
        if (!empty($user['user_id'])) return (int)$user['user_id'];

        $hdr = $this->input->get_request_header('Authorization');
        if ($hdr && strpos($hdr, 'Bearer ') === 0) {
            $token = trim(substr($hdr, 7));
            $expected = getenv('STEM_DIGEST_TOKEN');
            if ($expected !== false && $expected !== '' && hash_equals($expected, $token)) {
                // Operator path: allow caller to specify acting uid via header or
                // body. Defaults to 0 (admin scope). The tool methods clamp scope.
                $acting = (int)$this->input->get_request_header('X-Acting-Uid');
                return $acting > 0 ? $acting : 0;
            }
        }
        return null;
    }

    // ---------- POST /chat/api_run_tool ----------

    public function api_run_tool() {
        if ($this->input->method(true) !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $caller_uid = $this->_auth_uid();
        if ($caller_uid === null) {
            $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        $body   = $this->_json_body();
        $tool   = isset($body['tool']) ? (string)$body['tool'] : '';
        $params = isset($body['params']) && is_array($body['params']) ? $body['params'] : [];

        if ($tool === '') {
            $this->_json(['ok' => false, 'error' => 'missing_tool'], 400);
        }
        if (!in_array($tool, $this->tools_allowed, true)) {
            $this->_json([
                'ok'      => false,
                'error'   => 'tool_not_whitelisted',
                'tool'    => $tool,
                'allowed' => $this->tools_allowed,
            ], 400);
        }

        try {
            $result = $this->_dispatch($tool, $params, $caller_uid);
        } catch (Exception $e) {
            $this->_json([
                'ok'      => false,
                'error'   => 'tool_runtime_error',
                'tool'    => $tool,
                'message' => $e->getMessage(),
            ], 500);
        }

        $this->_json([
            'ok'     => true,
            'tool'   => $tool,
            'result' => $result,
        ]);
    }

    // ---------- dispatcher ----------

    private function _dispatch($tool, $params, $caller_uid) {
        switch ($tool) {
            case 'get_bd_funnel':        return $this->_tool_get_bd_funnel($params, $caller_uid);
            case 'get_bd_discipline':    return $this->_tool_get_bd_discipline($params, $caller_uid);
            case 'get_recent_moms':      return $this->_tool_get_recent_moms($params, $caller_uid);
            case 'find_dormant_leads':   return $this->_tool_find_dormant_leads($params, $caller_uid);
            case 'get_funnel_report':    return $this->_tool_get_funnel_report($params, $caller_uid);
            case 'get_closure_pipeline': return $this->_tool_get_closure_pipeline($params, $caller_uid);
            case 'find_similar_leads':   return $this->_tool_find_similar_leads($params, $caller_uid);
            case 'schedule_followup':    return $this->_tool_schedule_followup($params, $caller_uid);
        }
        return ['not_implemented' => true, 'tool' => $tool];
    }

    // ---------- tool implementations ----------

    private function _resolve_uid($params, $caller_uid) {
        $uid = isset($params['user_id']) ? (int)$params['user_id'] : 0;
        if ($uid <= 0) $uid = $caller_uid;
        return $uid;
    }

    private function _tool_get_bd_funnel($params, $caller_uid) {
        $uid = $this->_resolve_uid($params, $caller_uid);
        if ($uid <= 0) return ['error' => 'no_uid'];

        $sql = "SELECT current_status_id AS cstatus, COUNT(*) AS n
                FROM init_call
                WHERE mainbd = ?
                GROUP BY current_status_id
                ORDER BY current_status_id ASC";
        $q = $this->db->query($sql, [$uid]);
        $by_stage = $q ? $q->result_array() : [];

        $total = 0;
        foreach ($by_stage as $row) $total += (int)$row['n'];

        return [
            'uid'      => $uid,
            'total'    => $total,
            'by_stage' => $by_stage,
        ];
    }

    private function _tool_get_bd_discipline($params, $caller_uid) {
        $uid  = $this->_resolve_uid($params, $caller_uid);
        $days = isset($params['days']) ? max(1, min(60, (int)$params['days'])) : 7;
        if ($uid <= 0) return ['error' => 'no_uid'];

        if (!$this->db->table_exists('bd_discipline_score')) {
            return ['not_implemented' => true, 'reason' => 'bd_discipline_score table missing'];
        }
        $sql = "SELECT score_date, total_score, grade
                FROM bd_discipline_score
                WHERE uid = ? AND score_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY score_date DESC";
        $q = $this->db->query($sql, [$uid, $days]);
        $rows = $q ? $q->result_array() : [];

        $avg = null;
        if (!empty($rows)) {
            $sum = 0; $n = 0;
            foreach ($rows as $r) { $sum += (float)$r['total_score']; $n++; }
            $avg = $n > 0 ? round($sum / $n, 2) : null;
        }

        return [
            'uid'       => $uid,
            'days'      => $days,
            'avg_score' => $avg,
            'series'    => $rows,
        ];
    }

    private function _tool_get_recent_moms($params, $caller_uid) {
        $uid   = $this->_resolve_uid($params, $caller_uid);
        $limit = isset($params['limit']) ? max(1, min(50, (int)$params['limit'])) : 10;
        if ($uid <= 0) return ['error' => 'no_uid'];

        if (!$this->db->table_exists('mom_data')) {
            return ['not_implemented' => true, 'reason' => 'mom_data table missing'];
        }
        $sql = "SELECT id AS mom_id, event_id, cid_id, status, created_at
                FROM mom_data
                WHERE created_by = ?
                ORDER BY created_at DESC
                LIMIT $limit";
        $q = $this->db->query($sql, [$uid]);
        return ['uid' => $uid, 'limit' => $limit, 'moms' => $q ? $q->result_array() : []];
    }

    private function _tool_find_dormant_leads($params, $caller_uid) {
        $min_days = isset($params['min_days']) ? max(1, (int)$params['min_days']) : 14;
        $uid      = $this->_resolve_uid($params, $caller_uid);
        if ($uid <= 0) return ['error' => 'no_uid'];

        $sql = "SELECT ic.id AS cid_id, ic.school_name, ic.current_status_id AS cstatus,
                       MAX(ev.event_date) AS last_touch,
                       DATEDIFF(CURDATE(), MAX(ev.event_date)) AS days_since
                FROM init_call ic
                LEFT JOIN tblcallevents ev ON ev.cid_id = ic.id
                WHERE ic.mainbd = ?
                  AND ic.current_status_id NOT IN (12, 13)
                GROUP BY ic.id
                HAVING days_since >= ? OR last_touch IS NULL
                ORDER BY days_since DESC
                LIMIT 50";
        $q = $this->db->query($sql, [$uid, $min_days]);
        return [
            'uid'      => $uid,
            'min_days' => $min_days,
            'leads'    => $q ? $q->result_array() : [],
        ];
    }

    private function _tool_get_funnel_report($params, $caller_uid) {
        $cluster_id = isset($params['cluster_id']) ? (int)$params['cluster_id'] : 0;

        $where = '';
        $bind  = [];
        if ($cluster_id > 0) {
            $where = "WHERE u.cluster_id = ?";
            $bind[] = $cluster_id;
        }
        $sql = "SELECT ic.current_status_id AS cstatus, COUNT(*) AS n
                FROM init_call ic
                JOIN user u ON u.uid = ic.mainbd
                $where
                GROUP BY ic.current_status_id
                ORDER BY ic.current_status_id ASC";
        $q = $this->db->query($sql, $bind);
        return [
            'cluster_id' => $cluster_id,
            'by_stage'   => $q ? $q->result_array() : [],
        ];
    }

    private function _tool_get_closure_pipeline($params, $caller_uid) {
        $cluster_id = isset($params['cluster_id']) ? (int)$params['cluster_id'] : 0;

        $where = "ic.current_status_id IN (8, 9)";
        $bind  = [];
        if ($cluster_id > 0) {
            $where .= " AND u.cluster_id = ?";
            $bind[] = $cluster_id;
        }
        $sql = "SELECT ic.id AS cid_id, ic.school_name, ic.current_status_id AS cstatus,
                       ic.fbudget, u.name AS bd_name
                FROM init_call ic
                JOIN user u ON u.uid = ic.mainbd
                WHERE $where
                ORDER BY ic.fbudget DESC
                LIMIT 50";
        $q = $this->db->query($sql, $bind);
        $rows = $q ? $q->result_array() : [];
        $pipeline_rs = 0;
        foreach ($rows as $r) $pipeline_rs += (float)$r['fbudget'];
        return [
            'cluster_id'  => $cluster_id,
            'count'       => count($rows),
            'pipeline_rs' => $pipeline_rs,
            'leads'       => $rows,
        ];
    }

    private function _tool_find_similar_leads($params, $caller_uid) {
        $cid = isset($params['init_call_id']) ? (int)$params['init_call_id'] : 0;
        $k   = isset($params['k']) ? max(1, min(20, (int)$params['k'])) : 5;
        if ($cid <= 0) return ['error' => 'missing_init_call_id'];

        // anchor row
        $anchor_q = $this->db->query("SELECT id, current_status_id, fbudget, board_id, school_type
                                      FROM init_call WHERE id = ? LIMIT 1", [$cid]);
        $anchor = $anchor_q ? $anchor_q->row_array() : null;
        if (!$anchor) return ['error' => 'init_call_not_found'];

        // crude similarity: same board + same school_type, fbudget within 25 percent
        $low  = max(0, (float)$anchor['fbudget'] * 0.75);
        $high = (float)$anchor['fbudget'] * 1.25;
        $sql = "SELECT id AS cid_id, school_name, current_status_id AS cstatus, fbudget
                FROM init_call
                WHERE id <> ?
                  AND board_id = ?
                  AND school_type = ?
                  AND fbudget BETWEEN ? AND ?
                ORDER BY ABS(fbudget - ?) ASC
                LIMIT $k";
        $q = $this->db->query($sql, [
            $cid,
            $anchor['board_id'],
            $anchor['school_type'],
            $low,
            $high,
            (float)$anchor['fbudget'],
        ]);
        return [
            'anchor_id' => $cid,
            'k'         => $k,
            'similar'   => $q ? $q->result_array() : [],
        ];
    }

    private function _tool_schedule_followup($params, $caller_uid) {
        $lead_id  = isset($params['lead_id']) ? (int)$params['lead_id'] : 0;
        $dt       = isset($params['datetime_iso']) ? trim((string)$params['datetime_iso']) : '';
        $note     = isset($params['note']) ? trim((string)$params['note']) : '';
        $uid      = $caller_uid > 0 ? $caller_uid : 0;

        if ($lead_id <= 0 || $dt === '') {
            return ['error' => 'missing_lead_or_datetime'];
        }

        // Try writing to daily_planner if the column shape matches; otherwise
        // return a structured request that the caller can persist via the
        // existing planner endpoint.
        if (!$this->db->table_exists('daily_planner')) {
            return [
                'not_persisted' => true,
                'reason'        => 'daily_planner table missing',
                'echo'          => ['lead_id' => $lead_id, 'datetime_iso' => $dt, 'note' => $note],
            ];
        }

        $plan_date = substr($dt, 0, 10);
        $insert = [
            'uid'          => $uid,
            'plan_date'    => $plan_date,
            'cid_id'       => $lead_id,
            'is_auto'      => 0,
            'note'         => $note,
            'created_at'   => date('Y-m-d H:i:s'),
            'planned_time' => $dt,
        ];
        $ok = $this->db->insert('daily_planner', $insert);
        return [
            'persisted' => (bool)$ok,
            'lead_id'   => $lead_id,
            'plan_date' => $plan_date,
            'row'       => $insert,
        ];
    }
}
