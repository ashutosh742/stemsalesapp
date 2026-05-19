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
<h1 class="m-0"></h1>
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
<section class="content">
<div class="container-fluid">
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<h3 class="text-center m-3 text-secondary">BD Funnel Detail Acording to Categories</h3>
<form action="CategoryStatusPage" method="POST">
    <select name="categories">
        <option value="upsell_client">Upsell Client</option>
        <option value="focus_funnel">Focus Funnel</option>
        <option value="keycompany">Key Client</option>
        <option value="pkclient">Positive Key Client</option>
        <option value="priorityc">Priority Client</option>
        <option value="topspender">Top Spender</option>
    </select>
    <input type="submit">
</form>
</div>
<!-- /.card-header -->
<div class="card-body">
<div class="container body-content">
    <div class="page-header">
        <div class="form-group">
            <fieldset>
                <?php if(isset($_POST["categories"])){
                  $categories = $_POST["categories"];
                  
                  ?>
                <form action="" class="form-group" method="post">
                    <div class="table-responsive">
                      <div class="text-center">
                        <h3><?=  $categories ?></h3>
                      </div>
                        <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>BD Name</th>
                                 
                                        <th>Total Companies</th>
                                        <th>Open</th>
                                        <th>Open [RPEM]</th>
                                        <th>Reachout</th>
                                        <th>Tentative</th>
                                        <th>Will-Do-Later</th>
                                        <th>Not-Interest</th>
                                        <th>TTD-Reachout</th>
                                        <th>WNO-Reachout</th>
                                        <th>Positive</th>
                                        <th>Positive-NAP</th>
                                        <th>Very Positive</th>
                                        <th>Very Positive-NAP</th>
                                        <th>Closure</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=1;
                                    foreach($mdata as $dt){
                                    $bdid = $dt->user_id;
                                    $mbd=$this->Menu_model->get_mbdc12($bdid,$categories);
                                    // $mbdc=$this->Menu_model->get_mbdcbycat($bdid,$categories);
                                    // echo $str = $this->db->last_query();
                                    // echo "<br/>";
                                    foreach($mbd as $mc){
                                    ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$dt->name?></td>
                                       
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/0/<?=$bdid?>/<?=$categories?>"><?=$mc->a?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/1/<?=$bdid?>/<?=$categories?>"><?=$mc->b?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/8/<?=$bdid?>/<?=$categories?>"><?=$mc->i?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/2/<?=$bdid?>/<?=$categories?>"><?=$mc->c?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/3/<?=$bdid?>/<?=$categories?>"><?=$mc->d?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/4/<?=$bdid?>/<?=$categories?>"><?=$mc->e?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/5/<?=$bdid?>/<?=$categories?>"><?=$mc->f?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/10/<?=$bdid?>/<?=$categories?>"><?=$mc->k?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/11/<?=$bdid?>/<?=$categories?>"><?=$mc->l?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/6/<?=$bdid?>/<?=$categories?>"><?=$mc->g?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/12/<?=$bdid?>/<?=$categories?>"><?=$mc->o?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/9/<?=$bdid?>/<?=$categories?>"><?=$mc->j?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/13/<?=$bdid?>/<?=$categories?>"><?=$mc->p?></a></td>
                                        <td><a href="<?=base_url();?>Menu/CategoryBDC/7/<?=$bdid?>/<?=$categories?>"><?=$mc->h?></a></td>
                                    </tr>
                                    <?php $i++;}} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
                <?php } ?>
            </fieldset>
        </div>
        <hr />
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