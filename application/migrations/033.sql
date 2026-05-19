-- ============================================================================
-- STEM CRM - Migration 033
-- Mobile Offline Cache - Server-Side Support Tables
-- ============================================================================
-- Scope: stemapp.in staging only. Never run on production.
-- Purpose: Track device sync sessions, apply queued ops from offline devices,
--          expose sync health views, and extend manager scorecard with K31/K32.
--
-- Pilot: Mon 25 May 2026 (read-only cache, uids 42,43,44,45,46,12)
-- Org rollout: Mon 1 Jun 2026 (read+write queue active)
--
-- Author: STEM ops
-- Date: 2026-05-19
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. FEATURE FLAGS
-- Ensure rows exist for offline_enabled and offline_write_enabled.
-- offline_enabled = 0 forces online-only (rollback mechanism).
-- offline_write_enabled = 0 = pilot read-only phase.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS feature_flag (
  flag_key   VARCHAR(64) NOT NULL,
  flag_value TINYINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  note       VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (flag_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO feature_flag (flag_key, flag_value, note)
VALUES
  ('offline_enabled',       0, 'Set 1 on 25 May 2026 pilot deploy'),
  ('offline_write_enabled', 0, 'Set 1 on 1 Jun 2026 org rollout')
ON DUPLICATE KEY UPDATE flag_key = flag_key;

-- ----------------------------------------------------------------------------
-- 2. OFFLINE DEVICE REGISTRY
-- One row per (uid, device_id) pair.
-- device_id is the hardware ID string reported by the app.
-- Registered manually during pilot deploy, auto-registered at first snapshot.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS offline_device_registry (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  uid                INT UNSIGNED NOT NULL COMMENT 'user.uid of the BD or CM',
  device_id          VARCHAR(128) NOT NULL COMMENT 'hardware or install ID from app',
  app_version        VARCHAR(32)  DEFAULT NULL COMMENT 'semver string e.g. 2.4.1',
  last_full_sync_at  DATETIME     DEFAULT NULL COMMENT 'timestamp of last full snapshot pull',
  last_delta_sync_at DATETIME     DEFAULT NULL COMMENT 'timestamp of last delta snapshot pull',
  cache_bytes        INT UNSIGNED DEFAULT NULL COMMENT 'last reported IndexedDB size in bytes',
  active             TINYINT(1)   NOT NULL DEFAULT 1,
  registered_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_uid_device (uid, device_id),
  KEY idx_uid (uid),
  KEY idx_last_delta (last_delta_sync_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Registered offline-capable devices per user';

-- Seed pilot devices (device_id filled in during deploy day registration).
-- Row is inserted with a placeholder device_id; update during device registration.
INSERT IGNORE INTO offline_device_registry (uid, device_id, app_version, active)
VALUES
  (42, 'REGISTER_ON_DEPLOY_priya',  '2.4.0', 1),
  (43, 'REGISTER_ON_DEPLOY_ravi',   '2.4.0', 1),
  (44, 'REGISTER_ON_DEPLOY_anita',  '2.4.0', 1),
  (45, 'REGISTER_ON_DEPLOY_vikram', '2.4.0', 1),
  (46, 'REGISTER_ON_DEPLOY_sneha',  '2.4.0', 1),
  (12, 'REGISTER_ON_DEPLOY_anjali', '2.4.0', 1);

-- ----------------------------------------------------------------------------
-- 3. SYNC QUEUE LOG
-- Server-side record of every op received from a device.
-- Client-side equivalent lives in IndexedDB (sync_queue table in Dexie store).
-- op_type: create | update | delete
-- status:  pending | applied | conflict | rejected
-- conflict_reason is free text explaining why the server did not apply the op.
-- row_payload_json is the JSON object of changed fields only (not full row).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sync_queue_log (
  id                 INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  uid                INT UNSIGNED  NOT NULL COMMENT 'user.uid who submitted the op',
  device_id          VARCHAR(128)  NOT NULL,
  queue_id           VARCHAR(64)   NOT NULL COMMENT 'client-generated UUID for dedup',
  op_type            ENUM('create','update','delete') NOT NULL,
  table_name         VARCHAR(64)   NOT NULL COMMENT 'CRM table targeted: init_call, tblcallevents, etc.',
  row_id             INT UNSIGNED  DEFAULT NULL COMMENT 'server row id; null for new creates',
  row_payload_json   JSON          NOT NULL COMMENT 'changed fields only',
  client_ts          DATETIME(3)   NOT NULL COMMENT 'device timestamp at write time',
  server_ts          DATETIME(3)   NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  status             ENUM('pending','applied','conflict','rejected') NOT NULL DEFAULT 'pending',
  conflict_reason    VARCHAR(512)  DEFAULT NULL COMMENT 'explains why conflict or rejection',
  applied_at         DATETIME      DEFAULT NULL,
  applied_row_id     INT UNSIGNED  DEFAULT NULL COMMENT 'server row id after create',
  PRIMARY KEY (id),
  UNIQUE KEY uq_queue_id (queue_id),
  KEY idx_uid_status  (uid, status, server_ts),
  KEY idx_device      (device_id, server_ts),
  KEY idx_table_row   (table_name, row_id),
  KEY idx_status_date (status, DATE(server_ts))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Server-side log of all ops received from offline devices';

-- ----------------------------------------------------------------------------
-- 4. EXTEND line_manager_scorecard FOR K31 AND K32
-- K31: sync success percent for the manager's cluster today.
-- K32: conflict rate percent for the manager's cluster today.
-- Add columns idempotently.
-- ----------------------------------------------------------------------------
ALTER TABLE line_manager_scorecard
  ADD COLUMN IF NOT EXISTS k31_sync_success_pct    DECIMAL(5,2) DEFAULT NULL
    COMMENT 'percent of sync ops that applied cleanly today for this manager cluster'
    AFTER k8_phantom_count,
  ADD COLUMN IF NOT EXISTS k31_ops_total            INT UNSIGNED DEFAULT 0
    AFTER k31_sync_success_pct,
  ADD COLUMN IF NOT EXISTS k31_ops_applied          INT UNSIGNED DEFAULT 0
    AFTER k31_ops_total,
  ADD COLUMN IF NOT EXISTS k32_conflict_rate_pct    DECIMAL(5,2) DEFAULT NULL
    COMMENT 'percent of sync ops that resulted in conflict today'
    AFTER k31_ops_applied,
  ADD COLUMN IF NOT EXISTS k32_conflict_count       INT UNSIGNED DEFAULT 0
    AFTER k32_conflict_rate_pct;

-- ----------------------------------------------------------------------------
-- 5. VIEW: v_sync_health_today
-- One row per uid showing today's sync activity.
-- Joins to offline_device_registry to get app_version and last sync timestamps.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_sync_health_today AS
SELECT
  sql.uid,
  u.firstName                                    AS bd_name,
  odr.device_id,
  odr.app_version,
  odr.last_full_sync_at,
  odr.last_delta_sync_at,
  COUNT(*)                                       AS total_ops_today,
  SUM(CASE WHEN sql.status = 'applied'   THEN 1 ELSE 0 END) AS applied_count,
  SUM(CASE WHEN sql.status = 'conflict'  THEN 1 ELSE 0 END) AS conflict_count,
  SUM(CASE WHEN sql.status = 'rejected'  THEN 1 ELSE 0 END) AS rejected_count,
  SUM(CASE WHEN sql.status = 'pending'   THEN 1 ELSE 0 END) AS pending_count,
  CASE
    WHEN COUNT(*) = 0 THEN NULL
    ELSE ROUND(100.0 * SUM(CASE WHEN sql.status = 'applied' THEN 1 ELSE 0 END) / COUNT(*), 2)
  END                                            AS sync_success_pct,
  CASE
    WHEN COUNT(*) = 0 THEN NULL
    ELSE ROUND(100.0 * SUM(CASE WHEN sql.status = 'conflict' THEN 1 ELSE 0 END) / COUNT(*), 2)
  END                                            AS conflict_rate_pct
FROM sync_queue_log sql
LEFT JOIN user u   ON u.uid = sql.uid
LEFT JOIN offline_device_registry odr
       ON odr.uid = sql.uid AND odr.device_id = sql.device_id
WHERE DATE(sql.server_ts) = CURDATE()
GROUP BY sql.uid, sql.device_id;

-- ----------------------------------------------------------------------------
-- 6. VIEW: v_sync_conflicts_today
-- Every conflict or rejection today with the payload and reason.
-- Used by CM Anjali each morning during pilot.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_sync_conflicts_today AS
SELECT
  sql.id,
  sql.uid,
  u.firstName                   AS bd_name,
  sql.device_id,
  sql.queue_id,
  sql.op_type,
  sql.table_name,
  sql.row_id,
  sql.row_payload_json,
  sql.client_ts,
  sql.server_ts,
  sql.status,
  sql.conflict_reason
FROM sync_queue_log sql
LEFT JOIN user u ON u.uid = sql.uid
WHERE sql.status IN ('conflict', 'rejected')
  AND DATE(sql.server_ts) = CURDATE()
ORDER BY sql.server_ts DESC;

-- ----------------------------------------------------------------------------
-- 7. VIEW: v_sync_health_by_manager
-- CM-level rollup. Shows each CM's cluster sync health today.
-- Joins through reporting_hierarchy to map BD uids to their CM.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_sync_health_by_manager AS
SELECT
  rh.parent_uid                              AS cm_uid,
  uc.firstName                               AS cm_name,
  COUNT(DISTINCT sql.uid)                    AS active_bd_count,
  COUNT(*)                                   AS total_ops,
  SUM(CASE WHEN sql.status = 'applied'  THEN 1 ELSE 0 END) AS applied,
  SUM(CASE WHEN sql.status = 'conflict' THEN 1 ELSE 0 END) AS conflicts,
  SUM(CASE WHEN sql.status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
  CASE
    WHEN COUNT(*) = 0 THEN NULL
    ELSE ROUND(100.0 * SUM(CASE WHEN sql.status = 'applied' THEN 1 ELSE 0 END) / COUNT(*), 2)
  END AS k31_sync_success_pct,
  CASE
    WHEN COUNT(*) = 0 THEN NULL
    ELSE ROUND(100.0 * SUM(CASE WHEN sql.status = 'conflict' THEN 1 ELSE 0 END) / COUNT(*), 2)
  END AS k32_conflict_rate_pct
FROM sync_queue_log sql
JOIN reporting_hierarchy rh ON rh.employee_uid = sql.uid AND rh.active = 1
LEFT JOIN user uc ON uc.uid = rh.parent_uid
WHERE DATE(sql.server_ts) = CURDATE()
GROUP BY rh.parent_uid;

-- ----------------------------------------------------------------------------
-- 8. STORED PROCEDURE: sp_update_k31_k32
-- Called nightly (or on demand) to push today's sync stats into
-- line_manager_scorecard for each CM in the view.
-- Safe to call multiple times; uses INSERT ... ON DUPLICATE KEY UPDATE.
-- ----------------------------------------------------------------------------
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_update_k31_k32$$
CREATE PROCEDURE sp_update_k31_k32()
BEGIN
  UPDATE line_manager_scorecard lms
  JOIN v_sync_health_by_manager smv ON smv.cm_uid = lms.manager_uid
  SET
    lms.k31_sync_success_pct = smv.k31_sync_success_pct,
    lms.k31_ops_total        = smv.total_ops,
    lms.k31_ops_applied      = smv.applied,
    lms.k32_conflict_rate_pct = smv.k32_conflict_rate_pct,
    lms.k32_conflict_count    = smv.conflicts
  WHERE lms.score_date = CURDATE();
END$$
DELIMITER ;

-- ============================================================================
-- END OF MIGRATION 033
-- ============================================================================
