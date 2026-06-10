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
            width: 100%;
        }
    </style>
</head>
<body>

<?php include("nav.php"); ?>
<div class="container">
    <hr>
    <h1 class="text-center my-4">Todays Task vs Company Status Analysis</h1>
    <hr>
    <div class="col-md-12 text-center">
        <button class="btn btn-success" id="downloadPdf">Download PDF</button>
        <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="taskUserName">Task User Name</label>
                    <select id="taskUserName" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="userTypes">User Types</label>
                    <select id="userTypes" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="statusName">Status Name</label>
                    <select id="statusName" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="taskStatus">Task Status</label>
                    <select id="taskStatus" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="approvalStatus">Approval Status</label>
                    <select id="approvalStatus" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="autoTaskStatus">Auto Task Status</label>
                    <select id="autoTaskStatus" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="rejectStatus">Reject Status</label>
                    <select id="rejectStatus" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="adminSelfAssign">Admin Self-Assign</label>
                    <select id="adminSelfAssign" class="form-control">
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="taskAssignmentAfterReject">Task Assignment After Reject</label>
                    <select id="taskAssignmentAfterReject" class="form-control">
                    </select>
                </div>
            </div>
        </div>
    </div>
    <hr>
    </div>

    <div class="container-fluid">
    <div class="row">
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskLoadPerUserChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskApprovalRateChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskStatusByUserChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="userwiseTaskCompletionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="pendingVsCompletedChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="approvalStatusChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            <canvas id="taskOverviewChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            <canvas id="autoTaskOverviewChart"></canvas>
        </div>



        <div class="col-md-6 chart-container">
            <canvas id="actionPurposeChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="statusDistributionChart"></canvas>
        </div>



        <div class="col-md-6 chart-container">
            <canvas id="communicationTasksChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskTypeDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskStatusOverviewChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="rejectedTasksChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskAssignmentAfterRejectChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            <canvas id="actionPurposeComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            <canvas id="activityTypeOverviewChart"></canvas>
        </div>
      
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskCompletionRateChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskTypeByUserChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="rejectStatusOverviewChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="adminSelfAssignOverviewChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            <canvas id="taskAssignmentAfterRejectOverviewChart"></canvas>
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

<?php
$loadData = '';
foreach($getAllUserData as $cData){
    $loadData .= "{
        user_id: '{$cData->user_id}',
        task_user_name: '{$cData->task_user_name}',
        user_types: '{$cData->user_types}',
        status_name: '{$cData->status_name}',
        total_count: " . (int)$cData->total_count . ",
        task_pending: " . (int)$cData->task_pending . ",
        task_complete: " . (int)$cData->task_complete . ",
        approved_pending: " . (int)$cData->approved_pending . ",
        approved_done: " . (int)$cData->approved_done . ",
        total_auto_task_count: " . (int)$cData->total_auto_task_count . ",
        auto_task_pending: " . (int)$cData->auto_task_pending . ",
        auto_task_complete: " . (int)$cData->auto_task_complete . ",
        total_reject: " . (int)$cData->total_reject . ",
        pending_for_assign_after_reject_task: " . (int)$cData->pending_for_assign_after_reject_task . ",
        admin_create_request_for_self_assign: " . (int)$cData->admin_create_request_for_self_assign . ",
        task_assignd_by_admin_after_reject: " . (int)$cData->task_assignd_by_admin_after_reject . ",
        task_assignd_by_user_after_reject: " . (int)$cData->task_assignd_by_user_after_reject . ",
        action_yes_purpose_yes: " . (int)$cData->action_yes_purpose_yes . ",
        action_yes_purpose_no: " . (int)$cData->action_yes_purpose_no . ",
        action_no_purpose_no: " . (int)$cData->action_no_purpose_no . "
    },";
}
$loadData = rtrim($loadData, ',');

?>

<!-- Custom JS -->
<script>
   $(document).ready(function() {
    const data = [
       <?=$loadData;?>
    ];

    let filteredData = [...data];

    // Function to populate filter options dynamically
    function populateFilters() {
        const taskUserNames = [...new Set(data.map(item => item.task_user_name))];
        const userTypes = [...new Set(data.map(item => item.user_types))];
        const statusNames = [...new Set(data.map(item => item.status_name))];
        const rejectStatuses = [...new Set(data.map(item => item.total_reject > 0 ? 'Rejected' : 'Not Rejected'))];
        const adminSelfAssignStatuses = [...new Set(data.map(item => item.admin_create_request_for_self_assign > 0 ? 'Self-Assigned' : 'Not Self-Assigned'))];
        const taskAssignmentAfterRejectStatuses = [...new Set(data.map(item => item.task_assignd_by_admin_after_reject > 0 ? 'Admin Assigned' : item.task_assignd_by_user_after_reject > 0 ? 'User Assigned' : 'Not Assigned'))];

        // Populate Task User Name filter
        const taskUserNameSelect = $('#taskUserName');
        taskUserNameSelect.append('<option value="">All</option>');
        taskUserNames.forEach(name => {
            taskUserNameSelect.append(`<option value="${name}">${name}</option>`);
        });

        // Populate User Types filter
        const userTypesSelect = $('#userTypes');
        userTypesSelect.append('<option value="">All</option>');
        userTypes.forEach(type => {
            userTypesSelect.append(`<option value="${type}">${type}</option>`);
        });

        // Populate Status Name filter
        const statusNameSelect = $('#statusName');
        statusNameSelect.append('<option value="">All</option>');
        statusNames.forEach(status => {
            statusNameSelect.append(`<option value="${status}">${status}</option>`);
        });

        // Populate Task Status filter
        const taskStatusSelect = $('#taskStatus');
        taskStatusSelect.append('<option value="">All</option>');
        taskStatusSelect.append('<option value="pending">Pending</option>');
        taskStatusSelect.append('<option value="complete">Complete</option>');

        // Populate Approval Status filter
        const approvalStatusSelect = $('#approvalStatus');
        approvalStatusSelect.append('<option value="">All</option>');
        approvalStatusSelect.append('<option value="pending">Pending</option>');
        approvalStatusSelect.append('<option value="done">Done</option>');

        // Populate Auto Task Status filter
        const autoTaskStatusSelect = $('#autoTaskStatus');
        autoTaskStatusSelect.append('<option value="">All</option>');
        autoTaskStatusSelect.append('<option value="pending">Pending</option>');
        autoTaskStatusSelect.append('<option value="complete">Complete</option>');

        // Populate Reject Status filter
        const rejectStatusSelect = $('#rejectStatus');
        rejectStatusSelect.append('<option value="">All</option>');
        rejectStatuses.forEach(status => {
            rejectStatusSelect.append(`<option value="${status}">${status}</option>`);
        });

        // Populate Admin Self-Assign filter
        const adminSelfAssignSelect = $('#adminSelfAssign');
        adminSelfAssignSelect.append('<option value="">All</option>');
        adminSelfAssignStatuses.forEach(status => {
            adminSelfAssignSelect.append(`<option value="${status}">${status}</option>`);
        });

        // Populate Task Assignment After Reject filter
        const taskAssignmentAfterRejectSelect = $('#taskAssignmentAfterReject');
        taskAssignmentAfterRejectSelect.append('<option value="">All</option>');
        taskAssignmentAfterRejectStatuses.forEach(status => {
            taskAssignmentAfterRejectSelect.append(`<option value="${status}">${status}</option>`);
        });
    }

    function applyFilters() {
        const taskUserName = $('#taskUserName').val();
        const userTypes = $('#userTypes').val();
        const statusName = $('#statusName').val();
        const taskStatus = $('#taskStatus').val();
        const approvalStatus = $('#approvalStatus').val();
        const autoTaskStatus = $('#autoTaskStatus').val();
        const rejectStatus = $('#rejectStatus').val();
        const adminSelfAssign = $('#adminSelfAssign').val();
        const taskAssignmentAfterReject = $('#taskAssignmentAfterReject').val();

        filteredData = data.filter(item => {
            const matchesUserName = !taskUserName || item.task_user_name === taskUserName;
            const matchesUserTypes = !userTypes || item.user_types === userTypes;
            const matchesStatusName = !statusName || item.status_name === statusName;
            const matchesTaskStatus = !taskStatus || (taskStatus === 'pending' ? item.task_pending > 0 : item.task_complete > 0);
            const matchesApprovalStatus = !approvalStatus || (approvalStatus === 'pending' ? item.approved_pending > 0 : item.approved_done > 0);
            const matchesAutoTaskStatus = !autoTaskStatus || (autoTaskStatus === 'pending' ? item.auto_task_pending > 0 : item.auto_task_complete > 0);
            const matchesRejectStatus = !rejectStatus || (rejectStatus === 'Rejected' ? item.total_reject > 0 : item.total_reject === 0);
            const matchesAdminSelfAssign = !adminSelfAssign || (adminSelfAssign === 'Self-Assigned' ? item.admin_create_request_for_self_assign > 0 : item.admin_create_request_for_self_assign === 0);
            const matchesTaskAssignmentAfterReject = !taskAssignmentAfterReject || (taskAssignmentAfterReject === 'Admin Assigned' ? item.task_assignd_by_admin_after_reject > 0 : taskAssignmentAfterReject === 'User Assigned' ? item.task_assignd_by_user_after_reject > 0 : item.task_assignd_by_admin_after_reject === 0 && item.task_assignd_by_user_after_reject === 0);

            return matchesUserName && matchesUserTypes && matchesStatusName && matchesTaskStatus && matchesApprovalStatus && matchesAutoTaskStatus && matchesRejectStatus && matchesAdminSelfAssign && matchesTaskAssignmentAfterReject;
        });

        renderCharts();
    }

    // Attach event listeners to filter elements
    $('#taskUserName, #userTypes, #statusName, #taskStatus, #approvalStatus, #autoTaskStatus, #rejectStatus, #adminSelfAssign, #taskAssignmentAfterReject').change(applyFilters);

    function renderCharts() {
        const userTaskCompletion = {};
        const statusDistribution = {};
        const taskCompletionRate = {};
        const pendingVsCompleted = {};
        const approvalStatus = {};
        const taskTypeByUser = {};
        const taskLoadPerUser = {};
        const taskApprovalRate = {};
        const taskStatusByUser = {};
        const rejectStatusOverview = {};
        const adminSelfAssignOverview = {};
        const taskAssignmentAfterRejectOverview = {};

        filteredData.forEach(item => {
            const { task_user_name, status_name, total_count, task_pending, task_complete, approved_pending, approved_done, total_reject, admin_create_request_for_self_assign, task_assignd_by_admin_after_reject, task_assignd_by_user_after_reject } = item;

            if (!userTaskCompletion[task_user_name]) userTaskCompletion[task_user_name] = 0;
            userTaskCompletion[task_user_name] += parseInt(task_complete);

            if (!statusDistribution[status_name]) statusDistribution[status_name] = 0;
            statusDistribution[status_name] += parseInt(total_count);

            if (!taskCompletionRate[task_user_name]) taskCompletionRate[task_user_name] = { total: 0, completed: 0 };
            taskCompletionRate[task_user_name].total += parseInt(total_count);
            taskCompletionRate[task_user_name].completed += parseInt(task_complete);

            if (!pendingVsCompleted[task_user_name]) pendingVsCompleted[task_user_name] = { pending: 0, completed: 0 };
            pendingVsCompleted[task_user_name].pending += parseInt(task_pending);
            pendingVsCompleted[task_user_name].completed += parseInt(task_complete);

            if (!approvalStatus[task_user_name]) approvalStatus[task_user_name] = { pending: 0, approved: 0 };
            approvalStatus[task_user_name].pending += parseInt(approved_pending);
            approvalStatus[task_user_name].approved += parseInt(approved_done);

            if (!taskLoadPerUser[task_user_name]) taskLoadPerUser[task_user_name] = 0;
            taskLoadPerUser[task_user_name] += parseInt(total_count);

            if (!taskApprovalRate[task_user_name]) taskApprovalRate[task_user_name] = { total: 0, approved: 0 };
            taskApprovalRate[task_user_name].total += parseInt(total_count);
            taskApprovalRate[task_user_name].approved += parseInt(approved_done);

            if (!taskStatusByUser[task_user_name]) taskStatusByUser[task_user_name] = { pending: 0, completed: 0, approved: 0 };
            taskStatusByUser[task_user_name].pending += parseInt(task_pending);
            taskStatusByUser[task_user_name].completed += parseInt(task_complete);
            taskStatusByUser[task_user_name].approved += parseInt(approved_done);

            if (!rejectStatusOverview[task_user_name]) rejectStatusOverview[task_user_name] = { rejected: 0, notRejected: 0 };
            rejectStatusOverview[task_user_name].rejected += parseInt(total_reject);
            rejectStatusOverview[task_user_name].notRejected += parseInt(total_count) - parseInt(total_reject);

            if (!adminSelfAssignOverview[task_user_name]) adminSelfAssignOverview[task_user_name] = { selfAssigned: 0, notSelfAssigned: 0 };
            adminSelfAssignOverview[task_user_name].selfAssigned += parseInt(admin_create_request_for_self_assign);
            adminSelfAssignOverview[task_user_name].notSelfAssigned += parseInt(total_count) - parseInt(admin_create_request_for_self_assign);

            if (!taskAssignmentAfterRejectOverview[task_user_name]) taskAssignmentAfterRejectOverview[task_user_name] = { adminAssigned: 0, userAssigned: 0, notAssigned: 0 };
            taskAssignmentAfterRejectOverview[task_user_name].adminAssigned += parseInt(task_assignd_by_admin_after_reject);
            taskAssignmentAfterRejectOverview[task_user_name].userAssigned += parseInt(task_assignd_by_user_after_reject);
            taskAssignmentAfterRejectOverview[task_user_name].notAssigned += parseInt(total_count) - parseInt(task_assignd_by_admin_after_reject) - parseInt(task_assignd_by_user_after_reject);
        });

        // Destroy existing charts before rendering new ones
        Chart.helpers.each(Chart.instances, function(instance) {
            instance.destroy();
        });

        // Task Load per User Chart
        const taskLoadPerUserCtx = document.getElementById('taskLoadPerUserChart').getContext('2d');
        new Chart(taskLoadPerUserCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(taskLoadPerUser),
                datasets: [{
                    label: 'Task Load per User',
                    data: Object.values(taskLoadPerUser),
                    backgroundColor: '#FF6384',
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

        // Task Approval Rate Chart
        const taskApprovalRateCtx = document.getElementById('taskApprovalRateChart').getContext('2d');
        new Chart(taskApprovalRateCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(taskApprovalRate),
                datasets: [{
                    label: 'Task Approval Rate',
                    data: Object.values(taskApprovalRate).map(item => item.approved / item.total),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1
                    }
                }
            }
        });

        // Task Status by User Chart
        const taskStatusByUserCtx = document.getElementById('taskStatusByUserChart').getContext('2d');
        new Chart(taskStatusByUserCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(taskStatusByUser),
                datasets: [{
                    label: 'Pending Tasks',
                    data: Object.values(taskStatusByUser).map(item => item.pending),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }, {
                    label: 'Completed Tasks',
                    data: Object.values(taskStatusByUser).map(item => item.completed),
                    backgroundColor: '#36A2EB',
                    borderWidth: 1
                }, {
                    label: 'Approved Tasks',
                    data: Object.values(taskStatusByUser).map(item => item.approved),
                    backgroundColor: '#FFCE56',
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

        // User-wise Task Completion Chart
        const userwiseTaskCompletionCtx = document.getElementById('userwiseTaskCompletionChart').getContext('2d');
        new Chart(userwiseTaskCompletionCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(userTaskCompletion),
                datasets: [{
                    label: 'User-wise Task Completion',
                    data: Object.values(userTaskCompletion),
                    backgroundColor: '#FF6384',
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

        // Pending vs. Completed Tasks Chart
        const pendingVsCompletedCtx = document.getElementById('pendingVsCompletedChart').getContext('2d');
        new Chart(pendingVsCompletedCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(pendingVsCompleted),
                datasets: [{
                    label: 'Pending Tasks',
                    data: Object.values(pendingVsCompleted).map(item => item.pending),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }, {
                    label: 'Completed Tasks',
                    data: Object.values(pendingVsCompleted).map(item => item.completed),
                    backgroundColor: '#36A2EB',
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

        // Approval Status Chart
        const approvalStatusCtx = document.getElementById('approvalStatusChart').getContext('2d');
        new Chart(approvalStatusCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(approvalStatus),
                datasets: [{
                    label: 'Pending Approval',
                    data: Object.values(approvalStatus).map(item => item.pending),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }, {
                    label: 'Approved',
                    data: Object.values(approvalStatus).map(item => item.approved),
                    backgroundColor: '#36A2EB',
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

        // Task Overview Chart
        const taskOverviewCtx = document.getElementById('taskOverviewChart').getContext('2d');
        new Chart(taskOverviewCtx, {
            type: 'bar',
            data: {
                labels: ['Total Tasks', 'Approved Tasks', 'Complete Tasks', 'Pending Tasks'],
                datasets: [{
                    label: 'Task Overview',
                    data: [
                        filteredData.reduce((sum, item) => sum + item.total_count, 0),
                        filteredData.reduce((sum, item) => sum + item.approved_done, 0),
                        filteredData.reduce((sum, item) => sum + item.task_complete, 0),
                        filteredData.reduce((sum, item) => sum + item.task_pending, 0)
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
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

        // Auto Task Overview Chart
        const autoTaskOverviewCtx = document.getElementById('autoTaskOverviewChart').getContext('2d');
        new Chart(autoTaskOverviewCtx, {
            type: 'bar',
            data: {
                labels: ['Total Auto Tasks', 'Complete Auto Tasks', 'Pending Auto Tasks'],
                datasets: [{
                    label: 'Auto Task Overview',
                    data: [
                        filteredData.reduce((sum, item) => sum + item.total_auto_task_count, 0),
                        filteredData.reduce((sum, item) => sum + item.auto_task_complete, 0),
                        filteredData.reduce((sum, item) => sum + item.auto_task_pending, 0)
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
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

        // Action Purpose Overview Chart
        const actionPurposeCtx = document.getElementById('actionPurposeChart').getContext('2d');
        new Chart(actionPurposeCtx, {
            type: 'pie',
            data: {
                labels: ['Action Yes Purpose Yes', 'Action Yes Purpose No', 'Action No Purpose No'],
                datasets: [{
                    label: 'Action Purpose Overview',
                    data: [
                        filteredData.reduce((sum, item) => sum + item.action_yes_purpose_yes, 0),
                        filteredData.reduce((sum, item) => sum + item.action_yes_purpose_no, 0),
                        filteredData.reduce((sum, item) => sum + item.action_no_purpose_no, 0)
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]
            }
        });

        // Communication Tasks Chart
        const communicationTasksCtx = document.getElementById('communicationTasksChart').getContext('2d');
        new Chart(communicationTasksCtx, {
            type: 'doughnut',
            data: {
                labels: ['Call Tasks', 'Email Tasks', 'Scheduled Meeting Tasks', 'Barg Meeting Tasks', 'WhatsApp Activity'],
                datasets: [{
                    label: 'Communication Tasks',
                    data: [
                        filteredData.filter(item => item.status_name === 'Call Tasks').length,
                        filteredData.filter(item => item.status_name === 'Email Tasks').length,
                        filteredData.filter(item => item.status_name === 'Scheduled Meeting Tasks').length,
                        filteredData.filter(item => item.status_name === 'Barg Meeting Tasks').length,
                        filteredData.filter(item => item.status_name === 'WhatsApp Activity').length
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                }]
            }
        });

        // Task Type Distribution Chart
        const taskTypeDistributionCtx = document.getElementById('taskTypeDistributionChart').getContext('2d');
        new Chart(taskTypeDistributionCtx, {
            type: 'polarArea',
            data: {
                labels: ['Call Tasks', 'Email Tasks', 'Scheduled Meeting Tasks', 'Barg Meeting Tasks', 'WhatsApp Activity'],
                datasets: [{
                    label: 'Task Type Distribution',
                    data: [
                        filteredData.filter(item => item.status_name === 'Call Tasks').length,
                        filteredData.filter(item => item.status_name === 'Email Tasks').length,
                        filteredData.filter(item => item.status_name === 'Scheduled Meeting Tasks').length,
                        filteredData.filter(item => item.status_name === 'Barg Meeting Tasks').length,
                        filteredData.filter(item => item.status_name === 'WhatsApp Activity').length
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                }]
            }
        });

        // Task Status Overview Chart
        const taskStatusOverviewCtx = document.getElementById('taskStatusOverviewChart').getContext('2d');
        new Chart(taskStatusOverviewCtx, {
            type: 'bar',
            data: {
                labels: ['Pending Approved', 'Pending Tasks', 'Pending Auto Tasks'],
                datasets: [{
                    label: 'Task Status Overview',
                    data: [
                        filteredData.reduce((sum, item) => sum + item.approved_pending, 0),
                        filteredData.reduce((sum, item) => sum + item.task_pending, 0),
                        filteredData.reduce((sum, item) => sum + item.auto_task_pending, 0)
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
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

        // Rejected Tasks Overview Chart
        const rejectedTasksCtx = document.getElementById('rejectedTasksChart').getContext('2d');
        new Chart(rejectedTasksCtx, {
            type: 'bar',
            data: {
                labels: ['Total Rejected', 'Pending for Assign After Reject'],
                datasets: [{
                    label: 'Rejected Tasks Overview',
                    data: [
                        filteredData.reduce((sum, item) => sum + item.total_reject, 0),
                        filteredData.reduce((sum, item) => sum + item.pending_for_assign_after_reject_task, 0)
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB'],
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

        // Task Assignment After Reject Chart
        const taskAssignmentAfterRejectCtx = document.getElementById('taskAssignmentAfterRejectChart').getContext('2d');
        new Chart(taskAssignmentAfterRejectCtx, {
            type: 'bar',
            data: {
                labels: ['Admin Assigned', 'User Assigned'],
                datasets: [{
                    label: 'Task Assignment After Reject',
                    data: [
                        filteredData.reduce((sum, item) => sum + item.task_assignd_by_admin_after_reject, 0),
                        filteredData.reduce((sum, item) => sum + item.task_assignd_by_user_after_reject, 0)
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB'],
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

        // Action Purpose Comparison Chart
        const actionPurposeComparisonCtx = document.getElementById('actionPurposeComparisonChart').getContext('2d');
        new Chart(actionPurposeComparisonCtx, {
            type: 'radar',
            data: {
                labels: ['Action Yes Purpose Yes', 'Action Yes Purpose No', 'Action No Purpose No'],
                datasets: [{
                    label: 'Action Purpose Comparison',
                    data: [
                        filteredData.reduce((sum, item) => sum + item.action_yes_purpose_yes, 0),
                        filteredData.reduce((sum, item) => sum + item.action_yes_purpose_no, 0),
                        filteredData.reduce((sum, item) => sum + item.action_no_purpose_no, 0)
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    r: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Activity Type Overview Chart
        const activityTypeOverviewCtx = document.getElementById('activityTypeOverviewChart').getContext('2d');
        new Chart(activityTypeOverviewCtx, {
            type: 'bar',
            data: {
                labels: ['Write MOM', 'Write Proposal'],
                datasets: [{
                    label: 'Activity Type Overview',
                    data: [
                        filteredData.filter(item => item.status_name === 'Write MOM').length,
                        filteredData.filter(item => item.status_name === 'Write Proposal').length
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB'],
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

        // Status Distribution Chart
        const statusDistributionCtx = document.getElementById('statusDistributionChart').getContext('2d');
        new Chart(statusDistributionCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusDistribution),
                datasets: [{
                    label: 'Status Distribution',
                    data: Object.values(statusDistribution),
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            }
        });

        // Task Completion Rate Chart
        const taskCompletionRateCtx = document.getElementById('taskCompletionRateChart').getContext('2d');
        new Chart(taskCompletionRateCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(taskCompletionRate),
                datasets: [{
                    label: 'Task Completion Rate',
                    data: Object.values(taskCompletionRate).map(item => item.completed / item.total),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1
                    }
                }
            }
        });

        // Task Type by User Chart
        const taskTypeByUserCtx = document.getElementById('taskTypeByUserChart').getContext('2d');
        new Chart(taskTypeByUserCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(taskTypeByUser),
                datasets: [{
                    label: 'Task Type by User',
                    data: Object.values(taskTypeByUser),
                    backgroundColor: '#FF6384',
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

        // Reject Status Overview Chart
        const rejectStatusOverviewCtx = document.getElementById('rejectStatusOverviewChart').getContext('2d');
        new Chart(rejectStatusOverviewCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(rejectStatusOverview),
                datasets: [{
                    label: 'Rejected Tasks',
                    data: Object.values(rejectStatusOverview).map(item => item.rejected),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }, {
                    label: 'Not Rejected Tasks',
                    data: Object.values(rejectStatusOverview).map(item => item.notRejected),
                    backgroundColor: '#36A2EB',
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

        // Admin Self-Assign Overview Chart
        const adminSelfAssignOverviewCtx = document.getElementById('adminSelfAssignOverviewChart').getContext('2d');
        new Chart(adminSelfAssignOverviewCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(adminSelfAssignOverview),
                datasets: [{
                    label: 'Self-Assigned Tasks',
                    data: Object.values(adminSelfAssignOverview).map(item => item.selfAssigned),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }, {
                    label: 'Not Self-Assigned Tasks',
                    data: Object.values(adminSelfAssignOverview).map(item => item.notSelfAssigned),
                    backgroundColor: '#36A2EB',
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

        // Task Assignment After Reject Overview Chart
        const taskAssignmentAfterRejectOverviewCtx = document.getElementById('taskAssignmentAfterRejectOverviewChart').getContext('2d');
        new Chart(taskAssignmentAfterRejectOverviewCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(taskAssignmentAfterRejectOverview),
                datasets: [{
                    label: 'Admin Assigned Tasks',
                    data: Object.values(taskAssignmentAfterRejectOverview).map(item => item.adminAssigned),
                    backgroundColor: '#FF6384',
                    borderWidth: 1
                }, {
                    label: 'User Assigned Tasks',
                    data: Object.values(taskAssignmentAfterRejectOverview).map(item => item.userAssigned),
                    backgroundColor: '#36A2EB',
                    borderWidth: 1
                }, {
                    label: 'Not Assigned Tasks',
                    data: Object.values(taskAssignmentAfterRejectOverview).map(item => item.notAssigned),
                    backgroundColor: '#FFCE56',
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

    // Populate filters and initial render
    populateFilters();
    renderCharts();

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
        const chartIds = ['taskOverviewChart','autoTaskOverviewChart','actionPurposeChart','communicationTasksChart','taskTypeDistributionChart','taskStatusOverviewChart','rejectedTasksChart','taskAssignmentAfterRejectChart','actionPurposeComparisonChart','activityTypeOverviewChart','userwiseTaskCompletionChart','statusDistributionChart','taskCompletionRateChart','pendingVsCompletedChart','approvalStatusChart','taskTypeByUserChart','taskLoadPerUserChart','taskApprovalRateChart','taskStatusByUserChart','rejectStatusOverviewChart','adminSelfAssignOverviewChart','taskAssignmentAfterRejectOverviewChart'];
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
