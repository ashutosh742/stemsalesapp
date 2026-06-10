
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
<title>STEM APP | WebAPP</title>
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/icheck-bootstrap.min.css">
<!-- JQVMap -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/jqvmap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/adminlte.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/OverlayScrollbars.min.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/daterangepicker.css">
<!-- summernote -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/summernote-bs4.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/buttons.bootstrap4.min.css">
<style>
    .summary-highlight {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-size: 1.1rem;
    font-weight: bold;
    color: #333;
}
.summary-highlight ul {
    padding-left: 20px;
    list-style-type: disc;
}
.summary-highlight h3 {
    margin-bottom: 10px;
    color: #007bff;
}

.scrollme {
overflow-x: auto;
}

.table-striped tbody tr {
    background-color: transparent !important;
}
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<!-- Preloader -->

<!-- Navbar -->
<?php require ('nav.php'); ?>
<!-- /.navbar -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
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

  
<form class="p-3" method="POST" action="<?=base_url(); ?>/Menu/meetingReportData">
    <input type="date" name="start_date" class="mr-2" value="<?=$sd ?>">
    <input type="date" name="end_date" class="mr-2" value="<?=$ed ?>">
    <button type="submit" class="bg-primary text-light">Search</button>
</form>
<div class="container">
<div class="row">
<section class="content" id="pdf-content">
<div class="mt-2"><button class="btn btn-info" id="printPage">Print Page</button> <br><br>

<div class="col-md-12 text-center p-2">
                <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                    <h4>Meetings Reports</h4>
                  </div>
                </div>

<div class="col-md-12 text-center p-2"><h5>Summary</h5>
<table id="summary" class="table table-striped table-bordered" cellspacing="0" style="width:100% !important">
    <thead>
        <tr>
            <th style="background-color: #ffcccc;">Planned Meetings:</th>
            <th style="background-color: #ccffcc;">Bargin Meetings:</th>
            <th style="background-color: #ccccff;">Schedule Meetings:</th>
            <th style="background-color: #ffffcc;">RP :</th>
            <th style="background-color: #ffccff;">No RP :</th>
            <th style="background-color: #ccffff;">Only Got Details : </th>
            <th style="background-color: #e6e6fa;">Total Initiated :</th>
            <th style="background-color: #f0e68c;">Total Started : </th>
            <th style="background-color: #98fb98;">Total Closed :</th>
        </tr>
        <tr>
            <td><?php echo $totalmeetings; ?></td>
            <td><?php echo $barginmeetings; ?></td>
            <td><?php echo $schedulemeetings; ?></td>
            <td><?php echo $rpmeetings; ?></td>
            <td><?php echo $norpmeetings; ?></td>
            <td><?php echo $onlygotdetails; ?></td>
            <td><?php echo $initiated; ?></td>
            <td><?php echo $started; ?></td>
            <td><?php echo $completed; ?></td>
        </tr>
    </thead>
</table>
</div>

   
            </div>
</div>

   <?php 
  
      foreach($meetingSummaryData as $userid_key => $val){
        if(count($val) > 0){
    ?>
    <div class="summary-highlight">
        <h3>Meeting Report for <?= getUserNameById($userid_key); ?></h3>
        <p><strong> Meeting Summary </strong></p>
        <table id="summary" class="table table-striped table-bordered" cellspacing="0" style="width:100% !important">
    <thead>
        <tr>
        <th style="background-color: #ffcccc;">Plannned Meetings:</th>
        <th style="background-color: #ccffcc;">Bargin Meetings:</th>
        <th style="background-color: #ccccff;">Schedule Meetings:</th>
            <th style="background-color: #ffffcc;">RP :</th>
            <th style="background-color: #ffccff;">No RP :</th>
            <th style="background-color: #ccffff;">Only Got Details : </th>
            <th style="background-color: #e6e6fa;">Total Initiated :</th>
            <th style="background-color: #f0e68c;">Total Started : </th>
            <th style="background-color: #98fb98;">Total Closed :</th>
        </tr>
        <tr>
            <td><?php echo $val['TotalMeetingData']; ?></td>
            <td><?php echo $val['BarginMeetingData']; ?></td>
            <td><?php echo $val['ScheduleMeetingData']; ?></td>
            <td><?php echo $val['RPMeetingData']; ?></td>
            <td><?php echo $val['NoRPMeetingData']; ?></td>
            <td><?php echo $val['OnlyGotDetailsMeetingData']; ?></td>
            <td><?php echo $val['Initiated']; ?></td>
            <td><?php echo $val['Started']; ?></td>
            <td><?php echo $val['Completed']; ?></td>
        </tr>
    </thead>
</table>

        <ul>
        <?php /*  
                    echo "<li>Total Planned Meetings      : ".$val['TotalMeetingData'] ;
  		    echo "<li>Total Initiated Meeting : ".$val['Initiated'] ;
                    echo "<li>Bargin Meetings     : ".$val['BarginMeetingData'] ;
                    echo "<li>Schedule Meetings   : ".$val['ScheduleMeetingData'] ;
                    echo "<li>RP Meetings         : " .$val['RPMeetingData'] ;
                    echo "<li>No RP Meetings      : " .$val['NoRPMeetingData'] ;
                    echo "<li>Only Got Details Meetings : ".$val['OnlyGotDetailsMeetingData'] ;
                    echo "<li>Total Started Meeting : ".$val['Started'] ;
                    echo "<li>Total Closed Meeting : ".$val['Completed'] ;
                    exit;*/

        ?>
        </ul>
    </div>
<div class="pdf-viwer mt-4">
    <?php 
   if(isset($val['MeetingData'])){
   $i = 1;
    ?>
    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th rowspan="2">Sr.No.</th>
                <th rowspan="2">CIN </th>
                <th rowspan="2">Company Name</th>
                <th>Company Status</th>
                <th>Initiated Time & Location</th>
		        <th>Initiate Photo</th>
                <th>Started Time & Location</th>
                <th>Start Photo</th>
                <th>Closed Time & Location</th>
                <th>Total Time Taken</th>
            </tr>
            <tr>
                <th>RP Yes/No</th>
                <th>Potential Yes/No</th>
                <th>MOM Yes/No</th>
                <th>Thanks Mail Yes/No</th>
                <th>PST Assign Yes/No</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        foreach($val['MeetingData'] as $key=> $value){
            $bgcolor = "";
            // if($value['mtype'] == 'RP'){
            //     $bgcolor = "#fcc200";
            // }
            // else if($value['mtype'] == 'NO RP'){
            //     $bgcolor = "#f0e68c";
            // }
            // else{
            //     $bgcolor = "#f5f5dc";
            // }
        ?>
                <tr style="border: 1px solid black; !important">
                    <td rowspan="2"><?= $i; ?></td>
                    <td rowspan="2"><?= $value['CIN']; ?></td>
                    <td rowspan="2"><a href="<?=base_url();?>/Menu/CompanyDetails/<?=$value['CIN']?>"><?= $value['Company_Name']; ?></a></td>
                    <td><b>Company Status :</b><br/><?=getCStatusBystatusId($value['status_id']);?></td>
                    <td><b>Initiated Time :</b><br/><?= $value['initiateTime']; ?><br/>
                        <?php 
                        if(isset($value['initiateLat'])){

                            echo getLocationsfromURL($value['initiateLat'],$value['initiateLongi']);
                         } ?>
                    </td>
                    <td><b>Initiated Photo</b><br/><img class="uimage" height="80px" alt="" src="<?=base_url().'/'.$value['initphoto'];?>"></td>
                    <td><b>Started Time</b><br/><?= $value['startm']; ?> <br/>
                        <?php if(isset($value['slatitude']) && isset($value['slatitude'])) {
                          //  echo $value['slatitude']."-".$value['slongitude'];
                            echo getLocationsfromURL($value['slatitude'],$value['slongitude']);
                        }
                    ?>
                    </td>
                    <td><b>Start Photo</b><br/><img class="uimage" height="80px" alt="" src="<?=base_url().'/'.$value['cphoto'];?>"></td>               
                    <td><b>Close Time</b><br/><?= $value['closem']; ?> 
                           </td>
                    <td><b>Intiated-Closed Time Diff</b><br/><?php echo getDateTimeDiff($value['initiateTime'],$value['closem']);?>Hrs</td>
                </tr>
                <tr>
                    <td><b>RP ?</b><br/><?= $value['mtype']; ?></td>
                    <td><b>Business Potential ?</b><br/><?= $value['comp_business_potential']; ?></td>
                    <td><b>MOM ?</b><br/><?= $momc ? 'yes' : 'no'; ?></td>
                    <td><b>Thanks Mail ?</b><br/><?= $emailc ? 'yes' : 'no'; ?></td>
                    <td><b>PST ?</b><br/><?= $psta ? 'yes' : 'no'; ?></td>
                    <td><b>Review Remark :</b></br/><?= $value['queans']; ?><hr><?= $value['mcomment']; ?></td>
                    <?php $i++; $j++; ?>
                </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php }
    }
}?>
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


<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
<!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="<?=base_url(); ?>assets/js/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=base_url(); ?>assets/js/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?=base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?=base_url(); ?>assets/js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?=base_url(); ?>assets/js/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?=base_url(); ?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url(); ?>assets/js/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?=base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?=base_url(); ?>assets/js/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?=base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?=base_url(); ?>assets/js/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=base_url(); ?>assets/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="<?=base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url(); ?>assets/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/jszip.min.js"></script>
<script src="<?=base_url(); ?>assets/js/pdfmake.min.js"></script>
<script src="<?=base_url(); ?>assets/js/vfs_fonts.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.html5.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.print.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url(); ?>assets/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url(); ?>assets/js/dashboard.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <script>
    $(document).ready(function () {
    /*
        $.ajax({
        url: '<?= base_url(); ?>Menu/CheckPlannerlogReportExistsOrNot',
        type: "POST",
        data: {
          filetypesname: 'CRMMeetingReportData',
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
                console.log("Planner Log Report Allready Store");
            }
        }
    });


       

    $("#generate-pdf").click();

    $('#generate-pdf').on('click', function () {
    const { jsPDF } = window.jspdf;
    $(this).css("background-color", "green");

    // Use html2canvas to capture the content as an image
    html2canvas(document.querySelector('#pdf-content'), { scale: 1 }).then(function (canvas) {4

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
                pdf: pdfBase64.split(',')[1], // Only send the Base64 content
                filename: 'CRMMeetingReportData.pdf',
                filetypesname: 'CRMMeetingReportData',
            },
            success: function (response) {
                console.log('PDF uploaded successfully! Server Response: ' + response);
            },
            error: function (xhr, status, error) {
                console.error('Upload error: ' + error);
            },
        });
    });
});

*/


    });
  </script>
  <script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
        })
  </script>
</body>
</html>
