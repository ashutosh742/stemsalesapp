<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results: <?php echo $function_label; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; padding: 20px 0; }
        .result-header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .data-table { margin-top: 20px; }
        .back-btn { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <div class="back-btn">
            <a href="<?php echo site_url('function_query'); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Result Header -->
        <div class="result-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-chart-bar text-primary"></i> <?php echo htmlspecialchars($function_label); ?></h1>
                    <p class="text-muted">
                        <i class="fas fa-cog"></i> Function: <?php echo $function_name; ?> | 
                        <i class="fas fa-database"></i> Records: <?php echo $result_count; ?> | 
                        <i class="fas fa-clock"></i> Executed: <?php echo date('Y-m-d H:i:s'); ?>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success">Live Data</span>
                    <span class="badge bg-primary">Function Executed</span>
                </div>
            </div>
        </div>
        
        <!-- Results Table -->
        <div class="card data-table">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-table"></i> Results</h5>
            </div>
            <div class="card-body">
                <?php if(is_array($result_data) && !empty($result_data)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <?php 
                                    // Get column names from first row
                                    if (isset($result_data[0]) && is_array($result_data[0])) {
                                        foreach(array_keys($result_data[0]) as $column): ?>
                                            <th><?php echo ucwords(str_replace('_', ' ', $column)); ?></th>
                                        <?php endforeach;
                                    } else {
                                        echo '<th>Data</th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $display_limit = min(100, $result_count);
                                for($i = 0; $i < $display_limit; $i++): 
                                    $row = $result_data[$i];
                                ?>
                                    <tr>
                                        <?php 
                                        if (is_array($row)) {
                                            foreach($row as $cell): ?>
                                                <td>
                                                    <?php 
                                                    if(is_array($cell) || is_object($cell)) {
                                                        echo '[Complex Data]';
                                                    } else {
                                                        echo htmlspecialchars(substr($cell, 0, 100));
                                                        if(strlen($cell) > 100) echo '...';
                                                    }
                                                    ?>
                                                </td>
                                            <?php endforeach;
                                        } else {
                                            echo '<td>' . htmlspecialchars($row) . '</td>';
                                        }
                                        ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if($result_count > 100): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            Showing first 100 of <?php echo $result_count; ?> records.
                        </div>
                    <?php endif; ?>
                    
                <?php elseif(empty($result_data)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No data returned from function.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Function returned non-array data.
                        <pre><?php print_r($result_data); ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5><i class="fas fa-list"></i> Total Records</h5>
                        <h2><?php echo $result_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5><i class="fas fa-clock"></i> Execution Time</h5>
                        <h2>Now</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5><i class="fas fa-cog"></i> Function</h5>
                        <h6><?php echo $function_name; ?></h6>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- New Query Button -->
        <div class="text-center mt-5">
            <a href="<?php echo site_url('function_query'); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-redo"></i> Execute Another Function
            </a>
        </div>
    </div>
</body>
</html>