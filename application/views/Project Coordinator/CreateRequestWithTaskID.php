<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create BD Request | STEM APP | WebAPP</title>
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
    <style>
      .card-body.box-profile {
      background: cadetblue;
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
      <?php 
          $reqtaskData          = $this->Menu_model->getTBLTaskByID($req_taskid);
         
          $appointmentdatetime  = $reqtaskData[0]->appointmentdatetime;
          $actontaken           = $reqtaskData[0]->actontaken;
          $purpose_achieved     = $reqtaskData[0]->purpose_achieved;
          $remarks              = $reqtaskData[0]->remarks;
          $user_id              = $reqtaskData[0]->user_id;
          $actiontype_id        = $reqtaskData[0]->actiontype_id;
          $purpose_id           = $reqtaskData[0]->purpose_id;
          $cid_id               = $reqtaskData[0]->cid_id;

          $actiontype_idData    = $this->Menu_model->getTaskAction($actiontype_id);
          $actiontype_idname    = $actiontype_idData[0]->name;

          $get_purposeNameById  = $this->Menu_model->get_purposeNameById($purpose_id);

          $spclReqData          = $this->Menu_model->GetSpecialBdrequestByTaskID($req_taskid);
         
          $client_id            = $spclReqData[0]->client_id;
          $request_id           = $spclReqData[0]->request_id;

          $client_idData        = $this->Menu_model->GetSpecialBdrequestTypeByID($client_id);
          $client_idName        = $client_idData[0]->type;

          $request_idData       = $this->Menu_model->GetSpecialBdrequestByID($request_id);
          $request_typeName     = $request_idData[0]->request_type;


          $reqCmpData           = $this->Menu_model->get_cmpbyinid($cid_id);
          $getSpecialBdrequest  = $this->Menu_model->GetSpecialBdrequest();
          $compname             = $reqCmpData[0]->compname;
          $budget               = $reqCmpData[0]->budget;
          $address              = $reqCmpData[0]->address;
          $website              = $reqCmpData[0]->website;
    
          ?>
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-12 bg-info text-center p-2">
                <h3 class="m-0">Create BD Request</h3>
                <hr>
                <span><?= '<strong>Client Name : </strong>'.$client_idName.'<strong> | Request Type : </strong>'.$request_typeName  ?></span> <br>
                <span><?= '<strong>Company Name : </strong>'.$compname.'<strong> | Appointment datetime : </strong>'.$appointmentdatetime ?></span>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row p-3">
              <div class="col-sm col-md-6 col-lg-6">
                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                    <form action="<?=base_url();?>Menu/bdrequest_New" method="post" enctype="multipart/form-data">
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <input type="hidden" name="req_taskid" value="<?=$req_taskid;?>">
                      <div class="form-group">
                        <label for="task_type">Client Type</label>
                        <?php $crequesttype =  $this->Menu_model->GetSpecialBdrequestType(); ?>
                        <select class="custom-select rounded-0" name="ctype" id="ctype">
                          <!-- <option disabled>Select Request</option> -->
                          <?php foreach($crequesttype as $ctype): 
                            if($client_idName == $ctype->type){
                                $slcted = 'selected';
                            }else{
                                $slcted = 'disabled';
                                continue;
                            }  ?>
                            <option value ="<?= $ctype->type; ?>" <?= $slcted; ?> ><?= $ctype->type; ?></option>
                          <?php endforeach;?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="task_type">Request Type</label>
             
                            <select class="custom-select rounded-0" name="request_type" id="task_type">
                                <?php foreach($getSpecialBdrequest as $requestname):
                                    if($requestname->request_type == $request_typeName){
                                        $slcted = 'selected';
                                    }else{
                                        $slcted = 'disabled';
                                        continue;
                                    }
                                    ?>
                                    <option value="<?= $requestname->request_type; ?>" <?= $slcted; ?> ><?= $requestname->request_type; ?></option>
                                <?php 
                                
                            endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="cname">Client</label>
                        <select class="custom-select rounded-0" name="cname" id="cname">
                          <option value="<?=$compname;?>"><?=$compname;?></option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="task_date">Target Date</label>
                        <input type="date" class="form-control" name="targetd" id="task_date" >
                      </div>
                      <div class="form-group">
                        <label for="remark">Request Detail</label>
                        <textarea type="text" class="form-control" name="remark" id="remark" placeholder="Request Detail"></textarea>
                      </div>
                  </div>
                </div>
              </div>
              <div class="col-sm col-md-6 col-lg-6">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
              <div id="test1" style="display: none;">
              <div class="form-group">
              <label>Project Code</label>
              <select class="custom-select rounded-0" id="pcode" name="pcode">
              </select>
              </div>
              <div class="form-group mt-5">
              <div class="alert alert-primary" role="alert" id="cinfo">
              Info
              </div>
              </div>
              </div> 
              <div id="test2" style="display: none;">
              <div class="form-group">
              <label for="ccperson">Contact Person Name</label>
              <input type="text" class="form-control" name="ccperson" id="ccperson" placeholder="Contact Person Name">
              </div>
              <div class="form-group">
              <label for="ccpmno">Contact Person Mobile No</label>
              <input type="text" class="form-control" name="ccpmno" id="ccpmno" placeholder="Contact Person Mobile No">
              </div>
              <div class="form-group">
              <label for="visitdt">Date Time</label>
              <input type="datetime-local" class="form-control" name="visitdt" id="visitdt" >
              </div>
              <div class="form-group">
              <label for="caddress">Location</label>
              <textarea type="text" class="form-control" name="caddress" id="caddress" placeholder="Client Address"></textarea>
              </div>
              <div class="form-group">
              <label for="expectation">Expectation</label>
              <input type="text" class="form-control" name="expectation" id="expectation" placeholder="Expectation">
              </div>
              </div>
              <div id="test3" style="display: none;">
              <div class="form-group">
              <label>Client Name</label>
              <input type="text" class="form-control" placeholder="Client Name" name="cnamec">
              </div>
              <div class="form-group">
              <label for="caddress">Client Address</label>
              <textarea type="text" class="form-control" name="caddress" id="caddress" placeholder="Client Address"></textarea>
              </div>
              <div class="form-group">
              <label for="ccperson">Contact Person Name</label>
              <input type="text" class="form-control" name="ccperson" id="ccperson" placeholder="Contact Person Name">
              </div>
              <div class="form-group">
              <label for="ccpmno">Contact Person Mobile No</label>
              <input type="text" class="form-control" name="ccpmno" id="ccpmno" placeholder="Contact Person Mobile No">
              </div>
              <div class="form-group">
              <label for="visitdt">Demo Date Time</label>
              <input type="datetime-local" class="form-control" name="visitdt" id="visitdt" >
              </div>
              <div class="form-group">
              <label for="visitdt">Metting Type</label>
              <select class="custom-select rounded-0" name="m_id" id="m_id">
              <option value="">Metting Type</option>
              <option value="Online">Online</option>
              <option value="Offline">Offline</option>
              </select>
              </div>
              <div class="form-group">
              <label for="expectation">Expectation</label>
              <input type="text" class="form-control" name="expectation" id="expectation" placeholder="Expectation">
              </div>
              </div>
              <div id="test4" style="display: none;">
              </div>
              <div id="test5" style="display: none;">
              </div>
              <div id="test6" style="display: none;">
              <div class="form-group">
              <label>Identification Type</label>
              <select class="custom-select rounded-0" name="idetype">
              <option>Physical</option>
              <option>virtual</option>
              <option>Mix</option>
              </select>
              </div>
              <div class="form-group">
              <label>Type of School</label>
              <select class="custom-select rounded-0" name="tyschool">
              <option>Govt.</option>
              <option>Private</option>
              <option>Mix</option>
              </select>
              </div>
              <div class="form-group">
              <label for="noschool">NO of School</label>
              <input type="text" class="form-control" name="noschool" id="noschool" placeholder="NO of School">
              </div>
              <div class="form-group">
              <label for="location">Location</label>
              <textarea class="form-control" name="location" id="location" placeholder="Location"></textarea>
              </div>
              <div class="form-group">
              <label>Attach NGO Letter (only pdf)</label>
              <input type="file" class="form-control" name="filname">
              </div>
              <div class="form-group">
              <label>School Letter Required</label>
              <select class="custom-select rounded-0" name="sletter">
              <option>Yes</option>
              <option>No</option>
              </select>
              </div>
              <div class="form-group">
              <label>DM/DEO Letter Required</label>
              <select class="custom-select rounded-0" name="dmletter">
              <option>Yes</option>
              <option>No</option>
              </select>
              </div>
              <div class="form-group">
              <label>Client Validation</label>
              <select class="custom-select rounded-0" name="svalidation">
              <option>Physical</option>
              <option>Telephonic</option>
              </select>
              </div>
              </div>
              </div>
              </div>
              <button type="submit" class="btn btn-primary" onclick="this.form.submit(); this.disabled = true;">Submit</button>
              </form>
              </div> 
            </div>
          </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type='text/javascript'>

$( document ).ready(function() {
    console.log( "ready!" );
    var ctype = document.getElementById("ctype").value;
  
        var ab = $('#task_type').val();
         
             if(ab=="Report" || ab=="RTTP" || ab=="DIY" || ab=="MnE"){
              $("#test1").show();
              $("#test2").hide();
              $("#test3").hide();
              $("#test4").hide();
              $("#test5").hide();
              $("#test6").hide();
             }
             if(ab=="New Client Report"){
              $("#test1").hide();
              $("#test2").show();
              $("#test3").hide();
              $("#test4").hide();
              $("#test5").hide();
              $("#test6").hide();
             }
             if(ab=="New client school visit"){
              $("#test1").hide();
              $("#test2").show();
              $("#test3").hide();
              $("#test4").hide();
              $("#test5").hide();
              $("#test6").hide();
             }
             if(ab=="OnBoardVisit"){
              $("#test1").hide();
              $("#test2").hide();
              $("#test3").show();
              $("#test4").hide();
              $("#test5").hide();
              $("#test6").hide();
             }
             if(ab=="Demo"){
              $("#test1").hide();
              $("#test2").show();
              $("#test3").hide();
              $("#test4").hide();
              $("#test5").hide();
              $("#test6").hide();
             }
             if(ab=="Inauguration"){
              $("#test1").hide();
              $("#test2").hide();
              $("#test3").hide();
              $("#test4").hide();
              $("#test5").show();
              $("#test6").hide();
             }
             if(ab=="School Identification"){
              $("#test1").hide();
              $("#test2").hide();
              $("#test3").hide();
              $("#test4").hide();
              $("#test5").hide();
              $("#test6").show();
             }
          


          
});
        //   $('#ctype').on('change', function c() {
        //   var ctype = document.getElementById("ctype").value;
        //   $.ajax({
        //   url:'<?=base_url();?>Menu/getctot',
        //   type: "POST",
        //   data: {
        //   ctype: ctype
        //   },
        //   cache: false,
        //   success: function a(result){
        //   $("#task_type").html(result);
        //   }
        //   });
        //   });
          
          
          
          $('#pcode').on('change', function a() {
          var pcode = document.getElementById("pcode").value;
          $.ajax({
          url:'<?=base_url();?>Menu/getcinfo',
          type: "POST",
          data: {
          pcode: pcode
          },
          cache: false,
          success: function b(result){
          $("#cinfo").html(result);
          }
          });
          });
          
          
          
          $('#cname').on('change', function a() {
          var cname = document.getElementById("cname").value;
          $.ajax({
          url:'<?=base_url();?>Menu/getpcode',
          type: "POST",
          data: {
          cname: cname
          },
          cache: false,
          success: function b(result){
          $("#pcode").html(result);
          }
          });
          });
          
        //   $('#task_type').on('change', function() {
        //      var ab = this.value;
             
        //      if(ab=="Report" || ab=="RTTP" || ab=="DIY" || ab=="MnE"){
        //       $("#test1").show();
        //       $("#test2").hide();
        //       $("#test3").hide();
        //       $("#test4").hide();
        //       $("#test5").hide();
        //       $("#test6").hide();
        //      }
        //      if(ab=="New Client Report"){
        //       $("#test1").hide();
        //       $("#test2").show();
        //       $("#test3").hide();
        //       $("#test4").hide();
        //       $("#test5").hide();
        //       $("#test6").hide();
        //      }
        //      if(ab=="New client school visit"){
        //       $("#test1").hide();
        //       $("#test2").show();
        //       $("#test3").hide();
        //       $("#test4").hide();
        //       $("#test5").hide();
        //       $("#test6").hide();
        //      }
        //      if(ab=="OnBoardVisit"){
        //       $("#test1").hide();
        //       $("#test2").hide();
        //       $("#test3").show();
        //       $("#test4").hide();
        //       $("#test5").hide();
        //       $("#test6").hide();
        //      }
        //      if(ab=="Demo"){
        //       $("#test1").hide();
        //       $("#test2").show();
        //       $("#test3").hide();
        //       $("#test4").hide();
        //       $("#test5").hide();
        //       $("#test6").hide();
        //      }
        //      if(ab=="Inauguration"){
        //       $("#test1").hide();
        //       $("#test2").hide();
        //       $("#test3").hide();
        //       $("#test4").hide();
        //       $("#test5").show();
        //       $("#test6").hide();
        //      }
        //      if(ab=="School Identification"){
        //       $("#test1").hide();
        //       $("#test2").hide();
        //       $("#test3").hide();
        //       $("#test4").hide();
        //       $("#test5").hide();
        //       $("#test6").show();
        //      }
        //   });
        </script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
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
  </body>
</html>