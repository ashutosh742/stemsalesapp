<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Greetings Drafter Agent
 * Migration 036 (BD Coach + Greetings + Knowledge Repository)
 *
 * Responsibilities:
 *  1. Daily cron: scan festivals, birthdays, anniversaries; generate 3-variant drafts
 *  2. Win broadcast: triggered by Won closure over Rs 10 lakh
 *  3. Seasonal touch: quarterly nudge for dormant leads
 *  4. Loss recovery: 90-day re-engagement after Lost (cstatus 13)
 *  5. Approval and rejection workflow for CM/BD
 *
 * CRITICAL: Never auto-send. All paths end at 'approved_ready_to_send' status.
 *           BD manually triggers actual send from the UI.
 *
 * Rate limits (enforced before draft creation):
 *  - Max 3 messages per recipient per quarter
 *  - Festival messages only to schools with at least one prior tblcallevents touch
 *
 * LLM: Claude Sonnet 4.6 via $this->llm->call() placeholder.
 *
 * Migration 036. Author: STEM ops, 2026-05-18.
 */
class Greetings_drafter_agent extends CI_Model
{
    // Three-day pre-window for festival drafts.
    const FESTIVAL_PRE_WINDOW_DAYS = 3;

    // Minimum pipeline budget in rupees for win broadcast.
    const WIN_BROADCAST_MIN_RS = 1000000; // Rs 10 lakh

    // Days after Lost cstatus before recovery outreach draft.
    const LOSS_RECOVERY_DAYS = 90;

    // Max messages per recipient per quarter.
    const MAX_MSGS_PER_QUARTER = 3;

    // Claude model for greeting generation.
    const LLM_MODEL = 'claude-sonnet-4-6';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================================================
    // DAILY CRON ENTRY POINT
    // ==========================================================================

    /**
     * Cron entry. Scans greetings_template_seed for today's festivals,
     * birthdays, and anniversaries. Generates 3 variants per draft.
     * Writes to greetings_outbox with status='pending_cm_approval'.
     *
     * @return array ['drafts_created', 'skipped_rate_limit', 'errors']
     */
    public function generate_daily_drafts()
    {
        $log = [
            'started_at'         => date('Y-m-d H:i:s'),
            'drafts_created'     => 0,
            'skipped_rate_limit' => 0,
            'errors'             => [],
        ];

        $today     = date('Y-m-d');
        $window_to = date('Y-m-d', strtotime('+' . self::FESTIVAL_PRE_WINDOW_DAYS . ' days'));

        // --- Festivals and occasion-based seeds ---
        $seeds = $this->db->query("
            SELECT DISTINCT occasion_code
              FROM greetings_template_seed
             WHERE active = 1
               AND occasion_date BETWEEN ? AND ?
        ", [$today, $window_to])->result_array();

        foreach ($seeds as $seed) {
            $occasion_code = $seed['occasion_code'];
            try {
                $res = $this->_draft_for_occasion($occasion_code, $log);
                $log['drafts_created'] += (int)($res['created'] ?? 0);
                $log['skipped_rate_limit'] += (int)($res['skipped'] ?? 0);
            } catch (Exception $e) {
                $log['errors'][] = ['occasion' => $occasion_code, 'error' => $e->getMessage()];
                log_message('error', '[greetings_drafter] daily occasion=' . $occasion_code . ' ' . $e->getMessage());
            }
        }

        // --- Stakeholder birthdays (1 day ahead) ---
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $birthdays = $this->db->query("
            SELECT sc.id AS contact_id, sc.contact_name, sc.role_label,
                   sc.bd_uid_owner, sc.school_id
              FROM stakeholder_contact sc
             WHERE DATE_FORMAT(sc.birthday, '%m-%d') = DATE_FORMAT(?, '%m-%d')
               AND sc.birthday IS NOT NULL
        ", [$tomorrow])->result_array();

        foreach ($birthdays as $contact) {
            try {
                $created = $this->_create_draft(
                    'birthday_principal',
                    (int)$contact['contact_id'],
                    (int)$contact['bd_uid_owner'],
                    (int)$contact['school_id'],
                    $log
                );
                if ($created) $log['drafts_created']++;
            } catch (Exception $e) {
                $log['errors'][] = ['birthday_contact' => $contact['contact_id'], 'error' => $e->getMessage()];
            }
        }

        $log['finished_at'] = date('Y-m-d H:i:s');
        log_message('info', '[greetings_drafter] generate_daily_drafts ' . json_encode($log));
        return $log;
    }

    // ==========================================================================
    // WIN BROADCAST
    // ==========================================================================

    /**
     * Triggered when a Won closure over Rs 10 lakh is logged.
     * Drafts 3 variants per stakeholder type.
     *
     * @param  int $transition_id lead_progression_log.id
     * @return array ['ok', 'drafts_created']
     */
    public function generate_win_broadcast($transition_id)
    {
        $transition_id = (int)$transition_id;

        $transition = $this->db->query("
            SELECT lpl.cid_id, lpl.transition_by_uid AS bd_uid,
                   ic.compny_nm AS school_name, ic.fbudget AS deal_value_rs,
                   ic.cluster_id
              FROM lead_progression_log lpl
              INNER JOIN init_call ic ON ic.id = lpl.cid_id
             WHERE lpl.id = ?
               AND lpl.to_cstatus = 12
             LIMIT 1
        ", [$transition_id])->row_array();

        if (empty($transition)) {
            return ['ok' => false, 'error' => 'transition_not_found'];
        }

        $deal_value = (float)$transition['deal_value_rs'];
        if ($deal_value < self::WIN_BROADCAST_MIN_RS) {
            return ['ok' => false, 'error' => 'deal_below_threshold', 'deal_value_rs' => $deal_value];
        }

        // Get all stakeholders for this school.
        $contacts = $this->db->query("
            SELECT sc.id AS contact_id, sc.bd_uid_owner, ic.id AS school_id
              FROM stakeholder_contact sc
              INNER JOIN init_call ic ON ic.id = sc.cid_id
             WHERE sc.cid_id = ?
               AND sc.active = 1
        ", [(int)$transition['cid_id']])->result_array();

        // Also create a cluster broadcast for the CM.
        $cm_uid = $this->_get_cm_for_bd((int)$transition['bd_uid']);
        $cm_school_id = (int)$transition['cid_id'];

        $created = 0;
        $log     = [];
        $this->_create_win_outbox_draft($transition, $cm_uid, $cm_school_id, $log);
        $created++;

        foreach ($contacts as $c) {
            $this->_create_win_outbox_draft($transition, (int)$c['bd_uid_owner'], (int)$c['school_id'], $log);
            $created++;
        }

        return ['ok' => true, 'drafts_created' => $created];
    }

    // ==========================================================================
    // SEASONAL TOUCH
    // ==========================================================================

    /**
     * Quarterly nudge for dormant leads.
     *
     * @param  string $period_label e.g. 'Q1_FY27', 'new_academic_year_2026'
     * @return array  ['ok', 'drafts_created']
     */
    public function generate_seasonal_touch($period_label)
    {
        $period_label = $this->db->escape_str($period_label);

        // Dormant: no tblcallevents touch in last 90 days, cstatus not Won/Lost.
        $dormant = $this->db->query("
            SELECT ic.id AS cid_id, ic.mainbd AS bd_uid,
                   sc.id AS contact_id
              FROM init_call ic
              LEFT JOIN stakeholder_contact sc ON sc.cid_id = ic.id AND sc.active = 1
             WHERE ic.cstatus NOT IN (12, 13)
               AND NOT EXISTS (
                   SELECT 1 FROM tblcallevents t
                    WHERE t.cid_id = ic.id
                      AND t.event_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
               )
             LIMIT 100
        ")->result_array();

        $created = 0;
        foreach ($dormant as $row) {
            if (!$row['contact_id']) continue;
            $result = $this->_create_draft(
                'seasonal_touch_' . $period_label,
                (int)$row['contact_id'],
                (int)$row['bd_uid'],
                (int)$row['cid_id'],
                []
            );
            if ($result) $created++;
        }

        return ['ok' => true, 'drafts_created' => $created, 'period_label' => $period_label];
    }

    // ==========================================================================
    // LOSS RECOVERY
    // ==========================================================================

    /**
     * 90-day re-engagement after Lost cstatus 13.
     *
     * @param  int $lost_lead_id init_call.id
     * @return array ['ok', 'draft_id']
     */
    public function generate_recovery_outreach($lost_lead_id)
    {
        $lost_lead_id = (int)$lost_lead_id;

        $lead = $this->db->query("
            SELECT ic.id, ic.mainbd AS bd_uid, ic.cstatus,
                   lpl.transition_at AS lost_at,
                   sc.id AS contact_id
              FROM init_call ic
              INNER JOIN lead_progression_log lpl ON lpl.cid_id = ic.id
                AND lpl.to_cstatus = 13
              LEFT JOIN stakeholder_contact sc ON sc.cid_id = ic.id
                AND sc.active = 1
             WHERE ic.id = ?
               AND ic.cstatus = 13
               AND DATEDIFF(CURDATE(), DATE(lpl.transition_at)) >= ?
             ORDER BY lpl.transition_at DESC
             LIMIT 1
        ", [$lost_lead_id, self::LOSS_RECOVERY_DAYS])->row_array();

        if (empty($lead)) {
            return ['ok' => false, 'error' => 'lead_not_eligible_for_recovery'];
        }

        if (empty($lead['contact_id'])) {
            return ['ok' => false, 'error' => 'no_stakeholder_contact'];
        }

        $result = $this->_create_draft(
            'loss_recovery',
            (int)$lead['contact_id'],
            (int)$lead['bd_uid'],
            $lost_lead_id,
            []
        );

        if (!$result) {
            return ['ok' => false, 'error' => 'draft_creation_failed_or_rate_limited'];
        }

        return ['ok' => true, 'draft_id' => $result];
    }

    // ==========================================================================
    // APPROVAL WORKFLOW
    // ==========================================================================

    /**
     * CM/BD approves a draft, picks a variant, optionally edits.
     * Sets status = 'approved_ready_to_send'. NEVER sends.
     *
     * @param  int    $outbox_id      greetings_outbox.id
     * @param  int    $cm_uid         Approver user id
     * @param  string $variant_chosen formal_en|warm_en|regional
     * @param  string $edits_json     JSON of {field: new_value} overrides
     * @return array  ['ok', 'outbox_id', 'status']
     */
    public function approve_draft($outbox_id, $cm_uid, $variant_chosen, $edits_json)
    {
        $outbox_id      = (int)$outbox_id;
        $cm_uid         = (int)$cm_uid;
        $allowed_variants = ['formal_en', 'warm_en', 'regional'];
        $variant_chosen = in_array($variant_chosen, $allowed_variants) ? $variant_chosen : 'formal_en';
        $edits_json     = $edits_json ?: '{}';

        $draft = $this->db->query("
            SELECT id, status, bd_uid_owner
              FROM greetings_outbox
             WHERE id = ?
             LIMIT 1
        ", [$outbox_id])->row_array();

        if (empty($draft)) return ['ok' => false, 'error' => 'draft_not_found'];
        if (!in_array($draft['status'], ['draft', 'pending_cm_approval'])) {
            return ['ok' => false, 'error' => 'draft_not_in_pending_state', 'current_status' => $draft['status']];
        }

        $this->db->trans_start();
        $this->db->query("
            UPDATE greetings_outbox
               SET status = 'approved_ready_to_send',
                   approved_by_uid = ?,
                   approved_at = NOW(),
                   variant_chosen = ?,
                   edits_applied_json = ?
             WHERE id = ?
        ", [$cm_uid, $variant_chosen, $edits_json, $outbox_id]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return [
            'ok'       => true,
            'outbox_id'=> $outbox_id,
            'status'   => 'approved_ready_to_send',
            'note'     => 'BD must manually trigger send from the UI',
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Reject a draft with a reason.
     *
     * @param  int    $outbox_id
     * @param  int    $cm_uid
     * @param  string $reason
     * @return array  ['ok']
     */
    public function reject_draft($outbox_id, $cm_uid, $reason)
    {
        $outbox_id = (int)$outbox_id;
        $cm_uid    = (int)$cm_uid;
        $reason    = substr((string)$reason, 0, 500);

        $draft = $this->db->query("
            SELECT id, status FROM greetings_outbox WHERE id = ? LIMIT 1
        ", [$outbox_id])->row_array();

        if (empty($draft)) return ['ok' => false, 'error' => 'draft_not_found'];
        if ($draft['status'] === 'sent') {
            return ['ok' => false, 'error' => 'cannot_reject_sent_draft'];
        }

        $this->db->trans_start();
        $this->db->query("
            UPDATE greetings_outbox
               SET status = 'rejected',
                   approved_by_uid = ?,
                   approved_at = NOW(),
                   reject_reason = ?
             WHERE id = ?
        ", [$cm_uid, $reason, $outbox_id]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return ['ok' => true, 'outbox_id' => $outbox_id, 'status' => 'rejected'];
    }

    // ------------------------------------------------------------------

    /**
     * Return pending drafts for a CM approver from the pre-built view.
     *
     * @param  int $cm_uid
     * @return array
     */
    public function get_queue_for_approver($cm_uid)
    {
        $cm_uid = (int)$cm_uid;
        if (!$cm_uid) return ['ok' => false, 'error' => 'missing_cm_uid'];

        $rows = $this->db->query("
            SELECT v.*
              FROM v_greetings_queue_for_approver v
             WHERE v.cm_uid_approver = ?
             ORDER BY v.proposed_send_at ASC
        ", [$cm_uid])->result_array();

        return ['ok' => true, 'cm_uid' => $cm_uid, 'drafts' => $rows, 'count' => count($rows)];
    }

    // ==========================================================================
    // PRIVATE HELPERS
    // ==========================================================================

    /**
     * Draft messages for an occasion for all eligible stakeholders.
     *
     * @param  string $occasion_code
     * @param  array  &$log
     * @return array  ['created', 'skipped']
     */
    private function _draft_for_occasion($occasion_code, &$log)
    {
        // Find all active stakeholders who have had at least one tblcallevents touch.
        $contacts = $this->db->query("
            SELECT sc.id AS contact_id, sc.bd_uid_owner, sc.cid_id AS school_id,
                   sc.contact_name, sc.role_label, ic.compny_nm AS school_name
              FROM stakeholder_contact sc
              INNER JOIN init_call ic ON ic.id = sc.cid_id
             WHERE sc.active = 1
               AND EXISTS (
                   SELECT 1 FROM tblcallevents t WHERE t.cid_id = sc.cid_id LIMIT 1
               )
        ")->result_array();

        $created = 0;
        $skipped = 0;

        foreach ($contacts as $c) {
            $result = $this->_create_draft(
                $occasion_code,
                (int)$c['contact_id'],
                (int)$c['bd_uid_owner'],
                (int)$c['school_id'],
                $log
            );
            if ($result === null) {
                $skipped++;
            } elseif ($result) {
                $created++;
            }
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    // ------------------------------------------------------------------

    /**
     * Check rate limit, call LLM, write draft to greetings_outbox.
     *
     * @param  string $occasion_code
     * @param  int    $contact_id
     * @param  int    $bd_uid
     * @param  int    $school_id
     * @param  array  $log
     * @return int|null outbox id on success, null if rate-limited, false on error
     */
    private function _create_draft($occasion_code, $contact_id, $bd_uid, $school_id, &$log)
    {
        // Rate limit: max 3 per recipient per calendar quarter.
        $quarter_start = date('Y-m-d', mktime(0, 0, 0, (ceil(date('n') / 3) - 1) * 3 + 1, 1));
        $sent_this_quarter = (int)$this->db->query("
            SELECT COUNT(*) AS cnt FROM greetings_outbox
             WHERE recipient_contact_id = ?
               AND created_at >= ?
               AND status NOT IN ('rejected', 'expired')
        ", [$contact_id, $quarter_start])->row('cnt');

        if ($sent_this_quarter >= self::MAX_MSGS_PER_QUARTER) {
            return null; // Rate limited
        }

        // Fetch contact and school details for the prompt.
        $contact = $this->db->query("
            SELECT sc.contact_name, sc.role_label, sc.preferred_language,
                   ic.compny_nm AS school_name, ic.city, u.firstName AS bd_name
              FROM stakeholder_contact sc
              INNER JOIN init_call ic ON ic.id = sc.cid_id
              INNER JOIN user u ON u.uid = sc.bd_uid_owner
             WHERE sc.id = ?
             LIMIT 1
        ", [$contact_id])->row_array();

        if (empty($contact)) return false;

        // Get the CM approver for this BD.
        $cm_uid = $this->_get_cm_for_bd($bd_uid);

        // Proposed send time: 09:00 IST.
        $proposed_send_at = date('Y-m-d') . ' 09:00:00';

        // Build LLM prompt for 3 variants.
        $prompt = $this->_build_draft_prompt($occasion_code, $contact, $contact['preferred_language'] ?? 'en');

        $llm_result = $this->llm->call(self::LLM_MODEL, $prompt, [
            'max_tokens'  => 800,
            'temperature' => 0.7,
        ]);

        $variants = $this->_parse_llm_variants($llm_result);

        $this->db->trans_start();
        $this->db->query("
            INSERT INTO greetings_outbox
                (occasion_code, recipient_contact_id, bd_uid_owner, cm_uid_approver,
                 school_id, draft_formal_en, draft_warm_en, draft_regional,
                 draft_regional_lang, proposed_channel, proposed_send_at,
                 status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'whatsapp', ?, 'pending_cm_approval', NOW())
        ", [
            $occasion_code, $contact_id, $bd_uid, $cm_uid, $school_id,
            $variants['formal_en'] ?? '',
            $variants['warm_en']   ?? '',
            $variants['regional']  ?? '',
            $contact['preferred_language'] ?? 'en',
            $proposed_send_at,
        ]);
        $outbox_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) return false;

        return $outbox_id;
    }

    // ------------------------------------------------------------------

    /**
     * Build a greetings LLM prompt.
     *
     * @param  string $occasion_code
     * @param  array  $contact
     * @param  string $lang
     * @return string
     */
    private function _build_draft_prompt($occasion_code, $contact, $lang)
    {
        $name       = $contact['contact_name'] ?? 'the Principal';
        $role       = $contact['role_label']   ?? 'Principal';
        $school     = $contact['school_name']  ?? 'the school';
        $bd_name    = $contact['bd_name']      ?? 'STEM Team';
        $occasion   = str_replace('_', ' ', $occasion_code);
        $regional   = ($lang && $lang !== 'en') ? "the regional variant in {$lang}" : 'Hindi (fallback)';

        return <<<PROMPT
You are a warm, professional relationship manager at STEM Learning EdTech.
Write exactly 3 greeting message variants for this occasion:

Occasion: {$occasion}
Recipient: {$name} ({$role}) at {$school}
Sender: {$bd_name} from STEM Learning

Return JSON with exactly these three keys:
- "formal_en": Professional English (2-3 sentences, no slang, suitable for email)
- "warm_en": Friendly English (conversational, warm, 2-3 sentences, suitable for WhatsApp)
- "regional": {$regional} (2-3 sentences, culturally appropriate)

Rules:
- No auto-sending phrases (do not say "click to send" or "we will send this automatically")
- No em-dashes
- No pricing or product mentions
- Keep each variant under 200 characters for WhatsApp compatibility
- Address recipient by name
- Sign off as {$bd_name}, STEM Learning

Return only the JSON object, no extra text.
PROMPT;
    }

    // ------------------------------------------------------------------

    /**
     * Parse LLM response into variant array.
     * Falls back to placeholder text if parsing fails.
     *
     * @param  mixed $llm_result
     * @return array ['formal_en', 'warm_en', 'regional']
     */
    private function _parse_llm_variants($llm_result)
    {
        $text = is_string($llm_result) ? $llm_result : (string)($llm_result['content'] ?? '');
        // Strip markdown code fences.
        $text = preg_replace('/```json\s*|\s*```/', '', $text);
        $decoded = json_decode($text, true);

        if (is_array($decoded) &&
            isset($decoded['formal_en'], $decoded['warm_en'], $decoded['regional'])) {
            return $decoded;
        }

        return [
            'formal_en' => '[Formal greeting draft - LLM parse error]',
            'warm_en'   => '[Warm greeting draft - LLM parse error]',
            'regional'  => '[Regional greeting draft - LLM parse error]',
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Create a win-broadcast outbox entry.
     *
     * @param  array $transition
     * @param  int   $cm_uid
     * @param  int   $school_id
     * @param  array &$log
     * @return void
     */
    private function _create_win_outbox_draft($transition, $cm_uid, $school_id, &$log)
    {
        $deal_rs    = number_format((float)$transition['deal_value_rs'], 0);
        $school_name = $this->db->escape_str($transition['school_name'] ?? 'school');

        $prompt = <<<PROMPT
You are writing a congratulatory win broadcast for a B2B EdTech company.

Event: STEM Learning has won a deal with {$school_name} worth Rs {$deal_rs}.
Audience: Internal cluster team (BDs and CMs).

Return JSON with three keys:
- "formal_en": Professional congratulatory message (under 300 chars)
- "warm_en": Warm celebratory team message (under 300 chars)
- "regional": Hindi celebratory message (under 300 chars)

No auto-send language. No em-dashes. Return only the JSON object.
PROMPT;

        $llm_result = $this->llm->call(self::LLM_MODEL, $prompt, ['max_tokens' => 500]);
        $variants   = $this->_parse_llm_variants($llm_result);

        $this->db->trans_start();
        $this->db->query("
            INSERT INTO greetings_outbox
                (occasion_code, recipient_contact_id, bd_uid_owner, cm_uid_approver,
                 school_id, draft_formal_en, draft_warm_en, draft_regional,
                 draft_regional_lang, proposed_channel, proposed_send_at,
                 status, created_at)
            VALUES ('win_broadcast', 0, ?, ?, ?, ?, ?, ?, 'hi', 'whatsapp', NOW(), 'pending_cm_approval', NOW())
        ", [
            (int)$transition['bd_uid'] ?? 0,
            $cm_uid,
            $school_id,
            $variants['formal_en'],
            $variants['warm_en'],
            $variants['regional'],
        ]);
        $this->db->trans_complete();
    }

    // ------------------------------------------------------------------

    /**
     * Get the CM uid for a given BD.
     *
     * @param  int $bd_uid
     * @return int cm_uid or 0
     */
    private function _get_cm_for_bd($bd_uid)
    {
        $row = $this->db->query("
            SELECT parent_uid FROM reporting_hierarchy
             WHERE employee_uid = ? AND active = 1 LIMIT 1
        ", [(int)$bd_uid])->row_array();
        return $row ? (int)$row['parent_uid'] : 0;
    }
}
