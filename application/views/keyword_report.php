<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report: <?php echo $query_text; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; padding: 20px 0; }
        .report-header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .report-section { margin-bottom: 30px; }
        .section-header { border-left: 5px solid #007bff; padding-left: 15px; margin-bottom: 20px; }
        .card { margin-bottom: 20px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .insight-card { border-left: 4px solid #28a745; }
        .highlight { background: #fff3cd; padding: 2px 5px; border-radius: 3px; }
        .stat-badge { font-size: 0.9em; margin-right: 5px; }
        .back-btn { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <div class="back-btn">
            <a href="<?php echo site_url('ai_query'); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Report Header -->
        <div class="report-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1><i class="fas fa-file-alt text-primary"></i> <?php echo htmlspecialchars($report['query']); ?></h1>
                    <p class="text-muted">
                        <i class="fas fa-clock"></i> Generated: <?php echo $report['timestamp']; ?> | 
                        <i class="fas fa-cogs"></i> <?php echo $report['total_functions']; ?> functions executed
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary">AI Generated</span>
                    <span class="badge bg-success">Interactive</span>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="row mt-4">
                <?php 
                $total_records = 0;
                foreach ($report['sections'] as $section) {
                    $total_records += $section['data_count'];
                }
                ?>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-layer-group"></i> Sections</h5>
                            <h2 class="mb-0"><?php echo count($report['sections']); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-database"></i> Total Records</h5>
                            <h2 class="mb-0"><?php echo $total_records; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-chart-line"></i> Insights</h5>
                            <h2 class="mb-0">AI</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-table"></i> Tables</h5>
                            <h2 class="mb-0"><?php echo count($report['sections']); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Sections -->
        <?php foreach ($report['sections'] as $index => $section): ?>
            <div class="report-section">
                <div class="section-header">
                    <h3>
                        <span class="badge bg-primary"><?php echo $index + 1; ?></span>
                        <?php echo htmlspecialchars($section['title']); ?>
                        <span class="badge bg-secondary stat-badge">
                            <i class="fas fa-list"></i> <?php echo $section['data_count']; ?> records
                        </span>
                        <?php if(isset($section['keyword'])): ?>
                            <span class="badge bg-info stat-badge">
                                <i class="fas fa-tag"></i> <?php echo $section['keyword']; ?>
                            </span>
                        <?php endif; ?>
                    </h3>
                    <small class="text-muted">Function: <?php echo $section['function']; ?></small>
                </div>
                
                <!-- Section Content -->
                <?php echo $section['content']; ?>
            </div>
        <?php endforeach; ?>
        
        <!-- Executive Summary -->
        <div class="card insight-card">
            <div class="card-header bg-light">
                <h5><i class="fas fa-chart-pie"></i> Executive Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Key Highlights:</h6>
                        <ul>
                            <?php foreach ($report['sections'] as $section): ?>
                                <?php if($section['data_count'] > 0): ?>
                                    <li>
                                        <strong><?php echo $section['title']; ?>:</strong> 
                                        <?php echo $section['data_count']; ?> records found
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Recommendations:</h6>
                        <ul>
                            <li>Review data for accuracy and completeness</li>
                            <li>Schedule follow-up actions based on insights</li>
                            <li>Share relevant findings with team members</li>
                            <li>Consider exporting data for further analysis</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Quick Actions:</h6>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <a href="<?php echo site_url('ai_query'); ?>" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-redo"></i> New Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- New Report Button -->
        <div class="text-center mt-5 mb-5">
            <a href="<?php echo site_url('ai_query'); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-plus-circle"></i> Generate New Report
            </a>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Export to PDF (mock function)
        function exportToPDF() {
            alert('PDF export would be implemented with a library like jsPDF');
            // In real implementation, use jsPDF or send to server
        }
        
        // Print report
        function printReport() {
            window.print();
        }
        
        // Toggle section visibility
        function toggleSection(sectionId) {
            $('#' + sectionId).toggle();
        }
    </script>
</body>
</html>