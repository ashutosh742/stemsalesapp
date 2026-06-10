<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * stem_bd_planner_block_patch.php
 *
 * Migration 026 patch for BD daily planner submission.
 *
 * Inserted at the top of Planner_controller::submit_plan() and
 * Planner_controller::add_task() endpoints. Calls
 * Proposal_sla_enforcer_agent::check_planner_block($bd_uid, $plan_date)
 * to decide whether BD is allowed to draft tomorrow's plan.
 *
 * If blocked, returns HTTP 423 Locked with blocking_cid_ids array and
 * a plain English breach message. BD must either submit the missing
 * proposals via /api/proposal/sla/upload or request an extension via
 * /api/proposal/sla/extend before the planner accepts any tomorrow row.
 *
 * This is a HARD BLOCK per the founder's directive:
 *   "Wherever the proposals are need to be sent.
 *    It has to be hard block within 48 hours proposal has to be sent"
 *
 * Production hold until 18 May 2026 GitHub access.
 * Pilot first: 6 pilot uids on 1 Jun 2026.
 * Org rollout: 1 Jul 2026 after Phase 1 stable for 4 weeks.
 *
 * Hook order at submit_plan() top:
 *   1. Auth check (existing)
 *   2. Plan date parse (existing)
 *   3. THIS PATCH: proposal_sla_block_check  <-- new
 *   4. Day shape lock check (existing, migration 017_5)
 *   5. WFO leave gate (existing, migration 017_4)
 *   6. Same-day plan flag (existing)
 *   7. Continue with row insert (existing)
 *
 * Migration 026, June 2026.
 */

class Bd_planner_block_patch
{
    /**
     * Apply the proposal SLA block check.
     *
     * Call this at the top of submit_plan() and add_task() in
     * Planner_controller before any daily_planner insert.
     *
     * @param int    $bd_uid     The BD attempting to draft tomorrow
     * @param string $plan_date  Plan date in YYYY-MM-DD (must be > today)
     * @param object $CI         CI instance for db + load access
     * @return array             ['allowed' => bool, 'response' => array|null]
     *                           If allowed=false the response is a ready
     *                           JSON payload the controller should emit with
     *                           HTTP 423 before returning.
     */
    public static function check($bd_uid, $plan_date, &$CI)
    {
        // Only enforce for tomorrow plans. Same-day fixes still allowed
        // since SLA breach is about TOMORROW's plan-submit gate at 18:30.
        $today_ist = date('Y-m-d', strtotime('today'));
        if ($plan_date <= $today_ist) {
            return array('allowed' => true, 'response' => null);
        }

        // Skip if migration 026 not yet deployed (table missing fallback)
        if (!$CI->db->table_exists('proposal_sla_tracker')) {
            return array('allowed' => true, 'response' => null);
        }

        // Skip if BD not in pilot scope yet. Pilot mode reads from
        // config_setting key 'm026_pilot_uids' (CSV) until 1 Jul org-wide.
        $org_wide_date = '2026-07-01';
        if ($today_ist < $org_wide_date) {
            $row = $CI->db->select('config_value')
                          ->where('config_key', 'm026_pilot_uids')
                          ->get('config_setting')
                          ->row();
            $pilot_csv = $row ? $row->config_value : '42,43,44,45,46';
            $pilot_uids = array_map('intval', explode(',', $pilot_csv));
            if (!in_array((int)$bd_uid, $pilot_uids, true)) {
                return array('allowed' => true, 'response' => null);
            }
        }

        // Delegate to the SLA enforcer agent (file #4).
        $CI->load->library('AIAgents/Proposal_sla_enforcer_agent');
        $check = $CI->proposal_sla_enforcer_agent->check_planner_block(
            (int)$bd_uid,
            $plan_date
        );

        // check_planner_block returns:
        //   ['allowed' => true]
        // or
        //   ['allowed' => false,
        //    'blocking_cid_ids' => [...],
        //    'count' => N,
        //    'oldest_age_hours' => H,
        //    'message' => '...']

        if (!empty($check['allowed'])) {
            return array('allowed' => true, 'response' => null);
        }

        // Log the block attempt for audit trail.
        $CI->db->insert('bd_planner_block_log', array(
            'bd_uid'             => (int)$bd_uid,
            'plan_date'          => $plan_date,
            'blocked_at'         => date('Y-m-d H:i:s'),
            'block_reason'       => 'proposal_sla_breach',
            'blocking_cid_ids'   => json_encode($check['blocking_cid_ids']),
            'blocking_count'     => (int)$check['count'],
            'oldest_age_hours'   => (float)$check['oldest_age_hours'],
            'user_message'       => $check['message'],
        ));

        // Mark the daily_planner row (if any partial exists) as blocked.
        // Some BDs may have a draft row already from morning brief seed.
        $CI->db->where('mainbd', (int)$bd_uid)
               ->where('plan_date', $plan_date)
               ->update('daily_planner', array(
                   'blocked_by_proposal_sla_at' => date('Y-m-d H:i:s'),
                   'blocking_cid_ids'           => json_encode($check['blocking_cid_ids']),
               ));

        // Build response payload for the controller to emit.
        $blocking_details = self::_describe_blocking_leads(
            $check['blocking_cid_ids'], $CI
        );

        $payload = array(
            'status'             => 'blocked',
            'code'               => 423,
            'reason'             => 'proposal_sla_breach',
            'message'            => $check['message'],
            'blocking_cid_ids'   => $check['blocking_cid_ids'],
            'blocking_count'     => (int)$check['count'],
            'oldest_age_hours'   => (float)$check['oldest_age_hours'],
            'blocking_leads'     => $blocking_details,
            'unblock_options'    => array(
                array(
                    'action' => 'upload_proposal',
                    'label'  => 'Upload the proposal for each lead',
                    'route'  => '/api/proposal/sla/upload',
                ),
                array(
                    'action' => 'request_extension',
                    'label'  => 'Request a 24 hour extension (max one per SLA)',
                    'route'  => '/api/proposal/sla/extend',
                ),
            ),
            'help_url'           => '/docs/proposal-sla-rules',
        );

        return array('allowed' => false, 'response' => $payload);
    }

    /**
     * Build the human readable list of blocking leads for the BD UI.
     * Returns array of { cid_id, school_name, opened_at, age_hours,
     * proposal_due_at, extension_available }.
     */
    private static function _describe_blocking_leads($cid_ids, &$CI)
    {
        if (empty($cid_ids)) {
            return array();
        }

        $CI->db->select('p.cid_id, p.opened_at, p.proposal_due_at,
                         p.extension_count, p.sla_state,
                         cm.compname AS companyname, cm.compname AS school_name')
               ->from('proposal_sla_tracker p')
               ->join('init_call ic', 'ic.id = p.cid_id', 'left')
               ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
               ->where_in('p.cid_id', $cid_ids)
               ->where('p.sla_state', 'open')
               ->order_by('p.opened_at ASC');

        $rows = $CI->db->get()->result_array();
        $out = array();
        $now = time();
        foreach ($rows as $r) {
            $opened_ts = strtotime($r['opened_at']);
            $age_hours = round(($now - $opened_ts) / 3600, 1);
            $school = !empty($r['school_name']) ? $r['school_name']
                                                : ($r['companyname'] ?: 'Unknown school');
            $out[] = array(
                'cid_id'               => (int)$r['cid_id'],
                'school_name'          => $school,
                'opened_at'            => $r['opened_at'],
                'age_hours'            => $age_hours,
                'proposal_due_at'      => $r['proposal_due_at'],
                'extension_available'  => ((int)$r['extension_count'] === 0),
            );
        }
        return $out;
    }
}


/**
 * Drop-in replacement snippet for Planner_controller::submit_plan().
 *
 * Existing function header:
 *
 *   public function submit_plan() {
 *       $bd_uid    = $this->session->userdata('uid');
 *       $plan_date = $this->input->post('plan_date');
 *       $rows      = json_decode($this->input->post('rows'), true);
 *
 *       // ... existing logic ...
 *   }
 *
 * INSERT THIS BLOCK after the three lines above and before the
 * day-shape-lock check:
 *
 *   // === Migration 026 hard block ===
 *   require_once APPPATH.'patches/stem_bd_planner_block_patch.php';
 *   $block = Bd_planner_block_patch::check($bd_uid, $plan_date, $this);
 *   if (!$block['allowed']) {
 *       $this->output
 *            ->set_status_header(423, 'Locked')
 *            ->set_content_type('application/json')
 *            ->set_output(json_encode($block['response']));
 *       return;
 *   }
 *   // === end migration 026 block ===
 *
 * Same block goes at the top of add_task() (single-row insert path).
 *
 * No other planner code changes. The block is purely additive and
 * gracefully no-ops when migration 026 is not deployed (table missing
 * branch) or when BD is outside the pilot scope.
 *
 * ROLLBACK: delete the inserted block. The patch class file may stay
 * on disk; it has no side effects unless invoked.
 */
