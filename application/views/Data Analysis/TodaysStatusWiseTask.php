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
            /* height: 40vh; */
            width: 100%;
        }
    </style>
</head>
<body>

<?php
// dd($getAllUserData);
$dataJson = json_encode($getAllUserData);
?>

<?php include("nav.php"); ?>
<div class="container">
    <hr>
         <h1 class="text-center my-4">Todays Status wise Task Graph</h1>
    <hr>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <label for="userFilter">User Filter:</label>
                <select class="form-control" id="userFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($getAllUserData, 'task_user_name')) as $user) {
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
                    <?php foreach (array_unique(array_column($getAllUserData, 'user_types')) as $type) {
                        echo "<option value='$type'>$type</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="statusFilter">Status Filter:</label>
                <select class="form-control" id="statusFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($getAllUserData, 'status_name')) as $status) {
                        echo "<option value='$status'>$status</option>";
                    } ?>
                </select>
            </div>
        </div>
    </div>


    <hr>   
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
        <div class="col-md-6 chart-container mt-5">
            Task Distribution by User
            <canvas id="taskDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Distribution by User Type
            <canvas id="taskTypeDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Distribution by Status
            <canvas id="taskStatusDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            User Task Count Comparison by Status
            <canvas id="userTaskCountComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            User Type Task Count Comparison
            <canvas id="userTypeTaskCountComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Status Task Count Comparison
            <canvas id="statusTaskCountComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Count by Status and User Type
            <canvas id="statusUserTypeTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Count by User and Status
            <canvas id="userStatusTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Count by User Type and Status
            <canvas id="userTypeStatusTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Top Users by Task Count
            <canvas id="topUsersTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Top User Types by Task Count
            <canvas id="topUserTypesTaskCountChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            User-wise Status Distribution
            <canvas id="userWiseStatusChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            User Type-wise Status Distribution
            <canvas id="userTypeWiseStatusChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Status Comparison by User and Type
            <canvas id="statusComparisonUserTypeChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Status Distribution Over Time
            <canvas id="statusDistributionOverTimeChart"></canvas>
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
            const statusFilter = $('#statusFilter').val();

            return data.filter(item => {
                const userMatch = userFilter === "all" || item.task_user_name === userFilter;
                const typeMatch = typeFilter === "all" || item.user_types === typeFilter;
                const statusMatch = statusFilter === "all" || item.status_name === statusFilter;
                return userMatch && typeMatch && statusMatch;
            });
        }

        function renderCharts(filteredData) {
            // Destroy existing charts before creating new ones
            const charts = [
                Chart.getChart('taskDistributionChart'),
                Chart.getChart('taskTypeDistributionChart'),
                Chart.getChart('taskStatusDistributionChart'),
                Chart.getChart('userTaskCountComparisonChart'),
                Chart.getChart('userTypeTaskCountComparisonChart'),
                Chart.getChart('statusTaskCountComparisonChart'),
                Chart.getChart('statusUserTypeTaskCountChart'),
                Chart.getChart('userStatusTaskCountChart'),
                Chart.getChart('userTypeStatusTaskCountChart'),
                Chart.getChart('topUsersTaskCountChart'),
                Chart.getChart('topUserTypesTaskCountChart'),
                Chart.getChart('userWiseStatusChart'),
                Chart.getChart('userTypeWiseStatusChart'),
                Chart.getChart('statusComparisonUserTypeChart'),
                Chart.getChart('statusDistributionOverTimeChart')
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
                        label: 'Task Count',
                        data: filteredData.map(item => parseInt(item.task_count)),
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
                        label: 'Task Count',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Task Count',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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

            // User Task Count Comparison by Status Chart
            const userTaskCountComparisonCtx = document.getElementById('userTaskCountComparisonChart').getContext('2d');
            new Chart(userTaskCountComparisonCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.status_name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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
                        label: 'Task Count',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.task_count), 0)
                        ),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    }]
                }
            });

            // Status Task Count Comparison Chart
            const statusTaskCountComparisonCtx = document.getElementById('statusTaskCountComparisonChart').getContext('2d');
            new Chart(statusTaskCountComparisonCtx, {
                type: 'pie',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Task Count',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + item.task_count, 0)
                        ),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    }]
                }
            });

            // Task Count by Status and User Type Chart
            const statusUserTypeTaskCountCtx = document.getElementById('statusUserTypeTaskCountChart').getContext('2d');
            new Chart(statusUserTypeTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [...new Set(filteredData.map(item => item.user_types))].map((type, index) => ({
                        label: type,
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status && item.user_types === type).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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

            // Task Count by User and Status Chart
            const userStatusTaskCountCtx = document.getElementById('userStatusTaskCountChart').getContext('2d');
            new Chart(userStatusTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.status_name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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

            // Task Count by User Type and Status Chart
            const userTypeStatusTaskCountCtx = document.getElementById('userTypeStatusTaskCountChart').getContext('2d');
            new Chart(userTypeStatusTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [...new Set(filteredData.map(item => item.status_name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type && item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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

            // Top Users by Task Count Chart
            const topUsersTaskCountCtx = document.getElementById('topUsersTaskCountChart').getContext('2d');
            new Chart(topUsersTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: filteredData.map(item => item.task_user_name),
                    datasets: [{
                        label: 'Task Count',
                        data: filteredData.map(item => parseInt(item.task_count)),
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

            // Top User Types by Task Count Chart
            const topUserTypesTaskCountCtx = document.getElementById('topUserTypesTaskCountChart').getContext('2d');
            new Chart(topUserTypesTaskCountCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [{
                        label: 'Task Count',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.status_name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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

            // User Type-wise Status Distribution Chart
            const userTypeWiseStatusCtx = document.getElementById('userTypeWiseStatusChart').getContext('2d');
            new Chart(userTypeWiseStatusCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [...new Set(filteredData.map(item => item.status_name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type && item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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

            // Status Comparison by User and Type Chart
            const statusComparisonUserTypeCtx = document.getElementById('statusComparisonUserTypeChart').getContext('2d');
            new Chart(statusComparisonUserTypeCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [
                        {
                            label: 'User Comparison',
                            data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                                filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
                            ),
                            backgroundColor: colors[0],
                            borderColor: borders[0],
                            borderWidth: 1
                        },
                        {
                            label: 'User Type Comparison',
                            data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                                filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_count), 0)
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

            // Status Distribution Over Time Chart
            const statusDistributionOverTimeCtx = document.getElementById('statusDistributionOverTimeChart').getContext('2d');
            new Chart(statusDistributionOverTimeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Example months
                    datasets: [...new Set(filteredData.map(item => item.status_name))].map((status, index) => ({
                        label: status,
                        data: [/* Example data points */],
                        backgroundColor: colors[index % colors.length],
                        borderColor: borders[index % borders.length],
                        borderWidth: 1,
                        fill: false
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

        $('select').change(function() {
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
            const chartIds = ['taskDistributionChart', 'taskTypeDistributionChart', 'taskStatusDistributionChart', 'userTaskCountComparisonChart', 'userTypeTaskCountComparisonChart', 'statusTaskCountComparisonChart', 'statusUserTypeTaskCountChart', 'userStatusTaskCountChart', 'userTypeStatusTaskCountChart', 'topUsersTaskCountChart', 'topUserTypesTaskCountChart', 'userWiseStatusChart', 'userTypeWiseStatusChart', 'statusComparisonUserTypeChart', 'statusDistributionOverTimeChart'];
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
