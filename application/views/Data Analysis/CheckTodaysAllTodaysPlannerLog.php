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
 <h1 class="text-center my-4">Todays Planner Log Analysis</h1>
 <hr>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <label for="userFilter">User Filter:</label>
                <select class="form-control" id="userFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'to_user_name')) as $user) {
                        echo "<option value='$user'>$user</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="companyFilter">Company Filter:</label>
                <select class="form-control" id="companyFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'company_name')) as $company) {
                        echo "<option value='$company'>$company</option>";
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
                <label for="statusFilter">Status Filter:</label>
                <select class="form-control" id="statusFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'name')) as $status) {
                        echo "<option value='$status'>$status</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="timeDifferenceFilter">Time Difference Filter:</label>
                <select class="form-control" id="timeDifferenceFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'time_difference')) as $timeDiff) {
                        echo "<option value='$timeDiff'>$timeDiff</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="startDateFilter">Start Date Filter:</label>
                <input type="date" class="form-control" id="startDateFilter">
            </div>
        </div>
        <div class="col-md-2">
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
        <div class="col-md-6 chart-container">
            Task Distribution by User
            <canvas id="taskDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Task Distribution by Company
            <canvas id="taskCompanyDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Task Distribution by Status
            <canvas id="taskStatusDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Task Distribution by Task Name
            <canvas id="taskNameDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            User Planner Log Count Comparison by Status
            <canvas id="userTaskCountComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Company Planner Log Count Comparison
            <canvas id="companyTaskCountComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Status Count by User and Company
            <canvas id="statusUserCompanyTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Status Count by User and Status
            <canvas id="userStatusTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Status Count by Company and Status
            <canvas id="companyStatusTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Top Users by Planner Log Count
            <canvas id="topUsersTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Top Companies by Planner Log Count
            <canvas id="topCompaniesTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            User-wise Status Distribution
            <canvas id="userWiseStatusChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Company-wise Status Distribution
            <canvas id="companyWiseStatusChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Status Comparison by User and Company
            <canvas id="statusComparisonUserCompanyChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Task Name Distribution by User
            <canvas id="taskNameUserChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Task Name Distribution by Company
            <canvas id="taskNameCompanyChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Planner Log Count by Date
            <canvas id="taskDateChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Planner Log Count by User and Date
            <canvas id="userTaskDateChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Planner Log Count by Company and Date
            <canvas id="companyTaskDateChart"></canvas>
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
            const companyFilter = $('#companyFilter').val();
            const taskFilter = $('#taskFilter').val();
            const statusFilter = $('#statusFilter').val();
            const timeDifferenceFilter = $('#timeDifferenceFilter').val();
            const startDateFilter = $('#startDateFilter').val();
            const endDateFilter = $('#endDateFilter').val();

            return data.filter(item => {
                const userMatch = userFilter === "all" || item.to_user_name === userFilter;
                const companyMatch = companyFilter === "all" || item.company_name === companyFilter;
                const taskMatch = taskFilter === "all" || item.task_name === taskFilter;
                const statusMatch = statusFilter === "all" || item.name === statusFilter;
                const timeDifferenceMatch = timeDifferenceFilter === "all" || item.time_difference === timeDifferenceFilter;
                const dateMatch = (startDateFilter === "" || new Date(item.org_task_date) >= new Date(startDateFilter)) &&
                                 (endDateFilter === "" || new Date(item.org_task_date) <= new Date(endDateFilter));
                return userMatch && companyMatch && taskMatch && statusMatch && timeDifferenceMatch && dateMatch;
            });
        }

        function renderCharts(filteredData) {
            // Destroy existing charts before creating new ones
            const charts = [
                Chart.getChart('taskDistributionChart'),
                Chart.getChart('taskCompanyDistributionChart'),
                Chart.getChart('taskStatusDistributionChart'),
                Chart.getChart('taskNameDistributionChart'),
                Chart.getChart('userTaskCountComparisonChart'),
                Chart.getChart('companyTaskCountComparisonChart'),
                Chart.getChart('statusUserCompanyTaskCountChart'),
                Chart.getChart('userStatusTaskCountChart'),
                Chart.getChart('companyStatusTaskCountChart'),
                Chart.getChart('topUsersTaskCountChart'),
                Chart.getChart('topCompaniesTaskCountChart'),
                Chart.getChart('userWiseStatusChart'),
                Chart.getChart('companyWiseStatusChart'),
                Chart.getChart('statusComparisonUserCompanyChart'),
                Chart.getChart('taskNameUserChart'),
                Chart.getChart('taskNameCompanyChart'),
                Chart.getChart('taskDateChart'),
                Chart.getChart('userTaskDateChart'),
                Chart.getChart('companyTaskDateChart')
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
                    labels: filteredData.map(item => item.to_user_name),
                    datasets: [{
                        label: 'Planner Log Count',
                        data: filteredData.map(item => parseInt(item.duplicate_count)),
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

            // Task Distribution by Company Chart
            const taskCompanyDistributionCtx = document.getElementById('taskCompanyDistributionChart').getContext('2d');
            new Chart(taskCompanyDistributionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.company_name))],
                    datasets: [{
                        label: 'Planner Log Count',
                        data: [...new Set(filteredData.map(item => item.company_name))].map(company =>
                            filteredData.filter(item => item.company_name === company).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Task Distribution by Status Chart
            const taskStatusDistributionCtx = document.getElementById('taskStatusDistributionChart').getContext('2d');
            new Chart(taskStatusDistributionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.name))],
                    datasets: [{
                        label: 'Planner Log Count',
                        data: [...new Set(filteredData.map(item => item.name))].map(status =>
                            filteredData.filter(item => item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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
                        label: 'Planner Log Count',
                        data: [...new Set(filteredData.map(item => item.task_name))].map(task =>
                            filteredData.filter(item => item.task_name === task).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // User Planner Log Count Comparison by Status Chart
            const userTaskCountComparisonCtx = document.getElementById('userTaskCountComparisonChart').getContext('2d');
            new Chart(userTaskCountComparisonCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.to_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.to_user_name))].map(user =>
                            filteredData.filter(item => item.to_user_name === user && item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Company Planner Log Count Comparison Chart
            const companyTaskCountComparisonCtx = document.getElementById('companyTaskCountComparisonChart').getContext('2d');
            new Chart(companyTaskCountComparisonCtx, {
                type: 'pie',
                data: {
                    labels: [...new Set(filteredData.map(item => item.company_name))],
                    datasets: [{
                        label: 'Planner Log Count',
                        data: [...new Set(filteredData.map(item => item.company_name))].map(company =>
                            filteredData.filter(item => item.company_name === company).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
                        ),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    }]
                }
            });

            // Status Count by User and Company Chart
            const statusUserCompanyTaskCountCtx = document.getElementById('statusUserCompanyTaskCountChart').getContext('2d');
            new Chart(statusUserCompanyTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.name))],
                    datasets: [...new Set(filteredData.map(item => item.company_name))].map((company, index) => ({
                        label: company,
                        data: [...new Set(filteredData.map(item => item.name))].map(status =>
                            filteredData.filter(item => item.name === status && item.company_name === company).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Status Count by User and Status Chart
            const userStatusTaskCountCtx = document.getElementById('userStatusTaskCountChart').getContext('2d');
            new Chart(userStatusTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.to_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.to_user_name))].map(user =>
                            filteredData.filter(item => item.to_user_name === user && item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Status Count by Company and Status Chart
            const companyStatusTaskCountCtx = document.getElementById('companyStatusTaskCountChart').getContext('2d');
            new Chart(companyStatusTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.company_name))],
                    datasets: [...new Set(filteredData.map(item => item.name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.company_name))].map(company =>
                            filteredData.filter(item => item.company_name === company && item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Top Users by Planner Log Count Chart
            const topUsersTaskCountCtx = document.getElementById('topUsersTaskCountChart').getContext('2d');
            new Chart(topUsersTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: filteredData.map(item => item.to_user_name),
                    datasets: [{
                        label: 'Planner Log Count',
                        data: filteredData.map(item => parseInt(item.duplicate_count)),
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

            // Top Companies by Planner Log Count Chart
            const topCompaniesTaskCountCtx = document.getElementById('topCompaniesTaskCountChart').getContext('2d');
            new Chart(topCompaniesTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.company_name))],
                    datasets: [{
                        label: 'Planner Log Count',
                        data: [...new Set(filteredData.map(item => item.company_name))].map(company =>
                            filteredData.filter(item => item.company_name === company).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // User-wise Status Distribution Chart
            const userWiseStatusCtx = document.getElementById('userWiseStatusChart').getContext('2d');
            new Chart(userWiseStatusCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.to_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.to_user_name))].map(user =>
                            filteredData.filter(item => item.to_user_name === user && item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Company-wise Status Distribution Chart
            const companyWiseStatusCtx = document.getElementById('companyWiseStatusChart').getContext('2d');
            new Chart(companyWiseStatusCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.company_name))],
                    datasets: [...new Set(filteredData.map(item => item.name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.company_name))].map(company =>
                            filteredData.filter(item => item.company_name === company && item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Status Comparison by User and Company Chart
            const statusComparisonUserCompanyCtx = document.getElementById('statusComparisonUserCompanyChart').getContext('2d');
            new Chart(statusComparisonUserCompanyCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.name))],
                    datasets: [
                        {
                            label: 'User Comparison',
                            data: [...new Set(filteredData.map(item => item.name))].map(status =>
                                filteredData.filter(item => item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
                            ),
                            backgroundColor: colors[0],
                            borderColor: borders[0],
                            borderWidth: 1
                        },
                        {
                            label: 'Company Comparison',
                            data: [...new Set(filteredData.map(item => item.name))].map(status =>
                                filteredData.filter(item => item.name === status).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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
                    labels: [...new Set(filteredData.map(item => item.to_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.task_name))].map((task, index) => ({
                        label: task,
                        data: [...new Set(filteredData.map(item => item.to_user_name))].map(user =>
                            filteredData.filter(item => item.to_user_name === user && item.task_name === task).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Task Name Distribution by Company Chart
            const taskNameCompanyCtx = document.getElementById('taskNameCompanyChart').getContext('2d');
            new Chart(taskNameCompanyCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.company_name))],
                    datasets: [...new Set(filteredData.map(item => item.task_name))].map((task, index) => ({
                        label: task,
                        data: [...new Set(filteredData.map(item => item.company_name))].map(company =>
                            filteredData.filter(item => item.company_name === company && item.task_name === task).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Planner Log Count by Date Chart
            const taskDateCtx = document.getElementById('taskDateChart').getContext('2d');
            new Chart(taskDateCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.org_task_date.split(' ')[0]))],
                    datasets: [{
                        label: 'Planner Log Count',
                        data: [...new Set(filteredData.map(item => item.org_task_date.split(' ')[0]))].map(date =>
                            filteredData.filter(item => item.org_task_date.split(' ')[0] === date).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Planner Log Count by User and Date Chart
            const userTaskDateCtx = document.getElementById('userTaskDateChart').getContext('2d');
            new Chart(userTaskDateCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.org_task_date.split(' ')[0]))],
                    datasets: [...new Set(filteredData.map(item => item.to_user_name))].map((user, index) => ({
                        label: user,
                        data: [...new Set(filteredData.map(item => item.org_task_date.split(' ')[0]))].map(date =>
                            filteredData.filter(item => item.org_task_date.split(' ')[0] === date && item.to_user_name === user).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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

            // Planner Log Count by Company and Date Chart
            const companyTaskDateCtx = document.getElementById('companyTaskDateChart').getContext('2d');
            new Chart(companyTaskDateCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.org_task_date.split(' ')[0]))],
                    datasets: [...new Set(filteredData.map(item => item.company_name))].map((company, index) => ({
                        label: company,
                        data: [...new Set(filteredData.map(item => item.org_task_date.split(' ')[0]))].map(date =>
                            filteredData.filter(item => item.org_task_date.split(' ')[0] === date && item.company_name === company).reduce((sum, item) => sum + parseInt(item.duplicate_count), 0)
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
            const chartIds = ['taskDistributionChart', 'taskCompanyDistributionChart', 'taskStatusDistributionChart', 'taskNameDistributionChart', 'userTaskCountComparisonChart', 'companyTaskCountComparisonChart', 'statusUserCompanyTaskCountChart', 'userStatusTaskCountChart', 'companyStatusTaskCountChart', 'topUsersTaskCountChart', 'topCompaniesTaskCountChart', 'userWiseStatusChart', 'companyWiseStatusChart', 'statusComparisonUserCompanyChart', 'taskNameUserChart', 'taskNameCompanyChart', 'taskDateChart', 'userTaskDateChart', 'companyTaskDateChart'];
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
