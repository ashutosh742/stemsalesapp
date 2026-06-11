-- =====================================================================
-- v2.15.0 backend parity migration  (2026-06-11)
-- Target: STAGING database selfstaging_salescrm  (NEVER production)
-- Policy: ADDITIVE only. No DROP. Safe to re-run (idempotent guards via
--         INFORMATION_SCHEMA so a second apply is a no-op).
--
-- Independent staging inspection on 2026-06-11 found that an earlier
-- migration had ALREADY added every v2150 closure column to tblcallevents,
-- the lat/lng columns to company_master, and the title column to
-- app_reminder. The ONLY schema object still missing was the UNIQUE KEY on
-- tblcallevents.idempotency_key (the column existed but unindexed, which is
-- why submit_closure could not enforce idempotency at the DB layer).
--
-- This file therefore (a) (re)creates the unique key when absent and
-- (b) records the full intended column set with guarded ADDs so the file is
-- a complete, self-contained, re-runnable description of the v2150 schema.
-- =====================================================================

-- ---------------------------------------------------------------------
-- Helper: guarded ADD COLUMN. MySQL 5.x has no "ADD COLUMN IF NOT EXISTS",
-- so each statement is wrapped in a prepared statement gated on
-- INFORMATION_SCHEMA. Run inside the target schema.
-- ---------------------------------------------------------------------

-- ===== tblcallevents: closure detail columns (additive) ==============
-- need_identify_school
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN need_identify_school VARCHAR(16) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='need_identify_school');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN interested_school_visit VARCHAR(16) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='interested_school_visit');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN school_permission_letter VARCHAR(16) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='school_permission_letter');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN school_inauguration VARCHAR(16) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='school_inauguration');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- meeting_type already exists on tblcallevents (varchar). Guarded add for completeness.
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN meeting_type VARCHAR(100) NOT NULL DEFAULT ''NA''',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='meeting_type');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN proposal_status VARCHAR(64) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='proposal_status');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN proposal_type VARCHAR(100) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='proposal_type');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN submit_proposal VARCHAR(16) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='submit_proposal');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN proposal_submit_channel VARCHAR(64) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='proposal_submit_channel');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN proposal_of_location VARCHAR(255) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='proposal_of_location');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN proposal_no_of_school INT NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='proposal_no_of_school');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN proposal_of_budget BIGINT NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='proposal_of_budget');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN social_platform VARCHAR(64) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='social_platform');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN social_post_type VARCHAR(64) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='social_post_type');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN social_reach INT NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='social_reach');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN social_post_url VARCHAR(512) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='social_post_url');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- idempotency_key column (already present as varchar(80); guarded add keeps
-- file self-contained for a fresh database). Spec minimum is VARCHAR(64).
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD COLUMN idempotency_key VARCHAR(64) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND COLUMN_NAME='idempotency_key');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- UNIQUE KEY uniq_idem on idempotency_key. THIS is the object that was
-- missing on staging and is required for DB-level idempotency. Multiple NULLs
-- are permitted under a MySQL UNIQUE index, so existing NULL rows are fine.
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE tblcallevents ADD UNIQUE KEY uniq_idem (idempotency_key)',
  'SELECT 1') FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tblcallevents' AND INDEX_NAME='uniq_idem');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ===== company route-optimization columns (additive) =================
-- Canonical company table on this database is company_master (there is no
-- bare `company` table). lat/lng support the mobile route optimizer.
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE company_master ADD COLUMN lat DECIMAL(10,7) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='company_master' AND COLUMN_NAME='lat');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE company_master ADD COLUMN lng DECIMAL(10,7) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='company_master' AND COLUMN_NAME='lng');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ===== app_reminder: title column for v2150 mobile contract ==========
SET @s := (SELECT IF(COUNT(*)=0,
  'ALTER TABLE app_reminder ADD COLUMN title VARCHAR(255) NULL',
  'SELECT 1') FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='app_reminder' AND COLUMN_NAME='title');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- End of v2150 migration.
