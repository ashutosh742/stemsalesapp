<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Lead Query Controller
 * Migration 026 (Phase 2, live 1 Jul 2026)
 *
 * Routes:
 *   GET  /api/lead/query/probe
 *   GET  /api/lead/query/for_cid?cid_id=
 *   GET  /api/lead/query/for_owner?owner_uid=&status=
 *   GET  /api/lead/query/overdue
 *   POST /api/lead/query/raise
 *   POST /api/lead/query/mark_in_progress
 *   POST /api/lead/query/resolve
 *   POST /api/lead/query/drop
 *   POST /api/lead/query/suggest_from_mom    (used by MoM v2 submit hook)
 *   GET  /api/lead_query/checklist?days=N    (M026 - added Stream E 2026-05-26)
 */
class Lead_query extends CI_Controller
{
    protected $token;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // Load optional libraries - catch failures gracefully
        try {
            $this->load->library('Bearer_auth');
            $this->load->library('lead_query_tracker_agent');
            $this->token = $this->bearer_auth->get_bearer_token();
        } catch (Exception $e) {
            $this->token = null;
        }
    }

    public function probe()
    {
        try {
            if ($this->token !== null && isset($this->lead_query_tracker_agent)) {
                $this->_json($this->lead_query_tracker_agent->probe(), 200);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'controller' => 'Lead_query', 'migration' => 'M026', 'status' => 'ready', 'server_time' => date('c')]);
                exit;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'controller' => 'Lead_query', 'migration' => 'M026', 'status' => 'ready', 'server_time' => date('c')]);
            exit;
        }
    }

    // M026 checklist endpoint: leads missing DM contact / proposal / MoM in last N days
    // GET /api/lead_query/checklist?days=7
    public function checklist()
    {
        try {
            header('Content-Type: application/json');
            $days = max(1, min(90, (int)($this->input->get('days') ?: 7)));
            $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

            // Use JOIN+GROUP for performance (correlated subquery was too slow on 60k leads)
            $rows = $this->db->query(
                "SELECT ic.id AS lead_id,
                        ic.cstatus,
                        ic.createDate,
                        ic.proposal_amt,
                        ic.apst,
                        cm.compname AS school,
                        u.name AS bd_name,
                        MAX(md.cdate) AS last_mom_date,
                        DATEDIFF(NOW(), MAX(md.cdate)) AS days_since_mom
                 FROM init_call ic
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 LEFT JOIN user_details u    ON u.user_id = ic.mainbd
                 LEFT JOIN mom_data md       ON md.init_cmpid = ic.id
                 WHERE ic.cstatus BETWEEN 2 AND 10
                 GROUP BY ic.id, ic.cstatus, ic.createDate, ic.proposal_amt, ic.apst,
                          cm.compname, u.name
                 HAVING (last_mom_date IS NULL OR last_mom_date < ?)
                 ORDER BY ic.createDate DESC
                 LIMIT 100",
                [$cutoff]
            )->result_array();

            echo json_encode([
                'ok'    => true,
                'days'  => $days,
                'note'  => 'leads_with_no_recent_mom',
                'rows'  => $rows,
                'count' => count($rows),
            ]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'error', 'detail' => $e->getMessage()]);
            exit;
        }
    }

    public function for_cid()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $cid_id = (int)$this->input->get('cid_id');
        if ($cid_id <= 0) return $this->_json(['error' => 'cid_id_required'], 400);

        $rows = $this->db->select('q.*, ub.fname AS owner_fname, ub.lname AS owner_lname')
            ->from('lead_query_checklist q')
            ->join('user ub', 'ub.uid = q.owner_uid', 'left')
            ->where('q.cid_id', $cid_id)
            ->order_by('q.raised_at', 'desc')
            ->get()->result_array();

        $this->_json([
            'cid_id' => $cid_id,
            'count'  => count($rows),
            'rows'   => $rows,
        ], 200);
    }

    public function for_owner()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $owner_uid = (int)$this->input->get('owner_uid');
        $status    = (string)$this->input->get('status');
        if ($owner_uid <= 0) return $this->_json(['error' => 'owner_uid_required'], 400);

        $this->db->select('q.*, cm.compname AS school_name')
            ->from('lead_query_checklist q')
            ->join('init_call ic', 'ic.id = q.cid_id')
            ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
            ->where('q.owner_uid', $owner_uid);
        if ($status) {
            $this->db->where('q.status', $status);
        } else {
            $this->db->where_in('q.status', ['open','in_progress']);
        }
        $rows = $this->db->order_by('q.sla_deadline', 'asc')->get()->result_array();

        $this->_json(['owner_uid' => $owner_uid, 'count' => count($rows), 'rows' => $rows], 200);
    }

    public function overdue()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $rows = $this->db->select('*')->from('v_lead_query_overdue')->get()->result_array();
        $this->_json(['count' => count($rows), 'rows' => $rows], 200);
    }

    public function raise()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $payload = [
            'cid_id'         => (int)$this->input->post('cid_id'),
            'query_type'     => (string)$this->input->post('query_type'),
            'query_text'     => (string)$this->input->post('query_text'),
            'raised_by_uid'  => (int)$this->input->post('raised_by_uid'),
            'raised_by_role' => (string)$this->input->post('raised_by_role'),
            'owner_uid'      => (int)$this->input->post('owner_uid'),
            'owner_role'     => (string)$this->input->post('owner_role'),
            'sla_hours'      => $this->input->post('sla_hours'),
        ];
        $res = $this->lead_query_tracker_agent->raise_query($payload);
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function mark_in_progress()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $res = $this->lead_query_tracker_agent->mark_in_progress(
            (int)$this->input->post('query_id'),
            (int)$this->input->post('owner_uid')
        );
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function resolve()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $res = $this->lead_query_tracker_agent->resolve_query(
            (int)$this->input->post('query_id'),
            (int)$this->input->post('owner_uid'),
            (string)$this->input->post('resolution_note'),
            $this->input->post('resolution_doc_url')
        );
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function drop()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $res = $this->lead_query_tracker_agent->drop_query(
            (int)$this->input->post('query_id'),
            (int)$this->input->post('by_uid'),
            (string)$this->input->post('reason')
        );
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function suggest_from_mom()
    {
        try {
            if (!$this->token || !isset($this->bearer_auth) || !$this->bearer_auth->verify($this->token)) {
                return $this->_json(['error' => 'unauthorized'], 401);
            }
        } catch (Exception $e) { return $this->_json(['error' => 'unauthorized'], 401); }
        $mom_id = (int)$this->input->post('mom_id');
        if ($mom_id <= 0) return $this->_json(['error' => 'mom_id_required'], 400);
        $this->_json($this->lead_query_tracker_agent->suggest_from_mom($mom_id), 200);
    }

    protected function _json($data, $code)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
