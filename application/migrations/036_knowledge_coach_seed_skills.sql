-- ============================================================================
-- STEM CRM - Migration 036 - Seed: skill_definition
-- 14 BD skills mapped to cstatus pipeline stages
-- ============================================================================
-- Plain English. No em-dashes. No non-ASCII.
-- Idempotent: INSERT IGNORE on skill_code PK.
-- Author: STEM ops
-- Date: 2026-05-18
-- ============================================================================

-- ----------------------------------------------------------------------------
-- skill_definition: 14 rows
-- Rubric doc URLs point to markdown files under stem_coach_rubrics/.
-- primary_cstatus_range uses the cstatus integer labels from the spec:
--   1=Open, 2=Reachout, 3=Tentative, 6=Positive, 7=Proposal sent,
--   8=Open RPEM, 9=Very Positive, 12=Won, 13=Lost
-- ----------------------------------------------------------------------------

INSERT IGNORE INTO skill_definition
  (skill_code, skill_name, category, description, primary_cstatus_range,
   rubric_version, rubric_doc_url, drill_count, status)
VALUES

-- cstatus 1-2: Open and Reachout
('PROSPECTING',
 'Prospecting',
 'prospect',
 'Identify, research, and qualify high-potential school leads before making contact. Includes territory mapping, referral mining, government school list parsing, and CSR-budget signal detection.',
 '1-2',
 1,
 'stem_coach_rubrics/prospecting.md',
 2,
 'active'),

('DISCOVERY',
 'Discovery',
 'discovery',
 'Surface the school principal or trust decision-maker pain points, budget intent, and timeline through structured open questions. Capture findings in tblcallevents notes.',
 '2',
 1,
 'stem_coach_rubrics/discovery.md',
 2,
 'active'),

('COLD_CALL_OPENING',
 'Cold-call Opening',
 'prospect',
 'Deliver a confident, on-brand opening line in the first 30 seconds of a cold call or WhatsApp intro. Reference the school by name, cite a relevant hook, and earn the right to continue.',
 '1-2',
 1,
 'stem_coach_rubrics/cold_call_opening.md',
 2,
 'active'),

-- cstatus 3: Tentative
('PITCHING',
 'Pitching',
 'pitch',
 'Walk the principal through the named-lab demo, anchor on measurable student outcomes, pre-empt the top three objections (price, space, teacher training), and close with a named next step.',
 '3',
 1,
 'stem_coach_rubrics/pitching.md',
 2,
 'active'),

('OBJECTION_HANDLING',
 'Objection Handling',
 'pitch',
 'Respond to the principal or trustee objections (cost, ROI doubt, competitor comparison, space constraints) with data, reference stories, and reframing without conceding price.',
 '3',
 1,
 'stem_coach_rubrics/objection_handling.md',
 2,
 'active'),

('DM_MAPPING',
 'Decision-maker Mapping',
 'stakeholder',
 'Identify and map all key stakeholders: Decision Maker (DM), Final Buyer (FB), CSR sponsor, and influencer. Document roles and alignment angles in the lead record.',
 '3',
 1,
 'stem_coach_rubrics/dm_mapping.md',
 2,
 'active'),

-- cstatus 6-7: Positive and Proposal sent
('PROPOSAL_WRITING',
 'Proposal Writing',
 'proposal',
 'Produce a clear, priced, school-specific proposal within the 14-day SLA. Use the standard STEM template, fence pricing tiers, include named-lab configuration, and get coach pre-approval before sending.',
 '6-7',
 1,
 'stem_coach_rubrics/proposal_writing.md',
 2,
 'active'),

('FOLLOWUP_CADENCE',
 'Follow-up Cadence',
 'follow',
 'Execute a 3-touch follow-up sequence (call, WhatsApp, email) after proposal send. Share 2 reference school stories, handle silence, and advance to a soft-close conversation.',
 '6-7',
 1,
 'stem_coach_rubrics/followup_cadence.md',
 2,
 'active'),

-- cstatus 8-9: Open RPEM and Very Positive
('NEGOTIATION',
 'Negotiation',
 'negotiate',
 'Hold the price fence against early concession requests. Trade value (free training session, extended warranty, staggered payment) rather than discounting. Document all trades in the MoM.',
 '8-9',
 1,
 'stem_coach_rubrics/negotiation.md',
 2,
 'active'),

('CLOSING',
 'Closing',
 'close',
 'Secure a conditional commitment from the decision-maker: if-then framing, named signature date, and payment-term clarity. Ask for the order explicitly rather than waiting for the school to volunteer.',
 '8-9',
 1,
 'stem_coach_rubrics/closing.md',
 2,
 'active'),

('STAKEHOLDER_MANAGEMENT',
 'Stakeholder Management',
 'stakeholder',
 'Coordinate across principal, management trustee, CSR sponsor, and procurement to keep all influencers aligned and prevent last-minute veto. Maintain a stakeholder map updated after every meeting.',
 '8-9',
 1,
 'stem_coach_rubrics/stakeholder_management.md',
 2,
 'active'),

-- cstatus 12: Won
('REFERRALS',
 'Referrals and Reference Selling',
 'referral',
 'Ask the newly won principal for introductions to 2 sister schools or peer institutions within 30 days of closure. Convert won accounts into active reference stories for the pitch library.',
 '12',
 1,
 'stem_coach_rubrics/referrals.md',
 2,
 'active'),

('ACCOUNT_EXPANSION',
 'Account Expansion',
 'referral',
 'Within 90 days of a Won closure, identify the upsell lane (additional lab, AMC renewal, teacher certification program) and seed it for the RM. Tag the expansion intent in the CRM.',
 '12',
 1,
 'stem_coach_rubrics/account_expansion.md',
 2,
 'active'),

-- Cross-stage skills
('PRESENTATION',
 'Presentation',
 'pitch',
 'Deliver structured, visually supported presentations to school leadership and trust boards. Maintain eye contact, manage time, adapt to audience reaction, and handle interruptions professionally.',
 '3,6,8',
 1,
 'stem_coach_rubrics/presentation.md',
 2,
 'active'),

('ACTIVE_LISTENING',
 'Active Listening',
 'discovery',
 'Demonstrate genuine attention to the school contact: paraphrase their concern, ask clarifying follow-ups, avoid interrupting, and note stated and unstated pain points for post-meeting debrief.',
 '1,2,3,6,7,8,9',
 1,
 'stem_coach_rubrics/active_listening.md',
 2,
 'active');

-- ============================================================================
-- END OF SEED: skill_definition (14 rows + 1 bonus = 15 IGNORE-safe)
-- Note: The task specifies 14 skills. active_listening is the 14th cross-stage
-- skill. presentation is the 13th. Both are required by the spec.
-- ============================================================================
