-- ============================================================================
-- STEM CRM - Migration 024
-- Funnel Hygiene + DM Verification + Line Manager Accountability
-- ============================================================================
-- Scope: cstatus 3 onwards (Tentative, Positive, Proposal, RPEM, Very Positive)
-- Stages 1, 2 are BD-only (manager not gated)
-- Stages 12, 13 are closure (different surface, covered by migration 022)
--
-- Tasks are always planned against cid_id (init_call.id), which is the
-- school/company record. cstatus is an attribute on that init_call row.
-- "Funnel movement" means cstatus changing on a CID.
--
-- Author: STEM ops
-- Date: 2026-05-17
-- Deploy hold: Mon 18 May 2026 (GitHub access)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. FUNNEL CHANGE LOG (Rule 1)
-- Every cstatus transition on every CID. Source of truth for daily email.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS funnel_change_log (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id          INT UNSIGNED NOT NULL COMMENT 'init_call.id',
  bd_uid          INT UNSIGNED NOT NULL COMMENT 'user.uid of mainbd',
  cm_uid          INT UNSIGNED DEFAULT NULL COMMENT 'cluster manager from reporting_hierarchy',
  rm_uid          INT UNSIGNED DEFAULT NULL,
  from_cstatus    TINYINT UNSIGNED NOT NULL,
  to_cstatus      TINYINT UNSIGNED NOT NULL,
  changed_by_uid  INT UNSIGNED NOT NULL,
  change_source   VARCHAR(64) NOT NULL COMMENT 'task_submit, manual_update, bulk_admin, system_auto',
  fbudget_rs      DECIMAL(14,2) DEFAULT 0,
  closed_value_rs DECIMAL(14,2) DEFAULT 0,
  notes           TEXT,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_cid (cid_id),
  KEY idx_bd_date (bd_uid, created_at),
  KEY idx_cm_date (cm_uid, created_at),
  KEY idx_to_status (to_cstatus, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trigger: every UPDATE on init_call where cstatus changes writes a row.
DELIMITER $$
DROP TRIGGER IF EXISTS trg_init_call_funnel_change$$
CREATE TRIGGER trg_init_call_funnel_change
AFTER UPDATE ON init_call
FOR EACH ROW
BEGIN
  IF OLD.cstatus IS NOT NULL AND NEW.cstatus IS NOT NULL
     AND OLD.cstatus <> NEW.cstatus THEN
    INSERT INTO funnel_change_log
      (cid_id, bd_uid, cm_uid, rm_uid, from_cstatus, to_cstatus,
       changed_by_uid, change_source, fbudget_rs, closed_value_rs)
    VALUES
      (NEW.id, NEW.mainbd,
       (SELECT parent_uid FROM reporting_hierarchy WHERE employee_uid = NEW.mainbd AND active = 1 LIMIT 1),
       (SELECT skip_parent_uid FROM reporting_hierarchy WHERE employee_uid = NEW.mainbd AND active = 1 LIMIT 1),
       OLD.cstatus, NEW.cstatus,
       COALESCE(@change_user_id, NEW.mainbd),
       COALESCE(@change_source, 'system_auto'),
       COALESCE(NEW.fbudget, 0),
       CASE WHEN NEW.cstatus = 12 THEN COALESCE(NEW.fbudget, 0) ELSE 0 END);
  END IF;
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- 2. NO-PURPOSE TASK LOG (Rule 2)
-- tblcallevents rows where purpose_id is null or 0 or blank.
-- Detected nightly by stem_funnel_hygiene_model.php.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS no_purpose_task_log (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_id        INT UNSIGNED NOT NULL COMMENT 'tblcallevents.id',
  cid_id          INT UNSIGNED NOT NULL,
  bd_uid          INT UNSIGNED NOT NULL,
  cm_uid          INT UNSIGNED DEFAULT NULL,
  actiontype_id   INT UNSIGNED NOT NULL,
  event_date      DATE NOT NULL,
  detected_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved        TINYINT(1) NOT NULL DEFAULT 0,
  resolved_at     DATETIME DEFAULT NULL,
  resolved_by_uid INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_event (event_id),
  KEY idx_bd (bd_uid, detected_at),
  KEY idx_cm (cm_uid, detected_at),
  KEY idx_resolved (resolved, detected_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 3. PHANTOM TASK LOG (Rule 3)
-- Task planned but no work captured: no MoM, no photo, no GPS, no completion
-- after planned date. Detected nightly.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS phantom_task_log (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_id            INT UNSIGNED NOT NULL,
  cid_id              INT UNSIGNED NOT NULL,
  bd_uid              INT UNSIGNED NOT NULL,
  cm_uid              INT UNSIGNED DEFAULT NULL,
  actiontype_id       INT UNSIGNED NOT NULL,
  planned_date        DATE NOT NULL,
  has_mom             TINYINT(1) NOT NULL DEFAULT 0,
  has_photo           TINYINT(1) NOT NULL DEFAULT 0,
  has_gps             TINYINT(1) NOT NULL DEFAULT 0,
  has_completion      TINYINT(1) NOT NULL DEFAULT 0,
  days_since_planned  SMALLINT NOT NULL,
  detected_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved            TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_event (event_id),
  KEY idx_bd (bd_uid, detected_at),
  KEY idx_cm (cm_uid, detected_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 4. WEEKLY TOUCH GAP (Rule 4)
-- CID in cstatus 3, 6, 7, 8, 9 with zero tblcallevents in 7 days.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS weekly_touch_gap (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id               INT UNSIGNED NOT NULL,
  bd_uid               INT UNSIGNED NOT NULL,
  cm_uid               INT UNSIGNED DEFAULT NULL,
  cstatus              TINYINT UNSIGNED NOT NULL,
  last_task_date       DATE DEFAULT NULL,
  days_since_last_task SMALLINT NOT NULL,
  detected_at          DATE NOT NULL,
  resolved             TINYINT(1) NOT NULL DEFAULT 0,
  resolved_at          DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cid_date (cid_id, detected_at),
  KEY idx_bd (bd_uid, detected_at),
  KEY idx_cm (cm_uid, detected_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 5. 22-TASK STAGNANCY LOG (Rule 5, flag and email only, no block)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS stagnancy_22_log (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id              INT UNSIGNED NOT NULL,
  bd_uid              INT UNSIGNED NOT NULL,
  cm_uid              INT UNSIGNED DEFAULT NULL,
  rm_uid              INT UNSIGNED DEFAULT NULL,
  cstatus             TINYINT UNSIGNED NOT NULL,
  task_count          SMALLINT NOT NULL COMMENT 'count of tblcallevents on this CID',
  days_in_cstatus     SMALLINT NOT NULL,
  cstatus_locked_at   DATETIME DEFAULT NULL COMMENT 'when cstatus last changed',
  first_task_date     DATE DEFAULT NULL,
  detected_at         DATE NOT NULL,
  resolved            TINYINT(1) NOT NULL DEFAULT 0,
  resolved_at         DATETIME DEFAULT NULL,
  resolution_reason   VARCHAR(64) DEFAULT NULL COMMENT 'cstatus_moved, marked_lost, manual_clear',
  PRIMARY KEY (id),
  UNIQUE KEY uq_cid_date (cid_id, detected_at),
  KEY idx_bd (bd_uid, detected_at),
  KEY idx_cm (cm_uid, detected_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 6. DM VERIFICATION (Rule 7, Apollo + LinkedIn)
-- Every new DM contact on a CID enters this queue.
-- CID cannot move to cstatus 6 until verdict is verified.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS dm_verification (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id              INT UNSIGNED NOT NULL,
  bd_uid              INT UNSIGNED NOT NULL,
  dm_name             VARCHAR(255) NOT NULL,
  dm_designation      VARCHAR(255) DEFAULT NULL,
  dm_email            VARCHAR(255) DEFAULT NULL,
  dm_phone            VARCHAR(64) DEFAULT NULL,
  company_name        VARCHAR(255) NOT NULL,

  -- Apollo enrichment
  apollo_checked      TINYINT(1) NOT NULL DEFAULT 0,
  apollo_match_score  TINYINT UNSIGNED DEFAULT NULL COMMENT '0-100',
  apollo_title        VARCHAR(255) DEFAULT NULL,
  apollo_company      VARCHAR(255) DEFAULT NULL,
  apollo_linkedin_url VARCHAR(512) DEFAULT NULL,
  apollo_payload      JSON DEFAULT NULL,
  apollo_checked_at   DATETIME DEFAULT NULL,

  -- LinkedIn cross-check
  linkedin_checked    TINYINT(1) NOT NULL DEFAULT 0,
  linkedin_match_score TINYINT UNSIGNED DEFAULT NULL,
  linkedin_title      VARCHAR(255) DEFAULT NULL,
  linkedin_company    VARCHAR(255) DEFAULT NULL,
  linkedin_payload    JSON DEFAULT NULL,
  linkedin_checked_at DATETIME DEFAULT NULL,

  -- Final verdict
  csr_keyword_found   TINYINT(1) NOT NULL DEFAULT 0
                      COMMENT '1 if title contains CSR, Sustainability, Foundation, ESG, Social Impact',
  combined_score      TINYINT UNSIGNED DEFAULT NULL,
  verdict             ENUM('pending','verified','doubtful','not_csr') NOT NULL DEFAULT 'pending',
  verdict_reason      VARCHAR(255) DEFAULT NULL,
  verdict_at          DATETIME DEFAULT NULL,
  verdict_by          ENUM('agent','cm_manual') DEFAULT NULL,

  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cid_dm (cid_id, dm_name),
  KEY idx_cid (cid_id),
  KEY idx_verdict (verdict, created_at),
  KEY idx_bd (bd_uid, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Block cstatus 6 promotion when DM not verified.
DELIMITER $$
DROP TRIGGER IF EXISTS trg_block_cstatus_6_unverified_dm$$
CREATE TRIGGER trg_block_cstatus_6_unverified_dm
BEFORE UPDATE ON init_call
FOR EACH ROW
BEGIN
  DECLARE v_unverified_count INT DEFAULT 0;
  IF NEW.cstatus = 6 AND OLD.cstatus <> 6 THEN
    SELECT COUNT(*) INTO v_unverified_count
      FROM dm_verification
      WHERE cid_id = NEW.id
        AND verdict IN ('pending','doubtful');
    IF v_unverified_count > 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot promote to cstatus 6: DM contact not verified by Apollo plus LinkedIn agent';
    END IF;
  END IF;
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- 7. EXTEND line_manager_scorecard for K8 (Rule 6)
-- Rebalanced weights: K1 13, K2 13, K3 13, K4 13, K5 9, K6 9, K7 15, K8 15
-- ----------------------------------------------------------------------------
ALTER TABLE line_manager_scorecard
  ADD COLUMN k8_funnel_hygiene_pct DECIMAL(5,2) DEFAULT NULL
    COMMENT 'percent of CIDs under manager without hygiene breaches'
    AFTER k7_escalation_pre_sla_pct,
  ADD COLUMN k8_stagnant_22_count INT UNSIGNED DEFAULT 0 AFTER k8_funnel_hygiene_pct,
  ADD COLUMN k8_weekly_gap_count INT UNSIGNED DEFAULT 0 AFTER k8_stagnant_22_count,
  ADD COLUMN k8_no_purpose_count INT UNSIGNED DEFAULT 0 AFTER k8_weekly_gap_count,
  ADD COLUMN k8_phantom_count INT UNSIGNED DEFAULT 0 AFTER k8_no_purpose_count,
  ADD COLUMN incentive_deduction_rs DECIMAL(10,2) DEFAULT 0 AFTER k8_phantom_count;

-- Default weight rebalance for FY27 quarters
UPDATE quarter_config
   SET k1_weight = 13,
       k2_weight = 13,
       k3_weight = 13,
       k4_weight = 13,
       k5_weight = 9,
       k6_weight = 9,
       k7_weight = 15,
       k8_weight = 15
 WHERE fiscal_year >= 27;

-- Add k8_weight column if not present (idempotent)
ALTER TABLE quarter_config
  ADD COLUMN IF NOT EXISTS k8_weight TINYINT UNSIGNED NOT NULL DEFAULT 15
    COMMENT 'Funnel Hygiene weight';

-- ----------------------------------------------------------------------------
-- 8. INCENTIVE DEDUCTION LOG (Rule 6 second leg)
-- CM deduct Rs 500 per breach, RM deduct Rs 1000 per breach.
-- Aggregated weekly into incentive_payout_log via 023.2 cadence engine.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS incentive_deduction (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  manager_uid        INT UNSIGNED NOT NULL,
  manager_role       ENUM('CM','RM') NOT NULL,
  breach_type        ENUM('stagnant_22','weekly_gap','no_purpose','phantom_task') NOT NULL,
  breach_ref_id      INT UNSIGNED NOT NULL COMMENT 'id from the respective _log table',
  cid_id             INT UNSIGNED NOT NULL,
  bd_uid             INT UNSIGNED NOT NULL,
  deduction_rs       DECIMAL(8,2) NOT NULL,
  week_start         DATE NOT NULL,
  week_end           DATE NOT NULL,
  applied_to_payout  TINYINT(1) NOT NULL DEFAULT 0,
  payout_log_id      INT UNSIGNED DEFAULT NULL,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_breach (breach_type, breach_ref_id, manager_uid),
  KEY idx_manager_week (manager_uid, week_start),
  KEY idx_applied (applied_to_payout)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- VIEWS
-- ----------------------------------------------------------------------------

-- Yesterday's funnel transitions, joined to BD and manager names
CREATE OR REPLACE VIEW v_funnel_changes_yesterday AS
SELECT
  f.id,
  f.cid_id,
  ic.compny_nm           AS school_name,
  f.bd_uid,
  ub.firstName            AS bd_name,
  f.cm_uid,
  uc.firstName            AS cm_name,
  f.rm_uid,
  ur.firstName            AS rm_name,
  f.from_cstatus,
  f.to_cstatus,
  f.change_source,
  f.fbudget_rs,
  f.closed_value_rs,
  f.created_at
FROM funnel_change_log f
LEFT JOIN init_call ic ON ic.id = f.cid_id
LEFT JOIN user ub      ON ub.uid = f.bd_uid
LEFT JOIN user uc      ON uc.uid = f.cm_uid
LEFT JOIN user ur      ON ur.uid = f.rm_uid
WHERE DATE(f.created_at) = CURDATE() - INTERVAL 1 DAY;

-- All open hygiene breaches per CM (the LM inbox query)
CREATE OR REPLACE VIEW v_cm_hygiene_inbox AS
SELECT 'stagnant_22' AS breach_type, cm_uid, bd_uid, cid_id, detected_at AS opened_at
  FROM stagnancy_22_log WHERE resolved = 0
UNION ALL
SELECT 'weekly_gap', cm_uid, bd_uid, cid_id, detected_at
  FROM weekly_touch_gap WHERE resolved = 0
UNION ALL
SELECT 'no_purpose', cm_uid, bd_uid, cid_id, DATE(detected_at)
  FROM no_purpose_task_log WHERE resolved = 0
UNION ALL
SELECT 'phantom_task', cm_uid, bd_uid, cid_id, DATE(detected_at)
  FROM phantom_task_log WHERE resolved = 0;

-- DM verification doubtful list
CREATE OR REPLACE VIEW v_dm_doubtful AS
SELECT
  d.id, d.cid_id, ic.compny_nm AS school_name, d.bd_uid, ub.firstName AS bd_name,
  d.dm_name, d.dm_designation, d.apollo_match_score, d.linkedin_match_score,
  d.combined_score, d.verdict_reason, d.created_at
FROM dm_verification d
LEFT JOIN init_call ic ON ic.id = d.cid_id
LEFT JOIN user ub      ON ub.uid = d.bd_uid
WHERE d.verdict IN ('doubtful','not_csr');

-- Hygiene breach count per BD this week (drives K8 numerator)
CREATE OR REPLACE VIEW v_bd_hygiene_breaches_week AS
SELECT
  bd_uid,
  cm_uid,
  SUM(CASE WHEN breach_type = 'stagnant_22'  THEN 1 ELSE 0 END) AS stagnant_22_count,
  SUM(CASE WHEN breach_type = 'weekly_gap'   THEN 1 ELSE 0 END) AS weekly_gap_count,
  SUM(CASE WHEN breach_type = 'no_purpose'   THEN 1 ELSE 0 END) AS no_purpose_count,
  SUM(CASE WHEN breach_type = 'phantom_task' THEN 1 ELSE 0 END) AS phantom_count,
  COUNT(*) AS total_breaches
FROM v_cm_hygiene_inbox
WHERE opened_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY bd_uid, cm_uid;

-- K8 per manager this week (denominator = active CIDs under manager in cstatus 3+)
CREATE OR REPLACE VIEW v_k8_per_manager_week AS
SELECT
  rh.parent_uid AS manager_uid,
  COUNT(DISTINCT ic.id) AS active_cid_count,
  COALESCE(SUM(b.total_breaches), 0) AS breach_count,
  CASE
    WHEN COUNT(DISTINCT ic.id) = 0 THEN NULL
    ELSE ROUND(100.0 * (COUNT(DISTINCT ic.id) - COALESCE(SUM(b.total_breaches),0))
               / COUNT(DISTINCT ic.id), 2)
  END AS k8_pct
FROM reporting_hierarchy rh
LEFT JOIN init_call ic
       ON ic.mainbd = rh.employee_uid
      AND ic.cstatus IN (3, 6, 7, 8, 9)
LEFT JOIN v_bd_hygiene_breaches_week b
       ON b.bd_uid = rh.employee_uid
WHERE rh.active = 1
GROUP BY rh.parent_uid;

-- ============================================================================
-- END OF MIGRATION 024
-- ============================================================================
