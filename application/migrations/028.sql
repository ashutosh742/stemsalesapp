-- ============================================================================
-- STEM CRM - Migration 028
-- Upstream Hygiene + Proposal Backlog Sweep
-- ============================================================================
-- Scope: cstatus 1 (Open, 10,338 leads) and cstatus 2 (Reachout, 1,594 leads)
-- Migration 024 explicitly excluded these stages. Migration 028 closes the gap.
--
-- Depends on:
--   funnel_change_log      (migration 024)
--   proposal_sla_tracker   (migration 026)
--   reporting_hierarchy    (migration 023.3)
--   quarter_config         (migration 024)
--
-- Author: STEM ops
-- Date: 2026-05-17
-- Staging only: stemapp.in
-- Mumbai pilot: 25 May 2026 (uids 42, 43, 44, 45, 46, 12)
-- Org rollout: 1 Jun 2026
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. UPSTREAM HYGIENE STATE
-- One live row per CID currently in cstatus 1 or 2.
-- Upserted nightly by upstream_hygiene_agent.php.
-- Cleared by trigger when lead leaves cstatus 1/2.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS upstream_hygiene_state (
  cid_id                  INT UNSIGNED NOT NULL COMMENT 'init_call.id, PK',
  bd_uid                  INT UNSIGNED NOT NULL COMMENT 'mainbd user',
  cm_uid                  INT UNSIGNED DEFAULT NULL COMMENT 'cluster manager uid',
  cstatus                 TINYINT UNSIGNED NOT NULL COMMENT '1 or 2',
  days_stagnant           SMALLINT NOT NULL DEFAULT 0
                            COMMENT 'days since last qualifying touch; 0 if touched today',
  last_touch_at           DATE DEFAULT NULL COMMENT 'date of last qualifying touch event',
  last_touch_actiontype   INT UNSIGNED DEFAULT NULL
                            COMMENT 'actiontype_id of the most recent qualifying touch',
  near_miss_flag          TINYINT(1) NOT NULL DEFAULT 0
                            COMMENT '1 when days_stagnant >= 21 (cs1) or >= 14 (cs2)',
  stagnant_flag           TINYINT(1) NOT NULL DEFAULT 0
                            COMMENT '1 when days_stagnant >= 45 (cs1) or >= 30 (cs2)',
  hard_flag               TINYINT(1) NOT NULL DEFAULT 0
                            COMMENT '1 after auto-Lost transition has fired',
  wallet_debited          TINYINT(1) NOT NULL DEFAULT 0
                            COMMENT '1 after Rs 200 wallet debit has fired; prevents double debit',
  created_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (cid_id),
  KEY idx_bd_stagnant (bd_uid, days_stagnant),
  KEY idx_cm_stagnant (cm_uid, days_stagnant),
  KEY idx_cstatus_stagnant (cstatus, days_stagnant),
  KEY idx_flags (stagnant_flag, hard_flag, wallet_debited)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 2. UPSTREAM HYGIENE LOG
-- Full audit trail: near_miss, stagnant, hard_auto_lost, wallet_debit, manager_email.
-- Append-only. Never updated.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS upstream_hygiene_log (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id            INT UNSIGNED NOT NULL COMMENT 'init_call.id',
  event_type        ENUM('near_miss','stagnant','hard_auto_lost',
                         'wallet_debit','manager_email') NOT NULL,
  event_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  days_at_event     SMALLINT NOT NULL DEFAULT 0
                      COMMENT 'days_stagnant captured at the moment the event fired',
  rs_amount         DECIMAL(8,2) NOT NULL DEFAULT 0
                      COMMENT 'nonzero for wallet_debit events only',
  notes             TEXT DEFAULT NULL COMMENT 'free-text context, reason, override note',
  PRIMARY KEY (id),
  KEY idx_cid_event (cid_id, event_type, event_at),
  KEY idx_event_date (event_type, event_at),
  KEY idx_wallet (event_type, rs_amount)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 3. PROPOSAL BACKLOG LEGACY
-- Holds 3,014 leads that reached approved MoM but have no proposal filed,
-- predating migration 026. Seeded from v_approved_mom_no_proposal (ops-provided).
-- After 14-day grace window, standard 026 SLA penalties apply.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS proposal_backlog_legacy (
  id                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cid_id                  INT UNSIGNED NOT NULL COMMENT 'init_call.id',
  mom_approved_at         DATETIME NOT NULL COMMENT 'datetime of the approved MoM event',
  days_since_mom_approved SMALLINT NOT NULL DEFAULT 0
                            COMMENT 'computed at insert time: DATEDIFF(NOW(), mom_approved_at)',
  grace_window_ends_at    DATETIME NOT NULL
                            COMMENT '14 days from when migration 028 seed INSERT ran',
  status                  ENUM('legacy_grace','legacy_overdue','filed','closed_lost')
                            NOT NULL DEFAULT 'legacy_grace',
  created_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cid (cid_id),
  KEY idx_status_grace (status, grace_window_ends_at),
  KEY idx_cid_status (cid_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 4. UPSTREAM HYGIENE BLOCK LOG
-- Tracks BD planner-creation blocks from stagnancy thresholds.
-- Mirrors bd_planner_block_log pattern from migration 026.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS upstream_hygiene_block_log (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  bd_uid        INT UNSIGNED NOT NULL,
  blocked_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reason        ENUM('stagnant_45_over_10','stagnant_30_over_5') NOT NULL
                  COMMENT 'stagnant_45_over_10 = 10+ cs1 stagnant rows; stagnant_30_over_5 = 5+ cs2 stagnant rows',
  unblocked_at  DATETIME DEFAULT NULL COMMENT 'set when BD clears below threshold',
  PRIMARY KEY (id),
  KEY idx_bd_active (bd_uid, unblocked_at),
  KEY idx_reason (reason, blocked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 5. VIEWS
-- ----------------------------------------------------------------------------

-- Today's snapshot of cstatus 1 leads at or over 45 days stagnant.
CREATE OR REPLACE VIEW v_stagnant_open_45 AS
SELECT
  s.cid_id,
  ic.compny_nm       AS school_name,
  s.bd_uid,
  ub.firstName       AS bd_name,
  s.cm_uid,
  uc.firstName       AS cm_name,
  s.cstatus,
  s.days_stagnant,
  s.last_touch_at,
  s.last_touch_actiontype,
  s.near_miss_flag,
  s.stagnant_flag,
  s.hard_flag,
  s.wallet_debited,
  s.updated_at
FROM upstream_hygiene_state s
LEFT JOIN init_call ic ON ic.id = s.cid_id
LEFT JOIN user ub      ON ub.uid = s.bd_uid
LEFT JOIN user uc      ON uc.uid = s.cm_uid
WHERE s.cstatus = 1
  AND s.days_stagnant >= 45;

-- Today's snapshot of cstatus 2 leads at or over 30 days stagnant.
CREATE OR REPLACE VIEW v_stagnant_reachout_30 AS
SELECT
  s.cid_id,
  ic.compny_nm       AS school_name,
  s.bd_uid,
  ub.firstName       AS bd_name,
  s.cm_uid,
  uc.firstName       AS cm_name,
  s.cstatus,
  s.days_stagnant,
  s.last_touch_at,
  s.last_touch_actiontype,
  s.near_miss_flag,
  s.stagnant_flag,
  s.hard_flag,
  s.wallet_debited,
  s.updated_at
FROM upstream_hygiene_state s
LEFT JOIN init_call ic ON ic.id = s.cid_id
LEFT JOIN user ub      ON ub.uid = s.bd_uid
LEFT JOIN user uc      ON uc.uid = s.cm_uid
WHERE s.cstatus = 2
  AND s.days_stagnant >= 30;

-- Legacy proposal backlog rows past their grace window.
CREATE OR REPLACE VIEW v_proposal_backlog_overdue AS
SELECT
  p.id,
  p.cid_id,
  ic.compny_nm         AS school_name,
  ic.mainbd            AS bd_uid,
  ub.firstName         AS bd_name,
  (SELECT parent_uid
     FROM reporting_hierarchy
    WHERE employee_uid = ic.mainbd
      AND active = 1
    LIMIT 1)           AS cm_uid,
  p.mom_approved_at,
  p.days_since_mom_approved,
  p.grace_window_ends_at,
  p.status,
  p.updated_at,
  DATEDIFF(CURDATE(), p.grace_window_ends_at) AS days_past_grace
FROM proposal_backlog_legacy p
LEFT JOIN init_call ic ON ic.id = p.cid_id
LEFT JOIN user ub      ON ub.uid = ic.mainbd
WHERE p.status = 'legacy_overdue';

-- ----------------------------------------------------------------------------
-- 6. TRIGGER: after_lead_progression_update
-- Fires AFTER UPDATE on init_call when cstatus moves.
-- If new cstatus is 1 or 2: upsert upstream_hygiene_state.
-- If old cstatus was 1 or 2 and new is not: remove the state row and log exit.
-- ----------------------------------------------------------------------------
DELIMITER $$

DROP TRIGGER IF EXISTS trg_after_lead_progression_hygiene_028$$

CREATE TRIGGER trg_after_lead_progression_hygiene_028
AFTER UPDATE ON init_call
FOR EACH ROW
BEGIN
  DECLARE v_cm_uid INT UNSIGNED DEFAULT NULL;

  -- Only react when cstatus actually changed.
  IF OLD.cstatus IS NOT NULL
     AND NEW.cstatus IS NOT NULL
     AND OLD.cstatus <> NEW.cstatus THEN

    -- Resolve CM for this BD.
    SELECT parent_uid INTO v_cm_uid
      FROM reporting_hierarchy
     WHERE employee_uid = NEW.mainbd
       AND active = 1
     LIMIT 1;

    -- Entering cstatus 1 or 2: create or refresh state row.
    IF NEW.cstatus IN (1, 2) THEN
      INSERT INTO upstream_hygiene_state
        (cid_id, bd_uid, cm_uid, cstatus, days_stagnant,
         last_touch_at, last_touch_actiontype,
         near_miss_flag, stagnant_flag, hard_flag, wallet_debited)
      VALUES
        (NEW.id, NEW.mainbd, v_cm_uid, NEW.cstatus, 0,
         NULL, NULL, 0, 0, 0, 0)
      ON DUPLICATE KEY UPDATE
        bd_uid                = NEW.mainbd,
        cm_uid                = v_cm_uid,
        cstatus               = NEW.cstatus,
        days_stagnant         = 0,
        last_touch_at         = NULL,
        last_touch_actiontype = NULL,
        near_miss_flag        = 0,
        stagnant_flag         = 0,
        hard_flag             = 0,
        wallet_debited        = 0;
    END IF;

    -- Leaving cstatus 1 or 2: remove state row and log exit.
    IF OLD.cstatus IN (1, 2) AND NEW.cstatus NOT IN (1, 2) THEN
      INSERT INTO upstream_hygiene_log
        (cid_id, event_type, days_at_event, rs_amount, notes)
      SELECT
        OLD.id, 'hard_auto_lost',
        COALESCE(uhs.days_stagnant, 0),
        0,
        CONCAT('Lead exited cstatus ', OLD.cstatus,
               ' -> ', NEW.cstatus,
               ' via trigger trg_after_lead_progression_hygiene_028')
      FROM (SELECT 1) dummy
      LEFT JOIN upstream_hygiene_state uhs ON uhs.cid_id = OLD.id;

      DELETE FROM upstream_hygiene_state WHERE cid_id = OLD.id;
    END IF;

  END IF;
END$$

DELIMITER ;

-- ----------------------------------------------------------------------------
-- 7. KPI WEIGHT COLUMNS: add K9 and K10, rebalance K8
-- ----------------------------------------------------------------------------

-- Add K9 and K10 weight columns to quarter_config (idempotent).
ALTER TABLE quarter_config
  ADD COLUMN IF NOT EXISTS k9_weight TINYINT UNSIGNED NOT NULL DEFAULT 5
    COMMENT 'Open Hygiene (cs1) KPI weight',
  ADD COLUMN IF NOT EXISTS k10_weight TINYINT UNSIGNED NOT NULL DEFAULT 5
    COMMENT 'Reachout Hygiene (cs2) KPI weight';

-- Rebalance: K8 from 15 to 10, K9 = 5, K10 = 5 for FY27+.
UPDATE quarter_config
   SET k8_weight  = 10,
       k9_weight  = 5,
       k10_weight = 5
 WHERE fiscal_year >= 27;

-- Add K9 and K10 measurement columns to line_manager_scorecard.
ALTER TABLE line_manager_scorecard
  ADD COLUMN IF NOT EXISTS k9_open_hygiene_pct    DECIMAL(5,2) DEFAULT NULL
    COMMENT 'percent of cs1 CIDs under manager without stagnant_45 row this week'
    AFTER k8_phantom_count,
  ADD COLUMN IF NOT EXISTS k9_stagnant_45_count   INT UNSIGNED DEFAULT 0
    AFTER k9_open_hygiene_pct,
  ADD COLUMN IF NOT EXISTS k10_reachout_hygiene_pct DECIMAL(5,2) DEFAULT NULL
    COMMENT 'percent of cs2 CIDs under manager without stagnant_30 row this week'
    AFTER k9_stagnant_45_count,
  ADD COLUMN IF NOT EXISTS k10_stagnant_30_count  INT UNSIGNED DEFAULT 0
    AFTER k10_reachout_hygiene_pct;

-- ----------------------------------------------------------------------------
-- 8. SEED: bulk-populate proposal_backlog_legacy from v_approved_mom_no_proposal
-- The ops team provides v_approved_mom_no_proposal before running this step.
-- v_approved_mom_no_proposal must expose: cid_id, mom_approved_at.
-- The grace window is 14 days from NOW() (the moment this INSERT runs).
-- Run once only. IGNORE prevents re-insertion on retry.
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO proposal_backlog_legacy
  (cid_id, mom_approved_at, days_since_mom_approved, grace_window_ends_at, status)
SELECT
  v.cid_id,
  v.mom_approved_at,
  DATEDIFF(NOW(), v.mom_approved_at),
  DATE_ADD(NOW(), INTERVAL 14 DAY),
  'legacy_grace'
FROM v_approved_mom_no_proposal v
-- Exclude any CID already in proposal_sla_tracker (already handled by 026).
WHERE NOT EXISTS (
  SELECT 1 FROM proposal_sla_tracker pst WHERE pst.cid_id = v.cid_id
);

-- ============================================================================
-- END OF MIGRATION 028
-- ============================================================================
