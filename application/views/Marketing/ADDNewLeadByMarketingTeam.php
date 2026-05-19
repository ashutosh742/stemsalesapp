<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>STEM APP | WebAPP</title>
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
      .contact-group {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .removeContact {
            margin-top: 10px;
        }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
<?php $this->load->view($dep_name.'/nav.php');?>
    <?php $uid=$user['user_id']?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <br>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-12 m-auto">
                <!-- Default box -->
                <div class="card" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <div class="card-header">
                    <div class="text-center ">
                      <h3>Add New Client Details </h3>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body box-profile p-5">
            
                 <form method="post" id="myUpdateForm" action="<?=base_url();?>Menu/AddNewClientDetails" style="background:#f9f9f9;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
  <div class="form-row">
    <!-- LEFT SIDE -->
    <div class="col-sm-12 col-lg-6 p-3" style="border-right:2px solid #eee;">
      <div class="was-validated">
        <!-- Company Name -->
        <div class="form-group">
          <label for="compname">🏢 Company Name</label>
          <input type="text" class="form-control" placeholder="Enter Company Name" id="compname" name="compname" required>
          <div class="invalid-feedback">Please provide a Company name.</div>
        </div>

        <!-- Website -->
        <div class="form-group">
          <label for="website">🌐 Website Link</label>
          <input type="text" class="form-control" id="website" name="website" placeholder="Company Website" required>
          <div class="invalid-feedback">Please provide a company's website.</div>
        </div>

        <!-- Country -->
        <div class="form-group">
          <label for="country">🌏 Country</label>
          <select class="form-control" id="country" name="country" required>
            <option value="1" selected>India</option>
          </select>
        </div>


        <?php $userDatas =  $this->Menu_model->GetAllActiveBDLists(); ?>
        <!-- Sales Person -->
        <div class="form-group">
          <label for="main_bd">👨‍💼 Select Sales Person</label>
          <select class="form-control" id="main_bd" name="main_bd" required>
            <option value="" disabled selected> -- Select -- </option>
            <?php foreach($userDatas as $userData):
              $hue = rand(100, 255);
              $saturation = rand(60, 100);
              $lightness = rand(30, 45);
              $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
            ?>
              <option value="<?=$userData->user_id;?>" style="color:<?=$backgroundColorNew;?>;"><?=$userData->name;?> - (<?= $userData->type_name ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>


         <!-- Country -->
        <div class="form-group">
          <label for="country">🌏 Travel Type</label>
          <select class="form-control" id="travel_type" name="travel_type" required>
            <option value="" selected disabled>--select--</option>
            <option value="base">Base Station</option>
            <option value="outStation">Out Station</option>
          </select>
        </div>

        <!-- Cluster -->
        <div class="form-group">
          <label for="cluster">📍 Add Cluster</label>
          <select id="cluster" class="form-control" name="cluster" required>
          </select>
        </div>

        <!-- Location -->
        <div class="form-group">
          <label>🗺 State</label>
          <select name="state" id="id_state" required class="form-control"></select>
        </div>
        <div class="form-group">
          <label>🏙 District</label>
          <select name="id_district" id="id_district" required class="form-control"></select>
        </div>
        <div class="form-group">
          <label>🌆 City</label>
          <select name="id_city" id="id_city" required class="form-control"></select>
        </div>

        <!-- Address -->
        <div class="form-group">
          <label for="address">📮 Address</label>
          <textarea class="form-control" name="address" id="address" placeholder="Full Address" rows="3" required><?=$address?></textarea>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-sm-12 col-lg-6 p-3">
      <div class="was-validated">
        <!-- Company Type -->
        <div class="form-group">
          <label for="ctype">🏭 Company Type</label>
          <select id="ctype" name="ctype" class="form-control" required>
            <option value="">Select Partner Type</option>
            <?php foreach($partner as $p){ ?>
              <option value="<?=$p->id?>"><?=$p->name?></option>
            <?php }?>
          </select>
        </div>

        <!-- Budget -->
        <div class="form-group">
          <label for="budget">💰 Budget</label>
          <input type="text" name="budget" class="form-control" id="budget" placeholder="Budget" required>
        </div>

        <!-- Contact Details -->
        <div class="form-group">
          <label for="contactperson">👤 Contact Person</label>
          <input type="text" class="form-control" id="contactperson" name="compconname" placeholder="Contact person name" required>
        </div>
        <div class="form-group">
          <label for="designation">🎓 Designation</label>
          <input type="text" name="designation" class="form-control" id="designation" placeholder="Designation" required>
        </div>
        <div class="form-group">
          <label for="emailid">📧 Email ID</label>
          <input type="email" class="form-control" id="emailid" name="emailid" placeholder="Enter Email Id" required>
        </div>
        <div class="form-group">
          <label for="phoneno">📱 Mobile Number</label>
          <input type="text" minlength="10" maxlength="10" pattern="[0-9]{10}" class="form-control" id="phoneno" name="phoneno" placeholder="Enter 10-digit Mobile Number" required>
        </div>

        <!-- Contact Type -->
        <div class="form-group">
          <label>☎️ Contact Type</label><br>
          &nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" type="radio" name="contact_type" value="primary" checked> Primary 
          &nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" type="radio" name="contact_type" value="alternate"> Secondary
        </div>

        <!-- Company Flags -->
        <div class="form-group">
          <label>⭐ Top Spender</label><br>
          <input type="radio" name="top_spender" value="yes"> Yes 
          &nbsp;&nbsp;
          <input type="radio" name="top_spender" value="no"> No
        </div>

        <div class="form-group">
          <label>📈 Upsell Client</label><br>
          <input type="radio" name="upsell_client" value="yes"> Yes 
          &nbsp;&nbsp;
          <input type="radio" name="upsell_client" value="no"> No
        </div>

        <div class="form-group">
          <label>🎯 Focus Funnel</label><br>
          <input type="radio" name="focus_funnel" value="yes"> Yes 
          &nbsp;&nbsp;
          <input type="radio" name="focus_funnel" value="no"> No
        </div>

        <div class="form-group">
          <label>🔑 Key Client</label><br>
          <input type="radio" name="key_client" value="yes"> Yes 
          &nbsp;&nbsp;
          <input type="radio" name="key_client" value="no"> No
        </div>

        <div class="form-group">
          <label>💡 Potential Client</label><br>
          <input type="radio" name="potential_company" value="yes"> Yes 
          &nbsp;&nbsp;
          <input type="radio" name="potential_company" value="no"> No
        </div>

        <!-- Add More Contacts -->
        <div class="text-center" style="margin:20px 0;">
          <h5>➕ Add More Contact Details</h5>
          <button type="button" id="addContact" class="btn btn-sm btn-primary">Add New Contact</button>
        </div>
        <div id="contactContainer"></div>
      </div>
    </div>
  </div>

  <!-- Comments -->
  <div class="col-md-12" style="margin-top:20px;">
    <label for="draft">📝 Quick Comments / Observations</label>
    <textarea name="draft" id="draft" class="form-control" rows="5" placeholder="Write observation or comments about the company..."><?= $draft ?></textarea>
  </div>

  <!-- Submit -->
  <center style="width: 100%;margin-top:20px;">
    <button class="btn btn-success" style="width:180px;height:55px;font-size:18px;" type="submit" id="submitButton">🚀 Submit</button>
  </center>
</form>

                    </div>
                    <!-- /.card -->
                  </div>
                </div>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                    <script>
                $(document).ready(function () {
                    var countdown = 60;
                    var countdownInterval;

                    $("#myUpdateForm").on("submit", function (e) {
                        var $button = $("#submitButton");

                        // Disable the button immediately on form submission
                        $button.prop("disabled", true);
                        $button.text(countdown + " seconds");

                        // Start countdown
                        countdownInterval = setInterval(function () {
                            countdown--;
                            $button.text(countdown + " seconds");

                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                $button.prop("disabled", false);
                                $button.text("Submit");
                                countdown = 60; // Reset countdown
                            }
                        }, 1000);
                        // Allow form to submit normally
                    });
                });





                $( document ).ready(function() {
      var cluster_id = document.getElementById("cluster").value;
      $.ajax({
            url:'<?=base_url();?>Menu/GetClusterState',
            type: "POST",
            data: {
            cluster_id: cluster_id
            },
        cache: false,
        success: function a(result){
            $("#id_state").html(result);
        }
        });

        $.ajax({
            url:'<?=base_url();?>Menu/GetClusterDistrict',
            type: "POST",
            data: {
            cluster_id: cluster_id
            },
        cache: false,
        success: function a(result){
            $("#id_district").html(result);
        }
        });
        $.ajax({
            url:'<?=base_url();?>Menu/GetClusterCitys',
            type: "POST",
            data: {
            cluster_id: cluster_id
            },
        cache: false,
        success: function a(result){
            $("#id_city").html(result);
        }
        });
});


$('#cluster').on('change', function b() {
        var cluster_id = document.getElementById("cluster").value;
        $.ajax({
            url:'<?=base_url();?>Menu/GetClusterState',
            type: "POST",
            data: {
            cluster_id: cluster_id
            },
        cache: false,
        success: function a(result){
            $("#id_state").html(result);
        }
        });

        $.ajax({
            url:'<?=base_url();?>Menu/GetClusterDistrict',
            type: "POST",
            data: {
            cluster_id: cluster_id
            },
        cache: false,
        success: function a(result){
            $("#id_district").html(result);
        }
        });
        $.ajax({
            url:'<?=base_url();?>Menu/GetClusterCitys',
            type: "POST",
            data: {
            cluster_id: cluster_id
            },
        cache: false,
        success: function a(result){
            $("#id_city").html(result);
        }
        });

});













                </script>
                <script>
        $(document).ready(function() {
            $('#addContact').click(function() {
                var contactGroup = `
                    <div class="contact-group">
                        <div class="form-row">
                            <div class="col-12 col-md-6">
                                <label for="contactperson">Contact Person Name</label>
                                <input type="text" class="form-control" placeholder="Contact person name" required name="new_compconname[]">
                                <div class="invalid-feedback">Please provide a contact person name.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="designation">Designation</label>
                                <input type="text" name="new_designation[]" class="form-control" placeholder="Designation" required>
                                <div class="invalid-feedback">Please provide a designation.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-6">
                                <label for="emailid">Email ID</label>
                                <input type="email" class="form-control" placeholder="Email Id" name="new_emailid[]" required>
                                <div class="invalid-feedback">Please provide an email id.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="phoneno">Mobile Number</label>
                                <input type="text" minlength="10" maxlength="10" class="form-control" placeholder="Mobile Number" name="new_phoneno[]" required>
                                <div class="invalid-feedback">Please provide a valid number.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <label for="selectPersonType">Contact Type</label>
                                <select type="text" class="form-control" id="selectPersonType" name="new_contact_type[]">
                                  <option value="" disabled selected>Select Contact Type </option>
                                  <option value="primary">Primary</option>
                                  <option value="alternate">Alternate</option>
                                </select>
                                <div class="invalid-feedback">Please provide an person type.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger mt-3 removeContact">Remove</button>
                    </div>
                `;
                $('#contactContainer').append(contactGroup);
            });

            $('#contactContainer').on('click', '.removeContact', function() {
                $(this).closest('.contact-group').remove();
            });
        });
    </script>
                
                
                <script type='text/javascript'>



        $(document).ready(function(){


            $("#travel_type").on("change", function(){
                var main_bd = $("#main_bd").val();
                var selectedValue = $(this).val();

                if (main_bd === "" || main_bd === null) {
                  alert("* Please Select Sales Person");
                  return false;
                }

                $.ajax({
                  url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                  type: "POST",
                  data: {
                  uid: main_bd,
                  travelType: selectedValue,
                  },
                  cache: false,
                  success: function a(result){
                    $("#cluster").html(result);
                  }
                  });
            });



            $("#main_bd").on("change", function(){
                var travel_type = $("#travel_type").val();
                var main_bd = $(this).val();

                if (travel_type === "" || travel_type === null) {
                  alert("* Please Select User Type");
                  return false;
                }

                $.ajax({
                  url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                  type: "POST",
                  data: {
                  uid: main_bd,
                  travelType: travel_type,
                  },
                  cache: false,
                  success: function a(result){
                    $("#cluster").html(result);
                  }
                  });
            });


      
        });



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
                  url:'<?=base_url();?>Menu/getcitybystate',
                  type: "POST",
                  data: {
                  stid: stid
                  },
                  cache: false,
                  success: function a(result){
                  $("#city1").html(result);
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

                <!-- /.row (main row) -->
              </div>
              <!-- /.container-fluid -->
        </section>
        </div></div>
      </div>
      <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?= date("Y") ?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWi$dth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>