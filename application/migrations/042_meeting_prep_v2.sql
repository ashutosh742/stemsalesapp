-- Migration 042: Corporate Meeting Prep Agent
-- Date: 2026-05-20
-- Author: STEM Learning
-- Parallel to production. All tables are _v2.
-- Sits next to Migration 041 (CSR Prospecting); calls 041 endpoints for corporate + DM data.
-- Idempotent: safe to run multiple times.

START TRANSACTION;

-- ===========================================================================
-- 1. meeting_prep_run_v2
-- One row per agent invocation. Tracks input, source breakdown, runtime, status.
-- ===========================================================================
CREATE TABLE IF NOT EXISTS `meeting_prep_run_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK to tblcallevents (the meeting being prepped)',
  `cid_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK to init_call',
  `corporate_id` BIGINT UNSIGNED NULL COMMENT 'FK to csr_corporate_master_v2 (041)',
  `bd_uid` INT UNSIGNED NOT NULL COMMENT 'BD user.uid presenting',
  `dm_id` BIGINT UNSIGNED NULL COMMENT 'FK to csr_decision_maker_v2 (041)',
  `trigger_type` ENUM('auto','on_demand') NOT NULL DEFAULT 'auto',
  `triggered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `meeting_scheduled_at` DATETIME NOT NULL COMMENT 'From daily_planner.scheduled_at',
  `status` ENUM('queued','running','succeeded','partial','failed') NOT NULL DEFAULT 'queued',
  `internal_pulled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if L1 internal CRM data found',
  `linkedin_used` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if L3 LinkedIn hit',
  `apollo_used` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if L4 Apollo hit',
  `influencer_matched` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if L5 political influencer match',
  `enrichment_limited_reason` VARCHAR(120) NULL COMMENT 'e.g. apollo_quota_exhausted',
  `runtime_ms` INT UNSIGNED NULL,
  `error_log` TEXT NULL,
  `completed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_event` (`event_id`),
  KEY `idx_bd_date` (`bd_uid`, `meeting_scheduled_at`),
  KEY `idx_status` (`status`, `triggered_at`),
  KEY `idx_cid` (`cid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 042: one row per meeting prep agent run';

-- ===========================================================================
-- 2. meeting_prep_artifact_v2
-- One row per artifact (PDF, PPT, WhatsApp text). Allows re-fetch from app.
-- ===========================================================================
CREATE TABLE IF NOT EXISTS `meeting_prep_artifact_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `run_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK to meeting_prep_run_v2',
  `artifact_type` ENUM('pdf','pptx','whatsapp_text') NOT NULL,
  `file_path` VARCHAR(500) NOT NULL COMMENT 'Server path or S3 URL',
  `size_bytes` INT UNSIGNED NULL,
  `generated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `last_downloaded_at` DATETIME NULL,
  `delivery_status` ENUM('pending','sent','failed','skipped') NOT NULL DEFAULT 'pending' COMMENT 'For WhatsApp/email delivery',
  `delivery_error` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_run` (`run_id`),
  KEY `idx_type_generated` (`artifact_type`, `generated_at`),
  CONSTRAINT `fk_mp_artifact_run` FOREIGN KEY (`run_id`) REFERENCES `meeting_prep_run_v2`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 042: artifacts produced by each meeting prep run';

-- ===========================================================================
-- 3. enrichment_cache_v2
-- DM- and corporate-level enrichment cache. 30-day TTL. Reused across runs.
-- ===========================================================================
CREATE TABLE IF NOT EXISTS `enrichment_cache_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type` ENUM('dm','corporate') NOT NULL,
  `entity_id` BIGINT UNSIGNED NOT NULL COMMENT 'dm_id or corporate_id',
  `source` ENUM('linkedin','apollo','csr_gov','political','manual') NOT NULL,
  `payload_json` MEDIUMTEXT NOT NULL,
  `confidence` DECIMAL(3,2) NULL COMMENT '0.00-1.00, from source',
  `fetched_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_entity_source` (`entity_type`, `entity_id`, `source`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 042: enrichment cache with 30-day TTL';

-- ===========================================================================
-- Views
-- ===========================================================================

-- View: today's prep runs grouped by BD for morning brief consumption
DROP VIEW IF EXISTS `v_meeting_prep_today`;
CREATE VIEW `v_meeting_prep_today` AS
SELECT
  r.bd_uid,
  u.username AS bd_name,
  COUNT(*) AS runs_today,
  SUM(r.status='succeeded') AS succeeded,
  SUM(r.status='partial') AS partial,
  SUM(r.status='failed') AS failed,
  SUM(r.apollo_used) AS apollo_calls_used,
  SUM(r.linkedin_used) AS linkedin_calls_used,
  SUM(r.trigger_type='on_demand') AS on_demand_runs,
  SUM(r.trigger_type='auto') AS auto_runs,
  MAX(r.triggered_at) AS last_run_at
FROM meeting_prep_run_v2 r
JOIN user u ON u.uid = r.bd_uid
WHERE DATE(r.triggered_at) = CURDATE()
GROUP BY r.bd_uid, u.username;

-- View: artifact freshness for a given event_id (used by /api/meeting_prep/artifact)
DROP VIEW IF EXISTS `v_latest_artifact_per_event`;
CREATE VIEW `v_latest_artifact_per_event` AS
SELECT
  r.event_id,
  a.artifact_type,
  a.file_path,
  a.size_bytes,
  a.generated_at,
  a.download_count,
  r.status AS run_status,
  r.bd_uid
FROM meeting_prep_artifact_v2 a
JOIN meeting_prep_run_v2 r ON r.id = a.run_id
WHERE a.id IN (
  SELECT MAX(a2.id)
  FROM meeting_prep_artifact_v2 a2
  JOIN meeting_prep_run_v2 r2 ON r2.id = a2.run_id
  GROUP BY r2.event_id, a2.artifact_type
);

-- View: enrichment cache health (visible expired/expiring rows for cleanup)
DROP VIEW IF EXISTS `v_enrichment_cache_health`;
CREATE VIEW `v_enrichment_cache_health` AS
SELECT
  entity_type,
  source,
  COUNT(*) AS rows_total,
  SUM(expires_at < NOW()) AS rows_expired,
  SUM(expires_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)) AS rows_expiring_7d,
  MIN(fetched_at) AS oldest_fetched,
  MAX(fetched_at) AS newest_fetched
FROM enrichment_cache_v2
GROUP BY entity_type, source;

COMMIT;

-- ===========================================================================
-- Post-deploy seed (no-op if already seeded)
-- ===========================================================================

-- Index hint for daily cleanup job (cron will DELETE expired rows nightly)
-- Optional: schedule cleanup as a separate cron after pilot stabilizes
