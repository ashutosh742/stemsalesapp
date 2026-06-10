<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Add Target | STEM APP | WebAPP</title>
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
     .card-header,.col.card.coladjust1 select,.col.card.coladjust31 select,.container-fluid.body-content.mt-4,.each,.form-row>.col,.form-row>[class*=col-],.table-striped tbody tr:nth-of-type(odd),input.form-control,table.table-bordered.dataTable{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.scrollme{overflow-x:auto}.card-body{background:azure}.formSection{align-items:center;justify-content:center;display:flex}.form-control{width:200px!important}.modal-content{background:#faebd7}.coladjust{padding-right:5px;padding-left:5px;height:143px;align-items:center;justify-content:center;color:#000}.col.card.coladjust1{color:#000;text-align:center;align-items:center}th.sorting{background:#008b8b;color:#fff}.modal-header{background:#5f9ea0;color:#fff}.form-control.is-invalid,.was-validated .form-control:invalid{background-image:none!important}.form-control.is-valid,.was-validated .form-control:valid{background-image:none!important}.col.card.coladjust1 select{width:100%!important;height:100%!important;background:#f0fff0}.each{background:#f1f3f6;font-family:Arial,Helvetica,sans-serif;border:5px solid #eaeef5}.col.card.coladjust1.p-2.m-1{background:beige}.container-fluid.body-content.mt-4{padding:20px}.col.card.coladjust.m-1{background:#fff8dc}tbody{font-weight:500}.input-group{margin-bottom:10px;display:table-cell}.remove-btn{cursor:pointer;color:red}.coladjust121{min-height:143px;align-items:center;justify-content:center;color:#000;padding:20px}.col.card.coladjust31.p-2.m-1{align-items:center}.col.card.coladjust31 select{background:#f0fff0}.meetingbackground{background:#bdb76b}
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
          <?php if($this->session->flashdata('success_message')): ?>
          <div class="alert alert-success">
            <?php echo $this->session->flashdata('success_message'); ?>
          </div>
          <?php endif; ?>
          <div class="container-fluid">

           <?php 
           
           if($reviewData !==''){
            if(sizeof($reviewData) > 0){
           
              $reviewDatas      = $reviewData[0];
              $review_id        = $reviewDatas->id;
              $review_username  = $reviewDatas->name;
              $reviewtype       = $reviewDatas->reviewtype;
              $review_cremark   = $reviewDatas->cremark;
              $review_startt    = $reviewDatas->startt;
              $review_closet    = $reviewDatas->closet;
              $review_sdatet    = $reviewDatas->sdatet;

       
              
              // $max_start_date = date("Y-m-d");
         
              $max_start_date = date('Y-m-d', strtotime($review_closet));

         
              if ($reviewtype == 'Weekly' || $reviewtype == 'Self Weekly') {
                  $max_end_date = date("Y-m-d", strtotime("+7 days", strtotime($max_start_date)));
              } else if ($reviewtype == 'Fortnightly' || $reviewtype == 'Self Fortnightly') {
                  $max_end_date = date("Y-m-d", strtotime("+15 days", strtotime($max_start_date)));
              } else if ($reviewtype == 'Monthly' || $reviewtype == 'Self Monthly') {
                  $max_end_date = date("Y-m-d", strtotime("+1 month", strtotime($max_start_date)));
              } else if ($reviewtype == 'Quarterly' || $reviewtype == 'Self Quarterly') {
                  $max_end_date = date("Y-m-d", strtotime("+3 months", strtotime($max_start_date)));
              } else if ($reviewtype == 'Half Yearly' || $reviewtype == 'Self Half Yearly') {
                  $max_end_date = date("Y-m-d", strtotime("+6 months", strtotime($max_start_date)));
              }
              

             $reviewMessage = "Review Type : ".$reviewtype." | Start Date : ".$review_startt." | End Date : ".$review_closet." | Review Close Remarks : ".$review_cremark;
            }
           }else{
            $max_start_date = '';
            $max_end_date   = '';
            $reviewMessage  = '';
            $review_id      = '';
           }
           ?>


            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header text-white text-center" style="background: #002a1f;">
                    <h2 class="m-3">ADD TARGET</h2>
                    <hr style="background-color:white;">
                    <?php if($reviewData !==''){
                      if(sizeof($reviewData) > 0){ ?>
                        <h3>Add <?=$reviewtype?> Target </h3>
                        <hr style="background-color:white;">
                        <h6> Start Date : <?=$max_start_date?> - END Date : <?=$max_end_date?></h6>
                        <h6><?=$reviewMessage?></h6>
                     <?php }} ?>
                  </div>
                  <hr>
                  <div class="text-right">
                      <span class="p-2 bg-success"> 
                        <a href="<?= base_url()?>Menu/AddTargetListByUser" target="_BLANK">
                          Check Target List
                        </a>
                      </span>
                    </div>
                    <hr>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="each p-5 bg-info1">
                      <div class="was-validated">
                        <form action="<?=base_url();?>Menu/StoreTarget" id="add_target_form" method="post">
                          <input type="hidden" value="<?=$review_id?>" name="review_id">
                          <div class="form-row">
                            <?php $curtype_id = $user['type_id'];
                              $cuser_id = $user['user_id'];
                              $curedata = $this->Menu_model->get_userbyid($cuser_id);
                              $get_user_type = $this->Menu_model->get_user_type();
                              
                            ?>
                            <div class="col card coladjust1 p-2 m-1">
                              <lable>Select Target User Type : </lable>
                              <select name="user_type[]" class="form-control" id="user_type" multiple required >
                                <?php 
                                if($curtype_id !== 2){
                                  foreach($get_user_type as $row){
                                    if($curtype_id !== $row->id || $row->type_status == 0){
                                      continue;
                                    }else{
                                      echo '<option selected value="'.$row->id.'">'.$row->name.'</option>';
                                    }
                                  ?> 
                                <?php } } ?>
                                                             
                                 </select>
                              </select>
                              <small id="noofuser_type"></small>
                            </div>
                            
                            <div class="col card coladjust1 p-2 m-1">
                              <lable>Select Target User : </lable>
                              <select name="user_id[]" class="form-control" id="user_data1" multiple>
                                <?php 
                                if($curtype_id !== 2){ 
                                    echo '<option selected value="'.$curedata[0]->user_id.'">'.$curedata[0]->name.'</option>';
                                } ?>
                              </select>
                              <small id="noofuser"></small>
                            </div>


                            <div class="col card coladjust1 p-2 m-1">
                              <br>
                              <lable>No Of Proposal </lable>
                              <input type="number" name="num_of_proposal" class="form-control" required>
                              <hr>
                              <lable>No Of Prospective (New Lead) </lable>
                              <input type="number" name="num_of_prospective" class="form-control" required>
                              <hr>
                              <lable>Revenue </lable>
                              <input type="number" name="revenue" class="form-control" required>
                              <hr>
                              <lable>No Of School </lable>
                              <input type="number" name="school" class="form-control" required>
                              <br>
                            </div>
                          </div>


                          <hr>
                          <div class="form-row">
                            <div class="col card coladjust31 p-2 m-1 meetingbackground">
                              <br>
                              <lable>No Of RP Meeting </lable>
                              <input type="number" id="num_of_meeting" name="num_of_meeting" class="form-control" required>
                              <br>
                              <lable>RP Barg Meeting </lable>
                              <input type="number" id="num_of_barg_meeting" name="num_of_barg_meeting" class="form-control" required>
                              <hr>
                              <lable>Select Partner </lable>
                              <?php $get_partner = $this->Menu_model->get_partner(); ?>
                              <select class="form-control" id="select_partner_id" name="select_partner_id" style="width: 30% !important;">
                                <option>Select Partner</option>
                                <?php foreach($get_partner as $partner): ?>
                                <option value="<?= $partner->id; ?>"><span style="color:<?= $partner->clr;?>"><?= $partner->name; ?></span></option>
                                <?php endforeach; ?>
                              </select>
                              <br>
                            </div>
                            <div class="col card coladjust121 m-1 meetingbackground">
                              <div class="row" id="input-container" style="padding: 20px;">
                              </div>
                            </div>

                            
                          </div>

                          <hr>
                          <div class="form-row">


                            <div class="col card coladjust31 p-2 m-1 meetingbackground">
                     
                           
                              <lable>Select Category </lable>
                              <?php $get_categorys = $this->Menu_model->GetCategories(); ?>
                              <select class="form-control" id="select_category_id" name="select_category_id" style="width: 30% !important;">
                                <option>Select Category</option>
                                <?php foreach($get_categorys as $get_category): ?>
                                <option value="<?= $get_category->dataname; ?>"><span style="color:<?= $get_category->dataname;?>"><?= $get_category->name; ?></span></option>
                                <?php endforeach; ?>
                              </select>
                              <br>
                            </div>
                            <div class="col card coladjust121 m-1 meetingbackground">
                              <div class="row" id="input-container-category" style="padding: 20px;">
                              </div>
                            </div>


                          </div>


                          
                          <div class="form-row">
                            <div class="col card coladjust31 p-2 m-1 meetingbackground">
                              <br>
                              <lable>Out Station Meeting </lable>
                              <input type="number" name="out_station_meeting" id="out_station_meeting" class="form-control">
                              <hr>
                              <br>
                            </div>
                            <div class="col card coladjust121 m-1 meetingbackground">
                              <lable>Local Station Meeting </lable>
                              <input type="number" name="local_station_meeting" id="local_station_meeting" class="form-control">
                            </div>
                          </div>
                          <hr>
                          <div class="form-row">
                            <div class="col card coladjust m-1">
                              <div>
                                <lable>Target Start Date : </lable>
                                <input type="date" class="form-control" name="sdate" min="<?=$max_start_date?>" max="<?=$max_start_date?>" value="<?=$max_start_date?>" placeholder="Start Date" required >
                              </div>
                            </div>
                            <div class="col card m-1 coladjust">
                              <div>
                                <lable>Target End Date : </lable>
                                <input type="date" class="form-control" name="edate" min="<?=$max_end_date?>" max="<?=$max_end_date?>" value="<?=$max_end_date?>" placeholder="End Date" required >
                              </div>
                            </div>
                          </div>
                          <br>
                          <div class="form-row">
                            <div class="col-sm-12 p-3">
                              <center>
                                <button type="submit" id="add_target_btn" class="btn mb-2" style="background: #002a1f;color:white;" >Add Target </button>
                              </center>
                            </div>
                          </div>
                        </form>
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
      
      $(document).ready(function() {
            $('#select_partner_id').on('change', function() {
                var selectedValue = $(this).val();
                var selectedText = $("#select_partner_id option:selected").text();
                if (selectedValue !== 'Select Partner') {
                    var inputGroup = $('<div class="col input-group p-2"></div>');
                    var label = $('<label classs="p-2"></label>').text(selectedText + ':');
                    var input = $('<input type="text" class="form-control p-2" title="'+selectedText+'" name="partnerid_' + selectedValue + '">');
                    var removeButton = $('<span class="remove-btn p-2">Remove</span>');
                    removeButton.on('click', function() {
                        inputGroup.remove();
                    });
                    inputGroup.append(label, input, removeButton);
                    $('#input-container').append(inputGroup);
                }
            });


            
            $('#select_category_id').on('change', function() {
                var selectedValue = $(this).val();
                var selectedText = $("#select_category_id option:selected").text();
                if (selectedValue !== 'Select Category') {
                    var inputGroup = $('<div class="col input-group p-2"></div>');
                    var label = $('<label classs="p-2"></label>').text(selectedText + ':');
                    var input = $('<input type="text" class="form-control p-2" title="'+selectedText+'" name="categoryid_' + selectedValue + '">');
                    var removeButton = $('<span class="remove-btn p-2">Remove</span>');
                    removeButton.on('click', function() {
                        inputGroup.remove();
                    });
                    inputGroup.append(label, input, removeButton);
                    $('#input-container-category').append(inputGroup);
                }
            });


      
            $("#add_target_form").submit(function() {
             
              var num_of_meeting            = $("#num_of_meeting").val();
              var partner_id                = $("#partner_id").val();
              var out_station_meeting       = $("#out_station_meeting").val();
              var local_station_meeting     = $("#local_station_meeting").val();
      
              let values = [];
              var partnercount = 0;
              $("input[name^='partnerid_']").each(function() {
                  let title = $(this).attr("title"); 
                  let value = $(this).val();  
                  values.push(`${title}: ${value}`); 
                  partnercount += parseInt(value);
              });
              // Display the collected values in an alert

              let values_categoryid = [];
              var categorycount = 0;
              $("input[name^='categoryid_']").each(function() {
                  let category_title = $(this).attr("title"); 
                  let categoryid_value = $(this).val();  
                  values_categoryid.push(`${category_title}: ${categoryid_value}`); 
                  categorycount += parseInt(categoryid_value);
              });


              // alert(categorycount);
              // return false;

              // Display the collected values in an alert




              // alert("Collected Values:\n" + values.join("\n"));
      
              var out_local_partner_meeting = 0;
      
              if(out_station_meeting !== ''){
                out_local_partner_meeting += parseInt(out_station_meeting);
              }
      
              if(local_station_meeting !== ''){
                out_local_partner_meeting +=  parseInt(local_station_meeting);
              }
      



              if(num_of_meeting == ''){
                alert('* Please enter number of meeting');
                  return false;
                }else if (num_of_meeting < partnercount) {
                    var remaining = Math.abs(partnercount - num_of_meeting);
                    var alertmessage = 
                        "* Your meeting count is " + num_of_meeting + 
                        ", and your total partner count is " + partnercount + 
                        ". * You need to increase your meeting count by " + remaining + 
                        ", or remove " + remaining + " partner(s).";
                    alert(alertmessage);
                    return false;
                }else if (num_of_meeting > partnercount) {
                    var remaining_big = Math.abs(num_of_meeting - partnercount);
                    var alertmessage = 
                        "* Your meeting count is " + num_of_meeting + 
                        ", and your total partner count is " + partnercount + 
                        ". * You need to decrease your meeting count or add more " + remaining_big + 
                        " partner(s)";
                    alert(alertmessage);
                    return false;
                } else if (num_of_meeting < categorycount) {
                    var remaining = Math.abs(categorycount - num_of_meeting);
                    var alertmessage = 
                        "* Your meeting count is " + num_of_meeting + 
                        ", and your total category count is " + categorycount + 
                        ". * You need to increase your meeting count by " + remaining + 
                        ", or remove " + remaining + " category(s).";
                    alert(alertmessage);
                    return false;
                }else if (num_of_meeting > categorycount) {
                    var remaining_big = Math.abs(num_of_meeting - categorycount);
                    var alertmessage = 
                        "* Your meeting count is " + num_of_meeting + 
                        ", and your total category count is " + categorycount + 
                        ". * You need to decrease your meeting count or add more " + remaining_big + 
                        " category(s)";
                    alert(alertmessage);
                    return false;
                } else if (num_of_meeting > out_local_partner_meeting) {
                    var alertmessage = 
                          "* Your meeting count is " + num_of_meeting + 
                          ", and your total Out/Local Station Meeting count is " + out_local_partner_meeting+
                          ", no of meeting and total Out/Local Station Meeting does not matched";
                      alert(alertmessage);
                      return false;
                  }else if (out_local_partner_meeting > num_of_meeting) {
                    var alertmessage = 
                          "* Your meeting count is " + num_of_meeting + 
                          ", and your total Out/Local Station Meeting count is " + out_local_partner_meeting+
                          ", no of meeting and total Out/Local Station Meeting does not matched";
                      alert(alertmessage);
                      return false;
                  }
                  
            });
        });
    </script>
  </body>
</html>