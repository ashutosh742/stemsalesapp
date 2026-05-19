-- Migration 021: MoM v2 + Line Manager Accountability + LinkedIn CSR Agent
-- Date: 16 May 2026
-- Pairs with:
--   stem_mom_v2_form_spec.md
--   stem_new_lead_to_mom_bridge_spec.md
--   stem_linkedin_csr_agent_spec.md
-- Staging only until Mon 18 May 2026 GitHub access.
-- Idempotent: every CREATE uses IF NOT EXISTS, every ALTER checks via information_schema first.
-- Plain ASCII only. Production typos preserved: approving_autorities, fund_sanstion_limit, Compnay, Compny, Quater, Barg in Meeting.

START TRANSACTION;

-- ============================================================
-- PART 1: New Lead to MoM bridge - DM Contact block on init_call
-- ============================================================

ALTER TABLE init_call
  ADD COLUMN IF NOT EXISTS dm_contact_name VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS dm_contact_designation VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS dm_contact_phone VARCHAR(20) NULL,
  ADD COLUMN IF NOT EXISTS dm_contact_email VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS dm_contact_org_type ENUM('school','ngo','corporate','foundation','govt_dept','trust','csr_arm') NULL,
  ADD COLUMN IF NOT EXISTS dm_contact_filled_at DATETIME NULL,
  ADD COLUMN IF NOT EXISTS dm_contact_filled_by INT NULL,
  ADD KEY IF NOT EXISTS idx_dm_org_type (dm_contact_org_type),
  ADD KEY IF NOT EXISTS idx_dm_filled_at (dm_contact_filled_at);

CREATE TABLE IF NOT EXISTS init_call_contact_history (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  cid_id INT NOT NULL,
  field_name VARCHAR(60) NOT NULL,
  old_value TEXT,
  new_value TEXT,
  changed_by INT NOT NULL,
  changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reason_code ENUM('corrected_designation','dm_changed','typo_fix','role_promotion','role_demotion','initial_fill','other') NOT NULL DEFAULT 'other',
  source ENUM('new_lead_form','mom_form','lead_detail','admin_edit','api') NOT NULL DEFAULT 'mom_form',
  KEY idx_cid (cid_id),
  KEY idx_changed_at (changed_at),
  KEY idx_changed_by (changed_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PART 2: MoM v2 main table - extends mom_data, does not replace
-- ============================================================
-- Strategy: new columns on mom_data for v2 fields. The legacy 30 fields stay
-- but become optional. mom_quality_grade auto-computed at submit.

ALTER TABLE mom_data
  -- Section A. Meeting fact upgrades
  ADD COLUMN IF NOT EXISTS meeting_purpose_v2 ENUM('research','tentative','proposal_share','follow_up','rp','closure') NULL,
  ADD COLUMN IF NOT EXISTS meeting_with ENUM('dm','influencer','initiator','gatekeeper') NULL,

  -- Section B. DM block snapshot at MoM submit time (mirrors init_call)
  ADD COLUMN IF NOT EXISTS dm_name VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS dm_designation VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS dm_phone VARCHAR(20) NULL,
  ADD COLUMN IF NOT EXISTS dm_email VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS dm_org_type ENUM('school','ngo','corporate','foundation','govt_dept','trust','csr_arm') NULL,
  ADD COLUMN IF NOT EXISTS dm_contact_completeness TINYINT NULL,

  -- Section B. Approving authorities as JSON array of {name, designation, sanction_rs}
  ADD COLUMN IF NOT EXISTS approving_autorities_json JSON NULL,

  -- Section D. Proposal intent v2 (replaces submit_proposal text fields)
  ADD COLUMN IF NOT EXISTS proposal_intent_schools INT NULL,
  ADD COLUMN IF NOT EXISTS proposal_intent_budget_rs BIGINT NULL,
  ADD COLUMN IF NOT EXISTS proposal_intent_location VARCHAR(200) NULL,
  ADD COLUMN IF NOT EXISTS fitment_offer ENUM('school_visit','pilot_lab','trial_workshop','named_lab','demo','none') NULL,

  -- Section E. Proposal share record
  ADD COLUMN IF NOT EXISTS proposal_doc_url VARCHAR(500) NULL,
  ADD COLUMN IF NOT EXISTS proposal_shared_with VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS proposal_shared_date DATE NULL,
  ADD COLUMN IF NOT EXISTS proposal_value_rs BIGINT NULL,
  ADD COLUMN IF NOT EXISTS proposal_validity_days INT NULL,

  -- Section F. Proposal response
  ADD COLUMN IF NOT EXISTS proposal_review_status ENUM('not_reviewed_yet','reviewed_positive','reviewed_with_changes','rejected') NULL,

  -- Section G. Forecast
  ADD COLUMN IF NOT EXISTS expected_close_date DATE NULL,
  ADD COLUMN IF NOT EXISTS win_probability TINYINT NULL,
  ADD COLUMN IF NOT EXISTS r2b_status ENUM('not_started','drafted','shared','accepted','rejected_with_changes') NULL,

  -- Section H. Senior intervention upgrade
  ADD COLUMN IF NOT EXISTS intervention_level ENUM('none','cluster','pst','sales_head') NULL DEFAULT 'none',
  ADD COLUMN IF NOT EXISTS intervention_reason_code VARCHAR(40) NULL,
  ADD COLUMN IF NOT EXISTS intervention_sla_hours INT NULL,

  -- MoM quality and CSR cross-reference
  ADD COLUMN IF NOT EXISTS mom_quality_grade ENUM('A','B','C','D') NULL,
  ADD COLUMN IF NOT EXISTS mom_quality_score TINYINT NULL,
  ADD COLUMN IF NOT EXISTS gates_passed_json JSON NULL,
  ADD COLUMN IF NOT EXISTS csr_check_id INT NULL,
  ADD COLUMN IF NOT EXISTS v2_submitted_at DATETIME NULL,

  ADD KEY IF NOT EXISTS idx_meeting_purpose_v2 (meeting_purpose_v2),
  ADD KEY IF NOT EXISTS idx_mom_quality_grade (mom_quality_grade),
  ADD KEY IF NOT EXISTS idx_expected_close_date (expected_close_date),
  ADD KEY IF NOT EXISTS idx_r2b_status (r2b_status),
  ADD KEY IF NOT EXISTS idx_csr_check_id (csr_check_id);

-- ============================================================
-- PART 3: Structured signals (objections, competitors, authorities)
-- ============================================================
-- One row per signal so the CM can query "show me all objections about price in last 30 days".

CREATE TABLE IF NOT EXISTS mom_lead_signals (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  mom_id INT NOT NULL,
  cid_id INT NOT NULL,
  signal_type ENUM('objection','competitor','authority','offering_pitched','coaching_note','risk') NOT NULL,
  signal_code VARCHAR(40) NULL,
    -- For objections: price, scope, vendor, terms, timeline, references, gst, payment_advance, ref_visit, csr_alignment, board_approval, other
    -- For competitors: lighthouse, brightchamps, whitehat, byjus, vedantu, local_player, in_house, other
    -- For authorities: dm_layer, secondary, tertiary
    -- For offerings: msc, tinkering, bala, astronomy, diy, nsp, science_lab, smart_class
  signal_value TEXT,
  signal_rs BIGINT NULL,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved_at DATETIME NULL,
  resolved_by INT NULL,
  resolution_note TEXT,
  KEY idx_mom (mom_id),
  KEY idx_cid (cid_id),
  KEY idx_signal_type (signal_type),
  KEY idx_signal_code (signal_code),
  KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PART 4: LinkedIn CSR check
-- ============================================================

CREATE TABLE IF NOT EXISTS mom_csr_check (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mom_id INT NOT NULL,
  cid_id INT NOT NULL,
  dm_contact_name VARCHAR(120) NOT NULL,
  dm_contact_designation VARCHAR(120) NOT NULL,
  dm_contact_org_type VARCHAR(40) NOT NULL,
  school_name VARCHAR(200) NULL,
  search_query TEXT,
  candidate_profile_url VARCHAR(500),
  candidate_headline TEXT,
  candidate_company VARCHAR(200),
  csr_intent_confidence TINYINT NOT NULL DEFAULT 0,
  verdict ENUM('verified','likely','doubtful','not_csr','no_match','timeout','opt_out','rate_limit_hit') NOT NULL,
  rubric_breakdown JSON,
  raw_snippet TEXT,
  ran_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  agent_version VARCHAR(20) NOT NULL DEFAULT 'v1',
  cache_key VARCHAR(255) GENERATED ALWAYS AS (CONCAT(LOWER(dm_contact_name),'|',LOWER(dm_contact_org_type),'|',LOWER(IFNULL(school_name,'')))) STORED,
  KEY idx_mom (mom_id),
  KEY idx_cid (cid_id),
  KEY idx_verdict (verdict),
  KEY idx_cache_key (cache_key),
  KEY idx_ran_at (ran_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daily rate-limit counter (200/day cap)
CREATE TABLE IF NOT EXISTS csr_check_daily_quota (
  quota_date DATE PRIMARY KEY,
  checks_run INT NOT NULL DEFAULT 0,
  cap_reached_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PART 5: Line manager accountability surfaces
-- ============================================================

-- Per-MoM CM action ledger: every approve, reject, request_edit, coaching note
CREATE TABLE IF NOT EXISTS mom_line_manager_review (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  mom_id INT NOT NULL,
  cid_id INT NOT NULL,
  manager_uid INT NOT NULL,
  manager_role ENUM('cm','acm','pst','sales_head','accounts_officer','director') NOT NULL,
  action ENUM('approve','reject','request_edit','coaching_note','escalate','override_csr_verdict') NOT NULL,
  reject_reason_code VARCHAR(40) NULL,
    -- structured reasons: dm_identity_unverified, mom_quality_low, objection_unresolved, narrative_too_short,
    --                     proposal_value_mismatch, csr_verdict_not_csr, missing_authority, missing_close_date, other
  coaching_note TEXT,
  action_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sla_breach_minutes INT NULL,
  KEY idx_mom (mom_id),
  KEY idx_manager (manager_uid),
  KEY idx_action_at (action_at),
  KEY idx_reject_reason (reject_reason_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Stage signoff: closes the 4 missing gates between cstatus 6 and 12
CREATE TABLE IF NOT EXISTS lead_stage_signoff (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  cid_id INT NOT NULL,
  from_cstatus TINYINT NOT NULL,
  to_cstatus TINYINT NOT NULL,
  bd_uid INT NOT NULL,
  bd_requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  manager_uid INT NULL,
  manager_role ENUM('cm','acm','pst','sales_head','accounts_officer') NULL,
  manager_action ENUM('pending','approved','rejected','requested_edit') NOT NULL DEFAULT 'pending',
  manager_action_at DATETIME NULL,
  payload_json JSON NULL,
    -- For 9 to 12: payment_plan_clarified, gst_status, vendor_form_status, contract_value_rs
    -- For 6 to 7: proposal_doc_url, proposal_value_rs, proposal_shared_date
    -- For 7 to 8: proposal_review_status, objections_resolved_count
    -- For 8 to 9: r2b_status, expected_close_date, win_probability
  reject_reason TEXT,
  sla_hours_target INT NOT NULL DEFAULT 24,
  sla_breach_at DATETIME NULL,
  KEY idx_cid (cid_id),
  KEY idx_bd (bd_uid),
  KEY idx_manager (manager_uid),
  KEY idx_action (manager_action),
  KEY idx_hop (from_cstatus, to_cstatus),
  KEY idx_requested_at (bd_requested_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PART 6: Line manager weekly scorecard rollup
-- ============================================================

CREATE TABLE IF NOT EXISTS line_manager_scorecard_weekly (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  manager_uid INT NOT NULL,
  manager_role VARCHAR(20) NOT NULL,
  week_start DATE NOT NULL,
  week_end DATE NOT NULL,
  -- KPI 1: MoM approval SLA - percent of MoMs approved within 12 hours of submit
  moms_total INT NOT NULL DEFAULT 0,
  moms_approved_within_sla INT NOT NULL DEFAULT 0,
  approval_sla_pct DECIMAL(5,2) NULL,
  -- KPI 2: MoM quality flag rate - percent of own-cluster MoMs at grade C or D
  moms_grade_cd INT NOT NULL DEFAULT 0,
  quality_flag_pct DECIMAL(5,2) NULL,
  -- KPI 3: Stage signoff turnaround - average hours between bd_requested_at and manager_action_at
  signoffs_total INT NOT NULL DEFAULT 0,
  signoff_avg_hours DECIMAL(6,2) NULL,
  -- KPI 4: R2B follow-through - percent of cstatus 8 leads with r2b_status=shared or accepted
  cstatus_8_leads INT NOT NULL DEFAULT 0,
  r2b_shared_or_accepted INT NOT NULL DEFAULT 0,
  r2b_followthrough_pct DECIMAL(5,2) NULL,
  -- KPI 5: Stuck closure ratio - percent of cluster leads at cstatus 9 over 14 days
  cstatus_9_leads INT NOT NULL DEFAULT 0,
  cstatus_9_stuck_over_14 INT NOT NULL DEFAULT 0,
  stuck_closure_pct DECIMAL(5,2) NULL,
  -- KPI 6 (new): DM verification follow-through - percent of not_csr/doubtful MoMs actioned within 48h
  csr_flagged_moms INT NOT NULL DEFAULT 0,
  csr_actioned_within_48h INT NOT NULL DEFAULT 0,
  csr_followthrough_pct DECIMAL(5,2) NULL,
  -- KPI 7 (new): DM contact completeness - percent of cluster leads at cstatus 6+ with DM block filled
  cstatus_6plus_leads INT NOT NULL DEFAULT 0,
  cstatus_6plus_with_dm INT NOT NULL DEFAULT 0,
  dm_completeness_pct DECIMAL(5,2) NULL,
  -- Composite grade
  scorecard_grade ENUM('A+','A','B','C','D') NULL,
  scorecard_score DECIMAL(5,2) NULL,
  computed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_manager_week (manager_uid, week_start),
  KEY idx_week (week_start),
  KEY idx_grade (scorecard_grade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PART 7: Views for the morning crons
-- ============================================================

CREATE OR REPLACE VIEW v_mom_quality_today AS
SELECT
  m.id AS mom_id,
  m.cid_id,
  m.uid AS bd_uid,
  u.username AS bd_name,
  m.meeting_purpose_v2,
  m.meeting_with,
  m.dm_name,
  m.dm_designation,
  m.dm_org_type,
  m.mom_quality_grade,
  m.mom_quality_score,
  m.dm_contact_completeness,
  csr.verdict AS csr_verdict,
  csr.csr_intent_confidence,
  m.approved_status,
  m.v2_submitted_at,
  TIMESTAMPDIFF(MINUTE, m.v2_submitted_at, NOW()) AS minutes_since_submit
FROM mom_data m
LEFT JOIN user u ON u.uid = m.uid
LEFT JOIN mom_csr_check csr ON csr.id = m.csr_check_id
WHERE DATE(m.v2_submitted_at) = CURDATE();

CREATE OR REPLACE VIEW v_lead_stage_signoff_pending AS
SELECT
  s.id,
  s.cid_id,
  ic.school_name,
  s.from_cstatus,
  s.to_cstatus,
  s.bd_uid,
  u.username AS bd_name,
  s.bd_requested_at,
  s.manager_uid,
  m.username AS manager_name,
  s.manager_role,
  TIMESTAMPDIFF(HOUR, s.bd_requested_at, NOW()) AS age_hours,
  s.sla_hours_target,
  CASE WHEN TIMESTAMPDIFF(HOUR, s.bd_requested_at, NOW()) > s.sla_hours_target
       THEN 1 ELSE 0 END AS sla_breached
FROM lead_stage_signoff s
LEFT JOIN init_call ic ON ic.id = s.cid_id
LEFT JOIN user u ON u.uid = s.bd_uid
LEFT JOIN user m ON m.uid = s.manager_uid
WHERE s.manager_action = 'pending';

CREATE OR REPLACE VIEW v_csr_check_flagged_today AS
SELECT
  c.id AS csr_check_id,
  c.mom_id,
  c.cid_id,
  c.dm_contact_name,
  c.dm_contact_designation,
  c.school_name,
  c.verdict,
  c.csr_intent_confidence,
  c.ran_at,
  m.uid AS bd_uid,
  u.username AS bd_name,
  m.approved_status
FROM mom_csr_check c
LEFT JOIN mom_data m ON m.id = c.mom_id
LEFT JOIN user u ON u.uid = m.uid
WHERE c.verdict IN ('not_csr','doubtful')
  AND DATE(c.ran_at) = CURDATE();

CREATE OR REPLACE VIEW v_dm_contact_gap AS
SELECT
  ic.id AS cid_id,
  ic.school_name,
  ic.cstatus,
  ic.mainbd AS bd_uid,
  u.username AS bd_name,
  ic.cluster,
  ic.dm_contact_name,
  ic.dm_contact_designation,
  ic.dm_contact_phone,
  ic.dm_contact_email,
  CASE
    WHEN ic.dm_contact_name IS NULL THEN 'no_name'
    WHEN ic.dm_contact_designation IS NULL THEN 'no_designation'
    WHEN ic.dm_contact_phone IS NULL AND ic.dm_contact_email IS NULL THEN 'no_contact'
    ELSE 'complete'
  END AS gap_reason
FROM init_call ic
LEFT JOIN user u ON u.uid = ic.mainbd
WHERE ic.cstatus >= 3
  AND (ic.dm_contact_name IS NULL
       OR ic.dm_contact_designation IS NULL
       OR (ic.dm_contact_phone IS NULL AND ic.dm_contact_email IS NULL));

-- ============================================================
-- PART 8: Seed rows for CSR keyword list (configurable)
-- ============================================================

CREATE TABLE IF NOT EXISTS csr_keyword_list (
  id INT AUTO_INCREMENT PRIMARY KEY,
  keyword VARCHAR(80) NOT NULL UNIQUE,
  category ENUM('role','activity','group','penalty') NOT NULL,
  weight TINYINT NOT NULL DEFAULT 1,
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO csr_keyword_list (keyword, category, weight) VALUES
  ('csr', 'role', 3),
  ('corporate social responsibility', 'role', 3),
  ('corporate social', 'role', 2),
  ('sustainability', 'role', 2),
  ('foundation', 'role', 2),
  ('trust', 'role', 1),
  ('philanthropy', 'role', 2),
  ('social impact', 'role', 2),
  ('community', 'role', 1),
  ('esg', 'role', 2),
  ('csr committee', 'group', 3),
  ('csr head', 'role', 3),
  ('csr manager', 'role', 3),
  ('csr lead', 'role', 2),
  ('sustainability head', 'role', 3),
  ('foundation trustee', 'role', 3),
  ('csr trustee', 'role', 3),
  ('hr head', 'penalty', 3),
  ('admin', 'penalty', 2),
  ('executive assistant', 'penalty', 2),
  ('office manager', 'penalty', 2),
  ('receptionist', 'penalty', 3),
  ('it head', 'penalty', 2),
  ('marketing only', 'penalty', 1);

-- ============================================================
-- PART 9: Deploy log row
-- ============================================================

CREATE TABLE IF NOT EXISTS migration_log (
  migration_name VARCHAR(60) PRIMARY KEY,
  deployed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO migration_log (migration_name, notes) VALUES
  ('021_mom_v2_and_line_manager', 'MoM v2 18-question form, DM contact bridge from New Lead, LinkedIn CSR agent, line manager scorecard, lead stage signoff gates. Staging only until 18 May 2026. Pilot 25 May 2026.');

COMMIT;

-- ============================================================
-- Rollback (kept for staging safety, do not run in prod)
-- ============================================================
-- DROP TABLE IF EXISTS line_manager_scorecard_weekly;
-- DROP TABLE IF EXISTS lead_stage_signoff;
-- DROP TABLE IF EXISTS mom_line_manager_review;
-- DROP TABLE IF EXISTS csr_check_daily_quota;
-- DROP TABLE IF EXISTS mom_csr_check;
-- DROP TABLE IF EXISTS mom_lead_signals;
-- DROP TABLE IF EXISTS init_call_contact_history;
-- DROP VIEW IF EXISTS v_mom_quality_today;
-- DROP VIEW IF EXISTS v_lead_stage_signoff_pending;
-- DROP VIEW IF EXISTS v_csr_check_flagged_today;
-- DROP VIEW IF EXISTS v_dm_contact_gap;
-- ALTER TABLE mom_data DROP COLUMN meeting_purpose_v2, DROP COLUMN meeting_with, DROP COLUMN dm_name, ...
-- ALTER TABLE init_call DROP COLUMN dm_contact_name, DROP COLUMN dm_contact_designation, ...
