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
                                <h5>Category Wise Funnel Analysis</h5>
                            </center>
                        </div>
                        <div class="card-body FilterSection">
                            <form method="POST" action="<?=base_url();?>GraphNew/CategoryWiseFunnelAnalysis/">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="startDate">Start Date</label>
                                        <input id="startDate" name="startDate" class="form-control" type="date" value="<?=$sdate ?>"/>
                                        
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="endDate">End Date</label>
                                        <input id="endDate" class="form-control" name="endDate" type="date" value="<?=$edate ?>"/>
                                    </div>
                                    
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


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select User</label>
                                            <select id="user" class="custom-select rounded-0" name="user[]" data-live-search="true" multiple required>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select Category</label>
                                            <select class="custom-select rounded-0" name="category[]" id="category" multiple>
                                                <!-- <option value="select_all">Select All</option>
                                                <option value="topspender">Top Spender</option>
                                                <option value="focus_funnel">Focus Funnel</option>
                                                <option value="upsell_client">Upsell Client</option>
                                                <option value="keycompany">Key Company</option>
                                                <option value="pkclient ">Key CLient</option>
                                                <option value="priorityc ">Priority Client</option> -->
                                                <option value="select_all">Select All</option>
                                                <option value="topspender" <?= in_array('topspender', $category) ? 'selected' : '' ?>>Top Spender</option>
                                                <option value="focus_funnel" <?= in_array('focus_funnel', $category) ? 'selected' : '' ?>>Focus Funnel</option>
                                                <option value="upsell_client" <?= in_array('upsell_client', $category) ? 'selected' : '' ?>>Upsell Client</option>
                                                <option value="keycompany" <?= in_array('keycompany', $category) ? 'selected' : '' ?>>Key Company</option>
                                                <option value="pkclient" <?= in_array('pkclient', $category) ? 'selected' : '' ?>>Key Client</option>
                                                <option value="priorityc" <?= in_array('priorityc', $category) ? 'selected' : '' ?>>Priority Client</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6">
                                        <br>
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
                                <div id="CategoryWisePieChart" style="width: 100%; height: 400px;"></div>
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
                                                <!-- <div class="card-header">
                                                    <div class="row">
                                                        <?php foreach ($GraphData as $GraphDataSingle) { 
                                                            // var_dump($GraphDataSingle);
                                                            ?>
                                                            <div class="col-md-2 mb-2" >
                                                                <div class="card card p-2 col-sm m-auto bg-light" data-partnerType1="<?= htmlspecialchars($GraphDataSingle->PartnerMasterName) ?>">
                                                                    <strong>
                                                                        <a href="#" style="color:<?=$GraphDataSingle->PartnerMasterclr?>">
                                                                            <?=$GraphDataSingle->PartnerMasterName?> - <?=$GraphDataSingle->cont?>
                                                                        </a>
                                                                    </strong>
                                                                </div>
                                                            </div>
                                                        <?php }?>
                                                    </div>
                                                </div> -->
                                                <div class="card-body">
                                                    <div class="row">
                                                    <?php foreach($TableData as $TableDataGrid){

                                                        $tblc=$this->Graph_Model->get_tblbyidwithremark($TableDataGrid->ic_id);

                                                        if (sizeof($tblc) != 0) {

                                                            $remark=$tblc[0]->remarks;
                                                            $lastUpdateDate = $tblc[0]->updateddate;
                                                            $currentDate = new DateTime();
                                                            $NoUpdateSince = date_diff_format($lastUpdateDate, $currentDate->format('Y-m-d H:i:s'));
                                                            
                                                        }else{

                                                            $remark= "";
                                                            $lastUpdateDate = "";
                                                            $NoUpdateSince = "";
                                                        }   
                                                        ?> 
                                                        <div class="col-md-4 mb-4 filter-item" data-partnerType1="<?= htmlspecialchars($TableDataGrid->partner_typeName) ?>">
                                                            <div class="card-body p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                                                <!-- City : <br>
                                                                <strong><?= $TableDataGrid->city ?></strong><hr> -->
                                                                Current Status : <br>
                                                                <strong><?= $TableDataGrid->stname ?></strong><hr>
                                                                Company Name : <br>
                                                                <strong><?= $TableDataGrid->company_name ?></strong><hr>
                                                                Category : <br>
                                                                <strong><?php if($TableDataGrid->focus_funnel=='yes'){echo 'Focus Funnel, ';} if($TableDataGrid->upsell_client=='yes'){echo 'Upsell Client, ';} if($TableDataGrid->keycompany=='yes'){echo 'Key Company';}?></strong><hr>
                                                                Partner Type : <br>
                                                                <strong style="color:<?=$TableDataGrid->PartnerMasterclr?>"><?= $TableDataGrid->partner_typeName ?></strong><hr>
                                                                Current Remark<br><b style=""><?=$remark?></b><hr>
                                                                Last Action Date<br><b><?=$lastUpdateDate?></b><hr>
                                                                Same Status Since<br><b><?=$NoUpdateSince?></b><hr>
                                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
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
                                                            <!-- <th>City</th> -->
                                                            <th>Current Status</th>
                                                            <th>Company Name</th>
                                                            <th>Partner Type</th>
                                                            <th>Current Remark</th>
                                                            <th>Last Action Date</th>
                                                            <th>Same Status Since</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php 

                                                                $i=1;
                                                                foreach($TableData as $TableRow){ 
                                                                    $tblc=$this->Graph_Model->get_tblbyidwithremark($TableRow->ic_id);
                                                                        if (sizeof($tblc) != 0) {
                                                                            $remark=$tblc[0]->remarks;
                                                                            $lastUpdateDate = $tblc[0]->updateddate;
                                                                            $currentDate = new DateTime();
                                                                            // var_dump($currentDate);die;
                                                                        $NoUpdateSince = date_diff_format($lastUpdateDate, $currentDate->format('Y-m-d H:i:s'));
                                                                        }else{
                                                                            $remark= "";
                                                                            $lastUpdateDate = "";
                                                                            $NoUpdateSince = "";
                                                                        }   
                                                                    ?> 
                                                                    <tr>
                                                                        <!-- <td><?= $TableRow->city ?></td> -->
                                                                        <td><?= $TableRow->stname ?></td>
                                                                        <td><?= $TableRow->company_name ?></td>
                                                                        <td style="color:<?=$TableRow->PartnerMasterclr?>"><?= $TableRow->partner_typeName ?></td>
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
                                        <div class="tab-pane fade" id="TabView" role="tabpanel" aria-labelledby="nav_TabView">
                                            <div class="card-body">
                                                <div class="row">
                                                <?php foreach ($GraphData as $GraphDataGrid) { 
                                                    
                                                    $formId = 'hiddenForm_' . htmlspecialchars($GraphDataGrid->PartnerMasterID);
                                                    
                                                    ?>
                                                    <!-- <form id="<?= $formId; ?>" action="<?= base_url(); ?>GraphNew/SinglePartnerWiseData/<?=$GraphDataGrid->PartnerMasterID?>" method="POST" style="display: none;">

                                                        <input type="hidden" name="partnetType_id" value="<?= htmlspecialchars($GraphDataGrid->PartnerMasterID); ?>">
                                                        <input type="hidden" name="sdate" value="<?= htmlspecialchars($sdate); ?>">
                                                        <input type="hidden" name="edate" value="<?= htmlspecialchars($edate); ?>">
                                                    </form> -->
                                                    <div class="col-md-3 mb-2" >
                                                        <div class="card card p-3 col-sm m-auto bg-light">
                                                            <strong>
                                                                <a href="#" onclick="document.getElementById('<?= $formId; ?>').submit();"  style="color:<?=$GraphDataGrid->PartnerMasterclr ?>"><?=$GraphDataGrid->PartnerMasterName?> - <?=$GraphDataGrid->cont?>
	                                                            </a>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Get all category cards
    const categoryCards = document.querySelectorAll('.card-header .card');
    // Get all filter items
    const filterItems = document.querySelectorAll('.filter-item');

    // Function to filter grid items
    function filterItemsByCategory(partnerType) {
        filterItems.forEach(item => {
            // Check if the data-partnerType matches the selected partnerType
            if (item.dataset.partnertype1 === partnerType || partnerType === 'All') {
                item.style.display = 'block';  // Show item
            } else {
                item.style.display = 'none';   // Hide item
            }
        });
    }

    // Attach click event listeners to category cards
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const partnerType = this.dataset.partnertype1;
            console.log('Selected Partner Type:', partnerType); // Debugging
            filterItemsByCategory(partnerType);
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

        var sdate = <?php echo json_encode($sdate); ?>;
        var edate = <?php echo json_encode($edate); ?>;

        var dataArray = [
            ['Category', 'No of Company', '', ''] // Header row
        ];

        // var data = google.visualization.arrayToDataTable([

        //     ['Category', 'No of Compnay','',''],

        //     // ["No Category (<?php //$GraphData[0]->nocat?>)", <?php //$GraphData[0]->nocat?>,"0","<?php //$uid?>"],

        //     ["Top Spender (<?php //$GraphData[0]->topspender?>)", <?php //$GraphData[0]->topspender?>,"topspender","<?php //$uid?>"],

        //     ["Focus Funnel (<?php //$GraphData[0]->focus_funnel?>)", <?php //$GraphData[0]->focus_funnel?>,"focus_funnel","<?php //$uid?>"],

        //     ["Upsell Client (<?php // $GraphData[0]->upsell_client?>)", <?php //$GraphData[0]->upsell_client?>,"upsell_client","<?php //$uid?>"],

        //     ["Key Company (<?php //$GraphData[0]->keycompany?>)", <?php //$GraphData[0]->keycompany?>,"keycompany","<?php //$uid?>"],

        //     ["Positive Key Client (<?php //$GraphData[0]->pkclient?>)", <?php //$GraphData[0]->pkclient?>,"pkclient","<?php //$uid?>"],

        //     ["Priority Client (<?php //$GraphData[0]->priorityc?>)", <?php //$GraphData[0]->priorityc?>,"priorityc","<?php //$uid?>"],
 
        // ]);

        <?php 
        // Construct JavaScript array elements based on PHP data
            foreach ($category as $Singlecategory) {
                $value = $GraphData[0]->$Singlecategory; // Access the data dynamically
                $categoryName = ucfirst(str_replace('_', ' ', $Singlecategory));
                echo "dataArray.push([\"$categoryName ($value)\", $value, \"$Singlecategory\", \"$uid\"]);";
            }
        ?>
// Convert the PHP array to a JSON string


        var data = google.visualization.arrayToDataTable(dataArray);

        const options = {
            title:'Category Wise Funnel Graph',
            is3D:true
        };

        const chart = new google.visualization.PieChart(document.getElementById('CategoryWisePieChart'));

        google.visualization.events.addListener(chart, 'select', function() {

            var selection = chart.getSelection()[0];
            if (selection) {

                var Category = data.getValue(selection.row, 2);
                // console.log(Category);
            // Redirect to another URL with stid and uuid as parameters
                // window.location.href = '<?=base_url();?>GraphNew/CityWiseFunnelGraphData/' + cityid + '/' + sdate + '/' + edate ;
              //  var url = '<?=base_url();?>GraphNew/CategoryWiseFunnelGraphData/' + Category + '/' + sdate + '/' + edate;
                window.open(url, '_blank');
            }
        });

        chart.draw(data, options);
    }
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

        $("#category").change(function(){

            var selectedcategory = $(this).val(); 

            if (selectedcategory.includes('select_all')) {
            // Select all options
                $('#category option').prop('selected', true);
                // Remove 'select_all' from the selected values
                selectedcategory = $('#category option').map(function() {
                    return this.value !== 'select_all' ? this.value : null;
                }).get();

                selectedcategory = selectedcategory.filter(function(value) {
                    return value !== null;
                });
            }
        });
            
    });
</script>


