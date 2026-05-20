-- Migration 043: Mobile session table for token-based mobile auth
-- Supports the new POST /api/login and GET /api/session endpoints
-- introduced in commit e69396a (feature/mobile-api-endpoints).
--
-- Pattern:
--   1) Mobile client POSTs username + password to /api/login
--   2) Server validates against user table, mints a 64-char opaque token,
--      writes a row to mobile_session with TTL (default 30 days)
--   3) Client uses Authorization: Bearer <token> on subsequent calls
--   4) /api/session looks up the token, returns uid + role + cluster
--   5) Expired rows are cleaned by a daily housekeeping cron (see end of file)
--
-- This is parallel to STEM_DIGEST_TOKEN (which stays as the admin/cron token).
-- Field auth uses mobile_session; admin/cron auth uses STEM_DIGEST_TOKEN.

CREATE TABLE IF NOT EXISTS mobile_session (
  token       VARCHAR(64)  NOT NULL PRIMARY KEY,
  uid         INT          NOT NULL,
  created_at  DATETIME     NOT NULL,
  expires_at  DATETIME     NOT NULL,
  last_seen   DATETIME     NULL,
  user_agent  VARCHAR(255) NULL,
  ip          VARCHAR(45)  NULL,
  device_id   VARCHAR(64)  NULL,
  revoked     TINYINT(1)   NOT NULL DEFAULT 0,
  INDEX idx_mobile_session_uid (uid),
  INDEX idx_mobile_session_expires (expires_at),
  INDEX idx_mobile_session_device (device_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note on FK: we deliberately do NOT add a hard FK to user(uid) because
-- the prod user table is in a separate schema in some deployments. The
-- application layer already validates uid against user before issuing a
-- token; orphan rows are cleaned by the housekeeping query below.

-- Housekeeping (run daily by a cron, NOT installed by this migration):
--   DELETE FROM mobile_session
--   WHERE expires_at < NOW() OR revoked = 1;
--
-- Recommended TTL: 30 days. Token length: 64 chars (base62, ~380 bits).
-- Revoke on logout: UPDATE mobile_session SET revoked = 1 WHERE token = ?;
