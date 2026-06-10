<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM APP | WebAPP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css" rel="stylesheet">
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

        .custom-border-card {
            position: relative;
            border: 0px solid #ccc;
            border-radius: 8px;
            padding: 5px;
        }
        .custom-border-text {
            position: absolute;
            top: 70%;
            left: -10px; /* Adjust this value to position the text */
            background-color: white; /* Background color to cover the border */
            padding: 0 5px; /* Add some padding to the text */
            transform: translateY(-50%) rotate(270deg); /* Rotate the text 270 degrees */
            transform-origin: 0% 50%; /* Set the rotation origin to the left center */
        }
    </style>  
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  

  <!-- Navbar -->
  <?php require('nav.php');?>
  <?php  include 'addlog.php';?>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Company Details</h1>
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
       <div class="row p-3">
          <div class="col-sm col-lg-8">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <?php foreach($cd as $c){ ?>
                <div class="row">
                    <div class="col-sm col-lg-6"><h4><?=$c->compname?></h4></div>
                    <div class="col-sm col-lg-3"><h5><?=$cstatus=$cstatus[0]->name?></h5></div>
                    <!--<div class="col-sm col-lg-3"><button type="button" id="add_createact" class="btn btn-light"><b>+</b> Create Task</button></div>-->
                </div>
               <hr>
               <div class="row">
                    <div class="col-sm col-lg-4">Total Logs:</div>
                    <div class="col-sm col-lg-8"><?=sizeof($tblc)?></div>
                    <div class="col-sm col-lg-4">BD Assigned:</div>
                    <div class="col-sm col-lg-8"><?php $mbd = $this->Menu_model->get_userbyid($mainbd);echo $mbd[0]->name?></div>
                    <div class="col-sm col-lg-4">PST Assigned:</div>
                    <div class="col-sm col-lg-8"><?php if($apst){$apst = $this->Menu_model->get_userbyid($apst);echo $apst[0]->name;}else{echo '';}?></div>
                    <div class="col-sm col-lg-4">Conversion Details:</div>
                    <div class="col-sm col-lg-8">
                        <?php $sconbydate = $this->Menu_model->get_sconbydate($ciid);
                        foreach($sconbydate as $st){?>
                        <span class="badge p-2 m-1" style="background-color: <?=$st->sclr?>;color:#FFFFFF;"><?=$st->bstatus?>-<?=$st->astatus?> (<?=$st->time_difference?>)</span></span>
                        <?php } ?>
                    </div>
                    <?php if(sizeof($ccd)>0){$contactperson=$ccd[0]->contactperson;$phoneno=$ccd[0]->phoneno;$emailid=$ccd[0]->emailid;$designation=$ccd[0]->designation;}
                    else{$contactperson="";$phoneno="";$emailid="";$designation="";}?>
                    <div class="col-sm col-lg-4">Email:</div>
                    <div class="col-sm col-lg-8"><?=$emailid?></div>
                    
                    <div class="col-sm col-lg-4">Address:</div>
                    <div class="col-sm col-lg-8"><?=$c->address?></div>
                    
                    <div class="col-sm col-lg-4">State:</div>
                    <div class="col-sm col-lg-8"><?=$c->state?></div>
                
                    <div class="col-sm col-lg-4">City:</div>
                    <div class="col-sm col-lg-8"><?=$c->city?></div>
                    
                    <div class="col-sm col-lg-4">Country:</div>
                    <div class="col-sm col-lg-8"><?=$c->country?></div>
                    
                    <div class="col-sm col-lg-4">Website:</div>
                    <div class="col-sm col-lg-8"><?=$c->website?></div>
                    
                    <div class="col-sm col-lg-4">Phone No:</div>
                    <div class="col-sm col-lg-8"><?=$phoneno?>
                        <span>
                            <a href="tel:+91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                                <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;">
                            </a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                                <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;">
                            </a>
                        </span>
                    </div>
                    
                    
                    <div class="col-sm col-lg-4">Social Network :</div>
                    <div class="col-sm col-lg-8 p-1">
                        <span>
                            <a href="tel:+91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:25px;">
                                <img src="https://stemapp.in/uploads/SocialIcon/1.png" style="height:25px;">
                            </a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:25px;">
                                <img src="https://stemapp.in/uploads/SocialIcon/2.png" style="height:25px;">
                            </a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:25px;">
                                <img src="https://stemapp.in/uploads/SocialIcon/3.png" style="height:25px;">
                            </a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:25px;">
                                <img src="https://stemapp.in/uploads/SocialIcon/4.png" style="height:25px;">
                            </a>
                        </span>
                    </div>
                
                </div>
              </div>
              <div class="card-footer text-muted p-3">
                  <b>Logs</b>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="../PrimaryContact/<?=$c->id?>"><b>Edit Contact</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            
            </div>
            <?php } ?>
          <!-- /.col -->
          <div class="col-sm col-lg-4 showdata">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <div class="row">
                    <div class="col-sm col-lg-5"><h4>Contact</h4></div>
                    <div class="col-sm col-lg-7">
                      <!-- <button type="button" id="add_cont" class="btn btn-light" value="<?=$c->id?>" style="float:right;"><b>+</b> Add Contact</button> -->
                      <a class="btn btn-primary" href="<?=base_url().'Menu/AddPrimaryContactDetailsNew/'.$target_cid;?>">
                          Add OR Edit Contact 
                      </a>
                    </div>
                </div><hr>
                <?php foreach($pccd as $d){$contactperson=$d->contactperson;$phoneno=$d->phoneno;$emailid=$d->emailid;$designation=$d->designation;$type=$d->type;?>
                    <div class="col-sm"><b>(<?=$type?>)</b></div>
                    <div class="col-sm">Name : <?=$contactperson?></div>
                    <div class="col-sm">Designation : <?=$designation?></div>
                    <div class="col-sm">Email id : <?=$emailid?></div>
                    <div class="col-sm">Phone No : <?=$phoneno?>
                        <span>
                            <a href="tel:+91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                            <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;"></a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                            <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;"></a>
                        </span>
                        
                    </div>
                <?php } ?>
              </div>
              </div>
              <!-- /.card-header -->
              <?php if($tbllast[0]->status_id==6){?>
              <div class="card card-success card-outline">
              <div class="card-body box-profile">
                  <div class="row">
                    <div class="col-sm col-lg-5"><h4>Positive Status</h4></div>
                </div><hr>
                <?php foreach($init as $in){ ?>
                    <div class="col-sm">Proposal Shared BY : </div>
                    <div class="col-sm">No of School : <?=$in->noofschools?></div>
                    <div class="col-sm">Final Budget : <?=$in->fbudget?></div>
                <?php } ?>
              </div>
              </div>
              <?php } ?>
              <!-- /.card-header -->
              
            </div>
            <!-- /.card -->
  </div>
  
  
  <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Completed Task Logs</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  
                  <button id="grid-view-btn" class="btn border">Grid View</button>
                  <button id="list-view-btn" class="btn border">Xls View</button>
                    <div class="container-fluid card p-5" id="data-container">
                        <div class="row text-center" id="grid-view">
                            <?php
                            $oldd = "";$newd="";
                            $gdata = $this->Menu_model->get_utaskbycmp($ciid);
                            foreach ($gdata as $gd) { if($oldd==''){$oldd = $gd->updateddate;} $newd = $gd->updateddate;?>
                            
                            
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                        <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                                            <div class="custom-border-card">
                                            <span class="custom-border-text"><h5>Time Diff Between to Task : <?=$this->Menu_model->timediff($oldd, $newd);?></h5></span>
                                            <div class="card-body">
                                            
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
                                            Remark/MOM <br><b><?=$gd->remarks?><?=$gd->mom?></b><hr>
                                            <div class="rounded-circle bg-danger" style="position: absolute;
                                                bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            <div class="rounded-circle bg-danger" style="position: absolute;
                                                bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $oldd = $gd->updateddate;} ?>
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
                                            <th>MOM</th>
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
                                         <td><?=$gd->mom?></td>
                                     </tr>
                                     <?php $i++;} ?>
                                  </tbody>
                                </table>
                        </div>
                    </div>

                        <script>
                            document.getElementById("grid-view-btn").addEventListener("click", function () {
                                document.getElementById("grid-view").style.display = "block";
                                document.getElementById("list-view").style.display = "none";
                                document.getElementById("list-view-btn").classList.remove('btn-info');
                                document.getElementById("grid-view-btn").classList.add('btn-info');
                            });
                        
                            document.getElementById("list-view-btn").addEventListener("click", function () {
                                document.getElementById("grid-view").style.display = "none";
                                document.getElementById("list-view").style.display = "block";
                                document.getElementById("grid-view-btn").classList.remove('btn-info');
                                document.getElementById("list-view-btn").classList.add('btn-info');
                            });

                        </script>
                  
                  
                  
              </div>
              
              
              <div class="col-12">
    <div class="card">
              <div class="card-header bg-danger">
                <h3 class="card-title"><b>Pending Task Logs</b></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>								
                                            <th>Sr Number</th>
                                            <th>Current Action</th>
                                            <th>Created Date</th>
                                            <th>Task For</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php $i=1; foreach($tblc as $tb){
                                          $tid = $tb->id;
                                          $uid = $tb->user_id;
                                          $aid = $tb->actiontype_id;
                                          $ui=$this->Menu_model->get_userbyid($uid);
                                          $ai=$this->Menu_model->get_actionbyid($aid);
                                           if($tb->nextCFID=='0'){
                                      ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$ai[0]->name?></td>
                                        <td><?=$tb->updateddate?></td>
                                        <td><?=$ui[0]->name?></td>
                                    </tr>
                                    <?php $i++;}} ?>
                                  </tbody>
                                </table>
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
    
    
<div id="ecomp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Edit Company</div>
                       
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/editcmpbybd')?>
                           <input type="hidden" name="cid" id="cmpid">
                           <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample04">State</label>
                                <select name="state" id="id_state" required class="form-control">
                                    <option value="">Select State</option>
                                    <?php $states=$this->Menu_model->get_states();
                                    foreach($states as $st){?>
                                        <option><?=$st->state?></option>
                                    <?php }?>
                                </select>
                                <div class="invalid-feedback">Please provide a valid state.</div>
                                <div class="valid-feedback">Looks good!</div>
                            <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample04">City</label>
                                <select id="city" class="form-control" name="city" required>
                                </select>
                                <div class="invalid-feedback">Please provide a valid city.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample04">Address</label>
                                <textarea name="address" class="form-control"></textarea>
                                <div class="invalid-feedback">Please provide a valid city.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample04">Website</label>
                                <textarea name="website" class="form-control"></textarea>
                                <div class="invalid-feedback">Please provide a valid city.</div>
                                <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample04">partner_type</label>
                                <textarea name="partnerType_id" class="form-control"></textarea>
                                <div class="invalid-feedback">Please provide a valid partner_type.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-12 mb-12">
                                <label for="validationSample04">Budget</label>
                                <textarea name="budget" class="form-control"></textarea>
                                <div class="invalid-feedback">Please provide a valid Budget.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-12 col-md-12 mb-12">
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
                            />
                            &nbsp;&nbsp;NO &nbsp;&nbsp;
                            <input
                              type="radio"
                              name="top_spender"
                              value=""
                              required=""
                              checked
                            />
                            &nbsp;&nbsp;NO Change &nbsp;&nbsp;
                          </div>
                        <div class="col-12 col-md-12" style="margin-top: 10px">
                       <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                       </form>
                        </div>
                        </div>
                            </div></div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>   

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
$('[id^="editcomp"]').on('click', function() {
        var cmpid = this.value;
    $('#ecomp').modal('show');
    document.getElementById("cmpid").value = cmpid;
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
<script src="<?=base_url();?>assets/js/myjs.js"></script>

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