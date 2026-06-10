<?php
/**
 * stem_target_discipline_scorer.php
 *
 * Migration 028 - Target Discipline Scorer (K17)
 *
 * Computes K17 = target discipline component for line_manager_scorecard.
 * Reads from target_discipline_log, applies the 4 gate penalties,
 * returns a normalised 0 to 100 K17 score plus the raw hits.
 *
 *   G1 SET by Day 1            miss = -10 pts
 *   G2 LOCKED by Day 5         miss = -15 pts
 *   G3 weekly check-in Mon 11AM miss = -3 pts per week
 *   G4 SIGN-OFF Day 90         miss = -20 pts
 *
 * Also writes gate pass / miss outcomes (called by controller + cron).
 *
 * Migration: 028
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Stem_target_discipline_scorer extends CI_Model {

    private $penalty_map = [
        'G1_set'             => 10,
        'G2_locked'          => 15,
        'G3_weekly_checkin'  => 3,
        'G4_signoff'         => 20
    ];

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Record a gate outcome. Idempotent on (quarter, uid, gate, week_no).
     *
     * @param int    $target_quarter_id
     * @param int    $uid
     * @param string $gate  one of G1_set, G2_locked, G3_weekly_checkin, G4_signoff
     * @param string $status pass|miss|late
     * @param string $notes
     * @param int|null $week_no  required for G3 only
     */
    public function record_gate_outcome($target_quarter_id, $uid, $gate, $status, $notes='', $week_no=null) {
        if (!isset($this->penalty_map[$gate])) return ['status'=>'error','message'=>'unknown gate'];
        if (!in_array($status, ['pass','miss','late'])) return ['status'=>'error','message'=>'bad status'];

        $penalty = ($status === 'miss') ? -1 * $this->penalty_map[$gate] : 0;
        // 'late' counts as half penalty (warn but not zero out)
        if ($status === 'late') $penalty = -1 * intval(round($this->penalty_map[$gate] / 2));

        $data = [
            'target_quarter_id' => $target_quarter_id,
            'uid'               => $uid,
            'gate'              => $gate,
            'week_no'           => ($gate === 'G3_weekly_checkin') ? $week_no : null,
            'status'            => $status,
            'penalty_pts'       => $penalty,
            'notes'             => mb_substr($notes ?: '', 0, 255)
        ];

        // upsert
        $existing_q = [
            'target_quarter_id' => $target_quarter_id,
            'uid'               => $uid,
            'gate'              => $gate
        ];
        if ($gate === 'G3_weekly_checkin') $existing_q['week_no'] = $week_no;
        $existing = $this->db->get_where('target_discipline_log', $existing_q)->row();
        if ($existing) {
            // Only upgrade: pass beats miss, but never overwrite a pass with miss within same window
            if ($existing->status === 'pass' && $status !== 'pass') return ['status'=>'noop'];
            $this->db->update('target_discipline_log', $data, ['id'=>$existing->id]);
            return ['status'=>'ok','id'=>$existing->id,'mode'=>'updated'];
        }
        $this->db->insert('target_discipline_log', $data);
        return ['status'=>'ok','id'=>$this->db->insert_id(),'mode'=>'inserted'];
    }

    /**
     * Compute K17 = target discipline score for (uid, quarter).
     *
     * Starts at 100, subtracts penalties from target_discipline_log,
     * floors at 0.
     *
     * Returns: ['score'=>int 0-100, 'grade'=>'A+|A|B|C|D', 'gates'=>[...]]
     */
    public function compute_score($uid, $quarter) {
        $q = $this->db->get_where('target_quarter', ['quarter'=>$quarter])->row();
        // Allow scoping by quarter string even if multiple clusters: aggregate across the quarter row(s) that match this uid
        $log_rows = $this->db->select('dl.*')
                             ->from('target_discipline_log dl')
                             ->join('target_quarter tq', 'tq.id = dl.target_quarter_id')
                             ->where(['dl.uid'=>$uid, 'tq.quarter'=>$quarter])
                             ->get()->result();

        $score = 100;
        $gate_summary = ['G1_set'=>'pending','G2_locked'=>'pending','G3_weekly_checkin'=>[],'G4_signoff'=>'pending'];

        foreach ($log_rows as $r) {
            $score += (int)$r->penalty_pts; // penalty_pts is negative
            if ($r->gate === 'G3_weekly_checkin') {
                $gate_summary['G3_weekly_checkin'][] = ['week'=>$r->week_no,'status'=>$r->status];
            } else {
                $gate_summary[$r->gate] = $r->status;
            }
        }

        $score = max(0, min(100, $score));
        $grade = $this->score_to_grade($score);

        return [
            'uid'     => $uid,
            'quarter' => $quarter,
            'score'   => $score,
            'grade'   => $grade,
            'gates'   => $gate_summary
        ];
    }

    /**
     * Daily cron entry point. Walks every open target_quarter and applies
     * the time-based misses:
     *   - If today > start_date+1 AND no G1 pass logged: log G1 miss
     *   - If today > start_date+5 AND quarter.status != locked AND no G2 pass: log G2 miss
     *   - For each completed Monday week_no in [1..13]: if no G3 pass for that
     *     (uid, week_no): log G3 miss
     *   - If today > end_date+1 AND no G4 pass logged: log G4 miss
     *
     * Should run at 6 AM IST via the new daily target_actuals_refresh cron.
     *
     * @return array summary
     */
    public function nightly_gate_detection() {
        $today = date('Y-m-d');
        $open = $this->db->where_in('status', ['draft','set','locked'])
                         ->get('target_quarter')->result();

        $missed = 0; $checked = 0;

        foreach ($open as $q) {
            // Pull every uid covered by this quarter via target_allocation
            $uids = $this->db->select('DISTINCT uid')
                             ->where('target_quarter_id', $q->id)
                             ->get('target_allocation')->result();
            $uid_list = array_map(function($r){ return (int)$r->uid; }, $uids);
            if (empty($uid_list)) continue;

            // G1
            if (strtotime($today) > strtotime($q->start_date)) {
                foreach ($uid_list as $u) {
                    $has = $this->db->where(['target_quarter_id'=>$q->id,'uid'=>$u,'gate'=>'G1_set'])
                                    ->count_all_results('target_discipline_log');
                    if (!$has) {
                        $this->record_gate_outcome($q->id, $u, 'G1_set', 'miss', 'no target set by Day 1 cron detected');
                        $missed++;
                    }
                    $checked++;
                }
            }

            // G2
            $day5 = date('Y-m-d', strtotime($q->start_date.' +4 day'));
            if (strtotime($today) > strtotime($day5) && $q->status !== 'locked') {
                foreach ($uid_list as $u) {
                    $has = $this->db->where(['target_quarter_id'=>$q->id,'uid'=>$u,'gate'=>'G2_locked'])
                                    ->count_all_results('target_discipline_log');
                    if (!$has) {
                        $this->record_gate_outcome($q->id, $u, 'G2_locked', 'miss', 'not locked by Day 5');
                        $missed++;
                    }
                }
            }

            // G3 weekly: walk every Monday from start_date to today
            $cursor   = strtotime('monday this week', strtotime($q->start_date));
            $week_no  = 1;
            while ($cursor < strtotime($today) && $week_no <= 13) {
                $week_start = date('Y-m-d', $cursor);
                // detection happens Tuesday onwards so the Monday window has closed
                if ($cursor + 86400 < strtotime($today)) {
                    foreach ($uid_list as $u) {
                        $has = $this->db->where([
                                            'target_quarter_id'=>$q->id,
                                            'uid'=>$u,
                                            'gate'=>'G3_weekly_checkin',
                                            'week_no'=>$week_no
                                        ])
                                        ->count_all_results('target_discipline_log');
                        if (!$has) {
                            $this->record_gate_outcome($q->id, $u, 'G3_weekly_checkin', 'miss',
                                'no check-in for week '.$week_no.' starting '.$week_start, $week_no);
                            $missed++;
                        }
                    }
                }
                $cursor += 7 * 86400;
                $week_no++;
            }

            // G4
            if (strtotime($today) > strtotime($q->end_date.' +1 day')) {
                foreach ($uid_list as $u) {
                    $has = $this->db->where(['target_quarter_id'=>$q->id,'uid'=>$u,'gate'=>'G4_signoff'])
                                    ->count_all_results('target_discipline_log');
                    if (!$has) {
                        $this->record_gate_outcome($q->id, $u, 'G4_signoff', 'miss', 'no signoff by Day 91');
                        $missed++;
                    }
                }
            }
        }

        return ['quarters_checked'=>count($open), 'uids_checked'=>$checked, 'misses_recorded'=>$missed];
    }

    /**
     * Aggregate scorecard hits across all uids for a quarter.
     * Returns leaderboard sorted by score desc - used by /api/target/discipline_scoreboard.
     */
    public function quarter_scoreboard($quarter) {
        $uids = $this->db->query("
            SELECT DISTINCT a.uid
              FROM target_allocation a
              JOIN target_quarter q ON q.id = a.target_quarter_id
             WHERE q.quarter = ?", [$quarter])->result();

        $out = [];
        foreach ($uids as $row) {
            $out[] = $this->compute_score((int)$row->uid, $quarter);
        }
        usort($out, function($a, $b){ return $b['score'] - $a['score']; });
        return $out;
    }

    private function score_to_grade($score) {
        if ($score >= 90) return 'A+';
        if ($score >= 75) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        return 'D';
    }
}
/* End of file stem_target_discipline_scorer.php */
