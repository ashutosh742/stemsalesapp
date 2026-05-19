-- =====================================================================
-- Migration 027 - 360 Communication Agent
-- =====================================================================
-- Produced 17 May 2026. Apply to staging on 25 May 2026.
-- Production phased: Phase 1 (1 Aug), Phase 2 (1 Sep), Phase 3 (1 Oct).
-- 
-- Depends on: migration 026 (Gmail OAuth + email_template + bd_gmail_oauth_token)
-- 
-- 6 new tables, 3 views, 1 ALTER on init_call.
-- 
-- Rollback: see stem_migration_027_deploy_runbook.md
-- =====================================================================

-- ---------------------------------------------------------------------
-- Table 1: comm_event_log
-- Every triggered event lands here. Orchestrator picks up status='new'
-- rows. After processing, status becomes 'drafted', 'capped', 'deduped',
-- or 'errored'.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comm_event_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_type VARCHAR(40) NOT NULL
    COMMENT 'call_unanswered, call_dropped, meeting_completed, proposal_sent, query_raised, query_resolved, stage_progressed, dormant_re_engage',
  cid_id INT UNSIGNED NOT NULL COMMENT 'init_call.cid_id',
  bd_uid INT UNSIGNED NOT NULL COMMENT 'user.uid of the BD owner',
  triggered_by_uid INT UNSIGNED NULL COMMENT 'who caused the trigger (BD for call, CM for query raise, etc)',
  source_table VARCHAR(60) NULL COMMENT 'tblcallevents, mom_data, lead_query_checklist, etc',
  source_row_id BIGINT UNSIGNED NULL COMMENT 'FK back to source row for audit',
  context_json JSON NULL COMMENT 'event-specific payload (call duration, meeting id, stakeholder hint, etc)',
  status ENUM('new','drafted','capped','deduped','errored','discarded','sent') NOT NULL DEFAULT 'new',
  status_reason VARCHAR(255) NULL,
  draft_id BIGINT UNSIGNED NULL COMMENT 'FK to comm_draft_queue.id if drafted',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at DATETIME NULL,
  PRIMARY KEY (id),
  INDEX idx_status_created (status, created_at),
  INDEX idx_cid (cid_id, created_at),
  INDEX idx_bd_status (bd_uid, status, created_at),
  INDEX idx_event_cid (event_type, cid_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 027 - every triggered comm event';

-- ---------------------------------------------------------------------
-- Table 2: comm_draft_queue
-- The actual AI-drafted email awaiting human review. One row per
-- approved event (after dedup + frequency cap). Status moves to 'sent'
-- when BD/CM/RM taps send, or 'discarded' if discarded.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comm_draft_queue (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to comm_event_log.id',
  cid_id INT UNSIGNED NOT NULL,
  owner_uid INT UNSIGNED NOT NULL COMMENT 'who reviews + sends (BD, CM, or RM)',
  owner_role ENUM('bd','cm','rm') NOT NULL,
  template_key VARCHAR(60) NOT NULL COMMENT 'FK to comm_template_v2.template_key',
  recipient_to_email VARCHAR(255) NOT NULL,
  recipient_to_name VARCHAR(200) NULL,
  recipient_to_role VARCHAR(40) NULL COMMENT 'primary_dm, secondary_dm, cfo_bursar, principal, trustee',
  recipient_cc_json JSON NULL COMMENT 'array of {email,name,role}',
  subject VARCHAR(255) NOT NULL,
  body_plain MEDIUMTEXT NOT NULL,
  body_html MEDIUMTEXT NULL,
  ai_model VARCHAR(40) NULL DEFAULT 'gpt-4o-mini',
  ai_prompt_tokens INT UNSIGNED NULL,
  ai_completion_tokens INT UNSIGNED NULL,
  ai_cost_usd DECIMAL(10,6) NULL,
  context_snapshot JSON NULL COMMENT 'lead + stakeholder context at draft time (for regen)',
  attachment_path VARCHAR(500) NULL COMMENT 'e.g. proposal PDF path for proposal_send_cover',
  status ENUM('pending_review','sent','discarded','expired','regenerated') NOT NULL DEFAULT 'pending_review',
  edit_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  regen_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  reviewed_at DATETIME NULL,
  sent_at DATETIME NULL,
  send_log_id BIGINT UNSIGNED NULL COMMENT 'FK to comm_send_log.id once sent',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_owner_status (owner_uid, status, created_at),
  INDEX idx_cid_status (cid_id, status),
  INDEX idx_event (event_id),
  INDEX idx_template (template_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 027 - drafts awaiting human review';

-- ---------------------------------------------------------------------
-- Table 3: comm_send_log
-- Successful sends only. Mirrors migration 026 email_agent_send_log
-- but covers all 8 event types. Drives the comm timeline UI.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comm_send_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  draft_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to comm_draft_queue.id',
  event_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to comm_event_log.id',
  cid_id INT UNSIGNED NOT NULL,
  sender_uid INT UNSIGNED NOT NULL,
  sender_role ENUM('bd','cm','rm') NOT NULL,
  sender_gmail_address VARCHAR(255) NOT NULL,
  recipient_to_email VARCHAR(255) NOT NULL,
  recipient_cc_json JSON NULL,
  subject VARCHAR(255) NOT NULL,
  body_plain_hash CHAR(40) NULL COMMENT 'SHA1 of body for dedup audit; full body in draft_queue',
  template_key VARCHAR(60) NOT NULL,
  event_type VARCHAR(40) NOT NULL,
  gmail_message_id VARCHAR(120) NULL COMMENT 'returned by Gmail API',
  gmail_thread_id VARCHAR(120) NULL,
  send_status ENUM('sent','failed','bounced','retried') NOT NULL DEFAULT 'sent',
  send_error_message VARCHAR(500) NULL,
  sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  retry_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  INDEX idx_cid_sent (cid_id, sent_at),
  INDEX idx_sender (sender_uid, sent_at),
  INDEX idx_event (event_type, sent_at),
  INDEX idx_template (template_key, sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 027 - audit log of every sent communication';

-- ---------------------------------------------------------------------
-- Table 4: comm_frequency_cap
-- Per-stakeholder send count tracking for the rolling 7-day window.
-- Refreshed by daily 23:00 IST cleanup job.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comm_frequency_cap (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  stakeholder_email VARCHAR(255) NOT NULL,
  cid_id INT UNSIGNED NULL COMMENT 'NULL when global per-email tracking',
  sends_today TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'reset at 00:00 IST',
  sends_rolling_7d TINYINT UNSIGNED NOT NULL DEFAULT 0,
  last_send_at DATETIME NULL,
  last_event_type VARCHAR(40) NULL,
  capped_until DATETIME NULL COMMENT 'if set, no comms until this time',
  override_flag TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'CM/admin can override cap',
  override_reason VARCHAR(255) NULL,
  override_by_uid INT UNSIGNED NULL,
  override_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email_cid (stakeholder_email, cid_id),
  INDEX idx_capped (capped_until),
  INDEX idx_last_send (last_send_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 027 - frequency cap state per stakeholder + lead';

-- ---------------------------------------------------------------------
-- Table 5: comm_template_v2
-- 15 templates: 8 inherited from migration 026 email_template
-- (via inherits_from key) + 7 net-new templates for events 9-15.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comm_template_v2 (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  template_key VARCHAR(60) NOT NULL,
  event_type VARCHAR(40) NOT NULL,
  inherits_from VARCHAR(60) NULL COMMENT 'if set, source row in email_template (migration 026)',
  subject_template VARCHAR(255) NOT NULL,
  body_plain_template MEDIUMTEXT NOT NULL,
  body_html_template MEDIUMTEXT NULL,
  ai_persona_instructions TEXT NULL,
  required_context_fields JSON NULL COMMENT 'array of keys this template needs',
  recipient_to_role VARCHAR(40) NOT NULL DEFAULT 'primary_dm',
  recipient_cc_roles JSON NULL COMMENT 'array like ["principal","cfo_bursar"]',
  applicable_cstatus_min TINYINT NULL COMMENT 'restrict to leads at this stage or higher',
  applicable_cstatus_max TINYINT NULL,
  max_words SMALLINT UNSIGNED NOT NULL DEFAULT 120,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_template_key (template_key),
  INDEX idx_event (event_type, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 027 - communication templates (extends 026 email_template)';

-- ---------------------------------------------------------------------
-- Table 6: stakeholder_contact_book
-- Per-lead roster of up to 5 named contacts at the client side.
-- Drives the recipient resolution at event time.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS stakeholder_contact_book (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id INT UNSIGNED NOT NULL,
  contact_role ENUM('primary_dm','secondary_dm','cfo_bursar','principal','trustee') NOT NULL,
  contact_name VARCHAR(200) NOT NULL,
  contact_designation VARCHAR(120) NULL,
  contact_email VARCHAR(255) NOT NULL,
  contact_phone VARCHAR(40) NULL,
  email_verified_at DATETIME NULL COMMENT 'set after first successful send or manual verify',
  email_bounced_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
  source ENUM('init_call','mom_v2','linkedin_csr','manual_entry') NOT NULL,
  added_by_uid INT UNSIGNED NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  notes VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cid_role_email (cid_id, contact_role, contact_email),
  INDEX idx_cid_role (cid_id, contact_role, is_active),
  INDEX idx_email (contact_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 027 - stakeholder roster per lead';

-- ---------------------------------------------------------------------
-- ALTER existing init_call to track principal_email separately
-- (existing email_id is the primary DM, principal is often different)
-- ---------------------------------------------------------------------
ALTER TABLE init_call
  ADD COLUMN principal_email VARCHAR(255) NULL
    COMMENT 'Migration 027 - principal/headmaster email, distinct from primary DM email_id'
    AFTER email_id,
  ADD COLUMN principal_name VARCHAR(200) NULL
    COMMENT 'Migration 027 - principal full name'
    AFTER principal_email,
  ADD COLUMN comm_book_initialised_at DATETIME NULL
    COMMENT 'Migration 027 - when stakeholder_contact_book was first seeded for this lead'
    AFTER principal_name;

-- ---------------------------------------------------------------------
-- View 1: v_comm_pending_for_bd
-- Drafts pending review per BD with lead context.
-- ---------------------------------------------------------------------
CREATE OR REPLACE VIEW v_comm_pending_for_bd AS
SELECT
  d.id AS draft_id,
  d.owner_uid AS bd_uid,
  d.cid_id,
  d.template_key,
  d.recipient_to_email,
  d.recipient_to_name,
  d.recipient_to_role,
  d.subject,
  d.created_at AS drafted_at,
  e.event_type,
  e.context_json,
  ic.companyname,
  ic.school_name,
  ic.cstatus,
  TIMESTAMPDIFF(MINUTE, d.created_at, NOW()) AS age_minutes
FROM comm_draft_queue d
JOIN comm_event_log e ON e.id = d.event_id
LEFT JOIN init_call ic ON ic.cid_id = d.cid_id
WHERE d.status = 'pending_review'
  AND d.owner_role = 'bd'
ORDER BY d.created_at DESC;

-- ---------------------------------------------------------------------
-- View 2: v_comm_coverage_today
-- Per BD: how many triggerable events fired today vs how many produced
-- a sent comm. Coverage percent feeds the 7:30 audit.
-- ---------------------------------------------------------------------
CREATE OR REPLACE VIEW v_comm_coverage_today AS
SELECT
  e.bd_uid,
  u.fname AS bd_first_name,
  u.lname AS bd_last_name,
  COUNT(*) AS events_triggered,
  SUM(CASE WHEN e.status = 'sent' THEN 1 ELSE 0 END) AS comms_sent,
  SUM(CASE WHEN e.status = 'discarded' THEN 1 ELSE 0 END) AS comms_discarded,
  SUM(CASE WHEN e.status IN ('drafted','new') THEN 1 ELSE 0 END) AS comms_pending,
  SUM(CASE WHEN e.status = 'capped' THEN 1 ELSE 0 END) AS comms_capped,
  ROUND(
    100.0 * SUM(CASE WHEN e.status = 'sent' THEN 1 ELSE 0 END)
    / NULLIF(SUM(CASE WHEN e.status IN ('sent','discarded','drafted','new','capped') THEN 1 ELSE 0 END), 0),
    1
  ) AS coverage_percent
FROM comm_event_log e
LEFT JOIN user u ON u.uid = e.bd_uid
WHERE DATE(e.created_at) = CURDATE()
GROUP BY e.bd_uid, u.fname, u.lname;

-- ---------------------------------------------------------------------
-- View 3: v_comm_frequency_status
-- Stakeholders near the 4/week cap. Used by orchestrator to short-circuit
-- and by audit cron to warn line managers.
-- ---------------------------------------------------------------------
CREATE OR REPLACE VIEW v_comm_frequency_status AS
SELECT
  c.stakeholder_email,
  c.cid_id,
  c.sends_today,
  c.sends_rolling_7d,
  c.last_send_at,
  c.last_event_type,
  c.capped_until,
  CASE
    WHEN c.sends_rolling_7d >= 4 THEN 'capped'
    WHEN c.sends_rolling_7d >= 3 THEN 'near_cap'
    WHEN c.sends_today >= 1 THEN 'daily_hit'
    ELSE 'open'
  END AS status_band,
  ic.companyname,
  ic.school_name
FROM comm_frequency_cap c
LEFT JOIN init_call ic ON ic.cid_id = c.cid_id
ORDER BY c.sends_rolling_7d DESC, c.sends_today DESC;

-- ---------------------------------------------------------------------
-- Probe row in config_setting so the controller can answer the probe
-- ---------------------------------------------------------------------
INSERT IGNORE INTO config_setting (config_key, config_value, description)
VALUES
  ('m027_enabled', '1', 'Migration 027 master kill-switch'),
  ('m027_phase', '1', 'Active phase 1/2/3 - controls which events fire'),
  ('m027_pilot_uids', '42,43,44,45,46', 'BD pilot scope; CM Anjali 12 separate'),
  ('m027_frequency_cap_daily', '1', 'Max auto-drafts per stakeholder per day'),
  ('m027_frequency_cap_weekly', '4', 'Max auto-drafts per stakeholder per rolling 7d'),
  ('m027_orchestrator_run_interval_seconds', '300', 'Failover cron interval');

-- =====================================================================
-- End of migration 027 schema.
-- =====================================================================
