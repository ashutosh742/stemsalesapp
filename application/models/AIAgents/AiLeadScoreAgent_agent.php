<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AILeadScore_model
 * Computes win-probability + next-best-action for every open lead.
 * Lives at application/models/AIAgents/AILeadScore_model.php
 *
 * v1.0 uses a rules-based scorecard (no ML required).
 * v2.0 (later) replaces compute_score() with a trained model.
 */
class AILeadScore_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Score every open lead for a given BD on a given date.
     * Open = cstatus NOT IN (12, 13, 14).
     * Returns rows inserted into ai_lead_score.
     */
    public function score_bd($bd_uid, $score_date = null) {
        $score_date = $score_date ?: date('Y-m-d');
        $leads = $this->db
            ->select('ic.cid, ic.compnay, ic.cstatus, ic.fbudget, ic.createDate, ic.mainbd')
            ->from('init_call ic')
            ->where('ic.mainbd', $bd_uid)
            ->where_not_in('ic.cstatus', [12, 13, 14])
            ->get()->result();

        $inserted = 0;
        foreach ($leads as $lead) {
            $features = $this->extract_features($lead);
            $score = $this->compute_score($features);
            $this->db->replace('ai_lead_score', [
                'cid_id' => $lead->cid,
                'bd_uid' => $bd_uid,
                'score_run_date' => $score_date,
                'win_probability' => $score['win_probability'],
                'predicted_close_value_rs' => $score['predicted_value'],
                'predicted_close_date' => $score['predicted_date'],
                'confidence_band' => $score['confidence'],
                'top_positive_signal' => $score['top_positive'],
                'top_negative_signal' => $score['top_negative'],
                'next_best_action' => $score['nba'],
                'model_version' => 'v1.0',
                'features_json' => json_encode($features),
            ]);
            $inserted++;
        }
        return $inserted;
    }

    /**
     * Extract feature vector for one lead.
     */
    private function extract_features($lead) {
        $cid = $lead->cid;
        $f = [
            'cstatus' => (int) $lead->cstatus,
            'fbudget' => (float) ($lead->fbudget ?? 0),
            'age_days' => (int) ((time() - strtotime($lead->createDate)) / 86400),
        ];

        // Meetings in last 30 days
        $f['meetings_30d'] = (int) $this->db
            ->where('cid_id', $cid)
            ->where_in('actiontype_id', [3, 4])
            ->where('event_date >=', date('Y-m-d', strtotime('-30 days')))
            ->count_all_results('tblcallevents');

        // Days since last touch
        $row = $this->db->select_max('event_date')
            ->where('cid_id', $cid)
            ->get('tblcallevents')->row();
        $f['days_since_last_touch'] = $row && $row->event_date
            ? (int) ((time() - strtotime($row->event_date)) / 86400)
            : 999;

        // MoM approved in window
        $f['approved_moms_30d'] = (int) $this->db
            ->where('cid_id', $cid)
            ->where('approved_status', 1)
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->count_all_results('mom_data');

        // Sentiment from latest MoM (if available)
        $sent = $this->db->select('sentiment_score, buying_signal, blocker_signal')
            ->from('mom_sentiment ms')
            ->join('mom_data m', 'm.id = ms.mom_id', 'inner')
            ->where('m.cid_id', $cid)
            ->order_by('ms.processed_at', 'desc')
            ->limit(1)->get()->row();
        $f['latest_sentiment'] = $sent ? (float) $sent->sentiment_score : 0.0;
        $f['buying_signal'] = $sent ? (int) $sent->buying_signal : 0;
        $f['blocker_signal'] = $sent ? (int) $sent->blocker_signal : 0;

        return $f;
    }

    /**
     * Rules-based score. v2 will replace with trained model.
     * Weights chosen from STEM 2024 to 2026 historical Won/Lost analysis.
     */
    private function compute_score($f) {
        $base = 0.0;
        $positives = [];
        $negatives = [];

        // cstatus weight
        $cstatus_weights = [
            1 => 5, 8 => 8, 2 => 15, 10 => 12, 11 => 12,
            3 => 35, 6 => 60, 9 => 75, 7 => 70,
        ];
        $base += $cstatus_weights[$f['cstatus']] ?? 10;

        // Meeting cadence
        if ($f['meetings_30d'] >= 3) { $base += 12; $positives[] = 'multiple meetings in 30d'; }
        elseif ($f['meetings_30d'] == 0 && $f['age_days'] > 10) { $base -= 15; $negatives[] = 'no meeting in 30d'; }

        // Recency
        if ($f['days_since_last_touch'] <= 3) { $base += 8; $positives[] = 'recent touch'; }
        elseif ($f['days_since_last_touch'] > 14) { $base -= 12; $negatives[] = 'no contact ' . $f['days_since_last_touch'] . ' days'; }

        // MoM signal
        if ($f['approved_moms_30d'] >= 1) { $base += 10; $positives[] = 'approved MoM exists'; }

        // Sentiment
        if ($f['buying_signal']) { $base += 15; $positives[] = 'buying signal in MoM'; }
        if ($f['blocker_signal']) { $base -= 15; $negatives[] = 'blocker detected in MoM'; }
        $base += $f['latest_sentiment'] * 10;

        // Clamp
        $win_prob = max(0, min(100, $base));

        // Predicted value: discount fbudget by win probability
        $predicted_value = $f['fbudget'] * ($win_prob / 100.0);

        // Predicted close date
        $days_to_close = max(7, intval(60 - ($win_prob * 0.5)));
        $predicted_date = date('Y-m-d', strtotime("+{$days_to_close} days"));

        // Confidence
        $confidence = 'low';
        if ($f['meetings_30d'] >= 2 && $f['approved_moms_30d'] >= 1) $confidence = 'high';
        elseif ($f['meetings_30d'] >= 1) $confidence = 'medium';

        // Next-best-action
        $nba = $this->pick_nba($f, $win_prob);

        return [
            'win_probability' => round($win_prob, 2),
            'predicted_value' => round($predicted_value, 2),
            'predicted_date' => $predicted_date,
            'confidence' => $confidence,
            'top_positive' => $positives ? $positives[0] : null,
            'top_negative' => $negatives ? $negatives[0] : null,
            'nba' => $nba,
        ];
    }

    private function pick_nba($f, $prob) {
        if ($f['days_since_last_touch'] > 14) return 'Schedule a meeting this week. Last touch was over 2 weeks ago.';
        if ($f['blocker_signal']) return 'Address the blocker raised in last MoM before next meeting.';
        if ($f['cstatus'] == 6 && $prob >= 65) return 'Push for proposal. Win probability is over 65 percent.';
        if ($f['cstatus'] == 3 && $f['approved_moms_30d'] == 0) return 'Get the MoM approved to unlock PST assignment.';
        if ($f['cstatus'] == 9) return 'High intent. Confirm decision-maker availability for closure call.';
        return 'Continue planned cadence.';
    }
}
