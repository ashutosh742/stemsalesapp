<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DmVerifyAgent_model
 *
 * Verifies whether a DM contact on a CID is a genuine CSR head.
 * Combines Apollo API enrichment with a LinkedIn public profile fetch.
 *
 * Verdict rules:
 *   verified  - combined_score >= 70 AND csr_keyword_found = 1
 *   doubtful  - combined_score 40 to 69, OR Apollo says company mismatch
 *   not_csr   - combined_score < 40, OR title clearly non-CSR (sales, finance)
 *   pending   - either API failed or quota hit, retry next cycle
 *
 * CID cannot move to cstatus 6 (Positive) until verdict is verified.
 * Doubtful or not_csr verdicts surface in CM inbox and on lead detail badge.
 *
 * Required env:
 *   APOLLO_API_KEY  - paid API key, used as Bearer header
 *   LINKEDIN_FETCH_RATE_LIMIT  - max requests per minute (default 30)
 *
 * Migration 024.
 * Author: STEM ops, 2026-05-17.
 */
class DmVerifyAgent_model extends CI_Model
{
    private $apollo_endpoint = 'https://api.apollo.io/v1/people/match';
    private $apollo_quota_daily = 200;

    /** CSR-positive keywords. Title must contain at least one. */
    private $csr_positive = [
        'csr', 'sustainability', 'foundation', 'esg',
        'social impact', 'community relations', 'philanthropy',
        'chairperson', 'chairman', 'trustee'
    ];

    /** Strong-negative keywords. If title contains these, mark not_csr. */
    private $csr_negative = [
        'sales', 'business development', 'account executive',
        'finance', 'accounting', 'hr', 'recruiter',
        'engineer', 'developer', 'qa', 'support'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------------
    // PUBLIC: enqueue a new DM contact for verification.
    // Called when BD adds DM details to a CID in mom_v2 or lead detail.
    // ------------------------------------------------------------------------
    public function enqueue($cid_id, $bd_uid, $dm_name, $dm_designation,
                            $dm_email, $dm_phone, $company_name)
    {
        $this->db->query("
            INSERT INTO dm_verification
                (cid_id, bd_uid, dm_name, dm_designation, dm_email,
                 dm_phone, company_name, verdict)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ON DUPLICATE KEY UPDATE
                dm_designation = VALUES(dm_designation),
                dm_email       = VALUES(dm_email),
                dm_phone       = VALUES(dm_phone),
                updated_at     = NOW()
        ", [$cid_id, $bd_uid, $dm_name, $dm_designation,
            $dm_email, $dm_phone, $company_name]);
        return $this->db->insert_id() ?: $this->find_id($cid_id, $dm_name);
    }

    private function find_id($cid_id, $dm_name)
    {
        $row = $this->db->query("
            SELECT id FROM dm_verification
             WHERE cid_id = ? AND dm_name = ? LIMIT 1
        ", [$cid_id, $dm_name])->row_array();
        return $row ? (int)$row['id'] : null;
    }

    // ------------------------------------------------------------------------
    // RUN ONE: process a single pending row, both Apollo and LinkedIn.
    // ------------------------------------------------------------------------
    public function process_one($id)
    {
        $row = $this->db->query("
            SELECT * FROM dm_verification WHERE id = ? LIMIT 1
        ", [$id])->row_array();
        if (!$row) return ['error' => 'not_found'];

        // Step 1: Apollo
        $apollo = $this->call_apollo($row['dm_name'], $row['dm_email'],
                                     $row['company_name']);
        // Step 2: LinkedIn (best-effort, uses Apollo's linkedin_url if available)
        $linkedin = null;
        $li_url = $apollo['linkedin_url'] ?? null;
        if ($li_url) {
            $linkedin = $this->call_linkedin($li_url);
        }

        // Step 3: Score and verdict
        $verdict = $this->score_and_verdict($row, $apollo, $linkedin);

        // Step 4: Persist
        $this->db->query("
            UPDATE dm_verification SET
                apollo_checked = ?, apollo_match_score = ?, apollo_title = ?,
                apollo_company = ?, apollo_linkedin_url = ?, apollo_payload = ?,
                apollo_checked_at = NOW(),
                linkedin_checked = ?, linkedin_match_score = ?,
                linkedin_title = ?, linkedin_company = ?,
                linkedin_payload = ?, linkedin_checked_at = ?,
                csr_keyword_found = ?, combined_score = ?,
                verdict = ?, verdict_reason = ?,
                verdict_at = NOW(), verdict_by = 'agent'
              WHERE id = ?
        ", [
            $apollo['checked'] ? 1 : 0, $apollo['score'], $apollo['title'],
            $apollo['company'], $apollo['linkedin_url'],
            json_encode($apollo['raw'] ?? null),
            $linkedin ? 1 : 0,
            $linkedin['score'] ?? null,
            $linkedin['title'] ?? null,
            $linkedin['company'] ?? null,
            $linkedin ? json_encode($linkedin['raw'] ?? null) : null,
            $linkedin ? date('Y-m-d H:i:s') : null,
            $verdict['csr_keyword_found'] ? 1 : 0,
            $verdict['combined_score'],
            $verdict['verdict'], $verdict['reason'],
            $id,
        ]);
        return $verdict;
    }

    // ------------------------------------------------------------------------
    // APOLLO API CALL
    // POST /v1/people/match with name, email, organization_name.
    // Returns {title, organization_name, linkedin_url, ...}.
    // ------------------------------------------------------------------------
    private function call_apollo($name, $email, $company)
    {
        $key = getenv('APOLLO_API_KEY');
        if (!$key) {
            return ['checked' => false, 'score' => null, 'title' => null,
                    'company' => null, 'linkedin_url' => null, 'raw' => null,
                    'error' => 'no_api_key'];
        }
        if (!$this->within_quota()) {
            return ['checked' => false, 'score' => null, 'title' => null,
                    'company' => null, 'linkedin_url' => null, 'raw' => null,
                    'error' => 'quota_exhausted'];
        }
        $payload = json_encode([
            'name'              => $name,
            'email'             => $email,
            'organization_name' => $company,
        ]);
        $ch = curl_init($this->apollo_endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $key,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->record_quota_use();

        if ($http !== 200 || !$resp) {
            return ['checked' => false, 'score' => null, 'title' => null,
                    'company' => null, 'linkedin_url' => null, 'raw' => null,
                    'error' => 'apollo_http_' . $http];
        }
        $data = json_decode($resp, true);
        $person = $data['person'] ?? [];

        // Title-vs-company match score
        $title_match   = $this->title_match_score($person['title'] ?? '');
        $company_match = $this->company_match_score($company,
                                                    $person['organization_name'] ?? '');
        $score = (int)round(($title_match * 0.6) + ($company_match * 0.4));

        return [
            'checked'      => true,
            'score'        => $score,
            'title'        => $person['title'] ?? null,
            'company'      => $person['organization_name'] ?? null,
            'linkedin_url' => $person['linkedin_url'] ?? null,
            'raw'          => $data,
            'error'        => null,
        ];
    }

    // ------------------------------------------------------------------------
    // LINKEDIN PUBLIC PROFILE FETCH
    // No auth, public scrape, rate-limited at 30 per minute.
    // Lightweight: just confirm name and title visible on public profile.
    // ------------------------------------------------------------------------
    private function call_linkedin($profile_url)
    {
        if (!$this->within_li_rate_limit()) return null;

        $ch = curl_init($profile_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 STEMVerifyBot/1.0',
            CURLOPT_TIMEOUT        => 8,
        ]);
        $html = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->record_li_fetch();

        if ($http !== 200 || !$html) return null;

        // Minimal parse: look for og:title and og:description meta tags.
        $title   = $this->extract_meta($html, 'og:title');
        $company = $this->extract_meta($html, 'og:description');
        if (!$title) return null;

        $score = $this->title_match_score($title);
        return [
            'score'   => $score,
            'title'   => $title,
            'company' => $company,
            'raw'     => ['html_size' => strlen($html)],
        ];
    }

    private function extract_meta($html, $property)
    {
        $pattern = '/<meta property="' . preg_quote($property, '/') . '" content="([^"]+)"/i';
        if (preg_match($pattern, $html, $m)) return $m[1];
        return null;
    }

    // ------------------------------------------------------------------------
    // SCORING
    // ------------------------------------------------------------------------
    private function title_match_score($title)
    {
        if (!$title) return 0;
        $title_lc = strtolower($title);

        // Strong negative kills the score.
        foreach ($this->csr_negative as $neg) {
            if (strpos($title_lc, $neg) !== false) return 10;
        }
        // Positive keyword present
        foreach ($this->csr_positive as $pos) {
            if (strpos($title_lc, $pos) !== false) return 95;
        }
        // Soft mid (head, director, chief without CSR context)
        if (preg_match('/\b(head|director|chief|vp|svp)\b/i', $title)) {
            return 50;
        }
        return 25;
    }

    private function company_match_score($expected, $actual)
    {
        if (!$expected || !$actual) return 0;
        $a = strtolower(trim($expected));
        $b = strtolower(trim($actual));
        if ($a === $b) return 100;
        // Token overlap
        $a_tokens = preg_split('/\s+/', $a);
        $b_tokens = preg_split('/\s+/', $b);
        $overlap = count(array_intersect($a_tokens, $b_tokens));
        $denom = max(count($a_tokens), 1);
        return (int)round(100 * $overlap / $denom);
    }

    private function score_and_verdict($row, $apollo, $linkedin)
    {
        $apollo_score = $apollo['score'] ?? 0;
        $li_score     = $linkedin['score'] ?? 0;

        // Combined: Apollo 60 percent, LinkedIn 40 percent.
        // If LinkedIn not available, Apollo carries 100 percent.
        if ($linkedin) {
            $combined = (int)round($apollo_score * 0.6 + $li_score * 0.4);
        } else {
            $combined = $apollo_score;
        }

        $title_lc = strtolower(($apollo['title'] ?? '') . ' '
                              . ($linkedin['title'] ?? ''));
        $csr_kw = 0;
        foreach ($this->csr_positive as $pos) {
            if (strpos($title_lc, $pos) !== false) { $csr_kw = 1; break; }
        }
        // Strong negative override
        foreach ($this->csr_negative as $neg) {
            if (strpos($title_lc, $neg) !== false) {
                return [
                    'verdict'           => 'not_csr',
                    'reason'            => 'title contains non-CSR keyword: ' . $neg,
                    'combined_score'    => $combined,
                    'csr_keyword_found' => 0,
                ];
            }
        }

        if (!$apollo['checked']) {
            return [
                'verdict'           => 'pending',
                'reason'            => $apollo['error'] ?? 'apollo_failed',
                'combined_score'    => null,
                'csr_keyword_found' => 0,
            ];
        }

        if ($combined >= 70 && $csr_kw === 1) {
            return ['verdict' => 'verified',
                    'reason' => 'apollo plus linkedin score ' . $combined,
                    'combined_score' => $combined,
                    'csr_keyword_found' => 1];
        }
        if ($combined >= 40) {
            return ['verdict' => 'doubtful',
                    'reason' => 'combined score ' . $combined . ', no CSR keyword',
                    'combined_score' => $combined,
                    'csr_keyword_found' => $csr_kw];
        }
        return ['verdict' => 'not_csr',
                'reason' => 'combined score below 40',
                'combined_score' => $combined,
                'csr_keyword_found' => $csr_kw];
    }

    // ------------------------------------------------------------------------
    // QUOTA AND RATE LIMITS (per-day file counter, swap for redis in prod)
    // ------------------------------------------------------------------------
    private function quota_file() { return sys_get_temp_dir() . '/apollo_quota_' . date('Y-m-d') . '.txt'; }
    private function li_file()    { return sys_get_temp_dir() . '/li_fetch_' . date('Y-m-d-H-i') . '.txt'; }

    private function within_quota()
    {
        $f = $this->quota_file();
        $n = file_exists($f) ? (int)file_get_contents($f) : 0;
        return $n < $this->apollo_quota_daily;
    }

    private function record_quota_use()
    {
        $f = $this->quota_file();
        $n = file_exists($f) ? (int)file_get_contents($f) : 0;
        file_put_contents($f, $n + 1);
    }

    private function within_li_rate_limit()
    {
        $limit = (int)(getenv('LINKEDIN_FETCH_RATE_LIMIT') ?: 30);
        $f = $this->li_file();
        $n = file_exists($f) ? (int)file_get_contents($f) : 0;
        return $n < $limit;
    }

    private function record_li_fetch()
    {
        $f = $this->li_file();
        $n = file_exists($f) ? (int)file_get_contents($f) : 0;
        file_put_contents($f, $n + 1);
    }

    // ------------------------------------------------------------------------
    // BATCH RUN: process up to N pending rows.
    // ------------------------------------------------------------------------
    public function run_batch($limit = 50)
    {
        $rows = $this->db->query("
            SELECT id FROM dm_verification
             WHERE verdict = 'pending'
             ORDER BY created_at ASC
             LIMIT ?
        ", [(int)$limit])->result_array();

        $out = ['processed' => 0, 'verified' => 0, 'doubtful' => 0,
                'not_csr' => 0, 'still_pending' => 0];
        foreach ($rows as $r) {
            $v = $this->process_one($r['id']);
            $out['processed']++;
            $key = $v['verdict'] === 'pending' ? 'still_pending' : $v['verdict'];
            if (isset($out[$key])) $out[$key]++;
        }
        $out['ran_at'] = date('c');
        return $out;
    }
}
