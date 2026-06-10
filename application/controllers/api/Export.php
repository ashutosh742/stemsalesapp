<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/Export.php
 * CRM export endpoint - read-only PDF/zip export.
 * Mirrors production UploadCRMReports (CSV_BULK.txt) but read-only.
 * Bearer token authentication. All non-stream responses are JSON.
 * Plain ASCII only. No em-dash.
 */
class Export extends MY_Controller {

    // Allowed report type keys (matches production filetypesname values).
    private static $REPORT_TYPES = array(
        'AllPlannerLogPlannedByUsers'          => 'AllPlannerLogPlannedByUsers',
        'AllCompulsiveAndNeedYourAttentionLog'  => 'AllCompulsiveAndNeedYourAttentionLog',
        'CRMMeetingReportData'                 => 'CRMMeetingReportData',
        'proposaldata'                         => 'proposaldata',
        'handoverReportData'                   => 'handoverReportData',
        'AllSameStatusLogByUserReports'        => 'AllSameStatusLogByUserReports',
        'momReportData'                        => 'momReportData',
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    protected function _check_bearer() {
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $auth = '';
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!$auth || stripos($auth, 'Bearer ') !== 0) return false;
        $token = trim(substr($auth, 7));
        if ($token === '') return false;
        $row = $this->db->query(
            'SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1',
            array($token)
        )->row_array();
        if (!$row) return false;
        return $row;
    }

    protected function _json($payload, $http_code = 200) {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /api/export/probe
     * Liveness check. Always returns 200 JSON.
     */
    public function probe() {
        $this->_json(array(
            'ok'           => true,
            'endpoint'     => 'export',
            'status'       => 'ready',
            'server_time'  => date('c'),
            'report_types' => array_keys(self::$REPORT_TYPES),
        ));
    }

    /**
     * GET /api/export/crm_report/(:any) -> crm_report($type)
     * Generates a CRM report. Returns base64-encoded PDF or JSON summary.
     * Mirrors production UploadCRMReports (read-only: no file saved server-side).
     * Query params: uid (optional, defaults to token uid), format (json|pdf, default json).
     *
     * @param string $type  One of the allowed report type keys.
     */
    public function crm_report($type = '') {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }

            $type = trim($type);
            if ($type === '' || !array_key_exists($type, self::$REPORT_TYPES)) {
                $this->_json(array(
                    'ok'    => false,
                    'error' => 'Invalid report type. Allowed: ' . implode(', ', array_keys(self::$REPORT_TYPES))
                ), 400);
                return;
            }

            $uid    = (int)($this->input->get('uid') ?: $auth['uid']);
            $format = $this->input->get('format') ?: 'json';

            // Gather report data depending on type.
            $data   = $this->_gather_report_data($type, $uid, $auth);
            $cdate  = date('Y-m-d-H-i-s');

            // Build filename matching production convention.
            $filename_map = array(
                'AllPlannerLogPlannedByUsers'         => "AllPlannerLogPlannedByUsers-$cdate.pdf",
                'AllCompulsiveAndNeedYourAttentionLog' => "AllCompulsiveAndNeedYourAttentionLog-$cdate.pdf",
                'CRMMeetingReportData'                => "meetingreport-$cdate.pdf",
                'proposaldata'                        => "proposaldata-$cdate.pdf",
                'handoverReportData'                  => "handoverReportData-$cdate.pdf",
                'AllSameStatusLogByUserReports'       => "AllSameStatusLogByUserReports-$cdate.pdf",
                'momReportData'                       => "momReportData-$cdate.pdf",
            );
            $filename = $filename_map[$type];

            if ($format === 'pdf') {
                // Attempt TCPDF if available; else return base64-encoded simple text PDF.
                $pdf_content = $this->_generate_pdf($type, $data, $filename);
                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/pdf')
                    ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
                    ->set_output($pdf_content);
            } else {
                // Return as JSON with base64 content for mobile client.
                $pdf_content = $this->_generate_pdf($type, $data, $filename);
                $this->_json(array(
                    'ok'       => true,
                    'type'     => $type,
                    'filename' => $filename,
                    'pdf_b64'  => base64_encode($pdf_content),
                    'rows'     => count($data),
                ));
            }
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/export/zip/(:any) -> zip_download($folder_name)
     * Zips files under uploads/$folder_name and streams the zip.
     * Only alphanumeric and underscore folder names are allowed (security guard).
     *
     * @param string $folder_name  Folder under the uploads/ directory.
     */
    public function zip_download($folder_name = '') {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }

            $folder_name = trim($folder_name);

            // Security: allow only safe folder name characters.
            if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $folder_name)) {
                $this->_json(array('ok' => false, 'error' => 'Invalid folder name'), 400); return;
            }

            $base_path   = FCPATH . 'uploads/' . $folder_name;
            if (!is_dir($base_path)) {
                $this->_json(array('ok' => false, 'error' => 'Folder not found'), 404); return;
            }

            $this->load->library('zip');

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_path));
            $found = 0;
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $this->zip->read_file($file->getPathname());
                    $found++;
                }
            }

            if ($found === 0) {
                $this->_json(array('ok' => false, 'error' => 'No files found in folder'), 404); return;
            }

            $zip_name = $folder_name . '-' . time() . '.zip';
            $this->zip->download($zip_name);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Gather report data rows from the database based on report type.
     * Returns an array of associative rows.
     */
    protected function _gather_report_data($type, $uid, $auth) {
        $role = isset($auth['role']) ? strtolower($auth['role']) : '';
        try {
            switch ($type) {
                case 'CRMMeetingReportData':
                    return $this->db->query(
                        'SELECT m.id, m.uid, m.cid_id, m.meeting_date, m.status FROM barginmeeting m WHERE m.uid = ? ORDER BY m.meeting_date DESC LIMIT 500',
                        array($uid)
                    )->result_array();

                case 'proposaldata':
                    return $this->db->query(
                        'SELECT id, cid_id, proposal_date, amount, status FROM allreviewdata WHERE uid = ? ORDER BY proposal_date DESC LIMIT 500',
                        array($uid)
                    )->result_array();

                case 'handoverReportData':
                    $q = ($role === 'admin')
                        ? 'SELECT id, cid_id, project_code, compname, status, submitted_at FROM handover_v2 ORDER BY created_at DESC LIMIT 500'
                        : 'SELECT id, cid_id, project_code, compname, status, submitted_at FROM handover_v2 WHERE closing_bd_uid = ? ORDER BY created_at DESC LIMIT 500';
                    $params = ($role === 'admin') ? array() : array($uid);
                    return $this->db->query($q, $params)->result_array();

                case 'AllPlannerLogPlannedByUsers':
                    return $this->db->query(
                        'SELECT id, uid, plan_date, plan_type, status FROM cm_daily_plan ORDER BY plan_date DESC LIMIT 1000'
                    )->result_array();

                case 'momReportData':
                    return $this->db->query(
                        'SELECT id, cid_id, mom_date, status FROM allreview WHERE uid = ? ORDER BY mom_date DESC LIMIT 500',
                        array($uid)
                    )->result_array();

                default:
                    return array();
            }
        } catch (Exception $e) {
            // Table may not exist. Return empty gracefully.
            return array();
        }
    }

    /**
     * Generate a minimal PDF. Uses TCPDF if available, else a raw minimal PDF byte string.
     * Returns raw PDF content as a string.
     */
    protected function _generate_pdf($type, $data, $filename) {
        // Try TCPDF first.
        if (class_exists('TCPDF')) {
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('STEM CRM');
            $pdf->SetTitle($type . ' Report');
            $pdf->SetHeaderData('', 0, 'STEM CRM - ' . $type, date('d M Y H:i'));
            $pdf->setHeaderFont(array('helvetica', '', 10));
            $pdf->setFooterFont(array('helvetica', '', 8));
            $pdf->SetMargins(15, 27, 15);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(true, 25);
            $pdf->AddPage();

            $html = '<h2>' . htmlspecialchars($type) . ' Report</h2>';
            $html .= '<p>Generated: ' . date('d M Y H:i:s') . '</p>';
            $html .= '<p>Total records: ' . count($data) . '</p>';
            if (!empty($data)) {
                $html .= '<table border="1" cellpadding="3"><tr>';
                foreach (array_keys($data[0]) as $col) {
                    $html .= '<th>' . htmlspecialchars($col) . '</th>';
                }
                $html .= '</tr>';
                foreach (array_slice($data, 0, 200) as $row) {
                    $html .= '<tr>';
                    foreach ($row as $val) {
                        $html .= '<td>' . htmlspecialchars((string)$val) . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
            $pdf->writeHTML($html, true, false, true, false, '');
            return $pdf->Output($filename, 'S');
        }

        // Fallback: build a minimal valid PDF manually.
        $title   = $type . ' Report - Generated ' . date('d M Y H:i');
        $body    = "STEM CRM Export\nReport: $type\nGenerated: " . date('d M Y H:i:s') . "\nRecords: " . count($data);
        return $this->_minimal_pdf($title, $body);
    }

    /**
     * Build an extremely minimal PDF containing plain text.
     * This is a valid but very basic PDF 1.4 document.
     */
    protected function _minimal_pdf($title, $body_text) {
        $lines = explode("\n", wordwrap($body_text, 80));
        $text_stream = '';
        $y = 750;
        foreach ($lines as $line) {
            $safe = str_replace(array('(', ')', '\\'), array('\\(', '\\)', '\\\\'), $line);
            $text_stream .= "BT /F1 12 Tf 50 $y Td ($safe) Tj ET\n";
            $y -= 16;
            if ($y < 50) break;
        }

        $objects = array();
        $objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[2] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[3] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        $objects[4] = "4 0 obj\n<< /Length " . strlen($text_stream) . " >>\nstream\n" . $text_stream . "endstream\nendobj\n";
        $objects[5] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $out    = "%PDF-1.4\n";
        $xref   = array();
        foreach ($objects as $n => $obj) {
            $xref[$n] = strlen($out);
            $out .= $obj;
        }

        $xref_offset = strlen($out);
        $out .= "xref\n0 " . (count($objects) + 1) . "\n";
        $out .= "0000000000 65535 f \n";
        foreach ($xref as $offset) {
            $out .= str_pad($offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $out .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref_offset\n%%EOF\n";
        return $out;
    }
}
