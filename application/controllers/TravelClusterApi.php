<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * TravelClusterApi - WS-B Travel-Cluster Hub, 2026-06-07
 * File: application/controllers/TravelClusterApi.php
 *
 * Routes (added to routes.php):
 *   GET  travelcluster/clusters_for_user  -> TravelClusterApi/clusters_for_user
 *   GET  travelcluster/prospectable       -> TravelClusterApi/prospectable
 *   POST travelcluster/create             -> TravelClusterApi/create
 *   GET  travelcluster/list               -> TravelClusterApi/list
 *   GET  travelcluster/apollo_status      -> TravelClusterApi/apollo_status
 *   GET  travelcluster/linkedin_status    -> TravelClusterApi/linkedin_status
 *
 * All endpoints require bearer token (application/config/digest_token.txt).
 * PHP output: ASCII only - no em/en dashes, no rupee symbol, sanitize DB strings.
 * Empty results: {"ok":true,"empty":true, correct shape}.
 * apr_status in DB: tinyint 0=pending, 1=Approved
 */
class TravelClusterApi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // -------------------------------------------------------------------------
    // Bearer guard - reads token from application/config/digest_token.txt
    // -------------------------------------------------------------------------
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        $tok = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected || !hash_equals($expected, $tok)) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'bad_token']));
            return false;
        }
        return true;
    }

    private function _json($payload) {
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    // Sanitize a string to ASCII-safe output - remove em/en dashes, fancy chars
    private function _ascii($s) {
        if ($s === null) return '';
        // Replace em dash, en dash with hyphen
        $s = str_replace(["\xe2\x80\x94", "\xe2\x80\x93", chr(0x97), chr(0x96)], '-', $s);
        // Strip remaining non-ASCII
        $s = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $s);
        return trim($s);
    }

    // Map apr_status tinyint to label string
    private function _apr_label($val) {
        $v = (int)$val;
        if ($v === 1) return 'Approved';
        return 'pending';
    }

    // =========================================================================
    // 1. GET travelcluster/clusters_for_user?user_id=
    //    List clusters the user is mapped to (user_cluster_mapping JOIN cluster_master)
    //    with companies_in_cluster count from cluster_company_index.
    // =========================================================================
    public function clusters_for_user() {
        if (!$this->_bearer()) return;

        $user_id = (int)$this->input->get('user_id');
        if ($user_id <= 0) {
            return $this->_json([
                'ok'           => false,
                'error'        => 'user_id is required and must be a positive integer',
                'rows'         => [],
                'count'        => 0,
                'generated_at' => date('c'),
                'route'        => 'travelcluster/clusters_for_user',
            ]);
        }

        $sql = "SELECT
                    ucm.cluster_id,
                    cm.cluster_name,
                    cm.region,
                    cm.is_pilot,
                    cm.is_active,
                    ucm.user_type,
                    ucm.status AS mapping_status,
                    COALESCE(cci_cnt.companies_in_cluster, 0) AS companies_in_cluster
                FROM user_cluster_mapping ucm
                JOIN cluster_master cm ON cm.cluster_id = ucm.cluster_id
                LEFT JOIN (
                    SELECT cluster_id, COUNT(*) AS companies_in_cluster
                    FROM cluster_company_index
                    GROUP BY cluster_id
                ) cci_cnt ON cci_cnt.cluster_id = ucm.cluster_id
                WHERE ucm.user_id = ?
                ORDER BY cm.region, cm.cluster_name";

        $rows_raw = $this->db->query($sql, [$user_id])->result();

        if (empty($rows_raw)) {
            return $this->_json([
                'ok'           => true,
                'empty'        => true,
                'rows'         => [],
                'count'        => 0,
                'user_id'      => $user_id,
                'generated_at' => date('c'),
                'route'        => 'travelcluster/clusters_for_user',
            ]);
        }

        $rows = [];
        foreach ($rows_raw as $r) {
            $rows[] = [
                'cluster_id'           => (int)$r->cluster_id,
                'cluster_name'         => $this->_ascii($r->cluster_name),
                'region'               => $this->_ascii($r->region),
                'is_pilot'             => (int)$r->is_pilot,
                'is_active'            => (int)$r->is_active,
                'user_type'            => (int)$r->user_type,
                'mapping_status'       => (int)$r->mapping_status,
                'companies_in_cluster' => (int)$r->companies_in_cluster,
            ];
        }

        return $this->_json([
            'ok'           => true,
            'empty'        => false,
            'rows'         => $rows,
            'count'        => count($rows),
            'user_id'      => $user_id,
            'generated_at' => date('c'),
            'route'        => 'travelcluster/clusters_for_user',
        ]);
    }

    // =========================================================================
    // 2. GET travelcluster/prospectable?cluster_id=&user_id=
    //    From cluster_company_index: totals + breakdown by business_potential,
    //    focus funnel, key company, CSR window, geo coverage.
    //    Top 25 sample (key company first, then csr_window_open).
    // =========================================================================
    public function prospectable() {
        if (!$this->_bearer()) return;

        $cluster_id = (int)$this->input->get('cluster_id');
        $user_id    = (int)$this->input->get('user_id');

        if ($cluster_id <= 0) {
            return $this->_json([
                'ok'           => false,
                'error'        => 'cluster_id is required and must be a positive integer',
                'route'        => 'travelcluster/prospectable',
                'generated_at' => date('c'),
            ]);
        }

        // Verify cluster exists
        $cluster_row = $this->db->query(
            "SELECT cluster_id, cluster_name, region FROM cluster_master WHERE cluster_id = ? LIMIT 1",
            [$cluster_id]
        )->row();

        if (!$cluster_row) {
            return $this->_json([
                'ok'           => true,
                'empty'        => true,
                'cluster_id'   => $cluster_id,
                'totals'       => [
                    'total_companies'      => 0,
                    'by_business_potential'=> ['High' => 0, 'Medium' => 0, 'Low' => 0, 'Unknown' => 0],
                    'is_focus_funnel'      => 0,
                    'is_key_company'       => 0,
                    'csr_window_open'      => 0,
                    'with_geo'             => 0,
                ],
                'sample'       => [],
                'route'        => 'travelcluster/prospectable',
                'generated_at' => date('c'),
            ]);
        }

        // Aggregate counts
        $agg_sql = "SELECT
                        COUNT(*) AS total_companies,
                        SUM(CASE WHEN cci.business_potential = 'High'    THEN 1 ELSE 0 END) AS cnt_high,
                        SUM(CASE WHEN cci.business_potential = 'Medium'  THEN 1 ELSE 0 END) AS cnt_medium,
                        SUM(CASE WHEN cci.business_potential = 'Low'     THEN 1 ELSE 0 END) AS cnt_low,
                        SUM(CASE WHEN cci.business_potential = 'Unknown' THEN 1 ELSE 0 END) AS cnt_unknown,
                        SUM(cci.is_focus_funnel)  AS is_focus_funnel,
                        SUM(cci.is_key_company)   AS is_key_company,
                        SUM(cci.csr_window_open)  AS csr_window_open,
                        SUM(CASE WHEN cci.lat IS NOT NULL AND cci.lng IS NOT NULL THEN 1 ELSE 0 END) AS with_geo
                    FROM cluster_company_index cci
                    WHERE cci.cluster_id = ?";
        $agg = $this->db->query($agg_sql, [$cluster_id])->row();

        $total = $agg ? (int)$agg->total_companies : 0;

        if ($total === 0) {
            return $this->_json([
                'ok'           => true,
                'empty'        => true,
                'cluster_id'   => $cluster_id,
                'cluster_name' => $this->_ascii($cluster_row->cluster_name),
                'region'       => $this->_ascii($cluster_row->region),
                'totals'       => [
                    'total_companies'       => 0,
                    'by_business_potential' => ['High' => 0, 'Medium' => 0, 'Low' => 0, 'Unknown' => 0],
                    'is_focus_funnel'       => 0,
                    'is_key_company'        => 0,
                    'csr_window_open'       => 0,
                    'with_geo'              => 0,
                ],
                'sample'       => [],
                'route'        => 'travelcluster/prospectable',
                'generated_at' => date('c'),
            ]);
        }

        // Top 25 sample: key company first, then csr_window_open, then id
        $sample_sql = "SELECT
                           cci.company_id,
                           cm.compname,
                           cci.business_potential,
                           cm.city,
                           cm.district,
                           cm.state,
                           cci.lat,
                           cci.lng,
                           cci.is_focus_funnel,
                           cci.is_key_company,
                           cci.csr_window_open,
                           cci.partner_type_id,
                           cci.contact_count,
                           cci.verified_dm_count
                       FROM cluster_company_index cci
                       LEFT JOIN company_master cm ON cm.id = cci.company_id
                       WHERE cci.cluster_id = ?
                       ORDER BY cci.is_key_company DESC, cci.csr_window_open DESC, cci.id ASC
                       LIMIT 25";
        $sample_raw = $this->db->query($sample_sql, [$cluster_id])->result();

        $sample = [];
        foreach ($sample_raw as $s) {
            $sample[] = [
                'company_id'        => (int)$s->company_id,
                'compname'          => $this->_ascii($s->compname),
                'business_potential'=> $this->_ascii($s->business_potential),
                'city'              => $this->_ascii($s->city),
                'district'          => $this->_ascii($s->district),
                'state'             => $this->_ascii($s->state),
                'lat'               => $s->lat !== null ? (float)$s->lat : null,
                'lng'               => $s->lng !== null ? (float)$s->lng : null,
                'is_focus_funnel'   => (int)$s->is_focus_funnel,
                'is_key_company'    => (int)$s->is_key_company,
                'csr_window_open'   => (int)$s->csr_window_open,
                'partner_type_id'   => $s->partner_type_id !== null ? (int)$s->partner_type_id : null,
                'contact_count'     => (int)$s->contact_count,
                'verified_dm_count' => (int)$s->verified_dm_count,
            ];
        }

        return $this->_json([
            'ok'           => true,
            'empty'        => false,
            'cluster_id'   => $cluster_id,
            'cluster_name' => $this->_ascii($cluster_row->cluster_name),
            'region'       => $this->_ascii($cluster_row->region),
            'user_id'      => $user_id > 0 ? $user_id : null,
            'totals'       => [
                'total_companies'       => $total,
                'by_business_potential' => [
                    'High'    => (int)$agg->cnt_high,
                    'Medium'  => (int)$agg->cnt_medium,
                    'Low'     => (int)$agg->cnt_low,
                    'Unknown' => (int)$agg->cnt_unknown,
                ],
                'is_focus_funnel'  => (int)$agg->is_focus_funnel,
                'is_key_company'   => (int)$agg->is_key_company,
                'csr_window_open'  => (int)$agg->csr_window_open,
                'with_geo'         => (int)$agg->with_geo,
            ],
            'sample'       => $sample,
            'sample_count' => count($sample),
            'generated_at' => date('c'),
            'route'        => 'travelcluster/prospectable',
        ]);
    }

    // =========================================================================
    // 3. POST travelcluster/create
    //    BD creates a travel plan linked to a cluster + categories + status.
    //    Inserts into travel_cluster_edit_request.
    //    Body (JSON or form-encoded):
    //      user_id, cluster_id (-> clustername), in_state, in_district, in_city,
    //      travelType, remarks, business_potential (array or CSV), partner_type_id (int),
    //      planned_status (string label, stored in remarks extension)
    //    apr_status defaults to 0 (pending).
    //    travelId: uses cluster_id as the travel reference (consistent with existing rows).
    // =========================================================================
    public function create() {
        if (!$this->_bearer()) return;

        // Accept JSON body or form-encoded
        $raw = $this->input->raw_input_stream;
        $body = [];
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $body = $decoded;
            }
        }

        $p = function($key, $default = null) use ($body) {
            if (isset($body[$key])) return $body[$key];
            $v = $this->input->post($key);
            return $v !== null ? $v : $default;
        };

        $user_id    = (int)$p('user_id', 0);
        $cluster_id = (int)$p('cluster_id', 0);
        $in_state   = trim((string)$p('in_state', ''));
        $in_district= trim((string)$p('in_district', ''));
        $in_city    = trim((string)$p('in_city', ''));
        $travelType = trim((string)$p('travelType', 'base'));
        $remarks    = trim((string)$p('remarks', ''));
        $planned_status = trim((string)$p('planned_status', 'pending'));

        // Category filters (optional, stored as metadata in remarks extension)
        $bp_filter = $p('business_potential', null);
        if (is_array($bp_filter)) {
            $bp_filter = implode(',', array_map('trim', $bp_filter));
        } else {
            $bp_filter = trim((string)($bp_filter ?? ''));
        }
        $partner_type_id = $p('partner_type_id', null);
        if ($partner_type_id !== null) {
            $partner_type_id = (int)$partner_type_id;
        }

        // Validate required fields
        $errors = [];
        if ($user_id <= 0)    $errors[] = 'user_id is required and must be a positive integer';
        if ($cluster_id <= 0) $errors[] = 'cluster_id is required and must be a positive integer';
        if ($in_state === '')  $errors[] = 'in_state is required';

        if (!empty($errors)) {
            return $this->_json([
                'ok'           => false,
                'error'        => implode('; ', $errors),
                'route'        => 'travelcluster/create',
                'generated_at' => date('c'),
            ]);
        }

        // Validate travelType
        $allowed_types = ['base', 'outstation', 'local'];
        if (!in_array(strtolower($travelType), $allowed_types)) {
            $travelType = 'base';
        }

        // Look up cluster name
        $cluster_row = $this->db->query(
            "SELECT cluster_id, cluster_name FROM cluster_master WHERE cluster_id = ? LIMIT 1",
            [$cluster_id]
        )->row();

        if (!$cluster_row) {
            return $this->_json([
                'ok'           => false,
                'error'        => 'cluster_id ' . $cluster_id . ' not found in cluster_master',
                'route'        => 'travelcluster/create',
                'generated_at' => date('c'),
            ]);
        }

        $clustername = $this->_ascii($cluster_row->cluster_name);

        // Build extended remarks including category filter and planned status
        $remarks_parts = [];
        if ($remarks !== '') $remarks_parts[] = $remarks;
        if ($bp_filter !== '') $remarks_parts[] = 'bp_filter:' . $this->_ascii($bp_filter);
        if ($partner_type_id !== null) $remarks_parts[] = 'partner_type_id:' . $partner_type_id;
        if ($planned_status !== '') $remarks_parts[] = 'planned_status:' . $this->_ascii($planned_status);
        $full_remarks = implode(' | ', $remarks_parts);

        // Insert
        $now = date('Y-m-d H:i:s');
        $insert_sql = "INSERT INTO travel_cluster_edit_request
                       (user_id, travelId, clustername, in_state, in_district, in_city, travelType, remarks, apr_status, created_at, updated_at)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";

        $this->db->query($insert_sql, [
            $user_id,
            $cluster_id,           // travelId = cluster_id (consistent with existing data)
            $clustername,
            $this->_ascii($in_state),
            $this->_ascii($in_district),
            $this->_ascii($in_city),
            $travelType,
            $full_remarks,
            $now,
            $now,
        ]);

        $new_id = $this->db->insert_id();

        if (!$new_id) {
            return $this->_json([
                'ok'           => false,
                'error'        => 'Insert failed',
                'route'        => 'travelcluster/create',
                'generated_at' => date('c'),
            ]);
        }

        // SELECT back to confirm
        $created = $this->db->query(
            "SELECT * FROM travel_cluster_edit_request WHERE id = ? LIMIT 1",
            [$new_id]
        )->row();

        return $this->_json([
            'ok'           => true,
            'id'           => $new_id,
            'user_id'      => $user_id,
            'cluster_id'   => $cluster_id,
            'cluster_name' => $clustername,
            'in_state'     => $this->_ascii($created->in_state ?? $in_state),
            'in_district'  => $this->_ascii($created->in_district ?? $in_district),
            'in_city'      => $this->_ascii($created->in_city ?? $in_city),
            'travelType'   => $this->_ascii($created->travelType ?? $travelType),
            'remarks'      => $this->_ascii($created->remarks ?? $full_remarks),
            'apr_status'   => 'pending',
            'category_filter' => [
                'business_potential' => $bp_filter !== '' ? explode(',', $bp_filter) : [],
                'partner_type_id'    => $partner_type_id,
            ],
            'planned_status' => $planned_status,
            'created_at'   => $this->_ascii($created->created_at ?? $now),
            'generated_at' => date('c'),
            'route'        => 'travelcluster/create',
        ]);
    }

    // =========================================================================
    // 4. GET travelcluster/list?user_id=
    //    List user's travel-cluster plans from travel_cluster_edit_request
    //    with apr_status, cluster, district, created_at, company-category summary.
    // =========================================================================
    public function list() {
        if (!$this->_bearer()) return;

        $user_id = (int)$this->input->get('user_id');
        if ($user_id <= 0) {
            return $this->_json([
                'ok'           => false,
                'error'        => 'user_id is required and must be a positive integer',
                'rows'         => [],
                'count'        => 0,
                'route'        => 'travelcluster/list',
                'generated_at' => date('c'),
            ]);
        }

        $sql = "SELECT
                    t.id,
                    t.user_id,
                    t.travelId,
                    t.clustername,
                    t.in_state,
                    t.in_district,
                    t.in_city,
                    t.travelType,
                    t.remarks,
                    t.apr_status,
                    t.apr_by,
                    t.apr_date,
                    t.apr_remarks,
                    t.created_at,
                    t.updated_at,
                    COALESCE(cci_agg.total_companies, 0)   AS companies_in_cluster,
                    COALESCE(cci_agg.cnt_high, 0)          AS bp_high,
                    COALESCE(cci_agg.cnt_medium, 0)        AS bp_medium,
                    COALESCE(cci_agg.cnt_low, 0)           AS bp_low,
                    COALESCE(cci_agg.cnt_unknown, 0)       AS bp_unknown,
                    COALESCE(cci_agg.is_key_company, 0)    AS key_companies,
                    COALESCE(cci_agg.csr_window_open, 0)   AS csr_open
                FROM travel_cluster_edit_request t
                LEFT JOIN (
                    SELECT
                        cluster_id,
                        COUNT(*) AS total_companies,
                        SUM(CASE WHEN business_potential = 'High'    THEN 1 ELSE 0 END) AS cnt_high,
                        SUM(CASE WHEN business_potential = 'Medium'  THEN 1 ELSE 0 END) AS cnt_medium,
                        SUM(CASE WHEN business_potential = 'Low'     THEN 1 ELSE 0 END) AS cnt_low,
                        SUM(CASE WHEN business_potential = 'Unknown' THEN 1 ELSE 0 END) AS cnt_unknown,
                        SUM(is_key_company)  AS is_key_company,
                        SUM(csr_window_open) AS csr_window_open
                    FROM cluster_company_index
                    GROUP BY cluster_id
                ) cci_agg ON cci_agg.cluster_id = t.travelId
                WHERE t.user_id = ?
                ORDER BY t.created_at DESC";

        $rows_raw = $this->db->query($sql, [$user_id])->result();

        if (empty($rows_raw)) {
            return $this->_json([
                'ok'           => true,
                'empty'        => true,
                'rows'         => [],
                'count'        => 0,
                'user_id'      => $user_id,
                'route'        => 'travelcluster/list',
                'generated_at' => date('c'),
            ]);
        }

        $rows = [];
        foreach ($rows_raw as $r) {
            $rows[] = [
                'id'           => (int)$r->id,
                'user_id'      => (int)$r->user_id,
                'travelId'     => (int)$r->travelId,
                'clustername'  => $this->_ascii($r->clustername),
                'in_state'     => $this->_ascii($r->in_state),
                'in_district'  => $this->_ascii($r->in_district),
                'in_city'      => $this->_ascii($r->in_city),
                'travelType'   => $this->_ascii($r->travelType),
                'remarks'      => $this->_ascii($r->remarks),
                'apr_status'   => $this->_apr_label($r->apr_status),
                'apr_status_int' => (int)$r->apr_status,
                'apr_by'       => $r->apr_by !== null ? (int)$r->apr_by : null,
                'apr_date'     => $r->apr_date,
                'apr_remarks'  => $this->_ascii($r->apr_remarks),
                'created_at'   => $r->created_at,
                'updated_at'   => $r->updated_at,
                'company_category_summary' => [
                    'companies_in_cluster' => (int)$r->companies_in_cluster,
                    'by_business_potential' => [
                        'High'    => (int)$r->bp_high,
                        'Medium'  => (int)$r->bp_medium,
                        'Low'     => (int)$r->bp_low,
                        'Unknown' => (int)$r->bp_unknown,
                    ],
                    'key_companies'  => (int)$r->key_companies,
                    'csr_window_open'=> (int)$r->csr_open,
                ],
            ];
        }

        return $this->_json([
            'ok'           => true,
            'empty'        => false,
            'rows'         => $rows,
            'count'        => count($rows),
            'user_id'      => $user_id,
            'route'        => 'travelcluster/list',
            'generated_at' => date('c'),
        ]);
    }

    // =========================================================================
    // 5. GET travelcluster/apollo_status
    //    Read v_apollo_quota_today / apollo_daily_quota_v2: quota used/remaining.
    //    If Apollo API key absent: {ok:true, connected:false, reason:'no key', quota data from DB}.
    //    Never calls external Apollo without a key.
    // =========================================================================
    public function apollo_status() {
        if (!$this->_bearer()) return;

        // Check if Apollo API key is configured (look for a config file or env)
        $api_key_present = false;
        $key_file = APPPATH . 'config/apollo_key.txt';
        if (file_exists($key_file)) {
            $k = trim(@file_get_contents($key_file));
            $api_key_present = ($k !== '');
        }
        // Also check for environment variable
        if (!$api_key_present) {
            $env_key = getenv('APOLLO_API_KEY');
            if ($env_key && trim($env_key) !== '') {
                $api_key_present = true;
            }
        }

        // Read today's quota from DB view
        $quota_row = $this->db->query("SELECT * FROM v_apollo_quota_today LIMIT 1")->row();

        if (!$quota_row) {
            // View returned no rows - use defaults from quota table or fallback
            $fallback = $this->db->query(
                "SELECT calls_made, credits_used, daily_cap FROM apollo_daily_quota_v2 ORDER BY quota_date DESC LIMIT 1"
            )->row();

            $quota_used      = $fallback ? (int)$fallback->calls_made : 0;
            $credits_used    = $fallback ? (int)$fallback->credits_used : 0;
            $quota_limit     = $fallback ? (int)$fallback->daily_cap : 80;
            $calls_remaining = $quota_limit - $quota_used;
            $pct_used        = $quota_limit > 0 ? round(($quota_used / $quota_limit) * 100, 1) : 0.0;
            $quota_date      = date('Y-m-d');
            $quota_status    = 'no_data';
        } else {
            $quota_used      = (int)$quota_row->calls_made;
            $credits_used    = (int)$quota_row->credits_used;
            $quota_limit     = (int)$quota_row->daily_cap;
            $calls_remaining = (int)$quota_row->calls_remaining;
            $pct_used        = (float)$quota_row->pct_used;
            $quota_date      = $quota_row->quota_date;
            $quota_status    = $this->_ascii($quota_row->quota_status);
        }

        return $this->_json([
            'ok'              => true,
            'connected'       => $api_key_present,
            'reason'          => $api_key_present ? null : 'no key',
            'quota_date'      => $quota_date,
            'quota_used'      => $quota_used,
            'credits_used'    => $credits_used,
            'quota_limit'     => $quota_limit,
            'calls_remaining' => $calls_remaining,
            'pct_used'        => $pct_used,
            'quota_status'    => $quota_status,
            'note'            => $api_key_present
                ? 'Apollo key configured. Quota sourced from DB.'
                : 'Apollo API key not configured. Quota data from DB only. No external calls made.',
            'route'           => 'travelcluster/apollo_status',
            'generated_at'    => date('c'),
        ]);
    }

    // =========================================================================
    // 6. GET travelcluster/linkedin_status  [PDL-aware, WS-L 2026-06-07]
    //    Provider: People Data Labs (PDL) person/company enrichment.
    //    Reads PDL key from env STEM_PDL_KEY or CI3 config 'pdl_api_key'.
    //    No key present: returns connected:false graceful shape.
    //    ASCII only output. No external call without a key.
    // =========================================================================
    public function linkedin_status() {
        if (!$this->_bearer()) return;

        $pdl_key = $this->_pdl_key();

        if (!$pdl_key) {
            return $this->_json([
                'ok'               => true,
                'provider'         => 'people_data_labs',
                'connected'        => false,
                'reason'           => 'no key',
                'configure_hint'   => 'Set STEM_PDL_KEY',
                'placeholder'      => true,
                'capabilities'     => ['person_enrich', 'company_enrich', 'bulk_enrich'],
                'route'            => 'travelcluster/linkedin_status',
                'generated_at'     => date('c'),
            ]);
        }

        // Key is present: return connected shape (actual PDL probe can be added later)
        return $this->_json([
            'ok'               => true,
            'provider'         => 'people_data_labs',
            'connected'        => true,
            'reason'           => null,
            'placeholder'      => false,
            'capabilities'     => ['person_enrich', 'company_enrich', 'bulk_enrich'],
            'note'             => 'PDL key configured. Enrichment endpoints active.',
            'route'            => 'travelcluster/linkedin_status',
            'generated_at'     => date('c'),
        ]);
    }

    // =========================================================================
    // 7. POST travelcluster/linkedin_enrich  [WS-L 2026-06-07]
    //    Body (JSON): {company_id?, compname?, name?, domain?, title?}
    //    If company_id given: resolves compname/domain from company_master.
    //    No PDL key: returns would_request shape (contract verifiable).
    //    PDL key present: would call PDL person/enrich or company/enrich,
    //      map response -> normalised envelope.
    //    ASCII only. No external call without a key.
    // =========================================================================
    public function linkedin_enrich() {
        if (!$this->_bearer()) return;

        // Parse JSON body
        $raw  = $this->input->raw_input_stream;
        $body = [];
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $body = $decoded;
            }
        }
        $p = function($key, $default = null) use ($body) {
            return isset($body[$key]) ? $body[$key] : $default;
        };

        $company_id = $p('company_id') !== null ? (int)$p('company_id') : null;
        $compname   = trim((string)$p('compname', ''));
        $name       = trim((string)$p('name', ''));
        $domain     = trim((string)$p('domain', ''));
        $title      = trim((string)$p('title', ''));

        // Resolve company info from DB if company_id provided
        if ($company_id !== null && $company_id > 0) {
            $co = $this->db->query(
                "SELECT id, compname, city, state, district FROM company_master WHERE id = ? LIMIT 1",
                [$company_id]
            )->row();
            if ($co) {
                if ($compname === '') {
                    $compname = $this->_ascii($co->compname);
                }
            }
        }

        // Build PDL request shape
        // Person Enrichment: GET https://api.peopledatalabs.com/v5/person/enrich
        //   params: name, company, title (all optional but at least one required by PDL)
        // Company Enrichment: GET https://api.peopledatalabs.com/v5/company/enrich
        //   params: name, website
        $person_params = [];
        if ($name     !== '') $person_params['name']    = $name;
        if ($compname !== '') $person_params['company'] = $compname;
        if ($title    !== '') $person_params['title']   = $title;
        if ($domain   !== '') $person_params['website'] = $domain;

        $company_params = [];
        if ($compname !== '') $company_params['name']    = $compname;
        if ($domain   !== '') $company_params['website'] = $domain;

        // Determine primary mode: person enrich if name given, else company enrich
        $primary_mode     = ($name !== '') ? 'person' : 'company';
        $primary_endpoint = ($primary_mode === 'person')
            ? 'https://api.peopledatalabs.com/v5/person/enrich'
            : 'https://api.peopledatalabs.com/v5/company/enrich';
        $primary_params   = ($primary_mode === 'person') ? $person_params : $company_params;

        $would_request = [
            'method'   => 'GET',
            'endpoint' => $primary_endpoint,
            'params'   => $primary_params,
            'headers'  => ['X-Api-Key' => '<STEM_PDL_KEY>'],
            'mode'     => $primary_mode,
        ];

        // Check for PDL key
        $pdl_key = $this->_pdl_key();

        if (!$pdl_key) {
            return $this->_json([
                'ok'           => true,
                'provider'     => 'people_data_labs',
                'connected'    => false,
                'reason'       => 'no key',
                'would_request'=> $would_request,
                'note'         => 'Enrichment will run once PDL key is set',
                'company_id'   => $company_id,
                'compname'     => $compname !== '' ? $compname : null,
                'route'        => 'travelcluster/linkedin_enrich',
                'generated_at' => date('c'),
            ]);
        }

        // Key is present: call PDL and map response
        $pdl_response = $this->_pdl_call($primary_endpoint, $primary_params, $pdl_key);

        if (!$pdl_response['ok']) {
            return $this->_json([
                'ok'           => false,
                'provider'     => 'people_data_labs',
                'connected'    => true,
                'error'        => $pdl_response['error'],
                'route'        => 'travelcluster/linkedin_enrich',
                'generated_at' => date('c'),
            ]);
        }

        $normalized = $this->_pdl_map($pdl_response['data'], $primary_mode);

        return $this->_json([
            'ok'           => true,
            'provider'     => 'people_data_labs',
            'connected'    => true,
            'person'       => $normalized,
            'raw_meta'     => [
                'pdl_status' => $pdl_response['status'] ?? null,
                'likelihood' => $pdl_response['data']['likelihood'] ?? null,
            ],
            'company_id'   => $company_id,
            'route'        => 'travelcluster/linkedin_enrich',
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // _pdl_key() - Resolve PDL API key from env or CI3 config
    // Returns trimmed key string, or empty string if not set.
    // -------------------------------------------------------------------------
    private function _pdl_key() {
        // 1. Environment variable
        $env_key = getenv('STEM_PDL_KEY');
        if ($env_key && trim($env_key) !== '') {
            return trim($env_key);
        }
        // 2. CI3 config item
        $cfg_key = config_item('pdl_api_key');
        if ($cfg_key && trim($cfg_key) !== '') {
            return trim($cfg_key);
        }
        // 3. Flat key file (future extension)
        $key_file = APPPATH . 'config/pdl_key.txt';
        if (file_exists($key_file)) {
            $k = trim(@file_get_contents($key_file));
            if ($k !== '') return $k;
        }
        return '';
    }

    // -------------------------------------------------------------------------
    // _pdl_call() - Execute HTTP GET to PDL API using curl
    //   Only called when key is confirmed present.
    //   Returns ['ok'=>bool, 'data'=>array, 'status'=>int, 'error'=>string]
    // -------------------------------------------------------------------------
    private function _pdl_call($endpoint, $params, $api_key) {
        $url = $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'X-Api-Key: ' . $api_key,
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw    = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['ok' => false, 'error' => 'curl error: ' . $this->_ascii($err), 'data' => [], 'status' => 0];
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'error' => 'invalid JSON from PDL', 'data' => [], 'status' => $status];
        }
        if ($status !== 200) {
            $msg = isset($decoded['error']['message']) ? $decoded['error']['message'] : 'PDL error ' . $status;
            return ['ok' => false, 'error' => $this->_ascii($msg), 'data' => $decoded, 'status' => $status];
        }
        return ['ok' => true, 'data' => $decoded, 'status' => $status, 'error' => ''];
    }

    // -------------------------------------------------------------------------
    // _pdl_map() - Normalize PDL person/company response to standard envelope
    //   PDL person fields: full_name, job_title, linkedin_url, work_email,
    //     job_company_name, location_name
    //   PDL company fields: name, website, linkedin_url, location.name
    // -------------------------------------------------------------------------
    private function _pdl_map($data, $mode = 'person') {
        if ($mode === 'person') {
            return [
                'name'         => $this->_ascii($data['full_name']          ?? ''),
                'title'        => $this->_ascii($data['job_title']           ?? ''),
                'linkedin_url' => $this->_ascii($data['linkedin_url']        ?? ''),
                'email'        => $this->_ascii($data['work_email']          ?? ''),
                'company'      => $this->_ascii($data['job_company_name']    ?? ''),
                'location'     => $this->_ascii($data['location_name']       ?? ''),
            ];
        }
        // company mode
        return [
            'name'         => $this->_ascii($data['name']                ?? ''),
            'title'        => '',
            'linkedin_url' => $this->_ascii($data['linkedin_url']        ?? ''),
            'email'        => '',
            'company'      => $this->_ascii($data['name']                ?? ''),
            'location'     => $this->_ascii($data['location']['name']    ?? ''),
        ];
    }
}
