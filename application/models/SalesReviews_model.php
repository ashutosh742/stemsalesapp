<?php
date_default_timezone_set("Asia/Calcutta");
class SalesReviews_model extends Menu_model{




  public function GetAllTeamByLineManager($userid,$reviewid){

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
LEFT JOIN sales_review_ratings ON sales_review_ratings.user_id = u1.user_id AND sales_review_ratings.bd_wise_reviews_id = '$reviewid'
WHERE $text
AND sales_review_ratings.user_id IS NULL
AND u1.status = 'active'
ORDER BY u1.user_id DESC");
        $result = $query->result();

        return $result;
    }

    public function GetBDFunnelssadasdsaDatas(){
    
        $query1 = $this->db->query("SELECT
                user_zone.id,
                user_zone.name as zone_name,
                COUNT(init_call_bkp.id) as total
            FROM
                `init_call_bkp`
                LEFT JOIN user_details u1 on u1.user_id = init_call_bkp.mainbd
                LEFT JOIN user_zone on user_zone.id = u1.zone_id
            WHERE
                u1.status = 'active'
                GROUP BY u1.zone_id");

        $data1 = $query1->result();

        $result = [
            'zone_wise' => $data1,
        ];

        return $result;

    }

    public function GetAllReviewTypesonTable1(){
        $query  = $this->db->query("SELECT * FROM `review_types` WHERE review_status = 1");
        $result = $query->result();
       
        return $result;
    }

     public function GetBDWiseReviewByReviewID($review_id){
        $query  = $this->db->query("SELECT
    bd_wise_reviews.*,
    review_types.name as review_types_name,
    u1.name as by_name,
    u2.name as to_name
FROM
    `bd_wise_reviews`
    LEFT JOIN review_types on review_types.id = bd_wise_reviews.review_type_id
    LEFT JOIN user_details u1 on u1.user_id = bd_wise_reviews.by_uid
    LEFT JOIN user_details u2 on u2.user_id = bd_wise_reviews.to_uid
WHERE
    bd_wise_reviews.id = '$review_id'");
        $result = $query->result();
       
        return $result;
    }



    public function GetOnGoingBDReviews($uid,$review_status){
        $query  = $this->db->query("SELECT
    bd_wise_reviews.*,
    review_types.name as review_types_name,
    u1.name as by_name,
    u2.name as to_name
FROM
    `bd_wise_reviews`
    LEFT JOIN review_types on review_types.id = bd_wise_reviews.review_type_id
    LEFT JOIN user_details u1 on u1.user_id = bd_wise_reviews.by_uid
    LEFT JOIN user_details u2 on u2.user_id = bd_wise_reviews.to_uid
WHERE
    bd_wise_reviews.review_status = '$review_status' AND bd_wise_reviews.by_uid = '$uid'");
        $result = $query->result();
       
        return $result;
    }


     public function save_review($data) {
        // Ensure required fields
        if (empty($data['user_id']) || empty($data['metric_key']) || empty($data['rating'])) {
            return false;
        }

        // Use today's date as review_date if not provided
        if (empty($data['review_date'])) {
            $data['review_date'] = date('Y-m-d');
        }

        // Check if an entry already exists for this user, metric, and review_date
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('metric_key', $data['metric_key']);
        $this->db->where('review_date', $data['review_date']);
        $query = $this->db->get('sales_review_ratings');

        if ($query->num_rows() > 0) {
            // Update existing record
            $this->db->where('user_id', $data['user_id']);
            $this->db->where('metric_key', $data['metric_key']);
            $this->db->where('review_date', $data['review_date']);
            return $this->db->update('sales_review_ratings', $data);
        } else {
            // Insert new record
            return $this->db->insert('sales_review_ratings', $data);
        }
    }







    
public function getAllCompanyBYRollesMaiingClosurePipeLineOnReviesPage($userid,$sdate,$edate){
    $cfyear = $this->Menu_model->getCurrentFinancialYearStart();
 
       if($type_id == 25){
            $mainBDFilterQuery = 'init_call.creator_id';
        }else{
            $mainBDFilterQuery = 'init_call.mainbd';
        }


         $text = "u1.user_id = '$userid'";

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







public function getAllCompanyBYRollesMaiingBYMeetingByClusterID($userid,$sdate,$edate){

    $text = "(u1.user_id = '$userid')";

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

    COUNT(DISTINCT CASE WHEN (proposal.id IS NOT NULL AND proposal.apr IN (0,1)) THEN c.init_id END) AS total_proposal_sent,
    COUNT(DISTINCT CASE WHEN (c.cstatus IN (7,14)) THEN c.init_id END) AS total_clouser,

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
    END) AS total_deleted_meeting


    
    -- COUNT(DISTINCT CASE WHEN (c.in_quarter = '20 Closure Funnel in $currentQuarter' AND ce.id IS NULL) THEN c.init_id END) AS total_pending_for_meeting_20_clouser_funnel,
    -- COUNT(DISTINCT CASE WHEN (c.in_quarter = '20 Closure Funnel in $currentQuarter' AND proposal.id IS NULL) THEN c.init_id END) AS total_pending_for_proposal_20_clouser_funnel,
    -- COUNT(DISTINCT CASE WHEN (c.in_quarter = 'Potential Funnel For $currentQuarter' AND ce.id IS NULL) THEN c.init_id END) AS total_pending_for_meeting_potential_funnel,
    -- COUNT(DISTINCT CASE WHEN (c.in_quarter = 'Proposal to Be Sent After Review In $currentQuarter' AND proposal.id IS NULL) THEN c.init_id END) AS total_pending_proposal_to_be_sent_after_review
    
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






}