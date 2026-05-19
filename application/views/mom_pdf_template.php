<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>MOM PDF</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  

   <?php
        $write_mom_tid         = $momdata[0]->tid;
        $writeMomTidDatas      = $this->Menu_model->getTBLTaskByID($write_mom_tid);
        
        $aftertask_meeting     = $writeMomTidDatas[0]->aftertask;

        $cmeetingsDatas      = $this->Menu_model->GetMeetingDetailsByTaskID($aftertask_meeting);

       
      
    ?>

<center>
<h3 style="text-align:center;color:navy;">Meeting With MOM Report</h3>
<h4 style="text-align:center;">
    <a target="_blank" href="<?= base_url().'Menu/CompanyDetails/'.$cmeetingsDatas[0]->cid ?>">
        <?= $cmeetingsDatas[0]->compname ?> (CID - <?= $cmeetingsDatas[0]->cid ?>)
    </a>
 </h4>
<h5 style="text-align:center;"><?= $cmeetingsDatas[0]->task_username  ?> (Date - <?= $cmeetingsDatas[0]->appointmentdatetime ?>) </h5>
</center>
  <hr>

<?php
echo '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse; width:100%; font-family:Arial, sans-serif;">';

foreach($cmeetingsDatas as $meeting) {
    // Row 1: Left Column
    echo '<tr style="background-color:#f9f9f9;">';
    echo '<td style="width:50%; vertical-align:top;">
            <p><strong>Meeting BY:</strong> ' . $meeting->task_username . '</p>
            <p><strong>Company Name:</strong> ' . $meeting->compname . '</p>
            <p><strong>CID:</strong> <a target="_blank" href="'.base_url().'Menu/CompanyDetails/'.$meeting->cid.'">' . $meeting->cid . '</a></p>
            <p><strong>Forward Date:</strong> ' . $meeting->fwd_date . '</p>
            <p><strong>Appointment:</strong> ' . $meeting->appointmentdatetime . '</p>
            <p><strong>Action Taken:</strong> ' . $meeting->actontaken . '</p>
            <p><strong>Purpose Achieved:</strong> ' . $meeting->purpose_achieved . '</p>
            <p><strong>Remarks:</strong> ' . $meeting->remarks . '</p>
             <p><strong>Meeting Type:</strong> ' . $meeting->mtype . '</p>
            <p><strong>Select By:</strong> ' . $meeting->selectby . '</p>
            <p><strong>Task Approved By:</strong> ' . $meeting->task_approved_by . '</p>
          </td>';

    // Row 1: Right Column
    echo '<td style="width:50%; vertical-align:top;">
           
            <p><strong>Current Status:</strong> ' . $meeting->current_status . '</p>
            <p><strong>Task Time Status:</strong> ' . $meeting->task_time_status . '</p>
            <p><strong>Start Time:</strong> ' . $meeting->startm . '</p>
            <p><strong>Close Time:</strong> ' . $meeting->closem . '</p>
            <p><strong>Company AS:</strong> ' . $meeting->company_as . '</p>
            <p><strong>Meeting Status:</strong> ' . $meeting->meetings_status . '</p>
            <p><strong>Cluster:</strong> <a target="_blank" href="'.base_url().'Menu/TravelClusterDetailsByID/'.$meeting->cluster_id.'">' . $meeting->cluster_name . ' (' . $meeting->cluster_travel . ')</a></p>
            <p><strong>Initiate Photo:</strong> ' . (!empty($meeting->initphoto) ? '<a target="_blank" href="'.base_url().'uploads/day/'.$meeting->initphoto.'">View</a>' : '-') . '</p>
            <p><strong>Close Photo:</strong> ' . (!empty($meeting->cphoto) ? '<a target="_blank" href="'.base_url().$meeting->cphoto.'">View</a>' : '-') . '</p>
          </td>';
    echo '</tr>';
}

echo '</table>';

?>


<?php /*

<div class="container">
    <div class="row">
    <div class="col-md-6">
        <p>Initial Photo</p> <br>
        <img height="100px" src="<?=base_url().'uploads/day/' .$meeting->initphoto?>" style="max-width:100%; border-radius:5px;">
    </div>
    <div class="col-md-6">
        <p>Close Photo</p> <br>
         <img height="100px" src="<?= base_url().$meeting->cphoto ?>" style="max-width:100%; border-radius:5px;">
    </div>
</div>
</div>

*/ ?>


                
<hr>
<center>
    <a class="bg-success p-2 text-center" target="_blank" href="<?=base_url()?>Reports/ViewMeetingDetails/<?=$aftertask_meeting;?>" target="_blank">View Meeting Details</a>
</center>
<hr>

<br>
<h3 style="text-align:center;">MOM Report</h3>
<br>

 <?php //  dd($momdata); ?>

  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th>Sr No</th>
        <th>Question</th>
        <th>Answer</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $i = 1;
        foreach ($momdata as $values) {
          $mom_id = $values->id;
          $mom_tid = $values->tid;
          unset($values->id);
          unset($values->reject_remarks);
          unset($values->cm_call_task);
          unset($values->bd_request_task);
          unset($values->school_visit_task);
          unset($values->pst_call_task);
          unset($values->pst_assign);
          unset($values->edit_cnt);

          $getTblCallData = $this->Management_model->get_BDMoM_TBL_Call_Data($values->tid);
          $fwd_date         = $getTblCallData[0]->fwd_date;
          $appointmentdatetime = $getTblCallData[0]->appointmentdatetime;
          $plandt = $getTblCallData[0]->plandt;
          $updateddate = $getTblCallData[0]->updateddate;
          $remarks = $getTblCallData[0]->remarks;
          $utype = $this->Menu_model->get_userbyid($values->user_id);
          $uname = $utype[0]->name;
          $getcmp = $this->Menu_model->get_cmpbyinid($values->init_cmpid);
          $compname = $getcmp[0]->compname;
          $ccstatus = $this->Menu_model->get_statusbyid($values->ccstatus);
          $ccstatusname = $ccstatus[0]->name;
          $action_name = $this->Menu_model->get_actionbyid($values->action_id);
          $action_name = $action_name[0]->name;

          foreach ($values as $key => $value) {
            if ($key == 'tid') continue;

            if ($key == 'project_intervention_select' && $value !== 'Others') {
              unset($values->project_intervention);
            }
            if ($key == 'client_has_adopted_select' && $value == 'no') {
              unset($values->client_has_adopted);
            }
            if ($key == 'submit_proposal' && $value == 'no') {
              unset($values->proposal_no_of_school);
              unset($values->proposal_of_budget);
              unset($values->proposal_of_location);
            }
            if ($key == 'identify_school' && $value == 'yes') {
              unset($values->identify_school_district);
              unset($values->no_of_school);
            }
            if ($key == 'identify_school' && $value == 'no') {
              unset($values->identify_school_state);
              unset($values->identify_school_district);
              unset($values->no_of_school);
            }
            if ($key == 'permission_letter' && $value == 'no') {
              unset($values->permission_letter_rech);
              unset($values->Letter_organization_name);
              unset($values->Letter_organization_designation);
              unset($values->Letter_organization_location);
            }
            if ($key == 'client_int_school_visit' && $value == 'no') {
              unset($values->client_int_type_project);
              unset($values->client_int_school_date);
              unset($values->client_int_school_state);
              unset($values->client_int_school_district);
              unset($values->client_int_no_of_school);
            }
      ?>
        <tr>
          <td><?= $i; ?></td>
          <td>
            <?php
              if ($key == 'ccstatus') echo "Current Company Status";
              if ($key == 'action_id') echo "Action ID";
              if ($key == 'user_id') echo "BD Name";
              if ($key == 'init_cmpid') echo "Company Name";
              if ($key == 'actontaken') echo "Action Taken";
              if ($key == 'meetingdonewinitiator') echo "Meeting done with Initiator or influencer and decision maker of the company";
              if ($key == 'presentation') echo "Presentation and pitching is done for which offering:";
              if ($key == 'project_intervention_select') echo "What is the client's thematic Area for Project Intervention in the current financial Year";
              if ($key == 'project_intervention') echo "What is the client's Other thematic Area for Project Intervention in the current financial Year";
              if ($key == 'client_has_adopted_select') echo "Does the client have adopted any schools?";
              if ($key == 'client_has_adopted') echo "Specify details of client has adopted any schools";
              if ($key == 'approving_autorities') echo "Who are the approving authorities of the proposal?";
              if ($key == 'budget_for_cfyear') echo "What is the leftover budget for the current financial year?";
              if ($key == 'fund_sanstion_limit') echo "What is the fund sanction limit at their level?";
              if ($key == 'other_specific_remarks') echo "Any other specific remarks regarding the meeting?";
              if ($key == 'submit_proposal') echo "Do we need to submit the proposal?";
              if ($key == 'proposal_no_of_school') echo "Proposed number of schools";
              if ($key == 'proposal_of_budget') echo "Proposed budget";
              if ($key == 'proposal_of_location') echo "Proposed location";
              if ($key == 'identify_school') echo "Do we need to identify school?";
              if ($key == 'identify_school_state') echo "Name of identify school";
              if ($key == 'permission_letter') echo "School permission letter required?";
              if ($key == 'permission_letter_rech') echo "Letter should be addressed to whom in the organization, along with Name and designation and Location";
              if ($key == 'Letter_organization_name') echo "Letter Organization Name";
              if ($key == 'Letter_organization_designation') echo "Organization Designation Name";
              if ($key == 'Letter_organization_location') echo "Organization Location Name";
              if ($key == 'client_int_school_visit') echo "Client is interested in School Visit?";
              if ($key == 'client_int_type_project') echo "Type of project?";
              if ($key == 'client_int_school_date') echo "Date for Client is interested in School Visit?";
              if ($key == 'client_int_school_state') echo "State for Client is interested in School Visit?";
              if ($key == 'client_int_school_district') echo "District for Client is interested in School Visit?";
              if ($key == 'client_int_no_of_school') echo "Number of School for Client is interested in School Visit?";
              if ($key == 'intervention_cm_pst_sh') echo "Do you need intervention from Cluster/PST/Sales Head?";
              if ($key == 'rpmmom') echo "Short MOM Remarks";
              if ($key == 'partner') echo "Partner Type";
              if ($key == 'cdate') echo "Submit Date";
              if ($key == 'approved_status') echo "Approved Status";
              if ($key == 'approved_by') echo "Approved By";
              if ($key == 'approved_date') echo "Approved Date";
            ?>
          </td>
          <td>
            <?php
              if ($key == 'ccstatus') echo $ccstatusname;
              if ($key == 'action_id') echo $action_name;
              if ($key == 'user_id') echo $uname;
              if ($key == 'init_cmpid') echo $compname;
              if ($key == 'actontaken') echo $value;
              if ($key == 'meetingdonewinitiator') echo $value;
              if ($key == 'presentation') echo $value;
              if ($key == 'project_intervention_select') echo $value;
              if ($key == 'project_intervention') echo $value;
              if ($key == 'client_has_adopted_select') echo $value;
              if ($key == 'client_has_adopted') echo $value;
              if ($key == 'approving_autorities') echo $value;
              if ($key == 'budget_for_cfyear') echo $value;
              if ($key == 'fund_sanstion_limit') echo $value;
              if ($key == 'other_specific_remarks') echo $value;
              if ($key == 'submit_proposal') echo $value;
              if ($key == 'proposal_no_of_school') echo $value;
              if ($key == 'proposal_of_budget') echo $value;
              if ($key == 'proposal_of_location') echo $value;
              if ($key == 'identify_school') echo $value;
              if ($key == 'identify_school_state') {
                $state = $values->identify_school_state;
                $district = $values->identify_school_district;
                $noofschool = $values->no_of_school;
                $states = explode(',', $state);
                $districts = explode(',', $district);
                $schoolCounts = explode(',', $noofschool);
                for ($j = 0; $j < count($states); $j++) {
                  echo "State: " . $states[$j] . "<br>District: " . $districts[$j] . "<br>No. of Schools: " . $schoolCounts[$j] . "<br><br>";
                }
              }
              if ($key == 'permission_letter') echo $value;
              if ($key == 'permission_letter_rech') echo $value;
              if ($key == 'Letter_organization_name') echo $value;
              if ($key == 'Letter_organization_designation') echo $value;
              if ($key == 'Letter_organization_location') echo $value;
              if ($key == 'client_int_school_visit') echo $value;
              if ($key == 'client_int_type_project') echo $value;
              if ($key == 'client_int_school_date') echo $value;
              if ($key == 'client_int_school_state') echo $value;
              if ($key == 'client_int_school_district') echo $value;
              if ($key == 'client_int_no_of_school') echo $value;
              if ($key == 'intervention_cm_pst_sh') echo $value;
              if ($key == 'rpmmom') echo $value;
              if ($key == 'partner') echo $value;
              if ($key == 'cdate') echo $value;
              if ($key == 'approved_status') {
                if ($value == '') echo "<span class='bg-warning p-2'>Pending</span>";
                else if ($value == 'Approved') echo "<span class='bg-success p-2'>$value</span>";
                else if ($value == 'Reject' || $value == 'NO RP') echo "<span class='bg-danger p-2'>$value</span>";
              }
              if ($key == 'approved_by') {
                if ($value != '') echo $this->Menu_model->get_userbyid($value)[0]->name;
              }
              if ($key == 'approved_date') echo $value;
            ?>
          </td>
        </tr>

      <?php
        $i++;
        }}
      ?>
    </tbody>
  </table>
</body>
</html>
