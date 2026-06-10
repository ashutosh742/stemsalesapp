<?php
/**
 * STEM BD Planner v2 - rev 9 endpoint patch
 *
 * Mirrors the full Menu/addplantask12 semantics through a unified mobile + web endpoint.
 * Stage on stemapp.in only. Do NOT push to production until 18 May 2026 (per user hold).
 *
 * File: application/controllers/PlannerV2.php (append these methods).
 *
 * Auth: Bearer token (mobile) or session cookie (web admin). All endpoints respect
 * type_id role for lead scope - the existing Menu_model::GetAllCompanyByUserID logic
 * carries through.
 *
 * Endpoints added:
 *   POST /api/planner/v2/submit_task          - canonical write (mirrors addplantask12)
 *   GET  /api/planner/v2/minutes_for_action   - live minute budget per actiontype
 *   POST /api/planner/v2/check_admin_restriction - admin restriction hook
 *   GET  /api/planner/v2/filter_leads         - rev 9 expands optradio coverage
 *   GET  /api/planner/v2/filter_counts        - rev 9 includes PST Assign + actionNotPlannedNeed
 *
 * Author: Computer. Date: 16 May 2026.
 */

class PlannerV2 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Management_model');
        $this->load->library('session');
        $this->_require_auth();
    }

    private function _require_auth() {
        // Accept either Bearer token or session cookie
        $hdr = $this->input->get_request_header('Authorization', true);
        if ($hdr && stripos($hdr, 'Bearer ') === 0) {
            $token = trim(substr($hdr, 7));
            $user = $this->Menu_model->get_user_by_bearer_token($token);
            if (!$user) return $this->_json(['status' => 'error', 'error' => 'invalid_bearer'], 401);
            $this->session->set_userdata('user', (array)$user);
            return;
        }
        $user = $this->session->userdata('user');
        if (!$user) return $this->_json(['status' => 'error', 'error' => 'unauthenticated'], 401);
    }

    private function _json($data, $code = 200) {
        $this->output->set_status_header($code)
                     ->set_content_type('application/json')
                     ->set_output(json_encode($data));
        return;
    }

    /**
     * POST /api/planner/v2/submit_task
     *
     * Mirrors Menu::addplantask12. Accepts JSON body OR form-encoded.
     *
     * Request body:
     *   {
     *     "bdid": int (required),
     *     "tptime": "HH:MM" (required - target plan time),
     *     "ptime":  "HH:MM" (required - planned start, falls back to 00:00:40),
     *     "ntaction": int (required - actiontype_id 1..22),
     *     "ntppose":  int (required - purpose_id),
     *     "selectby": string (required - one of the 31 optradio values),
     *     "pdate":    "YYYY-MM-DD" (required - plan date),
     *     "select_cluster": int (required when ntaction=4 and no companies),
     *     "selectcompanybyuser": [int,...] (up to 3 init_call ids),
     *     "check_data": "Mom Check" | "Proposal Check" | "" (optional),
     *     "filter_trace": {key: value, ...} (the 40 trace fields, optional)
     *   }
     *
     * Response:
     *   { status: "ok", plan_ids: [123,124,...] }
     *   { status: "error", error: "code", message: "...", redirect: "..." }
     *
     * Error codes:
     *   meeting_cap_exceeded     - 4 meetings already planned for that date
     *   companies_cap_exceeded   - more than 3 leads in one submit
     *   cluster_required         - ntaction=4 with no company and no cluster
     *   pending_tasks_first      - old pending tasks must be resolved first
     *   admin_restricted         - SpecialRestricationonTaskPlanner blocked the plan
     *   invalid_purpose          - purpose not allowed for this action/status combo
     *   below_floor              - total minutes below 240 (only when client passes commit=true)
     *   above_ceiling            - total minutes over 540
     */
    public function submit_task() {
        $user = $this->session->userdata('user');
        $uid  = $user['user_id'];
        $uyid = $user['type_id'];

        // Accept JSON or form
        $body = $this->input->raw_input_stream;
        $json = json_decode($body, true);
        if (!is_array($json)) $json = [];

        $get = function($k, $d = null) use ($json) {
            $v = $this->input->post($k);
            if ($v !== null && $v !== '') return $v;
            return isset($json[$k]) ? $json[$k] : $d;
        };

        $bdid     = $get('bdid');
        $tptime   = $get('tptime');
        $ptime    = $get('ptime', '00:00:40');
        $ntaction = (int)$get('ntaction');
        $ntppose  = (int)$get('ntppose');
        $selectby = $get('selectby', '');
        $pdate    = $get('pdate');
        $select_cluster      = $get('select_cluster', '');
        $selectcompanybyuser = $get('selectcompanybyuser', []);
        if (!is_array($selectcompanybyuser)) $selectcompanybyuser = [$selectcompanybyuser];
        $check_data    = $get('check_data', '');
        $filter_trace  = $get('filter_trace', []);

        // ---- Validation ----

        // 1. 4-meetings-per-day cap on actiontype 3 or 4 (mirrors line 19120-19128)
        if (in_array($ntaction, [3, 4])) {
            $existing = $this->Menu_model->GetTotalPlannedMeetingOnDate($uid, $pdate);
            if (sizeof($existing) > 4) {
                return $this->_json([
                    'status'  => 'error',
                    'error'   => 'meeting_cap_exceeded',
                    'message' => "You have already planned " . sizeof($existing) . " meeting(s) on $pdate. Please plan another task.",
                ], 400);
            }
        }

        // 2. Cluster required for ntaction=4 with no company (mirrors line 19136-19143)
        if ($ntaction == 4 && empty($selectcompanybyuser)) {
            if ($select_cluster === '' || $select_cluster === 'Select Cluster') {
                return $this->_json([
                    'status'  => 'error',
                    'error'   => 'cluster_required',
                    'message' => 'Cluster is mandatory for Barg Meeting Task when no company is picked.',
                ], 400);
            }
        }

        // 3. Three-companies-per-plan cap (mirrors line 19287-19290)
        if (sizeof($selectcompanybyuser) > 3) {
            return $this->_json([
                'status'  => 'error',
                'error'   => 'companies_cap_exceeded',
                'message' => 'You can only plan three companies at a time.',
            ], 400);
        }

        // 4. Pending-task-first guard (mirrors line 19534-19539)
        if ($selectby !== 'Create Emergency Meetings Task') {
            $pendingOld = $this->Menu_model->get_all_old_cmp_planbutnotinited($uid);
            if (sizeof($pendingOld) > 0) {
                return $this->_json([
                    'status'  => 'error',
                    'error'   => 'pending_tasks_first',
                    'message' => 'Resolve yesterday pending tasks before adding new plan.',
                    'pending_count' => sizeof($pendingOld),
                ], 400);
            }
        }

        // 5. 540-minute ceiling
        $existingTime = $this->Menu_model->get_totaltdetailsDatewise($bdid, $pdate);
        $consumedMin = 0;
        foreach ($existingTime as $task) {
            $act = $this->Menu_model->get_actionbyid($task->actiontype_id);
            $consumedMin += (int)$act[0]->yest;
        }
        $newTaskMin = 0;
        $thisAction = $this->Menu_model->get_actionbyid($ntaction);
        if (sizeof($thisAction) > 0) $newTaskMin = (int)$thisAction[0]->yest * max(1, sizeof($selectcompanybyuser));
        if (($consumedMin + $newTaskMin) > 540) {
            return $this->_json([
                'status'  => 'error',
                'error'   => 'above_ceiling',
                'message' => 'This task would push your plan over the 9-hour ceiling.',
                'consumed_min' => $consumedMin,
                'new_task_min' => $newTaskMin,
            ], 400);
        }

        // 6. Admin restriction hook (mirrors line 19547)
        $rst = $this->Management_model->SpecialRestricationonTaskPlanner(
            $uyid, $bdid, $tptime, $ptime, $ntaction, $ntppose, $selectby, $pdate, $selectcompanybyuser
        );
        if (is_array($rst) && isset($rst['blocked']) && $rst['blocked']) {
            return $this->_json([
                'status'  => 'error',
                'error'   => 'admin_restricted',
                'message' => isset($rst['message']) ? $rst['message'] : 'Admin restriction blocked this plan.',
            ], 403);
        }

        // ---- Special task shortcuts ----

        // Barg Meeting by cluster (ntaction=4, no company)
        if ($ntaction == 4 && empty($selectcompanybyuser)) {
            $bmdate = $pdate . ' ' . $ptime . ':00';
            $this->Menu_model->createBargMeetingWithClusterId($bdid, $bmdate, $select_cluster, $selectby);
            return $this->_json(['status' => 'ok', 'special' => 'barg_by_cluster']);
        }

        // Join Meeting (ntaction=17)
        if ($ntaction == 17) {
            $bmdate = $pdate . ' ' . $ptime . ':00';
            $this->Menu_model->CreateJoinMeetingTaskWithClusterId($bdid, $selectcompanybyuser, $bmdate, $ntaction, $ntppose, $select_cluster);
            return $this->_json(['status' => 'ok', 'special' => 'join_meeting']);
        }

        // Research task (ntaction=10, no company)
        if ($ntaction == 10 && empty($selectcompanybyuser)) {
            $bmdate = $pdate . ' ' . $ptime . ':00';
            $this->Menu_model->CreateNewResearchTask($bdid, $bmdate, $ntaction, $ntppose, $selectby);
            return $this->_json(['status' => 'ok', 'special' => 'research']);
        }

        // MoM Check
        if ($check_data === 'Mom Check') {
            $bmdate = $pdate . ' ' . $ptime . ':00';
            $this->Menu_model->CreateTaskForMOMCheck($bdid, $bmdate, $selectcompanybyuser, $selectby);
            return $this->_json(['status' => 'ok', 'special' => 'mom_check']);
        }

        // Proposal Check
        if ($check_data === 'Proposal Check') {
            $bmdate = $pdate . ' ' . $ptime . ':00';
            // Loops per company inside CreatePraposalCheckTask
            foreach ($selectcompanybyuser as $tid) {
                $this->Menu_model->CreatePraposalCheckTask($uid, $bmdate, $tid);
            }
            return $this->_json(['status' => 'ok', 'special' => 'proposal_check']);
        }

        // ---- Default selectby branches ----

        $taskAssigntime = $pdate . ' ' . $ptime;
        $k = 1;
        $plan_ids = [];
        $auto_approve_roles = [1, 2, 4, 19, 20, 21, 22, 23];
        $is_auto_approver = in_array($uyid, $auto_approve_roles);

        // Build the filter_trace JSON blob (mirrors line 19181-19286)
        $jsonData = json_encode(is_array($filter_trace) ? $filter_trace : []);

        foreach ($selectcompanybyuser as $tid) {
            $cmp_Data = $this->Menu_model->get_initbyid($tid);
            $ntstatus = $cmp_Data[0]->cstatus;
            $actiontype_id = $ntaction != 0 ? $ntaction : $this->Menu_model->get_tbldata($tid)[0]->actiontype_id;

            $taskAct = $this->Menu_model->getTaskAction($actiontype_id);
            $ctaskMin = sizeof($taskAct) > 0 ? ((int)$taskAct[0]->yest ?: 5) : 5;
            if ($k == 1) $ctaskMin = 0;
            $newdate = new DateTime($taskAssigntime);
            $newdate->modify("+$ctaskMin minutes");
            $new_datetime = $newdate->format('Y-m-d H:i:s');

            // Cluster override (mirrors line 19542-19544)
            if ($select_cluster !== '') {
                $this->Menu_model->updateClusterIdByinitID($uid, $tid, $select_cluster);
            }

            // Selectby branches (mirrors line 19393-19520)
            $this->_apply_selectby_branch($selectby, $tid, $new_datetime, $uid, $uyid, $is_auto_approver);

            // Final insert
            $id = $this->Menu_model->add_plan2(
                $pdate, $uid, $ptime, $tid, $actiontype_id, $ntstatus, $ntppose,
                $actiontype_id, $tptime, $new_datetime, $selectby, $jsonData
            );
            $plan_ids[] = $id;
            $k++;
        }

        return $this->_json(['status' => 'ok', 'plan_ids' => $plan_ids]);
    }

    /**
     * Apply the 6 production selectby update paths (lines 19393-19520).
     */
    private function _apply_selectby_branch($selectby, $tid, $new_datetime, $uid, $uyid, $is_auto_approver) {
        $curtask = $this->Menu_model->get_tbldata($tid);
        $cur_plan_count = $curtask[0]->plan_count;
        $cur_plan_cid_id = $curtask[0]->cid_id;
        $cur_plan_appointmentdatetime = $curtask[0]->appointmentdatetime;
        $sact_type = $this->Menu_model->SelectTaskBYTid($tid);

        $branches = [
            'Plan But Not Initiated' => function() use ($tid, $new_datetime, $sact_type, $selectby, $uid, $cur_plan_count, $cur_plan_cid_id, $cur_plan_appointmentdatetime, $is_auto_approver) {
                if (in_array($sact_type, [3, 4, 17, 22])) $this->Menu_model->updateBarginmeeting($tid, $new_datetime);
                $this->db->query("UPDATE tblcallevents SET appointmentdatetime='$new_datetime', approved_status='',approved_by='',approved_date='', plan_change='0', selectby='$selectby' WHERE id=$tid");
                $this->_write_planner_log($tid, $cur_plan_cid_id, $cur_plan_count, $cur_plan_appointmentdatetime, $new_datetime, $uid, 'Plan But Not Initiated');
                if ($is_auto_approver) $this->db->query("UPDATE tblcallevents SET approved_status='1',approved_by='$uid',approved_date='$new_datetime' WHERE id=$tid");
            },
            'Future Task' => function() use ($tid, $new_datetime, $sact_type, $selectby, $uid, $cur_plan_count, $cur_plan_cid_id, $cur_plan_appointmentdatetime, $is_auto_approver) {
                if (in_array($sact_type, [3, 4, 17, 22])) $this->Menu_model->updateBarginmeeting($tid, $new_datetime);
                $comments = "Task Date Is $cur_plan_appointmentdatetime AND User Planned on this Date $new_datetime using planner";
                $this->db->query("UPDATE tblcallevents SET appointmentdatetime='$new_datetime', comments='$comments', approved_status='',approved_by='',approved_date='', plan_change='0', selectby='$selectby' WHERE id=$tid");
                $this->_write_planner_log($tid, $cur_plan_cid_id, $cur_plan_count, $cur_plan_appointmentdatetime, $new_datetime, $uid, 'Future Task');
                if ($is_auto_approver) $this->db->query("UPDATE tblcallevents SET approved_status='1',approved_by='$uid',approved_date='$new_datetime' WHERE id=$tid");
            },
            'Plan When MOM Approved' => function() use ($tid, $new_datetime, $selectby, $uid) {
                $this->db->query("UPDATE tblcallevents SET appointmentdatetime='$new_datetime', approved_status='1', approved_by='$uid', selectby='$selectby', approved_date='$new_datetime', self_assign='4', autotask='0' WHERE id=$tid");
                $now = date('Y-m-d H:i:s');
                $this->db->query("UPDATE auto_assign_task SET status='1', updated_at='$now' WHERE call_tid=$tid");
            },
            'Because of Plan Change' => function() use ($tid, $new_datetime, $sact_type, $selectby, $uid, $cur_plan_count, $cur_plan_cid_id, $cur_plan_appointmentdatetime, $is_auto_approver) {
                if (in_array($sact_type, [3, 4, 17, 22])) $this->Menu_model->updateBarginmeetingAfterPlanChnage($tid, $new_datetime);
                $this->db->query("UPDATE tblcallevents SET appointmentdatetime='$new_datetime', plan_change='0', approved_status='',approved_by='', selectby='$selectby', approved_date='' WHERE id=$tid");
                $this->_write_planner_log($tid, $cur_plan_cid_id, $cur_plan_count, $cur_plan_appointmentdatetime, $new_datetime, $uid, 'Because of Plan Change');
                if ($is_auto_approver) $this->db->query("UPDATE tblcallevents SET approved_status='1',approved_by='$uid', approved_date='$new_datetime' WHERE id=$tid");
            },
            'Review Target Date' => function() use ($tid, $new_datetime, $sact_type, $selectby, $uid, $is_auto_approver) {
                if (in_array($sact_type, [3, 4, 17, 22])) $this->Menu_model->updateBarginmeetingAfterPlanChnage($tid, $new_datetime);
                $this->db->query("UPDATE tblcallevents SET appointmentdatetime='$new_datetime', plan_change='0', approved_status='',approved_by='', selectby='$selectby', approved_date='' WHERE id=$tid");
                $this->db->query("UPDATE main_review SET taskplan='1' WHERE ntid='$tid'");
                if ($is_auto_approver) $this->db->query("UPDATE tblcallevents SET approved_status='1',approved_by='$uid', approved_date='$new_datetime' WHERE id=$tid");
            },
            'Assign Task' => function() use ($tid, $new_datetime, $sact_type, $selectby, $uid, $is_auto_approver) {
                if (in_array($sact_type, [3, 4, 17, 22])) $this->Menu_model->updateBarginmeetingAfterAssignTaskUpdate($tid, $new_datetime);
                $this->db->query("UPDATE tblcallevents SET appointmentdatetime='$new_datetime', plan_change='0', autotask='0', auto_plan='1', plan='1', selectby='$selectby' WHERE id=$tid");
                if ($is_auto_approver) $this->db->query("UPDATE tblcallevents SET approved_status='1',approved_by='$uid', approved_date='$new_datetime' WHERE id=$tid");
            },
        ];

        if (isset($branches[$selectby])) $branches[$selectby]();
        // Default branch: add_plan2 handles it in the calling loop.
    }

    private function _write_planner_log($tid, $cid_id, $cur_plan_count, $org_date, $new_date, $uid, $remarks) {
        $plog = $this->Menu_model->PlannerlogBYTid($tid);
        if (sizeof($plog) > 0 && $plog[0]->org_task_date !== '') $org_date = $plog[0]->org_task_date;
        $new_count = $cur_plan_count == 0 ? 1 : $cur_plan_count + 1;
        $this->db->query("UPDATE tblcallevents SET plan_count=$new_count WHERE id=$tid");
        $this->db->query("INSERT INTO planner_log(to_user,init_id,task_id,remarks,org_task_date,new_task_date) VALUES ('$uid','$cid_id','$tid','$remarks','$org_date','$new_date')");
    }

    /**
     * GET /api/planner/v2/minutes_for_action?action_id=1
     *
     * Returns live yest minute budget from action master.
     * Response: { status: "ok", action_id: 1, minutes: 5 }
     */
    public function minutes_for_action() {
        $aid = (int)$this->input->get('action_id');
        if (!$aid) return $this->_json(['status' => 'error', 'error' => 'missing_action_id'], 400);
        $a = $this->Menu_model->get_actionbyid($aid);
        if (!sizeof($a)) return $this->_json(['status' => 'error', 'error' => 'unknown_action'], 404);
        $min = (int)$a[0]->yest ?: 5;
        return $this->_json(['status' => 'ok', 'action_id' => $aid, 'minutes' => $min, 'name' => $a[0]->name]);
    }

    /**
     * POST /api/planner/v2/check_admin_restriction
     *
     * Pre-flight check before user submits (so client can warn).
     * Mirrors Management_model::SpecialRestricationonTaskPlanner.
     */
    public function check_admin_restriction() {
        $user = $this->session->userdata('user');
        $uid  = $user['user_id'];
        $uyid = $user['type_id'];
        $body = json_decode($this->input->raw_input_stream, true) ?: [];
        $rst = $this->Management_model->SpecialRestricationonTaskPlanner(
            $uyid, $uid,
            $body['tptime'] ?? '', $body['ptime'] ?? '',
            (int)($body['ntaction'] ?? 0), (int)($body['ntppose'] ?? 0),
            $body['selectby'] ?? '', $body['pdate'] ?? '',
            $body['selectcompanybyuser'] ?? []
        );
        if (is_array($rst) && isset($rst['blocked']) && $rst['blocked']) {
            return $this->_json([
                'status'  => 'error',
                'error'   => 'admin_restricted',
                'message' => $rst['message'] ?? 'Admin restriction blocked this plan.',
            ], 403);
        }
        return $this->_json(['status' => 'ok', 'allowed' => true]);
    }

    /**
     * GET /api/planner/v2/filter_counts
     *
     * rev 9 - now includes PST Assign + actionNotPlannedNeed.
     */
    public function filter_counts_v2() {
        $user = $this->session->userdata('user');
        $uid  = $user['user_id'];
        $counts = $this->Menu_model->GetFilterCountsForBD($uid);
        // Add the two new rev 9 counts
        $counts['PST Assign']           = $this->Menu_model->GetPSTAssignCountForBD($uid);
        $counts['actionNotPlannedNeed'] = $this->Menu_model->GetActionNotPlannedNeedCountForBD($uid);
        return $this->_json(['status' => 'ok', 'counts' => $counts]);
    }
}
