<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Graphs with Filters</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        #doughnutChart{
            width: 450px!important;
            height: 450px!important;
        }
        #adminApprovalChart{
            width: 450px!important;
            height: 450px!important;
        }
        #pieChart{
            width: 500px!important;
            height: 500px!important;
        }
    </style>
</head>
<body>
    <?php include ("nav.php"); ?>
    <div class="container">
        <br>
       <hr>
       <div class="text-center p-2">
        <h2 class="mt-4">Company Graphs That Do Not Have Primary Contact Analysis</h2>
       </div>
       <hr>
       <br>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="userFilter">User Name</label>
                <select id="userFilter" class="form-control">
                    <option value="">All Users</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="companyFilter">Company Name</label>
                <select id="companyFilter" class="form-control">
                    <option value="">All Companies</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="approvalFilter">Approval Status</label>
                <select id="approvalFilter" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="approved">Approved</option>
                    <option value="notApproved">Not Approved</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="dateFilter">Date Range</label>
                <div style="display: flex">
                <input type="date" id="startDate" class="form-control m-1" placeholder="Start Date">
                <input type="date" id="endDate" class="form-control m-1" placeholder="End Date">
                </div>
            </div>
        </div>

        </div>

        <hr>
        <br>
        <div class="container-fluid">

        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="barChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="doughnutChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="horizontalBarChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="stackedBarChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="polarAreaChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="groupedBarChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="mixedChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="userCompanyChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="adminApprovalChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="companyCreationChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <canvas id="companyDetailsChart"></canvas>
            </div>
        </div>
    </div>

    <?php include ("footer.php"); ?>
    <?php $data = $getAllUserData; ?>

    <script>
        $(document).ready(function() {
            // PHP array converted to JSON
            let data = <?php echo json_encode($data); ?>;

            // Populate filter options dynamically
            const users = [...new Set(data.map(item => item.user_name))];
            const companies = [...new Set(data.map(item => item.compname))];

            users.forEach(user => {
                $('#userFilter').append(`<option value="${user}">${user}</option>`);
            });

            companies.forEach(company => {
                $('#companyFilter').append(`<option value="${company}">${company}</option>`);
            });

            const filterData = () => {
                const userFilter = $('#userFilter').val();
                const companyFilter = $('#companyFilter').val();
                const approvalFilter = $('#approvalFilter').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();

                let filteredData = data.filter(item => {
                    const matchesUser = !userFilter || item.user_name === userFilter;
                    const matchesCompany = !companyFilter || item.compname === companyFilter;
                    const matchesApproval = !approvalFilter || (approvalFilter === 'approved' && item.is_admin_approved) || (approvalFilter === 'notApproved' && !item.is_admin_approved);
                    const matchesDate = (!startDate || new Date(item.created_at) >= new Date(startDate)) &&
                                        (!endDate || new Date(item.created_at) <= new Date(endDate));
                    return matchesUser && matchesCompany && matchesApproval && matchesDate;
                });

                return filteredData;
            };

            const updateCharts = () => {
                const filteredData = filterData();

                // Update Bar Chart
                barChart.data.datasets[0].data = [filteredData.length];
                barChart.update();

                // Update Pie Chart
                const userDistribution = filteredData.reduce((acc, item) => {
                    acc[item.user_name] = (acc[item.user_name] || 0) + 1;
                    return acc;
                }, {});
                pieChart.data.labels = Object.keys(userDistribution);
                pieChart.data.datasets[0].data = Object.values(userDistribution);
                pieChart.update();

                // Update Doughnut Chart
                const approvalDistribution = filteredData.reduce((acc, item) => {
                    if (item.is_admin_approved) acc.approved++;
                    else acc.notApproved++;
                    return acc;
                }, { approved: 0, notApproved: 0 });
                doughnutChart.data.datasets[0].data = [approvalDistribution.approved, approvalDistribution.notApproved];
                doughnutChart.update();

                // Update Horizontal Bar Chart
                horizontalBarChart.data.labels = filteredData.map(item => item.compname);
                horizontalBarChart.data.datasets[0].data = filteredData.map(item => item.contact_count);
                horizontalBarChart.update();

                // Update Stacked Bar Chart
                const companyApprovalStatus = filteredData.reduce((acc, item) => {
                    const existing = acc.find(entry => entry.name === item.compname);
                    if (existing) {
                        if (item.is_admin_approved) existing.approved++;
                        else existing.notApproved++;
                    } else {
                        acc.push({ name: item.compname, approved: item.is_admin_approved ? 1 : 0, notApproved: item.is_admin_approved ? 0 : 1 });
                    }
                    return acc;
                }, []);
                stackedBarChart.data.labels = companyApprovalStatus.map(item => item.name);
                stackedBarChart.data.datasets[0].data = companyApprovalStatus.map(item => item.approved);
                stackedBarChart.data.datasets[1].data = companyApprovalStatus.map(item => item.notApproved);
                stackedBarChart.update();

                // Update Polar Area Chart
                polarAreaChart.data.labels = filteredData.map(item => item.compname);
                polarAreaChart.data.datasets[0].data = filteredData.map(item => item.contact_count);
                polarAreaChart.update();

                // Update Grouped Bar Chart
                const users = [...new Set(filteredData.map(item => item.user_name))];
                groupedBarChart.data.labels = filteredData.map(item => item.compname);
                groupedBarChart.data.datasets = users.map(user => ({
                    label: user,
                    data: filteredData.map(item => item.user_name === user ? item.contact_count : 0),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }));
                groupedBarChart.update();

                // Update Mixed Chart
                mixedChart.data.labels = filteredData.map(item => item.compname);
                mixedChart.data.datasets[0].data = filteredData.map(item => item.contact_count);
                mixedChart.data.datasets[1].data = filteredData.map(item => item.is_admin_approved ? 1 : 0);
                mixedChart.update();

                // Update User-Company Association Chart
                const userCompanyAssociation = filteredData.reduce((acc, item) => {
                    const existing = acc.find(entry => entry.user === item.user_name);
                    if (existing) existing.companies++;
                    else acc.push({ user: item.user_name, companies: 1 });
                    return acc;
                }, []);
                userCompanyChart.data.labels = userCompanyAssociation.map(item => item.user);
                userCompanyChart.data.datasets[0].data = userCompanyAssociation.map(item => item.companies);
                userCompanyChart.update();

                // Update Admin Approval Status Chart
                const adminApprovalStatus = filteredData.reduce((acc, item) => {
                    if (item.is_admin_approved) acc.adminApproved++;
                    else acc.notAdminApproved++;
                    return acc;
                }, { adminApproved: 0, notAdminApproved: 0 });
                adminApprovalChart.data.datasets[0].data = [adminApprovalStatus.adminApproved, adminApprovalStatus.notAdminApproved];
                adminApprovalChart.update();

                // Update Company Creation Trend Chart
                const companyCreationTrend = filteredData.reduce((acc, item) => {
                    const month = new Date(item.created_at).toLocaleString('default', { month: 'short' });
                    const existing = acc.find(entry => entry.month === month);
                    if (existing) existing.count++;
                    else acc.push({ month, count: 1 });
                    return acc;
                }, []);
                companyCreationChart.data.labels = companyCreationTrend.map(item => item.month);
                companyCreationChart.data.datasets[0].data = companyCreationTrend.map(item => item.count);
                companyCreationChart.update();

                // Update Company Details Chart
                const companyDetailsData = filteredData.map(item => ({
                    label: `${item.compname} (${item.created_at.split(' ')[0]}) - ${item.user_name}`,
                    contactCount: item.contact_count,
                    approvalStatus: item.is_admin_approved ? 1 : 0
                }));

                companyDetailsChart.data.labels = companyDetailsData.map(item => item.label);
                companyDetailsChart.data.datasets[0].data = companyDetailsData.map(item => item.contactCount);
                companyDetailsChart.data.datasets[1].data = companyDetailsData.map(item => item.approvalStatus);
                companyDetailsChart.update();
            };

            // Initialize Charts
            const barCtx = $('#barChart');
            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Companies'],
                    datasets: [{
                        label: 'Without Primary Contact',
                        data: [data.length],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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

            const pieCtx = $('#pieChart');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: [...new Set(data.map(item => item.user_name))],
                    datasets: [{
                        data: [...new Set(data.map(item => item.user_name))].map(user => data.filter(item => item.user_name === user).length),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                    }]
                }
            });

            const doughnutCtx = $('#doughnutChart');
            const doughnutChart = new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Not Approved'],
                    datasets: [{
                        data: [data.filter(item => item.is_admin_approved).length, data.filter(item => !item.is_admin_approved).length],
                        backgroundColor: ['#FF6384', '#36A2EB']
                    }]
                }
            });

            const horizontalBarCtx = $('#horizontalBarChart');
            const horizontalBarChart = new Chart(horizontalBarCtx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.compname),
                    datasets: [{
                        label: 'Number of Contacts',
                        data: data.map(item => item.contact_count),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const stackedBarCtx = $('#stackedBarChart');
            const stackedBarChart = new Chart(stackedBarCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(data.map(item => item.compname))],
                    datasets: [
                        {
                            label: 'Approved',
                            data: [...new Set(data.map(item => item.compname))].map(compname => data.filter(item => item.compname === compname && item.is_admin_approved).length),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Not Approved',
                            data: [...new Set(data.map(item => item.compname))].map(compname => data.filter(item => item.compname === compname && !item.is_admin_approved).length),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const polarAreaCtx = $('#polarAreaChart');
            const polarAreaChart = new Chart(polarAreaCtx, {
                type: 'polarArea',
                data: {
                    labels: data.map(item => item.compname),
                    datasets: [{
                        data: data.map(item => item.contact_count),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                }
            });

            const groupedBarCtx = $('#groupedBarChart');
            const groupedBarChart = new Chart(groupedBarCtx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.compname),
                    datasets: []
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const mixedCtx = $('#mixedChart');
            const mixedChart = new Chart(mixedCtx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.compname),
                    datasets: [
                        {
                            label: 'Contact Count',
                            type: 'bar',
                            data: data.map(item => item.contact_count),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Approval Status',
                            type: 'line',
                            data: data.map(item => item.is_admin_approved ? 1 : 0),
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const userCompanyCtx = $('#userCompanyChart');
            const userCompanyChart = new Chart(userCompanyCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(data.map(item => item.user_name))],
                    datasets: [{
                        label: 'Number of Companies',
                        data: [...new Set(data.map(item => item.user_name))].map(user => data.filter(item => item.user_name === user).length),
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
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

            const adminApprovalCtx = $('#adminApprovalChart');
            const adminApprovalChart = new Chart(adminApprovalCtx, {
                type: 'pie',
                data: {
                    labels: ['Admin Approved', 'Not Admin Approved'],
                    datasets: [{
                        data: [data.filter(item => item.is_admin_approved).length, data.filter(item => !item.is_admin_approved).length],
                        backgroundColor: ['#FF6384', '#36A2EB']
                    }]
                }
            });

            const companyCreationCtx = $('#companyCreationChart');
            const companyCreationChart = new Chart(companyCreationCtx, {
                type: 'bar',
                data: {
                    labels: [...new Set(data.map(item => new Date(item.created_at).toLocaleString('default', { month: 'short' })))],
                    datasets: [{
                        label: 'Companies Created',
                        data: [...new Set(data.map(item => new Date(item.created_at).toLocaleString('default', { month: 'short' })))].map(month => data.filter(item => new Date(item.created_at).toLocaleString('default', { month: 'short' }) === month).length),
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
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

            const companyDetailsCtx = $('#companyDetailsChart');
            const companyDetailsChart = new Chart(companyDetailsCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Contact Count',
                            data: [],
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Approval Status',
                            data: [],
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Add event listeners to filter inputs
            $('#userFilter, #companyFilter, #approvalFilter, #startDate, #endDate').on('change', updateCharts);

            // Update companyFilter options based on selected userFilter
            $('#userFilter').on('change', function() {
                const selectedUser = $(this).val();
                const filteredCompanies = data.filter(item => !selectedUser || item.user_name === selectedUser);
                $('#companyFilter').empty().append('<option value="">All Companies</option>');
                filteredCompanies.forEach(company => {
                    $('#companyFilter').append(`<option value="${company.compname}">${company.compname}</option>`);
                });
                updateCharts();
            });
        });
    </script>
</body>
</html>
