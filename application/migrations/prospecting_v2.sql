-- ============================================================
-- Migration 019.2 - Prospecting Day-Before Plan Linkage
-- ============================================================
-- Wires the prospecting agent to STEM's day-before planning rhythm.
-- BDs submit tomorrow's plan by 18:30 IST today. The 7:00 AM Morning
-- Brief surfaces suggestions for tomorrow, BD accepts in-app, and the
-- system auto-seeds tomorrow's daily_planner row so the BD does not
-- need to re-find the school in a separate planner screen.
--
-- Status: STAGING ONLY. Do not run on production until GitHub access
-- lands Mon 18 May 2026.
--
-- Idempotent. Safe to run multiple times. All ALTER and INSERT
-- statements use IF NOT EXISTS or duplicate-tolerant patterns.
-- ============================================================

-- ============================================================
-- 1) location_prospect_run - anchor every run to a target plan_date
-- ============================================================
-- When the 7:00 AM cron triggers, target_plan_date is set to tomorrow
-- so accepted suggestions can be seeded into tomorrow's daily_planner.
-- If a BD manually triggers suggest_area after 18:30 IST (post plan cutoff),
-- the model defaults target_plan_date to day-after-tomorrow.

ALTER TABLE `location_prospect_run`
  ADD COLUMN IF NOT EXISTS `target_plan_date` DATE DEFAULT NULL
    COMMENT 'Plan date these suggestions should seed into (typically tomorrow)'
    AFTER `triggered_at`,
  ADD INDEX IF NOT EXISTS `idx_target_plan_date` (`target_plan_date`);

-- Backfill: any existing runs default to tomorrow's date (idempotent guard)
UPDATE `location_prospect_run`
SET `target_plan_date` = DATE_ADD(DATE(`triggered_at`), INTERVAL 1 DAY)
WHERE `target_plan_date` IS NULL;

-- ============================================================
-- 2) location_prospect_suggestion - per-row for_plan_date + seeded_planner_id
-- ============================================================
-- for_plan_date copies from parent run.target_plan_date at insert time.
-- seeded_planner_id is set when accept_and_seed creates a daily_planner row.

ALTER TABLE `location_prospect_suggestion`
  ADD COLUMN IF NOT EXISTS `for_plan_date` DATE DEFAULT NULL
    COMMENT 'Plan date this suggestion is for (copies parent run.target_plan_date)'
    AFTER `existing_init_call_id`,
  ADD COLUMN IF NOT EXISTS `seeded_planner_id` INT UNSIGNED DEFAULT NULL
    COMMENT 'daily_planner.id created when accept_and_seed fires'
    AFTER `accepted_init_call_id`,
  ADD COLUMN IF NOT EXISTS `seed_status` ENUM('not_seeded','seeded','seed_failed','seed_skipped') NOT NULL DEFAULT 'not_seeded'
    AFTER `seeded_planner_id`,
  ADD COLUMN IF NOT EXISTS `seed_error` VARCHAR(255) DEFAULT NULL
    AFTER `seed_status`,
  ADD INDEX IF NOT EXISTS `idx_for_plan_date` (`for_plan_date`),
  ADD INDEX IF NOT EXISTS `idx_seeded_planner` (`seeded_planner_id`);

-- Backfill: copy from parent run.target_plan_date where missing
UPDATE `location_prospect_suggestion` s
  JOIN `location_prospect_run` r ON r.id = s.run_id
SET s.for_plan_date = r.target_plan_date
WHERE s.for_plan_date IS NULL;

-- ============================================================
-- 3) View - v_prospect_seeded_tomorrow
--   Powers the 7:00 AM Morning Brief and the BD's "what I accepted
--   yesterday that landed in today's plan" check.
-- ============================================================

DROP VIEW IF EXISTS `v_prospect_seeded_tomorrow`;
CREATE VIEW `v_prospect_seeded_tomorrow` AS
SELECT
  r.bd_uid,
  u.name AS bd_name,
  r.target_plan_date AS plan_date,
  COUNT(s.id) AS suggested_count,
  SUM(CASE WHEN s.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_count,
  SUM(CASE WHEN s.seed_status = 'seeded' THEN 1 ELSE 0 END) AS seeded_count,
  SUM(CASE WHEN s.seed_status = 'seed_failed' THEN 1 ELSE 0 END) AS seed_failed_count,
  SUM(CASE WHEN s.status = 'dismissed' THEN 1 ELSE 0 END) AS dismissed_count
FROM `location_prospect_run` r
JOIN `location_prospect_suggestion` s ON s.run_id = r.id
LEFT JOIN `user` u ON u.uid = r.bd_uid
WHERE r.target_plan_date >= CURDATE()
GROUP BY r.bd_uid, u.name, r.target_plan_date;

-- ============================================================
-- 4) View - v_prospect_seed_gap
--   BDs who had suggestions but never seeded the planner for that date.
--   Surfaced in the 7:30 BD Audit so line managers can chase.
-- ============================================================

DROP VIEW IF EXISTS `v_prospect_seed_gap`;
CREATE VIEW `v_prospect_seed_gap` AS
SELECT
  r.bd_uid,
  u.name AS bd_name,
  r.target_plan_date AS plan_date,
  COUNT(s.id) AS suggested_count,
  SUM(CASE WHEN s.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_count,
  SUM(CASE WHEN s.seed_status = 'seeded' THEN 1 ELSE 0 END) AS seeded_count,
  (SUM(CASE WHEN s.status = 'accepted' THEN 1 ELSE 0 END)
   - SUM(CASE WHEN s.seed_status = 'seeded' THEN 1 ELSE 0 END)) AS accept_minus_seed
FROM `location_prospect_run` r
JOIN `location_prospect_suggestion` s ON s.run_id = r.id
LEFT JOIN `user` u ON u.uid = r.bd_uid
WHERE r.target_plan_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY r.bd_uid, u.name, r.target_plan_date
HAVING accept_minus_seed > 0;

-- ============================================================
-- 5) Optional safety - protect against same-day seeding sneaking in
-- ============================================================
-- Trigger refuses to set seeded_planner_id if the linked daily_planner.plan_date
-- equals today AND is_same_day_plan=0. Forces all auto-seeds to be next-day.

DELIMITER $$

DROP TRIGGER IF EXISTS `tr_prospect_seed_guard`$$
CREATE TRIGGER `tr_prospect_seed_guard`
BEFORE UPDATE ON `location_prospect_suggestion`
FOR EACH ROW
BEGIN
  DECLARE v_plan_date DATE;
  DECLARE v_is_same_day TINYINT;
  IF NEW.seeded_planner_id IS NOT NULL AND NEW.seeded_planner_id <> COALESCE(OLD.seeded_planner_id, 0) THEN
    SELECT plan_date, is_same_day_plan INTO v_plan_date, v_is_same_day
    FROM `daily_planner` WHERE id = NEW.seeded_planner_id LIMIT 1;
    IF v_plan_date IS NOT NULL AND v_plan_date = CURDATE() AND COALESCE(v_is_same_day, 0) = 0 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Prospect seed must be for a future plan_date (next-day or later)';
    END IF;
  END IF;
END$$

DELIMITER ;

-- ============================================================
-- 6) Seed audit log (lightweight, append-only)
--    Lets the 7:30 BD audit see when seeds happened and from which path
-- ============================================================
CREATE TABLE IF NOT EXISTS `prospect_seed_audit` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `suggestion_id` INT UNSIGNED NOT NULL,
  `run_id` INT UNSIGNED NOT NULL,
  `bd_uid` INT UNSIGNED NOT NULL,
  `init_call_id` INT UNSIGNED DEFAULT NULL,
  `seeded_planner_id` INT UNSIGNED DEFAULT NULL,
  `for_plan_date` DATE NOT NULL,
  `seed_result` ENUM('seeded','seed_failed','seed_skipped','seed_dup') NOT NULL,
  `seed_error` VARCHAR(255) DEFAULT NULL,
  `seeded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `source_channel` VARCHAR(40) NOT NULL DEFAULT 'app' COMMENT 'app, cron, manual_admin',
  PRIMARY KEY (`id`),
  KEY `idx_bd_date` (`bd_uid`, `for_plan_date`),
  KEY `idx_sugg` (`suggestion_id`),
  KEY `idx_run` (`run_id`),
  KEY `idx_seeded_at` (`seeded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DONE. Migration 019.2 is additive and reversible.
-- Rollback path:
--   DROP VIEW v_prospect_seeded_tomorrow;
--   DROP VIEW v_prospect_seed_gap;
--   DROP TRIGGER tr_prospect_seed_guard;
--   DROP TABLE prospect_seed_audit;
--   ALTER TABLE location_prospect_run DROP COLUMN target_plan_date;
--   ALTER TABLE location_prospect_suggestion
--     DROP COLUMN for_plan_date,
--     DROP COLUMN seeded_planner_id,
--     DROP COLUMN seed_status,
--     DROP COLUMN seed_error;
-- ============================================================
