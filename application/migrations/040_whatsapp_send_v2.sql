-- Migration 040: WhatsApp send agent (parallel surface, _v2 tables only)
-- Production untouched. Sends + receives WhatsApp messages via Meta Cloud API or
-- approved BSP (Gupshup/Wati). Templates pre-approved by Meta. Opt-in tracked.
-- Outbound: composed by BD or auto-triggered post-meeting. Inbound: webhook only.
-- Plain English. No em-dashes. Rs for rupees.

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Outbound send log
CREATE TABLE IF NOT EXISTS `whatsapp_send_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `to_phone` VARCHAR(20) NOT NULL COMMENT 'E.164 format, no plus',
  `to_name` VARCHAR(255) DEFAULT NULL,
  `to_lead_id` INT UNSIGNED DEFAULT NULL COMMENT 'init_call.cid_id if linked',
  `to_company_id` INT UNSIGNED DEFAULT NULL,
  `from_uid` INT UNSIGNED NOT NULL COMMENT 'sending BD/CM uid',
  `template_id` INT UNSIGNED DEFAULT NULL,
  `template_name` VARCHAR(100) DEFAULT NULL COMMENT 'Meta-approved template name',
  `template_language` VARCHAR(10) DEFAULT 'en',
  `template_variables_json` TEXT DEFAULT NULL,
  `message_body` MEDIUMTEXT DEFAULT NULL COMMENT 'rendered body for audit',
  `media_url` VARCHAR(1000) DEFAULT NULL,
  `media_type` ENUM('text','image','document','audio','video') NOT NULL DEFAULT 'text',
  `provider` ENUM('meta_cloud','gupshup','wati','interakt') NOT NULL DEFAULT 'meta_cloud',
  `provider_message_id` VARCHAR(255) DEFAULT NULL,
  `linked_event_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'tblcallevents.id if logged as activity',
  `status` ENUM('queued','sent','delivered','read','failed','rejected') NOT NULL DEFAULT 'queued',
  `error_code` VARCHAR(64) DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `queued_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at` DATETIME DEFAULT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  `read_at` DATETIME DEFAULT NULL,
  `failed_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_from_uid_status` (`from_uid`, `status`),
  KEY `idx_to_phone` (`to_phone`),
  KEY `idx_lead_id` (`to_lead_id`),
  KEY `idx_provider_message_id` (`provider_message_id`),
  KEY `idx_queued_at` (`queued_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 040: WhatsApp outbound send log';

-- 2. Inbound messages (webhook receiver)
CREATE TABLE IF NOT EXISTS `whatsapp_inbound_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_phone` VARCHAR(20) NOT NULL,
  `from_name` VARCHAR(255) DEFAULT NULL,
  `to_phone_number_id` VARCHAR(64) DEFAULT NULL COMMENT 'our WABA phone id',
  `message_body` MEDIUMTEXT DEFAULT NULL,
  `media_url` VARCHAR(1000) DEFAULT NULL,
  `media_type` ENUM('text','image','document','audio','video','button','interactive') NOT NULL DEFAULT 'text',
  `provider_message_id` VARCHAR(255) NOT NULL,
  `received_at` DATETIME NOT NULL,
  `matched_lead_id` INT UNSIGNED DEFAULT NULL,
  `matched_bd_uid` INT UNSIGNED DEFAULT NULL,
  `match_confidence` DECIMAL(4,3) DEFAULT NULL,
  `status` ENUM('new','assigned','handled','ignored') NOT NULL DEFAULT 'new',
  `assigned_to_uid` INT UNSIGNED DEFAULT NULL,
  `handled_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_provider_message_id` (`provider_message_id`),
  KEY `idx_from_phone` (`from_phone`),
  KEY `idx_matched_bd_status` (`matched_bd_uid`, `status`),
  KEY `idx_received_at` (`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 040: WhatsApp inbound webhook log';

-- 3. Meta-approved template catalog
CREATE TABLE IF NOT EXISTS `whatsapp_template_v2` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(100) NOT NULL,
  `display_name` VARCHAR(255) NOT NULL,
  `category` ENUM('UTILITY','MARKETING','AUTHENTICATION','SERVICE') NOT NULL DEFAULT 'UTILITY',
  `language` VARCHAR(10) NOT NULL DEFAULT 'en',
  `body_template` MEDIUMTEXT NOT NULL COMMENT '{{1}} {{2}} placeholders',
  `header_text` VARCHAR(500) DEFAULT NULL,
  `footer_text` VARCHAR(500) DEFAULT NULL,
  `variable_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `variable_hints_json` TEXT DEFAULT NULL COMMENT 'JSON: ["bd_name","school_name","date",...]',
  `meta_approval_status` ENUM('pending','approved','rejected','paused') NOT NULL DEFAULT 'pending',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_template_lang` (`template_name`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 040: pre-approved WhatsApp templates';

-- 4. Per-phone opt-in tracking (24h session window for free-form replies)
CREATE TABLE IF NOT EXISTS `whatsapp_optin_v2` (
  `phone` VARCHAR(20) NOT NULL,
  `consent_source` ENUM('lead_form','dm_signed_proposal','webform','manual','bd_attestation') NOT NULL,
  `consent_given_at` DATETIME NOT NULL,
  `consent_proof_url` VARCHAR(1000) DEFAULT NULL,
  `last_user_message_at` DATETIME DEFAULT NULL COMMENT 'updated when inbound webhook arrives; gates 24h session window',
  `opt_out_at` DATETIME DEFAULT NULL,
  `linked_lead_id` INT UNSIGNED DEFAULT NULL,
  `linked_bd_uid` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`phone`),
  KEY `idx_linked_lead` (`linked_lead_id`),
  KEY `idx_linked_bd` (`linked_bd_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 040: WhatsApp opt-in registry';

-- 5. Config
CREATE TABLE IF NOT EXISTS `whatsapp_config_v2` (
  `config_key` VARCHAR(100) NOT NULL,
  `config_value` VARCHAR(500) NOT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `whatsapp_config_v2` (`config_key`, `config_value`, `description`) VALUES
  ('provider_active', 'meta_cloud', 'meta_cloud | gupshup | wati | interakt'),
  ('meta_phone_number_id_ref', 'env:META_WA_PHONE_NUMBER_ID', 'secret store ref, never raw'),
  ('meta_access_token_ref', 'env:META_WA_ACCESS_TOKEN', 'secret store ref'),
  ('webhook_verify_token_ref', 'env:META_WA_WEBHOOK_VERIFY_TOKEN', 'webhook subscribe step'),
  ('rate_limit_per_uid_per_hour', '20', 'cap BD sends per hour'),
  ('rate_limit_per_phone_per_day', '3', 'cap sends to the same phone per day'),
  ('pilot_mode', '1', 'only pilot uids 42,43,44,45,46,12 can send during pilot'),
  ('pilot_uids_csv', '42,43,44,45,46,12', 'May 25 - Jun 1 2026 pilot scope');

-- 6. Seed 4 starter templates (pending Meta approval until BD team submits via Business Manager)
INSERT IGNORE INTO `whatsapp_template_v2`
  (`template_name`, `display_name`, `category`, `language`, `body_template`, `variable_count`, `variable_hints_json`, `meta_approval_status`) VALUES
  ('thank_you_after_meeting', 'Thank you after meeting',
   'UTILITY', 'en',
   'Hello {{1}}, thank you for taking time today to discuss STEM Learning programs for {{2}}. As promised, here is a quick recap. I will follow up by {{3}}. Regards, {{4}}.',
   4, '["dm_name","school_name","follow_up_date","bd_name"]', 'pending'),
  ('proposal_follow_up_day_3', 'Proposal follow up day 3',
   'UTILITY', 'en',
   'Hello {{1}}, hope the STEM proposal we shared for {{2}} on {{3}} is helpful. Any questions before we lock the demo date? Regards, {{4}}.',
   4, '["dm_name","school_name","proposal_date","bd_name"]', 'pending'),
  ('dm_no_show_reminder', 'DM no show reminder',
   'UTILITY', 'en',
   'Hello {{1}}, I was at {{2}} today at {{3}} for our scheduled meeting. Can we re-confirm a date this week? Regards, {{4}}.',
   4, '["dm_name","school_name","meeting_time","bd_name"]', 'pending'),
  ('monthly_newsletter', 'Monthly STEM newsletter',
   'MARKETING', 'en',
   'Hello {{1}}, this month at STEM Learning we have new updates for {{2}}. Here is the link: {{3}}. Regards, STEM Learning.',
   3, '["dm_name","school_name","newsletter_url"]', 'pending');

-- 7. Convenience view: per-BD outbound queue and recent activity
CREATE OR REPLACE VIEW `v_whatsapp_recent_for_bd` AS
SELECT
  s.id,
  s.from_uid AS bd_uid,
  s.to_phone,
  s.to_name,
  s.to_lead_id,
  s.template_name,
  s.status,
  s.queued_at,
  s.sent_at,
  s.delivered_at,
  s.read_at,
  TIMESTAMPDIFF(MINUTE, s.queued_at, NOW()) AS minutes_since_queued
FROM whatsapp_send_v2 s
WHERE s.queued_at >= DATE_SUB(NOW(), INTERVAL 14 DAY);

SET FOREIGN_KEY_CHECKS = 1;

-- End migration 040
