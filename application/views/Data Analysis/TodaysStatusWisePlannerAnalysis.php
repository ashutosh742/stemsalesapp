<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
        /* #taskTypeChart{
            width: 450px!important;
            height: 450px!important;
        } */
    </style>
</head>
<body>

<?php include ("nav.php"); ?>


    <div class="container mt-5">
<hr>
        


<?php 

if($filter_types == 'CurrentDate'){
        $message = 'Todays';
    }else if($filter_types == 'BETWEEN7Days'){
        $message = 'BETWEEN 7 Days';
    }else if($filter_types == 'BETWEEN1Month'){
        $message = 'BETWEEN 1 Month';
    }else if($filter_types == 'BETWEEN3Month'){
        $message = 'BETWEEN 3 Month';
    }else if ($filter_types == 'BETWEEN6Month') {
        $message = 'BETWEEN 6 Month';
    }
    else if ($filter_types == 'BETWEEN12Month') {
        $message = 'BETWEEN 12 Month';
       
    }
    ?>

      <div class="row">
            <div class="col-md-9">
                <div class="text-center p-2">
                    <h2><?=$message;?> Status Wise Planner Analysis</h2>
                </div>
            </div>
            <div class="col-md-3">

           

                <form class="form-inline" method="POST" action="<?= base_url().'SalesGraph/TodaysStatusWisePlannerAnalysis' ?>">
                    <div class="form-group">
                        <label for="taskUserNameFilter">Filter BY &nbsp;</label>
                        <select name="filter_types_option" class="form-control">
                        <option value="" <?= ($filter_types == '') ? 'selected' : '' ?>>Select</option>
                            <option value="CurrentDate" <?= ($filter_types == 'CurrentDate') ? 'selected' : '' ?>>Todays</option>
                            <option value="BETWEEN7Days" <?= ($filter_types == 'BETWEEN7Days') ? 'selected' : '' ?>>BETWEEN 7 Days</option>
                            <option value="BETWEEN1Month" <?= ($filter_types == 'BETWEEN1Month') ? 'selected' : '' ?>>BETWEEN 1 Month</option>
                            <option value="BETWEEN3Month" <?= ($filter_types == 'BETWEEN3Month') ? 'selected' : '' ?>>BETWEEN 3 Month</option>
                        </select> &nbsp;
                        <button type="submit" class="btn btn-primary mt-1">Filter</button>
                    </div>
                </form>

            </div>
        </div>
<hr>
<br>

        <!-- $filter_types       = 'CurrentDate';
        // $filter_types       = 'BETWEEN7Days';
        // $filter_types       = 'BETWEEN1Month';
        // $filter_types       = 'BETWEEN3Month';
        // $filter_types       = 'BETWEEN6Month';
        // $filter_types       = 'BETWEEN12Month'; -->



        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="taskUserNameFilter">Task User Name</label>
                    <select class="form-control" id="taskUserNameFilter"></select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="userTypesFilter">User Types</label>
                    <select class="form-control" id="userTypesFilter"></select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="statusNameFilter">Status Name</label>
                    <select class="form-control" id="statusNameFilter"></select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="taskNameFilter">Task Name</label>
                    <select class="form-control" id="taskNameFilter"></select>
                </div>
            </div>
        </div>


        <br>
      <div class="row">
        <div class="col-md-12 text-center">
          <button class="btn btn-success" id="downloadPdf">Download PDF</button>
          <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
        </div>
        <br>
      </div>

<hr>

        </div>




        <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-6">
                <canvas id="taskStatusChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="userTaskStatusChart"></canvas>
            </div>
           
        </div>
        </div>
        <hr>
        <div class="container-fluid mt-5">
        <div class="row mt-3">
        <div class="col-md-6">
                <canvas id="taskTypeChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="taskCompletionChart"></canvas>
            </div>
           
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <?php include ("footer.php"); ?>

    <script>
    $(document).ready(function() {
        const data = <?php echo json_encode($getAllUserData); ?>;

        // Convert string numbers to actual numbers
        data.forEach(item => {
            item.total_count = parseInt(item.total_count);
            item.task_pending = parseInt(item.task_pending);
            item.task_complete = parseInt(item.task_complete);
        });

        // Populate filter options
        populateFilterOptions(data, 'taskUserNameFilter', 'task_user_name');
        populateFilterOptions(data, 'userTypesFilter', 'user_types');
        populateFilterOptions(data, 'statusNameFilter', 'status_name');
        populateFilterOptions(data, 'taskNameFilter', 'task_name');

        // Create charts
        let taskStatusChart = createChart('taskStatusChart', 'bar', 'Task Status');
        let taskTypeChart = createChart('taskTypeChart', 'pie', 'Task Types');
        let taskCompletionChart = createChart('taskCompletionChart', 'doughnut', 'Task Completion');
        let userTaskStatusChart = createChart('userTaskStatusChart', 'bar', 'User Task Status');

        updateCharts(data);

        // Filter change handler
        $('select').change(function() {
            const filters = {
                task_user_name: $('#taskUserNameFilter').val(),
                user_types: $('#userTypesFilter').val(),
                status_name: $('#statusNameFilter').val(),
                task_name: $('#taskNameFilter').val()
            };

            const filteredData = data.filter(item => {
                return (!filters.task_user_name || item.task_user_name === filters.task_user_name) &&
                       (!filters.user_types || item.user_types === filters.user_types) &&
                       (!filters.status_name || item.status_name === filters.status_name) &&
                       (!filters.task_name || item.task_name === filters.task_name);
            });

            updateCharts(filteredData);
        });

        function populateFilterOptions(data, selectId, field) {
            const uniqueValues = [...new Set(data.map(item => item[field]))];
            const select = $(`#${selectId}`);
            select.append('<option value="">All</option>');
            uniqueValues.forEach(value => {
                select.append(`<option value="${value}">${value}</option>`);
            });
        }

        function createChart(canvasId, type, label) {
            const ctx = $(`#${canvasId}`)[0].getContext('2d');
            return new Chart(ctx, {
                type: type,
                data: {
                    labels: [],
                    datasets: [{
                        label: label,
                        data: [],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: type === 'bar' || type === 'line' ? {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: type === 'line' ? 'Date' : 'Category'
                            }
                        }
                    } : {}
                }
            });
        }

        function updateCharts(filteredData) {
            // Status Chart
            const statusCounts = {};
            filteredData.forEach(item => {
                statusCounts[item.status_name] = (statusCounts[item.status_name] || 0) + item.total_count;
            });
            taskStatusChart.data.labels = Object.keys(statusCounts);
            taskStatusChart.data.datasets[0].data = Object.values(statusCounts);
            taskStatusChart.update();

            // Task Type Chart
            const taskTypeCounts = {};
            filteredData.forEach(item => {
                taskTypeCounts[item.task_name] = (taskTypeCounts[item.task_name] || 0) + item.total_count;
            });
            taskTypeChart.data.labels = Object.keys(taskTypeCounts);
            taskTypeChart.data.datasets[0].data = Object.values(taskTypeCounts);
            taskTypeChart.update();

            // Completion Chart
            const completionCounts = {
                'Completed': filteredData.reduce((sum, item) => sum + item.task_complete, 0),
                'Pending': filteredData.reduce((sum, item) => sum + item.task_pending, 0)
            };
            taskCompletionChart.data.labels = Object.keys(completionCounts);
            taskCompletionChart.data.datasets[0].data = Object.values(completionCounts);
            taskCompletionChart.update();

            // User Task Status Chart
            const userTaskStatusCounts = {};
            filteredData.forEach(item => {
                const key = `${item.task_user_name} > ${item.status_name} > ${item.task_name}`;
                userTaskStatusCounts[key] = (userTaskStatusCounts[key] || 0) + item.total_count;
            });
            userTaskStatusChart.data.labels = Object.keys(userTaskStatusCounts);
            userTaskStatusChart.data.datasets[0].data = Object.values(userTaskStatusCounts);
            userTaskStatusChart.update();
        }




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
            const chartIds = ['taskStatusChart', 'userTaskStatusChart', 'taskTypeChart', 'taskCompletionChart'];
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
