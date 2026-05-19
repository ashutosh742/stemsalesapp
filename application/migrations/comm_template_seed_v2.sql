-- ============================================================================
-- STEM CRM - Migration 027 - Comm Template Seed v2
-- ============================================================================
-- Seeds comm_template_v2 with 15 templates total:
--   * 8 templates inherit from migration 026 email_template (FK inherits_from)
--   * 7 new templates added directly here (call follow-ups, proposal, stage,
--     dormant re-engagement)
--
-- All templates: plain English. No em-dashes. No non-ASCII. Rs for rupees.
-- Variable syntax: {{var_name}} resolved at draft time by comm_drafter_agent.
-- Required variables listed in required_context_fields JSON column.
--
-- Author: STEM Learning ops
-- Date: 17 May 2026
-- Production phase 1 target: Mon 1 Aug 2026
-- ============================================================================

-- ----------------------------------------------------------------------------
-- BLOCK A: Inherit 8 templates from migration 026 email_template
-- ----------------------------------------------------------------------------
-- These point back to migration 026 rows via inherits_from. The orchestrator
-- copies the 026 body at draft time and applies 027 frequency cap + dedup
-- on top. If the 026 template is updated, 027 picks up the change next run.

INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES
-- Template 1: Thank-you after first meeting (inherits from 026 thank_you_email)
('comm_v2_thank_you_first_meeting',
 'Thank-you after first meeting',
 'meeting_completed', 'email', 'primary_dm',
 '["secondary_dm"]',
 NULL, NULL, NULL,
 'Inherits from migration 026. Apply 027 frequency cap.',
 '["bd_name","stakeholder_first_name","school_name","meeting_date","mom_action_items"]',
 1, 6, 120,
 (SELECT id FROM email_template WHERE template_code='thank_you_email_v1' LIMIT 1),
 1, NOW()),

-- Template 2: Proposal cover (inherits from 026 proposal_cover)
('comm_v2_proposal_cover',
 'Proposal cover note',
 'proposal_sent', 'email', 'primary_dm',
 '["cfo_bursar","principal"]',
 NULL, NULL, NULL,
 'Inherits from migration 026. Apply 027 frequency cap and dedup.',
 '["bd_name","stakeholder_first_name","school_name","proposal_amount_rs","proposal_summary"]',
 6, 7, 150,
 (SELECT id FROM email_template WHERE template_code='proposal_cover_v1' LIMIT 1),
 1, NOW()),

-- Template 3: Proposal nudge 72h (inherits from 026 proposal_nudge_72h)
('comm_v2_proposal_nudge_72h',
 'Proposal nudge 72 hours',
 'proposal_sent', 'email', 'primary_dm',
 NULL,
 NULL, NULL, NULL,
 'Inherits from migration 026. Soft follow-up, no pressure tone.',
 '["bd_name","stakeholder_first_name","school_name","proposal_sent_date","days_elapsed"]',
 7, 7, 100,
 (SELECT id FROM email_template WHERE template_code='proposal_nudge_72h_v1' LIMIT 1),
 1, NOW()),

-- Template 4: Query raised acknowledgement (inherits from 026 query_raised_ack)
('comm_v2_query_raised_ack',
 'Query raised acknowledgement',
 'query_raised', 'email', 'primary_dm',
 NULL,
 NULL, NULL, NULL,
 'Inherits from migration 026. Acknowledge query within 4 hours.',
 '["bd_name","stakeholder_first_name","query_text","expected_resolution_date"]',
 1, 12, 80,
 (SELECT id FROM email_template WHERE template_code='query_raised_ack_v1' LIMIT 1),
 1, NOW()),

-- Template 5: Query resolved (inherits from 026 query_resolved)
('comm_v2_query_resolved',
 'Query resolved confirmation',
 'query_resolved', 'email', 'primary_dm',
 '["secondary_dm"]',
 NULL, NULL, NULL,
 'Inherits from migration 026. Close-out tone with action recap.',
 '["bd_name","stakeholder_first_name","query_text","resolution_summary"]',
 1, 12, 100,
 (SELECT id FROM email_template WHERE template_code='query_resolved_v1' LIMIT 1),
 1, NOW()),

-- Template 6: CM nurture call summary (inherits from 026 cm_call_summary)
('comm_v2_cm_call_summary',
 'CM nurture call summary',
 'meeting_completed', 'email', 'primary_dm',
 NULL,
 NULL, NULL, NULL,
 'Inherits from migration 026. CM-led call summary.',
 '["cm_name","stakeholder_first_name","school_name","call_summary","next_step"]',
 3, 9, 120,
 (SELECT id FROM email_template WHERE template_code='cm_call_summary_v1' LIMIT 1),
 1, NOW()),

-- Template 7: Lead query checklist sent (inherits from 026 lead_query_checklist_sent)
('comm_v2_lead_query_checklist_sent',
 'Lead query checklist sent',
 'query_raised', 'email', 'primary_dm',
 NULL,
 NULL, NULL, NULL,
 'Inherits from migration 026. Sends checklist of pending items.',
 '["bd_name","stakeholder_first_name","checklist_items"]',
 1, 12, 120,
 (SELECT id FROM email_template WHERE template_code='lead_query_checklist_sent_v1' LIMIT 1),
 1, NOW()),

-- Template 8: Proposal SLA breach internal (inherits from 026 proposal_sla_breach_internal)
('comm_v2_proposal_sla_breach_internal',
 'Proposal SLA breach internal alert',
 'proposal_sent', 'email', 'primary_dm',
 NULL,
 NULL, NULL, NULL,
 'Inherits from migration 026. Internal CM/RM escalation only, not client-facing.',
 '["bd_name","cm_name","school_name","proposal_age_hours"]',
 6, 8, 80,
 (SELECT id FROM email_template WHERE template_code='proposal_sla_breach_internal_v1' LIMIT 1),
 1, NOW());


-- ----------------------------------------------------------------------------
-- BLOCK B: 7 new templates introduced by migration 027
-- ----------------------------------------------------------------------------

-- Template 9: Call follow-up - no answer
INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES (
'call_followup_no_answer',
'Call follow-up no answer',
'call_unanswered', 'email', 'primary_dm',
NULL,
'Tried reaching you - {{call_purpose}}',
'Dear {{stakeholder_first_name}},

I tried calling you today at {{call_time}} regarding {{call_purpose}} for {{school_name}}. I was not able to reach you.

The reason for my call was to discuss {{call_topic_one_line}}.

When would be a good time for a 10-minute call later this week? I am available on {{suggested_slot_one}} or {{suggested_slot_two}}.

You can also reply to this email with your preferred slot.

Best regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',

'<p>Dear {{stakeholder_first_name}},</p>
<p>I tried calling you today at {{call_time}} regarding <strong>{{call_purpose}}</strong> for {{school_name}}. I was not able to reach you.</p>
<p>The reason for my call was to discuss {{call_topic_one_line}}.</p>
<p>When would be a good time for a 10-minute call later this week? I am available on:</p>
<ul>
<li>{{suggested_slot_one}}</li>
<li>{{suggested_slot_two}}</li>
</ul>
<p>You can also reply to this email with your preferred slot.</p>
<p>Best regards,<br>
{{bd_name}}<br>
STEM Learning<br>
{{bd_phone}}</p>',

'You are a polite BD assistant at STEM Learning. Tone: warm, brief, never pushy. The recipient missed a call. Acknowledge the miss without blame, restate the purpose in one short line, offer two clear slots in the next 3 working days. Plain English. Max 80 words for plain body. Use Rs for rupees if money mentioned. Never em-dash. Never non-ASCII.',
'["bd_name","bd_phone","stakeholder_first_name","school_name","call_time","call_purpose","call_topic_one_line","suggested_slot_one","suggested_slot_two"]',
1, 12, 80, NULL, 1, NOW());


-- Template 10: Call follow-up - dropped (call answered but cut short, no MoM in 1h)
INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES (
'call_followup_dropped',
'Call follow-up dropped',
'call_dropped', 'email', 'primary_dm',
NULL,
'Continuing our call from earlier today',
'Dear {{stakeholder_first_name}},

Thanks for taking my call earlier today. The line dropped before we could finish discussing {{call_topic_one_line}}.

To recap what we covered:
{{call_recap_two_lines}}

The point we did not get to was {{open_point}}.

Could we schedule 15 minutes later this week to close the loop? I am available on {{suggested_slot_one}} or {{suggested_slot_two}}.

Best regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',

'<p>Dear {{stakeholder_first_name}},</p>
<p>Thanks for taking my call earlier today. The line dropped before we could finish discussing <strong>{{call_topic_one_line}}</strong>.</p>
<p><em>Recap of what we covered:</em><br>
{{call_recap_two_lines}}</p>
<p><em>Open point we did not get to:</em><br>
{{open_point}}</p>
<p>Could we schedule 15 minutes later this week to close the loop? I am available on:</p>
<ul>
<li>{{suggested_slot_one}}</li>
<li>{{suggested_slot_two}}</li>
</ul>
<p>Best regards,<br>
{{bd_name}}<br>
STEM Learning<br>
{{bd_phone}}</p>',

'You are a polite BD assistant at STEM Learning. The recipient was on a call that dropped (30 to 120 seconds, no MoM logged in 1 hour). Acknowledge briefly, recap the two points covered, name the one open point, offer two slots. Plain English. Max 100 words for plain body. Use Rs for rupees if money mentioned. Never em-dash. Never non-ASCII.',
'["bd_name","bd_phone","stakeholder_first_name","call_topic_one_line","call_recap_two_lines","open_point","suggested_slot_one","suggested_slot_two"]',
1, 12, 100, NULL, 1, NOW());


-- Template 11: Proposal send cover (027 native, different from 026 inherit)
-- This fires when proposal_sla_tracker moves to 'closed' (proposal actually sent)
-- and orchestrator wants to ensure stakeholder got a cover note even if BD forgot.
INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES (
'proposal_send_cover',
'Proposal cover note (auto-draft fallback)',
'proposal_sent', 'email', 'primary_dm',
'["cfo_bursar","principal"]',
'Proposal for {{school_name}} - STEM Learning',
'Dear {{stakeholder_first_name}},

Please find attached our proposal for {{school_name}}.

Key points:
- Programme scope: {{programme_scope}}
- Investment: Rs {{proposal_amount_rs}}
- Timeline: {{programme_timeline}}

I will call you on {{followup_date}} to walk through any questions. If you would like to schedule a call sooner, please reply with a slot.

Thanks for considering STEM Learning.

Best regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',

'<p>Dear {{stakeholder_first_name}},</p>
<p>Please find attached our proposal for <strong>{{school_name}}</strong>.</p>
<p><strong>Key points:</strong></p>
<ul>
<li>Programme scope: {{programme_scope}}</li>
<li>Investment: Rs {{proposal_amount_rs}}</li>
<li>Timeline: {{programme_timeline}}</li>
</ul>
<p>I will call you on {{followup_date}} to walk through any questions. If you would like to schedule a call sooner, please reply with a slot.</p>
<p>Thanks for considering STEM Learning.</p>
<p>Best regards,<br>
{{bd_name}}<br>
STEM Learning<br>
{{bd_phone}}</p>',

'You are a polite BD assistant at STEM Learning. A proposal is being sent. Cover note must be brief, factual, restate three key points, name the follow-up date. Plain English. Max 120 words for plain body. Use Rs for rupees. Never em-dash. Never non-ASCII.',
'["bd_name","bd_phone","stakeholder_first_name","school_name","programme_scope","proposal_amount_rs","programme_timeline","followup_date"]',
6, 8, 120, NULL, 1, NOW());


-- Template 12: Proposal nudge 72h (027 native, different from 026 inherit)
-- Fires when proposal sent and no client response after 72 hours.
INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES (
'proposal_nudge_72h',
'Proposal soft nudge after 72 hours',
'proposal_sent', 'email', 'primary_dm',
NULL,
'Quick check-in on the STEM Learning proposal',
'Dear {{stakeholder_first_name}},

Just a quick check-in on the proposal I shared on {{proposal_sent_date}} for {{school_name}}.

Happy to clarify any point or set up a 15-minute walkthrough call. I am available on {{suggested_slot_one}} or {{suggested_slot_two}}.

If you need more time, please let me know a date that works.

Best regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',

'<p>Dear {{stakeholder_first_name}},</p>
<p>Just a quick check-in on the proposal I shared on <strong>{{proposal_sent_date}}</strong> for {{school_name}}.</p>
<p>Happy to clarify any point or set up a 15-minute walkthrough call. I am available on:</p>
<ul>
<li>{{suggested_slot_one}}</li>
<li>{{suggested_slot_two}}</li>
</ul>
<p>If you need more time, please let me know a date that works.</p>
<p>Best regards,<br>
{{bd_name}}<br>
STEM Learning<br>
{{bd_phone}}</p>',

'You are a polite BD assistant at STEM Learning. Soft 72-hour nudge after a proposal. Tone: helpful, never pressuring. Max 70 words for plain body. Plain English. Never em-dash. Never non-ASCII.',
'["bd_name","bd_phone","stakeholder_first_name","school_name","proposal_sent_date","suggested_slot_one","suggested_slot_two"]',
6, 8, 70, NULL, 1, NOW());


-- Template 13: Stage progress - Won
INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES (
'stage_progress_won',
'Stage progress - Won celebration',
'stage_progressed', 'email', 'primary_dm',
'["principal","cfo_bursar","trustee"]',
'Welcome to STEM Learning - {{school_name}}',
'Dear {{stakeholder_first_name}},

Thank you for choosing STEM Learning for {{school_name}}. We are excited to begin this journey with you.

What happens next:
1. Our delivery team will reach out within 3 working days to schedule the kick-off call.
2. You will receive the welcome pack with curriculum details and lab setup timeline.
3. Your single point of contact for the programme will be {{cm_name}}.

If you have any immediate questions, please call me on {{bd_phone}} or write to {{bd_email}}.

Looking forward to a long partnership.

Best regards,
{{bd_name}}
STEM Learning',

'<p>Dear {{stakeholder_first_name}},</p>
<p>Thank you for choosing STEM Learning for <strong>{{school_name}}</strong>. We are excited to begin this journey with you.</p>
<p><strong>What happens next:</strong></p>
<ol>
<li>Our delivery team will reach out within 3 working days to schedule the kick-off call.</li>
<li>You will receive the welcome pack with curriculum details and lab setup timeline.</li>
<li>Your single point of contact for the programme will be <strong>{{cm_name}}</strong>.</li>
</ol>
<p>If you have any immediate questions, please call me on {{bd_phone}} or write to {{bd_email}}.</p>
<p>Looking forward to a long partnership.</p>
<p>Best regards,<br>
{{bd_name}}<br>
STEM Learning</p>',

'You are a polite BD assistant at STEM Learning. The school has just signed (cstatus 9 to 12, Won). Tone: warm, celebratory but professional, set clear next-step expectations. CC principal and trustee for visibility. Max 120 words for plain body. Plain English. Never em-dash. Never non-ASCII.',
'["bd_name","bd_phone","bd_email","cm_name","stakeholder_first_name","school_name"]',
9, 12, 120, NULL, 1, NOW());


-- Template 14: Dormant re-engage 14 days
INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES (
'dormant_re_engage_14d',
'Dormant re-engagement 14 days',
'dormant_re_engage', 'email', 'primary_dm',
NULL,
'Following up on STEM Learning at {{school_name}}',
'Dear {{stakeholder_first_name}},

It has been a couple of weeks since we last spoke about {{last_meeting_topic}} for {{school_name}}.

I wanted to check in on where things stand and if there is anything I can help clarify.

A few schools we are working with recently shared {{social_proof_one_line}}, which might be useful for your context too.

Happy to set up a short call. I am available on {{suggested_slot_one}} or {{suggested_slot_two}}.

Best regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',

'<p>Dear {{stakeholder_first_name}},</p>
<p>It has been a couple of weeks since we last spoke about <strong>{{last_meeting_topic}}</strong> for {{school_name}}.</p>
<p>I wanted to check in on where things stand and if there is anything I can help clarify.</p>
<p>A few schools we are working with recently shared {{social_proof_one_line}}, which might be useful for your context too.</p>
<p>Happy to set up a short call. I am available on:</p>
<ul>
<li>{{suggested_slot_one}}</li>
<li>{{suggested_slot_two}}</li>
</ul>
<p>Best regards,<br>
{{bd_name}}<br>
STEM Learning<br>
{{bd_phone}}</p>',

'You are a polite BD assistant at STEM Learning. The lead has gone quiet for 14 days at cstatus 6 or above. Tone: low-pressure, helpful, lead with value (social proof). Max 100 words for plain body. Plain English. Never em-dash. Never non-ASCII.',
'["bd_name","bd_phone","stakeholder_first_name","school_name","last_meeting_topic","social_proof_one_line","suggested_slot_one","suggested_slot_two"]',
6, 9, 100, NULL, 1, NOW());


-- Template 15: Dormant re-engage 30 days
INSERT INTO comm_template_v2 (
    template_code, template_name, event_type, channel, recipient_to_role,
    recipient_cc_roles, subject_template, body_plain_template, body_html_template,
    ai_persona_instructions, required_context_fields, applicable_cstatus_min,
    applicable_cstatus_max, max_words, inherits_from, active, created_at
) VALUES (
'dormant_re_engage_30d',
'Dormant re-engagement 30 days',
'dormant_re_engage', 'email', 'primary_dm',
'["principal"]',
'Closing the loop on STEM Learning at {{school_name}}',
'Dear {{stakeholder_first_name}},

It has been about a month since we last connected on {{last_meeting_topic}} for {{school_name}}.

I want to respect your time. If now is not the right window, please let me know and I will pause and check back next quarter.

If there is any block I can help unlock (budget approval, board buy-in, curriculum question), I am one call away on {{bd_phone}}.

Either way, thanks for considering STEM Learning.

Best regards,
{{bd_name}}
STEM Learning',

'<p>Dear {{stakeholder_first_name}},</p>
<p>It has been about a month since we last connected on <strong>{{last_meeting_topic}}</strong> for {{school_name}}.</p>
<p>I want to respect your time. If now is not the right window, please let me know and I will pause and check back next quarter.</p>
<p>If there is any block I can help unlock (budget approval, board buy-in, curriculum question), I am one call away on {{bd_phone}}.</p>
<p>Either way, thanks for considering STEM Learning.</p>
<p>Best regards,<br>
{{bd_name}}<br>
STEM Learning</p>',

'You are a polite BD assistant at STEM Learning. The lead has been dormant 30 days. Tone: graceful exit ramp, not pushy, give them an easy out. CC principal so the decision-tree expands. Max 100 words for plain body. Plain English. Never em-dash. Never non-ASCII.',
'["bd_name","bd_phone","stakeholder_first_name","school_name","last_meeting_topic"]',
6, 9, 100, NULL, 1, NOW());


-- ----------------------------------------------------------------------------
-- BLOCK C: Verification queries (run after seed)
-- ----------------------------------------------------------------------------

-- Count templates by event type
-- SELECT event_type, COUNT(*) AS n FROM comm_template_v2 WHERE active=1 GROUP BY event_type;
-- Expected:
--   call_unanswered     1
--   call_dropped        1
--   meeting_completed   2  (thank_you_first_meeting, cm_call_summary)
--   proposal_sent       5  (cover x2, nudge x2, sla_breach_internal)
--   query_raised        2
--   query_resolved      1
--   stage_progressed    1
--   dormant_re_engage   2
-- Total = 15

-- Check FK integrity for inherited rows
-- SELECT t.template_code, e.template_code AS parent_026
-- FROM comm_template_v2 t LEFT JOIN email_template e ON t.inherits_from = e.id
-- WHERE t.inherits_from IS NOT NULL;
-- All 8 inherited rows must have a non-null parent_026.

-- ============================================================================
-- END migration 027 template seed v2
-- ============================================================================
