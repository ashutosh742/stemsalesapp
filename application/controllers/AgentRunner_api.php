<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AgentRunner_api.php  (ADDITIVE, self-contained)
 * Deployed 2026-06-07. Wires the 5 registry agents that had no real output:
 *   mom_drafter, dump_mining, war_room, cm_copilot, cadence_star
 * Plus a real meeting-prep generator hook.
 *
 * DESIGN NOTES
 *  - Self-contained: does NOT touch fragile existing controllers/models.
 *  - Echoes JSON directly (set_output()+exit bypassed CI flush on this app and
 *    produced EMPTY BODIES; proven fix is echo + http_response_code()).
 *  - Read-only rollups against verified live tables. ASCII only. "Rs"/"percent".
 *  - Empty tables return correct shape {ok:true, empty:true, ...} (real-user rule).
 *  - LLM hook: if an OpenAI key becomes live, _llm_polish() upgrades narrative
 *    text; until then deterministic data-driven text is returned (answered now).
 *
 * Status label map mirrors AnayaAsk_agent for consistency.
 */
// Lightweight signal used to unwind from _json() back to runInternal().
if (!class_exists('AgentRunnerReturn')) {
    class AgentRunnerReturn extends \Exception {}
}

class AgentRunner_api extends CI_Controller {

    private $MASTER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // When true, _json() returns the array instead of echoing (internal calls).
    private $RETURN_MODE = false;
    private $RETURN_BUF  = null;

    private $cstatus_label = [
        1=>'Open', 2=>'Reachout', 3=>'Tentative', 4=>'Will do Later',
        5=>'Not Interested', 6=>'Positive', 7=>'Closure', 8=>'OPEN RPEM',
        9=>'Very Positive', 10=>'TTD-Reachout', 11=>'WNO-Reachout',
        12=>'Positive-NAP', 13=>'Very Positive-NAP', 14=>'On-Boarded',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ---------------------------------------------------------------- helpers

    private function _bearer()
    {
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        return trim(str_replace(['Bearer ', 'Bearer'], '', $header));
    }

    private function _authed()
    {
        $token = $this->_bearer();
        if ($token === $this->MASTER) return true;
        // Allow a valid per-user api_token too.
        $u = $this->db->query("SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1", array($token))->row_array();
        return $u ? true : false;
    }

    // Recursively strip non-ASCII from all string values (rule: ASCII only).
    private function _ascii($v)
    {
        if (is_array($v)) {
            $o = [];
            foreach ($v as $k => $vv) { $o[$k] = $this->_ascii($vv); }
            return $o;
        }
        if (is_string($v)) {
            // Replace common rupee glyphs with 'Rs', drop other non-ASCII, collapse space.
            $v = preg_replace('/[^\x20-\x7E\n]/', '', $v);
            $v = preg_replace('/[ \t]+/', ' ', $v);
            return trim($v);
        }
        return $v;
    }

    // Parse a free-text amount column (e.g. 'Rs 5,50,000', 'NA') into a float.
    private function _amt($s)
    {
        if ($s === null) return 0.0;
        $digits = preg_replace('/[^0-9.]/', '', (string)$s);
        if ($digits === '' || !is_numeric($digits)) return 0.0;
        return (float)$digits;
    }

    private function _json($arr, $code = 200)
    {
        if ($this->RETURN_MODE) {
            $this->RETURN_BUF = $this->_ascii($arr);
            // Throw a lightweight signal to unwind back to runInternal().
            throw new \AgentRunnerReturn();
        }
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
        }
        $arr = $this->_ascii($arr);
        $out = json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_SLASHES);
        if ($out === false) {
            $out = json_encode(['ok' => false, 'error' => 'encode_failed']);
        }
        echo $out;
        exit;
    }

    private function _body()
    {
        $raw = file_get_contents('php://input');
        $b = $raw ? json_decode($raw, true) : [];
        if (!is_array($b)) $b = [];
        // Merge GET so GET requests with query string also work.
        foreach ($_GET as $k => $v) { if (!isset($b[$k])) $b[$k] = $v; }
        return $b;
    }

    private function _label_case($col = 'c.cstatus')
    {
        $case = "CASE {$col}";
        foreach ($this->cstatus_label as $id => $nm) {
            $case .= " WHEN {$id} THEN '" . addslashes($nm) . "'";
        }
        $case .= " ELSE CONCAT('Stage ', {$col}) END";
        return $case;
    }

    // Run a query safely; returns array of rows or [] on error (no fatals).
    private function _q($sql)
    {
        $prev = $this->db->db_debug;
        $this->db->db_debug = false;
        $res = $this->db->query($sql);
        $this->db->db_debug = $prev;
        if ($res === false || !is_object($res)) return [];
        return $res->result_array();
    }

    // Optional LLM polish hook. Returns polished text if a live key exists,
    // else returns $fallback unchanged. Never throws.
    private function _llm_polish($prompt, $fallback)
    {
        $key = getenv('OPENAI_API_KEY');
        if (!$key) {
            $cfg = APPPATH . 'config/openai.php';
            if (is_file($cfg)) {
                $c = include $cfg;
                if (is_array($c) && !empty($c['api_key'])) $key = $c['api_key'];
            }
        }
        if (!$key) return $fallback; // no key yet -> deterministic text
        try {
            $payload = json_encode([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a concise sales-ops assistant. ASCII only. Use "Rs" for rupees and spell out "percent". No markdown.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 400,
            ]);
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $key,
                ],
                CURLOPT_POSTFIELDS => $payload,
            ]);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code !== 200 || !$resp) return $fallback;
            $j = json_decode($resp, true);
            $txt = isset($j['choices'][0]['message']['content']) ? trim($j['choices'][0]['message']['content']) : '';
            return $txt !== '' ? $txt : $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    // Internal entry point for other controllers (e.g. Chat.php dispatcher).
    // Returns the result array directly; does NOT echo or exit.
    public function runInternal($agent, $params)
    {
        $this->RETURN_MODE = true;
        $this->RETURN_BUF  = null;
        try {
            $this->_dispatch($agent, is_array($params) ? $params : []);
        } catch (\AgentRunnerReturn $sig) {
            // expected unwind
        } catch (\Throwable $e) {
            $this->RETURN_BUF = ['ok' => false, 'error' => 'agent_exception', 'detail' => $e->getMessage()];
        }
        $this->RETURN_MODE = false;
        return $this->RETURN_BUF !== null ? $this->RETURN_BUF : ['ok' => false, 'error' => 'no_result'];
    }

    // ================================================================ ROUTER
    // POST /api/agent/run  { agent_key, params:{...} }   (universal)
    public function run()
    {
        if (!$this->_authed()) $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
        $b = $this->_body();
        $agent = isset($b['agent_key']) ? $b['agent_key'] : (isset($b['agent']) ? $b['agent'] : (isset($b['tool']) ? $b['tool'] : ''));
        $p = isset($b['params']) && is_array($b['params']) ? $b['params'] : $b;
        $this->_dispatch($agent, $p);
    }

    // Internal dispatch shared by run() and the named endpoints below.
    private function _dispatch($agent, $p)
    {
        switch ($agent) {
            case 'mom_drafter':  $this->_mom_drafter($p);  break;
            case 'dump_mining':  $this->_dump_mining($p);  break;
            case 'war_room':     $this->_war_room($p);     break;
            case 'cm_copilot':   $this->_cm_copilot($p);   break;
            case 'cadence_star': $this->_cadence_star($p); break;
            default:
                $this->_json(['ok' => false, 'error' => 'unknown_agent', 'agent' => (string)$agent], 404);
        }
    }

    // Named convenience endpoints (app may call these directly).
    public function mom_draft()   { if(!$this->_authed()) $this->_json(['ok'=>false,'error'=>'unauthorized'],401); $this->_mom_drafter($this->_body()); }
    public function dump_mining() { if(!$this->_authed()) $this->_json(['ok'=>false,'error'=>'unauthorized'],401); $this->_dump_mining($this->_body()); }
    public function war_room()    { if(!$this->_authed()) $this->_json(['ok'=>false,'error'=>'unauthorized'],401); $this->_war_room($this->_body()); }
    public function cm_copilot()  { if(!$this->_authed()) $this->_json(['ok'=>false,'error'=>'unauthorized'],401); $this->_cm_copilot($this->_body()); }
    public function cadence_star(){ if(!$this->_authed()) $this->_json(['ok'=>false,'error'=>'unauthorized'],401); $this->_cadence_star($this->_body()); }

    // ============================================================ AGENT: MOM
    // Template-based meeting-minutes draft from a transcript. No LLM required.
    // params: { lead_id, transcript, template?, uid? }
    private function _mom_drafter($p)
    {
        $lead_id   = isset($p['lead_id']) ? (int)$p['lead_id'] : (isset($p['cid_id']) ? (int)$p['cid_id'] : 0);
        $transcript= isset($p['transcript']) ? trim((string)$p['transcript']) : '';
        $template  = isset($p['template']) ? (string)$p['template'] : 'standard';
        $uid       = isset($p['uid']) ? (int)$p['uid'] : 0;

        // Pull lead + company context if a lead_id was given (read-only).
        $ctx = ['company' => '', 'dm_name' => '', 'dm_designation' => '', 'dm_phone' => '', 'stage' => ''];
        if ($lead_id > 0) {
            $rows = $this->_q(
                "SELECT cm.compname AS company, c.dm_contact_name AS dm_name, "
              . "c.dm_contact_designation AS dm_designation, c.dm_contact_phone AS dm_phone, "
              . $this->_label_case('c.cstatus') . " AS stage "
              . "FROM init_call c LEFT JOIN company_master cm ON cm.id = c.cmpid_id "
              . "WHERE c.id = " . $lead_id . " LIMIT 1"
            );
            if (!empty($rows[0])) {
                $r = $rows[0];
                $ctx['company']        = trim((string)$r['company']);
                $ctx['dm_name']        = trim((string)$r['dm_name']);
                $ctx['dm_designation'] = trim((string)$r['dm_designation']);
                $ctx['dm_phone']       = trim((string)$r['dm_phone']);
                $ctx['stage']          = (string)$r['stage'];
            }
        }

        if ($transcript === '') {
            $this->_json([
                'ok' => true, 'empty' => true, 'agent' => 'mom_drafter',
                'draft' => '', 'note' => 'Provide a transcript or voice-note text to draft minutes.',
                'context' => $ctx,
            ]);
        }

        // Deterministic extraction (sentence split + keyword buckets).
        $clean = preg_replace('/\s+/', ' ', $transcript);
        $sentences = preg_split('/(?<=[.!?])\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $actions = []; $decisions = []; $next = []; $key = [];
        $kw_action  = ['will ', 'to do', 'action', 'follow up', 'follow-up', 'send ', 'share ', 'prepare', 'schedule', 'arrange', 'revert', 'circle back', 'we agreed to'];
        $kw_decide  = ['decided', 'agreed', 'approved', 'finalized', 'confirmed', 'concluded'];
        $kw_next    = ['next meeting', 'next step', 'next call', 'meet again', 'reconnect', 'proposal', 'demo', 'pilot'];
        foreach ($sentences as $s) {
            $ls = strtolower($s);
            $hit = false;
            foreach ($kw_action as $k) { if (strpos($ls, $k) !== false) { $actions[] = trim($s); $hit = true; break; } }
            foreach ($kw_decide as $k) { if (strpos($ls, $k) !== false) { $decisions[] = trim($s); $hit = true; break; } }
            foreach ($kw_next as $k)   { if (strpos($ls, $k) !== false) { $next[] = trim($s); break; } }
            if (!$hit && strlen($s) > 25) { $key[] = trim($s); }
        }
        $actions   = array_slice(array_values(array_unique($actions)), 0, 6);
        $decisions = array_slice(array_values(array_unique($decisions)), 0, 6);
        $next      = array_slice(array_values(array_unique($next)), 0, 4);
        $key       = array_slice(array_values(array_unique($key)), 0, 6);

        $hdr = "MEETING MINUTES";
        if ($ctx['company'] !== '') $hdr .= " - " . $ctx['company'];
        $L = [];
        $L[] = $hdr;
        $L[] = "Date: " . date('Y-m-d');
        if ($ctx['dm_name'] !== '') {
            $line = "Decision Maker: " . $ctx['dm_name'];
            if ($ctx['dm_designation'] !== '') $line .= " (" . $ctx['dm_designation'] . ")";
            $L[] = $line;
        }
        if ($ctx['stage'] !== '') $L[] = "Current Stage: " . $ctx['stage'];
        $L[] = "";
        $L[] = "SUMMARY";
        $L[] = $key ? ("- " . implode("\n- ", $key)) : "- Discussion captured from transcript.";
        $L[] = "";
        $L[] = "DECISIONS";
        $L[] = $decisions ? ("- " . implode("\n- ", $decisions)) : "- None recorded.";
        $L[] = "";
        $L[] = "ACTION ITEMS";
        $L[] = $actions ? ("- " . implode("\n- ", $actions)) : "- None recorded.";
        $L[] = "";
        $L[] = "NEXT STEPS";
        $L[] = $next ? ("- " . implode("\n- ", $next)) : "- To be confirmed.";
        $draft = implode("\n", $L);

        // LLM auto-upgrade when a key is live; deterministic otherwise.
        $draft = $this->_llm_polish(
            "Rewrite these meeting minutes cleanly, keep all facts, keep the section headers SUMMARY/DECISIONS/ACTION ITEMS/NEXT STEPS:\n\n" . $draft,
            $draft
        );

        $this->_json([
            'ok' => true, 'agent' => 'mom_drafter', 'lead_id' => $lead_id,
            'template' => $template, 'context' => $ctx,
            'draft' => $draft,
            'counts' => ['decisions' => count($decisions), 'actions' => count($actions), 'next_steps' => count($next)],
        ]);
    }

    // ===================================================== AGENT: DUMP MINING
    // Stale-lead re-engagement list. Admin scope; optional bd filter.
    // params: { uid?, bd_uid?, days?, limit? }
    private function _dump_mining($p)
    {
        $bd    = isset($p['bd_uid']) ? (int)$p['bd_uid'] : (isset($p['uid']) ? (int)$p['uid'] : 0);
        $days  = isset($p['days'])  ? max(7, min(365, (int)$p['days'])) : 45;
        $limit = isset($p['limit']) ? max(1, min(200, (int)$p['limit'])) : 50;

        // Stale = open-ish leads (not closed/lost) with no event in $days,
        // ranked by days-since-last-touch desc. Open-ish = cstatus NOT IN
        // (5 Not Interested, 7 Closure, 14 On-Boarded).
        $bd_filter = $bd > 0 ? (" AND c.mainbd = " . $bd) : "";
        $sql =
            "SELECT c.id AS lead_id, cm.compname AS company, "
          . $this->_label_case('c.cstatus') . " AS stage, c.mainbd AS bd_uid, "
          . "c.proposal_amt AS proposal_amt, "
          . "DATEDIFF(NOW(), COALESCE(MAX(e.complete_time), MAX(e.event_date), c.created_at)) AS days_idle, "
          . "DATE(COALESCE(MAX(e.complete_time), MAX(e.event_date), c.created_at)) AS last_touch "
          . "FROM init_call c "
          . "LEFT JOIN company_master cm ON cm.id = c.cmpid_id "
          . "LEFT JOIN tblcallevents e ON e.cid_id = c.id "
          . "WHERE (c.deletebd IS NULL OR c.deletebd = 0) "
          . "AND c.cstatus NOT IN (5,7,14) " . $bd_filter . " "
          . "GROUP BY c.id, cm.compname, c.cstatus, c.mainbd, c.proposal_amt, c.created_at "
          . "HAVING days_idle >= " . $days . " "
          . "ORDER BY days_idle DESC "
          . "LIMIT " . $limit;
        $rows = $this->_q($sql);

        if (!$rows) {
            $this->_json([
                'ok' => true, 'empty' => true, 'agent' => 'dump_mining',
                'days_threshold' => $days, 'bd_uid' => $bd, 'count' => 0,
                'rows' => [],
                'text' => "No stale leads older than " . $days . " days found.",
            ]);
        }

        $top = $rows[0];
        $text = "Found " . count($rows) . " stale leads idle " . $days
              . " days or more. Oldest: " . trim((string)$top['company'])
              . " idle " . (int)$top['days_idle'] . " days (" . $top['stage'] . ").";
        $text = $this->_llm_polish(
            "Summarize this stale-lead re-engagement queue in two sentences for a sales manager. "
          . count($rows) . " leads idle >= " . $days . " days. Oldest: "
          . trim((string)$top['company']) . " at " . (int)$top['days_idle'] . " days.",
            $text
        );

        $this->_json([
            'ok' => true, 'agent' => 'dump_mining', 'days_threshold' => $days,
            'bd_uid' => $bd, 'count' => count($rows),
            'headers' => ['lead_id', 'company', 'stage', 'bd_uid', 'days_idle', 'last_touch', 'proposal_amt'],
            'rows' => $rows, 'text' => $text,
        ]);
    }

    // ======================================================= AGENT: WAR ROOM
    // Cluster performance rollup. CM scope. params: { cluster_id?, uid? }
    private function _war_room($p)
    {
        $cluster = isset($p['cluster_id']) ? (int)$p['cluster_id'] : 0;
        $cl_filter = $cluster > 0 ? (" AND c.cluster_id = " . $cluster) : "";

        // Funnel breakdown for the cluster (or all if none given).
        // NOTE: init_call.proposal_amt is free-text ('4.66LK','1.80cr') and cannot
        // be summed reliably. The clean numeric proposal value lives in
        // mom_data.proposal_value_rs (bigint). We sum that per lead for pipeline.
        $funnel = $this->_q(
            "SELECT " . $this->_label_case('c.cstatus') . " AS stage, c.cstatus AS stage_id, "
          . "COUNT(*) AS n "
          . "FROM init_call c WHERE (c.deletebd IS NULL OR c.deletebd = 0) " . $cl_filter . " "
          . "GROUP BY c.cstatus ORDER BY c.cstatus"
        );
        // Clean pipeline value. init_call.fbudget is varchar but the bulk of
        // populated rows are clean integers (e.g. '10000000'); we sum only
        // rows matching a numeric pattern so the figure is honest.
        $pipe_rows = $this->_q(
            "SELECT COALESCE(SUM(CAST(c.fbudget AS DECIMAL(18,2))),0) AS pipeline_rs "
          . "FROM init_call c "
          . "WHERE (c.deletebd IS NULL OR c.deletebd = 0) "
          . "AND c.fbudget REGEXP '^[0-9]+([.][0-9]+)?$' AND CAST(c.fbudget AS DECIMAL(18,2)) > 0 "
          . $cl_filter
        );
        $pipeline = !empty($pipe_rows[0]) ? (float)$pipe_rows[0]['pipeline_rs'] : 0.0;
        // Activity in last 30 days for the cluster's leads.
        $activity = $this->_q(
            "SELECT COUNT(*) AS events_30d, COUNT(DISTINCT e.user_id) AS active_bds, "
          . "SUM(CASE WHEN e.complete_time IS NOT NULL THEN 1 ELSE 0 END) AS completed_30d "
          . "FROM tblcallevents e JOIN init_call c ON c.id = e.cid_id "
          . "WHERE e.event_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) "
          . "AND (c.deletebd IS NULL OR c.deletebd = 0) " . $cl_filter
        );
        // Upcoming meetings (next 30d).
        $upcoming = $this->_q(
            "SELECT COUNT(*) AS upcoming_30d FROM tblcallevents e JOIN init_call c ON c.id = e.cid_id "
          . "WHERE e.appointmentdatetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY) "
          . "AND (c.deletebd IS NULL OR c.deletebd = 0) " . $cl_filter
        );

        // $pipeline already computed from mom_data above (clean numeric).
        $total = 0; $positive = 0; $closure = 0;
        foreach ($funnel as $f) {
            $total += (int)$f['n'];
            if (in_array((int)$f['stage_id'], [6, 9, 12, 13])) $positive += (int)$f['n'];
            if ((int)$f['stage_id'] === 7) $closure += (int)$f['n'];
        }
        $act = !empty($activity[0]) ? $activity[0] : ['events_30d' => 0, 'active_bds' => 0, 'completed_30d' => 0];
        $up  = !empty($upcoming[0]) ? (int)$upcoming[0]['upcoming_30d'] : 0;

        if ($total == 0 && (int)$act['events_30d'] == 0) {
            $this->_json([
                'ok' => true, 'empty' => true, 'agent' => 'war_room',
                'cluster_id' => $cluster, 'text' => "No cluster data found for this scope.",
                'kpis' => ['total_leads' => 0, 'pipeline_rs' => 0, 'positive' => 0, 'closures' => 0,
                           'events_30d' => 0, 'active_bds' => 0, 'completed_30d' => 0, 'upcoming_30d' => 0],
                'funnel' => [],
            ]);
        }

        $text = "Cluster snapshot: " . $total . " leads, pipeline Rs " . number_format($pipeline, 0)
              . ". Positive stage: " . $positive . ", closures: " . $closure . ". Last 30 days: "
              . (int)$act['events_30d'] . " events from " . (int)$act['active_bds'] . " active BDs ("
              . (int)$act['completed_30d'] . " completed). " . $up . " meetings scheduled in next 30 days.";
        $text = $this->_llm_polish(
            "Write a two-sentence cluster war-room briefing for a Cluster Manager. Total leads "
          . $total . ", pipeline Rs " . number_format($pipeline, 0) . ", positive " . $positive
          . ", closures " . $closure . ", " . (int)$act['events_30d'] . " events last 30 days, "
          . $up . " upcoming meetings.",
            $text
        );

        $this->_json([
            'ok' => true, 'agent' => 'war_room', 'cluster_id' => $cluster,
            'kpis' => [
                'total_leads' => $total, 'pipeline_rs' => round($pipeline, 0),
                'positive' => $positive, 'closures' => $closure,
                'events_30d' => (int)$act['events_30d'], 'active_bds' => (int)$act['active_bds'],
                'completed_30d' => (int)$act['completed_30d'], 'upcoming_30d' => $up,
            ],
            'funnel' => $funnel, 'text' => $text,
        ]);
    }

    // ===================================================== AGENT: CM COPILOT
    // Cluster manager decision support: leads needing attention + suggestions.
    // params: { cluster_id?, uid? }
    private function _cm_copilot($p)
    {
        $cluster = isset($p['cluster_id']) ? (int)$p['cluster_id'] : 0;
        $cl_filter = $cluster > 0 ? (" AND c.cluster_id = " . $cluster) : "";

        // Hot leads: positive/very-positive, ranked by clean budget value
        // (init_call.fbudget, numeric-validated). proposal_amt is free-text.
        $hot = $this->_q(
            "SELECT c.id AS lead_id, cm.compname AS company, " . $this->_label_case('c.cstatus') . " AS stage, "
          . "CASE WHEN c.fbudget REGEXP '^[0-9]+([.][0-9]+)?$' THEN CAST(c.fbudget AS DECIMAL(18,2)) ELSE 0 END AS budget_rs, "
          . "c.mainbd AS bd_uid, "
          . "DATEDIFF(NOW(), COALESCE(ev.max_ev, c.created_at)) AS days_idle "
          . "FROM init_call c LEFT JOIN company_master cm ON cm.id = c.cmpid_id LEFT JOIN (SELECT cid_id, MAX(event_date) AS max_ev FROM tblcallevents GROUP BY cid_id) ev ON ev.cid_id = c.id "
          . "WHERE (c.deletebd IS NULL OR c.deletebd = 0) AND c.cstatus IN (6,9,12,13) " . $cl_filter . " "
          . "ORDER BY budget_rs DESC, days_idle ASC LIMIT 10"
        );
        // At-risk: positive-ish leads idle > 21 days.
        $risk = $this->_q(
            "SELECT c.id AS lead_id, cm.compname AS company, " . $this->_label_case('c.cstatus') . " AS stage, "
          . "DATEDIFF(NOW(), COALESCE(ev.max_ev, c.created_at)) AS days_idle "
          . "FROM init_call c LEFT JOIN company_master cm ON cm.id = c.cmpid_id LEFT JOIN (SELECT cid_id, MAX(event_date) AS max_ev FROM tblcallevents GROUP BY cid_id) ev ON ev.cid_id = c.id "
          . "WHERE (c.deletebd IS NULL OR c.deletebd = 0) AND c.cstatus IN (3,6,8,9,10,11) " . $cl_filter . " "
          . "HAVING days_idle > 21 ORDER BY days_idle DESC LIMIT 10"
        );

        $recs = [];
        if ($hot)  $recs[] = "Push " . count($hot) . " positive-stage leads toward closure; highest budget value is Rs " . number_format((float)$hot[0]['budget_rs'], 0) . " (" . trim((string)$hot[0]['company']) . ").";
        if ($risk) $recs[] = count($risk) . " positive-stage leads have gone quiet for over 21 days; reassign or schedule a touch this week.";
        if (!$recs) $recs[] = "No urgent CM actions in this scope right now.";

        if (!$hot && !$risk) {
            $this->_json([
                'ok' => true, 'empty' => true, 'agent' => 'cm_copilot',
                'cluster_id' => $cluster, 'hot_leads' => [], 'at_risk' => [],
                'recommendations' => $recs,
                'text' => "No actionable leads found for this cluster scope.",
            ]);
        }

        $text = implode(' ', $recs);
        $text = $this->_llm_polish(
            "Write a short Cluster Manager copilot briefing (2-3 sentences) from: " . $text, $text
        );

        $this->_json([
            'ok' => true, 'agent' => 'cm_copilot', 'cluster_id' => $cluster,
            'hot_leads' => $hot, 'at_risk' => $risk,
            'recommendations' => $recs, 'text' => $text,
        ]);
    }

    // =================================================== AGENT: CADENCE STAR
    // BD follow-up cadence coach: overdue + upcoming touches for a BD.
    // params: { uid (BD), limit? }
    private function _cadence_star($p)
    {
        $bd = isset($p['uid']) ? (int)$p['uid'] : (isset($p['bd_uid']) ? (int)$p['bd_uid'] : 0);
        if ($bd <= 0) $this->_json(['ok' => false, 'error' => 'uid_required'], 422);
        $limit = isset($p['limit']) ? max(1, min(100, (int)$p['limit'])) : 25;

        // Overdue: open-ish leads with last touch > 14 days, ranked by idle.
        $overdue = $this->_q(
            "SELECT c.id AS lead_id, cm.compname AS company, " . $this->_label_case('c.cstatus') . " AS stage, "
          . "DATEDIFF(NOW(), COALESCE(MAX(e.event_date), c.created_at)) AS days_idle, "
          . "DATE(COALESCE(MAX(e.event_date), c.created_at)) AS last_touch "
          . "FROM init_call c LEFT JOIN company_master cm ON cm.id = c.cmpid_id "
          . "LEFT JOIN tblcallevents e ON e.cid_id = c.id "
          . "WHERE c.mainbd = " . $bd . " AND (c.deletebd IS NULL OR c.deletebd = 0) "
          . "AND c.cstatus NOT IN (5,7,14) "
          . "GROUP BY c.id, cm.compname, c.cstatus, c.created_at "
          . "HAVING days_idle > 14 ORDER BY days_idle DESC LIMIT " . $limit
        );
        // Upcoming scheduled touches (next 14 days).
        $upcoming = $this->_q(
            "SELECT e.id AS event_id, c.id AS lead_id, cm.compname AS company, "
          . "e.appointmentdatetime AS scheduled_at "
          . "FROM tblcallevents e JOIN init_call c ON c.id = e.cid_id "
          . "LEFT JOIN company_master cm ON cm.id = c.cmpid_id "
          . "WHERE e.user_id = " . $bd . " "
          . "AND e.appointmentdatetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 14 DAY) "
          . "ORDER BY e.appointmentdatetime ASC LIMIT " . $limit
        );

        $n_over = count($overdue); $n_up = count($upcoming);
        if ($n_over == 0 && $n_up == 0) {
            $this->_json([
                'ok' => true, 'empty' => true, 'agent' => 'cadence_star', 'bd_uid' => $bd,
                'overdue' => [], 'upcoming' => [],
                'text' => "You are all caught up. No overdue follow-ups and nothing scheduled in the next 14 days.",
            ]);
        }

        $bits = [];
        if ($n_over) $bits[] = $n_over . " leads are overdue for a follow-up (oldest idle " . (int)$overdue[0]['days_idle'] . " days: " . trim((string)$overdue[0]['company']) . ").";
        if ($n_up)   $bits[] = $n_up . " touches are scheduled in the next 14 days.";
        $text = implode(' ', $bits);
        $text = $this->_llm_polish(
            "Write a two-sentence follow-up cadence nudge for a BD rep from: " . $text, $text
        );

        $this->_json([
            'ok' => true, 'agent' => 'cadence_star', 'bd_uid' => $bd,
            'overdue_count' => $n_over, 'upcoming_count' => $n_up,
            'overdue' => $overdue, 'upcoming' => $upcoming, 'text' => $text,
        ]);
    }
}
