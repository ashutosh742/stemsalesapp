<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/RestApiBaseController.php';

class Remark extends RestApiBaseController {

    const UPLOAD_PATH = 'uploads/SCDailyActivity/';

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'date', 'file'));
    }

    public function probe() {
        $this->_json(array(
            'ok'       => true,
            'service'  => 'remark',
            'deployed' => true,
            'ts'       => date('Y-m-d H:i:s'),
            'auth_ok'  => $this->_auth_ok,
        ));
    }

    public function add() {
        $taskid        = isset($_POST['taskid'])        ? (int)$_POST['taskid']        : 0;
        $stid          = isset($_POST['stid'])          ? (int)$_POST['stid']          : 1;
        $remarks       = isset($_POST['remarks'])       ? trim($_POST['remarks'])       : '';
        $delay_remarks = isset($_POST['delay_remarks']) ? trim($_POST['delay_remarks']) : '';

        if ($taskid === 0) {
            $raw = file_get_contents('php://input');
            $body = @json_decode($raw, true);
            if ($body) {
                $taskid        = isset($body['taskid'])        ? (int)$body['taskid']        : 0;
                $stid          = isset($body['stid'])          ? (int)$body['stid']          : 1;
                $remarks       = isset($body['remarks'])       ? trim($body['remarks'])       : '';
                $delay_remarks = isset($body['delay_remarks']) ? trim($body['delay_remarks']) : '';
            }
        }

        if ($taskid <= 0) { return $this->_fail(400, 'taskid required'); }
        if ($remarks === '') { return $this->_fail(400, 'remarks cannot be empty'); }

        // Detect table name
        $table = $this->db->table_exists('tblsctaskrequest') ? 'tblsctaskrequest' : 'sc_task_request';
        $task = $this->db->query("SELECT id, status, cid_id FROM {$table} WHERE id = ? LIMIT 1", array($taskid))->row_array();

        if (!$task) { return $this->_fail(404, 'task_not_found'); }
        if ((int)$task['status'] !== 0) { return $this->_fail(409, 'task_already_completed'); }

        $attachment = 'NULL';
        if (isset($_FILES['filname']) && !empty($_FILES['filname']['name'])) {
            $upload_path = FCPATH . self::UPLOAD_PATH;
            if (!is_dir($upload_path)) { mkdir($upload_path, 0755, true); }
            $uploaded_files = array();
            $file_count = is_array($_FILES['filname']['name']) ? count($_FILES['filname']['name']) : 1;
            if ($file_count > 1 && is_array($_FILES['filname']['name'])) {
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES['filname']['error'][$i] !== UPLOAD_ERR_OK) continue;
                    $safe_name = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['filname']['name'][$i]));
                    if (move_uploaded_file($_FILES['filname']['tmp_name'][$i], $upload_path . $safe_name)) {
                        $uploaded_files[] = self::UPLOAD_PATH . $safe_name;
                    }
                }
            } else {
                if ($_FILES['filname']['error'] === UPLOAD_ERR_OK) {
                    $safe_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['filname']['name']));
                    if (move_uploaded_file($_FILES['filname']['tmp_name'], $upload_path . $safe_name)) {
                        $uploaded_files[] = self::UPLOAD_PATH . $safe_name;
                    }
                }
            }
            if (!empty($uploaded_files)) { $attachment = json_encode($uploaded_files); }
        }

        if ($delay_remarks !== '') {
            $callevent = $this->db->query(
                "SELECT event_id FROM tblcallevents WHERE task_id = ? LIMIT 1", array($taskid)
            )->row_array();
            if ($callevent && isset($callevent['event_id'])) {
                $this->db->query(
                    "UPDATE tblcallevents SET late_remarks_message = ? WHERE event_id = ?",
                    array($delay_remarks, $callevent['event_id'])
                );
            }
        }

        $updatedatetime = date('Y-m-d H:i:s');
        $this->db->query(
            "UPDATE {$table} SET status = 1, complete_datetime = ?, remarks = ?, stid = ?, attachment = ? WHERE id = ?",
            array($updatedatetime, $remarks, $stid, $attachment, $taskid)
        );

        $this->_json(array('ok' => true, 'taskid' => $taskid, 'remarks' => $remarks, 'stid' => $stid,
            'attachment' => $attachment !== 'NULL' ? json_decode($attachment) : null,
            'late_remark' => $delay_remarks !== ''));
    }

    public function list_for_event($event_id = 0) {
        $event_id = (int)$event_id;
        if ($event_id <= 0) { return $this->_fail(400, 'event_id required in URL segment'); }

        $rows = $this->db->query(
            "SELECT ce.event_id, ce.remarks, ce.late_remarks_message, ce.stid, ce.complete_datetime, ce.attachment
             FROM tblcallevents ce WHERE ce.event_id = ? LIMIT 1", array($event_id)
        )->result_array();

        $this->_json(array('ok' => true, 'event_id' => $event_id, 'rows' => $rows));
    }
}
