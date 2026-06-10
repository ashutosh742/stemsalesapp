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

<?php include ("nav.php"); ?>

<?php
// Ensure $getAllUserData is defined before using it
$getAllUserData = isset($getAllUserData) ? $getAllUserData : [];
$usernames = array_unique(array_column($getAllUserData, 'name'));
$type_names = array_unique(array_column($getAllUserData, 'type_name'));
$userworkfroms = array_unique(array_column($getAllUserData, 'userworkfrom'));

?>

    <div class="container">
       <hr>
       <h1 class="text-center my-4">Interactive Dashboard</h1>
       <hr>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nameFilter">Name Filter:</label>
                    <select class="form-control" id="nameFilter">
                        <option value="all">All</option>
                        <?php foreach ($usernames as $username) { echo "<option value='$username'>$username</option>"; } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="typeFilter">Type Filter:</label>
                    <select class="form-control" id="typeFilter">
                        <option value="all">All</option>
                        <?php foreach ($type_names as $type_name) { echo "<option value='$type_name'>$type_name</option>"; } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="workFromFilter">Work From Filter:</label>
                    <select class="form-control" id="workFromFilter">
                        <option value="all">All</option>
                        <?php foreach ($userworkfroms as $userworkfrom) { echo "<option value='$userworkfrom'>$userworkfrom</option>"; } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="taskPlanFilter">Task Plan for Today Request:</label>
                    <select class="form-control" id="taskPlanFilter">
                        <option value="all">All</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="leaveRequestFilter">Special Request for Leave:</label>
                    <select class="form-control" id="leaveRequestFilter">
                        <option value="all">All</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="reminderRequestFilter">Request Any Reminder:</label>
                    <select class="form-control" id="reminderRequestFilter">
                        <option value="all">All</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="pendingTaskPlannerFilter">Pending Task Planner Request:</label>
                    <select class="form-control" id="pendingTaskPlannerFilter">
                        <option value="all">All</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
        </div>


        <div class="col-md-12 text-center">
                    <button class="btn btn-success" id="downloadPdf">Download PDF</button>
                    <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
                </div>
    <hr>
        </div>

        <hr>
        <br>

        <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 chart-container">
                Total Tasks by User
                <canvas id="totalTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Total Pending Tasks by User
                <canvas id="pendingTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Total Completed Tasks by User
                <canvas id="completedTasksChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4 chart-container">
                Total Task
                <canvas id="pieTotalTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Pending Task
                <canvas id="piePendingTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Completed Task
                <canvas id="pieCompletedTasksChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4 chart-container">
                Total Task
                <canvas id="doughnutTotalTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Pending Task
                <canvas id="doughnutPendingTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Completed Task
                <canvas id="doughnutCompletedTasksChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4 chart-container">
                Total Task
                <canvas id="radarTotalTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Total Pending Task
                <canvas id="radarPendingTasksChart"></canvas>
            </div>
            <div class="col-md-4 chart-container">
                Total Completed Task
                <canvas id="radarCompletedTasksChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6 chart-container">
                Total Task
                <canvas id="polarAreaTotalTasksChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                Total Pending Task
                <canvas id="polarAreaPendingTasksChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                Total Completed Task
                <canvas id="polarAreaCompletedTasksChart"></canvas>
            </div>
        </div>
    </div>
    <?php include ("footer.php"); ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php
    $getAllUserDatajson =  json_encode($getAllUserData);
    ?>

    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            const data = <?=$getAllUserDatajson?>;

            function filterData() {
                const nameFilter = $('#nameFilter').val();
                const typeFilter = $('#typeFilter').val();
                const workFromFilter = $('#workFromFilter').val();
                const taskPlanFilter = $('#taskPlanFilter').val();
                const leaveRequestFilter = $('#leaveRequestFilter').val();
                const reminderRequestFilter = $('#reminderRequestFilter').val();
                const pendingTaskPlannerFilter = $('#pendingTaskPlannerFilter').val();

                return data.filter(item => {
                    const nameMatch = nameFilter === "all" || item.name === nameFilter;
                    const typeMatch = typeFilter === "all" || item.type_name === typeFilter;
                    const workFromMatch = workFromFilter === "all" || item.userworkfrom === workFromFilter;
                    const taskPlanMatch = taskPlanFilter === "all" || item.task_plan_for_today_request === taskPlanFilter;
                    const leaveRequestMatch = leaveRequestFilter === "all" || item.user_create_special_request_for_leave === leaveRequestFilter;
                    const reminderRequestMatch = reminderRequestFilter === "all" || item.user_request_any_reminder === reminderRequestFilter;
                    const pendingTaskPlannerMatch = pendingTaskPlannerFilter === "all" || item.pending_task_planner_request === pendingTaskPlannerFilter;

                    return nameMatch && typeMatch && workFromMatch && taskPlanMatch && leaveRequestMatch && reminderRequestMatch && pendingTaskPlannerMatch;
                });
            }

            function renderCharts(filteredData) {
                // Destroy existing charts before creating new ones
                const charts = [
                    Chart.getChart('totalTasksChart'),
                    Chart.getChart('pendingTasksChart'),
                    Chart.getChart('completedTasksChart'),
                    Chart.getChart('pieTotalTasksChart'),
                    Chart.getChart('piePendingTasksChart'),
                    Chart.getChart('pieCompletedTasksChart'),
                    Chart.getChart('doughnutTotalTasksChart'),
                    Chart.getChart('doughnutPendingTasksChart'),
                    Chart.getChart('doughnutCompletedTasksChart'),
                    Chart.getChart('radarTotalTasksChart'),
                    Chart.getChart('radarPendingTasksChart'),
                    Chart.getChart('radarCompletedTasksChart'),
                    Chart.getChart('polarAreaTotalTasksChart'),
                    Chart.getChart('polarAreaPendingTasksChart'),
                    Chart.getChart('polarAreaCompletedTasksChart')
                ];

                charts.forEach(chart => chart && chart.destroy());

                // Total Tasks by User Chart
                const totalTasksCtx = document.getElementById('totalTasksChart').getContext('2d');
                new Chart(totalTasksCtx, {
                    type: 'bar',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Total Tasks',
                            data: filteredData.map(item => item.total_task),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
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

                // Total Pending Tasks by User Chart
                const pendingTasksCtx = document.getElementById('pendingTasksChart').getContext('2d');
                new Chart(pendingTasksCtx, {
                    type: 'bar',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Pending Tasks',
                            data: filteredData.map(item => item.pending_task),
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
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

                // Total Completed Tasks by User Chart
                const completedTasksCtx = document.getElementById('completedTasksChart').getContext('2d');
                new Chart(completedTasksCtx, {
                    type: 'bar',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Completed Tasks',
                            data: filteredData.map(item => item.complete_task),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
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

                // Pie Charts
                const pieTotalTasksCtx = document.getElementById('pieTotalTasksChart').getContext('2d');
                new Chart(pieTotalTasksCtx, {
                    type: 'pie',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Total Tasks',
                            data: filteredData.map(item => item.total_task),
                            backgroundColor: ['#FF6384', '#36A2EB'],
                        }]
                    }
                });

                const piePendingTasksCtx = document.getElementById('piePendingTasksChart').getContext('2d');
                new Chart(piePendingTasksCtx, {
                    type: 'pie',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Pending Tasks',
                            data: filteredData.map(item => item.pending_task),
                            backgroundColor: ['#FFCE56', '#4BC0C0'],
                        }]
                    }
                });

                const pieCompletedTasksCtx = document.getElementById('pieCompletedTasksChart').getContext('2d');
                new Chart(pieCompletedTasksCtx, {
                    type: 'pie',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Completed Tasks',
                            data: filteredData.map(item => item.complete_task),
                            backgroundColor: ['#FF6384', '#36A2EB'],
                        }]
                    }
                });

                // Doughnut Charts
                const doughnutTotalTasksCtx = document.getElementById('doughnutTotalTasksChart').getContext('2d');
                new Chart(doughnutTotalTasksCtx, {
                    type: 'doughnut',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Total Tasks',
                            data: filteredData.map(item => item.total_task),
                            backgroundColor: ['#FFCE56', '#4BC0C0'],
                        }]
                    }
                });

                const doughnutPendingTasksCtx = document.getElementById('doughnutPendingTasksChart').getContext('2d');
                new Chart(doughnutPendingTasksCtx, {
                    type: 'doughnut',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Pending Tasks',
                            data: filteredData.map(item => item.pending_task),
                            backgroundColor: ['#FFCE56', '#4BC0C0'],
                        }]
                    }
                });

                const doughnutCompletedTasksCtx = document.getElementById('doughnutCompletedTasksChart').getContext('2d');
                new Chart(doughnutCompletedTasksCtx, {
                    type: 'doughnut',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Completed Tasks',
                            data: filteredData.map(item => item.complete_task),
                            backgroundColor: ['#FFCE56', '#4BC0C0'],
                        }]
                    }
                });

                // Radar Charts
                const radarTotalTasksCtx = document.getElementById('radarTotalTasksChart').getContext('2d');
                new Chart(radarTotalTasksCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Total Tasks', 'Pending Tasks', 'Approved Tasks'],
                        datasets: filteredData.map(item => ({
                            label: item.name,
                            data: [item.total_task, item.pending_task, item.approved_task],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }))
                    },
                    options: {
                        scales: {
                            r: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                const radarPendingTasksCtx = document.getElementById('radarPendingTasksChart').getContext('2d');
                new Chart(radarPendingTasksCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Pending Tasks'],
                        datasets: filteredData.map(item => ({
                            label: item.name,
                            data: [item.pending_task],
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }))
                    },
                    options: {
                        scales: {
                            r: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                const radarCompletedTasksCtx = document.getElementById('radarCompletedTasksChart').getContext('2d');
                new Chart(radarCompletedTasksCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Completed Tasks'],
                        datasets: filteredData.map(item => ({
                            label: item.name,
                            data: [item.complete_task],
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }))
                    },
                    options: {
                        scales: {
                            r: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Polar Area Charts
                const polarAreaTotalTasksCtx = document.getElementById('polarAreaTotalTasksChart').getContext('2d');
                new Chart(polarAreaTotalTasksCtx, {
                    type: 'polarArea',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Total Tasks',
                            data: filteredData.map(item => item.total_task),
                            backgroundColor: ['#FF6384', '#36A2EB'],
                        }]
                    }
                });

                const polarAreaPendingTasksCtx = document.getElementById('polarAreaPendingTasksChart').getContext('2d');
                new Chart(polarAreaPendingTasksCtx, {
                    type: 'polarArea',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Pending Tasks',
                            data: filteredData.map(item => item.pending_task),
                            backgroundColor: ['#FFCE56', '#4BC0C0'],
                        }]
                    }
                });

                const polarAreaCompletedTasksCtx = document.getElementById('polarAreaCompletedTasksChart').getContext('2d');
                new Chart(polarAreaCompletedTasksCtx, {
                    type: 'polarArea',
                    data: {
                        labels: filteredData.map(item => item.name),
                        datasets: [{
                            label: 'Completed Tasks',
                            data: filteredData.map(item => item.complete_task),
                            backgroundColor: ['#FF6384', '#36A2EB'],
                        }]
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
            const chartIds = ['totalTasksChart', 'pendingTasksChart', 'completedTasksChart', 'pieTotalTasksChart', 'piePendingTasksChart', 'pieCompletedTasksChart', 'doughnutTotalTasksChart', 'doughnutPendingTasksChart', 'doughnutCompletedTasksChart', 'radarTotalTasksChart', 'radarPendingTasksChart', 'radarCompletedTasksChart', 'polarAreaTotalTasksChart', 'polarAreaPendingTasksChart', 'polarAreaCompletedTasksChart'
        ];
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
