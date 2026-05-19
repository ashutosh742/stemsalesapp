<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM secrets loader.
 *
 * Resolves a secret in this order:
 *   1. PHP-FPM / Apache environment variable ($_ENV[$key])
 *   2. Shell environment fallback (getenv($key))
 *   3. /etc/stemapp/secrets.env key=value file (chmod 600, root:www-data)
 *   4. application/config/secrets.local.php (gitignored, dev only)
 *   5. Empty string ('') with an error_log warning. NEVER a hardcoded key.
 *
 * Usage from anywhere:
 *   $key = stem_secret('openai_api_key');
 *   $key = stem_secret('deepseek_api_key', '');  // explicit default
 *
 * Replaces every hardcoded sk-... in:
 *   application/config/config.php
 *   application/config/openai.php
 *   application/config/deepseek.php
 *   application/controllers/Chat.php
 *   application/models/Ai_model.php
 *
 * Author: STEM Learning ops, 2026-05-19
 */

if (!function_exists('stem_secret')) {

    /**
     * Cache parsed /etc/stemapp/secrets.env so we read disk once per request.
     * @var array|null
     */
    static $stem_secret_file_cache = null;

    function stem_secret($key, $default = '')
    {
        global $stem_secret_file_cache;
        $key = (string) $key;
        if ($key === '') {
            return $default;
        }

        // 1. PHP-FPM / Apache SetEnv / docker -e ENV
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }

        // 2. Shell env (CLI cron jobs etc)
        $env = getenv($key);
        if ($env !== false && $env !== '') {
            return $env;
        }

        // Also try uppercase variant (common convention)
        $up = strtoupper($key);
        if (isset($_ENV[$up]) && $_ENV[$up] !== '') {
            return $_ENV[$up];
        }
        $env_up = getenv($up);
        if ($env_up !== false && $env_up !== '') {
            return $env_up;
        }

        // 3. /etc/stemapp/secrets.env (preferred for production)
        if ($stem_secret_file_cache === null) {
            $stem_secret_file_cache = [];
            $secrets_path = '/etc/stemapp/secrets.env';
            if (is_readable($secrets_path)) {
                $lines = file($secrets_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines) {
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '' || $line[0] === '#') {
                            continue;
                        }
                        $eq = strpos($line, '=');
                        if ($eq === false) {
                            continue;
                        }
                        $k = trim(substr($line, 0, $eq));
                        $v = trim(substr($line, $eq + 1));
                        // Strip surrounding quotes if present.
                        if (strlen($v) >= 2) {
                            $first = $v[0]; $last = substr($v, -1);
                            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                                $v = substr($v, 1, -1);
                            }
                        }
                        $stem_secret_file_cache[$k] = $v;
                    }
                }
            }
        }
        if (isset($stem_secret_file_cache[$key]) && $stem_secret_file_cache[$key] !== '') {
            return $stem_secret_file_cache[$key];
        }
        if (isset($stem_secret_file_cache[$up]) && $stem_secret_file_cache[$up] !== '') {
            return $stem_secret_file_cache[$up];
        }

        // 4. application/config/secrets.local.php (gitignored, dev only)
        $local = APPPATH . 'config/secrets.local.php';
        if (file_exists($local)) {
            $local_secrets = include $local;
            if (is_array($local_secrets) && isset($local_secrets[$key]) && $local_secrets[$key] !== '') {
                return $local_secrets[$key];
            }
        }

        // 5. Last resort: warn and return default. NEVER a hardcoded key.
        error_log("[stem_secret] missing secret '$key', returning default. Set it in /etc/stemapp/secrets.env or env var.");
        return $default;
    }
}
