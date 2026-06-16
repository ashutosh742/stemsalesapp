-- ====================================================================
-- STEM Meeting-to-Money (M2M) Assurance - additive migration
-- Date: 2026-06-16
-- Mode: STRICTLY ADDITIVE. New tables + new nullable columns only.
-- NO drops, NO alters of existing columns, NO data destruction.
-- Idempotent: safe to run repeatedly (CREATE TABLE IF NOT EXISTS +
-- INFORMATION_SCHEMA guarded ADD COLUMN via a temporary procedure).
-- SQL mode assumed STRICT_TRANS_TABLES: every NOT NULL column has a
-- default or is nullable.
-- ====================================================================

-- --------------------------------------------------------------------
-- 1) Config table (nothing hardcoded - SLA days, weights, thresholds)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `m2m_config` (
  `cfg_key`    VARCHAR(64)  NOT NULL,
  `cfg_value`  VARCHAR(255) NOT NULL DEFAULT '',
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cfg_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed defaults. INSERT IGNORE keeps existing tuned values on re-run
-- (idempotent: never clobbers a value an admin already changed).
INSERT IGNORE INTO `m2m_config` (`cfg_key`, `cfg_value`) VALUES
  ('proposal_sla_working_days', '5'),
  ('quality_score_threshold',   '70'),
  ('weight_rp',                 '40'),
  ('weight_fit',                '20'),
  ('weight_purpose',            '20'),
  ('weight_mom',                '20'),
  ('dq8_count',                 '3'),
  ('manager_touch_sla_days',    '7');

-- --------------------------------------------------------------------
-- 2) Gate C manager closure ownership (one row per active Status 5-8 lead)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `m2m_manager_closure` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `cid_id`           INT          NOT NULL DEFAULT 0,
  `lead_status`      INT          NULL,
  `manager_uid`      INT          NULL,
  `manager_role`     VARCHAR(16)  NULL,
  `last_touch_date`  DATE         NULL,
  `next_action_text` TEXT         NULL,
  `next_action_date` DATE         NULL,
  `close_or_kill_date` DATE       NULL,
  `verdict`          ENUM('open','won','killed','pending') NOT NULL DEFAULT 'open',
  `idle_days`        INT          NOT NULL DEFAULT 0,
  `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cid` (`cid_id`),
  KEY `idx_manager` (`manager_uid`),
  KEY `idx_status`  (`lead_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 3) Disqualifier ledger (extends DQ1-DQ7; DQ8/DQ9/DQ10 auto, no-appeal)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `m2m_disqualifier_log` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `dq_code`        ENUM('DQ8','DQ9','DQ10') NOT NULL,
  `subject_uid`    INT          NOT NULL DEFAULT 0,
  `subject_role`   VARCHAR(16)  NULL,
  `cid_id`         INT          NULL,
  `period_month`   CHAR(7)      NOT NULL DEFAULT '',
  `reason`         TEXT         NULL,
  `source_tracker` VARCHAR(48)  NULL,
  `triggered_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `auto`           INT          NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  -- idempotent triggers: at most one row per (code, subject, cid, month)
  UNIQUE KEY `uniq_dq` (`dq_code`, `subject_uid`, `cid_id`, `period_month`),
  KEY `idx_period` (`period_month`),
  KEY `idx_subject` (`subject_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 4) Gate A new nullable columns on existing mom_data (guarded add)
--    Reuse existing columns where exact-match; only add what is new.
--    proposal_committed_date is NEW (differs from expected_close_date).
-- --------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `m2m_add_col`;
DELIMITER //
CREATE PROCEDURE `m2m_add_col`(
  IN p_table VARCHAR(64),
  IN p_col   VARCHAR(64),
  IN p_def   VARCHAR(255)
)
BEGIN
  DECLARE col_exists INT DEFAULT 0;
  SELECT COUNT(*) INTO col_exists
    FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME   = p_table
     AND COLUMN_NAME  = p_col;
  IF col_exists = 0 THEN
    SET @ddl = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_col, '` ', p_def);
    PREPARE stmt FROM @ddl;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END //
DELIMITER ;

CALL `m2m_add_col`('mom_data', 'rp_present',             'TINYINT NULL');
CALL `m2m_add_col`('mom_data', 'rp_plan_to_reach',       'TEXT NULL');
CALL `m2m_add_col`('mom_data', 'prospect_funded',        'TINYINT NULL');
CALL `m2m_add_col`('mom_data', 'funded_lever',           'VARCHAR(64) NULL');
CALL `m2m_add_col`('mom_data', 'purpose_achieved',       'TINYINT NULL');
CALL `m2m_add_col`('mom_data', 'client_commitment',      "ENUM('hard','soft','none') NULL");
CALL `m2m_add_col`('mom_data', 'next_step_text',         'TEXT NULL');
CALL `m2m_add_col`('mom_data', 'next_step_owner_uid',    'INT NULL');
CALL `m2m_add_col`('mom_data', 'next_step_date',         'DATE NULL');
CALL `m2m_add_col`('mom_data', 'proposal_committed_date','DATE NULL');
-- Gate B sent-date mirror (Gate B writes proposal_shared_date which already
-- exists; we add a dedicated m2m_proposal_sent_date so the M2M clock never
-- competes with any existing writer of proposal_shared_date).
CALL `m2m_add_col`('mom_data', 'm2m_proposal_sent_date', 'DATE NULL');

DROP PROCEDURE IF EXISTS `m2m_add_col`;

-- ====================================================================
-- END M2M Assurance migration (additive, idempotent)
-- ====================================================================
