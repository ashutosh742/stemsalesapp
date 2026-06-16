<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Meeting-to-Money (M2M) Assurance - Gate A: Meeting Quality
 * Additive build 2026-06-16. New controller, no existing code touched.
 *
 * Routes (see routes_m2m_assurance_20260616.php):
 *   POST /api/m2m/gatea/capture       BD logs RP/funded/purpose/next-step/commitment
 *   POST /api/m2m/gatea/grade         CM grades MoM, computes weighted Quality Score
 *   GET  /api/m2m/gatea/check         mandatory-field gate (JSON 200, never HTTP error)
 *   GET  /api/m2m/gatea/quality_log   Meeting Quality Log DAILY tracker
 *   GET  /api/m2m/gatea/probe         health probe
 *
 * Quality Score = RP*w_rp + Fit*w_fit + Purpose*w_purpose + MoM*w_mom
 * MoM grade map: Good=1.0, Partial=0.5, Vague=0. Vague => 2-star => blocks.
 * DQ8: 3+ meetings in a month scoring below threshold => disqualifier ledger.
 *
 * Nothing hardcoded - weights/threshold/dq8_count read from m2m_config.
 * ASCII only. Rupees written "Rs". "percent" spelled out. No em-dashes.
 */
class M2m_gate_a extends CI_Controller
{
    protected $token;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('Bearer_auth');
        $this->load->helper('url');
        $this->token = $this->bearer_auth->get_bearer_token();
    }

    // ---------- config (single source of truth) ----------
    private function cfg($key, $default = null)
    {
        $row = $this->db->select('cfg_value')
            ->from('m2m_config')
            ->where('cfg_key', $key)
            ->get()->row_array();
        return ($row && isset($row['cfg_value'])) ? $row['cfg_value'] : $default;
    }

    private function weights()
    {
        return [
            'rp'      => (float)$this->cfg('weight_rp', 40),
            'fit'     => (float)$this->cfg('weight_fit', 20),
            'purpose' => (float)$this->cfg('weight_purpose', 20),
            'mom'     => (float)$this->cfg('weight_mom', 20),
        ];
    }

    public function probe()
    {
        $this->_json([
            'ok'       => true,
            'gate'     => 'A',
            'name'     => 'meeting_quality',
            'weights'  => $this->weights(),
            'threshold'=> (int)$this->cfg('quality_score_threshold', 70),
            'dq8_count'=> (int)$this->cfg('dq8_count', 3),
            'ts'       => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * POST /api/m2m/gatea/capture
     * BD logs the meeting-quality fields onto the existing mom_data row.
     */
    public function capture()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $mom_id = (int)$this->input->post('mom_id');
        if ($mom_id <= 0) return $this->_json(['ok' => false, 'error' => 'mom_id_required'], 422);

        $exists = $this->db->select('id')->from('mom_data')->where('id', $mom_id)->get()->row_array();
        if (!$exists) return $this->_json(['ok' => false, 'error' => 'mom_not_found', 'mom_id' => $mom_id], 422);

        $set = [];
        $captured = [];

        $map = [
            'rp_present'              => 'int',
            'rp_plan_to_reach'        => 'text',
            'prospect_funded'         => 'int',
            'funded_lever'            => 'lever',
            'purpose_achieved'        => 'int',
            'client_commitment'       => 'commitment',
            'next_step_text'          => 'text',
            'next_step_owner_uid'     => 'int',
            'next_step_date'          => 'date',
            'proposal_committed_date' => 'date',
        ];

        foreach ($map as $field => $type) {
            $raw = $this->input->post($field);
            if ($raw === null || $raw === '') continue;
            $val = $this->_coerce($raw, $type);
            if ($val === null) continue;
            $set[$field] = $val;
            $captured[] = $field;
        }

        if (empty($set)) {
            return $this->_json(['ok' => false, 'error' => 'no_fields_supplied', 'mom_id' => $mom_id], 422);
        }

        $this->db->where('id', $mom_id)->update('mom_data', $set);

        return $this->_json([
            'ok'       => true,
            'mom_id'   => $mom_id,
            'captured' => $captured,
            'ts'       => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * POST /api/m2m/gatea/grade
     * CM grades MoM substance (Good/Partial/Vague), computes weighted score,
     * writes mom_quality_log + mom_data quality columns.
     */
    public function grade()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $mom_id     = (int)$this->input->post('mom_id');
        $mom_grade  = strtolower(trim((string)$this->input->post('mom_grade')));
        $graded_by  = (int)$this->input->post('graded_by');

        if ($mom_id <= 0) return $this->_json(['ok' => false, 'error' => 'mom_id_required'], 422);
        $grade_map = ['good' => 1.0, 'partial' => 0.5, 'vague' => 0.0];
        if (!isset($grade_map[$mom_grade])) {
            return $this->_json(['ok' => false, 'error' => 'mom_grade_must_be_good_partial_or_vague'], 422);
        }

        $mom = $this->db->select('id, bd_uid, uid, rp_present, prospect_funded, purpose_achieved')
            ->from('mom_data')->where('id', $mom_id)->get()->row_array();
        if (!$mom) return $this->_json(['ok' => false, 'error' => 'mom_not_found', 'mom_id' => $mom_id], 422);

        $bd_uid = (int)(isset($mom['bd_uid']) && $mom['bd_uid'] !== null ? $mom['bd_uid'] : (isset($mom['uid']) ? $mom['uid'] : 0));

        $w  = $this->weights();
        $rp = ((int)$mom['rp_present'] === 1) ? 1 : 0;
        // Fit == prospect funded flag (real lever present).
        $fit     = ((int)$mom['prospect_funded'] === 1) ? 1 : 0;
        $purpose = ((int)$mom['purpose_achieved'] === 1) ? 1 : 0;
        $mom_pts = $grade_map[$mom_grade];

        $score = ($rp * $w['rp']) + ($fit * $w['fit']) + ($purpose * $w['purpose']) + ($mom_pts * $w['mom']);
        $score = round($score, 2);

        $threshold = (float)$this->cfg('quality_score_threshold', 70);
        $is_quality = ($score >= $threshold) ? 1 : 0;
        $is_vague   = ($mom_grade === 'vague');

        // Grade label: Vague => 2-star (blocks advance). Otherwise quality-based.
        if ($is_vague) {
            $quality_grade = 'vague_2star';
        } else {
            $quality_grade = $is_quality ? 'quality' : 'below_threshold';
        }

        $gates_total  = 4; // RP, Fit, Purpose, MoM
        $gates_passed = $rp + $fit + $purpose + ($mom_pts >= 0.5 ? 1 : 0);

        $gates_json = json_encode([
            'rp'      => $rp,
            'fit'     => $fit,
            'purpose' => $purpose,
            'mom'     => $mom_pts,
            'weights' => $w,
        ]);

        // Write the existing mom_quality_log (reuse - do not duplicate).
        $this->db->insert('mom_quality_log', [
            'mom_id'        => $mom_id,
            'bd_uid'        => $bd_uid,
            'quality_grade' => $quality_grade,
            'quality_score' => $score,
            'gates_passed'  => $gates_passed,
            'gates_total'   => $gates_total,
            'graded_by'     => $graded_by,
            'graded_at'     => date('Y-m-d H:i:s'),
            'notes'         => 'm2m_gate_a mom_grade=' . $mom_grade,
        ]);

        // Mirror summary onto mom_data existing quality columns.
        $this->db->where('id', $mom_id)->update('mom_data', [
            'mom_quality_grade' => $quality_grade,
            'mom_quality_score' => $score,
            'gates_passed_json' => $gates_json,
        ]);

        // DQ8 evaluation: count below-threshold meetings for this BD this month.
        $dq8 = $this->_evaluate_dq8($bd_uid);

        $resp = [
            'ok'            => true,
            'mom_id'        => $mom_id,
            'bd_uid'        => $bd_uid,
            'mom_grade'     => $mom_grade,
            'quality_score' => $score,
            'threshold'     => $threshold,
            'quality'       => (bool)$is_quality,
            'quality_grade' => $quality_grade,
            'gates_passed'  => $gates_passed,
            'gates_total'   => $gates_total,
            'dq8'           => $dq8,
            'ts'            => date('Y-m-d H:i:s'),
        ];
        // Vague blocks status advance until fixed.
        if ($is_vague) {
            $resp['blocked'] = true;
            $resp['block_reason'] = 'mom_vague_2star_fix_required';
        }
        return $this->_json($resp, 200);
    }

    /**
     * GET /api/m2m/gatea/check?mom_id=
     * MANDATORY-FIELD GATE. Returns JSON 200 always (never a 4xx/5xx).
     * Missing any of: RP, funded, next-step+date, committed-date-if-promised
     * => {ok:false, blocked:true, missing:[...]}.
     */
    public function check()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $mom_id = (int)$this->input->get('mom_id');
        if ($mom_id <= 0) {
            // Even bad input stays 200 with structured payload (no HTTP error on app paths).
            return $this->_json(['ok' => false, 'blocked' => true, 'missing' => ['mom_id'], 'reason' => 'mom_id_required'], 200);
        }

        $mom = $this->db->select('id, rp_present, prospect_funded, purpose_achieved,
                next_step_text, next_step_date, client_commitment, proposal_committed_date')
            ->from('mom_data')->where('id', $mom_id)->get()->row_array();

        if (!$mom) {
            return $this->_json(['ok' => false, 'blocked' => true, 'missing' => ['mom_row'], 'mom_id' => $mom_id], 200);
        }

        $missing = [];
        if ($mom['rp_present'] === null || $mom['rp_present'] === '')           $missing[] = 'rp_present';
        if ($mom['prospect_funded'] === null || $mom['prospect_funded'] === '') $missing[] = 'prospect_funded';
        if (trim((string)$mom['next_step_text']) === '')                        $missing[] = 'next_step_text';
        if ($mom['next_step_date'] === null || $mom['next_step_date'] === '' || $mom['next_step_date'] === '0000-00-00') {
            $missing[] = 'next_step_date';
        }

        // Committed proposal date is mandatory only IF a proposal was promised.
        // Promise signal: client_commitment hard/soft implies a proposal path.
        $commitment = strtolower((string)$mom['client_commitment']);
        $promised = in_array($commitment, ['hard', 'soft'], true);
        if ($promised) {
            if ($mom['proposal_committed_date'] === null || $mom['proposal_committed_date'] === '' || $mom['proposal_committed_date'] === '0000-00-00') {
                $missing[] = 'proposal_committed_date';
            }
        }

        $blocked = !empty($missing);
        return $this->_json([
            'ok'      => !$blocked,
            'blocked' => $blocked,
            'mom_id'  => $mom_id,
            'missing' => $missing,
            'promised_proposal' => $promised,
            'ts'      => date('Y-m-d H:i:s'),
        ], 200);
    }

    /**
     * GET /api/m2m/gatea/quality_log?date=YYYY-MM-DD
     * Meeting Quality Log DAILY tracker with RAG flag auto-calc.
     */
    public function quality_log()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);

        $date = (string)$this->input->get('date');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = date('Y-m-d');

        $threshold = (float)$this->cfg('quality_score_threshold', 70);

        $rows = $this->db
            ->select('q.id, q.mom_id, q.bd_uid, q.quality_grade, q.quality_score,
                      q.gates_passed, q.gates_total, q.graded_at,
                      m.cid_id, m.rp_present, m.prospect_funded, m.purpose_achieved,
                      m.next_step_text, m.next_step_date', false)
            ->from('mom_quality_log q')
            ->join('mom_data m', 'm.id = q.mom_id', 'left')
            ->where('DATE(q.graded_at)', $date)
            ->order_by('q.graded_at', 'asc')
            ->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $score = (float)$r['quality_score'];
            $is_vague = (strpos((string)$r['quality_grade'], 'vague') !== false);
            if ($is_vague) {
                $flag = 'RED';
            } elseif ($score >= $threshold) {
                $flag = 'GREEN';
            } elseif ($score >= ($threshold * 0.7)) {
                $flag = 'AMBER';
            } else {
                $flag = 'RED';
            }
            $next_step_present = (trim((string)$r['next_step_text']) !== '') ? 1 : 0;
            $out[] = [
                'date'       => $date,
                'bd'         => (int)$r['bd_uid'],
                'cid'        => isset($r['cid_id']) ? (int)$r['cid_id'] : null,
                'mom_id'     => (int)$r['mom_id'],
                'rp'         => ($r['rp_present'] === null) ? null : (int)$r['rp_present'],
                'fit'        => ($r['prospect_funded'] === null) ? null : (int)$r['prospect_funded'],
                'purpose'    => ($r['purpose_achieved'] === null) ? null : (int)$r['purpose_achieved'],
                'mom_grade'  => $r['quality_grade'],
                'next_step'  => $next_step_present,
                'score'      => $score,
                'quality'    => ($score >= $threshold && !$is_vague),
                'flag'       => $flag,
            ];
        }

        return $this->_json([
            'ok'        => true,
            'date'      => $date,
            'threshold' => $threshold,
            'count'     => count($out),
            'rows'      => $out,
            'ts'        => date('Y-m-d H:i:s'),
        ], 200);
    }

    // ---------- DQ8 ----------
    private function _evaluate_dq8($bd_uid)
    {
        if ($bd_uid <= 0) return ['fired' => false, 'reason' => 'no_bd_uid'];

        $threshold = (float)$this->cfg('quality_score_threshold', 70);
        $dq8_count = (int)$this->cfg('dq8_count', 3);
        $month = date('Y-m');

        $row = $this->db->select('COUNT(*) AS n', false)
            ->from('mom_quality_log')
            ->where('bd_uid', $bd_uid)
            ->where("DATE_FORMAT(graded_at, '%Y-%m')", $month)
            ->where('quality_score <', $threshold)
            ->get()->row_array();

        $n = (int)($row ? $row['n'] : 0);
        if ($n < $dq8_count) {
            return ['fired' => false, 'below_threshold_meetings' => $n, 'needed' => $dq8_count];
        }

        // Idempotent: one DQ8 row per (subject, month). cid_id NULL for pattern DQ.
        $reason = 'Junk-meeting pattern: ' . $n . ' meetings this month scored below '
                . (int)$threshold . ' percent. Coaching mandatory; repeat triggers star and role review.';
        $this->_write_dq('DQ8', $bd_uid, 'BD', null, $month, $reason, 'meeting_quality_log');

        return ['fired' => true, 'below_threshold_meetings' => $n, 'needed' => $dq8_count];
    }

    private function _write_dq($code, $subject_uid, $role, $cid_id, $month, $reason, $tracker)
    {
        // INSERT IGNORE against the unique key (code, subject, cid, month) = idempotent.
        $cid_sql = ($cid_id === null) ? 'NULL' : (int)$cid_id;
        $sql = "INSERT IGNORE INTO m2m_disqualifier_log
                (dq_code, subject_uid, subject_role, cid_id, period_month, reason, source_tracker, triggered_at, auto)
                VALUES (?, ?, ?, " . $cid_sql . ", ?, ?, ?, NOW(), 1)";
        $this->db->query($sql, [$code, (int)$subject_uid, $role, $month, $reason, $tracker]);
    }

    // ---------- helpers ----------
    private function _coerce($raw, $type)
    {
        switch ($type) {
            case 'int':
                if (!is_numeric($raw)) return null;
                return (int)$raw;
            case 'date':
                $s = trim((string)$raw);
                return preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) ? $s : null;
            case 'lever':
                $s = strtolower(trim((string)$raw));
                return in_array($s, ['csr', 'dmft', 'psu', 'other'], true) ? $s : null;
            case 'commitment':
                $s = strtolower(trim((string)$raw));
                return in_array($s, ['hard', 'soft', 'none'], true) ? $s : null;
            case 'text':
            default:
                return trim((string)$raw);
        }
    }

    protected function _json($data, $code)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
