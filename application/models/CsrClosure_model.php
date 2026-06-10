<?php
/**
 * CsrClosure_model.php
 *
 * Migration 062 - CSR Intelligence Closure
 * Closes gap items S.2 (MCA-21 / csr.gov.in sync) and S.4 (DMFT calendar view).
 *
 * CodeIgniter 3 model. Plain English. No em-dashes. No non-ASCII characters.
 * Rs spelled out. Percent spelled out.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class CsrClosure_model extends CI_Model
{
    // Batch size for CSV bulk inserts
    const BATCH_SIZE = 100;

    // Apollo API environment variable name
    const APOLLO_KEY_ENV = 'APOLLO_API_KEY';

    // Apollo base URL (stub only - real wiring requires provisioned key)
    const APOLLO_BASE_URL = 'https://api.apollo.io/v1/people/match';

    // ---------------------------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ---------------------------------------------------------------------------
    // import_csr_csv
    //
    // Bulk imports a CSV file from csr.gov.in seed format into csr_gov_in_master.
    // Skips rows that violate the (cin, fy_year) unique constraint (upsert by
    // replacing on duplicate key).
    //
    // @param string $csv_path  Absolute path to the seed CSV file
    // @return array            ['imported' => int, 'skipped' => int, 'errors' => array]
    // ---------------------------------------------------------------------------
    public function import_csr_csv($csv_path)
    {
        $result = ['imported' => 0, 'skipped' => 0, 'errors' => []];

        if ( ! file_exists($csv_path) || ! is_readable($csv_path)) {
            $result['errors'][] = 'CSV file not found or not readable: ' . $csv_path;
            return $result;
        }

        $handle = fopen($csv_path, 'r');
        if ($handle === FALSE) {
            $result['errors'][] = 'Failed to open CSV file: ' . $csv_path;
            return $result;
        }

        // Read and validate header row
        $header = fgetcsv($handle);
        $required_cols = [
            'cin', 'company_name', 'fy_year', 'csr_spent_rs_cr',
            'csr_obligation_rs_cr', 'themes', 'state', 'sector', 'source_url'
        ];
        $missing = array_diff($required_cols, $header);
        if ( ! empty($missing)) {
            fclose($handle);
            $result['errors'][] = 'CSV missing required columns: ' . implode(', ', $missing);
            return $result;
        }

        $col_index = array_flip($header);
        $batch     = [];

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($row) < count($required_cols)) {
                $result['skipped']++;
                continue;
            }

            $cin = trim($row[$col_index['cin']]);
            if (empty($cin)) {
                $result['skipped']++;
                continue;
            }

            // Build themes JSON array from semicolon-separated list
            $themes_raw = trim($row[$col_index['themes']]);
            $themes_arr = array_map('trim', explode(';', $themes_raw));
            $themes_arr = array_filter($themes_arr);

            $batch[] = [
                'cin'                       => $cin,
                'company_name'              => trim($row[$col_index['company_name']]),
                'fy_year'                   => trim($row[$col_index['fy_year']]),
                'csr_spent_rs_cr'           => (float) $row[$col_index['csr_spent_rs_cr']],
                'csr_obligation_rs_cr'      => (float) $row[$col_index['csr_obligation_rs_cr']],
                'schedule_vii_themes_json'  => json_encode(array_values($themes_arr)),
                'state'                     => trim($row[$col_index['state']]),
                'sector'                    => trim($row[$col_index['sector']]),
                'source_url'                => trim($row[$col_index['source_url']]),
                'ingested_at'               => date('Y-m-d H:i:s'),
            ];

            if (count($batch) >= self::BATCH_SIZE) {
                $imported = $this->_upsert_csr_batch($batch, $result['errors']);
                $result['imported'] += $imported;
                $batch = [];
            }
        }

        fclose($handle);

        // Flush remaining rows
        if ( ! empty($batch)) {
            $imported = $this->_upsert_csr_batch($batch, $result['errors']);
            $result['imported'] += $imported;
        }

        return $result;
    }

    // ---------------------------------------------------------------------------
    // _upsert_csr_batch  (private helper)
    //
    // Inserts a batch of CSR rows using INSERT ... ON DUPLICATE KEY UPDATE
    // so re-runs do not create duplicate errors; they update the existing row.
    // ---------------------------------------------------------------------------
    private function _upsert_csr_batch(array $rows, array &$errors)
    {
        if (empty($rows)) {
            return 0;
        }

        // Build a multi-row INSERT ... ON DUPLICATE KEY UPDATE statement
        $cols = [
            'cin', 'company_name', 'fy_year', 'csr_spent_rs_cr',
            'csr_obligation_rs_cr', 'schedule_vii_themes_json',
            'state', 'sector', 'source_url', 'ingested_at'
        ];

        $placeholders = [];
        $values       = [];

        foreach ($rows as $r) {
            $row_ph = [];
            foreach ($cols as $col) {
                $row_ph[] = '?';
                $values[] = isset($r[$col]) ? $r[$col] : NULL;
            }
            $placeholders[] = '(' . implode(',', $row_ph) . ')';
        }

        $sql  = 'INSERT INTO `csr_gov_in_master` (`' . implode('`,`', $cols) . '`) VALUES ';
        $sql .= implode(',', $placeholders);
        $sql .= ' ON DUPLICATE KEY UPDATE '
             . '`company_name` = VALUES(`company_name`), '
             . '`csr_spent_rs_cr` = VALUES(`csr_spent_rs_cr`), '
             . '`csr_obligation_rs_cr` = VALUES(`csr_obligation_rs_cr`), '
             . '`schedule_vii_themes_json` = VALUES(`schedule_vii_themes_json`), '
             . '`state` = VALUES(`state`), '
             . '`sector` = VALUES(`sector`), '
             . '`source_url` = VALUES(`source_url`), '
             . '`ingested_at` = VALUES(`ingested_at`)';

        $this->db->query($sql, $values);

        if ($this->db->_error_message()) {
            $errors[] = 'DB error on batch upsert: ' . $this->db->_error_message();
            return 0;
        }

        return count($rows);
    }

    // ---------------------------------------------------------------------------
    // import_dmft_csv
    //
    // Bulk imports a DMFT district tranche CSV into dmft_calendar.
    // Uses INSERT IGNORE on the unique key (district_code, pmkkky_tranche).
    //
    // @param string $csv_path  Absolute path to the DMFT seed CSV
    // @return array            ['imported' => int, 'skipped' => int, 'errors' => array]
    // ---------------------------------------------------------------------------
    public function import_dmft_csv($csv_path)
    {
        $result = ['imported' => 0, 'skipped' => 0, 'errors' => []];

        if ( ! file_exists($csv_path) || ! is_readable($csv_path)) {
            $result['errors'][] = 'DMFT CSV file not found or not readable: ' . $csv_path;
            return $result;
        }

        $handle = fopen($csv_path, 'r');
        if ($handle === FALSE) {
            $result['errors'][] = 'Failed to open DMFT CSV file: ' . $csv_path;
            return $result;
        }

        $header = fgetcsv($handle);
        $required_cols = [
            'district_code', 'district_name', 'state',
            'pmkkky_tranche', 'tranche_amount_rs_cr', 'due_date'
        ];
        $missing = array_diff($required_cols, $header);
        if ( ! empty($missing)) {
            fclose($handle);
            $result['errors'][] = 'DMFT CSV missing required columns: ' . implode(', ', $missing);
            return $result;
        }

        $col_index = array_flip($header);
        $batch     = [];

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($row) < count($required_cols)) {
                $result['skipped']++;
                continue;
            }

            $district_code = trim($row[$col_index['district_code']]);
            if (empty($district_code)) {
                $result['skipped']++;
                continue;
            }

            // Derive initial status from due_date
            $due_date  = trim($row[$col_index['due_date']]);
            $due_ts    = strtotime($due_date);
            $now_ts    = time();
            $days_diff = ($due_ts - $now_ts) / 86400;

            if ($days_diff < 0) {
                $status = 'overdue';
            } elseif ($days_diff <= 30) {
                $status = 'due_soon';
            } else {
                $status = 'scheduled';
            }

            $batch[] = [
                'district_code'        => $district_code,
                'district_name'        => trim($row[$col_index['district_name']]),
                'state'                => trim($row[$col_index['state']]),
                'pmkkky_tranche'       => trim($row[$col_index['pmkkky_tranche']]),
                'tranche_amount_rs_cr' => (float) $row[$col_index['tranche_amount_rs_cr']],
                'due_date'             => $due_date,
                'completed_date'       => NULL,
                'status'               => $status,
            ];

            if (count($batch) >= self::BATCH_SIZE) {
                $imported = $this->_insert_dmft_batch($batch, $result['errors']);
                $result['imported'] += $imported;
                $batch = [];
            }
        }

        fclose($handle);

        if ( ! empty($batch)) {
            $imported = $this->_insert_dmft_batch($batch, $result['errors']);
            $result['imported'] += $imported;
        }

        return $result;
    }

    // ---------------------------------------------------------------------------
    // _insert_dmft_batch  (private helper)
    // ---------------------------------------------------------------------------
    private function _insert_dmft_batch(array $rows, array &$errors)
    {
        if (empty($rows)) {
            return 0;
        }

        $inserted = 0;
        foreach ($rows as $r) {
            // Use INSERT IGNORE to skip duplicates on (district_code, pmkkky_tranche)
            $existing = $this->db
                ->where('district_code', $r['district_code'])
                ->where('pmkkky_tranche', $r['pmkkky_tranche'])
                ->count_all_results('dmft_calendar');

            if ($existing > 0) {
                continue;
            }

            $this->db->insert('dmft_calendar', $r);

            if ($this->db->_error_message()) {
                $errors[] = 'DB insert error for district ' . $r['district_code'] . ': ' . $this->db->_error_message();
            } else {
                $inserted++;
            }
        }

        return $inserted;
    }

    // ---------------------------------------------------------------------------
    // get_csr_for_company
    //
    // Returns all CSR records for a given CIN, ordered by FY year descending.
    // Also returns the latest Apollo sync log entry if one exists.
    //
    // @param string $cin
    // @return array  ['csr_records' => array, 'apollo_log' => array|null]
    // ---------------------------------------------------------------------------
    public function get_csr_for_company($cin)
    {
        $cin = strtoupper(trim($cin));

        $csr_records = $this->db
            ->where('cin', $cin)
            ->order_by('fy_year', 'DESC')
            ->get('csr_gov_in_master')
            ->result_array();

        // Decode JSON themes for each record
        foreach ($csr_records as &$rec) {
            if ( ! empty($rec['schedule_vii_themes_json'])) {
                $rec['themes'] = json_decode($rec['schedule_vii_themes_json'], TRUE);
            } else {
                $rec['themes'] = [];
            }
            unset($rec['schedule_vii_themes_json']);
        }
        unset($rec);

        // Fetch most recent Apollo sync log for this CIN
        $apollo_log = $this->db
            ->where('cin', $cin)
            ->order_by('fetched_at', 'DESC')
            ->limit(1)
            ->get('apollo_sync_log')
            ->row_array();

        if ($apollo_log && ! empty($apollo_log['apollo_payload_json'])) {
            $apollo_log['payload'] = json_decode($apollo_log['apollo_payload_json'], TRUE);
            unset($apollo_log['apollo_payload_json']);
        }

        return [
            'cin'         => $cin,
            'csr_records' => $csr_records,
            'apollo_log'  => $apollo_log ?: NULL,
        ];
    }

    // ---------------------------------------------------------------------------
    // get_dmft_calendar
    //
    // Returns DMFT tranche records, optionally filtered by state and/or
    // due within a given number of days from today.
    //
    // @param string|null $state        Filter by state name (NULL = all states)
    // @param int         $within_days  Include only tranches due within this many days
    // @return array
    // ---------------------------------------------------------------------------
    public function get_dmft_calendar($state = NULL, $within_days = 180)
    {
        $this->db->where('completed_date IS NULL', NULL, FALSE);

        if ( ! empty($state)) {
            $this->db->where('state', $state);
        }

        if ($within_days > 0) {
            $cutoff = date('Y-m-d', strtotime('+' . (int) $within_days . ' days'));
            $this->db->where('due_date <=', $cutoff);
        }

        $rows = $this->db
            ->order_by('due_date', 'ASC')
            ->get('dmft_calendar')
            ->result_array();

        // Enrich each row with computed fields
        $today = strtotime(date('Y-m-d'));
        foreach ($rows as &$row) {
            $due_ts            = strtotime($row['due_date']);
            $days_until_due    = (int) round(($due_ts - $today) / 86400);
            $row['days_until_due'] = $days_until_due;

            // Recalculate status dynamically so it reflects current date
            if ($days_until_due < 0) {
                $row['display_status'] = 'overdue';
            } elseif ($days_until_due <= 30) {
                $row['display_status'] = 'due_soon';
            } else {
                $row['display_status'] = 'scheduled';
            }
        }
        unset($row);

        return $rows;
    }

    // ---------------------------------------------------------------------------
    // apollo_lookup_stub
    //
    // Placeholder for Apollo.io API integration. When APOLLO_API_KEY environment
    // variable is empty, this method logs a zero-status entry and returns NULL.
    // When the key is provisioned, it performs the API call, stores the payload,
    // and returns the decoded response.
    //
    // Provision the key by placing a marker at:
    //   /home/user/workspace/seeds/apollo_key.done
    // and setting the APOLLO_API_KEY environment variable on the server.
    //
    // @param string $cin
    // @return array|null  Apollo response payload, or NULL if key not provisioned
    // ---------------------------------------------------------------------------
    public function apollo_lookup_stub($cin)
    {
        $cin = strtoupper(trim($cin));
        $api_key = getenv(self::APOLLO_KEY_ENV);

        if (empty($api_key)) {
            // Log a zero-status entry to indicate the lookup was not attempted
            $log_row = [
                'cin'                => $cin,
                'apollo_payload_json'=> json_encode(['note' => 'APOLLO_API_KEY not provisioned']),
                'fetched_at'         => date('Y-m-d H:i:s'),
                'http_status'        => 0,
            ];
            $this->db->insert('apollo_sync_log', $log_row);
            return NULL;
        }

        // --- Begin Apollo API call ---
        // We map CIN to a company domain lookup. In a real wiring, the caller
        // would pass a company domain or email. This stub uses cin as a
        // query identifier in the request body.
        $payload = [
            'api_key'          => $api_key,
            'name'             => '',
            'organization_name'=> '',
            'reveal_personal_emails' => FALSE,
        ];

        // Look up the company name from our master table first
        $company = $this->db
            ->select('company_name, state, sector')
            ->where('cin', $cin)
            ->limit(1)
            ->get('csr_gov_in_master')
            ->row_array();

        if ( ! empty($company)) {
            $payload['organization_name'] = $company['company_name'];
        }

        $ch = curl_init(self::APOLLO_BASE_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response    = curl_exec($ch);
        $http_status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error  = curl_error($ch);
        curl_close($ch);

        $decoded = NULL;
        if ($response !== FALSE && $http_status === 200) {
            $decoded = json_decode($response, TRUE);
        }

        $log_row = [
            'cin'                => $cin,
            'apollo_payload_json'=> $response !== FALSE ? $response : json_encode(['curl_error' => $curl_error]),
            'fetched_at'         => date('Y-m-d H:i:s'),
            'http_status'        => $http_status,
        ];
        $this->db->insert('apollo_sync_log', $log_row);

        return $decoded;
    }

    // ---------------------------------------------------------------------------
    // sync_csr_weekly
    //
    // Cron entry point. Intended to be called by a scheduled task (e.g. weekly).
    // Currently performs the following steps:
    //   1. Re-imports the seed CSV from the workspace seeds directory.
    //   2. Runs Apollo stub lookups for any CINs not looked up in the last 7 days.
    //   3. Returns a summary report array.
    //
    // Set up the cron job as:
    //   0 2 * * 1  curl -X POST https://yourapp.com/api/csr_closure/import_csr
    //
    // @return array  Summary of actions taken
    // ---------------------------------------------------------------------------
    public function sync_csr_weekly()
    {
        $report = [
            'run_at'           => date('Y-m-d H:i:s'),
            'csv_import'       => NULL,
            'apollo_synced'    => 0,
            'apollo_skipped'   => 0,
            'errors'           => [],
        ];

        // Step 1: Re-import CSR seed CSV
        $csv_path = FCPATH . 'seeds/csr_gov_in_fy25_fy26_seed.csv';
        if (file_exists($csv_path)) {
            $report['csv_import'] = $this->import_csr_csv($csv_path);
        } else {
            $report['errors'][] = 'Seed CSV not found at ' . $csv_path;
        }

        // Step 2: Apollo stub lookups for CINs not synced in last 7 days
        $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));

        $all_cins = $this->db
            ->select('DISTINCT cin')
            ->get('csr_gov_in_master')
            ->result_array();

        foreach ($all_cins as $row) {
            $cin = $row['cin'];

            // Skip if recently looked up
            $recent = $this->db
                ->where('cin', $cin)
                ->where('fetched_at >=', $seven_days_ago)
                ->where('http_status !=', 0)
                ->count_all_results('apollo_sync_log');

            if ($recent > 0) {
                $report['apollo_skipped']++;
                continue;
            }

            $this->apollo_lookup_stub($cin);
            $report['apollo_synced']++;

            // Rate-limit: avoid hitting API too fast
            usleep(200000); // 0.2 seconds between calls
        }

        return $report;
    }
}
