<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Task lifecycle endpoints (today_plan, start, save, submit).
 * Backed by tblcallevents with the four v2.8 timestamps:
 *   plan_time, initiate_time, update_time, complete_time.
 */
class TaskV28 extends CI_Controller {

    public function probe() {
        $this->_json(['ok' => true, 'controller' => 'TaskV28']);
    }

    private function _auth_or_die() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            $this->_json(['ok' => false, 'message' => 'Unauthorized']);
            return false;
        }
        return true;
    }

    public function today_plan() {
        if (!$this->_auth_or_die()) return;
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        if ($bd_uid <= 0) {
            http_response_code(400);
            $this->_json(['ok' => false, 'message' => 'bd_uid required']);
            return;
        }
        $today = date('Y-m-d');
        $sql = "SELECT t.id AS task_id, t.cid_id, cm.compname AS company_name,
                       t.actiontype_id, t.purpose_id, t.date,
                       t.plan_time, t.initiate_time, t.update_time, t.complete_time,
                       t.status_id, t.remarks
                FROM tblcallevents t
                LEFT JOIN init_call ic       ON ic.id = t.cid_id
                LEFT JOIN company_master cm  ON cm.id = ic.cmpid_id
                WHERE t.user_id = ?
                  AND DATE(t.date) = ?
                ORDER BY t.plan_time ASC, t.date ASC
                LIMIT 200";
        $q = $this->db->query($sql, [$bd_uid, $today]);
        $this->_json(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    public function start() {
        if (!$this->_auth_or_die()) return;
        $task_id = (int) $this->input->post('task_id', TRUE);
        if ($task_id <= 0) {
            http_response_code(400);
            $this->_json(['ok' => false, 'message' => 'task_id required']);
            return;
        }
        $this->db->query(
            "UPDATE tblcallevents SET initiate_time = NOW(), update_time = NOW() WHERE id = ? AND initiate_time IS NULL",
            [$task_id]
        );
        $aff = $this->db->affected_rows();
        $this->_json(['ok' => true, 'task_id' => $task_id, 'affected' => $aff]);
    }

    public function save() {
        if (!$this->_auth_or_die()) return;
        $task_id = (int) $this->input->post('task_id', TRUE);
        $remarks = (string) $this->input->post('remarks', TRUE);
        if ($task_id <= 0) {
            http_response_code(400);
            $this->_json(['ok' => false, 'message' => 'task_id required']);
            return;
        }
        $this->db->query(
            "UPDATE tblcallevents SET remarks = ?, update_time = NOW() WHERE id = ?",
            [$remarks, $task_id]
        );
        $this->_json(['ok' => true, 'task_id' => $task_id]);
    }

    public function submit() {
        if (!$this->_auth_or_die()) return;
        $task_id = (int) $this->input->post('task_id', TRUE);
        if ($task_id <= 0) {
            http_response_code(400);
            $this->_json(['ok' => false, 'message' => 'task_id required']);
            return;
        }
        $this->db->query(
            "UPDATE tblcallevents SET complete_time = NOW(), update_time = NOW() WHERE id = ? AND complete_time IS NULL",
            [$task_id]
        );
        $this->_json(['ok' => true, 'task_id' => $task_id, 'affected' => $this->db->affected_rows()]);
    }

    private function _json(array $payload) {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
