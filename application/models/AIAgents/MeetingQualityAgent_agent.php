<?php
// =====================================================================
// STEM CRM - Migration 025: Meeting Quality Agent
// File: application/models/AIAgents/MeetingQuality_model.php
// =====================================================================
// Purpose:
//   Scores every meeting on three axes and assigns a letter grade:
//
//     1) Agenda coverage  - did the actor ask the gate questions from
//        meeting_agenda_template for this purpose + cstatus?
//     2) Punctuality      - did the meeting start within 10 minutes of
//        scheduled_start_time and was it classified within 15 min?
//     3) Outcome richness - does the MoM (draft) carry concrete answers
//        for fund_sanstion_limit, approving_autorities, DM block,
//        objections, next steps?
//
//   The hard rule the founder set:
//     "Got details only without DM met cannot grade higher than C"
//
//   For travel cluster meetings the cap is even tighter: got-details
//   only with no DM met gets D and double penalty when followup expires.
//
// Output written to meeting_quality_score (one row per callevent_id).
// Read by:
//   - planning grade computation (migration 013)
//   - line manager scorecard (migration 022) K3 coaching ratio
//   - the 7:30 consolidated audit (migration 020 + 025 amendment)
// =====================================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingQuality_model extends CI_Model {

    // Hard caps user dictated
    private $cap_got_details_no_dm = 'C';        // home
    private $cap_got_details_no_dm_travel = 'D'; // travel cluster
    private $cap_walkout = 'D';
    private $cap_no_audio_uploaded = 'C';

    // Letter grade thresholds
    private $grade_thresholds = array(
        'A+' => 90, 'A' => 80, 'B' => 70, 'C' => 55, 'D' => 0
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -----------------------------------------------------------------
    // ENTRY POINT - called by MeetingLifecycle::end() after extraction
    // -----------------------------------------------------------------
    public function score_meeting($params) {
        $callevent_id = (int)$params['callevent_id'];
        $cid_id = (int)$params['cid_id'];
        $actor_uid = (int)$params['actor_uid'];
        $actor_role = $params['actor_role'];
        $classification = $params['classification'];
        $is_travel_cluster = (int)($params['is_travel_cluster'] ?? 0);
        $dm_met = (int)($params['dm_met'] ?? 0);

        // Pull the meeting context
        $event = $this->db->select('purpose_id, atid, scheduled_start_time, actual_start_time, classified_at, actual_end_time')
                          ->from('tblcallevents')
                          ->where('id', $callevent_id)
                          ->get()->row_array();
        if (!$event) {
            return array('ok' => false, 'reason' => 'callevent_not_found');
        }

        // Pull init_call to get current cstatus
        $ic = $this->db->select('cstatus, mainbd, cluster_id, compny_nm')
                       ->from('init_call')
                       ->where('id', $cid_id)
                       ->get()->row_array();
        $cstatus = $ic ? (int)$ic['cstatus'] : 1;

        // Pull mom_draft if present
        $draft = $this->db->where('callevent_id', $callevent_id)
                          ->order_by('id', 'DESC')
                          ->limit(1)
                          ->get('mom_draft')->row_array();

        // Score three axes
        $coverage = $this->score_agenda_coverage($event['purpose_id'], $cstatus, $draft, $is_travel_cluster);
        $punctuality = $this->score_punctuality($event);
        $richness = $this->score_outcome_richness($draft, $classification);

        // Composite (weighted)
        $composite = round(
            ($coverage['score'] * 0.45) +
            ($punctuality['score'] * 0.20) +
            ($richness['score'] * 0.35), 1
        );

        // Convert to letter grade
        $grade = $this->score_to_grade($composite);

        // Apply hard caps
        $cap_applied = null;
        if ($classification === 'got_details_only' && $dm_met === 0) {
            if ($is_travel_cluster === 1) {
                if ($this->grade_rank($grade) > $this->grade_rank($this->cap_got_details_no_dm_travel)) {
                    $grade = $this->cap_got_details_no_dm_travel;
                    $cap_applied = 'travel_cluster_got_details_no_dm';
                }
            } else {
                if ($this->grade_rank($grade) > $this->grade_rank($this->cap_got_details_no_dm)) {
                    $grade = $this->cap_got_details_no_dm;
                    $cap_applied = 'got_details_no_dm';
                }
            }
        }

        if ($classification === 'walkout') {
            if ($this->grade_rank($grade) > $this->grade_rank($this->cap_walkout)) {
                $grade = $this->cap_walkout;
                $cap_applied = 'walkout';
            }
        }

        // If no audio uploaded for an extended-purpose meeting, cap at C
        $audio_row = $this->db->where('callevent_id', $callevent_id)
                              ->where('whisper_status', 'done')
                              ->get('meeting_audio_log')->row();
        if (!$audio_row && in_array($event['purpose_id'], array(1, 3, 4, 6, 13))) {
            if ($this->grade_rank($grade) > $this->grade_rank($this->cap_no_audio_uploaded)) {
                $grade = $this->cap_no_audio_uploaded;
                $cap_applied = ($cap_applied ? $cap_applied . ',' : '') . 'no_audio_uploaded';
            }
        }

        // Persist
        $row = array(
            'callevent_id' => $callevent_id,
            'cid_id' => $cid_id,
            'actor_uid' => $actor_uid,
            'actor_role' => $actor_role,
            'classification' => $classification,
            'is_travel_cluster' => $is_travel_cluster,
            'dm_met' => $dm_met,
            'coverage_pct' => $coverage['score'],
            'coverage_breakdown' => json_encode($coverage),
            'punctuality_pct' => $punctuality['score'],
            'punctuality_breakdown' => json_encode($punctuality),
            'richness_pct' => $richness['score'],
            'richness_breakdown' => json_encode($richness),
            'composite_score' => $composite,
            'grade' => $grade,
            'cap_applied' => $cap_applied,
            'scored_at' => date('Y-m-d H:i:s')
        );

        // Upsert (one row per callevent_id)
        $existing = $this->db->select('id')
                             ->where('callevent_id', $callevent_id)
                             ->get('meeting_quality_score')->row();
        if ($existing) {
            $this->db->where('id', $existing->id)
                     ->update('meeting_quality_score', $row);
            $row['id'] = $existing->id;
        } else {
            $this->db->insert('meeting_quality_score', $row);
            $row['id'] = $this->db->insert_id();
        }

        return array('ok' => true, 'score' => $row);
    }

    // -----------------------------------------------------------------
    // Score axis 1: agenda coverage
    // -----------------------------------------------------------------
    private function score_agenda_coverage($purpose_id, $cstatus, $draft, $is_travel_cluster) {
        $sql = "SELECT id, question_text, expected_answer_type, is_mandatory,
                       scoring_weight, gate_block
                FROM meeting_agenda_template
                WHERE purpose_id = ?
                  AND ? BETWEEN cstatus_min AND cstatus_max
                  AND (travel_cluster_only = 0 OR travel_cluster_only = ?)";
        $questions = $this->db->query($sql, array($purpose_id, $cstatus, $is_travel_cluster))
                              ->result_array();

        if (empty($questions)) {
            return array('score' => 60, 'covered' => 0, 'total' => 0, 'note' => 'no_template_found');
        }

        $covered = 0;
        $weight_earned = 0;
        $weight_total = 0;
        $missing_gates = array();

        $draft_text = $this->draft_to_searchable_text($draft);

        foreach ($questions as $q) {
            $weight_total += (int)$q['scoring_weight'];
            $hit = $this->question_answered_in_draft($q, $draft, $draft_text);
            if ($hit) {
                $covered++;
                $weight_earned += (int)$q['scoring_weight'];
            } else {
                if (!empty($q['gate_block']) && (int)$q['is_mandatory'] === 1) {
                    $missing_gates[] = $q['gate_block'];
                }
            }
        }

        $score = $weight_total > 0 ? round(($weight_earned / $weight_total) * 100, 1) : 0;
        return array(
            'score' => $score,
            'covered' => $covered,
            'total' => count($questions),
            'weight_earned' => $weight_earned,
            'weight_total' => $weight_total,
            'missing_gates' => $missing_gates
        );
    }

    private function draft_to_searchable_text($draft) {
        if (empty($draft)) return '';
        $parts = array();
        foreach (array('dm_name','dm_designation','dm_mobile','dm_email','agenda_text',
                       'discussion_text','objections_text','next_steps_text',
                       'fund_sanstion_limit','approving_autorities','expected_close_date',
                       'competition_text','requirements_text') as $f) {
            if (!empty($draft[$f])) $parts[] = (string)$draft[$f];
        }
        return strtolower(implode(' ', $parts));
    }

    private function question_answered_in_draft($q, $draft, $draft_text) {
        if (empty($draft)) return false;
        $type = $q['expected_answer_type'];

        switch ($type) {
            case 'dm_name_designation':
            case 'dm_full_block':
                return !empty($draft['dm_name']) && !empty($draft['dm_designation']);
            case 'rs_amount':
            case 'rs_lakh':
                return !empty($draft['fund_sanstion_limit'])
                       || (preg_match('/(rs|rupees|lakh|crore|inr)\s*[0-9]/i', $draft_text));
            case 'specific_date':
                return !empty($draft['expected_close_date']);
            case 'month_year':
                return preg_match('/(january|february|march|april|may|june|july|august|september|october|november|december).{0,6}20\d{2}/i', $draft_text);
            case 'role_list':
                return !empty($draft['approving_autorities']);
            case 'objection_list':
                return !empty($draft['objections_text']);
            case 'count_per_grade':
            case 'school_profile':
                return !empty($draft['requirements_text']) || strpos($draft_text, 'grade') !== false || strpos($draft_text, 'section') !== false;
            case 'yes_no':
            case 'yes_no_who':
            case 'yes_no_reason':
                return !empty($draft['discussion_text']) || !empty($draft['next_steps_text']);
            default:
                // Fallback: keyword match against question stem words
                $keywords = preg_split('/\s+/', strtolower($q['question_text']));
                $hits = 0;
                foreach ($keywords as $k) {
                    if (strlen($k) > 5 && strpos($draft_text, $k) !== false) $hits++;
                }
                return $hits >= 2;
        }
    }

    // -----------------------------------------------------------------
    // Score axis 2: punctuality
    // -----------------------------------------------------------------
    private function score_punctuality($event) {
        $score = 100;
        $notes = array();

        if (!empty($event['scheduled_start_time']) && !empty($event['actual_start_time'])) {
            $sched = strtotime($event['scheduled_start_time']);
            $actual = strtotime($event['actual_start_time']);
            $diff_min = round(($actual - $sched) / 60);
            if ($diff_min > 10) {
                $deduct = min(40, ($diff_min - 10) * 2);
                $score -= $deduct;
                $notes[] = 'late_start_by_' . $diff_min . 'min';
            } elseif ($diff_min < -15) {
                $score -= 5;
                $notes[] = 'started_way_early';
            }
        } else {
            $score -= 10;
            $notes[] = 'no_scheduled_or_actual_start';
        }

        // Classification within 15 min of actual start
        if (!empty($event['actual_start_time']) && !empty($event['classified_at'])) {
            $start = strtotime($event['actual_start_time']);
            $clsd = strtotime($event['classified_at']);
            $diff_min = round(($clsd - $start) / 60);
            if ($diff_min > 20) {
                $score -= min(30, ($diff_min - 15) * 2);
                $notes[] = 'late_classification_at_' . $diff_min . 'min';
            }
        } else {
            $score -= 20;
            $notes[] = 'no_classification_timestamp';
        }

        return array('score' => max(0, $score), 'notes' => $notes);
    }

    // -----------------------------------------------------------------
    // Score axis 3: outcome richness
    // -----------------------------------------------------------------
    private function score_outcome_richness($draft, $classification) {
        $score = 0;
        $present = array();

        if (empty($draft)) {
            return array('score' => 0, 'note' => 'no_draft', 'present' => array());
        }

        $fields = array(
            'dm_name' => 10, 'dm_designation' => 8, 'dm_mobile' => 6, 'dm_email' => 4,
            'fund_sanstion_limit' => 12, 'approving_autorities' => 10,
            'expected_close_date' => 10, 'objections_text' => 10,
            'next_steps_text' => 12, 'competition_text' => 5,
            'requirements_text' => 8, 'agenda_text' => 5
        );

        $total_weight = array_sum($fields);
        $earned = 0;
        foreach ($fields as $f => $w) {
            if (!empty($draft[$f]) && strlen(trim($draft[$f])) > 3) {
                $earned += $w;
                $present[] = $f;
            }
        }
        $score = round(($earned / $total_weight) * 100, 1);

        // Penalty for got-details classification
        if ($classification === 'got_details_only') {
            $score = max(0, $score - 15);
        }
        if ($classification === 'walkout') {
            $score = max(0, $score - 35);
        }

        return array('score' => $score, 'present' => $present, 'earned' => $earned, 'total' => $total_weight);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------
    private function score_to_grade($composite) {
        foreach ($this->grade_thresholds as $g => $min) {
            if ($composite >= $min) return $g;
        }
        return 'D';
    }

    private function grade_rank($g) {
        $order = array('D' => 0, 'C' => 1, 'B' => 2, 'A' => 3, 'A+' => 4);
        return $order[$g] ?? 0;
    }

    // -----------------------------------------------------------------
    // Bulk re-score (used after agenda template changes)
    // -----------------------------------------------------------------
    public function rescore_recent_meetings($days = 7) {
        $sql = "SELECT ce.id AS callevent_id, ce.cid_id, mqs.actor_uid, mqs.actor_role,
                       mqs.classification, mqs.is_travel_cluster, mqs.dm_met
                FROM tblcallevents ce
                LEFT JOIN meeting_quality_score mqs ON mqs.callevent_id = ce.id
                WHERE ce.event_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  AND mqs.id IS NOT NULL
                LIMIT 500";
        $rows = $this->db->query($sql, array($days))->result_array();
        foreach ($rows as $r) {
            $this->score_meeting($r);
        }
        return count($rows);
    }
}
// END MeetingQuality_model
