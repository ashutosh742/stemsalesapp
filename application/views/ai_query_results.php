<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .sql-box { background: #2d2d2d; color: #fff; padding: 15px; border-radius: 5px; font-family: monospace; }
        .table-responsive { max-height: 500px; overflow-y: auto; }
        .ai-response { background: #e8f4fd; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <a href="<?php echo site_url('ai_query'); ?>" class="btn btn-secondary mb-4">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        
        <!-- User Query -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Your Question</h5>
            </div>
            <div class="card-body">
                <p><?php echo htmlspecialchars($user_input); ?></p>
            </div>
        </div>
        
        <!-- Generated SQL -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5>Generated SQL</h5>
            </div>
            <div class="card-body">
                <div class="sql-box">
                    <?php echo htmlspecialchars($generated_sql); ?>
                </div>
                <div class="mt-3">
                    <a href="<?php echo site_url('ai_query/export_csv?sql=' . urlencode($generated_sql)); ?>" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
        
        <!-- AI Explanation -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>AI Analysis</h5>
            </div>
            <div class="card-body ai-response">
                <?php echo nl2br(htmlspecialchars($ai_explanation)); ?>
            </div>
        </div>
        
        <!-- Results -->
        <div class="card">
            <div class="card-header">
                <h5>Results <span class="badge bg-primary"><?php echo $result_count; ?> records</span></h5>
            </div>
            <div class="card-body">
                <?php if($result_count > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <?php foreach($columns as $column): ?>
                                        <th><?php echo $column; ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $display_limit = min(50, $result_count);
                                for($i = 0; $i < $display_limit; $i++): 
                                    $row = $query_results[$i];
                                ?>
                                    <tr>
                                        <?php foreach($row as $value): ?>
                                            <td>
                                                <?php 
                                                if(is_array($value) || is_object($value)) {
                                                    echo '[Complex Data]';
                                                } else {
                                                    echo htmlspecialchars(substr($value, 0, 100));
                                                    if(strlen($value) > 100) echo '...';
                                                }
                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if($result_count > 50): ?>
                        <div class="alert alert-info mt-3">
                            Showing first 50 of <?php echo $result_count; ?> rows. Export to CSV for all data.
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-warning">
                        No records found.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- New Query Button -->
        <div class="text-center mt-4">
            <a href="<?php echo site_url('ai_query'); ?>" class="btn btn-primary btn-lg">
                Ask Another Question
            </a>
        </div>
    </div>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>