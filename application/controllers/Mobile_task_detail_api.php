<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mobile_task_detail_api
 * ----------------------------------------------------------------------------
 * ADDITIVE controller created 2026-06-07 to replace two stub routes:
 *   GET  /api/task/detail     -> detail()      (was Mobile_stub_api/handle)
 *   POST /api/task/save_draft -> save_draft()  (was Mobile_stub_api/handle)
 *
 * STRICT additive. Does NOT touch production. Reads/writes only on staging.
 * Mirrors production addpop.php modal context + draft autosave.
 *
 * detail() surfaces the SAME context the production addpop modal loads:
 *   company name, contact, lead cstatus, actiontype label, purpose, the
 *   30-minute meeting startm reference, RP/MOM meeting hints, and proposal
 *   branch flags - so the mobile screen renders identical fields.
 *
 * save_draft() persists an in-progress execution into tblcallevents.draft
 * (a mediumtext column that already exists) without completing the task,
 * exactly like the web modal's local draft behaviour but server-side.
 * It NEVER sets actontaken/complete_time - only submit does that. So the
 * 30-min rule, MOM analysis and proposal logic at submit-time are untouched.
 *
 * ASCII only. "Rs" for rupees. No em/en-dashes.
 */
class Mobile_task_detail_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
        $this->load->library('BearerAuth');
    }

    /** rimlyproof_taskscope_20260609: authed identity captured by _bearer_ok() */
    private $auth_uid  = 0;
    private $auth_role = '';

    private function _bearer_ok() {
        $auth = $this->bearerauth->resolve();
        if (empty($auth['ok'])) return false;
        $this->auth_uid  = isset($auth['uid'])  ? (int)$auth['uid']                 : 0;
        $this->auth_role = isset($auth['role']) ? strtolower((string)$auth['role']) : '';
        return true;
    }

    /**
     * rimlyproof_taskscope_20260609: may the caller act on a task owned by
     * $task_user_id? Field users (BD/ACM) only on their OWN tasks; master/
     * system/superadmin/admin and managers (cm/rm/sc/pst/ea) -> yes.
     */
    private function _task_owner_ok($task_user_id) {
        $task_user_id = (int)$task_user_id;
        if ($this->auth_uid <= 0) return true; // master/system digest
        if (in_array($this->auth_role, array('system','superadmin','admin'), true)) return true;
        if ($this->auth_role === 'bd' || $this->auth_role === 'acm') {
            return ($this->auth_uid === $task_user_id);
        }
        return true; // managers / other roles: team visibility
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function _post($k, $def = null) {
        $v = $this->input->post($k);
        if ($v === null || $v === false) {
            $raw = json_decode((string)file_get_contents('php://input'), true);
            if (is_array($raw) && array_key_exists($k, $raw)) return $raw[$k];
            return $def;
        }
        return $v;
    }

    // -------------------------------------------------------------------------
    // GET /api/task/detail?tid=
    // -------------------------------------------------------------------------
    public function detail() {
        if (!$this->_bearer_ok()) return $this->_json(array('ok' => false, 'error' => 'bad token'), 401);

        $tid = (int)$this->input->get('tid');
        if ($tid <= 0) $tid = (int)$this->_post('tid', 0);
        if ($tid <= 0) return $this->_json(array('ok' => false, 'error' => 'tid required'), 400);

        $sql = "SELECT ce.id, ce.user_id, ce.cid_id, ce.actiontype_id, ce.purpose_id,
                       ce.status_id, ce.actontaken, ce.autotask, ce.mom, ce.mom_received,
                       ce.remarks, ce.draft, ce.appointmentdatetime, ce.initiateddt,
                       ce.date, ce.updateddate,
                       ic.cmpid_id, ic.cstatus, ic.dm_contact_name, ic.dm_contact_designation,
                       ic.dm_contact_phone, ic.dm_contact_email,
                       cm.compname,
                       a.name AS action_name,
                       p.name AS purpose_name
                  FROM tblcallevents ce
                  LEFT JOIN init_call ic     ON ic.id     = ce.cid_id
                  LEFT JOIN company_master cm ON cm.id     = ic.cmpid_id
                  LEFT JOIN action a          ON a.id      = ce.actiontype_id
                  LEFT JOIN purpose p         ON p.id      = ce.purpose_id
                 WHERE ce.id = ?
                 LIMIT 1";
        $row = $this->db->query($sql, array($tid))->row_array();

        if (!$row) {
            return $this->_json(array('ok' => false, 'empty' => true, 'error' => 'unknown tid', 'tid' => $tid), 404);
        }

        // rimlyproof_taskscope_20260609: a field user may only open their OWN task.
        if (!$this->_task_owner_ok($row['user_id'])) {
            return $this->_json(array('ok' => false, 'error' => 'forbidden', 'note' => 'task_not_in_your_scope'), 403);
        }

        $aid       = (int)$row['actiontype_id'];
        $isMeeting = ($aid === 3 || $aid === 4);   // Scheduled / Barge-in meeting
        $isMOM     = ($aid === 6);                 // Write MOM
        $isEmail   = ($aid === 2);
        $isWhatsapp= ($aid === 5);
        $isResearch= ($aid === 10);
        $isProposal= ($aid === 7);
        $isCall    = ($aid === 1);

        // 30-minute meeting reference (mirrors addpop hidden field "startm").
        // Production starts the clock at meeting-start; we expose initiateddt
        // (or date) so the app can compute elapsed minutes the same way.
        $startm = $row['initiateddt'] ?: $row['date'];
        $elapsed_min = null;
        if ($startm) {
            $elapsed_min = (int)floor((time() - strtotime($startm)) / 60);
        }

        // Decode any saved draft so the screen can rehydrate fields.
        $draft = null;
        if (!empty($row['draft'])) {
            $d = json_decode($row['draft'], true);
            if (is_array($d)) $draft = $d;
        }

        $data = array(
            'tid'                  => (int)$row['id'],
            'user_id'              => (int)$row['user_id'],
            'cid_id'               => (int)$row['cid_id'],
            'cmpid'                => (int)$row['cmpid_id'],
            'cname'                => $row['compname'] ?: '',
            'ctname'               => $row['dm_contact_name'] ?: '',
            'contact_designation'  => $row['dm_contact_designation'] ?: '',
            'contact_phone'        => $row['dm_contact_phone'] ?: '',
            'contact_email'        => $row['dm_contact_email'] ?: '',
            'cstatus'              => $row['cstatus'] !== null ? (int)$row['cstatus'] : null,
            'ystatus'              => $row['status_id'] !== null ? (int)$row['status_id'] : null,
            'actiontype_id'        => $aid,
            'action_name'          => $row['action_name'] ?: '',
            'purpose_id'           => (int)$row['purpose_id'],
            'purpose_name'         => $row['purpose_name'] ?: '',
            'actontaken'           => $row['actontaken'],
            'autotask'             => (int)$row['autotask'],
            'existing_mom'         => $row['mom'] ?: '',
            'mom_received'         => $row['mom_received'],
            'existing_remarks'     => $row['remarks'] ?: '',
            'appointmentdatetime'  => $row['appointmentdatetime'],

            // ---- 30-minute meeting rule context (parity with addpop) ----
            'startm'               => $startm,
            'elapsed_minutes'      => $elapsed_min,
            'meeting_overrun'      => ($elapsed_min !== null && $elapsed_min > 30),
            'meeting_overrun_threshold_min' => 30,

            // ---- branch flags so the app shows the right fields ----
            'flags' => array(
                'is_meeting_or_rp' => $isMeeting,
                'is_mom'           => $isMOM,
                'is_email'         => $isEmail,
                'is_whatsapp'      => $isWhatsapp,
                'is_research'      => $isResearch,
                'is_proposal'      => $isProposal,
                'is_call'          => $isCall,
                'show_structured_mom' => ($isMOM || $isMeeting),
            ),

            // ---- RP / MOM meeting branch options (mirrors RPMorN select) ----
            'rp_options' => $isMeeting ? array(
                array('value' => 'RP',             'label' => 'RP'),
                array('value' => 'NO RP',          'label' => 'No RP Meeting'),
                array('value' => 'Only Got Detail','label' => 'No RP But Got Details'),
                array('value' => 'Change RP',      'label' => 'Change RP'),
            ) : array(),

            // ---- proposal status branch (mirrors meetingProposalStatus) ----
            'proposal_status_options' => $isMeeting ? array(
                'Proposal Required',
                'Proposal Not Required',
                'Clarity Meeting For Proposal Sent',
            ) : array(),

            'draft'                => $draft,
        );

        // Return task fields at the TOP LEVEL (the app reads res.data.actiontype_id,
        // res.data.cname, res.data.ystatus, etc. directly). Also keep a nested
        // 'data' copy for any caller that expects an envelope. Both shapes work.
        $envelope = array(
            'ok'           => true,
            'stub'         => false,
            'status'       => 200,
            'route'        => 'api/task/detail',
            'generated_at' => date('c'),
            'data'         => $data,
        );
        return $this->_json(array_merge($data, $envelope));
    }

    // -------------------------------------------------------------------------
    // POST /api/task/save_draft   { tid, ...any execution fields }
    // Persists the in-progress payload into tblcallevents.draft as JSON.
    // Does NOT complete the task. Safe, reversible, additive.
    // -------------------------------------------------------------------------
    public function save_draft() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->_json(array('ok' => false, 'error' => 'POST only'), 405);
        if (!$this->_bearer_ok()) return $this->_json(array('ok' => false, 'error' => 'bad token'), 401);

        $raw  = json_decode((string)file_get_contents('php://input'), true);
        if (!is_array($raw)) $raw = array();
        $tid  = (int)(isset($raw['tid']) ? $raw['tid'] : $this->input->post('tid'));
        if ($tid <= 0) return $this->_json(array('ok' => false, 'error' => 'tid required'), 400);

        // confirm the task exists before writing
        $exists = $this->db->query("SELECT id, user_id FROM tblcallevents WHERE id = ? LIMIT 1", array($tid))->row_array();
        if (!$exists) return $this->_json(array('ok' => false, 'error' => 'unknown tid', 'tid' => $tid), 404);
        // rimlyproof_taskscope_20260609: a field user may only save their OWN task draft.
        if (!$this->_task_owner_ok($exists['user_id'])) {
            return $this->_json(array('ok' => false, 'error' => 'forbidden', 'note' => 'task_not_in_your_scope'), 403);
        }

        // strip nothing dangerous; just store the field map (minus tid) as JSON
        $payload = $raw;
        unset($payload['tid']);
        $payload['_saved_at'] = date('Y-m-d H:i:s');
        $json = json_encode($payload);

        $this->db->where('id', $tid)->update('tblcallevents', array('draft' => $json));
        $aff = $this->db->affected_rows();

        return $this->_json(array(
            'ok'        => true,
            'stub'      => false,
            'status'    => 200,
            'tid'       => $tid,
            'affected'  => $aff,
            'saved_at'  => $payload['_saved_at'],
            'route'     => 'api/task/save_draft',
        ));
    }
}
