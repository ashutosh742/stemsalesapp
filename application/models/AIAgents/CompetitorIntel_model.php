<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CompetitorIntel_model - Feature (additive, 2026-06-06)
 *
 * Mines real free-text remark fields on init_call (reject_remarks, kcremark,
 * pnpremark) for competitive / loss-reason signals. No mock data: every theme
 * count comes from a LIKE scan over real rows.
 *
 * Themes are keyword buckets (price, budget, competitor, existing-vendor,
 * timing, no-need). Workflow noise like "Approved" is ignored by requiring a
 * keyword hit.
 *
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class CompetitorIntel_model extends CI_Model {

    // theme_code => array of LIKE keywords (lowercased, scanned across remark cols)
    private $themes = array(
        'price_too_high'   => array('expensive','costly','high price','price high','too high','cost high'),
        'budget_constraint'=> array('budget','no fund','funds','financial','cannot afford','money'),
        'competitor'       => array('competitor','other vendor','another company','quote from','cheaper option'),
        'existing_vendor'  => array('already have','existing','current vendor','tie up','tie-up','already using'),
        'timing'           => array('next year','later','postpone','not now','timing','revisit'),
        'no_need'          => array('not interested','no need','not required','no requirement'),
    );

    public function manifest() {
        $withText = (int)$this->db->query("
            SELECT COUNT(*) c FROM init_call
            WHERE (reject_remarks IS NOT NULL AND reject_remarks <> '')
               OR (kcremark IS NOT NULL AND kcremark <> '')
               OR (pnpremark IS NOT NULL AND pnpremark <> '')")->row()->c;
        return array(
            'feature'       => 'competitor_intel',
            'source_table'  => 'init_call',
            'remark_fields' => array('reject_remarks','kcremark','pnpremark'),
            'rows_with_text'=> $withText,
            'themes'        => array_keys($this->themes),
            'deployed_at'   => '2026-06-06',
        );
    }

    /** Aggregate theme counts across all remark fields (real LIKE scan). */
    public function themes() {
        $out = array();
        foreach ($this->themes as $code => $kws) {
            $conds = array();
            $params = array();
            foreach ($kws as $kw) {
                foreach (array('reject_remarks','kcremark','pnpremark') as $col) {
                    $conds[] = "LOWER($col) LIKE ?";
                    $params[] = '%' . $kw . '%';
                }
            }
            $sql = "SELECT COUNT(*) c FROM init_call WHERE " . implode(' OR ', $conds);
            $c = (int)$this->db->query($sql, $params)->row()->c;
            $out[] = array('theme_code' => $code, 'mentions' => $c);
        }
        usort($out, function($a, $b) { return $b['mentions'] - $a['mentions']; });
        return $out;
    }

    /** Real example remarks for a given theme. */
    public function examples($theme_code, $limit = 15) {
        if (!isset($this->themes[$theme_code])) return array();
        $kws = $this->themes[$theme_code];
        $conds = array(); $params = array();
        foreach ($kws as $kw) {
            foreach (array('reject_remarks','kcremark','pnpremark') as $col) {
                $conds[] = "LOWER($col) LIKE ?";
                $params[] = '%' . $kw . '%';
            }
        }
        $params[] = (int)$limit;
        $sql = "
            SELECT ic.id AS lead_id,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   COALESCE(NULLIF(ic.reject_remarks,''), NULLIF(ic.kcremark,''), ic.pnpremark) AS remark
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE " . implode(' OR ', $conds) . "
            ORDER BY ic.id DESC
            LIMIT ?";
        $rows = $this->db->query($sql, $params)->result_array();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'lead_id' => (int)$r['lead_id'],
                'company' => $r['company'],
                'remark'  => $r['remark'],
            );
        }
        return $out;
    }
}
