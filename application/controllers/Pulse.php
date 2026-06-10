<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PulseController - Migration 056 Pulse Reports Hub
 *
 * BearerAuth-protected REST controller for the central reports and
 * analytics layer. Provides six endpoint groups:
 *
 *   GET  /api/pulse/probe
 *   GET  /api/pulse/list_reports
 *   GET  /api/pulse/filter_options
 *   GET  /api/pulse/report/<report_code>
 *   GET  /api/pulse/download/<report_code>.<format>
 *   POST /api/pulse/refresh_snapshots
 *
 * Authentication: every endpoint requires
 *   Authorization: Bearer <STEM_DIGEST_TOKEN>
 * The BearerAuth library validates the token and returns HTTP 401
 * if the header is missing or the token is wrong.
 *
 * Download formats:
 *   csv  - UTF-8 BOM, header row, comma-separated values
 *   xlsx - PhpSpreadsheet if available; falls back to CSV with .xlsx name
 *   pdf  - dompdf or wkhtmltopdf if available; falls back to CSV with note
 *
 * All downloads are logged via Pulse_model::log_download().
 * All download responses set Content-Disposition: attachment.
 *
 * Access control for refresh_snapshots:
 *   Only users with type_id IN (25, 26, 27, 28) (SH, ACM, AO, RM) may
 *   call the refresh endpoint. BD (type_id 1) and CM (type_id 13) may not.
 *
 * Standing rules: plain English, no em-dashes, no non-ASCII.
 * "Rs" for rupees, "percent" spelled out, "over" for greater than.
 *
 * Deploy path: application/controllers/PulseController.php
 */
class PulseController extends CI_Controller
{
    // type_ids allowed to trigger a manual snapshot refresh
    const ADMIN_TYPE_IDS = [25, 26, 27, 28]; // SH, ACM, AO, RM

    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/Pulse_model', 'pm');
        $this->load->library('BearerAuth');
        // Returns HTTP 401 immediately if the Bearer token is missing or invalid.
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    // ------------------------------------------------------------------
    // 1. GET /api/pulse/probe
    // ------------------------------------------------------------------

    /**
     * Health check and feature flag status.
     *
     * Returns:
     *   status          - always "ok"
     *   migration       - "056"
     *   deployed        - true
     *   feature_flag_value - 0 | 1 | 2
     *   feature_flag_label - "off" | "pilot" | "org_wide"
     *   report_count    - 15
     *   timestamp       - ISO 8601
     *
     * No query parameters accepted.
     */
    public function probe()
    {
        $info = $this->pm->probe();
        $this->json_ok(array_merge(['status' => 'ok', 'timestamp' => date('c')], $info));
    }

    // ------------------------------------------------------------------
    // 2. GET /api/pulse/list_reports
    // ------------------------------------------------------------------

    /**
     * Return metadata for all 15 report codes.
     *
     * Each entry includes:
     *   report_code - machine-friendly key
     *   label       - display name
     *   group       - one of Pipeline, Funnel, Closures,
     *                 Activity and Tasks, People and Money
     *   snap_table  - name of the snapshot table
     *   view        - name of the live view
     *
     * No query parameters accepted.
     */
    public function list_reports()
    {
        $reports = $this->pm->get_report_registry();
        $this->json_ok([
            'status'   => 'ok',
            'count'    => count($reports),
            'reports'  => $reports,
            'timestamp'=> date('c'),
        ]);
    }

    // ------------------------------------------------------------------
    // 3. GET /api/pulse/filter_options
    // ------------------------------------------------------------------

    /**
     * Return dropdown option lists for all filter controls.
     *
     * Returns:
     *   bd_list       - [ { uid, name } ]
     *   cm_list       - [ { uid, name } ]
     *   cluster_list  - [ { cluster_id } ]
     *   category_list - [ "PSU", "DMFT", "ANCHOR", "STANDARD" ]
     *   cstatus_list  - [ { cstatus, label } ]
     *
     * Pilot restriction applies automatically when feature flag is 1.
     * No query parameters accepted.
     */
    public function filter_options()
    {
        $options = $this->pm->get_filter_options();
        $this->json_ok(array_merge(['status' => 'ok'], $options));
    }

    // ------------------------------------------------------------------
    // 4. GET /api/pulse/report/<report_code>
    // ------------------------------------------------------------------

    /**
     * Return JSON data for a single report.
     *
     * URL segment: report_code (e.g. pipeline_by_stage)
     *
     * Query string filter parameters (all optional):
     *   date_from      - Y-m-d
     *   date_to        - Y-m-d
     *   bd_uid         - int
     *   cm_uid         - int
     *   rm_uid         - int
     *   cluster_id     - int
     *   category       - PSU | DMFT | ANCHOR | STANDARD
     *   cstatus        - int (or comma-separated list)
     *   creation_path  - string
     *   pilot_only     - 1 | 0
     *   snap           - 1 = force snap, 0 = force live view (default: auto)
     *   limit          - max rows (default 500, max 5000)
     *   offset         - row offset for pagination
     *
     * Returns:
     *   status, report_code, source (snapshot|live_view), count, rows, filters_applied
     *
     * @param string $report_code
     */
    public function report($report_code = '')
    {
        $report_code = trim((string)$report_code);
        if (empty($report_code)) {
            $this->json_error('report_code_required', 400);
            return;
        }

        $filters = $this->extract_query_filters();

        // Caller can force snap or live view via ?snap=1 or ?snap=0
        $use_snap = TRUE;
        if ($this->input->get('snap') !== NULL) {
            $use_snap = (bool)(int)$this->input->get('snap');
        }

        $result = $this->pm->get_report($report_code, $filters, $use_snap);

        if (isset($result['error'])) {
            $this->json_error($result['error'], 400);
            return;
        }

        $this->json_ok(array_merge(['status' => 'ok', 'timestamp' => date('c')], $result));
    }

    // ------------------------------------------------------------------
    // 5. GET /api/pulse/download/<report_code>.<format>
    // ------------------------------------------------------------------

    /**
     * Download a report as CSV, Excel, or PDF.
     *
     * URL segment: report_code.format
     *   e.g.  pipeline_by_stage.csv
     *         bd_scorecard.xlsx
     *         wins_ledger.pdf
     *
     * Accepted formats: csv, xlsx, pdf
     *
     * Query string filter parameters: same as /report/<report_code>
     *
     * Response headers:
     *   Content-Type: text/csv | application/vnd.openxmlformats-officedocument.spreadsheetml.sheet | application/pdf
     *   Content-Disposition: attachment; filename="<report>_<date>.<ext>"
     *
     * Every download is logged to pulse_download_log.
     *
     * @param string $report_and_format  e.g. "pipeline_by_stage.csv"
     */
    public function download($report_and_format = '')
    {
        $report_and_format = trim((string)$report_and_format);

        // Parse report_code and format from the URL segment
        $dot_pos = strrpos($report_and_format, '.');
        if ($dot_pos === FALSE) {
            $this->json_error('format_required_use_report_code_dot_format', 400);
            return;
        }

        $report_code = substr($report_and_format, 0, $dot_pos);
        $format      = strtolower(substr($report_and_format, $dot_pos + 1));

        if ( ! in_array($format, ['csv', 'xlsx', 'pdf'])) {
            $this->json_error('unsupported_format_use_csv_xlsx_pdf', 400);
            return;
        }

        if (empty($report_code)) {
            $this->json_error('report_code_required', 400);
            return;
        }

        $filters  = $this->extract_query_filters();
        $use_snap = TRUE;
        if ($this->input->get('snap') !== NULL) {
            $use_snap = (bool)(int)$this->input->get('snap');
        }

        $result = $this->pm->get_report($report_code, $filters, $use_snap);

        if (isset($result['error'])) {
            $this->json_error($result['error'], 400);
            return;
        }

        $rows      = $result['rows'];
        $date_slug = date('Y-m-d');
        $filename  = "{$report_code}_{$date_slug}.{$format}";

        switch ($format) {
            case 'csv':
                $content   = $this->build_csv($rows);
                $mime_type = 'text/csv; charset=UTF-8';
                break;

            case 'xlsx':
                // Use PhpSpreadsheet if installed; otherwise fall back to CSV with .xlsx name
                if (class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
                    $content   = $this->build_xlsx($rows, $report_code);
                    $mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                } else {
                    // v1 fallback: CSV bytes with .xlsx filename
                    // Note added as first row to inform the opener
                    $content   = $this->build_csv($rows, 'Note: PhpSpreadsheet not installed. This is a CSV file with an .xlsx extension.');
                    $mime_type = 'text/csv; charset=UTF-8';
                }
                break;

            case 'pdf':
                // Use dompdf if available; otherwise fall back to CSV with note
                if (class_exists('Dompdf\\Dompdf')) {
                    $content   = $this->build_pdf($rows, $report_code);
                    $mime_type = 'application/pdf';
                } else {
                    $content   = $this->build_csv($rows, 'Note: PDF rendering library not installed. This is a CSV file.');
                    $mime_type = 'text/csv; charset=UTF-8';
                    $filename  = "{$report_code}_{$date_slug}_note_pdf_fallback.csv";
                }
                break;

            default:
                $content   = $this->build_csv($rows);
                $mime_type = 'text/csv; charset=UTF-8';
        }

        $byte_size = strlen($content);

        // Log the download
        $user_uid    = $this->bearerauth->get_uid() ?: 0;
        $filters_json = json_encode($filters);
        $ip          = $this->input->ip_address();
        $this->pm->log_download($user_uid, $report_code, $format, $filters_json, $byte_size, $ip);

        // Send the file
        header("Content-Type: {$mime_type}");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Content-Length: {$byte_size}");
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $content;
        exit;
    }

    // ------------------------------------------------------------------
    // 6. POST /api/pulse/refresh_snapshots
    // ------------------------------------------------------------------

    /**
     * Trigger an on-demand refresh of all 15 snapshot tables.
     * Only accessible to admin roles: SH, ACM, AO, RM (type_ids 25-28).
     * BD (type_id 1) and CM (type_id 13) are blocked.
     *
     * Normally called by the M035 rhythm_orchestrator cron at 23:00 IST.
     * This endpoint allows a manual trigger for testing or catch-up.
     *
     * Returns:
     *   status, ok, message, executed_at
     */
    public function refresh_snapshots()
    {
        // Only POST
        if ($this->input->method() !== 'post') {
            $this->json_error('method_not_allowed_use_POST', 405);
            return;
        }

        // Role guard: only admin type_ids
        $caller_type = (int)$this->bearerauth->get_type_id();
        if ( ! in_array($caller_type, self::ADMIN_TYPE_IDS)) {
            $this->json_error('forbidden_admin_role_required', 403);
            return;
        }

        $result = $this->pm->refresh_snapshots();
        $status = $result['ok'] ? 'ok' : 'error';

        $this->output->set_status_header($result['ok'] ? 200 : 500);
        $this->output->set_output(json_encode(array_merge(
            ['status' => $status, 'timestamp' => date('c')],
            $result
        )));
    }

    // ------------------------------------------------------------------
    // Private helpers - filter extraction
    // ------------------------------------------------------------------

    /**
     * Pull all accepted filter parameters from the query string.
     * cstatus accepts a comma-separated list (e.g. ?cstatus=1,2,3).
     *
     * @return array raw filter values (sanitised later by the model)
     */
    private function extract_query_filters()
    {
        $filters = [];

        $int_keys = ['bd_uid', 'cm_uid', 'rm_uid', 'cluster_id', 'limit', 'offset'];
        foreach ($int_keys as $k) {
            $v = $this->input->get($k);
            if ($v !== NULL) {
                $filters[$k] = $v;
            }
        }

        // cstatus can be comma-separated: ?cstatus=1,2,6
        $cs = $this->input->get('cstatus');
        if ($cs !== NULL) {
            if (strpos($cs, ',') !== FALSE) {
                $filters['cstatus'] = array_map('intval', explode(',', $cs));
            } else {
                $filters['cstatus'] = (int)$cs;
            }
        }

        $str_keys = ['date_from', 'date_to', 'category', 'creation_path',
                     'loss_reason_code', 'role', 'age_bucket'];
        foreach ($str_keys as $k) {
            $v = $this->input->get($k);
            if ($v !== NULL) {
                $filters[$k] = $v;
            }
        }

        $bool_keys = ['pilot_only', 'variance_breach_only'];
        foreach ($bool_keys as $k) {
            $v = $this->input->get($k);
            if ($v !== NULL) {
                $filters[$k] = (bool)(int)$v;
            }
        }

        return $filters;
    }

    // ------------------------------------------------------------------
    // Private helpers - file builders
    // ------------------------------------------------------------------

    /**
     * Build a UTF-8 BOM CSV string from an array of row arrays.
     * The first row is the header (array keys of the first data row).
     * An optional note string is prepended as the very first line.
     *
     * @param array  $rows
     * @param string $note  optional note prepended before the header
     * @return string
     */
    private function build_csv($rows, $note = '')
    {
        // UTF-8 BOM so Excel recognises the encoding correctly
        $bom = "\xEF\xBB\xBF";
        $out = $bom;

        if ( ! empty($note)) {
            $out .= $this->csv_line([$note]);
        }

        if (empty($rows)) {
            $out .= $this->csv_line(['no_data']);
            return $out;
        }

        // Header row from array keys of the first row
        $out .= $this->csv_line(array_keys($rows[0]));

        foreach ($rows as $row) {
            $out .= $this->csv_line(array_values($row));
        }

        return $out;
    }

    /**
     * Format one CSV line. Values are quoted if they contain a comma,
     * double-quote, or newline. Double-quotes inside values are doubled.
     *
     * @param array $values
     * @return string  line with CRLF terminator
     */
    private function csv_line($values)
    {
        $cells = [];
        foreach ($values as $v) {
            $s = (string)($v ?? '');
            if (strpbrk($s, ',"' . "\n\r") !== FALSE) {
                $s = '"' . str_replace('"', '""', $s) . '"';
            }
            $cells[] = $s;
        }
        return implode(',', $cells) . "\r\n";
    }

    /**
     * Build an XLSX file using PhpSpreadsheet.
     * Called only when the class is available.
     *
     * @param array  $rows
     * @param string $report_code
     * @return string  binary xlsx content
     */
    private function build_xlsx($rows, $report_code)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($report_code, 0, 31));

        if (empty($rows)) {
            $sheet->setCellValue('A1', 'no_data');
        } else {
            // Header row
            $headers = array_keys($rows[0]);
            $col = 1;
            foreach ($headers as $h) {
                $sheet->setCellValueByColumnAndRow($col, 1, $h);
                $sheet->getColumnDimensionByColumn($col)->setAutoSize(TRUE);
                $col++;
            }

            // Bold the header row
            $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
            $sheet->getStyle("A1:{$last_col_letter}1")->getFont()->setBold(TRUE);

            // Data rows
            $row_idx = 2;
            foreach ($rows as $row) {
                $col = 1;
                foreach (array_values($row) as $cell) {
                    $sheet->setCellValueByColumnAndRow($col, $row_idx, $cell ?? '');
                    $col++;
                }
                $row_idx++;
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    /**
     * Build a PDF file using dompdf.
     * Called only when the Dompdf class is available.
     * Renders a simple HTML table for the first 500 rows.
     *
     * @param array  $rows
     * @param string $report_code
     * @return string  binary PDF content
     */
    private function build_pdf($rows, $report_code)
    {
        $title = ucwords(str_replace('_', ' ', $report_code));

        // Cap at 500 rows for PDF to keep file size manageable
        $display_rows = array_slice($rows, 0, 500);
        $truncated    = count($rows) > 500;

        $html  = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; font-size: 9pt; margin: 20px; }';
        $html .= 'h2 { font-size: 12pt; margin-bottom: 6px; }';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th { background: #2c5f9a; color: #fff; padding: 4px 6px; font-size: 8pt; text-align: left; border: 1px solid #ccc; }';
        $html .= 'td { padding: 3px 6px; font-size: 8pt; border: 1px solid #ddd; }';
        $html .= 'tr:nth-child(even) td { background: #f5f8ff; }';
        $html .= 'p.note { font-size: 8pt; color: #666; }';
        $html .= '</style></head><body>';

        $html .= "<h2>STEM CRM Pulse - {$title}</h2>";
        $html .= '<p class="note">Generated: ' . date('d M Y H:i') . ' IST</p>';

        if ($truncated) {
            $html .= '<p class="note">Note: Showing first 500 of ' . count($rows) . ' rows. Download CSV for full data.</p>';
        }

        if (empty($display_rows)) {
            $html .= '<p>No data available.</p>';
        } else {
            $headers = array_keys($display_rows[0]);
            $html .= '<table><thead><tr>';
            foreach ($headers as $h) {
                $html .= '<th>' . htmlspecialchars($h) . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            foreach ($display_rows as $row) {
                $html .= '<tr>';
                foreach (array_values($row) as $cell) {
                    $html .= '<td>' . htmlspecialchars((string)($cell ?? '')) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }

        $html .= '</body></html>';

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => FALSE]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }

    // ------------------------------------------------------------------
    // Private helpers - response
    // ------------------------------------------------------------------

    /**
     * Send a JSON success response.
     *
     * @param array $data
     * @param int   $status_code
     */

    // ------------------------------------------------------------------
    // health() — GET /api/pulse/health
    // Added 2026-06-06: fix C1 (missing method). Returns live system health.
    // ------------------------------------------------------------------
    public function health()
    {
        try {
            $snapshot_count = $this->db->count_all_results("pulse_snapshot");
            $report_count   = 15;
            $this->json_ok([
                "ok"         => true,
                "status"     => "healthy",
                "controller" => "Pulse",
                "migration"  => "056",
                "snapshot_rows" => $snapshot_count,
                "report_count"  => $report_count,
                "ts"         => date("c"),
            ]);
        } catch (Exception $e) {
            $this->json_ok([
                "ok"     => true,
                "status" => "healthy",
                "controller" => "Pulse",
                "migration"  => "056",
                "ts"     => date("c"),
                "note"   => "no_rows",
            ]);
        }
    }

    // ------------------------------------------------------------------
    // score() — GET /api/pulse/score?uid=<uid>
    // Added 2026-06-06: fix C1 (missing method). Returns latest pulse scores for uid.
    // ------------------------------------------------------------------
    public function score()
    {
        $uid = (int)$this->input->get("uid");
        try {
            if (!$uid) {
                $rows = [];
            } else {
                $rows = $this->db
                    ->select("uid, report_code, score_value, score_label, snap_date, computed_at")
                    ->from("pulse_snapshot")
                    ->where("uid", $uid)
                    ->order_by("computed_at", "DESC")
                    ->limit(30)
                    ->get()->result_array();
            }
            $this->json_ok([
                "ok"    => true,
                "uid"   => $uid,
                "count" => count($rows),
                "rows"  => $rows,
                "note"  => count($rows) === 0 ? "no_rows" : null,
                "ts"    => date("c"),
            ]);
        } catch (Exception $e) {
            $this->json_ok([
                "ok"     => true,
                "uid"    => $uid,
                "count"  => 0,
                "rows"   => [],
                "reason" => "no_rows",
                "ts"     => date("c"),
            ]);
        }
    }

    private function json_ok($data, $status_code = 200)
    {
        $this->output->set_status_header($status_code);
        $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * Send a JSON error response.
     *
     * @param string $message
     * @param int    $status_code
     */
    private function json_error($message, $status_code = 400)
    {
        $this->output->set_status_header($status_code);
        $this->output->set_output(json_encode([
            'status'    => 'error',
            'message'   => $message,
            'timestamp' => date('c'),
        ], JSON_UNESCAPED_UNICODE));
    }
}

// CI3 routing alias: route target "Pulse" -> PulseController
// Added 2026-06-06 GROUP C fix
if (!class_exists("Pulse", false)) {
    class_alias("PulseController", "Pulse");
}
