<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Handeover To Operations | STEM APP</title>
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
    <!-- Add this in the <head> or before closing </body> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
      .scrollme {overflow-x: auto;}.card{box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;}
      .select2-container--default .select2-selection--multiple .select2-selection__choice {
      color: black!important;
      }
      img.client_logo.img-fluide {
      box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
      }
      .form-check {
    font-weight: 700;
    font-size: 20px;
    letter-spacing: 2px;
}
.call_visit_required{
  background: azure;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <?php // require('addlog.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
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
            <?php
              if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <div class="row mb-2">
              <div class="col-sm-12 text-center bg-primary p-2">
                <h3>Handover to Operations</h3>
              </div>
            </div>
          </div>
        </div>
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <form action="<?=base_url();?>Menu/bdHandover_New" id="handover_new_form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="req_taskid" value="<?=$req_taskid ?>">
                    <input type="hidden" name="company_id" value="<?=$cid ?>">
                    <div class="card-body">
                      <div class="was-validated">
                        <div class="form-row">
                          <div class="col-lg-12 p-3 card" style="background: antiquewhite;">
                            <div class="row">
                              <div class="col">
                                <label for="validationSample01">Client Name</label>
                                <input type="hidden" class="form-control" name="bdid" value="<?=$uid?>" required readonly>
                                <input type="text" class="form-control" name="client_name" id="client_name" placeholder="Client Name" value="<?=$md->compname?>" required readonly>
                                <div class="invalid-feedback">Please provide a Company name.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="validationSample02">NGO / Mediator if any</label>
                                <input type="text" class="form-control" name="mediator" id="mediator" placeholder="NGO / Mediator if any" required="">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label for="noofschool">Number of Schools</label>
                                <input type="text" class="form-control" name="noofschool" id="noofschool" placeholder="Number of Schools" required value="<?=$md->noofschools?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="state">FTTP Type</label>
                                <select class="form-control" name="fttptype">
                                  <option>Individual FTTP</option>
                                  <option>Cluster FTTP</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label for="state">State</label>
                                <input type="text" class="form-control" name="state" id="state" placeholder="State" required value="<?=$md->state?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="city">City</label>
                                <input type="text" class="form-control" name="city" id="city" placeholder="City" required value="<?=$md->city?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label for="location">Location / Village</label>
                                <textarea class="form-control" name="location" id="location" placeholder="Location / Village" required></textarea>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                          </div>
                          <hr>
                          <div class="col-lg-12 card p-3" style="background: #f3f3b5;" >
                            <div class="row">
                              <div class="col">
                                <label>Are the schools to be Identified?</label>
                                <select class="form-control" name="are_spd_identify" id="are_spd_identify" placeholder="Are the schools to be Identified?" required>
                                  <option>Yes</option>
                                  <option>No</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="spd_identify_by">School Identification by</label>
                                <select class="form-control" name="spd_identify_by" id="spd_identify_by" placeholder="School Identification by" onchange="schoolFun()" required>
                                  <option value="">Identification Type</option>
                                  <option>STEM</option>
                                  <option>Client</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label>Identification Process</label>
                                <select class="form-control" name="idprocess" id="idprocess" placeholder="School Identification by" required>
                                  <option value="No Need">No Need</option>
                                  <option value="Post">Identification After Handover</option>
                                  <option value="Pre">Identification Before Handover</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="infrastructure">School Infrastructure for MSC (platform)</label>
                                <input type="text" class="form-control" name="infrastructure" id="infrastructure" placeholder="School Infrastructure for MSC (platform)" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="Contact Person" value="<?=$md->contactperson?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="cp_mno">Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="cp_mno" id="cp_mno" placeholder="Contact Person Mobile No" value="<?=$md->phoneno?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label for="acontact_person">Alternate Contact Person</label>
                                <input type="text" class="form-control" name="acontact_person" id="acontact_person" placeholder="Alternate Contact Person" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="acp_mno">Alternate Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="acp_mno" id="acp_mno" placeholder="Alternate Contact Person Mobile No" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row"></div>
                          </div>
                          <div class="col-lg-12 card p-3" style="background: aliceblue;">
                            <div class="row">
                              <div class="col">
                                <label for="language">Language on backdrop required by client for MSC (Multiple Selection)</label>
                                <select class="form-control select2" name="language[]" id="language" multiple="multiple" required>
                                  <?php foreach($languages as $language): ?>
                                  <option value="<?=$language->language_name?>"><?=$language->language_name.' ( '.$language->native_name.' )'?></option>
                                  <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="expected_installation_date">Expected Installation Date by Client/Sales Team</label>
                                <input type="date" class="form-control" name="expected_installation_date" id="expected_installation_date" placeholder="Expected Installation Date by Client/Sales Team" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label for="project_tenure">Project Tenure (Year)</label>
                                <input type="number" class="form-control" name="project_tenure" id="project_tenure" placeholder="Project Tenure" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="project_type">Project Type</label>
                                <select id="projectt" onchange="prot()" class="form-control" required>
                                  <option value="">Select Project Type</option>
                                  <option>MSC</option>
                                  <option>Other</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="project_type">Project Type</label>
                                <input type="text" class="form-control" name="project_type" id="project_type" placeholder="Project Type" required readonly>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <label for="comments">Special Requirements / Comments :</label>
                                <textarea class="form-control" name="comments" id="comments" placeholder="Special Requirements / Comments" required></textarea>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <label for="comments">Artwork Inputs :</label>
                                <textarea class="form-control" name="remark" id="remark" placeholder="Artwork Inputs" required></textarea>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            </div>
                            <br>
                            <div class="row">
                              <div class="col">
                                <label for="filname">Client logo (Attech Multiple Logos)</label>
                                <input type="file" class="form-control-file" id="filname" name="filname[]" required multiple>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                <div id="preview"></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="spdNewData"></div>
                    <div class="text-right p-2" id="stem_select">
                      <button type="button" id="addSchool" class="btn btn-success">+</button>
                      <button type="button" id="removeSchool" class="btn btn-danger">-</button>
                    </div>
                    <div class="clientSchool" id="clientSchool">
                      <?php 
                      $spd_verifys = $this->Menu_model->GetRequestSchoolDataonHandoverTime($cid);
                        $spd_verifys_cnt = sizeof($spd_verifys);
                        if($spd_verifys_cnt > 0){
                        $sd = 1;
                        foreach($spd_verifys as $spd_verify){ 
                          ?>
                      <div class="col-md-12 card p-2 mb-3" style="background:rgb(119, 215, 219);">
                        <div class="text-center">
                          <h4 class="pt-4">School Details - <?=$sd?> </h4>
                        </div>
                        <div class="was-validated">
                       
                          <div class="row">
                              <div class="col">
                              <label for="snumber${i}">Select This In Handover </label>
                              <select name="in_this_handover[]" class="form-control">
                                <option value="">Select</option>
                                <option value="<?=$spd_verify->id;?>">yes</option>
                                <option value="no">no</option>
                              </select>
                              <div class="invalid-feedback">Please provide a Information.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col">
                              <input type="hidden" name="sid[]" class="form-control" value="<?=$spd_verify->id;?>" id="sid${i}" placeholder="School ID" required="">
                              <label for="sname${i}">School Name : </label>
                              <input type="text" name="sname[]" class="form-control" value="<?=$spd_verify->sname;?>" id="sname${i}" placeholder="School Name" required="">
                              <div class="invalid-feedback">Please provide a Information.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col">
                              <label for="sstate${i}">School State : </label>
                              <input type="text" name="sstate[]" class="form-control" value="<?=$spd_verify->sstate;?>" id="sstate${i}" placeholder="School State" required="">
                              <div class="invalid-feedback">Please provide a Information.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col">
                              <label for="sdistrict${i}">School District : </label>
                              <input type="text" name="sdistrict[]" class="form-control" value="<?=$spd_verify->sdistrict;?>" id="sdistrict${i}" placeholder="School District" required="">
                              <div class="invalid-feedback">Please provide a district.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col">
                              <label for="scity${i}">School City : </label>
                              <input type="text" name="scity[]" class="form-control" value="<?=$spd_verify->scity;?>" id="scity${i}" placeholder="School City" required="">
                              <div class="invalid-feedback">Please provide a city.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col">
                              <label for="saddress${i}">School Address : </label>
                              <textarea class="form-control" name="saddress[]" id="saddress${i}" placeholder="School Address" required=""><?=$spd_verify->saddress;?></textarea>
                              <div class="invalid-feedback">Please provide a Information.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col">
                              <label for="scontact${i}">School Contact Person : </label>
                              <input type="text" name="scontact[]" class="form-control" id="scontact${i}" placeholder="School Contact Person">
                              <div class="invalid-feedback">Please provide a Information.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col">
                              <label for="sdegignation${i}">Designation : </label>
                              <input type="text" name="sdegignation[]" class="form-control" id="sdegignation${i}" placeholder="Designation">
                              <div class="invalid-feedback">Please provide a Information.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col">
                              <label for="snumber${i}">Phone Number : </label>
                              <input type="number" name="snumber[]" class="form-control" id="snumber${i}" placeholder="Contact No">
                              <div class="invalid-feedback">Please provide a Information.</div>
                              <div class="valid-feedback">Looks good!</div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <?php $sd++; }}  ?>
                    </div>
                    <div class="p-3" id="sdetail">
                      <div class="col-md-12 card p-4" style="background: cadetblue;">
                        <div id="scollcnt"></div>
                      </div>
                    </div>
                </div>
                <div class="col-md-12 mb-4">
                <div class="confirmarea">
                <div class="card p-3" style="background: burlywood;">
                <div class="card p-5 col-12" style="background: cornsilk;"><b><input id="allcheck" class="form-check-input" type="checkbox" onchange="toggleButton()"> Thank you for completing the form. Before you submit, please take a moment to review your responses for accuracy and completeness. Once you click the 'Submit' button, your form will be processed, and we will consider the information provided as final.</b></div>
                </div>
                </div>
                <div class="card-footer text-center">
                <button type="submit" id="btn" class="btn btn-primary" style="width:150px;">Submit</button>
                </div>
                </div>
                </form>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
      $(document).ready(function(){
          let count = 0; // Counter to track the number of added blocks
      
          // Add school details block
          $("#addSchool").click(function(){
              count++;
              let schoolBlock = `
                  <div class="col-md-12 card p-2 mb-3 school-block" id="schoolBlock${count}" style="background: #f3f3b5;">
                      <div class="text-center"><h4 class="pt-4">New School Details - ${count}</h4></div>
                      <div class="was-validated">
                          <div class="row">
                              <div class="col">
                                  <label for="sname_new${count}">School Name : </label>
                                  <input type="text" name="sname_new[]" class="form-control" id="sname_new${count}" placeholder="School Name" required>
                                  <div class="invalid-feedback">Please provide a name.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col">
                                  <label for="sstate_new${count}">School State : </label>
                                  <select class="form-control" name="sstate_new[]" id="sstate_new${count}" required>
                                      <option value="">Select State</option>
                                      <?php foreach($allStates as $allState): ?>
                                          <option value="<?=$allState->state_id?>"><?=$allState->state_title?></option>
                                      <?php endforeach; ?>
                                  </select>
                                  <div class="invalid-feedback">Please provide a state.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                  <label for="sdistrict_new${count}">School District : </label>
                                  <select class="form-control" name="sdistrict_new[]" id="sdistrict_new${count}" required></select>
                                  <div class="invalid-feedback">Please provide a district.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                  <label for="scity_new${count}">School City : </label>
                                  <select class="form-control" name="scity_new[]" id="scity_new${count}" required></select>
                                  <div class="invalid-feedback">Please provide a city.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col">
                                  <label for="saddress_new${count}">School Address : </label>
                                  <textarea class="form-control" name="saddress_new[]" id="saddress_new${count}" placeholder="School Address" required></textarea>
                                  <div class="invalid-feedback">Please provide an address.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col">
                                  <label for="scontact_new${count}">School Contact Person : </label>
                                  <input type="text" name="scontact_new[]" class="form-control" id="scontact_new${count}" placeholder="Contact Person" required>
                                  <div class="invalid-feedback">Please provide a contact person.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                  <label for="sdegignation_new${count}">Designation : </label>
                                  <input type="text" name="sdegignation_new[]" class="form-control" id="sdegignation_new${count}" placeholder="Designation" required>
                                  <div class="invalid-feedback">Please provide a designation.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="col">
                                  <label for="snumber_new${count}">Phone Number : </label>
                                  <input type="number" name="snumber_new[]" class="form-control" id="snumber_new${count}" placeholder="Phone Number" required>
                                  <div class="invalid-feedback">Please provide a phone number.</div>
                                  <div class="valid-feedback">Looks good!</div>
                              </div>
                          </div>
                          <hr>
                          <div class="row">
                            
                              <div class="col">
                                <div class="p-4 call_visit_required">
                                  <label for="call_visit_required${count}">Select Call / Visit Required : </label>
                                  <select class="form-control" name="call_visit_required[]" id="call_visit_required${count}" required>
                                      <option value="">Select</option>
                                          <option value="Call">Only Call Required</option>
                                          <option value="Visit">Only Visit Required</option>
                                  </select>
                                  <div class="invalid-feedback">Please provide a state.</div>
                                  <div class="valid-feedback">Looks good!</div>
                                </div>
                              </div>
                               
                            
                          </div>
                            <br/>
                      </div>
                  </div>
              `;
      
              $("#spdNewData").append(schoolBlock);
          });
      
          // Remove last added school details block
          $("#removeSchool").click(function(){
              if (count > 0) {
                  $("#schoolBlock" + count).remove();
                  count--;
              }
          });
      });
    </script>
    <!-- jQuery (Load First) -->
    <script type='text/javascript'>
      $(document).ready(function () {
          $('#filname').on('change', function () {
              let files = this.files; // Get selected files
              $('#preview').html(''); // Clear the preview area
      
              if (files.length > 0) {
                  Array.from(files).forEach(file => {
                      if (file.type.startsWith('image/')) { // Check if file is an image
                          let reader = new FileReader();
                          reader.onload = function (e) {
                              // Create an <img> tag with the image data
                              let img = $('<img>').attr('src', e.target.result).css({
                                  width: '220px',
                                  // height: '100px',
                                  margin: '5px',
                                  // border: '1px solid #ccc',
                                  borderRadius: '5px',
                                  padding: '5px',
                              }).addClass('client_logo img-fluide');
                              $('#preview').append(img);
                          };
                          reader.readAsDataURL(file); // Read the file
                      } else {
                          alert('Only image files are allowed!');
                      }
                  });
              }
          });
      
      
      });
      
      
      
      // $(document).ready(function () {
      
        
      // // Listen for changes to the #noofschool input field
      // $("#noofschool").on("input", function () {
      // const numOfSchools = parseInt($(this).val()) || 0; // Get the input value
      // const container = $("#scollcnt"); // Target the container to append the school blocks
      // $("#sdetail").show();
      // container.empty(); // Clear any existing blocks
      // var spd_identify_by = $('#spd_identify_by').val();
      // if(spd_identify_by == 'STEM'){
      // // Dynamically create the required number of blocks
      // for (let i = 1; i <= numOfSchools; i++) {
      //   const schoolBlock = `
      //     <div class="col-md-12 card p-2 mb-3" style="background: #f3f3b5;">
      //      <div class="text-center"><h4 class="pt-4">School Details - ${i} </h4></div>
      //      <div class="was-validated">
      //       <div class="row">
      //         <div class="col">
      //           <label for="sname${i}">School Name : </label>
      //           <input type="text" name="sname[]" class="form-control" id="sname${i}" placeholder="School Name" required="">
      //           <div class="invalid-feedback">Please provide a Information.</div>
      //           <div class="valid-feedback">Looks good!</div>
      //         </div>
      //       </div>
      //       <div class="row">
      //       <div class="col">
      //           <label for="sstate${i}">School State : </label>
      //           <select class="form-control" name="sstate[]"  id="sstate${i}" required>
      //            <option value="">Select State</option>
      //               <?php foreach($allStates as $allState): ?>
      //                 <option value="<?=$allState->state_id?>"><?=$allState->state_title?></option>
      //                 <?php endforeach; ?>
      //             </select>
      //           <div class="invalid-feedback">Please provide a Information.</div>
      //           <div class="valid-feedback">Looks good!</div>
      //         </div>
      //           <div class="col">
      //             <label for="sdistrict${i}">School District : </label>
      //             <select class="form-control" name="sdistrict[]" id="sdistrict${i}" required></select>
      //             <div class="invalid-feedback">Please provide a district.</div>
      //             <div class="valid-feedback">Looks good!</div>
      //         </div>
      //         <div class="col">
      //             <label for="scity${i}">School City : </label>
      //             <select class="form-control" name="scity[]" id="scity${i}" required></select>
      //             <div class="invalid-feedback">Please provide a city.</div>
      //             <div class="valid-feedback">Looks good!</div>
      //         </div>
      //       </div>
      //        <div class="row">
      //         <div class="col">
      //           <label for="saddress${i}">School Address : </label>
      //           <textarea class="form-control" name="saddress[]" id="saddress${i}" placeholder="School Address" required=""></textarea>
      //           <div class="invalid-feedback">Please provide a Information.</div>
      //           <div class="valid-feedback">Looks good!</div>
      //         </div>
      //       </div>
      //       <div class="row">
      //         <div class="col">
      //           <label for="scontact${i}">School Contact Person : </label>
      //           <input type="text" name="scontact[]" class="form-control" id="scontact${i}" placeholder="School Contact Person" required="">
      //           <div class="invalid-feedback">Please provide a Information.</div>
      //           <div class="valid-feedback">Looks good!</div>
      //         </div>
      //         <div class="col">
      //           <label for="sdegignation${i}">Designation : </label>
      //           <input type="text" name="sdegignation[]" class="form-control" id="sdegignation${i}" placeholder="Designation" required="" >
      //           <div class="invalid-feedback">Please provide a Information.</div>
      //           <div class="valid-feedback">Looks good!</div>
      //         </div>
      //         <div class="col">
      //           <label for="snumber${i}">Phone Number : </label>
      //           <input type="number" name="snumber[]" class="form-control" id="snumber${i}" placeholder="Contact No" required="" >
      //           <div class="invalid-feedback">Please provide a Information.</div>
      //           <div class="valid-feedback">Looks good!</div>
      //         </div>
      //       </div>
      //     </div>
      //   </div>
      //   `;
      //   container.append(schoolBlock); // Append the block to the container
      // }
      // }else{
      // $("#sdetail").hide();
      // }
      // });
      // });
      
      
            document.getElementById("sdetail").style.display = "none";
            
            document.getElementById('btn').disabled = true;
            
            function toggleButton() {
                var checkbox = document.getElementById('allcheck');
                if (checkbox.checked) {
                    document.getElementById('btn').disabled = false;
                  }
                else {
                    document.getElementById('btn').disabled = true;
                  }
            }
            
            $('#idprocess').on('change', function b() {
                var idprocess = this.value;
                if(idprocess=='Pre'){
                    document.getElementById('btn').disabled = true;
                }
                if(idprocess=='Post'){
                    document.getElementById('btn').disabled = false;
                }
                if(idprocess=='No Need'){
                    document.getElementById('btn').disabled = false;
                }
                
                
                
            });
            
        
            
            
            
            
            function prot(){
                var x = document.getElementById("projectt").value;
                if(x!='Other'){
                    document.getElementById("project_type").value = x;
                    document.getElementById("project_type").readOnly = true;
                }else{
                    document.getElementById("project_type").value = '';
                    document.getElementById("project_type").readOnly = false;
                }
            }
            
            $('#currentStatus').on('change', function b() {
            var sid = document.getElementById("currentStatus").value;
            $.ajax({
            url:'<?=base_url();?>Menu/getremark',
            type: "POST",
            data: {
            sid: sid
            },
            cache: false,
            success: function a(result){
            $("#remarks").html(result);
            }
            });
            });
            
            $('#action_type').on('change', function b() {
            var aid = document.getElementById("action_type").value;
            var sid = document.getElementById("currentStatus").value;
            $.ajax({
            url:'<?=base_url();?>Menu/getpurpose',
            type: "POST",
            data: {
            aid: aid,
            sid: sid
            },
            cache: false,
            success: function a(result){
            $("#purpose").html(result);
            }
            });
            });
            
            
            $('#purpose').on('change', function b() {
            var pid = document.getElementById("purpose").value;
            $.ajax({
            url:'<?=base_url();?>Menu/getnextaction',
            type: "POST",
            data: {
            pid: pid
            },
            cache: false,
            success: function a(result){
            $("#next_action").html(result);
            }
            });
            });
            
            
            $('#id_state').on('change', function b() {
            var stid = document.getElementById("id_state").value;
            $.ajax({
            url:'<?=base_url();?>Menu/getcity',
            type: "POST",
            data: {
            stid: stid
            },
            cache: false,
            success: function a(result){
            $("#city").html(result);
            }
            });
            });
            function replaceBudget(){                    
                var budgetdiv= document.getElementById('budgetdiv');
                var id_partnerType= document.getElementById('id_partnerType').value;
                if(id_partnerType=="4"){
                budgetdiv.innerHTML='<label for="validationSample01">Category</label><select id="budget" class="form-control" name="budget" required><option>A</option><option>B</option><option>C</option></select>';    
                }
                alert('budget checking '+id_partnerType);
            }
            var id_partnerType=document.getElementById('id_partnerType');
            id_partnerType.addEventListener("change", replaceBudget);
            
            
      
          
    </script>
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
    <script src="<?=base_url();?>assets/js/jquery.knob.min.js"></script>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script type='text/javascript'>
      $(document).ready(function() {
              console.log(typeof $.fn.select2); // Debugging check
              if (typeof $.fn.select2 !== "undefined") {
                  $('#language').select2({
                      placeholder: "Select Languages",
                      allowClear: true
                  });
              } else {
                  console.error("Select2 is not loaded!");
              }
          });
          
    </script>
    <script>
      $(document).ready(function () {
        $("#clientSchool").hide();
      
        $(document).on("change", "select[name='sstate[]']", function () {
                      let stateid = $(this).val();
                      let stateInput = $(this);
                      let state_form_id = $(this).attr('id');  // Get the ID
                      let state_i = state_form_id.replace('sstate', '');
                      $.ajax({
                        url:'<?=base_url();?>Menu/GetDistricONHandoverPage',
                        type: "POST",
                        data: { state: stateid },
                        success: function (response) {
                          $("#sdistrict" + state_i).html();
                          $("#sdistrict" + state_i).html(response);
                        }
                      });
                  });
                  $(document).on("change", "select[name='sdistrict[]']", function () {
                      let sdistrictid = $(this).val();
                      let sdistrictInput = $(this);
                      let sdistrict_form_id = $(this).attr('id');  // Get the ID
                      let sdistrict_i = sdistrict_form_id.replace('sdistrict', '');
                      $.ajax({
                        url:'<?=base_url();?>Menu/GetCityONHandoverPage',
                        type: "POST",
                        data: { sdistrictid: sdistrictid },
                        success: function (response) {
                          $("#scity" + sdistrict_i).html();
                          $("#scity" + sdistrict_i).html(response);
                        }
                      });
                  });
      

                  $(document).on("change", "select[name='sstate_new[]']", function () {
                      let stateid = $(this).val();
                      let stateInput = $(this);
                      let state_form_id = $(this).attr('id');  // Get the ID
                      let state_i = state_form_id.replace('sstate_new', '');
                      $.ajax({
                        url:'<?=base_url();?>Menu/GetDistricONHandoverPage',
                        type: "POST",
                        data: { state: stateid },
                        success: function (response) {
                          $("#sdistrict_new" + state_i).html();
                          $("#sdistrict_new" + state_i).html(response);
                        }
                      });
                  });
                  $(document).on("change", "select[name='sdistrict_new[]']", function () {
                      let sdistrictid = $(this).val();
                      let sdistrictInput = $(this);
                      let sdistrict_form_id = $(this).attr('id');  // Get the ID
                      let sdistrict_i = sdistrict_form_id.replace('sdistrict_new', '');
                      $.ajax({
                        url:'<?=base_url();?>Menu/GetCityONHandoverPage',
                        type: "POST",
                        data: { sdistrictid: sdistrictid },
                        success: function (response) {
                          $("#scity_new" + sdistrict_i).html();
                          $("#scity_new" + sdistrict_i).html(response);
                        }
                      });
                  });
      
      
      
          $('#spd_identify_by').on('change', function () {
              var spd_identify_by = $(this).val();
      
              if(spd_identify_by == 'Client') {
                  var noofschool = $("#noofschool").val();
                  const numOfSchools = parseInt(noofschool) || 0; // Get the input value
                  const container = $("#scollcnt"); // Target the container to append the school blocks
                  
                  $("#sdetail").hide();
                  $("#clientSchool").hide();
      
            
                  // $("#sdetail").show();
                  // container.empty(); // Clear any existing blocks
                  // // Dynamically create the required number of blocks
                  // for (let i = 1; i <= numOfSchools; i++) {
                  //     const schoolBlock = `
                  //         <div class="col-md-12 card p-2 mb-3" id="maicard" style="background: #f3f3b5;">
                  //             <div class="text-center"><h4 class="pt-4">School Details - ${i} </h4></div>
                  //             <div class="was-validated">
                  //                 <div class="row">
                  //                     <div class="col">
                  //                         <label for="sname${i}">School Name : </label>
                  //                         <input type="text" name="sname[]" class="form-control" id="sname${i}" placeholder="School Name" required>
                  //                         <div class="invalid-feedback">Please provide a name.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                 </div>
                  //                 <div class="row">
                  //                     <div class="col">
                  //                         <label for="sstate${i}">School State : </label>
                  //                         <select class="form-control" name="sstate[]" id="sstate${i}" required>
                  //                             <option value="">Select State</option>
                  //                             <?php foreach($allStates as $allState): ?>
                  //                                 <option value="<?=$allState->state_id?>"><?=$allState->state_title?></option>
                  //                             <?php endforeach; ?>
                  //                         </select>
                  //                         <div class="invalid-feedback">Please provide a state.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                     <div class="col">
                  //                         <label for="sdistrict${i}">School District : </label>
                  //                         <select class="form-control" name="sdistrict[]" id="sdistrict${i}" required></select>
                  //                         <div class="invalid-feedback">Please provide a district.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                     <div class="col">
                  //                         <label for="scity${i}">School City : </label>
                  //                         <select class="form-control" name="scity[]" id="scity${i}" required></select>
                  //                         <div class="invalid-feedback">Please provide a city.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                 </div>
                  //                 <div class="row">
                  //                     <div class="col">
                  //                         <label for="saddress${i}">School Address : </label>
                  //                         <textarea class="form-control" name="saddress[]" id="saddress${i}" placeholder="School Address" required></textarea>
                  //                         <div class="invalid-feedback">Please provide an address.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                 </div>
                  //                 <div class="row">
                  //                     <div class="col">
                  //                         <label for="scontact${i}">School Contact Person : </label>
                  //                         <input type="text" name="scontact[]" class="form-control" id="scontact${i}" placeholder="Contact Person" required>
                  //                         <div class="invalid-feedback">Please provide a contact person.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                     <div class="col">
                  //                         <label for="sdegignation${i}">Designation : </label>
                  //                         <input type="text" name="sdegignation[]" class="form-control" id="sdegignation${i}" placeholder="Designation" required>
                  //                         <div class="invalid-feedback">Please provide a designation.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                     <div class="col">
                  //                         <label for="snumber${i}">Phone Number : </label>
                  //                         <input type="number" name="snumber[]" class="form-control" id="snumber${i}" placeholder="Phone Number" required>
                  //                         <div class="invalid-feedback">Please provide a phone number.</div>
                  //                         <div class="valid-feedback">Looks good!</div>
                  //                     </div>
                  //                 </div>
                  //             </div>
                  //         </div>
                  //     `;
                  //     container.append(schoolBlock);
                  // }
      
                  // $(document).on("change", "select[name='sstate[]']", function () {
                  //     let stateid = $(this).val();
                  //     let stateInput = $(this);
                  //     let state_form_id = $(this).attr('id');  // Get the ID
                  //     let state_i = state_form_id.replace('sstate', '');
                  //     $.ajax({
                  //       url:'<?=base_url();?>Menu/GetDistricONHandoverPage',
                  //       type: "POST",
                  //       data: { state: stateid },
                  //       success: function (response) {
                  //         $("#sdistrict" + state_i).html();
                  //         $("#sdistrict" + state_i).html(response);
                  //       }
                  //     });
                  // });
                  // $(document).on("change", "select[name='sdistrict[]']", function () {
                  //     let sdistrictid = $(this).val();
                  //     let sdistrictInput = $(this);
                  //     let sdistrict_form_id = $(this).attr('id');  // Get the ID
                  //     let sdistrict_i = sdistrict_form_id.replace('sdistrict', '');
                  //     $.ajax({
                  //       url:'<?=base_url();?>Menu/GetCityONHandoverPage',
                  //       type: "POST",
                  //       data: { sdistrictid: sdistrictid },
                  //       success: function (response) {
                  //         $("#scity" + sdistrict_i).html();
                  //         $("#scity" + sdistrict_i).html(response);
                  //       }
                  //     });
                  // });
              }else if(spd_identify_by == 'STEM') {
                  // alert("* Please Select School Identification by");
                  $("#sdetail").show();
                  $("#clientSchool").show();
              }
          });
      });
      
      $(document).ready(function() {
        $('#btn').click(function(event) {
            event.preventDefault(); // Prevent form submission if inside a form
            let confirmAction = confirm('Are you sure you want to submit?');
            if (confirmAction) {
              $('#main_school_card').find('input, select, textarea').removeAttr('required');
              let isValid = true;
                  var spd_identify_by = $('#spd_identify_by').val();
                  if(spd_identify_by == 'Client') {
                    $("#handover_new_form").submit(); // Trigger form submission
                  }else if(spd_identify_by == 'STEM'){
                    if (isValid) {
                       $("#handover_new_form").submit(); // Trigger form submission
                      //  alert('* Please fill in all required fields.');
                    }
                  }
                return true;
            } else {
                return false;
            }
        });
      });
      
          
    </script>
  </body>
</html>