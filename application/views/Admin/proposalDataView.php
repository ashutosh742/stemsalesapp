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
.content {
      margin: 20px;
      padding: 20px;
      border: 1px solid #ccc;
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
            <h1 class="m-0">Proposal Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4><?php $uid=$user['user_id']; ?></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
<?php 
$bd = $this->Menu_model->get_userbyaid($uid);
?>
<form class="p-3" method="POST" action="<?=base_url(); ?>/Menu/proposalDataReport">
    <input type="date" name="start_date" class="mr-2" value="<?=$sd ?>">
    <input type="date" name="end_date" class="mr-2" value="<?=$ed ?>">
    <button type="submit" class="bg-primary text-light">Search</button>
</form>

    <!-- Main content -->
    <section class="content" id="pdf-content">
    <div class="text-right mt-2"><button class="btn btn-info" id="printPage">Print Page</button> <br><br>

      <div class="container-fluid">

      <div class="row">
                <div class="col-md-12 text-center p-2">
                <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                    <h4>Monthly Proposal Reports</h4>
                  </div>
                </div>
                <!-- <div class="col-md-1 p-2" style="align-items: center; justify-content: center; display: flex ;">
                    <div class="text-center">
                       <?= Date("Y-m-d H:i:s"); ?>
                    </div>
                </div> -->
                <!-- <div class="col-md-1 p-2" style="align-items: center; justify-content: center; display: flex ;">
                    <div class="text-center">
                        <button class="btn btn-primary" style="padding: 4px;" id="generate-pdf">Generate PDF</button>
                    </div>
                </div> -->
            </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                  <div class="body-content">
        <?php 
          foreach($proposalDataReport as $userid_key => $val){
            if(count($val) > 0){    
          ?>
        <div class="summary-highlight">
          <div class="row">
            <div class="col-md-6">
              <div class="card p-2" style="text-align: left;">
                <h5>Proposal Report for <?= getUserNameById($userid_key); ?></h5>
                <ul>
                  <?php   
                    echo "<li>Total Proposals: ".count($val)."</li>" ;
                    echo "<li>Approved :".$meetingcount['approvedCount'][$userid_key]."</li>";
                    echo "<li>Rejected :".$meetingcount['rejectedCount'][$userid_key]."</li>";
                    echo "<li>Pending  :".$meetingcount['pendingCount'][$userid_key]."</li>";
                    ?>
                </ul>
              </div>
            </div>
            <div class="col-md-6"></div>
          </div>
        </div>
        <div class="pdf-viwer mt-4">
          <?php 
            if(isset($val)){
            $i = 1;
            
             ?>
          <table id="example_<?php echo $userid_key;?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>Sr.No.</th>
                <th>CIN</th>
                <th>Company Name</th>
                <th>UserName</th>
                <th>Proposal</th>
                <th>Partner Type</th>
                <th>No Of School</th>
                <th>Top Spender</th>
                <th>Business Potential</th>
                <th>Created Date</th>
                <th>Budget</th>
                <th>Approved Status</th>
                <th>Approved Date</th>
                <th>Logs</th>
                <th>Remark</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $logscount = 0;
                foreach($val as $key=> $value){
                 //   dd($value);
                   $logscount     = $this->Menu_model->getProposalLogs($userid_key,$value['id']);
                   $value['logs'] = $logscount->logs;
                //dd($logscount);
                    if($value['apr'] == '1'){
                        $apr= 'Yes';
                    }
                    else{
                        $apr = 'No';
                    }
                ?>
              <tr>
                <td><?= $i; ?></td>
                <td><?php echo $value['cmpid_id'];?> </td>
                <td><a href="<?=base_url();?>/Menu/CompanyDetails/<?=$value['cmpid_id']?>"><?php echo getCompanyNameByCmpid($value['cmpid_id']);?></a></td>
                <td><?= getUserNameById($value['user_id']); ?></td>
                <td><a href="<?php echo base_url();?><?php echo $value['proattach'];?>" target='_blank'>View Proposal</a></td>
                <td><?= $value['partner']; ?></td>
                <td><?= $value['noofsc']; ?></td>
                <td><?php if($value['comp_top_spender']!='' && $value['comp_top_spender']!=0){ echo "Yes";}else { echo "No";} ?></td>
                <td><?= $value['comp_business_potential']; ?></td>
                <td><?= $value['sdatet']; ?></td>
                <td><?= $value['pbudgetme']; ?></td>
                <td><?= $apr; ?></td>
                <td><?= $value['aprdatet']; ?></td>
                <td><?= $value['logs'];?></td>
                <td><?= $value['remark']; ?></td>
                <?php $i++; ?>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          <?php }
            }
            }?>
        </div>
       
                     </div>


</div></div></div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
     /*
          $.ajax({
          url: '<?= base_url(); ?>Menu/CheckPlannerlogReportExistsOrNot',
          type: "POST",
          data: {
            filetypesname: 'CRMProposalReportData',
          },
          cache: false,
          success: function(result) {
              if (result !== 'exists') {
                  let clickTriggered = false;
                  setTimeout(function() {
                      if (!clickTriggered) {
                          $("#generate-pdf").click();
                          clickTriggered = true;
                      }
                  }, 2000);
              }else{
                  console.log("CRM Proposal Report Data Allready Store");
              }
          }
      });
      
      
         
      
      $("#generate-pdf").click();
      
      $('#generate-pdf').on('click', function () {
    const { jsPDF } = window.jspdf;
    $(this).css("background-color", "green");

    html2canvas(document.querySelector('#pdf-content'), { scale: 1 }).then(function (canvas) {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');

        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();

        const canvasWidth = canvas.width;
        const canvasHeight = canvas.height;

        const ratio = pdfWidth / canvasWidth;
        const newWidth = pdfWidth;
        const newHeight = canvasHeight * ratio;

        let yOffset = 0;

        while (yOffset < newHeight) {
            if (yOffset > 0) {
                pdf.addPage();
            }
            pdf.addImage(imgData, 'PNG', 0, -yOffset, newWidth, newHeight);
            yOffset += pdfHeight;
        }

        const pdfBase64 = pdf.output('datauristring');

        $.ajax({
            url: '<?=base_url();?>Menu/UploadCRMReports',
            type: 'POST',
            data: {
                pdf: pdfBase64.split(',')[1],
                filename: 'CRMProposalReportData.pdf',
                filetypesname: 'CRMProposalReportData',
            },
            success: function (response) {
                console.log('PDF uploaded successfully! Server Response: ' + response);
            },
            error: function (xhr, status, error) {
                console.error('Upload error: ' + error);
            },
        });
    }).catch(function (error) {
        console.error('Error generating PDF: ', error);
    });
});

      */
      
      
      });
    </script>
</body>
</html>