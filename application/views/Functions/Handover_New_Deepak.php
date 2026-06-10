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
      .modern-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.modern-table thead {
    background: #4e73df;
    color: #fff;
}

.modern-table th, .modern-table td {
    padding: 12px 15px;
    text-align: left;
}

.modern-table tbody tr {
    border-bottom: 1px solid #eee;
    transition: 0.2s;
}

.modern-table tbody tr:hover {
    background: #f5f7ff;
}

.modern-table th {
    font-weight: 600;
    font-size: 14px;
}

.modern-table td {
    font-size: 14px;
    color: #444;
}

.checkbox-style input {
    transform: scale(1.2);
    cursor: pointer;
}
tr,td{
  font-weight: 600;
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
                  <form action="<?=base_url();?>Menu/bdHandover_With_New_Pre_Post" id="handover_new_form" method="post" enctype="multipart/form-data">
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
                                <select class="form-control" name="spd_identify_by" id="spd_identify_by" placeholder="School Identification by" required>
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
                   


                    <?php 
                    $spd_verifys      = $this->Menu_model->GetRequestSchoolDataonHandoverTime($cid);
                    $spd_verifys_cnt  = sizeof($spd_verifys);
                    ?>

                      <hr>               
                        <div class="container p-3" style="background: aliceblue; border-radius:10px;">
                          <div class="col">
                            <label>🆔 Identification Process :</label>
                            <select class="form-control" name="idprocess" id="idprocess" required>
                              <option value="" selected disabled>🔽 -- Select Identification Type --</option>
                              <option value="Post">📦 Identification After Handover</option>
                              <option value="Pre" <?php if($spd_verifys_cnt == 0){ echo "disabled";} ?> title="Pre-Identification of Schools : <?= $spd_verifys_cnt; ?>">📝 Identification Before Handover</option>
                            </select>
                            
                            <div class="invalid-feedback">⚠️ Please select an option.</div>
                            <div class="valid-feedback">✅ Looks good!</div>
                          </div>
                          <span class="ml-2 p-2" id="idprocess_error"></span>
                          <hr>
                          <div class="ml-2 p-2 text-center"> 
                            <a target="_blank" href="<?=base_url()?>Menu/CompanyDetails/<?= $cid ?>">(CID - <?= $cid ?>) - <?=$md->compname?></a>  
                            - Pre-Identification of Schools : <?= $spd_verifys_cnt; ?>
                          </div>
                        </div>
                      <hr>


                

                  <div class="table-responsive" id="preidentifiedSchoolList">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Select</th>
                                <th>School Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Locations</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php 
                                $sd = 1;
                                foreach($spd_verifys as $spd_verify){ 
                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";

                                $hue        = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness  = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                            ?>
                            <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                                <td><?=$sd?></td>
                                <td class="checkbox-style">
                                    <input type="checkbox" name="selected_school[]" value="<?=$spd_verify->id;?>">
                                </td>
                                <td><?=$spd_verify->sname;?></td>
                                <td><?=$spd_verify->sstate;?></td>
                                <td><?=$spd_verify->sdistrict;?></td>
                                <td><?=$spd_verify->scity;?></td>
                                <td><?=$spd_verify->saddress;?></td>
                                <td>
                                  <?php if($spd_verify->slocation == null){ $spd_verify->slocation = "-"; }else{?> 
                                  <a href="<?=$spd_verify->slocation?>">view locations</a>
                                  <?php } ?>
                              </td>
                            </tr>
                            <?php 
                                $sd++; 
                                } 
                            ?>
                        </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-12 mb-4">
                  <div class="confirmarea">
                    <div class="card p-3" style="background: burlywood;">
                      <div class="card p-5 col-12" style="background: cornsilk;"><b><input id="allcheck" class="form-check-input" type="checkbox" onchange="toggleButton()"> Thank you for completing the form. Before you submit, please take a moment to review your responses for accuracy and completeness. Once you click the 'Submit' button, your form will be processed, and we will consider the information provided as final.</b>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer text-center">
                    <button type="submit" id="btn" class="btn btn-primary" disabled style="width:150px;">Submit</button>
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

    <!-- jQuery (Load First) -->
    <script type='text/javascript'>


        function toggleButton() {
                var checkbox = document.getElementById("allcheck");
                var button = document.getElementById("btn");

                button.disabled = !checkbox.checked;
            }



      $(document).ready(function () {



      $('#preidentifiedSchoolList').hide();




         


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
      
      

            
      
            $('#idprocess').on('change', function b() {
                var idprocess = this.value;
      
                if(idprocess=='Pre'){
                    $('#preidentifiedSchoolList').show();
                    $('#idprocess_error').text(" * You have selected ‘Identification Before Handover’ as the Identification Process. Please ensure that all required details are verified accordingly. Below, you can view the data of the schools previously identified for this client. Kindly review and confirm the schools that need to be handed over as part of this process.") .css('color', 'green');;
                }
                if(idprocess=='Post'){
                    $("#preidentifiedSchoolList").hide();
                    $('#idprocess_error').text(" * You have selected “Identification After Handover” as the Identification Process. Following this selection, an automatic task for BD Request Identification will be assigned to you after submit this form.") .css('color', 'red');;
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
  </body>
</html>