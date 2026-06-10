<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>India Location | STEM APP | WebAPP</title>
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
      <style>
        .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{height:600px}.card-height1{height:300px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}
      </style>
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
        <?php
        if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <?php endif; ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <h5>State Wise Area 🧭</h5>
                    <small style="color: #555;">
                    Displays a breakdown of geographical areas categorized under each state. Useful for understanding the administrative distribution and coverage.
                    </small>
                  </div>
                  <hr>
                  <!-- /.card-header -->
                  <?php 
                    $totalLocation        = $mdata['totalLocation'];
                    
                    $totalLocations = $getLocations['totalLocation'];
                    
                    $total_state = $totalLocations[0]->total_state;
                    $total_districts = $totalLocations[0]->total_districts;
                    $total_city = $totalLocations[0]->total_city;
                    
                    $totalByLocation = $getLocations['totalByLocation'];

                    ?>
                  <div class="card-body">
                    <hr>
                    <div class="row" style="background: azure; padding: 25px; border-radius: 20px;">
                      <div class="col-md-4">
                        <div class="card" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h5>Total State 🏞️</h5>
                            <small style="color: #555;">Number of states currently available in the system.</small>
                            <hr>
                            <h3>
                              <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/IndiaLocationDetailsByData/total_state">
                              <?=$total_state?>
                              </a>
                            </h3>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="card" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h5>Total District 🗺️</h5>
                            <small style="color: #555;">Number of districts available under the selected state.</small>
                            <hr>
                            <h3> <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/IndiaLocationDetailsByData/total_districts"><?=$total_districts?></a></h3>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="card" style="background: linear-gradient(to right, #fce4ec, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h5>Total City 🏙️</h5>
                            <small style="color: #555;">Number of cities added under the selected state or district.</small>
                            <hr>
                            <h3> <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/IndiaLocationDetailsByData/total_city"><?=$total_city?></a></h3>
                          </div>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <?php if($user['type_id'] == 2){ ?> 
                    <hr>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="col dflex text-white text-center">
                            <div style="background: linear-gradient(to right,rgb(228, 252, 233), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <button type="button" id="add_createact" class="custom-btn btn-11" style="width: fit-content;" onclick="AddNewLocation('State')"><b>+</b>State 🏞️</button>
                            </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="col dflex text-white text-center">
                             <div style="background: linear-gradient(to right,rgb(228, 252, 233), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                                    <button type="button" onclick="AddNewLocation('District')" id="add_createact" class="custom-btn btn-11" style="width: fit-content;"><b>+</b> District 🗺️</button>
                             </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="col dflex text-white text-center">
                            <div style="background: linear-gradient(to right,rgb(228, 252, 233), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                                <button type="button" onclick="AddNewLocation('city')" id="add_createact" class="custom-btn btn-11" style="width: fit-content;"><b>+</b> City 🏙️</button>
                            </div>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <?php } ?>
                    <?php  // echo $user['user_id']; ?>
                    <div class="container-fluid body-content">
                      <div class="page-header">
                        <div class="form-group" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <fieldset>
                            <div class="table-responsive">
                              <div class="table-responsive text-nowrap">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                    <tr>
                                      <th>S.No</th>
                                      <th>State Name</th>
                                      <th>Total District</th>
                                      <th>Total City</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1;
                                        foreach($totalByLocation as $dt){
                                            $cur_state_id = $dt->state_id;
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
                                      <td><?=$i?></td>
                                      <td><?=$dt->state_title?></td>
                                      <td>
                                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/IndiaLocationDetailsByDatas/total_districts_state_title/<?=$cur_state_id?>">
                                             <?=$dt->total_districts_state_title?>
                                        </a>
                                    </td>
                                      <td>
                                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/IndiaLocationDetailsByDatas/total_city_state_title/<?=$cur_state_id?>">
                                        <?=$dt->total_city_state_title?>
                                        </a>
                                    </td>
                                    </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <!--END OF FORM ^^-->
                          </fieldset>
                        </div>
                        <hr />
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

    <?php if($user['type_id'] == 2){ ?> 
    <div id="AddNewState" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-standard-title1"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- // END .modal-header -->
          <div class="modal-body">
            <div class="card-form col-md-12">
              <div class="row no-gutters">
                <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <center>
                    <h5>Add New State</h5>
                  </center>
                  <hr>
                  <?=form_open('Reports/AddNewState')?>
                  <lable style="font-weight: 600;">New State Name : </lable>
                  <input type="text" id="new_state_name" class="form-control" placeholder="Enter State Name" name="new_state_name" required>
                  <center>
                    <button type="submit" class="custom-btn btn-11 mt-3">+ Add State</button>
                  </center>
                  </form>
                </div>
              </div>
            </div>
            <br>
          </div>
        </div>
      </div>
    </div>



    <div id="AddDistrict" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-standard-title1"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- // END .modal-header -->
          <div class="modal-body">
            <div class="card-form col-md-12">
              <div class="row no-gutters">
                <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <center>
                    <h5>Add New District</h5>
                  </center>
                  <hr>
                  <?=form_open('Reports/AddNewDistrict')?>

                  <lable style="font-weight: 600;">Select State</lable>
                    <select id="state_id" name="in_state_id" class="form-control" required>
                        <option value="">Select State</option>
                        <?php $allstate = $this->Menu_model->GetState();
                        foreach($allstate as $state){?>
                            <option value="<?=$state->state_id?>"><?=$state->state_title?></option>
                        <?php } ?>
                    </select>

                  <lable style="font-weight: 600;">New District Name : </lable>
                  <input type="text" id="new_district_name" class="form-control" placeholder="Enter District Name" name="new_district_name" required>
                  <center>
                    <button type="submit" class="custom-btn btn-11 mt-3">+ Add District</button>
                  </center>
                  </form>
                </div>
              </div>
            </div>
             <br>
          </div>
        </div>
      </div>
    </div>


    <div id="AddNewCity" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-standard-title1"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- // END .modal-header -->
          <div class="modal-body">
            <div class="card-form col-md-12">
              <div class="row no-gutters">
                <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <center>
                    <h5>Add New City</h5>
                  </center>
                  <hr>
                  <?=form_open('Reports/AddNewCity')?>

                  <lable style="font-weight: 600;">Select State</lable>
                    <select id="select_state_id" name="in_state_id" class="form-control" required>
                        <option value="">Select State</option>
                        <?php $allstate = $this->Menu_model->GetState();
                        foreach($allstate as $state){?>
                            <option value="<?=$state->state_id?>"><?=$state->state_title?></option>
                        <?php } ?>
                    </select>

                <lable style="font-weight: 600;">Select District</lable>
                <select id="in_district_id" name="in_district_id" class="form-control" required>
                </select>

                  <lable style="font-weight: 600;">New City Name : </lable>
                  <input type="text" id="city_name" class="form-control" placeholder="Enter City Name" name="new_city_name" required>
                  <center>
                    <button type="submit" class="custom-btn btn-11 mt-3">+ Add City</button>
                  </center>
                  </form>
                </div>
              </div>
            </div>
             <br>
          </div>
        </div>
      </div>
    </div>
 <?php } ?> 



    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'>
      function AddNewLocation(val) {
        if(val == 'State'){
            $('#AddNewState').modal('show');
        }else if(val == 'District'){
            $('#AddDistrict').modal('show');
        }
        else if(val == 'city'){
            $('#AddNewCity').modal('show');
        }
        }
$('#select_state_id').on('change', function() {
    var selectedStateId = $(this).val();             // State ID (value)
    $.ajax({
        url:'<?=base_url();?>Menu/GetDistrictByStateID',
        type: "POST",
        data: {
        stid: selectedStateId
        },
        cache: false,
        success: function a(result){
        $("#in_district_id").html(result);
        }
    });
});
        
              
          
          
    </script>
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
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>