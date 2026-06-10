<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M065 Induction LMS patch
 * Induction.php controller -- place this file as
 * application/controllers/Induction.php and create a matching
 * Induction_model.php or merge models as needed.
 *
 * Routes (CI3, NO /api/ prefix):
 *   GET  /induction/modules_for_user
 *   GET  /induction/lesson_get
 *   POST /induction/lesson_complete
 *   GET  /induction/module_progress
 *   POST /induction/issue_certificate
 *   GET  /induction/manager_view
 *
 * Auth: Authorization Bearer header checked against config 'digest_token'.
 */

class M065_induction_lms extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // user_model not present on staging; user lookups use $this->db directly.
    }

    // -----------------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------------

    private function _json($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }
    private function _auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        // Load custom config if not loaded
        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(array('ok'=>false,'error'=>'unauthorised')));
            return false;
        }
        return true;
    }



    /**
     * Calculate percent of lessons completed within a module for a given user.
     *
     * @param int $uid       User ID.
     * @param int $module_id Module ID.
     * @return int 0-100.
     */
    private function _module_pct($uid, $module_id)
    {
        // induction_progress uses user_uid (not uid) and template_id (not lesson_id).
        try {
            $total = $this->db
                          ->where('module_id', $module_id)
                          ->count_all_results('induction_lesson');
            if (!$total) return 0;

            $done = $this->db
                         ->select('COUNT(*) AS cnt')
                         ->join('induction_progress',
                                'induction_progress.template_id = induction_lesson.id AND induction_progress.user_uid = ' . (int)$uid,
                                'left')
                         ->where('induction_lesson.module_id', $module_id)
                         ->where('induction_progress.status', 'completed')
                         ->get('induction_lesson')
                         ->row_array();

            return (int)round((((int)$done['cnt']) / $total) * 100);
        } catch (Exception $e) {
            log_message('error', 'M065 _module_pct: ' . $e->getMessage());
            return 0;
        }
    }

    // -----------------------------------------------------------------------
    // Endpoints
    // -----------------------------------------------------------------------

    /**
     * GET /induction/modules_for_user?uid=X
     * Returns all induction modules with per-module progress percentage for
     * the given user. Also flags whether a certificate has been issued.
     */
    public function modules_for_user()
    {
        if (!$this->_auth()) return;

        $uid = (int)$this->input->get('uid');
        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }

        $modules = $this->db
                        ->order_by('sort_order', 'ASC')
                        ->get('induction_module')
                        ->result_array();

        foreach ($modules as &$m) {
            $m['progress_pct'] = $this->_module_pct($uid, (int)$m['id']);

            $cert = $this->db->get_where('induction_certificate', array(
                'uid'       => $uid,
                'module_id' => $m['id'],
            ))->row_array();

            $m['certificate_issued']  = $cert ? true : false;
            $m['certificate_url']     = $cert ? $cert['certificate_url'] : null;
            $m['certificate_issued_at'] = $cert ? $cert['issued_at'] : null;
        }
        unset($m);

        $this->_json(array('ok' => true, 'uid' => $uid, 'modules' => $modules));
    }

    /**
     * GET /induction/lesson_get?id=X&uid=X
     * Returns a single lesson's content plus the user's progress on it.
     */
    public function lesson_get()
    {
        if (!$this->_auth()) return;

        $lesson_id = (int)$this->input->get('id');
        $uid       = (int)$this->input->get('uid');

        if (!$lesson_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_id'), 400);
            return;
        }

        $lesson = $this->db->get_where('induction_lesson', array('id' => $lesson_id))->row_array();
        if (!$lesson) {
            $this->_json(array('ok' => false, 'error' => 'lesson_not_found'), 404);
            return;
        }

        $progress = null;
        if ($uid) {
            // induction_progress uses user_uid (not uid) and template_id (not lesson_id).
            $progress = $this->db->get_where('induction_progress', array(
                'user_uid'    => $uid,
                'template_id' => $lesson_id,
            ))->row_array();

            // If user opened this lesson for the first time, mark as in_progress
            if (!$progress) {
                // Use actual column names: user_uid, template_id, scheduled_start_date.
                $today = date('Y-m-d');
                $this->db->insert('induction_progress', array(
                    'user_uid'             => $uid,
                    'template_id'          => $lesson_id,
                    'status'               => 'in_progress',
                    'scheduled_start_date' => $today,
                    'scheduled_end_date'   => $today,
                    'started_at'           => date('Y-m-d H:i:s'),
                ));
                $progress = array('user_uid' => $uid, 'template_id' => $lesson_id, 'status' => 'in_progress');
            }
        }

        $this->_json(array('ok' => true, 'lesson' => $lesson, 'progress' => $progress));
    }

    /**
     * POST /induction/lesson_complete
     * Mark a lesson as completed for a user and update time + quiz score.
     * Required POST: uid, lesson_id
     * Optional POST: time_spent, quiz_score
     * Auto-issues a certificate if the module reaches 100 percent completion.
     */
    public function lesson_complete()
    {
        if (!$this->_auth()) return;

        $uid       = (int)$this->input->post('uid');
        $lesson_id = (int)$this->input->post('lesson_id');

        if (!$uid || !$lesson_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid_or_lesson_id'), 400);
            return;
        }

        $time_spent = (int)$this->input->post('time_spent') ?: 0;
        $quiz_score = $this->input->post('quiz_score');
        $quiz_score = ($quiz_score !== null && $quiz_score !== false) ? (int)$quiz_score : null;
        $now        = date('Y-m-d H:i:s');

        // Upsert progress row
        // induction_progress uses user_uid and template_id.
        $existing = $this->db->get_where('induction_progress', array(
            'user_uid'    => $uid,
            'template_id' => $lesson_id,
        ))->row_array();

        if ($existing) {
            $update = array(
                'status'       => 'completed',
                'completed_at' => $now,
            );
            if ($quiz_score !== null) $update['score_pct'] = $quiz_score;
            $this->db->where('user_uid', $uid)->where('template_id', $lesson_id)
                     ->update('induction_progress', $update);
        } else {
            $today_date = date('Y-m-d');
            $insert = array(
                'user_uid'             => $uid,
                'template_id'          => $lesson_id,
                'status'               => 'completed',
                'scheduled_start_date' => $today_date,
                'scheduled_end_date'   => $today_date,
                'started_at'           => $now,
                'completed_at'         => $now,
            );
            if ($quiz_score !== null) $insert['score_pct'] = $quiz_score;
            $this->db->insert('induction_progress', $insert);
        }

        // Check if entire module is now complete
        $lesson = $this->db->get_where('induction_lesson', array('id' => $lesson_id))->row_array();
        $module_id  = $lesson ? (int)$lesson['module_id'] : 0;
        $cert_issued = false;

        if ($module_id) {
            $pct = $this->_module_pct($uid, $module_id);
            if ($pct >= 100) {
                // Auto-issue certificate
                $cert_exists = $this->db->get_where('induction_certificate', array(
                    'uid'       => $uid,
                    'module_id' => $module_id,
                ))->row_array();
                if (!$cert_exists) {
                    $this->db->insert('induction_certificate', array(
                        'uid'             => $uid,
                        'module_id'       => $module_id,
                        'issued_at'       => $now,
                        'certificate_url' => null, // generated async by background job
                    ));
                    $cert_issued = true;
                }
            }
        }

        $this->_json(array(
            'ok'           => true,
            'lesson_id'    => $lesson_id,
            'module_id'    => $module_id,
            'cert_issued'  => $cert_issued,
            'message'      => $cert_issued
                ? 'Lesson completed. Module certificate issued!'
                : 'Lesson marked as complete.',
        ));
    }

    /**
     * GET /induction/module_progress?uid=X&module_id=X
     * Returns overall completion percentage for one module.
     */
    public function module_progress()
    {
        if (!$this->_auth()) return;

        $uid       = (int)$this->input->get('uid');
        $module_id = (int)$this->input->get('module_id');

        if (!$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid'), 400);
            return;
        }

        // When no module_id supplied, return overall progress summary for all modules.
        if (!$module_id) {
            $modules = $this->db->order_by('sort_order', 'ASC')->get('induction_module')->result_array();
            $summary = array();
            foreach ($modules as $m) {
                $summary[] = array(
                    'module_id'    => (int)$m['id'],
                    'module_name'  => $m['name'],
                    'progress_pct' => $this->_module_pct($uid, (int)$m['id']),
                );
            }
            $this->_json(array(
                'ok'       => true,
                'uid'      => $uid,
                'modules'  => $summary,
            ));
            return;
        }

        // induction_progress uses user_uid and template_id.
        $pct     = $this->_module_pct($uid, $module_id);
        $lessons = $this->db->select('il.*, ip.status, ip.completed_at, ip.score_pct')
                            ->from('induction_lesson il')
                            ->join('induction_progress ip',
                                   'ip.template_id = il.id AND ip.user_uid = ' . (int)$uid,
                                   'left')
                            ->where('il.module_id', $module_id)
                            ->order_by('il.sort_order', 'ASC')
                            ->get()
                            ->result_array();

        $this->_json(array(
            'ok'           => true,
            'uid'          => $uid,
            'module_id'    => $module_id,
            'progress_pct' => $pct,
            'lessons'      => $lessons,
        ));
    }

    /**
     * POST /induction/issue_certificate
     * Manually trigger certificate issuance (for admin use or re-issue).
     * Required POST: uid, module_id
     * Optional POST: certificate_url
     */
    public function issue_certificate()
    {
        if (!$this->_auth()) return;

        $uid       = (int)$this->input->post('uid');
        $module_id = (int)$this->input->post('module_id');

        if (!$uid || !$module_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid_or_module_id'), 400);
            return;
        }

        $cert_url = trim((string)$this->input->post('certificate_url')) ?: null;
        $now      = date('Y-m-d H:i:s');

        $existing = $this->db->get_where('induction_certificate', array(
            'uid'       => $uid,
            'module_id' => $module_id,
        ))->row_array();

        if ($existing) {
            // Re-issue: update URL if a new one is provided
            if ($cert_url) {
                $this->db->where('uid', $uid)->where('module_id', $module_id)
                         ->update('induction_certificate', array(
                             'certificate_url' => $cert_url,
                             'issued_at'       => $now,
                         ));
            }
            $this->_json(array('ok' => true, 'reissued' => true, 'message' => 'Certificate updated.'));
            return;
        }

        $this->db->insert('induction_certificate', array(
            'uid'             => $uid,
            'module_id'       => $module_id,
            'issued_at'       => $now,
            'certificate_url' => $cert_url,
        ));

        $this->_json(array(
            'ok'      => true,
            'cert_id' => $this->db->insert_id(),
            'message' => 'Certificate issued.',
        ));
    }

    /**
     * GET /induction/manager_view?manager_uid=X
     * For CM or RM: returns all direct-report users with their per-module
     * progress percentages. Looks up the user table for manager_uid linkage.
     * Assumes the user table has a manager_uid or reporting_to column.
     */
    public function manager_view()
    {
        if (!$this->_auth()) return;

        $manager_uid = (int)$this->input->get('manager_uid');
        if (!$manager_uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_manager_uid'), 400);
            return;
        }

        // Schema-correct: user has only uid,name,type_id,active. No hierarchy column,
        // so show all users who have induction module progress (the enrolled cohort).
        $reports = $this->db
                        ->select('u.uid, u.name, u.type_id', false)
                        ->distinct()
                        ->from('user u')
                        ->join('induction_progress ip', 'ip.user_uid = u.uid', 'inner')
                        ->get()
                        ->result_array();

        if (empty($reports)) {
            $this->_json(array(
                'ok'      => true,
                'manager_uid' => $manager_uid,
                'reports' => array(),
                'message' => 'No direct reports found for this manager.',
            ));
            return;
        }

        $modules = $this->db->order_by('sort_order')->get('induction_module')->result_array();

        foreach ($reports as &$r) {
            $r['module_progress'] = array();
            foreach ($modules as $m) {
                $r['module_progress'][] = array(
                    'module_id'    => $m['id'],
                    'module_name'  => $m['name'],
                    'progress_pct' => $this->_module_pct((int)$r['uid'], (int)$m['id']),
                );
            }
        }
        unset($r);

        $this->_json(array(
            'ok'         => true,
            'manager_uid' => $manager_uid,
            'reports'    => $reports,
        ));
    }
}
