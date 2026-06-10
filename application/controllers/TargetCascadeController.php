<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TargetCascadeController - Migration 058
 *
 * Cascades a locked quarter target into Month, Fortnight, Week child rows
 * using working-day pro-rata.
 *
 * Routes (register in routes_review_v2.php):
 *   POST api/target/cascade/lock       -> lock(target_quarter_id)
 *   GET  api/target/cascade/periods    -> periods(uid, cadence, fiscal_quarter)
 *   GET  api/target/cascade/probe      -> probe()
 */
class TargetCascadeController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/TargetCascade_model', 'tc');
        $this->_require_bearer();
        $this->_rp_guard();
    }

    // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This controller
    // returned live business data with NO token check (fail-open). Allow only
    // liveness/probe methods; require a valid digest OR per-user login token for
    // every data method via the shared authunify_ok(). Additive: valid callers
    // unchanged; only missing/garbage tokens are now rejected.
    private $_rp_public = array('probe', 'status');
    private function _rp_guard() {
        $m = $this->router->fetch_method();
        if (in_array($m, $this->_rp_public, true)) { return; }
        if (substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }


    private function _require_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization', true);
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
            exit;
        }
    }

    private function _json($payload, $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    public function probe()
    {
        return $this->_json(['ok' => true, 'migration' => '058', 'component' => 'target_cascade']);
    }

    /**
     * POST /api/target/cascade/lock?target_quarter_id=N
     * Locks the quarter target and cascades all axis rows into 3 months + 6 fortnights + 13 weeks.
     */
    public function lock()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['error' => 'method_not_allowed'], 405);
        }
        $tq_id = (int)$this->input->get_post('target_quarter_id');
        if ($tq_id <= 0) {
            return $this->_json(['error' => 'bad_target_quarter_id'], 400);
        }
        try {
            $out = $this->tc->cascade_to_periods($tq_id);
            return $this->_json(array_merge(['ok' => true], $out));
        } catch (Exception $e) {
            log_message('error', 'target cascade lock failed: ' . $e->getMessage());
            return $this->_json(['error' => 'cascade_failed', 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/target/cascade/periods?uid=X&cadence=month&fiscal_quarter=Q1
     */
    public function periods()
    {
        $uid = (int)$this->input->get('uid');
        $cadence = $this->input->get('cadence') ?: 'month';
        $fq = $this->input->get('fiscal_quarter');
        if ($uid <= 0 || !in_array($cadence, ['quarter','month','fortnight','week'], true)) {
            return $this->_json(['error' => 'bad_params'], 400);
        }
        try {
            $rows = $this->tc->get_periods($uid, $cadence, $fq);
            return $this->_json([
                'uid' => $uid,
                'cadence' => $cadence,
                'fiscal_quarter' => $fq,
                'count' => count($rows),
                'rows' => $rows,
            ]);
        } catch (Exception $e) {
            return $this->_json(['error' => 'fetch_failed', 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/target/cascade/checkin_current_week?uid=NN
     * Returns the current ISO week target_checkin row (or stub if not yet submitted)
     * plus the BD's allocation snapshot for that week so the UI tile can render
     * achieved vs target per axis.
     * Migration 058 Patch G.
     */
    public function checkin_current_week()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'uid required'));
            return;
        }

        // Compute ISO week of today
        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('monday this week', strtotime($today)));
        $week_label = date('o-W', strtotime($week_start));
        $week_no = (int)date('W', strtotime($week_start));

        // Find active target_quarter for today (schema: fy varchar, quarter varchar, start_date, end_date)
        $tq = $this->db->query("
            SELECT id, quarter, start_date, end_date, status, cluster_id,
                   quarter AS quarter_label
            FROM target_quarter
            WHERE start_date <= ? AND end_date >= ?
              AND status IN ('set','locked','signed_off')
            ORDER BY start_date DESC LIMIT 1
        ", array($today, $today))->row_array();

        $checkin = null;
        $allocations = array();
        if ($tq) {
            $checkin = $this->db->query("
                SELECT id, week_no, week_start_date, achieved_last_week,
                       confidence_next_week, top_blocker, help_needed, help_text,
                       submitted_at, review_status, review_notes
                FROM target_checkin
                WHERE uid = ? AND target_quarter_id = ? AND week_start_date = ?
                LIMIT 1
            ", array($uid, $tq['id'], $week_start))->row_array();

            $allocations = $this->db->query("
                SELECT axis, cadence, period_label,
                       COALESCE(override_value, auto_value) AS target_value
                FROM target_allocation
                WHERE uid = ? AND target_quarter_id = ?
                  AND cadence IN ('week','fortnight','month','quarter')
                ORDER BY FIELD(cadence,'week','fortnight','month','quarter'), axis
            ", array($uid, $tq['id']))->result_array();
        }

        echo json_encode(array(
            'ok' => true,
            'uid' => $uid,
            'week_label' => $week_label,
            'week_no' => $week_no,
            'week_start_date' => $week_start,
            'target_quarter' => $tq,
            'checkin' => $checkin,
            'allocations' => $allocations,
        ));
    }

    /**
     * POST /api/target/cascade/checkin_submit
     * Body: uid, target_quarter_id, week_start_date, achieved_last_week (text),
     *       confidence_next_week (1-5), top_blocker, help_needed (0/1), help_text
     * Idempotent on uid + target_quarter_id + week_start_date (UPSERT).
     */
    public function checkin_submit()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            http_response_code(405);
            echo json_encode(array('ok' => false, 'error' => 'POST only'));
            return;
        }
        $uid = (int)$this->input->post('uid');
        $tq_id = (int)$this->input->post('target_quarter_id');
        $week_start = $this->input->post('week_start_date');
        if (!$uid || !$tq_id || !$week_start) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'uid, target_quarter_id, week_start_date required'));
            return;
        }
        $week_no = (int)date('W', strtotime($week_start));

        $existing = $this->db->query("
            SELECT id FROM target_checkin
            WHERE uid = ? AND target_quarter_id = ? AND week_start_date = ?
            LIMIT 1
        ", array($uid, $tq_id, $week_start))->row_array();

        $data = array(
            'achieved_last_week' => $this->input->post('achieved_last_week') ?: '',
            'confidence_next_week' => $this->input->post('confidence_next_week') ?: null,
            'top_blocker' => $this->input->post('top_blocker') ?: null,
            'help_needed' => $this->input->post('help_needed') ? 1 : 0,
            'help_text' => $this->input->post('help_text') ?: null,
            'submitted_at' => date('Y-m-d H:i:s'),
            'review_status' => 'pending',
        );

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('target_checkin', $data);
            $checkin_id = $existing['id'];
            $action = 'updated';
        } else {
            $data['uid'] = $uid;
            $data['target_quarter_id'] = $tq_id;
            $data['week_no'] = $week_no;
            $data['week_start_date'] = $week_start;
            $this->db->insert('target_checkin', $data);
            $checkin_id = $this->db->insert_id();
            $action = 'created';
        }

        echo json_encode(array(
            'ok' => true,
            'action' => $action,
            'checkin_id' => $checkin_id,
        ));
    }

}
