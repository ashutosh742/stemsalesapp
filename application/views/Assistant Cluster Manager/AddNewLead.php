
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Add New Lead || STEM APP | WebAPP</title>
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
    .card-body.box-profile.p-5 {
    background: beige;
}
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Preloader -->

<!-- Navbar -->
<?php require('nav.php');?>
<?php //require('addlog.php');?>
<!-- /.navbar -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- <h1 class="m-0">ADD NEW LEAD</h1> -->
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <h4>HI! <?php $uid=$user['user_id']?></h4>
                        </ol>
                        </div><!-- /.col -->
                        </div><!-- /.row -->
                        </div><!-- /.container-fluid -->
                    </div>
                    <!-- /.content-header -->
                    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 m-auto">
                    <!-- Default box -->
                    <div class="card card-primary">
                        <div class="card-header">
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body box-profile p-5">
                                            
                                            
            <!-- <div class="card p-3">
                <form class="" action="<?php echo base_url(); ?>excel_import/import" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="bdid" value="<?=$uid?>">
                    <input type="file" name="excel" required value="">
                    <button type="submit" name="import">Import</button>
                </form>
                <a href="<?=base_url();?>uploads/format/CompanyDetail.xlsx" target="_blank">Download Format</a>
            </div> -->
            <?php 

    
                $initData   = $this->Menu_model->get_initcallbyiid($init_id);
                $mcid       = $initData[0]->cmpid_id;
                $cData     = $this->Menu_model->get_cdbyid($mcid);
            
               
                
                $topspender       = $initData[0]->topspender;
                $upsell_client    = $initData[0]->upsell_client;
                $focus_funnel     = $initData[0]->focus_funnel;
                $priorityc        = $initData[0]->priorityc;
                $potential        = $initData[0]->potential;
                $cluster_id       = $initData[0]->cluster_id;
                $cstatus          = $initData[0]->cstatus;
                $draft            = $initData[0]->draft;
                $new_lead         = $initData[0]->new_lead;
                $is_admin_approved = $initData[0]->is_admin_approved;

                // $is_admin_approved =2;
                // $new_lead =1;
               
                if($new_lead == 1 && $is_admin_approved ==2){
                // dd($initData);
                $compname         = $cData[0]->compname;
                $address          = $cData[0]->address;
                $website          = $cData[0]->website;
                $budget           = $cData[0]->budget;
                $city             = $cData[0]->city;
                $state            = $cData[0]->state;
                $country          = $cData[0]->country;
                $partnerType_id   = $cData[0]->partnerType_id;

                $partners          = $this->Menu_model->get_partnerbyid($partnerType_id);
                $partnername      = $partners[0]->name;

                $cluster          = $this->Menu_model->GetClusterById($cluster_id);
                $clustername      = $cluster[0]->clustername;

                $keycompany       = $initData[0]->keycompany;
                $cstatus          = $initData[0]->cstatus;

                $cstatus          = $this->Menu_model->get_statusbyid($cstatus);
                $cstatusname      =  $cstatus[0]->name;

                $ccData     = $this->Menu_model->get_ccdbypid($mcid);
              
     
                $contactperson    = $ccData[0]->contactperson;
                $emailid          = $ccData[0]->emailid;
                $phoneno          = $ccData[0]->phoneno;
                $designation      = $ccData[0]->designation;
                $type             = $ccData[0]->type;
                $mainheadings     = 'Reupdate Lead';

        

                }else{
                $topspender       = '';
                $upsell_client    = '';
                $focus_funnel     = '';
                $priorityc        = '';
                $potential        = '';
                $cluster_id       = '';
                $new_lead         = '';


                $compname         = '';
                $address          = '';
                $website          = '';
                $budget           = '';
                $city             = '';
                $state            = '';
                $country          = '';
                $partnerType_id   = '';

                $keycompany       = '';
                $cstatus          = '';

                $contactperson    = '';
                $emailid          = '';
                $phoneno          = '';
                $designation      = '';
                $type             = '';
                $draft            = '';
                $mainheadings     = 'ADD NEW LEAD';

                }

        
            ?>
            
            
            <!-- form start -->
            <form method="post" action="<?=base_url();?>Menu/addcompany_new">
                <input type="hidden" name="uid" value="<?=$uid?>">
                <div class="text-center">
                    <h2><?= $mainheadings ?></h2> <hr>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-lg-6 p-3">
                        <div class="was-validated">
                            <div class="form-row">
                                <input type="hidden" name="init_id" value="<?=$init_id?>">
                                <div class="col-12 col-md-12">
                                    <label for="validationSample01">Company Name</label>
                                    <input type="text" class="form-control" value="<?= $compname ?>"  placeholder="Company Name" value="" required=""  id="compname" name="compname" >
                                    <div class="invalid-feedback">Please provide a Company name.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample02">Website Link</label>
                                    <input type="text" class="form-control" value="<?= $website ?>" id="website" placeholder="Website Link" name="website"  required="">
                                    <div class="invalid-feedback">Please provide a company's website.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample03">Country</label>
                                    <select type="text" class="form-control" id="country" name="country" required="">
                                        <option>India</option>
                                    </select>
                                    <div class="invalid-feedback">Please provide a valid city.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample04">State</label>
                                    <select name="state" id="id_state" required class="form-control">
                                        <option value="">Select State</option>
                                        <?php foreach($states as $st){
                                            
                                            if($state == $st->state){
                                                $selected = 'selected';
                                            }else{
                                                $selected = '';
                                            }
                                            
                                            ?>
                                        <option <?= $selected ?> value="<?=$st->id;?>" ><?=$st->state?></option>
                                        <?php }?>
                                    </select>
                                    <div class="invalid-feedback">Please provide a valid state.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample04">District</label>
                                    <select id="city" class="form-control" name="city" required>
                                    </select>
                                    <div class="invalid-feedback">Please provide a valid city.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-12">
                                <label for="validationSample01">Draft <sup>optional</sup></label>
                                <textarea type="text" name="draft" id="draft" class="form-control"  placeholder="Draft" ><?= $draft; ?></textarea>
                            </div>
                            <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample02">Address</label>
                                <textarea class="form-control" name="address" id="address" placeholder="Full Address" required=""><?= $address ?></textarea>
                                <div class="invalid-feedback">Please provide a company's address.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                              <br>            
                              <br>            
                              <br>            
                              <br>     
                              <br>            
                    
                            <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample04">Add Cluster</label>
                                    <?php $clusterData = $this->Menu_model->getClusterByUserId($uid);  ?>
                                    <select id="cluster" class="form-control" name="cluster" required>
                                       <option selected disabled>Select Cluster</option>   
                                       <?php foreach($clusterData as $cdata):
                                        if($cluster_id == $cdata->id){
                                            $selected = 'selected';
                                        }else{
                                            $selected = '';
                                        }
                                        ?>
                                       <option <?= $selected; ?> value="<?=$cdata->id?>"><?=$cdata->clustername ?></option>
                                       <?php endforeach; ?>
                                  </select>
                                    <div class="invalid-feedback">Please add a valid cluster.</div>
                                    <div class="valid-feedback">Looks good!</div>
                            </div>
                           
                
                            <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample04">Add Status</label>
                                    <select class="form-control" id="statusid" name="cstatusid" onchange="generateTable()">
                                      <option value="">Select Status</option>
                                      
                                      <?php if($new_lead = 1 && $is_admin_approved ==2){ $slctd = 'selected'; }else{ $slctd = '';} ?> 
                                      
                                      <option <?= $slctd; ?> value="1">Open</option>
                                      <!-- <option value="8">OPEN RPEM</option> -->
                                    </select>
                                    <div class="invalid-feedback">Please add a valid cluster.</div>
                                    <div class="valid-feedback">Looks good!</div>
                            </div>
                            <?php if($new_lead != 1){ ?> 
                            <table class="table table-striped table-bordered mt-2" cellspacing="0" width="" id="statustablehide">
                                <thead>
                                    <tr>
                                        <th>From Status</th>
                                        <th>To Status</th>
                                        <th>Target Date</th>
                                    </tr>
                                </thead>
                                  <tbody id="statusTable" >
                                  </tbody>
                                </table>
                                <?php } ?> 

                        </div>
                        <!-- <div class="col-12 col-md-12">
                            <label for="validationSample01">LinkedIn</label>
                            <input type="text" class="form-control"  placeholder="LinkedIn" value=""   id="linkedin" name="linkedin" >
                        </div>
                        <div class="col-12 col-md-12">
                            <label for="validationSample01">Facebook</label>
                            <input type="text" class="form-control"  placeholder="Facebook" value=""  id="facebook" name="facebook" >
                        </div>
                        <div class="col-12 col-md-12">
                            <label for="validationSample01">YouTube</label>
                            <input type="text" class="form-control"  placeholder="YouTube" value=""   id="youtube" name="youtube" >
                        </div>
                        <div class="col-12 col-md-12">
                            <label for="validationSample01">Instagram</label>
                            <input type="text" class="form-control"  placeholder="Instagram" value=""  id="instagram" name="instagram" >
                        </div> -->
                    </div>
                    
                    <div class="col-sm-12 col-lg-6 p-3">
                        <div class="was-validated">
                            <div class="form-row">
                                <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample02">Company Type</label>
                                    <select id="ctype" name="ctype" class="form-control" required>
                                        <option value="">Select Partner Type</option>
                                        <?php foreach($partner as $p){
                                            if($partnerType_id == $p->id){
                                                $slctd = 'selected';
                                            }else{
                                                $slctd = '';
                                            }
                                            ?>
                                        <option <?= $slctd; ?> value="<?=$p->id?>"><?=$p->name?></option>
                                        <?php }?>
                                    </select>
                                    <div class="invalid-feedback">Please provide a valid Company Type.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-12 col-md-12" id="budgetdiv">
                                    <label for="validationSample01">Budget</label>
                                    <input type="text" value="<?= $budget ?>" name="budget" class="form-control" id="budget" placeholder="Budget" value="" required=""  >
                                    <div class="invalid-feedback">Please provide a Company's budget.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-12">
                                    <label for="validationSample01">Contact person name</label>
                                    <input type="text" class="form-control" value="<?= $contactperson ?>"  placeholder="Contact person name" value="" required=""  id="compconname" name="compconname" >
                                    <div class="invalid-feedback">Please provide a contact person name.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="form-row">
                                    <div class="col-12 col-md-12 mb-12">
                                        <label for="validationSample01">Designation</label>
                                        <input type="text" value="<?= $designation ?>" name="designation" class="form-control" id="designation" placeholder="Designation" value="" required=""  >
                                        <div class="invalid-feedback">Please provide a designation.</div>
                                        <div class="valid-feedback">Looks good!</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample02">Email ID</label>
                                    <input type="email" value="<?= $emailid ?>" class="form-control" id="emailid" placeholder="Email Id" name="emailid"  required="">
                                    <div class="invalid-feedback">Please provide a email id.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-12 mb-12">
                                    <label for="validationSample03">Mobile Number</label>
                                    <input type="number" value="<?= $phoneno ?>" minlength="10" maxlength="10"  class="form-control" id="phoneno" name="phoneno" placeholder="Mobile Number" required="">
                                    <div class="invalid-feedback">Please provide a valid number.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="col-12 col-md-12">
                                <label for="validationSample01">Top Spender</label> &nbsp;&nbsp;
                                &nbsp;&nbsp;
                                <input
                                type="radio"
                                name="top_spender"
                                value="yes"
                                required=""
                                />
                                &nbsp;&nbsp;YES &nbsp;&nbsp;
                                <input
                                type="radio"
                                name="top_spender"
                                value="no"
                                required=""
                                checked
                                />
                                &nbsp;&nbsp;NO &nbsp;&nbsp;
                                <div class="invalid-feedback">
                                    Please provide a Company's budget.
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                                <label for="validationSample01">Upsell Client</label> &nbsp;&nbsp;
                                &nbsp;&nbsp;
                                <input
                                type="radio"
                                name="upsell_client"
                                value="yes"
                                required=""
                                />
                                &nbsp;YES &nbsp;
                                <input
                                type="radio"
                                name="upsell_client"
                                value="no"
                                required=""
                                checked
                                />
                                &nbsp;NO &nbsp;
                                <div class="invalid-feedback">
                                    Please provide a Company's budget.
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                                <label for="validationSample01">Focus Funnel</label> &nbsp;&nbsp;
                                &nbsp;&nbsp;
                                <input
                                type="radio"
                                name="focus_funnel"
                                value="yes"
                                required=""
                                />
                                &nbsp;YES &nbsp;
                                <input
                                type="radio"
                                name="focus_funnel"
                                value="no"
                                required=""
                                checked
                                />
                                &nbsp;&nbsp;NO &nbsp;&nbsp;
                                <div class="invalid-feedback">
                                    Please provide a Company's budget.
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                                <label for="validationSample01">Key Client</label> &nbsp;&nbsp;
                                &nbsp;&nbsp;
                                <input
                                type="radio"
                                name="key_company"
                                value="yes"
                                required=""
                                />
                                &nbsp;YES &nbsp;
                                <input
                                type="radio"
                                name="key_company"
                                value="no"
                                required=""
                                checked
                                />
                                &nbsp;&nbsp;NO &nbsp;&nbsp;
                                <div class="invalid-feedback">
                                    Select value for Key Company
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-12" style="margin-top: 10px">
                                <label for="validationSample01">Potential Client</label> &nbsp;&nbsp;
                                &nbsp;&nbsp;
                                <input
                                type="radio"
                                name="potential_company"
                                value="yes"
                                required=""
                                />
                                &nbsp;YES &nbsp;
                                <input
                                type="radio"
                                name="potential_company"
                                value="no"
                                required=""
                                checked
                                />
                                &nbsp;&nbsp;NO &nbsp;&nbsp;
                                <div class="invalid-feedback">
                                    Please provide a Company's budget.
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                    
                </div>
                <hr>
               <center>
                 <button class="btn btn-primary" type="submit">Submit</button>
               </center>
               <hr>
            </form>
            
            
            
            
            
        </div>
        <!-- /.card -->
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>
$('#currentStatus').on('change', function b() {
var sid = document.getElementById("currentStatus").value;
$.ajax({
url:'<?=base_url();?>Menu/getremark',
type: "POST",
data: {
sid: sid
},
cache: false,
success: function a(result){
$("#remarks").html(result);
}
});
});
$('#action_type').on('change', function b() {
var aid = document.getElementById("action_type").value;
var sid = document.getElementById("currentStatus").value;
$.ajax({
url:'<?=base_url();?>Menu/getpurpose',
type: "POST",
data: {
aid: aid,
sid: sid
},
cache: false,
success: function a(result){
$("#purpose").html(result);
}
});
});
$('#purpose').on('change', function b() {
var pid = document.getElementById("purpose").value;
$.ajax({
url:'<?=base_url();?>Menu/getnextaction',
type: "POST",
data: {
pid: pid
},
cache: false,
success: function a(result){
$("#next_action").html(result);
}
});
});
$('#id_state').on('change', function b() {
var stid = document.getElementById("id_state").value;
$.ajax({
url:'<?=base_url();?>Menu/getcitybystate',
type: "POST",
data: {
stid: stid
},
cache: false,
success: function a(result){
$("#city").html(result);
}
});
});
function replaceBudget(){
var budgetdiv= document.getElementById('budgetdiv');
var id_partnerType= document.getElementById('id_partnerType').value;
if(id_partnerType=="4"){
budgetdiv.innerHTML='<label for="validationSample01">Category</label><select id="budget" class="form-control" name="budget" required><option>A</option><option>B</option><option>C</option></select>';
}
alert('budget checking '+id_partnerType);
}
var id_partnerType=document.getElementById('id_partnerType');
id_partnerType.addEventListener("change", replaceBudget);
</script>

<!-- /.row (main row) -->
</div><!-- /.container-fluid -->
</section>

</div></div></div>
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

<script>
  function generateTable() {
$('#statustablehide').show();
var selectedOption = document.getElementById("statusid").value;
var table = document.getElementById("statusTable");
table.innerHTML = ""; // Clear previous table content

// Define mappings based on selected option

var mappings = {
    "1": ["Open", "Open RPEM", "Reachout", "Tentative", "Positive NAP", "Positive", "Very Positive", "Closure"],
    "8": ["Open RPEM", "Reachout", "Tentative", "Positive NAP", "Positive", "Very Positive", "Closure"],
    //   "2": ["Reachout", "Tentative", "Positive", "Positive NAP",  "Very Positive", "Closure"],
    //   "3": ["Tentative", "Positive", "Positive NAP",  "Very Positive", "Closure"],
    //   "6": ["Positive", "Positive NAP", "Very Positive", "Closure"],
    //   "12": ["Positive NAP", "Very Positive", "Closure"],
    //   "9": ["Very Positive", "Closure"],
    // Add mappings for other options as needed
};

// Generate table rows based on mappings
var statusList = mappings[selectedOption];
if (statusList) {
    for (var i = 0; i < statusList.length - 1; i++) {
        var sname = statusList[i + 1].toString().toLowerCase().replace(/\s+/g, '');
        var row = table.insertRow();
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        cell1.innerHTML = statusList[i];
        cell2.innerHTML = statusList[i + 1];
        cell3.innerHTML = '<input type="date" class="form-control" name="' + sname + '" required id="stat_' + i + '" >';
    }

    document.getElementById("stat_0").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_1");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });

    document.getElementById("stat_1").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_2");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });

    document.getElementById("stat_2").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_3");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });

    document.getElementById("stat_3").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_4");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);

    });

    document.getElementById("stat_4").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_5");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });

} else {
    table.innerHTML = "<tr><td colspan='3'>No mapping found for selected option</td></tr>";
}

}
</script>



</body>
</html>
