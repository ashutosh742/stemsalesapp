<?php
// Convert the PHP array to a JSON string
$jsonData = json_encode($groupedData);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Charts</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .container, .container-fluid {
            margin-top: 30px;
            background-color: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .container:hover, .container-fluid:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .form-control {
            border-radius: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }
        .form-control:focus {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        .row.mb-4 {
            background: beige;
            padding: 10px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .chart-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .chart-title h3 {
            font-weight: bold;
            color: #007bff;
        }
        .btn {
            border-radius: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .table thead th {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
<?php include ("nav.php"); ?>

<div class="container-fluid">
    <div class="text-center">
        <div class="bg-primary">
            <h3 class="p-2 text-white">Task Planner Analysis Of - <?=$cdate;?></h3>
        </div>
    </div>

    <?php
    $cttype = $user['type_id'];
    if($cttype == 2 || $cttype == 19 || $cttype == 20 || $cttype == 21 || $cttype == 22 || $cttype == 23){
       include ("RolesFilter.php");
         } ?>

    <hr>
    <div class="row mb-4" style="background: #b9ffbb;">
        <div class="col-md-3">
            <label for="">Zone</label>
            <select id="zoneFilter" class="form-control">
                <option value="">All</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="">Department</label>
            <select id="userTypeFilter" class="form-control">
                <option value="">All</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="">User Name</label>
            <select id="nameFilter" class="form-control">
                <option value="">All</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="">Task Type</label>
            <select id="taskTypeFilter" class="form-control">
                <option value="">All</option>
            </select>
        </div>
    </div>

    <hr>
    <br>
    <div class="row">
        <div class="col-md-12 text-center">
            <button class="btn btn-success" id="downloadPdf">Download PDF</button>
            <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
        </div>
    </div>
</div>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-6">
        <div class="chart-container">
            <div class="chart-title">
                <h3>Task Type Chart</h3>
                <hr>
            </div>
            <canvas id="taskTypeChart"></canvas>
            </div>
          
        </div>
        <div class="col-md-6">
        <div class="chart-container">
        <div class="chart-title">
                <h3>Total Tasks by User</h3>
                <hr>
            </div>
            <canvas id="userTaskChart"></canvas>
        </div>
          
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="chart-container">
            <div class="chart-title">
                <h3>Total Planned Task Time </h3>
                <hr>
            </div>
            <canvas id="totalPlanTaskTimeChart"></canvas>
            </div>
           
        </div>
        <div class="col-md-6">
        <div class="chart-container">
        <div class="chart-title">
                <h3>Total Review Plan Time</h3>
                <hr>
            </div>
            <canvas id="totalReviewPlanTimeChart"></canvas>
        </div>
           
        </div>
    </div>
</div>

<!-- Add a table to display the JSON data -->
<div class="container-fluid mt-5">
    <h3 class="chart-title">Task Data Table</h3>
    <hr>
    <div class="table-responsive text-nowrap">
        <table id="taskDataTable" class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Task Type</th>
                    <th>User Type</th>
                    <th>Zone</th>
                    <th>Task Count</th>
                    <th>Total Plan Task Time</th>
                    <th>Total Review Plan Time</th>
                </tr>
            </thead>
            <tbody id="taskDataTableBody">
                <!-- Table rows will be populated here -->
            </tbody>
        </table>
    </div>
</div>

<?php include ("footer.php"); ?>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Embed the JSON data into JavaScript
    const data = <?php echo $jsonData; ?>;
    // Function to convert time string to minutes
    function convertTimeToMinutes(timeString) {
        if (!timeString) return 0;
        var timeParts = timeString.split(' ');
        var hours = parseInt(timeParts[0]);
        var minutes = parseInt(timeParts[2]);
        return hours * 60 + minutes;
    }

    // Function to convert minutes to hours and minutes string
    function convertMinutesToTimeString(minutes) {
        var hours = Math.floor(minutes / 60);
        var mins = minutes % 60;
        return hours + " hours " + mins + " minutes";
    }

    // Function to generate random colors
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Function to populate dropdown options
    function populateDropdown(dropdownId, data, key) {
        if (!data) return; // Check if data is not null
        const dropdown = $(dropdownId);
        const uniqueValues = new Set();

        for (const userId in data) {
            data[userId].forEach(task => {
                if (task[key]) {
                    uniqueValues.add(task[key]);
                }
            });
        }

        uniqueValues.forEach(value => {
            dropdown.append(`<option value="${value}">${value}</option>`);
        });
    }

    // Populate dropdowns
    populateDropdown('#nameFilter', data, 'name');
    populateDropdown('#taskTypeFilter', data, 'tasktype');
    populateDropdown('#userTypeFilter', data, 'user_type_name');
    populateDropdown('#zoneFilter', data, 'zone_name');

    // Function to filter data based on selected filters
    function filterData() {
        const nameFilter = $('#nameFilter').val();
        const taskTypeFilter = $('#taskTypeFilter').val();
        const userTypeFilter = $('#userTypeFilter').val();
        const zoneFilter = $('#zoneFilter').val();

        const filteredData = {};

        for (const userId in data) {
            const tasks = data[userId].filter(task => {
                return (!nameFilter || task.name === nameFilter) &&
                       (!taskTypeFilter || task.tasktype === taskTypeFilter) &&
                       (!userTypeFilter || task.user_type_name === userTypeFilter) &&
                       (!zoneFilter || task.zone_name === zoneFilter);
            });

            if (tasks.length > 0) {
                filteredData[userId] = tasks;
            }
        }

        return filteredData;
    }

    // Function to update charts based on filtered data
    function updateCharts(filteredData) {
        console.log("Filtered Data:", filteredData);

        // Prepare data for Task Type Chart
        var taskLabels = [];
        var taskCounts = [];
        var taskBackgroundColors = [];

        for (var userId in filteredData) {
            filteredData[userId].forEach(function(task) {
                if (task.tasktype) {
                    var index = taskLabels.indexOf(task.tasktype);
                    if (index === -1) {
                        taskLabels.push(task.tasktype);
                        taskCounts.push(parseInt(task.task_count));
                        taskBackgroundColors.push(getRandomColor());
                    } else {
                        taskCounts[index] += parseInt(task.task_count);
                    }
                }
            });
        }

        // Update the Task Type Chart
        taskTypeChart.data.labels = taskLabels;
        taskTypeChart.data.datasets[0].data = taskCounts;
        taskTypeChart.data.datasets[0].backgroundColor = taskBackgroundColors;
        taskTypeChart.update();

        // Prepare data for User Task Chart
        var userLabels = [];
        var userTaskCounts = [];
        var userBackgroundColors = [];

        for (var userId in filteredData) {
            var user = filteredData[userId].find(task => task.name)?.name;
            var totalTaskCount = filteredData[userId].reduce(function(sum, task) {
                return sum + parseInt(task.task_count || 0);
            }, 0);

            userLabels.push(user);
            userTaskCounts.push(totalTaskCount);
            userBackgroundColors.push(getRandomColor());
        }

        // Update the User Task Chart
        userTaskChart.data.labels = userLabels;
        userTaskChart.data.datasets[0].data = userTaskCounts;
        userTaskChart.data.datasets[0].backgroundColor = userBackgroundColors;
        userTaskChart.update();

        // Prepare data for Total Planned Task Time Chart
        var planTimeData = {};

        for (var userId in filteredData) {
            var user = filteredData[userId].find(task => task.name)?.name;
            var totalPlanTime = 0;

            // Only consider the first occurrence of total_plan_task_time
            filteredData[userId].some(function(task) {
                if (task.total_plan_task_time) {
                    totalPlanTime = convertTimeToMinutes(task.total_plan_task_time);
                    return true;
                }
                return false;
            });

            if (!planTimeData[user]) {
                planTimeData[user] = totalPlanTime;
            }
        }

        var planTimeLabels = Object.keys(planTimeData);
        var planTimeValues = Object.values(planTimeData);
        var planTimeBackgroundColors = planTimeLabels.map(getRandomColor);

        console.log("Plan Time Data:", planTimeData);

        // Update the Total Planned Task Time Chart
        planTimeChart.data.labels = planTimeLabels;
        planTimeChart.data.datasets[0].data = planTimeValues;
        planTimeChart.data.datasets[0].backgroundColor = planTimeBackgroundColors;
        planTimeChart.update();

        // Prepare data for Total Review Plan Time Chart
        var reviewTimeData = {};

        for (var userId in filteredData) {
            var user = filteredData[userId].find(task => task.name)?.name;
            var totalReviewTime = 0;

            // Only consider the first occurrence of total_review_plan_time
            filteredData[userId].some(function(task) {
                if (task.total_review_plan_time) {
                    totalReviewTime = convertTimeToMinutes(task.total_review_plan_time);
                    return true;
                }
                return false;
            });

            if (!reviewTimeData[user]) {
                reviewTimeData[user] = totalReviewTime;
            }
        }

        var reviewTimeLabels = Object.keys(reviewTimeData);
        var reviewTimeValues = Object.values(reviewTimeData);
        var reviewTimeBackgroundColors = reviewTimeLabels.map(getRandomColor);

        console.log("Review Time Data:", reviewTimeData);

        // Update the Total Review Plan Time Chart
        reviewTimeChart.data.labels = reviewTimeLabels;
        reviewTimeChart.data.datasets[0].data = reviewTimeValues;
        reviewTimeChart.data.datasets[0].backgroundColor = reviewTimeBackgroundColors;
        reviewTimeChart.update();
    }

    // Function to populate the table with data
    function populateTable(filteredData) {
        const tableBody = $('#taskDataTableBody');
        tableBody.empty();

        for (const userId in filteredData) {
            filteredData[userId].forEach(task => {
                const row = `
                    <tr>
                        <td>${task.name}</td>
                        <td>${task.tasktype || ''}</td>
                        <td>${task.user_type_name || ''}</td>
                        <td>${task.zone_name || ''}</td>
                        <td>${task.task_count || ''}</td>
                        <td>${task.total_plan_task_time || ''}</td>
                        <td>${task.total_review_plan_time || ''}</td>
                    </tr>
                `;
                tableBody.append(row);
            });
        }
    }

    // Create the Task Type Chart
    var taskTypeCtx = document.getElementById('taskTypeChart').getContext('2d');
    var taskTypeChart = new Chart(taskTypeCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Task Count',
                data: [],
                backgroundColor: [],
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

    // Create the User Task Chart
    var userTaskCtx = document.getElementById('userTaskChart').getContext('2d');
    var userTaskChart = new Chart(userTaskCtx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Tasks',
                data: [],
                backgroundColor: [],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' tasks';
                        }
                    }
                }
            }
        }
    });

    // Create the Total Planned Task Time Chart
    var planTimeCtx = document.getElementById('totalPlanTaskTimeChart').getContext('2d');
    var planTimeChart = new Chart(planTimeCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Planned Task Time',
                data: [],
                backgroundColor: [],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return convertMinutesToTimeString(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + convertMinutesToTimeString(tooltipItem.raw);
                        }
                    }
                }
            }
        }
    });

    // Create the Total Review Plan Time Chart
    var reviewTimeCtx = document.getElementById('totalReviewPlanTimeChart').getContext('2d');
    var reviewTimeChart = new Chart(reviewTimeCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Review Plan Time',
                data: [],
                backgroundColor: [],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return convertMinutesToTimeString(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + convertMinutesToTimeString(tooltipItem.raw);
                        }
                    }
                }
            }
        }
    });

    // Initial chart update with all data
    const initialFilteredData = filterData();
    updateCharts(initialFilteredData);
    populateTable(initialFilteredData);

    // Initialize DataTable with buttons
    $('#taskDataTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    // Add event listeners to filters
    $('#nameFilter, #taskTypeFilter, #userTypeFilter, #zoneFilter').on('change', function() {
        const filteredData = filterData();
        updateCharts(filteredData);
        populateTable(filteredData);
    });

    $('#downloadPdf').click(function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    html2canvas(document.body, {
        scale: 2, // Increase the scale for better quality
        useCORS: true, // Enable CORS if you have external images
        logging: false // Disable logging for a cleaner console
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 295; // A4 height in mm
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;

        // Add the first page
        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add additional pages if needed
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        doc.save('plannerchart.pdf');
    }).catch(error => {
        console.error('Error generating PDF:', error);
    });
});



    // Download All Charts
    $('#downloadCharts').click(function() {
        const chartIds = ['taskTypeChart', 'userTaskChart', 'totalPlanTaskTimeChart', 'totalReviewPlanTimeChart'];
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
