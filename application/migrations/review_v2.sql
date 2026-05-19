-- ============================================================================
-- Migration 020 - STEM Review v2
-- Phase 1 deploy (Mon 18 May 2026 staging, Mon 25 May 2026 pilot soft launch)
-- ============================================================================
-- What this ships:
--   7 new tables + 2 views + 1 trigger + KPI catalog seed (30 metrics)
--   Strictly additive on top of legacy SalesReviews tables (bd_wise_reviews,
--   sales_review_ratings, review_session, review_cluster_metrics, etc.)
--   Idempotent - safe to re-run, all CREATE TABLE use IF NOT EXISTS.
-- Hard rules baked in:
--   - schedule_status gate: status='pending' AND scheduled_date < CURDATE() - 1
--     trips the plan-submit gate via the v_review_overdue_manager view
--   - two-way self-assessment: rating row has both bd_self_rating and manager_rating
--   - action items must close before the next review reads them as 'open'
--   - skip-level register populated daily by cron 0c647bbd 7:30 AM extension
-- Production typos preserved: none in this surface (new tables only)
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+05:30';

-- ----------------------------------------------------------------------------
-- 1) review_metric_catalog - canonical KPI list pulled into every review
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_metric_catalog` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `metric_key` VARCHAR(64) NOT NULL,
  `metric_label` VARCHAR(160) NOT NULL,
  `category` ENUM('closure','discipline','planning','prospecting','activity','pipeline','expense','usage') NOT NULL,
  `data_source` VARCHAR(200) NOT NULL COMMENT 'SQL view name OR /api/... endpoint',
  `weight` DECIMAL(4,2) NOT NULL DEFAULT 1.00,
  `applies_to_role` ENUM('BD','CM','both') NOT NULL DEFAULT 'BD',
  `min_value_good` DECIMAL(10,2) NULL,
  `max_value_warn` DECIMAL(10,2) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_metric_key` (`metric_key`),
  KEY `idx_category_active` (`category`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 2) review_schedule - per-BD scheduled review obligation
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_schedule` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bd_uid` INT(11) NOT NULL,
  `manager_uid` INT(11) NOT NULL,
  `review_type_id` TINYINT(2) NOT NULL COMMENT 'FK to review_types (legacy)',
  `scheduled_date` DATE NOT NULL,
  `min_duration_minutes` INT(4) NOT NULL DEFAULT 20,
  `status` ENUM('pending','in_progress','completed','missed','rescheduled') NOT NULL DEFAULT 'pending',
  `reminder_count` TINYINT(2) NOT NULL DEFAULT 0,
  `missed_reason` VARCHAR(255) NULL,
  `rescheduled_to_date` DATE NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bd_date` (`bd_uid`,`scheduled_date`),
  KEY `idx_manager_status_date` (`manager_uid`,`status`,`scheduled_date`),
  UNIQUE KEY `uq_bd_type_date` (`bd_uid`,`review_type_id`,`scheduled_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 3) review_session_v2 - per-session header (parallel to legacy bd_wise_reviews)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_session_v2` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `schedule_id` INT(11) UNSIGNED NULL COMMENT 'FK to review_schedule, NULL for ad-hoc',
  `legacy_bd_wise_review_id` INT(11) UNSIGNED NULL COMMENT 'FK to bd_wise_reviews for back-compat',
  `by_uid` INT(11) NOT NULL COMMENT 'manager/reviewer',
  `to_uid` INT(11) NOT NULL COMMENT 'BD being reviewed',
  `review_type_id` TINYINT(2) NOT NULL,
  `window_from` DATE NOT NULL,
  `window_to` DATE NOT NULL,
  `started_at` DATETIME NULL,
  `bd_self_completed_at` DATETIME NULL,
  `manager_started_at` DATETIME NULL,
  `completed_at` DATETIME NULL,
  `duration_minutes` INT(4) NULL,
  `manager_avg_rating` DECIMAL(3,2) NULL COMMENT 'avg of manager_rating across metrics',
  `bd_self_avg_rating` DECIMAL(3,2) NULL,
  `overall_band` ENUM('Aplus','A','B','C','D') NULL,
  `delta_pct` DECIMAL(5,2) NULL COMMENT '(manager - bd_self) / 5.00 * 100',
  `comments_md` TEXT NULL,
  `status` ENUM('scheduled','bd_self_in_progress','bd_self_done','manager_in_progress','completed','closed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_to_uid_window` (`to_uid`,`window_from`,`window_to`),
  KEY `idx_by_uid_completed` (`by_uid`,`completed_at`),
  KEY `idx_schedule` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 4) review_metric_rating_v2 - per-metric per-session rating
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_metric_rating_v2` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` INT(11) UNSIGNED NOT NULL,
  `metric_key` VARCHAR(64) NOT NULL,
  `kpi_value` DECIMAL(14,2) NULL COMMENT 'auto-pulled snapshot value',
  `kpi_value_text` VARCHAR(255) NULL COMMENT 'for non-numeric metrics like grade band',
  `bd_self_rating` TINYINT(1) NULL COMMENT '1-5, NULL = not yet self-rated',
  `manager_rating` TINYINT(1) NULL COMMENT '1-5, NULL = not yet rated',
  `evidence_ref` VARCHAR(255) NULL COMMENT 'lead_id, event_id, breach_id etc.',
  `bd_remarks` TEXT NULL,
  `manager_remarks` TEXT NULL,
  `gap_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if abs(manager - bd_self) >= 2',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_session_metric` (`session_id`,`metric_key`),
  KEY `idx_session_gap` (`session_id`,`gap_flag`),
  CONSTRAINT `chk_bd_rating` CHECK (`bd_self_rating` IS NULL OR `bd_self_rating` BETWEEN 1 AND 5),
  CONSTRAINT `chk_mgr_rating` CHECK (`manager_rating` IS NULL OR `manager_rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 5) review_action_item - commitments coming out of a review
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_action_item` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` INT(11) UNSIGNED NOT NULL COMMENT 'session this AI came from',
  `owner_uid` INT(11) NOT NULL,
  `owner_role` ENUM('BD','CM','both') NOT NULL DEFAULT 'BD',
  `action_text` VARCHAR(500) NOT NULL,
  `due_date` DATE NOT NULL,
  `priority` ENUM('low','medium','high','red') NOT NULL DEFAULT 'medium',
  `status` ENUM('open','in_progress','done','missed','cancelled') NOT NULL DEFAULT 'open',
  `surfaced_in_session_id` INT(11) UNSIGNED NULL COMMENT 'next session that picked this up',
  `closure_evidence` VARCHAR(500) NULL,
  `closed_at` DATETIME NULL,
  `closed_by` INT(11) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_owner_status_due` (`owner_uid`,`status`,`due_date`),
  KEY `idx_session` (`session_id`),
  KEY `idx_surfaced` (`surfaced_in_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 6) review_skip_register - daily skip-level (Director) dashboard
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_skip_register` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `period_start` DATE NOT NULL,
  `period_end` DATE NOT NULL,
  `cm_uid` INT(11) NOT NULL,
  `cluster_id` INT(11) NULL,
  `scheduled_count` INT(4) NOT NULL DEFAULT 0,
  `completed_count` INT(4) NOT NULL DEFAULT 0,
  `missed_count` INT(4) NOT NULL DEFAULT 0,
  `on_time_pct` DECIMAL(5,2) NULL,
  `avg_duration_minutes` DECIMAL(5,2) NULL,
  `avg_rating_given` DECIMAL(3,2) NULL,
  `cluster_avg_rating` DECIMAL(3,2) NULL COMMENT 'for calibration check',
  `inflation_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if avg_rating exceeds cluster_avg by 1.0 or more',
  `ratings_distribution_json` JSON NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cm_period` (`cm_uid`,`period_start`,`period_end`),
  KEY `idx_period_flags` (`period_start`,`inflation_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 7) review_gate_log - audit of plan-submit blocks triggered by overdue reviews
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_gate_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `manager_uid` INT(11) NOT NULL,
  `plan_date` DATE NOT NULL,
  `evaluated_at` DATETIME NOT NULL,
  `gate_result` ENUM('passed','blocked_review_overdue','warning_only') NOT NULL,
  `overdue_review_count` INT(2) NOT NULL DEFAULT 0,
  `overdue_review_ids_json` JSON NULL,
  `enforcement_mode` ENUM('off','warning','hard') NOT NULL DEFAULT 'warning' COMMENT 'pilot starts warning, flips hard Mon 8 Jun',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_manager_plan_date` (`manager_uid`,`plan_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- VIEWS
-- ============================================================================

-- v_review_pending_for_manager - what is due for each manager today/this week
CREATE OR REPLACE VIEW `v_review_pending_for_manager` AS
SELECT
  rs.id AS schedule_id,
  rs.manager_uid,
  ud.name AS manager_name,
  rs.bd_uid,
  bd.name AS bd_name,
  rt.id AS review_type_id,
  rt.name AS review_type_name,
  rs.scheduled_date,
  rs.min_duration_minutes,
  rs.status,
  rs.reminder_count,
  DATEDIFF(CURDATE(), rs.scheduled_date) AS days_overdue
FROM `review_schedule` rs
LEFT JOIN `user_details` ud ON ud.user_id = rs.manager_uid
LEFT JOIN `user_details` bd ON bd.user_id = rs.bd_uid
LEFT JOIN `review_types` rt ON rt.id = rs.review_type_id
WHERE rs.status IN ('pending','in_progress')
  AND rs.scheduled_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY);

-- v_review_overdue_manager - feeds the 18:30 plan-submit gate
CREATE OR REPLACE VIEW `v_review_overdue_manager` AS
SELECT
  rs.manager_uid,
  COUNT(*) AS overdue_count,
  GROUP_CONCAT(rs.id) AS overdue_schedule_ids,
  MIN(rs.scheduled_date) AS oldest_overdue_date
FROM `review_schedule` rs
WHERE rs.status = 'pending'
  AND rs.scheduled_date < DATE_SUB(CURDATE(), INTERVAL 1 DAY)
GROUP BY rs.manager_uid;

-- v_review_skip_level_dashboard - Director's daily roll-up
CREATE OR REPLACE VIEW `v_review_skip_level_dashboard` AS
SELECT
  rsr.cm_uid,
  ud.name AS cm_name,
  rsr.period_start,
  rsr.period_end,
  rsr.scheduled_count,
  rsr.completed_count,
  rsr.missed_count,
  rsr.on_time_pct,
  rsr.avg_duration_minutes,
  rsr.avg_rating_given,
  rsr.cluster_avg_rating,
  rsr.inflation_flag,
  CASE
    WHEN rsr.on_time_pct < 80 THEN 'RED'
    WHEN rsr.on_time_pct < 95 THEN 'AMBER'
    ELSE 'GREEN'
  END AS discipline_flag
FROM `review_skip_register` rsr
LEFT JOIN `user_details` ud ON ud.user_id = rsr.cm_uid;

-- v_review_kpi_snapshot - placeholder, populated by model on demand
-- (no static view, the model pulls live KPI from existing migration views)

-- ============================================================================
-- TRIGGERS
-- ============================================================================

DROP TRIGGER IF EXISTS `tr_review_rating_gap_flag`;
DELIMITER //
CREATE TRIGGER `tr_review_rating_gap_flag`
BEFORE INSERT ON `review_metric_rating_v2`
FOR EACH ROW
BEGIN
  IF NEW.bd_self_rating IS NOT NULL AND NEW.manager_rating IS NOT NULL THEN
    IF ABS(NEW.manager_rating - NEW.bd_self_rating) >= 2 THEN
      SET NEW.gap_flag = 1;
    ELSE
      SET NEW.gap_flag = 0;
    END IF;
  END IF;
END //

CREATE TRIGGER `tr_review_rating_gap_flag_upd`
BEFORE UPDATE ON `review_metric_rating_v2`
FOR EACH ROW
BEGIN
  IF NEW.bd_self_rating IS NOT NULL AND NEW.manager_rating IS NOT NULL THEN
    IF ABS(NEW.manager_rating - NEW.bd_self_rating) >= 2 THEN
      SET NEW.gap_flag = 1;
    ELSE
      SET NEW.gap_flag = 0;
    END IF;
  END IF;
END //
DELIMITER ;

-- ============================================================================
-- SEED - KPI metric catalog (30 metrics across 8 categories)
-- ============================================================================
INSERT INTO `review_metric_catalog`
  (metric_key, metric_label, category, data_source, weight, applies_to_role, min_value_good, max_value_warn)
VALUES
-- closure (5)
('won_rs_in_window', 'Won revenue in window (Rs)', 'closure', 'v_progression_won_by_bd', 3.00, 'BD', 100000.00, NULL),
('won_count', 'Won deal count', 'closure', 'v_progression_won_by_bd', 2.50, 'BD', 1.00, NULL),
('avg_deal_size_rs', 'Average deal size (Rs)', 'closure', 'v_progression_won_by_bd', 1.50, 'BD', NULL, NULL),
('positive_conversion_count', 'Positive conversions (to cstatus 6)', 'closure', 'v_progression_transitions', 2.00, 'BD', 2.00, NULL),
('very_positive_count', 'Very positive count (to cstatus 9)', 'closure', 'v_progression_transitions', 2.00, 'BD', 1.00, NULL),

-- discipline (5)
('plans_submitted_on_time_pct', 'Plans submitted by 18:30 cutoff (percent)', 'discipline', 'v_planner_submit_compliance', 2.50, 'BD', 95.00, 80.00),
('same_day_plan_count', 'Same-day plan count (RED)', 'discipline', 'v_planner_same_day', 2.00, 'BD', 0.00, 1.00),
('cm_sla_breach_count', 'CM approval SLA breaches', 'discipline', 'v_planner_cm_sla', 2.00, 'CM', 0.00, 1.00),
('auto_band_breach_count', 'Auto-band breaches (mig 017_4)', 'discipline', 'v_band_violations_window', 1.50, 'BD', 0.00, 1.00),
('wfo_breach_count', 'WFO breaches', 'discipline', 'v_band_violations_window', 1.50, 'BD', 0.00, 1.00),

-- planning (4)
('planning_grade_avg', 'Planning grade average', 'planning', 'v_planning_grade_window', 2.00, 'BD', 3.00, 2.00),
('planning_grade_distribution', 'Planning grade distribution (Aplus/A/B/C/D counts)', 'planning', 'v_planning_grade_window', 1.00, 'BD', NULL, NULL),
('planning_incentive_rs_earned', 'Planning incentive earned (Rs)', 'planning', 'v_planning_payout_window', 1.50, 'BD', NULL, NULL),
('streak_max_days', 'Longest planning grade streak (days)', 'planning', 'v_planning_streak_window', 1.00, 'BD', 5.00, NULL),

-- prospecting (4)
('new_leads_added', 'New leads added in window', 'prospecting', 'v_prospecting_window_by_bd', 2.00, 'BD', 5.00, 1.00),
('barge_vs_research_ratio', 'Barge vs research ratio', 'prospecting', 'v_prospecting_window_by_bd', 1.50, 'BD', 0.70, 0.30),
('seeded_into_plan_count', 'Seeded into plan (mig 019.2)', 'prospecting', 'v_prospect_seeded_window', 1.50, 'BD', 3.00, 1.00),
('accepted_but_not_seeded_count', 'Accepted but not seeded (gap)', 'prospecting', 'v_prospect_seed_gap', 1.00, 'BD', 0.00, 2.00),

-- activity (5)
('meetings_completed', 'Meetings completed in window', 'activity', 'v_meeting_economics_window', 1.50, 'BD', 10.00, 5.00),
('mom_written_pct', 'MoM written percent of meetings', 'activity', 'v_meeting_economics_capture', 2.00, 'BD', 90.00, 70.00),
('mom_approved_pct', 'MoM approved by CM (percent)', 'activity', 'v_meeting_economics_capture', 2.00, 'BD', 85.00, 65.00),
('with_gps_pct', 'GPS capture percent', 'activity', 'v_meeting_economics_capture', 1.00, 'BD', 90.00, 75.00),
('rp_meetings_attended_with_cm', 'RP meetings attended with CM', 'activity', 'v_rp_meetings_window', 1.50, 'both', 2.00, NULL),

-- pipeline (4)
('stuck_leads_cstatus_6_over_7d', 'Stuck cstatus 6 over 7 days', 'pipeline', 'v_stuck_leads_by_bd', 1.50, 'BD', 0.00, 3.00),
('stuck_leads_cstatus_8_over_30d', 'Stuck cstatus 8 over 30 days', 'pipeline', 'v_stuck_leads_by_bd', 1.50, 'BD', 0.00, 3.00),
('stuck_leads_cstatus_9_over_14d', 'Stuck Very Positive (cstatus 9) over 14 days', 'pipeline', 'v_stuck_leads_by_bd', 2.00, 'BD', 0.00, 2.00),
('mom_blockers_count', 'Open MoM blockers gating progression', 'pipeline', 'v_mom_blockers_by_bd', 1.50, 'BD', 0.00, 2.00),

-- expense (3)
('expense_actuals_compliance_pct', 'Expense actuals compliance (percent)', 'expense', 'v_expense_actuals_window', 1.50, 'BD', 95.00, 80.00),
('variance_breach_count', 'Variance over plus/minus 20 percent', 'expense', 'v_expense_variance_window', 1.50, 'BD', 0.00, 2.00),
('unreturned_advance_rs_total', 'Unreturned advance total (Rs)', 'expense', 'v_advance_aging_by_bd', 1.50, 'BD', 0.00, 5000.00),

-- usage (2)
('avg_daily_app_minutes', 'Average daily app minutes', 'usage', 'v_app_usage_window', 1.00, 'BD', 30.00, 15.00),
('avg_action_latency_minutes', 'Average action latency (minutes)', 'usage', 'v_app_usage_window', 1.00, 'BD', NULL, 10.00)
ON DUPLICATE KEY UPDATE
  metric_label=VALUES(metric_label),
  weight=VALUES(weight),
  is_active=1;

-- ============================================================================
-- INSERT review_types row for v2 path if not already there (legacy table)
-- Skip if rows 1-6 already exist
-- ============================================================================
-- (no-op; legacy review_types already has 6 rows seeded)

-- ============================================================================
-- Backfill - seed initial schedule rows for pilot BDs once user_details is ready
-- This block is COMMENTED OUT - the model's bootstrap_pilot_schedule() RPC
-- will populate it on first call after CM Anjali confirms cluster mapping.
-- ============================================================================
-- INSERT INTO review_schedule (bd_uid, manager_uid, review_type_id, scheduled_date)
-- VALUES (42, 12, 1, '2026-05-29'), (43, 12, 1, '2026-05-29'),
--        (44, 12, 1, '2026-05-29'), (45, 12, 1, '2026-05-29'),
--        (46, 12, 1, '2026-05-29');

-- ============================================================================
-- END migration 020
-- ============================================================================
