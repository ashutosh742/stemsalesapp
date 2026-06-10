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
                    <div class="card-header text-center"><h3><?=$TableData[0]->partner_type;?>(<?=sizeof($TableData)?>)</h3></div>
                    <div class="card-body">
                        <div class="table-responsive" id="tbdata">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S NO</th>
                                        <th>Current Status</th>
                                        <th>Company Name</th>
                                        <!-- <th>City</th>
                                        <th>State</th> -->
                                        <!-- <th>Address</th>
                                        <th>City</th>
                                        <th>State</th> -->
                                        <th>Partner Type</th>
                                        <th>Category</th>
                                        <th>Current Remark</th>
                                        <!-- <th>Total Logs on Same Status</th> -->
                                        <th>Current Status of from whitch date</th>
                                        <th>Same Status from Current Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 

                                        $i=1;
                                        foreach($TableData as $TableRow){

                                            $lastRemark = getLastRemark($TableRow->ic_id);
                                            if (sizeof($lastRemark) != 0) {

                                                $remark=$lastRemark[0]->remarks;
                                                $lastUpdateDate = $lastRemark[0]->updateddate;
                                                $currentDate = new DateTime();
                                                $NoUpdateSince = date_diff_format($lastUpdateDate, $currentDate->format('Y-m-d H:i:s'));

                                            }else{

                                                $remark= "";
                                                $lastUpdateDate = "";
                                                $NoUpdateSince = "";
                                            }
                                            // print_r($lastRemark);die;
                                            
                                    ?>
                                    <tr>
                                        <td><?= $i++?></td>
                                        <td><?= $TableRow->stname?></td>
                                        <td><?= $TableRow->company_name?></td>
                                        <!-- <td><?= $TableRow->city?></td>
                                        <td><?= $TableRow->state?></td> -->
                                        <td><?= $TableRow->partner_type?></td>
                                        <td><?php if($TableRow->focus_funnel=='yes'){echo 'Focus Funnel, ';} if($TableRow->upsell_client=='yes'){echo 'Upsell Client, ';} if($TableRow->keycompany=='yes'){echo 'Key Company';}?></td>
                                        <td><?= $remark ?></td>
                                        <td><?= $lastUpdateDate ?></td>
                                        <td><?= $NoUpdateSince ?></td>
                                    </tr>
                                    <?php } ?>
                                
                                </tbody>
                            </table>
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

