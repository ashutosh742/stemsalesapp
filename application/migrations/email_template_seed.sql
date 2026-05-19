-- =====================================================================
-- Migration 026: Email Template Seed
-- =====================================================================
-- 8 templates: 4 thank-you variants (by cstatus) + 4 query follow-up
-- variants. AI persona instructions tuned for STEM Learning founder
-- tone: respectful, concise, plain English, no flourish.
-- =====================================================================

START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. TENTATIVE THANKS (post first meeting, cstatus moved to 3)
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'tentative_thanks',
  'Thanks after first meeting (Tentative)',
  'post_meeting', 3, 3,
  'Thank you for meeting us today, {{school_name}}',
  '<p>Dear {{principal_name}},</p>
<p>Thank you for your time today. It was good to understand {{school_name}} and the direction you are taking on STEM education.</p>
<p>{{meeting_recap_one_line}}</p>
<p>{{next_step_one_line}}</p>
<p>Please reach out if any clarification is needed. I will follow up on the points we discussed.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

Thank you for your time today. It was good to understand {{school_name}} and the direction you are taking on STEM education.

{{meeting_recap_one_line}}

{{next_step_one_line}}

Please reach out if any clarification is needed. I will follow up on the points we discussed.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'Tone: respectful, professional, concise. No marketing fluff. Write as a junior person who genuinely listened. Use the meeting MoM to fill the recap line in plain English. Avoid the words excited, delighted, thrilled. Use single sentence for recap and next step.',
  'principal_name,school_name,meeting_recap_one_line,next_step_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

-- ---------------------------------------------------------------------
-- 2. POSITIVE THANKS (cstatus moved to 6)
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'positive_thanks',
  'Thanks after Positive conversion (cstatus 6)',
  'post_meeting', 6, 6,
  'Thank you for the positive discussion, {{school_name}}',
  '<p>Dear {{principal_name}},</p>
<p>Thank you for the time today and for your interest in taking the STEM lab forward at {{school_name}}.</p>
<p>{{meeting_recap_one_line}}</p>
<p>As discussed, I will share the proposal within 48 hours covering {{proposal_scope_one_line}}. {{queries_pending_one_line}}</p>
<p>Looking forward to the next conversation.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

Thank you for the time today and for your interest in taking the STEM lab forward at {{school_name}}.

{{meeting_recap_one_line}}

As discussed, I will share the proposal within 48 hours covering {{proposal_scope_one_line}}. {{queries_pending_one_line}}

Looking forward to the next conversation.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'This is the lead just turned Positive. The 48-hour proposal promise is explicit and non-negotiable per founder rule. If the BD has not entered proposal_scope, prompt them in review. If queries are pending, name them. Tone: confident, not pushy. Avoid the word congratulations.',
  'principal_name,school_name,meeting_recap_one_line,proposal_scope_one_line,queries_pending_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

-- ---------------------------------------------------------------------
-- 3. RP THANKS (cstatus 8 or 9, very positive RP meeting closed)
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'rp_thanks',
  'Thanks after RP meeting (cstatus 8 or 9)',
  'post_meeting', 8, 9,
  'Thank you for meeting us today, {{school_name}}',
  '<p>Dear {{principal_name}},</p>
<p>Thank you for the detailed conversation today on the STEM lab plan for {{school_name}}. It was useful to hear your view on {{rp_decision_areas}}.</p>
<p>{{meeting_recap_one_line}}</p>
<p>Next step: {{next_step_with_owner_and_date}}. {{cm_mention_one_line}}</p>
<p>Please let us know if anything else comes up between now and then.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

Thank you for the detailed conversation today on the STEM lab plan for {{school_name}}. It was useful to hear your view on {{rp_decision_areas}}.

{{meeting_recap_one_line}}

Next step: {{next_step_with_owner_and_date}}. {{cm_mention_one_line}}

Please let us know if anything else comes up between now and then.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'RP meeting means a decision maker was in the room. Email is more formal. Always name the next step owner and date. If CM attended, include "{{cm_name}} from our team will coordinate" line. Tone: senior, decisive, not pushy.',
  'principal_name,school_name,rp_decision_areas,meeting_recap_one_line,next_step_with_owner_and_date,cm_mention_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

-- ---------------------------------------------------------------------
-- 4. WON HANDOVER (cstatus 12)
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'won_handover',
  'Welcome and handover after Won (cstatus 12)',
  'post_meeting', 12, 12,
  'Welcome to STEM Learning, {{school_name}}',
  '<p>Dear {{principal_name}},</p>
<p>Thank you for choosing STEM Learning for {{school_name}}. We are committed to making this lab a strong impact at your school.</p>
<p>Handover details:</p>
<ul>
<li>Account manager: {{anchor_name}} ({{anchor_phone}}, {{anchor_email}})</li>
<li>Implementation kickoff: {{kickoff_date}}</li>
<li>Project timeline: {{project_timeline_one_line}}</li>
</ul>
<p>{{anchor_name}} will reach out within 2 working days with the kickoff plan. For any urgent need before then, please write back to this thread.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

Thank you for choosing STEM Learning for {{school_name}}. We are committed to making this lab a strong impact at your school.

Handover details:
- Account manager: {{anchor_name}} ({{anchor_phone}}, {{anchor_email}})
- Implementation kickoff: {{kickoff_date}}
- Project timeline: {{project_timeline_one_line}}

{{anchor_name}} will reach out within 2 working days with the kickoff plan. For any urgent need before then, please write back to this thread.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'This is the formal handover to anchor account manager. CC the anchor on send. Tone: warm but business-like. No celebratory language. Anchor contact details are mandatory.',
  'principal_name,school_name,anchor_name,anchor_phone,anchor_email,kickoff_date,project_timeline_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

-- ---------------------------------------------------------------------
-- 5. QUERY FOLLOWUP VISIT
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'query_followup_visit',
  'Follow up on school visit request',
  'query_raised', NULL, NULL,
  'School visit slot for {{school_name}}',
  '<p>Dear {{principal_name}},</p>
<p>Following our last discussion, we would like to firm up a school visit slot for {{school_name}}.</p>
<p>Proposed dates: {{visit_proposed_dates}}.</p>
<p>{{visit_team_one_line}}</p>
<p>Please confirm the date that works and any documents we should carry. We will plan the visit around your availability.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

Following our last discussion, we would like to firm up a school visit slot for {{school_name}}.

Proposed dates: {{visit_proposed_dates}}.

{{visit_team_one_line}}

Please confirm the date that works and any documents we should carry. We will plan the visit around your availability.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'Use when query_type=school_visit_request. Propose two or three dates max. Tone: helpful, accommodating.',
  'principal_name,school_name,visit_proposed_dates,visit_team_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

-- ---------------------------------------------------------------------
-- 6. QUERY FOLLOWUP DOCUMENTS
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'query_followup_documents',
  'Documents and clarifications for {{school_name}}',
  'query_raised', NULL, NULL,
  'Requested documents for {{school_name}}',
  '<p>Dear {{principal_name}},</p>
<p>As discussed, please find the items you requested:</p>
<ul>
{{document_list_bullets}}
</ul>
<p>{{additional_context_one_line}}</p>
<p>Let me know if you need anything else. Happy to share more detail on any of these.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

As discussed, please find the items you requested:

{{document_list_plain}}

{{additional_context_one_line}}

Let me know if you need anything else. Happy to share more detail on any of these.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'Use when query_type=documentation_check. Document list must be itemised. If attachments are too large for Gmail (25 MB cap), link to Drive instead and note this. Tone: helpful, complete.',
  'principal_name,school_name,document_list_bullets,document_list_plain,additional_context_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

-- ---------------------------------------------------------------------
-- 7. QUERY FOLLOWUP BUDGET
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'query_followup_budget',
  'Budget clarification for {{school_name}}',
  'query_raised', NULL, NULL,
  'Indicative pricing for {{school_name}} STEM lab',
  '<p>Dear {{principal_name}},</p>
<p>As requested, here is an indicative cost band for the STEM lab at {{school_name}}:</p>
<p><b>{{indicative_range_rs}}</b></p>
<p>Inclusions: {{inclusions_one_line}}<br>
Exclusions: {{exclusions_one_line}}</p>
<p>The final figure will depend on the room dimensions and the number of students. I will send the formal proposal within 48 hours with the breakdown.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

As requested, here is an indicative cost band for the STEM lab at {{school_name}}:

{{indicative_range_rs}}

Inclusions: {{inclusions_one_line}}
Exclusions: {{exclusions_one_line}}

The final figure will depend on the room dimensions and the number of students. I will send the formal proposal within 48 hours with the breakdown.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'Use when query_type=budget_clarification. Always state a range, never a single point figure (room for negotiation). Always commit to 48-hour formal proposal. Tone: honest, transparent.',
  'principal_name,school_name,indicative_range_rs,inclusions_one_line,exclusions_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

-- ---------------------------------------------------------------------
-- 8. QUERY FOLLOWUP GENERIC
-- Fallback for any other query type (curriculum_alignment, site_readiness,
-- principal_availability, tender_doc, csr_approval, product_demo, other)
-- ---------------------------------------------------------------------
INSERT INTO email_template (
  code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max,
  subject_template, body_html_template, body_plain_template,
  ai_persona_instructions, variables_required, active
) VALUES (
  'query_followup_generic',
  'Follow up: {{query_topic_short}} at {{school_name}}',
  'query_raised', NULL, NULL,
  'Follow up: {{query_topic_short}} at {{school_name}}',
  '<p>Dear {{principal_name}},</p>
<p>Following up on your query about {{query_topic_full}}.</p>
<p>{{response_body}}</p>
<p>{{next_step_one_line}}</p>
<p>Please let me know if more detail is helpful.</p>
<p>Warm regards,<br>{{bd_name}}<br>STEM Learning<br>{{bd_phone}}</p>',
  'Dear {{principal_name}},

Following up on your query about {{query_topic_full}}.

{{response_body}}

{{next_step_one_line}}

Please let me know if more detail is helpful.

Warm regards,
{{bd_name}}
STEM Learning
{{bd_phone}}',
  'Generic fallback. AI drafts the response_body based on lead context and query_text from lead_query_checklist row. Subject mentions the topic for searchability. Tone: solution-oriented.',
  'principal_name,school_name,query_topic_short,query_topic_full,response_body,next_step_one_line,bd_name,bd_phone',
  1
) ON DUPLICATE KEY UPDATE
  subject_template = VALUES(subject_template),
  body_html_template = VALUES(body_html_template),
  body_plain_template = VALUES(body_plain_template),
  ai_persona_instructions = VALUES(ai_persona_instructions),
  updated_at = NOW();

COMMIT;

-- =====================================================================
-- VERIFICATION
-- =====================================================================
-- SELECT code, title, trigger_event, meeting_cstatus_min, meeting_cstatus_max, active 
-- FROM email_template ORDER BY code;
-- 
-- Expected 8 rows:
-- positive_thanks         | post_meeting  | 6  | 6
-- query_followup_budget   | query_raised  |    |
-- query_followup_documents| query_raised  |    |
-- query_followup_generic  | query_raised  |    |
-- query_followup_visit    | query_raised  |    |
-- rp_thanks               | post_meeting  | 8  | 9
-- tentative_thanks        | post_meeting  | 3  | 3
-- won_handover            | post_meeting  | 12 | 12
