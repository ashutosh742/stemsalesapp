<style>
    #tabs .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        /* color: #0062cc; */
        background-color: #ffc107;
        border-color: transparent transparent #f3f3f3;
        border-bottom: 3px solid !important;
        font-size: 16px;
        font-weight: bolder;
    }

    .nav-tabs .nav-link {
        background-color: white;
        color: black;
        font-weight: 600;
    }

    .nav-tabs .nav-link:hover {
        background-color: #dfd5ef;
        color: #000;
        /* border-color: #007bff; Add a border color on hover */
        border-radius: 0.25rem;
        /* Optional: Rounded corners */
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
                                <h5>DB Request</h5>
                            </center>
                        </div>
                        <div class="card-body FilterSection">
                            <form method="POST" action="<?= base_url(); ?>GraphNew/BDRequest/">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="startDate">Start Date</label>
                                        <input id="startDate" name="startDate" class="form-control" type="date" value="<?= $sdate ?>" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="endDate">End Date</label>
                                        <input id="endDate" class="form-control" name="endDate" type="date" value="<?= $edate ?>" />
                                    </div>
                                    
                                    <!-- User Role -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select Role</label>
                                            <select class="custom-select rounded-0" name="userType[]" id="userType" multiple required>
                                                <option value="select_all">Select All</option>
                                                <?php foreach ($roles as $r) {
                                                ?>
                                                    <option value="<?= $r->id ?>" <?= in_array($r->id, $Selected_userType) ? 'selected' : '' ?>><?= $r->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Users -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select User</label>
                                            <select id="user" class="custom-select rounded-0" name="user[]" data-live-search="true" multiple required>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6">
                                        <button type="submit" name="submit" class="btn btn-primary">Filter</button>
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
                                <canvas id="dbrequest" style="width: 100%; height: 300px;"></canvas>

                                <hr>

                                <div id="StatusWisePieChart1">
                                    <nav>
                                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                            <a class="nav-item nav-link active" id="nav_GridView" data-toggle="tab" href="#GridView" role="tab" aria-controls="GridView" aria-selected="true">Grid View</a>
                                            <a class="nav-item nav-link" id="nav_TableView" data-toggle="tab" href="#TableView" role="tab" aria-controls="TableView" aria-selected="false">XLS View</a>
                                            <!-- <a class="nav-item nav-link" id="nav_TabView" data-toggle="tab" href="#TabView" role="tab" aria-controls="TabView" aria-selected="false">Tab View</a> -->
                                        </div>
                                    </nav>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="GridView" role="tabpanel" aria-labelledby="nav_GridView">
                                            <br>
                                            <div class="row text-center" id="grid-view">
                    
                   
                                                <?php
                                                
                                                    foreach($TableData as $dt){
                                                        if($dt->status=='1'){$color = "bg-success";}else{$color = "bg-danger";}
                                                        $cdate=date('Y-m-d H:i:s');$bdrid=$dt->id;?>
                                                    
                                                                
                                                    <div class="col-sm-12 col-md-4 col-lg-4 mb-4 filter-item">
                                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                                            Request Type<br><b><?=$dt->request_type?></b><hr>
                                                            Request Date<br><b><?=$dt->sdatet?></b><hr>
                                                            BD Name<br><b><?=$dt->bd_name?></b><hr>
                                                            PIA Name<br><b><?=$dt->pia?></b><hr>
                                                            Company Name<br><b><?=$dt->cname?> <?=$dt->cpname?></b><hr>
                                                            Target Date<br><b><?=$tdt=$dt->targetd?></b><hr></a>
                                                            <b><?=$this->Menu_model->timediff($tdt,$cdate)?></b><hr>
                                                            <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                            <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                    </div>
                                                    </div>
                                                <?php } ?>
                    
                    
                        
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="TableView" role="tabpanel" aria-labelledby="nav_TableView">
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
                				    $i=1;
                                     $mdata = $this->Menu_model->get_fannal($uid);
                                     foreach($mdata as $dt){
                                     $cid = $dt->cmpid_id;
                                     $init=$this->Menu_model->get_initcallbyid($cid);
                                     $ciid = $init[0]->id;
                                     $ldscd=$this->Menu_model->get_laststatuschangedate($ciid);
                                     $updatedt = $ldscd[0]->updatedt;
                                     $stlogs = $ldscd[0]->cont;
                                     $cdate=date('Y-m-d H:i:s');
                                     $timediff = $this->Menu_model->timediff($updatedt,$cdate);
                                     $pid = $init[0]->apst;  
                                     $patid = $dt->partnerType_id;
                                     if($patid){$patrid = $this->Menu_model->get_partnerbyid($patid);$patid = $patrid[0]->name;$pclr=$patrid[0]->clr;} else{$patid='';$pclr='';}
                                     if($patid){$sid = $dt->cstatus;$stid=$this->Menu_model->get_statusbyid($sid);$sid=$stid[0]->name;$sclr=$stid[0]->clr;}
                                     else{$sid='';$sclr='';}
                                     $tblc=$this->Menu_model->get_tblbyidwithremark($ciid);
                                     $logs = sizeof($tblc);
                                     if($logs>0){$appoint = $tblc[0]->appointmentdatetime;
                                     $nextaction = $tblc[0]->nextaction;
                                     $remarks = $tblc[0]->remarks;}else{$appoint='';$nextaction='';$remarks='';} 
                                    ?>
                                    
                                    <tr>
                                         <td><?=$i++?></td>
                                         <td style="color:<?=$sclr?>"><?=$sid?></td>
                                         <td style="color:<?=$pclr?>"><?=$dt->compname?></td>
                                         <td><?=$dt->address?></td>
                                         <td><?=$dt->city?></td>
                                         <td><?=$dt->state?></td>
                                         <td style="color:<?=$pclr?>"><?=$patid?></td>
                                         <td><?php if($dt->focus_funnel=='yes'){echo 'Focus Funnel, ';} if($dt->upsell_client=='yes'){echo 'Upsell Client, ';} if($dt->keycompany=='yes'){echo 'Key Company';}?></td>
                                         <td><?=$remarks?></td>
                                         <td><?=$stlogs?></td>
                                         <td><?=$ldscd[0]->updatedt?></td>
                                         <td><?=$timediff?></td>
                                    </tr>
                                    
                                    <?php } ?>
                				    
                				    
                				</tbody>
                    		</table> 
                    	</div>
                                            </div>
                                        </div>
                                        </div>

                                        
                                        <!-- <div class="tab-pane fade" id="TabView" role="tabpanel" aria-labelledby="nav_TabView">
                                            <div class="card card-body">
                                                <div class="row">
                                                    <?php

                                                    $arrayselected_partnerType = json_encode($selected_partnerType);
                                                    $arrayselected_cluster = json_encode($selected_cluster);
                                                    $arrayselected_user = json_encode($selected_users);
                                                    $arrayselected_category = json_encode($selected_category);
                                                    $arrayselected_userType = json_encode($userType);

                                                    ?>
                                                    <?php foreach ($FunnelData as $FunnelDataSingle) {

                                                        $formId = 'hiddenForm_' . htmlspecialchars($FunnelDataSingle->stid);
                                                    ?>
                                                        
                                                        <form id="<?= $formId; ?>" action="<?= base_url(); ?>GraphNew/StatusWiseFunnelData/<?= $FunnelDataSingle->stid ?>" method="POST" style="display: none;" target="_blank">
                                                            <input type="hidden" name="selected_partnerType" value="<?= htmlspecialchars($arrayselected_partnerType); ?>">

                                                            <input type="hidden" name="arrayselected_cluster" value="<?= htmlspecialchars($arrayselected_cluster) ?>">

                                                            <input type="hidden" name="arrayselected_user" value="<?= htmlspecialchars($arrayselected_user) ?>">

                                                            <input type="hidden" name="arrayselected_userType" value="<?= htmlspecialchars($arrayselected_userType) ?>">

                                                            <input type="hidden" name="arrayselected_category" value="<?= htmlspecialchars($arrayselected_category) ?>">

                                                            <input type="hidden" name="stid" value="<?= htmlspecialchars($FunnelDataSingle->stid); ?>">

                                                            <input type="hidden" name="sdate" value="<?= htmlspecialchars($sdate); ?>">
                                                            <input type="hidden" name="edate" value="<?= htmlspecialchars($edate); ?>">
                                                        </form>
                                                        <div class="col-md-3 mb-2" >
                                                            <div class="card card p-3 col-sm m-auto bg-light">
                                                                <strong>
                                                                    <a href="javascript:void(0);" onclick="document.getElementById('<?= $formId; ?>').submit();" style="color:<?= $FunnelDataSingle->stclr ?>">
                                                                        <?= $FunnelDataSingle->stname ?> - <?= $FunnelDataSingle->cont ?>
                                                                    </a>
                                                                    
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div> 
                                        </div> -->
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

<?php $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
<script>

    var combinedData1 = {

        labels: [
            <?php 
                $bdRequests = get_bdRequest(); 
                $i=1; 
                foreach($bdRequests as $bdRequest){ ?> 
                    '<?=$bdRequest->request_type?>', 
                <?php } ?> ],
        datasets: [
            {
                label: 'BD Request',
                backgroundColor: ['red'],
                data: [
                    <?php 
                        $bdrtype = get_bdRequest(); 

                        $i=1; 
                        foreach($bdrtype as $bdrt){

                            $rtype = $bdrt->request_type;
                            $task = get_bdRequestByType($sdate,$edate,$selected_users,$rtype);
                            // var_dump($task);die;
                            foreach($task as $ts){ ?>
                                <?php echo $ts->count?>,
                            <?php }
                    } 
                    ?>
                    ],
                    stack: 'Stack 0'
                    
            },
            
            <?php 
                $ttype = array('PIA Not Assign','PIA Assign But not Start','Started but not Complete','Completed but not BD Close');
            ?>

                <?php $i=1; for($j=0;$j<=3;$j++){ ?>
                {
                    label: '<?=$ttype[$j]?>',
                    backgroundColor: ['<?=$colors[$i]?>'],
                    data: [
                        <?php 
                            $bdrtype = get_bdRequest(); foreach($bdrtype as $bdrt){$rtype = $bdrt->request_type;
                            $task = get_bdRequestByTypeandStage($sdate,$edate,$selected_users,$rtype,$j);
                            foreach($task as $ts){
                        ?>
                        <?=$ts->count?>,
                        <?php }} ?>
                        
                        ],
                        stack: 'Stack 1'
                        
                        <?php $i++;?>
                },
                <?php } ?>

            
        ]
    };

        var combinedCtx = document.getElementById("dbrequest").getContext('2d');
        var combinedChart = new Chart(combinedCtx, {
            type: 'bar',
            data: combinedData1,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'BD Request'
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
</script>

<script>
    $("#example1").DataTable({
        "responsive": false,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>



<!-- <script>
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
</script> -->

    



<script>
    $(document).ready(function() {

        $("#userType").change(function() {

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
                url: '<?= base_url(); ?>GraphNew/getRoleUser_New',
                type: 'POST',
                data: {
                    RoleId: selectedUserType
                },
                success: function(response) {
                    // alert(response);
                    $("#user").html(response);
                    $('#user').prop('required', true);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        $("#user").change(function() {

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


        // $("#cluster").change(function() {

        //     var selectedCluster = $(this).val();

        //     if (selectedCluster.includes('select_all')) {
        //         // Select all options
        //         $('#cluster option').prop('selected', true);
        //         // Remove 'select_all' from the selected values
        //         selectedCluster = $('#cluster option').map(function() {
        //             return this.value !== 'select_all' ? this.value : null;
        //         }).get();

        //         selectedCluster = selectedCluster.filter(function(value) {
        //             return value !== null;
        //         });
        //     }

        //     $.ajax({
        //         url: '<?= base_url(); ?>GraphNew/getUserByCluster',
        //         type: 'POST',
        //         data: {
        //             clusterId: selectedCluster
        //         },
        //         success: function(response) {
        //             $("#user").html(response);
        //             $('#user').prop('required', true);
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error:', error);
        //         }
        //     });

        // });

        // $("#category").change(function() {

        //     var selectedCategory = $(this).val();

        //     if (selectedCategory.includes('select_all')) {
        //         // Select all options
        //         $('#category option').prop('selected', true);
        //         // Remove 'select_all' from the selected values
        //         selectedCategory = $('#category option').map(function() {
        //             return this.value !== 'select_all' ? this.value : null;
        //         }).get();

        //         selectedCategory = selectedCategory.filter(function(value) {
        //             return value !== null;
        //         });
        //     }

        // });

        // $("#partnerType").change(function() {

        //     var selectedPartnerType = $(this).val();

        //     if (selectedPartnerType.includes('select_all')) {
        //         // Select all options
        //         $('#partnerType option').prop('selected', true);
        //         // Remove 'select_all' from the selected values
        //         selectedPartnerType = $('#partnerType option').map(function() {
        //             return this.value !== 'select_all' ? this.value : null;
        //         }).get();

        //         selectedPartnerType = selectedPartnerType.filter(function(value) {
        //             return value !== null;
        //         });
        //     }
        // });

    });
</script>
    <!--  -->