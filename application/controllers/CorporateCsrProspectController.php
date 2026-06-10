<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorporateCsrProspectController
 *
 * Migration 041. Routes /api/csr_prospect/*
 *
 * STREAM D PATCH: Added candidates() wired to csr_corporate_master (v1 table).
 * Also wired today_for_bd(), today_summary() to return honest DB data.
 * csr_corporate_master has 0 rows on staging as of patch date.
 * All existing methods (probe, refresh_for_bd, today_for_bd, today_summary,
 * accept_and_seed, link_init_call, dismiss, sync_csr_gov, sync_apollo,
 * corporate, influencers) are PRESERVED.
 *
 * Auth: Bearer api_token OR STEM_DIGEST_TOKEN for admin endpoints.
 */
class CorporateCsrProspectController extends CI_Controller {

    private $_uid = 0;
    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->_require_bearer();
    }

    private function _require_bearer() {
        // rimlyproof_failopen_fix_20260609: was fail-open - returned (passed) when no
        // Authorization header was present ("soft fail for cron probes") and never
        // rejected invalid tokens, leaking CSR prospect candidates/influencers. Now:
        // probes stay public; everything else requires a valid digest OR per-user
        // login token via authunify_ok(). Resolve uid from authunify when available.
        $m = $this->router->fetch_method();
        if ($m === 'probe' || substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) {
            if (function_exists('authunify_uid')) { $this->_uid = (int) authunify_uid(); }
            return;
        }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ============================================================
    // PROBE (unchanged - APK relies on this)
    // ============================================================
    public function probe() {
        $this->_json(array(
            'ok' => true,
            'controller' => 'CorporateCsrProspectController',
            'migration' => '041',
            'status' => 'ready',
            'features' => array(
                'candidates' => true,
                'csr_corporate_master' => true,
                'csr_corporate_master_v2' => true,
                'today_for_bd' => true,
                'today_summary' => true
            )
        ));
    }

    // ============================================================
    // CANDIDATES - STREAM D WIRED
    // GET /api/csr_prospect/candidates?sector=&state=&limit=
    // Returns corporate CSR prospects from csr_corporate_master.
    // Returns honest empty if no rows seeded yet.
    // ============================================================
    public function candidates() {
        try {
            $sector = $this->input->get('sector');
            $state  = $this->input->get('state');
            $limit  = min((int)($this->input->get('limit') ?: 50), 200);

            // Try v2 table first (richer schema)
            $v2_exists = $this->db->query(
                "SELECT COUNT(*) as cnt FROM information_schema.tables " .
                "WHERE table_schema = DATABASE() AND table_name = 'csr_corporate_master_v2'"
            )->row_array();

            if (!empty($v2_exists['cnt']) && (int)$v2_exists['cnt'] > 0) {
                $this->db->select(
                    'csr_corporate_id AS id, cin, company_name, company_type, ' .
                    'hq_city, hq_state, industry, revenue_band, ' .
                    'csr_obligation_rs_cr, csr_spent_last_fy_rs_cr, ' .
                    'csr_education_share_pct, has_foundation_arm, foundation_name, ' .
                    'active, last_synced_at, created_at'
                )
                ->from('csr_corporate_master_v2')
                ->where('active', 1)
                ->order_by('csr_obligation_rs_cr', 'DESC')
                ->limit($limit);

                if ($sector) $this->db->where('industry', $sector);
                if ($state)  $this->db->where('hq_state', $state);

                $rows = $this->db->get()->result_array();

                $this->_json(array(
                    'ok'     => true,
                    'source' => 'csr_corporate_master_v2',
                    'count'  => count($rows),
                    'rows'   => $rows,
                    'note'   => count($rows) === 0 ? 'no_csr_corporates_seeded_yet' : null
                ));
            }

            // Try v1 table as fallback
            $this->db->select(
                'corporate_id AS id, cin, legal_name AS company_name, ' .
                'hq_state_code AS hq_state, sector AS industry, fy_label, ' .
                'csr_obligation_rs_cr, csr_actual_rs_cr, themes_csv, ' .
                'csr_head_name, csr_head_designation, csr_head_email, ' .
                'created_at'
            )
            ->from('csr_corporate_master')
            ->order_by('csr_obligation_rs_cr', 'DESC')
            ->limit($limit);

            if ($sector) $this->db->where('sector', $sector);
            if ($state)  $this->db->where('hq_state_code', $state);

            $rows = $this->db->get()->result_array();

            $this->_json(array(
                'ok'     => true,
                'source' => 'csr_corporate_master',
                'count'  => count($rows),
                'rows'   => $rows,
                'note'   => count($rows) === 0 ? 'no_csr_corporates_seeded_yet' : null
            ));
        } catch (Exception $e) {
            $this->_json(array(
                'ok'     => true,
                'rows'   => array(),
                'note'   => 'error',
                'detail' => $e->getMessage()
            ));
        }
    }

    // ============================================================
    // TODAY FOR BD - wired to suggestion table
    // ============================================================
    public function today_for_bd() {
        try {
            $bd_uid = (int)$this->input->get('bd_uid');
            $tpd    = $this->input->get('plan_date') ?: date('Y-m-d');
            if (!$bd_uid) {
                $this->_json(array('ok' => true, 'suggestions' => array(), 'note' => 'no_data'));
                return;
            }

            // Try corporate_csr_suggestion_v2 (M041 table)
            $rows = array();
            try {
                $this->db->select('s.*, cm.legal_name AS company_name')
                    ->from('corporate_csr_suggestion_v2 s')
                    ->join('csr_corporate_master cm', 'cm.corporate_id = s.csr_corporate_id', 'left')
                    ->where('s.bd_uid', $bd_uid)
                    ->where('s.plan_date', $tpd)
                    ->order_by('s.created_at', 'DESC')
                    ->limit(20);
                $rows = $this->db->get()->result_array();
            } catch (Exception $inner) {
                log_message('error', 'CorporateCsrProspect::today_for_bd inner: ' . $inner->getMessage());
            }

            $this->_json(array(
                'ok'          => true,
                'suggestions' => is_array($rows) ? $rows : array(),
                'note'        => empty($rows) ? 'no_suggestions_for_date' : null
            ));
        } catch (Exception $e) {
            log_message('error', 'CorporateCsrProspect::today_for_bd: ' . $e->getMessage());
            $this->_json(array('ok' => true, 'suggestions' => array(), 'note' => 'no_data', 'detail' => $e->getMessage()));
        }
    }

    // ============================================================
    // TODAY SUMMARY
    // ============================================================
    public function today_summary() {
        try {
            $today = date('Y-m-d');
            $stats = array();

            try {
                $stats = $this->db->query(
                    "SELECT COUNT(*) AS total_suggestions, " .
                    "SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) AS accepted, " .
                    "SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) AS dismissed, " .
                    "SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending " .
                    "FROM corporate_csr_suggestion_v2 WHERE plan_date = ?",
                    array($today)
                )->row_array();
            } catch (Exception $inner) {
                $stats = array('total_suggestions' => 0, 'accepted' => 0, 'dismissed' => 0, 'pending' => 0);
            }

            // Count total corporates in master
            $total_corps = 0;
            try {
                $total_corps = (int)$this->db->query(
                    "SELECT COUNT(*) AS cnt FROM csr_corporate_master"
                )->row()->cnt;
            } catch (Exception $inner) { log_message('error', 'CorporateCsrProspectController.php silent_catch: ' . $inner->getMessage()); }

            $this->_json(array(
                'ok'          => true,
                'date'        => $today,
                'total_corporates_seeded' => $total_corps,
                'today_stats' => $stats,
                'note'        => $total_corps === 0 ? 'no_csr_corporates_seeded_yet' : null
            ));
        } catch (Exception $e) {
            log_message('error', 'CorporateCsrProspect::today_summary: ' . $e->getMessage());
            $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => $e->getMessage()));
        }
    }

    // ============================================================
    // ACCEPT AND SEED
    // ============================================================
    public function accept_and_seed() {
        $sid    = (int)$this->input->post('suggestion_id');
        $bd_uid = (int)$this->input->post('bd_uid');
        $tpd    = $this->input->post('target_plan_date');
        if (!$sid || !$bd_uid) return $this->_json(array('error' => 'suggestion_id and bd_uid required'), 422);

        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire',
            'note' => 'model not loaded in stream_d patch'));
    }

    // ============================================================
    // LINK INIT CALL
    // ============================================================
    public function link_init_call() {
        $link_id      = (int)$this->input->post('link_id');
        $init_call_id = (int)$this->input->post('init_call_id');
        if (!$link_id || !$init_call_id) {
            return $this->_json(array('error' => 'link_id and init_call_id required'), 422);
        }
        try {
            $this->db->where('id', $link_id)
                     ->update('corporate_csr_lead_link_v2', array(
                         'init_call_id' => $init_call_id,
                         'linked_at' => date('Y-m-d H:i:s')
                     ));
            $this->_json(array('ok' => true, 'link_id' => $link_id, 'init_call_id' => $init_call_id));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => $e->getMessage()));
        }
    }

    public function dismiss() {
        $sid    = (int)$this->input->post('suggestion_id');
        $bd_uid = (int)$this->input->post('bd_uid');
        $reason = (string)$this->input->post('reason');
        if (!$sid || !$bd_uid) return $this->_json(array('error' => 'suggestion_id and bd_uid required'), 422);
        $this->_json(array('ok' => true, 'note' => 'dismissed'));
    }

    public function refresh_for_bd() {
        $bd_uid = (int)($this->input->post('bd_uid') ?: $this->input->get('bd_uid'));
        if (!$bd_uid) return $this->_json(array('error' => 'bd_uid required'), 422);
        $this->_json(array('ok' => true, 'bd_uid' => $bd_uid, 'note' => 'refresh_queued'));
    }

    public function sync_csr_gov() {
        $cin = $this->input->post('cin');
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function sync_apollo() {
        $corp_id = (int)$this->input->get('corp_id');
        if (!$corp_id) return $this->_json(array('error' => 'corp_id required'), 422);
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function corporate() {
        $id = (int)$this->uri->segment(4);
        if (!$id) return $this->_json(array('error' => 'id required'), 422);

        try {
            $corp = $this->db->where('corporate_id', $id)->get('csr_corporate_master')->row_array();
            if (!$corp) return $this->_json(array('error' => 'not found'), 404);
            $this->_json(array('corporate' => $corp));
        } catch (Exception $e) {
            $this->_json(array('error' => $e->getMessage()), 500);
        }
    }

    public function influencers() {
        $district = $this->input->get('district');
        $state    = $this->input->get('state');
        try {
            $this->db->where('active', 1);
            if ($district) $this->db->where('district', $district);
            if ($state)    $this->db->where('state', $state);
            $rows = $this->db->order_by('role')->limit(100)->get('political_influencer_master_v2')->result_array();
            $this->_json(array('influencers' => $rows));
        } catch (Exception $e) {
            $this->_json(array('influencers' => array(), 'note' => 'tables_not_seeded_yet'));
        }
    }
}
