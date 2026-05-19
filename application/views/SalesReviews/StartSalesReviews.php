<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>STEM APP | Sales Reviews | Modern Rating</title>
    <!-- Google Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE & Premium Bootstrap -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <!-- DataTables (optional) -->
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
        .modern-card {
            border: none;
            border-radius: 32px;
            background: #ffffff;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
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
        /* Table styling */
        .review-table, .cluster-metrics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .review-table th, .review-table td,
        .cluster-metrics-table th, .cluster-metrics-table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        .review-table th, .cluster-metrics-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #1e293b;
        }
        .review-table tr:last-child td, .cluster-metrics-table tr:last-child td {
            border-bottom: none;
        }
        /* Star rating widget */
        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 4px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            cursor: pointer;
            font-size: 1.4rem;
            color: #ddd;
            transition: color 0.2s;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #f5b301;
        }
        /* Remarks textarea - disabled style */
        .remarks-textarea {
            width: 100%;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
            padding: 0.5rem 1rem;
            resize: vertical;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .remarks-textarea:disabled {
            background-color: #f1f5f9;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .user-separator {
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-top: 1rem;
            border-top: 2px dashed #e2e8f0;
        }
        .user-separator:first-of-type {
            border-top: none;
            margin-top: 0;
        }
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
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 768px) {
            .card-body-modern {
                padding: 1.5rem;
            }
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            .review-table, .cluster-metrics-table {
                font-size: 0.85rem;
            }
            .review-table th, .review-table td,
            .cluster-metrics-table th, .cluster-metrics-table td {
                padding: 8px 10px;
            }
            .star-rating label {
                font-size: 1.2rem;
            }
            .card-header-modern {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        .content-header h1 {
            font-weight: 800;
            background: linear-gradient(125deg, #1e293b, #3b3b8c);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .value-link a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }
        .value-link a:hover {
            text-decoration: underline;
        }
        .text-muted small {
            font-size: 0.75rem;
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
        /* Overall message textarea */
        .overall-message {
            margin-top: 1rem;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
            padding: 0.75rem 1rem;
            width: 100%;
            resize: vertical;
        }
        .rating-counter {
            font-size: 0.85rem;
            background: #f1f5f9;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .action-buttons {
            align-items: center;
            justify-content: center;
        }
        /* required indicator */
        .required-star {
            color: #e11d48;
            margin-left: 4px;
            font-size: 0.8rem;
        }
        /* Timer styles */
        .session-timer {
            background: linear-gradient(135deg, #f5f7fa 0%, #eef2f6 100%);
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .session-timer i {
            color: #4f46e5;
            font-size: 1.2rem;
        }
        .session-timer span {
            font-family: monospace;
            font-size: 1.1rem;
            letter-spacing: 1px;
            background: #fff;
            padding: 0.2rem 0.6rem;
            border-radius: 40px;
            color: #0f172a;
        }
        /* Cluster table link styling */
        .cluster-metrics-table a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }
        .cluster-metrics-table a:hover {
            text-decoration: underline;
        }
        .cluster-metrics-table td {
            background-color: #ffffff;
        }
        .cluster-block {
            margin-top: 2rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 20px;
            border-left: 4px solid #4f46e5;
        }
        .cluster-header {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cluster-header i {
            color: #4f46e5;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar (dynamic from department) -->
    <?php $this->load->view($dep_name.'/nav.php');?>
    <!-- /.navbar -->

    <?php 
    function formatNumber($num) {
        return number_format($num);
    }
    function formatIndianUnits($amount, $decimals = 2, $symbol = '₹') {
        $sign = $amount < 0 ? '-' : '';
        $abs = abs($amount);
        if ($abs >= 10000000) {
            $value = $abs / 10000000;
            $formatted = number_format($value, $decimals, '.', ',') . 'Cr';
        } elseif ($abs >= 100000) {
            $value = $abs / 100000;
            $formatted = number_format($value, $decimals, '.', ',') . 'L';
        } elseif ($abs >= 1000) {
            $value = $abs / 1000;
            $formatted = number_format($value, $decimals, '.', ',') . 'K';
        } else {
            $formatted = number_format($abs, $decimals, '.', ',');
        }
        return $sign . $symbol . $formatted;
    }

    // Get existing session time from database (in minutes)
    $dbSessionMinutes = isset($startReviews[0]->session_time) ? (int)$startReviews[0]->session_time : 0;
    $dbSessionSeconds = $dbSessionMinutes * 60;
    $reviewId = $startReviews[0]->id;

    // Group cluster details by user_id for easy access inside the main user loop
    $clusterDetailsByUser = [];
    if (!empty($totalClusterVisitDetailsByuser)) {
        foreach ($totalClusterVisitDetailsByuser as $clusterRow) {
            $clusterDetailsByUser[$clusterRow->user_id][] = $clusterRow;
        }
    }

    // Define cluster metrics with labels and links (same as in original request)
    $clusterMetricsTemplate = [
        'total' => ['label' => 'Total Company', 'link' => "Reports/FunnelDetailsWithClusterID/total/{user_id}/{cluster_id}/userwise"],
        'base_location' => ['label' => 'Base Location', 'link' => "Reports/FunnelDetailsWithClusterID/base_location/{user_id}/{cluster_id}/userwise"],
        'outStation_location' => ['label' => 'OutStation Location', 'link' => "Reports/FunnelDetailsWithClusterID/outStation_location/{user_id}/{cluster_id}/userwise"],
        'total_planned_meeting' => ['label' => 'Planned Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_planned_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_complete_meeting' => ['label' => 'Completed Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_pending_for_meeting' => ['label' => 'Pending Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_pending_for_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_rp_complete_meeting' => ['label' => 'RP Complete Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_rp_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_no_rp_complete_meeting' => ['label' => 'No RP Complete Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_no_rp_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_ogt_complete_meeting' => ['label' => 'OGT Complete Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_ogt_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_deleted_meeting' => ['label' => 'Deleted Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_deleted_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_complete_planned_sheduled_meeting' => ['label' => 'Complete Planned Scheduled in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_planned_sheduled_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_complete_planned_barg_in_meeting' => ['label' => 'Complete Planned Barg‑in in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_planned_barg_in_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_complete_planned_virtual_meeting' => ['label' => 'Complete Planned Virtual in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_planned_virtual_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_proposal_sent' => ['label' => 'Proposal Sent in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_proposal_sent/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        'total_clouser' => ['label' => 'Closure in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_clouser/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],

        // 'total_pending_for_meeting_20_clouser_funnel' => ['label' => 'Pending Meeting (20% Closure)', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_pending_for_meeting_20_clouser_funnel/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        // 'total_pending_for_proposal_20_clouser_funnel' => ['label' => 'Pending Proposal (20% Closure)', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_pending_for_proposal_20_clouser_funnel/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        // 'total_pending_for_meeting_potential_funnel' => ['label' => 'Pending Meeting (Potential)', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_pending_for_meeting_potential_funnel/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
        // 'total_pending_proposal_to_be_sent_after_review' => ['label' => 'Pending Proposal After Review', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_pending_proposal_to_be_sent_after_review/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"]
    ];
    $clusterMetricsCount = count($clusterMetricsTemplate);
    ?>

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
                <?php if ($this->session->flashdata('error_message')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><?php echo $this->session->flashdata('error_message'); ?></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <hr>
                <div class="row mb-3 align-items-center">
                    <div class="col-sm-12 text-center">
                        <h1 class="m-0">
                            <i class="fas fa-star-half-alt mr-2" style="color:#4f46e5;"></i> 
                            Performance Review - <?= $startReviews[0]->review_types_name ?>
                        </h1>
                        <p class="text-muted mt-1 mb-0">Rate and remark on each sales metric for your team</p>
                        <p class="text-primary mt-1 mb-0"><?= $sdate ?> to <?= $edate ?></p>
                        <p class="text-success mt-1 mb-0"><?= $totalClosurePipeLineDatasByuser[0]->username ?></p>
                        <p class="text-success mt-1 mb-0">[ Review Start Date: <?= $startReviews[0]->start_date ?> ... On Going ]</p>
                    </div>
                </div>
                <hr>
            </div>
        </section>

        <?php if (empty($startReviews[0]->to_uid)){ ?>
            <section class="content">
                <div class="container-fluid">
                    <!-- Modern Filter Card -->
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h3>
                                <i class="fas fa-filter"></i> 
                                Sales Review - Set BD 
                            </h3>
                        </div>
                        <div class="card-body-modern">
                            <form action="<?=base_url();?>SalesReviews/PlannedUpdatesSalesReviews" method="POST" id="modernReviewForm">
                                <input type="text" value="<?= $startReviews[0]->id ?>" name="review_id" required hidden>
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="modern-form-group">
                                            <label for="userSelect">
                                                <i class="fas fa-user-circle"></i> Set Team Member
                                            </label>
                                            <select name="userSelect" id="userSelect" class="modern-select">
                                                <option value="">-- All team members --</option>
                                                <?php if(!empty($allteams)): ?>
                                                    <?php foreach($allteams as $team): ?>
                                                        <option value="<?= $team->user_id; ?>">
                                                            <?= $team->name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option disabled>No users found</option>
                                                <?php endif; ?>
                                            </select>
                                            <small class="form-text text-danger">Total remaining users in your team : <?php echo count($allteams); ?> </small>
                                        </div>
                                        <hr>
                                        <div class="action-buttons d-flex">
                                            <center>
                                                <button type="submit" class="btn-gradient-primary w-100">
                                                    <i class="fas fa-chart-simple"></i> Set BD
                                                </button>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-2 mb-4">
                        <div class="col-md-12 text-center">
                            <div class="d-inline-block px-4 py-2 bg-white rounded-pill shadow-sm">
                                <i class="fas fa-chart-line text-primary mr-2"></i>
                                <span class="font-weight-bold text-secondary">Data-driven reviews — smart decisions start here</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="small text-muted text-center">
                                <i class="far fa-clock"></i> Last sync: today | Powered by STEM Analytics
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php } else { ?> 
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php if (!empty($totalClosurePipeLineDatasByuser)): ?>
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3>
                                    <i class="fas fa-chalkboard-user"></i> 
                                    Team Performance Metrics - <?= $sdate . ' to ' . $edate ?>
                                </h3>
                                <div class="session-timer">
                                    <h3>
                                        <a target="_blank" href="<?=base_url();?>Reports/ReviewReportBDwiseDetails/<?= $startReviews[0]->id ?>">
                                            <i class="fas fa-chalkboard-user"></i> View Details
                                        </a>
                                    </h3>
                                </div>
                                <div class="session-timer">
                                    <i class="fas fa-hourglass-half"></i>
                                    Total time spent: <span id="timerDisplay">--:--:--:--</span>
                                </div>
                            </div>
                            <div class="card-body-modern">
                                <form action="<?=base_url();?>SalesReviews/saveReviewRatings" method="POST" id="reviewRatingForm">
                                    <input type="text" value="<?= $startReviews[0]->id ?>" name="review_id" required hidden>
                                    <input type="text" value="<?= $sdate ?>" name="sdate" required hidden>
                                    <input type="text" value="<?= $edate ?>" name="edate" required hidden>
                                    <!-- Hidden field to store session duration (in seconds) -->
                                    <input type="hidden" name="session_duration_seconds" id="session_duration_seconds" value="0">

                                    <?php 
                                    // Define metrics as a template with placeholders for user_id, sdate, edate
                                    $metricsTemplate = [
                                        'total_funnel' => ['label' => 'Total Funnel', 'link' => "Reports/FunnelDetails/total/{user_id}/userwise"],
                                        'total_funnel_complete_rp_meeting' => ['label' => 'Funnel Complete RP Meeting', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total__funnel_pending_for_rp_meeting' => ['label' => 'Funnel Pending for RP Meeting', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_pending_for_rp_meeting/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_funnel_complete_rp_meeting_by_other_bd' => ['label' => 'Funnel Complete by Other BD', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_other_bd/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_funnel_complete_rp_meeting_by_line_manager' => ['label' => 'Funnel Complete by Line Manager', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_line_manager/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_call_connected_after_rp_meeting' => ['label' => 'Calls Connected (after RP)', 'link' => "Reports/ClosurePipeLineDetails/total_call_connected_after_rp_meeting/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_line_manager_call_connected_after_rp_meeting' => ['label' => 'Line Manager Calls (after RP)', 'link' => "Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_rp_meeting/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_call_not_connected_after_rp_meeting' => ['label' => 'Calls Not Connected (after RP)', 'link' => "Reports/ClosurePipeLineDetails/total_call_not_connected_after_rp_meeting/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_proposal_sent' => ['label' => 'Proposal Sent', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_pending_for_proposal_sent' => ['label' => 'Pending Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_pending_for_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_proposal_sent_without_meeting' => ['label' => 'Proposal Sent without Meeting', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_without_meeting/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_proposal_sent_remeeting_done' => ['label' => 'Proposal Sent – Re-meeting Done', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_done/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_proposal_sent_remeeting_not_done' => ['label' => 'Proposal Sent – Re-meeting Not Done', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_not_done/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_proposal_sent_by_current_bd' => ['label' => 'Proposal Sent by Current BD', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_by_current_bd/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_proposal_sent_by_other_bd' => ['label' => 'Proposal Sent by Other BD', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_by_other_bd/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_closure' => ['label' => 'Total Closure', 'link' => "Reports/ClosurePipeLineDetails/total_closure/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_direct_closure_without_proposal_sent' => ['label' => 'Direct Closure (no proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_direct_closure_without_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_closure_after_proposal_sent' => ['label' => 'Closure after Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_closure_after_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_not_closure_after_proposal_sent' => ['label' => 'Not Closure after Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_not_closure_after_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_budget_where_proposal_sent' => ['label' => 'Budget (Proposal Sent)', 'link' => "Reports/ClosurePipeLineDetails/total_budget_where_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_closure_budget_where__proposal_sent' => ['label' => 'Closure Budget (Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_closure_budget_where__proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_not_closure_budget_where__proposal_sent' => ['label' => 'Not Closure Budget (Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_not_closure_budget_where__proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_wdl_or_ni_after_proposal_sent' => ['label' => 'WDL/NI after Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_wdl_or_ni_after_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_call_connected_after_proposal_sent' => ['label' => 'Calls Connected (after Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_line_manager_call_connected_after_proposal_sent' => ['label' => 'Line Manager Calls (after Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_call_not_connected_after_proposal_sent' => ['label' => 'Calls Not Connected (after Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_call_connected_after_proposal_sent_without_meeting' => ['label' => 'Calls Connected (Proposal w/o Meeting)', 'link' => "Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent_without_meeting/{user_id}/{$sdate}/{$edate}/userwise"],
                                        'total_call_not_connected_after_proposal_sent_without_meeting' => ['label' => 'Calls Not Connected (Proposal w/o Meeting)', 'link' => "Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent_without_meeting/{user_id}/{$sdate}/{$edate}/userwise"]
                                    ];

                                    $totalMainMetricsCount = count($metricsTemplate) + 3; // +3 for target_budget, remaining_budget, target_achieved
                                    ?>

                                    <?php foreach ($totalClosurePipeLineDatasByuser as $row): ?>
                                        <?php
                                        // Replace placeholder {user_id} in each link with actual user ID
                                        $metrics = [];
                                        foreach ($metricsTemplate as $key => $metric) {
                                            $link = str_replace('{user_id}', $row->user_id, $metric['link']);
                                            $metrics[$key] = ['label' => $metric['label'], 'link' => $link];
                                        }

                                        // Calculate total metrics for this user: main + (clusters * clusterMetricsCount)
                                        $numClusters = isset($clusterDetailsByUser[$row->user_id]) ? count($clusterDetailsByUser[$row->user_id]) : 0;
                                        $totalUserMetrics = $totalMainMetricsCount + ($numClusters * $clusterMetricsCount);
                                        ?>
                                        <div class="user-separator" data-user-id="<?= $row->user_id ?>" data-total-metrics="<?= $totalUserMetrics ?>">
                                            <div class="d-flex align-items-center mb-3 flex-wrap">
                                                <i class="fas fa-user-circle fa-2x text-primary mr-2"></i>
                                                <h4 class="mb-0 font-weight-bold"><?= htmlspecialchars($row->username) ?></h4>
                                                <span class="badge badge-light ml-3 px-3 py-1 rounded-pill"><?= $row->user_cluster_zone ?: 'N/A' ?></span>
                                                <input type="hidden" name="user_ids[]" value="<?= $row->user_id ?>">
                                                
                                                <!-- Rating counter display -->
                                                <span class="rating-counter ml-auto">
                                                    <i class="fas fa-star"></i> 
                                                    <span class="rated-count" id="rated-count-<?= $row->user_id ?>">0</span> / <?= $totalUserMetrics ?>
                                                </span>
                                            </div>

                                            <!-- MAIN METRICS TABLE -->
                                            <div class="table-responsive">
                                                <table class="review-table table table-sm">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width: 35%">Metric <span class="required-star">*</span></th>
                                                            <th style="width: 15%">Value</th>
                                                            <th style="width: 20%">Rating (1–5) <span class="required-star">*</span></th>
                                                            <th style="width: 30%">Remarks <span class="required-star">*</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($metrics as $key => $metric): ?>
                                                            <?php
                                                            $value = $row->$key ?? '';
                                                            if (strpos($key, 'budget') !== false && !empty($value)) {
                                                                $displayValue = formatIndianUnits($value);
                                                            } else {
                                                                $displayValue = $value !== '' ? $value : '—';
                                                            }
                                                            ?>
                                                            <tr data-metric-key="<?= $key ?>">
                                                                   <td><?= htmlspecialchars($metric['label']) ?></td>
                                                                <td class="value-link">
                                                                    <?php if (!empty($metric['link']) && !empty($value) && $value !== '—'): ?>
                                                                        <a href="<?= base_url($metric['link']) ?>" target="_blank"><?= $displayValue ?></a>
                                                                    <?php else: ?>
                                                                        <?= $displayValue ?>
                                                                    <?php endif; ?>
                                                                </td>
                                                                   <td>
                                                                    <div class="star-rating" data-metric="<?= $key ?>" data-user="<?= $row->user_id ?>">
                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][<?= $key ?>]" value="5" id="star5_<?= $row->user_id ?>_<?= $key ?>">
                                                                        <label for="star5_<?= $row->user_id ?>_<?= $key ?>" class="fas fa-star"></label>
                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][<?= $key ?>]" value="4" id="star4_<?= $row->user_id ?>_<?= $key ?>">
                                                                        <label for="star4_<?= $row->user_id ?>_<?= $key ?>" class="fas fa-star"></label>
                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][<?= $key ?>]" value="3" id="star3_<?= $row->user_id ?>_<?= $key ?>">
                                                                        <label for="star3_<?= $row->user_id ?>_<?= $key ?>" class="fas fa-star"></label>
                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][<?= $key ?>]" value="2" id="star2_<?= $row->user_id ?>_<?= $key ?>">
                                                                        <label for="star2_<?= $row->user_id ?>_<?= $key ?>" class="fas fa-star"></label>
                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][<?= $key ?>]" value="1" id="star1_<?= $row->user_id ?>_<?= $key ?>">
                                                                        <label for="star1_<?= $row->user_id ?>_<?= $key ?>" class="fas fa-star"></label>
                                                                    </div>
                                                                </td>
                                                                   <td>
                                                                    <textarea name="remarks[<?= $row->user_id ?>][<?= $key ?>]" class="remarks-textarea" rows="2" placeholder="Select rating first to add remarks..." disabled></textarea>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                        <!-- Additional rows for target budget, remaining budget, target achieved -->
                                                        <?php $targetBudget = 20000000; ?>
                                                        <tr data-metric-key="target_budget">
                                                            <td>Target Budget (₹) <span class="required-star">*</span></td>
                                                            <td class="value-link"><?= formatIndianUnits($targetBudget) ?></td>
                                                            <td>
                                                                <div class="star-rating" data-metric="target_budget" data-user="<?= $row->user_id ?>">
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_budget]" value="5" id="star5_<?= $row->user_id ?>_target_budget">
                                                                    <label for="star5_<?= $row->user_id ?>_target_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_budget]" value="4" id="star4_<?= $row->user_id ?>_target_budget">
                                                                    <label for="star4_<?= $row->user_id ?>_target_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_budget]" value="3" id="star3_<?= $row->user_id ?>_target_budget">
                                                                    <label for="star3_<?= $row->user_id ?>_target_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_budget]" value="2" id="star2_<?= $row->user_id ?>_target_budget">
                                                                    <label for="star2_<?= $row->user_id ?>_target_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_budget]" value="1" id="star1_<?= $row->user_id ?>_target_budget">
                                                                    <label for="star1_<?= $row->user_id ?>_target_budget" class="fas fa-star"></label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea name="remarks[<?= $row->user_id ?>][target_budget]" class="remarks-textarea" rows="2" placeholder="Select rating first to add remarks..." disabled></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr data-metric-key="remaining_budget">
                                                            <td>Remaining Budget to Target (₹) <span class="required-star">*</span></td>
                                                            <td class="value-link">
                                                                <?php
                                                                $remainingBudget = 0;
                                                                if ($targetBudget > $row->total_closure_budget_where__proposal_sent) {
                                                                    $remainingBudget = $targetBudget - $row->total_closure_budget_where__proposal_sent;
                                                                }
                                                                echo formatIndianUnits($remainingBudget);
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <div class="star-rating" data-metric="remaining_budget" data-user="<?= $row->user_id ?>">
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][remaining_budget]" value="5" id="star5_<?= $row->user_id ?>_remaining_budget">
                                                                    <label for="star5_<?= $row->user_id ?>_remaining_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][remaining_budget]" value="4" id="star4_<?= $row->user_id ?>_remaining_budget">
                                                                    <label for="star4_<?= $row->user_id ?>_remaining_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][remaining_budget]" value="3" id="star3_<?= $row->user_id ?>_remaining_budget">
                                                                    <label for="star3_<?= $row->user_id ?>_remaining_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][remaining_budget]" value="2" id="star2_<?= $row->user_id ?>_remaining_budget">
                                                                    <label for="star2_<?= $row->user_id ?>_remaining_budget" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][remaining_budget]" value="1" id="star1_<?= $row->user_id ?>_remaining_budget">
                                                                    <label for="star1_<?= $row->user_id ?>_remaining_budget" class="fas fa-star"></label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea name="remarks[<?= $row->user_id ?>][remaining_budget]" class="remarks-textarea" rows="2" placeholder="Select rating first to add remarks..." disabled></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr data-metric-key="target_achieved">
                                                            <td>Target Achieved (Yes/No) <span class="required-star">*</span></td>
                                                            <td class="value-link">
                                                                <?php
                                                                $targetAchieved = ($targetBudget <= $row->total_closure_budget_where__proposal_sent) ? 'Yes' : 'No';
                                                                echo $targetAchieved;
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <div class="star-rating" data-metric="target_achieved" data-user="<?= $row->user_id ?>">
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_achieved]" value="5" id="star5_<?= $row->user_id ?>_target_achieved">
                                                                    <label for="star5_<?= $row->user_id ?>_target_achieved" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_achieved]" value="4" id="star4_<?= $row->user_id ?>_target_achieved">
                                                                    <label for="star4_<?= $row->user_id ?>_target_achieved" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_achieved]" value="3" id="star3_<?= $row->user_id ?>_target_achieved">
                                                                    <label for="star3_<?= $row->user_id ?>_target_achieved" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_achieved]" value="2" id="star2_<?= $row->user_id ?>_target_achieved">
                                                                    <label for="star2_<?= $row->user_id ?>_target_achieved" class="fas fa-star"></label>
                                                                    <input type="radio" name="rating[<?= $row->user_id ?>][target_achieved]" value="1" id="star1_<?= $row->user_id ?>_target_achieved">
                                                                    <label for="star1_<?= $row->user_id ?>_target_achieved" class="fas fa-star"></label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea name="remarks[<?= $row->user_id ?>][target_achieved]" class="remarks-textarea" rows="2" placeholder="Select rating first to add remarks..." disabled></textarea>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- CLUSTER VISIT DETAILS SECTION WITH RATINGS & REMARKS -->
                                            <?php if (!empty($clusterDetailsByUser[$row->user_id])): ?>
                                                <div class="mt-4">
                                                    <h5 class="mb-3"><i class="fas fa-map-marker-alt text-primary mr-2"></i> Cluster Visit Details (Rate each metric)</h5>
                                                    <?php foreach ($clusterDetailsByUser[$row->user_id] as $clusterRow): 
                                                        $meeting_user_id = $row->user_id;
                                                        $cluster_id = $clusterRow->cluster_id;
                                                        $clustername = htmlspecialchars($clusterRow->clustername);
                                                    ?>
                                                        <div class="cluster-block">
                                                            <div class="cluster-header">
                                                                <i class="fas fa-building"></i> <?= $clustername ?>
                                                                <span class="badge badge-info ml-2">Cluster ID: <?= $cluster_id ?></span>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="cluster-metrics-table table table-sm">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th style="width: 35%">Metric <span class="required-star">*</span></th>
                                                                            <th style="width: 15%">Value</th>
                                                                            <th style="width: 20%">Rating (1–5) <span class="required-star">*</span></th>
                                                                            <th style="width: 30%">Remarks <span class="required-star">*</span></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($clusterMetricsTemplate as $metricKey => $metricInfo): 
                                                                            $value = $clusterRow->$metricKey ?? '';
                                                                            $displayValue = $value !== '' ? $value : '—';
                                                                            // Build link with placeholders replaced
                                                                            $link = str_replace(['{user_id}', '{cluster_id}', '{sdate}', '{edate}'], [$meeting_user_id, $cluster_id, $sdate, $edate], $metricInfo['link']);
                                                                        ?>
                                                                            <tr data-metric-key="cluster_<?= $metricKey ?>_<?= $cluster_id ?>">
                                                                                <td><?= htmlspecialchars($metricInfo['label']) ?></td>
                                                                                <td class="value-link">
                                                                                    <?php if (!empty($link) && !empty($value) && $value !== '—'): ?>
                                                                                        <a href="<?= base_url($link) ?>" target="_blank"><?= $displayValue ?></a>
                                                                                    <?php else: ?>
                                                                                        <?= $displayValue ?>
                                                                                    <?php endif; ?>
                                                                                    <!-- Hidden field to store the metric value -->
                                                                                    <input type="hidden" name="metric_value[<?= $row->user_id ?>][cluster_<?= $metricKey ?>_<?= $cluster_id ?>]" value="<?= htmlspecialchars($value) ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <div class="star-rating" data-metric="cluster_<?= $metricKey ?>_<?= $cluster_id ?>" data-user="<?= $row->user_id ?>">
                                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][cluster_<?= $metricKey ?>_<?= $cluster_id ?>]" value="5" id="star5_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>">
                                                                                        <label for="star5_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>" class="fas fa-star"></label>
                                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][cluster_<?= $metricKey ?>_<?= $cluster_id ?>]" value="4" id="star4_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>">
                                                                                        <label for="star4_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>" class="fas fa-star"></label>
                                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][cluster_<?= $metricKey ?>_<?= $cluster_id ?>]" value="3" id="star3_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>">
                                                                                        <label for="star3_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>" class="fas fa-star"></label>
                                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][cluster_<?= $metricKey ?>_<?= $cluster_id ?>]" value="2" id="star2_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>">
                                                                                        <label for="star2_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>" class="fas fa-star"></label>
                                                                                        <input type="radio" name="rating[<?= $row->user_id ?>][cluster_<?= $metricKey ?>_<?= $cluster_id ?>]" value="1" id="star1_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>">
                                                                                        <label for="star1_<?= $row->user_id ?>_cluster_<?= $metricKey ?>_<?= $cluster_id ?>" class="fas fa-star"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <textarea name="remarks[<?= $row->user_id ?>][cluster_<?= $metricKey ?>_<?= $cluster_id ?>]" class="remarks-textarea" rows="2" placeholder="Select rating first to add remarks..." disabled></textarea>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Hidden field to store the count of rated metrics for this user -->
                                            <input type="hidden" name="rating_count[<?= $row->user_id ?>]" class="rating-count-hidden" value="0">

                                            <!-- Overall message (proper feedback) for this user -->
                                            <div class="form-group mt-3">
                                                <label><i class="fas fa-comment-dots text-primary"></i> Overall Message / Feedback</label>
                                                <textarea name="message[<?= $row->user_id ?>]" class="overall-message" rows="2" placeholder="Provide overall feedback for this team member..."></textarea>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- Timer and action buttons -->
                                    <div class="d-flex justify-content-between align-items-center flex-wrap mt-4">
                                        <div class="action-buttons">
                                            <button type="submit" class="btn-gradient-primary">
                                                <i class="fas fa-save"></i> Save Ratings & Remarks
                                            </button>
                                            <button type="reset" class="btn-outline-modern" id="resetAllBtn">
                                                <i class="fas fa-undo-alt"></i> Reset All
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="modern-card mt-4">
                            <div class="card-body-modern text-center">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No sales data found.</h5>
                                <p class="small">Please adjust your filters or check back later.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row mt-2 mb-4">
                        <div class="col-md-12 text-center">
                            <div class="d-inline-block px-4 py-2 bg-white rounded-pill shadow-sm">
                                <i class="fas fa-chart-line text-primary mr-2"></i>
                                <span class="font-weight-bold text-secondary">Data-driven reviews — smart decisions start here</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="small text-muted text-center">
                                <i class="far fa-clock"></i> Last sync: today | Powered by STEM Analytics
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?>
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
<script src="<?=base_url();?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>assets/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url();?>assets/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>

<script>
$(document).ready(function() {
    // --- Persistent Session Timer with localStorage (survives page reload) ---
    var reviewId = <?= json_encode($reviewId) ?>;
    var dbSeconds = <?= $dbSessionSeconds ?>; // seconds from database
    var storageKey = 'stem_review_timer_' + reviewId;
    
    // Retrieve saved total seconds from localStorage, otherwise use database value
    var savedSeconds = localStorage.getItem(storageKey);
    var initialTotalSeconds = savedSeconds ? parseInt(savedSeconds, 10) : dbSeconds;
    
    // Set startTime based on initial total seconds
    var startTime = Date.now() - (initialTotalSeconds * 1000);
    var timerInterval;
    
    // Format time as DD:HH:MM:SS
    function formatDuration(totalSeconds) {
        var days = Math.floor(totalSeconds / 86400);
        var hours = Math.floor((totalSeconds % 86400) / 3600);
        var minutes = Math.floor((totalSeconds % 3600) / 60);
        var seconds = totalSeconds % 60;
        return days.toString().padStart(2, '0') + ':' + 
               hours.toString().padStart(2, '0') + ':' + 
               minutes.toString().padStart(2, '0') + ':' + 
               seconds.toString().padStart(2, '0');
    }
    
    function updateTimer() {
        var elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
        $('#timerDisplay').text(formatDuration(elapsedSeconds));
        $('#session_duration_seconds').val(elapsedSeconds);
        // Save current total to localStorage (for persistence across reloads)
        localStorage.setItem(storageKey, elapsedSeconds);
    }
    
    timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // initial display
    
    // Save timer on page unload (optional, but interval already saves each second)
    window.addEventListener('beforeunload', function() {
        // No need to do extra, already saving every second.
    });
    
    // 1. Initialize: all remarks textareas are disabled
    $('textarea.remarks-textarea').prop('disabled', true);
    
    // 2. When any rating radio is checked, enable its corresponding remarks textarea and mark as required
    $(document).on('change', 'input[type="radio"][name^="rating"]', function() {
        var $radio = $(this);
        var $row = $radio.closest('tr');
        var $remarksTextarea = $row.find('.remarks-textarea');
        if ($radio.is(':checked')) {
            $remarksTextarea.prop('disabled', false);
            $remarksTextarea.attr('required', true);
            $remarksTextarea.attr('placeholder', 'Add your remarks (required)');
        }
        updateRatingCountForUser($radio);
    });
    
    // Helper: update rating count and hidden field for a user
    function updateRatingCountForUser($element) {
        var $userDiv = $element.closest('.user-separator');
        if (!$userDiv.length) return;
        var userId = $userDiv.data('user-id');
        var totalMetrics = $userDiv.data('total-metrics'); // read total from data attribute
        var rated = $userDiv.find('input[type="radio"][name^="rating[' + userId + ']"]:checked').length;
        $userDiv.find('.rating-count-hidden').val(rated);
        $userDiv.find('.rated-count').text(rated);
    }
    
    // 3. On reset: clear all radios, disable all remarks, clear text, reset counter (timer keeps running)
    $('#resetAllBtn').on('click', function(e) {
        e.preventDefault();
        $('input[type="radio"]').prop('checked', false);
        $('textarea.remarks-textarea').val('').prop('disabled', true).attr('required', false).attr('placeholder', 'Select rating first to add remarks...');
        $('textarea.overall-message').val('');
        $('.user-separator').each(function() {
            var userId = $(this).data('user-id');
            $(this).find('.rating-count-hidden').val(0);
            $(this).find('.rated-count').text(0);
        });
        toastr.info('All ratings, remarks and messages have been reset.', 'Reset');
    });
    
    // 4. Form validation and final submission handling
    $('#reviewRatingForm').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        $('.user-separator').each(function() {
            let $userDiv = $(this);
            let userId = $userDiv.data('user-id');
            let totalMetrics = $userDiv.data('total-metrics');
            let ratedCount = $userDiv.find('input[type="radio"][name^="rating[' + userId + ']"]:checked').length;
            if (ratedCount !== totalMetrics) {
                isValid = false;
                errorMessages.push(`User ${$userDiv.find('h4').text().trim()} has only ${ratedCount} out of ${totalMetrics} metrics rated. Please rate all metrics.`);
                return false; // break loop
            }
            // Check each rated row: remarks must not be empty
            $userDiv.find('tr').each(function() {
                let $row = $(this);
                let $radioChecked = $row.find('input[type="radio"]:checked');
                if ($radioChecked.length) {
                    let $remark = $row.find('.remarks-textarea');
                    if (!$remark.val() || $remark.val().trim() === '') {
                        isValid = false;
                        let metricName = $row.find('td:first').text().trim();
                        errorMessages.push(`User ${$userDiv.find('h4').text().trim()} - Metric "${metricName}" has a rating but no remarks. Remarks are mandatory.`);
                        return false;
                    }
                }
            });
            if (!isValid) return false;
        });
        
        if (!isValid) {
            e.preventDefault();
            toastr.error(errorMessages.join('<br>'), 'Validation Error', { timeOut: 8000, closeButton: true, escapeHtml: false });
        } else {
            // All validations passed: clear localStorage so next load starts at 0
            localStorage.removeItem(storageKey);
            // Stop the timer interval (optional)
            clearInterval(timerInterval);
            // Form will submit normally
        }
    });
    
    // Initial rating count for any pre-filled (if editing)
    $('.user-separator').each(function() {
        let $userDiv = $(this);
        let userId = $userDiv.data('user-id');
        let rated = $userDiv.find('input[type="radio"][name^="rating[' + userId + ']"]:checked').length;
        $userDiv.find('.rating-count-hidden').val(rated);
        $userDiv.find('.rated-count').text(rated);
        // Also enable remarks if any radio pre-checked (for future edit mode)
        $userDiv.find('input[type="radio"]:checked').each(function() {
            let $row = $(this).closest('tr');
            $row.find('.remarks-textarea').prop('disabled', false).attr('required', true).attr('placeholder', 'Add your remarks (required)');
        });
    });
});
</script>

</body>
</html>