-- ============================================================
-- Migration 041.2: corporate_csr_lead_link_v2
-- ============================================================
-- Purpose: accountability layer between corporate_csr_suggestion_v2
-- and the production init_call table. Replaces the direct
-- init_call/daily_planner inserts in CorporateCsrProspect_agent::accept_and_seed
-- so the frozen 6 production lead-creation paths remain the only
-- writers to init_call.
--
-- Flow:
--   1. BD taps Accept on a CSR suggestion
--   2. accept_and_seed inserts a row here with link_status='pending'
--      and returns redirect_hint to NewLeadScreen
--   3. BD completes prod creation flow (e.g. research_born)
--   4. Mobile calls /api/csr_prospect/link_init_call with the resulting
--      init_call_id; row flips to link_status='linked'
--
-- Idempotent. Safe to re-run.
-- ============================================================

CREATE TABLE IF NOT EXISTS `corporate_csr_lead_link_v2` (
  `link_id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `suggestion_id`      BIGINT UNSIGNED NOT NULL,
  `bd_uid`             INT UNSIGNED NOT NULL,
  `csr_corporate_id`   BIGINT UNSIGNED NOT NULL,
  `company_name`       VARCHAR(255) NOT NULL,
  `target_plan_date`   DATE NULL,
  `init_call_id`       BIGINT UNSIGNED NULL,
  `link_status`        ENUM('pending','linked','cancelled') NOT NULL DEFAULT 'pending',
  `created_at`         DATETIME NOT NULL,
  `linked_at`          DATETIME NULL,
  `cancelled_at`       DATETIME NULL,
  `cancel_reason`      VARCHAR(180) NULL,
  PRIMARY KEY (`link_id`),
  KEY `idx_suggestion_id` (`suggestion_id`),
  KEY `idx_bd_uid` (`bd_uid`),
  KEY `idx_init_call_id` (`init_call_id`),
  KEY `idx_link_status` (`link_status`),
  KEY `idx_target_plan_date` (`target_plan_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional FK to suggestion table (only added if parent exists)
-- Skipped here to keep this migration self-contained and idempotent
-- across environments where parent table column types may differ.
