<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STEM APP| WebAPP</title>
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
    <style>
      .ApprovedStatus{
      padding:6px 10px;
      border-radius:4px;
      }
      .ApprovedStatus.Pending{
      background:orange;
      color:white;
      }
      .ApprovedStatus.Reject{
      background:red;
      color:white;
      }
      .ApprovedStatus.approved{
      background:green;
      color:white;
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
            <div class="row mb-2">
              <div class="col-sm-6">
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
        <section class="content">
          <div class="container-fluid">
            <div class="row p-3">
              <div class="col-sm col-md-12 col-lg-12 m-auto">
                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                      
                    <?php $comcount = sizeof($revDatatar); ?>
                    
                    <input type="hidden" id="compcount" value="<?=$comcount?>">
                    <!--<form  action="<?=base_url();?>Menu/startAnnualReview" method="post">-->
                      <input type="hidden" name="user_id" value="<?=$user['user_id']?>">
                      <div class="card p-2 bg-dark">
                        <center>
                          <h3>Annual Review Report</h3>
                        </center>
                      </div>
                      <div class="was-validated">
                      <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample22" aria-expanded="false" aria-controls="collapseExample">
                        Summary Of Report
                      </button>
                      <div class="collapse" id="collapseExample22">
                          <div class="card card-body">
                              <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                            //   var_dump($getbdteam); exit;
                              ?>
                              <div class="table-responsive">
                                <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th>sr. no</th>
                                      <!--<th>User Name</th>-->
                                      <th>Total Companies</th>
                                      <th>Review Done</th>
                                      <th>Pending Review</th>
                                      <th>Cluster Manager Approve</th>
                                      <th>Cluster Manager Reject</th>
                                      <th>PST Approve</th>
                                      <th>PST Reject</th>
                                      <th>Total Admin Approved</th>
                                      <th>Total Admin Rejected</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $j=1;
                                        // foreach($getbdteam as $t){
                                        $udata = $this->Menu_model->getReviewDetailsByUser($uid,$user['type_id']);
                                        // echo "<pre>";
                                        // print_r($udata);
                                        
                                        
                                    ?>
                                    <tr>
                                      <td><?= $j++;?></td>
                                      <!--<td><?=$t->name?></td>-->
                                      <td><?=$udata['totalcmp'][$uid]?></td>
                                      <td><?=$udata['reviewDone'][$uid]?></td>
                                      <td><?=abs($udata['totalcmp'][$uid] - $udata['reviewDone'][$uid])?></td>
                                      <td><?=$udata['cluster_approve'][$uid]?></td>
                                      <td><?=$udata['cluster_reject'][$uid]?></td>
                                      <td><?=$udata['pst_approve'][$uid]?></td>
                                      <td><?=$udata['pst_reject'][$uid]?></td>
                                      <td><?=$udata['approved'][$uid]?></td>
                                      <td><?=$udata['rejected'][$uid]?></td>
                                    </tr>
                                    <?php //} ?>
                                  </tbody>
                                </table>
                                <!-- Modal -->
            
                              </div>
                          </div>
                        </div>
                        <div class="btn btn-primary col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                          Check Our Annual Company Report: 
                         
                        </div>
                        <div class="collapse show" id="collapseExample">
                          <div class="card card-body">
                            <div class="ApprovedStatus">
                              <!--<h4 class="ApprovedStatus Pending">Changes For Academic year 2024-24</h4> -->
                              <div class="table-responsive">
                                  
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th>sr. no</th>
                                      <th>Company Name</th>
                                      <th>Last Status</th>
                                      <th>Current Status</th>
                                      <th>Will be keeping this company with you?</th>
                                      <th>Remark why to not keeping company</th>
                                      <th>Did you have any face to face or virtual meeting with client?</th>
                                      <th>Cluster Name</th>
                                      <th>Focus And Positive Key Client For FY 2024-25</th>
                                      <th>Focus Lead For this Quarter 1?</th>
                                      <th>Top Spender For this Year?</th>
                                      <th>Is This Upsell Client?</th>
                                      <th> Do You Need Any Intervention or Support From ?</th>
                                      <th>What Support You Required In Detail?</th>
                                      <th> No of school</th>
                                      <th>Expected Revenue</th>
                                      <th>Current year Status</th>
                                      <th>Reachout</th>
                                      <th>Tentative</th>
                                      <th>Positive</th>
                                      <th>Very Positive</th>
                                      <th>Positive nap</th>
                                      <th>Closure</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      
                                <?php $j=1; 
                                // var_dump($revDatatar);
                                   foreach($revDatatar as $a){
                                  $companyStatus = $this->Menu_model->companyStatusInitcall($a->company_id);
                                  $revTarget = $this->Menu_model->companyannualreviewtarget($a->company_id,$uid);
                                //   echo "<pre>";
                                //   print_r($revTarget);
                                //   die;
                                  $cstatus = $companyStatus->cstatus;
                                  $lstatus = $companyStatus->lstatus;
                                  $noofschools = $companyStatus->noofschools;
                                  
                                  $topspender = $companyStatus->topspender;
                                  $upsell_client = $companyStatus->upsell_client;
                                  $keycompany = $companyStatus->keycompany;
                                  $focus_funnel = $companyStatus->focus_funnel;
                                  
                                  $currentStatus = $this->Menu_model->get_statusbyid($cstatus);
                                  $currentStatusname = $currentStatus[0]->name; 
                                  
                                  $lastStatus = $this->Menu_model->get_statusbyid($lstatus);
                                  $lastStatusname = $lastStatus[0]->name; 
                                  
                                  ?>
                                      
                                    <tr>
                                      <td><?= $j; ?></td>
                                      <td>
                                        <a target="_BLANK" href="https://stemapp.in/Menu/AnnualReviewDetails/<?=$a->company_id ?>">
                                        <?=$this->Menu_model->get_cdbyid($a->company_id)[0]->compname .' - '.$a->company_id?> 
                                        </a>
                                      </td>
                                      <td><?= $lastStatusname ?></td>
                                      <td><?= $currentStatusname ?></td>
                                      <td><?= $revTarget->keep_company; ?></td>
                                      <td><?= $revTarget->keepremark; ?></td>
                                      <td><?php if($revTarget->keep_company == 'yes'){echo $revTarget->vmeeting; } ?></td>
                                      <td>
                                        <?php
                                          $revTarget->cluster_id;
                                          if($revTarget->cluster_id != 0){
                                             $clusterData = $this->Menu_model->getClusterByid($revTarget->cluster_id);
                                           echo $clusterData[0]->clustername;
                                          }else{
                                           //   echo "Not Select";
                                           echo $revTarget->cluster_id;
                                          }
                                          ?>
                                      </td>
                                      <td><?php if($revTarget->keep_company == 'yes'){ echo $revTarget->focuspositive;} ?></td>
                                      <td><?php if($revTarget->keep_company == 'yes'){ echo  $revTarget->focusfunnel;} ?></td>
                                      <td><?php if($revTarget->keep_company == 'yes'){ echo $revTarget->topspender;} ?></td>
                                      <td><?php if($revTarget->keep_company == 'yes'){ echo $revTarget->upsell;} ?></td>
                                      <td><?php if($revTarget->keep_company == 'yes'){
                                        $utype = $this->Menu_model->get_userbyid($revTarget->intervention);
                                        echo $utype[0]->name;}
                                        ?></td>
                                      <td><?=  $revTarget->support ?></td>
                                      <td><?=  $revTarget->noofschool ?></td>
                                      <td><?=  $revTarget->revenue ?> ₹ </td>
                                      <td><?= $this->Menu_model->get_statusbyid($revTarget->status)[0]->name ?></td>
                                      <td><?= $revTarget->reachout ?></td>
                                      <td><?= $revTarget->tentative ?></td>
                                      <td><?= $revTarget->positive ?></td>
                                      <td><?= $revTarget->very_positive ?></td>
                                      <td><?= $revTarget->positivenap ?></td>
                                      <td><?= $revTarget->closure ?></td>
                                    </tr>
                                    <?php  $j++; }?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <br>
                          </div>
                          <!-- </div> -->
                        </div>
                           </div>
                    <!--</form>-->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type='text/javascript'></script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
      <!-- </section> -->
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
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
    
            $( document ).ready(function() {
var compcount = $("#compcount").val();

for(var i = 1; i <= compcount; i++)
    {
        var str = 'exampledata'+i+'_wrapper';
    $("#exampledata"+i).DataTable({
        "responsive": true, 
            "lengthChange": false, 
            "autoWidth": true,
            "searching": true,
            "ordering": true,
            "info": false,
            "paging": true,
            "dom": 'Bfrtip', 
            "buttons": [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    }).buttons().container().appendTo('#'+str+' .col-md-6:eq(0)');
    }

});
    
   $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>