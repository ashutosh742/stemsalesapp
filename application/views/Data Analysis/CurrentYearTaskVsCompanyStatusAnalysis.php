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
                <label for="totalCountFilter">Total Count Filter:</label>
                <input type="number" class="form-control" id="totalCountFilter" placeholder="Total Count">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="actionYesPurposeYesFilter">Action Yes Purpose Yes Filter:</label>
                <input type="number" class="form-control" id="actionYesPurposeYesFilter" placeholder="Action Yes Purpose Yes">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="actionYesPurposeNoFilter">Action Yes Purpose No Filter:</label>
                <input type="number" class="form-control" id="actionYesPurposeNoFilter" placeholder="Action Yes Purpose No">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="actionNoPurposeNoFilter">Action No Purpose No Filter:</label>
                <input type="number" class="form-control" id="actionNoPurposeNoFilter" placeholder="Action No Purpose No">
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
                Task Distribution by Status
                <canvas id="taskDistributionChart"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                Task Completion Status
                <canvas id="taskCompletionChart"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                Action Purpose Distribution
                <canvas id="actionPurposeChart"></canvas>
            </div>
            <div class="col-md-12 chart-container">
                Total Count by User and Status
                <canvas id="totalCountByUserStatusChart"></canvas>
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
            const totalCountFilter = $('#totalCountFilter').val();
            const actionYesPurposeYesFilter = $('#actionYesPurposeYesFilter').val();
            const actionYesPurposeNoFilter = $('#actionYesPurposeNoFilter').val();
            const actionNoPurposeNoFilter = $('#actionNoPurposeNoFilter').val();

            return data.filter(item => {
                const userMatch = userFilter === "all" || item.task_user_name === userFilter;
                const typeMatch = typeFilter === "all" || item.user_types === typeFilter;
                const statusMatch = statusFilter === "all" || item.status_name === statusFilter;
                const totalCountMatch = totalCountFilter === "" || parseInt(item.total_count) === parseInt(totalCountFilter);
                const actionYesPurposeYesMatch = actionYesPurposeYesFilter === "" || parseInt(item.action_yes_purpose_yes) === parseInt(actionYesPurposeYesFilter);
                const actionYesPurposeNoMatch = actionYesPurposeNoFilter === "" || parseInt(item.action_yes_purpose_no) === parseInt(actionYesPurposeNoFilter);
                const actionNoPurposeNoMatch = actionNoPurposeNoFilter === "" || parseInt(item.action_no_purpose_no) === parseInt(actionNoPurposeNoFilter);

                return userMatch && typeMatch && statusMatch && totalCountMatch && actionYesPurposeYesMatch && actionYesPurposeNoMatch && actionNoPurposeNoMatch;
            });
        }

        function renderCharts(filteredData) {
            // Destroy existing charts before creating new ones
            const charts = [
                Chart.getChart('taskDistributionChart'),
                Chart.getChart('taskCompletionChart'),
                Chart.getChart('actionPurposeChart'),
                Chart.getChart('totalCountByUserStatusChart')
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
            const chartIds = ['taskDistributionChart', 'taskCompletionChart', 'actionPurposeChart', 'totalCountByUserStatusChart'];
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
