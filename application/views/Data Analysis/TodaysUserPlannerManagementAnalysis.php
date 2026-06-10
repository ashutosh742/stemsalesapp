<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
      #taskPlanRequestChart,#specialRequestLeaveChart,#anyReminderChart,#pendingTaskPlannerChart{
      width: 450px!important;
      height: 450px!important;
      }
      .piechartdata {
      align-items: center;
      justify-content: center;
      display: flex;
      margin-top: 14px;
      }
    </style>
  </head>
  <body>
    <?php include ("nav.php"); ?>
    <div class="container-fluid mt-5">
      <hr>
      <div class="text-center p-2">
        <h2>Todays User Planner Analysis</h2>
      </div>
      <hr>


      <?php // if($user['user_id'] == 100000){ ?>
      <div class="row">
      <div class="col-md-4">
      <?php $clusterPstDatas = $this->Menu_model->GetAdminActiveTeam($user['user_id']); ?>
        <form action="<?= base_url().'SalesGraph/TodaysUserPlannerManagementAnalysis' ?>" method="post" style="align-items: center; justify-content: center; display: flex ; padding: 10px;">
            <div class="form-group m-2">
            <label for="typeFilter">Select Admin</label>
            <select id="admin_id_filter" name="admin_id_filter" class="form-control">
              <option value="">All</option>
              <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                <option value="<?= $clusterPstData->user_id ?>"><?= $clusterPstData->name; ?>
              <?php }?>
            </select>
            </div>
            <div class="form-group mt-5">
            <button type="submit" class="btn btn-primary mt-1 p-2">Filter</button>
            </div>
            
        </form>
      </div>
      <div class="col-md-4">
      <?php $getZones = $this->Menu_model->getZoneByAdmin($uid); ?>
        <form action="<?= base_url().'SalesGraph/TodaysUserPlannerManagementAnalysis' ?>" method="post" style="align-items: center;width:300px;">
            <div class="form-group m-2">
            <label for="typeFilter">Select Zone</label>
            <input type="hidden" value="<?=$uid;?>" name="target_user">
            <select id="zone_filter" name="zone_filter[]" multiple class="form-control">
              <option value="">All Zone</option>
              <?php foreach ($getZones as $getZone) { ?>
                <option value="<?= $getZone->id ?>"><?= $getZone->name; ?>
              <?php }?>
            </select>
           
            <button type="submit" class="btn btn-primary mt-1 p-2">Filter</button>
            </div>
        </form>
      </div>

      <div class="col-md-4">
      <?php $clusterPstDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($uid); ?>
        <form action="<?= base_url().'SalesGraph/TodaysUserPlannerManagementAnalysis' ?>" method="post" style="align-items: center; justify-content: center; display: flex ; padding: 10px;">
            <div class="form-group m-2">
            <label for="typeFilter">Select RM</label>
            <select id="rm_filter" name="rm_filter" class="form-control">
              <option value="">All</option>
              <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                <option value="<?= $clusterPstData->user_id ?>"><?= $clusterPstData->name; ?>
              <?php }?>
            </select>
            </div>
            <div class="form-group mt-5">
            <button type="submit" class="btn btn-primary mt-1 p-2">Filter</button>
            </div>
        </form>
      </div>
     



      </div>
      <hr>
      <?php // } ?>

      <br>
      <div class="row">
      <div class="col-md-3">
          <label for="typeFilter">Type:</label>
          <select id="typeFilter" class="form-control">
            <option value="">All</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="nameFilter">Name:</label>
          <select id="nameFilter" class="form-control">
            <option value="">All</option>
          </select>
        </div>
      
        <div class="col-md-3">
          <label for="workFromFilter">Work From:</label>
          <select id="workFromFilter" class="form-control">
            <option value="">All</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="dateFilter">Date:</label>
          <select id="dateFilter" class="form-control">
            <option value="">All</option>
          </select>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-12 text-center">
          <button class="btn btn-success" id="downloadPdf">Download PDF</button>
          <button class="btn btn-primary" id="downloadCharts">Download All Charts</button>
        </div>
      </div>
    </div>
    <br>
    <hr>
    <div class="container-fluid mt-5">
      <div class="row mt-3">
        <div class="col-md-6">
          <canvas id="totalPlanTimeChart"></canvas>
        </div>
        <div class="col-md-6">
          <canvas id="totalTaskTimeChart"></canvas>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-md-12">
          <canvas id="newColumnChart"></canvas>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-md-6">
          <div class="piechartdata">
            <canvas id="taskPlanRequestChart"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="piechartdata">
            <canvas id="specialRequestLeaveChart"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="piechartdata">
            <canvas id="anyReminderChart"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="piechartdata">
            <canvas id="pendingTaskPlannerChart"></canvas>
          </div>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-md-12">
          <canvas id="totalAutoTasksChart"></canvas>
        </div>
      </div>
    </div>
    <?php include ("footer.php"); ?>
    <?php
      // Sample data
      $data = $getAllUserData;
      ?>
    <script>
      $(document).ready(function() {
          // PHP array converted to JSON string
          const data = <?php echo json_encode($data); ?>;

          // Populate filter options
          populateFilterOptions(data);

          // Initialize charts
          let totalPlanTimeChart = createChart('totalPlanTimeChart', 'How Many Hours Planning is Done From Planner (Only Planed Task)', data.map(item => item.total_plan_time_on_planner), data.map(item => item.name), true);
          let totalTaskTimeChart = createChart('totalTaskTimeChart', 'Total Task Time', data.map(item => item.total_task_time), data.map(item => item.name), true);
          let taskPlanRequestChart = createPieChart('taskPlanRequestChart', 'Task Plan for Today Request', countRequests(data, 'task_plan_for_today_request'));
          let specialRequestLeaveChart = createPieChart('specialRequestLeaveChart', 'Special Request for Leave', countRequests(data, 'user_create_special_request_for_leave'));
          let anyReminderChart = createPieChart('anyReminderChart', 'Any Reminder Request', countRequests(data, 'user_request_any_reminder'));
          let pendingTaskPlannerChart = createPieChart('pendingTaskPlannerChart', 'Pending Task Planner Request', countRequests(data, 'pending_task_planner_request'));
          let totalAutoTasksChart = createChart('totalAutoTasksChart', 'Total Auto Tasks', data.map(item => item.total_autotask), data.map(item => item.name));
          let newColumnChart = createNewColumnChart('newColumnChart', 'Total Task By User', data);

          // Filter functionality
          $('select').change(function() {
              let filteredData = filterData(data);
              updateCharts(filteredData, [
                  { chart: totalPlanTimeChart, field: 'total_plan_time_on_planner' },
                  { chart: totalTaskTimeChart, field: 'total_plan_time_with_autotask' },
                  { chart: taskPlanRequestChart, field: 'task_plan_for_today_request' },
                  { chart: specialRequestLeaveChart, field: 'user_create_special_request_for_leave' },
                  { chart: anyReminderChart, field: 'user_request_any_reminder' },
                  { chart: pendingTaskPlannerChart, field: 'pending_task_planner_request' },
                  { chart: totalAutoTasksChart, field: 'total_autotask' },
                  { chart: newColumnChart, field: 'newColumn' }
              ]);
          });

          function populateFilterOptions(data) {
              let names = [...new Set(data.map(item => item.name))];
              let types = [...new Set(data.map(item => item.type_name))];
              let workFrom = [...new Set(data.map(item => item.userworkfrom))];
              let dates = [...new Set(data.map(item => item.task_date.split(' ')[0]))];

              names.forEach(name => $('#nameFilter').append(`<option value="${name}">${name}</option>`));
              types.forEach(type => $('#typeFilter').append(`<option value="${type}">${type}</option>`));
              workFrom.forEach(wf => $('#workFromFilter').append(`<option value="${wf}">${wf}</option>`));
              dates.forEach(date => $('#dateFilter').append(`<option value="${date}">${date}</option>`));
          }

          function countRequests(data, field) {
              let counts = { Yes: 0, No: 0 };
              data.forEach(item => {
                  if (item[field] === 'Yes' || item[field] === 'No') {
                      counts[item[field]]++;
                  }
              });
              return counts;
          }

          function formatTime(minutes) {
              let hours = Math.floor(minutes / 60);
              let mins = minutes % 60;
              return `${hours} hours ${mins} minutes`;
          }

          function createChart(canvasId, label, data, labels, formatAsTime = false) {
              let ctx = document.getElementById(canvasId).getContext('2d');
              return new Chart(ctx, {
                  type: 'bar',
                  data: {
                      labels: labels,
                      datasets: [{
                          label: label,
                          data: data,
                          backgroundColor: 'rgba(75, 192, 192, 0.6)',
                          borderColor: 'rgba(75, 192, 192, 1)',
                          borderWidth: 1
                      }]
                  },
                  options: {
                      scales: {
                          y: {
                              beginAtZero: true
                          }
                      },
                      plugins: {
                          title: {
                              display: true,
                              text: label,
                              font: {
                                  size: 14
                              }
                          },
                          tooltip: {
                              callbacks: {
                                  label: function(tooltipItem) {
                                      let label = tooltipItem.label || '';
                                      let value = tooltipItem.raw;
                                      return `${label}: ${formatAsTime ? formatTime(value) : value}`;
                                  }
                              }
                          },
                          datalabels: {
                              display: formatAsTime,
                              color: 'black',
                              font: {
                                  weight: 'bold'
                              },
                              formatter: function(value) {
                                  return formatTime(value);
                              },
                              anchor: 'end',
                              align: 'end',
                              offset: 4
                          }
                      }
                  }
              });
          }

          function createPieChart(canvasId, label, data) {
              let ctx = document.getElementById(canvasId).getContext('2d');
              return new Chart(ctx, {
                  type: 'pie',
                  data: {
                      labels: Object.keys(data),
                      datasets: [{
                          label: label,
                          data: Object.values(data),
                          backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                          borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                          borderWidth: 1
                      }]
                  },
                  options: {
                      responsive: true,
                      plugins: {
                          title: {
                              display: true,
                              text: label,
                              font: {
                                  size: 14
                              }
                          },
                          legend: {
                              position: 'top',
                          },
                          datalabels: {
                              display: true,
                              color: 'white',
                              font: {
                                  weight: 'bold'
                              },
                              formatter: (value, ctx) => {
                                  let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                  let percentage = (value / sum * 100).toFixed(2) + "%";
                                  return percentage;
                              }
                          }
                      }
                  }
              });
          }

          function createNewColumnChart(canvasId, label, data) {
              let ctx = document.getElementById(canvasId).getContext('2d');
              let datasets = [
                  { label: 'Total Task', data: data.map(item => item.total_task), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
                  { label: 'Approved Task', data: data.map(item => item.approved_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
                  { label: 'Pending Approved', data: data.map(item => item.pending_approved), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
                  { label: 'Complete Task', data: data.map(item => item.complete_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                  { label: 'Pending Task', data: data.map(item => item.pending_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
                  { label: 'Total Auto Task', data: data.map(item => item.total_autotask), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
                  { label: 'After Task', data: data.map(item => item.after_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
                  { label: 'Complete Auto Task', data: data.map(item => item.complete_autotask), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
                  { label: 'Pending Auto Task', data: data.map(item => item.pending_autotask), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
                  { label: 'Total Reject', data: data.map(item => item.total_reject), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                  { label: 'Pending for Assign After Reject', data: data.map(item => item.pending_for_assign_after_reject_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
                  { label: 'Admin Create Request for Self Assign', data: data.map(item => item.admin_create_request_for_self_assign), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
                  { label: 'Task Assigned by Admin After Reject', data: data.map(item => item.task_assignd_by_admin_after_reject), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
                  { label: 'Task Assigned by User After Reject', data: data.map(item => item.task_assignd_by_user_after_reject), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                  { label: 'Action Yes Purpose Yes', data: data.map(item => item.action_yes_purpose_yes), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
                  { label: 'Action Yes Purpose No', data: data.map(item => item.action_yes_purpose_no), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
                  { label: 'Action No Purpose No', data: data.map(item => item.action_no_purpose_no), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
                  { label: 'Call Task', data: data.map(item => item.call_task), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
                  { label: 'Email Task', data: data.map(item => item.email_task), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
                  { label: 'Scheduled Meeting Task', data: data.map(item => item.scheduled_meeting_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                  { label: 'Barg Meeting Task', data: data.map(item => item.barg_meeting_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
                  { label: 'WhatsApp Activity', data: data.map(item => item.whatsapp_activity), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
                  { label: 'Write MoM', data: data.map(item => item.write_mom), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
                  { label: 'Write Proposal', data: data.map(item => item.write_proposal), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
                  { label: 'Research Task', data: data.map(item => item.research_task), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
                  { label: 'Documentation Task', data: data.map(item => item.documentation_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                  { label: 'Social Networking Task', data: data.map(item => item.social_networking_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
                  { label: 'Social Activity Task', data: data.map(item => item.social_activity_task), backgroundColor: 'rgba(255, 159, 64, 0.6)' },
                  { label: 'Annual Review Target Task', data: data.map(item => item.annual_review_target_task), backgroundColor: 'rgba(255, 99, 132, 0.6)' },
                  { label: 'Join Meeting Task', data: data.map(item => item.join_meeting_task), backgroundColor: 'rgba(75, 192, 192, 0.6)' },
                  { label: 'MoM Check Task', data: data.map(item => item.mom_check_task), backgroundColor: 'rgba(255, 206, 86, 0.6)' },
                  { label: 'Create BD Request Task', data: data.map(item => item.create_bd_request_task), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                  { label: 'Submit Handover Task', data: data.map(item => item.submit_handover_task), backgroundColor: 'rgba(153, 102, 255, 0.6)' },
                  { label: 'Proposal Check Task', data: data.map(item => item.praposal_check_task), backgroundColor: 'rgba(255, 159, 64, 0.6)' }
              ];

              return new Chart(ctx, {
                  type: 'bar',
                  data: {
                      labels: data.map(item => `${item.name} - ${item.task_date}`),
                      datasets: datasets
                  },
                  options: {
                      responsive: true,
                      plugins: {
                          title: {
                              display: true,
                              text: label,
                              font: {
                                  size: 14
                              }
                          },
                          legend: {
                              position: 'top',
                          },
                          datalabels: {
                              display: true,
                              color: 'black',
                              font: {
                                  weight: 'bold'
                              },
                              formatter: Math.round
                          }
                      }
                  }
              });
          }

          function filterData(data) {
              let name = $('#nameFilter').val();
              let type = $('#typeFilter').val();
              let workFrom = $('#workFromFilter').val();
              let date = $('#dateFilter').val();

              return data.filter(item => {
                  return (!name || item.name === name) &&
                         (!type || item.type_name === type) &&
                         (!workFrom || item.userworkfrom === workFrom) &&
                         (!date || item.task_date.startsWith(date));
              });
          }

          function updateCharts(filteredData, charts) {

              charts.forEach(chartInfo => {
                  let chart = chartInfo.chart;
                  let field = chartInfo.field;
                  if (chart.config.type === 'pie') {
                      // Recalculate counts for pie charts
                      let counts = countRequests(filteredData, field);
                      chart.data.labels = Object.keys(counts);
                      chart.data.datasets[0].data = Object.values(counts);
                  } else if (field === 'newColumn') {

                      // Update new column chart
                    //   chart.data.labels = filteredData.map(item => `${item.name} - ${item.type_name} - ${item.userworkfrom} - ${item.task_date}`);
                      chart.data.labels = filteredData.map(item => `${item.name} - ${item.type_name} - ${item.userworkfrom} - ${item.task_date}`);

                      chart.data.datasets.forEach(dataset => {
                          dataset.data = filteredData.map(item => item[dataset.label.toLowerCase().replace(/ /g, '_')]);

                          console.log(dataset.label.toLowerCase().replace(/ /g, '_'));
                          console.log(filteredData);
                        //   console.log(dataset.label.toLowerCase().replace(/ /g, '_'));
                      });
                  } else {
                      // Update bar charts
                      let data = filteredData.map(item => item[field]);
                      chart.data.labels = filteredData.map(item => item.name);
                      chart.data.datasets[0].data = data;
                  }
                  chart.update();
              });
          }

          $('#downloadPdf').click(function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            html2canvas(document.body, {
                scale: 0.5 // Adjust scale to fit content
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 210; // A4 width in mm
                const pageHeight = 295; // A4 height in mm
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position -= pageHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                doc.save('dashboard.pdf');
            });
        });

        // Download All Charts
        $('#downloadCharts').click(function() {
            const chartIds = ['totalPlanTimeChart', 'totalTaskTimeChart', 'newColumnChart', 'taskPlanRequestChart', 'specialRequestLeaveChart', 'anyReminderChart', 'pendingTaskPlannerChart', 'totalAutoTasksChart'];
            chartIds.forEach(chartId => {
                const chartCanvas = document.getElementById(chartId);
                const chartImage = chartCanvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = `${chartId}.png`;
                link.href = chartImage;
                link.click();
            });
        });

      });
    </script>
  </body>
</html>
