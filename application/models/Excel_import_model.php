<?php
class Excel_import_model extends CI_Model
{
	

	function insert($data,$bdid)
	{
	    date_default_timezone_set("Asia/Kolkata");
        $date=date('Y-m-d H:i:s');
        $cdate=date('Y-m-d');
        $assign_to = $bdid;
        $status = 1;
        $remark_msg = 'Research done';
        $action = 1;
        $purpose = 1;
        $next_action_id = 1;
        $next_action = 'Will do research on client complete details';
	   
	   $l = sizeof($data);
		for($i=0;$i<$l;$i++){
		    $compname = $data[$i]['compname'];
            $website = $data[$i]['website'];
            $country = $data[$i]['country'];
            $city = $data[$i]['city'];
            $state = $data[$i]['state'];
            $draft = $data[$i]['draft'];
            $address = $data[$i]['address'];
            $ctype = $data[$i]['ctype'];
            $budget = $data[$i]['budget'];
            $compconname = $data[$i]['compconname'];
            $emailid = $data[$i]['emailid'];
            $phoneno = $data[$i]['phoneno'];
            $designation = $data[$i]['designation'];
            $top_spender = $data[$i]['top_spender'];
            $upsell_client = $data[$i]['upsell_client'];
            $focus_funnel = $data[$i]['focus_funnel'];
		    $this->db->query("INSERT INTO company_master(compname, draft, budget, address, website, createddate, city, country, partnerType_id, state) VALUES ('$compname','$draft','$budget','$address','$website','$cdate','$city','$country','1','$state')");
		    
		    $cid = $this->db->insert_id();
       
           $this->db->query("INSERT INTO company_contact_master(contactperson, emailid, phoneno, designation, type, createddate, company_id) VALUES ('$compconname', '$emailid', '$phoneno', '$designation', 'primary', '$cdate', '$cid')");
           $ccid = $this->db->insert_id();
           
           $this->db->query("INSERT INTO init_call(draft, createDate, topspender, noofschools, cmpid_id, creator_id,upsell_client,focus_funnel,mainbd,cstatus) VALUES ('$draft', '$cdate', '$top_spender', '0','$cid','$bdid','$upsell_client','$focus_funnel','$bdid','1')");
           $inid = $this->db->insert_id();
           
           
           $this->db->query("INSERT INTO tblcallevents(lastCFID, nextCFID, draft, event, fwd_date, actontaken, nextaction, meeting_type, live_loaction, mom_received, appointmentdatetime, actiontype_id, assignedto_id, cid_id, purpose_id, remarks, status_id, user_id, date, updateddate, updation_data_type) VALUES ('0', '0', '$draft', '', '$date', '$action', 'Research & Data Collection', 'NA','NA','no','$date','$next_action_id','$assign_to','$inid','$purpose','$remark_msg','$status','$assign_to','$date','$date','updated')");
           $tblid = $this->db->insert_id();
           
           $this->db->query("INSERT INTO notify(uid,type,sms) VALUES ('$bdid','1','New Lead Added by Xls')");
		}
		
	}
}
