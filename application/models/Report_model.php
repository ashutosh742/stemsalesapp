<?php
date_default_timezone_set("Asia/Calcutta");
class Report_model extends Menu_model{
    public function GetAllUserByReportingManager($userid,$admin_id_filter,$rm_filter,$curdate){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;

    if($type_id ==1){
        $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 2){
        $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 3){
        $text = "(u1.user_id = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 4){
        $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 13){
        $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid' || u1.user_id = $userid)";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
    }else{
        $text = "u1.admin_id = '$userid'";
    }



    if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "u1.admin_id IN (2,45)";
        }else{
            $text = "u1.sadmin_id = '$cuid'";
        }
    }else{
     
        if($rm_filter != 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "(u1.user_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "(u1.sales_co = '$userid' || u1.user_id = '$userid')";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid' )";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid'";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid'";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "u1.admin_id = '$userid' ";
                }
        }
    }

    if($curdate !== ''){
        $curdate_filter = "AND CAST(user_day.ustart AS DATE) = '$curdate'";
    }else{
        $curdate_filter = "";
    }
        
    

        $query = $this->db->query("SELECT 
            u1.*,
            user_type.name AS user_roles,
            user_zone.name as user_zone_name,
            COUNT(DISTINCT CASE WHEN init_call.cmpid_id IS NOT NULL THEN init_call.id END) AS total_funnel
        FROM user_details u1
        LEFT JOIN user_type ON user_type.id = u1.type_id
        LEFT JOIN init_call ON init_call.mainbd = u1.user_id
        LEFT JOIN user_zone ON user_zone.id = u1.zone_id
        WHERE 
            $text
        GROUP BY 
            u1.user_id
        ORDER BY 
            u1.user_id DESC");
        $data1 = $query->result();

        
      $query1 = $this->db->query("SELECT 
            COUNT(u1.user_id) AS total_user,
            COUNT(CASE WHEN u1.status = 'active' THEN u1.user_id END) AS total_active_user,
            COUNT(CASE WHEN u1.status = 'inactive' THEN u1.user_id END) AS total_inactive_user
        FROM user_details u1
        
        WHERE $text");
        $data2 = $query1->result();
      $query2 = $this->db->query("SELECT 
            COUNT(DISTINCT CASE WHEN user_day.id IS NOT NULL THEN u1.user_id END) AS total_present,
            COUNT(DISTINCT CASE WHEN user_day.id IS NOT NULL AND uwf.ID = 1 THEN u1.user_id END) AS total_work_from_Office,
            COUNT(DISTINCT CASE WHEN user_day.id IS NOT NULL AND uwf.ID = 2 THEN u1.user_id END) AS total_work_from_field,
            COUNT(DISTINCT CASE WHEN user_day.id IS NOT NULL AND uwf.ID = 3 THEN u1.user_id END) AS total_work_from_field_Office
        FROM user_details u1
        LEFT JOIN user_day ON u1.user_id = user_day.user_id $curdate_filter
        LEFT JOIN userworkfrom uwf ON uwf.ID = user_day.wffo
        

        WHERE $text");
        $data3 = $query2->result();
        $result = [
            'data1'=>$data1,
            'data2'=>$data2,
            'data3'=>$data3
        ];
        return $result;
    }
    public function GetTodaysAllUserByReportingManager($mtypes,$target_userid,$sdate){
    $userid = $target_userid;
    
    $userwise  = '';
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = $userid || u1.user_id = $userid)";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}
        if($mtypes == 'total_present'){
            $filter = 'AND user_day.id IS NOT NULL';
        }else if($mtypes == 'total_work_from_Office'){
             $filter = 'AND user_day.id IS NOT NULL AND uwf.ID = 1';
        }else if($mtypes == 'total_work_from_field'){
            $filter = 'AND user_day.id IS NOT NULL AND uwf.ID = 2';
        }else if($mtypes == 'total_work_from_field_Office'){
            $filter = 'AND user_day.id IS NOT NULL AND uwf.ID = 3';
        }
      $query1 = $this->db->query("SELECT 
            u1.user_id,
            u1.name,
            user_type.name as type_name,
            uwf.TYPE AS start_from,
            user_day.sdatet AS start_date,
            user_day.uclose AS uclose_date,
            user_day.usimg AS start_image,
            user_day.slatitude AS start_slatitude,
            user_day.slongitude AS start_slongitude,
            user_day.ucimg AS close_image,
            user_day.clatitude AS close_clatitude,
            user_day.clongitude AS close_clongitude,
            user_day.queans,
            user_day.queansc
            FROM user_details u1
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN user_day ON u1.user_id = user_day.user_id 
            LEFT JOIN userworkfrom uwf ON uwf.ID = user_day.wffo
            WHERE $text $filter AND CAST(user_day.ustart AS DATE) = '$sdate' GROUP BY u1.user_id ORDER BY u1.user_id DESC
            ");
        $data2 = $query1->result();
        return $data2;
    }
    
public function GetTeamWisePlanedTaskType($userid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter){
  
 $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "AND u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "AND u1.admin_id = $userid";
    }else if($type_id == 3){
        $text = "AND u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "AND u1.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "AND (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "AND (u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "AND (u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "AND (u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "AND (u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[1])){
            $text = "AND u1.sadmin_id = '$cuid'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "AND u1.sadmin_id = $userid";
                }else if($type_id == 2){
                    $text = "AND u1.admin_id = $userid";
                }else if($type_id == 3){
                    $text = "AND u1.user_id = $userid";
                }else if($type_id == 4){
                    $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "AND u1.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "AND (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
                }else if($type_id == 20){
                    $text = "AND (u1.ash_w_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 21){
                    $text = "AND (u1.ash_s_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 22){
                    $text = "AND (u1.rm_east_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 23){
                    $text = "AND u1.rm_north_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 24){
                    $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
                }else{
                    $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
                }
        }
    }
    if($task_action_id == 'all'){
        $task_action_filter = "AND (tblcallevents.actiontype_id != 0 OR tblcallevents.actiontype_id != '') AND ((tblcallevents.approved_status = 1 || tblcallevents.approved_status = '')
    AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL))";
    }else{
        $task_action_filter = "AND tblcallevents.actiontype_id = $task_action_id AND ((tblcallevents.approved_status = 1 || tblcallevents.approved_status = '')
    AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL))";
    }
  
    $query=$this->db->query("SELECT
    u1.name,
    u1.user_id AS planner_user_id,
    -- Task/Review ID and Type
    COALESCE(ta.id, NULL) AS action_id,
    COALESCE(ta.name, NULL) AS tasktype,
    -- Task time estimate
    ta.yest,
    -- Count of tasks
    COUNT(tblcallevents.id) AS task_count,
    -- Total planned task count for the user
    (
        SELECT COUNT(*)
        FROM tblcallevents tce
        WHERE tce.actiontype_id != ''
		  AND DATE(tce.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
          AND (tce.approved_status = 1 || tce.approved_status = '')
          AND (tce.delete_request = '' OR tce.delete_request IS NULL)
          AND tce.user_id = u1.user_id
          AND tce.appointmentdatetime != '0000-00-00 00:00:00'
    ) AS total_task_count,
    -- Total time of all planned tasks
    CONCAT(
        FLOOR((
            SELECT SUM(ta_inner.yest)
            FROM tblcallevents tce_inner
            JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
            WHERE tce_inner.actiontype_id != ''
            --   AND tce_inner.plan = 1
              AND DATE(tce_inner.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
              AND tce_inner.user_id = u1.user_id
            --   AND tce_inner.autotask = 0
              AND (tce_inner.approved_status = 1 || tce_inner.approved_status = '')
              AND (tce_inner.delete_request = '' OR tce_inner.delete_request IS NULL)
              AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
        ) / 60),
        ' hours ',
        (
            SELECT SUM(ta_inner.yest)
            FROM tblcallevents tce_inner
            JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
            WHERE tce_inner.actiontype_id != ''
            --   AND tce_inner.plan = 1
              AND DATE(tce_inner.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
              AND tce_inner.user_id = u1.user_id
            --   AND tce_inner.autotask = 0
              AND (tce_inner.approved_status = 1 || tce_inner.approved_status = '')
              AND (tce_inner.delete_request = '' OR tce_inner.delete_request IS NULL)
              AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
        ) % 60,
        ' minutes'
    ) AS total_plan_task_time
FROM
    tblcallevents
LEFT JOIN action ta ON ta.id = tblcallevents.actiontype_id
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
WHERE
    tblcallevents.plan = 1
    AND DATE(tblcallevents.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
    AND tblcallevents.appointmentdatetime != '0000-00-00 00:00:00'
    -- AND tblcallevents.nextCFID != 0 
    AND (tblcallevents.approved_status = 1 || tblcallevents.approved_status = '')
    AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
    $text
    $task_action_filter
GROUP BY
    u1.user_id, ta.id
ORDER BY
    task_count DESC");
    return $query->result();
}
public function GetTeamWisePlanedTaskTypeUsingPlanner($userid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter){
  
    $udetail = $this->Menu_model->get_userbyid($userid);
       $type_id = $udetail[0]->type_id;
       
       if($type_id == 1){
           $text = "AND u1.sadmin_id = $userid";
       }else if($type_id == 2){
           $text = "AND u1.admin_id = $userid";
       }else if($type_id == 3){
           $text = "AND u1.user_id = $userid";
       }else if($type_id == 4){
           $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
       }else if($type_id == 13){
           $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
       }else if($type_id == 15){
           $text = "AND u1.sales_co = $userid";
       }elseif($type_id == 19){
           $text = "AND (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
       }else if($type_id == 20){
           $text = "AND (u1.ash_w_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 21){
           $text = "AND (u1.ash_s_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 22){
           $text = "AND (u1.rm_east_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 23){
           $text = "AND (u1.rm_north_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 24){
           $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
       }else{
           $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
       }
   
       if($admin_id_filter =='all'){
        //    $text = "AND u1.admin_id IN (2,45)";
            $cuser = $this->session->userdata('user');
            $cuid = $cuser['user_id'];
            if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
            }else if(in_array($cuid,[100000])){
                $text = "AND u1.sadmin_id = '100000'";
            }else{
                $text = "AND u1.admin_id = '$cuid'";
            }
       }else{
           if($rm_filter !== 'all'){
                   $udetail = $this->Menu_model->get_userbyid($rm_filter);
                   $type_id = $udetail[0]->type_id;
                   
                   if($type_id == 1){
                       $text = "AND u1.sadmin_id = $userid";
                   }else if($type_id == 2){
                       $text = "AND u1.admin_id = $userid";
                   }else if($type_id == 3){
                       $text = "AND u1.user_id = $userid";
                   }else if($type_id == 4){
                       $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
                   }else if($type_id == 13){
                       $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
                   }else if($type_id == 15){
                       $text = "AND u1.sales_co = $userid";
                   }elseif($type_id == 19){
                       $text = "AND (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
                   }else if($type_id == 20){
                       $text = "AND (u1.ash_w_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 21){
                       $text = "AND (u1.ash_s_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 22){
                       $text = "AND (u1.rm_east_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 23){
                       $text = "AND u1.rm_north_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 24){
                       $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
                   }else{
                       $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
                   }
           }
       }
   
       if($task_action_id == 'all'){
           $task_action_filter = "AND (tblcallevents.actiontype_id != 0 OR tblcallevents.actiontype_id != '') AND ((tblcallevents.approved_status = 1 || tblcallevents.approved_status = '')
       AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)) AND tblcallevents.autotask = 0";
       }else{
           $task_action_filter = "AND tblcallevents.actiontype_id = $task_action_id AND ((tblcallevents.approved_status = 1 || tblcallevents.approved_status = '')
       AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)) AND tblcallevents.autotask = 0";
       }
   
   
       $query=$this->db->query("SELECT
       u1.name,
       u1.user_id AS planner_user_id,
   
       -- Task/Review ID and Type
       COALESCE(ta.id, NULL) AS action_id,
       COALESCE(ta.name, NULL) AS tasktype,
   
       -- Task time estimate
       ta.yest,
   
       -- Count of tasks
       
       COUNT(tblcallevents.id) AS task_count,
   
       -- Total planned task count for the user
       (
           SELECT COUNT(*)
           FROM tblcallevents tce
           WHERE tce.actiontype_id != ''
             AND DATE(tce.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
             AND (tce.approved_status = 1 || tce.approved_status = '')
             AND (tce.delete_request = '' OR tce.delete_request IS NULL)
             AND tce.user_id = u1.user_id
             AND tce.autotask = 0
             AND tce.appointmentdatetime != '0000-00-00 00:00:00'
       ) AS total_task_count,
   
       -- Total time of all planned tasks
       CONCAT(
           FLOOR((
               SELECT SUM(ta_inner.yest)
               FROM tblcallevents tce_inner
               JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
               WHERE tce_inner.actiontype_id != ''
                 AND DATE(tce_inner.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
                 AND tce_inner.user_id = u1.user_id
                 AND tce_inner.autotask = 0
                 AND (tce_inner.approved_status = 1 || tce_inner.approved_status = '')
                 AND (tce_inner.delete_request = '' OR tce_inner.delete_request IS NULL)
                 AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
           ) / 60),
           ' hours ',
           (
               SELECT SUM(ta_inner.yest)
               FROM tblcallevents tce_inner
               JOIN action ta_inner ON ta_inner.id = tce_inner.actiontype_id
               WHERE tce_inner.actiontype_id != ''
                 AND DATE(tce_inner.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
                 AND tce_inner.user_id = u1.user_id
                 AND tce_inner.autotask = 0
                 AND (tce_inner.approved_status = 1 || tce_inner.approved_status = '')
                 AND (tce_inner.delete_request = '' OR tce_inner.delete_request IS NULL)
                 AND tce_inner.appointmentdatetime != '0000-00-00 00:00:00'
           ) % 60,
           ' minutes'
       ) AS total_plan_task_time
   
   FROM
       tblcallevents
   LEFT JOIN action ta ON ta.id = tblcallevents.actiontype_id
   LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
   WHERE
       tblcallevents.plan = 1
       AND tblcallevents.autotask = 0
       AND DATE(tblcallevents.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
       AND tblcallevents.appointmentdatetime != '0000-00-00 00:00:00'
       AND (tblcallevents.approved_status = 1 || tblcallevents.approved_status = '')
       AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
       $text
       $task_action_filter
   GROUP BY
       u1.user_id, ta.id
   
   ORDER BY
       task_count DESC");
    $totalTasksUserByDatas = $query->result();
    $query1 = $this->db->query("SELECT
    COUNT(task_plan_for_today.id) as same_day_planning
FROM
    `task_plan_for_today`
    LEFT JOIN user_details u1 on u1.user_id = task_plan_for_today.user_id
WHERE
    task_plan_for_today.date BETWEEN '$sdate' AND '$edate'
    $text");
 $sameDaysPlanning = $query1->result();
 $query2 = $this->db->query("SELECT
 u1.user_id,
u1.name AS username,
 COUNT(task_plan_for_today.id) as same_day_planning
FROM
 `task_plan_for_today`
 LEFT JOIN user_details u1 on u1.user_id = task_plan_for_today.user_id
WHERE
 task_plan_for_today.date BETWEEN '$sdate' AND '$edate'
 $text GROUP BY u1.user_id");
$sameDaysPlanningByUser = $query2->result();
    $query3 = $this->db->query("SELECT
 COUNT(create_planner_request.id) as pending_task_planning_request,
 COUNT(CASE WHEN( create_planner_request.request_type = 'Plan But Not Initiated') THEN create_planner_request.id END ) AS plan_but_not_initiated_request,
 COUNT(CASE WHEN( create_planner_request.request_type = 'Old Pending Task') THEN create_planner_request.id END ) AS old_pending_task_request
FROM
 `create_planner_request`
 LEFT JOIN user_details u1 on u1.user_id = create_planner_request.request_user_id
WHERE
 create_planner_request.request_date BETWEEN '$sdate' AND '$edate'
 $text");
 $pendingPlanning = $query3->result();
 $query4 = $this->db->query("SELECT
 u1.user_id,
 u1.name AS username,
 COUNT(create_planner_request.id) as pending_task_planning_request,
 COUNT(CASE WHEN( create_planner_request.request_type = 'Plan But Not Initiated') THEN create_planner_request.id END ) AS plan_but_not_initiated_request,
 COUNT(CASE WHEN( create_planner_request.request_type = 'Old Pending Task') THEN create_planner_request.id END ) AS old_pending_task_request
FROM
 `create_planner_request`
 LEFT JOIN user_details u1 on u1.user_id = create_planner_request.request_user_id
WHERE
 create_planner_request.request_date BETWEEN '$sdate' AND '$edate'
 $text GROUP BY u1.user_id");
$pendingPlanningByUser = $query4->result();
    $query5 = $this->db->query("SELECT
    COUNT(pending_meetings_request.id) as pending_meetings_task_request
FROM
    `pending_meetings_request`
    LEFT JOIN user_details u1 on u1.user_id = pending_meetings_request.user_uid
WHERE
    pending_meetings_request.request_date BETWEEN '$sdate' AND '$edate'
    $text");
 $pendingMeetingsDelete = $query5->result();
 $query6 = $this->db->query("SELECT
u1.user_id,
 u1.name AS username,
 COUNT(pending_meetings_request.id) as pending_meetings_task_request
FROM
 `pending_meetings_request`
 LEFT JOIN user_details u1 on u1.user_id = pending_meetings_request.user_uid
WHERE
 pending_meetings_request.request_date BETWEEN '$sdate' AND '$edate'
 $text GROUP BY u1.user_id");
$pendingMeetingsDeleteByUser = $query6->result();
    $query7 = $this->db->query("SELECT
    COUNT(special_request_for_leave.id) as special_request_for_leave_request
FROM
    `special_request_for_leave`
    LEFT JOIN user_details u1 on u1.user_id = special_request_for_leave.user_id
WHERE
    special_request_for_leave.date BETWEEN '$sdate' AND '$edate'
    $text");
 $specialLeaveRequest = $query7->result();
 $query8 = $this->db->query("SELECT
 u1.user_id,
 u1.name AS username,
    COUNT(special_request_for_leave.id) as special_request_for_leave_request
FROM
    `special_request_for_leave`
    LEFT JOIN user_details u1 on u1.user_id = special_request_for_leave.user_id
WHERE
    special_request_for_leave.date BETWEEN '$sdate' AND '$edate'
    $text GROUP BY u1.user_id");
$specialLeaveRequestByUser = $query8->result();
// -- CONCAT(
//     --     FLOOR(SUM(TIME_TO_SEC(session_plan_time.totaltime)) / 3600), ' hours ',
//     --     FLOOR(MOD(SUM(TIME_TO_SEC(session_plan_time.totaltime)), 3600) / 60), ' minutes and ',
//     --     MOD(SUM(TIME_TO_SEC(session_plan_time.totaltime)), 60), ' seconds'
//     -- ) AS total_time
    $query9 = $this->db->query("SELECT
    COUNT(session_plan_time.id) AS session_count,
        CONCAT(
        FLOOR(SUM(TIME_TO_SEC(TIMEDIFF(pctime, pstime))) / 3600), ' hours ',
        FLOOR((SUM(TIME_TO_SEC(TIMEDIFF(pctime, pstime))) % 3600) / 60), ' minutes ',
        SUM(TIME_TO_SEC(TIMEDIFF(pctime, pstime))) % 60, ' seconds'
    ) AS total_time
FROM
    session_plan_time
LEFT JOIN user_details u1 ON u1.user_id = session_plan_time.user_id
WHERE cast(session_plan_time.psdatetime as Date) BETWEEN '$sdate' AND '$edate'
    $text AND (pstime IS NOT NULL AND pctime IS NOT NULL)");
$userplanningTime = $query9->result();
    $query10 = $this->db->query("SELECT
        u1.user_id,
        u1.name AS username,
        COUNT(session_plan_time.id) AS session_count,
        CONCAT(
        FLOOR(TIME_TO_SEC(TIMEDIFF(pctime, pstime)) / 3600), ' hours ',
        FLOOR((TIME_TO_SEC(TIMEDIFF(pctime, pstime)) % 3600) / 60), ' minutes ',
        TIME_TO_SEC(TIMEDIFF(pctime, pstime)) % 60, ' seconds'
    ) AS total_time
    FROM
        session_plan_time
    LEFT JOIN user_details u1 ON u1.user_id = session_plan_time.user_id
    WHERE cast(session_plan_time.psdatetime as Date) BETWEEN '$sdate' AND '$edate'
        $text AND (pstime IS NOT NULL AND pctime IS NOT NULL)
    GROUP BY session_plan_time.user_id
    ");
    $userplanningTimeByUser = $query10->result();
    $query11 = $this->db->query("SELECT
         COUNT(allreview.id) AS total_review_count,
         COUNT(CASE WHEN allreview.uid = allreview.bdid THEN allreview.id END) for_him_self_review,
         COUNT(CASE WHEN allreview.uid != allreview.bdid THEN allreview.id END) for_other_review
    FROM
        `allreview`
        LEFT JOIN user_details u1 on u1.user_id = allreview.uid
    WHERE cast(allreview.plant as Date) BETWEEN '$sdate' AND '$edate'
             $text 
        ");
        $reviewPlanned= $query11->result();
        $query12 = $this->db->query("SELECT
        u1.user_id,
        u1.name AS username,
         COUNT(allreview.id) AS total_review_count,
         COUNT(CASE WHEN allreview.uid = allreview.bdid THEN allreview.id END) for_him_self_review,
         COUNT(CASE WHEN allreview.uid != allreview.bdid THEN allreview.id END) for_other_review
            FROM
                `allreview`
                LEFT JOIN user_details u1 on u1.user_id = allreview.uid
            WHERE cast(allreview.plant as Date) BETWEEN '$sdate' AND '$edate'
                    $text GROUP BY u1.user_id
        ");
        $reviewPlannedByUser = $query12->result();
            $query13=$this->db->query("SELECT
                COUNT(tblcallevents.id) AS total_user_task,
                COUNT(
                    CASE WHEN(
                    tblcallevents.autotask = 0 AND tblcallevents.plan = 1
                    ) THEN tblcallevents.id
                END
            ) AS total_planned_task,
            COUNT(
                CASE WHEN(tblcallevents.autotask = 1) THEN tblcallevents.id
            END
            ) AS total_autotask,
            COUNT(
                    CASE WHEN(
                        tblcallevents.approved_status = 1 AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
                    ) THEN tblcallevents.id
                END
            ) AS total_approved_task,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = '' AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
                ) THEN tblcallevents.id
            END
            ) AS total_pending_for_approved,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
                ) THEN tblcallevents.id
            END
            ) AS total_reject,
            COUNT(
                CASE WHEN(
                    tblcallevents.delete_request > 0
                ) THEN tblcallevents.id
            END
            ) AS total_deleted_task,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id 
            END
            ) AS pending_for_assign_after_reject_task,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id
            END
            ) AS admin_create_request_for_self_assign,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id
            END
            ) AS task_assignd_by_admin_after_reject,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id
            END
            ) AS task_assignd_by_user_after_reject,
            COUNT(
                CASE WHEN tblcallevents.nextCFID != 0 AND (tblcallevents.approved_status = 1 OR tblcallevents.approved_status = '') AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL) THEN tblcallevents.id
            END
            ) AS user_complete_task,
            COUNT(
                CASE WHEN tblcallevents.nextCFID = 0 AND (tblcallevents.approved_status = 1 OR tblcallevents.approved_status = '') AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL) THEN tblcallevents.id
            END
            ) AS user_pending_for_complete_task,
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
            ) AS pending_for_complete_autotask
            FROM
                tblcallevents
            LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
            WHERE
                CAST(
                    tblcallevents.appointmentdatetime AS DATE
                ) BETWEEN '$sdate' AND '$edate'
                AND tblcallevents.appointmentdatetime != '0000-00-00 00:00:00'
                $text
                ");
            
                $totalTasks = $query13->result();
            $query14=$this->db->query("SELECT
                u1.user_id,
                u1.name as username,
                COUNT(tblcallevents.id) AS total_user_task,
                COUNT(
                    CASE WHEN(
                    tblcallevents.autotask = 0 AND tblcallevents.plan = 1
                    ) THEN tblcallevents.id
                END
            ) AS total_planned_task,
            COUNT(
                CASE WHEN(tblcallevents.autotask = 1) THEN tblcallevents.id
            END
            ) AS total_autotask,
            COUNT(
                    CASE WHEN(
                        tblcallevents.approved_status = 1 AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
                    ) THEN tblcallevents.id
                END
            ) AS total_approved_task,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = '' AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
                ) THEN tblcallevents.id
            END
            ) AS total_pending_for_approved,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
                ) THEN tblcallevents.id
            END
            ) AS total_reject,
            COUNT(
                CASE WHEN(
                    tblcallevents.delete_request > 0
                ) THEN tblcallevents.id
            END
            ) AS total_deleted_task,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id 
            END
            ) AS pending_for_assign_after_reject_task,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id
            END
            ) AS admin_create_request_for_self_assign,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id
            END
            ) AS task_assignd_by_admin_after_reject,
            COUNT(
                CASE WHEN(
                    tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3' AND tblcallevents.autotask = 0
                ) THEN tblcallevents.id
            END
            ) AS task_assignd_by_user_after_reject,
            COUNT(
                CASE WHEN tblcallevents.nextCFID != 0 AND (tblcallevents.approved_status = 1 OR tblcallevents.approved_status = '') AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL) THEN tblcallevents.id
            END
            ) AS user_complete_task,
            COUNT(
                CASE WHEN tblcallevents.nextCFID = 0 AND (tblcallevents.approved_status = 1 OR tblcallevents.approved_status = '') AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL) THEN tblcallevents.id
            END
            ) AS user_pending_for_complete_task,
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
            ) AS pending_for_complete_autotask
            FROM
                tblcallevents
            LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
            WHERE
                CAST(
                    tblcallevents.appointmentdatetime AS DATE
                ) BETWEEN '$sdate' AND '$edate'
                AND tblcallevents.appointmentdatetime != '0000-00-00 00:00:00'
                $text GROUP BY u1.user_id
                ");
            
                $totalTasksByUser = $query14->result();
                    $query15 = $this->db->query("SELECT
                                        pa.request_date,
                                        COUNT(CASE WHEN pa.id IS NOT NULL THEN 1 END) AS total_request,
                                        COUNT(CASE WHEN pa.approved_status = 1 THEN 1 END) AS total_approved,
                                        COUNT(CASE WHEN pa.approved_status IS NULL THEN 1 END) AS total_pending
                                    FROM
                                        planner_approved pa
                                        LEFT JOIN user_details u1 ON u1.user_id = pa.user_id
                                    WHERE
                                      pa.request_date BETWEEN '$sdate' AND '$edate' $text
                                    GROUP BY pa.request_date");
        $teamDailyPlannedByUser = $query15->result();
$data = [
        'totalTasksUserByDatas'         => $totalTasksUserByDatas,
        'sameDaysPlanning'              => $sameDaysPlanning,
        'sameDaysPlanningByUser'        => $sameDaysPlanningByUser,
        'pendingPlanning'               => $pendingPlanning,
        'pendingPlanningByUser'         => $pendingPlanningByUser,
        'pendingMeetingsDelete'         => $pendingMeetingsDelete,
        'pendingMeetingsDeleteByUser'   => $pendingMeetingsDeleteByUser,
        'specialLeaveRequest'           => $specialLeaveRequest,
        'specialLeaveRequestByUser'     => $specialLeaveRequestByUser,
        'userplanningTime'              => $userplanningTime,
        'userplanningTimeByUser'        => $userplanningTimeByUser,
        'reviewPlanned'                 => $reviewPlanned,
        'reviewPlannedByUser'           => $reviewPlannedByUser,
        'totalTasks'                    => $totalTasks,
        'totalTasksByUser'              => $totalTasksByUser,
         'teamDailyPlannedByUser'       => $teamDailyPlannedByUser
    ];
    
       return $data;
   }
   
   public function GetUserRequestDetails($userid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise){
    
    
    
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}
   
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    if($mtypes == 'same_day_planning'){
       
        $query = $this->db->query("SELECT
        task_plan_for_today.*,
        u1.name as user_name
    FROM
        `task_plan_for_today`
        LEFT JOIN user_details u1 on u1.user_id = task_plan_for_today.user_id
    WHERE
        task_plan_for_today.date BETWEEN '$sdate' AND '$edate'
        AND $text");
    
    }else if($mtypes == 'pending_task_planning_request' || $mtypes == 'plan_but_not_initiated_request' || $mtypes == 'old_pending_task_request'){
        
        $checkFilter = '';
        if($mtypes == 'plan_but_not_initiated_request'){
            $checkFilter = "AND create_planner_request.request_type LIKE '%Plan But Not Initiated%'";
        }
        if($mtypes == 'old_pending_task_request'){
            $checkFilter = "AND create_planner_request.request_type LIKE '%Old Pending Task%'";
        }
        $query = $this->db->query("SELECT
        create_planner_request.*,
        u1.name as user_name,
        u2.name as approved_by_name
       FROM
        `create_planner_request`
        LEFT JOIN user_details u1 on u1.user_id = create_planner_request.request_user_id
         LEFT JOIN user_details u2 on u2.user_id = create_planner_request.approved_by
       WHERE
        create_planner_request.request_date BETWEEN '$sdate' AND '$edate'
        AND $text $checkFilter");
       
    }else if($mtypes == 'pending_meetings_task_request'){
        $query = $this->db->query("SELECT 
                    pending_meetings_request.*,
                    u1.name as request_user_name,
                    u2.name as apr_by_name
                    FROM pending_meetings_request
                    LEFT JOIN user_details u1 on u1.user_id = pending_meetings_request.user_uid
                    LEFT JOIN user_details u2 on u2.user_id = pending_meetings_request.apr_by
                    WHERE pending_meetings_request.request_date BETWEEN '$sdate' AND '$edate'
            AND $text ORDER BY pending_meetings_request.request_date");
    }else if($mtypes == 'special_request_for_leave_request'){
        $query = $this->db->query("SELECT
        special_request_for_leave.*,
        u1.name as username,
        u2.name as approved_by
    FROM
        `special_request_for_leave`
        LEFT JOIN user_details u1 on u1.user_id = special_request_for_leave.user_id
    LEFT JOIN user_details u2 on u2.user_id = special_request_for_leave.approve_by
        WHERE special_request_for_leave.date BETWEEN '$sdate' AND '$edate'
        AND $text");
    }else if($mtypes == 'total_review_count' || $mtypes == 'for_him_self_review' || $mtypes == 'for_other_review'){
        $checkFilter = '';
        if($mtypes == 'for_him_self_review'){
            $checkFilter = "AND allreview.uid = allreview.bdid";
        }
        if($mtypes == 'for_other_review'){
            $checkFilter = "AND allreview.uid != allreview.bdid";
        }
        $query = $this->db->query("SELECT
        u1.name AS by_username,
        u2.name as to_username,
        allreview1.reviewtype as after_reviewtype,
        allreview.*
            FROM
                `allreview`
                LEFT JOIN user_details u1 on u1.user_id = allreview.uid
                LEFT JOIN user_details u2 on u2.user_id = allreview.bdid
                LEFT JOIN allreview allreview1 on allreview1.id = allreview.after_review
            WHERE cast(allreview.plant as Date) BETWEEN '$sdate' AND '$edate'
            AND $text $checkFilter");
    }
   return  $query->result(); 
}
   public function GetTeamWisePlanedTaskTypeUsingPlannerDataLists($userid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise){
    
    
    
    if($userid == 'all'){
        $text = "user_details.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 1){
        $text = "user_details.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "user_details.admin_id = $userid";
    }else if($type_id == 15){
        $text = "user_details.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.ash_nae_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.ash_w_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.ash_s_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.rm_east_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.rm_north_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)"; 
            }
        }else{
             $text = "user_details.user_id = $userid";
        }
    }
}
   
// echo $text;
// die;
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
 
    // if($task_action_id == 'all'){
    //     $taskactionFilter = "AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '')";
    // }else{
    //     $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id";
    // }
    if($task_action_id == 'all'){
        $taskactionFilter = "AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '') AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
    AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL)) AND tbcl.autotask = 0 ";
    }else{
        $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL)) AND tbcl.autotask = 0";
    }
    if($mtypes == 'total_task'){
        $filter = ' ';
    }else if($mtypes == 'total_complete_task'){
        $filter = ' AND tbcl.nextCFID !=0';
    }else if($mtypes == 'total_pending_task'){
        $filter = ' AND tbcl.nextCFID =0';
    }else if($mtypes == 'total_status_change_task'){
        $filter = " AND tbcl.nextCFID !=0 AND tbcl.status_id != tbcl.nstatus_id AND (tbcl.nstatus_id IS NOT NULL AND tbcl.nstatus_id !=0)";
    }else if($mtypes == 'total_own_funnel_task'){
        $filter = " AND init_call.mainbd = user_details.user_id";
    }else if($mtypes == 'total_own_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd = user_details.user_id";
    }else if($mtypes == 'total_own_funnel_pending_task'){
        $filter = " AND tbcl.nextCFID =0 AND init_call.mainbd = user_details.user_id";
    } else if($mtypes == 'total_own_funnel_status_change_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd = user_details.user_id AND tbcl.status_id != tbcl.nstatus_id AND (tbcl.nstatus_id IS NOT NULL AND tbcl.nstatus_id !=0)";
    }else if($mtypes == 'total_team_funnel_task'){
        $filter = " AND init_call.mainbd != user_details.user_id";
    }else if($mtypes == 'total_team_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd != user_details.user_id";
    }else if($mtypes == 'total_team_funnel_pending_task'){
        $filter = " AND tbcl.nextCFID =0 AND init_call.mainbd != user_details.user_id";
    }else if($mtypes == 'total_team_funnel_status_change_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd != user_details.user_id AND tbcl.status_id != tbcl.nstatus_id AND (tbcl.nstatus_id IS NOT NULL AND tbcl.nstatus_id !=0)";
    }else if($mtypes == 'total_own_aypy_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd = user_details.user_id AND (tbcl.actontaken = 'yes' AND  tbcl.purpose_achieved = 'yes')";
    }else if($mtypes == 'total_team_aypy_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd != user_details.user_id AND (tbcl.actontaken = 'yes' AND  tbcl.purpose_achieved = 'yes')";
    }else if($mtypes == 'total_own_funnel_aypy_status_change_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd = user_details.user_id AND tbcl.status_id != tbcl.nstatus_id AND (tbcl.nstatus_id IS NOT NULL AND tbcl.nstatus_id !=0) AND (tbcl.actontaken = 'yes' AND  tbcl.purpose_achieved = 'yes')";
    }else if($mtypes == 'total_team_funnel_aypy_status_change_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd != user_details.user_id AND tbcl.status_id != tbcl.nstatus_id AND (tbcl.nstatus_id IS NOT NULL AND tbcl.nstatus_id !=0) AND (tbcl.actontaken = 'yes' AND  tbcl.purpose_achieved = 'yes')";
    }else if($mtypes == 'total_own_aypn_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd = user_details.user_id AND (tbcl.actontaken = 'yes' AND  tbcl.purpose_achieved = 'no')";
    }else if($mtypes == 'total_team_aypn_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd != user_details.user_id AND (tbcl.actontaken = 'yes' AND  tbcl.purpose_achieved = 'no')";
    }
    else if($mtypes == 'Check_task_type'){
        $filter = "AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL)) AND tbcl.autotask = 0";
    }else if($mtypes == 'total_own_anpn_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd = user_details.user_id AND (tbcl.actontaken = 'no' AND  tbcl.purpose_achieved = 'no')";
    }else if($mtypes == 'total_team_anpn_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd != user_details.user_id AND (tbcl.actontaken = 'no' AND  tbcl.purpose_achieved = 'no')";
    }else if($mtypes == 'total_own_anpy_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd = user_details.user_id AND (tbcl.actontaken = 'no' AND  tbcl.purpose_achieved = 'yes')";
    }else if($mtypes == 'total_team_anpy_funnel_complete_task'){
        $filter = " AND tbcl.nextCFID !=0 AND init_call.mainbd != user_details.user_id AND (tbcl.actontaken = 'no' AND  tbcl.purpose_achieved = 'yes')";
    }
    else if ($mtypes == 'total_open' || $mtypes == 'open') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 1";
    }else if ($mtypes == 'total_reachout' || $mtypes == 'reachout') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 2";
    }else if ($mtypes == 'total_tentative' || $mtypes == 'tentative') {
         $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 3";
    }else if ($mtypes == 'total_will_do_later' || $mtypes == 'will_do_later') {
         $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 4";
    }else if ($mtypes == 'total_not_interested' || $mtypes == 'not_interested') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 5";
    }else if ($mtypes == 'total_positive' || $mtypes == 'positive') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 6";
    }else if ($mtypes == 'total_closure' || $mtypes == 'closure') {
        $filter = " AND tbcl.nextCFID != 0 AND (tbcl.status_id = 7 || init_call.upsell_client = 'yes')";
    }else if ($mtypes == 'total_open_rpem' || $mtypes == 'open_rpem') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 8";
    }else if ($mtypes == 'total_very_positive' || $mtypes == 'very_positive') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 9";
    }else if ($mtypes == 'total_ttd_reachout' || $mtypes == 'ttd_reachout') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 10";
    }else if ($mtypes == 'total_wno_reachout' || $mtypes == 'wno_reachout') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 11";
    }else if ($mtypes == 'total_positive_nap' || $mtypes == 'positive_nap') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 12";
    }else if ($mtypes == 'total_very_positive_nap' || $mtypes == 'very_positive_nap') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 13";
    }else if ($mtypes == 'total_on_boarded' || $mtypes == 'on_boarded') {
        $filter = " AND tbcl.nextCFID != 0 AND tbcl.status_id = 14";
    }
    else if ($mtypes == 'total_in_q1_twetenty_closure_funnel') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.q1_twetenty_closure_funnel = '$year'";
        $userMainBDRolesFilter  = "AND (
                                (user_details.type_id = 3 AND init_call.mainbd = user_details.user_id)
                                OR
                                (user_details.type_id = 13 AND (init_call.mainbd = user_details.user_id))
                                OR
                                (user_details.type_id = 4 AND init_call.apst = user_details.user_id)
                                OR
                                (user_details.type_id = 24 AND init_call.mainbd = user_details.user_id )
                            )";
    }
    
    else if ($mtypes == 'total_in_potential_funnel_for_fy') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.potential_funnel_for_fy = '$year'";
        $userMainBDRolesFilter  = "AND (
                                (user_details.type_id = 3 AND init_call.mainbd = user_details.user_id)
                                OR
                                (user_details.type_id = 13 AND (init_call.mainbd = user_details.user_id))
                                OR
                                (user_details.type_id = 4 AND init_call.apst = user_details.user_id)
                                OR
                                (user_details.type_id = 24 AND (init_call.mainbd = user_details.user_id))
                            )";
    }else if ($mtypes == 'total_in_to_be_nurtured_for_fy') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.to_be_nurtured_for_fy = '$year'";
        $userMainBDRolesFilter  = "AND (
                                (user_details.type_id = 3 AND init_call.mainbd = user_details.user_id)
                                OR
                                (user_details.type_id = 13 AND (init_call.mainbd = user_details.user_id))
                                OR
                                (user_details.type_id = 4 AND init_call.apst = user_details.user_id)
                                OR
                                (user_details.type_id = 24 AND (init_call.mainbd = user_details.user_id))
                            )";
    }else if ($mtypes == 'total_in_fifity_new_lead_funnel') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.fifity_new_lead_funnel = '$year'";
        $userMainBDRolesFilter  = "AND (
                                (user_details.type_id = 3 AND init_call.mainbd = user_details.user_id)
                                OR
                                (user_details.type_id = 13 AND (init_call.mainbd = user_details.user_id))
                                OR
                                (user_details.type_id = 4 AND init_call.apst = user_details.user_id)
                                OR
                                (user_details.type_id = 24 AND (init_call.mainbd = user_details.user_id))
                            )";
    }else if ($mtypes == 'zero_to_fifty_nine_seconds') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) BETWEEN 0 AND 59";
    }else if ($mtypes == 'one_to_five_minutes') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) BETWEEN 1 AND 5 ";
    }else if ($mtypes == 'five_to_ten_minutes') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) BETWEEN 5 AND 10 ";
    }else if ($mtypes == 'ten_to_fifteen_minutes') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) BETWEEN 10 AND 15";
    }else if ($mtypes == 'sixteen_to_thirty_minutes') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) > 15 
             AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) <= 30";
    }else if ($mtypes == 'thirty_one_to_fifty_minutes') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) > 30 
             AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) <= 50";
    }else if ($mtypes == 'fifty_one_to_sixty_minutes') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) > 50 
             AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) <= 60";
    }else if ($mtypes == 'greater_than_one_sixty_minutes') {
        $filter = " AND tbcl.nextCFID != 0 AND TIMESTAMPDIFF(MINUTE, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updated_at) > 60";
    }else if ($mtypes == 'potential') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.potential = 'yes'";
    }else if ($mtypes == 'topspender') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.topspender = 'yes'";
    }else if ($mtypes == 'upsell_client') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.upsell_client = 'yes'";
    }else if ($mtypes == 'focus_funnel') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.focus_funnel = 'yes'";
    }else if ($mtypes == 'keycompany') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.keycompany = 'yes'";
    }else if ($mtypes == 'pkclient') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.pkclient = 'yes'";
    }else if ($mtypes == 'priorityc') {
        $filter = " AND tbcl.nextCFID != 0 AND init_call.priorityc = 'yes'";
    }else if ($mtypes == 'total_conversions') {
        $filter = "AND user_details.status = 'active' AND tbcl.nextCFID != 0 AND tbcl.appointmentdatetime IS NOT NULL
        AND (tbcl.status_id != tbcl.nstatus_id AND tbcl.nstatus_id !=0 AND tbcl.nstatus_id !='' AND tbcl.nstatus_id IS NOT NULL)";
    }else if ($mtypes == 'positive_conversions') {
        $filter = "AND user_details.status = 'active' AND tbcl.nextCFID != 0  AND tbcl.appointmentdatetime IS NOT NULL
        AND (tbcl.status_id != tbcl.nstatus_id AND tbcl.nstatus_id !=0 AND tbcl.nstatus_id !='' AND tbcl.nstatus_id IS NOT NULL)
        AND (
            (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) OR
            (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) OR
            (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Closure')) OR
            (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) OR
            (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) OR
            (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) OR
            (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) OR
            (s1.name = 'Very Positive' AND s2.name = 'Closure') OR
            (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) OR
            (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
            (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') OR
            (s1.name = 'On-Boarded' AND s2.name = 'Closure')
            )";
    }else if ($mtypes == 'negative_conversions') {
        $filter = "AND user_details.status = 'active' AND tbcl.nextCFID != 0  AND tbcl.appointmentdatetime IS NOT NULL
        AND (tbcl.status_id != tbcl.nstatus_id AND tbcl.nstatus_id !=0 AND tbcl.nstatus_id !='' AND tbcl.nstatus_id IS NOT NULL)
        AND (
            (s1.name = 'Will do Later' AND s2.name = 'Open') OR
            (s1.name = 'Not Interested' AND s2.name = 'Open') OR
            (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
            (s1.name = 'Open RPEM' AND s2.name = 'Open') OR
            (s1.name = 'Reachout' AND s2.name IN ('Open RPEM','Will do Later', 'Not Interested', 'Open')) OR
            (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) OR
            (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) OR
            (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) OR
            (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) OR
            (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) OR
            (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) OR
            (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout')
            )";
    }else if ($mtypes == 'other_conversions') {
        $filter = "AND user_details.status = 'active' AND tbcl.nextCFID != 0  AND tbcl.appointmentdatetime IS NOT NULL
        AND (tbcl.status_id != tbcl.nstatus_id AND tbcl.nstatus_id !=0 AND tbcl.nstatus_id !='' AND tbcl.nstatus_id IS NOT NULL)
        AND NOT (
            -- Positive conversions
            (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) OR
            (s1.name = 'Open RPEM' AND s2.name IN ('Reachout', 'Positive','Tentative', 'Closure')) OR
            (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) OR
            (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) OR
            (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) OR
            (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) OR
            (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) OR
            (s1.name = 'Very Positive' AND s2.name = 'Closure') OR
            (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) OR
            (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
            (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') OR
            (s1.name = 'On-Boarded' AND s2.name = 'Closure') OR
            -- Negative conversions
            (s1.name = 'Will do Later' AND s2.name = 'Open') OR
            (s1.name = 'Not Interested' AND s2.name = 'Open') OR
            (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
            (s1.name = 'Open RPEM' AND s2.name = 'Open') OR
            (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) OR
            (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) OR
            (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) OR
            (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) OR
            (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) OR
            (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) OR
            (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) OR
            (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout')
            )";
    }else if ($mtypes == 'corporate') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 1";
    }else if ($mtypes == 'PSU') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 2";
    }else if ($mtypes == 'NGO') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 3";
    } else if ($mtypes == 'Private_School') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 4";
    } else if ($mtypes == 'Individual') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 6";
    } else if ($mtypes == 'Govt_Off') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 8";
    } else if ($mtypes == 'Other') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 9";
    } else if ($mtypes == 'Online') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 10";
    } else if ($mtypes == 'STEM_School') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 11";
    } else if ($mtypes == 'Foundation') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 12";
    } else if ($mtypes == 'MNC') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 13";
    } else if ($mtypes == 'HO_Leads') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 14";
    } else if ($mtypes == 'DMFT') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 16";
    } else if ($mtypes == 'Elected_Representatives') {
        $filter = " AND tbcl.nextCFID != 0 AND partner_master.id = 17";
    }
     else if ($mtypes == 'total_user_task') {
        $filter = " ";
        $taskactionFilter = "";
    }else if ($mtypes == 'total_planned_task') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.autotask = 0 AND tbcl.plan = 1 ";
    }else if ($mtypes == 'total_autotask') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.autotask = 1 ";
    }else if ($mtypes == 'total_approved_task') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.approved_status = 1 AND tbcl.autotask = 0 AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL)";
    }else if ($mtypes == 'total_pending_for_approved') {
        $filter = " ";
        $taskactionFilter = "AND  tbcl.approved_status = '' AND tbcl.autotask = 0 AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL)";
    } else if ($mtypes == 'total_reject') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.approved_status = 0 AND tbcl.approved_by != '' AND tbcl.autotask = 0 AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL)";
    }else if ($mtypes == 'total_deleted_task') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.delete_request > 0";
    }else if ($mtypes == 'pending_for_assign_after_reject_task') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.approved_status = 0 AND tbcl.approved_by != '' AND tbcl.self_assign = '' AND tbcl.autotask = 0";
    }else if ($mtypes == 'admin_create_request_for_self_assign') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.approved_status = 0 AND tbcl.approved_by != '' AND tbcl.self_assign = '1' AND tbcl.autotask = 0";
    }else if ($mtypes == 'task_assignd_by_admin_after_reject') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.approved_status = 0 AND tbcl.approved_by != '' AND tbcl.self_assign = '2' AND tbcl.autotask = 0";
    }else if ($mtypes == 'task_assignd_by_user_after_reject') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.approved_status = 0 AND tbcl.approved_by != '' AND tbcl.self_assign = '3' AND tbcl.autotask = 0";
    }else if ($mtypes == 'user_complete_task') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.nextCFID != 0 AND (tbcl.approved_status = 1 || tbcl.approved_status = '') AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL) ";
    }else if ($mtypes == 'user_pending_for_complete_task') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.nextCFID = 0 AND (tbcl.approved_status = 1 || tbcl.approved_status = '') AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL) ";
    }else if ($mtypes == 'after_task') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.aftertask != 0 ";
    }else if ($mtypes == 'complete_autotask') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.nextCFID != 0 AND tbcl.autotask = 1";
    }else if ($mtypes == 'pending_for_complete_autotask') {
        $filter = " ";
        $taskactionFilter = "AND tbcl.nextCFID = 0 AND tbcl.autotask = 1";
    }
  
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.comments,
     tbcl.reviewtype,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.selectby,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     init_call.createDate AS company_createDate,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	AND $text $filter ");
    return $query->result(); 
}
public function GetTaskIDUsingTaskName($name){
    $query = $this->db->query("SELECT * FROM action WHERE name = '$name'");
    $data2 = $query->result();
    return $data2;
}
public function GetActiveTaskData($actiin_id){
    $query = $this->db->query("SELECT * FROM `action` WHERE action_status = '$actiin_id'");
    $data2 = $query->result();
    return $data2;
}




public function getAllCompanyBYRollesMaiing($userid,$admin_id_filter,$rm_filter){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    $inside_sales = $udetail[0]->inside_sales;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }


    // echo $text;
    // die;


       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
            $mainBDInsiseSalesFilterQuery   = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery              = 'init_call.mainbd';
            $mainBDInsiseSalesFilterQuery   = 'init_call.mainbd';
        }

        $mainBDInsideFilterQuery = '';
        if($inside_sales == 'yes'){
            $mainBDInsideFilterQuery        = " OR init_call.insidebd = '$userid'";
            $mainBDInsiseSalesFilterQuery  = " init_call.insidebd";
        }



    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];
    $totalStatusQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN init_call.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN init_call.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN init_call.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN init_call.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN init_call.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN init_call.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN init_call.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN init_call.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN init_call.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN init_call.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN init_call.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN init_call.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (init_call.cstatus = 14 || init_call.upsell_client = 'yes') THEN 1 END) AS on_boarded
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery $mainBDInsideFilterQuery
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_status = $totalStatusQuerysByAdminRoles->result();
$totalOldCaegoryQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.focus_funnel = 'yes' THEN 1 END) AS Focus_Funnel,
    COUNT(CASE WHEN init_call.upsell_client = 'yes' THEN 1 END) AS Upsell_Client,
    COUNT(CASE WHEN init_call.keycompany = 'yes' THEN 1 END) AS Key_Client,
    COUNT(CASE WHEN init_call.pkclient = 'yes' THEN 1 END) AS Positive_Key_Client,
    COUNT(CASE WHEN init_call.priorityc = 'yes' THEN 1 END) AS Priority_Calling,
    COUNT(CASE WHEN init_call.topspender = 'yes' THEN 1 END) AS Top_Spender,
    COUNT(CASE WHEN init_call.potential = 'yes' THEN 1 END) AS Potential_Client
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery $mainBDInsideFilterQuery
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_old_category = $totalOldCaegoryQuerysByAdminRoles->result();
$totalNewCaegoryQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.q1_twetenty_closure_funnel = '$cfyear' THEN 1 END) AS q1_twetenty_closure_funnel,
    COUNT(CASE WHEN init_call.potential_funnel_for_fy = '$cfyear' THEN 1 END) AS potential_funnel_for_fy,
    COUNT(CASE WHEN init_call.to_be_nurtured_for_fy = '$cfyear' THEN 1 END) AS to_be_nurtured_for_fy,
    COUNT(CASE WHEN init_call.fifity_new_lead_funnel = '$cfyear' THEN 1 END) AS fifity_new_lead_funnel,
    COUNT(CASE WHEN init_call.anchor_clients = 'yes' THEN 1 END) AS anchor_clients,
    COUNT(CASE WHEN init_call.need_to_be_monitored IS NOT NULL THEN 1 END) AS need_to_be_monitored
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery $mainBDInsideFilterQuery
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_new_category = $totalNewCaegoryQuerysByAdminRoles->result();
$currentQuarterByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.in_quarter = '20 Closure Funnel in $currentQuarter' THEN 1 END) AS current_quarter_twetenty_closure_funnel,
    COUNT(CASE WHEN init_call.in_quarter = 'Potential Funnel For $currentQuarter' THEN 1 END) AS current_quarter_potential_funnel_for_fy,
    COUNT(CASE WHEN init_call.in_quarter = 'To be Nurtured for $currentQuarter' THEN 1 END) AS current_quarter_to_be_nurtured_for_fy,
    COUNT(CASE WHEN init_call.prospecting_funnel = '$currentQuarter' THEN 1 END) AS total_prospecting_funnel,
    COUNT(CASE WHEN init_call.proposal_to_be_sent_review = '$currentQuarter' THEN 1 END) AS total_proposal_to_be_sent_review,
    COUNT(CASE WHEN init_call.proposal_to_be_sent_target = '$currentQuarter' THEN 1 END) AS total_proposal_to_be_sent_target,
    COUNT(CASE WHEN init_call.closure_pipeline = '$currentQuarter' THEN 1 END) AS total_closure_pipeline
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery $mainBDInsideFilterQuery
WHERE
    $text AND u1.status = 'active'
");
$total_current_quarter = $currentQuarterByAdminRoles->result();
$totalSiignQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN $mainBDFilterQuery = u1.user_id AND (init_call.apst IS NOT NULL AND init_call.apst != 0) THEN 1 END) AS PST_assign_funnel,
    COUNT(CASE WHEN $mainBDFilterQuery = u1.user_id AND (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0) THEN 1 END) AS cluster_manager_assign_funnel,
    COUNT(CASE 
        WHEN $mainBDFilterQuery = u1.user_id AND 
            ((init_call.apst IS NOT NULL AND init_call.apst != 0) AND 
             (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0)) 
        THEN 1 
    END) AS PST_and_cluster_manager_assign_funnel,
    COUNT(
    CASE 
        WHEN $mainBDFilterQuery = u1.user_id 
            AND (init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Sales_Head_(NAE)_assign_funnel`,
    COUNT(
    CASE 
        WHEN $mainBDFilterQuery = u1.user_id 
            AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Sales_Head_(W)_assign_funnel`,
    COUNT(
    CASE 
        WHEN $mainBDFilterQuery = u1.user_id 
            AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Sales_Head_(S)_assign_funnel`,
    COUNT(
    CASE 
        WHEN $mainBDFilterQuery = u1.user_id 
            AND (init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) 
        THEN 1 
    END
    ) AS `RM_East_assign_funnel`,
    COUNT(
    CASE 
        WHEN $mainBDFilterQuery = u1.user_id 
            AND (init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) 
        THEN 1 
    END
    ) AS `RM_North_assign_funnel`,
    COUNT(
    CASE 
        WHEN $mainBDFilterQuery = u1.user_id 
            AND (init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Cluster_Manager_assign_funnel`,

   COUNT(CASE WHEN ($mainBDInsiseSalesFilterQuery = u1.user_id OR (init_call.insidebd IS NOT NULL AND init_call.insidebd != 0 )) THEN 1 END) AS insidebd_assign_funnel

FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery $mainBDInsideFilterQuery
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_assign = $totalSiignQuerysByAdminRoles->result();

$totalFunnelByUserQuery = $this->db->query("SELECT
        u1.name AS user_name,
        u1.user_id AS current_user_id,
        COUNT(init_call.id) AS total,
        -- Funnel Statuses
        COUNT(CASE WHEN init_call.cstatus = 1 THEN 1 END) AS open,
        COUNT(CASE WHEN init_call.cstatus = 2 THEN 1 END) AS reachout,
        COUNT(CASE WHEN init_call.cstatus = 3 THEN 1 END) AS tentative,
        COUNT(CASE WHEN init_call.cstatus = 4 THEN 1 END) AS will_do_later,
        COUNT(CASE WHEN init_call.cstatus = 5 THEN 1 END) AS not_interested,
        COUNT(CASE WHEN init_call.cstatus = 6 THEN 1 END) AS positive,
        COUNT(CASE WHEN init_call.cstatus = 7 THEN 1 END) AS closure,
        COUNT(CASE WHEN init_call.cstatus = 8 THEN 1 END) AS open_rpem,
        COUNT(CASE WHEN init_call.cstatus = 9 THEN 1 END) AS very_positive,
        COUNT(CASE WHEN init_call.cstatus = 10 THEN 1 END) AS ttd_reachout,
        COUNT(CASE WHEN init_call.cstatus = 11 THEN 1 END) AS wno_reachout,
        COUNT(CASE WHEN init_call.cstatus = 12 THEN 1 END) AS positive_nap,
        COUNT(CASE WHEN init_call.cstatus = 13 THEN 1 END) AS very_positive_nap,
        COUNT(CASE WHEN (init_call.cstatus = 14 || init_call.upsell_client = 'yes') THEN 1 END) AS on_boarded,
        
        -- Flags
        COUNT(CASE WHEN init_call.focus_funnel = 'yes' THEN 1 END) AS Focus_Funnel,
        COUNT(CASE WHEN init_call.upsell_client = 'yes' THEN 1 END) AS Upsell_Client,
        COUNT(CASE WHEN init_call.keycompany = 'yes' THEN 1 END) AS Key_Client,
        COUNT(CASE WHEN init_call.pkclient = 'yes' THEN 1 END) AS Positive_Key_Client,
        COUNT(CASE WHEN init_call.priorityc = 'yes' THEN 1 END) AS Priority_Calling,
        COUNT(CASE WHEN init_call.topspender = 'yes' THEN 1 END) AS Top_Spender,
        COUNT(CASE WHEN init_call.potential = 'yes' THEN 1 END) AS Potential_Client,
        -- Use topspender or another field for potential, not same
        -- Year-Based Funnels
        COUNT(CASE WHEN init_call.q1_twetenty_closure_funnel = '$cfyear' THEN 1 END) AS q1_twetenty_closure_funnel,
        COUNT(CASE WHEN init_call.potential_funnel_for_fy = '$cfyear' THEN 1 END) AS potential_funnel_for_fy,
        COUNT(CASE WHEN init_call.to_be_nurtured_for_fy = '$cfyear' THEN 1 END) AS to_be_nurtured_for_fy,
        COUNT(CASE WHEN init_call.fifity_new_lead_funnel = '$cfyear' THEN 1 END) AS fifity_new_lead_funnel,
        COUNT(CASE WHEN init_call.anchor_clients = 'yes' THEN 1 END) AS anchor_clients,
        COUNT(CASE WHEN init_call.in_quarter = '20 Closure Funnel in $currentQuarter' THEN 1 END) AS current_quarter_twetenty_closure_funnel,
        COUNT(CASE WHEN init_call.in_quarter = 'Potential Funnel For $currentQuarter' THEN 1 END) AS current_quarter_potential_funnel_for_fy,
        COUNT(CASE WHEN init_call.in_quarter = 'To be Nurtured for $currentQuarter' THEN 1 END) AS current_quarter_to_be_nurtured_for_fy,

        COUNT(CASE WHEN init_call.prospecting_funnel = '$currentQuarter' THEN 1 END) AS total_prospecting_funnel,
        COUNT(CASE WHEN init_call.proposal_to_be_sent_review = '$currentQuarter' THEN 1 END) AS total_proposal_to_be_sent_review,
        COUNT(CASE WHEN init_call.proposal_to_be_sent_target = '$currentQuarter' THEN 1 END) AS total_proposal_to_be_sent_target,
        COUNT(CASE WHEN init_call.closure_pipeline = '$currentQuarter' THEN 1 END) AS total_closure_pipeline,
        -- Assignment Status
        COUNT(CASE WHEN $mainBDFilterQuery = u1.user_id AND (init_call.apst IS NOT NULL AND init_call.apst != 0) THEN 1 END) AS PST_assign_funnel,
        COUNT(CASE WHEN $mainBDFilterQuery = u1.user_id AND (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0) THEN 1 END) AS cluster_manager_assign_funnel,
        COUNT(CASE 
            WHEN $mainBDFilterQuery = u1.user_id AND 
                ((init_call.apst IS NOT NULL AND init_call.apst != 0) AND 
                (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0)) 
            THEN 1 
        END) AS PST_and_cluster_manager_assign_funnel,
        COUNT(
        CASE 
            WHEN $mainBDFilterQuery = u1.user_id 
                AND (init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Sales_Head_NAE_assign_funnel`,
        COUNT(
        CASE 
            WHEN $mainBDFilterQuery = u1.user_id 
                AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Sales_Head_W_assign_funnel`,
        COUNT(
        CASE 
            WHEN $mainBDFilterQuery = u1.user_id 
                AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Sales_Head_S_assign_funnel`,
        COUNT(
        CASE 
            WHEN $mainBDFilterQuery = u1.user_id 
                AND (init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) 
            THEN 1 
        END
        ) AS `RM_East_assign_funnel`,
        COUNT(
        CASE 
            WHEN $mainBDFilterQuery = u1.user_id 
                AND (init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) 
            THEN 1 
        END
        ) AS `RM_North_assign_funnel`,
        COUNT(
        CASE 
            WHEN $mainBDFilterQuery = u1.user_id 
                AND (init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Cluster_Manager_assign_funnel`

    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
    WHERE $text AND u1.status = 'active'
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$total_funnel_by_user = $totalFunnelByUserQuery->result();
 $totalPartnerQuerysByAdminRoles = $this->db->query("SELECT
 
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN partner_master.id = 1 THEN 1 END) AS corporate,
    COUNT(CASE WHEN partner_master.id = 2 THEN 1 END) AS PSU,
    COUNT(CASE WHEN partner_master.id = 3 THEN 1 END) AS NGO,
    COUNT(CASE WHEN partner_master.id = 4 THEN 1 END) AS Private_School,
    COUNT(CASE WHEN partner_master.id = 6 THEN 1 END) AS Individual,
    COUNT(CASE WHEN partner_master.id = 8 THEN 1 END) AS Govt_Off,
    COUNT(CASE WHEN partner_master.id = 9 THEN 1 END) AS Other,
    COUNT(CASE WHEN partner_master.id = 10 THEN 1 END) AS Online,
    COUNT(CASE WHEN partner_master.id = 11 THEN 1 END) AS STEM_School,
    COUNT(CASE WHEN partner_master.id = 12 THEN 1 END) AS Foundation,
    COUNT(CASE WHEN partner_master.id = 13 THEN 1 END) AS MNC,
    COUNT(CASE WHEN partner_master.id = 14 THEN 1 END) AS HO_Leads,
    COUNT(CASE WHEN partner_master.id = 16 THEN 1 END) AS DMFT,
    COUNT(CASE WHEN partner_master.id = 17 THEN 1 END) AS Elected_Representatives
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
WHERE
    $text AND u1.status = 'active' 
");
$total_funnels_partner = $totalPartnerQuerysByAdminRoles->result();
$totalPartnerUserQuerysByAdminRoles = $this->db->query("SELECT
 u1.name AS username,
        u1.user_id AS user_id,
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN partner_master.id = 1 THEN 1 END) AS corporate,
    COUNT(CASE WHEN partner_master.id = 2 THEN 1 END) AS PSU,
    COUNT(CASE WHEN partner_master.id = 3 THEN 1 END) AS NGO,
    COUNT(CASE WHEN partner_master.id = 4 THEN 1 END) AS Private_School,
    COUNT(CASE WHEN partner_master.id = 6 THEN 1 END) AS Individual,
    COUNT(CASE WHEN partner_master.id = 8 THEN 1 END) AS Govt_Off,
    COUNT(CASE WHEN partner_master.id = 9 THEN 1 END) AS Other,
    COUNT(CASE WHEN partner_master.id = 10 THEN 1 END) AS Online,
    COUNT(CASE WHEN partner_master.id = 11 THEN 1 END) AS STEM_School,
    COUNT(CASE WHEN partner_master.id = 12 THEN 1 END) AS Foundation,
    COUNT(CASE WHEN partner_master.id = 13 THEN 1 END) AS MNC,
    COUNT(CASE WHEN partner_master.id = 14 THEN 1 END) AS HO_Leads,
    COUNT(CASE WHEN partner_master.id = 16 THEN 1 END) AS DMFT,
    COUNT(CASE WHEN partner_master.id = 17 THEN 1 END) AS Elected_Representatives
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
WHERE
    $text AND u1.status = 'active' GROUP BY u1.user_id ORDER BY u1.name ASC
");
$total_funnels_partner_user = $totalPartnerUserQuerysByAdminRoles->result();
 $totalCluster = $this->db->query("SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    -- SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            -- WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND cluster.travelType IS NULL THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL");
$totalClusterDatas = $totalCluster->result();
 $totalClusterBYBD = $this->db->query("SELECT
    categorized.user_id,
    categorized.username,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND cluster.travelType IS NULL THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL
GROUP BY categorized.user_id, categorized.username");
$totalClusterBYBDatas = $totalClusterBYBD->result();
 $totalClusterBYClusterName = $this->db->query("SELECT
	categorized.cluster_id,
    categorized.clustername,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        cluster.clustername,
    	cluster.id as cluster_id,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND (cluster.travelType IS NULL OR cluster.travelType = '') THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL AND categorized.cluster_id IS  NOT NULL
GROUP BY categorized.clustername");
$totalClusterBYClusterNameDatas = $totalClusterBYClusterName->result();



 $totalClusterBYClusterNameBYBD = $this->db->query("SELECT
	categorized.user_id,
    categorized.username,
	categorized.cluster_id,
    categorized.clustername,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        cluster.clustername,
    	cluster.id as cluster_id,

        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND (cluster.travelType IS NULL OR cluster.travelType = '') THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL AND categorized.cluster_id IS  NOT NULL
GROUP BY categorized.user_id, categorized.username,categorized.clustername");
$totalClusterBYClusterNameBYBDDatas = $totalClusterBYClusterNameBYBD->result();



 $totalClusterBYStatus = $this->db->query("SELECT
    categorized.status_id,
    categorized.status_name,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        cluster.clustername,
        cluster.id AS cluster_id,
        status.id AS status_id,
        status.name AS status_name,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    LEFT JOIN status ON status.id = init_call.cstatus
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL
GROUP BY categorized.status_id, categorized.status_name");
$totalClusterBYStatusDatas = $totalClusterBYStatus->result();
    $result = [
        'total_funnels_status'              => $total_funnels_status,
        'total_funnels_old_category'        => $total_funnels_old_category,
        'total_funnels_new_category'        => $total_funnels_new_category,
        'total_funnels_assign'              => $total_funnels_assign,
        'total_funnel_by_user'              => $total_funnel_by_user,
        'total_funnels_partner'             => $total_funnels_partner,
        'total_funnels_partner_user'        => $total_funnels_partner_user,
        'total_current_quarter'             => $total_current_quarter,
        'totalClusterDatas'                 => $totalClusterDatas,
        'totalClusterBYBDatas'              => $totalClusterBYBDatas,
        'totalClusterBYClusterNameDatas'    => $totalClusterBYClusterNameDatas,
        'totalClusterBYClusterNameBYBDDatas'=> $totalClusterBYClusterNameBYBDDatas,
        'totalClusterBYStatusDatas'=> $totalClusterBYStatusDatas
    ];
    return $result;
}



public function GetFunnelDetails($userid,$mtypes,$userwise){
    

    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    $inside_sales = $udetail[0]->inside_sales;
    
   if($type_id == 1){
        $text = "u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 25){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}

        if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }

        $mainBDInsideFilterQuery = '';
        if($inside_sales == 'yes'){
            $mainBDInsideFilterQuery = " OR init_call.insidebd = '$userid'";
        }


        //  $mainBDFilterQuery = 'init_call.mainbd';
 
    $curFinancialDate           = $this->Menu_model->getFinancialYearRange();
    $start_financial_date       = $curFinancialDate['start_date'];
    $end_financial_date         = $curFinancialDate['end_date'];
    $start_financial_date       = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData                     = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter             = "Q".$cfData['quarter'];
    $groupfilter                = '';
    $userActiveStatus           = " AND u1.status = 'active'";
 
    if($mtypes == 'total'){
        $filter = '';
    }else if($mtypes == 'open'){
        $filter = 'AND init_call.cstatus = 1';
    }else if($mtypes == 'reachout'){
        $filter = 'AND init_call.cstatus = 2';
    }else if($mtypes == 'tentative'){
        $filter = 'AND init_call.cstatus = 3';
    }else if($mtypes == 'will_do_later'){
        $filter = 'AND init_call.cstatus = 4';
    }else if($mtypes == 'not_interested'){
        $filter = 'AND init_call.cstatus = 5';
    }else if($mtypes == 'positive'){
        $filter = 'AND init_call.cstatus = 6';
    }else if($mtypes == 'closure'){
        $filter = 'AND init_call.cstatus = 7';
    }else if($mtypes == 'open_rpem'){
        $filter = 'AND init_call.cstatus = 8';
    }else if($mtypes == 'very_positive'){
        $filter = 'AND init_call.cstatus = 9';
    }else if($mtypes == 'ttd_reachout'){
        $filter = 'AND init_call.cstatus = 10';
    }else if($mtypes == 'ttd_reachout'){
        $filter = 'AND init_call.cstatus = 10';
    }else if($mtypes == 'wno_reachout'){
        $filter = 'AND init_call.cstatus = 11';
    }else if($mtypes == 'positive_nap'){
        $filter = 'AND init_call.cstatus = 12';
    }else if($mtypes == 'very_positive_nap'){
        $filter = 'AND init_call.cstatus = 13';
    }else if($mtypes == 'on_boarded'){
        $filter = "AND (init_call.cstatus = 14 || init_call.upsell_client = 'yes')";
    }else if($mtypes == 'Focus_Funnel'){
        $filter = "AND init_call.focus_funnel = 'yes'";
    }else if($mtypes == 'Upsell_Client'){
        $filter = "AND init_call.upsell_client = 'yes'";
    }else if($mtypes == 'Key_Client'){
        $filter = "AND init_call.keycompany = 'yes'";
    }else if($mtypes == 'Positive_Key_Client'){
        $filter = "AND init_call.pkclient = 'yes'";
    }else if($mtypes == 'Priority_Calling'){
        $filter = "AND init_call.priorityc = 'yes'";
    }else if($mtypes == 'Top_Spender'){
        $filter = "AND init_call.topspender = 'yes'";
    }else if($mtypes == 'Potential_Client'){
        $filter = "AND init_call.potential = 'yes'";
    }else if($mtypes == 'q1_twetenty_closure_funnel'){
        $filter = "AND init_call.q1_twetenty_closure_funnel = '$year'";
    }else if($mtypes == 'potential_funnel_for_fy'){
        $filter = "AND init_call.potential_funnel_for_fy = '$year'";
    }else if($mtypes == 'to_be_nurtured_for_fy'){
        $filter = "AND init_call.to_be_nurtured_for_fy = '$year'";
    }else if($mtypes == 'fifity_new_lead_funnel'){
        $filter = "AND init_call.fifity_new_lead_funnel = '$year'";
    }else if($mtypes == 'PST_assign_funnel'){
        $filter = "AND (init_call.apst IS NOT NULL AND init_call.apst != 0)";
    }else if($mtypes == 'cluster_manager_assign_funnel'){
        $filter = "AND (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0)";
    }else if($mtypes == 'PST_and_cluster_manager_assign_funnel'){
        $filter = "AND  ((init_call.apst IS NOT NULL AND init_call.apst != 0) AND (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0))";
    }else if($mtypes == 'Assistant_Sales_Head_NAE_assign_funnel'){
        $filter = "AND (init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0)";
    }else if($mtypes == 'Assistant_Sales_Head_W_assign_funnel'){
        $filter = "AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) ";
    }else if($mtypes == 'Assistant_Sales_Head_S_assign_funnel'){
        $filter = "AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) ";
    }else if($mtypes == 'RM_East_assign_funnel'){
        $filter = "AND (init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) ";
    }else if($mtypes == 'RM_North_assign_funnel'){
        $filter = "AND (init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) ";
    }else if($mtypes == 'Assistant_Cluster_Manager_assign_funnel'){
        $filter = "AND (init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0) ";
    } else if ($mtypes == 'corporate') {
        $filter = " AND partner_master.id = 1";
    }else if ($mtypes == 'PSU') {
        $filter = " AND partner_master.id = 2";
    }else if ($mtypes == 'NGO') {
        $filter = " AND partner_master.id = 3";
    } else if ($mtypes == 'Private_School') {
        $filter = " AND partner_master.id = 4";
    } else if ($mtypes == 'Individual') {
        $filter = " AND partner_master.id = 6";
    } else if ($mtypes == 'Govt_Off') {
        $filter = " AND partner_master.id = 8";
    } else if ($mtypes == 'Other') {
        $filter = " AND partner_master.id = 9";
    } else if ($mtypes == 'Online') {
        $filter = " AND partner_master.id = 10";
    } else if ($mtypes == 'STEM_School') {
        $filter = " AND partner_master.id = 11";
    } else if ($mtypes == 'Foundation') {
        $filter = " AND partner_master.id = 12";
    } else if ($mtypes == 'MNC') {
        $filter = " AND partner_master.id = 13";
    } else if ($mtypes == 'HO_Leads') {
        $filter = " AND partner_master.id = 14";
    } else if ($mtypes == 'DMFT') {
        $filter = " AND  partner_master.id = 16";
    }else if ($mtypes == 'Elected_Representatives') {
        $filter = " AND  partner_master.id = 17";
    }else if ($mtypes == 'current_quarter_twetenty_closure_funnel') {
        $filter         = "AND init_call.in_quarter = '20 Closure Funnel in $currentQuarter'";
        $groupfilter    = 'GROUP BY company_master.id';
    }else if ($mtypes == 'current_quarter_potential_funnel_for_fy') {
        $filter         = "AND init_call.in_quarter = 'Potential Funnel For $currentQuarter'";
        $groupfilter    = 'GROUP BY company_master.id';
    }else if ($mtypes == 'current_quarter_to_be_nurtured_for_fy') {
        $filter         = "AND init_call.in_quarter = 'To be Nurtured for $currentQuarter'";
        $groupfilter    = 'GROUP BY company_master.id';
    }else if ($mtypes == 'anchor_clients') {
        $filter         = "AND init_call.anchor_clients = 'yes'";
        $groupfilter    = 'GROUP BY company_master.id';
    }else if ($mtypes == 'total_prospecting_funnel') {
        $filter         = "AND init_call.prospecting_funnel = '$currentQuarter'";
        $groupfilter    = 'GROUP BY company_master.id';
    }else if ($mtypes == 'total_proposal_to_be_sent_review') {
        $filter         = "AND init_call.proposal_to_be_sent_review = '$currentQuarter'";
        $groupfilter    = 'GROUP BY company_master.id';
    }else if ($mtypes == 'total_proposal_to_be_sent_target') {
        $filter         = "AND init_call.proposal_to_be_sent_target = '$currentQuarter'";
        $groupfilter    = 'GROUP BY company_master.id';
    } else if ($mtypes == 'total_closure_pipeline') {
        $filter         = "AND init_call.closure_pipeline = '$currentQuarter'";
        $groupfilter    = 'GROUP BY company_master.id';
    }else if ($mtypes == 'need_to_be_monitored') {
        $filter         = "AND init_call.need_to_be_monitored IS NOT NULL";
        $groupfilter    = 'GROUP BY company_master.id';
    }
    
    else if($mtypes == 'totalByBDDetailsPage'){
        $filter             = '';
        $userActiveStatus   = '';
    }

    else if ($mtypes == 'insidebd_assign_funnel') {
        $filter         = "AND (init_call.insidebd  IS NOT NULL AND init_call.insidebd != 0 )";
        $groupfilter    = 'GROUP BY company_master.id';
    }


   $groupfilter    = 'GROUP BY company_master.id';
   $query = $this->db->query("SELECT
    company_master.id AS cid,
    company_master.compname,
    company_master.state as company_state,
    company_master.district as company_district,
    company_master.city as company_city,
    company_master.address as company_address,
    init_call.createDate AS created_date,
    s1.name as current_status,
    s2.name as last_status,
    u1.name as mainbd_name,
    u2.name as cluster_manager_name,
    u3.name as pst_name,
    u4.name as ash_nae_co_id_name,
    u5.name as ash_w_co_id_name,
    u6.name as ash_s_co_id_name,
    u7.name as rm_east_co_id_name,
    u8.name as rm_north_co_id_name,
    u9.name as acm_co_id_name,
    u15.name as inside_bd_name,
    init_call.topspender,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.anchor_clients,
    init_call.in_quarter,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.potential,
    init_call.q1_twetenty_closure_funnel,
    init_call.potential_funnel_for_fy,
    init_call.to_be_nurtured_for_fy,
    init_call.fifity_new_lead_funnel,
    init_call.need_to_be_monitored,
    partner_master.name as partner_name,
    cluster.clustername,
    cluster.travelType as travelType,
    cluster.id as cluster_id,
    u10.name as travel_cluster_create_name,
    tblc.actiontype_id as task_action_id,
    action.name as compaany_createby_task_name,
    tblc.mtype as compaany_createby_mtype,
    init_call.after_task as compaany_after_task,
  CONCAT_WS(' | ',
        CONCAT('<b>Name :</b>', ccm.contactperson),
        CONCAT('<b>Email : </b>', ccm.emailid),
        CONCAT('<b>Phone : </b>', ccm.phoneno),
        CONCAT('<b>Type : </b>', ccm.type)
    ) AS contact_details,
    last_tblc.id as last_tblc_id,
    last_tblc.nextCFID as last_tblc_nextCFID,
    last_tblc.remarks as last_tblc_remarks,
    last_tblc.delete_request as last_tblc_delete_request,
    last_tblc.actiontype_id as last_tblc_actiontype_id,
    last_tblc.special_remarks as last_tblc_special_remarks,
    last_tblc.mom as last_tblc_mom, 
    last_tblc.mtype as last_tblc_mtype, 
    last_tblc.mom_remarks as last_tblc_mom_remarks, 
    last_tblc.appointmentdatetime as last_tblc_date, 
    a2.name as last_tblc_task,
    meet.id as last_meet_id,
    meet.mtype as last_meet_mtype,
    u11.name as last_meet_username,
    mom_data.approved_status as mom_approved_status,
    proposal.proattach as proposal_attach,
    proposal.apr as proposal_apr,
    proposal.id as proposal_id,
    proposal.tid as proposal_tid,
    cbm.potentional_client as cbm_potentional_client,
    init_call.super_admin as super_admin,
    u12.name as super_admin_name,
    u13.name as assign_support_name,
    u14.name as leads_main_bd_name,
    u1.user_cluster_zone 
FROM
    init_call
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id  AND ccm.type = 'primary'
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status s1 ON s1.id = init_call.cstatus
LEFT JOIN status s2 ON s2.id = init_call.lstatus
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery $mainBDInsideFilterQuery
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
LEFT JOIN cluster ON cluster.id = init_call.cluster_id
LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
LEFT JOIN user_details u12 ON u12.user_id = init_call.super_admin
LEFT JOIN user_details u13 ON u13.user_id = init_call.assign_support
LEFT JOIN user_details u14 ON u14.user_id = init_call.mainbd
LEFT JOIN user_details u15 ON u15.user_id  = init_call.insidebd 
LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
LEFT JOIN action ON action.id = tblc.actiontype_id
LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id
LEFT JOIN tblcallevents meet ON meet.cid_id = init_call.id  AND meet.nextCFID !=0 AND meet.actiontype_id IN(3,4) 
AND cast(meet.appointmentdatetime as Date) >= '$start_financial_date' 
-- AND YEAR(meet.appointmentdatetime) = '$year' 
AND (meet.mtype = 'RP' OR meet.mtype = 'RPClose' OR meet.mtype = 'Change RP')
LEFT JOIN user_details u11 ON u11.user_id = meet.user_id
LEFT JOIN tblcallevents mom_tblc ON mom_tblc.aftertask = meet.id AND mom_tblc.actiontype_id = 6
LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 
LEFT JOIN proposal ON proposal.init_id = init_call.id AND cast(proposal.sdatet as Date) >= '$start_financial_date' 
LEFT JOIN (
    SELECT t1.*
    FROM tblcallevents t1
    INNER JOIN (
        SELECT cid_id, MAX(id) AS max_id
        FROM tblcallevents
        GROUP BY cid_id
    ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
) AS last_tblc ON last_tblc.cid_id = init_call.id
LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id
WHERE 
     $text $filter $userActiveStatus $groupfilter");
    return $query->result(); 
}
public function GetFunnelDetailsWithCluster($userid,$mtypes,$userwise){
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}
 
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];
    $filter = '';
    $groupfilter = 'GROUP BY init_call.id';
    if ($mtypes == 'base_location') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL 
            AND init_call.cluster_id != 0 AND 
            cluster.travelType = 'base'
        )";
    } elseif ($mtypes == 'outStation_location') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL AND 
            init_call.cluster_id != 0 AND 
            cluster.travelType = 'outStation'
        )";
    } elseif ($mtypes == 'marked_travel_cluster_but_base_out_not_set') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL AND cluster.id IS NOT NULL 
            AND init_call.cluster_id != 0 
            AND (cluster.travelType IS NULL OR cluster.travelType = '')
        )";
    } elseif ($mtypes == 'not_set_travel_cluster') {
        $filter = "AND (
            init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL 
        )";
    }
    $query = $this->db->query("SELECT
        categorized.cid,
        categorized.compname,
        categorized.created_date,
        categorized.current_status,
        categorized.last_status,
        categorized.mainbd_name,
        categorized.cluster_manager_name,
        categorized.pst_name,
        categorized.ash_nae_co_id_name,
        categorized.ash_w_co_id_name,
        categorized.ash_s_co_id_name,
        categorized.rm_east_co_id_name,
        categorized.rm_north_co_id_name,
        categorized.acm_co_id_name,
        categorized.topspender,
        categorized.upsell_client,
        categorized.focus_funnel,
        categorized.anchor_clients,
        categorized.in_quarter,
        categorized.keycompany,
        categorized.pkclient,
        categorized.priorityc,
        categorized.potential,
        categorized.q1_twetenty_closure_funnel,
        categorized.potential_funnel_for_fy,
        categorized.to_be_nurtured_for_fy,
        categorized.fifity_new_lead_funnel,
        categorized.partner_name,
        categorized.clustername,
        categorized.travelType,
        categorized.cluster_id,
        categorized.travel_cluster_create_name,
        categorized.task_action_id,
        categorized.compaany_createby_task_name,
        categorized.compaany_createby_mtype,
        categorized.compaany_after_task,
        categorized.contact_details,
        categorized.last_tblc_id,
        categorized.last_tblc_nextCFID,
        categorized.last_tblc_remarks,
        categorized.last_tblc_delete_request,
        categorized.last_tblc_actiontype_id,
        categorized.last_tblc_special_remarks,
        categorized.last_tblc_mom,
        categorized.last_tblc_mtype,
        categorized.last_tblc_mom_remarks,
        categorized.last_tblc_date,
        categorized.last_tblc_task,
        categorized.last_meet_id,
        categorized.last_meet_mtype,
        categorized.last_meet_username,
        categorized.mom_approved_status,
        categorized.proposal_attach,
        categorized.proposal_apr,
        categorized.proposal_id,
        categorized.proposal_tid,
        categorized.cbm_potentional_client
    FROM (
        SELECT DISTINCT init_call.id,
            company_master.id AS cid,
            company_master.compname,
            init_call.createDate AS created_date,
            s1.name AS current_status,
            s2.name AS last_status,
            u1.name AS mainbd_name,
            u2.name AS cluster_manager_name,
            u3.name AS pst_name,
            u4.name AS ash_nae_co_id_name,
            u5.name AS ash_w_co_id_name,
            u6.name AS ash_s_co_id_name,
            u7.name AS rm_east_co_id_name,
            u8.name AS rm_north_co_id_name,
            u9.name AS acm_co_id_name,
            init_call.topspender,
            init_call.upsell_client,
            init_call.focus_funnel,
            init_call.anchor_clients,
            init_call.in_quarter,
            init_call.keycompany,
            init_call.pkclient,
            init_call.priorityc,
            init_call.potential,
            init_call.q1_twetenty_closure_funnel,
            init_call.potential_funnel_for_fy,
            init_call.to_be_nurtured_for_fy,
            init_call.fifity_new_lead_funnel,
            partner_master.name AS partner_name,
            cluster.clustername,
            cluster.travelType,
            cluster.id AS cluster_id,
            u10.name AS travel_cluster_create_name,
            tblc.actiontype_id AS task_action_id,
            action.name AS compaany_createby_task_name,
            tblc.mtype AS compaany_createby_mtype,
            init_call.after_task AS compaany_after_task,
            CONCAT_WS(' | ',
                CONCAT('<b>Name :</b>', ccm.contactperson),
                CONCAT('<b>Email : </b>', ccm.emailid),
                CONCAT('<b>Phone : </b>', ccm.phoneno),
                CONCAT('<b>Type : </b>', ccm.type)
            ) AS contact_details,
            last_tblc.id AS last_tblc_id,
            last_tblc.nextCFID AS last_tblc_nextCFID,
            last_tblc.remarks AS last_tblc_remarks,
            last_tblc.delete_request AS last_tblc_delete_request,
            last_tblc.actiontype_id AS last_tblc_actiontype_id,
            last_tblc.special_remarks AS last_tblc_special_remarks,
            last_tblc.mom AS last_tblc_mom, 
            last_tblc.mtype AS last_tblc_mtype, 
            last_tblc.mom_remarks AS last_tblc_mom_remarks, 
            last_tblc.appointmentdatetime AS last_tblc_date, 
            a2.name AS last_tblc_task,
            meet.id AS last_meet_id,
            meet.mtype AS last_meet_mtype,
            u11.name AS last_meet_username,
            mom_data.approved_status AS mom_approved_status,
            proposal.proattach AS proposal_attach,
            proposal.apr AS proposal_apr,
            proposal.id AS proposal_id,
            proposal.tid AS proposal_tid,
            cbm.potentional_client AS cbm_potentional_client,
            CASE 
                WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
                WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
                WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND (cluster.travelType IS NULL OR cluster.travelType = '') THEN 'marked_but_base_out_not_set'
                WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 THEN 'not_set_cluster'
            END AS travel_category
        FROM init_call
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id AND ccm.type = 'primary'
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        LEFT JOIN status s1 ON s1.id = init_call.cstatus
        LEFT JOIN status s2 ON s2.id = init_call.lstatus
        LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
        LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
        LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
        LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
        LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
        LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
        LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
        LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
        LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
        LEFT JOIN cluster ON cluster.id = init_call.cluster_id
        LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
        LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
        LEFT JOIN action ON action.id = tblc.actiontype_id
        LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id
        LEFT JOIN tblcallevents meet ON meet.cid_id = init_call.id  
            AND meet.nextCFID != 0 
            AND meet.actiontype_id IN (3, 4) 
            AND CAST(meet.appointmentdatetime AS DATE) >= '$start_financial_date'
            AND (meet.mtype = 'RP' OR meet.mtype = 'RPClose' OR meet.mtype = 'Change RP')
        LEFT JOIN user_details u11 ON u11.user_id = meet.user_id
        LEFT JOIN tblcallevents mom_tblc ON mom_tblc.aftertask = meet.id AND mom_tblc.actiontype_id = 6
        LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 
        LEFT JOIN proposal ON proposal.init_id = init_call.id AND CAST(proposal.sdatet AS DATE) >= '$start_financial_date'
        LEFT JOIN (
            SELECT t1.*
            FROM tblcallevents t1
            INNER JOIN (
                SELECT cid_id, MAX(id) AS max_id
                FROM tblcallevents
                GROUP BY cid_id
            ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
        ) AS last_tblc ON last_tblc.cid_id = init_call.id
        LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id
        WHERE $text $filter AND u1.status = 'active'
        $groupfilter
    ) AS categorized");
    return $query->result(); 
}


public function GetFunnelDetailsWithClusterID($userid,$mtypes,$clusterID,$userwise,$status_id){
    if($userwise == 'status'){
        $status_id = 'status';
    }
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];
    $filter = '';
    $groupfilter = 'GROUP BY init_call.id';
    if(is_null($status_id)){
    
   if ($mtypes == 'total') {
        $filter = "AND (
            init_call.cluster_id = '$clusterID'
        )";
    }else  if ($mtypes == 'base_location') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL AND init_call.cluster_id = '$clusterID'
            AND init_call.cluster_id != 0 AND 
            cluster.travelType = 'base'
        )";
    } elseif ($mtypes == 'outStation_location') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL 
            AND  init_call.cluster_id = '$clusterID' 
            AND init_call.cluster_id != 0 
            AND cluster.travelType = 'outStation'
        )";
    } elseif ($mtypes == 'marked_travel_cluster_but_base_out_not_set') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL AND cluster.id IS NOT NULL 
            AND init_call.cluster_id != 0 
            AND init_call.cluster_id = '$clusterID'
            AND (cluster.travelType IS NULL OR cluster.travelType = '')
        )";
    } elseif ($mtypes == 'not_set_travel_cluster') {
        $filter = "AND (
            init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL 
        )";
    }
}else if ($status_id == 'status') { 
    $status_id = $clusterID;
     if ($mtypes == 'total') {
        $filter = "AND (
            init_call.cstatus = '$status_id'
        )";
    }else  if ($mtypes == 'base_location') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL AND init_call.cstatus = '$status_id'
            AND init_call.cluster_id != 0 AND 
            cluster.travelType = 'base'
        )";
    } elseif ($mtypes == 'outStation_location') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL 
            AND init_call.cstatus = '$status_id'
            AND init_call.cluster_id != 0 
            AND cluster.travelType = 'outStation'
        )";
    } elseif ($mtypes == 'marked_travel_cluster_but_base_out_not_set') {
        $filter = "AND (
            init_call.cluster_id IS NOT NULL AND cluster.id IS NOT NULL 
            AND init_call.cluster_id != 0 
            AND init_call.cstatus = '$status_id'
            AND (cluster.travelType IS NULL OR cluster.travelType = '')
        )";
    } elseif ($mtypes == 'not_set_travel_cluster') {
        $filter = "AND (
            init_call.cstatus = '$status_id' AND (init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL )
        )";
    }
}
    $query = $this->db->query("SELECT
        categorized.cid,
        categorized.compname,
        categorized.created_date,
        categorized.current_status,
        categorized.last_status,
        categorized.mainbd_name,
        categorized.cluster_manager_name,
        categorized.pst_name,
        categorized.ash_nae_co_id_name,
        categorized.ash_w_co_id_name,
        categorized.ash_s_co_id_name,
        categorized.rm_east_co_id_name,
        categorized.rm_north_co_id_name,
        categorized.acm_co_id_name,
        categorized.topspender,
        categorized.upsell_client,
        categorized.focus_funnel,
        categorized.anchor_clients,
        categorized.in_quarter,
        categorized.keycompany,
        categorized.pkclient,
        categorized.priorityc,
        categorized.potential,
        categorized.q1_twetenty_closure_funnel,
        categorized.potential_funnel_for_fy,
        categorized.to_be_nurtured_for_fy,
        categorized.fifity_new_lead_funnel,
        categorized.partner_name,
        categorized.clustername,
        categorized.travelType,
        categorized.cluster_id,
        categorized.travel_cluster_create_name,
        categorized.task_action_id,
        categorized.compaany_createby_task_name,
        categorized.compaany_createby_mtype,
        categorized.compaany_after_task,
        categorized.contact_details,
        categorized.last_tblc_id,
        categorized.last_tblc_nextCFID,
        categorized.last_tblc_remarks,
        categorized.last_tblc_delete_request,
        categorized.last_tblc_actiontype_id,
        categorized.last_tblc_special_remarks,
        categorized.last_tblc_mom,
        categorized.last_tblc_mtype,
        categorized.last_tblc_mom_remarks,
        categorized.last_tblc_date,
        categorized.last_tblc_task,
        categorized.last_meet_id,
        categorized.last_meet_mtype,
        categorized.last_meet_username,
        categorized.mom_approved_status,
        categorized.proposal_attach,
        categorized.proposal_apr,
        categorized.proposal_id,
        categorized.proposal_tid,
        categorized.cbm_potentional_client
    FROM (
        SELECT DISTINCT init_call.id,
            company_master.id AS cid,
            company_master.compname,
            init_call.createDate AS created_date,
            s1.name AS current_status,
            s2.name AS last_status,
            u1.name AS mainbd_name,
            u2.name AS cluster_manager_name,
            u3.name AS pst_name,
            u4.name AS ash_nae_co_id_name,
            u5.name AS ash_w_co_id_name,
            u6.name AS ash_s_co_id_name,
            u7.name AS rm_east_co_id_name,
            u8.name AS rm_north_co_id_name,
            u9.name AS acm_co_id_name,
            init_call.topspender,
            init_call.upsell_client,
            init_call.focus_funnel,
            init_call.anchor_clients,
            init_call.in_quarter,
            init_call.keycompany,
            init_call.pkclient,
            init_call.priorityc,
            init_call.potential,
            init_call.q1_twetenty_closure_funnel,
            init_call.potential_funnel_for_fy,
            init_call.to_be_nurtured_for_fy,
            init_call.fifity_new_lead_funnel,
            partner_master.name AS partner_name,
            cluster.clustername,
            cluster.travelType,
            cluster.id AS cluster_id,
            u10.name AS travel_cluster_create_name,
            tblc.actiontype_id AS task_action_id,
            action.name AS compaany_createby_task_name,
            tblc.mtype AS compaany_createby_mtype,
            init_call.after_task AS compaany_after_task,
            CONCAT_WS(' | ',
                CONCAT('<b>Name :</b>', ccm.contactperson),
                CONCAT('<b>Email : </b>', ccm.emailid),
                CONCAT('<b>Phone : </b>', ccm.phoneno),
                CONCAT('<b>Type : </b>', ccm.type)
            ) AS contact_details,
            last_tblc.id AS last_tblc_id,
            last_tblc.nextCFID AS last_tblc_nextCFID,
            last_tblc.remarks AS last_tblc_remarks,
            last_tblc.delete_request AS last_tblc_delete_request,
            last_tblc.actiontype_id AS last_tblc_actiontype_id,
            last_tblc.special_remarks AS last_tblc_special_remarks,
            last_tblc.mom AS last_tblc_mom, 
            last_tblc.mtype AS last_tblc_mtype, 
            last_tblc.mom_remarks AS last_tblc_mom_remarks, 
            last_tblc.appointmentdatetime AS last_tblc_date, 
            a2.name AS last_tblc_task,
            meet.id AS last_meet_id,
            meet.mtype AS last_meet_mtype,
            u11.name AS last_meet_username,
            mom_data.approved_status AS mom_approved_status,
            proposal.proattach AS proposal_attach,
            proposal.apr AS proposal_apr,
            proposal.id AS proposal_id,
            proposal.tid AS proposal_tid,
            cbm.potentional_client AS cbm_potentional_client,
            CASE 
                WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
                WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
                WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND (cluster.travelType IS NULL OR cluster.travelType = '') THEN 'marked_but_base_out_not_set'
                WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 THEN 'not_set_cluster'
            END AS travel_category
        FROM init_call
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id AND ccm.type = 'primary'
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        LEFT JOIN status s1 ON s1.id = init_call.cstatus
        LEFT JOIN status s2 ON s2.id = init_call.lstatus
        LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
        LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
        LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
        LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
        LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
        LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
        LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
        LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
        LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
        LEFT JOIN cluster ON cluster.id = init_call.cluster_id
        LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
        LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
        LEFT JOIN action ON action.id = tblc.actiontype_id
        LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id
        LEFT JOIN tblcallevents meet ON meet.cid_id = init_call.id  
            AND meet.nextCFID != 0 
            AND meet.actiontype_id IN (3, 4) 
            AND CAST(meet.appointmentdatetime AS DATE) >= '$start_financial_date'
            AND (meet.mtype = 'RP' OR meet.mtype = 'RPClose' OR meet.mtype = 'Change RP')
        LEFT JOIN user_details u11 ON u11.user_id = meet.user_id
        LEFT JOIN tblcallevents mom_tblc ON mom_tblc.aftertask = meet.id AND mom_tblc.actiontype_id = 6
        LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 
        LEFT JOIN proposal ON proposal.init_id = init_call.id AND CAST(proposal.sdatet AS DATE) >= '$start_financial_date'
        LEFT JOIN (
            SELECT t1.*
            FROM tblcallevents t1
            INNER JOIN (
                SELECT cid_id, MAX(id) AS max_id
                FROM tblcallevents
                GROUP BY cid_id
            ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
        ) AS last_tblc ON last_tblc.cid_id = init_call.id
        LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id
        WHERE $text $filter AND u1.status = 'active'
        $groupfilter
    ) AS categorized");
    return $query->result(); 
}
public function GetProposalDetailbyDateByRoles($userid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter){
       
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "AND (u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "AND u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "AND u1.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "AND (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "AND (u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "AND (u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "AND (u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "AND (u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "AND u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
               $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "AND (u1.sadmin_id = $userid || u1.user_id = $userid)";
                }else if($type_id == 2){
                    $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
                }else if($type_id == 3){
                    $text = "AND u1.user_id = $userid";
                }else if($type_id == 4){
                    $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "AND u1.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "AND (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
                }else if($type_id == 20){
                    $text = "AND (u1.ash_w_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 21){
                    $text = "AND (u1.ash_s_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 22){
                    $text = "AND (u1.rm_east_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 23){
                    $text = "AND (u1.rm_north_co='$userid' || u1.user_id = $userid)";
                }else if($type_id == 24){
                    $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
                }else{
                    $text = "AND (u1.admin_id = $userid || u1.user_id = $userid)";
                }
        }
    }
        if($task_action_id == 'all'){
            $task_action_filter = "AND (tblc.actiontype_id != 0 OR tblc.actiontype_id != '') AND ((tblc.approved_status = 1 || tblc.approved_status = '')
        AND (tblc.delete_request = '' OR tblc.delete_request IS NULL))";
        }else{
            $task_action_filter = "AND tblc.actiontype_id = $task_action_id AND ((tblc.approved_status = 1 || tblc.approved_status = '')
        AND (tblc.delete_request = '' OR tblc.delete_request IS NULL))";
        }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year = new DateTime($start_financial_date);
    $year = $start_financial_date_year->format('Y');
    $totalProposalTasksQuery = $this->db->query("SELECT
        COUNT(DISTINCT CASE WHEN tblc.cid_id != 0 THEN tblc.cid_id END) AS total_company_task,
        COUNT(CASE WHEN tblc.id THEN 1 END) AS total_proposal_task,
        COUNT(DISTINCT CASE WHEN tblc.cid_id != 0 AND tblc.nextCFID != 0 THEN tblc.cid_id END) AS total_company_complete_proposal_task,
        COUNT(CASE WHEN tblc.nextCFID != 0 THEN 1 END) AS complete_proposal_task,
        COUNT(DISTINCT CASE WHEN tblc.cid_id != 0 AND tblc.nextCFID = 0 THEN tblc.cid_id END) AS total_company_pending_proposal_task,
        COUNT(CASE WHEN tblc.nextCFID = 0 THEN 1 END) AS pending_proposal_task,
        COUNT(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 1 THEN 1 END) AS proposal_approved,
        COUNT(DISTINCT CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 1 THEN tblc.cid_id END) AS total_company_proposal_approved,
        COUNT(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 2 THEN 1 END) AS proposal_reject,
        COUNT(DISTINCT CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 2 THEN tblc.cid_id END) AS total_company_proposal_reject,
        
        COUNT(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 0 THEN 1 END) AS pending_for_approved,
        COUNT(DISTINCT CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 0 THEN tblc.cid_id END) AS total_company_pending_for_approved,
        SUM(CASE WHEN tblc.nextCFID != 0 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_budget,
        SUM(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 1 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_approved_budget,
        SUM(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 0 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_pending_budget,
        SUM(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 2 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_reject_budget,
        -- COUNT(CASE 
        --     WHEN tblc.nextCFID != 0 
        --         AND proposal.apr = 1 
        --         AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (0, 2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --     THEN 1 
        -- END) AS proposal_approved,
        -- COUNT(CASE 
        --     WHEN tblc.nextCFID != 0 
        --         AND proposal.apr = 2
        --         AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,0) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --     THEN 1 
        -- END) AS proposal_reject,
        -- COUNT(CASE 
        --     WHEN tblc.nextCFID != 0 
        --         AND proposal.apr = 0
        --         AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --     THEN 1 
        -- END) AS pending_for_approved,
        -- SUM(CASE WHEN tblc.nextCFID != 0 
        --     AND proposal.apr = 1
        --     AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (0, 2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --  THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_approved_budget,
        -- SUM(CASE WHEN tblc.nextCFID != 0 
        -- AND proposal.apr = 0
        -- AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        -- THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_pending_budget,
        -- SUM(CASE WHEN tblc.nextCFID != 0 
        -- AND proposal.apr = 2 
        -- AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,0) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        -- THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_reject_budget,
        -- Total Proposal in Status
        COUNT(CASE WHEN tblc.status_id = 1 AND proposal.apr = 1 THEN 1 END) AS open,
        COUNT(CASE WHEN tblc.status_id = 2 AND proposal.apr = 1 THEN 1 END) AS reachout,
        COUNT(CASE WHEN tblc.status_id = 3 AND proposal.apr = 1 THEN 1 END) AS tentative,
        COUNT(CASE WHEN tblc.status_id = 4 AND proposal.apr = 1 THEN 1 END) AS will_do_later,
        COUNT(CASE WHEN tblc.status_id = 5 AND proposal.apr = 1 THEN 1 END) AS not_interested,
        COUNT(CASE WHEN tblc.status_id = 6 AND proposal.apr = 1 THEN 1 END) AS positive,
        COUNT(CASE WHEN (tblc.status_id = 7 || init_call.upsell_client = 'yes') AND proposal.apr = 1 THEN 1 END) AS closure,
        COUNT(CASE WHEN tblc.status_id = 8 AND proposal.apr = 1 THEN 1 END) AS open_rpem,
        COUNT(CASE WHEN tblc.status_id = 9 AND proposal.apr = 1 THEN 1 END) AS very_positive,
        COUNT(CASE WHEN tblc.status_id = 10 AND proposal.apr = 1 THEN 1 END) AS ttd_reachout,
        COUNT(CASE WHEN tblc.status_id = 11 AND proposal.apr = 1 THEN 1 END) AS wno_reachout,
        COUNT(CASE WHEN tblc.status_id = 12 AND proposal.apr = 1 THEN 1 END) AS positive_nap,
        COUNT(CASE WHEN tblc.status_id = 13 AND proposal.apr = 1 THEN 1 END) AS very_positive_nap,
        COUNT(CASE WHEN tblc.status_id = 14 AND proposal.apr = 1 THEN 1 END) AS on_boarded,
        -- Total Proposal in Status
        -- Total Proposal in Partner
        COUNT(CASE WHEN partner_master.id = 1 AND proposal.apr = 1 THEN 1 END) AS corporate,
        COUNT(CASE WHEN partner_master.id = 2 AND proposal.apr = 1 THEN 1 END) AS PSU,
        COUNT(CASE WHEN partner_master.id = 3 AND proposal.apr = 1 THEN 1 END) AS NGO,
        COUNT(CASE WHEN partner_master.id = 4 AND proposal.apr = 1 THEN 1 END) AS Private_School,
        COUNT(CASE WHEN partner_master.id = 6 AND proposal.apr = 1 THEN 1 END) AS Individual,
        COUNT(CASE WHEN partner_master.id = 8 AND proposal.apr = 1 THEN 1 END) AS Govt_Off,
        COUNT(CASE WHEN partner_master.id = 9 AND proposal.apr = 1 THEN 1 END) AS Other,
        COUNT(CASE WHEN partner_master.id = 10 AND proposal.apr = 1 THEN 1 END) AS Online,
        COUNT(CASE WHEN partner_master.id = 11 AND proposal.apr = 1 THEN 1 END) AS STEM_School,
        COUNT(CASE WHEN partner_master.id = 12 AND proposal.apr = 1 THEN 1 END) AS Foundation,
        COUNT(CASE WHEN partner_master.id = 13 AND proposal.apr = 1 THEN 1 END) AS MNC,
        COUNT(CASE WHEN partner_master.id = 14 AND proposal.apr = 1 THEN 1 END) AS HO_Leads,
        COUNT(CASE WHEN partner_master.id = 16 AND proposal.apr = 1 THEN 1 END) AS DMFT,
        COUNT(CASE WHEN partner_master.id = 17 AND proposal.apr = 1 THEN 1 END) AS Elected_Representatives,
        -- Total Proposal in Partner
        -- Total Proposal Category
        COUNT(CASE WHEN init_call.potential = 'yes' AND proposal.apr = 1 THEN 1 END) AS Potential,
        COUNT(CASE WHEN init_call.topspender = 'yes' AND proposal.apr = 1 THEN 1 END) AS Top_Spender,
        COUNT(CASE WHEN init_call.upsell_client = 'yes' AND proposal.apr = 1 THEN 1 END) AS Upsell_Client,
        COUNT(CASE WHEN init_call.focus_funnel = 'yes' AND proposal.apr = 1 THEN 1 END) AS Focus_Funnel,
        COUNT(CASE WHEN init_call.keycompany = 'yes' AND proposal.apr = 1 THEN 1 END) AS Key_Client,
        COUNT(CASE WHEN init_call.pkclient   = 'yes' AND proposal.apr = 1 THEN 1 END) AS Positive_Key_Client,
        COUNT(CASE WHEN init_call.priorityc = 'yes' AND proposal.apr = 1 THEN 1 END) AS Priority_Calling,
        -- Total Proposal Category
        -- Total Proposal New Category
        COUNT(CASE WHEN init_call.q1_twetenty_closure_funnel = '$year' AND proposal.apr = 1 THEN 1 END) AS total_proposal_in_q1_twetenty_closure_funnel,
        COUNT(CASE WHEN init_call.potential_funnel_for_fy = '$year' AND proposal.apr = 1 THEN 1 END) AS total_proposal_in_potential_funnel_for_fy,
        COUNT(CASE WHEN init_call.to_be_nurtured_for_fy = '$year' AND proposal.apr = 1 THEN 1 END) AS total_proposal_in_to_be_nurtured_for_fy,
        COUNT(CASE WHEN init_call.fifity_new_lead_funnel = '$year' AND proposal.apr = 1 THEN 1 END) AS total_proposal_in_fifity_new_lead_funnel,
        -- Total Proposal New Category
        -- Total Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' THEN 1 END) AS total_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' THEN 1 END) AS total_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' THEN 1 END) AS total_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' THEN 1 END) AS total_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' THEN 1 END) AS total_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' THEN 1 END) AS total_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' THEN 1 END) AS total_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' THEN 1 END) AS total_in_Smart_Class,
        -- Total Proposal Types
        
        -- Approved Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Smart_Class,
        -- Approved Proposal Types
        
        -- Reject Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' AND proposal.apr =2 THEN 1 END) AS total_Reject_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Smart_Class,
        -- Reject Proposal Types
        
        -- Reject Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Smart_Class
        -- Reject Proposal Types
        FROM
         tblcallevents tblc
        LEFT JOIN proposal ON proposal.tid = tblc.id
        LEFT JOIN user_details u1 ON u1.user_id = tblc.user_id
        LEFT JOIN init_call ON init_call.id = tblc.cid_id
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        WHERE 
            cast(tblc.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
            AND u1.status = 'active'
            $task_action_filter
            $text
            ");
    $totalProposalTasks = $totalProposalTasksQuery->result(); 
       $totalProposalTasksQueryByUser = $this->db->query("SELECT
        u1.user_id,
        u1.name AS username,
        COUNT(CASE WHEN tblc.id THEN 1 END) AS total_proposal_task,
        COUNT(CASE WHEN tblc.nextCFID != 0 THEN 1 END) AS complete_proposal_task,
        COUNT(CASE WHEN tblc.nextCFID = 0 THEN 1 END) AS pending_proposal_task,
        COUNT(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 1 THEN 1 END) AS proposal_approved,
        COUNT(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 2 THEN 1 END) AS proposal_reject,
        COUNT(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 0 THEN 1 END) AS pending_for_approved,
        SUM(CASE WHEN tblc.nextCFID != 0 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_budget,
        SUM(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 1 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_approved_budget,
        SUM(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 0 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_pending_budget,
        SUM(CASE WHEN tblc.nextCFID != 0 AND proposal.apr = 2 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_reject_budget,
        --   COUNT(CASE 
        --     WHEN tblc.nextCFID != 0 
        --         AND proposal.apr = 1 
        --         AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (0, 2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --     THEN 1 
        -- END) AS proposal_approved,
        -- COUNT(CASE 
        --     WHEN tblc.nextCFID != 0 
        --         AND proposal.apr = 2
        --         AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,0) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --     THEN 1 
        -- END) AS proposal_reject,
        -- COUNT(CASE 
        --     WHEN tblc.nextCFID != 0 
        --         AND proposal.apr = 0
        --         AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --     THEN 1 
        -- END) AS pending_for_approved,
        -- SUM(CASE WHEN tblc.nextCFID != 0 THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_budget,
        -- SUM(CASE WHEN tblc.nextCFID != 0 
        --     AND proposal.apr = 1
        --     AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (0, 2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        --  THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_approved_budget,
        -- SUM(CASE WHEN tblc.nextCFID != 0 
        -- AND proposal.apr = 0
        -- AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,2) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        -- THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_pending_budget,
        -- SUM(CASE WHEN tblc.nextCFID != 0 
        -- AND proposal.apr = 2 
        -- AND NOT EXISTS (
        --             SELECT 1 FROM proposal p2 
        --             WHERE p2.init_id = init_call.id 
        --             AND p2.apr IN (1,0) 
        --             AND YEAR(p2.sdatet) = '$year'
        --             AND p2.user_id = tblc.user_id
        --         )
        -- THEN proposal.pbudgetme ELSE 0 END) AS total_proposal_reject_budget,
        -- Total Proposal in Status
        COUNT(CASE WHEN tblc.status_id = 1 AND proposal.apr = 1 THEN 1 END) AS open,
        COUNT(CASE WHEN tblc.status_id = 2 AND proposal.apr = 1 THEN 1 END) AS reachout,
        COUNT(CASE WHEN tblc.status_id = 3 AND proposal.apr = 1 THEN 1 END) AS tentative,
        COUNT(CASE WHEN tblc.status_id = 4 AND proposal.apr = 1 THEN 1 END) AS will_do_later,
        COUNT(CASE WHEN tblc.status_id = 5 AND proposal.apr = 1 THEN 1 END) AS not_interested,
        COUNT(CASE WHEN tblc.status_id = 6 AND proposal.apr = 1 THEN 1 END) AS positive,
        COUNT(CASE WHEN (tblc.status_id = 7 || init_call.upsell_client = 'yes') AND proposal.apr = 1 THEN 1 END) AS closure,
        COUNT(CASE WHEN tblc.status_id = 8 AND proposal.apr = 1 THEN 1 END) AS open_rpem,
        COUNT(CASE WHEN tblc.status_id = 9 AND proposal.apr = 1 THEN 1 END) AS very_positive,
        COUNT(CASE WHEN tblc.status_id = 10 AND proposal.apr = 1 THEN 1 END) AS ttd_reachout,
        COUNT(CASE WHEN tblc.status_id = 11 AND proposal.apr = 1 THEN 1 END) AS wno_reachout,
        COUNT(CASE WHEN tblc.status_id = 12 AND proposal.apr = 1 THEN 1 END) AS positive_nap,
        COUNT(CASE WHEN tblc.status_id = 13 AND proposal.apr = 1 THEN 1 END) AS very_positive_nap,
        COUNT(CASE WHEN tblc.status_id = 14 AND proposal.apr = 1 THEN 1 END) AS on_boarded,
        -- Total Proposal in Status
        -- Total Proposal in Partner
        COUNT(CASE WHEN partner_master.id = 1 AND proposal.apr = 1 THEN 1 END) AS corporate,
        COUNT(CASE WHEN partner_master.id = 2 AND proposal.apr = 1 THEN 1 END) AS PSU,
        COUNT(CASE WHEN partner_master.id = 3 AND proposal.apr = 1 THEN 1 END) AS NGO,
        COUNT(CASE WHEN partner_master.id = 4 AND proposal.apr = 1 THEN 1 END) AS Private_School,
        COUNT(CASE WHEN partner_master.id = 6 AND proposal.apr = 1 THEN 1 END) AS Individual,
        COUNT(CASE WHEN partner_master.id = 8 AND proposal.apr = 1 THEN 1 END) AS Govt_Off,
        COUNT(CASE WHEN partner_master.id = 9 AND proposal.apr = 1 THEN 1 END) AS Other,
        COUNT(CASE WHEN partner_master.id = 10 AND proposal.apr = 1 THEN 1 END) AS Online,
        COUNT(CASE WHEN partner_master.id = 11 AND proposal.apr = 1 THEN 1 END) AS STEM_School,
        COUNT(CASE WHEN partner_master.id = 12 AND proposal.apr = 1 THEN 1 END) AS Foundation,
        COUNT(CASE WHEN partner_master.id = 13 AND proposal.apr = 1 THEN 1 END) AS MNC,
        COUNT(CASE WHEN partner_master.id = 14 AND proposal.apr = 1 THEN 1 END) AS HO_Leads,
        COUNT(CASE WHEN partner_master.id = 16 AND proposal.apr = 1 THEN 1 END) AS DMFT,
        COUNT(CASE WHEN partner_master.id = 17 AND proposal.apr = 1 THEN 1 END) AS Elected_Representatives,
        -- Total Proposal in Partner
        -- Total Proposal Category
        COUNT(CASE WHEN init_call.potential = 'yes' AND proposal.apr = 1 THEN 1 END) AS Potential,
        COUNT(CASE WHEN init_call.topspender = 'yes' AND proposal.apr = 1 THEN 1 END) AS Top_Spender,
        COUNT(CASE WHEN init_call.upsell_client = 'yes' AND proposal.apr = 1 THEN 1 END) AS Upsell_Client,
        COUNT(CASE WHEN init_call.focus_funnel = 'yes' AND proposal.apr = 1 THEN 1 END) AS Focus_Funnel,
        COUNT(CASE WHEN init_call.keycompany = 'yes' AND proposal.apr = 1 THEN 1 END) AS Key_Client,
        COUNT(CASE WHEN init_call.pkclient   = 'yes' AND proposal.apr = 1 THEN 1 END) AS Positive_Key_Client,
        COUNT(CASE WHEN init_call.priorityc = 'yes' AND proposal.apr = 1 THEN 1 END) AS Priority_Calling,
        -- Total Proposal Category
        -- Total Proposal New Category
        COUNT(CASE WHEN init_call.q1_twetenty_closure_funnel = '$year' AND proposal.apr = 1 THEN 1 END) AS total_company_in_q1_twetenty_closure_funnel,
        COUNT(CASE WHEN init_call.potential_funnel_for_fy = '$year' AND proposal.apr = 1 THEN 1 END) AS total_company_in_potential_funnel_for_fy,
        COUNT(CASE WHEN init_call.to_be_nurtured_for_fy = '$year' AND proposal.apr = 1 THEN 1 END) AS total_company_in_to_be_nurtured_for_fy,
        COUNT(CASE WHEN init_call.fifity_new_lead_funnel = '$year' AND proposal.apr = 1 THEN 1 END) AS total_company_in_fifity_new_lead_funnel,
        -- Total Proposal New Category
        -- Total Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' THEN 1 END) AS total_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' THEN 1 END) AS total_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' THEN 1 END) AS total_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' THEN 1 END) AS total_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' THEN 1 END) AS total_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' THEN 1 END) AS total_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' THEN 1 END) AS total_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' THEN 1 END) AS total_in_Smart_Class,
        -- Total Proposal Types
        
        -- Approved Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' AND proposal.apr = 1 THEN 1 END) AS total_Approved_in_Smart_Class,
        -- Approved Proposal Types
        
        -- Reject Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' AND proposal.apr =2 THEN 1 END) AS total_Reject_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' AND proposal.apr = 2 THEN 1 END) AS total_Reject_in_Smart_Class,
        -- Reject Proposal Types
        
        -- Reject Proposal Types
        COUNT(CASE WHEN proposal.propasal_types = 'MSC' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_MSC,
        COUNT(CASE WHEN proposal.propasal_types = 'BALA' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_BALA,
        COUNT(CASE WHEN proposal.propasal_types = 'Astronomy' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Astronomy,
        COUNT(CASE WHEN proposal.propasal_types = 'Tinkering' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Tinkering,
        COUNT(CASE WHEN proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Big_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Small_Laboratory,
        COUNT(CASE WHEN proposal.propasal_types = 'Customize' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Customize,
        COUNT(CASE WHEN proposal.propasal_types = 'Smart Class' AND proposal.apr = 0 THEN 1 END) AS total_Pending_For_Approve_in_Smart_Class
        -- Reject Proposal Types
        FROM tblcallevents tblc
        LEFT JOIN proposal ON proposal.tid = tblc.id
        LEFT JOIN user_details u1 ON u1.user_id = tblc.user_id
        LEFT JOIN init_call ON init_call.id = tblc.cid_id
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        WHERE 
            cast(tblc.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
            AND u1.status = 'active'
            $task_action_filter
            $text GROUP BY u1.user_id
            ");
    $totalProposalTasksByUser = $totalProposalTasksQueryByUser->result(); 
    $results = [
        'totalProposalTasks'        => $totalProposalTasks,
        'totalProposalTasksByUser'  => $totalProposalTasksByUser
    ];
        
    return $results;
    }
    public function GetTeamProposalTaskListsDatas($userid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise){
    if($userid == 'all'){
        $text = "user_details.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "user_details.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "user_details.admin_id = $userid";
    }else if($type_id == 15){
        $text = "user_details.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.ash_nae_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.ash_w_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.ash_s_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.rm_east_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.rm_north_co = $userid || user_details.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "user_details.user_id = $userid";
            }else{
                $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)"; 
            }
        }else{
             $text = "user_details.user_id = $userid";
        }
    }
}
   
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
 
    // if($task_action_id == 'all'){
    //     $taskactionFilter = "AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '')";
    // }else{
    //     $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id";
    // }
    if($task_action_id == 'all'){
        $taskactionFilter = "AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '') AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
    AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }else{
        $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }
    if($mtypes == 'total_proposal_task'){
        $filter = ' ';
    }else if($mtypes == 'complete_proposal_task'){
        $filter = ' AND tbcl.nextCFID != 0';
    }else if($mtypes == 'pending_proposal_task'){
        $filter = ' AND tbcl.nextCFID = 0';
    }else if($mtypes == 'proposal_approved'){
        // $filter = ' AND tbcl.nextCFID != 0 AND proposal.apr = 1';
            $filter = " AND tbcl.nextCFID != 0 
                    AND proposal.apr = 1 
                    -- AND NOT EXISTS (
                    --             SELECT 1 FROM proposal p2 
                    --             WHERE p2.init_id = init_call.id 
                    --             AND p2.apr IN (0, 2) 
                    --             AND YEAR(p2.sdatet) = '$year'
                    --             AND p2.user_id = tbcl.user_id
                    --         )
                            ";
        
    }else if($mtypes == 'proposal_reject'){
        // $filter = ' AND tbcl.nextCFID != 0 AND proposal.apr = 2';
        $filter = " AND tbcl.nextCFID != 0 
                    AND proposal.apr = 2 
                    -- AND NOT EXISTS (
                    --             SELECT 1 FROM proposal p2 
                    --             WHERE p2.init_id = init_call.id 
                    --             AND p2.apr IN (1,0) 
                    --             AND YEAR(p2.sdatet) = '$year'
                    --             AND p2.user_id = tbcl.user_id
                    --         )
                            ";
    }else if($mtypes == 'pending_for_approved'){
        // $filter = ' AND tbcl.nextCFID != 0 AND proposal.apr = 0';
        $filter = " AND tbcl.nextCFID != 0 
                    AND proposal.apr = 0 
                    -- AND NOT EXISTS (
                    --             SELECT 1 FROM proposal p2 
                    --             WHERE p2.init_id = init_call.id 
                    --             AND p2.apr IN (1,2) 
                    --             AND YEAR(p2.sdatet) = '$year'
                    --             AND p2.user_id = tbcl.user_id
                    --         )
                            ";
    }else if($mtypes == 'total_proposal_approved_budget'){
        // $filter = ' AND tbcl.nextCFID != 0 AND proposal.apr = 1';
        $filter = " AND tbcl.nextCFID != 0 
                    AND proposal.apr = 1
                    -- AND NOT EXISTS (
                    --             SELECT 1 FROM proposal p2 
                    --             WHERE p2.init_id = init_call.id 
                    --             AND p2.apr IN (0, 2) 
                    --             AND YEAR(p2.sdatet) = '$year'
                    --             AND p2.user_id = tbcl.user_id
                    --         )
                            ";
    }else if($mtypes == 'total_proposal_pending_budget'){
        // $filter = ' AND tbcl.nextCFID != 0 AND proposal.apr = 0';
          $filter = "   AND tbcl.nextCFID != 0 
                        AND proposal.apr = 0
                        -- AND NOT EXISTS (
                        --             SELECT 1 FROM proposal p2 
                        --             WHERE p2.init_id = init_call.id 
                        --             AND p2.apr IN (1,2) 
                        --             AND YEAR(p2.sdatet) = '$year'
                        --             AND p2.user_id = tbcl.user_id
                        --         )
                                
                                ";
    }else if($mtypes == 'total_proposal_reject_budget'){
        // $filter = ' AND tbcl.nextCFID != 0 AND proposal.apr = 2';
    $filter = " AND tbcl.nextCFID != 0 
                AND proposal.apr = 2
                -- AND NOT EXISTS (
                --             SELECT 1 FROM proposal p2 
                --             WHERE p2.init_id = init_call.id 
                --             AND p2.apr IN (1,0) 
                --             AND YEAR(p2.sdatet) = '$year'
                --             AND p2.user_id = tbcl.user_id
                --         )
                        
                        ";
    }else if($mtypes == 'open'){
        $filter = ' AND tbcl.status_id = 1 AND proposal.apr = 1';
    }else if($mtypes == 'reachout'){
        $filter = ' AND tbcl.status_id = 2 AND proposal.apr = 1';
    }else if($mtypes == 'tentative'){
        $filter = ' AND tbcl.status_id = 3 AND proposal.apr = 1';
    }else if($mtypes == 'will_do_later'){
        $filter = ' AND tbcl.status_id = 3 AND proposal.apr = 1';
    }else if($mtypes == 'not_interested'){
        $filter = ' AND tbcl.status_id = 5 AND proposal.apr = 1';
    }else if($mtypes == 'positive'){
        $filter = ' AND tbcl.status_id = 6 AND proposal.apr = 1';
    }else if($mtypes == 'closure'){
        $filter = " AND (tbcl.status_id = 7 || init_call.upsell_client = 'yes') AND proposal.apr = 1";
    }else if($mtypes == 'open_rpem'){
        $filter = ' AND tbcl.status_id = 8 AND proposal.apr = 1';
    }else if($mtypes == 'very_positive'){
        $filter = ' AND tbcl.status_id = 9 AND proposal.apr = 1';
    }else if($mtypes == 'ttd_reachout'){
        $filter = ' AND tbcl.status_id = 10 AND proposal.apr = 1';
    }else if($mtypes == 'wno_reachout'){
        $filter = ' AND tbcl.status_id = 11 AND proposal.apr = 1';
    }else if($mtypes == 'positive_nap'){
        $filter = ' AND tbcl.status_id = 12 AND proposal.apr = 1';
    }else if($mtypes == 'very_positive_nap'){
        $filter = ' AND tbcl.status_id = 13 AND proposal.apr = 1';
    }else if($mtypes == 'on_boarded'){
        $filter = ' AND tbcl.status_id = 13 AND proposal.apr = 1';
    }else if($mtypes == 'corporate'){
        $filter = ' AND partner_master.id = 1 AND proposal.apr = 1';
    }else if($mtypes == 'PSU'){
        $filter = ' AND partner_master.id = 2 AND proposal.apr = 1';
    }else if($mtypes == 'NGO'){
        $filter = ' AND partner_master.id = 3 AND proposal.apr = 1';
    }else if($mtypes == 'Private_School'){
        $filter = ' AND partner_master.id = 4 AND proposal.apr = 1';
    }else if($mtypes == 'Individual'){
        $filter = ' AND partner_master.id = 6 AND proposal.apr = 1';
    }else if($mtypes == 'Govt_Off'){
        $filter = ' AND partner_master.id = 8 AND proposal.apr = 1';
    }else if($mtypes == 'Other'){
        $filter = ' AND partner_master.id = 9 AND proposal.apr = 1';
    }else if($mtypes == 'Online'){
        $filter = ' AND partner_master.id = 10 AND proposal.apr = 1';
    }else if($mtypes == 'STEM_School'){
        $filter = ' AND partner_master.id = 11 AND proposal.apr = 1';
    }else if($mtypes == 'Foundation'){
        $filter = ' AND partner_master.id = 12 AND proposal.apr = 1';
    }else if($mtypes == 'MNC'){
        $filter = ' AND partner_master.id = 13 AND proposal.apr = 1';
    }else if($mtypes == 'HO_Leads'){
        $filter = ' AND partner_master.id = 14 AND proposal.apr = 1';
    }else if($mtypes == 'DMFT'){
        $filter = ' AND partner_master.id = 14 AND proposal.apr = 1';
    }else if($mtypes == 'Elected_Representatives'){
        $filter = ' AND partner_master.id = 14 AND proposal.apr = 1';
    }else if($mtypes == 'Potential'){
        $filter = " AND init_call.potential = 'yes' AND proposal.apr = 1";
    }else if($mtypes == 'Top_Spender'){
        $filter = " AND init_call.topspender = 'yes' AND proposal.apr = 1";
    }else if($mtypes == 'Upsell_Client'){
        $filter = " AND init_call.upsell_client = 'yes' AND proposal.apr = 1";
    }else if($mtypes == 'Focus_Funnel'){
        $filter = " AND init_call.focus_funnel = 'yes' AND proposal.apr = 1";
    }else if($mtypes == 'Key_Client'){
        $filter = " AND init_call.keycompany = 'yes' AND proposal.apr = 1";
    }else if($mtypes == 'Positive_Key_Client'){
        $filter = " AND init_call.pkclient = 'yes' AND proposal.apr = 1";
    }else if($mtypes == 'Priority_Calling'){
        $filter = " AND init_call.priorityc = 'yes' AND proposal.apr = 1";
    }else if($mtypes == 'total_proposal_in_q1_twetenty_closure_funnel'){
        $filter = " AND init_call.q1_twetenty_closure_funnel = '$year' AND proposal.apr = 1";
    }else if($mtypes == 'total_proposal_in_potential_funnel_for_fy'){
        $filter = " AND init_call.potential_funnel_for_fy = '$year' AND proposal.apr = 1";
    }else if($mtypes == 'total_proposal_in_to_be_nurtured_for_fy'){
        $filter = " AND init_call.to_be_nurtured_for_fy = '$year' AND proposal.apr = 1";
    }else if($mtypes == 'total_proposal_in_fifity_new_lead_funnel'){
        $filter = " AND init_call.fifity_new_lead_funnel = '$year' AND proposal.apr = 1";
    }else if($mtypes == 'total_in_MSC'){
        $filter = " AND proposal.propasal_types = 'MSC'";
    }else if($mtypes == 'total_in_BALA'){
        $filter = " AND proposal.propasal_types = 'BALA'";
    }else if($mtypes == 'total_in_Astronomy'){
        $filter = " AND proposal.propasal_types = 'Astronomy'";
    }else if($mtypes == 'total_in_Tinkering'){
        $filter = " AND proposal.propasal_types = 'Tinkering'";
    }else if($mtypes == 'total_in_Big_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Big Laboratory'";
    }else if($mtypes == 'total_in_Small_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Small Laboratory'";
    }else if($mtypes == 'total_in_Customize'){
        $filter = " AND proposal.propasal_types = 'Customize'";
    }else if($mtypes == 'total_in_Smart_Class'){
        $filter = " AND proposal.propasal_types = 'Smart Class'";
    }else if($mtypes == 'total_Approved_in_MSC'){
        $filter = " AND proposal.propasal_types = 'MSC' AND proposal.apr = 1";
    }else if($mtypes == 'total_Approved_in_BALA'){
        $filter = " AND proposal.propasal_types = 'BALA' AND proposal.apr = 1";
    }else if($mtypes == 'total_Approved_in_Astronomy'){
        $filter = " AND proposal.propasal_types = 'Astronomy' AND proposal.apr = 1";
    }else if($mtypes == 'total_Approved_in_Tinkering'){
        $filter = " AND proposal.propasal_types = 'Tinkering' AND proposal.apr = 1";
    }else if($mtypes == 'total_Approved_in_Big_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 1";
    }else if($mtypes == 'total_Approved_in_Small_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 1";
    }else if($mtypes == 'total_Approved_in_Customize'){
        $filter = " AND proposal.propasal_types = 'Customize' AND proposal.apr = 1";
    }else if($mtypes == 'total_Approved_in_Smart_Class'){
        $filter = " AND proposal.propasal_types = 'Smart Class' AND proposal.apr = 1";
    }else if($mtypes == 'total_Reject_in_MSC'){
        $filter = " AND proposal.propasal_types = 'MSC' AND proposal.apr = 2";
    }else if($mtypes == 'total_Reject_in_BALA'){
        $filter = " AND proposal.propasal_types = 'BALA' AND proposal.apr = 2";
    }else if($mtypes == 'total_Reject_in_Astronomy'){
        $filter = " AND proposal.propasal_types = 'Astronomy' AND proposal.apr = 2";
    }else if($mtypes == 'total_Reject_in_Tinkering'){
        $filter = " AND proposal.propasal_types = 'Tinkering' AND proposal.apr = 2";
    }else if($mtypes == 'total_Reject_in_Big_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 2";
    }else if($mtypes == 'total_Reject_in_Small_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 2";
    }else if($mtypes == 'total_Reject_in_Customize'){
        $filter = " AND proposal.propasal_types = 'Customize' AND proposal.apr = 2";
    }else if($mtypes == 'total_Reject_in_Smart_Class'){
        $filter = " AND proposal.propasal_types = 'Smart Class' AND proposal.apr = 2";
    }else if($mtypes == 'total_Pending_For_Approve_in_MSC'){
        $filter = " AND proposal.propasal_types = 'MSC' AND proposal.apr = 0";
    }else if($mtypes == 'total_Pending_For_Approve_in_BALA'){
        $filter = " AND proposal.propasal_types = 'BALA' AND proposal.apr = 0";
    }else if($mtypes == 'total_Pending_For_Approve_in_Astronomy'){
        $filter = " AND proposal.propasal_types = 'Astronomy' AND proposal.apr = 0";
    }else if($mtypes == 'total_Pending_For_Approve_in_Tinkering'){
        $filter = " AND proposal.propasal_types = 'Tinkering' AND proposal.apr = 0";
    }else if($mtypes == 'total_Pending_For_Approve_in_Big_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Big Laboratory' AND proposal.apr = 0";
    }else if($mtypes == 'total_Pending_For_Approve_in_Small_Laboratory'){
        $filter = " AND proposal.propasal_types = 'Small Laboratory' AND proposal.apr = 0";
    }else if($mtypes == 'total_Pending_For_Approve_in_Customize'){
        $filter = " AND proposal.propasal_types = 'Customize' AND proposal.apr = 0";
    }else if($mtypes == 'total_Pending_For_Approve_in_Smart_Class'){
        $filter = " AND proposal.propasal_types = 'Smart Class' AND proposal.apr = 0";
    }
 
      $query = $this->db->query("SELECT
       
            user_details.user_id as task_user_id,
            proposal.id as proposal_id,
            user_details.name as task_username,
            company_master.compname,
            company_master.id as cid,
            tbcl.id as task_id,
            tbcl.nextCFID,
            tbcl.fwd_date,
            tbcl.appointmentdatetime,
            tbcl.initiateddt,
            tbcl.updated_at,
            CONCAT(
                FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
                FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
                MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
            ) AS total_time_taken,
            tbcl.late_remarks_message,
            tbcl.actontaken,
            tbcl.purpose_achieved,
            tbcl.meeting_type,
            tbcl.actiontype_id,
            tbcl.assignedto_id,
            tbcl.cid_id,
            tbcl.remarks,
            tbcl.mom as mom_remarks,
            tbcl.mtype,
            tbcl.selectby,
            tbcl.special_remarks,
            tbcl.filter_by,
            tbcl.approved_status as task_approved_status,
            tbcl.approved_by as task_approved_by,
            tbcl.assignedto_by as task_assignedto_by,
            tbcl.assignedto_by as task_aftertask,
            action.name as task_name,
            status.name as current_status,
            tbcl.plan_count as plan_count,
            init_call.potential,
            init_call.topspender,
            init_call.upsell_client,
            init_call.focus_funnel,
            init_call.keycompany,
            init_call.pkclient,
            init_call.priorityc,
            init_call.q1_twetenty_closure_funnel,
            init_call.potential_funnel_for_fy,
            init_call.potential_funnel_for_fy,
            init_call.to_be_nurtured_for_fy,
            init_call.fifity_new_lead_funnel,
            u1.name as main_bd_name,
            u2.name as main_bd_manager_name,
            s1.name as task_time_status,
            s2.name as task_time_new_status,
            partner_master.name as partner_name,
            COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
            proposal.proattach,
            proposal.partner as proposal_partner,
            proposal.propasal_types as propasal_types,
            proposal.noofsc as propasal_noofsc,
            proposal.pbudgetme as propasal_budgetme,
            proposal.remark as propasal_apr_remarks,
            proposal.apr as apr_status,
            COALESCE(proposal.apr_date, proposal.aprdatet) AS propasal_apr_date,
            u10.name as pst_name,
            u4.name as ash_nae_co_id_name,
            u5.name as ash_w_co_id_name,
            u6.name as ash_s_co_id_name,
            u7.name as rm_east_co_id_name,
            u8.name as rm_north_co_id_name,
            u9.name as acm_co_id_name,

            u21.name as proposal_checked_by,
            proposal_checked.appointmentdatetime as proposal_checked_on_date


        FROM tblcallevents tbcl
        LEFT JOIN proposal ON proposal.tid = tbcl.id
        LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
        LEFT JOIN init_call ON init_call.id = tbcl.cid_id
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        LEFT JOIN action ON action.id = tbcl.actiontype_id
        LEFT JOIN status ON status.id = init_call.cstatus
        LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
        LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
        LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
        LEFT JOIN status s1 ON s1.id = tbcl.status_id
        LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
        LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
        LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
        LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
        LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
        LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
        LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
        LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
        LEFT JOIN tblcallevents proposal_checked ON proposal_checked.aftertask = tbcl.id AND proposal_checked.actiontype_id = 21
        LEFT JOIN user_details u21 ON u21.user_id = proposal_checked.user_id
        WHERE 
            cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
            AND user_details.status = 'active'
            $taskactionFilter
            AND $text $filter
            ");
    return $query->result(); 
}
public function GetProposalDetaislByPID($pid){
     $query=$this->db->query("SELECT
            user_details.user_id as task_user_id,
            proposal.id as proposal_id,
            user_details.name as task_username,
            company_master.compname,
            company_master.id as cid,
            tbcl.id as task_id,
            tbcl.nextCFID,
            tbcl.fwd_date,
            tbcl.appointmentdatetime,
            tbcl.initiateddt,
            tbcl.updated_at,
            CONCAT(
                FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
                FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
                MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
            ) AS total_time_taken,
            tbcl.late_remarks_message,
            tbcl.actontaken,
            tbcl.purpose_achieved,
            tbcl.meeting_type,
            tbcl.actiontype_id,
            tbcl.assignedto_id,
            tbcl.cid_id,
            tbcl.remarks,
            tbcl.selectby,
            tbcl.approved_status as task_approved_status,
            tbcl.approved_by as task_approved_by,
            tbcl.assignedto_by as task_assignedto_by,
            tbcl.assignedto_by as task_aftertask,
            action.name as task_name,
            status.name as current_status,
            tbcl.plan_count as plan_count,
            init_call.id as init_call_id,
            init_call.potential,
            init_call.topspender,
            init_call.upsell_client,
            init_call.focus_funnel,
            init_call.keycompany,
            init_call.pkclient,
            init_call.priorityc,
            init_call.q1_twetenty_closure_funnel,
            init_call.potential_funnel_for_fy,
            init_call.potential_funnel_for_fy,
            init_call.to_be_nurtured_for_fy,
            init_call.fifity_new_lead_funnel,
            u1.name as main_bd_name,
            u2.name as main_bd_manager_name,
            s1.name as task_time_status,
            s2.name as task_time_new_status,
            partner_master.name as partner_name,
            COALESCE(u3.name) AS task_approved_by,
    proposal.proattach,
            proposal.partner as proposal_partner,
            proposal.propasal_types as propasal_types,
            proposal.noofsc as propasal_noofsc,
            proposal.pbudgetme as propasal_budgetme,
            proposal.apr as apr_status,
            proposal.remark as propasal_apr_remarks,
            COALESCE(proposal.apr_date, proposal.aprdatet) AS propasal_apr_date
FROM
    `proposal`
        LEFT JOIN tblcallevents tbcl ON tbcl.id = proposal.tid
        LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
        LEFT JOIN init_call ON init_call.id = tbcl.cid_id
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        LEFT JOIN action ON action.id = tbcl.actiontype_id
        LEFT JOIN status ON status.id = init_call.cstatus
        
        LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
        LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
        LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
        LEFT JOIN status s1 ON s1.id = tbcl.status_id
        LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
WHERE
    proposal.id = $pid");
        return $query->result();
}
public function GetWorkAfterProposalApprovedOrRejectByPID($pid,$init_call_id,$propasal_apr_date){
    $taskactionFilter = "AND (tbcl.actiontype_id != 0 AND tbcl.actiontype_id != '') AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')";
   
    $current_date = date("Y-m-d");
$query = $this->db->query("SELECT
    user_details.user_id as task_user_id,
    user_details.name as task_username,
    company_master.compname,
    company_master.id as cid,
    tbcl.id as task_id,
    tbcl.nextCFID,
    tbcl.fwd_date,
    tbcl.appointmentdatetime,
    tbcl.initiateddt,
    tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
    tbcl.late_remarks_message,
    tbcl.actontaken,
    tbcl.purpose_achieved,
    tbcl.meeting_type,
    tbcl.actiontype_id,
    tbcl.assignedto_id,
    tbcl.cid_id,
    tbcl.remarks,
    tbcl.mom as mom_remarks,
    tbcl.mtype,
    tbcl.selectby,
    tbcl.special_remarks,
    tbcl.filter_by,
    tbcl.approved_status as task_approved_status,
    tbcl.approved_by as task_approved_by,
    tbcl.assignedto_by as task_assignedto_by,
    action.name as task_name,
    status.name as current_status,
    tbcl.plan_count as plan_count,
    init_call.potential,
    init_call.topspender,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.q1_twetenty_closure_funnel,
    init_call.potential_funnel_for_fy,
    init_call.to_be_nurtured_for_fy,
    init_call.fifity_new_lead_funnel,
    u1.name as main_bd_name,
    u2.name as main_bd_manager_name,
    s1.name as task_time_status,
    s2.name as task_time_new_status,
    partner_master.name as partner_name,
    COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
    u10.name as pst_name,
    u4.name as ash_nae_co_id_name,
    u5.name as ash_w_co_id_name,
    u6.name as ash_s_co_id_name,
    u7.name as rm_east_co_id_name,
    u8.name as rm_north_co_id_name,
    u9.name as acm_co_id_name
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    init_call.id =  '$init_call_id'
    AND tbcl.appointmentdatetime >= '$propasal_apr_date'
    AND tbcl.actiontype_id = 1
    AND (
        tbcl.approved_status = 1 OR tbcl.approved_status = ''
    )
    -- AND user_details.user_id != init_call.mainbd;
");
return $query->result();
}
public function GetTodaysMeetingsPendingTaskByUserId($userid,$tdate){
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }
    
    // elseif($type_id == 19){
    //     $text = "u1.ash_nae_co = '$userid'";
    // }else if($type_id == 20){
    //     $text = "u1.ash_w_co='$userid'";
    // }else if($type_id == 21){
    //     $text = "u1.ash_s_co='$userid'";
    // }else if($type_id == 22){
    //     $text = "u1.rm_east_co='$userid'";
    // }else if($type_id == 23){
    //     $text = "u1.rm_north_co='$userid'";
    // }
    
    else{
        $text = "u1.user_id='$userid'";
    }
    
     $today = date("Y-m-d");
    if ($tdate == $today) {
        $filterDate = "AND CAST(tblcallevents.appointmentdatetime AS DATE) < '$today'";
    } elseif ($tdate > $today) {
        $filterDate = "AND CAST(tblcallevents.appointmentdatetime AS DATE) = '$today'";
    } 
    $taskactionFilter = "tblcallevents.actiontype_id IN (3,4)";
    $query = $this->db->query("SELECT 
            tblcallevents.id as task_id,
            company_master.id as cid,
            u1.name as task_bd_name,
            u2.name as task_approved_by,
            company_master.compname as company_name,
            tblcallevents.fwd_date,
            tblcallevents.appointmentdatetime,
            tblcallevents.nextCFID,
            tblcallevents.approved_status,
            tblcallevents.delete_request,
            tblcallevents.delete_remarks,
            action.name as task_name,
            s1.name as task_time_status,
            s2.name as current_status,
            tblcallevents.plan_count as task_plan_count
            FROM `tblcallevents` 
            LEFT JOIN init_call on init_call.id = tblcallevents.cid_id
            LEFT JOIN company_master on company_master.id = init_call.cmpid_id
            LEFT JOIN user_details u1 on u1.user_id = tblcallevents.user_id
            LEFT JOIN user_details u2 on u2.user_id = tblcallevents.approved_by
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            LEFT JOIN status s1 on s1.id = tblcallevents.status_id
            LEFT JOIN status s2 on s2.id = init_call.cstatus
            WHERE 
            $taskactionFilter
            AND tblcallevents.nextCFID = 0 
            AND (tblcallevents.approved_status = '1' OR tblcallevents.approved_status = '')
            AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
            AND plan =1
            AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
            $filterDate
            AND u1.status = 'active'
            AND $text 
            ");
    return $query->result(); 
}
public function GetTotalLocations(){
    $query = $this->db->query("SELECT
    COUNT(DISTINCT in_state.state_id) AS total_state,
    COUNT(DISTINCT in_district.districtid) AS total_districts,
    COUNT(DISTINCT in_city.id) AS total_city
FROM in_state
LEFT JOIN in_district ON in_district.state_id = in_state.state_id
LEFT JOIN in_city ON in_city.districtid = in_district.districtid");
    $totalLocation =  $query->result(); 
    $query1 = $this->db->query("SELECT
    in_state.state_title,
    in_state.state_id,
    COUNT(DISTINCT in_district.districtid) AS total_districts_state_title,
    COUNT(DISTINCT in_city.id) AS total_city_state_title
FROM in_state
LEFT JOIN in_district ON in_district.state_id = in_state.state_id
LEFT JOIN in_city ON in_city.districtid = in_district.districtid
GROUP BY in_state.state_id, in_state.state_title
ORDER BY in_state.state_title");
    $totalByLocation =  $query1->result(); 
    $result = [
        'totalLocation'     => $totalLocation,
        'totalByLocation'   => $totalByLocation
    ];
    return $result;
}
public function GetTotalLocationsBYKeysPoint($keys){
    if($keys == 'total_state'){
        $query = $this->db->query("SELECT * FROM in_state");
    }
    else if($keys == 'total_districts'){
       $query = $this->db->query("SELECT in_district.*,in_state.state_title FROM in_district LEFT JOIN in_state ON in_state.state_id = in_district.state_id");
    }
    else if($keys == 'total_city'){
      $query = $this->db->query("SELECT 
        in_city.name, 
        in_district.district_title, 
        in_state.state_title
        FROM in_city 
        LEFT JOIN in_state ON in_state.state_id = in_city.state_id
        LEFT JOIN in_district ON in_district.districtid = in_city.districtid
    ");
    }
    
    $totalLocation =  $query->result(); 
    return $totalLocation;
}
public function GetTotalLocationsBYKeysPoints($keys,$state_id){
       
        if($keys =='total_districts_state_title'){
            $filter = "in_state.state_id = '$state_id' GROUP BY in_district.districtid";
            $selectto = 'in_district.district_title,in_state.state_title';
        }
        if($keys =='total_city_state_title'){
            $filter = "in_state.state_id = '$state_id'";
            $selectto = 'in_district.district_title,in_state.state_title,in_city.name';
        }
      $query = $this->db->query("SELECT 
        $selectto
        FROM in_city 
        LEFT JOIN in_state ON in_state.state_id = in_city.state_id
        LEFT JOIN in_district ON in_district.districtid = in_city.districtid
        WHERE $filter
    ");
    
    $totalLocation =  $query->result(); 
    return $totalLocation;
}
public function GetOurFutureTaskListsByRoles($userid,$admin_id_filter,$rm_filter){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
               $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }
    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];
$query = $this->db->query("SELECT 
    init_call.id AS init_call_id,
    company_master.id AS cid,
    company_master.compname,
    u2.name AS main_bd_name,
    u1.name as task_user_name,
    tblcallevents.id as task_id,
    tblcallevents.appointmentdatetime,
    tblcallevents.reviewtype,
    CONCAT('+ ', DATEDIFF(tblcallevents.appointmentdatetime, CURDATE()), ' days') AS marked_meeting_days,
    action.name AS task_name,
    s1.name as planned_on_status,
    s2.name as current_status
FROM 
    tblcallevents 
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id    
LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN user_details u2 ON u2.user_id = init_call.mainbd
LEFT JOIN action ON action.id = tblcallevents.actiontype_id
LEFT JOIN status s1 ON s1.id = tblcallevents.status_id
LEFT JOIN status s2 ON s2.id = init_call.cstatus
WHERE 
    DATE(tblcallevents.appointmentdatetime) > CURDATE()
    AND $text AND u1.status = 'active'
    ORDER BY tblcallevents.appointmentdatetime");
$queryData = $query->result();
    return $queryData;
}
public function GetPlannerAndUserSummury($userid,$sdate,$edate,$admin_id_filter,$rm_filter){
  
    $udetail = $this->Menu_model->get_userbyid($userid);
       $type_id = $udetail[0]->type_id;
       
       if($type_id == 1){
           $text = " sadmin_id = $userid";
       }else if($type_id == 2){
           $text = " admin_id = $userid";
       }else if($type_id == 3){
           $text = " user_id = $userid";
       }else if($type_id == 4){
           $text = " (pst_co = $userid || user_id = $userid)";
       }else if($type_id == 13){
           $text = " (aadmin = $userid || user_id = $userid)";
       }else if($type_id == 15){
           $text = " sales_co = $userid";
       }elseif($type_id == 19){
           $text = " (ash_nae_co = '$userid' || user_id = $userid)";
       }else if($type_id == 20){
           $text = " (ash_w_co='$userid' || user_id = $userid)";
       }else if($type_id == 21){
           $text = " (ash_s_co='$userid' || user_id = $userid)";
       }else if($type_id == 22){
           $text = " (rm_east_co='$userid' || user_id = $userid)";
       }else if($type_id == 23){
           $text = " (rm_north_co='$userid' || user_id = $userid)";
       }else if($type_id == 24){
           $text = " (acm_co = $userid || user_id = $userid)";
       }else{
           $text = " (admin_id = $userid || user_id = $userid)";
       }
   
       if($admin_id_filter =='all'){
            $cuser = $this->session->userdata('user');
            $cuid = $cuser['user_id'];
            if(in_array($cuid,[2,45])){
            $text = " admin_id IN (2,45)";
            }else if(in_array($cuid,[100000])){
                $text = " sadmin_id = '100000'";
            }else{
                $text = " admin_id = '$cuid'";
            }
       }else{
           if($rm_filter !== 'all'){
                   $udetail = $this->Menu_model->get_userbyid($rm_filter);
                   $type_id = $udetail[0]->type_id;
                   
                   if($type_id == 1){
                       $text = " sadmin_id = $userid";
                   }else if($type_id == 2){
                       $text = " admin_id = $userid";
                   }else if($type_id == 3){
                       $text = " user_id = $userid";
                   }else if($type_id == 4){
                       $text = " (pst_co = $userid || user_id = $userid)";
                   }else if($type_id == 13){
                       $text = " (aadmin = $userid || user_id = $userid)";
                   }else if($type_id == 15){
                       $text = " sales_co = $userid";
                   }elseif($type_id == 19){
                       $text = " (ash_nae_co = '$userid' || user_id = $userid)";
                   }else if($type_id == 20){
                       $text = " (ash_w_co='$userid' || user_id = $userid)";
                   }else if($type_id == 21){
                       $text = " (ash_s_co='$userid' || user_id = $userid)";
                   }else if($type_id == 22){
                       $text = " (rm_east_co='$userid' || user_id = $userid)";
                   }else if($type_id == 23){
                       $text = " rm_north_co='$userid' || user_id = $userid)";
                   }else if($type_id == 24){
                       $text = " (acm_co = $userid || user_id = $userid)";
                   }else{
                       $text = " (admin_id = $userid || user_id = $userid)";
                   }
           }
       }
   
      $query = $this->db->query("SELECT 
    d.report_date,
    -- Total active users under admin 45
    (SELECT COUNT(*) 
     FROM user_details 
     WHERE $text AND status = 'active'
    ) AS total_user,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL THEN ud.user_id
    END) AS total_present_user,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.uclose IS NULL THEN ud.user_id
    END) AS total_present_and_not_closed_day_user,
       COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.uclose IS NOT NULL THEN u1.user_id
    END) AS total_present_and_closed_day_user,
    (
        (SELECT COUNT(*) 
         FROM user_details 
         WHERE $text AND status = 'active')
        -
        COUNT(DISTINCT CASE 
            WHEN ud.id IS NOT NULL THEN u1.user_id
        END)
    ) AS total_absent_user,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.wffo = 1 THEN u1.user_id
    END) AS total_work_from_office,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.wffo = 2 THEN u1.user_id
    END) AS total_work_from_field,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.wffo = 3 THEN u1.user_id
    END) AS total_work_from_field_office,
    
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL AND DATE(aut.date) = d.report_date THEN aut.user_id
    END) AS total_planning_set,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL AND DATE(aut.created_at) = d.report_date THEN aut.user_id
    END) AS same_day_planning,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL AND DATE(aut.created_at) != d.report_date AND DATE(aut.date) = d.report_date THEN aut.user_id
    END) AS previous_day_planning,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NULL THEN u1.user_id
    END) AS planner_not_set,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL 
          AND DATE(aut.created_at) != DATE(aut.date) 
          AND tpft.id IS NOT NULL THEN aut.user_id
    END) AS spdplanning_then_create_same_day_request
FROM (
    SELECT DATE(date) AS report_date 
    FROM autotask_time 
    WHERE date BETWEEN '$sdate' AND '$edate'
    GROUP BY DATE(date)
) d
CROSS JOIN (
    SELECT user_id 
    FROM user_details 
    WHERE status = 'active' AND $text
) u1
LEFT JOIN autotask_time aut ON aut.user_id = u1.user_id AND DATE(aut.date) = d.report_date
LEFT JOIN task_plan_for_today tpft ON tpft.user_id = u1.user_id AND DATE(tpft.date) = d.report_date
LEFT JOIN user_day ud ON ud.user_id = u1.user_id AND DATE(ud.ustart) = d.report_date
GROUP BY d.report_date
ORDER BY d.report_date");
    $getPlannerReports = $query->result();
    return $getPlannerReports; 
}
public function GetUserPlannerReportBetweenDate($userid,$sdate,$edate,$admin_id_filter,$rm_filter){
  
    $udetail = $this->Menu_model->get_userbyid($userid);
       $type_id = $udetail[0]->type_id;
       
       if($type_id == 1){
           $text = " u1.sadmin_id = $userid";
       }else if($type_id == 2){
           $text = " u1.admin_id = $userid";
       }else if($type_id == 3){
           $text = " u1.user_id = $userid";
       }else if($type_id == 4){
           $text = " (u1.pst_co = $userid || u1.user_id = $userid)";
       }else if($type_id == 13){
           $text = " (u1.aadmin = $userid || u1.user_id = $userid)";
       }else if($type_id == 15){
           $text = " u1.sales_co = $userid";
       }elseif($type_id == 19){
           $text = " (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
       }else if($type_id == 20){
           $text = " (u1.ash_w_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 21){
           $text = " (u1.ash_s_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 22){
           $text = " (u1.rm_east_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 23){
           $text = " (u1.rm_north_co='$userid' || u1.user_id = $userid)";
       }else if($type_id == 24){
           $text = " (u1.acm_co = $userid || u1.user_id = $userid)";
       }else{
           $text = " (u1.admin_id = $userid || u1.user_id = $userid)";
       }
   
       if($admin_id_filter =='all'){
            $cuser = $this->session->userdata('user');
            $cuid = $cuser['user_id'];
            if(in_array($cuid,[2,45])){
            $text = " u1.admin_id IN (2,45)";
            }else if(in_array($cuid,[100000])){
                $text = " u1.sadmin_id = '100000'";
            }else{
                $text = " u1.admin_id = '$cuid'";
            }
       }else{
           if($rm_filter !== 'all'){
                   $udetail = $this->Menu_model->get_userbyid($rm_filter);
                   $type_id = $udetail[0]->type_id;
                   
                   if($type_id == 1){
                       $text = " u1.sadmin_id = $userid";
                   }else if($type_id == 2){
                       $text = " u1.admin_id = $userid";
                   }else if($type_id == 3){
                       $text = " u1.user_id = $userid";
                   }else if($type_id == 4){
                       $text = " (u1.pst_co = $userid || u1.user_id = $userid)";
                   }else if($type_id == 13){
                       $text = " (u1.aadmin = $userid || u1.user_id = $userid)";
                   }else if($type_id == 15){
                       $text = " u1.sales_co = $userid";
                   }elseif($type_id == 19){
                       $text = " (u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
                   }else if($type_id == 20){
                       $text = " (u1.ash_w_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 21){
                       $text = " (u1.ash_s_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 22){
                       $text = " (u1.rm_east_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 23){
                       $text = " u1.rm_north_co='$userid' || u1.user_id = $userid)";
                   }else if($type_id == 24){
                       $text = " (u1.acm_co = $userid || u1.user_id = $userid)";
                   }else{
                       $text = " (u1.admin_id = $userid || u1.user_id = $userid)";
                   }
           }
       }
   $query=$this->db->query("SELECT
    COUNT(tblcallevents.id) AS total_task,
    COUNT(
        CASE WHEN(
           tblcallevents.autotask = 0 AND tblcallevents.plan = 1
        ) THEN tblcallevents.id
    END
) AS total_planned_task,
COUNT(
    CASE WHEN(tblcallevents.autotask = 1) THEN tblcallevents.id
END
) AS total_autotask,
COUNT(
        CASE WHEN(
            tblcallevents.approved_status = 1 AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
        ) THEN tblcallevents.id
    END
) AS total_approved_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = '' AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
    ) THEN tblcallevents.id
END
) AS total_pending_for_approved,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.autotask = 0 AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
    ) THEN tblcallevents.id
END
) AS total_reject,
COUNT(
    CASE WHEN(
        tblcallevents.delete_request > 0
    ) THEN tblcallevents.id
END
) AS total_deleted_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '' AND tblcallevents.autotask = 0
    ) THEN tblcallevents.id 
END
) AS pending_for_assign_after_reject_task,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '1' AND tblcallevents.autotask = 0
    ) THEN tblcallevents.id
END
) AS admin_create_request_for_self_assign,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '2' AND tblcallevents.autotask = 0
    ) THEN tblcallevents.id
END
) AS task_assignd_by_admin_after_reject,
COUNT(
    CASE WHEN(
        tblcallevents.approved_status = 0 AND tblcallevents.approved_by != '' AND tblcallevents.self_assign = '3' AND tblcallevents.autotask = 0
    ) THEN tblcallevents.id
END
) AS task_assignd_by_user_after_reject,
COUNT(
    CASE WHEN tblcallevents.nextCFID != 0 THEN tblcallevents.id
END
) AS complete_task,
COUNT(
    CASE WHEN tblcallevents.nextCFID = 0 THEN tblcallevents.id
END
) AS pending_for_complete_task,
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
) AS pending_for_complete_autotask
FROM
    tblcallevents
LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
WHERE
    $text
    AND CAST(
        tblcallevents.appointmentdatetime AS DATE
    ) BETWEEN '$sdate' AND '$edate'
    AND tblcallevents.appointmentdatetime != '0000-00-00 00:00:00'");
   
    $totalTasks = $query->result();
    return $totalTasks; 
}
 public function GetPlannerAndUserSummuryDatas($userid,$sdate,$edate,$mtypes,$userwise){
    
    if($userid == 'all'){
        $text = "admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "admin_id = $userid";
    }else if($type_id == 15){
        $text = "sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(pst_co = $userid || user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(aadmin = $userid || user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(ash_nae_co = $userid || user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(ash_w_co = $userid || user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(ash_s_co = $userid || user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(rm_east_co = $userid || user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(rm_north_co = $userid || user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "user_id = $userid";
            }else{
                $text = "(acm_co = $userid || user_id = $userid)"; 
            }
        }else{
             $text = "user_id = $userid";
        }
    }
}
$groupByClause = "";
$additionalWhere = "";
$selectFilter  = "";
$selectDayFilter = "
        , ud.ustart AS user_start,
        ud.uclose AS user_close,
        ud.usimg AS usimg,
        ud.ucimg AS ucimg,
        ud.slatitude AS slatitude,
        ud.slongitude AS slongitude,
        ud.clatitude AS clatitude,
        ud.clongitude AS clongitude,
        ud.wffo AS user_wffo,
        ud.queans AS user_queans,
        ud.queansc AS queansc
    ";
    $selectPlannerFilter = "
        , d.report_date,
        aut.stime as auto_task_start_time,
        aut.etime as auto_task_end_time,
        aut.start_tttpft as next_day_planning_start_time,
        aut.end_tttpft as next_day_planning_end_time,
        aut.created_at as planner_setting_datetime,
        aut.userworkfrom as planner_setting_userworkfrom    
    ";
    $selectextraFilter = "
        , tpft.taskcnt as task_count,
        tpft.would_you_want as plan_would_you_want,
        tpft.id as same_day_request_id,
        tpft.request_remarks as plan_request_remarks,
        tpft.approvel_status as plan_approvel_status,
        tpft.remarks as plan_approvel_remarks,
        tpft.created_at as plan_created_at,
        tpft.apr_time as plan_apr_time
    ";
    if ($mtypes == 'total_user') {
        $groupByClause = "GROUP BY u1.user_id";
    } else if($mtypes == 'total_present_user') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND ud.id IS NOT NULL";
        $selectFilter       = $selectDayFilter;
    }else if($mtypes == 'total_present_user') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND ud.id IS NOT NULL";
        $selectFilter       = $selectDayFilter;
    } 
    
    else if($mtypes == 'total_present_and_closed_day_user') {
        
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND ud.id IS NOT NULL AND ud.uclose IS NOT NULL";
        $selectFilter       = $selectDayFilter;
    }
    else if($mtypes == 'total_present_and_not_closed_day_user') {
        
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND ud.id IS NOT NULL AND ud.uclose IS NULL";
        $selectFilter       = $selectDayFilter;
    }
    else if($mtypes == 'total_work_from_office') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND ud.id IS NOT NULL AND ud.wffo = 1";
        $selectFilter       = $selectDayFilter;
    }
    else if($mtypes == 'total_work_from_field') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND ud.id IS NOT NULL AND ud.wffo = 2";
        $selectFilter       = $selectDayFilter;
    }
    else if($mtypes == 'total_work_from_field_office') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND ud.id IS NOT NULL AND ud.wffo = 3";
        $selectFilter       = $selectDayFilter;
    }
    else if($mtypes == 'total_planning_set') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND aut.id IS NOT NULL AND DATE(aut.date) = d.report_date";
        $selectFilter       = $selectPlannerFilter;
    }
    else if($mtypes == 'same_day_planning') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND aut.id IS NOT NULL AND DATE(aut.created_at) = d.report_date";
        $selectFilter       = $selectPlannerFilter;
        $selectFilter       .= $selectextraFilter;
    }
    else if($mtypes == 'previous_day_planning') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND aut.id IS NOT NULL AND DATE(aut.created_at) != d.report_date AND DATE(aut.date) = d.report_date";
        $selectFilter       = $selectPlannerFilter;
    }
    else if($mtypes == 'planner_not_set') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND aut.id IS NULL";
        $selectFilter       = $selectPlannerFilter;
    }
    else if($mtypes == 'spdplanning_then_create_same_day_request') {
        $groupByClause      = "GROUP BY u1.user_id";
        $additionalWhere    = "AND aut.id IS NOT NULL AND DATE(aut.created_at) != DATE(aut.date) AND tpft.id IS NOT NULL";
        $selectFilter       = $selectPlannerFilter;
        $selectFilter .= $selectextraFilter;
    }
$query = $this->db->query("SELECT 
        u1.*,
        user_type.name AS user_type_name
        $selectFilter
    FROM (
        SELECT DATE(date) AS report_date 
        FROM autotask_time 
        WHERE date BETWEEN '$sdate' AND '$edate'
        GROUP BY DATE(date)
    ) d
    CROSS JOIN (
        SELECT * 
        FROM user_details 
        WHERE status = 'active' AND ($text)
    ) u1
    LEFT JOIN autotask_time aut 
        ON aut.user_id = u1.user_id AND DATE(aut.date) = d.report_date
    LEFT JOIN task_plan_for_today tpft 
        ON tpft.user_id = u1.user_id AND DATE(tpft.date) = d.report_date
    LEFT JOIN user_day ud  
        ON ud.user_id = u1.user_id AND DATE(ud.ustart) = d.report_date
    LEFT JOIN user_type 
        ON user_type.id = u1.type_id
    WHERE 1=1
        $additionalWhere
    $groupByClause
");
    $getPlannerReports = $query->result();
    return $getPlannerReports; 
 }
 public function GetPlannerApprovedAndUserSummuryDatas($userid,$sdate,$edate,$mtypes,$userwise){
    
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='u1.userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}
    if($mtypes == 'total_request') {
        $filter = "";
    } else if($mtypes == 'total_pending') {
         $filter = " AND pa.approved_status IS NULL";
    }else if($mtypes == 'total_approved') {
         $filter = " AND pa.approved_status  = 1";
    }
   
$query = $this->db->query("SELECT
                        pa.*,
                        u1.name AS request_username,
                        u2.name AS request_approved_name,
                        CONCAT(
                            TIMESTAMPDIFF(HOUR, pa.created_at, pa.approved_date), ' hours ',
                            MOD(TIMESTAMPDIFF(MINUTE, pa.created_at, pa.approved_date), 60), ' minutes ',
                            MOD(TIMESTAMPDIFF(SECOND, pa.created_at, pa.approved_date), 60), ' seconds'
                        ) AS time_taken_in_planner_approved
                    FROM
                        planner_approved pa
                        LEFT JOIN user_details u1 ON u1.user_id = pa.user_id
                        LEFT JOIN user_details u2 ON u2.user_id = pa.approved_by
                    WHERE
                        $text AND pa.request_date BETWEEN '$sdate' AND '$edate'
                        $filter
                    ");
    $getPlannerReports = $query->result();
    return $getPlannerReports; 
 }
public function DayManagementCheckingDatas($userid,$userwise,$cdate) {
    
    if($userid =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "user_details.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "user_details.sadmin_id = $cuid";
        }else{
            
            $udetail = $this->Menu_model->get_userbyid($cuid); 
            $type_id = $udetail[0]->type_id;
            if($type_id == 1){
                $text = "user_details.sadmin_id = $cuid";
            }else if($type_id == 2){
                $text = "user_details.admin_id = $cuid";
            }else if($type_id == 13){
                $text = "user_details.aadmin='$cuid'";
            }else if($type_id == 4){
                $text = "user_details.pst_co='$cuid'";
            }else if($type_id == 15){
                $text = "user_details.sales_co = $cuid";
            }elseif($type_id == 19){
                $text = "user_details.ash_nae_co = '$cuid'";
            }else if($type_id == 20){
                $text = "user_details.ash_w_co='$cuid'";
            }else if($type_id == 21){
                $text = "user_details.ash_s_co='$cuid'";
            }else if($type_id == 22){
                $text = "user_details.rm_east_co='$cuid'";
            }else if($type_id == 23){
                $text = "user_details.rm_north_co='$cuid'";
            }else if($type_id == 24){
                $text = "user_details.ash_nae_co='$cuid'";
            }else{
                $text = "user_details.user_id='$cuid'";
            }
        }
      
        }else{
        $udetail = $this->Menu_model->get_userbyid($userid); 
        $type_id = $udetail[0]->type_id;
            if($type_id == 1){
                $text = "user_details.sadmin_id = $userid";
            }else if($type_id == 2){
                $text = "user_details.admin_id = $userid";
            }else if($type_id == 13){
                $text = "user_details.aadmin='$userid'";
            }else if($type_id == 4){
                $text = "user_details.pst_co='$userid'";
            }else if($type_id == 15){
                $text = "user_details.sales_co = $userid";
            }elseif($type_id == 19){
                $text = "user_details.ash_nae_co = '$userid'";
            }else if($type_id == 20){
                $text = "user_details.ash_w_co='$userid'";
            }else if($type_id == 21){
                $text = "user_details.ash_s_co='$userid'";
            }else if($type_id == 22){
                $text = "user_details.rm_east_co='$userid'";
            }else if($type_id == 23){
                $text = "user_details.rm_north_co='$userid'";
            }else if($type_id == 24){
                $text = "user_details.ash_nae_co='$userid'";
            }else{
                $text = "user_details.user_id='$userid'";
            }
            }
            if($userwise == 'User_Wise'){
                $text = "user_details.user_id='$userid'";
            }
        
             $query = $this->db->query("SELECT 
    d.report_date,
    -- Total active users under admin 45
    (SELECT COUNT(*) 
     FROM user_details 
     WHERE $text AND status = 'active'
    ) AS total_user,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL THEN u1.user_id
    END) AS total_present_user,
     COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.uclose IS NULL THEN u1.user_id
    END) AS total_present_and_not_closed_day_user,
       COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.uclose IS NOT NULL THEN u1.user_id
    END) AS total_present_and_closed_day_user,
    (
        (SELECT COUNT(*) 
         FROM user_details 
         WHERE $text AND status = 'active')
        -
        COUNT(DISTINCT CASE 
            WHEN ud.id IS NOT NULL THEN u1.user_id
        END)
    ) AS total_absent_user,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.wffo = 1 THEN u1.user_id
    END) AS total_work_from_office,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.wffo = 2 THEN u1.user_id
    END) AS total_work_from_field,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.wffo = 3 THEN u1.user_id
    END) AS total_work_from_field_office,
    
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL AND DATE(aut.date) = d.report_date THEN aut.user_id
    END) AS total_planning_set,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL AND DATE(aut.created_at) = d.report_date THEN aut.user_id
    END) AS same_day_planning,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL AND DATE(aut.created_at) != d.report_date AND DATE(aut.date) = d.report_date THEN aut.user_id
    END) AS previous_day_planning,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NULL THEN u1.user_id
    END) AS planner_not_set,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL 
          AND DATE(aut.created_at) != DATE(aut.date) 
          AND tpft.id IS NOT NULL THEN aut.user_id
    END) AS spdplanning_then_create_same_day_request
FROM (
    SELECT DATE(date) AS report_date 
    FROM autotask_time 
    WHERE date BETWEEN '$cdate' AND '$cdate'
    GROUP BY DATE(date)
) d
CROSS JOIN (
    SELECT user_id 
    FROM user_details 
    WHERE status = 'active' AND $text
) u1
LEFT JOIN autotask_time aut ON aut.user_id = u1.user_id AND DATE(aut.date) = d.report_date
LEFT JOIN task_plan_for_today tpft ON tpft.user_id = u1.user_id AND DATE(tpft.date) = d.report_date
LEFT JOIN user_day ud ON ud.user_id = u1.user_id AND DATE(ud.ustart) = d.report_date
GROUP BY d.report_date
ORDER BY d.report_date");
    $getPlannerReports = $query->result();
    $mtypes = 'total_present_user';
    $selectDayFilter = "
            , ud.ustart AS user_start,
            ud.uclose AS user_close,
            ud.usimg AS usimg,
            ud.ucimg AS ucimg,
            ud.slatitude AS slatitude,
            ud.slongitude AS slongitude,
            ud.clatitude AS clatitude,
            ud.clongitude AS clongitude,
            ud.wffo AS user_wffo,
            ud.queans AS user_queans,
            ud.queansc AS queansc
        ";
        if($mtypes == 'total_present_user') {
            $groupByClause      = "GROUP BY u1.user_id";
            $additionalWhere    = "AND ud.id IS NOT NULL";
            $selectFilter       = $selectDayFilter;
        }
$query1 = $this->db->query("SELECT 
        u1.*,
        user_type.name AS user_type_name
        $selectFilter
    FROM (
        SELECT DATE(date) AS report_date 
        FROM autotask_time 
        WHERE date BETWEEN '$cdate' AND '$cdate'
        GROUP BY DATE(date)
    ) d
    CROSS JOIN (
        SELECT * 
        FROM user_details 
        WHERE status = 'active' AND ($text)
    ) u1
    LEFT JOIN autotask_time aut  ON aut.user_id = u1.user_id AND DATE(aut.date) = d.report_date
    LEFT JOIN task_plan_for_today tpft ON tpft.user_id = u1.user_id AND DATE(tpft.date) = d.report_date
    LEFT JOIN user_day ud ON ud.user_id = u1.user_id AND DATE(ud.ustart) = d.report_date
    LEFT JOIN user_type ON user_type.id = u1.type_id
    WHERE 1=1
        $additionalWhere
    $groupByClause
");
  $totalTasksUserByDatas = $query1->result();
            $result = [
                'plannerReportDatas' => $getPlannerReports,
                'totalTasksUserByDatas' => $totalTasksUserByDatas
            ];
  
    return $result;
}
public function DayendManagementCheckingDatas($userid,$userwise,$cdate,$plannerDate) {
    
    if($userid =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "user_details.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "user_details.sadmin_id = $cuid";
        }else{
            $text = "user_details.admin_id = '$cuid'";
        }
      
        }else{
        $udetail = $this->Menu_model->get_userbyid($userid); 
        $type_id = $udetail[0]->type_id;
            if($type_id == 1){
                $text = "user_details.sadmin_id = $userid";
            }else if($type_id == 2){
                $text = "user_details.admin_id = $userid";
            }else if($type_id == 13){
                $text = "user_details.aadmin='$userid'";
            }else if($type_id == 4){
                $text = "user_details.pst_co='$userid'";
            }else if($type_id == 15){
                $text = "user_details.sales_co = $userid";
            }elseif($type_id == 19){
                $text = "user_details.ash_nae_co = '$userid'";
            }else if($type_id == 20){
                $text = "user_details.ash_w_co='$userid'";
            }else if($type_id == 21){
                $text = "user_details.ash_s_co='$userid'";
            }else if($type_id == 22){
                $text = "user_details.rm_east_co='$userid'";
            }else if($type_id == 23){
                $text = "user_details.rm_north_co='$userid'";
            }else if($type_id == 24){
                $text = "user_details.ash_nae_co='$userid'";
            }else{
                $text = "user_details.user_id='$userid'";
            }
            }
            if($userwise == 'User_Wise'){
                $text = "user_details.user_id='$userid'";
            }
             $query = $this->db->query("SELECT 
    d.report_date,
    (SELECT COUNT(*) 
     FROM user_details 
     WHERE $text AND status = 'active'
    ) AS total_user,
    COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL THEN u1.user_id
    END) AS total_present_user,
     COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.uclose IS NULL THEN u1.user_id
    END) AS total_present_and_not_closed_day_user,
       COUNT(DISTINCT CASE 
        WHEN ud.id IS NOT NULL AND ud.uclose IS NOT NULL THEN u1.user_id
    END) AS total_present_and_closed_day_user,
    (
        (SELECT COUNT(*) 
         FROM user_details 
         WHERE $text AND status = 'active')
        -
        COUNT(DISTINCT CASE 
            WHEN ud.id IS NOT NULL THEN u1.user_id
        END)
    ) AS total_absent_user,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NOT NULL THEN aut.user_id
    END) AS total_planning_set,
    COUNT(DISTINCT CASE 
        WHEN aut.id IS NULL THEN u1.user_id
    END) AS planner_not_set
FROM (
    SELECT DATE(date) AS report_date 
    FROM autotask_time 
    WHERE date BETWEEN '$cdate' AND '$cdate'
    GROUP BY DATE(date)
) d
CROSS JOIN (
    SELECT user_id 
    FROM user_details 
    WHERE status = 'active' AND $text
) u1
LEFT JOIN autotask_time aut ON aut.user_id = u1.user_id AND DATE(aut.date) = '$plannerDate'
LEFT JOIN task_plan_for_today tpft ON tpft.user_id = u1.user_id AND DATE(tpft.date) = d.report_date
LEFT JOIN user_day ud ON ud.user_id = u1.user_id AND DATE(ud.ustart) = d.report_date
GROUP BY d.report_date
ORDER BY d.report_date");
    $getPlannerReports = $query->result();
    $mtypes = 'total_present_user';
    $selectDayFilter = "
            , ud.ustart AS user_start,
            ud.uclose AS user_close,
            ud.usimg AS usimg,
            ud.ucimg AS ucimg,
            ud.slatitude AS slatitude,
            ud.slongitude AS slongitude,
            ud.clatitude AS clatitude,
            ud.clongitude AS clongitude,
            ud.wffo AS user_wffo,
            ud.queans AS user_queans,
            ud.queansc AS queansc
        ";
        if($mtypes == 'total_present_user') {
            $groupByClause      = "GROUP BY u1.user_id";
            $additionalWhere    = "AND ud.id IS NOT NULL";
            $selectFilter       = $selectDayFilter;
        }
$query1 = $this->db->query("SELECT 
        u1.*,
        user_type.name AS user_type_name
        $selectFilter
    FROM (
        SELECT DATE(date) AS report_date 
        FROM autotask_time 
        WHERE date BETWEEN '$cdate' AND '$cdate'
        GROUP BY DATE(date)
    ) d
    CROSS JOIN (
        SELECT * 
        FROM user_details 
        WHERE status = 'active' AND ($text)
    ) u1
    LEFT JOIN autotask_time aut  ON aut.user_id = u1.user_id AND DATE(aut.date) = '$plannerDate'
    LEFT JOIN user_day ud ON ud.user_id = u1.user_id AND DATE(ud.ustart) = d.report_date
    LEFT JOIN user_type ON user_type.id = u1.type_id
    WHERE 1=1
        $additionalWhere
    $groupByClause
");
  $totalTasksUserByDatas = $query1->result();
            $result = [
                'plannerReportDatas' => $getPlannerReports,
                'totalTasksUserByDatas' => $totalTasksUserByDatas
            ];
  
    return $result;
}
public function UserWholDayActivity($userid,$userwise,$cdate) {
    
    if($userid =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "u1.sadmin_id = $cuid";
        }else{
            $udetail = $this->Menu_model->get_userbyid($cuid); 
            $type_id = $udetail[0]->type_id;
            if($type_id == 1){
                $text = "u1.sadmin_id = $cuid";
            }else if($type_id == 2){
                $text = "u1.admin_id = $cuid";
            }else if($type_id == 13){
                $text = "u1.aadmin='$cuid'";
            }else if($type_id == 4){
                $text = "u1.pst_co='$cuid'";
            }else if($type_id == 15){
                $text = "u1.sales_co = $cuid";
            }elseif($type_id == 19){
                $text = "u1.ash_nae_co = '$cuid'";
            }else if($type_id == 20){
                $text = "u1.ash_w_co='$cuid'";
            }else if($type_id == 21){
                $text = "u1.ash_s_co='$cuid'";
            }else if($type_id == 22){
                $text = "u1.rm_east_co='$cuid'";
            }else if($type_id == 23){
                $text = "u1.rm_north_co='$cuid'";
            }else if($type_id == 24){
                $text = "u1.ash_nae_co='$cuid'";
            }else{
                $text = "u1.user_id='$cuid'";
            }
        }
      
        }else{
        $udetail = $this->Menu_model->get_userbyid($userid); 
        $type_id = $udetail[0]->type_id;
            if($type_id == 1){
                $text = "u1.sadmin_id = $userid";
            }else if($type_id == 2){
                $text = "u1.admin_id = $userid";
            }else if($type_id == 13){
                $text = "u1.aadmin='$userid'";
            }else if($type_id == 4){
                $text = "u1.pst_co='$userid'";
            }else if($type_id == 15){
                $text = "u1.sales_co = $userid";
            }elseif($type_id == 19){
                $text = "u1.ash_nae_co = '$userid'";
            }else if($type_id == 20){
                $text = "u1.ash_w_co='$userid'";
            }else if($type_id == 21){
                $text = "u1.ash_s_co='$userid'";
            }else if($type_id == 22){
                $text = "u1.rm_east_co='$userid'";
            }else if($type_id == 23){
                $text = "u1.rm_north_co='$userid'";
            }else if($type_id == 24){
                $text = "u1.ash_nae_co='$userid'";
            }else{
                $text = "u1.user_id='$userid'";
            }
            }
            if($userwise == 'User_Wise'){
                $text = "u1.user_id='$userid'";
            }
            
$query2 = $this->db->query("SELECT 
    u1.user_id,
    u1.type_id as user_type_id,
    u1.name AS username,
    ut.name AS user_roles,
    
    ud.ustart AS user_start,
    ud.uclose AS user_close,
    ud.usimg AS usimg,
    ud.ucimg AS ucimg,
    ud.slatitude AS slatitude,
    ud.slongitude AS slongitude,
    ud.clatitude AS clatitude,
    ud.clongitude AS clongitude,
    userworkfrom.TYPE AS planning_userworkfrom,
    userworkfrom1.TYPE AS start_userworkfrom,
    ud.wffo AS user_wffo,
    CASE 
        WHEN ud.ustart IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS user_start_own_day,
    CASE 
        WHEN ud.uclose IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS user_close_own_day,
    CASE 
        WHEN ud.wffo = autotask_time.userworkfrom AND autotask_time.userworkfrom != 0 AND autotask_time.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS start_as_like_planning,
    CASE 
        WHEN autotask_time.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS start_planning,
    autotask_time.created_at AS start_planning_on_day,
    CASE 
        WHEN tpft.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS request_same_day_task_plan,
    tpft.would_you_want AS request_same_day_would_you_want,
    tpft.request_remarks AS request_same_day_user_remarks,
    tpft.approvel_status AS request_same_day_approvel_status,
    tpft.remarks AS request_same_day_admin_remarks,
    tpft.created_at AS request_same_day_created_at,
    tpft.apr_time AS request_same_day_planner_apr_time,       
    CASE 
        WHEN pa.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS user_create_planner_approved_request,
    pa.request_type AS planner_request_type,
    pa.request_message AS planner_request_message,
    pa.approved_status AS planner_approved_status,
    u2.name AS planner_approved_by,
    pa.created_at AS planner_request_time,
    pa.approved_date AS planner_approved_date,
    
    CONCAT(
        TIMESTAMPDIFF(HOUR, pa.created_at, pa.approved_date), ' hours ',
        MOD(TIMESTAMPDIFF(MINUTE, pa.created_at, pa.approved_date), 60), ' minutes ',
        MOD(TIMESTAMPDIFF(SECOND, pa.created_at, pa.approved_date), 60), ' seconds'
    ) AS time_taken_in_planner_approved,
    CASE 
        WHEN srfl.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS special_request_for_leave,
    srfl.stime AS special_request_start,
    srfl.etime AS special_request_end,
    srfl.prupose AS special_request_prupose,
    srfl.approve_status AS special_request_approve_status,
    u3.name AS special_request_approve_by,
    srfl.approve_date AS special_request_approve_date,
    srfl.approve_remarks AS special_request_approve_admin_remarks,
    srfl.created_at AS special_request_for_create_time,
    
    CASE 
        WHEN cpr.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS request_pending_task_planning,
    cpr.request_type AS request_pending_task_planning_request_type,
    cpr.task_count AS request_pending_task_planning_task_count,
    cpr.request_remarks AS request_pending_task_planning_user_request_remarks,
    cpr.approved AS request_pending_task_planning_approved_status,
    u4.name AS request_pending_task_planning_approved_by,
    cpr.approved_date AS request_pending_task_planning_approve_date,
    cpr.approved_message AS request_pending_task_planning_admin_remarks,
    cpr.created_at AS request_pending_task_planning_create_time,
    
    CASE 
        WHEN pmr.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS pending_meetings_request,
    pmr.task_ids AS pending_meetings_request_task_ids,
    pmr.request_task_count AS pending_meetings_request_task_count,
    pmr.remarks AS pending_meetings_request_user_request_remarks,
    pmr.apr_status AS pending_meetings_request_approved_status,
    u5.name AS pending_meetings_request_approved_by,
    pmr.apr_date AS pending_meetings_request_approve_date,
    pmr.apr_remakrs AS pending_meetings_request_admin_remarks,
    pmr.created_at AS pending_meetings_request_create_time,
    
    IFNULL(totals.total_complete_task, 0) AS total_complete_task,
    IFNULL(totals.total_pending_task, 0) AS total_pending_task
FROM user_day ud
LEFT JOIN user_details u1 ON u1.user_id = ud.user_id 
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN (
    SELECT 
        user_id,
        DATE(appointmentdatetime) AS task_date,
        SUM(CASE 
                WHEN nextCFID != 0 AND (approved_status = 1 OR approved_status = '') THEN 1 
                ELSE 0 
            END) AS total_complete_task,
        SUM(CASE 
                WHEN nextCFID = 0 AND (approved_status = 1 OR approved_status = '') THEN 1 
                ELSE 0 
            END) AS total_pending_task
    FROM tblcallevents
    GROUP BY user_id, DATE(appointmentdatetime)
) totals ON totals.user_id = u1.user_id AND totals.task_date = DATE(ud.ustart)
LEFT JOIN autotask_time aut ON aut.user_id = u1.user_id AND DATE(aut.date) = DATE(ud.ustart)
LEFT JOIN task_plan_for_today tpft ON tpft.user_id = u1.user_id AND DATE(tpft.date) = DATE(ud.ustart)
LEFT JOIN planner_approved pa ON pa.user_id = u1.user_id AND pa.request_date = DATE(ud.ustart)
LEFT JOIN special_request_for_leave srfl ON srfl.user_id = u1.user_id AND DATE(srfl.date) = DATE(ud.ustart)
LEFT JOIN create_planner_request cpr ON cpr.request_user_id = u1.user_id AND DATE(cpr.request_date) = DATE(ud.ustart)
LEFT JOIN pending_meetings_request pmr ON pmr.user_uid = u1.user_id AND DATE(pmr.request_date) = DATE(ud.ustart)
LEFT JOIN user_details u2 ON u2.user_id = pa.approved_by 
LEFT JOIN user_details u3 ON u3.user_id = srfl.approve_by
LEFT JOIN user_details u4 ON u4.user_id = cpr.approved_by
LEFT JOIN user_details u5 ON u5.user_id = pmr.apr_by 
LEFT JOIN autotask_time ON autotask_time.user_id = u1.user_id AND DATE(autotask_time.date) = DATE(ud.ustart)
LEFT JOIN userworkfrom userworkfrom ON userworkfrom.id = autotask_time.userworkfrom
LEFT JOIN userworkfrom userworkfrom1 ON userworkfrom1.id = ud.wffo
WHERE 
    DATE(ud.ustart) BETWEEN '$cdate' AND '$cdate'
    AND $text
GROUP BY u1.user_id, DATE(ud.ustart)");
  $userWholeDays = $query2->result();
            $result = [
                'userWholeDays' => $userWholeDays
            ];
  
    return $result;
}
public function UserWholDayENDActivity($userid,$userwise,$cdate,$plannerDate) {
    
    if($userid =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "u1.sadmin_id = $cuid";
        }else{
            $text = "u1.admin_id = '$cuid'";
        }
      
        }else{
        $udetail = $this->Menu_model->get_userbyid($userid); 
        $type_id = $udetail[0]->type_id;
            if($type_id == 1){
                $text = "u1.sadmin_id = $userid";
            }else if($type_id == 2){
                $text = "u1.admin_id = $userid";
            }else if($type_id == 13){
                $text = "u1.aadmin='$userid'";
            }else if($type_id == 4){
                $text = "u1.pst_co='$userid'";
            }else if($type_id == 15){
                $text = "u1.sales_co = $userid";
            }elseif($type_id == 19){
                $text = "u1.ash_nae_co = '$userid'";
            }else if($type_id == 20){
                $text = "u1.ash_w_co='$userid'";
            }else if($type_id == 21){
                $text = "u1.ash_s_co='$userid'";
            }else if($type_id == 22){
                $text = "u1.rm_east_co='$userid'";
            }else if($type_id == 23){
                $text = "u1.rm_north_co='$userid'";
            }else if($type_id == 24){
                $text = "u1.ash_nae_co='$userid'";
            }else{
                $text = "u1.user_id='$userid'";
            }
            }
            if($userwise == 'User_Wise'){
                $text = "u1.user_id='$userid'";
            }
$query2 = $this->db->query("SELECT
    u1.user_id,
    u1.type_id as user_type_id,
    u1.name AS username,
    ut.name AS user_roles,
    ud.ustart AS user_start,
    ud.uclose AS user_close,
    ud.usimg AS usimg,
    ud.ucimg AS ucimg,
    ud.slatitude AS slatitude,
    ud.slongitude AS slongitude,
    ud.clatitude AS clatitude,
    ud.clongitude AS clongitude,
    uwf.type AS planning_userworkfrom,
    uwf1.type AS start_userworkfrom,
    ud.wffo AS user_wffo,
    CASE 
        WHEN ud.ustart IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS user_start_own_day,
    CASE 
        WHEN aut.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS start_planning,
	aut.date AS start_planning_on_date,
    aut.created_at AS start_planning_on_day,
    CASE 
        WHEN pa.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS user_create_planner_approved_request,
    pa.request_type      AS planner_request_type,
    pa.request_message   AS planner_request_message,
    pa.approved_status   AS planner_approved_status,
    u2.name              AS planner_approved_by,
    pa.created_at        AS planner_request_time,
    pa.approved_date     AS planner_approved_date,
    CONCAT(
        TIMESTAMPDIFF(HOUR, pa.created_at, pa.approved_date), ' hours ',
        MOD(TIMESTAMPDIFF(MINUTE, pa.created_at, pa.approved_date), 60), ' minutes ',
        MOD(TIMESTAMPDIFF(SECOND, pa.created_at, pa.approved_date), 60), ' seconds'
    ) AS time_taken_in_planner_approved,
    CASE 
        WHEN cpr.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS request_pending_task_planning,
    cpr.request_type      AS request_pending_task_planning_request_type,
    cpr.task_count        AS request_pending_task_planning_task_count,
    cpr.request_remarks   AS request_pending_task_planning_user_request_remarks,
    cpr.approved          AS request_pending_task_planning_approved_status,
    u4.name               AS request_pending_task_planning_approved_by,
    cpr.approved_date     AS request_pending_task_planning_approve_date,
    cpr.approved_message  AS request_pending_task_planning_admin_remarks,
    cpr.created_at        AS request_pending_task_planning_create_time,
    CASE 
        WHEN pmr.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS pending_meetings_request,
    pmr.request_date as task_planner_date,
    pmr.task_ids         AS pending_meetings_request_task_ids,
    pmr.request_task_count AS pending_meetings_request_task_count,
    pmr.remarks          AS pending_meetings_request_user_request_remarks,
    pmr.apr_status       AS pending_meetings_request_approved_status,
    u5.name              AS pending_meetings_request_approved_by,
    pmr.apr_date         AS pending_meetings_request_approve_date,
    pmr.apr_remakrs      AS pending_meetings_request_admin_remarks,
    pmr.created_at       AS pending_meetings_request_create_time,
    IFNULL(totals.total_complete_task, 0) AS total_complete_task,
    IFNULL(totals.total_pending_task, 0)  AS total_pending_task
FROM user_day ud
LEFT JOIN user_details u1 ON u1.user_id = ud.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN (
    SELECT 
        user_id,
        DATE(appointmentdatetime) AS task_date,
        SUM(
            CASE 
                WHEN nextCFID != 0 AND (approved_status = 1 OR approved_status = '') THEN 1 
                ELSE 0 
            END
        ) AS total_complete_task,
        SUM(
            CASE 
                WHEN nextCFID = 0 AND (approved_status = 1 OR approved_status = '') THEN 1 
                ELSE 0 
            END
        ) AS total_pending_task
    FROM tblcallevents
    GROUP BY user_id, DATE(appointmentdatetime)
) totals 
       ON totals.user_id = u1.user_id 
      AND totals.task_date = DATE(ud.ustart)
LEFT JOIN autotask_time aut ON aut.user_id = u1.user_id  AND DATE(aut.date) = '$plannerDate'
LEFT JOIN userworkfrom uwf ON uwf.id = aut.userworkfrom 
LEFT JOIN userworkfrom uwf1 ON uwf1.id = ud.wffo
LEFT JOIN planner_approved pa ON pa.user_id = u1.user_id AND pa.request_date = '$plannerDate'
LEFT JOIN create_planner_request cpr ON cpr.request_user_id = u1.user_id AND DATE(cpr.request_date) = '$plannerDate'
LEFT JOIN pending_meetings_request pmr ON pmr.user_uid = u1.user_id AND DATE(pmr.request_date) = '$plannerDate'
LEFT JOIN user_details u2 ON u2.user_id = pa.approved_by
LEFT JOIN user_details u4 ON u4.user_id = cpr.approved_by
LEFT JOIN user_details u5 ON u5.user_id = pmr.apr_by
WHERE DATE(ud.ustart) BETWEEN '$cdate' AND '$cdate' 
  AND $text
GROUP BY u1.user_id, DATE(ud.ustart)");
  $userWholeDays = $query2->result();
            $result = [
                'userWholeDays' => $userWholeDays
            ];
  
    return $result;
}
public function UserWholSameDayActivity($userid,$userwise,$cdate,$plannerDate) {
    
    // echo $userid;
    // die;
    if($userid =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "u1.sadmin_id = $cuid";
        }else{
            $text = "u1.admin_id = '$cuid'";
        }
      
        }else{
        $udetail = $this->Menu_model->get_userbyid($userid); 
        $type_id = $udetail[0]->type_id;
            if($type_id == 1){
                $text = "u1.sadmin_id = $userid";
            }else if($type_id == 2){
                $text = "u1.admin_id = $userid";
            }else if($type_id == 13){
                $text = "u1.aadmin='$userid'";
            }else if($type_id == 4){
                $text = "u1.pst_co='$userid'";
            }else if($type_id == 15){
                $text = "u1.sales_co = $userid";
            }elseif($type_id == 19){
                $text = "u1.ash_nae_co = '$userid'";
            }else if($type_id == 20){
                $text = "u1.ash_w_co='$userid'";
            }else if($type_id == 21){
                $text = "u1.ash_s_co='$userid'";
            }else if($type_id == 22){
                $text = "u1.rm_east_co='$userid'";
            }else if($type_id == 23){
                $text = "u1.rm_north_co='$userid'";
            }else if($type_id == 24){
                $text = "u1.ash_nae_co='$userid'";
            }else{
                $text = "u1.user_id='$userid'";
            }
            }
            if($userwise == 'User_Wise'){
                $text = "u1.user_id='$userid'";
            }
            // echo $text;
            // die;
$query2 = $this->db->query("SELECT
    u1.user_id,
    u1.type_id as user_type_id,
    u1.name AS username,
    ut.name AS user_roles,
    ud.ustart AS user_start,
    ud.uclose AS user_close,
    ud.usimg AS usimg,
    ud.ucimg AS ucimg,
    ud.slatitude AS slatitude,
    ud.slongitude AS slongitude,
    ud.clatitude AS clatitude,
    ud.clongitude AS clongitude,
    uwf.type AS planning_userworkfrom,
    uwf1.type AS start_userworkfrom,
    ud.wffo AS user_wffo,
    CASE 
        WHEN ud.ustart IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS user_start_own_day,
    CASE 
        WHEN aut.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS start_planning,
	aut.date AS start_planning_on_date,
    aut.created_at AS start_planning_on_day,
    CASE 
        WHEN ud.wffo = autotask_time.userworkfrom AND autotask_time.userworkfrom != 0 AND autotask_time.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS start_as_like_planning,
    CASE 
        WHEN autotask_time.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS start_planning,
    autotask_time.created_at AS start_planning_on_day,
    CASE 
        WHEN tpft.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS request_same_day_task_plan,
    tpft.would_you_want AS request_same_day_would_you_want,
    tpft.request_remarks AS request_same_day_user_remarks,
    tpft.approvel_status AS request_same_day_approvel_status,
    tpft.remarks AS request_same_day_admin_remarks,
    tpft.created_at AS request_same_day_created_at,
    tpft.apr_time AS request_same_day_planner_apr_time,   
    CASE 
        WHEN pa.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS user_create_planner_approved_request,
    pa.request_type      AS planner_request_type,
    pa.request_message   AS planner_request_message,
    pa.approved_status   AS planner_approved_status,
    u2.name              AS planner_approved_by,
    pa.created_at        AS planner_request_time,
    pa.approved_date     AS planner_approved_date,
    CONCAT(
        TIMESTAMPDIFF(HOUR, pa.created_at, pa.approved_date), ' hours ',
        MOD(TIMESTAMPDIFF(MINUTE, pa.created_at, pa.approved_date), 60), ' minutes ',
        MOD(TIMESTAMPDIFF(SECOND, pa.created_at, pa.approved_date), 60), ' seconds'
    ) AS time_taken_in_planner_approved,
    CASE 
        WHEN cpr.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS request_pending_task_planning,
    cpr.request_type      AS request_pending_task_planning_request_type,
    cpr.task_count        AS request_pending_task_planning_task_count,
    cpr.request_remarks   AS request_pending_task_planning_user_request_remarks,
    cpr.approved          AS request_pending_task_planning_approved_status,
    u4.name               AS request_pending_task_planning_approved_by,
    cpr.approved_date     AS request_pending_task_planning_approve_date,
    cpr.approved_message  AS request_pending_task_planning_admin_remarks,
    cpr.created_at        AS request_pending_task_planning_create_time,
    CASE 
        WHEN pmr.id IS NOT NULL THEN 'Yes' 
        ELSE 'No' 
    END AS pending_meetings_request,
    pmr.request_date as task_planner_date,
    pmr.task_ids         AS pending_meetings_request_task_ids,
    pmr.request_task_count AS pending_meetings_request_task_count,
    pmr.remarks          AS pending_meetings_request_user_request_remarks,
    pmr.apr_status       AS pending_meetings_request_approved_status,
    u5.name              AS pending_meetings_request_approved_by,
    pmr.apr_date         AS pending_meetings_request_approve_date,
    pmr.apr_remakrs      AS pending_meetings_request_admin_remarks,
    pmr.created_at       AS pending_meetings_request_create_time,
    IFNULL(totals.total_complete_task, 0) AS total_complete_task,
    IFNULL(totals.total_pending_task, 0)  AS total_pending_task
FROM user_day ud
LEFT JOIN user_details u1 ON u1.user_id = ud.user_id
LEFT JOIN user_type ut ON ut.id = u1.type_id
LEFT JOIN (
    SELECT 
        user_id,
        DATE(appointmentdatetime) AS task_date,
        SUM(
            CASE 
                WHEN nextCFID != 0 AND (approved_status = 1 OR approved_status = '') THEN 1 
                ELSE 0 
            END
        ) AS total_complete_task,
        SUM(
            CASE 
                WHEN nextCFID = 0 AND (approved_status = 1 OR approved_status = '') THEN 1 
                ELSE 0 
            END
        ) AS total_pending_task
    FROM tblcallevents
    GROUP BY user_id, DATE(appointmentdatetime)
) totals 
       ON totals.user_id = u1.user_id 
      AND totals.task_date = DATE(ud.ustart)
LEFT JOIN autotask_time aut ON aut.user_id = u1.user_id  AND DATE(aut.date) = '$plannerDate'
LEFT JOIN userworkfrom uwf ON uwf.id = aut.userworkfrom 
LEFT JOIN userworkfrom uwf1 ON uwf1.id = ud.wffo
LEFT JOIN planner_approved pa ON pa.user_id = u1.user_id AND pa.request_date = '$plannerDate'
LEFT JOIN task_plan_for_today tpft ON tpft.user_id = u1.user_id AND DATE(tpft.date) = '$plannerDate'
LEFT JOIN create_planner_request cpr ON cpr.request_user_id = u1.user_id AND DATE(cpr.request_date) = '$plannerDate'
LEFT JOIN pending_meetings_request pmr ON pmr.user_uid = u1.user_id AND DATE(pmr.request_date) = '$plannerDate'
LEFT JOIN autotask_time ON autotask_time.user_id = u1.user_id AND DATE(autotask_time.date) = DATE(ud.ustart)
LEFT JOIN user_details u2 ON u2.user_id = pa.approved_by
LEFT JOIN user_details u4 ON u4.user_id = cpr.approved_by
LEFT JOIN user_details u5 ON u5.user_id = pmr.apr_by
WHERE DATE(ud.ustart) BETWEEN '$cdate' AND '$cdate' 
  AND $text
GROUP BY u1.user_id, DATE(ud.ustart)");
  $userWholeDays = $query2->result();
            $result = [
                'userWholeDays' => $userWholeDays
            ];
  
    return $result;
}
public  function checkNextPlannerDate($date) {
// Get the user session details
    $user = $this->session->userdata('user');
    $uid = $user['user_id'];
// Debug log to see the passed date and if it's valid or not
    log_message('debug', "Initial date passed: $date, Is holiday or leave: " . ($this->isCheckHolidayOrLeave($uid, $date) ? 'Yes' : 'No'));
     if (date('l', strtotime($date)) == 'Saturday') {
        $weekOfMonth = ceil(date('j', strtotime($date)) / 7); // Calculate the week number of the month (1st, 2nd, etc.)
        
        if ($weekOfMonth == 2 || $weekOfMonth == 4) {
            log_message('debug', "Date is a 2nd or 4th Saturday: $date");
            $date = date('Y-m-d', strtotime($date . ' +2 day'));
            // Perform actions for 2nd or 4th Saturday here
        }
    }
    // First, check if the given date is a Sunday and adjust to Monday if it is
    if (date('l', strtotime($date)) == 'Sunday') {
        log_message('debug', "Initial date is Sunday, adjusting to Monday: $date");
        $date = date('Y-m-d', strtotime($date . ' +1 day')); // Set to Monday
    }
    // If the given date is valid (not holiday or leave), return it as is
    if (!$this->isCheckHolidayOrLeave($uid, $date)) {
        // Debug log to confirm the given date is valid
        log_message('debug', "Returning the original date: $date");
        return $date;
    }
// Start by calculating the next date
    $adate = getNextDate($date);
    // Check if the next date is Sunday, and adjust it to Monday if so
    if (date('l', strtotime($adate)) == 'Sunday') {
        log_message('debug', "Next date is Sunday, adjusting to Monday: $adate");
        $adate = date('Y-m-d', strtotime($adate . ' +1 day')); // Set to Monday
    }
// Keep checking the next days until we find a valid date (not a holiday or leave)
    while ($this->isCheckHolidayOrLeave($uid, $adate)) {
        // If the next date is a holiday or leave, get the next date
        $adate = getNextDate($adate);
        // If the next date is Sunday, skip to Monday
        if (date('l', strtotime($adate)) == 'Sunday') {
            log_message('debug', "Next date is Sunday, adjusting to Monday: $adate");
            $adate = date('Y-m-d', strtotime($adate . ' +1 day')); // Set to Monday
        }
        // Debug log for each iteration
        log_message('debug', "Checking date: $adate, Is holiday or leave: " . ($this->isCheckHolidayOrLeave($uid, $adate) ? 'Yes' : 'No'));
    }
    // Debug log to confirm the final date returned
    log_message('debug', "Returning adjusted date: $adate");
    // Return the final valid date
    return $adate;
}
public function isCheckHolidayOrLeave($uid, $date) {
    // Check if the date is a holiday
    $checkforHoliday = checkforHoliday($date);
    
    // Check if the date has leave approved
    $checkLeaveForDay = checkLeaveForDay($uid, $date);
    
    // Log the results of the checks for debugging
    log_message('debug', "Checking date: $date, Holiday check: " . sizeof($checkforHoliday) . ", Leave check: " . (sizeof($checkLeaveForDay) > 0 ? 'Yes' : 'No'));
    
    // Return true if either is a holiday or the user has leave
    return (sizeof($checkforHoliday) > 0 || sizeof($checkLeaveForDay) > 0);
}
public function getAllCompanyBYRollesMaiingByUid($userid){
   
     $text = "u1.user_id = '$userid'";
     $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];
    $totalStatusQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN init_call.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN init_call.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN init_call.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN init_call.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN init_call.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN init_call.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN init_call.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN init_call.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN init_call.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN init_call.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN init_call.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN init_call.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (init_call.cstatus = 14 || init_call.upsell_client = 'yes') THEN 1 END) AS on_boarded
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_status = $totalStatusQuerysByAdminRoles->result();
$totalOldCaegoryQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.focus_funnel = 'yes' THEN 1 END) AS Focus_Funnel,
    COUNT(CASE WHEN init_call.upsell_client = 'yes' THEN 1 END) AS Upsell_Client,
    COUNT(CASE WHEN init_call.keycompany = 'yes' THEN 1 END) AS Key_Client,
    COUNT(CASE WHEN init_call.pkclient = 'yes' THEN 1 END) AS Positive_Key_Client,
    COUNT(CASE WHEN init_call.priorityc = 'yes' THEN 1 END) AS Priority_Calling,
    COUNT(CASE WHEN init_call.topspender = 'yes' THEN 1 END) AS Top_Spender,
    COUNT(CASE WHEN init_call.potential = 'yes' THEN 1 END) AS Potential_Client
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_old_category = $totalOldCaegoryQuerysByAdminRoles->result();
$totalNewCaegoryQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.q1_twetenty_closure_funnel = '$cfyear' THEN 1 END) AS q1_twetenty_closure_funnel,
    COUNT(CASE WHEN init_call.potential_funnel_for_fy = '$cfyear' THEN 1 END) AS potential_funnel_for_fy,
    COUNT(CASE WHEN init_call.to_be_nurtured_for_fy = '$cfyear' THEN 1 END) AS to_be_nurtured_for_fy,
    COUNT(CASE WHEN init_call.fifity_new_lead_funnel = '$cfyear' THEN 1 END) AS fifity_new_lead_funnel,
    COUNT(CASE WHEN init_call.anchor_clients = 'yes' THEN 1 END) AS anchor_clients
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_new_category = $totalNewCaegoryQuerysByAdminRoles->result();
$currentQuarterByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.in_quarter = '20 Closure Funnel in $currentQuarter' THEN 1 END) AS current_quarter_twetenty_closure_funnel,
    COUNT(CASE WHEN init_call.in_quarter = 'Potential Funnel For $currentQuarter' THEN 1 END) AS current_quarter_potential_funnel_for_fy,
    COUNT(CASE WHEN init_call.in_quarter = 'To be Nurtured for $currentQuarter' THEN 1 END) AS current_quarter_to_be_nurtured_for_fy
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
WHERE
    $text AND u1.status = 'active'
");
$total_current_quarter = $currentQuarterByAdminRoles->result();
$totalSiignQuerysByAdminRoles = $this->db->query("SELECT
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN init_call.mainbd = u1.user_id AND (init_call.apst IS NOT NULL AND init_call.apst != 0) THEN 1 END) AS PST_assign_funnel,
    COUNT(CASE WHEN init_call.mainbd = u1.user_id AND (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0) THEN 1 END) AS cluster_manager_assign_funnel,
    COUNT(CASE 
        WHEN init_call.mainbd = u1.user_id AND 
            ((init_call.apst IS NOT NULL AND init_call.apst != 0) AND 
             (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0)) 
        THEN 1 
    END) AS PST_and_cluster_manager_assign_funnel,
    COUNT(
    CASE 
        WHEN init_call.mainbd = u1.user_id 
            AND (init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Sales_Head_(NAE)_assign_funnel`,
    COUNT(
    CASE 
        WHEN init_call.mainbd = u1.user_id 
            AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Sales_Head_(W)_assign_funnel`,
    COUNT(
    CASE 
        WHEN init_call.mainbd = u1.user_id 
            AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Sales_Head_(S)_assign_funnel`,
    COUNT(
    CASE 
        WHEN init_call.mainbd = u1.user_id 
            AND (init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) 
        THEN 1 
    END
    ) AS `RM_East_assign_funnel`,
    COUNT(
    CASE 
        WHEN init_call.mainbd = u1.user_id 
            AND (init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) 
        THEN 1 
    END
    ) AS `RM_North_assign_funnel`,
    COUNT(
    CASE 
        WHEN init_call.mainbd = u1.user_id 
            AND (init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0) 
        THEN 1 
    END
    ) AS `Assistant_Cluster_Manager_assign_funnel`
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
WHERE
    $text AND u1.status = 'active'
");
$total_funnels_assign = $totalSiignQuerysByAdminRoles->result();
$totalFunnelByUserQuery = $this->db->query("SELECT
        u1.name AS user_name,
        u1.user_id AS current_user_id,
        COUNT(init_call.id) AS total,
        -- Funnel Statuses
        COUNT(CASE WHEN init_call.cstatus = 1 THEN 1 END) AS open,
        COUNT(CASE WHEN init_call.cstatus = 2 THEN 1 END) AS reachout,
        COUNT(CASE WHEN init_call.cstatus = 3 THEN 1 END) AS tentative,
        COUNT(CASE WHEN init_call.cstatus = 4 THEN 1 END) AS will_do_later,
        COUNT(CASE WHEN init_call.cstatus = 5 THEN 1 END) AS not_interested,
        COUNT(CASE WHEN init_call.cstatus = 6 THEN 1 END) AS positive,
        COUNT(CASE WHEN init_call.cstatus = 7 THEN 1 END) AS closure,
        COUNT(CASE WHEN init_call.cstatus = 8 THEN 1 END) AS open_rpem,
        COUNT(CASE WHEN init_call.cstatus = 9 THEN 1 END) AS very_positive,
        COUNT(CASE WHEN init_call.cstatus = 10 THEN 1 END) AS ttd_reachout,
        COUNT(CASE WHEN init_call.cstatus = 11 THEN 1 END) AS wno_reachout,
        COUNT(CASE WHEN init_call.cstatus = 12 THEN 1 END) AS positive_nap,
        COUNT(CASE WHEN init_call.cstatus = 13 THEN 1 END) AS very_positive_nap,
        COUNT(CASE WHEN (init_call.cstatus = 14 || init_call.upsell_client = 'yes') THEN 1 END) AS on_boarded,
        
        -- Flags
        COUNT(CASE WHEN init_call.focus_funnel = 'yes' THEN 1 END) AS Focus_Funnel,
        COUNT(CASE WHEN init_call.upsell_client = 'yes' THEN 1 END) AS Upsell_Client,
        COUNT(CASE WHEN init_call.keycompany = 'yes' THEN 1 END) AS Key_Client,
        COUNT(CASE WHEN init_call.pkclient = 'yes' THEN 1 END) AS Positive_Key_Client,
        COUNT(CASE WHEN init_call.priorityc = 'yes' THEN 1 END) AS Priority_Calling,
        COUNT(CASE WHEN init_call.topspender = 'yes' THEN 1 END) AS Top_Spender,
        COUNT(CASE WHEN init_call.potential = 'yes' THEN 1 END) AS Potential_Client,
        -- Use topspender or another field for potential, not same
        -- Year-Based Funnels
        COUNT(CASE WHEN init_call.q1_twetenty_closure_funnel = '$cfyear' THEN 1 END) AS q1_twetenty_closure_funnel,
        COUNT(CASE WHEN init_call.potential_funnel_for_fy = '$cfyear' THEN 1 END) AS potential_funnel_for_fy,
        COUNT(CASE WHEN init_call.to_be_nurtured_for_fy = '$cfyear' THEN 1 END) AS to_be_nurtured_for_fy,
        COUNT(CASE WHEN init_call.fifity_new_lead_funnel = '$cfyear' THEN 1 END) AS fifity_new_lead_funnel,
        COUNT(CASE WHEN init_call.anchor_clients = 'yes' THEN 1 END) AS anchor_clients,
        COUNT(CASE WHEN init_call.in_quarter = '20 Closure Funnel in $currentQuarter' THEN 1 END) AS current_quarter_twetenty_closure_funnel,
        COUNT(CASE WHEN init_call.in_quarter = 'Potential Funnel For $currentQuarter' THEN 1 END) AS current_quarter_potential_funnel_for_fy,
        COUNT(CASE WHEN init_call.in_quarter = 'To be Nurtured for $currentQuarter' THEN 1 END) AS current_quarter_to_be_nurtured_for_fy,
        -- Assignment Status
        COUNT(CASE WHEN init_call.mainbd = u1.user_id AND (init_call.apst IS NOT NULL AND init_call.apst != 0) THEN 1 END) AS PST_assign_funnel,
        COUNT(CASE WHEN init_call.mainbd = u1.user_id AND (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0) THEN 1 END) AS cluster_manager_assign_funnel,
        COUNT(CASE 
            WHEN init_call.mainbd = u1.user_id AND 
                ((init_call.apst IS NOT NULL AND init_call.apst != 0) AND 
                (init_call.clm_id IS NOT NULL AND init_call.clm_id != 0)) 
            THEN 1 
        END) AS PST_and_cluster_manager_assign_funnel,
        COUNT(
        CASE 
            WHEN init_call.mainbd = u1.user_id 
                AND (init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Sales_Head_NAE_assign_funnel`,
        COUNT(
        CASE 
            WHEN init_call.mainbd = u1.user_id 
                AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Sales_Head_W_assign_funnel`,
        COUNT(
        CASE 
            WHEN init_call.mainbd = u1.user_id 
                AND (init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Sales_Head_S_assign_funnel`,
        COUNT(
        CASE 
            WHEN init_call.mainbd = u1.user_id 
                AND (init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) 
            THEN 1 
        END
        ) AS `RM_East_assign_funnel`,
        COUNT(
        CASE 
            WHEN init_call.mainbd = u1.user_id 
                AND (init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) 
            THEN 1 
        END
        ) AS `RM_North_assign_funnel`,
        COUNT(
        CASE 
            WHEN init_call.mainbd = u1.user_id 
                AND (init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0) 
            THEN 1 
        END
        ) AS `Assistant_Cluster_Manager_assign_funnel`
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    WHERE $text AND u1.status = 'active'
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$total_funnel_by_user = $totalFunnelByUserQuery->result();
 $totalPartnerQuerysByAdminRoles = $this->db->query("SELECT
 
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN partner_master.id = 1 THEN 1 END) AS corporate,
    COUNT(CASE WHEN partner_master.id = 2 THEN 1 END) AS PSU,
    COUNT(CASE WHEN partner_master.id = 3 THEN 1 END) AS NGO,
    COUNT(CASE WHEN partner_master.id = 4 THEN 1 END) AS Private_School,
    COUNT(CASE WHEN partner_master.id = 6 THEN 1 END) AS Individual,
    COUNT(CASE WHEN partner_master.id = 8 THEN 1 END) AS Govt_Off,
    COUNT(CASE WHEN partner_master.id = 9 THEN 1 END) AS Other,
    COUNT(CASE WHEN partner_master.id = 10 THEN 1 END) AS Online,
    COUNT(CASE WHEN partner_master.id = 11 THEN 1 END) AS STEM_School,
    COUNT(CASE WHEN partner_master.id = 12 THEN 1 END) AS Foundation,
    COUNT(CASE WHEN partner_master.id = 13 THEN 1 END) AS MNC,
    COUNT(CASE WHEN partner_master.id = 14 THEN 1 END) AS HO_Leads,
    COUNT(CASE WHEN partner_master.id = 16 THEN 1 END) AS DMFT,
    COUNT(CASE WHEN partner_master.id = 17 THEN 1 END) AS Elected_Representatives
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
WHERE
    $text AND u1.status = 'active' 
");
$total_funnels_partner = $totalPartnerQuerysByAdminRoles->result();
$totalPartnerUserQuerysByAdminRoles = $this->db->query("SELECT
 u1.name AS username,
        u1.user_id AS user_id,
    COUNT(init_call.id) AS total,
    COUNT(CASE WHEN partner_master.id = 1 THEN 1 END) AS corporate,
    COUNT(CASE WHEN partner_master.id = 2 THEN 1 END) AS PSU,
    COUNT(CASE WHEN partner_master.id = 3 THEN 1 END) AS NGO,
    COUNT(CASE WHEN partner_master.id = 4 THEN 1 END) AS Private_School,
    COUNT(CASE WHEN partner_master.id = 6 THEN 1 END) AS Individual,
    COUNT(CASE WHEN partner_master.id = 8 THEN 1 END) AS Govt_Off,
    COUNT(CASE WHEN partner_master.id = 9 THEN 1 END) AS Other,
    COUNT(CASE WHEN partner_master.id = 10 THEN 1 END) AS Online,
    COUNT(CASE WHEN partner_master.id = 11 THEN 1 END) AS STEM_School,
    COUNT(CASE WHEN partner_master.id = 12 THEN 1 END) AS Foundation,
    COUNT(CASE WHEN partner_master.id = 13 THEN 1 END) AS MNC,
    COUNT(CASE WHEN partner_master.id = 14 THEN 1 END) AS HO_Leads,
    COUNT(CASE WHEN partner_master.id = 16 THEN 1 END) AS DMFT,
    COUNT(CASE WHEN partner_master.id = 17 THEN 1 END) AS Elected_Representatives
FROM
    init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
WHERE
    $text AND u1.status = 'active' GROUP BY u1.user_id ORDER BY u1.name ASC
");
$total_funnels_partner_user = $totalPartnerUserQuerysByAdminRoles->result();
 $totalCluster = $this->db->query("SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    -- SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            -- WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND cluster.travelType IS NULL THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL");
$totalClusterDatas = $totalCluster->result();
 $totalClusterBYBD = $this->db->query("SELECT
    categorized.user_id,
    categorized.username,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND cluster.travelType IS NULL THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL
GROUP BY categorized.user_id, categorized.username");
$totalClusterBYBDatas = $totalClusterBYBD->result();
 $totalClusterBYClusterName = $this->db->query("SELECT
	categorized.cluster_id,
    categorized.clustername,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        cluster.clustername,
    	cluster.id as cluster_id,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND (cluster.travelType IS NULL OR cluster.travelType = '') THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL AND categorized.cluster_id IS  NOT NULL
GROUP BY categorized.clustername");
$totalClusterBYClusterNameDatas = $totalClusterBYClusterName->result();
 $totalClusterBYClusterNameBYBD = $this->db->query("SELECT
	categorized.user_id,
    categorized.username,
	categorized.cluster_id,
    categorized.clustername,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'marked_but_base_out_not_set' THEN 1 ELSE 0 END) AS marked_travel_cluster_but_base_out_not_set,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        cluster.clustername,
    	cluster.id as cluster_id,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.id IS NOT NULL AND (cluster.travelType IS NULL OR cluster.travelType = '') THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL AND categorized.cluster_id IS  NOT NULL
GROUP BY categorized.user_id, categorized.username,categorized.clustername");
$totalClusterBYClusterNameBYBDDatas = $totalClusterBYClusterNameBYBD->result();
 $totalClusterBYStatus = $this->db->query("SELECT
    categorized.status_id,
    categorized.status_name,
    COUNT(*) AS total,
    SUM(CASE WHEN travel_category = 'base' THEN 1 ELSE 0 END) AS base_location,
    SUM(CASE WHEN travel_category = 'outStation' THEN 1 ELSE 0 END) AS outStation_location,
    SUM(CASE WHEN travel_category = 'not_set_cluster' THEN 1 ELSE 0 END) AS not_set_travel_cluster
FROM (
    SELECT DISTINCT init_call.id,
        u1.user_id,
        u1.name AS username,
        cluster.clustername,
        cluster.id AS cluster_id,
        status.id AS status_id,
        status.name AS status_name,
        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NULL OR init_call.cluster_id = 0 OR cluster.id IS NULL THEN 'not_set_cluster'
        END AS travel_category
    FROM init_call
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    LEFT JOIN status ON status.id = init_call.cstatus
    WHERE $text AND u1.status = 'active'
) AS categorized
WHERE travel_category IS NOT NULL
GROUP BY categorized.status_id, categorized.status_name");
$totalClusterBYStatusDatas = $totalClusterBYStatus->result();
    $result = [
        'total_funnels_status'              => $total_funnels_status,
        'total_funnels_old_category'        => $total_funnels_old_category,
        'total_funnels_new_category'        => $total_funnels_new_category,
        'total_funnels_assign'              => $total_funnels_assign,
        'total_funnel_by_user'              => $total_funnel_by_user,
        'total_funnels_partner'             => $total_funnels_partner,
        'total_funnels_partner_user'        => $total_funnels_partner_user,
        'total_current_quarter'             => $total_current_quarter,
        'totalClusterDatas'                 => $totalClusterDatas,
        'totalClusterBYBDatas'              => $totalClusterBYBDatas,
        'totalClusterBYClusterNameDatas'    => $totalClusterBYClusterNameDatas,
        'totalClusterBYClusterNameBYBDDatas'=> $totalClusterBYClusterNameBYBDDatas,
        'totalClusterBYStatusDatas'=> $totalClusterBYStatusDatas
    ];
    return $result;
}
public function GetCompanySameStatusSinceDays($userid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "user_details.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "user_details.admin_id = $userid";
    }else if($type_id == 3){
        $text = "user_details.user_id = $userid";
    }else if($type_id == 4){
        $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
    }else if($type_id == 15){
        $text = "user_details.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
    }else{
        $text = "user_details.admin_id = $userid";
    }
    if($admin_id_filter =='all'){
        // $text = "user_details.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
               $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id ==1){
                    $text = "user_details.sadmin_id = $userid";
                }else if($type_id == 2){
                    $text = "user_details.admin_id = $userid";
                }else if($type_id == 3){
                    $text = "user_details.user_id = $userid";
                }else if($type_id == 4){
                    $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "user_details.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
                }else if($type_id == 20){
                    $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 21){
                    $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 22){
                    $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 23){
                    $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 24){
                    $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
                }else{
                    $text = "user_details.admin_id = $userid";
                }
        }
    }
     $userstatus = "user_details.status = 'active'";
     $cuserstatus = "u1.status = 'active'";
//      $query = $this->db->query("SELECT
//     init_call.id AS inid,
//     company_master.id as cid,
//     company_master.compname AS compname,
//     u1.name as main_bd_name,
//     partner_master.name AS pname,
//     cur_status.name AS current_status,
//     s2.name as last_task_on_status_name,
//     s3.name as last_update_status_name,
//     a1.name as last_task_name,
//     user_details.name AS task_username,
//     main_review.sdate AS main_review_date,
//     main_review.rtype AS main_review_rtype,
//     MAX(DATEDIFF('$edate', tblcallevents.appointmentdatetime)) AS days,
//     GREATEST(
//         DATE(main_review.sdate),
//         DATE(tblcallevents.updated_at)
//     ) AS review_date,
//     COUNT(
//         CASE 
//             WHEN DATE(tblcallevents.appointmentdatetime) 
//                  BETWEEN '$sdate' AND '$edate'
//             THEN tblcallevents.id 
//         END
//     ) AS total_log_in_days,
//     (
//         SELECT tce.appointmentdatetime
//         FROM tblcallevents tce
//         WHERE tce.cid_id = init_call.id
//         ORDER BY tce.id DESC
//         LIMIT 1
//     ) AS last_task_update,
//     DATEDIFF(
//         '$edate',
//         MAX(tblcallevents.appointmentdatetime)
//     ) AS last_task_days,
//     (
//         SELECT tce.id
//         FROM tblcallevents tce
//         WHERE tce.cid_id = init_call.id
//         ORDER BY tce.id DESC
//         LIMIT 1
//     ) AS last_task_id,
//     (
//     SELECT 
//         CASE 
//             WHEN tce.nextCFID = 0 THEN 'Pending'
//             ELSE 'Complete'
//         END
//     FROM tblcallevents tce
//     WHERE tce.cid_id = init_call.id
//     ORDER BY tce.id DESC
//     LIMIT 1
// ) AS last_task_status
// FROM init_call
// LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
// LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
// LEFT JOIN tblcallevents ON tblcallevents.cid_id = init_call.id
// LEFT JOIN main_review ON main_review.inid = init_call.id
// LEFT JOIN user_details ON user_details.user_id = tblcallevents.user_id
// LEFT JOIN status cur_status ON cur_status.id = init_call.cstatus
// LEFT JOIN status s2 ON s2.id = tblcallevents.status_id
// LEFT JOIN status s3 ON s3.id = tblcallevents.nstatus_id
// LEFT JOIN action a1 ON a1.id = tblcallevents.actiontype_id
// LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
// WHERE
//     $text
//     AND init_call.cstatus != '' 
//     AND tblcallevents.id IS NOT NULL
//     AND DATE(tblcallevents.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
//     AND main_review.sdate IS NOT NULL
//     AND (
//         SELECT COUNT(*) 
//         FROM tblcallevents tce
//         WHERE DATE(tce.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
//         AND tce.cid_id = init_call.id
//     ) >= 3
//     AND NOT EXISTS (
//         SELECT 1
//         FROM tblcallevents tce
//         WHERE tce.cid_id = init_call.id
//         AND DATE(tce.appointmentdatetime) = '$edate'
//     )
// GROUP BY
//     init_call.id,
//     init_call.cstatus
// HAVING
//     days > $days_inclusive
//     AND last_task_days > $days_inclusive
//     AND last_task_update < '$edate'
//     AND last_task_update != '0000-00-00 00:00:00'
//     AND review_date > main_review.sdate");
     $query = $this->db->query("SELECT *
FROM (
    SELECT 
        init_call.id AS inid,
        company_master.id AS cid,
        company_master.compname AS compname,
        u1.name AS main_bd_name,
        partner_master.name AS pname,
        cur_status.name AS current_status,
        s2.name AS last_task_on_status_name,
        s3.name AS last_update_status_name,
        a1.name AS last_task_name,
        user_details.name AS task_username,
        main_review.sdate AS main_review_date,
        main_review.rtype AS main_review_rtype,
        MAX(DATEDIFF('$edate', tblcallevents.appointmentdatetime)) AS days,
        GREATEST(
            DATE(main_review.sdate),
            DATE(MAX(tblcallevents.updated_at))
        ) AS review_date,
        COUNT(
            CASE 
                WHEN DATE(tblcallevents.appointmentdatetime) 
                     BETWEEN '$sdate' AND '$edate' 
                THEN tblcallevents.id 
            END
        ) AS total_log_in_days,
        ( SELECT tce.appointmentdatetime 
          FROM tblcallevents tce 
          WHERE tce.cid_id = init_call.id 
          ORDER BY tce.id DESC 
          LIMIT 1 ) AS last_task_update,
        DATEDIFF(
            '$edate', 
            MAX(tblcallevents.appointmentdatetime)
        ) AS last_task_days,
        ( SELECT tce.id 
          FROM tblcallevents tce 
          WHERE tce.cid_id = init_call.id 
          ORDER BY tce.id DESC 
          LIMIT 1 ) AS last_task_id,
        ( SELECT CASE 
                   WHEN tce.nextCFID = 0 THEN 'Pending' 
                   ELSE 'Complete' 
                 END
          FROM tblcallevents tce 
          WHERE tce.cid_id = init_call.id 
          ORDER BY tce.id DESC 
          LIMIT 1 ) AS last_task_status
    FROM init_call
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
    LEFT JOIN tblcallevents 
  ON tblcallevents.id = (
      SELECT MAX(tce.id) 
      FROM tblcallevents tce 
      WHERE tce.cid_id = init_call.id
  )
    LEFT JOIN main_review ON main_review.inid = init_call.id
    LEFT JOIN user_details ON user_details.user_id = tblcallevents.user_id
    LEFT JOIN status cur_status ON cur_status.id = init_call.cstatus
    LEFT JOIN status s2 ON s2.id = tblcallevents.status_id
    LEFT JOIN status s3 ON s3.id = tblcallevents.nstatus_id
  LEFT JOIN action a1 
  ON a1.id = (
      SELECT tce.actiontype_id
      FROM tblcallevents tce
      WHERE tce.cid_id = init_call.id
      ORDER BY tce.id DESC
      LIMIT 1
  )
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    WHERE $text AND $userstatus AND $cuserstatus
      AND init_call.cstatus != ''
      AND init_call.cstatus NOT IN (7,14)
      AND tblcallevents.id IS NOT NULL
      AND main_review.sdate IS NOT NULL
     
      AND NOT EXISTS (
            SELECT 1 
            FROM tblcallevents tce 
            WHERE tce.cid_id = init_call.id 
              AND DATE(tce.appointmentdatetime) = '$edate'
          )
    GROUP BY init_call.id, init_call.cstatus
) AS sub
WHERE sub.days > $days_inclusive
  AND sub.last_task_days > $days_inclusive");
     $datas = $query->result();
    return $datas;
}
public function GetCompanyAfterAssignSameStatusSinceDays($userid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "user_details.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "user_details.admin_id = $userid";
    }else if($type_id == 3){
        $text = "user_details.user_id = $userid";
    }else if($type_id == 4){
        $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
    }else if($type_id == 15){
        $text = "user_details.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
    }else{
        $text = "user_details.admin_id = $userid";
    }
    if($admin_id_filter =='all'){
        // $text = "user_details.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND user_details.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
               $text = "AND user_details.sadmin_id = '100000'";
        }else{
            $text = "AND user_details.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id ==1){
                    $text = "user_details.sadmin_id = $userid";
                }else if($type_id == 2){
                    $text = "user_details.admin_id = $userid";
                }else if($type_id == 3){
                    $text = "user_details.user_id = $userid";
                }else if($type_id == 4){
                    $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "user_details.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
                }else if($type_id == 20){
                    $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 21){
                    $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 22){
                    $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 23){
                    $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 24){
                    $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
                }else{
                    $text = "user_details.admin_id = $userid";
                }
        }
    }
     $userstatus = "user_details.status = 'active'";
     $cuserstatus = "u1.status = 'active'";
     
     $query = $this->db->query("SELECT *
                FROM (
                    SELECT 
                        init_call.id AS inid,
                        company_master.id AS cid,
                        company_master.compname AS compname,
                        u1.name AS main_bd_name,
                        partner_master.name AS pname,
                        cur_status.name AS current_status,
                        s2.name AS last_task_on_status_name,
                        s3.name AS last_update_status_name,
                        a1.name AS last_task_name,
                        user_details.name AS task_username,
                        main_review.sdate AS main_review_date,
                        main_review.rtype AS main_review_rtype,
                        MAX(DATEDIFF('$edate', tblcallevents.appointmentdatetime)) AS days,
                        GREATEST(
                            DATE(main_review.sdate),
                            DATE(MAX(tblcallevents.updated_at))
                        ) AS review_date,
                        COUNT(
                            CASE 
                                WHEN DATE(tblcallevents.appointmentdatetime) 
                                    BETWEEN '$sdate' AND '$edate' 
                                THEN tblcallevents.id 
                            END
                        ) AS total_log_in_days,
                        ( SELECT tce.appointmentdatetime 
                        FROM tblcallevents tce 
                        WHERE tce.cid_id = init_call.id 
                        ORDER BY tce.id DESC 
                        LIMIT 1 ) AS last_task_update,
                        DATEDIFF(
                            '$edate', 
                            MAX(tblcallevents.appointmentdatetime)
                        ) AS last_task_days,
                        ( SELECT tce.id 
                        FROM tblcallevents tce 
                        WHERE tce.cid_id = init_call.id 
                        ORDER BY tce.id DESC 
                        LIMIT 1 ) AS last_task_id,
                        ( SELECT CASE 
                                WHEN tce.nextCFID = 0 THEN 'Pending' 
                                ELSE 'Complete' 
                                END
                        FROM tblcallevents tce 
                        WHERE tce.cid_id = init_call.id 
                        ORDER BY tce.id DESC 
                        LIMIT 1 ) AS last_task_status
                    FROM init_call
                    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
                    LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
                    LEFT JOIN tblcallevents 
                        ON tblcallevents.id = (
                            SELECT MAX(tce.id) 
                            FROM tblcallevents tce 
                            WHERE tce.cid_id = init_call.id
                        )
                    LEFT JOIN main_review ON main_review.inid = init_call.id
                    LEFT JOIN user_details ON user_details.user_id = tblcallevents.user_id
                    LEFT JOIN status cur_status ON cur_status.id = init_call.cstatus
                    LEFT JOIN status s2 ON s2.id = tblcallevents.status_id
                    LEFT JOIN status s3 ON s3.id = tblcallevents.nstatus_id
                    LEFT JOIN action a1 
                    ON a1.id = (
                        SELECT tce.actiontype_id
                        FROM tblcallevents tce
                        WHERE tce.cid_id = init_call.id
                        ORDER BY tce.id DESC
                        LIMIT 1
                    )
                    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
                    WHERE $text AND $userstatus AND $cuserstatus
                    AND init_call.cstatus != ''
                    AND init_call.cstatus NOT IN (7,14)
                    AND tblcallevents.id IS NOT NULL
                    AND main_review.sdate IS NOT NULL
                    AND user_details.type_id IN(4,13,18,19,20,21,22,23,24)
                    AND NOT EXISTS (
                            SELECT 1 
                            FROM tblcallevents tce 
                            WHERE tce.cid_id = init_call.id 
                            AND DATE(tce.appointmentdatetime) = '$edate'
                        )
                    GROUP BY init_call.id, init_call.cstatus
                ) AS sub
                WHERE sub.days > $days_inclusive
                AND sub.last_task_days > $days_inclusive
                AND last_task_update < '$edate'
                ");
     $datas = $query->result();
    return $datas;
}
   public function GetSpecialRemarksTaskLists($userid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "AND u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "AND u1.admin_id = $userid";
    }else if($type_id == 3){
        $text = "AND u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "AND u1.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "AND u1.ash_nae_co = '$userid'";
    }else if($type_id == 20){
        $text = "AND u1.ash_w_co='$userid'";
    }else if($type_id == 21){
        $text = "AND u1.ash_s_co='$userid'";
    }else if($type_id == 22){
        $text = "AND u1.rm_east_co='$userid'";
    }else if($type_id == 23){
        $text = "AND u1.rm_north_co='$userid'";
    }else if($type_id == 24){
        $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text = "AND u1.admin_id = $userid";
    }
    if($admin_id_filter =='all'){
        // $text = "AND u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "AND u1.sadmin_id = $userid";
                }else if($type_id == 2){
                    $text = "AND u1.admin_id = $userid";
                }else if($type_id == 2){
                    $text = "AND u1.admin_id = $userid";
                }else if($type_id == 3){
                    $text = "AND u1.user_id = $userid";
                }else if($type_id == 4){
                    $text = "AND (u1.pst_co = $userid || u1.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "AND (u1.aadmin = $userid || u1.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "AND u1.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "AND u1.ash_nae_co = '$userid'";
                }else if($type_id == 20){
                    $text = "AND u1.ash_w_co='$userid'";
                }else if($type_id == 21){
                    $text = "AND u1.ash_s_co='$userid'";
                }else if($type_id == 22){
                    $text = "AND u1.rm_east_co='$userid'";
                }else if($type_id == 23){
                    $text = "AND u1.rm_north_co='$userid'";
                }else if($type_id == 24){
                    $text = "AND (u1.acm_co = $userid || u1.user_id = $userid)";
                }else{
                    $text = "AND u1.admin_id = $userid";
                }
        }
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
     if($task_action_id == 'all'){
        $taskactionFilter = "AND tbcl.nextCFID !=0 AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '') AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
    AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }else{
        $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }
     $filter = " AND tbcl.special_remarks IS NOT NULL AND (tbcl.special_remarks !='null' AND tbcl.special_remarks != '[]')";
  
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.proposal_require,
     tbcl.proposal_tid AS calling_proposal_tid,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	$text $filter ");
$data1 =  $query->result(); 
$query1 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    s2.name AS planned_status_name,
    COUNT(CASE WHEN tbcl.nextCFID != 0 THEN tbcl.id END) AS total_task
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      -- current status
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id     -- planned status
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text
    $filter
GROUP BY tbcl.user_id, s2.id");
$userwiseCount =  $query1->result(); 
$query2 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    COUNT(CASE
        -- Positive Conversion conditions
        WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
        WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
        WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
        ELSE NULL
    END) AS positive_conversions,
    COUNT(CASE
        -- Negative Conversion conditions
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
        WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
        ELSE NULL
    END) AS negative_conversions,
    -- Other conversions = total - positive - negative
    (
        COUNT(tbcl.id) -
        COUNT(CASE
            WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
            WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
            WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
            ELSE NULL
        END) -
        COUNT(CASE
            WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
            WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
            ELSE NULL
        END)
    ) AS other_conversions
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      -- current status
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id     -- planned status
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text
    $filter
GROUP BY tbcl.user_id");
$conversions =  $query2->result(); 
  $result = [
    'totalTasksUserByDatas' => $data1,
    'userwiseCount'         => $userwiseCount,
    'conversions'           => $conversions
  ];
    return  $result;
}
   public function GetAdminSpecialRemarksTaskLists($userid,$sdate,$edate,$task_action_id,$admin_id_filter){
    
      if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND user_details.user_id = $userid";
        }else if(in_array($cuid,[100000])){
            $text = "AND user_details.sadmin_id IN (100000) AND user_details.type_id IN (2,1,22,23) AND user_details.status = 'active'";
        }else{
             $text = "AND user_details.sadmin_id = '100000' AND user_details.type_id IN (2,1,22,23) AND user_details.status = 'active'";
        }
    }else{
         $text = "AND user_details.user_id = $userid";
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
     if($task_action_id == 'all'){
        $taskactionFilter = "AND tbcl.nextCFID !=0 AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '') AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
    AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }else{
        $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }
     
  
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.proposal_require,
     tbcl.proposal_tid AS calling_proposal_tid,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	$text ");
$data1 =  $query->result(); 
 if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
   
        if(in_array($cuid,[2,45])){
            $text1 = "AND ud.user_id = $userid";
        }else if(in_array($cuid,[100000])){
            $text1 = "AND ud.sadmin_id IN (100000) AND ud.type_id IN (2,1,22,23) AND ud.status = 'active'";
        }else{
             $text1 = "AND ud.sadmin_id = '100000' AND ud.type_id IN (2,1,22,23) AND ud.status = 'active'";
        }
    }else{
         $text1 = "AND ud.user_id = $userid";
    }
$query1 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    s2.name AS planned_status_name,
    COUNT(CASE WHEN tbcl.nextCFID != 0 THEN tbcl.id END) AS total_task
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id    
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text1
GROUP BY tbcl.user_id, s2.id");
$userwiseCount =  $query1->result(); 
$query2 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    COUNT(CASE
        -- Positive Conversion conditions
        WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
        WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
        WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
        ELSE NULL
    END) AS positive_conversions,
    COUNT(CASE
        -- Negative Conversion conditions
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
        WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
        ELSE NULL
    END) AS negative_conversions,
    -- Other conversions = total - positive - negative
    (
        COUNT(tbcl.id) -
        COUNT(CASE
            WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
            WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
            WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
            ELSE NULL
        END) -
        COUNT(CASE
            WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
            WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
            ELSE NULL
        END)
    ) AS other_conversions
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      -- current status
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id     -- planned status
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text1
GROUP BY tbcl.user_id");
$conversions =  $query2->result(); 
  $result = [
    'totalTasksUserByDatas' => $data1,
    'userwiseCount'         => $userwiseCount,
    'conversions'           => $conversions
  ];
    return  $result;
}
   public function LineManagerCallingonRPLeadsLists($userid,$sdate,$edate,$task_action_id,$admin_id_filter){
    
      if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND user_details.user_id = $userid";
        }else if(in_array($cuid,[100000])){
            $text = "AND user_details.sadmin_id IN (100000) AND user_details.type_id IN (4,13,24,18,19,20,21,22,23) AND user_details.status = 'active'";
        }else{
             $text = "AND user_details.sadmin_id = '100000' AND user_details.type_id IN (4,13,24,18,19,20,21,22,23) AND user_details.status = 'active'";
        }
    }else{
         
            $udetail = $this->Menu_model->get_userbyid($userid);
            $type_id = $udetail[0]->type_id;
                
            
                $text = "AND user_details.sadmin_id = '$userid' AND user_details.type_id IN (4,13,24,18,19,20,21,22,23) AND user_details.status = 'active'";
                $udetail = $this->Menu_model->get_userbyid($userid);
                $type_id = $udetail[0]->type_id;
                $aadmin     = !empty($udetail[0]->aadmin)     && $udetail[0]->aadmin != 0     ? $udetail[0]->aadmin     : null;
                $acm_co     = !empty($udetail[0]->acm_co)     && $udetail[0]->acm_co != 0     ? $udetail[0]->acm_co     : null;
                $ash_nae_co = !empty($udetail[0]->ash_nae_co) && $udetail[0]->ash_nae_co != 0 ? $udetail[0]->ash_nae_co : null;
                $ash_w_co   = !empty($udetail[0]->ash_w_co)   && $udetail[0]->ash_w_co != 0   ? $udetail[0]->ash_w_co   : null;
                $ash_s_co   = !empty($udetail[0]->ash_s_co)   && $udetail[0]->ash_s_co != 0   ? $udetail[0]->ash_s_co   : null;
                $rm_east_co = !empty($udetail[0]->rm_east_co) && $udetail[0]->rm_east_co != 0 ? $udetail[0]->rm_east_co : null;
                $rm_north_co= !empty($udetail[0]->rm_north_co)&& $udetail[0]->rm_north_co != 0? $udetail[0]->rm_north_co: null;
                if($type_id == 1){
                   $text = "AND user_details.sadmin_id = '$userid' AND user_details.type_id IN (4,13,24,18,19,20,21,22,23) AND user_details.status = 'active'";
                }else if($type_id == 2){
                    $text = "AND user_details.admin_id = '$userid' AND user_details.type_id IN (4,13,24,18,19,20,21,22,23) AND user_details.status = 'active'";
                }else if($type_id == 3){
                     $text = "AND user_details.user_id = '$userid' AND user_details.type_id IN (3) AND user_details.status = 'active'";
                }else if($type_id == 4){
                    $text = "AND (user_details.pst_co = '$userid' OR user_details.user_id = '$userid') AND user_details.type_id IN (13,24,4) AND user_details.status = 'active'";
                }else if($type_id == 13){
                    $text = "AND (user_details.aadmin = '$userid' OR user_details.user_id = $userid) AND user_details.type_id IN (3,13,24) AND user_details.status = 'active'";
                }else if($type_id == 15){
                    $text = "AND (user_details.sales_co = '$userid') AND user_details.type_id IN (3,4,13,24,22) AND user_details.status = 'active'";
                }elseif($type_id == 19){
                    $text = "AND (user_details.ash_nae_co = '$userid' OR user_details.user_id = $userid) AND user_details.type_id IN (3,4,13,24,22) AND user_details.status = 'active'";
                }else if($type_id == 20){
                    $text = "AND (user_details.ash_w_co = '$userid' OR user_details.user_id = $userid) AND user_details.type_id IN (3,4,13,24,22) AND user_details.status = 'active'";
                }else if($type_id == 21){
                    $text = "AND (user_details.ash_s_co = '$userid' OR user_details.user_id = $userid) AND user_details.type_id IN (3,4,13,24,22) AND user_details.status = 'active'";
                }else if($type_id == 22){
                    $text = "AND (user_details.rm_east_co = '$userid' OR user_details.user_id = $userid) AND user_details.type_id IN (3,4,13,24,22) AND user_details.status = 'active'";
                }else if($type_id == 23){
                    $text = "AND (user_details.rm_north_co = '$userid' OR user_details.user_id = $userid) AND user_details.type_id IN (3,4,13,24,23) AND user_details.status = 'active'";
                }else if($type_id == 24){
                    $text = "AND (user_details.acm_co = '$userid' OR user_details.user_id = $userid) AND user_details.type_id IN (3,24) AND user_details.status = 'active'";
                }else{
                    $text = "AND user_details.admin_id = $userid";
                }
    }
    // echo $text;
    // die;
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
     if($task_action_id == 'all'){
        $taskactionFilter = "AND tbcl.nextCFID !=0 AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '') AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
    AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }else{
        $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }
     
  
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.proposal_require,
     tbcl.proposal_tid AS calling_proposal_tid,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	 $text
    AND EXISTS (
        SELECT 1 
        FROM tblcallevents sub_tbcl 
        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP')
        AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
        AND sub_tbcl.cid_id = init_call.id
    )
");
$data1 =  $query->result(); 




 if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
   
        if(in_array($cuid,[2,45])){
            $text1 = "AND ud.user_id = $userid";
        }else if(in_array($cuid,[100000])){
            $text1 = "AND ud.sadmin_id IN (100000) AND ud.type_id IN (4,13,24) AND ud.status = 'active'";
        }else{
             $text1 = "AND ud.sadmin_id = '100000' AND ud.type_id IN (4,13,24) AND ud.status = 'active'";
        }
    }else{
        // $text1 = "AND ud.sadmin_id = '$userid' AND ud.type_id IN (4,13,24) AND ud.status = 'active'";
            $udetail = $this->Menu_model->get_userbyid($userid);
            $type_id = $udetail[0]->type_id;
            $aadmin     = !empty($udetail[0]->aadmin)     && $udetail[0]->aadmin != 0  ? $udetail[0]->aadmin     : null;
            $acm_co     = !empty($udetail[0]->acm_co)     && $udetail[0]->acm_co != 0  ? $udetail[0]->acm_co     : null;
            $ash_nae_co = !empty($udetail[0]->ash_nae_co) && $udetail[0]->ash_nae_co != 0 ? $udetail[0]->ash_nae_co : null;
            $ash_w_co   = !empty($udetail[0]->ash_w_co)   && $udetail[0]->ash_w_co != 0   ? $udetail[0]->ash_w_co   : null;
            $ash_s_co   = !empty($udetail[0]->ash_s_co)   && $udetail[0]->ash_s_co != 0   ? $udetail[0]->ash_s_co   : null;
            $rm_east_co = !empty($udetail[0]->rm_east_co) && $udetail[0]->rm_east_co != 0 ? $udetail[0]->rm_east_co : null;
            $rm_north_co= !empty($udetail[0]->rm_north_co)&& $udetail[0]->rm_north_co != 0? $udetail[0]->rm_north_co: null;
            $otherFilter = '';
                
                if($type_id == 1){
                   $text1 = "AND ud.sadmin_id = '$userid' AND ud.type_id IN (4,13,24,18,19,20,21,22,23) AND ud.status = 'active'";
                }else if($type_id == 2){
                    $text1 = "AND ud.admin_id = '$userid' AND ud.type_id IN (4,13,24,18,19,20,21,22,23) AND ud.status = 'active'";
                }else if($type_id == 3){
                    $text1 = "AND (ud.user_id = $userid) AND ud.status = 'active'";
                     $otherFilter = "AND (
                        ic.mainbd = '$userid' 
                        OR (ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co')
                    )";
                }else if($type_id == 4){
                    $text1 = "AND (ud.pst_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,13,24) AND ud.status = 'active'";
                }else if($type_id == 13){
                    $text1 = "AND (ud.aadmin = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,13,24) AND ud.status = 'active'";
                }else if($type_id == 15){
                    $text1 = "AND (ud.sales_co = '$userid') AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                }elseif($type_id == 19){
                    $text1 = "AND (ud.ash_nae_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                }else if($type_id == 20){
                    $text1 = "AND (ud.ash_w_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                }else if($type_id == 21){
                    $text1 = "AND (ud.ash_s_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                }else if($type_id == 22){
                    $text1 = "AND (ud.rm_east_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                }else if($type_id == 23){
                    $text1 = "AND (ud.rm_north_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,23) AND ud.status = 'active'";
                }else if($type_id == 24){
                    $text1 = "AND (ud.acm_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,24) AND ud.status = 'active'";
                }else{
                    $text1 = "AND u1.admin_id = $userid";
                }
    }
$query1 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    s2.name AS planned_status_name,
    COUNT(CASE WHEN tbcl.nextCFID != 0 THEN tbcl.id END) AS total_task,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND ((tbcl.actontaken = 'yes' OR tbcl.actontaken = 'Yes') AND purpose_achieved = 'yes') THEN tbcl.id END) AS ay_py,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND ((tbcl.actontaken = 'yes' OR tbcl.actontaken = 'Yes') AND purpose_achieved = 'no') THEN tbcl.id END) AS ay_pn,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND (tbcl.actontaken = 'no' AND purpose_achieved = 'no') THEN tbcl.id END) AS an_pn,
    COUNT(CASE WHEN tbcl.nextCFID != 0 AND (tbcl.actontaken = 'no' AND purpose_achieved = 'yes') THEN tbcl.id END) AS an_py,
    COUNT(DISTINCT CASE WHEN tbcl.nextCFID != 0 THEN tbcl.cid_id END) AS total_call_on_unique,
    COUNT(DISTINCT CASE WHEN tbcl.nextCFID != 0 AND ((tbcl.actontaken = 'yes' OR tbcl.actontaken = 'Yes') AND purpose_achieved = 'yes') THEN tbcl.cid_id END) AS total_ay_py_call_on_unique

FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id    
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text1
    $otherFilter
     AND EXISTS (
        SELECT 1 
        FROM tblcallevents sub_tbcl 
        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP')
        -- AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
        AND CAST(sub_tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
        AND sub_tbcl.cid_id = ic.id
    )
GROUP BY tbcl.user_id, s2.id");
$userwiseCount =  $query1->result(); 


$query2 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    COUNT(CASE
        -- Positive Conversion conditions
        WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
        WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
        WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
        ELSE NULL
    END) AS positive_conversions,
    COUNT(CASE
        -- Negative Conversion conditions
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
        WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
        ELSE NULL
    END) AS negative_conversions,
    -- Other conversions = total - positive - negative
    (
        COUNT(tbcl.id) -
        COUNT(CASE
            WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
            WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
            WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
            ELSE NULL
        END) -
        COUNT(CASE
            WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
            WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
            ELSE NULL
        END)
    ) AS other_conversions
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      -- current status
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id     -- planned status
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text1
     AND EXISTS (
        SELECT 1 
        FROM tblcallevents sub_tbcl 
        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP') 
        AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
        AND sub_tbcl.cid_id = ic.id
    )
GROUP BY tbcl.user_id");
$conversions =  $query2->result(); 
  $result = [
    'totalTasksUserByDatas' => $data1,
    'userwiseCount'         => $userwiseCount,
    'conversions'           => $conversions
  ];
    return  $result;
}
   public function LineManagerCallingonRPLeadsListsonBDCM($userid,$sdate,$edate,$task_action_id,$admin_id_filter){
    
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
     if($task_action_id == 'all'){
        $taskactionFilter = "AND tbcl.nextCFID !=0 AND (tbcl.actiontype_id != 0 OR tbcl.actiontype_id != '') AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
    AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }else{
        $taskactionFilter = "AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    }
     
 if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
   
        if(in_array($cuid,[2,45])){
            $text1 = "AND ud.user_id = $userid";
        }else if(in_array($cuid,[100000])){
            $text1 = "AND ud.sadmin_id IN (100000) AND ud.type_id IN (4,13,24) AND ud.status = 'active'";
        }else{
             $text1 = "AND ud.sadmin_id = '100000' AND ud.type_id IN (4,13,24) AND ud.status = 'active'";
        }
    }else{
        // $text1 = "AND ud.sadmin_id = '$userid' AND ud.type_id IN (4,13,24) AND ud.status = 'active'";
            $udetail = $this->Menu_model->get_userbyid($userid);
            $type_id = $udetail[0]->type_id;
            $aadmin = $udetail[0]->aadmin;
            $rm_east_co = $udetail[0]->rm_east_co;
            $rm_north_co = $udetail[0]->rm_north_co;
            $acm_co = $udetail[0]->acm_co;
            $ash_nae_co = $udetail[0]->ash_nae_co;
            $ash_w_co = $udetail[0]->ash_w_co;
            $ash_s_co = $udetail[0]->ash_s_co;
            $otherFilter = '';
            $text11 = '';
                
                if($type_id == 1){
                   $text1 = "AND ud.sadmin_id = '$userid' AND ud.type_id IN (4,13,24,18,19,20,21,22,23) AND ud.status = 'active'";
                }else if($type_id == 2){
                    $text1 = "AND ud.admin_id = '$userid' AND ud.type_id IN (4,13,24,18,19,20,21,22,23) AND ud.status = 'active'";
                }else if($type_id == 3){
                    $values = [
                        // $udetail[0]->user_id,
                        $udetail[0]->aadmin,
                        $udetail[0]->rm_east_co,
                        $udetail[0]->rm_north_co,
                        $udetail[0]->acm_co,
                        $udetail[0]->ash_nae_co,
                        $udetail[0]->ash_w_co,
                        $udetail[0]->ash_s_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser = implode(",", $filtered);
                    $text11          = "AND (u1.user_id = '$userid') AND u1.status = 'active'";
                    $text1 = "AND (ud.user_id IN ($selectedUser)) AND ud.status = 'active'";
                     $otherFilter = " AND  (
                        -- ic.mainbd = '$userid' 
                        ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co'
                    )";
                }else if($type_id == 4){
                    // $text1 = "AND (ud.pst_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,13,24) AND ud.status = 'active'";
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($userid);
                    $user_ids = array_map(function($item) {
                        return $item->user_id;
                    }, $teamsDatas);
                    $team_user_ids =  implode(",", $user_ids);
                    
                     $values = [
                        $udetail[0]->user_id,
                        // $udetail[0]->aadmin,
                        $udetail[0]->rm_east_co,
                        $udetail[0]->rm_north_co,
                        // $udetail[0]->acm_co,
                        $udetail[0]->ash_nae_co,
                        $udetail[0]->ash_w_co,
                        $udetail[0]->ash_s_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser = implode(",", $filtered);
                    $text11          = "AND (u1.user_id IN ($team_user_ids)) AND u1.status = 'active'";
                    $text1 = "AND (ud.user_id IN ($selectedUser)) AND ud.status = 'active'";
                     $otherFilter = " AND  (
                        -- ic.mainbd = '$userid' 
                        ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co'
                    )";
                }else if($type_id == 13){
                    // $text1 = "AND (ud.aadmin = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,13,24) AND ud.status = 'active'";
                   $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($userid);
                   $user_ids = array_map(
                        function($item) {
                            return $item->user_id;
                        }, 
                        array_filter($teamsDatas, function($item) {
                            return in_array($item->type_id, [13,4,18,19,20,21,22,23]);
                        })
                    );
                    $team_user_ids =  implode(",", $user_ids);
                     $values = [
                        $udetail[0]->user_id,
                        // $udetail[0]->aadmin,
                        $udetail[0]->rm_east_co,
                        $udetail[0]->rm_north_co,
                        // $udetail[0]->acm_co,
                        $udetail[0]->ash_nae_co,
                        $udetail[0]->ash_w_co,
                        $udetail[0]->ash_s_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser = implode(",", $filtered);
                    $text11          = "AND (u1.user_id IN ($team_user_ids)) AND u1.status = 'active'";
                    $text1 = "AND (ud.user_id IN ($selectedUser)) AND ud.status = 'active'";
                     $otherFilter = " AND  (
                        -- ic.mainbd = '$userid' 
                        ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co'
                    )";
                }else if($type_id == 15){
                    // $text1 = "AND (ud.sales_co = '$userid') AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                   $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($userid);
                   $user_ids = array_map(
                        function($item) {
                            return $item->user_id;
                        }, 
                        array_filter($teamsDatas, function($item) {
                            return in_array($item->type_id, [13,4,18,19,20,21,22,23]);
                        })
                    );
                    $team_user_ids =  implode(",", $user_ids);
                     $values = [
                        $udetail[0]->user_id,
                        // $udetail[0]->aadmin,
                        $udetail[0]->rm_east_co,
                        $udetail[0]->rm_north_co,
                        // $udetail[0]->acm_co,
                        $udetail[0]->ash_nae_co,
                        $udetail[0]->ash_w_co,
                        $udetail[0]->ash_s_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser = implode(",", $filtered);
                    $text11          = "AND (u1.user_id IN ($team_user_ids)) AND u1.status = 'active'";
                    $text1 = "AND (ud.user_id IN ($selectedUser)) AND ud.status = 'active'";
                     $otherFilter = " AND  (
                        -- ic.mainbd = '$userid' 
                        ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co'
                    )";
                }elseif($type_id == 19){
                    // $text1 = "AND (ud.ash_nae_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($userid);
                   $user_ids = array_map(
                        function($item) {
                            return $item->user_id;
                        }, 
                        array_filter($teamsDatas, function($item) {
                            return in_array($item->type_id, [13,4,18,19,20,21,22,23]);
                        })
                    );
                    $team_user_ids =  implode(",", $user_ids);
                     $values = [
                        $udetail[0]->user_id,
                        // $udetail[0]->aadmin,
                        $udetail[0]->rm_east_co,
                        $udetail[0]->rm_north_co,
                        // $udetail[0]->acm_co,
                        // $udetail[0]->ash_nae_co,
                        $udetail[0]->ash_w_co,
                        $udetail[0]->ash_s_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser = implode(",", $filtered);
                    $text11          = "AND (u1.user_id IN ($team_user_ids)) AND u1.status = 'active'";
                    $text1 = "AND (ud.user_id IN ($selectedUser)) AND ud.status = 'active'";
                     $otherFilter = " AND  (
                        -- ic.mainbd = '$userid' 
                        ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co'
                    )";
                }else if($type_id == 20){
                    // $text1 = "AND (ud.ash_w_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($userid);
                   $user_ids = array_map(
                        function($item) {
                            return $item->user_id;
                        }, 
                        array_filter($teamsDatas, function($item) {
                            return in_array($item->type_id, [13,4,18,19,20,21,22,23]);
                        })
                    );
                    $team_user_ids =  implode(",", $user_ids);
                     $values = [
                        // $udetail[0]->user_id,
                        $udetail[0]->aadmin,
                        $udetail[0]->rm_east_co,
                        $udetail[0]->rm_north_co,
                        $udetail[0]->acm_co,
                        $udetail[0]->ash_nae_co,
                        // $udetail[0]->ash_w_co,
                        $udetail[0]->ash_s_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser = implode(",", $filtered);
                    $text11          = "AND (u1.user_id IN ($team_user_ids)) AND u1.status = 'active'";
                    $text1 = "AND (ud.user_id IN ($selectedUser)) AND ud.status = 'active'";
                     $otherFilter = " AND  (
                        -- ic.mainbd = '$userid' 
                        ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co'
                    )";
                }else if($type_id == 21){
                    $text1 = "AND (ud.ash_s_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                }else if($type_id == 22){
                    $text1 = "AND (ud.rm_east_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,22) AND ud.status = 'active'";
                }else if($type_id == 23){
                    $text1 = "AND (ud.rm_north_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,4,13,24,23) AND ud.status = 'active'";
                }else if($type_id == 24){
                    
                    // $text1 = "AND (ud.acm_co = '$userid' OR ud.user_id = $userid) AND ud.type_id IN (3,24) AND ud.status = 'active'";
                     $values = [
                        // $udetail[0]->user_id,
                        $udetail[0]->aadmin,
                        $udetail[0]->rm_east_co,
                        $udetail[0]->rm_north_co,
                        // $udetail[0]->acm_co,
                        $udetail[0]->ash_nae_co,
                        $udetail[0]->ash_w_co,
                        $udetail[0]->ash_s_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser = implode(",", $filtered);
                    $text11          = "AND (u1.user_id IN ($userid)) AND u1.status = 'active'";
                    $text1 = "AND (ud.user_id IN ($selectedUser)) AND ud.status = 'active'";
                     $otherFilter = " AND  (
                        -- ic.mainbd = '$userid' 
                        ic.clm_id = '$aadmin' 
                        OR ic.ash_nae_co_id = '$ash_nae_co' 
                        OR ic.ash_w_co_id = '$ash_w_co' 
                        OR ic.ash_s_co_id = '$ash_s_co' 
                        OR ic.rm_east_co_id = '$rm_east_co' 
                        OR ic.rm_north_co_id = '$rm_north_co' 
                        OR ic.acm_co_id = '$acm_co'
                    )";
                }else{
                    $text1 = "AND u1.admin_id = $userid";
                }
    }
$query1 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    s2.name AS planned_status_name,
    COUNT(CASE WHEN tbcl.nextCFID != 0 THEN tbcl.id END) AS total_task
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id    
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text1
    $text11
    $otherFilter
     AND EXISTS (
        SELECT 1 
        FROM tblcallevents sub_tbcl 
        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP')
        AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
        AND sub_tbcl.cid_id = ic.id
    )
GROUP BY tbcl.user_id, s2.id");
$userwiseCount =  $query1->result(); 
$query2 = $this->db->query("SELECT
    ud.user_id AS task_user_id,
    ud.name AS user_name,
    COUNT(CASE
        -- Positive Conversion conditions
        WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
        WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
        WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
        ELSE NULL
    END) AS positive_conversions,
    COUNT(CASE
        -- Negative Conversion conditions
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
        WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
        WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
        ELSE NULL
    END) AS negative_conversions,
    -- Other conversions = total - positive - negative
    (
        COUNT(tbcl.id) -
        COUNT(CASE
            WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Will do Later', 'Closure')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN 1
            WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN 1
            WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN 1
            ELSE NULL
        END) -
        COUNT(CASE
            WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN 1
            WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN 1
            WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN 1
            WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN 1
            WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN 1
            WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN 1
            WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN 1
            WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN 1
            ELSE NULL
        END)
    ) AS other_conversions
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ic ON ic.id = tbcl.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN partner_master pm ON pm.id = cm.partnerType_id
LEFT JOIN user_details ud ON ud.user_id = tbcl.user_id
LEFT JOIN action a ON a.id = tbcl.actiontype_id
LEFT JOIN status st ON st.id = ic.cstatus
LEFT JOIN status s1 ON s1.id = tbcl.status_id      -- current status
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id     -- planned status
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
WHERE
    CAST(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
    $text1
     AND EXISTS (
        SELECT 1 
        FROM tblcallevents sub_tbcl 
        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP') 
        AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
        AND sub_tbcl.cid_id = ic.id
    )
GROUP BY tbcl.user_id");
$conversions =  $query2->result(); 
  $result = [
    'userwiseCount'         => $userwiseCount,
    'conversions'           => $conversions
  ];
    return  $result;
}
   public function DayManagementStarRatingLists($userid,$sdate,$edate,$admin_id_filter,$rm_filter){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   
  
        $query= $this->db->query("SELECT
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
            AND u1.status = 'active'
            AND  CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id");
        $data1 =  $query->result(); 
        $query1= $this->db->query("SELECT
                ssr.date as day_date,
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id, ssr.date");
        $data2 =  $query1->result(); 
  $result = [
    'totalStarsDatas' => $data1,
    'totalStarsDatasByDate' => $data2
  ];
    return  $result;
}
   public function TaskManagementStarRatingLists($userid,$sdate,$edate,$admin_id_filter,$rm_filter){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   
  
        $query= $this->db->query("SELECT
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
            AND u1.status = 'active'
            AND  CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id");
        $data1 =  $query->result(); 
        $query1= $this->db->query("SELECT
                ssr.date as day_date,
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id, ssr.date");
        $data2 =  $query1->result(); 
        $query3= $this->db->query("SELECT
                ssr.date as day_date,
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name,
            action.name as task_name
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN tblcallevents on tblcallevents.id = ssr.task_id
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            
            WHERE
             $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id, ssr.date,action.id");
        $data3 =  $query3->result(); 
  $result = [
    'totalStarsDatas' => $data1,
    'totalStarsDatasByDate' => $data2,
    'totalStarsDatasByDateWithTaskAction' => $data3
  ];
    return  $result;
}
   public function DayManagementStarRatingListsDetails($userid,$sdate,$edate,$approved_by){
      $text = " u1.user_id = '$userid'";
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   
  
        $query= $this->db->query("SELECT
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id");
        $data1 =  $query->result(); 
        $query1= $this->db->query("SELECT
                ssr.date as day_date,
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
             AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id, ssr.date");
        $data2 =  $query1->result(); 
        if($approved_by == 'approved_by'){
            $addcolumn = ",ssr.remarks,u2.name as star_given_by";
            $leftjoin = 'LEFT JOIN user_details u2 ON u2.user_id = ssr.star_by';
        }else{
            $addcolumn = '';
             $leftjoin = '';
        }
        
        $query2= $this->db->query("SELECT
            ssr.question,
            SUM(ssr.star) AS total_stars
            $addcolumn
        FROM
            sales_star_rating ssr
        LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
        $leftjoin
        LEFT JOIN user_type ON user_type.id = u1.type_id
        WHERE
           $text 
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'  
        GROUP BY
        ssr.question ORDER BY total_stars DESC ");
        $data3 =  $query2->result(); 
  $result = [
    'totalStarsDatas' => $data1,
    'totalStarsDatasByDate' => $data2,
    'totalStarsDatasQuestionByDate' => $data3
  ];
    return  $result;
}
   public function TaskCheckingManagementStarRatingDetailsLists($userid,$sdate,$edate,$approved_by){
    $text = " u1.user_id = '$userid'";
    
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   
  
        $query= $this->db->query("SELECT
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
            AND u1.status = 'active'
            AND  CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id");
        $data1 =  $query->result(); 
        $query1= $this->db->query("SELECT
                ssr.date as day_date,
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            WHERE
            $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id, ssr.date");
        $data2 =  $query1->result(); 
        $query3= $this->db->query("SELECT
                ssr.date as day_date,
                u1.user_id AS day_user_id,
                u1.name,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name,
            action.name as task_name
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN tblcallevents on tblcallevents.id = ssr.task_id
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            WHERE
             $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY u1.user_id, ssr.date,action.id");
        $data3 =  $query3->result(); 
        if($approved_by == 'approved_by'){
            $addcolumn = ",ssr.remarks,u2.name as star_given_by";
            $leftjoin = 'LEFT JOIN user_details u2 ON u2.user_id = ssr.star_by';
        }else{
            $addcolumn = '';
             $leftjoin = '';
        }
        
        $query4 = $this->db->query("SELECT
            ssr.question,
            SUM(ssr.star) AS total_stars
            $addcolumn
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN tblcallevents on tblcallevents.id = ssr.task_id
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            LEFT JOIN init_call on init_call.id = tblcallevents.cid_id
            LEFT JOIN company_master on company_master.id = init_call.cmpid_id
            LEFT JOIN status s1 on s1.id = tblcallevents.status_id
            LEFT JOIN status s2 on s2.id = tblcallevents.nstatus_id
            LEFT JOIN status s3 on s3.id = init_call.cstatus
            $leftjoin
            WHERE
             $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY
        ssr.question ORDER BY total_stars DESC");
        $data4 =  $query4->result(); 


        $query5 = $this->db->query("SELECT
            tblcallevents.id as task_id,
            company_master.id as cid,
            company_master.compname,
            action.name as task_name,
            tblcallevents.mtype,
            tblcallevents.actontaken,
            tblcallevents.purpose_achieved,
            tblcallevents.purpose_achieved,
            tblcallevents.remarks as task_remarks,
            tblcallevents.nextCFID,
            tblcallevents.delete_request,
            tblcallevents.delete_remarks,
            tblcallevents.actiontype_id,
            SUM(ssr.star) AS total_stars
            
            $addcolumn
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN tblcallevents on tblcallevents.id = ssr.task_id
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            LEFT JOIN init_call on init_call.id = tblcallevents.cid_id
            LEFT JOIN company_master on company_master.id = init_call.cmpid_id
            LEFT JOIN status s1 on s1.id = tblcallevents.status_id
            LEFT JOIN status s2 on s2.id = tblcallevents.nstatus_id
            LEFT JOIN status s3 on s3.id = init_call.cstatus
            $leftjoin
            WHERE
             $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY company_master.id,action.id");
        $data5 =  $query5->result(); 
        $query6 = $this->db->query("SELECT
            ssr.question,
            SUM(ssr.star) AS total_stars,
            user_type.name AS roles_name,
            action.name as task_name,
            company_master.id as cid,
            company_master.compname,
            s1.name as planned_status,
            s2.name as update_new_status,
            s3.name as current_status
            $addcolumn
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN tblcallevents on tblcallevents.id = ssr.task_id
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            LEFT JOIN init_call on init_call.id = tblcallevents.cid_id
            LEFT JOIN company_master on company_master.id = init_call.cmpid_id
            LEFT JOIN status s1 on s1.id = tblcallevents.status_id
            LEFT JOIN status s2 on s2.id = tblcallevents.nstatus_id
            LEFT JOIN status s3 on s3.id = init_call.cstatus
            $leftjoin
            WHERE
             $text
            AND u1.status = 'active'
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY company_master.id");
        $data6 =  $query6->result(); 
  $result = [
    'totalStarsDatas' => $data1,
    'totalStarsDatasByDate' => $data2,
    'totalStarsDatasByDateWithTaskAction' => $data3,
    'taskAddedStarLists' => $data4,
    'taskAddedStarListsCID' => $data5,
  ];
    return  $result;
}




public function GetTaskCheckComplete($uid,$sdate,$edate){

       $query = $this->db->query("SELECT
            tblcallevents.id as task_id,
            company_master.id as cid,
            company_master.compname,
            action.name as task_name,
            tblcallevents.mtype,
            tblcallevents.actontaken,
            tblcallevents.purpose_achieved,
            tblcallevents.purpose_achieved,
            tblcallevents.remarks as task_remarks,
            tblcallevents.nextCFID,
            tblcallevents.delete_request,
            tblcallevents.delete_remarks,
            tblcallevents.actiontype_id,
            SUM(ssr.star) AS total_stars,
            ssr.remarks,
            u2.name as star_given_by
        
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN tblcallevents on tblcallevents.id = ssr.task_id
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            LEFT JOIN init_call on init_call.id = tblcallevents.cid_id
            LEFT JOIN company_master on company_master.id = init_call.cmpid_id
            LEFT JOIN status s1 on s1.id = tblcallevents.status_id
            LEFT JOIN status s2 on s2.id = tblcallevents.nstatus_id
            LEFT JOIN status s3 on s3.id = init_call.cstatus
            LEFT JOIN user_details u2 ON u2.user_id = ssr.star_by
            WHERE
             ssr.star_by = '$uid' 
            AND CAST(ssr.date AS DATE) BETWEEN '$sdate' AND '$edate'
            GROUP BY company_master.id,tblcallevents.id");
 

        return  $query->result(); 

}






   public function TaskCheckingManagementStarRatingSingleDetailsLists($task_id,$sdate,$edate){
    
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
      
    $addcolumn = ",ssr.remarks,u2.name as star_given_by";
    $leftjoin = 'LEFT JOIN user_details u2 ON u2.user_id = ssr.star_by';
       
        
        $query5 = $this->db->query("SELECT
            u1.user_id as task_user_id,
            u1.name as task_user_name,
            tblcallevents.id as task_id,
            company_master.id as cid,
             ssr.question,
            company_master.compname,
            action.name as task_name,
            tblcallevents.mtype,
            tblcallevents.actontaken,
            tblcallevents.purpose_achieved,
            tblcallevents.purpose_achieved,
            tblcallevents.remarks as task_remarks,
            tblcallevents.appointmentdatetime,
            tblcallevents.initiateddt,
            tblcallevents.updated_at,
            tblcallevents.updateddate,
            tblcallevents.nextCFID,
            tblcallevents.actiontype_id,
            tblcallevents.mom,
            s1.name as planned_status,
            s2.name as update_new_status,
            s3.name as current_status,
            
            ssr.star
            
            $addcolumn
            FROM
            sales_task_star_rating ssr
            LEFT JOIN user_details u1 ON u1.user_id = ssr.user_id
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN tblcallevents on tblcallevents.id = ssr.task_id
            LEFT JOIN action on action.id = tblcallevents.actiontype_id
            LEFT JOIN init_call on init_call.id = tblcallevents.cid_id
            LEFT JOIN company_master on company_master.id = init_call.cmpid_id
            LEFT JOIN status s1 on s1.id = tblcallevents.status_id
            LEFT JOIN status s2 on s2.id = tblcallevents.nstatus_id
            LEFT JOIN status s3 on s3.id = init_call.cstatus
            $leftjoin
            WHERE
             ssr.task_id =  $task_id ");
        $data5 =  $query5->result(); 
  $result = [
    'taskAddedStarListsCID' => $data5,
  ];
    return  $result;
}
   public function GetSpecialRemarksTaskListsByUser($userid,$sdate,$edate,$task_action_id){
    $text = "AND tbcl.user_id = $userid";
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   $taskactionFilter = "AND tbcl.nextCFID !=0 AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    $filter = " AND tbcl.special_remarks IS NOT NULL AND (tbcl.special_remarks !='null' AND tbcl.special_remarks != '[]')";
  
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.proposal_require,
     tbcl.proposal_tid AS calling_proposal_tid,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	$text $filter ");
$data1 =  $query->result(); 
  $result = [
    'totalTasksUserByDatas' => $data1
  ];
    return  $result;
}
   public function LineManagerCallingRemarksonRPLeadsLists($userid,$sdate,$edate,$task_action_id){
    $text = "AND tbcl.user_id = $userid";
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   $taskactionFilter = "AND tbcl.nextCFID !=0 AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.proposal_require,
     tbcl.proposal_tid AS calling_proposal_tid,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	$text 
    AND EXISTS (
        SELECT 1 
        FROM tblcallevents sub_tbcl 
        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP')
        -- AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
        AND CAST(sub_tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
        AND sub_tbcl.cid_id = init_call.id
    )
    ");
$data1 =  $query->result(); 
  $result = [
    'totalTasksUserByDatas' => $data1
  ];
    return  $result;
}
   public function LineManagerCallingRemarksonRPLeadsBDLists($userid,$sdate,$edate,$task_action_id,$uid){
            $udetail        = $this->Menu_model->get_userbyid($uid);
            $type_id        = $udetail[0]->type_id;
            $aadmin         = $udetail[0]->aadmin;
            $rm_east_co     = $udetail[0]->rm_east_co;
            $rm_north_co    = $udetail[0]->rm_north_co;
            $acm_co         = $udetail[0]->acm_co;
            $ash_nae_co     = $udetail[0]->ash_nae_co;
            $ash_w_co       = $udetail[0]->ash_w_co;
            $ash_s_co       = $udetail[0]->ash_s_co;
            $otherFilter    = '';
            $text1          = '';
                
            if(in_array($type_id,[3,24,13,4])){
                $udetailCheck        = $this->Menu_model->get_userbyid($userid);
                $type_id_check        = $udetailCheck[0]->type_id;
                if($type_id_check ==13){
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($uid);
                        $values = array_map(function($item) {
                            return $item->user_id;
                        }, $teamsDatas);
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser   = implode(",", $filtered);
                    $text           = "AND (user_details.user_id IN ($userid)) AND user_details.status = 'active'";
                    $text1          = "AND (u1.user_id IN ($selectedUser)) AND u1.status = 'active'";
                }
                  if($type_id_check ==4){
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($uid);
                        $values = array_map(function($item) {
                            return $item->user_id;
                        }, $teamsDatas);
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser   = implode(",", $filtered);
                    $text           = "AND (user_details.user_id IN ($userid)) AND user_details.status = 'active'";
                    $text1          = "AND (u1.user_id IN ($selectedUser)) AND u1.status = 'active'";
                }
                else if($type_id_check ==24){
                    $values = [
                        $udetail[0]->acm_co
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser   = implode(",", $filtered);
                    $text           = "AND (user_details.user_id IN ($selectedUser)) AND user_details.status = 'active'";
                    $text1          = "AND (u1.user_id = '$uid') AND u1.status = 'active'";
                }
                else if($type_id_check ==22){
                    // $values = [
                    //     $udetail[0]->rm_east_co
                    // ];
                $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($uid);
                    $values = array_map(function($item) {
                    return $item->user_id;
                }, $teamsDatas);
                $filtered = array_filter($values, function($val) {
                    return !empty($val) && $val != 0;
                });
                $selectedUser   = implode(",", $filtered);
                $text           = "AND (user_details.user_id IN ($userid)) AND user_details.status = 'active'";
                $text1          = "AND (u1.user_id IN ($selectedUser)) AND u1.status = 'active'";
                }
                else if($type_id_check ==23){
                    
                    // $values = [
                    //     $udetail[0]->rm_north_co
                    // ];
                  
                $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($uid);
                $values = array_map(function($item) {
                    return $item->user_id;
                }, $teamsDatas);
                $filtered = array_filter($values, function($val) {
                    return !empty($val) && $val != 0;
                });
                $selectedUser   = implode(",", $filtered);
                $text           = "AND (user_details.user_id IN ($userid)) AND user_details.status = 'active'";
                $text1          = "AND (u1.user_id IN ($selectedUser)) AND u1.status = 'active'";
                }
                else if($type_id_check ==19){
                    // $values = [
                    //     $udetail[0]->ash_nae_co
                    // ];
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($uid);
                    $values = array_map(function($item) {
                        return $item->user_id;
                    }, $teamsDatas);
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser   = implode(",", $filtered);
                    $text           = "AND (user_details.user_id IN ($userid)) AND user_details.status = 'active'";
                    $text1          = "AND (u1.user_id IN ($selectedUser)) AND u1.status = 'active'";
                }
                else if($type_id_check ==20){
                    // $values = [
                    //     $udetail[0]->ash_w_co
                    // ];
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($uid);
                    $values = array_map(function($item) {
                        return $item->user_id;
                    }, $teamsDatas);
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser   = implode(",", $filtered);
                    $text           = "AND (user_details.user_id IN ($userid)) AND user_details.status = 'active'";
                    $text1          = "AND (u1.user_id IN ($selectedUser)) AND u1.status = 'active'";
                }
                else if($type_id_check ==21){
                    // $values = [
                    //     $udetail[0]->ash_s_co
                    // ];
                    $teamsDatas = $this->Menu_model->getTotalActiveTeamDetailsUid($uid);
                    $values = array_map(function($item) {
                        return $item->user_id;
                    }, $teamsDatas);
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser   = implode(",", $filtered);
                    $text           = "AND (user_details.user_id IN ($userid)) AND user_details.status = 'active'";
                    $text1          = "AND (u1.user_id IN ($selectedUser)) AND u1.status = 'active'";
                }else{
                    $values = [
                        $udetail[0]->user_id
                    ];
                    $filtered = array_filter($values, function($val) {
                        return !empty($val) && $val != 0;
                    });
                    $selectedUser   = implode(",", $filtered);
                    $text           = "AND (user_details.user_id IN ($selectedUser)) AND user_details.status = 'active'";
                    $text1          = "AND (u1.user_id = '$uid') AND u1.status = 'active'";
                }
                // dd($values);
                // $values = [
                //     // $udetail[0]->user_id,
                //     $udetail[0]->aadmin,
                //     $udetail[0]->rm_east_co,
                //     $udetail[0]->rm_north_co,
                //     $udetail[0]->acm_co,
                //     $udetail[0]->ash_nae_co,
                //     $udetail[0]->ash_w_co,
                //     $udetail[0]->ash_s_co
                // ];
            
                
                // $udetailCheck        = $this->Menu_model->get_userbyid($userid);
                // $type_id_check        = $udetailCheck[0]->type_id;
                // if($type_id_check ==13){
                //     $otherFilter = " AND  (
                //         init_call.clm_id = '$aadmin'
                //     )";
                // }
                // else if($type_id_check ==24){
                //     $otherFilter = " AND  (
                //         init_call.acm_co_id = '$aadmin'
                //     )";
                // }
                 $otherFilter = " AND  (
                        -- init_call.mainbd = '$userid' 
                        init_call.clm_id = '$aadmin' 
                        OR init_call.ash_nae_co_id = '$ash_nae_co' 
                        OR init_call.ash_w_co_id = '$ash_w_co' 
                        OR init_call.ash_s_co_id = '$ash_s_co' 
                        OR init_call.rm_east_co_id = '$rm_east_co' 
                        OR init_call.rm_north_co_id = '$rm_north_co' 
                        OR init_call.acm_co_id = '$acm_co'
                    )";
            }else{
                $text = "AND tbcl.user_id = $userid";
            }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   $taskactionFilter = "AND tbcl.nextCFID !=0 AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.proposal_require,
     tbcl.proposal_tid AS calling_proposal_tid,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	$text 
    $text1 
    $otherFilter
    AND EXISTS (
        SELECT 1 
        FROM tblcallevents sub_tbcl 
        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP')
        AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
        AND sub_tbcl.cid_id = init_call.id
    )
    ");
$data1 =  $query->result(); 
  $result = [
    'totalTasksUserByDatas' => $data1
  ];
    return  $result;
}
   public function GetAdminTaskListsByUser($userid,$sdate,$edate,$task_action_id){
    $text = "AND tbcl.user_id = $userid";
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
   $taskactionFilter = "AND tbcl.nextCFID !=0 AND tbcl.actiontype_id = $task_action_id AND ((tbcl.approved_status = 1 || tbcl.approved_status = '')
        AND (tbcl.delete_request = '' OR tbcl.delete_request IS NULL))";
    $query= $this->db->query("SELECT
     user_details.user_id as task_user_id,
     user_details.name as task_username,
     company_master.compname,
     company_master.id as cid,
     tbcl.id as task_id,
     tbcl.nextCFID,
     tbcl.fwd_date,
     tbcl.appointmentdatetime,
     tbcl.initiateddt,
     tbcl.updated_at,
    CONCAT(
        FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
        FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
        MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
    ) AS total_time_taken,
     tbcl.late_remarks_message,
     tbcl.actontaken,
     tbcl.purpose_achieved,
     tbcl.proposal_require,
     tbcl.proposal_tid AS calling_proposal_tid,
     tbcl.meeting_type,
     tbcl.actiontype_id,
     tbcl.assignedto_id,
     tbcl.cid_id,
     tbcl.remarks,
     tbcl.mom as mom_remarks,
     tbcl.mtype,
     tbcl.selectby,
     tbcl.special_remarks,
     tbcl.closing_timeline,
     tbcl.filter_by,
     tbcl.approved_status as task_approved_status,
     tbcl.approved_by as task_approved_by,
     tbcl.assignedto_by as task_assignedto_by,
     tbcl.assignedto_by as task_aftertask,
     action.name as task_name,
     status.name as current_status,
     tbcl.plan_count as plan_count,
     init_call.potential,
     init_call.topspender,
     init_call.upsell_client,
     init_call.focus_funnel,
     init_call.keycompany,
     init_call.pkclient,
     init_call.priorityc,
     init_call.q1_twetenty_closure_funnel,
     init_call.potential_funnel_for_fy,
     init_call.potential_funnel_for_fy,
     init_call.to_be_nurtured_for_fy,
     init_call.fifity_new_lead_funnel,
     u1.name as main_bd_name,
     u2.name as main_bd_manager_name,
     s1.name as task_time_status,
     s2.name as task_time_new_status,
     partner_master.name as partner_name,
     COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
     u10.name as pst_name,
     u4.name as ash_nae_co_id_name,
     u5.name as ash_w_co_id_name,
     u6.name as ash_s_co_id_name,
     u7.name as rm_east_co_id_name,
     u8.name as rm_north_co_id_name,
     u9.name as acm_co_id_name
 
FROM
    tblcallevents AS tbcl
LEFT JOIN init_call ON init_call.id = tbcl.cid_id
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
LEFT JOIN action ON action.id = tbcl.actiontype_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
LEFT JOIN status s1 ON s1.id = tbcl.status_id
LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
WHERE
    cast(tbcl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    $taskactionFilter
	$text");
$data1 =  $query->result(); 
  $result = [
    'totalTasksUserByDatas' => $data1
  ];
    return  $result;
}
public function GetFunnelTransferLogDatas($userid,$sdate,$edate,$admin_id_filter,$rm_filter){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "AND (u1.sadmin_id = '$userid' OR u2.sadmin_id = '$userid')";
    }else if($type_id == 2){
        $text = "AND (u1.admin_id = '$userid' OR u2.admin_id = '$userid')";
    }else if($type_id == 3){
        $text = "AND (u1.user_id = '$userid' OR u2.user_id = '$userid')";
    }else if($type_id == 4){
        $text = "AND ((u1.pst_co = '$userid' OR u1.user_id = '$userid') OR (u2.pst_co = '$userid' OR u2.user_id = '$userid'))";
    }else if($type_id == 13){
        $text = "AND ((u1.aadmin = '$userid' OR u1.user_id = '$userid') OR (u2.aadmin = '$userid' OR u2.user_id = '$userid'))";
    }else if($type_id == 15){
        $text = "AND (u1.sales_co = '$userid' OR u2.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "AND (u1.ash_nae_co = '$userid' OR u2.ash_nae_co = '$userid')";
    }else if($type_id == 20){
        $text = "AND (u1.ash_w_co='$userid' OR u2.ash_w_co='$userid')";
    }else if($type_id == 21){
        $text = "AND (u1.ash_s_co='$userid' OR u2.ash_s_co='$userid')";
    }else if($type_id == 22){
        $text = "AND (u1.rm_east_co='$userid' OR u2.rm_east_co='$userid')";
    }else if($type_id == 23){
        $text = "AND (u1.rm_north_co='$userid' OR u2.rm_north_co='$userid')";
    }else if($type_id == 24){
        $text = "AND ((u1.acm_co = '$userid' OR u1.user_id = '$userid') OR (u2.acm_co = '$userid' OR u2.user_id = '$userid'))";
    }else{
        $text = "AND (u1.admin_id = '$userid' OR u2.admin_id = '$userid')";
    }
    if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND (u1.admin_id IN (2,45) OR u2.admin_id IN (2,45))";
        }else if(in_array($cuid,[100000])){
            $text = "AND (u1.sadmin_id = '100000' OR u2.sadmin_id = '100000')";
        }else{
            $text = "AND (u1.admin_id = '$cuid' OR u2.admin_id = '$cuid')";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "AND (u1.sadmin_id = '$userid' OR u2.sadmin_id = '$userid')";
                }else if($type_id == 2){
                    $text = "AND (u1.admin_id = '$userid' OR u2.admin_id = '$userid')";
                }else if($type_id == 3){
                    $text = "AND (u1.user_id = '$userid' OR u2.user_id = '$userid')";
                }else if($type_id == 4){
                    $text = "AND ((u1.pst_co = '$userid' OR u1.user_id = '$userid') OR (u2.pst_co = '$userid' OR u2.user_id = '$userid'))";
                }else if($type_id == 13){
                    $text = "AND ((u1.aadmin = '$userid' OR u1.user_id = '$userid') OR (u2.aadmin = '$userid' OR u2.user_id = '$userid'))";
                }else if($type_id == 15){
                    $text = "AND (u1.sales_co = '$userid' OR u2.sales_co = '$userid')";
                }elseif($type_id == 19){
                    $text = "AND (u1.ash_nae_co = '$userid' OR u2.ash_nae_co = '$userid')";
                }else if($type_id == 20){
                    $text = "AND (u1.ash_w_co='$userid' OR u2.ash_w_co='$userid')";
                }else if($type_id == 21){
                    $text = "AND (u1.ash_s_co='$userid' OR u2.ash_s_co='$userid')";
                }else if($type_id == 22){
                    $text = "AND (u1.rm_east_co='$userid' OR u2.rm_east_co='$userid')";
                }else if($type_id == 23){
                    $text = "AND (u1.rm_north_co='$userid' OR u2.rm_north_co='$userid')";
                }else if($type_id == 24){
                    $text = "AND ((u1.acm_co = '$userid' OR u1.user_id = '$userid') OR (u2.acm_co = '$userid' OR u2.user_id = '$userid'))";
                }else{
                    $text = "AND (u1.admin_id = '$userid' OR u2.admin_id = '$userid')";
                }
        }
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
     $query = $this->db->query("SELECT
    ftl.id AS ftl_id,
    company_master.id AS cid,
    company_master.compname,
    u1.name AS form_name,
    u2.name AS to_name,
    u3.name AS by_name,
    s1.name AS to_status,
    ftl.remarks,
    ftl.created_at
    
FROM funnel_transfer_log ftl
LEFT JOIN company_master ON company_master.id = ftl.cid
LEFT JOIN user_details u1 ON u1.user_id = ftl.from_uid
LEFT JOIN user_details u2 ON u2.user_id = ftl.to_uid
LEFT JOIN user_details u3 ON u3.user_id = ftl.by_uid
LEFT JOIN status s1 on s1.id = ftl.new_status
WHERE cast(ftl.created_at as DATE) Between '$sdate' and '$edate'
$text
 ORDER BY ftl.created_at DESC");
    return $query->result();
}
public function GetTBLTaskUsingSinleTaskID($taskID) {
    $taskactionFilter = "(tbcl.actiontype_id !='' AND (tbcl.approved_status = 1 OR tbcl.approved_status = ''))";
    $query = $this->db->query("SELECT
            user_details.user_id AS task_user_id,
            user_details.name AS task_username,
            company_master.compname,
            company_master.id AS cid,
            tbcl.id AS task_id,
            tbcl.nextCFID,
            tbcl.fwd_date,
            tbcl.appointmentdatetime,
            tbcl.initiateddt as task_initiateddt,
            tbcl.updated_at,
            tbcl.updateddate as task_updateddate,
            CONCAT(
                FLOOR(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate) / 3600), ' hours ',
                FLOOR(MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 3600) / 60), ' minutes and ',
                MOD(TIMESTAMPDIFF(SECOND, COALESCE(tbcl.initiateddt, tbcl.appointmentdatetime), tbcl.updateddate), 60), ' seconds'
            ) AS total_time_taken,
            tbcl.late_remarks_message,
            tbcl.actontaken,
            tbcl.purpose_achieved,
            tbcl.meeting_type,
            tbcl.actiontype_id,
            tbcl.assignedto_id,
            tbcl.cid_id,
            tbcl.remarks,
            tbcl.mom AS mom_remarks,
            tbcl.mtype,
            tbcl.selectby,
            tbcl.special_remarks,
            tbcl.follow_up_id,
            tbcl.filter_by,
            tbcl.delete_request,
            tbcl.reminder_remarks,
            tbcl.approved_status AS task_approved_status,
            tbcl.approved_date AS task_approved_date,
            -- tbcl.approved_by AS task_approved_by,
            tbcl.assignedto_by AS task_assignedto_by,
            tbcl.assignedto_by AS task_aftertask,
            action.name AS task_name,
            status.name AS current_status,
            tbcl.plan_count AS plan_count,
            init_call.potential,
            init_call.topspender,
            init_call.upsell_client,
            init_call.focus_funnel,
            init_call.keycompany,
            init_call.pkclient,
            init_call.priorityc,
            init_call.q1_twetenty_closure_funnel,
            init_call.potential_funnel_for_fy,
            init_call.to_be_nurtured_for_fy,
            init_call.fifity_new_lead_funnel,
            init_call.anchor_clients,
            init_call.in_quarter,
            u1.name AS main_bd_name,
            u2.name AS main_bd_manager_name,
            s1.name AS task_time_status,
            s2.name AS task_time_new_status,
            s3.name as ccurrent_status,
            partner_master.name AS partner_name,
            COALESCE(u3.name, tbcl.approved_by) AS task_approved_by,
            u10.name AS pst_name,
            init_call.createDate as company_createDate,
            tbcl.mom as mom_remarks,
            partner_master.name as partner_master_name
        FROM
            tblcallevents AS tbcl
        LEFT JOIN init_call ON init_call.id = tbcl.cid_id
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        LEFT JOIN user_details ON user_details.user_id = tbcl.user_id
        LEFT JOIN action ON action.id = tbcl.actiontype_id
        LEFT JOIN status ON status.id = init_call.cstatus
        LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
        LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
        LEFT JOIN user_details u3 ON u3.user_id = tbcl.approved_by
        LEFT JOIN status s1 ON s1.id = tbcl.status_id
        LEFT JOIN status s2 ON s2.id = tbcl.nstatus_id
        LEFT JOIN status s3 ON s3.id = init_call.cstatus
        LEFT JOIN user_details u10 ON u10.user_id = init_call.apst
        WHERE
        tbcl.id = '$taskID'
    ");
    return $query->result();
}
public function GetTBLTaskReminderCommentsByTaskIDs($taskID) {
    $query = $this->db->query("SELECT
    reminder_comments.*,
    u1.name as reminderby_name
FROM
    `reminder_comments`
    LEFT JOIN user_details u1 on u1.user_id = reminder_comments.reminderby
WHERE
    task_id = '$taskID'
     ORDER BY reminder_comments.created_at DESC
    ");
    return $query->result();
}
public function GetSeesionTimeTakeBYSConTaskCheck($userid,$check_date1,$check_date2,$admin_id_filter,$rm_filter) {
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    


    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 3){
        
    $cudetail = $this->Menu_model->get_userbyid($userid);
    $sales_co = $cudetail[0]->sales_co;
    $aadmin = $cudetail[0]->aadmin;
    $filteruserslct = '';
        if($sales_co !=0){
            $filteruserslct = $sales_co;
        }
        if($sales_co !=0){
            $filteruserslct += ','.$aadmin;
        }
    $text = "u1.user_id IN ($filteruserslct) AND u2.user_id = '$userid'";
    }else if($type_id == 4){
    $cudetail = $this->Menu_model->get_userbyid($userid);
    $pst_co = $cudetail[0]->pst_co;
    $sales_co = $cudetail[0]->sales_co;
    $filteruserslct = '';
    if($sales_co !=0){
            $filteruserslct = $sales_co;
    }
    if($pst_co !=0){
            $filteruserslct = ",".$userid;
    }
    $text = "(u1.user_id IN  ($filteruserslct))";
    }else if($type_id == 13){
    $cudetail = $this->Menu_model->get_userbyid($userid);
    $sales_co = $cudetail[0]->sales_co;
    $aadmin = $cudetail[0]->aadmin;
    $filteruserslct = '';
        if($sales_co !=0){
            $filteruserslct = $sales_co;
        }
        if($sales_co !=0){
            $filteruserslct += ','.$userid;
        }
    $text = "u1.user_id IN ($filteruserslct)";
    }else if($type_id == 15){
        $text = "(u1.sales_co ='$userid' || u1.user_id = '$userid') AND u1.type_id IN(13,4,15) ";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid AND u1.type_id IN(13,4,15) )";
    }else{
        $text  = "(u1.admin_id = $userid AND u1.type_id IN(13,4,15) )";
    }


 


    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = " u1.admin_id IN (2,45) AND u1.type_id IN(13,4,15)";
        }else if(in_array($cuid,[100000])){
            $text = " u1.sadmin_id = '100000' AND u1.type_id IN(13,4,15)";
        }else{
            $text = " u1.admin_id = '$cuid' AND u1.type_id IN(13,4,15)";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
               if($type_id == 1){
        $text = "(u1.sadmin_id = $userid AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 3){
        
    $cudetail = $this->Menu_model->get_userbyid($userid);
    $sales_co = $cudetail[0]->sales_co;
    $aadmin = $cudetail[0]->aadmin;
    $filteruserslct = '';
        if($sales_co !=0){
            $filteruserslct = $sales_co;
        }
        if($sales_co !=0){
            $filteruserslct += ','.$aadmin;
        }
    $text = "u1.user_id IN ($filteruserslct) AND u2.user_id = '$userid'";
    }else if($type_id == 4){
    $cudetail = $this->Menu_model->get_userbyid($userid);
    $pst_co = $cudetail[0]->pst_co;
    $sales_co = $cudetail[0]->sales_co;
    $filteruserslct = '';
    if($sales_co !=0){
            $filteruserslct = $sales_co;
    }
    if($pst_co !=0){
            $filteruserslct = ",".$userid;
    }
    $text = "(u1.user_id IN  ($filteruserslct))";
    }else if($type_id == 13){
    $cudetail = $this->Menu_model->get_userbyid($userid);
    $sales_co = $cudetail[0]->sales_co;
    $aadmin = $cudetail[0]->aadmin;
    $filteruserslct = '';
        if($sales_co !=0){
            $filteruserslct = $sales_co;
        }
        if($sales_co !=0){
            $filteruserslct += ','.$userid;
        }
    $text = "u1.user_id IN ($filteruserslct)";
    }else if($type_id == 15){
        $text = "(u1.sales_co ='$userid' || u1.user_id = '$userid') AND u1.type_id IN(13,4,15) AND u2.sales_co ='$userid'";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' AND u1.type_id IN(13,4,15) )";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid AND u1.type_id IN(13,4,15) )";
    }else{
        $text  = "(u1.admin_id = $userid AND u1.type_id IN(13,4,15) )";
    }
        }
    }


    $query = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text
    AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,u1.user_id
ORDER BY
    tcs.check_date");
    $data1 =  $query->result();


     $query1 = $this->db->query("SELECT
    tcs.check_date,
    -- u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    u2.user_id AS to_user_user_id,
    u2.name AS to_user_name,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,tcs.user_id
ORDER BY
    tcs.check_date");
    $data2 =  $query1->result();


     $query3 = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manager_user_id,
    u1.name AS by_line_manager,
    COUNT(tcs.id) AS total_session,
    COUNT(DISTINCT tcs.user_id) AS number_of_user,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session,
    
    CONCAT(
        FLOOR((SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id)) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 60), ' seconds'
    ) AS average_time_taken
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 ON u1.user_id = tcs.check_by
WHERE
    $text AND  cast(tcs.check_date as DATE) BETWEEN '$check_date1' and '$check_date2'
    AND u1.type_id IN (13,4,15)
    AND tcs.check_type = 'task check'
GROUP BY
 u1.user_id");

    $data3 =  $query3->result();



    // echo $text;
    // die;

    
     $query4 = $this->db->query("SELECT
    u1.user_id AS by_line_manager_user_id,
    u1.name AS by_line_manager,
    COUNT(tcs.id) AS total_session,
    COUNT(DISTINCT tcs.user_id) AS number_of_user,
   
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session,
    
    CONCAT(
        FLOOR((SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id)) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 60), ' seconds'
    ) AS average_time_taken,

    -- Existing task counts
    (
        SELECT COUNT(DISTINCT tblc.id) 
        FROM tblcallevents tblc
        LEFT JOIN user_details u2 ON u2.user_id = tblc.user_id
        WHERE 
            (
                (u2.type_id IN (3,24) AND u2.aadmin = tcs.check_by)
                OR
                (u2.type_id IN (3,24,13,4) AND u2.sales_co = tcs.check_by)
            )
            AND (tblc.actiontype_id != '' AND (tblc.approved_status = 1 OR tblc.approved_status = ''))
            AND CAST(tblc.appointmentdatetime AS DATE) BETWEEN '$check_date1' and '$check_date2'
    ) AS total_task_in_present,
    
    (
        SELECT COUNT(DISTINCT str.task_id)
        FROM sales_task_star_rating str
        WHERE str.star_by = tcs.check_by
        AND CAST(str.date AS DATE) BETWEEN '$check_date1' and '$check_date2'
    ) AS total_task_check,
    
    (
        SELECT COUNT(DISTINCT str.task_id)
        FROM sales_task_star_rating str
        WHERE str.star_by = tcs.check_by
          AND CAST(str.date AS DATE) BETWEEN '$check_date1' and '$check_date2'
          AND str.task_id IN (
              SELECT tblc.id
              FROM tblcallevents tblc
              WHERE 
                  (tblc.actiontype_id != '' AND (tblc.approved_status = 1 OR tblc.approved_status = ''))
                  AND CAST(tblc.appointmentdatetime AS DATE) BETWEEN '$check_date1' and '$check_date2'
          )
    ) AS total_task_check_present,

    -- ✅ New column: average time taken per task
    CASE 
        WHEN (
            SELECT COUNT(DISTINCT str.task_id)
            FROM sales_task_star_rating str
            WHERE str.star_by = tcs.check_by
            AND CAST(str.date AS DATE) BETWEEN '$check_date1' and '$check_date2'
        ) = 0 THEN NULL
        ELSE CONCAT(
            FLOOR(
                SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE))))
                /
                (SELECT COUNT(DISTINCT str.task_id)
                 FROM sales_task_star_rating str
                 WHERE str.star_by = tcs.check_by
                 AND CAST(str.date AS DATE) BETWEEN '$check_date1' and '$check_date2')
                 / 3600
            ), ' hours ',
            FLOOR(
                MOD(
                    SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE))))
                    /
                    (SELECT COUNT(DISTINCT str.task_id)
                     FROM sales_task_star_rating str
                     WHERE str.star_by = tcs.check_by
                     AND CAST(str.date AS DATE) BETWEEN '$check_date1' and '$check_date2')
                , 3600) / 60
            ), ' minutes ',
            MOD(
                SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE))))
                /
                (SELECT COUNT(DISTINCT str.task_id)
                 FROM sales_task_star_rating str
                 WHERE str.star_by = tcs.check_by
                 AND CAST(str.date AS DATE) BETWEEN '$check_date1' and '$check_date2')
            , 60
            ), ' seconds'
        )
    END AS average_time_taken_per_task

FROM
    task_check_session tcs
    LEFT JOIN user_details u1 ON u1.user_id = tcs.check_by
    
WHERE
    $text 
    AND CAST(tcs.check_date AS DATE) BETWEEN '$check_date1' and '$check_date2'
    AND u1.type_id IN (13,4,15)
    AND tcs.check_type = 'task check'

GROUP BY
    u1.user_id");
    $data4 =  $query4->result();
   




 $result = [
    'totalTimeByUser'               => $data1,
    'totalTimeByEachUser'           => $data2,
    'average_time_taken'            => $data3,
     'average_time_taken_with_task' => $data4
 ];
 return $result;
}
public function GetSeesionTimeTakeBYLineManageronTaskCheck($userid,$check_date1,$check_date2,$admin_id_filter,$rm_filter) {
  

    $text  = "(u1.user_id = $admin_id_filter)";

    $query = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text
    AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    -- AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,u1.user_id
ORDER BY
    tcs.check_date");
    $data1 =  $query->result();


     $query1 = $this->db->query("SELECT
    tcs.check_date,
    -- u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    u2.user_id AS to_user_user_id,
    u2.name AS to_user_name,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    -- AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,tcs.user_id
ORDER BY
    tcs.check_date");
    $data2 =  $query1->result();


     $query3 = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manager_user_id,
    u1.name AS by_line_manager,
    COUNT(tcs.id) AS total_session,
    COUNT(DISTINCT tcs.user_id) AS number_of_user,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session,
    
    CONCAT(
        FLOOR((SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id)) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 60), ' seconds'
    ) AS average_time_taken
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 ON u1.user_id = tcs.check_by
WHERE
    $text AND  cast(tcs.check_date as DATE) BETWEEN '$check_date1' and '$check_date2'
    -- AND u1.type_id IN (13,4,15)
    AND tcs.check_type = 'task check'
GROUP BY
 u1.user_id");



    $data3 =  $query3->result();
 $result = [
    'totalTimeByUser' => $data1,
    'totalTimeByEachUser' => $data2,
    'average_time_taken' => $data3,
 ];
 return $result;
}
public function GetSeesionTimeTakeBYSLineManagerConTaskCheck($userid,$check_date1,$check_date2,$admin_id_filter,$rm_filter) {
  
    $text = "(u1.user_id = '$admin_id_filter')";

    $query = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text
    AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    -- AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,u1.user_id
ORDER BY
    tcs.check_date");
    $data1 =  $query->result();


     $query1 = $this->db->query("SELECT
    tcs.check_date,
    -- u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    u2.user_id AS to_user_user_id,
    u2.name AS to_user_name,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    -- AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,tcs.user_id
ORDER BY
    tcs.check_date");
    $data2 =  $query1->result();


     $query3 = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manager_user_id,
    u1.name AS by_line_manager,
    COUNT(tcs.id) AS total_session,
    COUNT(DISTINCT tcs.user_id) AS number_of_user,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session,
    
    CONCAT(
        FLOOR((SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id)) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 60), ' seconds'
    ) AS average_time_taken
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 ON u1.user_id = tcs.check_by
WHERE
    $text AND  cast(tcs.check_date as DATE) BETWEEN '$check_date1' and '$check_date2'
    -- AND u1.type_id IN (13,4,15)
    AND tcs.check_type = 'task check'
GROUP BY
 u1.user_id");



    $data3 =  $query3->result();
 $result = [
    'totalTimeByUser' => $data1,
    'totalTimeByEachUser' => $data2,
    'average_time_taken' => $data3,
 ];
 return $result;
}
public function GetSeesionTimeTakeBYSConTaskCheckBySingleUser($userid,$check_date1,$check_date2,$approved_by) {
     $text  = " u2.user_id = '$userid' ";
    $query = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text
    AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,u1.user_id
ORDER BY
    tcs.check_date");
    $data1 =  $query->result();
     $query1 = $this->db->query("SELECT
    tcs.check_date,
    -- u1.user_id AS by_line_manger_user_id,
    u1.name AS by_line_manger,
    u2.user_id AS to_user_user_id,
    u2.name AS to_user_name,
    COUNT(tcs.id) AS total_session,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 on u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text AND cast(tcs.check_date as DATE) Between '$check_date1' and '$check_date2'
    AND u1.type_id IN(13,4,15)
GROUP BY
    tcs.check_date,tcs.user_id
ORDER BY
    tcs.check_date");
    $data2 =  $query1->result();
     $query3 = $this->db->query("SELECT
    tcs.check_date,
    u1.user_id AS by_line_manager_user_id,
    u1.name AS by_line_manager,
    COUNT(tcs.id) AS total_session,
    COUNT(DISTINCT tcs.user_id) AS number_of_user,
    CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session,
    
    CONCAT(
        FLOOR((SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id)) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / COUNT(DISTINCT tcs.user_id), 60), ' seconds'
    ) AS average_time_taken
FROM
    task_check_session tcs
    LEFT JOIN user_details u1 ON u1.user_id = tcs.check_by
    LEFT JOIN user_details u2 on u2.user_id = tcs.user_id
WHERE
    $text AND  cast(tcs.check_date as DATE) BETWEEN '$check_date1' and '$check_date2'
    AND u1.type_id IN (13,4,15)
    AND tcs.check_type = 'task check'
GROUP BY
 u1.user_id");
    $data3 =  $query3->result();

 $result = [
    'totalTimeByUser' => $data1,
    'totalTimeByEachUser' => $data2,
    'average_time_taken' => $data3
 ];
 return $result;
}
public function GetSeesionTimeTakeBYSCOnUseronTaskCheck($user_id,$check_date) {
    $query = $this->db->query("SELECT
    tcs.check_date,
    COUNT(tcs.id) AS total_session,
       CONCAT(
        FLOOR(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))) / 86400), ' days ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 86400) / 3600), ' hours ',
        FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 3600) / 60), ' minutes ',
        MOD(SUM(TIMESTAMPDIFF(SECOND, tcs.psdatetime, COALESCE(tcs.pcdatetime, DATE_ADD(tcs.psdatetime, INTERVAL 2 MINUTE)))), 60), ' seconds'
    ) AS total_time_in_session
FROM
    task_check_session tcs
    
WHERE
    tcs.user_id = '$user_id'
    AND tcs.check_date = '$check_date'
GROUP BY
    tcs.check_date
ORDER BY
    tcs.check_date");
    return $query->result();
}
public function GetCompanyAfterLMAssignSameStatusSinceDays($userid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "user_details.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "user_details.admin_id = $userid";
    }else if($type_id == 3){
        $text = "user_details.user_id = $userid";
    }else if($type_id == 4){
        $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
    }else if($type_id == 15){
        $text = "user_details.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
    }else{
        $text = "user_details.admin_id = $userid";
    }
    
    if($admin_id_filter =='all'){
        // $text = "user_details.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND user_details.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
               $text = "AND user_details.sadmin_id = '100000'";
        }else{
            $text = "AND user_details.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id ==1){
                    $text = "user_details.sadmin_id = $userid";
                }else if($type_id == 2){
                    $text = "user_details.admin_id = $userid";
                }else if($type_id == 3){
                    $text = "user_details.user_id = $userid";
                }else if($type_id == 4){
                    $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "user_details.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
                }else if($type_id == 20){
                    $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 21){
                    $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 22){
                    $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 23){
                    $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 24){
                    $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
                }else{
                    $text = "user_details.admin_id = $userid";
                }
        }
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
$mainCompanyFilter  = "AND init_call.mainbd = u1.user_id ";
$mainCompanyFilter .= "AND (";
$mainCompanyFilter .= "init_call.clm_id = u1.aadmin ";
$mainCompanyFilter .= "OR (init_call.apst = u1.pst_co AND init_call.apst IS NOT NULL AND init_call.apst != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_nae_co_id = u1.ash_nae_co AND init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_w_co_id = u1.ash_w_co AND init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_s_co_id = u1.ash_s_co AND init_call.ash_s_co_id IS NOT NULL AND init_call.ash_s_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.rm_east_co_id = u1.rm_east_co AND init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.rm_north_co_id = u1.rm_north_co AND init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.acm_co_id = u1.acm_co AND init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0)";
$mainCompanyFilter .= ")";
     $userstatus = "user_details.status = 'active'";
     $cuserstatus = "u1.status = 'active'";
     $query = $this->db->query("SELECT *
                FROM (
                    SELECT 
                        init_call.id AS inid,
                        company_master.id AS cid,
                        company_master.compname AS compname,
                        u1.name AS main_bd_name,
                        partner_master.name AS pname,
                        cur_status.name AS current_status,
                        s2.name AS last_task_on_status_name,
                        s3.name AS last_update_status_name,
                        a1.name AS last_task_name,
                        user_details.name AS task_username,
                        main_review.sdate AS main_review_date,
                        main_review.rtype AS main_review_rtype,
                        u2.name AS cluster_manager_name,
                        u3.name AS pst_name,
                        u4.name AS ash_nae_co_name,
                        u5.name AS ash_w_co_name,
                        u6.name AS ash_s_co_name,
                        u7.name AS rm_east_co_name,
                        u8.name AS rm_north_co_name,
                        u9.name AS acm_co_name,
                        
                        u2.user_id AS cluster_manager_user_id,
                        u3.user_id AS pst_user_id,
                        u4.user_id AS ash_nae_co_user_id,
                        u5.user_id AS ash_w_co_user_id,
                        u6.user_id AS ash_s_co_user_id,
                        u7.user_id AS rm_east_co_user_id,
                        u8.user_id AS rm_north_co_user_id,
                        u9.user_id AS acm_co_user_id,
                        MAX(DATEDIFF('$edate', tblcallevents.appointmentdatetime)) AS days,
                        GREATEST(
                            DATE(main_review.sdate),
                            DATE(MAX(tblcallevents.updated_at))
                        ) AS review_date,
                        COUNT(
                            CASE 
                                WHEN DATE(tblcallevents.appointmentdatetime) 
                                    BETWEEN '$sdate' AND '$edate' 
                                THEN tblcallevents.id 
                            END
                        ) AS total_log_in_days,
                        ( SELECT tce.appointmentdatetime 
                        FROM tblcallevents tce 
                        WHERE tce.cid_id = init_call.id 
                        ORDER BY tce.id DESC 
                        LIMIT 1 ) AS last_task_update,
                        DATEDIFF(
                            '$edate', 
                            MAX(tblcallevents.appointmentdatetime)
                        ) AS last_task_days,
                        ( SELECT tce.id 
                        FROM tblcallevents tce 
                        WHERE tce.cid_id = init_call.id 
                        ORDER BY tce.id DESC 
                        LIMIT 1 ) AS last_task_id,
                        ( SELECT CASE 
                                WHEN tce.nextCFID = 0 THEN 'Pending' 
                                ELSE 'Complete' 
                                END
                        FROM tblcallevents tce 
                        WHERE tce.cid_id = init_call.id 
                        ORDER BY tce.id DESC 
                        LIMIT 1 ) AS last_task_status
                    FROM init_call
                    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
                    LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
                    LEFT JOIN tblcallevents 
                        ON tblcallevents.id = (
                            SELECT MAX(tce.id) 
                            FROM tblcallevents tce 
                            WHERE tce.cid_id = init_call.id
                        )
                    LEFT JOIN main_review ON main_review.inid = init_call.id
                    LEFT JOIN user_details ON user_details.user_id = tblcallevents.user_id
                    LEFT JOIN status cur_status ON cur_status.id = init_call.cstatus
                    LEFT JOIN status s2 ON s2.id = tblcallevents.status_id
                    LEFT JOIN status s3 ON s3.id = tblcallevents.nstatus_id
                    LEFT JOIN action a1 
                    ON a1.id = (
                        SELECT tce.actiontype_id
                        FROM tblcallevents tce
                        WHERE tce.cid_id = init_call.id
                        ORDER BY tce.id DESC
                        LIMIT 1
                    )
                    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
                    LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id AND u2.status = 'active'
                    LEFT JOIN user_details u3 ON u3.user_id = init_call.apst AND u3.status = 'active'
                    LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id AND u4.status = 'active'
                    LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id AND u5.status = 'active'
                    LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id AND u6.status = 'active'
                    LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id AND u7.status = 'active'
                    LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id AND u8.status = 'active'
                    LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id AND u9.status = 'active'
                    WHERE $text AND $userstatus AND $cuserstatus
                    AND init_call.cstatus != ''
                    AND init_call.cstatus NOT IN (7,14)
                    AND tblcallevents.id IS NOT NULL
                    AND main_review.sdate IS NOT NULL
                    -- AND user_details.type_id IN(4,13,18,19,20,21,22,23,24)
                     $mainCompanyFilter
                    AND EXISTS (
                        SELECT 1 
                        FROM tblcallevents sub_tbcl 
                        WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP')
                        AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
                        AND sub_tbcl.cid_id = init_call.id
                    )
                    GROUP BY init_call.id, init_call.cstatus
                ) AS sub
                WHERE sub.days > $days_inclusive
                AND sub.last_task_days > $days_inclusive
                AND last_task_update < '$edate'
                ");
     $datas = $query->result();
    return $datas;
}
public function GetCompanyAfterLMAssignSameStatusSinceDaysTaskLogsCount($userid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "ud.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "ud.admin_id = $userid";
    }else if($type_id == 3){
        $text = "ud.user_id = $userid";
    }else if($type_id == 4){
        $text = "(ud.pst_co = $userid || ud.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(ud.aadmin = $userid || ud.user_id = $userid)";
    }else if($type_id == 15){
        $text = "ud.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "(ud.ash_nae_co = '$userid' || ud.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(ud.ash_w_co='$userid' || ud.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(ud.ash_s_co='$userid' || ud.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(ud.rm_east_co='$userid' || ud.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(ud.rm_north_co='$userid' || ud.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(ud.acm_co = $userid || ud.user_id = $userid)";
    }else{
        $text = "ud.admin_id = $userid";
    }
    
    if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND ud.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
               $text = "AND ud.sadmin_id = '100000'";
        }else{
            $text = "AND ud.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id ==1){
                    $text = "ud.sadmin_id = $userid";
                }else if($type_id == 2){
                    $text = "ud.admin_id = $userid";
                }else if($type_id == 3){
                    $text = "ud.user_id = $userid";
                }else if($type_id == 4){
                    $text = "(ud.pst_co = $userid || ud.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "(ud.aadmin = $userid || ud.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "ud.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "(ud.ash_nae_co = '$userid' || ud.user_id = $userid)";
                }else if($type_id == 20){
                    $text = "(ud.ash_w_co='$userid' || ud.user_id = $userid)";
                }else if($type_id == 21){
                    $text = "(ud.ash_s_co='$userid' || ud.user_id = $userid)";
                }else if($type_id == 22){
                    $text = "(ud.rm_east_co='$userid' || ud.user_id = $userid)";
                }else if($type_id == 23){
                    $text = "(ud.rm_north_co='$userid' || ud.user_id = $userid)";
                }else if($type_id == 24){
                    $text = "(ud.acm_co = $userid || ud.user_id = $userid)";
                }else{
                    $text = "ud.admin_id = $userid";
                }
        }
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
$mainCompanyFilter  = "AND init_call.mainbd = u1.user_id ";
$mainCompanyFilter .= "AND (";
$mainCompanyFilter .= "init_call.clm_id = u1.aadmin ";
$mainCompanyFilter .= "OR (init_call.apst = u1.pst_co AND init_call.apst IS NOT NULL AND init_call.apst != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_nae_co_id = u1.ash_nae_co AND init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_w_co_id = u1.ash_w_co AND init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_s_co_id = u1.ash_s_co AND init_call.ash_s_co_id IS NOT NULL AND init_call.ash_s_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.rm_east_co_id = u1.rm_east_co AND init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.rm_north_co_id = u1.rm_north_co AND init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.acm_co_id = u1.acm_co AND init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0)";
$mainCompanyFilter .= ")";
     $userstatus = "user_details.status = 'active'";
     $cuserstatus = "u1.status = 'active'";
//    $query = $this->db->query("SELECT *
//     FROM (
//         SELECT 
//             company_master.id AS cid,
//             company_master.compname AS compname,
//             user_details.name AS task_username,
//             a2.name AS task_name,
//             COUNT(tblcallevents.id) AS total_events,
//             u2.name AS cluster_manager_name,
//             u3.name AS pst_name,
//             u4.name AS ash_nae_co_name,
//             u5.name AS ash_w_co_name,
//             u6.name AS ash_s_co_name,
//             u7.name AS rm_east_co_name,
//             u8.name AS rm_north_co_name,
//             u9.name AS acm_co_name,
//             tblcallevents.appointmentdatetime,
//             MAX(DATEDIFF('$edate', tblcallevents.appointmentdatetime)) AS days
//         FROM init_call
//         LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
//         LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
//         LEFT JOIN tblcallevents ON tblcallevents.cid_id = init_call.id 
//         LEFT JOIN main_review ON main_review.inid = init_call.id
//         LEFT JOIN user_details ON user_details.user_id = tblcallevents.user_id
//         LEFT JOIN status cur_status ON cur_status.id = init_call.cstatus
//         LEFT JOIN status s2 ON s2.id = tblcallevents.status_id
//         LEFT JOIN status s3 ON s3.id = tblcallevents.nstatus_id
//         LEFT JOIN action a2 ON a2.id = tblcallevents.actiontype_id
//         LEFT JOIN action a1 ON a1.id = (
//             SELECT tce.actiontype_id
//             FROM tblcallevents tce
//             WHERE tce.cid_id = init_call.id
//             ORDER BY tce.id DESC
//             LIMIT 1
//         )
//         LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
//         LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id AND u2.status = 'active'
//         LEFT JOIN user_details u3 ON u3.user_id = init_call.apst AND u3.status = 'active'
//         LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id AND u4.status = 'active'
//         LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id AND u5.status = 'active'
//         LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id AND u6.status = 'active'
//         LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id AND u7.status = 'active'
//         LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id AND u8.status = 'active'
//         LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id AND u9.status = 'active'
//         WHERE $text AND $userstatus AND $cuserstatus
//           AND init_call.cstatus != ''
//           AND init_call.cstatus NOT IN (7,14)
//           AND tblcallevents.id IS NOT NULL
//           AND main_review.sdate IS NOT NULL
//           AND (tblcallevents.delete_request = '' OR tblcallevents.delete_request IS NULL)
//           AND (tblcallevents.approved_status = 1 || tblcallevents.approved_status = '')
//           AND tblcallevents.nextCFID != 0
//           AND user_details.type_id IN (4,13,18,19,20,21,22,23,24)
//           $mainCompanyFilter
//           AND EXISTS (
//               SELECT 1 
//               FROM tblcallevents sub_tbcl 
//               WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP') 
//                 AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
//                 AND sub_tbcl.cid_id = init_call.id
//           )
//         GROUP BY 
//             init_call.id, 
//             tblcallevents.user_id, 
//             a2.id
//     ) AS sub
//     WHERE sub.days > $days_inclusive
  
// ");
   $query = $this->db->query("SELECT
	tce.id,
    c.id AS cid,
    c.compname,
    ud.name AS task_username,
    a2.name AS task_name,
    COUNT(DISTINCT tce.id) AS total_events,
    u2.name AS cluster_manager_name,
    u3.name AS pst_name,
    u4.name AS ash_nae_co_name,
    u5.name AS ash_w_co_name,
    u6.name AS ash_s_co_name,
    u7.name AS rm_east_co_name,
    u8.name AS rm_north_co_name,
    u9.name AS acm_co_name,
    u10.name AS main_bd_name,
    status.name AS current_status_name,
	tce.appointmentdatetime,
    MAX(DATEDIFF('$edate', tce.appointmentdatetime)) AS days
FROM init_call ic
JOIN company_master c ON c.id = ic.cmpid_id
JOIN main_review mr ON mr.inid = ic.id AND mr.sdate IS NOT NULL
JOIN tblcallevents tce
    ON tce.cid_id = ic.id
   AND (tce.delete_request = '' OR tce.delete_request IS NULL)
   AND (tce.approved_status = 1 OR tce.approved_status = '')
   AND tce.nextCFID != 0
JOIN user_details ud
    ON ud.user_id = tce.user_id
    AND $text
   AND ud.status = 'active'
   -- AND ud.type_id IN (4,13,18,19,20,21,22,23,24)
JOIN user_details u1 ON u1.user_id = ic.mainbd AND u1.status = 'active'
LEFT JOIN action a2 ON a2.id = tce.actiontype_id
LEFT JOIN user_details u2 ON u2.user_id = ic.clm_id       AND u2.status = 'active'
LEFT JOIN user_details u3 ON u3.user_id = ic.apst         AND u3.status = 'active'
LEFT JOIN user_details u4 ON u4.user_id = ic.ash_nae_co_id AND u4.status = 'active'
LEFT JOIN user_details u5 ON u5.user_id = ic.ash_w_co_id   AND u5.status = 'active'
LEFT JOIN user_details u6 ON u6.user_id = ic.ash_s_co_id   AND u6.status = 'active'
LEFT JOIN user_details u7 ON u7.user_id = ic.rm_east_co_id AND u7.status = 'active'
LEFT JOIN user_details u8 ON u8.user_id = ic.rm_north_co_id AND u8.status = 'active'
LEFT JOIN user_details u9 ON u9.user_id = ic.acm_co_id      AND u9.status = 'active'
LEFT JOIN user_details u10 ON u10.user_id = ic.mainbd      AND u10.status = 'active'
LEFT JOIN status ON status.id = ic.cstatus 
WHERE ic.cstatus != '' 
  AND ic.cstatus NOT IN (7,14)
  AND YEAR(tce.appointmentdatetime) = $year
  AND (
        ic.mainbd = u1.user_id AND
        (ic.clm_id = u1.aadmin OR
        (ic.apst = u1.pst_co AND ic.apst IS NOT NULL AND ic.apst != 0) OR
        (ic.ash_nae_co_id = u1.ash_nae_co AND ic.ash_nae_co_id IS NOT NULL AND ic.ash_nae_co_id != 0) OR
        (ic.ash_w_co_id = u1.ash_w_co AND ic.ash_w_co_id IS NOT NULL AND ic.ash_w_co_id != 0) OR
        (ic.ash_s_co_id = u1.ash_s_co AND ic.ash_s_co_id IS NOT NULL AND ic.ash_s_co_id != 0) OR
        (ic.rm_east_co_id = u1.rm_east_co AND ic.rm_east_co_id IS NOT NULL AND ic.rm_east_co_id != 0) OR
        (ic.rm_north_co_id = u1.rm_north_co AND ic.rm_north_co_id IS NOT NULL AND ic.rm_north_co_id != 0) OR
        (ic.acm_co_id = u1.acm_co AND ic.acm_co_id IS NOT NULL AND ic.acm_co_id != 0))
      )
        AND EXISTS (
            SELECT 1 FROM tblcallevents sub
            WHERE sub.cid_id = ic.id
                AND sub.mtype IN ('RP','RPClose','Change RP')
                AND YEAR(sub.appointmentdatetime) = $year
        )
--   AND ic.cstatus = tce.nstatus_id 
--   AND tce.nstatus_id IS NOT NULL 
--   AND tce.nstatus_id !=0
GROUP BY ic.id, ud.user_id, a2.id
HAVING MAX(DATEDIFF('$edate', tce.appointmentdatetime)) > $days_inclusive");
     $datas = $query->result();
    return $datas;
}
public function GetCompanyAfterLMAssignSameStatusSinceDaysLists($userid,$sdate,$edate,$admin_id_filter,$rm_filter,$firstDays,$secondDays){
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "user_details.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "user_details.admin_id = $userid";
    }else if($type_id == 3){
        $text = "user_details.user_id = $userid";
    }else if($type_id == 4){
        $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
    }else if($type_id == 15){
        $text = "user_details.sales_co = $userid";
    }elseif($type_id == 19){
        $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
    }else{
        $text = "user_details.admin_id = $userid";
    }
    
    if($admin_id_filter =='all'){
        // $text = "user_details.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "AND user_details.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
               $text = "AND user_details.sadmin_id = '100000'";
        }else{
            $text = "AND user_details.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id ==1){
                    $text = "user_details.sadmin_id = $userid";
                }else if($type_id == 2){
                    $text = "user_details.admin_id = $userid";
                }else if($type_id == 3){
                    $text = "user_details.user_id = $userid";
                }else if($type_id == 4){
                    $text = "(user_details.pst_co = $userid || user_details.user_id = $userid)";
                }else if($type_id == 13){
                    $text = "(user_details.aadmin = $userid || user_details.user_id = $userid)";
                }else if($type_id == 15){
                    $text = "user_details.sales_co = $userid";
                }elseif($type_id == 19){
                    $text = "(user_details.ash_nae_co = '$userid' || user_details.user_id = $userid)";
                }else if($type_id == 20){
                    $text = "(user_details.ash_w_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 21){
                    $text = "(user_details.ash_s_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 22){
                    $text = "(user_details.rm_east_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 23){
                    $text = "(user_details.rm_north_co='$userid' || user_details.user_id = $userid)";
                }else if($type_id == 24){
                    $text = "(user_details.acm_co = $userid || user_details.user_id = $userid)";
                }else{
                    $text = "user_details.admin_id = $userid";
                }
        }
    }
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
$mainCompanyFilter  = "AND init_call.mainbd = u1.user_id ";
$mainCompanyFilter .= "AND (";
$mainCompanyFilter .= "init_call.clm_id = u1.aadmin ";
$mainCompanyFilter .= "OR (init_call.apst = u1.pst_co AND init_call.apst IS NOT NULL AND init_call.apst != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_nae_co_id = u1.ash_nae_co AND init_call.ash_nae_co_id IS NOT NULL AND init_call.ash_nae_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_w_co_id = u1.ash_w_co AND init_call.ash_w_co_id IS NOT NULL AND init_call.ash_w_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.ash_s_co_id = u1.ash_s_co AND init_call.ash_s_co_id IS NOT NULL AND init_call.ash_s_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.rm_east_co_id = u1.rm_east_co AND init_call.rm_east_co_id IS NOT NULL AND init_call.rm_east_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.rm_north_co_id = u1.rm_north_co AND init_call.rm_north_co_id IS NOT NULL AND init_call.rm_north_co_id != 0) ";
$mainCompanyFilter .= "OR (init_call.acm_co_id = u1.acm_co AND init_call.acm_co_id IS NOT NULL AND init_call.acm_co_id != 0)";
$mainCompanyFilter .= ")";
     $userstatus = "user_details.status = 'active'";
     $cuserstatus = "u1.status = 'active'";
     $query = $this->db->query("SELECT *
    FROM (
        SELECT 
            init_call.id AS inid,
            company_master.id AS cid,
            company_master.compname AS compname,
            u1.name AS main_bd_name,
            partner_master.name AS pname,
            cur_status.name AS current_status,
            s2.name AS last_task_on_status_name,
            s3.name AS last_update_status_name,
            a1.name AS last_task_name,
            user_details.name AS task_username,
            main_review.sdate AS main_review_date,
            main_review.rtype AS main_review_rtype,
            u2.name AS cluster_manager_name,
            u3.name AS pst_name,
            u4.name AS ash_nae_co_name,
            u5.name AS ash_w_co_name,
            u6.name AS ash_s_co_name,
            u7.name AS rm_east_co_name,
            u8.name AS rm_north_co_name,
            u9.name AS acm_co_name,
            
            u2.user_id AS cluster_manager_user_id,
            u3.user_id AS pst_user_id,
            u4.user_id AS ash_nae_co_user_id,
            u5.user_id AS ash_w_co_user_id,
            u6.user_id AS ash_s_co_user_id,
            u7.user_id AS rm_east_co_user_id,
            u8.user_id AS rm_north_co_user_id,
            u9.user_id AS acm_co_user_id,
            MAX(DATEDIFF('$edate', tblcallevents.appointmentdatetime)) AS days,
            GREATEST(
                DATE(main_review.sdate),
                DATE(MAX(tblcallevents.updated_at))
            ) AS review_date,
            COUNT(
                CASE 
                    WHEN DATE(tblcallevents.appointmentdatetime) 
                        BETWEEN '$sdate' AND '$edate' 
                    THEN tblcallevents.id 
                END
            ) AS total_log_in_days,
            ( SELECT tce.appointmentdatetime 
              FROM tblcallevents tce 
              WHERE tce.cid_id = init_call.id 
              ORDER BY tce.id DESC 
              LIMIT 1 ) AS last_task_update,
            DATEDIFF(
                '$edate', 
                MAX(tblcallevents.appointmentdatetime)
            ) AS last_task_days,
            ( SELECT tce.id 
              FROM tblcallevents tce 
              WHERE tce.cid_id = init_call.id 
              ORDER BY tce.id DESC 
              LIMIT 1 ) AS last_task_id,
            ( SELECT CASE 
                        WHEN tce.nextCFID = 0 THEN 'Pending' 
                        ELSE 'Complete' 
                     END
              FROM tblcallevents tce 
              WHERE tce.cid_id = init_call.id 
              ORDER BY tce.id DESC 
              LIMIT 1 ) AS last_task_status
        FROM init_call
        LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
        LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
        LEFT JOIN tblcallevents 
            ON tblcallevents.id = (
                SELECT MAX(tce.id) 
                FROM tblcallevents tce 
                WHERE tce.cid_id = init_call.id
            )
        LEFT JOIN main_review ON main_review.inid = init_call.id
        LEFT JOIN user_details ON user_details.user_id = tblcallevents.user_id
        LEFT JOIN status cur_status ON cur_status.id = init_call.cstatus
        LEFT JOIN status s2 ON s2.id = tblcallevents.status_id
        LEFT JOIN status s3 ON s3.id = tblcallevents.nstatus_id
        LEFT JOIN action a1 
            ON a1.id = (
                SELECT tce.actiontype_id
                FROM tblcallevents tce
                WHERE tce.cid_id = init_call.id
                ORDER BY tce.id DESC
                LIMIT 1
            )
        LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
        LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id AND u2.status = 'active'
        LEFT JOIN user_details u3 ON u3.user_id = init_call.apst AND u3.status = 'active'
        LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id AND u4.status = 'active'
        LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id AND u5.status = 'active'
        LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id AND u6.status = 'active'
        LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id AND u7.status = 'active'
        LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id AND u8.status = 'active'
        LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id AND u9.status = 'active'
        WHERE $text AND $userstatus AND $cuserstatus
        AND init_call.cstatus != ''
        AND init_call.cstatus NOT IN (7,14)
        AND tblcallevents.id IS NOT NULL
        AND main_review.sdate IS NOT NULL
        $mainCompanyFilter
        AND EXISTS (
            SELECT 1 
            FROM tblcallevents sub_tbcl 
            WHERE sub_tbcl.mtype IN ('RP','RPClose','Change RP') 
            AND YEAR(sub_tbcl.appointmentdatetime) = '$year' 
            AND sub_tbcl.cid_id = init_call.id
        )
        GROUP BY init_call.id, init_call.cstatus
    ) AS sub
    WHERE sub.days BETWEEN $firstDays AND $secondDays
    AND sub.last_task_days BETWEEN $firstDays AND $secondDays
    AND sub.last_task_update < '$edate'
");
     $datas = $query->result();
    return $datas;
}
public function GetRemoveCompanyLogs($uid,$sdate,$edate){
 $udata = $this->Menu_model->get_userbyid($uid);
    $utype = $udata[0]->type_id;
    if($utype == 1){
        $text = "AND u1.sadmin_id  = '$uid'";
    }else if($utype == 2){
        $text = "AND u1.admin_id  = '$uid'";
    }else if($utype == 3){
        $text = "AND u1.user_id  = '$uid' ";
    }else if($utype == 13){
        $text = "AND (u1.user_id  = '$uid' || u1.aadmin  = '$uid')";
    }else if($utype == 4){
        $text = "AND (u1.user_id  = '$uid' || u1.pst_co  = '$uid')";
    }else if($utype == 19){
        $text = "AND u1.ash_nae_co='$uid'";
    }else if($utype == 20){
        $text = "AND u1.ash_w_co='$uid'";
    }else if($utype == 21){
        $text = "AND u1.ash_s_co='$uid'";
    }else if($utype == 22){
        $text = "AND (u1.user_id  = '$uid' || u1.rm_east_co='$uid')";
    }else if($utype == 23){
        $text = "AND (u1.user_id  = '$uid' || u1.rm_north_co='$uid')";
    }else if($utype == 24){
        $text = "AND (u1.user_id  = '$uid' || u1.acm_co='$uid')";
    }else if($utype == 15){
        $text = "AND u1.sales_co='$uid'";
    }else{
         $text = "AND u1.user_id  = '$uid' ";
    }
   $activeFilter = "AND u1.status = 'active'";
        $query=$this->db->query("SELECT
	u1.user_id,
    u1.name AS username,
    COUNT(company_master.id) AS total_deleted
FROM
    remove_company_log rcl
    LEFT JOIN tblcallevents ON tblcallevents.id = rcl.task_id
    LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
	LEFT JOIN action ON action.id = tblcallevents.actiontype_id
    LEFT JOIN pending_meetings_request pmr on pmr.id = rcl.request_id
    LEFT JOIN user_details u2 ON u2.user_id = pmr.apr_by
    
WHERE 
    init_call.mainbd = 0
     $text $activeFilter
    AND EXISTS (
        SELECT 1
        FROM tblcallevents sub_tbcl
        WHERE sub_tbcl.cid_id = init_call.id 
          AND sub_tbcl.actiontype_id = 4
    )
    
    AND cast(init_call.createDate as DATE) BETWEEN '$sdate' and '$edate'
    GROUP BY u1.user_id ORDER BY total_deleted DESC");
        return $query->result();
    }
public function GetRemoveCompanyLogslists($uid,$sdate,$edate){
   $text            = "AND u1.user_id  = '$uid' ";
   $activeFilter    = "AND u1.status = 'active'";
    $query=$this->db->query("SELECT
    u1.user_id AS mainbd_user_id,
    company_master.id AS CID,
    company_master.compname AS company_name,
    u1.name AS username,
    init_call.createDate as company_created_date,
    action.name as task_name,
    tblcallevents.appointmentdatetime,
    
    rcl.request_date as delete_request_date,
    pmr.remarks as deleted_request_remarks,
    rcl.reamrks as deleted_remarks,
    u2.name as request_aprroved_by,
    
    pmr.apr_status as request_apr_status,
    pmr.apr_date as request_aprroved_date,
    pmr.apr_remakrs as request_aprroved_remarks,
    pmr.created_at as request_created_at
    
FROM
    remove_company_log rcl
    LEFT JOIN tblcallevents ON tblcallevents.id = rcl.task_id
    LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
	LEFT JOIN action ON action.id = tblcallevents.actiontype_id
    LEFT JOIN pending_meetings_request pmr on pmr.id = rcl.request_id
    LEFT JOIN user_details u2 ON u2.user_id = pmr.apr_by
    
WHERE 
    init_call.mainbd = 0
     $text $activeFilter
    AND EXISTS (
        SELECT 1
        FROM tblcallevents sub_tbcl
        WHERE sub_tbcl.cid_id = init_call.id 
          AND sub_tbcl.actiontype_id = 4
    )
    
    AND cast(init_call.createDate as DATE) BETWEEN '$sdate' and '$edate'");
        return $query->result();
    }



public function GetSingleMeetingExpenseDetails($meetID){

    $query=$this->db->query("SELECT
    cash_expense.*
FROM
    `cash_expense`
    LEFT JOIN barginmeeting on barginmeeting.id = cash_expense.meetid
WHERE
    (
        (verify = 1 AND admin_apr = 1) 
        OR (verify = 0 AND admin_apr = 0) 
        OR (verify = 1 AND admin_apr = 0)
    )
    AND barginmeeting.tid = '$meetID'");

   
        return $query->result();
    }




public function GetAllTravelAdvancedBySingleUid($uid){

    $text       = " u1.user_id = '$uid'";
    $ustatus    = " AND u1.status = 'active'";

    $query=$this->db->query("SELECT u1.name,u1.ucash, ta.user_id, COUNT(*) AS total,COUNT(CASE WHEN ((ta.cluster_apr = '0' AND ta.admin_apr = '0' AND ta.account_apr = '0') || (ta.cluster_apr = '1' AND ta.admin_apr = '0' AND ta.account_apr = '0') || (ta.cluster_apr = '1' AND ta.admin_apr = '1' AND ta.account_apr = '0')) THEN 1 END) AS total_pending,COUNT(CASE WHEN ((ta.cluster_apr = '2' AND ta.admin_apr = '0' AND ta.account_apr = '0') || (ta.cluster_apr = '1' AND ta.admin_apr = '2' AND ta.account_apr = '0') || (ta.cluster_apr = '1' AND ta.admin_apr = '1' AND ta.account_apr = '2')) THEN 1 END) AS total_reject,COUNT(CASE WHEN ((ta.cluster_apr = '1' AND ta.admin_apr = '3' AND ta.account_apr = '0') || (ta.cluster_apr = '1' AND ta.admin_apr = '1' AND ta.account_apr = '3') ) THEN 1 END) AS total_suspect, COUNT( CASE WHEN ta.cluster_apr = '0' THEN 1 END ) AS manager_pending, COUNT( CASE WHEN ta.cluster_apr = '1' THEN 1 END ) AS manager_approved, COUNT( CASE WHEN ta.cluster_apr = '2' THEN 1 END ) AS manager_reject, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '0' THEN 1 END ) AS admin_pending, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1' THEN 1 END ) AS admin_approved, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '2' THEN 1 END ) AS admin_reject, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '3' THEN 1 END ) AS admin_suspected, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1' AND ta.account_apr = '0' THEN 1 END ) AS account_pending, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1' AND ta.account_apr = '1' THEN 1 END ) AS account_approved, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1' AND ta.account_apr = '2' THEN 1 END ) AS account_reject, COUNT( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1' AND ta.account_apr = '3' THEN 1 END ) AS account_suspect, SUM(ta.cash) AS total_cash_request, SUM( CASE WHEN ta.cluster_apr = '1' THEN ta.cash ELSE 0 END ) AS total_approved_cash_form_manager, SUM( CASE WHEN ta.cluster_apr = '2' THEN ta.cash ELSE 0 END ) AS total_reject_cash_form_manager, SUM( CASE WHEN ta.cluster_apr = '3' THEN ta.cash ELSE 0 END ) AS total_suspect_cash_form_manager, SUM( CASE WHEN ta.cluster_apr = '0' THEN ta.cash ELSE 0 END ) AS total_pending_cash_form_manager, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1' THEN ta.cash ELSE 0 END ) AS total_approved_cash_form_admin, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '2' THEN ta.cash ELSE 0 END ) AS total_reject_cash_form_admin, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '3' THEN ta.cash ELSE 0 END ) AS total_suspect_cash_form_admin, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '0' THEN ta.cash ELSE 0 END ) AS total_pending_cash_form_admin, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1'  AND ta.account_apr = '1' THEN ta.cash ELSE 0 END ) AS total_approved_cash_form_account, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1'  AND ta.account_apr = '2' THEN ta.cash ELSE 0 END ) AS total_reject_cash_form_account, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1'  AND ta.account_apr = '3' THEN ta.cash ELSE 0 END ) AS total_suspect_cash_form_account, SUM( CASE WHEN ta.cluster_apr = '1' AND ta.admin_apr = '1'  AND ta.account_apr = '0' THEN ta.cash ELSE 0 END ) AS total_pending_cash_form_account  FROM `travel_advance` ta LEFT JOIN user_details u1 ON u1.user_id = ta.user_id WHERE u1.name IS NOT NULL AND $text GROUP BY ta.user_id");
    return $query->result();
}

public function GetAllCashExpenseBySingleUid($uid){
 

    $text = " u1.user_id = '$uid'";

   $query=$this->db->query("SELECT u1.name,u1.ucash, ce.user_id, COUNT(*) AS total,COUNT( CASE WHEN( ( ce.verify = '0' AND ce.admin_apr = '0' AND ce.account_apr = '0' ) ||( ce.verify = '1' AND ce.admin_apr = '0' AND ce.account_apr = '0' ) ||( ce.verify = '1' AND ce.admin_apr = '1' AND ce.account_apr = '0' ) ) THEN 1 END ) AS total_pending, COUNT( CASE WHEN( ( ce.verify = '2' AND ce.admin_apr = '0' AND ce.account_apr = '0' ) ||( ce.verify = '1' AND ce.admin_apr = '2' AND ce.account_apr = '0' ) ||( ce.verify = '1' AND ce.admin_apr = '1' AND ce.account_apr = '2' ) ) THEN 1 END ) AS total_reject, COUNT( CASE WHEN( ( ce.verify = '1' AND ce.admin_apr = '3' AND ce.account_apr = '0' ) ||( ce.verify = '1' AND ce.admin_apr = '1' AND ce.account_apr = '3' )) THEN 1 END ) AS total_suspect, COUNT( CASE WHEN ce.verify = '0' THEN 1 END ) AS manager_pending, COUNT( CASE WHEN ce.verify = '1' THEN 1 END ) AS manager_approved, COUNT( CASE WHEN ce.verify = '2' THEN 1 END ) AS manager_reject, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '0' ) THEN 1 END ) AS admin_pending, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '1' ) THEN 1 END ) AS admin_approved, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '2' ) THEN 1 END ) AS admin_reject, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '3' ) THEN 1 END ) AS admin_suspected, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '1' AND ce.account_apr = '0' ) THEN 1 END ) AS account_pending, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '1' AND ce.account_apr = '1' ) THEN 1 END ) AS account_approved, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '1' AND ce.account_apr = '2' ) THEN 1 END ) AS account_reject, COUNT( CASE WHEN( ce.verify = '1' AND ce.admin_apr = '1' AND ce.account_apr = '3' ) THEN 1 END ) AS account_suspected,SUM(ce.expense) AS total_cash_expense, SUM( CASE WHEN ce.verify = '1' THEN ce.expense ELSE 0 END ) AS total_approved_cash_form_manager, SUM( CASE WHEN ce.verify = '2' THEN ce.expense ELSE 0 END ) AS total_reject_cash_form_manager, SUM( CASE WHEN ce.verify = '3' THEN ce.expense ELSE 0 END ) AS total_suspect_cash_form_manager, SUM( CASE WHEN ce.verify = '0' THEN ce.expense ELSE 0 END ) AS total_pending_cash_form_manager, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '1' THEN ce.expense ELSE 0 END ) AS total_approved_cash_form_admin, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '2' THEN ce.expense ELSE 0 END ) AS total_reject_cash_form_admin, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '3' THEN ce.expense ELSE 0 END ) AS total_suspect_cash_form_admin, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '0' THEN ce.expense ELSE 0 END ) AS total_pending_cash_form_admin, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '1'  AND ce.account_apr = '1' THEN ce.expense ELSE 0 END ) AS total_approved_cash_form_account, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '1'  AND ce.account_apr = '2' THEN ce.expense ELSE 0 END ) AS total_reject_cash_form_account, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '1'  AND ce.account_apr = '3' THEN ce.expense ELSE 0 END ) AS total_suspect_cash_form_account, SUM( CASE WHEN ce.verify = '1' AND ce.admin_apr = '1'  AND ce.account_apr = '0' THEN ce.expense ELSE 0 END ) AS total_pending_cash_form_account FROM `cash_expense` ce LEFT JOIN user_details u1 ON u1.user_id = ce.user_id WHERE u1.name IS NOT NULL AND $text GROUP BY ce.user_id");
    return $query->result();
}



public function GetAllTravelAdvancedByCodeSingleUser($code,$uid){
   
    $text = "user_details.user_id = '$uid'";
    $textfilter = "AND travel_advance.user_id = '$uid'";
    if($code ==1){
        $filter = '';
    }else if($code ==2){
        $filter = "AND travel_advance.cluster_apr = '1'";
    }else if($code ==3){
        $filter = "AND travel_advance.cluster_apr = '0'";
    }else if($code ==4){
        $filter = "AND travel_advance.cluster_apr = '2'";
    }else if($code ==5){
        $filter = "AND travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1'";
    }else if($code ==6){
        $filter = "AND travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '0'";
    }else if($code ==7){
        $filter = "AND travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '2'";
    }else if($code ==8){
        $filter = "AND travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1' AND travel_advance.account_apr = '1'";
    }else if($code ==9){
        $filter = "AND travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1' AND travel_advance.account_apr = '0'";
    }else if($code ==10){
        $filter = "AND travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1' AND travel_advance.account_apr = '2'";
    }else if($code ==11){
        $filter = "AND travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1' AND travel_advance.account_apr = '3'";
    }else if($code ==12){
        $filter = "AND ((travel_advance.cluster_apr = '0' AND travel_advance.admin_apr = '0' AND travel_advance.account_apr = '0') || (travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '0' AND travel_advance.account_apr = '0') || (travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1' AND travel_advance.account_apr = '0') $textfilter )";
    }else if($code ==13){
        $filter = "AND ((travel_advance.cluster_apr = '2' AND travel_advance.admin_apr = '0' AND travel_advance.account_apr = '0') || (travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '2' AND travel_advance.account_apr = '0') || (travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1' AND travel_advance.account_apr = '2') $textfilter )";
    }else if($code ==14){
        $filter = "AND ((travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '3' AND travel_advance.account_apr = '0') || (travel_advance.cluster_apr = '1' AND travel_advance.admin_apr = '1' AND travel_advance.account_apr = '3') $textfilter )";
    }

    $query=$this->db->query("SELECT
    travel_advance.*,
    user_details.name,
    u1.name as cluster_by_name,
    u2.name as admin_by_name
FROM
    travel_advance
LEFT JOIN user_details ON user_details.user_id = travel_advance.user_id
LEFT JOIN user_details u1 ON u1.user_id = travel_advance.cluster_by
LEFT JOIN user_details u2 ON u2.user_id = travel_advance.admin_by
WHERE
    $text 
    $filter $textfilter
ORDER BY
    travel_advance.id
DESC
    ");
    return $query->result();
}




public function GetTravelExpenseTotalSumBySingleUser($uid){
   
    $query=$this->db->query("SELECT 
    *
FROM cash_expense c1
WHERE 
     c1.user_id = '$uid'
    AND (
        ( c1.verify = 1 AND  c1.admin_apr = 1)
        OR ( c1.verify = 0 AND  c1.admin_apr = 0)
        OR ( c1.verify = 1 AND  c1.admin_apr = 0)
    ) GROUP BY  c1.meetid
    ");
    return $query->result();
}


public function GetLatedExpenseDetails($uid){
   
    $query = $this->db->query("SELECT 
        cm.id as cid,
        cm.compname,
        tce2.id as task_id,
        barginmeeting.storedt,
        a1.name as action_name,
        tce2.appointmentdatetime,
        tce2.mtype,
        u1.name as task_user_name,
        cash_expense.*
        FROM `cash_expense`
        LEFT JOIN barginmeeting on barginmeeting.id = cash_expense.meetid
        LEFT JOIN tblcallevents tce2 ON tce2.id = barginmeeting.tid
        LEFT JOIN init_call on init_call.id = tce2.cid_id
        LEFT JOIN company_master cm on cm.id = init_call.cmpid_id
        LEFT JOIN action a1 ON a1.id = tce2.actiontype_id
        LEFT JOIN user_details u1 on u1.user_id = tce2.user_id
        WHERE 
        cash_expense.user_id = '$uid'
        -- AND (
        --     (cash_expense.verify = 1 AND cash_expense.admin_apr = 1)
        --     OR (cash_expense.verify = 0 AND cash_expense.admin_apr = 0)
        --     OR (cash_expense.verify = 1 AND cash_expense.admin_apr = 0)
        -- )
        ORDER BY cash_expense.id DESC LIMIT 0, 10");
    return $query->result();
}


public function GetLatedAdvacncedDetails($uid){
   
    $query = $this->db->query("SELECT
        u1.name as request_user_name,
        u2.name as request_apr_lm_user_name,
        u3.name as request_apr_admin_user_name,
        travel_advance.*
        FROM `travel_advance`
        LEFT JOIN user_details u1 on u1.user_id = travel_advance.user_id
        LEFT JOIN user_details u2 on u2.user_id = travel_advance.cluster_by
        LEFT JOIN user_details u3 on u3.user_id = travel_advance.admin_by
        WHERE 
        travel_advance.user_id = '$uid'
        -- AND (
        --     (travel_advance.verify = 1 AND travel_advance.admin_apr = 1)
        --     OR (travel_advance.verify = 0 AND travel_advance.admin_apr = 0)
        --     OR (travel_advance.verify = 1 AND travel_advance.admin_apr = 0)
        -- )
        ORDER BY travel_advance.id DESC LIMIT 0, 5");
    return $query->result();
}




public function format_inr_intl($amount, $decimals = 2, $locale = 'en_IN') {
    // Ensure it's numeric
    $amount = (float) preg_replace('/[^\d.\-]/', '', (string)$amount);
    if (class_exists('NumberFormatter')) {
        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        // control fraction digits if needed
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
        return $fmt->formatCurrency($amount, 'INR'); // returns e.g. "₹10,000.00" with Indian grouping
    }
    // fallback
    return '₹' . number_format($amount, $decimals);
}








public function getAllCompanyBYRollesMaiingClosurePipeLine($userid,$admin_id_filter,$rm_filter,$sdate,$edate){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }

       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }

    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];

    $totalClosurePipeLine = $this->db->query("SELECT
    COUNT(DISTINCT init_call.id) AS total_funnel,
    COUNT(DISTINCT tblcm.cid_id) AS total_funnel_complete_rp_meeting,
    COUNT(DISTINCT CASE WHEN tblcm.id IS  NULL THEN init_call.id END) AS total__funnel_pending_for_rp_meeting,

    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND u1.user_id = tblcm.user_id THEN tblcm.cid_id END) AS total_funnel_complete_rp_meeting_by_current_bd,


    -- SUM(CASE WHEN barginmeeting.id IS NOT NULL THEN cash_expense.expense END) AS total_cash_expense,

    -- RP Meeting Call Connection
    COUNT(DISTINCT CASE WHEN tblcarp.id IS NOT NULL THEN tblcm.cid_id END) AS total_call_connected_after_rp_meeting,
    COUNT(DISTINCT CASE WHEN tblcarp.id IS NULL THEN tblcm.cid_id END) AS total_call_not_connected_after_rp_meeting,

    -- RP Meeting Proposal Status
    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NOT NULL THEN init_call.id END) AS total_proposal_sent,
    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NULL THEN init_call.id END) AS total_pending_for_proposal_sent,
    COUNT(DISTINCT CASE WHEN tblcm.id IS NULL AND p1.id IS NOT NULL THEN init_call.id END) AS total_proposal_sent_without_meeting,

    COUNT(DISTINCT CASE WHEN (tblcm.id IS NOT NULL AND u1.user_id != tblcm.user_id) AND (tblcm.user_id NOT IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id))  THEN tblcm.cid_id END) AS total_funnel_complete_rp_meeting_by_other_bd,

    COUNT(DISTINCT CASE WHEN (tblcm.id IS NOT NULL AND u1.user_id != tblcm.user_id) AND (tblcm.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) THEN tblcm.cid_id END) AS total_funnel_complete_rp_meeting_by_line_manager,

    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND u1.user_id = p1.user_id THEN init_call.id END) AS total_proposal_sent_by_current_bd,
    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND u1.user_id != p1.user_id AND (p1.user_id NOT IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) THEN init_call.id END) AS total_proposal_sent_by_other_bd,
    
   --  Closure Status
    COUNT(DISTINCT CASE WHEN (init_call.cstatus IN (7,14)) THEN init_call.id END) AS total_closure,
    COUNT(DISTINCT CASE WHEN (init_call.cstatus IN (7,14) AND p1.id IS NULL)  THEN init_call.id END) AS total_direct_closure_without_proposal_sent,

    -- Proposal Closure Status
    COUNT(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus IN (7,14) THEN init_call.id END) AS total_closure_after_proposal_sent,
    COUNT(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus NOT IN (7,14) THEN init_call.id END) AS total_not_closure_after_proposal_sent,

    COUNT(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus IN (4,5) THEN init_call.id END) AS total_wdl_or_ni_after_proposal_sent,

    -- Budget Sums
    SUM(DISTINCT CASE WHEN p1.id IS NOT NULL THEN p1.pbudgetme END) AS total_budget_where_proposal_sent,
    SUM(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus IN (7,14) THEN p1.pbudgetme END) AS total_closure_budget_where__proposal_sent,
    SUM(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus NOT IN (7,14) THEN p1.pbudgetme END) AS total_not_closure_budget_where__proposal_sent,
    
 /* Call Connection After Proposal */
    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS NOT NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NOT NULL
        THEN init_call.id END
    ) AS total_call_connected_after_proposal_sent,


    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS NOT NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NULL
        THEN init_call.id END
    ) AS total_call_not_connected_after_proposal_sent,
    
     /* Call Connection After Proposal */
    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS  NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NOT NULL
        THEN init_call.id END
    ) AS total_call_connected_after_proposal_sent_without_meeting,

    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NULL
        THEN init_call.id END
    ) AS total_call_not_connected_after_proposal_sent_without_meeting,


    COUNT(DISTINCT CASE WHEN tblcarp.id IS NOT NULL 
    AND (tblcarp.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) 
    AND tblcarp.user_id != u1.user_id
    THEN init_call.id END) AS total_line_manager_call_connected_after_rp_meeting,
    COUNT(
        DISTINCT CASE 
            WHEN 
                tblcm.id IS NOT NULL
                AND p1.id IS NOT NULL
                AND tblcap.user_id IN (
                    u1.aadmin, u1.acm_co, u1.pst_co, 
                    u1.rm_east_co, u1.rm_north_co, 
                    u1.ash_nae_co, u1.ash_w_co, 
                    u1.ash_s_co,u1.admin_id,u1.sadmin_id
                )
                AND tblcap.user_id != u1.user_id
                AND tblcap.id IS NOT NULL
            THEN init_call.id
        END
    ) AS total_line_manager_call_connected_after_proposal_sent,


 COUNT(
    DISTINCT CASE 
        WHEN p1.id IS NOT NULL
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents c2 
                 WHERE c2.cid_id = init_call.id 
                  AND c2.actiontype_id IN (3,4,22)
                  AND c2.nextCFID != 0
                  AND c2.mtype IN ('RP','RPClose','Change RP')
             ) > 1
        THEN init_call.id 
    END
) AS total_proposal_sent_remeeting_done,

 COUNT(
    DISTINCT CASE 
        WHEN p1.id IS NOT NULL
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents c2 
                 WHERE c2.cid_id = init_call.id 
                  AND c2.actiontype_id IN (3,4,22)
                  AND c2.nextCFID != 0
                  AND c2.mtype IN ('RP','RPClose','Change RP')
             ) = 1
        THEN init_call.id 
    END
) AS total_proposal_sent_remeeting_not_done
    
    
FROM init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN tblcallevents tblcm
       ON tblcm.cid_id = init_call.id
       AND tblcm.actiontype_id IN (3,4,22)
       AND tblcm.nextCFID != 0
       AND tblcm.mtype IN ('RP','RPClose','Change RP')
       AND CAST(tblcm.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
LEFT JOIN barginmeeting ON barginmeeting.tid = tblcm.id         
LEFT JOIN tblcallevents tblcarp
       ON tblcarp.cid_id = init_call.id
       AND tblcarp.actiontype_id IN (1)
       AND tblcarp.nextCFID != 0
       AND (tblcarp.actontaken = 'yes' AND tblcarp.purpose_achieved = 'yes')
       AND CAST(tblcarp.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id IN (3,4,22)
                  AND tblcm2.nextCFID != 0
                  AND tblcm2.mtype IN ('RP','RPClose','Change RP')
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'

-- LEFT JOIN proposal p1
--        ON p1.init_id = init_call.id
--        AND p1.apr IN (0,1)
--        AND CAST(p1.sdatet AS DATE) BETWEEN '2025-02-14' AND '2025-11-15'

LEFT JOIN (
   SELECT id, init_id, pbudgetme, user_id
   FROM proposal
   WHERE apr IN (0,1)
     AND CAST(sdatet AS DATE) BETWEEN '$sdate' AND '$edate'
) p1 ON p1.init_id = init_call.id
       
LEFT JOIN tblcallevents tblcap
       ON tblcap.cid_id = init_call.id
       AND tblcap.actiontype_id = 1
       AND tblcap.nextCFID != 0
       AND (tblcap.actontaken = 'yes' AND tblcap.purpose_achieved = 'yes')
    --    AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
     AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            -- LEFT JOIN proposal on proposal.tid = tblcm2.id
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id = 7
                  AND tblcm2.nextCFID != 0
                  -- AND proposal.apr IN (0,1)
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'
       
 LEFT JOIN cash_expense ON cash_expense.meetid = barginmeeting.id
 AND (
       (cash_expense.verify = 1 AND cash_expense.admin_apr = 1)
        OR (cash_expense.verify = 0 AND cash_expense.admin_apr = 0)
        OR (cash_expense.verify = 1 AND cash_expense.admin_apr = 0)
      )
     
WHERE $text
      AND u1.status = 'active'");
    $totalClosurePipeLineDatas = $totalClosurePipeLine->result();



    $totalClosurePipeLineByUser = $this->db->query("SELECT
    u1.user_id,
    u1.name as username,
    u1.user_cluster_zone,
    COUNT(DISTINCT init_call.id) AS total_funnel,
    COUNT(DISTINCT tblcm.cid_id) AS total_funnel_complete_rp_meeting,
    COUNT(DISTINCT CASE WHEN tblcm.id IS  NULL THEN init_call.id END) AS total__funnel_pending_for_rp_meeting,

    -- SUM(CASE WHEN barginmeeting.id IS NOT NULL THEN cash_expense.expense END) AS total_cash_expense,

    -- RP Meeting Call Connection
    COUNT(DISTINCT CASE WHEN tblcarp.id IS NOT NULL THEN tblcm.cid_id END) AS total_call_connected_after_rp_meeting,
    COUNT(DISTINCT CASE WHEN tblcarp.id IS NULL THEN tblcm.cid_id END) AS total_call_not_connected_after_rp_meeting,

    -- RP Meeting Proposal Status
    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NOT NULL THEN init_call.id END) AS total_proposal_sent,
    
    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NULL THEN init_call.id END) AS total_pending_for_proposal_sent,
    
    COUNT(DISTINCT CASE WHEN tblcm.id IS NULL AND p1.id IS NOT NULL THEN init_call.id END) AS total_proposal_sent_without_meeting,
    
    
   --  Closure Status
    COUNT(DISTINCT CASE WHEN (init_call.cstatus IN (7,14)) THEN init_call.id END) AS total_closure,
    COUNT(DISTINCT CASE WHEN (init_call.cstatus IN (7,14) AND p1.id IS NULL)  THEN init_call.id END) AS total_direct_closure_without_proposal_sent,

    -- Proposal Closure Status
    COUNT(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus IN (7,14) THEN init_call.id END) AS total_closure_after_proposal_sent,
    COUNT(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus NOT IN (7,14) THEN init_call.id END) AS total_not_closure_after_proposal_sent,

    -- Budget Sums
    SUM(DISTINCT CASE WHEN p1.id IS NOT NULL THEN p1.pbudgetme END) AS total_budget_where_proposal_sent,
    SUM(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus IN (7,14) THEN p1.pbudgetme END) AS total_closure_budget_where__proposal_sent,
    SUM(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus NOT IN (7,14) THEN p1.pbudgetme END) AS total_not_closure_budget_where__proposal_sent,

    COUNT(DISTINCT CASE WHEN p1.id IS NOT NULL AND init_call.cstatus IN (4,5) THEN init_call.id END) AS total_wdl_or_ni_after_proposal_sent,

    COUNT(DISTINCT CASE WHEN tblcarp.id IS NOT NULL 
    AND (tblcarp.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) 
    AND tblcarp.user_id != u1.user_id
    THEN init_call.id END) AS total_line_manager_call_connected_after_rp_meeting,

    COUNT(DISTINCT CASE WHEN (tblcm.id IS NOT NULL AND u1.user_id != tblcm.user_id) AND (tblcm.user_id NOT IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id))  THEN tblcm.cid_id END) AS total_funnel_complete_rp_meeting_by_other_bd,

    COUNT(DISTINCT CASE WHEN (tblcm.id IS NOT NULL AND u1.user_id != tblcm.user_id) AND (tblcm.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) THEN tblcm.cid_id END) AS total_funnel_complete_rp_meeting_by_line_manager,

    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND u1.user_id = p1.user_id THEN init_call.id END) AS total_proposal_sent_by_current_bd,
    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND u1.user_id != p1.user_id AND (p1.user_id NOT IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) THEN init_call.id END) AS total_proposal_sent_by_other_bd,

    COUNT(
        DISTINCT CASE 
            WHEN 
                tblcm.id IS NOT NULL
                AND p1.id IS NOT NULL
                AND tblcap.user_id IN (
                    u1.aadmin, u1.acm_co, u1.pst_co, 
                    u1.rm_east_co, u1.rm_north_co, 
                    u1.ash_nae_co, u1.ash_w_co, u1.ash_s_co,u1.admin_id,u1.sadmin_id
                )
                AND tblcap.user_id != u1.user_id
                AND tblcap.id IS NOT NULL
            THEN init_call.id 
        END
    ) AS total_line_manager_call_connected_after_proposal_sent,
    
 /* Call Connection After Proposal */
    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS NOT NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NOT NULL
        THEN init_call.id END
    ) AS total_call_connected_after_proposal_sent,

    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS NOT NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NULL
        THEN init_call.id END
    ) AS total_call_not_connected_after_proposal_sent,
    
     /* Call Connection After Proposal */
    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS  NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NOT NULL
        THEN init_call.id END
    ) AS total_call_connected_after_proposal_sent_without_meeting,

    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NULL
        THEN init_call.id END
    ) AS total_call_not_connected_after_proposal_sent_without_meeting,


COUNT(
    DISTINCT CASE 
        WHEN p1.id IS NOT NULL
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents c2 
                 WHERE c2.cid_id = init_call.id 
                  AND c2.actiontype_id IN (3,4,22)
                  AND c2.nextCFID != 0
                  AND c2.mtype IN ('RP','RPClose','Change RP')
             ) > 1
        THEN init_call.id 
    END
) AS total_proposal_sent_remeeting_done,

 COUNT(
    DISTINCT CASE 
        WHEN p1.id IS NOT NULL
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents c2 
                 WHERE c2.cid_id = init_call.id 
                  AND c2.actiontype_id IN (3,4,22)
                  AND c2.nextCFID != 0
                  AND c2.mtype IN ('RP','RPClose','Change RP')
             ) = 1
        THEN init_call.id 
    END
) AS total_proposal_sent_remeeting_not_done
    
    
    

FROM init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN tblcallevents tblcm
       ON tblcm.cid_id = init_call.id
       AND tblcm.actiontype_id IN (3,4,22)
       AND tblcm.nextCFID != 0
       AND tblcm.mtype IN ('RP','RPClose','Change RP')
       AND CAST(tblcm.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
LEFT JOIN barginmeeting ON barginmeeting.tid = tblcm.id         
LEFT JOIN tblcallevents tblcarp
       ON tblcarp.cid_id = init_call.id
       AND tblcarp.actiontype_id IN (1)
       AND tblcarp.nextCFID != 0
       AND (tblcarp.actontaken = 'yes' AND tblcarp.purpose_achieved = 'yes')
       AND CAST(tblcarp.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id IN (3,4,22)
                  AND tblcm2.nextCFID != 0
                  AND tblcm2.mtype IN ('RP','RPClose','Change RP')
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'
           
-- LEFT JOIN proposal p1
--        ON p1.init_id = init_call.id
--        AND p1.apr IN (0,1)
--        AND CAST(p1.sdatet AS DATE) BETWEEN '2025-02-14' AND '2025-11-15'

LEFT JOIN (
   SELECT id, init_id, pbudgetme, user_id
   FROM proposal
   WHERE apr IN (0,1)
     AND CAST(sdatet AS DATE) BETWEEN '$sdate' AND '$edate'
) p1 ON p1.init_id = init_call.id
       
LEFT JOIN tblcallevents tblcap
       ON tblcap.cid_id = init_call.id
       AND tblcap.actiontype_id = 1
       AND tblcap.nextCFID != 0
       AND (tblcap.actontaken = 'yes' AND tblcap.purpose_achieved = 'yes')
    --    AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
     AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id = 7
                  AND tblcm2.nextCFID != 0
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'
       
 LEFT JOIN cash_expense ON cash_expense.meetid = barginmeeting.id
 AND (
       (cash_expense.verify = 1 AND cash_expense.admin_apr = 1)
        OR (cash_expense.verify = 0 AND cash_expense.admin_apr = 0)
        OR (cash_expense.verify = 1 AND cash_expense.admin_apr = 0)
      )
     
WHERE $text
      AND u1.status = 'active' GROUP BY u1.user_id ");
    $totalClosurePipeLineDatasByuser = $totalClosurePipeLineByUser->result();

    $result = [
        'totalClosurePipeLineDatas'         => $totalClosurePipeLineDatas,
        'totalClosurePipeLineDatasByuser'   => $totalClosurePipeLineDatasByuser
    ];
    return $result;
}






public function getAllCompanyBYRollesMaiingClosurePipeLineDetails($userid,$ftype,$sdate,$edate,$userwise){

    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;

    if($userwise !== 'userwise'){
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }

}else{
    $text = "u1.user_id = $userid";
}

    if($type_id == 25){
        $mainBDFilterQuery = 'init_call.creator_id';
    }else{
        $mainBDFilterQuery = 'init_call.mainbd';
    }


    $sqlFilter = "";
    $GroupSqlFilter = "";

    if($ftype == 'total_funnel_complete_rp_meeting'){
        $sqlFilter = " AND tblcm.id IS NOT NULL";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_funnel_complete_rp_meeting_by_current_bd'){
        $sqlFilter = " AND tblcm.id IS NOT NULL AND u1.user_id = tblcm.user_id";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_funnel_complete_rp_meeting_by_other_bd'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND u1.user_id != tblcm.user_id) AND (tblcm.user_id NOT IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_funnel_complete_rp_meeting_by_line_manager'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND u1.user_id != tblcm.user_id) AND (tblcm.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) ";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_funnel_pending_for_rp_meeting'){
        $sqlFilter = " AND tblcm.id IS NULL";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_call_connected_after_rp_meeting'){
        $sqlFilter = " AND tblcarp.id IS NOT NULL AND tblcm.id IS NOT NULL";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_line_manager_call_connected_after_rp_meeting'){
        $sqlFilter = " AND tblcm.id IS NOT NULL 
        AND (tblcarp.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id))
        AND tblcarp.user_id != u1.user_id
        ";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }

    else if($ftype == 'total_call_not_connected_after_rp_meeting'){
        $sqlFilter = " AND (tblcarp.id IS NULL AND tblcm.id IS NOT NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_proposal_sent_remeeting_done'){
        $sqlFilter = " AND p1.id IS NOT NULL
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents c2 
                 WHERE c2.cid_id = init_call.id 
                  AND c2.actiontype_id IN (3,4,22)
                  AND c2.nextCFID != 0
                  AND c2.mtype IN ('RP','RPClose','Change RP')
             ) > 1";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_proposal_sent_remeeting_not_done'){
        $sqlFilter = " AND p1.id IS NOT NULL
             AND (
                 SELECT COUNT(*) 
                 FROM tblcallevents c2 
                 WHERE c2.cid_id = init_call.id 
                  AND c2.actiontype_id IN (3,4,22)
                  AND c2.nextCFID != 0
                  AND c2.mtype IN ('RP','RPClose','Change RP')
             ) = 1";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_proposal_sent_by_other_bd'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL) AND u1.user_id != p1.user_id AND (p1.user_id NOT IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_proposal_sent_by_current_bd'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL) AND u1.user_id = p1.user_id";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_pending_for_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_proposal_sent_without_meeting'){
        $sqlFilter = " AND (tblcm.id IS NULL AND p1.id IS NOT NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_closure_after_proposal_sent'){
        $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus IN (7,14))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_wdl_or_ni_after_proposal_sent'){
        $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus IN (4,5))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_not_closure_after_proposal_sent'){
        $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus NOT IN (7,14))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_direct_closure_without_proposal_sent'){
        $sqlFilter = " AND (p1.id IS NULL AND init_call.cstatus IN (7,14))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_budget_where_proposal_sent'){
        $sqlFilter = " AND (p1.id IS NOT NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_closure_budget_where_proposal_sent'){
        $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus IN (7,14))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_closure'){
        $sqlFilter = " AND (init_call.cstatus IN (7,14))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_not_closure_budget_where_proposal_sent'){
        $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus NOT IN (7,14))";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_call_connected_after_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND tblcap.id IS NOT NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }

    else if($ftype == 'total_line_manager_call_connected_after_proposal_sent'){
        $sqlFilter = "  AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND tblcap.id IS NOT NULL) 
                        AND tblcap.user_id != u1.user_id
                        AND tblcap.user_id IN (
                        u1.aadmin, u1.acm_co, u1.pst_co, 
                        u1.rm_east_co, u1.rm_north_co, 
                        u1.ash_nae_co, u1.ash_w_co, u1.ash_s_co,u1.admin_id,u1.sadmin_id
                )";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_call_not_connected_after_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND tblcap.id IS NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_call_connected_after_proposal_sent_without_meeting'){
        $sqlFilter = " AND (tblcm.id IS NULL AND p1.id IS NOT NULL AND tblcap.id IS NOT NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }
    else if($ftype == 'total_call_not_connected_after_proposal_sent_without_meeting'){
        $sqlFilter = " AND (tblcm.id IS NULL AND p1.id IS NOT NULL AND tblcap.id IS NULL)";
        $GroupSqlFilter = " GROUP BY init_call.id";
    }

    $totalClosurePipeLineByUser = $this->db->query("SELECT
    u1.user_id,
    u1.name as main_bd_name,
    company_master.compname,
    init_call.in_quarter,
    company_master.id AS cid,
    status.name as current_status_name,
    u2.name as cluster_manager_name,
    SUM(DISTINCT CASE WHEN p1.id IS NOT NULL THEN p1.pbudgetme END) AS proposal_budgetme,
    COUNT(DISTINCT CASE WHEN tblcm.id IS NOT NULL THEN tblcm.id END) AS total_rp_meeting,
    COUNT(DISTINCT CASE WHEN p1.id IS NOT NULL THEN p1.id END) AS total_proposal_sent,

    COUNT(DISTINCT CASE WHEN tblcarp.id IS NOT NULL THEN tblcarp.id END) AS total_call_connected_after_rp_meeting,

    COUNT(DISTINCT CASE WHEN tblcarp.id IS NOT NULL AND (tblcarp.actontaken = 'yes' || tblcarp.purpose_achieved = 'yes') THEN tblcarp.id END) AS total_AY_PY_call_connected_after_rp_meeting,

    COUNT(DISTINCT CASE
        -- Positive Conversion conditions
        WHEN (s3.name = 'Open' AND s4.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN tblcarp.id
        WHEN (s3.name = 'Open RPEM' AND s4.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN tblcarp.id
        WHEN (s3.name = 'Reachout' AND s4.name IN ('Tentative','Positive', 'Closure')) THEN tblcarp.id
        WHEN (s3.name = 'Tentative' AND s4.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN tblcarp.id
        WHEN (s3.name = 'Positive-NAP' AND s4.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN tblcarp.id
        WHEN (s3.name = 'Positive' AND s4.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN tblcarp.id
        WHEN (s3.name = 'Very Positive-NAP' AND s4.name IN ('Very Positive', 'Closure')) THEN tblcarp.id
        WHEN (s3.name = 'Very Positive' AND s4.name = 'Closure') THEN tblcarp.id
        WHEN (s3.name = 'Will do Later' AND s4.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN tblcarp.id
        WHEN (s3.name = 'Not Interested' AND s4.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN tblcarp.id
        WHEN (s3.name = 'OPEN RPEM' AND s4.name = 'On-Boarded') THEN tblcarp.id
        WHEN (s3.name = 'On-Boarded' AND s4.name = 'Closure') THEN tblcarp.id
        ELSE NULL
    END) AS total_positive_conversions_call_after_rp_meeting,

    COUNT(DISTINCT CASE
        -- Negative Conversion conditions
        WHEN (s3.name = 'Will do Later' AND s4.name = 'Open') THEN tblcarp.id
        WHEN (s3.name = 'Not Interested' AND s4.name = 'Open') THEN tblcarp.id
        WHEN (s3.name = 'TTD-Reachout' AND s4.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN tblcarp.id
        WHEN (s3.name = 'Open RPEM' AND s4.name = 'Open') THEN tblcarp.id
        WHEN (s3.name = 'Reachout' AND s4.name IN ('Open RPEM', 'Will do Later', 'Not Interested', 'Open')) THEN tblcarp.id
        WHEN (s3.name = 'Positive-NAP' AND s4.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN tblcarp.id
        WHEN (s3.name = 'Positive' AND s4.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN tblcarp.id
        WHEN (s3.name = 'Very Positive-NAP' AND s4.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN tblcarp.id
        WHEN (s3.name = 'Very Positive' AND s4.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN tblcarp.id
        WHEN (s3.name = 'Closure' AND s4.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN tblcarp.id
        WHEN (s3.name = 'Tentative' AND s4.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN tblcarp.id
        WHEN (s3.name = 'WNO-Reachout' AND s4.name = 'Reachout') THEN tblcarp.id
        ELSE NULL
    END) AS total_negative_conversions_call_after_rp_meeting,

    COUNT(DISTINCT CASE WHEN 
    tblcarp.id IS NOT NULL 
    AND tblcarp.user_id != u1.user_id
    AND (tblcarp.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) 
    THEN tblcarp.id END) AS total_line_manager_call_connected_after_rp_meeting,

    COUNT(DISTINCT CASE 
        WHEN 
             (tblcm.id IS NOT NULL AND p1.id IS NOT NULL) 
             AND tblcap.id IS NOT NULL
        THEN tblcap.id END
    ) AS total_call_connected_after_proposal_sent,

    COUNT(DISTINCT CASE 
        WHEN 
             (tblcm.id IS NOT NULL AND p1.id IS NOT NULL) 
             AND tblcap.id IS NOT NULL
             AND (tblcap.actontaken = 'yes' || tblcap.purpose_achieved = 'yes')
        THEN tblcap.id END
    ) AS total_AY_PY_call_connected_after_proposal_sent,


    COUNT(DISTINCT CASE
        -- Positive Conversion conditions
        WHEN (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) THEN tblcap.id
        WHEN (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) THEN tblcap.id
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Closure')) THEN tblcap.id
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) THEN tblcap.id
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) THEN tblcap.id
        WHEN (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) THEN tblcap.id
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) THEN tblcap.id
        WHEN (s1.name = 'Very Positive' AND s2.name = 'Closure') THEN tblcap.id
        WHEN (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) THEN tblcap.id
        WHEN (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN tblcap.id
        WHEN (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') THEN tblcap.id
        WHEN (s1.name = 'On-Boarded' AND s2.name = 'Closure') THEN tblcap.id
        ELSE NULL
    END) AS total_positive_conversions_call_after_proposal_sent,

    COUNT(DISTINCT CASE
        -- Negative Conversion conditions
        WHEN (s1.name = 'Will do Later' AND s2.name = 'Open') THEN tblcap.id
        WHEN (s1.name = 'Not Interested' AND s2.name = 'Open') THEN tblcap.id
        WHEN (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) THEN tblcap.id
        WHEN (s1.name = 'Open RPEM' AND s2.name = 'Open') THEN tblcap.id
        WHEN (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Will do Later', 'Not Interested', 'Open')) THEN tblcap.id
        WHEN (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) THEN tblcap.id
        WHEN (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) THEN tblcap.id
        WHEN (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) THEN tblcap.id
        WHEN (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) THEN tblcap.id
        WHEN (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) THEN tblcap.id
        WHEN (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) THEN tblcap.id
        WHEN (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout') THEN tblcap.id
        ELSE NULL
    END) AS total_negative_conversions_call_after_proposal_sent,

    COUNT(
        DISTINCT CASE 
            WHEN 
                tblcm.id IS NOT NULL
                AND p1.id IS NOT NULL
                AND tblcap.user_id != u1.user_id
                AND tblcap.user_id IN (
                    u1.aadmin, u1.acm_co, u1.pst_co, 
                    u1.rm_east_co, u1.rm_north_co, 
                    u1.ash_nae_co, u1.ash_w_co, u1.ash_s_co,u1.admin_id,u1.sadmin_id
                )
                AND tblcap.id IS NOT NULL
            THEN tblcap.id 
        END
    ) AS total_line_manager_call_connected_after_proposal_sent,

    COUNT(DISTINCT CASE 
        WHEN 
             tblcm.id IS  NULL AND p1.id IS NOT NULL 
             AND tblcap.id IS NOT NULL
        THEN tblcap.id END
    ) AS total_call_connected_after_proposal_sent_without_meeting,

    DATEDIFF(
    CURDATE(),
    (
        SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
        FROM tblcallevents tblcm2
        WHERE tblcm2.cid_id = init_call.id
              AND tblcm2.actiontype_id IN (3,4,22)
              AND tblcm2.nextCFID != 0
              AND tblcm2.mtype IN ('RP','RPClose','Change RP')
              AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    )
) AS after_rp_meeting_days,

DATEDIFF(
    CURDATE(),
    (
        SELECT MIN(CAST(sdatet AS DATE))
        FROM proposal
        WHERE proposal.init_id = init_call.id
          AND apr IN (0,1)
          AND CAST(sdatet AS DATE) BETWEEN '$sdate' AND '$edate'
    )
) AS after_proposal_sent_days,

DATEDIFF(
    CURDATE(),
    (
        SELECT MAX(CAST(appointmentdatetime AS DATE))
        FROM tblcallevents as lasttbl
        WHERE lasttbl.cid_id = init_call.id
          AND lasttbl.nextCFID != 0
          AND CAST(lasttbl.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
    )
) AS number_Of_days_last_activity 

    
FROM init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id

LEFT JOIN tblcallevents tblcm
       ON tblcm.cid_id = init_call.id
       AND tblcm.actiontype_id IN (3,4,22)
       AND tblcm.nextCFID != 0
       AND tblcm.mtype IN ('RP','RPClose','Change RP')
       AND CAST(tblcm.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
LEFT JOIN barginmeeting ON barginmeeting.tid = tblcm.id         
LEFT JOIN tblcallevents tblcarp
       ON tblcarp.cid_id = init_call.id
       AND tblcarp.actiontype_id IN (1)
       AND tblcarp.nextCFID != 0
       AND (tblcarp.actontaken = 'yes' AND tblcarp.purpose_achieved = 'yes')
       AND CAST(tblcarp.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id IN (3,4,22)
                  AND tblcm2.nextCFID != 0
                  AND tblcm2.mtype IN ('RP','RPClose','Change RP')
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'

LEFT JOIN status s3 ON s3.id = tblcarp.status_id
LEFT JOIN status s4 ON s4.id = tblcarp.nstatus_id
           
-- LEFT JOIN proposal p1
--        ON p1.init_id = init_call.id
--        AND p1.apr IN (0,1)
--        AND CAST(p1.sdatet AS DATE) BETWEEN '2025-02-14' AND '2025-11-15'
LEFT JOIN (
   SELECT id, init_id, pbudgetme, sdatet, user_id
   FROM proposal
   WHERE apr IN (0,1)
     AND CAST(sdatet AS DATE) BETWEEN '$sdate' AND '$edate'
) p1 ON p1.init_id = init_call.id
       
LEFT JOIN tblcallevents tblcap
       ON tblcap.cid_id = init_call.id
       AND tblcap.actiontype_id = 1
       AND tblcap.nextCFID != 0
       AND (tblcap.actontaken = 'yes' AND tblcap.purpose_achieved = 'yes')
    --    AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
     AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id = 7
                  AND tblcm2.nextCFID != 0
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'

LEFT JOIN status s1 ON s1.id = tblcap.status_id
LEFT JOIN status s2 ON s2.id = tblcap.nstatus_id
       
 LEFT JOIN cash_expense ON cash_expense.meetid = barginmeeting.id
 AND (
       (cash_expense.verify = 1 AND cash_expense.admin_apr = 1)
        OR (cash_expense.verify = 0 AND cash_expense.admin_apr = 0)
        OR (cash_expense.verify = 1 AND cash_expense.admin_apr = 0)
      )
     
WHERE $text
      AND u1.status = 'active' 
      $sqlFilter
      $GroupSqlFilter 
      ");
    $totalClosurePipeLineDatasByuser = $totalClosurePipeLineByUser->result();


    
    return $totalClosurePipeLineDatasByuser;
}

public function getAllCompanyBYRollesMaiingClosurePipeLineDetailsBYCID($cid,$ftype,$sdate,$edate){

    if($type_id == 25){
        $mainBDFilterQuery = 'init_call.creator_id';
    }else{
        $mainBDFilterQuery = 'init_call.mainbd';
    }

    $sqlFilter = "";
    $groupSqlFilter = "";

    if($ftype == 'total_rp_meeting'){
        $sqlFilter = " AND tblcm.id IS NOT NULL";
        $groupSqlFilter = " GROUP BY tblcm.id";
    }
    else if($ftype == 'total_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL)";
        $groupSqlFilter = " GROUP BY p1.id";
    }
    else if($ftype == 'total_call_connected_after_rp_meeting'){
        $sqlFilter = " AND (tblcarp.id IS NOT NULL )";
        $groupSqlFilter = " GROUP BY tblcarp.id";
    }
    else if($ftype == 'total_AY_PY_call_connected_after_rp_meeting'){
        $sqlFilter = " AND (tblcarp.id IS NOT NULL AND (tblcarp.actontaken = 'yes' || tblcarp.purpose_achieved = 'yes'))";
        $groupSqlFilter = " GROUP BY tblcarp.id";
    }

    else if($ftype == 'total_positive_conversions_call_after_rp_meeting'){
        $sqlFilter = "  AND (tblcarp.id IS NOT NULL ) 
                        AND (
                                (s3.name = 'Open' AND s4.name IN ('Reachout', 'Open RPEM', 'Tentative')) OR
                                (s3.name = 'Open RPEM' AND s4.name IN ('Reachout','Positive', 'Tentative', 'Closure')) OR
                                (s3.name = 'Reachout' AND s4.name IN ('Tentative','Positive', 'Closure')) OR
                                (s3.name = 'Tentative' AND s4.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) OR
                                (s3.name = 'Positive-NAP' AND s4.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) OR
                                (s3.name = 'Positive' AND s4.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) OR
                                (s3.name = 'Very Positive-NAP' AND s4.name IN ('Very Positive', 'Closure')) OR
                                (s3.name = 'Very Positive' AND s4.name = 'Closure') OR
                                (s3.name = 'Will do Later' AND s4.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) OR
                                (s3.name = 'Not Interested' AND s4.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
                                (s3.name = 'OPEN RPEM' AND s4.name = 'On-Boarded') OR
                                (s3.name = 'On-Boarded' AND s4.name = 'Closure')
                            )";
        $groupSqlFilter = " GROUP BY tblcarp.id";
    }

    else if($ftype == 'total_negative_conversions_call_after_rp_meeting'){
        $sqlFilter = "  AND (tblcarp.id IS NOT NULL ) 
                        AND (
                                (s3.name = 'Will do Later' AND s4.name = 'Open') OR
                                (s3.name = 'Not Interested' AND s4.name = 'Open') OR
                                (s3.name = 'TTD-Reachout' AND s4.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
                                (s3.name = 'Open RPEM' AND s4.name = 'Open') OR
                                (s3.name = 'Reachout' AND s4.name IN ('Open RPEM', 'Will do Later', 'Not Interested', 'Open')) OR
                                (s3.name = 'Positive-NAP' AND s4.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) OR
                                (s3.name = 'Positive' AND s4.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) OR
                                (s3.name = 'Very Positive-NAP' AND s4.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) OR
                                (s3.name = 'Very Positive' AND s4.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) OR
                                (s3.name = 'Closure' AND s4.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) OR
                                (s3.name = 'Tentative' AND s4.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) OR
                                (s3.name = 'WNO-Reachout' AND s4.name = 'Reachout')
                            )";
        $groupSqlFilter = " GROUP BY tblcarp.id";
    }

    else if($ftype == 'total_positive_conversions_call_after_proposal_sent'){
        $sqlFilter = "  AND (tblcap.id IS NOT NULL ) 
                        AND (
                                (s1.name = 'Open' AND s2.name IN ('Reachout', 'Open RPEM', 'Tentative')) OR
                                (s1.name = 'Open RPEM' AND s2.name IN ('Reachout','Positive', 'Tentative', 'Closure')) OR
                                (s1.name = 'Reachout' AND s2.name IN ('Tentative','Positive', 'Closure')) OR
                                (s1.name = 'Tentative' AND s2.name IN ('Positive-NAP', 'Closure', 'Positive', 'TTD-Reachout', 'WNO-Reachout')) OR
                                (s1.name = 'Positive-NAP' AND s2.name IN ('Positive', 'Closure', 'Very Positive-NAP', 'Very Positive')) OR
                                (s1.name = 'Positive' AND s2.name IN ('Very Positive-NAP', 'Closure', 'Positive-NAP', 'Very Positive')) OR
                                (s1.name = 'Very Positive-NAP' AND s2.name IN ('Very Positive', 'Closure')) OR
                                (s1.name = 'Very Positive' AND s2.name = 'Closure') OR
                                (s1.name = 'Will do Later' AND s2.name IN ('Reachout', 'Tentative', 'OPEN RPEM', 'Closure')) OR
                                (s1.name = 'Not Interested' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
                                (s1.name = 'OPEN RPEM' AND s2.name = 'On-Boarded') OR
                                (s1.name = 'On-Boarded' AND s2.name = 'Closure')
                            )";
        $groupSqlFilter = " GROUP BY tblcap.id";
    }
    else if($ftype == 'total_negative_conversions_call_after_proposal_sent'){
        $sqlFilter = "  AND (tblcap.id IS NOT NULL ) 
                        AND (
                                (s1.name = 'Will do Later' AND s2.name = 'Open') OR
                                (s1.name = 'Not Interested' AND s2.name = 'Open') OR
                                (s1.name = 'TTD-Reachout' AND s2.name IN ('Tentative', 'Reachout', 'OPEN RPEM')) OR
                                (s1.name = 'Open RPEM' AND s2.name = 'Open') OR
                                (s1.name = 'Reachout' AND s2.name IN ('Open RPEM', 'Will do Later', 'Not Interested', 'Open')) OR
                                (s1.name = 'Positive-NAP' AND s2.name IN ('Tentative', 'Will do Later', 'WNO Reachout', 'Open')) OR
                                (s1.name = 'Positive' AND s2.name IN ('Will do Later', 'Tentative', 'WNO Reachout', 'TTD Reachout', 'Not Interested', 'Open')) OR
                                (s1.name = 'Very Positive-NAP' AND s2.name IN ('Positive', 'Will do Later', 'WNO Reachout', 'TTD Reachout')) OR
                                (s1.name = 'Very Positive' AND s2.name IN ('Will do Later', 'WNO Reachout', 'TTD Reachout', 'Positive')) OR
                                (s1.name = 'Closure' AND s2.name IN ('Very Positive', 'Will do Later', 'WNO Reachout', 'Tentative', 'Positive')) OR
                                (s1.name = 'Tentative' AND s2.name IN ('Will do Later', 'Not Interested', 'WNO Reachout', 'Open RPEM', 'Reachout', 'Open')) OR
                                (s1.name = 'WNO-Reachout' AND s2.name = 'Reachout')
                            )";
        $groupSqlFilter = " GROUP BY tblcap.id";
    }
    else if($ftype == 'total_line_manager_call_connected_after_rp_meeting'){
        $sqlFilter = " AND (tblcarp.id IS NOT NULL AND tblcarp.user_id != u1.user_id AND (tblcarp.user_id IN (u1.aadmin,u1.acm_co,u1.pst_co,u1.rm_east_co,u1.rm_north_co,u1.ash_nae_co,u1.ash_w_co,u1.ash_s_co,u1.admin_id,u1.sadmin_id)) )";
        $groupSqlFilter = " GROUP BY tblcarp.id";
    }
    // else if($ftype == 'total_proposal_sent_without_meeting'){
    //     $sqlFilter = " AND (tblcm.id IS NULL AND p1.id IS NOT NULL)";
    //     $groupSqlFilter = " GROUP BY init_call.id";
    // }
    // else if($ftype == 'total_closure_after_proposal_sent'){
    //     $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus IN (7,14))";
    //     $groupSqlFilter = " GROUP BY init_call.id";
    // }
    // else if($ftype == 'total_direct_closure_without_proposal_sent'){
    //     $sqlFilter = " AND (p1.id IS NULL AND init_call.cstatus IN (7,14))";
    //     $groupSqlFilter = " GROUP BY init_call.id";
    // }
    // else if($ftype == 'total_budget_where_proposal_sent'){
    //     $sqlFilter = " AND (p1.id IS NOT NULL)";
    //     $groupSqlFilter = " GROUP BY init_call.id";
    // }
    // else if($ftype == 'total_closure_budget_where_proposal_sent'){
    //     $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus IN (7,14))";
    //     $groupSqlFilter = " GROUP BY init_call.id";
    // }
    // else if($ftype == 'total_not_closure_budget_where_proposal_sent'){
    //     $sqlFilter = " AND (p1.id IS NOT NULL AND init_call.cstatus NOT IN (7,14))";
    //     $groupSqlFilter = " GROUP BY init_call.id";
    // }
    else if($ftype == 'total_call_connected_after_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND tblcap.id IS NOT NULL)";
        $groupSqlFilter = " GROUP BY tblcap.id";
    }
    else if($ftype == 'total_AY_PY_call_connected_after_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND tblcap.id IS NOT NULL) AND (tblcap.actontaken = 'yes' || tblcap.purpose_achieved = 'yes')";
        $groupSqlFilter = " GROUP BY tblcap.id";
    }

    else if($ftype == 'total_line_manager_call_connected_after_proposal_sent'){
        $sqlFilter = " AND (tblcm.id IS NOT NULL AND p1.id IS NOT NULL AND tblcap.id IS NOT NULL) AND tblcap.user_id IN ( u1.aadmin, u1.acm_co, u1.pst_co, u1.rm_east_co, u1.rm_north_co, u1.ash_nae_co, u1.ash_w_co, u1.ash_s_co,u1.admin_id,u1.sadmin_id ) AND tblcap.user_id != u1.user_id";
        $groupSqlFilter = " GROUP BY tblcap.id";
    }
    
    
    $totalClosurePipeLineByUser = $this->db->query("SELECT
    u1.user_id,
    u1.name as main_bd_name,
    company_master.compname,
    company_master.id AS cid,
    status.name as current_status_name,
    u2.name as cluster_manager_name,
    tblcm.id as task_id,
    tblcm.appointmentdatetime,
    u3.name as meeting_done_by,
    tblcm.remarks,
    tblcm.mom,
    tblcm.mtype,
    a1.name as meeting_task_name,
    p1.pbudgetme as proposal_budgetme,
    p1.sdatet as proposal_date,
    p1.ptask_id as proposal_task_id,
    p1.proposal_id as proposal_sid,
    u4.name as proposal_by,
    u5.name as call_by_after_rp,

    s3.name as call_by_after_rp_on_status,
    s4.name as call_by_after_rp_new_status,

    s1.name as call_by_after_p_on_status,
    s2.name as call_by_after_p_new_status,

    tblcarp.id as call_by_after_rp_task_id,
    tblcarp.appointmentdatetime as call_by_after_rp_date,
    tblcarp.remarks as call_by_after_rp_remarks,
    tblcarp.actontaken as call_by_after_rp_actontaken,
    tblcarp.purpose_achieved as call_by_after_rp_purpose_achieved,
    tblcarp.special_remarks as call_by_after_rp_special_remarks,
    a3.name as call_by_after_rp_call,

    u6.name as call_by_after_rp_proposal,
    tblcap.appointmentdatetime as call_by_after_rp_pdate,
    tblcap.remarks as call_by_after_rp_premarks,
    tblcap.actontaken as call_by_after_rp_pactontaken,
    tblcap.purpose_achieved as call_by_after_rp_ppurpose_achieved,
    tblcap.id as call_by_after_rp_ptask_id,
    tblcap.special_remarks as call_by_after_rp_pspecial_remarks
    

FROM init_call
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status ON status.id = init_call.cstatus
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id

LEFT JOIN tblcallevents tblcm
       ON tblcm.cid_id = init_call.id
       AND tblcm.actiontype_id IN (3,4,22)
       AND tblcm.nextCFID != 0
       AND tblcm.mtype IN ('RP','RPClose','Change RP')
       AND CAST(tblcm.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
LEFT JOIN barginmeeting ON barginmeeting.tid = tblcm.id 
LEFT JOIN user_details u3 ON u3.user_id = tblcm.user_id        
LEFT JOIN action a1 ON a1.id = tblcm.actiontype_id   

LEFT JOIN tblcallevents tblcarp
       ON tblcarp.cid_id = init_call.id
       AND tblcarp.actiontype_id IN (1)
       AND tblcarp.nextCFID != 0
       AND (tblcarp.actontaken = 'yes' AND tblcarp.purpose_achieved = 'yes')
       AND CAST(tblcarp.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id IN (3,4,22)
                  AND tblcm2.nextCFID != 0
                  AND tblcm2.mtype IN ('RP','RPClose','Change RP')
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'

LEFT JOIN status s3 ON s3.id = tblcarp.status_id
LEFT JOIN status s4 ON s4.id = tblcarp.nstatus_id
      
-- LEFT JOIN proposal p1
--        ON p1.init_id = init_call.id
--        AND p1.apr IN (0,1)
--        AND CAST(p1.sdatet AS DATE) BETWEEN '2025-02-14' AND '2025-11-15'
LEFT JOIN (
   SELECT id, init_id, pbudgetme,sdatet,tid as ptask_id, id as proposal_id, user_id as puser_id
   FROM proposal
   WHERE apr IN (0,1)
     AND CAST(sdatet AS DATE) BETWEEN '$sdate' AND '$edate'
) p1 ON p1.init_id = init_call.id

LEFT JOIN user_details u4 ON u4.user_id = p1.puser_id 
LEFT JOIN user_details u5 ON u5.user_id = tblcarp.user_id 
LEFT JOIN action a3 ON a3.id = tblcarp.actiontype_id 

LEFT JOIN tblcallevents tblcap
       ON tblcap.cid_id = init_call.id
       AND tblcap.actiontype_id = 1
       AND tblcap.nextCFID != 0
       AND (tblcap.actontaken = 'yes' AND tblcap.purpose_achieved = 'yes')

    -- AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
       AND CAST(tblcap.appointmentdatetime AS DATE) BETWEEN
           (SELECT MIN(CAST(tblcm2.appointmentdatetime AS DATE))
            FROM tblcallevents tblcm2
            WHERE tblcm2.cid_id = init_call.id
                  AND tblcm2.actiontype_id = 7
                  AND tblcm2.nextCFID != 0
                  AND CAST(tblcm2.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate')
           AND '$edate'

LEFT JOIN status s1 ON s1.id = tblcap.status_id
LEFT JOIN status s2 ON s2.id = tblcap.nstatus_id

LEFT JOIN user_details u6 ON u6.user_id = tblcap.user_id        
 LEFT JOIN cash_expense ON cash_expense.meetid = barginmeeting.id
 AND (
       (cash_expense.verify = 1 AND cash_expense.admin_apr = 1)
        OR (cash_expense.verify = 0 AND cash_expense.admin_apr = 0)
        OR (cash_expense.verify = 1 AND cash_expense.admin_apr = 0)
      )
     
WHERE init_call.cmpid_id = '$cid'
      AND u1.status = 'active' 
      $sqlFilter
      $groupSqlFilter 
      ");
    $totalClosurePipeLineDatasByuser = $totalClosurePipeLineByUser->result();

    return $totalClosurePipeLineDatasByuser;
}

public function GetHandoverDetailsByBetWeenDateByAdminUid($userid,$admin_id_filter,$rm_filter,$sdate,$edate){

    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }

    $queryUser   = $this->db->query("SELECT u1.user_id, u1.name FROM `user_details` u1 WHERE $text");
    $userDatas   = $queryUser->result();

    $userIds = array_map(function($u) {
        return $u->user_id;
    }, array_filter($userDatas, function($u) {
        return !empty($u->user_id) && $u->user_id != 0;
    }));

    $user_id_list =  implode(",", $userIds);


    $db3 = $this->load->database('db3', TRUE);

    $db3 = $this->load->database('db3', TRUE);
    $query=$db3->query("SELECT * FROM `client_handover` WHERE CAST(sdatet AS DATE) BETWEEN '$sdate' AND '$edate' AND bd_id IN ($user_id_list)");
    return $query->result();
}

public function GetHandoverDetailsByCidBetWeenDate($cid,$sdate,$edate){
    $db3 = $this->load->database('db3', TRUE);
    $query=$db3->query("SELECT client_handover.*, CAST(client_handover.sdatet AS DATE) as client_handover_date FROM `client_handover` WHERE client_id = '$cid' AND CAST(sdatet AS DATE) BETWEEN '$sdate' AND '$edate'");
    return $query->result();
}

public function GetHandoverAccountDetailsByHid($hid){
    $db3 = $this->load->database('db3', TRUE);
    $query=$db3->query("SELECT * FROM `handover_account` WHERE handover_id = '$hid'");
    return $query->result();
}


public function getAllCompanyBYRollesMaiingBYMeeting($userid,$admin_id_filter,$rm_filter,$sdate,$edate){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }

       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }


    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];

 $totalClusterBYClusterNameBYBD = $this->db->query("SELECT
    c.user_id,
    c.username,
    c.cluster_id,
    c.clustername,

    COUNT(DISTINCT c.init_id) AS total,

    COUNT(DISTINCT CASE WHEN c.travel_category = 'base' THEN c.init_id END) AS base_location,
    COUNT(DISTINCT CASE WHEN c.travel_category = 'outStation' THEN c.init_id END) AS outStation_location,

    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL THEN c.init_id END) AS total_planned_meeting,
    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.nextCFID != 0 THEN c.init_id END) AS total_complete_meeting,
    COUNT(DISTINCT CASE WHEN ce.id IS NULL THEN c.init_id END) AS total_pending_for_meeting,

    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.mtype IN ('RP','RPClose','Change RP') THEN c.init_id END) AS total_rp_complete_meeting,

    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.actiontype_id IN (3) THEN c.init_id END) AS total_planned_sheduled_meeting,
    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.actiontype_id IN (4) THEN c.init_id END) AS total_planned_barg_in_meeting,
    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.actiontype_id IN (22) THEN c.init_id END) AS total_planned_virtual_meeting,

    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.actiontype_id IN (3) AND ce.nextCFID != 0 THEN c.init_id END) AS total_complete_planned_sheduled_meeting,
    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.actiontype_id IN (4) AND ce.nextCFID != 0 THEN c.init_id END) AS total_complete_planned_barg_in_meeting,
    COUNT(DISTINCT CASE WHEN ce.id IS NOT NULL AND ce.actiontype_id IN (22) AND ce.nextCFID != 0 THEN c.init_id END) AS total_complete_planned_virtual_meeting,

    COUNT(DISTINCT CASE WHEN 
    ce.id IS NOT NULL 
    AND ce.mtype  = 'Only Got Detail' 
    AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce3
                WHERE ce3.cid_id = c.init_id
                  AND ce3.id IS NOT NULL
                  AND ce3.mtype IN (
                        'NO RP',
                        'RP','RPClose','Change RP'
                    )
                )
    THEN c.init_id END) AS total_ogt_complete_meeting,
    COUNT(DISTINCT CASE WHEN 
    ce.id IS NOT NULL 
    AND ce.mtype  = 'NO RP'
    AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce2
                WHERE ce2.cid_id = c.init_id
                  AND ce2.id IS NOT NULL
                  AND ce2.mtype IN (
                        'Only Got Detail',
                        'RP','RPClose','Change RP'
                    )
                )
     THEN c.init_id END) AS total_no_rp_complete_meeting,

    COUNT(DISTINCT CASE 
        WHEN ce.id IS NOT NULL 
            AND ce.delete_request IS NOT NULL 
            AND ce.delete_request != '' 
            AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce3
                WHERE ce3.cid_id = c.init_id
                  AND ce3.id IS NOT NULL
                  AND ce3.mtype IN (
                        'NO RP','Only Got Detail',
                        'RP','RPClose','Change RP'
                    )
                )
        THEN c.init_id 
    END) AS total_deleted_meeting,

    COUNT(DISTINCT CASE WHEN (proposal.id IS NOT NULL AND proposal.apr IN (0,1)) THEN c.init_id END) AS total_proposal_sent,

    COUNT(DISTINCT CASE WHEN (c.cstatus IN (7,14)) THEN c.init_id END) AS total_clouser,
    COUNT(DISTINCT CASE WHEN (c.in_quarter = '20 Closure Funnel in $currentQuarter' AND ce.id IS NULL) THEN c.init_id END) AS total_pending_for_meeting_20_clouser_funnel,
    COUNT(DISTINCT CASE WHEN (c.in_quarter = '20 Closure Funnel in $currentQuarter' AND proposal.id IS NULL) THEN c.init_id END) AS total_pending_for_proposal_20_clouser_funnel,
    COUNT(DISTINCT CASE WHEN (c.in_quarter = 'Potential Funnel For $currentQuarter' AND ce.id IS NULL) THEN c.init_id END) AS total_pending_for_meeting_potential_funnel,
    COUNT(DISTINCT CASE WHEN (c.in_quarter = 'Proposal to Be Sent After Review In $currentQuarter' AND proposal.id IS NULL) THEN c.init_id END) AS total_pending_proposal_to_be_sent_after_review
    
FROM (
    SELECT DISTINCT
        init_call.id AS init_id,
        u1.user_id,
        u1.name AS username,
        cluster.clustername,
        cluster.id AS cluster_id,
        init_call.cstatus,
        init_call.in_quarter,

        CASE 
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 
                 AND cluster.travelType = 'base' THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 
                 AND cluster.travelType = 'outStation' THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL AND init_call.cluster_id != 0 
                 AND cluster.id IS NOT NULL 
                 AND (cluster.travelType IS NULL OR cluster.travelType = '') 
                 THEN 'marked_but_base_out_not_set'
            ELSE 'not_set_cluster'
        END AS travel_category
    FROM init_call

    LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    
    WHERE $text AND u1.status = 'active'
) AS c

LEFT JOIN tblcallevents ce  ON ce.cid_id = c.init_id AND ce.actiontype_id IN (3, 4, 22) AND CAST(ce.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
LEFT JOIN proposal ON proposal.init_id = c.init_id AND CAST(proposal.sdatet AS DATE) BETWEEN '$sdate' AND '$edate'

WHERE c.travel_category IS NOT NULL
  AND c.cluster_id IS NOT NULL

GROUP BY 
    c.user_id, c.clustername");

$totalClusterBYClusterNameBYBDDatas = $totalClusterBYClusterNameBYBD->result();

    $result = [
        'totalClusterBYClusterNameBYBDDatas'=> $totalClusterBYClusterNameBYBDDatas,
    ];
    return $result;
}





public function GetFunnelDetailsWithClusterIDMeetingDetails($userid,$mtypes,$clusterID,$userwise,$sdate,$edate){
    if($userwise == 'status'){
        $status_id = 'status';
    }
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{
    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{
        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}
    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    $start_financial_date   = $curFinancialDate['start_date'];
    $end_financial_date     = $curFinancialDate['end_date'];
    $start_financial_date   = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];
    $filter = '';
    $groupfilter = 'GROUP BY init_call.id';

    $leftJoinfilterMeet = "";


    if ($mtypes == 'total_pending_for_meeting') {
        $filterMeet = " (ce.id IS NULL)";
    }
    else if ($mtypes == 'total_rp_complete_meeting') {
        $filterMeet = " (ce.id IS NOT NULL AND ce.mtype IN ('RP','RPClose','Change RP')) ";
    }
    else if ($mtypes == 'total_proposal_sent') {
        $filterMeet = " (proposal.id IS NOT NULL AND proposal.apr IN (0,1)) ";
    }
    else if ($mtypes == 'total_clouser') {
        $filterMeet = " (categorized.cstatus IN (7,14)) ";
    }
    else if ($mtypes == 'total_pending_for_meeting_20_clouser_funnel') {
        $filterMeet = " (categorized.in_quarter = '20 Closure Funnel in $currentQuarter' AND ce.id IS NULL) ";
    }
    else if ($mtypes == 'total_pending_for_proposal_20_clouser_funnel') {
        $filterMeet = " (categorized.in_quarter = '20 Closure Funnel in $currentQuarter' AND proposal.id IS NULL) ";
    }
    else if ($mtypes == 'total_pending_for_meeting_potential_funnel') {
        $filterMeet = " (categorized.in_quarter = 'Potential Funnel For $currentQuarter' AND ce.id IS NULL) ";
    }
    else if ($mtypes == 'total_pending_proposal_to_be_sent_after_review') {
        $filterMeet = " (categorized.in_quarter = 'Proposal to Be Sent After Review In $currentQuarter' AND proposal.id IS NULL) ";
    }
    
    else if ($mtypes == 'total_no_rp_complete_meeting') {
        $filterMeet = " (ce.id IS NOT NULL AND ce.mtype  = 'NO RP') ";
        $leftJoinfilterMeet = " AND ce.mtype = 'NO RP'
            AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce2
                WHERE ce2.cid_id = categorized.id
                AND ce2.mtype IN ('Only Got Detail','RP','RPClose','Change RP')
        )
        ";
    }
    else if ($mtypes == 'total_ogt_complete_meeting') {
        $filterMeet = " (ce.id IS NOT NULL AND ce.mtype  = 'Only Got Detail') ";
        $leftJoinfilterMeet = " AND ce.mtype = 'Only Got Detail'
            AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce2
                WHERE ce2.cid_id = categorized.id
                AND ce2.mtype IN ('NO RP','RP','RPClose','Change RP')
        )
        ";
    }
    else if ($mtypes == 'total_planned_meeting') {
        $filterMeet = " ce.id IS NOT NULL ";
        $leftJoinfilterMeet = "";
    }
    else if($mtypes == 'total_complete_meeting') {
        $filterMeet = " ce.id IS NOT NULL AND ce.nextCFID != 0 ";
        $leftJoinfilterMeet = "AND ce.nextCFID != 0";
    }
    else if ($mtypes == 'total_deleted_meeting') {
        $filterMeet = " ce.id IS NOT NULL ";
        $leftJoinfilterMeet = " AND (ce.delete_request IS NOT NULL AND ce.delete_request != '')
            AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce2
                WHERE ce2.cid_id = categorized.id
                AND ce2.mtype IN ('NO RP','Only Got Detail','RP','RPClose','Change RP')
        )
        ";
    }
    else if ($mtypes == 'total_no_rp_complete_meeting') {
        $filterMeet = " ce.id IS NOT NULL AND ce.nextCFID != 0";
        $leftJoinfilterMeet = " AND ce.mtype  = 'NO RP'
            AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce2
                WHERE ce2.cid_id = categorized.id
                AND ce2.mtype IN ('Only Got Detail','RP','RPClose','Change RP')
        )
        ";
    }
    else if ($mtypes == 'total_ogt_complete_meeting') {
        $filterMeet = " ce.id IS NOT NULL AND ce.nextCFID != 0";
        $leftJoinfilterMeet = " AND ce.mtype  = 'Only Got Detail'
            AND NOT EXISTS (
                SELECT 1 
                FROM tblcallevents ce2
                WHERE ce2.cid_id = categorized.id
                AND ce2.mtype IN ('NO RP','RP','RPClose','Change RP')
        )
        ";
    }
    else if ($mtypes == 'total_complete_planned_sheduled_meeting') {
        $filterMeet = " ce.id IS NOT NULL AND ce.nextCFID != 0";
        $leftJoinfilterMeet = " AND ce.actiontype_id = 3";
    }
    else if ($mtypes == 'total_complete_planned_barg_in_meeting') {
        $filterMeet = " ce.id IS NOT NULL AND ce.nextCFID != 0";
        $leftJoinfilterMeet = " AND ce.actiontype_id = 4";
    }
    else if ($mtypes == 'total_complete_planned_virtual_meeting') {
        $filterMeet = " ce.id IS NOT NULL AND ce.nextCFID != 0";
        $leftJoinfilterMeet = " AND ce.actiontype_id = 22";
    }




$query = $this->db->query("SELECT
    categorized.id as init_id,
    categorized.cid,
    categorized.compname,
    categorized.created_date,
    categorized.current_status,
    categorized.last_status,
    categorized.mainbd_name,
    categorized.cluster_manager_name,
    categorized.pst_name,
    categorized.ash_nae_co_id_name,
    categorized.ash_w_co_id_name,
    categorized.ash_s_co_id_name,
    categorized.rm_east_co_id_name,
    categorized.rm_north_co_id_name,
    categorized.acm_co_id_name,
    categorized.topspender,
    categorized.upsell_client,
    categorized.focus_funnel,
    categorized.anchor_clients,
    categorized.in_quarter,
    categorized.keycompany,
    categorized.pkclient,
    categorized.priorityc,
    categorized.potential,
    categorized.q1_twetenty_closure_funnel,
    categorized.potential_funnel_for_fy,
    categorized.to_be_nurtured_for_fy,
    categorized.fifity_new_lead_funnel,
    categorized.partner_name,
    categorized.clustername,
    categorized.travelType,
    categorized.cluster_id,
    categorized.travel_cluster_create_name,
    categorized.task_action_id,
    categorized.compaany_createby_task_name,
    categorized.compaany_createby_mtype,
    categorized.compaany_after_task,
    categorized.contact_details,
    categorized.last_tblc_id,
    categorized.last_tblc_nextCFID,
    categorized.last_tblc_remarks,
    categorized.last_tblc_delete_request,
    categorized.last_tblc_actiontype_id,
    categorized.last_tblc_special_remarks,
    categorized.last_tblc_mom,
    categorized.last_tblc_mtype,
    categorized.last_tblc_mom_remarks,
    categorized.last_tblc_date,
    categorized.last_tblc_task,
    categorized.last_meet_id,
    categorized.last_meet_mtype,
    categorized.last_meet_username,
    categorized.mom_approved_status,
    categorized.proposal_attach,
    categorized.proposal_apr,
    categorized.proposal_id,
    categorized.proposal_tid,
    categorized.cstatus,
    categorized.cbm_potentional_client
FROM (
    SELECT DISTINCT 
        init_call.id,
        company_master.id AS cid,
        company_master.compname,
        init_call.createDate AS created_date,
        s1.name AS current_status,
        s2.name AS last_status,
        u1.name AS mainbd_name,
        u2.name AS cluster_manager_name,
        u3.name AS pst_name,
        u4.name AS ash_nae_co_id_name,
        u5.name AS ash_w_co_id_name,
        u6.name AS ash_s_co_id_name,
        u7.name AS rm_east_co_id_name,
        u8.name AS rm_north_co_id_name,
        u9.name AS acm_co_id_name,
        init_call.cstatus,
        init_call.topspender,
        init_call.upsell_client,
        init_call.focus_funnel,
        init_call.anchor_clients,
        init_call.in_quarter,
        init_call.keycompany,
        init_call.pkclient,
        init_call.priorityc,
        init_call.potential,
        init_call.q1_twetenty_closure_funnel,
        init_call.potential_funnel_for_fy,
        init_call.to_be_nurtured_for_fy,
        init_call.fifity_new_lead_funnel,
        partner_master.name AS partner_name,
        cluster.clustername,
        cluster.travelType,
        cluster.id AS cluster_id,
        u10.name AS travel_cluster_create_name,
        tblc.actiontype_id AS task_action_id,
        action.name AS compaany_createby_task_name,
        tblc.mtype AS compaany_createby_mtype,
        init_call.after_task AS compaany_after_task,

        CONCAT_WS(' | ',
            CONCAT('<b>Name :</b>', ccm.contactperson),
            CONCAT('<b>Email : </b>', ccm.emailid),
            CONCAT('<b>Phone : </b>', ccm.phoneno),
            CONCAT('<b>Type : </b>', ccm.type)
        ) AS contact_details,

        last_tblc.id AS last_tblc_id,
        last_tblc.nextCFID AS last_tblc_nextCFID,
        last_tblc.remarks AS last_tblc_remarks,
        last_tblc.delete_request AS last_tblc_delete_request,
        last_tblc.actiontype_id AS last_tblc_actiontype_id,
        last_tblc.special_remarks AS last_tblc_special_remarks,
        last_tblc.mom AS last_tblc_mom, 
        last_tblc.mtype AS last_tblc_mtype, 
        last_tblc.mom_remarks AS last_tblc_mom_remarks, 
        last_tblc.appointmentdatetime AS last_tblc_date, 
        a2.name AS last_tblc_task,

        meet.id AS last_meet_id,
        meet.mtype AS last_meet_mtype,
        u11.name AS last_meet_username,
        mom_data.approved_status AS mom_approved_status,
        proposal.proattach AS proposal_attach,
        proposal.apr AS proposal_apr,
        proposal.id AS proposal_id,
        proposal.tid AS proposal_tid,
        cbm.potentional_client AS cbm_potentional_client,

        CASE 
            WHEN init_call.cluster_id IS NOT NULL 
                AND init_call.cluster_id != 0 
                AND cluster.travelType = 'base' 
            THEN 'base'
            WHEN init_call.cluster_id IS NOT NULL 
                AND init_call.cluster_id != 0 
                AND cluster.travelType = 'outStation' 
            THEN 'outStation'
            WHEN init_call.cluster_id IS NOT NULL 
                AND init_call.cluster_id != 0 
                AND (cluster.travelType IS NULL OR cluster.travelType = '') 
            THEN 'marked_but_base_out_not_set'
            WHEN init_call.cluster_id IS NULL 
                OR init_call.cluster_id = 0 
            THEN 'not_set_cluster'
        END AS travel_category

    FROM init_call
    LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
    LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id AND ccm.type = 'primary'
    LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
    LEFT JOIN status s1 ON s1.id = init_call.cstatus
    LEFT JOIN status s2 ON s2.id = init_call.lstatus
    LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd
    LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
    LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
    LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
    LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
    LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
    LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
    LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
    LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
    LEFT JOIN cluster ON cluster.id = init_call.cluster_id
    LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
    LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
    LEFT JOIN action ON action.id = tblc.actiontype_id
    LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id

    LEFT JOIN tblcallevents meet 
        ON meet.cid_id = init_call.id  
       AND meet.nextCFID != 0 
       AND meet.actiontype_id IN (3, 4, 22) 
       AND CAST(meet.appointmentdatetime AS DATE) >= '$start_financial_date'
       AND meet.mtype IN ('RP','RPClose','Change RP')

    LEFT JOIN user_details u11 ON u11.user_id = meet.user_id

    LEFT JOIN tblcallevents mom_tblc 
        ON mom_tblc.aftertask = meet.id 
       AND mom_tblc.actiontype_id = 6

    LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 

    LEFT JOIN proposal 
        ON proposal.init_id = init_call.id 
       AND CAST(proposal.sdatet AS DATE) >= '$start_financial_date'

    LEFT JOIN (
        SELECT t1.*
        FROM tblcallevents t1
        INNER JOIN (
            SELECT cid_id, MAX(id) AS max_id
            FROM tblcallevents
            GROUP BY cid_id
        ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
    ) AS last_tblc ON last_tblc.cid_id = init_call.id
    LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id

    WHERE $text 
    AND u1.status = 'active'
    $groupfilter
) AS categorized

LEFT JOIN tblcallevents ce 
       ON ce.cid_id = categorized.id
    --   AND ce.nextCFID != 0
      AND ce.actiontype_id IN (3, 4, 22) 
      AND CAST(ce.appointmentdatetime AS DATE) BETWEEN '$sdate' AND '$edate'
      $leftJoinfilterMeet
LEFT JOIN proposal ON proposal.init_id = categorized.id AND CAST(proposal.sdatet AS DATE) BETWEEN '$sdate' AND '$edate' 

WHERE $filterMeet
  AND categorized.cluster_id  = '$clusterID'
GROUP BY 
    categorized.cid
");
return $query->result();

}








public function MoreThenNDaysNoActivityDoneBYUser($userid,$admin_id_filter,$rm_filter){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }

       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }

    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];

$totalFunnelQuery = $this->db->query("SELECT

    (
      /* 0 to 8 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 ELSE 0 END)
      +
      /* 9 to 15 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 ELSE 0 END)
      +
      /* 16 to 30 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 ELSE 0 END)
      +
      /* 31 to 50 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 ELSE 0 END)
      +
      /* >50 (not null) */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 ELSE 0 END)
      +
      /* no activity ever */
      SUM(CASE WHEN last.last_event IS NULL THEN 1 ELSE 0 END)
    ) AS total,

    /* 0 to 8 days (includes future appointments treated as 0) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_9_to_15_days,

    /* 16 to 30 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_16_to_30_days,

    /* 31 to 50 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_31_to_50_days,

    /* > 50 days (only non-NULL) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_more_than_50_days,

    /* never did activity */
    SUM(
      CASE 
        WHEN last.last_event IS NULL THEN 1
        ELSE 0
      END
    ) AS no_activity_till_date

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
");
$total_funnel = $totalFunnelQuery->result();


$totalFunnelByUserQuery = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    /* total derived from buckets so it always matches */
    (
      /* 0 to 8 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 ELSE 0 END)
      +
      /* 9 to 15 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 ELSE 0 END)
      +
      /* 16 to 30 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 ELSE 0 END)
      +
      /* 31 to 50 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 ELSE 0 END)
      +
      /* >50 (not null) */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 ELSE 0 END)
      +
      /* no activity ever */
      SUM(CASE WHEN last.last_event IS NULL THEN 1 ELSE 0 END)
    ) AS total,

    /* 0 to 8 days (includes future appointments treated as 0) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_9_to_15_days,

    /* 16 to 30 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_16_to_30_days,

    /* 31 to 50 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_31_to_50_days,

    /* > 50 days (only non-NULL) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_more_than_50_days,

    /* never did activity */
    SUM(
      CASE 
        WHEN last.last_event IS NULL THEN 1
        ELSE 0
      END
    ) AS no_activity_till_date

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$total_funnel_by_user = $totalFunnelByUserQuery->result();




$totalFunnelByUserAssignQuery = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    /* total derived from buckets so it always matches */
    (
      /* 0 to 8 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 ELSE 0 END)
      +
      /* 9 to 15 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 ELSE 0 END)
      +
      /* 16 to 30 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 ELSE 0 END)
      +
      /* 31 to 50 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 ELSE 0 END)
      +
      /* >50 (not null) */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 ELSE 0 END)
      +
      /* no activity ever */
      SUM(CASE WHEN last.last_event IS NULL THEN 1 ELSE 0 END)
    ) AS total,

    /* 0 to 8 days (includes future appointments treated as 0) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_9_to_15_days,

    /* 16 to 30 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_16_to_30_days,

    /* 31 to 50 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_31_to_50_days,

    /* > 50 days (only non-NULL) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_more_than_50_days,

    /* never did activity */
    SUM(
      CASE 
        WHEN last.last_event IS NULL THEN 1
        ELSE 0
      END
    ) AS no_activity_till_date

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  JOIN user_details ud ON te.user_id = ud.user_id
  GROUP BY te.cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$total_funnel_by_user_assign = $totalFunnelByUserAssignQuery->result();


// By Status Start


$totalNotDoneinZeroToEightDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 0 AND 8
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinZeroToEightDays = $totalNotDoneinZeroToEightDays->result();

$totalNotDoneinNineToFifteenDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinNineToFifteenDays = $totalNotDoneinNineToFifteenDays->result();


$totalNotDoneinNineToFifteenDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinNineToFifteenDays = $totalNotDoneinNineToFifteenDays->result();


$totalNotDoneinsixteenToThirtyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinsixteenToThirtyDays = $totalNotDoneinsixteenToThirtyDays->result();

$totalNotDoneinthirtyOneTofiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinthirtyOneTofiftyDays = $totalNotDoneinthirtyOneTofiftyDays->result();


$totalNotDoneinMoreThanfiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanfiftyDays = $totalNotDoneinMoreThanfiftyDays->result();


$totalNotDoneinMoreThanDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    cid_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents
  GROUP BY cid_id
) last
  ON last.cid_id = ic.id

WHERE
     $text AND u1.status = 'active'
     AND last.last_event IS NULL
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanDays = $totalNotDoneinMoreThanDays->result();

// By Status Close

    $result = [
        'total_funnel'                          => $total_funnel,
        'total_funnel_by_user'                  => $total_funnel_by_user,
        'total_funnel_assign_by_user'           => $total_funnel_by_user_assign,
        'totalNotDoneinZeroToEightDays'         => $totalNotDoneinZeroToEightDays,
        'totalNotDoneinNineToFifteenDays'       => $totalNotDoneinNineToFifteenDays,
        'totalNotDoneinsixteenToThirtyDays'     => $totalNotDoneinsixteenToThirtyDays,
        'totalNotDoneinthirtyOneTofiftyDays'    => $totalNotDoneinthirtyOneTofiftyDays,
        'totalNotDoneinMoreThanfiftyDays'       => $totalNotDoneinMoreThanfiftyDays,
        'totalNotDoneinMoreThanDays'            => $totalNotDoneinMoreThanDays,
    ];
    return $result;
}



public function MoreThenNDaysNoActivityDoneBYUserCompanyDetails($tarFilter,$userid,$userwise,$status){
    
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
   if($type_id == 1){
        $text = "u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{

        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 25){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}

        if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }

     
 
 
    $curFinancialDate           = $this->Menu_model->getFinancialYearRange();
    $start_financial_date       = $curFinancialDate['start_date'];
    $end_financial_date         = $curFinancialDate['end_date'];
    $start_financial_date       = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData                     = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter             = "Q".$cfData['quarter'];
    $groupfilter                = '';
    $userActiveStatus           = " AND u1.status = 'active'";
 
    $whereConditions = '';

    if($tarFilter == 'task_not_done_in_0_to_8_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 0 AND 8";
    }
    else if($tarFilter == 'task_not_done_in_9_to_15_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15";
    }
    else if($tarFilter == 'task_not_done_in_16_to_30_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30";
    }
    else if($tarFilter == 'task_not_done_in_31_to_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50";
    }
    else if($tarFilter == 'task_not_done_in_more_than_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50";
    }
    else if($tarFilter == 'no_activity_till_date'){
        $whereConditions = "AND last.last_event IS NULL";
    }

    $companyStatusFilter = '';
    $statusMap = [
        'open'              => 1,
        'reachout'          => 2,
        'tentative'         => 3,
        'will_do_later'     => 4,
        'not_interested'    => 5,
        'positive'          => 6,
        'closure'           => 7,
        'open_rpem'         => 8,
        'very_positive'     => 9,
        'ttd_reachout'      => 10,
        'wno_reachout'      => 11,
        'positive_nap'      => 12,
        'very_positive_nap' => 13,
        'on_boarded'        => 14
    ];

    if(isset($statusMap[$status])) {
        $companyStatusFilter = " AND init_call.cstatus = ".$statusMap[$status];
    }

    if($mtypes == 'total'){
        $filter = '';
    }

   $groupfilter    = 'GROUP BY company_master.id';
   $query = $this->db->query("SELECT
    company_master.id AS cid,
    company_master.compname,
    company_master.state as company_state,
    company_master.district as company_district,
    company_master.city as company_city,
    company_master.address as company_address,
    init_call.createDate AS created_date,
    s1.name as current_status,
    s2.name as last_status,
    u1.name as mainbd_name,
    u2.name as cluster_manager_name,
    u3.name as pst_name,
    u4.name as ash_nae_co_id_name,
    u5.name as ash_w_co_id_name,
    u6.name as ash_s_co_id_name,
    u7.name as rm_east_co_id_name,
    u8.name as rm_north_co_id_name,
    u9.name as acm_co_id_name,
    init_call.topspender,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.anchor_clients,
    init_call.in_quarter,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.potential,
    init_call.q1_twetenty_closure_funnel,
    init_call.potential_funnel_for_fy,
    init_call.to_be_nurtured_for_fy,
    init_call.fifity_new_lead_funnel,
    partner_master.name as partner_name,
    cluster.clustername,
    cluster.travelType as travelType,
    cluster.id as cluster_id,
    u10.name as travel_cluster_create_name,
    tblc.actiontype_id as task_action_id,
    action.name as compaany_createby_task_name,
    tblc.mtype as compaany_createby_mtype,
    init_call.after_task as compaany_after_task,
  CONCAT_WS(' | ',
        CONCAT('<b>Name :</b>', ccm.contactperson),
        CONCAT('<b>Email : </b>', ccm.emailid),
        CONCAT('<b>Phone : </b>', ccm.phoneno),
        CONCAT('<b>Type : </b>', ccm.type)
    ) AS contact_details,
    last_tblc.id as last_tblc_id,
    last_tblc.nextCFID as last_tblc_nextCFID,
    u20.name as last_tblc_call_by_user,
    last_tblc.remarks as last_tblc_remarks,
    last_tblc.delete_request as last_tblc_delete_request,
    last_tblc.actiontype_id as last_tblc_actiontype_id,
    last_tblc.special_remarks as last_tblc_special_remarks,
    last_tblc.mom as last_tblc_mom, 
    last_tblc.mtype as last_tblc_mtype, 
    last_tblc.mom_remarks as last_tblc_mom_remarks, 
    last_tblc.appointmentdatetime as last_tblc_date, 
    a2.name as last_tblc_task,
    meet.id as last_meet_id,
    meet.mtype as last_meet_mtype,
    u11.name as last_meet_username,
    mom_data.approved_status as mom_approved_status,
    proposal.proattach as proposal_attach,
    proposal.apr as proposal_apr,
    proposal.id as proposal_id,
    proposal.tid as proposal_tid,
    cbm.potentional_client as cbm_potentional_client,
    init_call.super_admin as super_admin,
    u12.name as super_admin_name,
    u13.name as assign_support_name,
    u14.name as leads_main_bd_name,
    u1.user_cluster_zone,
    last.last_event as task_not_done_in_days
FROM
    init_call
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id  AND ccm.type = 'primary'
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status s1 ON s1.id = init_call.cstatus
LEFT JOIN status s2 ON s2.id = init_call.lstatus
LEFT JOIN user_details u1 ON u1.user_id = $mainBDFilterQuery 
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
LEFT JOIN cluster ON cluster.id = init_call.cluster_id
LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
LEFT JOIN user_details u12 ON u12.user_id = init_call.super_admin
LEFT JOIN user_details u13 ON u13.user_id = init_call.assign_support
LEFT JOIN user_details u14 ON u14.user_id = init_call.mainbd
LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
LEFT JOIN action ON action.id = tblc.actiontype_id
LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id
LEFT JOIN tblcallevents meet ON meet.cid_id = init_call.id  AND meet.nextCFID !=0 AND meet.actiontype_id IN(3,4) 
AND cast(meet.appointmentdatetime as Date) >= '$start_financial_date' 
-- AND YEAR(meet.appointmentdatetime) = '$year' 
AND (meet.mtype = 'RP' OR meet.mtype = 'RPClose' OR meet.mtype = 'Change RP')
LEFT JOIN user_details u11 ON u11.user_id = meet.user_id
LEFT JOIN tblcallevents mom_tblc ON mom_tblc.aftertask = meet.id AND mom_tblc.actiontype_id = 6
LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 
LEFT JOIN proposal ON proposal.init_id = init_call.id AND cast(proposal.sdatet as Date) >= '$start_financial_date' 
LEFT JOIN (
    SELECT t1.*
    FROM tblcallevents t1
    INNER JOIN (
        SELECT cid_id, MAX(id) AS max_id
        FROM tblcallevents WHERE 
        actiontype_id = 1
        AND actontaken = 'yes' 
        AND purpose_achieved = 'yes'
        AND cast(appointmentdatetime as Date) >= '$start_financial_date'
        GROUP BY cid_id
    ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
) AS last_tblc ON last_tblc.cid_id = init_call.id
LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id
LEFT JOIN user_details u20 ON u20.user_id = last_tblc.user_id
-- LEFT JOIN (
--   SELECT
--     cid_id,
--     MAX(appointmentdatetime) AS last_appt,
--     DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
--   FROM tblcallevents
--   GROUP BY cid_id
-- ) last
--   ON last.cid_id = init_call.id

  LEFT JOIN (
        SELECT
            te.cid_id,
            te.user_id,
            MAX(te.id) as last_task_id,
            MAX(appointmentdatetime) AS last_appt,
            DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
            DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
        FROM tblcallevents te
        GROUP BY te.cid_id, te.user_id
        ) last
        ON last.cid_id = init_call.id

WHERE 
     $text $filter $userActiveStatus $companyStatusFilter
     $whereConditions
     
     $groupfilter");
    return $query->result(); 
}


public function MoreThenNDaysNoActivityDoneBYUserLineManager($userid,$admin_id_filter,$rm_filter){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }

       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }

    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];

$totalFunnelQuery = $this->db->query("SELECT

    (
      /* 0 to 8 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 ELSE 0 END)
      +
      /* 9 to 15 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 ELSE 0 END)
      +
      /* 16 to 30 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 ELSE 0 END)
      +
      /* 31 to 50 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 ELSE 0 END)
      +
      /* >50 (not null) */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 ELSE 0 END)
      +
      /* no activity ever */
      SUM(CASE WHEN last.last_event IS NULL THEN 1 ELSE 0 END)
    ) AS total,

    /* 0 to 8 days (includes future appointments treated as 0) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_9_to_15_days,

    /* 16 to 30 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_16_to_30_days,

    /* 31 to 50 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_31_to_50_days,

    /* > 50 days (only non-NULL) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_more_than_50_days,

    /* never did activity */
    SUM(
      CASE 
        WHEN last.last_event IS NULL THEN 1
        ELSE 0
      END
    ) AS no_activity_till_date

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
");
$total_funnel = $totalFunnelQuery->result();




$totalFunnelByUserAssignQuery = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    /* total derived from buckets so it always matches */
    (
      /* 0 to 8 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 ELSE 0 END)
      +
      /* 9 to 15 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 ELSE 0 END)
      +
      /* 16 to 30 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 ELSE 0 END)
      +
      /* 31 to 50 */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 ELSE 0 END)
      +
      /* >50 (not null) */
      SUM(CASE WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 ELSE 0 END)
      +
      /* no activity ever */
      SUM(CASE WHEN last.last_event IS NULL THEN 1 ELSE 0 END)
    ) AS total,

    /* 0 to 8 days (includes future appointments treated as 0) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 0 AND 8 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 9 AND 15 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_9_to_15_days,

    /* 16 to 30 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 16 AND 30 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_16_to_30_days,

    /* 31 to 50 days */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) BETWEEN 31 AND 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_31_to_50_days,

    /* > 50 days (only non-NULL) */
    SUM(
      CASE 
        WHEN (CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END) > 50 THEN 1 
        ELSE 0 
      END
    ) AS task_not_done_in_more_than_50_days,

    /* never did activity */
    SUM(
      CASE 
        WHEN last.last_event IS NULL THEN 1
        ELSE 0
      END
    ) AS no_activity_till_date

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
GROUP BY u1.user_id
ORDER BY u1.name ASC");
$total_funnel_by_user_assign = $totalFunnelByUserAssignQuery->result();


// By Status Start


$totalNotDoneinZeroToEightDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 0 AND 8
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinZeroToEightDays = $totalNotDoneinZeroToEightDays->result();

$totalNotDoneinNineToFifteenDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinNineToFifteenDays = $totalNotDoneinNineToFifteenDays->result();

$totalNotDoneinsixteenToThirtyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinsixteenToThirtyDays = $totalNotDoneinsixteenToThirtyDays->result();

$totalNotDoneinthirtyOneTofiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinthirtyOneTofiftyDays = $totalNotDoneinthirtyOneTofiftyDays->result();


$totalNotDoneinMoreThanfiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanfiftyDays = $totalNotDoneinMoreThanfiftyDays->result();


$totalNotDoneinMoreThanDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(ic.id) AS total,
    COUNT(CASE WHEN ic.cstatus = 1 THEN 1 END) AS open,
    COUNT(CASE WHEN ic.cstatus = 2 THEN 1 END) AS reachout,
    COUNT(CASE WHEN ic.cstatus = 3 THEN 1 END) AS tentative,
    COUNT(CASE WHEN ic.cstatus = 4 THEN 1 END) AS will_do_later,
    COUNT(CASE WHEN ic.cstatus = 5 THEN 1 END) AS not_interested,
    COUNT(CASE WHEN ic.cstatus = 6 THEN 1 END) AS positive,
    COUNT(CASE WHEN ic.cstatus = 7 THEN 1 END) AS closure,
    COUNT(CASE WHEN ic.cstatus = 8 THEN 1 END) AS open_rpem,
    COUNT(CASE WHEN ic.cstatus = 9 THEN 1 END) AS very_positive,
    COUNT(CASE WHEN ic.cstatus = 10 THEN 1 END) AS ttd_reachout,
    COUNT(CASE WHEN ic.cstatus = 11 THEN 1 END) AS wno_reachout,
    COUNT(CASE WHEN ic.cstatus = 12 THEN 1 END) AS positive_nap,
    COUNT(CASE WHEN ic.cstatus = 13 THEN 1 END) AS very_positive_nap,
    COUNT(CASE WHEN (ic.cstatus = 14) THEN 1 END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
WHERE
     $text AND u1.status = 'active'
     AND last.last_event IS NULL
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanDays = $totalNotDoneinMoreThanDays->result();

// By Status Close

    $result = [
        'total_funnel'                          => $total_funnel,
        'total_funnel_by_user'                  => $total_funnel_by_user_assign,
        'totalNotDoneinZeroToEightDays'         => $totalNotDoneinZeroToEightDays,
        'totalNotDoneinNineToFifteenDays'       => $totalNotDoneinNineToFifteenDays,
        'totalNotDoneinsixteenToThirtyDays'     => $totalNotDoneinsixteenToThirtyDays,
        'totalNotDoneinthirtyOneTofiftyDays'    => $totalNotDoneinthirtyOneTofiftyDays,
        'totalNotDoneinMoreThanfiftyDays'       => $totalNotDoneinMoreThanfiftyDays,
        'totalNotDoneinMoreThanDays'            => $totalNotDoneinMoreThanDays,
    ];
    return $result;
}


public function MoreThenNDaysNoActivityDoneBYUserCompanyDetailsLineManager($tarFilter,$userid,$userwise,$status){

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    $cmpUserWhereConditions = '';

   if($type_id == 1){
        $text = "u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{

        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.apst = '$userid'";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.clm_id = '$userid'";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_nae_co_id = '$userid'";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_w_co_id = '$userid'";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_s_co_id = '$userid'";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.rm_east_co_id = '$userid'";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.rm_east_co_id = '$userid'";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.acm_co_id = '$userid'";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 25){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}


    $mainBDFilterQuery = " IN (init_call.mainbd,init_call.clm_id, init_call.apst, init_call.acm_co_id, init_call.rm_east_co_id,init_call.rm_north_co_id,init_call.rm_north_co_id,init_call.ash_nae_co_id,init_call.ash_w_co_id,init_call.ash_s_co_id)";

    if($type_id == 25){
        $mainBDFilterQuery = ' IN (init_call.creator_id)';
    }


    $curFinancialDate           = $this->Menu_model->getFinancialYearRange();
    $start_financial_date       = $curFinancialDate['start_date'];
    $end_financial_date         = $curFinancialDate['end_date'];
    $start_financial_date       = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData                     = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter             = "Q".$cfData['quarter'];
    $groupfilter                = '';
    $userActiveStatus           = " AND u1.status = 'active'";
 
    $whereConditions = '';

    if($tarFilter == 'task_not_done_in_0_to_8_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 0 AND 8";
    }
    else if($tarFilter == 'task_not_done_in_9_to_15_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15";
    }
    else if($tarFilter == 'task_not_done_in_16_to_30_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30";
    }
    else if($tarFilter == 'task_not_done_in_31_to_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50";
    }
    else if($tarFilter == 'task_not_done_in_more_than_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50";
    }
    else if($tarFilter == 'no_activity_till_date'){
        $whereConditions = "AND last.last_event IS NULL";
    }

    $companyStatusFilter = '';
    $statusMap = [
        'open'              => 1,
        'reachout'          => 2,
        'tentative'         => 3,
        'will_do_later'     => 4,
        'not_interested'    => 5,
        'positive'          => 6,
        'closure'           => 7,
        'open_rpem'         => 8,
        'very_positive'     => 9,
        'ttd_reachout'      => 10,
        'wno_reachout'      => 11,
        'positive_nap'      => 12,
        'very_positive_nap' => 13,
        'on_boarded'        => 14
    ];

    if(isset($statusMap[$status])) {
        $companyStatusFilter = " AND init_call.cstatus = ".$statusMap[$status];
    }

    if($mtypes == 'total'){
        $filter = '';
    }

    // echo $whereConditions;
    //     die;

    // echo $whereConditions;
    // die;
 

   $groupfilter    = 'GROUP BY company_master.id';
   $query = $this->db->query("SELECT
    company_master.id AS cid,
    company_master.compname,
    company_master.state as company_state,
    company_master.district as company_district,
    company_master.city as company_city,
    company_master.address as company_address,
    init_call.createDate AS created_date,
    s1.name as current_status,
    s2.name as last_status,
    u1.name as mainbd_name,
    u2.name as cluster_manager_name,
    u3.name as pst_name,
    u4.name as ash_nae_co_id_name,
    u5.name as ash_w_co_id_name,
    u6.name as ash_s_co_id_name,
    u7.name as rm_east_co_id_name,
    u8.name as rm_north_co_id_name,
    u9.name as acm_co_id_name,
    init_call.topspender,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.anchor_clients,
    init_call.in_quarter,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.potential,
    init_call.q1_twetenty_closure_funnel,
    init_call.potential_funnel_for_fy,
    init_call.to_be_nurtured_for_fy,
    init_call.fifity_new_lead_funnel,
    partner_master.name as partner_name,
    cluster.clustername,
    cluster.travelType as travelType,
    cluster.id as cluster_id,
    u10.name as travel_cluster_create_name,
    tblc.actiontype_id as task_action_id,
    action.name as compaany_createby_task_name,
    tblc.mtype as compaany_createby_mtype,
    init_call.after_task as compaany_after_task,
  CONCAT_WS(' | ',
        CONCAT('<b>Name :</b>', ccm.contactperson),
        CONCAT('<b>Email : </b>', ccm.emailid),
        CONCAT('<b>Phone : </b>', ccm.phoneno),
        CONCAT('<b>Type : </b>', ccm.type)
    ) AS contact_details,
    last_tblc.id as last_tblc_id,
    last_tblc.nextCFID as last_tblc_nextCFID,
    u20.name as last_tblc_call_by_user,
    last_tblc.remarks as last_tblc_remarks,
    last_tblc.delete_request as last_tblc_delete_request,
    last_tblc.actiontype_id as last_tblc_actiontype_id,
    last_tblc.special_remarks as last_tblc_special_remarks,
    last_tblc.mom as last_tblc_mom, 
    last_tblc.mtype as last_tblc_mtype, 
    last_tblc.mom_remarks as last_tblc_mom_remarks, 
    last_tblc.appointmentdatetime as last_tblc_date, 
    u16.name as last_tblc_task_user, 
    a2.name as last_tblc_task,
    meet.id as last_meet_id,
    meet.mtype as last_meet_mtype,
    u11.name as last_meet_username,
    mom_data.approved_status as mom_approved_status,
    proposal.proattach as proposal_attach,
    proposal.apr as proposal_apr,
    proposal.id as proposal_id,
    proposal.tid as proposal_tid,
    cbm.potentional_client as cbm_potentional_client,
    init_call.super_admin as super_admin,
    u12.name as super_admin_name,
    u13.name as assign_support_name,
    u14.name as leads_main_bd_name,
    u1.user_cluster_zone,
    last.last_event as task_not_done_in_days
FROM
    init_call
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id  AND ccm.type = 'primary'
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status s1 ON s1.id = init_call.cstatus
LEFT JOIN status s2 ON s2.id = init_call.lstatus
LEFT JOIN user_details u1 ON u1.user_id $mainBDFilterQuery
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
LEFT JOIN cluster ON cluster.id = init_call.cluster_id
LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
LEFT JOIN user_details u12 ON u12.user_id = init_call.super_admin
LEFT JOIN user_details u13 ON u13.user_id = init_call.assign_support
LEFT JOIN user_details u14 ON u14.user_id = init_call.mainbd
LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
LEFT JOIN action ON action.id = tblc.actiontype_id
LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id
LEFT JOIN tblcallevents meet ON meet.cid_id = init_call.id  AND meet.nextCFID !=0 AND meet.actiontype_id IN(3,4) 
AND cast(meet.appointmentdatetime as Date) >= '$start_financial_date' 
-- AND YEAR(meet.appointmentdatetime) = '$year' 
AND (meet.mtype = 'RP' OR meet.mtype = 'RPClose' OR meet.mtype = 'Change RP')
LEFT JOIN user_details u11 ON u11.user_id = meet.user_id
LEFT JOIN tblcallevents mom_tblc ON mom_tblc.aftertask = meet.id AND mom_tblc.actiontype_id = 6
LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 
LEFT JOIN proposal ON proposal.init_id = init_call.id AND cast(proposal.sdatet as Date) >= '$start_financial_date' 
LEFT JOIN (
    SELECT t1.*
    FROM tblcallevents t1
    INNER JOIN (
        SELECT cid_id, MAX(id) AS max_id
        FROM tblcallevents
        WHERE 
        actiontype_id = 1
        AND actontaken = 'yes' 
        AND purpose_achieved = 'yes'
        AND cast(appointmentdatetime as Date) >= '$start_financial_date'
        GROUP BY cid_id
    ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
) AS last_tblc ON last_tblc.cid_id = init_call.id
LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id
LEFT JOIN user_details u16 ON u16.user_id = last_tblc.user_id
LEFT JOIN user_details u20 ON u20.user_id = last_tblc.user_id

-- LEFT JOIN (
--   SELECT
--     te.cid_id,
--     te.user_id,
--     te.id as last_task_id,
--     MAX(appointmentdatetime) AS last_appt,
--     DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
--     DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
--   FROM tblcallevents te
--   GROUP BY te.cid_id, te.user_id
-- ) last
--   ON last.cid_id = init_call.id
--  AND last.user_id = $userid

LEFT JOIN (
        SELECT
            te.cid_id,
            te.user_id,
            te.id as last_task_id,
            MAX(appointmentdatetime) AS last_appt,
            DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
            DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
        FROM tblcallevents te
        GROUP BY te.cid_id, te.user_id
        ) last
        ON last.cid_id = init_call.id
        AND last.user_id = $userid


WHERE 
     $text $filter $userActiveStatus $companyStatusFilter $cmpUserWhereConditions
     $whereConditions
     
     $groupfilter");
    return $query->result(); 
}


public function MoreThenNDaysNoActivityDoneBYUserCompanyDetailsLineManagerWhereProposalSent($tarFilter,$userid,$userwise,$status){

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    $cmpUserWhereConditions = '';

   if($type_id == 1){
        $text = "u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{

        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.apst = '$userid'";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.clm_id = '$userid'";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_nae_co_id = '$userid'";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_w_co_id = '$userid'";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_s_co_id = '$userid'";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.rm_east_co_id = '$userid'";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.rm_east_co_id = '$userid'";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.acm_co_id = '$userid'";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 25){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}

    $mainBDFilterQuery = " IN (init_call.mainbd,init_call.clm_id, init_call.apst, init_call.acm_co_id, init_call.rm_east_co_id,init_call.rm_north_co_id,init_call.rm_north_co_id,init_call.ash_nae_co_id,init_call.ash_w_co_id,init_call.ash_s_co_id)";

    if($type_id == 25){
        $mainBDFilterQuery = ' IN (init_call.creator_id)';
    }

    $curFinancialDate           = $this->Menu_model->getFinancialYearRange();
    $start_financial_date       = $curFinancialDate['start_date'];
    $end_financial_date         = $curFinancialDate['end_date'];
    $start_financial_date       = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData                     = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter             = "Q".$cfData['quarter'];
    $groupfilter                = '';
    $userActiveStatus           = " AND u1.status = 'active'";

    $sdate                      = '2025-02-14';
    $edate                      = date("Y-m-d");
 
    $whereConditions = '';

    if($tarFilter == 'task_not_done_in_0_to_8_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 0 AND 8";
    }
    else if($tarFilter == 'task_not_done_in_9_to_15_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15";
    }
    else if($tarFilter == 'task_not_done_in_16_to_30_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30";
    }
    else if($tarFilter == 'task_not_done_in_31_to_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50";
    }
    else if($tarFilter == 'task_not_done_in_more_than_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50";
    }
    else if($tarFilter == 'no_activity_till_date'){
        $whereConditions = "AND last.last_event IS NULL";
    }

    $companyStatusFilter = '';
    $statusMap = [
        'open'              => 1,
        'reachout'          => 2,
        'tentative'         => 3,
        'will_do_later'     => 4,
        'not_interested'    => 5,
        'positive'          => 6,
        'closure'           => 7,
        'open_rpem'         => 8,
        'very_positive'     => 9,
        'ttd_reachout'      => 10,
        'wno_reachout'      => 11,
        'positive_nap'      => 12,
        'very_positive_nap' => 13,
        'on_boarded'        => 14
    ];

    if(isset($statusMap[$status])) {
        $companyStatusFilter = " AND init_call.cstatus = ".$statusMap[$status];
    }

    if($mtypes == 'total'){
        $filter = '';
    }

   $groupfilter    = 'GROUP BY company_master.id';
   $query = $this->db->query("SELECT
    company_master.id AS cid,
    company_master.compname,
    company_master.state as company_state,
    company_master.district as company_district,
    company_master.city as company_city,
    company_master.address as company_address,
    init_call.createDate AS created_date,
    s1.name as current_status,
    s2.name as last_status,
    u1.name as mainbd_name,
    u2.name as cluster_manager_name,
    u3.name as pst_name,
    u4.name as ash_nae_co_id_name,
    u5.name as ash_w_co_id_name,
    u6.name as ash_s_co_id_name,
    u7.name as rm_east_co_id_name,
    u8.name as rm_north_co_id_name,
    u9.name as acm_co_id_name,
    init_call.topspender,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.anchor_clients,
    init_call.in_quarter,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.potential,
    init_call.q1_twetenty_closure_funnel,
    init_call.potential_funnel_for_fy,
    init_call.to_be_nurtured_for_fy,
    init_call.fifity_new_lead_funnel,
    partner_master.name as partner_name,
    cluster.clustername,
    cluster.travelType as travelType,
    cluster.id as cluster_id,
    u10.name as travel_cluster_create_name,
    tblc.actiontype_id as task_action_id,
    action.name as compaany_createby_task_name,
    tblc.mtype as compaany_createby_mtype,
    init_call.after_task as compaany_after_task,
  CONCAT_WS(' | ',
        CONCAT('<b>Name :</b>', ccm.contactperson),
        CONCAT('<b>Email : </b>', ccm.emailid),
        CONCAT('<b>Phone : </b>', ccm.phoneno),
        CONCAT('<b>Type : </b>', ccm.type)
    ) AS contact_details,
    last_tblc.id as last_tblc_id,
    last_tblc.nextCFID as last_tblc_nextCFID,
    u20.name as last_tblc_call_by_user,
    last_tblc.remarks as last_tblc_remarks,
    last_tblc.delete_request as last_tblc_delete_request,
    last_tblc.actiontype_id as last_tblc_actiontype_id,
    last_tblc.special_remarks as last_tblc_special_remarks,
    last_tblc.mom as last_tblc_mom, 
    last_tblc.mtype as last_tblc_mtype, 
    last_tblc.mom_remarks as last_tblc_mom_remarks, 
    last_tblc.appointmentdatetime as last_tblc_date, 
    u16.name as last_tblc_task_user, 
    a2.name as last_tblc_task,
    meet.id as last_meet_id,
    meet.mtype as last_meet_mtype,
    u11.name as last_meet_username,
    mom_data.approved_status as mom_approved_status,
    proposal.proattach as proposal_attach,
    proposal.apr as proposal_apr,
    proposal.id as proposal_id,
    proposal.tid as proposal_tid,
    cbm.potentional_client as cbm_potentional_client,
    init_call.super_admin as super_admin,
    u12.name as super_admin_name,
    u13.name as assign_support_name,
    u14.name as leads_main_bd_name,
    u1.user_cluster_zone,
    last.last_event as task_not_done_in_days
FROM
    init_call
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id  AND ccm.type = 'primary'
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status s1 ON s1.id = init_call.cstatus
LEFT JOIN status s2 ON s2.id = init_call.lstatus
LEFT JOIN user_details u1 ON u1.user_id $mainBDFilterQuery
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
LEFT JOIN cluster ON cluster.id = init_call.cluster_id
LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
LEFT JOIN user_details u12 ON u12.user_id = init_call.super_admin
LEFT JOIN user_details u13 ON u13.user_id = init_call.assign_support
LEFT JOIN user_details u14 ON u14.user_id = init_call.mainbd
LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
LEFT JOIN action ON action.id = tblc.actiontype_id
LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id
LEFT JOIN tblcallevents meet ON meet.cid_id = init_call.id  AND meet.nextCFID !=0 AND meet.actiontype_id IN(3,4) 
AND cast(meet.appointmentdatetime as Date) >= '$start_financial_date' 
-- AND YEAR(meet.appointmentdatetime) = '$year' 
AND (meet.mtype = 'RP' OR meet.mtype = 'RPClose' OR meet.mtype = 'Change RP')
LEFT JOIN user_details u11 ON u11.user_id = meet.user_id
LEFT JOIN tblcallevents mom_tblc ON mom_tblc.aftertask = meet.id AND mom_tblc.actiontype_id = 6
LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 
LEFT JOIN proposal ON proposal.init_id = init_call.id AND cast(proposal.sdatet as Date) >= '$start_financial_date' 

LEFT JOIN (
    SELECT t1.*
    FROM tblcallevents t1
    INNER JOIN (
        SELECT cid_id, MAX(id) AS max_id
        FROM tblcallevents
        WHERE 
        actiontype_id = 1
        AND actontaken = 'yes' 
        AND purpose_achieved = 'yes'
        AND cast(appointmentdatetime as Date) >= '$start_financial_date'
        GROUP BY cid_id
    ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
) AS last_tblc ON last_tblc.cid_id = init_call.id
LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id
LEFT JOIN user_details u16 ON u16.user_id = last_tblc.user_id
LEFT JOIN user_details u20 ON u20.user_id = last_tblc.user_id

 LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    MAX(te.id) AS last_task_id,
    MAX(te.appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(te.appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(te.appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = init_call.id
 AND last.user_id = $userid


INNER JOIN proposal p2 on p2.init_id = init_call.id AND cast(p2.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND p2.apr IN (0,1)

WHERE 
     $text $filter $userActiveStatus $companyStatusFilter $cmpUserWhereConditions
     $whereConditions
     
     $groupfilter");
    return $query->result(); 
}


public function MoreThenNDaysNoActivityDoneBYUserLineManagerWhereProposalSent($userid,$admin_id_filter,$rm_filter){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }

       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }

    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];

    $sdate = '2025-02-14';
    $edate = date("Y-m-d");

$totalFunnelQuery = $this->db->query("SELECT

(
  /* 0 to 8 */
  COUNT(
    DISTINCT CASE 
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 0 AND 8 
      THEN ic.id ELSE NULL END
  )
  +
  /* 9 to 15 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 9 AND 15
      THEN ic.id ELSE NULL END
  )
  +
  /* 16 to 30 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 16 AND 30
      THEN ic.id ELSE NULL END
  )
  +
  /* 31 to 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 31 AND 50
      THEN ic.id ELSE NULL END
  )
  +
  /* > 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) > 50
      THEN ic.id ELSE NULL END
  )
  +
  /* no activity ever */
  COUNT(
    DISTINCT CASE
      WHEN last.last_event IS NULL THEN ic.id 
      ELSE NULL END
  )
) AS total,



    /* 0 to 8 days (includes future appointments treated as 0) */
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 9 AND 15
        THEN ic.id   -- or last.user_id, or any unique key
        ELSE NULL
    END
) AS task_not_done_in_9_to_15_days,


    /* 16 to 30 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 16 AND 30
        THEN ic.id
        ELSE NULL
    END
) AS task_not_done_in_16_to_30_days,


    /* 31 to 50 days */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
               WHEN last.last_event < 0 THEN 0 
               ELSE last.last_event END) 
         BETWEEN 31 AND 50 
    THEN ic.id  -- or any unique identifier
    ELSE NULL 
  END
) AS task_not_done_in_31_to_50_days,


    /* > 50 days (only non-NULL) */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL
               WHEN last.last_event < 0 THEN 0
               ELSE last.last_event END) > 50
    THEN ic.id   -- use a unique column here
    ELSE NULL
  END
) AS task_not_done_in_more_than_50_days,


    /* never did activity */
   COUNT(
    DISTINCT CASE 
        WHEN last.last_event IS NULL THEN ic.id
        ELSE NULL
    END
    ) AS no_activity_till_date


FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id

 INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)

WHERE
     $text AND u1.status = 'active'
");
$total_funnel = $totalFunnelQuery->result();


    // echo $this->db->last_query();
    // die;



$totalFunnelByUserAssignQuery = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    /* total derived from buckets so it always matches */
    (
  /* 0 to 8 */
  COUNT(
    DISTINCT CASE 
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 0 AND 8 
      THEN ic.id ELSE NULL END
  )
  +
  /* 9 to 15 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 9 AND 15
      THEN ic.id ELSE NULL END
  )
  +
  /* 16 to 30 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 16 AND 30
      THEN ic.id ELSE NULL END
  )
  +
  /* 31 to 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 31 AND 50
      THEN ic.id ELSE NULL END
  )
  +
  /* > 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) > 50
      THEN ic.id ELSE NULL END
  )
  +
  /* no activity ever */
  COUNT(
    DISTINCT CASE
      WHEN last.last_event IS NULL THEN ic.id 
      ELSE NULL END
  )
) AS total,



    /* 0 to 8 days (includes future appointments treated as 0) */
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 9 AND 15
        THEN ic.id   -- or last.user_id, or any unique key
        ELSE NULL
    END
) AS task_not_done_in_9_to_15_days,


    /* 16 to 30 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 16 AND 30
        THEN ic.id
        ELSE NULL
    END
) AS task_not_done_in_16_to_30_days,


    /* 31 to 50 days */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
               WHEN last.last_event < 0 THEN 0 
               ELSE last.last_event END) 
         BETWEEN 31 AND 50 
    THEN ic.id  -- or any unique identifier
    ELSE NULL 
  END
) AS task_not_done_in_31_to_50_days,


    /* > 50 days (only non-NULL) */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL
               WHEN last.last_event < 0 THEN 0
               ELSE last.last_event END) > 50
    THEN ic.id   -- use a unique column here
    ELSE NULL
  END
) AS task_not_done_in_more_than_50_days,


    /* never did activity */
   COUNT(
   DISTINCT CASE 
        WHEN last.last_event IS NULL THEN ic.id 
        ELSE NULL
    END
    ) AS no_activity_till_date

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
  INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)
WHERE
     $text AND u1.status = 'active'
GROUP BY u1.user_id
ORDER BY u1.name ASC");
$total_funnel_by_user_assign = $totalFunnelByUserAssignQuery->result();


// By Status Start


$totalNotDoneinZeroToEightDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
 INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)
WHERE
     $text AND u1.status = 'active'
      AND (
            CASE 
                WHEN last.last_event IS NULL THEN NULL 
                WHEN last.last_event < 0 THEN 0 
                ELSE last.last_event 
            END
        ) BETWEEN 0 AND 8
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinZeroToEightDays = $totalNotDoneinZeroToEightDays->result();

$totalNotDoneinNineToFifteenDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
 INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinNineToFifteenDays = $totalNotDoneinNineToFifteenDays->result();

$totalNotDoneinsixteenToThirtyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
 INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinsixteenToThirtyDays = $totalNotDoneinsixteenToThirtyDays->result();

$totalNotDoneinthirtyOneTofiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
 INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinthirtyOneTofiftyDays = $totalNotDoneinthirtyOneTofiftyDays->result();


$totalNotDoneinMoreThanfiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
  INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanfiftyDays = $totalNotDoneinMoreThanfiftyDays->result();


$totalNotDoneinMoreThanDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
 INNER JOIN proposal on proposal.init_id = ic.id AND cast(proposal.sdatet as DATE) BETWEEN '$sdate' and '$edate' AND proposal.apr IN (0,1)
WHERE
     $text AND u1.status = 'active'
     AND last.last_event IS NULL
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanDays = $totalNotDoneinMoreThanDays->result();

// By Status Close

    $result = [
        'total_funnel'                          => $total_funnel,
        'total_funnel_by_user'                  => $total_funnel_by_user_assign,
        'totalNotDoneinZeroToEightDays'         => $totalNotDoneinZeroToEightDays,
        'totalNotDoneinNineToFifteenDays'       => $totalNotDoneinNineToFifteenDays,
        'totalNotDoneinsixteenToThirtyDays'     => $totalNotDoneinsixteenToThirtyDays,
        'totalNotDoneinthirtyOneTofiftyDays'    => $totalNotDoneinthirtyOneTofiftyDays,
        'totalNotDoneinMoreThanfiftyDays'       => $totalNotDoneinMoreThanfiftyDays,
        'totalNotDoneinMoreThanDays'            => $totalNotDoneinMoreThanDays,
    ];
    return $result;
}




public function MoreThenNDaysNoActivityDoneBYUserLineManagerWhereRPMeetingDone($userid,$admin_id_filter,$rm_filter){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }
    if($admin_id_filter =='all'){
        // $text = "u1.admin_id IN (2,45)";
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
       if(in_array($cuid,[2,45])){
            $text = "AND u1.admin_id IN (2,45)";
        }else if(in_array($cuid,[100000])){
            $text = "AND u1.sadmin_id = '100000'";
        }else{
            $text = "AND u1.admin_id = '$cuid'";
        }
    }else{
        if($rm_filter !== 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "u1.user_id = '$userid'";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "u1.sales_co = '$userid'";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }
        }
    }

       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }

    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];

    $sdate = '2025-02-14';
    $edate = date("Y-m-d");

$totalFunnelQuery = $this->db->query("SELECT

(
  /* 0 to 8 */
  COUNT(
    DISTINCT CASE 
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 0 AND 8 
      THEN ic.id ELSE NULL END
  )
  +
  /* 9 to 15 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 9 AND 15
      THEN ic.id ELSE NULL END
  )
  +
  /* 16 to 30 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 16 AND 30
      THEN ic.id ELSE NULL END
  )
  +
  /* 31 to 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 31 AND 50
      THEN ic.id ELSE NULL END
  )
  +
  /* > 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) > 50
      THEN ic.id ELSE NULL END
  )
  +
  /* no activity ever */
  COUNT(
    DISTINCT CASE
      WHEN last.last_event IS NULL THEN ic.id 
      ELSE NULL END
  )
) AS total,



    /* 0 to 8 days (includes future appointments treated as 0) */
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 9 AND 15
        THEN ic.id   -- or last.user_id, or any unique key
        ELSE NULL
    END
) AS task_not_done_in_9_to_15_days,


    /* 16 to 30 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 16 AND 30
        THEN ic.id
        ELSE NULL
    END
) AS task_not_done_in_16_to_30_days,


    /* 31 to 50 days */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
               WHEN last.last_event < 0 THEN 0 
               ELSE last.last_event END) 
         BETWEEN 31 AND 50 
    THEN ic.id  -- or any unique identifier
    ELSE NULL 
  END
) AS task_not_done_in_31_to_50_days,


    /* > 50 days (only non-NULL) */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL
               WHEN last.last_event < 0 THEN 0
               ELSE last.last_event END) > 50
    THEN ic.id   -- use a unique column here
    ELSE NULL
  END
) AS task_not_done_in_more_than_50_days,


    /* never did activity */
   COUNT(
    DISTINCT CASE 
        WHEN last.last_event IS NULL THEN ic.id 
        ELSE NULL
    END
    ) AS no_activity_till_date


FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id

INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')

WHERE
     $text AND u1.status = 'active'
");
$total_funnel = $totalFunnelQuery->result();


    // echo $this->db->last_query();
    // die;



$totalFunnelByUserAssignQuery = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    /* total derived from buckets so it always matches */
    (
  /* 0 to 8 */
  COUNT(
    DISTINCT CASE 
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 0 AND 8 
      THEN ic.id ELSE NULL END
  )
  +
  /* 9 to 15 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 9 AND 15
      THEN ic.id ELSE NULL END
  )
  +
  /* 16 to 30 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 16 AND 30
      THEN ic.id ELSE NULL END
  )
  +
  /* 31 to 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) BETWEEN 31 AND 50
      THEN ic.id ELSE NULL END
  )
  +
  /* > 50 */
  COUNT(
    DISTINCT CASE
      WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
                 WHEN last.last_event < 0 THEN 0 
                 ELSE last.last_event END) > 50
      THEN ic.id ELSE NULL END
  )
  +
  /* no activity ever */
  COUNT(
    DISTINCT CASE
      WHEN last.last_event IS NULL THEN ic.id 
      ELSE NULL END
  )
) AS total,



    /* 0 to 8 days (includes future appointments treated as 0) */
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,
    COUNT(
        DISTINCT
        CASE
            WHEN (
                CASE
                    WHEN last.last_event IS NULL THEN NULL
                    WHEN last.last_event < 0 THEN 0
                    ELSE last.last_event
                END
            ) BETWEEN 0 AND 8
            THEN ic.id
            ELSE NULL
        END
    ) AS task_not_done_in_0_to_8_days,

    /* 9 to 15 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 9 AND 15
        THEN ic.id   -- or last.user_id, or any unique key
        ELSE NULL
    END
) AS task_not_done_in_9_to_15_days,


    /* 16 to 30 days */
   COUNT(
    DISTINCT
    CASE
        WHEN (
            CASE
                WHEN last.last_event IS NULL THEN NULL
                WHEN last.last_event < 0 THEN 0
                ELSE last.last_event
            END
        ) BETWEEN 16 AND 30
        THEN ic.id
        ELSE NULL
    END
) AS task_not_done_in_16_to_30_days,


    /* 31 to 50 days */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL 
               WHEN last.last_event < 0 THEN 0 
               ELSE last.last_event END) 
         BETWEEN 31 AND 50 
    THEN ic.id  -- or any unique identifier
    ELSE NULL 
  END
) AS task_not_done_in_31_to_50_days,


    /* > 50 days (only non-NULL) */
    COUNT(
  DISTINCT CASE 
    WHEN (CASE WHEN last.last_event IS NULL THEN NULL
               WHEN last.last_event < 0 THEN 0
               ELSE last.last_event END) > 50
    THEN ic.id   -- use a unique column here
    ELSE NULL
  END
) AS task_not_done_in_more_than_50_days,


    /* never did activity */
   COUNT(
    DISTINCT CASE 
        WHEN last.last_event IS NULL THEN ic.id
        ELSE NULL
    END
    ) AS no_activity_till_date

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')
WHERE
     $text AND u1.status = 'active'
GROUP BY u1.user_id
ORDER BY u1.name ASC");
$total_funnel_by_user_assign = $totalFunnelByUserAssignQuery->result();


// By Status Start


$totalNotDoneinZeroToEightDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')
WHERE
     $text AND u1.status = 'active'
      AND (
            CASE 
                WHEN last.last_event IS NULL THEN NULL 
                WHEN last.last_event < 0 THEN 0 
                ELSE last.last_event 
            END
        ) BETWEEN 0 AND 8
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinZeroToEightDays = $totalNotDoneinZeroToEightDays->result();

$totalNotDoneinNineToFifteenDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinNineToFifteenDays = $totalNotDoneinNineToFifteenDays->result();

$totalNotDoneinsixteenToThirtyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinsixteenToThirtyDays = $totalNotDoneinsixteenToThirtyDays->result();

$totalNotDoneinthirtyOneTofiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinthirtyOneTofiftyDays = $totalNotDoneinthirtyOneTofiftyDays->result();


$totalNotDoneinMoreThanfiftyDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id
INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')
WHERE
     $text AND u1.status = 'active'
     AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanfiftyDays = $totalNotDoneinMoreThanfiftyDays->result();


$totalNotDoneinMoreThanDays = $this->db->query("SELECT

    u1.user_id,
    u1.name AS user_name,
    u1.user_cluster_zone,
    COUNT(DISTINCT ic.id) AS total,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 1 THEN ic.id END) AS open,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 2 THEN ic.id END) AS reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 3 THEN ic.id END) AS tentative,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 4 THEN ic.id END) AS will_do_later,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 5 THEN ic.id END) AS not_interested,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 6 THEN ic.id END) AS positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 7 THEN ic.id END) AS closure,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 8 THEN ic.id END) AS open_rpem,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 9 THEN ic.id END) AS very_positive,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 10 THEN ic.id END) AS ttd_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 11 THEN ic.id END) AS wno_reachout,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 12 THEN ic.id END) AS positive_nap,
    COUNT(DISTINCT CASE WHEN ic.cstatus = 13 THEN ic.id END) AS very_positive_nap,
    COUNT(DISTINCT CASE WHEN (ic.cstatus = 14) THEN ic.id END) AS on_boarded

FROM init_call ic

LEFT JOIN user_details u1 
ON u1.user_id IN (ic.clm_id, ic.apst, ic.acm_co_id, ic.rm_east_co_id,ic.rm_north_co_id,ic.rm_north_co_id,ic.ash_nae_co_id,ic.ash_w_co_id,ic.ash_s_co_id)

/* single scan of tblcallevents to get last appointment + days difference */
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    te.id as last_task_id,
    MAX(appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(appointmentdatetime)) AS last_event
  FROM tblcallevents te
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = ic.id
 AND last.user_id = u1.user_id

INNER JOIN tblcallevents tblc 
    ON tblc.cid_id = ic.id 
    AND cast(tblc.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblc.actiontype_id IN (3,4,17,22) 
    AND tblc.mtype IN ('RP','RPClose','Change RP')

WHERE
     $text AND u1.status = 'active'
     AND last.last_event IS NULL
    GROUP BY u1.user_id
    ORDER BY u1.name ASC
");
$totalNotDoneinMoreThanDays = $totalNotDoneinMoreThanDays->result();

// By Status Close

    $result = [
        'total_funnel'                          => $total_funnel,
        'total_funnel_by_user'                  => $total_funnel_by_user_assign,
        'totalNotDoneinZeroToEightDays'         => $totalNotDoneinZeroToEightDays,
        'totalNotDoneinNineToFifteenDays'       => $totalNotDoneinNineToFifteenDays,
        'totalNotDoneinsixteenToThirtyDays'     => $totalNotDoneinsixteenToThirtyDays,
        'totalNotDoneinthirtyOneTofiftyDays'    => $totalNotDoneinthirtyOneTofiftyDays,
        'totalNotDoneinMoreThanfiftyDays'       => $totalNotDoneinMoreThanfiftyDays,
        'totalNotDoneinMoreThanDays'            => $totalNotDoneinMoreThanDays,
    ];
    return $result;
}





public function MoreThenNDaysNoActivityDoneBYUserCompanyDetailsLineManagerWhereRPMeetingDone($tarFilter,$userid,$userwise,$status){

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    if($userid == 'all'){
        $text = "u1.admin_id IN(2,45)";
    }else{

    $udetail = $this->Menu_model->get_userbyid($userid); 
    $type_id = $udetail[0]->type_id;
    
    $cmpUserWhereConditions = '';

   if($type_id == 1){
        $text = "u1.sadmin_id = $userid";
    }else if($type_id == 2){
        $text = "u1.admin_id = $userid";
    }else if($type_id == 15){
        $text = "u1.sales_co = $userid";
    }else{

        if($type_id == 4){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.apst = '$userid'";
            }else{
                $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
            }
        }else if($type_id == 13){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.clm_id = '$userid'";
            }else{
                $text = "(u1.aadmin = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 19){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_nae_co_id = '$userid'";
            }else{
                $text = "(u1.ash_nae_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 20){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_w_co_id = '$userid'";
            }else{
                $text = "(u1.ash_w_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 21){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.ash_s_co_id = '$userid'";
            }else{
                $text = "(u1.ash_s_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 22){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.rm_east_co_id = '$userid'";
            }else{
                $text = "(u1.rm_east_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 23){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.rm_east_co_id = '$userid'";
            }else{
                $text = "(u1.rm_north_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 24){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
                $cmpUserWhereConditions = " AND init_call.acm_co_id = '$userid'";
            }else{
                $text = "(u1.acm_co = $userid || u1.user_id = $userid)"; 
            }
        }else if($type_id == 25){
            if($userwise =='userwise'){
                $text = "u1.user_id = $userid";
            }else{
                $text = "(u1.user_id = $userid)"; 
            }
        }else{
             $text = "u1.user_id = $userid";
        }
    }
}

    $mainBDFilterQuery = " IN (init_call.mainbd,init_call.clm_id, init_call.apst, init_call.acm_co_id, init_call.rm_east_co_id,init_call.rm_north_co_id,init_call.rm_north_co_id,init_call.ash_nae_co_id,init_call.ash_w_co_id,init_call.ash_s_co_id)";

    if($type_id == 25){
        $mainBDFilterQuery = ' IN (init_call.creator_id)';
    }

    $curFinancialDate           = $this->Menu_model->getFinancialYearRange();
    $start_financial_date       = $curFinancialDate['start_date'];
    $end_financial_date         = $curFinancialDate['end_date'];
    $start_financial_date       = '2025-02-14';
    $start_financial_date_year  = new DateTime($start_financial_date);
    $year                       = $start_financial_date_year->format('Y');
    $cfData                     = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter             = "Q".$cfData['quarter'];
    $groupfilter                = '';
    $userActiveStatus           = " AND u1.status = 'active'";

    $sdate                      = '2025-02-14';
    $edate                      = date("Y-m-d");
 
    $whereConditions = '';

    if($tarFilter == 'task_not_done_in_0_to_8_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 0 AND 8";
    }
    else if($tarFilter == 'task_not_done_in_9_to_15_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 9 AND 15";
    }
    else if($tarFilter == 'task_not_done_in_16_to_30_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 16 AND 30";
    }
    else if($tarFilter == 'task_not_done_in_31_to_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) BETWEEN 31 AND 50";
    }
    else if($tarFilter == 'task_not_done_in_more_than_50_days'){
        $whereConditions = "AND (
            CASE WHEN last.last_event IS NULL THEN NULL WHEN last.last_event < 0 THEN 0 ELSE last.last_event END
        ) > 50";
    }
    else if($tarFilter == 'no_activity_till_date'){
        $whereConditions = "AND last.last_event IS NULL";
    }

    $companyStatusFilter = '';
    $statusMap = [
        'open'              => 1,
        'reachout'          => 2,
        'tentative'         => 3,
        'will_do_later'     => 4,
        'not_interested'    => 5,
        'positive'          => 6,
        'closure'           => 7,
        'open_rpem'         => 8,
        'very_positive'     => 9,
        'ttd_reachout'      => 10,
        'wno_reachout'      => 11,
        'positive_nap'      => 12,
        'very_positive_nap' => 13,
        'on_boarded'        => 14
    ];

    if(isset($statusMap[$status])) {
        $companyStatusFilter = " AND init_call.cstatus = ".$statusMap[$status];
    }

    if($mtypes == 'total'){
        $filter = '';
    }

   $groupfilter    = 'GROUP BY company_master.id';
   $query = $this->db->query("SELECT
    company_master.id AS cid,
    company_master.compname,
    company_master.state as company_state,
    company_master.district as company_district,
    company_master.city as company_city,
    company_master.address as company_address,
    init_call.createDate AS created_date,
    s1.name as current_status,
    s2.name as last_status,
    u1.name as mainbd_name,
    u2.name as cluster_manager_name,
    u3.name as pst_name,
    u4.name as ash_nae_co_id_name,
    u5.name as ash_w_co_id_name,
    u6.name as ash_s_co_id_name,
    u7.name as rm_east_co_id_name,
    u8.name as rm_north_co_id_name,
    u9.name as acm_co_id_name,
    init_call.topspender,
    init_call.upsell_client,
    init_call.focus_funnel,
    init_call.anchor_clients,
    init_call.in_quarter,
    init_call.keycompany,
    init_call.pkclient,
    init_call.priorityc,
    init_call.potential,
    init_call.q1_twetenty_closure_funnel,
    init_call.potential_funnel_for_fy,
    init_call.to_be_nurtured_for_fy,
    init_call.fifity_new_lead_funnel,
    partner_master.name as partner_name,
    cluster.clustername,
    cluster.travelType as travelType,
    cluster.id as cluster_id,
    u10.name as travel_cluster_create_name,
    tblc.actiontype_id as task_action_id,
    action.name as compaany_createby_task_name,
    tblc.mtype as compaany_createby_mtype,
    init_call.after_task as compaany_after_task,
  CONCAT_WS(' | ',
        CONCAT('<b>Name :</b>', ccm.contactperson),
        CONCAT('<b>Email : </b>', ccm.emailid),
        CONCAT('<b>Phone : </b>', ccm.phoneno),
        CONCAT('<b>Type : </b>', ccm.type)
    ) AS contact_details,
    last_tblc.id as last_tblc_id,
    last_tblc.nextCFID as last_tblc_nextCFID,
    u20.name as last_tblc_call_by_user,
    last_tblc.remarks as last_tblc_remarks,
    last_tblc.delete_request as last_tblc_delete_request,
    last_tblc.actiontype_id as last_tblc_actiontype_id,
    last_tblc.special_remarks as last_tblc_special_remarks,
    last_tblc.mom as last_tblc_mom, 
    last_tblc.mtype as last_tblc_mtype, 
    last_tblc.mom_remarks as last_tblc_mom_remarks, 
    last_tblc.appointmentdatetime as last_tblc_date, 
    u16.name as last_tblc_task_user, 
    a2.name as last_tblc_task,
    meet.id as last_meet_id,
    meet.mtype as last_meet_mtype,
    u11.name as last_meet_username,
    mom_data.approved_status as mom_approved_status,
    proposal.proattach as proposal_attach,
    proposal.apr as proposal_apr,
    proposal.id as proposal_id,
    proposal.tid as proposal_tid,
    cbm.potentional_client as cbm_potentional_client,
    init_call.super_admin as super_admin,
    u12.name as super_admin_name,
    u13.name as assign_support_name,
    u14.name as leads_main_bd_name,
    u1.user_cluster_zone,
    last_task_id as last_tblc_id,
    last.last_event as task_not_done_in_days
FROM
    init_call
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN company_contact_master ccm ON ccm.company_id = company_master.id  AND ccm.type = 'primary'
LEFT JOIN partner_master ON partner_master.id = company_master.partnerType_id
LEFT JOIN status s1 ON s1.id = init_call.cstatus
LEFT JOIN status s2 ON s2.id = init_call.lstatus
LEFT JOIN user_details u1 ON u1.user_id $mainBDFilterQuery
LEFT JOIN user_details u2 ON u2.user_id = init_call.clm_id
LEFT JOIN user_details u3 ON u3.user_id = init_call.apst
LEFT JOIN user_details u4 ON u4.user_id = init_call.ash_nae_co_id
LEFT JOIN user_details u5 ON u5.user_id = init_call.ash_w_co_id
LEFT JOIN user_details u6 ON u6.user_id = init_call.ash_s_co_id
LEFT JOIN user_details u7 ON u7.user_id = init_call.rm_east_co_id
LEFT JOIN user_details u8 ON u8.user_id = init_call.rm_north_co_id
LEFT JOIN user_details u9 ON u9.user_id = init_call.acm_co_id
LEFT JOIN cluster ON cluster.id = init_call.cluster_id
LEFT JOIN user_details u10 ON u10.user_id = cluster.user_id
LEFT JOIN user_details u12 ON u12.user_id = init_call.super_admin
LEFT JOIN user_details u13 ON u13.user_id = init_call.assign_support
LEFT JOIN user_details u14 ON u14.user_id = init_call.mainbd
LEFT JOIN tblcallevents tblc ON tblc.id = init_call.after_task
LEFT JOIN action ON action.id = tblc.actiontype_id
LEFT JOIN barginmeeting cbm ON cbm.tid = tblc.id
LEFT JOIN tblcallevents meet ON meet.cid_id = init_call.id  AND meet.nextCFID !=0 AND meet.actiontype_id IN(3,4,22) 
AND cast(meet.appointmentdatetime as Date) >= '$start_financial_date' 
-- AND YEAR(meet.appointmentdatetime) = '$year' 
AND (meet.mtype = 'RP' OR meet.mtype = 'RPClose' OR meet.mtype = 'Change RP')
LEFT JOIN user_details u11 ON u11.user_id = meet.user_id
LEFT JOIN tblcallevents mom_tblc ON mom_tblc.aftertask = meet.id AND mom_tblc.actiontype_id = 6
LEFT JOIN mom_data ON mom_data.tid = mom_tblc.id 
LEFT JOIN proposal ON proposal.init_id = init_call.id AND cast(proposal.sdatet as Date) >= '$start_financial_date' 

LEFT JOIN (
    SELECT t1.*
    FROM tblcallevents t1
    INNER JOIN (
        SELECT cid_id, MAX(id) AS max_id
        FROM tblcallevents
        WHERE
        actiontype_id = 1
        AND actontaken = 'yes' 
        AND purpose_achieved = 'yes'
        AND cast(appointmentdatetime as Date) >= '$start_financial_date'
        GROUP BY cid_id
    ) t2 ON t1.cid_id = t2.cid_id AND t1.id = t2.max_id
) AS last_tblc ON last_tblc.cid_id = init_call.id 

LEFT JOIN action a2 ON a2.id = last_tblc.actiontype_id
LEFT JOIN user_details u16 ON u16.user_id = last_tblc.user_id
LEFT JOIN user_details u20 ON u20.user_id = last_tblc.user_id
LEFT JOIN (
  SELECT
    te.cid_id,
    te.user_id,
    MAX(te.id) AS last_task_id,
    MAX(te.appointmentdatetime) AS last_appt,
    DATEDIFF(CURDATE(), MIN(te.appointmentdatetime)) AS min_last_event,
    DATEDIFF(CURDATE(), MAX(te.appointmentdatetime)) AS last_event
  FROM tblcallevents te
  -- AND te.nextCFID !=0
  GROUP BY te.cid_id, te.user_id
) last
  ON last.cid_id = init_call.id
 AND last.user_id = $userid

INNER JOIN tblcallevents tblcm 
    ON tblcm.cid_id = init_call.id 
    AND cast(tblcm.appointmentdatetime as DATE) BETWEEN '$sdate' and '$edate'
    AND tblcm.actiontype_id IN (3,4,17,22) 
    AND tblcm.mtype IN ('RP','RPClose','Change RP')

WHERE 
     $text $filter $userActiveStatus $companyStatusFilter $cmpUserWhereConditions
     $whereConditions
     
     $groupfilter");
    return $query->result(); 
}







public function GetTodaysTaskPlannedByOperationTeam($uid,$sdate,$edate){

    $db3        = $this->load->database('db3', TRUE);
    $utype      = $this->Menu_model->get_userbyid($uid);
    $userTypeid = $utype[0]->type_id;
    
    if($userTypeid ==1){
        $userfilter  = " (u1.sadmin_id = '$uid' OR u1.user_id = '$uid')";
    }else if($userTypeid ==2){
        $userfilter  = " (u1.admin_id = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid == 4){
        $userfilter  = " (u1.pst_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid == 13){
        $userfilter  = " (u1.aadmin = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==15){
        $userfilter  = " (u1.sales_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==19){
        $userfilter  = " (u1.ash_nae_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==20){
        $userfilter  = " (u1.ash_w_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==21){ 
        $userfilter  = " (u1.ash_s_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==22){
        $userfilter  = " (u1.rm_east_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==23){
        $userfilter  = " (u1.rm_north_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==24){
        $userfilter  = " (u1.acm_co = '$uid' OR u1.user_id = '$uid')";
    }elseif($userTypeid ==25){
        $userfilter  = " u1.user_id = '$uid' ";
    }else{
         $userfilter  = " u1.user_id = '$uid' ";
    }


    $query      = $this->db->query("SELECT name,user_id FROM user_details u1 WHERE $userfilter");
    $userData   =  $query->result();

    $user_ids = array_map(function($item){
            return $item->user_id;
        }, $userData);

    
    $user_ids_list = implode(',', $user_ids);


    $opps_query         = $db3->query("SELECT projectcode FROM client_handover WHERE projectcode IS NOT NULL AND bd_id IN ($user_ids_list)");
    $projectCodeData    =  $opps_query->result();

    $projectcodes = array_map(function($item){
        return "'" . $item->projectcode . "'";
    }, $projectCodeData);

    $projectcodes_list = implode(',', $projectcodes);

    $taskQuery =  $db3->query("SELECT
                tblcallevents.id as task_id,
                tblcallevents.fwd_date,
                tblcallevents.project_code,
                tblcallevents.appointment_datetime,
                tblcallevents.initiate_datetime,
                tblcallevents.updated_datetime,
                tblcallevents.selectby,
                tblcallevents.task_assigned_date,
                tblcallevents.sid,
                tblcallevents.rsid,
                tblcallevents.comments,
                tblcallevents.comment_by,
                tblcallevents.target_date,
                tblcallevents.pi_target_date,
                tblcallevents.targetstatus,
                tblcallevents.task_status,
                ta.id as task_action_id,
                ta.tasktype,
                ta.taskname,
                COALESCE(spdr.sname, spd.sname) AS sname,
                u1.fullname as task_username,
                u2.fullname as task_assigned_by,
                s1.name as task_time_status,
                s2.name as new_status,
                s3.name as target_status,
                COALESCE(NULLIF(spd.sname, ''), spdr.sname) AS task_school

                FROM
                `tblcallevents`
                LEFT JOIN task_action ta on ta.id = tblcallevents.task_action
                LEFT JOIN spd on spd.id = tblcallevents.sid
                LEFT JOIN spd_request spdr on spdr.id = tblcallevents.rsid
                LEFT JOIN user_detail u1 on u1.id = tblcallevents.user_id
                LEFT JOIN user_detail u2 on u2.id = tblcallevents.assigned_by
                LEFT JOIN status s1 on s1.id = tblcallevents.status_id
                LEFT JOIN status s2 on s2.id = tblcallevents.nstatus_id
                LEFT JOIN status s3 on s3.id = tblcallevents.targetstatus
                WHERE
                tblcallevents.project_code IN ($projectcodes_list)
                AND DATE(tblcallevents.appointment_datetime) BETWEEN '$sdate' AND '$edate'
                ");

            $projectTaskCodeData   =  $taskQuery->result();

    return $projectTaskCodeData;

}












public function GetAllStatusChangedRequiredRequest($userid,$sdate,$edate){
    
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;
    
    if($type_id == 1){
        $text = "(u1.sadmin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 2){
        $text = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }else if($type_id == 3){
        $text = "u1.user_id = $userid";
    }else if($type_id == 4){
        $text = "(u1.pst_co = $userid || u1.user_id = $userid)";
    }else if($type_id == 13){
        $text = "(u1.aadmin = $userid || u1.user_id = $userid)";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid')";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = $userid || u1.user_id = $userid)";
    }else{
        $text  = "(u1.admin_id = $userid || u1.user_id = $userid)";
    }


    $query = $this->db->query("SELECT
	u1.name as user_id,
    u1.name,
    u2.name as request_accept_by_name,
    status_changed_required.*,
    company_master.id as cid,
    company_master.compname,
    s1.name as from_status,
    s1.name as to_status,
    s3.name as current_status_name,
    action.name as action_name,
    tblcallevents.remarks,
    tblcallevents.actontaken,
    tblcallevents.purpose_achieved
FROM
    `status_changed_required`
    LEFT JOIN user_details u1 on u1.user_id = status_changed_required.by_id
    LEFT JOIN user_details u2 on u2.user_id = status_changed_required.request_accept_by
    LEFT JOIN tblcallevents on tblcallevents.id = status_changed_required.task_id
    LEFT JOIN init_call on init_call.id = tblcallevents.cid_id
    LEFT JOIN company_master on company_master.id = init_call.cmpid_id
    LEFT JOIN status s1 on s1.id = status_changed_required.current_status
    LEFT JOIN status s2 on s2.id = status_changed_required.change_on_status
    LEFT JOIN status s3 on s3.id = init_call.cstatus
    LEFT JOIN action on action.id = tblcallevents.actiontype_id
    WHERE $text 
    ");
  $totalTasksUserByDatas = $query->result();

return $totalTasksUserByDatas;

}














    public function RMCMCallingOutcomeANDRPProposalConversionDatas($target_userid,$admin_id_filter,$rm_filter,$sdate,$edate){
    $userid = $target_userid;
    

    $udetail = $this->Menu_model->get_userbyid($userid);
    $type_id = $udetail[0]->type_id;

    if($type_id ==1){
        $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 2){
        $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 3){
        $text = "(u1.user_id = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 4){
        $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 13){
        $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
    }else if($type_id == 15){
        $text = "(u1.sales_co = '$userid' || u1.user_id = $userid)";
    }elseif($type_id == 19){
        $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
    }else if($type_id == 20){
        $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 21){
        $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 22){
        $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 23){
        $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
    }else if($type_id == 24){
        $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
    }else{
        $text = "u1.admin_id = '$userid'";
    }



    if($admin_id_filter =='all'){
        $cuser = $this->session->userdata('user');
        $cuid = $cuser['user_id'];
        if(in_array($cuid,[2,45])){
            $text = "u1.admin_id IN (2,45)";
        }else{
            $text = "u1.sadmin_id = '$cuid'";
        }
    }else{
     
        if($rm_filter != 'all'){
                $udetail = $this->Menu_model->get_userbyid($rm_filter);
                $type_id = $udetail[0]->type_id;
                
                if($type_id == 1){
                    $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
                }if($type_id == 2){
                    $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 3){
                    $text = "(u1.user_id = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 4){
                    $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 13){
                    $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 15){
                    $text = "(u1.sales_co = '$userid' || u1.user_id = '$userid')";
                }elseif($type_id == 19){
                    $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid')";
                }else if($type_id == 20){
                    $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid' )";
                }else if($type_id == 21){
                    $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid'";
                }else if($type_id == 22){
                    $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid'";
                }else if($type_id == 23){
                    $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid')";
                }else if($type_id == 24){
                    $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
                }else{
                    $text = "u1.admin_id = '$userid' ";
                }
        }
    }


        if($mtypes == 'total_present'){
            $filter = 'AND user_day.id IS NOT NULL';
        }else if($mtypes == 'total_work_from_Office'){
             $filter = 'AND user_day.id IS NOT NULL AND uwf.ID = 1';
        }else if($mtypes == 'total_work_from_field'){
            $filter = 'AND user_day.id IS NOT NULL AND uwf.ID = 2';
        }else if($mtypes == 'total_work_from_field_Office'){
            $filter = 'AND user_day.id IS NOT NULL AND uwf.ID = 3';
        }
        $query = $this->db->query("SELECT
    cm.id AS cid,
    cm.compname,
    s1.name as current_status,
    u1.name as mainbd_name,

    COUNT(DISTINCT ce.id) AS total_meeting,
    COUNT(DISTINCT p.id) AS proposal_shared,

    COUNT(DISTINCT t1.id) AS total_planned_call_after_first_rp,
    COUNT(
        DISTINCT CASE
            WHEN t1.actontaken = 'yes'
             AND t1.purpose_achieved = 'yes'
            THEN t1.id
        END
    ) AS total_call_connect_after_first_rp,

    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.user_id = ic.mainbd
            THEN t1.id
        END
    ) AS total_planned_call_after_first_rp_by_mainbd,
    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.actontaken = 'yes'
             AND t1.purpose_achieved = 'yes'
             AND t1.user_id = ic.mainbd
            THEN t1.id
        END
    ) AS total_call_connect_after_first_rp_by_mainbd,

    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 24
            THEN t1.id
        END
    ) AS total_planned_call_after_first_rp_by_acm,
    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.actontaken = 'yes'
             AND t1.purpose_achieved = 'yes'
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 24
            THEN t1.id
        END
    ) AS total_call_connect_after_first_rp_by_acm,

    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 13
            THEN t1.id
        END
    ) AS total_planned_call_after_first_rp_by_cm,
    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.actontaken = 'yes'
             AND t1.purpose_achieved = 'yes'
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 13
            THEN t1.id
        END
    ) AS total_call_connect_after_first_rp_by_cm,

    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 4
            THEN t1.id
        END
    ) AS total_planned_call_after_first_rp_by_pst,
    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.actontaken = 'yes'
             AND t1.purpose_achieved = 'yes'
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 4
            THEN t1.id
        END
    ) AS total_call_connect_after_first_rp_by_pst,


    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 22
            THEN t1.id
        END
    ) AS total_planned_call_after_first_rp_by_rm,
    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.actontaken = 'yes'
             AND t1.purpose_achieved = 'yes'
             AND t1.user_id != ic.mainbd
             AND u3.type_id = 22
            THEN t1.id
        END
    ) AS total_call_connect_after_first_rp_by_rm,

    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.user_id != ic.mainbd
             AND u3.type_id IN (1,2)
            THEN t1.id
        END
    ) AS total_planned_call_after_first_rp_by_admin,
    COUNT(
        DISTINCT CASE
            WHEN t1.id IS NOT NULL
             AND t1.actontaken = 'yes'
             AND t1.purpose_achieved = 'yes'
             AND t1.user_id != ic.mainbd
             AND u3.type_id IN (1,2)
            THEN t1.id
        END
    ) AS total_call_connect_after_first_rp_by_admin,


    COUNT(DISTINCT t2.id) AS total_planned_call_after_first_proposal,
    COUNT(
        DISTINCT CASE
            WHEN t2.actontaken = 'yes'
             AND t2.purpose_achieved = 'yes'
            THEN t2.id
        END
    ) AS total_call_connect_after_first_proposal

FROM tblcallevents ce

LEFT JOIN init_call ic
    ON ic.id = ce.cid_id

LEFT JOIN company_master cm
    ON cm.id = ic.cmpid_id

LEFT JOIN company_contact_master ccm
    ON ccm.company_id = cm.id
   AND ccm.type = 'primary'

LEFT JOIN proposal p
    ON p.init_id = ic.id
   AND p.apr IN (0,1)
   AND DATE(p.sdatet) BETWEEN '$sdate' AND '$edate'

-- Calls after first RP
LEFT JOIN tblcallevents t1
    ON t1.cid_id = ic.id
   AND DATE(t1.appointmentdatetime) > DATE(ce.appointmentdatetime)
   AND t1.actiontype_id = 1

-- Calls after first Proposal
LEFT JOIN tblcallevents t2
    ON t2.cid_id = ic.id
   AND DATE(t2.appointmentdatetime) > DATE(p.sdatet)
   AND t2.actiontype_id = 1
LEFT JOIN status s1 ON s1.id = ic.cstatus
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ce.user_id
LEFT JOIN user_details u3 ON u3.user_id = t1.user_id
LEFT JOIN user_details u4 ON u4.user_id = t2.user_id


WHERE
    ce.actiontype_id IN (3,4,17,22)
    AND DATE(ce.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
    AND ce.nextCFID <> 0
    AND ce.mtype IN ('RP','RPClose','Change RP')
    AND $text
GROUP BY cm.id");
        $data = $query->result();
        return $data;
    }




    public function RMCMCallingOutcomeANDRPProposalConversionDatasLists($cid,$sdate,$edate,$rtype){
    

    $grpupBYFilter  = "GROUP BY cm.id";
    $orderBTFilter  = "";
    $actiontype_id  = " AND ce.actiontype_id IN (3,4,17,22)";
    $mtype          = "";
    $extraFilter    = "";
    

        if($rtype == 'total_meeting'){

            $grpupBYFilter  = "GROUP BY ce.id";
            $orderBTFilter  = "ORDER BY ce.id DESC";
            $mtype          = " AND ce.mtype IN ('RP','RPClose','Change RP')";

        }else if($rtype == 'proposal_shared'){

            $grpupBYFilter  = "GROUP BY ce.id";
            $orderBTFilter  = "ORDER BY ce.id DESC";
            $actiontype_id  = " AND ce.actiontype_id = 7";

        }
        
        else if($rtype == 'total_planned_call_after_first_rp'){

            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }

        else if($rtype == 'total_call_connect_after_first_rp'){
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";
            $actiontype_id  = " AND ce.actiontype_id = 7";
            $extraFilter  = " AND t1.actontaken = 'yes' AND t1.purpose_achieved = 'yes'";
        }

        else if($rtype == 'total_planned_call_after_first_rp_by_mainbd'){

            $extraFilter  = " AND (t1.user_id = ic.mainbd)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }
        else if($rtype == 'total_call_connect_after_first_rp_by_mainbd'){

            $extraFilter  = " AND t1.actontaken = 'yes' AND t1.purpose_achieved = 'yes' AND t1.user_id = ic.mainbd";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }

        else if($rtype == 'total_planned_call_after_first_rp_by_acm'){

            $extraFilter  = " AND (t1.user_id != ic.mainbd AND u3.type_id = 24)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }
        else if($rtype == 'total_call_connect_after_first_rp_by_acm'){

            $extraFilter  = " AND t1.actontaken = 'yes' AND t1.purpose_achieved = 'yes' AND (t1.user_id != ic.mainbd AND u3.type_id = 24)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }
        else if($rtype == 'total_planned_call_after_first_rp_by_cm'){

            $extraFilter  = " AND (t1.user_id != ic.mainbd AND u3.type_id = 13)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }
        else if($rtype == 'total_call_connect_after_first_rp_by_cm'){

            $extraFilter  = " AND t1.actontaken = 'yes' AND t1.purpose_achieved = 'yes' AND (t1.user_id != ic.mainbd AND u3.type_id = 13)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }

        else if($rtype == 'total_planned_call_after_first_rp_by_pst'){

            $extraFilter  = " AND (t1.user_id != ic.mainbd AND u3.type_id = 4)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }
        else if($rtype == 'total_call_connect_after_first_rp_by_pst'){

            $extraFilter  = " AND t1.actontaken = 'yes' AND t1.purpose_achieved = 'yes' AND (t1.user_id != ic.mainbd AND u3.type_id = 4)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }

        else if($rtype == 'total_planned_call_after_first_rp_by_rm'){

            $extraFilter  = " AND (t1.user_id != ic.mainbd AND u3.type_id = 22)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }
        else if($rtype == 'total_call_connect_after_first_rp_by_rm'){

            $extraFilter  = " AND t1.actontaken = 'yes' AND t1.purpose_achieved = 'yes' AND (t1.user_id != ic.mainbd AND u3.type_id = 22)";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }

         else if($rtype == 'total_planned_call_after_first_rp_by_admin'){

            $extraFilter  = " AND (t1.user_id != ic.mainbd AND u3.type_id IN (1,2))";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }
        else if($rtype == 'total_call_connect_after_first_rp_by_admin'){

            $extraFilter  = " AND t1.actontaken = 'yes' AND t1.purpose_achieved = 'yes' AND (t1.user_id != ic.mainbd AND u3.type_id IN (1,2))";
            $grpupBYFilter  = "GROUP BY t1.id";
            $orderBTFilter  = "ORDER BY t1.id DESC";

        }



        else if($rtype == 'total_planned_call_after_first_proposal'){

            $grpupBYFilter  = "GROUP BY t2.id";
            $orderBTFilter  = "ORDER BY t2.id DESC";

        }
        else if($rtype == 'total_call_connect_after_first_proposal'){
            $grpupBYFilter  = "GROUP BY t2.id";
            $orderBTFilter  = "ORDER BY t2.id DESC";
            $extraFilter  = " AND t2.actontaken = 'yes' AND t2.purpose_achieved = 'yes'";
        }


         

        $query = $this->db->query("SELECT

    cm.id AS cid,
    cm.compname,
    u1.name as mainbd_name,

    u2.name as task_by_name,

    ce.appointmentdatetime,
    ce.actontaken,
    ce.purpose_achieved,
    ce.remarks,
    ce.special_remarks,

--    Calls after first RP
    t1.appointmentdatetime as call_after_first_rp,
    t1.actontaken as call_after_first_rp_actontaken,
    t1.purpose_achieved as call_after_first_rp_purpose_achieved,
    t1.remarks as call_after_first_rp_remarks,
    t1.special_remarks as call_after_first_rp_special_remarks,
    u3.name as call_after_first_rp_task_by_name,
    a2.name as call_after_first_rp_task_name,
    s4.name as call_after_first_rp_task_planned_on_status,
    s5.name as call_after_first_rp_update_on_status,

--    Calls after first Proposal
    t2.appointmentdatetime as call_after_first_proposal,
    t2.actontaken as call_after_first_proposal_actontaken,
    t2.purpose_achieved as call_after_first_proposal_purpose_achieved,
    t2.remarks as call_after_first_proposal_remarks,
    t2.special_remarks as call_after_first_proposal_special_remarks,
    u4.name as call_after_first_proposal_task_by_name,
    a3.name as call_after_first_proposal_task_name,
    s4.name as call_after_first_proposal_task_planned_on_status,
    s5.name as call_after_first_proposal_update_on_status,


    a1.name as task_name,
    ce.id as task_id,
    s1.name as current_status,
    s3.name as update_on_status,
    s2.name as task_planned_on_status,
    p.id as proposal_id,
    ce.mtype


FROM tblcallevents ce

LEFT JOIN init_call ic ON ic.id = ce.cid_id
LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
LEFT JOIN company_contact_master ccm ON ccm.company_id = cm.id AND ccm.type = 'primary'
LEFT JOIN proposal p ON p.init_id = ic.id AND p.apr IN (0,1) AND DATE(p.sdatet) BETWEEN '$sdate' AND '$edate'
-- Calls after first RP
LEFT JOIN tblcallevents t1 ON t1.cid_id = ic.id AND DATE(t1.appointmentdatetime) > DATE(ce.appointmentdatetime) AND t1.actiontype_id = 1
-- Calls after first Proposal
LEFT JOIN tblcallevents t2 ON t2.cid_id = ic.id AND DATE(t2.appointmentdatetime) > DATE(p.sdatet) AND t2.actiontype_id = 1
LEFT JOIN user_details u1 ON u1.user_id = ic.mainbd
LEFT JOIN user_details u2 ON u2.user_id = ce.user_id
LEFT JOIN user_details u3 ON u3.user_id = t1.user_id
LEFT JOIN user_details u4 ON u4.user_id = t2.user_id
LEFT JOIN status s1 ON s1.id = ic.cstatus
LEFT JOIN status s2 ON s2.id = ce.status_id
LEFT JOIN status s3 ON s3.id = ce.nstatus_id

LEFT JOIN status s4 ON s4.id = t1.status_id
LEFT JOIN status s5 ON s5.id = t1.nstatus_id

LEFT JOIN status s6 ON s6.id = t2.status_id
LEFT JOIN status s7 ON s7.id = t2.nstatus_id

LEFT JOIN action a1 ON a1.id = ce.actiontype_id
LEFT JOIN action a2 ON a2.id = t1.actiontype_id
LEFT JOIN action a3 ON a3.id = t2.actiontype_id

WHERE
    ce.id is not null
    $actiontype_id
    AND DATE(ce.appointmentdatetime) BETWEEN '$sdate' AND '$edate'
    AND ce.nextCFID <> 0
    $mtype
    AND cm.id = $cid
    $extraFilter
    $grpupBYFilter 
    $orderBTFilter
    

");
        $data = $query->result();
        return $data;
    }
    





// ************************ Start To Get BD Wise Review Report Details **************************



    public function ReviewReportBDwiseDetails($userid,$sdate,$edate,$reviewtype){

    $reviewtypeFilter = '';
    if($reviewtype !== 'all'){
        $reviewtypeFilter = " AND  bd_wise_reviews.review_type_id = '$reviewtype'";
    }
    
        $udetail = $this->Menu_model->get_userbyid($userid);
        $type_id = $udetail[0]->type_id;

        if($type_id ==1){
            $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid')";
        }else if($type_id == 2){
            $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid')";
        }else if($type_id == 3){
            $text = "(u1.user_id = '$userid' || u1.user_id = '$userid')";
        }else if($type_id == 4){
            $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid')";
        }else if($type_id == 13){
            $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid')";
        }else if($type_id == 15){
            $text = "(u1.sales_co = '$userid' || u1.user_id = $userid)";
        }elseif($type_id == 19){
            $text = "(u1.ash_nae_co = '$userid' || u1.user_id = $userid)";
        }else if($type_id == 20){
            $text = "(u1.ash_w_co='$userid' || u1.user_id = $userid)";
        }else if($type_id == 21){
            $text = "(u1.ash_s_co='$userid' || u1.user_id = $userid)";
        }else if($type_id == 22){
            $text = "(u1.rm_east_co='$userid' || u1.user_id = $userid)";
        }else if($type_id == 23){
            $text = "(u1.rm_north_co='$userid' || u1.user_id = $userid)";
        }else if($type_id == 24){
            $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid')";
        }else{
            $text = "u1.admin_id = '$userid'";
        }

      $query2 = $this->db->query("SELECT
    bd_wise_reviews.*,
    review_types.name AS review_types_name,
    u1.name AS by_name,
    u2.name AS to_name,
    COUNT(DISTINCT sales_review_ratings.user_id) AS total_user
FROM bd_wise_reviews
LEFT JOIN review_types 
    ON review_types.id = bd_wise_reviews.review_type_id
LEFT JOIN user_details u1 
    ON u1.user_id = bd_wise_reviews.by_uid
LEFT JOIN user_details u2 
    ON u2.user_id = bd_wise_reviews.to_uid
LEFT JOIN sales_review_ratings 
    ON sales_review_ratings.bd_wise_reviews_id = bd_wise_reviews.id
WHERE
    bd_wise_reviews.planned_date BETWEEN '$sdate' AND '$edate'
    AND $text
    $reviewtypeFilter
GROUP BY bd_wise_reviews.id
ORDER BY bd_wise_reviews.id DESC");
        $result = $query2->result();
        return $result;
    }


    
    public function ReviewReportBDwiseDetailsID($review_id,$to_uid){

      $query2 = $this->db->query("SELECT
            bd_wise_reviews.id AS review_id,
            bd_wise_reviews.planned_date AS review_planned_date,
            bd_wise_reviews.start_date AS review_start_date,
            bd_wise_reviews.end_date AS review_end_date,
            bd_wise_reviews.review_status AS review_status,
            bd_wise_reviews.close_reamrks AS review_close_reamrks,
            bd_wise_reviews.close_reamrks AS review_comments,
            bd_wise_reviews.session_time AS review_session_time,
            bd_wise_reviews.created_at AS review_created_at,
            review_types.name AS review_types_name,
            u1.name AS by_name,
            u2.name AS ongoing_user_name,
            COUNT(DISTINCT sales_review_ratings.user_id) AS total_complete_review_user
        FROM bd_wise_reviews
        LEFT JOIN review_types ON review_types.id = bd_wise_reviews.review_type_id
        LEFT JOIN user_details u1 ON u1.user_id = bd_wise_reviews.by_uid
        LEFT JOIN user_details u2 ON u2.user_id = bd_wise_reviews.to_uid
        LEFT JOIN sales_review_ratings ON sales_review_ratings.bd_wise_reviews_id = bd_wise_reviews.id
        LEFT JOIN user_details u3 ON u3.user_id = sales_review_ratings.user_id
        WHERE
            bd_wise_reviews.id = '$review_id'
            ");
        $result1 = $query2->result();


        $query3 = $this->db->query("SELECT
            bd_wise_reviews.id AS review_id,
            review_types.name AS review_types_name,
            u1.name AS by_name,
            u3.name AS to_name,
            u3.name AS review_done_user_name,
            sales_review_ratings.*
        FROM sales_review_ratings 
        LEFT JOIN bd_wise_reviews ON bd_wise_reviews.id = sales_review_ratings.bd_wise_reviews_id
        LEFT JOIN review_types ON review_types.id = bd_wise_reviews.review_type_id
        LEFT JOIN user_details u1 ON u1.user_id = bd_wise_reviews.by_uid
        LEFT JOIN user_details u2 ON u2.user_id = bd_wise_reviews.to_uid
        LEFT JOIN user_details u3 ON u3.user_id = sales_review_ratings.user_id
        WHERE
            bd_wise_reviews.id = '$review_id'
            AND sales_review_ratings.user_id = '$to_uid'
            ");
        $result2 = $query3->result();



        $query4 = $this->db->query("SELECT
            bd_wise_reviews.id AS review_id,
            review_types.name AS review_types_name,
            u1.name AS by_name,
            u3.name AS to_name,
            u3.name AS review_done_user_name,
            c.clustername AS clustername,
            review_cluster_metrics.*
        FROM review_cluster_metrics 
        LEFT JOIN bd_wise_reviews ON bd_wise_reviews.id = review_cluster_metrics.review_id
        LEFT JOIN review_types ON review_types.id = bd_wise_reviews.review_type_id
        LEFT JOIN user_details u1 ON u1.user_id = bd_wise_reviews.by_uid
        LEFT JOIN user_details u2 ON u2.user_id = bd_wise_reviews.to_uid
        LEFT JOIN user_details u3 ON u3.user_id = review_cluster_metrics.user_id
        LEFT JOIN cluster as c ON c.id = review_cluster_metrics.cluster_id
        WHERE
            bd_wise_reviews.id = '$review_id'
            AND review_cluster_metrics.user_id = '$to_uid'
   
            ");
        $result3 = $query4->result();




            $data = [
                'review_informations'   => $result1,
                'review_details'        => $result2,
                'review_cluster_metrics' => $result3,
            ];

            return $data;

               
            }



public function ReviewReportBDwiseDetailsSingleReviewID($review_id){

    $query1 = $this->db->query("
        SELECT
            bd_wise_reviews.*,
            bd_wise_reviews.id AS review_id,
            u1.name AS by_name,
            u2.name AS to_name,
            u2.name AS ongoing_user_name,
            review_types.name as review_types_name
        FROM bd_wise_reviews
        LEFT JOIN review_types ON review_types.id = bd_wise_reviews.review_type_id
        LEFT JOIN user_details u1 ON u1.user_id = bd_wise_reviews.by_uid
        LEFT JOIN user_details u2 ON u2.user_id = bd_wise_reviews.to_uid
        WHERE bd_wise_reviews.id = '$review_id'
    ");
    $result1 = $query1->result();

    $query2 = $this->db->query("
        SELECT
            bd_wise_reviews.id AS review_id,
            bd_wise_reviews.start_date AS review_start_date,
            bd_wise_reviews.end_date AS review_end_date,
            u1.name AS by_name,
            u2.name AS to_name,
            u3.name AS reviewed_name,
            sales_review_ratings.user_id AS reviewed_user_id,
            review_types.name as review_types_name,
            sales_review_ratings.sdate as review_sdate,
            sales_review_ratings.edate as review_edate,
            sales_review_ratings.created_at as review_created_at
        FROM bd_wise_reviews
        LEFT JOIN review_types ON review_types.id = bd_wise_reviews.review_type_id
        LEFT JOIN user_details u1 ON u1.user_id = bd_wise_reviews.by_uid
        LEFT JOIN user_details u2 ON u2.user_id = bd_wise_reviews.to_uid
        LEFT JOIN sales_review_ratings ON sales_review_ratings.bd_wise_reviews_id = bd_wise_reviews.id
        LEFT JOIN user_details u3 ON u3.user_id = sales_review_ratings.user_id
        WHERE bd_wise_reviews.id = '$review_id'
        GROUP BY sales_review_ratings.user_id
    ");
    $result2 = $query2->result();

    return [
        'review_informations' => $result1,
        'review_complete_details' => $result2,
    ];
}

/**
 * GetAllTeamByLineManager
 *
 * This function is used to get all team of a given user by line manager.
 *
 * @param int $userid User ID
 * @param int $reviewid Review ID
 * @return array
 */
  public function GetAllActiveTeamByLineManager($userid){

        $udetail = $this->Menu_model->get_userbyid($userid);
        $type_id = $udetail[0]->type_id;   

        if($type_id == 1){
            $text = "(u1.sadmin_id = '$userid' || u1.user_id = '$userid') AND u1.type_id IN (1,3,4,13,24,22)";
        }if($type_id == 2){
            $text = "(u1.admin_id = '$userid' || u1.user_id = '$userid') AND u1.type_id IN (2,3,4,13,24,22)";
        }else if($type_id == 3){
            $text = "(u1.user_id = '$userid' || u1.user_id = '$userid')";
        }else if($type_id == 4){
            $text = "(u1.pst_co = '$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,4,13,24)";
        }else if($type_id == 13){
            $text = "(u1.aadmin = '$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,13,24)";
        }else if($type_id == 15){
            $text = "(u1.sales_co = '$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,4,13,24)";
        }elseif($type_id == 19){
            $text = "(u1.ash_nae_co = '$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,4,13,24)";
        }else if($type_id == 20){
            $text = "(u1.ash_w_co='$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,4,13,24)";
        }else if($type_id == 21){
            $text = "(u1.ash_s_co='$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,4,13,24)";
        }else if($type_id == 22){
            $text = "(u1.rm_east_co='$userid' || u1.user_id = '$userid' AND u1.type_id IN (3,4,13,24) ";
        }else if($type_id == 23){
            $text = "(u1.rm_north_co='$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,4,13,24)";
        }else if($type_id == 24){
            $text = "(u1.acm_co = '$userid' || u1.user_id = '$userid') AND u1.type_id IN (3,24)";
        }else{
            $text = "u1.user_id = '$userid' ";
        }
    
        $query = $this->db->query("SELECT DISTINCT 
    u1.user_id,
    u1.name
FROM user_details u1 
WHERE $text
AND u1.status = 'active'
ORDER BY u1.user_id DESC");
    $result = $query->result();

        return $result;
    }

// ************************ End To Get BD Wise Review Report Details ****************************



}