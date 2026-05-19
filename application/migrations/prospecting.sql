-- Migration 019_prospecting_agent.sql
-- Date: 2026-05-16
-- Owner: STEM Learning - stemapp.in (staging first, prod hold until Mon 18 May 2026)
-- Purpose: Track prospecting effectiveness for BDs and surface location-aware
--          lead suggestions (cluster nearby companies + Google Maps web research).
--
-- Builds on:
--   - init_call (existing lead table, has category, cluster_id, current_status_id, compnay typo preserved)
--   - tblcallevents (actiontype_id=4 purpose_id=66 = Barg in Meeting / batch meeting new,
--                    actiontype_id=10 purpose_id=94 = Research)
--   - cluster_mapping (existing cluster + travel_type)
--   - research_candidates (mig 002, holds Anaya/dump-mining/war-room surfaced schools)
--   - user.uid (BDs), user_details.type_id
--
-- New objects:
--   1. lead_category_master              - canonical category dictionary
--   2. partner_type_master               - canonical partner-type dictionary
--   3. prospecting_daily_score           - per-BD daily prospecting effectiveness
--   4. location_prospect_run             - one row per "suggest leads near here" call
--   5. location_prospect_suggestion      - per-school suggestion within a run
--   6. cluster_school_index              - denormalized school-by-cluster lookup
--   7. v_prospecting_today_org           - org-wide leads-added-today view
--   8. v_prospecting_today_by_bd         - per-BD prospecting view
--
-- Staging only. Do NOT cut to production until GitHub access lands.

START TRANSACTION;

-- ============================================================
-- 1) CATEGORY MASTER (formalizes init_call.category freeform text)
-- ============================================================
CREATE TABLE IF NOT EXISTS `lead_category_master` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(40) NOT NULL COMMENT 'positive_key, focused, partner, cold, follow_up, lapsed',
  `display_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `priority_weight` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 low to 5 highest, drives ranking',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `lead_category_master` (`code`, `display_name`, `description`, `priority_weight`) VALUES
('positive_key',  'Positive Key Client', 'High-fbudget warm school, already cstatus 6 or above', 5),
('focused',       'Focused Client',      'Cluster-priority school targeted by BD or RM',         4),
('partner',       'Partner',             'Integrator, channel partner, NGO, govt scheme',         4),
('cold',          'Cold New',            'Fresh prospect, no prior contact',                      2),
('follow_up',     'Follow Up',           'Existing lead awaiting next BD action',                 3),
('lapsed',        'Lapsed',              'No activity for 90 plus days, revival candidate',       2);

-- ============================================================
-- 2) PARTNER TYPE MASTER (sub-category for partner leads)
-- ============================================================
CREATE TABLE IF NOT EXISTS `partner_type_master` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(40) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_partner_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `partner_type_master` (`code`, `display_name`, `description`) VALUES
('integrator',     'System Integrator',    'Resells STEM solutions into schools'),
('channel',        'Channel Partner',      'Regional channel reseller or distributor'),
('ngo',            'NGO',                  'Foundation or nonprofit funding STEM in schools'),
('govt_scheme',    'Government Scheme',    'PMSHRI, Samagra Shiksha, state innovation cell'),
('csr',            'CSR Sponsor',          'Corporate CSR arm sponsoring labs'),
('alumni_network', 'Alumni Network',       'Alumni body funding lab at alma mater'),
('education_body', 'Education Body',       'Board, university, education consortium');

-- Add optional partner_type_id link on init_call (only if column does not exist)
-- Production has init_call.category as freeform text - this adds the formal foreign key
SET @col_exists := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = DATABASE() AND table_name = 'init_call' AND column_name = 'category_code');
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE init_call ADD COLUMN category_code VARCHAR(40) DEFAULT NULL COMMENT ''FK -> lead_category_master.code'' AFTER category,
                         ADD COLUMN partner_type_code VARCHAR(40) DEFAULT NULL COMMENT ''FK -> partner_type_master.code'' AFTER category_code,
                         ADD KEY idx_category_code (category_code),
                         ADD KEY idx_partner_type (partner_type_code)',
  'SELECT ''category_code already exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================
-- 3) PROSPECTING DAILY SCORE (per BD per day)
-- ============================================================
CREATE TABLE IF NOT EXISTS `prospecting_daily_score` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bd_uid` INT UNSIGNED NOT NULL,
  `score_date` DATE NOT NULL,
  `research_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'actiontype 10 purpose 94 rows yesterday',
  `barge_meeting_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'actiontype 4 purpose 66 rows yesterday',
  `new_leads_added` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'init_call.new_lead=1 created by BD',
  `category_positive_key` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `category_focused` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `category_partner` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `category_cold` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `category_follow_up` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `category_lapsed` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `cluster_id` INT UNSIGNED DEFAULT NULL COMMENT 'primary cluster worked',
  `prospecting_score` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 to 100, weighted',
  `grade` ENUM('A+','A','B','C','D') DEFAULT NULL,
  `notes` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bd_date` (`bd_uid`, `score_date`),
  KEY `idx_date_grade` (`score_date`, `grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4) LOCATION PROSPECT RUN (one row per "show me leads near Colaba" call)
-- ============================================================
CREATE TABLE IF NOT EXISTS `location_prospect_run` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bd_uid` INT UNSIGNED NOT NULL,
  `triggered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `area_name` VARCHAR(120) NOT NULL COMMENT 'e.g. Colaba, Andheri East, Bandra West',
  `city` VARCHAR(80) NOT NULL DEFAULT 'Mumbai',
  `lat` DECIMAL(10,7) DEFAULT NULL,
  `lng` DECIMAL(10,7) DEFAULT NULL,
  `radius_km` DECIMAL(4,2) NOT NULL DEFAULT 2.00,
  `cluster_id` INT UNSIGNED DEFAULT NULL,
  `source_mix` VARCHAR(80) NOT NULL DEFAULT 'cluster+web' COMMENT 'cluster only, web only, or cluster+web',
  `cluster_suggestion_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `web_suggestion_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `accepted_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `dismissed_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('pending','partial','complete','expired') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `idx_bd_run` (`bd_uid`, `triggered_at`),
  KEY `idx_area` (`area_name`, `city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5) LOCATION PROSPECT SUGGESTION (per school, within a run)
-- ============================================================
CREATE TABLE IF NOT EXISTS `location_prospect_suggestion` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `run_id` INT UNSIGNED NOT NULL COMMENT 'FK -> location_prospect_run.id',
  `school_name` VARCHAR(255) NOT NULL,
  `address_short` VARCHAR(255) DEFAULT NULL,
  `board` VARCHAR(40) DEFAULT NULL COMMENT 'CBSE, ICSE, State, IB, IGCSE, Other',
  `est_student_count` INT UNSIGNED DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `principal_name` VARCHAR(150) DEFAULT NULL,
  `category_hint` VARCHAR(40) DEFAULT NULL COMMENT 'maps to lead_category_master.code',
  `partner_hint` VARCHAR(40) DEFAULT NULL COMMENT 'if known partner',
  `lat` DECIMAL(10,7) DEFAULT NULL,
  `lng` DECIMAL(10,7) DEFAULT NULL,
  `distance_km` DECIMAL(5,2) DEFAULT NULL,
  `source` ENUM('cluster_index','web_google_maps','web_directory','manual') NOT NULL,
  `source_detail` VARCHAR(255) DEFAULT NULL,
  `confidence` DECIMAL(4,3) NOT NULL DEFAULT 0.500,
  `existing_init_call_id` INT UNSIGNED DEFAULT NULL COMMENT 'if school already a lead, do not double',
  `status` ENUM('suggested','accepted','dismissed','duplicate') NOT NULL DEFAULT 'suggested',
  `accepted_init_call_id` INT UNSIGNED DEFAULT NULL,
  `dismissed_reason` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actioned_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_run` (`run_id`),
  KEY `idx_status` (`status`),
  KEY `idx_school` (`school_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 6) CLUSTER SCHOOL INDEX (denorm for fast "what schools are in Colaba" lookups)
-- ============================================================
CREATE TABLE IF NOT EXISTS `cluster_school_index` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cluster_id` INT UNSIGNED NOT NULL,
  `init_call_id` INT UNSIGNED DEFAULT NULL COMMENT 'NULL if not yet a lead, prospect only',
  `school_name` VARCHAR(255) NOT NULL,
  `area_name` VARCHAR(120) NOT NULL,
  `lat` DECIMAL(10,7) DEFAULT NULL,
  `lng` DECIMAL(10,7) DEFAULT NULL,
  `board` VARCHAR(40) DEFAULT NULL,
  `est_student_count` INT UNSIGNED DEFAULT NULL,
  `category_code` VARCHAR(40) DEFAULT NULL,
  `last_meeting_date` DATE DEFAULT NULL,
  `last_meeting_bd_uid` INT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cluster_area` (`cluster_id`, `area_name`),
  KEY `idx_area_active` (`area_name`, `is_active`),
  KEY `idx_init_call` (`init_call_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 7) VIEW - leads added today org-wide (drives the cron headline)
-- ============================================================
CREATE OR REPLACE VIEW `v_prospecting_today_org` AS
SELECT
  CURDATE() AS as_of_date,
  COUNT(*) AS total_added,
  SUM(CASE WHEN ic.category_code='positive_key' THEN 1 ELSE 0 END) AS positive_key_count,
  SUM(CASE WHEN ic.category_code='focused'      THEN 1 ELSE 0 END) AS focused_count,
  SUM(CASE WHEN ic.category_code='partner'      THEN 1 ELSE 0 END) AS partner_count,
  SUM(CASE WHEN ic.category_code='cold'         THEN 1 ELSE 0 END) AS cold_count,
  SUM(CASE WHEN ic.category_code='follow_up'    THEN 1 ELSE 0 END) AS follow_up_count,
  SUM(CASE WHEN ic.category_code='lapsed'       THEN 1 ELSE 0 END) AS lapsed_count,
  SUM(CASE WHEN ic.partner_type_code IS NOT NULL THEN 1 ELSE 0 END) AS partner_typed_count
FROM init_call ic
WHERE ic.new_lead = 1
  AND DATE(ic.createDate) = CURDATE();

-- ============================================================
-- 8) VIEW - leads added today by BD with research/barge split
-- ============================================================
CREATE OR REPLACE VIEW `v_prospecting_today_by_bd` AS
SELECT
  ic.creator_id AS bd_uid,
  u.username    AS bd_name,
  COUNT(*)      AS leads_added,
  SUM(CASE WHEN ic.category_code='positive_key' THEN 1 ELSE 0 END) AS positive_key_count,
  SUM(CASE WHEN ic.category_code='focused'      THEN 1 ELSE 0 END) AS focused_count,
  SUM(CASE WHEN ic.category_code='partner'      THEN 1 ELSE 0 END) AS partner_count,
  SUM(CASE WHEN ic.category_code='cold'         THEN 1 ELSE 0 END) AS cold_count,
  SUM(CASE WHEN ic.category_code IS NULL OR ic.category_code='' THEN 1 ELSE 0 END) AS uncategorized_count,
  (SELECT COUNT(*) FROM tblcallevents te
     WHERE te.uid = ic.creator_id
       AND te.actiontype_id = 10 AND te.purpose_id = 94
       AND DATE(te.event_date) = CURDATE()) AS research_today,
  (SELECT COUNT(*) FROM tblcallevents te
     WHERE te.uid = ic.creator_id
       AND te.actiontype_id = 4  AND te.purpose_id = 66
       AND DATE(te.event_date) = CURDATE()) AS barge_meeting_today
FROM init_call ic
LEFT JOIN user u ON u.uid = ic.creator_id
WHERE ic.new_lead = 1
  AND DATE(ic.createDate) = CURDATE()
GROUP BY ic.creator_id, u.username
ORDER BY leads_added DESC;

COMMIT;
