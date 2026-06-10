<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CompanyGeoTag controller — meeting-time GPS capture.
 *
 * Endpoints:
 *   GET  /api/company_geotag/probe
 *   POST /api/company_geotag/log
 *     body: company_id, lat, lng, accuracy_m, is_mock(0/1), source(meeting_start|meeting_end|mom_submit|manual_pin),
 *           ref_table, ref_id, notes
 *     writes company_geo_tag + geofence_gate_log, then calls sp_company_anchor_recompute(company_id)
 *
 *   GET /api/company_geotag/anchor?company_id=N
 *     returns the learned anchor (lat,lng,radius,confidence,capture_count,source)
 *
 *   GET /api/company_geotag/audit?bd_uid=N&from=YYYY-MM-DD&to=YYYY-MM-DD
 *     for the advance-settlement screen. Returns per-visit audit rows from v_advance_settlement_gps_audit.
 *
 * Auth: Bearer STEM_DIGEST_TOKEN (matches existing /api/route_brain pattern).
 */
class CompanyGeoTag extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('Geofence_helper');
        $this->_check_auth();
    }

    private function _check_auth() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization', TRUE);
        $expected = 'Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if ($hdr !== $expected) {
            http_response_code(401);
            echo json_encode(['error' => 'unauthorized']);
            exit;
        }
    }

    public function probe() {
        $row = $this->db->query("SELECT COUNT(*) AS c FROM company_geo_tag")->row();
        $cm = $this->db->query("SELECT COUNT(*) AS c FROM company_master WHERE anchor_source='learned_from_meetings'")->row();
        echo json_encode([
            'ok' => true,
            'company_geo_tag_rows' => (int)$row->c,
            'companies_with_learned_anchor' => (int)$cm->c,
            'time' => date('c'),
        ]);
    }

    public function log() {
        $in = json_decode(file_get_contents('php://input'), true);
        if (!is_array($in)) { $in = $_POST; }

        $company_id  = (int)($in['company_id'] ?? 0);
        $user_id     = (int)($in['user_id']    ?? 0);
        $lat         = isset($in['lat']) && $in['lat'] !== '' ? (float)$in['lat'] : null;
        $lng         = isset($in['lng']) && $in['lng'] !== '' ? (float)$in['lng'] : null;
        $accuracy_m  = isset($in['accuracy_m']) && $in['accuracy_m'] !== '' ? (float)$in['accuracy_m'] : null;
        $is_mock     = !empty($in['is_mock']) ? 1 : 0;
        $source      = $in['source']    ?? 'meeting_end';
        $ref_table   = $in['ref_table'] ?? null;
        $ref_id      = isset($in['ref_id']) ? (int)$in['ref_id'] : null;
        $notes       = $in['notes']     ?? null;

        if (!$company_id || !$user_id) {
            http_response_code(400);
            echo json_encode(['error' => 'company_id and user_id required']);
            return;
        }
        $allowed = ['meeting_start','meeting_end','mom_submit','manual_pin'];
        if (!in_array($source, $allowed, true)) {
            http_response_code(400);
            echo json_encode(['error' => 'invalid source']);
            return;
        }

        // Outlier check: if a learned anchor already exists and this point is >2km away,
        // flag it (but still record).
        $outlier = 0;
        $distance_from_prev = null;
        $existing = $this->db->query("SELECT anchor_lat, anchor_lng FROM company_master WHERE id=? LIMIT 1", [$company_id])->row();
        if ($existing && $existing->anchor_lat !== null && $lat !== null && $lng !== null) {
            $distance_from_prev = $this->geofence_helper->haversine_m(
                (float)$existing->anchor_lat, (float)$existing->anchor_lng, $lat, $lng
            );
            if ($distance_from_prev > 2000.0) {
                $outlier = 1;
            }
        }

        // Insert geo_tag
        $this->db->insert('company_geo_tag', [
            'company_id'    => $company_id,
            'user_id'       => $user_id,
            'lat'           => $lat,
            'lng'           => $lng,
            'accuracy_m'    => $accuracy_m,
            'is_mock'       => $is_mock,
            'source'        => $source,
            'ref_table'     => $ref_table,
            'ref_id'        => $ref_id,
            'captured_at'   => date('Y-m-d H:i:s'),
            'distance_from_prev_median_m' => $distance_from_prev,
            'outlier_flag'  => $outlier,
            'notes'         => $notes,
        ]);
        $tag_id = $this->db->insert_id();

        // Mirror to geofence_gate_log for unified telemetry
        $gate_status = 'pass';
        if ($is_mock)                                    $gate_status = 'mocked_time';
        elseif ($lat === null || $lng === null)          $gate_status = 'missing';
        elseif ($accuracy_m !== null && $accuracy_m > 100) $gate_status = 'low_accuracy';
        elseif ($outlier)                                $gate_status = 'out_of_range';

        $this->db->insert('geofence_gate_log', [
            'created_at'         => date('Y-m-d H:i:s'),
            'user_id'            => $user_id,
            'surface'            => $source,
            'ref_table'          => 'company_geo_tag',
            'ref_id'             => $tag_id,
            'lat'                => $lat,
            'lng'                => $lng,
            'accuracy_m'         => $accuracy_m,
            'anchor_label'       => 'company',
            'anchor_lat'         => $existing ? $existing->anchor_lat : null,
            'anchor_lng'         => $existing ? $existing->anchor_lng : null,
            'anchor_radius_km'   => null,
            'distance_m'         => $distance_from_prev,
            'gate_status'        => $gate_status,
            'is_mock'            => $is_mock,
            'notes'              => $outlier ? 'flagged outlier vs learned anchor' : null,
        ]);

        // Recompute the learned anchor (only if this row is clean)
        if (!$is_mock && !$outlier && $lat !== null && $lng !== null) {
            $this->db->query('CALL sp_company_anchor_recompute(?)', [$company_id]);
        }

        // Pull the updated anchor to return to caller
        $cm = $this->db->query(
            "SELECT anchor_lat, anchor_lng, anchor_capture_count, anchor_confidence, anchor_source FROM company_master WHERE id=?",
            [$company_id]
        )->row();

        echo json_encode([
            'ok'         => true,
            'tag_id'     => (int)$tag_id,
            'gate_status'=> $gate_status,
            'outlier'    => (bool)$outlier,
            'anchor'     => $cm ? [
                'lat'          => $cm->anchor_lat,
                'lng'          => $cm->anchor_lng,
                'capture_count'=> (int)$cm->anchor_capture_count,
                'confidence'   => (int)$cm->anchor_confidence,
                'source'       => $cm->anchor_source,
            ] : null,
        ]);
    }

    public function anchor() {
        $cid = (int)$this->input->get('company_id');
        if (!$cid) { http_response_code(400); echo json_encode(['error'=>'company_id required']); return; }
        $row = $this->db->query("
            SELECT id, compname, anchor_lat, anchor_lng, anchor_radius_m,
                   anchor_capture_count, anchor_confidence, anchor_last_updated, anchor_source
            FROM company_master WHERE id=? LIMIT 1
        ", [$cid])->row();
        if (!$row) { http_response_code(404); echo json_encode(['error'=>'company not found']); return; }
        echo json_encode([
            'company_id'    => (int)$row->id,
            'company_name'  => $row->compname,
            'anchor_lat'    => $row->anchor_lat,
            'anchor_lng'    => $row->anchor_lng,
            'radius_m'      => (int)$row->anchor_radius_m,
            'capture_count' => (int)$row->anchor_capture_count,
            'confidence'    => (int)$row->anchor_confidence,
            'last_updated'  => $row->anchor_last_updated,
            'source'        => $row->anchor_source,
        ]);
    }

    public function audit() {
        $bd   = (int)$this->input->get('bd_uid');
        $from = $this->input->get('from');
        $to   = $this->input->get('to');
        $where = "1=1";
        $params = [];
        if ($bd)   { $where .= " AND bd_uid=?";        $params[] = $bd; }
        if ($from) { $where .= " AND captured_at>=?";  $params[] = $from . ' 00:00:00'; }
        if ($to)   { $where .= " AND captured_at<=?";  $params[] = $to   . ' 23:59:59'; }
        $rows = $this->db->query("SELECT * FROM v_advance_settlement_gps_audit WHERE $where ORDER BY captured_at DESC LIMIT 500", $params)->result_array();

        // Summary block for the accounts officer
        $summary = ['total'=>0,'verified'=>0,'mocked'=>0,'missing'=>0,'low_accuracy'=>0,'company_unanchored'=>0];
        foreach ($rows as $r) {
            $summary['total']++;
            if (isset($summary[$r['audit_status']])) $summary[$r['audit_status']]++;
        }
        echo json_encode(['summary'=>$summary, 'rows'=>$rows]);
    }
}
