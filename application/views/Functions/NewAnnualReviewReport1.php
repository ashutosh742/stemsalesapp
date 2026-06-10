<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>STEM APP | Annual Review Report</title>
    <!-- Google Fonts: Modern Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE & Plugins (preserved paths) -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    
    <style>
        /* ----- MODERN PREMIUM RESET & VARIABLES ----- */
        :root {
            --primary-gradient: linear-gradient(135deg, #1e2b6e 0%, #0f1a47 100%);
            --accent-gradient: linear-gradient(105deg, #2563eb 0%, #38bdf8 100%);
            --card-radius: 24px;
            --card-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.02);
            --hover-shadow: 0 25px 40px -14px rgba(0, 0, 0, 0.12);
            --transition-smooth: all 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            --border-light: 1px solid rgba(0, 0, 0, 0.05);
            --glass-bg: rgba(255, 255, 255, 0.92);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: #f5f7fc;
            letter-spacing: -0.01em;
        }

        /* Premium card redesign */
        .card {
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            border: none;
            transition: var(--transition-smooth);
            background: #ffffff;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            background: rgba(255, 255, 255, 0.98);
            border-bottom: var(--border-light);
            padding: 1.2rem 1.5rem;
        }

        /* Modern filter card (glassmorphism touch) */
        .filter-card {
            background: #ffffff;
            backdrop-filter: blur(0px);
            border-radius: 28px;
            padding: 1.2rem 1.8rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.04), 0 1px 1px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.03);
            transition: var(--transition-smooth);
        }

        .filter-card .form-group label {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #1e293b;
            margin-bottom: 0.35rem;
        }

        .filter-card select {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 2rem 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            background-color: #fefefe;
            transition: var(--transition-smooth);
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .filter-card select:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }

        /* Modern button */
        .btn-modern {
            background: var(--accent-gradient);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.8rem;
            border-radius: 40px;
            font-size: 0.8rem;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 8px rgba(37,99,235,0.2);
            transition: var(--transition-smooth);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(37,99,235,0.3);
            background: linear-gradient(105deg, #1d4ed8 0%, #0ea5e9 100%);
        }

        /* Hero header inside card */
        .report-hero {
            background: var(--primary-gradient);
            border-radius: 20px;
            margin: -0.2rem 0.8rem 1.2rem 0.8rem;
            padding: 1.2rem 1rem;
            box-shadow: 0 10px 18px -8px rgba(0, 0, 0, 0.2);
        }

        .report-hero h3 {
            font-weight: 700;
            letter-spacing: -0.3px;
            font-size: 1.7rem;
            margin: 0;
        }

        .report-hero h4 {
            font-weight: 500;
            opacity: 0.9;
            font-size: 1.2rem;
            margin-top: 0.3rem;
            margin-bottom: 0;
        }

        /* Modern table design */
        .table-responsive-modern {
            overflow-x: auto;
            border-radius: 20px;
        }

        .table {
            font-size: 0.8rem;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table thead th {
            background: #f8fafd;
            color: #0f172a;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 1rem 0.75rem;
            border-bottom: 1.5px solid #e2edf2;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: var(--transition-smooth);
            border-bottom: 1px solid #f0f2f5;
        }

        .table tbody tr:hover {
            background-color: #fafcff;
            transform: scale(1.002);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .table tbody td {
            padding: 0.9rem 0.75rem;
            vertical-align: middle;
            font-weight: 500;
            color: #1e293b;
            border-top: none;
        }

        .table a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            border-bottom: 1px dotted transparent;
            transition: 0.2s;
        }

        .table a:hover {
            color: #1e40af;
            border-bottom: 1px dotted #2563eb;
        }

        /* DataTables buttons modernize */
        .dt-buttons .btn {
            background: #f1f5f9;
            border: none;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3rem 0.9rem;
            margin: 0 2px;
            color: #1e293b;
            transition: all 0.2s;
        }

        .dt-buttons .btn:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        /* pagination & search styling */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 40px;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 30px;
            background: transparent;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--accent-gradient);
            border: none;
            color: white !important;
        }

        /* scrollbar premium */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #eef2f6;
            border-radius: 12px;
        }

        ::-webkit-scrollbar-thumb {
            background: #b9c3d0;
            border-radius: 12px;
        }

        /* footer */
        .main-footer {
            border-top: none;
            background: #fff;
            border-radius: 32px 32px 0 0;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.02);
            font-size: 0.8rem;
            padding: 1rem 1.5rem;
        }

        /* badge for counts */
        .stat-badge {
            font-weight: 700;
        }

        /* responsiveness improvements */
        @media (max-width: 768px) {
            .filter-card .form-group {
                width: 100%;
                margin-right: 0 !important;
                margin-bottom: 0.8rem;
            }
            .btn-modern {
                width: 100%;
            }
            .table thead th {
                font-size: 0.7rem;
                padding: 0.8rem 0.5rem;
            }
            .report-hero h3 {
                font-size: 1.2rem;
            }
        }

        /* Additional premium touches */
        .badge-custom {
            background: linear-gradient(120deg, #eef2ff, #ffffff);
            border-radius: 40px;
            padding: 0.2rem 0.6rem;
            font-weight: 600;
        }
        
        .table tbody td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }
        .table tbody td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar (dynamic from controller) -->
    <?php $this->load->view($dep_name.'/nav.php'); ?>
     <style>
    .card, table {
    background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
}
  </style>
    <!-- Content Wrapper -->
    <div class="content-wrapper" style="background: #f5f7fc;">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0"></h1></div>
                    <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><h4></h4></ol></div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- FILTER SECTION - Premium redesign -->
                <div class="filter-card">
                    <form class="form-inline w-100 flex-wrap align-items-center" 
                          method="POST" 
                          action="<?= base_url().'Menu/NewAnnualReviewReport' ?>">
                        
                        <div class="form-group mb-2 mr-3">
                            <label for="financialYearSelect"><i class="far fa-calendar-alt mr-1"></i> Financial Year</label>
                            <select name="teaget_fyear" id="financialYearSelect" class="form-control form-control-sm">
                                <option value="">Select</option>
                                <?php foreach($allReviewfinancialYear as $financialYear){ 
                                    $selected = ($cfyear == $financialYear->financial_year) ? 'selected' : '';
                                    echo '<option '.$selected.' value="'.$financialYear->financial_year.'">'.$financialYear->financial_year.'</option>';
                                } ?>
                            </select>
                        </div>

                       <div class="form-group mb-2 mr-3">
                        <label for="target_types">
                            <i class="fas fa-building mr-1"></i> Department
                        </label>

                        <select name="target_types" id="target_types" class="form-control form-control-sm">
                            <option value="">Select</option>

                            <?php foreach($user_types as $user_type):
                               $selected = '';
                                if($target_types == $user_type->id){
                                    $selected = 'selected';
                                }
                                ?>
                                <option <?=$selected?> value="<?= $user_type->id ?>">
                                    <?= $user_type->name ?>
                                </option>
                            <?php endforeach; ?>
                            
                        </select>
                    </div>

                        <div class="form-group mb-2">
                            <button type="submit" class="btn-modern"><i class="fas fa-sliders-h mr-1"></i> Apply Insights</button>
                        </div>
                    </form>
                </div>

                <!-- Main Card with Annual Review Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <div class="report-hero text-center">
                                    <h3 class="text-white m-0"><i class="fas fa-chart-line mr-2"></i>Annual Review Report</h3>
                                    <h4 class="text-white-50 m-0 mt-2"><?= $cfyear; ?> · Performance Intelligence</h4>
                                </div>
                            </div>
                            <!-- card-body with table -->
                            <div class="card-body pt-2">
                                <div class="container-fluid body-content px-0">
                                    <div class="table-responsive-modern text-nowrap">
                                        <table id="example1" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>BD Name</th>
                                                    <th>Total Companies</th>
                                                    <th>Complete Review</th>
                                                    <th>Pending Review</th>
                                                    <th>BD Marked Current Year Focus Funnel</th>
                                                    <th>BD Marked Q1 Closure</th>
                                                    <th>BD Marked To Be Nurture in Q1</th>
                                                    <th>Keep Company (Yes)</th>
                                                    <th>Keep Company (No)</th>
                                                    <th>Any Vmeeting Yes</th>
                                                    <th>Any Vmeeting No</th>
                                                    <th>Annual Focus Positive Yes</th>
                                                    <th>Annual Focus Positive No</th>
                                                    <th>Annual Topspender Yes</th>
                                                    <th>Annual Topspender No</th>
                                                    <th>Annual Upsell Yes</th>
                                                    <th>Annual Upsell No</th>
                                                    <th>Total Annual Revenue</th>
                                                    <th>Total Transfer The Funnel Yes</th>
                                                    <th>Pending for Check</th>
                                                    <th>Agree After Check</th>
                                                    <th>Disagree After Check</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; 
                                                $commingusertype = $user['type_id']; 
                                                $fyear = $cfyear;
                                                ?>
                                                <?php foreach($bd_annualData as $adata){ ?>
                                                    <tr>
                                                        <td><?=$i?></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewSessionReportData/'.$fyear.'/'.$adata->user_id.'/check_session'?>"><?=$adata->username;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/total_count'?>"><?=$adata->total_count;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/complete_review'?>"><?=$adata->complete_review;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/pending_review'?>"><?=$adata->pending_review;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/bd_marked_current_year_focus_funnel'?>"><?=$adata->bd_marked_current_year_focus_funnel;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/bd_marked_q1_closure'?>"><?=$adata->bd_marked_q1_closure;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/bd_marked_to_be_Nurture_in_q1'?>"><?=$adata->bd_marked_to_be_Nurture_in_q1;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/keep_company_yes'?>"><?=$adata->keep_company_yes;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/keep_company_no'?>"><?=$adata->keep_company_no;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/vmeeting_yes'?>"><?=$adata->vmeeting_yes;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/vmeeting_no'?>"><?=$adata->vmeeting_no;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/annaul_focuspositive_yes'?>"><?=$adata->annaul_focuspositive_yes;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/annaul_focuspositive_no'?>"><?=$adata->annaul_focuspositive_no;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/annaul_topspender_yes'?>"><?=$adata->annaul_topspender_yes;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/annaul_topspender_no'?>"><?=$adata->annaul_topspender_no;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/annaul_upsell_yes'?>"><?=$adata->annaul_upsell_yes;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/annaul_upsell_no'?>"><?=$adata->annaul_upsell_no;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/total_annaul_revenue'?>"><?=$adata->total_annaul_revenue;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/total_transfer_the_funnel_yes'?>"><?=$adata->total_transfer_the_funnel_yes;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/pending_for_check'?>"><?=$adata->pending_for_check;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/complete_for_check'?>"><?=$adata->complete_for_check;?></a></td>
                                                        <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportData/'.$fyear.'/'.$adata->user_id.'/reject_for_check'?>"><?=$adata->reject_for_check;?></a></td>
                                                    </tr>
                                                    <?php $i++; } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php $startYear = date("Y"); $endYear = $startYear + 1; ?>
    <footer class="main-footer">
        <strong>Copyright &copy; <?php echo "$startYear-$endYear"; ?> <a href="<?=base_url();?>">Stemlearning</a>.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline-block"><b>Version</b> 2.0</div>
    </footer>
    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<!-- Scripts (preserved) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script>$.widget.bridge('uibutton', $.ui.button)</script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url();?>assets/js/Chart.min.js"></script>
<script src="<?=base_url();?>assets/js/sparkline.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
<script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
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
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
    $(function () {
        var table = $("#example1").DataTable({
            responsive: false, 
            lengthChange: false, 
            autoWidth: false,
            buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
            dom: '<"d-flex justify-content-between align-items-center"Bf>rtip',
            language: { search: "🔍 Quick insight:" }
        });
        table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
</body>
</html>