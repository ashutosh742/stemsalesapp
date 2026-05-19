# API Key Rotation Runbook

Rotate the leaked OpenAI and DeepSeek keys and move them out of source code.

**Status of leaked keys (as of 2026-05-19):** EXPOSED in `production` branch git history of `ashutosh742/stemsalesapp`. Assume compromised. Rotate immediately at the provider, then deploy this branch.

## Files in this PR

| File | Purpose |
|---|---|
| `staging/application/config/secrets.php` | New loader. Reads from env or `/etc/stemapp/secrets.env`. |
| `staging/application/config/openai.php` | Patched. Calls `stem_secret('openai_api_key')`. |
| `staging/application/config/deepseek.php` | Patched. Calls `stem_secret('deepseek_api_key')`. |
| `staging/application/models/Ai_model.php` | Patched. Reads from config instead of inline `$apiKey`. |
| `staging/application/config/secrets.local.php.example` | Dev-only template. Copy to `secrets.local.php` for local dev. |
| `staging/server-config/secrets.env.example` | Production template for `/etc/stemapp/secrets.env`. |
| `staging/docs/rotate_keys_diff.patch` | Unified diff for `config.php` line 526 and `Chat.php` line 344 (apply with `patch -p1`). |
| `.gitignore` additions | `secrets.local.php`, `*.env`, `secrets.env`. |

## Step 1 — Rotate at provider (DO THIS FIRST)

1. **OpenAI** — Log in to https://platform.openai.com/api-keys
   - Create new project-scoped key for STEM CRM
   - Copy it (you only see it once)
   - **DELETE** the old key `sk-svcacct-Wj0Ip7ChL2sTuDHkHaH...` immediately after
2. **DeepSeek** — Log in to https://platform.deepseek.com
   - Create new key
   - Copy it
   - **REVOKE** the old key `sk-f56ad619214642b5b31a8f7436685065`

The old keys must be revoked at the provider BEFORE the new ones are deployed, otherwise anyone who scraped the GitHub branch can keep using them.

## Step 2 — Place secrets on production server

SSH to the production server:

```bash
# Create the secrets directory outside the webroot
sudo mkdir -p /etc/stemapp
sudo chmod 750 /etc/stemapp
sudo chown root:www-data /etc/stemapp

# Create the secrets file
sudo nano /etc/stemapp/secrets.env
```

Paste:

```
openai_api_key=sk-PASTE_NEW_OPENAI_KEY_HERE
deepseek_api_key=sk-PASTE_NEW_DEEPSEEK_KEY_HERE
```

Lock it down:

```bash
sudo chown root:www-data /etc/stemapp/secrets.env
sudo chmod 640 /etc/stemapp/secrets.env

# Verify only root and www-data can read
sudo ls -la /etc/stemapp/secrets.env
# Should show: -rw-r----- 1 root www-data ...
```

## Step 3 — Deploy code changes

Once PR is merged into `stemsalesapp/main`:

```bash
# On the production server, pull the merged branch
cd /var/www/stemapp
sudo -u www-data git fetch origin main
sudo -u www-data git pull origin main

# Copy the new files into place (they are in staging/ inside the repo)
sudo cp staging/application/config/secrets.php       application/config/secrets.php
sudo cp staging/application/config/openai.php        application/config/openai.php
sudo cp staging/application/config/deepseek.php      application/config/deepseek.php
sudo cp staging/application/models/Ai_model.php      application/models/Ai_model.php

# Apply the two-file diff for config.php and Chat.php
sudo patch -p1 < staging/docs/rotate_keys_diff.patch

# Fix ownership
sudo chown -R www-data:www-data application/

# Verify no hardcoded keys remain
sudo grep -rn "sk-svcacct\|sk-proj\|sk-f56ad" application/ || echo "CLEAN: no hardcoded keys found"
```

## Step 4 — Restart PHP

```bash
# Pick whichever applies on your server
sudo systemctl restart php8.1-fpm   # most likely
# or
sudo systemctl restart php-fpm
# or
sudo systemctl reload apache2       # if mod_php instead of fpm
```

## Step 5 — Smoke test

```bash
# From the server itself, hit a ChatAI endpoint
curl -sS -H 'Authorization: Bearer <STEM_DIGEST_TOKEN>' \
  'https://stemapp.in/api/chatai/test_ping' | head -50

# Or open the mobile demo and trigger any AI analyst chat.
# Confirm the response is a real GPT-4o output, not the canned fallback string.
```

Tail the PHP error log for any `[stem_secret] missing secret` warnings:

```bash
sudo tail -f /var/log/php-fpm/error.log
# or
sudo tail -f /var/log/apache2/error.log
```

If you see `missing secret 'openai_api_key'`, the env file is unreadable by `www-data`. Recheck the `chmod 640` and `chown root:www-data`.

## Step 6 — Scrub git history (separate task)

The old keys are still searchable in the `production` branch history of `stemsalesapp`. Even after rotation, scrubbing prevents future confusion.

Options:

**A. Quick fix** — Force-overwrite the `production` branch with a fresh snapshot containing the same redactions already applied earlier today (placeholders):

```bash
# Already done: the current production branch HEAD 92af6c0 has the keys
# redacted to "sk-REDACTED-..." style placeholders. The history before that
# point does not exist because it was an orphan branch.
#
# So in fact the production branch is already clean. Nothing more needed.
```

**B. Belt and braces** — Use BFG repo cleaner if you discover any other branch with the original keys:

```bash
# Install: brew install bfg
git clone --mirror https://github.com/ashutosh742/stemsalesapp.git
bfg --replace-text patterns.txt stemsalesapp.git
cd stemsalesapp.git && git reflog expire --expire=now --all && git gc --prune=now --aggressive
git push --force
```

Where `patterns.txt` lists each leaked key on its own line.

## Step 7 — Add monitoring

After rotation, watch the OpenAI billing dashboard daily for the next 7 days. Any usage from unknown IP addresses means the new key has also leaked.

## Rollback

If anything breaks:

```bash
# Restore the previous files from backup
cd /var/www/stemapp
sudo cp application/config/openai.php.bak application/config/openai.php
sudo cp application/config/deepseek.php.bak application/config/deepseek.php
sudo cp application/config/config.php.bak application/config/config.php
sudo cp application/controllers/Chat.php.bak application/controllers/Chat.php
sudo cp application/models/Ai_model.php.bak application/models/Ai_model.php
sudo systemctl restart php8.1-fpm
```

(Make these backups in Step 3 before overwriting.)

## What did NOT change

- Mobile clients (no API key was ever in the mobile app)
- The 22 AIAgent files under `application/models/AIAgents/` — none of them read keys directly, all go through `ChatAI_model::call_chatgpt_api` which already uses `$this->config->item('openai_api_key')`
- `application/libraries/Openai.php` — already correctly reads from config
- `application/models/ChatAI_model.php` (cache wrapper from PR #2) — already correctly reads from config
- Database, controllers, routes, views

## Audit checklist

After deploy, run this and confirm all six items:

- [ ] OpenAI old key revoked at platform.openai.com
- [ ] DeepSeek old key revoked at platform.deepseek.com
- [ ] `/etc/stemapp/secrets.env` exists with `chmod 640`, owner `root:www-data`
- [ ] `grep -rn "sk-svcacct\|sk-proj\|sk-f56ad" application/` returns nothing
- [ ] One ChatAI endpoint returns a real GPT response (not fallback)
- [ ] PHP error log clean of `[stem_secret] missing` warnings
