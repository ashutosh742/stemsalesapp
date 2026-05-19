<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- Chart.js CSS -->
    <style>
        .chart-container {
            position: relative;
            margin: auto;
            width: 100%;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<?php
// Sample data
$data = $getAllUserData;
$dataJson = json_encode($data);
?>

<?php include("nav.php"); ?>
<div class="container">
    <hr>
    <h1 class="text-center my-4">Current Year Status Conversion Analysis</h1>
    <hr>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <label for="userFilter">User Filter:</label>
                <select class="form-control" id="userFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'task_user_name')) as $user) {
                        echo "<option value='$user'>$user</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="typeFilter">Type Filter:</label>
                <select class="form-control" id="typeFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'user_types')) as $type) {
                        echo "<option value='$type'>$type</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="conversionFilter">Conversion Filter:</label>
                <select class="form-control" id="conversionFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'conversion_name')) as $conversion) {
                        echo "<option value='$conversion'>$conversion</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="taskFilter">Task Filter:</label>
                <select class="form-control" id="taskFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'task_name')) as $task) {
                        echo "<option value='$task'>$task</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="startDateFilter">Start Date Filter:</label>
                <input type="date" class="form-control" id="startDateFilter">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="endDateFilter">End Date Filter:</label>
                <input type="date" class="form-control" id="endDateFilter">
            </div>
        </div>
    </div>


    <div class="col-md-12 text-center">
                    <button class="btn btn-success" id="downloadPdf">Download PDF</button>
                    <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
                </div>
                <br>    
    </div>

    <hr>
    <br>

    <div class="container-fluid">
    <div class="row">

        <div class="col-md-12 chart-container">
            Conversion Count by Date
            <canvas id="conversionDateChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Conversion Count by User and Date
            <canvas id="userConversionDateChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Conversion Count by User Type and Date
            <canvas id="userTypeConversionDateChart"></canvas>
        </div>

        <div class="col-md-12 chart-container">
            Task Distribution by User
            <canvas id="taskDistributionChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Task Distribution by User Type
            <canvas id="taskTypeDistributionChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Task Distribution by Conversion
            <canvas id="taskConversionDistributionChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Task Distribution by Task Name
            <canvas id="taskNameDistributionChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            User Task Count Comparison by Conversion
            <canvas id="userTaskCountComparisonChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Conversion Count by User and Type
            <canvas id="conversionUserTypeTaskCountChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Conversion Count by User and Conversion
            <canvas id="userConversionTaskCountChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Conversion Count by User Type and Conversion
            <canvas id="userTypeConversionTaskCountChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Top Users by Conversion Count
            <canvas id="topUsersConversionCountChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Top User Types by Conversion Count
            <canvas id="topUserTypesConversionCountChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            User-wise Conversion Distribution
            <canvas id="userWiseConversionChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            User Type-wise Conversion Distribution
            <canvas id="userTypeWiseConversionChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Conversion Comparison by User and Type
            <canvas id="conversionComparisonUserTypeChart"></canvas>
        </div>
        <div class="col-md-12 chart-container">
            Task Name Distribution by User
            <canvas id="taskNameUserChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            User Type Task Count Comparison
            <canvas id="userTypeTaskCountComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Task Name Distribution by User Type
            <canvas id="taskNameUserTypeChart"></canvas>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS -->
<script>
    $(document).ready(function() {
        const data = <?php echo $dataJson; ?>;

        function filterData() {
            const userFilter = $('#userFilter').val();
            const typeFilter = $('#typeFilter').val();
            const conversionFilter = $('#conversionFilter').val();
            const taskFilter = $('#taskFilter').val();
            const startDateFilter = $('#startDateFilter').val();
            const endDateFilter = $('#endDateFilter').val();

            return data.filter(item => {
                const userMatch = userFilter === "all" || item.task_user_name === userFilter;
                const typeMatch = typeFilter === "all" || item.user_types === typeFilter;
                const conversionMatch = conversionFilter === "all" || item.conversion_name === conversionFilter;
                const taskMatch = taskFilter === "all" || item.task_name === taskFilter;
                const dateMatch = (startDateFilter === "" || new Date(item.appointment_date) >= new Date(startDateFilter)) &&
                                 (endDateFilter === "" || new Date(item.appointment_date) <= new Date(endDateFilter));
                return userMatch && typeMatch && conversionMatch && taskMatch && dateMatch;
            });
        }

        function renderCharts(filteredData) {
            // Destroy existing charts before creating new ones
            const charts = [
                Chart.getChart('taskDistributionChart'),
                Chart.getChart('taskTypeDistributionChart'),
                Chart.getChart('taskConversionDistributionChart'),
                Chart.getChart('taskNameDistributionChart'),
                Chart.getChart('userTaskCountComparisonChart'),
                Chart.getChart('userTypeTaskCountComparisonChart'),
                Chart.getChart('conversionUserTypeTaskCountChart'),
                Chart.getChart('userConversionTaskCountChart'),
                Chart.getChart('userTypeConversionTaskCountChart'),
                Chart.getChart('topUsersConversionCountChart'),
                Chart.getChart('topUserTypesConversionCountChart'),
                Chart.getChart('userWiseConversionChart'),
                Chart.getChart('userTypeWiseConversionChart'),
                Chart.getChart('conversionComparisonUserTypeChart'),
                Chart.getChart('taskNameUserChart'),
                Chart.getChart('taskNameUserTypeChart'),
                Chart.getChart('conversionDateChart'),
                Chart.getChart('userConversionDateChart'),
                Chart.getChart('userTypeConversionDateChart')
            ];

            charts.forEach(chart => chart && chart.destroy());

            const colors = [
                'rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)',
                'rgba(231, 233, 237, 0.2)'
            ];

            const borders = [
                'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
                'rgba(231, 233, 237, 1)'
            ];

            // Task Distribution by User Chart
            const taskDistributionCtx = document.getElementById('taskDistributionChart').getContext('2d');
            new Chart(taskDistributionCtx, {
                type: 'bar',
                data: {
                    labels: filteredData.map(item => item.task_user_name),
                    datasets: [{
                        label: 'Conversion Count',
                        data: filteredData.map(item => parseInt(item.conversion_count)),
                        backgroundColor: colors[0],
                        borderColor: borders[0],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Task Distribution by User Type Chart
            const taskTypeDistributionCtx = document.getElementById('taskTypeDistributionChart').getContext('2d');
            new Chart(taskTypeDistributionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [{
                        label: 'Conversion Count',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[1],
                        borderColor: borders[1],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Task Distribution by Conversion Chart
            const taskConversionDistributionCtx = document.getElementById('taskConversionDistributionChart').getContext('2d');
            new Chart(taskConversionDistributionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.conversion_name))],
                    datasets: [{
                        label: 'Conversion Count',
                        data: [...new Set(filteredData.map(item => item.conversion_name))].map(conversion =>
                            filteredData.filter(item => item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[2],
                        borderColor: borders[2],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Task Distribution by Task Name Chart
            const taskNameDistributionCtx = document.getElementById('taskNameDistributionChart').getContext('2d');
            new Chart(taskNameDistributionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_name))],
                    datasets: [{
                        label: 'Conversion Count',
                        data: [...new Set(filteredData.map(item => item.task_name))].map(task =>
                            filteredData.filter(item => item.task_name === task).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[3],
                        borderColor: borders[3],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // User Task Count Comparison by Conversion Chart
            const userTaskCountComparisonCtx = document.getElementById('userTaskCountComparisonChart').getContext('2d');
            new Chart(userTaskCountComparisonCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.conversion_name))].map((conversion, index) => ({
                        label: conversion,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // User Type Task Count Comparison Chart
            const userTypeTaskCountComparisonCtx = document.getElementById('userTypeTaskCountComparisonChart').getContext('2d');
            new Chart(userTypeTaskCountComparisonCtx, {
                type: 'pie',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [{
                        label: 'Conversion Count',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    }]
                }
            });

            // Conversion Count by User and Type Chart
            const conversionUserTypeTaskCountCtx = document.getElementById('conversionUserTypeTaskCountChart').getContext('2d');
            new Chart(conversionUserTypeTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.conversion_name))],
                    datasets: [...new Set(filteredData.map(item => item.user_types))].map((type, index) => ({
                        label: type,
                        data: [...new Set(filteredData.map(item => item.conversion_name))].map(conversion =>
                            filteredData.filter(item => item.conversion_name === conversion && item.user_types === type).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Conversion Count by User and Conversion Chart
            const userConversionTaskCountCtx = document.getElementById('userConversionTaskCountChart').getContext('2d');
            new Chart(userConversionTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.conversion_name))].map((conversion, index) => ({
                        label: conversion,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Conversion Count by User Type and Conversion Chart
            const userTypeConversionTaskCountCtx = document.getElementById('userTypeConversionTaskCountChart').getContext('2d');
            new Chart(userTypeConversionTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [...new Set(filteredData.map(item => item.conversion_name))].map((conversion, index) => ({
                        label: conversion,
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type && item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Top Users by Conversion Count Chart
            const topUsersConversionCountCtx = document.getElementById('topUsersConversionCountChart').getContext('2d');
            new Chart(topUsersConversionCountCtx, {
                type: 'bar',
                data: {
                    labels: filteredData.map(item => item.task_user_name),
                    datasets: [{
                        label: 'Conversion Count',
                        data: filteredData.map(item => parseInt(item.conversion_count)),
                        backgroundColor: colors[0],
                        borderColor: borders[0],
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Top User Types by Conversion Count Chart
            const topUserTypesConversionCountCtx = document.getElementById('topUserTypesConversionCountChart').getContext('2d');
            new Chart(topUserTypesConversionCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [{
                        label: 'Conversion Count',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[1],
                        borderColor: borders[1],
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // User-wise Conversion Distribution Chart
            const userWiseConversionCtx = document.getElementById('userWiseConversionChart').getContext('2d');
            new Chart(userWiseConversionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.conversion_name))].map((conversion, index) => ({
                        label: conversion,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // User Type-wise Conversion Distribution Chart
            const userTypeWiseConversionCtx = document.getElementById('userTypeWiseConversionChart').getContext('2d');
            new Chart(userTypeWiseConversionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [...new Set(filteredData.map(item => item.conversion_name))].map((conversion, index) => ({
                        label: conversion,
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type && item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Conversion Comparison by User and Type Chart
            const conversionComparisonUserTypeCtx = document.getElementById('conversionComparisonUserTypeChart').getContext('2d');
            new Chart(conversionComparisonUserTypeCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.conversion_name))],
                    datasets: [
                        {
                            label: 'User Comparison',
                            data: [...new Set(filteredData.map(item => item.conversion_name))].map(conversion =>
                                filteredData.filter(item => item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                            ),
                            backgroundColor: colors[0],
                            borderColor: borders[0],
                            borderWidth: 1
                        },
                        {
                            label: 'User Type Comparison',
                            data: [...new Set(filteredData.map(item => item.conversion_name))].map(conversion =>
                                filteredData.filter(item => item.conversion_name === conversion).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                            ),
                            backgroundColor: colors[1],
                            borderColor: borders[1],
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Task Name Distribution by User Chart
            const taskNameUserCtx = document.getElementById('taskNameUserChart').getContext('2d');
            new Chart(taskNameUserCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.task_name))].map((task, index) => ({
                        label: task,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.task_name === task).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Task Name Distribution by User Type Chart
            const taskNameUserTypeCtx = document.getElementById('taskNameUserTypeChart').getContext('2d');
            new Chart(taskNameUserTypeCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [...new Set(filteredData.map(item => item.task_name))].map((task, index) => ({
                        label: task,
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type && item.task_name === task).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Conversion Count by Date Chart
            const conversionDateCtx = document.getElementById('conversionDateChart').getContext('2d');
            new Chart(conversionDateCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.appointment_date))],
                    datasets: [{
                        label: 'Conversion Count',
                        data: [...new Set(filteredData.map(item => item.appointment_date))].map(date =>
                            filteredData.filter(item => item.appointment_date === date).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[4],
                        borderColor: borders[4],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Conversion Count by User and Date Chart
            const userConversionDateCtx = document.getElementById('userConversionDateChart').getContext('2d');
            new Chart(userConversionDateCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.appointment_date))],
                    datasets: [...new Set(filteredData.map(item => item.task_user_name))].map((user, index) => ({
                        label: user,
                        data: [...new Set(filteredData.map(item => item.appointment_date))].map(date =>
                            filteredData.filter(item => item.appointment_date === date && item.task_user_name === user).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Conversion Count by User Type and Date Chart
            const userTypeConversionDateCtx = document.getElementById('userTypeConversionDateChart').getContext('2d');
            new Chart(userTypeConversionDateCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.appointment_date))],
                    datasets: [...new Set(filteredData.map(item => item.user_types))].map((type, index) => ({
                        label: type,
                        data: [...new Set(filteredData.map(item => item.appointment_date))].map(date =>
                            filteredData.filter(item => item.appointment_date === date && item.user_types === type).reduce((sum, item) => sum + parseInt(item.conversion_count), 0)
                        ),
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1
                    }))
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        $('select, input').change(function() {
            const filteredData = filterData();
            renderCharts(filteredData);
        });

        // Initial render
        renderCharts(data);





        
        $('#downloadPdf').click(function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            html2canvas(document.body, {
                scale: 0.5 // Adjust scale to fit content
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 210; // A4 width in mm
                const pageHeight = 295; // A4 height in mm
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position -= pageHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                doc.save('dashboard.pdf');
            });
        });

        // Download All Charts
        $('#downloadCharts').click(function() {
            const chartIds = ['taskDistributionChart', 'taskTypeDistributionChart', 'taskConversionDistributionChart', 'taskNameDistributionChart', 'userTaskCountComparisonChart', 'userTypeTaskCountComparisonChart', 'conversionUserTypeTaskCountChart', 'userConversionTaskCountChart', 'userTypeConversionTaskCountChart', 'topUsersConversionCountChart', 'topUserTypesConversionCountChart', 'userWiseConversionChart', 'userTypeWiseConversionChart', 'conversionComparisonUserTypeChart', 'taskNameUserChart', 'taskNameUserTypeChart', 'conversionDateChart', 'userConversionDateChart', 'userTypeConversionDateChart'];
            chartIds.forEach(chartId => {
                const chartCanvas = document.getElementById(chartId);
                const chartImage = chartCanvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = `${chartId}.png`;
                link.href = chartImage;
                link.click();
            });
        });










    });
</script>
</body>
</html>
