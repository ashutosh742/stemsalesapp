<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <label for="nameFilter">Name:</label>
                <select id="nameFilter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="typeFilter">Type:</label>
                <select id="typeFilter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="workFromFilter">Work From:</label>
                <select id="workFromFilter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="dateFilter">Date:</label>
                <select id="dateFilter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <canvas id="totalPlanTimeChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="totalTaskTimeChart"></canvas>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <canvas id="taskPlanRequestChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="specialRequestLeaveChart"></canvas>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <canvas id="anyReminderChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="pendingTaskPlannerChart"></canvas>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <canvas id="totalAutoTasksChart"></canvas>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <canvas id="newColumnChart"></canvas>
            </div>
        </div>
    </div>

    <?php
    // Sample data
    $data = [
        (object) [
            'name' => 'Abinash Tarai',
            'user_id' => 100059,
            'type_name' => 'Sales Person',
            'userworkfrom' => 'Work From Field + Office',
            'task_date' => '2025-04-07 11:05:00',
            'total_task' => 31,
            'approved_task' => 20,
            'pending_approved' => 0,
            'complete_task' => 12,
            'pending_task' => 19,
            'total_autotask' => 11,
            'after_task' => 11,
            'complete_autotask' => 0,
            'pending_autotask' => 11,
            'total_reject' => 0,
            'pending_for_assign_after_reject_task' => 0,
            'admin_create_request_for_self_assign' => 0,
            'task_assignd_by_admin_after_reject' => 0,
            'task_assignd_by_user_after_reject' => 0,
            'action_yes_purpose_yes' => 2,
            'action_yes_purpose_no' => 2,
            'action_no_purpose_no' => 8,
            'call_task' => 15,
            'email_task' => 11,
            'scheduled_meeting_task' => 4,
            'barg_meeting_task' => 1,
            'whatsapp_activity' => 0,
            'write_mom' => 0,
            'write_proposal' => 0,
            'research_task' => 0,
            'documentation_task' => 0,
            'social_networking_task' => 0,
            'social_activity_task' => 0,
            'annual_review_target_task' => 0,
            'join_meeting_task' => 0,
            'mom_check_task' => 0,
            'create_bd_request_task' => 0,
            'submit_handover_task' => 0,
            'praposal_check_task' => 0,
            'total_task_time' => 485,
            'total_plan_time_on_planner' => 375,
            'total_plan_time_with_autotask' => 110,
            'task_plan_for_today_request' => 'Yes',
            'user_create_special_request_for_leave' => 'Yes',
            'user_request_any_reminder' => 'No',
            'pending_task_planner_request' => 'No',
            'session_count' => 287
        ],
        (object) [
            'name' => 'Nitin Poddar',
            'user_id' => 100062,
            'type_name' => 'Sales Person',
            'userworkfrom' => 'Work From Field + Office',
            'task_date' => '2025-04-07 10:00:00',
            'total_task' => 39,
            'approved_task' => 20,
            'pending_approved' => 0,
            'complete_task' => 35,
            'pending_task' => 4,
            'total_autotask' => 19,
            'after_task' => 18,
            'complete_autotask' => 15,
            'pending_autotask' => 4,
            'total_reject' => 0,
            'pending_for_assign_after_reject_task' => 0,
            'admin_create_request_for_self_assign' => 0,
            'task_assignd_by_admin_after_reject' => 0,
            'task_assignd_by_user_after_reject' => 0,
            'action_yes_purpose_yes' => 17,
            'action_yes_purpose_no' => 1,
            'action_no_purpose_no' => 17,
            'call_task' => 15,
            'email_task' => 17,
            'scheduled_meeting_task' => 4,
            'barg_meeting_task' => 1,
            'whatsapp_activity' => 0,
            'write_mom' => 2,
            'write_proposal' => 0,
            'research_task' => 0,
            'documentation_task' => 0,
            'social_networking_task' => 0,
            'social_activity_task' => 0,
            'annual_review_target_task' => 0,
            'join_meeting_task' => 0,
            'mom_check_task' => 0,
            'create_bd_request_task' => 0,
            'submit_handover_task' => 0,
            'praposal_check_task' => 0,
            'total_task_time' => 565,
            'total_plan_time_on_planner' => 375,
            'total_plan_time_with_autotask' => 190,
            'task_plan_for_today_request' => 'No',
            'user_create_special_request_for_leave' => 'No',
            'user_request_any_reminder' => 'Yes',
            'pending_task_planner_request' => 'No',
            'session_count' => 201
        ]
    ];
    ?>

    <script>
      $(document).ready(function() {
    // PHP array converted to JSON string
    const data = <?php echo json_encode($data); ?>;

    // Populate filter options
    populateFilterOptions(data);

    // Initialize charts
    let totalPlanTimeChart = createChart('totalPlanTimeChart', 'Total Plan Time on Planner', data.map(item => item.total_plan_time_on_planner), data.map(item => item.name));
    let totalTaskTimeChart = createChart('totalTaskTimeChart', 'Total Task Time', data.map(item => item.total_task_time), data.map(item => item.name));
    let taskPlanRequestChart = createPieChart('taskPlanRequestChart', 'Task Plan for Today Request', countRequests(data, 'task_plan_for_today_request'));
    let specialRequestLeaveChart = createPieChart('specialRequestLeaveChart', 'Special Request for Leave', countRequests(data, 'user_create_special_request_for_leave'));
    let anyReminderChart = createPieChart('anyReminderChart', 'Any Reminder Request', countRequests(data, 'user_request_any_reminder'));
    let pendingTaskPlannerChart = createPieChart('pendingTaskPlannerChart', 'Pending Task Planner Request', countRequests(data, 'pending_task_planner_request'));
    let totalAutoTasksChart = createChart('totalAutoTasksChart', 'Total Auto Tasks', data.map(item => item.total_autotask), data.map(item => item.name));
    let newColumnChart = createNewColumnChart('newColumnChart', 'New Column Chart', data);

    // Filter functionality
    $('select').change(function() {
        let filteredData = filterData(data);
        updateCharts(filteredData, [
            { chart: totalPlanTimeChart, field: 'total_plan_time_on_planner' },
            { chart: totalTaskTimeChart, field: 'total_task_time' },
            { chart: taskPlanRequestChart, field: 'task_plan_for_today_request' },
            { chart: specialRequestLeaveChart, field: 'user_create_special_request_for_leave' },
            { chart: anyReminderChart, field: 'user_request_any_reminder' },
            { chart: pendingTaskPlannerChart, field: 'pending_task_planner_request' },
            { chart: totalAutoTasksChart, field: 'total_autotask' },
            { chart: newColumnChart, field: 'newColumn' }
        ]);
    });

    function populateFilterOptions(data) {
        let names = [...new Set(data.map(item => item.name))];
        let types = [...new Set(data.map(item => item.type_name))];
        let workFrom = [...new Set(data.map(item => item.userworkfrom))];
        let dates = [...new Set(data.map(item => item.task_date.split(' ')[0]))];

        names.forEach(name => $('#nameFilter').append(`<option value="${name}">${name}</option>`));
        types.forEach(type => $('#typeFilter').append(`<option value="${type}">${type}</option>`));
        workFrom.forEach(wf => $('#workFromFilter').append(`<option value="${wf}">${wf}</option>`));
        dates.forEach(date => $('#dateFilter').append(`<option value="${date}">${date}</option>`));
    }

    function convertMinutesToHours(minutes) {
        let hours = Math.floor(minutes / 60);
        let mins = minutes % 60;
        return `${hours}h ${mins}m`;
    }

    function countRequests(data, field) {
        let counts = { Yes: 0, No: 0 };
        data.forEach(item => {
            if (item[field] === 'Yes' || item[field] === 'No') {
                counts[item[field]]++;
            }
        });
        return counts;
    }

    function createChart(canvasId, label, data, labels) {
        let ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: label,
                        font: {
                            size: 14
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let label = tooltipItem.label || '';
                                let value = tooltipItem.raw;
                                return `${label}: ${value}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: 'black',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round
                    }
                }
            }
        });
    }

    function createPieChart(canvasId, label, data) {
        let ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: label,
                    data: Object.values(data),
                    backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: label,
                        font: {
                            size: 14
                        }
                    },
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        display: true,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value / sum * 100).toFixed(2) + "%";
                            return percentage;
                        }
                    }
                }
            }
        });
    }

    function createNewColumnChart(canvasId, label, data) {
        let ctx = document.getElementById(canvasId).getContext('2d');
        let datasets = [
            { label: 'Total Tasks', data: data.map(item => item.total_task), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
            { label: 'Approved Tasks', data: data.map(item => item.approved_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
            { label: 'Pending Approved', data: data.map(item => item.pending_approved), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
            { label: 'Complete Tasks', data: data.map(item => item.complete_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
            { label: 'Pending Tasks', data: data.map(item => item.pending_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
            { label: 'Total Auto Tasks', data: data.map(item => item.total_autotask), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
            { label: 'After Tasks', data: data.map(item => item.after_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
            { label: 'Complete Auto Tasks', data: data.map(item => item.complete_autotask), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
            { label: 'Pending Auto Tasks', data: data.map(item => item.pending_autotask), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
            { label: 'Total Reject', data: data.map(item => item.total_reject), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
            { label: 'Pending for Assign After Reject', data: data.map(item => item.pending_for_assign_after_reject_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
            { label: 'Admin Create Request for Self Assign', data: data.map(item => item.admin_create_request_for_self_assign), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
            { label: 'Task Assigned by Admin After Reject', data: data.map(item => item.task_assignd_by_admin_after_reject), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
            { label: 'Task Assigned by User After Reject', data: data.map(item => item.task_assignd_by_user_after_reject), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
            { label: 'Action Yes Purpose Yes', data: data.map(item => item.action_yes_purpose_yes), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
            { label: 'Action Yes Purpose No', data: data.map(item => item.action_yes_purpose_no), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
            { label: 'Action No Purpose No', data: data.map(item => item.action_no_purpose_no), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
            { label: 'Call Task', data: data.map(item => item.call_task), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
            { label: 'Email Task', data: data.map(item => item.email_task), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
            { label: 'Scheduled Meeting Task', data: data.map(item => item.scheduled_meeting_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
            { label: 'Barg Meeting Task', data: data.map(item => item.barg_meeting_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
            { label: 'WhatsApp Activity', data: data.map(item => item.whatsapp_activity), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
            { label: 'Write MoM', data: data.map(item => item.write_mom), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
            { label: 'Write Proposal', data: data.map(item => item.write_proposal), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
            { label: 'Research Task', data: data.map(item => item.research_task), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
            { label: 'Documentation Task', data: data.map(item => item.documentation_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
            { label: 'Social Networking Task', data: data.map(item => item.social_networking_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
            { label: 'Social Activity Task', data: data.map(item => item.social_activity_task), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
            { label: 'Annual Review Target Task', data: data.map(item => item.annual_review_target_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
            { label: 'Join Meeting Task', data: data.map(item => item.join_meeting_task), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
            { label: 'MoM Check Task', data: data.map(item => item.mom_check_task), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
            { label: 'Create BD Request Task', data: data.map(item => item.create_bd_request_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
            { label: 'Submit Handover Task', data: data.map(item => item.submit_handover_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
            { label: 'Proposal Check Task', data: data.map(item => item.praposal_check_task), backgroundColor: 'rgba(255, 159, 64, 0.6)' }
        ];

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => `${item.name} - ${item.type_name} - ${item.userworkfrom} - ${item.task_date}`),
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: label,
                        font: {
                            size: 14
                        }
                    },
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        display: true,
                        color: 'black',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round
                    }
                }
            }
        });
    }

    function filterData(data) {
        let name = $('#nameFilter').val();
        let type = $('#typeFilter').val();
        let workFrom = $('#workFromFilter').val();
        let date = $('#dateFilter').val();

        return data.filter(item => {
            return (!name || item.name === name) &&
                   (!type || item.type_name === type) &&
                   (!workFrom || item.userworkfrom === workFrom) &&
                   (!date || item.task_date.startsWith(date));
        });
    }

    function updateCharts(filteredData, charts) {
        charts.forEach(chartInfo => {
            let chart = chartInfo.chart;
            let field = chartInfo.field;
            if (chart.config.type === 'pie') {
                // Recalculate counts for pie charts
                let counts = countRequests(filteredData, field);
                chart.data.labels = Object.keys(counts);
                chart.data.datasets[0].data = Object.values(counts);
            } else if (field === 'newColumn') {
                // Update new column chart
                chart.data.labels = filteredData.map(item => `${item.name} - ${item.type_name} - ${item.userworkfrom} - ${item.task_date}`);
                chart.data.datasets.forEach(dataset => {
                    dataset.data = filteredData.map(item => item[dataset.label.toLowerCase().replace(/ /g, '_')]);
                });
            } else {
                // Update bar charts
                let data = filteredData.map(item => item[field]);
                chart.data.labels = filteredData.map(item => item.name);
                chart.data.datasets[0].data = data;
            }
            chart.update();
        });
    }
});

    </script>
</body>
</html>
