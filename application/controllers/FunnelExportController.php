<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FunnelExportController
 *
 * Migration 075 - Card 3 Export Sibling (Agent D)
 *
 * Provides three export endpoints that re-query the same SQL used by
 * FunnelReportController (Agent B owns that file - DO NOT EDIT IT).
 * Exports live in this sibling controller to avoid file-edit collisions.
 *
 * Auth: Bearer STEM_DIGEST_TOKEN header required on all endpoints except probe.
 *
 * Routes (added to application/config/routes_mobile_pilot.php):
 *   $route['api/funnel_export/probe'] = 'FunnelExportController/probe';
 *   $route['api/funnel_export/xlsx']  = 'FunnelExportController/export_xlsx';
 *   $route['api/funnel_export/pdf']   = 'FunnelExportController/export_pdf';
 *   $route['api/funnel_export/email'] = 'FunnelExportController/email_report';
 *
 * Libraries used (all pre-existing on staging):
 *   - application/libraries/PHPExcel.php  (PHPExcel class)
 *   - application/third_party/dompdf/vendor/autoload.php  (Dompdf\Dompdf)
 *   - application/libraries/Crm_emailer.php  (Crm_emailer::send_and_log)
 *
 * New table: funnel_export_log (created by Agent A, migration 075)
 *
 * Standing rules: plain English, no em-dashes, no non-ASCII.
 * "Rs" for rupees. Do NOT touch production stemapp.in.
 */
class FunnelExportController extends CI_Controller
{
    const MIGRATION = '075';
    const DOMPDF_AUTOLOAD = APPPATH . '../application/third_party/dompdf/vendor/autoload.php';
    const PHPEXCEL_PATH   = APPPATH . 'libraries/PHPExcel.php';

    // -----------------------------------------------------------------------
    // Constructor - mirrors FunnelReportController verbatim
    // -----------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->config->load('rest',   true, true);
        $this->config->load('custom', true, true);
        header('Content-Type: application/json; charset=utf-8');
    }

    // -----------------------------------------------------------------------
    // Auth guard - Bearer token or active session
    // Copied verbatim from FunnelReportController (lines 30-145)
    // -----------------------------------------------------------------------
    private $_authed_uid = 0;

    private function _jwt_token_valid($token)
    {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k])  && (int)$_GET[$k]  > 0) $candidates[(int)$_GET[$k]]  = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _auth_or_die()
    {
        $hdr = $this->input->get_request_header('Authorization', true);
        if (empty($hdr) && function_exists('apache_request_headers')) {
            $hdrs = apache_request_headers();
            if (isset($hdrs['Authorization']))  { $hdr = $hdrs['Authorization']; }
            elseif (isset($hdrs['authorization'])) { $hdr = $hdrs['authorization']; }
        }
        if (empty($hdr) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        }

        $expected = getenv('STEM_DIGEST_TOKEN');
        if (empty($expected)) $expected = $this->config->item('stem_digest_token');
        if (empty($expected)) $expected = $this->config->item('STEM_DIGEST_TOKEN');
        if (empty($expected)) $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

        if (!empty($hdr) && $hdr === 'Bearer ' . $expected) {
            return true;
        }
        if (!empty($hdr) && stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) { $this->_authed_uid = $uid; return true; }
        }

        $session_uid = $this->session->userdata('user_id');
        if ((int)$session_uid > 0) {
            return true;
        }

        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized', 'hdr_received' => !empty($hdr)));
        exit;
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_export/probe
    // Returns library availability booleans for smoke-testing.
    // -----------------------------------------------------------------------
    public function probe()
    {
        $dompdf_file   = APPPATH . '../application/third_party/dompdf/vendor/autoload.php';
        $phpexcel_file = APPPATH . 'libraries/PHPExcel.php';
        $phpss_avail   = class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet');
        $crm_email_file= APPPATH . 'libraries/Crm_emailer.php';

        // Load dompdf to test class availability
        // Note: PHPExcel.php file exists but is PHP 8.x incompatible (fatal in ReferenceHelper.php)
        // so we do NOT load it here. xlsx uses CSV fallback on this PHP 8.2 server.
        if (file_exists($dompdf_file) && !class_exists('Dompdf\Dompdf')) {
            @require_once($dompdf_file);
        }

        echo json_encode(array(
            'ok'         => true,
            'controller' => 'FunnelExportController',
            'migration'  => self::MIGRATION,
            'ts'         => date('Y-m-d H:i:s'),
            'libs'       => array(
                'dompdf'         => class_exists('Dompdf\Dompdf'),
                'phpexcel_file'  => file_exists($phpexcel_file),
                'phpspreadsheet' => $phpss_avail,
                'xlsx_mode'      => $phpss_avail ? 'PhpSpreadsheet' : 'csv_fallback',
                'crm_emailer'    => file_exists($crm_email_file),
            ),
        ));
    }

    // -----------------------------------------------------------------------
    // Private: fetch_rows($endpoint, $filters)
    // Re-queries the same SQL patterns as FunnelReportController.
    // $endpoint: stuck_status | companies_without_dm | closing_timeline |
    //            funnel_transfer | created_between | deleted_between
    // $filters:  array with keys: uid, days, sdate, edate
    // -----------------------------------------------------------------------
    private function fetch_rows($endpoint, $filters)
    {
        $uid   = isset($filters['uid'])   ? (int)$filters['uid']  : 0;
        $days  = isset($filters['days'])  ? max(1, (int)$filters['days']) : 14;
        $sdate = isset($filters['sdate']) ? $filters['sdate'] : date('Y-m-01');
        $edate = isset($filters['edate']) ? $filters['edate'] : date('Y-m-d');

        switch ($endpoint) {

            case 'stuck_status':
                $sql = "
                    SELECT
                        ic.id          AS lead_id,
                        cm.compname    AS company_name,
                        ic.cstatus,
                        s.name         AS cstatus_name,
                        COALESCE(
                            DATEDIFF(NOW(), MAX(fcl.created_at)),
                            DATEDIFF(NOW(), ic.updated_at)
                        )              AS days_in_stage,
                        DATEDIFF(NOW(), ic.updated_at) AS slip_days,
                        DATEDIFF(NOW(), ic.updated_at) AS stagnant_days,
                        ic.fbudget,
                        ic.proposaldate,
                        u.name         AS bd_name,
                        ic.updated_at  AS last_activity
                    FROM init_call ic
                    LEFT JOIN company_master cm  ON cm.id  = ic.cmpid_id
                    LEFT JOIN status s           ON s.id   = ic.cstatus
                    LEFT JOIN funnel_change_log fcl ON fcl.cid_id = ic.id
                    LEFT JOIN user u             ON u.uid  = ic.mainbd
                    WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
                      AND ic.cstatus NOT IN (13, 14)
                    GROUP BY ic.id, cm.compname, ic.cstatus, s.name, ic.updated_at,
                             ic.fbudget, ic.proposaldate, u.name
                    HAVING days_in_stage >= ?
                    ORDER BY days_in_stage DESC
                    LIMIT 200
                ";
                return $this->db->query($sql, array($uid, $uid, $uid, $days))->result_array();

            case 'companies_without_dm':
                $sql = "
                    SELECT
                        ic.id          AS lead_id,
                        cm.compname    AS company_name,
                        ic.cstatus,
                        s.name         AS cstatus_name,
                        ic.fbudget,
                        ic.proposaldate,
                        u.name         AS bd_name,
                        COUNT(ccm.id)  AS contact_count,
                        DATEDIFF(NOW(), ic.updated_at) AS stagnant_days
                    FROM init_call ic
                    LEFT JOIN company_master cm       ON cm.id  = ic.cmpid_id
                    LEFT JOIN status s                ON s.id   = ic.cstatus
                    LEFT JOIN user u                  ON u.uid  = ic.mainbd
                    LEFT JOIN company_contact_master ccm
                           ON ccm.company_id = ic.cmpid_id
                          AND ccm.phoneno IS NOT NULL
                          AND ccm.phoneno != ''
                    WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
                      AND ic.cstatus NOT IN (13, 14)
                      AND (
                            (ic.dm_contact_phone IS NULL OR ic.dm_contact_phone = '')
                            AND (ic.dm_contact_email IS NULL OR ic.dm_contact_email = '')
                      )
                    GROUP BY ic.id, cm.compname, ic.cstatus, s.name,
                             ic.fbudget, ic.proposaldate, u.name, ic.updated_at
                    HAVING contact_count = 0
                    ORDER BY ic.createDate DESC
                    LIMIT 200
                ";
                return $this->db->query($sql, array($uid, $uid, $uid))->result_array();

            case 'closing_timeline':
                $sql = "
                    SELECT
                        ic.id               AS lead_id,
                        cm.compname         AS company_name,
                        ic.cstatus,
                        s.name              AS cstatus_name,
                        ic.proposaldate,
                        ic.fbudget,
                        ic.fbudget_min,
                        ic.fbudget_max,
                        u.name              AS bd_name,
                        DATEDIFF(NOW(), ic.updated_at) AS days_in_stage,
                        CASE
                            WHEN ic.proposaldate < CURDATE() AND ic.cstatus < 12
                            THEN DATEDIFF(CURDATE(), ic.proposaldate)
                            ELSE 0
                        END                 AS slip_days,
                        DATEDIFF(NOW(), ic.updated_at) AS stagnant_days,
                        CASE
                            WHEN ic.proposaldate < CURDATE() AND ic.cstatus < 12
                            THEN 1 ELSE 0
                        END                 AS slipped
                    FROM init_call ic
                    LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                    LEFT JOIN status s          ON s.id  = ic.cstatus
                    LEFT JOIN user u            ON u.uid = ic.mainbd
                    WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
                      AND ic.proposaldate IS NOT NULL
                      AND ic.cstatus NOT IN (13, 14)
                    ORDER BY ic.proposaldate ASC
                    LIMIT 200
                ";
                return $this->db->query($sql, array($uid, $uid, $uid))->result_array();

            case 'funnel_transfer':
                $sql = "
                    SELECT
                        ftl.id            AS transfer_id,
                        ftl.cid           AS lead_id,
                        cm.compname       AS company_name,
                        ic.cstatus,
                        s.name            AS cstatus_name,
                        ic.fbudget,
                        ic.proposaldate,
                        uf.name           AS from_user,
                        ut.name           AS to_user,
                        ub.name           AS transferred_by,
                        ftl.old_status,
                        ftl.new_status,
                        ftl.remarks,
                        ftl.created_at,
                        DATEDIFF(NOW(), ftl.created_at) AS days_in_stage,
                        0 AS slip_days,
                        0 AS stagnant_days
                    FROM funnel_transfer_log ftl
                    LEFT JOIN init_call ic      ON ic.id   = ftl.cid
                    LEFT JOIN company_master cm ON cm.id   = ic.cmpid_id
                    LEFT JOIN status s          ON s.id    = ic.cstatus
                    LEFT JOIN user uf           ON uf.uid  = ftl.from_uid
                    LEFT JOIN user ut           ON ut.uid  = ftl.to_uid
                    LEFT JOIN user ub           ON ub.uid  = ftl.by_uid
                    WHERE (ftl.from_uid = ? OR ftl.to_uid = ?)
                      AND ftl.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    ORDER BY ftl.created_at DESC
                    LIMIT 200
                ";
                return $this->db->query($sql, array($uid, $uid, $days))->result_array();

            case 'created_between':
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sdate)) $sdate = date('Y-m-01');
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $edate)) $edate = date('Y-m-d');
                $sql = "
                    SELECT
                        ic.id           AS lead_id,
                        cm.compname     AS company_name,
                        ic.cstatus,
                        s.name          AS cstatus_name,
                        u.name          AS bd_name,
                        DATEDIFF(NOW(), ic.updated_at) AS days_in_stage,
                        DATEDIFF(NOW(), ic.updated_at) AS slip_days,
                        DATEDIFF(NOW(), ic.updated_at) AS stagnant_days,
                        ic.fbudget,
                        ic.proposaldate,
                        ic.createDate,
                        ic.lead_source
                    FROM init_call ic
                    LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                    LEFT JOIN status s          ON s.id  = ic.cstatus
                    LEFT JOIN user u            ON u.uid = ic.mainbd
                    WHERE ic.mainbd = ?
                      AND ic.createDate BETWEEN ? AND ?
                    ORDER BY ic.createDate DESC
                    LIMIT 500
                ";
                return $this->db->query($sql, array($uid, $sdate, $edate))->result_array();

            case 'deleted_between':
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sdate)) $sdate = date('Y-m-01');
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $edate)) $edate = date('Y-m-d');
                $sql = "
                    SELECT
                        ic.id           AS lead_id,
                        cm.compname     AS company_name,
                        ic.cstatus,
                        s.name          AS cstatus_name,
                        u.name          AS bd_name,
                        DATEDIFF(NOW(), ic.updated_at) AS days_in_stage,
                        DATEDIFF(ic.updated_at, ic.createDate) AS slip_days,
                        DATEDIFF(ic.updated_at, ic.createDate) AS stagnant_days,
                        ic.fbudget,
                        ic.proposaldate,
                        ic.createDate,
                        ic.updated_at   AS closed_at,
                        ic.lead_source
                    FROM init_call ic
                    LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                    LEFT JOIN status s          ON s.id  = ic.cstatus
                    LEFT JOIN user u            ON u.uid = ic.mainbd
                    WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
                      AND ic.cstatus IN (13, 14)
                      AND DATE(ic.updated_at) BETWEEN ? AND ?
                    ORDER BY ic.updated_at DESC
                    LIMIT 500
                ";
                return $this->db->query($sql, array($uid, $uid, $uid, $sdate, $edate))->result_array();

            default:
                return array();
        }
    }

    // -----------------------------------------------------------------------
    // Private: _ensure_libs()
    // Load dompdf once per request. PHPExcel is NOT loaded - it is PHP 8.x
    // incompatible (ReferenceHelper.php uses removed {0} syntax) and causes
    // Fatal errors on this PHP 8.2 server. PhpSpreadsheet (the PHP 8 successor)
    // is not installed via composer. xlsx export therefore falls back to CSV
    // with an .xlsx extension, exactly as Pulse.php does.
    // -----------------------------------------------------------------------
    private function _ensure_libs()
    {
        $dompdf_autoload = APPPATH . '../application/third_party/dompdf/vendor/autoload.php';
        if (file_exists($dompdf_autoload) && !class_exists('Dompdf\Dompdf')) {
            require_once($dompdf_autoload);
        }
    }

    // -----------------------------------------------------------------------
    // Private: _build_xlsx($rows, $report_code)
    // Mirrors Pulse.php build_xlsx pattern exactly.
    // PhpSpreadsheet (modern, PHP 8 compatible) is tried first.
    // PHPExcel (legacy library present on this server) is NOT used because
    // it has PHP 8.x fatal errors in ReferenceHelper.php ({0} syntax removed).
    // Falls back to CSV with .xlsx extension when no spreadsheet lib is found,
    // matching the Pulse.php fallback behavior exactly.
    // -----------------------------------------------------------------------
    private function _build_xlsx($rows, $report_code)
    {
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // PhpSpreadsheet path (PHP 8 compatible modern library)
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(substr($report_code, 0, 31));

            if (empty($rows)) {
                $sheet->setCellValue('A1', 'no_data');
            } else {
                $headers = array_keys($rows[0]);
                $col = 1;
                foreach ($headers as $h) {
                    $sheet->setCellValueByColumnAndRow($col, 1, $h);
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                    $col++;
                }
                $last_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
                $sheet->getStyle("A1:{$last_col}1")->getFont()->setBold(true);

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

        } else {
            // CSV fallback with .xlsx extension - mirrors Pulse.php exactly
            return $this->_build_csv($rows, 'Note: PhpSpreadsheet not installed. This is a CSV file with an .xlsx extension.');
        }
    }

    // -----------------------------------------------------------------------
    // Private: _build_pdf($rows, $report_code, $tab, $sdate, $edate)
    // Mirrors Pulse.php build_pdf exactly. Uses dompdf.
    // -----------------------------------------------------------------------
    private function _build_pdf($rows, $report_code, $tab = '', $sdate = '', $edate = '')
    {
        if (!class_exists('Dompdf\Dompdf')) {
            // Fallback to CSV if dompdf not loaded
            return $this->_build_csv($rows, 'Note: dompdf not loaded. This is CSV with .pdf extension.');
        }

        $title = ucwords(str_replace('_', ' ', $report_code));
        $display_rows = array_slice($rows, 0, 500);
        $truncated    = count($rows) > 500;

        $tab_label  = htmlspecialchars($tab  ?: $report_code);
        $sdate_html = htmlspecialchars($sdate ?: date('Y-m-d'));
        $edate_html = htmlspecialchars($edate ?: date('Y-m-d'));
        $now        = date('d M Y H:i') . ' IST';

        $html  = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Calibri, Arial, sans-serif; font-size: 9pt; color: #222; margin: 20px; }';
        $html .= 'h1 { font-size: 14pt; color: #0a3d62; margin: 0 0 4px 0; }';
        $html .= '.meta { font-size: 8pt; color: #666; margin-bottom: 12px; }';
        $html .= 'table { width: 100%; border-collapse: collapse; }';
        $html .= 'th { background: #0a3d62; color: #fff; padding: 4px 6px; font-size: 8pt; text-align: left; border: 1px solid #0a3d62; }';
        $html .= 'td { padding: 3px 6px; font-size: 8pt; border: 1px solid #ddd; }';
        $html .= 'tr:nth-child(even) td { background: #f6f8fa; }';
        $html .= 'p.note { font-size: 7pt; color: #888; margin-top: 12px; }';
        $html .= '</style></head><body>';
        $html .= '<h1>STEM CRM Funnel Report</h1>';
        $html .= "<div class=\"meta\">Tab: {$tab_label} | Window: {$sdate_html} to {$edate_html} | Generated: {$now}</div>";

        if ($truncated) {
            $html .= '<p class="note">Note: Showing first 500 of ' . count($rows) . ' rows. Download XLSX for full data.</p>';
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

        $html .= '<p class="note">Source: selfstagingstemapp.in /api/funnel_export. Plain English. Rs for rupees.</p>';
        $html .= '</body></html>';

        $dompdf = new \Dompdf\Dompdf(array('isRemoteEnabled' => false));
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->output();
    }

    // -----------------------------------------------------------------------
    // Private: _build_csv($rows, $note)
    // Fallback CSV builder (UTF-8 BOM, header row).
    // -----------------------------------------------------------------------
    private function _build_csv($rows, $note = '')
    {
        $out = "\xEF\xBB\xBF"; // UTF-8 BOM
        if (!empty($note)) {
            $out .= $this->_csv_line(array($note));
        }
        if (empty($rows)) {
            $out .= $this->_csv_line(array('no_data'));
            return $out;
        }
        $out .= $this->_csv_line(array_keys($rows[0]));
        foreach ($rows as $row) {
            $out .= $this->_csv_line(array_values($row));
        }
        return $out;
    }

    private function _csv_line($values)
    {
        $cells = array();
        foreach ($values as $v) {
            $s = (string)($v ?? '');
            if (strpbrk($s, ',"' . "\n\r") !== false) {
                $s = '"' . str_replace('"', '""', $s) . '"';
            }
            $cells[] = $s;
        }
        return implode(',', $cells) . "\r\n";
    }

    // -----------------------------------------------------------------------
    // Private: _log_export($data)
    // Inserts a row into funnel_export_log. Silently skips if table missing.
    // Schema (Agent A): export_type ENUM('pdf','xlsx','email'), endpoint VARCHAR(64),
    // row_count INT, file_path VARCHAR(255), email_to VARCHAR(255),
    // status ENUM('success','failed'), error_msg VARCHAR(500), created_at DATETIME.
    // -----------------------------------------------------------------------
    private function _log_export($data)
    {
        try {
            $this->db->insert('funnel_export_log', $data);
        } catch (Exception $e) {
            // Table may not exist yet (Agent A creates it). Do not crash.
            log_message('error', 'FunnelExportController::_log_export failed: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------
    // Private: _get_uid_from_request()
    // Reads uid from query string or authed session.
    // -----------------------------------------------------------------------
    private function _get_uid_from_request()
    {
        $uid = (int)$this->input->get('user_id');
        if ($uid <= 0) $uid = $this->_authed_uid;
        return $uid;
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_export/xlsx?endpoint=stuck_status&user_id=X[&days=14&sdate=&edate=]
    // Streams an XLSX file to the client.
    // -----------------------------------------------------------------------
    public function export_xlsx()
    {
        $this->_auth_or_die();
        $this->_ensure_libs();

        $endpoint = $this->input->get('endpoint') ?: 'stuck_status';
        $uid      = $this->_get_uid_from_request();
        $filters  = array(
            'uid'   => $uid,
            'days'  => (int)($this->input->get('days') ?: 14),
            'sdate' => $this->input->get('sdate') ?: date('Y-m-01'),
            'edate' => $this->input->get('edate') ?: date('Y-m-d'),
        );

        try {
            $rows = $this->fetch_rows($endpoint, $filters);
            if (empty($rows)) { $rows = array(array('note' => 'no_data')); }

            $report_code = 'funnel_' . $endpoint . '_' . date('Ymd_His');
            $content     = $this->_build_xlsx($rows, $report_code);
            $filename    = $report_code . '.xlsx';
            $tmp_path    = '/tmp/' . $filename;
            $row_count   = count($rows);

            file_put_contents($tmp_path, $content);

            // Log to funnel_export_log (schema: export_type ENUM pdf|xlsx|email)
            $this->_log_export(array(
                'uid'         => $uid,
                'export_type' => 'xlsx',
                'endpoint'    => $endpoint,
                'row_count'   => $row_count,
                'file_path'   => $tmp_path,
                'status'      => 'success',
                'created_at'  => date('Y-m-d H:i:s'),
            ));

            // Stream to client
            header_remove('Content-Type');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($content));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $content;
            @unlink($tmp_path);

        } catch (Exception $e) {
            $this->_log_export(array(
                'uid'         => $uid,
                'export_type' => 'xlsx',
                'endpoint'    => $endpoint,
                'row_count'   => 0,
                'status'      => 'failed',
                'error_msg'   => $e->getMessage(),
                'created_at'  => date('Y-m-d H:i:s'),
            ));
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => $e->getMessage()));
        }
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_export/pdf?endpoint=stuck_status&user_id=X[&days=14&sdate=&edate=]
    // Streams a PDF file to the client.
    // -----------------------------------------------------------------------
    public function export_pdf()
    {
        $this->_auth_or_die();
        $this->_ensure_libs();

        $endpoint = $this->input->get('endpoint') ?: 'stuck_status';
        $uid      = $this->_get_uid_from_request();
        $sdate    = $this->input->get('sdate') ?: date('Y-m-01');
        $edate    = $this->input->get('edate') ?: date('Y-m-d');
        $filters  = array(
            'uid'   => $uid,
            'days'  => (int)($this->input->get('days') ?: 14),
            'sdate' => $sdate,
            'edate' => $edate,
        );

        try {
            $rows = $this->fetch_rows($endpoint, $filters);
            if (empty($rows)) { $rows = array(array('note' => 'no_data')); }

            $report_code = 'funnel_' . $endpoint . '_' . date('Ymd_His');
            $content     = $this->_build_pdf($rows, $report_code, $endpoint, $sdate, $edate);
            $filename    = $report_code . '.pdf';
            $tmp_path    = '/tmp/' . $filename;
            $row_count   = count($rows);

            file_put_contents($tmp_path, $content);

            // Log to funnel_export_log (schema: export_type ENUM pdf|xlsx|email)
            $this->_log_export(array(
                'uid'         => $uid,
                'export_type' => 'pdf',
                'endpoint'    => $endpoint,
                'row_count'   => $row_count,
                'file_path'   => $tmp_path,
                'status'      => 'success',
                'created_at'  => date('Y-m-d H:i:s'),
            ));

            // Stream to client
            header_remove('Content-Type');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($content));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $content;
            @unlink($tmp_path);

        } catch (Exception $e) {
            $this->_log_export(array(
                'uid'         => $uid,
                'export_type' => 'pdf',
                'endpoint'    => $endpoint,
                'row_count'   => 0,
                'status'      => 'failed',
                'error_msg'   => $e->getMessage(),
                'created_at'  => date('Y-m-d H:i:s'),
            ));
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => $e->getMessage()));
        }
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_export/email?endpoint=stuck_status&to=x@y.com&format=pdf[&dry_run=1]
    // Builds the file and emails it via Crm_emailer. Logs to both tables.
    // Pass dry_run=1 to skip the actual send (SMTP probe).
    // -----------------------------------------------------------------------
    public function email_report()
    {
        $this->_auth_or_die();
        $this->_ensure_libs();

        $endpoint = $this->input->get('endpoint') ?: 'stuck_status';
        $uid      = $this->_get_uid_from_request();
        $sdate    = $this->input->get('sdate') ?: date('Y-m-01');
        $edate    = $this->input->get('edate') ?: date('Y-m-d');
        $format   = strtolower($this->input->get('format') ?: 'pdf');
        $dry_run  = (int)$this->input->get('dry_run');

        // Resolve recipient: ?to= param, or fall back to a safe default
        $to = $this->input->get('to');
        if (empty($to)) {
            // Try to get email from user table for authed uid
            if ($uid > 0) {
                $u_row = $this->db->select('email')->from('user')->where('uid', $uid)->get()->row_array();
                $to = isset($u_row['email']) ? $u_row['email'] : '';
            }
        }
        if (empty($to)) {
            echo json_encode(array('ok' => false, 'error' => 'to email required (pass ?to= or ensure user has email)'));
            return;
        }

        $filters = array(
            'uid'   => $uid,
            'days'  => (int)($this->input->get('days') ?: 14),
            'sdate' => $sdate,
            'edate' => $edate,
        );

        try {
            $rows = $this->fetch_rows($endpoint, $filters);
            if (empty($rows)) { $rows = array(array('note' => 'no_data')); }

            $report_code = 'funnel_' . $endpoint . '_' . date('Ymd_His');
            $row_count   = count($rows);

            if ($format === 'xlsx') {
                $content  = $this->_build_xlsx($rows, $report_code);
                $filename = $report_code . '.xlsx';
                $mime     = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            } else {
                $content  = $this->_build_pdf($rows, $report_code, $endpoint, $sdate, $edate);
                $filename = $report_code . '.pdf';
                $mime     = 'application/pdf';
                $format   = 'pdf';
            }

            $tmp_path = '/tmp/' . $filename;
            file_put_contents($tmp_path, $content);

            $subject = 'STEM CRM Funnel Report - ' . $endpoint . ' - ' . $sdate . ' to ' . $edate;
            $html_body = '<p>Please find the funnel report attached.</p>'
                       . '<p>Tab: ' . htmlspecialchars($endpoint) . '<br>'
                       . 'Window: ' . htmlspecialchars($sdate) . ' to ' . htmlspecialchars($edate) . '<br>'
                       . 'Rows: ' . $row_count . '</p>'
                       . '<p style="font-size:11px;color:#888">Generated by STEM CRM. Plain English. Rs for rupees.</p>';

            $sent = false;
            $smtp_note = '';

            if ($dry_run) {
                $smtp_note = 'dry_run_skipped';
                $sent      = false;
            } else {
                // Use Crm_emailer for send + audit log
                $this->load->library('Crm_emailer');

                // Attach file directly via CI email (Crm_emailer does not support attachments)
                $this->CI_email_attach($to, $subject, $html_body, $tmp_path);

                // Also call send_and_log for crm_email_logs audit trail
                $sent = $this->crm_emailer->send_and_log(
                    $subject,
                    $html_body,
                    $to,
                    array(
                        'from_email' => 'no-reply@stemapp.in',
                        'from_name'  => 'STEM CRM',
                        'for_user'   => $uid,
                        'type'       => 'funnel_report',
                    )
                );
            }

            // Log to funnel_export_log (schema: export_type ENUM pdf|xlsx|email)
            // Use 'email' enum value; format detail goes in error_msg/note field
            $this->_log_export(array(
                'uid'         => $uid,
                'export_type' => 'email',
                'endpoint'    => $endpoint,
                'row_count'   => $row_count,
                'email_to'    => $to,
                'status'      => ($dry_run || $sent) ? 'success' : 'failed',
                'error_msg'   => $smtp_note ?: ($sent ? null : 'smtp_not_configured'),
                'created_at'  => date('Y-m-d H:i:s'),
            ));

            @unlink($tmp_path);

            echo json_encode(array(
                'ok'       => true,
                'sent_to'  => $to,
                'file'     => $filename,
                'rows'     => $row_count,
                'sent'     => $sent,
                'dry_run'  => (bool)$dry_run,
                'note'     => $smtp_note ?: null,
            ));

        } catch (Exception $e) {
            $this->_log_export(array(
                'uid'         => $uid,
                'export_type' => 'email',
                'endpoint'    => $endpoint,
                'row_count'   => 0,
                'status'      => 'failed',
                'error_msg'   => $e->getMessage(),
                'created_at'  => date('Y-m-d H:i:s'),
            ));
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => $e->getMessage()));
        }
    }

    // -----------------------------------------------------------------------
    // Private helper: send email with attachment using CI email library
    // Used by email_report when attachment is needed.
    // -----------------------------------------------------------------------
    private function CI_email_attach($to, $subject, $html_body, $attachment_path)
    {
        $this->load->library('email');
        $config = array('mailtype' => 'html', 'charset' => 'utf-8', 'newline' => "\r\n");
        $this->email->initialize($config);
        $this->email->clear();
        $this->email->from('no-reply@stemapp.in', 'STEM CRM');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($html_body);
        if (file_exists($attachment_path)) {
            $this->email->attach($attachment_path);
        }
        return $this->email->send();
    }
}
