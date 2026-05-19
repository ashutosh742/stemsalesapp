<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BD Request Details | STEM APP | WebAPP</title>
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
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    <style>
      .scrollme {
      overflow-x: auto;
      }
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
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
        <section class="content">
          <div class="container-fluid">
               <?php if ($this->session->flashdata('pending_message')): ?>
                    <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= $this->session->flashdata('pending_message'); ?>
                </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success_message')): ?>
                    <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= $this->session->flashdata('success_message'); ?>
                </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('errors_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('errors_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <?php endif; ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h4 class="text-center m-3">🏷️ BD Request Details </h4>
                    <h3 class="text-center m-3">🏷️ <?= $formatted = ucwords(str_replace("_", " ", strtolower($requestStatus))); ?></h3>
                    <h5 class="text-center m-3">🏷️ <?= ucwords(str_replace("_", " ", strtolower($rtype))); ?></h5>

                    <hr style="width:25%;">
         

                      <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                     <thead class="table-dark">
                <tr>
                    <th>🔢 Sr. No</th>
                    <th>🔢 BD REQUEST ID</th>
                    <th>📅 Start Date</th>
                    <th>👤 BD Name</th>
                    <th>📝 Request Type</th>
                    <th>🎯 Target Date</th>
                    <th>💬 Remark</th>
                     <th>🎉 Inauguration Date</th>
                    <th>📌 MNE Type</th>
                    <th>📝 Client Report Remarks</th>
                    <th>📎 Report Attachment</th>
                    <th>💼 Sales CID</th>
                    <th>📂 Project Code</th>
                    <th>🆔 SID</th>
                    <th>🏢 Client Name</th>
                    <th>👤 Contact Person Name</th>
                    <th>📋 Contact Person MObile</th>
                    <th>🗓️ Visit Date</th>
                    <th>📍 Visit Location</th>
                    <th>🎯 Expectation</th>
                    <th>🏫 School Type</th>
                    <th>🏫 No. of Schools</th>
                    <th>🏫 Schools Latter / Yes/NO</th>
                    <th>🏫 Schools Latter</th>
                    <th>🏫 Schools Latter Remarks</th>
                    <th>🏫 NGO Latter</th>
                    <th>📄 School Request Draft</th>
                    <th>📜 DM Letter</th>
                    <th>💬 DM/DEO Letter Remarks</th>
                    <th>📄 DM/DEO Draft</th>
                    <th>💬 Reason</th>
                    <th>🆔 Identification Type</th>
                    <th>📍 Single/Multi Location</th>
                    <th>✅ SValidation</th>
                    <th>🔗 Meetings Links</th>
                    <th>📍 Location Count</th>
                    <th>🔄 TStatus</th>
                    <th>🌍 State</th>
                    <th>🏙️ District</th>
                    <th>🏙️ City</th>
                    <th>📍 Venue</th>
                    <th>🔄 Assign Status</th>
                    <th>👤 Assign By</th>
                    <th>💬 Assigning Remarks</th>
                    <th>📅 Assign ID Date</th>
                    <th>🔄 Request Status</th>
                    <th>⏳ Create Date</th>
                    <th>⏳ Last Update</th>
                    <th>✅ Closed BD Request</th>

                    <th>✅ Line Manager Approved Status</th>
                    <th>✅ Line Manager Approved BY</th>
                    <th>✅ Line Manager Approved Date</th>
                    <th>✅ Line Manager Approved Remarks</th>

                    <th>✅ Admin Approved Status</th>
                    <th>✅ Admin Assign Type</th>
                    <th>✅ Admin Approved BY</th>
                    <th>✅ Admin Approved Date</th>
                    <th>✅ Admin Approved Remarks</th>
                    <th>✅ Admin Request By</th>

                    <th>✅ View Details</th>
                </tr>
            </thead>
            <tbody>
               <?php 
               $i = 1;
               foreach ($allBDRequestDatas as $row) : 
               
                        $r = rand(150, 255);
                        $g = rand(150, 255);
                        $b = rand(150, 255);
                        $backgroundColor = "rgba($r, $g, $b,.2)";
                        $hue        = rand(0, 360); // Full color wheel
                        $saturation = rand(60, 100); // High saturation for rich colors
                        $lightness  = rand(30, 45); // Low lightness for a deeper tone
                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                        ?>
                    <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="BD Request ID: <?=$row->id?>">
                        <td><?=$i; ?></td>
                        <td><?= $row->id; ?></td>
                        <td><?= htmlspecialchars($row->sdatet) ?></td>
                         <td>
                                <?php 
                                    if(!empty($row->bd_id) && $row->bd_id !='NA' && $row->bd_id != 0){ 
                                        $bdDetails = $this->Menu_model->get_userbyid($row->bd_id);
                                        if(sizeof($bdDetails) > 0){
                                            echo $bdDetails[0]->name;
                                        }else{
                                            echo " N/A";
                                        }
                                    }else{
                                        echo $row->bd_name;
                                    }
                                ?>
                        </td>
                        <td><?= htmlspecialchars($row->request_type) ?></td>

                        <td><?= htmlspecialchars($row->targetd) ?></td>

                        <td><?= htmlspecialchars($row->remark) ?></td>
                        <td><?= !empty($row->inauguration_date) ? htmlspecialchars($row->inauguration_date) : '-' ?></td>
                        <td><?= !empty($row->mne_type) ? htmlspecialchars($row->mne_type) : '-' ?></td>
                        <td><?= !empty($row->client_report_remarks) ? htmlspecialchars($row->client_report_remarks) : '-' ?></td>
                        <td><?= !empty($row->report_attachement) ? htmlspecialchars($row->report_attachement) : '-' ?></td>

                        <td>
                             <?php if(!empty($row->sales_cid)){ ?> 
                                <a href="<?= 'https://stemapp.in/Menu/CompanyDetails/'.$row->sales_cid; ?> ?>"><?= htmlspecialchars($row->sales_cid) ?></a>
                             <?php }else{echo "-";}?>
                        </td>

                        <td><?= !empty($row->project_code) ? htmlspecialchars($row->project_code) : '-' ?></td>
                        <td><?= !empty($row->sid) ? htmlspecialchars($row->sid) : '-' ?></td>
                        <td><?= htmlspecialchars($row->cname) ?></td>
                        <td><?= !empty($row->cpname) ? htmlspecialchars($row->cpname) : '-' ?></td>
                        <td><?= !empty($row->cpmo) ? htmlspecialchars($row->cpmo) : '-' ?></td>
                        <td><?= !empty($row->visitdate) ? htmlspecialchars($row->visitdate) : '-' ?></td>
                        <td><?= !empty($row->vlocation) ? htmlspecialchars($row->vlocation) : '-' ?></td>
                        <td><?= !empty($row->expectation) ? htmlspecialchars($row->expectation) : '-' ?></td>
                       
                        <td><?= htmlspecialchars($row->schooltype) ?></td>

                        <td><?= htmlspecialchars($row->noofschool) ?></td>

                        <td><?= htmlspecialchars($row->sletter) ?></td>
                        <td>
                            <?php if(!empty($row->school_request_draft)){ ?> 
                            <a href="<?= base_url(). htmlspecialchars($row->school_request_draft) ?>" target="_blank">📥 Download</a>
                            <?php }else{echo "-";}?>
                        </td>
                        <td><?= !empty($row->school_letter_remarks) ? htmlspecialchars($row->school_letter_remarks) : '-' ?></td>

                        <td>
                            <?php if(!empty($row->ngoletter)){ ?> 
                            <a href="<?= base_url(). htmlspecialchars($row->ngoletter) ?>" target="_blank">📥 Download</a>
                            <?php }else{echo "-";}?>
                        </td>

                        <td><?= !empty($row->school_request_draft) ? htmlspecialchars($row->school_request_draft) : '-' ?></td>
                        <td><?= htmlspecialchars($row->dmletter) ?></td>
                        <td><?= !empty($row->dm_deo_letter_remarks) ? htmlspecialchars($row->dm_deo_letter_remarks) : '-' ?></td>
                        
                        <td>
                            <?php if(!empty($row->dm_deo_draft)){ ?> 
                            <a href="<?= base_url(). htmlspecialchars($row->dm_deo_draft) ?>" target="_blank">📥 Download</a>
                            <?php }else{echo "-";}?>
                        </td>

                        <td><?= htmlspecialchars($row->rysn) ?></td>

                        <td><?= htmlspecialchars($row->idetype) ?></td>

                        <td><?= htmlspecialchars($row->single_or_multi_location) ?></td>

                        <td><?= !empty($row->svalidation) ? htmlspecialchars($row->svalidation) : '-' ?></td>
                        <td><?= !empty($row->meetings_links) ? htmlspecialchars($row->meetings_links) : '-' ?></td>
                        <td><?= htmlspecialchars($row->location_count) ?></td>
                        <td><?= htmlspecialchars($row->tstatus) ?></td>
                        <td><?= !empty($row->state) ? htmlspecialchars($row->state) : '-' ?></td>
                        <td><?= !empty($row->district) ? htmlspecialchars($row->district) : '-' ?></td>
                        <td><?= !empty($row->city) ? htmlspecialchars($row->city) : '-' ?></td>
                        <td><?= !empty($row->venue) ? htmlspecialchars($row->venue) : '-' ?></td>

                         <td>

                        <?php if($row->assignstatus == 0){?> 
                            <span class="bg-warning p-1">Pending</span>
                         <?php }else if($row->assignstatus == 1){ ?> 
                            <span class="bg-success p-1">Complete</span>
                        <?php }?>

                         </td>
                         <td>
                            <?php if($row->assignstatus == 0){?> 
                                <span class="bg-warning p-1">Pending</span>
                            <?php }else if($row->assignstatus == 1){
                                if(!empty($row->assign_by)){
                                    $fullname =  $this->Menu_model->getOperationUserByUID($row->assign_by)[0]->fullname;
                                    echo $fullname;
                                }else{
                                    echo "-";
                                }
                                ?> 
                            <?php }?>
                        </td>   
                        <td><?= !empty($row->assigning_remarkes) ? htmlspecialchars($row->assigning_remarkes) : '-' ?></td>
                        <td><?= htmlspecialchars($row->assignid_date) ?></td>

                         <td>

                        <?php 
                        if($row->assignstatus == 1){
                            if($row->status == 0){?> 
                                <span class="bg-danger p-1">Open</span>
                            <?php }else if($row->status == 1){ ?> 
                                <span class="bg-success p-1">Closed</span>
                            <?php }
                        }else{?> 
                         <span class="bg-warning p-1">Assigning Pending</span>
                        <?php }?>
                        </td>
                        <td><?= htmlspecialchars($row->created_at) ?></td>
                        <td><?= htmlspecialchars($row->updated_at) ?></td>
                        <td>
                            <?php if($row->status == 0){ ?>
                             <span  class="bg-danger p-1 text-white" style="cursor:pointer; border-radius:5px; transition:0.3s;" onmouseover="this.style.backgroundColor='#218838';" onmouseout="this.style.backgroundColor='#198754';"  onclick="BDRequestClosed(<?= $row->id ?>,'Closed')">BD Request Closed</span>
                             <?php }else{?>
                                
                            <?php } ?>
                        </td>


                       
                          <td>
                              <?php if(in_array($user['type_id'],[13,15,4,22,23])){ ?>
                                
                                  <?php if($row->approve_status == 0){ ?>
                                  <span  class="bg-success p-1 text-white" style="cursor:pointer; border-radius:5px; transition:0.3s;" onmouseover="this.style.backgroundColor='#218838';" onmouseout="this.style.backgroundColor='#198754';"  onclick="BDApprovedRequestClosed(<?= $row->id ?>,'Approve','LineManager')"> Approve</span>
                                  <span  class="bg-danger p-1 text-white" style="cursor:pointer; border-radius:5px; transition:0.3s;" onmouseover="this.style.backgroundColor='#010101ff';" onmouseout="this.style.backgroundColor='#198754';"  onclick="BDApprovedRequestClosed(<?= $row->id ?>,'Reject','LineManager')"> Reject</span>
                                  <?php }else if($row->approve_status == 1){?>
                                      <span class="bg-success p-1">Approved</span>
                                  <?php }else if($row->approve_status == 2){ ?>
                                      <span class="bg-danger p-1">Rejected</span>
                                  <?php } ?>

                              <?php }else{ ?> 

                                 <?php if($row->approve_status == 0){ ?>
                                    <span class="bg-warning p-1">Pending</span>
                                 <?php }else if($row->approve_status == 1){ ?>
                                    <span class="bg-success p-1">Approved</span>
                                 <?php }else if($row->approve_status == 2){ ?>
                                    <span class="bg-danger p-1">Rejected</span>
                                  <?php } ?>

                              <?php }?>
                          </td>
                          <td>
                              <?php 
                                  if(!empty($row->approve_by) && $row->approve_by !='NA' && $row->approve_by != 0){ 
                                      $bdDetails = $this->Menu_model->get_userbyid($row->approve_by);
                                      if(sizeof($bdDetails) > 0){
                                          echo $bdDetails[0]->name;
                                      }else{
                                          echo " N/A";
                                      }
                                  }else{
                                      echo "-";
                                  }
                              ?>
                          </td>
                          <td>
                              <?= !empty($row->approve_date) ? htmlspecialchars($row->approve_date) : '-' ?>  
                          </td>
                          <td>
                              <?= !empty($row->approve_remarks) ? htmlspecialchars($row->approve_remarks) : '-' ?>
                          </td>





                          <td>

                              <?php 
                              
                                if(in_array($user['type_id'],[1,2])){ ?>
                                
                                  <?php if($row->approve_by_admin_status == 0 && $row->approve_status == 1){ ?>
                                  <span  class="bg-success p-1 text-white" style="cursor:pointer; border-radius:5px; transition:0.3s;" onmouseover="this.style.backgroundColor='#218838';" onmouseout="this.style.backgroundColor='#198754';"  onclick="BDApprovedRequestClosed(<?= $row->id ?>,'Approve','Admin')"> Approve</span>
                                  <span  class="bg-danger p-1 text-white" style="cursor:pointer; border-radius:5px; transition:0.3s;" onmouseover="this.style.backgroundColor='#010101ff';" onmouseout="this.style.backgroundColor='#198754';"  onclick="BDApprovedRequestClosed(<?= $row->id ?>,'Reject','Admin')"> Reject</span>
                                  <?php }else if($row->approve_by_admin_status == 1){?>
                                      <span class="bg-success p-1">Approved</span>
                                  <?php }else if($row->approve_by_admin_status == 2){ ?>
                                      <span class="bg-danger p-1">Rejected</span>
                                  <?php }else if($row->approve_status == 0){?> 
                                      <span class="bg-warning p-1">Pending By Line Manager</span>
                                    <?php }else if($row->approve_status == 2){?> 
                                      <span class="bg-danger p-1">Rejected By Line Manager</span>
                                    <?php } ?>

                              <?php }else{ ?> 

                                 <?php if($row->approve_by_admin_status == 0){ ?>
                                    <span class="bg-warning p-1">Pending</span>
                                 <?php }else if($row->approve_by_admin_status == 1){ ?>
                                    <span class="bg-success p-1">Approved</span>
                                 <?php }else if($row->approve_by_admin_status == 2){ ?>
                                    <span class="bg-danger p-1">Rejected</span>
                                  <?php } ?>

                              <?php }?>
                          </td>

                           <td>
                              <?= !empty($row->approve_by_admin_types) ? htmlspecialchars($row->approve_by_admin_types) : '-' ?>
                          </td>

                          <td>
                              <?php 
                                  if(!empty($row->approve_by_admin_by) && $row->approve_by_admin_by !='NA' && $row->approve_by_admin_by != 0){ 
                                      $bdDetails = $this->Menu_model->get_userbyid($row->approve_by_admin_by);
                                      if(sizeof($bdDetails) > 0){
                                          echo $bdDetails[0]->name;
                                      }else{
                                          echo " N/A";
                                      }
                                  }else{
                                      echo "-";
                                  }
                              ?>
                          </td>
                          <td>
                              <?= !empty($row->approve_by_admin_date) ? htmlspecialchars($row->approve_by_admin_date) : '-' ?>
                          </td>
                          <td>
                              <?= !empty($row->approve_by_admin_remarks) ? htmlspecialchars($row->approve_by_admin_remarks) : '-' ?>
                          </td>

                          <td>
                              <?php 
                              
                              if(!empty($row->approve_by_admin_types) && $row->approve_by_admin_types == 'Project Coordinator'){

                                $approve_by_admin_task_by =  !empty($row->approve_by_admin_task_by) ? htmlspecialchars($row->approve_by_admin_task_by) : 0;
                                  if($approve_by_admin_task_by !=0){
                                    echo $approve_by_admin_task_by =  $this->Menu_model->get_userbyid($approve_by_admin_task_by)[0]->name;

                                  }
                              }

                              ?>
                          </td>

                         <td>
                              <a class="bg-success p-1" target="_blank" href="<?= 'https://stemoppapp.in/Menu/BDRequestDetails/'.$row->id; ?>/<?=$user['user_id']?>">View</a>
                        </td>
                    </tr>
                    <?php $i++; endforeach; ?>
                    </tbody>
                  </table>
                </div>



                  <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">BD Request Closed Remark</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                   <form action="<?=base_url();?>Menu/BDRequestClosedSubmit" method="post" >
                                        <input type="hidden" value="<?=$rtype?>" name="rtype" required>
                                        <input type="hidden" value="<?=$requestStatus?>" name="requestStatus" required>
                                        <input type="hidden" id="bd_request_id" value="" name="bd_request_id" required>
                                         <div class="mb-3 mt-3">
                                          <textarea id="bd_request_closed_remarks" name="bd_request_closed_remarks" cols="30" placeholder="Add Remark " class="form-control" required rows="4"></textarea>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-success mt-2">Submit</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                  <div class="modal fade" id="exampleModalCenterdata_ApprovedByLineManager" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">BD Request <span id='approvedTextMessagetop'></span> Remark</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                   <form action="<?=base_url();?>Menu/BDRequestApprovedByLineManager" method="post">
                                        
                                        <input type="hidden" value="<?=$rtype?>" name="rtype" required>
                                        <input type="hidden" value="<?=$requestStatus?>" name="requestStatus" required>
                                        <input type="hidden" id="bd_request_id_bylm" value="" name="bd_request_id_bylm" required>
                                        <input type="hidden" id="bd_request_text_message_bylm" value="" name="bd_request_lm_status" required>
                                        <input type="hidden" id="bd_request_apr_status_by_whome" value="" name="bd_request_apr_status_by_whome" required>

                                        <?php if($rtype == 'SSCHOOLID'){ ?>
                                          <div class="form-group" id="selectTaskPerformByDiv">
                                            <label for="lm_apr_status">Select Task Perform By</label>
                                            <select class="form-control" id="approve_by_admin_types" name="approve_by_admin_types">
                                              <option value="">-- Select -- </option>
                                              <option value="Project Coordinator">Project Coordinator</option>
                                              <option value="PIA">PIA</option>
                                            </select>
                                          </div>
                                        <?php } ?>

                                          <?php $pcDatas =  $this->Menu_model->GetAllUserByDepartment(16); ?>
                                          <div class="form-group" id="performByProjectCoordinatorDiv">
                                            <label for="lm_apr_status">Select Perform By Project Coordinator </label>
                                            <select class="form-control" id="perform_by_project_coordinator" name="perform_by_project_coordinator">
                                              <option value="">-- Select -- </option>
                                              <?php foreach($pcDatas as $pcRow){ ?>
                                                <option value="<?= $pcRow->user_id ?>"><?= $pcRow->name ?></option>
                                              <?php } ?>
                                            </select>
                                          </div>

                                         <div class="form-group">
                                            <label for="lm_apr_status"><span id="approvedTextMessage">Approved</span> Status</label>
                                            <select class="form-control" id="lm_apr_status" name="lm_apr_status" required>
                                              <option value="">-- Select Status -- </option>
                                              <option value="Approve">Approve</option>
                                              <option value="Reject">Reject</option>
                                            </select>
                                          </div>

                                         <div class="mb-3 mt-3">
                                          <textarea id="bd_request_remarks" name="bd_request_remarks" cols="30" placeholder="Add Remark " class="form-control" required rows="4"></textarea>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-success mt-2">Submit</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                   </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>

    <style>
      .modal-content {
    background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
    transition: all 0.3s ease-in-out;
}
    </style>

    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script>
         function BDRequestClosed(id,val){
                $('#exampleModalCenterdata').modal('show');
                $('#bd_request_id').val(id);
            }
         function BDApprovedRequestClosed(id,val,bywhom){

          $("#selectTaskPerformByDiv").hide();
          $("#performByProjectCoordinatorDiv").hide();

                if(val == 'Approve'){
                    $('#lm_apr_status option[value="Approve"]').prop('selected', true);
                    $('#lm_apr_status option[value="Reject"]').prop('disabled', true);
                }else if(val == 'Reject'){
                    $('#lm_apr_status option[value="Reject"]').prop('selected', true);
                    $('#lm_apr_status option[value="Approve"]').prop('disabled', true);
                }
                $('#approvedTextMessagetop').text(val);
                $('#approvedTextMessage').text(val);
                $('#bd_request_id_bylm').val(id);
                $('#bd_request_text_message_bylm').val("");
                $('#bd_request_text_message_bylm').val(val);
                if(bywhom == 'LineManager'){
                    $("#selectTaskPerformByDiv").hide();
                    $('#approve_by_admin_types').prop('required', false);
                }else if(bywhom == 'Admin'){
                    $("#selectTaskPerformByDiv").show();
                    $('#approve_by_admin_types').prop('required', true);
                }
                $('#bd_request_apr_status_by_whome').val(bywhom);

                $('#exampleModalCenterdata_ApprovedByLineManager').modal('show');
                
            }

            $('#approve_by_admin_types').on('change', function () {
                let val = $(this).val();
                if(val === "Project Coordinator") {
                    $("#performByProjectCoordinatorDiv").show();
                    $('#perform_by_project_coordinator').prop('required', true);
                } else {
                    $("#performByProjectCoordinatorDiv").hide();
                    $('#perform_by_project_coordinator').prop('required', false);
                }
                console.log(val); 
            });
    </script>
    
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
    <!-- DataTables  & Plugins -->
    <script src="<?=base_url();?>assets/js/jquery.dataTables.min.js"></script>
    <script src="<?=base_url();?>assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?=base_url();?>assets/js/dataTables.responsive.min.js"></script>
    <script src="<?=base_url();?>assets/js/responsive.bootstrap4.min.js"></script>
    <script src="<?=base_url();?>assets/js/dataTables.buttons.min.js"></script>
    <script src="<?=base_url();?>assets/js/buttons.bootstrap4.min.js"></script>
    <script src="<?=base_url();?>assets/js/jszip.min.js"></script>
    <script src="<?=base_url();?>assets/js/pdfmake.min.js"></script>
    <script src="<?=base_url();?>assets/js/vfs_fonts.js"></script>
    <script src="<?=base_url();?>assets/js/buttons.html5.min.js"></script>
    <script src="<?=base_url();?>assets/js/buttons.print.min.js"></script>
    <script src="<?=base_url();?>assets/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?=base_url();?>assets/js/adminlte.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?=base_url();?>assets/js/dashboard.js"></script>
    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>