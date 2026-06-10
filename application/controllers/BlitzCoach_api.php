<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BlitzCoach_api
 * Endpoints:
 *   GET /api/planner_coach/today?uid={uid}    - coaching tips for today's plan
 *   GET /api/planner_analyst/today?uid={uid}  - analyst breakdown of today's tasks
 *
 * Both read from tblcallevents (fwd_date = today) joined with init_call and company_master.
 *
 * Planner Coach Today:
 *   - tip 1: meeting count (actiontype_id IN (3,4)); tip if < 3 meetings planned
 *   - tip 2: travel clustering; tip if >= 2 distinct districts planned
 *   - tip 3: stale leads (last touch > 7 days ago) not in today's plan
 *
 * Planner Analyst Today:
 *   - meetings_count, research_count, barge_count, other_count
 *   - total_estimated_travel_hours (distinct districts * 1.5, minus 1 hop)
 *   - total_estimated_wallet_spend_rs (SUM of planned_cost)
 *   - expected_positive_conversions (meeting tasks where lead cstatus IN (6,7,9,12,13))
 *
 * Routes:
 *   routes_blitz_30may_d.php -> BlitzCoach_api/analyst_today (/api/planner_analyst/today)
 *   routes_additions.php     -> PlannerCoachController/today (/api/planner_coach/today)
 *   (alias at bottom makes both work from this file)
 */
class BlitzCoach_api extends CI_Controller {

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

    // Load today's planned tasks for a BD
    private function _load_today_plan($uid) {
        return $this->db->query(
            "SELECT
                tc.id AS event_id,
                tc.cid_id,
                tc.actiontype_id,
                tc.planned_cost,
                ic.cstatus,
                TRIM(cm.district)  AS district,
                TRIM(cm.city)      AS city,
                TRIM(cm.compname)  AS compname
             FROM tblcallevents tc
             LEFT JOIN init_call ic   ON ic.id = tc.cid_id
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             WHERE tc.user_id = ?
               AND DATE(tc.fwd_date) = ?",
            array($uid, date('Y-m-d'))
        )->result();
    }

    /**
     * GET /api/planner_coach/today?uid={uid}
     * Returns coaching tips array for this BD's current plan.
     */
    public function today() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json(array('ok' => false, 'error' => 'uid required'));
        }

        $today = date('Y-m-d');
        $tasks = $this->_load_today_plan($uid);

        $tips = array();

        // Tip 1: meeting count
        $meeting_count = 0;
        foreach ($tasks as $t) {
            if (in_array((int)$t->actiontype_id, array(3, 4), true)) $meeting_count++;
        }
        if ($meeting_count < 3) {
            $tips[] = array(
                'tip_key' => 'low_meeting_count',
                'message' => "Add at least 3 meetings to hit the planning grade A band. Currently have {$meeting_count}.",
                'severity' => 'warning',
            );
        }

        // Tip 2: travel clustering
        $meeting_districts = array();
        foreach ($tasks as $t) {
            if (!in_array((int)$t->actiontype_id, array(3, 4), true)) continue;
            $d = $t->district ? $t->district : ($t->city ?: 'Unknown');
            $meeting_districts[$d] = true;
        }
        $district_count = count($meeting_districts);
        if ($district_count >= 2) {
            $tips[] = array(
                'tip_key'  => 'travel_spread',
                'message'  => "Cluster meetings by area to reduce travel time. Currently spanning {$district_count} districts.",
                'severity' => 'info',
                'districts' => array_keys($meeting_districts),
            );
        }

        // Tip 3: stale leads not in today's plan
        $plan_cid_ids = array();
        foreach ($tasks as $t) {
            $plan_cid_ids[(int)$t->cid_id] = true;
        }

        $stale_leads = $this->db->query(
            "SELECT ic.id AS cid_id, TRIM(cm.compname) AS compname,
                    DATEDIFF(CURDATE(), MAX(ce.date)) AS days_since
             FROM init_call ic
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             LEFT JOIN tblcallevents ce ON ce.cid_id = ic.id
             WHERE ic.mainbd = ?
               AND ic.cstatus NOT IN (5, 10, 11, 14)
             GROUP BY ic.id, cm.compname
             HAVING days_since > 7 OR days_since IS NULL
             ORDER BY days_since DESC
             LIMIT 10",
            array($uid)
        )->result();

        $stale_added = 0;
        foreach ($stale_leads as $sl) {
            if (isset($plan_cid_ids[(int)$sl->cid_id])) continue;
            if ($stale_added >= 3) break;
            $name  = $sl->compname ?: "Lead #{$sl->cid_id}";
            $days  = $sl->days_since !== null ? (int)$sl->days_since : 'unknown';
            $tips[] = array(
                'tip_key'  => 'stale_lead',
                'message'  => "Visit {$name} today -- last touched {$days} days ago.",
                'severity' => 'reminder',
                'cid_id'   => (int)$sl->cid_id,
            );
            $stale_added++;
        }

        return $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $tips,
            'data'         => array(
                'count'          => count($tips),
                'uid'            => $uid,
                'date'           => $today,
                'meeting_count'  => $meeting_count,
                'reason'         => empty($tips) ? 'no_rows' : null,
            ),
            'route'        => 'api/planner_coach/today',
            'generated_at' => date('c'),
        ));
    }

    /**
     * GET /api/planner_analyst/today?uid={uid}
     * Returns quantitative breakdown of today's plan.
     */
    public function analyst_today() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json(array('ok' => false, 'error' => 'uid required'));
        }

        $today = date('Y-m-d');
        $tasks = $this->_load_today_plan($uid);

        $meetings_count     = 0;
        $research_count     = 0;
        $barge_count        = 0;
        $other_count        = 0;
        $wallet_spend_rs    = 0;
        $meeting_districts  = array();
        $positive_conv_count = 0;

        $positive_statuses = array(6, 7, 9, 12, 13);

        foreach ($tasks as $t) {
            $at = (int) $t->actiontype_id;
            $wallet_spend_rs += (int)($t->planned_cost ?: 500);
            if ($at === 3) { $meetings_count++; }
            elseif ($at === 4) { $meetings_count++; $barge_count++; }
            elseif ($at === 10) { $research_count++; }
            else { $other_count++; }
            if (in_array($at, array(3, 4), true)) {
                $d = $t->district ?: ($t->city ?: 'Unknown');
                $meeting_districts[$d] = true;
                if ($t->cstatus && in_array((int)$t->cstatus, $positive_statuses, true)) {
                    $positive_conv_count++;
                }
            }
        }

        $district_count  = count($meeting_districts);
        $travel_hops     = max(0, $district_count - 1);
        $travel_hours    = round($travel_hops * 1.5, 1);

        $row = array(
            'uid'                            => $uid,
            'date'                           => $today,
            'meetings_count'                 => $meetings_count,
            'research_count'                 => $research_count,
            'barge_count'                    => $barge_count,
            'other_count'                    => $other_count,
            'total_tasks'                    => count($tasks),
            'distinct_districts'             => $district_count,
            'total_estimated_travel_hours'   => $travel_hours,
            'total_estimated_wallet_spend_rs' => $wallet_spend_rs,
            'expected_positive_conversions'  => $positive_conv_count,
        );

        $is_empty = count($tasks) === 0;

        return $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $is_empty ? array() : array($row),
            'data'         => array(
                'count'  => $is_empty ? 0 : 1,
                'uid'    => $uid,
                'date'   => $today,
                'reason' => $is_empty ? 'no_rows' : null,
            ),
            'route'        => 'api/planner_analyst/today',
            'generated_at' => date('c'),
        ));
    }
}

// Routes for /api/planner_coach/today point to PlannerCoachController/today
if (!class_exists('PlannerCoachController', false)) {
    class_alias('BlitzCoach_api', 'PlannerCoachController');
}
