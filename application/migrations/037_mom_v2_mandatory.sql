-- Migration 037: MoM v2 Mandatory Schema + Brian Tracy Agenda Gate
-- Date: 2026-05-19
-- Author: STEM AI
-- Purpose: Enforce 15 mandatory MoM questions per Brian Tracy agenda-driven meeting principle.
--          BD must (a) lock the agenda before meeting starts, (b) cover at least 60 percent
--          of questions in voice recording, (c) confirm structured answers in form post-meeting.
--          CM reviews structured 15-field row, not narrative blob.
-- Parallel demo only. Does NOT modify production mom_data table.

-- =========================================================================
-- 1. mom_v2_question_schema: the canonical 15-question catalog
-- =========================================================================
CREATE TABLE IF NOT EXISTS mom_v2_question_schema (
    question_id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    sr_no               TINYINT UNSIGNED NOT NULL COMMENT 'matches production MOM Report sr no 5-20',
    question_text       VARCHAR(500) NOT NULL,
    answer_type         ENUM('yes_no','single_select','multi_select','rs_amount','text_short','text_long','dropdown_partner','dropdown_offering') NOT NULL,
    options_json        TEXT NULL COMMENT 'JSON array of options for select types',
    required_always     TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = required for every MoM',
    required_cstatus_min INT NULL COMMENT 'required when current_status_id >= this value',
    required_rp_only    TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = required only for RP meetings (actiontype 4)',
    required_partner_non_direct TINYINT(1) NOT NULL DEFAULT 0,
    voice_keywords_json TEXT NULL COMMENT 'JSON array of keywords/phrases to detect in Whisper transcript',
    validation_regex    VARCHAR(255) NULL,
    sort_order          TINYINT UNSIGNED NOT NULL,
    is_active           TINYINT(1) NOT NULL DEFAULT 1,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (question_id),
    UNIQUE KEY uk_sr_no (sr_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed the 15 BD-answerable questions from production MOM Report
INSERT INTO mom_v2_question_schema
    (sr_no, question_text, answer_type, options_json, required_always, required_cstatus_min, required_rp_only, required_partner_non_direct, voice_keywords_json, sort_order)
VALUES
    (5,  'Action Taken in the meeting', 'yes_no', NULL, 1, NULL, 0, 0, '["action taken","followed up","completed","did","done"]', 1),
    (6,  'Meeting done with Initiator, Influencer or Decision Maker', 'single_select', '["Initiator","Influencer","Decision Maker"]', 1, NULL, 0, 0, '["principal","director","chairman","trustee","owner","decision maker","DM","HOD","head of department","procurement"]', 2),
    (7,  'Presentation and pitching is done for which offering', 'dropdown_offering', '["MSC","Lab Setup","Kit","Workshop","Training","Curriculum","Other"]', 1, NULL, 0, 0, '["pitched","showed","presented","demoed","MSC","lab","kit","workshop","curriculum"]', 3),
    (8,  'Client thematic area for Project Intervention this FY', 'text_short', NULL, 0, 6, 0, 0, '["thematic","focus area","priority","FY","financial year","intervention"]', 4),
    (9,  'Does the client have already adopted schools', 'yes_no', NULL, 1, NULL, 0, 0, '["already","existing","current schools","adopted","running"]', 5),
    (10, 'Who are the approving authorities of the proposal', 'text_short', NULL, 0, 6, 0, 0, '["approving","authority","management","board","committee","sanction"]', 6),
    (11, 'Left over budget for current financial year (Rs)', 'rs_amount', NULL, 0, 6, 0, 0, '["budget","leftover","balance","unspent","remaining","crore","lakh"]', 7),
    (12, 'Fund sanction limit at their level (Rs)', 'rs_amount', NULL, 0, 6, 0, 0, '["sanction","limit","approve up to","their level","ceiling"]', 8),
    (13, 'Any other specific remarks regards to the meeting', 'text_long', NULL, 1, NULL, 0, 0, NULL, 9),
    (14, 'Do we need to submit the proposal', 'yes_no', NULL, 0, NULL, 1, 0, '["proposal","submit","send","share document"]', 10),
    (15, 'Do we need to identify school', 'yes_no', NULL, 0, NULL, 1, 0, '["identify school","find school","candidate school","shortlist"]', 11),
    (16, 'School permission letter required', 'yes_no', NULL, 0, NULL, 1, 0, '["permission","letter","NOC","authorization"]', 12),
    (17, 'Client interested for School Visit', 'yes_no', NULL, 0, NULL, 1, 0, '["school visit","site visit","come and see","field visit"]', 13),
    (18, 'Intervention needed from Cluster / PST / Sales Head', 'single_select', '["Cluster","PST","Sales Head","None"]', 0, NULL, 1, 0, '["cluster","PST","sales head","escalate","senior","manager visit"]', 14),
    (19, 'Short MoM Remarks (narrative summary)', 'text_long', NULL, 1, NULL, 0, 0, NULL, 15),
    (20, 'Partner Type', 'dropdown_partner', '["NGO","Integrator","Channel","Direct","CSR","Govt Scheme","Alumni Network"]', 1, NULL, 0, 0, '["NGO","integrator","channel partner","CSR","government","scheme","alumni"]', 16);

-- =========================================================================
-- 2. mom_v2_meeting_agenda_lock: BD locks agenda BEFORE meeting starts
-- =========================================================================
CREATE TABLE IF NOT EXISTS mom_v2_meeting_agenda_lock (
    lock_id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id            INT UNSIGNED NOT NULL COMMENT 'tblcallevents.id',
    bd_uid              INT UNSIGNED NOT NULL,
    cid_id              INT UNSIGNED NOT NULL COMMENT 'init_call.id',
    locked_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    required_questions_json TEXT NOT NULL COMMENT 'JSON array of question_ids BD committed to ask',
    cstatus_at_lock     INT NOT NULL,
    actiontype_id       INT NOT NULL COMMENT 'meeting type at lock time',
    bd_committed        TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = BD ticked I will ask these',
    PRIMARY KEY (lock_id),
    UNIQUE KEY uk_event_id (event_id),
    KEY idx_bd_uid (bd_uid),
    KEY idx_locked_at (locked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- 3. mom_v2_voice_coverage: scan result of Whisper transcript against 15 questions
-- =========================================================================
CREATE TABLE IF NOT EXISTS mom_v2_voice_coverage (
    coverage_id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id            INT UNSIGNED NOT NULL,
    bd_uid              INT UNSIGNED NOT NULL,
    voice_clip_url      VARCHAR(500) NULL,
    transcript_text     LONGTEXT NULL,
    transcript_lang     VARCHAR(10) NULL COMMENT 'en, hi, en-hi mix',
    whisper_confidence  DECIMAL(5,2) NULL COMMENT 'overall Whisper transcription confidence 0-100',
    per_question_coverage_json TEXT NOT NULL COMMENT 'JSON object {question_id: {covered: bool, confidence: 0-100, extracted_answer: string}}',
    coverage_pct        DECIMAL(5,2) NOT NULL COMMENT 'percent of required questions covered',
    coverage_passed     TINYINT(1) NOT NULL COMMENT '1 if coverage_pct >= 60',
    recording_attempt   TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'BD may re-record up to 3 times',
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (coverage_id),
    KEY idx_event_attempt (event_id, recording_attempt),
    KEY idx_bd_uid (bd_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- 4. mom_v2_answers: structured 15-field row submitted by BD
-- =========================================================================
CREATE TABLE IF NOT EXISTS mom_v2_answers (
    answer_id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id            INT UNSIGNED NOT NULL,
    bd_uid              INT UNSIGNED NOT NULL,
    cid_id              INT UNSIGNED NOT NULL,
    question_id         INT UNSIGNED NOT NULL,
    answer_value        TEXT NOT NULL COMMENT 'string value, parse by answer_type',
    auto_filled         TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = pre-filled from voice',
    bd_confirmed        TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = BD explicitly confirmed in form',
    voice_confidence    DECIMAL(5,2) NULL,
    submitted_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (answer_id),
    UNIQUE KEY uk_event_question (event_id, question_id),
    KEY idx_bd_submitted (bd_uid, submitted_at),
    CONSTRAINT fk_mom_v2_question FOREIGN KEY (question_id) REFERENCES mom_v2_question_schema (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- 5. mom_v2_submission: top-level submission row + CM review state
-- =========================================================================
CREATE TABLE IF NOT EXISTS mom_v2_submission (
    submission_id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id            INT UNSIGNED NOT NULL,
    bd_uid              INT UNSIGNED NOT NULL,
    cid_id              INT UNSIGNED NOT NULL,
    cm_uid              INT UNSIGNED NULL COMMENT 'CM assigned to review',
    agenda_locked       TINYINT(1) NOT NULL DEFAULT 0,
    voice_coverage_pct  DECIMAL(5,2) NULL,
    answers_completed   TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'count of confirmed answers',
    answers_required    TINYINT UNSIGNED NOT NULL COMMENT 'count of required questions for this meeting',
    quality_grade       ENUM('A+','A','B','C','D') NULL,
    quality_score       DECIMAL(5,2) NULL,
    status              ENUM('draft','voice_done','form_done','submitted','pending_cm','approved','rejected') NOT NULL DEFAULT 'draft',
    submitted_at        TIMESTAMP NULL,
    cm_action_at        TIMESTAMP NULL,
    cm_action_reason    VARCHAR(500) NULL,
    rejected_question_ids_json TEXT NULL COMMENT 'if rejected, JSON array of question_ids CM flagged',
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (submission_id),
    UNIQUE KEY uk_event_id (event_id),
    KEY idx_status_cm (status, cm_uid),
    KEY idx_bd_submitted (bd_uid, submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- 6. Views for audit cron consumption
-- =========================================================================
CREATE OR REPLACE VIEW v_mom_v2_today AS
SELECT
    s.submission_id, s.event_id, s.bd_uid, u.firstname AS bd_name,
    s.cid_id, ic.company_name AS school_name,
    s.agenda_locked, s.voice_coverage_pct, s.answers_completed, s.answers_required,
    s.quality_grade, s.quality_score, s.status, s.submitted_at, s.cm_action_at,
    s.rejected_question_ids_json,
    CASE
        WHEN s.voice_coverage_pct IS NULL THEN 'no_voice'
        WHEN s.voice_coverage_pct < 60 THEN 'below_60_voice_coverage'
        WHEN s.answers_completed < s.answers_required THEN 'incomplete_form'
        WHEN s.status = 'rejected' THEN 'cm_rejected'
        WHEN s.quality_grade = 'D' THEN 'grade_d'
        ELSE 'ok'
    END AS top_failed_gate
FROM mom_v2_submission s
LEFT JOIN user u ON u.uid = s.bd_uid
LEFT JOIN init_call ic ON ic.id = s.cid_id
WHERE DATE(s.submitted_at) = CURDATE() OR (s.submitted_at IS NULL AND DATE(s.created_at) = CURDATE());

CREATE OR REPLACE VIEW v_mom_v2_agenda_skipped AS
SELECT
    e.id AS event_id, e.user_id AS bd_uid, u.firstname AS bd_name,
    e.cid_id, ic.company_name AS school_name,
    e.event_date, e.actiontype_id
FROM tblcallevents e
LEFT JOIN user u ON u.uid = e.user_id
LEFT JOIN init_call ic ON ic.id = e.cid_id
LEFT JOIN mom_v2_meeting_agenda_lock l ON l.event_id = e.id
WHERE e.actiontype_id IN (3, 4)
  AND e.event_date >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
  AND l.lock_id IS NULL;

-- =========================================================================
-- 7. Config flag (default OFF, parallel demo only)
-- =========================================================================
INSERT INTO app_config (config_key, config_value, description)
VALUES ('mom_v2_mandatory_enabled', 'false', 'Migration 037 MoM v2 mandatory schema gate. OFF in production until staging signoff.')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Rollback:
-- DROP VIEW IF EXISTS v_mom_v2_today, v_mom_v2_agenda_skipped;
-- DROP TABLE IF EXISTS mom_v2_submission, mom_v2_answers, mom_v2_voice_coverage, mom_v2_meeting_agenda_lock, mom_v2_question_schema;
-- DELETE FROM app_config WHERE config_key = 'mom_v2_mandatory_enabled';
