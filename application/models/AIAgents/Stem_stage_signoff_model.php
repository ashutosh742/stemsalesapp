<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * StageSignoff_model
 *
 * Migration 022 - the 4 hard signoff gates that put the line manager
 * truly accountable for stage transitions in the lead journey.
 *
 *   G1  cstatus 6 -> 7   Positive to Proposal Sent
 *   G2  cstatus 7 -> 8   Proposal Sent to Open RPEM
 *   G3  cstatus 8 -> 9   Open RPEM to Very Positive
 *   G4  cstatus 9 -> 12  Very Positive to Won
 *
 * Per founder decision (16 May 2026): all 4 gates are HARD from day one.
 * RM may bypass any gate with a written reason. Every bypass emails the
 * RM and stemlearning@gmail.com immediately, plus surfaces on RM scorecard.
 *
 * Author: STEM Build Agent. Date: 16 May 2026.
 */
class StageSignoff_model extends CI_Model {

    const SLA_HOURS = [
        'G1' => 24,
        'G2' => 24,
        'G3' => 24,
        'G4' => 48,
    ];

    const HOP_MAP = [
        'G1' => [6, 7],
        'G2' => [7, 8],
        'G3' => [8, 9],
        'G4' => [9, 12],
    ];

    const REJECT_REASONS_G1 = [
        'cohort_too_small','budget_unrealistic','no_r2b_yet',
        'dm_not_aligned','proposal_template_outdated','missing_fitment_offer',
        'other_with_note'
    ];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('email');
        $this->load->model('LineManagerScorecard_model', 'sm');
    }

    /**
     * BD requests a stage signoff. Validates required payload per gate.
     */
    public function request_signoff($payload) {
        $required = ['init_call_id','bd_uid','gate_code'];
        foreach ($required as $k) {
            if (empty($payload[$k])) return ['ok' => false, 'error' => "missing_$k"];
        }
        $gate = strtoupper($payload['gate_code']);
        if (!isset(self::HOP_MAP[$gate])) return ['ok' => false, 'error' => 'unknown_gate'];

        // Validate current cstatus matches gate's from-state
        $lead = $this->db->where('id', $payload['init_call_id'])->get('init_call')->row_array();
        if (!$lead) return ['ok' => false, 'error' => 'lead_not_found'];
        $expected_from = self::HOP_MAP[$gate][0];
        if ((int)$lead['cstatus'] !== $expected_from) {
            return ['ok' => false, 'error' => "lead_not_in_cstatus_$expected_from", 'current' => (int)$lead['cstatus']];
        }

        // No duplicate pending signoff on same lead and gate
        $dup = $this->db->where('init_call_id', $payload['init_call_id'])
                        ->where('gate_code', $gate)
                        ->where('status', 'pending')
                        ->get('lead_stage_signoff')
                        ->row_array();
        if ($dup) return ['ok' => false, 'error' => 'pending_signoff_exists', 'signoff_id' => (int)$dup['id']];

        // Gate-specific validation
        $err = $this->_validate_gate_payload($gate, $payload, $lead);
        if ($err) return ['ok' => false, 'error' => $err];

        // Resolve CM uid (assigned line manager for this BD)
        $cm_uid = $this->_cm_for_bd($payload['bd_uid']);
        if (!$cm_uid) return ['ok' => false, 'error' => 'no_cm_assigned'];

        $row = [
            'init_call_id'         => (int)$payload['init_call_id'],
            'bd_uid'               => (int)$payload['bd_uid'],
            'cm_uid'               => $cm_uid,
            'gate_code'            => $gate,
            'from_cstatus'         => $expected_from,
            'to_cstatus'           => self::HOP_MAP[$gate][1],
            'gate_strength'        => 'hard',
            'signoff_role'         => 'CM',
            'request_payload_json' => json_encode($payload),
            'proposal_doc_url'     => $payload['proposal_doc_url'] ?? null,
            'proposal_cohort_count'=> $payload['proposal_cohort_count'] ?? null,
            'proposal_budget_rs'   => $payload['proposal_budget_rs'] ?? null,
            'proposal_decision_date' => $payload['proposal_decision_date'] ?? null,
            'r2b_status'           => $payload['r2b_status'] ?? null,
            'expected_close_date'  => $payload['expected_close_date'] ?? null,
            'win_probability'      => $payload['win_probability'] ?? null,
            'contract_value_rs'    => $payload['contract_value_rs'] ?? null,
            'work_order_target_date' => $payload['work_order_target_date'] ?? null,
            'payment_plan_json'    => isset($payload['payment_plan']) ? json_encode($payload['payment_plan']) : null,
            'sla_hours'            => self::SLA_HOURS[$gate],
            'requested_at'         => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('lead_stage_signoff', $row);
        $signoff_id = (int)$this->db->insert_id();

        // Update init_call.current_signoff_id pointer
        $this->db->where('id', $row['init_call_id'])
            ->update('init_call', ['current_signoff_id' => $signoff_id]);

        return ['ok' => true, 'signoff_id' => $signoff_id, 'sla_hours' => self::SLA_HOURS[$gate]];
    }

    /**
     * Validate gate-specific required fields.
     * Returns error code string or null on success.
     */
    private function _validate_gate_payload($gate, $payload, $lead) {
        switch ($gate) {
            case 'G1':
                if (empty($payload['proposal_doc_url'])) return 'g1_proposal_doc_required';
                if (empty($payload['proposal_cohort_count'])) return 'g1_cohort_count_required';
                if (empty($payload['proposal_budget_rs'])) return 'g1_budget_required';
                if (empty($payload['proposal_decision_date'])) return 'g1_decision_date_required';
                return null;
            case 'G2':
                if (empty($payload['r2b_status'])) return 'g2_r2b_status_required';
                // Founder rule: cannot move to cstatus 8 with r2b_status='not_yet'
                if ($payload['r2b_status'] === 'not_yet') return 'g2_r2b_must_be_shared_or_better';
                return null;
            case 'G3':
                if (empty($payload['expected_close_date'])) return 'g3_close_date_required';
                if (empty($payload['win_probability'])) return 'g3_win_probability_required';
                // Plausibility: >90 days out blocks
                $days_out = (strtotime($payload['expected_close_date']) - time()) / 86400;
                if ($days_out > 90) return 'g3_close_date_too_far_out';
                // CSR doubtful blocks (per migration 021 rule)
                $csr = $this->db->where('mom_id', $payload['mom_id'] ?? 0)
                                ->order_by('checked_at','DESC')->limit(1)
                                ->get('mom_csr_check')->row_array();
                if ($csr && in_array($csr['csr_verdict'], ['doubtful','not_csr'])) {
                    return 'g3_csr_verdict_blocks';
                }
                return null;
            case 'G4':
                $req = ['contract_value_rs','work_order_target_date','payment_plan'];
                foreach ($req as $f) if (empty($payload[$f])) return "g4_{$f}_required";
                // 20% variance check against last proposal_budget_rs
                $last_prop = $this->db->where('init_call_id', $payload['init_call_id'])
                                      ->where('gate_code', 'G1')
                                      ->where('status', 'approved')
                                      ->order_by('decided_at','DESC')->limit(1)
                                      ->get('lead_stage_signoff')->row_array();
                if ($last_prop && $last_prop['proposal_budget_rs'] > 0) {
                    $variance = abs($payload['contract_value_rs'] - $last_prop['proposal_budget_rs'])
                              / $last_prop['proposal_budget_rs'] * 100;
                    if ($variance > 20) return 'g4_variance_over_20pct';
                }
                $pp = $payload['payment_plan'];
                $req_pp = ['advance_pct','milestones','gst_status','vendor_form_status'];
                foreach ($req_pp as $f) if (!isset($pp[$f]) || $pp[$f] === '') return "g4_payment_plan_$f";
                return null;
        }
        return null;
    }

    /**
     * CM (or auto-promoted RM) decides. Action: approve, reject, request_edit.
     */
    public function decide($signoff_id, $decided_by_uid, $action, $reason_code = null, $note = null, $coaching_note = null) {
        $s = $this->db->where('id', $signoff_id)->get('lead_stage_signoff')->row_array();
        if (!$s) return ['ok' => false, 'error' => 'signoff_not_found'];
        if ($s['status'] !== 'pending') return ['ok' => false, 'error' => 'already_decided', 'current' => $s['status']];

        if (!in_array($action, ['approve','reject','request_edit'])) {
            return ['ok' => false, 'error' => 'invalid_action'];
        }
        $status_map = ['approve' => 'approved', 'reject' => 'rejected', 'request_edit' => 'request_edit'];
        $update = [
            'status'              => $status_map[$action],
            'decision_reason_code'=> $reason_code,
            'decision_note'       => $note,
            'coaching_note'       => $coaching_note,
            'decided_at'          => date('Y-m-d H:i:s'),
            'decided_by_uid'      => (int)$decided_by_uid,
        ];
        $this->db->where('id', $signoff_id)->update('lead_stage_signoff', $update);

        // On approve: move the lead cstatus
        if ($action === 'approve') {
            $this->_advance_lead($s);
        }
        // On reject or request_edit: clear current_signoff_id pointer so BD can refile
        if (in_array($action, ['reject','request_edit'])) {
            $this->db->where('id', $s['init_call_id'])
                ->update('init_call', ['current_signoff_id' => null]);
        }

        return ['ok' => true, 'signoff_id' => (int)$signoff_id, 'new_status' => $update['status']];
    }

    /**
     * Move lead cstatus on approve.
     * G4 also triggers work_order INSERT (replaces legacy quirk).
     */
    private function _advance_lead($s) {
        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $s['init_call_id'])->update('init_call', [
            'cstatus'             => (int)$s['to_cstatus'],
            'current_signoff_id'  => null,
            'r2b_status'          => $s['r2b_status'] ?: $this->_current_r2b($s['init_call_id']),
            'r2b_shared_at'       => in_array($s['r2b_status'], ['shared','accepted','accepted_with_changes']) ? $now : null,
            'expected_close_date' => $s['expected_close_date'] ?: $this->_current_close_date($s['init_call_id']),
        ]);

        // Insert into lead_progression_log so existing migration 012 surfaces pick it up
        $this->db->insert('lead_progression_log', [
            'cid_id'          => (int)$s['init_call_id'],
            'bd_uid'          => (int)$s['bd_uid'],
            'cm_uid'          => (int)$s['cm_uid'],
            'from_cstatus'    => (int)$s['from_cstatus'],
            'to_cstatus'      => (int)$s['to_cstatus'],
            'creation_path_hint' => 'stage_signoff_' . $s['gate_code'],
            'closed_value_rs' => $s['contract_value_rs'],
            'requires_cm_review' => 0, // already CM-approved
            'created_at'      => $now,
        ]);

        // G4 Won: insert work_order row
        if ($s['gate_code'] === 'G4' && (int)$s['to_cstatus'] === 12) {
            $this->db->insert('work_order', [
                'init_call_id' => (int)$s['init_call_id'],
                'bd_uid'       => (int)$s['bd_uid'],
                'cm_uid'       => (int)$s['cm_uid'],
                'contract_value_rs' => (float)$s['contract_value_rs'],
                'wo_target_date'   => $s['work_order_target_date'],
                'payment_plan_json' => $s['payment_plan_json'],
                'status'        => 'pending_payment',
                'source_signoff_id' => (int)$s['id'],
                'created_at'    => $now,
            ]);
        }
    }

    /**
     * RM bypass. Mandatory reason. Emails RM + admin immediately.
     */
    public function bypass($signoff_id, $rm_uid, $reason) {
        if (empty($reason) || strlen(trim($reason)) < 10) {
            return ['ok' => false, 'error' => 'reason_too_short'];
        }
        $s = $this->db->where('id', $signoff_id)->get('lead_stage_signoff')->row_array();
        if (!$s) return ['ok' => false, 'error' => 'signoff_not_found'];
        if ($s['status'] !== 'pending') return ['ok' => false, 'error' => 'already_decided'];

        // RM role check
        $rm = $this->db->select('uid, type_id, fname, email')->where('uid', $rm_uid)->get('user')->row_array();
        if (!$rm || (int)$rm['type_id'] !== 23) return ['ok' => false, 'error' => 'not_rm'];

        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $signoff_id)->update('lead_stage_signoff', [
            'status'              => 'bypassed',
            'bypassed_by_rm_uid'  => (int)$rm_uid,
            'bypass_reason'       => $reason,
            'bypassed_at'         => $now,
            'decided_at'          => $now,
            'decided_by_uid'      => (int)$rm_uid,
        ]);

        // Log to bypass table
        $log_id = null;
        $this->db->insert('signoff_bypass_log', [
            'signoff_id'      => (int)$signoff_id,
            'init_call_id'    => (int)$s['init_call_id'],
            'bd_uid'          => (int)$s['bd_uid'],
            'cm_uid'          => $s['cm_uid'] ? (int)$s['cm_uid'] : null,
            'rm_uid'          => (int)$rm_uid,
            'gate_code'       => $s['gate_code'],
            'bypass_reason'   => $reason,
            'bypassed_at'     => $now,
        ]);
        $log_id = (int)$this->db->insert_id();

        // Advance the lead just like an approve
        $this->_advance_lead($s);

        // Email RM and admin
        $this->_email_bypass($rm, $s, $reason, $log_id);

        return ['ok' => true, 'log_id' => $log_id];
    }

    private function _email_bypass($rm, $s, $reason, $log_id) {
        $bd = $this->db->select('fname,email')->where('uid', $s['bd_uid'])->get('user')->row_array();
        $cm = $s['cm_uid'] ? $this->db->select('fname,email')->where('uid', $s['cm_uid'])->get('user')->row_array() : null;
        $lead = $this->db->select('compny_nm, compny_loction')->where('id', $s['init_call_id'])->get('init_call')->row_array();

        $subject = "RM bypass on signoff " . $s['gate_code'] . " - " . ($lead['compny_nm'] ?? 'lead#' . $s['init_call_id']);
        $body  = "RM " . $rm['fname'] . " just bypassed stage signoff " . $s['gate_code'] . ".\n\n";
        $body .= "Lead: " . ($lead['compny_nm'] ?? '') . " (" . ($lead['compny_loction'] ?? '') . ")\n";
        $body .= "BD: " . ($bd['fname'] ?? '') . "\n";
        $body .= "Assigned CM: " . ($cm['fname'] ?? 'none') . "\n";
        $body .= "Gate hop: cstatus " . $s['from_cstatus'] . " to " . $s['to_cstatus'] . "\n";
        $body .= "Reason: " . $reason . "\n\n";
        $body .= "Bypass log id: " . $log_id . "\n";
        $body .= "Signoff id: " . $s['id'] . "\n";
        $body .= "Timestamp: " . date('c') . "\n\n";
        $body .= "This bypass costs the RM 3 points on this day's scorecard and surfaces on the Monday weekly funnel cron if 3 or more bypasses happen this week.";

        $recipients = ['stemlearning@gmail.com'];
        if (!empty($rm['email'])) $recipients[] = $rm['email'];

        $this->email->from('no-reply@stemapp.in', 'STEM Stage Signoff');
        $this->email->to(implode(',', $recipients));
        $this->email->subject($subject);
        $this->email->message($body);
        $ok = $this->email->send();

        $this->db->where('id', $log_id)->update('signoff_bypass_log', [
            'email_sent_at' => $ok ? date('Y-m-d H:i:s') : null,
            'email_recipients' => implode(',', $recipients),
        ]);
    }

    /**
     * Cron-callable: process stuck signoffs.
     *   4-hour business: push to RM
     *  24-hour: email to RM + admin
     *  48-hour: auto-escalate (flip signoff_role to RM, deduct CM day_score)
     */
    public function sweep_stuck_alarms() {
        // rimlyproof_sweepfix_20260609: use real lead_stage_signoff columns.
        $now = time();
        $rows = $this->db->where('manager_action', 'pending')->get('lead_stage_signoff')->result_array();
        $stats = ['pushed_4h' => 0, 'emailed_24h' => 0, 'auto_escalated' => 0];

        foreach ($rows as $s) {
            $req_at = isset($s['bd_requested_at']) ? $s['bd_requested_at'] : date('Y-m-d H:i:s');
            $age = ($now - strtotime($req_at)) / 3600;
            $in_business_window = $this->_in_business_hours($req_at);
            if ($age >= 4 && empty($s['alarm_4h_sent_at']) && $in_business_window) {
                $this->_alarm_4h($s);
                $stats['pushed_4h']++;
            }
            if ($age >= 24 && empty($s['alarm_24h_sent_at'])) {
                $this->_alarm_24h($s);
                $stats['emailed_24h']++;
            }
            if ($age >= 48 && empty($s['auto_escalated_at'])) {
                $this->_auto_escalate($s);
                $stats['auto_escalated']++;
            }
        }
        return $stats;
    }

    private function _alarm_4h($s) {
        // rimlyproof_sweepfix_20260609
        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $s['id'])->update('lead_stage_signoff', ['alarm_4h_sent_at' => $now]);
        $mgr_uid = isset($s['manager_uid']) ? $s['manager_uid'] : null;
        $lead_id = isset($s['cid_id']) ? $s['cid_id'] : 0;
        $gate    = isset($s['to_cstatus']) ? ('cstatus_' . $s['to_cstatus']) : 'stage';
        if ($mgr_uid) {
            $this->load->library('NotificationDispatcher');
            $this->notificationdispatcher->push_to_rm_of_cm($mgr_uid, [
                'title' => 'Signoff stuck 4h',
                'body'  => 'BD signoff request on lead ' . $lead_id . ' gate ' . $gate . ' waiting 4h with CM.',
            ]);
        }
    }

    private function _alarm_24h($s) {
        // rimlyproof_sweepfix_20260609
        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $s['id'])->update('lead_stage_signoff', ['alarm_24h_sent_at' => $now]);
        $mgr_uid = isset($s['manager_uid']) ? $s['manager_uid'] : null;
        $lead_id = isset($s['cid_id']) ? $s['cid_id'] : 0;
        $gate    = isset($s['to_cstatus']) ? ('cstatus_' . $s['to_cstatus']) : 'stage';
        $req_at  = isset($s['bd_requested_at']) ? $s['bd_requested_at'] : $now;
        $cm = $mgr_uid ? $this->db->select('fname,email')->where('uid', $mgr_uid)->get('user')->row_array() : null;
        $rm_email = $mgr_uid ? $this->_rm_email_for_cm($mgr_uid) : null;
        $recipients = ['stemlearning@gmail.com'];
        if ($rm_email) $recipients[] = $rm_email;
        $subject = 'Signoff over 24h - lead ' . $lead_id . ' gate ' . $gate;
        $body  = "CM " . ($cm['fname'] ?? 'unassigned') . " has a signoff request open for over 24 hours.\n";
        $body .= "Gate: " . $gate . "\n";
        $body .= "Lead id: " . $lead_id . "\n";
        $body .= "Requested at: " . $req_at . "\n";
        $body .= "Auto-escalation to RM happens at 48 hours.\n";
        $this->email->from('no-reply@stemapp.in', 'STEM Stage Signoff');
        $this->email->to(implode(',', $recipients));
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();
    }

    private function _auto_escalate($s) {
        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $s['id'])->update('lead_stage_signoff', [
            'auto_escalated_at' => $now,
            'signoff_role'      => 'RM',
        ]);
        // The 5-point CM deduction is reflected automatically via scorecard refresh
        // because signoffs_over_48h count will include this row.
    }

    // -----------------------------------------------------------------
    // Read helpers
    // -----------------------------------------------------------------

    public function queue_for_cm($cm_uid, $rm_uid = null, $status = 'pending', $limit = 50) {
        // v_signoff_pending_summary aliases manager_uid->cm_uid, manager_action->status
        $this->db->order_by('requested_at', 'ASC')->limit((int)$limit);
        if (!empty($cm_uid)) {
            $this->db->where('cm_uid', (int)$cm_uid);
        }
        if (!empty($status)) {
            $this->db->where('status', $status);
        }
        return $this->db->get('v_signoff_pending_summary')->result_array();
    }

    public function pending_for_bd($bd_uid) {
        return $this->db->order_by('requested_at', 'DESC')
            ->where('bd_uid', $bd_uid)
            ->where('status', 'pending')
            ->get('v_signoff_pending_summary')
            ->result_array();
    }

    public function bypass_log($from = null, $to = null, $rm_uid = null) {
        if (!$from) $from = date('Y-m-d', strtotime('monday this week'));
        if (!$to)   $to   = date('Y-m-d');
        $this->db->where('bypassed_at >=', $from . ' 00:00:00')
                 ->where('bypassed_at <=', $to . ' 23:59:59');
        if ($rm_uid) $this->db->where('rm_uid', $rm_uid);
        return $this->db->order_by('bypassed_at', 'DESC')
                 ->get('signoff_bypass_log')->result_array();
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    private function _cm_for_bd($bd_uid) {
        $r = $this->db->select('cm_uid')->where('uid', $bd_uid)->get('user')->row_array();
        return $r ? (int)$r['cm_uid'] : null;
    }

    private function _rm_email_for_cm($cm_uid) {
        if (!$cm_uid) return null;
        $sql = "
            SELECT rm.email
            FROM user cm
            LEFT JOIN user rm ON rm.uid = cm.rm_uid
            WHERE cm.uid = ? AND rm.type_id = 23
        ";
        $r = $this->db->query($sql, [$cm_uid])->row_array();
        return $r ? $r['email'] : null;
    }

    private function _in_business_hours($timestamp_str) {
        $h = (int)date('G', strtotime($timestamp_str));
        $dow = (int)date('N', strtotime($timestamp_str));
        return ($dow <= 5 && $h >= 9 && $h < 19);
    }

    private function _current_r2b($init_call_id) {
        $r = $this->db->select('r2b_status')->where('id', $init_call_id)->get('init_call')->row_array();
        return $r ? $r['r2b_status'] : null;
    }

    private function _current_close_date($init_call_id) {
        $r = $this->db->select('expected_close_date')->where('id', $init_call_id)->get('init_call')->row_array();
        return $r ? $r['expected_close_date'] : null;
    }
}
