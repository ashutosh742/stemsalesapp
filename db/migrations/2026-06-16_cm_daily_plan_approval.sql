-- =====================================================================
-- CM approval-state migration  (2026-06-16)
-- Target: STAGING database selfstaging_salescrm  (NEVER production)
-- Policy: ADDITIVE only. No DROP. Safe to re-run (idempotent guards via
--         INFORMATION_SCHEMA so a second apply is a no-op).
--
-- Root cause being fixed: the CM Approval Queue (v28/PlannerV28::v2_cm_queue)
-- reads cm_daily_plan, but the approve endpoint (v2_resolve_request) only ever
-- wrote to the unrelated bd_request table. cm_daily_plan had NO approval state
-- of its own (its status enum is planned/done/skipped/rolled), so a manager
-- approval could never persist against the row the queue actually shows.
--
-- This migration adds a real, additive approval state to cm_daily_plan:
--   approval_status  enum('pending','approved','rejected') default 'pending'
--   approved_by_uid  int           (the CM/manager uid who decided)
--   approved_at      datetime      (decision timestamp)
-- plus a helper index for the pending-queue filter.
--
-- MySQL 5.x has no "ADD COLUMN IF NOT EXISTS", so each statement is wrapped
-- in a prepared statement gated on INFORMATION_SCHEMA. Run inside the target
-- schema (selfstaging_salescrm).
-- =====================================================================

-- approval_status
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE cm_daily_plan ADD COLUMN approval_status ENUM(''pending'',''approved'',''rejected'') NOT NULL DEFAULT ''pending''',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='cm_daily_plan' AND COLUMN_NAME='approval_status');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- approved_by_uid
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE cm_daily_plan ADD COLUMN approved_by_uid INT(11) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='cm_daily_plan' AND COLUMN_NAME='approved_by_uid');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- approved_at
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE cm_daily_plan ADD COLUMN approved_at DATETIME NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='cm_daily_plan' AND COLUMN_NAME='approved_at');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- helper index for the pending-queue filter (cm_uid + plan_date + approval_status)
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE cm_daily_plan ADD KEY idx_cm_date_approval (cm_uid, plan_date, approval_status)',
  'SELECT 1') FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='cm_daily_plan' AND INDEX_NAME='idx_cm_date_approval');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- End of cm_daily_plan approval migration.
