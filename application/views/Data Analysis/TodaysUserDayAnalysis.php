<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Management Graphs</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
        .chart-container {
            justify-content: space-around;
        }
        .chart-box {
            margin-top: 40px;
        }
        .blink {
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
        }
        .display-flex {
            align-items: center;
            justify-content: center;
            display: flex;
        }
    </style>
</head>
<body>
<?php include("nav.php"); ?>

<div class="container-fluid">
    <br>
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <h3>Today's Day Management</h3>
            </div>
        </div>
    </div>
    <hr>
    <br>

    <div class="card p-2">
        <div class="row mb-4">
            <div class="col-md-4">
                <select id="userFilter" class="form-control">
                    <option value="">Users</option>
                </select>
            </div>
            <div class="col-md-4">
                <select id="departmentFilter" class="form-control">
                    <option value="">Departments</option>
                </select>
            </div>
            <div class="col-md-4">
                <select id="workFromFilter" class="form-control">
                    <option value="">Work From</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <select id="dayStatusFilter" class="form-control">
                    <option value="">Day Status</option>
                </select>
            </div>
            <div class="col-md-6">
                <select id="timeDifferenceFilter" class="form-control">
                    <option value="">Time Differences</option>
                </select>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <select id="startDecisionFilter" class="form-control">
                    <option value="">Start Day Decisions</option>
                </select>
            </div>
            <div class="col-md-6">
                <select id="closedDecisionFilter" class="form-control">
                    <option value="">Closed Day Decisions</option>
                </select>
            </div>
        </div>
        <hr>
        <div class="row">
            <!-- Start Date and Closed Date filters removed -->
        </div>
    </div>
    </div>

    <br>
    <hr>
        <div class="container">
        <div class="row">

<div class="col-md-3">
 <p>Start System Decision</p>
<ul>
<li><strong>10:00 - 10:14:</strong> <span>Good Start</span></li>
<li><strong>10:15 - 10:29:</strong> <span>Not Good Start</span></li>
<li><strong>10:30 - 10:59:</strong> <span>Bad Start</span></li>
<li><strong>11:00 - 11:29:</strong> <span>Very Bad Start</span></li>
<li><strong>11:30 - 23:59:</strong> <span>Very Very Bad Start</span></li>
<li><strong>Other:</strong> <span>Start time out of range</span></li>
</ul>
 </div>

 <div class="col-md-3">
     <p>Closed System Decision</p>
 <ul>
<li><strong>18:30 - 23:45:</strong> <span>Good Closed</span></li>
<li><strong>10:00 - 13:30:</strong> <span>Bad Closed</span></li>
<li><strong>13:30 - 18:30:</strong> <span>Bad Closed</span></li>
<li><strong>Other:</strong> <span>Closed time out of range</span></li>
</ul>

 </div>

 <div class="col-md-3">
<p>Day Status Message</p>
<ul>
<li><strong>Status:</strong> <span>User not closed their day</span></li>
<li><strong>Status:</strong> <span>Closed on same day by user</span></li>
<li><strong>Status:</strong> <span>Not closed on same day by user</span></li>
</ul>
 </div>

 <div class="col-md-3 text-center">
     <button class="btn btn-success" id="downloadPdf">Download PDF</button>
     <hr>
     <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
 </div>

</div>
        </div>
    <hr>
    <br>

    <div class="container-fluid">
        <div class="chart-container">
            <div class="row">
            <div class="col-md-12">
                    <div class="chart-box">
                        <canvas id="userStatusChart"></canvas>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="chart-box">
                        <canvas id="timeDifferenceChart"></canvas>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="chart-box">
                        <canvas id="dateWiseChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="decisionPieChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="monthWisePieChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="startTimeTrendChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-box">
                        <canvas id="closedTimeTrendChart"></canvas>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="chart-box">
                        <canvas id="userDayStartAndClosedAnalysisGraph"></canvas>
                    </div>
                </div>

            </div>
            <hr>
            <br>
            <br>
        </div>
    </div>
    <?php include("footer.php"); ?>

    <?php
    $data = $getAllUserData;
    ?>

<script>
    // Register the plugin
    Chart.register(ChartDataLabels);

    // Data from the provided array
    const data = <?php echo json_encode($data); ?>;

    // Calculate decision counts
    const decisionCounts = data.reduce((acc, item) => {
        acc[item.start_system_decision] = (acc[item.start_system_decision] || 0) + 1;
        acc[item.closed_system_decision] = (acc[item.closed_system_decision] || 0) + 1;
        return acc;
    }, {});

    // Populate filter options
    const populateFilterOptions = (filterId, dataKey) => {
        const select = document.getElementById(filterId);
        const uniqueValues = [...new Set(data.map(item => item[dataKey]))];
        uniqueValues.forEach(value => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.appendChild(option);
        });
    };

    populateFilterOptions('userFilter', 'user_name');
    populateFilterOptions('departmentFilter', 'department_name');
    populateFilterOptions('workFromFilter', 'work_from');
    populateFilterOptions('startDecisionFilter', 'start_system_decision');
    populateFilterOptions('closedDecisionFilter', 'closed_system_decision');
    populateFilterOptions('dayStatusFilter', 'day_status_message');
    populateFilterOptions('timeDifferenceFilter', 'time_difference');

    // Filter data based on selected options
    const filterData = () => {
        const userFilter = document.getElementById('userFilter').value;
        const departmentFilter = document.getElementById('departmentFilter').value;
        const workFromFilter = document.getElementById('workFromFilter').value;
        const startDecisionFilter = document.getElementById('startDecisionFilter').value;
        const closedDecisionFilter = document.getElementById('closedDecisionFilter').value;
        const dayStatusFilter = document.getElementById('dayStatusFilter').value;
        const timeDifferenceFilter = document.getElementById('timeDifferenceFilter').value;

        return data.filter(item => {
            return (
                (userFilter === '' || item.user_name === userFilter) &&
                (departmentFilter === '' || item.department_name === departmentFilter) &&
                (workFromFilter === '' || item.work_from === workFromFilter) &&
                (startDecisionFilter === '' || item.start_system_decision === startDecisionFilter) &&
                (closedDecisionFilter === '' || item.closed_system_decision === closedDecisionFilter) &&
                (dayStatusFilter === '' || item.day_status_message === dayStatusFilter) &&
                (timeDifferenceFilter === '' || item.time_difference === timeDifferenceFilter)
            );
        });
    };

    // Initialize dateWiseDataWithTimes globally
    let dateWiseDataWithTimes = {};
    let monthWisePercentages = {};

    // Update charts based on filtered data
    const updateCharts = () => {
        const filteredData = filterData();

        // Calculate decision counts
        const decisionCounts = filteredData.reduce((acc, item) => {
            acc[item.start_system_decision] = (acc[item.start_system_decision] || 0) + 1;
            acc[item.closed_system_decision] = (acc[item.closed_system_decision] || 0) + 1;
            return acc;
        }, {});

        // Calculate startTimes and closeTimes
        const startTimes = filteredData.map(item => new Date(item.ustart));
        const closeTimes = filteredData.map(item => item.uclose ? new Date(item.uclose) : null).filter(time => time !== null);

        // Calculate dateWiseDataWithTimes
        dateWiseDataWithTimes = filteredData.reduce((acc, item) => {
            const date = new Date(item.ustart).toLocaleDateString();
            if (!acc[date]) {
                acc[date] = { startTimes: [], closeTimes: [] };
            }
            acc[date].startTimes.push((new Date(item.ustart).getHours() + new Date(item.ustart).getMinutes() / 60).toFixed(2));
            if (item.uclose) {
                acc[date].closeTimes.push((new Date(item.uclose).getHours() + new Date(item.uclose).getMinutes() / 60).toFixed(2));
            }
            return acc;
        }, {});

        // Update timeDifferenceChart
        timeDifferenceChart.data.labels = filteredData.map(item => `${new Date(item.ustart).toLocaleDateString()}\n${item.user_name}`);
        timeDifferenceChart.data.datasets[0].data = filteredData.map(item => {
            if (item.time_difference === 'N/A' || !item.time_difference) return 0;
            const [days, time] = item.time_difference.split(' days ');
            if (!time) return 0;
            const [hours, minutes] = time.split(' hours ');
            return (parseInt(days) * 24 + parseInt(hours) + parseInt(minutes) / 60).toFixed(2);
        });
        timeDifferenceChart.update();

        // Update decisionPieChart
        decisionPieChart.data.labels = Object.keys(decisionCounts);
        decisionPieChart.data.datasets[0].data = Object.values(decisionCounts);
        decisionPieChart.update();

        // Update startTimeTrendChart
        startTimeTrendChart.data.labels = startTimes.map(time => {
            const date = time.toLocaleDateString();
            const firstEntry = filteredData.find(item => new Date(item.ustart).toLocaleDateString() === date);
            const userName = firstEntry ? firstEntry.user_name : 'N/A';
            return `${date}\n${userName}`;
        });
        startTimeTrendChart.data.datasets[0].data = startTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2));
        startTimeTrendChart.update();

        // Update userDayStartAndClosedAnalysisGraph
        userDayStartAndClosedAnalysisGraph.data.labels = startTimes.map(time => {
            const date = time.toLocaleDateString();
            const firstEntry = filteredData.find(item => new Date(item.ustart).toLocaleDateString() === date);
            const userName = firstEntry ? firstEntry.user_name : 'N/A';
            return `${date}\n${userName}`;
        });
        userDayStartAndClosedAnalysisGraph.data.datasets[0].data = startTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2));
        userDayStartAndClosedAnalysisGraph.data.datasets[1].data = closeTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2));
        userDayStartAndClosedAnalysisGraph.update();

        // Update closedTimeTrendChart
        closedTimeTrendChart.data.labels = closeTimes.map(time => {
            const date = time.toLocaleDateString();
            const firstEntry = filteredData.find(item => item.uclose && new Date(item.uclose).toLocaleDateString() === date);
            const userName = firstEntry ? firstEntry.user_name : 'N/A';
            return `${date}\n${userName}`;
        });
        closedTimeTrendChart.data.datasets[0].data = closeTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2));
        closedTimeTrendChart.update();

        // Update dateWiseChart
        dateWiseChart.data.labels = Object.keys(dateWiseDataWithTimes).map(date => {
            const firstEntry = filteredData.find(item => new Date(item.ustart).toLocaleDateString() === date);
            const userName = firstEntry ? firstEntry.user_name : 'N/A';
            return `${date}\n${userName}`;
        });
        dateWiseChart.data.datasets[0].data = Object.keys(dateWiseDataWithTimes).map(date =>
            dateWiseDataWithTimes[date].startTimes.length > 0 ?
            (dateWiseDataWithTimes[date].startTimes.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / dateWiseDataWithTimes[date].startTimes.length).toFixed(2) :
            0
        );
        dateWiseChart.data.datasets[1].data = Object.keys(dateWiseDataWithTimes).map(date =>
            dateWiseDataWithTimes[date].closeTimes.length > 0 ?
            (dateWiseDataWithTimes[date].closeTimes.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / dateWiseDataWithTimes[date].closeTimes.length).toFixed(2) :
            0
        );
        dateWiseChart.update();

        // Update monthWisePieChart
        const monthWiseData = filteredData.reduce((acc, item) => {
            const month = new Date(item.ustart).toLocaleString('default', { month: 'long', year: 'numeric' });
            if (!acc[month]) {
                acc[month] = { decisions: {}, status: {} };
            }
            acc[month].decisions[item.start_system_decision] = (acc[month].decisions[item.start_system_decision] || 0) + 1;
            acc[month].status[item.day_status_message] = (acc[month].status[item.day_status_message] || 0) + 1;
            return acc;
        }, {});
        monthWisePercentages = Object.keys(monthWiseData).reduce((acc, month) => {
            const totalDecisions = Object.values(monthWiseData[month].decisions).reduce((a, b) => a + b, 0);
            const totalStatus = Object.values(monthWiseData[month].status).reduce((a, b) => a + b, 0);

            acc[month] = {
                decisions: Object.keys(monthWiseData[month].decisions).map(decision => ({
                    decision,
                    count: monthWiseData[month].decisions[decision],
                    percentage: (monthWiseData[month].decisions[decision] / totalDecisions * 100).toFixed(2)
                })),
                status: Object.keys(monthWiseData[month].status).map(status => ({
                    status,
                    count: monthWiseData[month].status[status],
                    percentage: (monthWiseData[month].status[status] / totalStatus * 100).toFixed(2)
                }))
            };
            return acc;
        }, {});
        monthWisePieChart.data.labels = Object.keys(monthWisePercentages).flatMap(month => [
            `${month} Decisions`,
            `${month} Status`
        ]);
        monthWisePieChart.data.datasets[0].data = Object.values(monthWisePercentages).flatMap(item => [
            item.decisions.reduce((a, b) => a + b.count, 0),
            item.status.reduce((a, b) => a + b.count, 0)
        ]);
        monthWisePieChart.update();

        // Update userStatusChart
        userStatusChart.data.labels = filteredData.map(item => `${item.user_name}\n${item.department_name}\n${item.work_from}`);
        userStatusChart.data.datasets[0].data = filteredData.map(item => new Date(item.ustart).getHours() + new Date(item.ustart).getMinutes() / 60);
        userStatusChart.data.datasets[1].data = filteredData.map(item => item.uclose ? new Date(item.uclose).getHours() + new Date(item.uclose).getMinutes() / 60 : null);
        userStatusChart.data.datasets[2].data = filteredData.map(item => item.uclose ? 'Closed' : 'Pending');
        userStatusChart.update();
    };

    // Chart configurations
    const ctx1 = document.getElementById('timeDifferenceChart').getContext('2d');
    const timeDifferenceChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: data.map(item => `${new Date(item.ustart).toLocaleDateString()}\n${item.user_name}`),
            datasets: [{
                label: 'Time Difference (Hours)',
                data: data.map(item => {
                    if (item.time_difference === 'N/A' || !item.time_difference) return 0;
                    const [days, time] = item.time_difference.split(' days ');
                    if (!time) return 0;
                    const [hours, minutes] = time.split(' hours ');
                    return (parseInt(days) * 24 + parseInt(hours) + parseInt(minutes) / 60).toFixed(2);
                }),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Time Difference (hours)'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const timeDifference = data[index].time_difference;
                            const dayStatus = data[index].day_status_message;
                            const [days, time] = timeDifference.split(' days ');
                            if (!time) return 0;
                            const [hours, minutes] = time.split(' hours ');
                            const totalHours = (parseInt(days) * 24 + parseInt(hours) + parseInt(minutes) / 60).toFixed(2);
                            return [`Time Difference: ${totalHours} hours`, `Day Status: ${dayStatus}`];
                        }
                    }
                }
            }
        }
    });

    const ctx2 = document.getElementById('decisionPieChart').getContext('2d');
    const decisionPieChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: Object.keys(decisionCounts),
            datasets: [{
                label: 'System Decisions',
                data: Object.values(decisionCounts),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
                borderColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
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
                            const label = tooltipItem.label || '';
                            const value = tooltipItem.raw || 0;
                            const total = tooltipItem.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(2);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    formatter: function(value, context) {
                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(2);
                        return `${percentage}%`;
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    const ctx3 = document.getElementById('startTimeTrendChart').getContext('2d');
    const startTimes = data.map(item => new Date(item.ustart)); // Define startTimes here
    const closeTimes = data.map(item => item.uclose ? new Date(item.uclose) : null).filter(time => time !== null); // Define closeTimes here
    const startTimeTrendChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: startTimes.map(time => {
                const date = time.toLocaleDateString();
                const firstEntry = data.find(item => new Date(item.ustart).toLocaleDateString() === date);
                const userName = firstEntry ? firstEntry.user_name : 'N/A';
                return `${date}\n${userName}`;
            }),
            datasets: [{
                label: 'Start Times',
                data: startTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2)),
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hour of Day'
                    }
                },
                x: {
                    ticks: {
                        callback: function(value, index) {
                            const label = this.getLabelForValue(index);
                            const parts = label.split('\n');
                            return [`${parts[0]}`, `${parts[1]}`];
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const time = startTimes[index];
                            const date = time.toLocaleDateString();
                            const hours = (time.getHours() + time.getMinutes() / 60).toFixed(2);

                            const firstEntry = data.find(item => new Date(item.ustart).toLocaleDateString() === date);
                            const departmentName = firstEntry ? firstEntry.department_name : 'N/A';
                            const workFrom = firstEntry ? firstEntry.work_from : 'N/A';
                            const startSystemDecision = firstEntry ? firstEntry.start_system_decision : 'N/A';
                            const closedSystemDecision = firstEntry ? firstEntry.closed_system_decision : 'N/A';
                            const timeDifference = firstEntry ? firstEntry.time_difference : 'N/A';

                            return [
                                `Start Time: ${hours} hours`,
                                `Department: ${departmentName}`,
                                `Work From: ${workFrom}`,
                                `Start Decision: ${startSystemDecision}`,
                                `Closed Decision: ${closedSystemDecision}`,
                                `Time Difference: ${timeDifference}`
                            ];
                        }
                    }
                }
            }
        }
    });

    const ctxUserDayAnalysis = document.getElementById('userDayStartAndClosedAnalysisGraph').getContext('2d');
    const userDayStartAndClosedAnalysisGraph = new Chart(ctxUserDayAnalysis, {
        type: 'line',
        data: {
            labels: startTimes.map(time => {
                const date = time.toLocaleDateString();
                const firstEntry = data.find(item => new Date(item.ustart).toLocaleDateString() === date);
                const userName = firstEntry ? firstEntry.user_name : 'N/A';
                return `${date}\n${userName}`;
            }),
            datasets: [
                {
                    label: 'Start Times',
                    data: startTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2)),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    fill: false
                },
                {
                    label: 'Close Times',
                    data: closeTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2)),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: false
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hour of Day'
                    }
                },
                x: {
                    ticks: {
                        callback: function(value, index) {
                            const label = this.getLabelForValue(index);
                            const parts = label.split('\n');
                            return [`${parts[0]}`, `${parts[1]}`];
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const startTime = startTimes[index];
                            const closeTime = closeTimes[index];
                            const date = startTime.toLocaleDateString();
                            const startHours = (startTime.getHours() + startTime.getMinutes() / 60).toFixed(2);
                            const closeHours = closeTime ? (closeTime.getHours() + closeTime.getMinutes() / 60).toFixed(2) : 'N/A';

                            const firstEntry = data.find(item => new Date(item.ustart).toLocaleDateString() === date);
                            const departmentName = firstEntry ? firstEntry.department_name : 'N/A';
                            const workFrom = firstEntry ? firstEntry.work_from : 'N/A';
                            const startSystemDecision = firstEntry ? firstEntry.start_system_decision : 'N/A';
                            const closedSystemDecision = firstEntry ? firstEntry.closed_system_decision : 'N/A';
                            const timeDifference = firstEntry ? firstEntry.time_difference : 'N/A';

                            return [
                                `Start Time: ${startHours} hours`,
                                `Close Time: ${closeHours} hours`,
                                `Department: ${departmentName}`,
                                `Work From: ${workFrom}`,
                                `Start Decision: ${startSystemDecision}`,
                                `Closed Decision: ${closedSystemDecision}`,
                                `Time Difference: ${timeDifference}`
                            ];
                        }
                    }
                }
            }
        }
    });

    const ctx5 = document.getElementById('closedTimeTrendChart').getContext('2d');
    const closedTimeTrendChart = new Chart(ctx5, {
        type: 'line',
        data: {
            labels: closeTimes.map(time => {
                const date = time.toLocaleDateString();
                const firstEntry = data.find(item => item.uclose && new Date(item.uclose).toLocaleDateString() === date);
                const userName = firstEntry ? firstEntry.user_name : 'N/A';
                return `${date}\n${userName}`;
            }),
            datasets: [{
                label: 'Close Times',
                data: closeTimes.map(time => (time.getHours() + time.getMinutes() / 60).toFixed(2)),
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hour of Day'
                    }
                },
                x: {
                    ticks: {
                        callback: function(value, index) {
                            const label = this.getLabelForValue(index);
                            const parts = label.split('\n');
                            return [`${parts[0]}`, `${parts[1]}`];
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const time = closeTimes[index];
                            const date = time.toLocaleDateString();
                            const hours = (time.getHours() + time.getMinutes() / 60).toFixed(2);

                            const firstEntry = data.find(item => item.uclose && new Date(item.uclose).toLocaleDateString() === date);
                            const departmentName = firstEntry ? firstEntry.department_name : 'N/A';
                            const workFrom = firstEntry ? firstEntry.work_from : 'N/A';
                            const startSystemDecision = firstEntry ? firstEntry.start_system_decision : 'N/A';
                            const closedSystemDecision = firstEntry ? firstEntry.closed_system_decision : 'N/A';
                            const timeDifference = firstEntry ? firstEntry.time_difference : 'N/A';

                            return [
                                `Close Time: ${hours} hours`,
                                `Department: ${departmentName}`,
                                `Work From: ${workFrom}`,
                                `Start Decision: ${startSystemDecision}`,
                                `Closed Decision: ${closedSystemDecision}`,
                                `Time Difference: ${timeDifference}`
                            ];
                        }
                    }
                }
            }
        }
    });

    const ctx6 = document.getElementById('dateWiseChart').getContext('2d');
    const dateWiseChart = new Chart(ctx6, {
        type: 'bar',
        data: {
            labels: Object.keys(dateWiseDataWithTimes).map(date => {
                const firstEntry = data.find(item => new Date(item.ustart).toLocaleDateString() === date);
                const userName = firstEntry ? firstEntry.user_name : 'N/A';
                return `${date}\n${userName}`;
            }),
            datasets: [{
                label: 'Start Time (Hours)',
                data: Object.keys(dateWiseDataWithTimes).map(date =>
                    dateWiseDataWithTimes[date].startTimes.length > 0 ?
                    (dateWiseDataWithTimes[date].startTimes.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / dateWiseDataWithTimes[date].startTimes.length).toFixed(2) :
                    0
                ),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Close Time (Hours)',
                data: Object.keys(dateWiseDataWithTimes).map(date =>
                    dateWiseDataWithTimes[date].closeTimes.length > 0 ?
                    (dateWiseDataWithTimes[date].closeTimes.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / dateWiseDataWithTimes[date].closeTimes.length).toFixed(2) :
                    0
                ),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                },
                x: {
                    ticks: {
                        callback: function(value, index) {
                            const label = this.getLabelForValue(index);
                            const parts = label.split('\n');
                            return [`${parts[0]}`, `${parts[1]}`];
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const date = Object.keys(dateWiseDataWithTimes)[index];
                            const averageStartTime = dateWiseDataWithTimes[date].startTimes.length > 0 ?
                                (dateWiseDataWithTimes[date].startTimes.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / dateWiseDataWithTimes[date].startTimes.length).toFixed(2) :
                                0;
                            const averageCloseTime = dateWiseDataWithTimes[date].closeTimes.length > 0 ?
                                (dateWiseDataWithTimes[date].closeTimes.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / dateWiseDataWithTimes[date].closeTimes.length).toFixed(2) :
                                0;

                            const firstEntry = data.find(item => new Date(item.ustart).toLocaleDateString() === date);
                            const departmentName = firstEntry ? firstEntry.department_name : 'N/A';
                            const workFrom = firstEntry ? firstEntry.work_from : 'N/A';
                            const startSystemDecision = firstEntry ? firstEntry.start_system_decision : 'N/A';
                            const closedSystemDecision = firstEntry ? firstEntry.closed_system_decision : 'N/A';
                            const timeDifference = firstEntry ? firstEntry.time_difference : 'N/A';

                            return [
                                `Average Start Time: ${averageStartTime} hours`,
                                `Average Close Time: ${averageCloseTime} hours`,
                                `Department: ${departmentName}`,
                                `Work From: ${workFrom}`,
                                `Start Decision: ${startSystemDecision}`,
                                `Closed Decision: ${closedSystemDecision}`,
                                `Time Difference: ${timeDifference}`
                            ];
                        }
                    }
                }
            }
        }
    });

    const ctx7 = document.getElementById('monthWisePieChart').getContext('2d');
    const monthWisePieChart = new Chart(ctx7, {
        type: 'pie',
        data: {
            labels: Object.keys(monthWisePercentages).flatMap(month => [
                `${month} Decisions`,
                `${month} Status`
            ]),
            datasets: [{
                label: 'Month-wise Counts and Percentages',
                data: Object.values(monthWisePercentages).flatMap(item => [
                    item.decisions.reduce((a, b) => a + b.count, 0),
                    item.status.reduce((a, b) => a + b.count, 0)
                ]),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
                borderColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
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
                            const index = tooltipItem.dataIndex;
                            const month = Object.keys(monthWisePercentages)[Math.floor(index / 2)];
                            const isDecision = index % 2 === 0;
                            const data = isDecision ? monthWisePercentages[month].decisions : monthWisePercentages[month].status;
                            const label = isDecision ? 'Decision' : 'Status';
                            const info = data.map(item => `${item[isDecision ? 'decision' : 'status']}: ${item.count} (${item.percentage}%)`).join(', ');
                            return [`${month} ${label}: ${info}`];
                        }
                    }
                }
            }
        }
    });

    const ctxUserStatus = document.getElementById('userStatusChart').getContext('2d');
    const userStatusChart = new Chart(ctxUserStatus, {
        type: 'bar',
        data: {
            labels: data.map(item => `${item.user_name}\n${item.department_name}\n${item.work_from}`),
            datasets: [{
                label: 'Start Time',
                data: data.map(item => new Date(item.ustart).getHours() + new Date(item.ustart).getMinutes() / 60),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Close Time',
                data: data.map(item => item.uclose ? new Date(item.uclose).getHours() + new Date(item.uclose).getMinutes() / 60 : null),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Status',
                data: data.map(item => item.uclose ? 'Closed' : 'Pending'),
                type: 'line',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                yAxisID: 'statusAxis'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hour of Day'
                    }
                },
                statusAxis: {
                    type: 'category',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Status'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    ticks: {
                        callback: function(value, index) {
                            const label = this.getLabelForValue(index);
                            const parts = label.split('\n');
                            return [`${parts[0]}`, `${parts[1]}`, `${parts[2]}`];
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const index = tooltipItem.dataIndex;
                            const item = data[index];
                            const startTime = new Date(item.ustart).getHours() + new Date(item.ustart).getMinutes() / 60;
                            const closeTime = item.uclose ? new Date(item.uclose).getHours() + new Date(item.uclose).getMinutes() / 60 : 'Pending';
                            
                            const status = item.uclose ? 'Closed' : 'Pending';

                            return [
                                `Start Time: ${startTime.toFixed(2)} hours`,
                                `Close Time: ${closeTime === 'Pending' ? closeTime : closeTime.toFixed(2) + ' hours'}`,
                                `Status: ${status}`
                            ];
                        }
                    }
                }
            }
        }
    });

    // Update charts when filters change
    document.getElementById('userFilter').addEventListener('change', updateCharts);
    document.getElementById('departmentFilter').addEventListener('change', updateCharts);
    document.getElementById('workFromFilter').addEventListener('change', updateCharts);
    document.getElementById('startDecisionFilter').addEventListener('change', updateCharts);
    document.getElementById('closedDecisionFilter').addEventListener('change', updateCharts);
    document.getElementById('dayStatusFilter').addEventListener('change', updateCharts);
    document.getElementById('timeDifferenceFilter').addEventListener('change', updateCharts);

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
        const chartIds = ['timeDifferenceChart', 'decisionPieChart', 'startTimeTrendChart', 'userDayStartAndClosedAnalysisGraph', 'closedTimeTrendChart', 'dateWiseChart', 'monthWisePieChart', 'userStatusChart'];
        chartIds.forEach(chartId => {
            const chartCanvas = document.getElementById(chartId);
            const chartImage = chartCanvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = `${chartId}.png`;
            link.href = chartImage;
            link.click();
        });
    });
</script>

</body>
</html>
