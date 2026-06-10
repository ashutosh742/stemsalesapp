<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LinkedinCsr_model - the LinkedIn CSR verification agent.
 *
 * Migration 021. Staging only until 18 May 2026.
 *
 * What it does:
 *   - Fires at MoM submit when DM org_type in (corporate, ngo, foundation, csr_arm, trust)
 *     AND designation contains a CSR keyword.
 *   - Queries Google with site:linkedin.com/in operator, reads public snippets only.
 *   - Scores csr_intent_confidence 0 to 100, returns verdict.
 *   - 5 second hard timeout. Cache by (name + org_type + school) for 30 days.
 *   - Daily cap 200 checks.
 *   - Also checks dm_email domain against verified company name (penalty -10 if mismatch).
 *   - Also checks the highest-sanction approving authority (one additional check).
 *
 * Privacy: only public Google snippets. No LinkedIn login. No scraping.
 * BD can opt out per MoM via opt_out flag.
 */
class LinkedinCsr_model extends CI_Model {

    const TIMEOUT_SECONDS  = 5;
    const DAILY_CAP        = 200;
    const CACHE_DAYS       = 30;
    const SEARCH_ENDPOINT  = 'https://www.googleapis.com/customsearch/v1';  // configurable

    private $csr_role_keywords = [
        'csr','corporate social','sustainability','foundation','trust','philanthropy',
        'social impact','community','esg','csr committee','csr head','csr manager',
        'csr lead','sustainability head','foundation trustee','csr trustee'
    ];

    private $penalty_keywords = [
        'hr head','admin','executive assistant','office manager','receptionist',
        'it head','it manager','marketing only'
    ];

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ============================================================
    // PUBLIC API
    // ============================================================

    /**
     * verify_sync - run the check now and return the result inline.
     * Caller blocks for up to TIMEOUT_SECONDS.
     *
     * @param array $input  keys: mom_id, cid_id, dm_contact_name, dm_contact_designation,
     *                            dm_contact_org_type, dm_contact_email, school_name,
     *                            optional opt_out (bool)
     * @return array  { ok, verdict, csr_intent_confidence, csr_check_id, rubric }
     */
    public function verify_sync($input) {
        // Opt out
        if (!empty($input['opt_out'])) {
            return $this->_persist_and_return($input, 'opt_out', 0, [], null, null, null);
        }

        // Daily cap
        if ($this->_daily_cap_reached()) {
            return $this->_persist_and_return($input, 'rate_limit_hit', 0, [], null, null, null);
        }

        // Cache hit
        $cache_key = strtolower(
            ($input['dm_contact_name'] ?? '') . '|' .
            ($input['dm_contact_org_type'] ?? '') . '|' .
            ($input['school_name'] ?? '')
        );
        $cached = $this->_lookup_cache($cache_key);
        if ($cached) {
            // Re-link to this MoM but reuse verdict
            $row = $cached;
            $row['mom_id'] = $input['mom_id'];
            return $this->_persist_and_return(
                $input, $cached['verdict'], $cached['csr_intent_confidence'],
                json_decode($cached['rubric_breakdown'] ?? '[]', true),
                $cached['candidate_profile_url'], $cached['candidate_headline'],
                $cached['candidate_company']
            );
        }

        // Fresh fetch
        $started_at = microtime(true);
        $snippets = $this->_fetch_google_snippets($input);
        $elapsed = microtime(true) - $started_at;

        if (empty($snippets) || $elapsed > self::TIMEOUT_SECONDS) {
            return $this->_persist_and_return($input, 'no_match', 0,
                ['timeout' => $elapsed > self::TIMEOUT_SECONDS], null, null, null);
        }

        $best = $this->_pick_best_match($snippets, $input);
        $score_result = $this->_score($best, $input);

        $verdict = $this->_verdict_from_score($score_result['score']);

        $result = $this->_persist_and_return(
            $input, $verdict, $score_result['score'], $score_result['rubric'],
            $best['url'] ?? null, $best['headline'] ?? null, $best['company'] ?? null
        );

        // Bump daily counter
        $this->_increment_daily_counter();

        // Also check primary authority if present (max 1 additional fetch)
        if (!empty($input['primary_authority']) && is_array($input['primary_authority'])) {
            $auth = $input['primary_authority'];
            $this->verify_sync([
                'mom_id' => $input['mom_id'],
                'cid_id' => $input['cid_id'],
                'dm_contact_name' => $auth['name'] ?? '',
                'dm_contact_designation' => $auth['designation'] ?? '',
                'dm_contact_org_type' => $input['dm_contact_org_type'],
                'school_name' => $input['school_name'] ?? null,
                '_is_authority_check' => true
            ]);
        }

        return $result;
    }

    // ============================================================
    // GOOGLE SNIPPET FETCH
    // ============================================================

    private function _fetch_google_snippets($input) {
        $name = $input['dm_contact_name'] ?? '';
        $org  = $input['school_name'] ?? '';

        // Build query: site:linkedin.com/in "Name" "Company"
        $query = 'site:linkedin.com/in "' . $name . '"';
        if ($org) $query .= ' "' . $org . '"';

        // For deployment we use Google Custom Search API or a similar permitted fetch.
        // Staging uses curl wrapper. Production switches to whatever the host policy approves.
        $api_key = getenv('GOOGLE_CSE_KEY')
                ?: (defined('GOOGLE_CSE_KEY') ? GOOGLE_CSE_KEY : '');
        $cse_id  = getenv('GOOGLE_CSE_ID')
                ?: (defined('GOOGLE_CSE_ID') ? GOOGLE_CSE_ID : '');
        if (!$api_key || !$cse_id) {
            log_message('error', 'CSR agent: Google CSE credentials missing');
            return [];
        }

        $url = self::SEARCH_ENDPOINT . '?' . http_build_query([
            'key' => $api_key,
            'cx'  => $cse_id,
            'q'   => $query,
            'num' => 3
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => (int)self::TIMEOUT_SECONDS - 1,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'StemLearningCSRAgent/1.0'
        ]);
        $body = curl_exec($ch);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err || !$body) {
            log_message('error', 'CSR agent fetch error: ' . $err);
            return [];
        }

        $json = json_decode($body, true);
        $items = $json['items'] ?? [];
        $snippets = [];
        foreach ($items as $it) {
            $snippets[] = [
                'url'      => $it['link'] ?? '',
                'title'    => $it['title'] ?? '',
                'headline' => $it['snippet'] ?? '',
                'company'  => $this->_extract_company($it['snippet'] ?? '')
            ];
        }
        return $snippets;
    }

    private function _extract_company($snippet) {
        // LinkedIn snippets often contain " at COMPANY " - simple extraction
        if (preg_match('/ at ([A-Za-z0-9 &.\'\-]+?)(\.|,| \||$)/i', $snippet, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    // ============================================================
    // MATCH PICKING + SCORING
    // ============================================================

    private function _pick_best_match($snippets, $input) {
        $org = strtolower($input['school_name'] ?? '');
        $best = null;
        $best_score = -1;
        foreach ($snippets as $s) {
            $score = 0;
            $hl = strtolower($s['headline'] ?? '');
            $co = strtolower($s['company'] ?? '');
            if ($org && (strpos($hl, $org) !== false || strpos($co, $org) !== false)) $score += 5;
            foreach ($this->csr_role_keywords as $kw) {
                if (strpos($hl, $kw) !== false) { $score += 2; break; }
            }
            if (!empty($s['url'])) $score += 1;
            if ($score > $best_score) {
                $best = $s;
                $best_score = $score;
            }
        }
        return $best ?: [];
    }

    private function _score($best, $input) {
        $rubric = [];
        $score = 0;

        if (empty($best)) {
            $rubric['no_match_found'] = -25;
            return ['score' => 0, 'rubric' => $rubric];
        }

        $hl = strtolower($best['headline'] ?? '');
        $co = strtolower($best['company'] ?? '');
        $org = strtolower($input['school_name'] ?? '');
        $des = strtolower($input['dm_contact_designation'] ?? '');

        // +30 headline has CSR keyword
        foreach ($this->csr_role_keywords as $kw) {
            if (strpos($hl, $kw) !== false) {
                $rubric['headline_csr_keyword'] = 30;
                $score += 30;
                break;
            }
        }

        // +20 current company matches school
        if ($org && $co && (strpos($co, $org) !== false || strpos($org, $co) !== false)) {
            $rubric['company_match'] = 20;
            $score += 20;
        } elseif ($org && $hl && strpos($hl, $org) !== false) {
            $rubric['company_match_headline'] = 15;
            $score += 15;
        }

        // +15 past CSR role (heuristic: "former csr" or "previously csr")
        if (preg_match('/(former|previously|earlier|past) +(csr|sustainability|foundation)/i', $hl)) {
            $rubric['past_csr_role'] = 15;
            $score += 15;
        }

        // +15 activity signal heuristic: snippet length over 250 chars with csr keyword count >= 2
        $csr_hits = 0;
        foreach ($this->csr_role_keywords as $kw) {
            $csr_hits += substr_count($hl, $kw);
        }
        if (strlen($hl) > 250 && $csr_hits >= 2) {
            $rubric['activity_signal'] = 15;
            $score += 15;
        }

        // +10 group/committee mention
        if (preg_match('/(committee|forum|council) +(.{0,40})(csr|esg|sustainability)/i', $hl)) {
            $rubric['group_mention'] = 10;
            $score += 10;
        }

        // +5 profile completeness heuristic
        if (strlen($hl) > 200) {
            $rubric['profile_complete'] = 5;
            $score += 5;
        }

        // +5 senior tenure heuristic
        if (preg_match('/(\d{4})\s*[-to]+\s*(present|current)/i', $hl, $m)) {
            $years = (int)date('Y') - (int)$m[1];
            if ($years >= 5) {
                $rubric['senior_tenure'] = 5;
                $score += 5;
            }
        }

        // Penalty: penalty keyword in headline
        foreach ($this->penalty_keywords as $pk) {
            if (strpos($hl, $pk) !== false) {
                $rubric['penalty_role_'.str_replace(' ','_',$pk)] = -20;
                $score -= 20;
                break;
            }
        }

        // Penalty: company differs from school
        if ($org && $co && strpos($co, $org) === false && strpos($org, $co) === false) {
            $rubric['penalty_company_diff'] = -15;
            $score -= 15;
        }

        // Email domain check
        if (!empty($input['dm_contact_email']) && !empty($best['company'])) {
            $email_domain = strtolower(substr(strrchr($input['dm_contact_email'], '@'), 1));
            $co_slug = preg_replace('/[^a-z0-9]/', '', strtolower($best['company']));
            $domain_slug = preg_replace('/[^a-z0-9]/', '', $email_domain);
            if ($co_slug && $domain_slug && strpos($domain_slug, substr($co_slug, 0, min(8, strlen($co_slug)))) === false) {
                $rubric['email_domain_mismatch'] = -10;
                $score -= 10;
            }
        }

        // Cap
        $score = max(0, min(100, $score));
        return ['score' => $score, 'rubric' => $rubric];
    }

    private function _verdict_from_score($score) {
        if ($score >= 80) return 'verified';
        if ($score >= 55) return 'likely';
        if ($score >= 30) return 'doubtful';
        if ($score > 0)   return 'not_csr';
        return 'no_match';
    }

    // ============================================================
    // PERSISTENCE
    // ============================================================

    private function _persist_and_return($input, $verdict, $score, $rubric, $url, $headline, $company) {
        $row = [
            'mom_id'                 => (int)($input['mom_id'] ?? 0),
            'cid_id'                 => (int)($input['cid_id'] ?? 0),
            'dm_contact_name'        => $input['dm_contact_name'] ?? '',
            'dm_contact_designation' => $input['dm_contact_designation'] ?? '',
            'dm_contact_org_type'    => $input['dm_contact_org_type'] ?? '',
            'school_name'            => $input['school_name'] ?? null,
            'search_query'           => 'site:linkedin.com/in "' . ($input['dm_contact_name'] ?? '') . '"',
            'candidate_profile_url'  => $url,
            'candidate_headline'     => $headline,
            'candidate_company'      => $company,
            'csr_intent_confidence'  => (int)$score,
            'verdict'                => $verdict,
            'rubric_breakdown'       => json_encode($rubric),
            'raw_snippet'            => $headline,
            'ran_at'                 => date('Y-m-d H:i:s'),
            'agent_version'          => 'v1'
        ];
        $this->db->insert('mom_csr_check', $row);
        $csr_check_id = $this->db->insert_id();
        return [
            'ok' => true,
            'csr_check_id' => $csr_check_id,
            'verdict' => $verdict,
            'csr_intent_confidence' => (int)$score,
            'candidate_url' => $url,
            'rubric' => $rubric
        ];
    }

    // ============================================================
    // CACHE + RATE LIMIT
    // ============================================================

    private function _lookup_cache($cache_key) {
        $this->db->where('cache_key', $cache_key);
        $this->db->where('ran_at >=', date('Y-m-d H:i:s', strtotime('-' . self::CACHE_DAYS . ' days')));
        $this->db->where_in('verdict', ['verified','likely','doubtful','not_csr']);
        $this->db->order_by('ran_at', 'DESC');
        $this->db->limit(1);
        return $this->db->get('mom_csr_check')->row_array();
    }

    private function _daily_cap_reached() {
        $row = $this->db->where('quota_date', date('Y-m-d'))->get('csr_check_daily_quota')->row_array();
        if (!$row) return false;
        return ($row['checks_run'] ?? 0) >= self::DAILY_CAP;
    }

    private function _increment_daily_counter() {
        $today = date('Y-m-d');
        $row = $this->db->where('quota_date', $today)->get('csr_check_daily_quota')->row_array();
        if (!$row) {
            $this->db->insert('csr_check_daily_quota', ['quota_date' => $today, 'checks_run' => 1]);
        } else {
            $this->db->set('checks_run', 'checks_run+1', false);
            if ($row['checks_run'] + 1 >= self::DAILY_CAP) {
                $this->db->set('cap_reached_at', date('Y-m-d H:i:s'));
            }
            $this->db->where('quota_date', $today);
            $this->db->update('csr_check_daily_quota');
        }
    }
}
