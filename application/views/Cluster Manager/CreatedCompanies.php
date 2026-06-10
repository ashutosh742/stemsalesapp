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
.flexitems {
    align-items: center;
    justify-content: center;
    display: flex;
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
<h1 class="m-0">Total Companies</h1>
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

<div class="row">
        <div class="col-md-10">
        <div>
        <form class="p-3" method="POST" action="">

            <label for="">Common Companies
            <select class="form-control" id="commoncompanies" name="commoncompanies" style="width:200px" >
                <option <?php if($commoncompanies == ''){echo "selected";} ?> value="">Select </option>
                <option <?php if($commoncompanies == 'meetings'){echo "selected";} ?> value="meetings">Because of meetings</option>
                <option <?php if($commoncompanies == 'topspender'){echo "selected";} ?> value="topspender">Because of Top Spender </option>
            </select>
            </label>

            <label id="commonwith">Common with
            <select class="form-control" name="commonwith" style="width:200px">
            <option <?php if($commonwith == ''){echo "selected";} ?> value="">Select </option>
                <option <?php if($commonwith == 'bd'){echo "selected";} ?> value="bd">BD</option>
                <option <?php if($commonwith == 'pst'){echo "selected";} ?> value="pst">PST</option>
                <option <?php if($commonwith == 'both'){echo "selected";} ?> value="both">Both</option>
            </select>
            </label>
    
            <label id="topspender">Top Spender
            <select class="form-control" name="topspender" style="width:200px">
            <option <?php if($topspender == ''){echo "selected";} ?> value="">Select </option>
                <option <?php if($topspender == 'yes'){echo "selected";} ?> value="yes">Yes</option>
                <option <?php if($topspender == 'no'){echo "selected";} ?> value="no">No</option>
            </select>
            </label>
                   
            <button type="submit" name="submit" class="btn btn-primary mb-2">Submit</button> 
            </form>
              </div>
    </div>
    <div class="col-md-2 flexitems">
      <a style="margin-top:40px;" class="btn btn-primary p-2" href="<?=base_url();?>Menu/pcompanies/0/<?=$uid ?>">Reset</a>
    </div>
</div>
<!-- /.card-header -->
<div class="card-body">
    <div class="container-fluid body-content">

        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>Cluster Manager</th>
                                            <th>PST</th>
                                            <th>Company ID</th>
                                            <th>Company Name</th>
                                            <th>Total Logs</th>
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
                                            <th>Contact Person</th>
                                            <th>Contact Number</th>
                                            <th>Email</th>
                                            <th>Designation</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Next Action Date</th>
                                            <th>Next Action Type</th>
                                            <th>Current Status</th>
                                            <th>Current Remark</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php
                                        $i=1;
                                        // echo "<pre>";
                                        // print_r($mdata);
                                        foreach($mdata as $dt){
                                        $cid = $dt->cmpid_id;
                                        
                                        $cd=$this->Menu_model->get_cdbyid($cid);
                                        $ccd=$this->Menu_model->get_ccdbyid($cid);
                                        if($ccd){$contactperson=$ccd[0]->contactperson;$phoneno=$ccd[0]->phoneno;$emailid=$ccd[0]->emailid;$designation=$ccd[0]->designation;}
                                        else{$contactperson="";$phoneno="";$emailid="";$designation="";}
                                        
                                        $init=$this->Menu_model->get_initcallbyid($cid);
                                        $ciid = $init[0]->id;
                                        $pid = $init[0]->apst;
                                        $mbdid = $init[0]->mainbd;
                                        $clm_id = $init[0]->clm_id;
                                        
                                        
                                        $tblc=$this->Menu_model->get_tblbyidwithremark($ciid);
                                        $logs = sizeof($tblc);
                                        
                                        
                                        
                                        $patid = $cd[0]->partnerType_id;
                                        if($patid){$patid = $this->Menu_model->get_partnerbyid($patid);$patid = $patid[0]->name;} else{$patid='';}
                                        
                                        
                                        if($init){$sid = $init[0]->cstatus;$sid=$this->Menu_model->get_statusbyid($sid);$sid=$sid[0]->name;}
                                        else{$sid='';}
                                        
                                        if($pid){$pstname=$this->Menu_model->get_userbyid($pid);$pstname=$pstname[0]->name;}
                                        else{$pstname='';}

                                        if($clm_id){$clm_name=$this->Menu_model->get_userbyid($clm_id);$clm_name=$clm_name[0]->name;}
                                        else{$clm_name='';}

                                        if($mbdid){$bdname=$this->Menu_model->get_userbyid($mbdid);$bdname=$bdname[0]->name;}
                                        else{$bdname='';}
                                        
                                        if($init){$cretname=$init[0]->creator_id;$cretname=$this->Menu_model->get_userbyid($cretname);$cretname=$cretname[0]->name;}
                                        else{$cretname='';}
                                        
                                        if($logs>0){$appoint = $tblc[0]->appointmentdatetime;
                                        $nextaction = $tblc[0]->nextaction;
                                        $remarks = $tblc[0]->remarks;}else{$appoint='';$nextaction='';$remarks='';}
                                        ?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><?=$bdname?></td>
                                            <td><?=$clm_name?></td>
                                            <td><?=$pstname?></td>
                                            <td><?=$cd[0]->id?></td>
                                            <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$cid?>"><?=$cd[0]->compname?></a></td>
                                            <td><?=$logs?></td>
                                            <td><?php 
                                            $stateid = $cd[0]->state;
                                            if (is_numeric($stateid)) {
                                              $state = $this->Menu_model->get_statebyid($stateid);
                                              echo $state;
                                            } else {
                                                echo $stateid;
                                            }
                                            ?></td>
                                            <td><?php 
                                            $cityid = $cd[0]->city;
                                            if (is_numeric($cityid)) {
                                              $city = $this->Menu_model->get_citybyid($city);
                                              echo $city;
                                            } else {
                                                echo $cityid;
                                            }
                                            
                                            ?></td>
                                            <td><?=$cd[0]->draft?></td>
                                            <td><?=$cd[0]->budget?></td>
                                            <td><?=$cd[0]->address?></td>
                                            <td><?=$cd[0]->website?></td>
                                            <td><?=$patid?></td>
                                            <td><?=$init[0]->focus_funnel;?></td>
                                            <td><?=$init[0]->upsell_client;?></td>
                                            <td><?=$init[0]->keycompany;?></td>
                                            <td><?=$contactperson?></td>
                                            <td><?=$phoneno?></td>
                                            <td><?=$emailid?></td>
                                            <td><?=$designation?></td>
                                            <td><?=$cd[0]->createddate?></td>
                                            <td><?=$cretname?></td>
                                            <td><?=$appoint?></td>
                                            <td><?=$nextaction?></td>
                                            <td><?=$sid?></td>
                                            <td><?=$remarks?></td>
                                           
                                        </tr>
                                        <?php $i++;} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </form>            <!--END OF FORM ^^-->
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
<div id="dcompany" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="modal-standard-title1"></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div> <!-- // END .modal-header -->
<div class="modal-body">
    <div class="card card-form col-md-12">
        <div class="card-header bg-info">Add Delete Remark</div>
        <div class="col-lg-12 card-body">
            <?=form_open('Menu/dremark')?>
            <input type="hidden" name="codeid" value="<?=$code?>">
            <input type="hidden" name="bdid" value="<?=$bdid?>">
            <input type="hidden" name="cid" id="cmpida">
            <input type="hidden" name="pstuid" value="<?=$uid?>">
            <textarea rows="10" name="dremark" class="form-control"></textarea><hr>
            <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
        </form>
    </div>
</div>
</div>
</div>
</div> <!-- // END .modal-body -->
</div></div></div>
<div id="doaction" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title" id="modal-standard-title1"></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div> <!-- // END .modal-header -->
    <div class="modal-body">
        <div class="card card-form col-md-12">
            <div class="card-header bg-info">Add Review Remark</div>
            <div class="col-lg-12 card-body">
                <?=form_open('Menu/rremark')?>
                
                <input type="hidden" name="codeid" value="<?=$code?>">
                <input type="hidden" name="cid" id="cmpid">
                <input type="hidden" name="pstuid" value="<?=$uid?>">
                <textarea rows="10" name="remark" class="form-control"></textarea><hr>
                
                <center><h5>Create Task</h5></center>
                <input type="hidden" name="ntuid" value="<?=$uid?>">
                <input type="hidden" name="ntinid" value="<?=$init[0]->id?>">
                <?php $date = date('Y-m-d h:i:s');?>
                <input type="datetime-local" name="ntdate" value="<?=$date?>" class="form-control">
                <lable>Current Status</lable>
                <input type="hidden" id="ntstatus" name="ntstatus" value="8">
                <lable>Select Task for</lable>
                <select class="form-control" name="bdid">
                    <option value="<?=$uid?>"><?=$user['name']?></option>
                </select>
                <lable>Select Action</lable>
                <select id="ntaction" name="ntaction" class="form-control">
                    <option value="">Select Action</option>
                    <?php $action = $this->Menu_model->get_action();
                    foreach($action as $a){?>
                    <option value="<?=$a->id?>"><?=$a->name?></option>
                    <?php } ?>
                </select>
                <lable>Select Purpose</lable>
                <select id="ntppose" class="form-control" name="ntppose">
                </select>
                <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
            </form>
        </div>
    </div>
</div>
</div>
</div> <!-- // END .modal-body -->
</div></div></div>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
$('[id^="add_Rremark"]').on('click', function() {
var cmpid = this.value;
$('#doaction').modal('show');
document.getElementById("cmpid").value = cmpid;
});
$('[id^="DeleteC"]').on('click', function() {
var cmpid = this.value;
$('#dcompany').modal('show');
document.getElementById("cmpida").value = cmpid;
});
$('#ntaction').on('change', function f() {
var sid = document.getElementById("ntstatus").value;
var aid = document.getElementById("ntaction").value;
$.ajax({
url:'<?=base_url();?>Menu/getpurpose',
type: "POST",
data: {
sid: sid,
aid: aid
},
cache: false,
success: function a(result){
$("#ntppose").html(result);
}
});
});
$('#ntppose').on('change', function f() {
var pid = document.getElementById("ntppose").value;
$.ajax({
url:'<?=base_url();?>Menu/getnextaction',
type: "POST",
data: {
pid: pid
},
cache: false,
success: function a(result){
$("#nextaction").html(result);
}
});
});



$( document ).ready(function() {
// $("#commonwith").hide();
var commoncompanies = '<?= $commoncompanies ?>';
var topsepender = '<?= $topspender ?>';
var commonwith = '<?= $commonwith ?>';

if(commoncompanies == 'meetings'){
    $("#topspender").hide();
}
    
if(commoncompanies == 'topspender'){
    $("#commonwith").hide();
}

if(commoncompanies == ''){
    $("#commonwith").hide();
    $("#topspender").hide();
}

$("#commoncompanies").change(function(){
          var val = $(this).val();
      
          if (val == 'meetings') {
                $("#commonwith").show();
                $("#topspender").hide();
            }else if (val == 'topspender') {
                $("#topspender").show();
                $("#commonwith").hide();
            }else{
                $("#commonwith").hide();
                $("#topspender").hide();
            }
        });
});

















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