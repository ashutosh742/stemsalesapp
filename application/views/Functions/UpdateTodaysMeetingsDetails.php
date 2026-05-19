<?php date_default_timezone_set("Asia/Calcutta"); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Todays Meetings Details | STEM APP | WebAPP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- Font Awesome -->
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
   .main-timeline .timeline-year,.main-timeline .title{color:#fff;background:var(--color2);font-weight:400}body{background-color:#e3f2e1;font-family:Arial,sans-serif}.container{display:flex;flex-wrap:wrap;justify-content:center;align-items:center;padding:20px}.box{background-color:#f4f9f2;margin:10px;border-radius:20px;box-shadow:4px 4px 12px rgba(0,0,0,.1);display:flex;justify-content:center;align-items:center;transition:transform .3s}.card{background-color:#e2e8f0!important}.comapnydetails,.formdata{padding:10px}:root{--color1:#2E3C47;--color2:#202C36}.main-timeline{font-family:Poppins,sans-serif}.main-timeline:after{content:'';display:block;clear:both}.main-timeline .timeline{border-right:10px solid var(--color1);width:50%;padding:10px 20px 10px 0;box-shadow:10px 0 var(--color2);float:left}.main-timeline .timeline-content{text-align:center;display:block;position:relative;border-radius:10px;box-shadow:rgba(9,30,66,.25) 0 1px 1px,rgba(9,30,66,.13) 0 0 1px 1px}.main-timeline .timeline-content:hover{text-decoration:none}.main-timeline .timeline-content:after,.main-timeline .timeline-content:before{content:"";background:var(--color2);width:80px;height:3px;transform:translateY(-50%);position:absolute;top:50%;right:-120px}.main-timeline .timeline-content:after{width:15px;height:15px;border-radius:50%;right:-125px}.main-timeline .timeline-year{font-size:40px;padding:3px 20px 2px;border-radius:100px;border:5px solid var(--color1);transform:translateY(-50%);position:absolute;top:50%;right:-300px}.main-timeline .title{font-size:22px;text-transform:uppercase;letter-spacing:1px;padding:10px 10px 9px;margin:0;border-radius:10px 10px 0 0}.main-timeline .description{color:#555;background:linear-gradient(#f1f1f1,#d9d9d9);font-size:15px;letter-spacing:1px;padding:20px 10px;border-radius:0 0 10px 10px;margin:0}.main-timeline .timeline:nth-child(2n){float:right;border:none;border-left:10px solid var(--color2);box-shadow:-10px 0 var(--color1);padding:10px 0 10px 20px;margin:0 0 0 10px}.main-timeline .timeline:nth-child(2n) .timeline-content:before{right:auto;left:-120px}.main-timeline .timeline:nth-child(2n) .timeline-content:after{right:auto;left:-125px}.main-timeline .timeline:nth-child(2n) .timeline-year{right:auto;left:-300px}.main-timeline .timeline:nth-child(2){--color1:#9AE365;--color2:#81CA47}.main-timeline .timeline:nth-child(3){--color1:#F15B53;--color2:#D63C38}.main-timeline .timeline:nth-child(4){--color1:#7c49b7;--color2:#5b2499}@media screen and (max-width:767px){.main-timeline .timeline,.main-timeline .timeline:nth-child(2n){width:100%;padding:100px 0 20px 20px;margin:0 0 0 10px;box-shadow:-10px 0 var(--color2);border:none;border-left:10px solid var(--color1);float:none}.main-timeline .timeline-content:after,.main-timeline .timeline-content:before,.main-timeline .timeline:nth-child(2n) .timeline-content:after,.main-timeline .timeline:nth-child(2n) .timeline-content:before{top:-50px;left:-20px;width:50px}.main-timeline .timeline-content:after,.main-timeline .timeline:nth-child(2n) .timeline-content:after{width:15px;right:auto;left:15px}.main-timeline .timeline-year,.main-timeline .timeline:nth-child(2n) .timeline-year{transform:translateX(-50%);top:-85px;left:50%;right:auto}}img.billesimage{padding:5px;border:1px solid #5f9ea0;box-shadow:rgba(0,0,0,.17) 0 -23px 25px 0 inset,rgba(0,0,0,.15) 0 -36px 30px 0 inset,rgba(0,0,0,.1) 0 -79px 40px 0 inset,rgba(0,0,0,.06) 0 2px 1px,rgba(0,0,0,.09) 0 4px 2px,rgba(0,0,0,.09) 0 8px 4px,rgba(0,0,0,.09) 0 16px 8px,rgba(0,0,0,.09) 0 32px 16px}.cat1{background:#effaf8!important}.cat2{background:#fef2f4!important}.cat3{background:#fffaef!important}.cat4{background:#eefbf5!important}.cat5{background:#f7f3ff!important}.cat6{background:#fff7ef!important}.cat7{background:#f1fbff!important}.cat8{background:#fff0f8!important}
    div.preview{
    margin-top: 40px!important;
}.main-timeline .timeline-year, .main-timeline .title
 {
    background: #01434d;
}
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <style>
      .select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: #000000!important;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
     <?php $this->load->view($dep_name.'/nav.php');?>
      <div class="content-wrapper">
        <div class="content-header">
          <div class="container-fluid">
            <?php
              if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php
              if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header text-center" style="background:#01434d;">
                    <h3 class="text-center text-white">
                     <hr> <center><b>📝💼 Update Meetings Expense Details</b></center><hr>
                    </h3>
                    <small class="text-white">
                      Easily record, track, and verify all meeting-related expenses 💰. Manage company name 🏢, dates 📅, bills 📎, remarks 📝, and approvals ✅ seamlessly for transparent, accurate, and efficient expense reporting.
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
              <div class="col-md-12">
                <?php 
                  $meetDatacnt = sizeof($meetData);
                  if($meetDatacnt > 0){
                    ?>
                <div class="text-center cat8 p-3" style="box-shadow: rgba(14, 63, 126, 0.06) 0px 0px 0px 1px, rgba(42, 51, 70, 0.03) 0px 1px 1px -0.5px, rgba(42, 51, 70, 0.04) 0px 2px 2px -1px, rgba(42, 51, 70, 0.04) 0px 3px 3px -1.5px, rgba(42, 51, 70, 0.03) 0px 5px 5px -2.5px, rgba(42, 51, 70, 0.03) 0px 10px 10px -5px, rgba(42, 51, 70, 0.03) 0px 24px 24px -8px;">
                  <h3 style="color: #ff007a;">Total <?= $meetDatacnt ?> Meetings Expense Pending</h3>
                </div>
                <hr>
                <?php
                  if($meetDatacnt == 1){?>
                <style>
                  .main-timeline .timeline{
                  width: 70%!important;
                  }
                </style>
                <?php } ?>
                <?php if($meetDatacnt > 1){ ?>
                <style>
                  .main-timeline .timeline{
                  width: 100%;
                  }
                </style>
                <?php } ?>
                <?php
                  if($meetDatacnt >0){
                  ?>
                <style>
                  .main-timeline .timeline{
                  width: 100%;
                  }
                </style>
                <?php } ?>
                <form method="post" action="<?=base_url();?>Menu/AddCashSpentInMeetings" enctype="multipart/form-data">
                  <div class="was-validated">
                    <div class="main-timeline">
                      <?php  $i=1;$jk=1; foreach($meetData as $data): ?>
                      <?php 
                        $start_timestamp = strtotime($data->startm);
                        $end_timestamp = strtotime($data->closem);
                        $time_difference = $end_timestamp - $start_timestamp;
                        $hours = floor($time_difference / 3600);
                        $minutes = floor(($time_difference % 3600) / 60);
                        $seconds = $time_difference % 60;
                        if($jk==8){
                          $jk = 1;
                        }
                        if($i ==2){
                          break;
                        }


                        $cash_expense_if_allready_exists = $this->Menu_model->get_cash_expense_by_meetid11($data->meetid);
                        // echo $this->db->last_query();
                        // echo  sizeof($cash_expense_if_allready_exists);

                    

                        ?>
                      <div class="timeline">
                        <div class="timeline-content cat<?=$jk;?>">
                          <div class="timeline-year"><?= $i; ?></div>
                          <h3 class="title"><?= $data->company_name; ?></h3>
                          <?php 
                           if(sizeof($cash_expense_if_allready_exists) > 0){
                            ?>
                            <div class="description">
                              <strong style="color:#417602;">Expense Ammount:</strong>  ₹ <?= $cash_expense_if_allready_exists[0]->expense ?><br/>
                              <strong style="color:#417602;">Expense Type:</strong>  <?= $cash_expense_if_allready_exists[0]->expense_type; ?><br/>
                              <strong style="color:#417602;">Expense Date:</strong>  <?= $cash_expense_if_allready_exists[0]->created_at; ?><br/>
                              <strong style="color:#417602;">Expense Remarks:</strong>  <?= $cash_expense_if_allready_exists[0]->expense_remarks; ?><br/>
                              <strong style="color:#417602;">LM Expense Rejected Remarks:</strong>  <?= $cash_expense_if_allready_exists[0]->verify_remarks; ?><br/>
                              <strong style="color:#417602;">Admin Expense Rejected Remarks:</strong>  <?= $cash_expense_if_allready_exists[0]->admin_msg; ?><br/>
                              </div>
                            <?php
                           }
                          ?>
                          <div class="description cat<?=$jk;?>">
                            <strong style="color:#417602;">Start Meeting:</strong>  <?= $data->startm; ?><br/>
                            <strong style="color:#417602;">Close Meeting:</strong>  <?= $data->closem; ?><br/>
                            <strong style="color:#417602;">Close Remarks:</strong>  <?= $data->remarks; ?><br/>
                            <strong style="color:#417602;">Time Spent Meeting:</strong>  <?= " {$hours} hours, {$minutes} minutes, {$seconds} seconds"; ?><br/><br/>
                            <input type="hidden" name="meetingid[]" value="<?= $data->meetid; ?>" required>

                            <div>
                              <label for="exampleFormControlTextarea1" class="form-label" style="color: crimson;">Enter Expense Ammount: </label>
                              <input type="number" name="expensecash[]" placeholder="₹ Enter Expense Ammount" class="form-control" required><br/>
                            </div>

                            <?php $allexpenstypes =  $this->Menu_model->GetAllTravelExpenseType(); ?>
                            <div class="mb-1" style="justify-content: center; display: flex ; flex-direction: column;">
                             <label style="color: crimson;">Select Expense Type: </label>
                              <select id="multiple" class="js-states form-control" name="travel_expense_type[]" multiple>
                                <option value="">--Select--</option>
                                <?php foreach($allexpenstypes as $allexpenstype): ?>
                                <option><?= $allexpenstype->expense_name; ?></option>
                                <?php endforeach; ?>
                              </select>
                             
                            </div>
                            <div class="mb-1">
                              <label for="exampleFormControlTextarea1" class="form-label" style="color: crimson;">Expense Remarks: </label>
                              <textarea class="form-control" id="exampleFormControlTextarea1" name="expense_remarks[]" placeholder=" Expense Remarks" rows="3" required ></textarea>
                            </div>


                            <hr>
                            <div class="form-group text-left">
                              <lable style="color: crimson;">Upload bills:</lable>
                              <input type="file" name="images<?=$i;?>[]" class="form-control file-input" multiple="multiple" accept="image/*,application/pdf" required />
                              <div id="preview-images<?=$i;?>" class="preview"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php $i++;$jk++; endforeach; ?> 
                    </div>
                    <hr>
                    <div class="form-group text-center">
                      <button class="btn btn-primary m-3" type="submit" id="autoplan_submit">Submit</button>
                    </div>
                  </div>
                </form>
                <?php }else{ ?>
                <div class="norecord cat6 p-5 text-center">
                  <h3>No Meetings Found</h3>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <style>
            .form-control{
            box-shadow: 7px 7px 10px #cbced1, -7px -7px 10px #fff;
            }
          </style>

<div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header bg-success">
                    <h5 class="text-center">Meetings Details</h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="table-responsive text-nowrap">
              <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead class="thead-dark">
                  <tr>
                    <th>#️⃣</th>
                    <th>🏢 Company Name</th>
                    <th>📅 Plan Date</th>
                    <th>🟢 Start</th>
                    <th>🔴 Close</th>
                    <th>📌 Company As</th>
                    <th>📖 Company Descriptions</th>
                    <th>📝 Remarks</th>
                  </tr>

                </thead>
                <tbody>
                  <?php $i = 1; foreach($meetData as $meetval){?>
                  <tr>
                    <td><?=$i?></td>
                    <td><?= $meetval->company_name; ?></td>
                    <td><?= $meetval->storedt; ?></td>
                    <td><?= $meetval->startm; ?></td>
                    <td><?= $meetval->closem; ?></td>
                    <td><?= $meetval->company_as; ?></td>
                    <td><?= $meetval->company_descri; ?></td>
                    <td><?= $meetval->remarks; ?></td>
                  </tr>
                  <?php $i++;} ?>
                </tbody>
              </table>
            </div>
          </div>


      </div>
    </div>
    </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
      $("#multiple").select2({
          placeholder: "Select Expense Type",
          allowClear: true
      });
    </script>

    <!-- ./wrapper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
    <script src="https://cdn.tiny.cloud/1/qagffr3pkuv17a8on1afax661irst1hbr4e6tbv888sz91jc/tinymce/6/tinymce.min.js"></script>
    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $(document).ready(function () {
      $('.file-input').on('change', function (e) {
          const files = e.target.files;
          const previewContainer = $(this).next('.preview');
          previewContainer.empty(); 
          Array.from(files).forEach(file => {
              const fileType = file.type;
              const fileURL = URL.createObjectURL(file); 
              if (fileType.startsWith('image/')) {
                  const img = $('<img class="billesimage">').attr('src', fileURL).css({
                      width: '100px',
                      height: '100px',
                      margin: '5px'
                  });
                  previewContainer.append(img);
              }else if (fileType === 'application/pdf') {
                  const embed = $('<embed>').attr({
                      src: fileURL,
                      width: '100px',
                      height: '100px'
                  }).css('margin', '5px');
                  previewContainer.append(embed);
              }
          });
      });
      });
    </script>
  </body>
</html>