<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart.js Enhanced Charts with Filters</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
       .chart-container {
            position: relative;
            margin: auto;
            width: 100%;
        }
        .filters {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include("nav.php"); ?>
    <div class="container">
    <hr>
    <div class="card">
        <div class="card-header text-center">
        <h4>User has marked the current year's focus funnels and Todays added them to the planner.</h4>
        </div>
    </div>
    <hr>
        <form class="filters">
            <div class="row">
            <div class="col-md-4"></div>
                <div class="col-md-4">
                    <label for="usernameFilter" class="form-label">Username:</label>
                    <select id="usernameFilter" class="form-control">
                        <option value="">All</option>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>
                <div class="col-md-4"></div>
            </div>
        </form>
        <br>
        <div class="col-md-12 text-center">
            <button class="btn btn-success" id="downloadPdf">Download PDF</button>
            <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
        </div>
        <hr>
        <br>
        <div class="container-fluid">
            <div class="row">
                <!-- Multi-line Chart: User vs Focus Funnels vs Planned Tasks -->
                <div class="col-md-6 chart-container">
                    <div class="chart-box">
                        <canvas id="multiLineChart"></canvas>
                    </div>
                </div>
                <!-- New Line Chart: Focus Funnels vs Planned Tasks by Date -->
                <div class="col-md-6">
                    <div class="chart-box chart-container">
                        <canvas id="focusVsPlannedByDateLineChart"></canvas>
                    </div>
                </div>
                <!-- Bar Chart: User vs Focus Funnels -->
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="userFocusFunnelsBarChart"></canvas>
                    </div>
                </div>
                <!-- Horizontal Bar Chart: User vs Planned Tasks -->
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="userPlannedTasksHorizontalBarChart"></canvas>
                    </div>
                </div>
                <!-- Pie Chart: Focus Funnels Distribution -->
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="focusFunnelsPieChart"></canvas>
                    </div>
                </div>
                <!-- Doughnut Chart: Planned Tasks Distribution -->
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="plannedTasksDoughnutChart"></canvas>
                    </div>
                </div>
                <!-- Radar Chart: User Performance -->
                <div class="col-md-12">
                    <div class="chart-box">
                        <canvas id="userPerformanceRadarChart"></canvas>
                    </div>
                </div>
                <!-- Bubble Chart: Focus Funnels vs Planned Tasks -->
                <div class="col-md-12">
                    <div class="chart-box">
                        <canvas id="focusVsPlannedBubbleChart"></canvas>
                    </div>
                </div>
                <!-- Line Chart: User Index vs Planned Tasks -->
                <div class="col-md-12">
                    <div class="chart-box">
                        <canvas id="userIndexPlannedTasksLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("footer.php"); ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Data from the provided array
        let data = <?php
            echo json_encode($getAllUserData);
        ?>;

        // Color schemes with 100% opacity
        const colors = [
            'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)',
            'rgba(54, 162, 235, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'
        ];

        const borderColors = [
            'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)',
            'rgba(54, 162, 235, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'
        ];

        function populateFilters() {
            // Populate unique usernames
            const usernames = [...new Set(data.map(item => item.username))];
            const usernameFilter = document.getElementById('usernameFilter');
            usernames.forEach(username => {
                const option = document.createElement('option');
                option.value = username;
                option.textContent = username;
                usernameFilter.appendChild(option);
            });
        }

        function applyFilters() {
            const usernameFilter = document.getElementById('usernameFilter').value;

            const filteredData = data.filter(item =>
                (usernameFilter === '' || item.username === usernameFilter)
            );

            updateCharts(filteredData);
        }

        function updateCharts(filteredData) {
            const labels = filteredData.map(item => item.username);
            const focusFunnels = filteredData.map(item => item.current_year_focus_funnels);
            const taskCounts = filteredData.map(item => item.plan_task_count);
            const taskDates = filteredData.map(item => item.task_date);

            // Bar Chart: User vs Focus Funnels
            userFocusFunnelsBarChart.data.labels = labels;
            userFocusFunnelsBarChart.data.datasets[0].data = focusFunnels;
            userFocusFunnelsBarChart.update();

            // Line Chart: User Index vs Planned Tasks
            userIndexPlannedTasksLineChart.data.labels = labels.map((_, index) => index + 1);
            userIndexPlannedTasksLineChart.data.datasets[0].data = taskCounts;
            userIndexPlannedTasksLineChart.update();

            // Pie Chart: Focus Funnels Distribution
            focusFunnelsPieChart.data.labels = labels;
            focusFunnelsPieChart.data.datasets[0].data = focusFunnels;
            focusFunnelsPieChart.update();

            // Doughnut Chart: Planned Tasks Distribution
            plannedTasksDoughnutChart.data.labels = labels;
            plannedTasksDoughnutChart.data.datasets[0].data = taskCounts;
            plannedTasksDoughnutChart.update();

            // Radar Chart: User Performance
            userPerformanceRadarChart.data.labels = labels;
            userPerformanceRadarChart.data.datasets[0].data = focusFunnels;
            userPerformanceRadarChart.data.datasets[1].data = taskCounts;
            userPerformanceRadarChart.update();

            // Bubble Chart: Focus Funnels vs Planned Tasks
            focusVsPlannedBubbleChart.data.datasets[0].data = labels.map((label, index) => ({
                x: focusFunnels[index],
                y: taskCounts[index],
                r: 10
            }));
            focusVsPlannedBubbleChart.update();

            // Horizontal Bar Chart: User vs Planned Tasks
            userPlannedTasksHorizontalBarChart.data.labels = labels;
            userPlannedTasksHorizontalBarChart.data.datasets[0].data = taskCounts;
            userPlannedTasksHorizontalBarChart.update();

            // Multi-line Chart: User vs Focus Funnels vs Planned Tasks
            multiLineChart.data.labels = labels;
            multiLineChart.data.datasets[0].data = focusFunnels;
            multiLineChart.data.datasets[1].data = taskCounts;
            multiLineChart.update();

            // New Line Chart: Focus Funnels vs Planned Tasks by Date
            focusVsPlannedByDateLineChart.data.labels = taskDates;
            focusVsPlannedByDateLineChart.data.datasets[0].data = focusFunnels;
            focusVsPlannedByDateLineChart.data.datasets[1].data = taskCounts;
            focusVsPlannedByDateLineChart.update();
        }

        // Initialize charts
        const userFocusFunnelsBarChart = new Chart(document.getElementById('userFocusFunnelsBarChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Current Year Focus Funnels', data: [], backgroundColor: colors[0], borderColor: borderColors[0], borderWidth: 1 }] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const userIndexPlannedTasksLineChart = new Chart(document.getElementById('userIndexPlannedTasksLineChart'), {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Planned Task Count', data: [], borderColor: borderColors[1], fill: false }] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const focusFunnelsPieChart = new Chart(document.getElementById('focusFunnelsPieChart'), {
            type: 'pie',
            data: { labels: [], datasets: [{ label: 'Current Year Focus Funnels', data: [], backgroundColor: colors }] },
            options: { responsive: true }
        });

        const plannedTasksDoughnutChart = new Chart(document.getElementById('plannedTasksDoughnutChart'), {
            type: 'doughnut',
            data: { labels: [], datasets: [{ label: 'Planned Task Count', data: [], backgroundColor: colors }] },
            options: { responsive: true }
        });

        const userPerformanceRadarChart = new Chart(document.getElementById('userPerformanceRadarChart'), {
            type: 'radar',
            data: { labels: [], datasets: [{ label: 'Focus Funnels', data: [], backgroundColor: colors[2], borderColor: borderColors[2], borderWidth: 1 }, { label: 'Planned Tasks', data: [], backgroundColor: colors[3], borderColor: borderColors[3], borderWidth: 1 }] },
            options: { responsive: true }
        });

        const focusVsPlannedBubbleChart = new Chart(document.getElementById('focusVsPlannedBubbleChart'), {
            type: 'bubble',
            data: { datasets: [{ label: 'Focus Funnels vs Planned Tasks', data: [], backgroundColor: colors[4], borderColor: borderColors[4], borderWidth: 1 }] },
            options: { responsive: true }
        });

        const userPlannedTasksHorizontalBarChart = new Chart(document.getElementById('userPlannedTasksHorizontalBarChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Planned Task Count', data: [], backgroundColor: colors[5], borderColor: borderColors[5], borderWidth: 1 }] },
            options: { indexAxis: 'y', responsive: true, scales: { x: { beginAtZero: true } } }
        });

        const multiLineChart = new Chart(document.getElementById('multiLineChart'), {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Current Year Focus Funnels', data: [], borderColor: borderColors[0], fill: false }, { label: 'Planned Task Count', data: [], borderColor: borderColors[1], fill: false }] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const focusVsPlannedByDateLineChart = new Chart(document.getElementById('focusVsPlannedByDateLineChart'), {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Current Year Focus Funnels', data: [], borderColor: borderColors[0], fill: false }, { label: 'Planned Task Count', data: [], borderColor: borderColors[1], fill: false }] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Populate filters and initial chart update
        populateFilters();
        updateCharts(data);

        // Add event listeners to filter inputs
        document.getElementById('usernameFilter').addEventListener('change', applyFilters);

        $(document).ready(function() {
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
                const chartIds = ['multiLineChart', 'focusVsPlannedByDateLineChart', 'userFocusFunnelsBarChart', 'userIndexPlannedTasksLineChart', 'focusFunnelsPieChart', 'plannedTasksDoughnutChart', 'userPerformanceRadarChart', 'focusVsPlannedBubbleChart', 'userPlannedTasksHorizontalBarChart'];
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
