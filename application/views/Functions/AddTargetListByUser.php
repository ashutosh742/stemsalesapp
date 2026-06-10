<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Add Target List | STEM APP | WebAPP</title>
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
     .card-header,.col.card.coladjust1 select,.col.card.coladjust31 select,.container-fluid.body-content.mt-4,.each,.form-row>.col,.form-row>[class*=col-],.table-striped tbody tr:nth-of-type(odd),input.form-control,table.table-bordered.dataTable{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.scrollme{overflow-x:auto}.card-body{background:azure}.formSection{align-items:center;justify-content:center;display:flex}.form-control{width:200px!important}.modal-content{background:#faebd7}.coladjust{padding-right:5px;padding-left:5px;height:143px;align-items:center;justify-content:center;color:#000}.col.card.coladjust1{color:#000;text-align:center;align-items:center}th.sorting{background:#008b8b;color:#fff}.modal-header{background:#5f9ea0;color:#fff}.form-control.is-invalid,.was-validated .form-control:invalid{background-image:none!important}.form-control.is-valid,.was-validated .form-control:valid{background-image:none!important}.col.card.coladjust1 select{width:100%!important;height:100%!important;background:#f0fff0}.each{background:#f1f3f6;font-family:Arial,Helvetica,sans-serif;border:5px solid #eaeef5}.col.card.coladjust1.p-2.m-1{background:beige}.container-fluid.body-content.mt-4{padding:20px}.col.card.coladjust.m-1{background:#fff8dc}tbody{font-weight:500}.input-group{margin-bottom:10px;display:table-cell}.remove-btn{cursor:pointer;color:red}.coladjust121{min-height:143px;align-items:center;justify-content:center;color:#000;padding:20px}.col.card.coladjust31.p-2.m-1{align-items:center}.col.card.coladjust31 select{background:#f0fff0}.meetingbackground{background:#bdb76b}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
    <?php $this->load->view($dep_name.'/nav.php');?>

    <style>
          .maintitlebg{
             /* background-image: linear-gradient(to right, #0f2027, #203a43, #2c5364); */
             background-image: linear-gradient(to right top, #00922a, #008c6c, #0081a1, #0071ba, #0058ae, #4c4ea9, #6f429f, #8a328f, #bd3b82, #df5572, #f37865, #fb9f5f);
              border-radius: 12px;
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
              padding: 20px;
               background-size: 300% 300%;
         animation: waveBackground 10s ease infinite;
        }

            .custom-btn {
            width: 130px;
            height: 40px;
            color: #fff;
            border-radius: 5px;
            padding: 10px 25px;
            font-family: Lato, sans-serif;
            font-weight: 500;
            background: 0 0;
            cursor: pointer;
            transition: .3s;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
            outline: 0;
        }

        .btn-11 {
            border: none;
            background: #212ffb;
            background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
            color: #fff;
            overflow: hidden;
            width: fit-content;
        }

        .btn-11:hover {
            text-decoration: none;
            color: #fff;
            opacity: .7;
        }

        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: 3s ease-in-out infinite shiny-btn1;
        }

        .btn-11:active {
            box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }

        @keyframes shiny-btn1 {
            0% { transform: scale(0) rotate(45deg); opacity: 0; }
            80% { transform: scale(0) rotate(45deg); opacity: .5; }
            81% { transform: scale(4) rotate(45deg); opacity: 1; }
            100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
        tr,td{
          font-weight: 600;
        }
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
        }
    </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
          <?php if($this->session->flashdata('success_message')): ?>
          <div class="alert alert-success">
            <?php echo $this->session->flashdata('success_message'); ?>
          </div>
          <?php endif; ?>
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: #002a1f;">
                    <h2 class="text-center m-3 text-white">TARGET SETTING LIST</h2>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                  <?php  
                  // dd($targetData);
                  ?>

                    <div class="container-fluid body-content mt-4">
                      <div class="page-header">
                        <div class="table-responsive text-nowrap">
                          <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>📅 Quarter</th>
                                <th>📂 Review Type</th>
                                <th>👤 Target User</th>
                                <th>📄 Proposals</th>
                                <th>🌱 Prospective Leads</th>
                                <th>💰 Revenue</th>
                                <th>🏫 Schools</th>
                                <th>🤝 Meetings</th>
                                <th>📆 Scheduled</th>
                                <th>⚡ Bargaining</th>
                                <th>👥 By Partner Type</th>
                                <!-- <th>📁 By Category Type</th> -->
                                <th>🛫 Outstation</th>
                                <th>🏠 Local</th>
                                <th>🚀 Q1 Closure</th>
                                <th>🎯 FY Funnel</th>
                                <th>🌾 To Nurture (FY)</th>
                                <th>📌 50-Lead Funnel</th>
                                <th>🏆 Anchor Clients</th>
                                <th>🕒 Start</th>
                                <th>🕓 End</th>
                                <th>🗓️ Created</th>
                                <th>👤 Added By</th>
                                <th>👁️ View</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              
                              $i = 1; foreach($targetData as $data):
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
                                <td><?= $i; ?></td>
                                <td><?php 
                                if(!is_null($data->currentQuarter)){
                                  echo $data->currentQuarter;
                                }else{
                                  echo "NA";
                                }
                                ?></td>
                                <td><?php 
                                if(!is_null($data->reviewtype)){
                                  echo $data->reviewtype;
                                  // echo "<span class='custom-btn btn-11'>".$data->reviewtype."</span>";
                                }else{
                                  echo "NA";
                                }
                                ?></td>
                                <td><?= $data->to_user; ?></td>
                                <td><?= $data->no_of_proposal; ?></td>
                                <td><?= $data->no_of_prospective; ?></td>
                                <td><?= $data->revenue; ?></td>
                                <td><?= $data->school; ?></td>
                                <td><?= $data->no_of_meeting; ?></td>
                                <td><?php 
                                  if(!is_null($data->num_of_sheduled_meeting)){
                                  echo $data->num_of_sheduled_meeting;
                                }else{
                                  echo "NA";
                                }
                                ?></td>
                                <td><?= $data->num_of_barg_meeting; ?></td>
                                <td>
                                  <?php
                                    if($data->partner_id !== '' ){
                                      $partnearray = json_decode($data->partner_id, true);
                                      foreach($partnearray as $key => $value){
                                        $getpartner_name = $this->Menu_model->get_partnerbyid($key)[0]->name;
                                        $partner_text = "<span class='custom-btn btn-11 partnearray p-2'>$getpartner_name&nbsp;&nbsp;:&nbsp;&nbsp;$value</span> &nbsp;";
                                        echo $partner_text;
                                      }
                                    
                                    }else{
                                      echo "<span class='bg-warning p-2'>Not&nbsp;Set</span>";
                                    }
                                    ?>
                                </td>
                                <?php /*
                                <td>
                                  <?php
                                    if(!empty($data->category_id) || !is_null($data->category_id)){
                                      $categoryarray = json_decode($data->category_id, true);
                                      foreach($categoryarray as $key => $value){
                                        $getcategory_name = $this->Menu_model->GetCategoriesbydataname($key)[0]->name;
                                        $getcategory_name = str_replace(' ', '&nbsp;', $getcategory_name);
                                        $category_text = "<span class='custom-btn btn-11 categoryarray p-2'>$getcategory_name&nbsp;&nbsp;:&nbsp;&nbsp;$value</span> &nbsp;";
                                        echo $category_text;
                                      }
                                    
                                    }else{
                                      echo "<span class='bg-warning p-2'>Not&nbsp;Set</span>";
                                    }
                                    ?>
                                </td>
                                */?>

                                <td><?= $data->out_station_meeting; ?></td>
                                <td><?= $data->local_station_meeting; ?></td>
                                <td><?php 
                                 if(!is_null($data->twetenty_closure_funnel)){
                                  echo $data->twetenty_closure_funnel;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->potential_funnel_for_fy)){
                                  echo $data->potential_funnel_for_fy;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->to_be_nurtured_for_fy)){
                                  echo $data->to_be_nurtured_for_fy;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->fifity_new_lead_funnel)){
                                  echo $data->fifity_new_lead_funnel;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->anchor_client_meeting)){
                                  echo $data->anchor_client_meeting;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?= $data->start_date; ?></td>
                                <td><?= $data->end_date; ?></td>
                                <td><?= $data->created_at; ?></td>
                                <td><?= $data->by_user; ?></td>
                                <td> <a class="custom-btn btn-11 text-center" href="<?=base_url();?>Menu/target_vs_achievement/<?= $data->id; ?>">View</a> </td>
                              </tr>
                              <?php $i++; endforeach; ?>
                            </tbody>
                          </table>
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
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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