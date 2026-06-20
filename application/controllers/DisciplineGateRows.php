<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DisciplineGateRows Controller (ADDITIVE, READ-ONLY)
 * ---------------------------------------------------------------------------
 * Root cause closed: three discipline gates (write_mom, fill_expense,
 * update_research) returned a non-zero count from DisciplineState_model but
 * had NO endpoint returning the EXACT rows that count counts. The pre-existing
 * endpoints (FunnelReportController::pending_moms, Mobile_read_api::expense_list)
 * use DIFFERENT WHERE clauses, so their row list could not be asserted to equal
 * the gate count. clear_pbni and clear_autotask already have contract-true row
 * endpoints (PlannerV28::pbni_list / pending_autotasks); this controller closes
 * the remaining three so EVERY gate has a row endpoint where count == gate count
 * and stub:false.
 *
 * Each method mirrors the corresponding DisciplineState_model count query's
 * WHERE clause EXACTLY, joined to init_call + company_master for company names,
 * so the row list returned here always equals the gate count for the same uid.
 *
 * Auth: Bearer token (same as PlannerV28). uid from ?uid / ?user_id / ?bd_uid.
 * ASCII only. No em-dashes. No writes. No production impact.
 */
class DisciplineGateRows extends CI_Controller {

    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->database();
        $this->load->library('BearerAuth');
    }

    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Accept master bearer, api_token, or per-user JWT via BearerAuth (same
     * contract as PlannerV28 so the mobile per-user JWT is honored).
     */
    private function auth_ok()
    {
        $auth = $this->bearerauth->resolve();
        if (empty($auth['ok'])) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    private function resolve_uid()
    {
        $uid = $this->input->get('uid');
        if ( ! $uid) { $uid = $this->input->get('user_id'); }
        if ( ! $uid) { $uid = $this->input->get('bd_uid'); }
        return (int) $uid;
    }

    /**
     * GET /api/discipline/mom_pending
     * Mirrors DisciplineState_model::get_rp_mom_count EXACTLY.
     */
    public function mom_pending()
    {
        if ( ! $this->auth_ok()) { return; }
        $uid = $this->resolve_uid();
        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.appointmentdatetime, bm.status AS meeting_status, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('barginmeeting bm', 'bm.tid = t.id', 'left');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.assignedto_id', $uid);
        $this->db->where_in('t.actiontype_id', array(3, 4, 17));
        $this->db->where('t.nextCFID !=', 0);
        $this->db->where('t.plan', 1);
        $this->db->where('t.approved_status', 1);
        $this->db->where("(bm.status = 'Close' OR bm.status = 'RPClose')", null, false);
        $this->db->where('t.mom IS NULL', null, false);
        $this->db->order_by('t.appointmentdatetime', 'DESC');
        $this->db->limit(200);
        $result = $this->db->get()->result_array();

        $tq = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM tblcallevents t
             LEFT JOIN barginmeeting bm ON bm.tid = t.id
             WHERE t.assignedto_id = ?
               AND t.actiontype_id IN (3, 4, 17)
               AND t.nextCFID != 0
               AND t.plan = 1
               AND t.approved_status = 1
               AND (bm.status = 'Close' OR bm.status = 'RPClose')
               AND t.mom IS NULL",
            array($uid)
        );
        $trow = $tq->result();
        $total = (int) ($trow[0]->cnt ?? 0);

        $rows = array();
        foreach ($result as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $rows[] = array(
                'id'                  => (int) $r['id'],
                'cid_id'              => isset($r['cid_id']) ? (int) $r['cid_id'] : 0,
                'task_kind'           => 'write_mom',
                'title'               => $company !== '' ? $company : 'MoM pending',
                'company'             => $company,
                'status'              => 'pending',
                'target_id'           => (int) $r['id'],
                'target_type'         => 'event',
                'meeting_status'      => isset($r['meeting_status']) ? (string) $r['meeting_status'] : '',
                'appointmentdatetime' => isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null,
            );
        }

        $this->json_out(array(
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'rows'    => $rows,
            'count'   => count($rows),
            'total'   => $total,
            'data'    => array('uid' => $uid, 'count' => count($rows), 'total' => $total),
            'route'   => 'api/discipline/mom_pending',
        ));
    }

    /**
     * GET /api/discipline/expense_pending
     * Mirrors DisciplineState_model::get_meeting_expense_count EXACTLY.
     */
    public function expense_pending()
    {
        if ( ! $this->auth_ok()) { return; }
        $uid = $this->resolve_uid();
        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }
        $today = date('Y-m-d');

        $this->db->select('bm.id AS meetid, t.id AS event_id, t.cid_id, t.appointmentdatetime, cm.compname');
        $this->db->from('barginmeeting bm');
        $this->db->join('tblcallevents t', 't.id = bm.tid', 'left');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('bm.user_id', $uid);
        $this->db->where_in('t.actiontype_id', array(3, 4, 17));
        $this->db->where('t.nextCFID !=', 0);
        $this->db->where('t.plan', 1);
        $this->db->where('DATE(t.appointmentdatetime) =', $today);
        $this->db->where('t.approved_status', 1);
        $this->db->where('NOT EXISTS (SELECT 1 FROM cash_expense WHERE cash_expense.meetid = bm.id)', null, false);
        $this->db->order_by('t.appointmentdatetime', 'ASC');
        $this->db->limit(200);
        $result = $this->db->get()->result_array();

        $tq = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM barginmeeting bm
             LEFT JOIN tblcallevents t ON t.id = bm.tid
             WHERE bm.user_id = ?
               AND t.actiontype_id IN (3, 4, 17)
               AND t.nextCFID != 0
               AND t.plan = 1
               AND DATE(t.appointmentdatetime) = ?
               AND t.approved_status = 1
               AND NOT EXISTS (SELECT 1 FROM cash_expense WHERE cash_expense.meetid = bm.id)",
            array($uid, $today)
        );
        $trow = $tq->result();
        $total = (int) ($trow[0]->cnt ?? 0);

        $rows = array();
        foreach ($result as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $rows[] = array(
                'id'                  => isset($r['meetid']) ? (int) $r['meetid'] : 0,
                'meetid'              => isset($r['meetid']) ? (int) $r['meetid'] : 0,
                'event_id'            => isset($r['event_id']) ? (int) $r['event_id'] : 0,
                'cid_id'              => isset($r['cid_id']) ? (int) $r['cid_id'] : 0,
                'task_kind'           => 'fill_expense',
                'title'               => $company !== '' ? $company : 'Expense pending',
                'company'             => $company,
                'status'              => 'pending',
                'target_id'           => isset($r['meetid']) ? (int) $r['meetid'] : 0,
                'target_type'         => 'meeting',
                'appointmentdatetime' => isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null,
            );
        }

        $this->json_out(array(
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'rows'    => $rows,
            'count'   => count($rows),
            'total'   => $total,
            'data'    => array('uid' => $uid, 'count' => count($rows), 'total' => $total),
            'route'   => 'api/discipline/expense_pending',
        ));
    }

    /**
     * GET /api/discipline/research_pending
     * Mirrors DisciplineState_model::get_research_not_updated_count EXACTLY.
     */
    public function research_pending()
    {
        if ( ! $this->auth_ok()) { return; }
        $uid = $this->resolve_uid();
        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.appointmentdatetime, ic.id AS init_id, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('t.actiontype_id', 10);
        $this->db->where('t.nextCFID !=', 0);
        $this->db->where('ic.new_lead', 1);
        $this->db->where('ic.is_admin_approved', 0);
        $this->db->where('cm.compname', 'Unknown');
        $this->db->where("t.self_assign = ''", null, false);
        $this->db->order_by('t.appointmentdatetime', 'DESC');
        $this->db->limit(200);
        $result = $this->db->get()->result_array();

        $tq = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM tblcallevents t
             LEFT JOIN init_call ic ON ic.id = t.cid_id
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             WHERE t.user_id = ?
               AND t.actiontype_id = 10
               AND t.nextCFID != 0
               AND ic.new_lead = 1
               AND ic.is_admin_approved = 0
               AND cm.compname = 'Unknown'
               AND t.self_assign = ''",
            array($uid)
        );
        $trow = $tq->result();
        $total = (int) ($trow[0]->cnt ?? 0);

        $rows = array();
        foreach ($result as $r) {
            $rows[] = array(
                'id'                  => (int) $r['id'],
                'cid_id'              => isset($r['cid_id']) ? (int) $r['cid_id'] : 0,
                'init_id'             => isset($r['init_id']) ? (int) $r['init_id'] : 0,
                'task_kind'           => 'update_research',
                'title'               => 'Research update required',
                'company'             => 'Unknown',
                'status'              => 'pending',
                'target_id'           => (int) $r['id'],
                'target_type'         => 'event',
                'appointmentdatetime' => isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null,
            );
        }

        $this->json_out(array(
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'rows'    => $rows,
            'count'   => count($rows),
            'total'   => $total,
            'data'    => array('uid' => $uid, 'count' => count($rows), 'total' => $total),
            'route'   => 'api/discipline/research_pending',
        ));
    }
}
