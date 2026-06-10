<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
</head>
<body>

<?php
// Ensure $getAllUserData is defined before using it
$getAllUserData = isset($getAllUserData) ? $getAllUserData : [];
$usernames = array_unique(array_column($getAllUserData, 'name'));
$type_names = array_unique(array_column($getAllUserData, 'type_name'));
$userworkfroms = array_unique(array_column($getAllUserData, 'userworkfrom'));

$loadData = [];
foreach($getAllUserData as $userData){
    $loadData[] = [
        "name" => $userData->name,
        "type_name" => $userData->type_name,
        "userworkfrom" => $userData->userworkfrom,
        "total_task" => $userData->total_task,
        "approved_task" => $userData->approved_task,
        "pending_approved" => $userData->pending_approved,
        "complete_task" => $userData->complete_task,
        "pending_task" => $userData->pending_task,
        "total_reject" => $userData->total_reject,
        "total_autotask" => $userData->total_autotask,
        "after_task" => $userData->after_task,
        "complete_autotask" => $userData->complete_autotask,
        "pending_autotask" => $userData->pending_autotask,
        "pending_for_assign_after_reject_task" => $userData->pending_for_assign_after_reject_task,
        "admin_create_request_for_self_assign" => $userData->admin_create_request_for_self_assign,
        "task_assignd_by_admin_after_reject" => $userData->task_assignd_by_admin_after_reject,
        "task_assignd_by_user_after_reject" => $userData->task_assignd_by_user_after_reject,
        "action_yes_purpose_yes" => $userData->action_yes_purpose_yes,
        "action_yes_purpose_no" => $userData->action_yes_purpose_no,
        "action_no_purpose_no" => $userData->action_no_purpose_no,
        "call_task" => $userData->call_task,
        "email_task" => $userData->email_task,
        "scheduled_meeting_task" => $userData->scheduled_meeting_task,
        "barg_meeting_task" => $userData->barg_meeting_task,
        "whatsapp_activity" => $userData->whatsapp_activity,
        "write_mom" => $userData->write_mom,
        "write_proposal" => $userData->write_proposal,
        "research_task" => $userData->research_task,
        "documentation_task" => $userData->documentation_task,
        "social_networking_task" => $userData->social_networking_task,
        "social_activity_task" => $userData->social_activity_task,
        "annual_review_target_task" => $userData->annual_review_target_task,
        "join_meeting_task" => $userData->join_meeting_task,
        "mom_check_task" => $userData->mom_check_task,
        "create_bd_request_task" => $userData->create_bd_request_task,
        "submit_handover_task" => $userData->submit_handover_task,
        "praposal_check_task" => $userData->praposal_check_task,
        "total_task_time" => $userData->total_task_time,
        "total_plan_time_on_planner" => $userData->total_plan_time_on_planner,
        "total_plan_time_with_autotask" => $userData->total_plan_time_with_autotask,
        "task_plan_for_today_request" => $userData->task_plan_for_today_request,
        "user_create_special_request_for_leave" => $userData->user_create_special_request_for_leave,
        "user_request_any_reminder" => $userData->user_request_any_reminder,
        "pending_task_planner_request" => $userData->pending_task_planner_request,
        "session_count" => $userData->session_count,
        "task_date" => $userData->task_date // Ensure task_date is included
    ];
}
?>

<?php include ("nav.php"); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3>Get All User All Time Day Activity On APP Graph</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">User Department:</label>
                    <select id="typeFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($type_names as $type_name) { echo "<option value='$type_name'>$type_name</option>"; } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User Name:</label>
                    <select id="userFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($usernames as $username) { echo "<option value='$username'>$username</option>"; } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User Work From:</label>
                    <select id="userworkfromFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($userworkfroms as $userworkfrom) { echo "<option value='$userworkfrom'>$userworkfrom</option>"; } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year:</label>
                    <select id="yearFilter" class="form-select">
                        <option value="all">All</option>
                        <?php
                        $currentYear = date('Y');
                        for ($i = 0; $i < 5; $i++) {
                            echo "<option value='" . ($currentYear - $i) . "'>" . ($currentYear - $i) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <button class="btn btn-success" id="downloadPdf">Download PDF</button>
                    <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
                </div>
            </div>
            </div>
            <br>
            <hr>
            <br>
            <div class="row">
                <div class="col-md-12"><canvas id="barChart"></canvas></div>
                <div class="col-md-12"><canvas id="lineChart"></canvas></div>
                <div class="col-md-12"><canvas id="stackedBarChart"></canvas></div>
                <div class="col-md-12"><canvas id="areaChart"></canvas></div>
                <div class="col-md-12"><canvas id="pieChart"></canvas></div>
                <div class="col-md-12"><canvas id="doughnutChart"></canvas></div>
                <div class="col-md-12"><canvas id="scatterPlot"></canvas></div>
                <div class="col-md-12"><canvas id="bubbleChart"></canvas></div>
                <div class="col-md-12"><canvas id="radarChart"></canvas></div>
                <div class="col-md-12"><canvas id="polarAreaChart"></canvas></div>
                <div class="col-md-12"><canvas id="groupedBarChart"></canvas></div>
                <div class="col-md-12"><canvas id="stackedAreaChart"></canvas></div>
                <div class="col-md-12"><canvas id="multiLineChart"></canvas></div>
                <!-- Add more canvas elements as needed -->
            </div>
        </div>
    </div>
</div>
<?php include ("footer.php"); ?>
<script>
$(document).ready(function() {
    let data = <?php echo json_encode($loadData); ?>;
    let charts = {};

    function createChart(chartId, type, dataset, options = {}) {
        if (charts[chartId]) charts[chartId].destroy();
        let ctx = document.getElementById(chartId).getContext('2d');
        charts[chartId] = new Chart(ctx, {
            type: type,
            data: dataset,
            options: { responsive: true, animation: { duration: 1000 }, ...options }
        });
    }

    function updateCharts() {
        let typeFilter = $("#typeFilter").val();
        let userFilter = $("#userFilter").val();
        let userworkfromFilter = $("#userworkfromFilter").val();
        let yearFilter = $("#yearFilter").val();

        let filteredData = data.filter(item => {
            let userworkfromMatch = userworkfromFilter === "all" || item.userworkfrom === userworkfromFilter;
            let typeMatch = typeFilter === "all" || item.type_name === typeFilter;
            let userMatch = userFilter === "all" || item.name === userFilter;
            let yearMatch = yearFilter === "all" || new Date(item.task_date).getFullYear() == yearFilter;
            return typeMatch && userMatch && userworkfromMatch && yearMatch;
        });

        let labels = filteredData.map(item => item.name);
        let type_name = filteredData.map(item => item.type_name);
        let userworkfrom = filteredData.map(item => item.userworkfrom);
        let totalTasks = filteredData.map(item => item.total_task);
        let completedTasks = filteredData.map(item => item.complete_task);
        let pendingTasks = filteredData.map(item => item.pending_task);
        let approvedTasks = filteredData.map(item => item.approved_task);
        let pendingApproved = filteredData.map(item => item.pending_approved);
        let rejectedTasks = filteredData.map(item => item.total_reject);
        let total_autotask = filteredData.map(item => item.total_autotask);
        let after_task = filteredData.map(item => item.after_task);
        let complete_autotask = filteredData.map(item => item.complete_autotask);
        let pending_autotask = filteredData.map(item => item.pending_autotask);
        let pending_for_assign_after_reject_task = filteredData.map(item => item.pending_for_assign_after_reject_task);
        let admin_create_request_for_self_assign = filteredData.map(item => item.admin_create_request_for_self_assign);
        let task_assignd_by_admin_after_reject = filteredData.map(item => item.task_assignd_by_admin_after_reject);
        let task_assignd_by_user_after_reject = filteredData.map(item => item.task_assignd_by_user_after_reject);
        let action_yes_purpose_yes = filteredData.map(item => item.action_yes_purpose_yes);
        let action_yes_purpose_no = filteredData.map(item => item.action_yes_purpose_no);
        let action_no_purpose_no = filteredData.map(item => item.action_no_purpose_no);
        let call_task = filteredData.map(item => item.call_task);
        let email_task = filteredData.map(item => item.email_task);
        let scheduled_meeting_task = filteredData.map(item => item.scheduled_meeting_task);
        let barg_meeting_task = filteredData.map(item => item.barg_meeting_task);
        let whatsapp_activity = filteredData.map(item => item.whatsapp_activity);
        let write_mom = filteredData.map(item => item.write_mom);
        let write_proposal = filteredData.map(item => item.write_proposal);
        let research_task = filteredData.map(item => item.research_task);
        let documentation_task = filteredData.map(item => item.documentation_task);
        let social_networking_task = filteredData.map(item => item.social_networking_task);
        let social_activity_task = filteredData.map(item => item.social_activity_task);
        let annual_review_target_task = filteredData.map(item => item.annual_review_target_task);
        let join_meeting_task = filteredData.map(item => item.join_meeting_task);
        let mom_check_task = filteredData.map(item => item.mom_check_task);
        let create_bd_request_task = filteredData.map(item => item.create_bd_request_task);
        let submit_handover_task = filteredData.map(item => item.submit_handover_task);
        let praposal_check_task = filteredData.map(item => item.praposal_check_task);

        let colors = [
            'rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)', 'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)',
            'rgba(36, 230, 62, 0.6)', 'rgba(220, 230, 36, 0.6)', 'rgba(255, 4, 0, 0.6)',
            'rgba(255, 4, 0, 0.26)', 'rgba(255, 4, 0, 0.11)', 'rgb(255, 4, 0)', 'rgb(132, 60, 59)',
            'rgb(26, 3, 2)', 'rgb(26, 43, 42)', 'rgba(0, 255, 255, 0.6)', 'rgba(0, 128, 128, 0.6)',
            'rgba(255, 165, 0, 0.6)', 'rgba(34, 193, 195, 0.6)', 'rgba(253, 187, 45, 0.6)',
            'rgba(123, 31, 162, 0.6)', 'rgba(239, 83, 80, 0.6)', 'rgba(0, 121, 107, 0.6)',
            'rgba(63, 81, 181, 0.6)', 'rgba(255, 87, 34, 0.6)', 'rgba(156, 39, 176, 0.6)',
            'rgba(103, 58, 183, 0.6)', 'rgba(233, 30, 99, 0.6)', 'rgba(0, 188, 212, 0.6)'
        ];

        let datasets = [
            { label: "Type Name", data: type_name, backgroundColor: colors[16] },
            { label: "User Work From", data: userworkfrom, backgroundColor: colors[16] },
            { label: "Total Task", data: totalTasks, backgroundColor: colors[0] },
            { label: "Completed", data: completedTasks, backgroundColor: colors[1] },
            { label: "Pending", data: pendingTasks, backgroundColor: colors[2] },
            { label: "Approved Task", data: approvedTasks, backgroundColor: colors[3] },
            { label: "Pending Approved", data: pendingApproved, backgroundColor: colors[4] },
            { label: "Rejected Task", data: rejectedTasks, backgroundColor: colors[5] },
            { label: "Total Auto Task", data: total_autotask, backgroundColor: colors[6] },
            { label: "After Task", data: after_task, backgroundColor: colors[7] },
            { label: "Complete Auto Task", data: complete_autotask, backgroundColor: colors[8] },
            { label: "Pending Autotask", data: pending_autotask, backgroundColor: colors[9] },
            { label: "Pending for Assign After Reject Task", data: pending_for_assign_after_reject_task, backgroundColor: colors[10] },
            { label: "Admin Create Request for Self Assign", data: admin_create_request_for_self_assign, backgroundColor: colors[11] },
            { label: "Task Assigned by Admin After Reject", data: task_assignd_by_admin_after_reject, backgroundColor: colors[12] },
            { label: "Task Assigned by User After Reject", data: task_assignd_by_user_after_reject, backgroundColor: colors[13] },
            { label: "Action Yes Purpose Yes", data: action_yes_purpose_yes, backgroundColor: colors[14] },
            { label: "Action Yes Purpose No", data: action_yes_purpose_no, backgroundColor: colors[15] },
            { label: "Action No Purpose No", data: action_no_purpose_no, backgroundColor: colors[16] },
            { label: "Call Task", data: call_task, backgroundColor: colors[17] },
            { label: "Email Task", data: email_task, backgroundColor: colors[18] },
            { label: "Scheduled Meeting Task", data: scheduled_meeting_task, backgroundColor: colors[19] },
            { label: "Barg Meeting Task", data: barg_meeting_task, backgroundColor: colors[20] },
            { label: "Whatsapp Activity", data: whatsapp_activity, backgroundColor: colors[21] },
            { label: "Write MOM", data: write_mom, backgroundColor: colors[22] },
            { label: "Write Proposal", data: write_proposal, backgroundColor: colors[23] },
            { label: "Research Task", data: research_task, backgroundColor: colors[24] },
            { label: "Documentation Task", data: documentation_task, backgroundColor: colors[25] },
            { label: "Social Networking Task", data: social_networking_task, backgroundColor: colors[26] },
            { label: "Social Activity Task", data: social_activity_task, backgroundColor: colors[27] },
            { label: "Annual Review Target Task", data: annual_review_target_task, backgroundColor: colors[28] },
            { label: "Join Meeting Task", data: join_meeting_task, backgroundColor: colors[29] },
            { label: "MOM Check Task", data: mom_check_task, backgroundColor: colors[0] },
            { label: "Create BD Request Task", data: create_bd_request_task, backgroundColor: colors[1] },
            { label: "Submit Handover Task", data: submit_handover_task, backgroundColor: colors[2] },
            { label: "Proposal Check Task", data: praposal_check_task, backgroundColor: colors[3] }
        ];

        let barDataset = {
            labels: labels,
            datasets: datasets
        };

        let lineDataset = {
            labels: labels,
            datasets: datasets
        };

        let areaDataset = {
            labels: labels,
            datasets: [{
                label: "Total Task",
                data: totalTasks,
                backgroundColor: colors[0],
                fill: true
            }]
        };

        let pieDataset = {
            labels: labels,
            datasets: [{
                data: totalTasks,
                backgroundColor: colors
            }]
        };

        let doughnutDataset = {
            labels: labels,
            datasets: [{
                data: totalTasks,
                backgroundColor: colors
            }]
        };

        let scatterDataset = {
            datasets: [{
                label: "Completed vs Pending Tasks",
                data: filteredData.map(item => ({
                    x: item.complete_task,
                    y: item.pending_task
                })),
                backgroundColor: colors[0]
            }]
        };

        let bubbleDataset = {
            datasets: [{
                label: "Bubble Chart",
                data: filteredData.map(item => ({
                    x: item.complete_task,
                    y: item.pending_task,
                    r: item.total_task / 10
                })),
                backgroundColor: colors[0]
            }]
        };

        let radarDataset = {
            labels: labels,
            datasets: [{
                label: "Task Distribution",
                data: totalTasks,
                backgroundColor: colors[0]
            }]
        };

        let polarAreaDataset = {
            labels: labels,
            datasets: [{
                data: totalTasks,
                backgroundColor: colors
            }]
        };

        let groupedBarDataset = {
            labels: labels,
            datasets: [
                { label: "Completed Tasks", data: completedTasks, backgroundColor: colors[0] },
                { label: "Pending Tasks", data: pendingTasks, backgroundColor: colors[1] }
            ]
        };

        let stackedAreaDataset = {
            labels: labels,
            datasets: [
                { label: "Completed Tasks", data: completedTasks, backgroundColor: colors[0] },
                { label: "Pending Tasks", data: pendingTasks, backgroundColor: colors[1] }
            ]
        };

        let multiLineDataset = {
            labels: labels,
            datasets: [
                { label: "Completed Tasks", data: completedTasks, backgroundColor: colors[0] },
                { label: "Pending Tasks", data: pendingTasks, backgroundColor: colors[1] }
            ]
        };

        createChart("barChart", "bar", barDataset);
        createChart("lineChart", "line", lineDataset);
        createChart("stackedBarChart", "bar", barDataset, { scales: { x: { stacked: true }, y: { stacked: true } } });
        createChart("areaChart", "line", areaDataset, { fill: true });
        createChart("pieChart", "pie", pieDataset);
        createChart("doughnutChart", "doughnut", doughnutDataset);
        createChart("scatterPlot", "scatter", scatterDataset);
        createChart("bubbleChart", "bubble", bubbleDataset);
        createChart("radarChart", "radar", radarDataset);
        createChart("polarAreaChart", "polarArea", polarAreaDataset);
        createChart("groupedBarChart", "bar", groupedBarDataset);
        createChart("stackedAreaChart", "line", stackedAreaDataset, { fill: true, scales: { x: { stacked: true }, y: { stacked: true } } });
        createChart("multiLineChart", "line", multiLineDataset);
    }

    updateCharts();
    $(".form-select").on("change", updateCharts);

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
            const chartIds = ['barChart', 'pieChart', 'lineChart', 'doughnutChart', 'radarChart',
                              'polarChart', 'bubbleChart', 'scatterChart','scatterPlot','polarAreaChart','groupedBarChart','stackedAreaChart','multiLineChart','stackedBarChart',
                              'areaChart', 'funnelChart', 'heatmapChart'];
            chartIds.forEach(chartId => {
                const chartCanvas = document.getElementById(chartId);
                const chartImage = chartCanvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = chartId + '.png';
                link.href = chartImage;
                link.click();
            });
        });
});

</script>
</body>
</html>
