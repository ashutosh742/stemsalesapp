<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * KnowledgeV28 Controller
 *
 * Routes:
 *   GET /api/knowledge
 *   GET /api/knowledge/library
 *   GET /api/knowledge/list
 *   GET /api/knowledge/probe
 *
 * Real tables: knowledge_artifact, knowledge_category, knowledge_faq, knowledge_ack
 * knowledge_artifact columns: id, artifact_type, title, body, file_url, target_segment,
 *   force_ack, expiry_date, status, category, tags, version, uploaded_by_name, uploaded_at
 */
class KnowledgeV28 extends CI_Controller {

    private $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->output->set_content_type('application/json');
    }

    private function auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || trim(str_replace('Bearer', '', $h)) !== $this->token) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    private function json_out($data, $status = 200)
    {
        $this->output->set_status_header($status)
                     ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * GET /api/knowledge
     * Summary: counts per category and total live artifacts.
     */
    public function index()
    {
        if (!$this->auth()) return;
        $total = $this->db->where('status', 'live')->count_all_results('knowledge_artifact');
        $cats  = $this->db->select('category, COUNT(*) AS cnt')
                          ->from('knowledge_artifact')
                          ->where('status', 'live')
                          ->group_by('category')
                          ->order_by('cnt', 'DESC')
                          ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'total_live' => $total, 'by_category' => $cats]);
    }

    /**
     * GET /api/knowledge/library
     * Full library of live artifacts, optionally filtered by ?category= or ?type=
     */
    public function library()
    {
        if (!$this->auth()) return;
        $cat  = $this->input->get('category');
        $type = $this->input->get('type');
        $this->db->select('id, artifact_type, title, file_url, target_segment, category, tags, version, uploaded_by_name, uploaded_at')
                 ->from('knowledge_artifact')
                 ->where('status', 'live');
        if ($cat)  $this->db->where('category', $cat);
        if ($type) $this->db->where('artifact_type', $type);
        $rows = $this->db->order_by('uploaded_at', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/knowledge/list
     * Alias for library - returns live artifacts list.
     */
    public function list()
    {
        if (!$this->auth()) return;
        $rows = $this->db->select('id, artifact_type, title, category, tags, target_segment, uploaded_at')
                         ->from('knowledge_artifact')
                         ->where('status', 'live')
                         ->order_by('uploaded_at', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $faqs = $this->db->select('id, question, status')
                         ->from('knowledge_faq')
                         ->where('status', 'approved')
                         ->limit(20)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'artifacts' => $rows,
                         'faqs' => $faqs, 'count' => count($rows)]);
    }

    /**
     * GET /api/knowledge/probe
     */
    public function probe()
    {
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'KnowledgeV28 online']);
    }
}
