<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Task Check Live | STEM APP | WebAPP</title>
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
      /* th.text-capitalize:nth-child(1),
      td:nth-child(1),
      th.text-capitalize:nth-child(3),
      td:nth-child(3) {
      position: sticky;
      left: 0;
      color: white;
      z-index: 10;
      }
      tbody tr td:nth-child(1),
      tbody tr td:nth-child(3) {
      background-color: white;
      color: black;
      } */

        .custom-btn {
            /* width: 130px;
            height: 40px; */
            color: #fff;
            border-radius: 5px;
            /* padding: 10px 25px; */
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
      
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
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
        <br>
        <section class="content" >
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card" style="background: linear-gradient(to right, #ede7f6, #ede7f6); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">✅ Task Check Live 🌞</h3>
                    <h6 class="text-center m-3"><?=$sdate?></h6>
                  </div>
                  <hr>
                  <?php if($user['type_id'] != 3): ?>
                  <div class="row">
                    <hr>
                    <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 60%;">
                      <?php 
                        $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                        $taskActions      = $this->Menu_model->get_action();
                        $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                        ?>
                      <div class="col text-center formcenterData">
                        <div>
                          <hr>
                          <form action="<?=base_url()?>Menu/TaskCheckLive" method="post" class="mt-3">
                            <div class="row mb-4">
                              <div class="col">
                                <label for="selectedDate">Date</label>
                                <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=$sdate;?>" name="sdate" style="width: 250px;" class="form-control">
                              </div>
                              <!--  <div class="col">
                                <label for="selectedDate">End Date</label>
                                <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" style="width: 200px;" max="<?=date('Y-m-d')?>" class="form-control">
                                </div> -->
                              <div class="col">
                                <label for="selectedDate">Select Admin</label>
                                <select id="admin_id_filter" name="admin_id_filter" class="form-control">
                                  <?php if($user['type_id'] == 2){ ?> 
                                  <option value="all">All</option>
                                  <?php } ?>
                                  <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                                  <option value="<?= $clusterPstData->user_id; ?>" <?= ($clusterPstData->user_id == $admin_id_filter) ? 'selected' : ''; ?>>
                                    <?= $clusterPstData->name; ?>
                                  </option>
                                  <?php } ?>
                                </select>
                              </div>
                              <div class="col">
                                <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                                <label for="selectedDate">Select RM</label>
                                <select id="rm_filter" name="rm_filter" class="form-control">
                                  <?php if($user['type_id'] !== 3){ ?> 
                                  <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                  <?php } ?>
                                  <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                                  <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                                    <?= $getTeamsData->name; ?>
                                  </option>
                                  <?php }?>
                                </select>
                              </div>
                              <div class="col text-center">
                                <div class="form-group">
                                  <button type="submit" class="btn btn-primary p-1" style="margin-top: 33px; width: 230px;">Filter</button>
                                </div>
                              </div>
                            </div>
                          </form>
                          <hr>
                        </div>
                      </div>
                    </div>
                    <hr>
                  </div>
                  <!-- /.card-header -->
                  <?php endif; ?>

                  <hr>
                  <div class="card-body">

                   <div class="row mb-2">
                        <div class="col-md-12 text-center">
                            <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
                        </div>
                    </div>

                    <!-- <hr> -->
                   <!-- <div class="row mb-2">
                        <div class="col-md-12 text-center">
                            <a target="_BLANK" class="custom-btn btn-11 partnearray p-2" href="<?=base_url()?>Reports/TaskCheckingManagementStarRating">View Full Details</a>
                        </div>
                    </div> -->
                  
                  
                  <?php  
                //   dd($resultDatas);
                  function sortByStatus($a, $b) {
                        if ($a->status == $b->status) {
                            return 0;
                        }
                        return ($a->status == "complete") ? -1 : 1;
                    }

                    // Sort array
                    usort($resultDatas, "sortByStatus");
                  ?>

                    <hr>
                    <div class="row" style="background: azure; padding: 25px; border-radius: 20px;">

                    <?php $k=1; foreach($resultDatas as $resultData): ?>
                      <div class="col-md-4">
                        <div class="card" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">

                             <?php
                              $stylecolor = '';
                             if($resultData->status == 'complete'){ 
                                   $badgeprit = "badge badge-pill badge-success p-2";
                                   $textmessage = "text-success";
                                   $stylecolor = "style='background-color: #e41351;color:white;'";
                                 }else if($resultData->status == 'pending'){ 
                                     $badgeprit = "badge badge-pill p-2";
                                     $textmessage = "text-warning";
                                     $stylecolor = "style='background-color: #e41351;color:white;'";
                                  }  ?>

                            <h5 class="badge badge-pill p-2" <?=$stylecolor?>><?=$k;?></h5>
                            <h5 class="<?=$textmessage?>"><?=$resultData->name;?></h5>
                            <hr style="width: 50px;">

                              <?php if($resultData->status == 'complete'){ ?> 
                                    <span class="bg-success p-1">Complete</span>
                                <?php }else if($resultData->status == 'pending'){ ?>
                                     <span class="bg-warning p-1">Pending</span>
                                <?php }  ?>
                                 <hr style="width: 50px;">

                            <?php if($resultData->status == 'complete'){
                                
       
                                $specialRemakrsDatas   = $this->Report_model->TaskCheckingManagementStarRatingDetailsLists($resultData->user_id,$sdate,$sdate,'approved_by');
                                $sessiontime   = $this->Report_model->GetSeesionTimeTakeBYSConTaskCheckBySingleUser($resultData->user_id,$sdate,$sdate,'approved_by');
                               
                               
                                $totalStarsDatas        = $specialRemakrsDatas['totalStarsDatas'];
                                $totalStarsDatasByDate  = $specialRemakrsDatas['totalStarsDatasByDate'];
                                $totalStarsDatasQuestionByDate  = $specialRemakrsDatas['totalStarsDatasQuestionByDate'];
                                // echo "<pre>";
                                // print_r($totalStarsDatas);

                                ?>

                                 <div tyle="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                                        <div class="card-body-content">
                                            <p class="card-text <?=$textmessage?>">
                                                <strong>✮ Total Stars:</strong> <span class="<?=$badgeprit?>"><?= $totalStarsDatas[0]->total_stars;?></span>
                                            </p>
                                        </div>
                                    </div>
                                <hr style="width: 50px;">
                                 <a target="_BLANK" class="bg-success p-1" href="<?=base_url()?>Reports/TaskCheckingManagementStarRatingDetails/<?= $resultData->user_id;?>/<?=$sdate?>/<?=$sdate?>/approved_by">
                                    View Details
                                 </a>
                              <?php }  ?>
                    
                          </div>
                        </div>
                      </div>
                    <?php $k++; endforeach; ?>
                    </div>
                    <hr>
                
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
   


      $(document).ready(function () {
        $('#downloadPdf').click(function () {
          const button = $(this);
          button.prop('disabled', true).text('Please wait...');

          const { jsPDF } = window.jspdf;
          const doc = new jsPDF('p', 'mm', 'a4');
          const content = document.querySelector('.content-wrapper');

          html2canvas(content, {
            scale: 2,
          }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = canvas.height * imgWidth / canvas.width;

            let heightLeft = imgHeight;
            let position = 0;

            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft > 0) {
              position -= pageHeight;
              doc.addPage();
              doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
              heightLeft -= pageHeight;
            }

            doc.save('dashboard.pdf');
            // Revert button
            button.prop('disabled', false).text('Download PDF');
          }).catch(error => {
            console.error('PDF generation failed:', error);
            button.prop('disabled', false).text('Download PDF');
          });
        });
      });
    </script>
  </body>
</html>