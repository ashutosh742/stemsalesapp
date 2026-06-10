<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Task comments (special remarks + thanks comment).
 * Read/write goes against tblcallevents columns:
 *   special_remarks, comments, comment_by, thnkscomments.
 */
class TaskCommentsV28 extends CI_Controller {

    public function probe() {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'controller' => 'TaskCommentsV28']));
    }

    private function _auth() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'message' => 'Unauthorized']));
            return false;
        }
        return true;
    }

    public function add_special_comment() {
        $this->output->set_content_type('application/json');
        if (!$this->_auth()) return;
        $task_id = (int) $this->input->post('task_id', TRUE);
        $by_uid  = (int) $this->input->post('by_uid',  TRUE);
        $remark  = trim((string) $this->input->post('remark', TRUE));
        if ($task_id <= 0 || $remark === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'task_id and remark are required']);
            return;
        }
        $this->db->query(
            "UPDATE tblcallevents SET special_remarks = ?, comment_by = ? WHERE id = ?",
            [$remark, $by_uid, $task_id]
        );
        echo json_encode(['ok' => true, 'task_id' => $task_id]);
    }

    public function add_thanks_comment() {
        $this->output->set_content_type('application/json');
        if (!$this->_auth()) return;
        $task_id = (int) $this->input->post('task_id', TRUE);
        $remark  = trim((string) $this->input->post('remark', TRUE));
        if ($task_id <= 0 || $remark === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'task_id and remark are required']);
            return;
        }
        $this->db->query(
            "UPDATE tblcallevents SET thnkscomments = ? WHERE id = ?",
            [$remark, $task_id]
        );
        echo json_encode(['ok' => true, 'task_id' => $task_id]);
    }

    public function list_for_task($task_id = 0) {
        $this->output->set_content_type('application/json');
        if (!$this->_auth()) return;
        $task_id = (int) $task_id;
        if ($task_id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'task_id required']);
            return;
        }
        $q = $this->db->query(
            "SELECT id, special_remarks, comments, comment_by, thnkscomments, updated_at
             FROM tblcallevents WHERE id = ? LIMIT 1",
            [$task_id]
        );
        $row = $q ? $q->row_array() : null;
        echo json_encode(['ok' => true, 'comments' => $row ?: new stdClass()]);
    }
}
