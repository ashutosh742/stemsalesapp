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

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <div class="content-wrapper">
            <br>
            <section class="FilterSection">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <center>
                                <h5>Status Wise Task Analysis</h5>
                            </center>
                        </div>
                        <div class="card-body FilterSection">
                            <form method="POST" action="<?=base_url();?>GraphNew/StatusWiseTaskAnalysis/<?php ?>">
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
                                        <label for="endDate">Select User Filter</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="radioFilter" id="filterByRole" onchange="handleRadioChange()">
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    Filter By Role
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="radioFilter" id="filterByCluster" onchange="handleRadioChange()">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    Filter By Cluster
                                                </label>
                                            </div>
                                    </div>
                                    <!-- User Role -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select Role</label>
                                            <select class="custom-select rounded-0" name="userType[]" id="userType" multiple>
                                                <option value="select_all">Select All</option>
                                                <?php foreach($roles as $r) {
                                                ?>
                                                <option value="<?= $r->id ?>" <?= in_array($r->id, $Selected_userType) ? 'selected' : '' ?>><?= $r->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Cluster -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select Cluster</label>
                                            <select class="custom-select rounded-0" name="cluster[]" id="cluster" multiple disabled>
                                                <option value="select_all">Select All</option>
                                                <?php foreach($clusters as $cluster) {
                                                ?>
                                                    <option value="<?= $cluster->id ?>"><?= $cluster->cluster_name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Users -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select User</label>
                                            <select id="user" class="custom-select rounded-0" name="user[]" data-live-search="true" multiple>
                                                

                                            </select>
                                        </div>
                                    </div>

                                    <!-- Partner Type -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select Partner Type</label>
                                            <select class="custom-select rounded-0" name="partnerType[]" id="partnerType" multiple>
                                                <option value="select_all">Select All</option>
                                                <?php foreach($partner_type as $pt) {
                                                ?>
                                                <option value="<?= $pt->id ?>" <?= in_array($pt->id, $selected_partnerType) ? 'selected' : '' ?> ><?= $pt->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Category Type -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select Category</label>
                                            <select class="custom-select rounded-0" name="category[]" id="category" multiple>
                                                <option value="select_all">Select All</option>
                                                <option value="topspender" <?= in_array('topspender', $selected_category) ? 'selected' : '' ?>>Top Spender</option>
                                                <option value="focus_funnel" <?= in_array('focus_funnel', $selected_category) ? 'selected' : '' ?>>Focus Funnel</option>
                                                <option value="upsell_client" <?= in_array('upsell_client', $selected_category) ? 'selected' : '' ?>>Upsell Client</option>
                                                <option value="keycompany" <?= in_array('keycompany', $selected_category) ? 'selected' : '' ?>>Key Company</option>
                                                <option value="pkclient" <?= in_array('pkclient', $selected_category) ? 'selected' : '' ?>>Key Client</option>
                                                <option value="priorityc" <?= in_array('priorityc', $selected_category) ? 'selected' : '' ?>>Priority Client</option>
                                            </select>
                                        </div>
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
                                <div id="StatusWiseTaskChart" style="width: 100%; height: 800px;"></div>

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
                                            <div class="card">
                                                
                                                <div class="card-body">
                                                    <div class="row">
                                                    <?php foreach($TableData as $TableDataGrid){ 
                                                            // echo "<pre>";
                                                            // print_r($TableDataGrid);die;

                                                            $oldd = '';
                                                            $newd='';

                                                            if($oldd==''){

                                                                $oldd = $TableDataGrid->updateddate;
                                                            } 

                                                            $newd = $TableDataGrid->updateddate;
                                                            // $diff = date_diff_format($oldd, $newd);
                                                            // $tblc=$this->Graph_Model->get_tblbyidwithremark($TableDataGrid->ic_id);

                                                            // if(!empty($tblc[0]->updateddate) ){

                                                            //     $updatedDate = $tblc[0]->updateddate;
                                                            //     $currentDate = new DateTime();
                                                            //     $updatedDateObj = new DateTime($updatedDate);
                                                            //     $interval = $currentDate->diff($updatedDateObj);
                                                            //     $diffInDays = $interval->days;
                                                            //     $diffInHours = $interval->h;
                                                            //     $diffInMinutes = $interval->i;

                                                            // // Create a single string variable
                                                            //     $diffString = sprintf('%d days, %d hours, %d minutes', $diffInDays, $diffInHours, $diffInMinutes);
                                                            // }else{
                                                            //     $updatedDate = 'NA';
                                                            //     $diffString ='NA';
                                                            // }

                                                        ?>
                                                        <div class="col-md-4 mb-4 filter-item" data-category="">
                                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-center">
                                                                <div class="custom-border-card">
                                                                <span class="custom-border-text"><h5>Time Diff Between to Task : <?=date_diff_format($oldd, $newd);?></h5></span>
                                                                <div class="card-body">
                                                                
                                                                Action<br><b style="color:<?=$TableDataGrid->aclr?>"><?=$TableDataGrid->acname?></b><hr>
                                                                Befour Status<br><b style="color:<?=$TableDataGrid->bsclr?>"><?=$TableDataGrid->bstatus?></b><hr>
                                                                After Status<br><b style="color:<?=$TableDataGrid->asclr?>"><?=$TableDataGrid->astatus?></b><hr>
                                                                Company Name<br><a href="<?=base_url();?>Menu/CompanyDetails/<?=$TableDataGrid->cid?>"><b style="color:<?=$TableDataGrid->pclr?>"><?=$TableDataGrid->compname?><a></a><br>(<?=$TableDataGrid->pname?>)</b><hr>
                                                                Task By<br><b><?=$TableDataGrid->uname?></b><hr>
                                                                Task Plan<br><b><?=$this->Menu_model->get_dformat($TableDataGrid->appointmentdatetime)?></b><hr>
                                                                Task Inistaed<br><b><?=$TableDataGrid->initiateddt ?></b><br>
                                                                Time Diff : <?=date_diff_format($TableDataGrid->appointmentdatetime,$TableDataGrid->initiateddt);?><hr>
                                                                Task Updated<br><b><?=$this->Menu_model->get_dformat($TableDataGrid->updateddate)?></b><br>
                                                                Time Diff : <?=date_diff_format($TableDataGrid->initiateddt,$TableDataGrid->updateddate);?><hr>
                                                                Remark/MOM <br><b><?=$TableDataGrid->remarks?><?=$TableDataGrid->mom?></b><hr>
                                                                <div class="rounded-circle bg-danger" style="position: absolute;
                                                                    bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                                <div class="rounded-circle bg-danger" style="position: absolute;
                                                                    bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                                    
                                                            </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="tab-pane fade" id="TableView" role="tabpanel" aria-labelledby="nav_TableView">
                                            <div class="card card-body">
                                                <div class="table-responsive" id="tbdata">
                                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>S NO</th>
                                                                <th>Action</th>
                                                                <th>Before Status</th>
                                                                <th>After Status</th>
                                                                <th>Company Name</th>
                                                                <th>Partner Type</th>
                                                                <th>Task By</th>
                                                                <th>Task Plan</th>
                                                                <th>Task Initiated</th>
                                                                <th>Time Diff</th>
                                                                <th>Task Updated</th>
                                                                <th>Time Diff</th>
                                                                <th>Remark</th>
                                                                <th>MOM</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 

                                                                $i=1;
                                                                foreach($TableData as $TableDataGrid){

                                                            ?>
                                                            <tr>
                                                                <td><?= $i++?></td>
                                                                <td><b style="color:<?=$TableDataGrid->aclr?>"><?=$TableDataGrid->acname?></b></td>
                                                                <td><b style="color:<?=$TableDataGrid->bsclr?>"><?=$TableDataGrid->bstatus?></b></td>
                                                                <td><b style="color:<?=$TableDataGrid->asclr?>"><?=$TableDataGrid->astatus?></b></td>
                                                                <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$TableDataGrid->cid?>"><b style="color:"><?=$TableDataGrid->compname?></b></a></td>
                                                                <td><b style="color:<?=$TableDataGrid->pclr?>"><?=$TableDataGrid->pname?></b></td>
                                                                <td><?=$TableDataGrid->uname?></td>
                                                                <td><?=$this->Menu_model->get_dformat($TableDataGrid->appointmentdatetime)?></td>
                                                                <td><?=$TableDataGrid->initiateddt?></td>
                                                                <td><?=date_diff_format($TableDataGrid->appointmentdatetime,$TableDataGrid->initiateddt)?></td>
                                                                <td><?=$this->Menu_model->get_dformat($TableDataGrid->updateddate)?></td>
                                                                <td><?=date_diff_format($TableDataGrid->initiateddt,$TableDataGrid->updateddate)?></td>
                                                                <td><?=$TableDataGrid->remarks?></td>
                                                                <td><?=$TableDataGrid->mom?></td>
                                                            </tr>
                                                            <?php } ?>
                                                        
                                                        </tbody>
                                                    </table>
                                                </div>
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
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
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
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    
    function drawChart() {

        const chartData = <?php echo json_encode($FunnelData); ?>;
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
console.log(data);

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

            
        const chart = new google.visualization.ColumnChart(document.getElementById('StatusWiseTaskChart'));
        
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

        chart.draw(data, options_fullStacked);
    }
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
<!--  -->

