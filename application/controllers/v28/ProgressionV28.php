<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProgressionV28 Controller
 *
 * Handles lead cstatus progression analytics for STEM CRM v2.8.
 *
 * Routes served (group: progression):
 *   GET /api/progression/bd_score
 *   GET /api/progression/dropoff
 *   GET /api/progression/leads
 *   GET /api/progression/probe
 *   GET /api/progression/scorecard
 *   GET /api/progression/scores
 *   GET /api/progression/stats
 *   GET /api/progression/stuck_tasks
 *   GET /api/progression/yesterday
 *
 * Tables used:
 *   init_call            -- lead master (cstatus INT)
 *   lead_progression_log -- per-lead transition history
 *   bd_progression_daily -- daily BD roll-up
 *   stuck_leads_daily    -- pre-computed stuck leads (nightly cron)
 *   stuck_threshold      -- cstatus -> threshold days
 *   user                 -- BD name lookups
 *   tblcallevents        -- call event history
 *
 * cstatus enum: 1=Open, 2=Reachout, 3=Tentative, 6=Positive,
 *               8=Open RPEM, 9=Very Positive, 12=Won, 13=Lost
 *
 * Bearer token guard on every endpoint.
 */
class ProgressionV28 extends CI_Controller {

    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private function auth()
    {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        if (!$hdr || !preg_match('/^Bearer\s+(.+)$/i', $hdr, $m)) {
            return false;
        }
        return hash_equals(self::BEARER, trim($m[1]));
    }

    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function unauth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $this->json_out(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
    }

    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return date('Y-m-d');
    }

    private function cstatus_label($cs)
    {
        $map = [
            1  => 'Open',
            2  => 'Reachout',
            3  => 'Tentative',
            6  => 'Positive',
            8  => 'Open RPEM',
            9  => 'Very Positive',
            12 => 'Won',
            13 => 'Lost',
        ];
        return isset($map[(int)$cs]) ? $map[(int)$cs] : 'Unknown';
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * probe
     * GET /api/progression/probe
     * Health check.
     */
    public function probe()
    {
        $this->json_out(['ok' => true, 'success' => true, 'controller' => 'ProgressionV28']);
    }

    /**
     * bd_score
     * GET /api/progression/bd_score[?bd_uid=&date=]
     *
     * Returns bd_progression_daily row(s) for the given BD and date.
     * If bd_uid omitted, returns top 50 BDs for the date ordered by
     * leads_progressed_forward DESC.
     */
    public function bd_score()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $for_date = $this->resolve_date();
        $bd_uid   = $this->input->get('bd_uid');
        $bd_uid   = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;

        $this->db->select('b.bd_uid, u.name AS bd_name, b.record_date,
            b.leads_progressed_forward, b.leads_progressed_backward,
            b.leads_at_cstatus_6, b.leads_at_cstatus_8, b.leads_at_cstatus_9,
            b.leads_won, b.revenue_won_rs');
        $this->db->from('bd_progression_daily b');
        $this->db->join('user u', 'u.uid = b.bd_uid', 'left');
        $this->db->where('b.record_date', $for_date);
        if ($bd_uid) {
            $this->db->where('b.bd_uid', $bd_uid);
        }
        $this->db->order_by('b.leads_progressed_forward', 'DESC');
        $this->db->limit(50);

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$r) {
            $r['bd_uid']                  = (int) $r['bd_uid'];
            $r['leads_progressed_forward']  = (int) $r['leads_progressed_forward'];
            $r['leads_progressed_backward'] = (int) $r['leads_progressed_backward'];
            $r['leads_at_cstatus_6']        = (int) $r['leads_at_cstatus_6'];
            $r['leads_at_cstatus_8']        = (int) $r['leads_at_cstatus_8'];
            $r['leads_at_cstatus_9']        = (int) $r['leads_at_cstatus_9'];
            $r['leads_won']                 = (int) $r['leads_won'];
            $r['revenue_won_rs']            = (int) $r['revenue_won_rs'];
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * scores
     * GET /api/progression/scores[?date=]
     *
     * Returns bd_progression_daily scores for all BDs for the given date.
     * Sorted by leads_progressed_forward DESC.
     */
    public function scores()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $for_date = $this->resolve_date();

        $this->db->select('b.bd_uid, u.name AS bd_name, b.record_date,
            b.leads_progressed_forward, b.leads_progressed_backward,
            b.leads_at_cstatus_6, b.leads_at_cstatus_8, b.leads_at_cstatus_9,
            b.leads_won, b.revenue_won_rs');
        $this->db->from('bd_progression_daily b');
        $this->db->join('user u', 'u.uid = b.bd_uid', 'left');
        $this->db->where('b.record_date', $for_date);
        $this->db->order_by('b.leads_progressed_forward', 'DESC');
        $this->db->limit(100);

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$r) {
            $r['bd_uid']                    = (int) $r['bd_uid'];
            $r['leads_progressed_forward']  = (int) $r['leads_progressed_forward'];
            $r['leads_progressed_backward'] = (int) $r['leads_progressed_backward'];
            $r['leads_at_cstatus_6']        = (int) $r['leads_at_cstatus_6'];
            $r['leads_at_cstatus_8']        = (int) $r['leads_at_cstatus_8'];
            $r['leads_at_cstatus_9']        = (int) $r['leads_at_cstatus_9'];
            $r['leads_won']                 = (int) $r['leads_won'];
            $r['revenue_won_rs']            = (int) $r['revenue_won_rs'];
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * scorecard
     * GET /api/progression/scorecard[?bd_uid=&date=]
     *
     * Returns an enriched scorecard for a BD (or all BDs):
     *   - progression daily row
     *   - transitions breakdown (forward/backward counts by stage)
     *   - stuck count from stuck_leads_daily
     */
    public function scorecard()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $for_date = $this->resolve_date();
        $bd_uid   = $this->input->get('bd_uid');
        $bd_uid   = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;

        // Daily progression row
        $this->db->select('b.bd_uid, u.name AS bd_name,
            b.leads_progressed_forward, b.leads_progressed_backward,
            b.leads_at_cstatus_6, b.leads_at_cstatus_8, b.leads_at_cstatus_9,
            b.leads_won, b.revenue_won_rs');
        $this->db->from('bd_progression_daily b');
        $this->db->join('user u', 'u.uid = b.bd_uid', 'left');
        $this->db->where('b.record_date', $for_date);
        if ($bd_uid) { $this->db->where('b.bd_uid', $bd_uid); }
        $this->db->limit(50);
        $daily_rows = $this->db->get()->result_array();

        // Stuck count from stuck_leads_daily for the date
        $this->db->select('s.bd_uid, COUNT(*) AS stuck_count');
        $this->db->from('stuck_leads_daily s');
        $this->db->where('s.for_date', $for_date);
        if ($bd_uid) { $this->db->where('s.bd_uid', $bd_uid); }
        $this->db->group_by('s.bd_uid');
        $stuck_raw = $this->db->get()->result_array();
        $stuck_map = [];
        foreach ($stuck_raw as $sr) {
            $stuck_map[(int)$sr['bd_uid']] = (int)$sr['stuck_count'];
        }

        $cards = [];
        foreach ($daily_rows as $r) {
            $uid = (int) $r['bd_uid'];
            $cards[] = [
                'bd_uid'                    => $uid,
                'bd_name'                   => $r['bd_name'],
                'date'                      => $for_date,
                'leads_progressed_forward'  => (int) $r['leads_progressed_forward'],
                'leads_progressed_backward' => (int) $r['leads_progressed_backward'],
                'leads_at_cstatus_6'        => (int) $r['leads_at_cstatus_6'],
                'leads_at_cstatus_8'        => (int) $r['leads_at_cstatus_8'],
                'leads_at_cstatus_9'        => (int) $r['leads_at_cstatus_9'],
                'leads_won'                 => (int) $r['leads_won'],
                'revenue_won_rs'            => (int) $r['revenue_won_rs'],
                'stuck_count'               => isset($stuck_map[$uid]) ? $stuck_map[$uid] : 0,
            ];
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $cards,
            'count'   => count($cards),
        ]);
    }

    /**
     * stats
     * GET /api/progression/stats[?date=]
     *
     * Returns aggregate cstatus distribution across all active leads,
     * plus totals from bd_progression_daily for the date.
     */
    public function stats()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $for_date = $this->resolve_date();

        // cstatus distribution from init_call
        $this->db->select('cstatus, COUNT(*) AS cnt');
        $this->db->from('init_call');
        $this->db->where_in('cstatus', [1, 2, 3, 6, 8, 9, 12, 13]);
        $this->db->group_by('cstatus');
        $this->db->order_by('cstatus', 'ASC');
        $dist_raw = $this->db->get()->result_array();

        $distribution = [];
        foreach ($dist_raw as $d) {
            $distribution[] = [
                'cstatus' => (int) $d['cstatus'],
                'label'   => $this->cstatus_label($d['cstatus']),
                'count'   => (int) $d['cnt'],
            ];
        }

        // Daily totals for the date
        $this->db->select('
            SUM(leads_progressed_forward)  AS total_forward,
            SUM(leads_progressed_backward) AS total_backward,
            SUM(leads_won)                 AS total_won,
            SUM(revenue_won_rs)            AS total_revenue_rs,
            COUNT(DISTINCT bd_uid)         AS bd_count');
        $this->db->from('bd_progression_daily');
        $this->db->where('record_date', $for_date);
        $totals = $this->db->get()->row_array();

        // Stuck leads count for the date
        $stuck_count = (int) $this->db->query(
            "SELECT COUNT(*) AS cnt FROM stuck_leads_daily WHERE for_date = ?",
            [$for_date]
        )->row()->cnt;

        $this->json_out([
            'ok'           => true,
            'success'      => true,
            'date'         => $for_date,
            'distribution' => $distribution,
            'totals'       => [
                'total_forward'    => (int) ($totals['total_forward'] ?? 0),
                'total_backward'   => (int) ($totals['total_backward'] ?? 0),
                'total_won'        => (int) ($totals['total_won'] ?? 0),
                'total_revenue_rs' => (int) ($totals['total_revenue_rs'] ?? 0),
                'bd_count'         => (int) ($totals['bd_count'] ?? 0),
            ],
            'stuck_count'  => $stuck_count,
        ]);
    }

    /**
     * leads
     * GET /api/progression/leads[?bd_uid=&cstatus=&limit=]
     *
     * Returns leads from init_call with recent progression log info.
     * Optional filters: bd_uid, cstatus.
     */
    public function leads()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $bd_uid  = $this->input->get('bd_uid');
        $bd_uid  = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;
        $cstatus = $this->input->get('cstatus');
        $cstatus = ($cstatus !== false && $cstatus !== null && $cstatus !== '') ? (int)$cstatus : null;
        $limit   = min((int)($this->input->get('limit') ?: 50), 100);

        $this->db->select('i.id AS lead_id, i.cstatus, i.mainbd AS bd_uid,
            u.name AS bd_name, c.compname AS company_name,
            i.createDate, i.updated_at, i.fbudget');
        $this->db->from('init_call i');
        $this->db->join('company_master c', 'c.id = i.cmpid_id', 'left');
        $this->db->join('user u', 'u.uid = i.mainbd', 'left');

        if ($bd_uid)  { $this->db->where('i.mainbd', $bd_uid); }
        if ($cstatus !== null) { $this->db->where('i.cstatus', $cstatus); }
        $this->db->where_in('i.cstatus', [1, 2, 3, 6, 8, 9, 12, 13]);
        $this->db->order_by('i.updated_at', 'DESC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$r) {
            $r['lead_id'] = (int) $r['lead_id'];
            $r['cstatus'] = (int) $r['cstatus'];
            $r['bd_uid']  = (int) $r['bd_uid'];
            $r['cstatus_label'] = $this->cstatus_label($r['cstatus']);
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * dropoff
     * GET /api/progression/dropoff[?date=]
     *
     * Returns leads that progressed backward (regression) in the lead_progression_log
     * for the given date. Highlights cstatus drops.
     */
    public function dropoff()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $for_date = $this->resolve_date();

        $this->db->select('l.id, l.lead_id, l.bd_uid,
            u.name AS bd_name,
            c.compname AS company_name,
            l.from_status, l.to_status,
            l.progression_type, l.triggered_by,
            l.notes, l.created_at');
        $this->db->from('lead_progression_log l');
        $this->db->join('init_call i', 'i.id = l.lead_id', 'left');
        $this->db->join('company_master c', 'c.id = i.cmpid_id', 'left');
        $this->db->join('user u', 'u.uid = l.bd_uid', 'left');
        $this->db->where('l.progression_type', 'backward');
        $this->db->where('DATE(l.created_at)', $for_date);
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit(100);

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$r) {
            $r['id']          = (int) $r['id'];
            $r['lead_id']     = (int) $r['lead_id'];
            $r['bd_uid']      = (int) $r['bd_uid'];
            $r['from_status'] = (int) $r['from_status'];
            $r['to_status']   = (int) $r['to_status'];
            $r['from_label']  = $this->cstatus_label($r['from_status']);
            $r['to_label']    = $this->cstatus_label($r['to_status']);
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * stuck_tasks
     * GET /api/progression/stuck_tasks[?bd_uid=&date=]
     *
     * Returns stuck_leads_daily rows for the given date.
     * Includes threshold info and days overage.
     */
    public function stuck_tasks()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $for_date = $this->resolve_date();
        $bd_uid   = $this->input->get('bd_uid');
        $bd_uid   = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;

        $this->db->select('s.id, s.for_date, s.cid_id AS lead_id,
            s.bd_uid, u.name AS bd_name,
            c.compname AS company_name,
            s.cstatus, s.days_in_stage,
            s.threshold_days, s.last_touch_date,
            (s.days_in_stage - s.threshold_days) AS overage_days');
        $this->db->from('stuck_leads_daily s');
        $this->db->join('init_call i', 'i.id = s.cid_id', 'left');
        $this->db->join('company_master c', 'c.id = i.cmpid_id', 'left');
        $this->db->join('user u', 'u.uid = s.bd_uid', 'left');
        $this->db->where('s.for_date', $for_date);
        if ($bd_uid) { $this->db->where('s.bd_uid', $bd_uid); }
        $this->db->order_by('s.days_in_stage', 'DESC');
        $this->db->limit(100);

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$r) {
            $r['lead_id']       = (int) $r['lead_id'];
            $r['bd_uid']        = (int) $r['bd_uid'];
            $r['cstatus']       = (int) $r['cstatus'];
            $r['cstatus_label'] = $this->cstatus_label($r['cstatus']);
            $r['days_in_stage'] = (int) $r['days_in_stage'];
            $r['threshold_days']= (int) $r['threshold_days'];
            $r['overage_days']  = (int) $r['overage_days'];
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * yesterday
     * GET /api/progression/yesterday[?bd_uid=]
     *
     * Returns bd_progression_daily rows for yesterday.
     */
    public function yesterday()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $bd_uid    = $this->input->get('bd_uid');
        $bd_uid    = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;

        $this->db->select('b.bd_uid, u.name AS bd_name, b.record_date,
            b.leads_progressed_forward, b.leads_progressed_backward,
            b.leads_at_cstatus_6, b.leads_at_cstatus_8, b.leads_at_cstatus_9,
            b.leads_won, b.revenue_won_rs');
        $this->db->from('bd_progression_daily b');
        $this->db->join('user u', 'u.uid = b.bd_uid', 'left');
        $this->db->where('b.record_date', $yesterday);
        if ($bd_uid) { $this->db->where('b.bd_uid', $bd_uid); }
        $this->db->order_by('b.leads_progressed_forward', 'DESC');
        $this->db->limit(100);

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$r) {
            $r['bd_uid']                    = (int) $r['bd_uid'];
            $r['leads_progressed_forward']  = (int) $r['leads_progressed_forward'];
            $r['leads_progressed_backward'] = (int) $r['leads_progressed_backward'];
            $r['leads_at_cstatus_6']        = (int) $r['leads_at_cstatus_6'];
            $r['leads_at_cstatus_8']        = (int) $r['leads_at_cstatus_8'];
            $r['leads_at_cstatus_9']        = (int) $r['leads_at_cstatus_9'];
            $r['leads_won']                 = (int) $r['leads_won'];
            $r['revenue_won_rs']            = (int) $r['revenue_won_rs'];
        }
        unset($r);

        $this->json_out([
            'ok'       => true,
            'success'  => true,
            'date'     => $yesterday,
            'rows'     => $rows,
            'count'    => count($rows),
        ]);
    }
}
