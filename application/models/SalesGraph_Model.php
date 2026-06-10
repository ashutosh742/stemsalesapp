<?php
date_default_timezone_set("Asia/Calcutta");
class SalesGraph_Model extends Menu_model{



    public function GetAllCompulsiveAndNeedYourAttentioninGraph($uid,$tdate,$cdate = 'NULL'){
        $udetail = $this->Menu_model->get_userbyid($uid);
           $uyid = $udetail[0]->type_id;
        if($uyid == 3){
            $text = " u1.user_id = '$uid'";
        }else if($uyid == 13){
            $text = " u1.aadmin = '$uid' || u1.user_id = '$uid'";
        }else if($uyid == 4){
            $text = " u1.pst_co = '$uid' || u1.user_id = '$uid'";
        }else if($uyid == 15){
            $text = " u1.sales_co = '$uid'";
        }else{
            $text = " u1.admin_id = '$uid'";
        }
    
        if($cdate == 'NULL'){
            $cdate = $tdate;
         }


    $query=$this->db->query("SELECT
    clt.*,
    company_master.compname,
    partner_master.name AS pname,
    company_master.id AS cid,
    tbc1.actiontype_id AS task_type,
    action.name AS last_task_name,
    u2.name AS last_task_byuser,
    tbc1.remarks AS last_task_remarks,
    u1.name AS tag_username,
    init_call.mainbd,
    u3.name AS mainbd_name,
    init_call.cstatus,
    init_call.cstatus as status,
    status.name as current_status,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.topspender,
    (
    SELECT
        COUNT(*)
    FROM
        compulsive_log_test clt_sub
    WHERE
        clt_sub.init_id = clt.init_id AND DATE(clt_sub.create_date) BETWEEN DATE_SUB('$tdate', INTERVAL 2 DAY) AND '$tdate'
) AS init_id_count
FROM
    `compulsive_log_test` clt
LEFT JOIN tblcallevents tbc1 ON
    tbc1.id = clt.last_task_id
LEFT JOIN action ON action.id = tbc1.actiontype_id
LEFT JOIN user_details u2 ON u2.user_id = tbc1.user_id
LEFT JOIN user_details u1 ON u1.user_id = clt.user_id
LEFT JOIN init_call ON init_call.id = clt.init_id
LEFT JOIN user_details u3 ON u3.user_id = init_call.mainbd
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status ON status.id = init_call.cstatus
WHERE
    $text AND DATE(clt.create_date) = '$tdate' AND NOT EXISTS(
    SELECT
        1
    FROM
        tblcallevents tbc2
    WHERE
        tbc2.cid_id = clt.init_id AND DATE(tbc2.fwd_date) BETWEEN DATE_SUB('$cdate', INTERVAL 8 DAY) AND '$cdate'
)
GROUP BY
    clt.init_id
HAVING
    init_id_count >= 2
ORDER BY
    clt.id
DESC");
        return $query->result();
    }






    public function GetDailyTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate){

        $udata = $this->Menu_model->get_userbyid($uid);
        $utype = $udata[0]->type_id;
        if($utype == 3){
            $text = "AND u1.user_id  = '$uid'";
        }elseif($utype == 13){
            $text = "AND u1.aadmin  = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co  = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co  = '$uid'";
        }elseif($utype == 2){
            $text = "AND u1.admin_id  = '$uid'";
        }else{
            $text = '';
        }
    
        $query=$this->db->query("SELECT
    u1.name,
    u1.user_id,
    ut.name AS type_name,
    userworkfrom.TYPE AS userworkfrom,
    COUNT(tblcallevents.id) AS total_task,
    COUNT(
        CASE WHEN(
            tblcallevents.approved_status = 1 AND tblcallevents.autotask = 0
        ) THEN tblcallevents.id
    END
) AS approved_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = '' AND tblcallevents.autotask = 0
    ) THEN tblcallevents.id
END
) AS pending_approved,
COUNT(
    CASE WHEN tblcallevents.nextCFID != 0 THEN tblcallevents.id
END
) AS complete_task,
COUNT(
    CASE WHEN tblcallevents.nextCFID = 0 THEN tblcallevents.id
END
) AS pending_task,
COUNT(
    CASE WHEN(tblcallevents.autotask = 1) THEN tblcallevents.id
END
) AS total_autotask,
COUNT(
    CASE WHEN(tblcallevents.aftertask != 0) THEN tblcallevents.id
END
) AS after_task,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND tblcallevents.autotask = 1
    ) THEN tblcallevents.id
END
) AS complete_autotask,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID = 0 AND tblcallevents.autotask = 1
    ) THEN tblcallevents.id
END
) AS pending_autotask,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != ''
    ) THEN tblcallevents.id
END
) AS total_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = ''
    ) THEN tblcallevents.id
END
) AS pending_for_assign_after_reject_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1'
    ) THEN tblcallevents.id
END
) AS admin_create_request_for_self_assign,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_admin_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_user_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_yes,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_no,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_no_purpose_no,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 1 THEN actiontype_id
END
) call_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 THEN actiontype_id
END
) email_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 THEN actiontype_id
END
) scheduled_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 THEN actiontype_id
END
) barg_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 THEN actiontype_id
END
) whatsapp_activity,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 THEN actiontype_id
END
) write_mom,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 THEN actiontype_id
END
) write_proposal,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 THEN actiontype_id
END
) research_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 THEN actiontype_id
END
) documentation_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 THEN actiontype_id
END
) social_networking_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 THEN actiontype_id
END
) social_activity_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 15 THEN actiontype_id
END
) annual_review_target_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 THEN actiontype_id
END
) join_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 18 THEN actiontype_id
END
) mom_check_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 THEN actiontype_id
END
) create_bd_request_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 20 THEN actiontype_id
END
) submit_handover_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 21 THEN actiontype_id
END
) praposal_check_task,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 THEN 1
END
) * 10
) AS total_task_time,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 AND tblcallevents.autotask = 0 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 AND tblcallevents.autotask = 0 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 AND tblcallevents.autotask = 0 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 AND tblcallevents.autotask = 0 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 AND tblcallevents.autotask = 0 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 AND tblcallevents.autotask = 0 THEN 1
END
) * 10
) AS total_plan_time_on_planner,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 AND tblcallevents.autotask = 1 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 AND tblcallevents.autotask = 1 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 AND tblcallevents.autotask = 1 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 AND tblcallevents.autotask = 1 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 AND tblcallevents.autotask = 1 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 AND tblcallevents.autotask = 1 THEN 1
END
) * 10
) AS total_plan_time_with_autotask,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `task_plan_for_today`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS task_plan_for_today_request,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `special_request_for_leave`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS user_create_special_request_for_leave,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `task_plan_for_today`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS task_plan_for_today_request,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `reminder`
    WHERE
        user_id = u1.user_id AND DATE(created_at) = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS user_request_any_reminder,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `create_planner_request`
    WHERE
        request_user_id = u1.user_id AND DATE(created_at) = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS pending_task_planner_request,
(
    SELECT
        (
        SELECT
            COUNT(*)
        FROM
            `session_plan_time`
        WHERE
            user_id = u1.user_id AND DATE(created_at) = '$tdate'
    )
) AS session_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON
    u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON
    ut.id = u1.type_id
LEFT JOIN user_day ud ON
    ud.user_id = u1.user_id
LEFT JOIN userworkfrom ON userworkfrom.id = ud.wffo
WHERE
    CAST(
        tblcallevents.appointmentdatetime AS DATE
    ) = '$tdate' AND CAST(ud.ustart AS DATE) = '$tdate' $text
GROUP BY
    u1.user_id");
        return $query->result();
    }





    public function GetTotalTaskDataByUserIdDaily($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "u1.user_id = '$uid'";
        }else{
            $text = "u1.admin_id = '$uid'";
        }

        $query=$this->db->query("SELECT
    e.id,
    u1.name as task_user,
    cm.compname as company_name,
    e.nextCFID AS next_cfid,
    e.actontaken,
    e.purpose_achieved,
    e.appointmentdatetime AS task_date,
    e.initiateddt AS initiate_time,
    e.updateddate AS updated_time,
    s.name AS status_name,
    a.name AS task_name,
    ic.cstatus AS cstatus,
    e.autotask AS is_autotask,
    s1.name AS task_time_status_id,
    s2.name AS task_new_status_id,
    s3.name AS compay_current_satus,
    e.approved_status AS task_approved_status,
    user_type.name as user_types_name
FROM tblcallevents e
LEFT JOIN action a ON a.id = e.actiontype_id
LEFT JOIN init_call ic ON ic.id = e.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN status s ON s.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = e.status_id
LEFT JOIN status s2 ON s2.id = e.nstatus_id
LEFT JOIN status s3 ON s3.id = ic.cstatus
LEFT JOIN user_details u1 ON u1.user_id = e.user_id 
LEFT JOIN user_type ON user_type.id = u1.type_id
WHERE
    -- (e.user_id = '$uid' OR e.assignedto_id = '$uid')
    $text 
    AND CAST(e.appointmentdatetime AS DATE) = '$tdate'
    AND e.nextCFID = 0
    AND e.plan = 1;

");
        return $query->result();
    }
 








    public function AllTimesTaskwiseDetailsAnalysisData($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "u1.user_id = '$uid'";
        }else{
            $text = "u1.admin_id = '$uid'";
        }

        $query=$this->db->query("SELECT
    e.id,
    u1.name as task_user,
    cm.compname as company_name,
    e.nextCFID AS next_cfid,
    e.actontaken,
    e.purpose_achieved,
    e.appointmentdatetime AS task_date,
    e.initiateddt AS initiate_time,
    e.updateddate AS updated_time,
    s.name AS status_name,
    a.name AS task_name,
    ic.cstatus AS cstatus,
    e.autotask AS is_autotask,
    s1.name AS task_time_status_id,
    s2.name AS task_new_status_id,
    s3.name AS compay_current_satus,
    e.approved_status AS task_approved_status,
    user_type.name as user_types_name
FROM tblcallevents e
LEFT JOIN action a ON a.id = e.actiontype_id
LEFT JOIN init_call ic ON ic.id = e.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN status s ON s.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = e.status_id
LEFT JOIN status s2 ON s2.id = e.nstatus_id
LEFT JOIN status s3 ON s3.id = ic.cstatus
LEFT JOIN user_details u1 ON u1.user_id = e.user_id 
LEFT JOIN user_type ON user_type.id = u1.type_id
WHERE
    -- (e.user_id = '$uid' OR e.assignedto_id = '$uid')
    $text 
    -- AND CAST(e.appointmentdatetime AS DATE) = '$tdate'
    AND e.nextCFID = 0
    AND e.plan = 1;

");
        return $query->result();
    }





    public function GetAllTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate){

        $udata = $this->Menu_model->get_userbyid($uid);
        $utype = $udata[0]->type_id;
        if($utype == 3){
            $text = "AND u1.user_id  = '$uid'";
        }elseif($utype == 13){
            $text = "AND u1.aadmin  = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co  = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co  = '$uid'";
        }elseif($utype == 2){
            $text = "AND u1.admin_id  = '$uid'";
        }else{
            $text = '';
        }
    
        $cuurentYear = date('Y');

        $query=$this->db->query("SELECT
    u1.name,
    u1.user_id,
    ut.name AS type_name,
    userworkfrom.TYPE AS userworkfrom,
    tblcallevents.appointmentdatetime AS task_date,
    COUNT(tblcallevents.id) AS total_task,
    COUNT(
        CASE WHEN(
            tblcallevents.approved_status = 1 AND tblcallevents.autotask = 0
        ) THEN tblcallevents.id
    END
) AS approved_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = '' AND tblcallevents.autotask = 0
    ) THEN tblcallevents.id
END
) AS pending_approved,
COUNT(
    CASE WHEN tblcallevents.nextCFID != 0 THEN tblcallevents.id
END
) AS complete_task,
COUNT(
    CASE WHEN tblcallevents.nextCFID = 0 THEN tblcallevents.id
END
) AS pending_task,
COUNT(
    CASE WHEN(tblcallevents.autotask = 1) THEN tblcallevents.id
END
) AS total_autotask,
COUNT(
    CASE WHEN(tblcallevents.aftertask != 0) THEN tblcallevents.id
END
) AS after_task,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND tblcallevents.autotask = 1
    ) THEN tblcallevents.id
END
) AS complete_autotask,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID = 0 AND tblcallevents.autotask = 1
    ) THEN tblcallevents.id
END
) AS pending_autotask,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != ''
    ) THEN tblcallevents.id
END
) AS total_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = ''
    ) THEN tblcallevents.id
END
) AS pending_for_assign_after_reject_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1'
    ) THEN tblcallevents.id
END
) AS admin_create_request_for_self_assign,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_admin_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_user_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_yes,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_no,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_no_purpose_no,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 1 THEN actiontype_id
END
) call_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 THEN actiontype_id
END
) email_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 THEN actiontype_id
END
) scheduled_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 THEN actiontype_id
END
) barg_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 THEN actiontype_id
END
) whatsapp_activity,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 THEN actiontype_id
END
) write_mom,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 THEN actiontype_id
END
) write_proposal,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 THEN actiontype_id
END
) research_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 THEN actiontype_id
END
) documentation_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 THEN actiontype_id
END
) social_networking_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 THEN actiontype_id
END
) social_activity_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 15 THEN actiontype_id
END
) annual_review_target_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 THEN actiontype_id
END
) join_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 18 THEN actiontype_id
END
) mom_check_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 THEN actiontype_id
END
) create_bd_request_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 20 THEN actiontype_id
END
) submit_handover_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 21 THEN actiontype_id
END
) praposal_check_task,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 THEN 1
END
) * 10
) AS total_task_time,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 AND tblcallevents.autotask = 0 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 AND tblcallevents.autotask = 0 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 AND tblcallevents.autotask = 0 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 AND tblcallevents.autotask = 0 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 AND tblcallevents.autotask = 0 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 AND tblcallevents.autotask = 0 THEN 1
END
) * 10
) AS total_plan_time_on_planner,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 AND tblcallevents.autotask = 1 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 AND tblcallevents.autotask = 1 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 AND tblcallevents.autotask = 1 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 AND tblcallevents.autotask = 1 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 AND tblcallevents.autotask = 1 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 AND tblcallevents.autotask = 1 THEN 1
END
) * 10
) AS total_plan_time_with_autotask,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `task_plan_for_today`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS task_plan_for_today_request,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `special_request_for_leave`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS user_create_special_request_for_leave,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `task_plan_for_today`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS task_plan_for_today_request,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `reminder`
    WHERE
        user_id = u1.user_id AND DATE(created_at) = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS user_request_any_reminder,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `create_planner_request`
    WHERE
        request_user_id = u1.user_id AND DATE(created_at) = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS pending_task_planner_request,
(
    SELECT
        (
        SELECT
            COUNT(*)
        FROM
            `session_plan_time`
        WHERE
            user_id = u1.user_id AND DATE(created_at) != '$tdate'
    )
) AS session_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN user_day ud ON ud.user_id = u1.user_id
LEFT JOIN userworkfrom ON userworkfrom.id = ud.wffo
WHERE
    --   YEAR(tblcallevents.appointmentdatetime) = $cuurentYear ..
  CAST(
        tblcallevents.appointmentdatetime AS DATE
    ) = '$tdate' AND CAST(ud.ustart AS DATE) = '$tdate' $text

    -- CAST(ud.ustart AS DATE) != '$tdate'
    -- u1.status = 'active'
    -- AND CAST(ud.ustart AS DATE) != '' AND ud.ustart IS NOT NULL
    --  $text
GROUP BY
    u1.user_id");
        return $query->result();
    }






    public function GetTodaysAllTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate){

        $udata = $this->Menu_model->get_userbyid($uid);
        $utype = $udata[0]->type_id;
        if($utype == 3){
            $text = "AND u1.user_id  = '$uid'";
        }elseif($utype == 13){
            $text = "AND u1.aadmin  = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co  = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co  = '$uid'";
        }elseif($utype == 2){
            $text = "AND u1.admin_id  = '$uid'";
        }else{
            $text = '';
        }
    
        $cuurentYear = date('Y');

        $query=$this->db->query("SELECT
    u1.name,
    u1.user_id,
    ut.name AS type_name,
    userworkfrom.TYPE AS userworkfrom,
    tblcallevents.appointmentdatetime AS task_date,
    COUNT(tblcallevents.id) AS total_task,
    COUNT(
        CASE WHEN(
            tblcallevents.approved_status = 1 AND tblcallevents.autotask = 0
        ) THEN tblcallevents.id
    END
) AS approved_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = '' AND tblcallevents.autotask = 0
    ) THEN tblcallevents.id
END
) AS pending_approved,
COUNT(
    CASE WHEN tblcallevents.nextCFID != 0 THEN tblcallevents.id
END
) AS complete_task,
COUNT(
    CASE WHEN tblcallevents.nextCFID = 0 THEN tblcallevents.id
END
) AS pending_task,
COUNT(
    CASE WHEN(tblcallevents.autotask = 1) THEN tblcallevents.id
END
) AS total_autotask,
COUNT(
    CASE WHEN(tblcallevents.aftertask != 0) THEN tblcallevents.id
END
) AS after_task,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND tblcallevents.autotask = 1
    ) THEN tblcallevents.id
END
) AS complete_autotask,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID = 0 AND tblcallevents.autotask = 1
    ) THEN tblcallevents.id
END
) AS pending_autotask,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != ''
    ) THEN tblcallevents.id
END
) AS total_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = ''
    ) THEN tblcallevents.id
END
) AS pending_for_assign_after_reject_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1'
    ) THEN tblcallevents.id
END
) AS admin_create_request_for_self_assign,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_admin_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_user_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_yes,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_no,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_no_purpose_no,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 1 THEN actiontype_id
END
) call_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 THEN actiontype_id
END
) email_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 THEN actiontype_id
END
) scheduled_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 THEN actiontype_id
END
) barg_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 THEN actiontype_id
END
) whatsapp_activity,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 THEN actiontype_id
END
) write_mom,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 THEN actiontype_id
END
) write_proposal,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 THEN actiontype_id
END
) research_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 THEN actiontype_id
END
) documentation_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 THEN actiontype_id
END
) social_networking_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 THEN actiontype_id
END
) social_activity_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 15 THEN actiontype_id
END
) annual_review_target_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 THEN actiontype_id
END
) join_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 18 THEN actiontype_id
END
) mom_check_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 THEN actiontype_id
END
) create_bd_request_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 20 THEN actiontype_id
END
) submit_handover_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 21 THEN actiontype_id
END
) praposal_check_task,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 THEN 1
END
) * 10
) AS total_task_time,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 AND tblcallevents.autotask = 0 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 AND tblcallevents.autotask = 0 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 AND tblcallevents.autotask = 0 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 AND tblcallevents.autotask = 0 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 AND tblcallevents.autotask = 0 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 AND tblcallevents.autotask = 0 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 AND tblcallevents.autotask = 0 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 AND tblcallevents.autotask = 0 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 AND tblcallevents.autotask = 0 THEN 1
END
) * 10
) AS total_plan_time_on_planner,
(
    COUNT(
        CASE WHEN tblcallevents.actiontype_id = 1 AND tblcallevents.autotask = 1 THEN 1
    END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 AND tblcallevents.autotask = 1 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 AND tblcallevents.autotask = 1 THEN 1
END
) * 10 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 AND tblcallevents.autotask = 1 THEN 1
END
) * 15 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 8 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 9 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 AND tblcallevents.autotask = 1 THEN 1
END
) * 5 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 11 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 AND tblcallevents.autotask = 1 THEN 1
END
) * 30 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 AND tblcallevents.autotask = 1 THEN 1
END
) * 2 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 AND tblcallevents.autotask = 1 THEN 1
END
) * 60 + COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 AND tblcallevents.autotask = 1 THEN 1
END
) * 10
) AS total_plan_time_with_autotask,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `task_plan_for_today`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS task_plan_for_today_request,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `special_request_for_leave`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS user_create_special_request_for_leave,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `task_plan_for_today`
    WHERE
        user_id = u1.user_id AND DATE = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS task_plan_for_today_request,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `reminder`
    WHERE
        user_id = u1.user_id AND DATE(created_at) = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS user_request_any_reminder,
CASE WHEN EXISTS(
    SELECT
        *
    FROM
        `create_planner_request`
    WHERE
        request_user_id = u1.user_id AND DATE(created_at) = '$tdate'
) THEN 'Yes' ELSE 'No'
END AS pending_task_planner_request,
(
    SELECT
        (
        SELECT
            COUNT(*)
        FROM
            `session_plan_time`
        WHERE
            user_id = u1.user_id AND DATE(created_at) != '$tdate'
    )
) AS session_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN user_day ud ON ud.user_id = u1.user_id
LEFT JOIN userworkfrom ON userworkfrom.id = ud.wffo
LEFT JOIN status ON status.id = ud.wffo
WHERE
    --   YEAR(tblcallevents.appointmentdatetime) = $cuurentYear ..
  CAST(
        tblcallevents.appointmentdatetime AS DATE
    ) = '$tdate' AND CAST(ud.ustart AS DATE) = '$tdate' $text
AND tblcallevents.autotask = 0
    -- CAST(ud.ustart AS DATE) != '$tdate'
    -- u1.status = 'active'
    -- AND CAST(ud.ustart AS DATE) != '' AND ud.ustart IS NOT NULL
    --  $text
GROUP BY
    u1.user_id");
        return $query->result();
    }








    public function GetTodaysStatusWiseTaskCount($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $query=$this->db->query("SELECT
                u1.name AS task_user_name,
                ut.name AS user_types,
                status.name AS status_name,
                COUNT(tblcallevents.id) AS task_count
            FROM
                `tblcallevents`
            LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
            LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
            LEFT JOIN user_type ut ON ut.id = u1.type_id
            LEFT JOIN status ON status.id = init_call.cstatus
            WHERE
                CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate' 
                $text
                AND u1.user_id IS NOT NULL AND u1.status = 'active'
            GROUP BY
                u1.name, ut.name, status.name");
                    return $query->result();
    }

    public function GetTodaysActionWiseTaskCount($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $query=$this->db->query("SELECT
                u1.name AS task_user_name,
                ut.name AS user_types,
                status.name AS status_name,
                action.name as task_name,
                COUNT(tblcallevents.id) AS task_count
            FROM
                `tblcallevents`
            LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
            LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
            LEFT JOIN user_type ut ON ut.id = u1.type_id
            LEFT JOIN status ON status.id = init_call.cstatus
            LEFT JOIN action ON action.id = tblcallevents.actiontype_id
            WHERE
                CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate' 
                $text
                AND u1.status = 'active'
            GROUP BY
                u1.user_id,tblcallevents.actiontype_id,status.id");
                    return $query->result();
    }
    public function GetTodaysTimeSlotWiseTaskCount($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $query=$this->db->query("SELECT
    u1.name AS task_user_name,
    ut.name AS user_types,
    status.name AS status_name,
    action.name AS task_name,
    CONCAT(
        LPAD(HOUR(tblcallevents.appointmentdatetime) % 12, 2, '0'), ':00 ', 
        CASE 
            WHEN HOUR(tblcallevents.appointmentdatetime) < 12 THEN 'AM' 
            ELSE 'PM' 
        END,
        ' to ',
        LPAD((HOUR(tblcallevents.appointmentdatetime) + 1) % 12, 2, '0'), ':00 ',
        CASE 
            WHEN (HOUR(tblcallevents.appointmentdatetime) + 1) < 12 THEN 'AM' 
            ELSE 'PM' 
        END
    ) AS task_hour,
    COUNT(tblcallevents.id) AS task_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
WHERE
    CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate'
    AND HOUR(tblcallevents.appointmentdatetime) BETWEEN 10 AND 19
    $text
    AND u1.status = 'active'
GROUP BY
    u1.user_id,
    tblcallevents.actiontype_id,
    status.id,
    HOUR(tblcallevents.appointmentdatetime)
ORDER BY
    HOUR(tblcallevents.appointmentdatetime)");
                    return $query->result();
    }



    public function GetTodaysStatusConversionWiseTaskCount($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $query=$this->db->query("SELECT
    u1.name AS task_user_name,
    ut.name AS user_types,
    action.name AS task_name,
    CONCAT(s1.name, ' → ', s2.name) AS conversion_name,
    COUNT(tblcallevents.id) AS conversion_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
LEFT JOIN status s1 ON s1.id = tblcallevents.status_id         -- previous status
LEFT JOIN status s2 ON s2.id = tblcallevents.nstatus_id        -- new status
WHERE
-- CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate' AND
     u1.status = 'active' $text
    AND tblcallevents.status_id != tblcallevents.nstatus_id
GROUP BY
    u1.user_id,
    tblcallevents.actiontype_id,
    tblcallevents.status_id,
    tblcallevents.nstatus_id
ORDER BY
    conversion_count DESC");
                    return $query->result();
    }

    public function GetTodaysStatusConversionWiseTaskWithDateCount($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $query = $this->db->query("SELECT
    DATE(tblcallevents.appointmentdatetime) AS appointment_date,
    u1.name AS task_user_name,
    ut.name AS user_types,
    action.name AS task_name,
    CONCAT(s1.name, ' → ', s2.name) AS conversion_name,
    COUNT(tblcallevents.id) AS conversion_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
LEFT JOIN status s1 ON s1.id = tblcallevents.status_id         -- previous status
LEFT JOIN status s2 ON s2.id = tblcallevents.nstatus_id        -- new status
WHERE
     u1.status = 'active' $text
    AND tblcallevents.status_id != tblcallevents.nstatus_id
    AND tblcallevents.appointmentdatetime IS NOT NULL
GROUP BY
    DATE(tblcallevents.appointmentdatetime),
    u1.user_id,
    tblcallevents.actiontype_id,
    tblcallevents.status_id,
    tblcallevents.nstatus_id
ORDER BY
    appointment_date DESC,
    conversion_count DESC
");
        return $query->result();
    }




    public function GetTodaysAllTodaysPlannerLog($uid,$tdate){

        $udetail = $this->Menu_model->get_userbyid($uid);
        $uyid = $udetail[0]->type_id;
        if($uyid == 3){
            $text = " u1.user_id = '$uid'";
        }else if($uyid == 13){
            $text = " u1.aadmin = '$uid'";
        }else if($uyid == 4){
            $text = " u1.pst_co = '$uid'";
        }else if($uyid == 15){
            $text = " u1.sales_co = '$uid'";
        }else{
            $text = " u1.admin_id = '$uid'";
        }
        $query=$this->db->query("SELECT
    plog.init_id,
    company_master.id AS cid,
    company_master.compname AS company_name,
    plog.to_user,
    plog.task_id,
    tbc.actiontype_id,
    action.name AS task_name,
    init_call.cstatus,
    init_call.mainbd,
    init_call.apst,
    init_call.clm_id,
    status.name,
    MIN(plog.org_task_date) AS org_task_date,
    plog.remarks,
    (
    SELECT
        IFNULL(
            MAX(plog1.re_created_at),
            plog.re_created_at
        )
    FROM
        planner_log plog1
    WHERE
        plog1.task_id = plog.task_id AND plog1.to_user = plog.to_user AND plog1.re_created_at > MIN(plog.re_created_at)
) AS last_create_date,
(
    SELECT
        COUNT(*)
    FROM
        planner_log plog3
    WHERE
        plog3.task_id = plog.task_id
) AS duplicate_count,
CONCAT(
    TIMESTAMPDIFF(
        DAY,
        MIN(plog.org_task_date),
        (
        SELECT
            IFNULL(
                MAX(plog1.re_created_at),
                plog.re_created_at
            )
        FROM
            planner_log plog1
        WHERE
            plog1.init_id = plog.init_id AND plog1.to_user = plog.to_user AND plog1.re_created_at > MIN(plog.re_created_at)
    )
    ),
    ' days ',
    MOD(
        TIMESTAMPDIFF(
            HOUR,
            MIN(plog.org_task_date),
            (
            SELECT
                IFNULL(
                    MAX(plog1.re_created_at),
                    plog.re_created_at
                )
            FROM
                planner_log plog1
            WHERE
                plog1.init_id = plog.init_id AND plog1.to_user = plog.to_user AND plog1.re_created_at > MIN(plog.org_task_date)
        )
        ),
        24
    ),
    ' hours ',
    MOD(
        TIMESTAMPDIFF(
            MINUTE,
            MIN(plog.org_task_date),
            (
            SELECT
                IFNULL(
                    MAX(plog1.re_created_at),
                    plog.re_created_at
                )
            FROM
                planner_log plog1
            WHERE
                plog1.init_id = plog.init_id AND plog1.to_user = plog.to_user AND plog1.re_created_at > MIN(plog.org_task_date)
        )
        ),
        60
    ),
    ' minutes'
) AS time_difference,
u1.name AS to_user_name
FROM
    planner_log plog
LEFT JOIN tblcallevents tbc ON
    tbc.id = plog.task_id
LEFT JOIN action ON action.id = tbc.actiontype_id
LEFT JOIN user_details u1 ON u1.user_id = plog.to_user
LEFT JOIN init_call ON init_call.id = plog.init_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN status ON status.id = init_call.cstatus
WHERE
    DATE(plog.re_created_at) = '$tdate' AND tbc.nextCFID = 0 AND org_task_date != '0000-00-00 00:00:00' AND $text
GROUP BY
    plog.task_id
HAVING
    duplicate_count >= 1");
    
        return $query->result();
    }
    




    public function GetTodaysPlannerCurrentYearFocusFunnelsLogByuser($uid,$fyear,$cdate){


        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
      
        if($utype == 2){
            $adminfilter = " AND u1.admin_id = '$uid'";
        } elseif($utype == 15){
            $adminfilter = " AND u1.sales_co = '$uid'";
        }elseif($utype == 4){
            $adminfilter = " AND (u1.pst_co = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 13){
            $adminfilter = " AND  (u1.aadmin = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 3){
            $adminfilter = " AND u1.user_id = '$uid'";
        }else{
            $adminfilter = " AND u1.admin_id = '$uid'";
        }
    
    
        $cfyear = date("Y");
    
        $query = $this->db->query("SELECT 
        amr.by_uid AS cuser_id,
        u1.name AS username,
        -- amr.financial_year as financial_year,
        COUNT(amr.id) AS current_year_focus_funnels,
        tblc.appointmentdatetime as task_date,
        COUNT(DISTINCT tblc.cid_id) AS plan_task_count
    FROM 
        annual_main_review amr
    LEFT JOIN user_details u1 ON amr.by_uid = u1.user_id
    LEFT JOIN company_master cm ON cm.id = amr.inid
    LEFT JOIN tblcallevents tblc 
        ON tblc.cid_id = amr.inid 
        AND DATE(tblc.appointmentdatetime) = '$cdate'
        AND YEAR(tblc.appointmentdatetime) = '$cfyear'
        AND tblc.user_id = amr.by_uid  -- Ensure correct user match
    WHERE 
        u1.type_id IN (3,13,4) 
        AND u1.status = 'active'
        $adminfilter
        AND amr.financial_year = '$fyear'
        AND amr.current_year_focus_funnel = 'yes'
        AND amr.id IS NOT NULL
        AND amr.inid IS NOT NULL
        -- AND tblc.appointmentdatetime is not null
    GROUP BY 
        amr.by_uid, u1.name
    ORDER BY 
        u1.name ASC");
        
        return $data = $query->result();
    }


    public function GetAllPlannerCurrentYearFocusFunnelsLogByuser($uid,$fyear,$cdate){


        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
      
        if($utype == 2){
            $adminfilter = " AND u1.admin_id = '$uid'";
        } elseif($utype == 15){
            $adminfilter = " AND u1.sales_co = '$uid'";
        }elseif($utype == 4){
            $adminfilter = " AND (u1.pst_co = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 13){
            $adminfilter = " AND  (u1.aadmin = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 3){
            $adminfilter = " AND u1.user_id = '$uid'";
        }else{
            $adminfilter = " AND u1.admin_id = '$uid'";
        }
    
    
        $cfyear = date("Y");
    
        $query = $this->db->query("SELECT 
        amr.by_uid AS cuser_id,
        u1.name AS username,
        -- amr.financial_year as financial_year,
        COUNT(amr.id) AS current_year_focus_funnels,
        tblc.appointmentdatetime as task_date,
        COUNT(DISTINCT tblc.cid_id) AS plan_task_count
    FROM 
        annual_main_review amr
    LEFT JOIN user_details u1 ON amr.by_uid = u1.user_id
    LEFT JOIN company_master cm ON cm.id = amr.inid
    LEFT JOIN tblcallevents tblc 
        ON tblc.cid_id = amr.inid 
        -- AND DATE(tblc.appointmentdatetime) = '$cdate'
        AND YEAR(tblc.appointmentdatetime) = '$cfyear'
        AND tblc.user_id = amr.by_uid  -- Ensure correct user match
    WHERE 
        u1.type_id IN (3,13,4) 
        AND u1.status = 'active'
        $adminfilter
        AND amr.financial_year = '$fyear'
        AND amr.current_year_focus_funnel = 'yes'
        AND amr.id IS NOT NULL
        AND amr.inid IS NOT NULL
        AND tblc.appointmentdatetime is not null
    GROUP BY 
        amr.by_uid, u1.name
    ORDER BY 
        u1.name ASC");
        
        return $data = $query->result();
    }




    public function getTodaysTaskVsCompanyStatusAnalysis($uid,$cdate){


        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
      
        if($utype == 2){
            $adminfilter = " AND u1.admin_id = '$uid'";
        } elseif($utype == 15){
            $adminfilter = " AND u1.sales_co = '$uid'";
        }elseif($utype == 4){
            $adminfilter = " AND (u1.pst_co = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 13){
            $adminfilter = " AND  (u1.aadmin = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 3){
            $adminfilter = " AND u1.user_id = '$uid'";
        }else{
            $adminfilter = " AND u1.admin_id = '$uid'";
        }
    
    
        $cfyear = date("Y");
    
        $query = $this->db->query("SELECT
    u1.user_id,
    u1.name AS task_user_name,
    ut.name AS user_types,
    s.name AS status_name,

    COUNT(tblcallevents.id) AS total_count,

    COUNT(CASE WHEN tblcallevents.nextCFID = '0' THEN tblcallevents.id END) AS task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' THEN tblcallevents.id END) AS task_complete,

    COUNT(CASE WHEN tblcallevents.approved_status = '0' THEN tblcallevents.id END) AS approved_pending,
    COUNT(CASE WHEN tblcallevents.approved_status = '1' THEN tblcallevents.id END) AS approved_done,

    COUNT(CASE WHEN tblcallevents.autotask = '1' THEN tblcallevents.id END) AS total_auto_task_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_complete,
    COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != ''
    ) THEN tblcallevents.id
END
) AS total_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = ''
    ) THEN tblcallevents.id
END
) AS pending_for_assign_after_reject_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1'
    ) THEN tblcallevents.id
END
) AS admin_create_request_for_self_assign,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_admin_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_user_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_yes,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_no,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_no_purpose_no

FROM tblcallevents

LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status s ON s.id = tblcallevents.status_id

WHERE
    u1.status = 'active'
-- AND YEAR(tblcallevents.appointmentdatetime) = '$cfyear'
    -- AND tblcallevents.status_id != tblcallevents.nstatus_id
    AND CAST(tblcallevents.appointmentdatetime AS DATE) = '$cdate' -- uncomment if needed
    $adminfilter

GROUP BY
    u1.user_id,
    u1.name,
    ut.name,
    s.name");
        
        return $data = $query->result();
    }
    
    public function getAlltimesTaskVsCompanyStatusAnalysis($uid,$cdate){


        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
      
        if($utype == 2){
            $adminfilter = " AND u1.admin_id = '$uid'";
        } elseif($utype == 15){
            $adminfilter = " AND u1.sales_co = '$uid'";
        }elseif($utype == 4){
            $adminfilter = " AND (u1.pst_co = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 13){
            $adminfilter = " AND  (u1.aadmin = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 3){
            $adminfilter = " AND u1.user_id = '$uid'";
        }else{
            $adminfilter = " AND u1.admin_id = '$uid'";
        }
    
    
        $cfyear = date("Y");
    
        $query = $this->db->query("SELECT
    u1.user_id,
    u1.name AS task_user_name,
    ut.name AS user_types,
    s.name AS status_name,
    

    COUNT(tblcallevents.id) AS total_count,

    COUNT(CASE WHEN tblcallevents.nextCFID = '0' THEN tblcallevents.id END) AS task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' THEN tblcallevents.id END) AS task_complete,

    COUNT(CASE WHEN tblcallevents.approved_status = '0' THEN tblcallevents.id END) AS approved_pending,
    COUNT(CASE WHEN tblcallevents.approved_status = '1' THEN tblcallevents.id END) AS approved_done,

    COUNT(CASE WHEN tblcallevents.autotask = '1' THEN tblcallevents.id END) AS total_auto_task_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_complete,
    COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != ''
    ) THEN tblcallevents.id
END
) AS total_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = ''
    ) THEN tblcallevents.id
END
) AS pending_for_assign_after_reject_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1'
    ) THEN tblcallevents.id
END
) AS admin_create_request_for_self_assign,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_admin_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_user_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_yes,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_no,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_no_purpose_no

FROM tblcallevents

LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status s ON s.id = tblcallevents.status_id

WHERE
    u1.status = 'active'
AND YEAR(tblcallevents.appointmentdatetime) = '$cfyear'
    -- AND tblcallevents.status_id != tblcallevents.nstatus_id
    -- AND CAST(tblcallevents.appointmentdatetime AS DATE) = '$cdate' -- uncomment if needed
    $adminfilter

GROUP BY
    u1.user_id,
    u1.name,
    ut.name,
    s.name");
        
        return $data = $query->result();
    }





    public function getAlltimesStatusConversionTaskVsCompanyStatusAnalysis($uid,$cdate){


        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
      
        if($utype == 2){
            $adminfilter = " AND u1.admin_id = '$uid'";
        } elseif($utype == 15){
            $adminfilter = " AND u1.sales_co = '$uid'";
        }elseif($utype == 4){
            $adminfilter = " AND (u1.pst_co = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 13){
            $adminfilter = " AND  (u1.aadmin = '$uid' || u1.user_id = '$uid')";
        }elseif($utype == 3){
            $adminfilter = " AND u1.user_id = '$uid'";
        }else{
            $adminfilter = " AND u1.admin_id = '$uid'";
        }
    
    
        $cfyear = date("Y");
    
        $query = $this->db->query("SELECT
    u1.user_id,
    u1.name AS task_user_name,
    ut.name AS user_types,
    s.name AS status_name,

    COUNT(tblcallevents.id) AS total_count,

    COUNT(CASE WHEN tblcallevents.nextCFID = '0' THEN tblcallevents.id END) AS task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' THEN tblcallevents.id END) AS task_complete,

    COUNT(CASE WHEN tblcallevents.approved_status = '0' THEN tblcallevents.id END) AS approved_pending,
    COUNT(CASE WHEN tblcallevents.approved_status = '1' THEN tblcallevents.id END) AS approved_done,

    COUNT(CASE WHEN tblcallevents.autotask = '1' THEN tblcallevents.id END) AS total_auto_task_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_complete,
    COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != ''
    ) THEN tblcallevents.id
END
) AS total_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = ''
    ) THEN tblcallevents.id
END
) AS pending_for_assign_after_reject_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1'
    ) THEN tblcallevents.id
END
) AS admin_create_request_for_self_assign,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_admin_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3'
    ) THEN tblcallevents.id
END
) AS task_assignd_by_user_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_yes,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_yes_purpose_no,
COUNT(
    CASE WHEN(
        tblcallevents.nextCFID != 0 AND(
            tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no'
        )
    ) THEN tblcallevents.id
END
) AS action_no_purpose_no

FROM tblcallevents

LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status s ON s.id = tblcallevents.status_id

WHERE
    u1.status = 'active'
AND YEAR(tblcallevents.appointmentdatetime) = '$cfyear'
    -- AND tblcallevents.status_id != tblcallevents.nstatus_id
    -- AND CAST(tblcallevents.appointmentdatetime AS DATE) = '$cdate' -- uncomment if needed
    $adminfilter

GROUP BY
    u1.user_id,
    u1.name,
    ut.name,
    s.name");
        
        return $data = $query->result();
    }

    public function GetCurrentYearsStatusConversionWiseTaskCount($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $cfyear = date("Y");
      
        $query = $this->db->query("SELECT
    DATE(tblcallevents.appointmentdatetime) AS appointment_date,
    u1.name AS task_user_name,
    ut.name AS user_types,
    action.name AS task_name,
    CONCAT(s1.name, ' → ', s2.name) AS conversion_name,
    COUNT(tblcallevents.id) AS conversion_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
LEFT JOIN status s1 ON s1.id = tblcallevents.status_id         -- previous status
LEFT JOIN status s2 ON s2.id = tblcallevents.nstatus_id        -- new status
WHERE
     u1.status = 'active' $text
    AND tblcallevents.status_id != tblcallevents.nstatus_id
    AND tblcallevents.appointmentdatetime IS NOT NULL
    AND YEAR(tblcallevents.appointmentdatetime) = '$cfyear'
GROUP BY
    DATE(tblcallevents.appointmentdatetime),
    u1.user_id,
    tblcallevents.actiontype_id,
    tblcallevents.status_id,
    tblcallevents.nstatus_id
ORDER BY
    appointment_date DESC,
    conversion_count DESC
");
      return $query->result();
    }


    public function GetTodaysCompanyStatusConversionWiseTaskCount($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $cfyear = date("Y");
      
        $query = $this->db->query("SELECT
    DATE(tblcallevents.appointmentdatetime) AS appointment_date,
    u1.name AS task_user_name,
    ut.name AS user_types,
    action.name AS task_name,
    CONCAT(s1.name, ' → ', s2.name) AS conversion_name,
    COUNT(tblcallevents.id) AS conversion_count
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
LEFT JOIN status s1 ON s1.id = tblcallevents.status_id    
LEFT JOIN status s2 ON s2.id = tblcallevents.nstatus_id
WHERE
     u1.status = 'active' $text
    AND tblcallevents.status_id != tblcallevents.nstatus_id
    AND tblcallevents.appointmentdatetime IS NOT NULL
    AND CAST(tblcallevents.appointmentdatetime as Date) = '$tdate'
GROUP BY
    DATE(tblcallevents.appointmentdatetime),
    u1.user_id,
    tblcallevents.actiontype_id,
    tblcallevents.status_id,
    tblcallevents.nstatus_id
ORDER BY
    appointment_date DESC,
    conversion_count DESC
");
      return $query->result();
    }




    public function GetCompanyStatusConversionRate($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $cfyear = date("Y");
      
        $query = $this->db->query("SELECT
    DATE(tblcallevents.appointmentdatetime) AS appointment_date,
    u1.name AS task_user_name,
    ut.name AS user_types,
    action.name AS task_name,
    CONCAT(s1.name, ' → ', s2.name) AS conversion_name,
    COUNT(tblcallevents.id) AS conversion_count,
    CASE
        -- Positive  Conversion conditions
        WHEN (s1.name = 'Open' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open' AND s2.name = 'Open RPEM') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Will do Later') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Very Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Very Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'OPEN RPEM') THEN 'Positive Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'TTD-Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Very Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'WNO-Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'OPEN RPEM') THEN 'Positive Conversion'
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Very Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Very Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 'Positive Conversion'
        WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 'Positive Conversion'
        

        -- Negative Conversion conditions
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'TTD-Reachout' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Open RPEM') THEN 'Negative Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Not Interested') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Very Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Not Interested') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Open RPEM') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Not Interested') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'TTD-Reachout' AND s2.name = 'Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'TTD-Reachout' AND s2.name = 'OPEN RPEM') THEN 'Negative Conversion'
        
        ELSE 'Other'
    END AS conversion_type
FROM
    tblcallevents
LEFT JOIN
    user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN
    user_type ut ON ut.id = u1.type_id
LEFT JOIN
    action ON action.id = tblcallevents.actiontype_id
LEFT JOIN
    status s1 ON s1.id = tblcallevents.status_id
LEFT JOIN
    status s2 ON s2.id = tblcallevents.nstatus_id
WHERE
    u1.status = 'active'
    -- AND u1.admin_id = '45'
    $text
    AND tblcallevents.status_id != tblcallevents.nstatus_id
    AND tblcallevents.appointmentdatetime IS NOT NULL
    AND YEAR(tblcallevents.appointmentdatetime) = '$cfyear'
    --  AND CAST(tblcallevents.appointmentdatetime AS DATE) = '2025-03-12'
GROUP BY
    DATE(tblcallevents.appointmentdatetime),
    u1.user_id,
    tblcallevents.actiontype_id,
    tblcallevents.status_id,
    tblcallevents.nstatus_id
ORDER BY
    appointment_date DESC,
    conversion_count DESC;
");
      return $query->result();
    }
    public function GetTodaysCompanyStatusConversionRate($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $cfyear = date("Y");
      
        $query = $this->db->query("SELECT
    DATE(tblcallevents.appointmentdatetime) AS appointment_date,
    u1.name AS task_user_name,
    ut.name AS user_types,
    action.name AS task_name,
    CONCAT(s1.name, ' → ', s2.name) AS conversion_name,
    COUNT(tblcallevents.id) AS conversion_count,
    CASE
        -- Positive  Conversion conditions
        WHEN (s1.name = 'Open' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open' AND s2.name = 'Open RPEM') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Will do Later') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Very Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Very Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'OPEN RPEM') THEN 'Positive Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'TTD-Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Very Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'WNO-Reachout') THEN 'Positive Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'OPEN RPEM') THEN 'Positive Conversion'
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Tentative') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Very Positive-NAP') THEN 'Positive Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Very Positive') THEN 'Positive Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Closure') THEN 'Positive Conversion'
        WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 'Positive Conversion'
        WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 'Positive Conversion'
        

        -- Negative Conversion conditions
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'TTD-Reachout' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Open RPEM') THEN 'Negative Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Not Interested') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Very Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Will do Later') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Not Interested') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Open RPEM') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive-NAP' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'WNO Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'TTD Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Not Interested') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Positive') THEN 'Negative Conversion'
        WHEN (s1.name = 'Closure' AND s2.name = 'Tentative') THEN 'Negative Conversion'
        WHEN (s1.name = 'Positive' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Reachout' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'Tentative' AND s2.name = 'Open') THEN 'Negative Conversion'
        WHEN (s1.name = 'TTD-Reachout' AND s2.name = 'Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 'Negative Conversion'
        WHEN (s1.name = 'TTD-Reachout' AND s2.name = 'OPEN RPEM') THEN 'Negative Conversion'
        
        ELSE 'Other'
    END AS conversion_type
FROM
    tblcallevents
LEFT JOIN
    user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN
    user_type ut ON ut.id = u1.type_id
LEFT JOIN
    action ON action.id = tblcallevents.actiontype_id
LEFT JOIN
    status s1 ON s1.id = tblcallevents.status_id
LEFT JOIN
    status s2 ON s2.id = tblcallevents.nstatus_id
WHERE
    u1.status = 'active'
    AND u1.admin_id = '45'
    AND tblcallevents.status_id != tblcallevents.nstatus_id
    AND tblcallevents.appointmentdatetime IS NOT NULL
    -- AND YEAR(tblcallevents.appointmentdatetime) = '$cfyear'
     AND CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate'
GROUP BY
    DATE(tblcallevents.appointmentdatetime),
    u1.user_id,
    tblcallevents.actiontype_id,
    tblcallevents.status_id,
    tblcallevents.nstatus_id
ORDER BY
    appointment_date DESC,
    conversion_count DESC;
");
      return $query->result();
    }



    public function GetDateWiseTaskCompanyStatusConversionRate($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $cfyear = date("Y");
      
        $query = $this->db->query("SELECT 
    u1.user_id,
    u1.name AS task_user_name,
    ut.name AS user_types,
    s.name AS status_name,
    DATE(tblcallevents.appointmentdatetime) AS task_date,
    
    COUNT(tblcallevents.id) AS total_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' THEN tblcallevents.id END) AS task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' THEN tblcallevents.id END) AS task_complete,
    
    COUNT(CASE WHEN tblcallevents.approved_status = '0' THEN tblcallevents.id END) AS approved_pending,
    COUNT(CASE WHEN tblcallevents.approved_status = '1' THEN tblcallevents.id END) AS approved_done,
    
    COUNT(CASE WHEN tblcallevents.autotask = '1' THEN tblcallevents.id END) AS total_auto_task_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_complete,
    
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' THEN tblcallevents.id END) AS total_reject,
    
    COUNT(CASE 
        WHEN tblcallevents.approved_status = '0' 
             AND tblcallevents.approved_by != '' 
             AND tblcallevents.self_assign = '' 
        THEN tblcallevents.id 
    END) AS pending_for_assign_after_reject_task,
    
    COUNT(CASE 
        WHEN tblcallevents.approved_status = '0' 
             AND tblcallevents.approved_by != '' 
             AND tblcallevents.self_assign = '1' 
        THEN tblcallevents.id 
    END) AS admin_create_request_for_self_assign,
    
    COUNT(CASE 
        WHEN tblcallevents.approved_status = '0' 
             AND tblcallevents.approved_by != '' 
             AND tblcallevents.self_assign = '2' 
        THEN tblcallevents.id 
    END) AS task_assignd_by_admin_after_reject,
    
    COUNT(CASE 
        WHEN tblcallevents.approved_status = '0' 
             AND tblcallevents.approved_by != '' 
             AND tblcallevents.self_assign = '3' 
        THEN tblcallevents.id 
    END) AS task_assignd_by_user_after_reject,
    
    COUNT(CASE 
        WHEN tblcallevents.nextCFID != '0' 
             AND tblcallevents.actontaken = 'yes' 
             AND tblcallevents.purpose_achieved = 'yes' 
        THEN tblcallevents.id 
    END) AS action_yes_purpose_yes,
    
    COUNT(CASE 
        WHEN tblcallevents.nextCFID != '0' 
             AND tblcallevents.actontaken = 'yes' 
             AND tblcallevents.purpose_achieved = 'no' 
        THEN tblcallevents.id 
    END) AS action_yes_purpose_no,
    
    COUNT(CASE 
        WHEN tblcallevents.nextCFID != '0' 
             AND tblcallevents.actontaken = 'no' 
             AND tblcallevents.purpose_achieved = 'no' 
        THEN tblcallevents.id 
    END) AS action_no_purpose_no

FROM tblcallevents

LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status s ON s.id = tblcallevents.status_id

WHERE 
    u1.status = 'active' 
    AND YEAR(tblcallevents.appointmentdatetime) = '$cfyear' 
    $text

GROUP BY 
    u1.user_id, 
    u1.name, 
    ut.name, 
    s.name, 
    DATE(tblcallevents.appointmentdatetime)");
      return $query->result();
    }





    public function GetTodaysUserDayManagement($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $cfyear = date("Y");

        $query=$this->db->query("SELECT
    u1.name AS user_name,
    ut.name AS department_name,
    userworkfrom.TYPE AS work_from,
    user_day.ustart,
    user_day.uclose,
    user_day.slatitude AS start_latitude,
    user_day.slongitude AS start_longitude,
    user_day.clatitude AS closed_latitude,
    user_day.clongitude AS closed_longitude,

    -- Start System Decision Logic
    CASE
        WHEN TIME(user_day.ustart) BETWEEN '10:00:00' AND '10:14:59' THEN 'Good Start'
        WHEN TIME(user_day.ustart) BETWEEN '10:15:00' AND '10:29:59' THEN 'Not Good Start'
        WHEN TIME(user_day.ustart) BETWEEN '10:30:00' AND '10:59:59' THEN 'Bad Start'
        WHEN TIME(user_day.ustart) BETWEEN '11:00:00' AND '11:29:59' THEN 'Very Bad Start'
        WHEN TIME(user_day.ustart) BETWEEN '11:30:00' AND '23:59:59' THEN 'Very Very Bad Start'
        ELSE 'Start time out of range'
    END AS start_system_decision,

    -- Closed System Decision Logic
    CASE
        WHEN TIME(user_day.uclose) BETWEEN '18:30:00' AND '23:45:00' THEN 'Good Closed'
        WHEN TIME(user_day.uclose) BETWEEN '10:00:00' AND '13:30:00' THEN 'Bad Closed'
        WHEN TIME(user_day.uclose) BETWEEN '13:30:00' AND '18:30:00' THEN 'Bad Closed'
        ELSE NULL
    END AS closed_system_decision,

    -- Day Status Message
    CASE
        WHEN user_day.uclose IS NULL THEN 'User not closed their day'
        WHEN DATE(user_day.ustart) = DATE(user_day.uclose) THEN 'Closed on same day by user'
        ELSE 'Not closed on same day by user'
    END AS day_status_message,

    -- Time Difference (formatted as days, hours, minutes)
    CASE 
        WHEN user_day.uclose IS NOT NULL THEN
            CONCAT(
                FLOOR(TIMESTAMPDIFF(MINUTE, user_day.ustart, user_day.uclose) / 1440), ' days ',
                FLOOR(MOD(TIMESTAMPDIFF(MINUTE, user_day.ustart, user_day.uclose), 1440) / 60), ' hours ',
                MOD(TIMESTAMPDIFF(MINUTE, user_day.ustart, user_day.uclose), 60), ' minutes'
            )
        ELSE 'N/A'
    END AS time_diffrerent

FROM
    user_day
LEFT JOIN user_details u1 ON u1.user_id = user_day.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN userworkfrom ON userworkfrom.ID = user_day.wffo
WHERE
    u1.status = 'active'
    $text
    AND CAST(user_day.ustart as Date) = '$tdate'");

    return $query->result();
      
    }


    public function GetCurrentYearUserDayManagement($uid,$tdate){

        $utype = $this->Menu_model->get_userbyid($uid);
        $utype = $utype[0]->type_id;
        if($utype == 13){
            $text = "AND u1.aadmin = '$uid'";
        }elseif($utype == 4){
            $text = "AND u1.pst_co = '$uid'";
        }elseif($utype == 15){
            $text = "AND u1.sales_co = '$uid'";
        }elseif($utype == 3){
            $text = "AND u1.user_id = '$uid'";
        }else{
            $text = "AND u1.admin_id = '$uid'";
        }

        $cfyear = date("Y");

        $query=$this->db->query("SELECT
    u1.name AS user_name,
    ut.name AS department_name,
    userworkfrom.TYPE AS work_from,
    user_day.ustart,
    user_day.uclose,
    user_day.slatitude AS start_latitude,
    user_day.slongitude AS start_longitude,
    user_day.clatitude AS closed_latitude,
    user_day.clongitude AS closed_longitude,

    -- Start System Decision Logic
    CASE
        WHEN TIME(user_day.ustart) BETWEEN '10:00:00' AND '10:14:59' THEN 'Good Start'
        WHEN TIME(user_day.ustart) BETWEEN '10:15:00' AND '10:29:59' THEN 'Not Good Start'
        WHEN TIME(user_day.ustart) BETWEEN '10:30:00' AND '10:59:59' THEN 'Bad Start'
        WHEN TIME(user_day.ustart) BETWEEN '11:00:00' AND '11:29:59' THEN 'Very Bad Start'
        WHEN TIME(user_day.ustart) BETWEEN '11:30:00' AND '23:59:59' THEN 'Very Very Bad Start'
        ELSE 'Start time out of range'
    END AS start_system_decision,

    -- Closed System Decision Logic
    CASE
        WHEN TIME(user_day.uclose) BETWEEN '18:30:00' AND '23:45:00' THEN 'Good Closed'
        WHEN TIME(user_day.uclose) BETWEEN '10:00:00' AND '13:30:00' THEN 'Bad Closed'
        
        WHEN TIME(user_day.uclose) BETWEEN '13:30:00' AND '18:30:00' THEN 'Bad Closed'
        ELSE NULL
    END AS closed_system_decision,

    -- Day Status Message
    CASE
        WHEN user_day.uclose IS NULL THEN 'User not closed their day'
        WHEN DATE(user_day.ustart) = DATE(user_day.uclose) THEN 'Closed on same day by user'
        ELSE 'Not closed on same day by user'
    END AS day_status_message,

    -- Time Difference (formatted as days, hours, minutes)
    CASE 
        WHEN user_day.uclose IS NOT NULL THEN
            CONCAT(
                FLOOR(TIMESTAMPDIFF(MINUTE, user_day.ustart, user_day.uclose) / 1440), ' days ',
                FLOOR(MOD(TIMESTAMPDIFF(MINUTE, user_day.ustart, user_day.uclose), 1440) / 60), ' hours ',
                MOD(TIMESTAMPDIFF(MINUTE, user_day.ustart, user_day.uclose), 60), ' minutes'
            )
        ELSE 'N/A'
    END AS time_diffrerent

FROM
    user_day
LEFT JOIN user_details u1 ON u1.user_id = user_day.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN userworkfrom ON userworkfrom.ID = user_day.wffo
WHERE
    u1.status = 'active'
    $text
    AND YEAR(user_day.ustart) = '$cfyear'");

    return $query->result();
      
    }




    // Start to Check Company contact Logic
public function GetAllCompaniesThatHasPrimaryContactDetails($uid){

    $udata = $this->Menu_model->get_userbyid($uid);
    $utype = $udata[0]->type_id;
    if($utype == 3 || $utype == 13 || $utype == 4){
        $text = "AND u1.user_id  = '$uid'";
    }elseif($utype == 2){
        $text = "AND u1.admin_id  = '$uid'";
    }else{
        $text = '';
    }

    $query=$this->db->query("SELECT u1.name AS user_name, u1.user_id AS tar_user_id, cm.compname, cm.id AS cid, ic.id AS init_id, ic.mainbd, ic.clm_id, ic.apst, ic.created_at, ic.is_admin_approved, ic.apr_by, ic.apr_date, (SELECT COUNT(*) FROM company_contact_master ccm WHERE ccm.company_id = cm.id) AS contact_count FROM init_call ic LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd LEFT JOIN company_master cm ON cm.id = ic.cmpid_id WHERE ic.mainbd IS NOT NULL AND cm.id IS NOT NULL $text AND EXISTS( SELECT 1 FROM company_contact_master ccm_inner WHERE ccm_inner.company_id = cm.id AND ccm_inner.type = 'primary')");

    return $query->result();
}
// Close  to Check Company contact Logic





public function TodaysStatusWisePlannerAnalysisData($uid,$tdate,$filter_types){

    $utype = $this->Menu_model->get_userbyid($uid);
    $utype = $utype[0]->type_id;
    if($utype == 13){
        $text = " u1.aadmin = '$uid'";
    }elseif($utype == 4){
        $text = " u1.pst_co = '$uid'";
    }elseif($utype == 15){
        $text = " u1.sales_co = '$uid'";
    }elseif($utype == 3){
        $text = " u1.user_id = '$uid'";
    }else{
        $text = " u1.admin_id = '$uid'";
    }

    $cfyear = date("Y");


    if($filter_types == 'CurrentDate'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate' AND";
    }else if($filter_types == 'BETWEEN7Days'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) BETWEEN DATE_SUB('$tdate', INTERVAL 7 DAY) AND DATE_SUB('$tdate', INTERVAL 1 DAY) AND";
    }else if($filter_types == 'BETWEEN1Month'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) 
        BETWEEN DATE_SUB('$tdate', INTERVAL 1 MONTH) AND DATE_SUB('$tdate', INTERVAL 1 DAY) AND";
    }else if($filter_types == 'BETWEEN3Month'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE)
         BETWEEN DATE_SUB('$tdate', INTERVAL 3 MONTH) AND DATE_SUB('$tdate', INTERVAL 1 DAY) AND";
    }else if ($filter_types == 'BETWEEN6Month') {
        $datfilterquery = "cast(tblcallevents.appointmentdatetime as DATE) BETWEEN '2024-06-01' and '$tdate' AND";
    }
    else if ($filter_types == 'BETWEEN12Month') {

        $year = date('Y', strtotime($tdate));
        $startDate = "$year-01-01";
        $endDate = "$year-12-31";
    
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) 
            BETWEEN '$startDate' AND '$endDate' AND";
    }
    

    $query = $this->db->query("SELECT
    u1.user_id,
    u1.name AS task_user_name,
    ut.name AS user_types,
    s.name AS status_name,
    action.name as task_name,
    DATE(tblcallevents.appointmentdatetime) AS task_date,

    COUNT(tblcallevents.id) AS total_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' THEN tblcallevents.id END) AS task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' THEN tblcallevents.id END) AS task_complete,

    COUNT(CASE WHEN tblcallevents.approved_status = '0' THEN tblcallevents.id END) AS approved_pending,
    COUNT(CASE WHEN tblcallevents.approved_status = '1' THEN tblcallevents.id END) AS approved_done,

    COUNT(CASE WHEN tblcallevents.autotask = '1' THEN tblcallevents.id END) AS total_auto_task_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_complete,

    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' THEN tblcallevents.id END) AS total_reject,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '' THEN tblcallevents.id END) AS pending_for_assign_after_reject_task,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1' THEN tblcallevents.id END) AS admin_create_request_for_self_assign,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2' THEN tblcallevents.id END) AS task_assignd_by_admin_after_reject,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3' THEN tblcallevents.id END) AS task_assignd_by_user_after_reject,

    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes' THEN tblcallevents.id END) AS action_yes_purpose_yes,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no' THEN tblcallevents.id END) AS action_yes_purpose_no,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no' THEN tblcallevents.id END) AS action_no_purpose_no,
    COUNT(
    CASE WHEN tblcallevents.actiontype_id = 1 THEN actiontype_id
END
) call_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 2 THEN actiontype_id
END
) email_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 3 THEN actiontype_id
END
) scheduled_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 4 THEN actiontype_id
END
) barg_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 5 THEN actiontype_id
END
) whatsapp_activity,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 6 THEN actiontype_id
END
) write_mom,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 7 THEN actiontype_id
END
) write_proposal,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 10 THEN actiontype_id
END
) research_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 12 THEN actiontype_id
END
) documentation_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 13 THEN actiontype_id
END
) social_networking_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 14 THEN actiontype_id
END
) social_activity_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 15 THEN actiontype_id
END
) annual_review_target_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 17 THEN actiontype_id
END
) join_meeting_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 18 THEN actiontype_id
END
) mom_check_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 19 THEN actiontype_id
END
) create_bd_request_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 20 THEN actiontype_id
END
) submit_handover_task,
COUNT(
    CASE WHEN tblcallevents.actiontype_id = 21 THEN actiontype_id
END
) praposal_check_task

FROM tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status s ON s.id = tblcallevents.status_id
LEFT JOIN action ON action.id = tblcallevents.actiontype_id

WHERE
    u1.status = 'active' AND
    
   $datfilterquery
    $text

GROUP BY
    u1.user_id,
    u1.name,
    ut.name,
    s.name,
    action.name,
    DATE(tblcallevents.appointmentdatetime)");
  return $query->result();
}








public function TodaysCategoryWisePlannerAnalysisData($uid,$tdate,$filter_types){

    $utype = $this->Menu_model->get_userbyid($uid);
    $utype = $utype[0]->type_id;
    if($utype == 13){
        $text = "AND u1.aadmin = '$uid'";
    }elseif($utype == 4){
        $text = "AND u1.pst_co = '$uid'";
    }elseif($utype == 15){
        $text = "AND u1.sales_co = '$uid'";
    }elseif($utype == 3){
        $text = "AND u1.user_id = '$uid'";
    }else{
        $text = "AND u1.admin_id = '$uid'";
    }

    $cfyear = date("Y");


    if($filter_types == 'CurrentDate'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate' AND";
    }else if($filter_types == 'BETWEEN7Days'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) BETWEEN DATE_SUB('$tdate', INTERVAL 7 DAY) AND DATE_SUB('$tdate', INTERVAL 1 DAY) AND";
    }else if($filter_types == 'BETWEEN1Month'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) 
        BETWEEN DATE_SUB('$tdate', INTERVAL 1 MONTH) AND DATE_SUB('$tdate', INTERVAL 1 DAY) AND";
    }else if($filter_types == 'BETWEEN3Month'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE)
         BETWEEN DATE_SUB('$tdate', INTERVAL 3 MONTH) AND DATE_SUB('$tdate', INTERVAL 1 DAY) AND";
    }else if ($filter_types == 'BETWEEN6Month') {
        $datfilterquery = "cast(tblcallevents.appointmentdatetime as DATE) BETWEEN '2024-06-01' and '$tdate' AND";
    }
    else if ($filter_types == 'BETWEEN12Month') {

        $year = date('Y', strtotime($tdate));
        $startDate = "$year-01-01";
        $endDate = "$year-12-31";
    
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) 
            BETWEEN '$startDate' AND '$endDate' AND";
    }
    
    $query = $this->db->query("SELECT
    u1.user_id,
    u1.name AS task_user_name,
    ut.name AS user_types,
    s.name AS status_name,
    action.name AS task_name,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.topspender,
    DATE(tblcallevents.appointmentdatetime) AS task_date,
    COUNT(tblcallevents.id) AS total_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' THEN tblcallevents.id END) AS task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' THEN tblcallevents.id END) AS task_complete,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' THEN tblcallevents.id END) AS approved_pending,
    COUNT(CASE WHEN tblcallevents.approved_status = '1' THEN tblcallevents.id END) AS approved_done,
    COUNT(CASE WHEN tblcallevents.autotask = '1' THEN tblcallevents.id END) AS total_auto_task_count,
    COUNT(CASE WHEN tblcallevents.nextCFID = '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_pending,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.autotask = '1' THEN tblcallevents.id END) AS auto_task_complete,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' THEN tblcallevents.id END) AS total_reject,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '' THEN tblcallevents.id END) AS pending_for_assign_after_reject_task,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1' THEN tblcallevents.id END) AS admin_create_request_for_self_assign,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2' THEN tblcallevents.id END) AS task_assignd_by_admin_after_reject,
    COUNT(CASE WHEN tblcallevents.approved_status = '0' AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3' THEN tblcallevents.id END) AS task_assignd_by_user_after_reject,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'yes' THEN tblcallevents.id END) AS action_yes_purpose_yes,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.actontaken = 'yes' AND tblcallevents.purpose_achieved = 'no' THEN tblcallevents.id END) AS action_yes_purpose_no,
    COUNT(CASE WHEN tblcallevents.nextCFID != '0' AND tblcallevents.actontaken = 'no' AND tblcallevents.purpose_achieved = 'no' THEN tblcallevents.id END) AS action_no_purpose_no,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 1 THEN actiontype_id END) call_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 2 THEN actiontype_id END) email_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 3 THEN actiontype_id END) scheduled_meeting_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 4 THEN actiontype_id END) barg_meeting_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 5 THEN actiontype_id END) whatsapp_activity,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 6 THEN actiontype_id END) write_mom,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 7 THEN actiontype_id END) write_proposal,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 10 THEN actiontype_id END) research_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 12 THEN actiontype_id END) documentation_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 13 THEN actiontype_id END) social_networking_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 14 THEN actiontype_id END) social_activity_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 15 THEN actiontype_id END) annual_review_target_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 17 THEN actiontype_id END) join_meeting_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 18 THEN actiontype_id END) mom_check_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 19 THEN actiontype_id END) create_bd_request_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 20 THEN actiontype_id END) submit_handover_task,
    COUNT(CASE WHEN tblcallevents.actiontype_id = 21 THEN actiontype_id END) praposal_check_task
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status s ON s.id = tblcallevents.status_id
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
WHERE
    u1.status = 'active'
    $text AND
    $datfilterquery
    
    (init_call.upsell_client = 'yes' 
    OR init_call.focus_funnel = 'yes' 
    OR init_call.keycompany = 'yes' 
    OR init_call.pkclient = 'yes' 
    OR init_call.priorityc = 'yes' 
    OR init_call.topspender = 'yes')
GROUP BY
    u1.user_id,
    u1.name,
    ut.name,
    s.name,
    action.name,
    DATE(tblcallevents.appointmentdatetime),
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.topspender");
  return $query->result();
}



public function TodaysCompanyNameWisePlannerAnalysisData($uid,$tdate,$filter_types){

    $utype = $this->Menu_model->get_userbyid($uid);
    $utype = $utype[0]->type_id;
    if($utype == 13){
        $text = "AND u1.aadmin = '$uid'";
    }elseif($utype == 4){
        $text = "AND u1.pst_co = '$uid'";
    }elseif($utype == 15){
        $text = "AND u1.sales_co = '$uid'";
    }elseif($utype == 3){
        $text = "AND u1.user_id = '$uid'";
    }else{
        $text = "AND u1.admin_id = '$uid'";
    }

    $cfyear = date("Y");


    if($filter_types == 'CurrentDate'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) = '$tdate'";
    }
    
    else if($filter_types == 'BETWEEN7Days'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) BETWEEN DATE_SUB('$tdate', INTERVAL 7 DAY) AND DATE_SUB('$tdate', INTERVAL 1 DAY) ";
    }
    
    else if($filter_types == 'BETWEEN1Month'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) 
        BETWEEN DATE_SUB('$tdate', INTERVAL 1 MONTH) AND DATE_SUB('$tdate', INTERVAL 1 DAY) ";
    }
    
    else if($filter_types == 'BETWEEN3Month'){
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE)
         BETWEEN DATE_SUB('$tdate', INTERVAL 3 MONTH) AND DATE_SUB('$tdate', INTERVAL 1 DAY) ";
    }
    
    
    else if ($filter_types == 'BETWEEN6Month') {
        $datfilterquery = "cast(tblcallevents.appointmentdatetime as DATE) BETWEEN '2024-06-01' and '$tdate' ";
    }

    else if ($filter_types == 'BETWEEN12Month') {

        $year = date('Y', strtotime($tdate));
        $startDate = "$year-01-01";
        $endDate = "$year-12-31";
    
        $datfilterquery = " CAST(tblcallevents.appointmentdatetime AS DATE) 
            BETWEEN '$startDate' AND '$endDate' ";
    }
    
    $query = $this->db->query("SELECT
    u1.user_id,
    u1.name AS task_user_name,
    ut.name AS user_types,
    s.name AS status_name,
    action.name AS task_name,
    company_master.compname as company_name,
    tblcallevents.appointmentdatetime AS task_date_time,
    tblcallevents.actontaken AS action_taken,
    tblcallevents.purpose_achieved AS purpose_achieved,
    CASE WHEN  tblcallevents.nextCFID !=0 THEN 'Complete' ELSE 'Pending' END AS task_status
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN status s ON s.id = tblcallevents.status_id
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
WHERE
    u1.status = 'active'
    $text AND
    $datfilterquery
    ");
  return $query->result();
}


public function GetTodaysPlanedTaskTypeBYRoles($uid,$planner_date,$admin_filter_type,$rm_filter){
  

    $utype      = $this->Menu_model->get_userbyid($uid);
    $user_type_id      = $utype[0]->type_id;
    if($user_type_id == 3){
        $userfilter = "AND u1.user_id = '$uid'";
        $userfilter1 = "AND u2.user_id = '$uid'";
    }elseif($user_type_id == 13){
        $userfilter = "AND (u1.aadmin = '$uid') AND u1.type_id IN (3)";
        $userfilter1 = "AND (u2.aadmin = '$uid' ) AND u2.type_id IN (3)";
    }else if($user_type_id == 4){
        $userfilter = "AND u1.pst_co = '$uid' AND u1.type_id IN (3,13)";
        $userfilter1 = "AND u2.pst_co = '$uid' AND u2.type_id IN (3,13)";
    }else if($user_type_id == 15){
        $userfilter = "AND u1.sales_co = '$uid' AND u1.type_id IN (3,13,4)";
        $userfilter1 = "AND u2.sales_co = '$uid' AND u2.type_id IN (3,13,4)";
    }elseif($user_type_id == 2){
        $userfilter = "AND u1.admin_id = '$uid'";
        $userfilter1 = "AND u2.admin_id = '$uid'";
    }elseif($user_type_id == 19){
        $userfilter = "AND u1.ash_nae_co = '$uid'";
        $userfilter1 = "AND u2.ash_nae_co = '$uid'";
    }elseif($user_type_id == 20){
        $userfilter = "AND u1.ash_w_co = '$uid'";
        $userfilter1 = "AND u2.ash_w_co = '$uid'";
    }elseif($user_type_id == 21){
        $userfilter = "AND u1.ash_s_co = '$uid'";
        $userfilter1 = "AND u2.ash_s_co = '$uid'";
    }elseif($user_type_id == 22){
        $userfilter = "AND u1.rm_east_co = '$uid'";
        $userfilter1 = "AND u2.rm_east_co = '$uid'";
    }elseif($user_type_id == 23){
        $userfilter = "AND u1.rm_north_co = '$uid'";
        $userfilter1 = "AND u2.rm_north_co = '$uid'";
    }elseif($user_type_id == 24){
        $userfilter = "AND u1.acm_co = '$uid' AND u1.type_id IN (3)";
        $userfilter1 = "AND u2.acm_co = '$uid' AND u2.type_id IN (3)";
    } else{
        $userfilter = "AND u1.admin_id = '$uid'";
        $userfilter1 = "AND u2.admin_id = '$uid'";
    }

    if($admin_filter_type == 'all'){
        $userfilter = "";
        $userfilter1 = "";
    }

    // echo  $admin_filter_type;
    // echo  $userfilter;
    // echo  $userfilter1;
    // die;

//     $query=$this->db->query("SELECT
//     u1.name,
//     u1.user_id AS planner_user_id,
//     ta.id AS action_id,
//     ta.name as tasktype,
//     ta.yest,
//     COUNT(tblcallevents.id) AS task_count,
//     (
//         SELECT COUNT(*)
//         FROM tblcallevents tce
//         WHERE tce.actiontype_id != ''
//           AND tce.plan = 1
//           AND DATE(tce.appointmentdatetime) = '2025-04-22'
//           AND tce.user_id = u1.user_id
//           AND tce.autotask = 0
//           AND tce.appointmentdatetime != '0000-00-00 00:00:00'
//     ) AS total_task_count,

//     CONCAT(
//         FLOOR((
//             SELECT SUM(ta_inner.yest)
//             FROM tblcallevents tce_inner
//             JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
//             WHERE tce_inner.actiontype_id != ''
//               AND tce_inner.plan = 1
//               AND DATE(tce_inner.appointmentdatetime) = '2025-04-22'
//               AND tce_inner.user_id = u1.user_id
//               AND tce_inner.autotask = 0
//               AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
//         ) / 60),
//         ' hours ',
//         (
//             SELECT SUM(ta_inner.yest)
//             FROM tblcallevents tce_inner
//             JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
//             WHERE tce_inner.actiontype_id != ''
//               AND tce_inner.plan = 1
//               AND DATE(tce_inner.appointmentdatetime) = '2025-04-22'
//               AND tce_inner.user_id = u1.user_id
//               AND tce_inner.autotask = 0
//               AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
//         ) % 60,
//         ' minutes'
//     ) AS total_plan_task_time

// FROM
//     tblcallevents
// LEFT JOIN
//     action ta ON ta.id = tblcallevents.actiontype_id
// LEFT JOIN
//     user_details u1 ON u1.user_id = tblcallevents.user_id
// WHERE
//     tblcallevents.plan = 1
//     AND DATE(tblcallevents.appointmentdatetime) = '2025-04-22'
//     AND tblcallevents.appointmentdatetime != '0000-00-00 00:00:00'
//     AND tblcallevents.autotask = 0
//     $usermapping
// GROUP BY
//     u1.user_id, ta.id
// ORDER BY
//     task_count DESC");



    $query=$this->db->query("SELECT
    u1.name,
    u1.user_id AS planner_user_id,
    ta.id AS action_id,
    ta.name AS tasktype,
    COALESCE(ut1.name) AS user_type_name,
    ta.yest,
    uz.name as zone_name, -- New column for zone name
    COUNT(tblcallevents.id) AS task_count,
    (
        SELECT COUNT(*)
        FROM tblcallevents tce
        WHERE tce.actiontype_id != ''
          AND tce.plan = 1
          AND DATE(tce.appointmentdatetime) = '$planner_date'
          AND tce.user_id = u1.user_id
          AND tce.autotask = 0
          AND tce.appointmentdatetime != '0000-00-00 00:00:00'
    ) + (
        SELECT COUNT(*)
        FROM allreview ar
        WHERE ar.uid = u1.user_id
          AND DATE(ar.plant) = '$planner_date'
    ) AS total_task_count,
    CONCAT(
        FLOOR((
            SELECT SUM(ta_inner.yest)
            FROM tblcallevents tce_inner
            JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
            WHERE tce_inner.actiontype_id != ''
              AND tce_inner.plan = 1
              AND DATE(tce_inner.appointmentdatetime) = '$planner_date'
              AND tce_inner.user_id = u1.user_id
              AND tce_inner.autotask = 0
              AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
        ) / 60),
        ' hours ',
        (
            SELECT SUM(ta_inner.yest)
            FROM tblcallevents tce_inner
            JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
            WHERE tce_inner.actiontype_id != ''
              AND tce_inner.plan = 1
              AND DATE(tce_inner.appointmentdatetime) = '$planner_date'
              AND tce_inner.user_id = u1.user_id
              AND tce_inner.autotask = 0
              AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
        ) % 60,
        ' minutes'
    ) AS total_plan_task_time,
    NULL AS total_review_plan_time
FROM tblcallevents
LEFT JOIN action ta ON ta.id = tblcallevents.actiontype_id
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
LEFT JOIN user_type ut1 ON ut1.id = u1.type_id
LEFT JOIN user_zone uz ON uz.id = u1.zone_id -- New join
WHERE tblcallevents.plan = 1
  AND DATE(tblcallevents.appointmentdatetime) = '$planner_date'
  AND tblcallevents.appointmentdatetime != '0000-00-00 00:00:00'
  AND tblcallevents.autotask = 0
  $userfilter
  -- AND u1.type_id IN (3,13,4,15)
GROUP BY u1.user_id, ta.id, u1.name, ut1.name, ta.name, ta.yest, uz.name

UNION ALL

SELECT
    u2.name,
    u2.user_id AS planner_user_id,
    ar.id AS action_id,
    CONCAT(ar.reviewtype, ' Review') AS tasktype,
    NULL AS user_type_name,
    NULL AS yest,
    uz.name as zone_name, -- New column for zone name
    COUNT(ar.id) AS task_count,
    (
        SELECT COUNT(*)
        FROM tblcallevents tce
        WHERE tce.actiontype_id != ''
          AND tce.plan = 1
          AND DATE(tce.appointmentdatetime) = '$planner_date'
          AND tce.user_id = u2.user_id
          AND tce.autotask = 0
          AND tce.appointmentdatetime != '0000-00-00 00:00:00'
    ) + (
        SELECT COUNT(*)
        FROM allreview ar_inner
        WHERE ar_inner.uid = u2.user_id
          AND DATE(ar_inner.plant) = '$planner_date'
    ) AS total_task_count,
    NULL AS total_plan_task_time,
    CASE
        WHEN SUM(
            CASE
                WHEN ar.plant IS NOT NULL AND ar.review_close_time IS NOT NULL
                THEN TIME_TO_SEC(TIMEDIFF(ar.review_close_time, ar.plant)) / 60
                ELSE 0
            END
        ) < 0 THEN '0 hours and 0 minutes'
        ELSE CONCAT(
            FLOOR(SUM(
                CASE
                    WHEN ar.plant IS NOT NULL AND ar.review_close_time IS NOT NULL
                    THEN TIME_TO_SEC(TIMEDIFF(ar.review_close_time, ar.plant)) / 60
                    ELSE 0
                END
            ) / 60),
            ' hours ',
            FLOOR(SUM(
                CASE
                    WHEN ar.plant IS NOT NULL AND ar.review_close_time IS NOT NULL
                    THEN TIME_TO_SEC(TIMEDIFF(ar.review_close_time, ar.plant)) / 60
                    ELSE 0
                END
            ) % 60),
            ' minutes'
        )
    END AS total_review_plan_time
FROM allreview ar
JOIN user_details u2 ON u2.user_id = ar.uid
LEFT JOIN user_zone uz ON uz.id = u2.zone_id -- New join
WHERE DATE(ar.plant) = '$planner_date'
   $userfilter1
  -- AND u2.type_id IN (3,13,4,15)
GROUP BY u2.user_id, ar.id, uz.name
ORDER BY task_count DESC");

    return $query->result();
}



public function MeetingDetailsByRolesUidInSaleGraph($userid,$sdate,$edate,$admin_filter_type,$rm_filter){

    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 2){
        $text = " AND user_details.admin_id = $userid";
    }else if($type_id == 3){
        $text = " AND user_details.user_id = $userid";
    }else if($type_id == 4){
        $text = " AND user_details.pst_co = $userid";
    }else if($type_id == 13){
        $text = " AND user_details.aadmin = $userid";
    }else if($type_id == 15){
        $text = " AND user_details.sales_co = $userid";
    }elseif($type_id == 19){
        $text = " AND user_details.ash_nae_co = '$userid'";
    }else if($type_id == 20){
        $text = " AND user_details.ash_w_co='$userid'";
    }else if($type_id == 21){
        $text = " AND user_details.ash_s_co='$userid'";
    }else if($type_id == 22){
        $text = " AND user_details.rm_east_co='$userid'";
    }else if($type_id == 23){
        $text = " AND user_details.rm_north_co='$userid'";
    }else if($type_id == 24){
        $text = " AND user_details.acm_co='$userid'";
    }else{
        $text = " AND user_details.admin_id = $userid";
    }


    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];

    // $start_financial_date = '2026-04-01';

    if($admin_filter_type == 'all'){
        $text = "";
        $userfilter1 = "";
    }



    $query= $this->db->query("SELECT
    user_details.user_id,
	user_details.name,
    user_type.name as user_roles,
    user_zone.name as user_zone,
    COUNT(tbcl.id) AS total_plan_meetings,
    COUNT(CASE WHEN tbcl.nextCFID != 0 THEN 1 END) AS complete_meetings,
    COUNT(CASE WHEN tbcl.nextCFID = 0 THEN 1 END) AS not_complete_meetings,

    COUNT(CASE WHEN tbcl.actiontype_id = 3 THEN 1 END) AS total_sheduled_meetings,
    COUNT(CASE WHEN tbcl.actiontype_id = 3 AND tbcl.nextCFID != 0 THEN 1 END) AS total_complete_sheduled_meetings,
    COUNT(CASE WHEN tbcl.actiontype_id = 3 AND tbcl.nextCFID = 0 THEN 1 END) AS not_complete_sheduled_meetings,

    COUNT(CASE WHEN tbcl.actiontype_id = 4 THEN 1 END) AS total_barg_meetings,
    COUNT(CASE WHEN tbcl.actiontype_id = 4 AND tbcl.nextCFID != 0 THEN 1 END) AS total_complete_barg_meetings,
    COUNT(CASE WHEN tbcl.actiontype_id = 4 AND tbcl.nextCFID = 0 THEN 1 END) AS not_complete_barg_meetings,

    COUNT(CASE WHEN tbcl.actiontype_id = 17 THEN 1 END) AS total_join_meetings,
    COUNT(CASE WHEN tbcl.actiontype_id = 17 AND tbcl.nextCFID != 0 THEN 1 END) AS total_complete_join_meetings,
    COUNT(CASE WHEN tbcl.actiontype_id = 17 AND tbcl.nextCFID = 0 THEN 1 END) AS not_complete_join_meetings,

    COUNT(CASE WHEN (tbcl.mtype = 'RP' OR tbcl.mtype = 'RPClose') AND tbcl.nextCFID != 0 THEN 1 END) AS total_RP_meetings,
    COUNT(CASE WHEN (tbcl.mtype = 'NO RP' OR tbcl.mtype = 'Close') AND tbcl.nextCFID != 0 THEN 1 END) AS total_NO_RP_meetings,
    COUNT(CASE WHEN tbcl.mtype = 'Only Got Detail' AND tbcl.nextCFID != 0 THEN 1 END) AS total_Only_Got_details_meetings,

    COUNT(CASE WHEN (tbcl.mtype = 'RP' OR tbcl.mtype = 'RPClose') AND tbcl.actiontype_id = 3 AND tbcl.nextCFID != 0 THEN 1 END) AS total_Sheduled_RP_meetings,
    COUNT(CASE WHEN (tbcl.mtype = 'NO RP' OR tbcl.mtype = 'Close') AND tbcl.actiontype_id = 3 AND tbcl.nextCFID != 0 THEN 1 END) AS total_Sheduled_NO_RP_meetings,
    COUNT(CASE WHEN tbcl.mtype = 'Only Got Detail' AND tbcl.actiontype_id = 3 AND tbcl.nextCFID != 0 THEN 1 END) AS total_Sheduled_Only_Got_details_meetings,

    COUNT(CASE WHEN (tbcl.mtype = 'RP' OR tbcl.mtype = 'RPClose') AND tbcl.actiontype_id = 4 AND tbcl.nextCFID != 0 THEN 1 END) AS total_Barge_RP_meetings,
    COUNT(CASE WHEN (tbcl.mtype = 'NO RP' OR tbcl.mtype = 'Close') AND tbcl.actiontype_id = 4 AND tbcl.nextCFID != 0 THEN 1 END) AS total_Barge_NO_RP_meetings,
    COUNT(CASE WHEN tbcl.mtype = 'Only Got Detail' AND tbcl.actiontype_id = 4 THEN 1 END) AS total_Barge_Only_Got_details_meetings,

    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.potential = 'yes' THEN 1 END) AS total_potential_meetings,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.potential = 'no' THEN 1 END) AS total_non_potential_meetings,

    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.topspender = 'yes' THEN 1 END) AS total_topspender_meetings,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.upsell_client = 'yes' THEN 1 END) AS total_upsell_client_meetings,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.focus_funnel = 'yes' THEN 1 END) AS total_focus_funnel_meetings,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.keycompany = 'yes' THEN 1 END) AS total_keycompany_meetings,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.pkclient = 'yes' THEN 1 END) AS total_pkclient_meetings,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND init_call.priorityc = 'yes' THEN 1 END) AS total_priorityc_meetings,

    -- COUNT(
    --     CASE 
    --         WHEN tbcl.nextCFID != 0 
    --         AND (
    --             SELECT COUNT(*) 
    --             FROM barginmeeting bm 
    --             WHERE bm.cid = init_call.cmpid_id AND CAST(storedt as Date) >= $start_financial_date
    --         ) = 1
    --         THEN 1 
    --     END
    -- ) AS total_fresh_meetings,

    COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
              AND DATE(barginmeeting.storedt) >= '$start_financial_date'
              AND (
                  SELECT COUNT(*) 
                  FROM barginmeeting bm_sub 
                  LEFT JOIN tblcallevents fremeetingtask on fremeetingtask.id = bm_sub.tid
                  WHERE bm_sub.cid = barginmeeting.cid 
                    AND DATE(bm_sub.storedt) >= '$start_financial_date' AND (fremeetingtask.mtype = 'RP' || fremeetingtask.mtype = 'RPClose')
              ) >= 1
            THEN 1
        END
    ) AS total_fresh_meetings,

    -- COUNT(
    --     CASE 
    --         WHEN tbcl.nextCFID != 0 
    --         AND (
    --             SELECT COUNT(*) 
    --             FROM barginmeeting bm 
    --             WHERE bm.cid = init_call.cmpid_id AND CAST(storedt as Date) >= $start_financial_date
    --         ) > 1
    --         THEN 1 
    --     END
    -- ) AS total_re_meetings,

   COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
              AND DATE(barginmeeting.storedt) >= '$start_financial_date'
              AND (
                  SELECT COUNT(*) 
                  FROM barginmeeting bm_sub 
                  LEFT JOIN tblcallevents remeetingtask on remeetingtask.id = bm_sub.tid
                  WHERE bm_sub.cid = barginmeeting.cid 
                    AND DATE(bm_sub.storedt) >= '$start_financial_date' AND (remeetingtask.mtype = 'RP' || remeetingtask.mtype = 'RPClose')
              ) > 1
            THEN 1
        END
    ) AS total_re_meetings,
        COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
             )
            THEN 1 
        END
    ) AS total_write_mom_rp_meetings,

    COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
             AND NOT EXISTS (
                 SELECT 1 
                 FROM tblcallevents tblc_not_mom
                 WHERE tblc_not_mom.aftertask = tbcl.id AND actiontype_id = 6 
                 LIMIT 1
             )
            THEN 1 
        END
    ) AS total_pending_for_write_mom_rp_meeting,

        COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
             )
            THEN 1 
        END
    ) AS total_mom_data,
      COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status = 'Approved'
             )
            THEN 1 
        END
    ) AS total_approved_after_check_mom_data,
      COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status = 'Reject'
             )
            THEN 1 
        END
    ) AS total_reject_after_check_mom_data,
    
         COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'NO RP' || tbcl.mtype = 'NO RP')
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status = 'NO RP'
             )
            THEN 1 
        END
    ) AS total_NO_RP_after_check_mom_data,
    
      COUNT(
        CASE 
            WHEN tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status IS NULL
             )
            THEN 1 
        END
    ) AS total_pending_for_check_mom_data

  

FROM
    tblcallevents AS tbcl
LEFT JOIN barginmeeting ON barginmeeting.tid = tbcl.id
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN user_type ON user_type.id = user_details.type_id
LEFT JOIN user_zone ON user_zone.id = user_details.zone_id
WHERE
 
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    AND tbcl.actiontype_id IN (3,4,17) AND user_details.status ='active' AND tbcl.approved_status = 1
    AND (
    (user_details.type_id = 3 AND init_call.mainbd = user_details.user_id)
    OR
    (user_details.type_id = 13 AND (init_call.mainbd = user_details.user_id OR init_call.clm_id = user_details.user_id))
    OR
    (user_details.type_id = 4 AND init_call.apst = user_details.user_id)
    )
	 $text 
    GROUP BY user_details.user_id");

    return $query->result(); 
}




public function TotalMeetingDetailsDatasSalesGraph($userid,$sdate,$edate,$mtypes,$uid,$admin_filter_type,$rm_filter){

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 2){
        $text = " AND user_details.admin_id = $userid";
    }else if($type_id == 3){
        $text = " AND user_details.user_id = $userid";
    }else if($type_id == 4){
        $text = " AND user_details.pst_co = $userid";
    }else if($type_id == 13){
        $text = " AND user_details.aadmin = $userid";
    }else if($type_id == 15){
        $text = " AND user_details.sales_co = $userid";
    }elseif($type_id == 19){
        $text = " AND user_details.ash_nae_co = '$userid'";
    }else if($type_id == 20){
        $text = " AND user_details.ash_w_co='$userid'";
    }else if($type_id == 21){
        $text = " AND user_details.ash_s_co='$userid'";
    }else if($type_id == 22){
        $text = " AND user_details.rm_east_co='$userid'";
    }else if($type_id == 23){
        $text = " AND user_details.rm_north_co='$userid'";
    }else if($type_id == 24){
        $text = " AND user_details.acm_co='$userid'";
    }else{
        $text = " AND user_details.admin_id = $userid";
    }
    

     if($admin_filter_type == 'all'){
        $text = "";
        $userfilter1 = "";
    }

    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];

    // $start_financial_date = '2026-04-01';

    $taskactionFilter = "AND tbcl.actiontype_id IN (3,4,17)";

    if($mtypes == 'total_plan_meetings'){
        $filter = '';
    }else if($mtypes == 'complete_meetings'){
        $filter = ' AND tbcl.nextCFID != 0';
    }else if($mtypes == 'not_complete_meetings'){
        $filter = ' AND tbcl.nextCFID = 0';
    }else if($mtypes == 'total_RP_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND (tbcl.mtype = "RP" || tbcl.mtype = "RPClose")';
    }else if($mtypes == 'total_NO_RP_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND tbcl.mtype = "NO RP"';
    }else if($mtypes == 'total_Only_Got_details_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND tbcl.mtype = "Only Got Detail"';
    }else if($mtypes == 'total_fresh_meetings'){
        $filter = " AND tbcl.nextCFID != 0  AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
              AND DATE(barginmeeting.storedt) >= '$start_financial_date'
              AND (
                  SELECT COUNT(*) 
                  FROM barginmeeting bm_sub 
                  LEFT JOIN tblcallevents fremeetingtask on fremeetingtask.id = bm_sub.tid
                  WHERE bm_sub.cid = barginmeeting.cid 
                    AND DATE(bm_sub.storedt) >= '$start_financial_date' AND (fremeetingtask.mtype = 'RP' || fremeetingtask.mtype = 'RPClose')
              ) >=1";
    }else if($mtypes == 'total_re_meetings'){
        $filter = " AND tbcl.nextCFID != 0  AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
              AND DATE(barginmeeting.storedt) >= '$start_financial_date'
              AND (
                  SELECT COUNT(*) 
                  FROM barginmeeting bm_sub 
                  LEFT JOIN tblcallevents remeetingtask on remeetingtask.id = bm_sub.tid
                  WHERE bm_sub.cid = barginmeeting.cid 
                    AND DATE(bm_sub.storedt) >= '$start_financial_date' AND (remeetingtask.mtype = 'RP' || remeetingtask.mtype = 'RPClose')
              ) > 1";
    }else if($mtypes == 'total_scheduled_meetings'){
        $filter = '';
        $taskactionFilter = "AND tbcl.actiontype_id IN (3)";
    }else if($mtypes == 'total_complete_scheduled_meetings'){
        $filter = ' AND tbcl.nextCFID != 0';
        $taskactionFilter = "AND tbcl.actiontype_id IN (3)";
    }else if($mtypes == 'not_complete_scheduled_meetings'){
        $filter = ' AND tbcl.nextCFID = 0';
        $taskactionFilter = "AND tbcl.actiontype_id IN (3)";
    }else if($mtypes == 'total_Scheduled_RP_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND (tbcl.mtype = "RP" || tbcl.mtype = "RPClose")';
        $taskactionFilter = "AND tbcl.actiontype_id IN (3)";
    }else if($mtypes == 'total_Scheduled_NO_RP_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND tbcl.mtype = "NO RP"';
        $taskactionFilter = "AND tbcl.actiontype_id IN (3)";
    }else if($mtypes == 'total_Scheduled_Only_Got_details_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND tbcl.mtype = "Only Got Detail"';
        $taskactionFilter = "AND tbcl.actiontype_id IN (3)";
    }else if($mtypes == 'total_barg_meetings'){
        $filter = '';
        $taskactionFilter = "AND tbcl.actiontype_id IN (4)";
    }else if($mtypes == 'total_complete_barg_meetings'){
        $filter = ' AND tbcl.nextCFID != 0';
        $taskactionFilter = "AND tbcl.actiontype_id IN (4)";
    }else if($mtypes == 'not_complete_barg_meetings'){
        $filter = ' AND tbcl.nextCFID = 0';
        $taskactionFilter = "AND tbcl.actiontype_id IN (4)";
    }else if($mtypes == 'total_Barge_RP_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND (tbcl.mtype = "RP" || tbcl.mtype = "RPClose")';
        $taskactionFilter = "AND tbcl.actiontype_id IN (4)";
    }else if($mtypes == 'total_Barge_NO_RP_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND tbcl.mtype = "NO RP"';
        $taskactionFilter = "AND tbcl.actiontype_id IN (4)";
    }else if($mtypes == 'total_Barge_Only_Got_details_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND tbcl.mtype = "Only Got Detail"';
        $taskactionFilter = "AND tbcl.actiontype_id IN (4)";
    }else if($mtypes == 'total_potential_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.potential = "yes"';
    }else if($mtypes == 'total_non_potential_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.potential = "no"';
    }else if($mtypes == 'total_topspender_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.topspender = "yes"';
    }else if($mtypes == 'total_upsell_client_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.upsell_client = "yes"';
    }else if($mtypes == 'total_focus_funnel_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.focus_funnel = "yes"';
    }else if($mtypes == 'total_keycompany_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.keycompany = "yes"';
    }else if($mtypes == 'total_pkclient_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.pkclient = "yes"';
    }else if($mtypes == 'total_priorityc_meetings'){
        $filter = ' AND tbcl.nextCFID != 0 AND init_call.priorityc = "yes"';
    }else if ($mtypes == 'total_write_mom_rp_meetings') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
            AND (
                SELECT COUNT(*) 
                FROM tblcallevents tblc_momtid
                WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
            ) > 0";
    }else if ($mtypes == 'total_pending_for_write_mom_rp_meeting') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')  
            AND NOT EXISTS (
                 SELECT 1 
                 FROM tblcallevents tblc_not_mom
                 WHERE tblc_not_mom.aftertask = tbcl.id AND actiontype_id = 6 
                 LIMIT 1
             )";
    }else if ($mtypes == 'total_mom_data') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
            AND (
                SELECT COUNT(*) 
                FROM tblcallevents tblc_momtid
                WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
            ) > 0";
    }else if ($mtypes == 'total_approved_after_check_mom_data') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
                AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status = 'Approved'
             ) > 0";
    }else if ($mtypes == 'total_reject_after_check_mom_data') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
                AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status = 'Reject'
             ) > 0";
    }else if ($mtypes == 'total_NO_RP_after_check_mom_data') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.mtype = 'NO RP' || tbcl.mtype = 'NO RP')
                AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status = 'NO RP'
             ) > 0";
    }else if ($mtypes == 'total_pending_for_check_mom_data') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.mtype = 'RP' || tbcl.mtype = 'RPClose')
                AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents tblc_momtid
                 LEFT JOIN mom_data ON mom_data.tid = tblc_momtid.id
                 WHERE tbcl.id = tblc_momtid.aftertask AND tblc_momtid.actiontype_id = 6
                 AND mom_data.approved_status IS NULL
             ) > 0";
    }
    
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_type.name as user_roles,
     user_zone.name as user_zone,
     user_details.name as task_username,
     company_master.compname,
    --  company_master.country as company_country,
    --  company_master.state as company_state,
    --  company_master.city as company_city,
     company_master.id as cid,
     tbcl.fwd_date as task_orignal_date,
     tbcl.appointmentdatetime as task_perform_date,
     tbcl.meeting_type,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     barginmeeting.startm,
     barginmeeting.closem,
     barginmeeting.company_as,
     barginmeeting.company_descri,
     barginmeeting.status as meetings_status,
     barginmeeting.initphoto,
     barginmeeting.cphoto,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
    barginmeeting.initiateLat,
    barginmeeting.initiateLongi,
    barginmeeting.slatitude,
    barginmeeting.slongitude,
    barginmeeting.clatitude,
    barginmeeting.clongitude
FROM
    tblcallevents AS tbcl
LEFT JOIN barginmeeting ON barginmeeting.tid = tbcl.id
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN user_type ON user_type.id = user_details.type_id
LEFT JOIN user_zone ON user_zone.id = user_details.zone_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    AND tbcl.approved_status = 1 
    AND user_details.status ='active'
    AND (
    (user_details.type_id = 3 AND init_call.mainbd = user_details.user_id)
    OR
    (user_details.type_id = 13 AND (init_call.mainbd = user_details.user_id OR init_call.clm_id = user_details.user_id))
    OR
    (user_details.type_id = 4 AND init_call.apst = user_details.user_id)
)
	$text $filter");

    return $query->result(); 


}







}