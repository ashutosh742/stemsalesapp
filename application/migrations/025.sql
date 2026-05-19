-- Migration 025: Universal Meeting Lifecycle
-- Date: 17 May 2026
-- Status: Staging only. Production after Mon 18 May 2026 GitHub access.
-- Pairs with: stem_universal_meeting_lifecycle_spec.md
-- All columns nullable to preserve backward compatibility with existing mom_data rows.
-- Production typos preserved: approving_autorities, fund_sanstion_limit, Compnay, Quater, "Barg in Meeting".

-- =============================================================================
-- SECTION 1: New columns on daily_planner (PLANNED state)
-- =============================================================================

ALTER TABLE daily_planner
  ADD COLUMN scheduled_start_time DATETIME NULL COMMENT 'Planned meeting start, e.g. 10:00 AM on the plan date' AFTER plan_date,
  ADD COLUMN scheduled_end_time DATETIME NULL COMMENT 'Planned meeting end' AFTER scheduled_start_time,
  ADD COLUMN dm_name_planned VARCHAR(120) NULL COMMENT 'Pre-filled from init_call.dm_contact_name at plan time' AFTER scheduled_end_time,
  ADD COLUMN is_scheduled TINYINT(1) DEFAULT 1 COMMENT '1 if scheduled in plan, 0 if walk-in/barge' AFTER dm_name_planned,
  ADD COLUMN is_travel_cluster TINYINT(1) DEFAULT 0 COMMENT '1 if meeting cluster != actor home cluster' AFTER is_scheduled,
  ADD COLUMN home_cluster_id INT NULL COMMENT 'Snapshot of actor home cluster at plan time' AFTER is_travel_cluster,
  ADD COLUMN meeting_cluster_id INT NULL COMMENT 'Snapshot of meeting cluster at plan time' AFTER home_cluster_id,
  ADD COLUMN travel_cost_advance_rs DECIMAL(10,2) NULL COMMENT 'Estimated travel cost for this trip' AFTER meeting_cluster_id,
  ADD INDEX idx_dp_travel (is_travel_cluster, plan_date),
  ADD INDEX idx_dp_scheduled_start (scheduled_start_time);

-- =============================================================================
-- SECTION 2: New columns on tblcallevents (STARTED + ENDED states)
-- =============================================================================

ALTER TABLE tblcallevents
  ADD COLUMN actual_start_time DATETIME NULL COMMENT 'When actor tapped Start meeting',
  ADD COLUMN actual_end_time DATETIME NULL COMMENT 'When actor tapped End meeting',
  ADD COLUMN start_lat DECIMAL(10,7) NULL COMMENT 'GPS at Start tap',
  ADD COLUMN start_lng DECIMAL(10,7) NULL,
  ADD COLUMN end_lat DECIMAL(10,7) NULL COMMENT 'GPS at End tap',
  ADD COLUMN end_lng DECIMAL(10,7) NULL,
  ADD COLUMN started_on_time TINYINT(1) NULL COMMENT '1 if within +/- 15 min of scheduled_start_time',
  ADD COLUMN punctuality_delta_min INT NULL COMMENT 'actual_start_time minus scheduled_start_time, in minutes',
  ADD COLUMN was_in_plan TINYINT(1) DEFAULT 0 COMMENT '1 if this callevent matches a daily_planner row, 0 if walk-in',
  ADD COLUMN barge_reason ENUM('opportunistic','plan_cancelled_by_client','in_area_extra_visit','escort_with_cm','other') NULL COMMENT 'Set when was_in_plan=0',
  ADD COLUMN is_travel_cluster TINYINT(1) DEFAULT 0,
  ADD COLUMN meeting_duration_min INT NULL COMMENT 'Computed at End',
  ADD INDEX idx_tce_actual_start (actual_start_time),
  ADD INDEX idx_tce_travel (is_travel_cluster);

-- =============================================================================
-- SECTION 3: meeting_lifecycle - one row per meeting, tracks all 5 states
-- =============================================================================

CREATE TABLE meeting_lifecycle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  callevent_id INT NOT NULL COMMENT 'FK to tblcallevents.id, one-to-one',
  cid_id INT NOT NULL COMMENT 'FK to init_call.id',
  actor_uid INT NOT NULL COMMENT 'Whoever did the meeting',
  actor_role ENUM('BD','CM','RM','SH','Director') NOT NULL,
  meeting_purpose_planned ENUM('Research','Tentative','Proposal','FollowUp','RP','Closure') NULL COMMENT 'From daily_planner',
  
  -- State 1: PLANNED
  planned_at DATETIME NULL,
  daily_planner_id INT NULL COMMENT 'FK to daily_planner row if scheduled',
  
  -- State 2: STARTED
  started_at DATETIME NULL,
  start_lat DECIMAL(10,7) NULL,
  start_lng DECIMAL(10,7) NULL,
  punctuality_delta_min INT NULL,
  audio_recording_started TINYINT(1) DEFAULT 0,
  mom_draft_id INT NULL COMMENT 'FK to mom_draft created at Start',
  
  -- State 3: CLASSIFIED (15-min nudge response)
  classified_at DATETIME NULL,
  classified_as ENUM('Tentative','RP','Closure','GotDetails','Walkout','FollowUp') NULL,
  classification_source ENUM('actor_tap','auto_timeout','agent_inferred') NULL,
  classification_punctuality_score TINYINT NULL COMMENT '0 to 100 based on how fast actor tapped',
  
  -- State 4: ENDED
  ended_at DATETIME NULL,
  end_lat DECIMAL(10,7) NULL,
  end_lng DECIMAL(10,7) NULL,
  audio_file_url VARCHAR(500) NULL,
  audio_file_size_bytes INT NULL,
  audio_duration_sec INT NULL,
  transcription_started_at DATETIME NULL,
  transcription_completed_at DATETIME NULL,
  extraction_completed_at DATETIME NULL,
  mom_submitted_at DATETIME NULL,
  mom_data_id INT NULL COMMENT 'FK to mom_data after submit',
  
  -- State 5: FOLLOWED UP
  followup_due_by DATETIME NULL,
  followup_sla_days INT NULL,
  followup_status ENUM('due','overdue','snoozed','done','abandoned','expired') NULL,
  followup_completed_at DATETIME NULL,
  followup_completed_event_id INT NULL COMMENT 'FK to the next tblcallevents row that closed this follow-up',
  
  -- Travel cluster
  is_travel_cluster TINYINT(1) DEFAULT 0,
  home_cluster_id INT NULL,
  meeting_cluster_id INT NULL,
  travel_cost_rs DECIMAL(10,2) NULL,
  
  -- Wasted visit and got-details clock
  wasted_visit TINYINT(1) DEFAULT 0,
  got_details_clock_active TINYINT(1) DEFAULT 0,
  got_details_deadline_at DATETIME NULL COMMENT 'started_at + 15 days for home cluster, + 7 days for travel cluster',
  auto_downgrade_at DATETIME NULL COMMENT 'started_at + 10 days for got-details',
  expired_at DATETIME NULL,
  
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  UNIQUE KEY uk_callevent (callevent_id),
  INDEX idx_ml_cid (cid_id),
  INDEX idx_ml_actor (actor_uid, actor_role),
  INDEX idx_ml_classified (classified_as),
  INDEX idx_ml_followup (followup_due_by, followup_status),
  INDEX idx_ml_got_details_clock (got_details_clock_active, got_details_deadline_at),
  INDEX idx_ml_travel (is_travel_cluster),
  CONSTRAINT fk_ml_callevent FOREIGN KEY (callevent_id) REFERENCES tblcallevents(id),
  CONSTRAINT fk_ml_cid FOREIGN KEY (cid_id) REFERENCES init_call(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 4: mom_draft - separate from mom_data, agent writes here
-- =============================================================================

CREATE TABLE mom_draft (
  id INT AUTO_INCREMENT PRIMARY KEY,
  callevent_id INT NOT NULL,
  cid_id INT NOT NULL,
  actor_uid INT NOT NULL,
  actor_role ENUM('BD','CM','RM','SH','Director') NOT NULL,
  meeting_purpose ENUM('Research','Tentative','Proposal','FollowUp','RP','Closure') NULL,
  meeting_with ENUM('DM','Influencer','Initiator','Gatekeeper') NULL,
  
  -- Section A: meeting fact
  rpmmom TEXT NULL,
  rpmmom_source ENUM('manual','audio_extract','prefilled') NULL,
  
  -- Section B: DM block (prefilled from init_call)
  dm_name VARCHAR(120) NULL,
  dm_name_source ENUM('manual','audio_extract','prefilled') NULL,
  dm_designation VARCHAR(120) NULL,
  dm_designation_source ENUM('manual','audio_extract','prefilled') NULL,
  dm_phone VARCHAR(20) NULL,
  dm_phone_source ENUM('manual','audio_extract','prefilled') NULL,
  dm_email VARCHAR(120) NULL,
  dm_email_source ENUM('manual','audio_extract','prefilled') NULL,
  approving_autorities JSON NULL COMMENT 'Array of {name, designation, sanction_rs}',
  
  -- Section C: discovery
  budget_for_cfyear DECIMAL(14,2) NULL,
  budget_for_cfyear_source ENUM('manual','audio_extract','prefilled') NULL,
  fund_sanstion_limit DECIMAL(14,2) NULL,
  fund_sanstion_limit_source ENUM('manual','audio_extract','prefilled') NULL,
  presentation_pitched JSON NULL COMMENT 'Array of offerings: MSC, Tinkering, Astronomy, etc',
  thematic_area VARCHAR(120) NULL,
  
  -- Section D: proposal intent
  submit_proposal TINYINT(1) NULL,
  proposed_schools INT NULL,
  proposed_budget DECIMAL(14,2) NULL,
  proposed_location VARCHAR(200) NULL,
  fitment_offer ENUM('school_visit','pilot_lab','trial_workshop','named_lab','demo') NULL,
  
  -- Section E: proposal share
  proposal_doc_url VARCHAR(500) NULL,
  proposal_shared_with VARCHAR(120) NULL,
  proposal_shared_date DATE NULL,
  proposal_value_rs DECIMAL(14,2) NULL,
  proposal_validity_days INT NULL,
  
  -- Section F: proposal response
  proposal_review_status ENUM('not_reviewed_yet','reviewed_positive','reviewed_with_changes','rejected') NULL,
  objection_log JSON NULL COMMENT 'Array of {type, note}',
  
  -- Section G: forecast
  expected_close_date DATE NULL,
  win_probability TINYINT NULL,
  r2b_status ENUM('not_started','drafted','shared','accepted','rejected_with_changes') NULL,
  
  -- Section H: intervention
  intervention_level ENUM('Cluster','PST','SalesHead','none') NULL,
  intervention_reason_code VARCHAR(60) NULL,
  intervention_sla_hours INT NULL,
  
  -- Section I: closure readiness
  payment_plan_clarified TINYINT(1) NULL,
  gst_status ENUM('registered','not_registered','exempted') NULL,
  vendor_form_status ENUM('not_started','in_progress','completed') NULL,
  contract_value_rs DECIMAL(14,2) NULL,
  
  -- Audio agent metadata
  audio_extracted_at DATETIME NULL,
  transcript_url VARCHAR(500) NULL,
  agenda_coverage_pct TINYINT NULL,
  agenda_questions_covered JSON NULL,
  agenda_questions_missing JSON NULL,
  talk_ratio_actor DECIMAL(4,3) NULL COMMENT 'Actor talk time as fraction',
  commitment_count INT DEFAULT 0,
  
  -- Lifecycle
  draft_status ENUM('open','reviewing','submitted','abandoned') DEFAULT 'open',
  draft_started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_edited_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  submitted_at DATETIME NULL,
  mom_data_id INT NULL COMMENT 'FK after submit',
  
  INDEX idx_md_callevent (callevent_id),
  INDEX idx_md_cid (cid_id),
  INDEX idx_md_actor (actor_uid),
  INDEX idx_md_status (draft_status),
  CONSTRAINT fk_md_callevent FOREIGN KEY (callevent_id) REFERENCES tblcallevents(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 5: Extend mom_data for v3
-- =============================================================================

ALTER TABLE mom_data
  ADD COLUMN mom_draft_id INT NULL COMMENT 'FK back to mom_draft' AFTER id,
  ADD COLUMN actor_role ENUM('BD','CM','RM','SH','Director') DEFAULT 'BD' AFTER uid,
  ADD COLUMN cosigned_by_uid INT NULL COMMENT 'When CM/RM joins BD on joint, their uid here' AFTER actor_role,
  ADD COLUMN cosigned_role ENUM('CM','RM','SH','Director') NULL AFTER cosigned_by_uid,
  ADD COLUMN mom_quality_grade ENUM('A','B','C','D') NULL,
  ADD COLUMN mom_quality_score INT NULL,
  ADD COLUMN agenda_coverage_pct TINYINT NULL,
  ADD COLUMN talk_ratio_actor DECIMAL(4,3) NULL,
  ADD COLUMN commitment_count INT DEFAULT 0,
  ADD COLUMN was_audio_assisted TINYINT(1) DEFAULT 0,
  ADD COLUMN mid_meeting_classification ENUM('Tentative','RP','Closure','GotDetails','Walkout','FollowUp') NULL,
  ADD COLUMN classification_source ENUM('actor_tap','auto_timeout','agent_inferred') NULL,
  ADD COLUMN is_travel_cluster TINYINT(1) DEFAULT 0,
  ADD COLUMN gates_passed TINYINT NULL,
  ADD COLUMN gates_total TINYINT DEFAULT 10,
  ADD INDEX idx_md_actor_role (actor_role),
  ADD INDEX idx_md_grade (mom_quality_grade),
  ADD INDEX idx_md_classification (mid_meeting_classification),
  ADD INDEX idx_md_cosigner (cosigned_by_uid);

-- NOTE: mom_data.callevent_id stays nullable for backward compat with legacy rows.
-- Application layer enforces NOT NULL for v3 submits via Gate 0.

-- =============================================================================
-- SECTION 6: init_call - DM contact block (folded from bridge spec)
-- =============================================================================

ALTER TABLE init_call
  ADD COLUMN dm_contact_name VARCHAR(120) NULL,
  ADD COLUMN dm_contact_designation VARCHAR(120) NULL,
  ADD COLUMN dm_contact_phone VARCHAR(20) NULL,
  ADD COLUMN dm_contact_email VARCHAR(120) NULL,
  ADD COLUMN dm_contact_org_type ENUM('school','ngo','corporate','foundation','govt_dept','trust','csr_arm') NULL,
  ADD COLUMN dm_contact_filled_at DATETIME NULL,
  ADD COLUMN dm_contact_filled_by INT NULL,
  ADD INDEX idx_ic_dm_org_type (dm_contact_org_type);

CREATE TABLE init_call_contact_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cid_id INT NOT NULL,
  field_name VARCHAR(60) NOT NULL,
  old_value VARCHAR(200) NULL,
  new_value VARCHAR(200) NULL,
  changed_by INT NOT NULL,
  changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  reason_code ENUM('corrected_designation','dm_changed','typo_fix','role_promotion','role_demotion','other') NULL,
  INDEX idx_icch_cid (cid_id),
  CONSTRAINT fk_icch_cid FOREIGN KEY (cid_id) REFERENCES init_call(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 7: lead_followup_tracker - the rot detector
-- =============================================================================

CREATE TABLE lead_followup_tracker (
  cid_id INT PRIMARY KEY,
  last_meeting_event_id INT NULL,
  last_meeting_purpose ENUM('Research','Tentative','Proposal','FollowUp','RP','Closure','GotDetails','Walkout') NULL,
  last_meeting_actor_uid INT NULL,
  last_meeting_actor_role ENUM('BD','CM','RM','SH','Director') NULL,
  last_meeting_at DATETIME NULL,
  next_followup_due_by DATETIME NULL,
  followup_sla_days INT NULL,
  followup_status ENUM('due','overdue','snoozed','done','abandoned','expired') DEFAULT 'due',
  days_since_last_touch INT NULL,
  
  -- 15-day got-details clock
  got_details_clock_active TINYINT(1) DEFAULT 0,
  got_details_started_at DATETIME NULL,
  got_details_deadline_at DATETIME NULL COMMENT '+15 days home, +7 days travel cluster',
  got_details_clock_days_left INT NULL,
  
  -- Auto-downgrade and expire
  auto_downgrade_at DATETIME NULL,
  expired_at DATETIME NULL,
  wasted_visit TINYINT(1) DEFAULT 0,
  wasted_visit_penalty_rs DECIMAL(10,2) NULL,
  
  -- Pattern detection
  consecutive_got_details_count INT DEFAULT 0,
  cm_review_required TINYINT(1) DEFAULT 0,
  cm_review_required_at DATETIME NULL,
  cm_joint_completed_at DATETIME NULL,
  
  -- Context
  is_travel_cluster TINYINT(1) DEFAULT 0,
  current_cstatus INT NULL,
  current_fbudget_rs DECIMAL(14,2) NULL,
  
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX idx_lft_status (followup_status, next_followup_due_by),
  INDEX idx_lft_clock (got_details_clock_active, got_details_deadline_at),
  INDEX idx_lft_actor (last_meeting_actor_uid),
  INDEX idx_lft_cm_review (cm_review_required),
  CONSTRAINT fk_lft_cid FOREIGN KEY (cid_id) REFERENCES init_call(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 8: rm_upsell_pipeline - auto-categorize on Won
-- =============================================================================

CREATE TABLE rm_upsell_pipeline (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cid_id INT NOT NULL,
  rm_uid INT NULL COMMENT 'Assigned RM, null until first touch',
  lane ENUM('ANCHOR','DMFT','PSU','STANDARD_UPSELL') NOT NULL,
  derivation_reason VARCHAR(200) NULL COMMENT 'e.g. "top_10_pct_fbudget", "named_csr_donor"',
  status ENUM('auto_added','rm_assigned','rm_active','rm_renewed','rm_lost') DEFAULT 'auto_added',
  cstatus_at_entry INT NULL,
  fbudget_at_entry DECIMAL(14,2) NULL,
  last_rm_touch_at DATETIME NULL,
  days_since_rm_touch INT NULL,
  renewal_due_at DATE NULL COMMENT 'For ANCHOR lane',
  auto_added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  UNIQUE KEY uk_cid_lane (cid_id, lane),
  INDEX idx_rmup_rm (rm_uid),
  INDEX idx_rmup_lane (lane),
  INDEX idx_rmup_status (status),
  INDEX idx_rmup_renewal (renewal_due_at),
  CONSTRAINT fk_rmup_cid FOREIGN KEY (cid_id) REFERENCES init_call(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 9: travel_cluster_bundle - groups all meetings on a travel day
-- =============================================================================

CREATE TABLE travel_cluster_bundle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  actor_uid INT NOT NULL,
  actor_role ENUM('BD','CM','RM','SH','Director') NOT NULL,
  travel_date DATE NOT NULL,
  home_cluster_id INT NOT NULL,
  travel_cluster_id INT NOT NULL,
  meetings_planned INT DEFAULT 0,
  meetings_completed INT DEFAULT 0,
  rp_count INT DEFAULT 0,
  got_details_count INT DEFAULT 0,
  travel_cost_rs DECIMAL(10,2) NULL,
  cost_per_rp_rs DECIMAL(10,2) NULL COMMENT 'travel_cost / rp_count',
  prospect_suggestions_offered INT DEFAULT 0,
  prospect_suggestions_accepted INT DEFAULT 0,
  cm_approved_low_count TINYINT(1) DEFAULT 0 COMMENT '1 if CM approved despite less than 3 meetings',
  cm_approval_reason TEXT NULL,
  is_underutilized TINYINT(1) DEFAULT 0 COMMENT '1 if meetings_completed less than 3',
  
  UNIQUE KEY uk_actor_date_cluster (actor_uid, travel_date, travel_cluster_id),
  INDEX idx_tcb_date (travel_date),
  INDEX idx_tcb_under (is_underutilized)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 10: meeting_agenda_template - question bank by stage
-- =============================================================================

CREATE TABLE meeting_agenda_template (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cstatus INT NOT NULL,
  meeting_purpose ENUM('Research','Tentative','Proposal','FollowUp','RP','Closure') NOT NULL,
  question_text VARCHAR(500) NOT NULL,
  question_key VARCHAR(60) NOT NULL COMMENT 'Maps to mom_v2 field name',
  is_mandatory TINYINT(1) DEFAULT 1,
  display_order INT NOT NULL,
  example_phrasings JSON NULL COMMENT 'Phrasing variants the audio agent looks for',
  active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  UNIQUE KEY uk_stage_purpose_key (cstatus, meeting_purpose, question_key),
  INDEX idx_mat_stage (cstatus, meeting_purpose)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 11: meeting_audio_log - one row per audio upload
-- =============================================================================

CREATE TABLE meeting_audio_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  callevent_id INT NOT NULL,
  meeting_lifecycle_id INT NULL,
  audio_file_url VARCHAR(500) NOT NULL,
  file_size_bytes INT NULL,
  duration_sec INT NULL,
  codec VARCHAR(30) DEFAULT 'opus_24kbps_mono',
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  transcription_started_at DATETIME NULL,
  transcription_completed_at DATETIME NULL,
  transcript_url VARCHAR(500) NULL,
  transcript_word_count INT NULL,
  whisper_cost_rs DECIMAL(8,2) NULL,
  extraction_started_at DATETIME NULL,
  extraction_completed_at DATETIME NULL,
  extraction_model VARCHAR(60) NULL COMMENT 'gpt-4o or claude-3-5-sonnet',
  extraction_cost_rs DECIMAL(8,2) NULL,
  agenda_coverage_pct TINYINT NULL,
  talk_ratio_actor DECIMAL(4,3) NULL,
  failure_reason VARCHAR(200) NULL,
  retention_purge_at DATETIME NULL COMMENT 'When this audio gets purged from S3, default uploaded_at + 90 days',
  
  INDEX idx_mal_callevent (callevent_id),
  INDEX idx_mal_uploaded (uploaded_at),
  INDEX idx_mal_purge (retention_purge_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 12: meeting_quality_score - per-meeting score breakdown
-- =============================================================================

CREATE TABLE meeting_quality_score (
  callevent_id INT PRIMARY KEY,
  mom_data_id INT NULL,
  total_score INT NOT NULL,
  grade ENUM('A','B','C','D') NOT NULL,
  
  -- Component scores
  gates_passed TINYINT NOT NULL,
  gates_score INT DEFAULT 0,
  narrative_score INT DEFAULT 0,
  agenda_coverage_score INT DEFAULT 0,
  commitment_score INT DEFAULT 0,
  objection_score INT DEFAULT 0,
  punctuality_score INT DEFAULT 0,
  talk_ratio_penalty INT DEFAULT 0,
  travel_cluster_bonus INT DEFAULT 0,
  travel_cluster_penalty INT DEFAULT 0,
  
  -- Hard caps
  max_grade_cap ENUM('A','B','C','D') DEFAULT 'A' COMMENT 'A=no cap, C=got_details cap, D=walkout cap',
  cap_reason VARCHAR(200) NULL,
  
  computed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  INDEX idx_mqs_grade (grade),
  INDEX idx_mqs_mom (mom_data_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SECTION 13: v_travel_cluster_underutilized - reporting view
-- =============================================================================

CREATE OR REPLACE VIEW v_travel_cluster_underutilized AS
SELECT
  tcb.actor_uid,
  u.fname AS actor_name,
  tcb.actor_role,
  tcb.travel_date,
  tcb.travel_cluster_id,
  tcb.meetings_planned,
  tcb.meetings_completed,
  tcb.rp_count,
  tcb.got_details_count,
  tcb.travel_cost_rs,
  CASE WHEN tcb.rp_count > 0 THEN tcb.travel_cost_rs / tcb.rp_count ELSE NULL END AS cost_per_rp_rs,
  tcb.prospect_suggestions_offered,
  tcb.prospect_suggestions_accepted
FROM travel_cluster_bundle tcb
JOIN user u ON u.uid = tcb.actor_uid
WHERE tcb.is_underutilized = 1
ORDER BY tcb.travel_date DESC, tcb.travel_cost_rs DESC;

-- =============================================================================
-- SECTION 14: v_got_details_rot_risk - top section of consolidated audit
-- =============================================================================

CREATE OR REPLACE VIEW v_got_details_rot_risk AS
SELECT
  lft.cid_id,
  ic.compny_nm AS school_name,
  lft.last_meeting_actor_uid AS actor_uid,
  u.fname AS actor_name,
  lft.last_meeting_actor_role AS actor_role,
  lft.last_meeting_at,
  lft.got_details_deadline_at,
  DATEDIFF(lft.got_details_deadline_at, NOW()) AS days_left,
  lft.is_travel_cluster,
  lft.consecutive_got_details_count,
  lft.cm_review_required,
  CASE
    WHEN DATEDIFF(NOW(), lft.last_meeting_at) >= 15 THEN 'EXPIRED'
    WHEN DATEDIFF(NOW(), lft.last_meeting_at) >= 10 THEN 'RED_AUTO_DOWNGRADE_DUE'
    WHEN DATEDIFF(NOW(), lft.last_meeting_at) >= 7 THEN 'YELLOW_ALERT'
    WHEN DATEDIFF(NOW(), lft.last_meeting_at) >= 3 THEN 'RED_NUDGE'
    ELSE 'GREEN'
  END AS risk_band
FROM lead_followup_tracker lft
JOIN init_call ic ON ic.id = lft.cid_id
JOIN user u ON u.uid = lft.last_meeting_actor_uid
WHERE lft.got_details_clock_active = 1
ORDER BY days_left ASC, lft.is_travel_cluster DESC;

-- =============================================================================
-- SECTION 15: v_followup_overdue_today - morning brief surface
-- =============================================================================

CREATE OR REPLACE VIEW v_followup_overdue_today AS
SELECT
  lft.cid_id,
  ic.compny_nm AS school_name,
  ic.cluster AS cluster_id,
  lft.last_meeting_actor_uid AS actor_uid,
  u.fname AS actor_name,
  lft.last_meeting_actor_role AS actor_role,
  lft.last_meeting_purpose,
  lft.last_meeting_at,
  lft.next_followup_due_by,
  DATEDIFF(NOW(), lft.next_followup_due_by) AS days_overdue,
  lft.followup_status,
  lft.is_travel_cluster,
  lft.current_cstatus
FROM lead_followup_tracker lft
JOIN init_call ic ON ic.id = lft.cid_id
JOIN user u ON u.uid = lft.last_meeting_actor_uid
WHERE lft.followup_status IN ('due','overdue')
  AND lft.next_followup_due_by <= NOW()
ORDER BY days_overdue DESC, lft.is_travel_cluster DESC;

-- =============================================================================
-- SECTION 16: v_upsell_pending_assignment - RM morning view
-- =============================================================================

CREATE OR REPLACE VIEW v_upsell_pending_assignment AS
SELECT
  rup.id,
  rup.cid_id,
  ic.compny_nm AS school_name,
  ic.cluster AS cluster_id,
  rup.lane,
  rup.derivation_reason,
  rup.status,
  rup.cstatus_at_entry,
  rup.fbudget_at_entry,
  rup.auto_added_at,
  DATEDIFF(NOW(), rup.auto_added_at) AS days_unassigned
FROM rm_upsell_pipeline rup
JOIN init_call ic ON ic.id = rup.cid_id
WHERE rup.status = 'auto_added'
  AND rup.rm_uid IS NULL
ORDER BY rup.fbudget_at_entry DESC;

-- =============================================================================
-- SECTION 17: Backfill helpers
-- =============================================================================

-- Backfill meeting_lifecycle from existing tblcallevents rows (last 90 days only)
-- This is a one-time script, run manually after deploy
-- INSERT INTO meeting_lifecycle (callevent_id, cid_id, actor_uid, actor_role, started_at, ended_at, ...)
-- SELECT id, cid_id, uid, 'BD', event_start_time, event_end_time, ...
-- FROM tblcallevents
-- WHERE event_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
-- AND actiontype_id IN (3,4)
-- AND id NOT IN (SELECT callevent_id FROM meeting_lifecycle);

-- Backfill lead_followup_tracker from current init_call + latest meeting
-- INSERT INTO lead_followup_tracker (cid_id, last_meeting_event_id, last_meeting_at, current_cstatus)
-- SELECT ic.id, MAX(tce.id), MAX(tce.event_date), ic.current_status_id
-- FROM init_call ic
-- LEFT JOIN tblcallevents tce ON tce.cid_id = ic.id AND tce.actiontype_id IN (3,4)
-- WHERE ic.current_status_id NOT IN (12,13)
-- GROUP BY ic.id;

-- Backfill rm_upsell_pipeline from existing Won schools
-- INSERT INTO rm_upsell_pipeline (cid_id, lane, derivation_reason, status, cstatus_at_entry, fbudget_at_entry)
-- SELECT id,
--   CASE
--     WHEN fbudget >= (SELECT fbudget FROM init_call WHERE current_status_id=12 ORDER BY fbudget DESC LIMIT 1 OFFSET CEIL(0.1 * (SELECT COUNT(*) FROM init_call WHERE current_status_id=12))) THEN 'ANCHOR'
--     WHEN compny_nm LIKE '%PSU%' OR compny_nm LIKE '%Govt%' THEN 'PSU'
--     ELSE 'STANDARD_UPSELL'
--   END AS lane,
--   'backfill_initial' AS derivation_reason,
--   'auto_added',
--   12,
--   fbudget
-- FROM init_call WHERE current_status_id = 12;

-- =============================================================================
-- SECTION 18: Probe endpoint helper
-- =============================================================================

-- Used by cron probes to detect if migration 025 is deployed:
-- SELECT 'migration_025_deployed' AS marker, NOW() AS checked_at
-- FROM meeting_lifecycle LIMIT 0;
-- If query succeeds (even with 0 rows), migration is deployed.
-- If query errors with "Table doesn't exist", migration is not deployed.

-- End of migration 025
