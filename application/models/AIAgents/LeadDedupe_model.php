<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadDedupe_model - Agent (additive, 2026-06-06)
 *
 * Surfaces likely duplicate leads/companies. Two real data sources:
 *   1. dup_check_log (563 real rows): historical near-duplicate matches with
 *      similarity_pct and match_method (soundex/like).
 *   2. Live check: given a company name, find similar existing company_master
 *      rows via SOUNDEX and substring match (no mock data).
 *
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class LeadDedupe_model extends CI_Model {

    public function manifest() {
        $n = (int)$this->db->query("SELECT COUNT(*) c FROM dup_check_log")->row()->c;
        $hi = (int)$this->db->query("SELECT COUNT(*) c FROM dup_check_log WHERE similarity_pct >= 90")->row()->c;
        return array(
            'feature'        => 'lead_dedupe',
            'source_tables'  => array('dup_check_log','company_master','init_call'),
            'logged_checks'  => $n,
            'high_conf_dups' => $hi,
            'deployed_at'    => '2026-06-06',
        );
    }

    /** Recent logged near-duplicate pairs (real dup_check_log rows). */
    public function recent_dups($min_pct = 85, $limit = 50) {
        $min_pct = (int)$min_pct; $limit = (int)$limit;
        $rows = $this->db->query("
            SELECT id, query_compname, matched_compname, matched_cmpid,
                   similarity_pct, match_method, checked_at
            FROM dup_check_log
            WHERE similarity_pct >= ?
            ORDER BY checked_at DESC, similarity_pct DESC
            LIMIT ?", array($min_pct, $limit))->result_array();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'             => (int)$r['id'],
                'query_name'     => $r['query_compname'],
                'matched_name'   => $r['matched_compname'],
                'matched_cmpid'  => $r['matched_cmpid'] !== null ? (int)$r['matched_cmpid'] : null,
                'similarity_pct' => (int)$r['similarity_pct'],
                'match_method'   => $r['match_method'],
                'checked_at'     => $r['checked_at'],
            );
        }
        return $out;
    }

    /** Live duplicate check for a company name against company_master. */
    public function check_name($name, $limit = 10) {
        $name = trim($name);
        if ($name === '') return array();
        $like = '%' . $name . '%';
        $rows = $this->db->query("
            SELECT id, compname, city, state,
                   CASE
                     WHEN LOWER(compname) = LOWER(?) THEN 100
                     WHEN SOUNDEX(compname) = SOUNDEX(?) THEN 92
                     ELSE 80
                   END AS similarity_pct
            FROM company_master
            WHERE compname LIKE ? OR SOUNDEX(compname) = SOUNDEX(?)
            ORDER BY similarity_pct DESC, compname ASC
            LIMIT ?",
            array($name, $name, $like, $name, (int)$limit))->result_array();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'cmpid'          => (int)$r['id'],
                'compname'       => $r['compname'],
                'city'           => $r['city'],
                'state'          => $r['state'],
                'similarity_pct' => (int)$r['similarity_pct'],
                'verdict'        => (int)$r['similarity_pct'] >= 90 ? 'LIKELY_DUPLICATE' : 'POSSIBLE_MATCH',
            );
        }
        return $out;
    }
}
