<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Praposal Check Task | STEM APP | WebAPP</title>
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
      .card-body.box-profile {
      background: cadetblue;
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
      <?php 
          $reqtaskData          = $this->Menu_model->getTBLTaskByID($req_taskid);
          $appointmentdatetime  = $reqtaskData[0]->appointmentdatetime;
          $actontaken           = $reqtaskData[0]->actontaken;
          $purpose_achieved     = $reqtaskData[0]->purpose_achieved;
          $remarks              = $reqtaskData[0]->remarks;
          $user_id              = $reqtaskData[0]->user_id;
          $actiontype_id        = $reqtaskData[0]->actiontype_id;
          $purpose_id           = $reqtaskData[0]->purpose_id;
          $cid_id               = $reqtaskData[0]->cid_id;
         
          $actiontype_idData    = $this->Menu_model->getTaskAction($actiontype_id);
          $actiontype_idname    = $actiontype_idData[0]->name;

          $get_purposeNameById  = $this->Menu_model->get_purposeNameById($purpose_id);

          $reqCmpData           = $this->Menu_model->get_cmpbyinid($cid_id);
          $getSpecialBdrequest  = $this->Menu_model->GetSpecialBdrequest();
          $compname             = $reqCmpData[0]->compname;
          $budget               = $reqCmpData[0]->budget;
          $address              = $reqCmpData[0]->address;
          $website              = $reqCmpData[0]->website;
          
          $praposalData         = $this->Menu_model->GetPraposalByID($pid);
          $praposaluser         = $praposalData[0]->prauser;
          $proattach            = $praposalData[0]->proattach;
          $sdatet               = $praposalData[0]->sdatet;
          $main                 = $praposalData[0]->main;
          $partner              = $praposalData[0]->partner;
          $noofsc               = $praposalData[0]->noofsc;
          $pbudgetme            = $praposalData[0]->pbudgetme;
         
          ?>
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-12 bg-info text-center p-2">
                <h3 class="m-0">Praposal Check</h3>
                <hr>
                <span><?= '<strong>Company Name : </strong>'.$compname.'<strong>' ?></span> <br>
                <span><?= '<strong>Praposal Submit By : </strong>'.$praposaluser.'<strong> | Praposal Submit Date : </strong>'.$sdatet;?></span>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
        <section class="content">
          <div class="container">

            <div class="row">
              <div class="col-md-12">
                <table class="table table-bordered">
                  <tbody>
                    <tr class="table-primary">
                      <td>Comapny Name</td>
                      <td><?= $compname; ?></td>
                    </tr>
                    <tr class="table-primary">
                      <td>Main</td>
                      <td><?= $main; ?></td>
                    </tr>
                    <tr class="table-secondary">
                      <td>Partner</td>
                      <td><?= $partner; ?></td>
                    </tr>
                    <tr class="table-info">
                      <td>No of school</td>
                      <td><?= $noofsc; ?></td>
                    </tr>
                    <tr class="table-success">
                      <td>Budget </td>
                      <td><?= $pbudgetme; ?></td>
                    </tr>
                    <tr class="table-warning">
                      <td>Praposal Submit By </td>
                      <td><?= $praposaluser; ?></td>
                    </tr>
                    <tr class="table-light">
                      <td>Praposal Submit Date </td>
                      <td><?= $sdatet; ?></td>
                    </tr>
                    <tr class="table-warning">
                      <td>View Praposal  </td>
                      <td> <a href="<?=base_url().$proattach;?>">View</a>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
                <div class="row">
                  <div class="col-md-12 text-center">
                  <?php 
                  $fullfilepath = base_url().$proattach;
                  // $fullfilepath = 'https://stemapp.in/uploads/proposal/DIY_Proposal_for_Ambuja_cement_foundation,_Raigarh.pdf';
                  $fileExtension = strtolower(pathinfo($fullfilepath, PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) { ?>
                        <img src='<?= $fullfilepath; ?>' alt='Image Preview' style='max-width:100%; height:auto;'>
                  <?php  } elseif ($fileExtension === 'pdf') { ?>
                    <iframe src='<?= $fullfilepath; ?>' style='width:100%; height:500px;' frameborder='0'></iframe>
                  <?php } else {
                          echo "<h3>Unsupported File Type</h3>";
                          echo "<p>The file $file cannot be previewed.</p>"; 
                    } ?>
                  </div>
                </div>

<br>
                <div class="card text-center" style="padding: 10px;background: darkseagreen;">
                  <form method="post" action="<?=base_url();?>Menu/PraposalApprovedOrRejectByManager">
                    <div class="text-center">
                    <input type="hidden" class="form-control" name="pid" Value="<?=$pid;?>">
                      <input type="hidden" class="form-control" name="req_taskid" value="<?=$req_taskid;?>">
                      <br>
                        <center><textarea class="form-control"  cols="80" rows="3" name="remarks" placeholder="Add Your Remarks" style="width:50%"></textarea></center> 
                        <br>
                      <div class="text-center">
                      <button class="btn btn-primary m-3" name="btntext" value="Approved" type="submit">Approved</button>
                      <button class="btn btn-danger m-3" name="btntext" value="Reject" type="submit">Reject</button>
                      </div>
                    </div>
                  </form>


                </div>




         </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type='text/javascript'>
        </script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
      </section>
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
    <!-- AdminLTE App -->
    <script src="<?=base_url();?>assets/js/adminlte.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?=base_url();?>assets/js/dashboard.js"></script>
  </body>
</html>