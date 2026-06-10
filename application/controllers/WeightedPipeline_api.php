<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WeightedPipeline_api.php  (Phase 2 - Agent E - 2026-06-08)
 *
 * C5b: Stage Probability config  (table: stage_probability)
 * C5c + F3: Weighted Pipeline + Rs 200 Cr Back-Plan
 *
 * Endpoints:
 *   GET  /api/probability/config         List all stage probabilities
 *   POST /api/probability/set            {stage_code, probability_pct} update probability
 *   GET  /api/pipeline/weighted          Weighted pipeline summary
 *   GET  /api/pipeline/backplan?target_rs_cr=200  Gap analysis vs target
 *
 * Bearer token required. 401 without token.
 * Output: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class WeightedPipeline_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    private $_stage_labels = [
        1  => 'Open',
        2  => 'Reachout',
        3  => 'Tentative',
        4  => 'Will-do-Later',
        5  => 'Not-Interested',
        6  => 'Positive',
        7  => 'Closure',
        8  => 'OPEN RPEM',
        9  => 'Very-Positive',
        10 => 'TTD-Reachout',
        11 => 'WNO-Reachout',
        12 => 'Positive-NAP',
        13 => 'Very-Positive-NAP',
        14 => 'On-Boarded',
    ];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth helpers
    // ------------------------------------------------------------------
    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env   = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // rimlyproof_bearerdelegate_20260608: also accept per-user login token via shared BearerAuth library (additive)
        try {
            $CI =& get_instance();
            if (!isset($CI->bearerauth)) { $CI->load->library('BearerAuth'); }
            $___ba = $CI->bearerauth->resolve();
            if (!empty($___ba['ok']) && !empty($___ba['uid'])) {
                if (property_exists($this, '_authed_uid')) { $this->_authed_uid = (int)$___ba['uid']; }
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        ini_set("serialize_precision", "10");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function _post_body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $d = json_decode($raw, true);
            if (is_array($d)) return $d;
        }
        return $_POST;
    }

    // ------------------------------------------------------------------
    // GET /api/probability/config
    // ------------------------------------------------------------------
    public function config() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $rows = $this->db->query(
            "SELECT stage_code, probability_pct, active
             FROM stage_probability ORDER BY stage_code"
        )->result_array();

        if (empty($rows)) {
            $this->_json(['ok' => true, 'empty' => true, 'probabilities' => []]);
        }

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'stage_code'      => (int)$r['stage_code'],
                'stage_label'     => $this->_stage_labels[(int)$r['stage_code']] ?? 'Unknown',
                'probability_pct' => (int)$r['probability_pct'],
                'active'          => (bool)$r['active'],
            ];
        }

        $this->_json(['ok' => true, 'count' => count($out), 'probabilities' => $out]);
    }

    // ------------------------------------------------------------------
    // POST /api/probability/set  {stage_code, probability_pct}
    // ------------------------------------------------------------------
    public function set_prob() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $body        = $this->_post_body();
        $stage_code  = (int)($body['stage_code']      ?? -1);
        $prob_pct    = (int)($body['probability_pct'] ?? -1);

        if ($stage_code < 1 || $stage_code > 14 || $prob_pct < 0 || $prob_pct > 100) {
            $this->_json([
                'ok'    => false,
                'error' => 'stage_code (1-14) and probability_pct (0-100) are required',
            ], 422);
        }

        $this->db->query(
            "INSERT INTO stage_probability (stage_code, probability_pct, active)
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE probability_pct=VALUES(probability_pct)",
            [$stage_code, $prob_pct]
        );

        $this->_json([
            'ok'              => true,
            'stage_code'      => $stage_code,
            'stage_label'     => $this->_stage_labels[$stage_code] ?? 'Unknown',
            'probability_pct' => $prob_pct,
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/pipeline/weighted
    // ------------------------------------------------------------------
    public function weighted() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // Total lead counts
        $totals = $this->db->query(
            "SELECT
               COUNT(*) AS total_leads,
               SUM(CASE WHEN ov.lead_id IS NOT NULL THEN 1 ELSE 0 END) AS count_valued,
               SUM(CASE WHEN ov.lead_id IS NULL THEN 1 ELSE 0 END)     AS count_pending_value
             FROM init_call ic
             LEFT JOIN opportunity_value ov ON ov.lead_id = ic.id"
        )->row_array();

        $count_valued  = (int)($totals['count_valued']        ?? 0);
        $count_pending = (int)($totals['count_pending_value'] ?? 0);

        // Overall weighted sum for leads WITH opportunity_value
        $overall = $this->db->query(
            "SELECT
               SUM(ov.value_rs * sp.probability_pct / 100) AS total_weighted_rs,
               SUM(ov.value_rs)                            AS total_raw_rs,
               COUNT(*)                                    AS valued_count
             FROM init_call ic
             INNER JOIN opportunity_value ov ON ov.lead_id = ic.id
             INNER JOIN stage_probability sp ON sp.stage_code = ic.cstatus AND sp.active = 1"
        )->row_array();

        $total_weighted = (float)($overall['total_weighted_rs'] ?? 0);
        $total_raw      = (float)($overall['total_raw_rs']      ?? 0);

        // By cluster
        $cluster_rows = $this->db->query(
            "SELECT
               ic.cluster_id,
               c.clustername,
               SUM(ov.value_rs * sp.probability_pct / 100) AS weighted_rs,
               SUM(ov.value_rs)                            AS raw_rs,
               COUNT(*)                                    AS count_valued
             FROM init_call ic
             INNER JOIN opportunity_value ov ON ov.lead_id = ic.id
             INNER JOIN stage_probability sp ON sp.stage_code = ic.cstatus AND sp.active = 1
             LEFT JOIN cluster c ON c.id = ic.cluster_id
             GROUP BY ic.cluster_id, c.clustername
             ORDER BY weighted_rs DESC
             LIMIT 50"
        )->result_array();

        $by_cluster = [];
        foreach ($cluster_rows as $r) {
            $by_cluster[] = [
                'cluster_id'   => $r['cluster_id'] ? (int)$r['cluster_id'] : null,
                'cluster_name' => $r['clustername'] ?? 'Unassigned',
                'weighted_rs'  => (float)$r['weighted_rs'],
                'raw_rs'       => (float)$r['raw_rs'],
                'count_valued' => (int)$r['count_valued'],
            ];
        }

        // By BD (mainbd)
        $bd_rows = $this->db->query(
            "SELECT
               ic.mainbd,
               u.name AS bd_name,
               SUM(ov.value_rs * sp.probability_pct / 100) AS weighted_rs,
               SUM(ov.value_rs)                            AS raw_rs,
               COUNT(*)                                    AS count_valued
             FROM init_call ic
             INNER JOIN opportunity_value ov ON ov.lead_id = ic.id
             INNER JOIN stage_probability sp ON sp.stage_code = ic.cstatus AND sp.active = 1
             LEFT JOIN user u ON u.uid = ic.mainbd
             GROUP BY ic.mainbd, u.name
             ORDER BY weighted_rs DESC
             LIMIT 50"
        )->result_array();

        $by_bd = [];
        foreach ($bd_rows as $r) {
            $by_bd[] = [
                'mainbd'       => (int)$r['mainbd'],
                'bd_name'      => $r['bd_name'] ?? 'Unknown BD',
                'weighted_rs'  => (float)$r['weighted_rs'],
                'raw_rs'       => (float)$r['raw_rs'],
                'count_valued' => (int)$r['count_valued'],
            ];
        }

        $note = 'Pre-April 2026 proposals show value pending and are excluded from weighted calculation. '
              . 'Weighted pipeline only reflects leads with opportunity_value set. '
              . 'count_pending_value leads have no value entered yet.';

        if ($count_valued === 0) {
            $this->_json([
                'ok'                 => true,
                'empty'              => true,
                'total_weighted_rs'  => 0,
                'total_raw_rs'       => 0,
                'count_valued'       => 0,
                'count_pending_value'=> $count_pending,
                'by_cluster'         => [],
                'by_bd'              => [],
                'note'               => $note,
            ]);
        }

        $this->_json([
            'ok'                  => true,
            'total_weighted_rs'   => round($total_weighted, 2),
            'total_raw_rs'        => round($total_raw, 2),
            'count_valued'        => $count_valued,
            'count_pending_value' => $count_pending,
            'by_cluster'          => $by_cluster,
            'by_bd'               => $by_bd,
            'note'                => $note,
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/pipeline/backplan?target_rs_cr=200
    // ------------------------------------------------------------------
    public function backplan() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // Target resolution (permanent fix, no hardcoded 200 Cr):
        // 1) explicit ?target_rs_cr override (used by admins for what-if),
        // 2) else the REAL configured target in pipeline_coverage_config
        //    (scope-aware: bd/cm/rm/org). Admins and line managers set this
        //    via POST /api/pipeline/target/set.
        $scope_type = strtolower((string)($_GET['scope_type'] ?? 'org'));
        if (!in_array($scope_type, ['bd','cm','rm','org'], true)) $scope_type = 'org';
        $scope_uid  = (int)($_GET['scope_uid'] ?? 0);
        $target_cr  = (float)($_GET['target_rs_cr'] ?? 0);
        $target_source = 'override';
        if ($target_cr <= 0) {
            $cfg = $this->db->query(
                "SELECT target_rs FROM pipeline_coverage_config
                 WHERE scope_type = ? AND scope_uid = ?
                 ORDER BY updated_at DESC LIMIT 1",
                [$scope_type, $scope_uid]
            )->row_array();
            if ($cfg && (float)$cfg['target_rs'] > 0) {
                $target_cr = (float)$cfg['target_rs'] / 10000000;
                $target_source = 'config';
            } else {
                // Org fallback if a narrower scope has no row yet.
                $org = $this->db->query(
                    "SELECT target_rs FROM pipeline_coverage_config
                     WHERE scope_type = 'org' ORDER BY updated_at DESC LIMIT 1"
                )->row_array();
                if ($org && (float)$org['target_rs'] > 0) {
                    $target_cr = (float)$org['target_rs'] / 10000000;
                    $target_source = 'org_fallback';
                }
            }
        }
        if ($target_cr <= 0) { $target_cr = 0; $target_source = 'unset'; }

        // Convert target from Cr to Rs (1 Cr = 10,000,000)
        $target_rs = $target_cr * 10000000;

        // Fetch weighted pipeline totals
        $overall = $this->db->query(
            "SELECT
               COALESCE(SUM(ov.value_rs * sp.probability_pct / 100), 0) AS total_weighted_rs,
               COALESCE(SUM(ov.value_rs), 0)                            AS total_raw_rs,
               COUNT(*)                                                  AS count_valued
             FROM init_call ic
             INNER JOIN opportunity_value ov ON ov.lead_id = ic.id
             INNER JOIN stage_probability sp ON sp.stage_code = ic.cstatus AND sp.active = 1"
        )->row_array();

        $total_weighted_rs = (float)($overall['total_weighted_rs'] ?? 0);
        $total_raw_rs      = (float)($overall['total_raw_rs']      ?? 0);
        $count_valued      = (int)($overall['count_valued']        ?? 0);

        // Total pending count
        $pend = $this->db->query(
            "SELECT COUNT(*) as cnt FROM init_call ic
             LEFT JOIN opportunity_value ov ON ov.lead_id = ic.id
             WHERE ov.lead_id IS NULL"
        )->row_array();
        $count_pending = (int)($pend['cnt'] ?? 0);

        $gap_rs         = max(0, $target_rs - $total_weighted_rs);
        $gap_cr         = $gap_rs / 10000000;
        $weighted_cr    = $total_weighted_rs / 10000000;
        $coverage_pct   = ($target_rs > 0) ? round(($total_weighted_rs / $target_rs) * 100, 2) : 0;

        // By cluster breakdown
        $cluster_rows = $this->db->query(
            "SELECT
               ic.cluster_id,
               c.clustername,
               COALESCE(SUM(ov.value_rs * sp.probability_pct / 100), 0) AS weighted_rs,
               COALESCE(SUM(ov.value_rs), 0)                            AS raw_rs,
               COUNT(*)                                                  AS count_valued
             FROM init_call ic
             INNER JOIN opportunity_value ov ON ov.lead_id = ic.id
             INNER JOIN stage_probability sp ON sp.stage_code = ic.cstatus AND sp.active = 1
             LEFT JOIN cluster c ON c.id = ic.cluster_id
             GROUP BY ic.cluster_id, c.clustername
             ORDER BY weighted_rs DESC
             LIMIT 50"
        )->result_array();

        $by_cluster = [];
        foreach ($cluster_rows as $r) {
            $w_cr = (float)$r['weighted_rs'] / 10000000;
            $by_cluster[] = [
                'cluster_id'        => $r['cluster_id'] ? (int)$r['cluster_id'] : null,
                'cluster_name'      => $r['clustername'] ?? 'Unassigned',
                'weighted_rs_cr'    => round($w_cr, 4),
                'raw_rs_cr'         => round((float)$r['raw_rs'] / 10000000, 4),
                'count_valued'      => (int)$r['count_valued'],
                'coverage_of_target_percent' => ($target_rs > 0)
                    ? round(((float)$r['weighted_rs'] / $target_rs) * 100, 2)
                    : 0,
            ];
        }

        // By BD breakdown
        $bd_rows = $this->db->query(
            "SELECT
               ic.mainbd,
               u.name AS bd_name,
               COALESCE(SUM(ov.value_rs * sp.probability_pct / 100), 0) AS weighted_rs,
               COALESCE(SUM(ov.value_rs), 0)                            AS raw_rs,
               COUNT(*)                                                  AS count_valued
             FROM init_call ic
             INNER JOIN opportunity_value ov ON ov.lead_id = ic.id
             INNER JOIN stage_probability sp ON sp.stage_code = ic.cstatus AND sp.active = 1
             LEFT JOIN user u ON u.uid = ic.mainbd
             GROUP BY ic.mainbd, u.name
             ORDER BY weighted_rs DESC
             LIMIT 50"
        )->result_array();

        $by_bd = [];
        foreach ($bd_rows as $r) {
            $w_cr = (float)$r['weighted_rs'] / 10000000;
            $by_bd[] = [
                'mainbd'            => (int)$r['mainbd'],
                'bd_name'           => $r['bd_name'] ?? 'Unknown BD',
                'weighted_rs_cr'    => round($w_cr, 4),
                'raw_rs_cr'         => round((float)$r['raw_rs'] / 10000000, 4),
                'count_valued'      => (int)$r['count_valued'],
                'coverage_of_target_percent' => ($target_rs > 0)
                    ? round(((float)$r['weighted_rs'] / $target_rs) * 100, 2)
                    : 0,
            ];
        }

        // Build note inline
        $pending_note = ($count_pending > 0)
            ? ($count_pending . ' leads have no opportunity_value entered (value pending).')
            : 'All leads have opportunity_value entered.';

        $note = 'Weighted pipeline = sum(opportunity_value x stage_probability / 100). '
              . 'Pre-April 2026 proposals show value pending and are excluded. '
              . $pending_note
              . ' Gap = target minus weighted pipeline (Rs Cr).';

        $this->_json([
            'ok'                        => true,
            'target_rs_cr'              => $target_cr,
            'target_rs'                 => $target_rs,
            'target_source'             => $target_source,
            'scope_type'                => $scope_type,
            'scope_uid'                 => $scope_uid,
            'weighted_pipeline_rs_cr'   => round($weighted_cr, 4),
            'weighted_pipeline_rs'      => round($total_weighted_rs, 2),
            'raw_pipeline_rs_cr'        => round($total_raw_rs / 10000000, 4),
            'gap_rs_cr'                 => round($gap_cr, 4),
            'gap_rs'                    => round($gap_rs, 2),
            'coverage_percent'          => $coverage_pct,
            'count_valued'              => $count_valued,
            'count_pending_value'       => $count_pending,
            'by_cluster'                => $by_cluster,
            'by_bd'                     => $by_bd,
            'note'                      => $note,
        ]);
    }
    // ------------------------------------------------------------------
    // GET /api/pipeline/target  -> read the configured target (any signed-in user)
    //   optional ?scope_type=org|bd|cm|rm  &scope_uid=<id>
    // ------------------------------------------------------------------
    public function target_get() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }
        $scope_type = strtolower((string)($_GET['scope_type'] ?? 'org'));
        if (!in_array($scope_type, ['bd','cm','rm','org'], true)) $scope_type = 'org';
        $scope_uid  = (int)($_GET['scope_uid'] ?? 0);

        $row = $this->db->query(
            "SELECT id, scope_type, scope_uid, target_rs, period_start, period_end,
                    healthy_ratio_min, healthy_ratio_max, updated_at
             FROM pipeline_coverage_config
             WHERE scope_type = ? AND scope_uid = ?
             ORDER BY updated_at DESC LIMIT 1",
            [$scope_type, $scope_uid]
        )->row_array();

        if (!$row) {
            $this->_json([
                'ok' => true, 'found' => false,
                'scope_type' => $scope_type, 'scope_uid' => $scope_uid,
                'target_rs' => 0, 'target_rs_cr' => 0,
            ]);
        }
        $this->_json([
            'ok' => true, 'found' => true,
            'id' => (int)$row['id'],
            'scope_type' => $row['scope_type'],
            'scope_uid'  => (int)$row['scope_uid'],
            'target_rs'  => (float)$row['target_rs'],
            'target_rs_cr' => round((float)$row['target_rs'] / 10000000, 4),
            'period_start' => $row['period_start'],
            'period_end'   => $row['period_end'],
            'updated_at'   => $row['updated_at'],
        ]);
    }

    // ------------------------------------------------------------------
    // POST /api/pipeline/target/set  -> ADMIN + LINE MANAGERS only.
    //   Body: uid (caller), target_rs_cr, scope_type(org|bd|cm|rm), scope_uid,
    //         optional period_start, period_end.
    //   Admins (SuperAdmin/Admin) may set any scope. Line managers
    //   (RM/Cluster Manager/Asst Cluster Manager/Asst Sales Heads) may set
    //   org/their-scope targets. BDs cannot set targets.
    // ------------------------------------------------------------------
    public function target_set() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }
        $b = $this->_post_body();
        $uid = (int)($b['uid'] ?? 0);
        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid required'], 200);
        }
        // Resolve caller role.
        $caller = $this->db->query(
            "SELECT uid, type_id FROM user WHERE uid = ? AND active = 1 LIMIT 1",
            [$uid]
        )->row_array();
        if (!$caller) {
            $this->_json(['ok' => false, 'error' => 'Unknown or inactive user'], 200);
        }
        $type_id = (int)$caller['type_id'];
        // Admins: 1 SuperAdmin, 2 Admin. Line managers: 10 RM, 13 Cluster Mgr,
        // 19/20/21 Asst Sales Heads, 22 Regional Mgr, 24 Asst Cluster Mgr.
        $admins   = [1, 2];
        $managers = [10, 13, 19, 20, 21, 22, 24];
        $allowed  = array_merge($admins, $managers);
        if (!in_array($type_id, $allowed, true)) {
            $this->_json([
                'ok' => false,
                'error' => 'Only admins and line managers can set targets',
                'your_type_id' => $type_id,
            ], 200);
        }

        $target_cr = (float)($b['target_rs_cr'] ?? 0);
        if ($target_cr <= 0) {
            $this->_json(['ok' => false, 'error' => 'target_rs_cr must be greater than 0'], 200);
        }
        $target_rs = $target_cr * 10000000;

        $scope_type = strtolower((string)($b['scope_type'] ?? 'org'));
        if (!in_array($scope_type, ['bd','cm','rm','org'], true)) $scope_type = 'org';
        $scope_uid  = (int)($b['scope_uid'] ?? 0);

        // Non-admin managers cannot set the org-wide target.
        if (in_array($type_id, $managers, true) && !in_array($type_id, $admins, true)
            && $scope_type === 'org') {
            $this->_json([
                'ok' => false,
                'error' => 'Line managers cannot change the org target; contact an admin',
            ], 200);
        }

        $period_start = $b['period_start'] ?? null;
        $period_end   = $b['period_end'] ?? null;

        // Upsert: update the latest matching scope row, else insert.
        $existing = $this->db->query(
            "SELECT id, period_start, period_end FROM pipeline_coverage_config
             WHERE scope_type = ? AND scope_uid = ?
             ORDER BY updated_at DESC LIMIT 1",
            [$scope_type, $scope_uid]
        )->row_array();

        if ($existing) {
            $ps = $period_start ?: $existing['period_start'];
            $pe = $period_end   ?: $existing['period_end'];
            $this->db->query(
                "UPDATE pipeline_coverage_config
                 SET target_rs = ?, period_start = ?, period_end = ?
                 WHERE id = ?",
                [$target_rs, $ps, $pe, (int)$existing['id']]
            );
            $row_id = (int)$existing['id'];
            $action = 'updated';
        } else {
            $ps = $period_start ?: date('Y-m-d');
            $pe = $period_end   ?: date('Y-m-d', strtotime('+90 days'));
            $this->db->query(
                "INSERT INTO pipeline_coverage_config
                   (scope_type, scope_uid, target_rs, period_start, period_end)
                 VALUES (?, ?, ?, ?, ?)",
                [$scope_type, $scope_uid, $target_rs, $ps, $pe]
            );
            $row_id = (int)$this->db->insert_id();
            $action = 'inserted';
        }

        $this->_json([
            'ok' => true,
            'action' => $action,
            'id' => $row_id,
            'scope_type' => $scope_type,
            'scope_uid'  => $scope_uid,
            'target_rs'  => $target_rs,
            'target_rs_cr' => round($target_cr, 4),
            'set_by_uid' => $uid,
            'set_by_type_id' => $type_id,
        ]);
    }
}
