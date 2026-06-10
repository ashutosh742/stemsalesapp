<?php date_default_timezone_set("Asia/Calcutta"); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Travel Cash Advanced Details | STEM APP | WebAPP</title>
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
      img.billesimage {
      padding: 5px;
      border: 1px solid cadetblue;
      box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
      padding: 3px;
      margin: 4px;
      }
      .cat1{background:#effaf8!important}.cat2{background:#fef2f4!important}.cat3{background:#fffaef!important}.cat4{background:#eefbf5!important}.cat5{background:#f7f3ff!important}.cat6{background:#fff7ef!important}.cat7{background:#f1fbff!important}.cat8{background:#fff0f8!important}
      td.billtd {
      display: flex;
      }
      td.billtd a{
      padding: 4px;
      }
      .progress-vertical {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 30px;
      position: relative;
    }
    .circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 14px;
      font-weight: bold;
      color: white;
    }
    .pending {
      background-color: #ffc107;
    }
    .approved {
      background-color: #28a745;
    }
    .rejected {
      background-color: #dc3545;
    }
    .connector {
      width: 4px;
      background-color: #ddd;
      height: 40px;
    }
    .connector.active {
      background-color: #28a745;
    }
    .step .label {
      margin-top: 10px;
      text-align: center;
      font-size: 14px;
      font-weight: bold;
    }
    .step .collapse-content {
      text-align: left;
      margin-top: 10px;
      width: 100%;
    }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <div class="content-header">
          <div class="container-fluid">
            <?php
              if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
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
          </div>
        </div>
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header bg-info">
                    <h3 class="text-center">
                      <center><b>Travel Cash Advanced Details</b></center>
                    </h3>
                  </div>
                </div>
              </div>
            </div>
            <div class="container card p-2">
               <div class="row">
                <div class="col-md-6" style="align-items: center; justify-content: center; display: flex ;">
                    <?php 
                    $reusername = $this->Menu_model->get_userbyid($cashData[0]->user_id)[0]->name;
                    $reqdate = $cashData[0]->date;
                    $reqcash = $cashData[0]->cash;
                    $purpose = $cashData[0]->purpose;
                    ?>
                    <div class="card p-4">
                        <table class="table table-striped p-2">
                            <tr class="table-primary">
                                <td><b>Request BY:</b></td>
                                <td><b><?= $reusername; ?></b></td>
                            </tr>
                            <tr class="table-secondary">
                                <td><b>Request Date:</b></td>
                                <td><b><?= $reqdate; ?></b></td>
                            </tr>
                            <tr class="table-success">
                                <td><b>Request Ammount:</b></td>
                                <td><b><?= $reqcash; ?> <i class="fa fa-inr" aria-hidden="true"></i></b></td>
                            </tr>
                            <tr class="table-warning">
                                <td><b>Request Purpose:</b></td>
                                <td><b><?= $purpose; ?></b></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                <?php 
                 
                 $statusMap = [
                    0 => ["label" => "Pending", "class" => "pending"],
                    1 => ["label" => "Approved", "class" => "approved"],
                    2 => ["label" => "Rejected", "class" => "rejected"],
                    3 => ["label" => "Suspect", "class" => "pending"],
                  ];
                
                  foreach ($cashData as $item) {
                       // Cluster Approval
                $clusterStatus = $statusMap[$item->cluster_apr];
                if($item->cluster_by != 0){$cluster_by =  $this->Menu_model->get_userbyid($item->cluster_by)[0]->name;}else{$cluster_by = "<span class='bg-warning p-1'>Pending</span>";}
                echo '
                  <div class="step">
                    <div class="circle ' . $clusterStatus["class"] . '">' . $clusterStatus["label"] . '</div>
                    <div class="connector ' . ($item->cluster_apr === 1 ? 'active' : '') . '"></div>
                    <div class="label">Manager Approval</div>
                    <button class="btn btn-link p-0" data-toggle="collapse" data-target="#clusterDetails" aria-expanded="false">Details</button>
                    <div class="collapse collapse-content text-center" id="clusterDetails">
                      <p><strong>By:</strong> ' .$cluster_by  . '</p>
                      <p><strong>Message:</strong> ' . $item->cluster_msg . '</p>
                      <p><strong>Date:</strong> ' . $item->cluster_date . '</p>
                    </div>
                  </div>
                ';
        // Admin Approval
        $adminStatus = $statusMap[$item->admin_apr];
        if($item->admin_by != 0){$admin_by =  $this->Menu_model->get_userbyid($item->admin_by)[0]->name;}else{$admin_by = "<span class='bg-warning p-1'>Pending</span>";}
        echo '
          <div class="step">
            <div class="circle ' . $adminStatus["class"] . '">' . $adminStatus["label"] . '</div>
            <div class="connector ' . ($item->admin_apr === 1 ? 'active' : '') . '"></div>
            <div class="label">Admin Approval</div>
            <button class="btn btn-link p-0" data-toggle="collapse" data-target="#adminDetails" aria-expanded="false">Details</button>
            <div class="collapse collapse-content text-center" id="adminDetails">
              <p><strong> By:</strong> ' . $admin_by . '</p>
              <p><strong>Message:</strong> ' . $item->admin_msg . '</p>
              <p><strong>Date:</strong> ' . $item->admin_date . '</p>
            </div>
          </div>
        ';
        // Account Approval
        $accountStatus = $statusMap[$item->account_apr];
        if($item->account_by != 0){$account_by = $item->account_by;}else{$account_by = "<span class='bg-warning p-1'>Pending</span>";}
        echo '
          <div class="step">
            <div class="circle ' . $accountStatus["class"] . '">' . $accountStatus["label"] . '</div>
            <div class="label">Account Approval</div>
            <button class="btn btn-link p-0" data-toggle="collapse" data-target="#accountDetails" aria-expanded="false">Details</button>
            <div class="collapse collapse-content text-center" id="accountDetails">
              <p><strong> By:</strong> ' . $account_by . '</p>
              <p><strong>Message:</strong> ' . ($item->account_msg ?: "N/A") . '</p>
              <p><strong>Date:</strong> ' . $item->account_date . '</p>
            </div>
          </div>
        ';
      }
      ?>
                </div>
               </div>
            </div>
          </div>
      </div>
    </div>
    </div>
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'>
      function ViewBills(rid,fileUrl){
        var fileExtension = fileUrl.split('.').pop().toLowerCase(); 
        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            $('#viewbillimage').attr('src', fileUrl).show();
            $('#viewbillimageatag').attr('href', fileUrl).show();
        } else if (fileExtension === 'pdf') {
            $('#pdfViewer').attr('src', fileUrl).show();
            $('#viewbillimageatag').attr('href', fileUrl).show();
        } else {
            alert('The file is neither an image nor a PDF.');
        }
        $('#exampleModalCenter').modal('show');
      }
      
      function Reject(mid,id,val){
      $('#exampleModalCenterdata').modal('show');
      $('#rejectid').val(id);
      }
       function Suspected(mid,id,val){
      $('#exampleModalCenterdata1').modal('show');
      $('#suspectedid').val(id);
      }
      
      
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
      $("#exampledata").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": [ "excel", "pdf"]
      }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>