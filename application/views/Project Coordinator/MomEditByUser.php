<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>STEM APP |Edit MOM | WebAPP</title>
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
      .content-wrapper>.content {
    background: blanchedalmond;
}
.form-control {
    background: azure !important;
}
span.remove-field.bg-danger {
    padding: 6px;
    border-radius: 34%;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <?php 
               
               $id         = $momdata[0]->id;
               $ccstatus   = $momdata[0]->ccstatus;
               $action_id  = $momdata[0]->action_id;
               $user_id    = $momdata[0]->user_id;
               $init_cmpid = $momdata[0]->init_cmpid;
               $tid        = $momdata[0]->tid;
               $actontaken = $momdata[0]->actontaken;

               $getCompanyData = $this->Menu_model->get_cmpbyinid($init_cmpid);
               $getCompanyPartner = $this->Menu_model->get_partnerbyid($getCompanyData[0]->partnerType_id)[0]->name;

                $meetingdonewinitiator = $momdata[0]->meetingdonewinitiator;

                $presentation          = $momdata[0]->presentation;

                $project_intervention_select = $momdata[0]->project_intervention_select;
                $project_intervention        = $momdata[0]->project_intervention;

                $client_has_adopted_select   = $momdata[0]->client_has_adopted_select;
                $client_has_adopted          = $momdata[0]->client_has_adopted;

                $approving_autorities   = $momdata[0]->approving_autorities;

                $budget_for_cfyear      = $momdata[0]->budget_for_cfyear;

                $fund_sanstion_limit    = $momdata[0]->fund_sanstion_limit;

                $other_specific_remarks = $momdata[0]->other_specific_remarks;

                $submit_proposal        = $momdata[0]->submit_proposal;
                $proposal_no_of_school  = $momdata[0]->proposal_no_of_school;
                $proposal_of_budget     = $momdata[0]->proposal_of_budget;
                $proposal_of_location   = $momdata[0]->proposal_of_location;

                $identify_school             = $momdata[0]->identify_school;
                $identify_school_state       = $momdata[0]->identify_school_state;
                $identify_school_district    = $momdata[0]->identify_school_district;
                $no_of_school                = $momdata[0]->no_of_school;


                $permission_letter                = $momdata[0]->permission_letter;
                $permission_letter_rech           = $momdata[0]->permission_letter_rech;
                $letter_organization_name         = $momdata[0]->Letter_organization_name;
                $letter_organization_designation  = $momdata[0]->Letter_organization_designation;
                $letter_organization_location     = $momdata[0]->Letter_organization_location;


                $client_int_school_visit     = $momdata[0]->client_int_school_visit;
                $client_int_type_project     = $momdata[0]->client_int_type_project;
                $client_int_school_date      = $momdata[0]->client_int_school_date;
                $client_int_school_state     = $momdata[0]->client_int_school_state;
                $client_int_school_district  = $momdata[0]->client_int_school_district;
                $client_int_no_of_school     = $momdata[0]->client_int_no_of_school;

                $intervention_cm_pst_sh     = $momdata[0]->intervention_cm_pst_sh;

                $rpmmom     = $momdata[0]->rpmmom;

                $partner     = $momdata[0]->partner;

                ?>
                <div class="card mt-2">
                  <div class="card-header bg-info">
                    <h3 class="text-center">MINUTES OF MEETING (Edit MoM)</h3>
                  </div>     
                  <div class="card-body">
                    <div class="container body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <fieldset>
                              <!-- Start Meeting Form -->
                  <form method="post" action="<?=base_url();?>/Management/UpdateEditMomData">
               
                    <input type="hidden" class="form-control" value="<?= $id ?>" id="id" name="id">
                    <input type="hidden" class="form-control" value="<?= $tardate ?>" name="tardate">
                    <input type="hidden" class="form-control" value="<?= $ccstatus ?>" id="ccstatus" name="ccstatus">
                    <input type="hidden" class="form-control" value="<?= $tid ?>" name="tid">
                    <input type="hidden" class="form-control" value="<?= $action_id ?>" id="action_id" name="action_id">
                    <input type="hidden" class="form-control" value="<?= $user_id ?>" id="user_id" name="user_id">
                    <input type="hidden" class="form-control" value="<?= $init_cmpid ?>" id="init_cmpid" name="init_cmpid">
                    <input type="hidden" class="form-control" value="<?= $actontaken ?>" id="actontaken" name="actontaken">
                    <input type="hidden" class="form-control" value="<?= $getCompanyPartner ?>" name="partner">

                  <label>Meeting done with Initiator or influencer and decision maker of the company</label>
                    <select class="form-control" name="meetingdonewinitiator">
                    <option value="Initiator" <?php if ($meetingdonewinitiator == 'Initiator') echo 'selected'; ?>>Initiator</option>
                    <option value="Influencer" <?php if ($meetingdonewinitiator == 'Influencer') echo 'selected'; ?>>Influencer</option>
                    <option value="Decision maker" <?php if ($meetingdonewinitiator == 'Decision maker') echo 'selected'; ?>>Decision maker</option>
                    </select>

                    <hr>
                    <label>Presentation and pitching is done for which offering :</label>
                    <?php 
                    $presentationArray = explode(',', $presentation);
                    // HTML part
                    echo '<select class="form-control" name="presentation[]" multiple>';
                    $options = [
                    'MSC', 'Tinkering', 'Bala', 'Astronomy', 'DIY', 'NSP', 'Science Lab', 'Smart Class'
                    ];
                    foreach ($options as $option) {
                    $selected = in_array($option, $presentationArray) ? 'selected' : '';
                    echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                    }
                    echo '</select>'; 
                    ?>
                    <hr>
                    <input type="hidden" name="momdata" value="momdata">

                    <div>
                      <label>What is the client's thematic Area for Project Intervention in the current financial Year</label>
                      <select class="form-control" id="project_intervention_select" name="project_intervention_select">
                          <option value="Education" <?php if ($project_intervention_select == 'Education') echo 'selected'; ?> >Education</option>
                          <option value="Health" <?php if ($project_intervention_select == 'Health') echo 'selected'; ?> >Health</option>
                          <option value="Nutrition" <?php if ($project_intervention_select == 'Nutrition') echo 'selected'; ?> >Nutrition</option>
                          <option value="Others" <?php if ($project_intervention_select == 'Others') echo 'selected'; ?> >Others</option>
                      </select> 
                      <br>
                      <input type="text" class="form-control" name="project_intervention" id="project_interventionText" placeholder="Please add client's thematic Area for Project Intervention in the current financial Year">
                    </div>
                    <hr>
                    <div>
                      <label>Does the client has adopted any schools ?</label>
                      <select class="form-control" id="client_has_adopted_select"  name="client_has_adopted_select">
                        <option value="no" <?php if ($client_has_adopted_select == 'no') echo 'selected'; ?>>No</option>
                        <option value="yes" <?php if ($client_has_adopted_select == 'yes') echo 'selected'; ?>>Yes</option>
                      </select>
                      <br>
                      <textarea type="text" class="form-control" name="client_has_adopted" placeholder="Please specify details of the schools that client has adopted"  id="client_has_adoptedText" rows="3"><?=$client_has_adopted?></textarea> 
                    </div>
                    <hr>
                    <div>
                      <label>Who are the approving autorities of the proposal ?</label>
                      <textarea  class="form-control" name="approving_autorities" placeholder="Please type name and designation of the officer approving the proposal"><?=$approving_autorities?></textarea>
                    </div>
                    <hr>
                    <div>
                      <label>What is the left over budget for the current financial year ?</label>
                      <input type="number"  class="form-control" name="budget_for_cfyear" value="<?=$budget_for_cfyear?>" placeholder="Please Type budget for the current financial year">
                    </div>
                    <hr>
                    <div>
                      <label>what is the fund sanction limit at their level ?</label>
                      <input type="number"  class="form-control" name="fund_sanstion_limit" value="<?=$fund_sanstion_limit?>" placeholder="Please Type fund sanction limit at their level">
                    </div>
                    <hr>
                    <div>
                      <label>Any other specific remarks regards to the meeting ?</label>
                      <textarea type="text"  class="form-control" name="other_specific_remarks" placeholder="specific remarks regards to the meeting"  rows="3"><?=$other_specific_remarks?></textarea> 
                    </div>
                    <hr>
                    <div>
                    <label>Do we need to submit the proposal ?</label>
                    <select class="form-control"  name="submit_proposal" id="submit_proposal_select" >
                        <option value="no" <?php if ($submit_proposal == 'no') echo 'selected'; ?> >No</option>
                        <option value="yes" <?php if ($submit_proposal == 'yes') echo 'selected'; ?> >Yes</option>
                    </select>
                    <br>
                    <div id="submit_proposal_file" class="identify_school_box">
                    <input type="text"  class="form-control"name="proposal_no_of_school" value="<?=$proposal_no_of_school?>" placeholder="Proposed number of schools"> <br>
                    <input type="text"  class="form-control"name="proposal_of_budget" value="<?=$proposal_of_budget?>" placeholder="Proposed budget"><br>
                    <input type="text"  class="form-control"name="proposal_of_location" value="<?=$proposal_of_location?>" placeholder="Proposed location"><br>
                    </div>
                    </div>
                     <hr>
                    <div>
                    <label>Do we need to identify school ?</label>
                    <select class="form-control"  name="identify_school" id="identify_school_select" >
                        <option value="no" <?php if ($identify_school == 'no') echo 'selected'; ?> >No</option>
                        <option value="yes" <?php if ($identify_school == 'yes') echo 'selected'; ?> >Yes</option>
                    </select>
                    <?php 
                    if($identify_school == 'yes'){
                      $states = explode(',', $identify_school_state);
                      $districts = explode(',', $identify_school_district);
                      $schoolCounts = explode(',', $no_of_school);

                      // Combine into a single array
                      $combinedArray = [];

                      for ($i = 0; $i < count($states); $i++) {
                          $combinedArray[] = [
                              'identify_school_state' => $states[$i],
                              'identify_school_district' => $districts[$i],
                              'no_of_school' => $schoolCounts[$i]
                          ];
                      }
                    }
                   $combinedArraycnt = sizeof($combinedArray);
                    ?>
                    <br>
                    <div id="identify_school_box" class="identify_school_box">
                      <div class="text-right mb-2">
                      <span id="add_field" class="p-2 bg-primary" >+</span>
                      </div>
                      <?php 
                         if($combinedArraycnt > 0){
                              foreach($combinedArray as $arrays){ ?>
                              <lable>Name of State : </lable>
                                   <input type="text"  class="form-control"name="identify_school_state[]" value="<?=$arrays['identify_school_state']?>" placeholder="Enter Name of State">
                                   <lable>Name of District : </lable>
                                   <input type="text"  class="form-control"name="identify_school_district[]" value="<?=$arrays['identify_school_district']?>" placeholder="Enter Name of District">
                                   <lable>No of School : </lable>
                                   <input type="text"  class="form-control"name="no_of_school[]" value="<?=$arrays['no_of_school']?>" placeholder="Enter No of School"> <br/>
                            <?php } } ?>
                    </div>
                    
                    </div>
         
                    <hr>
                    <div>
                    <label>School permission letter  ?</label>
                    <select class="form-control"  name="permission_letter" id="permission_letter_select" >
                        <option value="no" <?php if ($permission_letter == 'no') echo 'selected'; ?> >No</option>
                        <option value="yes" <?php if ($permission_letter == 'yes') echo 'selected'; ?> >Yes</option>
                    </select>
                    </div>
                
                    <hr>
                    <div>
                     
                    <div id="permission_letter_box" class="identify_school_box" >
                    <label>Letter should be address to whom in the organization, along with Name and designation and Location</label>

                    <select class="form-control"  name="permission_letter_rech" >
                        <option value="NGO" <?php if ($permission_letter_rech == 'NGO') echo 'selected'; ?> >NGO</option>
                        <option value="STEM" <?php if ($permission_letter_rech == 'STEM') echo 'selected'; ?> >STEM</option>
                    </select>
                    <br>
                     <lable>Concern person name : </lable>              
                    <input type="text"  value="<?=$letter_organization_name?>" class="form-control"name="Letter_organization_name" placeholder="Add Concern person name"> <br>
                    <lable>Name of Designation : </lable> 
                    <input type="text"  value="<?=$letter_organization_designation?>" class="form-control"name="Letter_organization_designation" placeholder="Enter Name of Designation"><br>
                    <lable>Location : </lable> 
                    <input type="text"  value="<?=$letter_organization_location?>" class="form-control"name="Letter_organization_location" placeholder="Enter Location">
                    </div>  
                    </div>

                    <hr>
                    <div>
                    <label>Client is interested for School Visit ?</label>
                    <select class="form-control"  name="client_int_school_visit" id="client_int_school_select">
                        <option value="no" <?php if ($client_int_school_visit == 'no') echo 'selected'; ?> >No</option>
                        <option value="yes" <?php if ($client_int_school_visit == 'yes') echo 'selected'; ?> >Yes</option>
                    </select>
                    <br>
                    <div id="client_int_school_box" class="identify_school_box">

                    <!-- <input type="text"  class="form-control"name="client_int_type_project" placeholder="Add type of project"> -->
                    <select class="form-control" name="client_int_type_project">
                      <option selected disabled>Select Type of project</option>
                      <option value="MSC" <?php if ($client_int_type_project == 'MSC') echo 'selected'; ?>>MSC</option>
                      <option value="Tinkering" <?php if ($client_int_type_project == 'Tinkering') echo 'selected'; ?>>Tinkering</option>
                      <option value="Bala" <?php if ($client_int_type_project == 'Bala') echo 'selected'; ?>>Bala</option>
                      <option value="Astronomy" <?php if ($client_int_type_project == 'Astronomy') echo 'selected'; ?>>Astronomy</option>
                      <option value="DIY" <?php if ($client_int_type_project == 'DIY') echo 'selected'; ?>>DIY</option>
                      <option value="NSP" <?php if ($client_int_type_project == 'NSP') echo 'selected'; ?>>NSP</option>
                      <option value="Science Lab" <?php if ($client_int_type_project == 'Science Lab') echo 'selected'; ?>>Science Lab</option>
                      <option value="Smart Class" <?php if ($client_int_type_project == 'Smart Class') echo 'selected'; ?>>Smart Class</option>
                    </select>
                    <br>
                    <lable>Date : </lable>
                    <input type="date"  value="<?=$client_int_school_date?>" class="form-control"name="client_int_school_date" placeholder="Select Date"> <br>
                    <lable>State : </lable>
                    <input type="text"  value="<?=$client_int_school_state?>" class="form-control"name="client_int_school_state" placeholder="Enter State"> <br>
                    <lable>District : </lable>
                    <input type="text"  value="<?=$client_int_school_district?>" class="form-control"name="client_int_school_district" placeholder="Enter Name of District"><br>
                    <lable>Number of School : </lable>
                    <input type="number"  value="<?=$client_int_no_of_school?>" class="form-control"name="client_int_no_of_school" placeholder="Enter no of School">
                    </div>

                    </div>
                    <hr>
                    <div>
                    <label>Do you need intervention from Cluster/PST/ Sales Head ?</label>
                    <select class="form-control"  name="intervention_cm_pst_sh" id="client_int_school_select" >
                        <option <?php if ($intervention_cm_pst_sh == 'Cluster') echo 'selected'; ?> value="Cluster">Cluster</option>
                        <option <?php if ($intervention_cm_pst_sh == 'PST') echo 'selected'; ?> value="PST">PST</option>
                        <option <?php if ($intervention_cm_pst_sh == 'Sales Head') echo 'selected'; ?> value="Sales Head">Sales Head</option>
                    </select>
                    </div> 

                    <hr>
                    <label>Write Short MOM Remarks</label>
                      <textarea type="text" class="form-control" id="rpmmom" name="rpmmom" rows="3" ><?=$rpmmom ?></textarea> 
                  </div>
                  <!-- End Meeting Form -->
                              <hr>
                                   <button type="submit" class="btn btn-primary">Submit</button>
                              </form>
                          </fieldset>
                        </div>
                        <hr />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
    </div>
    </section>
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'></script>
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
        $(document).ready(function() {
          $('#smallProposaltext').hide();
          $('#identify_school_box').hide();
          $('#permission_letter_box').hide();

            $('#project_intervention_select').change(function() {
                if ($(this).val() === 'Others') {
                    $('#project_interventionText').show();
                } else {
                    $('#project_interventionText').hide();
                }
            });

            $('#client_has_adopted_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#client_has_adoptedText').show();
                } else {
                    $('#client_has_adoptedText').hide();

                }
            });

            $('#submit_proposal_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#submit_proposal_file').show();
                    $('#smallProposaltext').show();
                } else {
                    $('#submit_proposal_file').hide();
                    $('#smallProposaltext').hide();
                }
            });
            $('#identify_school_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#identify_school_box').show();
                } else {
                    $('#identify_school_box').hide();
                }
            });
            $('#client_int_school_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#client_int_school_box').show();
                } else {
                    $('#client_int_school_box').hide();
                }
            });
            
            $('#permission_letter_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#permission_letter_box').show();
                } else {
                    $('#permission_letter_box').hide();
                }
            });

            // Trigger the change event on page load in case "Others" is preselected
            $('#project_intervention_select').trigger('change');
            $('#client_has_adopted_select').trigger('change');
            $('#submit_proposal_select').trigger('change');
            $('#identify_school_select').trigger('change');
            $('#client_int_school_select').trigger('change');
            $('#permission_letter_select').trigger('change');


             // Start Add More School Data When Mom Upload 
           // Function to add a new set of fields
           $('#add_field').click(function() {
                $('#identify_school_box').append(`
                    <div class="identify_school_box mt-2">
                        <input type="text"  class="form-control" name="identify_school_state[]" placeholder="Enter Name of State"> <br>
                        <input type="text"  class="form-control" name="identify_school_district[]" placeholder="Enter Name of District"><br>
                        <input type="text"  class="form-control" name="no_of_school[]" placeholder="Enter No of School">
                        <br>
                         <span class="remove-field bg-danger mt-4">-</span>
                    </div>
                `);
            });

            // Function to remove a set of fields
            $('#identify_school_box').on('click', '.remove-field', function() {
                $(this).parent().remove();
            });
            // End Add More School Data When Mom Upload 
        });
    </script>
  </body>
</html>