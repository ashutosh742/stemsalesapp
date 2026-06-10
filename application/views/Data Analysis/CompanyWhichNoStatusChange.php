<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Analytics Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- html2canvas and jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

</head>
<body>

<?php
$uniqueRemarks = array_unique(array_column($alllogData, 'remarks'));
$last_task_days = array_unique(array_column($alllogData, 'last_task_days'));
$compnames = array_unique(array_column($alllogData, 'compname'));
$mainbd_names = array_unique(array_column($alllogData, 'mainbd_name'));
$current_status = array_unique(array_column($alllogData, 'current_status'));
$pnames = array_unique(array_column($alllogData, 'pname'));
?>

<?php include ("nav.php") ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3>Compulsive Task & Need Your Attention</h3>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Company:</label>
                    <select id="companyFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($compnames as $compname) { ?>
                            <option value="<?=$compname?>"><?=$compname?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Partner:</label>
                    <select id="partnerFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($pnames as $pname) { ?>
                            <option value="<?=$pname?>"><?=$pname?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category:</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($category as $categorys) { ?>
                            <option value="<?=$categorys->dataname?>"><?=$categorys->name?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Username:</label>
                    <select id="usernameFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($mainbd_names as $mainbd_name) { ?>
                            <option value="<?=$mainbd_name?>"><?=$mainbd_name?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status:</label>
                    <select id="statusFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($current_status as $status) { ?>
                            <option value="<?=$status?>"><?=$status?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Days Since Last Task:</label>
                    <select id="daysFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($last_task_days as $last_task_day) { ?>
                            <option value="<?=$last_task_day?>"><?=$last_task_day?> days</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Filter Type</label>
                    <select id="reamrksFilter" class="form-select">
                        <option value="all">All</option>
                        <?php foreach ($uniqueRemarks as $uniqueRemark) { ?>
                            <option value="<?=$uniqueRemark?>"><?=$uniqueRemark?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <button class="btn btn-success" id="downloadPdf">Download PDF</button>
                    <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
                </div>
            </div>
            </div>

            

            <!-- Graphs Grid -->
            <div class="row">
                <div class="col-md-12"><canvas id="barChart"></canvas></div>
                <div class="col-md-12"><canvas id="pieChart"></canvas></div>
                <div class="col-md-12"><canvas id="lineChart"></canvas></div>
                <div class="col-md-12"><canvas id="doughnutChart"></canvas></div>
                <div class="col-md-12"><canvas id="radarChart"></canvas></div>
                <div class="col-md-12"><canvas id="polarChart"></canvas></div>
                <div class="col-md-12"><canvas id="bubbleChart"></canvas></div>
                <div class="col-md-12"><canvas id="scatterChart"></canvas></div>
                <div class="col-md-12"><canvas id="stackedBarChart"></canvas></div>
                <div class="col-md-12"><canvas id="areaChart"></canvas></div>
                <div class="col-md-12"><canvas id="funnelChart"></canvas></div>
                <div class="col-md-12"><canvas id="heatmapChart"></canvas></div>
            </div>

           
        </div>
    </div>
</div>

<?php include ("footer.php") ?>

<?php
// dd($alllogData);
$loadData = '';
foreach($alllogData as $cData){
    $loadData .= "{ compname: '{$cData->compname}', partner: '{$cData->pname}',
    username: '{$cData->mainbd_name}',
    last_task_days: {$cData->last_task_days},
    remarks: '{$cData->remarks}',
    status: '{$cData->current_status}' },";
}
$loadData = rtrim($loadData, ',');
?>

<script>
    $(document).ready(function() {
        let dummyData = [ <?=$loadData?> ];
        let charts = {};

        function createChart(chartId, type, labels, data) {
            if (charts[chartId]) charts[chartId].destroy();
            let ctx = document.getElementById(chartId).getContext('2d');
            charts[chartId] = new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{ label: 'Days Since Last Task', data: data }]
                },
                options: { responsive: true, animation: { duration: 1000 } }
            });
        }

        function updateCharts() {
            let filteredData = dummyData.filter(item => {
                let companyMatch = $('#companyFilter').val() === 'all' || item.compname === $('#companyFilter').val();
                let partnerMatch = $('#partnerFilter').val() === 'all' || item.partner === $('#partnerFilter').val();
                let userMatch = $('#usernameFilter').val() === 'all' || item.username === $('#usernameFilter').val();
                let statusMatch = $('#statusFilter').val() === 'all' || item.status === $('#statusFilter').val();
                // let categoryMatch = $('#categoryFilter').val() === 'all' || item.status === $('#statusFilter').val();
                let daysMatch = $('#daysFilter').val() === 'all' || item.last_task_days >= parseInt($('#daysFilter').val());
                let remarksMatch = $('#reamrksFilter').val() === 'all' || item.remarks >= $('#reamrksFilter').val();
                return companyMatch && partnerMatch && userMatch && statusMatch && daysMatch && remarksMatch;
            });

            let labels = filteredData.map(item => item.compname);
            let data = filteredData.map(item => item.last_task_days);

            createChart("barChart", "bar", labels, data);
            createChart("pieChart", "pie", labels, data);
            createChart("lineChart", "line", labels, data);
            createChart("doughnutChart", "doughnut", labels, data);
            createChart("radarChart", "radar", labels, data);
            createChart("polarChart", "polarArea", labels, data);
            createChart("bubbleChart", "bubble", labels, data);
            createChart("scatterChart", "scatter", labels, data);
            createChart("stackedBarChart", "bar", labels, data);
            createChart("areaChart", "line", labels, data);
            createChart("funnelChart", "bar", labels, data);
            createChart("heatmapChart", "bar", labels, data);
        }

        updateCharts();
        $(".form-select").on("change", updateCharts);

        // Download PDF
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
                              'polarChart', 'bubbleChart', 'scatterChart', 'stackedBarChart',
                              'areaChart', 'funnelChart', 'heatmapChart'];
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
