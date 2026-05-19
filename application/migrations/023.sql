-- ============================================================================
-- STEM CRM - MIGRATION 023
-- CM as Actor + RM Upsell + Rs 200 crore Target Ladder
--
-- Pilot: Mon 25 May 2026
-- Builds on migration 022 (line manager accountability)
-- Plain English. 'Rs' for rupees. No em-dashes. No non-ASCII.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1) cm_daily_plan
--    Mirrors daily_planner but rows belong to CM, not BD.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cm_daily_plan (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cm_uid          INT NOT NULL,
  plan_date       DATE NOT NULL,
  task_kind       ENUM('joint_meeting','cm_call','anchor_visit','office_block','escalation_call') NOT NULL,
  linked_lead_id  INT NULL,
  linked_bd_uid   INT NULL,
  linked_event_id BIGINT NULL,
  start_time      TIME NULL,
  end_time        TIME NULL,
  notes           TEXT NULL,
  submitted_at    DATETIME NULL,
  submitted_by_cutoff TINYINT(1) DEFAULT 0,
  status          ENUM('planned','done','skipped','rolled') DEFAULT 'planned',
  done_at         DATETIME NULL,
  skip_reason     VARCHAR(120) NULL,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_cm_plan_event (cm_uid, plan_date, linked_event_id),
  KEY idx_cm_date (cm_uid, plan_date),
  KEY idx_lead (linked_lead_id),
  KEY idx_status_date (status, plan_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 2) cm_joint_meeting_log
--    One row per BD meeting at cstatus 8/9/12. Captures whether CM joined.
--    Auto-created by trigger when tblcallevents inserts a row at those stages.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cm_joint_meeting_log (
  id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id            BIGINT NOT NULL,
  lead_id             INT NOT NULL,
  bd_uid              INT NOT NULL,
  expected_cm_uid     INT NULL,
  cstatus_at_meeting  TINYINT NOT NULL,
  meeting_date        DATE NOT NULL,
  cm_joined           ENUM('yes','no','unset') DEFAULT 'unset',
  cm_actual_uid       INT NULL,
  not_joined_reason   ENUM('cm_busy_approvals','cm_on_leave','cm_not_informed','cm_cancelled','other') NULL,
  not_joined_note     VARCHAR(300) NULL,
  bd_reported_at      DATETIME NULL,
  cm_confirmed_at     DATETIME NULL,
  mandatory           TINYINT(1) DEFAULT 1,
  blame_split         ENUM('bd','cm','both','none') NULL,
  created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_event (event_id),
  KEY idx_cm_date (expected_cm_uid, meeting_date),
  KEY idx_bd_date (bd_uid, meeting_date),
  KEY idx_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 3) account_category_tag
--    One row per init_call lead. Tagged PSU / DMFT / ANCHOR / STANDARD.
--    Source: rule engine or manual RM override.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS account_category_tag (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lead_id         INT NOT NULL,
  category_code   ENUM('PSU','DMFT','ANCHOR','STANDARD') NOT NULL DEFAULT 'STANDARD',
  source          ENUM('rule','manual','imported') NOT NULL DEFAULT 'rule',
  confidence      DECIMAL(3,2) DEFAULT 0.50,
  matched_keyword VARCHAR(120) NULL,
  set_manual      TINYINT(1) DEFAULT 0,
  set_by_uid      INT NULL,
  reason_note     VARCHAR(300) NULL,
  tagged_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
  reviewed_at     DATETIME NULL,
  UNIQUE KEY uniq_lead (lead_id),
  KEY idx_cat (category_code),
  KEY idx_source (source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 4) rm_upsell_pipeline
--    Cached view-like table refreshed nightly for fast RM screen load.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rm_upsell_pipeline (
  id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rm_uid            INT NOT NULL,
  lead_id           INT NOT NULL,
  category_code     ENUM('PSU','DMFT','ANCHOR') NOT NULL,
  current_cstatus   TINYINT NOT NULL,
  bd_owner_uid      INT NULL,
  cm_owner_uid      INT NULL,
  school_name       VARCHAR(255) NULL,
  compny_loction    VARCHAR(120) NULL,
  proposal_budget_rs BIGINT NULL,
  last_rm_touch_at  DATETIME NULL,
  days_since_rm_touch INT GENERATED ALWAYS AS (
                          CASE WHEN last_rm_touch_at IS NULL THEN 999
                               ELSE DATEDIFF(NOW(), last_rm_touch_at) END
                       ) STORED,
  -- PSU specific
  psu_tender_deadline DATE NULL,
  psu_tender_ref      VARCHAR(80) NULL,
  -- DMFT specific
  dmft_district       VARCHAR(80) NULL,
  dmft_fund_cycle_q   VARCHAR(10) NULL,
  -- Anchor specific
  anchor_annual_spend_rs BIGINT NULL,
  anchor_renewal_due_date DATE NULL,
  anchor_cohort_count INT NULL,
  refreshed_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_rm_lead (rm_uid, lead_id),
  KEY idx_rm_cat (rm_uid, category_code),
  KEY idx_renewal (anchor_renewal_due_date),
  KEY idx_tender (psu_tender_deadline)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 5) revenue_target_matrix
--    The Rs 200 crore split. Cluster x Category x Quarter.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS revenue_target_matrix (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cluster_id      INT NOT NULL,
  cluster_name    VARCHAR(80) NOT NULL,
  category_code   ENUM('PSU','DMFT','ANCHOR','STANDARD') NOT NULL,
  fiscal_quarter  VARCHAR(8) NOT NULL,   -- e.g. 'FY27Q1'
  target_rs       BIGINT NOT NULL,
  owner_rm_uid    INT NULL,
  owner_cm_uid    INT NULL,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  notes           VARCHAR(300) NULL,
  UNIQUE KEY uniq_cell (cluster_id, category_code, fiscal_quarter),
  KEY idx_quarter (fiscal_quarter),
  KEY idx_owner_rm (owner_rm_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 6) revenue_actual_ledger
--    One row per Won deal (cstatus 9 to 12 via G4 signoff).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS revenue_actual_ledger (
  id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lead_id           INT NOT NULL,
  work_order_id     BIGINT NULL,
  signoff_id        BIGINT NULL,
  won_at            DATETIME NOT NULL,
  contract_value_rs BIGINT NOT NULL,
  cluster_id        INT NOT NULL,
  cluster_name      VARCHAR(80) NOT NULL,
  category_code     ENUM('PSU','DMFT','ANCHOR','STANDARD') NOT NULL,
  fiscal_quarter    VARCHAR(8) NOT NULL,
  bd_uid            INT NOT NULL,
  cm_uid            INT NULL,
  rm_uid            INT NULL,
  rm_led            TINYINT(1) DEFAULT 0,
  joint_meeting_count INT DEFAULT 0,
  created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_lead (lead_id),
  KEY idx_quarter_cat (fiscal_quarter, category_code),
  KEY idx_cluster_quarter (cluster_id, fiscal_quarter),
  KEY idx_rm (rm_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 7) Add columns to existing tables
-- ----------------------------------------------------------------------------

-- user table: confirm cluster_id + rm_uid linkage
ALTER TABLE user
  ADD COLUMN IF NOT EXISTS rm_uid INT NULL AFTER cluster_id,
  ADD COLUMN IF NOT EXISTS is_cluster_anchor TINYINT(1) DEFAULT 0;

-- mom_data: CM-joined toggle on every MoM at cstatus 8/9/12
ALTER TABLE mom_data
  ADD COLUMN IF NOT EXISTS cm_joined_self_report ENUM('yes','no','na') DEFAULT 'na',
  ADD COLUMN IF NOT EXISTS cm_joined_reason VARCHAR(80) NULL;

-- init_call: cache the category for fast filtering
ALTER TABLE init_call
  ADD COLUMN IF NOT EXISTS category_code ENUM('PSU','DMFT','ANCHOR','STANDARD') DEFAULT 'STANDARD',
  ADD COLUMN IF NOT EXISTS category_set_at DATETIME NULL;

-- line_manager_scorecard_daily (from migration 022): extend with CM activity KPIs
ALTER TABLE line_manager_scorecard_daily
  ADD COLUMN IF NOT EXISTS k8_joint_coverage_pct DECIMAL(5,2) NULL,
  ADD COLUMN IF NOT EXISTS k9_cm_plan_submitted TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS k10_cm_calls_week INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS k11_anchor_visits_quarter INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS k12_joint_mom_contribution_pct DECIMAL(5,2) NULL,
  -- RM specific
  ADD COLUMN IF NOT EXISTS k13_psu_touch_pct DECIMAL(5,2) NULL,
  ADD COLUMN IF NOT EXISTS k14_dmft_quarter_visits INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS k15_anchor_renewal_lock_pct DECIMAL(5,2) NULL,
  ADD COLUMN IF NOT EXISTS k16_rm_led_closure_pct DECIMAL(5,2) NULL;

-- ----------------------------------------------------------------------------
-- 8) VIEWS
-- ----------------------------------------------------------------------------

CREATE OR REPLACE VIEW v_cm_joint_coverage_today AS
SELECT
  j.expected_cm_uid AS cm_uid,
  j.meeting_date,
  COUNT(*) AS expected_mandatory,
  SUM(CASE WHEN j.cm_joined='yes' THEN 1 ELSE 0 END) AS joined,
  SUM(CASE WHEN j.cm_joined='no'  THEN 1 ELSE 0 END) AS missed,
  ROUND(100 * SUM(CASE WHEN j.cm_joined='yes' THEN 1 ELSE 0 END) / NULLIF(COUNT(*),0), 1) AS pct
FROM cm_joint_meeting_log j
WHERE j.mandatory=1 AND j.meeting_date=CURDATE()
GROUP BY j.expected_cm_uid, j.meeting_date;

CREATE OR REPLACE VIEW v_cm_joint_coverage_this_week AS
SELECT
  j.expected_cm_uid AS cm_uid,
  YEARWEEK(j.meeting_date, 1) AS yr_week,
  COUNT(*) AS expected_mandatory,
  SUM(CASE WHEN j.cm_joined='yes' THEN 1 ELSE 0 END) AS joined,
  SUM(CASE WHEN j.cm_joined='no'  THEN 1 ELSE 0 END) AS missed,
  ROUND(100 * SUM(CASE WHEN j.cm_joined='yes' THEN 1 ELSE 0 END) / NULLIF(COUNT(*),0), 1) AS pct
FROM cm_joint_meeting_log j
WHERE j.mandatory=1
  AND j.meeting_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY j.expected_cm_uid, YEARWEEK(j.meeting_date, 1);

CREATE OR REPLACE VIEW v_rm_pipeline_health AS
SELECT
  p.rm_uid,
  p.category_code,
  COUNT(*) AS active_leads,
  SUM(CASE WHEN p.days_since_rm_touch > 14 THEN 1 ELSE 0 END) AS stale_touches,
  ROUND(100 * SUM(CASE WHEN p.days_since_rm_touch <= 14 THEN 1 ELSE 0 END) / NULLIF(COUNT(*),0), 1) AS touch_freshness_pct,
  SUM(CASE WHEN p.category_code='ANCHOR' AND p.anchor_renewal_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) THEN 1 ELSE 0 END) AS anchor_renewals_due_90d
FROM rm_upsell_pipeline p
GROUP BY p.rm_uid, p.category_code;

CREATE OR REPLACE VIEW v_target_progress AS
SELECT
  t.id AS target_id,
  t.cluster_id,
  t.cluster_name,
  t.category_code,
  t.fiscal_quarter,
  t.target_rs,
  COALESCE(SUM(a.contract_value_rs), 0) AS actual_rs,
  t.target_rs - COALESCE(SUM(a.contract_value_rs), 0) AS gap_rs,
  ROUND(100 * COALESCE(SUM(a.contract_value_rs),0) / NULLIF(t.target_rs,0), 1) AS pct_achieved,
  COUNT(a.id) AS won_deals_count
FROM revenue_target_matrix t
LEFT JOIN revenue_actual_ledger a
  ON a.cluster_id=t.cluster_id
 AND a.category_code=t.category_code
 AND a.fiscal_quarter=t.fiscal_quarter
GROUP BY t.id, t.cluster_id, t.cluster_name, t.category_code, t.fiscal_quarter, t.target_rs;

CREATE OR REPLACE VIEW v_category_gap_critical AS
SELECT
  vp.target_id,
  vp.cluster_id,
  vp.cluster_name,
  vp.category_code,
  vp.fiscal_quarter,
  vp.target_rs,
  vp.actual_rs,
  vp.gap_rs,
  vp.pct_achieved,
  -- approximate day-of-quarter percent (FY27 = Apr 2026 - Mar 2027)
  CASE vp.fiscal_quarter
    WHEN 'FY27Q1' THEN ROUND(100 * GREATEST(0, LEAST(91, DATEDIFF(CURDATE(),'2026-04-01'))) / 91, 0)
    WHEN 'FY27Q2' THEN ROUND(100 * GREATEST(0, LEAST(92, DATEDIFF(CURDATE(),'2026-07-01'))) / 92, 0)
    WHEN 'FY27Q3' THEN ROUND(100 * GREATEST(0, LEAST(92, DATEDIFF(CURDATE(),'2026-10-01'))) / 92, 0)
    WHEN 'FY27Q4' THEN ROUND(100 * GREATEST(0, LEAST(90, DATEDIFF(CURDATE(),'2027-01-01'))) / 90, 0)
    ELSE 0
  END AS pct_of_quarter_elapsed,
  CASE
    WHEN vp.pct_achieved >= (
      CASE vp.fiscal_quarter
        WHEN 'FY27Q1' THEN ROUND(100 * GREATEST(0, LEAST(91, DATEDIFF(CURDATE(),'2026-04-01'))) / 91, 0)
        WHEN 'FY27Q2' THEN ROUND(100 * GREATEST(0, LEAST(92, DATEDIFF(CURDATE(),'2026-07-01'))) / 92, 0)
        WHEN 'FY27Q3' THEN ROUND(100 * GREATEST(0, LEAST(92, DATEDIFF(CURDATE(),'2026-10-01'))) / 92, 0)
        WHEN 'FY27Q4' THEN ROUND(100 * GREATEST(0, LEAST(90, DATEDIFF(CURDATE(),'2027-01-01'))) / 90, 0)
        ELSE 0
      END - 10) THEN 'on_pace'
    WHEN vp.pct_achieved >= (
      CASE vp.fiscal_quarter
        WHEN 'FY27Q1' THEN ROUND(100 * GREATEST(0, LEAST(91, DATEDIFF(CURDATE(),'2026-04-01'))) / 91, 0)
        WHEN 'FY27Q2' THEN ROUND(100 * GREATEST(0, LEAST(92, DATEDIFF(CURDATE(),'2026-07-01'))) / 92, 0)
        WHEN 'FY27Q3' THEN ROUND(100 * GREATEST(0, LEAST(92, DATEDIFF(CURDATE(),'2026-10-01'))) / 92, 0)
        WHEN 'FY27Q4' THEN ROUND(100 * GREATEST(0, LEAST(90, DATEDIFF(CURDATE(),'2027-01-01'))) / 90, 0)
        ELSE 0
      END - 25) THEN 'behind'
    ELSE 'critical'
  END AS pacing_flag
FROM v_target_progress vp
WHERE vp.fiscal_quarter IN ('FY27Q1','FY27Q2','FY27Q3','FY27Q4')
  AND CURDATE() >= '2026-04-01'
  AND CURDATE() <  '2027-04-01';

-- ----------------------------------------------------------------------------
-- 9) SEED DATA - Rs 200 crore target matrix DRAFT (founder must confirm)
--    8 clusters x 4 categories x 4 quarters = 128 rows
--    Mumbai loaded heavier in Q1 (pilot ramp). Others split evenly.
-- ----------------------------------------------------------------------------

-- Cluster annual share (in Rs lakhs for precision; convert to crore in app):
-- Mumbai      25 percent of national = Rs 50 cr   (5000 lakh)
-- Pune        12 percent = Rs 24 cr
-- Delhi       15 percent = Rs 30 cr
-- Bangalore   12 percent = Rs 24 cr
-- Hyderabad   10 percent = Rs 20 cr
-- Chennai     10 percent = Rs 20 cr
-- Kolkata     8 percent  = Rs 16 cr
-- Ahmedabad   8 percent  = Rs 16 cr
-- TOTAL                  = Rs 200 cr

-- Category annual split:
-- PSU      40 percent = Rs 80 cr
-- ANCHOR   30 percent = Rs 60 cr
-- DMFT     15 percent = Rs 30 cr
-- STANDARD 15 percent = Rs 30 cr

-- Quarterly distribution (front-load light, back-load heavy):
-- Q1 15%, Q2 25%, Q3 30.5%, Q4 29.5%

INSERT IGNORE INTO revenue_target_matrix
  (cluster_id, cluster_name, category_code, fiscal_quarter, target_rs)
VALUES
-- ===== Mumbai (cluster_id=1, 25% national) =====
-- Mumbai PSU: 50cr * 40% = 20cr/yr
(1,'Mumbai','PSU','FY27Q1',  30000000),
(1,'Mumbai','PSU','FY27Q2',  50000000),
(1,'Mumbai','PSU','FY27Q3',  61000000),
(1,'Mumbai','PSU','FY27Q4',  59000000),
-- Mumbai ANCHOR: 50cr * 30% = 15cr/yr
(1,'Mumbai','ANCHOR','FY27Q1', 22500000),
(1,'Mumbai','ANCHOR','FY27Q2', 37500000),
(1,'Mumbai','ANCHOR','FY27Q3', 45750000),
(1,'Mumbai','ANCHOR','FY27Q4', 44250000),
-- Mumbai DMFT: 50cr * 15% = 7.5cr/yr
(1,'Mumbai','DMFT','FY27Q1', 11250000),
(1,'Mumbai','DMFT','FY27Q2', 18750000),
(1,'Mumbai','DMFT','FY27Q3', 22875000),
(1,'Mumbai','DMFT','FY27Q4', 22125000),
-- Mumbai STANDARD: 7.5cr/yr
(1,'Mumbai','STANDARD','FY27Q1', 11250000),
(1,'Mumbai','STANDARD','FY27Q2', 18750000),
(1,'Mumbai','STANDARD','FY27Q3', 22875000),
(1,'Mumbai','STANDARD','FY27Q4', 22125000),

-- ===== Pune (cluster_id=2, 12% national = 24cr/yr) =====
(2,'Pune','PSU','FY27Q1',     14400000),
(2,'Pune','PSU','FY27Q2',     24000000),
(2,'Pune','PSU','FY27Q3',     29280000),
(2,'Pune','PSU','FY27Q4',     28320000),
(2,'Pune','ANCHOR','FY27Q1',  10800000),
(2,'Pune','ANCHOR','FY27Q2',  18000000),
(2,'Pune','ANCHOR','FY27Q3',  21960000),
(2,'Pune','ANCHOR','FY27Q4',  21240000),
(2,'Pune','DMFT','FY27Q1',     5400000),
(2,'Pune','DMFT','FY27Q2',     9000000),
(2,'Pune','DMFT','FY27Q3',    10980000),
(2,'Pune','DMFT','FY27Q4',    10620000),
(2,'Pune','STANDARD','FY27Q1', 5400000),
(2,'Pune','STANDARD','FY27Q2', 9000000),
(2,'Pune','STANDARD','FY27Q3',10980000),
(2,'Pune','STANDARD','FY27Q4',10620000),

-- ===== Delhi (cluster_id=3, 15% national = 30cr/yr) =====
(3,'Delhi','PSU','FY27Q1',     18000000),
(3,'Delhi','PSU','FY27Q2',     30000000),
(3,'Delhi','PSU','FY27Q3',     36600000),
(3,'Delhi','PSU','FY27Q4',     35400000),
(3,'Delhi','ANCHOR','FY27Q1',  13500000),
(3,'Delhi','ANCHOR','FY27Q2',  22500000),
(3,'Delhi','ANCHOR','FY27Q3',  27450000),
(3,'Delhi','ANCHOR','FY27Q4',  26550000),
(3,'Delhi','DMFT','FY27Q1',     6750000),
(3,'Delhi','DMFT','FY27Q2',    11250000),
(3,'Delhi','DMFT','FY27Q3',    13725000),
(3,'Delhi','DMFT','FY27Q4',    13275000),
(3,'Delhi','STANDARD','FY27Q1',6750000),
(3,'Delhi','STANDARD','FY27Q2',11250000),
(3,'Delhi','STANDARD','FY27Q3',13725000),
(3,'Delhi','STANDARD','FY27Q4',13275000),

-- ===== Bangalore (cluster_id=4, 12% = 24cr) =====
(4,'Bangalore','PSU','FY27Q1',     14400000),
(4,'Bangalore','PSU','FY27Q2',     24000000),
(4,'Bangalore','PSU','FY27Q3',     29280000),
(4,'Bangalore','PSU','FY27Q4',     28320000),
(4,'Bangalore','ANCHOR','FY27Q1',  10800000),
(4,'Bangalore','ANCHOR','FY27Q2',  18000000),
(4,'Bangalore','ANCHOR','FY27Q3',  21960000),
(4,'Bangalore','ANCHOR','FY27Q4',  21240000),
(4,'Bangalore','DMFT','FY27Q1',     5400000),
(4,'Bangalore','DMFT','FY27Q2',     9000000),
(4,'Bangalore','DMFT','FY27Q3',    10980000),
(4,'Bangalore','DMFT','FY27Q4',    10620000),
(4,'Bangalore','STANDARD','FY27Q1',5400000),
(4,'Bangalore','STANDARD','FY27Q2',9000000),
(4,'Bangalore','STANDARD','FY27Q3',10980000),
(4,'Bangalore','STANDARD','FY27Q4',10620000),

-- ===== Hyderabad (5), Chennai (6) -- 10% each = 20cr =====
(5,'Hyderabad','PSU','FY27Q1',12000000),(5,'Hyderabad','PSU','FY27Q2',20000000),(5,'Hyderabad','PSU','FY27Q3',24400000),(5,'Hyderabad','PSU','FY27Q4',23600000),
(5,'Hyderabad','ANCHOR','FY27Q1',9000000),(5,'Hyderabad','ANCHOR','FY27Q2',15000000),(5,'Hyderabad','ANCHOR','FY27Q3',18300000),(5,'Hyderabad','ANCHOR','FY27Q4',17700000),
(5,'Hyderabad','DMFT','FY27Q1',4500000),(5,'Hyderabad','DMFT','FY27Q2',7500000),(5,'Hyderabad','DMFT','FY27Q3',9150000),(5,'Hyderabad','DMFT','FY27Q4',8850000),
(5,'Hyderabad','STANDARD','FY27Q1',4500000),(5,'Hyderabad','STANDARD','FY27Q2',7500000),(5,'Hyderabad','STANDARD','FY27Q3',9150000),(5,'Hyderabad','STANDARD','FY27Q4',8850000),

(6,'Chennai','PSU','FY27Q1',12000000),(6,'Chennai','PSU','FY27Q2',20000000),(6,'Chennai','PSU','FY27Q3',24400000),(6,'Chennai','PSU','FY27Q4',23600000),
(6,'Chennai','ANCHOR','FY27Q1',9000000),(6,'Chennai','ANCHOR','FY27Q2',15000000),(6,'Chennai','ANCHOR','FY27Q3',18300000),(6,'Chennai','ANCHOR','FY27Q4',17700000),
(6,'Chennai','DMFT','FY27Q1',4500000),(6,'Chennai','DMFT','FY27Q2',7500000),(6,'Chennai','DMFT','FY27Q3',9150000),(6,'Chennai','DMFT','FY27Q4',8850000),
(6,'Chennai','STANDARD','FY27Q1',4500000),(6,'Chennai','STANDARD','FY27Q2',7500000),(6,'Chennai','STANDARD','FY27Q3',9150000),(6,'Chennai','STANDARD','FY27Q4',8850000),

-- ===== Kolkata (7), Ahmedabad (8) -- 8% each = 16cr =====
(7,'Kolkata','PSU','FY27Q1',9600000),(7,'Kolkata','PSU','FY27Q2',16000000),(7,'Kolkata','PSU','FY27Q3',19520000),(7,'Kolkata','PSU','FY27Q4',18880000),
(7,'Kolkata','ANCHOR','FY27Q1',7200000),(7,'Kolkata','ANCHOR','FY27Q2',12000000),(7,'Kolkata','ANCHOR','FY27Q3',14640000),(7,'Kolkata','ANCHOR','FY27Q4',14160000),
(7,'Kolkata','DMFT','FY27Q1',3600000),(7,'Kolkata','DMFT','FY27Q2',6000000),(7,'Kolkata','DMFT','FY27Q3',7320000),(7,'Kolkata','DMFT','FY27Q4',7080000),
(7,'Kolkata','STANDARD','FY27Q1',3600000),(7,'Kolkata','STANDARD','FY27Q2',6000000),(7,'Kolkata','STANDARD','FY27Q3',7320000),(7,'Kolkata','STANDARD','FY27Q4',7080000),

(8,'Ahmedabad','PSU','FY27Q1',9600000),(8,'Ahmedabad','PSU','FY27Q2',16000000),(8,'Ahmedabad','PSU','FY27Q3',19520000),(8,'Ahmedabad','PSU','FY27Q4',18880000),
(8,'Ahmedabad','ANCHOR','FY27Q1',7200000),(8,'Ahmedabad','ANCHOR','FY27Q2',12000000),(8,'Ahmedabad','ANCHOR','FY27Q3',14640000),(8,'Ahmedabad','ANCHOR','FY27Q4',14160000),
(8,'Ahmedabad','DMFT','FY27Q1',3600000),(8,'Ahmedabad','DMFT','FY27Q2',6000000),(8,'Ahmedabad','DMFT','FY27Q3',7320000),(8,'Ahmedabad','DMFT','FY27Q4',7080000),
(8,'Ahmedabad','STANDARD','FY27Q1',3600000),(8,'Ahmedabad','STANDARD','FY27Q2',6000000),(8,'Ahmedabad','STANDARD','FY27Q3',7320000),(8,'Ahmedabad','STANDARD','FY27Q4',7080000);

-- Sanity: 128 rows total, sum should be Rs 200 crore = 200,00,00,000 paise * 100? No - all values stored in Rs (paise=NO).
-- 200 cr = 2,000,000,000 Rs = 2e9. Each row above is in Rs.
-- Quick check: Mumbai annual = 5,00,00,00,000 / 10 (because we coded in 10x scale for unique seed) wait... let me re-check.
-- Actually I coded each value as is. Mumbai PSU = 30M+50M+61M+59M = 200M = Rs 20 crore. OK that matches Mumbai PSU 40% of 50cr = 20cr.
-- Total all 128 rows should sum to Rs 200 crore = 2,000,000,000.
-- Run after migration: SELECT SUM(target_rs) FROM revenue_target_matrix; should return 2000000000.

-- ----------------------------------------------------------------------------
-- 10) Trigger to auto-create cm_joint_meeting_log row on qualifying tblcallevents insert
--     Fires when actiontype_id IN (3,4) AND lead is at cstatus 8/9/12
-- ----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_callevent_cm_joint_log;

DELIMITER $$
CREATE TRIGGER trg_callevent_cm_joint_log
AFTER INSERT ON tblcallevents
FOR EACH ROW
BEGIN
  DECLARE v_cstatus TINYINT DEFAULT NULL;
  DECLARE v_cm_uid INT DEFAULT NULL;

  IF NEW.actiontype_id IN (3, 4) THEN
    SELECT current_status_id INTO v_cstatus FROM init_call WHERE id = NEW.cid_id LIMIT 1;
    IF v_cstatus IN (8, 9, 12) THEN
      SELECT u.cm_uid INTO v_cm_uid FROM user u WHERE u.uid = NEW.mainbd LIMIT 1;
      INSERT IGNORE INTO cm_joint_meeting_log
        (event_id, lead_id, bd_uid, expected_cm_uid, cstatus_at_meeting, meeting_date, mandatory)
      VALUES
        (NEW.id, NEW.cid_id, NEW.mainbd, v_cm_uid, v_cstatus, DATE(NEW.event_date), 1);
    END IF;
  END IF;
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- 11) Trigger to write revenue_actual_ledger on G4 signoff approve
--     Reads lead_stage_signoff (from mig 022) when status changes to 'approved' AND to_cstatus=12
-- ----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_signoff_revenue_ledger;

DELIMITER $$
CREATE TRIGGER trg_signoff_revenue_ledger
AFTER UPDATE ON lead_stage_signoff
FOR EACH ROW
BEGIN
  DECLARE v_contract_rs BIGINT DEFAULT 0;
  DECLARE v_cluster_id INT DEFAULT NULL;
  DECLARE v_cluster_name VARCHAR(80) DEFAULT NULL;
  DECLARE v_cat VARCHAR(16) DEFAULT 'STANDARD';
  DECLARE v_bd INT DEFAULT NULL;
  DECLARE v_cm INT DEFAULT NULL;
  DECLARE v_rm INT DEFAULT NULL;
  DECLARE v_won_at DATETIME DEFAULT NOW();
  DECLARE v_q VARCHAR(8) DEFAULT 'FY27Q1';
  DECLARE v_month TINYINT DEFAULT MONTH(NOW());

  IF NEW.status = 'approved' AND OLD.status != 'approved' AND NEW.to_cstatus = 12 THEN
    -- contract value from payload
    SET v_contract_rs = CAST(JSON_UNQUOTE(JSON_EXTRACT(NEW.payload_json, '$.contract_value_rs')) AS UNSIGNED);

    -- cluster & owners
    SELECT u.cluster_id, c.cluster_name, ic.mainbd, u.cm_uid, u.rm_uid
      INTO v_cluster_id, v_cluster_name, v_bd, v_cm, v_rm
    FROM init_call ic
    LEFT JOIN user u ON u.uid = ic.mainbd
    LEFT JOIN (SELECT DISTINCT cluster_id, cluster_name FROM revenue_target_matrix) c ON c.cluster_id = u.cluster_id
    WHERE ic.id = NEW.lead_id LIMIT 1;

    -- category
    SELECT COALESCE(act.category_code, 'STANDARD') INTO v_cat
    FROM account_category_tag act WHERE act.lead_id = NEW.lead_id LIMIT 1;

    -- fiscal quarter (FY27 = Apr 2026 to Mar 2027)
    SET v_q = CASE
      WHEN v_month BETWEEN 4 AND 6 THEN 'FY27Q1'
      WHEN v_month BETWEEN 7 AND 9 THEN 'FY27Q2'
      WHEN v_month BETWEEN 10 AND 12 THEN 'FY27Q3'
      ELSE 'FY27Q4'
    END;

    INSERT IGNORE INTO revenue_actual_ledger
      (lead_id, signoff_id, won_at, contract_value_rs, cluster_id, cluster_name,
       category_code, fiscal_quarter, bd_uid, cm_uid, rm_uid)
    VALUES
      (NEW.lead_id, NEW.id, v_won_at, v_contract_rs, COALESCE(v_cluster_id,0),
       COALESCE(v_cluster_name,'unknown'), v_cat, v_q, COALESCE(v_bd,0), v_cm, v_rm);
  END IF;
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- END OF MIGRATION 023
-- Sanity checks to run after applying:
--   1. SELECT COUNT(*) FROM revenue_target_matrix;  -- expect 128
--   2. SELECT SUM(target_rs) FROM revenue_target_matrix;  -- expect 2,000,000,000 (Rs 200 cr)
--   3. SHOW CREATE VIEW v_target_progress;
--   4. SHOW TRIGGERS LIKE 'tblcallevents';  -- expect trg_callevent_cm_joint_log
-- ----------------------------------------------------------------------------
