<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --info-color: #7209b7;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .ai-insight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .refresh-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            z-index: 1000;
        }
        
        .refresh-indicator .spinner {
            display: none;
        }
        
        .refresh-indicator.refreshing .spinner {
            display: block;
        }
        
        .refresh-indicator.refreshing .icon {
            display: none;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .priority-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .priority-high {
            background: #fee;
            color: #dc3545;
        }
        
        .priority-medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .priority-low {
            background: #e7f5ff;
            color: #0d6efd;
        }
        
        .ai-response {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            white-space: pre-line;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }
        
        .loading-spinner {
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
        
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .custom-toast {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 1rem;
            margin-bottom: 1rem;
            max-width: 350px;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">
                        <i class="fas fa-chart-line me-2"></i>Proposal Details Dashboard
                    </h1>
                    <p class="lead mb-0">Business Analytics AI - Real-time Proposal Analysis & Insights</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <button class="btn btn-light" onclick="exportData('json')">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <button class="btn btn-light" onclick="refreshData()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#settingsModal">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Date Range Selector -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card bg-transparent border-light">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label text-white">Start Date</label>
                                    <input type="date" class="form-control" id="startDate" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-white">End Date</label>
                                    <input type="date" class="form-control" id="endDate" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-white">Analysis Query</label>
                                    <input type="text" class="form-control" id="analysisQuery" 
                                           placeholder="e.g., Show me top 10 companies with financial analysis" 
                                           value="Show me comprehensive proposal analysis">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-light w-100" onclick="loadAnalysisData()">
                                        <i class="fas fa-search me-1"></i> Analyze
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats Summary -->
        <div class="row" id="statsSummary">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e3f2fd; color: #1976d2;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-number text-primary">0</div>
                    <div class="stat-label">Total Proposals</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e8f5e9; color: #388e3c;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number text-success">0%</div>
                    <div class="stat-label">Approval Rate</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fff3e0; color: #f57c00;">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-number text-warning">₹0</div>
                    <div class="stat-label">Total Budget</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f3e5f5; color: #7b1fa2;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number text-info">0</div>
                    <div class="stat-label">Companies</div>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row">
            <div class="col-md-12">
                <div class="ai-insight-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-robot me-2"></i>AI Business Insights
                        </h5>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-sync-alt me-1"></i> Auto-refresh: 5 min
                        </span>
                    </div>
                    <div id="aiInsights" class="ai-response">
                        Loading AI analysis...
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">Performance Metrics</h5>
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">Product Distribution</h5>
                    <canvas id="productChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="topCompanies-tab" data-bs-toggle="tab" data-bs-target="#topCompanies" type="button">
                            <i class="fas fa-building me-1"></i> Top Companies
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="topUsers-tab" data-bs-toggle="tab" data-bs-target="#topUsers" type="button">
                            <i class="fas fa-users me-1"></i> Top Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                            <i class="fas fa-boxes me-1"></i> Products
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button">
                            <i class="fas fa-chart-bar me-1"></i> Financial
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="priority-tab" data-bs-toggle="tab" data-bs-target="#priority" type="button">
                            <i class="fas fa-flag me-1"></i> Priority Alerts
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="dashboardTabsContent">
                    <!-- Top Companies Tab -->
                    <div class="tab-pane fade show active" id="topCompanies" role="tabpanel">
                        <div class="table-container mt-3">
                            <table id="companiesTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Company</th>
                                        <th>Proposals</th>
                                        <th>Total Budget</th>
                                        <th>Approved</th>
                                        <th>Score</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="companiesTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Top Users Tab -->
                    <div class="tab-pane fade" id="topUsers" role="tabpanel">
                        <div class="table-container mt-3">
                            <table id="usersTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>User</th>
                                        <th>Proposals</th>
                                        <th>Approval Rate</th>
                                        <th>Total Budget</th>
                                        <th>Performance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Products Tab -->
                    <div class="tab-pane fade" id="products" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <div class="table-container">
                                    <table id="productsTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Proposals</th>
                                                <th>Approved</th>
                                                <th>Rejected</th>
                                                <th>Approval Rate</th>
                                                <th>Avg Budget</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productsTableBody">
                                            <!-- Data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="chart-container">
                                    <h5 class="mb-3">Top Products</h5>
                                    <canvas id="topProductsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Financial Tab -->
                    <div class="tab-pane fade" id="financial" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h5 class="mb-3">Budget Distribution</h5>
                                    <canvas id="budgetChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h5 class="mb-3">Monthly Trend</h5>
                                    <canvas id="monthlyTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="table-container">
                                    <table id="financialTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Metric</th>
                                                <th>Value</th>
                                                <th>Change</th>
                                                <th>Trend</th>
                                            </tr>
                                        </thead>
                                        <tbody id="financialTableBody">
                                            <!-- Data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Priority Alerts Tab -->
                    <div class="tab-pane fade" id="priority" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div id="priorityAlerts" class="row">
                                    <!-- Priority alerts will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh Indicator -->
    <div class="refresh-indicator" id="refreshIndicator" onclick="refreshData()" title="Click to refresh now">
        <div class="icon">
            <i class="fas fa-sync-alt"></i>
        </div>
        <div class="spinner">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-cog me-2"></i>Dashboard Settings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Auto-refresh Interval</label>
                        <select class="form-select" id="refreshInterval">
                            <option value="300000">5 minutes</option>
                            <option value="600000">10 minutes</option>
                            <option value="900000">15 minutes</option>
                            <option value="1800000">30 minutes</option>
                            <option value="0">Off</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Analysis Type</label>
                        <select class="form-select" id="defaultAnalysis">
                            <option value="comprehensive">Comprehensive</option>
                            <option value="companies">Companies Only</option>
                            <option value="users">Users Only</option>
                            <option value="financial">Financial Only</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enableNotifications" checked>
                        <label class="form-check-label" for="enableNotifications">
                            Enable Desktop Notifications
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enableSounds" checked>
                        <label class="form-check-label" for="enableSounds">
                            Enable Sound Alerts
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global variables
        let refreshInterval = 300000; // 5 minutes in milliseconds
        let refreshTimer = null;
        let currentData = null;
        let charts = {};

        // Initialize on page load
        $(document).ready(function() {
            // Load initial data
            loadAnalysisData();
            
            // Initialize DataTables
            initDataTables();
            
            // Load saved settings
            loadSettings();
            
            // Start auto-refresh
            startAutoRefresh();
            
            // Show welcome notification
            showToast('Dashboard loaded successfully! Auto-refresh every 5 minutes.', 'success');
        });

        // Load analysis data
        function loadAnalysisData() {
            showLoading(true);
            
            const data = {
                message: $('#analysisQuery').val(),
                analysis_type: $('#defaultAnalysis').val() || 'comprehensive',
                start_date: $('#startDate').val(),
                end_date: $('#endDate').val()
            };
            
            $.ajax({
                url: '<?php echo site_url("BusinessAnalytics/ProposalDetails/get_proposal_analysis"); ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    showLoading(false);
                    
                    if (response.status === 'success') {
                        currentData = response.data;
                        updateDashboard(response.data);
                        showToast('Data loaded successfully!', 'success');
                    } else {
                        showToast('Error: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showLoading(false);
                    showToast('Error loading data: ' + error, 'error');
                    console.error('AJAX Error:', error);
                }
            });
        }

        // Update dashboard with new data
        function updateDashboard(data) {
            // Update summary stats
            updateSummaryStats(data.data?.summary || {});
            
            // Update AI insights
            updateAIInsights(data.content || '');
            
            // Update charts
            updateCharts(data.data?.charts || {});
            
            // Update tables
            updateTables(data.data?.tables || {});
            
            // Update priority alerts
            updatePriorityAlerts(data.data?.detailed_analysis?.priority_flags || []);
            
            // Save last update time
            $('#refreshIndicator').attr('data-last-update', new Date().toLocaleTimeString());
        }

        // Update summary statistics
        function updateSummaryStats(summary) {
            $('#statsSummary .stat-card:nth-child(1) .stat-number').text(summary.total_proposals || '0');
            $('#statsSummary .stat-card:nth-child(2) .stat-number').text((summary.approval_rate || '0') + '%');
            $('#statsSummary .stat-card:nth-child(3) .stat-number').text('₹' + formatNumber(summary.total_budget || 0));
            $('#statsSummary .stat-card:nth-child(4) .stat-number').text(summary.total_companies || '0');
        }

        // Update AI insights
        function updateAIInsights(insights) {
            $('#aiInsights').html(insights.replace(/\n/g, '<br>'));
        }

        // Update all charts
        function updateCharts(chartData) {
            // Performance Metrics Chart
            if (chartData.performance_metrics) {
                updateChart('performanceChart', chartData.performance_metrics);
            }
            
            // Product Distribution Chart
            if (chartData.product_distribution) {
                updateChart('productChart', chartData.product_distribution);
            }
            
            // Budget Distribution Chart
            if (chartData.budget_distribution) {
                updateChart('budgetChart', chartData.budget_distribution);
            }
            
            // Additional charts can be added here
        }

        // Update a specific chart
        function updateChart(canvasId, chartConfig) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            
            // Destroy existing chart if it exists
            if (charts[canvasId]) {
                charts[canvasId].destroy();
            }
            
            // Create new chart
            charts[canvasId] = new Chart(ctx, {
                type: chartConfig.type || 'bar',
                data: {
                    labels: chartConfig.labels || [],
                    datasets: chartConfig.datasets || []
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Update all tables
        function updateTables(tables) {
            // Update Companies Table
            if (tables.top_companies) {
                updateTable('companiesTableBody', tables.top_companies.rows);
            }
            
            // Update Users Table
            if (tables.top_users) {
                updateTable('usersTableBody', tables.top_users.rows);
            }
            
            // Update Products Table
            if (tables.product_performance) {
                updateTable('productsTableBody', tables.product_performance.rows);
            }
            
            // Update Financial Table
            if (tables.financial_summary) {
                updateTable('financialTableBody', tables.financial_summary.rows);
            }
        }

        // Update a specific table
        function updateTable(tbodyId, rows) {
            const tbody = $('#' + tbodyId);
            tbody.empty();
            
            if (!rows || rows.length === 0) {
                tbody.append('<tr><td colspan="10" class="text-center text-muted">No data available</td></tr>');
                return;
            }
            
            rows.forEach(row => {
                const tr = $('<tr>');
                
                // Convert row object to array if it's an object
                const rowData = Array.isArray(row) ? row : Object.values(row);
                
                rowData.forEach(cell => {
                    const td = $('<td>');
                    
                    // Handle special formatting
                    if (typeof cell === 'string') {
                        if (cell.includes('₹')) {
                            td.addClass('text-end fw-bold');
                        } else if (cell.includes('%')) {
                            td.addClass('text-end');
                            const value = parseFloat(cell);
                            if (value > 70) td.addClass('text-success');
                            else if (value < 30) td.addClass('text-danger');
                        }
                    }
                    
                    td.html(cell);
                    tr.append(td);
                });
                
                tbody.append(tr);
            });
            
            // Re-initialize DataTables
            $('#' + tbodyId.replace('Body', '')).DataTable().destroy();
            $('#' + tbodyId.replace('Body', '')).DataTable({
                pageLength: 10,
                order: [[0, 'asc']]
            });
        }

        // Update priority alerts
        function updatePriorityAlerts(flags) {
            const container = $('#priorityAlerts');
            container.empty();
            
            if (!flags || flags.length === 0) {
                container.html(`
                    <div class="col-12">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            No priority alerts at this time.
                        </div>
                    </div>
                `);
                return;
            }
            
            flags.forEach(flag => {
                const priorityClass = flag.priority?.toLowerCase() || 'info';
                const icon = getPriorityIcon(flag.priority);
                
                container.append(`
                    <div class="col-md-6 mb-3">
                        <div class="card border-${priorityClass === 'high' ? 'danger' : priorityClass === 'medium' ? 'warning' : 'info'}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            ${icon}
                                            ${flag.label}
                                        </h6>
                                        <p class="mb-0 text-muted">${flag.count} items need attention</p>
                                    </div>
                                    <span class="badge bg-${priorityClass === 'high' ? 'danger' : priorityClass === 'medium' ? 'warning' : 'info'}">
                                        ${flag.priority || 'Info'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        // Helper: Get priority icon
        function getPriorityIcon(priority) {
            switch(priority?.toLowerCase()) {
                case 'high': return '<i class="fas fa-exclamation-triangle text-danger me-1"></i>';
                case 'medium': return '<i class="fas fa-exclamation-circle text-warning me-1"></i>';
                default: return '<i class="fas fa-info-circle text-info me-1"></i>';
            }
        }

        // Refresh data function
        function refreshData() {
            const indicator = $('#refreshIndicator');
            indicator.addClass('refreshing');
            
            loadAnalysisData();
            
            // Reset indicator after 2 seconds
            setTimeout(() => {
                indicator.removeClass('refreshing');
            }, 2000);
        }

        // Start auto-refresh
        function startAutoRefresh() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
            
            if (refreshInterval > 0) {
                refreshTimer = setInterval(refreshData, refreshInterval);
                console.log('Auto-refresh started: every', refreshInterval / 60000, 'minutes');
            }
        }

        // Load settings from localStorage
        function loadSettings() {
            const settings = JSON.parse(localStorage.getItem('dashboardSettings')) || {};
            
            $('#refreshInterval').val(settings.refreshInterval || 300000);
            $('#defaultAnalysis').val(settings.defaultAnalysis || 'comprehensive');
            $('#enableNotifications').prop('checked', settings.enableNotifications !== false);
            $('#enableSounds').prop('checked', settings.enableSounds !== false);
            
            refreshInterval = parseInt($('#refreshInterval').val());
            startAutoRefresh();
        }

        // Save settings to localStorage
        function saveSettings() {
            const settings = {
                refreshInterval: parseInt($('#refreshInterval').val()),
                defaultAnalysis: $('#defaultAnalysis').val(),
                enableNotifications: $('#enableNotifications').prop('checked'),
                enableSounds: $('#enableSounds').prop('checked')
            };
            
            localStorage.setItem('dashboardSettings', JSON.stringify(settings));
            
            // Update auto-refresh
            refreshInterval = settings.refreshInterval;
            startAutoRefresh();
            
            $('#settingsModal').modal('hide');
            showToast('Settings saved successfully!', 'success');
        }

        // Export data
        function exportData(format) {
            if (!currentData) {
                showToast('No data available to export', 'warning');
                return;
            }
            
            let url = '<?php echo site_url("proposaldetails/export"); ?>/' + format;
            url += '?start_date=' + $('#startDate').val();
            url += '&end_date=' + $('#endDate').val();
            
            window.open(url, '_blank');
            showToast('Export started...', 'info');
        }

        // Initialize DataTables
        function initDataTables() {
            $('table').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        pageLength: 10,
                        lengthMenu: [5, 10, 25, 50, 100],
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Search..."
                        }
                    });
                }
            });
        }

        // Show loading overlay
        function showLoading(show) {
            $('#loadingOverlay').css('display', show ? 'flex' : 'none');
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = $(`
                <div class="custom-toast border-start border-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'}">
                    <div class="me-3">
                        <i class="fas fa-${type === 'success' ? 'check-circle text-success' : type === 'error' ? 'times-circle text-danger' : type === 'warning' ? 'exclamation-triangle text-warning' : 'info-circle text-info'}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small fw-bold">${type.toUpperCase()}</div>
                        <div class="small">${message}</div>
                    </div>
                    <button type="button" class="btn-close btn-close-sm ms-2" onclick="$(this).closest('.custom-toast').remove()"></button>
                </div>
            `);
            
            $('#toastContainer').append(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Play sound if enabled
            if ($('#enableSounds').prop('checked')) {
                playNotificationSound(type);
            }
        }

        // Play notification sound
        function playNotificationSound(type) {
            // You can add different sounds for different notification types
            const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-software-interface-start-2574.mp3');
            audio.volume = 0.3;
            audio.play().catch(e => console.log('Audio play failed:', e));
        }

        // Format number with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Handle browser visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Tab became visible, refresh data
                refreshData();
            }
        });

        // Handle beforeunload
        window.addEventListener('beforeunload', function() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
        });
    </script>
</body>
</html>