<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BlitzManager_api
 * Endpoints:
 *   GET /api/line_manager/leaderboard?period=week
 *   GET /api/manager_incentive/this_week?uid={uid}
 *
 * Line Manager Leaderboard:
 *   Ranks active CM (type_id=13) and RM (type_id=22) managers.
 *   Primary sort key: manager_incentive.grade for current week.
 *   Fallback: cm_productivity_daily avg score_pct last 7 days.
 *
 * Manager Incentive This Week:
 *   Reads manager_incentive WHERE manager_uid=uid AND incentive_week=this_monday.
 *   Falls back to computing from mom_data approvals and positive conversions.
 *
 * manager_incentive columns: id, manager_uid, manager_role, incentive_week,
 *   gross_rs, deduction_rs, net_rs, grade, payout_status, approved_by_uid,
 *   approved_at, created_at, updated_at
 *
 * Routes:
 *   routes_blitz_30may_d.php -> BlitzManager_api/leaderboard
 *   routes_additions.php    -> ManagerIncentiveController/this_week
 *   (This file serves both via class alias at bottom)
 */
class BlitzManager_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected) {
            $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        if (!hash_equals($expected, $tok)) {
            $this->output->set_status_header(200)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'bad_token')));
            return false;
        }
        return true;
    }

    private function _json($payload) {
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function _week_start() {
        return date('Y-m-d', strtotime('monday this week'));
    }

    private function _grade_order($grade) {
        $map = array('A+' => 0, 'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4);
        return isset($map[$grade]) ? $map[$grade] : 5;
    }

    private function _pct_to_grade($pct) {
        $p = (float) $pct;
        if ($p >= 85) return 'A+';
        if ($p >= 70) return 'A';
        if ($p >= 55) return 'B';
        if ($p >= 40) return 'C';
        return 'D';
    }

    /**
     * GET /api/line_manager/leaderboard?period=week
     */
    public function leaderboard() {
        if (!$this->_bearer()) return;

        $period     = $this->input->get('period') ?: 'week';
        $week_start = $this->_week_start();

        $managers = $this->db->select('uid, name, type_id')
            ->from('user')
            ->where_in('type_id', array(13, 22))
            ->where('active', 1)
            ->get()->result();

        if (empty($managers)) {
            return $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0, 'period' => $period, 'week_start' => $week_start),
                'route'        => 'api/line_manager/leaderboard',
                'generated_at' => date('c'),
            ));
        }

        // Load current week incentive rows
        $incentive_rows = $this->db->select('manager_uid, grade, net_rs, gross_rs, payout_status')
            ->where('incentive_week', $week_start)
            ->get('manager_incentive')->result();
        $incentive_map = array();
        foreach ($incentive_rows as $ir) {
            $incentive_map[(int)$ir->manager_uid] = $ir;
        }

        // Load cm_productivity_daily avg for fallback
        $seven_days_ago = date('Y-m-d', strtotime('-7 days'));
        $prod_rows = $this->db->select('cm_uid, AVG(score_pct) AS avg_score_pct')
            ->from('cm_productivity_daily')
            ->where('for_date >=', $seven_days_ago)
            ->group_by('cm_uid')
            ->get()->result();
        $prod_map = array();
        foreach ($prod_rows as $pr) {
            $prod_map[(int)$pr->cm_uid] = (float)$pr->avg_score_pct;
        }

        $rows = array();
        foreach ($managers as $mgr) {
            $uid = (int)$mgr->uid;
            if (isset($incentive_map[$uid])) {
                $ir    = $incentive_map[$uid];
                $grade = $ir->grade ?: 'B';
                $net_rs = (float)$ir->net_rs;
                $avg_score_pct = isset($prod_map[$uid]) ? $prod_map[$uid] : 0;
                $source = 'manager_incentive';
            } else {
                $avg_score_pct = isset($prod_map[$uid]) ? $prod_map[$uid] : 0;
                $grade = $this->_pct_to_grade($avg_score_pct);
                $net_rs = 0;
                $source = 'cm_productivity_daily';
            }
            $rows[] = array(
                'uid'           => $uid,
                'name'          => $mgr->name,
                'type_id'       => (int)$mgr->type_id,
                'grade'         => $grade,
                'net_rs'        => $net_rs,
                'avg_score_pct' => round($avg_score_pct, 2),
                'grade_order'   => $this->_grade_order($grade),
                'source'        => $source,
            );
        }

        usort($rows, function($a, $b) {
            if ($a['grade_order'] !== $b['grade_order']) return $a['grade_order'] - $b['grade_order'];
            if ($b['net_rs'] !== $a['net_rs']) return (int)($b['net_rs'] - $a['net_rs']);
            return (int)($b['avg_score_pct'] - $a['avg_score_pct']);
        });

        $rank = 1;
        foreach ($rows as &$r) {
            $r['rank'] = $rank++;
            unset($r['grade_order']);
        }
        unset($r);

        return $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array('count' => count($rows), 'period' => $period, 'week_start' => $week_start),
            'route'        => 'api/line_manager/leaderboard',
            'generated_at' => date('c'),
        ));
    }

    /**
     * GET /api/manager_incentive/this_week?uid={uid}
     */
    public function this_week() {
        if (!$this->_bearer()) return;

        $uid        = (int) $this->input->get('uid');
        $week_start = $this->_week_start();

        if ($uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'uid required'));
            return;
        }

        // Try manager_incentive table first
        $row = $this->db->query(
            "SELECT gross_rs, deduction_rs, net_rs, grade, payout_status,
                    incentive_week, created_at
             FROM manager_incentive
             WHERE manager_uid = ?
               AND incentive_week = ?
             LIMIT 1",
            array($uid, $week_start)
        )->row_array();

        if ($row) {
            return $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array($row),
                'data'         => array('count' => 1, 'uid' => $uid, 'week_start' => $week_start),
                'route'        => 'api/manager_incentive/this_week',
                'generated_at' => date('c'),
            ));
        }

        // Fallback: compute from mom_data approvals
        $mom_row = $this->db->query(
            "SELECT COUNT(*) AS approved_count
             FROM mom_data
             WHERE approved_by = ?
               AND approved_status = 'Approved'
               AND DATE(cdate) >= ?",
            array((string)$uid, $week_start)
        )->row_array();
        $approved_moms = (int)($mom_row ? $mom_row['approved_count'] : 0);

        // Team BDs: distinct user_id from mom_data approved by this manager
        $bd_rows = $this->db->query(
            "SELECT DISTINCT user_id FROM mom_data WHERE approved_by = ? AND user_id != ''",
            array((string)$uid)
        )->result_array();
        $bd_ids = array_column($bd_rows, 'user_id');

        $positive_conversions = 0;
        if (!empty($bd_ids)) {
            $bd_in = implode(',', array_map('intval', $bd_ids));
            $conv_row = $this->db->query(
                "SELECT COUNT(*) AS pos_cnt
                 FROM tblcallevents
                 WHERE user_id IN ({$bd_in})
                   AND nstatus_id >= 6
                   AND DATE(date) >= ?",
                array($week_start)
            )->row_array();
            $positive_conversions = (int)($conv_row ? $conv_row['pos_cnt'] : 0);
        }

        $gross_rs = ($approved_moms * 500) + ($positive_conversions * 200);
        $net_rs   = $gross_rs;

        $computed = array(
            'uid'                   => $uid,
            'week_start'            => $week_start,
            'gross_rs'              => $gross_rs,
            'deduction_rs'          => 0,
            'net_rs'                => $net_rs,
            'grade'                 => ($gross_rs >= 3000 ? 'A' : ($gross_rs >= 1000 ? 'B' : 'C')),
            'payout_status'         => 'pending',
            'approved_moms_count'   => $approved_moms,
            'positive_conversions'  => $positive_conversions,
            'source'                => 'computed',
        );

        if ($gross_rs === 0) {
            return $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'   => 0,
                    'uid'     => $uid,
                    'reason'  => 'no_rows',
                    'summary' => $computed,
                ),
                'route'        => 'api/manager_incentive/this_week',
                'generated_at' => date('c'),
            ));
        }

        return $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => array($computed),
            'data'         => array('count' => 1, 'uid' => $uid, 'week_start' => $week_start),
            'route'        => 'api/manager_incentive/this_week',
            'generated_at' => date('c'),
        ));
    }
}

// CI3 routing alias so routes pointing to ManagerIncentiveController also work
if (!class_exists('ManagerIncentiveController', false)) {
    class_alias('BlitzManager_api', 'ManagerIncentiveController');
}
// Alias for LineManagerScorecardController leaderboard route
if (!class_exists('LineManagerScorecardController', false)) {
    class_alias('BlitzManager_api', 'LineManagerScorecardController');
}
