<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Sales Analytics</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --primary-color: #10a37f;
            --secondary-color: #5436da;
            --success-color: #66bb6a;
            --warning-color: #ffa726;
            --danger-color: #ff6b6b;
            --light-bg: #f8f9fa;
            --dark-bg: #2a2a2a;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            border-bottom: 2px solid var(--primary-color);
            font-weight: 600;
            color: var(--dark-bg);
        }
        
        .stat-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .stat-card.danger {
            border-left-color: var(--danger-color);
        }
        
        .stat-card.success {
            border-left-color: var(--success-color);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-number.warning {
            color: var(--warning-color);
        }
        
        .stat-number.danger {
            color: var(--danger-color);
        }
        
        .stat-number.success {
            color: var(--success-color);
        }
        
        .refresh-btn {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .refresh-btn:hover {
            background-color: #0d8a6a;
            border-color: #0d8a6a;
        }
        
        .tab-content {
            background-color: white;
            border-radius: 0 0 10px 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .nav-tabs .nav-link {
            color: #666;
            font-weight: 500;
            border: none;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background-color: transparent;
        }
        
        .analysis-text {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 5px;
            white-space: pre-wrap;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255,255,255,0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .dataTables_wrapper {
            padding: 0;
        }
        
        .badge-performance {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .badge-excellent {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-good {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-average {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-poor {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .company-name {
            font-weight: 600;
            color: #333;
        }
        
        .remarks-preview {
            font-size: 0.85rem;
            color: #666;
            font-style: italic;
        }
        
        .comparison-positive {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .comparison-negative {
            color: var(--danger-color);
            font-weight: 600;
        }
        
        .auto-refresh-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            font-size: 0.9rem;
            z-index: 1000;
            display: none;
        }
        
        @media (max-width: 768px) {
            .stat-number {
                font-size: 1.5rem;
            }
            
            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="spinner"></div>
        <p class="mt-3">Loading Analysis Data...</p>
    </div>
    
    <!-- Auto Refresh Indicator -->
    <div id="autoRefreshIndicator" class="auto-refresh-indicator">
        <i class="fas fa-sync-alt me-2"></i>
        <span id="refreshTimer">Auto-refresh in 5:00</span>
    </div>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chart-line me-2"></i>
                Sales Analytics
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-phone me-1"></i> RP Leads Calling
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-dashboard me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i> <?php echo $username; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('login/logout'); ?>">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <!-- Header with Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fas fa-phone-volume me-2"></i>
                                    Line Manager Calling on RP Leads Analysis
                                </h5>
                                <small class="text-muted">Real-time analysis of calling performance and pipeline health</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button id="exportBtn" class="btn btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                                <button id="refreshBtn" class="btn btn-primary refresh-btn">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh Now
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <input type="date" id="startDate" class="form-control" 
                                       value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <input type="date" id="endDate" class="form-control" 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">User/Team Analysis</label>
                                <select id="userSelect" class="form-select">
                                    <option value="">All Users (Team Analysis)</option>
                                    <!-- Users will be populated dynamically -->
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Auto Refresh</label>
                                <select id="refreshInterval" class="form-select">
                                    <option value="0">Off</option>
                                    <option value="300" selected>Every 5 Minutes</option>
                                    <option value="600">Every 10 Minutes</option>
                                    <option value="1800">Every 30 Minutes</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="includeBDCM" checked>
                                    <label class="form-check-label" for="includeBDCM">Include BDCM Data</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="showCharts" checked>
                                    <label class="form-check-label" for="showCharts">Show Charts</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="showDetails" checked>
                                    <label class="form-check-label" for="showDetails">Show Details</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div id="summaryStats" class="row mb-4">
            <!-- Stats will be populated here -->
        </div>
        
        <!-- AI Analysis Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-robot me-2"></i> AI Analysis</span>
                        <small class="text-muted" id="lastUpdated">Last updated: --</small>
                    </div>
                    <div class="card-body">
                        <div id="aiAnalysis" class="analysis-text">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                <p>Click "Refresh Now" to load analysis data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs for Detailed Analysis -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="analysisTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="performance-tab" data-bs-toggle="tab" 
                                        data-bs-target="#performance" type="button">
                                    <i class="fas fa-tachometer-alt me-1"></i> Performance
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="charts-tab" data-bs-toggle="tab" 
                                        data-bs-target="#charts" type="button">
                                    <i class="fas fa-chart-bar me-1"></i> Charts
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="companies-tab" data-bs-toggle="tab" 
                                        data-bs-target="#companies" type="button">
                                    <i class="fas fa-building me-1"></i> Companies
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="remarks-tab" data-bs-toggle="tab" 
                                        data-bs-target="#remarks" type="button">
                                    <i class="fas fa-comment-alt me-1"></i> Remarks
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pipeline-tab" data-bs-toggle="tab" 
                                        data-bs-target="#pipeline" type="button">
                                    <i class="fas fa-funnel-dollar me-1"></i> Pipeline
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="analysisTabContent">
                        <!-- Performance Tab -->
                        <div class="tab-pane fade show active" id="performance" role="tabpanel">
                            <div class="table-responsive">
                                <table id="performanceTable" class="table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Username</th>
                                            <th>Total Calls</th>
                                            <th>Completed</th>
                                            <th>Pending</th>
                                            <th>Completion Rate</th>
                                            <th>Efficiency</th>
                                            <th>Overall</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Charts Tab -->
                        <div class="tab-pane fade" id="charts" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Performance Comparison</div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="performanceChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Call Performance</div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="callChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Client Portfolio</div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="clientChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Documentation Quality</div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="documentationChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Companies Tab -->
                        <div class="tab-pane fade" id="companies" role="tabpanel">
                            <ul class="nav nav-tabs mb-3" id="companyTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="positive-companies-tab" data-bs-toggle="tab" 
                                            data-bs-target="#positive-companies">Positive Companies</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="negative-companies-tab" data-bs-toggle="tab" 
                                            data-bs-target="#negative-companies">Negative Companies</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="work-companies-tab" data-bs-toggle="tab" 
                                            data-bs-target="#work-companies">To Work On</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="overall-companies-tab" data-bs-toggle="tab" 
                                            data-bs-target="#overall-companies">Overall Companies</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="positive-companies">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Positive Calls</th>
                                                <th>Total Calls</th>
                                                <th>Positive Ratio</th>
                                                <th>Last Call Result</th>
                                            </tr>
                                        </thead>
                                        <tbody id="positiveCompaniesBody">
                                            <!-- Data will be populated -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="negative-companies">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Negative Calls</th>
                                                <th>Total Calls</th>
                                                <th>Negative Ratio</th>
                                                <th>Last Call Result</th>
                                            </tr>
                                        </thead>
                                        <tbody id="negativeCompaniesBody">
                                            <!-- Data will be populated -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="work-companies">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Pending Calls</th>
                                                <th>Total Calls</th>
                                                <th>Work Ratio</th>
                                                <th>Last Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="workCompaniesBody">
                                            <!-- Data will be populated -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="overall-companies">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Total Calls</th>
                                                <th>Engagement Score</th>
                                                <th>Completion Rate</th>
                                                <th>Last Call</th>
                                            </tr>
                                        </thead>
                                        <tbody id="overallCompaniesBody">
                                            <!-- Data will be populated -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Remarks Tab -->
                        <div class="tab-pane fade" id="remarks" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Remarks Analysis</div>
                                        <div class="card-body">
                                            <table class="table">
                                                <tbody id="remarksAnalysisBody">
                                                    <!-- Data will be populated -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Companies with Special Remarks</div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Company</th>
                                                            <th>Special Remarks</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="specialRemarksBody">
                                                        <!-- Data will be populated -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pipeline Tab -->
                        <div class="tab-pane fade" id="pipeline" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Closing Timeline Distribution</div>
                                        <div class="card-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Timeline</th>
                                                        <th>Count</th>
                                                        <th>Percentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="timelineDistributionBody">
                                                    <!-- Data will be populated -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Companies with Closing Timelines</div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Company</th>
                                                            <th>Timeline</th>
                                                            <th>Status</th>
                                                            <th>Special Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="pipelineCompaniesBody">
                                                        <!-- Data will be populated -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Global variables
        let refreshInterval = 300; // 5 minutes in seconds
        let refreshTimer = null;
        let timeLeft = refreshInterval;
        let currentChartInstances = {};
        let performanceTable = null;
        
        // Initialize when page loads
        $(document).ready(function() {
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#performanceTable')) {
                performanceTable.destroy();
            }
            performanceTable = $('#performanceTable').DataTable({
                pageLength: 10,
                order: [[0, 'asc']],
                responsive: true,
                language: {
                    search: "Search users:"
                }
            });
            
            // Load available users
            loadAvailableUsers();
            
            // Load initial data
            loadAnalysisData();
            
            // Set up event listeners
            $('#refreshBtn').click(loadAnalysisData);
            $('#exportBtn').click(exportAnalysis);
            $('#refreshInterval').change(function() {
                refreshInterval = parseInt($(this).val());
                setupAutoRefresh();
            });
            $('#userSelect').change(loadAnalysisData);
            $('#startDate, #endDate').change(loadAnalysisData);
            
            // Set up auto-refresh
            setupAutoRefresh();
        });
        
        // Load available users for dropdown
        function loadAvailableUsers() {
            $.ajax({
                url: '<?php echo base_url("BusinessAnalytics/LineManagerCallingonRPLeads/get_available_users"); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.users) {
                        const userSelect = $('#userSelect');
                        userSelect.empty();
                        userSelect.append('<option value="">All Users (Team Analysis)</option>');
                        
                        response.users.forEach(user => {
                            userSelect.append(
                                `<option value="${user.id}">${user.full_name || user.username} (${user.username})</option>`
                            );
                        });
                    }
                },
                error: function() {
                    console.error('Failed to load users');
                }
            });
        }
        
        // Load analysis data
        function loadAnalysisData() {
            showLoading(true);
            
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const userId = $('#userSelect').val();
            const analysisType = userId ? 'user' : 'team';
            
            $.ajax({
                url: '<?php echo base_url("BusinessAnalytics/LineManagerCallingonRPLeads/get_analysis_data"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    specific_user_id: userId,
                    analysis_type: analysisType,
                    message: 'Show detailed calling analysis'
                },
                success: function(response) {
                    showLoading(false);
                    
                    if (response.success) {
                        updateLastUpdated(response.timestamp);
                        displayAnalysisData(response);
                    } else {
                        showError('Failed to load analysis: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    showLoading(false);
                    showError('Network error: ' + error);
                }
            });
        }
        
        // Display analysis data
        function displayAnalysisData(data) {
            // Update AI Analysis
            if (data.content) {
                $('#aiAnalysis').html(formatAIAnalysis(data.content));
            }
            
            // Update summary stats
            if (data.data && data.data.summaryData) {
                displaySummaryStats(data.data.summaryData);
            }
            
            // Update performance table
            if (data.data && data.data.tableData) {
                updatePerformanceTable(data.data.tableData);
            }
            
            // Update charts
            if ($('#showCharts').is(':checked') && data.data && data.data.chartData) {
                updateCharts(data.data.chartData);
            }
            
            // Update company tables
            if (data.data && data.data.companyTables) {
                updateCompanyTables(data.data.companyTables);
            }
            
            // Update remarks tables
            if (data.data && data.data.remarksAnalysisTables) {
                updateRemarksTables(data.data.remarksAnalysisTables);
            }
            
            // Update pipeline tables
            if (data.data && data.data.pipelineTables) {
                updatePipelineTables(data.data.pipelineTables);
            }
            
            // Show/hide details based on checkbox
            const showDetails = $('#showDetails').is(':checked');
            $('.tab-content .table-responsive').toggle(showDetails);
            $('.chart-container').toggle($('#showCharts').is(':checked'));
        }
        
        // Format AI Analysis text
        function formatAIAnalysis(htmlContent) {
            // Convert HTML to formatted text
            let text = htmlContent.replace(/<[^>]*>/g, '');
            text = text.replace(/\n/g, '<br>');
            
            // Add section headers styling
            text = text.replace(/(\d+\.\s+[A-Z][^:]+:)/g, '<strong>$1</strong>');
            text = text.replace(/(\d+\.\s+[A-Z\s]+)/g, '<strong>$1</strong>');
            
            return `<div class="analysis-content">${text}</div>`;
        }
        
        // Display summary statistics
        function displaySummaryStats(summaryData) {
            const statsHtml = `
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Calls</h6>
                            <div class="stat-number">${summaryData.total_calls || 0}</div>
                            <p class="card-text">
                                <small class="text-muted">
                                    ${summaryData.completed_calls || 0} completed<br>
                                    ${summaryData.pending_calls || 0} pending
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card success">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Completion Rate</h6>
                            <div class="stat-number success">${summaryData.completion_rate || '0%'}</div>
                            <p class="card-text">
                                <small class="text-muted">
                                    Efficiency: ${summaryData.efficiency_score || '0/100'}<br>
                                    Quality: ${summaryData.quality_score || '0/100'}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Client Portfolio</h6>
                            <div class="stat-number">${summaryData.total_clients || 0}</div>
                            <p class="card-text">
                                <small class="text-muted">
                                    ${summaryData.total_companies || 0} companies<br>
                                    ${summaryData.special_remarks_coverage || '0%'} special remarks
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card warning">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Pipeline Health</h6>
                            <div class="stat-number warning">${summaryData.closing_timeline_coverage || '0%'}</div>
                            <p class="card-text">
                                <small class="text-muted">
                                    ${summaryData.total_closing_timelines || 0} timelines set<br>
                                    ${summaryData.top_performers_count || 0} top performers
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            `;
            
            $('#summaryStats').html(statsHtml);
        }
        
        // Update performance table
        function updatePerformanceTable(tableData) {
            if (performanceTable) {
                performanceTable.clear();
                
                tableData.rows.forEach(row => {
                    const formattedRow = [
                        `<span class="badge badge-performance ${getPerformanceClass(row[6])}">${row[0]}</span>`,
                        row[1],
                        row[2],
                        row[3],
                        row[4],
                        formatPercentage(row[5]),
                        formatScore(row[6]),
                        formatPercentage(row[7]),
                        `<button class="btn btn-sm btn-outline-primary" onclick="viewUserDetails(${row[8]})">
                            <i class="fas fa-eye"></i>
                        </button>`
                    ];
                    
                    performanceTable.row.add(formattedRow);
                });
                
                performanceTable.draw();
            }
        }
        
        // Update charts
        function updateCharts(chartData) {
            // Destroy existing charts
            Object.values(currentChartInstances).forEach(chart => {
                if (chart) chart.destroy();
            });
            currentChartInstances = {};
            
            // Create new charts
            if (chartData.performance_comparison) {
                createChart('performanceChart', chartData.performance_comparison);
            }
            if (chartData.call_success_rate) {
                createChart('callChart', chartData.call_success_rate);
            }
            if (chartData.client_distribution) {
                createChart('clientChart', chartData.client_distribution);
            }
            if (chartData.documentation_quality) {
                createChart('documentationChart', chartData.documentation_quality);
            }
        }
        
        // Create a Chart.js chart
        function createChart(canvasId, chartConfig) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            currentChartInstances[canvasId] = new Chart(ctx, {
                type: chartConfig.type,
                data: {
                    labels: chartConfig.labels,
                    datasets: chartConfig.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: chartConfig.type === 'bar' ? {
                        y: {
                            beginAtZero: true
                        }
                    } : {}
                }
            });
        }
        
        // Update company tables
        function updateCompanyTables(companyTables) {
            // Positive companies
            if (companyTables.positive_companies && companyTables.positive_companies.rows) {
                updateCompanyTable('positiveCompaniesBody', companyTables.positive_companies.rows);
            }
            
            // Negative companies
            if (companyTables.negative_companies && companyTables.negative_companies.rows) {
                updateCompanyTable('negativeCompaniesBody', companyTables.negative_companies.rows);
            }
            
            // To work companies
            if (companyTables.to_work_companies && companyTables.to_work_companies.rows) {
                updateCompanyTable('workCompaniesBody', companyTables.to_work_companies.rows);
            }
            
            // Overall companies
            if (companyTables.overall_companies && companyTables.overall_companies.rows) {
                updateCompanyTable('overallCompaniesBody', companyTables.overall_companies.rows);
            }
        }
        
        // Update a company table
        function updateCompanyTable(tbodyId, rows) {
            const tbody = $('#' + tbodyId);
            tbody.empty();
            
            rows.forEach(row => {
                const tr = $('<tr>');
                row.forEach((cell, index) => {
                    const td = $('<td>');
                    
                    // Format based on column index
                    if (index === 3 || index === 4) { // Ratio columns
                        if (typeof cell === 'string' && cell.includes('%')) {
                            const percentage = parseFloat(cell);
                            const badgeClass = percentage > 70 ? 'success' : 
                                              percentage > 40 ? 'warning' : 'danger';
                            td.html(`<span class="badge bg-${badgeClass}">${cell}</span>`);
                        } else {
                            td.text(cell);
                        }
                    } else if (index === 1 || index === 2) { // Count columns
                        td.html(`<strong>${cell}</strong>`);
                    } else if (index === 0) { // Company name
                        td.html(`<span class="company-name">${cell}</span>`);
                    } else {
                        td.text(cell);
                    }
                    
                    tr.append(td);
                });
                
                tbody.append(tr);
            });
        }
        
        // Update remarks tables
        function updateRemarksTables(remarksTables) {
            // Remarks analysis
            if (remarksTables.remarks_summary && remarksTables.remarks_summary.rows) {
                updateRemarksTable('remarksAnalysisBody', remarksTables.remarks_summary.rows);
            }
            
            // Special remarks companies
            if (remarksTables.special_remarks_companies && remarksTables.special_remarks_companies.rows) {
                updateRemarksTable('specialRemarksBody', remarksTables.special_remarks_companies.rows);
            }
        }
        
        // Update remarks table
        function updateRemarksTable(tbodyId, rows) {
            const tbody = $('#' + tbodyId);
            tbody.empty();
            
            rows.forEach(row => {
                const tr = $('<tr>');
                row.forEach(cell => {
                    const td = $('<td>');
                    
                    if (typeof cell === 'string' && cell.includes('%')) {
                        const percentage = parseFloat(cell);
                        let badgeClass = 'secondary';
                        if (percentage > 70) badgeClass = 'success';
                        else if (percentage > 40) badgeClass = 'warning';
                        else if (percentage > 0) badgeClass = 'danger';
                        
                        td.html(`<span class="badge bg-${badgeClass}">${cell}</span>`);
                    } else {
                        td.text(cell);
                    }
                    
                    tr.append(td);
                });
                
                tbody.append(tr);
            });
        }
        
        // Update pipeline tables
        function updatePipelineTables(pipelineTables) {
            // Timeline distribution
            if (pipelineTables.closing_timeline_summary && pipelineTables.closing_timeline_summary.rows) {
                updatePipelineTable('timelineDistributionBody', pipelineTables.closing_timeline_summary.rows);
            }
            
            // Pipeline companies
            if (pipelineTables.pipeline_companies && pipelineTables.pipeline_companies.rows) {
                updatePipelineTable('pipelineCompaniesBody', pipelineTables.pipeline_companies.rows);
            }
        }
        
        // Update pipeline table
        function updatePipelineTable(tbodyId, rows) {
            const tbody = $('#' + tbodyId);
            tbody.empty();
            
            rows.forEach(row => {
                const tr = $('<tr>');
                row.forEach((cell, index) => {
                    const td = $('<td>');
                    
                    if (index === 2 && typeof cell === 'string' && cell.includes('%')) {
                        const percentage = parseFloat(cell);
                        const badgeClass = percentage > 50 ? 'success' : 
                                          percentage > 20 ? 'warning' : 'danger';
                        td.html(`<span class="badge bg-${badgeClass}">${cell}</span>`);
                    } else if (index === 1) { // Count column
                        td.html(`<strong>${cell}</strong>`);
                    } else {
                        td.text(cell);
                    }
                    
                    tr.append(td);
                });
                
                tbody.append(tr);
            });
        }
        
        // Export analysis
        function exportAnalysis() {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const userId = $('#userSelect').val();
            const format = confirm('Export as Excel? (OK for Excel, Cancel for CSV)') ? 'excel' : 'csv';
            
            let url = `<?php echo base_url('BusinessAnalytics/LineManagerCallingonRPLeads/export_analysis'); ?>?format=${format}&start_date=${startDate}&end_date=${endDate}`;
            
            if (userId) {
                url += `&specific_user_id=${userId}&analysis_type=user`;
            }
            
            window.open(url, '_blank');
        }
        
        // View user details
        function viewUserDetails(userId) {
            // Set the user in dropdown and reload
            $('#userSelect').val(userId).trigger('change');
            // Switch to AI Analysis tab
            $('button[data-bs-target="#performance"]').tab('show');
        }
        
        // Setup auto-refresh
        function setupAutoRefresh() {
            // Clear existing timer
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
            
            // Hide indicator if refresh is off
            if (refreshInterval <= 0) {
                $('#autoRefreshIndicator').hide();
                return;
            }
            
            // Show indicator
            $('#autoRefreshIndicator').show();
            
            // Reset timer
            timeLeft = refreshInterval;
            updateRefreshTimer();
            
            // Start countdown
            refreshTimer = setInterval(function() {
                timeLeft--;
                updateRefreshTimer();
                
                if (timeLeft <= 0) {
                    // Time to refresh
                    loadAnalysisData();
                    timeLeft = refreshInterval;
                }
            }, 1000);
        }
        
        // Update refresh timer display
        function updateRefreshTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            $('#refreshTimer').text(`Auto-refresh in ${minutes}:${seconds.toString().padStart(2, '0')}`);
        }
        
        // Format percentage
        function formatPercentage(percentageStr) {
            if (!percentageStr) return '0%';
            const percentage = parseFloat(percentageStr);
            
            let badgeClass = 'secondary';
            if (percentage > 85) badgeClass = 'success';
            else if (percentage > 70) badgeClass = 'primary';
            else if (percentage > 50) badgeClass = 'warning';
            else badgeClass = 'danger';
            
            return `<span class="badge bg-${badgeClass}">${percentageStr}</span>`;
        }
        
        // Format score
        function formatScore(scoreStr) {
            if (!scoreStr) return '0/100';
            const score = parseFloat(scoreStr);
            
            let badgeClass = 'secondary';
            if (score > 85) badgeClass = 'success';
            else if (score > 70) badgeClass = 'primary';
            else if (score > 50) badgeClass = 'warning';
            else badgeClass = 'danger';
            
            return `<span class="badge bg-${badgeClass}">${scoreStr}</span>`;
        }
        
        // Get performance class
        function getPerformanceClass(scoreStr) {
            const score = parseFloat(scoreStr);
            if (score > 85) return 'badge-excellent';
            if (score > 70) return 'badge-good';
            if (score > 50) return 'badge-average';
            return 'badge-poor';
        }
        
        // Show loading overlay
        function showLoading(show) {
            if (show) {
                $('#loadingOverlay').fadeIn();
            } else {
                $('#loadingOverlay').fadeOut();
            }
        }
        
        // Show error message
        function showError(message) {
            // Create toast notification
            const toastHtml = `
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                    <div class="toast show" role="alert">
                        <div class="toast-header bg-danger text-white">
                            <strong class="me-auto">Error</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            ${message}
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(toastHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                $('.toast').remove();
            }, 5000);
        }
        
        // Update last updated timestamp
        function updateLastUpdated(timestamp) {
            $('#lastUpdated').text('Last updated: ' + timestamp);
        }
        
        // Handle window visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && refreshInterval > 0) {
                // Page became visible, refresh if needed
                if (timeLeft <= 30) { // Refresh if less than 30 seconds left
                    loadAnalysisData();
                }
            }
        });
    </script>
</body>
</html>