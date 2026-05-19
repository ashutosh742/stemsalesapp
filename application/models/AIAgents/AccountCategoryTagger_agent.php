<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AccountCategoryTagger - Migration 023
 *
 * Rule engine that auto-tags every init_call row with a category_code:
 *   - PSU             Govt schools, KVS, JNV, PMSHRI, Samagra Shiksha, public sector
 *   DMFT = District Mineral Foundation Trust (statutory trust per Section 9B of
 *   the Mines and Minerals Development and Regulation Act 1957). Set up in every
 *   mining-affected district. Collector / DM chairs the trust. Funds collected
 *   from mining lease royalties are spent on education, healthcare, water and
 *   skills in mining-affected areas. RM owns DMFT end-to-end from cstatus 1
 *   because the only funding door is the Collector. No BD hunting on DMFT leads.
 *
 *   - DMFT            School where the funding path is the District Mineral Foundation Trust.
 *                     RM mainbd from day 1. Identification rule TBD - founder to specify.
 *                     Placeholder rule: location in DMFT-eligible district whitelist.
 *   - ANCHOR          Corporate/trust whose CSR budget (2 percent of 3-year avg net profit per
 *                     Companies Act sec 135) is Rs 5 crore or more. One trust spans many schools.
 *                     RM owns the corporate relationship; closure is high-certainty.
 *   - STANDARD        everything else (default)
 *
 * Run nightly via cron 0c647bbd section 13.97 or on demand from /api/category_tag/refresh.
 *
 * Manual override is respected: rows with source='manual' are never overwritten by rule.
 *
 * Routes:
 *   $route['api/category_tag/probe']           = 'AccountCategory/probe';
 *   $route['api/category_tag/refresh']['post'] = 'AccountCategory/refresh';
 *   $route['api/category_tag/manual_override']['post'] = 'AccountCategory/manual_override';
 *   $route['api/category_tag/for_lead/(:num)'] = 'AccountCategory/for_lead/$1';
 */
class AccountCategoryTagger {

    private $db;

    public function __construct() {
        $CI =& get_instance();
        $this->db = $CI->db;
    }

    /**
     * Rule patterns. Order matters: PSU > DMFT > ANCHOR > STANDARD.
     */
    public function rules() {
        return array(
            'PSU' => array(
                'school_name_regex' => '/\b(kvs|kendriya\s*vidyalaya|jnv|jawahar\s*navodaya|pmshri|pm-shri|samagra\s*shiksha|sarkari|govt|government|navodaya|model\s*school|central\s*school)\b/i',
                'company_regex'     => '/\b(psu|public\s*sector|ministry|department\s*of\s*education)\b/i',
                'location_regex'    => null,
            ),
            'DMFT_ELIGIBLE_DISTRICTS' => array(
                // Districts with an active District Mineral Foundation Trust per Section 9B of
                // the Mines and Minerals Development and Regulation Act 1957. Lower-case match
                // on init_call.compny_loction.
                'location_regex'    => '/\b(keonjhar|sundargarh|jajpur|angul|jharsuguda|mayurbhanj|koraput|kalahandi|rayagada|sambalpur|dhenkanal|deogarh|bolangir|nuapada|dhanbad|bokaro|hazaribagh|ramgarh|west\s*singhbhum|east\s*singhbhum|saraikela|chatra|godda|pakur|sahebganj|raigarh|korba|bastar|dantewada|kanker|kondagaon|surguja|balod|rajnandgaon|bilaspur|janjgir|mungeli|chandrapur|gadchiroli|yavatmal|nagpur|bhandara|sindhudurg|bellary|hospet|tumkur|chitradurga|bagalkot)\b/i',
            ),
            'ANCHOR' => array(
                // Anchor uses CSR budget lookup (see _is_anchor), not regex.
                'csr_budget_threshold_rs' => 50000000,
            ),
        );
    }

    public function tag_lead($lead_id) {
        $lead_id = (int)$lead_id;
        // Respect manual override
        $existing = $this->db->where('lead_id', $lead_id)
                             ->where('source', 'manual')
                             ->get('account_category_tag')->row_array();
        if (!empty($existing)) {
            return array(
                'lead_id' => $lead_id,
                'category_code' => $existing['category_code'],
                'source' => 'manual',
                'changed' => false,
            );
        }

        $row = $this->db->select('id, compny_nm, compny_loction, mainbd, createDate')
                        ->where('id', $lead_id)->get('init_call')->row_array();
        if (empty($row)) {
            return array('error' => 'lead not found');
        }

        $school = strtolower((string)$row['compny_nm']);
        $loc    = strtolower((string)$row['compny_loction']);

        $code = $this->_apply_rules($school, $loc, $lead_id);
        return $this->_persist($lead_id, $code);
    }

    private function _apply_rules($school, $loc, $lead_id) {
        $rules = $this->rules();

        // PSU first
        if (!empty($rules['PSU']['school_name_regex']) && preg_match($rules['PSU']['school_name_regex'], $school)) {
            return 'PSU';
        }
        if (!empty($rules['PSU']['company_regex']) && preg_match($rules['PSU']['company_regex'], $school)) {
            return 'PSU';
        }

        // ANCHOR before DMFT - corporate CSR sponsor track is independent of district
        if ($this->_is_anchor($lead_id)) {
            return 'ANCHOR';
        }

        // DMFT - school in a DMFT-eligible mining district. RM works the Collector
        // for districtwide allocation. No prerequisite (NO seed dependency).
        // FOUNDER TO SPECIFY EXACT IDENTIFICATION RULE - placeholder is whitelist match.
        if ($this->_is_dmft($lead_id, $school, $loc, $rules)) {
            return 'DMFT';
        }

        return 'STANDARD';
    }

    /**
     * DMFT: school sits in a District Mineral Foundation Trust eligible area.
     * RM mainbd from cstatus 1. The funding door is the Collector / DM who chairs
     * the trust. No dependency on prior CSR wins, no "seed school" prerequisite.
     *
     * Two-signal identification (founder-locked 16 May 2026):
     *   Signal A (primary): school name + district matches a row in
     *     dmft_portal_snapshot within last 90 days. Portal scraper job runs weekly
     *     against dmft.gov.in and dumps current schools into the snapshot table.
     *   Signal B (secondary fallback): school's location matches one of the
     *     pan-India mining-affected districts in dmft_eligible_district. Used
     *     when the portal snapshot does not have a row for the school yet.
     *
     * Returns true if either signal fires. RM can always override via
     * account_category_tag.set_manual=1.
     */
    private function _is_dmft($lead_id, $school_name, $loc, $rules) {
        // Signal A: DMFT portal snapshot match
        if (!empty($school_name) && !empty($loc)) {
            $norm = $this->_normalise_school_name($school_name);
            $district = $this->_extract_district_token($loc);
            if (!empty($norm) && !empty($district)) {
                $r = $this->db->query("
                    SELECT 1
                    FROM dmft_portal_snapshot
                    WHERE school_name_normalised = ?
                      AND district_token = ?
                      AND snapshot_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                    LIMIT 1
                ", array($norm, $district))->row();
                if ($r) return true;
            }
        }

        // Signal B: pan-India mining-district whitelist fallback
        if (empty($rules['DMFT_ELIGIBLE_DISTRICTS']['location_regex']) || empty($loc)) return false;
        return (bool)preg_match($rules['DMFT_ELIGIBLE_DISTRICTS']['location_regex'], $loc);
    }

    private function _normalise_school_name($s) {
        $s = strtolower(trim($s));
        $s = preg_replace('/[^a-z0-9 ]/', ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    private function _extract_district_token($loc) {
        $loc = strtolower(trim($loc));
        // Try last comma-separated token first ("X school, Keonjhar, Odisha" -> "keonjhar")
        $parts = array_map('trim', explode(',', $loc));
        $tokens = array_reverse($parts);
        // Skip state names that commonly trail location strings
        $states = array('odisha','jharkhand','chhattisgarh','maharashtra','karnataka',
                        'andhra pradesh','telangana','madhya pradesh','rajasthan',
                        'goa','gujarat','tamil nadu');
        foreach ($tokens as $tok) {
            if (empty($tok) || in_array($tok, $states)) continue;
            return $tok;
        }
        return '';
    }

    /**
     * Anchor logic: the corporate/trust funding this school has CSR budget over Rs 5 crore.
     * CSR budget = 2 percent of avg net profit of last 3 financial years (Companies Act sec 135).
     *
     * Lookup path:
     *   init_call.corporate_sponsor_id -> corporate_sponsor.id
     *   corporate_sponsor.csr_budget_rs OR (avg of net_profit_fy1/2/3 * 0.02)
     *
     * A single trust may sponsor many schools - we tag ALL of those schools ANCHOR because
     * RM owns the corporate relationship at the trust level, not the individual school level.
     */
    private function _is_anchor($lead_id) {
        $threshold = 50000000; // Rs 5 crore
        $r = $this->db->query("
            SELECT cs.csr_budget_rs,
                   cs.net_profit_fy1_rs, cs.net_profit_fy2_rs, cs.net_profit_fy3_rs
            FROM init_call ic
            JOIN corporate_sponsor cs ON cs.id = ic.corporate_sponsor_id
            WHERE ic.id = ?
        ", array($lead_id))->row();
        if (!$r) return false;

        // Prefer explicit csr_budget_rs if maintained
        if (!empty($r->csr_budget_rs) && (float)$r->csr_budget_rs >= $threshold) {
            return true;
        }

        // Compute from 3-FY net profit if explicit budget missing
        $fy = array_filter(array(
            (float)$r->net_profit_fy1_rs,
            (float)$r->net_profit_fy2_rs,
            (float)$r->net_profit_fy3_rs,
        ));
        if (count($fy) === 0) return false;
        $avg_profit = array_sum($fy) / count($fy);
        $computed_csr = $avg_profit * 0.02;
        return $computed_csr >= $threshold;
    }

    private function _persist($lead_id, $code) {
        $existing = $this->db->where('lead_id', $lead_id)->get('account_category_tag')->row_array();
        $confidence = ($code === 'STANDARD') ? 0.5 : 0.9;

        if (empty($existing)) {
            $this->db->insert('account_category_tag', array(
                'lead_id'       => $lead_id,
                'category_code' => $code,
                'source'        => 'rule',
                'confidence'    => $confidence,
                'tagged_at'     => date('Y-m-d H:i:s'),
            ));
            // Also write category_code back to init_call for fast filtering
            $this->db->where('id', $lead_id)->update('init_call', array(
                'category_code'  => $code,
                'category_set_at'=> date('Y-m-d H:i:s'),
            ));
            return array('lead_id'=>$lead_id, 'category_code'=>$code, 'source'=>'rule', 'changed'=>true, 'created'=>true);
        }
        if ($existing['category_code'] !== $code && $existing['source'] !== 'manual') {
            $this->db->where('lead_id', $lead_id)->update('account_category_tag', array(
                'category_code' => $code,
                'source'        => 'rule',
                'confidence'    => $confidence,
                'tagged_at'     => date('Y-m-d H:i:s'),
            ));
            $this->db->where('id', $lead_id)->update('init_call', array(
                'category_code'  => $code,
                'category_set_at'=> date('Y-m-d H:i:s'),
            ));
            return array('lead_id'=>$lead_id, 'category_code'=>$code, 'source'=>'rule', 'changed'=>true, 'previous'=>$existing['category_code']);
        }
        return array('lead_id'=>$lead_id, 'category_code'=>$code, 'source'=>'rule', 'changed'=>false);
    }

    /**
     * Bulk refresh - run nightly. Caps at 5000 per call to keep cron under 5 min.
     */
    public function refresh_all($limit = 5000) {
        $limit = (int)$limit;
        $sql = "
            SELECT ic.id
            FROM init_call ic
            LEFT JOIN account_category_tag t ON t.lead_id = ic.id
            WHERE t.id IS NULL
               OR t.source != 'manual'
               OR ic.category_set_at IS NULL
               OR ic.category_set_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY ic.id DESC
            LIMIT ?
        ";
        $ids = $this->db->query($sql, array($limit))->result_array();
        $stats = array('tagged'=>0, 'changed'=>0, 'unchanged'=>0, 'errors'=>0);
        foreach ($ids as $row) {
            $res = $this->tag_lead($row['id']);
            if (!empty($res['error'])) { $stats['errors']++; continue; }
            $stats['tagged']++;
            if (!empty($res['changed'])) $stats['changed']++;
            else $stats['unchanged']++;
        }
        return $stats;
    }

    /**
     * Manual override (CM or RM action).
     */
    public function manual_override($lead_id, $category_code, $set_by_uid, $reason) {
        $lead_id = (int)$lead_id;
        $category_code = strtoupper($category_code);
        if (!in_array($category_code, array('PSU','DMFT','ANCHOR','STANDARD'))) {
            return array('ok'=>false, 'error'=>'invalid category_code');
        }
        $now = date('Y-m-d H:i:s');
        $existing = $this->db->where('lead_id', $lead_id)->get('account_category_tag')->row_array();
        $payload = array(
            'lead_id'       => $lead_id,
            'category_code' => $category_code,
            'source'        => 'manual',
            'confidence'    => 1.0,
            'set_by_uid'    => (int)$set_by_uid,
            'override_reason'=> $reason,
            'tagged_at'     => $now,
        );
        if (empty($existing)) {
            $this->db->insert('account_category_tag', $payload);
        } else {
            $this->db->where('lead_id', $lead_id)->update('account_category_tag', $payload);
        }
        $this->db->where('id', $lead_id)->update('init_call', array(
            'category_code'  => $category_code,
            'category_set_at'=> $now,
        ));
        return array('ok'=>true, 'lead_id'=>$lead_id, 'category_code'=>$category_code, 'source'=>'manual');
    }

    public function for_lead($lead_id) {
        return $this->db->where('lead_id', (int)$lead_id)->get('account_category_tag')->row_array();
    }
}


/**
 * Controller wrapper for the tagger.
 */
class AccountCategory extends CI_Controller {

    private $tagger;

    public function __construct() {
        parent::__construct();
        $this->_check_bearer();
        header('Content-Type: application/json');
        $this->tagger = new AccountCategoryTagger();
    }

    private function _check_bearer() {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        $tok = getenv('STEM_DIGEST_TOKEN');
        if (empty($tok)) return;
        if (empty($hdr) || strpos($hdr, 'Bearer ') !== 0
            || !hash_equals($tok, trim(substr($hdr, 7)))) {
            http_response_code(401);
            echo json_encode(array('ok'=>false, 'error'=>'unauthorized'));
            exit;
        }
    }

    public function probe() {
        echo json_encode(array(
            'ok'             => true,
            'migration'      => '023',
            'feature'        => 'account_category_tagger',
            'deployed_at'    => '2026-05-25',
            'categories'     => array('PSU','DMFT','ANCHOR','STANDARD'),
            'rules_active'   => array_keys($this->tagger->rules()),
            'anchor_thresholds' => array(
                'contract_value_rs' => 5000000,
                'cohort_count'      => 50,
                'window_days'       => 365,
            ),
        ));
    }

    public function refresh() {
        $limit = (int)$this->input->post('limit') ?: 5000;
        echo json_encode(array(
            'ok'    => true,
            'stats' => $this->tagger->refresh_all($limit),
        ));
    }

    public function manual_override() {
        $lead_id  = (int)$this->input->post('lead_id');
        $code     = $this->input->post('category_code');
        $set_by   = (int)$this->input->post('set_by_uid');
        $reason   = $this->input->post('reason');
        if (empty($lead_id) || empty($code) || empty($set_by)) {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'lead_id, category_code, set_by_uid required'));
            return;
        }
        echo json_encode($this->tagger->manual_override($lead_id, $code, $set_by, $reason));
    }

    public function for_lead($lead_id) {
        echo json_encode(array(
            'ok'   => true,
            'data' => $this->tagger->for_lead($lead_id),
        ));
    }
}

/* End of file account_category_tagger.php */
/* Two PHP classes: AccountCategoryTagger (logic) + AccountCategory (CI controller) */
