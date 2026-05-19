-- ============================================================================
-- STEM CRM - Migration 032
-- Anaya Ask: Conversational Natural-Language Query Agent
-- ============================================================================
-- Scope: staging only (stemapp.in). Never run on production.
-- Pilot:  Mon 25 May 2026 (Mumbai cluster: uids 42-46, CM uid 12)
-- Org:    Mon 1 Jun 2026
--
-- Tables added:
--   ask_session           - one row per conversation session
--   ask_message           - one row per turn (user question or assistant answer)
--   ask_audit_log         - immutable log of every query attempt (allowed + denied)
--   safe_query_allowlist  - seed table of permitted tables and columns
--
-- Views added:
--   v_ask_usage_today     - per-user query counts for today
--   v_ask_denied_today    - denied queries today (for monitoring)
--
-- The read-only DB user grant is in the deploy runbook (Section 1).
-- No existing table is modified by this migration.
--
-- Author: STEM ops
-- Date: 2026-05-20
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. ASK SESSION
-- One row per conversation thread. A user starts a session; messages belong to it.
-- Sessions inactive over 2 hours are considered closed (app enforces this).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ask_session (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  uid             INT UNSIGNED NOT NULL    COMMENT 'user.uid who owns this session',
  role            ENUM('bd','cm','rm','director') NOT NULL
                  COMMENT 'role at session start - copied from user record',
  cluster_id      INT UNSIGNED DEFAULT NULL COMMENT 'cluster if role=bd or cm',
  region_id       INT UNSIGNED DEFAULT NULL COMMENT 'region if role=rm',
  message_count   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  last_active_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                  ON UPDATE CURRENT_TIMESTAMP,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_uid_created (uid, created_at),
  KEY idx_last_active (last_active_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 032: one row per Anaya Ask conversation session';

-- ----------------------------------------------------------------------------
-- 2. ASK MESSAGE
-- One row per turn. role=user for the human question, role=assistant for Anaya.
-- sql_generated and rows_returned are NULL for user turns.
-- latency_ms is the LLM + SQL round trip for assistant turns (NULL for user turns).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ask_message (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  session_id      INT UNSIGNED NOT NULL    COMMENT 'ask_session.id',
  role            ENUM('user','assistant') NOT NULL,
  text            TEXT NOT NULL            COMMENT 'plain-text content of the turn',
  sql_generated   TEXT DEFAULT NULL        COMMENT 'sanitized SQL if this turn ran a query',
  rows_returned   SMALLINT UNSIGNED DEFAULT NULL,
  latency_ms      SMALLINT UNSIGNED DEFAULT NULL,
  feedback        ENUM('good','bad') DEFAULT NULL
                  COMMENT 'thumbs up/down from user; NULL until rated',
  denied          TINYINT(1) NOT NULL DEFAULT 0
                  COMMENT '1 if the guard denied the SQL for this turn',
  denied_reason   VARCHAR(255) DEFAULT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_session  (session_id, created_at),
  KEY idx_feedback (feedback, created_at),
  KEY idx_denied   (denied, created_at),
  CONSTRAINT fk_ask_message_session
    FOREIGN KEY (session_id) REFERENCES ask_session(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 032: individual message turns within an Ask session';

-- ----------------------------------------------------------------------------
-- 3. ASK AUDIT LOG
-- Immutable. Written for every query attempt, allowed or denied.
-- sql_executed is NULL when denied. denied_reason is NULL when allowed.
-- No DELETE endpoint exists for this table.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ask_audit_log (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  uid             INT UNSIGNED NOT NULL,
  role            ENUM('bd','cm','rm','director') NOT NULL,
  session_id      INT UNSIGNED DEFAULT NULL,
  message_id      INT UNSIGNED DEFAULT NULL,
  query_text      TEXT NOT NULL            COMMENT 'original plain-English question',
  sql_executed    TEXT DEFAULT NULL        COMMENT 'sanitized SQL that ran; NULL if denied',
  denied_reason   VARCHAR(255) DEFAULT NULL COMMENT 'guard rejection reason; NULL if allowed',
  rows_returned   SMALLINT UNSIGNED DEFAULT NULL,
  latency_ms      SMALLINT UNSIGNED DEFAULT NULL,
  executed_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_uid_date    (uid, executed_at),
  KEY idx_denied_date (denied_reason, executed_at),
  KEY idx_date        (executed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 032: immutable audit log of every Ask query attempt';

-- ----------------------------------------------------------------------------
-- 4. SAFE QUERY ALLOWLIST
-- Seed table. The guard reads this at startup (cached for 5 minutes).
-- table_name: the MySQL table or view name.
-- allowed_columns: comma-separated list, or '*' to allow all columns.
--   Column restrictions are advisory for the prompt; the guard does not
--   parse column lists from SELECT - only table names are hard-blocked.
-- active: set to 0 to temporarily remove a table without deleting the row.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS safe_query_allowlist (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  table_name      VARCHAR(128) NOT NULL,
  allowed_columns TEXT NOT NULL DEFAULT '*'
                  COMMENT 'comma-separated column list or * for all',
  is_view         TINYINT(1) NOT NULL DEFAULT 0,
  notes           VARCHAR(255) DEFAULT NULL,
  active          TINYINT(1) NOT NULL DEFAULT 1,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_table (table_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 032: whitelisted tables and columns for Anaya Ask queries';

-- Seed: base CRM tables
INSERT INTO safe_query_allowlist (table_name, allowed_columns, is_view, notes) VALUES
('init_call',
 'id,compny_nm,mainbd,cstatus,fbudget,cluster_id,region_id,created_at,updated_at',
 0, 'Core lead/CID table'),
('tblcallevents',
 'id,cid_id,bd_uid,cm_uid,actiontype_id,purpose_id,event_date,has_mom,has_photo,has_gps,has_completion,created_at',
 0, 'Call and visit events against CIDs'),
('lead_progression_log',
 'id,cid_id,bd_uid,from_stage,to_stage,changed_by_uid,change_source,created_at',
 0, 'Stage progression history'),
('daily_planner',
 'id,bd_uid,plan_date,cid_id,actiontype_id,purpose_id,status,created_at',
 0, 'BD daily task planner'),
('user',
 'uid,firstName,lastName,type_id,email,cluster_id,region_id,active',
 0, 'User directory - no password columns'),
('mom_data',
 'id,event_id,cid_id,bd_uid,summary,created_at',
 0, 'Minutes of meeting records'),
('reporting_hierarchy',
 'id,employee_uid,parent_uid,skip_parent_uid,active',
 0, 'BD to CM to RM chain'),
('funnel_change_log',
 'id,cid_id,bd_uid,cm_uid,rm_uid,from_cstatus,to_cstatus,changed_by_uid,change_source,fbudget_rs,closed_value_rs,created_at',
 0, 'Every cstatus transition (migration 024)'),
('weekly_touch_gap',
 'id,cid_id,bd_uid,cm_uid,cstatus,last_task_date,days_since_last_task,detected_at,resolved',
 0, 'CIDs with no touch in 7 days (migration 024)'),
('no_purpose_task_log',
 'id,event_id,cid_id,bd_uid,cm_uid,actiontype_id,event_date,detected_at,resolved',
 0, 'Tasks without purpose_id (migration 024)'),
('phantom_task_log',
 'id,event_id,cid_id,bd_uid,cm_uid,planned_date,days_since_planned,detected_at,resolved',
 0, 'Planned tasks with no evidence (migration 024)'),
('stagnancy_22_log',
 'id,cid_id,bd_uid,cm_uid,rm_uid,cstatus,task_count,days_in_cstatus,detected_at,resolved',
 0, 'CIDs stagnant over 22 tasks (migration 024)'),
('dm_verification',
 'id,cid_id,bd_uid,dm_name,dm_designation,combined_score,verdict,verdict_at,created_at',
 0, 'DM verification status - no email or phone columns'),
('line_manager_scorecard',
 'id,manager_uid,week_start,day_score,grade,k8_funnel_hygiene_pct,k8_stagnant_22_count,k8_weekly_gap_count,k8_no_purpose_count,k8_phantom_count,incentive_deduction_rs',
 0, 'CM/RM scorecard (migrations 022-024)'),
('quarter_config',
 'id,fiscal_year,quarter_number,k1_weight,k2_weight,k3_weight,k4_weight,k5_weight,k6_weight,k7_weight,k8_weight',
 0, 'Quarter weight config'),
('proposal_sla_log',
 'id,cid_id,bd_uid,cm_uid,proposal_due_date,proposal_sent_at,sla_met,created_at',
 0, 'Proposal SLA tracking (migration 026)'),
('upstream_hygiene_state',
 'id,cid_id,bd_uid,cstatus,days_stagnant,flag,wallet_debit_rs,detected_at',
 0, 'Upstream hygiene state (migration 028)'),
('incentive_deduction',
 'id,manager_uid,manager_role,breach_type,cid_id,bd_uid,deduction_rs,week_start,week_end,created_at',
 0, 'Manager incentive deductions (migration 024)');

-- Seed: views
INSERT INTO safe_query_allowlist (table_name, allowed_columns, is_view, notes) VALUES
('v_funnel_changes_yesterday', '*', 1, 'View from migration 024'),
('v_cm_hygiene_inbox',         '*', 1, 'View from migration 024'),
('v_bd_hygiene_breaches_week', '*', 1, 'View from migration 024'),
('v_k8_per_manager_week',      '*', 1, 'View from migration 024'),
('v_dm_doubtful',              '*', 1, 'View from migration 024'),
('v_ask_usage_today',          '*', 1, 'View from migration 032'),
('v_ask_denied_today',         '*', 1, 'View from migration 032');

-- ----------------------------------------------------------------------------
-- 5. VIEW: v_ask_usage_today
-- Per-user count of Ask queries fired today. Used by the quota chip in the UI
-- and by the admin usage endpoint.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_ask_usage_today AS
SELECT
  a.uid,
  u.firstName                                  AS user_name,
  u.type_id,
  COUNT(*)                                     AS query_count,
  SUM(CASE WHEN a.denied_reason IS NULL THEN 1 ELSE 0 END) AS allowed_count,
  SUM(CASE WHEN a.denied_reason IS NOT NULL THEN 1 ELSE 0 END) AS denied_count,
  MAX(a.executed_at)                           AS last_query_at
FROM ask_audit_log a
LEFT JOIN user u ON u.uid = a.uid
WHERE DATE(a.executed_at) = CURDATE()
GROUP BY a.uid, u.firstName, u.type_id;

-- ----------------------------------------------------------------------------
-- 6. VIEW: v_ask_denied_today
-- Denied queries today with guard reason. Used for monitoring and alert crons.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_ask_denied_today AS
SELECT
  a.id,
  a.uid,
  u.firstName  AS user_name,
  a.role,
  a.query_text,
  a.denied_reason,
  a.executed_at
FROM ask_audit_log a
LEFT JOIN user u ON u.uid = a.uid
WHERE DATE(a.executed_at) = CURDATE()
  AND a.denied_reason IS NOT NULL
ORDER BY a.executed_at DESC;

-- ============================================================================
-- END OF MIGRATION 032
-- ============================================================================
