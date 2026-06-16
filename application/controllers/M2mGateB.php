<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Meeting-to-Money (M2M) Assurance - Gate B: Proposal Commitment SLA
 * Additive build 2026-06-16. New controller, no existing code touched.
 *
 * Routes (see routes_m2m_assurance_20260616.php):
 *   GET  /api/m2m/gateb/committed_not_sent?as_of=  DAILY T+2 tracker
 *   POST /api/m2m/gateb/mark_sent                  logs proposal sent date
 *   GET  /api/m2m/gateb/probe                       health probe
 *
 * Committed date = mom_data.proposal_committed_date (the MoM promise).
 * Sent date      = mom_data.m2m_proposal_sent_date (also mirrors to
 *                  proposal_shared_date when that existing column is empty).
 * SLA = m2m_config.proposal_sla_working_days WORKING days (Mon-Fri).
 * WARN at day-3, BREACH at >= SLA. On breach -> DQ9 (idempotent per cid,month).
 *
 * Nothing hardcoded. ASCII only. Rupees written "Rs". "percent" spelled out.
 */
class M2m_gate_b extends CI_Controller
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
            'gate' => 'B',
            'name' => 'proposal_commitment_sla',
            'sla_working_days' => (int)$this->cfg('proposal_sla_working_days', 5),
            'ts'   => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * Inclusive count of working days (Mon-Fri) between two Y-m-d dates.
     * Weekends skipped. Returns elapsed full working days from $from up to $to.
     */
    private function working_days_between($from, $to)
    {
        $start = strtotime($from . ' 00:00:00');
        $end   = strtotime($to . ' 00:00:00');
        if ($start === false || $end === false || $end < $start) return 0;
        $days = 0;
        for ($t = $start; $t <= $end; $t += 86400) {
            $dow = (int)date('N', $t); // 1=Mon .. 7=Sun
            if ($dow >= 1 && $dow <= 5) $days++;
        }
        // elapsed = working days strictly after the committed day
        return max(0, $days - 1);
    }

    /**
     * GET /api/m2m/gateb/committed_not_sent?as_of=YYYY-MM-DD
     * DAILY tracker. Sent blank => clock running. WARN day-3, BREACH at SLA.
     */
    public function committed_not_sent()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $as_of = (string)$this->input->get('as_of');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $as_of)) $as_of = date('Y-m-d');

        $sla  = (int)$this->cfg('proposal_sla_working_days', 5);
        $warn = max(1, $sla - 2); // day-3 warn when SLA=5

        $rows = $this->db
            ->select('m.id AS mom_id, m.init_cmpid AS cid_id,
                      m.user_id AS bd_uid,
                      m.proposal_committed_date,
                      m.m2m_proposal_sent_date,
                      m.proposal_shared_date', false)
            ->from('mom_data m')
            ->where('m.proposal_committed_date IS NOT NULL')
            ->where("m.proposal_committed_date <>", '0000-00-00')
            ->where('m.proposal_committed_date <=', $as_of)
            ->order_by('m.proposal_committed_date', 'asc')
            ->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $committed = (string)$r['proposal_committed_date'];
            $sent = $r['m2m_proposal_sent_date'];
            if ($sent === null || $sent === '' || $sent === '0000-00-00') {
                $sent = ($r['proposal_shared_date'] && $r['proposal_shared_date'] !== '0000-00-00')
                    ? (string)$r['proposal_shared_date'] : null;
            }

            if ($sent !== null) {
                $elapsed = $this->working_days_between($committed, $sent);
                $status  = ($elapsed <= $sla) ? 'SENT' : 'BREACH';
                $block_reason = ($status === 'BREACH') ? 'sent_after_sla' : '';
            } else {
                $elapsed = $this->working_days_between($committed, $as_of);
                if ($elapsed >= $sla)        { $status = 'BREACH'; $block_reason = 'not_sent_within_sla'; }
                elseif ($elapsed >= $warn)   { $status = 'WARN';   $block_reason = 'approaching_sla'; }
                else                         { $status = 'OK';     $block_reason = ''; }
            }

            // DQ9 on breach (idempotent per cid,month).
            $dq9 = false;
            if ($status === 'BREACH') {
                $dq9 = $this->_fire_dq9((int)$r['cid_id'], (int)$r['bd_uid'], $committed, $sent, $as_of);
            }

            $out[] = [
                'bd'             => (int)$r['bd_uid'],
                'cid'            => (int)$r['cid_id'],
                'mom_id'         => (int)$r['mom_id'],
                'committed_date' => $committed,
                'sent_date'      => $sent,
                'days_elapsed'   => $elapsed,
                'sla'            => $sla,
                'status'         => $status,
                'block_reason'   => $block_reason,
                'dq9_fired'      => $dq9,
            ];
        }

        return $this->_json([
            'ok'    => true,
            'as_of' => $as_of,
            'sla'   => $sla,
            'count' => count($out),
            'rows'  => $out,
            'ts'    => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * POST /api/m2m/gateb/mark_sent
     * Body: mom_id, sent_date (YYYY-MM-DD). Logs sent date; mirrors to
     * existing proposal_shared_date only when that column is empty.
     */
    public function mark_sent()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $mom_id    = (int)$this->input->post('mom_id');
        $sent_date = trim((string)$this->input->post('sent_date'));
        if ($mom_id <= 0) return $this->_json(['ok' => false, 'error' => 'mom_id_required'], 422);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sent_date)) {
            return $this->_json(['ok' => false, 'error' => 'sent_date_must_be_YYYY_MM_DD'], 422);
        }

        $mom = $this->db->select('id, proposal_shared_date')->from('mom_data')
            ->where('id', $mom_id)->get()->row_array();
        if (!$mom) return $this->_json(['ok' => false, 'error' => 'mom_not_found', 'mom_id' => $mom_id], 422);

        $set = ['m2m_proposal_sent_date' => $sent_date];
        $existing = $mom['proposal_shared_date'];
        if ($existing === null || $existing === '' || $existing === '0000-00-00') {
            $set['proposal_shared_date'] = $sent_date;
        }
        $this->db->where('id', $mom_id)->update('mom_data', $set);

        return $this->_json([
            'ok'         => true,
            'mom_id'     => $mom_id,
            'sent_date'  => $sent_date,
            'mirrored_shared_date' => isset($set['proposal_shared_date']),
            'ts'         => date('Y-m-d H:i:s'),
        ], 200);
    }

    // ---------- DQ9 ----------
    private function _fire_dq9($cid_id, $bd_uid, $committed, $sent, $as_of)
    {
        if ($cid_id <= 0) return false;
        $month = date('Y-m', strtotime($as_of));
        $sent_label = ($sent === null) ? 'not yet sent' : $sent;
        $reason = 'Committed proposal not sent within SLA. Committed ' . $committed
                . ', sent ' . $sent_label . '. No incentive this month; CM logs root cause '
                . '(content, pricing, approval, or neglect) plus 2-week probation.';
        $cid_sql = (int)$cid_id;
        $sql = "INSERT IGNORE INTO m2m_disqualifier_log
                (dq_code, subject_uid, subject_role, cid_id, period_month, reason, source_tracker, triggered_at, auto)
                VALUES ('DQ9', ?, 'BD', " . $cid_sql . ", ?, ?, 'committed_not_sent', NOW(), 1)";
        $this->db->query($sql, [(int)$bd_uid, $month, $reason]);
        return ($this->db->affected_rows() > 0);
    }

    protected function _json($data, $code)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
