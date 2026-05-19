-- ============================================================
-- ChatAI cache layer migration
-- Adds a 1-hour TTL response cache for ChatAI_model::call_chatgpt_api
-- All 22 production AIAgents call that single method, so caching
-- here cuts GPT cost across the entire analyst surface.
-- ============================================================
-- Expected savings: 70 to 80 percent of OpenAI spend on repeat
-- analyst queries within the same hour (dashboard reloads,
-- multi-user open of same cluster scoreboard, narrative blocks
-- regenerated during a single morning standup window).
-- ============================================================

CREATE TABLE IF NOT EXISTS `chatai_cache` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cache_key` CHAR(40) NOT NULL COMMENT 'sha1 of message + previousMessages JSON',
  `prompt_preview` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'First 240 chars of user message, debug only',
  `response` LONGTEXT NOT NULL,
  `model` VARCHAR(64) NOT NULL DEFAULT 'gpt-4o',
  `prompt_tokens` INT UNSIGNED NULL DEFAULT NULL,
  `completion_tokens` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hit_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Incremented each time this row is served from cache',
  `last_hit_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cache_key` (`cache_key`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_last_hit_at` (`last_hit_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='ChatAI 1h TTL cache. TTL enforced in PHP wrapper, not via event.';

-- Optional: nightly cleanup of rows older than 24h to keep the table small.
-- Add a server cron or CodeIgniter task that runs this once per day:
--
--   DELETE FROM chatai_cache WHERE created_at < (NOW() - INTERVAL 24 HOUR);
--
-- The wrapper itself only serves rows newer than 1 hour, so older rows
-- are dead weight after that point.

-- ============================================================
-- Rollback (manual, only if cache wrapper is reverted):
--   DROP TABLE IF EXISTS chatai_cache;
-- ============================================================
