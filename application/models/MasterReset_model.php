<?php
date_default_timezone_set("Asia/Calcutta");
class MasterReset_model extends Menu_model{


    public function GetBDFunnelsDatas(){
    
        $query1 = $this->db->query("SELECT
	user_zone.id,
    user_zone.name as zone_name,
    COUNT(init_call.id) as total
FROM
    `init_call`
    LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
    LEFT JOIN user_zone on user_zone.id = u1.zone_id
WHERE
    u1.status = 'active'
    GROUP BY u1.zone_id");
        $data1 = $query1->result();

        $query2 = $this->db->query("SELECT
            u1.user_id,
            u1.name as bd_name,
            user_type.name as department_name,
            COUNT(init_call.id) as total
        FROM
            `init_call`
            LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
            LEFT JOIN user_type on user_type.id = u1.type_id
        WHERE
            u1.status = 'active'
            GROUP BY u1.user_id");
        $data2 = $query2->result();


        $result = [
            'zone_wise' => $data1,
            'bd_wise'   => $data2,
        ];

        return $result;

    }



    public function GetBDFunnelsDatas_ZoneWiseReset($getLastFinancialYear){
    
        $query1 = $this->db->query("SELECT
	user_zone.id,
    user_zone.name as zone_name,
    COUNT(init_call.id) as total,
    annual_reset_status.id as annual_reset_status_id,
    annual_reset_status.fyear as annual_reset_fyear,
    annual_reset_status.status as annual_reset_funnel_status,
    annual_reset_status.created_at as annual_reset_created_at
FROM
    `init_call`
    LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
    LEFT JOIN user_zone on user_zone.id = u1.zone_id
    LEFT JOIN annual_reset_status on annual_reset_status.zone_id = user_zone.id AND annual_reset_status.fyear = '$getLastFinancialYear'
WHERE
    u1.status = 'active'
    GROUP BY u1.zone_id");
        $data1 = $query1->result();

        $result = [
            'zone_wise' => $data1,
        ];

        return $result;

    }

       public function GetBDFunnelsDatas_BDWiseReset($getLastFinancialYear){
    
         $query2 = $this->db->query("SELECT
            u1.user_id,
            u1.name as bd_name,
            user_type.name as department_name,
            COUNT(init_call.id) as total,
            annual_reset_status.id as annual_reset_status_id,
            annual_reset_status.fyear as annual_reset_fyear,
            annual_reset_status.status as annual_reset_funnel_status,
            annual_reset_status.created_at as annual_reset_created_at
        FROM
            `init_call`
            LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
            LEFT JOIN user_type on user_type.id = u1.type_id
            LEFT JOIN annual_reset_status on annual_reset_status.bd_id = u1.user_id AND annual_reset_status.fyear = '$getLastFinancialYear'
        WHERE
            u1.status = 'active'
            GROUP BY u1.user_id");
        $data2 = $query2->result();

        $result = [
            'bd_wise' => $data2,
        ];

        return $result;

    }

     public function GetBDFunnelsDatas_zoneID($zone_id){
    
         $query2 = $this->db->query("SELECT
            u1.user_id as main_bd_id,
            u1.name as bd_name,
            init_call.id as init_id,
            init_call.cmpid_id as cid,
            init_call.cstatus,
            init_call.lstatus,
            init_call.clm_id,
            init_call.acm_co_id,
            init_call.apst,
            init_call.rm_east_co_id,
            init_call.rm_north_co_id,
            init_call.ash_nae_co_id,
            init_call.ash_w_co_id
          
        FROM
            `init_call`
            LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
        WHERE
            u1.status = 'active'
            AND u1.zone_id = $zone_id");
        $result = $query2->result();

        return $result;

    }
     public function GetBDFunnelsDatas_BDID($user_id){
    
         $query2 = $this->db->query("SELECT
            u1.user_id as main_bd_id,
            u1.name as bd_name,
            init_call.id as init_id,
            init_call.cmpid_id as cid,
            init_call.cstatus,
            init_call.lstatus,
            init_call.clm_id,
            init_call.acm_co_id,
            init_call.apst,
            init_call.rm_east_co_id,
            init_call.rm_north_co_id,
            init_call.ash_nae_co_id,
            init_call.ash_w_co_id
          
        FROM
            `init_call`
            LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
        WHERE
            u1.status = 'active'
            AND u1.user_id = $user_id");
        $result = $query2->result();

        return $result;

    }


   



    public function GetBDFunnelsStatusDatasByZoneID($zone_id){
    
        $query = $this->db->query("SELECT
                status.id,
                status.name as status_name,
                COUNT(init_call.id) as total
            FROM
                `init_call`
                LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
                LEFT JOIN user_zone on user_zone.id = u1.zone_id
                LEFT JOIN status on status.id = init_call.cstatus
            WHERE
                u1.status = 'active'
                AND u1.zone_id = $zone_id
                GROUP BY status.id");
        $result = $query->result();

        return $result;

    }
    public function CheckPrimaryContactExistsOrNotinTable($cid){
        $query  = $this->db->query("SELECT * FROM `company_contact_master` WHERE company_id = '$cid' AND type IN ('primary', 'primary','Primary')");
        $result = $query->result();
        return $result;
    }

    public function CheckCurrentFinancialYearRPMeetingDoneOrNot($init_id,$sdate,$edate){
        $query  = $this->db->query("SELECT * FROM `tblcallevents` WHERE DATE(appointmentdatetime) BETWEEN '$sdate' AND '$edate' AND tblcallevents.mtype IN ('RP', 'RPClose', 'Change RP') AND cid_id = '$init_id' AND nextCFID != 0 AND actiontype_id IN (3, 4,22)");
        $result = $query->result();
        return $result;
    }

      public function CheckCurrentFinancialYearProposalDoneOrNot($init_id,$sdate,$edate,$user_id){
        $query  = $this->db->query("SELECT * FROM `tblcallevents` WHERE DATE(appointmentdatetime) BETWEEN '$sdate' AND '$edate' AND cid_id = '$init_id' AND actiontype_id = 7 AND user_id = '$user_id'");
        $result = $query->result();
        return $result;
    }


    public function GetBDFunnelsStatusDatasByBDID($bd_id){
    
        $query = $this->db->query("SELECT
                status.id,
                status.name as status_name,
                COUNT(init_call.id) as total
            FROM
                `init_call`
                LEFT JOIN user_details u1 on u1.user_id = init_call.mainbd
                LEFT JOIN user_zone on user_zone.id = u1.zone_id
                LEFT JOIN status on status.id = init_call.cstatus
            WHERE
                u1.status = 'active'
                AND u1.user_id = $bd_id
                GROUP BY status.id");
        $result = $query->result();

        return $result;

    }
    


    public function getLastFinancialYear() {

        $currentMonth = date('n'); // 1 to 12
        $currentYear  = date('Y');

        if ($currentMonth >= 4) {
            // Current FY: YYYY-(YYYY+1)
            $startYear = $currentYear - 1;
            $endYear   = $currentYear;
        } else {
            // Current FY: (YYYY-1)-YYYY
            $startYear = $currentYear - 2;
            $endYear   = $currentYear - 1;
        }

        return $startYear . '-' . $endYear;
}


}