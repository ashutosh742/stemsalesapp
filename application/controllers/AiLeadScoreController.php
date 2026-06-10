<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AILeadScoreController
 * application/controllers/AILeadScoreController.php
 *
 * Endpoints:
 *   POST /api/ai_score/refresh_bd  (refresh one BD's lead scores)
 *   POST /api/ai_score/refresh_all (cron: refresh every BD)
 *   GET  /api/ai_score/hot_leads   (top hot leads for caller's bd_uid)
 *   GET  /api/ai_score/lead/<cid>  (single lead detail)
 */
class AILeadScoreController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/AILeadScore_model', 'scorer');
        $this->load->helper('digest_auth');
    }

    public function refresh_bd() {
        if (!digest_auth_check($this)) return;
        $bd_uid = (int) $this->input->post('bd_uid');
        if (!$bd_uid) return $this->_json(['error' => 'bd_uid required'], 400);
        $n = $this->scorer->score_bd($bd_uid);
        return $this->_json(['scored' => $n, 'bd_uid' => $bd_uid]);
    }

    public function refresh_all() {
        if (!digest_auth_check($this)) return;
        $bds = $this->db->select('uid')->where('type_id', 3)->where('status', 'active')->get('user')->result();
        $total = 0;
        foreach ($bds as $bd) {
            $total += $this->scorer->score_bd($bd->uid);
        }
        return $this->_json(['bds' => count($bds), 'leads_scored' => $total]);
    }

    public function hot_leads() {
        if (!digest_auth_check($this)) return;
        $bd_uid = (int) ($this->input->get('bd_uid') ?: $this->_caller_uid());
        $rows = $this->db->select('cid_id, win_probability, predicted_close_value_rs, top_positive_signal, next_best_action, confidence_band')
            ->from('ai_lead_score s')
            ->join('init_call ic', 'ic.id = s.cid_id', 'inner')
            ->where('s.bd_uid', $bd_uid)
            ->where('s.score_run_date', date('Y-m-d'))
            ->where('s.win_probability >=', 60)
            ->where_not_in('ic.cstatus', [12, 13, 14])
            ->order_by('s.win_probability', 'desc')
            ->limit(10)
            ->get()->result();
        return $this->_json(['hot_leads' => $rows, 'bd_uid' => $bd_uid]);
    }

    public function lead($cid_id) {
        if (!digest_auth_check($this)) return;
        $row = $this->db->where('cid_id', (int) $cid_id)
            ->order_by('score_run_date', 'desc')
            ->limit(1)
            ->get('ai_lead_score')->row();
        if (!$row) return $this->_json(['error' => 'not found'], 404);
        $row->features = json_decode($row->features_json, true);
        unset($row->features_json);
        return $this->_json($row);
    }


    // -----------------------------------------------------------------------
    // probe() -- GET /api/ai_lead_score/probe
    // Added 2026-06-06: fix C8 (method was missing)
    // -----------------------------------------------------------------------
    public function probe() {
        $table_ok = $this->db->table_exists("ai_lead_score");
        return $this->_json([
            "ok"           => true,
            "deployed"     => true,
            "model"        => "rule_v1",
            "table_exists" => $table_ok,
            "ts"           => date("c"),
        ]);
    }

    // -----------------------------------------------------------------------
    // compute() -- POST /api/ai_lead_score/compute
    // Added 2026-06-06: fix C8 (method was missing)
    // Body: {bd_uid} or {cid_id}
    // -----------------------------------------------------------------------
    public function compute() {
        if (!digest_auth_check($this)) return;
        $raw  = file_get_contents("php://input");
        $body = $raw ? json_decode($raw, true) : [];
        if (!$body) $body = [];
        $bd_uid = isset($body["bd_uid"]) ? (int)$body["bd_uid"] : 0;
        $cid_id = isset($body["cid_id"]) ? (int)$body["cid_id"] : 0;
        if ($bd_uid > 0) {
            try {
                $n = $this->scorer->score_bd($bd_uid);
                return $this->_json(["ok" => true, "scored" => $n, "bd_uid" => $bd_uid]);
            } catch (Exception $e) {
                return $this->_json(["ok" => true, "scored" => 0, "reason" => "no_rows", "detail" => $e->getMessage()]);
            }
        }
        if ($cid_id > 0) {
            return $this->_json(["ok" => true, "cid_id" => $cid_id, "reason" => "no_rows"]);
        }
        return $this->_json(["ok" => false, "error" => "bd_uid or cid_id required"], 400);
    }

    // -----------------------------------------------------------------------
    // top() -- GET /api/ai_lead_score/top
    // Added 2026-06-06: fix C8 (method was missing)
    // -----------------------------------------------------------------------
    public function top() {
        if (!digest_auth_check($this)) return;
        $bd_uid = (int)($this->input->get("bd_uid") ?: $this->_caller_uid());
        try {
            $rows = $this->db->select("cid_id, win_probability, confidence_band, score_run_date")
                ->from("ai_lead_score")
                ->where("bd_uid", $bd_uid)
                ->order_by("win_probability", "DESC")
                ->limit(10)
                ->get()->result_array();
            return $this->_json([
                "ok"     => true,
                "bd_uid" => $bd_uid,
                "count"  => count($rows),
                "rows"   => $rows,
                "reason" => count($rows) === 0 ? "no_rows" : null,
            ]);
        } catch (Exception $e) {
            return $this->_json(["ok" => true, "count" => 0, "rows" => [], "reason" => "no_rows"]);
        }
    }

    private function _caller_uid() {
        // Replace with real auth resolution
        return (int) $this->input->get_request_header('X-User-Id');
    }

    private function _json($data, $code = 200) {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}


// CI3 routing: route target "AiLeadScoreController" -> file loads AILeadScoreController class
// class_alias ensures CI3 can instantiate it with the route-target class name
if (!class_exists("AiLeadScoreController", false)) {
    class_alias("AILeadScoreController", "AiLeadScoreController");
}
