<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * School_logo_api
 *
 * Manages school logo images for the STEM CRM.
 * Endpoints:
 *   GET  /api/school/logo/get           - fetch logo info by cid_id
 *   POST /api/school/logo/upload         - upload base64 image (Bearer required)
 *   POST /api/school/logo/fetch_clearbit - pull logo from Clearbit (Bearer required)
 *   POST /api/school/logo/bulk_seed      - seed top 200 schools (Bearer required)
 *
 * Created: Agent C - School Logo Support
 */
class School_logo_api extends MY_Controller {

    private $upload_dir;
    private $upload_url_base;
    private $hardcoded_token;

    public function __construct() {
        parent::__construct();
        $this->upload_dir      = FCPATH . 'uploads/school_logos/';
        $this->upload_url_base = 'https://selfstagingstemapp.in/uploads/school_logos/';
        $this->hardcoded_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

        // Ensure upload directory exists
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Override parent _json to match return style of other API controllers.
     */
    protected function _json($payload, $code = 200) {
        http_response_code($code);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * Override parent _require_bearer to use hardcoded fallback token.
     */
    protected function _require_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $all = apache_request_headers();
            $hdr = isset($all['Authorization']) ? $all['Authorization']
                : (isset($all['authorization']) ? $all['authorization'] : '');
        }
        $expected = getenv('STEM_DIGEST_TOKEN') ?: $this->hardcoded_token;
        if (strpos($hdr, 'Bearer ') === 0) {
            $provided = trim(substr($hdr, 7));
            if (hash_equals($expected, $provided)) {
                return true;
            }
        }
        $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
        exit;
    }

    /**
     * Derive a domain from school_website or school name.
     * Returns a string like "school.edu.in" or empty string.
     */
    private function _derive_domain($website, $school_name) {
        if (!empty($website)) {
            $website = trim($website);
            // Strip protocol
            $website = preg_replace('#^https?://#i', '', $website);
            // Strip path and trailing slash
            $website = explode('/', $website)[0];
            $website = strtolower(trim($website));
            if (!empty($website) && strpos($website, '.') !== false) {
                return $website;
            }
        }

        // Guess from school name
        if (!empty($school_name)) {
            $name  = strtolower(preg_replace('/[^a-z0-9 ]/i', '', $school_name));
            $words = array_filter(explode(' ', $name), function($w) {
                return strlen($w) > 2 && !in_array($w, [
                    'the', 'and', 'for', 'of', 'in', 'at', 'by', 'to',
                    'school', 'high', 'new', 'public', 'govt', 'government',
                    'international', 'national', 'central', 'state', 'ltd',
                    'foundation', 'trust', 'society', 'education',
                ]);
            });
            if (!empty($words)) {
                $first = reset($words);
                return $first . '.edu.in';
            }
        }
        return '';
    }

    /**
     * Generate a colored initials SVG for a school name.
     */
    private function _make_initials_svg($school_name, $cid_id) {
        $words    = preg_split('/\s+/', trim($school_name));
        $initials = '';
        foreach ($words as $w) {
            $w = preg_replace('/[^a-zA-Z]/', '', $w);
            if (strlen($w) > 0) {
                $initials .= strtoupper($w[0]);
                if (strlen($initials) >= 2) break;
            }
        }
        if (empty($initials)) {
            $initials = 'SC';
        }

        $hash     = crc32($school_name . $cid_id);
        $hue      = abs($hash) % 360;
        $bg_color = $this->_hsl_to_hex($hue, 60, 45);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80">'
            . '<rect width="80" height="80" rx="8" fill="' . $bg_color . '"/>'
            . '<text x="40" y="52" font-family="Arial,sans-serif" font-size="28" font-weight="bold"'
            . ' text-anchor="middle" fill="#FFFFFF">' . htmlspecialchars($initials) . '</text>'
            . '</svg>';

        return $svg;
    }

    private function _hsl_to_hex($h, $s, $l) {
        $h = $h / 360;
        $s = $s / 100;
        $l = $l / 100;
        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->_hue2rgb($p, $q, $h + 1/3);
            $g = $this->_hue2rgb($p, $q, $h);
            $b = $this->_hue2rgb($p, $q, $h - 1/3);
        }
        return sprintf('#%02x%02x%02x', round($r * 255), round($g * 255), round($b * 255));
    }

    private function _hue2rgb($p, $q, $t) {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }

    /**
     * Try to fetch logo from Clearbit free API.
     */
    private function _clearbit_fetch($domain) {
        $url = 'https://logo.clearbit.com/' . urlencode($domain);
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'STEM-CRM/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $data   = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ctype  = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        $is_image = ($status === 200) && (strpos($ctype, 'image') !== false);
        return ['ok' => $is_image, 'data' => $is_image ? $data : null, 'status' => $status];
    }

    /**
     * Save logo data to disk and update both init_call and schoolDetails.
     */
    private function _save_logo($cid_id, $data, $source, $ext = 'png') {
        $filename   = $cid_id . '_' . $source . '.' . $ext;
        $filepath   = $this->upload_dir . $filename;
        file_put_contents($filepath, $data);
        chmod($filepath, 0644);

        $public_url = $this->upload_url_base . $filename;
        $fetched_at = date('Y-m-d H:i:s');

        // Update init_call.logo_url
        $this->db->where('id', $cid_id)->update('init_call', ['logo_url' => $public_url]);

        // Upsert schoolDetails
        $existing = $this->db->get_where('schoolDetails', ['cid_id' => $cid_id])->row();
        $logo_data = [
            'logo_url'        => $public_url,
            'logo_source'     => $source,
            'logo_fetched_at' => $fetched_at,
            'brand_color_hex' => null,
        ];
        if ($existing) {
            $this->db->where('cid_id', $cid_id)->update('schoolDetails', $logo_data);
        } else {
            $logo_data['cid_id']            = $cid_id;
            $logo_data['user_id']           = 0;
            $logo_data['client_name']       = '';
            $logo_data['district']          = '';
            $logo_data['location']          = '';
            $logo_data['noofschools']       = '0';
            $logo_data['pst_approve']       = '';
            $logo_data['pst_remark']        = '';
            $logo_data['pst_name']          = '';
            $logo_data['cluster_approve']   = '';
            $logo_data['cluster_remark']    = '';
            $logo_data['cluster_name']      = '';
            $logo_data['is_approve']        = '';
            $logo_data['is_approve_remark'] = '';
            $logo_data['is_admin_approved'] = '';
            $logo_data['createdat']         = $fetched_at;
            $logo_data['updatedat']         = $fetched_at;
            $this->db->insert('schoolDetails', $logo_data);
        }

        return $public_url;
    }

    /**
     * Get logo info for a given cid_id.
     */
    private function _get_logo_info($cid_id) {
        $ic = $this->db->select('id, logo_url, school_website')
            ->get_where('init_call', ['id' => $cid_id])
            ->row();
        if (!$ic) return null;

        $sd = $this->db->select('logo_url, logo_source, logo_fetched_at, brand_color_hex')
            ->get_where('schoolDetails', ['cid_id' => $cid_id])
            ->row();

        return [
            'cid_id'          => (int)$cid_id,
            'logo_url'        => $ic->logo_url ?: ($sd ? $sd->logo_url : null),
            'source'          => $sd ? $sd->logo_source : null,
            'fetched_at'      => $sd ? $sd->logo_fetched_at : null,
            'brand_color_hex' => $sd ? $sd->brand_color_hex : null,
        ];
    }

    // -------------------------------------------------------------------------
    // Endpoints
    // -------------------------------------------------------------------------

    /**
     * GET /api/school/logo/get?cid_id=<id>
     */
    public function get() {
        $cid_id = (int)$this->input->get('cid_id');
        if (!$cid_id) {
            return $this->_json(['ok' => false, 'error' => 'cid_id required'], 400);
        }
        $info = $this->_get_logo_info($cid_id);
        if ($info === null) {
            return $this->_json(['ok' => false, 'error' => 'school not found'], 404);
        }
        $this->_json(array_merge(['ok' => true], $info));
    }

    /**
     * POST /api/school/logo/upload
     * Body (JSON): { cid_id: int, image_base64: string }
     */
    public function upload() {
        $this->_require_bearer();

        $body   = json_decode(file_get_contents('php://input'), true) ?: [];
        $cid_id = isset($body['cid_id']) ? (int)$body['cid_id'] : (int)$this->input->post('cid_id');
        $b64    = isset($body['image_base64']) ? trim($body['image_base64'])
                : trim((string)$this->input->post('image_base64'));

        if (!$cid_id || empty($b64)) {
            return $this->_json(['ok' => false, 'error' => 'cid_id and image_base64 required'], 400);
        }
        if (!$this->db->get_where('init_call', ['id' => $cid_id])->row()) {
            return $this->_json(['ok' => false, 'error' => 'school not found'], 404);
        }

        // Strip data URI prefix if present
        $b64    = preg_replace('#^data:image/[a-z]+;base64,#i', '', $b64);
        $binary = base64_decode($b64);
        if (!$binary) {
            return $this->_json(['ok' => false, 'error' => 'invalid base64 data'], 400);
        }

        try {
            $public_url = $this->_save_logo($cid_id, $binary, 'manual_upload', 'png');
            $this->_json(['ok' => true, 'cid_id' => $cid_id, 'logo_url' => $public_url, 'source' => 'manual_upload']);
        } catch (Exception $e) {
            log_message('error', 'School_logo_api::upload: ' . $e->getMessage());
            $this->_json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/school/logo/fetch_clearbit?cid_id=<id>&domain=<domain>
     * Also accepts POST body.
     */
    public function fetch_clearbit() {
        $this->_require_bearer();

        $cid_id = (int)($this->input->post('cid_id') ?: $this->input->get('cid_id'));
        $domain = trim((string)($this->input->post('domain') ?: $this->input->get('domain')));

        if (!$cid_id) {
            return $this->_json(['ok' => false, 'error' => 'cid_id required'], 400);
        }

        $ic = $this->db->select('id, school_website, logo_url')
            ->get_where('init_call', ['id' => $cid_id])
            ->row();
        if (!$ic) {
            return $this->_json(['ok' => false, 'error' => 'school not found'], 404);
        }

        // Get school name from company_master
        $cm = $this->db->select('cm.compname')
            ->from('init_call ic')
            ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
            ->where('ic.id', $cid_id)
            ->get()->row();
        $school_name = $cm ? trim((string)$cm->compname) : '';

        if (empty($domain)) {
            $domain = $this->_derive_domain((string)$ic->school_website, $school_name);
        }

        if (empty($domain)) {
            // No domain - go straight to derived
            $svg        = $this->_make_initials_svg($school_name ?: 'School', $cid_id);
            $public_url = $this->_save_logo($cid_id, $svg, 'derived', 'svg');
            return $this->_json([
                'ok'       => true,
                'cid_id'   => $cid_id,
                'domain'   => '',
                'logo_url' => $public_url,
                'source'   => 'derived',
                'note'     => 'no_domain_available',
            ]);
        }

        $result = $this->_clearbit_fetch($domain);

        if ($result['ok']) {
            $public_url = $this->_save_logo($cid_id, $result['data'], 'clearbit', 'png');
            return $this->_json([
                'ok'          => true,
                'cid_id'      => $cid_id,
                'domain'      => $domain,
                'logo_url'    => $public_url,
                'source'      => 'clearbit',
                'http_status' => $result['status'],
            ]);
        }

        // Clearbit failed - fall back to derived initials SVG
        $svg        = $this->_make_initials_svg($school_name ?: 'School', $cid_id);
        $public_url = $this->_save_logo($cid_id, $svg, 'derived', 'svg');
        return $this->_json([
            'ok'              => true,
            'cid_id'          => $cid_id,
            'domain'          => $domain,
            'logo_url'        => $public_url,
            'source'          => 'derived',
            'clearbit_status' => $result['status'],
            'note'            => 'clearbit_failed_used_initials_svg',
        ]);
    }

    /**
     * POST /api/school/logo/bulk_seed
     * Seeds top 200 schools by recent tblcallevents activity.
     */
    public function bulk_seed() {
        $this->_require_bearer();
        set_time_limit(600);

        $sql = "
            SELECT ic.id AS cid_id,
                   cm.compname AS school_name,
                   ic.school_website,
                   COUNT(te.id) AS event_count
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN tblcallevents te ON te.cid_id = ic.id
                AND te.fwd_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
            GROUP BY ic.id
            ORDER BY event_count DESC
            LIMIT 200
        ";
        $rows = $this->db->query($sql)->result();

        $results       = [];
        $clearbit_hits = 0;
        $derived_count = 0;
        $failed_count  = 0;
        $already_count = 0;

        foreach ($rows as $row) {
            $cid_id      = (int)$row->cid_id;
            $school_name = trim((string)$row->school_name);
            $website     = trim((string)$row->school_website);

            // Skip if already has a logo
            $existing = $this->db->select('logo_url')->get_where('init_call', ['id' => $cid_id])->row();
            if ($existing && !empty($existing->logo_url)) {
                $results[] = [
                    'cid_id'      => $cid_id,
                    'school_name' => $school_name,
                    'domain'      => '',
                    'status'      => 'already_has_logo',
                    'logo_url'    => $existing->logo_url,
                ];
                $already_count++;
                continue;
            }

            $domain = $this->_derive_domain($website, $school_name);

            if (!empty($domain)) {
                $cb = $this->_clearbit_fetch($domain);
                if ($cb['ok']) {
                    $url = $this->_save_logo($cid_id, $cb['data'], 'clearbit', 'png');
                    $results[] = [
                        'cid_id'      => $cid_id,
                        'school_name' => $school_name,
                        'domain'      => $domain,
                        'status'      => 'clearbit_ok',
                        'logo_url'    => $url,
                    ];
                    $clearbit_hits++;
                    continue;
                }
            }

            // Fall back to derived initials SVG
            $svg = $this->_make_initials_svg($school_name ?: 'School', $cid_id);
            $url = $this->_save_logo($cid_id, $svg, 'derived', 'svg');
            $results[] = [
                'cid_id'      => $cid_id,
                'school_name' => $school_name,
                'domain'      => $domain,
                'status'      => 'derived',
                'logo_url'    => $url,
            ];
            $derived_count++;
        }

        $this->_json([
            'ok'              => true,
            'total_attempted' => count($rows),
            'clearbit_hits'   => $clearbit_hits,
            'derived'         => $derived_count,
            'already_had_logo'=> $already_count,
            'failed'          => $failed_count,
            'results'         => $results,
        ]);
    }
}
