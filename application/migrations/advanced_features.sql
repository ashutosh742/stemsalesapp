-- Migration 019: Advanced Feature Pack (Rev 11)
-- Author: Perplexity Computer for STEM Learning
-- Date: 2026-05-16
-- Staging only (stemapp.in), do not run on production until 18 May GitHub access lands.
-- Adds 8 advanced surfaces missing from production: AI lead scoring, geofence check-in, win-probability,
-- voice-of-customer (VoC) sentiment, churn radar, commission ledger, attendance auto-derive, knowledge nudges.

-- ============================================================================
-- 019.1 AI LEAD SCORING (replaces gut-feel category chips with model output)
-- ============================================================================
CREATE TABLE IF NOT EXISTS ai_lead_score (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id BIGINT UNSIGNED NOT NULL COMMENT 'init_call.cid',
  bd_uid INT UNSIGNED NOT NULL,
  score_run_date DATE NOT NULL,
  win_probability DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '0.00 to 100.00 percent',
  predicted_close_value_rs DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  predicted_close_date DATE NULL,
  confidence_band ENUM('high','medium','low') NOT NULL DEFAULT 'low',
  top_positive_signal VARCHAR(200) NULL COMMENT 'e.g. 3 meetings + MoM in 14d',
  top_negative_signal VARCHAR(200) NULL COMMENT 'e.g. no contact 18 days',
  next_best_action VARCHAR(255) NULL COMMENT 'human-readable nudge for BD',
  model_version VARCHAR(20) NOT NULL DEFAULT 'v1.0',
  features_json TEXT NULL COMMENT 'JSON of input features used',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cid_run (cid_id, score_run_date),
  KEY idx_bd_run (bd_uid, score_run_date),
  KEY idx_winprob (win_probability)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- View: top 10 hottest leads per BD today (drives mobile "Hot leads" tile)
CREATE OR REPLACE VIEW v_hot_leads_today AS
SELECT s.bd_uid, u.full_name AS bd_name, s.cid_id, ic.compnay AS school,
       s.win_probability, s.predicted_close_value_rs, s.next_best_action,
       s.top_positive_signal, s.confidence_band
FROM ai_lead_score s
JOIN init_call ic ON ic.cid = s.cid_id
JOIN user u ON u.user_id = s.bd_uid
WHERE s.score_run_date = CURDATE()
  AND s.win_probability >= 60
  AND ic.cstatus NOT IN (12, 13, 14)
ORDER BY s.bd_uid, s.win_probability DESC;

-- ============================================================================
-- 019.2 GEOFENCE CHECK-IN (proves BD actually visited the school)
-- ============================================================================
CREATE TABLE IF NOT EXISTS school_geofence (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id BIGINT UNSIGNED NOT NULL,
  lat DECIMAL(10,7) NOT NULL,
  lng DECIMAL(10,7) NOT NULL,
  radius_meters SMALLINT UNSIGNED NOT NULL DEFAULT 150,
  verified_by INT UNSIGNED NULL COMMENT 'CM or Admin who verified',
  verified_at DATETIME NULL,
  source ENUM('bd_first_visit','google_places','admin_manual') DEFAULT 'bd_first_visit',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cid (cid_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS visit_checkin_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_id BIGINT UNSIGNED NOT NULL COMMENT 'tblcallevents.id',
  cid_id BIGINT UNSIGNED NOT NULL,
  bd_uid INT UNSIGNED NOT NULL,
  checkin_at DATETIME NOT NULL,
  checkin_lat DECIMAL(10,7) NOT NULL,
  checkin_lng DECIMAL(10,7) NOT NULL,
  distance_from_school_m INT UNSIGNED NOT NULL,
  within_geofence TINYINT(1) NOT NULL DEFAULT 0,
  checkout_at DATETIME NULL,
  duration_minutes INT UNSIGNED NULL,
  override_reason VARCHAR(255) NULL COMMENT 'BD reason if checked in outside fence',
  override_approved_by INT UNSIGNED NULL COMMENT 'CM uid who approved override',
  PRIMARY KEY (id),
  KEY idx_event (event_id),
  KEY idx_bd_day (bd_uid, checkin_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- View: meetings logged today without a check-in (fraud signal)
CREATE OR REPLACE VIEW v_meetings_without_checkin AS
SELECT ce.id AS event_id, ce.cid_id, ce.user_id AS bd_uid,
       u.full_name AS bd_name, ic.compnay AS school, ce.event_date, ce.actiontype_id
FROM tblcallevents ce
JOIN user u ON u.user_id = ce.user_id
JOIN init_call ic ON ic.cid = ce.cid_id
LEFT JOIN visit_checkin_log v ON v.event_id = ce.id
WHERE ce.actiontype_id IN (3, 4)
  AND ce.event_date = CURDATE()
  AND v.id IS NULL;

-- ============================================================================
-- 019.3 VOICE-OF-CUSTOMER SENTIMENT (MoM and call notes)
-- ============================================================================
CREATE TABLE IF NOT EXISTS mom_sentiment (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mom_id BIGINT UNSIGNED NOT NULL COMMENT 'mom_data.id',
  cid_id BIGINT UNSIGNED NOT NULL,
  sentiment_score DECIMAL(4,2) NOT NULL DEFAULT 0.00 COMMENT '-1.00 to +1.00',
  sentiment_label ENUM('strong_negative','negative','neutral','positive','strong_positive') NOT NULL DEFAULT 'neutral',
  top_pain_keywords VARCHAR(255) NULL COMMENT 'comma-list e.g. budget,delay,principal_unavailable',
  top_intent_keywords VARCHAR(255) NULL COMMENT 'comma-list e.g. demo,proposal,pricing',
  buying_signal TINYINT(1) NOT NULL DEFAULT 0,
  blocker_signal TINYINT(1) NOT NULL DEFAULT 0,
  processed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  model_version VARCHAR(20) NOT NULL DEFAULT 'v1.0',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_mom (mom_id),
  KEY idx_cid (cid_id),
  KEY idx_buying (buying_signal),
  KEY idx_blocker (blocker_signal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 019.4 CHURN RADAR (existing-customer at-risk detection)
-- ============================================================================
CREATE TABLE IF NOT EXISTS churn_risk_score (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id BIGINT UNSIGNED NOT NULL COMMENT 'won school',
  bd_uid INT UNSIGNED NOT NULL,
  score_date DATE NOT NULL,
  churn_probability DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  risk_band ENUM('green','yellow','orange','red') NOT NULL DEFAULT 'green',
  days_since_last_touch INT UNSIGNED NOT NULL DEFAULT 0,
  pending_renewal_value_rs DECIMAL(12,2) NULL,
  top_risk_signal VARCHAR(255) NULL,
  recommended_touch VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cid_date (cid_id, score_date),
  KEY idx_band (risk_band, score_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 019.5 COMMISSION LEDGER (transparent payout per BD)
-- ============================================================================
CREATE TABLE IF NOT EXISTS commission_event (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  bd_uid INT UNSIGNED NOT NULL,
  cid_id BIGINT UNSIGNED NOT NULL,
  event_type ENUM('won_closure','renewal','upsell','referral_bonus','clawback') NOT NULL,
  amount_rs DECIMAL(12,2) NOT NULL,
  rate_percent DECIMAL(5,2) NULL COMMENT 'commission rate applied',
  base_value_rs DECIMAL(12,2) NULL COMMENT 'deal value the rate was applied to',
  earned_on DATE NOT NULL,
  payout_status ENUM('accrued','approved','paid','reversed') NOT NULL DEFAULT 'accrued',
  approved_by INT UNSIGNED NULL,
  approved_at DATETIME NULL,
  paid_in_cycle VARCHAR(10) NULL COMMENT 'e.g. 2026-05',
  PRIMARY KEY (id),
  KEY idx_bd_status (bd_uid, payout_status),
  KEY idx_cid (cid_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 019.6 ATTENDANCE AUTO-DERIVE (no separate punch-in needed)
-- ============================================================================
CREATE TABLE IF NOT EXISTS attendance_daily (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  bd_uid INT UNSIGNED NOT NULL,
  attendance_date DATE NOT NULL,
  status ENUM('present','half_day','absent','wfh','leave','holiday') NOT NULL DEFAULT 'absent',
  first_activity_at DATETIME NULL COMMENT 'first task/checkin event',
  last_activity_at DATETIME NULL,
  total_active_minutes INT UNSIGNED NOT NULL DEFAULT 0,
  first_geofence_visit_at DATETIME NULL,
  derived_from ENUM('auto','manual_override','leave_request') NOT NULL DEFAULT 'auto',
  override_by INT UNSIGNED NULL,
  override_reason VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_bd_date (bd_uid, attendance_date),
  KEY idx_date (attendance_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 019.7 KNOWLEDGE NUDGES (in-context coaching, deeper than Planner Coach)
-- ============================================================================
CREATE TABLE IF NOT EXISTS knowledge_nudge (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  trigger_event VARCHAR(80) NOT NULL COMMENT 'e.g. before_first_meeting_at_school, after_lost',
  cstatus_filter VARCHAR(40) NULL COMMENT 'comma-list of applicable cstatus',
  partner_type_filter VARCHAR(40) NULL,
  nudge_title VARCHAR(120) NOT NULL,
  nudge_body TEXT NOT NULL,
  nudge_video_url VARCHAR(255) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_trigger_active (trigger_event, active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS knowledge_nudge_delivery (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nudge_id INT UNSIGNED NOT NULL,
  bd_uid INT UNSIGNED NOT NULL,
  cid_id BIGINT UNSIGNED NULL,
  delivered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  acknowledged TINYINT(1) NOT NULL DEFAULT 0,
  helpful TINYINT(1) NULL,
  PRIMARY KEY (id),
  KEY idx_nudge (nudge_id),
  KEY idx_bd (bd_uid, delivered_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 019.8 ROUTE OPTIMIZATION (daily optimal field route from planned visits)
-- ============================================================================
CREATE TABLE IF NOT EXISTS route_plan (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  bd_uid INT UNSIGNED NOT NULL,
  plan_date DATE NOT NULL,
  optimized_order TEXT NOT NULL COMMENT 'JSON array of {event_id, seq, eta, distance_m}',
  total_distance_km DECIMAL(7,2) NOT NULL DEFAULT 0,
  total_travel_minutes INT UNSIGNED NOT NULL DEFAULT 0,
  savings_vs_naive_km DECIMAL(7,2) NOT NULL DEFAULT 0,
  optimizer_version VARCHAR(20) NOT NULL DEFAULT 'v1.0',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_bd_date (bd_uid, plan_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DONE 019_advanced_feature_pack
