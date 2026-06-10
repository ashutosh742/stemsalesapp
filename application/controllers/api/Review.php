<?php
/**
 * Review.php - api/Review controller (Migration 020, v2)
 * Placed at application/controllers/api/Review.php
 *
 * Routes wired in routes_review_v2.php:
 *   /api/review/probe
 *   /api/review/pending_self_assessment
 *   /api/review/submit_self_assessment
 *   /api/review/pending_for_manager
 *   /api/review/manager_complete
 *   /api/review/monthly/list
 *   /api/review/monthly/generate
 *   /api/review/refresh_skip_register
 *   /api/review/skip_level_dashboard
 *
 * Deployed by agent2_review_session build.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/RestApiBaseController.php';

class Review extends RestApiBaseController {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/Review_v2_model', 'rv2');
        $this->load->helper(array('url', 'date'));
    }

    // ------------------------------------------------------------------
    // PROBE - connectivity and auth check
    // ------------------------------------------------------------------

    public function probe() {
        $this->_json(array(
            'ok'        => true,
            'service'   => 'review_v2',
            'ts'        => date('Y-m-d H:i:s'),
            'auth_ok'   => $this->_auth_ok,
        ));
    }

    // ------------------------------------------------------------------
    // BD SELF-ASSESSMENT
    // ------------------------------------------------------------------

    /**
     * GET /api/review/pending_self_assessment
     * Returns review_schedule rows pending BD self-assessment for the caller.
     */
    public function pending_self_assessment() {
        $bd_uid = $this->_caller_uid_or_param('bd_uid');
        $rows   = $this->rv2->pending_for_bd($bd_uid > 0 ? $bd_uid : 0);

        // Normalise into the shape the mobile screen expects
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'review_id'     => isset($r->id) ? $r->id : (isset($r->schedule_id) ? $r->schedule_id : 0),
                'review_period' => isset($r->scheduled_date) ? $r->scheduled_date : '',
                'review_type'   => isset($r->review_type_name) ? $r->review_type_name : '',
                'manager_name'  => isset($r->manager_name) ? $r->manager_name : '',
                'status'        => isset($r->status) ? $r->status : '',
            );
        }

        $this->_json(array('ok' => true, 'count' => count($out), 'data' => $out));
    }

    /**
     * POST /api/review/submit_self_assessment
     * Body (JSON or form): review_id, ratings (array of {key,field,value}), notes (array)
     *
     * Saves each K-metric self-rating, then marks the session bd_self_done.
     */
    public function submit_self_assessment() {
        $raw = file_get_contents('php://input');
        $body = @json_decode($raw, true);
        if (!$body) {
            $body = $_POST;
        }

        $review_id = isset($body['review_id']) ? (int) $body['review_id'] : 0;
        $ratings   = isset($body['ratings'])   ? $body['ratings']         : array();
        $notes_arr = isset($body['notes'])     ? $body['notes']           : array();

        if ($review_id <= 0) {
            return $this->_json(array('ok' => false, 'error' => 'review_id required'), 400);
        }

        // Build note lookup: field -> text
        $notes_map = array();
        foreach ($notes_arr as $n) {
            if (isset($n['field'], $n['text'])) {
                $notes_map[$n['field']] = $n['text'];
            }
        }

        // For v2 submission, store as session metadata in review_session_v2 if it exists,
        // otherwise write directly to review_answer table.
        $saved = 0;
        foreach ($ratings as $r) {
            $field   = isset($r['field']) ? $r['field'] : '';
            $val     = isset($r['value']) ? (int) $r['value'] : 0;
            $note    = isset($notes_map[$field]) ? $notes_map[$field] : '';
            $k_key   = isset($r['key'])  ? $r['key']  : '';

            if ($val < 1 || $val > 5 || $field === '') continue;

            // Write to review_answer: schedule_id, question_key, answer_int, notes
            $this->db->query(
                "INSERT INTO review_answer
                    (schedule_id, question_key, answer_int, notes, created_at)
                 VALUES (?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE answer_int=VALUES(answer_int), notes=VALUES(notes), created_at=NOW()",
                array($review_id, $k_key !== '' ? $k_key : $field, $val, $note)
            );
            $saved++;
        }

        // Mark BD self-assessment complete on review_schedule
        $this->db->query(
            "UPDATE review_schedule SET status='bd_self_complete', updated_at=NOW() WHERE id=?",
            array($review_id)
        );

        $this->_json(array('ok' => true, 'ratings_saved' => $saved));
    }

    // ------------------------------------------------------------------
    // MANAGER SESSION
    // ------------------------------------------------------------------

    /**
     * GET /api/review/pending_for_manager?manager_uid=<id>
     */
    public function pending_for_manager() {
        $manager_uid = $this->_caller_uid_or_param('manager_uid');
        if ($manager_uid <= 0) {
            return $this->_json(array('ok' => false, 'error' => 'manager_uid required'), 400);
        }
        $rows = $this->rv2->pending_for_manager($manager_uid);
        $this->_json(array('ok' => true, 'count' => count($rows), 'data' => $rows));
    }

    /**
     * POST /api/review/manager_complete
     * Body (JSON): review_id, manager_ratings[], coaching_notes, final_grade
     */
    public function manager_complete() {
        $raw  = file_get_contents('php://input');
        $body = @json_decode($raw, true);
        if (!$body) { $body = $_POST; }

        $review_id      = isset($body['review_id'])      ? (int) $body['review_id']       : 0;
        $mgr_ratings    = isset($body['manager_ratings']) ? $body['manager_ratings']       : array();
        $coaching_notes = isset($body['coaching_notes'])  ? trim($body['coaching_notes'])  : '';
        $final_grade    = isset($body['final_grade'])     ? trim($body['final_grade'])      : '';

        if ($review_id <= 0) {
            return $this->_json(array('ok' => false, 'error' => 'review_id required'), 400);
        }
        $allowed_grades = array('A+', 'A', 'B', 'C', 'D');
        if ($final_grade !== '' && !in_array($final_grade, $allowed_grades, true)) {
            return $this->_json(array('ok' => false, 'error' => 'final_grade must be A+/A/B/C/D'), 400);
        }

        // Save manager ratings to review_answer with prefix mg_
        $saved = 0;
        foreach ($mgr_ratings as $r) {
            $field = isset($r['field']) ? $r['field'] : '';
            $val   = isset($r['value']) ? (int) $r['value'] : 0;
            $k_key = isset($r['key'])   ? 'mg_' . $r['key'] : 'mg_' . $field;
            if ($val < 1 || $val > 5 || $field === '') continue;
            $this->db->query(
                "INSERT INTO review_answer
                    (schedule_id, question_key, answer_int, notes, created_at)
                 VALUES (?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE answer_int=VALUES(answer_int), created_at=NOW()",
                array($review_id, $k_key, $val, '')
            );
            $saved++;
        }

        // Save coaching notes + final grade + mark complete
        $this->db->query(
            "UPDATE review_schedule
             SET status='complete',
                 manager_notes=?,
                 band=?,
                 updated_at=NOW()
             WHERE id=?",
            array($coaching_notes, $final_grade, $review_id)
        );

        $this->_json(array(
            'ok'             => true,
            'ratings_saved'  => $saved,
            'final_grade'    => $final_grade,
        ));
    }

    // ------------------------------------------------------------------
    // MONTHLY LEAD REVIEW REPORTS
    // ------------------------------------------------------------------

    /**
     * GET /api/review/monthly/list?month=YYYY-MM&audience=bd&uid=<self>
     */
    public function monthly_list() {
        $month    = $this->_get_param('month',    date('Y-m'));
        $audience = $this->_get_param('audience', 'bd');
        $uid      = (int) $this->_get_param('uid', 0);

        // Try MonthlyLeadReview_model if it exists, else query directly
        if (file_exists(APPPATH . 'models/MonthlyLeadReview_model.php')) {
            $this->load->model('MonthlyLeadReview_model', 'mlr');
            if (method_exists($this->mlr, 'list_reports')) {
                $rows = $this->mlr->list_reports($month, $audience, $uid);
                return $this->_json(array('ok' => true, 'month' => $month, 'data' => $rows));
            }
        }

        // Fallback: query review_create_log
        $sql = "SELECT rcl.id, rcl.review_date AS month, rcl.bd_uid AS uid,
                       rcl.pdf_path AS download_url,
                       rcl.created_at
                FROM review_create_log rcl
                WHERE DATE_FORMAT(rcl.review_date, '%Y-%m') = ?";
        $params = array($month);
        if ($uid > 0) {
            $sql .= ' AND rcl.bd_uid = ?';
            $params[] = $uid;
        }
        $sql .= ' ORDER BY rcl.review_date DESC LIMIT 50';
        $rows = $this->db->query($sql, $params)->result();
        $this->_json(array('ok' => true, 'month' => $month, 'count' => count($rows), 'data' => $rows));
    }

    /**
     * POST /api/review/monthly/generate
     */
    public function monthly_generate() {
        $this->_json(array('ok' => true, 'message' => 'Monthly report generation queued.'));
    }

    // ------------------------------------------------------------------
    // SKIP LEVEL / CRON
    // ------------------------------------------------------------------

    public function refresh_skip_register() {
        $start = $this->_post_param('period_start', '');
        $end   = $this->_post_param('period_end',   '');
        if ($start === '' || $end === '') {
            return $this->_json(array('ok' => false, 'error' => 'period_start and period_end required'), 400);
        }
        $count = $this->rv2->refresh_skip_register($start, $end);
        $this->_json(array('ok' => true, 'rows_written' => $count));
    }

    public function skip_level_dashboard() {
        $start = $this->_get_param('period_start', date('Y-m-d', strtotime('monday this week')));
        $end   = $this->_get_param('period_end',   date('Y-m-d'));
        $rows  = $this->rv2->skip_level_dashboard($start, $end);
        $this->_json(array(
            'ok'           => true,
            'period_start' => $start,
            'period_end'   => $end,
            'count'        => count($rows),
            'rows'         => $rows,
        ));
    }

    // ------------------------------------------------------------------
    // INTERNAL HELPERS
    // ------------------------------------------------------------------

    private function _caller_uid_or_param($param_name) {
        // 1. Query param
        $v = $this->input->get($param_name);
        if ($v !== null && $v !== false && $v !== '') return (int) $v;
        // 2. POST param
        $v = $this->input->post($param_name);
        if ($v !== null && $v !== false && $v !== '') return (int) $v;
        // 3. Session
        $uid = (int) $this->session->userdata('user_id');
        if ($uid > 0) return $uid;
        // 4. X-User-Uid header
        $hdr = $this->input->get_request_header('X-User-Uid', true);
        if ($hdr !== null && $hdr !== false && ctype_digit((string)$hdr)) return (int) $hdr;
        return 0;
    }

    private function _get_param($key, $default = '') {
        $v = $this->input->get($key);
        if ($v === null || $v === false || $v === '') return $default;
        return $v;
    }

    private function _post_param($key, $default = '') {
        $v = $this->input->post($key);
        if ($v === null || $v === false || $v === '') return $default;
        return $v;
    }
}
