<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Function Query Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        .card { margin-bottom: 20px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .function-table { width: 100%; }
        .function-table tr:hover { background: #f8f9fa; cursor: pointer; }
        .function-table td { padding: 10px; border-bottom: 1px solid #dee2e6; }
        .category-header { background: #007bff; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .search-box { margin-bottom: 20px; }
        .recent-query { border-left: 3px solid #28a745; padding-left: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-code text-primary"></i> Function Query Dashboard</h1>
                    <div>
                        <span class="badge bg-primary"><?php echo count($all_functions); ?> Functions</span>
                        <span class="badge bg-success ms-2">Live Data</span>
                    </div>
                </div>
                <p class="text-muted">Click on any function to execute it and view results</p>
            </div>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Left: Function Tables -->
            <div class="col-lg-8">
                <!-- Search Box -->
                <div class="card search-box">
                    <div class="card-body">
                        <form method="POST" action="<?php echo site_url('function_query/execute_function'); ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" 
                                       placeholder="Type function name or select from below..." 
                                       id="functionSearch">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-play"></i> Execute
                                </button>
                            </div>
                            <small class="text-muted">Search will show suggestions as you type</small>
                        </form>
                    </div>
                </div>
                
                <!-- Function Categories -->
                <?php foreach($function_categories as $category => $category_data): ?>
                    <div class="card">
                        <div class="category-header">
                            <h5 class="mb-0"><?php echo $category_data['label']; ?></h5>
                        </div>
                        <div class="card-body">
                            <table class="function-table">
                                <?php foreach($category_data['functions'] as $label => $function): ?>
                                    <tr onclick="executeFunction('<?php echo $label; ?>')">
                                        <td width="70%">
                                            <strong><?php echo $label; ?></strong><br>
                                            <small class="text-muted">Function: <?php echo $function; ?></small>
                                        </td>
                                        <td width="30%" class="text-end">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-play"></i> Execute
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Multiple Execution Form -->
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Execute Multiple Functions</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo site_url('function_query/execute_multiple'); ?>">
                            <div class="mb-3">
                                <label class="form-label">Select functions (comma separated or multiple selection):</label>
                                <textarea class="form-control" name="function_labels" rows="3" 
                                          placeholder="Enter function labels separated by commas"></textarea>
                                <small class="text-muted">Example: Show all users, Today's meetings, Pending leave requests</small>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-bolt"></i> Execute Multiple
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Right: Recent Queries -->
            <div class="col-lg-4">
                <!-- Recent Queries -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Recent Functions</h5>
                            <a href="<?php echo site_url('function_query/clear_recent'); ?>" class="btn btn-sm btn-outline-danger">
                                Clear
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(empty($recent_queries)): ?>
                            <p class="text-muted">No recent functions executed</p>
                        <?php else: ?>
                            <?php foreach($recent_queries as $recent): ?>
                                <div class="recent-query">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted"><?php echo $recent['timestamp']; ?></small>
                                        <?php if(isset($recent['record_count'])): ?>
                                            <span class="badge bg-success"><?php echo $recent['record_count']; ?> records</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-1">
                                        <strong><?php echo htmlspecialchars(substr($recent['label'], 0, 40)); ?></strong>
                                        <?php if(strlen($recent['label']) > 40) echo '...'; ?>
                                    </p>
                                    <?php if(isset($recent['function'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-cog"></i> <?php echo $recent['function']; ?>
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
                                    <h4 class="text-primary"><?php echo count($all_functions); ?></h4>
                                    <small>Available Functions</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-success"><?php echo count($function_categories); ?></h4>
                                    <small>Categories</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <h4 class="text-warning">Real-time</h4>
                                    <small>Data Execution</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> How to Use</h5>
                    </div>
                    <div class="card-body">
                        <ol class="small">
                            <li>Click any function in the tables</li>
                            <li>Or type function name in search box</li>
                            <li>View results in table format</li>
                            <li>Execute multiple functions at once</li>
                            <li>Recent functions are saved for quick access</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Execute function when clicked
        function executeFunction(label) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo site_url('function_query/execute_function'); ?>';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'function_label';
            input.value = label;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        // Search autocomplete
        $(document).ready(function() {
            $('#functionSearch').on('input', function() {
                const searchTerm = $(this).val();
                if (searchTerm.length > 2) {
                    $.get('<?php echo site_url('function_query/search_functions'); ?>', 
                        { q: searchTerm }, 
                        function(data) {
                            // You can implement dropdown here
                            console.log('Search results:', data);
                        }
                    );
                }
            });
            
            // Auto-focus on search
            $('#functionSearch').focus();
        });
    </script>
</body>
</html>