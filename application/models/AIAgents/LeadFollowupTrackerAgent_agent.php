<?php
// =====================================================================
// STEM CRM - Migration 025: Lead Followup Tracker Agent
// File: application/models/AIAgents/LeadFollowupTracker_model.php
// =====================================================================
// Purpose:
//   The rot detector. Every meeting that ends with classification
//   got_details_only, tentative_*, or rp_* opens a 15-day clock on
//   the lead. This agent runs nightly at 23:00 IST and:
//
//     1) Reads every open followup row in lead_followup_tracker
//     2) Computes days_elapsed since last meeting
//     3) Applies SLA matrix per classification + travel_cluster flag
//     4) Sends nudges at Day 1 reminder, Day 3 red flag, Day 7 yellow,
//        Day 10 auto-downgrade, Day 15 hard expiry with penalty
//     5) Detects repeat got-details on same cid (2 in 30 days) and
//        forces a CM joint meeting requirement on the BD
//     6) Detects 3+ expired got-details by same BD in 30 days and
//        flags pattern_violation = 1 for SH coaching
//     7) Auto-categorises closed (cstatus=12) leads as upsell with
//        lane derivation (ANCHOR / DMFT / PSU / STANDARD_UPSELL)
//
// Hooked from:
//   - controllers/MeetingLifecycle.php::end() creates the followup row
//   - cron 0c647bbd weekday 07:30 IST consolidated audit (new section)
//   - controllers/Discipline.php on penalty deduction
//
// Staging table: lead_followup_tracker (see stem_migration_025_sql.sql)
// =====================================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class LeadFollowupTracker_model extends CI_Model {

    // SLA matrix in days. Keyed by classification, with travel_cluster
    // override doubling the urgency and the penalty.
    private $sla_days = array(
        'got_details_only'        => array('reminder' => 3, 'red' => 7, 'expire' => 15),
        'got_details_only_travel' => array('reminder' => 2, 'red' => 4, 'expire' => 7),
        'tentative_met_dm'        => array('reminder' => 2, 'red' => 4, 'expire' => 7),
        'tentative_met_inflncer'  => array('reminder' => 2, 'red' => 3, 'expire' => 5),
        'rp_positive'             => array('reminder' => 4, 'red' => 7, 'expire' => 10),
        'rp_with_objection'       => array('reminder' => 2, 'red' => 3, 'expire' => 5),
        'proposal_shared'         => array('reminder' => 5, 'red' => 9, 'expire' => 14),
    );

    // Penalty in rupees, deducted from BD wallet on hard expiry
    private $penalty_rs = array(
        'got_details_only'        => 500,
        'got_details_only_travel' => 1000,  // double penalty for travel waste
        'tentative_met_dm'        => 300,
        'tentative_met_inflncer'  => 300,
        'rp_positive'             => 200,
        'rp_with_objection'       => 400,
        'proposal_shared'         => 500,
    );

    // Planning grade points deducted on expiry
    private $grade_points = array(
        'got_details_only'        => 5,
        'got_details_only_travel' => 10,
        'tentative_met_dm'        => 3,
        'tentative_met_inflncer'  => 3,
        'rp_positive'             => 2,
        'rp_with_objection'       => 4,
        'proposal_shared'         => 5,
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -----------------------------------------------------------------
    // ENTRY POINT 1 - Called on meeting end by MeetingLifecycle::end()
    // Opens or refreshes a followup row for this cid+actor combo.
    // -----------------------------------------------------------------
    public function open_followup($params) {
        $cid_id       = (int)$params['cid_id'];
        $actor_uid    = (int)$params['actor_uid'];
        $actor_role   = $this->db->escape($params['actor_role']);
        $callevent_id = (int)$params['callevent_id'];
        $classification = $this->db->escape($params['classification']);
        $is_travel_cluster = isset($params['is_travel_cluster']) ? (int)$params['is_travel_cluster'] : 0;
        $meeting_ended_at = $this->db->escape($params['meeting_ended_at']);

        // Travel cluster overrides classification key for SLA matrix
        $sla_key = trim($params['classification']);
        if ($sla_key === 'got_details_only' && $is_travel_cluster === 1) {
            $sla_key = 'got_details_only_travel';
        }

        if (!isset($this->sla_days[$sla_key])) {
            // Classification does not open a clock (eg walkout, closure)
            return array('opened' => false, 'reason' => 'classification_no_clock');
        }

        $sla = $this->sla_days[$sla_key];
        $today = date('Y-m-d');
        $reminder_due = date('Y-m-d', strtotime($today . ' +' . $sla['reminder'] . ' days'));
        $red_due      = date('Y-m-d', strtotime($today . ' +' . $sla['red'] . ' days'));
        $expire_due   = date('Y-m-d', strtotime($today . ' +' . $sla['expire'] . ' days'));

        // Close any prior open row on same cid (the new meeting resets clock)
        $this->db->where('cid_id', $cid_id)
                 ->where('status', 'open')
                 ->update('lead_followup_tracker', array(
                    'status' => 'reset_by_new_meeting',
                    'closed_at' => date('Y-m-d H:i:s')
                 ));

        $insert = array(
            'cid_id' => $cid_id,
            'actor_uid' => $actor_uid,
            'actor_role' => trim($params['actor_role']),
            'callevent_id' => $callevent_id,
            'classification' => trim($params['classification']),
            'sla_key' => $sla_key,
            'is_travel_cluster' => $is_travel_cluster,
            'opened_at' => date('Y-m-d H:i:s'),
            'meeting_ended_at' => trim($params['meeting_ended_at']),
            'reminder_due_date' => $reminder_due,
            'red_flag_due_date' => $red_due,
            'expire_due_date' => $expire_due,
            'status' => 'open',
            'penalty_rs' => $this->penalty_rs[$sla_key],
            'grade_points_at_risk' => $this->grade_points[$sla_key]
        );

        $this->db->insert('lead_followup_tracker', $insert);
        $tracker_id = $this->db->insert_id();

        // Check for repeat got-details on same cid within 30 days
        $repeat_check = $this->detect_repeat_got_details($cid_id, $sla_key);

        return array(
            'opened' => true,
            'tracker_id' => $tracker_id,
            'sla_key' => $sla_key,
            'expire_due_date' => $expire_due,
            'penalty_rs' => $this->penalty_rs[$sla_key],
            'repeat_got_details' => $repeat_check
        );
    }

    // -----------------------------------------------------------------
    // ENTRY POINT 2 - Nightly cron sweep at 23:00 IST
    // Returns counts + list of actions taken for the consolidated audit
    // -----------------------------------------------------------------
    public function nightly_sweep() {
        $today = date('Y-m-d');
        $actions = array(
            'reminders_sent' => 0,
            'red_flags_raised' => 0,
            'auto_downgrades' => 0,
            'expirations' => 0,
            'penalty_total_rs' => 0,
            'grade_pts_deducted' => 0,
            'pattern_violations' => array(),
            'rows' => array()
        );

        // Pull every open row
        $rows = $this->db->where('status', 'open')
                         ->get('lead_followup_tracker')->result_array();

        foreach ($rows as $r) {
            $action = $this->process_one_followup($r, $today);
            if (!empty($action)) {
                $actions['rows'][] = $action;
                if (isset($action['kind'])) {
                    if ($action['kind'] === 'reminder')      $actions['reminders_sent']++;
                    if ($action['kind'] === 'red_flag')      $actions['red_flags_raised']++;
                    if ($action['kind'] === 'auto_downgrade')$actions['auto_downgrades']++;
                    if ($action['kind'] === 'expire') {
                        $actions['expirations']++;
                        $actions['penalty_total_rs'] += (int)$action['penalty_rs'];
                        $actions['grade_pts_deducted'] += (int)$action['grade_pts'];
                    }
                }
            }
        }

        // After per-row processing, detect BD-level pattern violations
        // (3+ expired got-details same BD in 30 days)
        $patterns = $this->detect_bd_pattern_violations();
        $actions['pattern_violations'] = $patterns;

        // Auto-categorise closed leads as upsell
        $upsell = $this->auto_categorise_closed_as_upsell();
        $actions['upsell_assigned'] = $upsell;

        return $actions;
    }

    // -----------------------------------------------------------------
    // Per-row state machine
    // -----------------------------------------------------------------
    private function process_one_followup($r, $today) {
        $opened = strtotime($r['opened_at']);
        $now    = strtotime($today);
        $days_elapsed = floor(($now - $opened) / 86400);
        $tracker_id = $r['id'];

        // Did the lead progress on its own? If cstatus moved up since open,
        // close this row as resolved.
        $progressed = $this->check_lead_progressed($r['cid_id'], $r['opened_at']);
        if ($progressed) {
            $this->db->where('id', $tracker_id)
                     ->update('lead_followup_tracker', array(
                        'status' => 'resolved_progressed',
                        'closed_at' => date('Y-m-d H:i:s'),
                        'days_to_resolve' => $days_elapsed
                     ));
            return array('tracker_id' => $tracker_id, 'kind' => 'resolved',
                         'cid_id' => $r['cid_id']);
        }

        // Hard expiry
        if ($today >= $r['expire_due_date']) {
            return $this->expire_and_penalise($r, $days_elapsed);
        }

        // Day 10 auto-downgrade (only for got-details cases)
        $downgrade_day = (int)($r['expire_due_date_days_total'] ?? 15) - 5;
        if ($days_elapsed >= 10 && in_array($r['sla_key'], array('got_details_only', 'got_details_only_travel'))) {
            return $this->auto_downgrade($r, $days_elapsed);
        }

        // Red flag
        if ($today >= $r['red_flag_due_date'] && (int)$r['red_flag_sent'] === 0) {
            return $this->raise_red_flag($r, $days_elapsed);
        }

        // Reminder
        if ($today >= $r['reminder_due_date'] && (int)$r['reminder_sent'] === 0) {
            return $this->send_reminder($r, $days_elapsed);
        }

        return null;
    }

    // -----------------------------------------------------------------
    // Action helpers
    // -----------------------------------------------------------------
    private function send_reminder($r, $days_elapsed) {
        $this->db->where('id', $r['id'])
                 ->update('lead_followup_tracker', array(
                    'reminder_sent' => 1,
                    'reminder_sent_at' => date('Y-m-d H:i:s')
                 ));

        // Get BD name for the message
        $this->db->select('first_name, last_name')->from('user')->where('uid', $r['actor_uid']);
        $bd = $this->db->get()->row_array();
        $bd_name = trim(($bd['first_name'] ?? '') . ' ' . ($bd['last_name'] ?? ''));

        return array(
            'tracker_id' => $r['id'],
            'kind' => 'reminder',
            'cid_id' => $r['cid_id'],
            'actor_uid' => $r['actor_uid'],
            'actor_name' => $bd_name,
            'days_elapsed' => $days_elapsed,
            'expire_in_days' => max(0, floor((strtotime($r['expire_due_date']) - time())/86400)),
            'message' => 'Followup reminder: lead ' . $r['cid_id'] . ' has been silent for '
                       . $days_elapsed . ' days. Schedule next meeting within '
                       . max(0, floor((strtotime($r['expire_due_date']) - time())/86400))
                       . ' days or it expires with penalty Rs ' . $r['penalty_rs'] . '.'
        );
    }

    private function raise_red_flag($r, $days_elapsed) {
        $this->db->where('id', $r['id'])
                 ->update('lead_followup_tracker', array(
                    'red_flag_sent' => 1,
                    'red_flag_sent_at' => date('Y-m-d H:i:s'),
                    'status' => 'red_flagged'
                 ));

        return array(
            'tracker_id' => $r['id'],
            'kind' => 'red_flag',
            'cid_id' => $r['cid_id'],
            'actor_uid' => $r['actor_uid'],
            'days_elapsed' => $days_elapsed,
            'message' => 'RED FLAG: lead ' . $r['cid_id'] . ' rotting at ' . $days_elapsed
                       . ' days. Travel cluster: ' . ($r['is_travel_cluster'] ? 'YES' : 'no')
                       . '. Penalty triggers in ' . max(0, floor((strtotime($r['expire_due_date']) - time())/86400)) . ' days.'
        );
    }

    private function auto_downgrade($r, $days_elapsed) {
        // Drop init_call.cstatus down by one if it stayed put
        $cstatus = $this->db->select('cstatus')->from('init_call')
                            ->where('id', $r['cid_id'])->get()->row()->cstatus ?? null;

        if ($cstatus !== null && $cstatus > 1) {
            $new_cstatus = $cstatus - 1;
            $this->db->where('id', $r['cid_id'])
                     ->update('init_call', array(
                        'cstatus' => $new_cstatus,
                        'last_auto_downgrade_at' => date('Y-m-d H:i:s'),
                        'last_auto_downgrade_reason' => 'got_details_no_followup_in_10_days'
                     ));
        }

        $this->db->where('id', $r['id'])
                 ->update('lead_followup_tracker', array(
                    'auto_downgrade_done' => 1,
                    'auto_downgrade_at' => date('Y-m-d H:i:s'),
                    'status' => 'auto_downgraded'
                 ));

        return array(
            'tracker_id' => $r['id'],
            'kind' => 'auto_downgrade',
            'cid_id' => $r['cid_id'],
            'actor_uid' => $r['actor_uid'],
            'days_elapsed' => $days_elapsed,
            'old_cstatus' => $cstatus,
            'new_cstatus' => $new_cstatus ?? $cstatus,
            'message' => 'AUTO DOWNGRADE: lead ' . $r['cid_id'] . ' cstatus dropped from '
                       . $cstatus . ' to ' . ($new_cstatus ?? $cstatus) . ' after 10 days no followup.'
        );
    }

    private function expire_and_penalise($r, $days_elapsed) {
        $penalty = (int)$r['penalty_rs'];
        $grade_pts = (int)$r['grade_points_at_risk'];

        // Deduct from BD wallet via cash_log
        $this->db->insert('cash_log', array(
            'uid' => $r['actor_uid'],
            'debit_amt' => $penalty,
            'credit_amt' => 0,
            'remarks' => 'Penalty for expired followup on cid ' . $r['cid_id']
                       . ' classification ' . $r['classification']
                       . ($r['is_travel_cluster'] ? ' (travel cluster double penalty)' : ''),
            'created_at' => date('Y-m-d H:i:s')
        ));

        // Deduct planning grade points
        $this->db->insert('planning_grade_adjustments', array(
            'uid' => $r['actor_uid'],
            'date' => date('Y-m-d'),
            'points_delta' => -$grade_pts,
            'reason' => 'followup_expired_cid_' . $r['cid_id']
        ));

        // Mark tracker row expired
        $this->db->where('id', $r['id'])
                 ->update('lead_followup_tracker', array(
                    'status' => 'expired',
                    'expired_at' => date('Y-m-d H:i:s'),
                    'penalty_applied_rs' => $penalty,
                    'grade_pts_applied' => $grade_pts,
                    'closed_at' => date('Y-m-d H:i:s'),
                    'days_to_resolve' => $days_elapsed
                 ));

        return array(
            'tracker_id' => $r['id'],
            'kind' => 'expire',
            'cid_id' => $r['cid_id'],
            'actor_uid' => $r['actor_uid'],
            'days_elapsed' => $days_elapsed,
            'penalty_rs' => $penalty,
            'grade_pts' => $grade_pts,
            'is_travel_cluster' => $r['is_travel_cluster'],
            'message' => 'EXPIRED: cid ' . $r['cid_id'] . ' classification '
                       . $r['classification'] . ' after ' . $days_elapsed
                       . ' days. Penalty Rs ' . $penalty . ' deducted from wallet, '
                       . $grade_pts . ' planning grade points removed.'
        );
    }

    // -----------------------------------------------------------------
    // Did the lead progress on its own? (cstatus jumped up since open)
    // -----------------------------------------------------------------
    private function check_lead_progressed($cid_id, $opened_at) {
        $sql = "SELECT 1 FROM lead_progression_log
                WHERE cid_id = ? AND created_at > ?
                  AND to_cstatus > from_cstatus
                LIMIT 1";
        $row = $this->db->query($sql, array($cid_id, $opened_at))->row();
        return ($row ? true : false);
    }

    // -----------------------------------------------------------------
    // Repeat got-details detector
    // 2 got-details on same cid in 30 days forces a CM joint within 7 days
    // -----------------------------------------------------------------
    private function detect_repeat_got_details($cid_id, $sla_key) {
        if (strpos($sla_key, 'got_details_only') !== 0) return false;

        $sql = "SELECT COUNT(*) AS cnt FROM lead_followup_tracker
                WHERE cid_id = ?
                  AND sla_key LIKE 'got_details_only%'
                  AND opened_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $cnt = (int)$this->db->query($sql, array($cid_id))->row()->cnt;

        if ($cnt >= 2) {
            // Insert required joint meeting flag on init_call
            $this->db->where('id', $cid_id)
                     ->update('init_call', array(
                        'requires_cm_joint_within' => date('Y-m-d', strtotime('+7 days')),
                        'requires_cm_joint_reason' => 'repeat_got_details_2_in_30_days'
                     ));
            return array('flag_set' => true, 'count_30d' => $cnt);
        }
        return false;
    }

    // -----------------------------------------------------------------
    // BD pattern violation detector
    // 3+ expired got-details in 30 days = mandatory joints + SH coaching
    // + planning grade auto D
    // -----------------------------------------------------------------
    private function detect_bd_pattern_violations() {
        $sql = "SELECT actor_uid, COUNT(*) AS expired_cnt
                FROM lead_followup_tracker
                WHERE sla_key LIKE 'got_details_only%'
                  AND status = 'expired'
                  AND expired_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY actor_uid
                HAVING expired_cnt >= 3";

        $rows = $this->db->query($sql)->result_array();
        $flagged = array();

        foreach ($rows as $r) {
            // Get BD name
            $this->db->select('first_name, last_name')->from('user')->where('uid', $r['actor_uid']);
            $bd = $this->db->get()->row_array();
            $bd_name = trim(($bd['first_name'] ?? '') . ' ' . ($bd['last_name'] ?? ''));

            // Set pattern violation
            $this->db->insert('bd_pattern_violations', array(
                'uid' => $r['actor_uid'],
                'violation_type' => 'got_details_rot_pattern',
                'detected_at' => date('Y-m-d H:i:s'),
                'expired_count_30d' => $r['expired_cnt'],
                'sanctions' => json_encode(array(
                    'planning_grade_forced_D' => true,
                    'mandatory_joints_until' => date('Y-m-d', strtotime('+30 days')),
                    'sh_coaching_required' => true
                ))
            ));

            $flagged[] = array(
                'actor_uid' => $r['actor_uid'],
                'actor_name' => $bd_name,
                'expired_count_30d' => $r['expired_cnt']
            );
        }
        return $flagged;
    }

    // -----------------------------------------------------------------
    // Auto-categorise closed (cstatus=12) leads as upsell with lane
    // -----------------------------------------------------------------
    private function auto_categorise_closed_as_upsell() {
        // Pull Won leads that have not yet been pushed to rm_upsell_pipeline
        $sql = "SELECT ic.id AS cid_id, ic.compny_nm, ic.fbudget, ic.closed_value_rs,
                       ic.mainbd, ic.cluster_id
                FROM init_call ic
                LEFT JOIN rm_upsell_pipeline rup ON rup.cid_id = ic.id
                WHERE ic.cstatus = 12
                  AND rup.id IS NULL
                LIMIT 200";

        $rows = $this->db->query($sql)->result_array();
        if (empty($rows)) return array('assigned' => 0, 'rows' => array());

        $assigned = array();
        // Compute fbudget P90 cutoff for ANCHOR lane
        $p90_sql = "SELECT closed_value_rs FROM init_call WHERE cstatus = 12
                    ORDER BY closed_value_rs DESC LIMIT 1 OFFSET (
                      SELECT FLOOR(COUNT(*) * 0.10) FROM init_call WHERE cstatus = 12
                    )";
        $p90_row = $this->db->query($p90_sql)->row();
        $p90 = $p90_row ? (float)$p90_row->closed_value_rs : 5000000.0;

        foreach ($rows as $r) {
            $lane = $this->derive_upsell_lane($r, $p90);

            // Find or stub the RM that owns this cluster
            $rm_uid = $this->find_rm_for_cluster($r['cluster_id']);

            $this->db->insert('rm_upsell_pipeline', array(
                'cid_id' => $r['cid_id'],
                'rm_uid' => $rm_uid,
                'lane' => $lane,
                'school_name' => $r['compny_nm'],
                'closed_value_rs' => $r['closed_value_rs'],
                'cluster_id' => $r['cluster_id'],
                'auto_assigned_at' => date('Y-m-d H:i:s'),
                'days_since_rm_touch' => 9999
            ));

            $assigned[] = array(
                'cid_id' => $r['cid_id'],
                'school' => $r['compny_nm'],
                'lane' => $lane,
                'rm_uid' => $rm_uid
            );
        }
        return array('assigned' => count($assigned), 'rows' => $assigned);
    }

    private function derive_upsell_lane($r, $p90) {
        // ANCHOR: top 10% fbudget OR named CSR sponsor flagged on init_call
        if ((float)$r['closed_value_rs'] >= $p90) return 'ANCHOR';

        // PSU: cluster_id flagged govt/PSU
        $cluster = $this->db->select('is_psu, is_govt_cohort')
                            ->from('cluster')
                            ->where('id', $r['cluster_id'])
                            ->get()->row_array();
        if (!empty($cluster['is_psu'])) return 'PSU';
        if (!empty($cluster['is_govt_cohort'])) return 'DMFT';

        return 'STANDARD_UPSELL';
    }

    private function find_rm_for_cluster($cluster_id) {
        $sql = "SELECT uid FROM user
                WHERE type_id = 28 AND cluster_id = ?
                ORDER BY uid LIMIT 1";
        $row = $this->db->query($sql, array($cluster_id))->row();
        return $row ? $row->uid : null;
    }
}
// END LeadFollowupTracker_model
