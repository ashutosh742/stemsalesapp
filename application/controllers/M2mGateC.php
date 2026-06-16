<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Meeting-to-Money (M2M) Assurance - Gate C: Manager Closure Ownership
 * Additive build 2026-06-16. New controller, no existing code touched.
 *
 * Routes (see routes_m2m_assurance_20260616.php):
 *   POST /api/m2m/gatec/touch        RM/CM upsert last touch + next action + verdict date
 *   GET  /api/m2m/gatec/adherence    WEEKLY Mgr Follow-Up Adherence tracker
 *   GET  /api/m2m/gatec/scorecard    MONTHLY Closure Scorecard
 *   GET  /api/m2m/gatec/probe        health probe
 *
 * Stores into m2m_manager_closure (one row per active Status 5-8 lead).
 * DQ10: Status 5-8 lead, no manager touch beyond one SLA cycle
 * (manager_touch_sla_days) -> disqualifier ledger (idempotent per cid,month).
 *
 * Nothing hardcoded. ASCII only. "percent" spelled out. Rupees "Rs".
 */
class M2m_gate_c extends CI_Controller
{
    protected $token;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('Bearer_auth');
        $this->load->helper('url');
        $this->token = $this->bearer_auth->get_bearer_token();
    }

    private function cfg($key, $default = null)
    {
        $row = $this->db->select('cfg_value')->from('m2m_config')
            ->where('cfg_key', $key)->get()->row_array();
        return ($row && isset($row['cfg_value'])) ? $row['cfg_value'] : $default;
    }

    public function probe()
    {
        $this->_json([
            'ok'   => true,
            'gate' => 'C',
            'name' => 'manager_closure_ownership',
            'manager_touch_sla_days' => (int)$this->cfg('manager_touch_sla_days', 7),
            'ts'   => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * POST /api/m2m/gatec/touch
     * Body: cid_id, manager_uid, manager_role, lead_status?,
     *       last_touch_date?, next_action_text?, next_action_date?,
     *       close_or_kill_date?, verdict?
     * Upsert per cid (m2m_manager_closure.uniq_cid).
     */
    public function touch()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $cid_id      = (int)$this->input->post('cid_id');
        $manager_uid = (int)$this->input->post('manager_uid');
        $manager_role= trim((string)$this->input->post('manager_role'));
        if ($cid_id <= 0)      return $this->_json(['ok' => false, 'error' => 'cid_id_required'], 422);
        if ($manager_uid <= 0) return $this->_json(['ok' => false, 'error' => 'manager_uid_required'], 422);

        $last_touch  = $this->_date($this->input->post('last_touch_date'));
        if ($last_touch === null) $last_touch = date('Y-m-d');
        $next_text   = trim((string)$this->input->post('next_action_text'));
        $next_date   = $this->_date($this->input->post('next_action_date'));
        $cok_date    = $this->_date($this->input->post('close_or_kill_date'));
        $lead_status = $this->input->post('lead_status');
        $lead_status = ($lead_status === null || $lead_status === '') ? null : (int)$lead_status;
        $verdict     = strtolower(trim((string)$this->input->post('verdict')));
        if (!in_array($verdict, ['open', 'won', 'killed', 'pending'], true)) $verdict = 'open';

        $idle = 0; // touched now => idle resets

        $existing = $this->db->select('id')->from('m2m_manager_closure')
            ->where('cid_id', $cid_id)->get()->row_array();

        $row = [
            'cid_id'             => $cid_id,
            'lead_status'        => $lead_status,
            'manager_uid'        => $manager_uid,
            'manager_role'       => ($manager_role !== '') ? $manager_role : null,
            'last_touch_date'    => $last_touch,
            'next_action_text'   => ($next_text !== '') ? $next_text : null,
            'next_action_date'   => $next_date,
            'close_or_kill_date' => $cok_date,
            'verdict'            => $verdict,
            'idle_days'          => $idle,
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->db->where('cid_id', $cid_id)->update('m2m_manager_closure', $row);
            $id = (int)$existing['id'];
        } else {
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('m2m_manager_closure', $row);
            $id = (int)$this->db->insert_id();
        }

        return $this->_json([
            'ok'      => true,
            'id'      => $id,
            'cid_id'  => $cid_id,
            'verdict' => $verdict,
            'last_touch_date' => $last_touch,
            'ts'      => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * GET /api/m2m/gatec/adherence?week=YYYY-MM-DD
     * WEEKLY tracker. Touched >=1x within the week of the given date => OK,
     * else NO TOUCH. Idle days computed from last_touch_date to week-end.
     */
    public function adherence()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $week = (string)$this->input->get('week');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $week)) $week = date('Y-m-d');
        // Monday..Sunday window containing $week.
        $dow = (int)date('N', strtotime($week));
        $week_start = date('Y-m-d', strtotime($week . ' -' . ($dow - 1) . ' days'));
        $week_end   = date('Y-m-d', strtotime($week_start . ' +6 days'));

        $sla = (int)$this->cfg('manager_touch_sla_days', 7);

        $rows = $this->db->select('*')->from('m2m_manager_closure')
            ->order_by('manager_uid', 'asc')->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $lt = $r['last_touch_date'];
            $touched_this_week = ($lt !== null && $lt !== '' && $lt !== '0000-00-00'
                && $lt >= $week_start && $lt <= $week_end);

            if ($lt && $lt !== '0000-00-00') {
                $idle = (int)floor((strtotime($week_end) - strtotime($lt)) / 86400);
                if ($idle < 0) $idle = 0;
            } else {
                $idle = null;
            }

            $next_date = $r['next_action_date'];
            $adherence = $touched_this_week ? 'OK' : 'NO TOUCH';

            $dq10 = false;
            if (!$touched_this_week && $idle !== null && $idle > $sla) {
                $dq10 = $this->_fire_dq10($r, $week_end);
            }

            $out[] = [
                'manager'          => (int)$r['manager_uid'],
                'cid'              => (int)$r['cid_id'],
                'status'           => ($r['lead_status'] === null) ? null : (int)$r['lead_status'],
                'last_touch'       => ($lt && $lt !== '0000-00-00') ? $lt : null,
                'next_action_date' => ($next_date && $next_date !== '0000-00-00') ? $next_date : null,
                'idle_days'        => $idle,
                'adherence'        => $adherence,
                'verdict_date'     => ($r['close_or_kill_date'] && $r['close_or_kill_date'] !== '0000-00-00') ? $r['close_or_kill_date'] : null,
                'verdict'          => $r['verdict'],
                'dq10_fired'       => $dq10,
            ];
        }

        return $this->_json([
            'ok'         => true,
            'week_start' => $week_start,
            'week_end'   => $week_end,
            'sla_days'   => $sla,
            'count'      => count($out),
            'rows'       => $out,
            'ts'         => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * GET /api/m2m/gatec/scorecard?month=YYYY-MM
     * MONTHLY Closure Scorecard per manager.
     * Targets: on-time >= 90 percent, weekly touch >= 95 percent.
     */
    public function scorecard()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $month = (string)$this->input->get('month');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) $month = date('Y-m');
        $month_start = $month . '-01';
        $month_end   = date('Y-m-t', strtotime($month_start));

        $sla = (int)$this->cfg('proposal_sla_working_days', 5);

        $rows = $this->db->select('*')->from('m2m_manager_closure')
            ->get()->result_array();

        $by_mgr = [];
        foreach ($rows as $r) {
            $mgr = (int)$r['manager_uid'];
            if (!isset($by_mgr[$mgr])) {
                $by_mgr[$mgr] = [
                    'manager'         => $mgr,
                    'proposals_owned' => 0,
                    'sent_on_time'    => 0,
                    'touched_weekly'  => 0,
                    'closed_won'      => 0,
                ];
            }
            $by_mgr[$mgr]['proposals_owned']++;

            // touched_weekly: last touch within this month counts as touched.
            $lt = $r['last_touch_date'];
            if ($lt && $lt !== '0000-00-00' && $lt >= $month_start && $lt <= $month_end) {
                $by_mgr[$mgr]['touched_weekly']++;
            }
            if ($r['verdict'] === 'won') {
                $by_mgr[$mgr]['closed_won']++;
            }
        }

        // sent_on_time joins to mom_data committed-vs-sent for this manager's cids.
        foreach ($by_mgr as $mgr => &$agg) {
            $cids = array_map(function ($r) { return (int)$r['cid_id']; },
                array_filter($rows, function ($r) use ($mgr) { return (int)$r['manager_uid'] === $mgr; }));
            if (!empty($cids)) {
                $on_time = $this->_sent_on_time_count($cids, $sla, $month_start, $month_end);
                $agg['sent_on_time'] = $on_time;
            }
        }
        unset($agg);

        $out = [];
        foreach ($by_mgr as $agg) {
            $owned = max(1, (int)$agg['proposals_owned']);
            $on_time_pct = round(($agg['sent_on_time']   / $owned) * 100, 1);
            $touch_pct   = round(($agg['touched_weekly'] / $owned) * 100, 1);
            $win_pct     = round(($agg['closed_won']     / $owned) * 100, 1);
            $out[] = [
                'manager'         => (int)$agg['manager'],
                'proposals_owned' => (int)$agg['proposals_owned'],
                'sent_on_time'    => (int)$agg['sent_on_time'],
                'touched_weekly'  => (int)$agg['touched_weekly'],
                'closed_won'      => (int)$agg['closed_won'],
                'on_time_pct'     => $on_time_pct,
                'touch_pct'       => $touch_pct,
                'win_pct'         => $win_pct,
                'on_time_target_met' => ($on_time_pct >= 90),
                'touch_target_met'   => ($touch_pct >= 95),
            ];
        }

        return $this->_json([
            'ok'      => true,
            'month'   => $month,
            'targets' => ['on_time_pct' => 90, 'touch_pct' => 95],
            'count'   => count($out),
            'rows'    => $out,
            'ts'      => date('Y-m-d H:i:s'),
        ], 200);
    }

    // ---------- helpers ----------
    private function _sent_on_time_count($cids, $sla, $month_start, $month_end)
    {
        // mom_data links the lead via init_cmpid (not cid_id). $cids come from
        // m2m_manager_closure.cid_id, which equals mom_data.init_cmpid.
        $rows = $this->db->select('init_cmpid, proposal_committed_date, m2m_proposal_sent_date, proposal_shared_date', false)
            ->from('mom_data')
            ->where_in('init_cmpid', $cids)
            ->where('proposal_committed_date IS NOT NULL')
            ->get()->result_array();
        $n = 0;
        foreach ($rows as $r) {
            $committed = (string)$r['proposal_committed_date'];
            if ($committed === '' || $committed === '0000-00-00') continue;
            $sent = $r['m2m_proposal_sent_date'];
            if ($sent === null || $sent === '' || $sent === '0000-00-00') {
                $sent = ($r['proposal_shared_date'] && $r['proposal_shared_date'] !== '0000-00-00')
                    ? (string)$r['proposal_shared_date'] : null;
            }
            if ($sent === null) continue;
            if ($sent < $month_start || $sent > $month_end) continue;
            if ($this->_working_days($committed, $sent) <= $sla) $n++;
        }
        return $n;
    }

    private function _working_days($from, $to)
    {
        $start = strtotime($from . ' 00:00:00');
        $end   = strtotime($to . ' 00:00:00');
        if ($start === false || $end === false || $end < $start) return 0;
        $days = 0;
        for ($t = $start; $t <= $end; $t += 86400) {
            $dow = (int)date('N', $t);
            if ($dow >= 1 && $dow <= 5) $days++;
        }
        return max(0, $days - 1);
    }

    private function _fire_dq10($r, $as_of)
    {
        $cid_id = (int)$r['cid_id'];
        if ($cid_id <= 0) return false;
        $mgr = (int)$r['manager_uid'];
        $month = date('Y-m', strtotime($as_of));
        $role = ($r['manager_role'] !== null && $r['manager_role'] !== '') ? $r['manager_role'] : 'RM';
        $status = ($r['lead_status'] === null) ? 'post-proposal' : ('Status ' . (int)$r['lead_status']);
        $reason = 'Manager post-proposal lead with no touch beyond one SLA cycle (' . $status
                . '). RM scorecard impact; auto-escalate per ladder (RM then CEO/NSH).';
        $cid_sql = (int)$cid_id;
        $sql = "INSERT IGNORE INTO m2m_disqualifier_log
                (dq_code, subject_uid, subject_role, cid_id, period_month, reason, source_tracker, triggered_at, auto)
                VALUES ('DQ10', ?, ?, " . $cid_sql . ", ?, ?, 'mgr_follow_up_adherence', NOW(), 1)";
        $this->db->query($sql, [$mgr, $role, $month, $reason]);
        return ($this->db->affected_rows() > 0);
    }

    private function _date($raw)
    {
        $s = trim((string)$raw);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) ? $s : null;
    }

    protected function _json($data, $code)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
