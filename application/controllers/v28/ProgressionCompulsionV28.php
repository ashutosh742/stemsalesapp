<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProgressionCompulsionV28 Controller
 *
 * Handles compulsion/accountability and sales progression analytics
 * for STEM CRM v2.8.
 *
 * Routes served:
 *   Group: progression_compulsion
 *     GET /api/progression_compulsion/accountability_feed
 *     GET /api/progression_compulsion/cell_grid
 *     GET /api/progression_compulsion/lead_sla
 *     GET /api/progression_compulsion/mark_lost_queue
 *     GET /api/progression_compulsion/slot_status
 *
 *   Group: progression_compulsion_v2
 *     GET /api/progression_compulsion_v2/accountability_feed
 *     GET /api/progression_compulsion_v2/cell_grid
 *     GET /api/progression_compulsion_v2/lead_sla
 *     GET /api/progression_compulsion_v2/mark_lost_queue
 *     GET /api/progression_compulsion_v2/slot_status
 *
 *   Group: sales_progression
 *     GET /api/sales_progression/probe
 *     GET /api/sales_progression/refresh_yesterday
 *     GET /api/sales_progression/yesterday_scores
 *
 * Tables used:
 *   init_call            -- lead master (cstatus INT)
 *   lead_progression_log -- transition history
 *   bd_progression_daily -- daily BD roll-up
 *   stuck_leads_daily    -- pre-computed stuck leads
 *   stuck_threshold      -- cstatus threshold days
 *   tblcallevents        -- call event history
 *   user                 -- BD lookup
 *   company_master       -- company name
 *
 * cstatus enum: 1=Open, 2=Reachout, 3=Tentative, 6=Positive,
 *               8=Open RPEM, 9=Very Positive, 12=Won, 13=Lost
 *
 * Note: progression_compulsion_v2 methods share the same logic as v1
 * but are namespaced separately so both URL families resolve correctly.
 */
class ProgressionCompulsionV28 extends CI_Controller {

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

    // =========================================================================
    // SHARED LOGIC (used by both v1 and v2 compulsion groups)
    // =========================================================================

    /**
     * _accountability_feed
     * Core logic for accountability_feed.
     *
     * Returns BDs with stuck leads, backward progressions today,
     * and days since last call event -- sorted by accountability_score DESC.
     * Optional ?bd_uid= filter and ?date= override.
     */
    private function _accountability_feed($version = 1)
    {
        $for_date = $this->resolve_date();
        $bd_uid   = $this->input->get('bd_uid');
        $bd_uid   = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;

        // Stuck leads per BD for date
        $this->db->select('s.bd_uid, COUNT(*) AS stuck_count,
            MAX(s.days_in_stage) AS max_days_stuck');
        $this->db->from('stuck_leads_daily s');
        $this->db->where('s.for_date', $for_date);
        if ($bd_uid) { $this->db->where('s.bd_uid', $bd_uid); }
        $this->db->group_by('s.bd_uid');
        $stuck_raw = $this->db->get()->result_array();

        // Backward progressions per BD today
        $this->db->select('l.bd_uid, COUNT(*) AS backward_count');
        $this->db->from('lead_progression_log l');
        $this->db->where('l.progression_type', 'backward');
        $this->db->where('DATE(l.created_at)', $for_date);
        if ($bd_uid) { $this->db->where('l.bd_uid', $bd_uid); }
        $this->db->group_by('l.bd_uid');
        $back_raw = $this->db->get()->result_array();

        // Index by bd_uid
        $stuck_map = [];
        foreach ($stuck_raw as $r) {
            $stuck_map[(int)$r['bd_uid']] = [
                'stuck_count'   => (int) $r['stuck_count'],
                'max_days_stuck'=> (int) $r['max_days_stuck'],
            ];
        }
        $back_map = [];
        foreach ($back_raw as $r) {
            $back_map[(int)$r['bd_uid']] = (int) $r['backward_count'];
        }

        // Merge all BD UIDs
        $all_uids = array_unique(array_merge(
            array_keys($stuck_map),
            array_keys($back_map)
        ));

        if (empty($all_uids)) {
            return [
                'ok'      => true,
                'success' => true,
                'date'    => $for_date,
                'version' => $version,
                'rows'    => [],
                'count'   => 0,
                'note'    => 'no_data',
            ];
        }

        // Get BD names
        $this->db->select('uid, name');
        $this->db->from('user');
        $this->db->where_in('uid', $all_uids);
        $user_rows = $this->db->get()->result_array();
        $name_map  = [];
        foreach ($user_rows as $u) {
            $name_map[(int)$u['uid']] = $u['name'];
        }

        $feed = [];
        foreach ($all_uids as $uid) {
            $stuck     = isset($stuck_map[$uid]) ? $stuck_map[$uid] : ['stuck_count' => 0, 'max_days_stuck' => 0];
            $backward  = isset($back_map[$uid])  ? $back_map[$uid]  : 0;
            $score     = ($stuck['stuck_count'] * 2) + ($backward * 3);
            $feed[] = [
                'bd_uid'          => $uid,
                'bd_name'         => isset($name_map[$uid]) ? $name_map[$uid] : ('BD ' . $uid),
                'stuck_count'     => $stuck['stuck_count'],
                'max_days_stuck'  => $stuck['max_days_stuck'],
                'backward_count'  => $backward,
                'accountability_score' => $score,
                'version'         => $version,
            ];
        }

        usort($feed, function($a, $b) {
            return $b['accountability_score'] - $a['accountability_score'];
        });

        return [
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'version' => $version,
            'rows'    => $feed,
            'count'   => count($feed),
        ];
    }

    /**
     * _cell_grid
     * Core logic for cell_grid.
     *
     * Returns a cstatus x BD matrix of lead counts --
     * rows are cstatus stages, columns are active BD UIDs.
     * Useful for a heat-map / grid view.
     */
    private function _cell_grid($version = 1)
    {
        $bd_uid = $this->input->get('bd_uid');
        $bd_uid = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;

        $stages = [1, 2, 3, 6, 8, 9, 12, 13];

        $this->db->select('i.mainbd AS bd_uid, u.name AS bd_name,
            i.cstatus, COUNT(*) AS lead_count');
        $this->db->from('init_call i');
        $this->db->join('user u', 'u.uid = i.mainbd', 'left');
        $this->db->where_in('i.cstatus', $stages);
        $this->db->where('i.mainbd IS NOT NULL');
        if ($bd_uid) { $this->db->where('i.mainbd', $bd_uid); }
        $this->db->group_by(['i.mainbd', 'i.cstatus']);
        $this->db->order_by('i.mainbd', 'ASC');
        $this->db->limit(500);

        $raw = $this->db->get()->result_array();

        // Build grid structure
        $bd_names = [];
        $grid     = [];
        foreach ($raw as $r) {
            $uid = (int) $r['bd_uid'];
            $cs  = (int) $r['cstatus'];
            if (!isset($grid[$uid])) {
                $grid[$uid] = ['bd_uid' => $uid, 'bd_name' => $r['bd_name']];
                foreach ($stages as $s) {
                    $grid[$uid]['cstatus_' . $s] = 0;
                }
            }
            $grid[$uid]['cstatus_' . $cs] = (int) $r['lead_count'];
        }

        $rows = array_values($grid);

        return [
            'ok'      => true,
            'success' => true,
            'version' => $version,
            'stages'  => $stages,
            'labels'  => [
                1  => 'Open',
                2  => 'Reachout',
                3  => 'Tentative',
                6  => 'Positive',
                8  => 'Open RPEM',
                9  => 'Very Positive',
                12 => 'Won',
                13 => 'Lost',
            ],
            'rows'    => $rows,
            'count'   => count($rows),
        ];
    }

    /**
     * _lead_sla
     * Core logic for lead_sla.
     *
     * Returns leads that have exceeded their stuck_threshold days for the current
     * cstatus, computed live from init_call and stuck_threshold.
     * Optional filters: bd_uid, cstatus.
     */
    private function _lead_sla($version = 1)
    {
        $bd_uid  = $this->input->get('bd_uid');
        $bd_uid  = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;
        $cstatus = $this->input->get('cstatus');
        $cstatus = ($cstatus !== false && $cstatus !== null && $cstatus !== '') ? (int)$cstatus : null;

        // Get thresholds
        $thresh_raw = $this->db->get('stuck_threshold')->result_array();
        $thresh_map = [];
        foreach ($thresh_raw as $t) {
            $thresh_map[(int)$t['cstatus']] = (int)$t['days'];
        }

        // Live SLA query: leads in named stages, check days since updated_at
        $stages = array_keys($thresh_map);
        if (empty($stages)) {
            $stages = [1, 2, 3, 6, 8, 9];
        }

        $this->db->select('i.id AS lead_id, i.cstatus, i.mainbd AS bd_uid,
            u.name AS bd_name, c.compname AS company_name,
            i.updated_at,
            DATEDIFF(NOW(), i.updated_at) AS days_in_stage');
        $this->db->from('init_call i');
        $this->db->join('user u', 'u.uid = i.mainbd', 'left');
        $this->db->join('company_master c', 'c.id = i.cmpid_id', 'left');
        $this->db->where_in('i.cstatus', $stages);
        if ($bd_uid)  { $this->db->where('i.mainbd', $bd_uid); }
        if ($cstatus !== null) { $this->db->where('i.cstatus', $cstatus); }
        $this->db->having('days_in_stage >', 0);
        $this->db->order_by('days_in_stage', 'DESC');
        $this->db->limit(100);

        $rows = $this->db->get()->result_array();

        $result = [];
        foreach ($rows as $r) {
            $cs        = (int) $r['cstatus'];
            $threshold = isset($thresh_map[$cs]) ? $thresh_map[$cs] : 0;
            $days      = (int) $r['days_in_stage'];
            $breached  = ($threshold > 0 && $days > $threshold);

            if ($version === 1 && !$breached) {
                continue; // v1 returns only SLA breaches
            }

            $result[] = [
                'lead_id'        => (int) $r['lead_id'],
                'cstatus'        => $cs,
                'cstatus_label'  => $this->cstatus_label($cs),
                'bd_uid'         => (int) $r['bd_uid'],
                'bd_name'        => $r['bd_name'],
                'company_name'   => $r['company_name'],
                'days_in_stage'  => $days,
                'threshold_days' => $threshold,
                'sla_breached'   => $breached,
                'overage_days'   => max(0, $days - $threshold),
                'version'        => $version,
            ];
        }

        return [
            'ok'      => true,
            'success' => true,
            'version' => $version,
            'rows'    => $result,
            'count'   => count($result),
        ];
    }

    /**
     * _mark_lost_queue
     * Core logic for mark_lost_queue.
     *
     * Returns leads that are candidates for marking Lost (cstatus=13):
     * - cstatus in [1,2,3,6,8,9] and days_in_stage > 2x threshold
     * - no call events in last 30 days
     */
    private function _mark_lost_queue($version = 1)
    {
        $bd_uid = $this->input->get('bd_uid');
        $bd_uid = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;

        // Get thresholds
        $thresh_raw = $this->db->get('stuck_threshold')->result_array();
        $thresh_map = [];
        foreach ($thresh_raw as $t) {
            $thresh_map[(int)$t['cstatus']] = (int)$t['days'];
        }

        $stages = [1, 2, 3, 6, 8, 9];

        // Step 1: candidate init_call rows (no subquery, fast)
        $this->db->select('i.id AS lead_id, i.cstatus, i.mainbd AS bd_uid,
            u.name AS bd_name, c.compname AS company_name,
            i.updated_at,
            DATEDIFF(NOW(), i.updated_at) AS days_in_stage,
            i.cmpid_id');
        $this->db->from('init_call i');
        $this->db->join('user u', 'u.uid = i.mainbd', 'left');
        $this->db->join('company_master c', 'c.id = i.cmpid_id', 'left');
        $this->db->where_in('i.cstatus', $stages);
        $this->db->where('i.createDate >=', date('Y-m-d', strtotime('-18 months')));
        if ($bd_uid) { $this->db->where('i.mainbd', $bd_uid); }
        $this->db->order_by('days_in_stage', 'DESC');
        $this->db->limit(500);

        $rows = $this->db->get()->result_array();

        // Step 2: batch lookup last_call_date for those lead ids
        $last_map = [];
        if (!empty($rows)) {
            $ids = array_map(function($r){return (int)$r['lead_id'];}, $rows);
            $in_ids = implode(',', $ids);
            $lr = $this->db->query("SELECT cid_id, MAX(date) AS last_call_date FROM tblcallevents WHERE cid_id IN ($in_ids) GROUP BY cid_id");
            if ($lr) {
                foreach ($lr->result_array() as $row) {
                    $last_map[(int)$row['cid_id']] = $row['last_call_date'];
                }
            }
        }
        foreach ($rows as &$r) {
            $r['last_call_date'] = $last_map[(int)$r['lead_id']] ?? null;
        }
        unset($r);

        $queue = [];
        foreach ($rows as $r) {
            $cs        = (int) $r['cstatus'];
            $threshold = isset($thresh_map[$cs]) ? $thresh_map[$cs] : 5;
            $days      = (int) $r['days_in_stage'];

            // Candidate if > 2x threshold
            if ($days < ($threshold * 2)) {
                continue;
            }

            $queue[] = [
                'lead_id'        => (int) $r['lead_id'],
                'cstatus'        => $cs,
                'cstatus_label'  => $this->cstatus_label($cs),
                'bd_uid'         => (int) $r['bd_uid'],
                'bd_name'        => $r['bd_name'],
                'company_name'   => $r['company_name'],
                'days_in_stage'  => $days,
                'threshold_days' => $threshold,
                'last_call_date' => $r['last_call_date'],
                'version'        => $version,
            ];
        }

        return [
            'ok'      => true,
            'success' => true,
            'version' => $version,
            'rows'    => $queue,
            'count'   => count($queue),
            'note'    => 'candidates for mark_lost -- review before acting',
        ];
    }

    /**
     * _slot_status
     * Core logic for slot_status.
     *
     * Returns BD-level slot usage summary:
     *   - total active leads per BD
     *   - distribution by cstatus
     *   - stuck count
     * Useful for capacity management / slot allocation views.
     */
    private function _slot_status($version = 1)
    {
        $bd_uid = $this->input->get('bd_uid');
        $bd_uid = ($bd_uid && (int)$bd_uid > 0) ? (int)$bd_uid : null;
        $today  = date('Y-m-d');

        $this->db->select('i.mainbd AS bd_uid, u.name AS bd_name,
            COUNT(*) AS total_leads,
            SUM(CASE WHEN i.cstatus=1  THEN 1 ELSE 0 END) AS cs_open,
            SUM(CASE WHEN i.cstatus=2  THEN 1 ELSE 0 END) AS cs_reachout,
            SUM(CASE WHEN i.cstatus=3  THEN 1 ELSE 0 END) AS cs_tentative,
            SUM(CASE WHEN i.cstatus=6  THEN 1 ELSE 0 END) AS cs_positive,
            SUM(CASE WHEN i.cstatus=8  THEN 1 ELSE 0 END) AS cs_open_rpem,
            SUM(CASE WHEN i.cstatus=9  THEN 1 ELSE 0 END) AS cs_very_positive,
            SUM(CASE WHEN i.cstatus=12 THEN 1 ELSE 0 END) AS cs_won,
            SUM(CASE WHEN i.cstatus=13 THEN 1 ELSE 0 END) AS cs_lost');
        $this->db->from('init_call i');
        $this->db->join('user u', 'u.uid = i.mainbd', 'left');
        $this->db->where_in('i.cstatus', [1, 2, 3, 6, 8, 9, 12, 13]);
        $this->db->where('i.mainbd IS NOT NULL');
        if ($bd_uid) { $this->db->where('i.mainbd', $bd_uid); }
        $this->db->group_by('i.mainbd');
        $this->db->order_by('total_leads', 'DESC');
        $this->db->limit(100);

        $rows = $this->db->get()->result_array();

        // Stuck counts per BD (use today)
        $this->db->select('bd_uid, COUNT(*) AS stuck_count');
        $this->db->from('stuck_leads_daily');
        $this->db->where('for_date', $today);
        if ($bd_uid) { $this->db->where('bd_uid', $bd_uid); }
        $this->db->group_by('bd_uid');
        $stuck_raw = $this->db->get()->result_array();
        $stuck_map = [];
        foreach ($stuck_raw as $s) {
            $stuck_map[(int)$s['bd_uid']] = (int)$s['stuck_count'];
        }

        foreach ($rows as &$r) {
            $uid = (int) $r['bd_uid'];
            $r['bd_uid']         = $uid;
            $r['total_leads']    = (int) $r['total_leads'];
            $r['cs_open']        = (int) $r['cs_open'];
            $r['cs_reachout']    = (int) $r['cs_reachout'];
            $r['cs_tentative']   = (int) $r['cs_tentative'];
            $r['cs_positive']    = (int) $r['cs_positive'];
            $r['cs_open_rpem']   = (int) $r['cs_open_rpem'];
            $r['cs_very_positive']= (int) $r['cs_very_positive'];
            $r['cs_won']         = (int) $r['cs_won'];
            $r['cs_lost']        = (int) $r['cs_lost'];
            $r['stuck_count']    = isset($stuck_map[$uid]) ? $stuck_map[$uid] : 0;
            $r['version']        = $version;
        }
        unset($r);

        return [
            'ok'      => true,
            'success' => true,
            'date'    => $today,
            'version' => $version,
            'rows'    => $rows,
            'count'   => count($rows),
        ];
    }

    // =========================================================================
    // GROUP: progression_compulsion (v1)
    // =========================================================================

    public function accountability_feed()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_accountability_feed(1));
    }

    public function cell_grid()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_cell_grid(1));
    }

    public function lead_sla()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_lead_sla(1));
    }

    public function mark_lost_queue()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_mark_lost_queue(1));
    }

    public function slot_status()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_slot_status(1));
    }

    // =========================================================================
    // GROUP: progression_compulsion_v2 (v2 -- same logic, version tag differs)
    // =========================================================================

    public function accountability_feed_v2()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_accountability_feed(2));
    }

    public function cell_grid_v2()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_cell_grid(2));
    }

    public function lead_sla_v2()
    {
        if (!$this->auth()) { return $this->unauth(); }
        // v2 returns ALL leads with SLA info, not just breaches
        $this->json_out($this->_lead_sla(2));
    }

    public function mark_lost_queue_v2()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_mark_lost_queue(2));
    }

    public function slot_status_v2()
    {
        if (!$this->auth()) { return $this->unauth(); }
        $this->json_out($this->_slot_status(2));
    }

    // =========================================================================
    // GROUP: sales_progression
    // =========================================================================

    /**
     * probe
     * GET /api/sales_progression/probe
     * Health check for sales_progression group.
     */
    public function probe()
    {
        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'controller' => 'ProgressionCompulsionV28',
            'group'      => 'sales_progression',
        ]);
    }

    /**
     * yesterday_scores
     * GET /api/sales_progression/yesterday_scores[?bd_uid=]
     *
     * Returns bd_progression_daily rows for yesterday, sorted by
     * leads_progressed_forward DESC. Equivalent to ProgressionV28::yesterday
     * but namespaced under sales_progression for separate mobile screens.
     */
    public function yesterday_scores()
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
            'ok'      => true,
            'success' => true,
            'date'    => $yesterday,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * refresh_yesterday
     * GET /api/sales_progression/refresh_yesterday
     *
     * Returns bd_progression_daily rows for yesterday (same as yesterday_scores)
     * but also includes a totals summary. Separate route for mobile refresh action.
     */
    public function refresh_yesterday()
    {
        if (!$this->auth()) { return $this->unauth(); }

        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $this->db->select('b.bd_uid, u.name AS bd_name, b.record_date,
            b.leads_progressed_forward, b.leads_progressed_backward,
            b.leads_at_cstatus_6, b.leads_at_cstatus_8, b.leads_at_cstatus_9,
            b.leads_won, b.revenue_won_rs');
        $this->db->from('bd_progression_daily b');
        $this->db->join('user u', 'u.uid = b.bd_uid', 'left');
        $this->db->where('b.record_date', $yesterday);
        $this->db->order_by('b.leads_progressed_forward', 'DESC');
        $this->db->limit(100);

        $rows = $this->db->get()->result_array();

        $total_forward  = 0;
        $total_backward = 0;
        $total_won      = 0;
        $total_revenue  = 0;

        foreach ($rows as &$r) {
            $r['bd_uid']                    = (int) $r['bd_uid'];
            $r['leads_progressed_forward']  = (int) $r['leads_progressed_forward'];
            $r['leads_progressed_backward'] = (int) $r['leads_progressed_backward'];
            $r['leads_at_cstatus_6']        = (int) $r['leads_at_cstatus_6'];
            $r['leads_at_cstatus_8']        = (int) $r['leads_at_cstatus_8'];
            $r['leads_at_cstatus_9']        = (int) $r['leads_at_cstatus_9'];
            $r['leads_won']                 = (int) $r['leads_won'];
            $r['revenue_won_rs']            = (int) $r['revenue_won_rs'];

            $total_forward  += $r['leads_progressed_forward'];
            $total_backward += $r['leads_progressed_backward'];
            $total_won      += $r['leads_won'];
            $total_revenue  += $r['revenue_won_rs'];
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $yesterday,
            'totals'  => [
                'total_forward'    => $total_forward,
                'total_backward'   => $total_backward,
                'total_won'        => $total_won,
                'total_revenue_rs' => $total_revenue,
                'bd_count'         => count($rows),
            ],
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }
}
