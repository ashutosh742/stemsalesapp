<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
<title>Special Restriction on Task Planner | STEM APP | WebAPP</title>
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
.card-body {
    background: azure;
}
.formSection{
    align-items: center;
    justify-content: center;
    display: flex;
}
.form-control {
    width: 200px !important;
}
.modal-content {
    background: antiquewhite;
    }
.coladjust {
    padding-right: 5px;
    padding-left: 5px;
    height: 143px;
    align-items: center;
    justify-content: center;
    color: #000;
}
.col.card.coladjust1 {
    color: black;
}
.col.card.coladjust1 select {
    width: 100% !important;
}th.sorting {
    background: darkcyan;
    color: white;
}
.modal-header {
    background: cadetblue;
    color: white;
}
.form-control.is-invalid, .was-validated .form-control:invalid {background-image: none !important;}
 .form-control.is-valid, .was-validated .form-control:valid {background-image: none !important;} 
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

<?php if($this->session->flashdata('success_message')): ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('success_message'); ?>
        </div>
    <?php endif; ?>

<div class="container-fluid">
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header" style="background: #002a1f;">
<h2 class="text-center m-3 text-white">Special Restriction on Task Planner</h2>
</div>
<!-- /.card-header -->
<div class="card-body">
    <div class="formSection p-5 bg-info">
    <div class="was-validated">
    <form action="<?=base_url();?>Management/AddTaskPlannerRestrication" method="post">
  <div class="form-row">
   <?php $curtype_id = $user['type_id']; ?>
    <div class="col card coladjust1 p-2 m-1">
    <lable>Select User Type : </lable>
       <select name="user_type[]" class="form-control" id="user_type" multiple required >
       <option disabled >Select User Type </option>
                <?php $get_user_type = $this->Menu_model->get_user_type();
   
                if($curtype_id == 2){
                    foreach($get_user_type as $at){
                        if($at->id != 1 && $at->id != 2){
                    ?>
                    <option value="<?=$at->id?>"><?=$at->name?></option>
                    <?php }}
                }elseif($curtype_id == 15){
                    foreach($get_user_type as $at){
                        if($at->id == 3 || $at->id == 4 || $at->id == 13){
                    ?>
                    <option value="<?=$at->id?>"><?=$at->name?></option>
                    <?php }}
                }elseif($curtype_id == 4){
                    foreach($get_user_type as $at){
                        if($at->id == 3 || $at->id == 13){
                    ?>
                    <option value="<?=$at->id?>"><?=$at->name?></option>
                    <?php }}
                }elseif($curtype_id == 13){
                    foreach($get_user_type as $at){
                        if($at->id == 3){
                    ?>
                    <option value="<?=$at->id?>"><?=$at->name?></option>
                    <?php }}
                }
                ?>
        </select>
        <small id="noofuser_type"></small>
    </div>
    <?php 
     ?>
    <div class="col card coladjust1 p-2 m-1">
    <lable>Select User : </lable>
       <select name="user_id[]" class="form-control" id="user_data" multiple>
        </select>
        <small id="noofuser"></small>
    </div>
    <div class="col card coladjust1 p-2 m-1">
    <lable>Select Company Status : </lable>
       <select name="company_status[]" id="company_status" class="form-control" multiple >
        <option selected value='' disabled >Select Status</option>
                <?php $allStatus = $this->Menu_model->get_status(); ?>
                <?php foreach($allStatus as $getstatus): ?>
                    <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                    <?php endforeach; ?>
        </select>
        <small id="company_statuss"></small>
    </div>
    <div class="col card coladjust1 p-2 m-1">
    <lable>Select Partner Type : </lable>
       <select name="partner_type[]" id="partner_type" class="form-control" multiple >
       <option selected value='' disabled >Select Partner Type</option>
        <?php $get_partner = $this->Menu_model->get_partner();
                foreach($get_partner as $a){
                ?>
            <option value="<?=$a->id?>"><?=$a->name?></option>
            <?php } ?>
        </select>
        <small id="partner_types"></small>
    </div>
    <div class="col card coladjust1 p-2 m-1">
    <lable>Select Category : </lable>
    <select class="form-control" name="category[]" id="category" multiple>
    <option selected value='' disabled >Select Category</option>
        <option value="topspender">Top Spender</option>
        <option value="upsell_client">Upsell Client</option>
        <option value="focus_funnel">Focus Funnel</option>
        <option value="keycompany">Key Company</option>
        <option value="pkclient">P Key Client</option>
    </select>
    <small id="categorys"></small>
    </div>
    <div class="col card coladjust1 p-2 m-1">
    <lable>Select Task : </lable>
       <select name="action[]" class="form-control" id="action_type" class="select_area" multiple required >
       <option disabled >Select Task </option>
            <?php $action = $this->Menu_model->get_action();
            foreach($action as $a){
                if($a->id!=4 && $a->id!=6 && $a->id!=8 && $a->id!=9 && $a->id!=11){
            ?>
            <option value="<?=$a->id?>"><?=$a->name?></option>
            <?php }} ?>
        </select>
        <small id="noofaction_type"></small>
    </div>

    
  </div>

  <div class="form-row">
  <div class="col card coladjust m-1">
        <div>
        <lable>Start Date : </lable>
        <input type="date" class="form-control" min="<?= date('Y-m-d') ?>" name="sdate" placeholder="Start Date" required >
        </div>
    </div>
    <div class="col card m-1 coladjust">
    <div>
    <lable>End Date : </lable>
      <input type="date" class="form-control" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+3 days')) ?>" name="edate" placeholder="End Date" required >
      </div>
    </div>
    <div class="col card coladjust m-1">
    <div>
    <lable> Select Restrication Status </lable>
    <select  name="status" class="form-control" required >
    <option disbaled value="" >Select Status</option>
                <option value="1">Active</option>
                <option selected value="0">Inactive</option>
        </select>
        </div>
    </div>
  </div>






  <br>
  <hr>

  <div class="form-row">
  <div class="col-sm-12">
    <center>
    <button type="submit" class="btn mb-2" style="background: #002a1f;color:white;" >Add Restriction </button>
    </center>
    </div>
  </div>

</form>
</div>
    </div>



<div class="container-fluid body-content mt-4">
    <div class="page-header">
        <div class="form-group">
            <fieldset>
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>User Type</th>
                                    <th>User Name</th>
                                    <th>Company Status</th>
                                    <th>Partner Type</th>
                                    <th>Company Category</th>
                                    <th>Restrication</th>
                                    <th>Start&nbsp;Date</th>
                                    <th>End&nbsp;Date</th>
                                    <th>Status</th>
                                    <th>Added By</th>
                                    <th>Manage</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $cur_user_id = $user['user_id'];
                                $cur_type_id = $user['type_id'];
                                $restData = $this->Management_model->GetTaskPlannerRestricationInTable1($cur_user_id,$cur_type_id);
                                $i=1;
                                foreach($restData as $data):
                                ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                         <td>
                                            <?php 
                                            if($data->user_types !=='all'){
                                                $auser_types = $this->Menu_model->get_user_types($data->user_types);
                                                foreach($auser_types as $types){
                                                    $utypes = $types->name;
                                                    $utypes = str_replace(' ', '&nbsp;', $utypes);
                                                    echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$utypes</span>";
                                                }
                                            }else{
                                                echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >All</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if($data->user_ids !==''){
                                                $rusers = $this->Menu_model->get_userbyids($data->user_ids);
                                                $k=1;
                                                foreach($rusers as $ruser){
                                                    if($k == 6){
                                                        echo "<br/>";
                                                        $k=1;
                                                    }
                                                    $ruser_name = $ruser->name;
                                                    $ruser_name = str_replace(' ', '&nbsp;', $ruser_name);
                                                    echo "<span class='p-2 bg-success m-1 text-capitalize' style='line-height: 50px;' >$ruser_name</span>";
                                                    $k++;
                                                 }
                                            }else{
                                                echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >No&nbsp;Select</span>";
                                            }
                                            ?>
                                        </td>
                                        <td> 
                                        <?php 
                                            if($data->company_status !==''){
                                                $acompany_status = $this->Menu_model->get_statusbyMultiid($data->company_status);
                                                foreach($acompany_status as $c_statuss){
                                                    $com_status = $c_statuss->name;
                                                    $com_status = str_replace(' ', '&nbsp;', $com_status);
                                                    echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$com_status</span>";
                                                }
                                            }else{
                                                if($data->company_status ==''){
                                                    echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >No&nbsp;Select</span>";
                                                }
                                            }
                                            ?>
                                          </td>
                                        <td> 
                                        <?php 
                                            if($data->partner_types !==''){
                                                $apartner_types = $this->Menu_model->get_partnerbyMultiid($data->partner_types);
                                                foreach($apartner_types as $cprt_statuss){
                                                    $comp_partnername = $cprt_statuss->name;
                                                    $comp_partnername = str_replace(' ', '&nbsp;', $comp_partnername);
                                                    echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$comp_partnername</span>";
                                                }
                                            }else{
                                                if($data->partner_types ==''){
                                                    echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >No&nbsp;Select</span>";
                                                }
                                            }
                                            ?>
                                          </td>
                                         <td> 
                                        <?php 
                                          if($data->categorys ==''){
                                            echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >No&nbsp;Select</span>";
                                        }else{
                                            $arrays = explode(',', $data->categorys);
                                            foreach($arrays as $arr){
                                                if($arr == 'topspender'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Top&nbsp;Spender</span>";}
                                                if($arr == 'upsell_client'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Upsell&nbsp;Client</span>";}
                                                if($arr == 'focus_funnel'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Focus&nbsp;Funnel</span>";}
                                                if($arr == 'keycompany'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Key&nbsp;Company</span>";}
                                                if($arr == 'pkclient'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Positive&nbsp;Key&nbsp;Client</span>";}
                                                if($arr == 'all'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >All</span>";}
                                            }
                                        }
                                      ?></td>
                                       <td>
                                        <?php
                                        if($data->action_id !=='all'){
                                            $actiondata = $this->Menu_model->get_actionbyids($data->action_id);
                                            foreach($actiondata as $actiondatasp){
                                             $actiondataname = $actiondatasp->name;
                                             $actiondataname = str_replace(' ', '&nbsp;', $actiondataname);
                                            echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$actiondataname</span>";
                                            }
                                        }else{
                                            echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >All</span>";
                                        }
                                         ?>
                                         </td>
                                         <td>
                                            <?= $data->sdate ?> 
                                        </td>
                                        <td>
                                        <?php
                                        $renddate = $data->edate;
                                        $currentDate = new DateTime();
                                        $givenDateObj = new DateTime($renddate);

                                        // Remove the time part from the current date for accurate comparison
                                        $currentDate->setTime(0, 0, 0);
                                        $givenDateObj->setTime(0, 0, 0);

                                        if ($givenDateObj < $currentDate) {
                                            echo "<span class='p-2 bg-danger m-1' style='line-height: 50px;' >$renddate</span><br>"; 
                                            echo "<span class='p-2 bg-danger m-1' style='line-height: 50px;' >The&nbsp;Restriction&nbsp;has&nbsp;expired.</span>";
                                        } elseif ($givenDateObj == $currentDate) {
                                            echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >$renddate</span><br>"; 
                                            echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >The&nbsp;Restriction&nbsp;expires&nbsp;today.</span>";
                                        } else {
                                            echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$renddate</span><br>"; 
                                            echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >The&nbsp;Restriction&nbsp;is&nbsp;still&nbsp;active.</span>";
                                        }
                                        
                                        
                                        ?>
                                        </td>
                                        <td>
                                        <?php 
                                        if($data->status ==1){
                                            echo "<span class='p-2 bg-success'>Active</span>";
                                        }else{
                                             echo "<span class='p-2 bg-danger'>Inactive</span>";
                                        }
                                        ?>
                                        </td>
                                        <td><?php 
                                        $addedData = $this->Menu_model->get_userbyids($data->added_by);
                                        $addedDatacnt = sizeof($addedData);
                                        if($addedDatacnt > 0){
                                            $addedDataName = $addedData[0]->name;
                                            echo $addedDataName;
                                        }
                                        
                                        ?></td>
                                        <td>
                                        <p><button type="button" class="btn btn-success"  onclick="RestrictionUpdate(<?= $data->id?>,'<?= $data->sdate ?>','<?= $data->edate ?>')"><i class="fa fa-cog" aria-hidden="true"></i></button></p>
                                        </td>
                                        <td>
                                        <p><a href="https://stemapp.in/Management/SpecialRestricationonDelete/<?= $data->id?>"><span class='p-2 bg-danger'>Delete</span></a></p>
                                        </td>
                                    </tr>
                                    <?php $i++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    </div>           <!--END OF FORM ^^-->
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

<div class="modal fade" id="exampleModalCenterdatamom" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header text-center">
                       
                          <h4 class="modal-title" id="exampleModalLongTitle">Update Restrication</h4>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <form action="<?=base_url();?>Management/ChangeStatusofRestrication" method="post" >
                            <input type="hidden" id="res_id" value="" name="res_id">
                            <div class="row">
                            <div class="col-md-6">
                            <lable>Start Date</lable>
                            <input type="date" class="form-control" id="start_date"  value="" name="start_date">
                            </div>
                            <div class="col-md-6">
                            <lable>End Date</lable>
                            <input type="date" class="form-control" id="end_date"  value="" name="end_date">
                            </div>
                            </div>
                            <hr>
                            <div class="col-md-12 mb-3">
                            <lable>Restriction Status</lable>
                            <select class="form-control" style="width:98%!important;" name="active_diactive" >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                              </select>
                            </div>
                            <div class="form-group text-center">
                              <button type="submit" class="btn btn-success mt-2">Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>


</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

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
"buttons": ["csv", "excel", "pdf"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>

<script type='text/javascript'>  
function RestrictionUpdate(id,sdate,edate){
$('#exampleModalCenterdatamom').modal('show');
$('#res_id').val(id);
$('#start_date').val(sdate);
$('#end_date').val(edate);
}

$(document).ready(function(){

var optionCount = $('#action_type').find('option').length;
$("#noofaction_type").text("Total Number of Task: " + (optionCount-1));
$("#noofaction_type").css({"color": "green"});

var optionCount1 = $('#user_type').find('option').length;
$("#noofuser_type").text("Total Number of Department: " + (optionCount1-1));
$("#noofuser_type").css({"color": "green"});

var optionCount2 = $('#company_status').find('option').length;
$("#company_statuss").text("Total Number of Status: " + (optionCount2-1));
$("#company_statuss").css({"color": "green"});

var optionCount3 = $('#partner_type').find('option').length;
$("#partner_types").text("Total Number of Partner: " + (optionCount3-1));
$("#partner_types").css({"color": "green"});

var optionCount4 = $('#category').find('option').length;
$("#categorys").text("Total Number of Category: " + (optionCount4-1));
$("#categorys").css({"color": "green"});

});


$('#user_type').on('change', function() {

var user_type_id = $(this).val();
$.ajax({
url: '<?=base_url();?>Management/getAllActiveUserInDepartment',
type: "POST",
data: {
    user_type_id: user_type_id
},
cache: false,
success: function a(result) {
$("#user_data").html(result);

var optionCount = $('#user_data').find('option').length;
$("#noofuser").text("Total Number of User: " + optionCount);
$("#noofuser").css({"color": "green"});
}
});
});







</script>





</body>
</html>