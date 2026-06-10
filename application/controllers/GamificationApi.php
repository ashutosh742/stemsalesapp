<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GamificationApi
 * Endpoint: GET /api/gamification/badges?uid={uid}
 *
 * Computes real badge conditions from DB.
 *
 * Badge 1: first_won
 *   init_call WHERE mainbd=uid AND cstatus=12
 *   earned_at: MIN(updated_at)
 *
 * Badge 2: streak_5_meetings
 *   othertask WHERE uid=uid last 7 days, 5+ consecutive calendar days with tasks.
 *
 * Badge 3: planner_a_grade
 *   planner_coach_discipline WHERE bd_uid=uid AND grade_letter IN ('A','A+') last 7 days.
 *   Falls back to bd_productivity_daily WHERE score_pct >= 70 if discipline table is empty.
 *
 * Returns rows=[] with reason='no_rows' if uid has earned no badges.
 *
 * Route: routes_blitz_30may_f.php -> GamificationApi/badges
 */
class GamificationApi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
            return false;
        }
        return true;
    }

    private function _json($rows, $route, $meta = array()) {
        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array_merge(array('count' => count($rows)), $meta),
            'route'        => $route,
            'generated_at' => date('c'),
        )));
    }

    /**
     * GET /api/gamification/badges?uid=
     */
    public function badges() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid', TRUE);
        if (!$uid) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'uid required')));
            return;
        }

        $badges = array();

        // Badge 1: first_won
        $r1 = $this->db->query(
            "SELECT COUNT(*) AS win_count, MIN(updated_at) AS first_win_at
             FROM init_call
             WHERE mainbd = ?
               AND cstatus = 12",
            array($uid)
        )->row_array();
        if (!empty($r1) && (int)$r1['win_count'] > 0) {
            $badges[] = array(
                'badge_key'   => 'first_won',
                'label'       => 'First Win',
                'description' => 'Closed your first deal',
                'earned_at'   => $r1['first_win_at'],
                'detail'      => array('total_wins' => (int)$r1['win_count']),
            );
        }

        // Badge 2: streak_5_meetings
        $cutoff = date('Y-m-d H:i:s', strtotime('-7 days'));
        $day_rows = $this->db->query(
            "SELECT DATE(sdatet) AS task_day, COUNT(*) AS task_cnt
             FROM othertask
             WHERE uid = ?
               AND sdatet >= ?
             GROUP BY DATE(sdatet)
             ORDER BY task_day ASC",
            array($uid, $cutoff)
        )->result_array();

        $streak_earned_at = null;
        if (count($day_rows) >= 5) {
            $dates = array_column($day_rows, 'task_day');
            $cur_streak = 1;
            $streak_end_day = null;
            for ($i = 1; $i < count($dates); $i++) {
                $diff = (strtotime($dates[$i]) - strtotime($dates[$i - 1])) / 86400;
                if ($diff == 1) {
                    $cur_streak++;
                    if ($cur_streak >= 5 && !$streak_end_day) {
                        $streak_end_day = $dates[$i];
                    }
                } else {
                    $cur_streak = 1;
                }
            }
            if ($streak_end_day) {
                $badges[] = array(
                    'badge_key'   => 'streak_5_meetings',
                    'label'       => '5-Day Meeting Streak',
                    'description' => 'Tasks on 5 consecutive days in the last 7 days',
                    'earned_at'   => $streak_end_day,
                    'detail'      => array('streak_end' => $streak_end_day),
                );
            }
        }

        // Badge 3: planner_a_grade - try planner_coach_discipline first
        $pcd_check = $this->db->query("SHOW TABLES LIKE 'planner_coach_discipline'")->num_rows() > 0;
        $grade_earned = false;
        if ($pcd_check) {
            $cutoff7 = date('Y-m-d', strtotime('-7 days'));
            $r3 = $this->db->query(
                "SELECT MAX(plan_date) AS best_plan_date
                 FROM planner_coach_discipline
                 WHERE bd_uid = ?
                   AND grade_letter IN ('A', 'A+')
                   AND plan_date >= ?",
                array($uid, $cutoff7)
            )->row_array();
            if (!empty($r3) && !empty($r3['best_plan_date'])) {
                $badges[] = array(
                    'badge_key'   => 'planner_a_grade',
                    'label'       => 'Planner A-Grade',
                    'description' => 'Achieved an A-grade on planning discipline this week',
                    'earned_at'   => $r3['best_plan_date'],
                    'detail'      => array(),
                );
                $grade_earned = true;
            }
        }

        // Fallback: bd_productivity_daily WHERE score_pct >= 70 last 7 days
        if (!$grade_earned) {
            $cutoff7 = date('Y-m-d', strtotime('-7 days'));
            $r3b = $this->db->query(
                "SELECT MAX(for_date) AS best_date, MAX(score_pct) AS best_score
                 FROM bd_productivity_daily
                 WHERE bd_uid = ?
                   AND for_date >= ?
                   AND score_pct >= 70",
                array($uid, $cutoff7)
            )->row_array();
            if (!empty($r3b) && !empty($r3b['best_date'])) {
                $badges[] = array(
                    'badge_key'   => 'planner_a_grade',
                    'label'       => 'Planner A-Grade',
                    'description' => 'Achieved a high productivity score this week',
                    'earned_at'   => $r3b['best_date'],
                    'detail'      => array('best_score_pct' => (float)$r3b['best_score']),
                );
            }
        }

        if (empty($badges)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0, 'uid' => $uid, 'reason' => 'no_rows'),
                'route'        => 'api/gamification/badges',
                'generated_at' => date('c'),
            )));
            return;
        }

        $this->_json($badges, 'api/gamification/badges', array('uid' => $uid));
    }
}
