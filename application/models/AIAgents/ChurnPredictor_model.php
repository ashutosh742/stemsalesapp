<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ChurnPredictor_model - Agent (additive, 2026-06-06)
 *
 * Predicts which open leads are at risk of going cold ("churn"), derived from
 * the real init_call table. No mock data.
 *
 * Risk signal = days since last movement (updated_at) on an OPEN lead that is
 * NOT yet in closure/sign-off. Banded:
 *   >= 90 days idle  -> HIGH
 *   45..89 days idle -> MEDIUM
 *   21..44 days idle -> LOW
 *   < 21 days        -> not at risk (excluded)
 *
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class ChurnPredictor_model extends CI_Model {

    public function manifest() {
        $open = (int)$this->db->query("SELECT COUNT(*) c FROM init_call WHERE open = 1")->row()->c;
        return array(
            'feature'      => 'churn_predictor',
            'source_table' => 'init_call',
            'open_leads'   => $open,
            'bands'        => array('HIGH>=90d','MEDIUM 45-89d','LOW 21-44d'),
            'deployed_at'  => '2026-06-06',
        );
    }

    /** At-risk open leads for a BD (or org-wide if bd_uid<=0). */
    public function at_risk($bd_uid, $limit = 30) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        $params = array();
        $bd_filter = '';
        if ($bd_uid > 0) {
            $bd_filter = " AND (ic.mainbd = ? OR ic.insidebd = ?)";
            $params = array($bd_uid, $bd_uid);
        }
        $sql = "
            SELECT ic.id AS lead_id,
                   COALESCE(NULLIF(cm.compname,''), CONCAT('Lead #', ic.id)) AS company,
                   ic.cstatus,
                   COALESCE(ic.proposal_amt,0) AS proposal_amt,
                   DATEDIFF(NOW(), COALESCE(ic.updated_at, ic.createDate)) AS days_idle
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.open = 1
              $bd_filter
            HAVING days_idle >= 21
            ORDER BY days_idle DESC
            LIMIT ?";
        $params[] = $limit;
        $rows = $this->db->query($sql, $params)->result_array();
        $out = array();
        foreach ($rows as $r) {
            $d = (int)$r['days_idle'];
            if ($d >= 90)      { $band = 'HIGH';   $risk = 90 + min($d - 90, 10); }
            elseif ($d >= 45)  { $band = 'MEDIUM'; $risk = 60 + (int)(($d - 45) / 3); }
            else               { $band = 'LOW';    $risk = 30 + ($d - 21); }
            $out[] = array(
                'lead_id'      => (int)$r['lead_id'],
                'company'      => $r['company'],
                'churn_band'   => $band,
                'churn_risk'   => (int)min($risk, 100),
                'days_idle'    => $d,
                'proposal_amt' => (float)$r['proposal_amt'],
                'recommended'  => $band === 'HIGH'
                    ? 'Re-engage immediately or mark lost'
                    : ($band === 'MEDIUM' ? 'Schedule a touch within 3 days' : 'Plan a follow-up this week'),
            );
        }
        return $out;
    }

    /** Summary counts by band for a BD/org. */
    public function summary($bd_uid) {
        $rows = $this->at_risk($bd_uid, 5000);
        $agg = array('HIGH'=>0,'MEDIUM'=>0,'LOW'=>0,'amt_at_risk'=>0.0);
        foreach ($rows as $r) {
            $agg[$r['churn_band']]++;
            $agg['amt_at_risk'] += $r['proposal_amt'];
        }
        $agg['total_at_risk'] = count($rows);
        return $agg;
    }
}
