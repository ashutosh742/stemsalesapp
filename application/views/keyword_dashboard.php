<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Keyword Report Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .card { margin-bottom: 20px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .keyword-chip { display: inline-block; padding: 8px 15px; margin: 5px; background: #e9ecef; border-radius: 20px; cursor: pointer; transition: all 0.3s; }
        .keyword-chip:hover { background: #007bff; color: white; transform: translateY(-2px); }
        .keyword-chip.selected { background: #007bff; color: white; }
        .category-card { height: 100%; border-left: 4px solid #007bff; }
        .recent-query { border-left: 3px solid #28a745; padding-left: 10px; margin-bottom: 10px; }
        .keyword-count { font-size: 0.8em; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-robot text-primary"></i> AI Keyword Report Generator</h1>
                    <div>
                        <span class="badge bg-primary">100+ Keywords</span>
                        <span class="badge bg-success ms-2">Auto Functions</span>
                    </div>
                </div>
                <p class="text-muted">Select keywords to generate instant reports with cards, tables, and AI insights</p>
            </div>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Left: Keyword Selection -->
            <div class="col-lg-8">
                <!-- Single Keyword Form -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-keyboard"></i> Quick Keyword Search</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo site_url('ai_query/process_keyword'); ?>" id="singleKeywordForm">
                            <div class="mb-3">
                                <label class="form-label">Select or type a keyword:</label>
                                <input type="text" class="form-control" name="keyword" id="keywordInput" 
                                       placeholder="Type or select from below..." list="keywordSuggestions">
                                <datalist id="keywordSuggestions">
                                    <?php foreach($keywords as $keyword): ?>
                                        <option value="<?php echo $keyword; ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Or enter custom query:</label>
                                <textarea class="form-control" name="custom_query" rows="2" 
                                          placeholder="e.g., Show me users who logged in today"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-play"></i> Generate Report
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Multiple Keywords Form -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-layer-group"></i> Multiple Keywords Report</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo site_url('ai_query/execute_multiple'); ?>" id="multipleKeywordForm">
                            <div class="mb-3">
                                <label class="form-label">Select multiple keywords:</label>
                                <select class="form-control select2-multiple" name="keywords[]" multiple="multiple" style="width: 100%;">
                                    <?php foreach($keywords as $keyword): ?>
                                        <option value="<?php echo $keyword; ?>"><?php echo $keyword; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-bolt"></i> Generate Combined Report
                            </button>
                            <small class="text-muted ms-2">Get insights from multiple data sources</small>
                        </form>
                    </div>
                </div>
                
                <!-- Keyword Categories -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-folder"></i> Keyword Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach($keyword_categories as $category => $category_keywords): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card category-card">
                                        <div class="card-body">
                                            <h6><?php echo $category; ?> 
                                                <span class="keyword-count">(<?php echo count($category_keywords); ?>)</span>
                                            </h6>
                                            <div class="mt-2">
                                                <?php foreach($category_keywords as $keyword): ?>
                                                    <span class="keyword-chip" onclick="selectKeyword('<?php echo $keyword; ?>')">
                                                        <?php echo $keyword; ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Recent Queries & Info -->
            <div class="col-lg-4">
                <!-- Recent Queries -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Recent Reports</h5>
                            <a href="<?php echo site_url('ai_query/clear_recent'); ?>" class="btn btn-sm btn-outline-danger">
                                Clear All
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(empty($recent_queries)): ?>
                            <p class="text-muted">No recent reports</p>
                        <?php else: ?>
                            <?php foreach($recent_queries as $recent): ?>
                                <div class="recent-query">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted"><?php echo $recent['timestamp']; ?></small>
                                        <span class="badge bg-success"><?php echo $recent['record_count']; ?> records</span>
                                    </div>
                                    <p class="mb-1" title="<?php echo htmlspecialchars($recent['query']); ?>">
                                        <strong><?php echo htmlspecialchars(substr($recent['query'], 0, 40)); ?></strong>
                                        <?php if(strlen($recent['query']) > 40) echo '...'; ?>
                                    </p>
                                    <?php if(isset($recent['functions'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-cogs"></i> 
                                            <?php echo count($recent['functions']); ?> functions executed
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-primary"><?php echo count($keywords); ?></h3>
                                    <small>Total Keywords</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-success"><?php echo count($keyword_categories); ?></h3>
                                    <small>Categories</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <h3 class="text-warning">20+</h3>
                                    <small>Report Functions</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <h3 class="text-info">AI</h3>
                                    <small>Insights</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- How to Use -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-question-circle"></i> How to Use</h5>
                    </div>
                    <div class="card-body">
                        <ol class="small">
                            <li>Select a keyword from categories</li>
                            <li>Or type your own keyword</li>
                            <li>Click "Generate Report"</li>
                            <li>View cards, tables, and AI insights</li>
                            <li>Select multiple keywords for combined reports</li>
                        </ol>
                        <div class="alert alert-info small">
                            <i class="fas fa-lightbulb"></i> 
                            <strong>Tip:</strong> Try "users", "meetings", "tasks" for quick reports
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-multiple').select2({
                placeholder: "Select keywords...",
                allowClear: true
            });
            
            // Auto-focus on keyword input
            $('#keywordInput').focus();
            
            // Initialize keyword chips
            initKeywordChips();
        });
        
        // Select keyword and put in input
        function selectKeyword(keyword) {
            $('#keywordInput').val(keyword).focus();
            
            // Highlight selected chip
            $('.keyword-chip').removeClass('selected');
            event.target.classList.add('selected');
        }
        
        // Initialize keyword chips
        function initKeywordChips() {
            $('.keyword-chip').click(function() {
                $('.keyword-chip').removeClass('selected');
                $(this).addClass('selected');
                $('#keywordInput').val($(this).text().trim());
            });
        }
        
        // Handle form submission
        $('#singleKeywordForm').submit(function(e) {
            const keyword = $('#keywordInput').val().trim();
            if (!keyword) {
                e.preventDefault();
                alert('Please select or enter a keyword');
                return false;
            }
        });
    </script>
</body>
</html>