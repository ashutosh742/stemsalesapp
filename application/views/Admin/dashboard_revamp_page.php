<style>
    #tabs .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        /* color: #0062cc; */
        background-color: #ffc107;
        border-color: transparent transparent #f3f3f3;
        border-bottom: 3px solid !important;
        font-size: 16px;
        font-weight: bolder;
    }
    .nav-tabs .nav-link{
        background-color: white;
        color: black;
        font-weight: 600;
    }
    .nav-tabs .nav-link:hover {
    background-color: #dfd5ef;
    color: #000;
    /* border-color: #007bff; Add a border color on hover */
    border-radius: 0.25rem; /* Optional: Rounded corners */
}
</style>
<style type="text/css">      
      html, body, #container { 
        width: 100%; height: 100%; margin: 0; padding: 0; 
      } 
    </style>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">
<div class="row">
        <div class="col-lg-6 col-xl-3 mb-4">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="text-lg fw-bold">Team Details</div>
                                                <div class="text-white-75 small">Total BD - <b><?= sizeof($mdata)?></b></div>
                                                <div class="text-white-75 small">Total BD Present - <b><?=$day[0]->b?></b></div>
                                              <!--  <div class="text-white-75 small">Total Work From Office -<b><?= sizeof($mdata)?></b></p></div>
                                                <div class="text-white-75 small">Total Work From Field - <b><?= sizeof($mdata)?></b></p></div>
                                                <div class="text-white-75 small">Total Field+Office- <b><?= sizeof($mdata)?></b></p></div> -->
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar feather-xl text-white-50"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between small">
                                        <a class="text-white stretched-link" href="TeamDetail">View Report</a>
                                        <div class="text-white"><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M246.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L178.7 256 41.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fas fa-angle-right"></i> Font Awesome fontawesome.com --></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-3 mb-4">
                                <div class="card bg-warning text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="text-lg fw-bold">Today's Team Task Detail</div>
                                                <div class="text-white-75 small">Total Task Assigned/Planned- <b><?=$tttd->a?></b> </div>
                                                <div class="text-white-75 small">Total Task Pending - <b><?=$tttd->b?></b></div>
                                                <div class="text-white-75 small">Total Task Completed <b><?=$tttd->c?></b></div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign feather-xl text-white-50"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between small">
                                        <a class="text-white stretched-link" href="">View Report</a>
                                        <div class="text-white"><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M246.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L178.7 256 41.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fas fa-angle-right"></i> Font Awesome fontawesome.com --></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-3 mb-4">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="text-lg fw-bold">Funnel By Categories</div>
                                                <div class="text-white-75 small">Upsell Client - <b><?=$mc->n?></b></div>
                                                <div class="text-white-75 small">EX-BD Tf - <b><?=$mc->o?></b></div>
                                                <div class="text-white-75 small">MP's/MLA - <b><?=$mc->p?></b></div>
                                                <div class="text-white-75 small">Potential Partner BD - <b><?=$mc->q?></b></div>
                                                <div class="text-white-75 small">Non Potential Partner BD - <b><?=$mc->u?></b></div>
                                                <div class="text-white-75 small">Pending Potential Marking - <b><?=$mc->v?></b></div>
                                                <div class="text-white-75 small">Potential Partner PST - <b><?=$mc->r?></b></div>
                                                <div class="text-white-75 small">Potential Partner This QTR - <b><?=$mc->s?></b></div>
                                                <div class="text-white-75 small">Potential Partner This FY - <b><?=$mc->t?></b></div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square feather-xl text-white-50"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between small">
                                        <a class="text-white stretched-link" href="">View Tasks</a>
                                        <div class="text-white"><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M246.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L178.7 256 41.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fas fa-angle-right"></i> Font Awesome fontawesome.com --></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-3 mb-4">
                                <div class="card bg-danger text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="text-lg fw-bold">CM/PST Assigned Data</div>
                                                <div class="text-white-75 small">Total CM - <b><?=$totalCM ?></b></div>
                                                <div class="text-white-75 small">Total PST - <b><?=$totalSC?></b></div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle feather-xl text-white-50"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            <br>
            <div class="row">
            <div class="col-xl-6 mb-4">
            <section class="GraphSection">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-body">
                                <div id="StatusWisePieChart" style="width: 100%; height: 400px;"></div>
                                <!-- <div id="sankey_basic" style="width: 100%; height: 400px;"></div> -->
                                <div id="sankey_basic"></div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div> 
                </section>
            </div>
        </div>
        <div class="row">
                <div class="col-xl-6 mb-4">
                    <div style="width: 80%; margin: auto;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
           
            <br>
            
           
        </div>
        </div>
        
    </div>

<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all category cards
    const categoryCards = document.querySelectorAll('.card-header .card');
    // Get all filter items
    const filterItems = document.querySelectorAll('.filter-item');

    // Function to filter grid items
    function filterItemsByCategory(category) {
        filterItems.forEach(item => {
            if (item.dataset.category === category || category === 'All') {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Attach click event listeners to category cards
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const category = this.dataset.category;
            filterItemsByCategory(category);
        });
    });

    // Optional: Show all items by default
    filterItemsByCategory('All');
});
</script>


<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    
    
    function drawChart() {

        const chartData = <?php echo json_encode($FunnelData); ?>;
        var sdate = <?php echo json_encode($sdate); ?>;
        var edate = <?php echo json_encode($edate); ?>;
        var selected_category = <?php echo json_encode($selected_category); ?>;
        var selected_partnerType = <?php echo json_encode($selected_partnerType); ?>;
        var selected_userType = <?php echo json_encode($userType); ?>;
        var selected_cluster = <?php echo json_encode($selected_cluster); ?>;
        var selected_users = <?php echo json_encode($selected_users); ?>;
        var uid = <?php echo json_encode($uid); ?>;
        
        // console.log(selected_cluster);

        var selectedPartnerTypeString = JSON.stringify(selected_partnerType);
        var selected_categoryString = JSON.stringify(selected_category);
        var selected_clusterString = JSON.stringify(selected_cluster);
        var selected_usersString = JSON.stringify(selected_users);
        var selected_userTypeString = JSON.stringify(selected_userType);
        // var uid = JSON.stringify(uid);

        // console.log(selectedPartnerTypeString);
        const code = '';
        const filteredData = chartData.filter(item => item.stname && item.cont && item.stid);
        // console.log(chartData);

                // Map labels and values
        const labels = filteredData.map(item => item.stname);
        const dataValues = filteredData.map(item => Number(item.cont));
        const stid = filteredData.map(item => Number(item.stid));

        const dataArray = [
            ['Status', 'Count','StatusID']  // Adjust column names as needed
        ];

        for (let i = 0; i < labels.length; i++) {
            dataArray.push([labels[i], dataValues[i], stid[i]]);
        }

        const data = google.visualization.arrayToDataTable(dataArray);
        const options = {
            title:'Status Wise Funnel Graph',
            is3D:true
        };

        const chart = new google.visualization.PieChart(document.getElementById('StatusWisePieChart'));

        google.visualization.events.addListener(chart, 'select', function() {
            var selection = chart.getSelection()[0];
            if (selection) {


                var stid = data.getValue(selection.row, 2);
                // var sdate = encodeURIComponent(sdate);
                // var edate = encodeURIComponent(edate);
                // var selected_partnerType = encodeURIComponent(selected_partnerType);

                // Create a hidden form
                var form = document.createElement('form');
                form.method = 'POST'; // Use POST method
                form.action = '<?=base_url();?>GraphNew/StatusWiseFunnelGraphData';
                form.target = '_blank';

                // Create hidden input fields
                var inputStid = document.createElement('input');
                inputStid.type = 'hidden';
                inputStid.name = 'stid';
                inputStid.value = stid;
                form.appendChild(inputStid);

                var inputSdate = document.createElement('input');
                inputSdate.type = 'hidden';
                inputSdate.name = 'sdate';
                inputSdate.value = sdate;
                form.appendChild(inputSdate);

                var inputEdate = document.createElement('input');
                inputEdate.type = 'hidden';
                inputEdate.name = 'edate';
                inputEdate.value = edate;
                form.appendChild(inputEdate);

                var inputuid = document.createElement('input');
                inputuid.type = 'hidden';
                inputuid.name = 'uid';
                inputuid.value = uid;
                form.appendChild(inputuid);
                

                var inputSelectedPartnerType = document.createElement('input');
                inputSelectedPartnerType.type = 'hidden';
                inputSelectedPartnerType.name = 'selected_partnerType';
                inputSelectedPartnerType.value = selectedPartnerTypeString;
                form.appendChild(inputSelectedPartnerType);

                var inputselected_userType = document.createElement('input');
                inputselected_userType.type = 'hidden';
                inputselected_userType.name = 'selected_userType';
                inputselected_userType.value = selected_userTypeString;
                form.appendChild(inputselected_userType);

                var inputselected_cluster = document.createElement('input');
                inputselected_cluster.type = 'hidden';
                inputselected_cluster.name = 'selected_cluster';
                inputselected_cluster.value = selected_clusterString;
                form.appendChild(inputselected_cluster);

                var inputselected_users = document.createElement('input');
                inputselected_users.type = 'hidden';
                inputselected_users.name = 'selected_users';
                inputselected_users.value = selected_usersString;
                form.appendChild(inputselected_users);

                var inputselected_category = document.createElement('input');
                inputselected_category.type = 'hidden';
                inputselected_category.name = 'selected_category';
                inputselected_category.value = selected_categoryString;
                form.appendChild(inputselected_category);
                // Append the form to the body and submit
                
                document.body.appendChild(form);
               // form.submit();

            }
        });

        chart.draw(data, options);
    }
</script>
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart1);
    
    function drawChart1(){
        const chartData = <?php echo json_encode($TableTaskData); ?>;
      //  console.log(chartData);
        var sdate = <?php echo json_encode($sdate); ?>;
        var edate = <?php echo json_encode($edate); ?>;
        var selected_category = <?php echo json_encode($selected_category); ?>;
        var selected_partnerType = <?php echo json_encode($selected_partnerType); ?>;
        var selected_userType = <?php echo json_encode($Selected_userType); ?>;
        var selected_cluster = <?php echo json_encode($selected_cluster); ?>;
        var selected_users = <?php echo json_encode($selected_users); ?>;
        var uid = <?php echo json_encode($uid); ?>;
        

        var selectedPartnerTypeString = JSON.stringify(selected_partnerType);
        var selected_categoryString = JSON.stringify(selected_category);
        var selected_clusterString = JSON.stringify(selected_cluster);
        var selected_usersString = JSON.stringify(selected_users);
        var selected_userTypeString = JSON.stringify(selected_userType);

        var data = new google.visualization.DataTable();

            data.addColumn('string', 'All Action');
            data.addColumn('number', 'Call');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Email');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Scheduled Meeting');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Barg in Meeting');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Whatsapp Activity');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Write MOM');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Write Proposal');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Research');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Mail Check');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Social Networking');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Social Activity');
            data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn({type: 'string', role: 'annotationText'});

            <?php
            foreach($getStatus as $singleStatus){ 
                $stid = $singleStatus->id;
                $stname = $singleStatus->name;
                $StatusWiseTask = getStatusWiseTask($uid,$sdate,$edate,$selected_category,$selected_partnerType,$Selected_userType,$selected_cluster,$selected_users,$stid,$userTypeid);
                // var_dump($StatusWiseTask);
                foreach($StatusWiseTask as $sw){
                    // $url="https://stemapp.in/Menu";
                    $url = "#";
                    ?>
                    data.addRow(['<?=$stname?> ( <?=$sw->cont?>)',

                    <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
                    <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>',
                    <?=$sw->c?>, '<?php if($sw->c == 0){echo "";}else{ echo $sw->c;}?>',
                    <?=$sw->d?>, '<?php if($sw->d == 0){echo "";}else{ echo $sw->d;}?>',
                    <?=$sw->e?>, '<?php if($sw->e == 0){echo "";}else{ echo $sw->e;}?>',
                    <?=$sw->f?>, '<?php if($sw->f == 0){echo "";}else{ echo $sw->f;}?>',
                    <?=$sw->g?>, '<?php if($sw->g == 0){echo "";}else{ echo $sw->g;}?>',
                    <?=$sw->h?>, '<?php if($sw->h == 0){echo "";}else{ echo $sw->h;}?>',
                    <?=$sw->i?>, '<?php if($sw->i == 0){echo "";}else{ echo $sw->i;}?>',
                    <?=$sw->j?>, '<?php if($sw->j == 0){echo "";}else{ echo $sw->j;}?>',
                    <?=$sw->k?>, '<?php if($sw->k == 0){echo "";}else{ echo $sw->k;}?>','<?=$sw->nstatus_id?>']);
                    <?php
                }
            }
            
            ?>

            var options_fullStacked = {
                isStacked: 'percent',
                // height: 600,
                legend: {position: 'top', maxLines: 3},
                vAxis: {
                minValue: 0,
                ticks: [0, 0.3, 0.6, 0.9, 1]
                },
                annotations: {
                alwaysOutside: false,
                textStyle: {
                    fontSize: 10,
                },
                },
            };

            
        const chart1 = new google.visualization.ColumnChart(document.getElementById('StatusWiseTaskChart'));
        
        // var chart = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
        // google.visualization.events.addListener(chart, 'select', function() {
        //     var selection = chart.getSelection()[0];
        //     if (selection) {

        //         var stid = data.getValue(selection.row, 23);

        //         // Create a hidden form
        //         var form = document.createElement('form');
        //         form.method = 'POST'; // Use POST method
        //         form.action = '<?=base_url();?>GraphNew/StatusWiseTaskFunnelGraphData';
        //         form.target = '_blank';

        //         // Create hidden input fields
        //         var inputStid = document.createElement('input');
        //         inputStid.type = 'hidden';
        //         inputStid.name = 'stid';
        //         inputStid.value = stid;
        //         form.appendChild(inputStid);

        //         var inputSdate = document.createElement('input');
        //         inputSdate.type = 'hidden';
        //         inputSdate.name = 'sdate';
        //         inputSdate.value = sdate;
        //         form.appendChild(inputSdate);

        //         var inputEdate = document.createElement('input');
        //         inputEdate.type = 'hidden';
        //         inputEdate.name = 'edate';
        //         inputEdate.value = edate;
        //         form.appendChild(inputEdate);

        //         var inputuid = document.createElement('input');
        //         inputuid.type = 'hidden';
        //         inputuid.name = 'uid';
        //         inputuid.value = uid;
        //         form.appendChild(inputuid);
                

        //         var inputSelectedPartnerType = document.createElement('input');
        //         inputSelectedPartnerType.type = 'hidden';
        //         inputSelectedPartnerType.name = 'selected_partnerType';
        //         inputSelectedPartnerType.value = selectedPartnerTypeString;
        //         form.appendChild(inputSelectedPartnerType);

        //         var inputselected_userType = document.createElement('input');
        //         inputselected_userType.type = 'hidden';
        //         inputselected_userType.name = 'selected_userType';
        //         inputselected_userType.value = selected_userTypeString;
        //         form.appendChild(inputselected_userType);

        //         var inputselected_cluster = document.createElement('input');
        //         inputselected_cluster.type = 'hidden';
        //         inputselected_cluster.name = 'selected_cluster';
        //         inputselected_cluster.value = selected_clusterString;
        //         form.appendChild(inputselected_cluster);

        //         var inputselected_users = document.createElement('input');
        //         inputselected_users.type = 'hidden';
        //         inputselected_users.name = 'selected_users';
        //         inputselected_users.value = selected_usersString;
        //         form.appendChild(inputselected_users);

        //         var inputselected_category = document.createElement('input');
        //         inputselected_category.type = 'hidden';
        //         inputselected_category.name = 'selected_category';
        //         inputselected_category.value = selected_categoryString;
        //         form.appendChild(inputselected_category);
        //         // Append the form to the body and submit
                
        //         document.body.appendChild(form);
        //         form.submit();

        //     }
        // });

        chart1.draw(data, options_fullStacked);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
     // Data passed from PHP
     const statusCounts = <?= json_encode($TableTaskData); ?>;
// Prepare labels and data for the chart
const labels = Object.keys(statusCounts);
const data = Object.values(statusCounts);
//console.log(data);
// Create bar chart using Chart.js
const ctx = document.getElementById('barChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Status Count',
            data: data,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ],
            borderColor: '#333',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<!-- <script src="https://d3js.org/d3.v5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3-sankey/0.12.3/d3-sankey.v0.12.3.min.js"></script> -->


<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-core.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-sankey.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/sankey.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>



<script>

    const dataFromServer = [
                <?php foreach ($jsonSankeyData as $item): ?>
                    ['<?php echo $item[0]; ?>', '<?php echo $item[1]; ?>', <?php echo $item[2]; ?>],
                <?php endforeach; ?>
            ];

    // console.log(dataFromServer);
    Highcharts.chart('sankey_basic', {

        title: {
            text: 'Status Conversion Variance'
        },

        tooltip: {
            headerFormat: null,
            pointFormat:
                '{point.fromNode.name} \u2192 {point.toNode.name}: {point.weight:.2f} ' +
                'tasks',
                    nodeFormat: '{point.name}: {point.sum:.2f} tasks'
        },
        series: [{
            keys: ['from', 'to', 'weight'],

            nodes: [

            ],

            data: [
                 
                <?php foreach ($jsonSankeyData as $item): ?>
                    ['<?php echo $item[0]; ?>', '<?php echo $item[1]; ?>', <?php echo $item[2]; ?>],
                <?php endforeach; ?>

                // ['Open', 'Reachout', 10],
                // ['Open', 'OPEN RPEM', 33],
                // ['Reachout', 'Will do Later', 5],
                // ['Reachout', 'Not Interested', 3],
                // ['Tentative', 'Will do Later', 3],
                // ['Tentative', 'Positive', 1],
                // ['Tentative', 'TTD-Reachout', 6],
                // ['Tentative', 'Positive-NAP', 4],
                // ['Will do Later', 'OPEN RPEM', 1],
                // ['OPEN RPEM', 'Reachout', 31],
                // ['TTD-Reachout', 'Reachout', 1],
                // ['Positive-NAP', 'Positive', 5]
            ],
            type: 'sankey',
            name: 'Task Status Variance'
        }],
        animation: {
                    duration: 1000,
                    easing: 'inAndOut'
                }

    });
</script>



<script>
    // Function to handle radio button change
    function handleRadioChange() {
        const selectedRadio = document.querySelector('input[name="radioFilter"]:checked');
        
        if (selectedRadio) {
            const selectedOption = selectedRadio.id;
            
            const filterByClusterID = document.getElementById('cluster');
            const filterByRoleID = document.getElementById('userType');
            
            if (selectedOption === 'filterByCluster') {
                
                filterByClusterID.disabled = false;
                filterByRoleID.disabled = true;

            } else if (selectedOption === 'filterByRole') {

                filterByClusterID.disabled = true;
                filterByRoleID.disabled = false;
            }
        }
    }

    // Attach change event listener to all radio buttons
    document.querySelectorAll('input[name="option"]').forEach(radio => {
        radio.addEventListener('change', handleRadioChange);
    });

</script>


<script>
    $(document).ready(function() {
        
        $("#userType").change(function(){

            var selectedUserType = $(this).val(); 
            if (selectedUserType.includes('select_all')) {
            // Select all options
                $('#userType option').prop('selected', true);
                // Remove 'select_all' from the selected values
                selectedUserType = $('#userType option').map(function() {
                    return this.value !== 'select_all' ? this.value : null;
                }).get();

                selectedUserType = selectedUserType.filter(function(value) {
                    return value !== null;
                });
            }

            $.ajax({
                url: '<?=base_url();?>GraphNew/getRoleUser_New',
                type: 'POST', 
                data: {RoleId: selectedUserType},
                success: function(response) {
                    // alert(response);
                    $("#user").html(response);
                    $('#user').prop('required',true);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        $("#user").change(function(){

            var selectedUser = $(this).val(); 
            if (selectedUser.includes('select_all')) {
            // Select all options
                $('#user option').prop('selected', true);
                // Remove 'select_all' from the selected values
                selectedUser = $('#user option').map(function() {
                    return this.value !== 'select_all' ? this.value : null;
                }).get();

                selectedUser = selectedUser.filter(function(value) {
                    return value !== null;
                });
            }
            // var selectedValues = $("#userType").val(); // Get selected values
        });


        $("#cluster").change(function(){

            var selectedCluster = $(this).val(); 

            if (selectedCluster.includes('select_all')) {
            // Select all options
                $('#cluster option').prop('selected', true);
                // Remove 'select_all' from the selected values
                selectedCluster = $('#cluster option').map(function() {
                    return this.value !== 'select_all' ? this.value : null;
                }).get();

                selectedCluster = selectedCluster.filter(function(value) {
                    return value !== null;
                });
            }

            $.ajax({
                url: '<?=base_url();?>GraphNew/getUserByCluster',
                type: 'POST', 
                data: {clusterId: selectedCluster},
                success: function(response) {
                    $("#user").html(response);
                    $('#user').prop('required',true);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        });

        $("#category").change(function(){

            var selectedCategory = $(this).val(); 

            if (selectedCategory.includes('select_all')) {
            // Select all options
                $('#category option').prop('selected', true);
                // Remove 'select_all' from the selected values
                selectedCategory = $('#category option').map(function() {
                    return this.value !== 'select_all' ? this.value : null;
                }).get();

                selectedCategory = selectedCategory.filter(function(value) {
                    return value !== null;
                });
            }

        });

        $("#partnerType").change(function(){

            var selectedPartnerType = $(this).val(); 

            if (selectedPartnerType.includes('select_all')) {
            // Select all options
                $('#partnerType option').prop('selected', true);
                // Remove 'select_all' from the selected values
                selectedPartnerType = $('#partnerType option').map(function() {
                    return this.value !== 'select_all' ? this.value : null;
                }).get();

                selectedPartnerType = selectedPartnerType.filter(function(value) {
                    return value !== null;
                });
            }
        });
            
    });
</script>



