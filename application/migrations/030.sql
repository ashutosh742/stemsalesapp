-- ============================================================================
-- STEM CRM - Migration 030
-- Email and Calendar Auto-Capture + AI Email Insights
-- Closes the Salesforce/Outlook + Zoho/Gmail email sync gap.
-- ============================================================================
-- Scope: all BDs. Pilot uids 42,43,44,45,46,12 from 25 May 2026.
--        Org rollout 1 Jun 2026.
-- Naming: stem_ prefix pattern (same as migrations 024-028).
-- Charset: utf8mb4. Engine: InnoDB. All DATETIME cols are UTC+5:30
--          (Mumbai IST) in application layer; stored as UTC in DB.
-- Author: STEM ops
-- Date: 2026-05-19
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. FEATURE FLAG EXTENSION
-- If feature_flag table exists (assumed from mig 022+), add email_capture_enabled.
-- feature_flag.email_capture_enabled values:
--   0 = off (all users)
--   1 = sync only, no UI (pilot phase 25 May 2026)
--   2 = full with UI insights (org rollout 1 Jun 2026)
-- ----------------------------------------------------------------------------
ALTER TABLE feature_flag
  ADD COLUMN IF NOT EXISTS email_capture_enabled TINYINT UNSIGNED NOT NULL DEFAULT 0
    COMMENT '0=off, 1=sync only no UI, 2=full with insights UI';

-- Per-uid override table so pilot uids can be enabled independently.
CREATE TABLE IF NOT EXISTS feature_flag_override (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  uid         INT UNSIGNED NOT NULL  COMMENT 'user.uid',
  flag_name   VARCHAR(64)  NOT NULL  COMMENT 'matches column in feature_flag',
  flag_value  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  set_by_uid  INT UNSIGNED DEFAULT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_uid_flag (uid, flag_name),
  KEY idx_flag (flag_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed pilot overrides (phase 1 = 1, will be updated to 2 on 1 Jun)
INSERT IGNORE INTO feature_flag_override (uid, flag_name, flag_value, set_by_uid)
VALUES
  (42, 'email_capture_enabled', 1, 1),
  (43, 'email_capture_enabled', 1, 1),
  (44, 'email_capture_enabled', 1, 1),
  (45, 'email_capture_enabled', 1, 1),
  (46, 'email_capture_enabled', 1, 1),
  (12, 'email_capture_enabled', 1, 1);

-- ----------------------------------------------------------------------------
-- 2. EMAIL ACCOUNT OAUTH
-- One row per BD per provider (gmail or outlook).
-- Tokens stored AES-256-CBC encrypted. Key in PHP config EMAIL_TOKEN_KEY.
-- Unique on (uid, provider): only one active connection per provider per user.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_account_oauth (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  uid                INT UNSIGNED NOT NULL  COMMENT 'user.uid - BD owner',
  provider           ENUM('gmail','outlook') NOT NULL,
  oauth_token_enc    TEXT NOT NULL            COMMENT 'AES-256 encrypted access token',
  refresh_token_enc  TEXT NOT NULL            COMMENT 'AES-256 encrypted refresh token',
  scopes             VARCHAR(512) NOT NULL     COMMENT 'space-separated OAuth scopes granted',
  token_expires_at   DATETIME DEFAULT NULL     COMMENT 'access token expiry (UTC)',
  last_sync_at       DATETIME DEFAULT NULL     COMMENT 'last successful email poll',
  last_cal_sync_at   DATETIME DEFAULT NULL     COMMENT 'last successful calendar poll',
  status             ENUM('active','revoked') NOT NULL DEFAULT 'active',
  revoked_reason     VARCHAR(255) DEFAULT NULL,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_uid_provider (uid, provider),
  KEY idx_status_provider (status, provider),
  KEY idx_uid (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 3. EMAIL MESSAGE LOG
-- One row per inbound or outbound email ingested via OAuth sync.
-- body_snippet is first 300 chars of plain text only (privacy).
-- Full body is never stored.
-- message_id is provider-native (Gmail messageId, Graph message id).
-- lead_id is nullable; thread_linker sets it when a match is found.
-- attached_files_json: JSON array of filename strings, e.g. ["quote.pdf"].
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_message_log (
  id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uid                  INT UNSIGNED NOT NULL     COMMENT 'BD whose account synced this',
  provider             ENUM('gmail','outlook') NOT NULL,
  message_id           VARCHAR(512) NOT NULL      COMMENT 'provider native message id',
  thread_id            VARCHAR(512) DEFAULT NULL  COMMENT 'Gmail threadId / Outlook conversationId',
  lead_id              INT UNSIGNED DEFAULT NULL  COMMENT 'init_call.id, set by thread_linker',
  direction            ENUM('in','out') NOT NULL  COMMENT 'in=received, out=sent by BD',
  from_addr            VARCHAR(512) NOT NULL,
  to_addr              VARCHAR(512) NOT NULL       COMMENT 'first To: address',
  subject              VARCHAR(512) DEFAULT NULL,
  body_snippet         VARCHAR(1000) DEFAULT NULL  COMMENT 'first 300 chars plain text',
  received_at          DATETIME NOT NULL           COMMENT 'provider timestamp (UTC)',
  attached_files_json  JSON DEFAULT NULL           COMMENT 'array of attachment filenames',
  processed_at         DATETIME DEFAULT NULL       COMMENT 'when insight agent last ran on this row',
  created_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_provider_msg (provider, message_id),
  KEY idx_lead_id (lead_id),
  KEY idx_uid (uid),
  KEY idx_received_at (received_at),
  KEY idx_uid_received (uid, received_at),
  KEY idx_thread (uid, thread_id),
  KEY idx_processed (processed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 4. EMAIL INSIGHT
-- One row per email_message_log row, written by stem_email_insight_agent.
-- Confidence: 0.000-1.000 decimal.
-- intent set: question, objection, decision, next_step, chase
-- action_taken: 1 when BD marks it done via POST /api/email/insight/action_taken
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_insight (
  id                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email_message_log_id  BIGINT UNSIGNED NOT NULL,
  sentiment             ENUM('positive','neutral','negative') NOT NULL DEFAULT 'neutral',
  intent                ENUM('question','objection','decision','next_step','chase')
                          NOT NULL DEFAULT 'question',
  suggested_next_action VARCHAR(512) DEFAULT NULL,
  confidence            DECIMAL(4,3) DEFAULT NULL COMMENT '0.000 to 1.000',
  action_taken          TINYINT(1) NOT NULL DEFAULT 0,
  action_taken_at       DATETIME DEFAULT NULL,
  action_taken_uid      INT UNSIGNED DEFAULT NULL,
  generated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email_msg (email_message_log_id),
  KEY idx_generated (generated_at),
  KEY idx_intent (intent, generated_at),
  KEY idx_action_taken (action_taken, generated_at),
  CONSTRAINT fk_insight_msg FOREIGN KEY (email_message_log_id)
    REFERENCES email_message_log (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 5. CALENDAR EVENT LOG
-- One row per calendar event linked to a known lead.
-- Polled every 15 min from Google Calendar and Outlook Calendar.
-- ext_event_id is provider-native (Google event id, Outlook event id).
-- attendees_json: JSON array of email strings.
-- source_sync_at: updated on every poll that touches this row.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS calendar_event_log (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  uid             INT UNSIGNED NOT NULL    COMMENT 'BD user.uid',
  provider        ENUM('gmail','outlook') NOT NULL,
  ext_event_id    VARCHAR(512) NOT NULL     COMMENT 'provider event id',
  lead_id         INT UNSIGNED DEFAULT NULL COMMENT 'init_call.id if matched',
  title           VARCHAR(512) DEFAULT NULL,
  start_at        DATETIME NOT NULL,
  end_at          DATETIME DEFAULT NULL,
  attendees_json  JSON DEFAULT NULL         COMMENT 'array of attendee email strings',
  location        VARCHAR(512) DEFAULT NULL,
  source_sync_at  DATETIME NOT NULL         COMMENT 'last time this row was synced',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_uid_provider_event (uid, provider, ext_event_id),
  KEY idx_lead_id (lead_id),
  KEY idx_uid (uid),
  KEY idx_start_at (start_at),
  KEY idx_uid_start (uid, start_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 6. EXTEND line_manager_scorecard FOR K27-K30
-- Migration 024 added K8. Migration 030 adds K27-K30.
-- All four new KPIs default to NULL (not scored until org rollout).
-- Quarter weight = 0 during observation period (Q1 FY27).
-- ----------------------------------------------------------------------------
ALTER TABLE line_manager_scorecard
  ADD COLUMN IF NOT EXISTS k27_email_response_sla_pct  DECIMAL(5,2) DEFAULT NULL
    COMMENT 'K27: pct inbound emails with reply within 4 hours working hours',
  ADD COLUMN IF NOT EXISTS k28_emails_per_lead_avg     DECIMAL(6,2) DEFAULT NULL
    COMMENT 'K28: avg email_message_log rows per lead in cstatus 6-9',
  ADD COLUMN IF NOT EXISTS k29_insight_action_pct      DECIMAL(5,2) DEFAULT NULL
    COMMENT 'K29: pct email_insight rows with action_taken=1 within 24h',
  ADD COLUMN IF NOT EXISTS k30_calendar_capture_pct    DECIMAL(5,2) DEFAULT NULL
    COMMENT 'K30: pct tblcallevents visits with matching calendar_event_log row';

-- Add weight columns to quarter_config for K27-K30 (0 weight = observation only)
ALTER TABLE quarter_config
  ADD COLUMN IF NOT EXISTS k27_weight TINYINT UNSIGNED NOT NULL DEFAULT 0
    COMMENT 'Email Response SLA weight (0 = observe only)',
  ADD COLUMN IF NOT EXISTS k28_weight TINYINT UNSIGNED NOT NULL DEFAULT 0
    COMMENT 'Emails per lead avg weight',
  ADD COLUMN IF NOT EXISTS k29_weight TINYINT UNSIGNED NOT NULL DEFAULT 0
    COMMENT 'Insight action taken weight',
  ADD COLUMN IF NOT EXISTS k30_weight TINYINT UNSIGNED NOT NULL DEFAULT 0
    COMMENT 'Calendar capture rate weight';

-- ----------------------------------------------------------------------------
-- 7. VIEWS
-- ----------------------------------------------------------------------------

-- 7a. v_email_insights_today
-- All email insights generated today with BD name, lead/school name, intent.
-- Used by CM dashboard morning briefing.
CREATE OR REPLACE VIEW v_email_insights_today AS
SELECT
  ei.id                     AS insight_id,
  eml.id                    AS message_id,
  eml.uid                   AS bd_uid,
  u.firstName                AS bd_name,
  eml.lead_id,
  ic.compny_nm              AS school_name,
  eml.direction,
  eml.from_addr,
  eml.to_addr,
  eml.subject,
  eml.body_snippet,
  eml.received_at,
  ei.sentiment,
  ei.intent,
  ei.suggested_next_action,
  ei.confidence,
  ei.action_taken,
  ei.generated_at
FROM email_insight ei
INNER JOIN email_message_log eml ON eml.id = ei.email_message_log_id
LEFT JOIN  user u                ON u.uid = eml.uid
LEFT JOIN  init_call ic          ON ic.id = eml.lead_id
WHERE DATE(ei.generated_at) = CURDATE();

-- 7b. v_lead_email_thread
-- All emails for a given lead, ordered by received_at ascending.
-- Used by inbox tile endpoint and lead detail screen.
CREATE OR REPLACE VIEW v_lead_email_thread AS
SELECT
  eml.id              AS message_id,
  eml.uid             AS bd_uid,
  u.firstName          AS bd_name,
  eml.lead_id,
  ic.compny_nm        AS school_name,
  eml.provider,
  eml.thread_id,
  eml.direction,
  eml.from_addr,
  eml.to_addr,
  eml.subject,
  eml.body_snippet,
  eml.received_at,
  eml.attached_files_json,
  ei.sentiment,
  ei.intent,
  ei.suggested_next_action,
  ei.confidence,
  ei.action_taken
FROM email_message_log eml
LEFT JOIN user u          ON u.uid = eml.uid
LEFT JOIN init_call ic    ON ic.id = eml.lead_id
LEFT JOIN email_insight ei ON ei.email_message_log_id = eml.id
WHERE eml.lead_id IS NOT NULL
ORDER BY eml.received_at ASC;

-- 7c. v_email_inbox_unread
-- All inbound emails that have no outbound reply in the same thread within 4h.
-- "Unread" here means unresponded within SLA window.
-- Used to compute K27 and for BD inbox alert badge.
CREATE OR REPLACE VIEW v_email_inbox_unread AS
SELECT
  eml.id             AS message_id,
  eml.uid            AS bd_uid,
  u.firstName         AS bd_name,
  eml.lead_id,
  ic.compny_nm       AS school_name,
  eml.from_addr,
  eml.subject,
  eml.received_at,
  ei.intent,
  ei.sentiment,
  CASE
    WHEN HOUR(eml.received_at) BETWEEN 9 AND 19
         AND DAYOFWEEK(eml.received_at) BETWEEN 2 AND 7
    THEN 'in_sla_window'
    ELSE 'outside_hours'
  END AS sla_window,
  TIMESTAMPDIFF(MINUTE, eml.received_at, NOW()) AS minutes_since_received
FROM email_message_log eml
LEFT JOIN user u          ON u.uid = eml.uid
LEFT JOIN init_call ic    ON ic.id = eml.lead_id
LEFT JOIN email_insight ei ON ei.email_message_log_id = eml.id
WHERE eml.direction = 'in'
  AND NOT EXISTS (
    SELECT 1
    FROM email_message_log reply
    WHERE reply.uid       = eml.uid
      AND reply.thread_id = eml.thread_id
      AND reply.direction = 'out'
      AND reply.received_at > eml.received_at
      AND reply.received_at <= DATE_ADD(eml.received_at, INTERVAL 4 HOUR)
  );

-- ----------------------------------------------------------------------------
-- 8. STORED PROCEDURE: sp_compute_email_kpis
-- Called nightly by cron to populate line_manager_scorecard K27-K30.
-- Accepts @score_date (defaults to CURDATE()).
-- ----------------------------------------------------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_compute_email_kpis$$
CREATE PROCEDURE sp_compute_email_kpis(IN p_score_date DATE)
BEGIN
  DECLARE v_date DATE;
  SET v_date = COALESCE(p_score_date, CURDATE());

  -- K27: email response SLA pct per manager
  -- Numerator: inbound mails in working hours that got a reply within 4h.
  -- Denominator: all inbound mails in working hours for leads under that manager.
  CREATE TEMPORARY TABLE IF NOT EXISTS tmp_k27 AS
  SELECT
    rh.parent_uid AS manager_uid,
    COUNT(*)       AS total_inbound,
    SUM(CASE WHEN EXISTS (
          SELECT 1 FROM email_message_log r
          WHERE r.uid = eml.uid
            AND r.thread_id = eml.thread_id
            AND r.direction = 'out'
            AND r.received_at > eml.received_at
            AND r.received_at <= DATE_ADD(eml.received_at, INTERVAL 4 HOUR)
        ) THEN 1 ELSE 0 END) AS replied_in_sla
  FROM email_message_log eml
  INNER JOIN init_call ic       ON ic.id = eml.lead_id
  INNER JOIN reporting_hierarchy rh ON rh.employee_uid = eml.uid AND rh.active = 1
  WHERE eml.direction = 'in'
    AND DATE(eml.received_at) = v_date
    AND HOUR(eml.received_at) BETWEEN 9 AND 19
    AND DAYOFWEEK(eml.received_at) BETWEEN 2 AND 7
  GROUP BY rh.parent_uid;

  -- K28: emails per lead avg for leads in cstatus 6-9 under manager
  CREATE TEMPORARY TABLE IF NOT EXISTS tmp_k28 AS
  SELECT
    rh.parent_uid AS manager_uid,
    ROUND(COUNT(eml.id) / NULLIF(COUNT(DISTINCT eml.lead_id), 0), 2) AS emails_per_lead
  FROM email_message_log eml
  INNER JOIN init_call ic       ON ic.id = eml.lead_id AND ic.cstatus IN (6,7,8,9)
  INNER JOIN reporting_hierarchy rh ON rh.employee_uid = eml.uid AND rh.active = 1
  WHERE DATE(eml.received_at) <= v_date
  GROUP BY rh.parent_uid;

  -- K29: insight action taken pct within 24h
  CREATE TEMPORARY TABLE IF NOT EXISTS tmp_k29 AS
  SELECT
    rh.parent_uid AS manager_uid,
    COUNT(*)       AS total_insights,
    SUM(CASE WHEN ei.action_taken = 1
               AND TIMESTAMPDIFF(HOUR, ei.generated_at, ei.action_taken_at) <= 24
             THEN 1 ELSE 0 END) AS actioned
  FROM email_insight ei
  INNER JOIN email_message_log eml ON eml.id = ei.email_message_log_id
  INNER JOIN reporting_hierarchy rh ON rh.employee_uid = eml.uid AND rh.active = 1
  WHERE DATE(ei.generated_at) = v_date
  GROUP BY rh.parent_uid;

  -- K30: calendar capture rate - tblcallevents visits with matching calendar row
  -- TODO: confirm actiontype_id for 'school visit' with infra team (assumed = 3)
  CREATE TEMPORARY TABLE IF NOT EXISTS tmp_k30 AS
  SELECT
    rh.parent_uid AS manager_uid,
    COUNT(*)       AS total_visit_tasks,
    SUM(CASE WHEN EXISTS (
          SELECT 1 FROM calendar_event_log cel
          WHERE cel.lead_id = t.cid_id
            AND cel.uid = t.user_id
            AND cel.start_at BETWEEN
                DATE_SUB(DATE(t.event_date), INTERVAL 1 DAY)
                AND DATE_ADD(DATE(t.event_date), INTERVAL 1 DAY)
        ) THEN 1 ELSE 0 END) AS cal_matched
  FROM tblcallevents t
  INNER JOIN init_call ic ON ic.id = t.cid_id
  INNER JOIN reporting_hierarchy rh ON rh.employee_uid = t.user_id AND rh.active = 1
  WHERE t.actiontype_id = 3   -- TODO: confirm visit actiontype_id
    AND DATE(t.event_date) = v_date
  GROUP BY rh.parent_uid;

  -- Write to line_manager_scorecard
  -- Assumes one row per manager per score_date. Adjust to your scorecard key if different.
  UPDATE line_manager_scorecard lms
  LEFT JOIN tmp_k27 k27 ON k27.manager_uid = lms.uid
  LEFT JOIN tmp_k28 k28 ON k28.manager_uid = lms.uid
  LEFT JOIN tmp_k29 k29 ON k29.manager_uid = lms.uid
  LEFT JOIN tmp_k30 k30 ON k30.manager_uid = lms.uid
  SET
    lms.k27_email_response_sla_pct = CASE WHEN COALESCE(k27.total_inbound,0) = 0 THEN NULL
        ELSE ROUND(100.0 * k27.replied_in_sla / k27.total_inbound, 2) END,
    lms.k28_emails_per_lead_avg    = k28.emails_per_lead,
    lms.k29_insight_action_pct     = CASE WHEN COALESCE(k29.total_insights,0) = 0 THEN NULL
        ELSE ROUND(100.0 * k29.actioned / k29.total_insights, 2) END,
    lms.k30_calendar_capture_pct   = CASE WHEN COALESCE(k30.total_visit_tasks,0) = 0 THEN NULL
        ELSE ROUND(100.0 * k30.cal_matched / k30.total_visit_tasks, 2) END
  WHERE DATE(lms.score_date) = v_date;  -- TODO: confirm score_date column name in your scorecard

  DROP TEMPORARY TABLE IF EXISTS tmp_k27;
  DROP TEMPORARY TABLE IF EXISTS tmp_k28;
  DROP TEMPORARY TABLE IF EXISTS tmp_k29;
  DROP TEMPORARY TABLE IF EXISTS tmp_k30;
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- 9. TRIGGER: auto-flag high-signal email insights to lead_progression_log
-- Fires after INSERT on email_insight when intent is decision, next_step,
-- or objection and confidence over 0.75.
-- Writes a lead_progression_log row (table assumed from mig 022/027).
-- event_type value 'email_insight_signal' must be valid in your ENUM or
-- column constraint - add it if needed (ALTER TABLE shown below as example).
-- ----------------------------------------------------------------------------

-- Example: extend lead_progression_log event_type if needed
-- ALTER TABLE lead_progression_log
--   MODIFY COLUMN event_type
--     ENUM('call_unanswered','call_dropped','meeting_completed','proposal_sent',
--          'query_raised','query_resolved','stage_progressed','dormant_re_engage',
--          'email_insight_signal')
--     NOT NULL;

DELIMITER $$
DROP TRIGGER IF EXISTS trg_email_insight_to_progression$$
CREATE TRIGGER trg_email_insight_to_progression
AFTER INSERT ON email_insight
FOR EACH ROW
BEGIN
  DECLARE v_lead_id   INT UNSIGNED DEFAULT NULL;
  DECLARE v_bd_uid    INT UNSIGNED DEFAULT NULL;
  DECLARE v_subject   VARCHAR(512) DEFAULT NULL;

  IF NEW.intent IN ('decision','next_step','objection')
     AND NEW.confidence > 0.75 THEN

    SELECT eml.lead_id, eml.uid, eml.subject
      INTO v_lead_id, v_bd_uid, v_subject
      FROM email_message_log eml
      WHERE eml.id = NEW.email_message_log_id
      LIMIT 1;

    IF v_lead_id IS NOT NULL THEN
      INSERT INTO lead_progression_log
        (cid_id, bd_uid, event_type, source_id, payload, created_at)
      VALUES
        (v_lead_id, v_bd_uid, 'email_insight_signal', NEW.id,
         JSON_OBJECT(
           'insight_id',             NEW.id,
           'intent',                 NEW.intent,
           'sentiment',              NEW.sentiment,
           'suggested_next_action',  NEW.suggested_next_action,
           'confidence',             NEW.confidence,
           'email_subject',          v_subject
         ),
         NOW());
    END IF;
  END IF;
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- END OF MIGRATION 030
-- ============================================================================
