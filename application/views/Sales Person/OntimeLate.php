<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
  <?php include 'addpop.php';?>
  <?php include 'popupbox.php';?>
  <!-- /.navbar -->


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Theme Color</h1>
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


    <!-- Main content -->
    

                <section class="content p-3">
                    
                    <button id="grid-view-btn" class="btn btn-primary">Grid View</button>
                            <button id="list-view-btn" class="btn btn-primary">Xls View</button>
                    <div class="container-fluid card p-5" id="data-container">
                        <div class="row text-center" id="grid-view">
                            <?php
                            $gdata = $this->Menu_model->get_olbyu($uid, $sd, $ed, $code);
                            foreach ($gdata as $gd) {
                                ?>
                                <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                                        Action<br><b style="color:<?=$gd->aclr?>"><?=$gd->acname?></b><hr>
                                        Befour Status<br><b style="color:<?=$gd->bsclr?>"><?=$gd->bstatus?></b><hr>
                                        After Status<br><b style="color:<?=$gd->asclr?>"><?=$gd->astatus?></b><hr>
                                        Company Name<br><a href="<?=base_url();?>Menu/CompanyDetails/<?=$gd->cid?>"><b style="color:<?=$gd->pclr?>"><?=$gd->compname?><a></a><br>(<?=$gd->pname?>)</b><hr>
                                        Task By<br><b><?=$gd->uname?></b><hr>
                                        Task Plan<br><b><?=$this->Menu_model->get_dformat($gd->appointmentdatetime)?></b><hr>
                                        Task Inistaed<br><b><?=$this->Menu_model->get_dformat($gd->initiateddt)?></b><br>
                                        Time Diff : <?=$this->Menu_model->timediff($gd->appointmentdatetime,$gd->initiateddt);?><hr>
                                        Task Updated<br><b><?=$this->Menu_model->get_dformat($gd->updateddate)?></b><br>
                                        Time Diff : <?=$this->Menu_model->timediff($gd->initiateddt,$gd->updateddate);?><hr>
                                        Remark <br><b><?=$gd->remarks?></b><hr>
                                        <div class="rounded-circle bg-danger" style="position: absolute;
                                            bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;
                                            bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div id="list-view" style="display: none;">
                            
                            <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Action</th>
                                            <th>Before Status</th>
                                            <th>After Status</th>
                                            <th>Company Name</th>
                                            <th>Partner Type</th>
                                            <th>Task By</th>
                                            <th>Task Plan</th>
                                            <th>Task Initiated</th>
                                            <th>Time Diff</th>
                                            <th>Task Updated</th>
                                            <th>Time Diff</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1;
                            foreach ($gdata as $gd) {
                                ?>
                                
                                        <tr>
                                         <td><?=$i?></td>
                                         <td><b style="color:<?=$gd->aclr?>"><?=$gd->acname?></b></td>
                                         <td><b style="color:<?=$gd->bsclr?>"><?=$gd->bstatus?></b></td>
                                         <td><b style="color:<?=$gd->asclr?>"><?=$gd->astatus?></b></td>
                                         <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$gd->cid?>"><b style="color:<?=$gd->pclr?>"><?=$gd->compname?></b></a></td>
                                         <td><b style="color:<?=$gd->pclr?>"><?=$gd->pname?></b></td>
                                         <td><?=$gd->uname?></td>
                                         <td><?=$this->Menu_model->get_dformat($gd->appointmentdatetime)?></td>
                                         <td><?=$this->Menu_model->get_dformat($gd->initiateddt)?></td>
                                         <td><?=$this->Menu_model->timediff($gd->appointmentdatetime,$gd->initiateddt)?></td>
                                         <td><?=$this->Menu_model->get_dformat($gd->updateddate)?></td>
                                         <td><?=$this->Menu_model->timediff($gd->initiateddt,$gd->updateddate)?></td>
                                         <td><?=$gd->remarks?></td>
                                     </tr>
                                     <?php $i++;} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                            
                            
                            
                            
                            
                            
                        </div>
                    </div>
                </section>

                        <script>
                            document.getElementById("grid-view-btn").addEventListener("click", function () {
                                document.getElementById("grid-view").style.display = "block";
                                document.getElementById("list-view").style.display = "none";
                            });
                        
                            document.getElementById("list-view-btn").addEventListener("click", function () {
                                document.getElementById("grid-view").style.display = "none";
                                document.getElementById("list-view").style.display = "block";
                            });
                        </script>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
<style>

#myInput {
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myUL {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

#myUL li a {
  border: 1px solid #ddd;
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6;
  padding: 12px;
  text-decoration: none;
  font-size: 18px;
  color: black;
  display: block
}

#myUL li a:hover:not(.header) {
  background-color: #eee;
}
</style>


<script>
function myFunction() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
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

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>