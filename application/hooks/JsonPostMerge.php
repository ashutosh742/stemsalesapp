<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * JsonPostMerge - pre_controller hook (installed 2026-06-08)
 *
 * PERMANENT GLOBAL FIX: the mobile app sends POST bodies as
 * Content-Type: application/json (JSON.stringify). CodeIgniter 3's
 * $this->input->post() only reads form-urlencoded bodies, so every
 * controller that used input->post() saw empty values (e.g. day-start
 * failing with 'uid is required').
 *
 * This hook decodes a JSON request body ONCE per request and merges it
 * into $_POST and $_REQUEST, so ALL existing and future controllers
 * that call input->post() work unchanged. Form-encoded posts are left
 * untouched. Non-JSON / empty bodies are no-ops.
 *
 * Additive and defensive: never throws, never overwrites a key that is
 * already present in $_POST (real form fields win).
 */
class JsonPostMerge {

    public function merge() {
        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        if ($method !== 'POST' && $method !== 'PUT' && $method !== 'PATCH' && $method !== 'DELETE') {
            return;
        }
        $ctype = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : (isset($_SERVER['HTTP_CONTENT_TYPE']) ? $_SERVER['HTTP_CONTENT_TYPE'] : '');
        if (stripos($ctype, 'application/json') === false) {
            return; // only act on JSON bodies; leave form posts alone
        }
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return;
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return; // not a JSON object/array - no-op
        }
        foreach ($decoded as $k => $v) {
            if (!array_key_exists($k, $_POST)) {
                $_POST[$k] = $v;
            }
            if (!array_key_exists($k, $_REQUEST)) {
                $_REQUEST[$k] = $v;
            }
        }
    }
}
