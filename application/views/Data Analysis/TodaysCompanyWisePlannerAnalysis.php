<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php
    $filter_types = isset($_POST['filter_types_option']) ? $_POST['filter_types_option'] : '';
    if($filter_types == 'CurrentDate'){
        $message = 'Todays';
    } else if($filter_types == 'BETWEEN7Days'){
        $message = 'BETWEEN 7 Days';
    } else if($filter_types == 'BETWEEN1Month'){
        $message = 'BETWEEN 1 Month';
    } else if($filter_types == 'BETWEEN3Month'){
        $message = 'BETWEEN 3 Month';
    } else if ($filter_types == 'BETWEEN6Month') {
        $message = 'BETWEEN 6 Month';
    } else if ($filter_types == 'BETWEEN12Month') {
        $message = 'BETWEEN 12 Month';
    }
?>

<?php include ("nav.php"); ?>

<div class="container mt-5">
    <hr>
    <div class="row">
        <div class="col-md-9">
            <div class="text-center p-2">
                <h3><?= $message; ?> Company Name Wise Task Analysis Dashboard</h3>
            </div>
        </div>

        <div class="col-md-3">
            <form class="form-inline" method="POST" action="<?= base_url().'SalesGraph/TodaysCompanyWisePlannerAnalysis' ?>">
                <div class="form-group">
                    <label for="taskUserNameFilter">Filter BY &nbsp;</label>
                    <select name="filter_types_option" class="form-control">
                        <option value="" <?= ($filter_types == '') ? 'selected' : '' ?>>Select</option>
                        <option value="CurrentDate" <?= ($filter_types == 'CurrentDate') ? 'selected' : '' ?>>Todays</option>
                        <option value="BETWEEN7Days" <?= ($filter_types == 'BETWEEN7Days') ? 'selected' : '' ?>>BETWEEN 7 Days</option>
                        <option value="BETWEEN1Month" <?= ($filter_types == 'BETWEEN1Month') ? 'selected' : '' ?>>BETWEEN 1 Month</option>
                        <option value="BETWEEN3Month" <?= ($filter_types == 'BETWEEN3Month') ? 'selected' : '' ?>>BETWEEN 3 Month</option>
                    </select>
                    &nbsp;
                    <button type="submit" class="btn btn-primary mt-1">Filter</button>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="taskUserNameFilter">Task User Name</label>
                <select class="form-control filter" id="taskUserNameFilter"></select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="userTypesFilter">User Types</label>
                <select class="form-control filter" id="userTypesFilter"></select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="statusNameFilter">Status Name</label>
                <select class="form-control filter" id="statusNameFilter"></select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="taskNameFilter">Task Name</label>
                <select class="form-control filter" id="taskNameFilter"></select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="companyNameFilter">Company Name</label>
                <select class="form-control filter" id="companyNameFilter"></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="taskDateFilter">Task Date</label>
                <select class="form-control filter" id="taskDateFilter"></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="actionTakenFilter">Action Taken</label>
                <select class="form-control filter" id="actionTakenFilter"></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="purposeAchievedFilter">Purpose Achieved</label>
                <select class="form-control filter" id="purposeAchievedFilter"></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="taskStatusFilter">Task Status</label>
                <select class="form-control filter" id="taskStatusFilter"></select>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <canvas id="taskStatusChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="taskTypeChart"></canvas>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <canvas id="taskUserNameChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="companyNameChart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <canvas id="actionTakenChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="purposeAchievedChart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="taskStatusOverTimeChart"></canvas>
        </div>
        <div class="col-md-12">
            <canvas id="taskDateChart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="totalTaskByUserChart"></canvas>
        </div>
        <div class="col-md-12">
            <canvas id="totalTaskByCompanyNameChart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="totalTaskByCompanyNameWithUserNameChart"></canvas>
        </div>
        <div class="col-md-12">
            <canvas id="totalTaskByCompanyWithTaskNameChart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="totalTaskByUserWithCompanyNameChart"></canvas>
        </div>
        <div class="col-md-12">
            <canvas id="totalTaskByCompanyNameWithUserName2Chart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="totalTaskActionYesPurposeYesChart"></canvas>
        </div>
        <div class="col-md-12">
            <canvas id="totalTaskActionYesPurposeNoChart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="totalTaskActionNoPurposeNoChart"></canvas>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <canvas id="taskByTimeSlotWithTaskNameChart"></canvas>
        </div>
        <div class="col-md-12">
            <canvas id="taskByTimeWithTaskNameChart"></canvas>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <canvas id="taskUserDateStatusChart"></canvas>
        </div>
    </div>
</div>
<?php include ("footer.php"); ?>
<script>
    $(document).ready(function() {
        const data = <?php echo json_encode($getAllUserData); ?>;
        // Populate filter options
        populateFilterOptions(data, 'taskUserNameFilter', 'task_user_name');
        populateFilterOptions(data, 'userTypesFilter', 'user_types');
        populateFilterOptions(data, 'statusNameFilter', 'status_name');
        populateFilterOptions(data, 'taskNameFilter', 'task_name');
        populateFilterOptions(data, 'companyNameFilter', 'company_name');
        populateFilterOptions(data, 'actionTakenFilter', 'action_taken');
        populateFilterOptions(data, 'purposeAchievedFilter', 'purpose_achieved');
        populateFilterOptions(data, 'taskStatusFilter', 'task_status');
        populateDateFilterOptions(data, 'taskDateFilter', 'task_date_time');

        // Create charts
        let taskStatusChart = createChart('taskStatusChart', 'bar', 'Task Status');
        let taskTypeChart = createChart('taskTypeChart', 'pie', 'Task Types');
        let taskUserDateStatusChart = createChart('taskUserDateStatusChart', 'bar', 'Task User Date Status');
        let taskUserNameChart = createChart('taskUserNameChart', 'bar', 'Task User Name');
        let companyNameChart = createChart('companyNameChart', 'bar', 'Company Name');
        let actionTakenChart = createChart('actionTakenChart', 'bar', 'Action Taken');
        let purposeAchievedChart = createChart('purposeAchievedChart', 'bar', 'Purpose Achieved');
        let taskStatusOverTimeChart = createChart('taskStatusOverTimeChart', 'bar', 'Task Status Over Time');
        let taskDateChart = createChart('taskDateChart', 'bar', 'Task Date');

        // New charts
        let totalTaskByUserChart = createChart('totalTaskByUserChart', 'bar', 'Total Task by User');
        let totalTaskByCompanyNameChart = createChart('totalTaskByCompanyNameChart', 'bar', 'Total Task by Company Name');
        let totalTaskByCompanyNameWithUserNameChart = createChart('totalTaskByCompanyNameWithUserNameChart', 'bar', 'Total Task by Company Name with User Name');
        let totalTaskByCompanyWithTaskNameChart = createChart('totalTaskByCompanyWithTaskNameChart', 'bar', 'Total Task by Company with Task Name');
        let totalTaskByUserWithCompanyNameChart = createChart('totalTaskByUserWithCompanyNameChart', 'bar', 'Total Task by User with Company Name');
        let totalTaskByCompanyNameWithUserName2Chart = createChart('totalTaskByCompanyNameWithUserName2Chart', 'bar', 'Total Task by Company Name with User Name');
        let totalTaskActionYesPurposeYesChart = createChart('totalTaskActionYesPurposeYesChart', 'bar', 'Total Task with Action Taken Yes and Purpose Achieved Yes');
        let totalTaskActionYesPurposeNoChart = createChart('totalTaskActionYesPurposeNoChart', 'bar', 'Total Task with Action Taken Yes and Purpose Achieved No');
        let totalTaskActionNoPurposeNoChart = createChart('totalTaskActionNoPurposeNoChart', 'bar', 'Total Task with Action Taken No and Purpose Achieved No');

        // New charts for time slots
        let taskByTimeSlotWithTaskNameChart = createChart('taskByTimeSlotWithTaskNameChart', 'bar', 'Task by Time Slot with Task Name');
        let taskByTimeWithTaskNameChart = createChart('taskByTimeWithTaskNameChart', 'bar', 'Task by Time with Task Name');

        updateCharts(data);

        // Filter change handler
        $('.filter').change(function() {
            const filters = {
                task_user_name: $('#taskUserNameFilter').val(),
                user_types: $('#userTypesFilter').val(),
                status_name: $('#statusNameFilter').val(),
                task_name: $('#taskNameFilter').val(),
                company_name: $('#companyNameFilter').val(),
                task_date: $('#taskDateFilter').val(),
                action_taken: $('#actionTakenFilter').val(),
                purpose_achieved: $('#purposeAchievedFilter').val(),
                task_status: $('#taskStatusFilter').val()
            };

            console.log('Filters:', filters);

            const filteredData = data.filter(item => {
                const taskDate = item.task_date_time ? item.task_date_time.split('T')[0] : null; // Extract date part
                return (!filters.task_user_name || item.task_user_name === filters.task_user_name) &&
                       (!filters.user_types || item.user_types === filters.user_types) &&
                       (!filters.status_name || item.status_name === filters.status_name) &&
                       (!filters.task_name || item.task_name === filters.task_name) &&
                       (!filters.company_name || item.company_name === filters.company_name) &&
                       (!filters.task_date || taskDate === filters.task_date) &&
                       (!filters.action_taken || item.action_taken === filters.action_taken) &&
                       (!filters.purpose_achieved || item.purpose_achieved === filters.purpose_achieved) &&
                       (!filters.task_status || item.task_status === filters.task_status);
            });

            console.log('Filtered Data:', filteredData);

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

        function populateDateFilterOptions(data, selectId, field) {
            const uniqueDates = [...new Set(data.map(item => item[field] ? item[field].split('T')[0] : null))];
            const select = $(`#${selectId}`);
            select.append('<option value="">All</option>');
            uniqueDates.forEach(date => {
                if (date) {
                    select.append(`<option value="${date}">${date}</option>`);
                }
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
                    scales: type === 'bar' ? {
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
                                text: 'Category'
                            }
                        }
                    } : {}
                }
            });
        }

      
        function getTimeSlot(dateTime) {
    if (!dateTime) return 'Unknown';

    const date = new Date(dateTime);
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const period = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = hours % 12 || 12; // Convert to 12-hour format
    return `${formattedHours}:${minutes < 10 ? '0' : ''}${minutes} ${period}`;
}

function updateCharts(filteredData) {
    console.log('Updating charts with filtered data:', filteredData);

    // Status Chart
    const statusCounts = {};
    filteredData.forEach(item => {
        if (item.status_name) {
            statusCounts[item.status_name] = (statusCounts[item.status_name] || 0) + 1;
        }
    });
    taskStatusChart.data.labels = Object.keys(statusCounts);
    taskStatusChart.data.datasets[0].data = Object.values(statusCounts);
    taskStatusChart.update();

    // Task Type Chart
    const taskTypeCounts = {};
    filteredData.forEach(item => {
        if (item.task_name) {
            taskTypeCounts[item.task_name] = (taskTypeCounts[item.task_name] || 0) + 1;
        }
    });
    taskTypeChart.data.labels = Object.keys(taskTypeCounts);
    taskTypeChart.data.datasets[0].data = Object.values(taskTypeCounts);
    taskTypeChart.update();

    // Task User Date Status Chart
    const userDateStatusCounts = {};
    filteredData.forEach(item => {
        if (item.task_user_name && item.task_date_time && item.status_name && item.task_name) {
            const taskDate = item.task_date_time.split('T')[0]; // Safeguard: Ensure task_date_time is defined
            const key = `${item.task_user_name} > ${taskDate} > ${item.status_name} > ${item.task_name}`;
            userDateStatusCounts[key] = (userDateStatusCounts[key] || 0) + 1;
        }
    });
    taskUserDateStatusChart.data.labels = Object.keys(userDateStatusCounts);
    taskUserDateStatusChart.data.datasets[0].data = Object.values(userDateStatusCounts);
    taskUserDateStatusChart.update();

    // Task User Name Chart
    const taskUserNameCounts = {};
    filteredData.forEach(item => {
        if (item.task_user_name) {
            taskUserNameCounts[item.task_user_name] = (taskUserNameCounts[item.task_user_name] || 0) + 1;
        }
    });
    taskUserNameChart.data.labels = Object.keys(taskUserNameCounts);
    taskUserNameChart.data.datasets[0].data = Object.values(taskUserNameCounts);
    taskUserNameChart.update();

    // Company Name Chart
    const companyNameCounts = {};
    filteredData.forEach(item => {
        if (item.company_name) {
            companyNameCounts[item.company_name] = (companyNameCounts[item.company_name] || 0) + 1;
        }
    });
    companyNameChart.data.labels = Object.keys(companyNameCounts);
    companyNameChart.data.datasets[0].data = Object.values(companyNameCounts);
    companyNameChart.update();

    // Action Taken Chart
    const actionTakenCounts = {};
    filteredData.forEach(item => {
        if (item.action_taken) {
            actionTakenCounts[item.action_taken] = (actionTakenCounts[item.action_taken] || 0) + 1;
        }
    });
    actionTakenChart.data.labels = Object.keys(actionTakenCounts);
    actionTakenChart.data.datasets[0].data = Object.values(actionTakenCounts);
    actionTakenChart.update();

    // Purpose Achieved Chart
    const purposeAchievedCounts = {};
    filteredData.forEach(item => {
        if (item.purpose_achieved) {
            purposeAchievedCounts[item.purpose_achieved] = (purposeAchievedCounts[item.purpose_achieved] || 0) + 1;
        }
    });
    purposeAchievedChart.data.labels = Object.keys(purposeAchievedCounts);
    purposeAchievedChart.data.datasets[0].data = Object.values(purposeAchievedCounts);
    purposeAchievedChart.update();

    // Task Status Over Time Chart
    const taskStatusOverTimeCounts = {};
    filteredData.forEach(item => {
        if (item.task_date_time && item.task_status) {
            const date = item.task_date_time.split('T')[0]; // Safeguard: Ensure task_date_time is defined
            if (!taskStatusOverTimeCounts[date]) {
                taskStatusOverTimeCounts[date] = {};
            }
            taskStatusOverTimeCounts[date][item.task_status] = (taskStatusOverTimeCounts[date][item.task_status] || 0) + 1;
        }
    });
    const dates = Object.keys(taskStatusOverTimeCounts);
    const datasets = [];
    const statuses = [...new Set(filteredData.map(item => item.task_status))];
    statuses.forEach(status => {
        datasets.push({
            label: status,
            data: dates.map(date => taskStatusOverTimeCounts[date][status] || 0),
            backgroundColor: 'rgba(' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ', 0.7)',
            borderColor: 'rgba(255, 255, 255, 0.8)',
            borderWidth: 1
        });
    });
    taskStatusOverTimeChart.data.labels = dates;
    taskStatusOverTimeChart.data.datasets = datasets;
    taskStatusOverTimeChart.update();

    // Task Date Chart
    const taskDateCounts = {};
    filteredData.forEach(item => {
        if (item.task_date_time) {
            const date = item.task_date_time.split('T')[0]; // Safeguard: Ensure task_date_time is defined
            taskDateCounts[date] = (taskDateCounts[date] || 0) + 1;
        }
    });
    taskDateChart.data.labels = Object.keys(taskDateCounts);
    taskDateChart.data.datasets[0].data = Object.values(taskDateCounts);
    taskDateChart.update();

    // New charts
    // Total Task by User
    const totalTaskByUserCounts = {};
    filteredData.forEach(item => {
        if (item.task_user_name) {
            totalTaskByUserCounts[item.task_user_name] = (totalTaskByUserCounts[item.task_user_name] || 0) + 1;
        }
    });
    totalTaskByUserChart.data.labels = Object.keys(totalTaskByUserCounts);
    totalTaskByUserChart.data.datasets[0].data = Object.values(totalTaskByUserCounts);
    totalTaskByUserChart.update();

    // Total Task by Company Name
    const totalTaskByCompanyNameCounts = {};
    filteredData.forEach(item => {
        if (item.company_name) {
            totalTaskByCompanyNameCounts[item.company_name] = (totalTaskByCompanyNameCounts[item.company_name] || 0) + 1;
        }
    });
    totalTaskByCompanyNameChart.data.labels = Object.keys(totalTaskByCompanyNameCounts);
    totalTaskByCompanyNameChart.data.datasets[0].data = Object.values(totalTaskByCompanyNameCounts);
    totalTaskByCompanyNameChart.update();

    // Total Task by Company Name with User Name
    const totalTaskByCompanyNameWithUserNameCounts = {};
    filteredData.forEach(item => {
        if (item.company_name && item.task_user_name) {
            const key = `${item.company_name} > ${item.task_user_name}`;
            totalTaskByCompanyNameWithUserNameCounts[key] = (totalTaskByCompanyNameWithUserNameCounts[key] || 0) + 1;
        }
    });
    totalTaskByCompanyNameWithUserNameChart.data.labels = Object.keys(totalTaskByCompanyNameWithUserNameCounts);
    totalTaskByCompanyNameWithUserNameChart.data.datasets[0].data = Object.values(totalTaskByCompanyNameWithUserNameCounts);
    totalTaskByCompanyNameWithUserNameChart.update();

    // Total Task by Company with Task Name
    const totalTaskByCompanyWithTaskNameCounts = {};
    filteredData.forEach(item => {
        if (item.company_name && item.task_name) {
            const key = `${item.company_name} > ${item.task_name}`;
            totalTaskByCompanyWithTaskNameCounts[key] = (totalTaskByCompanyWithTaskNameCounts[key] || 0) + 1;
        }
    });
    totalTaskByCompanyWithTaskNameChart.data.labels = Object.keys(totalTaskByCompanyWithTaskNameCounts);
    totalTaskByCompanyWithTaskNameChart.data.datasets[0].data = Object.values(totalTaskByCompanyWithTaskNameCounts);
    totalTaskByCompanyWithTaskNameChart.update();

    // Total Task by User with Company Name
    const totalTaskByUserWithCompanyNameCounts = {};
    filteredData.forEach(item => {
        if (item.task_user_name && item.company_name) {
            const key = `${item.task_user_name} > ${item.company_name}`;
            totalTaskByUserWithCompanyNameCounts[key] = (totalTaskByUserWithCompanyNameCounts[key] || 0) + 1;
        }
    });
    totalTaskByUserWithCompanyNameChart.data.labels = Object.keys(totalTaskByUserWithCompanyNameCounts);
    totalTaskByUserWithCompanyNameChart.data.datasets[0].data = Object.values(totalTaskByUserWithCompanyNameCounts);
    totalTaskByUserWithCompanyNameChart.update();

    // Total Task by Company Name with User Name (Duplicate for consistency)
    const totalTaskByCompanyNameWithUserName2Counts = {};
    filteredData.forEach(item => {
        if (item.company_name && item.task_user_name) {
            const key = `${item.company_name} > ${item.task_user_name}`;
            totalTaskByCompanyNameWithUserName2Counts[key] = (totalTaskByCompanyNameWithUserName2Counts[key] || 0) + 1;
        }
    });
    totalTaskByCompanyNameWithUserName2Chart.data.labels = Object.keys(totalTaskByCompanyNameWithUserName2Counts);
    totalTaskByCompanyNameWithUserName2Chart.data.datasets[0].data = Object.values(totalTaskByCompanyNameWithUserName2Counts);
    totalTaskByCompanyNameWithUserName2Chart.update();

    // Total Task with Action Taken Yes and Purpose Achieved Yes
    const totalTaskActionYesPurposeYesCounts = filteredData.filter(item => item.action_taken === 'yes' && item.purpose_achieved === 'yes').length;
    totalTaskActionYesPurposeYesChart.data.labels = ['Total Tasks'];
    totalTaskActionYesPurposeYesChart.data.datasets[0].data = [totalTaskActionYesPurposeYesCounts];
    totalTaskActionYesPurposeYesChart.update();

    // Total Task with Action Taken Yes and Purpose Achieved No
    const totalTaskActionYesPurposeNoCounts = filteredData.filter(item => item.action_taken === 'yes' && item.purpose_achieved === 'no').length;
    totalTaskActionYesPurposeNoChart.data.labels = ['Total Tasks'];
    totalTaskActionYesPurposeNoChart.data.datasets[0].data = [totalTaskActionYesPurposeNoCounts];
    totalTaskActionYesPurposeNoChart.update();

    // Total Task with Action Taken No and Purpose Achieved No
    const totalTaskActionNoPurposeNoCounts = filteredData.filter(item => item.action_taken === 'no' && item.purpose_achieved === 'no').length;
    totalTaskActionNoPurposeNoChart.data.labels = ['Total Tasks'];
    totalTaskActionNoPurposeNoChart.data.datasets[0].data = [totalTaskActionNoPurposeNoCounts];
    totalTaskActionNoPurposeNoChart.update();

    // Task by Time Slot with Task Name
    const taskByTimeSlotWithTaskNameCounts = {};
    filteredData.forEach(item => {
        if (item.task_date_time && item.task_name) {
            const timeSlot = getTimeSlot(item.task_date_time);
            const key = `${timeSlot} > ${item.task_name}`;
            taskByTimeSlotWithTaskNameCounts[key] = (taskByTimeSlotWithTaskNameCounts[key] || 0) + 1;
        }
    });
    taskByTimeSlotWithTaskNameChart.data.labels = Object.keys(taskByTimeSlotWithTaskNameCounts);
    taskByTimeSlotWithTaskNameChart.data.datasets[0].data = Object.values(taskByTimeSlotWithTaskNameCounts);
    taskByTimeSlotWithTaskNameChart.update();

    // Task by Time with Task Name
    const taskByTimeWithTaskNameCounts = {};
    filteredData.forEach(item => {
        if (item.task_date_time && item.task_name) {
            const time = item.task_date_time.split('T')[1];
            if (time) {
                const formattedTime = time.substring(0, 5); // Extract HH:MM
                const key = `${formattedTime} > ${item.task_name}`;
                taskByTimeWithTaskNameCounts[key] = (taskByTimeWithTaskNameCounts[key] || 0) + 1;
            }
        }
    });
    taskByTimeWithTaskNameChart.data.labels = Object.keys(taskByTimeWithTaskNameCounts);
    taskByTimeWithTaskNameChart.data.datasets[0].data = Object.values(taskByTimeWithTaskNameCounts);
    taskByTimeWithTaskNameChart.update();
}

    });
</script>
</body>
</html>
