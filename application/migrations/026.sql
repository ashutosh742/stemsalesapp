-- =====================================================================
-- Migration 026: CM Nurture Discipline + Proposal SLA + Email Agent
-- =====================================================================
-- Author: STEM Learning eng
-- Target: stemapp.in staging first, prod 1 Jun 2026 (Phase 1) + 1 Jul 2026 (Phase 2)
-- Sibling migrations: 022 (line manager), 023 (CM/RM/target), 024 (funnel hygiene), 025 (universal meeting)
--
-- Phase 1 (1 Jun 2026): proposal_sla_tracker, bd_planner_block_log, email_agent_*, bd_gmail_oauth_token, email_template, lead_nurture_score
-- Phase 2 (1 Jul 2026): lead_query_checklist, lead_query_breach_log, cm_lead_call_log
--
-- Probe endpoint: GET /api/proposal/sla/probe returns 200 when 026 deployed
-- =====================================================================

START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. PROPOSAL SLA TRACKER
-- One row per init_call when it reaches cstatus 6 (Positive). Tracks the
-- 48 h hard deadline for BD to upload a proposal document.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS proposal_sla_tracker (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id INT UNSIGNED NOT NULL COMMENT 'FK init_call.id',
  bd_uid INT UNSIGNED NOT NULL COMMENT 'FK user.uid - lead owner',
  cm_uid INT UNSIGNED DEFAULT NULL COMMENT 'FK user.uid - cluster CM',
  positive_at DATETIME NOT NULL COMMENT 'When cstatus moved to 6',
  sla_deadline DATETIME NOT NULL COMMENT 'positive_at + 48 hours',
  proposal_submitted_at DATETIME DEFAULT NULL,
  proposal_doc_url VARCHAR(500) DEFAULT NULL,
  extension_used TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if BD took the single 24h extension',
  extension_granted_at DATETIME DEFAULT NULL,
  extension_reason VARCHAR(300) DEFAULT NULL,
  status ENUM('open','submitted','extended','breached','downgraded') NOT NULL DEFAULT 'open',
  breach_processed_at DATETIME DEFAULT NULL COMMENT 'When wallet debit + grade penalty applied',
  wallet_debit_rs INT NOT NULL DEFAULT 0,
  grade_penalty_points INT NOT NULL DEFAULT 0,
  downgrade_from_cstatus TINYINT UNSIGNED DEFAULT NULL,
  downgrade_to_cstatus TINYINT UNSIGNED DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_cid (cid_id),
  KEY idx_bd_status (bd_uid, status),
  KEY idx_deadline (sla_deadline, status),
  KEY idx_positive_at (positive_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026: proposal must be submitted within 48 h of cstatus 6';

-- ---------------------------------------------------------------------
-- 2. BD PLANNER BLOCK LOG
-- Audit trail of every time BD planner draft is hard-blocked because
-- of an open proposal SLA breach. Joins to daily_planner via plan_date.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bd_planner_block_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  bd_uid INT UNSIGNED NOT NULL,
  plan_date DATE NOT NULL COMMENT 'The day BD was trying to plan FOR',
  blocked_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  block_reason VARCHAR(60) NOT NULL DEFAULT 'proposal_sla_breach',
  blocking_cid_ids TEXT NOT NULL COMMENT 'CSV of init_call.id values causing block',
  blocking_count TINYINT UNSIGNED NOT NULL DEFAULT 1,
  unblocked_at DATETIME DEFAULT NULL,
  unblocked_by VARCHAR(40) DEFAULT NULL COMMENT 'proposal_submitted | extension_granted | admin_override',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_bd_date (bd_uid, plan_date),
  KEY idx_blocked_at (blocked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026: every BD planner draft block event';

-- ---------------------------------------------------------------------
-- 3. LEAD QUERY CHECKLIST (Phase 2 - 1 Jul 2026)
-- Embedded in Lead Detail screen. Joint BD and CM ownership. Each query
-- has an owner, an SLA in hours, and a breach state.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lead_query_checklist (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id INT UNSIGNED NOT NULL COMMENT 'FK init_call.id',
  query_type ENUM(
    'school_visit_request',
    'documentation_check',
    'budget_clarification',
    'curriculum_alignment',
    'site_readiness',
    'principal_availability',
    'tender_doc',
    'csr_approval',
    'product_demo',
    'other'
  ) NOT NULL,
  query_text VARCHAR(500) NOT NULL,
  raised_by_uid INT UNSIGNED NOT NULL COMMENT 'Who flagged the query (BD or CM)',
  raised_by_role ENUM('bd','cm','principal','dm','other') NOT NULL,
  owner_uid INT UNSIGNED NOT NULL COMMENT 'Who must resolve',
  owner_role ENUM('bd','cm') NOT NULL,
  sla_hours TINYINT UNSIGNED NOT NULL DEFAULT 48,
  raised_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sla_deadline DATETIME NOT NULL,
  resolved_at DATETIME DEFAULT NULL,
  resolution_note VARCHAR(500) DEFAULT NULL,
  resolution_doc_url VARCHAR(500) DEFAULT NULL,
  status ENUM('open','in_progress','resolved','breached','dropped') NOT NULL DEFAULT 'open',
  breach_processed_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_cid_status (cid_id, status),
  KEY idx_owner_status (owner_uid, status),
  KEY idx_deadline (sla_deadline, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026 Phase 2: joint BD+CM query checklist per lead';

-- ---------------------------------------------------------------------
-- 4. LEAD QUERY BREACH LOG (Phase 2)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lead_query_breach_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  query_id INT UNSIGNED NOT NULL COMMENT 'FK lead_query_checklist.id',
  cid_id INT UNSIGNED NOT NULL,
  owner_uid INT UNSIGNED NOT NULL,
  owner_role ENUM('bd','cm') NOT NULL,
  breached_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  hours_overdue DECIMAL(6,2) NOT NULL,
  penalty_points INT NOT NULL DEFAULT 5,
  cm_scorecard_impacted TINYINT(1) NOT NULL DEFAULT 0,
  bd_grade_impacted TINYINT(1) NOT NULL DEFAULT 0,
  notified_to_rm TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_query (query_id),
  KEY idx_owner (owner_uid),
  KEY idx_breached_at (breached_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026 Phase 2: breach audit for query checklist';

-- ---------------------------------------------------------------------
-- 5. CM LEAD CALL LOG (Phase 2 - K17 direct call cadence)
-- Auto-populated from tblcallevents where actiontype_id=1 (call), 
-- caller is CM (user.type_id=13), duration_seconds > 120.
-- Plus manual entries via CM direct call logger UI.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cm_lead_call_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cm_uid INT UNSIGNED NOT NULL,
  cid_id INT UNSIGNED NOT NULL,
  bd_uid INT UNSIGNED NOT NULL COMMENT 'Lead owner BD for context',
  cstatus_at_call TINYINT UNSIGNED NOT NULL,
  call_at DATETIME NOT NULL,
  duration_seconds INT UNSIGNED NOT NULL,
  source ENUM('auto_tblcallevents','manual_logger','phone_sync') NOT NULL,
  source_event_id INT UNSIGNED DEFAULT NULL COMMENT 'FK tblcallevents.id when source=auto',
  call_purpose VARCHAR(200) DEFAULT NULL,
  next_action VARCHAR(200) DEFAULT NULL,
  notes_text TEXT DEFAULT NULL,
  iso_week CHAR(10) NOT NULL COMMENT 'YYYY-Www for cadence rollup',
  counted_for_k17 TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_cm_week (cm_uid, iso_week),
  KEY idx_cid_week (cid_id, iso_week),
  KEY idx_call_at (call_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026 Phase 2: CM K17 direct call cadence ledger';

-- ---------------------------------------------------------------------
-- 6. EMAIL AGENT DRAFT
-- AI-drafted emails awaiting BD review. GPT-4o-mini produces draft;
-- BD approves on mobile; send happens via BD Gmail OAuth.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_agent_draft (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id INT UNSIGNED NOT NULL,
  meeting_id INT UNSIGNED DEFAULT NULL COMMENT 'FK tblcallevents.id when post-meeting',
  bd_uid INT UNSIGNED NOT NULL COMMENT 'Sender',
  recipient_email VARCHAR(200) NOT NULL,
  recipient_name VARCHAR(200) NOT NULL,
  recipient_role ENUM('principal','dm','vp','director','admin','procurement','csr_head','other') NOT NULL,
  template_code VARCHAR(60) NOT NULL COMMENT 'FK email_template.code',
  trigger_reason ENUM(
    'post_tentative_meeting',
    'post_positive_meeting',
    'post_rp_meeting',
    'post_won_handover',
    'query_followup_visit',
    'query_followup_documents',
    'query_followup_budget',
    'query_followup_generic'
  ) NOT NULL,
  subject_line VARCHAR(300) NOT NULL,
  body_html MEDIUMTEXT NOT NULL,
  body_plain MEDIUMTEXT NOT NULL,
  ai_model VARCHAR(40) NOT NULL DEFAULT 'gpt-4o-mini',
  ai_tokens_used INT UNSIGNED DEFAULT NULL,
  ai_cost_usd DECIMAL(8,4) DEFAULT NULL,
  drafted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  bd_reviewed_at DATETIME DEFAULT NULL,
  bd_edits_made TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('drafted','approved','sent','failed','discarded','expired') NOT NULL DEFAULT 'drafted',
  expires_at DATETIME NOT NULL COMMENT 'drafted_at + 24h',
  send_attempted_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_bd_status (bd_uid, status),
  KEY idx_cid (cid_id),
  KEY idx_meeting (meeting_id),
  KEY idx_expires (expires_at, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026: AI-drafted emails awaiting BD review';

-- ---------------------------------------------------------------------
-- 7. EMAIL AGENT SEND LOG
-- One row per send attempt. Gmail API response saved.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_agent_send_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  draft_id INT UNSIGNED NOT NULL COMMENT 'FK email_agent_draft.id',
  bd_uid INT UNSIGNED NOT NULL,
  recipient_email VARCHAR(200) NOT NULL,
  cid_id INT UNSIGNED NOT NULL,
  send_attempt_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  send_status ENUM('sent','failed','retry','rate_limited','token_expired') NOT NULL,
  gmail_message_id VARCHAR(120) DEFAULT NULL,
  gmail_thread_id VARCHAR(120) DEFAULT NULL,
  http_status INT DEFAULT NULL,
  error_code VARCHAR(60) DEFAULT NULL,
  error_message VARCHAR(500) DEFAULT NULL,
  retry_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_draft (draft_id),
  KEY idx_bd_at (bd_uid, send_attempt_at),
  KEY idx_status (send_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026: Gmail API send attempts';

-- ---------------------------------------------------------------------
-- 8. BD GMAIL OAUTH TOKEN
-- Per-BD Gmail OAuth credentials. Refresh token persisted; access token
-- cached with expiry. gmail.send scope only (least privilege).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bd_gmail_oauth_token (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  bd_uid INT UNSIGNED NOT NULL,
  gmail_address VARCHAR(200) NOT NULL,
  refresh_token VARCHAR(500) NOT NULL,
  access_token VARCHAR(2000) DEFAULT NULL,
  access_token_expires_at DATETIME DEFAULT NULL,
  scope VARCHAR(200) NOT NULL DEFAULT 'https://www.googleapis.com/auth/gmail.send',
  consent_screen_version VARCHAR(20) NOT NULL DEFAULT 'v1',
  enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_used_at DATETIME DEFAULT NULL,
  revoked_at DATETIME DEFAULT NULL,
  revoke_reason VARCHAR(200) DEFAULT NULL,
  status ENUM('active','revoked','expired','error') NOT NULL DEFAULT 'active',
  PRIMARY KEY (id),
  UNIQUE KEY uk_bd (bd_uid),
  KEY idx_gmail (gmail_address),
  KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026: per-BD Gmail OAuth gmail.send scope';

-- ---------------------------------------------------------------------
-- 9. EMAIL TEMPLATE
-- Seeded with 8 templates. AI uses template body as scaffolding then
-- personalises with meeting context.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_template (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(60) NOT NULL COMMENT 'tentative_thanks, positive_thanks, rp_thanks, won_handover, query_followup_*',
  title VARCHAR(200) NOT NULL,
  trigger_event ENUM('post_meeting','query_raised','manual','scheduled') NOT NULL,
  meeting_cstatus_min TINYINT UNSIGNED DEFAULT NULL COMMENT 'Match meeting where cstatus_after >= this',
  meeting_cstatus_max TINYINT UNSIGNED DEFAULT NULL,
  subject_template VARCHAR(300) NOT NULL,
  body_html_template MEDIUMTEXT NOT NULL,
  body_plain_template MEDIUMTEXT NOT NULL,
  ai_persona_instructions TEXT DEFAULT NULL COMMENT 'Tone, style hints fed to GPT-4o-mini',
  variables_required TEXT DEFAULT NULL COMMENT 'CSV of variable names like principal_name, school_name',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_code (code),
  KEY idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026: email template library';

-- ---------------------------------------------------------------------
-- 10. LEAD NURTURE SCORE
-- Per-cid health snapshot computed daily by lead_followup_tracker_agent
-- (already exists for meetings, migration 025). This adds the proposal,
-- query, email signal.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lead_nurture_score (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id INT UNSIGNED NOT NULL,
  snapshot_date DATE NOT NULL,
  bd_uid INT UNSIGNED NOT NULL,
  cm_uid INT UNSIGNED DEFAULT NULL,
  cstatus_now TINYINT UNSIGNED NOT NULL,
  days_in_cstatus INT UNSIGNED NOT NULL,
  proposal_sla_state ENUM('na','open','submitted','breached','downgraded') NOT NULL DEFAULT 'na',
  proposal_hours_remaining DECIMAL(6,2) DEFAULT NULL,
  open_queries_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
  overdue_queries_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
  cm_calls_this_week TINYINT UNSIGNED NOT NULL DEFAULT 0,
  cm_call_gap_flag TINYINT(1) NOT NULL DEFAULT 0,
  thanks_email_sent_last_meeting TINYINT(1) NOT NULL DEFAULT 0,
  nurture_score TINYINT NOT NULL COMMENT '0 to 100',
  nurture_grade ENUM('A+','A','B','C','D') NOT NULL,
  top_concern VARCHAR(120) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_cid_date (cid_id, snapshot_date),
  KEY idx_bd_date (bd_uid, snapshot_date),
  KEY idx_grade (nurture_grade, snapshot_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Migration 026: daily lead nurture health snapshot';

-- =====================================================================
-- ALTER TABLES
-- =====================================================================

-- A) daily_planner: block flag when an SLA is breached
ALTER TABLE daily_planner
  ADD COLUMN blocked_by_proposal_sla_at DATETIME DEFAULT NULL
    COMMENT 'Migration 026: timestamp the BD planner draft was hard-blocked',
  ADD COLUMN blocking_cid_ids VARCHAR(200) DEFAULT NULL
    COMMENT 'Migration 026: CSV of init_call.id values causing block',
  ADD INDEX idx_blocked_proposal (blocked_by_proposal_sla_at);

-- B) line_manager_scorecard_daily: add K17 CM call gap
ALTER TABLE line_manager_scorecard_daily
  ADD COLUMN k17_cm_call_gap DECIMAL(5,2) DEFAULT NULL
    COMMENT 'Migration 026 Phase 2: percent of cstatus 6 and 7 leads in cluster where CM called fewer than 2 times this ISO week. Lower is better. Threshold: under 30 percent = healthy.';

-- C) init_call: verified DM email for thank-you sends
ALTER TABLE init_call
  ADD COLUMN dm_email_verified_at DATETIME DEFAULT NULL
    COMMENT 'Migration 026: BD confirmed DM email is valid (used as send-to address)';

-- =====================================================================
-- VIEWS
-- =====================================================================

-- View 1: Proposal SLA breaches surfacing now (consumed by 7:30 cron, planner block agent)
CREATE OR REPLACE VIEW v_proposal_sla_breach_today AS
SELECT
  p.id              AS sla_id,
  p.cid_id,
  ic.school_name,
  p.bd_uid,
  u.fname           AS bd_fname,
  u.lname           AS bd_lname,
  p.cm_uid,
  cm.fname          AS cm_fname,
  cm.lname          AS cm_lname,
  p.positive_at,
  p.sla_deadline,
  p.extension_used,
  p.status,
  TIMESTAMPDIFF(HOUR, p.sla_deadline, NOW()) AS hours_overdue
FROM proposal_sla_tracker p
JOIN init_call ic ON ic.id = p.cid_id
JOIN user u       ON u.uid = p.bd_uid
LEFT JOIN user cm ON cm.uid = p.cm_uid
WHERE p.status IN ('open','extended')
  AND p.sla_deadline <= NOW();

-- View 2: BDs currently blocked from drafting tomorrow plan
CREATE OR REPLACE VIEW v_bd_planner_blocked_now AS
SELECT
  bl.bd_uid,
  u.fname,
  u.lname,
  bl.plan_date,
  bl.blocked_at,
  bl.blocking_count,
  bl.blocking_cid_ids,
  TIMESTAMPDIFF(HOUR, bl.blocked_at, NOW()) AS hours_blocked
FROM bd_planner_block_log bl
JOIN user u ON u.uid = bl.bd_uid
WHERE bl.unblocked_at IS NULL
ORDER BY bl.blocked_at DESC;

-- View 3: Lead queries overdue (Phase 2)
CREATE OR REPLACE VIEW v_lead_query_overdue AS
SELECT
  q.id              AS query_id,
  q.cid_id,
  ic.school_name,
  q.query_type,
  q.query_text,
  q.owner_uid,
  q.owner_role,
  u.fname           AS owner_fname,
  u.lname           AS owner_lname,
  q.raised_at,
  q.sla_deadline,
  q.status,
  TIMESTAMPDIFF(HOUR, q.sla_deadline, NOW()) AS hours_overdue
FROM lead_query_checklist q
JOIN init_call ic ON ic.id = q.cid_id
JOIN user u       ON u.uid = q.owner_uid
WHERE q.status IN ('open','in_progress')
  AND q.sla_deadline <= NOW();

-- View 4: CM K17 call gap this ISO week (Phase 2)
-- For every cstatus 6 and 7 lead, count CM calls this week.
-- Healthy: >= 2 calls. Gap: 0 or 1 call.
CREATE OR REPLACE VIEW v_cm_call_gap_this_week AS
SELECT
  ic.id                                                    AS cid_id,
  ic.school_name,
  ic.mainbd                                                AS bd_uid,
  ic.cm_uid,
  ic.current_status_id                                     AS cstatus,
  DATE_FORMAT(CURDATE(), '%x-W%v')                         AS iso_week,
  COALESCE(cc.calls_this_week, 0)                          AS calls_this_week,
  CASE WHEN COALESCE(cc.calls_this_week, 0) < 2 THEN 1 ELSE 0 END AS gap_flag
FROM init_call ic
LEFT JOIN (
  SELECT
    cid_id,
    COUNT(*) AS calls_this_week
  FROM cm_lead_call_log
  WHERE iso_week = DATE_FORMAT(CURDATE(), '%x-W%v')
    AND counted_for_k17 = 1
  GROUP BY cid_id
) cc ON cc.cid_id = ic.id
WHERE ic.current_status_id IN (6, 7)
  AND ic.archived IS NULL;

-- View 5: Thank-you emails pending after a meeting
CREATE OR REPLACE VIEW v_email_thanks_pending AS
SELECT
  ev.id              AS meeting_id,
  ev.cid_id,
  ic.school_name,
  ev.user_id         AS bd_uid,
  u.fname,
  u.lname,
  ev.event_date      AS meeting_date,
  ev.event_time      AS meeting_time,
  ev.actiontype_id,
  ic.current_status_id AS cstatus,
  d.id               AS draft_id,
  d.status           AS draft_status,
  TIMESTAMPDIFF(HOUR, CONCAT(ev.event_date, ' ', ev.event_time), NOW()) AS hours_since_meeting
FROM tblcallevents ev
JOIN init_call ic ON ic.id = ev.cid_id
JOIN user u       ON u.uid = ev.user_id
LEFT JOIN email_agent_draft d 
  ON d.meeting_id = ev.id 
  AND d.trigger_reason IN ('post_tentative_meeting','post_positive_meeting','post_rp_meeting')
WHERE ev.actiontype_id IN (3,4)
  AND ev.event_date >= CURDATE() - INTERVAL 2 DAY
  AND (d.status IS NULL OR d.status NOT IN ('sent','discarded'));

-- =====================================================================
-- MIGRATION REGISTRY
-- =====================================================================
INSERT INTO schema_migrations (version, applied_at, notes)
VALUES (
  '026',
  NOW(),
  'CM Nurture Discipline + Proposal SLA + Email Agent. Phase 1 (BD-facing) 1 Jun 2026: proposal_sla_tracker, bd_planner_block_log, email_agent_*, bd_gmail_oauth_token, email_template, lead_nurture_score. Phase 2 (CM-facing) 1 Jul 2026: lead_query_checklist, lead_query_breach_log, cm_lead_call_log.'
)
ON DUPLICATE KEY UPDATE applied_at = NOW(), notes = VALUES(notes);

COMMIT;

-- =====================================================================
-- POST-DEPLOY VERIFICATION
-- =====================================================================
-- Run these manually after migration:
--   SELECT COUNT(*) FROM information_schema.tables 
--   WHERE table_schema = DATABASE() 
--     AND table_name IN (
--       'proposal_sla_tracker','bd_planner_block_log','lead_query_checklist',
--       'lead_query_breach_log','cm_lead_call_log','email_agent_draft',
--       'email_agent_send_log','bd_gmail_oauth_token','email_template',
--       'lead_nurture_score'
--     );
--   -- Expected: 10
--
--   SELECT COUNT(*) FROM information_schema.views
--   WHERE table_schema = DATABASE()
--     AND table_name IN (
--       'v_proposal_sla_breach_today','v_bd_planner_blocked_now',
--       'v_lead_query_overdue','v_cm_call_gap_this_week','v_email_thanks_pending'
--     );
--   -- Expected: 5
--
--   SHOW COLUMNS FROM daily_planner LIKE 'blocked_by_proposal_sla_at';
--   SHOW COLUMNS FROM line_manager_scorecard_daily LIKE 'k17_cm_call_gap';
--   SHOW COLUMNS FROM init_call LIKE 'dm_email_verified_at';
--
-- Probe endpoint expected behavior after deploy:
--   curl -sS -o /dev/null -w '%{http_code}' \
--     -H 'Authorization: Bearer <STEM_DIGEST_TOKEN>' \
--     'https://stemapp.in/api/proposal/sla/probe'
--   -- Expected: 200
