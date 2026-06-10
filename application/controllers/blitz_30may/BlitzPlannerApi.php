<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BlitzPlannerApi
 * application/controllers/blitz_30may/BlitzPlannerApi.php
 *
 * Endpoints:
 *   GET /api/planner_v2/purpose_cascade?actiontype_id={n} - purposes from purpose table
 *   GET /api/mom_v2/queue?cm_uid={uid}                   - pending_cm MoM submissions
 *   GET /api/plan_approval/queue?cm_uid={uid}            - pending plan approvals
 *
 * purpose table columns: id, name, action_id, status_id
 * mom_v2_submission columns: submission_id, event_id, bd_uid, cid_id, cm_uid,
 *   agenda_locked, quality_grade, quality_score, status, submitted_at,
 *   cm_action_at, cm_action_reason, created_at, updated_at
 * task_plan_for_today columns: id, user_id, admin_id, date, taskcnt,
 *   would_you_want, request_remarks, approvel_status, remarks, action_by,
 *   created_at, updated_at
 *
 * Route:
 *   routes_blitz_30may_b.php -> blitz_30may/BlitzPlannerApi/purpose_cascade
 *   routes_blitz_30may_b.php -> blitz_30may/BlitzPlannerApi/mom_queue
 *   routes_blitz_30may_b.php -> blitz_30may/BlitzPlannerApi/plan_approval_queue
 */
class BlitzPlannerApi extends CI_Controller {

    private $bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
        $this->load->library('BearerAuth');
    }

    private function _bearer() {
        $auth = $this->bearerauth->resolve();
        if (empty($auth['ok'])) {
            $this->output->set_status_header(401)
                ->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
            return false;
        }
        return true;
    }

    private function _json($rows, $extra = array(), $route = '') {
        $payload = array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array_merge(array('count' => count($rows)), $extra),
            'route'        => $route,
            'generated_at' => date('c'),
        );
        $this->output->set_output(json_encode($payload));
    }

    // =========================================================================
    // GET /api/planner_v2/purpose_cascade?actiontype_id={n}
    // Returns purposes for a given actiontype from the purpose table.
    // =========================================================================
    public function purpose_cascade() {
        if (!$this->_bearer()) return;

        $actiontype_id = (int) $this->input->get('actiontype_id', TRUE);

        if ($actiontype_id <= 0) {
            $this->_json(array(), array(
                'count'         => 0,
                'note'          => 'actiontype_id param missing or zero',
                'actiontype_id' => $actiontype_id,
            ), 'api/planner_v2/purpose_cascade');
            return;
        }

        $rows = $this->db->query(
            "SELECT p.id AS purpose_id, p.name AS purpose_name,
                    p.action_id AS actiontype_id, p.status_id
             FROM purpose p
             WHERE p.action_id = ?
             ORDER BY p.id ASC",
            array($actiontype_id)
        )->result_array();

        $extra = array('actiontype_id' => $actiontype_id, 'source_table' => 'purpose');
        if (empty($rows)) {
            $extra['note']   = 'no purposes found for this actiontype_id';
            $extra['reason'] = 'no_rows';
        }

        $this->_json($rows, $extra, 'api/planner_v2/purpose_cascade');
    }

    // =========================================================================
    // GET /api/mom_v2/queue?cm_uid={uid}
    // Returns mom_v2_submission rows in status=pending_cm for BDs in CM clusters.
    // =========================================================================
    public function mom_queue() {
        if (!$this->_bearer()) return;

        $cm_uid = (int) $this->input->get('cm_uid', TRUE);

        if ($cm_uid <= 0) {
            $this->_json(array(), array(
                'count' => 0,
                'note'  => 'cm_uid param missing or zero',
            ), 'api/mom_v2/queue');
            return;
        }

        // Get clusters managed by this CM
        $cluster_rows = $this->db->query(
            "SELECT cluster_id FROM user_cluster_mapping WHERE user_id = ? AND status = 1",
            array($cm_uid)
        )->result_array();

        if (empty($cluster_rows)) {
            $this->_json(array(), array(
                'count'  => 0,
                'cm_uid' => $cm_uid,
                'reason' => 'no_rows',
                'note'   => 'CM has no active cluster assignments',
            ), 'api/mom_v2/queue');
            return;
        }

        $cluster_ids = array_column($cluster_rows, 'cluster_id');
        $cluster_in  = implode(',', array_map('intval', $cluster_ids));

        // Get active BDs in those clusters
        $bd_rows = $this->db->query(
            "SELECT ucm.user_id
             FROM user_cluster_mapping ucm
             JOIN user u ON u.uid = ucm.user_id
             WHERE ucm.cluster_id IN ({$cluster_in})
               AND ucm.status = 1
               AND u.type_id = 3
               AND u.active  = 1"
        )->result_array();

        if (empty($bd_rows)) {
            $this->_json(array(), array(
                'count'       => 0,
                'cm_uid'      => $cm_uid,
                'cluster_ids' => $cluster_ids,
                'reason'      => 'no_rows',
                'note'        => 'No active BDs found in CM clusters',
            ), 'api/mom_v2/queue');
            return;
        }

        $bd_ids = array_column($bd_rows, 'user_id');
        $bd_in  = implode(',', array_map('intval', $bd_ids));

        $sql = "SELECT
                    ms.submission_id,
                    ms.event_id,
                    ms.bd_uid,
                    ud.name      AS bd_name,
                    ms.cid_id,
                    cm.compname  AS school_name,
                    ms.quality_grade,
                    ms.quality_score,
                    ms.status,
                    ms.submitted_at,
                    ms.created_at
                FROM mom_v2_submission ms
                LEFT JOIN user_details ud ON ud.user_id = ms.bd_uid
                LEFT JOIN init_call ic    ON ic.id = ms.cid_id
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                WHERE ms.bd_uid IN ({$bd_in})
                  AND ms.status = 'pending_cm'
                ORDER BY ms.submitted_at DESC
                LIMIT 100";

        $rows = $this->db->query($sql)->result_array();

        $extra = array(
            'cm_uid'      => $cm_uid,
            'cluster_ids' => $cluster_ids,
            'bd_uids'     => $bd_ids,
        );
        if (empty($rows)) {
            $extra['note']   = 'no pending_cm MoM submissions at this time';
            $extra['reason'] = 'no_rows';
        }

        $this->_json($rows, $extra, 'api/mom_v2/queue');
    }

    // =========================================================================
    // GET /api/plan_approval/queue?cm_uid={uid}
    // Returns task_plan_for_today rows with approvel_status pending/empty
    // for BDs in the CM's cluster.
    // =========================================================================
    public function plan_approval_queue() {
        if (!$this->_bearer()) return;

        $cm_uid = (int) $this->input->get('cm_uid', TRUE);

        if ($cm_uid <= 0) {
            $this->_json(array(), array(
                'count' => 0,
                'note'  => 'cm_uid param missing or zero',
            ), 'api/plan_approval/queue');
            return;
        }

        $cluster_rows = $this->db->query(
            "SELECT cluster_id FROM user_cluster_mapping WHERE user_id = ? AND status = 1",
            array($cm_uid)
        )->result_array();

        if (empty($cluster_rows)) {
            $this->_json(array(), array(
                'count'  => 0,
                'cm_uid' => $cm_uid,
                'reason' => 'no_rows',
                'note'   => 'CM has no active cluster assignments',
            ), 'api/plan_approval/queue');
            return;
        }

        $cluster_ids = array_column($cluster_rows, 'cluster_id');
        $cluster_in  = implode(',', array_map('intval', $cluster_ids));

        $member_rows = $this->db->query(
            "SELECT DISTINCT ucm.user_id
             FROM user_cluster_mapping ucm
             JOIN user u ON u.uid = ucm.user_id
             WHERE ucm.cluster_id IN ({$cluster_in})
               AND ucm.status = 1
               AND u.active = 1"
        )->result_array();

        if (empty($member_rows)) {
            $this->_json(array(), array(
                'count'  => 0,
                'cm_uid' => $cm_uid,
                'reason' => 'no_rows',
                'note'   => 'No active members found in CM clusters',
            ), 'api/plan_approval/queue');
            return;
        }

        $member_ids = array_column($member_rows, 'user_id');
        $member_in  = implode(',', array_map('intval', $member_ids));

        $sql = "SELECT
                    t.id,
                    t.user_id        AS bd_uid,
                    u.name           AS bd_name,
                    t.date           AS plan_date,
                    t.taskcnt,
                    t.would_you_want,
                    t.request_remarks,
                    t.approvel_status,
                    t.remarks        AS cm_remarks,
                    t.action_by,
                    t.created_at,
                    t.updated_at
                FROM task_plan_for_today t
                JOIN user u ON u.uid = t.user_id
                WHERE t.user_id IN ({$member_in})
                  AND (t.approvel_status = 'pending'
                    OR t.approvel_status = ''
                    OR t.approvel_status IS NULL)
                ORDER BY t.created_at DESC
                LIMIT 100";

        $rows = $this->db->query($sql)->result_array();

        $extra = array(
            'cm_uid'       => $cm_uid,
            'cluster_ids'  => $cluster_ids,
            'source_table' => 'task_plan_for_today',
        );
        if (empty($rows)) {
            $extra['note']   = 'no pending plan approvals at this time';
            $extra['reason'] = 'no_rows';
        }

        $this->_json($rows, $extra, 'api/plan_approval/queue');
    }
}
