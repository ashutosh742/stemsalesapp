<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>STEM APP Organization | WebAPP</title>
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
    <script src="https://balkan.app/js/OrgChart.js"></script>
    <style>
      .scrollme {
      overflow-x: auto;
      }
      /*CSS*/

/* html, body {
    margin: 0px;
    padding: 0px;
    width: 100%;
    height: 100%;
    overflow: hidden;
    font-family: Helvetica;
} */

#tree {
    width: 100%;
    /* min-height: 100%; */
}


/*partial*/
.node.QA rect {
    fill: #F57C00;
}

.node.Dev rect {
    fill: #039BE5;
}

.node.Marketing rect {
    fill: #FFCA28;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <!-- <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">This Year RP Meeting</h1>
              </div>
            </div>
          </div>
        </div> -->
        <section class="content">
          <div class="container-fluid">
            <?php //dd($allusers); ?>
            <div id="tree"></div>
          </div>
      </div>
    </div>
    </section>

    <?php 


$text = '['; 
$i = 1;


 
foreach($allusers as $users) { 
    if($users->type_id == 2 && $users->user_id == 45) {
            $typesname = $this->Menu_model->get_user_types($users->type_id)[0]->name;
        $text .= '{ "id": "' . $users->user_id . '", "name": "' . $users->name . '", "title": "' .$typesname. '", "email": "' . $users->email . '","phone": "' . $users->phoneno . '","user_id": "' . $users->user_id . '","username": "' .$users->username. '","password": "' . $users->password . '", "img": "' . base_url().$users->photo . '" },';
    }
    $i++;
}
$j = 1;
foreach($allusers as $users) { 

    if($users->type_id == 15 && $users->admin_id !=0 && $users->admin_id ==45) {
        $typesname = $this->Menu_model->get_user_types($users->type_id)[0]->name;
        $text .= '{ "id": "' . $users->user_id . '", "pid": "' . $users->admin_id . '", "name": "' . $users->name . '", "title": "' .$typesname. '", "email": "' . $users->email . '","phone": "' . $users->phoneno . '","user_id": "' . $users->user_id . '","username": "' .$users->username. '","password": "' . $users->password . '", "img": "' . base_url().$users->photo . '" },';
    }
    $j++;
}


foreach($allusers as $users) { 
    if($users->type_id == 4 && $users->sales_co !=0 && $users->admin_id ==45) {
        $typesname = $this->Menu_model->get_user_types($users->type_id)[0]->name;
        $text .= '{ "id": "' . $users->user_id . '", "pid": "' . $users->sales_co . '", "name": "' . $users->name . '", "title": "PST", "email": "' . $users->email . '","phone": "' . $users->phoneno . '","user_id": "' . $users->user_id . '","username": "' .$users->username. '","password": "' . $users->password . '", "img": "' . base_url().$users->photo . '" },';
    }
}

foreach($allusers as $users) { 
    if($users->type_id == 13 && $users->pst_co !=0 && $users->admin_id ==45) {
        $typesname = $this->Menu_model->get_user_types($users->type_id)[0]->name;
        $text .= '{ "id": "' . $users->user_id . '", "pid": "' . $users->pst_co . '", "name": "' . $users->name . '", "title": "Cluster Manager", "email": "' . $users->email . '","phone": "' . $users->phoneno . '","user_id": "' . $users->user_id . '","username": "' .$users->username. '","password": "' . $users->password . '", "img": "' . base_url().$users->photo . '" },';
    }
}

foreach($allusers as $users) { 
    if($users->type_id == 3 && $users->aadmin !=0 && $users->admin_id ==45) {
        $typesname = $this->Menu_model->get_user_types($users->type_id)[0]->name;
        $text .= '{ "id": "' . $users->user_id . '", "pid": "' . $users->aadmin . '", "name": "' . $users->name . '", "title": "Sales Person", "email": "' . $users->email . '","phone": "' . $users->phoneno . '","user_id": "' . $users->user_id . '","username": "' .$users->username. '","password": "' . $users->password . '", "img": "' . base_url().$users->photo . '" },';
    }
}

$data = rtrim($text, ',');

$data .= ']';
// echo $data;
?>


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

let nodes =
    <?= $data; ?>;

for (let i = 0; i < nodes.length; i++) {
    let node = nodes[i];
    switch (node.title) {
        case "QA":
            node.tags = ["QA"];
            break;
        case "Marketer":
        case "Designer":
        case "Sales Manager":
            node.tags = ["Marketing"];
            break;
    }
}

let options = getOptions();
let chart = new OrgChart(document.getElementById("tree"), {    
    mouseScrool: OrgChart.none,    
    scaleInitial: OrgChart.match.width,
    scaleInitial: options.scaleInitial,
    enableSearch: true,
    mode: 'dark',
    layout: OrgChart.mixed,
    nodeBinding: {
        field_0: "name",
        field_1: "title",
        img_0: "img"
    }
});

chart.load(nodes)

function getOptions(){
    const searchParams = new URLSearchParams(window.location.search);
    let fit = searchParams.get('fit');
    let enableSearch = true;
    let scaleInitial = 1;
    if (fit == 'yes'){
        enableSearch = false;
        scaleInitial = OrgChart.match.boundary;
    }
    return {enableSearch, scaleInitial};
}


    </script>
  </body>
</html>