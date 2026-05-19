-- Migration 020.1 - Monthly per-lead deep review
-- Companion to migration 020 (BD-level Review v2). This one snapshots every lead each month
-- so we can compile per-BD and per-CM PDFs without re-pulling stale data later.
-- Staging only until Mon 18 May 2026 GitHub handover.

-- 1. Main snapshot table
DROP TABLE IF EXISTS monthly_lead_review;
CREATE TABLE monthly_lead_review (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    month CHAR(7) NOT NULL COMMENT 'YYYY-MM',
    lead_id INT UNSIGNED NOT NULL COMMENT 'init_call.id',
    bd_uid INT UNSIGNED NOT NULL COMMENT 'user.uid owning BD',
    cm_uid INT UNSIGNED NULL COMMENT 'user.uid line manager CM type_id=13',
    cluster_id INT UNSIGNED NULL,
    current_cstatus TINYINT UNSIGNED NOT NULL,
    fbudget_rs DECIMAL(12,2) NOT NULL DEFAULT 0,
    days_in_stage INT NOT NULL DEFAULT 0,
    lead_age_days INT NOT NULL DEFAULT 0,
    meetings_count INT NOT NULL DEFAULT 0,
    moms_approved INT NOT NULL DEFAULT 0,
    moms_pending INT NOT NULL DEFAULT 0,
    calls_count INT NOT NULL DEFAULT 0,
    emails_count INT NOT NULL DEFAULT 0,
    cash_expense_rs DECIMAL(12,2) NOT NULL DEFAULT 0,
    cash_wallet_exposure_rs DECIMAL(12,2) NOT NULL DEFAULT 0,
    photos_count INT NOT NULL DEFAULT 0,
    gps_count INT NOT NULL DEFAULT 0,
    stage_journey JSON NULL COMMENT 'Array of {from,to,at,actor_uid}',
    activity_this_month JSON NULL COMMENT 'Array of {date,actiontype,purpose,outcome,mom_status}',
    last_mom_remark TEXT NULL,
    last_review_remark TEXT NULL,
    ai_recommendation TEXT NULL,
    next_milestone VARCHAR(255) NULL,
    open_auto_tasks_count INT NOT NULL DEFAULT 0,
    auto_flags JSON NULL COMMENT '{red_stuck,red_burn,red_silent,red_mom_gap}',
    snapshot_at DATETIME NOT NULL,
    pdf_bd_path VARCHAR(500) NULL,
    pdf_cm_path VARCHAR(500) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_month_lead (month, lead_id),
    KEY idx_month_bd (month, bd_uid),
    KEY idx_month_cm (month, cm_uid),
    KEY idx_month_cluster (month, cluster_id),
    KEY idx_month_cstatus (month, current_cstatus),
    CONSTRAINT fk_mlr_lead FOREIGN KEY (lead_id) REFERENCES init_call(id) ON DELETE CASCADE,
    CONSTRAINT fk_mlr_bd FOREIGN KEY (bd_uid) REFERENCES user(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Migration 020.1 - End-of-month snapshot per lead for in-depth review PDFs';

-- 2. Per-BD compiled-PDF manifest
DROP TABLE IF EXISTS monthly_lead_review_bd_pdf;
CREATE TABLE monthly_lead_review_bd_pdf (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    month CHAR(7) NOT NULL,
    bd_uid INT UNSIGNED NOT NULL,
    cluster_id INT UNSIGNED NULL,
    leads_count INT NOT NULL,
    leads_active INT NOT NULL,
    leads_won INT NOT NULL,
    leads_lost INT NOT NULL,
    pipeline_rs DECIMAL(14,2) NOT NULL DEFAULT 0,
    closed_won_rs DECIMAL(14,2) NOT NULL DEFAULT 0,
    pdf_path VARCHAR(500) NOT NULL,
    page_count INT NOT NULL,
    bytes_size INT NOT NULL,
    generated_at DATETIME NOT NULL,
    delivered_in_app TINYINT(1) NOT NULL DEFAULT 0,
    delivered_email TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uk_month_bd (month, bd_uid),
    KEY idx_month (month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Per-BD monthly compiled PDF manifest';

-- 3. Per-CM compiled-PDF manifest
DROP TABLE IF EXISTS monthly_lead_review_cm_pdf;
CREATE TABLE monthly_lead_review_cm_pdf (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    month CHAR(7) NOT NULL,
    cm_uid INT UNSIGNED NOT NULL,
    cluster_id INT UNSIGNED NULL,
    bd_count INT NOT NULL,
    leads_count INT NOT NULL,
    leads_active INT NOT NULL,
    leads_won INT NOT NULL,
    leads_lost INT NOT NULL,
    pipeline_rs DECIMAL(14,2) NOT NULL DEFAULT 0,
    closed_won_rs DECIMAL(14,2) NOT NULL DEFAULT 0,
    pdf_path VARCHAR(500) NOT NULL,
    page_count INT NOT NULL,
    bytes_size INT NOT NULL,
    generated_at DATETIME NOT NULL,
    delivered_in_app TINYINT(1) NOT NULL DEFAULT 0,
    delivered_email TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uk_month_cm (month, cm_uid),
    KEY idx_month (month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Per-CM cluster monthly compiled PDF manifest';

-- 4. Run-log so we can re-run safely
DROP TABLE IF EXISTS monthly_lead_review_run;
CREATE TABLE monthly_lead_review_run (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    month CHAR(7) NOT NULL,
    started_at DATETIME NOT NULL,
    completed_at DATETIME NULL,
    status ENUM('running','done','failed') NOT NULL DEFAULT 'running',
    leads_processed INT NOT NULL DEFAULT 0,
    bd_pdfs_generated INT NOT NULL DEFAULT 0,
    cm_pdfs_generated INT NOT NULL DEFAULT 0,
    duration_sec INT NULL,
    error_msg TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_month_started (month, started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Run audit for monthly lead review batch';

-- 5. Reporting view - one row per lead with denormalized BD/CM names for quick query
DROP VIEW IF EXISTS v_monthly_lead_review_dashboard;
CREATE VIEW v_monthly_lead_review_dashboard AS
SELECT
    mlr.id,
    mlr.month,
    mlr.lead_id,
    ic.compny_name AS school_name,
    ic.cstatus AS current_cstatus_now,
    mlr.current_cstatus AS cstatus_at_snapshot,
    mlr.bd_uid,
    ubd.fname AS bd_first_name,
    ubd.lname AS bd_last_name,
    ubd.cluster_id,
    mlr.cm_uid,
    ucm.fname AS cm_first_name,
    ucm.lname AS cm_last_name,
    mlr.fbudget_rs,
    mlr.days_in_stage,
    mlr.lead_age_days,
    mlr.meetings_count,
    mlr.moms_approved,
    mlr.cash_expense_rs,
    mlr.auto_flags,
    mlr.snapshot_at,
    mlr.pdf_bd_path,
    mlr.pdf_cm_path
FROM monthly_lead_review mlr
JOIN init_call ic ON ic.id = mlr.lead_id
JOIN user ubd ON ubd.uid = mlr.bd_uid
LEFT JOIN user ucm ON ucm.uid = mlr.cm_uid;

-- 6. Seed: register the new endpoint set in the API audit table (if migration 020 added it)
INSERT INTO api_endpoint_registry (endpoint, method, controller, action, added_in_migration, added_at)
VALUES
    ('/api/review/monthly/generate', 'POST', 'MonthlyLeadReviewController', 'generate', '020.1', NOW()),
    ('/api/review/monthly/manifest', 'GET',  'MonthlyLeadReviewController', 'manifest', '020.1', NOW()),
    ('/api/review/monthly/lead/{id}', 'GET', 'MonthlyLeadReviewController', 'lead_onepager', '020.1', NOW()),
    ('/api/review/monthly/bd/{uid}',  'GET', 'MonthlyLeadReviewController', 'bd_pdf', '020.1', NOW()),
    ('/api/review/monthly/cm/{uid}',  'GET', 'MonthlyLeadReviewController', 'cm_pdf', '020.1', NOW())
ON DUPLICATE KEY UPDATE added_at = NOW();

-- ROLLBACK:
-- DROP VIEW IF EXISTS v_monthly_lead_review_dashboard;
-- DROP TABLE IF EXISTS monthly_lead_review_run;
-- DROP TABLE IF EXISTS monthly_lead_review_cm_pdf;
-- DROP TABLE IF EXISTS monthly_lead_review_bd_pdf;
-- DROP TABLE IF EXISTS monthly_lead_review;
-- DELETE FROM api_endpoint_registry WHERE added_in_migration='020.1';
