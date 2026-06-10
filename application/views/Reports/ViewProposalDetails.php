<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Praposal Details | STEM APP | WebAPP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
    <style>
      .card-body.box-profile {
      background: cadetblue;
      }
       body {
            background-color: #e0f7fa; /* A beautiful light cyan background */
            /* padding: 20px; */
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #00796b; /* A beautiful teal color for the card header */
            color: white;
        }
        .card {
    /* align-items: center; */
    text-align: center;
}
 .card .card-header {
    text-align: center;
    justify-content: center;
    display: flex;
}
p.card-text {
    font-weight: 600;
    color: #ff147f;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
       <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
      <?php 
       
          ?>
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2 p-3">
              <div class="col-sm-12 bg-info text-center p-2">
                <h3 class="m-0">Praposal Details</h3>
              </div>
            </div>
          </div>
        </div>
        <hr>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">

 
<?php 


$data = $proposalDatas[0];

// Extract each field into separate variables
$task_user_id = $data->task_user_id;
$proposal_id = $data->proposal_id;
$task_username = $data->task_username;
$compname = $data->compname;
$cid = $data->cid;
$task_id = $data->task_id;
$nextCFID = $data->nextCFID;
$fwd_date = $data->fwd_date;
$appointmentdatetime = $data->appointmentdatetime;
$initiateddt = $data->initiateddt;
$updated_at = $data->updated_at;
$total_time_taken = $data->total_time_taken;
$late_remarks_message = $data->late_remarks_message;
$actontaken = $data->actontaken;
$purpose_achieved = $data->purpose_achieved;
$meeting_type = $data->meeting_type;
$actiontype_id = $data->actiontype_id;
$assignedto_id = $data->assignedto_id;
$cid_id = $data->cid_id;
$remarks = $data->remarks;
$selectby = $data->selectby;
$task_approved_status = $data->task_approved_status;
$task_approved_by = $data->task_approved_by;
$task_assignedto_by = $data->task_assignedto_by;
$task_aftertask = $data->task_aftertask;
$task_name = $data->task_name;
$current_status = $data->current_status;
$plan_count = $data->plan_count;
$potential = $data->potential;
$topspender = $data->topspender;
$upsell_client = $data->upsell_client;
$focus_funnel = $data->focus_funnel;
$keycompany = $data->keycompany;
$pkclient = $data->pkclient;
$priorityc = $data->priorityc;
$q1_twetenty_closure_funnel = $data->q1_twetenty_closure_funnel;
$potential_funnel_for_fy = $data->potential_funnel_for_fy;
$to_be_nurtured_for_fy = $data->to_be_nurtured_for_fy;
$fifity_new_lead_funnel = $data->fifity_new_lead_funnel;
$main_bd_name = $data->main_bd_name;
$main_bd_manager_name = $data->main_bd_manager_name;
$task_time_status = $data->task_time_status;
$task_time_new_status = $data->task_time_new_status;
$partner_name = $data->partner_name;
$proattach = $data->proattach;
$proposal_partner = $data->proposal_partner;
$propasal_types = $data->propasal_types;
$propasal_noofsc = $data->propasal_noofsc;
$propasal_budgetme = $data->propasal_budgetme;
$apr_status = $data->apr_status;
$propasal_apr_date = $data->propasal_apr_date;
$propasal_apr_remarks = $data->propasal_apr_remarks;

?>




        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Company Name (cid)</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($compname); ?> (<?php echo htmlspecialchars($cid); ?>)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Current Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($current_status); ?></p>
                    </div>
                </div>
            </div>
        </div>
      
        <div class="row">
          <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Username</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_username); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Orignal Date</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($fwd_date); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Appointment Date Time</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($appointmentdatetime); ?></p>
                    </div>
                </div>
            </div>

             <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Initiated Date Time</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($initiateddt); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Updated At</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($updated_at); ?></p>
                    </div>
                </div>
            </div>


        </div>
        <div class="row">
           
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Total Time Taken</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($total_time_taken); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
             <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Name</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_name); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Action Taken</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($actontaken); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Purpose Achieved</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($purpose_achieved); ?></p>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Remarks</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($remarks); ?></p>
                    </div>
                </div>
            </div>
        </div>
       
    
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Approved Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php if(htmlspecialchars($task_approved_status) ==1){
                          echo "Approved";
                        }else{
                            if(htmlspecialchars($task_approved_status) ==''){
                                echo "Pending";
                            }else if(htmlspecialchars($task_approved_status) ==0){
                               echo "Rejected";
                            }
                            
                        } ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Approved By</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_approved_by); ?></p>
                    </div>
                </div>
            </div>
  <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Time Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_time_status); ?></p>
                    </div>
                </div>
            </div>
             <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Selected By</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($selectby); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Plan Count</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($plan_count); ?></p>
                    </div>
                </div>
            </div>
           
        </div>
       
        <div class="row">
            
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Potential</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($potential); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Top Spender</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($topspender); ?></p>
                    </div>
                </div>
            </div>
       
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Upsell Client</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($upsell_client); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Focus Funnel</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($focus_funnel); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Key Company</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($keycompany); ?></p>
                    </div>
                </div>
            </div>

             <div class="col-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">PK Client</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($pkclient); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Priority</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($priorityc); ?></p>
                    </div>
                </div>
            </div>

        </div>
       
        <div class="row">
           <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Q1 Twenty Closure Funnel</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                        if(is_null($q1_twetenty_closure_funnel) || $q1_twetenty_closure_funnel == 'NULL'){
                          echo "NA";
                        }else{
                            echo htmlspecialchars($q1_twetenty_closure_funnel);
                        }
                        
                         ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Potential Funnel for FY</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                         if(is_null($potential_funnel_for_fy) || $potential_funnel_for_fy == 'NULL'){
                          echo "NA";
                        }else{
                            echo htmlspecialchars($potential_funnel_for_fy);
                        }
                        
                         ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">To Be Nurtured for FY</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                         if(is_null($to_be_nurtured_for_fy) || $to_be_nurtured_for_fy == 'NULL'){
                          echo "NA";
                        }else{
                            echo htmlspecialchars($to_be_nurtured_for_fy);
                        }
                        
                         ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Fifty New Lead Funnel</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php
                          if(is_null($fifity_new_lead_funnel) || $fifity_new_lead_funnel == 'NULL'){
                          echo "NA";
                        }else{
                            echo htmlspecialchars($fifity_new_lead_funnel);
                        }
                         ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Main BD Name</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($main_bd_name); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Cluster Manager Name</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($main_bd_manager_name); ?></p>
                    </div>
                </div>
            </div>
          
        </div>
      
        <div class="row">
               <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Partner Name</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($partner_name); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Partner</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($proposal_partner); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Types</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($propasal_types); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Number of SCHOOL</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($propasal_noofsc); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Budget</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($propasal_budgetme); ?> ₹</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal APR Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                   
                         if (isset($apr_status) && $apr_status !== '' && $apr_status !== null) {
                            if($apr_status == 'NULL'){
                              echo 'NA';
                            }else{
                              if($apr_status == 0){
                                echo '<span class="p-1 bg-warning">Pending For Approve</span>';
                              }else if($apr_status == 1){
                                echo '<span class="p-1 bg-success">Approved</span>';
                              }else  if($apr_status == 2){
                                echo '<span class="p-1 bg-danger">Rejected</span>';
                              }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal APR Date</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                        
                        
                        
                        if (isset($propasal_apr_date) && $propasal_apr_date !== '' && $propasal_apr_date !== null) {
                            if($propasal_apr_date == 'NULL'){
                              echo 'NA';
                            }else{
                                if($propasal_apr_date == '0000-00-00 00:00:00'){
                                    echo 'NA';
                                }else{
                                    echo htmlspecialchars($propasal_apr_date);
                                }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Approved/Reject Remarks</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                    
                        if (isset($propasal_apr_remarks) && $propasal_apr_remarks !== '' && $propasal_apr_remarks !== null) {
                            if($propasal_apr_remarks == 'NULL'){
                              echo 'NA';
                            }else{
                                if($propasal_apr_remarks == '0000-00-00 00:00:00'){
                                    echo 'NA';
                                }else{
                                    echo htmlspecialchars($propasal_apr_remarks);
                                }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
        </div>

                          <div class="row">
                             <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Attachment</h5>
                    </div>
                    <div class="card-body">
                       <a class="p-1" style="background: #f4ff0f; color: red;" target="_BLANK" href="<?=base_url().htmlspecialchars($proattach)?>">view proposal</a>
                      </p>
                    </div>
                </div>
            </div>
           </div>


                <div class="row">
                  <div class="col-md-12 text-center">
                  <?php 
                  $fullfilepath = base_url().$proattach;
                  // $fullfilepath = 'https://stemapp.in/uploads/proposal/DIY_Proposal_for_Ambuja_cement_foundation,_Raigarh.pdf';
                  $fileExtension = strtolower(pathinfo($fullfilepath, PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) { ?>
                        <img src='<?= $fullfilepath; ?>' alt='Image Preview' style='max-width:100%; height:auto;'>
                  <?php  } elseif ($fileExtension === 'pdf') { ?>
                    <iframe src='<?= $fullfilepath; ?>' style='width:100%; height:100vh;' frameborder='0'></iframe>
                  <?php } else {
                          echo "<h3>Unsupported File Type</h3>";
                          echo "<p>The file $file cannot be previewed.</p>"; 
                    } ?>
                  </div>
                </div>





                <?php 
                if($comming_user_types == 2){ 
                
                if($apr_status == 0){
                  
                  ?> 
<br>
                <div class="card text-center" style="padding: 10px;background: darkseagreen;">
                  <form method="post" action="<?=base_url();?>Reports/CheckPraposalApprovedOrRejectByManager">
                    <div class="text-center">
                    <input type="hidden" class="form-control" name="pid" Value="<?=$pid;?>">
                      <br>
                        <center><textarea class="form-control"  cols="80" rows="3" name="remarks" placeholder="Add Your Remarks" style="width:50%"></textarea></center> 
                        <br>
                      <div class="text-center">
                      <button class="btn btn-primary m-3" name="btntext" value="Approved" type="submit">Approved</button>
                      <button class="btn btn-danger m-3" name="btntext" value="Reject" type="submit">Reject</button>
                      </div>
                    </div>
                  </form>


                </div>
<?php }else{ ?>

  <hr>
  <br>
  <br>
  <div class="row">
     <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Approved Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                   
                         if (isset($apr_status) && $apr_status !== '' && $apr_status !== null) {
                            if($apr_status == 'NULL'){
                              echo 'NA';
                            }else{
                              if($apr_status == 0){
                                echo '<span class="p-1 bg-warning">Pending For Approve</span>';
                              }else if($apr_status == 1){
                                echo '<span class="p-1 bg-success">Approved</span>';
                              }else  if($apr_status == 2){
                                echo '<span class="p-1 bg-danger">Rejected</span>';
                              }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
  </div>

  <br>
  <br>
  <hr>
 <?php } } ?>



         </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type='text/javascript'>
        </script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
      </div>
    </footer>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?=base_url();?>assets/js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?=base_url();?>assets/js/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
    <script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?=base_url();?>assets/js/moment.min.js"></script>
    <script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?=base_url();?>assets/js/adminlte.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?=base_url();?>assets/js/dashboard.js"></script>
  </body>
</html>