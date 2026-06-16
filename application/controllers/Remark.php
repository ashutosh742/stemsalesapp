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
        // sweep_fix_20260616 (H3): the two task-request tables have different
        // columns (tblsctaskrequest has cid_id/stid/complete_datetime/attachment;
        // sc_task_request does not). Select only columns that exist so the lookup
        // never errors on either schema.
        $cols = $this->db->list_fields($table);
        $has_cid     = in_array('cid_id', $cols, true);
        $has_user    = in_array('user_id', $cols, true);
        $sel = 'id, status' . ($has_cid ? ', cid_id' : '') . ($has_user ? ', user_id' : '');
        $task = $this->db->query("SELECT {$sel} FROM {$table} WHERE id = ? LIMIT 1", array($taskid))->row_array();

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

        // sweep_fix_20260616 (H3): the legacy delay-remarks block referenced
        // tblcallevents.task_id/event_id columns that do not exist on every schema
        // (staging tblcallevents has neither), which would error. Only run it when
        // those columns are present; the additive cid/user mirror below covers the
        // delay remark otherwise.
        if ($delay_remarks !== '' && $this->db->table_exists('tblcallevents')) {
            $ce_cols0 = $this->db->list_fields('tblcallevents');
            if (in_array('task_id', $ce_cols0, true) && in_array('event_id', $ce_cols0, true)
                && in_array('late_remarks_message', $ce_cols0, true)) {
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
        }

        $updatedatetime = date('Y-m-d H:i:s');
        // sweep_fix_20260616 (H3): build the UPDATE from columns that actually
        // exist on this schema (sc_task_request lacks complete_datetime/stid/
        // attachment), so the write never silently fails on a missing column.
        $set = array('status' => 1, 'remarks' => $remarks);
        if (in_array('complete_datetime', $cols, true)) { $set['complete_datetime'] = $updatedatetime; }
        if (in_array('stid', $cols, true))              { $set['stid'] = $stid; }
        if (in_array('attachment', $cols, true))        { $set['attachment'] = $attachment; }
        if (in_array('updated_at', $cols, true))        { $set['updated_at'] = $updatedatetime; }
        $this->db->where('id', $taskid)->update($table, $set);

        // sweep_fix_20260616 (H3 root cause): Remark::list reads remarks from
        // tblcallevents (by cid_id/user_id), but add() only wrote the task-request
        // table - so a submitted remark was never read back. Additively mirror the
        // remark into tblcallevents (the table the list screen reads) for the same
        // cid/user. We UPDATE the matching event row if one exists; otherwise we
        // INSERT a lightweight remark-carrier row so it is visible. Legacy write
        // above is preserved.
        $cid = $has_cid ? (int)(isset($task['cid_id']) ? $task['cid_id'] : 0) : 0;
        $tuser = $has_user ? (int)(isset($task['user_id']) ? $task['user_id'] : 0) : 0;
        if (($cid > 0 || $tuser > 0) && $this->db->table_exists('tblcallevents')) {
            $ce_cols = $this->db->list_fields('tblcallevents');
            if (in_array('remarks', $ce_cols, true)) {
                $this->db->from('tblcallevents');
                if ($cid > 0)   { $this->db->where('cid_id', $cid); }
                if ($tuser > 0) { $this->db->where('user_id', $tuser); }
                $existing = $this->db->order_by('id', 'DESC')->limit(1)->get()->row_array();
                $ce_set = array('remarks' => $remarks);
                if ($delay_remarks !== '' && in_array('late_remarks_message', $ce_cols, true)) {
                    $ce_set['late_remarks_message'] = $delay_remarks;
                }
                if ($existing) {
                    $this->db->where('id', (int)$existing['id'])->update('tblcallevents', $ce_set);
                } else {
                    $ins = array('remarks' => $remarks);
                    if (in_array('cid_id', $ce_cols, true) && $cid > 0)   { $ins['cid_id'] = $cid; }
                    if (in_array('user_id', $ce_cols, true) && $tuser > 0) { $ins['user_id'] = $tuser; }
                    if ($delay_remarks !== '' && in_array('late_remarks_message', $ce_cols, true)) {
                        $ins['late_remarks_message'] = $delay_remarks;
                    }
                    $this->db->insert('tblcallevents', $ins);
                }
            }
        }

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

    // GET /api/remark/list?cid_id=<N>&uid=<N> -- added 28 May 2026
    public function list() {
        if (!$this->_auth_ok) {
            $this->_fail(401, 'unauthorized');
            return;
        }
        $cid_id  = (int)$this->input->get('cid_id');
        $uid     = (int)$this->input->get('uid');
        $date    = $this->input->get('date') ?: null;
        $limit   = max(1, min(500, (int)($this->input->get('limit') ?: 100)));

        $where  = '1=1';
        $params = array();

        if ($cid_id > 0) {
            $where .= ' AND t.cid_id = ?';
            $params[] = $cid_id;
        }
        if ($uid > 0) {
            $where .= ' AND t.user_id = ?';
            $params[] = $uid;
        }
        if ($date) {
            $where .= ' AND DATE(t.appointmentdatetime) = ?';
            $params[] = $date;
        }

        // Remarks live in tblcallevents (remarks column) and optionally tblsctaskrequest
        $rows = $this->db->query(
            "SELECT t.id AS event_id, t.cid_id, t.user_id, t.remarks,
                    t.late_remarks_message, t.appointmentdatetime AS event_time,
                    t.actiontype_id, t.status_id
             FROM tblcallevents t
             WHERE $where
             ORDER BY t.appointmentdatetime DESC
             LIMIT $limit",
            $params
        )->result_array();

        $this->_json(array(
            'ok'    => true,
            'cid_id' => $cid_id ?: null,
            'uid'   => $uid ?: null,
            'count' => count($rows),
            'rows'  => $rows,
        ));
    }


}
