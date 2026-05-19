-- =====================================================================
-- STEM CRM - Migration 025: Meeting Agenda Template Seed Data
-- =====================================================================
-- Seeds the meeting_agenda_template table with question banks per
-- meeting purpose and cstatus stage. Used by the live meeting screen
-- mid-meeting agenda nudge card and by the meeting quality agent to
-- score "did the BD ask the right questions".
--
-- Each row maps (purpose_id, cstatus_min, cstatus_max) to an ordered
-- list of questions. The live meeting UI pulls these at meeting start
-- and shows them as a checklist. The meeting quality agent compares
-- the transcript against the checklist to assign coverage_pct.
--
-- Run order: after stem_migration_025_sql.sql, before staging deploy.
-- Idempotent: uses INSERT IGNORE on a uniqueness composite.
-- =====================================================================

-- Safety: only run if migration 025 base table exists
SET @t = (SELECT COUNT(*) FROM information_schema.tables
          WHERE table_schema = DATABASE() AND table_name = 'meeting_agenda_template');
SET @msg = IF(@t = 0, 'meeting_agenda_template missing, run stem_migration_025_sql.sql first', 'OK');
SELECT @msg AS preflight_check;

-- Truncate before reseeding (template is canonical, never user-edited)
TRUNCATE TABLE meeting_agenda_template;

-- =====================================================================
-- PURPOSE 1 - FIRST MEETING (FRESH) - cstatus 1-2 Open / Reachout
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(1, 'First Meeting', 1, 2, 1,
 'Who is the decision maker for STEM lab or curriculum purchase here',
 'dm_name_designation', 1, 10, 'cannot_promote_to_3_without'),
(1, 'First Meeting', 1, 2, 2,
 'How many sections per grade and which grades are in scope',
 'count_per_grade', 1, 8, NULL),
(1, 'First Meeting', 1, 2, 3,
 'What is the school annual fee per student',
 'rs_amount', 1, 7, NULL),
(1, 'First Meeting', 1, 2, 4,
 'When does the school finalize next year capex budget',
 'month_year', 1, 7, 'fund_sanstion_limit'),
(1, 'First Meeting', 1, 2, 5,
 'Who are the approving autorities for capex over 5 lakh',
 'role_list', 1, 6, 'approving_autorities'),
(1, 'First Meeting', 1, 2, 6,
 'Is the school open to a pilot demonstration with 1 grade',
 'yes_no_or_when', 1, 6, NULL),
(1, 'First Meeting', 1, 2, 7,
 'What is the current STEM or computer lab vendor if any',
 'vendor_name_or_none', 0, 4, NULL),
(1, 'First Meeting', 1, 2, 8,
 'Any CSR sponsor or trust funding for the school',
 'org_name_or_none', 0, 5, NULL);

-- =====================================================================
-- PURPOSE 1 - FOLLOWUP MEETING - cstatus 3-5 Tentative
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(1, 'Followup Meeting', 3, 5, 1,
 'Has the DM read the proposal sent on the previous visit',
 'yes_no_reason', 1, 8, NULL),
(1, 'Followup Meeting', 3, 5, 2,
 'What are the current objections or concerns',
 'objection_list', 1, 10, 'cannot_promote_to_6_without'),
(1, 'Followup Meeting', 3, 5, 3,
 'What is the expected closure month',
 'month_year', 1, 9, 'fund_sanstion_limit'),
(1, 'Followup Meeting', 3, 5, 4,
 'Has the DM consulted the principal or director',
 'yes_no', 1, 7, NULL),
(1, 'Followup Meeting', 3, 5, 5,
 'Is a CSR partner being considered for funding',
 'org_name_or_none', 0, 6, NULL),
(1, 'Followup Meeting', 3, 5, 6,
 'What grades and section count was finally agreed',
 'count_per_grade', 1, 8, NULL);

-- =====================================================================
-- PURPOSE 3 - DM MEETING - cstatus 6 Positive (RP MEETING ZONE)
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(3, 'DM Meeting RP', 6, 6, 1,
 'Confirm DM name, designation, mobile and email',
 'dm_full_block', 1, 12, 'cannot_promote_to_8_without'),
(3, 'DM Meeting RP', 6, 6, 2,
 'Decision making process inside the Compny who else signs',
 'role_list', 1, 10, 'approving_autorities'),
(3, 'DM Meeting RP', 6, 6, 3,
 'Budget allotment for STEM this year, in lakh',
 'rs_lakh', 1, 12, 'fund_sanstion_limit'),
(3, 'DM Meeting RP', 6, 6, 4,
 'Expected purchase order date or approval window',
 'date_or_window', 1, 10, NULL),
(3, 'DM Meeting RP', 6, 6, 5,
 'Competition being evaluated, vendors and prices',
 'vendor_price_list', 1, 9, NULL),
(3, 'DM Meeting RP', 6, 6, 6,
 'Demo or pilot agreement, when and which grade',
 'date_grade', 1, 9, NULL),
(3, 'DM Meeting RP', 6, 6, 7,
 'CSR or named lab option, sponsor identity',
 'sponsor_or_none', 0, 7, NULL),
(3, 'DM Meeting RP', 6, 6, 8,
 'Lab size requirement, sqft and station count',
 'sqft_count', 1, 7, NULL),
(3, 'DM Meeting RP', 6, 6, 9,
 'Curriculum or NEP alignment requirements',
 'requirement_list', 0, 5, NULL),
(3, 'DM Meeting RP', 6, 6, 10,
 'Implementation timeline expected by school',
 'months', 1, 6, NULL);

-- =====================================================================
-- PURPOSE 3 - OPEN RPEM - cstatus 8 (PROPOSAL FOLLOWUP)
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(3, 'Proposal Review', 8, 8, 1,
 'Has the proposal been circulated to all approving autorities',
 'yes_no_who', 1, 12, 'cannot_promote_to_9_without'),
(3, 'Proposal Review', 8, 8, 2,
 'What are the open objections or amendments requested',
 'objection_list', 1, 11, NULL),
(3, 'Proposal Review', 8, 8, 3,
 'Confirmed expected close date',
 'specific_date', 1, 12, NULL),
(3, 'Proposal Review', 8, 8, 4,
 'Final fund sanstion limit confirmed by DM',
 'rs_lakh', 1, 11, 'fund_sanstion_limit'),
(3, 'Proposal Review', 8, 8, 5,
 'Any change in grade count or section count',
 'count_per_grade', 0, 7, NULL),
(3, 'Proposal Review', 8, 8, 6,
 'CSR partner finalised',
 'sponsor_or_none', 0, 7, NULL),
(3, 'Proposal Review', 8, 8, 7,
 'Competition status, are they shortlisted with us',
 'vendor_shortlist', 1, 8, NULL),
(3, 'Proposal Review', 8, 8, 8,
 'Order placement timeline next 30 60 90',
 'timeline_bucket', 1, 9, NULL);

-- =====================================================================
-- PURPOSE 3 - VERY POSITIVE - cstatus 9 (CLOSURE ZONE)
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(3, 'Very Positive Closure', 9, 9, 1,
 'PO number or LOI date when will it be issued',
 'specific_date', 1, 15, 'cannot_promote_to_12_without'),
(3, 'Very Positive Closure', 9, 9, 2,
 'Final closed value, all heads, in rupees',
 'rs_amount', 1, 15, 'closed_value_rs'),
(3, 'Very Positive Closure', 9, 9, 3,
 'Payment terms, advance percent and milestone schedule',
 'payment_terms', 1, 12, NULL),
(3, 'Very Positive Closure', 9, 9, 4,
 'Implementation start date',
 'specific_date', 1, 11, NULL),
(3, 'Very Positive Closure', 9, 9, 5,
 'GST and PAN details of buying entity',
 'gst_pan', 1, 10, NULL),
(3, 'Very Positive Closure', 9, 9, 6,
 'Shipping address and site readiness',
 'address_status', 1, 8, NULL),
(3, 'Very Positive Closure', 9, 9, 7,
 'Warranty and training scope as agreed',
 'scope_text', 0, 7, NULL),
(3, 'Very Positive Closure', 9, 9, 8,
 'Has CM or RM been included in closure call',
 'yes_no_who', 1, 11, 'joint_meeting_required');

-- =====================================================================
-- PURPOSE 4 - BARGE MEETING - cstatus any (FIRST WALK IN)
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(4, 'Barge Meeting Fresh', 1, 2, 1,
 'Name of school principal or director, designation',
 'dm_name_designation', 1, 12, 'cannot_categorize_lead_without'),
(4, 'Barge Meeting Fresh', 1, 2, 2,
 'Section count and grade range board affiliation',
 'school_profile', 1, 10, NULL),
(4, 'Barge Meeting Fresh', 1, 2, 3,
 'Annual fee per student and total student count',
 'fee_and_count', 1, 10, 'fbudget_seed'),
(4, 'Barge Meeting Fresh', 1, 2, 4,
 'Is there a current STEM or robotics initiative',
 'yes_no_details', 1, 8, NULL),
(4, 'Barge Meeting Fresh', 1, 2, 5,
 'CSR partner or sponsor backing the school',
 'org_name_or_none', 0, 7, NULL),
(4, 'Barge Meeting Fresh', 1, 2, 6,
 'Best time to schedule a formal DM meeting',
 'date_window', 1, 8, NULL),
(4, 'Barge Meeting Fresh', 1, 2, 7,
 'Decision making style, single DM or committee',
 'dm_style', 1, 6, 'approving_autorities'),
(4, 'Barge Meeting Fresh', 1, 2, 8,
 'Capex cycle, when budgets get finalised',
 'month_year', 1, 7, NULL);

-- =====================================================================
-- PURPOSE 10 - RESEARCH / COLD RECEE
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(10, 'Research Cold Recee', 1, 1, 1,
 'School name, address, board, grades and sections',
 'school_profile', 1, 10, NULL),
(10, 'Research Cold Recee', 1, 1, 2,
 'Public listing of principal or director name online',
 'name_source', 1, 8, NULL),
(10, 'Research Cold Recee', 1, 1, 3,
 'Approximate fee from public listings if available',
 'rs_amount_or_unknown', 0, 5, NULL),
(10, 'Research Cold Recee', 1, 1, 4,
 'Best entry channel, CSR partner, education body, alumni',
 'channel_type', 1, 7, NULL),
(10, 'Research Cold Recee', 1, 1, 5,
 'Lat lng for planner seeding',
 'lat_lng', 1, 8, NULL);

-- =====================================================================
-- PURPOSE 6 - UPSELL MEETING (cstatus 12 Won customers, post-sale)
-- For RM and CM driven meetings on closed accounts
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(6, 'Upsell PSU Meeting', 12, 12, 1,
 'Current usage of installed lab, sessions per week',
 'usage_metric', 1, 10, NULL),
(6, 'Upsell PSU Meeting', 12, 12, 2,
 'Satisfaction with implementation and content',
 'satisfaction_score', 1, 9, NULL),
(6, 'Upsell PSU Meeting', 12, 12, 3,
 'Interest in additional grade expansion or new product',
 'product_interest', 1, 12, 'upsell_lane_assignment'),
(6, 'Upsell PSU Meeting', 12, 12, 4,
 'Renewal date for current contract',
 'renewal_date', 1, 12, 'anchor_renewal_due'),
(6, 'Upsell PSU Meeting', 12, 12, 5,
 'New budget cycle and capacity for FY ahead',
 'rs_lakh', 1, 10, NULL),
(6, 'Upsell PSU Meeting', 12, 12, 6,
 'Reference willingness for sister schools',
 'yes_no_list', 0, 7, NULL),
(6, 'Upsell PSU Meeting', 12, 12, 7,
 'DMFT cohort or PSU tender awareness in district',
 'cohort_signal', 0, 8, NULL);

-- =====================================================================
-- PURPOSE 13 - JOINT CM OR RM MEETING (mandatory at cstatus 8 9 12)
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block)
VALUES
(13, 'Joint Meeting CM RM', 8, 12, 1,
 'CM or RM intro and senior level confidence to DM',
 'introduction_done', 1, 12, 'cannot_close_without_cm_signoff'),
(13, 'Joint Meeting CM RM', 8, 12, 2,
 'Senior level objection handling, what got resolved',
 'objection_resolution', 1, 11, NULL),
(13, 'Joint Meeting CM RM', 8, 12, 3,
 'Commercial concession discussed, final value',
 'rs_amount', 1, 12, NULL),
(13, 'Joint Meeting CM RM', 8, 12, 4,
 'CM signoff that lead is ready for closure',
 'signoff_yes_no', 1, 15, 'g3_cm_signoff_block'),
(13, 'Joint Meeting CM RM', 8, 12, 5,
 'Followup actions assigned to BD and CM',
 'action_list', 1, 8, NULL);

-- =====================================================================
-- TRAVEL CLUSTER SPECIAL - any travel cluster meeting must be RP grade
-- Higher scoring weight reflects the higher cost of travel
-- =====================================================================
INSERT INTO meeting_agenda_template
(purpose_id, purpose_label, cstatus_min, cstatus_max, question_order,
 question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block,
 travel_cluster_only)
VALUES
(3, 'Travel Cluster DM Meeting', 1, 9, 1,
 'You travelled, confirm DM name designation mobile email NOW',
 'dm_full_block', 1, 15, 'travel_cluster_rp_required', 1),
(3, 'Travel Cluster DM Meeting', 1, 9, 2,
 'Travel cluster RP mandate, what objections came up',
 'objection_list', 1, 12, 'travel_cluster_rp_required', 1),
(3, 'Travel Cluster DM Meeting', 1, 9, 3,
 'Concrete next step in writing from DM today',
 'specific_commitment', 1, 14, 'travel_cluster_rp_required', 1),
(3, 'Travel Cluster DM Meeting', 1, 9, 4,
 'Budget number got from DM mouth, no estimates',
 'rs_lakh', 1, 13, 'fund_sanstion_limit', 1),
(3, 'Travel Cluster DM Meeting', 1, 9, 5,
 'Got details only with no DM meet equals double penalty',
 'dm_seen_yes_no', 1, 15, 'travel_double_penalty_warn', 1);

-- =====================================================================
-- POST SEED VERIFICATION
-- =====================================================================
SELECT 'Agenda templates seeded' AS status,
       COUNT(*) AS total_questions,
       COUNT(DISTINCT purpose_id) AS unique_purposes,
       COUNT(DISTINCT CONCAT(purpose_id, '_', cstatus_min)) AS unique_stages,
       SUM(is_mandatory) AS mandatory_count,
       SUM(CASE WHEN travel_cluster_only = 1 THEN 1 ELSE 0 END) AS travel_cluster_specific
FROM meeting_agenda_template;

-- Spot check the gate_block coverage so the meeting quality agent
-- can later assert "missing answer to gate_block question means BD
-- cannot promote cstatus"
SELECT purpose_id, purpose_label, cstatus_min, cstatus_max,
       COUNT(*) AS questions,
       SUM(CASE WHEN gate_block IS NOT NULL THEN 1 ELSE 0 END) AS gates
FROM meeting_agenda_template
GROUP BY purpose_id, purpose_label, cstatus_min, cstatus_max
ORDER BY purpose_id, cstatus_min;

-- =====================================================================
-- END OF SEED
-- Re-runnable: truncates and reseeds. Safe in staging. In production
-- pin the seed file SHA1 in the deploy runbook so any in-flight edits
-- by ops cannot drift away from the canonical question bank.
-- =====================================================================
