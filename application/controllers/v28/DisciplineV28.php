<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DisciplineV28 Controller
 *
 * Covers BD/CM discipline metrics for STEM CRM v2.8.
 * Primary tables: bd_discipline_score, cm_discipline_score,
 *   bd_productivity_daily, cm_productivity_daily, stuck_leads_daily,
 *   band_violation_log, discipline_advance, expense_actuals_log,
 *   plan_submit_gate_log, wallet_trigger_log, cancellation_audit,
 *   meeting_cancel_log, planner_coach_discipline.
 *
 * Routes (see routes_v28_b.php):
 *   GET api/discipline/advance/list
 *   GET api/discipline/advance/probe
 *   GET api/discipline/advance_aging
 *   GET api/discipline/approval_gap
 *   GET api/discipline/band_violations
 *   GET api/discipline/cancellation_advance
 *   GET api/discipline/execution_gap
 *   GET api/discipline/expense_actuals
 *   GET api/discipline/meeting_expense_trail
 *   GET api/discipline/plan_submission
 *   GET api/discipline/probe
 *   GET api/discipline/wallet
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 *
 * Response envelope: {ok:true, success:true, rows:[...], count:N}
 * If table is empty: {ok:true, success:true, rows:[], count:0, note:"no_data"}
 */
class DisciplineV28 extends CI_Controller {

    /** Bearer token expected in Authorization header. */
    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    /**
     * Send JSON and stop.
     */
    private function json_out($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Verify bearer token. Returns false if missing or wrong.
     */
    private function auth_ok()
    {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        if (!$hdr) {
            return false;
        }
        // Accept "Bearer <token>" or raw token.
        $token = preg_replace('/^Bearer\s+/i', '', trim($hdr));
        return hash_equals(self::BEARER, $token);
    }

    /**
     * Require auth; outputs 401 and exits if not authenticated.
     */
    private function require_auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        if (!$this->auth_ok()) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
        }
    }

    /**
     * Resolve ?date= param; falls back to today.
     */
    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return date('Y-m-d');
    }

    /**
     * Build standard envelope from a result set.
     */
    private function envelope($rows, $extra = [])
    {
        $out = array_merge(
            ['ok' => true, 'success' => true],
            $extra,
            ['rows' => $rows, 'count' => count($rows)]
        );
        if (count($rows) === 0) {
            $out['note'] = 'no_data';
        }
        return $out;
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * GET api/discipline/advance/list
     *
     * Lists all open (unsettled) cash advances given to BDs.
     * Table: discipline_advance (id, uid, amount, given_date, settled, purpose)
     * Optional filter: ?uid=<bd_uid>
     */
    public function advance_list()
    {
        $this->require_auth();

        $uid = (int) $this->input->get('uid');

        $this->db->select('da.id, da.uid, u.name AS bd_name, da.amount, da.given_date, da.settled, da.purpose, da.created_at');
        $this->db->from('discipline_advance da');
        $this->db->join('user u', 'u.uid = da.uid', 'left');
        if ($uid > 0) {
            $this->db->where('da.uid', $uid);
        }
        $this->db->where('da.settled', 0);
        $this->db->order_by('da.given_date', 'DESC');
        $this->db->limit(200);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']       = (int) $r['id'];
            $r['uid']      = (int) $r['uid'];
            $r['amount']   = (float) $r['amount'];
            $r['settled']  = (int) $r['settled'];
        }
        unset($r);

        $this->json_out($this->envelope($rows));
    }

    /**
     * GET api/discipline/advance/probe
     *
     * Health check for the advance sub-group.
     */
    public function advance_probe()
    {
        header('Content-Type: application/json; charset=utf-8');
        $db_ok = (bool) $this->db->conn_id;
        echo json_encode(['ok' => $db_ok, 'success' => $db_ok]);
        exit;
    }

    /**
     * GET api/discipline/advance_aging
     *
     * Shows aging of unsettled BD advances:
     * how many days each advance has been outstanding.
     * Table: discipline_advance
     * Optional: ?uid=<bd_uid>
     */
    public function advance_aging()
    {
        $this->require_auth();

        $uid = (int) $this->input->get('uid');

        $this->db->select([
            'da.id',
            'da.uid',
            'u.name AS bd_name',
            'da.amount',
            'da.given_date',
            'da.purpose',
            'DATEDIFF(CURDATE(), da.given_date) AS days_outstanding',
        ], FALSE);
        $this->db->from('discipline_advance da');
        $this->db->join('user u', 'u.uid = da.uid', 'left');
        $this->db->where('da.settled', 0);
        if ($uid > 0) {
            $this->db->where('da.uid', $uid);
        }
        $this->db->order_by('days_outstanding', 'DESC');
        $this->db->limit(200);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']               = (int) $r['id'];
            $r['uid']              = (int) $r['uid'];
            $r['amount']           = (float) $r['amount'];
            $r['days_outstanding'] = (int) $r['days_outstanding'];
        }
        unset($r);

        $this->json_out($this->envelope($rows));
    }

    /**
     * GET api/discipline/approval_gap
     *
     * Shows CM discipline gaps: days where expense approvals are pending
     * beyond the expected SLA. Compares expense_actuals_log rows that are
     * still in pending state.
     * Optional: ?cm_uid=<cm_uid>&date=YYYY-MM-DD
     */
    public function approval_gap()
    {
        $this->require_auth();

        $cm_uid   = (int) $this->input->get('cm_uid');
        $for_date = $this->resolve_date();

        // Expense approvals pending for CM: rows needing cm_apr but not yet done.
        $this->db->select([
            'e.id',
            'e.bd_uid',
            'u_bd.name AS bd_name',
            'e.log_date',
            'e.expense_type',
            'e.actual_amount',
            'e.status',
            'e.final_state',
            'e.approved_by_uid AS cm_approved_by_uid',
            'DATEDIFF(CURDATE(), e.log_date) AS days_pending',
        ], FALSE);
        $this->db->from('expense_actuals_log e');
        $this->db->join('user u_bd', 'u_bd.uid = e.bd_uid', 'left');
        // Expenses where CM approval is not yet done
        $this->db->where('e.cm_approved', 0);
        $this->db->where('e.final_state', 'pending_cm');
        if ($cm_uid > 0) {
            // Filter by the CM who is responsible (approved_by_uid column used as target cm)
            $this->db->where('e.approved_by_uid', $cm_uid);
        }
        $this->db->order_by('days_pending', 'DESC');
        $this->db->limit(100);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']           = (int) $r['id'];
            $r['bd_uid']       = (int) $r['bd_uid'];
            $r['actual_amount'] = (float) $r['actual_amount'];
            $r['days_pending'] = (int) $r['days_pending'];
        }
        unset($r);

        $this->json_out($this->envelope($rows, ['for_date' => $for_date]));
    }

    /**
     * GET api/discipline/band_violations
     *
     * Lists band violation events for BDs.
     * Table: band_violation_log
     * Optional: ?bd_uid=<uid>&date=YYYY-MM-DD&status=open|acknowledged|resolved
     */
    public function band_violations()
    {
        $this->require_auth();

        $bd_uid = (int) $this->input->get('bd_uid');
        $date   = $this->resolve_date();
        $status = $this->input->get('status');
        $valid_statuses = ['open', 'acknowledged', 'disputed', 'resolved'];

        $this->db->select([
            'bvl.id',
            'bvl.bd_uid',
            'u.name AS bd_name',
            'bvl.violation_date',
            'bvl.violation_type',
            'bvl.band_code',
            'bvl.penalty_rs',
            'bvl.auto_detected',
            'bvl.status',
            'bvl.notes',
            'bvl.created_at',
        ], FALSE);
        $this->db->from('band_violation_log bvl');
        $this->db->join('user u', 'u.uid = bvl.bd_uid', 'left');
        if ($bd_uid > 0) {
            $this->db->where('bvl.bd_uid', $bd_uid);
        }
        if ($status && in_array($status, $valid_statuses, TRUE)) {
            $this->db->where('bvl.status', $status);
        }
        $this->db->order_by('bvl.violation_date', 'DESC');
        $this->db->limit(200);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']            = (int) $r['id'];
            $r['bd_uid']        = (int) $r['bd_uid'];
            $r['penalty_rs']    = (float) $r['penalty_rs'];
            $r['auto_detected'] = (int) $r['auto_detected'];
        }
        unset($r);

        $this->json_out($this->envelope($rows));
    }

    /**
     * GET api/discipline/cancellation_advance
     *
     * Shows cancellation events linked to travel advances that were
     * disbursed but the meeting was then cancelled.
     * Joins cancellation_audit -> travel_advance via linked_cancellation_event_id.
     * Optional: ?bd_uid=<uid>
     */
    public function cancellation_advance()
    {
        $this->require_auth();

        $bd_uid = (int) $this->input->get('bd_uid');

        // meeting_cancel_log has cid_id (lead) and uid (who cancelled).
        // travel_advance has linked_cancellation_event_id.
        // We join them to find advances that correspond to cancelled meetings.
        $this->db->select([
            'ta.id AS advance_id',
            'ta.user_id AS bd_uid',
            'u.name AS bd_name',
            'ta.date AS advance_date',
            'ta.cash AS advance_amount',
            'ta.purpose',
            'ta.consumed_status',
            'mcl.id AS cancel_log_id',
            'mcl.cid_id AS lead_id',
            'mcl.free_text AS cancel_reason',
            'mcl.created_at AS cancelled_at',
        ], FALSE);
        $this->db->from('travel_advance ta');
        $this->db->join('meeting_cancel_log mcl', 'mcl.id = ta.linked_cancellation_event_id', 'inner');
        $this->db->join('user u', 'u.uid = ta.user_id', 'left');
        if ($bd_uid > 0) {
            $this->db->where('ta.user_id', $bd_uid);
        }
        $this->db->order_by('ta.date', 'DESC');
        $this->db->limit(100);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['advance_id']     = (int) $r['advance_id'];
            $r['bd_uid']         = (int) $r['bd_uid'];
            $r['advance_amount'] = (int) $r['advance_amount'];
            $r['cancel_log_id']  = (int) $r['cancel_log_id'];
            $r['lead_id']        = (int) $r['lead_id'];
        }
        unset($r);

        $this->json_out($this->envelope($rows));
    }

    /**
     * GET api/discipline/execution_gap
     *
     * Shows BD execution gap: difference between planned_min and executed_min
     * from bd_productivity_daily, ordered by gap descending.
     * Optional: ?bd_uid=<uid>&date=YYYY-MM-DD
     */
    public function execution_gap()
    {
        $this->require_auth();

        $bd_uid   = (int) $this->input->get('bd_uid');
        $for_date = $this->resolve_date();

        $this->db->select([
            'p.id',
            'p.bd_uid',
            'u.name AS bd_name',
            'p.for_date',
            'p.planned_min',
            'p.executed_min',
            'p.idle_min',
            'p.budget_min',
            'p.score_pct',
            '(p.planned_min - p.executed_min) AS execution_gap_min',
        ], FALSE);
        $this->db->from('bd_productivity_daily p');
        $this->db->join('user u', 'u.uid = p.bd_uid', 'left');
        $this->db->where('p.for_date', $for_date);
        if ($bd_uid > 0) {
            $this->db->where('p.bd_uid', $bd_uid);
        }
        $this->db->order_by('execution_gap_min', 'DESC');
        $this->db->limit(100);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']                  = (int) $r['id'];
            $r['bd_uid']              = (int) $r['bd_uid'];
            $r['planned_min']         = (int) $r['planned_min'];
            $r['executed_min']        = (int) $r['executed_min'];
            $r['idle_min']            = (int) $r['idle_min'];
            $r['budget_min']          = (int) $r['budget_min'];
            $r['score_pct']           = (float) $r['score_pct'];
            $r['execution_gap_min']   = (int) $r['execution_gap_min'];
        }
        unset($r);

        $this->json_out($this->envelope($rows, ['for_date' => $for_date]));
    }

    /**
     * GET api/discipline/expense_actuals
     *
     * Returns expense actuals log rows for a given date with approval status.
     * Table: expense_actuals_log
     * Optional: ?bd_uid=<uid>&date=YYYY-MM-DD&status=pending|approved|rejected
     */
    public function expense_actuals()
    {
        $this->require_auth();

        $bd_uid   = (int) $this->input->get('bd_uid');
        $for_date = $this->resolve_date();
        $status   = $this->input->get('status');
        $valid_statuses = ['pending', 'approved', 'rejected'];

        $this->db->select([
            'e.id',
            'e.bd_uid',
            'u.name AS bd_name',
            'e.log_date',
            'e.expense_type',
            'e.planned_amount',
            'e.actual_amount',
            'e.variance_pct',
            'e.status',
            'e.final_state',
            'e.cm_approved',
            'e.ao_approved',
            'e.notes',
        ], FALSE);
        $this->db->from('expense_actuals_log e');
        $this->db->join('user u', 'u.uid = e.bd_uid', 'left');
        $this->db->where('e.log_date', $for_date);
        if ($bd_uid > 0) {
            $this->db->where('e.bd_uid', $bd_uid);
        }
        if ($status && in_array($status, $valid_statuses, TRUE)) {
            $this->db->where('e.status', $status);
        }
        $this->db->order_by('e.log_date', 'DESC');
        $this->db->limit(200);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']              = (int) $r['id'];
            $r['bd_uid']          = (int) $r['bd_uid'];
            $r['planned_amount']  = (float) $r['planned_amount'];
            $r['actual_amount']   = (float) $r['actual_amount'];
            $r['variance_pct']    = $r['variance_pct'] !== null ? (float) $r['variance_pct'] : null;
            $r['cm_approved']     = (int) $r['cm_approved'];
            $r['ao_approved']     = (int) $r['ao_approved'];
        }
        unset($r);

        $this->json_out($this->envelope($rows, ['for_date' => $for_date]));
    }

    /**
     * GET api/discipline/meeting_expense_trail
     *
     * For each meeting event, shows the corresponding expense claim trail:
     * advance given, actual spent, variance, approval state.
     * Joins expense_actuals_log -> tblcallevents via event_id.
     * Optional: ?bd_uid=<uid>&date=YYYY-MM-DD
     */
    public function meeting_expense_trail()
    {
        $this->require_auth();

        $bd_uid   = (int) $this->input->get('bd_uid');
        $for_date = $this->resolve_date();

        $this->db->select([
            'e.id AS expense_id',
            'e.event_id',
            'e.bd_uid',
            'u.name AS bd_name',
            'e.log_date',
            'e.expense_type',
            'e.planned_amount',
            'e.actual_amount',
            'e.variance_pct',
            'e.status AS expense_status',
            'e.final_state',
            'e.cm_approved',
            'e.ao_approved',
            'e.travel_advance_id',
            'ta.cash AS advance_given',
            'ta.consumed_status AS advance_status',
        ], FALSE);
        $this->db->from('expense_actuals_log e');
        $this->db->join('user u', 'u.uid = e.bd_uid', 'left');
        $this->db->join('travel_advance ta', 'ta.id = e.travel_advance_id', 'left');
        $this->db->where('e.log_date', $for_date);
        if ($bd_uid > 0) {
            $this->db->where('e.bd_uid', $bd_uid);
        }
        $this->db->order_by('e.log_date', 'DESC');
        $this->db->limit(100);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['expense_id']      = (int) $r['expense_id'];
            $r['event_id']        = $r['event_id'] !== null ? (int) $r['event_id'] : null;
            $r['bd_uid']          = (int) $r['bd_uid'];
            $r['planned_amount']  = (float) $r['planned_amount'];
            $r['actual_amount']   = (float) $r['actual_amount'];
            $r['variance_pct']    = $r['variance_pct'] !== null ? (float) $r['variance_pct'] : null;
            $r['cm_approved']     = (int) $r['cm_approved'];
            $r['ao_approved']     = (int) $r['ao_approved'];
            $r['advance_given']   = $r['advance_given'] !== null ? (int) $r['advance_given'] : null;
        }
        unset($r);

        $this->json_out($this->envelope($rows, ['for_date' => $for_date]));
    }

    /**
     * GET api/discipline/plan_submission
     *
     * Shows plan submission gate results per BD per day.
     * Table: plan_submit_gate_log
     * Optional: ?bd_uid=<uid>&date=YYYY-MM-DD&gate_result=passed|blocked|warning
     */
    public function plan_submission()
    {
        $this->require_auth();

        $bd_uid      = (int) $this->input->get('bd_uid');
        $for_date    = $this->resolve_date();
        $gate_result = $this->input->get('gate_result');
        $valid_gates = ['passed', 'blocked', 'warning'];

        $this->db->select([
            'g.id',
            'g.bd_uid',
            'u.name AS bd_name',
            'g.plan_date',
            'g.gate_result',
            'g.gate_reason',
            'g.submitted_at',
            'g.is_late',
            'g.blocked_reason_code',
            'g.created_at',
        ], FALSE);
        $this->db->from('plan_submit_gate_log g');
        $this->db->join('user u', 'u.uid = g.bd_uid', 'left');
        $this->db->where('g.plan_date', $for_date);
        if ($bd_uid > 0) {
            $this->db->where('g.bd_uid', $bd_uid);
        }
        if ($gate_result && in_array($gate_result, $valid_gates, TRUE)) {
            $this->db->where('g.gate_result', $gate_result);
        }
        $this->db->order_by('g.plan_date', 'DESC');
        $this->db->limit(200);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']      = (int) $r['id'];
            $r['bd_uid']  = (int) $r['bd_uid'];
            $r['is_late'] = (int) $r['is_late'];
        }
        unset($r);

        $this->json_out($this->envelope($rows, ['for_date' => $for_date]));
    }

    /**
     * GET api/discipline/probe
     *
     * Health check for the entire DisciplineV28 controller.
     * No auth required - safe to call freely.
     */
    public function probe()
    {
        header('Content-Type: application/json; charset=utf-8');
        $db_ok = (bool) $this->db->conn_id;
        echo json_encode(['ok' => $db_ok, 'success' => $db_ok, 'controller' => 'DisciplineV28']);
        exit;
    }

    /**
     * GET api/discipline/wallet
     *
     * Returns wallet trigger log entries for BDs.
     * Table: wallet_trigger_log (id, bd_uid, lead_id, reason, amount_rs, triggered_at)
     * Optional: ?bd_uid=<uid>
     */
    public function wallet()
    {
        $this->require_auth();

        $bd_uid = (int) $this->input->get('bd_uid');

        $this->db->select([
            'w.id',
            'w.bd_uid',
            'u.name AS bd_name',
            'w.lead_id',
            'w.reason',
            'w.amount_rs',
            'w.triggered_at',
        ], FALSE);
        $this->db->from('wallet_trigger_log w');
        $this->db->join('user u', 'u.uid = w.bd_uid', 'left');
        if ($bd_uid > 0) {
            $this->db->where('w.bd_uid', $bd_uid);
        }
        $this->db->order_by('w.triggered_at', 'DESC');
        $this->db->limit(200);

        $q = $this->db->get();
        if (!$q) {
            $this->json_out(['ok' => false, 'error' => 'db_error'], 500);
        }

        $rows = $q->result_array();
        foreach ($rows as &$r) {
            $r['id']        = (int) $r['id'];
            $r['bd_uid']    = (int) $r['bd_uid'];
            $r['lead_id']   = (int) $r['lead_id'];
            $r['amount_rs'] = (float) $r['amount_rs'];
        }
        unset($r);

        $this->json_out($this->envelope($rows));
    }
}
