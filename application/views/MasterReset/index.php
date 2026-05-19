<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STEM APP | Master Reset | WebAPP</title>
    <!-- Google Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE & Premium Bootstrap -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    <!-- Toastr (modern notifications) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Premium gradient backgrounds */
        .bg-premium-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08), 0 10px 20px rgba(0,0,0,0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 50px rgba(0,0,0,0.12), 0 15px 25px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 20px 20px 0 0 !important;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            font-weight: 600;
            padding: 1.2rem 1.5rem;
            border-bottom: none;
            display: flex;
            align-items: center;
        }
        .card-header i {
            font-size: 2rem;
            margin-right: 1rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem;
            border-radius: 50%;
        }
        .card-header .card-title {
            font-size: 1.4rem;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .card-header .card-tools .btn-tool {
            color: white;
            opacity: 0.9;
        }
        .card-header .card-tools .btn-tool:hover {
            opacity: 1;
        }
        .card-body {
            padding: 2rem 1.5rem;
            background: #ffffff;
            border-radius: 0 0 20px 20px;
            text-align: center;
        }
        .btn-reset {
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 10px 20px rgba(0, 13, 255, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
        }
        .btn-reset:hover {
            background: linear-gradient(135deg, #5a64e0 0%, #0000cc 100%);
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 13, 255, 0.4);
            color: white;
        }
        .btn-reset i {
            font-size: 1.2rem;
        }
        .content-header {
            padding: 1.5rem 0 0.5rem 0;
        }
        .content-header h1 {
            font-size: 2.2rem;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            margin-bottom: 0;
        }
        .content-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        .card-description {
            color: #6c757d;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        /* Different gradient for each card header */
        .card.zone .card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .card.bd .card-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .card1.bd {
    align-items: center;
    justify-content: center;
    display: flex;
    height: 290px;
    background: white;
}
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <?php $this->load->view($dep_name.'/nav.php');?>
    <!-- /.navbar -->

    <style>
        .card {
    background: white;
}
    </style>
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4><i class="fas fa-sync-alt mr-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i> Master Reset</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?=base_url();?>">Home</a></li>
                            <li class="breadcrumb-item active">Master Reset</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <br><br><br>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="text-center">
                    <hr>
                    <h2 style="color: navy;"><i class="fas fa-sync-alt mr-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i> Funnel Reset Management</h2>
                    <hr>
                </div>
                <div class="row">
                    <div class="col-12 mt-4">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Warning!</strong> These reset actions are <strong>irreversible</strong>. Please ensure you have a complete backup before proceeding.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Zone Wise Reset Card -->
                    <div class="col-md-4">
                        <div class="card zone">   <!-- Fixed class: was "card1 zone", now "card zone" to match CSS -->
                            <div class="card-header">
                                <i class="fas fa-globe-asia"></i>
                                <span class="card-title">Zone Wise Reset (Funnel)</span>
                            </div>
                            <div class="card-body">
                                <p class="card-description">
                                    <i class="fas fa-info-circle mr-1" style="color: #17a2b8;"></i>
                                    Reset all funnel data in Open / Open RPEM and configurations for a specific zone. This action is irreversible.
                                </p>
                                <a href="<?=base_url();?>MasterReset/ZoneWiseReset" class="btn btn-reset">
                                    <i class="fas fa-redo-alt"></i> Reset By Zone
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- BD Wise Reset Card -->
                    <div class="col-md-4">
                        <div class="card bd">     <!-- Fixed class: was "card1 bd", now "card bd" -->
                            <div class="card-header">
                                <i class="fas fa-user-tie"></i>
                                <span class="card-title">BD Wise Reset (Funnel)</span>
                            </div>
                            <div class="card-body">
                                <p class="card-description">
                                    <i class="fas fa-info-circle mr-1" style="color: #17a2b8;"></i>
                                    Reset all funnel data in Open / Open RPEM and configurations for a specific Business Development (BD) manager.
                                </p>
                                <a href="<?=base_url();?>MasterReset/BDWiseReset" class="btn btn-reset">
                                    <i class="fas fa-redo-alt"></i> Reset By BD
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card1 bd" style="align-items: center;">     <!-- Fixed class: was "card1 bd", now "card bd" -->
                           <img style="height: 250px; width: 250px;" src="http://stemapp.in/assets/img/conversion-rate-funnel-line-icon-vector.jpg" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <hr>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<br><br><br><br><br>
    <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline-block"><b>Version</b> 1.0</div>
    </footer>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
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
<!-- AdminLTE -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Moment (for datetime) -->
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>

<!-- Optional: Initialize toastr or other scripts -->
<script>
    $(function () {
        // Any additional JS can go here
    });
</script>
</body>
</html>