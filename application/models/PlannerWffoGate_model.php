<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/models/PlannerWffoGate_model.php
 * Planner Work From Field Office (WFFO) gate model.
 * Used by Leave controller to check if a user has WFFO approvals
 * that would conflict with leave requests.
 * Plain ASCII only. No em-dash.
 */
class PlannerWffoGate_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Check if user has active WFFO plan for a date range.
     * Returns array of conflicting plan entries, empty if none.
     */
    public function get_conflicts($user_id, $start_date, $end_date) {
        $user_id    = (int)$user_id;
        $start_date = (string)$start_date;
        $end_date   = (string)$end_date;

        // Try cm_daily_plan table (standard planner store).
        try {
            $rows = $this->db->query(
                'SELECT id, plan_date, plan_type, status FROM cm_daily_plan WHERE uid = ? AND plan_date BETWEEN ? AND ? AND status IN (?,?,?) ORDER BY plan_date ASC',
                array($user_id, $start_date, $end_date, 'approved', 'submitted', 'active')
            )->result_array();
            return $rows;
        } catch (Exception $e) {
            // Table may not exist in all deployments. Return empty gracefully.
            return array();
        }
    }

    /**
     * Get approved WFFO days for a user in the current month.
     */
    public function get_monthly_wffo($user_id, $year_month = null) {
        $user_id = (int)$user_id;
        if ($year_month === null) {
            $year_month = date('Y-m');
        }
        $start = $year_month . '-01';
        $end   = date('Y-m-t', strtotime($start));

        try {
            $rows = $this->db->query(
                'SELECT id, plan_date, plan_type, status FROM cm_daily_plan WHERE uid = ? AND plan_date BETWEEN ? AND ? ORDER BY plan_date ASC',
                array($user_id, $start, $end)
            )->result_array();
            return $rows;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Get the leave gate config. Returns an array of rules.
     * If the table stem_feature_flags does not exist, returns safe defaults.
     */
    public function get_gate_config() {
        try {
            $rows = $this->db->query(
                "SELECT flag_key, flag_value FROM stem_feature_flags WHERE flag_key LIKE 'leave_%' AND flag_scope = 'staging'"
            )->result_array();
            $config = array();
            foreach ($rows as $r) {
                $config[$r['flag_key']] = $r['flag_value'];
            }
            return $config;
        } catch (Exception $e) {
            return array('leave_gate_enabled' => '0');
        }
    }
}
