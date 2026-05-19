-- ============================================================================
-- STEM CRM Migration 023.3
-- Quarter Target Gate + Hardcoded Reporting Hierarchy + Quarter Config
-- Author: Perplexity Computer for stemlearning@gmail.com
-- Date: 17 May 2026
-- Production hold: deploys after 18 May 2026 GitHub access
-- ============================================================================
--
-- Three things this migration locks down:
--   1. reporting_hierarchy table - 5 levels (Director, RM, CM, ACM, SC, BD)
--      seeded exactly from Sales-Team-Reporting.xlsx. Production source of
--      truth for review routing, escalation, scorecards.
--   2. quarter_config table - which cadences fire which quarter, which KPI
--      weights apply, plus the visual chip metadata (Q1 FY27 etc).
--   3. quarter_target_gate columns on review_session - blocks quarter review
--      submission until next quarter targets are filled in for every direct
--      report. Targets land in revenue_target_matrix automatically.
--
-- Depends on:
--   migration 020 (review_session)
--   migration 022 (line_manager_scorecard)
--   migration 023 (revenue_target_matrix, rm_upsell_pipeline)
--   migration 023.2 (cluster_master, incentive_cadence_master, incentive_split_rule)
-- ============================================================================

START TRANSACTION;

-- ============================================================================
-- 1. REPORTING HIERARCHY (5 levels, hardcoded from sheet)
-- ============================================================================

CREATE TABLE IF NOT EXISTS reporting_hierarchy (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    employee_uid INT UNSIGNED NOT NULL COMMENT 'user.uid',
    employee_name VARCHAR(120) NOT NULL,
    role ENUM('Director','RM','CM','ACM','SC','PC','BD') NOT NULL,
    cluster_id TINYINT UNSIGNED NULL COMMENT 'cluster_master.id; NULL for Director',
    cluster_text VARCHAR(40) NULL COMMENT 'denormalised cluster name for fast lookup',
    manager_uid INT UNSIGNED NULL COMMENT 'direct line manager user.uid; NULL for Director',
    manager_name VARCHAR(120) NULL,
    skip_manager_uid INT UNSIGNED NULL COMMENT 'one level above manager, used for escalation',
    skip_manager_name VARCHAR(120) NULL,
    director_uid INT UNSIGNED NULL COMMENT 'always Meera Dhanuka uid',
    director_name VARCHAR(120) NULL,
    level TINYINT UNSIGNED NOT NULL COMMENT '1=Director, 2=RM, 3=CM, 4=ACM, 5=SC/BD',
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    effective_from DATE NOT NULL,
    effective_to DATE NULL COMMENT 'NULL means current',
    notes VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_employee_active (employee_uid, effective_to),
    KEY idx_manager (manager_uid),
    KEY idx_role_cluster (role, cluster_id),
    KEY idx_skip (skip_manager_uid),
    CONSTRAINT fk_rh_cluster FOREIGN KEY (cluster_id) REFERENCES cluster_master(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Hardcoded 5-level reporting tree from Sales-Team-Reporting.xlsx 16 May 2026.';

-- Seed: Director (level 1)
-- employee_uid placeholder 1 for Meera Dhanuka. Replace with real uid from user table at deploy time.
INSERT INTO reporting_hierarchy
(employee_uid, employee_name, role, cluster_id, cluster_text, manager_uid, manager_name, skip_manager_uid, skip_manager_name, director_uid, director_name, level, status, effective_from, notes)
VALUES
-- LEVEL 1: Director
(1, 'Meera Dhanuka', 'Director', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Meera Dhanuka', 1, 'Active', '2026-04-01', 'Top of tree. FY27 onwards.'),

-- LEVEL 2: RMs (4 active, reporting to Director)
-- Tamil Nadu/South: Sunny Babu
(102, 'Sunny Babu', 'RM', 6, 'Tamil Nadu', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 2, 'Active', '2026-04-01', 'RM South Tamil Nadu hub'),
-- Bangalore/South: Mahesh Kumar
(103, 'Mahesh Kumar', 'RM', 5, 'Bangalore', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 2, 'Active', '2026-04-01', 'RM South Bangalore'),
-- Mumbai/West: Sadanand Shetty
(104, 'Sadanand Shetty', 'RM', 1, 'Mumbai', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 2, 'Active', '2026-04-01', 'RM West Mumbai'),
-- East roll-up: Mehak Sarraf covers West Bengal and Jharkhand
(105, 'Mehak Sarraf', 'RM', NULL, 'East', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 2, 'Active', '2026-04-01', 'RM East: covers cluster 8 West Bengal and cluster 9 Jharkhand. cluster_id NULL because spans two.'),

-- LEVEL 3: CMs (5 active)
-- Telangana: Nagdev R reports direct to Director (no RM in Telangana)
(201, 'Nagdev R', 'CM', 7, 'Telangana', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 3, 'Active', '2026-04-01', 'CM Telangana, also covers Bangalore via type'),
-- Rajasthan: Md Zenul reports direct to Director (no RM in Rajasthan)
(202, 'Mohammad Zenul', 'CM', 3, 'Rajasthan', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 3, 'Active', '2026-04-01', 'CM Rajasthan, reports direct to Director'),
-- Delhi: Atul Rai reports direct to Director (no RM in Delhi)
(203, 'Atul Rai', 'CM', 2, 'Delhi', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 3, 'Active', '2026-04-01', 'CM Delhi, reports direct to Director'),
-- Tamil Nadu: Karthikeyan reports to RM Sunny Babu
(204, 'Karthikeyan', 'CM', 6, 'Tamil Nadu', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 3, 'Active', '2026-04-01', 'CM Tamil Nadu under RM Sunny'),
-- West Bengal: Nilanjan Chatterjee reports to RM Mehak
(205, 'Nilanjan Chatterjee', 'CM', 8, 'West Bengal', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 3, 'Active', '2026-04-01', 'CM West Bengal under RM Mehak'),

-- LEVEL 4: ACMs (6 active)
-- West Bengal: Abinash Tarai under RM Mehak
(301, 'Abinash Tarai', 'ACM', 8, 'West Bengal', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 4, 'Active', '2026-04-01', 'ACM West Bengal'),
-- Jharkhand: Nitin Poddar under RM Mehak
(302, 'Nitin Poddar', 'ACM', 9, 'Jharkhand', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 4, 'Active', '2026-04-01', 'ACM Jharkhand'),
-- Punjab: Ruchika Agarwal acts as cluster lead reporting direct to Director (no RM in Punjab)
(303, 'Ruchika Agarwal', 'ACM', 4, 'Punjab', 1, 'Meera Dhanuka', NULL, NULL, 1, 'Meera Dhanuka', 4, 'Active', '2026-04-01', 'ACM Punjab cluster lead, no RM layer'),
-- Rajasthan: Vikas Kumar under CM Zenul
(304, 'Vikas Kumar', 'ACM', 3, 'Rajasthan', 202, 'Mohammad Zenul', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 4, 'Active', '2026-04-01', 'ACM Rajasthan under CM Zenul'),
-- Telangana: Archana D under CM Nagdev
(305, 'Archana D', 'ACM', 7, 'Telangana', 201, 'Nagdev R', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 4, 'Active', '2026-04-01', 'ACM Telangana under CM Nagdev'),
-- Bangalore: Jyothi Anil under RM Mahesh
(306, 'Jyothi Anil', 'ACM', 5, 'Bangalore', 103, 'Mahesh Kumar', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 4, 'Active', '2026-04-01', 'ACM Bangalore under RM Mahesh'),

-- LEVEL 5: SCs (7 active)
(401, 'Sourav Kundu', 'SC', 2, 'Delhi', 203, 'Atul Rai', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'SC Delhi'),
(402, 'Surya G', 'SC', 5, 'Bangalore', 103, 'Mahesh Kumar', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'SC Bangalore'),
(403, 'DEEPJOY SARKAR', 'SC', 9, 'Jharkhand', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'SC Jharkhand'),
(404, 'Bharathikannan S', 'SC', 6, 'Tamil Nadu', 204, 'Karthikeyan', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'SC Tamil Nadu under CM Karthikeyan'),
(405, 'Debabrata Mukherjee', 'SC', 8, 'West Bengal', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'SC West Bengal'),
(406, 'Subham Saha', 'SC', 3, 'Rajasthan', 202, 'Mohammad Zenul', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'SC Rajasthan'),
(407, 'Basit Ansari', 'SC', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'SC Mumbai'),

-- LEVEL 5: BDs (34 active, exactly as the sheet)
-- Tamil Nadu BDs under CM Karthikeyan, skip RM Sunny
(501, 'David J', 'BD', 6, 'Tamil Nadu', 204, 'Karthikeyan', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(502, 'Steephan', 'BD', 7, 'Telangana', 305, 'Archana D', 201, 'Nagdev R', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(503, 'Vishal Kashyap', 'BD', 4, 'Punjab', 303, 'Ruchika Agarwal', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(504, 'Ayesha Begum', 'BD', 5, 'Bangalore', 103, 'Mahesh Kumar', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', 'Reports direct to RM Mahesh per sheet'),
(505, 'Kruthi A Kumar', 'BD', 5, 'Bangalore', 306, 'Jyothi Anil', 103, 'Mahesh Kumar', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(506, 'Ankit Kumar', 'BD', 9, 'Jharkhand', 302, 'Nitin Poddar', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(507, 'V Vimal Alosious', 'BD', 6, 'Tamil Nadu', 204, 'Karthikeyan', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(508, 'Elavarasi Sekar', 'BD', 6, 'Tamil Nadu', 204, 'Karthikeyan', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(509, 'Minaketan Sahoo', 'BD', 8, 'West Bengal', 301, 'Abinash Tarai', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(510, 'Animesh Anand', 'BD', 9, 'Jharkhand', 302, 'Nitin Poddar', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(511, 'Shahjada Tanweer', 'BD', 2, 'Delhi', 203, 'Atul Rai', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(512, 'Ashis Mishra', 'BD', 8, 'West Bengal', 301, 'Abinash Tarai', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(513, 'Maryleena Bori', 'BD', 8, 'West Bengal', 205, 'Nilanjan Chatterjee', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(514, 'Gaurav Kumar', 'BD', 2, 'Delhi', 203, 'Atul Rai', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(515, 'Samir Raval', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(516, 'Avishek Pathak', 'BD', 8, 'West Bengal', 205, 'Nilanjan Chatterjee', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(517, 'Smit Solanki', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(518, 'Sumeet Ghosh', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(519, 'Reshmi Krishna', 'BD', 5, 'Bangalore', 306, 'Jyothi Anil', 103, 'Mahesh Kumar', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(520, 'VIJAYAKUMAR P', 'BD', 6, 'Tamil Nadu', 204, 'Karthikeyan', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(521, 'Vammi Rajesh', 'BD', 6, 'Tamil Nadu', 204, 'Karthikeyan', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(522, 'Kishan Madam', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(523, 'Sumit Srivastava', 'BD', 3, 'Rajasthan', 304, 'Vikas Kumar', 202, 'Mohammad Zenul', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(524, 'Geetanjali Sharma', 'BD', 2, 'Delhi', 203, 'Atul Rai', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(525, 'Gaurav Sharma', 'BD', 4, 'Punjab', 303, 'Ruchika Agarwal', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(526, 'Rimly Lahiri Chakraborty', 'BD', 8, 'West Bengal', 205, 'Nilanjan Chatterjee', 105, 'Mehak Sarraf', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(527, 'Harsha Polson', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(528, 'Najmaben Shaikh', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(529, 'Neha Gondhali', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(530, 'Ramesh P', 'BD', 6, 'Tamil Nadu', 204, 'Karthikeyan', 102, 'Sunny Babu', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(531, 'Reya Gajbhiye', 'BD', 1, 'Mumbai', 104, 'Sadanand Shetty', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(532, 'Azka Chaudhary', 'BD', 2, 'Delhi', 203, 'Atul Rai', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(533, 'Jetti Sravanthi', 'BD', 7, 'Telangana', 305, 'Archana D', 201, 'Nagdev R', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL),
(534, 'Harsimran Singh', 'BD', 4, 'Punjab', 303, 'Ruchika Agarwal', 1, 'Meera Dhanuka', 1, 'Meera Dhanuka', 5, 'Active', '2026-04-01', NULL);

-- ============================================================================
-- 2. QUARTER CONFIG (visual chip + KPI weight matrix)
-- ============================================================================

CREATE TABLE IF NOT EXISTS quarter_config (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fiscal_year SMALLINT UNSIGNED NOT NULL COMMENT 'FY end year, e.g. 27 for FY26-27',
    quarter TINYINT UNSIGNED NOT NULL COMMENT '1 to 4',
    quarter_label VARCHAR(12) NOT NULL COMMENT 'e.g. Q1 FY27',
    quarter_chip_color VARCHAR(7) NOT NULL DEFAULT '#01696F' COMMENT 'hex for the visual chip on every screen',
    quarter_chip_bg VARCHAR(7) NOT NULL DEFAULT '#F7F6F2',
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    review_window_start DATE NOT NULL COMMENT 'when quarterly review can begin',
    review_window_end DATE NOT NULL COMMENT 'hard close for target setting',
    target_gate_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 blocks review submission without targets',
    -- KPI weights for line manager scorecard. Sum should be 100.
    k1_mom_sla_weight TINYINT UNSIGNED NOT NULL DEFAULT 15,
    k2_coaching_ratio_weight TINYINT UNSIGNED NOT NULL DEFAULT 15,
    k3_signoff_speed_weight TINYINT UNSIGNED NOT NULL DEFAULT 15,
    k4_r2b_followthrough_weight TINYINT UNSIGNED NOT NULL DEFAULT 15,
    k5_stuck_closure_weight TINYINT UNSIGNED NOT NULL DEFAULT 10,
    k6_coaching_notes_weight TINYINT UNSIGNED NOT NULL DEFAULT 10,
    k7_escalation_pre_sla_weight TINYINT UNSIGNED NOT NULL DEFAULT 20,
    -- Which cadence groups fire this quarter
    dmft_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 only in Q3 per incentive rules',
    proposal_50cr_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 only in Q3',
    top_closure_cluster_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 in Q3 and Q4',
    notes VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_fy_quarter (fiscal_year, quarter),
    KEY idx_period (period_start, period_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Per-quarter config: visual chip, KPI weights, cadence activation flags.';

-- Seed FY26-27 (current year, Apr 2026 to Mar 2027)
INSERT INTO quarter_config
(fiscal_year, quarter, quarter_label, quarter_chip_color, quarter_chip_bg, period_start, period_end, review_window_start, review_window_end, target_gate_active, dmft_active, proposal_50cr_active, top_closure_cluster_active, notes)
VALUES
(27, 1, 'Q1 FY27', '#01696F', '#E8F1F2', '2026-04-01', '2026-06-30', '2026-06-25', '2026-07-05', 1, 0, 0, 0, 'Q1 - core sales motion'),
(27, 2, 'Q2 FY27', '#964219', '#F4E8DD', '2026-07-01', '2026-09-30', '2026-09-25', '2026-10-05', 1, 0, 0, 0, 'Q2 - mid year push'),
(27, 3, 'Q3 FY27', '#7A39BB', '#EFE3F5', '2026-10-01', '2026-12-31', '2026-12-22', '2027-01-05', 1, 1, 1, 1, 'Q3 - DMFT + 50 CR proposals + top closure cluster all live'),
(27, 4, 'Q4 FY27', '#A12C7B', '#F5E0EE', '2027-01-01', '2027-03-31', '2027-03-25', '2027-04-05', 1, 0, 0, 1, 'Q4 - closure cluster carries forward');

-- Seed FY27-28
INSERT INTO quarter_config
(fiscal_year, quarter, quarter_label, period_start, period_end, review_window_start, review_window_end, dmft_active, proposal_50cr_active, top_closure_cluster_active, notes)
VALUES
(28, 1, 'Q1 FY28', '2027-04-01', '2027-06-30', '2027-06-25', '2027-07-05', 0, 0, 0, NULL),
(28, 2, 'Q2 FY28', '2027-07-01', '2027-09-30', '2027-09-25', '2027-10-05', 0, 0, 0, NULL),
(28, 3, 'Q3 FY28', '2027-10-01', '2027-12-31', '2027-12-22', '2028-01-05', 1, 1, 1, NULL),
(28, 4, 'Q4 FY28', '2028-01-01', '2028-03-31', '2028-03-25', '2028-04-05', 0, 0, 1, NULL);

-- ============================================================================
-- 3. QUARTER TARGET GATE (blocks review submission)
-- ============================================================================

-- Extend review_session (migration 020) to track per-quarter target setting
ALTER TABLE review_session
    ADD COLUMN target_gate_required TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if this review is a quarter close and must set next-quarter targets' AFTER status,
    ADD COLUMN target_gate_satisfied TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 only when every direct report has next-quarter target filled' AFTER target_gate_required,
    ADD COLUMN target_gate_count_required SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'count of direct reports needing target' AFTER target_gate_satisfied,
    ADD COLUMN target_gate_count_set SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'count of direct reports with target set' AFTER target_gate_count_required,
    ADD COLUMN target_gate_satisfied_at DATETIME NULL AFTER target_gate_count_set,
    ADD COLUMN quarter_config_id INT UNSIGNED NULL AFTER target_gate_satisfied_at,
    ADD COLUMN next_quarter_config_id INT UNSIGNED NULL COMMENT 'quarter_config row whose targets need filling' AFTER quarter_config_id,
    ADD KEY idx_target_gate (target_gate_required, target_gate_satisfied),
    ADD CONSTRAINT fk_rs_qcfg FOREIGN KEY (quarter_config_id) REFERENCES quarter_config(id),
    ADD CONSTRAINT fk_rs_qcfg_next FOREIGN KEY (next_quarter_config_id) REFERENCES quarter_config(id);

-- Audit log every time a target is set inside a review
CREATE TABLE IF NOT EXISTS quarter_target_audit (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    review_session_id INT UNSIGNED NOT NULL,
    next_quarter_config_id INT UNSIGNED NOT NULL,
    set_by_uid INT UNSIGNED NOT NULL COMMENT 'manager who set this target',
    set_for_uid INT UNSIGNED NOT NULL COMMENT 'employee receiving the target',
    cluster_id TINYINT UNSIGNED NOT NULL,
    category VARCHAR(32) NOT NULL COMMENT 'matches revenue_target_matrix.category: PSU, DMFT, ANCHOR, MFT, CSR, GENERAL',
    target_rs_lakh DECIMAL(12,2) NOT NULL,
    prev_quarter_actual_rs_lakh DECIMAL(12,2) NULL,
    rationale_text TEXT NULL COMMENT 'manager note: why this number',
    set_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    overridden_at DATETIME NULL COMMENT 'set when this row was superseded',
    overridden_by_uid INT UNSIGNED NULL,
    PRIMARY KEY (id),
    KEY idx_review (review_session_id),
    KEY idx_employee (set_for_uid, next_quarter_config_id),
    KEY idx_setter (set_by_uid),
    CONSTRAINT fk_qta_review FOREIGN KEY (review_session_id) REFERENCES review_session(id),
    CONSTRAINT fk_qta_qcfg FOREIGN KEY (next_quarter_config_id) REFERENCES quarter_config(id),
    CONSTRAINT fk_qta_cluster FOREIGN KEY (cluster_id) REFERENCES cluster_master(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Every target set during a quarter review. Feeds revenue_target_matrix.';

-- ============================================================================
-- 4. EXTEND revenue_target_matrix with quarter linkage
-- ============================================================================

ALTER TABLE revenue_target_matrix
    ADD COLUMN quarter_config_id INT UNSIGNED NULL AFTER quarter,
    ADD COLUMN set_via_review_id INT UNSIGNED NULL COMMENT 'review_session that locked this target' AFTER quarter_config_id,
    ADD COLUMN set_by_uid INT UNSIGNED NULL AFTER set_via_review_id,
    ADD COLUMN set_at DATETIME NULL AFTER set_by_uid,
    ADD KEY idx_qcfg (quarter_config_id),
    ADD KEY idx_review (set_via_review_id),
    ADD CONSTRAINT fk_rtm_qcfg FOREIGN KEY (quarter_config_id) REFERENCES quarter_config(id);

-- ============================================================================
-- 5. EXTEND line_manager_scorecard to read from cadence engine
-- ============================================================================

ALTER TABLE line_manager_scorecard
    ADD COLUMN quarter_config_id INT UNSIGNED NULL AFTER period_end,
    ADD COLUMN cadence_engine_version VARCHAR(16) NULL DEFAULT '023.2' COMMENT 'which engine version computed this row' AFTER quarter_config_id,
    ADD COLUMN incentive_payout_log_id INT UNSIGNED NULL COMMENT 'engine output' AFTER cadence_engine_version,
    ADD KEY idx_qcfg (quarter_config_id),
    ADD CONSTRAINT fk_lms_qcfg FOREIGN KEY (quarter_config_id) REFERENCES quarter_config(id);

-- ============================================================================
-- 6. VIEWS
-- ============================================================================

DROP VIEW IF EXISTS v_current_quarter;
CREATE VIEW v_current_quarter AS
SELECT id, fiscal_year, quarter, quarter_label, quarter_chip_color, quarter_chip_bg,
       period_start, period_end, review_window_start, review_window_end,
       dmft_active, proposal_50cr_active, top_closure_cluster_active
FROM quarter_config
WHERE CURDATE() BETWEEN period_start AND period_end
LIMIT 1;

DROP VIEW IF EXISTS v_next_quarter;
CREATE VIEW v_next_quarter AS
SELECT id, fiscal_year, quarter, quarter_label, quarter_chip_color, quarter_chip_bg,
       period_start, period_end, review_window_start, review_window_end,
       dmft_active, proposal_50cr_active, top_closure_cluster_active
FROM quarter_config
WHERE period_start > CURDATE()
ORDER BY period_start ASC
LIMIT 1;

DROP VIEW IF EXISTS v_manager_direct_reports;
CREATE VIEW v_manager_direct_reports AS
SELECT manager_uid, manager_name,
       employee_uid, employee_name, role, cluster_id, cluster_text
FROM reporting_hierarchy
WHERE status = 'Active' AND effective_to IS NULL AND manager_uid IS NOT NULL;

DROP VIEW IF EXISTS v_target_gate_pending;
CREATE VIEW v_target_gate_pending AS
SELECT rs.id AS review_session_id,
       rs.manager_uid, rs.manager_name,
       rs.next_quarter_config_id,
       qc.quarter_label AS next_quarter_label,
       rs.target_gate_count_required - rs.target_gate_count_set AS targets_missing,
       rs.target_gate_count_required, rs.target_gate_count_set
FROM review_session rs
JOIN quarter_config qc ON qc.id = rs.next_quarter_config_id
WHERE rs.target_gate_required = 1
  AND rs.target_gate_satisfied = 0
  AND rs.status IN ('in_progress', 'pending_submit');

DROP VIEW IF EXISTS v_escalation_chain;
CREATE VIEW v_escalation_chain AS
SELECT rh.employee_uid, rh.employee_name, rh.role, rh.cluster_id, rh.cluster_text,
       rh.manager_uid AS l1_manager_uid, rh.manager_name AS l1_manager_name,
       rh.skip_manager_uid AS l2_manager_uid, rh.skip_manager_name AS l2_manager_name,
       rh.director_uid AS l3_director_uid, rh.director_name AS l3_director_name
FROM reporting_hierarchy rh
WHERE rh.status = 'Active' AND rh.effective_to IS NULL;

-- ============================================================================
-- 7. GUARDRAIL: target gate trigger
-- ============================================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_review_target_gate_check$$
CREATE TRIGGER trg_review_target_gate_check
BEFORE UPDATE ON review_session
FOR EACH ROW
BEGIN
    IF NEW.status = 'submitted' AND OLD.status != 'submitted'
       AND NEW.target_gate_required = 1
       AND NEW.target_gate_satisfied = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot submit quarter review until all direct report targets are set for next quarter.';
    END IF;
END$$

DROP TRIGGER IF EXISTS trg_target_audit_to_matrix$$
CREATE TRIGGER trg_target_audit_to_matrix
AFTER INSERT ON quarter_target_audit
FOR EACH ROW
BEGIN
    INSERT INTO revenue_target_matrix
        (employee_uid, cluster_id, category, fiscal_year, quarter,
         quarter_config_id, target_rs_lakh,
         set_via_review_id, set_by_uid, set_at, created_at)
    SELECT NEW.set_for_uid, NEW.cluster_id, NEW.category,
           qc.fiscal_year, qc.quarter,
           NEW.next_quarter_config_id, NEW.target_rs_lakh,
           NEW.review_session_id, NEW.set_by_uid, NEW.set_at, NOW()
    FROM quarter_config qc
    WHERE qc.id = NEW.next_quarter_config_id
    ON DUPLICATE KEY UPDATE
        target_rs_lakh = NEW.target_rs_lakh,
        set_via_review_id = NEW.review_session_id,
        set_by_uid = NEW.set_by_uid,
        set_at = NEW.set_at,
        updated_at = NOW();
END$$

DELIMITER ;

-- ============================================================================
-- 8. ROLLBACK BLOCK (for emergency revert)
-- ============================================================================
-- DROP TRIGGER IF EXISTS trg_target_audit_to_matrix;
-- DROP TRIGGER IF EXISTS trg_review_target_gate_check;
-- DROP VIEW IF EXISTS v_escalation_chain;
-- DROP VIEW IF EXISTS v_target_gate_pending;
-- DROP VIEW IF EXISTS v_manager_direct_reports;
-- DROP VIEW IF EXISTS v_next_quarter;
-- DROP VIEW IF EXISTS v_current_quarter;
-- ALTER TABLE line_manager_scorecard DROP FOREIGN KEY fk_lms_qcfg, DROP COLUMN incentive_payout_log_id, DROP COLUMN cadence_engine_version, DROP COLUMN quarter_config_id;
-- ALTER TABLE revenue_target_matrix DROP FOREIGN KEY fk_rtm_qcfg, DROP COLUMN set_at, DROP COLUMN set_by_uid, DROP COLUMN set_via_review_id, DROP COLUMN quarter_config_id;
-- ALTER TABLE review_session DROP FOREIGN KEY fk_rs_qcfg_next, DROP FOREIGN KEY fk_rs_qcfg, DROP COLUMN next_quarter_config_id, DROP COLUMN quarter_config_id, DROP COLUMN target_gate_satisfied_at, DROP COLUMN target_gate_count_set, DROP COLUMN target_gate_count_required, DROP COLUMN target_gate_satisfied, DROP COLUMN target_gate_required;
-- DROP TABLE IF EXISTS quarter_target_audit;
-- DROP TABLE IF EXISTS quarter_config;
-- DROP TABLE IF EXISTS reporting_hierarchy;

COMMIT;
