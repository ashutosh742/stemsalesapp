<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NegotiationPriceGuard_model - Agent (additive, 2026-06-06)
 *
 * Rule-based price guardrail for negotiation. For a deal it compares the quoted
 * proposal_amt against the MEDIAN of real quotes in the same init_call.cluster_id
 * peer group, and scans the deal's remarks / kcremark for discount-pressure
 * language. Surfaces a recommended floor and a walk-away discount limit.
 *
 * Real data only (init_call peer quotes; revenue_target_matrix used only when the
 * cluster_id genuinely matches). No LLM. ASCII only. "Rs" for rupees.
 * "percent" spelled out.
 */
class NegotiationPriceGuard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function manifest() {
        $guardable = (int)$this->db->query("
            SELECT COUNT(*) c FROM init_call
            WHERE cstatus IN (6,7,9) AND proposal_amt REGEXP '[0-9]'
              AND proposal_amt NOT IN ('NA','0')"
        )->row()->c;
        return array(
            'feature'        => 'negotiation_price_guard',
            'mode'           => 'rule_based',
            'source_tables'  => array('init_call','tblcallevents','revenue_target_matrix','cluster_master'),
            'guardable_deals'=> $guardable,
            'deployed_at'    => '2026-06-06',
        );
    }

    /** Price guard analysis for one deal. Returns null if lead not found. */
    public function guard($lead_id) {
        $lead_id = (int)$lead_id;
        $r = $this->db->query("
            SELECT ic.id, ic.cmpid_id, ic.cluster_id, ic.cstatus, ic.proposal_amt,
                   ic.noofschools, ic.kcremark,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.id = ? LIMIT 1", array($lead_id))->row_array();
        if (!$r) return null;

        $quoted = $this->_parse_amount($r['proposal_amt']);
        $peer   = $this->_cluster_median((int)$r['cluster_id'], $lead_id);
        $target = $this->_cluster_target((int)$r['cluster_id']);
        $pressure = $this->_discount_pressure($lead_id, $r['kcremark']);

        // Guardrails: floor at 85 percent of peer median (or quote if no peers),
        // walk-away discount capped at 15 percent off the quote.
        $reference = $peer['median'] > 0 ? $peer['median'] : $quoted;
        $floor = $reference > 0 ? (int)round($reference * 0.85) : 0;
        $max_discount_pct = 15;
        $min_acceptable = $quoted > 0 ? (int)round($quoted * (1 - $max_discount_pct / 100)) : 0;

        $verdict = $this->_verdict($quoted, $peer['median'], $pressure);

        return array(
            'lead_id'            => (int)$r['id'],
            'company'            => $r['company'],
            'cluster_id'         => (int)$r['cluster_id'],
            'quoted_amount'      => $quoted > 0 ? ('Rs ' . number_format($quoted)) : 'not quoted yet',
            'peer_median'        => $peer['median'] > 0 ? ('Rs ' . number_format($peer['median'])) : 'no peer quotes',
            'peer_sample_size'   => $peer['n'],
            'cluster_target'     => $target ? ('Rs ' . number_format($target)) : 'no target on record',
            'recommended_floor'  => $floor > 0 ? ('Rs ' . number_format($floor)) : 'not enough data',
            'max_discount_pct'   => $max_discount_pct,
            'walk_away_below'    => $min_acceptable > 0 ? ('Rs ' . number_format($min_acceptable)) : 'not enough data',
            'discount_pressure'  => $pressure,
            'verdict'            => $verdict,
            'guidance'           => $this->_guidance($verdict, $quoted, $peer['median'], $floor, $pressure),
        );
    }

    /** Ranked list of deals under discount pressure for a BD. */
    public function for_bd($bd_uid, $limit = 20) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        $rows = $this->db->query("
            SELECT ic.id, ic.cluster_id, ic.cstatus, ic.proposal_amt, ic.kcremark,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.mainbd = ? AND ic.cstatus IN (6,7,9)
              AND ic.proposal_amt REGEXP '[0-9]' AND ic.proposal_amt NOT IN ('NA','0')
            ORDER BY ic.cstatus DESC
            LIMIT ?", array($bd_uid, $limit))->result_array();

        $out = array();
        foreach ($rows as $row) {
            $quoted = $this->_parse_amount($row['proposal_amt']);
            $peer   = $this->_cluster_median((int)$row['cluster_id'], (int)$row['id']);
            $pressure = $this->_discount_pressure((int)$row['id'], $row['kcremark']);
            $out[] = array(
                'lead_id'          => (int)$row['id'],
                'company'          => $row['company'],
                'quoted_amount'    => 'Rs ' . number_format($quoted),
                'peer_median'      => $peer['median'] > 0 ? ('Rs ' . number_format($peer['median'])) : 'no peers',
                'discount_pressure'=> $pressure['level'],
                'verdict'          => $this->_verdict($quoted, $peer['median'], $pressure),
            );
        }
        // Surface high-pressure deals first
        usort($out, function($a, $b) {
            $rank = array('walk_away_risk'=>0,'hold_firm'=>1,'room_to_negotiate'=>2,'priced_low'=>3,'insufficient_data'=>4);
            $ra = isset($rank[$a['verdict']]) ? $rank[$a['verdict']] : 9;
            $rb = isset($rank[$b['verdict']]) ? $rank[$b['verdict']] : 9;
            return $ra - $rb;
        });
        return $out;
    }

    // ---------------- helpers ----------------

    /** Median of real numeric quotes in the same cluster, excluding this lead. */
    private function _cluster_median($cluster_id, $exclude_lead_id) {
        if ($cluster_id <= 0) return array('median' => 0, 'n' => 0);
        $rows = $this->db->query("
            SELECT proposal_amt FROM init_call
            WHERE cluster_id = ? AND id <> ?
              AND proposal_amt REGEXP '[0-9]' AND proposal_amt NOT IN ('NA','0')",
            array($cluster_id, $exclude_lead_id))->result_array();
        $vals = array();
        foreach ($rows as $row) {
            $v = $this->_parse_amount($row['proposal_amt']);
            if ($v > 0) $vals[] = $v;
        }
        $n = count($vals);
        if ($n === 0) return array('median' => 0, 'n' => 0);
        sort($vals);
        $mid = intdiv($n, 2);
        $median = ($n % 2) ? $vals[$mid] : (int)round(($vals[$mid - 1] + $vals[$mid]) / 2);
        return array('median' => $median, 'n' => $n);
    }

    /** Latest quarter target for the cluster, only when cluster_id genuinely matches. */
    private function _cluster_target($cluster_id) {
        if ($cluster_id <= 0) return 0;
        $row = $this->db->query("
            SELECT target_rs FROM revenue_target_matrix
            WHERE cluster_id = ?
            ORDER BY fiscal_quarter DESC, id DESC LIMIT 1", array($cluster_id))->row_array();
        return $row ? (int)$row['target_rs'] : 0;
    }

    /** Scan kcremark + meeting remarks for discount-pressure language. */
    private function _discount_pressure($lead_id, $kcremark) {
        $blob = strtolower(' ' . (string)$kcremark);
        $rows = $this->db->query("
            SELECT remarks, special_remarks, nextaction FROM tblcallevents
            WHERE cid_id = ? ORDER BY date DESC LIMIT 5", array((int)$lead_id))->result_array();
        foreach ($rows as $m) {
            $blob .= ' ' . strtolower((string)$m['remarks'] . ' ' . (string)$m['special_remarks'] . ' ' . (string)$m['nextaction']);
        }
        $words = array('discount','reduce','lower the price','too costly','too expensive','cheaper',
                       'negotiate','best price','final price','match the','rate cut','less budget',
                       'price down','come down','offer better','revise the quote');
        $matched = array();
        foreach ($words as $w) {
            if (strpos($blob, $w) !== false) $matched[] = $w;
        }
        $count = count($matched);
        $level = $count >= 3 ? 'high' : ($count >= 1 ? 'moderate' : 'none');
        return array('level' => $level, 'signals' => array_values(array_unique($matched)));
    }

    private function _verdict($quoted, $median, $pressure) {
        if ($quoted <= 0 || $median <= 0) return 'insufficient_data';
        $ratio = $quoted / $median;
        if ($pressure['level'] === 'high' && $ratio <= 1.05) return 'walk_away_risk';
        if ($ratio < 0.9)  return 'priced_low';
        if ($ratio > 1.15) return 'room_to_negotiate';
        return 'hold_firm';
    }

    private function _guidance($verdict, $quoted, $median, $floor, $pressure) {
        switch ($verdict) {
            case 'priced_low':
                return 'Quote is below the cluster median. Do not discount further. If anything, justify and hold, or add scope rather than cut price.';
            case 'room_to_negotiate':
                return 'Quote is above the cluster median. There is room to concede on price if needed, but stay above the recommended floor of Rs ' . number_format($floor) . '.';
            case 'hold_firm':
                return 'Quote is in line with the cluster median. Hold firm. Trade any concession for a faster close or a larger order, not a flat cut.';
            case 'walk_away_risk':
                return 'Heavy discount pressure on a deal already at market. Protect margin. Do not go below Rs ' . number_format($floor) . '. Consider walking away rather than racing to the bottom.';
            default:
                return 'Not enough peer pricing data to set a guardrail. Gather one or two comparable quotes in this cluster before conceding.';
        }
    }

    private function _parse_amount($raw) {
        $raw = strtoupper(trim((string)$raw));
        if ($raw === '' || $raw === 'NA' || $raw === '0') return 0;
        $num = (float)preg_replace('/[^0-9.]/', '', $raw);
        if ($num <= 0) return 0;
        if (strpos($raw, 'CR') !== false) return (int)round($num * 10000000);
        if (strpos($raw, 'LK') !== false || strpos($raw, 'LAC') !== false || strpos($raw, 'LAKH') !== false) return (int)round($num * 100000);
        return (int)round($num);
    }
}
