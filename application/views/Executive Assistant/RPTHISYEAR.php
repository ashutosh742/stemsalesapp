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
            <h1 class="m-0">This Year RP Meeting</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
<form class="p-3" method="POST" action="<?=base_url();?>/Menu/RPTHISYEAR">
    <select name="bdid">
        <option value="0">All</option>
        <?php $bd = $this->Menu_model->get_userbyaid($uid);
        foreach($bd as $bd){
        ?>
        <option value="<?=$bd->user_id?>"><?=$bd->name?></option>
        <?php } ?>
        <?php $bd = $this->Menu_model->get_userbyaid($uid);
        foreach($bd as $b){if($b->type_id==9){
        ?>
        <option value="<?=$b->user_id?>"><?=$b->name?></option>
        <?php }} ?>
    </select>
    <button type="submit" class="bg-primary text-light">Filter</button>
</form>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <fieldset>
                
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <div class="pdf-viwer">
                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>PST Name</th>
                                            <th>Company ID</th>
                                            <th>Company Name</th>
                                            <th>Current Status</th>
                                            <th>PST Assign Date</th>
                                            <th>Total Logs</th>
                                            <th>Task Detail</th>
                                            <th>No of School</th>
                                            <th>State</th>	
                                            <th>City</th>	
                                            <th>Draft</th>	
                                            <th>Budget</th>	
                                            <th>Address</th>	
                                            <th>Website</th>
                                            <th>Partner Type</th>
                                            <th>Focus Funnel</th>
                                            <th>Upsell Client</th>
                                            <th>Key Client</th>
                                            <th>Positive Key Client</th>
                                            <th>Priority Client</th>
                                            <th>Contact Person</th>	
                                            <th>Contact Number</th>	
                                            <th>Email</th>	
                                            <th>Designation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=1;
                                        $sdate = "2023-04-01";
                                        $edate = date('Y-m-d');
                                        $outask = 0;
                                        if(isset($_POST["bdid"])){
                                        $bdid = $_POST["bdid"];
                                        $mdata=$this->Menu_model->get_rpthisyear($bdid,$uid);
                                        foreach($mdata as $dt){
                                            $cid = $dt->cmpid_id;
                                            $ciid = $dt->ciid;
                                            $userid = $dt->userid;
                                            $apst = $dt->apst;
                                            $patassign = $dt->abc;
                                            $patassign = new DateTime($patassign);
                                            $patassign = $patassign->format('Y-m-d');
                                            $bd=$this->Menu_model->get_userbyid($userid);
                                            
                                            if($apst){$apst=$this->Menu_model->get_userbyid($apst);$apst=$apst[0]->name;}else{$apst='';}
                                            
                                            
                                            
                                            
                                            $ccd=$this->Menu_model->get_ccdbyid($cid);
                                            if($ccd){$contactperson=$ccd[0]->contactperson;$phoneno=$ccd[0]->phoneno;$emailid=$ccd[0]->emailid;$designation=$ccd[0]->designation;}
                                            else{$contactperson="";$phoneno="";$emailid="";$designation="";}
                                            $sid = $dt->cstatus;
                                            if($sid){$sid=$this->Menu_model->get_statusbyid($sid);$sid=$sid[0]->name;}
                                            else{$sid='';}
                                            $tblc=$this->Menu_model->get_tasklogs($ciid,$sdate,$edate,$outask);
                                            $logs = sizeof($tblc);
                                        ?>
                                        <tr>
                                         <td><?=$i++?></td>
                                         <td><?=$bd[0]->name?></td>
                                         <td><?=$apst?></td>
                                         <td><?=$cid?></td>
                                         <td><a href="CompanyDetails/<?=$cid?>"><?=$dt->compname?></a></td>
                                         <td><?=$sid?></td>
                                         <td><?=$patassign?></td>
                                         <td><?=$logs?></td>
                                         <td>
                                             <?php $olddate='';$olds='';foreach($tblc as $cd){
                                                $updateddate = $cd->updateddate;
                                                if($olddate==''){$olddate=$sdate;}
                                                $timed = $this->Menu_model->timediff($updateddate, $olddate);
                                                ?>
                                            <b><?=$cd->aname?></b> Updated by <b><?=$cd->umane?></b> on <?=$cd->updateddate?> <b><?=$timed?></b> <?=$olds?> to <?=$cd->sname?> <br><b>Remark:</b> <?=$cd->mom?><?=$cd->remarks?><hr>
                                            <?php $olddate = $cd->updateddate; $olds=$cd->sname; }?>
                                         </td>
                                         <td><?=$dt->noofschools?></td>
                                         <td><?=$dt->state?></td>
                                         <td><?=$dt->city?></td>
                                         <td><?=$dt->draft?></td>
                                         <td><?=$dt->budget?></td>
                                         <td><?=$dt->address?></td>
                                         <td><?=$dt->website?></td>
                                         <td><?php if(isset($dt->partnerType_id)){
                                         $pid = $dt->partnerType_id;
                                         $pid = $this->Menu_model->get_partnerbyid($pid);
                                         echo $pid[0]->name;
                                         }?>
                                         </td>
                                         <td><?=$dt->focus_funnel;?></td>
                                         <td><?=$dt->upsell_client;?></td>
                                         <td><?=$dt->keycompany;?></td>
                                         <td><?=$dt->pkclient;?></td>
                                         <td><?=$dt->priorityc;?></td>
                                         <td><?=$contactperson?></td>
                                         <td><?=$phoneno?></td>
                                         <td><?=$emailid?></td>
                                         <td><?=$designation?></td>
                                        </tr>
                                        <?php }} ?>
                                    </tbody>
                                  </tbody>
                                </table> 
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
                </fieldset>
            
        </div>
    </div></div></div></div>
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