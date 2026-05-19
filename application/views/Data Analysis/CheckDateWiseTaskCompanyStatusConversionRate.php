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
// New data
$data = $getAllUserData;
$dataJson = json_encode($data);
?>

<?php include("nav.php"); ?>
<div class="container">
    <hr>
    <h1 class="text-center my-4">Date Wise Task Status Analysis</h1>
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
                <label for="statusFilter">Status Filter:</label>
                <select class="form-control" id="statusFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'status_name')) as $status) {
                        echo "<option value='$status'>$status</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="dateFilter">Date Filter:</label>
                <select class="form-control" id="dateFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'task_date')) as $date) {
                        echo "<option value='$date'>$date</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="totalCountFilter">Total Count Filter:</label>
                <select class="form-control" id="totalCountFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'total_count')) as $count) {
                        echo "<option value='$count'>$count</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="actionYesPurposeYesFilter">Action Yes Purpose Yes Filter:</label>
                <select class="form-control" id="actionYesPurposeYesFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'action_yes_purpose_yes')) as $yesYes) {
                        echo "<option value='$yesYes'>$yesYes</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="actionYesPurposeNoFilter">Action Yes Purpose No Filter:</label>
                <select class="form-control" id="actionYesPurposeNoFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'action_yes_purpose_no')) as $yesNo) {
                        echo "<option value='$yesNo'>$yesNo</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="actionNoPurposeNoFilter">Action No Purpose No Filter:</label>
                <select class="form-control" id="actionNoPurposeNoFilter">
                    <option value="all">All</option>
                    <?php foreach (array_unique(array_column($data, 'action_no_purpose_no')) as $noNo) {
                        echo "<option value='$noNo'>$noNo</option>";
                    } ?>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-center">
        <button class="btn btn-success" id="downloadPdf">Download PDF</button>
        <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
    </div>
    <br>

    <hr>
    <br>

    <div class="container-fluid">
        <div class="row">
        <div class="col-md-12 chart-container">
                <canvas id="stackedBarChart3"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                <canvas id="taskDistributionChart"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                <canvas id="taskCompletionChart"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                <canvas id="actionPurposeChart"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                <canvas id="totalCountByUserStatusChart"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                <canvas id="stackedBarChart1"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                <canvas id="stackedBarChart2"></canvas>
            </div>
            
            <div class="col-md-12 chart-container">
                <canvas id="stackedBarChart4"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                <canvas id="stackedBarChart5"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <canvas id="pieChart1"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <canvas id="pieChart2"></canvas>
            </div>
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
            const dateFilter = $('#dateFilter').val();
            const totalCountFilter = $('#totalCountFilter').val();
            const actionYesPurposeYesFilter = $('#actionYesPurposeYesFilter').val();
            const actionYesPurposeNoFilter = $('#actionYesPurposeNoFilter').val();
            const actionNoPurposeNoFilter = $('#actionNoPurposeNoFilter').val();

            return data.filter(item => {
                const userMatch = userFilter === "all" || item.task_user_name === userFilter;
                const typeMatch = typeFilter === "all" || item.user_types === typeFilter;
                const statusMatch = statusFilter === "all" || item.status_name === statusFilter;
                const dateMatch = dateFilter === "all" || item.task_date === dateFilter;
                const totalCountMatch = totalCountFilter === "all" || parseInt(item.total_count) === parseInt(totalCountFilter);
                const actionYesPurposeYesMatch = actionYesPurposeYesFilter === "all" || parseInt(item.action_yes_purpose_yes) === parseInt(actionYesPurposeYesFilter);
                const actionYesPurposeNoMatch = actionYesPurposeNoFilter === "all" || parseInt(item.action_yes_purpose_no) === parseInt(actionYesPurposeNoFilter);
                const actionNoPurposeNoMatch = actionNoPurposeNoFilter === "all" || parseInt(item.action_no_purpose_no) === parseInt(actionNoPurposeNoFilter);

                return userMatch && typeMatch && statusMatch && dateMatch && totalCountMatch && actionYesPurposeYesMatch && actionYesPurposeNoMatch && actionNoPurposeNoMatch;
            });
        }

        function renderCharts(filteredData) {
            // Destroy existing charts before creating new ones
            const charts = [
                Chart.getChart('taskDistributionChart'),
                Chart.getChart('taskCompletionChart'),
                Chart.getChart('actionPurposeChart'),
                Chart.getChart('totalCountByUserStatusChart'),
                Chart.getChart('stackedBarChart1'),
                Chart.getChart('stackedBarChart2'),
                Chart.getChart('stackedBarChart3'),
                Chart.getChart('stackedBarChart4'),
                Chart.getChart('stackedBarChart5'),
                Chart.getChart('pieChart1'),
                Chart.getChart('pieChart2')
            ];

            charts.forEach(chart => chart && chart.destroy());

            const colors = [
                'rgba(75, 192, 192, 0.2)',  // Teal
                'rgba(255, 99, 132, 0.2)',  // Red
                'rgba(54, 162, 235, 0.2)',  // Blue
                'rgba(255, 206, 86, 0.2)',  // Yellow
                'rgba(153, 102, 255, 0.2)', // Purple
                'rgba(255, 159, 64, 0.2)',  // Orange
                'rgba(231, 233, 237, 0.2)', // Light Gray
                'rgba(75, 192, 120, 0.2)',  // Green
                'rgba(255, 99, 255, 0.2)',  // Magenta
                'rgba(54, 162, 162, 0.2)',  // Cyan
                'rgba(255, 206, 206, 0.2)', // Light Red
                'rgba(153, 102, 102, 0.2)', // Dark Red
                'rgba(255, 159, 159, 0.2)', // Light Orange
                'rgba(231, 233, 137, 0.2)', // Light Yellow
                'rgba(75, 192, 255, 0.2)',  // Light Blue
                'rgba(255, 99, 99, 0.2)',   // Light Pink
                'rgba(54, 162, 54, 0.2)',   // Dark Green
                'rgba(255, 206, 54, 0.2)',  // Dark Yellow
                'rgba(153, 102, 204, 0.2)', // Indigo
                'rgba(255, 159, 255, 0.2)', // Light Magenta
                'rgba(231, 233, 231, 0.2)'  // Very Light Gray
            ];

            const borders = [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(231, 233, 237, 1)',
                'rgba(75, 192, 120, 1)',
                'rgba(255, 99, 255, 1)',
                'rgba(54, 162, 162, 1)',
                'rgba(255, 206, 206, 1)',
                'rgba(153, 102, 102, 1)',
                'rgba(255, 159, 159, 1)',
                'rgba(231, 233, 137, 1)',
                'rgba(75, 192, 255, 1)',
                'rgba(255, 99, 99, 1)',
                'rgba(54, 162, 54, 1)',
                'rgba(255, 206, 54, 1)',
                'rgba(153, 102, 204, 1)',
                'rgba(255, 159, 255, 1)',
                'rgba(231, 233, 231, 1)'
            ];

            // Task Distribution by Status Chart
            const taskDistributionCtx = document.getElementById('taskDistributionChart').getContext('2d');
            new Chart(taskDistributionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Total Count',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.total_count), 0)
                        ),
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

            // Task Completion Status Chart
            const taskCompletionCtx = document.getElementById('taskCompletionChart').getContext('2d');
            new Chart(taskCompletionCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Task Pending',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_pending), 0)
                        ),
                        backgroundColor: colors[1],
                        borderColor: borders[1],
                        borderWidth: 1
                    }, {
                        label: 'Task Complete',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.task_complete), 0)
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

            // Action Purpose Distribution Chart
            const actionPurposeCtx = document.getElementById('actionPurposeChart').getContext('2d');
            new Chart(actionPurposeCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Action Yes Purpose Yes',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.action_yes_purpose_yes), 0)
                        ),
                        backgroundColor: colors[3],
                        borderColor: borders[3],
                        borderWidth: 1
                    }, {
                        label: 'Action Yes Purpose No',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.action_yes_purpose_no), 0)
                        ),
                        backgroundColor: colors[4],
                        borderColor: borders[4],
                        borderWidth: 1
                    }, {
                        label: 'Action No Purpose No',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.action_no_purpose_no), 0)
                        ),
                        backgroundColor: colors[5],
                        borderColor: borders[5],
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

            // Total Count by User and Status Chart
            const totalCountByUserStatusCtx = document.getElementById('totalCountByUserStatusChart').getContext('2d');
            new Chart(totalCountByUserStatusCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [...new Set(filteredData.map(item => item.status_name))].map((status, index) => ({
                        label: status,
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user && item.status_name === status).reduce((sum, item) => sum + parseInt(item.total_count), 0)
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

            // Stacked Bar Chart 1
            const stackedBarChart1Ctx = document.getElementById('stackedBarChart1').getContext('2d');
            new Chart(stackedBarChart1Ctx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_user_name))],
                    datasets: [{
                        label: 'Total Count',
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user).reduce((sum, item) => sum + parseInt(item.total_count), 0)
                        ),
                        backgroundColor: colors[0],
                        borderColor: borders[0],
                        borderWidth: 1
                    }, {
                        label: 'Task Complete',
                        data: [...new Set(filteredData.map(item => item.task_user_name))].map(user =>
                            filteredData.filter(item => item.task_user_name === user).reduce((sum, item) => sum + parseInt(item.task_complete), 0)
                        ),
                        backgroundColor: colors[1],
                        borderColor: borders[1],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });

            // Stacked Bar Chart 2
            const stackedBarChart2Ctx = document.getElementById('stackedBarChart2').getContext('2d');
            new Chart(stackedBarChart2Ctx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Action Yes Purpose Yes',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.action_yes_purpose_yes), 0)
                        ),
                        backgroundColor: colors[2],
                        borderColor: borders[2],
                        borderWidth: 1
                    }, {
                        label: 'Action Yes Purpose No',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.action_yes_purpose_no), 0)
                        ),
                        backgroundColor: colors[3],
                        borderColor: borders[3],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });

            // Stacked Bar Chart 3
            const stackedBarChart3Ctx = document.getElementById('stackedBarChart3').getContext('2d');
            new Chart(stackedBarChart3Ctx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.task_date))],
                    datasets: [{
                        label: 'Total Count',
                        data: [...new Set(filteredData.map(item => item.task_date))].map(date =>
                            filteredData.filter(item => item.task_date === date).reduce((sum, item) => sum + parseInt(item.total_count), 0)
                        ),
                        backgroundColor: colors[4],
                        borderColor: borders[4],
                        borderWidth: 1
                    }, {
                        label: 'Task Pending',
                        data: [...new Set(filteredData.map(item => item.task_date))].map(date =>
                            filteredData.filter(item => item.task_date === date).reduce((sum, item) => sum + parseInt(item.task_pending), 0)
                        ),
                        backgroundColor: colors[5],
                        borderColor: borders[5],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });

            // Stacked Bar Chart 4
            const stackedBarChart4Ctx = document.getElementById('stackedBarChart4').getContext('2d');
            new Chart(stackedBarChart4Ctx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.user_types))],
                    datasets: [{
                        label: 'Total Count',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.total_count), 0)
                        ),
                        backgroundColor: colors[6],
                        borderColor: borders[6],
                        borderWidth: 1
                    }, {
                        label: 'Task Complete',
                        data: [...new Set(filteredData.map(item => item.user_types))].map(type =>
                            filteredData.filter(item => item.user_types === type).reduce((sum, item) => sum + parseInt(item.task_complete), 0)
                        ),
                        backgroundColor: colors[0],
                        borderColor: borders[0],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });

            // Stacked Bar Chart 5
            const stackedBarChart5Ctx = document.getElementById('stackedBarChart5').getContext('2d');
            new Chart(stackedBarChart5Ctx, {
                type: 'bar',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Total Count',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.total_count), 0)
                        ),
                        backgroundColor: colors[1],
                        borderColor: borders[1],
                        borderWidth: 1
                    }, {
                        label: 'Action No Purpose No',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.action_no_purpose_no), 0)
                        ),
                        backgroundColor: colors[2],
                        borderColor: borders[2],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });

            // Pie Chart 1: Task Distribution by Status
            const pieChart1Ctx = document.getElementById('pieChart1').getContext('2d');
            new Chart(pieChart1Ctx, {
                type: 'pie',
                data: {
                    labels: [...new Set(filteredData.map(item => item.status_name))],
                    datasets: [{
                        label: 'Total Count',
                        data: [...new Set(filteredData.map(item => item.status_name))].map(status =>
                            filteredData.filter(item => item.status_name === status).reduce((sum, item) => sum + parseInt(item.total_count), 0)
                        ),
                        backgroundColor: colors,
                        borderColor: borders,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Pie Chart 2: Action Purpose Distribution
            const pieChart2Ctx = document.getElementById('pieChart2').getContext('2d');
            new Chart(pieChart2Ctx, {
                type: 'pie',
                data: {
                    labels: ['Action Yes Purpose Yes', 'Action Yes Purpose No', 'Action No Purpose No'],
                    datasets: [{
                        label: 'Action Purpose Distribution',
                        data: [
                            filteredData.reduce((sum, item) => sum + parseInt(item.action_yes_purpose_yes), 0),
                            filteredData.reduce((sum, item) => sum + parseInt(item.action_yes_purpose_no), 0),
                            filteredData.reduce((sum, item) => sum + parseInt(item.action_no_purpose_no), 0)
                        ],
                        backgroundColor: colors,
                        borderColor: borders,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
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
            const chartIds = ['taskDistributionChart', 'taskCompletionChart', 'actionPurposeChart', 'totalCountByUserStatusChart', 'stackedBarChart1', 'stackedBarChart2', 'stackedBarChart3', 'stackedBarChart4', 'stackedBarChart5', 'pieChart1', 'pieChart2'];
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
