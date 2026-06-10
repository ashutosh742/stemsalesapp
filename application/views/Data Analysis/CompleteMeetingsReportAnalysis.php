<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Data Visualization</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Chart.js plugin for displaying labels on pie charts -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <style>
        #map { height: 130vh; }
        .popup-image { max-width: 200px; max-height: 200px; }
        .leaflet-popup-content-wrapper {
            border-radius: 10px;
            border: 2px solid #3498db;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .leaflet-popup-content {
            margin: 10px;
        }
        .leaflet-popup-content img {
            display: block;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Blinking animation for markers */
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .blinking-marker {
            animation: blink 1.5s infinite;
        }

        .chart-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            text-align: center;
            margin-bottom: 10px;
            color: #343a40;
        }

        .chart-description {
            text-align: center;
            margin-bottom: 20px;
            color: #6c757d;
            font-style: italic;
        }

        .filter-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2,h4,.chart-title{
                color: #8e0083;
        }
    </style>
</head>
<body>
    <?php include ("nav.php"); ?>
    <div class="container-fluid mt-5">
      <hr>
        <h2 class="text-center mb-4">Meeting Data Visualization</h2>
      <hr>
      <h5 class="text-center">(<?=$sdate?> To <?=$edate?>)</h5>
<hr>

    <br>
    <?php
    $cttype = $user['type_id'];
    if($cttype == 2 || $cttype == 19 || $cttype == 20 || $cttype == 21 || $cttype == 22 || $cttype == 23){
       ?>
  <?php $clusterPstDatas = $this->Menu_model->GetAdminActiveTeam($user['user_id']); ?>
  <form action="<?=base_url()?>SalesGraph/CompleteMeetingsReportAnalysis" method="post">
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
         <form action="<?=base_url()?>SalesGraph/CompleteMeetingsReportAnalysis" method="post">
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

        <div class="row">
            <div class="col-md-12">
                <div class="filter-container">
                    <h4 class="text-center mb-4">Filters</h4>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="userZoneFilter">User Zone</label>
                                <select class="form-control" id="userZoneFilter">
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="userRolesFilter">User Roles</label>
                                <select class="form-control" id="userRolesFilter">
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="taskUsernameFilter">Task Username</label>
                                <select class="form-control" id="taskUsernameFilter">
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="taskNameFilter">Task Name</label>
                                <select class="form-control" id="taskNameFilter">
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="mtypeFilter">Meeting Type</label>
                                <select class="form-control" id="mtypeFilter">
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="compnameFilter">Company Name</label>
                                <select class="form-control" id="compnameFilter">
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
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


        <div class="row">
            <div class="col-md-12">
                <div class="chart-container">
                    <h4 class="chart-title">Meetings Per User</h4>
                    <p class="chart-description">This bar chart shows the number of meetings conducted by each user.</p>
                    <canvas id="meetingsPerUserChart" max-height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="chart-title">Meetings By Zone</h4>
                    <p class="chart-description">This pie chart shows the distribution of meetings across different zones.</p>
                    <canvas id="meetingsByZoneChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="chart-title">Meetings By Type</h4>
                    <p class="chart-description">This pie chart shows the distribution of meetings by type.</p>
                    <canvas id="meetingsByTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="chart-container">
                    <h4 class="chart-title">Meetings By User On RP</h4>
                    <p class="chart-description">This pie chart shows the distribution of meetings by user with RP.</p>
                    <canvas id="meetingsByUserOnRPChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h4 class="chart-title">Meetings By User On NO RP</h4>
                    <p class="chart-description">This pie chart shows the distribution of meetings by user with NO RP.</p>
                    <canvas id="meetingsByUserOnNoRPChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h4 class="chart-title">Meetings By User On Only Got Detail</h4>
                    <p class="chart-description">This pie chart shows the distribution of meetings by user with Only Got Detail.</p>
                    <canvas id="meetingsByUserOnOnlyGotDetailChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="chart-title text-capitalize">Meetings By User with task name</h4>
                    <p class="chart-description text-capitalize">This pie chart shows the distribution of meetings by user with task name.</p>
                    <canvas id="meetingsByUserWithTaskNameChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="chart-title text-capitalize">Meetings By User with task name with RP</h4>
                    <p class="chart-description text-capitalize">This pie chart shows the distribution of meetings by user with task name with RP.</p>
                    <canvas id="meetingsByUserWithTaskNameWithRPChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="chart-title text-capitalize">Meetings By User with task name with NO RP</h4>
                    <p class="chart-description text-capitalize">This pie chart shows the distribution of meetings by user with task name with NO RP.</p>
                    <canvas id="meetingsByUserWithTaskNameWithNoRPChart" height="300"></canvas>
                </div>
            </div>
             <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="chart-title text-capitalize">Meetings By User with task name with Only Got Detail</h4>
                    <p class="chart-description text-capitalize">This pie chart shows the distribution of meetings by user with task name with Only Got Detail.</p>
                    <canvas id="meetingsByUserWithTaskNameWithOnlyGotDetailChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="chart-container">
                    <h4 class="chart-title">Meeting Locations</h4>
                    <p class="chart-description">This map shows the locations of the meetings.</p>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
<?php include ("footer.php"); ?>
    <script>
        // Sample data in JSON format
      var meetingsData =  <?php echo json_encode($taskDatadatas, true); ?>;

        // Define the types variable and populate it with data from meetingsData
        var types = {};
        meetingsData.forEach(function(meeting) {
            if (types[meeting.mtype]) {
                types[meeting.mtype]++;
            } else {
                types[meeting.mtype] = 1;
            }
        });

        // Function to populate filter options
        function populateFilterOptions() {
            const userZoneFilter = document.getElementById('userZoneFilter');
            const userRolesFilter = document.getElementById('userRolesFilter');
            const taskUsernameFilter = document.getElementById('taskUsernameFilter');
            const taskNameFilter = document.getElementById('taskNameFilter');
            const mtypeFilter = document.getElementById('mtypeFilter');
            const compnameFilter = document.getElementById('compnameFilter');

            const userZones = [...new Set(meetingsData.map(meeting => meeting.user_zone))];
            const userRoles = [...new Set(meetingsData.map(meeting => meeting.user_roles))];
            const taskUsernames = [...new Set(meetingsData.map(meeting => meeting.task_username))];
            const taskNames = [...new Set(meetingsData.map(meeting => meeting.task_name))];
            const mtypes = [...new Set(meetingsData.map(meeting => meeting.mtype))];
            const compnames = [...new Set(meetingsData.map(meeting => meeting.compname))];

            userZones.forEach(zone => {
                const option = document.createElement('option');
                option.value = zone;
                option.textContent = zone;
                userZoneFilter.appendChild(option);
            });

            userRoles.forEach(role => {
                const option = document.createElement('option');
                option.value = role;
                option.textContent = role;
                userRolesFilter.appendChild(option);
            });

            taskUsernames.forEach(username => {
                const option = document.createElement('option');
                option.value = username;
                option.textContent = username;
                taskUsernameFilter.appendChild(option);
            });

            taskNames.forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                taskNameFilter.appendChild(option);
            });

            mtypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                mtypeFilter.appendChild(option);
            });

            compnames.forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                compnameFilter.appendChild(option);
            });
        }

        // Function to filter data based on selected filters
        function filterData() {
            const userZoneFilter = document.getElementById('userZoneFilter').value;
            const userRolesFilter = document.getElementById('userRolesFilter').value;
            const taskUsernameFilter = document.getElementById('taskUsernameFilter').value;
            const taskNameFilter = document.getElementById('taskNameFilter').value;
            const mtypeFilter = document.getElementById('mtypeFilter').value;
            const compnameFilter = document.getElementById('compnameFilter').value;

            return meetingsData.filter(meeting => {
                return (userZoneFilter === '' || meeting.user_zone === userZoneFilter) &&
                       (userRolesFilter === '' || meeting.user_roles === userRolesFilter) &&
                       (taskUsernameFilter === '' || meeting.task_username === taskUsernameFilter) &&
                       (taskNameFilter === '' || meeting.task_name === taskNameFilter) &&
                       (mtypeFilter === '' || meeting.mtype === mtypeFilter) &&
                       (compnameFilter === '' || meeting.compname === compnameFilter);
            });
        }

        // Function to update charts and map with filtered data
        function updateVisualizations(filteredData) {
            // Process data for charts
            var users = {};
            var zones = {};
            var types = {};
            var usersOnRP = {};
            var usersOnNoRP = {};
            var usersOnOnlyGotDetail = {};
            var usersWithTaskName = {};
            var usersWithTaskNameWithRP = {};
            var usersWithTaskNameWithNoRP = {};
            var usersWithTaskNameWithOnlyGotDetail = {};

            filteredData.forEach(function(meeting) {
                // Count meetings per user
                if (users[meeting.task_username]) {
                    users[meeting.task_username]++;
                } else {
                    users[meeting.task_username] = 1;
                }

                // Count meetings per zone
                if (zones[meeting.user_zone]) {
                    zones[meeting.user_zone]++;
                } else {
                    zones[meeting.user_zone] = 1;
                }

                // Count meetings by type
                if (types[meeting.mtype]) {
                    types[meeting.mtype]++;
                } else {
                    types[meeting.mtype] = 1;
                }

                // Count meetings by user on RP
                if (meeting.mtype === "RP") {
                    if (usersOnRP[meeting.task_username]) {
                        usersOnRP[meeting.task_username]++;
                    } else {
                        usersOnRP[meeting.task_username] = 1;
                    }
                }

                // Count meetings by user on NO RP
                if (meeting.mtype === "NO RP") {
                    if (usersOnNoRP[meeting.task_username]) {
                        usersOnNoRP[meeting.task_username]++;
                    } else {
                        usersOnNoRP[meeting.task_username] = 1;
                    }
                }

                // Count meetings by user on Only Got Detail
                if (meeting.mtype === "Only Got Detail") {
                    if (usersOnOnlyGotDetail[meeting.task_username]) {
                        usersOnOnlyGotDetail[meeting.task_username]++;
                    } else {
                        usersOnOnlyGotDetail[meeting.task_username] = 1;
                    }
                }

                // Count meetings by user with task_name
                if (usersWithTaskName[meeting.task_username]) {
                    if (usersWithTaskName[meeting.task_username][meeting.task_name]) {
                        usersWithTaskName[meeting.task_username][meeting.task_name]++;
                    } else {
                        usersWithTaskName[meeting.task_username][meeting.task_name] = 1;
                    }
                } else {
                    usersWithTaskName[meeting.task_username] = {};
                    usersWithTaskName[meeting.task_username][meeting.task_name] = 1;
                }

                // Count meetings by user with task_name with RP
                if (meeting.mtype === "RP") {
                    if (usersWithTaskNameWithRP[meeting.task_username]) {
                        if (usersWithTaskNameWithRP[meeting.task_username][meeting.task_name]) {
                            usersWithTaskNameWithRP[meeting.task_username][meeting.task_name]++;
                        } else {
                            usersWithTaskNameWithRP[meeting.task_username][meeting.task_name] = 1;
                        }
                    } else {
                        usersWithTaskNameWithRP[meeting.task_username] = {};
                        usersWithTaskNameWithRP[meeting.task_username][meeting.task_name] = 1;
                    }
                }

                // Count meetings by user with task_name with NO RP
                if (meeting.mtype === "NO RP") {
                    if (usersWithTaskNameWithNoRP[meeting.task_username]) {
                        if (usersWithTaskNameWithNoRP[meeting.task_username][meeting.task_name]) {
                            usersWithTaskNameWithNoRP[meeting.task_username][meeting.task_name]++;
                        } else {
                            usersWithTaskNameWithNoRP[meeting.task_username][meeting.task_name] = 1;
                        }
                    } else {
                        usersWithTaskNameWithNoRP[meeting.task_username] = {};
                        usersWithTaskNameWithNoRP[meeting.task_username][meeting.task_name] = 1;
                    }
                }

                // Count meetings by user with task_name with Only Got Detail
                if (meeting.mtype === "Only Got Detail") {
                    if (usersWithTaskNameWithOnlyGotDetail[meeting.task_username]) {
                        if (usersWithTaskNameWithOnlyGotDetail[meeting.task_username][meeting.task_name]) {
                            usersWithTaskNameWithOnlyGotDetail[meeting.task_username][meeting.task_name]++;
                        } else {
                            usersWithTaskNameWithOnlyGotDetail[meeting.task_username][meeting.task_name] = 1;
                        }
                    } else {
                        usersWithTaskNameWithOnlyGotDetail[meeting.task_username] = {};
                        usersWithTaskNameWithOnlyGotDetail[meeting.task_username][meeting.task_name] = 1;
                    }
                }
            });

            // Update charts
            updateChart(meetingsPerUserChart, Object.keys(users), Object.values(users), 'bar');
            updateChart(meetingsByZoneChart, Object.keys(zones), Object.values(zones), 'pie');
            updateChart(meetingsByTypeChart, Object.keys(types), Object.values(types), 'pie');
            updateChart(meetingsByUserOnRPChart, Object.keys(usersOnRP), Object.values(usersOnRP), 'pie');
            updateChart(meetingsByUserOnNoRPChart, Object.keys(usersOnNoRP), Object.values(usersOnNoRP), 'pie');
            updateChart(meetingsByUserOnOnlyGotDetailChart, Object.keys(usersOnOnlyGotDetail), Object.values(usersOnOnlyGotDetail), 'pie');
            updateChart(meetingsByUserWithTaskNameChart, Object.keys(usersWithTaskName).flatMap(user => Object.keys(usersWithTaskName[user]).map(taskName => `${user} - ${taskName}`)), Object.values(usersWithTaskName).flatMap(user => Object.values(user)), 'pie');
            updateChart(meetingsByUserWithTaskNameWithRPChart, Object.keys(usersWithTaskNameWithRP).flatMap(user => Object.keys(usersWithTaskNameWithRP[user]).map(taskName => `${user} - ${taskName}`)), Object.values(usersWithTaskNameWithRP).flatMap(user => Object.values(user)), 'pie');
            updateChart(meetingsByUserWithTaskNameWithNoRPChart, Object.keys(usersWithTaskNameWithNoRP).flatMap(user => Object.keys(usersWithTaskNameWithNoRP[user]).map(taskName => `${user} - ${taskName}`)), Object.values(usersWithTaskNameWithNoRP).flatMap(user => Object.values(user)), 'pie');
            updateChart(meetingsByUserWithTaskNameWithOnlyGotDetailChart, Object.keys(usersWithTaskNameWithOnlyGotDetail).flatMap(user => Object.keys(usersWithTaskNameWithOnlyGotDetail[user]).map(taskName => `${user} - ${taskName}`)), Object.values(usersWithTaskNameWithOnlyGotDetail).flatMap(user => Object.values(user)), 'pie');

            // Update map
            updateMap(filteredData);
        }

        // Function to update a chart
        function updateChart(chart, labels, data, type) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.type = type;
            chart.update();
        }

        // Function to update the map
        function updateMap(filteredData) {
            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });

            filteredData.forEach(function(meeting) {
                if (meeting.slatitude && meeting.slongitude) {
                    var marker = L.marker([meeting.slatitude, meeting.slongitude], {
                        icon: L.divIcon({
                            className: 'blinking-marker',
                            html: '📍',
                            iconSize: [12, 12] // Adjust the size of the marker here
                        })
                    }).addTo(map);
                    marker.bindPopup(`
                        <div style="text-align: center;">
                           <b>${meeting.compname}</b><br>
                            BD Name: ${meeting.task_username}<br>
                            Zone: ${meeting.user_zone}<br>
                            Start: ${meeting.startm}<br>
                            Close: ${meeting.closem}<br>
                            Task Name: ${meeting.task_name}<br>
                            Current Status: ${meeting.current_status}<br>
                            Type: ${meeting.mtype}<br>
                            <img src="https://stemapp.in/${meeting.cphoto}" class="popup-image" alt="Closed Photo">
                        </div>
                    `);
                }
                if (meeting.clatitude && meeting.clongitude) {
                    var marker = L.marker([meeting.clatitude, meeting.clongitude], {
                        icon: L.divIcon({
                            className: 'blinking-marker',
                            html: '📍',
                            iconSize: [12, 12] // Adjust the size of the marker here
                        })
                    }).addTo(map);
                    marker.bindPopup(`
                        <div style="text-align: center;">
                            <b>${meeting.compname}</b><br>
                            BD Name: ${meeting.task_username}<br>
                            Zone: ${meeting.user_zone}<br>
                            Start: ${meeting.startm}<br>
                            Close: ${meeting.closem}<br>
                            Task Name: ${meeting.task_name}<br>
                            Current Status: ${meeting.current_status}<br>
                            Type: ${meeting.mtype}<br>
                            <img src="https://stemapp.in/${meeting.cphoto}" class="popup-image" alt="Closed Photo">
                        </div>
                    `);
                }
            });
        }

        // Initialize the map
        var map = L.map('map').setView([20.5937, 78.9629], 5); // Centered on India

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Initialize charts
        var ctx1 = document.getElementById('meetingsPerUserChart').getContext('2d');
        var meetingsPerUserChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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

        var ctx2 = document.getElementById('meetingsByZoneChart').getContext('2d');
        var meetingsByZoneChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });

        var ctx4 = document.getElementById('meetingsByTypeChart').getContext('2d');
        var totalMeetings = Object.values(types).reduce((a, b) => a + b, 0);
        var meetingsByTypeChart = new Chart(ctx4, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Meeting By User On RP
        var ctx5 = document.getElementById('meetingsByUserOnRPChart').getContext('2d');
        var meetingsByUserOnRPChart = new Chart(ctx5, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
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
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Meeting BY User On NO RP
        var ctx6 = document.getElementById('meetingsByUserOnNoRPChart').getContext('2d');
        var meetingsByUserOnNoRPChart = new Chart(ctx6, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
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
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Meeting BY User On Only Got Detail
        var ctx7 = document.getElementById('meetingsByUserOnOnlyGotDetailChart').getContext('2d');
        var meetingsByUserOnOnlyGotDetailChart = new Chart(ctx7, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
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
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Meeting By User with task_name
        var ctx8 = document.getElementById('meetingsByUserWithTaskNameChart').getContext('2d');
        var meetingsByUserWithTaskNameChart = new Chart(ctx8, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
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
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Meeting By User with task_name with RP
        var ctx9 = document.getElementById('meetingsByUserWithTaskNameWithRPChart').getContext('2d');
        var meetingsByUserWithTaskNameWithRPChart = new Chart(ctx9, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
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
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Meeting By User with task_name with NO RP
        var ctx10 = document.getElementById('meetingsByUserWithTaskNameWithNoRPChart').getContext('2d');
        var meetingsByUserWithTaskNameWithNoRPChart = new Chart(ctx10, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
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
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Meeting By User with task_name with Only Got Detail
        var ctx11 = document.getElementById('meetingsByUserWithTaskNameWithOnlyGotDetailChart').getContext('2d');
        var meetingsByUserWithTaskNameWithOnlyGotDetailChart = new Chart(ctx11, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Meetings',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
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
                plugins: {
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Populate filter options
        populateFilterOptions();

        // Add event listeners to filter select elements
        document.getElementById('userZoneFilter').addEventListener('change', function() {
            updateVisualizations(filterData());
        });

        document.getElementById('userRolesFilter').addEventListener('change', function() {
            updateVisualizations(filterData());
        });

        document.getElementById('taskUsernameFilter').addEventListener('change', function() {
            updateVisualizations(filterData());
        });

        document.getElementById('taskNameFilter').addEventListener('change', function() {
            updateVisualizations(filterData());
        });

        document.getElementById('mtypeFilter').addEventListener('change', function() {
            updateVisualizations(filterData());
        });

        document.getElementById('compnameFilter').addEventListener('change', function() {
            updateVisualizations(filterData());
        });

        // Initial update of visualizations
        updateVisualizations(meetingsData);




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

        // Download All Charts
    $('#downloadCharts').click(function() {
        const chartIds = ['meetingsPerUserChart', 'meetingsByZoneChart', 'meetingsByTypeChart', 'meetingsByUserOnRPChart', 'meetingsByUserOnNoRPChart', 'meetingsByUserOnOnlyGotDetailChart', 'meetingsByUserWithTaskNameChart', 'meetingsByUserWithTaskNameWithRPChart', 'meetingsByUserWithTaskNameWithNoRPChart', 'meetingsByUserWithTaskNameWithOnlyGotDetailChart', 'map'];
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
