<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graph with Filters</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
</head>
<body>

<?php include ("nav.php"); ?>
<?php 
$filters = ['task_user', 'task_time_status_id', 'task_name', 'company_name', 'user_types_name', 'task_date'];
$uniqueValues = [];
foreach ($filters as $filter) {
    $uniqueValues[$filter] = array_unique(array_column($getAllUserData, $filter));
    array_unshift($uniqueValues[$filter], ""); // Adding blank status
}
?>

<div class="container-fluid mt-4">
    <div class="card-header bg-primary text-white text-center p-2">
            <h3>All Times Task Analytics</h3>
        </div>
        <br>

    <div class="row mb-3">
    <?php foreach ($uniqueValues as $filter => $values): ?>
        <?php if ($filter !== 'task_date'): ?>
            <div class="col-md-2">
                <label><?= ucwords(str_replace('_', ' ', $filter)) ?>:</label>
                <select id="<?= $filter ?>Filter" class="form-select">
                    <option value="all">All</option>
                    <?php foreach ($values as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>">
                            <?= $value === "" ? "(Blank)" : htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <div class="col-md-1">
        <label>Start Date:</label>
        <input type="date" id="startDateFilter" class="form-control">
    </div>
    <div class="col-md-1">
        <label>End Date:</label>
        <input type="date" id="endDateFilter" class="form-control">
    </div>
</div>
</div>
<br>
<br>
<div class="container mt-4">

<div class="row mt-5">
                <div class="col-md-12 text-center">
                    <button class="btn btn-success" id="downloadPdf">Download PDF</button>
                    <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
                </div>
               
            </div>

            <br>
            <br>

    <div class="row">
        <?php 
        $chartIds = ['tasksByUser', 'tasksByStatus', 'tasksByAction', 'tasksByCompany', 'stackedBarChart', 'areaChart', 'pieChart', 'doughnutChart', 'scatterPlot', 'bubbleChart', 'radarChart', 'polarAreaChart', 'groupedBarChart'];
        foreach ($chartIds as $id): ?>
            <div class="col-md-12 chart-container"><canvas id="<?= $id ?>"></canvas></div>
        <?php endforeach; ?>
    </div>


    
</div>

<?php include ("footer.php"); ?>

<?php
$loadData = array_map(function ($cData) use ($filters) {
    $dataArr = [];
    foreach ($filters as $filter) {
        $dataArr[$filter] = $cData->$filter ?? ""; // Ensuring blank status
    }
    return $dataArr;
}, $getAllUserData);

$loadDataJSON = json_encode($loadData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>
<script>
$(document).ready(function () {
 
    let rawData = <?=$loadDataJSON;?>;
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

    function filterData() {
    let startDate = $('#startDateFilter').val();
    let endDate = $('#endDateFilter').val();

    return rawData.filter(d => {
        let taskDate = d.task_date ? new Date(d.task_date) : null;
        let isDateInRange = true;

        if (startDate) {
            let start = new Date(startDate);
            isDateInRange = taskDate && taskDate >= start;
        }

        if (endDate) {
            let end = new Date(endDate);
            isDateInRange = isDateInRange && taskDate && taskDate <= end;
        }

        return isDateInRange && 
            <?php foreach ($filters as $filter) {
                if ($filter !== 'task_date') {
                    echo "($('#$filter' + 'Filter').val() === 'all' || d.$filter === $('#$filter' + 'Filter').val() || ($('#$filter' + 'Filter').val() === '(Blank)' && d.$filter === '')) && ";
                }
            } ?> true;
    });
}

// Apply filtering when date fields change
$('#startDateFilter, #endDateFilter').on("change", function () {
    generateCharts(filterData());
});

    function countOccurrences(arr, key) {
        return arr.reduce((acc, obj) => {
            acc[obj[key] || '(Blank)'] = (acc[obj[key] || '(Blank)'] || 0) + 1;
            return acc;
        }, {});
    }

    function createOrUpdateChart(chartId, type, labels, data, colorIndex) {
        let ctx = document.getElementById(chartId);
        if (ctx.chart) ctx.chart.destroy();
        ctx.chart = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: chartId,
                    data: data,
                    backgroundColor: colors.slice(colorIndex, colorIndex + labels.length),
                    borderColor: '#000',
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });
    }

    function generateCharts(filteredData) {
        let counts = {
            user: countOccurrences(filteredData, 'task_user'),
            status: countOccurrences(filteredData, 'task_time_status_id'),
            action: countOccurrences(filteredData, 'task_name'),
            company: countOccurrences(filteredData, 'company_name')
        };

        let chartTypes = ['bar', 'pie', 'bar', 'doughnut', 'bar', 'line', 'pie', 'doughnut', 'scatter', 'bubble', 'radar', 'polarArea', 'bar'];
        let chartIds = ['tasksByUser', 'tasksByStatus', 'tasksByAction', 'tasksByCompany', 'stackedBarChart', 'areaChart', 'pieChart', 'doughnutChart', 'scatterPlot', 'bubbleChart', 'radarChart', 'polarAreaChart', 'groupedBarChart'];
        
        chartIds.forEach((id, i) => {
            let countData = counts[Object.keys(counts)[i % Object.keys(counts).length]];
            createOrUpdateChart(id, chartTypes[i], Object.keys(countData), Object.values(countData), i);
        });
    }

    generateCharts(rawData);
    $("select").on("change", function () {
        generateCharts(filterData());
    });



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
            const chartIds = ['tasksByUser', 'tasksByStatus', 'tasksByAction', 'tasksByCompany', 'stackedBarChart', 'areaChart', 'pieChart', 'doughnutChart', 'scatterPlot', 'bubbleChart', 'radarChart', 'polarAreaChart', 'groupedBarChart'];
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
