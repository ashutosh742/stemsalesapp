<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/MasterReset_api.php
 *
 * DESTRUCTIVE mobile API mirroring the web MasterReset.php / MasterReset_model.php
 * ZoneWiseReset and BDWiseReset logic.
 *
 * What a reset does (per web source):
 *   For each init_call row owned by a BD in the target zone (or by the target BD):
 *     1. Determine resetCStatus:
 *        - Default 1 (no primary contact in company_contact_master)
 *        - If primary contact exists -> 8
 *        - If RP meeting done this FY (tblcallevents, mtype IN RP/RPClose/Change RP,
 *          actiontype_id IN 3,4,22) -> 3; also update clm_id, acm_co_id from BD's user row
 *        - If Proposal done this FY (tblcallevents, actiontype_id=7, user=bd) -> 6;
 *          also update rm_east_co_id, clm_id, acm_co_id
 *     2. Reset init_call: cstatus=resetCStatus, lstatus=old_cstatus,
 *        insidebd, bdid, abd, apst, ash columns, rm columns, acm_co_id, bpst, cpst, clm_id, super_admin all = 0
 *     3. Log to reset_company_log (all the old/new field values).
 *   After all rows: insert into annual_reset_status (fyear, by_type, zone_id|bd_id, reset_by, status=1).
 *
 * Endpoints (all Bearer protected):
 *   GET  api/master_reset/probe
 *   GET  api/master_reset/preview?scope=zone&zone_id=X  (or scope=bd&bd_id=X)
 *        DRY RUN: returns counts of what would be reset. NO write.
 *   POST api/master_reset/execute
 *        body: {scope, zone_id|bd_id, by_uid, confirm_token}
 *        confirm_token MUST equal 'CONFIRM-RESET'. Performs real reset.
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * ASCII only. No em-dashes. Rs not currency symbol.
 * Reads params via $_GET / php://input directly.
 * STAGING ONLY. Additive. Does NOT touch production.
 */
class MasterReset_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        return hash_equals($this->_known_token, $token);
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function _body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $j = json_decode($raw, true);
            if (is_array($j)) return $j;
        }
        return $_POST;
    }

    /**
     * Returns current financial year date range (April-to-March, last complete FY).
     * Mirrors web getFinancialYearRange().
     */
    private function _fy_range() {
        $month = (int)date('n');
        $year  = (int)date('Y');
        if ($month >= 4) {
            $sdate = $year . '-04-01';
            $edate = ($year + 1) . '-03-31';
        } else {
            $sdate = ($year - 1) . '-04-01';
            $edate = $year . '-03-31';
        }
        return array('start_date' => $sdate, 'end_date' => $edate);
    }

    /**
     * Returns last complete financial year string e.g. "2024-2025".
     * Mirrors web getLastFinancialYear() (note: web uses start-1 -> start, not current FY).
     */
    private function _last_fy() {
        $month = (int)date('n');
        $year  = (int)date('Y');
        if ($month >= 4) {
            $start = $year - 1;
            $end   = $year;
        } else {
            $start = $year - 2;
            $end   = $year - 1;
        }
        return $start . '-' . $end;
    }

    /**
     * Fetch funnel entries for a zone. Returns result array.
     * Mirrors MasterReset_model::GetBDFunnelsDatas_zoneID.
     */
    private function _get_entries_zone($zone_id) {
        $sql = "SELECT
                    u1.user_id as main_bd_id,
                    u1.name as bd_name,
                    init_call.id as init_id,
                    init_call.cmpid_id as cid,
                    init_call.cstatus,
                    init_call.lstatus,
                    init_call.clm_id,
                    init_call.acm_co_id,
                    init_call.apst,
                    init_call.rm_east_co_id,
                    init_call.rm_north_co_id,
                    init_call.ash_nae_co_id,
                    init_call.ash_w_co_id,
                    init_call.ash_s_co_id
                FROM init_call
                LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
                WHERE u1.status = 'active'
                  AND u1.zone_id = " . (int)$zone_id;
        return $this->db->query($sql)->result_array();
    }

    /**
     * Fetch funnel entries for a BD. Returns result array.
     * Mirrors MasterReset_model::GetBDFunnelsDatas_BDID.
     */
    private function _get_entries_bd($bd_id) {
        $sql = "SELECT
                    u1.user_id as main_bd_id,
                    u1.name as bd_name,
                    init_call.id as init_id,
                    init_call.cmpid_id as cid,
                    init_call.cstatus,
                    init_call.lstatus,
                    init_call.clm_id,
                    init_call.acm_co_id,
                    init_call.apst,
                    init_call.rm_east_co_id,
                    init_call.rm_north_co_id,
                    init_call.ash_nae_co_id,
                    init_call.ash_w_co_id,
                    init_call.ash_s_co_id
                FROM init_call
                LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
                WHERE u1.status = 'active'
                  AND u1.user_id = " . (int)$bd_id;
        return $this->db->query($sql)->result_array();
    }

    /**
     * Check whether a primary contact exists for a company.
     * Mirrors MasterReset_model::CheckPrimaryContactExistsOrNotinTable.
     */
    private function _has_primary_contact($cid) {
        $r = $this->db->query(
            "SELECT COUNT(*) c FROM company_contact_master
             WHERE company_id = " . (int)$cid . "
               AND type IN ('primary','Primary')"
        )->row();
        return ($r && $r->c > 0);
    }

    /**
     * Check whether an RP meeting was done in the current FY for an init_call.
     * Mirrors MasterReset_model::CheckCurrentFinancialYearRPMeetingDoneOrNot.
     */
    private function _has_rp_meeting($init_id, $sdate, $edate) {
        $r = $this->db->query(
            "SELECT COUNT(*) c FROM tblcallevents
             WHERE DATE(appointmentdatetime) BETWEEN '$sdate' AND '$edate'
               AND mtype IN ('RP','RPClose','Change RP')
               AND cid_id = " . (int)$init_id . "
               AND nextCFID != 0
               AND actiontype_id IN (3,4,22)"
        )->row();
        return ($r && $r->c > 0);
    }

    /**
     * Check whether a Proposal event was done in the current FY for an init_call by a BD.
     * Mirrors MasterReset_model::CheckCurrentFinancialYearProposalDoneOrNot.
     */
    private function _has_proposal($init_id, $sdate, $edate, $bd_id) {
        $r = $this->db->query(
            "SELECT COUNT(*) c FROM tblcallevents
             WHERE DATE(appointmentdatetime) BETWEEN '$sdate' AND '$edate'
               AND cid_id = " . (int)$init_id . "
               AND actiontype_id = 7
               AND user_id = " . (int)$bd_id
        )->row();
        return ($r && $r->c > 0);
    }

    /**
     * Get the RM/ACM/CM user IDs from the BD's user_details row.
     * Returns array with rm_user_id, acm_user_id, cm_user_id.
     */
    private function _get_bd_managers($main_bd_id) {
        $u = $this->db->query(
            "SELECT rm_east_co, acm_co, aadmin FROM user_details WHERE user_id = " . (int)$main_bd_id . " LIMIT 1"
        )->row_array();
        if (!$u) return array('rm_user_id' => 0, 'acm_user_id' => 0, 'cm_user_id' => 0);
        return array(
            'rm_user_id'  => (int)$u['rm_east_co'],
            'acm_user_id' => (int)$u['acm_co'],
            'cm_user_id'  => (int)$u['aadmin'],
        );
    }

    /**
     * Compute resetCStatus for one entry (read-only checks).
     * Returns the reset status integer (1, 3, 6, or 8).
     */
    private function _compute_reset_status($entry, $sdate, $edate) {
        $init_id = (int)$entry['init_id'];
        $cid     = (int)$entry['cid'];
        $bd_id   = (int)$entry['main_bd_id'];

        $reset_status = 1; // default: no primary contact

        if ($this->_has_primary_contact($cid)) {
            $reset_status = 8;
        }

        if ($this->_has_rp_meeting($init_id, $sdate, $edate)) {
            $reset_status = 3;
        }

        if ($this->_has_proposal($init_id, $sdate, $edate, $bd_id)) {
            $reset_status = 6;
        }

        return $reset_status;
    }

    // ----------------------------------------------------------------
    /** GET api/master_reset/probe */
    public function probe() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);

        $total_companies = (int)$this->db->query("SELECT COUNT(*) c FROM init_call")->row()->c;
        $active_bds      = (int)$this->db->query("SELECT COUNT(DISTINCT user_id) c FROM user_details WHERE status='active'")->row()->c;
        $zones_r         = $this->db->query("SELECT id, name FROM user_zone ORDER BY id")->result_array();
        $last_resets     = $this->db->query(
            "SELECT reset_type, zone_id, bd_id, update_by AS reset_by, created_at
             FROM reset_company_log
             ORDER BY id DESC LIMIT 5"
        )->result_array();

        $this->_json(array(
            'ok'               => true,
            'feature'          => 'master_reset',
            'total_init_calls' => $total_companies,
            'active_bds'       => $active_bds,
            'zones'            => $zones_r,
            'last_5_log_rows'  => $last_resets,
            'warning'          => 'execute endpoint is DESTRUCTIVE; requires confirm_token=CONFIRM-RESET and by_uid'
        ));
    }

    // ----------------------------------------------------------------
    /**
     * GET api/master_reset/preview?scope=zone&zone_id=X
     *                            OR scope=bd&bd_id=X
     * DRY RUN only. Returns counts of what would be reset. NO write.
     */
    public function preview() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);

        $scope   = isset($_GET['scope']) ? trim($_GET['scope']) : '';
        $zone_id = isset($_GET['zone_id']) ? (int)$_GET['zone_id'] : 0;
        $bd_id   = isset($_GET['bd_id'])   ? (int)$_GET['bd_id']   : 0;

        if ($scope === 'zone') {
            if ($zone_id <= 0) {
                $this->_json(array('ok' => false, 'error' => 'zone_id required for scope=zone'), 400);
            }
            $entries = $this->_get_entries_zone($zone_id);
            $scope_label = 'zone #' . $zone_id;
        } elseif ($scope === 'bd') {
            if ($bd_id <= 0) {
                $this->_json(array('ok' => false, 'error' => 'bd_id required for scope=bd'), 400);
            }
            $entries = $this->_get_entries_bd($bd_id);
            $scope_label = 'bd #' . $bd_id;
        } else {
            $this->_json(array('ok' => false, 'error' => 'scope must be zone or bd'), 400);
        }

        $total = count($entries);
        if ($total === 0) {
            $this->_json(array(
                'ok'            => true,
                'dry_run'       => true,
                'scope'         => $scope,
                'scope_label'   => $scope_label,
                'total_entries' => 0,
                'reason'        => 'no_rows',
                'summary'       => 'No active init_call records found for this scope. Nothing would be reset.'
            ));
        }

        $fy     = $this->_fy_range();
        $sdate  = $fy['start_date'];
        $edate  = $fy['end_date'];

        $status_buckets = array(
            'would_reset_to_1' => 0,
            'would_reset_to_3' => 0,
            'would_reset_to_6' => 0,
            'would_reset_to_8' => 0,
        );
        $bd_breakdown = array();

        foreach ($entries as $e) {
            $rs = $this->_compute_reset_status($e, $sdate, $edate);
            $key = 'would_reset_to_' . $rs;
            if (isset($status_buckets[$key])) {
                $status_buckets[$key]++;
            }
            $bd_name = $e['bd_name'];
            if (!isset($bd_breakdown[$bd_name])) {
                $bd_breakdown[$bd_name] = 0;
            }
            $bd_breakdown[$bd_name]++;
        }

        $this->_json(array(
            'ok'                => true,
            'dry_run'           => true,
            'scope'             => $scope,
            'scope_label'       => $scope_label,
            'total_entries'     => $total,
            'financial_year'    => array('start' => $sdate, 'end' => $edate),
            'would_reset_counts'=> $status_buckets,
            'bd_breakdown'      => $bd_breakdown,
            'status_legend'     => array(
                '1' => 'reset to init (no primary contact)',
                '3' => 'reset to RP meeting stage',
                '6' => 'reset to proposal stage',
                '8' => 'reset to primary contact stage'
            ),
            'summary'           => "Dry run for $scope_label: $total init_call rows would be processed."
        ));
    }

    // ----------------------------------------------------------------
    /**
     * POST api/master_reset/execute
     * body: {scope, zone_id|bd_id, by_uid, confirm_token}
     * confirm_token MUST equal 'CONFIRM-RESET'. DESTRUCTIVE.
     */
    public function execute() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);

        $b             = $this->_body();
        $scope         = isset($b['scope'])         ? trim($b['scope'])         : '';
        $zone_id       = isset($b['zone_id'])       ? (int)$b['zone_id']       : 0;
        $bd_id         = isset($b['bd_id'])         ? (int)$b['bd_id']         : 0;
        $by_uid        = isset($b['by_uid'])        ? (int)$b['by_uid']        : 0;
        $confirm_token = isset($b['confirm_token']) ? trim($b['confirm_token']) : '';

        // Hard guards
        if ($by_uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'by_uid required'), 400);
        }
        if ($confirm_token !== 'CONFIRM-RESET') {
            $this->_json(array(
                'ok'    => false,
                'error' => 'confirm_token must equal CONFIRM-RESET to proceed with destructive reset'
            ), 400);
        }
        if ($scope !== 'zone' && $scope !== 'bd') {
            $this->_json(array('ok' => false, 'error' => 'scope must be zone or bd'), 400);
        }

        // Fetch entries
        if ($scope === 'zone') {
            if ($zone_id <= 0) {
                $this->_json(array('ok' => false, 'error' => 'zone_id required for scope=zone'), 400);
            }
            $entries     = $this->_get_entries_zone($zone_id);
            $scope_id    = $zone_id;
            $scope_label = 'zone #' . $zone_id;
        } else {
            if ($bd_id <= 0) {
                $this->_json(array('ok' => false, 'error' => 'bd_id required for scope=bd'), 400);
            }
            $entries     = $this->_get_entries_bd($bd_id);
            $scope_id    = $bd_id;
            $scope_label = 'bd #' . $bd_id;
        }

        if (empty($entries)) {
            $this->_json(array(
                'ok'     => true,
                'scope'  => $scope,
                'reason' => 'no_rows',
                'affected_counts' => array('processed' => 0, 'logged' => 0),
                'logged' => false
            ));
        }

        $fy    = $this->_fy_range();
        $sdate = $fy['start_date'];
        $edate = $fy['end_date'];

        $processed  = 0;
        $log_count  = 0;

        foreach ($entries as $e) {
            $init_id        = (int)$e['init_id'];
            $cid            = (int)$e['cid'];
            $main_bd_id     = (int)$e['main_bd_id'];
            $old_cstatus    = $e['cstatus'];
            $old_lstatus    = $e['lstatus'];
            $old_clm_id     = (int)$e['clm_id'];
            $old_apst       = (int)$e['apst'];
            $old_rm_east    = (int)$e['rm_east_co_id'];
            $old_rm_north   = (int)$e['rm_north_co_id'];
            $old_ash_nae    = (int)$e['ash_nae_co_id'];
            $old_ash_w      = (int)$e['ash_w_co_id'];
            $old_ash_s      = (int)$e['ash_s_co_id'];
            $old_acm        = (int)$e['acm_co_id'];

            // Compute new status
            $reset_status = 1;
            if ($this->_has_primary_contact($cid)) {
                $reset_status = 8;
            }
            if ($this->_has_rp_meeting($init_id, $sdate, $edate)) {
                $reset_status = 3;
            }
            if ($this->_has_proposal($init_id, $sdate, $edate, $main_bd_id)) {
                $reset_status = 6;
            }

            // Get manager IDs from BD user row (needed when escalating to status 3 or 6)
            $mgrs = $this->_get_bd_managers($main_bd_id);

            // If RP meeting -> also set clm_id and acm_co_id (mirrors web logic)
            if ($reset_status === 3) {
                $this->db->where('id', $init_id)->update('init_call', array(
                    'clm_id'    => $mgrs['cm_user_id'],
                    'acm_co_id' => $mgrs['acm_user_id'],
                ));
            }

            // If Proposal -> also set rm_east_co_id, clm_id, acm_co_id
            if ($reset_status === 6) {
                $this->db->where('id', $init_id)->update('init_call', array(
                    'rm_east_co_id' => $mgrs['rm_user_id'],
                    'clm_id'        => $mgrs['cm_user_id'],
                    'acm_co_id'     => $mgrs['acm_user_id'],
                ));
            }

            // Main funnel reset
            $reset_data = array(
                'cstatus'       => $reset_status,
                'lstatus'       => $old_cstatus,
                'insidebd'      => 0,
                'bdid'          => 0,
                'abd'           => 0,
                'apst'          => 0,
                'ash_nae_co_id' => 0,
                'ash_w_co_id'   => 0,
                'ash_s_co_id'   => 0,
                'rm_east_co_id' => 0,
                'rm_north_co_id'=> 0,
                'acm_co_id'     => 0,
                'bpst'          => 0,
                'cpst'          => 0,
                'clm_id'        => 0,
                'super_admin'   => 0,
            );
            $this->db->where('id', $init_id)->update('init_call', $reset_data);

            if ($this->db->affected_rows() > 0) {
                $processed++;

                // Log to reset_company_log
                $log_data = array(
                    'reset_type'   => $scope,
                    'zone_id'      => ($scope === 'zone') ? $scope_id : 0,
                    'bd_id'        => ($scope === 'bd')   ? $scope_id : 0,
                    'init_id'      => $init_id,
                    'cid'          => $cid,
                    'old_status'   => $old_cstatus,
                    'new_status'   => $reset_status,
                    'old_clm_bd'   => $old_clm_id,
                    'new_clm_bd'   => 0,
                    'old_apst'     => $old_apst,
                    'new_apst'     => 0,
                    'old_rm_east'  => $old_rm_east,
                    'new_rm_east'  => 0,
                    'old_rm_north' => $old_rm_north,
                    'new_rm_north' => 0,
                    'old_ash_nae'  => $old_ash_nae,
                    'new_ash_nae'  => 0,
                    'old_ash_w'    => $old_ash_w,
                    'new_ash_w'    => 0,
                    'old_ash_s'    => $old_ash_s,
                    'new_ash_s'    => 0,
                    'old_acm'      => $old_acm,
                    'new_acm'      => 0,
                    'update_by'    => $by_uid,
                );
                $this->db->insert('reset_company_log', $log_data);
                if ($this->db->affected_rows() > 0) {
                    $log_count++;
                }
            }
        }

        // Insert summary row into annual_reset_status
        $last_fy      = $this->_last_fy();
        $annual_row   = array(
            'fyear'    => $last_fy,
            'by_type'  => $scope,
            'zone_id'  => ($scope === 'zone') ? $scope_id : 0,
            'bd_id'    => ($scope === 'bd')   ? $scope_id : 0,
            'reset_by' => $by_uid,
            'status'   => 1,
        );
        $this->db->insert('annual_reset_status', $annual_row);

        $this->_json(array(
            'ok'     => true,
            'scope'  => $scope,
            'scope_label'    => $scope_label,
            'financial_year' => $last_fy,
            'affected_counts' => array(
                'entries_found' => count($entries),
                'processed'     => $processed,
                'log_rows'      => $log_count,
            ),
            'logged' => ($log_count > 0)
        ));
    }
}
