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
            /* height: 40vh; */
            width: 100%;
        }
    </style>
</head>
<body>

<?php
$dataJson = json_encode($sums);
?>

<?php include("nav.php"); ?>
<div class="container">
    <hr>
    <h1 class="text-center my-4">Total Team Task Graph</h1>
    <hr>
    <div class="col-md-12 text-center">
                    <button class="btn btn-success" id="downloadPdf">Download PDF</button>
                    <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
                </div>
    <hr>
    <div class="row">
        <div class="col-md-6 chart-container">
            Task Overview
            <canvas id="taskOverviewChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Auto Task Overview
            <canvas id="autoTaskOverviewChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Action Purpose Overview
            <canvas id="actionPurposeChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
             Tasks Type
            <canvas id="communicationTasksChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Type Distribution
            <canvas id="taskTypeDistributionChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Status Overview
            <canvas id="taskStatusOverviewChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Rejected Tasks Overview
            <canvas id="rejectedTasksChart"></canvas>
        </div>
        <div class="col-md-6 chart-container mt-5">
            Task Assignment After Reject
            <canvas id="taskAssignmentAfterRejectChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Action Purpose Comparison
            <canvas id="actionPurposeComparisonChart"></canvas>
        </div>
        <div class="col-md-6 chart-container">
            Activity Type Overview
            <canvas id="activityTypeOverviewChart"></canvas>
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

        function renderCharts() {
            // Task Overview Chart
            const taskOverviewCtx = document.getElementById('taskOverviewChart').getContext('2d');
            new Chart(taskOverviewCtx, {
                type: 'bar',
                data: {
                    labels: ['Total Tasks', 'Approved Tasks', 'Complete Tasks', 'Pending Tasks'],
                    datasets: [{
                        label: 'Task Overview',
                        data: [data.total_task, data.approved_task, data.complete_task, data.pending_task],
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
                        data: [data.total_autotask, data.complete_autotask, data.pending_autotask],
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
                        data: [data.action_yes_purpose_yes, data.action_yes_purpose_no, data.action_no_purpose_no],
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
                        data: [data.call_task, data.email_task, data.scheduled_meeting_task, data.barg_meeting_task, data.whatsapp_activity],
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
                        data: [data.call_task, data.email_task, data.scheduled_meeting_task, data.barg_meeting_task, data.whatsapp_activity],
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
                        data: [data.pending_approved, data.pending_task, data.pending_autotask],
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
                        data: [data.total_reject, data.pending_for_assign_after_reject_task],
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
                        data: [data.task_assignd_by_admin_after_reject, data.task_assignd_by_user_after_reject],
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
                        data: [data.action_yes_purpose_yes, data.action_yes_purpose_no, data.action_no_purpose_no],
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
                        data: [data.write_mom, data.write_proposal],
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
        }

        // Initial render
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
            const chartIds = ['taskOverviewChart','autoTaskOverviewChart','actionPurposeChart','communicationTasksChart','taskTypeDistributionChart','taskStatusOverviewChart','rejectedTasksChart','taskAssignmentAfterRejectChart','actionPurposeComparisonChart','activityTypeOverviewChart'];
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
