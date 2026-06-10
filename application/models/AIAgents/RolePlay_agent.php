<?php
/**
 * RolePlay_model.php
 * Location: application/AIAgents/RolePlay_model.php
 *
 * Migration 054 - AI Role-Play Coaching for Business Development.
 *
 * Responsibilities:
 *  1. Build persona-grounded system prompts using real CRM context
 *     (init_call, M051 stakeholder_map, M052 objection_instance).
 *  2. Orchestrate turn-by-turn LLM calls during a session.
 *  3. Score completed sessions across 4 dimensions (0-25 each).
 *  4. Enforce the Rs 5 per-session cost cap (20-turn hard limit).
 *  5. Check the induction gate (5 mandatory drills complete).
 *
 * Standing rules:
 *  - Plain English, no em-dashes, no non-ASCII.
 *  - "Rs" for rupees, "percent" spelled out, "over" for greater than.
 *  - BearerAuth enforced by the calling controller, not here.
 *  - Pilot guard: caller must pre-check feature_flag before calling.
 *
 * Type IDs: 1=BD, 13=CM, 25=SH, 26=ACM, 27=AO, 28=RM
 *
 * WB pilot uids: SC 1000356, BDs 1000289+1000351, CM 1000305, RM 1000269
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class RolePlay_model extends CI_Model
{

    /** INR / USD exchange rate used for cost display. Update monthly. */
    const INR_PER_USD = 83.5;

    /** Hard cap per session. After this many turns, session ends. */
    const MAX_TURNS_PER_SESSION = 20;

    /** LLM cost ceiling in INR. Sessions over this are flagged in run log. */
    const COST_CAP_RS = 5.0;

    /** LLM model used for persona turns. */
    const TURN_MODEL = 'gpt-4o-mini';

    /** LLM model used for end-of-session scoring (same budget). */
    const SCORE_MODEL = 'gpt-4o-mini';

    /** Maximum characters to pull from a single objection_instance.detail. */
    const MAX_OBJECTION_CHARS = 200;

    /** WB pilot uid list. Read from feature_flag at runtime when flag=1. */
    private static $PILOT_UIDS = [1000289, 1000351, 1000305, 1000269, 1000356];


    // ---------------------------------------------------------------
    // PUBLIC: Session lifecycle
    // ---------------------------------------------------------------

    /**
     * start_session
     *
     * Creates a role_play_session row and returns the session id.
     * For pre_meeting mode, builds context from init_call + M051 + M052.
     * For drill and induction modes, uses scenario defaults.
     *
     * @param  int    $bd_uid
     * @param  string $scenario_code
     * @param  string $mode          pre_meeting | drill | induction
     * @param  int    $cid_id        NULL unless mode = pre_meeting
     * @param  int    $event_id      NULL unless mode = pre_meeting
     * @param  int    $assignment_id NULL unless mode = drill | induction
     * @return array  ['session_id' => int, 'system_prompt' => string,
     *                 'persona_role' => string, 'persona_name' => string,
     *                 'school_name' => string]
     */
    public function start_session(
        $bd_uid,
        $scenario_code,
        $mode,
        $cid_id        = null,
        $event_id      = null,
        $assignment_id = null
    ) {
        $scenario = $this->get_scenario($scenario_code);
        if (empty($scenario)) {
            return ['error' => 'scenario_not_found'];
        }

        if ($mode === 'pre_meeting' && !empty($cid_id)) {
            $context = $this->build_pre_meeting_context($cid_id, $scenario);
        } else {
            $context = $this->build_generic_context($scenario);
        }

        $system_prompt = $this->build_system_prompt($scenario, $context);

        $row = [
            'bd_uid'               => (int) $bd_uid,
            'scenario_code'        => $scenario_code,
            'mode'                 => $mode,
            'cid_id'               => $cid_id ? (int) $cid_id : null,
            'event_id'             => $event_id ? (int) $event_id : null,
            'drill_assignment_id'  => $assignment_id ? (int) $assignment_id : null,
            'persona_role_used'    => $context['persona_role'],
            'persona_name_used'    => $context['persona_name'],
            'school_name_snapshot' => $context['school_name'],
            'system_prompt_text'   => $system_prompt,
            'status'               => 'in_progress',
            'started_at'           => date('Y-m-d H:i:s'),
            'llm_model'            => self::TURN_MODEL,
        ];

        $this->db->insert('role_play_session', $row);
        $session_id = $this->db->insert_id();

        // If assignment exists, flip it to in_progress
        if (!empty($assignment_id)) {
            $this->db->where('id', (int) $assignment_id)
                     ->update('role_play_drill_assignment',
                              ['status' => 'in_progress']);
        }

        return [
            'session_id'   => $session_id,
            'system_prompt' => $system_prompt,
            'persona_role'  => $context['persona_role'],
            'persona_name'  => $context['persona_name'],
            'school_name'   => $context['school_name'],
        ];
    }


    /**
     * post_turn
     *
     * Accepts one BD message, calls the LLM for an AI response,
     * saves both turns to role_play_turn, updates session token counts.
     *
     * Returns the AI reply and a flag indicating whether the session
     * has hit the 20-turn cap.
     *
     * @param  int    $session_id
     * @param  int    $bd_uid       For ownership check
     * @param  string $bd_message
     * @return array  ['ai_reply' => string,
     *                 'turn_number' => int,
     *                 'session_limit_reached' => bool,
     *                 'cost_rs' => float]
     */
    public function post_turn($session_id, $bd_uid, $bd_message)
    {
        $session = $this->get_session_row($session_id);
        if (empty($session) || (int) $session['bd_uid'] !== (int) $bd_uid) {
            return ['error' => 'session_not_found_or_not_owner'];
        }
        if ($session['status'] !== 'in_progress') {
            return ['error' => 'session_not_active'];
        }

        $current_turns = (int) $session['turn_count'];
        // Each BD+AI exchange = 2 turns. Check before adding.
        if ($current_turns >= self::MAX_TURNS_PER_SESSION) {
            return [
                'ai_reply'             => '',
                'turn_number'          => $current_turns,
                'session_limit_reached' => true,
                'cost_rs'              => (float) $session['total_cost_rs'],
            ];
        }

        // Build message history for context
        $history   = $this->get_turn_history($session_id);
        $messages  = $this->build_messages_array(
            $session['system_prompt_text'],
            $history,
            $bd_message
        );

        // Call LLM
        $llm_result = $this->call_llm(self::TURN_MODEL, $messages, 300);
        if (!empty($llm_result['error'])) {
            return ['error' => 'llm_call_failed: ' . $llm_result['error']];
        }

        $ai_reply    = $llm_result['content'];
        $tokens_in   = $llm_result['usage']['prompt_tokens'];
        $tokens_out  = $llm_result['usage']['completion_tokens'];
        $cost_usd    = $this->estimate_cost_usd(
            self::TURN_MODEL, $tokens_in, $tokens_out
        );
        $cost_rs     = round($cost_usd * self::INR_PER_USD, 4);

        // Save BD turn
        $bd_turn_num = $current_turns + 1;
        $this->insert_turn($session_id, $bd_turn_num, 'bd', $bd_message, $tokens_in);

        // Save AI turn
        $ai_turn_num = $bd_turn_num + 1;
        $this->insert_turn($session_id, $ai_turn_num, 'ai', $ai_reply, $tokens_out);

        // Update session aggregates
        $new_turn_count  = $ai_turn_num;
        $new_tokens_in   = (int) $session['total_tokens_in'] + $tokens_in;
        $new_tokens_out  = (int) $session['total_tokens_out'] + $tokens_out;
        $new_cost_usd    = (float) $session['total_cost_usd'] + $cost_usd;
        $new_cost_rs     = (float) $session['total_cost_rs'] + $cost_rs;

        $this->db->where('id', $session_id)
                 ->update('role_play_session', [
                     'turn_count'       => $new_turn_count,
                     'total_tokens_in'  => $new_tokens_in,
                     'total_tokens_out' => $new_tokens_out,
                     'total_cost_usd'   => $new_cost_usd,
                     'total_cost_rs'    => $new_cost_rs,
                 ]);

        $limit_reached = ($new_turn_count >= self::MAX_TURNS_PER_SESSION);

        return [
            'ai_reply'              => $ai_reply,
            'turn_number'           => $ai_turn_num,
            'session_limit_reached' => $limit_reached,
            'cost_rs'               => $new_cost_rs,
        ];
    }


    /**
     * end_session
     *
     * Marks the session complete, calls the scoring LLM, writes
     * role_play_score, updates drill_assignment if applicable, and
     * checks induction gate.
     *
     * @param  int   $session_id
     * @param  int   $bd_uid
     * @param  int   $satisfaction_stars  1-5 from BD, nullable
     * @return array Score result plus induction gate status
     */
    public function end_session($session_id, $bd_uid, $satisfaction_stars = null)
    {
        $session = $this->get_session_row($session_id);
        if (empty($session) || (int) $session['bd_uid'] !== (int) $bd_uid) {
            return ['error' => 'session_not_found_or_not_owner'];
        }
        if ($session['status'] !== 'in_progress') {
            return ['error' => 'session_not_active'];
        }

        // Mark session complete
        $update = [
            'status'   => 'completed',
            'ended_at' => date('Y-m-d H:i:s'),
        ];
        if (!empty($satisfaction_stars)) {
            $update['bd_satisfaction_stars'] = max(1, min(5, (int) $satisfaction_stars));
        }
        $this->db->where('id', $session_id)->update('role_play_session', $update);

        // Score the session
        $score = $this->score_session($session_id, $session);
        if (empty($score['error'])) {
            $this->write_score_row($session_id, $bd_uid,
                                   $session['scenario_code'], $score);
        }

        // Update drill assignment
        $assignment_id = $session['drill_assignment_id'];
        if (!empty($assignment_id)) {
            $this->db->where('id', (int) $assignment_id)
                     ->update('role_play_drill_assignment', [
                         'status'       => 'completed',
                         'session_id'   => (int) $session_id,
                         'completed_at' => date('Y-m-d H:i:s'),
                     ]);
        }

        // Check induction gate
        $induction_status = $this->check_induction_gate($bd_uid);

        $result = [
            'session_id'       => $session_id,
            'status'           => 'completed',
            'score'            => $score,
            'induction_status' => $induction_status,
        ];
        return $result;
    }


    // ---------------------------------------------------------------
    // PUBLIC: Context builders
    // ---------------------------------------------------------------

    /**
     * build_pre_meeting_context
     *
     * Pulls school context from init_call, the primary mapped
     * stakeholder from M051 stakeholder_map, and the top 3 known
     * objections from M052 objection_instance.
     *
     * @param  int   $cid_id
     * @param  array $scenario  Scenario row for fallback values
     * @return array Context array for build_system_prompt
     */
    public function build_pre_meeting_context($cid_id, $scenario)
    {
        // School data from init_call
        $lead = $this->db
            ->select('cid_id, school_name, category, cluster, cstatus')
            ->where('cid_id', (int) $cid_id)
            ->get('init_call')
            ->row_array();

        $school_name = !empty($lead['school_name']) ? $lead['school_name'] : 'the school';
        $category    = !empty($lead['category'])    ? $lead['category']    : '';
        $cluster     = !empty($lead['cluster'])     ? $lead['cluster']     : '';

        // Primary stakeholder from M051
        $stakeholder = $this->db
            ->select('stakeholder_name, stakeholder_role, stakeholder_tenure')
            ->where('cid_id', (int) $cid_id)
            ->where_in('stakeholder_role',
                ['Principal', 'Trustee', 'Correspondent', 'Director', 'Manager'])
            ->order_by('priority', 'ASC')
            ->limit(1)
            ->get('stakeholder_map')
            ->row_array();

        $persona_name = !empty($stakeholder['stakeholder_name'])
            ? $stakeholder['stakeholder_name']
            : '';
        $persona_role = !empty($stakeholder['stakeholder_role'])
            ? $stakeholder['stakeholder_role']
            : $scenario['persona_role'];

        // Top 3 known objections from M052
        $objection_rows = $this->db
            ->select('objection_type, objection_detail')
            ->where('cid_id', (int) $cid_id)
            ->order_by('severity', 'DESC')
            ->limit(3)
            ->get('objection_instance')
            ->result_array();

        $known_objections = [];
        foreach ($objection_rows as $obj) {
            $detail = substr($obj['objection_detail'], 0, self::MAX_OBJECTION_CHARS);
            $known_objections[] = $obj['objection_type'] . ': ' . $detail;
        }
        // Fall back to scenario expected_objections if none in CRM
        if (empty($known_objections)) {
            $fallback = json_decode($scenario['expected_objections_json'], true);
            $known_objections = is_array($fallback) ? $fallback : [];
        }

        return [
            'school_name'      => $school_name,
            'category'         => $category,
            'cluster'          => $cluster,
            'persona_name'     => $persona_name,
            'persona_role'     => $persona_role,
            'persona_traits'   => $scenario['persona_traits'],
            'known_objections' => $known_objections,
            'starting_context' => $scenario['starting_context'],
            'is_pre_meeting'   => true,
        ];
    }


    /**
     * build_generic_context
     *
     * Used for drill and induction modes where no specific school
     * is linked. Uses scenario defaults only.
     *
     * @param  array $scenario
     * @return array Context array for build_system_prompt
     */
    public function build_generic_context($scenario)
    {
        $fallback_objections = json_decode(
            $scenario['expected_objections_json'], true
        );
        return [
            'school_name'      => 'a school',
            'category'         => '',
            'cluster'          => '',
            'persona_name'     => '',
            'persona_role'     => $scenario['persona_role'],
            'persona_traits'   => $scenario['persona_traits'],
            'known_objections' => is_array($fallback_objections)
                                  ? $fallback_objections : [],
            'starting_context' => $scenario['starting_context'],
            'is_pre_meeting'   => false,
        ];
    }


    /**
     * build_system_prompt
     *
     * Assembles the 4-section persona system prompt from context.
     * Sections: PERSONA IDENTITY, SCHOOL CONTEXT, KNOWN OBJECTIONS,
     * BEHAVIOUR RULES.
     *
     * @param  array $scenario
     * @param  array $context  From build_pre_meeting_context or
     *                         build_generic_context
     * @return string
     */
    public function build_system_prompt($scenario, $context)
    {
        $name_line = !empty($context['persona_name'])
            ? 'Your name is ' . $context['persona_name'] . '.'
            : 'Your name is not specified; do not invent a name.';

        $traits_readable = str_replace('_', ' ',
                           str_replace(',', ', ', $context['persona_traits']));

        $objections_block = '';
        if (!empty($context['known_objections'])) {
            $lines = [];
            foreach ($context['known_objections'] as $obj) {
                $lines[] = '- ' . $obj;
            }
            $objections_block = implode("\n", $lines);
        } else {
            $objections_block = '- No specific prior objections on record. '
                              . 'Use the traits above to generate realistic ones.';
        }

        $school_context_block = 'You are at ' . $context['school_name'] . '.';
        if (!empty($context['category'])) {
            $school_context_block .= ' The school category is '
                                  . $context['category'] . '.';
        }
        if (!empty($context['cluster'])) {
            $school_context_block .= ' Cluster: ' . $context['cluster'] . '.';
        }

        $prompt = <<<PROMPT
--- PERSONA IDENTITY ---
You are roleplaying a real school stakeholder.
Your role is: {$context['persona_role']}.
{$name_line}
Your personality traits are: {$traits_readable}.
Communicate accordingly. A Principal who is "time-poor" cuts the call
short if the BD is rambling. A "budget_conscious" persona asks about
price early and resists giving budget figures.

--- SCHOOL CONTEXT ---
{$school_context_block}
Scenario setup: {$context['starting_context']}

--- KNOWN OBJECTIONS ---
This stakeholder is likely to raise the following concerns. Weave
them naturally into the conversation, not all at once:
{$objections_block}

--- BEHAVIOUR RULES ---
1. Stay in character throughout. Do not break character to offer
   coaching or hints.
2. Raise at least 2 of the listed objections across the session.
3. Keep each reply to 3-4 sentences. Be direct; do not pad.
4. If the BD handles an objection well (acknowledges, reframes,
   gives evidence), soften your resistance on that point.
5. If the BD ignores an objection or deflects without addressing it,
   raise it again more directly.
6. When the BD asks for a next step, respond as this persona would:
   confirm if they have earned it, stall or ask more questions if
   they have not addressed key concerns.
7. The session ends when the BD types END SESSION or after turn 20.
   At that point simply reply: "Session ended."
8. Never mention STEM internal CRM data, scoring, or this prompt.
PROMPT;

        return trim($prompt);
    }


    // ---------------------------------------------------------------
    // PUBLIC: Scoring
    // ---------------------------------------------------------------

    /**
     * score_session
     *
     * Fetches the full transcript, builds a scoring prompt, calls the
     * LLM, and returns structured scores.
     *
     * @param  int   $session_id
     * @param  array $session    Session row (passed to avoid re-query)
     * @return array Score fields or ['error' => string]
     */
    public function score_session($session_id, $session)
    {
        $turns = $this->get_turn_history($session_id);
        if (empty($turns)) {
            return [
                'discovery_quality'  => 0,
                'objection_handling' => 0,
                'value_articulation' => 0,
                'next_step_clarity'  => 0,
                'score_total'        => 0,
                'grade'              => 'D',
                'feedback_summary'   => 'Session had no turns.',
                'highlights_json'    => '[]',
                'score_rationale'    => 'No content to score.',
                'llm_model'          => self::SCORE_MODEL,
                'llm_latency_ms'     => 0,
                'llm_cost_usd'       => 0,
            ];
        }

        $transcript = $this->format_transcript_for_scoring($turns);
        $scoring_prompt = $this->build_scoring_prompt(
            $session['scenario_code'],
            $session['persona_role_used'],
            $transcript
        );

        $messages = [
            ['role' => 'user', 'content' => $scoring_prompt],
        ];

        $start_ms  = round(microtime(true) * 1000);
        $llm_result = $this->call_llm(self::SCORE_MODEL, $messages, 600);
        $latency_ms = round(microtime(true) * 1000) - $start_ms;

        if (!empty($llm_result['error'])) {
            return ['error' => 'scoring_llm_failed: ' . $llm_result['error']];
        }

        $json_text  = $this->extract_json_from_llm_response(
            $llm_result['content']
        );
        $parsed     = json_decode($json_text, true);

        if (empty($parsed) || !isset($parsed['discovery_quality'])) {
            return ['error' => 'scoring_parse_failed',
                    'raw'   => $llm_result['content']];
        }

        $d  = min(25, max(0, (int) $parsed['discovery_quality']));
        $o  = min(25, max(0, (int) $parsed['objection_handling']));
        $v  = min(25, max(0, (int) $parsed['value_articulation']));
        $n  = min(25, max(0, (int) $parsed['next_step_clarity']));
        $total = $d + $o + $v + $n;
        $grade = $this->compute_grade($total);

        $tokens_in  = $llm_result['usage']['prompt_tokens'];
        $tokens_out = $llm_result['usage']['completion_tokens'];
        $cost_usd   = $this->estimate_cost_usd(
            self::SCORE_MODEL, $tokens_in, $tokens_out
        );

        return [
            'discovery_quality'  => $d,
            'objection_handling' => $o,
            'value_articulation' => $v,
            'next_step_clarity'  => $n,
            'score_total'        => $total,
            'grade'              => $grade,
            'feedback_summary'   => isset($parsed['feedback_summary'])
                                    ? substr($parsed['feedback_summary'], 0, 800)
                                    : '',
            'highlights_json'    => isset($parsed['highlights'])
                                    ? json_encode($parsed['highlights'])
                                    : '[]',
            'score_rationale'    => isset($parsed['score_rationale'])
                                    ? substr($parsed['score_rationale'], 0, 512)
                                    : '',
            'llm_model'          => self::SCORE_MODEL,
            'llm_latency_ms'     => $latency_ms,
            'llm_cost_usd'       => $cost_usd,
        ];
    }


    /**
     * build_scoring_prompt
     *
     * Returns the scoring prompt to send to the LLM. Instructs it to
     * return valid JSON only with the four score fields plus feedback.
     *
     * @param  string $scenario_code
     * @param  string $persona_role
     * @param  string $transcript
     * @return string
     */
    public function build_scoring_prompt($scenario_code, $persona_role, $transcript)
    {
        return <<<PROMPT
You are an expert sales coaching evaluator. Read the following role-play
transcript in which a BD practices a conversation with a {$persona_role}
persona (scenario: {$scenario_code}).

Score the BD's performance across exactly 4 dimensions, each 0 to 25:

1. discovery_quality (0-25):
   Did the BD ask open questions about budget authority, timeline,
   decision-making process and stakeholder map? Generic openers score 0.
   Named DM confirmed, timeline captured, decision body identified = 25.

2. objection_handling (0-25):
   Did the BD acknowledge, reframe and provide evidence for each
   objection raised? Deflecting or ignoring = 0. Full LAER method
   (Listen, Acknowledge, Explore, Respond) applied = 25.

3. value_articulation (0-25):
   Did the BD tie STEM product capabilities to outcomes the stakeholder
   said they care about? Generic feature list = 0. Bespoke outcome
   story with data = 25.

4. next_step_clarity (0-25):
   Did the BD close with a specific next step: action, date and named
   attendee? "I will follow up" = 0. Confirmed date, action, and who
   attends = 25.

Respond ONLY with valid JSON in this exact format:
{
  "discovery_quality": <integer 0-25>,
  "objection_handling": <integer 0-25>,
  "value_articulation": <integer 0-25>,
  "next_step_clarity": <integer 0-25>,
  "feedback_summary": "<plain English 4-6 lines, no em-dashes>",
  "highlights": [
    {"dimension": "discovery_quality", "strength": "<1 sentence>", "improvement": "<1 sentence>"},
    {"dimension": "objection_handling", "strength": "<1 sentence>", "improvement": "<1 sentence>"},
    {"dimension": "value_articulation", "strength": "<1 sentence>", "improvement": "<1 sentence>"},
    {"dimension": "next_step_clarity", "strength": "<1 sentence>", "improvement": "<1 sentence>"}
  ],
  "score_rationale": "<under 100 words explaining the total>"
}

TRANSCRIPT:
{$transcript}
PROMPT;
    }


    // ---------------------------------------------------------------
    // PUBLIC: Induction gate
    // ---------------------------------------------------------------

    /**
     * check_induction_gate
     *
     * Returns how many of the 5 mandatory induction drills this BD
     * has completed and whether the gate is satisfied.
     *
     * @param  int  $bd_uid
     * @return array ['total_required' => 5,
     *                'completed' => int,
     *                'gate_passed' => bool]
     */
    public function check_induction_gate($bd_uid)
    {
        $total_required = 5;

        $completed = (int) $this->db
            ->where('bd_uid', (int) $bd_uid)
            ->where('induction_required', 1)
            ->where('status', 'completed')
            ->count_all_results('role_play_drill_assignment');

        return [
            'total_required' => $total_required,
            'completed'      => $completed,
            'gate_passed'    => ($completed >= $total_required),
        ];
    }


    /**
     * seed_induction_drills
     *
     * Called when a new induction_sequence row is created (M045 hook).
     * Inserts 5 mandatory role_play_drill_assignment rows for the BD.
     *
     * @param  int  $bd_uid
     * @param  int  $induction_sequence_id
     * @return void
     */
    public function seed_induction_drills($bd_uid, $induction_sequence_id)
    {
        $mandatory_scenarios = [
            'DISCOVERY_FRESH_LEAD',
            'PRICE_OBJECTION_PRINCIPAL',
            'TRUSTEE_MEETING_FIRST_TIME',
            'BUDGET_TIMING_DEFER',
            'CLOSING_VERY_POSITIVE',
        ];

        $due_date = date('Y-m-d', strtotime('+3 weekdays'));

        foreach ($mandatory_scenarios as $code) {
            // Avoid duplicate seeding
            $exists = $this->db
                ->where('bd_uid', (int) $bd_uid)
                ->where('scenario_code', $code)
                ->where('induction_required', 1)
                ->count_all_results('role_play_drill_assignment');
            if ($exists > 0) {
                continue;
            }
            $this->db->insert('role_play_drill_assignment', [
                'bd_uid'               => (int) $bd_uid,
                'scenario_code'        => $code,
                'assigned_by_uid'      => null,
                'source'               => 'induction',
                'induction_required'   => 1,
                'induction_sequence_id' => (int) $induction_sequence_id,
                'due_date'             => $due_date,
                'status'               => 'pending',
                'assigned_at'          => date('Y-m-d H:i:s'),
            ]);
        }
    }


    // ---------------------------------------------------------------
    // PUBLIC: Queries used by controller
    // ---------------------------------------------------------------

    /**
     * get_scenario
     *
     * @param  string $scenario_code
     * @return array|null
     */
    public function get_scenario($scenario_code)
    {
        return $this->db
            ->where('scenario_code', $scenario_code)
            ->where('is_active', 1)
            ->get('role_play_scenario')
            ->row_array();
    }


    /**
     * list_scenarios
     *
     * @return array
     */
    public function list_scenarios()
    {
        return $this->db
            ->select('scenario_code, scenario_name, persona_role,
                      persona_traits, starting_context,
                      expected_objections_json, success_criteria,
                      is_seed, is_induction_required, display_order')
            ->where('is_active', 1)
            ->order_by('display_order', 'ASC')
            ->get('role_play_scenario')
            ->result_array();
    }


    /**
     * get_session_row
     *
     * @param  int $session_id
     * @return array|null
     */
    public function get_session_row($session_id)
    {
        return $this->db
            ->where('id', (int) $session_id)
            ->get('role_play_session')
            ->row_array();
    }


    /**
     * get_session_with_score
     *
     * Returns session + turns + score for coach review.
     *
     * @param  int $session_id
     * @return array
     */
    public function get_session_with_score($session_id)
    {
        $session = $this->get_session_row($session_id);
        if (empty($session)) {
            return ['error' => 'not_found'];
        }
        $turns = $this->db
            ->where('session_id', (int) $session_id)
            ->order_by('turn_number', 'ASC')
            ->get('role_play_turn')
            ->result_array();
        $score = $this->db
            ->where('session_id', (int) $session_id)
            ->get('role_play_score')
            ->row_array();
        return [
            'session' => $session,
            'turns'   => $turns,
            'score'   => $score ?: null,
        ];
    }


    /**
     * list_my_sessions
     *
     * @param  int $bd_uid
     * @param  int $limit
     * @return array
     */
    public function list_my_sessions($bd_uid, $limit = 20)
    {
        return $this->db
            ->select('ses.id, ses.scenario_code, sc.scenario_name,
                      ses.mode, ses.status, ses.started_at, ses.ended_at,
                      ses.turn_count, ses.total_cost_rs,
                      ses.bd_satisfaction_stars,
                      rs.score_total, rs.grade')
            ->from('role_play_session ses')
            ->join('role_play_scenario sc',
                   'sc.scenario_code = ses.scenario_code', 'left')
            ->join('role_play_score rs',
                   'rs.session_id = ses.id', 'left')
            ->where('ses.bd_uid', (int) $bd_uid)
            ->order_by('ses.started_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }


    /**
     * list_my_drills
     *
     * Returns pending and in-progress drill assignments for a BD.
     *
     * @param  int $bd_uid
     * @return array
     */
    public function list_my_drills($bd_uid)
    {
        return $this->db
            ->select('da.id, da.scenario_code, sc.scenario_name,
                      sc.persona_role, da.source, da.induction_required,
                      da.due_date, da.status, da.assigned_at,
                      da.completed_at, rs.score_total, rs.grade')
            ->from('role_play_drill_assignment da')
            ->join('role_play_scenario sc',
                   'sc.scenario_code = da.scenario_code', 'left')
            ->join('role_play_session ses',
                   'ses.id = da.session_id', 'left')
            ->join('role_play_score rs',
                   'rs.session_id = da.session_id', 'left')
            ->where('da.bd_uid', (int) $bd_uid)
            ->order_by('da.induction_required', 'DESC')
            ->order_by('da.due_date', 'ASC')
            ->get()
            ->result_array();
    }


    /**
     * assign_drill
     *
     * Called by CM/RM via coach endpoint.
     *
     * @param  int    $assigned_by_uid
     * @param  int    $bd_uid
     * @param  string $scenario_code
     * @param  string $due_date       Y-m-d
     * @return array  ['assignment_id' => int]
     */
    public function assign_drill($assigned_by_uid, $bd_uid,
                                  $scenario_code, $due_date = null)
    {
        $this->db->insert('role_play_drill_assignment', [
            'bd_uid'          => (int) $bd_uid,
            'scenario_code'   => $scenario_code,
            'assigned_by_uid' => (int) $assigned_by_uid,
            'source'          => 'cm',
            'induction_required' => 0,
            'due_date'        => $due_date,
            'status'          => 'pending',
            'assigned_at'     => date('Y-m-d H:i:s'),
        ]);
        return ['assignment_id' => $this->db->insert_id()];
    }


    /**
     * coach_review
     *
     * CM adds a coaching note to a completed assignment.
     *
     * @param  int    $assignment_id
     * @param  int    $reviewer_uid
     * @param  string $cm_note
     * @return bool
     */
    public function coach_review($assignment_id, $reviewer_uid, $cm_note)
    {
        $this->db->where('id', (int) $assignment_id)
                 ->update('role_play_drill_assignment', [
                     'cm_note'       => $cm_note,
                     'reviewed_by_uid' => (int) $reviewer_uid,
                     'reviewed_at'   => date('Y-m-d H:i:s'),
                 ]);
        return $this->db->affected_rows() > 0;
    }


    /**
     * get_upcoming_meeting
     *
     * Checks tblcallevents for a meeting within the next 15 minutes
     * for the requesting BD that has a cid_id set.
     *
     * @param  int $bd_uid
     * @return array|null
     */
    public function get_upcoming_meeting($bd_uid)
    {
        $now     = date('Y-m-d H:i:s');
        $in15min = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // audit_20260606: corrected column names for tblcallevents
        // id -> event_id alias; assignedto_id (not assigned_uid); purpose_id (not event_purpose)
        try {
            $row = $this->db
                ->select('id AS event_id, cid_id, event_date, purpose_id AS event_purpose')
                ->where('assignedto_id', (int) $bd_uid)
                ->where('cid_id IS NOT NULL', null, false)
                ->where('event_date >=', $now)
                ->where('event_date <=', $in15min)
                ->order_by('event_date', 'ASC')
                ->limit(1)
                ->get('tblcallevents')
                ->row_array();
        } catch (Exception $e) {
            return null;
        }

        return !empty($row) ? $row : null;
    }


    /**
     * abandon_stale_sessions
     *
     * Called by the nightly rhythm_orchestrator (M035) extension at
     * 23:00 IST. Sets status = abandoned for any in_progress session
     * older than 6 hours.
     *
     * @return int  Number of sessions abandoned
     */
    public function abandon_stale_sessions()
    {
        $cutoff = date('Y-m-d H:i:s', strtotime('-6 hours'));
        $this->db
            ->where('status', 'in_progress')
            ->where('started_at <', $cutoff)
            ->update('role_play_session', ['status' => 'abandoned']);
        return $this->db->affected_rows();
    }


    // ---------------------------------------------------------------
    // PRIVATE: Helpers
    // ---------------------------------------------------------------

    /**
     * get_turn_history
     *
     * @param  int $session_id
     * @return array
     */
    private function get_turn_history($session_id)
    {
        return $this->db
            ->select('turn_number, speaker, message_text')
            ->where('session_id', (int) $session_id)
            ->order_by('turn_number', 'ASC')
            ->get('role_play_turn')
            ->result_array();
    }


    /**
     * build_messages_array
     *
     * Assembles the OpenAI-style messages array from system prompt,
     * prior turn history, and the new BD message.
     *
     * @param  string $system_prompt
     * @param  array  $history
     * @param  string $new_bd_message
     * @return array
     */
    private function build_messages_array($system_prompt, $history, $new_bd_message)
    {
        $messages = [
            ['role' => 'system', 'content' => $system_prompt],
        ];
        foreach ($history as $turn) {
            $role = ($turn['speaker'] === 'bd') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $turn['message_text']];
        }
        $messages[] = ['role' => 'user', 'content' => $new_bd_message];
        return $messages;
    }


    /**
     * insert_turn
     *
     * @param  int    $session_id
     * @param  int    $turn_number
     * @param  string $speaker       bd | ai
     * @param  string $message_text
     * @param  int    $token_count
     * @return void
     */
    private function insert_turn($session_id, $turn_number,
                                  $speaker, $message_text, $token_count)
    {
        $this->db->insert('role_play_turn', [
            'session_id'   => (int) $session_id,
            'turn_number'  => (int) $turn_number,
            'speaker'      => $speaker,
            'message_text' => $message_text,
            'token_count'  => (int) $token_count,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }


    /**
     * write_score_row
     *
     * @param  int    $session_id
     * @param  int    $bd_uid
     * @param  string $scenario_code
     * @param  array  $score
     * @return void
     */
    private function write_score_row($session_id, $bd_uid,
                                      $scenario_code, $score)
    {
        $this->db->insert('role_play_score', [
            'session_id'         => (int) $session_id,
            'bd_uid'             => (int) $bd_uid,
            'scenario_code'      => $scenario_code,
            'discovery_quality'  => $score['discovery_quality'],
            'objection_handling' => $score['objection_handling'],
            'value_articulation' => $score['value_articulation'],
            'next_step_clarity'  => $score['next_step_clarity'],
            'score_total'        => $score['score_total'],
            'grade'              => $score['grade'],
            'feedback_summary'   => $score['feedback_summary'],
            'highlights_json'    => $score['highlights_json'],
            'score_rationale'    => $score['score_rationale'],
            'llm_model'          => $score['llm_model'],
            'llm_latency_ms'     => $score['llm_latency_ms'],
            'llm_cost_usd'       => $score['llm_cost_usd'],
            'scored_at'          => date('Y-m-d H:i:s'),
        ]);
    }


    /**
     * format_transcript_for_scoring
     *
     * Formats turn history as plain text for the scoring prompt.
     *
     * @param  array $turns
     * @return string
     */
    private function format_transcript_for_scoring($turns)
    {
        $lines = [];
        foreach ($turns as $turn) {
            $label = ($turn['speaker'] === 'bd') ? 'BD' : 'PERSONA';
            $lines[] = '[' . $label . ' Turn ' . $turn['turn_number'] . '] '
                     . $turn['message_text'];
        }
        return implode("\n\n", $lines);
    }


    /**
     * extract_json_from_llm_response
     *
     * Strips markdown code fences if the LLM wrapped the JSON.
     *
     * @param  string $raw
     * @return string
     */
    private function extract_json_from_llm_response($raw)
    {
        $raw = trim($raw);
        // Remove ```json ... ``` wrapping
        if (strpos($raw, '```') !== false) {
            $raw = preg_replace('/```json\s*/i', '', $raw);
            $raw = preg_replace('/```/', '', $raw);
            $raw = trim($raw);
        }
        return $raw;
    }


    /**
     * compute_grade
     *
     * A+ 90+, A 75+, B 60+, C 40+, D under 40
     *
     * @param  int $total  0-100
     * @return string
     */
    private function compute_grade($total)
    {
        if ($total >= 90) return 'A+';
        if ($total >= 75) return 'A';
        if ($total >= 60) return 'B';
        if ($total >= 40) return 'C';
        return 'D';
    }


    /**
     * estimate_cost_usd
     *
     * Approximate cost for gpt-4o-mini.
     * Pricing at time of migration: input $0.00015/1K, output $0.00060/1K.
     * Update these constants when pricing changes.
     *
     * @param  string $model
     * @param  int    $tokens_in
     * @param  int    $tokens_out
     * @return float
     */
    private function estimate_cost_usd($model, $tokens_in, $tokens_out)
    {
        // gpt-4o-mini (default)
        $in_rate  = 0.00015 / 1000;
        $out_rate  = 0.00060 / 1000;
        return ($tokens_in * $in_rate) + ($tokens_out * $out_rate);
    }


    /**
     * call_llm
     *
     * Thin wrapper around the STEM LLM gateway (same pattern as
     * RemarkCoherence_model.php in M049). Uses OPENAI_API_KEY from
     * CI config or environment.
     *
     * @param  string $model
     * @param  array  $messages
     * @param  int    $max_tokens
     * @return array  ['content' => string, 'usage' => array]
     *                or ['error' => string]
     */
    private function call_llm($model, $messages, $max_tokens = 400)
    {
        $api_key = getenv('OPENAI_API_KEY')
                   ?: $this->config->item('openai_api_key');
        if (empty($api_key)) {
            return ['error' => 'openai_api_key_not_configured'];
        }

        $payload = json_encode([
            'model'       => $model,
            'messages'    => $messages,
            'max_tokens'  => $max_tokens,
            'temperature' => 0.7,
        ]);

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key,
            ],
        ]);

        $raw      = curl_exec($ch);
        $curl_err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!empty($curl_err)) {
            return ['error' => 'curl_error: ' . $curl_err];
        }

        $resp = json_decode($raw, true);

        if ($http_code !== 200 || empty($resp['choices'][0]['message']['content'])) {
            $err_msg = isset($resp['error']['message'])
                       ? $resp['error']['message']
                       : 'http_' . $http_code;
            return ['error' => $err_msg];
        }

        return [
            'content' => $resp['choices'][0]['message']['content'],
            'usage'   => [
                'prompt_tokens'     => $resp['usage']['prompt_tokens']     ?? 0,
                'completion_tokens' => $resp['usage']['completion_tokens'] ?? 0,
            ],
        ];
    }

}
// End RolePlay_model.php
