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
</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <div class="content-wrapper">
            <br>
            <section class="FilterSection">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <center>
                                <h5>Status Wise Funnel Graph</h5>
                            </center>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?=base_url();?>GraphNew/StatusWiseFunnelGraph/<?=$code?>">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="startDate">Start Date</label>
                                        <input id="startDate" name="startDate" class="form-control" type="date" value="<?=$sdate ?>"/>
                                        
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="endDate">End Date</label>
                                        <input id="endDate" class="form-control" name="endDate" type="date" value="<?=$edate ?>"/>
                                       
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                       <button type="submit" name="submit" class="btn btn-primary"> Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </section>
            <br>
            <section class="GraphSection">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-body">
                                <div id="StatusWisePieChart" style="width: 100%; height: 400px;"></div>

                                <hr>

                                <div id="StatusWisePieChart1">
                                    <nav>
                                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                            <!-- <a class="nav-item nav-link active" id="nav_GridView" data-toggle="tab" href="#GridView" role="tab" aria-controls="GridView" aria-selected="true">Grid View</a> -->
                                            <a class="nav-item nav-link active" id="nav_TableView" data-toggle="tab" href="#TableView" role="tab" aria-controls="TableView" aria-selected="false">Table View</a>
                                            <a class="nav-item nav-link" id="nav_TabView" data-toggle="tab" href="#TabView" role="tab" aria-controls="TabView" aria-selected="false">Tab View</a>
                                        </div>
                                    </nav>
                                    <div class="tab-content">
                                        <!-- <div class="tab-pane fade show active" id="GridView" role="tabpanel" aria-labelledby="nav_GridView">
                                            <div class="card card-body">

                                            </div>
                                            
                                        </div> -->
                                        <div class="tab-pane fade show active" id="TableView" role="tabpanel" aria-labelledby="nav_TableView">
                                            
                                            <div class="card card-body">
                                                <div class="table-responsive" id="tbdata">
                                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>S NO</th>
                                                                <th>Current Status</th>
                                                                <th>Company Name</th>
                                                                <th>Address</th>
                                                                <th>City</th>
                                                                <th>State</th>
                                                                <th>Partner Type</th>
                                                                <th>Category</th>
                                                                <th>Current Remark</th>
                                                                <th>Total Logs on Same Status</th>
                                                                <th>Current Status of from whitch date</th>
                                                                <th>Same Status from Current Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php 

                                                        // var_dump($TableData);die;
                                                        $i=1;
                                                        foreach($TableData as $TableRow){
                                                            // var_dump($TableRow);die; ?>
                                                            <tr>
                                                                <td><?= $i++?></td>
                                                                <td><?= $TableRow->stname?></td>
                                                                <td><?= $TableRow->company_name?></td>
                                                                <td><?= $TableRow->company_address?></td>
                                                                <td><?= $TableRow->city?></td>
                                                                <td><?= $TableRow->state?></td>
                                                                <td><?= $TableRow->partner_type?></td>
                                                                <td><?php  echo $TableRow->focus_funnel;echo $TableRow->keycompany;echo $TableRow->upsell_client;?></td>
                                                                <td><?= $TableRow->remark?></td>
                                                                <td><?= $TableRow->total_count?></td>
                                                                <td><?= $TableRow->latest_updated_date?></td>
                                                                <td><?= $TableRow->time_since_last_update?></td>
                                                            </tr>
                                                    <?php     }
                                                        ?>
                                                            <!-- <tr></tr> -->
                                                        </tbody>
                                                    </table>
    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="TabView" role="tabpanel" aria-labelledby="nav_TabView">
                                            <div class="card card-body">
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div> 
            </section>
        </div>
    </div>




<script src="https://www.gstatic.com/charts/loader.js"></script>

<script>

    $(document).ready(function() {
        $('#example1').DataTable();
    });
</script>
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    
    function drawChart() {

        const chartData = <?php echo json_encode($chartData); ?>;
        const code = <?php echo ($code); ?>;
        // alert(code);
        const filteredData = chartData.filter(item => item.stname && item.cont);

                // Map labels and values
        const labels = filteredData.map(item => item.stname);
        const dataValues = filteredData.map(item => Number(item.cont));

        const dataArray = [
            ['Status', 'Count']  // Adjust column names as needed
        ];

        for (let i = 0; i < labels.length; i++) {
            dataArray.push([labels[i], dataValues[i]]);
        }

        const data = google.visualization.arrayToDataTable(dataArray);
        const options = {
            title:'Status Wise Funnel Graph',
            is3D:true
        };

        const chart = new google.visualization.PieChart(document.getElementById('StatusWisePieChart'));

        google.visualization.events.addListener(chart, 'select', function() {
            const selection = chart.getSelection()[0];
            if (selection) {
                const row = selection.row;
                const stid = filteredData[row].stid;
                const uuid = '<?= $uid ?>'; // Ensure $uid is available
                const code = 1;
                // Redirect to another URL with stid and uuid as parameters
                window.location.href = '<?= base_url(); ?>GraphNew/StatusWiseFunnelGraph/' + code + '/' + stid;
            }
        });

        chart.draw(data, options);
    }
</script>

<!--  -->

