<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ClusterForecaster_model - Agent (additive, 2026-06-06)
 *
 * Per-cluster revenue forecast combining:
 *   - revenue_target_matrix (real FY targets per cluster, 48 rows)
 *   - cluster_master (9 real clusters)
 *   - init_call open pipeline summed by cluster_id (real proposal_amt)
 *
 * Weighted pipeline uses stage probabilities applied to proposal_amt:
 *   closure/closure_pipeline 0.7, verypositive 0.5, positive 0.3, else 0.1.
 *
 * No mock data. Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class ClusterForecaster_model extends CI_Model {

    public function manifest() {
        $cl = (int)$this->db->query("SELECT COUNT(*) c FROM cluster_master")->row()->c;
        $tg = (int)$this->db->query("SELECT COUNT(*) c FROM revenue_target_matrix")->row()->c;
        return array(
            'feature'       => 'cluster_forecaster',
            'source_tables' => array('cluster_master','revenue_target_matrix','init_call'),
            'clusters'      => $cl,
            'target_rows'   => $tg,
            'deployed_at'   => '2026-06-06',
        );
    }

    /** Forecast rows, one per cluster. Optional fiscal_quarter filter. */
    public function forecast($fiscal_quarter = null) {
        // Target by cluster (sum across categories, optionally one quarter).
        $tparams = array();
        $tfilter = '';
        if ($fiscal_quarter) { $tfilter = " WHERE fiscal_quarter = ?"; $tparams[] = $fiscal_quarter; }
        $targets = $this->db->query("
            SELECT cluster_id, MAX(cluster_name) AS cluster_name, SUM(target_rs) AS target_rs
            FROM revenue_target_matrix $tfilter
            GROUP BY cluster_id", $tparams)->result_array();

        $tmap = array();
        foreach ($targets as $t) {
            $tmap[(int)$t['cluster_id']] = array(
                'cluster_name' => $t['cluster_name'],
                'target_rs'    => (float)$t['target_rs'],
            );
        }

        // Weighted open pipeline by cluster from init_call.
        $pipe = $this->db->query("
            SELECT cluster_id,
                   SUM(CASE
                        WHEN closure = 1 OR closure_pipeline = 1 THEN COALESCE(proposal_amt,0) * 0.7
                        WHEN verypositive = 1 THEN COALESCE(proposal_amt,0) * 0.5
                        WHEN positive = 1 THEN COALESCE(proposal_amt,0) * 0.3
                        ELSE COALESCE(proposal_amt,0) * 0.1 END) AS weighted_rs,
                   SUM(COALESCE(proposal_amt,0)) AS raw_pipeline_rs,
                   COUNT(*) AS open_leads
            FROM init_call
            WHERE open = 1 AND cluster_id IS NOT NULL AND cluster_id > 0
            GROUP BY cluster_id")->result_array();

        $out = array();
        foreach ($pipe as $p) {
            $cid = (int)$p['cluster_id'];
            $name = isset($tmap[$cid]) ? $tmap[$cid]['cluster_name'] : null;
            if (!$name) {
                $cm = $this->db->query("SELECT cluster_name FROM cluster_master WHERE cluster_id = ? LIMIT 1", array($cid))->row();
                $name = $cm ? $cm->cluster_name : ('Cluster #'.$cid);
            }
            $target = isset($tmap[$cid]) ? $tmap[$cid]['target_rs'] : 0.0;
            $weighted = (float)$p['weighted_rs'];
            $attain = $target > 0 ? round(($weighted / $target) * 100, 1) : null;
            $out[] = array(
                'cluster_id'        => $cid,
                'cluster_name'      => $name,
                'target_rs'         => $target,
                'weighted_forecast' => round($weighted, 2),
                'raw_pipeline_rs'   => round((float)$p['raw_pipeline_rs'], 2),
                'open_leads'        => (int)$p['open_leads'],
                'forecast_attainment_pct' => $attain,
                'gap_rs'            => round($target - $weighted, 2),
            );
        }
        // Sort by gap desc (biggest shortfall first).
        usort($out, function($a, $b) { return ($b['gap_rs'] <=> $a['gap_rs']); });
        return $out;
    }

    public function headline($fiscal_quarter = null) {
        $rows = $this->forecast($fiscal_quarter);
        $t = 0; $w = 0; $leads = 0;
        foreach ($rows as $r) { $t += $r['target_rs']; $w += $r['weighted_forecast']; $leads += $r['open_leads']; }
        return array(
            'clusters'          => count($rows),
            'total_target_rs'   => round($t, 2),
            'total_forecast_rs' => round($w, 2),
            'total_gap_rs'      => round($t - $w, 2),
            'open_leads'        => $leads,
            'attainment_pct'    => $t > 0 ? round(($w / $t) * 100, 1) : null,
        );
    }
}
