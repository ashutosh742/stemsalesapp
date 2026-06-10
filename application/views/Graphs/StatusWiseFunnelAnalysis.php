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
        background-color: lightblue;
        color: black;
        font-weight: 600;
    }
    .nav-tabs .nav-link:hover {
    background-color: cadetblue;
    color: #000;
    /* border-color: #007bff; Add a border color on hover */
    border-radius: 0.25rem; /* Optional: Rounded corners */
}
</style>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <div class="content-wrapper">
            <br>
                <div class="card text-left">
                    <?php //var_dump($StatusWiseFunnelData);die;?>
                  <!-- <img class="card-img-top" src="holder.js/100px180/" alt=""> -->
                   <?php 

                    // var_dump($StatusWiseFunnelData);die;
                   ?>
                  <div class="card-header text-center"><h3><?=$StatusWiseFunnelData[0]->stname;?>(<?=sizeof($StatusWiseFunnelData)?>)</h3></div>
                  <div class="card-body">
                    <div id="StatusWisePieChart1">
                        <nav>
                            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                <!-- <a class="nav-item nav-link active ml-2" id="nav_GridView" data-toggle="tab" href="#GridView" role="tab" aria-controls="GridView" aria-selected="true">Grid View</a> -->
                                <a class="nav-item nav-link active ml-2" id="nav_XLSView" data-toggle="tab" href="#XLSView" role="tab" aria-controls="TableView" aria-selected="false">XLS View</a>
                                <!-- <a class="nav-item nav-link ml-2" id="nav_TabView" data-toggle="tab" href="#TabView" role="tab" aria-controls="TabView" aria-selected="false">Tab View</a> -->
                            </div>
                        </nav>
                        <br>
                        <div class="tab-content">
                            <!-- <div class="tab-pane fade show active" id="GridView" role="tabpanel" aria-labelledby="nav_GridView">
                                <div class="card card-body">
                                    <div class="row">
                                        <?php foreach($StatusWiseFunnelData as $TableDataGrid){ 
                                            ?>
                                            <div class="col-md-4 mb-4 filter-item" >
                                                <div class="card-body p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">

                                                    Current Status<br><b style="color:black"><?=$TableDataGrid->stname?></b><hr>
                                                    Company Name<br><b style=""><?=$TableDataGrid->company_name?></b><hr>
                                                    Address<br><b><?=$TableDataGrid->company_address?></b><hr>
                                                    City<br><b><?=$TableDataGrid->city?></b><hr>
                                                    State<br><b><?=$TableDataGrid->state?></b><hr>
                                                    Partner Type<br><b style=""><?=$TableDataGrid->partner_type?></b><hr>
                                                    <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                    <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div> -->
                            <div class="tab-pane fade show active" id="XLSView" role="tabpanel" aria-labelledby="nav_XLSView">
                                <div class="table-responsive" id="tbdata">
                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S NO</th>
                                                <th>BD Name</th>
                                                <th>PST Name</th>
                                                <th>Current Status</th>
                                                <th>Company Name</th>
                                                <th>Address</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Partner Type</th>
                                                <!-- <th>Category</th>
                                                <th>Current Remark</th>
                                                <th>Total Logs on Same Status</th>
                                                <th>Current Status of from whitch date</th>
                                                <th>Same Status from Current Time</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 

                                                $i=1;
                                                foreach($StatusWiseFunnelData as $TableRow){
                                            ?>
                                            <tr>
                                                <td><?= $i++?></td>
                                                <td><?= $TableRow->bd_name?></td>
                                                <td><?= $TableRow->pst_name?></td>
                                                <td><?= $TableRow->stname?></td>
                                                <td><?= $TableRow->company_name?></td>
                                                <td><?= $TableRow->company_address?></td>
                                                <td><?= $TableRow->city?></td>
                                                <td><?= $TableRow->state?></td>
                                                <td><?= $TableRow->partner_type?></td>
                                            </tr>
                                            <?php } ?>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="TabView" role="tabpanel" aria-labelledby="nav_TabView">
                                
                            </div>
                        </div>
                    </div>
                    
                  </div>
                </div>
            <br>
            
        </div>
    </div>

<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>

<!--  -->

