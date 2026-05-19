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
     
      body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
      }
      .card-header {
        background-color: #4e73df;
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 1rem;
      }
      .form-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }
      .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
      label {
        font-weight: 600;
        color: #5a5c69;
      }
      select, input[type="submit"] {
        border-radius: 8px;
        padding: 0.75rem;
        border: 1px solid #d1d3e2;
      }
      input[type="submit"] {
        background-color: #4e73df;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 600;
        align-self: flex-start;
      }
      input[type="submit"]:hover {
        background-color: #2e59d9;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
      }
      th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e3e6f0;
      }
      th {
        background-color: #f8f9fa;
        font-weight: 600;
      }
      tr:hover {
        background-color: #f1f3f8;
      }
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
      <style>
          .card{
        background: linear-gradient(to right, #fff8e1, #ffffff); 
        border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;
      }
      </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
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
        <!-- Main content -->
        <section class="content">
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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="text-center m-3">
                    Funnel Transfer Inside BD to Other BD
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <div class="text-center" style="background: lavender;color: #ff0059; font-weight: bold;box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;">
                            <hr>

                            <?php 
                            if(isset($_POST['tpst'])){
                              $select_tpst = $_POST['tpst'];
                              $select_fpst = $_POST['fpst'];
                              }else{
                                $select_tpst = 0;
                                $select_fpst = 0;
                              }
                              
                              ?>


                            <form action="" method="POST">
                            <lable>FROM BD</lable> &nbsp;&nbsp;&nbsp;
                            <Select name="fpst">
                              <option>Select BD</option>
                              <?php foreach($mdata as $md){
                                if($select_fpst == $md->user_id){
                                  $selected_fpst = 'selected';
                                }else{
                                  $selected_fpst = '';
                                }
                                ?>
                              <option <?= $selected_fpst;?> value="<?=$md->user_id?>"><?=$md->name?></option>
                              <?php }?>
                            </Select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <lable> ➡️ &nbsp;&nbsp;&nbsp;&nbsp; TO BD</lable> &nbsp;&nbsp;&nbsp;
                            <Select name="tpst">
                              <option>Select BD</option>
                              <?php foreach($mdata as $md){
                                 if($select_tpst == $md->user_id){
                                  $selected_tpst = 'selected';
                                }else{
                                  $selected_tpst = '';
                                }
                                
                                ?>
                              <option <?=$selected_tpst; ?> value="<?=$md->user_id?>"><?=$md->name?></option>
                              <?php }?>
                            </Select>
                            &nbsp;&nbsp;&nbsp;
                            <input type="submit">
                          </form>
                            <hr>
                          </div>
                          <hr>
                          <fieldset>
                            <div class="table-responsive text-nowrap">
                              <form action="<?=base_url();?>Menu/cbdtf" method="POST">
                                <?php if(isset($_POST['tpst'])){
                                  $tpst = $_POST['tpst'];
                                  $clusters = $this->Menu_model->getClusterByUserId($tpst);
                                  // dd($clusters);
                                  
                                  ?>
                                <input type="hidden" name="topst" value="<?=$tpst?>">
                                <?php } 
                                  if(isset($_POST['fpst'])){
                                    $fpst  = $_POST['fpst'];
                                    $cdata = $this->Menu_model->get_cdbyBD($fpst);
                                    error_reporting(E_ALL);
ini_set('display_errors', 1);
                                    ?>
                                <input type="hidden" name="fopst" value="<?=$fpst?>">
                                <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                    <tr>
                                      <th>Check for Change BD</th>
                                      <th>Select Which Category</th>
                                      <th>Select Which Cluster</th>
                                      <th>CIN</th>
                                      <th>Company Name</th>
                                      <th>Patner Type</th>
                                      <th>BD Name</th>
                                      <th>Status</th>
                                      <th>Address</th>
                                      <th>City</th>
                                      <th>State</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1;
                                        foreach($cdata as $ct){
                                           $pid=$ct->partnerType_id;
                                           $cluster_name = $ct->cluster_name;
                                           $cluster_id = $ct->cluster_id;
                                           $pid = $this->Menu_model->get_partnerbyid($pid);
                                           
                                           $bdid=$ct->mainbd;
                                           $bdid = $this->Menu_model->get_userbyid($bdid);
                                           
                                           if(isset($ct->cstatus)){$sid=$ct->cstatus;
                                           $sid = $this->Menu_model->get_statusbyid($sid); $sname=$sid[0]->name;}else{$sname="";}

                                            $r = rand(150, 255);
                                            $g = rand(150, 255);
                                            $b = rand(150, 255);
                                            $backgroundColor = "rgba($r, $g, $b,.2)";

                                            $hue = rand(0, 360); // Full color wheel
                                            $saturation = rand(60, 100); // High saturation for rich colors
                                            $lightness = rand(30, 45); // Low lightness for a deeper tone
                                            
                                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                        ?>
                                         <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important;font-weight: 500;">
                                        <td><input type="checkbox" name="cid[]" value="<?=$ct->id?>"></td>
                                        <td class="ThisFinancialYear">
                                            <select class="form-control1" name="this_financial_year[]">
                                              <option value="" disabled selected>This Financial Year</option>
                                              <option value="q1_twetenty_closure_funnel">Q1 20 Closure Funnel</option>
                                              <option value="potential_funnel_for_fy">Potential Funnel For FY	</option>
                                              <option value="to_be_nurtured_for_fy">To be Nurtured for FY</option>
                                              <option value="fifity_new_lead_funnel">50 New Lead Funnel</option>
                                            </select>
                                        </td>
                                        <td class="ThisFinancialYear">
                                            <?=$cluster_name;?>
                                            <select name="select_cluster[]" class="form-control">
                                                <option value="" disabled selected>-- Select Cluster --</option>

                                                <?php foreach($clusters as $cluster){ 

                                                    if($cluster->id == $cluster_id){
                                                        $selected = 'selected';   
                                                        $disabled1 = '';
                                                    }else{
                                                        $selected = '';
                                                        $disabled1 = '';
                                                        $disabled1 = 'disabled';
                                                    } 

                                                    if($cluster_id == ''){
                                                        $selected = '';
                                                        $disabled1 = '';
                                                    }
                                                    
                                                  ?>
                                                <option value="<?=$cluster->id;?>" <?=$selected;?> <?=$disabled1;?> ><?=$cluster->clustername;?> (Travel Type : <?= empty($cluster->travel_type) ? 'Not Marked' : $cluster->travel_type; ?>) </option>
                                                
                                                <?php } ?>
                                            </select>
                                            
                                        </td>
                                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_blank" href="<?=base_url();?>/menu/CompanyDetails/<?=$ct->id?>"><?=$ct->id?></a></td>
                                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_blank" href="<?=base_url();?>/menu/CompanyDetails/<?=$ct->id?>"><?=$ct->compname?></a></td>
                                        <td><?=$pid[0]->name?></td>
                                        <td><?=$bdid[0]->name?></td>
                                        <td><?=$sname?></td>
                                        <td><?=$ct->address?></td>
                                        <td><?=$ct->city?></td>
                                        <td><?=$ct->state?></td>
                                      </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                    <div class="text-center" style="background: #c3f6c3;">
                                      <hr>
                                        <input type="submit" class="bg-success" style="width:200px;">
                                      <hr>
                                    </div>
                                <?php }?>
                            </div>
                            </form>            <!--END OF FORM ^^-->
                          </fieldset>
                        </div>
                        <hr />
                      </div>
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
        "responsive": false, "lengthChange": false, "autoWi$dth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>