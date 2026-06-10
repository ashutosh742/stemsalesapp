<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DashboardBdController
 * application/controllers/DashboardBdController.php
 *
 * Endpoint:
 *   GET /api/dashboard/bd/{uid}
 *
 * Returns a BD-level dashboard summary for the given BD uid:
 *   - today_planned   : count of tblcallevents tasks scheduled for today for this BD
 *   - today_done      : count of today's tasks that are completed (actontaken != 'no')
 *   - open_leads      : count of init_call rows where mainbd=uid and cstatus < 12
 *   - won_this_month  : count and fbudget sum for cstatus=12 leads updated this calendar month
 *   - wallet_balance  : av_cash from the most recent cash_log row for this uid
 *                       (av_cash is a running balance column in cash_log)
 *                       Returns 0 if no cash_log rows exist for the uid.
 *
 * Schema notes from staging DESCRIBE:
 *   init_call: PK=id, mainbd=int, cstatus=int, fbudget=varchar(255), updated_at=datetime
 *   tblcallevents: user_id=int FK to user.uid, event_date=date (preferred),
 *     appointmentdatetime=datetime (fallback), actontaken=varchar(500) default 'no'
 *   cash_log: uid=int, av_cash=int (running balance after transaction), id=PK auto-increment
 *
 * 'today planned' = event_date = CURDATE() OR (event_date IS NULL AND DATE(appointmentdatetime) = CURDATE())
 * 'done' = actontaken is not empty and not 'no' (matches Task_api.php pattern on staging)
 * Won month: uses updated_at to detect when cstatus was last set to 12.
 *
 * Auth: Bearer token vs APPPATH/config/digest_token.txt
 *
 * Agent A - Blitz 30 May 2026
 * Plain English. No em-dashes. ASCII only. Rs not currency symbol.
 */
class DashboardBdController extends CI_Controller {

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
    // GET /api/dashboard/bd/{uid}
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
                'route'        => 'api/dashboard/bd',
                'generated_at' => date('c'),
            ]);
            return;
        }

        // -----------------------------------------------------------------
        // 1. Today planned tasks for this BD.
        //    Uses event_date when set, falls back to DATE(appointmentdatetime).
        //    This matches the existing Task_api.php pattern on staging.
        // -----------------------------------------------------------------
        $planned_sql = "
            SELECT COUNT(*) AS planned_today
            FROM tblcallevents
            WHERE user_id = ?
              AND (
                  (event_date IS NOT NULL AND event_date = CURDATE())
                  OR (event_date IS NULL AND DATE(appointmentdatetime) = CURDATE())
              )
        ";
        $planned_row   = $this->db->query($planned_sql, [$uid])->row_array();
        $planned_today = (int) ($planned_row['planned_today'] ?? 0);

        // -----------------------------------------------------------------
        // 2. Today done tasks.
        //    Done = actontaken is populated and not the default value 'no'.
        //    Restricted to today's tasks using same date logic.
        // -----------------------------------------------------------------
        $done_sql = "
            SELECT COUNT(*) AS done_today
            FROM tblcallevents
            WHERE user_id = ?
              AND actontaken IS NOT NULL
              AND actontaken != ''
              AND actontaken != 'no'
              AND (
                  (event_date IS NOT NULL AND event_date = CURDATE())
                  OR (event_date IS NULL AND DATE(appointmentdatetime) = CURDATE())
              )
        ";
        $done_row   = $this->db->query($done_sql, [$uid])->row_array();
        $done_today = (int) ($done_row['done_today'] ?? 0);

        // -----------------------------------------------------------------
        // 3. Open leads: init_call where mainbd=uid and cstatus < 12.
        //    cstatus 12 = On-Boarded (won). Less than 12 = active pipeline.
        // -----------------------------------------------------------------
        $open_sql = "
            SELECT COUNT(*) AS open_leads
            FROM init_call
            WHERE mainbd = ?
              AND cstatus < 12
        ";
        $open_row   = $this->db->query($open_sql, [$uid])->row_array();
        $open_leads = (int) ($open_row['open_leads'] ?? 0);

        // -----------------------------------------------------------------
        // 4. Won this month: cstatus=12, updated_at in current month/year.
        //    fbudget is varchar in DB. We cast to decimal for the SUM.
        //    COALESCE ensures 0 is returned when no wins exist.
        // -----------------------------------------------------------------
        $won_sql = "
            SELECT
                COUNT(*) AS won_count,
                COALESCE(SUM(CAST(fbudget AS DECIMAL(15,2))), 0) AS won_sum_rs
            FROM init_call
            WHERE mainbd = ?
              AND cstatus = 12
              AND MONTH(updated_at) = MONTH(CURDATE())
              AND YEAR(updated_at)  = YEAR(CURDATE())
        ";
        $won_row       = $this->db->query($won_sql, [$uid])->row_array();
        $won_count     = (int)   ($won_row['won_count']    ?? 0);
        $won_sum_rs    = (float) ($won_row['won_sum_rs']   ?? 0);

        // -----------------------------------------------------------------
        // 5. Wallet balance: most recent av_cash value in cash_log for uid.
        //    av_cash is a running balance column -- the latest row holds the
        //    current balance. Returns 0 when no cash_log rows exist.
        // -----------------------------------------------------------------
        $wallet_sql = "
            SELECT av_cash
            FROM cash_log
            WHERE uid = ?
            ORDER BY id DESC
            LIMIT 1
        ";
        $wallet_row     = $this->db->query($wallet_sql, [$uid])->row_array();
        $wallet_balance = isset($wallet_row['av_cash']) ? (int) $wallet_row['av_cash'] : 0;

        // -----------------------------------------------------------------
        // Build dashboard row and return.
        // -----------------------------------------------------------------
        $dashboard = [
            'uid'            => $uid,
            'today_planned'  => $planned_today,
            'today_done'     => $done_today,
            'open_leads'     => $open_leads,
            'won_this_month' => [
                'count'  => $won_count,
                'sum_rs' => $won_sum_rs,
            ],
            'wallet_balance' => $wallet_balance,
            'as_of_date'     => date('Y-m-d'),
        ];

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => [$dashboard],
            'data'         => ['count' => 1, 'uid' => $uid],
            'route'        => 'api/dashboard/bd',
            'generated_at' => date('c'),
        ]);
    }
}
