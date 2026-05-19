-- ============================================================================
-- STEM CRM - Migration 034
-- Business Card Scan + OCR Lead Capture
-- ============================================================================
-- Scope: card_scan_log table, two supporting views, K33/K34 scorecard fields.
--        All on staging (stemapp.in). Pilot deploy Mon 25 May 2026.
--        Org rollout Mon 1 Jun 2026.
--
-- Author: STEM ops
-- Date: 2026-05-19
-- Depends on: migration 021 (init_call DM contact columns), 023_3 (scorecard)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. FEATURE FLAG ROW
-- Pilot restricted to uids 42,43,44,45,46 until 1 Jun 2026.
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO feature_flags
  (flag_key, description, enabled, allowed_uids, created_at)
VALUES
  ('card_scan_enabled',
   'Business card OCR scan on new-lead and school-visit task screens',
   1,
   '42,43,44,45,46',
   NOW());

-- ----------------------------------------------------------------------------
-- 2. CARD SCAN LOG
-- One row per scan attempt. Status lifecycle:
--   pending -> matched_existing (dedup hit, BD chose update existing)
--   pending -> new_lead         (BD chose create new)
--   pending -> discarded        (BD cancelled or navigated away)
-- lead_id is NULL until BD confirms. Set at confirm time.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS card_scan_log (
  id                    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  uid                   INT UNSIGNED     NOT NULL
                          COMMENT 'user.uid of the BD who scanned',
  lead_id               INT UNSIGNED     DEFAULT NULL
                          COMMENT 'init_call.id, set at confirm time',
  image_url             VARCHAR(512)     NOT NULL
                          COMMENT 'S3 pre-signed URL or path, 90-day retention',
  ocr_raw_text          MEDIUMTEXT       DEFAULT NULL
                          COMMENT 'Full text blob from Google Vision or Textract',
  ocr_provider          VARCHAR(32)      NOT NULL DEFAULT 'vision'
                          COMMENT 'vision or textract',
  ocr_ms                SMALLINT UNSIGNED DEFAULT NULL
                          COMMENT 'OCR round-trip milliseconds',
  parsed_name           VARCHAR(255)     DEFAULT NULL,
  parsed_designation    VARCHAR(255)     DEFAULT NULL,
  parsed_email          VARCHAR(255)     DEFAULT NULL,
  parsed_phone          VARCHAR(64)      DEFAULT NULL
                          COMMENT 'Digits only, no formatting',
  parsed_org            VARCHAR(255)     DEFAULT NULL,
  parsed_address        TEXT             DEFAULT NULL,
  confidence            JSON             DEFAULT NULL
                          COMMENT 'Per-field scores: {"name":0.92,"designation":0.87,...}',
  status                ENUM(
                          'pending',
                          'matched_existing',
                          'new_lead',
                          'discarded'
                        ) NOT NULL DEFAULT 'pending',
  dedup_match_lead_id   INT UNSIGNED     DEFAULT NULL
                          COMMENT 'init_call.id of nearest dedup hit, if any',
  dedup_match_reason    VARCHAR(64)      DEFAULT NULL
                          COMMENT 'email_exact, phone_exact, or org_fuzzy',
  discarded_reason      VARCHAR(64)      DEFAULT NULL
                          COMMENT 'bd_cancelled, ocr_failed, dedup_error',
  scanned_at            DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  confirmed_at          DATETIME         DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_uid_date       (uid, scanned_at),
  KEY idx_lead           (lead_id),
  KEY idx_status_date    (status, scanned_at),
  KEY idx_dedup_match    (dedup_match_lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT 'Migration 034: business card OCR scan log';

-- ----------------------------------------------------------------------------
-- 3. VIEW: v_card_scan_today
-- All scans from today with BD name, lead school name (if confirmed), and
-- per-field confidence scores expanded from JSON.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_card_scan_today AS
SELECT
  c.id                                          AS scan_id,
  c.uid                                         AS bd_uid,
  u.firstName                                   AS bd_name,
  c.lead_id,
  ic.compny_nm                                  AS school_name,
  c.ocr_provider,
  c.ocr_ms,
  c.parsed_name,
  c.parsed_designation,
  c.parsed_email,
  c.parsed_phone,
  c.parsed_org,
  c.status,
  c.dedup_match_lead_id,
  c.dedup_match_reason,
  -- Expand confidence JSON to typed columns for dashboard queries.
  CAST(JSON_UNQUOTE(JSON_EXTRACT(c.confidence, '$.name'))        AS DECIMAL(4,3))
    AS conf_name,
  CAST(JSON_UNQUOTE(JSON_EXTRACT(c.confidence, '$.designation')) AS DECIMAL(4,3))
    AS conf_designation,
  CAST(JSON_UNQUOTE(JSON_EXTRACT(c.confidence, '$.email'))       AS DECIMAL(4,3))
    AS conf_email,
  CAST(JSON_UNQUOTE(JSON_EXTRACT(c.confidence, '$.phone'))       AS DECIMAL(4,3))
    AS conf_phone,
  c.scanned_at,
  c.confirmed_at
FROM card_scan_log c
LEFT JOIN user u       ON u.uid  = c.uid
LEFT JOIN init_call ic ON ic.id  = c.lead_id
WHERE DATE(c.scanned_at) = CURDATE();

-- ----------------------------------------------------------------------------
-- 4. VIEW: v_card_dedup_candidates
-- For any pending scan, surface init_call rows that are likely duplicates:
--   - same email (exact), or
--   - same phone digits (exact), or
--   - school name within 4 Levenshtein edit distance (requires UDF or app-side).
-- This view handles exact-match only; fuzzy org match is handled in PHP.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_card_dedup_candidates AS
SELECT
  csl.id                                AS scan_id,
  csl.uid                               AS bd_uid,
  csl.parsed_email,
  csl.parsed_phone,
  csl.parsed_org,
  ic.id                                 AS candidate_lead_id,
  ic.compny_nm                          AS candidate_school,
  ic.dm_contact_name                    AS candidate_dm_name,
  ic.dm_contact_email                   AS candidate_dm_email,
  ic.dm_contact_phone                   AS candidate_dm_phone,
  ic.cstatus,
  ic.mainbd,
  CASE
    WHEN csl.parsed_email IS NOT NULL
         AND csl.parsed_email <> ''
         AND ic.dm_contact_email = csl.parsed_email
      THEN 'email_exact'
    WHEN csl.parsed_phone IS NOT NULL
         AND csl.parsed_phone <> ''
         AND REGEXP_REPLACE(ic.dm_contact_phone, '[^0-9]', '')
               = REGEXP_REPLACE(csl.parsed_phone, '[^0-9]', '')
      THEN 'phone_exact'
    ELSE 'org_fuzzy'
  END                                   AS match_reason,
  ic.created_at                         AS lead_created_at
FROM card_scan_log csl
JOIN init_call ic
  ON (
       -- Email exact match
       (csl.parsed_email IS NOT NULL AND csl.parsed_email <> ''
        AND ic.dm_contact_email = csl.parsed_email)
     OR
       -- Phone exact match (digits only)
       (csl.parsed_phone IS NOT NULL AND csl.parsed_phone <> ''
        AND REGEXP_REPLACE(ic.dm_contact_phone, '[^0-9]', '')
              = REGEXP_REPLACE(csl.parsed_phone, '[^0-9]', ''))
     )
  AND ic.created_at >= NOW() - INTERVAL 12 MONTH
WHERE csl.status = 'pending';

-- ----------------------------------------------------------------------------
-- 5. EXTEND line_manager_scorecard FOR K33 AND K34
-- K33: card_scan_success_pct  - scans reaching new_lead or matched_existing
--      divided by total scans by BDs under this manager.
-- K34: card_scan_to_meeting_pct - confirmed scans (lead_id not null) where
--      that lead has a tblcallevents row within 21 days of scanned_at.
-- ----------------------------------------------------------------------------
ALTER TABLE line_manager_scorecard
  ADD COLUMN IF NOT EXISTS k33_card_scan_success_pct  DECIMAL(5,2) DEFAULT NULL
    COMMENT 'percent of card scans by team reaching new_lead or matched_existing'
    AFTER incentive_deduction_rs,
  ADD COLUMN IF NOT EXISTS k33_total_scans            INT UNSIGNED DEFAULT 0
    AFTER k33_card_scan_success_pct,
  ADD COLUMN IF NOT EXISTS k33_successful_scans       INT UNSIGNED DEFAULT 0
    AFTER k33_total_scans,
  ADD COLUMN IF NOT EXISTS k34_card_scan_to_meeting_pct DECIMAL(5,2) DEFAULT NULL
    COMMENT 'percent of confirmed scans that produced a tblcallevents row within 21 days'
    AFTER k33_successful_scans,
  ADD COLUMN IF NOT EXISTS k34_confirmed_scans        INT UNSIGNED DEFAULT 0
    AFTER k34_card_scan_to_meeting_pct,
  ADD COLUMN IF NOT EXISTS k34_met_scans              INT UNSIGNED DEFAULT 0
    AFTER k34_confirmed_scans;

-- Stored procedure to refresh K33 and K34 for a given manager.
-- Called by the nightly consolidated cron (see deploy runbook).
DROP PROCEDURE IF EXISTS sp_refresh_k33_k34;
DELIMITER $$
CREATE PROCEDURE sp_refresh_k33_k34(IN p_manager_uid INT UNSIGNED, IN p_week_start DATE)
BEGIN
  DECLARE v_total      INT DEFAULT 0;
  DECLARE v_successful INT DEFAULT 0;
  DECLARE v_confirmed  INT DEFAULT 0;
  DECLARE v_met        INT DEFAULT 0;

  -- K33: scans in the 7-day window ending today by BDs under this manager.
  SELECT
    COUNT(*),
    SUM(CASE WHEN c.status IN ('new_lead','matched_existing') THEN 1 ELSE 0 END)
  INTO v_total, v_successful
  FROM card_scan_log c
  JOIN reporting_hierarchy rh
    ON rh.employee_uid = c.uid
   AND rh.parent_uid   = p_manager_uid
   AND rh.active       = 1
  WHERE c.scanned_at >= p_week_start
    AND c.scanned_at <  p_week_start + INTERVAL 7 DAY;

  -- K34: confirmed scans that produced a tblcallevents row within 21 days.
  SELECT
    COUNT(*),
    SUM(CASE WHEN te.cid_id IS NOT NULL THEN 1 ELSE 0 END)
  INTO v_confirmed, v_met
  FROM card_scan_log c
  JOIN reporting_hierarchy rh
    ON rh.employee_uid = c.uid
   AND rh.parent_uid   = p_manager_uid
   AND rh.active       = 1
  LEFT JOIN tblcallevents te
    ON te.cid_id     = c.lead_id
   AND te.event_date >= DATE(c.scanned_at)
   AND te.event_date <= DATE(c.scanned_at) + INTERVAL 21 DAY
  WHERE c.lead_id IS NOT NULL
    AND c.scanned_at >= p_week_start
    AND c.scanned_at <  p_week_start + INTERVAL 7 DAY;

  UPDATE line_manager_scorecard
     SET k33_total_scans               = v_total,
         k33_successful_scans          = v_successful,
         k33_card_scan_success_pct     = CASE WHEN v_total > 0
                                         THEN ROUND(100.0 * v_successful / v_total, 2)
                                         ELSE NULL END,
         k34_confirmed_scans           = v_confirmed,
         k34_met_scans                 = v_met,
         k34_card_scan_to_meeting_pct  = CASE WHEN v_confirmed > 0
                                         THEN ROUND(100.0 * v_met / v_confirmed, 2)
                                         ELSE NULL END
   WHERE manager_uid  = p_manager_uid
     AND week_start   = p_week_start;
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- 6. SEED: quarter_config weight update (optional, only if weights not set)
-- K33 and K34 combined carry 5 percent of total scorecard weight in FY27 Q2+.
-- Only runs if k33_weight and k34_weight columns exist (idempotent guard).
-- ----------------------------------------------------------------------------
ALTER TABLE quarter_config
  ADD COLUMN IF NOT EXISTS k33_weight TINYINT UNSIGNED NOT NULL DEFAULT 3
    COMMENT 'Card scan success weight',
  ADD COLUMN IF NOT EXISTS k34_weight TINYINT UNSIGNED NOT NULL DEFAULT 2
    COMMENT 'Card scan to meeting weight';

-- ----------------------------------------------------------------------------
-- 7. INDEX ADDITIONS on init_call for dedup lookup performance
-- These may already exist from earlier migrations; IF NOT EXISTS guards them.
-- ----------------------------------------------------------------------------
-- Email dedup index
SET @idx := (
  SELECT COUNT(1) FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name   = 'init_call'
    AND index_name   = 'idx_dm_email_dedup'
);
SET @sql := IF(@idx = 0,
  'ALTER TABLE init_call ADD INDEX idx_dm_email_dedup (dm_contact_email(64))',
  'SELECT ''idx_dm_email_dedup already exists'' AS note'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Phone dedup index
SET @idx2 := (
  SELECT COUNT(1) FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name   = 'init_call'
    AND index_name   = 'idx_dm_phone_dedup'
);
SET @sql2 := IF(@idx2 = 0,
  'ALTER TABLE init_call ADD INDEX idx_dm_phone_dedup (dm_contact_phone(20))',
  'SELECT ''idx_dm_phone_dedup already exists'' AS note'
);
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- ----------------------------------------------------------------------------
-- END OF MIGRATION 034
-- ============================================================================
