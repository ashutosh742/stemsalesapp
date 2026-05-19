-- =====================================================================
-- STEM Migration 022 - Line Manager Accountability + Stage Signoff Gates
-- Author: STEM Build Agent
-- Date: 16 May 2026
-- Target: stem_staging first, production after GitHub access Mon 18 May 2026
-- Sibling of: migration 012 (BD progression), 012.2 (WDL attribution),
--             013 (planning grade), 020 (review v2), 021 (MoM v2 + CSR)
--
-- Rollback: see stem_migration_022_deploy_runbook.md section "Rollback"
-- =====================================================================

START TRANSACTION;

-- ---------------------------------------------------------------------
-- Table 1. lead_stage_signoff (CREATE - new in 022, was only spec in 021)
-- One row per hop request. Hops: G1=6to7, G2=7to8, G3=8to9, G4=9to12.
-- Hard gate enforced when gate_strength='hard' (the default in pilot).
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS lead_stage_signoff (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  init_call_id INT UNSIGNED NOT NULL,
  bd_uid INT UNSIGNED NOT NULL,
  cm_uid INT UNSIGNED NULL,
  rm_uid INT UNSIGNED NULL,
  gate_code ENUM('G1','G2','G3','G4') NOT NULL,
  from_cstatus TINYINT UNSIGNED NOT NULL,
  to_cstatus TINYINT UNSIGNED NOT NULL,
  gate_strength ENUM('hard','soft','log_only') NOT NULL DEFAULT 'hard',
  signoff_role ENUM('CM','RM') NOT NULL DEFAULT 'CM',
  -- request payload (json snapshot for audit)
  request_payload_json JSON NULL,
  proposal_doc_url VARCHAR(500) NULL,
  proposal_cohort_count INT UNSIGNED NULL,
  proposal_budget_rs DECIMAL(12,2) NULL,
  proposal_decision_date DATE NULL,
  r2b_status ENUM('shared','accepted_with_changes','accepted','rejected','not_yet') NULL,
  expected_close_date DATE NULL,
  win_probability ENUM('low','medium','high') NULL,
  contract_value_rs DECIMAL(12,2) NULL,
  work_order_target_date DATE NULL,
  payment_plan_json JSON NULL,
  -- decision
  status ENUM('pending','approved','rejected','request_edit','bypassed','expired') NOT NULL DEFAULT 'pending',
  decision_reason_code VARCHAR(64) NULL,
  decision_note TEXT NULL,
  coaching_note TEXT NULL,
  -- timing
  requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sla_hours TINYINT UNSIGNED NOT NULL DEFAULT 24,
  sla_breach_at DATETIME GENERATED ALWAYS AS (DATE_ADD(requested_at, INTERVAL sla_hours HOUR)) STORED,
  alarm_4h_sent_at DATETIME NULL,
  alarm_24h_sent_at DATETIME NULL,
  auto_escalated_at DATETIME NULL,
  decided_at DATETIME NULL,
  decided_by_uid INT UNSIGNED NULL,
  -- bypass
  bypassed_by_rm_uid INT UNSIGNED NULL,
  bypass_reason TEXT NULL,
  bypassed_at DATETIME NULL,
  -- bookkeeping
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_lss_lead (init_call_id, gate_code, status),
  INDEX idx_lss_bd (bd_uid, status, requested_at),
  INDEX idx_lss_cm_queue (cm_uid, status, requested_at),
  INDEX idx_lss_breach (sla_breach_at, status),
  INDEX idx_lss_decided (decided_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Table 2. line_manager_scorecard_daily
-- Per CM or RM, per day, the 7 KPI columns + day_score + grade.
-- Refreshed by the daily cron, never written by app code.
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS line_manager_scorecard_daily (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  manager_uid INT UNSIGNED NOT NULL,
  manager_role ENUM('CM','ACM','RM','SH') NOT NULL,
  cluster_id INT UNSIGNED NULL,
  score_date DATE NOT NULL,
  -- K1 MoM SLA
  moms_decided_total INT UNSIGNED NOT NULL DEFAULT 0,
  moms_decided_by_1900 INT UNSIGNED NOT NULL DEFAULT 0,
  mom_sla_pct DECIMAL(5,2) NULL,
  mom_sla_breaches INT UNSIGNED NOT NULL DEFAULT 0,
  -- K2 + K6 coaching
  cd_moms_total INT UNSIGNED NOT NULL DEFAULT 0,
  cd_moms_with_coaching_note INT UNSIGNED NOT NULL DEFAULT 0,
  cd_moms_approved_no_note INT UNSIGNED NOT NULL DEFAULT 0,
  coaching_ratio_pct DECIMAL(5,2) NULL,
  moms_sent_back_with_note INT UNSIGNED NOT NULL DEFAULT 0,
  -- K3 signoff turnaround
  signoffs_decided INT UNSIGNED NOT NULL DEFAULT 0,
  signoffs_over_48h INT UNSIGNED NOT NULL DEFAULT 0,
  signoff_avg_hours DECIMAL(6,2) NULL,
  -- K4 R2B follow-through
  cstatus6_leads_in_cluster INT UNSIGNED NOT NULL DEFAULT 0,
  cstatus6_r2b_shared_within_7d INT UNSIGNED NOT NULL DEFAULT 0,
  r2b_follow_through_pct DECIMAL(5,2) NULL,
  cstatus6_stuck_over_7d INT UNSIGNED NOT NULL DEFAULT 0,
  -- K5 stuck closure ratio
  cstatus9_leads_in_cluster INT UNSIGNED NOT NULL DEFAULT 0,
  cstatus9_over_14d_no_date INT UNSIGNED NOT NULL DEFAULT 0,
  stuck_closure_pct DECIMAL(5,2) NULL,
  -- K7 escalation pre-SLA rate
  escalations_resolved_or_up INT UNSIGNED NOT NULL DEFAULT 0,
  escalations_resolved_pre_sla INT UNSIGNED NOT NULL DEFAULT 0,
  escalations_post_breach INT UNSIGNED NOT NULL DEFAULT 0,
  pre_sla_pct DECIMAL(5,2) NULL,
  -- RM-only bypass count
  bypasses_today INT UNSIGNED NOT NULL DEFAULT 0,
  -- scoring
  day_score INT NOT NULL DEFAULT 100,
  computed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_mgr_date (manager_uid, score_date),
  INDEX idx_lmsd_date_score (score_date, day_score),
  INDEX idx_lmsd_cluster (cluster_id, score_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Table 3. escalation_ticket
-- Replaces v1 intervention_cm_pst_sh single-dropdown.
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS escalation_ticket (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  init_call_id INT UNSIGNED NOT NULL,
  mom_id INT UNSIGNED NULL,
  opened_by_uid INT UNSIGNED NOT NULL,
  reason_code ENUM(
    'budget_negotiation',
    'competitor_pressure',
    'decision_maker_change',
    'price_objection',
    'vendor_onboarding_block',
    'pst_sanction_needed',
    'legal_or_compliance',
    'stuck_no_response'
  ) NOT NULL,
  reason_note TEXT NULL,
  current_handler_uid INT UNSIGNED NOT NULL,
  current_handler_role ENUM('CM','RM','SH','PST') NOT NULL,
  handover_chain_json JSON NULL,
  sla_hours TINYINT UNSIGNED NOT NULL,
  opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  breach_at DATETIME GENERATED ALWAYS AS (DATE_ADD(opened_at, INTERVAL sla_hours HOUR)) STORED,
  resolved_at DATETIME NULL,
  resolution_note TEXT NULL,
  status ENUM('open','in_progress','resolved','escalated_up','breached') NOT NULL DEFAULT 'open',
  pre_sla_resolved TINYINT(1) GENERATED ALWAYS AS (
    CASE WHEN status IN ('resolved','escalated_up') AND resolved_at IS NOT NULL AND resolved_at <= breach_at THEN 1 ELSE 0 END
  ) STORED,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_esc_lead (init_call_id, status),
  INDEX idx_esc_handler (current_handler_uid, status, breach_at),
  INDEX idx_esc_breach (breach_at, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed the default SLA hours per reason_code (lookup by app)
CREATE TABLE IF NOT EXISTS escalation_reason_sla (
  reason_code VARCHAR(64) NOT NULL PRIMARY KEY,
  default_handler_role ENUM('CM','RM','SH','PST') NOT NULL,
  default_sla_hours TINYINT UNSIGNED NOT NULL,
  description VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO escalation_reason_sla (reason_code, default_handler_role, default_sla_hours, description) VALUES
  ('budget_negotiation',       'CM',  24, 'Client pushed on budget, CM to renegotiate'),
  ('competitor_pressure',      'RM',  24, 'Named competitor in play, RM strategy needed'),
  ('decision_maker_change',    'CM',  12, 'DM left or changed, fast rebuild needed'),
  ('price_objection',          'CM',  24, 'Price too high, CM first, RM if not resolved'),
  ('vendor_onboarding_block',  'SH',  48, 'Vendor form or compliance blocking closure'),
  ('pst_sanction_needed',      'PST', 48, 'Sanction limit needs PST approval'),
  ('legal_or_compliance',      'SH',  72, 'Legal or compliance review needed'),
  ('stuck_no_response',        'CM',  24, 'No DM response over 7 days')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- ---------------------------------------------------------------------
-- Table 4. signoff_bypass_log
-- Every RM bypass logged. Triggers email to RM + stemlearning@gmail.com.
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS signoff_bypass_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  signoff_id BIGINT UNSIGNED NOT NULL,
  init_call_id INT UNSIGNED NOT NULL,
  bd_uid INT UNSIGNED NOT NULL,
  cm_uid INT UNSIGNED NULL,
  rm_uid INT UNSIGNED NOT NULL,
  gate_code ENUM('G1','G2','G3','G4') NOT NULL,
  bypass_reason TEXT NOT NULL,
  email_sent_at DATETIME NULL,
  email_recipients VARCHAR(500) NULL,
  bypassed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  week_iso VARCHAR(10) GENERATED ALWAYS AS (DATE_FORMAT(bypassed_at, '%x-W%v')) STORED,
  INDEX idx_sbl_rm_week (rm_uid, week_iso),
  INDEX idx_sbl_bypassed_at (bypassed_at),
  CONSTRAINT fk_sbl_signoff FOREIGN KEY (signoff_id) REFERENCES lead_stage_signoff(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Table 5. manager_incentive_ledger
-- Weekly Rs payout per manager. Live from Mon 25 May 2026 pilot week.
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS manager_incentive_ledger (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  manager_uid INT UNSIGNED NOT NULL,
  manager_role ENUM('CM','ACM','RM','SH') NOT NULL,
  week_from DATE NOT NULL,
  week_to DATE NOT NULL,
  weekly_avg_score DECIMAL(5,2) NOT NULL,
  weekly_grade ENUM('A+','A','B','C','D') NOT NULL,
  payout_rs DECIMAL(10,2) NOT NULL DEFAULT 0,
  deduction_rs DECIMAL(10,2) NOT NULL DEFAULT 0,
  net_rs DECIMAL(10,2) GENERATED ALWAYS AS (payout_rs - deduction_rs) STORED,
  payout_status ENUM('pending','approved','paid','disputed') NOT NULL DEFAULT 'pending',
  approved_by_uid INT UNSIGNED NULL,
  approved_at DATETIME NULL,
  paid_at DATETIME NULL,
  payroll_reference VARCHAR(100) NULL,
  computed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_mgr_week (manager_uid, week_from),
  INDEX idx_mil_week (week_from, weekly_grade),
  INDEX idx_mil_status (payout_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- New columns on existing tables
-- ---------------------------------------------------------------------

-- init_call gets r2b_status snapshot, R2B shared date, expected close date
ALTER TABLE init_call
  ADD COLUMN IF NOT EXISTS r2b_status ENUM('shared','accepted_with_changes','accepted','rejected','not_yet') NULL AFTER dm_org_type,
  ADD COLUMN IF NOT EXISTS r2b_shared_at DATETIME NULL AFTER r2b_status,
  ADD COLUMN IF NOT EXISTS expected_close_date DATE NULL AFTER r2b_shared_at,
  ADD COLUMN IF NOT EXISTS next_decision_date DATE NULL AFTER expected_close_date,
  ADD COLUMN IF NOT EXISTS current_signoff_id BIGINT UNSIGNED NULL AFTER next_decision_date;

ALTER TABLE init_call
  ADD INDEX IF NOT EXISTS idx_ic_r2b (r2b_status, r2b_shared_at),
  ADD INDEX IF NOT EXISTS idx_ic_expected_close (expected_close_date),
  ADD INDEX IF NOT EXISTS idx_ic_next_decision (next_decision_date);

-- mom_data gets coaching note flag, expected_close link
ALTER TABLE mom_data
  ADD COLUMN IF NOT EXISTS cm_coaching_note TEXT NULL AFTER rejection_reason,
  ADD COLUMN IF NOT EXISTS cm_coaching_note_added_at DATETIME NULL AFTER cm_coaching_note,
  ADD COLUMN IF NOT EXISTS expected_close_date_snapshot DATE NULL AFTER cm_coaching_note_added_at,
  ADD COLUMN IF NOT EXISTS win_probability_snapshot ENUM('low','medium','high') NULL AFTER expected_close_date_snapshot;

-- ---------------------------------------------------------------------
-- Views
-- ---------------------------------------------------------------------

-- v_signoff_pending_summary - what each CM sees in queue
CREATE OR REPLACE VIEW v_signoff_pending_summary AS
SELECT
  s.id              AS signoff_id,
  s.init_call_id,
  s.bd_uid,
  bd.fname          AS bd_name,
  s.cm_uid,
  cm.fname          AS cm_name,
  s.gate_code,
  s.from_cstatus,
  s.to_cstatus,
  s.gate_strength,
  s.status,
  s.requested_at,
  s.sla_breach_at,
  TIMESTAMPDIFF(HOUR, s.requested_at, NOW())    AS age_hours,
  TIMESTAMPDIFF(HOUR, NOW(), s.sla_breach_at)   AS hours_to_breach,
  CASE WHEN NOW() > s.sla_breach_at THEN 1 ELSE 0 END AS sla_breached,
  ic.compny_nm     AS school_name,
  ic.compny_loction AS school_location,
  s.r2b_status,
  s.expected_close_date,
  s.contract_value_rs
FROM lead_stage_signoff s
LEFT JOIN user bd ON bd.uid = s.bd_uid
LEFT JOIN user cm ON cm.uid = s.cm_uid
LEFT JOIN init_call ic ON ic.id = s.init_call_id
WHERE s.status = 'pending';

-- v_signoff_breached_today - what cron 0c647bbd reads
CREATE OR REPLACE VIEW v_signoff_breached_today AS
SELECT
  s.id              AS signoff_id,
  s.gate_code,
  s.bd_uid,
  bd.fname          AS bd_name,
  s.cm_uid,
  cm.fname          AS cm_name,
  ic.compny_nm     AS school_name,
  TIMESTAMPDIFF(HOUR, s.requested_at, NOW()) AS age_hours,
  s.requested_at
FROM lead_stage_signoff s
LEFT JOIN user bd ON bd.uid = s.bd_uid
LEFT JOIN user cm ON cm.uid = s.cm_uid
LEFT JOIN init_call ic ON ic.id = s.init_call_id
WHERE s.status = 'pending'
  AND TIMESTAMPDIFF(HOUR, s.requested_at, NOW()) >= 48;

-- v_bypass_log_this_week - the abuse detector
CREATE OR REPLACE VIEW v_bypass_log_this_week AS
SELECT
  b.rm_uid,
  rm.fname          AS rm_name,
  COUNT(*)          AS bypass_count_this_week,
  GROUP_CONCAT(b.gate_code ORDER BY b.bypassed_at SEPARATOR ',') AS gates,
  GROUP_CONCAT(b.init_call_id ORDER BY b.bypassed_at SEPARATOR ',') AS lead_ids,
  MIN(b.bypassed_at) AS first_bypass_at,
  MAX(b.bypassed_at) AS last_bypass_at
FROM signoff_bypass_log b
LEFT JOIN user rm ON rm.uid = b.rm_uid
WHERE b.bypassed_at >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
GROUP BY b.rm_uid, rm.fname
HAVING bypass_count_this_week >= 3;

-- v_line_manager_scorecard_weekly - what the cron + dashboard read
CREATE OR REPLACE VIEW v_line_manager_scorecard_weekly AS
SELECT
  manager_uid,
  manager_role,
  cluster_id,
  YEARWEEK(score_date, 3) AS year_week,
  MIN(score_date)         AS week_from,
  MAX(score_date)         AS week_to,
  COUNT(*)                AS days_recorded,
  ROUND(AVG(day_score), 2) AS weekly_avg_score,
  CASE
    WHEN AVG(day_score) >= 90 THEN 'A+'
    WHEN AVG(day_score) >= 75 THEN 'A'
    WHEN AVG(day_score) >= 60 THEN 'B'
    WHEN AVG(day_score) >= 40 THEN 'C'
    ELSE 'D'
  END AS weekly_grade,
  SUM(mom_sla_breaches)         AS mom_sla_breaches_week,
  SUM(signoffs_over_48h)        AS signoffs_over_48h_week,
  ROUND(AVG(coaching_ratio_pct), 2) AS coaching_ratio_avg,
  ROUND(AVG(r2b_follow_through_pct), 2) AS r2b_follow_avg,
  ROUND(AVG(pre_sla_pct), 2)    AS pre_sla_avg,
  SUM(bypasses_today)           AS bypasses_week
FROM line_manager_scorecard_daily
WHERE score_date >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
GROUP BY manager_uid, manager_role, cluster_id, YEARWEEK(score_date, 3);

COMMIT;

-- =====================================================================
-- Smoke test (run manually after migration)
-- =====================================================================
-- SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema=DATABASE()
--   AND table_name IN ('lead_stage_signoff','line_manager_scorecard_daily',
--                      'escalation_ticket','escalation_reason_sla',
--                      'signoff_bypass_log','manager_incentive_ledger');
-- -- expect 6
--
-- SELECT COUNT(*) FROM escalation_reason_sla;
-- -- expect 8
--
-- SELECT COUNT(*) FROM information_schema.VIEWS WHERE table_schema=DATABASE()
--   AND table_name IN ('v_signoff_pending_summary','v_signoff_breached_today',
--                      'v_bypass_log_this_week','v_line_manager_scorecard_weekly');
-- -- expect 4
--
-- INSERT INTO lead_stage_signoff (init_call_id, bd_uid, cm_uid, gate_code, from_cstatus, to_cstatus)
--   VALUES (1, 42, 12, 'G1', 6, 7);
-- SELECT signoff_id, age_hours, sla_breached FROM v_signoff_pending_summary WHERE signoff_id = LAST_INSERT_ID();
-- DELETE FROM lead_stage_signoff WHERE id = LAST_INSERT_ID();
