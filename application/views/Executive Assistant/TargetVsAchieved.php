<?php include('header.php'); ?>
<style>
.background-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('assets/image/crm/targetCRM.png');  /* Replace with your image path */
    background-size: cover;
    background-position: center;
    filter: blur(8px);  /* Apply blur effect */
    z-index: -1; /* Place the background behind content */
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid .background-image">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="container-fluid body-content">
                <div class="page-header">
                  <fieldset>
                    <?php 
                  // dd($sdate);
                     ?>
                    <div class="table-responsive">
                      <div class="table-responsive">
                        <div class="pdf-viwer">
                        <div class="container">
                        <form name="targetVsachivementform" method="post" action="targetVsAchievedData">
    <div class="row g-2 align-items-center">
        <div class="col-sm-4">
            <label for="reviewtype">Select Review Type</label>
            <select name="reviewtype" id="reviewtype" class="form-select" required>
                <option value="">Select Review Type</option>
                <option value="1">Weekly</option>
                <option value="2">Fortnightly</option>
                <option value="3">Monthly</option>
                <option value="4">Quarterly</option>
                <option value="6">Half Yearly</option>
                
                <!-- <?php foreach($allReviewType as $key=>$val){ ?>
                    <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                <?php } ?> -->
            </select>
        </div>
        <div class="col-sm-4">
            <label for="partnertype">Select Partner Type</label>
            <select name="partnertype" id="partnertype" class="form-select">
                <option value="">Select Partner Type</option>
                <?php foreach($partnerType as $key=>$val){ ?>
                    <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <hr>
    <div class="row g-2 align-items-center">
        <div class="col-sm-3">
            <label for="sdate">Start Date(mm/dd/yyyy)</label>
            <input type="date" class="form-control" name="sdate" id="sdate" required>
        </div>
        <div class="col-sm-3">
            <label for="edate">End Date(mm/dd/yyyy)</label>
            <input type="date" class="form-control" name="edate" id="edate" required readonly>
        </div>
        <div class="col-sm-2">
            <img src="<?php echo base_url();?>assets/image/crm/tandvsmall.avif" alt="TargetVsAchivement" class="img-fluid" style="width:45%;height=45%" />
        </div>
    </div>
    <hr>
    <div class="row g-2 align-items-center">
        <div class="col-sm-2">
            <input type="submit" value="Show Report" class="btn btn-primary w-100">
        </div>
                  
    </div>
</form>

<hr>
                        </div>
                          <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <input type="hidden" name="hidden_mdata" value="<?php print_r($targetVsAchieved);?>"/>
                            <thead>
                                <tr>
                                    <th>BD</th>
                                    <th colspan=3>Prospecting Schools</th>
                                    <th colspan=3>Proposal Count</th>
                                    <th colspan=3>Proposal Revenue</th>
                                    <th colspan=3>Closure Clients</th>
                                    <!-- <th colspan=2>Closure Schools</th> -->
                                    <th colspan=3>Closure Revenue</th>
                                </tr>
                              <tr>
                                <th></th>
                                <th>Target</th>
                                <th>Achieved</th>
                                <th></th>
                                <th>Target</th>
                                <th>Achieved</th> 
                                <th></th>
                                <th>Target</th>
                                <th>Achieved</th>
                                <th></th>
                                <th>Target</th>
                                <th>Achieved</th>
                                <th></th>
                                 <!-- <th>Target</th>
                                <th>Achieved</th> -->
                                <th>Target</th>
                                <th>Achieved</th>
                                <th></th>
                              </tr>
                            </thead>
                                <tbody>
                                    <?php
                                    foreach($targetVsAchieved as $key=>$val){
                                      if(!empty($val['target'])){
                                        /***Porspecting */
                                        if($val['prospecting_target'] > $val['prospective_achieved']){
                                          $prospimage = '<i class="far fa-grin-beam"></i>'; //Not Good Performance
                                        }
                                        else if($val['prospecting_target'] = $val['prospective_achieved']){
                                          $prospimage = '<i class="far fa-grin"></i>';//Good Performance
                                        }
                                        else{
                                          $prospimage='<i class="far fa-frown"></i>';//Great Performance
                                        }

                                          /***Proposal Target*/
                                        if($val['proposal_target'] > $val['proposal_achieved']){
                                          $propsalimage = '<i class="far fa-grin-beam"></i>'; //Not Good Performance
                                        }
                                        else if($val['proposal_target'] = $val['proposal_achieved']){
                                          $propsalimage = '<i class="far fa-grin"></i>';//Good Performance
                                        }
                                        else{
                                          $propsalimage='<i class="far fa-frown"></i>';//Great Performance
                                        }

                                          /***Proposal Revenue*/
                                        if($val['proposal_revenue'] > $val['proposal_revenue_achieved']){
                                          $prosreveimage = '<i class="far fa-grin-beam"></i>'; //Not Good Performance
                                        }
                                        else if($val['proposal_revenue'] = $val['proposal_revenue_achieved']){
                                          $prosreveimage = '<i class="far fa-grin"></i>';//Good Performance
                                        }
                                        else{
                                          $prosreveimage='<i class="far fa-frown"></i>';//Great Performance
                                        }

                                         /***Closure target*/
                                        if($val['closure_client_target'] > $val['closure_clients_achieved']){
                                          $closureclientreveimage = '<i class="far fa-grin-beam"></i>'; //Not Good Performance
                                        }
                                        else if($val['closure_client_target'] = $val['closure_clients_achieved']){
                                          $closureclientreveimage = '<i class="far fa-grin"></i>';//Good Performance
                                        }
                                        else{
                                          $closureclientreveimage='<i class="far fa-frown"></i>';//Great Performance
                                        }

                                         /***Closure Revenue*/
                                        if($val['closure_revenue_target'] > $val['closure_revenue_achieved']){
                                          $closurerevenueimage = '<i class="far fa-grin-beam"></i>'; //Not Good Performance
                                        }
                                        else if($val['closure_revenue_target'] = $val['closure_revenue_achieved']){
                                          $closurerevenueimage = '<i class="far fa-grin"></i>';//Good Performance
                                        }
                                        else{
                                          $closurerevenueimage='<i class="far fa-frown"></i>';//Great Performance
                                        }
                                       ?>
                                       <tr>
                                             <!-- <td><?php //echo getUserNameById($key['targetUserId']); ?></td> -->
                                              <td><?php echo ($val['targetUserId'])? getUserNameById($val['targetUserId']) :"-"; ?></td>
                                              <td><?php echo ($val['prospecting_target'])? $val['prospecting_target'] :"-"; ?></td>
                                              <td><a href='<?php echo base_url();?>Menu/getAchievedDataList/<?php echo $val['targetUserId']?>/prospective_achieved/<?php echo $sdate;?>/<?php echo $edate;?>'><?php echo ($val['prospective_achieved'])? $val['prospective_achieved']:"-"; ?></a></td>
                                              <td><?php echo $prospimage;?></td>
                                              <td><?php echo ($val['proposal_target'])? $val['proposal_target']:"-"; ?></td>
                                              <td><a href='<?php echo base_url();?>Menu/getAchievedDataList/<?php echo $val['targetUserId']?>/proposal_achieved/<?php echo $sdate;?>/<?php echo $edate;?>'><?php echo ($val['proposal_achieved'])? $val['proposal_achieved']:"-" ?></a></td>
                                              <td><?php echo $image;?></td>
                                              <td><?php echo ($val['proposal_revenue'])? $val['proposal_revenue']:"-"; ?></td>
                                              <td><a href='<?php echo base_url();?>Menu/getAchievedDataList/<?php echo $val['targetUserId']?>/revenue_achieved/<?php echo $sdate;?>/<?php echo $edate;?>'><?php echo ($val['proposal_revenue_achieved']) ? $val['proposal_revenue_achieved']:"-";?></a></td>
                                              <td><?php echo $prosreveimage;?></td>

                                              <td><?php echo ($val['closure_client_target'])? $val['closure_client_target']:"-"; ?></td>
                                             
                                              <td><a href='<?php echo base_url();?>Menu/getAchievedDataList/<?php echo $val['targetUserId']?>/closure_achieved/<?php echo $sdate;?>/<?php echo $edate;?>'><?php echo ($val['closure_clients_achieved'])? $val['closure_clients_achieved']:"-"; ?></a></td>
                                              <!-- <td><?php //echo ($val['closure_school_target'])? $val['closure_school_target']:"-"; ?></td>
                                              <td><?php //echo ($val['closure_schools_achieved'])? $val['closure_schools_achieved']:"-"; ?></td> -->
                                              <td><?php echo $closureclientreveimage;?></td>
                                              <td><?php echo ($val['closure_revenue_target'])? $val['closure_revenue_target']:"-"; ?></td>
                                              <td><a href='<?php echo base_url();?>Menu/getAchievedDataList/<?php echo $val['targetUserId']?>/closure_achieved/<?php echo $sdate;?>/<?php echo $edate;?>'><?php echo ($val['closure_revenue_achieved'])? $val['closure_revenue_achieved'] :"-"; ?></a></td>
                                              <td><?php echo $closurerevenueimage;?></td>

                                            </tr>
                                       <?php }
                                    }?>
                                </tbody>
                            </table>
                          <div style="width: 60%; margin: auto;">
                              <canvas id="targetAchievedChart"></canvas>
                          </div>

                          </div>
                        </div>
                        </form>            <!--END OF FORM ^^-->
                      </fieldset>
                    </div>
                  </div></div></div></div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </section>
      
      <?php include('footer.php') ;?>
      <script>
        // Sample data extracted from your array structure
        // var data = {
        //     100059: {
        //         prospecting_target: 20,
        //         prospecting_achieved: 13,
        //         proposal_target: 20,
        //         proposal_achieved: 3,
        //         closure_client_target: 5,
        //         closure_clients_achieved: 1
        //     },
        //     100062: {
        //         prospecting_target: 10,
        //         prospecting_achieved: 11,
        //         proposal_target: 12,
        //         proposal_achieved: 0,
        //         closure_client_target: 0,
        //         closure_clients_achieved: 0
        //     },
        //     100087: {
        //         prospecting_target: 40,
        //         prospecting_achieved: 20,
        //         proposal_target: 20,
        //         proposal_achieved: 0,
        //         closure_client_target: 0,
        //         closure_clients_achieved: 0
        //     },
        //     100172: {
        //         prospecting_target: 35,
        //         prospecting_achieved: 9,
        //         proposal_target: 12,
        //         proposal_achieved: 0,
        //         closure_client_target: 0,
        //         closure_clients_achieved: 0
        //     }
        // };

        var data = <?php $data;?>
        // Extracting data for plotting
        var labels = ['Prospecting', 'Proposal', 'Closure Clients'];
        var targetData = [
            data[100059].prospecting_target, data[100059].proposal_target, data[100059].closure_client_target,
            data[100062].prospecting_target, data[100062].proposal_target, data[100062].closure_client_target,
            data[100087].prospecting_target, data[100087].proposal_target, data[100087].closure_client_target,
            data[100172].prospecting_target, data[100172].proposal_target, data[100172].closure_client_target
        ];

        var achievedData = [
            data[100059].prospecting_achieved, data[100059].proposal_achieved, data[100059].closure_clients_achieved,
            data[100062].prospecting_achieved, data[100062].proposal_achieved, data[100062].closure_clients_achieved,
            data[100087].prospecting_achieved, data[100087].proposal_achieved, data[100087].closure_clients_achieved,
            data[100172].prospecting_achieved, data[100172].proposal_achieved, data[100172].closure_clients_achieved
        ];

        // Configuration for the bar chart
        var ctx = document.getElementById('targetAchievedChart').getContext('2d');
        var targetAchievedChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: ['User 100059', 'User 100062', 'User 100087', 'User 100172'],
                datasets: [
                    {
                        label: 'Prospecting Target',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        data: [
                            data[100059].prospecting_target, data[100059].proposal_target, data[100059].closure_client_target,
                            data[100062].prospecting_target, data[100062].proposal_target, data[100062].closure_client_target,
                            data[100087].prospecting_target, data[100087].proposal_target, data[100087].closure_client_target,
                            data[100172].prospecting_target, data[100172].proposal_target, data[100172].closure_client_target
                        ]
                    },
                    {
                        label: 'Prospecting Achieved',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        data: [
                            data[100059].prospecting_achieved, data[100059].proposal_achieved, data[100059].closure_clients_achieved,
                            data[100062].prospecting_achieved, data[100062].proposal_achieved, data[100062].closure_clients_achieved,
                            data[100087].prospecting_achieved, data[100087].proposal_achieved, data[100087].closure_clients_achieved,
                            data[100172].prospecting_achieved, data[100172].proposal_achieved, data[100172].closure_clients_achieved
                        ]
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Values'
                        }
                    }
                }
            }
        });
    </script>
      <!-- jQuery for Auto-Filling End Date -->
<script>
    $(document).ready(function() {
        $('#sdate, #reviewtype').on('change', function() {
            var startDate = $('#sdate').val();
            var reviewType = $('#reviewtype').val();
            if (startDate && reviewType) {
                var endDate = new Date(startDate);
                if (reviewType === "1" ) {
                    endDate.setDate(endDate.getDate() + 7); // Add 7 days for weekly review
                } else if (reviewType === "2") {
                  endDate.setDate(endDate.getDate() + 14);// Add Fortnightly review
                }
                else if(reviewType === "3"){
                  endDate.setMonth(endDate.getMonth() + 1); // Add Monthly month for monthly review
                }
                else if(reviewType === "4"){
                  endDate.setMonth(endDate.getMonth() + 3); 
                }
                else if(reviewType === "6"){
                  endDate.setMonth(endDate.getMonth() + 6); 
                }
                // Format the end date to YYYY-MM-DD
                var year = endDate.getFullYear();
                var month = (endDate.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-indexed
                var day = endDate.getDate().toString().padStart(2, '0');
                console.log(startDate+"=="+day+"-"+month+"-"+year);
                $('#edate').val(`${year}-${month}-${day}`);
            }
        });
    });
</script>