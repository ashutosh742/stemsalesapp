<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>STEM Operation | WebAPP</title>
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
        
        <div class="bg-primary p-2 text-center">
        <h1 class="m-0">Create Travel Cluster</h1>
        </div>
        <hr>
        <?php if ($this->session->flashdata('success_message')): ?>
        <div class="container-fluid">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row p-3">
            <div class="col-md-6 col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <form action="<?=base_url();?>Menu/AddNewCluster" method="post" enctype="multipart/form-data">
                            <div class="was-validated">
                                <input type="hidden" name="uid" value="<?=$uid?>">

				 <div class="form-group">
                                    <label for="task_type">Type of Travel</label><br>
                                    <!-- <input type="text" placeholder='Enter Cluster Name' class="form-control" name="typeofTravel" id="typeofTravel" required> -->
                                    <select class="form-control" name="typeofTravel" id="typeofTravel" required>
                                        <option value="" Selected disabled>Select Location</option>
                                        <option value="base">Base Location</option>
                                        <option value="outStation">Out Station</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="task_type">Travel Cluster Name</label><br>
                                    <input type="text" placeholder='Enter Cluster Name' class="form-control" name="cluster" id="cluster" required>
                                </div>
                                
                                
                                
                                <div class="form-group">
                                    <label for="task_type">Select Cluster State</label><br>
                                    <select  name="clusterState" id="clusterState" class="form-control" required>
                                        <!--<option disabled selected>Select Cluster State</option>-->
                                        <?php foreach($state as $stat){?>
                                        <option value='<?=$stat->state_id ?>'><?=$stat->state_title ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="task_type">Select Cluster District</label><br>
                                    <select  name="clusterDistrict[]" id="clusterDistrict" multiple class="form-control" required>
                                        <!--<option disabled selected>Select Cluster District</option>-->
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="task_type">Select Cluster City</label><br>
                                    <select  name="clusterCity[]" multiple id="clusterCity" class="form-control" required>
                                        <!--<option disabled selected>Select Cluster City</option>-->
                                    </select>
                                </div>
                                
                                <hr>
                                <center>
                                <button type="submit" class="btn btn-primary">Submit</button>
                                </center>
                                <hr>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <img src="<?=base_url()?>assets/image/travel.jpg" class="img-fluid" alt="">
            </div>



        </div>
        
        <div class="table-responsive">
            <div class="table-responsive">
                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cluster Name</th>
                            <th>Cluster State</th>
                            <th>Cluster District</th>
                            <th>Cluster City</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $k=1;
                        foreach($cluster as $c){
                        
                        $inState = $this->Menu_model->getInStateById($c->in_state);
                        
                        $inDistrict = $this->Menu_model->getInDistricById($c->in_district);
                        
                        $distData = '';
                        foreach($inDistrict as $dis){
                        $distData .= $dis->district_title.'  | ';
                        }
                        $distData = rtrim($distData, " | ");
                        
                        $in_cityid = rtrim($c->in_city, ',');
                        $inCity = $this->Menu_model->getInCityById($in_cityid);
                        
                        $cityData = '';
                        foreach($inCity as $cityd){
                        $cityData .= $cityd->name.'  | ';
                        }
                        $cityData = rtrim($cityData, " | ");
                        
                        ?>
                        <tr>
                            <td><?= $k ?></td>
                            <td><?= $c->clustername  ?></td>
                            <td><?= $inState[0]->state_title ?></td>
                            <td><?= $distData;  ?></td>
                            <td><?= $cityData;  ?></td>
                        </tr>
                        <?php $k++; } ?>
                        
                    </tbody>
                </table>
                
            </div>
        </div>
        
        
        
        
        
    </div>
</section>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type='text/javascript'>
</script>

<!-- /.row (main row) -->
</div><!-- /.container-fluid -->
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
<script>
$('#clusterState').on('change', function() {
var clusterState = $('#clusterState').val();

$.ajax({
url: '<?=base_url();?>Menu/getAllclusterDistrict',
type: "POST",
data: {
clusterState: clusterState
},
cache: false,
success: function a(result) {
$("#clusterDistrict").html(result);
}
});
});


$('#clusterDistrict').on('change', function() {


var clusterDistrict = $('#clusterDistrict').val();
$.ajax({
url: '<?=base_url();?>Menu/getAllclusterDistrictCity',
type: "POST",
data: {
clusterDistrict: clusterDistrict
},
cache: false,
success: function a(result) {
$("#clusterCity").html(result);
}
});
});


$("#example1").DataTable({
"responsive": false, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>