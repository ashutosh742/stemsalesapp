<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily to-do list share | STEM APP | WebAPP</title>
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
            <?php
              if ($this->session->flashdata('success_message_plan')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message_plan'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php
              if ($this->session->flashdata('error_message_plan')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message_plan'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <div class="row mb-2">
              <div class="col-sm-12 bg-primary text-center">
                <h2 class="mb-4">Daily to-do list share</h2>
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
          <div class="row">
            <div class="col-md-4">
              <div class="card p-2">
                <div class="card text-center">
                  <h5> Team </h5>
                </div>
                <ul class="list-group">
                  <?php $i=1; foreach($usersData as $list){
                    $get_user_types = $this->Menu_model->get_user_types($list->type_id);
                    $get_user_typesname = $get_user_types[0]->name;
                    ?>
                  <li class="list-group-item card sccardlist" onclick="getDataValue(this)" data="<?=$list->user_id;?>" >(<?= $i; ?>) <?= $list->name; ?> - (<?= $get_user_typesname;?>)</li>
                  <?php $i++; } ?>
                </ul>
                <style>
                  .scataglist{color: #ff00ba;font-size: 14px;}.sccardlist{padding: 5px; margin-bottom: 7px !important;background: chartreuse;text-transform: capitalize;}
                  .list-group-item:first-child {background: chartreuse;}
                  .sccardlist:hover{cursor: pointer;background: yellow;}
                  .flexitamsdata{align-items: center;justify-content: center;display: flex;}
                  form#AddDailyToDoList {padding: 30px;}
                </style>
              </div>
            </div>
            <div class="col-md-8">
              <div class="card text-center">
                <h5 id="slsctusername"> </h5>
              </div>
              <div class="card" id="tasklistcard">
                <form action="" id="AddDailyToDoList" method="post">
                  <input type="hidden" class="form-control" name="slct_daily_user_id" id="slct_daily_user_id" value="">
                  <?php 
                    $getDailyActiveToDoList = $this->Menu_model->getDailyActiveToDoList();
                    foreach($getDailyActiveToDoList as $gdatlist):
                    ?>
                  <div class="form-group">
                    <label><?= $gdatlist->list; ?></label>
                    <input type="hidden" class="form-control" value="<?=$gdatlist->id;?>" name="slct_daily_list_id[]">
                    <?php if($gdatlist->types == 'textarea'){ ?>
                    <textarea class="form-control" placeholder="<?=$gdatlist->list;?>" name="slct_daily_list_answer[]" required></textarea>
                    <?php }else{ ?>
                    <input type="<?=$gdatlist->types;?>" class="form-control"  placeholder="<?=$gdatlist->list;?>" name="slct_daily_list_answer[]" required>
                    <?php } ?>
                  </div>
                  <?php endforeach; ?>
                  <!-- <div class="form-group">
                    <label for="exampleFormControlInput1">How many calls will you make today?</label>
                    <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="How many calls will you make today?" required>
                    </div>
                    <div class="form-group">
                    <label for="exampleFormControlInput2">How many Meetings will you make today?</label>
                    <input type="number" class="form-control" id="exampleFormControlInput2" placeholder="How many Meetings will you make today?" required>
                    </div>
                    <div class="form-group">
                    <label for="exampleFormControlInput2">How many Email will you make today?</label>
                    <input type="number" class="form-control" id="exampleFormControlInput2" placeholder="How many Email will you make today?" required>
                    </div> -->
                  <center><button type="submit" class="btn btn-primary">Add Daily to-do list</button></center>
                </form>
              </div>
              <div class="card p-2" id="taskcompleteform">
                <center>
                  <form action="<?=base_url().'Menu/UpdateScTaskWithIdWithRemarks'?>" method="POST" enctype="multipart/form-data">
                    <?php  
                    $getSCTaskByTaskID = $this->Menu_model->GetSCTaskByTaskID($ctaskid);
                 
                    $file_required = $getSCTaskByTaskID[0]->file_required;
                  
                    ?>
                    <input type="hidden" name="taskid" value="<?= $ctaskid ?>">

                    <?php if($file_required == 'yes'){ ?> 
                      <div class="form-group">
                        <label for="exampleFormControlTextarea1">Upload Report</label>
                         <input type="file" name="filname[]" id="filname" class="form-control" multiple required>
                      </div>
                      <?php } ?>

                    <div class="form-group">
                      <label for="exampleFormControlTextarea1">Add Final Remarks</label>
                      <textarea required class="form-control" name="remarks" placeholder="* Please Add Daily Team Planner Summary With Location Remarks"  id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                    <hr>
                    <div id="file-preview"></div>
                    <button type="submit" class="btn btn-primary"> Final Submit</button>
                  </form>
                </center>
              </div>
              <div class="card p-2 flexitamsdata" id="tasklistcardimg">
                <img class="img-fluide" width="400" src="https://i.ibb.co/r0NLBfP/pngtree-to-do-list-icon-png-image-9158735.png" alt="pngtree-to-do-list-icon-png-image-9158735" border="0">
              </div>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-10">
            </div>
            <div class="col-md-1"></div>
          </div>
        </section>
        <br>
        <br>
      </div>
    </div>
    </section>
    </div>
    </div> 
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
    $(document).ready(function() {
     $("#tasklistcard").hide();
     $("#tasklistcardimg").show();
     $("#taskcompleteform").hide();

     var alluserdone = true; // Assume all users are done initially
     var ajaxCount = 0; // Counter to track the number of completed AJAX requests
     var totalUsers = $(".list-group-item").length; // Total number of users

     $(".list-group-item").each(function() {
         const userId = $(this).attr("data");
         const listItem = $(this);

         $.ajax({
             url: "<?=base_url().'Menu/CheckDailyToDoListByUser'?>",
             type: "POST",
             data: {
                 user_id: userId
             },
             success: function(response) {
                 if (response === "exists") {
                     listItem.css({
                         "background-color": "white",
                         "pointer-events": "none",
                         "opacity": "0.6"
                     }).text(listItem.text() + " - 👍");
                     alluserdone = false; // At least one user is not done
                 }
             },
             error: function(xhr, status, error) {
                 console.error("Error checking user_id:", error);
             },
             complete: function() {
                 ajaxCount++; // Increment the counter when an AJAX request completes
                 // Once all AJAX requests are completed, check if all users are disabled
                 if (ajaxCount === totalUsers) {
                     var allDisabled = true;
                     $(".list-group-item").each(function() {
                         if ($(this).css("pointer-events") !== "none") {
                             allDisabled = false;
                             const userId = $(this).attr("data");
                             console.log("Check User ID: " + userId + ' = ' + allDisabled + ' = ' + $(this).css("pointer-events"));
                         }
                     });
                     if (allDisabled) {
                         console.log("All users are disabled.");
                         $("#taskcompleteform").show();
                         $("#tasklistcard").hide();
                         $("#tasklistcardimg").hide();
                     } else {
                         console.log("Not all users are disabled.");
                     }
                 }
             }
         });
     });
 });

 function getDataValue(element) {
     let dataValue = $(element).attr("data");
     let dataValuetext = $(element).text();
     $("#slsctusername").text(dataValuetext);
     $("#slsctusername").css('color', 'green');
     $("#slct_daily_user_id").val(dataValue);
     $("#tasklistcard").show();
     $("#tasklistcardimg").hide();
     const listItems = document.querySelectorAll('.sccardlist');
     listItems.forEach(item => {
         item.style.backgroundColor = '';
     });
     element.style.backgroundColor = 'yellow';
 }

 $(document).ready(function() {
     $("#AddDailyToDoList").on("submit", function(e) {
         e.preventDefault();
         var formData = $(this).serialize();
         $.ajax({
             url: "<?=base_url().'Menu/AddDailyToDoListByUser'?>",
             type: "POST",
             data: formData,
             success: function(response) {
                 alert("Daily to-do list added successfully!");
                 var userId = $("#slct_daily_user_id").val();
                 $.ajax({
                     url: "<?= base_url().'Menu/CheckDailyToDoListByUser' ?>",
                     type: "POST",
                     data: {
                         user_id: userId
                     },
                     success: function(response) {
                         if (response === "exists") {
                             $(".list-group-item[data='" + userId + "']")
                                 .css({
                                     "background-color": "white",
                                     "pointer-events": "none",
                                     "opacity": "0.6"
                                 })
                                 .text($(".list-group-item[data='" + userId + "']").text() + " - 👍");
                         }
                         var allDisabled = true;
                         $(".list-group-item").each(function() {
                             if ($(this).css("pointer-events") !== "none") {
                                 allDisabled = false;
                                 const userId = $(this).attr("data");
                                 console.log("Check User ID: " + userId + ' = ' + allDisabled + ' = ' + $(this).css("pointer-events"));
                             }
                         });
                         if (allDisabled) {
                             console.log("All users are disabled.");
                             $("#taskcompleteform").show();
                             $("#tasklistcard").hide();
                         } else {
                             console.log("Not all users are disabled.");
                         }
                     },
                     error: function(xhr, status, error) {
                         console.error("Error checking user_id:", error);
                     }
                 });
             },
             error: function(xhr, status, error) {
                 alert("An error occurred while adding the daily to-do list.");
                 console.log(error);
             }
         });
     });
 });



 $(document).ready(function () {
    $('#filname').on('change', function (event) {
        const files = event.target.files;
        const previewContainer = $('#file-preview');
        previewContainer.empty(); // Clear previous previews

        Array.from(files).forEach((file) => {
            const fileType = file.type;
            const fileReader = new FileReader();

            fileReader.onload = function (e) {
                if (fileType.startsWith('image/')) {
                    // Display image preview
                    previewContainer.append(
                        `<img src="${e.target.result}" alt="${file.name}" style="max-width: 200px; margin: 10px;">`
                    );
                } else if (fileType === 'application/pdf') {
                    // Display PDF preview
                    previewContainer.append(
                        `<div>
                            <embed src="${e.target.result}" type="application/pdf" width="300" height="200" />
                         </div>`
                    );
                } else {
                    previewContainer.append(
                        `<p>Unsupported file type: ${file.name}</p>`
                    );
                }
            };

            fileReader.readAsDataURL(file);
        });
    });
});

    </script>
  </body>
</html>