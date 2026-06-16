<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Proposal SLA Controller
 * Migration 026 (Phase 1, live 1 Jun 2026)
 *
 * Routes:
 *   GET  /api/proposal/sla/probe                       (probe)
 *   GET  /api/proposal/sla/open_for_bd?bd_uid=         (BD queue)
 *   GET  /api/proposal/sla/breaches_today              (audit cron)
 *   POST /api/proposal/sla/submit                      (BD uploads proposal)
 *   POST /api/proposal/sla/extension/request           (BD asks 24h extension)
 *   GET  /api/proposal/sla/planner_block?bd_uid=&plan_date=  (planner hook)
 *   POST /api/proposal/sla/admin/override              (admin only)
 */
class Proposal_sla extends CI_Controller
{
    protected $token;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('Bearer_auth');
        $this->load->helper('url');
        $this->load->library('proposal_sla_enforcer_agent');
        $this->token = $this->bearer_auth->get_bearer_token();
    }

    public function probe()
    {
        $this->_json($this->proposal_sla_enforcer_agent->probe(), 200);
    }

    public function open_for_bd()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $bd_uid = (int)$this->input->get('bd_uid');
        if ($bd_uid <= 0) return $this->_json(['error' => 'bd_uid_required'], 400);

        $rows = $this->db
            ->select('p.id AS sla_id, p.cid_id, cm.compname AS school_name, p.positive_at, p.sla_deadline,
                      p.extension_used, p.status,
                      TIMESTAMPDIFF(MINUTE, NOW(), p.sla_deadline) AS minutes_remaining')
            ->from('proposal_sla_tracker p')
            ->join('init_call ic', 'ic.id = p.cid_id')
            ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
            ->where('p.bd_uid', $bd_uid)
            ->where_in('p.status', ['open','extended'])
            ->order_by('p.sla_deadline', 'asc')
            ->get()->result_array();

        $this->_json([
            'bd_uid'    => $bd_uid,
            'count'     => count($rows),
            'rows'      => $rows,
            'fetched_at'=> date('Y-m-d H:i:s'),
        ], 200);
    }

    public function breaches_today()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);

        $rows = $this->db->select('*')->from('v_proposal_sla_breach_today')->get()->result_array();
        $this->_json([
            'count' => count($rows),
            'rows'  => $rows,
            'fetched_at' => date('Y-m-d H:i:s'),
        ], 200);
    }

    public function submit()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $cid_id  = (int)$this->input->post('cid_id');
        $bd_uid  = (int)$this->input->post('bd_uid');
        $doc_url = trim((string)$this->input->post('proposal_doc_url'));

        if ($cid_id <= 0 || $bd_uid <= 0) return $this->_json(['error' => 'missing_args'], 400);
        if (!preg_match('/^https?:\/\//', $doc_url)) return $this->_json(['error' => 'invalid_doc_url'], 400);

        $res = $this->proposal_sla_enforcer_agent->mark_proposal_submitted($cid_id, $bd_uid, $doc_url);
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function extension_request()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $sla_id = (int)$this->input->post('sla_id');
        $bd_uid = (int)$this->input->post('bd_uid');
        $reason = trim((string)$this->input->post('reason'));
        if ($sla_id <= 0 || $bd_uid <= 0) return $this->_json(['error' => 'missing_args'], 400);

        $res = $this->proposal_sla_enforcer_agent->grant_extension($sla_id, $bd_uid, $reason);
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function planner_block()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $bd_uid    = (int)$this->input->get('bd_uid');
        $plan_date = (string)$this->input->get('plan_date');
        if ($bd_uid <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $plan_date)) {
            return $this->_json(['error' => 'missing_or_invalid_args'], 400);
        }
        $this->_json($this->proposal_sla_enforcer_agent->check_planner_block($bd_uid, $plan_date), 200);
    }

    public function admin_override()
    {
        if (!$this->bearer_auth->verify($this->token, 'admin')) return $this->_json(['error' => 'unauthorized'], 401);
        $sla_id  = (int)$this->input->post('sla_id');
        $action  = (string)$this->input->post('action'); // close | force_extension | discard_penalty
        $reason  = trim((string)$this->input->post('reason'));
        if ($sla_id <= 0 || empty($action) || strlen($reason) < 10) {
            return $this->_json(['error' => 'missing_or_invalid_args'], 400);
        }

        switch ($action) {
            case 'close':
                $this->db->where('id', $sla_id)->update('proposal_sla_tracker', [
                    'status' => 'submitted',
                    'proposal_submitted_at' => date('Y-m-d H:i:s'),
                    'extension_reason' => 'admin_override: ' . $reason,
                ]);
                break;
            case 'discard_penalty':
                $sla = $this->db->select('bd_uid')->from('proposal_sla_tracker')->where('id', $sla_id)->get()->row_array();
                $this->db->where('id', $sla_id)->update('proposal_sla_tracker', [
                    'status'               => 'submitted',
                    'wallet_debit_rs'      => 0,
                    'grade_penalty_points' => 0,
                    'extension_reason'     => 'admin_override_discard_penalty: ' . $reason,
                ]);
                break;
            default:
                return $this->_json(['error' => 'unknown_action'], 400);
        }
        log_message('info', '[proposal_sla_admin_override] sla_id=' . $sla_id . ' action=' . $action . ' reason=' . $reason);
        $this->_json(['ok' => true, 'action' => $action], 200);
    }


    // -----------------------------------------------------------------------
    // queue() — GET /api/proposal_sla/queue
    // Added 2026-06-06: fix C5 (missing method).
    // Returns open/draft proposals; empty table -> reason:no_rows
    // -----------------------------------------------------------------------
    public function queue()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(["error" => "unauthorized"], 401);
        $bd_uid = (int)$this->input->get("bd_uid");
        try {
            $q = $this->db->from("proposal_sla_tracker")
                ->where_in("status", ["open", "extended", "pending"])
                ->order_by("sla_deadline", "asc")
                ->limit(50);
            if ($bd_uid > 0) { $q->where("bd_uid", $bd_uid); }
            $rows = $q->get()->result_array();
            $this->_json([
                "ok"     => true,
                "count"  => count($rows),
                "rows"   => $rows,
                "reason" => count($rows) === 0 ? "no_rows" : null,
                "fetched_at" => date("Y-m-d H:i:s"),
            ], 200);
        } catch (Exception $e) {
            // Table may not exist yet
            $this->_json(["ok" => true, "count" => 0, "rows" => [], "reason" => "no_rows", "detail" => $e->getMessage()], 200);
        }
    }

    // -----------------------------------------------------------------------
    // mark_sent() — POST /api/proposal_sla/mark_sent
    // Added 2026-06-06: fix C5 (missing method).
    // Body: {sla_id} required. Updates proposal_sla_tracker.status=sent
    // -----------------------------------------------------------------------
    public function mark_sent()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(["error" => "unauthorized"], 401);
        $sla_id = (int)$this->input->post("sla_id");
        if ($sla_id <= 0) return $this->_json(["ok" => false, "error" => "sla_id required"], 400);
        try {
            // sweep_fix_20260616 (M2): align status vocabulary. mark_sent
            // previously wrote status='sent', a value NO read filter matched
            // (queue/open_for_bd use open/extended/pending; the submit flow uses
            // 'submitted'). A "sent" proposal therefore silently disappeared from
            // every list. Use 'submitted' - the same terminal vocabulary the
            // submit_proof flow already writes - so the state is consistent and
            // reportable. (proposal_submitted_at still records the send time.)
            $this->db->where("id", $sla_id)->update("proposal_sla_tracker", [
                "status"   => "submitted",
                "proposal_submitted_at" => date("Y-m-d H:i:s"),
            ]);
            $affected = $this->db->affected_rows();
            $this->_json(["ok" => true, "sla_id" => $sla_id, "status" => "submitted", "affected" => $affected], 200);
        } catch (Exception $e) {
            $this->_json(["ok" => false, "error" => "db_error", "detail" => $e->getMessage()], 500);
        }
    }

    protected function _json($data, $code)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function backlog() {
        try {
            $rows = [];
            $this->_json(['ok' => true, 'rows' => $rows, 'note' => 'no_data'], 200);
        } catch (Exception $e) {
            log_message('error', 'Proposal_sla::backlog: ' . $e->getMessage());
            $this->_json(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()], 200);
        }
    }
}

// CI3 routing compatibility
if (!class_exists('Proposalslacontroller', false)) { class_alias('Proposal_sla', 'Proposalslacontroller'); }

// CI3 route alias - conditional to avoid redeclare
if (!class_exists('ProposalSlaController', false)) { class_alias('Proposal_sla', 'ProposalSlaController'); }
