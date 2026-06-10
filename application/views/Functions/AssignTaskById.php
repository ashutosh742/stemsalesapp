<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM Oppration | WebAPP</title>

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>

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
   <?php $this->load->view($dep_name.'/nav.php');?>

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
              
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
         <?php if ($this->session->flashdata('pending_message')): ?>
            <div class="alert alert-warning alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?= $this->session->flashdata('pending_message'); ?>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?= $this->session->flashdata('success_message'); ?>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <?php endif; ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body11" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">


                        <div class="text-center" style="background: linear-gradient(to right, #f5f6e7ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h5>Assign Task</h5>
               
                  </div>
                  <hr>

                     <form action="<?=base_url();?>Menu/dailyTaskAssignById" method="post" style="padding:20px; background:#f9f9f9; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
    
    <div class="row" style="margin-bottom:15px;">
        <div class="col-4">
            <label style="font-weight:bold;">👤 User</label>
            <input type="text" class="form-control" id="user" name="user" value="<?= $userdtl[0]->name ?>" readonly style="border-radius:8px;">
            <input type="hidden" id="taskDetails" name="taskDetails" value='<?= json_encode($taskDetails) ?>'>
            <input type="hidden" id="taskDetails_id" name="taskDetails_id" value='<?= $taskDetails[0]->id ?>'>
        </div>

        <div class="col-4">
            <label style="font-weight:bold;">📅 Date</label>
            <input type="text" class="form-control" id="date_display" name="date_display" value="<?= date('Y-m-d', strtotime($taskDetails[0]->appointmentdatetime)) ?>" readonly style="border-radius:8px;">
        </div>

        <div class="col-4">
            <label style="font-weight:bold;">⏰ Time</label>
            <input type="text" class="form-control" id="time_display" name="time_display" value="<?= date('H:i:s', strtotime($taskDetails[0]->appointmentdatetime)) ?>" readonly style="border-radius:8px;">
        </div>
    </div>

  

    <div class="row" style="margin-bottom:15px;">
          <div class="col-3">
            <label style="font-weight:bold;">📝 Current Status</label>
            <select id="current_status" class="form-control" name="current_status" required style="border-radius:8px;">
                <option value="" disabled selected>Select Task</option>
                <?php $mstatus = $this->Menu_model->get_status(); 
                foreach($mstatus as $status) { ?>
                    <option value="<?=$status->id?>"><?=$status->name?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-3">
            <label style="font-weight:bold;">🏢 Company <span class="ccount" style="font-size:12px; padding-left:5px;"></span></label>
            <select id="company" class="form-control select2" name="company" style="border-radius:8px;"></select>
        </div>

        <div class="col-3">
            <?php  
                $user_day = $this->Menu_model->getUserDayStartDetails($userdtl[0]->user_id,date("Y-m-d"));
                $user_daycnt =  sizeof($user_day);
                if($user_daycnt > 0){
                    $user_daywfo = $user_day[0]->wffo;
                    $disabled = ($user_daywfo == 2) ? 'disabled' : '';
                }
            ?>
            <label style="font-weight:bold;">⚡ Task/Action</label>
            <select id="task_action" class="form-control" name="task_action" style="border-radius:8px;">
                <option value="">Select Task</option>
                <?php 
                $action = $this->Menu_model->get_action(); 
                foreach ($action as $a) {
                    if(in_array($a->id,[6,7,8,9,10,11,12,13,14,15,17,18,19,20,21])){continue;}
                    echo '<option value="' . $a->id . '">' . $a->name . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="col-3">
            <label style="font-weight:bold;">🎯 Target Purpose</label>
            <select id="ntppose" class="form-control" name="ntppose" style="border-radius:8px;"></select>
        </div>
    </div>
<hr>
    <div class="row">
        <div class="col-12" style="text-align:center; margin-top:20px;">
            <button type="submit" class="btn btn-success" style="padding:10px 25px; font-size:16px; border-radius:8px;">✅ Submit</button>
        </div>
    </div>

</form>

                        </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
   
<script type="text/javascript">
    $(document).ready(function() {
        // Trigger AJAX call when the status is changed
        $('#current_status').change(function() {
            var uid = <?= $userdtl[0]->user_id ?>;
            var statusId = $(this).val();
            $.ajax({
                url: '<?=base_url();?>Menu/getcmpbyStatus',
                type: 'POST',
                data: {
                    uid: uid,
                    status: statusId
                },
                dataType: 'json',
                success: function(response) {
                    $('#company').empty(); // Clear the current options
                    if(response.length > 0){
                        $.each(response, function(key, company) {
                            $('#company').append(new Option(company.compname, company.inid));
                        });
                    } else {
                        $('#company').append(new Option('No companies found', ''));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        });

        $('#task_action').change(function() {

            var initId = $('#company').val();
            var aId = $(this).val();

            $.ajax({
                url: '<?=base_url();?>Menu/getPurposeByAction',
                type: 'POST',
                data: {
                    initId: initId,
                    aId: aId
                },
                dataType: 'json',
                success: function(response) {
                    $('#ntppose').empty(); // Clear the current options
                    if(response.length > 0){
                        $.each(response, function(key, purpose) {
                            $('#ntppose').append(new Option(purpose.name, purpose.id));
                        });
                    } else {
                        $('#ntppose').append(new Option('No purpose found', ''));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        });

        $('#company').change(function() {
          $('#ntppose').val('');
          $('#task_action').val('');
      });

        // $('form').submit(function() {
        //     $('#user_hidden').val($('#user').val());
        //     $('#date_hidden').val($('#date_display').val());
        //     $('#time_hidden').val($('#time_display').val());
        //     $('#current_status_hidden').val($('#current_status').val());
        //     $('#company_hidden').val($('#company').val());
        //     $('#task_action_hidden').val($('#task_action').val());
        //     $('#ntppose_hidden').val($('#ntppose').val());
        // });
    
    });
</script>



