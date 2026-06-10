<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM APP | WebAPP</title>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 
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
  <?php include 'addpop.php';?>
  <?php include 'popupbox.php';?>
  <!-- /.navbar -->


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Planned Task</h1>
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
    

                <section class="content p-3">
                    
                    <button id="grid-view-btn" class="btn btn-primary">Grid View</button>
                            <button id="list-view-btn" class="btn btn-primary">Xls View</button>
                    <div class="container-fluid card p-5" id="data-container">
                        <div class="row text-center" id="grid-view">
                            <?php $gdata1 = $this->Menu_model->get_daymanagment($uid,'2023-08-22'); ?>
                                <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                                        Name<br><b><?=$gdata1[0]->bdname?></b><hr>
                                        Day Started <br><b><?=$this->Menu_model->get_dformat($gdata1[0]->ustart)?></b><hr>
                                        <img src="https://stemapp.in/<?=$gdata1[0]->usimg?>" class="img-thumbnail"><hr>
                                          <input type="text" id="latitude" name="latitude" value="<?=$gdata1[0]->slatitude?>" hidden>
                                          <input type="text" id="longitude" name="longitude" value="<?=$gdata1[0]->slongitude?>" hidden>
                                          <div id="result"></div>
                                              <script>
                                                function getAddressFromLatLng(latitude, longitude) {
                                                  const apiUrl = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`;
                                                  fetch(apiUrl)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                    if (data.display_name) {
                                                      const address = data.display_name;
                                                      document.getElementById('result').textContent = address;
                                                    } else {
                                                      document.getElementById('result').textContent = 'No results found';
                                                    }
                                                  })
                                                    .catch(error => {
                                                    document.getElementById('result').textContent = 'Error: ' + error;
                                                  });
                                                }
                                                const latitude = parseFloat(document.getElementById('latitude').value);
                                                const longitude = parseFloat(document.getElementById('longitude').value);
                                                if (!isNaN(latitude) && !isNaN(longitude)) {
                                                  getAddressFromLatLng(latitude, longitude);
                                                } else {
                                                  document.getElementById('result').textContent = 'Invalid coordinates';
                                                }
                                              </script>
                                        
                                        
                                        <div class="rounded-circle bg-danger" style="position: absolute;
                                            bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;
                                            bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                                        Name<br><b><?=$gdata1[0]->bdname?></b><hr>
                                        Day Started <br><b><?=$this->Menu_model->get_dformat($gdata1[0]->uclose)?></b><hr>
                                        <img src="https://stemapp.in/<?=$gdata1[0]->ucimg?>" class="img-thumbnail"><hr>
                                          <input type="text" id="latitude1" value="<?=$gdata1[0]->clatitude?>" hidden>
                                          <input type="text" id="longitude1" value="<?=$gdata1[0]->clongitude?>" hidden>
                                          <div id="result1"></div>
                                              <script>
                                                function getAddressFromLatLng(latitude, longitude) {
                                                  const apiUrl = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`;
                                                  fetch(apiUrl)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                    if (data.display_name) {
                                                      const address = data.display_name;
                                                      document.getElementById('result1').textContent = address;
                                                    } else {
                                                      document.getElementById('result1').textContent = 'No results found';
                                                    }
                                                  })
                                                    .catch(error => {
                                                    document.getElementById('result1').textContent = 'Error: ' + error;
                                                  });
                                                }
                                                const latitude1 = parseFloat(document.getElementById('latitude1').value);
                                                const longitude1 = parseFloat(document.getElementById('longitude1').value);
                                                if (!isNaN(latitude1) && !isNaN(longitude1)) {
                                                  getAddressFromLatLng(latitude1, longitude1);
                                                } else {
                                                  document.getElementById('result1').textContent = 'Invalid coordinates';
                                                }
                                              </script>
                                        
                                        
                                        <div class="rounded-circle bg-danger" style="position: absolute;
                                            bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;
                                            bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                                </div>
                        </div>
                        
                        
                        
                        <div class="col-lg-12 col-sm">
                        <div class="card card-primary card-outline card-outline-tabs">
                          <h4 class="p-3">Task Updated</h4>
                            <div class="row p-3">
                      <div class="col-lg-6 col-sm">
                          <div id="tistatusgraphu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytis($uid, '2023-08-22');
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->stame . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Updated Againt Status',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytis($uid, '2023-08-22');foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->sclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tistatusgraphu'));
                                    chart.draw(data, options);
                                }
                            </script>
                          
                      </div>
                      <div class="col-lg-6 col-sm">
                          <div id="tiactiongraphu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Action', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytia($uid, '2023-08-22');
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->acname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Updated Againt Action',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytia($uid, '2023-08-22');foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->aclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tiactiongraphu'));
                                    chart.draw(data, options);
                                }
                            </script>
                      
                      </div>
                      <div class="col-lg-6 col-sm">
                          <div id="tipartnergraphu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Action', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytip($uid, '2023-08-22');
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->pname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Updated Againt Partner',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytip($uid, '2023-08-22');foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->pclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tipartnergraphu'));
                                    chart.draw(data, options);
                                }
                            </script>
                      </div>
                      
                      
                      
                      <div class="col-lg-6 col-sm">
                            <div id="tilateontimeu"></div>
                            <script>
                                google.charts.load("current", { packages: ["corechart"] });
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var sampleData = [
                                        ['Action', 'No of Company', 'URL'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytiol($uid, '2023-08-22');?>
                                        ["Late (<?=$gdata[0]->late?>)",'<?=$gdata[0]->late?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/1"],
                                        ["Ontime (<?=$gdata[0]->ontime?>)",'<?=$gdata[0]->ontime?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/2"],
                                    ];
                                    var data = google.visualization.arrayToDataTable(sampleData);
                                    var options = {
                                        title: 'Task Updated Ontime/Late',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            0: { color: 'red' },
                                            1: { color: 'green' },
                                        }
                                    };
                        
                                    var chart = new google.visualization.PieChart(document.getElementById('tilateontimeu'));
                                    google.visualization.events.addListener(chart, 'select', chartSliceClick);
                                    chart.draw(data, options);
                                    function chartSliceClick() {
                                        var selectedItem = chart.getSelection()[0];
                                        if (selectedItem) {
                                            var selectedSliceURL = data.getValue(selectedItem.row, 2);
                                            if (selectedSliceURL) {
                                                window.open(selectedSliceURL, '_blank');
                                            }
                                        }
                                    }
                                }
                            </script>

                      </div>
                      
                      
                      <div class="col-lg-6 col-sm">
                            <div id="converstiontvu"></div>
                            <script>
                                google.charts.load("current", { packages: ["corechart"] });
                                google.charts.setOnLoadCallback(drawChart);
                        
                                function drawChart() {
                                    // Sample data for demonstration
                                    var sampleData = [
                                        ['Action', 'No of Company', 'URL'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytiol($uid, '2023-08-22');?>
                                        ["Late (<?=$gdata[0]->late?>)",'<?=$gdata[0]->late?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/1"],
                                        ["Ontime (<?=$gdata[0]->ontime?>)",'<?=$gdata[0]->ontime?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/2"],
                                    ];
                        
                                    var data = google.visualization.arrayToDataTable(sampleData);
                        
                                    var options = {
                                        title: 'Task Vs Convertion',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            0: { color: 'red' },
                                            1: { color: 'green' },
                                        }
                                    };
                        
                                    var chart = new google.visualization.PieChart(document.getElementById('converstiontvu'));
                                    google.visualization.events.addListener(chart, 'select', chartSliceClick);
                                    chart.draw(data, options);
                                    function chartSliceClick() {
                                        var selectedItem = chart.getSelection()[0];
                                        if (selectedItem) {
                                            var selectedSliceURL = data.getValue(selectedItem.row, 2);
                                            if (selectedSliceURL) {
                                                window.open(selectedSliceURL, '_blank');
                                            }
                                        }
                                    }
                                }
                            </script>

                      </div>
                      
                      
                      
                      <div class="col-lg-6 col-sm">
                            <div id="converstionu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytis($uid, '2023-08-22');
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->stame . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Status Wise Task Converstion',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytis($uid, '2023-08-22');foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->sclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('converstionu'));
                                    chart.draw(data, options);
                                }
                            </script>

                      </div>
                      
                        
                        
                        </div>
                    </div>
                </section>
                
                        

                        <script>
                            document.getElementById("grid-view-btn").addEventListener("click", function () {
                                document.getElementById("grid-view").style.display = "block";
                                document.getElementById("list-view").style.display = "none";
                            });
                        
                            document.getElementById("list-view-btn").addEventListener("click", function () {
                                document.getElementById("grid-view").style.display = "none";
                                document.getElementById("list-view").style.display = "block";
                            });
                        </script>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
<style>

#myInput {
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myUL {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

#myUL li a {
  border: 1px solid #ddd;
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6;
  padding: 12px;
  text-decoration: none;
  font-size: 18px;
  color: black;
  display: block
}

#myUL li a:hover:not(.header) {
  background-color: #eee;
}
</style>


<script>
function myFunction() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
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