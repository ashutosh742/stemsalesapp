<?php
/**
 * ReviewV2Controller - migration 020 STEM Review v2 REST surface
 *
 * Endpoints exposed under /api/review/*:
 *   GET  /api/review/pending_for_manager      - schedules due for the calling manager
 *   GET  /api/review/pending_for_bd           - BD self-assessment obligations
 *   POST /api/review/start_session            - manager opens a review session
 *   POST /api/review/save_bd_self_rating      - BD posts self-rating per metric
 *   POST /api/review/save_manager_rating      - manager posts rating per metric
 *   POST /api/review/mark_bd_self_done        - BD completes self-assessment
 *   POST /api/review/close_session            - manager closes, band computed, next schedule created
 *   GET  /api/review/session/{id}             - full session payload with metrics + action items
 *   POST /api/review/action_item/add          - add commitment to session
 *   POST /api/review/action_item/close        - mark action item done
 *   GET  /api/review/gate_check               - 18:30 plan-submit gate hook
 *   GET  /api/review/skip_level_dashboard     - Director skip-level view
 *   POST /api/review/refresh_skip_register    - daily roll-up refresh
 *   POST /api/review/bootstrap_pilot_schedule - one-shot pilot seed
 *
 * Auth: Bearer <STEM_DIGEST_TOKEN> for cron-style endpoints, session_id for
 *       interactive BD/CM endpoints (mirrors existing CI auth pattern).
 * Staging only until Mon 18 May 2026 GitHub gate.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/RestApiBaseController.php';

class ReviewV2Controller extends RestApiBaseController {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/Review_v2_model', 'rv2');
        $this->load->helper(array('url','date'));
        $this->load->library('form_validation');
    }

    // ----------------------------------------------------------------------
    // SCHEDULE LISTING
    // ----------------------------------------------------------------------

    /**
     * GET /api/review/pending_for_manager?manager_uid=<id>
     * Returns schedules with status pending or in_progress for the manager.
     */
    public function pending_for_manager() {
        $manager_uid = (int) $this->_param('manager_uid', $this->_caller_uid());
        if ($manager_uid <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'manager_uid required'), 400);
        }
        $rows = $this->rv2->pending_for_manager($manager_uid);
        return $this->_json(array(
            'ok'         => true,
            'manager_uid'=> $manager_uid,
            'count'      => count($rows),
            'rows'       => $rows,
        ));
    }

    /**
     * GET /api/review/pending_for_bd?bd_uid=<id>
     */
    public function pending_for_bd() {
        $bd_uid = (int) $this->_param('bd_uid', $this->_caller_uid());
        if ($bd_uid <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'bd_uid required'), 400);
        }
        $rows = $this->rv2->pending_for_bd($bd_uid);
        return $this->_json(array(
            'ok'    => true,
            'bd_uid'=> $bd_uid,
            'count' => count($rows),
            'rows'  => $rows,
        ));
    }

    // ----------------------------------------------------------------------
    // SESSION LIFECYCLE
    // ----------------------------------------------------------------------

    /**
     * POST /api/review/start_session
     * Body: schedule_id, manager_uid (optional, defaults to caller)
     * Returns session_id and snapshotted metric rows.
     */
    public function start_session() {
        $schedule_id = (int) $this->input->post('schedule_id');
        $manager_uid = (int) $this->input->post('manager_uid');
        if ($manager_uid <= 0) { $manager_uid = $this->_caller_uid(); }

        if ($schedule_id <= 0 || $manager_uid <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'schedule_id and manager_uid required'), 400);
        }

        try {
            $res = $this->rv2->start_session($schedule_id, $manager_uid);
            return $this->_json(array_merge(array('ok'=>true), $res));
        } catch (Exception $e) {
            return $this->_json(array('ok'=>false,'error'=>$e->getMessage()), 500);
        }
    }

    /**
     * POST /api/review/save_bd_self_rating
     * Body: session_id, metric_id, bd_self_rating (1-5), bd_remarks (optional)
     */
    public function save_bd_self_rating() {
        $session_id  = (int) $this->input->post('session_id');
        $metric_id   = (int) $this->input->post('metric_id');
        $rating      = (int) $this->input->post('bd_self_rating');
        $remarks     = trim((string) $this->input->post('bd_remarks'));

        if ($session_id <= 0 || $metric_id <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'session_id and metric_id required'), 400);
        }
        if ($rating < 1 || $rating > 5) {
            return $this->_json(array('ok'=>false,'error'=>'bd_self_rating must be 1-5'), 400);
        }

        $ok = $this->rv2->save_bd_self_rating($session_id, $metric_id, $rating, $remarks);
        return $this->_json(array('ok'=>$ok));
    }

    /**
     * POST /api/review/save_manager_rating
     * Body: session_id, metric_id, manager_rating (1-5), manager_remarks (optional)
     */
    public function save_manager_rating() {
        $session_id  = (int) $this->input->post('session_id');
        $metric_id   = (int) $this->input->post('metric_id');
        $rating      = (int) $this->input->post('manager_rating');
        $remarks     = trim((string) $this->input->post('manager_remarks'));

        if ($session_id <= 0 || $metric_id <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'session_id and metric_id required'), 400);
        }
        if ($rating < 1 || $rating > 5) {
            return $this->_json(array('ok'=>false,'error'=>'manager_rating must be 1-5'), 400);
        }

        $ok = $this->rv2->save_manager_rating($session_id, $metric_id, $rating, $remarks);
        return $this->_json(array('ok'=>$ok));
    }

    /**
     * POST /api/review/mark_bd_self_done
     * Body: session_id
     * BD finishes self-assessment. Session moves to bd_self_complete state.
     */
    public function mark_bd_self_done() {
        $session_id = (int) $this->input->post('session_id');
        if ($session_id <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'session_id required'), 400);
        }
        $ok = $this->rv2->mark_bd_self_done($session_id);
        return $this->_json(array('ok'=>$ok));
    }

    /**
     * POST /api/review/close_session
     * Body: session_id, manager_summary (optional)
     * Computes band, delta_pct, creates next schedule row.
     */
    public function close_session() {
        $session_id      = (int) $this->input->post('session_id');
        $manager_summary = trim((string) $this->input->post('manager_summary'));

        if ($session_id <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'session_id required'), 400);
        }

        try {
            $res = $this->rv2->close_session($session_id, $manager_summary);
            return $this->_json(array_merge(array('ok'=>true), $res));
        } catch (Exception $e) {
            return $this->_json(array('ok'=>false,'error'=>$e->getMessage()), 500);
        }
    }

    /**
     * GET /api/review/session?id=<session_id>
     * Returns header + metric rows + action items.
     */
    public function session() {
        $session_id = (int) $this->_param('id', 0);
        if ($session_id <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'id required'), 400);
        }
        $payload = $this->rv2->session_full($session_id);
        if (empty($payload)) {
            return $this->_json(array('ok'=>false,'error'=>'session not found'), 404);
        }
        return $this->_json(array_merge(array('ok'=>true), $payload));
    }

    // ----------------------------------------------------------------------
    // ACTION ITEMS
    // ----------------------------------------------------------------------

    /**
     * POST /api/review/action_item/add
     * Body: session_id, owner_uid, item_text, due_date (YYYY-MM-DD), priority (low|med|high)
     */
    public function action_item_add() {
        $session_id = (int) $this->input->post('session_id');
        $owner_uid  = (int) $this->input->post('owner_uid');
        $item_text  = trim((string) $this->input->post('item_text'));
        $due_date   = trim((string) $this->input->post('due_date'));
        $priority   = trim((string) $this->input->post('priority'));
        if ($priority === '') { $priority = 'med'; }

        if ($session_id <= 0 || $owner_uid <= 0 || $item_text === '') {
            return $this->_json(array('ok'=>false,'error'=>'session_id, owner_uid, item_text required'), 400);
        }
        if (!in_array($priority, array('low','med','high'), true)) {
            return $this->_json(array('ok'=>false,'error'=>'priority must be low, med, or high'), 400);
        }

        $action_id = $this->rv2->add_action_item($session_id, $owner_uid, $item_text, $due_date, $priority);
        return $this->_json(array('ok'=>true, 'action_item_id'=>$action_id));
    }

    /**
     * POST /api/review/action_item/close
     * Body: action_item_id, closure_note (optional), closed_by_uid
     */
    public function action_item_close() {
        $action_id    = (int) $this->input->post('action_item_id');
        $closed_by    = (int) $this->input->post('closed_by_uid');
        $closure_note = trim((string) $this->input->post('closure_note'));

        if ($action_id <= 0 || $closed_by <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'action_item_id and closed_by_uid required'), 400);
        }
        $ok = $this->rv2->close_action_item($action_id, $closed_by, $closure_note);
        return $this->_json(array('ok'=>$ok));
    }

    // ----------------------------------------------------------------------
    // PLAN SUBMIT GATE
    // ----------------------------------------------------------------------

    /**
     * GET /api/review/gate_check?bd_uid=<id>&plan_date=<YYYY-MM-DD>
     *
     * Plan-submit gate hook. Returns:
     *   { ok, allow, mode, overdue_count, message }
     * Modes:
     *   off     - always allow
     *   warning - allow but return overdue message
     *   hard    - block when overdue_count > 0
     */
    public function gate_check() {
        $bd_uid    = (int) $this->_param('bd_uid', 0);
        $plan_date = trim((string) $this->_param('plan_date', date('Y-m-d')));

        if ($bd_uid <= 0) {
            return $this->_json(array('ok'=>false,'error'=>'bd_uid required'), 400);
        }

        $res = $this->rv2->check_plan_submit_gate($bd_uid, $plan_date);
        return $this->_json(array_merge(array('ok'=>true), $res));
    }

    // ----------------------------------------------------------------------
    // SKIP-LEVEL DIRECTOR DASHBOARD
    // ----------------------------------------------------------------------

    /**
     * GET /api/review/skip_level_dashboard?period_start=<YYYY-MM-DD>&period_end=<YYYY-MM-DD>
     */
    public function skip_level_dashboard() {
        $start = trim((string) $this->_param('period_start', date('Y-m-d', strtotime('monday this week'))));
        $end   = trim((string) $this->_param('period_end',   date('Y-m-d')));
        $rows  = $this->rv2->skip_level_dashboard($start, $end);
        return $this->_json(array(
            'ok'          => true,
            'period_start'=> $start,
            'period_end'  => $end,
            'count'       => count($rows),
            'rows'        => $rows,
        ));
    }

    /**
     * POST /api/review/refresh_skip_register
     * Body: period_start, period_end
     * Called by daily 0c647bbd audit cron. Recomputes per-manager metrics
     * and writes review_skip_register rows.
     */
    public function refresh_skip_register() {
        $start = trim((string) $this->input->post('period_start'));
        $end   = trim((string) $this->input->post('period_end'));
        if ($start === '' || $end === '') {
            return $this->_json(array('ok'=>false,'error'=>'period_start and period_end required'), 400);
        }
        $count = $this->rv2->refresh_skip_register($start, $end);
        return $this->_json(array('ok'=>true, 'rows_written'=>$count));
    }

    // ----------------------------------------------------------------------
    // PILOT BOOTSTRAP
    // ----------------------------------------------------------------------

    /**
     * POST /api/review/bootstrap_pilot_schedule
     * Body: manager_uid (default 12), first_review_date (default 2026-06-01)
     * Seeds review_schedule for the 5 pilot BDs. Idempotent.
     */
    public function bootstrap_pilot_schedule() {
        $manager_uid       = (int) $this->input->post('manager_uid');
        if ($manager_uid <= 0) { $manager_uid = 12; }
        $first_review_date = trim((string) $this->input->post('first_review_date'));
        if ($first_review_date === '') { $first_review_date = '2026-06-01'; }

        $res = $this->rv2->bootstrap_pilot_schedule($manager_uid, $first_review_date);
        return $this->_json(array_merge(array('ok'=>true), $res));
    }

    // ----------------------------------------------------------------------
    // INTERNAL HELPERS
    // ----------------------------------------------------------------------

    /**
     * Get param from GET or POST.
     */
    private function _param($key, $default = null) {
        $v = $this->input->get($key);
        if ($v === null || $v === false || $v === '') { $v = $this->input->post($key); }
        if ($v === null || $v === false || $v === '') { return $default; }
        return $v;
    }

    /**
     * Resolve caller uid from session or Bearer claim. Falls back to 0.
     */
    private function _caller_uid() {
        $uid = (int) $this->session->userdata('user_id');
        if ($uid > 0) { return $uid; }
        // Allow X-User-Uid header for cron/internal callers carrying Bearer token.
        $hdr = $this->input->get_request_header('X-User-Uid', true);
        if ($hdr !== null && $hdr !== false && ctype_digit((string)$hdr)) {
            return (int) $hdr;
        }
        return 0;
    }

    /**
     * Emit JSON response with status code.
     */
    private function _json($payload, $status = 200) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload, JSON_UNESCAPED_SLASHES));
    }
}
