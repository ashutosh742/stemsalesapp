<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomV2_model
 *
 * Migration 021 - MoM v2 + Line Manager Accountability
 * Date: 16 May 2026
 * Staging only until Mon 18 May 2026.
 *
 * Responsibilities:
 *   1. Load draft + prefill DM contact block from init_call
 *   2. Run 10 BD-check gates at submit
 *   3. Compute mom_quality_grade A to D
 *   4. Write back edited DM fields to init_call and log to init_call_contact_history
 *   5. Trigger CSR agent (sync, 5s timeout)
 *   6. Insert structured mom_lead_signals rows
 *   7. Persist mom_v2 columns on mom_data
 *
 * Production typos preserved: approving_autorities, fund_sanstion_limit.
 */
class MomV2_model extends CI_Model {

    const GATE_PASS = 'pass';
    const GATE_FAIL = 'fail';
    const GATE_NA   = 'na';

    /**
     * CSR keyword designation match - used to decide if CSR agent fires.
     */
    private $csr_keywords = [
        'csr', 'corporate social', 'sustainability', 'foundation', 'trust',
        'philanthropy', 'social impact', 'community', 'esg',
        'csr committee', 'csr head', 'csr manager', 'csr lead',
        'sustainability head', 'foundation trustee', 'csr trustee'
    ];

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ============================================================
    // PUBLIC API
    // ============================================================

    /**
     * Get draft for a MoM. Prefills DM block from init_call.
     */
    public function get_draft($cid_id, $uid) {
        $this->db->select('id, school_name, cstatus, mainbd, dm_contact_name, dm_contact_designation, dm_contact_phone, dm_contact_email, dm_contact_org_type');
        $this->db->where('id', $cid_id);
        $lead = $this->db->get('init_call')->row_array();
        if (!$lead) return ['ok' => false, 'error' => 'lead_not_found'];

        $this->db->where('cid_id', $cid_id);
        $this->db->where('uid', $uid);
        $this->db->where('approved_status IS NULL', null, false);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $draft = $this->db->get('mom_data')->row_array();

        return [
            'ok' => true,
            'lead' => $lead,
            'draft' => $draft,
            'dm_prefill' => [
                'dm_name'        => $lead['dm_contact_name'],
                'dm_designation' => $lead['dm_contact_designation'],
                'dm_phone'       => $lead['dm_contact_phone'],
                'dm_email'       => $lead['dm_contact_email'],
                'dm_org_type'    => $lead['dm_contact_org_type']
            ]
        ];
    }

    /**
     * Submit a MoM v2. Runs all 10 gates, scores grade, writes back DM block,
     * fires CSR agent (sync with 5s cap), returns full result.
     *
     * Payload keys mirror the 18-question spec.
     */
    public function submit($payload) {
        $uid    = (int)($payload['uid']    ?? 0);
        $cid_id = (int)($payload['cid_id'] ?? 0);
        if (!$uid || !$cid_id) {
            return ['ok' => false, 'error' => 'missing_uid_or_cid'];
        }

        // 1. Run gates
        $gate_result = $this->run_gates($payload);
        if (!$gate_result['all_pass']) {
            return [
                'ok' => false,
                'error' => 'gate_failed',
                'gates' => $gate_result
            ];
        }

        // 2. Compute MoM quality grade
        $quality = $this->compute_quality_grade($payload, $gate_result);

        // 3. Insert or update mom_data with v2 columns
        $mom_id = $this->persist_mom($payload, $quality, $gate_result);

        // 4. Write back DM block to init_call + history
        $this->writeback_dm_to_init_call($cid_id, $uid, $payload);

        // 5. Insert structured signals (objections, competitors, authorities)
        $this->insert_signals($mom_id, $cid_id, $uid, $payload);

        // 6. Fire CSR agent if criteria met (sync, 5s timeout)
        $csr_result = null;
        if ($this->should_fire_csr($payload)) {
            $this->load->model('LinkedinCsr_model');
            $csr_result = $this->LinkedinCsr_model->verify_sync([
                'mom_id'                => $mom_id,
                'cid_id'                => $cid_id,
                'dm_contact_name'       => $payload['dm_name'],
                'dm_contact_designation'=> $payload['dm_designation'],
                'dm_contact_org_type'   => $payload['dm_org_type'],
                'dm_contact_email'      => $payload['dm_email'] ?? null,
                'school_name'           => $payload['school_name'] ?? null
            ]);
            if (!empty($csr_result['csr_check_id'])) {
                $this->db->where('id', $mom_id);
                $this->db->update('mom_data', ['csr_check_id' => $csr_result['csr_check_id']]);
            }
        }

        return [
            'ok' => true,
            'mom_id' => $mom_id,
            'mom_quality_grade' => $quality['grade'],
            'mom_quality_score' => $quality['score'],
            'csr_result' => $csr_result,
            'gates' => $gate_result
        ];
    }

    // ============================================================
    // GATES (10 BD-check gates from form spec)
    // ============================================================

    public function run_gates($p) {
        $gates = [];
        $purpose = strtolower($p['meeting_purpose'] ?? '');
        $meeting_with = strtolower($p['meeting_with'] ?? '');

        // Gate 1: DM contact block
        if ($meeting_with === 'dm') {
            $ok = !empty($p['dm_name']) && !empty($p['dm_designation'])
                  && (!empty($p['dm_phone']) || !empty($p['dm_email']));
            $gates['dm_contact'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'Met DM but DM block incomplete. Need name, designation, and phone or email.'
            ];
        } else {
            $gates['dm_contact'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 2: Authorities for Tentative
        if ($purpose === 'tentative') {
            $auths = $p['approving_autorities_json'] ?? [];
            if (is_string($auths)) $auths = json_decode($auths, true);
            $ok = is_array($auths) && count($auths) >= 1;
            $gates['authorities_tentative'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'Tentative meeting needs at least one approving authority row.'
            ];
        } else {
            $gates['authorities_tentative'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 3: Budget for Tentative
        if ($purpose === 'tentative') {
            $b = $p['budget_for_cfyear'] ?? null;
            $ok = is_numeric($b) && $b >= 0;
            $gates['budget_tentative'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'Tentative needs a budget number. Use 0 if no FY budget confirmed.'
            ];
        } else {
            $gates['budget_tentative'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 4: Proposal cohort if submit_proposal=yes
        if (!empty($p['submit_proposal']) && strtolower($p['submit_proposal']) === 'yes') {
            $ok = !empty($p['proposal_intent_schools'])
                  && !empty($p['proposal_intent_budget_rs'])
                  && !empty($p['proposal_intent_location'])
                  && !empty($p['fitment_offer']);
            $gates['proposal_cohort'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'Proposal needed - fill schools, budget, location, fitment offer.'
            ];
        } else {
            $gates['proposal_cohort'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 5: Proposal shared record
        if ($purpose === 'proposal_share') {
            $ok = !empty($p['proposal_doc_url']) && !empty($p['proposal_value_rs']);
            $gates['proposal_shared_record'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'Proposal share meeting needs document URL and value.'
            ];
        } else {
            $gates['proposal_shared_record'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 6: Objection on push-back
        $review = strtolower($p['proposal_review_status'] ?? '');
        if (in_array($review, ['reviewed_with_changes', 'rejected'])) {
            $objs = $p['objection_log'] ?? [];
            if (is_string($objs)) $objs = json_decode($objs, true);
            $ok = is_array($objs) && count($objs) >= 1;
            $gates['objection_on_pushback'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'Client pushed back - log at least one objection so CM can help.'
            ];
        } else {
            $gates['objection_on_pushback'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 7: Close date for RP and Closure
        if (in_array($purpose, ['rp', 'closure'])) {
            $d = $p['expected_close_date'] ?? null;
            $ok = !empty($d) && strtotime($d) >= strtotime(date('Y-m-d'));
            $gates['close_date_rp'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'RP and Closure meetings need a forward-looking close date.'
            ];
        } else {
            $gates['close_date_rp'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 8: R2B for RP and Closure
        if (in_array($purpose, ['rp', 'closure'])) {
            $ok = !empty($p['r2b_status']);
            $gates['r2b_rp'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'RP meeting must record R2B status.'
            ];
        } else {
            $gates['r2b_rp'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 9: Won readiness
        if (!empty($p['request_won_signoff'])) {
            $payment_ok = !empty($p['payment_plan_clarified']) && strtolower($p['payment_plan_clarified']) === 'yes';
            $vendor_ok  = !empty($p['vendor_form_status']) && in_array($p['vendor_form_status'], ['in_progress','completed']);
            $contract   = (float)($p['contract_value_rs'] ?? 0);
            $proposal_v = (float)($p['proposal_value_rs'] ?? 0);
            $value_ok   = ($proposal_v == 0) || ($contract >= $proposal_v * 0.8);
            $ok = $payment_ok && $vendor_ok && $value_ok;
            $gates['won_readiness'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'Won signoff needs payment plan, vendor onboarding, contract within 20 percent of proposal.'
            ];
        } else {
            $gates['won_readiness'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Gate 10: Narrative depth on RP and Closure
        if (in_array($purpose, ['rp', 'closure'])) {
            $len = strlen(trim($p['rpmmom'] ?? ''));
            $ok = $len >= 200;
            $gates['narrative_depth'] = [
                'status' => $ok ? self::GATE_PASS : self::GATE_FAIL,
                'message' => $ok ? null : 'RP and Closure narratives must be at least 200 characters. Got ' . $len . '.'
            ];
        } else {
            $gates['narrative_depth'] = ['status' => self::GATE_NA, 'message' => null];
        }

        // Roll up
        $fails = array_filter($gates, function($g){ return $g['status'] === self::GATE_FAIL; });
        return [
            'all_pass' => empty($fails),
            'gates' => $gates,
            'fail_count' => count($fails)
        ];
    }

    // ============================================================
    // QUALITY GRADE A to D
    // ============================================================

    public function compute_quality_grade($p, $gate_result) {
        $score = 0;
        $max   = 100;

        // 40 points: all applicable gates passed
        $applicable = array_filter($gate_result['gates'], function($g){ return $g['status'] !== self::GATE_NA; });
        $passed     = array_filter($applicable, function($g){ return $g['status'] === self::GATE_PASS; });
        $gate_pct   = count($applicable) ? count($passed) / count($applicable) : 1;
        $score += round(40 * $gate_pct);

        // 20 points: narrative length
        $len = strlen(trim($p['rpmmom'] ?? ''));
        if ($len >= 400) $score += 20;
        elseif ($len >= 300) $score += 15;
        elseif ($len >= 200) $score += 10;
        elseif ($len >= 100) $score += 5;

        // 15 points: structured signal density
        $authorities = $p['approving_autorities_json'] ?? [];
        if (is_string($authorities)) $authorities = json_decode($authorities, true);
        $auth_count = is_array($authorities) ? count($authorities) : 0;
        $score += min(8, $auth_count * 2);

        $objections = $p['objection_log'] ?? [];
        if (is_string($objections)) $objections = json_decode($objections, true);
        $obj_count = is_array($objections) ? count($objections) : 0;
        $score += min(7, $obj_count * 2);

        // 15 points: DM completeness
        $dm_complete = 0;
        if (!empty($p['dm_name'])) $dm_complete++;
        if (!empty($p['dm_designation'])) $dm_complete++;
        if (!empty($p['dm_phone'])) $dm_complete++;
        if (!empty($p['dm_email'])) $dm_complete++;
        if (!empty($p['dm_org_type'])) $dm_complete++;
        $score += round(15 * ($dm_complete / 5));

        // 10 points: forecast quality
        if (!empty($p['expected_close_date'])) $score += 5;
        if (!empty($p['win_probability'])) $score += 3;
        if (!empty($p['r2b_status'])) $score += 2;

        // Cap
        $score = max(0, min($max, $score));

        // Grade buckets
        if ($score >= 85)      $grade = 'A';
        elseif ($score >= 65)  $grade = 'B';
        elseif ($score >= 45)  $grade = 'C';
        else                   $grade = 'D';

        // DM contact completeness percent (separate field for surface)
        $dm_pct = round(($dm_complete / 5) * 100);

        return [
            'score' => $score,
            'grade' => $grade,
            'dm_contact_completeness' => $dm_pct
        ];
    }

    // ============================================================
    // PERSISTENCE
    // ============================================================

    private function persist_mom($p, $quality, $gate_result) {
        $cid_id = (int)$p['cid_id'];
        $uid    = (int)$p['uid'];

        $row = [
            'cid_id'                   => $cid_id,
            'uid'                      => $uid,
            'meeting_purpose_v2'       => $p['meeting_purpose'],
            'meeting_with'             => $p['meeting_with'],
            'dm_name'                  => $p['dm_name']         ?? null,
            'dm_designation'           => $p['dm_designation']  ?? null,
            'dm_phone'                 => $p['dm_phone']        ?? null,
            'dm_email'                 => $p['dm_email']        ?? null,
            'dm_org_type'              => $p['dm_org_type']     ?? null,
            'dm_contact_completeness'  => $quality['dm_contact_completeness'],
            'approving_autorities_json'=> isset($p['approving_autorities_json']) ? json_encode($p['approving_autorities_json']) : null,
            'budget_for_cfyear'        => $p['budget_for_cfyear']     ?? null,
            'fund_sanstion_limit'      => $p['fund_sanstion_limit']   ?? null,
            'submit_proposal'          => $p['submit_proposal']       ?? null,
            'proposal_intent_schools'  => $p['proposal_intent_schools'] ?? null,
            'proposal_intent_budget_rs'=> $p['proposal_intent_budget_rs'] ?? null,
            'proposal_intent_location' => $p['proposal_intent_location'] ?? null,
            'fitment_offer'            => $p['fitment_offer']         ?? null,
            'proposal_doc_url'         => $p['proposal_doc_url']      ?? null,
            'proposal_shared_with'     => $p['proposal_shared_with']  ?? null,
            'proposal_shared_date'     => $p['proposal_shared_date']  ?? null,
            'proposal_value_rs'        => $p['proposal_value_rs']     ?? null,
            'proposal_validity_days'   => $p['proposal_validity_days']?? null,
            'proposal_review_status'   => $p['proposal_review_status']?? null,
            'expected_close_date'      => $p['expected_close_date']   ?? null,
            'win_probability'          => $p['win_probability']       ?? null,
            'r2b_status'               => $p['r2b_status']            ?? null,
            'intervention_level'       => $p['intervention_level']    ?? 'none',
            'intervention_reason_code' => $p['intervention_reason_code']?? null,
            'intervention_sla_hours'   => $p['intervention_sla_hours']?? null,
            'rpmmom'                   => $p['rpmmom']                ?? null,
            'mom_quality_grade'        => $quality['grade'],
            'mom_quality_score'        => $quality['score'],
            'gates_passed_json'        => json_encode($gate_result['gates']),
            'v2_submitted_at'          => date('Y-m-d H:i:s')
        ];

        if (!empty($p['mom_id'])) {
            $this->db->where('id', (int)$p['mom_id']);
            $this->db->update('mom_data', $row);
            return (int)$p['mom_id'];
        } else {
            $this->db->insert('mom_data', $row);
            return $this->db->insert_id();
        }
    }

    // ============================================================
    // INIT_CALL WRITE-BACK + AUDIT
    // ============================================================

    public function writeback_dm_to_init_call($cid_id, $uid, $p) {
        $this->db->where('id', $cid_id);
        $lead = $this->db->get('init_call')->row_array();
        if (!$lead) return;

        $fields = [
            'dm_contact_name'        => 'dm_name',
            'dm_contact_designation' => 'dm_designation',
            'dm_contact_phone'       => 'dm_phone',
            'dm_contact_email'       => 'dm_email',
            'dm_contact_org_type'    => 'dm_org_type'
        ];

        $update = [];
        foreach ($fields as $col => $payload_key) {
            $new = $p[$payload_key] ?? null;
            if ($new === null || $new === '') continue;
            $old = $lead[$col] ?? null;
            if ($old != $new) {
                $update[$col] = $new;
                $this->db->insert('init_call_contact_history', [
                    'cid_id'      => $cid_id,
                    'field_name'  => $col,
                    'old_value'   => $old,
                    'new_value'   => $new,
                    'changed_by'  => $uid,
                    'changed_at'  => date('Y-m-d H:i:s'),
                    'reason_code' => $p['dm_edit_reason'] ?? ($old === null ? 'initial_fill' : 'other'),
                    'source'      => 'mom_form'
                ]);
            }
        }

        if (!empty($update)) {
            if (empty($lead['dm_contact_filled_at'])) {
                $update['dm_contact_filled_at'] = date('Y-m-d H:i:s');
                $update['dm_contact_filled_by'] = $uid;
            }
            $this->db->where('id', $cid_id);
            $this->db->update('init_call', $update);
        }
    }

    // ============================================================
    // SIGNALS (objections, competitors, authorities, offerings)
    // ============================================================

    private function insert_signals($mom_id, $cid_id, $uid, $p) {
        // Objections
        $objs = $p['objection_log'] ?? [];
        if (is_string($objs)) $objs = json_decode($objs, true);
        if (is_array($objs)) {
            foreach ($objs as $o) {
                if (empty($o['code'])) continue;
                $this->db->insert('mom_lead_signals', [
                    'mom_id'      => $mom_id,
                    'cid_id'      => $cid_id,
                    'signal_type' => 'objection',
                    'signal_code' => $o['code'],
                    'signal_value'=> $o['note'] ?? null,
                    'created_by'  => $uid
                ]);
            }
        }

        // Competitor mentioned
        if (!empty($p['competitor_mentioned'])) {
            $this->db->insert('mom_lead_signals', [
                'mom_id'      => $mom_id,
                'cid_id'      => $cid_id,
                'signal_type' => 'competitor',
                'signal_code' => $p['competitor_mentioned'],
                'signal_value'=> $p['competitor_note'] ?? null,
                'created_by'  => $uid
            ]);
        }

        // Authority rows
        $auths = $p['approving_autorities_json'] ?? [];
        if (is_string($auths)) $auths = json_decode($auths, true);
        if (is_array($auths)) {
            foreach ($auths as $idx => $a) {
                if (empty($a['name'])) continue;
                $this->db->insert('mom_lead_signals', [
                    'mom_id'      => $mom_id,
                    'cid_id'      => $cid_id,
                    'signal_type' => 'authority',
                    'signal_code' => $idx === 0 ? 'dm_layer' : ($idx === 1 ? 'secondary' : 'tertiary'),
                    'signal_value'=> $a['name'] . ' | ' . ($a['designation'] ?? ''),
                    'signal_rs'   => $a['sanction_rs'] ?? null,
                    'created_by'  => $uid
                ]);
            }
        }

        // Offerings pitched
        $offs = $p['presentation_pitched'] ?? [];
        if (is_string($offs)) $offs = json_decode($offs, true);
        if (is_array($offs)) {
            foreach ($offs as $off) {
                $this->db->insert('mom_lead_signals', [
                    'mom_id'      => $mom_id,
                    'cid_id'      => $cid_id,
                    'signal_type' => 'offering_pitched',
                    'signal_code' => strtolower($off),
                    'created_by'  => $uid
                ]);
            }
        }
    }

    // ============================================================
    // CSR DECISION
    // ============================================================

    public function should_fire_csr($p) {
        $org = strtolower($p['dm_org_type'] ?? '');
        if (!in_array($org, ['corporate','ngo','foundation','csr_arm','trust'])) return false;
        $des = strtolower($p['dm_designation'] ?? '');
        if (empty($des)) return false;
        foreach ($this->csr_keywords as $kw) {
            if (strpos($des, $kw) !== false) return true;
        }
        return false;
    }

    // ============================================================
    // APPROVAL QUEUE (CM surface)
    // ============================================================

    public function approval_queue($manager_uid, $cluster = null, $limit = 50) {
        $this->db->select('m.id AS mom_id, m.cid_id, m.uid AS bd_uid, u.username AS bd_name,
                           ic.school_name, m.meeting_purpose_v2, m.meeting_with,
                           m.dm_name, m.dm_designation, m.mom_quality_grade, m.mom_quality_score,
                           m.dm_contact_completeness, m.expected_close_date, m.win_probability,
                           m.r2b_status, csr.verdict AS csr_verdict, csr.csr_intent_confidence,
                           m.v2_submitted_at,
                           TIMESTAMPDIFF(MINUTE, m.v2_submitted_at, NOW()) AS minutes_pending');
        $this->db->from('mom_data m');
        $this->db->join('init_call ic', 'ic.id = m.cid_id', 'left');
        $this->db->join('user u', 'u.uid = m.uid', 'left');
        $this->db->join('mom_csr_check csr', 'csr.id = m.csr_check_id', 'left');
        $this->db->where('m.approved_status IS NULL', null, false);
        $this->db->where('m.v2_submitted_at IS NOT NULL', null, false);
        if ($cluster) $this->db->where('ic.cluster', $cluster);
        $this->db->order_by('m.v2_submitted_at ASC, m.mom_quality_grade DESC'); // oldest first, worst quality first
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function approve($mom_id, $manager_uid, $manager_role, $coaching_note = null) {
        $this->db->where('id', $mom_id);
        $this->db->update('mom_data', [
            'approved_status' => 1,
            'approved_by' => $manager_uid,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
        $this->db->insert('mom_line_manager_review', [
            'mom_id' => $mom_id,
            'cid_id' => $this->db->select('cid_id')->where('id', $mom_id)->get('mom_data')->row()->cid_id,
            'manager_uid' => $manager_uid,
            'manager_role' => $manager_role,
            'action' => 'approve',
            'coaching_note' => $coaching_note
        ]);
        return ['ok' => true];
    }

    public function reject($mom_id, $manager_uid, $manager_role, $reject_reason_code, $coaching_note = null) {
        $this->db->where('id', $mom_id);
        $this->db->update('mom_data', [
            'approved_status' => 'NO RP',
            'approved_by' => $manager_uid,
            'approved_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reject_reason_code
        ]);
        $this->db->insert('mom_line_manager_review', [
            'mom_id' => $mom_id,
            'cid_id' => $this->db->select('cid_id')->where('id', $mom_id)->get('mom_data')->row()->cid_id,
            'manager_uid' => $manager_uid,
            'manager_role' => $manager_role,
            'action' => 'reject',
            'reject_reason_code' => $reject_reason_code,
            'coaching_note' => $coaching_note
        ]);
        return ['ok' => true];
    }
}
