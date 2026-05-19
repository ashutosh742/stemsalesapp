-- Migration 038: Day Ceremony v2 strict gate
-- Closes Day Start + Day Close discipline gaps audited in stem_production_discipline_audit_v1.md
-- Parallel tables only (suffix _v2). Production user_day, daily_planner, tblcallevents untouched.
-- Date: 19 May 2026. Author: STEM Computer agent.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1) Day ceremony v2 ledger (one row per user per day)
CREATE TABLE IF NOT EXISTS day_ceremony_v2 (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  ceremony_date DATE NOT NULL,
  -- Day Start fields
  ustart DATETIME NULL,
  ustart_lat DECIMAL(10,6) NULL,
  ustart_lng DECIMAL(10,6) NULL,
  ustart_photo_url VARCHAR(500) NULL,
  ustart_photo_exif_taken_at DATETIME NULL,
  ustart_radius_ok TINYINT(1) NOT NULL DEFAULT 0,
  ustart_photo_fresh_ok TINYINT(1) NOT NULL DEFAULT 0,
  ustart_prev_day_closed_ok TINYINT(1) NOT NULL DEFAULT 0,
  ustart_late_minutes INT NOT NULL DEFAULT 0,
  ustart_blocked_reason VARCHAR(255) NULL,
  -- Day Close fields
  uclose DATETIME NULL,
  uclose_lat DECIMAL(10,6) NULL,
  uclose_lng DECIMAL(10,6) NULL,
  uclose_photo_url VARCHAR(500) NULL,
  uclose_pending_breakdown_json TEXT NULL,
  uclose_blocked_reason VARCHAR(255) NULL,
  uclose_late_minutes INT NOT NULL DEFAULT 0,
  -- Audit
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_user_date (user_id, ceremony_date),
  KEY idx_ceremony_date (ceremony_date),
  KEY idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Day close pending breakdown log (one row per blocking item per attempt)
CREATE TABLE IF NOT EXISTS day_close_pending_v2 (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  ceremony_date DATE NOT NULL,
  block_category ENUM('task_not_closed','mom_missing','photo_missing','geotag_missing','expense_actuals_missing','cancellation_reason_missing','new_lead_reupdate','wfo_breach','other') NOT NULL,
  event_id INT NULL,
  lead_id INT NULL,
  detail VARCHAR(500) NULL,
  resolved_at DATETIME NULL,
  resolved_path VARCHAR(100) NULL,
  detected_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_user_date (user_id, ceremony_date),
  KEY idx_block_category (block_category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Day-Start home radius registry (per BD home/office anchor)
CREATE TABLE IF NOT EXISTS day_start_home_anchor_v2 (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  anchor_label VARCHAR(64) NOT NULL DEFAULT 'home',
  lat DECIMAL(10,6) NOT NULL,
  lng DECIMAL(10,6) NOT NULL,
  radius_km DECIMAL(4,1) NOT NULL DEFAULT 5.0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_user_label (user_id, anchor_label)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Day-Close late escalation log
CREATE TABLE IF NOT EXISTS day_close_late_log_v2 (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  ceremony_date DATE NOT NULL,
  expected_close_by TIME NOT NULL DEFAULT '21:30:00',
  detected_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  closed_at DATETIME NULL,
  escalated_to_uid INT NULL,
  notes VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_user_date (user_id, ceremony_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) Config rows
CREATE TABLE IF NOT EXISTS day_ceremony_config_v2 (
  config_key VARCHAR(64) NOT NULL,
  config_value VARCHAR(255) NOT NULL,
  description VARCHAR(255) NULL,
  PRIMARY KEY (config_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO day_ceremony_config_v2 (config_key, config_value, description) VALUES
('day_start_home_radius_km_default', '5.0', 'Default radius in km for Day Start home/office anchor'),
('day_start_photo_freshness_minutes', '5', 'Photo EXIF must be within N minutes of submission time'),
('day_start_expected_by', '09:30:00', 'Day Start expected by this time IST; later counts as late'),
('day_close_expected_by', '21:30:00', 'Day Close expected by this time IST; later goes to late log'),
('strict_gate_role_ids', '3,4,13,24,28', 'BD, PST, CM, ACM, RM enforced; Admin bypass with audit')
ON DUPLICATE KEY UPDATE config_value=VALUES(config_value);

-- 6) View: today's pending breakdown per user
CREATE OR REPLACE VIEW v_day_close_pending_today AS
SELECT
  p.user_id,
  u.name AS user_name,
  p.ceremony_date,
  p.block_category,
  COUNT(*) AS block_count,
  GROUP_CONCAT(p.event_id) AS event_ids
FROM day_close_pending_v2 p
LEFT JOIN user_details u ON u.user_id = p.user_id
WHERE p.ceremony_date = CURDATE() AND p.resolved_at IS NULL
GROUP BY p.user_id, p.ceremony_date, p.block_category;

SET FOREIGN_KEY_CHECKS = 1;
