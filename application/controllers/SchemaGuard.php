<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SchemaGuard  (additive, 2026-06-08)
 * application/controllers/SchemaGuard.php
 *
 * PERMANENT FIX for the recurring "invented column" bug class.
 *
 * The newer agent/migration controllers were hand-written against column names
 * that never existed on init_call (company_id, school_name, cid_id, compny_nm,
 * current_status_id, ...). Each query was wrapped in a silent try/catch that
 * returned 0/empty on failure, so a broken query never threw a 4xx/5xx - it just
 * showed zeros until a real user complained. This guard makes that class of bug
 * impossible to ship silently: it scans every active controller/model for
 * references to init_call.<col> / ic.<col> (where ic is bound to init_call) and
 * fails LOUDLY if any referenced column does not exist in the live schema.
 *
 * GET /api/_schema_guard            -> { ok, mismatches:[...], checked_files, status }
 *   status 200 + ok:true  when zero mismatches
 *   status 500 + ok:false when any mismatch (so the smoke test / CI can gate on it)
 *
 * STRICTLY READ-ONLY. Never writes. Never touches production.
 */
class SchemaGuard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('digest_auth');
    }

    public function index() {
        if (!digest_auth_check($this)) return; // rimlyproof_empty200_20260609: real 401

        $appRoot = APPPATH; // application/
        $tables  = array('init_call');
        $cols    = array();
        foreach ($tables as $t) {
            $cols[$t] = array();
            try {
                $q = $this->db->query("SHOW COLUMNS FROM `$t`");
                foreach (($q ? $q->result() : array()) as $r) {
                    $cols[$t][strtolower($r->Field)] = true;
                }
            } catch (Exception $e) { log_message('error', 'SchemaGuard.php silent_catch: ' . $e->getMessage()); }
        }

        $dirs = array($appRoot . 'controllers', $appRoot . 'models');
        $mismatches  = array();
        $checked     = 0;

        foreach ($dirs as $base) {
            if (!is_dir($base)) continue;
            $rii = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
            );
            foreach ($rii as $f) {
                $p = $f->getPathname();
                if (substr($p, -4) !== '.php') continue;
                if (strpos($p, '.bak') !== false) continue;
                if (basename($p) === 'SchemaGuard.php') continue;
                $src = @file_get_contents($p);
                if ($src === false || strpos($src, 'init_call') === false) continue;
                $checked++;
                $rel = str_replace($appRoot, '', $p);

                // Is the 'ic' alias bound to init_call in this file?
                $icIsInit = (bool) preg_match('/(from|join)\s*\(?\s*[\'"]?init_call(\s+as)?\s+ic\b/i', $src)
                          || (bool) preg_match('/FROM\s+init_call\s+ic\b/i', $src);

                // Direct init_call.<col>
                if (preg_match_all('/\binit_call\.([a-zA-Z_][a-zA-Z0-9_]*)/', $src, $mm)) {
                    foreach (array_unique($mm[1]) as $c) {
                        $lc = strtolower($c);
                        if ($lc === 'dm') continue; // regex fragment of dm_* columns
                        if (!isset($cols['init_call'][$lc])) {
                            $mismatches[] = array('file' => $rel, 'ref' => "init_call.$c");
                        }
                    }
                }
                // ic.<col> only when ic is bound to init_call
                if ($icIsInit && preg_match_all('/\bic\.([a-zA-Z_][a-zA-Z0-9_]*)/', $src, $mm)) {
                    foreach (array_unique($mm[1]) as $c) {
                        $lc = strtolower($c);
                        if (!isset($cols['init_call'][$lc])) {
                            $mismatches[] = array('file' => $rel, 'ref' => "ic.$c");
                        }
                    }
                }
            }
        }

        // de-dup
        $seen = array(); $clean = array();
        foreach ($mismatches as $mz) {
            $k = $mz['file'] . '|' . $mz['ref'];
            if (isset($seen[$k])) continue;
            $seen[$k] = true; $clean[] = $mz;
        }
        usort($clean, function($a,$b){ return strcmp($a['file'].$a['ref'], $b['file'].$b['ref']); });

        $ok   = (count($clean) === 0);
        $code = $ok ? 200 : 500;
        return $this->_json(array(
            'ok'             => $ok,
            'status'         => $ok ? 'PASS' : 'FAIL',
            'mismatch_count' => count($clean),
            'checked_files'  => $checked,
            'mismatches'     => $clean,
            'note'           => $ok
                ? 'All init_call column references resolve against the live schema.'
                : 'Some controllers reference columns that do not exist on init_call. These would silently return zeros to real users. Fix before shipping.',
            'generated_at'   => date('c'),
        ), $code);
    }

    private function _json($payload, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
