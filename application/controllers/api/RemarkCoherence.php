<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/RestApiBaseController.php';

class RemarkCoherence extends RestApiBaseController {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'date'));
        if (file_exists(APPPATH . 'models/AIAgents/RemarkCoherence_model.php')) {
            $this->load->model('AIAgents/RemarkCoherence_model', 'rcs');
        }
    }

    public function probe() {
        $model_loaded = isset($this->rcs);
        $enabled = false;
        if ($model_loaded && method_exists($this->rcs, 'is_enabled')) {
            $enabled = (bool)$this->rcs->is_enabled();
        }
        $tables = $this->db->list_tables();
        $table_exists = in_array('remark_coherence_score', $tables, true);
        $this->_json(array(
            'ok'           => true,
            'service'      => 'remark_coherence',
            'migration'    => '049',
            'deployed'     => true,
            'model_loaded' => $model_loaded,
            'feature_flag' => $enabled ? 'on' : 'off',
            'table_exists' => $table_exists,
            'ts'           => date('Y-m-d H:i:s'),
            'auth_ok'      => $this->_auth_ok,
        ));
    }

    public function late() {
        $days = (int)$this->input->get('days');
        if ($days <= 0 || $days > 90) $days = 7;

        $tables = $this->db->list_tables();
        $has_score_table = in_array('remark_coherence_score', $tables, true);

        if ($has_score_table) {
            $sql = "SELECT
                        ce.id AS event_id, ce.late_remarks_message, ce.remarks,
                        COALESCE(ce.complete_time, ce.updated_at) AS event_date, ce.cid_id,
                        cm.compname AS school_name, cm.city,
                        u.name AS bd_name,
                        rcs.id AS score_id, rcs.score_total, rcs.grade,
                        ROUND(rcs.score_total / 100, 2) AS coherence_score,
                        rcs.pushback_required, rcs.source_table, rcs.source_pk,
                        rcs.is_money_claim, rcs.is_stage_promotion,
                        rpq.question_text AS pushback_template, rpq.status AS pushback_status
                    FROM tblcallevents ce
                    LEFT JOIN init_call ic ON ic.cmpid_id = ce.cid_id
                    LEFT JOIN company_master cm ON cm.id = ce.cid_id
                    LEFT JOIN user u ON u.uid = ce.user_id
                    LEFT JOIN remark_coherence_score rcs ON rcs.source_table = 'tblcallevents' AND rcs.source_pk = ce.id
                    LEFT JOIN remark_pushback_question rpq ON rpq.coherence_score_id = rcs.id AND rpq.status = 'open'
                    WHERE ce.late_remarks_message IS NOT NULL AND ce.late_remarks_message != ''
                      AND COALESCE(ce.complete_time, ce.updated_at) >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    ORDER BY rcs.score_total ASC, COALESCE(ce.complete_time, ce.updated_at) DESC LIMIT 200";
            $rows = $this->db->query($sql, array($days))->result_array();
        } else {
            $sql = "SELECT
                        ce.id AS event_id, ce.late_remarks_message, ce.remarks,
                        COALESCE(ce.complete_time, ce.updated_at) AS event_date, ce.cid_id,
                        cm.compname AS school_name, cm.city,
                        u.name AS bd_name,
                        NULL AS score_total, NULL AS grade, NULL AS coherence_score,
                        NULL AS pushback_template, 'tblcallevents' AS source_table, ce.id AS source_pk
                    FROM tblcallevents ce
                    LEFT JOIN company_master cm ON cm.id = ce.cid_id
                    LEFT JOIN user u ON u.uid = ce.user_id
                    WHERE ce.late_remarks_message IS NOT NULL AND ce.late_remarks_message != ''
                      AND COALESCE(ce.complete_time, ce.updated_at) >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    ORDER BY COALESCE(ce.complete_time, ce.updated_at) DESC LIMIT 200";
            $rows = $this->db->query($sql, array($days))->result_array();
        }

        $red_count = 0;
        foreach ($rows as $r) {
            $sc = isset($r['score_total']) && $r['score_total'] !== null ? (int)$r['score_total'] : 100;
            if ($sc < 40) $red_count++;
        }

        $this->_json(array('ok' => true, 'days' => $days, 'count' => count($rows),
            'red_count' => $red_count, 'rows' => $rows));
    }

    public function score() {
        $raw = file_get_contents('php://input');
        $body = @json_decode($raw, true);
        if (!$body) { $body = $_POST; }

        $action       = isset($body['action'])       ? trim($body['action'])       : '';
        $source_pk    = isset($body['source_pk'])    ? (int)$body['source_pk']    : 0;
        $source_table = isset($body['source_table']) ? trim($body['source_table']) : 'tblcallevents';
        $cm_note      = isset($body['cm_note'])      ? trim($body['cm_note'])      : '';

        if ($source_pk <= 0) { return $this->_fail(400, 'source_pk required'); }
        if (!in_array($action, array('approve', 'request_rewrite'), true)) {
            return $this->_fail(400, 'action must be approve or request_rewrite');
        }

        $tables = $this->db->list_tables();
        $has_score_table = in_array('remark_coherence_score', $tables, true);

        if ($action === 'approve') {
            if ($has_score_table) {
                $this->db->query(
                    "UPDATE remark_coherence_score SET pushback_required = 0 WHERE source_table = ? AND source_pk = ?",
                    array($source_table, $source_pk)
                );
            }
            $this->_json(array('ok' => true, 'action' => 'approved', 'source_pk' => $source_pk));
        } else {
            if ($has_score_table) {
                $score_row = $this->db->query(
                    "SELECT id, actor_uid FROM remark_coherence_score WHERE source_table = ? AND source_pk = ? LIMIT 1",
                    array($source_table, $source_pk)
                )->row_array();
                if ($score_row) {
                    $question_text = $cm_note !== '' ? 'CM rewrite request: ' . $cm_note : 'Please rewrite this remark with clearer justification.';
                    $existing = $this->db->query(
                        "SELECT id FROM remark_pushback_question WHERE coherence_score_id = ? AND status = 'open' LIMIT 1",
                        array($score_row['id'])
                    )->row_array();
                    if (!$existing) {
                        $this->db->query(
                            "INSERT INTO remark_pushback_question (coherence_score_id, template_code, actor_uid, actor_role, question_text, status, asked_at, sla_hours) VALUES (?, 'FREE_TEXT_PUSHBACK', ?, 'BD', ?, 'open', NOW(), 24)",
                            array($score_row['id'], $score_row['actor_uid'], $question_text)
                        );
                    }
                }
            }
            $this->_json(array('ok' => true, 'action' => 'rewrite_requested', 'source_pk' => $source_pk));
        }
    }
}
