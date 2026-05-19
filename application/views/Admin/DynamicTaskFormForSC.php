<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dyanmic Create Task For SC | STEM APP | WebAPP</title>
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
                <h2 class="mb-4">Dynamic Task Form</h2>
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
            <div class="row">
              <div class="col-md-8"></div>
              <div class="col-md-4">
              </div>
            </div>
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-md-6">
              <div class="card p-2">
                <ul class="list-group">
                  <?php $i=1; foreach($sctasklist as $list){
                    $liststatus = $list->status;
                    if($liststatus == 0){
                      $csstext = "bg-warning";
                    }else if($liststatus == 1){
                      $csstext = "sccardlist1";
                    }
                    ?>
                  <li class="list-group-item card <?= $csstext; ?>" onclick="getDataValue(this)" data="<?=$list->id;?>" > <strong>(<?=$i;?>)</strong> <?= $list->unique_kpa; ?></li>
                  <?php $i++; } ?>
                  <li class="list-group-item card sccardlist" onclick="getDataValue(this)" data="AddNewTask" style="background: #e600ff!important;">Add New Task</li>
                </ul>
                <style>
              .scataglist{color:#ff00ba;font-size:14px}.sccardlist{padding:5px;margin-bottom:7px!important}.list-group-item:first-child,.sccardlist1{background:#7fff00}.list-group-item:hover{background:#dc143c!important;cursor:pointer!important;color:#fff!important;transition:.2s ease-in-out!important}#vectorimageIDGIF{height:100vh;align-items:center;justify-content:center;display:flex}.card, .card.card-outline-tabs, .small-box>.inner, table { padding: 7px 10px;margin-bottom: 4px; }.active-bg { background-color: #007bff !important;color: white; }
              .list-group-item:first-child, .sccardlist1 {
                    box-shadow: rgba(14, 30, 37, 0.12) 0px 2px 4px 0px, rgba(14, 30, 37, 0.32) 0px 2px 16px 0px;
                }
                </style>
                <hr>
                <div class="text-center">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                  Add New Type of Task
                  </button>
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal2">
                  Add New Daily to-do list share
                  </button>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card" id="tasklistcard">
                <div class="card p-2" id="vectorimageID">
                  <img src="https://static.vecteezy.com/system/resources/previews/002/531/100/non_2x/task-list-illustration-vector.jpg" class="img-fluide" alt="task png">
                </div>
                <div class="card p-2" id="vectorimageIDGIF">
                  <img src="https://c.tenor.com/zecVkmevzcIAAAAC/tenor.gif" width="350px" class="img-fluide" alt="task png">
                </div>
              </div>
            </div>
          </div>
        </section>
        <br>
        <br>
      </div>
    </div>
    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add New Type of Task</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <?php $daywisetasktype = $this->Menu_model->day_wise_task_type();
              foreach($daywisetasktype as $daytask){ ?>
            <span class="p-1 bg-success"><?= $daytask->task_type; ?></span>
            <?php  }?>
            <hr>
            <form method="POST" action="<?=base_url().'Menu/AddNewDayWiseTaskType'?>">
              <lable>Day wise task type : </lable>
              <input type="text" class="form-control" name="day_wise_task_type" id="day_wise_task_type" placeholder="Day wise task type"> 
              <hr/>
              <lable>Number of Days : </lable>
              <input type="number" class="form-control" name="number_of_days" id="number_of_days" placeholder="Number of Days"> <br/>
              <center>
                <button type="submit" class="btn btn-primary">Add </button>
              </center>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">Daily to-do list share</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="align-items: center; justify-content: center; display: grid ;">
            <?php 
              $getDailyActiveToDoList = $this->Menu_model->getDailyActiveToDoList();
              $getDailyInActiveToDoList = $this->Menu_model->getDailyInActiveToDoList();
               ?>
            <label for="">Active</label>
            <?php foreach($getDailyActiveToDoList as $gdatlist){ ?>
            <span class="p-1 bg-success m-1"><?= $gdatlist->list; ?></span>
            <?php  }?>
            <hr/>
            <label for="">In Active</label>
            <?php foreach($getDailyInActiveToDoList as $gdatnlist){ ?>
            <span class="p-1 bg-warning m-1"><?= $gdatnlist->list; ?></span>
            <?php  }?>
            <hr/>
            <form method="POST" action="<?=base_url().'Menu/AddNewDailytodolistQuestions'?>">
              <lable>Add New Daily to-do list Questions : </lable>
              <input type="text" class="form-control" name="to_do_list_question" id="to_do_list_question" placeholder="Add New Daily to-do list Questions"> 
              <hr/>
              <lable> Define Types </lable>
              <select name="define_types" class="form-control">
                <option value="text">text</option>
                <option value="number">number</option>
                <option value="textarea">textarea</option>
              </select>
              <br>
              <center>
                <button type="submit" class="btn btn-primary">Add New Daily to-do list </button>
              </center>
            </form>
          </div>
        </div>
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
    $("#vectorimageIDGIF").hide();
    $(".list-group-item").click(function () {
        $(".list-group-item").removeClass("active-bg"); // Remove the background from all
        $(this).addClass("active-bg"); // Add background to the clicked one
    });
  });

      function getDataValue(element) {
          let dataValue = $(element).attr("data");
         
          $("#vectorimageID").hide();
          $("#vectorimageIDGIF").show();
          $.ajax({
            url:'<?=base_url();?>Menu/GetScTaskDetailsById',
            type: "POST",
            data: {
              dataValue: dataValue,
            },
            cache: false,
            success: function a(result){
              $("#vectorimageIDGIF").fadeOut(200);
              $("#tasklistcard").html("");
              $("#tasklistcard").html(result);
            }
            });
      }
      
    </script>
  </body>
</html>