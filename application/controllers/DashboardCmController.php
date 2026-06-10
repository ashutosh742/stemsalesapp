<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DashboardCmController
 * application/controllers/DashboardCmController.php
 *
 * Endpoint:
 *   GET /api/dashboard/cm/{uid}
 *
 * Returns a CM-level dashboard for the given CM uid (type_id=13):
 *   - team_bd_uids      : distinct BD uid list under this CM
 *   - team_bd_count     : number of distinct BDs
 *   - open_leads        : total open leads (init_call cstatus < 12) across all team BDs
 *   - today_planned     : total tasks planned today across all team BDs
 *   - today_done        : total tasks done today across all team BDs
 *   - pending_approvals : count of mom_v2_submission rows with status='pending_cm' for this CM
 *   - won_this_month    : count and Rs sum of cstatus=12 leads updated this month for the CM's leads
 *
 * How BDs map to a CM:
 *   The most reliable join in staging data is init_call.clm_id = uid (varchar column).
 *   clm_id stores the CM's uid as a string. We find DISTINCT mainbd values from init_call
 *   where clm_id matches the CM uid. This was verified against real data on 30 May 2026.
 *
 *   NOTE: user_details.sales_co does NOT reliably link to CM uid=100070.
 *   The clm_id approach returns 5 distinct BDs for CM 100070, confirmed from DB.
 *
 * Pending approvals:
 *   mom_v2_submission table exists with cm_uid int column and status enum including 'pending_cm'.
 *   Queried directly for this CM uid.
 *
 * Won this month:
 *   Uses init_call rows where clm_id = uid (CM's full team scope), cstatus=12,
 *   and updated_at is in the current calendar month.
 *
 * Schema confirmed on staging 30 May 2026:
 *   init_call: clm_id=varchar(100), mainbd=int, cstatus=int, fbudget=varchar(255)
 *   tblcallevents: user_id=int, event_date=date, appointmentdatetime=datetime
 *   mom_v2_submission: cm_uid=int(10) unsigned, status enum includes 'pending_cm'
 *
 * Auth: Bearer token vs APPPATH/config/digest_token.txt
 *
 * Agent A - Blitz 30 May 2026
 * Plain English. No em-dashes. ASCII only. Rs not currency symbol.
 */
class DashboardCmController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // Bearer token check.
    // -------------------------------------------------------------------------
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected || !hash_equals($expected, $tok)) {
            $this->_json(['ok' => false, 'error' => 'bad_token'], 401);
            return false;
        }
        return true;
    }

    // -------------------------------------------------------------------------
    // Write JSON response.
    // -------------------------------------------------------------------------
    private function _json($data, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    // -------------------------------------------------------------------------
    // GET /api/dashboard/cm/{uid}
    // -------------------------------------------------------------------------
    public function index($uid = 0) {
        if (!$this->_bearer()) return;

        $uid = (int) $uid;
        if ($uid <= 0) {
            $this->_json([
                'ok'           => false,
                'success'      => false,
                'stub'         => false,
                'error'        => 'uid must be a positive integer',
                'rows'         => [],
                'data'         => ['count' => 0],
                'route'        => 'api/dashboard/cm',
                'generated_at' => date('c'),
            ]);
            return;
        }

        // -----------------------------------------------------------------
        // 1. Resolve BD team members under this CM.
        //    clm_id is varchar in init_call, stores CM uid as a string.
        //    We cast the param to string for the comparison.
        //    Returns distinct mainbd values (BD uids) for this CM.
        // -----------------------------------------------------------------
        $bd_sql = "
            SELECT DISTINCT mainbd
            FROM init_call
            WHERE clm_id = ?
              AND mainbd IS NOT NULL
              AND mainbd > 0
        ";
        $bd_rows = $this->db->query($bd_sql, [(string) $uid])->result_array();

        $team_bd_uids = [];
        foreach ($bd_rows as $r) {
            $team_bd_uids[] = (int) $r['mainbd'];
        }
        $team_bd_count = count($team_bd_uids);

        // -----------------------------------------------------------------
        // 2. Aggregate open leads across the CM's full scope.
        //    We use clm_id = uid to capture all leads managed by this CM,
        //    regardless of whether mainbd has changed.
        // -----------------------------------------------------------------
        $open_sql = "
            SELECT COUNT(*) AS open_leads
            FROM init_call
            WHERE clm_id = ?
              AND cstatus < 12
        ";
        $open_row   = $this->db->query($open_sql, [(string) $uid])->row_array();
        $open_leads = (int) ($open_row['open_leads'] ?? 0);

        // -----------------------------------------------------------------
        // 3. Today planned and done tasks across team BD uids.
        //    If no BDs are found, both counts are 0 (skip the query).
        // -----------------------------------------------------------------
        $planned_today = 0;
        $done_today    = 0;

        if (!empty($team_bd_uids)) {
            // Build a safe placeholder list for IN clause.
            $placeholders = implode(',', array_fill(0, count($team_bd_uids), '?'));

            $planned_sql = "
                SELECT COUNT(*) AS planned_today
                FROM tblcallevents
                WHERE user_id IN ($placeholders)
                  AND (
                      (event_date IS NOT NULL AND event_date = CURDATE())
                      OR (event_date IS NULL AND DATE(appointmentdatetime) = CURDATE())
                  )
            ";
            $planned_row   = $this->db->query($planned_sql, $team_bd_uids)->row_array();
            $planned_today = (int) ($planned_row['planned_today'] ?? 0);

            $done_sql = "
                SELECT COUNT(*) AS done_today
                FROM tblcallevents
                WHERE user_id IN ($placeholders)
                  AND actontaken IS NOT NULL
                  AND actontaken != ''
                  AND actontaken != 'no'
                  AND (
                      (event_date IS NOT NULL AND event_date = CURDATE())
                      OR (event_date IS NULL AND DATE(appointmentdatetime) = CURDATE())
                  )
            ";
            $done_row   = $this->db->query($done_sql, $team_bd_uids)->row_array();
            $done_today = (int) ($done_row['done_today'] ?? 0);
        }

        // -----------------------------------------------------------------
        // 4. Pending MOM approvals for this CM from mom_v2_submission.
        //    Table confirmed to exist on staging with cm_uid column and
        //    status enum value 'pending_cm'.
        // -----------------------------------------------------------------
        $approval_sql = "
            SELECT COUNT(*) AS pending_approvals
            FROM mom_v2_submission
            WHERE cm_uid = ?
              AND status = 'pending_cm'
        ";
        $approval_row      = $this->db->query($approval_sql, [$uid])->row_array();
        $pending_approvals = (int) ($approval_row['pending_approvals'] ?? 0);

        // -----------------------------------------------------------------
        // 5. Won this month across the CM's full lead scope (clm_id = uid).
        //    fbudget is varchar; cast to decimal for SUM.
        // -----------------------------------------------------------------
        $won_sql = "
            SELECT
                COUNT(*) AS won_count,
                COALESCE(SUM(CAST(fbudget AS DECIMAL(15,2))), 0) AS won_sum_rs
            FROM init_call
            WHERE clm_id = ?
              AND cstatus = 12
              AND MONTH(updated_at) = MONTH(CURDATE())
              AND YEAR(updated_at)  = YEAR(CURDATE())
        ";
        $won_row    = $this->db->query($won_sql, [(string) $uid])->row_array();
        $won_count  = (int)   ($won_row['won_count']  ?? 0);
        $won_sum_rs = (float) ($won_row['won_sum_rs'] ?? 0);

        // -----------------------------------------------------------------
        // Build dashboard row and return.
        // -----------------------------------------------------------------
        $dashboard = [
            'uid'               => $uid,
            'team_bd_count'     => $team_bd_count,
            'team_bd_uids'      => $team_bd_uids,
            'open_leads'        => $open_leads,
            'today_planned'     => $planned_today,
            'today_done'        => $done_today,
            'pending_approvals' => $pending_approvals,
            'won_this_month'    => [
                'count'  => $won_count,
                'sum_rs' => $won_sum_rs,
            ],
            'as_of_date'        => date('Y-m-d'),
        ];

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => [$dashboard],
            'data'         => ['count' => 1, 'uid' => $uid],
            'route'        => 'api/dashboard/cm',
            'generated_at' => date('c'),
        ]);
    }
}
