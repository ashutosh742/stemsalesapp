<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RemarkCoherence_model - Migration 049
 *
 * AI agent that scores BD/CM/RM remarks for logical coherence against the
 * stage transition or money claim they are attached to, and generates a
 * pushback question when the remark is illogical.
 *
 * Source tables scored:
 *   - tblcallevents          (BD field remarks)
 *   - line_manager_signoff   (CM signoff)
 *   - plan_approval_log      (CM approval)
 *   - expense_actuals_log    (CM variance note)
 *   - rm_upsell_pipeline     (RM last touch)
 *   - anchor_renewal_log     (RM renewal note)
 *   - review_session         (RM review signoff)
 *
 * Standing rules: plain English, no em-dashes, no non-ASCII in output,
 * "Rs" for rupees, "percent" spelled out, BearerAuth on controller side.
 *
 * Pilot guardrail enforced here too via feature_flag + WB uids:
 *   1000289 Avishek Pathak (BD)
 *   1000351 Rimly Lahiri Chakraborty (BD)
 *   1000305 Nilanjan Chatterjee (CM)
 *   1000269 Mehak Sarraf (RM East)
 *   1000356 Debabrata Mukherjee (SC)
 */
class RemarkCoherence_model extends CI_Model
{
    const FEATURE_FLAG       = 'remark_coherence_049_enabled';
    const PUSHBACK_THRESHOLD = 70;          // score under 70 + money/stage trigger
    const REMARK_MAX_CHARS   = 2000;
    const DEFAULT_SLA_HOURS  = 24;
    const PILOT_UIDS         = [1000289, 1000351, 1000305, 1000269, 1000356];

    // Source-table whitelist with the columns that hold remark narrative.
    // (source_pk_column, remark_columns[], actor_uid_column, optional cid_column, optional event_column)
    private $sources = [
        'tblcallevents' => [
            'pk' => 'event_id',
            'remarks' => ['late_remarks_message', 'remarks', 'special_remarks', 'next_step_confirmation', 'closing_timeline'],
            'actor_col' => 'mainbd',
            'cid_col' => 'cid_id',
            'event_col' => 'event_id',
        ],
        'line_manager_signoff' => [
            'pk' => 'id',
            'remarks' => ['signoff_remarks'],
            'actor_col' => 'cm_uid',
            'cid_col' => 'cid_id',
            'event_col' => NULL,
        ],
        'plan_approval_log' => [
            'pk' => 'id',
            'remarks' => ['cm_remarks'],
            'actor_col' => 'cm_uid',
            'cid_col' => NULL,
            'event_col' => NULL,
        ],
        'expense_actuals_log' => [
            'pk' => 'id',
            'remarks' => ['cm_note'],
            'actor_col' => 'cm_uid',
            'cid_col' => NULL,
            'event_col' => 'event_id',
        ],
        'rm_upsell_pipeline' => [
            'pk' => 'id',
            'remarks' => ['last_touch_note'],
            'actor_col' => 'rm_uid',
            'cid_col' => 'cid_id',
            'event_col' => NULL,
        ],
        'anchor_renewal_log' => [
            'pk' => 'id',
            'remarks' => ['rm_remark'],
            'actor_col' => 'rm_uid',
            'cid_col' => 'cid_id',
            'event_col' => NULL,
        ],
        'review_session' => [
            'pk' => 'id',
            'remarks' => ['rm_signoff_note'],
            'actor_col' => 'rm_uid',
            'cid_col' => NULL,
            'event_col' => NULL,
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        if (file_exists(APPPATH . "libraries/LlmClient.php")) { $this->load->library("LlmClient"); }
    }

    /**
     * Entry point for the nightly batch.
     * Runs as part of M035 rhythm_orchestrator at 22:00 IST.
     */
    public function run_nightly_batch($scope = 'pilot')
    {
        if ( ! $this->is_enabled()) {
            return ['status' => 'skipped', 'reason' => 'feature_flag_off'];
        }

        $run_id = $this->open_run_log($scope);
        $stats = [
            'scanned' => 0,
            'scored' => 0,
            'pushbacks' => 0,
            'cost_usd' => 0.0,
            'errors' => 0,
        ];
        $latencies = [];

        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $today     = date('Y-m-d');

        foreach ($this->sources as $table => $cfg) {
            $rows = $this->fetch_unscored_remarks($table, $cfg, $yesterday, $today, $scope);
            foreach ($rows as $row) {
                $stats['scanned']++;
                try {
                    $result = $this->score_one_remark($table, $cfg, $row, $run_id);
                    if ($result === NULL) {
                        continue; // remark empty or trivially short, skip
                    }
                    $stats['scored']++;
                    $stats['cost_usd'] += (float)$result['llm_cost_usd'];
                    $latencies[] = (int)$result['llm_latency_ms'];

                    if ($result['pushback_required']) {
                        $this->create_pushback_question($result);
                        $stats['pushbacks']++;
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    log_message('error', 'M049 score failed: ' . $e->getMessage());
                }
            }
        }

        $this->close_run_log($run_id, $stats, $latencies);
        return ['status' => 'completed', 'run_id' => $run_id, 'stats' => $stats];
    }

    /**
     * Score a single remark on demand (used by controller score_one endpoint
     * and by writers who want immediate feedback after submission).
     */
    public function score_one_remark($table, $cfg, $row, $run_id = NULL)
    {
        // 1) Collect remark text
        $remark_parts = [];
        foreach ($cfg['remarks'] as $col) {
            if ( ! empty($row[$col])) {
                $remark_parts[] = trim((string)$row[$col]);
            }
        }
        $remark_text = trim(implode(" | ", $remark_parts));
        $remark_text = mb_substr($remark_text, 0, self::REMARK_MAX_CHARS, 'UTF-8');

        if (strlen($remark_text) < 8) {
            return NULL; // not enough to score
        }

        // 2) Resolve context
        $actor_uid = (int)$row[$cfg['actor_col']];
        if ( ! $this->is_actor_in_scope($actor_uid)) {
            return NULL;
        }

        $actor_role = $this->resolve_role($actor_uid);
        $cid_id     = $cfg['cid_col'] ? ($row[$cfg['cid_col']] ?? NULL) : NULL;
        $event_id   = $cfg['event_col'] ? ($row[$cfg['event_col']] ?? NULL) : NULL;
        $from_to    = $this->resolve_stage_transition($table, $row, $cid_id);
        $claimed    = $this->extract_claimed_amount($remark_text, $cid_id);

        // 3) Build LLM grading prompt
        $context = $this->build_context_snapshot($table, $cid_id, $event_id, $from_to);
        $prompt  = $this->build_grading_prompt($remark_text, $actor_role, $from_to, $claimed, $context);

        // 4) Call LLM
        $t0 = microtime(TRUE);
        $llm_out = $this->llmclient->grade_remark($prompt); // returns ['dims'=>[...], 'rationale'=>..., 'raw'=>..., 'cost_usd'=>..., 'model'=>...]
        $latency_ms = (int)((microtime(TRUE) - $t0) * 1000);

        $dims = $llm_out['dims'];
        $score_total = (int)($dims['stage_justification'] + $dims['evidence_link'] + $dims['stakeholder_named'] + $dims['next_step_concreteness'] + $dims['internal_consistency']);
        $grade = $this->grade_from_score($score_total);

        $is_stage_promotion = ($from_to['to'] !== NULL && $from_to['to'] > (int)$from_to['from']) ? 1 : 0;
        $is_money_claim = ($claimed !== NULL && $claimed > 0) || in_array((int)$from_to['to'], [8, 9, 12], TRUE) ? 1 : 0;
        $pushback_required = ($score_total < self::PUSHBACK_THRESHOLD && ($is_stage_promotion || $is_money_claim)) ? 1 : 0;

        $template_code = NULL;
        if ($pushback_required) {
            $template_code = $this->pick_template($dims, $from_to, $remark_text, $actor_role, $cid_id);
        }

        // 5) Persist score
        $score_id = $this->insert_score([
            'source_table' => $table,
            'source_pk' => (int)$row[$cfg['pk']],
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'cid_id' => $cid_id,
            'event_id' => $event_id,
            'from_cstatus' => $from_to['from'],
            'to_cstatus' => $from_to['to'],
            'claimed_amount_rs' => $claimed,
            'remark_text' => $remark_text,
            'remark_length_chars' => mb_strlen($remark_text, 'UTF-8'),
            'dim_stage_justification' => (int)$dims['stage_justification'],
            'dim_evidence_link' => (int)$dims['evidence_link'],
            'dim_stakeholder_named' => (int)$dims['stakeholder_named'],
            'dim_next_step_concreteness' => (int)$dims['next_step_concreteness'],
            'dim_internal_consistency' => (int)$dims['internal_consistency'],
            'score_total' => $score_total,
            'grade' => $grade,
            'is_money_claim' => $is_money_claim,
            'is_stage_promotion' => $is_stage_promotion,
            'pushback_required' => $pushback_required,
            'pushback_template_code' => $template_code,
            'llm_model' => $llm_out['model'],
            'llm_latency_ms' => $latency_ms,
            'llm_cost_usd' => $llm_out['cost_usd'],
            'llm_raw_response' => $llm_out['raw'],
            'scored_by_run_id' => $run_id,
        ]);

        return [
            'score_id' => $score_id,
            'pushback_required' => $pushback_required,
            'template_code' => $template_code,
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'cid_id' => $cid_id,
            'event_id' => $event_id,
            'score_total' => $score_total,
            'grade' => $grade,
            'llm_cost_usd' => $llm_out['cost_usd'],
            'llm_latency_ms' => $latency_ms,
        ];
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    public function is_enabled()
    {
        $row = $this->db->select('flag_value')
                        ->from('feature_flag')
                        ->where('flag_key', self::FEATURE_FLAG)
                        ->get()->row_array();
        return $row && (int)$row['flag_value'] >= 1;
    }

    public function is_actor_in_scope($actor_uid)
    {
        $row = $this->db->select('flag_value')
                        ->from('feature_flag')
                        ->where('flag_key', self::FEATURE_FLAG)
                        ->get()->row_array();
        $flag = $row ? (int)$row['flag_value'] : 0;
        if ($flag === 0) {
            return FALSE;
        }
        if ($flag === 1) {
            return in_array((int)$actor_uid, self::PILOT_UIDS, TRUE);
        }
        // flag 2 = org-wide
        return TRUE;
    }

    private function fetch_unscored_remarks($table, $cfg, $yesterday, $today, $scope)
    {
        $remark_cols = implode(',', $cfg['remarks']);
        $select_cols = $cfg['pk'] . ',' . $remark_cols . ',' . $cfg['actor_col'];
        if ($cfg['cid_col']) {
            $select_cols .= ',' . $cfg['cid_col'];
        }
        if ($cfg['event_col']) {
            $select_cols .= ',' . $cfg['event_col'];
        }
        // Anti-join against already-scored rows for this source table
        $sql = "SELECT {$select_cols}
                FROM `{$table}` t
                WHERE NOT EXISTS (
                  SELECT 1 FROM remark_coherence_score s
                  WHERE s.source_table = ? AND s.source_pk = t.`{$cfg['pk']}`
                )
                AND DATE(COALESCE(t.updated_at, t.created_at, NOW())) BETWEEN ? AND ?
                LIMIT 500";
        $q = $this->db->query($sql, [$table, $yesterday, $today]);
        return $q ? $q->result_array() : [];
    }

    /**
     * Build the 5-dimension grading prompt sent to the LLM.
     * Returns a string. LlmClient::grade_remark parses the JSON response.
     */
    private function build_grading_prompt($remark_text, $actor_role, $from_to, $claimed, $context)
    {
        $from = $from_to['from'] !== NULL ? (int)$from_to['from'] : 'none';
        $to   = $from_to['to'] !== NULL ? (int)$from_to['to'] : 'none';
        $amt  = $claimed !== NULL ? ('Rs ' . number_format((float)$claimed, 0)) : 'none';

        $ctx_lines = [];
        foreach ($context as $k => $v) {
            $ctx_lines[] = $k . ': ' . (is_scalar($v) ? $v : json_encode($v));
        }
        $ctx_block = implode("\n", $ctx_lines);

        return "You are an auditor grading the logical coherence of a sales remark in plain English.\n"
             . "Actor role: {$actor_role}.\n"
             . "Stage transition: from {$from} to {$to}.\n"
             . "Claimed amount: {$amt}.\n\n"
             . "Lead context:\n{$ctx_block}\n\n"
             . "Remark text:\n\"\"\"{$remark_text}\"\"\"\n\n"
             . "Grade on five dimensions, each 0 to 20:\n"
             . "1. stage_justification - does the remark explain why this stage is now correct\n"
             . "2. evidence_link - does it cite a concrete document, MoM id, quote id, photo, or named source\n"
             . "3. stakeholder_named - is the decision maker or stakeholder named with designation\n"
             . "4. next_step_concreteness - is the next action time-bound and specific\n"
             . "5. internal_consistency - does the remark agree with the stage, amount, and other context\n\n"
             . "Penalise vague words like discussing, considering, soon, follow up. Reward names, dates, document references, and clear next steps.\n"
             . "Return strict JSON only: {\"stage_justification\": <0-20>, \"evidence_link\": <0-20>, \"stakeholder_named\": <0-20>, \"next_step_concreteness\": <0-20>, \"internal_consistency\": <0-20>, \"rationale\": \"<one sentence>\"}";
    }

    private function build_context_snapshot($table, $cid_id, $event_id, $from_to)
    {
        $ctx = ['source_table' => $table];
        if ($cid_id) {
            $lead = $this->db->select('school_name, fbudget, current_status_id, partner_type, dm_name, dm_designation')
                             ->from('init_call')->where('cid_id', $cid_id)->get()->row_array();
            if ($lead) {
                $ctx['school_name'] = $lead['school_name'] ?? '';
                $ctx['fbudget_rs'] = $lead['fbudget'] ?? '';
                $ctx['current_status_id'] = $lead['current_status_id'] ?? '';
                $ctx['partner_type'] = $lead['partner_type'] ?? '';
                $ctx['dm_name'] = $lead['dm_name'] ?? '';
                $ctx['dm_designation'] = $lead['dm_designation'] ?? '';
            }
            $mom = $this->db->select('id, status, approved_at')
                            ->from('mom_data')
                            ->where('cid_id', $cid_id)
                            ->order_by('id', 'DESC')->limit(1)
                            ->get()->row_array();
            if ($mom) {
                $ctx['latest_mom_id'] = $mom['id'];
                $ctx['mom_status'] = $mom['status'];
            }
        }
        if ($event_id) {
            $ev = $this->db->select('actiontype_id, purpose_id, event_date')
                           ->from('tblcallevents')->where('event_id', $event_id)->get()->row_array();
            if ($ev) {
                $ctx['event_actiontype_id'] = $ev['actiontype_id'];
                $ctx['event_purpose_id'] = $ev['purpose_id'];
                $ctx['event_date'] = $ev['event_date'];
            }
        }
        return $ctx;
    }

    private function resolve_role($actor_uid)
    {
        $row = $this->db->select('type_id')->from('user')->where('uid', $actor_uid)->get()->row_array();
        if ( ! $row) return 'UNK';
        $type_id = (int)$row['type_id'];
        // type_id mapping per existing user table convention
        if ($type_id === 13) return 'CM';
        if ($type_id === 28) return 'RM';
        if ($type_id === 27) return 'AO';
        if ($type_id === 26) return 'ACM';
        if ($type_id === 25) return 'SH';
        return 'BD';
    }

    private function resolve_stage_transition($table, $row, $cid_id)
    {
        $out = ['from' => NULL, 'to' => NULL];
        if ($table === 'tblcallevents') {
            // Look up lead_progression_log around this event
            if ($cid_id) {
                $log = $this->db->select('from_cstatus, to_cstatus')
                                ->from('lead_progression_log')
                                ->where('cid_id', $cid_id)
                                ->where('event_id', $row['event_id'])
                                ->order_by('id', 'DESC')->limit(1)
                                ->get()->row_array();
                if ($log) {
                    $out['from'] = (int)$log['from_cstatus'];
                    $out['to']   = (int)$log['to_cstatus'];
                }
            }
        }
        return $out;
    }

    private function extract_claimed_amount($remark_text, $cid_id)
    {
        // Look for "Rs <number>" patterns - handles "Rs 1.5 lakh", "Rs 50,000", "Rs 2.5 cr"
        if (preg_match('/Rs\s*([\d,]+(?:\.\d+)?)\s*(lakh|cr|crore|k)?/i', $remark_text, $m)) {
            $num = (float)str_replace(',', '', $m[1]);
            $unit = strtolower($m[2] ?? '');
            if ($unit === 'lakh') $num *= 100000;
            elseif ($unit === 'cr' || $unit === 'crore') $num *= 10000000;
            elseif ($unit === 'k') $num *= 1000;
            return $num;
        }
        return NULL;
    }

    private function grade_from_score($score)
    {
        if ($score >= 90) return 'A+';
        if ($score >= 75) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        return 'D';
    }

    /**
     * Deterministic template picker.
     * 1) Find the lowest-scoring dimension.
     * 2) Filter templates by trigger_dim, applies_to_role, trigger_min_cstatus, optional regex.
     * 3) Among matches, pick highest priority. Fall back to FREE_TEXT_PUSHBACK.
     */
    private function pick_template($dims, $from_to, $remark_text, $actor_role, $cid_id)
    {
        // Find lowest dim
        asort($dims);
        $lowest_dim = key($dims); // e.g. "stage_justification"
        $trigger_col = 'dim_' . $lowest_dim;

        $to_cstatus = $from_to['to'] !== NULL ? (int)$from_to['to'] : 0;

        $candidates = $this->db
            ->where('is_active', 1)
            ->where("FIND_IN_SET('{$actor_role}', applies_to_role) >", 0)
            ->where("(trigger_dim IS NULL OR trigger_dim = '{$trigger_col}')", NULL, FALSE)
            ->where("(trigger_min_cstatus IS NULL OR trigger_min_cstatus <= {$to_cstatus})", NULL, FALSE)
            ->order_by('priority', 'DESC')
            ->get('remark_pushback_template')->result_array();

        foreach ($candidates as $c) {
            // Optional regex match on remark text
            if ( ! empty($c['trigger_keyword_regex'])) {
                if ( ! @preg_match('/' . str_replace('/', '\/', $c['trigger_keyword_regex']) . '/i', $remark_text)) {
                    continue;
                }
            }
            // Specific cstatus 12 templates - check remark for "discussing"/"pending" hedge words
            if ($c['template_code'] === 'WON_PENDING_DISCUSSION') {
                if ( ! preg_match('/\b(discussing|considering|pending|maybe|likely|expect to)\b/i', $remark_text)) {
                    continue;
                }
            }
            return $c['template_code'];
        }
        return 'FREE_TEXT_PUSHBACK';
    }

    private function insert_score($data)
    {
        $this->db->insert('remark_coherence_score', $data);
        return (int)$this->db->insert_id();
    }

    private function create_pushback_question($result)
    {
        // Render question text - substitute lead/school where appropriate
        $tpl = $this->db->where('template_code', $result['template_code'])
                        ->get('remark_pushback_template')->row_array();
        $q_text = $tpl ? $tpl['question_text'] : 'Please clarify this remark.';

        if ($result['cid_id']) {
            $lead = $this->db->select('school_name')->from('init_call')
                             ->where('cid_id', $result['cid_id'])->get()->row_array();
            if ($lead && ! empty($lead['school_name'])) {
                $q_text = 'For ' . $lead['school_name'] . ': ' . $q_text;
            }
        }

        $this->db->insert('remark_pushback_question', [
            'coherence_score_id' => $result['score_id'],
            'template_code' => $result['template_code'],
            'actor_uid' => $result['actor_uid'],
            'actor_role' => $result['actor_role'],
            'question_text' => $q_text,
            'cid_id' => $result['cid_id'],
            'event_id' => $result['event_id'],
            'status' => 'open',
            'sla_hours' => self::DEFAULT_SLA_HOURS,
        ]);

        $q_id = (int)$this->db->insert_id();

        // Notify via M027 comm_orchestrator (in-app first, email 30 min fallback)
        if (class_exists('Comm_orchestrator_model') || file_exists(APPPATH . 'models/AIAgents/Comm_orchestrator_model.php')) {
            $this->load->model('AIAgents/Comm_orchestrator_model', 'comm');
            $this->comm->enqueue([
                'channel' => 'in_app',
                'recipient_uid' => $result['actor_uid'],
                'subject' => 'Pushback on your remark',
                'body' => $q_text,
                'deep_link' => '/pushback/inbox/' . $q_id,
                'fallback_email_after_minutes' => 30,
                'source' => 'remark_coherence_049',
                'source_id' => $q_id,
            ]);
        }
        return $q_id;
    }

    private function open_run_log($scope)
    {
        $this->db->insert('remark_coherence_run_log', [
            'run_start_at' => date('Y-m-d H:i:s'),
            'run_status' => 'running',
            'scope' => $scope,
        ]);
        return (int)$this->db->insert_id();
    }

    private function close_run_log($run_id, $stats, $latencies)
    {
        $avg = count($latencies) ? (int)round(array_sum($latencies) / count($latencies)) : 0;
        $this->db->where('id', $run_id)->update('remark_coherence_run_log', [
            'run_end_at' => date('Y-m-d H:i:s'),
            'run_status' => $stats['errors'] > 0 && $stats['scored'] === 0 ? 'failed' : ($stats['errors'] > 0 ? 'partial' : 'completed'),
            'remarks_scanned' => $stats['scanned'],
            'remarks_scored' => $stats['scored'],
            'pushbacks_created' => $stats['pushbacks'],
            'llm_total_cost_usd' => round($stats['cost_usd'], 4),
            'llm_avg_latency_ms' => $avg,
            'errors_count' => $stats['errors'],
        ]);
    }

    // ------------------------------------------------------------------
    // Read-side helpers used by controller and huddle drafter
    // ------------------------------------------------------------------

    public function get_open_pushbacks_for_user($uid)
    {
        return $this->db->where('actor_uid', $uid)
                        ->where('status', 'open')
                        ->order_by('asked_at', 'ASC')
                        ->get('v_pushback_open_for_actor')->result_array();
    }

    public function get_yesterday_coherence_by_actor()
    {
        return $this->db->get('v_coherence_yesterday_by_actor')->result_array();
    }

    public function get_pushback($question_id)
    {
        return $this->db->where('id', $question_id)
                        ->get('remark_pushback_question')->row_array();
    }

    public function record_response($question_id, $responder_uid, $responder_role, $response_text, $is_override = 0)
    {
        $this->db->insert('remark_pushback_response', [
            'question_id' => $question_id,
            'responder_uid' => $responder_uid,
            'responder_role' => $responder_role,
            'is_override' => $is_override,
            'response_text' => $response_text,
            'response_length_chars' => mb_strlen($response_text, 'UTF-8'),
            'responded_at' => date('Y-m-d H:i:s'),
        ]);
        $resp_id = (int)$this->db->insert_id();

        $new_status = $is_override ? 'overridden' : 'answered';
        $this->db->where('id', $question_id)->update('remark_pushback_question', [
            'status' => $new_status,
            'answered_at' => date('Y-m-d H:i:s'),
        ]);
        return $resp_id;
    }
}
