<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Notifications inbox + alerts.
 * Real schema:  notification(id, msg, user, company_id, date, status)
 * status: 'pending' or 'read' (or other).
 */
class NotificationsV28 extends CI_Controller {

    public function probe() {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'controller' => 'NotificationsV28']));
    }

    private function _auth_or_die() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return false;
        }
        return true;
    }

    public function inbox() {
        $this->output->set_content_type('application/json');
        if (!$this->_auth_or_die()) return;
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        if ($bd_uid <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'bd_uid is required']);
            return;
        }
        $sql = "SELECT n.id, n.msg, n.user, n.company_id, n.date, n.status,
                       cm.compname AS company_name
                FROM notification n
                LEFT JOIN company_master cm ON cm.id = n.company_id
                WHERE n.user = ?
                ORDER BY n.date DESC LIMIT 200";
        $q = $this->db->query($sql, [(string)$bd_uid]);
        echo json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    public function alerts() {
        $this->output->set_content_type('application/json');
        if (!$this->_auth_or_die()) return;
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        if ($bd_uid <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'bd_uid is required']);
            return;
        }
        $sql = "SELECT n.id, n.msg, n.date, n.status,
                       cm.compname AS company_name
                FROM notification n
                LEFT JOIN company_master cm ON cm.id = n.company_id
                WHERE n.user = ? AND n.status = 'pending'
                ORDER BY n.date DESC LIMIT 100";
        $q = $this->db->query($sql, [(string)$bd_uid]);
        echo json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    public function mark_read() {
        $this->output->set_content_type('application/json');
        if (!$this->_auth_or_die()) return;
        $ids = $this->input->post('ids', TRUE);
        if (empty($ids)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'ids required']);
            return;
        }
        $arr = array_filter(array_map('intval', explode(',', $ids)));
        if (!$arr) { echo json_encode(['ok' => true, 'marked' => 0]); return; }
        $in  = implode(',', $arr);
        $this->db->query("UPDATE notification SET status = 'read' WHERE id IN ({$in})");
        echo json_encode(['ok' => true, 'marked' => count($arr)]);
    }
}
