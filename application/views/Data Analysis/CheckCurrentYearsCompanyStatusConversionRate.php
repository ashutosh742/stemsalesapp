<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversion Data Visualization</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- Chart.js CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include("nav.php"); ?>
    <div class="container mt-5">

    <hr>
    <h1 class="text-center">Current Year Status Conversion Data Visualization</h1>
    <hr>

        <div class="row mt-4">
            <div class="col-md-2">
                <label for="appointmentDateFilter">Date:</label>
                <select id="appointmentDateFilter" class="form-control mb-2">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="taskUserNameFilter">User Name:</label>
                <select id="taskUserNameFilter" class="form-control mb-2">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="userTypesFilter">User Types:</label>
                <select id="userTypesFilter" class="form-control mb-2">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="taskNameFilter">Task Name:</label>
                <select id="taskNameFilter" class="form-control mb-2">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="conversionNameFilter">Conversion Name:</label>
                <select id="conversionNameFilter" class="form-control mb-2">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="conversionTypeFilter">Conversion Type:</label>
                <select id="conversionTypeFilter" class="form-control mb-2">
                    <option value="">All</option>
                </select>
            </div>
        </div>

        <br>
    <div class="col-md-12 text-center">
        <button class="btn btn-success" id="downloadPdf">Download PDF</button>
        <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
    </div>
    <br>
    </div>
    <hr>
    <br>

    <div class="container-fluid mt-5">

        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="conversionByDateChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="sankeyUserWiseChart"></canvas>
            </div>

            <div class="col-md-6">
                <canvas id="conversionByUserTypeChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="conversionByTaskNameChart"></canvas>
            </div>

            <div class="col-md-6">
                <canvas id="conversionByUserChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="conversionTrendChart"></canvas>
            </div>

            <div class="col-md-6">
                <canvas id="stackedConversionChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="userConversionByDateStackedChart"></canvas>
            </div>

            <div class="col-md-6">
                <canvas id="sankeyUserDateWiseChart"></canvas>
            </div>

            <div class="col-md-6">
                <canvas id="barChart"></canvas>
            </div>
            <div class="col-md-12">
                <canvas id="hierarchyChart"></canvas>
            </div>

            <div class="col-md-12">
                <canvas id="columnChart"></canvas>
            </div>
            
          
            <div class="col-md-12">
                <canvas id="areaChart"></canvas>
            </div>
        </div>
    </div>

<?php

$loadData = '';
foreach($getAllUserData as $cData){
    $loadData .= "{
        appointment_date: '{$cData->appointment_date}',
        task_user_name: '{$cData->task_user_name}',
        user_types: '{$cData->user_types}',
        task_name: '{$cData->task_name}',
        conversion_name: '{$cData->conversion_name}',
        conversion_type: '{$cData->conversion_type}',
        conversion_count: " . (int)$cData->conversion_count . "
    },";
}
$loadData = rtrim($loadData, ',');

?>

    <?php include("footer.php"); ?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const data = [
            <?=$loadData?>
        ];

        // Function to aggregate data
        function aggregateData(data, key) {
            return data.reduce((acc, item) => {
                const group = item[key];
                if (!acc[group]) {
                    acc[group] = 0;
                }
                acc[group] += item.conversion_count;
                return acc;
            }, {});
        }

        // Function to create a chart
        function createChart(ctx, labels, data, chartType = 'bar', colors = []) {
            return new Chart(ctx, {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Conversion Count',
                        data: data,
                        backgroundColor: colors.length ? colors : 'rgba(75, 192, 192, 0.2)',
                        borderColor: colors.length ? colors.map(color => color.replace('0.2', '1')) : 'rgba(75, 192, 192, 1)',
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
        }

        // Function to update charts
        function updateCharts(filteredData) {
            // Conversion Count by Date
            const conversionByDateData = aggregateData(filteredData, 'appointment_date');
            conversionByDateChart.data.labels = Object.keys(conversionByDateData);
            conversionByDateChart.data.datasets[0].data = Object.values(conversionByDateData);
            conversionByDateChart.update();

            // Conversion Count by User Type
            const conversionByUserTypeData = aggregateData(filteredData, 'user_types');
            conversionByUserTypeChart.data.labels = Object.keys(conversionByUserTypeData);
            conversionByUserTypeChart.data.datasets[0].data = Object.values(conversionByUserTypeData);
            conversionByUserTypeChart.update();

            // Conversion Count by Task Name
            const conversionByTaskNameData = aggregateData(filteredData, 'task_name');
            conversionByTaskNameChart.data.labels = Object.keys(conversionByTaskNameData);
            conversionByTaskNameChart.data.datasets[0].data = Object.values(conversionByTaskNameData);
            conversionByTaskNameChart.update();

            // Conversion Count by User
            const conversionByUserData = aggregateData(filteredData, 'task_user_name');
            conversionByUserChart.data.labels = Object.keys(conversionByUserData);
            conversionByUserChart.data.datasets[0].data = Object.values(conversionByUserData);
            conversionByUserChart.update();

            // Conversion Trend Over Time
            const conversionTrendData = filteredData.reduce((acc, item) => {
                const date = item.appointment_date;
                if (!acc[date]) {
                    acc[date] = { positive: 0, negative: 0 };
                }
                if (item.conversion_type === 'Positive Conversion') {
                    acc[date].positive += item.conversion_count;
                } else {
                    acc[date].negative += item.conversion_count;
                }
                return acc;
            }, {});

            conversionTrendChart.data.labels = Object.keys(conversionTrendData);
            conversionTrendChart.data.datasets[0].data = Object.values(conversionTrendData).map(item => item.positive);
            conversionTrendChart.update();

            // Stacked Conversion Chart
            stackedConversionChart.data.labels = Object.keys(conversionTrendData);
            stackedConversionChart.data.datasets[0].data = Object.values(conversionTrendData).map(item => item.positive);
            stackedConversionChart.data.datasets[1].data = Object.values(conversionTrendData).map(item => item.negative);
            stackedConversionChart.update();

            // User Conversion by Date Stacked Chart
            const userConversionByDateData = filteredData.reduce((acc, item) => {
                const user = item.task_user_name;
                const date = item.appointment_date;
                if (!acc[user]) {
                    acc[user] = { positive: 0, negative: 0 };
                }
                if (item.conversion_type === 'Positive Conversion') {
                    acc[user].positive += item.conversion_count;
                } else {
                    acc[user].negative += item.conversion_count;
                }
                return acc;
            }, {});

            userConversionByDateStackedChart.data.labels = Object.keys(userConversionByDateData);
            userConversionByDateStackedChart.data.datasets[0].data = Object.values(userConversionByDateData).map(userData => userData.positive);
            userConversionByDateStackedChart.data.datasets[1].data = Object.values(userConversionByDateData).map(userData => userData.negative);
            userConversionByDateStackedChart.update();

            // Sankey User Date Wise Chart
            const sankeyUserDateWiseData = filteredData.reduce((acc, item) => {
                const user = item.task_user_name;
                const date = item.appointment_date;
                if (!acc[user]) {
                    acc[user] = { positive: 0, negative: 0 };
                }
                if (item.conversion_type === 'Positive Conversion') {
                    acc[user].positive += item.conversion_count;
                } else {
                    acc[user].negative += item.conversion_count;
                }
                return acc;
            }, {});

            sankeyUserDateWiseChart.data.labels = Object.keys(sankeyUserDateWiseData);
            sankeyUserDateWiseChart.data.datasets[0].data = Object.values(sankeyUserDateWiseData).map(userData => userData.positive);
            sankeyUserDateWiseChart.data.datasets[1].data = Object.values(sankeyUserDateWiseData).map(userData => userData.negative);
            sankeyUserDateWiseChart.update();

            // Sankey User Wise Chart
            const sankeyUserWiseData = filteredData.reduce((acc, item) => {
                const user = item.task_user_name;
                if (!acc[user]) {
                    acc[user] = { positive: 0, negative: 0 };
                }
                if (item.conversion_type === 'Positive Conversion') {
                    acc[user].positive += item.conversion_count;
                } else {
                    acc[user].negative += item.conversion_count;
                }
                return acc;
            }, {});

            sankeyUserWiseChart.data.labels = Object.keys(sankeyUserWiseData);
            sankeyUserWiseChart.data.datasets[0].data = Object.values(sankeyUserWiseData).map(userData => userData.positive);
            sankeyUserWiseChart.data.datasets[1].data = Object.values(sankeyUserWiseData).map(userData => userData.negative);
            sankeyUserWiseChart.update();

            // Bar Chart
            const barChartData = aggregateData(filteredData, 'appointment_date');
            barChart.data.labels = Object.keys(barChartData);
            barChart.data.datasets[0].data = Object.values(barChartData);
            barChart.update();

            // Column Chart
            const columnChartData = aggregateData(filteredData, 'task_user_name');
            columnChart.data.labels = Object.keys(columnChartData);
            columnChart.data.datasets[0].data = Object.values(columnChartData);
            columnChart.update();

            // Area Chart
            const areaChartData = filteredData.reduce((acc, item) => {
                const date = item.appointment_date;
                if (!acc[date]) {
                    acc[date] = 0;
                }
                acc[date] += item.conversion_count;
                return acc;
            }, {});

            areaChart.data.labels = Object.keys(areaChartData);
            areaChart.data.datasets[0].data = Object.values(areaChartData);
            areaChart.update();

            // Hierarchy Chart
            const hierarchyData = filteredData.reduce((acc, item) => {
                const userType = item.user_types;
                const conversionName = item.conversion_name;
                const conversionType = item.conversion_type;
                const date = item.appointment_date;
                if (!acc[userType]) {
                    acc[userType] = {};
                }
                if (!acc[userType][conversionName]) {
                    acc[userType][conversionName] = {};
                }
                if (!acc[userType][conversionName][conversionType]) {
                    acc[userType][conversionName][conversionType] = {};
                }
                if (!acc[userType][conversionName][conversionType][date]) {
                    acc[userType][conversionName][conversionType][date] = 0;
                }
                acc[userType][conversionName][conversionType][date] += item.conversion_count;
                return acc;
            }, {});

            const hierarchyLabels = [];
            const hierarchyDataValues = [];

            for (const userType in hierarchyData) {
                for (const conversionName in hierarchyData[userType]) {
                    for (const conversionType in hierarchyData[userType][conversionName]) {
                        for (const date in hierarchyData[userType][conversionName][conversionType]) {
                            hierarchyLabels.push(`${userType} > ${conversionName} > ${conversionType} > ${date}`);
                            hierarchyDataValues.push(hierarchyData[userType][conversionName][conversionType][date]);
                        }
                    }
                }
            }

            hierarchyChart.data.labels = hierarchyLabels;
            hierarchyChart.data.datasets[0].data = hierarchyDataValues;
            hierarchyChart.update();
        }

        // Initialize charts
        const conversionByDateCtx = document.getElementById('conversionByDateChart').getContext('2d');
        const conversionByUserTypeCtx = document.getElementById('conversionByUserTypeChart').getContext('2d');
        const conversionByTaskNameCtx = document.getElementById('conversionByTaskNameChart').getContext('2d');
        const conversionByUserCtx = document.getElementById('conversionByUserChart').getContext('2d');
        const conversionTrendCtx = document.getElementById('conversionTrendChart').getContext('2d');
        const stackedConversionCtx = document.getElementById('stackedConversionChart').getContext('2d');
        const userConversionByDateStackedCtx = document.getElementById('userConversionByDateStackedChart').getContext('2d');
        const sankeyUserDateWiseCtx = document.getElementById('sankeyUserDateWiseChart').getContext('2d');
        const sankeyUserWiseCtx = document.getElementById('sankeyUserWiseChart').getContext('2d');
        const barCtx = document.getElementById('barChart').getContext('2d');
        const columnCtx = document.getElementById('columnChart').getContext('2d');
        const areaCtx = document.getElementById('areaChart').getContext('2d');
        const hierarchyCtx = document.getElementById('hierarchyChart').getContext('2d');

        const conversionByDateChart = createChart(conversionByDateCtx, [], [], 'bar', ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)']);
        const conversionByUserTypeChart = createChart(conversionByUserTypeCtx, [], [], 'bar', ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)']);
        const conversionByTaskNameChart = createChart(conversionByTaskNameCtx, [], [], 'bar', ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)']);
        const conversionByUserChart = createChart(conversionByUserCtx, [], [], 'bar', ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(255, 99, 132, 0.2)']);
        const conversionTrendChart = createChart(conversionTrendCtx, [], [], 'line', ['rgba(54, 162, 235, 0.2)']);
        const stackedConversionChart = new Chart(stackedConversionCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Positive Conversions',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Negative Conversions',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true
                    },
                    x: {
                        stacked: true
                    }
                }
            }
        });
        const userConversionByDateStackedChart = new Chart(userConversionByDateStackedCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Positive Conversions',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Negative Conversions',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true
                    },
                    x: {
                        stacked: true
                    }
                }
            }
        });
        const sankeyUserDateWiseChart = new Chart(sankeyUserDateWiseCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Positive Conversions',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Negative Conversions',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true
                    },
                    x: {
                        stacked: true
                    }
                }
            }
        });
        const sankeyUserWiseChart = new Chart(sankeyUserWiseCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Positive Conversions',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Negative Conversions',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true
                    },
                    x: {
                        stacked: true
                    }
                }
            }
        });
        const barChart = createChart(barCtx, [], [], 'bar', ['rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)']);
        const columnChart = createChart(columnCtx, [], [], 'bar', ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)']);
        const areaChart = createChart(areaCtx, [], [], 'line', ['rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)']);
        const hierarchyChart = createChart(hierarchyCtx, [], [], 'bar', ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)']);

        // Populate filters
        const uniqueDates = [...new Set(data.map(item => item.appointment_date))];
        const uniqueUsers = [...new Set(data.map(item => item.task_user_name))];
        const uniqueUserTypes = [...new Set(data.map(item => item.user_types))];
        const uniqueTaskNames = [...new Set(data.map(item => item.task_name))];
        const uniqueConversionNames = [...new Set(data.map(item => item.conversion_name))];
        const uniqueConversionTypes = [...new Set(data.map(item => item.conversion_type))];

        const appointmentDateFilter = document.getElementById('appointmentDateFilter');
        const taskUserNameFilter = document.getElementById('taskUserNameFilter');
        const userTypesFilter = document.getElementById('userTypesFilter');
        const taskNameFilter = document.getElementById('taskNameFilter');
        const conversionNameFilter = document.getElementById('conversionNameFilter');
        const conversionTypeFilter = document.getElementById('conversionTypeFilter');

        uniqueDates.forEach(date => {
            const option = document.createElement('option');
            option.value = date;
            option.textContent = date;
            appointmentDateFilter.appendChild(option);
        });

        uniqueUsers.forEach(user => {
            const option = document.createElement('option');
            option.value = user;
            option.textContent = user;
            taskUserNameFilter.appendChild(option);
        });

        uniqueUserTypes.forEach(userType => {
            const option = document.createElement('option');
            option.value = userType;
            option.textContent = userType;
            userTypesFilter.appendChild(option);
        });

        uniqueTaskNames.forEach(taskName => {
            const option = document.createElement('option');
            option.value = taskName;
            option.textContent = taskName;
            taskNameFilter.appendChild(option);
        });

        uniqueConversionNames.forEach(conversionName => {
            const option = document.createElement('option');
            option.value = conversionName;
            option.textContent = conversionName;
            conversionNameFilter.appendChild(option);
        });

        uniqueConversionTypes.forEach(conversionType => {
            const option = document.createElement('option');
            option.value = conversionType;
            option.textContent = conversionType;
            conversionTypeFilter.appendChild(option);
        });

        // Function to filter data
        function filterData() {
            const selectedDate = appointmentDateFilter.value;
            const selectedUser = taskUserNameFilter.value;
            const selectedUserType = userTypesFilter.value;
            const selectedTaskName = taskNameFilter.value;
            const selectedConversionName = conversionNameFilter.value;
            const selectedConversionType = conversionTypeFilter.value;

            let filteredData = data;

            if (selectedDate) {
                filteredData = filteredData.filter(item => item.appointment_date === selectedDate);
            }
            if (selectedUser) {
                filteredData = filteredData.filter(item => item.task_user_name === selectedUser);
            }
            if (selectedUserType) {
                filteredData = filteredData.filter(item => item.user_types === selectedUserType);
            }
            if (selectedTaskName) {
                filteredData = filteredData.filter(item => item.task_name === selectedTaskName);
            }
            if (selectedConversionName) {
                filteredData = filteredData.filter(item => item.conversion_name === selectedConversionName);
            }
            if (selectedConversionType) {
                filteredData = filteredData.filter(item => item.conversion_type === selectedConversionType);
            }

            updateCharts(filteredData);
        }

        // Add event listeners to filters
        appointmentDateFilter.addEventListener('change', filterData);
        taskUserNameFilter.addEventListener('change', filterData);
        userTypesFilter.addEventListener('change', filterData);
        taskNameFilter.addEventListener('change', filterData);
        conversionNameFilter.addEventListener('change', filterData);
        conversionTypeFilter.addEventListener('change', filterData);

        // Initial chart update
        filterData();

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
            const chartIds = ['conversionByDateChart', 'conversionByUserTypeChart', 'conversionByTaskNameChart', 'conversionByUserChart', 'conversionTrendChart', 'stackedConversionChart', 'userConversionByDateStackedChart', 'sankeyUserDateWiseChart', 'sankeyUserWiseChart', 'barChart', 'columnChart', 'areaChart', 'hierarchyChart'];
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
