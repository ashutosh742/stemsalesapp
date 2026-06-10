<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Closing TimeLine Target After Quarter Review Details | STEM APP | WebAPP</title>
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
      .card-bg-1 { background-color: #FFD700!important; } /* Gold */
      .card-bg-2 { background-color: #C0C0C0!important; } /* Silver */
      .card-bg-3 { background-color: #CD7F32!important; } /* Bronze */
      .card-bg-4 { background-color: #4682B4!important; } /* Steel Blue */
      .card-bg-5 { background-color: #556B2F!important; } /* Dark Olive Green */
      .card-bg-6 { background-color: #8B0000!important; } /* Dark Red */
      .card-bg-7 { background-color: #4B0082!important; } /* Indigo */
      .card-bg-8 { background-color: #2E8B57!important; } /* Sea Green */
      .card-bg-9 { background-color: #FF6347!important; } /* Tomato */
      .card-bg-10 { background-color: #9932CC!important; } /* Dark Orchid */
      .card-bg-11 { background-color: #8B4513!important; } /* Saddle Brown */
      .card-bg-12 { background-color: #20B2AA!important; } /* Light Sea Green */
      .card-bg-13 { background-color: #FFDAB9!important; } /* Peach Puff */
      .card-bg-14 { background-color: #6A5ACD!important; } /* Slate Blue */
      .card-bg-15 { background-color: #FF69B4!important; } /* Hot Pink */
      .card-bg-16 { background-color: #00BFFF!important; } /* Deep Sky Blue */
      .text-light {
      color: white !important;
      }
      .text-dark {
      color: black !important;
      }
      .form-container {
      display: flex;
      align-items: center;
      gap: 10px;
      }
      .form-container input, .form-container button {
      padding: 8px;
      border-radius: 4px;
      border: 1px solid #ccc;
      }
      .form-container button {
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      }
      .col-sm-6.text-right-data {
      align-items: right;
      justify-content: right;
      display: flex;
      }
      .row-equal-height {
      display: flex;
      flex-wrap: wrap;
      }
      .row-equal-height > [class*='col-'] {
      display: flex;
      flex-direction: column;
      }
      .card {
      margin-bottom: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
      }
      .card-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center; /* Center content vertically */
      }
      .card-bg-1 { border: 2px solid #D4AF37;  } /* Gold border */
      .card-bg-2 { border: 2px solid #A9A9A9; } /* Silver border */
      .card-bg-3 { border: 2px solid #8B4513; } /* Bronze border */
      .card-bg-4 { border: 2px solid #1E90FF; } /* Steel Blue border */
      .card-bg-5 { border: 2px solid #556B2F; } /* Dark Olive Green border */
      .card-bg-6 { border: 2px solid #800000; } /* Dark Red border */
      .card-bg-7 { border: 2px solid #4B0082; } /* Indigo border */
      .card-bg-8 { border: 2px solid #2E8B57; } /* Sea Green border */
      .card-bg-9 { border: 2px solid #FF6347; } /* Tomato border */
      .card-bg-10 { border: 2px solid #9932CC; } /* Dark Orchid border */
      .card-bg-11 { border: 2px solid #8B4513; } /* Saddle Brown border */
      .card-bg-12 { border: 2px solid #20B2AA; } /* Light Sea Green border */
      .card-bg-13 { border: 2px solid #FFDAB9; } /* Peach Puff border */
      .card-bg-14 { border: 2px solid #6A5ACD; } /* Slate Blue border */
      .card-bg-15 { border: 2px solid #FF69B4; } /* Hot Pink border */
      .card-bg-16 { border: 2px solid #00BFFF; } /* Deep Sky Blue border */
      /* Multiple layer frame styles */
      .frame-layer-1 {
      padding: 5px;
      background: linear-gradient(145deg, #f0f0f0, #d9d9d9);
      border-radius: 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
      margin-bottom: 2px;
      }
      .frame-layer-2 {
      padding: 10px;
      background: linear-gradient(145deg, #e6e6e6, #cccccc);
      border-radius: 10px;
      flex: 1;
      display: flex;
      flex-direction: column;
      margin-bottom: 2px;
      }
      .frame-layer-3 {
      padding: 15px;
      background: linear-gradient(145deg, #f5f5f5, #e0e0e0);
      border-radius: 5px;
      flex: 1;
      display: flex;
      flex-direction: column;
      margin-bottom: 2px;
      }
      .card.text-center{
      align-items: center;
      justify-content: center;
      display: flex;
      }
      .card-group {
      margin-bottom: 20px;
      }
      .card-group-title {
      width: 100%;
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
      font-size: 1.2em;
      }
      @media (min-width: 576px) {
      .card-group {
      display: -ms-flexbox;
      display: unset;
      -ms-flex-flow: row wrap;
      flex-flow: row wrap;
      }
      }
      .row-color-1 { background-color: #ffdddd; }
      .row-color-2 { background-color: #ddffdd; }
      .row-color-3 { background-color: #ddddff; }
      .row-color-4 { background-color: #ffffdd; }
      .row-color-5 { background-color: #ffddff; }
      .row-color-6 { background-color: #d1ffd1; }
      .row-color-7 { background-color: #ffd1d1; }
      .row-color-8 { background-color: #d1d1ff; }
      .row-color-9 { background-color: #ffefd1; }
      .row-color-10 { background-color: #d1ffe7; }
      .row-color-11 { background-color: #ffd1f9; }
      .row-color-12 { background-color: #d1f9ff; }
      .row-color-13 { background-color: #f9d1ff; }
      .row-color-14 { background-color: #d1ffd9; }
      .row-color-15 { background-color: #ffd9d1; }
      .row-color-16 { background-color: #d9ffd1; }
      .row-color-17 { background-color: #d1d9ff; }
      .row-color-18 { background-color: #ffd1d9; }
      .row-color-19 { background-color: #d9d1ff; }
      .row-color-20 { background-color: #ffd1e7; }
      .row-color-21 { background-color: #d1ffe7; }
      .row-color-22 { background-color: #e7d1ff; }
      .row-color-23 { background-color: #d1ffd1; }
      .row-color-24 { background-color: #ffd1ff; }
      .row-color-25 { background-color: #d1e7ff; }
      .row-color-26 { background-color: #ffd1d1; }
      .row-color-27 { background-color: #d1ffd9; }
      .row-color-28 { background-color: #d9d1ff; }
      .row-color-29 { background-color: #ffd9d1; }
      .row-color-30 { background-color: #d1d9ff; }
      .row-color-31 { background-color: #ffd1e7; }
      .row-color-32 { background-color: #e7d1ff; }
      .row-color-33 { background-color: #d1ffd1; }
      .row-color-34 { background-color: #ffd1ff; }
      .row-color-35 { background-color: #d1e7ff; }
      .row-color-36 { background-color: #ffd1d1; }
      .row-color-37 { background-color: #d1ffd9; }
      .row-color-38 { background-color: #d9d1ff; }
      .row-color-39 { background-color: #ffd9d1; }
      .row-color-40 { background-color: #d1d9ff; }
      .row-color-41 { background-color: #ffd1e7; }
      .row-color-42 { background-color: #e7d1ff; }
      .row-color-43 { background-color: #d1ffd1; }
      .row-color-44 { background-color: #ffd1ff; }
      .row-color-45 { background-color: #d1e7ff; }
      .row-color-46 { background-color: #ffd1d1; }
      .row-color-47 { background-color: #d1ffd9; }
      .row-color-48 { background-color: #d9d1ff; }
      .row-color-49 { background-color: #ffd9d1; }
      .row-color-50 { background-color: #d1d9ff; }
      tr{
      font-weight: bold;
      }
      .beautiful-textarea {
      padding: 10px;
      border: 2px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
      font-size: 16px;
      resize: none; /* Prevents resizing */
      outline: none; /* Removes the default focus outline */
      transition: border-color 0.3s, box-shadow 0.3s;
      }
      .beautiful-textarea:focus {
      border-color: #66afe9;
      box-shadow: 0 4px 12px rgba(102, 175, 233, 0.4);
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
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                    <div class="frame-layer-3" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h1 class="m-0 premium-color-1" style="color: #ff009b;">
                                    📅 Closing Timeline Target After Quarter Review Details
                                    </h1>
                                    <br>
                                    <center>
                                    <p class="premium-color-2" style="color: #ff0000; width: 60%;">
                                        📊 This section highlights upcoming funnel transitions identified after the quarterly review. It tracks movements like 🟢 <b>Open → Tentative</b>, 📞 <b>Reachout → Tentative</b>, 📅 <b>Tentative → Closure</b>, and ✅ <b>Positive → Closure</b>. These transitions ensure a structured plan for follow-ups, client engagement, and improved closure outcomes. 🔄
                                    </p>
                                    </center>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>

          </div>
        </div>
        <!-- Main content -->
       
        <hr>
        <section class="content">
          <div class="container-fluid">

           <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <form method="POST" action="<?=base_url()?>/Menu/ClosingTimeLineTargetAfterQuarterReview" style="display: flex">
                        <select id="filterType" name="filterType" class="form-control" style="width: 70%;">
                            <option <?php if($currentQuarter == 'FY'){echo "selected";} ?> value="FY">📅 Whole Financial Year</option>
                            <option <?php if($currentQuarter == 'Q1'){echo "selected";} ?> value="Q1">🟡 Quarter 1 (Apr–Jun)</option>
                            <option <?php if($currentQuarter == 'Q2'){echo "selected";} ?> value="Q2">🟢 Quarter 2 (Jul–Sep)</option>
                            <option <?php if($currentQuarter == 'Q3'){echo "selected";} ?> value="Q3">🔵 Quarter 3 (Oct–Dec)</option>
                            <option <?php if($currentQuarter == 'Q4'){echo "selected";} ?> value="Q4">🔴 Quarter 4 (Jan–Mar)</option>
                        </select>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary p-1">Filter</button>
                    </form>
                </div>
              </div>
<hr>

            <?php 
    // dd($closingTimeline);

              ?>

              <div class="card">

              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example10" class="table table-striped table-bordered" cellspacing="0" width="100%">
                   <thead class="thead-dark">
                       <tr>
                        <th>#</th>
                        <th>🏢 Timeline Name</th>
                        <th>👤 Total</th>
                      </tr>
                      </thead>
                      <tbody>
                        <?php $ftasksc = 1; foreach($closingTimeline as $futuretask): 
                            
                            
                        $r = rand(150, 255);
                        $g = rand(150, 255);
                        $b = rand(150, 255);
                        $backgroundColor = "rgba($r, $g, $b,.2)";

                        $hue        = rand(0, 360); // Full color wheel
                        $saturation = rand(60, 100); // High saturation for rich colors
                        $lightness  = rand(30, 45); // Low lightness for a deeper tone
                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                         $cleanName = ucwords(str_replace('_____', ' → ', $futuretask->timeline_name));

                        ?>
                  
                  <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                          <td><?= $ftasksc; ?></td>
                          <td class="text-capitalize"><?= $cleanName; ?></td>
                           <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url()?>/Menu/ClosingTimeLineTargetAfterQuarterReviewDatas/<?=$currentQuarter?>/<?=$uid;?>/<?= $futuretask->timeline_name; ?>"><?= $futuretask->total; ?></a></td>
                        </tr>
                        <?php $ftasksc++; endforeach; ?>
                      </tbody>
                  </table>
                </div>
              </div>
              </div>







          </div>
      </div>
      </section>
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
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script>
      function Reject(mid,id,val){
             // alert(mid);
             // alert('#exampleModalCenterdata'+mid);
             $('#exampleModalCenterdata').modal('show');
             $('#rejectid').val(id);
         // $('#exampleModalCenterdata'+mid).modal('show');
         // $('#exampleModalCenterdata'+mid+' #rejectid').val(id);
         }
    </script>
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
      $("#example2").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      $("#example3").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
      $("#example4").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');

      $("#example10").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example10_wrapper .col-md-6:eq(0)');

      $("#example15").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example15_wrapper .col-md-6:eq(0)');
      
      
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
      
        cards.forEach(card => {
          const bgColor = window.getComputedStyle(card).backgroundColor;
          const rgb = bgColor.match(/\d+/g);
          const brightness = (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000;
      
          if (brightness > 128) {
            card.classList.add('text-dark');
          } else {
            card.classList.add('text-light');
          }
        });
      });
    </script>
  </body>
</html>