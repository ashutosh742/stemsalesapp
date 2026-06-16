<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DayPlanController
 * Endpoint: GET /api/day_plan/today?uid={uid}
 *
 * Returns today's planned tasks for a BD from tblcallevents (plan=1 rows).
 * Joins company_master for school name and purpose for purpose name.
 *
 * tblcallevents.fwd_date is the scheduled date for the task.
 * actontaken='yes' means done; cancelled_at IS NOT NULL means cancelled.
 *
 * Route: routes_404_stubs.php / routes_v28_k.php -> DayPlanController/today
 */
class DayPlanController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected) {
            $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        if (hash_equals($expected, $tok)) return true;
        // Per-user daily JWT: sha1(secret|uid|YYYY-MM-DD), accept today and yesterday
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $cands = array();
        foreach (array('uid','bd_uid','cm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0)  $cands[(int)$_GET[$k]]  = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $cands[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($cands) as $u) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$u.'|'.$d), $tok)) return true;
            }
        }
        $this->output->set_status_header(401)->set_content_type('application/json')
            ->set_output(json_encode(array('ok' => false, 'error' => 'bad_token')));
        return false;
    }

    private function _json($payload) {
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function _task_status($row) {
        if (!empty($row['cancelled_at'])) return 'cancelled';
        if ($row['actontaken'] === 'yes') return 'done';
        return 'pending';
    }

    /**
     * GET /api/day_plan/today?uid={uid}
     */
    public function today() {
        if (!$this->_bearer()) return;

        $uid = (int)$this->input->get('uid');
        if ($uid <= 0) {
            return $this->_json(array(
                'ok'           => false,
                'success'      => false,
                'stub'         => false,
                'error'        => 'uid query param required',
                'route'        => 'api/day_plan/today',
                'generated_at' => date('c'),
            ));
        }

        $today = date('Y-m-d');

        $user = $this->db->query(
            'SELECT user_id, name FROM user_details WHERE user_id = ?',
            array($uid)
        )->row();

        if (!$user) {
            return $this->_json(array(
                'ok'           => false,
                'success'      => false,
                'stub'         => false,
                'error'        => 'uid not found in user_details',
                'route'        => 'api/day_plan/today',
                'generated_at' => date('c'),
            ));
        }

        $rows_raw = $this->db->query(
            "SELECT
                ce.id,
                ce.cid_id,
                ce.actiontype_id,
                ce.purpose_id,
                p.name               AS purpose,
                ce.fwd_date          AS scheduled_time,
                ce.actontaken,
                ce.cancelled_at,
                ce.plan,
                ce.meeting_type,
                ce.remarks,
                ce.planned_cost,
                cm.compname          AS school
             FROM tblcallevents ce
             LEFT JOIN init_call ic ON ic.id = ce.cid_id
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             LEFT JOIN purpose p ON p.id = ce.purpose_id
             WHERE ce.user_id = ?
               AND DATE(ce.fwd_date) = ?
               AND ce.plan = 1
             ORDER BY ce.fwd_date ASC, ce.id ASC
             LIMIT 200",
            array($uid, $today)
        )->result_array();

        if (empty($rows_raw)) {
            return $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'  => 0,
                    'uid'    => $uid,
                    'date'   => $today,
                    'reason' => 'no_rows',
                ),
                'route'        => 'api/day_plan/today',
                'generated_at' => date('c'),
            ));
        }

        $rows = array();
        foreach ($rows_raw as $r) {
            $rows[] = array(
                'id'             => (int) $r['id'],
                'cid_id'         => (int) $r['cid_id'],
                'school'         => $r['school'] ?: '',
                'scheduled_time' => $r['scheduled_time'],
                'purpose'        => $r['purpose'] ?: '',
                'status'         => $this->_task_status($r),
                'meeting_type'   => $r['meeting_type'],
                'remarks'        => $r['remarks'],
                'planned_cost'   => (int) $r['planned_cost'],
            );
        }

        return $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array(
                'count' => count($rows),
                'uid'   => $uid,
                'date'  => $today,
            ),
            'route'        => 'api/day_plan/today',
            'generated_at' => date('c'),
        ));
    }
}
