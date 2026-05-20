-- Migration 041: Corporate CSR Prospecting Agent
-- All tables use _v2 suffix. Idempotent. Re-runnable.
-- Parallel to existing migration 019 (school-side Prospect_model).

SET FOREIGN_KEY_CHECKS = 1;

-- 1. csr_corporate_master_v2
CREATE TABLE IF NOT EXISTS csr_corporate_master_v2 (
  csr_corporate_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cin VARCHAR(40) UNIQUE COMMENT 'MCA CIN from csr.gov.in',
  company_name VARCHAR(255) NOT NULL,
  company_type ENUM('listed','unlisted','private','psu','foundation','trust') NOT NULL DEFAULT 'private',
  hq_city VARCHAR(80),
  hq_state VARCHAR(80),
  industry VARCHAR(80),
  revenue_band ENUM('lt_500_cr','500_to_1000_cr','1000_to_5000_cr','5000_to_10000_cr','gt_10000_cr'),
  employee_count INT,
  csr_obligation_rs_cr DECIMAL(10,2) COMMENT 'Section 135 mandated 2 percent of avg PAT',
  csr_spent_last_fy_rs_cr DECIMAL(10,2),
  csr_education_share_pct DECIMAL(5,2),
  has_foundation_arm TINYINT(1) DEFAULT 0,
  foundation_name VARCHAR(255),
  data_source ENUM('csr_gov_in','apollo','manual','hybrid') DEFAULT 'csr_gov_in',
  last_synced_at DATETIME,
  active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_hq_city_state (hq_city, hq_state),
  INDEX idx_csr_education (csr_education_share_pct, csr_spent_last_fy_rs_cr),
  INDEX idx_active_spend (active, csr_spent_last_fy_rs_cr)
);

-- 2. csr_project_v2 (granular projects per corporate, this is where geography lives)
CREATE TABLE IF NOT EXISTS csr_project_v2 (
  csr_project_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  csr_corporate_id INT UNSIGNED NOT NULL,
  project_name VARCHAR(255) NOT NULL,
  project_theme ENUM('education','health','environment','rural_dev','women_empowerment','skill_dev','other') DEFAULT 'other',
  project_state VARCHAR(80),
  project_district VARCHAR(80),
  project_city VARCHAR(80),
  start_year YEAR,
  end_year YEAR,
  spent_rs_cr DECIMAL(10,2),
  cycle_status ENUM('planning','active','last_quarter','closed','renewing') DEFAULT 'active',
  fy_label VARCHAR(20) COMMENT 'e.g. FY24, FY25',
  source_url VARCHAR(500),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_district_theme (project_district, project_theme, cycle_status),
  INDEX idx_corporate_fy (csr_corporate_id, fy_label),
  INDEX idx_state_theme (project_state, project_theme),
  CONSTRAINT fk_csr_proj_corp FOREIGN KEY (csr_corporate_id) REFERENCES csr_corporate_master_v2(csr_corporate_id) ON DELETE CASCADE
);

-- 3. csr_decision_maker_v2 (Apollo + LinkedIn contacts)
CREATE TABLE IF NOT EXISTS csr_decision_maker_v2 (
  csr_dm_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  csr_corporate_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  designation VARCHAR(120),
  designation_role ENUM('csr_head','foundation_trustee','sustainability_head','head_hr_csr','md_ceo','board_csr_committee','other') DEFAULT 'other',
  email VARCHAR(120),
  phone VARCHAR(40),
  linkedin_url VARCHAR(255),
  linkedin_verified_at DATETIME,
  csr_confidence_score TINYINT UNSIGNED DEFAULT 0 COMMENT '0-100 from LinkedinCsrAgent',
  source ENUM('apollo','linkedin','manual','referral') DEFAULT 'apollo',
  apollo_person_id VARCHAR(60),
  last_synced_at DATETIME,
  active TINYINT(1) DEFAULT 1,
  INDEX idx_corp_role (csr_corporate_id, designation_role, active),
  INDEX idx_confidence (csr_confidence_score),
  CONSTRAINT fk_csr_dm_corp FOREIGN KEY (csr_corporate_id) REFERENCES csr_corporate_master_v2(csr_corporate_id) ON DELETE CASCADE
);

-- 4. political_influencer_master_v2 (MPs, MLAs, collectors, education officers)
CREATE TABLE IF NOT EXISTS political_influencer_master_v2 (
  influencer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role ENUM('mp','mla','district_collector','municipal_commissioner','education_secretary','smc_chair','panchayat_head','district_csr_chair') NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  constituency VARCHAR(120),
  state VARCHAR(80),
  district VARCHAR(80),
  party VARCHAR(40),
  office_address TEXT,
  contact_phone VARCHAR(40),
  email VARCHAR(120),
  tenure_start DATE,
  tenure_end DATE,
  active TINYINT(1) DEFAULT 1,
  notes TEXT COMMENT 'pet causes, prior CSR partnerships, BD intro angle',
  source_url VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_district_role (district, state, role, active),
  INDEX idx_state_role (state, role, active),
  INDEX idx_party (party)
);

-- 5. corporate_csr_prospect_run_v2 (one run per BD per day)
CREATE TABLE IF NOT EXISTS corporate_csr_prospect_run_v2 (
  run_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bd_uid INT UNSIGNED NOT NULL,
  area_name VARCHAR(120),
  cluster_id TINYINT UNSIGNED,
  district VARCHAR(80),
  target_plan_date DATE NOT NULL,
  triggered_by ENUM('cron','bd_app','admin') DEFAULT 'cron',
  total_suggested INT DEFAULT 0,
  accepted_count INT DEFAULT 0,
  dismissed_count INT DEFAULT 0,
  seeded_count INT DEFAULT 0,
  apollo_calls_made SMALLINT DEFAULT 0,
  linkedin_calls_made SMALLINT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bd_date (bd_uid, target_plan_date),
  INDEX idx_date (target_plan_date)
);

-- 6. corporate_csr_suggestion_v2 (the actual ranked output)
CREATE TABLE IF NOT EXISTS corporate_csr_suggestion_v2 (
  suggestion_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  run_id BIGINT UNSIGNED NOT NULL,
  bd_uid INT UNSIGNED NOT NULL,
  csr_corporate_id INT UNSIGNED NOT NULL,
  csr_project_id INT UNSIGNED COMMENT 'specific project in BD area',
  csr_dm_id INT UNSIGNED COMMENT 'recommended decision maker',
  influencer_id INT UNSIGNED COMMENT 'local political angle',
  rank_score DECIMAL(6,2) COMMENT '0-100 composite score',
  rank_band ENUM('A','B','C','D') DEFAULT 'C',
  rank_reasons JSON COMMENT 'why this corporate ranks here',
  outreach_angle VARCHAR(60) COMMENT 'channel_partner | foundation_aligned | renewal_hot | influencer_intro | new_relationship',
  outreach_blurb TEXT,
  status ENUM('suggested','accepted','dismissed','contacted','meeting_done','converted_lead') DEFAULT 'suggested',
  existing_init_call_id INT UNSIGNED COMMENT 'if already in funnel, skip suggesting',
  accepted_at DATETIME,
  dismissed_at DATETIME,
  dismiss_reason VARCHAR(120),
  init_call_id_seeded INT UNSIGNED COMMENT 'init_call.cid_id once seeded',
  daily_planner_id_seeded INT UNSIGNED,
  for_plan_date DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bd_status_date (bd_uid, status, for_plan_date),
  INDEX idx_run (run_id),
  INDEX idx_corp (csr_corporate_id),
  CONSTRAINT fk_ccs_run FOREIGN KEY (run_id) REFERENCES corporate_csr_prospect_run_v2(run_id) ON DELETE CASCADE,
  CONSTRAINT fk_ccs_corp FOREIGN KEY (csr_corporate_id) REFERENCES csr_corporate_master_v2(csr_corporate_id),
  CONSTRAINT fk_ccs_dm FOREIGN KEY (csr_dm_id) REFERENCES csr_decision_maker_v2(csr_dm_id) ON DELETE SET NULL,
  CONSTRAINT fk_ccs_inf FOREIGN KEY (influencer_id) REFERENCES political_influencer_master_v2(influencer_id) ON DELETE SET NULL
);

-- 7. apollo_lookup_log_v2 (quota tracking)
CREATE TABLE IF NOT EXISTS apollo_lookup_log_v2 (
  lookup_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  csr_corporate_id INT UNSIGNED,
  apollo_org_id VARCHAR(60),
  query_payload JSON,
  response_status SMALLINT,
  contacts_returned SMALLINT DEFAULT 0,
  credits_used SMALLINT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_corp_date (csr_corporate_id, created_at),
  INDEX idx_date (created_at)
);

-- 8. csr_gov_sync_log_v2 (track csr.gov.in pulls)
CREATE TABLE IF NOT EXISTS csr_gov_sync_log_v2 (
  sync_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sync_type ENUM('full','incremental','one_corporate') DEFAULT 'incremental',
  cin VARCHAR(40),
  rows_inserted INT DEFAULT 0,
  rows_updated INT DEFAULT 0,
  rows_skipped INT DEFAULT 0,
  errors TEXT,
  started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  finished_at DATETIME,
  INDEX idx_started (started_at)
);

-- 9. apollo_daily_quota_v2 (running count for cap enforcement)
CREATE TABLE IF NOT EXISTS apollo_daily_quota_v2 (
  quota_date DATE PRIMARY KEY,
  calls_made SMALLINT DEFAULT 0,
  credits_used SMALLINT DEFAULT 0,
  daily_cap SMALLINT DEFAULT 80,
  last_call_at DATETIME
);

-- 10. View: today's suggestions joined with full corporate + DM + influencer
CREATE OR REPLACE VIEW v_corp_csr_suggestions_today AS
SELECT
  s.suggestion_id,
  s.bd_uid,
  s.rank_score,
  s.rank_band,
  s.outreach_angle,
  s.outreach_blurb,
  s.status,
  s.for_plan_date,
  c.csr_corporate_id,
  c.cin,
  c.company_name,
  c.company_type,
  c.hq_city,
  c.hq_state,
  c.csr_spent_last_fy_rs_cr,
  c.csr_education_share_pct,
  c.has_foundation_arm,
  c.foundation_name,
  p.csr_project_id,
  p.project_name,
  p.project_theme,
  p.project_district,
  p.project_state,
  p.spent_rs_cr AS project_spend_rs_cr,
  p.cycle_status,
  dm.csr_dm_id,
  dm.full_name AS dm_name,
  dm.designation AS dm_designation,
  dm.designation_role AS dm_role,
  dm.email AS dm_email,
  dm.linkedin_url AS dm_linkedin,
  dm.csr_confidence_score AS dm_confidence,
  inf.influencer_id,
  inf.full_name AS influencer_name,
  inf.role AS influencer_role,
  inf.party AS influencer_party,
  inf.constituency AS influencer_constituency
FROM corporate_csr_suggestion_v2 s
JOIN csr_corporate_master_v2 c ON c.csr_corporate_id = s.csr_corporate_id
LEFT JOIN csr_project_v2 p ON p.csr_project_id = s.csr_project_id
LEFT JOIN csr_decision_maker_v2 dm ON dm.csr_dm_id = s.csr_dm_id
LEFT JOIN political_influencer_master_v2 inf ON inf.influencer_id = s.influencer_id
WHERE s.for_plan_date >= CURDATE();

-- 11. View: Apollo quota status today
CREATE OR REPLACE VIEW v_apollo_quota_today AS
SELECT
  quota_date,
  calls_made,
  credits_used,
  daily_cap,
  (daily_cap - calls_made) AS calls_remaining,
  ROUND((calls_made / daily_cap) * 100, 1) AS pct_used,
  CASE WHEN calls_made >= daily_cap THEN 'EXHAUSTED'
       WHEN calls_made >= (daily_cap * 0.9) THEN 'NEAR_CAP'
       ELSE 'OK' END AS quota_status
FROM apollo_daily_quota_v2
WHERE quota_date = CURDATE();

-- 12. View: corporates with hot renewal windows in next 90 days
CREATE OR REPLACE VIEW v_csr_renewals_hot AS
SELECT
  c.csr_corporate_id,
  c.company_name,
  c.csr_spent_last_fy_rs_cr,
  p.csr_project_id,
  p.project_name,
  p.project_district,
  p.project_theme,
  p.end_year,
  p.cycle_status
FROM csr_corporate_master_v2 c
JOIN csr_project_v2 p ON p.csr_corporate_id = c.csr_corporate_id
WHERE p.cycle_status IN ('last_quarter','renewing')
  AND c.active = 1
ORDER BY c.csr_spent_last_fy_rs_cr DESC;

-- 13. Seed Apollo quota row for today
INSERT IGNORE INTO apollo_daily_quota_v2 (quota_date, calls_made, credits_used, daily_cap)
VALUES (CURDATE(), 0, 0, 80);

-- 14. Seed a sample influencer record so probe queries return non-empty
INSERT IGNORE INTO political_influencer_master_v2
  (influencer_id, role, full_name, constituency, state, district, party, active, source_url)
VALUES
  (1, 'district_collector', 'Sample Collector', NULL, 'Maharashtra', 'Mumbai City', NULL, 1, 'manual_seed');

-- End of migration 041
