-- Migration 039: Email-to-task auto capture (parallel surface, _v2 tables only)
-- Production untouched. Polls Gmail/IMAP for stemlearning addresses, matches inbound
-- emails to init_call.dm_email or company_master.email, surfaces a suggested task
-- (action 2 = email, purpose 21 = Reply to inbound) for the BD to accept or dismiss.
-- Plain English. Never touch tblcallevents directly; suggestions land in inbound_email_v2
-- and only become tblcallevents rows when the BD taps Accept (which calls a write
-- endpoint that uses the standard Menu_model::submit_task path with Bearer + uid).

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Inbound emails polled from mailbox
CREATE TABLE IF NOT EXISTS `inbound_email_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` VARCHAR(255) NOT NULL COMMENT 'RFC 5322 Message-ID header',
  `mailbox_account` VARCHAR(255) NOT NULL COMMENT 'which inbox polled (stemlearning@gmail.com, etc)',
  `from_email` VARCHAR(255) NOT NULL,
  `from_name` VARCHAR(255) DEFAULT NULL,
  `to_email` VARCHAR(255) NOT NULL,
  `cc_emails` TEXT DEFAULT NULL,
  `subject` VARCHAR(500) DEFAULT NULL,
  `body_text` MEDIUMTEXT DEFAULT NULL,
  `body_html` MEDIUMTEXT DEFAULT NULL,
  `received_at` DATETIME NOT NULL,
  `has_attachment` TINYINT(1) NOT NULL DEFAULT 0,
  `attachment_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `matched_lead_id` INT UNSIGNED DEFAULT NULL COMMENT 'init_call.cid_id if matched',
  `matched_company_id` INT UNSIGNED DEFAULT NULL COMMENT 'company_master.cid if matched via company email',
  `matched_bd_uid` INT UNSIGNED DEFAULT NULL COMMENT 'init_call.mainbd of matched lead',
  `match_confidence` DECIMAL(4,3) DEFAULT NULL COMMENT '0.000 to 1.000, 1.0 = exact email match',
  `match_method` ENUM('dm_email_exact','company_email_exact','domain_match','subject_keyword','none') DEFAULT 'none',
  `suggested_action_type_id` TINYINT UNSIGNED DEFAULT 2 COMMENT '2 = email action',
  `suggested_purpose_id` SMALLINT UNSIGNED DEFAULT 21 COMMENT '21 = Reply to inbound',
  `status` ENUM('pending','accepted','dismissed','no_match','duplicate') NOT NULL DEFAULT 'pending',
  `accepted_as_event_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'tblcallevents.id once accepted',
  `accepted_by_uid` INT UNSIGNED DEFAULT NULL,
  `accepted_at` DATETIME DEFAULT NULL,
  `dismissed_by_uid` INT UNSIGNED DEFAULT NULL,
  `dismissed_at` DATETIME DEFAULT NULL,
  `dismissed_reason` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_message_id` (`message_id`),
  KEY `idx_matched_bd_status` (`matched_bd_uid`, `status`),
  KEY `idx_matched_lead` (`matched_lead_id`),
  KEY `idx_received_at` (`received_at`),
  KEY `idx_status` (`status`),
  KEY `idx_from_email` (`from_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 039: polled inbound emails awaiting BD accept/dismiss';

-- 2. Poll run log (one row per polling cycle)
CREATE TABLE IF NOT EXISTS `inbound_email_poll_log_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mailbox_account` VARCHAR(255) NOT NULL,
  `poll_started_at` DATETIME NOT NULL,
  `poll_ended_at` DATETIME DEFAULT NULL,
  `messages_fetched` INT UNSIGNED NOT NULL DEFAULT 0,
  `messages_new` INT UNSIGNED NOT NULL DEFAULT 0,
  `messages_matched` INT UNSIGNED NOT NULL DEFAULT 0,
  `messages_unmatched` INT UNSIGNED NOT NULL DEFAULT 0,
  `error_code` VARCHAR(64) DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `status` ENUM('running','success','partial','failed') NOT NULL DEFAULT 'running',
  PRIMARY KEY (`id`),
  KEY `idx_mailbox_started` (`mailbox_account`, `poll_started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 039: polling cycle log';

-- 3. Mailbox config (which inboxes to poll, auth mode, poll interval)
CREATE TABLE IF NOT EXISTS `inbound_email_mailbox_v2` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mailbox_account` VARCHAR(255) NOT NULL,
  `auth_mode` ENUM('gmail_oauth','imap_password','app_password') NOT NULL DEFAULT 'gmail_oauth',
  `imap_host` VARCHAR(255) DEFAULT NULL,
  `imap_port` SMALLINT UNSIGNED DEFAULT 993,
  `oauth_refresh_token_ref` VARCHAR(255) DEFAULT NULL COMMENT 'pointer to secret store, never raw token',
  `last_poll_at` DATETIME DEFAULT NULL,
  `last_uid_seen` BIGINT UNSIGNED DEFAULT NULL COMMENT 'IMAP UID watermark to skip already-fetched',
  `poll_interval_minutes` SMALLINT UNSIGNED NOT NULL DEFAULT 5,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mailbox_account` (`mailbox_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 039: mailbox poller config';

-- 4. Attachments (kept separate so the email row stays light)
CREATE TABLE IF NOT EXISTS `inbound_email_attachment_v2` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inbound_email_id` BIGINT UNSIGNED NOT NULL,
  `filename` VARCHAR(500) DEFAULT NULL,
  `mime_type` VARCHAR(255) DEFAULT NULL,
  `size_bytes` INT UNSIGNED DEFAULT NULL,
  `storage_path` VARCHAR(1000) DEFAULT NULL COMMENT 'local path or s3 ref',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inbound_email_id` (`inbound_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 039: email attachments';

-- 5. Per-BD inbox view: pending matches
CREATE OR REPLACE VIEW `v_inbound_email_pending_for_bd` AS
SELECT
  e.id,
  e.matched_bd_uid AS bd_uid,
  e.matched_lead_id AS lead_id,
  e.from_email,
  e.from_name,
  e.subject,
  e.received_at,
  e.match_confidence,
  e.match_method,
  e.suggested_action_type_id,
  e.suggested_purpose_id,
  e.has_attachment,
  TIMESTAMPDIFF(HOUR, e.received_at, NOW()) AS hours_since_received
FROM inbound_email_v2 e
WHERE e.status = 'pending'
  AND e.matched_bd_uid IS NOT NULL;

-- 6. Seed default mailbox config
INSERT IGNORE INTO `inbound_email_mailbox_v2`
  (`mailbox_account`, `auth_mode`, `imap_host`, `imap_port`, `poll_interval_minutes`, `is_active`)
VALUES
  ('stemlearning@gmail.com', 'gmail_oauth', 'imap.gmail.com', 993, 5, 1);

-- 7. Config table (thresholds, role gating)
CREATE TABLE IF NOT EXISTS `inbound_email_config_v2` (
  `config_key` VARCHAR(100) NOT NULL,
  `config_value` VARCHAR(500) NOT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Migration 039: config';

INSERT IGNORE INTO `inbound_email_config_v2` (`config_key`, `config_value`, `description`) VALUES
  ('match_min_confidence', '0.700', 'Below this, mark no_match instead of pending'),
  ('domain_match_enabled', '1', 'Allow domain-only fallback match'),
  ('pilot_mode', '1', 'When 1, only show inbox to pilot uids 42,43,44,45,46,12'),
  ('pilot_uids_csv', '42,43,44,45,46,12', 'Pilot user ids during May 25 - Jun 1 2026'),
  ('autotask_purpose_id_default', '21', 'Reply to inbound'),
  ('autotask_action_type_id_default', '2', 'Email action');

SET FOREIGN_KEY_CHECKS = 1;

-- End migration 039
