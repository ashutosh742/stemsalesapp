
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Same Status Since Days (Task Count With User) | STEM APP | WebAPP</title>
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
    .card-count-heading{
      color: #ff0022;
    text-decoration: none;
    background-color: yellow;
    padding: 4px 10px;
    }
    .content-header{
        padding: 35px 50px 35px 50px;
    border-radius: 10px;
    background: linear-gradient(145deg, #e2e8ec, #ffffff);
    box-shadow: 5px 5px 15px #D1D9E6, -5px -5px 15px #ffffff;
    }
    html {
  scroll-behavior: smooth;
}
.col.text-center.formcenterData {
    align-items: center;
    justify-content: center;
    display: flex;
}
   
th.text-capitalize:nth-child(1),
td:nth-child(1),
th.text-capitalize:nth-child(2),
td:nth-child(2) {
    position: sticky;
    left: 0;
    color: white;
    z-index: 10;
}

tbody tr td:nth-child(1),
tbody tr td:nth-child(2) {
    background-color: white;
    color: black;
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
                      <h4 class="m-0 premium-color-1" style="color: #ff009b;">🏷️ After RP Meetings Assign Same Status Since (n) Days (Current Financial Year)</h4>
                      <p class="premium-color-2" style="color: #ff0000;">After RP meetings, tasks stay stuck with the same status for (n) days ⏳—causing delays, no progress, and frustration 😓. Quick resolution is needed for smoother workflow 🚀.</p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$edate ?></mark></strong></p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$days_inclusive ?> Days</mark></strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
           
          </div>
        </div>
        <hr>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <!-- Total Plan Meetings Group -->

            <div class="body-content">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                
                <hr>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5><i> 📅 Same Status Since Days (🏢 Task Count With User)</i></h5>
                    <span>
                          📅 Tracks the number of days a task remains in the same status, 🏢 showing the company associated, and 👤 counts tasks assigned to each user, helping monitor performance effectively.
                    </span>
                  </div>
                  <br>
              <div class="table-responsive text-nowrap" style="overflow-x: auto;">
        
              <?php
// sameStatusSinceDaysDatas

// Group by cid + username
$grouped = [];
foreach ($sameStatusSinceDaysDatas as $row) {
    $cid = $row->cid;
    if (!isset($grouped[$cid])) {
        $grouped[$cid] = [
            "compname" => $row->compname,
            "main_bd_name" => $row->main_bd_name,
            "current_status_name" => $row->current_status_name,
            "cluster_manager_name" => $row->cluster_manager_name,
            "pst_name" => $row->pst_name,
            "ash_nae_co_name" => $row->ash_nae_co_name,
            "ash_w_co_name" => $row->ash_w_co_name,
            "ash_s_co_name" => $row->ash_s_co_name,
            "rm_east_co_name" => $row->rm_east_co_name,
            "rm_north_co_name" => $row->rm_north_co_name,
            "acm_co_name" => $row->acm_co_name,
            "days" => $row->days,
            "tasks"    => []
        ];
    }
    $grouped[$cid]["tasks"][$row->task_username][] = "<span class='bg-success p-1'>{$row->task_name} ({$row->total_events})</span>";
}

// Fixed max usernames = 10
$maxUsers = 10;

echo "<table id='example1' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
echo "<thead class='thead-dark'><tr>
    <th>#️⃣</th>
    <th>CID 🆔</th>
    <th>Company 🏢</th>
    <th>MainBD Name 👤</th>
    <th>Current Status 📊</th>
    <th>Same Status Since Days 📅</th>
    <th>Cluster Manager 🧑‍💼</th>
    <th>PST 🧑‍💻</th>
    <th>Assistant Sales Head (NAE) 📝</th>
    <th>Assistant Sales Head (W) ✍️</th>
    <th>Assistant Sales Head (S) 📌</th>
    <th>RM East 🌅</th>
    <th>RM North 🌄</th>
    <th>Assistant Cluster Manager 👥</th>";

    for ($i = 1; $i <= $maxUsers; $i++) {
        echo "<th>Task Username{$i} 👤</th>";
    }
    echo "</tr></thead><tbody>";
$k = 1;
foreach ($grouped as $cid => $info) {

    $r = rand(150, 255);
    $g = rand(150, 255);
    $b = rand(150, 255);
    $backgroundColor = "rgba($r, $g, $b,.2)";

    $hue        = rand(0, 360); // Full color wheel
    $saturation = rand(60, 100); // High saturation for rich colors
    $lightness  = rand(30, 45); // Low lightness for a deeper tone
    $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";


echo "<tr style='background-color: " . $backgroundColor . "; color: " . $backgroundColorNew . " !important;'>";

    echo "<td>{$k}</td>";
    echo "<td> <a style='background-color: " . $backgroundColor . "; color: " . $backgroundColorNew . " !important;' target ='blank' href='".base_url()."Menu/CompanyDetails/{$cid}'>{$cid}</a></td>";
    echo "<td> <a style='background-color: " . $backgroundColor . "; color: " . $backgroundColorNew . " !important;' target ='blank' href='".base_url()."Menu/CompanyDetails/{$cid}'>{$info['compname']}</a></td>";
    echo "<td> {$info['main_bd_name']}</td>";
    echo "<td> {$info['current_status_name']}</td>";
    echo "<td> {$info['days']}</td>";
    echo "<td> {$info['cluster_manager_name']}</td>";
    echo "<td> {$info['pst_name']}</td>";
    echo "<td> {$info['ash_nae_co_name']}</td>";
    echo "<td> {$info['ash_w_co_name']}</td>";
    echo "<td> {$info['ash_s_co_name']}</td>";
    echo "<td> {$info['rm_east_co_name']}</td>";
    echo "<td> {$info['rm_north_co_name']}</td>";
    echo "<td> {$info['acm_co_name']}</td>";

    $userIndex = 1;
    foreach ($info['tasks'] as $username => $tasks) {
        $taskList = implode(" ", $tasks);
       echo "<td><span class='bg-info p-1'><b>{$username}</b></span> ⟶ {$taskList}</td>";
        $userIndex++;
    }

    // Empty columns if less than 10
    for ($i=$userIndex; $i <= $maxUsers; $i++) {
        echo "<td></td>";
    }

    echo "</tr>";
    $k++;
}

echo "</tbody></table>";



?>


                  
                </div>


                <hr />
              </div>
            </div>


          


            </div>
          </div>
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
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');


      </script>
    </body>
</html>
