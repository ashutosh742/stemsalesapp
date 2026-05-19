<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied / Under Maintenance</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
            /* background-image: url('<?php echo base_url("uploads/istockphoto-1348157796-612x612.jpg"); ?>'); */
            
        }
        .img{
            background-image: url('<?php echo base_url("uploads/istockphoto-1348157796-612x612.jpg"); ?>');
            background-size: cover;
        }
        .container {
            text-align: center;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: white;
            
        }
        h1 {
            font-size: 2.5rem;
            color: #dc3545;
        }
        p {
            font-size: 1.2rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-md-6 img" >
                
            </div>
            <div class="col-md-6">
                <h1>Access Restricted | Under Maintainace</h1>
                <p>We're sorry, but you do not have permission to access this page.</p>
                <p>Or the site is currently under maintenance. Please check back later.</p>
            </div>

        </div>
        
        <!-- <a href="/" class="btn btn-primary">Go to Homepage</a> -->
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
