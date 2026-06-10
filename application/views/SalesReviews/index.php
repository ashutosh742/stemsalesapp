<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>STEM APP | Sales Reviews | Modern Filter</title>
    <!-- Google Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE & Premium Bootstrap -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <!-- DataTables (kept for potential usage) -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Global modern touches */
        body {
            background: #f4f7fc;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        /* Override card styles for modern look */
        .modern-card {
            border: none;
            border-radius: 32px;
            background: #ffffff;
            /* box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.15), 0 4px 12px rgba(0, 0, 0, 0.05); */
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .modern-card:hover {
            box-shadow: 0 30px 55px -15px rgba(0, 0, 0, 0.2);
        }
        .card-header-modern {
            background: linear-gradient(105deg, #2b2d42 0%, #1e2a5e 100%);
            padding: 1.2rem 2rem;
            border-bottom: none;
        }
        .card-header-modern h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
            letter-spacing: -0.2px;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .card-header-modern h3 i {
            font-size: 1.8rem;
            background: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 60%;
        }
        .card-body-modern {
            padding: 2rem 2rem 2rem 2rem;
        }
        /* Form group modern styling */
        .modern-form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }
        .modern-form-group label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            letter-spacing: -0.2px;
        }
        .modern-form-group label i {
            color: #4f46e5;
            font-size: 1.1rem;
        }
        .modern-select {
            width: 100%;
            padding: 0.8rem 1.2rem;
            font-size: 1rem;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            background-color: #fefefe;
            transition: all 0.25s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234f46e5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.2rem center;
            background-size: 1.2rem;
            cursor: pointer;
        }
        .modern-select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            background-color: #fff;
        }
        /* Button group */
        .btn-gradient-primary {
            background: linear-gradient(95deg, #6366f1 0%, #a855f7 100%);
            border: none;
            padding: 0.9rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 40px;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 18px rgba(99, 102, 241, 0.3);
            letter-spacing: 0.3px;
        }
        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(95deg, #4f46e5 0%, #9333ea 100%);
            box-shadow: 0 15px 25px -8px rgba(79, 70, 229, 0.5);
            color: white;
        }
        .btn-outline-modern {
            background: transparent;
            border: 1.5px solid #cbd5e1;
            padding: 0.9rem 1.8rem;
            font-weight: 500;
            border-radius: 40px;
            color: #334155;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-outline-modern:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: flex-start;
            margin-top: 1rem;
        }
        /* Info badge */
        .info-badge {
            background: #f1f5f9;
            padding: 0.7rem 1.2rem;
            border-radius: 60px;
            font-size: 0.85rem;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 1rem;
        }
        .content-header h1 {
            font-weight: 800;
            background: linear-gradient(125deg, #1e293b, #3b3b8c);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .card-body-modern {
                padding: 1.5rem;
            }
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-gradient-primary, .btn-outline-modern {
                justify-content: center;
            }
        }
        /* fix spacing */
        .content-wrapper {
            background: #f4f7fc;
        }
        hr.modern-divider {
            margin: 0.5rem 0 1.5rem;
            border: 0;
            height: 2px;
            background: linear-gradient(90deg, #cbd5e1, transparent);
        }
        /* subtle floating effect for card */
        .floating-icon {
            transition: transform 0.2s;
        }
        .action-buttons {
    align-items: center;
    justify-content: center;
}
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar (dynamic from department) -->
    <?php $this->load->view($dep_name.'/nav.php');?>
    <!-- /.navbar -->

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">





             <?php if ($this->session->flashdata('pending_message')): ?>
            <div class="alert alert-success alert-dismissible">
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
        <?php
        if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <?php endif; ?>
         <?php if ($this->session->flashdata('check_error_message')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong> <?php echo $this->session->flashdata('check_error_message'); ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <?php endif; ?>



                <div class="row mb-3 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-chart-line mr-2" style="color:#4f46e5;"></i> 
                            Sales Reviews
                        </h1>
                        <p class="text-muted mt-1 mb-0">Filter and analyze reviews performance</p>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right bg-transparent">
                            <li class="breadcrumb-item"><a href="<?=base_url();?>" class="text-decoration-none"><i class="fas fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item active text-primary">Sales Reviews</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content - Modern Filter Card -->
        <section class="content">
            <div class="container-fluid">
                <!-- Modern Filter Card -->
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h3>
                            <i class="fas fa-filter"></i> 
                            Sales Review - BD Wise
                        </h3>
                    </div>
                    <div class="card-body-modern">
                        <!-- Important: form action points to controller method -->
                        <form action="<?=base_url();?>SalesReviews/PlannedSalesReviews" method="POST" id="modernReviewForm">
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="modern-form-group">
                                        <label for="reviewTypeSelect">
                                            <i class="fas fa-tag"></i> Review Category
                                        </label>
                                        <select name="reviews_type" id="reviewTypeSelect" class="modern-select">
                                            <option value="">-- All review types --</option>
                                            <?php if(!empty($reviews_type)): ?>
                                                <?php foreach($reviews_type as $type): ?>
                                                    <option value="<?= htmlspecialchars($type->id, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?= htmlspecialchars($type->name, ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option disabled>No types available</option>
                                            <?php endif; ?>
                                        </select>
                                        <small class="form-text text-muted">Select a specific review category or leave blank for all</small>
                                    </div>
                            
                                    <hr>

                                    
                                        <div class="action-buttons d-flex">
                                            <center>
                                                <button type="submit" class="btn-gradient-primary w-100">
                                                    <i class="fas fa-chart-simple"></i> Planned
                                                </button>
                                            </center>
                                        </div>
                                    

                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Additional design element: visual hint or recent summary (optional) -->
                <div class="row mt-2 mb-4">
                    <div class="col-md-12 text-center">
                        <div class="d-inline-block px-4 py-2 bg-white rounded-pill shadow-sm">
                            <i class="fas fa-chart-line text-primary mr-2"></i>
                            <span class="font-weight-bold text-secondary">Data-driven reviews — smart decisions start here</span>
                        </div>
                    </div>
                </div>

                <!-- Optional: upcoming or note, no extra clutter -->
                <div class="row">
                    <div class="col-12">
                        <div class="small text-muted text-center">
                            <i class="far fa-clock"></i> Last sync: today | Powered by STEM Analytics
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>" class="text-primary">Stemlearning</a>.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline-block"><b>Version</b> 2.0 <i class="fas fa-rocket ml-1"></i></div>
    </footer>
    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<!-- DataTables (optional but kept for consistency) -->
<script src="<?=base_url();?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>assets/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url();?>assets/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<!-- Toastr for modern notifications -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>

<script>

</script>
