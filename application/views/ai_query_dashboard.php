<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Database Query - Sales CRM System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .card { margin-bottom: 20px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .keyword-badge { cursor: pointer; margin: 2px; }
        .keyword-badge:hover { opacity: 0.8; }
        .query-example { cursor: pointer; padding: 8px; border-radius: 5px; margin: 5px 0; }
        .query-example:hover { background: #f8f9fa; }
        .table-preview { font-size: 0.85em; }
        .category-card { height: 100%; }
        .category-icon { font-size: 2em; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-robot text-primary"></i> Sales CRM AI Assistant</h1>
                    <div class="text-end">
                        <span class="badge bg-primary"><?php echo $total_tables; ?> Tables</span>
                        <span class="badge bg-success ms-2">Connected</span>
                    </div>
                </div>
                <p class="text-muted">Query your Sales CRM database using natural language or predefined queries</p>
            </div>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Main Query Section -->
            <div class="col-lg-8">
                <!-- Natural Language Query -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-comment-dots"></i> Ask About Your Data</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo site_url('ai_query/process_query'); ?>">
                            <div class="mb-3">
                                <label class="form-label">Ask in plain English:</label>
                                <textarea class="form-control" name="query" rows="3" 
                                          placeholder="Examples:
- Show me all active users
- List today's meetings
- Count pending leave requests
- Find companies in New York
- Show sales proposals from last month
- Get recent reviews and ratings"
                                          required></textarea>
                            </div>
                            
                            <!-- Common Keywords -->
                            <div class="mb-3">
                                <label class="form-label">Common Keywords (click to add):</label>
                                <div>
                                    <?php
                                    $keywords = ['users', 'reviews', 'tasks', 'meetings', 'leaves', 'companies', 'sales', 'clusters', 'today', 'yesterday', 'recent', 'pending', 'active', 'count', 'list', 'show'];
                                    foreach ($keywords as $keyword): ?>
                                        <span class="badge bg-info keyword-badge" onclick="addKeyword('<?php echo $keyword; ?>')">
                                            <?php echo $keyword; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-magic"></i> Generate & Execute Query
                            </button>
                            <small class="text-muted ms-2">AI understands business context and relationships</small>
                        </form>
                    </div>
                </div>
                
                <!-- Predefined Queries by Category -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-bolt"></i> Quick Queries by Category</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach($predefined_queries as $category => $category_data): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card category-card border-success">
                                        <div class="card-body">
                                            <div class="text-center">
                                                <?php
                                                $icons = [
                                                    'user' => 'fas fa-users',
                                                    'review' => 'fas fa-star',
                                                    'task' => 'fas fa-tasks',
                                                    'meeting' => 'fas fa-calendar-alt',
                                                    'leave' => 'fas fa-umbrella-beach',
                                                    'company' => 'fas fa-building',
                                                    'sales' => 'fas fa-chart-line',
                                                    'cluster' => 'fas fa-map-marker-alt',
                                                    'recent' => 'fas fa-history'
                                                ];
                                                $icon = isset($icons[$category]) ? $icons[$category] : 'fas fa-database';
                                                ?>
                                                <div class="category-icon text-success">
                                                    <i class="<?php echo $icon; ?>"></i>
                                                </div>
                                                <h6><?php echo $category_data['label']; ?></h6>
                                            </div>
                                            <div class="mt-2">
                                                <?php foreach($category_data['queries'] as $label => $query): ?>
                                                    <div class="query-example border-bottom" 
                                                         onclick="executePredefined('<?php echo urlencode($query); ?>')">
                                                        <small><i class="fas fa-play text-success"></i> <?php echo $label; ?></small>
                                                    </div>
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
            
            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Database Overview -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-database"></i> Database Overview</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Available Tables:</strong> <?php echo $total_tables; ?></p>
                        
                        <div class="mb-3">
                            <strong>Categories:</strong>
                            <div class="mt-2">
                                <span class="badge bg-primary">User Management</span>
                                <span class="badge bg-success">Reviews & Ratings</span>
                                <span class="badge bg-warning">Tasks</span>
                                <span class="badge bg-danger">Meetings</span>
                                <span class="badge bg-info">Leaves</span>
                                <span class="badge bg-dark">Companies</span>
                                <span class="badge bg-secondary">Sales</span>
                                <span class="badge bg-purple">Clusters</span>
                            </div>
                        </div>
                        
                        <!-- Smart Search -->
                        <div class="mb-3">
                            <strong>Search All Tables:</strong>
                            <form method="POST" action="<?php echo site_url('ai_query/smart_search'); ?>" class="mt-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search_keyword" placeholder="Search keyword...">
                                    <button class="btn btn-outline-info" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Sample Data Preview -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-eye"></i> Data Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-preview">
                            <?php foreach($sample_data as $table => $data): ?>
                                <?php if(!isset($data['error']) && !empty($data)): ?>
                                    <div class="mb-3">
                                        <strong><?php echo $table; ?>:</strong>
                                        <table class="table table-sm table-bordered mt-1">
                                            <thead>
                                                <tr>
                                                    <?php 
                                                    if(!empty($data)) {
                                                        $first_row = $data[0];
                                                        foreach(array_keys($first_row) as $col): 
                                                            if($col != 'id' && $col != 'password'): ?>
                                                                <th><?php echo substr($col, 0, 10); ?></th>
                                                            <?php endif;
                                                        endforeach;
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach(array_slice($data, 0, 2) as $row): ?>
                                                    <tr>
                                                        <?php foreach($row as $col => $val): 
                                                            if($col != 'id' && $col != 'password'): ?>
                                                                <td title="<?php echo htmlspecialchars($val); ?>">
                                                                    <?php echo substr($val, 0, 10); ?>
                                                                    <?php if(strlen($val) > 10) echo '...'; ?>
                                                                </td>
                                                            <?php endif;
                                                        endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Queries -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Recent Queries</h5>
                            <a href="<?php echo site_url('ai_query/clear_recent'); ?>" class="btn btn-sm btn-outline-danger">
                                Clear
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(empty($recent_queries)): ?>
                            <p class="text-muted">No recent queries</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach($recent_queries as $recent): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted"><?php echo $recent['timestamp']; ?></small>
                                            <span class="badge bg-success"><?php echo $recent['row_count']; ?></span>
                                        </div>
                                        <p class="mb-1" title="<?php echo htmlspecialchars($recent['query']); ?>">
                                            <?php echo htmlspecialchars(substr($recent['query'], 0, 50)); ?>
                                            <?php if(strlen($recent['query']) > 50) echo '...'; ?>
                                        </p>
                                        <code style="font-size: 0.7em;" title="<?php echo htmlspecialchars($recent['sql']); ?>">
                                            <?php echo htmlspecialchars(substr($recent['sql'], 0, 60)); ?>...
                                        </code>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add keyword to query textarea
        function addKeyword(keyword) {
            const textarea = document.querySelector('textarea[name="query"]');
            const current = textarea.value;
            const cursorPos = textarea.selectionStart;
            
            const newText = current.substring(0, cursorPos) + keyword + ' ' + current.substring(cursorPos);
            textarea.value = newText;
            textarea.focus();
            textarea.setSelectionRange(cursorPos + keyword.length + 1, cursorPos + keyword.length + 1);
        }
        
        // Execute predefined query
        function executePredefined(query) {
            window.location.href = '<?php echo site_url('ai_query/execute_predefined'); ?>?query=' + query;
        }
        
        // Auto-focus on query input
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('textarea[name="query"]').focus();
        });
        
        // Example query click handlers
        document.querySelectorAll('.query-example').forEach(function(element) {
            element.addEventListener('click', function() {
                const queryText = this.querySelector('small').textContent.replace(/^▶\s*/, '');
                document.querySelector('textarea[name="query"]').value = queryText;
            });
        });
    </script>
    
    <style>
        .bg-purple { background-color: #6f42c1; }
    </style>
</body>
</html>