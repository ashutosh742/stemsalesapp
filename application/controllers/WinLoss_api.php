<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WinLoss_api.php  (Phase 2 - Agent E - F6 - 2026-06-08)
 *
 * Win/Loss Analytics built on Phase 1 lead_loss + loss_reason_master tables
 * and init_call closures (cstatus IN (7, 14)).
 *
 * Endpoints:
 *   GET /api/winloss/summary        wins, losses, win_rate_percent, loss by reason
 *   GET /api/winloss/by_cluster     summary grouped by cluster
 *   GET /api/winloss/by_bd          summary grouped by BD (mainbd)  (also ?by_bd=1 alias)
 *
 * Bearer token required. 401 without token.
 * Output: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class WinLoss_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

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

    // ------------------------------------------------------------------
    // GET /api/winloss/summary
    // ------------------------------------------------------------------
    public function summary() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // Wins: closures (cstatus 7 or 14)
        $wins_row = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM init_call WHERE cstatus IN (7, 14)"
        )->row_array();
        $wins = (int)($wins_row['cnt'] ?? 0);

        // Losses: rows in lead_loss
        $losses_row = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM lead_loss"
        )->row_array();
        $losses = (int)($losses_row['cnt'] ?? 0);

        $total_decided = $wins + $losses;
        $win_rate = ($total_decided > 0)
            ? round(($wins / $total_decided) * 100, 2)
            : 0;

        // Loss breakdown by reason
        $reason_rows = $this->db->query(
            "SELECT lrm.code, lrm.label,
                    COUNT(ll.id) AS loss_count
             FROM loss_reason_master lrm
             LEFT JOIN lead_loss ll ON ll.reason_id = lrm.id
             WHERE lrm.active = 1
             GROUP BY lrm.id, lrm.code, lrm.label
             ORDER BY loss_count DESC"
        )->result_array();

        $loss_by_reason = [];
        foreach ($reason_rows as $r) {
            $pct = ($losses > 0) ? round(((int)$r['loss_count'] / $losses) * 100, 2) : 0;
            $loss_by_reason[] = [
                'reason_code'          => $r['code'],
                'reason_label'         => $r['label'],
                'count'                => (int)$r['loss_count'],
                'percent_of_losses'    => $pct,
            ];
        }

        if ($wins === 0 && $losses === 0) {
            $this->_json([
                'ok'               => true,
                'empty'            => true,
                'wins'             => 0,
                'losses'           => 0,
                'win_rate_percent' => 0,
                'loss_by_reason'   => [],
                'note'             => 'No closures or loss records found.',
            ]);
        }

        $this->_json([
            'ok'               => true,
            'wins'             => $wins,
            'losses'           => $losses,
            'total_decided'    => $total_decided,
            'win_rate_percent' => $win_rate,
            'loss_by_reason'   => $loss_by_reason,
            'note'             => 'Wins = count of init_call with cstatus 7 (Closure) or 14 (On-Boarded). Losses = count of lead_loss records.',
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/winloss/by_cluster
    // ------------------------------------------------------------------
    public function by_cluster() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // Wins by cluster
        $win_rows = $this->db->query(
            "SELECT ic.cluster_id, c.clustername,
                    COUNT(*) AS wins
             FROM init_call ic
             LEFT JOIN cluster c ON c.id = ic.cluster_id
             WHERE ic.cstatus IN (7, 14)
             GROUP BY ic.cluster_id, c.clustername
             ORDER BY wins DESC
             LIMIT 50"
        )->result_array();

        // Losses by cluster
        $loss_rows = $this->db->query(
            "SELECT ic.cluster_id, c.clustername,
                    COUNT(ll.id) AS losses
             FROM lead_loss ll
             INNER JOIN init_call ic ON ic.id = ll.lead_id
             LEFT JOIN cluster c ON c.id = ic.cluster_id
             GROUP BY ic.cluster_id, c.clustername
             ORDER BY losses DESC
             LIMIT 50"
        )->result_array();

        // Merge into map
        $map = [];
        foreach ($win_rows as $r) {
            $k = (int)$r['cluster_id'];
            if (!isset($map[$k])) {
                $map[$k] = [
                    'cluster_id'   => $k ?: null,
                    'cluster_name' => $r['clustername'] ?? 'Unassigned',
                    'wins'         => 0,
                    'losses'       => 0,
                ];
            }
            $map[$k]['wins'] = (int)$r['wins'];
        }
        foreach ($loss_rows as $r) {
            $k = (int)$r['cluster_id'];
            if (!isset($map[$k])) {
                $map[$k] = [
                    'cluster_id'   => $k ?: null,
                    'cluster_name' => $r['clustername'] ?? 'Unassigned',
                    'wins'         => 0,
                    'losses'       => 0,
                ];
            }
            $map[$k]['losses'] = (int)$r['losses'];
        }

        $out = [];
        foreach ($map as $item) {
            $decided = $item['wins'] + $item['losses'];
            $item['win_rate_percent'] = ($decided > 0)
                ? round(($item['wins'] / $decided) * 100, 2)
                : 0;
            $out[] = $item;
        }

        // Sort by wins desc
        usort($out, function($a, $b) { return $b['wins'] - $a['wins']; });

        if (empty($out)) {
            $this->_json([
                'ok'         => true,
                'empty'      => true,
                'by_cluster' => [],
            ]);
        }

        $this->_json(['ok' => true, 'count' => count($out), 'by_cluster' => $out]);
    }

    // ------------------------------------------------------------------
    // GET /api/winloss/by_bd
    // ------------------------------------------------------------------
    public function by_bd() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // Wins by BD
        $win_rows = $this->db->query(
            "SELECT ic.mainbd, u.name AS bd_name, COUNT(*) AS wins
             FROM init_call ic
             LEFT JOIN user u ON u.uid = ic.mainbd
             WHERE ic.cstatus IN (7, 14)
             GROUP BY ic.mainbd, u.name
             ORDER BY wins DESC
             LIMIT 50"
        )->result_array();

        // Losses by BD
        $loss_rows = $this->db->query(
            "SELECT ic.mainbd, u.name AS bd_name, COUNT(ll.id) AS losses
             FROM lead_loss ll
             INNER JOIN init_call ic ON ic.id = ll.lead_id
             LEFT JOIN user u ON u.uid = ic.mainbd
             GROUP BY ic.mainbd, u.name
             ORDER BY losses DESC
             LIMIT 50"
        )->result_array();

        $map = [];
        foreach ($win_rows as $r) {
            $k = (int)$r['mainbd'];
            if (!isset($map[$k])) {
                $map[$k] = [
                    'mainbd'  => $k,
                    'bd_name' => $r['bd_name'] ?? 'Unknown BD',
                    'wins'    => 0,
                    'losses'  => 0,
                ];
            }
            $map[$k]['wins'] = (int)$r['wins'];
        }
        foreach ($loss_rows as $r) {
            $k = (int)$r['mainbd'];
            if (!isset($map[$k])) {
                $map[$k] = [
                    'mainbd'  => $k,
                    'bd_name' => $r['bd_name'] ?? 'Unknown BD',
                    'wins'    => 0,
                    'losses'  => 0,
                ];
            }
            $map[$k]['losses'] = (int)$r['losses'];
        }

        $out = [];
        foreach ($map as $item) {
            $decided = $item['wins'] + $item['losses'];
            $item['win_rate_percent'] = ($decided > 0)
                ? round(($item['wins'] / $decided) * 100, 2)
                : 0;
            $out[] = $item;
        }

        usort($out, function($a, $b) { return $b['wins'] - $a['wins']; });

        if (empty($out)) {
            $this->_json([
                'ok'    => true,
                'empty' => true,
                'by_bd' => [],
            ]);
        }

        $this->_json(['ok' => true, 'count' => count($out), 'by_bd' => $out]);
    }
}
