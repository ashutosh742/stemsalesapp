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
        $bds = $this->db->select('user_id')->where('type_id', 3)->where('status', 1)->get('user')->result();
        $total = 0;
        foreach ($bds as $bd) {
            $total += $this->scorer->score_bd($bd->user_id);
        }
        return $this->_json(['bds' => count($bds), 'leads_scored' => $total]);
    }

    public function hot_leads() {
        if (!digest_auth_check($this)) return;
        $bd_uid = (int) ($this->input->get('bd_uid') ?: $this->_caller_uid());
        $rows = $this->db->select('cid_id, win_probability, predicted_close_value_rs, top_positive_signal, next_best_action, confidence_band')
            ->from('ai_lead_score s')
            ->join('init_call ic', 'ic.cid = s.cid_id', 'inner')
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
