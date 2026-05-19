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
      form.p-3 {
      display: flex;
      width: 75%;
      }
      input.form-control {
      margin: 2px;
      }
      select.form-control {
      margin: 2px;
      }
      button.btn.btn-primary.text-light {
      margin: 3px;
      }
      .card-body {
    background: beige;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php require('nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12 card bg-primary text-center p-2">
                <h1>Review Report</h1>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
         <?php 

         $ctype_id = $user['type_id'];
         $bddata = $this->Menu_model->GetAllUserByAdminRole($ctype_id,$uid);
         ?>
        <center>
          <hr style="width:80%">
          <form class="p-3" method="POST" action="<?=base_url();?>/Menu/ReviewDataReport">
            <input type="date" name="sdate" class="form-control" class="mr-2" value="<?=$sd?>">
            <input type="date" name="edate" class="form-control" class="mr-2" value="<?=$ed?>">
            <select name="pstname" class="form-control" id="adminvalue">
                <option value="">Select</option>
                <?php foreach ($bddata as $userinfo) {
                  if($userinfo->status == 'inactive'){continue;}
                    $userinfotype = $userinfo->type_id;
                    $color = "color:#d90d2b;"; 
                    if ($userinfotype == 2) {
                        $color = "color:navy;";
                    } elseif ($userinfotype == 4) {
                        $color = "color:#ff4500;";
                    } elseif ($userinfotype == 13) {
                        $color = "color:#ff0086;";
                    } elseif ($userinfotype == 3) {
                        $color = "color:darkgreen;";
                    }
                    ?>
                    <option value="<?= $userinfo->user_id; ?>" style="<?= $color; ?>">
                        <?= $userinfo->name; ?> (<?= $userinfo->departname; ?>)
                    </option>
                <?php } ?>
            </select>
            <select name="bdid" id="teamuser" class="form-control" >
            </select>
            &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary text-light">Filter</button>
          </form>
          <hr style="width:80%">
        </center>
        <!-- Main content -->

        <?php // dd($mdata); ?>

        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                      <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead>
                                <tr>
                                <tr>
                                  <!-- <th>Review Report</th>-->
                                  
                                  <th>S.No.</th>
                                  <th>By User</th>
			                            <th>To User</th>
                                  <th>After Review Work</th> 
                                  <th>Review Type</th>
                                  <th>Plan Date</th>
                                  <th>Start Date</th>
                                  <th>Close Date</th>
                                  <th>Review Time</th>
                                  <th>No of Company</th>
                                  <th>Review Report</th>
                                </tr>
                                </tr>
                              </thead>
                              <tbody>
                                <?php $i=1;
                                  foreach($mdata as $md){
                                      $startt=$md->startt;
                                      $closet=$md->closet;

					$byuserid = $md->uid;
					$byuserData= $this->Menu_model->get_userbyid($byuserid);
    				        $byuser = $byuserData[0]->name;


                                     
					$bdname = $md->bdid;
					$bdiduidData = $this->Menu_model->get_userbyid($bdname);
    				        $touser = $bdiduidData[0]->name;


 					if(!is_null($startt)){
                                        $nstartt= date('d-m-Y  h:i A', strtotime($startt));
                              
                                      }else{
 					$nstartt= "<span class='bg-warning p-2'>Not Start</span>";
					}
	

                                      if(!is_null($closet)){
                                        $ncloset = date('d-m-Y  h:i A', strtotime($closet));
                                        $tdiff = $this->Menu_model->timediff($startt,$closet);
                                      }else{
                                        $ncloset = "<span class='bg-warning p-2'>Not Close</span>";
                                        $closetcur = date("Y-m-d H:i:s");
                                        $tdiff = $this->Menu_model->timediff($startt,$closetcur);
                                      }
                                      
                                      ?>
                                <tr>
                                  <td><?=$i?></td>
                                  <td><?=$byuser;?></td>
				  <td><?=$touser; ?></td>
                                  <td><a href="AfterReviewDetailNew/<?=$md->rid?>">Current Scenario</a></td>
                                  <td><?=$md->reviewtype?></td>
                                  <td><?=$md->sdatet?></td>
                                  <td><?=$nstartt?></td>
                                  <td><?=$ncloset?></td>
                                  <td><?=$tdiff;?></td>
                                  <td><?=$md->totalc?></td>
                                  <td><a href="<?=base_url();?>Menu/ReviewDetailNew/<?=$md->rid?>">Check</a></td>
                                </tr>
                                </a>
                                <?php $i++;} ?>
                              </tbody>
                            </table>    
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
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
    <script>
      $.widget.bridge('uibutton', $.ui.button);
      
$( document ).ready(function() {
    $("#teamuser").hide();
});
      $('#adminvalue').change(function() {
        var selectedValue = $(this).val(); // Get the selected value
        $.ajax({
            url:'<?=base_url();?>Menu/GetAdminReviewUser',
            type: "POST",
            data: {
            admin_id: selectedValue
            },
            cache: false,
            success: function a(result){
                $("#teamuser").html(result);
                $("#teamuser").show();
                var optionCount = $('#teamuser').find('option').length;
                optionCount = optionCount-1;
                }
            });
      });
      
      
      
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
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>