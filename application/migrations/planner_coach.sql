-- migration 018: Planner Coach AI agent
-- Date: 2026-05-16
-- Author: STEM Learning rev 10
-- Surface: live planning suggestions + discipline report + execution live monitor + day end report
-- Tables: 4 new. No write contract changes to existing planner_v2 tables.
-- Bands enforced upstream by sp_check_band_lock (migration 017_4). No new band logic here.
--
-- Plain English. Production typos preserved where they appear in joined columns.

START TRANSACTION;

-- -----------------------------------------------------------------------------
-- 1. planner_coach_live_log
-- Tracks live planning behaviour during the 17:30 to 18:30 plan_window band.
-- One row per BD per refresh (every 15 min via cron) OR per significant edit event.
-- -----------------------------------------------------------------------------
CREATE TABLE planner_coach_live_log (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bd_uid INT UNSIGNED NOT NULL,
    plan_date DATE NOT NULL COMMENT 'tomorrow date the BD is planning for',
    snapshot_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_planning TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if BD has TaskPlannerV2 open in last 60 sec',
    minutes_in_planner INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'cumulative minutes today',
    tasks_added INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_removed INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_edited INT UNSIGNED NOT NULL DEFAULT 0,
    minute_budget_used INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'sum of actiontype minute budgets',
    minute_budget_ceiling INT UNSIGNED NOT NULL DEFAULT 540 COMMENT 'rev 9 cap',
    mandatory_tasks_picked INT UNSIGNED NOT NULL DEFAULT 0,
    mandatory_tasks_total INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'count of mandatory filter chip hits available',
    suggestion_text TEXT NULL COMMENT 'plain English nudge emitted to this BD at this snapshot',
    peer_count_planning INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'how many other BDs in same cluster have planner open right now',
    PRIMARY KEY (id),
    KEY idx_bd_date (bd_uid, plan_date),
    KEY idx_snapshot (snapshot_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Planner Coach live planning snapshots, every 15 min during 17:30 to 18:30';

-- -----------------------------------------------------------------------------
-- 2. planner_coach_discipline
-- One row per BD per plan_date written after the 18:30 cutoff. Grades the planning act.
-- -----------------------------------------------------------------------------
CREATE TABLE planner_coach_discipline (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bd_uid INT UNSIGNED NOT NULL,
    plan_date DATE NOT NULL COMMENT 'tomorrow date the BD planned for',
    computed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME NULL,
    submitted_by_cutoff TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if submit before 18:30 IST',
    minutes_to_submit INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'minutes from first planner open to submit',
    edit_count INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_planned INT UNSIGNED NOT NULL DEFAULT 0,
    minute_budget_used INT UNSIGNED NOT NULL DEFAULT 0,
    mandatory_tasks_picked INT UNSIGNED NOT NULL DEFAULT 0,
    mandatory_tasks_total INT UNSIGNED NOT NULL DEFAULT 0,
    mandatory_coverage_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    late_cutoff_minutes INT NOT NULL DEFAULT 0 COMMENT 'positive if late, zero if on time',
    same_day_flag TINYINT(1) NOT NULL DEFAULT 0,
    grade_score DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'percent 0 to 100',
    grade_letter VARCHAR(2) NOT NULL DEFAULT 'D' COMMENT 'A+ A B C D mirroring migration 012 thresholds',
    nudge_text TEXT NULL COMMENT 'plain English coach feedback',
    PRIMARY KEY (id),
    UNIQUE KEY uniq_bd_plandate (bd_uid, plan_date),
    KEY idx_grade (grade_letter)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Planner Coach discipline grade per BD per plan_date, written post 18:30 cutoff';

-- -----------------------------------------------------------------------------
-- 3. planner_coach_execution
-- Live execution monitor, populated every 30 min during 10:00 to 18:30 next day.
-- Compares the approved plan against tblcallevents actuals.
-- One row per BD per (plan_date, snapshot_at).
-- -----------------------------------------------------------------------------
CREATE TABLE planner_coach_execution (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bd_uid INT UNSIGNED NOT NULL,
    plan_date DATE NOT NULL COMMENT 'the day being executed, equals todays date',
    snapshot_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    current_band ENUM('manual','auto','plan_window','closed') NOT NULL,
    tasks_planned INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_started INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_completed INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_cancelled INT UNSIGNED NOT NULL DEFAULT 0,
    completion_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    minutes_idle INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'minutes since last tblcallevents row',
    late_start_flag TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if no task started by 10:30',
    skip_no_cancel_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'tasks past start time with no event and no cancel',
    receipt_missing_count INT UNSIGNED NOT NULL DEFAULT 0,
    mom_pending_count INT UNSIGNED NOT NULL DEFAULT 0,
    nudge_emitted TINYINT(1) NOT NULL DEFAULT 0,
    nudge_text TEXT NULL,
    cm_escalated TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_bd_date (bd_uid, plan_date),
    KEY idx_snapshot (snapshot_at),
    KEY idx_late_start (late_start_flag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Planner Coach execution live snapshots, every 30 min during the day';

-- -----------------------------------------------------------------------------
-- 4. planner_coach_day_end
-- End-of-day summary row written at 18:30 closure. One per BD per plan_date.
-- -----------------------------------------------------------------------------
CREATE TABLE planner_coach_day_end (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bd_uid INT UNSIGNED NOT NULL,
    plan_date DATE NOT NULL,
    closed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tasks_planned INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_completed INT UNSIGNED NOT NULL DEFAULT 0,
    tasks_cancelled INT UNSIGNED NOT NULL DEFAULT 0,
    completion_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    time_variance_min INT NOT NULL DEFAULT 0 COMMENT 'actual minus planned minutes',
    cost_planned_rs DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    cost_actual_rs DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    cost_variance_pct DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    mom_coverage_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'percent of meetings with approved MoM',
    receipt_coverage_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    purpose_achieved_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    day_grade_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    day_grade_letter VARCHAR(2) NOT NULL DEFAULT 'D',
    headline_text TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_bd_plandate (bd_uid, plan_date),
    KEY idx_grade (day_grade_letter)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Planner Coach day end summary, written at 18:30 closure';

-- -----------------------------------------------------------------------------
-- Views: feed the 7:30 BD audit cron and the 11 AM presence cron.
-- -----------------------------------------------------------------------------

CREATE OR REPLACE VIEW v_planner_coach_yesterday AS
SELECT
    d.bd_uid,
    u.fullname AS bd_name,
    u.cluster,
    d.plan_date,
    d.submitted_by_cutoff,
    d.late_cutoff_minutes,
    d.same_day_flag,
    d.minutes_to_submit,
    d.edit_count,
    d.mandatory_coverage_pct,
    d.grade_score,
    d.grade_letter,
    d.nudge_text
FROM planner_coach_discipline d
JOIN user u ON u.uid = d.bd_uid
WHERE d.plan_date = CURDATE() - INTERVAL 1 DAY;

CREATE OR REPLACE VIEW v_planner_coach_live_now AS
SELECT
    l.bd_uid,
    u.fullname AS bd_name,
    u.cluster,
    l.plan_date,
    l.snapshot_at,
    l.is_planning,
    l.minutes_in_planner,
    l.tasks_added,
    l.mandatory_tasks_picked,
    l.mandatory_tasks_total,
    l.peer_count_planning,
    l.suggestion_text
FROM planner_coach_live_log l
JOIN user u ON u.uid = l.bd_uid
WHERE l.snapshot_at >= NOW() - INTERVAL 30 MINUTE;

CREATE OR REPLACE VIEW v_planner_coach_execution_now AS
SELECT
    e.bd_uid,
    u.fullname AS bd_name,
    u.cluster,
    e.plan_date,
    e.snapshot_at,
    e.current_band,
    e.completion_pct,
    e.minutes_idle,
    e.late_start_flag,
    e.skip_no_cancel_count,
    e.receipt_missing_count,
    e.mom_pending_count,
    e.nudge_emitted,
    e.cm_escalated
FROM planner_coach_execution e
JOIN user u ON u.uid = e.bd_uid
WHERE e.snapshot_at >= NOW() - INTERVAL 45 MINUTE;

CREATE OR REPLACE VIEW v_planner_coach_day_end_yesterday AS
SELECT
    d.bd_uid,
    u.fullname AS bd_name,
    u.cluster,
    d.plan_date,
    d.completion_pct,
    d.time_variance_min,
    d.cost_variance_pct,
    d.mom_coverage_pct,
    d.receipt_coverage_pct,
    d.purpose_achieved_pct,
    d.day_grade_score,
    d.day_grade_letter,
    d.headline_text
FROM planner_coach_day_end d
JOIN user u ON u.uid = d.bd_uid
WHERE d.plan_date = CURDATE() - INTERVAL 1 DAY;

COMMIT;

-- -----------------------------------------------------------------------------
-- Rollback (manual): DROP VIEW v_planner_coach_day_end_yesterday; DROP VIEW v_planner_coach_execution_now;
--                    DROP VIEW v_planner_coach_live_now; DROP VIEW v_planner_coach_yesterday;
--                    DROP TABLE planner_coach_day_end; DROP TABLE planner_coach_execution;
--                    DROP TABLE planner_coach_discipline; DROP TABLE planner_coach_live_log;
-- -----------------------------------------------------------------------------
