<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CardOcr_agent
 *
 * Migration 034 - Business Card Scan + OCR Lead Capture
 * Staging only (stemapp.in). Pilot Mon 25 May 2026.
 *
 * Responsibilities:
 *   1. Receive an uploaded image path (already saved to S3 by controller).
 *   2. Call Google Vision DOCUMENT_TEXT_DETECTION (primary).
 *   3. Fall back to AWS Textract if Vision returns under 10 characters or errors.
 *   4. Run regex extraction for name/designation/email/phone/org/address.
 *   5. Run GPT-4o-mini cleanup pass (1s timeout cap, non-blocking on failure).
 *   6. Score per-field confidence (0.0 to 1.0).
 *   7. Run dedup: exact email/phone + fuzzy org Levenshtein against init_call
 *      rows from the last 12 months.
 *   8. Write a card_scan_log row and return the full parsed payload.
 *
 * Author: STEM ops, 2026-05-19.
 */
class CardOcr_agent
{
    const VISION_URL          = 'https://vision.googleapis.com/v1/images:annotate';
    const TEXTRACT_REGION     = 'ap-south-1';
    const VISION_MIN_CHARS    = 10;
    const LLM_TIMEOUT_S       = 1;
    const DEDUP_MONTHS        = 12;
    const ORG_LEVENSHTEIN_MAX = 4;

    private $dm_keywords = [
        'principal','director','vice principal','headmaster','headmistress',
        'head of school','school director','chairman','chairperson',
        'managing trustee','trustee','secretary','ceo','managing director',
        'founder','co-founder','correspondent'
    ];

    private $db;
    private $vision_key;  // TODO: provision - add GOOGLE_VISION_API_KEY to stem_secrets.php
    private $aws_key;
    private $aws_secret;
    private $openai_key;  // TODO: provision - add OPENAI_API_KEY to stem_secrets.php

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->database();
        $this->db         = $CI->db;
        $this->vision_key = defined('GOOGLE_VISION_API_KEY') ? GOOGLE_VISION_API_KEY : null;
        $this->aws_key    = defined('AWS_ACCESS_KEY_ID')     ? AWS_ACCESS_KEY_ID     : null;
        $this->aws_secret = defined('AWS_SECRET_ACCESS_KEY') ? AWS_SECRET_ACCESS_KEY : null;
        $this->openai_key = defined('OPENAI_API_KEY')        ? OPENAI_API_KEY        : null;
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Main entry point. Called by CardOcrController after image is saved to S3.
     *
     * @param  int    $uid        BD user.uid
     * @param  string $image_url  S3 URL stored in card_scan_log.image_url
     * @param  string $image_path Absolute local path for reading image bytes
     * @return array {
     *   ok, scan_id, parsed{name,designation,email,phone,org,address},
     *   confidence{name,designation,email,phone,org},
     *   dedup{match,lead_id,reason,school,dm_name,days_ago},
     *   ocr_provider, ocr_ms, error
     * }
     */
    public function process_scan($uid, $image_url, $image_path)
    {
        $ocr = $this->_run_ocr($image_path);

        if (!$ocr['ok']) {
            $scan_id = $this->_write_log($uid, $image_url, null, null, null,
                                         null, $ocr['provider'], $ocr['ms'],
                                         'discarded', null, null, 'ocr_failed');
            return ['ok' => false, 'scan_id' => $scan_id,
                    'error' => 'ocr_failed: ' . $ocr['error'],
                    'ocr_provider' => $ocr['provider'], 'ocr_ms' => $ocr['ms']];
        }

        $raw_text   = $ocr['text'];
        $parsed     = $this->_regex_extract($raw_text);
        $parsed     = $this->_llm_cleanup($parsed, $raw_text);
        $confidence = $this->_score_confidence($parsed);
        $dedup      = $this->_run_dedup($parsed);

        $scan_id = $this->_write_log($uid, $image_url, $raw_text, $parsed,
                                     $confidence, $dedup, $ocr['provider'],
                                     $ocr['ms'], 'pending', null, null, null);

        return ['ok' => true, 'scan_id' => $scan_id, 'parsed' => $parsed,
                'confidence' => $confidence, 'dedup' => $dedup,
                'ocr_provider' => $ocr['provider'], 'ocr_ms' => $ocr['ms'],
                'error' => null];
    }

    /**
     * Confirm a scan. Updates status, links lead_id, persists BD-edited values.
     *
     * @param  int    $scan_id
     * @param  int    $lead_id         init_call.id
     * @param  string $status          matched_existing or new_lead
     * @param  array  $confirmed_values BD-reviewed field values
     * @return array { ok, error }
     */
    public function confirm_scan($scan_id, $lead_id, $status, $confirmed_values)
    {
        if (!in_array($status, ['matched_existing', 'new_lead'], true)) {
            return ['ok' => false, 'error' => 'invalid_status'];
        }
        $this->db->where('id', (int)$scan_id);
        $this->db->update('card_scan_log', [
            'lead_id'            => (int)$lead_id,
            'status'             => $status,
            'confirmed_at'       => date('Y-m-d H:i:s'),
            'parsed_name'        => $confirmed_values['name']        ?? null,
            'parsed_designation' => $confirmed_values['designation'] ?? null,
            'parsed_email'       => $confirmed_values['email']       ?? null,
            'parsed_phone'       => $confirmed_values['phone']       ?? null,
            'parsed_org'         => $confirmed_values['org']         ?? null,
        ]);
        return ['ok' => true, 'error' => null];
    }

    /** Mark a scan as discarded. */
    public function discard_scan($scan_id, $reason = 'bd_cancelled')
    {
        $this->db->where('id', (int)$scan_id);
        $this->db->update('card_scan_log', ['status' => 'discarded',
                                             'discarded_reason' => $reason]);
        return ['ok' => true];
    }

    /** Return dedup candidates for a scan_id (used by GET dedup_candidates). */
    public function get_dedup_candidates($scan_id)
    {
        $scan = $this->db->where('id', (int)$scan_id)->get('card_scan_log')->row_array();
        if (!$scan) return ['ok' => false, 'error' => 'scan_not_found'];
        $dedup = $this->_run_dedup([
            'email' => $scan['parsed_email'],
            'phone' => $scan['parsed_phone'],
            'org'   => $scan['parsed_org'],
        ]);
        return ['ok' => true, 'dedup' => $dedup];
    }

    // =========================================================================
    // PRIVATE: OCR
    // =========================================================================

    /** Try Vision, fall back to Textract. Returns { ok, text, provider, ms, error }. */
    private function _run_ocr($image_path)
    {
        $t0     = microtime(true);
        $vision = $this->_call_google_vision($image_path);
        $ms     = (int)((microtime(true) - $t0) * 1000);

        if ($vision['ok'] && strlen($vision['text']) >= self::VISION_MIN_CHARS) {
            return ['ok' => true, 'text' => $vision['text'],
                    'provider' => 'vision', 'ms' => $ms, 'error' => null];
        }

        $t1       = microtime(true);
        $textract = $this->_call_aws_textract($image_path);
        $ms2      = (int)((microtime(true) - $t1) * 1000);

        if ($textract['ok'] && strlen($textract['text']) >= self::VISION_MIN_CHARS) {
            return ['ok' => true, 'text' => $textract['text'],
                    'provider' => 'textract', 'ms' => $ms2, 'error' => null];
        }

        return ['ok' => false, 'text' => '', 'provider' => 'textract',
                'ms' => $ms2, 'error' => $textract['error'] ?? 'empty_text'];
    }

    /** Google Vision DOCUMENT_TEXT_DETECTION via REST. */
    private function _call_google_vision($image_path)
    {
        if (!$this->vision_key) {
            return ['ok' => false, 'text' => '', 'error' => 'vision_key_not_configured'];
        }
        $body = json_encode(['requests' => [[
            'image'        => ['content' => base64_encode(file_get_contents($image_path))],
            'features'     => [['type' => 'DOCUMENT_TEXT_DETECTION']],
            'imageContext' => ['languageHints' => ['en']]
        ]]]);
        $ch = curl_init(self::VISION_URL . '?key=' . $this->vision_key);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 8,
        ]);
        $raw  = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($err || $http !== 200) {
            return ['ok' => false, 'text' => '', 'error' => "vision_http_{$http}: {$err}"];
        }
        $resp = json_decode($raw, true);
        $text = $resp['responses'][0]['fullTextAnnotation']['text'] ?? '';
        return ['ok' => true, 'text' => $text];
    }

    /**
     * AWS Textract DetectDocumentText via Signature V4.
     * Sends image bytes directly; no S3 object reference needed.
     */
    private function _call_aws_textract($image_path)
    {
        if (!$this->aws_key || !$this->aws_secret) {
            return ['ok' => false, 'text' => '', 'error' => 'textract_keys_not_configured'];
        }
        $payload    = json_encode(['Document' => ['Bytes' => base64_encode(file_get_contents($image_path))]]);
        $region     = self::TEXTRACT_REGION;
        $host       = "textract.{$region}.amazonaws.com";
        $amz_date   = gmdate('Ymd\THis\Z');
        $date_stamp = gmdate('Ymd');
        $canon_hdrs = "content-type:application/x-amz-json-1.1\nhost:{$host}\nx-amz-date:{$amz_date}\nx-amz-target:Amazon_Textract.DetectDocumentText\n";
        $signed     = 'content-type;host;x-amz-date;x-amz-target';
        $phash      = hash('sha256', $payload);
        $canon_req  = "POST\n/\n\n{$canon_hdrs}\n{$signed}\n{$phash}";
        $scope      = "{$date_stamp}/{$region}/textract/aws4_request";
        $sts        = "AWS4-HMAC-SHA256\n{$amz_date}\n{$scope}\n" . hash('sha256', $canon_req);
        $k_date     = hash_hmac('sha256', $date_stamp, 'AWS4' . $this->aws_secret, true);
        $k_region   = hash_hmac('sha256', $region,     $k_date,   true);
        $k_svc      = hash_hmac('sha256', 'textract',  $k_region, true);
        $k_sign     = hash_hmac('sha256', 'aws4_request', $k_svc, true);
        $sig        = hash_hmac('sha256', $sts, $k_sign);
        $auth       = "AWS4-HMAC-SHA256 Credential={$this->aws_key}/{$scope}, SignedHeaders={$signed}, Signature={$sig}";
        $ch = curl_init("https://{$host}/");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-amz-json-1.1',
                "Host: {$host}", "X-Amz-Date: {$amz_date}",
                'X-Amz-Target: Amazon_Textract.DetectDocumentText',
                "Authorization: {$auth}",
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $raw  = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($err || $http !== 200) {
            return ['ok' => false, 'text' => '', 'error' => "textract_http_{$http}: {$err}"];
        }
        $resp  = json_decode($raw, true);
        $lines = array_filter($resp['Blocks'] ?? [], fn($b) => $b['BlockType'] === 'LINE');
        $text  = implode("\n", array_column(array_values($lines), 'Text'));
        return ['ok' => true, 'text' => $text];
    }

    // =========================================================================
    // PRIVATE: EXTRACTION
    // =========================================================================

    /** Regex extraction pass. Returns array of parsed fields (strings or null). */
    private function _regex_extract($text)
    {
        $lines  = array_values(array_filter(
            array_map('trim', explode("\n", $text)), fn($l) => strlen($l) > 0
        ));
        $parsed = ['name' => null, 'designation' => null, 'email' => null,
                   'phone' => null, 'org' => null, 'address' => null];

        // Email
        foreach ($lines as $l) {
            if (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $l, $m)) {
                $parsed['email'] = strtolower(trim($m[0])); break;
            }
        }

        // Phone: 10-digit Indian mobile or landline
        foreach ($lines as $l) {
            $d = preg_replace('/[^0-9]/', '', $l);
            if (preg_match('/(?:91)?([6-9][0-9]{9})/', $d, $m)) {
                $parsed['phone'] = $m[1]; break;
            }
        }

        // Designation: first line matching a DM keyword
        foreach ($lines as $l) {
            $lower = strtolower($l);
            foreach ($this->dm_keywords as $kw) {
                if (strpos($lower, $kw) !== false) {
                    $parsed['designation'] = trim($l); break 2;
                }
            }
        }

        // Org: first line, title-cased, no digits or commas
        foreach ($lines as $i => $l) {
            if ($i === 0 && strlen($l) > 3 && !preg_match('/[0-9,@]/', $l)) {
                $parsed['org'] = $l; break;
            }
        }

        // Name: title-cased, 2-5 words, no digits, not org or designation
        foreach ($lines as $l) {
            if (preg_match('/^[A-Z][a-zA-Z\.\s]{4,50}$/', trim($l))
                && !preg_match('/[0-9,@]/', $l)
                && str_word_count($l) >= 2 && str_word_count($l) <= 5
                && $l !== $parsed['org'] && $l !== $parsed['designation']) {
                $parsed['name'] = trim($l); break;
            }
        }

        // Address: lines with address keywords or 6-digit pin codes
        $addr = [];
        foreach ($lines as $l) {
            if (preg_match('/\b(nagar|road|street|lane|district|floor|building|plot|sector|phase|near|opp)\b/i', $l)
                || preg_match('/\b[1-9][0-9]{5}\b/', $l)) {
                $addr[] = $l;
            }
        }
        if ($addr) $parsed['address'] = implode(', ', $addr);

        return $parsed;
    }

    /**
     * GPT-4o-mini cleanup pass: normalise abbreviations, merge split names,
     * clean phone format. Returns original parsed on timeout or key missing.
     */
    private function _llm_cleanup($parsed, $raw_text)
    {
        if (!$this->openai_key) return $parsed;

        $prompt = "You are a business card parser. Given raw OCR text and initial extractions, "
            . "return a JSON object with keys: name, designation, email, phone, org, address. "
            . "Rules: 1. Expand designation abbreviations (Prin.=Principal, Dir.=Director). "
            . "2. Join name split across lines. 3. Normalise phone to 10 digits only. "
            . "4. Email lowercase. 5. Use null if field cannot be determined. "
            . "6. Do not invent values. Respond with ONLY the JSON object.\n\n"
            . "RAW TEXT:\n" . substr($raw_text, 0, 800)
            . "\n\nINITIAL:\n" . json_encode($parsed);

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'model'       => 'gpt-4o-mini',
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'max_tokens'  => 200,
                'temperature' => 0,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->openai_key,
            ],
            CURLOPT_TIMEOUT => self::LLM_TIMEOUT_S,
        ]);
        $raw  = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!$raw || $http !== 200) return $parsed;

        $content = json_decode($raw, true)['choices'][0]['message']['content'] ?? null;
        if (!$content) return $parsed;
        $cleaned = json_decode(trim($content), true);
        if (!is_array($cleaned)) return $parsed;

        foreach (['name','designation','email','phone','org','address'] as $f) {
            if (!empty($cleaned[$f])) $parsed[$f] = $cleaned[$f];
        }
        return $parsed;
    }

    // =========================================================================
    // PRIVATE: CONFIDENCE SCORING
    // =========================================================================

    /** Score each field 0.0-1.0 based on format validity and heuristics. */
    private function _score_confidence($parsed)
    {
        $s = [];

        $s['name'] = empty($parsed['name']) ? 0.0
            : (str_word_count($parsed['name']) >= 2 ? 0.90 : 0.60);

        if (empty($parsed['designation'])) {
            $s['designation'] = 0.0;
        } else {
            $exact = in_array(strtolower($parsed['designation']), $this->dm_keywords, true);
            $s['designation'] = $exact ? 0.95 : 0.75;
        }

        $s['email'] = empty($parsed['email']) ? 0.0
            : (filter_var($parsed['email'], FILTER_VALIDATE_EMAIL) ? 0.97 : 0.40);

        $s['phone'] = empty($parsed['phone']) ? 0.0
            : (preg_match('/^[6-9][0-9]{9}$/', $parsed['phone']) ? 0.95 : 0.50);

        $s['org'] = empty($parsed['org']) ? 0.0
            : (str_word_count($parsed['org']) >= 2 ? 0.85 : 0.60);

        return $s;
    }

    // =========================================================================
    // PRIVATE: DEDUP
    // =========================================================================

    /**
     * Match parsed fields against init_call rows within DEDUP_MONTHS.
     * Order: email exact > phone exact > org fuzzy (Levenshtein, PHP-side).
     */
    private function _run_dedup($parsed)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . self::DEDUP_MONTHS . ' months'));
        $sel    = 'id, compny_nm, dm_contact_name, created_at';

        // Email exact
        if (!empty($parsed['email'])) {
            $row = $this->db->select($sel)
                ->where('dm_contact_email', $parsed['email'])
                ->where('created_at >=', $cutoff)
                ->order_by('created_at', 'DESC')->limit(1)
                ->get('init_call')->row_array();
            if ($row) return $this->_dedup_hit($row, 'email_exact');
        }

        // Phone exact (digits only)
        if (!empty($parsed['phone'])) {
            $d   = preg_replace('/[^0-9]/', '', $parsed['phone']);
            $row = $this->db->select($sel . ', dm_contact_phone')
                ->where("REGEXP_REPLACE(dm_contact_phone,'[^0-9]','') = '{$d}'")
                ->where('created_at >=', $cutoff)
                ->order_by('created_at', 'DESC')->limit(1)
                ->get('init_call')->row_array();
            if ($row) return $this->_dedup_hit($row, 'phone_exact');
        }

        // Fuzzy org (PHP Levenshtein, 200-row cap)
        if (!empty($parsed['org'])) {
            $rows = $this->db->select($sel)->where('created_at >=', $cutoff)
                ->limit(200)->get('init_call')->result_array();
            $best_dist = PHP_INT_MAX;
            $best_row  = null;
            foreach ($rows as $r) {
                $dist = levenshtein(strtolower(trim($parsed['org'])),
                                    strtolower(trim($r['compny_nm'] ?? '')));
                if ($dist < $best_dist) { $best_dist = $dist; $best_row = $r; }
            }
            if ($best_row && $best_dist <= self::ORG_LEVENSHTEIN_MAX) {
                return $this->_dedup_hit($best_row, 'org_fuzzy');
            }
        }

        return ['match' => false, 'lead_id' => null, 'reason' => null,
                'school' => null, 'dm_name' => null, 'days_ago' => null];
    }

    private function _dedup_hit($row, $reason)
    {
        return [
            'match'    => true,
            'lead_id'  => (int)$row['id'],
            'reason'   => $reason,
            'school'   => $row['compny_nm'],
            'dm_name'  => $row['dm_contact_name'],
            'days_ago' => (int)round((time() - strtotime($row['created_at'])) / 86400),
        ];
    }

    // =========================================================================
    // PRIVATE: DATABASE WRITE
    // =========================================================================

    private function _write_log($uid, $image_url, $raw_text, $parsed, $confidence,
                                 $dedup, $provider, $ms, $status, $lead_id,
                                 $dedup_match_lead_id, $discarded_reason)
    {
        $dmatch_id  = $dedup_match_lead_id ?? ($dedup && $dedup['match'] ? $dedup['lead_id'] : null);
        $dmatch_rsn = ($dedup && $dedup['match']) ? $dedup['reason'] : null;

        $this->db->insert('card_scan_log', [
            'uid'                 => (int)$uid,
            'lead_id'             => $lead_id ? (int)$lead_id : null,
            'image_url'           => $image_url,
            'ocr_raw_text'        => $raw_text,
            'ocr_provider'        => $provider,
            'ocr_ms'              => $ms,
            'parsed_name'         => $parsed['name']        ?? null,
            'parsed_designation'  => $parsed['designation'] ?? null,
            'parsed_email'        => $parsed['email']       ?? null,
            'parsed_phone'        => $parsed['phone']       ?? null,
            'parsed_org'          => $parsed['org']         ?? null,
            'parsed_address'      => $parsed['address']     ?? null,
            'confidence'          => $confidence ? json_encode($confidence) : null,
            'status'              => $status,
            'dedup_match_lead_id' => $dmatch_id,
            'dedup_match_reason'  => $dmatch_rsn,
            'discarded_reason'    => $discarded_reason,
            'scanned_at'          => date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->insert_id();
    }
}
// End CardOcr_agent
