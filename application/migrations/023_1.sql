-- =====================================================================
-- Migration 023.1 - DMFT + Corporate Sponsor + BD-to-RM Handover
--
-- DMFT = District Mineral Foundation Trust. Statutory trust set up in every
-- district affected by mining operations under Section 9B of the Mines and
-- Minerals Development and Regulation Act 1957 (amended 2015). Mining lease
-- holders contribute a percentage of royalty to the trust, and the Collector / DM
-- chairs it. Funds are spent on education, healthcare, drinking water and skills
-- in mining-affected areas. The Collector is the single decision authority on
-- school deployments funded by DMFT.
--
-- Patch on top of migration 023 to lock in the refined logic:
--   1. DMFT is a single category. NO seed-school prerequisite. RM walks
--      straight into the Collector office and pitches the trust.
--   2. RM owns DMFT end-to-end from cstatus 1. No BD hunting. Closure credit
--      0 BD / 100 RM.
--   3. Anchor uses CSR budget computed from 3-year avg net profit per
--      Companies Act sec 135, threshold Rs 5 crore. RM owns from cstatus 1
--      if no BD touch, else BD hunts cstatus 1-7 and RM takes over cstatus 8+.
--   4. PSU / STANDARD - BD hunts cstatus 1-7, RM auto-takes-over at cstatus 8+.
--      Closure credit splits 30 BD / 70 RM. Joint meetings at cstatus 8/9/12.
--   5. DMFT identification:
--      (a) Primary signal - school appears on the DMFT portal (dmft.gov.in)
--          pulled into `dmft_portal_snapshot` table by a separate scraper job
--      (b) Secondary signal - school's district matches `dmft_eligible_district`
--          (pan-India mining-district whitelist, every state with notified DMFTs)
--      (c) Manual override - RM can set tag via account_category_tag.set_manual=1
--   6. CSR budget capture: existing `fbudget` field on init_call and mom_data
--      stays as prospecting budget. NEW column `csr_budget_rs` added next to it
--      to capture the corporate sponsor's annual CSR pool size. Filled by BD on
--      the Add-New-Lead screen and during MoM submission.
--   7. Annual revenue target is BD-WISE not cluster-wise. Each BD = Rs 2 cr
--      annual. Cluster rows in revenue_target_matrix are kept for roll-up
--      reporting only, not for personal target setting.
--
-- Run order: after stem_migration_023_sql.sql, before any data seed
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. corporate_sponsor table (CSR budget source of truth)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS corporate_sponsor;
CREATE TABLE corporate_sponsor (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  legal_name VARCHAR(255) NOT NULL,
  cin_or_pan VARCHAR(32) NULL COMMENT 'MCA CIN for company, PAN for trust',
  entity_type ENUM('public_ltd','private_ltd','llp','trust','section_8','foundation') NOT NULL DEFAULT 'public_ltd',
  industry_code VARCHAR(64) NULL,
  hq_location VARCHAR(255) NULL,
  net_profit_fy1_rs DECIMAL(18,2) NULL COMMENT 'most recent FY net profit in Rs',
  net_profit_fy2_rs DECIMAL(18,2) NULL,
  net_profit_fy3_rs DECIMAL(18,2) NULL,
  csr_budget_rs DECIMAL(18,2) NULL COMMENT 'explicit CSR budget if disclosed; else computed as 2 pct of 3FY avg net profit',
  csr_budget_source ENUM('mca_filing','annual_report','self_disclosed','computed') NOT NULL DEFAULT 'computed',
  csr_budget_set_at DATETIME NULL,
  schools_sponsored_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'denormalised, refreshed nightly',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_cin (cin_or_pan),
  KEY idx_csr_budget (csr_budget_rs),
  KEY idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 2. ALTER init_call: link to corporate_sponsor and store district token
-- ---------------------------------------------------------------------
ALTER TABLE init_call
  ADD COLUMN corporate_sponsor_id INT UNSIGNED NULL AFTER mainbd,
  ADD COLUMN sponsor_relation ENUM('csr_funded','corporate_gift','co_funded','direct_purchase') NULL AFTER corporate_sponsor_id,
  ADD COLUMN csr_budget_rs DECIMAL(18,2) NULL AFTER fbudget COMMENT 'corporate sponsor annual CSR pool in Rs - captured at Add-New-Lead and during MoM. Separate from fbudget which is the prospecting budget for this specific lead.',
  ADD COLUMN district_token VARCHAR(64) NULL AFTER compny_loction COMMENT 'lower-cased district name parsed from compny_loction',
  ADD KEY idx_sponsor (corporate_sponsor_id),
  ADD KEY idx_csr_budget (csr_budget_rs),
  ADD KEY idx_district (district_token);

-- Mirror csr_budget_rs onto mom_data so BD can confirm/update it during MoM
ALTER TABLE mom_data
  ADD COLUMN csr_budget_rs DECIMAL(18,2) NULL AFTER budgt COMMENT 'corporate CSR pool - mirror of init_call.csr_budget_rs, BD can update during MoM',
  ADD KEY idx_csr_budget (csr_budget_rs);

-- ---------------------------------------------------------------------
-- 2b. BD annual target (Rs 2 cr per BD per FY)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS bd_annual_target;
CREATE TABLE bd_annual_target (
  bd_uid INT UNSIGNED NOT NULL,
  fy_year VARCHAR(8) NOT NULL COMMENT 'e.g. FY27',
  target_rs DECIMAL(18,2) NOT NULL DEFAULT 20000000 COMMENT 'default Rs 2 cr',
  actual_rs DECIMAL(18,2) NOT NULL DEFAULT 0 COMMENT 'denormalised, refreshed nightly from revenue_actual_ledger',
  set_by_uid INT UNSIGNED NULL,
  set_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (bd_uid, fy_year),
  KEY idx_fy (fy_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- category_code ENUM stays unchanged from Migration 023:
--   ('PSU','DMFT','ANCHOR','STANDARD')
-- No new codes added. Single DMFT category is the locked decision per founder
-- direction "nothing to do with seed". revenue_target_matrix already has
-- DMFT rows seeded at Rs 30 cr annual from Migration 023 - no migration needed.

-- ---------------------------------------------------------------------
-- 3. lead_handover_log: BD-to-RM handover audit trail
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS lead_handover_log;
CREATE TABLE lead_handover_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lead_id INT UNSIGNED NOT NULL,
  from_bd_uid INT UNSIGNED NOT NULL,
  to_rm_uid INT UNSIGNED NOT NULL,
  from_cstatus TINYINT NOT NULL,
  to_cstatus TINYINT NOT NULL,
  reason ENUM('cstatus_8_auto','dmft_rm_owns_from_day_1','manual_anchor_rm_takeover','manual_dmft_collector_open','cm_escalation') NOT NULL,
  triggered_by ENUM('system','cm','rm','director') NOT NULL DEFAULT 'system',
  triggered_by_uid INT UNSIGNED NULL,
  bd_credit_share_pct TINYINT UNSIGNED NOT NULL DEFAULT 30 COMMENT 'closure credit retained by BD (0 for DMFT)',
  rm_credit_share_pct TINYINT UNSIGNED NOT NULL DEFAULT 70 COMMENT '100 for DMFT, 70 for PSU/Anchor/Standard',
  bd_notified_at DATETIME NULL,
  rm_notified_at DATETIME NULL,
  notes VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_lead (lead_id),
  KEY idx_from_bd (from_bd_uid),
  KEY idx_to_rm (to_rm_uid),
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 4. Trigger: auto-handover when cstatus hits 8
-- ---------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_init_call_cstatus_handover;

DELIMITER $$
CREATE TRIGGER trg_init_call_cstatus_handover
AFTER UPDATE ON init_call
FOR EACH ROW
BEGIN
  DECLARE v_cluster_id INT;
  DECLARE v_rm_uid INT;
  DECLARE v_existing_handover INT DEFAULT 0;

  -- Fire only on BD-managed leads crossing into cstatus 8+ for the first time
  IF NEW.cstatus >= 8 AND OLD.cstatus < 8 AND NEW.mainbd IS NOT NULL THEN

    -- Skip if a handover is already logged for this lead
    SELECT COUNT(*) INTO v_existing_handover
    FROM lead_handover_log
    WHERE lead_id = NEW.id;

    IF v_existing_handover = 0 THEN
      -- Find the RM for the BD's cluster
      SELECT u_bd.cluster_id INTO v_cluster_id
      FROM user u_bd WHERE u_bd.uid = NEW.mainbd LIMIT 1;

      SELECT u_rm.uid INTO v_rm_uid
      FROM user u_rm
      WHERE u_rm.type_id = 28
        AND u_rm.cluster_id = v_cluster_id
        AND u_rm.status = 1
      ORDER BY u_rm.uid ASC
      LIMIT 1;

      IF v_rm_uid IS NOT NULL THEN
        INSERT INTO lead_handover_log (
          lead_id, from_bd_uid, to_rm_uid,
          from_cstatus, to_cstatus,
          reason, triggered_by,
          bd_credit_share_pct, rm_credit_share_pct,
          notes, created_at
        ) VALUES (
          NEW.id, NEW.mainbd, v_rm_uid,
          OLD.cstatus, NEW.cstatus,
          'cstatus_8_auto', 'system',
          30, 70,
          CONCAT('Auto handover at cstatus ', NEW.cstatus, '. BD retains 30 percent closure credit.'),
          NOW()
        );

        -- Reassign mainbd to RM but keep BD as support_bd
        UPDATE init_call
        SET support_bd = NEW.mainbd,
            mainbd = v_rm_uid,
            handover_at = NOW()
        WHERE id = NEW.id;
      END IF;
    END IF;
  END IF;
END$$
DELIMITER ;

-- Need support_bd and handover_at columns on init_call
ALTER TABLE init_call
  ADD COLUMN support_bd INT UNSIGNED NULL AFTER mainbd COMMENT 'original BD after RM takeover',
  ADD COLUMN handover_at DATETIME NULL AFTER support_bd;

-- ---------------------------------------------------------------------
-- 5. View: DMFT pipeline by district. For each district with DMFT-tagged
--    schools, show count, stage spread, RM, and last Collector touch.
-- ---------------------------------------------------------------------
DROP VIEW IF EXISTS v_dmft_district_pipeline;
CREATE VIEW v_dmft_district_pipeline AS
SELECT
  ic.district_token AS district,
  COUNT(DISTINCT ic.id) AS dmft_school_count,
  SUM(CASE WHEN ic.cstatus BETWEEN 1 AND 5 THEN 1 ELSE 0 END) AS early_stage_count,
  SUM(CASE WHEN ic.cstatus BETWEEN 6 AND 9 THEN 1 ELSE 0 END) AS mid_stage_count,
  SUM(CASE WHEN ic.cstatus = 12 THEN 1 ELSE 0 END) AS won_count,
  MAX(ic.mainbd) AS rm_uid_sample,
  (
    SELECT MAX(t2.event_date)
    FROM tblcallevents t2
    JOIN init_call ic2 ON ic2.id = t2.cid_id
    WHERE ic2.district_token = ic.district_token
      AND ic2.category_code = 'DMFT'
      AND (
        LOWER(t2.event_remarks) LIKE '%collector%'
        OR LOWER(t2.event_remarks) LIKE '%district magistrate%'
        OR LOWER(t2.event_remarks) LIKE '%dmft%'
      )
      AND t2.event_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
  ) AS last_collector_touch_at
FROM init_call ic
WHERE ic.category_code = 'DMFT'
  AND ic.district_token IS NOT NULL
GROUP BY ic.district_token;

-- ---------------------------------------------------------------------
-- 6. View: anchor sponsors with multi-school presence (RM relationship view)
-- ---------------------------------------------------------------------
DROP VIEW IF EXISTS v_anchor_sponsors;
CREATE VIEW v_anchor_sponsors AS
SELECT
  cs.id AS sponsor_id,
  cs.legal_name,
  cs.csr_budget_rs,
  CASE
    WHEN cs.csr_budget_rs IS NOT NULL THEN cs.csr_budget_rs
    ELSE (
      COALESCE(cs.net_profit_fy1_rs,0) + COALESCE(cs.net_profit_fy2_rs,0) + COALESCE(cs.net_profit_fy3_rs,0)
    ) / NULLIF(
      (CASE WHEN cs.net_profit_fy1_rs IS NOT NULL THEN 1 ELSE 0 END)
      + (CASE WHEN cs.net_profit_fy2_rs IS NOT NULL THEN 1 ELSE 0 END)
      + (CASE WHEN cs.net_profit_fy3_rs IS NOT NULL THEN 1 ELSE 0 END), 0
    ) * 0.02
  END AS effective_csr_budget_rs,
  COUNT(DISTINCT ic.id) AS schools_in_pipeline,
  SUM(CASE WHEN ic.cstatus = 12 THEN 1 ELSE 0 END) AS schools_won,
  SUM(CASE WHEN ic.cstatus BETWEEN 8 AND 11 THEN 1 ELSE 0 END) AS schools_at_closure_stage,
  MAX(CASE WHEN ic.cstatus = 12 THEN ic.modify_date END) AS most_recent_win_at
FROM corporate_sponsor cs
LEFT JOIN init_call ic ON ic.corporate_sponsor_id = cs.id
WHERE cs.is_active = 1
GROUP BY cs.id
HAVING effective_csr_budget_rs >= 50000000;

-- ---------------------------------------------------------------------
-- 7. View: BD-to-RM handover scorecard (for line manager review)
-- ---------------------------------------------------------------------
DROP VIEW IF EXISTS v_handover_scorecard;
CREATE VIEW v_handover_scorecard AS
SELECT
  l.from_bd_uid AS bd_uid,
  u_bd.first_name AS bd_name,
  l.to_rm_uid AS rm_uid,
  u_rm.first_name AS rm_name,
  COUNT(*) AS total_handovers,
  SUM(CASE WHEN ic.cstatus = 12 THEN 1 ELSE 0 END) AS won_after_handover,
  SUM(CASE WHEN ic.cstatus = 13 THEN 1 ELSE 0 END) AS lost_after_handover,
  SUM(CASE WHEN ic.cstatus BETWEEN 8 AND 11 THEN 1 ELSE 0 END) AS still_at_closure,
  ROUND(SUM(CASE WHEN ic.cstatus = 12 THEN 1 ELSE 0 END) / NULLIF(COUNT(*),0) * 100, 1) AS win_rate_pct,
  AVG(DATEDIFF(IFNULL(ic.modify_date, NOW()), l.created_at)) AS avg_days_to_resolution
FROM lead_handover_log l
JOIN init_call ic ON ic.id = l.lead_id
LEFT JOIN user u_bd ON u_bd.uid = l.from_bd_uid
LEFT JOIN user u_rm ON u_rm.uid = l.to_rm_uid
WHERE l.created_at >= DATE_SUB(NOW(), INTERVAL 180 DAY)
GROUP BY l.from_bd_uid, l.to_rm_uid;

-- ---------------------------------------------------------------------
-- 8. dmft_portal_snapshot - schools listed on the DMFT portal (dmft.gov.in)
--    Populated by a separate scraper job that runs weekly. Primary DMFT
--    identification signal: if a school name + district matches a portal row
--    within the last 90 days, tag it DMFT.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS dmft_portal_snapshot;
CREATE TABLE dmft_portal_snapshot (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  school_name VARCHAR(255) NOT NULL,
  school_name_normalised VARCHAR(255) NOT NULL COMMENT 'lowercased, punctuation stripped',
  district_token VARCHAR(64) NOT NULL,
  state_name VARCHAR(64) NOT NULL,
  proposal_status VARCHAR(64) NULL COMMENT 'as seen on portal: proposed, sanctioned, in_progress, completed',
  sanctioned_amount_rs DECIMAL(18,2) NULL,
  portal_url VARCHAR(512) NULL,
  snapshot_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_school_norm (school_name_normalised),
  KEY idx_district (district_token),
  KEY idx_snapshot (snapshot_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 9. Seed DMFT-eligible district reference rows (pan-India)
--    Districts notified under Section 9B of Mines and Minerals Act with an
--    active District Mineral Foundation Trust. Used as secondary fallback
--    when the portal snapshot has no row for the school.
--    Sourced from DMFT portal state directories: 11 states with active funds.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS dmft_eligible_district;
CREATE TABLE dmft_eligible_district (
  district_token VARCHAR(64) NOT NULL,
  state_name VARCHAR(64) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  notes VARCHAR(255) NULL,
  PRIMARY KEY (district_token),
  KEY idx_state (state_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO dmft_eligible_district (district_token, state_name, notes) VALUES
-- Odisha (11)
('keonjhar','Odisha','Iron ore belt'),
('sundargarh','Odisha','Iron and manganese'),
('jajpur','Odisha','Chromite'),
('angul','Odisha','Coal'),
('jharsuguda','Odisha','Coal'),
('mayurbhanj','Odisha','Iron ore'),
('koraput','Odisha','Bauxite'),
('kalahandi','Odisha','Bauxite'),
('rayagada','Odisha','Bauxite'),
('sambalpur','Odisha','Coal'),
('dhenkanal','Odisha','Coal'),
-- Jharkhand (11)
('dhanbad','Jharkhand','Coal capital'),
('bokaro','Jharkhand','Coal and steel'),
('hazaribagh','Jharkhand','Coal'),
('ramgarh','Jharkhand','Coal'),
('west singhbhum','Jharkhand','Iron ore'),
('east singhbhum','Jharkhand','Copper and chromite'),
('saraikela','Jharkhand','Iron ore'),
('chatra','Jharkhand','Coal'),
('godda','Jharkhand','Coal'),
('pakur','Jharkhand','Stone'),
('sahebganj','Jharkhand','Stone'),
-- Chhattisgarh (12)
('raigarh','Chhattisgarh','Coal'),
('korba','Chhattisgarh','Coal'),
('bastar','Chhattisgarh','Iron ore'),
('dantewada','Chhattisgarh','Iron ore'),
('kanker','Chhattisgarh','Iron ore'),
('kondagaon','Chhattisgarh','Iron ore'),
('surguja','Chhattisgarh','Coal'),
('balod','Chhattisgarh','Iron ore'),
('rajnandgaon','Chhattisgarh','Iron ore'),
('bilaspur','Chhattisgarh','Coal'),
('janjgir','Chhattisgarh','Coal'),
('mungeli','Chhattisgarh','Coal'),
-- Maharashtra (6)
('chandrapur','Maharashtra','Coal'),
('gadchiroli','Maharashtra','Iron ore'),
('yavatmal','Maharashtra','Coal'),
('nagpur','Maharashtra','Manganese'),
('bhandara','Maharashtra','Manganese'),
('sindhudurg','Maharashtra','Iron ore'),
-- Karnataka (5)
('bellary','Karnataka','Iron ore'),
('hospet','Karnataka','Iron ore'),
('tumkur','Karnataka','Iron ore'),
('chitradurga','Karnataka','Iron ore'),
('bagalkot','Karnataka','Limestone'),
-- Andhra Pradesh (5)
('anantapur','Andhra Pradesh','Iron ore'),
('kadapa','Andhra Pradesh','Barytes'),
('kurnool','Andhra Pradesh','Limestone'),
('prakasam','Andhra Pradesh','Limestone'),
('visakhapatnam','Andhra Pradesh','Bauxite'),
-- Telangana (4)
('khammam','Telangana','Coal'),
('warangal','Telangana','Coal'),
('peddapalli','Telangana','Coal'),
('mancherial','Telangana','Coal'),
-- Madhya Pradesh (6)
('singrauli','Madhya Pradesh','Coal'),
('shahdol','Madhya Pradesh','Coal'),
('umaria','Madhya Pradesh','Coal'),
('anuppur','Madhya Pradesh','Coal'),
('sidhi','Madhya Pradesh','Coal'),
('katni','Madhya Pradesh','Limestone'),
-- Rajasthan (6)
('udaipur','Rajasthan','Zinc and lead'),
('rajsamand','Rajasthan','Marble'),
('chittorgarh','Rajasthan','Limestone'),
('bhilwara','Rajasthan','Mica'),
('nagaur','Rajasthan','Gypsum'),
('jodhpur','Rajasthan','Limestone'),
-- Goa (2)
('north goa','Goa','Iron ore'),
('south goa','Goa','Iron ore'),
-- Gujarat (3)
('kutch','Gujarat','Lignite and bauxite'),
('bhavnagar','Gujarat','Lignite'),
('panchmahal','Gujarat','Limestone'),
-- Tamil Nadu (3)
('salem','Tamil Nadu','Magnesite and iron'),
('namakkal','Tamil Nadu','Limestone'),
('tirunelveli','Tamil Nadu','Limestone');

-- =====================================================================
-- END MIGRATION 023.1
-- =====================================================================
