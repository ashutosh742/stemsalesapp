<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/BulkImport_api.php
 *
 * Mobile API for bulk CSV / JSON import of companies into company_master.
 *
 * Endpoints (all Bearer protected):
 *   GET  api/import/probe
 *       Returns ok + current company_master row count.
 *
 *   POST api/import/validate
 *       Body: {rows:[{compname,city,state,district,budget,...}]}
 *          OR {csv:"...raw CSV string..."}
 *       Parses, validates (compname required; dedupe by compname+city vs DB).
 *       Returns preview (first 50 rows with per-row status/errors) + dedupe_hits.
 *       NO database write.
 *
 *   POST api/import/commit
 *       Body: {rows:[...], by_uid:<int>}
 *       Inserts valid rows into company_master. Skips invalid/duplicate rows.
 *       Returns {ok, inserted, skipped, skipped_reasons}.
 *
 * company_master writable columns used here:
 *   compname, city, state, district, budget, createddate, draft
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * ASCII only. No em-dashes. Rs not currency symbol.
 * Reads params via $_GET / php://input directly.
 * STAGING ONLY. Additive. Does NOT touch production.
 */
class BulkImport_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        return hash_equals($this->_known_token, $token);
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function _body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $j = json_decode($raw, true);
            if (is_array($j)) return $j;
        }
        return $_POST;
    }

    /**
     * Parse a raw CSV string into an array of associative rows.
     * First row is treated as the header. Returns array of arrays.
     */
    private function _parse_csv($csv_string) {
        $rows = array();
        $lines = preg_split('/\r?\n/', trim($csv_string));
        if (count($lines) < 2) return $rows;

        // Parse header
        $header = str_getcsv($lines[0]);
        $header = array_map('trim', $header);

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ($line === '') continue;
            $vals = str_getcsv($line);
            $row = array();
            foreach ($header as $k => $col) {
                $row[$col] = isset($vals[$k]) ? trim($vals[$k]) : '';
            }
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Validate a single row. Returns array with keys:
     *   valid (bool), errors (array of strings),
     *   compname, city, state, district, budget (cleaned values)
     */
    private function _validate_row($row, $idx) {
        $errors = array();
        $compname = isset($row['compname']) ? trim($row['compname']) : '';
        $city     = isset($row['city'])     ? trim($row['city'])     : '';
        $state    = isset($row['state'])    ? trim($row['state'])    : '';
        $district = isset($row['district']) ? trim($row['district']) : '';
        $budget   = isset($row['budget'])   ? trim($row['budget'])   : '';

        if ($compname === '') {
            $errors[] = 'compname is required';
        }

        return array(
            'row_index' => $idx,
            'valid'     => empty($errors),
            'errors'    => $errors,
            'compname'  => $compname,
            'city'      => $city,
            'state'     => $state,
            'district'  => $district,
            'budget'    => $budget,
        );
    }

    /**
     * Check for duplicates in company_master by compname + city.
     * Accepts array of {compname, city}. Returns set of "compname|city" keys that exist.
     */
    private function _find_dupes($candidates) {
        if (empty($candidates)) return array();

        // Build WHERE clause: (compname=? AND city=?) OR ...
        $clauses = array();
        foreach ($candidates as $c) {
            $cn = $this->db->escape($c['compname']);
            $ct = $this->db->escape($c['city']);
            $clauses[] = "(LOWER(compname) = LOWER($cn) AND LOWER(city) = LOWER($ct))";
        }
        $sql = "SELECT compname, city FROM company_master WHERE " . implode(' OR ', $clauses);
        $res = $this->db->query($sql)->result_array();

        $found = array();
        foreach ($res as $r) {
            $key = strtolower(trim($r['compname'])) . '|' . strtolower(trim($r['city']));
            $found[$key] = true;
        }
        return $found;
    }

    // ----------------------------------------------------------------
    /** GET api/import/probe */
    public function probe() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        $count = (int)$this->db->query("SELECT COUNT(*) c FROM company_master")->row()->c;
        $this->_json(array(
            'ok'            => true,
            'feature'       => 'bulk_import',
            'table'         => 'company_master',
            'company_count' => $count
        ));
    }

    // ----------------------------------------------------------------
    /**
     * POST api/import/validate
     * Accepts JSON {rows:[...]} or {csv:"..."}. NO write.
     */
    public function validate() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);

        $b    = $this->_body();
        $rows = array();

        if (isset($b['csv']) && is_string($b['csv']) && trim($b['csv']) !== '') {
            // Parse raw CSV
            $rows = $this->_parse_csv($b['csv']);
            if (empty($rows)) {
                $this->_json(array('ok' => false, 'error' => 'csv parse failed or empty'), 400);
            }
        } elseif (isset($b['rows']) && is_array($b['rows'])) {
            $rows = $b['rows'];
        } else {
            $this->_json(array('ok' => false, 'error' => 'Provide rows array or csv string'), 400);
        }

        if (empty($rows)) {
            $this->_json(array('ok' => false, 'error' => 'No rows provided'), 400);
        }

        // Validate each row
        $validated    = array();
        $candidates   = array(); // rows that pass basic validation, for dedupe check
        $valid_count  = 0;
        $invalid_count = 0;

        foreach ($rows as $idx => $row) {
            $v = $this->_validate_row($row, $idx);
            $validated[] = $v;
            if ($v['valid']) {
                $valid_count++;
                $candidates[] = array('compname' => $v['compname'], 'city' => $v['city'], '_idx' => $idx);
            } else {
                $invalid_count++;
            }
        }

        // Dedupe check against DB
        $dupe_keys = array();
        $dedupe_hits = array();
        if (!empty($candidates)) {
            $dupe_keys = $this->_find_dupes($candidates);
            foreach ($candidates as $c) {
                $key = strtolower($c['compname']) . '|' . strtolower($c['city']);
                if (isset($dupe_keys[$key])) {
                    $dedupe_hits[] = array(
                        'row_index' => $c['_idx'],
                        'compname'  => $c['compname'],
                        'city'      => $c['city'],
                        'reason'    => 'duplicate in company_master (compname+city match)'
                    );
                    // Mark duplicate in validated set
                    $validated[$c['_idx']]['duplicate'] = true;
                    $validated[$c['_idx']]['errors'][]  = 'duplicate: company already exists in company_master';
                    // Re-count: valid becomes invalid if duplicate
                    $validated[$c['_idx']]['valid'] = false;
                    $valid_count--;
                    $invalid_count++;
                } else {
                    $validated[$c['_idx']]['duplicate'] = false;
                }
            }
        }

        // Preview: first 50 rows with per-row status
        $preview = array_slice($validated, 0, 50);

        $this->_json(array(
            'ok'           => true,
            'total_rows'   => count($rows),
            'valid_count'  => $valid_count,
            'invalid_count'=> $invalid_count,
            'dedupe_hit_count' => count($dedupe_hits),
            'preview'      => $preview,
            'dedupe_hits'  => $dedupe_hits
        ));
    }

    // ----------------------------------------------------------------
    /**
     * POST api/import/commit
     * Accepts {rows:[...], by_uid:<int>}. Inserts valid, non-duplicate rows.
     */
    public function commit() {
        if (!$this->_bearer_ok()) $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);

        $b      = $this->_body();
        $rows   = isset($b['rows']) && is_array($b['rows']) ? $b['rows'] : array();
        $by_uid = isset($b['by_uid']) ? (int)$b['by_uid'] : 0;

        if (empty($rows)) {
            $this->_json(array('ok' => false, 'error' => 'rows array required'), 400);
        }
        if ($by_uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'by_uid required'), 400);
        }

        $inserted        = 0;
        $skipped         = 0;
        $skipped_reasons = array();

        // Validate all rows, then dedupe
        $valid_rows = array();
        foreach ($rows as $idx => $row) {
            $v = $this->_validate_row($row, $idx);
            if (!$v['valid']) {
                $skipped++;
                $skipped_reasons[] = array(
                    'row_index' => $idx,
                    'compname'  => $v['compname'],
                    'reason'    => implode('; ', $v['errors'])
                );
            } else {
                $valid_rows[] = $v;
            }
        }

        // Bulk dedupe check
        if (!empty($valid_rows)) {
            $dupe_keys = $this->_find_dupes($valid_rows);
            $now = date('Y-m-d');

            foreach ($valid_rows as $v) {
                $key = strtolower($v['compname']) . '|' . strtolower($v['city']);
                if (isset($dupe_keys[$key])) {
                    $skipped++;
                    $skipped_reasons[] = array(
                        'row_index' => $v['row_index'],
                        'compname'  => $v['compname'],
                        'reason'    => 'duplicate: company already exists in company_master (compname+city)'
                    );
                    continue;
                }

                // Insert into company_master
                // company_master required columns: compname, createddate, draft, partnerType_id, locations
                $insert = array(
                    'compname'    => $v['compname'],
                    'city'        => $v['city'],
                    'state'       => $v['state'],
                    'district'    => $v['district'],
                    'budget'      => $v['budget'],
                    'createddate' => $now,
                    'draft'       => 0,
                    // Required NOT NULL with no default -- set safe defaults
                    'partnerType_id' => 0,
                    'locations'      => '',
                    'anchor_source'  => 'bulk_import',
                );

                $this->db->insert('company_master', $insert);
                if ($this->db->affected_rows() > 0) {
                    $inserted++;
                } else {
                    $skipped++;
                    $skipped_reasons[] = array(
                        'row_index' => $v['row_index'],
                        'compname'  => $v['compname'],
                        'reason'    => 'db insert failed'
                    );
                }
            }
        }

        $this->_json(array(
            'ok'              => true,
            'inserted'        => $inserted,
            'skipped'         => $skipped,
            'skipped_reasons' => $skipped_reasons
        ));
    }
}
