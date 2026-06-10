<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create BD Request | STEM APP | WebAPP</title>
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
      .card-body.box-profile {
      background: #c1cbdb87;
      }
      label {
      color: #ff0059;
      }
      .strongcolor {
      font-weight: bolder;
      color: #85ff00;
      }
      .animated-form {
      background: #f8f9fa;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      }
      .form-section {
      background: white;
      padding: 2rem;
      margin-bottom: 2rem;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      transition: transform 0.3s ease, opacity 0.3s ease;
      }
      .section-title {
      color: #2c3e50;
      font-weight: 600;
      border-left: 4px solid #3498db;
      padding-left: 1rem;
      }
      .floating-label {
      position: relative;
      }
      .floating-label label {
      position: absolute;
      top: 18px;
      left: 15px;
      pointer-events: none;
      transition: 0.2s ease all;
      color: #95a5a6;
      font-size: 0.9em;
      }
      .animated-input {
      height: 55px;
      border: 1px solid #ecf0f1;
      border-radius: 10px;
      padding-top: 25px;
      transition: all 0.3s ease;
      }
      .animated-input:focus {
      border-color: #3498db;
      box-shadow: none;
      }
      .animated-input:focus ~ label,
      .animated-input:not(:placeholder-shown) ~ label {
      top: 8px;
      font-size: 0.75em;
      color: #3498db;
      }
      .submit-btn {
      background: linear-gradient(135deg, #3498db, #2980b9);
      border: none;
      padding: 1rem 2.5rem;
      font-size: 1.1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(52,152,219,0.4);
      }
      @keyframes slideIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
      }
      .slide-in {
      animation: slideIn 0.5s ease forwards;
      }
      .animated-container {
      transition: height 0.3s ease;
      }
      .location-group {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1rem;
      animation: fadeIn 0.3s ease;
      }
      @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
      }
      .handover-card{
        background: #22222212; padding: 10px;
      }
      .handoverbg-card{background: azure; padding: 10px;}
      .section-title {
    color: darkgreen;
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
   
          $reqtaskData          = $this->Menu_model->getTBLTaskByID($req_taskid);
        
          $appointmentdatetime  = $reqtaskData[0]->appointmentdatetime;
          $actontaken           = $reqtaskData[0]->actontaken;
          $purpose_achieved     = $reqtaskData[0]->purpose_achieved;
          $remarks              = $reqtaskData[0]->remarks;
          $user_id              = $reqtaskData[0]->user_id;
          $actiontype_id        = $reqtaskData[0]->actiontype_id;
          $purpose_id           = $reqtaskData[0]->purpose_id;
          $cid_id               = $reqtaskData[0]->cid_id;
          
          $actiontype_idData    = $this->Menu_model->getTaskAction($actiontype_id);
          $actiontype_idname    = $actiontype_idData[0]->name;
          
          $get_purposeNameById  = $this->Menu_model->get_purposeNameById($purpose_id);
          
          $spclReqData          = $this->Menu_model->GetSpecialBdrequestByTaskID($req_taskid);
          
          $client_id            = $spclReqData[0]->client_id;
          $request_id           = $spclReqData[0]->request_id;
          $handover_id          = $spclReqData[0]->handover_id;

          $client_idData        = $this->Menu_model->GetSpecialBdrequestTypeByID($client_id);
          $client_idName        = $client_idData[0]->type;
          
          $request_idData       = $this->Menu_model->GetSpecialBdrequestByID($request_id);
          $request_typeName     = $request_idData[0]->request_type;
          
          $reqCmpData           = $this->Menu_model->get_cmpbyinid($cid_id);
          $getSpecialBdrequest  = $this->Menu_model->GetSpecialBdrequest();
          $compname             = $reqCmpData[0]->compname;
          $budget               = $reqCmpData[0]->budget;
          $address              = $reqCmpData[0]->address;
          $website              = $reqCmpData[0]->website;
          $cmpid_id             = $reqCmpData[0]->cmpid_id;
          // $cmpid_id             = 65765;
          
          $handOverDatas = $this->Menu_model->GetAllHandoverRequestInThisCompany($cmpid_id,$user_id);
          // $handover_id = '';
          if($handover_id !== ''){
            $chandOverData  = $this->Menu_model->GetHandoverDetailsByHid($handover_id);
            $chandOverDatacnt =sizeof($chandOverData);
            if($chandOverDatacnt > 0){
            $chandOver      = $chandOverData[0];
           
            $project_tenure = $chandOver->project_tenure;
            $noofschool     = $chandOver->noofschool;
            $remark         = $chandOver->remark;
            $created_at     = $chandOver->created_at;
            $logo           = $chandOver->logo;
          
            $be_request     = $chandOver->be_request;
            $spd_identify_by     = $chandOver->spd_identify_by;
            $readonly       = 'readonly';
          
            $rsData         =   $this->Menu_model->GetSchoolDataonHandoverTime($handover_id);
            
          
            }else{
            $logo           = '';
            $project_tenure = '';
            $noofschool     = '';
            $remark         = '';
            $created_at     = '';
            $created_at     = '';
            $be_request     = '';
            $readonly       = '';
            }
          }
          
          
         
          
          /*
          
          ============ $client_id - Client Type ==========================
          
              Database Table : special_bdrequest_type
                1 - On Board Client
                2 - New Client
          
          ============ $request_id  - BD Request Type =====================
          
              Database Table - special_bdrequest 
                #	Category
                1	Client Report	
                2	School Identification	
                3	On Board Client School Visit	
                4	Inauguration	
                5	Employee Engagement	
                6	School Maintenance	
                7	RTTP	
                8	DIY	
                9	MnE	
                10	New client school visit	
                11	Online Demo	
                12	Offline Demo	
                13	New Client Report	

                14 CM call or Visit
                15 PM Call or Visit
                16 PI Call or Visit
                17 Installation Person Call or Visit
                18 Other Department Call (Attachement / Remarks)
          
          */

          

          
           // On Board Client
          //  $request_typeName = "Client Report";
          //  $request_typeName = "School Identification";
          //  $request_typeName = "On Board Client School Visit";
          //  $request_typeName = "Inauguration";
          //  $request_typeName = "Employee Engagement";
          //  $request_typeName = "School Maintenance";
           
          //  $request_typeName = "RTTP";
          //  $request_typeName = "DIY";
          //  $request_typeName = "MnE";
           // New Client
          //  $request_typeName = "School Identification";
          //  $request_typeName = "New client school visit";
          
          //  $request_typeName = "Online Demo";
          //  $request_typeName = "Offline Demo";
          
          //  $request_typeName = "New Client Report";
          




          // Developement List
          // 1 School Identification                        - Done
          // 2 Inauguration                                 - WIP
          // 3 Client engagement
          // 4 RTTP 
          // 5 DIY
          // 6 M&E visit
          // 7 Offline client visit-school visit (New)
          // 8 Online demo
          // 9 New client reports
          // 10 Onboarded client visit
          // 11 Reports (any customized)
          // 12 CM call or visit
          // 13 PM call or visit (mandatory auto task)
          // 14 PIA call or visit
          // 15 Installation person call or visit
          // 16 Other dept call









          // $client_idName  = 'On Board Client';
          
          //  $request_id =15;
          
          ?>
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-12 bg-info text-center p-2">
                <h3 class="m-0">Create BD Request</h3>
                <hr>
                <span><strong class="strongcolor"> Task Datetime : </strong> <?=$appointmentdatetime ?></span>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row p-3">
              <div class="col-sm col-md-12 col-lg-12">
                <div class="card card-primary card-outline">
                  <div class="card text-center">
                    <h3 style="color: darkgreen !important; font-family: inherit !important; text-transform: uppercase !important;"> <u><?= $request_typeName ?></u> <span style="color:darkgreen!important;">Request of</span> <span style="color: darkgreen !important;"><u><?=$compname;?> (<?= $cmpid_id;?>)</u></span></h3>
                  </div>
                  <div class="card-body box-profile animated-form">

                  <?php 
                   //  echo $request_id;
                  ?>



 <!-- Start On Board Client School Visit Request Form -->
 <?php  if($request_id == 3){ ?>
                    <form action="<?=base_url();?>Menu/CreateOnboardClientSchoolVisitBDRequest" id="CreateOnboardClientSchoolVisitBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required>
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Contact Information</h4>
                       
                        <div class="row">
                          <div class="col-md-6 form-group floating-label">
                              <div class="form-group floating-label">
                                <input type="text" class="form-control animated-input" name="contact_person_name" id="contact_person_name" required>
                                <label for="contact_person_name">Contact Person Name</label>
                              </div>
                            </div>
                            <div class="col-md-6 form-group floating-label">
                              <div class="form-group floating-label">
                                <input type="number" class="form-control animated-input" name="contact_person_mobile_no" id="contact_person_mobile_no" required>
                                <label for="contact_person_mobile_no">Contact Person Mobile Number</label>
                              </div>
                            </div>
                        </div>

                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <input type="number" class="form-control animated-input" name="noofschool" id="noofschool_onboard_client_visit" required />
                              <label>No of schools need to visit</label>
                            </div>
                            <strong><small id="noofschool_onboard_client_visit_messgae" class="text-success"></small></strong>
                          </div>                         

                         
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="onboard_client_school_visit_date" id="inauguration_date" required min="<?= $minDate ?>" />
                              <label><?php echo $request_typeName; ?> Target Date </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Max Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Remarks</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed On Board Client School Visit Request Form -->



                   <!-- Start Inauguration Form -->
                  <?php  if($request_id == 4){ ?>
                    <form action="<?=base_url();?>Menu/InaugurationBDRequest" id="InaugurationForm" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for Inauguration</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="inauguration_date" id="inauguration_date" required min="<?= $minDate ?>" >
                              <label>Inauguration Date</label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Inauguration Form -->



               <!-- Start Employee Engagement Form -->
               <?php  if($request_id == 5){ ?>
                    <form action="<?=base_url();?>Menu/ClientEngagementBDRequest" id="ClientEngagement" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for Client Engagement</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="client_engagement_date" id="inauguration_date" required min="<?= $minDate ?>" >
                              <label>Client Engagement Date for visit </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Employee Engagement Form -->


                  <!-- Start RTTP Form -->
                  <?php  if($request_id == 7){ ?>
                    <form action="<?=base_url();?>Menu/ClientRTTPBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for RTTP</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="rttp_date" id="inauguration_date" required min="<?= $minDate ?>" >
                              <label>RTTP Date for visit </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed RTTP Form -->

   <!-- Start DIY Form -->
   <?php  if($request_id == 8){ ?>
                    <form action="<?=base_url();?>Menu/CreateDIYBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for DIY</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="diy_date" id="inauguration_date" required min="<?= $minDate ?>" >
                              <label>DIY Date for visit </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed DIY Form -->







                 <!-- Start DIY Form -->
   <?php  if($request_id == 6){ ?>
                    <form action="<?=base_url();?>Menu/CreateSchoolMaintenanceBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for Maintenance</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="diy_date" id="inauguration_date" required min="<?= $minDate ?>" >
                              <label>Maintenance Date for visit </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>School Maintenance Remarks</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed DIY Form -->













  <!-- Start Mne Form -->
  <?php  if($request_id == 9){ ?>
                    <form action="<?=base_url();?>Menu/CreateMnEBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for MnE</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="MnE_Type" id="MnE_Type" required>
                                <option value="Base Line & END Line">Base Line & END Line M&E</option>
                                <option value="Base Line M&E">Base Line M&E</option>
                                <option value="END Line M&E">END Line M&E</option>
                              </select>
                              <label>Select MnE Type</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="mne_date" id="inauguration_date" required min="<?= $minDate ?>" >
                              <label>MnE Date </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Mne Form -->



                  <?php 
                  // Start School Identification Form
                  if($request_id == 10 OR $request_id == 2){ ?>
                    <form action="<?=base_url();?>Menu/schoolIdentificationRequest" id="schoolIdentificationForm" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                      <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <!-- Form Sections -->
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>


                        <?php

                        if($handover_id !=='' && !is_null($handover_id)): ?>
                      <div class="handover-card">
                        <hr>
                        <h4 class="section-title mb-4">Handover Details</h4>
                        <hr>
                        <div class="form-group floating-label">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Logo</th>
                                <th scope="col">Project Year</th>
                                <th scope="col">No Of School</th>
                                <th scope="col">Identify By</th>
                                <th scope="col">Remarks</th>
                                <th scope="col">Handover Request Date</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th><img src="<?=base_url().$logo?>" class="img-fluide" width="200" alt="logo not found"></th>
                                <th><?=$project_tenure;?></th>
                                <th><?=$noofschool;?></th>
                                <th><?=$spd_identify_by;?></th>
                                <th><?=$remark;?></th>
                                <th><?=$created_at;?></th>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <?php 
                          // dd($rsData);
                           if(sizeof($rsData) > 0): ?>
                        <hr>
                        <h4 class="section-title mb-4">School info</h4>
                        <hr>
                        <div class="form-group floating-label">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">School Name</th>
                                <th scope="col">State</th>
                                <th scope="col">District</th>
                                <th scope="col">City</th>
                                <th scope="col">School Address</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $si=1; foreach($rsData as $schoolData): ?>
                              <tr>
                                <th><?=$si?></th>
                                <th><?=$schoolData->sname;?></th>
                                <th><?=$schoolData->sstate;?></th>
                                <th><?=$schoolData->sdistrict;?></th>
                                <th><?=$schoolData->scity;?></th>
                                <th><?=$schoolData->saddress;?></th>
                              </tr>
                              <?php $si++; endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                        <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="handoverbg-card mt-1">
                        <hr>
                        <h4 class="section-title mb-4">School Information</h4>
                        <div class="row g-3">
                          <!-- <div class="col-md-6 form-group floating-label">
                            <select class="form-control animated-input" name="idetype" required>
                                <option value="Physical">Physical</option>
                                <option value="Virtual">Virtual</option>
                            </select>
                            <label>Identification Type</label>
                            </div> -->
                          <div class="col-md-6 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="idetype" required>
                                <option value="Physical">Physical</option>
                                <option value="Telephonic">Telephonic</option>
                              </select>
                              <label>Identification Type</label>
                            </div>
                          </div>
                          <div class="col-md-6 form-group floating-label">
                            <select class="form-control animated-input" name="tyschool" required>
                              <option>Govt.</option>
                              <option>Private</option>
                              <option>Mix</option>
                            </select>
                            <label>Type of School</label>
                          </div>
                        </div>
                        <div class="form-group floating-label">
                          <input type="number" class="form-control animated-input" name="noschool" <?=$readonly;?> id="noschool" value="<?=$noofschool;?>" required>
                          <label for="noschool">Number of Schools</label>
                        </div>
                        </div>


                        
                        <hr>
                        <h4 class="section-title mb-4">Location Details</h4>
                        <hr>
                        <div class="form-group floating-label">
                          <select class="form-control animated-input" id="single_or_multi_location" name="single_or_multi_location" required>
                            <option value="">Select Location Type</option>
                            <option value="single">Single Location</option>
                            <option value="multiple">Multiple Locations</option>
                          </select>
                          <label>Location Type</label>
                        </div>
                        <!-- Dynamic Location Fields -->
                        <div id="location-single-container" class="animated-container">
                          <!-- Fields will be injected here via JavaScript -->
                          <div class="row g-3 location-group">
                            <div class="col-md-6">
                              <div class="form-group floating-label">
                                <input type="text" class="form-control animated-input" name="single_location_name[]">
                                <label for="noschool">Location</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group floating-label">
                                <input type="text" class="form-control animated-input" <?=$readonly;?> value="<?=$noofschool;?>" name="single_num_schools[]">
                                <label for="noschool">Number of Schools</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="location-multiple-container" class="animated-container">
                          <!-- Fields will be injected here via JavaScript -->
                          <div class="row g-3 location-group">
                            <div class="col-md-5">
                              <div class="form-group floating-label">
                                <input type="text" class="form-control animated-input" name="multiple_location_name[]">
                                <label for="noschool">Location</label>
                              </div>
                            </div>
                            <div class="col-md-5">
                              <div class="form-group floating-label">
                                <input type="text" class="form-control animated-input" name="multiple_num_schools[]">
                                <label for="noschool">Number of Schools</label>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <button type="button" class="btn btn-success add_location" id="add_new_location">+</button>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Documents</h4>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group floating-label">
                                    <input type="file" class="form-control animated-input" id="attachement_ngo_letter" name="filename" accept=".pdf" required>
                                    <label>Attach NGO Letter (PDF only)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="attachement_ngo_letter_preview"></div>
                            </div>
                        </div>


                        <!-- Conditional Fields -->
                        <div class="conditional-field">
                          <!-- School Letter -->
                          <div class="form-group floating-label">
                            <select class="form-control animated-input" name="sletter" id="sletter" required>
                              <option value="">Select Requirement</option>
                              <option value="Yes">Yes</option>
                              <option value="No">No</option>
                            </select>
                            <label>School Letter Required</label>
                          </div>
                          <div id="school_letter_remarks_box">
                            <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group floating-label">
                                  <input type="file" class="form-control animated-input" id="school_request_draft" name="school_request_draft" >
                                  <label>School Request Draft</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                              <div id="school_request_draft_preview"></div>
                              </div>
                            </div>
                            <div class="form-group floating-label">
                              <textarea class="form-control animated-input" name="school_letter_remarks" id="school_letter_remarks" ></textarea>
                              <label>School Letter Remarks</label>
                            </div>
                          </div>
                          <!-- DM/DEO Letter -->
                          <div class="form-group floating-label">
                            <select class="form-control animated-input" name="dmletter" id="dmletter" required>
                              <option value="">Select Requirement</option>
                              <option value="Yes">Yes</option>
                              <option value="No">No</option>
                            </select>
                            <label>DM/DEO Letter Required</label>
                          </div>
                          <div id="dm_deo_letter_remarks_box">
                          <div class="row">
                            <div class="col-md-6">
                            <div class="form-group floating-label">
                              <input type="file" class="form-control animated-input" id="dm_deo_draft" name="dm_deo_draft">
                              <label>DM/DEO Draft</label>
                            </div>
                            </div>
                            <div class="col-md-6">
                            <div id="dm_deo_draft_preview"></div>
                            </div>
                          </div>
                           

                            <div class="form-group floating-label">
                              <textarea class="form-control animated-input" name="dm_deo_letter_remarks" id="dm_deo_letter_remarks" ></textarea>
                              <label>DM/DEO Letter Remarks</label>
                            </div>
                          </div>

                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                          <hr>
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" min="<?= $minDate; ?>" max="<?= $maxDate; ?>" required>
                            <label>Max Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>


                        </div>
                        <!-- Submit Section -->
                        <div class="form-section text-center mt-5">
                          <button type="submit" class="btn btn-primary btn-lg submit-btn" id="schoolIdentificationBtn">
                          <span class="submit-text">Submit Request</span>
                          <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                          </button>
                        </div>
                      </div>
                    </form>
                    <?php 
                  // Closed School Identification Form
                   } ?>










  <!-- Start New client school visit Request Form -->
  <?php  if($request_id == 11){ ?>
                    <form action="<?=base_url();?>Menu/CreateNewClientSchoolVisitBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>



        

                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Contact Information</h4>
                       
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <input type="text" class="form-control animated-input" name="contact_person_name" id="contact_person_name" required>
                              <label for="contact_person_name">Contact Person Name</label>
                            </div>
                          </div>
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <input type="number" class="form-control animated-input" name="contact_person_mobile_no" id="contact_person_mobile_no" required>
                              <label for="contact_person_mobile_no">Contact Person Mobile Number</label>
                            </div>
                          </div>
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <input type="number" class="form-control animated-input" name="noschool" required>
                              <label for="noschool">Number of Schools</label>
                            </div>
                          </div>
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <textarea class="form-control animated-input" name="new_client_school_visit_location" id="new_client_school_visit_location" required></textarea>
                              <label>Visit Location</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="school_visit_date" id="inauguration_date" required min="<?= $minDate ?>" >
                              <label>Client school visit Target Date </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Max Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Remark and instrcutions</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed New client school visit Request Form -->




                <!-- Start Online Demo Request Form -->
  <?php  if($request_id == 12){ ?>
                    <form action="<?=base_url();?>Menu/CreateOnlineDemoBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Contact Information</h4>
                       
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <input type="text" class="form-control animated-input" name="contact_person_name" id="contact_person_name" required>
                              <label for="contact_person_name">Contact Person Name</label>
                            </div>
                          </div>
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <input type="number" class="form-control animated-input" name="contact_person_mobile_no" id="contact_person_mobile_no" required>
                              <label for="contact_person_mobile_no">Contact Person Mobile Number</label>
                            </div>
                          </div>
                       
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <textarea class="form-control animated-input" name="online_demo_meetings" id="online_demo_meetings" required></textarea>
                              <label> Meeting Links</label>
                            </div>
                          </div>
                         
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="online_demo_date" id="inauguration_date" required min="<?= $minDate ?>" />
                              <label><?= $request_typeName ?> Target Date </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Max Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Remark and instrcutions</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Online Demo Request Form -->


                <!-- Start Offline Demo Request Form -->
                <?php  if($request_id == 13){ ?>
                    <form action="<?=base_url();?>Menu/CreateOfflineDemoBDRequest" id="CreateOfflineDemoBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required>
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Contact Information</h4>
                       
                        <div class="row">
                          <div class="col-md-6 form-group floating-label">
                              <div class="form-group floating-label">
                                <input type="text" class="form-control animated-input" name="contact_person_name" id="contact_person_name" required>
                                <label for="contact_person_name">Contact Person Name</label>
                              </div>
                            </div>
                            <div class="col-md-6 form-group floating-label">
                              <div class="form-group floating-label">
                                <input type="number" class="form-control animated-input" name="contact_person_mobile_no" id="contact_person_mobile_no" required>
                                <label for="contact_person_mobile_no">Contact Person Mobile Number</label>
                              </div>
                            </div>
                        </div>
                          <?php $getStateData = $this->Menu_model->GetState();   ?>
                          <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="get_state" id="get_state" required >
                                <option value="">Select State</option>
                                <?php foreach($getStateData as $stateData): ?>
                                      <option value="<?= $stateData->state_title; ?>">
                                          <?= $stateData->state_title ?>
                                      </option>
                                  <?php endforeach; ?>
                              </select>
                              <label for="task_type">State</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="get_district" id="get_district" required >
                              </select>
                              <label for="task_type">District</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="get_city" id="get_city" required >
                              </select>
                              <label for="task_type">City</label>
                            </div>
                          </div>
                        </div>

                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <textarea class="form-control animated-input" name="offline_demo_venue" id="offline_demo_venue" required></textarea>
                              <label> Venue</label>
                            </div>
                          </div>
                         
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="offline_demo_date" id="inauguration_date" required min="<?= $minDate ?>" />
                              <label><?php echo $request_typeName; ?> Target Date </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Max Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Remark and instrcutions</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Offline Demo Request Form -->



                 <!-- Start New Client Report Request Form -->
                 <?php  if($request_id == 14){ ?>
                    <form action="<?=base_url();?>Menu/CreateNewClientReportBDRequest" id="CreateNewClientReportBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required>
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Report Remarks</h4>
                       
                
                          <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <textarea class="form-control animated-input" name="new_client_report_remarks" id="new_client_report_remarks" required></textarea>
                              <label> Write New Client Report Remarks</label>
                            </div>
                          </div>

                          
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group floating-label">
                                  <input type="file" class="form-control animated-input" id="new_client_report_attachement" name="new_client_report_attachement" >
                                  <label>Add Attachment (Optional)</label>
                                </div>
                              </div>
                            </div>


                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="new_client_report_date" id="inauguration_date" required min="<?= $minDate ?>">
                              <label><?= $request_typeName ?> Target Date </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Max Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Remark and instrcutions</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed New Client Report Request Form -->


 <!-- Start Reports (any customized) Request Form -->
 <?php  if($request_id == 15 OR $request_id == 1){ ?>
                    <form action="<?=base_url();?>Menu/CreateReportsAnyCustomizedBDRequest" id="CreateOnboardClientSchoolVisitBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required>
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>

                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                                          
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="custmized_report_date" id="inauguration_date" required min="<?= $minDate ?>" />
                              <label><?php echo $request_typeName; ?> Target Date </label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Max Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Customized Report Remarks</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Reports (any customized) Request Form -->





<!-- Start CM Call or Visit Form -->
<?php  if($request_id == 16){ ?>
                    <form action="<?=base_url();?>Menu/CreateCMCallOrVisitBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for <?= $request_typeName ?></label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="cm_call_or_visit_date" id="inauguration_date" required min="<?= $minDate ?>">
                              <label><?= $request_typeName ?> Date </label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <select class="form-control animated-input" name="cm_call_or_visit" id="cm_call_or_visit" required>
                              <option value="">Select</option>
                              <option value="Only Call">Only Call</option>
                              <option value="Only Visit">Only Visit</option>
                              <option value="Call And Visit">Call And Visit</option>
                              </select>
                              <label>Select <?= $request_typeName ?></label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed CM Call or Visit Form -->





<!-- Start PM Call or Visit Form -->
<?php  if($request_id == 17){ ?>
                    <form action="<?=base_url();?>Menu/CreatePMCallOrVisitBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for <?= $request_typeName ?></label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="cm_call_or_visit_date" id="inauguration_date" required min="<?= $minDate ?>">
                              <label><?= $request_typeName ?> Date </label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <select class="form-control animated-input" name="cm_call_or_visit" id="cm_call_or_visit" required>
                              <option value="">Select</option>
                              <option value="Only Call">Only Call</option>
                              <option value="Only Visit">Only Visit</option>
                              <option value="Call And Visit">Call And Visit</option>
                              </select>
                              <label>Select <?= $request_typeName ?></label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed PM Call or Visit Form -->


<!-- Start PIA Call or Visit Form -->
<?php  if($request_id == 18){ ?>
                    <form action="<?=base_url();?>Menu/CreatePIACallOrVisitBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for <?= $request_typeName ?></label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="cm_call_or_visit_date" id="inauguration_date" required min="<?= $minDate ?>">
                              <label><?= $request_typeName ?> Date </label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <select class="form-control animated-input" name="cm_call_or_visit" id="cm_call_or_visit" required>
                              <option value="">Select</option>
                              <option value="Only Call">Only Call</option>
                              <option value="Only Visit">Only Visit</option>
                              <option value="Call And Visit">Call And Visit</option>
                              </select>
                              <label>Select <?= $request_typeName ?></label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed PIA Call or Visit Form -->


<!-- Start Insta Call or Visit Form -->
<?php  if($request_id == 19){ ?>
                    <form action="<?=base_url();?>Menu/CreateInstaCallOrVisitBDRequest" id="ClientRTTPBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for <?= $request_typeName ?></label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="cm_call_or_visit_date" id="inauguration_date" required min="<?= $minDate ?>">
                              <label><?= $request_typeName ?> Date </label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <select class="form-control animated-input" name="cm_call_or_visit" id="cm_call_or_visit" required>
                              <option value="">Select</option>
                              <option value="Only Call">Only Call</option>
                              <option value="Only Visit">Only Visit</option>
                              <option value="Call And Visit">Call And Visit</option>
                              </select>
                              <label>Select <?= $request_typeName ?></label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Insta Call or Visit Form -->


<!-- Start Other Department Call BD Request Form -->
<?php  if($request_id == 20){ ?>
                    <form action="<?=base_url();?>Menu/CreateOtherDepartmentCallBDRequest" id="CreateOtherDepartmentCallBDRequest" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Hidden Fields -->
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <input type="hidden" name="sales_cid" value="<?=$cmpid_id;?>">
                      <input type="hidden" name="bd_request_type_id" value="<?=$request_id;?>">
                      <input type="hidden" name="handover_id" value="<?=$handover_id;?>">
                      <div class="form-section">
                        <h4 class="section-title mb-4">Client Information</h4>
                        <hr>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="cname" id="cname" required>
                                <option value="<?=$compname;?>"><?=$compname;?></option>
                              </select>
                              <label for="cname">Client Name</label>
                              <div class="invalid-feedback">Please select a client</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="ctype" id="ctype" required>
                                <option value ="<?=$client_idName; ?>" selected ><?= $client_idName; ?></option>
                              </select>
                              <label for="ctype">Client Type</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="request_type" id="task_type" required >
                                <option value="<?= $request_typeName ?>">
                                  <?=$request_typeName?>
                                </option>
                              </select>
                              <label for="task_type">Request Type</label>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <h4 class="section-title mb-4">Project Information</h4>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="pcode" id="pcode" required>
                              </select>
                              <label>Select Project Code</label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                              <select class="form-control animated-input" name="sid" id="sid" required>
                              </select>
                              <label>Select a School for <?= $request_typeName ?></label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <?php $minDate = date('Y-m-d', strtotime('+2 days')); ?>
                            <input type="date" class="form-control animated-input" name="cm_call_or_visit_date" id="inauguration_date" required min="<?= $minDate ?>">

                              <label><?= $request_typeName ?> Date </label>
                            </div>
                          </div>
                        <div class="col-md-12 form-group floating-label">
                            <div class="form-group floating-label">
                            <select class="form-control animated-input" name="cm_call_or_visit" id="cm_call_or_visit" required>
                              <option value="">Select</option>
                              <option value="Only Call">Only Call</option>
                              <option value="Only Visit">Only Visit</option>
                              <option value="Call And Visit">Call And Visit</option>
                              </select>
                              <label>Select <?= $request_typeName ?></label>
                            </div>
                          </div>
                          <hr>
                            <h4 class="section-title mb-4">Request Target Date & Remarks</h4>
                    
                          <div class="form-group floating-label">
                            <?php
                              $minDate = date('Y-m-d', strtotime('+1 days')); // Next 2 days
                              $maxDate = date('Y-m-d', strtotime('+10 days')); // Next 10 days
                              ?>
                            <input type="date" class="form-control animated-input" name="targetd" id="inauguration_target_date" required>
                            <label>Complete Target Date</label>
                          </div>
                          <div class="form-group floating-label">
                            <textarea class="form-control animated-input" name="remark" id="remark" required></textarea>
                            <label>Request Detail</label>
                          </div>
                         <!-- Submit Section -->
                         <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn" id="InaugurationSubitBtn">
                            <span class="submit-text">Submit <?= $request_typeName ?> Request</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                      </div>
                    </form>
                <?php } ?>
              <!-- Closed Other Department Call BD Request Form -->



                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script>
          $( document ).ready(function() {
          

            $("#add_new_location").hide();
            $("#location-single-container").hide();
            $("#location-multiple-container").hide();



            // Start School Identification Request
          
            $('#single_or_multi_location').change(function () {
                  var single_multiple_value = $(this).val();
                  if (single_multiple_value === "single") {
                    $("#add_new_location").hide();
                    $("#location-single-container").show();
                    $("#location-multiple-container").hide();
                  } else if (single_multiple_value === "multiple") {
                    $("#add_new_location").show();
                    $("#location-single-container").hide();
                    $("#location-multiple-container").show();
                  } else {
                    $("#add_new_location").hide();
                    $("#location-single-container").hide();
                    $("#location-multiple-container").hide();
                  }
              });
          
          // Add dynamic location fields
          document.addEventListener('click', function(e) {
              if(e.target.classList.contains('add_location')) {
                  const newGroup = document.createElement('div');
                  newGroup.className = 'location-group';
                  newGroup.innerHTML = `
                     
                      <div class="row g-3 location-group">
                              <div class="col-md-5">
                                  <div class="form-group floating-label">
                                      <input type="text" class="form-control animated-input" name="multiple_location_name[]">
                                      <label for="noschool">Location</label>
                                  </div>
                              </div>
                              <div class="col-md-5">
                                  <div class="form-group floating-label">
                                      <input type="text" class="form-control animated-input" name="multiple_num_schools[]">
                                      <label for="noschool">Number of Schools</label>
                                  </div>
                              </div>
                              <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-location">−</button>
                              </div>
                          </div>
                  `;
                  document.getElementById('location-multiple-container').appendChild(newGroup);
              }
              
              if(e.target.classList.contains('remove-location')) {
                  e.target.closest('.location-group').remove();
              }
          });
          
          // Form validation
          (function () {
              'use strict';
              var forms = document.querySelectorAll('.needs-validation');
              Array.prototype.slice.call(forms).forEach(function (form) {
                  form.addEventListener('submit', function (event) {
                      if (!form.checkValidity()) {
                          event.preventDefault();
                          event.stopPropagation();
                      } else {
                          // Form is valid, disable button and show loading state
                          const btn = form.querySelector('.submit-btn');
                          if (btn) {
                              btn.disabled = true;
                              btn.querySelector('.submit-text').textContent = 'Processing...';
                              btn.querySelector('.spinner-border').classList.remove('d-none');
                          }
                      }
                      form.classList.add('was-validated');
                  }, false);
              });
          })();
          


          $("#attachement_ngo_letter").on("change", function (event) {
        var file = event.target.files[0];
        var previewDiv = $("#attachement_ngo_letter_preview");

        // Clear previous preview
        previewDiv.html("");

        if (file) {
            var fileType = file.type;

            if (fileType === "application/pdf") {
                var fileURL = URL.createObjectURL(file);
                previewDiv.html('<iframe src="' + fileURL + '" width="100%" height="200px" style="border:1px solid #ccc;"></iframe>');
            } else {
                alert("Please upload a valid PDF file.");
                $(this).val(""); // Clear the input
            }
        }
    });
    $("#school_request_draft").on("change", function (event) {
        var school_request_draft_file = event.target.files[0];
        var school_request_draft_previewDiv = $("#school_request_draft_preview");

        // Clear previous preview
        school_request_draft_previewDiv.html("");

        if (school_request_draft_file) {
            var school_request_draft_fileType = school_request_draft_file.type;

            if (school_request_draft_fileType === "application/pdf") {
                var school_request_draft_fileURL = URL.createObjectURL(school_request_draft_file);
                school_request_draft_previewDiv.html('<iframe src="' + school_request_draft_fileURL + '" width="100%" height="200px" style="border:1px solid #ccc;"></iframe>');
            } else {
                alert("Please upload a valid PDF file.");
                $(this).val(""); // Clear the input
            }
        }
    });

    $("#dm_deo_draft").on("change", function (event) {
        var dm_deo_draft_file = event.target.files[0];
        var dm_deo_draft_previewDiv = $("#dm_deo_draft_preview");

        // Clear previous preview
        dm_deo_draft_previewDiv.html("");

        if (dm_deo_draft_file) {
            var dm_deo_draft_fileType = dm_deo_draft_file.type;

            if (dm_deo_draft_fileType === "application/pdf") {
                var dm_deo_draft_fileURL = URL.createObjectURL(dm_deo_draft_file);
                dm_deo_draft_previewDiv.html('<iframe src="' + dm_deo_draft_fileURL + '" width="100%" height="200px" style="border:1px solid #ccc;"></iframe>');
            } else {
                alert("Please upload a valid PDF file.");
                $(this).val(""); // Clear the input
            }
        }
    });



    $("#school_letter_remarks_box").fadeOut();
            $("#dm_deo_letter_remarks_box").fadeOut();
          
            $("#sletter").change(function() {
              var sletterValue = $(this).val(); 
              if(sletterValue == 'Yes'){
                $("#school_letter_remarks_box").fadeIn();
                $("#school_request_draft").prop("required", true);
                $("#school_letter_remarks").prop("required", true);
              }else if(sletterValue == 'No'){
                $("#school_letter_remarks_box").fadeOut();
                $("#school_request_draft").prop("required", false);
                $("#school_letter_remarks").prop("required", false);
              }else if(sletterValue == ''){
                $("#school_letter_remarks_box").fadeOut();
                $("#school_request_draft").prop("required", false);
                $("#school_letter_remarks").prop("required", false);
              }
            });
          
            $("#dmletter").change(function() {
              var dmletterValue = $(this).val(); 
              if(dmletterValue == 'Yes'){
                $("#dm_deo_letter_remarks_box").fadeIn();
                $("#dm_deo_draft").prop("required", true);
                $("#dm_deo_letter_remarks").prop("required", true);
              }else if(dmletterValue == 'No'){
                $("#dm_deo_letter_remarks_box").fadeOut();
                $("#dm_deo_draft").prop("required", false);
                $("#dm_deo_letter_remarks").prop("required", false);
              }else if(dmletterValue == ''){
                $("#dm_deo_letter_remarks_box").fadeOut();
                $("#dm_deo_draft").prop("required", false);
                $("#dm_deo_letter_remarks").prop("required", false);
              }
            });


            $("#schoolIdentificationBtn").click(function (e) {

                var single_or_multi_location  = $("#single_or_multi_location").val();

                let totalSchools = 0;
                if(single_or_multi_location !== ''){
                  if(single_or_multi_location == 'single'){
                  $("input[name='single_num_schools[]']").each(function () {
                    let val = parseInt($(this).val()) || 0;
                    totalSchools += val;
                });
                }
                if(single_or_multi_location == 'multiple'){
                  $("input[name='multiple_num_schools[]']").each(function () {
                    let val = parseInt($(this).val()) || 0;
                    totalSchools += val;
                });
                }

                let noSchool = parseInt($("#noschool").val()) || 0;
                  if(noSchool == ''){
                    alert("Please Add Number Of School");
                  }else{
                      if (totalSchools == noSchool) {
                        return true;
                      } else {
                          alert("The total number of schools must be equal to the given 'noschool' value for the selected locations!");
                          return false;
                      }
                  }
                }
            });




        // School Identification Request Closed

      
        // Start Inauguration Request

        


                  $.ajax({
                    url:'<?=base_url();?>Menu/GetProjectCode',
                    type: "POST",
                    data: {
                    cname: '<?=$compname;?>',
                    salse_cid: '<?=$cmpid_id;?>',
                    },
                    cache: false,
                    success: function b(result){
                    // $("#pcode").html(result);
                    // $("#OnBoardClientSchoolVisitPcode").html(result);
                    $("#pcode").html(result);
                    // $("#employee_engagementPcode").html(result);
                    // $("#school_for_maintenance_Pcode").html(result);
                    // $("#school_for_rttp_pcode").html(result);
                    // $("#school_for_diy_Pcode").html(result);
                    // $("#school_for_MnE_pcode").html(result);
          
                    var pcode = document.getElementById("pcode").value;
                   
                      $.ajax({
                      url:'<?=base_url();?>Menu/getcinfo',
                      type: "POST",
                      data: {
                      pcode: pcode
                      },
                      cache: false,
                      success: function b(result){
                      $("#cinfo").html(result);
                      }
                      });

                      var pcode = $("pcode").val();
                      $.ajax({
                      url:'<?=base_url();?>Menu/getSchoolinfo',
                      type: "POST",
                      data: {
                      pcode: pcode,
                      request_id: <?= $request_id ?>
                      },
                      cache: false,
                      success: function b(result){
                        $("#sid").html(result);
                      // $("#OnBoardClientSchoolVisitSchool").html(result);
                      // $("#employee_engagement_sid").html(result);
                      // $("#school_for_maintenance_sid").html(result);
                      // $("#school_for_rttp_sid").html(result);
                      // $("#school_for_diy_sid").html(result);
                      // $("#school_for_MnE_sid").html(result);
                      }
                      });

                    
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolCountInUsingPcode',
                          type: "POST",
                          data: { pcode: pcode },
                          cache: false,
                          success: function (result) {   
                            if(result !== 0){
                              $("#noofschool_onboard_client_visit").attr("max", result);
                              $("#noofschool_onboard_client_visit_messgae").text("Total "+result+" School Present in  Project Code");
                            }
                          }
                      });
                 
          
                  
                   
          
          
                    }
                    });

                    $("#inauguration_target_date").on("change", function () {
                        var  inauguration_date = $("#inauguration_date").val();
                        if(inauguration_date == ''){
                          $("#inauguration_target_date").val('');
                          alert("Select First inauguration date");
                        }
                    });

                    $("#inauguration_date").on("change", function () {
                        let inaugurationDate = new Date($(this).val());
                        if (!isNaN(inaugurationDate.getTime())) {
                            // Subtract 2 days
                            inaugurationDate.setDate(inaugurationDate.getDate() - 1);
                            // Format as YYYY-MM-DD
                            let formattedDate = inaugurationDate.toISOString().split("T")[0];
                            // console.log("formattedDate" + formattedDate);
                            // Set max attribute for inauguration_target_date
                            $("#inauguration_target_date").attr("max", formattedDate);
                        }
                    });


        // Closed Inauguration Request



        $('#pcode').on('change', function () {
          var pcode = $('#pcode').val(); // Use jQuery for consistency
  
                $.ajax({
                    url: '<?= base_url(); ?>Menu/getSchoolinfo',
                    type: "POST",
                    data: { pcode: pcode,request_id: <?= $request_id ?> },
                    cache: false,
                    success: function (result) {
                        $("#sid").html(result);
                        var totalOptions = $("#sid option").length;
                        console.log('totalOptions' + totalOptions);
                    }
                });
                
                $.ajax({
                    url: '<?= base_url(); ?>Menu/getSchoolCountInUsingPcode',
                    type: "POST",
                    data: { pcode: pcode },
                    cache: false,
                    success: function (result) {   
                      if(result !== 0){
                              $("#noofschool_onboard_client_visit").attr("max", result);
                              $("#noofschool_onboard_client_visit_messgae").text("Total "+result+" School Present in  Project Code");
                            }
                    }
                });
            });
          

            
            $("#get_state").change(function() {
              $("#get_district").html('');
              var get_state = $(this).val();
              $.ajax({
                url: '<?=base_url();?>Menu/GetDistrictINState',
                type: "POST",
                data: {
                  userslsctstate: get_state
                },
                cache: false,
                success: function b(result) {
                  console.log(result);
                  $("#get_district").html(result);
                }
              });
            });
            $("#get_district").change(function() {
              $("#get_city").html('');
              var get_district = $(this).val();
              $.ajax({
                url: '<?=base_url();?>Menu/GetCityInDistrict',
                type: "POST",
                data: {
                  selectdistrict: get_district
                },
                cache: false,
                success: function b(result) {
                  console.log(result);
                  $("#get_city").html(result);
                }
              });
            });











          });
        </script>






        <script type='text/javascript'>
          $( document ).ready(function() {
          
  
            // $("#handoverlog").fadeOut();
            // $("#type_Of_employee_engagement_spd").hide();
          
          
            //   $("#verify_handover_request").change(function() {
            //       var selectedValue = $(this).val();  // Get selected option value
                
            //       if (selectedValue) {
            //         $("#request_imagecard").fadeOut();
            //         $("#handoverlog").fadeIn();
            //           $.ajax({
            //               url: "<?= base_url('Menu/get_handover_details') ?>", // Replace with your controller function
            //               type: "POST",
            //               data: { handover_id: selectedValue },
            //               dataType: "json",
            //               success: function(response) {
            //                 if(response == 'no data found'){
            //                   console.log(response);
            //                 }else{
            //                   $("#handoverlog").html("");
            //                   $.each(response, function (index, item) {
            //                   var html = `
            //                       <div class="entry">
            //                           <h3>School: ${item.sname}</h3>
            //                           <p><strong>Client:</strong> ${item.clientname}</p>
            //                           <p><strong>Address:</strong> ${item.saddress}, ${item.sdistrict}, ${item.tehshil}, ${item.sstate}</p>
            //                           <p><strong>Year:</strong> ${item.sayear}</p>
            //                           <p><strong>Created At:</strong> ${item.created_at}</p>
            //                           <hr>
            //                       </div>
            //                   `;
            //                   $("#handoverlog").append(html);
            //               });
            //                 }
            //               }
            //           });
          
            //           $.ajax({
            //               url: "<?= base_url('Menu/get_handover_details_New') ?>", // Replace with your controller function
            //               type: "POST",
            //               data: { handover_id: selectedValue },
            //               dataType: "json",
            //               success: function(response) {
            //                   var noofschool = response[0].noofschool; // Access the first object
            //                   $("#noschool").val(noofschool).prop("readonly", true);
            //                   // console.log('noofschool', noofschool);
          
            //               }
            //           });
          
          
          
            //       }else{
            //         $("#handoverlog").fadeOut();
            //         $("#request_imagecard").fadeIn();
            //       }
            //   });
          
          
           
              var ctype = document.getElementById("ctype").value;
            
                  var ab = $('#task_type').val();
                        console.log(ab);
                       if(ab=="On Board Client School Visit"){
                        $("#test4").show();
                        $("div[id^='test']:not(#test4) :input").removeAttr("name"); 
                       }else{
                        $("#test4").hide();
                       }
                       if(ab == "Inauguration"){
                        $("#test5").show();
                        $("div[id^='test']:not(#test5) :input").removeAttr("name"); 
                       }else{
                        $("#test5").hide();
                       }
                       if(ab == 'Client Report'){
                        $("#test1").show();
                        $("div[id^='test']:not(#test1) :input").removeAttr("name"); 
                       }else{
                        $("#test1").hide();
                       }
               
                      
                       if(ab == "OnBoardVisit"){
                        $("#test3").show();
                        $("div[id^='test']:not(#test3) :input").removeAttr("name"); 
                       }else{
                        $("#test3").hide();
                       }
                       if(ab == "Demo" || ab == "Online Demo" || ab == "Offline Demo"){
                        $("#test2").show();
                        $("div[id^='test']:not(#test2) :input").removeAttr("name"); 
                       }else{
                        $("#test2").hide();
                       }
                      
                       if(ab == "School Identification"){
                        $("#test6").show();
                        $("div[id^='test']:not(#test6) :input").removeAttr("name"); 
                       }else{
                        $("#test6").hide();
                       }
                       if(ab == "Employee Engagement"){
          
                        // $("#RequestDetail").hide();
                        $("#test7").show();
                        $("div[id^='test']:not(#test7) :input").removeAttr("name"); 
                        $("#type_Of_employee_engagement").change(function () {
                          var selectedValue_employee_engagement = $(this).val();
                          if(selectedValue_employee_engagement == 'Physical'){
                            $("#type_Of_employee_engagement_spd").show();
                          }else{
                            $("#type_Of_employee_engagement_spd").hide();
                          }
                      });
                       }else{
                        $("#test7").hide();
                       }
          
                       if(ab == 'School Maintenance'){
                        $("#test8").show();
                        // $("#RequestDetail").hide();
                        $("div[id^='test']:not(#test8) :input").removeAttr("name"); 
                        $("#school_for_maintenance_sid").html('');
                       }else{
                        $("#test8").hide();
                       }
                       if(ab == 'RTTP'){
                        $("#test9").show();
                        $("div[id^='test']:not(#test9) :input").removeAttr("name"); 
                       }else{
                        $("#test9").hide();
                       }
                       if(ab == 'DIY'){
                        $("#test10").show();
                        $("div[id^='test']:not(#test10) :input").removeAttr("name"); 
                       }else{
                        $("#test10").hide();
                       }
                       if(ab == 'MnE'){
                        $("#test11").show();
                        $("div[id^='test']:not(#test11) :input").removeAttr("name"); 
                       }else{
                        $("#test11").hide();
                       }
                       if(ab == 'New Client Report'){
                        $("#test12").show();
                        $("div[id^='test']:not(#test12) :input").removeAttr("name"); 
                       }else{
                        $("#test12").hide();
                       }
          
                       if(ab == "New client school visit"){
                        $("#test13").show();
                        $("div[id^='test']:not(#test13) :input").removeAttr("name"); 
                       }else{
                        $("#test13").hide();
                       }
                    
                    var cname = document.getElementById("cname").value;
                    $.ajax({
                    url:'<?=base_url();?>Menu/getSPDDataBYCName',
                    type: "POST",
                    data: {
                    cname: cname,
                    cid: <?= $cmpid_id;?>
                    },
                    cache: false,
                    success: function b(result){
                    $("#inauguration_sid").html(result);
                    }
                    });
                    
                    $.ajax({
                    url:'<?=base_url();?>Menu/getpcode',
                    type: "POST",
                    data: {
                    cname: cname,
                    cid: <?= $cmpid_id;?>
                    },
                    cache: false,
                    success: function b(result){
          
                    $("#pcode").html(result);
                    $("#OnBoardClientSchoolVisitPcode").html(result);
                    $("#inaugurationPcode").html(result);
                    $("#employee_engagementPcode").html(result);
                    $("#school_for_maintenance_Pcode").html(result);
                    $("#school_for_rttp_pcode").html(result);
                    $("#school_for_diy_Pcode").html(result);
                    $("#school_for_MnE_pcode").html(result);
          
                    var pcode = document.getElementById("pcode").value;
                   
                      $.ajax({
                      url:'<?=base_url();?>Menu/getcinfo',
                      type: "POST",
                      data: {
                      pcode: pcode
                      },
                      cache: false,
                      success: function b(result){
                      $("#cinfo").html(result);
                      }
                      });
                      
                      $.ajax({
                      url:'<?=base_url();?>Menu/getSchoolinfo',
                      type: "POST",
                      data: {
                      pcode: pcode,
                      request_id: <?= $request_id ?>
                      },
                      cache: false,
                      success: function b(result){
                      $("#OnBoardClientSchoolVisitSchool").html(result);
                      $("#inaugurationSID").html(result);
                      $("#employee_engagement_sid").html(result);
                      $("#school_for_maintenance_sid").html(result);
                      $("#school_for_rttp_sid").html(result);
                      $("#school_for_diy_sid").html(result);
                      $("#school_for_MnE_sid").html(result);
                      }
                      });
                 
          
                  
                   
          
          
                    }
                    });
          
                
          
          
                 
                   
                   
                    
          
                    
          });
                  //   $('#ctype').on('change', function c() {
                  //   var ctype = document.getElementById("ctype").value;
                  //   $.ajax({
                  //   url:'<?=base_url();?>Menu/getctot',
                  //   type: "POST",
                  //   data: {
                  //   ctype: ctype
                  //   },
                  //   cache: false,
                  //   success: function a(result){
                  //   $("#task_type").html(result);
                  //   }
                  //   });
                  //   });
                    
                    
                    
                    $('#pcode').on('change', function a() {
                    var pcode = document.getElementById("pcode").value;
                    $.ajax({
                    url:'<?=base_url();?>Menu/getcinfo',
                    type: "POST",
                    data: {
                    pcode: pcode
                    },
                    cache: false,
                    success: function b(result){
                    $("#cinfo").html(result);
                    }
                    });
                    });
          
                    $('#OnBoardClientSchoolVisitPcode').on('change', function () {
                      let OnBoardClientSchoolVisitPcode = $('#OnBoardClientSchoolVisitPcode').val(); // Use jQuery for consistency
                       
                      var pcode = document.getElementById("pcode").value;
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolinfo',
                          type: "POST",
                          data: { pcode: OnBoardClientSchoolVisitPcode,request_id: <?= $request_id ?> },
                          cache: false,
                          success: function (result) {
                              $("#OnBoardClientSchoolVisitSchool").html(result);
                          }
                      });
                  });
          
                    $('#inaugurationPcode').on('change', function () {
                      let inaugurationPcode = $('#inaugurationPcode').val(); // Use jQuery for consistency
                      
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolinfo',
                          type: "POST",
                          data: { pcode: inaugurationPcode,request_id: <?= $request_id ?> },
                          cache: false,
                          success: function (result) {
                              $("#inaugurationSID").html(result);
                          }
                      });
                  });
                    $('#employee_engagementPcode').on('change', function () {
                     
                      let employee_engagementPcode = $('#employee_engagementPcode').val(); // Use jQuery for consistency
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolinfo',
                          type: "POST",
                          data: { pcode: employee_engagementPcode,request_id: <?= $request_id ?> },
                          cache: false,
                          success: function (result) {
                              $("#employee_engagement_sid").html(result);
                          }
                      });
                  });
                    $('#school_for_maintenance_Pcode').on('change', function () {
                       
                      let school_for_maintenance_Pcode = $('#school_for_maintenance_Pcode').val(); // Use jQuery for consistency
                     
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolinfo',
                          type: "POST",
                          data: { pcode: school_for_maintenance_Pcode,request_id: <?= $request_id ?> },
                          cache: false,
                          success: function (result) {
                              $("#school_for_maintenance_sid").html(result);
                              $("#school_for_rttp_sid").html(result);
                          }
                      });
                  });
                    $('#school_for_rttp_pcode').on('change', function () {
                     
                      let school_for_rttp_pcode = $('#school_for_rttp_pcode').val(); 
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolinfo',
                          type: "POST",
                          data: { pcode: school_for_rttp_pcode,request_id: <?= $request_id ?> },
                          cache: false,
                          success: function (result) {
                              $("#school_for_rttp_sid").html(result);
                          }
                      });
                  });
                    $('#school_for_diy_Pcode').on('change', function () {
                    
                      let school_for_diy_Pcode = $('#school_for_diy_Pcode').val(); 
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolinfo',
                          type: "POST",
                          data: { pcode: school_for_diy_Pcode,request_id: <?= $request_id ?> },
                          cache: false,
                          success: function (result) {
                              $("#school_for_diy_sid").html(result);
                          }
                      });
                  });
                    $('#school_for_MnE_pcode').on('change', function () {
                        
                      let school_for_MnE_pcode = $('#school_for_MnE_pcode').val(); 
                      $.ajax({
                          url: '<?= base_url(); ?>Menu/getSchoolinfo',
                          type: "POST",
                          data: { pcode: school_for_MnE_pcode,request_id: <?= $request_id ?> },
                          cache: false,
                          success: function (result) {
                              $("#school_for_MnE_sid").html(result);
                          }
                      });
                  });
          
                    
                    
                    
                    $('#cname').on('change', function a() {
                    var cname = document.getElementById("cname").value;
                    $.ajax({
                    url:'<?=base_url();?>Menu/getpcode',
                    type: "POST",
                    data: {
                    cname: cname,
                    cid: <?= $cmpid_id;?>
                    },
                    cache: false,
                    success: function b(result){
                    $("#pcode").html(result);
                    }
                    });
                    });
                    
               
                  
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