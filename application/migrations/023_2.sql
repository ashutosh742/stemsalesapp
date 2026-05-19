-- ============================================================================
-- STEM CRM Migration 023.2 - Cluster Hard-Code + Incentive Cadence Master
-- ============================================================================
-- Anchors: 9 canonical clusters from Sales-Team-Reporting.xlsx + full incentive
-- cadence ladder from Final-Incentive-Sheet.xlsx.
--
-- Source of truth (16 May 2026 founder lock):
--   - 9 clusters fixed (no Hyderabad, no Pune, no Ahmedabad, no Kolkata,
--     no Chennai - those Migration 023 placeholders are replaced).
--   - East is a RM-only roll-up (Mehak Sarraf, RM East), NOT a cluster_id.
--   - Incentive sheet is the cadence source of truth. All crons read from
--     incentive_cadence_master, no hard-coded grade math.
--   - BD target stays Rs 2 cr per BD per FY (locked in 023.1).
--   - Production typos preserved: Compnay, Compny, Quater, budgt, etc.
--
-- Run order: 023 -> 023.1 -> 023.2.
-- Re-runnable. All ALTERs are idempotent via INFORMATION_SCHEMA guards.
-- Plain English comments only. No em-dashes. No non-ASCII.
-- ============================================================================

SET @schema := DATABASE();

-- ============================================================================
-- SECTION 1: CLUSTER MASTER (9 canonical clusters)
-- ============================================================================

DROP TABLE IF EXISTS cluster_master;
CREATE TABLE cluster_master (
  cluster_id          TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  cluster_name        VARCHAR(40) NOT NULL,
  region              ENUM('North','South','East','West','Central') NOT NULL,
  rm_uid              INT UNSIGNED DEFAULT NULL COMMENT 'Resolved at deploy time from user.type_id=28',
  cm_uid              INT UNSIGNED DEFAULT NULL COMMENT 'Resolved at deploy time from user.type_id=13',
  is_pilot            TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 for Mumbai pilot 25 May 2026',
  is_active           TINYINT(1) NOT NULL DEFAULT 1,
  created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_cluster_name (cluster_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO cluster_master (cluster_id, cluster_name, region, is_pilot) VALUES
  (1, 'Mumbai',      'West',  1),
  (2, 'Delhi',       'North', 0),
  (3, 'Rajasthan',   'North', 0),
  (4, 'Punjab',      'North', 0),
  (5, 'Bangalore',   'South', 0),
  (6, 'Tamil Nadu',  'South', 0),
  (7, 'Telangana',   'South', 0),
  (8, 'West Bengal', 'East',  0),
  (9, 'Jharkhand',   'East',  0);

-- ----------------------------------------------------------------------------
-- 1b. ALTER init_call: hard-link cluster_id to cluster_master
-- ----------------------------------------------------------------------------

-- Add cluster_id column if missing
SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA=@schema
                      AND TABLE_NAME='init_call'
                      AND COLUMN_NAME='cluster_id');
SET @sql := IF(@col_exists=0,
  'ALTER TABLE init_call ADD COLUMN cluster_id TINYINT UNSIGNED DEFAULT NULL AFTER compny_loction',
  'SELECT "init_call.cluster_id already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add FK if missing
SET @fk_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                   WHERE TABLE_SCHEMA=@schema
                     AND TABLE_NAME='init_call'
                     AND CONSTRAINT_NAME='fk_init_call_cluster');
SET @sql := IF(@fk_exists=0,
  'ALTER TABLE init_call ADD CONSTRAINT fk_init_call_cluster FOREIGN KEY (cluster_id) REFERENCES cluster_master(cluster_id) ON DELETE SET NULL',
  'SELECT "fk_init_call_cluster already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index for cluster reporting roll-ups
SET @idx_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                    WHERE TABLE_SCHEMA=@schema
                      AND TABLE_NAME='init_call'
                      AND INDEX_NAME='idx_init_call_cluster');
SET @sql := IF(@idx_exists=0,
  'ALTER TABLE init_call ADD INDEX idx_init_call_cluster (cluster_id, cstatus, createDate)',
  'SELECT "idx_init_call_cluster already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- 1c. Backfill cluster_id from user.cluster (legacy text column)
-- ----------------------------------------------------------------------------

UPDATE init_call ic
JOIN user u ON u.uid = ic.mainbd
JOIN cluster_master cm ON LOWER(TRIM(u.cluster)) = LOWER(cm.cluster_name)
SET ic.cluster_id = cm.cluster_id
WHERE ic.cluster_id IS NULL;

-- ----------------------------------------------------------------------------
-- 1d. Migrate revenue_target_matrix to new cluster ids
-- ----------------------------------------------------------------------------
-- Migration 023 seeded placeholder cluster ids (1-8 with old names).
-- Map by cluster_name where it matches; drop rows for retired clusters
-- (Hyderabad, Pune, Ahmedabad, Kolkata, Chennai placeholders if present).

UPDATE revenue_target_matrix rtm
JOIN cluster_master cm ON LOWER(TRIM(rtm.cluster_name)) = LOWER(cm.cluster_name)
SET rtm.cluster_id = cm.cluster_id;

DELETE FROM revenue_target_matrix
 WHERE cluster_id NOT IN (SELECT cluster_id FROM cluster_master);


-- ============================================================================
-- SECTION 2: INCENTIVE CADENCE MASTER
-- ============================================================================
-- Mirrors Final-Incentive-Sheet.xlsx. Every cron reads cadence from here.
-- Categories observed in the sheet:
--   - sales_closure       Sales Closure Incentive (per-lead pct on STEM Booking)
--   - performance         Performance Incentive (quarterly buckets, see below)
--   - flat                Flat Incentive (probation / non-target roles)
--   - daily_discipline    Daily Login + Huddle + Planner + T-Review + Funnel + CRM Review
--   - daily_huddle        Daily Huddle and Review separate bucket
--   - prospecting         RP Meetings + LinkedIn prospecting (quarterly)
--   - barg_in_conversion  Conversion of 75 percent from barg-in to RP (quarterly)
--   - fresh_meeting_100   100 to 129 RP meetings (quarterly)
--   - fresh_meeting_130   130+ RP meetings (quarterly)
--   - dmft_govt_activation 10 DMFT and Govt accounts activation (Oct-Dec)
--   - upsell_rm_meeting   80 percent complete of assigned upsell funnel (Oct-Dec)
--   - proposal_submission 50 CR new proposals submitted per cluster (quarterly)
--   - top_closure_cluster Cluster revenue between 10-15 CR (Q3 + Q4 only)
--   - region_upsell_pc    PC role: region upsell identification (Rs 5000 flat)
--   - support             Other project support (variable)
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS incentive_cadence_master;
CREATE TABLE incentive_cadence_master (
  cadence_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cadence_code        VARCHAR(40) NOT NULL,
  cadence_label       VARCHAR(160) NOT NULL,
  applies_to_role     ENUM('BD','CM','ACM','RM','SC','PC','SH','ALL') NOT NULL,
  category            VARCHAR(40) NOT NULL,
  measurement_window  ENUM('daily','weekly','monthly','quarterly','half_yearly','annual') NOT NULL,
  payout_window       ENUM('monthly','quarterly','annual') NOT NULL,
  threshold_value     DECIMAL(18,4) DEFAULT NULL COMMENT 'Numeric threshold (count, pct, Rs)',
  threshold_unit      ENUM('count','percent','rupees','crore','lakh') DEFAULT NULL,
  payout_amount_rs    DECIMAL(18,2) DEFAULT NULL COMMENT 'Fixed payout when slab met',
  payout_formula      VARCHAR(300) DEFAULT NULL COMMENT 'Plain-English formula, no code',
  qualifying_quarter  VARCHAR(20) DEFAULT NULL COMMENT 'e.g. Q3, Q3-Q4, all',
  is_active           TINYINT(1) NOT NULL DEFAULT 1,
  notes               TEXT,
  created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_cadence_code (cadence_code),
  INDEX idx_role_category (applies_to_role, category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 2a. Seed BD + CM + ACM + RM cadences (Sales Performance sheet)
-- ----------------------------------------------------------------------------

INSERT INTO incentive_cadence_master
  (cadence_code, cadence_label, applies_to_role, category, measurement_window,
   payout_window, threshold_value, threshold_unit, payout_amount_rs,
   payout_formula, qualifying_quarter, notes)
VALUES
-- BD slab ladders (Sales Performance sheet, observed payouts)
('bd_perf_25k',         'BD Performance bucket Rs 25000',
   'BD',  'performance',          'quarterly', 'quarterly', 1, 'count',  25000,
   'Fixed Rs 25000 on quarterly performance bucket clearance', 'all',
   'Source: Sales Performance sheet, Ayesha Begum / Maryleena Bori'),

('bd_perf_35k',         'BD Performance bucket Rs 35000 (Barg-in 25k + Fresh meeting 10k)',
   'BD',  'performance',          'quarterly', 'quarterly', 1, 'count',  35000,
   'Barg-in conversion Rs 25000 + Fresh meeting 100+ bonus Rs 10000', 'all',
   'Source: Sales Performance, Avishek Pathak'),

('bd_perf_10k_fresh',   'BD Fresh meeting 100+ bucket Rs 10000',
   'BD',  'performance',          'quarterly', 'quarterly', 100, 'count', 10000,
   'Rs 10000 flat on completing 100 plus fresh RP meetings in quarter', 'all',
   'Source: Sales Performance, Shahjada Tanweer'),

-- CM slab ladders
('cm_perf_15k_barge',   'CM Barg-in conversion bucket Rs 15000',
   'CM',  'performance',          'quarterly', 'quarterly', 75, 'percent', 15000,
   '75 percent barg-in to RP conversion = Rs 15000', 'all',
   'Source: Sales Performance, Nagdev R'),

('cm_perf_30k_barge',   'CM Barg-in conversion bucket Rs 30000',
   'CM',  'performance',          'quarterly', 'quarterly', 75, 'percent', 30000,
   '75 percent barg-in to RP conversion at higher cluster volume = Rs 30000', 'all',
   'Source: Sales Performance, Mohammad Zenul / Nilanjan Chatterjee'),

('cm_topclose_cluster_150k',
   'CM Top Closure Cluster Rs 150000',
   'CM',  'top_closure_cluster',  'quarterly', 'quarterly', 10, 'crore', 150000,
   'Cluster revenue 10 cr to 15 cr in Q3 or Q4 = Rs 150000', 'Q3-Q4',
   'Source: Sales Performance, Atul Rai. Q3 and Q4 only.'),

-- ACM slab ladders
('acm_perf_35k',        'ACM Performance bucket Rs 35000',
   'ACM', 'performance',          'quarterly', 'quarterly', 1, 'count',  35000,
   'Barg-in Rs 25000 + Fresh meeting Rs 10000', 'all',
   'Source: Sales Performance, Ruchika Agarwal'),

('acm_perf_25k_barge',  'ACM Barg-in conversion Rs 25000',
   'ACM', 'performance',          'quarterly', 'quarterly', 75, 'percent', 25000,
   '75 percent barg-in to RP conversion', 'all',
   'Source: Sales Performance, Vikas Kumar'),

('acm_proposal_50cr',   'ACM 50 CR Proposal Submission Rs 10000',
   'ACM', 'proposal_submission',  'quarterly', 'quarterly', 50, 'crore',  10000,
   'New 50 CR of proposals submitted in quarter (CM and RM must change status)', 'Q3',
   'Source: Sales Performance, Abinash Tarai. Quarterly Q3 sheet.'),

('acm_topclose_75k',    'ACM Top Closure Cluster Rs 75000',
   'ACM', 'top_closure_cluster',  'quarterly', 'quarterly', 10, 'crore',  75000,
   'Cluster revenue 10 cr to 15 cr in Q3 or Q4 = Rs 75000', 'Q3-Q4',
   'Source: Sales Performance, Tarun Kushwaha'),

-- RM slab ladders
('rm_perf_10k_barge',   'RM Barg-in conversion Rs 10000',
   'RM',  'performance',          'quarterly', 'quarterly', 75, 'percent', 10000,
   'Cluster barg-in to RP conversion 75 percent', 'all',
   'Source: Sales Performance, Mahesh Kumar'),

('rm_perf_20k_barge',   'RM Barg-in conversion Rs 20000 (multi-cluster)',
   'RM',  'performance',          'quarterly', 'quarterly', 75, 'percent', 20000,
   'Multi-cluster RM (West Bengal + Jharkhand) Rs 20000', 'all',
   'Source: Sales Performance, Mehak Sarraf');

-- ----------------------------------------------------------------------------
-- 2b. Seed Sales Coordinator (SC) cadences
-- ----------------------------------------------------------------------------

INSERT INTO incentive_cadence_master
  (cadence_code, cadence_label, applies_to_role, category, measurement_window,
   payout_window, threshold_value, threshold_unit, payout_amount_rs,
   payout_formula, qualifying_quarter, notes)
VALUES
('sc_closure_1k',       'SC Rs 1000 per closure',
   'SC', 'sales_closure',         'monthly',  'monthly',   1,    'count',   1000,
   'Rs 1000 per closure assisted', 'all',
   'Source: SC sheet, Incentive from Closure table'),

('sc_discipline_50k',   'SC Daily Discipline Rs 50000 quarterly cap',
   'SC', 'daily_discipline',      'daily',    'quarterly', 90,   'percent', 50000,
   'Rs 50000 quarterly when 90 percent average across Login-Out + Huddle + Planner + T-Review + Funnel + CRM Review', 'all',
   'Source: SC Daily Discipline table. Six sub-KPIs averaged.'),

('sc_prospecting_30k',  'SC Prospecting RP 30 meetings Rs 30000',
   'SC', 'prospecting',           'quarterly','quarterly', 30,   'count',   30000,
   '30 RP meetings + LinkedIn prospecting (10 top spender) quarterly', 'all',
   'Source: SC Prospecting table'),

('sc_prospecting_50k',  'SC Prospecting RP 50 meetings Rs 50000',
   'SC', 'prospecting',           'quarterly','quarterly', 50,   'count',   50000,
   '50 RP meetings + LinkedIn prospecting (20 top spender) quarterly', 'all',
   'Source: SC Prospecting table tier 2'),

('sc_barge_75pct',      'SC Barg-in conversion 75 percent Rs 40000',
   'SC', 'barg_in_conversion',    'quarterly','quarterly', 75,   'percent', 40000,
   '75 percent conversion from total barg-in to RP meeting', 'all',
   'Source: SC Barg-in Meeting table. Sourav Kundu cleared.'),

('sc_fresh_100',        'SC 100-129 Fresh meetings quarterly',
   'SC', 'fresh_meeting_100',     'quarterly','quarterly', 100,  'count',   30000,
   '100 to 129 RP meetings (new prospecting first-time)', 'all',
   'Source: SC 100/130 Fresh Meeting table'),

('sc_fresh_130',        'SC 130+ Fresh meetings quarterly',
   'SC', 'fresh_meeting_130',     'quarterly','quarterly', 130,  'count',   50000,
   '130 plus RP meetings (new prospecting first-time)', 'all',
   'Source: SC 100/130 Fresh Meeting table tier 2'),

('sc_dmft_10',          'SC 10 DMFT or Govt activations Rs 25000',
   'SC', 'dmft_govt_activation',  'half_yearly','quarterly', 10, 'count',   25000,
   '10 DMFT, Govt or Smart Cities accounts activated (CM or ACM nurturing)', 'Q3',
   'Source: SC 10 Govt and DMFT Activation table. Oct-Dec window.'),

('sc_dmft_15',          'SC 15 DMFT or Govt activations Rs 50000',
   'SC', 'dmft_govt_activation',  'half_yearly','quarterly', 15, 'count',   50000,
   '15 DMFT, Govt or Smart Cities accounts activated', 'Q3',
   'Source: SC 10 Govt and DMFT Activation tier 2'),

('sc_upsell_80pct',     'SC Upsell RM meeting 80 percent Rs 20000',
   'SC', 'upsell_rm_meeting',     'half_yearly','quarterly', 80, 'percent', 20000,
   '80 percent complete of the assigned upsell funnel (RM assigns)', 'Q3',
   'Source: SC 80 Percent Upsell RM Meeting table'),

('sc_proposal_50cr',    'SC 50 CR Proposal Submission Rs 75000',
   'SC', 'proposal_submission',   'quarterly','quarterly', 50,   'crore',   75000,
   'New 50 cr of proposal submission in Q3 from each cluster, CM and RM must change status', 'Q3',
   'Source: SC On Proposal Submission table'),

('sc_topclose_10cr',    'SC Top Closure Cluster Rs 75000',
   'SC', 'top_closure_cluster',   'quarterly','quarterly', 10,   'crore',   75000,
   'Revenue 10 cr to 15 cr in Q3 or Q4 only', 'Q3-Q4',
   'Source: SC Top Closure Cluster 10CR table');

-- ----------------------------------------------------------------------------
-- 2c. Seed Project Coordinator (PC) cadences
-- ----------------------------------------------------------------------------

INSERT INTO incentive_cadence_master
  (cadence_code, cadence_label, applies_to_role, category, measurement_window,
   payout_window, threshold_value, threshold_unit, payout_amount_rs,
   payout_formula, qualifying_quarter, notes)
VALUES
('pc_region_upsell',    'PC Region Upsell Identification Rs 5000',
   'PC', 'region_upsell_pc',      'monthly',  'monthly',   1,    'count',   5000,
   'Rs 5000 flat per month for region upsell identification', 'all',
   'Source: PC sheet. Most PCs earn this fixed monthly.'),

('pc_dc_deo_letter',    'PC DC/DEO Letter Rs 5000 per cluster',
   'PC', 'support',               'monthly',  'monthly',   1,    'count',   5000,
   'Fixed Rs 5000 monthly for DC/DEO letter outreach support', 'all',
   'Source: PC Total DC/DEO Letter column');

-- ----------------------------------------------------------------------------
-- 2d. Sales Closure Incentive (BD/ACM/CM/RM percent-of-booking, from Sales Leads sheet)
-- ----------------------------------------------------------------------------
-- This is the per-lead split observed in Sales Leads sheet columns R-W.
-- Total Incentive percent of "Amount Qualified for Incentive" (i.e. STEM Booking
-- excluding GST and NGO percent) varies by deal size and role mix:
--   - New Client (BD-only):       2.25 percent (typical)
--   - New Client (BD + ACM + CM): 3.75 percent (e.g. Minaketan/Wacker)
--   - Upsell (RM-led):            0.75 to 1.0 percent (Mehak Sarraf cases)
--   - Standard split 4 roles:     BD 50, ACM 17, CM 33, RM 33 (sum = approx 100 pct of pool)
-- Detailed split logged in incentive_split_rule below.

DROP TABLE IF EXISTS incentive_split_rule;
CREATE TABLE incentive_split_rule (
  split_id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rule_code           VARCHAR(40) NOT NULL,
  rule_label          VARCHAR(160) NOT NULL,
  deal_type           ENUM('new_client','upsell','dmft','psu','anchor') NOT NULL,
  total_pct_of_qualifying DECIMAL(6,4) NOT NULL COMMENT 'Total incentive as fraction of qualifying amount',
  bd_pct              DECIMAL(6,4) NOT NULL,
  acm_pct             DECIMAL(6,4) NOT NULL,
  cm_pct              DECIMAL(6,4) NOT NULL,
  rm_pct              DECIMAL(6,4) NOT NULL,
  support_pct         DECIMAL(6,4) NOT NULL DEFAULT 0.0000,
  is_active           TINYINT(1) NOT NULL DEFAULT 1,
  notes               TEXT,
  UNIQUE KEY uk_split_code (rule_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO incentive_split_rule
  (rule_code, rule_label, deal_type, total_pct_of_qualifying, bd_pct, acm_pct, cm_pct, rm_pct, support_pct, notes)
VALUES
('new_client_full_chain',    'New Client with BD + ACM + CM + RM all active',
   'new_client', 0.0375, 0.0150, 0.0050, 0.0100, 0.0075, 0.0000,
   'Source: Sales Leads sheet, Patnaik Steel / MGM Minerals / Meenakshi mines'),

('new_client_bd_cm_rm',      'New Client BD + CM + RM (no ACM)',
   'new_client', 0.0225, 0.0150, 0.0000, 0.0000, 0.0075, 0.0000,
   'Source: Sales Leads, Smit Solanki Vatsalya Foundation, Buro Happold'),

('new_client_bd_rm_only',    'New Client BD + RM only',
   'new_client', 0.0225, 0.0150, 0.0000, 0.0000, 0.0075, 0.0000,
   'Source: Sales Leads, Sumeet Ghosh SAMPARC'),

('new_client_bd_acm_cm',     'New Client BD + ACM + CM (no RM)',
   'new_client', 0.0300, 0.0150, 0.0050, 0.0100, 0.0000, 0.0000,
   'Source: Sales Leads, Vishal Kashyap Ambuja Moga'),

('upsell_full_chain',        'Upsell with BD + ACM + CM + RM',
   'upsell',     0.0150, 0.0025, 0.0025, 0.0025, 0.0075, 0.0000,
   'Source: Sales Leads, Ashis Mishra GMR Kamalanga / Gram Vikas'),

('upsell_bd_rm',             'Upsell BD + RM only',
   'upsell',     0.0100, 0.0025, 0.0000, 0.0000, 0.0075, 0.0000,
   'Source: Sales Leads, Kishan Madam United Way Baroda, Neha Gondhali Fino'),

('upsell_rm_only',           'Upsell RM-led, no BD',
   'upsell',     0.0075, 0.0000, 0.0000, 0.0000, 0.0075, 0.0000,
   'Source: Sales Leads, Plan International (Mehak Sarraf only)'),

('upsell_cm_rm',             'Upsell CM + RM (no BD)',
   'upsell',     0.0100, 0.0000, 0.0000, 0.0025, 0.0075, 0.0000,
   'Source: Sales Leads, Brillio Foundation (Nagdev R + Mahesh Kumar)'),

('upsell_bd_cm',             'Upsell BD + CM (no RM)',
   'upsell',     0.0050, 0.0025, 0.0000, 0.0025, 0.0000, 0.0000,
   'Source: Sales Leads, Ruchika Punjab Chemicals');


-- ============================================================================
-- SECTION 3: INCENTIVE PAYOUT LOG (per-employee, per-month, per-cadence)
-- ============================================================================

DROP TABLE IF EXISTS incentive_payout_log;
CREATE TABLE incentive_payout_log (
  payout_id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_uid        INT UNSIGNED NOT NULL,
  employee_name       VARCHAR(120) NOT NULL COMMENT 'Snapshot at payout time',
  role_code           ENUM('BD','CM','ACM','RM','SC','PC','SH') NOT NULL,
  cluster_id          TINYINT UNSIGNED DEFAULT NULL,
  cadence_id          INT UNSIGNED NOT NULL,
  fy_year             VARCHAR(8) NOT NULL COMMENT 'e.g. FY26-27',
  quarter             VARCHAR(8) DEFAULT NULL COMMENT 'Q1/Q2/Q3/Q4',
  month_yyyy_mm       VARCHAR(7) DEFAULT NULL,
  threshold_required  DECIMAL(18,4) DEFAULT NULL,
  threshold_achieved  DECIMAL(18,4) DEFAULT NULL,
  achieved_pct        DECIMAL(8,4) DEFAULT NULL,
  is_eligible         TINYINT(1) NOT NULL DEFAULT 0,
  amount_eligible_rs  DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  amount_paid_rs      DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  payout_status       ENUM('computed','approved','paid','clawback','disputed') NOT NULL DEFAULT 'computed',
  approved_by         INT UNSIGNED DEFAULT NULL,
  approved_at         DATETIME DEFAULT NULL,
  notes               TEXT,
  created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cadence_id)  REFERENCES incentive_cadence_master(cadence_id),
  FOREIGN KEY (cluster_id)  REFERENCES cluster_master(cluster_id) ON DELETE SET NULL,
  INDEX idx_employee_fy (employee_uid, fy_year, quarter),
  INDEX idx_status (payout_status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================================
-- SECTION 4: INCENTIVE CADENCE CALENDAR (quarter and window definitions)
-- ============================================================================
-- STEM FY = April to March. Quarters:
--   Q1 = Apr-Jun, Q2 = Jul-Sep, Q3 = Oct-Dec, Q4 = Jan-Mar
-- Some cadences fire only in Q3 (DMFT activation, 50 CR proposal) or Q3-Q4
-- (Top Closure Cluster). Calendar lets crons resolve "is_in_window" quickly.

DROP TABLE IF EXISTS incentive_cadence_calendar;
CREATE TABLE incentive_cadence_calendar (
  calendar_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fy_year             VARCHAR(8) NOT NULL COMMENT 'FY26-27',
  quarter             VARCHAR(8) NOT NULL,
  quarter_start       DATE NOT NULL,
  quarter_end         DATE NOT NULL,
  computation_due     DATE NOT NULL COMMENT 'Day cron computes and writes incentive_payout_log',
  payout_due          DATE NOT NULL COMMENT 'Day finance must pay (typically 7 days later)',
  UNIQUE KEY uk_fy_quarter (fy_year, quarter)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO incentive_cadence_calendar
  (fy_year, quarter, quarter_start, quarter_end, computation_due, payout_due)
VALUES
('FY26-27','Q1','2026-04-01','2026-06-30','2026-07-05','2026-07-12'),
('FY26-27','Q2','2026-07-01','2026-09-30','2026-10-05','2026-10-12'),
('FY26-27','Q3','2026-10-01','2026-12-31','2027-01-05','2027-01-12'),
('FY26-27','Q4','2027-01-01','2027-03-31','2027-04-05','2027-04-12'),
('FY27-28','Q1','2027-04-01','2027-06-30','2027-07-05','2027-07-12'),
('FY27-28','Q2','2027-07-01','2027-09-30','2027-10-05','2027-10-12'),
('FY27-28','Q3','2027-10-01','2027-12-31','2028-01-05','2028-01-12'),
('FY27-28','Q4','2028-01-01','2028-03-31','2028-04-05','2028-04-12');


-- ============================================================================
-- SECTION 5: REPORTING VIEWS for cron consumption
-- ============================================================================

DROP VIEW IF EXISTS v_cluster_active;
CREATE VIEW v_cluster_active AS
SELECT cm.cluster_id, cm.cluster_name, cm.region, cm.rm_uid, cm.cm_uid,
       cm.is_pilot,
       (SELECT COUNT(*) FROM user u WHERE u.cluster = cm.cluster_name
        AND u.type_id IN (10,11) AND u.is_active = 1) AS active_bd_count,
       (SELECT COUNT(*) FROM user u WHERE u.cluster = cm.cluster_name
        AND u.type_id = 13 AND u.is_active = 1) AS active_cm_count,
       (SELECT COUNT(*) FROM init_call ic WHERE ic.cluster_id = cm.cluster_id
        AND ic.cstatus NOT IN (12,13)) AS open_lead_count
FROM cluster_master cm
WHERE cm.is_active = 1;

DROP VIEW IF EXISTS v_incentive_cadence_active;
CREATE VIEW v_incentive_cadence_active AS
SELECT cadence_id, cadence_code, cadence_label, applies_to_role, category,
       measurement_window, payout_window, threshold_value, threshold_unit,
       payout_amount_rs, qualifying_quarter
FROM incentive_cadence_master
WHERE is_active = 1
ORDER BY applies_to_role, category, threshold_value;

DROP VIEW IF EXISTS v_incentive_payout_pending;
CREATE VIEW v_incentive_payout_pending AS
SELECT pl.payout_id, pl.employee_name, pl.role_code, cm.cluster_name,
       icm.cadence_label, pl.fy_year, pl.quarter, pl.amount_eligible_rs,
       pl.payout_status, pl.created_at
FROM incentive_payout_log pl
JOIN incentive_cadence_master icm ON icm.cadence_id = pl.cadence_id
LEFT JOIN cluster_master cm ON cm.cluster_id = pl.cluster_id
WHERE pl.payout_status IN ('computed','approved')
  AND pl.amount_eligible_rs > 0
ORDER BY pl.created_at DESC;


-- ============================================================================
-- SECTION 6: REVENUE TARGET MATRIX patch (legacy 023 -> new cluster ids)
-- ============================================================================
-- 023 seeded with 8 placeholder clusters. After backfill in section 1d the
-- legacy matrix rows now point at correct cluster_id. Add the missing 9th
-- (Punjab and Telangana were not in original 023 placeholder).

INSERT IGNORE INTO revenue_target_matrix
  (cluster_id, cluster_name, fy_year, category_psu_rs, category_dmft_rs,
   category_anchor_rs, category_standard_rs, total_target_rs, created_at)
SELECT cm.cluster_id, cm.cluster_name, 'FY26-27',
       400000000, 200000000, 800000000, 600000000,  -- Rs 4 cr / 2 cr / 8 cr / 6 cr default split
       2000000000,                                   -- Rs 20 cr per cluster ceiling
       NOW()
FROM cluster_master cm
WHERE cm.cluster_id NOT IN (SELECT cluster_id FROM revenue_target_matrix WHERE fy_year='FY26-27');


-- ============================================================================
-- SECTION 7: GUARDRAILS
-- ============================================================================

-- Block accidental deletion of any cluster_master row while leads exist
DROP TRIGGER IF EXISTS trg_cluster_master_protect;
DELIMITER //
CREATE TRIGGER trg_cluster_master_protect
BEFORE DELETE ON cluster_master
FOR EACH ROW
BEGIN
  DECLARE lead_count INT DEFAULT 0;
  SELECT COUNT(*) INTO lead_count FROM init_call WHERE cluster_id = OLD.cluster_id;
  IF lead_count > 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot delete cluster with active leads. Reassign first.';
  END IF;
END //
DELIMITER ;

-- ============================================================================
-- VERIFICATION
-- ============================================================================
-- Run these after migration to confirm:
--   SELECT * FROM cluster_master ORDER BY cluster_id;            -- 9 rows
--   SELECT COUNT(*) FROM incentive_cadence_master;               -- 21+ rows
--   SELECT COUNT(*) FROM incentive_split_rule;                   -- 9 rows
--   SELECT COUNT(*) FROM init_call WHERE cluster_id IS NULL;     -- backfill check
--   SELECT * FROM v_cluster_active;                              -- 9 rows
--   SELECT * FROM v_incentive_cadence_active;                    -- 21+ rows
-- ============================================================================
