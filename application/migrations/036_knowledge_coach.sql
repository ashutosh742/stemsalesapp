-- ============================================================================
-- STEM CRM - Migration 036
-- BD Coach + Greetings Agent + Knowledge Repository
-- ============================================================================
-- Scope: All BDs, CMs, RMs, AVPs, Director.
--        Pilot: 25 May 2026 (Mumbai 6 uids).
--        Org rollout: 1 Jun 2026.
-- Naming: snake_case, idx_ prefix for indexes.
-- Engine: InnoDB. Charset: utf8mb4_unicode_ci.
-- Collation: utf8mb4_unicode_ci (matches existing tables).
-- DATETIME cols stored in UTC; app layer converts to IST (UTC+5:30).
-- Re-runnable: CREATE TABLE IF NOT EXISTS, INSERT IGNORE for seeds.
-- Deploy target: staging only (stemapp.in). NEVER run on prod without runbook.
-- Author: STEM ops
-- Date: 2026-05-18
-- Builds on: 008, 011, 012, 019, 020, 022, 023, 025, 027, 035
-- Feature flag: feature_flag.coach_036_enabled (0=off, 1=pilot, 2=org)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 0. PRE-REQUISITE: user_type row for AVP (type_id = 29)
-- Idempotent INSERT IGNORE - safe to re-run.
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO user_type (id, type_name, description, created_at)
VALUES (29, 'AVP', 'Area Vice President - can upload artifacts, publish FAQs, force-acknowledge', NOW());

-- ----------------------------------------------------------------------------
-- 0b. FEATURE FLAG for coach_036_enabled
-- feature_flag table first created in migration 022.
-- ----------------------------------------------------------------------------
ALTER TABLE feature_flag
  ADD COLUMN IF NOT EXISTS coach_036_enabled TINYINT UNSIGNED NOT NULL DEFAULT 0
    COMMENT '0=off, 1=pilot 25 May 2026, 2=org rollout 1 Jun 2026';

-- Seed the feature_flag row so it exists for probe endpoint.
INSERT IGNORE INTO feature_flag (flag_name, flag_value, description, created_at)
VALUES ('coach_036_enabled', 0, 'BD Coach, Greetings, Knowledge Repository - Migration 036', NOW());

-- ----------------------------------------------------------------------------
-- 1. KNOWLEDGE_ARTIFACT
--    Master table for AVP/Director/CM uploads (brochures, pricing, memos, etc.)
--    file_size_bytes: BIGINT to hold up to 25 MB (app layer enforces 25 MB cap).
--    parent_artifact_id: self-referential for version chaining.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS knowledge_artifact (
  id                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  title                VARCHAR(255)     NOT NULL,
  description          TEXT             DEFAULT NULL,
  artifact_type        ENUM(
                          'product_brochure',
                          'pricing_update',
                          'marketing_campaign',
                          'competitor_battlecard',
                          'policy_update',
                          'case_study',
                          'training_video',
                          'govt_scheme_circular',
                          'regional_content',
                          'internal_memo'
                       )                NOT NULL,
  file_url             VARCHAR(2048)    DEFAULT NULL  COMMENT 'relative or CDN path',
  file_size_bytes      BIGINT UNSIGNED  DEFAULT NULL  COMMENT 'app enforces 25 MB cap',
  mime_type            VARCHAR(128)     DEFAULT NULL,
  raw_text             LONGTEXT         DEFAULT NULL  COMMENT 'parsed text from file',
  embedding            LONGTEXT         DEFAULT NULL  COMMENT 'vector embedding JSON',
  version              SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  parent_artifact_id   INT UNSIGNED     DEFAULT NULL  COMMENT 'previous version FK',
  uploaded_by_uid      INT UNSIGNED     NOT NULL      COMMENT 'user.uid of uploader',
  target_segment_json  JSON             DEFAULT NULL  COMMENT 'clusters, bd_uids, roles',
  source_link          VARCHAR(2048)    DEFAULT NULL  COMMENT 'external URL if any',
  tags                 JSON             DEFAULT NULL,
  force_ack            TINYINT(1)       NOT NULL DEFAULT 0  COMMENT '1=BD must tap Read before next plan',
  status               ENUM('draft','published','expired','archived') NOT NULL DEFAULT 'draft',
  publish_at           DATETIME         DEFAULT NULL,
  expire_at            DATETIME         DEFAULT NULL  COMMENT 'NULL = never expires',
  created_at           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ka_type_status   (artifact_type, status),
  KEY idx_ka_status_pub    (status, publish_at),
  KEY idx_ka_uploader      (uploaded_by_uid),
  KEY idx_ka_expire        (expire_at),
  KEY idx_ka_parent        (parent_artifact_id),
  KEY idx_ka_created       (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'AVP-curated knowledge repository artifacts';

-- ----------------------------------------------------------------------------
-- 2. KNOWLEDGE_DISTRIBUTION
--    One row per channel per artifact when it is published.
--    Tracks how many targets received the push and how many succeeded.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS knowledge_distribution (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  artifact_id      INT UNSIGNED   NOT NULL  COMMENT 'knowledge_artifact.id',
  channel          ENUM(
                     'push',
                     'huddle_slide',
                     'faq_seed',
                     'greetings_template',
                     'drill_library',
                     'coach_prompt'
                   )              NOT NULL,
  target_uid       INT UNSIGNED   DEFAULT NULL COMMENT 'specific BD uid if targeted',
  target_segment_label VARCHAR(120) DEFAULT NULL COMMENT 'cluster or role label',
  fired_at         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  target_count     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  success_count    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  error_summary    VARCHAR(500)   DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_kd_artifact   (artifact_id),
  KEY idx_kd_channel    (channel, fired_at),
  KEY idx_kd_target_uid (target_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Distribution channel log per knowledge artifact';

-- ----------------------------------------------------------------------------
-- 3. KNOWLEDGE_ACKNOWLEDGEMENT
--    One row per BD per force-ack artifact. BD must ack before next plan submit.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS knowledge_acknowledgement (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  artifact_id      INT UNSIGNED   NOT NULL  COMMENT 'knowledge_artifact.id',
  uid              INT UNSIGNED   NOT NULL  COMMENT 'user.uid - the BD required to ack',
  acked_at         DATETIME       DEFAULT NULL,
  ack_source       ENUM('open','button','gate_unblock') DEFAULT NULL,
  required_by_at   DATETIME       NOT NULL  COMMENT '48h after distribution',
  status           ENUM('pending','acknowledged','overdue') NOT NULL DEFAULT 'pending',
  reminded_count   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ka_ack (artifact_id, uid),
  KEY idx_kack_uid     (uid, status),
  KEY idx_kack_status  (status, required_by_at),
  KEY idx_kack_art     (artifact_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'BD force-acknowledge tracking for policy and pricing artifacts';

-- ----------------------------------------------------------------------------
-- 4. KNOWLEDGE_CANDIDATE_FAQ
--    LLM-generated candidate FAQs from an artifact, awaiting AVP one-tap publish.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS knowledge_candidate_faq (
  id                 INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  source_artifact_id INT UNSIGNED  NOT NULL  COMMENT 'knowledge_artifact.id',
  candidate_question VARCHAR(1000) NOT NULL,
  candidate_answer   TEXT          NOT NULL,
  generated_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status             ENUM('pending','published_to_faq','rejected') NOT NULL DEFAULT 'pending',
  reviewed_by_uid    INT UNSIGNED  DEFAULT NULL,
  reviewed_at        DATETIME      DEFAULT NULL,
  published_faq_id   INT UNSIGNED  DEFAULT NULL COMMENT 'faq_entry.id once published',
  PRIMARY KEY (id),
  KEY idx_kcf_artifact (source_artifact_id, status),
  KEY idx_kcf_status   (status, generated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'AI-drafted candidate FAQs awaiting AVP review and publish';

-- ----------------------------------------------------------------------------
-- 5. BD_SKILL_SIGNAL
--    One row per observed skill event. Source: agent nightly run or CM manual push.
--    score_delta range: -3 to +3 (enforced by app layer; stored as TINYINT).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bd_skill_signal (
  id                     INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  bd_uid                 INT UNSIGNED   NOT NULL  COMMENT 'user.uid of BD',
  lead_id                INT UNSIGNED   DEFAULT NULL COMMENT 'init_call.id',
  cstatus_at_observation TINYINT UNSIGNED DEFAULT NULL COMMENT 'cstatus at time of observation',
  skill_code             VARCHAR(32)    NOT NULL  COMMENT 'skill_definition.skill_code',
  signal_type            ENUM('positive','gap','critical_gap') NOT NULL,
  evidence_type          ENUM('callevent','mom','proposal','email','huddle','self_rec') NOT NULL,
  evidence_ref_id        INT UNSIGNED   DEFAULT NULL COMMENT 'id in the evidence table',
  score_delta            TINYINT        NOT NULL DEFAULT 0 COMMENT '-3 to +3',
  note                   VARCHAR(500)   DEFAULT NULL,
  observed_at            DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  source                 ENUM('agent','cm','self') NOT NULL DEFAULT 'agent',
  PRIMARY KEY (id),
  KEY idx_bss_bd_skill   (bd_uid, skill_code, observed_at),
  KEY idx_bss_lead       (lead_id),
  KEY idx_bss_type       (signal_type, observed_at),
  KEY idx_bss_skill      (skill_code, observed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Individual skill observations per BD per lead (nightly agent + CM)';

-- ----------------------------------------------------------------------------
-- 6. BD_SKILL_SCORE
--    Rolling 30-day aggregate per BD per skill.
--    Primary key is composite (uid, skill_code) - one live score row.
--    manual_adjustment_pts: CM can add bonus or penalty points.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bd_skill_score (
  uid                  INT UNSIGNED   NOT NULL  COMMENT 'user.uid of BD',
  skill_code           VARCHAR(32)    NOT NULL  COMMENT 'skill_definition.skill_code',
  score_0_100          TINYINT UNSIGNED NOT NULL DEFAULT 50,
  grade                ENUM('A+','A','B','C','D') NOT NULL DEFAULT 'C',
  signals_30d_count    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  manual_adjustment_pts TINYINT       NOT NULL DEFAULT 0 COMMENT 'CM-added bonus/penalty, INT',
  period_start         DATE           NOT NULL,
  period_end           DATE           NOT NULL,
  computed_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (uid, skill_code),
  KEY idx_bsc_grade    (grade),
  KEY idx_bsc_score    (score_0_100),
  KEY idx_bsc_updated  (last_updated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Rolling 30-day skill score per BD - refreshed nightly by sp_compute_skill_scores_nightly';

-- ----------------------------------------------------------------------------
-- 7. SKILL_DEFINITION
--    Catalogue of 14 skills. skill_code is the natural PK.
--    Seeds inserted in stem_migration_036_seed_skills.sql.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS skill_definition (
  skill_code           VARCHAR(32)    NOT NULL,
  skill_name           VARCHAR(120)   NOT NULL,
  category             ENUM(
                         'prospect',
                         'discovery',
                         'pitch',
                         'proposal',
                         'negotiate',
                         'close',
                         'follow',
                         'referral',
                         'onboard',
                         'product',
                         'faq',
                         'csr',
                         'stakeholder',
                         'regional'
                       )              NOT NULL,
  description          TEXT           DEFAULT NULL,
  primary_cstatus_range VARCHAR(32)   DEFAULT NULL COMMENT 'e.g. 1-2, 3, 6-7',
  rubric_version       TINYINT UNSIGNED NOT NULL DEFAULT 1,
  rubric_doc_url       VARCHAR(512)   DEFAULT NULL COMMENT 'path to rubric markdown',
  drill_count          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  status               ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at           DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (skill_code),
  KEY idx_sd_category  (category),
  KEY idx_sd_cstatus   (primary_cstatus_range)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Catalogue of 14 BD skills mapped to cstatus pipeline stages';

-- ----------------------------------------------------------------------------
-- 8. COACHING_DRILL
--    Library of daily drills (video, script, peer_watch, role_play).
--    Seeds inserted in stem_migration_036_seed_drills.sql.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS coaching_drill (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  drill_code       VARCHAR(64)    NOT NULL COMMENT 'unique code e.g. PROS_BEG_01',
  skill_code       VARCHAR(32)    NOT NULL  COMMENT 'skill_definition.skill_code',
  drill_type       ENUM('video','script','peer_watch','role_play') NOT NULL DEFAULT 'script',
  level            ENUM('beginner','advanced') NOT NULL DEFAULT 'beginner',
  title            VARCHAR(255)   NOT NULL,
  prompt_for_bd    TEXT           DEFAULT NULL COMMENT 'instruction shown to BD',
  success_criteria TEXT           DEFAULT NULL COMMENT 'what good looks like',
  content_url      VARCHAR(2048)  DEFAULT NULL COMMENT 'video/script resource URL',
  llm_rubric_path  VARCHAR(512)   DEFAULT NULL COMMENT 'path to LLM eval rubric',
  estimated_minutes TINYINT UNSIGNED NOT NULL DEFAULT 5,
  active           TINYINT(1)     NOT NULL DEFAULT 1,
  created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_drill_code (drill_code),
  KEY idx_cd_skill   (skill_code),
  KEY idx_cd_level   (level, active),
  KEY idx_cd_type    (drill_type, active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Library of coaching drills linked to skill definitions';

-- ----------------------------------------------------------------------------
-- 9. COACHING_ASSIGNMENT
--    Drill assigned to a BD for a given day. One row per assign event.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS coaching_assignment (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  bd_uid           INT UNSIGNED   NOT NULL  COMMENT 'user.uid of BD',
  drill_id         INT UNSIGNED   NOT NULL  COMMENT 'coaching_drill.id',
  assigned_date    DATE           NOT NULL,
  completed_at     DATETIME       DEFAULT NULL,
  self_rating      TINYINT UNSIGNED DEFAULT NULL COMMENT '1-5 BD self-rating',
  cm_rating        TINYINT UNSIGNED DEFAULT NULL COMMENT '1-5 CM rating (nullable)',
  notes            TEXT           DEFAULT NULL,
  created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ca_bd_drill_date (bd_uid, drill_id, assigned_date),
  KEY idx_ca_bd_date   (bd_uid, assigned_date),
  KEY idx_ca_completed (completed_at),
  KEY idx_ca_drill     (drill_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Daily drill assignments per BD; tracks completion and ratings';

-- ----------------------------------------------------------------------------
-- 10. ASSET_REVIEW
--     Proposal/pitch/email submitted by BD for AI review before sending.
--     reviewer_llm: model used (e.g. claude-sonnet-4-6, gpt-4o).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS asset_review (
  id                 INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  uid                INT UNSIGNED   NOT NULL  COMMENT 'user.uid of submitting BD',
  lead_id            INT UNSIGNED   DEFAULT NULL COMMENT 'init_call.id (nullable)',
  asset_type         ENUM(
                       'proposal',
                       'pitch',
                       'email',
                       'deck',
                       'followup'
                     )              NOT NULL,
  input_text         LONGTEXT       DEFAULT NULL COMMENT 'pasted content for review',
  file_url           VARCHAR(2048)  DEFAULT NULL COMMENT 'uploaded file URL',
  transcript_text    TEXT           DEFAULT NULL COMMENT 'Whisper transcript if audio input',
  rubric_scores_json JSON           DEFAULT NULL COMMENT 'per-criterion scores array',
  strengths_json     JSON           DEFAULT NULL COMMENT '3 strength strings',
  improvements_json  JSON           DEFAULT NULL COMMENT '3 improvement strings with rewrite',
  redflags_json      JSON           DEFAULT NULL COMMENT 'red flags array',
  overall_grade      ENUM('A+','A','B','C','D') DEFAULT NULL,
  approved_for_send  TINYINT(1)     NOT NULL DEFAULT 0 COMMENT '1 if grade A or A+',
  reviewer_llm       VARCHAR(64)    DEFAULT NULL COMMENT 'model used for review',
  submitted_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reviewed_at        DATETIME       DEFAULT NULL,
  sent_via_stem_at   DATETIME       DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_ar_uid       (uid, submitted_at),
  KEY idx_ar_lead      (lead_id),
  KEY idx_ar_grade     (overall_grade),
  KEY idx_ar_type      (asset_type, submitted_at),
  KEY idx_ar_approved  (approved_for_send)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'AI-graded asset reviews (proposals, pitches, emails) per BD';

-- ----------------------------------------------------------------------------
-- 11. FAQ_ENTRY
--     Searchable FAQ index. Seeded with 100 rows in stem_migration_036_seed_faqs.sql.
--     embedding: longtext JSON vector for semantic search.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS faq_entry (
  id                 INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  question           VARCHAR(1000)  NOT NULL,
  answer             TEXT           NOT NULL,
  category           ENUM(
                       'product',
                       'pricing',
                       'competitor',
                       'policy_process',
                       'tech_crm',
                       'regional_govt'
                     )              NOT NULL DEFAULT 'product',
  tags               VARCHAR(500)   DEFAULT NULL COMMENT 'comma-separated tags',
  source_url         VARCHAR(2048)  DEFAULT NULL,
  source_artifact_id INT UNSIGNED   DEFAULT NULL COMMENT 'knowledge_artifact.id (nullable)',
  embedding          LONGTEXT       DEFAULT NULL COMMENT 'vector embedding JSON',
  upvotes            INT UNSIGNED   NOT NULL DEFAULT 0,
  downvotes          INT UNSIGNED   NOT NULL DEFAULT 0,
  last_used_at       DATETIME       DEFAULT NULL,
  created_by_uid     INT UNSIGNED   NOT NULL DEFAULT 1 COMMENT 'user.uid of creator',
  status             ENUM('published','archived','pending') NOT NULL DEFAULT 'pending',
  published_at       DATETIME       DEFAULT NULL,
  created_at         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_fe_category  (category, status),
  KEY idx_fe_status    (status, published_at),
  KEY idx_fe_artifact  (source_artifact_id),
  KEY idx_fe_used      (last_used_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Searchable FAQ index - seeded with 100 rows at launch';

-- ----------------------------------------------------------------------------
-- 12. FAQ_QUERY_LOG
--     Every BD question - matched or unmatched. Drives analytics and digest.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS faq_query_log (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  asker_uid        INT UNSIGNED   NOT NULL  COMMENT 'user.uid of BD',
  query_text       VARCHAR(1000)  NOT NULL,
  matched_faq_id   INT UNSIGNED   DEFAULT NULL COMMENT 'faq_entry.id if matched',
  match_score      DECIMAL(5,4)   DEFAULT NULL COMMENT 'semantic similarity 0-1',
  was_helpful      TINYINT(1)     DEFAULT NULL COMMENT 'NULL=no feedback, 1=helpful, 0=not',
  asked_at         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_fql_asker   (asker_uid, asked_at),
  KEY idx_fql_faq     (matched_faq_id),
  KEY idx_fql_date    (asked_at),
  KEY idx_fql_helpful (was_helpful)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Log of every BD FAQ query for analytics and unanswered detection';

-- ----------------------------------------------------------------------------
-- 13. FAQ_UNANSWERED_QUEUE
--     Questions that had no match, awaiting Director/AVP answer.
--     Once answered, the row promotes to faq_entry via sp or admin action.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS faq_unanswered_queue (
  id                 INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  asker_uid          INT UNSIGNED   NOT NULL  COMMENT 'user.uid of BD who asked',
  query_text         VARCHAR(1000)  NOT NULL,
  asked_at           DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  assigned_to_uid    INT UNSIGNED   DEFAULT NULL COMMENT 'Director or AVP uid',
  answer_text        TEXT           DEFAULT NULL,
  answered_at        DATETIME       DEFAULT NULL,
  status             ENUM('open','answered','archived') NOT NULL DEFAULT 'open',
  promoted_to_faq_id INT UNSIGNED   DEFAULT NULL COMMENT 'faq_entry.id once promoted',
  PRIMARY KEY (id),
  KEY idx_fuq_status  (status, asked_at),
  KEY idx_fuq_asker   (asker_uid),
  KEY idx_fuq_assign  (assigned_to_uid, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'BD questions with no FAQ match - queued for Director/AVP reply';

-- ----------------------------------------------------------------------------
-- 14. ONBOARDING_CHECKPOINT
--     30-60-90 day ladder rows per new BD.
--     Nine milestone checkpoints per BD, created on BD account creation.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS onboarding_checkpoint (
  id                    INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  bd_uid                INT UNSIGNED   NOT NULL  COMMENT 'user.uid of new BD',
  day_offset            TINYINT UNSIGNED NOT NULL COMMENT '1, 3, 5, 10, 15, 30, 45, 60, 90',
  module_name           VARCHAR(120)   NOT NULL,
  checkpoint_description TEXT          NOT NULL,
  owner_role            ENUM('self','senior_bd','cm','rm','director','coach') NOT NULL DEFAULT 'self',
  status                ENUM('pending','in_progress','passed','failed','skipped') NOT NULL DEFAULT 'pending',
  evidence_ref          VARCHAR(255)   DEFAULT NULL COMMENT 'URL or ref id of proof',
  target_due_at         DATE           NOT NULL,
  completed_at          DATETIME       DEFAULT NULL,
  created_at            DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_oc_bd_day (bd_uid, day_offset),
  KEY idx_oc_bd     (bd_uid, status),
  KEY idx_oc_due    (target_due_at, status),
  KEY idx_oc_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT '30-60-90 day onboarding ladder checkpoints per new BD hire';

-- ----------------------------------------------------------------------------
-- 15. GREETINGS_TEMPLATE_SEED
--     Master library of festival and occasion templates.
--     Variant labels: formal_en, warm_en, regional_hi, regional_ta, etc.
--     Seeds inserted in stem_migration_036_seed_festivals.sql.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS greetings_template_seed (
  id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  occasion_code        VARCHAR(64)    NOT NULL  COMMENT 'e.g. diwali, eid_al_fitr, birthday_principal',
  festival_name        VARCHAR(120)   DEFAULT NULL,
  festival_date_pattern VARCHAR(16)   DEFAULT NULL COMMENT 'MM-DD or lunar',
  occasion_type        VARCHAR(64)    DEFAULT NULL COMMENT 'for non-festival: birthday, anniversary, win, recovery',
  variant_label        ENUM(
                         'formal_en',
                         'warm_en',
                         'regional_hi',
                         'regional_ta',
                         'regional_bn',
                         'regional_mr',
                         'regional_kn',
                         'regional_te',
                         'regional_ml'
                       )              NOT NULL DEFAULT 'formal_en',
  template_formal_en   TEXT           DEFAULT NULL,
  template_warm_en     TEXT           DEFAULT NULL,
  template_regional_hint VARCHAR(500) DEFAULT NULL COMMENT 'hint for regional language draft',
  template_body        TEXT           DEFAULT NULL COMMENT 'with {placeholders}',
  proposed_channel     ENUM('whatsapp','email','both') NOT NULL DEFAULT 'whatsapp',
  target_audience      VARCHAR(64)    NOT NULL DEFAULT 'all_stakeholders',
  active               TINYINT(1)     NOT NULL DEFAULT 1,
  created_by_uid       INT UNSIGNED   NOT NULL DEFAULT 1,
  last_used_at         DATETIME       DEFAULT NULL,
  created_at           DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_gts_occasion (occasion_code),
  KEY idx_gts_date     (festival_date_pattern),
  KEY idx_gts_active   (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Pre-approved festival and occasion greeting templates';

-- ----------------------------------------------------------------------------
-- 16. GREETINGS_OUTBOX
--     Drafts generated by the agent, awaiting BD/CM approval before send.
--     draft-only policy: nothing goes out without human tap.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS greetings_outbox (
  id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  occasion_code        VARCHAR(64)    NOT NULL,
  recipient_contact_id INT UNSIGNED   DEFAULT NULL COMMENT 'stakeholder_contact.id (mig 027)',
  bd_uid_owner         INT UNSIGNED   NOT NULL  COMMENT 'BD who owns this school',
  cm_uid_approver      INT UNSIGNED   DEFAULT NULL COMMENT 'CM who must approve',
  school_id            INT UNSIGNED   DEFAULT NULL COMMENT 'init_call.id of the school',
  draft_formal_en      TEXT           DEFAULT NULL,
  draft_warm_en        TEXT           DEFAULT NULL,
  draft_regional       TEXT           DEFAULT NULL,
  draft_regional_lang  VARCHAR(8)     DEFAULT NULL COMMENT 'hi, ta, bn, mr, kn, te, ml',
  proposed_channel     ENUM('whatsapp','email','both') NOT NULL DEFAULT 'whatsapp',
  proposed_send_at     DATETIME       DEFAULT NULL  COMMENT 'festival morning 09:00 IST',
  status               ENUM('draft','approved','sent','rejected','expired') NOT NULL DEFAULT 'draft',
  approved_by_uid      INT UNSIGNED   DEFAULT NULL,
  approved_at          DATETIME       DEFAULT NULL,
  sent_via             ENUM('whatsapp','email') DEFAULT NULL,
  sent_at              DATETIME       DEFAULT NULL,
  created_at           DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_go_bd_status   (bd_uid_owner, status),
  KEY idx_go_cm_status   (cm_uid_approver, status),
  KEY idx_go_send_at     (proposed_send_at, status),
  KEY idx_go_occasion    (occasion_code),
  KEY idx_go_contact     (recipient_contact_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Greetings drafts queue - approved by BD/CM before send';

-- ----------------------------------------------------------------------------
-- 17. GREETINGS_SENT
--     Audit log of every message actually sent. Enforces 3-per-quarter limit.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS greetings_sent (
  id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  outbox_id            INT UNSIGNED   NOT NULL  COMMENT 'greetings_outbox.id',
  recipient_contact_id INT UNSIGNED   DEFAULT NULL,
  channel              ENUM('whatsapp','email') NOT NULL,
  message_body         TEXT           NOT NULL,
  sent_at              DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  delivery_status      ENUM('sent','delivered','failed','unknown') NOT NULL DEFAULT 'sent',
  response_at          DATETIME       DEFAULT NULL COMMENT 'first reply from recipient',
  PRIMARY KEY (id),
  KEY idx_gs_outbox    (outbox_id),
  KEY idx_gs_contact   (recipient_contact_id, sent_at),
  KEY idx_gs_channel   (channel, sent_at),
  KEY idx_gs_delivery  (delivery_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT 'Audit log of all sent greetings messages';

-- ============================================================================
-- VIEWS
-- ============================================================================

-- ----------------------------------------------------------------------------
-- V1. v_bd_skill_gaps_today
--     Top 3 skill gaps per BD (lowest score), joined to best available drill.
--     Used by My Coach home tab and 07:30 morning brief extension.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_bd_skill_gaps_today AS
SELECT
  bss.uid                                       AS bd_uid,
  bss.skill_code,
  sd.skill_name,
  bss.score_0_100,
  bss.grade,
  bss.signals_30d_count,
  cd.id                                         AS recommended_drill_id,
  cd.drill_code                                 AS recommended_drill_code,
  cd.title                                      AS recommended_drill_title,
  cd.estimated_minutes                          AS drill_minutes,
  cd.drill_type,
  ROW_NUMBER() OVER (
    PARTITION BY bss.uid
    ORDER BY bss.score_0_100 ASC, bss.grade DESC
  )                                             AS gap_rank
FROM bd_skill_score bss
JOIN skill_definition sd
  ON sd.skill_code = bss.skill_code
 AND sd.status = 'active'
LEFT JOIN coaching_drill cd
  ON cd.skill_code = bss.skill_code
 AND cd.active = 1
 AND cd.id = (
   SELECT d2.id
   FROM coaching_drill d2
   WHERE d2.skill_code = bss.skill_code
     AND d2.active = 1
   ORDER BY d2.estimated_minutes ASC
   LIMIT 1
 );

-- ----------------------------------------------------------------------------
-- V2. v_onboarding_at_risk
--     BDs with 3 or more failed or missed (overdue pending) checkpoints.
--     Feeds Migration 035 red flag 16.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_onboarding_at_risk AS
SELECT
  oc.bd_uid,
  u.firstName                                    AS bd_name,
  COUNT(*)                                       AS missed_checkpoint_count,
  MIN(oc.target_due_at)                          AS earliest_missed_due,
  MAX(oc.target_due_at)                          AS latest_missed_due
FROM onboarding_checkpoint oc
LEFT JOIN user u ON u.uid = oc.bd_uid
WHERE (
        oc.status = 'failed'
     OR (oc.status = 'pending' AND oc.target_due_at < CURDATE())
     )
GROUP BY oc.bd_uid, u.firstName
HAVING COUNT(*) >= 3;

-- ----------------------------------------------------------------------------
-- V3. v_greetings_queue_for_approver
--     Pending greetings drafts per CM, sorted by send deadline.
--     Approver inbox for the Greetings tab.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_greetings_queue_for_approver AS
SELECT
  go.id                                          AS outbox_id,
  go.cm_uid_approver,
  go.bd_uid_owner,
  go.occasion_code,
  go.proposed_send_at,
  go.proposed_channel,
  go.draft_formal_en,
  go.draft_warm_en,
  go.draft_regional_lang,
  go.created_at                                  AS draft_created_at,
  DATEDIFF(go.proposed_send_at, NOW())           AS days_until_send
FROM greetings_outbox go
WHERE go.status = 'draft'
ORDER BY go.proposed_send_at ASC;

-- ----------------------------------------------------------------------------
-- V4. v_knowledge_whats_new
--     Last 14 days of published artifacts per BD-relevant scope.
--     BD's My Coach "What's new" feed, ordered by recency.
--     Note: cluster-scope filtering is done in the app layer on target_segment_json;
--     this view returns all published artifacts for the app to filter by BD uid.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_knowledge_whats_new AS
SELECT
  ka.id                                          AS artifact_id,
  ka.title,
  ka.artifact_type,
  ka.description,
  ka.file_url,
  ka.mime_type,
  ka.force_ack,
  ka.version,
  ka.publish_at,
  ka.expire_at,
  ka.tags,
  ka.target_segment_json,
  u.firstName                                    AS uploaded_by_name,
  DATEDIFF(NOW(), ka.publish_at)                 AS days_since_publish
FROM knowledge_artifact ka
LEFT JOIN user u ON u.uid = ka.uploaded_by_uid
WHERE ka.status = 'published'
  AND ka.publish_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
ORDER BY ka.publish_at DESC;

-- ----------------------------------------------------------------------------
-- V5. v_knowledge_ack_overdue
--     BDs with pending force-acknowledge older than 48 hours.
--     Feeds new red flag 19. Input to 09:00 and 17:00 ack reminder sweep.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_knowledge_ack_overdue AS
SELECT
  kack.id                                        AS ack_id,
  kack.artifact_id,
  ka.title                                       AS artifact_title,
  ka.artifact_type,
  kack.uid                                       AS bd_uid,
  u.firstName                                    AS bd_name,
  kack.required_by_at,
  kack.reminded_count,
  TIMESTAMPDIFF(HOUR, kack.required_by_at, NOW()) AS hours_overdue
FROM knowledge_acknowledgement kack
JOIN knowledge_artifact ka ON ka.id = kack.artifact_id
LEFT JOIN user u ON u.uid = kack.uid
WHERE kack.status IN ('pending','overdue')
  AND kack.required_by_at < NOW()
ORDER BY hours_overdue DESC;

-- ============================================================================
-- STORED PROCEDURES
-- ============================================================================

DELIMITER $$

-- ----------------------------------------------------------------------------
-- SP1. sp_compute_skill_scores_nightly
--      Aggregates bd_skill_signal into bd_skill_score using a rolling 30-day
--      window. Converts net score_delta to a 0-100 scale and assigns a grade.
--      Called by 00:30 IST cron. Safe to re-run - uses INSERT ... ON DUPLICATE.
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_compute_skill_scores_nightly$$
CREATE PROCEDURE sp_compute_skill_scores_nightly()
BEGIN
  DECLARE v_period_start DATE DEFAULT DATE_SUB(CURDATE(), INTERVAL 30 DAY);
  DECLARE v_period_end   DATE DEFAULT CURDATE();

  -- Step 1: aggregate raw deltas per BD per skill over 30 days
  CREATE TEMPORARY TABLE IF NOT EXISTS tmp_skill_agg (
    uid             INT UNSIGNED NOT NULL,
    skill_code      VARCHAR(32)  NOT NULL,
    net_delta       INT          NOT NULL DEFAULT 0,
    signal_count    SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (uid, skill_code)
  );

  TRUNCATE TABLE tmp_skill_agg;

  INSERT INTO tmp_skill_agg (uid, skill_code, net_delta, signal_count)
  SELECT
    bd_uid,
    skill_code,
    SUM(score_delta),
    COUNT(*)
  FROM bd_skill_signal
  WHERE observed_at >= v_period_start
  GROUP BY bd_uid, skill_code;

  -- Step 2: convert net_delta to 0-100 score and assign grade
  -- Formula: base 50 + net_delta * 2, clamped to 0-100.
  INSERT INTO bd_skill_score
    (uid, skill_code, score_0_100, grade, signals_30d_count,
     period_start, period_end, computed_at)
  SELECT
    t.uid,
    t.skill_code,
    GREATEST(0, LEAST(100, 50 + t.net_delta * 2))   AS score_0_100,
    CASE
      WHEN GREATEST(0, LEAST(100, 50 + t.net_delta * 2)) >= 90 THEN 'A+'
      WHEN GREATEST(0, LEAST(100, 50 + t.net_delta * 2)) >= 75 THEN 'A'
      WHEN GREATEST(0, LEAST(100, 50 + t.net_delta * 2)) >= 60 THEN 'B'
      WHEN GREATEST(0, LEAST(100, 50 + t.net_delta * 2)) >= 45 THEN 'C'
      ELSE 'D'
    END                                               AS grade,
    t.signal_count,
    v_period_start,
    v_period_end,
    NOW()
  FROM tmp_skill_agg t
  ON DUPLICATE KEY UPDATE
    score_0_100       = VALUES(score_0_100),
    grade             = VALUES(grade),
    signals_30d_count = VALUES(signals_30d_count),
    period_start      = VALUES(period_start),
    period_end        = VALUES(period_end),
    computed_at       = VALUES(computed_at),
    last_updated      = NOW();

  DROP TEMPORARY TABLE IF EXISTS tmp_skill_agg;

  -- Step 3: mark overdue acknowledgements
  UPDATE knowledge_acknowledgement
  SET status = 'overdue'
  WHERE status = 'pending'
    AND required_by_at < NOW();
END$$

-- ----------------------------------------------------------------------------
-- SP2. sp_distribute_knowledge_artifact(p_artifact_id)
--      Reads target_segment_json from knowledge_artifact, fans out to
--      knowledge_distribution rows for each enabled channel, and inserts
--      knowledge_acknowledgement rows when force_ack = 1.
--      Called by Knowledge_repo_agent->distribute() on publish.
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_distribute_knowledge_artifact$$
CREATE PROCEDURE sp_distribute_knowledge_artifact(IN p_artifact_id INT UNSIGNED)
BEGIN
  DECLARE v_force_ack    TINYINT(1)  DEFAULT 0;
  DECLARE v_artifact_type VARCHAR(64) DEFAULT '';
  DECLARE v_status        VARCHAR(32) DEFAULT '';

  SELECT force_ack, artifact_type, status
    INTO v_force_ack, v_artifact_type, v_status
    FROM knowledge_artifact
   WHERE id = p_artifact_id
   LIMIT 1;

  IF v_status <> 'published' THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'sp_distribute_knowledge_artifact: artifact must be in published status';
  END IF;

  -- Fire push channel for all published artifacts
  INSERT IGNORE INTO knowledge_distribution
    (artifact_id, channel, fired_at, target_count, success_count)
  VALUES
    (p_artifact_id, 'push', NOW(), 0, 0);

  -- Huddle slide for policy and product types
  IF v_artifact_type IN ('policy_update', 'product_brochure', 'pricing_update', 'internal_memo') THEN
    INSERT IGNORE INTO knowledge_distribution
      (artifact_id, channel, fired_at, target_count, success_count)
    VALUES
      (p_artifact_id, 'huddle_slide', NOW(), 0, 0);
  END IF;

  -- Drill library if training_video or case_study
  IF v_artifact_type IN ('training_video', 'case_study') THEN
    INSERT IGNORE INTO knowledge_distribution
      (artifact_id, channel, fired_at, target_count, success_count)
    VALUES
      (p_artifact_id, 'drill_library', NOW(), 0, 0);
  END IF;

  -- FAQ seed channel (candidate FAQs generated separately by app layer)
  INSERT IGNORE INTO knowledge_distribution
    (artifact_id, channel, fired_at, target_count, success_count)
  VALUES
    (p_artifact_id, 'faq_seed', NOW(), 0, 0);

  -- Greetings template if marketing_campaign
  IF v_artifact_type = 'marketing_campaign' THEN
    INSERT IGNORE INTO knowledge_distribution
      (artifact_id, channel, fired_at, target_count, success_count)
    VALUES
      (p_artifact_id, 'greetings_template', NOW(), 0, 0);
  END IF;

  -- If force_ack = 1, create pending ack rows for all active BDs
  -- target_segment_json filtering is applied by app; here we insert for all BDs
  -- with type_id = 4 (BD). Adjust type_id to match your user_type table.
  IF v_force_ack = 1 THEN
    INSERT IGNORE INTO knowledge_acknowledgement
      (artifact_id, uid, required_by_at, status)
    SELECT
      p_artifact_id,
      u.uid,
      DATE_ADD(NOW(), INTERVAL 48 HOUR),
      'pending'
    FROM user u
    WHERE u.type_id IN (4, 13, 29)  -- BD, CM, AVP
      AND u.status = 'active';
  END IF;
END$$

-- ----------------------------------------------------------------------------
-- SP3. sp_expire_knowledge_artifacts
--      Daily 00:15 IST job. Flips status to expired where expire_at < CURDATE().
--      Archives dependent FAQs and candidate FAQs pointing to expired artifacts.
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_expire_knowledge_artifacts$$
CREATE PROCEDURE sp_expire_knowledge_artifacts()
BEGIN
  -- Step 1: expire artifacts past their expiry date
  UPDATE knowledge_artifact
  SET status = 'expired',
      updated_at = NOW()
  WHERE status = 'published'
    AND expire_at IS NOT NULL
    AND expire_at < NOW();

  -- Step 2: archive live faq_entry rows that point to now-expired artifacts
  UPDATE faq_entry fe
  JOIN knowledge_artifact ka ON ka.id = fe.source_artifact_id
  SET fe.status = 'archived',
      fe.updated_at = NOW()
  WHERE fe.status = 'published'
    AND ka.status = 'expired';

  -- Step 3: reject pending candidate FAQs from expired artifacts
  UPDATE knowledge_candidate_faq kcf
  JOIN knowledge_artifact ka ON ka.id = kcf.source_artifact_id
  SET kcf.status = 'rejected',
      kcf.reviewed_at = NOW()
  WHERE kcf.status = 'pending'
    AND ka.status = 'expired';

  -- Step 4: mark overdue knowledge acknowledgements
  UPDATE knowledge_acknowledgement
  SET status = 'overdue'
  WHERE status = 'pending'
    AND required_by_at < NOW();
END$$

DELIMITER ;

-- ============================================================================
-- RED FLAG DEFINITIONS (Migration 035 extension)
-- Inserts 4 new red flag rows into the red_flag_definition table (created in
-- migration 035). INSERT IGNORE to remain idempotent.
-- ============================================================================
INSERT IGNORE INTO red_flag_definition
  (flag_id, flag_name, description, owner_role, severity, threshold_hours, escalates_to, active)
VALUES
  (16, 'onboarding_3plus_checkpoints_missed',
   'New BD missed 3 or more onboarding checkpoints',
   'CM', 'red', 24, 'RM', 1),
  (17, 'asset_review_grade_D_submitted',
   'BD submitted a grade-D asset for send without coach approval',
   'CM', 'amber', 2, 'RM', 1),
  (18, 'greetings_outbox_over_20_pending',
   'CM greetings outbox has more than 20 unapproved drafts',
   'CM', 'info', 48, 'RM', 1),
  (19, 'force_ack_artifact_unread_48h',
   'BD has not acknowledged a force-ack knowledge artifact within 48 hours',
   'CM', 'amber', 24, 'RM', 1);

-- ============================================================================
-- PILOT FEATURE FLAG OVERRIDES
-- Placeholder uids 42-46 + 12 (Mumbai pilot). Reconcile before deploy.
-- ============================================================================
INSERT IGNORE INTO feature_flag_override (uid, flag_name, flag_value, set_by_uid)
VALUES
  (42, 'coach_036_enabled', 1, 1),
  (43, 'coach_036_enabled', 1, 1),
  (44, 'coach_036_enabled', 1, 1),
  (45, 'coach_036_enabled', 1, 1),
  (46, 'coach_036_enabled', 1, 1),
  (12, 'coach_036_enabled', 1, 1);

-- ============================================================================
-- END OF MIGRATION 036
-- ============================================================================
