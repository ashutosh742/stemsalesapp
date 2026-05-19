# ChatAI Cache Wrapper Deploy Runbook

Drop-in 1-hour TTL response cache for `ChatAI_model::call_chatgpt_api`. All 22
production AIAgents call that single method, so the cache benefits the full
analyst surface in one shot.

Expected outcome: 70 to 80 percent reduction in OpenAI spend on repeat queries
within the same hour (dashboard reloads, multi-user opens of the same scoreboard,
morning standup chain regenerating the same narrative blocks).

## Scope

- Adds one table: `chatai_cache`
- Replaces one file: `application/models/ChatAI_model.php`
- Optional: three config keys in `application/config/config.php`
- Zero changes to any of the 22 `application/models/AIAgents/*.php` files
- Zero changes to controllers
- Zero changes to mobile clients

## Files

- `stem_chatai_cache_migration.sql` - creates `chatai_cache` table
- `ChatAI_model_cached.php` - drop-in replacement for `ChatAI_model.php`

## Pre-deploy checks

1. Confirm DB user has `CREATE TABLE` privilege on the staging schema.
2. Backup the current `ChatAI_model.php`:
   `cp application/models/ChatAI_model.php application/models/ChatAI_model.php.bak.2026-05-19`
3. Confirm `application/config/database.php` points at staging, not production.
4. Confirm there are no other custom forks of `ChatAI_model.php` under git.

## Deploy steps (staging)

### Step 1: run migration

```bash
mysql -u <user> -p <staging_db> < stem_chatai_cache_migration.sql
```

Verify:

```sql
SHOW CREATE TABLE chatai_cache;
SELECT COUNT(*) FROM chatai_cache;  -- expect 0
```

### Step 2: swap in the wrapper

```bash
cp ChatAI_model_cached.php application/models/ChatAI_model.php
```

The class name in `ChatAI_model_cached.php` is `ChatAI_model`, so CodeIgniter's
autoloader picks it up with no other change.

### Step 3 (optional): tune via config

Append to `application/config/config.php`:

```php
$config['chatai_cache_enabled']     = TRUE;   // master switch, default TRUE
$config['chatai_cache_ttl_seconds'] = 3600;   // 1 hour, default 3600
$config['chatai_cache_log_misses']  = FALSE;  // verbose miss log, default FALSE
```

Skip this step to accept the defaults.

### Step 4: smoke test

From the CLI:

```bash
php index.php cli/chat_ai_test "Summarise Pune cluster last week" 2>&1 | tee /tmp/cache_smoke.log
```

Or from the mobile app: open any AI analyst chat (LMS card 3 funnel report works
well), ask the same question twice within 5 minutes.

Verify second call is served from cache:

```sql
SELECT cache_key, prompt_preview, hit_count, last_hit_at, created_at
FROM chatai_cache
ORDER BY created_at DESC
LIMIT 5;
```

Expected: at least one row with `hit_count >= 1` after the second query.

### Step 5: monitor for 24 hours

```sql
-- Hit rate snapshot
SELECT
  COUNT(*) AS rows_cached,
  SUM(hit_count) AS total_hits,
  ROUND(SUM(hit_count) / NULLIF(COUNT(*) + SUM(hit_count), 0) * 100, 1) AS hit_rate_pct,
  MIN(created_at) AS oldest,
  MAX(last_hit_at) AS newest_hit
FROM chatai_cache
WHERE created_at >= (NOW() - INTERVAL 24 HOUR);
```

Healthy band after 24h: rows_cached 200 to 2000, hit_rate_pct 30 to 70.

### Step 6: schedule nightly cleanup

Add to CodeIgniter cron or server crontab once per day:

```sql
DELETE FROM chatai_cache WHERE created_at < (NOW() - INTERVAL 24 HOUR);
```

The wrapper only serves rows newer than `chatai_cache_ttl_seconds`, so older
rows are dead weight after that point. Keeping 24h gives the analytics team
yesterday's prompt-preview log for debugging.

## Rollback

If anything goes wrong:

```bash
cp application/models/ChatAI_model.php.bak.2026-05-19 application/models/ChatAI_model.php
```

The `chatai_cache` table can stay; it does nothing without the wrapper. Drop it
only if you want a clean revert:

```sql
DROP TABLE chatai_cache;
```

## Production deploy gate

Do NOT deploy to production until:

1. Staging has run for 5 weekdays with no errors in CodeIgniter log
2. Hit rate is at least 30 percent
3. No corruption: every cached `response` round-trips through the same prompt
   key (manual spot-check 10 random keys)
4. Confirmation that the optional config keys are also added to production
   `config.php` if non-default values are required
5. The standing pilot is at full coverage (post 25 May 2026)

## Notes

- Fallback responses (the canned "API key not configured" string) are
  intentionally NOT cached, so the next caller still gets a real attempt.
- Cache key is `sha1(json_encode({m: message, p: previousMessages}))`. Order
  of `previousMessages` matters; identical conversation context with
  reordered turns will miss the cache, which is the safe behaviour.
- Cache writes use `INSERT ... ON DUPLICATE KEY UPDATE` so parallel writers
  cannot collide on the unique cache_key.
- This wrapper is parallel to production. The original `ChatAI_model.php` is
  preserved as `.bak.2026-05-19` under the same models folder.

## Cross-references

- `/home/user/workspace/stemapp-source/application/models/ChatAI_model.php`
  (original, 107 lines)
- `/home/user/workspace/stem_mig036_demo/client/src/lib/agentDispatch.ts`
  (mobile-side map of the 22 agents that benefit from this cache)
