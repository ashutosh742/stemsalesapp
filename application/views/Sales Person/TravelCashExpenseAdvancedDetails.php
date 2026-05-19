<?php date_default_timezone_set("Asia/Calcutta"); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Travel Cash Expense Details | STEM APP | WebAPP</title>
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
      .flexdata{
        align-items: center;
    justify-content: center;
    display: flex;
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
                      <center><b>Travel Cash Expense Details</b></center>
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
                    $created_at = $cashData[0]->created_at;
                    $expense    = $cashData[0]->expense;
                    $compname   = $cashData[0]->compname;
                    $expense_remarks = $cashData[0]->expense_remarks;
                    $bills      = $cashData[0]->bills;
                    ?>
                  <div class="card p-4">
                    <table class="table table-striped p-2">
                      <tr class="table-primary">
                        <td><b>Request BY:</b></td>
                        <td><b><?= $reusername; ?></b></td>
                      </tr>
                      <tr class="table-secondary">
                        <td><b>Request Date:</b></td>
                        <td><b><?= $created_at; ?></b></td>
                      </tr>
                      <tr class="table-secondary">
                        <td><b>Meeting Company Name:</b></td>
                        <td><b><?= $compname; ?></b></td>
                      </tr>
                      <tr class="table-success">
                        <td><b>Request Ammount:</b></td>
                        <td><b><?= $expense; ?> <i class="fa fa-inr" aria-hidden="true"></i></b></td>
                      </tr>
                      <tr class="table-warning">
                        <td><b>Request Purpose:</b></td>
                        <td><b><?= $expense_remarks; ?></b></td>
                      </tr>
                      <tr class="table-warning">
                        <td><b>Request Bills:</b></td>
                        <td class="billtd">
                          <?php 
                            $decodedArray = json_decode($bills, true);
                            foreach($decodedArray as $bill){ 
                            $fileUrl = base_url().$bill;
                            $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
                            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            if (@getimagesize($fileUrl)) { ?>
                          <img  onclick="ViewBills(<?= $j ?>,'<?= $fileUrl; ?>')" src="<?= $fileUrl ?>" width="100px" height="100px" class="billesimage" alt="Image">
                          <a target="_BLANK" href="<?= $fileUrl; ?>">View</a>
                          <?php } else {
                            echo 'Image not found or not accessible.';
                            }
                            } elseif ($fileExtension === 'pdf') { ?>
                          <iframe src="<?= $fileUrl ?>" width="100px" height="100px" class="billesimage"></iframe>
                          <a target="_BLANK" href="<?= $fileUrl; ?>">View</a>
                          <?php } else {
                            echo 'The file is neither an image nor a PDF.';
                            }
                            ?>
                          <?php } ?>
                        </td>
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
                       3 => ["label" => "Suspected", "class" => "rejected"],
                     ];
                    
                     foreach ($cashData as $item) {
                          // Cluster Approval
                    $clusterStatus = $statusMap[$item->verify];
                    if($item->verify_by != 0){$verify_by =  $this->Menu_model->get_userbyid($item->verify_by)[0]->name;}else{$verify_by = "<span class='bg-warning p-1'>Pending</span>";}
                    echo '
                    <div class="step">
                    <div class="circle ' . $clusterStatus["class"] . '">' . $clusterStatus["label"] . '</div>
                    <div class="connector ' . ($item->verify === 1 ? 'active' : '') . '"></div>
                    <div class="label">Manager Approval</div>
                    <button class="btn btn-link p-0" data-toggle="collapse" data-target="#clusterDetails" aria-expanded="false">Details</button>
                    <div class="collapse collapse-content text-center" id="clusterDetails">
                    <p><strong>By:</strong> ' .$verify_by  . '</p>
                    <p><strong>Message:</strong> ' . $item->verify_remarks . '</p>
                    <p><strong>Date:</strong> ' . $item->verify_date . '</p>
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
                
                <div class="col-md-12">  
                    <?php
                      $meetids    = $cashData[0]->meetid;
                      $meetData   = $this->Menu_model->GetMeetingDetailsByMeetId($meetids);
                      
                      $id                 = $meetData[0]->id;
                      $storedt            = $meetData[0]->storedt;
                      $meetuser_id        = $meetData[0]->user_id;
                      $cid                = $meetData[0]->cid;
                      $ccid               = $meetData[0]->ccid;
                      $inid               = $meetData[0]->inid;
                      $tid                = $meetData[0]->tid;
                      $company_name       = $meetData[0]->company_name;
                      $cphoto             = $meetData[0]->cphoto;
                      $caddress           = $meetData[0]->caddress;
                      $cpname             = $meetData[0]->cpname;
                      $cpdes              = $meetData[0]->cpdes;
                      $cpno               = $meetData[0]->cpno;
                      $cpemail            = $meetData[0]->cpemail;
                      $startm             = $meetData[0]->startm;
                      $slatitude          = $meetData[0]->slatitude;
                      $slongitude         = $meetData[0]->slongitude;
                      $initiateLat        = $meetData[0]->initiateLat;
                      $initiateLongi      = $meetData[0]->initiateLongi;
                      $initphoto          = $meetData[0]->initphoto;
                      $closem             = $meetData[0]->closem;
                      $clatitude          = $meetData[0]->clatitude;
                      $clongitude         = $meetData[0]->clongitude;
                      $status             = $meetData[0]->status;
                      $location           = $meetData[0]->location;
                      $city               = $meetData[0]->city;
                      $state              = $meetData[0]->state;
                      $type               = $meetData[0]->type;
                      $queans             = $meetData[0]->queans;
                      $mcomment           = $meetData[0]->mcomment;
                      $letmeetingsremarks = $meetData[0]->letmeetingsremarks;
                      $company_as         = $meetData[0]->company_as;
                      $company_descri     = $meetData[0]->company_descri;
                      $potentional_client = $meetData[0]->potentional_client;
                      $approved_status    = $meetData[0]->approved_status;
                      $approved_by        = $meetData[0]->approved_by;
                      $plan_change        = $meetData[0]->plan_change;
                      $org_company_name   = $meetData[0]->org_company_name;
                      $partnerType_id     = $meetData[0]->partnerType_id;
                      $partner_name       = $meetData[0]->partner_name;
                      $mainbd             = $meetData[0]->mainbd;
                      $apst               = $meetData[0]->apst;
                      $clm_id             = $meetData[0]->clm_id;
                      $cstatus            = $meetData[0]->cstatus;
                      $current_status     = $meetData[0]->current_status;
                      $lstatus            = $meetData[0]->lstatus;
                      $last_status        = $meetData[0]->last_status;
                      $actiontype_id      = $meetData[0]->actiontype_id;
                      $task_name          = $meetData[0]->task_name;
                      $actontaken         = $meetData[0]->actontaken;
                      $purpose_achieved   = $meetData[0]->purpose_achieved;
                      $updateddate        = $meetData[0]->updateddate;
                      
                      if($mainbdname !== ''){
                        $mainbdname = $this->Menu_model->get_userbyid($mainbd)[0]->name;
                      }
                      if($apst !== ''){
                        $apstname = $this->Menu_model->get_userbyid($apst)[0]->name;
                      }
                      if($clm_id !== ''){
                        $clm_idname = $this->Menu_model->get_userbyid($clm_id)[0]->name;
                      }
                      $meetuser_name = $this->Menu_model->get_userbyid($meetuser_id)[0]->name;
                      ?>
  
                    <div id="accordion">
                  <div class="card">
                    <div class="card-header" id="headingOne">
                      <h5 class="mb-0">
                        <button class="btn btn-primary" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Meeting Details
                        </button>
                      </h5>
                    </div>
                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                      <div class="card-body">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr class="text-center">
                                <th colspan="2">Company Name - <?= $org_company_name; ?></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th>Meeting Date</th>
                                <th><?= $storedt; ?></th>
                              </tr>
                              <tr>
                                <th>Meeting By</th>
                                <th><?= $meetuser_name; ?></th>
                              </tr>
                              <tr>
                                <th>Meeting Start</th>
                                <th><?= $startm; ?></th>
                              </tr>
                              <tr>
                                <th>Meeting Close</th>
                                <th><?= $closem; ?></th>
                              </tr>
                              <tr>
                                <th>Time in Meeting</th>
                                <th><?= $this->Menu_model->timediff($startm,$closem); ?></th>
                              </tr>
                              <tr>
                                <th>Company As</th>
                                <th><?= $company_as ?></th>
                              </tr>
                              <tr>
                                <th>Company Descriptions</th>
                                <th><?= $company_descri; ?></th>
                              </tr>
                              <?php if($potentional_client !== ''){ ?>
                              <tr>
                                <th>Is this potentional Client</th>
                                <th><?= $potentional_client; ?></th>
                              </tr>
                              <tr>
                                <th class="flexdata" style="height:300px;">Meeting Initiate Location</th>
                                <th>
                                    <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border flexdata">
                                      <a href="<?= base_url().$initphoto ?>" target="_BLANK"> <img src="<?= base_url().$initphoto ?>" class="card img-fluide" height="200px" alt="" style="margin-top: 16px;"></a> &nbsp;&nbsp;&nbsp;
                                      <iframe style="width:100%;height:200px;" src="https://maps.google.com/?q=<?=$initiateLat?>,<?=$initiateLongi?>&t=k&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" style="border:0" allowfullscreen></iframe>
                                    </div>
                                </th>
                              </tr>
                            
                              <tr>
                                <th class="flexdata" style="height:300px;">Meeting Start Location</th>
                                <th>
                                    <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border flexdata">
                                   <a href="<?= base_url().$cphoto ?>" target="_BLANK"> <img src="<?= base_url().$cphoto ?>" class="card img-fluide" height="200px" alt="" style="margin-top: 16px;"></a> &nbsp;&nbsp;&nbsp;
                                  <iframe style="width:100%;height:200px;" src="https://maps.google.com/?q=<?=$slatitude?>,<?=$slongitude?>&t=k&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" style="border:0" allowfullscreen></iframe>
                                </div>
                                </th>
                              </tr>
                              <tr>
                                <th class="flexdata" style="height:300px;">Meeting Close Location</th>
                                <th>
                                    <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border flexdata">
                                  <iframe style="width:100%;height:200px;" src="https://maps.google.com/?q=<?=$clatitude?>,<?=$clongitude?>&t=k&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" style="border:0" allowfullscreen></iframe>
                                </div>
                                </th>
                              </tr>
                              <?php } ?>



                             

                              </tr>
                            </tbody>
                          </table>


                      </div>
                    </div>
                  </div>
               
                </div>



                    

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