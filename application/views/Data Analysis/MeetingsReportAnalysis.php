<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphs and Data Table</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
        .chart-container {
            margin-bottom: 30px;
        }
        .chart-heading {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .chart-description {
            text-align: center;
            margin-bottom: 20px;
            font-style: italic;
            color: #666;
        }
        .filter-container {
            margin-bottom: 20px;
        }
        .pie-chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }
        .pie-chart-container canvas {
            width: 500px !important;
            height: 500px !important;
        }
     



    section.nav-main {
        background: #f0f8ff;
    }

    span.btn.btn-primary {
        margin: 4px;
        border: 0;
        box-shadow: rgba(0, 0, 0, 0.15) 0 2px 8px;
    }

    a {
        color: var(--bs-link-color);
        text-decoration: none;
        font-size: 16px;
        font-weight: 400;
        color: #f900dd;
    }

    span.footer-title {
        color: #c0026c;
        font-weight: 500;
    }

    .colaps-title.text-center {
        background: beige;
        padding: 4px;
        box-shadow: rgba(9, 30, 66, 0.25) 0 1px 1px, rgba(9, 30, 66, 0.13) 0 0 1px 1px;
    }

    .nav-tabs .nav-link {
        width: 300px;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        background: #ff0061;
        color: #fff;
    }

    li.nav-item {
        align-items: center;
        justify-content: center;
        display: flex;
    }

    .active-btn {
        background-color: #28a745 !important;
        color: #fff !important;
    }

    span.usernameclass {
        color: #ce08b0;
        font-weight: 500;
    }

    .horizentallineclass {
        color: red;
    }

    a.nav-link {
        color: #ff00ae !important;
    }

    button.accordion-button.collapsed {
        font-weight: 500;
        color: #ff00ae;
        font-size: 16px;
    }

    body::-webkit-scrollbar {
        width: 15px;
    }

    body::-webkit-scrollbar-track {
        box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    }

    body::-webkit-scrollbar-thumb {
        border-radius: 15px;
        border: none;
        outline: 0;
        background: #a8f268;
        background: linear-gradient(90deg, #a8f268 0, #f7025c 100%);
        background: -moz-linear-gradient(90deg, #a8f268 0, #f7025c 100%);
        background: -webkit-linear-gradient(90deg, #a8f268 0, #f7025c 100%);
    }

    /* Row and Graph Styling */
    .list-group-item {
        background-color: #f8f9fa;
        margin-bottom: 5px;
        border-left: 4px solid #ff0061;
    }

    .list-group-item:nth-child(even) {
        background-color: #e9ecef;
    }

    .list-group-item:hover {
        background-color: #dee2e6;
    }

    .card {
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .colaps-title.text-center {
        background: #ff0061;
        color: white;
        padding: 10px;
    }

    .accordion-button {
        background-color: #f8f9fa;
    }

    .accordion-button:not(.collapsed) {
        background-color: #ff0061;
        color: white;
    }

    .accordion-item {
        margin-bottom: 10px;
    }

    .accordion-body {
        background-color: #f8f9fa;
    }
    </style>
</head>
<body>

<?php include ("nav.php"); ?>

    <div class="container-fluid">
    <br>
    <hr>
    <div class="text-center">
        <div class="bg-primary p-2">
        <h2 class="text-center text-white">Meeting Data Analysis By User - (<?=$sdate?> To <?=$edate?>) </h2>
        </div>
    </div>
    <hr>
    <br>
    <?php
    $cttype = $user['type_id'];
    if($cttype == 2 || $cttype == 19 || $cttype == 20 || $cttype == 21 || $cttype == 22 || $cttype == 23){
       ?>
  <?php $clusterPstDatas = $this->Menu_model->GetAdminActiveTeam($user['user_id']); ?>
  <form action="<?=base_url()?>SalesGraph/MeetingsReportAnalysis" method="post">
            <div class="row mb-4">
                <div class="col-md-2">
                    <label for="selectedDate">Start Date</label>
                    <input type="date" value="<?=$sdate;?>" id="selectedDate" name="sdate" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="selectedDate">End Date</label>
                    <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="selectedDate">Select Admin</label>
                    <select id="admin_id_filter" name="admin_id_filter" class="form-control">
                        <option value="all">All</option>
                        <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                            <option value="<?= $clusterPstData->user_id; ?>" <?= ($clusterPstData->user_id == $admin_id_filter) ? 'selected' : ''; ?>>
                                <?= $clusterPstData->name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                    <label for="selectedDate">Select RM</label>
                    <select id="rm_filter" name="rm_filter" class="form-control">
                        <option value="all">All</option>
                        <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                            <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $uid) ? 'selected' : ''; ?>>
                                <?= $getTeamsData->name; ?>
                            </option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary p-1" style="margin-top: 36px; width: 100px;">Filter</button>
                    </div>
                </div>
            </div>
        </form>
       <?php  }else{ ?>
         <form action="<?=base_url()?>SalesGraph/MeetingsReportAnalysis" method="post">
            <div class="row mb-4">
                <div class="col-md-5">
                    <label for="selectedDate">Start Date</label>
                    <input type="date" value="<?=$sdate;?>" id="selectedDate" name="sdate" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="selectedDate">End Date</label>
                    <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" class="form-control">
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary p-1" style="margin-top: 36px; width: 100px;">Filter</button>
                    </div>
                </div>
            </div>
        </form>
        <?php } ?>
    <hr>

        <!-- Filter Section -->
        <div class="filter-container">
            <div class="row">
                <div class="col-md-4">
                    <label for="userZoneFilter">User Zone:</label>
                    <select id="userZoneFilter" class="form-control">
                        <option value="">All</option>
                        <!-- User Zone options will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="userRolesFilter">User Roles:</label>
                    <select id="userRolesFilter" class="form-control">
                        <option value="">All</option>
                        <!-- User Roles options will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="nameFilter">Name:</label>
                    <select id="nameFilter" class="form-control">
                        <option value="">All</option>
                        <!-- Name options will be populated dynamically -->
                    </select>
                </div>
            </div>

            <br>
            <!-- Add more filters as needed -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <button class="btn btn-success" id="downloadPdf">Download PDF</button>
                    <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
                </div>
            </div>
            <br>
            <!-- Add more filters as needed -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <a class="bg-primary p-1 text-white" target="_BLANK" href="<?=base_url();?>Menu/MeetingDetailNew">
                        Go To Details Analysis
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
            <table id="meetingDataTable" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>User Roles</th>
                        <th>User Zone</th>
                        <th>Total Meetings</th>
                        <th>Complete Meetings</th>
                        <th>Not Complete Meetings</th>
                        <th>Total Scheduled Meetings</th>
                        <th>Total Complete Scheduled Meetings</th>
                        <th>Not Complete Scheduled Meetings</th>
                        <th>Total Barg Meetings</th>
                        <th>Total Complete Barg Meetings</th>
                        <th>Not Complete Barg Meetings</th>
                        <th>Total Join Meetings</th>
                        <th>Total Complete Join Meetings</th>
                        <th>Not Complete Join Meetings</th>
                        <th>Total RP Meetings</th>
                        <th>Total NO RP Meetings</th>
                        <th>Total Only Got Details Meetings</th>
                        <th>Total Scheduled RP Meetings</th>
                        <th>Total Scheduled NO RP Meetings</th>
                        <th>Total Scheduled Only Got Details Meetings</th>
                        <th>Total Barge RP Meetings</th>
                        <th>Total Barge NO RP Meetings</th>
                        <th>Total Barge Only Got Details Meetings</th>
                        <th>Total Potential Meetings</th>
                        <th>Total Non Potential Meetings</th>
                        <th>Total Topspender Meetings</th>
                        <th>Total Upsell Client Meetings</th>
                        <th>Total Focus Funnel Meetings</th>
                        <th>Total Keycompany Meetings</th>
                        <th>Total Pkclient Meetings</th>
                        <th>Total Priorityc Meetings</th>
                        <th>Total Fresh Meetings</th>
                        <th>Total Re Meetings</th>
                        <th>Total Write Mom RP Meetings</th>
                        <th>Total Pending For Write Mom RP Meeting</th>
                        <th>Total Mom Data</th>
                        <th>Total Approved After Check Mom Data</th>
                        <th>Total Reject After Check Mom Data</th>
                        <th>Total NO RP After Check Mom Data</th>
                        <th>Total Pending For Check Mom Data</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table content will be dynamically updated by JavaScript -->
                </tbody>
            </table>
        </div>
        <hr>
        <br>
        <!-- Graphs Section -->
        <div class="row chart-container">
            <div class="col-md-4">
                <div class="chart-heading">Total Plan Meetings</div>
                <div class="chart-description">Total number of planned meetings for each user.</div>
                <canvas id="totalPlanMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Complete Meetings</div>
                <div class="chart-description">Total number of completed meetings for each user.</div>
                <canvas id="completeMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Not Complete Meetings</div>
                <div class="chart-description">Total number of incomplete meetings for each user.</div>
                <canvas id="notCompleteMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-4">
                <div class="chart-heading">Total RP Meetings</div>
                <div class="chart-description">Total number of RP meetings for each user.</div>
                <canvas id="totalRPMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total NO RP Meetings</div>
                <div class="chart-description">Total number of NO RP meetings for each user.</div>
                <canvas id="totalNO_RPMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Only Got Details Meetings</div>
                <div class="chart-description">Total number of meetings where only details were got for each user.</div>
                <canvas id="totalOnlyGotDetailsMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <!-- Rest of the existing charts -->
        <div class="row chart-container">
        <div class="col-md-4">
                <div class="chart-heading">Total Scheduled Meetings</div>
                <div class="chart-description">Total number of scheduled meetings for each user.</div>
                <canvas id="totalScheduledMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Complete Scheduled Meetings</div>
                <div class="chart-description">Total number of completed scheduled meetings for each user.</div>
                <canvas id="totalCompleteScheduledMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Not Complete Scheduled Meetings</div>
                <div class="chart-description">Total number of incomplete scheduled meetings for each user.</div>
                <canvas id="notCompleteScheduledMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-4">
                <div class="chart-heading">Total Scheduled RP Meetings</div>
                <div class="chart-description">Total number of scheduled RP meetings for each user.</div>
                <canvas id="totalScheduledRPMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Scheduled NO RP Meetings</div>
                <div class="chart-description">Total number of scheduled NO RP meetings for each user.</div>
                <canvas id="totalScheduledNO_RPMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Scheduled Only Got Details Meetings</div>
                <div class="chart-description">Total number of scheduled meetings where only details were got for each user.</div>
                <canvas id="totalScheduledOnlyGotDetailsMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-4">
                <div class="chart-heading">Total Barg Meetings</div>
                <div class="chart-description">Total number of barge meetings for each user.</div>
                <canvas id="totalBargMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Complete Barg Meetings</div>
                <div class="chart-description">Total number of completed barge meetings for each user.</div>
                <canvas id="totalCompleteBargMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Not Complete Barg Meetings</div>
                <div class="chart-description">Total number of incomplete barge meetings for each user.</div>
                <canvas id="notCompleteBargMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-4">
                <div class="chart-heading">Total Barge RP Meetings</div>
                <div class="chart-description">Total number of barge RP meetings for each user.</div>
                <canvas id="totalBargeRPMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Barge NO RP Meetings</div>
                <div class="chart-description">Total number of barge NO RP meetings for each user.</div>
                <canvas id="totalBargeNO_RPMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Barge Only Got Details Meetings</div>
                <div class="chart-description">Total number of barge meetings where only details were got for each user.</div>
                <canvas id="totalBargeOnlyGotDetailsMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
        <div class="col-md-4">
                <div class="chart-heading">Total Join Meetings</div>
                <div class="chart-description">Total number of join meetings for each user.</div>
                <canvas id="totalJoinMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Complete Join Meetings</div>
                <div class="chart-description">Total number of completed join meetings for each user.</div>
                <canvas id="totalCompleteJoinMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Not Complete Join Meetings</div>
                <div class="chart-description">Total number of incomplete join meetings for each user.</div>
                <canvas id="notCompleteJoinMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-6">
                <div class="chart-heading">Total Potential Meetings</div>
                <div class="chart-description">Total number of potential meetings for each user.</div>
                <canvas id="totalPotentialMeetingsChart"></canvas>
            </div>
            <div class="col-md-6">
                <div class="chart-heading">Total Non Potential Meetings</div>
                <div class="chart-description">Total number of non-potential meetings for each user.</div>
                <canvas id="totalNonPotentialMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
        <div class="col-md-4">
                <div class="chart-heading">Total Topspender Meetings</div>
                <div class="chart-description">Total number of topspender meetings for each user.</div>
                <canvas id="totalTopspenderMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Upsell Client Meetings</div>
                <div class="chart-description">Total number of upsell client meetings for each user.</div>
                <canvas id="totalUpsellClientMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Focus Funnel Meetings</div>
                <div class="chart-description">Total number of focus funnel meetings for each user.</div>
                <canvas id="totalFocusFunnelMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-4">
                <div class="chart-heading">Total Keycompany Meetings</div>
                <div class="chart-description">Total number of keycompany meetings for each user.</div>
                <canvas id="totalKeycompanyMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Pkclient Meetings</div>
                <div class="chart-description">Total number of pkclient meetings for each user.</div>
                <canvas id="totalPkclientMeetingsChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Priorityc Meetings</div>
                <div class="chart-description">Total number of priorityc meetings for each user.</div>
                <canvas id="totalPrioritycMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
        <div class="col-md-6">
                <div class="chart-heading">Total Fresh Meetings</div>
                <div class="chart-description">Total number of fresh meetings for each user.</div>
                <canvas id="totalFreshMeetingsChart"></canvas>
            </div>
            <div class="col-md-6">
                <div class="chart-heading">Total Re Meetings</div>
                <div class="chart-description">Total number of re meetings for each user.</div>
                <canvas id="totalReMeetingsChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
        <div class="col-md-6">
                <div class="chart-heading">Total Write Mom RP Meetings</div>
                <div class="chart-description">Total number of write mom RP meetings for each user.</div>
                <canvas id="totalWriteMomRPMeetingsChart"></canvas>
            </div>
            <div class="col-md-6">
                <div class="chart-heading">Total Pending For Write Mom RP Meeting</div>
                <div class="chart-description">Total number of pending write mom RP meetings for each user.</div>
                <canvas id="totalPendingForWriteMomRPMeetingChart"></canvas>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
        <div class="col-md-4">
                <div class="chart-heading">Total Mom Data</div>
                <div class="chart-description">Total number of mom data for each user.</div>
                <canvas id="totalMomDataChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Approved After Check Mom Data</div>
                <div class="chart-description">Total number of approved mom data after check for each user.</div>
                <canvas id="totalApprovedAfterCheckMomDataChart"></canvas>
            </div>
            <div class="col-md-4">
                <div class="chart-heading">Total Reject After Check Mom Data</div>
                <div class="chart-description">Total number of rejected mom data after check for each user.</div>
                <canvas id="totalRejectAfterCheckMomDataChart"></canvas>
            </div>
        </div>
       <div class="bgcolor4">
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-6">
                <div class="chart-heading">Total NO RP After Check Mom Data</div>
                <div class="chart-description">Total number of NO RP mom data after check for each user.</div>
                <canvas id="totalNO_RPAfterCheckMomDataChart"></canvas>
            </div>
            <div class="col-md-6">
                <div class="chart-heading">Total Pending For Check Mom Data</div>
                <div class="chart-description">Total number of pending mom data for check for each user.</div>
                <canvas id="totalPendingForCheckMomDataChart"></canvas>
            </div>
        </div>
       </div>

         <!-- New Pie Charts -->
         <div class="row chart-container">
            <div class="col-md-6 pie-chart-container">
                <div>
                <div class="chart-heading">Total Plan Meetings VS Complete Meetings</div>
                <div class="chart-description">Comparison of Total Plan Meetings and Complete Meetings.</div>
                <canvas id="totalPlanVsCompleteMeetingsChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 pie-chart-container">
              <div>
              <div class="chart-heading">Complete Meetings VS Total RP Meetings</div>
                <div class="chart-description">Comparison of Complete Meetings and Total RP Meetings.</div>
                <canvas id="completeVsTotalRPMeetingsChart"></canvas>
              </div>
            </div>
        </div>
        <hr>
        <br>
        <div class="row chart-container">
            <div class="col-md-6 pie-chart-container">
               <div>
               <div class="chart-heading">Complete Meetings VS Total NO RP Meetings</div>
                <div class="chart-description">Comparison of Complete Meetings and Total NO RP Meetings.</div>
                <canvas id="completeVsTotalNO_RPMeetingsChart"></canvas>
               </div>
            </div>
            <div class="col-md-6 pie-chart-container">
                <div>
                <div class="chart-heading">Complete Meetings VS Total Only Got Details Meetings</div>
                <div class="chart-description">Comparison of Complete Meetings and Total Only Got Details Meetings.</div>
                <canvas id="completeVsTotalOnlyGotDetailsMeetingsChart"></canvas>
                </div>
            </div>
        </div>
        <hr>
        <br>

    </div>
    <?php include ("footer.php"); ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

    <script>
        // PHP data embedded in JavaScript
        const data = <?php echo json_encode($taskDatadatas, true); ?>;

        // Function to create a bar chart
        function createBarChart(ctx, labels, data, label) {
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Function to create a pie chart
        function createPieChart(ctx, labels, data, label) {
            return new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            formatter: (value, ctx) => {
                                let total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return `${percentage}%`;
                            },
                            color: '#fff',
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        // Function to create a line chart
        function createLineChart(ctx, labels, data, label) {
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Function to filter data based on selected filters
        function filterData() {
            const userRolesFilter = document.getElementById('userRolesFilter').value;
            const nameFilter = document.getElementById('nameFilter').value;
            const userZoneFilter = document.getElementById('userZoneFilter').value;

            return data.filter(user => {
                return (userRolesFilter === '' || user.user_roles === userRolesFilter) &&
                       (nameFilter === '' || user.name === nameFilter) &&
                       (userZoneFilter === '' || user.user_zone === userZoneFilter);
            });
        }

        // Function to update the table content based on filtered data
        function updateTable(filteredData) {
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = ''; // Clear existing table rows

            filteredData.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.name}</td>
                    <td>${user.user_roles}</td>
                    <td>${user.user_zone}</td>
                    <td>${user.total_plan_meetings}</td>
                    <td>${user.complete_meetings}</td>
                    <td>${user.not_complete_meetings}</td>
                    <td>${user.total_sheduled_meetings}</td>
                    <td>${user.total_complete_sheduled_meetings}</td>
                    <td>${user.not_complete_sheduled_meetings}</td>
                    <td>${user.total_barg_meetings}</td>
                    <td>${user.total_complete_barg_meetings}</td>
                    <td>${user.not_complete_barg_meetings}</td>
                    <td>${user.total_join_meetings}</td>
                    <td>${user.total_complete_join_meetings}</td>
                    <td>${user.not_complete_join_meetings}</td>
                    <td>${user.total_RP_meetings}</td>
                    <td>${user.total_NO_RP_meetings}</td>
                    <td>${user.total_Only_Got_details_meetings}</td>
                    <td>${user.total_Sheduled_RP_meetings}</td>
                    <td>${user.total_Sheduled_NO_RP_meetings}</td>
                    <td>${user.total_Sheduled_Only_Got_details_meetings}</td>
                    <td>${user.total_Barge_RP_meetings}</td>
                    <td>${user.total_Barge_NO_RP_meetings}</td>
                    <td>${user.total_Barge_Only_Got_details_meetings}</td>
                    <td>${user.total_potential_meetings}</td>
                    <td>${user.total_non_potential_meetings}</td>
                    <td>${user.total_topspender_meetings}</td>
                    <td>${user.total_upsell_client_meetings}</td>
                    <td>${user.total_focus_funnel_meetings}</td>
                    <td>${user.total_keycompany_meetings}</td>
                    <td>${user.total_pkclient_meetings}</td>
                    <td>${user.total_priorityc_meetings}</td>
                    <td>${user.total_fresh_meetings}</td>
                    <td>${user.total_re_meetings}</td>
                    <td>${user.total_write_mom_rp_meetings}</td>
                    <td>${user.total_pending_for_write_mom_rp_meeting}</td>
                    <td>${user.total_mom_data}</td>
                    <td>${user.total_approved_after_check_mom_data}</td>
                    <td>${user.total_reject_after_check_mom_data}</td>
                    <td>${user.total_NO_RP_after_check_mom_data}</td>
                    <td>${user.total_pending_for_check_mom_data}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Function to update charts based on filtered data
        function updateCharts() {
            const filteredData = filterData();
            const labels = filteredData.map(user => user.name);

            // Update each chart with filtered data
            totalPlanMeetingsChart.data.labels = labels;
            totalPlanMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_plan_meetings));
            totalPlanMeetingsChart.update();

            completeMeetingsChart.data.labels = labels;
            completeMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.complete_meetings));
            completeMeetingsChart.update();

            // Update pie charts
            totalPlanVsCompleteMeetingsChart.data.datasets[0].data = [
                filteredData.reduce((sum, user) => sum + parseInt(user.total_plan_meetings), 0),
                filteredData.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0)
            ];
            totalPlanVsCompleteMeetingsChart.update();

            completeVsTotalRPMeetingsChart.data.datasets[0].data = [
                filteredData.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0),
                filteredData.reduce((sum, user) => sum + parseInt(user.total_RP_meetings), 0)
            ];
            completeVsTotalRPMeetingsChart.update();

            completeVsTotalNO_RPMeetingsChart.data.datasets[0].data = [
                filteredData.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0),
                filteredData.reduce((sum, user) => sum + parseInt(user.total_NO_RP_meetings), 0)
            ];
            completeVsTotalNO_RPMeetingsChart.update();

            completeVsTotalOnlyGotDetailsMeetingsChart.data.datasets[0].data = [
                filteredData.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0),
                filteredData.reduce((sum, user) => sum + parseInt(user.total_Only_Got_details_meetings), 0)
            ];
            completeVsTotalOnlyGotDetailsMeetingsChart.update();

            // Update other charts similarly...
            notCompleteMeetingsChart.data.labels = labels;
            notCompleteMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.not_complete_meetings));
            notCompleteMeetingsChart.update();

            totalScheduledMeetingsChart.data.labels = labels;
            totalScheduledMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_sheduled_meetings));
            totalScheduledMeetingsChart.update();

            totalCompleteScheduledMeetingsChart.data.labels = labels;
            totalCompleteScheduledMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_complete_sheduled_meetings));
            totalCompleteScheduledMeetingsChart.update();

            notCompleteScheduledMeetingsChart.data.labels = labels;
            notCompleteScheduledMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.not_complete_sheduled_meetings));
            notCompleteScheduledMeetingsChart.update();

            totalBargMeetingsChart.data.labels = labels;
            totalBargMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_barg_meetings));
            totalBargMeetingsChart.update();

            totalCompleteBargMeetingsChart.data.labels = labels;
            totalCompleteBargMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_complete_barg_meetings));
            totalCompleteBargMeetingsChart.update();

            notCompleteBargMeetingsChart.data.labels = labels;
            notCompleteBargMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.not_complete_barg_meetings));
            notCompleteBargMeetingsChart.update();

            totalJoinMeetingsChart.data.labels = labels;
            totalJoinMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_join_meetings));
            totalJoinMeetingsChart.update();

            totalCompleteJoinMeetingsChart.data.labels = labels;
            totalCompleteJoinMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_complete_join_meetings));
            totalCompleteJoinMeetingsChart.update();

            notCompleteJoinMeetingsChart.data.labels = labels;
            notCompleteJoinMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.not_complete_join_meetings));
            notCompleteJoinMeetingsChart.update();

            totalRPMeetingsChart.data.labels = labels;
            totalRPMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_RP_meetings));
            totalRPMeetingsChart.update();

            totalNO_RPMeetingsChart.data.labels = labels;
            totalNO_RPMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_NO_RP_meetings));
            totalNO_RPMeetingsChart.update();

            totalOnlyGotDetailsMeetingsChart.data.labels = labels;
            totalOnlyGotDetailsMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_Only_Got_details_meetings));
            totalOnlyGotDetailsMeetingsChart.update();

            totalScheduledRPMeetingsChart.data.labels = labels;
            totalScheduledRPMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_Sheduled_RP_meetings));
            totalScheduledRPMeetingsChart.update();

            totalScheduledNO_RPMeetingsChart.data.labels = labels;
            totalScheduledNO_RPMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_Sheduled_NO_RP_meetings));
            totalScheduledNO_RPMeetingsChart.update();

            totalScheduledOnlyGotDetailsMeetingsChart.data.labels = labels;
            totalScheduledOnlyGotDetailsMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_Sheduled_Only_Got_details_meetings));
            totalScheduledOnlyGotDetailsMeetingsChart.update();

            totalBargeRPMeetingsChart.data.labels = labels;
            totalBargeRPMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_Barge_RP_meetings));
            totalBargeRPMeetingsChart.update();

            totalBargeNO_RPMeetingsChart.data.labels = labels;
            totalBargeNO_RPMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_Barge_NO_RP_meetings));
            totalBargeNO_RPMeetingsChart.update();

            totalBargeOnlyGotDetailsMeetingsChart.data.labels = labels;
            totalBargeOnlyGotDetailsMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_Barge_Only_Got_details_meetings));
            totalBargeOnlyGotDetailsMeetingsChart.update();

            totalPotentialMeetingsChart.data.labels = labels;
            totalPotentialMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_potential_meetings));
            totalPotentialMeetingsChart.update();

            totalNonPotentialMeetingsChart.data.labels = labels;
            totalNonPotentialMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_non_potential_meetings));
            totalNonPotentialMeetingsChart.update();

            totalTopspenderMeetingsChart.data.labels = labels;
            totalTopspenderMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_topspender_meetings));
            totalTopspenderMeetingsChart.update();

            totalUpsellClientMeetingsChart.data.labels = labels;
            totalUpsellClientMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_upsell_client_meetings));
            totalUpsellClientMeetingsChart.update();

            totalFocusFunnelMeetingsChart.data.labels = labels;
            totalFocusFunnelMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_focus_funnel_meetings));
            totalFocusFunnelMeetingsChart.update();

            totalKeycompanyMeetingsChart.data.labels = labels;
            totalKeycompanyMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_keycompany_meetings));
            totalKeycompanyMeetingsChart.update();

            totalPkclientMeetingsChart.data.labels = labels;
            totalPkclientMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_pkclient_meetings));
            totalPkclientMeetingsChart.update();

            totalPrioritycMeetingsChart.data.labels = labels;
            totalPrioritycMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_priorityc_meetings));
            totalPrioritycMeetingsChart.update();

            totalFreshMeetingsChart.data.labels = labels;
            totalFreshMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_fresh_meetings));
            totalFreshMeetingsChart.update();

            totalReMeetingsChart.data.labels = labels;
            totalReMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_re_meetings));
            totalReMeetingsChart.update();

            totalWriteMomRPMeetingsChart.data.labels = labels;
            totalWriteMomRPMeetingsChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_write_mom_rp_meetings));
            totalWriteMomRPMeetingsChart.update();

            totalPendingForWriteMomRPMeetingChart.data.labels = labels;
            totalPendingForWriteMomRPMeetingChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_pending_for_write_mom_rp_meeting));
            totalPendingForWriteMomRPMeetingChart.update();

            totalMomDataChart.data.labels = labels;
            totalMomDataChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_mom_data));
            totalMomDataChart.update();

            totalApprovedAfterCheckMomDataChart.data.labels = labels;
            totalApprovedAfterCheckMomDataChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_approved_after_check_mom_data));
            totalApprovedAfterCheckMomDataChart.update();

            totalRejectAfterCheckMomDataChart.data.labels = labels;
            totalRejectAfterCheckMomDataChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_reject_after_check_mom_data));
            totalRejectAfterCheckMomDataChart.update();

            totalNO_RPAfterCheckMomDataChart.data.labels = labels;
            totalNO_RPAfterCheckMomDataChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_NO_RP_after_check_mom_data));
            totalNO_RPAfterCheckMomDataChart.update();

            totalPendingForCheckMomDataChart.data.labels = labels;
            totalPendingForCheckMomDataChart.data.datasets[0].data = filteredData.map(user => parseInt(user.total_pending_for_check_mom_data));
            totalPendingForCheckMomDataChart.update();

            // Update the table
            updateTable(filteredData);
        }

        // Function to populate filter dropdowns dynamically
        function populateFilters() {
            // Extract unique values for each filter category
            const userZones = [...new Set(data.map(user => user.user_zone))];
            const userRoles = [...new Set(data.map(user => user.user_roles))];
            const names = [...new Set(data.map(user => user.name))];

            // Populate User Zone filter
            const userZoneFilter = document.getElementById('userZoneFilter');
            userZones.forEach(zone => {
                const option = document.createElement('option');
                option.value = zone;
                option.textContent = zone;
                userZoneFilter.appendChild(option);
            });

            // Populate User Roles filter
            const userRolesFilter = document.getElementById('userRolesFilter');
            userRoles.forEach(role => {
                const option = document.createElement('option');
                option.value = role;
                option.textContent = role;
                userRolesFilter.appendChild(option);
            });

            // Populate Name filter
            const nameFilter = document.getElementById('nameFilter');
            names.forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                nameFilter.appendChild(option);
            });
        }

        // Function to update filter options based on selected filter
        function updateFilterOptions() {
            const userZoneFilter = document.getElementById('userZoneFilter').value;
            const userRolesFilter = document.getElementById('userRolesFilter').value;
            const nameFilter = document.getElementById('nameFilter').value;

            let filteredData = data;

            if (userZoneFilter) {
                filteredData = filteredData.filter(user => user.user_zone === userZoneFilter);
            }

            if (userRolesFilter) {
                filteredData = filteredData.filter(user => user.user_roles === userRolesFilter);
            }

            if (nameFilter) {
                filteredData = filteredData.filter(user => user.name === nameFilter);
            }

            // Update other filters based on filtered data
            updateUserRolesFilter(filteredData);
            updateNameFilter(filteredData);
        }

        // Function to update User Roles filter options
        function updateUserRolesFilter(filteredData) {
            const userRolesFilter = document.getElementById('userRolesFilter');
            const currentValue = userRolesFilter.value;
            userRolesFilter.innerHTML = '<option value="">All</option>';

            const userRoles = [...new Set(filteredData.map(user => user.user_roles))];
            userRoles.forEach(role => {
                const option = document.createElement('option');
                option.value = role;
                option.textContent = role;
                userRolesFilter.appendChild(option);
            });

            if (userRoles.includes(currentValue)) {
                userRolesFilter.value = currentValue;
            }
        }

        // Function to update Name filter options
        function updateNameFilter(filteredData) {
            const nameFilter = document.getElementById('nameFilter');
            const currentValue = nameFilter.value;
            nameFilter.innerHTML = '<option value="">All</option>';

            const names = [...new Set(filteredData.map(user => user.name))];
            names.forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                nameFilter.appendChild(option);
            });

            if (names.includes(currentValue)) {
                nameFilter.value = currentValue;
            }
        }

        // Add event listeners to filter dropdowns
        document.getElementById('userZoneFilter').addEventListener('change', function() {
            updateFilterOptions();
            updateCharts();
        });

        document.getElementById('userRolesFilter').addEventListener('change', function() {
            updateFilterOptions();
            updateCharts();
        });

        document.getElementById('nameFilter').addEventListener('change', function() {
            updateFilterOptions();
            updateCharts();
        });

        // Create charts
        const labels = data.map(user => user.name);

        const totalPlanMeetingsChart = createBarChart(document.getElementById('totalPlanMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_plan_meetings)), 'Total Plan Meetings');
        const completeMeetingsChart = createBarChart(document.getElementById('completeMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.complete_meetings)), 'Complete Meetings');

        // New pie charts
        const totalPlanVsCompleteMeetingsChart = createPieChart(document.getElementById('totalPlanVsCompleteMeetingsChart').getContext('2d'),
            ['Total Plan Meetings', 'Complete Meetings'],
            [data.reduce((sum, user) => sum + parseInt(user.total_plan_meetings), 0), data.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0)],
            'Total Plan Meetings VS Complete Meetings');

        const completeVsTotalRPMeetingsChart = createPieChart(document.getElementById('completeVsTotalRPMeetingsChart').getContext('2d'),
            ['Complete Meetings', 'Total RP Meetings'],
            [data.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0), data.reduce((sum, user) => sum + parseInt(user.total_RP_meetings), 0)],
            'Complete Meetings VS Total RP Meetings');

        const completeVsTotalNO_RPMeetingsChart = createPieChart(document.getElementById('completeVsTotalNO_RPMeetingsChart').getContext('2d'),
            ['Complete Meetings', 'Total NO RP Meetings'],
            [data.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0), data.reduce((sum, user) => sum + parseInt(user.total_NO_RP_meetings), 0)],
            'Complete Meetings VS Total NO RP Meetings');

        const completeVsTotalOnlyGotDetailsMeetingsChart = createPieChart(document.getElementById('completeVsTotalOnlyGotDetailsMeetingsChart').getContext('2d'),
            ['Complete Meetings', 'Total Only Got Details Meetings'],
            [data.reduce((sum, user) => sum + parseInt(user.complete_meetings), 0), data.reduce((sum, user) => sum + parseInt(user.total_Only_Got_details_meetings), 0)],
            'Complete Meetings VS Total Only Got Details Meetings');

        // Rest of the existing chart creation code
        const notCompleteMeetingsChart = createBarChart(document.getElementById('notCompleteMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.not_complete_meetings)), 'Not Complete Meetings');
        const totalScheduledMeetingsChart = createBarChart(document.getElementById('totalScheduledMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_sheduled_meetings)), 'Total Scheduled Meetings');
        const totalCompleteScheduledMeetingsChart = createBarChart(document.getElementById('totalCompleteScheduledMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_complete_sheduled_meetings)), 'Total Complete Scheduled Meetings');
        const notCompleteScheduledMeetingsChart = createBarChart(document.getElementById('notCompleteScheduledMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.not_complete_sheduled_meetings)), 'Not Complete Scheduled Meetings');
        const totalBargMeetingsChart = createBarChart(document.getElementById('totalBargMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_barg_meetings)), 'Total Barg Meetings');
        const totalCompleteBargMeetingsChart = createBarChart(document.getElementById('totalCompleteBargMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_complete_barg_meetings)), 'Total Complete Barg Meetings');
        const notCompleteBargMeetingsChart = createBarChart(document.getElementById('notCompleteBargMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.not_complete_barg_meetings)), 'Not Complete Barg Meetings');
        const totalJoinMeetingsChart = createBarChart(document.getElementById('totalJoinMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_join_meetings)), 'Total Join Meetings');
        const totalCompleteJoinMeetingsChart = createBarChart(document.getElementById('totalCompleteJoinMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_complete_join_meetings)), 'Total Complete Join Meetings');
        const notCompleteJoinMeetingsChart = createBarChart(document.getElementById('notCompleteJoinMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.not_complete_join_meetings)), 'Not Complete Join Meetings');
        const totalRPMeetingsChart = createBarChart(document.getElementById('totalRPMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_RP_meetings)), 'Total RP Meetings');
        const totalNO_RPMeetingsChart = createBarChart(document.getElementById('totalNO_RPMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_NO_RP_meetings)), 'Total NO RP Meetings');
        const totalOnlyGotDetailsMeetingsChart = createBarChart(document.getElementById('totalOnlyGotDetailsMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_Only_Got_details_meetings)), 'Total Only Got Details Meetings');
        const totalScheduledRPMeetingsChart = createBarChart(document.getElementById('totalScheduledRPMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_Sheduled_RP_meetings)), 'Total Scheduled RP Meetings');
        const totalScheduledNO_RPMeetingsChart = createBarChart(document.getElementById('totalScheduledNO_RPMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_Sheduled_NO_RP_meetings)), 'Total Scheduled NO RP Meetings');
        const totalScheduledOnlyGotDetailsMeetingsChart = createBarChart(document.getElementById('totalScheduledOnlyGotDetailsMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_Sheduled_Only_Got_details_meetings)), 'Total Scheduled Only Got Details Meetings');
        const totalBargeRPMeetingsChart = createBarChart(document.getElementById('totalBargeRPMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_Barge_RP_meetings)), 'Total Barge RP Meetings');
        const totalBargeNO_RPMeetingsChart = createBarChart(document.getElementById('totalBargeNO_RPMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_Barge_NO_RP_meetings)), 'Total Barge NO RP Meetings');
        const totalBargeOnlyGotDetailsMeetingsChart = createBarChart(document.getElementById('totalBargeOnlyGotDetailsMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_Barge_Only_Got_details_meetings)), 'Total Barge Only Got Details Meetings');
        const totalPotentialMeetingsChart = createBarChart(document.getElementById('totalPotentialMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_potential_meetings)), 'Total Potential Meetings');
        const totalNonPotentialMeetingsChart = createBarChart(document.getElementById('totalNonPotentialMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_non_potential_meetings)), 'Total Non Potential Meetings');
        const totalTopspenderMeetingsChart = createBarChart(document.getElementById('totalTopspenderMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_topspender_meetings)), 'Total Topspender Meetings');
        const totalUpsellClientMeetingsChart = createBarChart(document.getElementById('totalUpsellClientMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_upsell_client_meetings)), 'Total Upsell Client Meetings');
        const totalFocusFunnelMeetingsChart = createBarChart(document.getElementById('totalFocusFunnelMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_focus_funnel_meetings)), 'Total Focus Funnel Meetings');
        const totalKeycompanyMeetingsChart = createBarChart(document.getElementById('totalKeycompanyMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_keycompany_meetings)), 'Total Keycompany Meetings');
        const totalPkclientMeetingsChart = createBarChart(document.getElementById('totalPkclientMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_pkclient_meetings)), 'Total Pkclient Meetings');
        const totalPrioritycMeetingsChart = createBarChart(document.getElementById('totalPrioritycMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_priorityc_meetings)), 'Total Priorityc Meetings');
        const totalFreshMeetingsChart = createBarChart(document.getElementById('totalFreshMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_fresh_meetings)), 'Total Fresh Meetings');
        const totalReMeetingsChart = createBarChart(document.getElementById('totalReMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_re_meetings)), 'Total Re Meetings');
        const totalWriteMomRPMeetingsChart = createBarChart(document.getElementById('totalWriteMomRPMeetingsChart').getContext('2d'), labels, data.map(user => parseInt(user.total_write_mom_rp_meetings)), 'Total Write Mom RP Meetings');
        const totalPendingForWriteMomRPMeetingChart = createBarChart(document.getElementById('totalPendingForWriteMomRPMeetingChart').getContext('2d'), labels, data.map(user => parseInt(user.total_pending_for_write_mom_rp_meeting)), 'Total Pending For Write Mom RP Meeting');
        const totalMomDataChart = createBarChart(document.getElementById('totalMomDataChart').getContext('2d'), labels, data.map(user => parseInt(user.total_mom_data)), 'Total Mom Data');
        const totalApprovedAfterCheckMomDataChart = createBarChart(document.getElementById('totalApprovedAfterCheckMomDataChart').getContext('2d'), labels, data.map(user => parseInt(user.total_approved_after_check_mom_data)), 'Total Approved After Check Mom Data');
        const totalRejectAfterCheckMomDataChart = createBarChart(document.getElementById('totalRejectAfterCheckMomDataChart').getContext('2d'), labels, data.map(user => parseInt(user.total_reject_after_check_mom_data)), 'Total Reject After Check Mom Data');
        const totalNO_RPAfterCheckMomDataChart = createBarChart(document.getElementById('totalNO_RPAfterCheckMomDataChart').getContext('2d'), labels, data.map(user => parseInt(user.total_NO_RP_after_check_mom_data)), 'Total NO RP After Check Mom Data');
        const totalPendingForCheckMomDataChart = createBarChart(document.getElementById('totalPendingForCheckMomDataChart').getContext('2d'), labels, data.map(user => parseInt(user.total_pending_for_check_mom_data)), 'Total Pending For Check Mom Data');

        // Populate filter dropdowns
        populateFilters();

        $('#downloadPdf').click(function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    html2canvas(document.body, {
        scale: 2, // Increase the scale for better quality
        useCORS: true, // Enable CORS if you have external images
        logging: false // Disable logging for a cleaner console
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 295; // A4 height in mm
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;

        // Add the first page
        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add additional pages if needed
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        doc.save('MeetingsReportAnalysis.pdf');
    }).catch(error => {
        console.error('Error generating PDF:', error);
    });
});

        // Initialize DataTables
        $(document).ready(function() {
            $('#meetingDataTable').DataTable({
                data: data,
                columns: [
                    { data: 'name' },
                    { data: 'user_roles' },
                    { data: 'user_zone' },
                    { data: 'total_plan_meetings' },
                    { data: 'complete_meetings' },
                    { data: 'not_complete_meetings' },
                    { data: 'total_sheduled_meetings' },
                    { data: 'total_complete_sheduled_meetings' },
                    { data: 'not_complete_sheduled_meetings' },
                    { data: 'total_barg_meetings' },
                    { data: 'total_complete_barg_meetings' },
                    { data: 'not_complete_barg_meetings' },
                    { data: 'total_join_meetings' },
                    { data: 'total_complete_join_meetings' },
                    { data: 'not_complete_join_meetings' },
                    { data: 'total_RP_meetings' },
                    { data: 'total_NO_RP_meetings' },
                    { data: 'total_Only_Got_details_meetings' },
                    { data: 'total_Sheduled_RP_meetings' },
                    { data: 'total_Sheduled_NO_RP_meetings' },
                    { data: 'total_Sheduled_Only_Got_details_meetings' },
                    { data: 'total_Barge_RP_meetings' },
                    { data: 'total_Barge_NO_RP_meetings' },
                    { data: 'total_Barge_Only_Got_details_meetings' },
                    { data: 'total_potential_meetings' },
                    { data: 'total_non_potential_meetings' },
                    { data: 'total_topspender_meetings' },
                    { data: 'total_upsell_client_meetings' },
                    { data: 'total_focus_funnel_meetings' },
                    { data: 'total_keycompany_meetings' },
                    { data: 'total_pkclient_meetings' },
                    { data: 'total_priorityc_meetings' },
                    { data: 'total_fresh_meetings' },
                    { data: 'total_re_meetings' },
                    { data: 'total_write_mom_rp_meetings' },
                    { data: 'total_pending_for_write_mom_rp_meeting' },
                    { data: 'total_mom_data' },
                    { data: 'total_approved_after_check_mom_data' },
                    { data: 'total_reject_after_check_mom_data' },
                    { data: 'total_NO_RP_after_check_mom_data' },
                    { data: 'total_pending_for_check_mom_data' }
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
                ]
            });
        });

         // Download All Charts
    $('#downloadCharts').click(function() {
        const chartIds = [ 'totalPlanMeetingsChart', 'completeMeetingsChart', 'notCompleteMeetingsChart', 'totalRPMeetingsChart', 'totalNO_RPMeetingsChart', 'totalOnlyGotDetailsMeetingsChart', 'totalScheduledMeetingsChart', 'totalCompleteScheduledMeetingsChart', 'notCompleteScheduledMeetingsChart', 'totalScheduledRPMeetingsChart', 'totalScheduledNO_RPMeetingsChart', 'totalScheduledOnlyGotDetailsMeetingsChart', 'totalBargMeetingsChart', 'totalCompleteBargMeetingsChart', 'notCompleteBargMeetingsChart', 'totalBargeRPMeetingsChart', 'totalBargeNO_RPMeetingsChart', 'totalBargeOnlyGotDetailsMeetingsChart', 'totalJoinMeetingsChart', 'totalCompleteJoinMeetingsChart', 'notCompleteJoinMeetingsChart', 'totalPotentialMeetingsChart', 'totalNonPotentialMeetingsChart', 'totalTopspenderMeetingsChart', 'totalUpsellClientMeetingsChart', 'totalFocusFunnelMeetingsChart', 'totalKeycompanyMeetingsChart', 'totalPkclientMeetingsChart', 'totalPrioritycMeetingsChart', 'totalFreshMeetingsChart', 'totalReMeetingsChart', 'totalWriteMomRPMeetingsChart', 'totalPendingForWriteMomRPMeetingChart', 'totalMomDataChart', 'totalApprovedAfterCheckMomDataChart', 'totalRejectAfterCheckMomDataChart', 'totalNO_RPAfterCheckMomDataChart', 'totalPendingForCheckMomDataChart', 'totalPlanVsCompleteMeetingsChart', 'completeVsTotalRPMeetingsChart', 'completeVsTotalNO_RPMeetingsChart', 'completeVsTotalOnlyGotDetailsMeetingsChart'];
        chartIds.forEach(chartId => {
            const chartCanvas = document.getElementById(chartId);
            const chartImage = chartCanvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = `${chartId}.png`;
            link.href = chartImage;
            link.click();
        });
    });

    </script>
</body>
</html>
