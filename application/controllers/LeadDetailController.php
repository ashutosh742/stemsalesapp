<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadDetailController
 * application/controllers/LeadDetailController.php
 *
 * Endpoint:
 *   GET /api/lead/detail/{cid_id}
 *
 * Returns:
 *   - Full init_call row for the given cid_id
 *   - cstatus integer + cstatus label from the status table
 *   - fbudget (varchar as stored in DB)
 *   - Last 50 tblcallevents rows for this cid_id
 *     (joined with action name, purpose name, and createdby user name)
 *   - Cash debit rows from cash_log linked via task_id -> tblcallevents.id for this cid
 *
 * Schema confirmed on staging 30 May 2026:
 *   init_call PK is `id`, not `cid_id`. cstatus is int. fbudget is varchar(255).
 *   mainbd is int (BD uid). clm_id is varchar (CM uid, stored as string).
 *   tblcallevents.cid_id is FK to init_call.id.
 *   tblcallevents.actiontype_id FK to action.id (name col).
 *   tblcallevents.purpose_id FK to purpose.id (name col).
 *   tblcallevents.user_id FK to user.uid (name col).
 *   cash_log.task_id links to tblcallevents.id; no direct cid_id in cash_log.
 *
 * Auth: Bearer token compared against APPPATH/config/digest_token.txt
 *
 * Agent A - Blitz 30 May 2026
 * Plain English comments. No em-dashes. ASCII only. Rs not currency symbol.
 */
class LeadDetailController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // Bearer token check. Returns true if valid, writes 401 and returns false
    // if the token is missing or wrong.
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
    // Write JSON response. Always HTTP 200 unless $code is set differently.
    // -------------------------------------------------------------------------
    private function _json($data, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    // -------------------------------------------------------------------------
    // GET /api/lead/detail/{cid_id}
    //
    // $cid_id is the init_call.id value passed in the URI segment.
    // -------------------------------------------------------------------------
    public function index($cid_id = 0) {
        if (!$this->_bearer()) return;

        $cid_id = (int) $cid_id;
        // Additive 2026-06-17: the app also calls GET /api/lead/detail with the
        // lead id as a query param instead of a uri segment. When the path arg
        // is absent, read the id from ?id= or ?cid=. The existing
        // /api/lead/detail/(:num) route is unaffected ($cid_id arrives > 0).
        if ($cid_id <= 0) {
            $cid_id = (int) ($this->input->get('id') ?: $this->input->get('cid'));
        }
        if ($cid_id <= 0) {
            $this->_json([
                'ok'           => false,
                'success'      => false,
                'stub'         => false,
                'error'        => 'cid_id must be a positive integer',
                'rows'         => [],
                'data'         => ['count' => 0],
                'route'        => 'api/lead/detail',
                'generated_at' => date('c'),
            ]);
            return;
        }

        // -----------------------------------------------------------------
        // 1. Fetch the init_call lead row joined with status label.
        //    Columns confirmed via DESCRIBE init_call on staging.
        // -----------------------------------------------------------------
        $lead_sql = "
            SELECT
                ic.id                AS cid_id,
                ic.cstatus,
                s.name               AS cstatus_label,
                ic.fbudget,
                ic.fbudget_min,
                ic.fbudget_max,
                ic.mainbd,
                ic.clm_id,
                ic.insidebd,
                ic.createDate,
                ic.created_at,
                ic.updated_at,
                ic.lead_source,
                ic.fyear,
                ic.cmpid_id,
                ic.creator_id,
                ic.priorityc,
                ic.potential,
                ic.upsell_client,
                ic.keycompany,
                ic.new_lead,
                ic.in_quarter,
                ic.focus_funnel,
                ic.noofschools,
                ic.proposal_type,
                ic.proposal_amt,
                ic.proposaldate,
                ic.dm_contact_name,
                ic.dm_contact_designation,
                ic.dm_contact_phone,
                ic.dm_contact_email,
                ic.lead_score_cached,
                ic.lead_score_updated_at,
                ic.reachout,
                ic.positive,
                ic.verypositive,
                ic.closure,
                ic.review_date,
                ic.school_lat,
                ic.school_lng
            FROM init_call ic
            LEFT JOIN status s ON s.id = ic.cstatus
            WHERE ic.id = ?
            LIMIT 1
        ";

        $lead_row = $this->db->query($lead_sql, [$cid_id])->row_array();

        // If no lead found, return 200 with empty rows (per contract).
        if (empty($lead_row)) {
            $this->_json([
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => [],
                'data'         => ['count' => 0, 'cid_id' => $cid_id],
                'route'        => 'api/lead/detail',
                'generated_at' => date('c'),
            ]);
            return;
        }

        // rimlyproof_leadscope_20260609: a field user (BD/ACM) may only open a
        // lead they own or created. Managers/system see all. Closes the cross-BD
        // detail leak where any BD could read another BD's lead by id.
        if (function_exists('authunify_lead_can_view')) {
            $owner_uid   = isset($lead_row['mainbd'])     ? (int)$lead_row['mainbd']     : 0;
            $creator_uid = isset($lead_row['creator_id']) ? (int)$lead_row['creator_id'] : 0;
            if (!authunify_lead_can_view($owner_uid, $creator_uid)) {
                $this->_json([
                    'ok'    => false,
                    'error' => 'forbidden',
                    'note'  => 'lead_not_in_your_scope',
                ], 403);
                return;
            }
        }

        // -----------------------------------------------------------------
        // 2. Fetch last 50 tblcallevents rows for this cid_id.
        //    Joined with action.name (actiontype), purpose.name, user.name.
        //    Columns confirmed via DESCRIBE tblcallevents on staging.
        //    'action' table holds actiontype names (id, name, ...).
        // -----------------------------------------------------------------
        $events_sql = "
            SELECT
                ce.id,
                ce.cid_id,
                ce.actiontype_id,
                a.name               AS actiontype_name,
                ce.purpose_id,
                p.name               AS purpose_name,
                ce.date              AS createDate,
                ce.remarks,
                ce.special_remarks,
                ce.actontaken,
                ce.status_id,
                ce.user_id,
                u.name               AS createdby_name,
                ce.plan,
                ce.appointmentdatetime,
                ce.event_date,
                ce.mom,
                ce.nextaction,
                ce.fwd_date,
                ce.mom_received,
                ce.meeting_type,
                ce.cash_allot,
                ce.cash_expense,
                ce.actual_cost,
                ce.advance_disposition,
                ce.gps_gate_status
            FROM tblcallevents ce
            LEFT JOIN action a ON a.id = ce.actiontype_id
            LEFT JOIN purpose p ON p.id = ce.purpose_id
            LEFT JOIN user u ON u.uid = ce.user_id
            WHERE ce.cid_id = ?
            ORDER BY ce.date DESC, ce.id DESC
            LIMIT 50
        ";

        $events = $this->db->query($events_sql, [$cid_id])->result_array();

        // -----------------------------------------------------------------
        // 3. Fetch cash_log debit rows linked to this cid via task IDs.
        //    cash_log has no direct cid_id column. We join through
        //    tblcallevents.id = cash_log.task_id.
        //    We pull all cash_log rows for tasks that belong to this cid.
        // -----------------------------------------------------------------
        $cash_sql = "
            SELECT
                cl.id,
                cl.uid,
                cl.cash,
                cl.av_cash          AS running_balance,
                cl.type             AS transaction_type,
                cl.remarks,
                cl.task_id,
                cl.created_at
            FROM cash_log cl
            WHERE cl.task_id IN (
                SELECT id FROM tblcallevents WHERE cid_id = ?
            )
            ORDER BY cl.id DESC
            LIMIT 100
        ";

        $cash_rows = $this->db->query($cash_sql, [$cid_id])->result_array();

        // -----------------------------------------------------------------
        // Build and return the response.
        // -------------------------------------------------------------------------
        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => [[
                'lead'   => $lead_row,
                'events' => $events,
                'cash'   => $cash_rows,
            ]],
            'data'         => [
                'count'        => 1,
                'cid_id'       => $cid_id,
                'events_count' => count($events),
                'cash_count'   => count($cash_rows),
            ],
            'route'        => 'api/lead/detail',
            'generated_at' => date('c'),
        ]);
    }
}
