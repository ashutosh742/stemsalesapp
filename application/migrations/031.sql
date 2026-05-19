-- ============================================================================
-- STEM CRM - Migration 031
-- Lead Activity Heatmap
-- ============================================================================
-- Adds:
--   TABLE  lead_activity_score
--   VIEW   v_lead_activity_heatmap
--   VIEW   v_pipeline_with_heatmap
-- Refreshed nightly at 2 AM IST by stem_lead_heatmap_agent_php.php.
--
-- Scoring weights (applied with linear 30-day decay):
--   meeting        10   (tblcallevents actiontype matching visit/meeting)
--   MoM approved   15   (mom_data approval_status = approved)
--   CM touch        8   (tblcallevents cm_uid not null)
--   email inbound   6   (lead_email_log direction = in)
--   email outbound  4   (lead_email_log direction = out)
--   call logged     5   (tblcallevents actiontype = call)
--   photo captured  3   (tblcallevents photo_url not null)
--   GPS check-in    2   (tblcallevents lat not zero)
--
-- Bands: cold under 20, warm 20-49, hot 50-89, on_fire 90+.
--
-- Author: STEM ops
-- Date: 2026-05-19
-- Pilot hold: Mon 25 May 2026 (Mumbai cluster UIDs 12,42,43,44,45,46)
-- Org rollout: Mon 1 Jun 2026
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. LEAD ACTIVITY SCORE TABLE
-- One row per active lead. Upserted nightly by the refresh agent.
-- lead_id mirrors init_call.id (cid_id throughout the codebase).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lead_activity_score (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lead_id           INT UNSIGNED NOT NULL COMMENT 'init_call.id',
  bd_uid            INT UNSIGNED NOT NULL COMMENT 'init_call.mainbd',
  cm_uid            INT UNSIGNED DEFAULT NULL,

  -- Rolling scores with linear decay
  score_today       DECIMAL(10,2) NOT NULL DEFAULT 0.00
                    COMMENT 'weighted score for events on today only',
  score_7d          DECIMAL(10,2) NOT NULL DEFAULT 0.00
                    COMMENT 'weighted decayed score over last 7 days',
  score_30d         DECIMAL(10,2) NOT NULL DEFAULT 0.00
                    COMMENT 'weighted decayed score over last 30 days',

  -- Last-touch timestamps per signal type
  last_meeting_at   DATETIME DEFAULT NULL,
  last_mom_at       DATETIME DEFAULT NULL,
  last_email_at     DATETIME DEFAULT NULL,
  last_call_at      DATETIME DEFAULT NULL,
  last_cm_touch_at  DATETIME DEFAULT NULL,
  last_photo_at     DATETIME DEFAULT NULL,
  last_gps_at       DATETIME DEFAULT NULL,

  -- Band classification
  heatmap_band      ENUM('cold','warm','hot','on_fire') NOT NULL DEFAULT 'cold',

  -- Top contributing signal in the 30-day window
  top_signal_type   VARCHAR(32) DEFAULT NULL
                    COMMENT 'meeting|mom|cm_touch|email_in|email_out|call|photo|gps',

  -- Days since any touch
  days_since_touch  SMALLINT UNSIGNED NOT NULL DEFAULT 0,

  -- Metadata
  computed_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
  backfill          TINYINT(1) NOT NULL DEFAULT 0
                    COMMENT '1 if this row was written by the 30-day backfill run',

  PRIMARY KEY (id),
  UNIQUE KEY uq_lead (lead_id),
  KEY idx_las_score_30d (score_30d DESC),
  KEY idx_las_band      (heatmap_band),
  KEY idx_las_bd        (bd_uid),
  KEY idx_las_bd_band   (bd_uid, heatmap_band),
  KEY idx_las_cm        (cm_uid),
  KEY idx_las_computed  (computed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Nightly-refreshed activity heat score per lead. Migration 031.';

-- ----------------------------------------------------------------------------
-- 2. VIEW: v_lead_activity_heatmap
-- Flat list view for the heatmap list screen and CM cluster inspection.
-- Exposes BD name, cluster, school, cstatus, fbudget alongside heat columns.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_lead_activity_heatmap AS
SELECT
  las.lead_id,
  las.bd_uid,
  CONCAT(ub.firstName, ' ', COALESCE(ub.lastName, '')) AS bd_name,
  las.cm_uid,
  CONCAT(uc.firstName, ' ', COALESCE(uc.lastName, '')) AS cm_name,

  -- Cluster from reporting_hierarchy label; fall back to cm_uid cast
  COALESCE(rh.cluster_name, CONCAT('Cluster-', COALESCE(las.cm_uid, 0)))
                                                        AS cluster,

  ic.compny_nm      AS school,
  ic.cstatus,
  COALESCE(ic.fbudget, 0) AS fbudget_rs,

  las.score_today,
  las.score_7d,
  las.score_30d,
  las.heatmap_band  AS band,
  las.days_since_touch,
  las.top_signal_type,
  las.last_meeting_at,
  las.last_mom_at,
  las.last_email_at,
  las.last_call_at,
  las.last_cm_touch_at,
  las.computed_at

FROM lead_activity_score las
INNER JOIN init_call ic       ON ic.id  = las.lead_id
LEFT  JOIN user ub            ON ub.uid = las.bd_uid
LEFT  JOIN user uc            ON uc.uid = las.cm_uid
LEFT  JOIN reporting_hierarchy rh
           ON rh.employee_uid = las.bd_uid
          AND rh.active = 1
ORDER BY las.score_30d DESC;

-- ----------------------------------------------------------------------------
-- 3. VIEW: v_pipeline_with_heatmap
-- Extends the standard pipeline surface (init_call) with heatmap columns.
-- Consumed by the patched PipelineScreen via the activity_heatmap endpoint.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_pipeline_with_heatmap AS
SELECT
  ic.id             AS cid_id,
  ic.compny_nm      AS school,
  ic.mainbd         AS bd_uid,
  CONCAT(ub.firstName, ' ', COALESCE(ub.lastName, '')) AS bd_name,
  ic.cstatus,
  COALESCE(ic.fbudget, 0)        AS fbudget_rs,
  ic.createDate                  AS lead_created_at,

  -- Heat columns (NULL-safe; pipeline rows without a score row show 0 / cold)
  COALESCE(las.score_today, 0)   AS score_today,
  COALESCE(las.score_7d,    0)   AS score_7d,
  COALESCE(las.score_30d,   0)   AS score_30d,
  COALESCE(las.heatmap_band, 'cold') AS band,
  COALESCE(las.days_since_touch, 99) AS days_since_touch,
  las.top_signal_type,
  las.last_meeting_at,
  las.last_mom_at,
  las.last_email_at,
  las.last_call_at,
  las.last_cm_touch_at,
  las.computed_at   AS score_computed_at

FROM init_call ic
LEFT JOIN user ub                ON ub.uid  = ic.mainbd
LEFT JOIN lead_activity_score las ON las.lead_id = ic.id
WHERE ic.cstatus NOT IN (12, 13)   -- exclude Won/Lost from default view
ORDER BY COALESCE(las.score_30d, 0) DESC, ic.createDate DESC;

-- ----------------------------------------------------------------------------
-- 4. VIEW: v_on_fire_leaders
-- Top BDs by on_fire count in their cluster. Drives the applause callout.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_on_fire_leaders AS
SELECT
  las.bd_uid,
  CONCAT(ub.firstName, ' ', COALESCE(ub.lastName, '')) AS bd_name,
  las.cm_uid,
  COUNT(*) AS on_fire_count,
  MAX(las.score_30d) AS peak_score_30d
FROM lead_activity_score las
LEFT JOIN user ub ON ub.uid = las.bd_uid
WHERE las.heatmap_band = 'on_fire'
GROUP BY las.bd_uid, las.cm_uid
ORDER BY on_fire_count DESC, peak_score_30d DESC;

-- ----------------------------------------------------------------------------
-- 5. VIEW: v_band_summary_by_bd
-- Count of leads per band per BD. Used by the BD summary card.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_band_summary_by_bd AS
SELECT
  las.bd_uid,
  CONCAT(ub.firstName, ' ', COALESCE(ub.lastName, '')) AS bd_name,
  las.cm_uid,
  SUM(CASE WHEN las.heatmap_band = 'on_fire' THEN 1 ELSE 0 END) AS on_fire_count,
  SUM(CASE WHEN las.heatmap_band = 'hot'     THEN 1 ELSE 0 END) AS hot_count,
  SUM(CASE WHEN las.heatmap_band = 'warm'    THEN 1 ELSE 0 END) AS warm_count,
  SUM(CASE WHEN las.heatmap_band = 'cold'    THEN 1 ELSE 0 END) AS cold_count,
  COUNT(*) AS total_leads,
  ROUND(AVG(las.score_30d), 2)                                   AS avg_score_30d
FROM lead_activity_score las
LEFT JOIN user ub ON ub.uid = las.bd_uid
GROUP BY las.bd_uid, las.cm_uid;

-- ----------------------------------------------------------------------------
-- 6. HELPER: score_band function
-- Returns band label given a score. Used in stored context if needed.
-- Provided as a deterministic function for use in ad-hoc queries.
-- ----------------------------------------------------------------------------
DROP FUNCTION IF EXISTS fn_score_band;
DELIMITER $$
CREATE FUNCTION fn_score_band(p_score DECIMAL(10,2))
RETURNS VARCHAR(8)
DETERMINISTIC
BEGIN
  IF p_score IS NULL THEN RETURN 'cold'; END IF;
  IF p_score >= 90   THEN RETURN 'on_fire'; END IF;
  IF p_score >= 50   THEN RETURN 'hot'; END IF;
  IF p_score >= 20   THEN RETURN 'warm'; END IF;
  RETURN 'cold';
END$$
DELIMITER ;

-- ----------------------------------------------------------------------------
-- 7. NOTE: single-lead on-demand refresh
-- The PHP agent (LeadHeatmap_model::refresh_single) handles on-demand refresh
-- for one lead via POST /api/lead/heatmap/refresh?lead_id=N.
-- The fn_score_band() function above is available for ad-hoc queries.
-- Bulk nightly refresh is handled entirely in PHP (batch SQL, no stored proc
-- needed; avoids DEFINER permission issues on shared hosting).
-- ============================================================================
-- END OF MIGRATION 031
-- ============================================================================
