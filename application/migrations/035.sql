-- ============================================================================
-- STEM CRM - Migration 035
-- Daily Rhythm Standardisation
-- ============================================================================
-- Scope: all active BDs and their CMs/RMs.
--        Pilot uids 42,43,44,45,46,12 from 25 May 2026 (placeholder - reconcile
--        against Sales-Team-Reporting.xlsx before deploy; real Mumbai team is
--        Samir, Smit, Sumeet, Reya, Najmaben, Reshmi, Kishan, Harsha, Neha,
--        ACM Sadanad Shetty).
--        Org rollout 1 Jun 2026.
-- Naming: snake_case, idx_ prefix for indexes, fk_ prefix for foreign keys.
-- Engine: InnoDB. Charset: utf8mb4. DATETIME cols in UTC; app layer converts
--         to IST (UTC+5:30) for display.
-- Re-runnable: CREATE TABLE IF NOT EXISTS, INSERT IGNORE for seeds.
-- Deploy target: staging only (stemapp.in). NEVER run on prod.
-- Author: STEM ops
-- Date: 2026-05-20
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. FEATURE FLAG EXTENSION
-- Adds rhythm_035_enabled to the existing feature_flag table (first created
-- in migration 022). Values: 0=off, 1=pilot, 2=org-wide.
-- ----------------------------------------------------------------------------
ALTER TABLE feature_flag
  ADD COLUMN IF NOT EXISTS rhythm_035_enabled TINYINT UNSIGNED NOT NULL DEFAULT 0
    COMMENT '0=off, 1=pilot 25 May 2026, 2=org rollout 1 Jun 2026';

-- Pilot uid overrides via feature_flag_override (table from migration 030).
-- Placeholder uids - reconcile against real roster before deploy.
INSERT IGNORE INTO feature_flag_override (uid, flag_name, flag_value, set_by_uid)
VALUES
  (42, 'rhythm_035_enabled', 1, 1),
  (43, 'rhythm_035_enabled', 1, 1),
  (44, 'rhythm_035_enabled', 1, 1),
  (45, 'rhythm_035_enabled', 1, 1),
  (46, 'rhythm_035_enabled', 1, 1),
  (12, 'rhythm_035_enabled', 1, 1);

-- ----------------------------------------------------------------------------
-- 2. DAILY RHYTHM TOUCHPOINT
-- Static lookup. Five rows, one per fixed weekday touchpoint.
-- cron_id references the entry in the cron registry spreadsheet.
-- status_template JSON stores: grace_minutes, owner_roles[], red_flags_evaluated[].
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS daily_rhythm_touchpoint (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  touchpoint_code      VARCHAR(32)  NOT NULL  COMMENT 'morning_brief|daily_huddle|midday_pulse|day_close|evening_review',
  touchpoint_name      VARCHAR(128) NOT NULL,
  scheduled_time_ist   TIME         NOT NULL  COMMENT 'scheduled fire time in IST',
  audience_scope       ENUM('sc','cm','rm','all') NOT NULL DEFAULT 'all',
  cron_id              VARCHAR(16)  DEFAULT NULL COMMENT 'cron registry id, e.g. 77b08026',
  cron_action          ENUM('extend','new') NOT NULL DEFAULT 'new',
  status_template      JSON         DEFAULT NULL COMMENT 'grace_minutes, owner_roles, red_flags_evaluated',
  active               TINYINT(1)   NOT NULL DEFAULT 1,
  created_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_touchpoint_code (touchpoint_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed: 5 touchpoints
INSERT IGNORE INTO daily_rhythm_touchpoint
  (touchpoint_code, touchpoint_name, scheduled_time_ist, audience_scope,
   cron_id, cron_action, status_template)
VALUES
  ('morning_brief',  '07:00 IST Morning Brief',
   '07:00:00', 'all', '77b08026', 'extend',
   '{"grace_minutes":30,"owner_roles":["SC","CM"],"red_flags_evaluated":[2,3,14]}'),

  ('daily_huddle',   '09:30 IST Daily Huddle',
   '09:30:00', 'all', NULL, 'new',
   '{"grace_minutes":60,"owner_roles":["SC","CM"],"red_flags_evaluated":[6,7]}'),

  ('midday_pulse',   '12:30 IST Mid-day Pulse',
   '12:30:00', 'sc',  NULL, 'new',
   '{"grace_minutes":30,"owner_roles":["SC"],"red_flags_evaluated":[1,3]}'),

  ('day_close',      '18:30 IST BD Day Close',
   '18:30:00', 'all', '0c647bbd', 'extend',
   '{"grace_minutes":30,"owner_roles":["SC","CM"],"red_flags_evaluated":[1,2,3,5,8]}'),

  ('evening_review', '19:30 IST Evening Review',
   '19:30:00', 'cm',  NULL, 'new',
   '{"grace_minutes":60,"owner_roles":["CM","RM"],"red_flags_evaluated":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]}');

-- ----------------------------------------------------------------------------
-- 3. DAILY RHYTHM CHECKPOINT
-- One row per touchpoint per responsible user per weekday.
-- Created by rhythm_orchestrator at the scheduled time.
-- Marked done when the owner completes the action via POST /api/rhythm/checkpoint/{id}/ack.
-- Becomes missed if still pending after grace_minutes elapses.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS daily_rhythm_checkpoint (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  touchpoint_id  INT UNSIGNED NOT NULL  COMMENT 'daily_rhythm_touchpoint.id',
  owner_role     ENUM('SC','CM','RM')   NOT NULL,
  owner_uid      INT UNSIGNED NOT NULL  COMMENT 'user.uid',
  cluster_id     INT UNSIGNED DEFAULT NULL,
  planned_at     DATETIME     NOT NULL  COMMENT 'exact scheduled datetime in UTC',
  completed_at   DATETIME     DEFAULT NULL,
  status         ENUM('pending','done','missed') NOT NULL DEFAULT 'pending',
  notes          TEXT         DEFAULT NULL COMMENT 'JSON payload from orchestrator or free text from owner',
  created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_touchpoint_date (touchpoint_id, planned_at),
  KEY idx_owner_date (owner_uid, planned_at),
  KEY idx_status_date (status, planned_at),
  KEY idx_cluster_date (cluster_id, planned_at),
  CONSTRAINT fk_checkpoint_touchpoint FOREIGN KEY (touchpoint_id)
    REFERENCES daily_rhythm_touchpoint (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 4. RED FLAG DEFINITION
-- 15 standardised red flags. Seeded at deploy time.
-- trigger_sql contains a SELECT expression evaluated by rhythm_orchestrator.
-- owner_role: who is accountable for resolving within resolve_hours.
-- escalates_to_role: pipe-separated if multiple (e.g. CM|AO).
-- active: 0 to disable a flag without deleting the seed row.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS red_flag_definition (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  flag_code           VARCHAR(64)  NOT NULL  COMMENT 'short machine key',
  flag_title          VARCHAR(255) NOT NULL  COMMENT 'human-readable label',
  trigger_sql         TEXT         DEFAULT NULL COMMENT 'SELECT returning target_user_uid, target_lead_id rows',
  owner_role          ENUM('SC','CM','RM','AO') NOT NULL,
  severity            ENUM('red','amber','info') NOT NULL,
  resolve_hours       SMALLINT UNSIGNED NOT NULL DEFAULT 24,
  escalates_to_role   VARCHAR(32)  NOT NULL  COMMENT 'SC|CM|RM|AO|Director or pipe-separated combo',
  active              TINYINT(1)   NOT NULL DEFAULT 1,
  created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_flag_code (flag_code),
  KEY idx_severity (severity),
  KEY idx_owner_role (owner_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed: 15 red flags (trigger_sql values are placeholders confirmed by DBA before pilot)
INSERT IGNORE INTO red_flag_definition
  (flag_code, flag_title, trigger_sql, owner_role, severity, resolve_hours, escalates_to_role)
VALUES
  ('zero_rp_24h',
   'Zero-prospecting BD over 24h',
   'SELECT ic.mainbd AS target_user_uid, ic.id AS target_lead_id FROM init_call ic WHERE ic.cstatus BETWEEN 3 AND 9 AND ic.mainbd NOT IN (SELECT DISTINCT user_id FROM tblcallevents WHERE purpose_id IN (SELECT id FROM tblpurpose WHERE purpose_name LIKE ''%RP%'') AND event_date >= DATE_SUB(CURDATE(), INTERVAL 1 DAY))',
   'SC', 'red', 4, 'CM'),

  ('planner_late',
   'Planner submitted late',
   'SELECT user_id AS target_user_uid, NULL AS target_lead_id FROM tblcallevents WHERE event_date = CURDATE() AND submitted_at > CONCAT(CURDATE(), '' 18:30:00'') AND submitted_at IS NOT NULL',
   'SC', 'amber', 1, 'CM'),

  ('missing_tomorrow_planner',
   'Missing tomorrow planner',
   'SELECT u.uid AS target_user_uid, NULL AS target_lead_id FROM user u WHERE u.role = ''BD'' AND u.uid NOT IN (SELECT DISTINCT user_id FROM tblcallevents WHERE event_date = CURDATE() + INTERVAL 1 DAY) AND u.active = 1',
   'SC', 'red', 2, 'CM'),

  ('cm_approval_sla',
   'CM approval SLA breach',
   'SELECT dm.cm_uid AS target_user_uid, NULL AS target_lead_id FROM daily_huddle_mom dm WHERE dm.cm_signed_at IS NULL AND TIMESTAMPDIFF(HOUR, dm.drafted_at, NOW()) > 1 AND dm.huddle_date = CURDATE()',
   'CM', 'red', 1, 'RM'),

  ('same_day_plan_red',
   'Same-day planning RED',
   'SELECT drc.owner_uid AS target_user_uid, NULL AS target_lead_id FROM daily_rhythm_checkpoint drc INNER JOIN daily_rhythm_touchpoint drt ON drt.id = drc.touchpoint_id WHERE drt.touchpoint_code = ''day_close'' AND drc.status = ''missed'' AND DATE(drc.planned_at) = CURDATE()',
   'CM', 'red', 0, 'RM'),

  ('mom_unwritten_24h',
   'MoM unwritten over 24h',
   'SELECT drc.owner_uid AS target_user_uid, NULL AS target_lead_id FROM daily_rhythm_checkpoint drc INNER JOIN daily_rhythm_touchpoint drt ON drt.id = drc.touchpoint_id WHERE drt.touchpoint_code = ''daily_huddle'' AND drc.status = ''pending'' AND TIMESTAMPDIFF(HOUR, drc.planned_at, NOW()) > 24',
   'SC', 'amber', 6, 'CM'),

  ('mom_pending_cm_24h',
   'MoM pending CM approval over 24h',
   'SELECT dhm.cm_uid AS target_user_uid, NULL AS target_lead_id FROM daily_huddle_mom dhm WHERE dhm.cm_signed_at IS NULL AND dhm.drafted_at IS NOT NULL AND TIMESTAMPDIFF(HOUR, dhm.drafted_at, NOW()) > 24',
   'CM', 'amber', 4, 'RM'),

  ('variance_over_20pct',
   'Variance over 20 percent',
   'SELECT ic.mainbd AS target_user_uid, ic.id AS target_lead_id FROM init_call ic WHERE ic.cstatus BETWEEN 6 AND 9 AND ic.fbudget > 0 AND ABS(ic.fbudget - ic.actual_value) / ic.fbudget > 0.20',
   'CM', 'amber', 12, 'AO|RM'),

  ('unreturned_advance',
   'Unreturned advance',
   'SELECT user_id AS target_user_uid, NULL AS target_lead_id FROM advance_request WHERE status = ''disbursed'' AND DATEDIFF(CURDATE(), disbursed_date) > 24 AND returned_at IS NULL',
   'SC', 'red', 24, 'CM|AO'),

  ('stuck_dual_approval',
   'Stuck dual approval over 12h',
   'SELECT initiated_by AS target_user_uid, ref_id AS target_lead_id FROM dual_approval_queue WHERE status = ''pending'' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 12',
   'AO', 'red', 2, 'RM'),

  ('band_violation',
   'Band violation',
   'SELECT ic.mainbd AS target_user_uid, ic.id AS target_lead_id FROM init_call ic WHERE ic.fbudget > 0 AND ic.fbudget > (SELECT max_band FROM band_config WHERE role = ''BD'' LIMIT 1)',
   'SC', 'info', 24, 'CM'),

  ('stale_tentative_5d',
   'Stale tentative over 5 days',
   'SELECT ic.mainbd AS target_user_uid, ic.id AS target_lead_id FROM init_call ic WHERE ic.cstatus = 3 AND DATEDIFF(CURDATE(), ic.cstatus_updated_at) > 5',
   'CM', 'info', 48, 'RM'),

  ('psu_stale_touch_14d',
   'PSU stale touch over 14 days',
   'SELECT ic.mainbd AS target_user_uid, ic.id AS target_lead_id FROM init_call ic WHERE ic.school_type = ''PSU'' AND ic.cstatus BETWEEN 3 AND 9 AND (SELECT MAX(t.event_date) FROM tblcallevents t WHERE t.cid_id = ic.id) < DATE_SUB(CURDATE(), INTERVAL 14 DAY)',
   'RM', 'amber', 72, 'Director'),

  ('reviews_missed_yesterday',
   'Reviews missed yesterday',
   'SELECT drc.owner_uid AS target_user_uid, NULL AS target_lead_id FROM daily_rhythm_checkpoint drc WHERE drc.status = ''missed'' AND DATE(drc.planned_at) = CURDATE() - INTERVAL 1 DAY AND drc.owner_role = ''CM''',
   'CM', 'info', 24, 'RM'),

  ('bypass_abuse_3pw',
   'Bypass abuse 3 or more in week',
   'SELECT user_id AS target_user_uid, NULL AS target_lead_id FROM bypass_log WHERE bypass_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY user_id HAVING COUNT(*) >= 3',
   'RM', 'red', 24, 'Director');

-- ----------------------------------------------------------------------------
-- 5. RED FLAG EVENT
-- Runtime table. One row per fired flag instance.
-- rhythm_orchestrator evaluates trigger_sql on each cycle and inserts here.
-- Auto-escalation: orchestrator checks open rows past their SLA and sets
-- status=escalated, escalated_at=NOW(), routes via line_manager_chain.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS red_flag_event (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  flag_definition_id INT UNSIGNED NOT NULL  COMMENT 'red_flag_definition.id',
  target_user_uid    INT UNSIGNED NOT NULL  COMMENT 'user.uid of the BD/CM the flag is about',
  target_lead_id     INT UNSIGNED DEFAULT NULL COMMENT 'init_call.id if flag is lead-specific',
  target_event_id    INT UNSIGNED DEFAULT NULL COMMENT 'tblcallevents.id if flag is task-specific',
  fired_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  owner_uid          INT UNSIGNED NOT NULL  COMMENT 'user.uid accountable for resolution',
  status             ENUM('open','ack','resolved','escalated') NOT NULL DEFAULT 'open',
  ack_at             DATETIME     DEFAULT NULL,
  ack_uid            INT UNSIGNED DEFAULT NULL,
  escalated_at       DATETIME     DEFAULT NULL,
  escalated_to_uid   INT UNSIGNED DEFAULT NULL,
  resolved_at        DATETIME     DEFAULT NULL,
  resolved_by_uid    INT UNSIGNED DEFAULT NULL,
  resolution_note    TEXT         DEFAULT NULL,
  created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_flag_user_lead (flag_definition_id, target_user_uid, target_lead_id, fired_at),
  KEY idx_status_fired (status, fired_at),
  KEY idx_owner_uid (owner_uid, status),
  KEY idx_target_lead (target_lead_id, fired_at),
  KEY idx_flag_def (flag_definition_id, fired_at),
  CONSTRAINT fk_rfe_flag_def FOREIGN KEY (flag_definition_id)
    REFERENCES red_flag_definition (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 6. LINE MANAGER CHAIN
-- Routing key for red flag escalation and check chain visibility.
-- One row per direct report relationship. level 1 = direct, level 2 = skip.
-- Pilot seed rows use placeholder uids. Reconcile against real roster
-- from Sales-Team-Reporting.xlsx before 25 May 2026 pilot.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS line_manager_chain (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  child_uid    INT UNSIGNED NOT NULL  COMMENT 'user.uid of the report',
  child_role   ENUM('BD','SC','CM','RM','AO') NOT NULL,
  parent_uid   INT UNSIGNED NOT NULL  COMMENT 'user.uid of the manager',
  parent_role  ENUM('SC','CM','RM','AO','Director') NOT NULL,
  level        TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=direct, 2=skip-level',
  active       TINYINT(1) NOT NULL DEFAULT 1,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_child_parent_level (child_uid, parent_uid, level),
  KEY idx_child_active (child_uid, active),
  KEY idx_parent_active (parent_uid, active),
  KEY idx_child_role (child_role, active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pilot seed rows (placeholder uids - reconcile before deploy)
-- BDs 42-46 report to CM Anjali (uid 12)
INSERT IGNORE INTO line_manager_chain (child_uid, child_role, parent_uid, parent_role, level)
VALUES
  (42, 'BD', 12, 'CM', 1),
  (43, 'BD', 12, 'CM', 1),
  (44, 'BD', 12, 'CM', 1),
  (45, 'BD', 12, 'CM', 1),
  (46, 'BD', 12, 'CM', 1);
-- Add RM above Anjali here once RM uid is confirmed with ops team.
-- Example (placeholder): INSERT IGNORE INTO line_manager_chain (child_uid, child_role, parent_uid, parent_role, level) VALUES (12, 'CM', 99, 'RM', 1);

-- ----------------------------------------------------------------------------
-- 7. DAILY HUDDLE MOM
-- One row per cluster per huddle date.
-- Eight MoM sections stored as JSON for flexible rendering.
-- agenda_theme_code driven by day-of-week logic in rhythm_orchestrator:
--   Monday=partner_type, Tuesday=funnel_category, Wednesday=bd_request,
--   Thursday=funnel_status, Friday odd=travel_plan, Friday even=weekly_review,
--   Saturday odd=weekly_review.
-- dar_status: CM's assessment of whether the daily activity report is on track.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS daily_huddle_mom (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cluster_id           INT UNSIGNED NOT NULL  COMMENT 'CM uid used as cluster identifier',
  huddle_date          DATE         NOT NULL,
  agenda_theme_code    VARCHAR(32)  NOT NULL  COMMENT 'partner_type|funnel_category|bd_request|funnel_status|travel_plan|weekly_review',
  attendance_json      JSON         DEFAULT NULL COMMENT '{present:[uids], absent:[uids], on_leave:[uids]}',
  open_tickets_json    JSON         DEFAULT NULL COMMENT 'array of red_flag_event ids open at 09:30',
  rp_strategy_json     JSON         DEFAULT NULL COMMENT '{zero_rp_bds:[uids], targets:{uid:count}, actuals:{uid:count}}',
  task_completion_json JSON         DEFAULT NULL COMMENT '{uid:{planned:n, completed:n}} from yesterday',
  meetings_json        JSON         DEFAULT NULL COMMENT 'array of {uid, cid_id, school_name, actiontype, time} for today',
  other_json           JSON         DEFAULT NULL COMMENT 'free text blocks added during call',
  recording_url        VARCHAR(512) DEFAULT NULL COMMENT 'call recording URL pasted by CM after huddle',
  dar_status           ENUM('on_track','at_risk','behind') DEFAULT NULL,
  drafted_at           DATETIME     DEFAULT NULL COMMENT 'when SC submitted the draft',
  cm_signed_at         DATETIME     DEFAULT NULL COMMENT 'when CM approved',
  cm_uid               INT UNSIGNED DEFAULT NULL COMMENT 'user.uid of signing CM',
  created_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cluster_date (cluster_id, huddle_date),
  KEY idx_huddle_date (huddle_date),
  KEY idx_cm_signed (cm_uid, cm_signed_at),
  KEY idx_dar_status (dar_status, huddle_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 8. MIDDAY PULSE SWEEP
-- One row per SC per weekday, written by rhythm_orchestrator at 12:30.
-- planners_reviewed: how many of the 35 BD planners the SC checked.
-- zero_rp_count: BDs with zero RP tasks planned today.
-- missing_gps_count: BDs with a completed visit task but no GPS log yesterday.
-- missing_mom_count: BDs with a completed visit task but no MoM submitted yesterday.
-- whatsapp_nudges_sent: count of nudges fired to zero-RP BDs via comm_orchestrator.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS midday_pulse_sweep (
  id                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  sc_uid                INT UNSIGNED NOT NULL  COMMENT 'user.uid of SC who owns the sweep',
  cluster_id            INT UNSIGNED NOT NULL  COMMENT 'CM uid of the cluster',
  sweep_at              DATETIME     NOT NULL  COMMENT 'UTC datetime when orchestrator wrote this row',
  planners_reviewed     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  zero_rp_count         TINYINT UNSIGNED NOT NULL DEFAULT 0,
  missing_gps_count     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  missing_mom_count     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  whatsapp_nudges_sent  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  sc_completed_at       DATETIME     DEFAULT NULL COMMENT 'when SC tapped Pulse checked in UI',
  notes                 TEXT         DEFAULT NULL,
  created_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_sc_sweep (sc_uid, sweep_at),
  KEY idx_cluster_sweep (cluster_id, sweep_at),
  KEY idx_sweep_date (DATE(sweep_at))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 9. VIEWS
-- ----------------------------------------------------------------------------

-- 9a. v_open_red_flags
-- All open red flag events with flag metadata and target user name.
-- Used by Red Flag Inbox API and CM dashboard.
CREATE OR REPLACE VIEW v_open_red_flags AS
SELECT
  rfe.id                       AS event_id,
  rfd.flag_code,
  rfd.flag_title,
  rfd.severity,
  rfd.owner_role,
  rfd.resolve_hours,
  rfd.escalates_to_role,
  rfe.target_user_uid,
  ut.firstName                  AS target_user_name,
  rfe.target_lead_id,
  ic.compny_nm                 AS school_name,
  rfe.fired_at,
  rfe.owner_uid,
  uo.firstName                  AS owner_name,
  rfe.status,
  TIMESTAMPDIFF(HOUR, rfe.fired_at, NOW()) AS hours_open,
  GREATEST(0, rfd.resolve_hours - TIMESTAMPDIFF(HOUR, rfe.fired_at, NOW())) AS hours_remaining
FROM red_flag_event rfe
INNER JOIN red_flag_definition rfd ON rfd.id = rfe.flag_definition_id
LEFT JOIN  user ut                  ON ut.uid = rfe.target_user_uid
LEFT JOIN  user uo                  ON uo.uid = rfe.owner_uid
LEFT JOIN  init_call ic             ON ic.id  = rfe.target_lead_id
WHERE rfe.status IN ('open','ack');

-- 9b. v_touchpoint_status_today
-- Today's checkpoint status across all touchpoints for all active users.
-- Used by the Rhythm Dashboard API.
CREATE OR REPLACE VIEW v_touchpoint_status_today AS
SELECT
  drc.id                    AS checkpoint_id,
  drt.touchpoint_code,
  drt.touchpoint_name,
  drt.scheduled_time_ist,
  drc.owner_role,
  drc.owner_uid,
  u.firstName                AS owner_name,
  drc.cluster_id,
  drc.planned_at,
  drc.completed_at,
  drc.status,
  TIMESTAMPDIFF(MINUTE, drc.planned_at, NOW()) AS minutes_since_planned
FROM daily_rhythm_checkpoint drc
INNER JOIN daily_rhythm_touchpoint drt ON drt.id = drc.touchpoint_id
LEFT JOIN  user u                      ON u.uid   = drc.owner_uid
WHERE DATE(drc.planned_at) = CURDATE();

-- 9c. v_cluster_red_flag_summary
-- Count of open red flags by cluster and severity.
-- Used by RM evening digest.
CREATE OR REPLACE VIEW v_cluster_red_flag_summary AS
SELECT
  lmc.parent_uid           AS cm_uid,
  ucm.firstName             AS cm_name,
  rfd.severity,
  COUNT(rfe.id)            AS open_count
FROM red_flag_event rfe
INNER JOIN red_flag_definition rfd ON rfd.id = rfe.flag_definition_id
INNER JOIN line_manager_chain lmc  ON lmc.child_uid = rfe.target_user_uid
                                   AND lmc.child_role = 'BD'
                                   AND lmc.level = 1
                                   AND lmc.active = 1
LEFT JOIN  user ucm                ON ucm.uid = lmc.parent_uid
WHERE rfe.status IN ('open','ack')
GROUP BY lmc.parent_uid, ucm.firstName, rfd.severity;

-- 9d. v_huddle_mom_pending_sign
-- MoMs drafted but not yet signed by CM. Used by red flag 7 trigger.
CREATE OR REPLACE VIEW v_huddle_mom_pending_sign AS
SELECT
  dhm.id,
  dhm.cluster_id,
  dhm.huddle_date,
  dhm.drafted_at,
  dhm.cm_uid,
  u.firstName                     AS cm_name,
  TIMESTAMPDIFF(HOUR, dhm.drafted_at, NOW()) AS hours_since_draft
FROM daily_huddle_mom dhm
LEFT JOIN user u ON u.uid = dhm.cm_uid
WHERE dhm.drafted_at IS NOT NULL
  AND dhm.cm_signed_at IS NULL;

-- 9e. v_midday_pulse_today
-- Today's midday pulse sweep rows with SC name and colour-coded risk level.
CREATE OR REPLACE VIEW v_midday_pulse_today AS
SELECT
  mps.id,
  mps.sc_uid,
  u.firstName                         AS sc_name,
  mps.cluster_id,
  mps.sweep_at,
  mps.planners_reviewed,
  mps.zero_rp_count,
  mps.missing_gps_count,
  mps.missing_mom_count,
  mps.whatsapp_nudges_sent,
  mps.sc_completed_at,
  CASE
    WHEN mps.zero_rp_count > 5 THEN 'red'
    WHEN mps.zero_rp_count BETWEEN 2 AND 5 THEN 'amber'
    ELSE 'green'
  END AS zero_rp_risk,
  CASE
    WHEN mps.missing_gps_count > 5 THEN 'red'
    WHEN mps.missing_gps_count BETWEEN 2 AND 5 THEN 'amber'
    ELSE 'green'
  END AS gps_risk
FROM midday_pulse_sweep mps
LEFT JOIN user u ON u.uid = mps.sc_uid
WHERE DATE(mps.sweep_at) = CURDATE();

-- ----------------------------------------------------------------------------
-- 10. STORED PROCEDURE: sp_escalate_overdue_flags
-- Called by rhythm_orchestrator on each cycle (all five touchpoints).
-- Finds open/ack red_flag_event rows past their resolve_hours SLA and
-- auto-escalates them: sets status=escalated, escalated_at=NOW(),
-- sets escalated_to_uid by looking up line_manager_chain.
-- ----------------------------------------------------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_escalate_overdue_flags$$
CREATE PROCEDURE sp_escalate_overdue_flags()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE v_event_id   INT UNSIGNED;
  DECLARE v_owner_uid  INT UNSIGNED;
  DECLARE v_parent_uid INT UNSIGNED DEFAULT NULL;

  DECLARE cur_overdue CURSOR FOR
    SELECT rfe.id, rfe.owner_uid
    FROM red_flag_event rfe
    INNER JOIN red_flag_definition rfd ON rfd.id = rfe.flag_definition_id
    WHERE rfe.status IN ('open','ack')
      AND TIMESTAMPDIFF(HOUR, rfe.fired_at, NOW()) > rfd.resolve_hours;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur_overdue;
  escalate_loop: LOOP
    FETCH cur_overdue INTO v_event_id, v_owner_uid;
    IF done THEN LEAVE escalate_loop; END IF;

    -- Look up the direct manager of the owner
    SELECT parent_uid INTO v_parent_uid
      FROM line_manager_chain
      WHERE child_uid = v_owner_uid AND level = 1 AND active = 1
      LIMIT 1;

    UPDATE red_flag_event
       SET status          = 'escalated',
           escalated_at    = NOW(),
           escalated_to_uid = v_parent_uid
     WHERE id = v_event_id;
  END LOOP;
  CLOSE cur_overdue;
END$$
DELIMITER ;

-- ============================================================================
-- END OF MIGRATION 035
-- ============================================================================
