<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadScoreAgent.php (A.3 - 2026-06-01)
 *
 * Scores a single lead (init_call row) 0-100 across four dimensions:
 *   40 pts  Schedule VII CSR category fit
 *   30 pts  Prior-giving signal (past won deals on the same company)
 *   20 pts  Pipeline strength (cstatus weight)
 *   10 pts  Recency
 *
 * Usage (from a CI controller):
 *   $this->load->add_package_path(APPPATH.'agents/');
 *   require_once(APPPATH.'agents/LeadScoreAgent.php');
 *   $agent = new LeadScoreAgent($this->db);
 *   $result = $agent->compute($cid_id);
 */
class LeadScoreAgent {

    private $db;

    // cstatus -> pipeline weight (20 pts max)
    // Higher stage = more pipeline commitment
    private $status_weights = [
        1  => 2,   // Init call
        2  => 4,   // Prospect
        3  => 6,   // Proposal sent
        4  => 10,  // Negotiation
        5  => 14,  // Verbal commit
        6  => 18,  // Agreement signed
        7  => 20,  // Won/Closure
        8  => 1,   // On hold
        9  => 0,   // Lost
        10 => 0,   // Inactive
    ];

    // Schedule VII CSR categories that strongly match education/STEM:
    // partner_type_hint or lead_source text is mapped to a fit tier.
    // Fit tiers -> points (40 pts max):
    //   high   -> 40
    //   medium -> 25
    //   low    -> 10
    //   none   -> 0
    private $schedule7_keywords_high = [
        'education', 'csr_inbound', 'stem', 'school', 'training',
        'skill development', 'skill india', 'vocational',
    ];
    private $schedule7_keywords_medium = [
        'healthcare', 'health', 'environment', 'sanitation', 'ngo',
        'rural', 'women empowerment',
    ];

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * compute($cid_id)
     *
     * Returns array with keys:
     *   cid_id, schedule7_fit, prior_giving_signal,
     *   pipeline_strength, recency_score, base_score, computed_at
     *
     * Also upserts the row into lead_score_v1.
     */
    public function compute($cid_id) {
        $cid_id = (int)$cid_id;

        // -- Fetch lead row --
        $lead = $this->db->query(
            "SELECT ic.id, ic.cstatus, ic.cmpid_id, ic.fbudget,
                    ic.lead_source, ic.createDate,
                    cm.partnerType_id,
                    pt.display_name AS partner_type_hint
             FROM init_call ic
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             LEFT JOIN partner_type_master pt ON pt.id = cm.partnerType_id
             WHERE ic.id = ?
             LIMIT 1",
            [$cid_id]
        )->row();

        if (!$lead) {
            return ['ok' => false, 'error' => 'Lead not found', 'cid_id' => $cid_id];
        }

        // -- Component 1: Schedule VII fit (0-40) --
        $schedule7_fit = $this->_schedule7_score($lead);

        // -- Component 2: Prior-giving signal (0-30) --
        $prior_giving_signal = $this->_prior_giving_score($lead->cmpid_id);

        // -- Component 3: Pipeline strength (0-20) --
        $cstatus = (int)$lead->cstatus;
        $pipeline_strength = isset($this->status_weights[$cstatus])
            ? (int)$this->status_weights[$cstatus]
            : 0;

        // -- Component 4: Recency (0-10) --
        $recency_score = $this->_recency_score($lead->createDate);

        $base_score = $schedule7_fit + $prior_giving_signal + $pipeline_strength + $recency_score;
        $computed_at = date('Y-m-d H:i:s');

        // -- Upsert lead_score_v1 --
        $this->db->query(
            "INSERT INTO lead_score_v1
               (cid_id, schedule7_fit, prior_giving_signal,
                pipeline_strength, recency_score, base_score, computed_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               schedule7_fit       = VALUES(schedule7_fit),
               prior_giving_signal = VALUES(prior_giving_signal),
               pipeline_strength   = VALUES(pipeline_strength),
               recency_score       = VALUES(recency_score),
               base_score          = VALUES(base_score),
               computed_at         = VALUES(computed_at)",
            [$cid_id, $schedule7_fit, $prior_giving_signal,
             $pipeline_strength, $recency_score, $base_score, $computed_at]
        );

        return [
            'ok'                  => true,
            'cid_id'              => $cid_id,
            'schedule7_fit'       => $schedule7_fit,
            'prior_giving_signal' => $prior_giving_signal,
            'pipeline_strength'   => $pipeline_strength,
            'recency_score'       => $recency_score,
            'base_score'          => $base_score,
            'computed_at'         => $computed_at,
        ];
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function _schedule7_score($lead) {
        // Combine partner_type_hint and lead_source for keyword matching
        $text = strtolower(
            ($lead->partner_type_hint ?? '') . ' ' .
            ($lead->lead_source ?? '') . ' ' .
            ($lead->fbudget ?? '')
        );

        // CSR_Inbound lead source is strongest signal
        if ($lead->lead_source === 'CSR_Inbound') return 40;

        foreach ($this->schedule7_keywords_high as $kw) {
            if (strpos($text, $kw) !== false) return 40;
        }
        foreach ($this->schedule7_keywords_medium as $kw) {
            if (strpos($text, $kw) !== false) return 25;
        }

        // Budget band hint: large budgets (>50L) suggest CSR compliance spend
        $fbudget = (float)preg_replace('/[^0-9.]/', '', $lead->fbudget ?? '0');
        if ($fbudget >= 5000000) return 20;  // >= 50L
        if ($fbudget >= 1000000) return 10;  // >= 10L

        return 5; // default: some potential
    }

    private function _prior_giving_score($cmpid_id) {
        if (!(int)$cmpid_id) return 0;

        // Count past won deals (cstatus=7) on same company
        $row = $this->db->query(
            "SELECT COUNT(*) AS won_count,
                    COALESCE(SUM(CASE WHEN fbudget REGEXP '^[0-9]+$' THEN fbudget+0 ELSE 0 END), 0) AS total_budget
             FROM init_call
             WHERE cmpid_id = ? AND cstatus = 7",
            [(int)$cmpid_id]
        )->row();

        if (!$row) return 0;

        $won = (int)$row->won_count;
        if ($won >= 3)  return 30;
        if ($won == 2)  return 22;
        if ($won == 1)  return 15;
        return 0;
    }

    private function _recency_score($createDate) {
        if (!$createDate) return 0;
        $days = (int)((time() - strtotime($createDate)) / 86400);
        if ($days <= 30)  return 10;
        if ($days <= 90)  return 7;
        if ($days <= 180) return 4;
        if ($days <= 365) return 2;
        return 0;
    }
}
