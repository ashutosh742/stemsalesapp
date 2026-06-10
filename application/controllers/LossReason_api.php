<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LossReason_api - Phase 1 Agent B, C6 Reason-for-Loss
 * Created: 2026-06-08 (additive only)
 *
 * Endpoints:
 *   GET  /api/loss/reasons             - master list of loss reason codes
 *   POST /api/loss/capture             - record a loss reason for a lead
 *   GET  /api/loss/report              - counts by reason (F6 win/loss analytics foundation)
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Tables: loss_reason_master (seeded), lead_loss
 * ADDITIVE: capture does NOT modify init_call write path
 * Rules: ASCII only, empty -> {ok:true, empty:true}
 */
class LossReason_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // Auth helper
    // -------------------------------------------------------------------------
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $cfg_file = APPPATH . 'config/digest_token.txt';
        if (file_exists($cfg_file)) {
            $t = trim(file_get_contents($cfg_file));
            if ($t) { $expected = $t; }
        }

        $header = '';
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }

        $provided = trim(str_replace(['Bearer ', 'Bearer'], '', $header));
        if (!$provided || $provided !== $expected) {
            $this->output->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200) {
        $this->output->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function _input_json() {
        $raw = file_get_contents('php://input');
        if ($raw && $raw[0] === '{') { return json_decode($raw, true) ?: []; }
        return $_POST ?: [];
    }

    // -------------------------------------------------------------------------
    // GET /api/loss/reasons  - master reason list
    // -------------------------------------------------------------------------
    public function reasons() {
        if (!$this->_bearer()) return;

        $rows = $this->db->query(
            "SELECT id, code, label FROM loss_reason_master WHERE active = 1 ORDER BY id ASC"
        )->result_array();

        foreach ($rows as &$r) { $r['id'] = (int)$r['id']; }
        unset($r);

        $this->_json([
            'ok'           => true,
            'empty'        => empty($rows),
            'reasons'      => $rows,
            'count'        => count($rows),
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/loss/capture
    // Required: lead_id, reason_id, by_uid
    // Optional: note
    // ADDITIVE: does not touch init_call; only inserts into lead_loss
    // -------------------------------------------------------------------------
    public function capture() {
        if (!$this->_bearer()) return;

        $in        = $this->_input_json();
        $lead_id   = isset($in['lead_id'])   ? (int)$in['lead_id']   : 0;
        $reason_id = isset($in['reason_id']) ? (int)$in['reason_id'] : 0;
        $by_uid    = isset($in['by_uid'])    ? (int)$in['by_uid']    : 0;
        $note      = isset($in['note'])      ? trim($in['note'])      : null;

        if (!$lead_id || !$reason_id || !$by_uid) {
            $this->_json(['ok' => false, 'error' => 'lead_id, reason_id, by_uid are required'], 422);
            return;
        }

        // Validate lead exists in init_call (read only - no write)
        $lead = $this->db->query("SELECT id FROM init_call WHERE id = ? LIMIT 1", [$lead_id])->row_array();
        if (!$lead) {
            $this->_json(['ok' => false, 'error' => 'lead_id not found in init_call'], 422);
            return;
        }

        // Validate reason exists
        $reason = $this->db->query(
            "SELECT id, code, label FROM loss_reason_master WHERE id = ? AND active = 1",
            [$reason_id]
        )->row_array();
        if (!$reason) {
            $this->_json(['ok' => false, 'error' => 'reason_id not found or inactive'], 422);
            return;
        }

        $this->db->query(
            "INSERT INTO lead_loss (lead_id, reason_id, note, by_uid, ts) VALUES (?, ?, ?, ?, NOW())",
            [$lead_id, $reason_id, $note, $by_uid]
        );
        $new_id = $this->db->insert_id();

        $this->_json([
            'ok'          => true,
            'action'      => 'loss_captured',
            'id'          => (int)$new_id,
            'lead_id'     => $lead_id,
            'reason_code' => $reason['code'],
            'reason_label'=> $reason['label'],
            'generated_at'=> date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/loss/report
    // Optional filters: from_ts, to_ts (YYYY-MM-DD)
    // Returns counts grouped by reason - foundation for F6 win/loss analytics
    // -------------------------------------------------------------------------
    public function report() {
        if (!$this->_bearer()) return;

        $from_ts = trim((string)$this->input->get('from_ts'));
        $to_ts   = trim((string)$this->input->get('to_ts'));

        $where  = ['1=1'];
        $params = [];

        if ($from_ts) {
            $where[]  = 'll.ts >= ?';
            $params[] = $from_ts . ' 00:00:00';
        }
        if ($to_ts) {
            $where[]  = 'll.ts <= ?';
            $params[] = $to_ts . ' 23:59:59';
        }

        $where_sql = implode(' AND ', $where);

        $rows = $this->db->query(
            "SELECT lrm.id AS reason_id, lrm.code, lrm.label,
                    COUNT(ll.id) AS loss_count
             FROM loss_reason_master lrm
             LEFT JOIN lead_loss ll ON ll.reason_id = lrm.id AND {$where_sql}
             WHERE lrm.active = 1
             GROUP BY lrm.id, lrm.code, lrm.label
             ORDER BY loss_count DESC, lrm.id ASC",
            $params
        )->result_array();

        $total = 0;
        foreach ($rows as &$r) {
            $r['reason_id']  = (int)$r['reason_id'];
            $r['loss_count'] = (int)$r['loss_count'];
            $total += $r['loss_count'];
        }
        unset($r);

        // Add percent share per reason (spelled out as "percent")
        foreach ($rows as &$r) {
            $r['share_percent'] = $total > 0 ? round($r['loss_count'] / $total * 100, 1) : 0;
        }
        unset($r);

        $this->_json([
            'ok'           => true,
            'empty'        => ($total === 0),
            'report'       => $rows,
            'total_losses' => $total,
            'filters'      => ['from_ts' => $from_ts ?: null, 'to_ts' => $to_ts ?: null],
            'note'         => 'Foundation data for F6 win/loss analytics. share_percent is proportion of total losses.',
            'generated_at' => date('c'),
        ]);
    }
}
