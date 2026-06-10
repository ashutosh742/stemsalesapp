<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Program TimeLine Details | STEM APP | WebAPP</title>
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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">🏷️ All Program TimeLine Details </h3>
                    <center><span>Track key program milestones, scheduled activities, and completion dates in one place.</span></center>
                    <hr style="width:25%;">
         
                 
                      <div class="table-responsive text-nowrap">
                   <table id="example1" class="table table-striped">
        <thead class="thead-dark">
          <tr>
            <th scope="col">#️⃣</th>
            <th scope="col">📚 Academic Year</th>
            <th scope="col">🆔 Project Code</th>
            <th scope="col">👋 Welcome Message</th>
            <th scope="col">✉️ 1st 5 Communication</th>
            <th scope="col">✉️ 2nd 5 Communication</th>
            <th scope="col">✉️ 3rd 5 Communication</th>
            <th scope="col">📞 1st 5 Calls for Utilisation</th>
            <th scope="col">📞 2nd 5 Calls for Utilisation</th>
            <th scope="col">📑 Report Type</th>
            <th scope="col">📌 FTTP</th>
            <th scope="col">📌 RTTP</th>
            <th scope="col">♻️ Replacement</th>
            <th scope="col">🛠️ Maintenance</th>
            <th scope="col">📊 Base Line M&E</th>
            <th scope="col">📊 End Line M&E</th>
            <th scope="col">🔎 NSP</th>
            <th scope="col">📞 1st 5 Utilisation</th>
            <th scope="col">📞 2nd 5 Utilisation</th>
            <th scope="col">📞 3rd 5 Utilisation</th>
            <th scope="col">📞 Other Department Call</th>
            <th scope="col">📤 1st 4 Outbound Communication</th>
            <th scope="col">📤 2nd 4 Outbound Communication</th>
            <th scope="col">📤 3rd 4 Outbound Communication</th>
            <th scope="col">🤝 Review with BD</th>
            <th scope="col">📖 Case Study</th>
            <th scope="col">🧩 DIY</th>
            <th scope="col">👥 Client Engagement</th>
            <th scope="col">📌 Expected Status</th>
            <th scope="col">🗓️ Expected Status Date</th>
            <th scope="col">👨‍💼 ZM Visit 10% each</th>
            <th scope="col">👩‍💼 PM Visit 10% each</th>
            <th scope="col">☀️ Summer Activity</th>
            <th scope="col">❄️ Winter Activity</th>
            <th scope="col">💻 Online Activity</th>
            <th scope="col">🎤 Webinar</th>
            <th scope="col">📢 Social Media Post 1</th>
            <th scope="col">📢 Social Media Post 2</th>
            <th scope="col">📢 Social Media Post 3</th>
            <th scope="col">📢 Social Media Post 4</th>
            <th scope="col">🗓️ Create Date</th>
            <th scope="col">🔍 View Details</th>
            <th scope="col">🔍 View Program Delevery Tracker</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $i = 1; 
            foreach($gettimeline as $pdata) {
              $r = rand(150, 255);
              $g = rand(150, 255);
              $b = rand(150, 255);
              $backgroundColor = "rgba($r, $g, $b,.2)";
            
              $hue        = rand(0, 360); // Full color wheel
              $saturation = rand(60, 100); // High saturation for rich colors
              $lightness  = rand(30, 45); // Low lightness for a deeper tone
              $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
            ?>
          <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
<td><?= $i ?? '' ?></td>
<td><?= $pdata->academic_year ?? '' ?></td>
<td>
    <?= $pdata->projectcode ?? '' ?>
</td>
<td><?= $pdata->wmessage ?? '' ?></td>
<td><?= $pdata->communication1 ?? '' ?></td>
<td><?= $pdata->communication2 ?? '' ?></td>
<td><?= $pdata->communication3 ?? '' ?></td>
<td><?= $pdata->callsfu1 ?? '' ?></td>
<td><?= $pdata->callsfu2 ?? '' ?></td>
<td>
<?php
  if(isset($pdata->reporttype)){
      if($pdata->reporttype == 8) echo "Monthly";
      elseif($pdata->reporttype == 4) echo "Quarterly";
      elseif($pdata->reporttype == 1) echo "Yearly";
  }
?>
</td>
<td><?= $pdata->fttp ?? '' ?></td>
<td><?= $pdata->rttp ?? '' ?></td>
<td><?= $pdata->replacement ?? '' ?></td>
<td><?= $pdata->maintenance ?? '' ?></td>
<td><?= $pdata->blmne ?? '' ?></td>
<td><?= $pdata->elmne ?? '' ?></td>
<td><?= $pdata->nsp ?? '' ?></td>
<td><?= $pdata->utilisation1 ?? '' ?></td>
<td><?= $pdata->utilisation2 ?? '' ?></td>
<td><?= $pdata->utilisation3 ?? '' ?></td>
<td><?= $pdata->otherdcall ?? '' ?></td>
<td><?= $pdata->outbondc1 ?? '' ?></td>
<td><?= $pdata->outbondc2 ?? '' ?></td>
<td><?= $pdata->outbondc3 ?? '' ?></td>
<td><?= $pdata->bdreview ?? '' ?></td>
<td><?= $pdata->casestudy ?? '' ?></td>
<td><?= $pdata->diy ?? '' ?></td>
<td><?= $pdata->cengagement ?? '' ?></td>
<td><?= $pdata->status ?? '' ?></td>
<td><?= $pdata->exstatusdt ?? '' ?></td>
<td><?= $pdata->zmvisit ?? '' ?></td>
<td><?= $pdata->pmvisit ?? '' ?></td>
<td><?= $pdata->summeractivity ?? '' ?></td>
<td><?= $pdata->winteractivity ?? '' ?></td>
<td><?= $pdata->onlineactivity ?? '' ?></td>
<td><?= $pdata->webinar ?? '' ?></td>
<td><?= $pdata->socialMediaPost1 ?? '' ?></td>
<td><?= $pdata->socialMediaPost2 ?? '' ?></td>
<td><?= $pdata->socialMediaPost3 ?? '' ?></td>
<td><?= $pdata->socialMediaPost4 ?? '' ?></td>
<td><?= $pdata->created_at ?? '' ?></td>
<td>
  <a target="_BLANK" href="<?= 'https://stemoppapp.in/Menu/ViewProgramTimelineData/'.$pdata->id.'/'.$user['user_id'] ?>">
    <span class="p-1 bg-success text-white">view</span>
  </a>
</td>
<td>
  <a target="_BLANK" href="<?= 'https://stemoppapp.in/Menu/ProgramDeleveryTracker/'.$pdata->id.'/'.$user['user_id']?>">
    <span class="p-1 bg-success text-white">view Tracker</span>
  </a>
</td>

          </tr>
          <?php 
            $i++; 
            } 
            ?>
        </tbody>
      </table>
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