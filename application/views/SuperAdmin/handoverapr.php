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
   .custom-btn {
            /* width: 130px;
            height: 40px; */
            color: #fff;
            border-radius: 5px;
            /* padding: 10px 25px; */
            font-family: Lato, sans-serif;
            font-weight: 500;
            background: 0 0;
            cursor: pointer;
            transition: .3s;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
            outline: 0;
        }

        .btn-11 {
            border: none;
            background: #212ffb;
            background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
            color: #fff;
            overflow: hidden;
            width: fit-content;
        }

        .btn-11:hover {
            text-decoration: none;
            color: #fff;
            opacity: .7;
        }

        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: 3s ease-in-out infinite shiny-btn1;
        }

        .btn-11:active {
            box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }

        @keyframes shiny-btn1 {
            0% { transform: scale(0) rotate(45deg); opacity: 0; }
            80% { transform: scale(0) rotate(45deg); opacity: .5; }
            81% { transform: scale(4) rotate(45deg); opacity: 1; }
            100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
      
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
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
       <h5 class="card-header text-center">
        <?php if ($this->session->flashdata('success_message')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
          <?= $this->session->flashdata('success_message'); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
          <?= $this->session->flashdata('error_message'); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
      </h5>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header text-center">
                <h3>Handover Request Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <input type="hidden" id="uuid" value="<?=$uid?>">
                        <div class="table-responsive">
                            <div class="table-responsive text-nowrap">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="thead-dark">
                                        <tr>
                                              <th>SNo</th>
                                              <th>Handover ID</th>
                                              <th>Client Name</th>
                                              <th>💳 Sales CID</th>
                                              <th>Mediator</th>
                                              <th>No of School</th>
                                              <th>Location</th>
                                              <th>City</th>
                                              <th>State</th>
                                              <th>SPD Identify By</th>
                                              <th>Infrastructure</th>
                                              <th>Logo</th>
                                              <th>Contact Person</th>
                                              <th>Mobile No</th>
                                              <th>Language</th>
                                              <th>Ex Installation Date</th>
                                              <th>Project Tenure</th>
                                              <th>Work Order No</th>
                                              <th>Total Budget</th>
                                              <th>PAN NO</th>
                                              <th>GST NO</th>
                                              <th>Remark</th>
                                              <th>Payment Term</th>
                                              <th>Other Remark</th>
                                              <th>MOU Date</th>
                                              <th>MOU</th>
                                              <th>🕒 Created At</th>
                                              <th>♻️ Updated At</th>

                                               <?php if(in_array($user['type_id'],[1,2])){?> 
                                               <th>⏰ Add PRO</th>
                                               <?php } ?>


                                              <th>Approval</th>
                                              <th>Edit</th>
                                              <th>Reject</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php 
                                        $bds=[];
                                        $j=0;
                                           $i=1; 
                                        foreach($bdid as $bd){
                                            $bds[$j]=$bd->user_id;
                                        $j++;}
                                        
                                      
                                      
                                        foreach($client as $d){
                                             
                                          $cid = $d->cid;
                                          if (in_array($d->bd_id, $bds))
                                          {
                                             
                                      ?>
                                      
                                    <tr>
                                        <td><input type="hidden" name="id[]" value="<?=$i?>"><?=$i?></td>
                                        <td>
                                            <a target="_BLANK" href="https://stemoppapp.in/Menu/ProjectProfileDetails/<?=$d->cid?>/<?=$user['user_id']?>">
                                            <?= $d->cid?>
                                            </a>
                                        </td>
                                        <td><input type="hidden" name="client_name[]" value="<?=$d->client_name?>"><?=$d->client_name?></td>
                                           <td><?php
                                           if(!empty($d->sales_cid)){
                                            ?>
                                            <a target='_blank' href="https://stemapp.in/Menu/CompanyDetails/<?= $d->sales_cid; ?>"><?= $d->sales_cid; ?></a>
                                            <?php
                                           }else{
                                            ?>
                                            <a target='_blank' href="https://stemapp.in/Menu/CompanyDetails/<?= $d->client_id; ?>"><?= $d->client_id; ?></a>
                                            <?php 
                                           }
                                           ?></td>
                                        <td><input type="hidden" name="mediator[]" value="<?=$d->mediator?>"><?=$d->mediator?></td>
                                        <td><input type="hidden" name="noofschool[]" value="<?=$d->noofschool?>"><a href="spddetail/<?=$cid?>"><?=$d->noofschool?></a></td>
                                        <td><input type="hidden" name="location[]" value="<?=$d->location?>"><?=$d->location?></td>
                                        <td><input type="hidden" name="city[]" value="<?=$d->id?>"><?=$d->city?></td>
                                        <td><input type="hidden" name="state[]" value="<?=$d->id?>"><?=$d->state?></td>
                                        <td><input type="hidden" name="spd_identify_by[]" value="<?=$d->spd_identify_by?>"><?=$d->spd_identify_by?></td>
                                        <td><input type="hidden" name="infrastructure[]" value="<?=$d->infrastructure?>"><?=$d->infrastructure?></td>
                                        <td><input type="hidden" name="logo[]" value="<?=$d->logo?>">
                                        
                                        <?php $logo = $d->logo; $logo = preg_split ("/\,/", $logo);
                                                $l=sizeof($logo);
                                                for($i=0;$i<$l;$i++){?>
                                            
                                            <a href="../<?=$logo[$i]?>" target="_blank">LOGO</a>
                                            <?php } ?>
                                        
                                        </td>
                                        <td><input type="hidden" name="contact_person[]" value="<?=$d->contact_person?>"><?=$d->contact_person?></td>
                                        <td><input type="hidden" name="cp_mno[]" value="<?=$d->cp_mno?>"><?=$d->cp_mno?></td>
                                        <td><input type="hidden" name="language[]" value="<?=$d->language?>"><?=$d->language?></td>
                                        <td><input type="hidden" name="expected_installation_date[]" value="<?=$d->expected_installation_date?>"><?=$d->expected_installation_date?></td>
                                        <td><input type="hidden" name="project_tenure[]" value="<?=$d->project_tenure?>"><?=$d->project_tenure?></td>
                                        <td><?=$d->wono?></td>
                                        <td><?=$d->tbudget?></td>
                                        <td><?=$d->panno?></td>
                                        <td><?=$d->gstno?></td>
                                        <td><?=$d->remark?></td>
                                        <td><?=$d->payterm?></td>
                                        <td><?=$d->srfinovice?></td>
                                        <td><?=$d->moudate?></td>
                                        <td>
                                            <?php $mou = $d->mou; $mou = preg_split ("/\,/", $mou);
                                                $l=sizeof($mou);
                                                for($i=0;$i<$l;$i++){?>
                                            
                                            <a href="../<?=$mou[$i]?>" target="_blank">MOU</a>
                                            <?php } ?>
                                        
                                        
                                        </td>
                                         <td><?= $d->created_at ?></td>
                                        <td><?= $d->updated_at ?></td>

                                         <?php if(in_array($user['type_id'],[1,2])){?> 
                                            <td>
                                                <span class='custom-btn btn-11 partnearray p-1' onclick="UpdatePROMapping(<?=$d->cid?>)">Add PRO</span>
                                            </td>
                                        <?php } ?>


                                       <td>
                                            <a class="bg-success p-2" href="handApr/<?=$cid?>" 
                                            onclick="return confirm('Are you sure you want to approve this handover?');">
                                            <b>Approval</b>
                                            </a>
                                        </td>

                                        <td>
                                            <a class="bg-warning p-2" href="handEdit/<?=$cid?>" 
                                            onclick="return confirm('Do you want to edit this handover?');">
                                            <b>Edit</b>
                                            </a>
                                        </td>

                                        <td>
                                            <a class="bg-danger p-2" href="handDelete/<?=$cid?>" 
                                            onclick="return confirm('Are you sure you want to reject this handover? This action cannot be undone.');">
                                            <b>Reject</b>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++; }} ?>
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
                       <div class="card-header bg-info">Delete Handover</div>
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

<div id="aprh" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Approval Handover</div>
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
                       <div class="card-header bg-info">Delete Handover</div>
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




       <?php if(in_array($user['type_id'],[1,2])){?> 
                  <div class="modal fade" id="AssignYourselfCenterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header" style="background-color: #d1e7ff;">
                            <h5 class="modal-title text-center" style="color: red;" id="exampleModalLongTitle"><b><u>Add PRO </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" style="background-color: #ffc4c45c;">
                         <form id="assignForm" action="<?=base_url();?>Menu/AddPROinProjectCode" method="post">
                              <input type="hidden" id="request_id" name="request_id" required value="">

                              <?php 
                              $allPROLists =  $this->Menu_model->getOperationDepartmentUserByUID(20);
                              ?>
                              <hr>
                              <label>Select PRO</label>
                              <select id="select_pro" class="form-control" name="select_pro" required>
                                  <option value="" disabled selected>--Select--</option>
                                  <?php foreach($allPROLists as $allPROList): ?>
                                    <option value="<?= $allPROList->id; ?>"><?= $allPROList->fullname; ?></option>
                                  <?php endforeach; ?>
                              </select>

                              <br>
                              <div class="form-group text-center">
                                  <button type="submit" class="custom-btn btn-11 p-2">Update PRO</button>
                              </div>
                          </form>
                          </div>
                        </div>
                      </div>
                    </div>
          <?php } ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
   function UpdatePROMapping(id){
                $('#AssignYourselfCenterModal').modal('show');
                $('#request_id').val(id);
            }
function Apr(aprid) {
document.getElementById("cmpida").value = cmpid;
$.ajax({
url:'<?=base_url();?>Menu/handApr',
type: "POST",
data: {
aprid: aprid,
admid: admid
},
cache: false,
success: function b(result){
$("#cinfo").html(result);
}
});
}

$('[id^="Edit"]').on('click', function() {
        var cmpid = this.value;
    $('#edith').modal('show');
    document.getElementById("cmpida").value = cmpid;
});
$('[id^="Delete"]').on('click', function() {
        var cmpid = this.value;
    $('#deleteh').modal('show');
    document.getElementById("cmpida").value = cmpid;
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