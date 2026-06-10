<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>STEM APP | Annual Review Portal</title>
  
  <!-- Google Fonts Premium: Inter + Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 & Legacy -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  
  <!-- AdminLTE & Plugins (core structure preserved) -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
  
  <style>
    /* ---------- PREMIUM CSS OVERRIDES & ENHANCEMENTS ---------- */
    :root {
      --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      --accent-gradient: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
      --premium-shadow: 0 20px 35px -12px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.02);
      --card-radius: 1.25rem;
      --table-radius: 1rem;
      --transition-smooth: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, sans-serif;
      background: #f4f7fc;
      letter-spacing: -0.01em;
    }

    /* Premium card redesign */
    .card {
      border: none;
      border-radius: var(--card-radius);
      box-shadow: var(--premium-shadow);
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.3s ease;
      background: #ffffff;
    }
    .card:hover {
      box-shadow: 0 25px 40px -14px rgba(0,0,0,0.15);
    }
    .card-header {
      background: var(--primary-gradient);
      border-bottom: none;
      padding: 1rem 1.5rem;
    }
    .card-header h3, .card-header h4 {
      font-weight: 600;
      letter-spacing: -0.3px;
    }
    .card-header .text-center.bg-primary {
      background: rgba(255,255,255,0.12) !important;
      backdrop-filter: blur(2px);
      border-radius: 60px;
      display: inline-block;
      width: auto;
      margin: 0 auto;
      padding: 0.5rem 2rem !important;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .card-header .text-center.bg-primary h3 {
      margin: 0;
      font-weight: 700;
      font-size: 1.6rem;
      background: linear-gradient(120deg, #fff, #e0e7ff);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      text-shadow: none;
    }
    .text-secondary1 {
      color: #fff !important;
      background: linear-gradient(135deg, #FFD89B, #C7E9FB);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent !important;
      font-weight: 700;
      letter-spacing: -0.2px;
    }

    /* Premium table styling */
    .table-responsive {
      border-radius: var(--table-radius);
      overflow-x: auto;
    }
    .table {
      margin-bottom: 0;
      font-size: 0.85rem;
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
    }
    .table thead th {
      background: #0f2b3d;
      color: white;
      font-weight: 600;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 14px 12px;
      border-bottom: none;
      vertical-align: middle;
      white-space: nowrap;
    }
    .table thead th:first-child {
      border-top-left-radius: 12px;
    }
    .table thead th:last-child {
      border-top-right-radius: 12px;
    }
    .table tbody tr {
      transition: var(--transition-smooth);
      border-bottom: 1px solid #eef2f6;
    }
    .table tbody tr:hover {
      background: #f8fafd !important;
      transform: scale(1.001);
      box-shadow: inset 0 0 0 1px rgba(42,82,152,0.15);
    }
    .table td {
      padding: 12px 12px;
      vertical-align: middle;
      color: #1e2a3e;
      font-weight: 500;
    }
    .table a {
      color: #1e4a76;
      font-weight: 600;
      text-decoration: none;
      border-bottom: 1px dotted transparent;
      transition: all 0.2s;
    }
    .table a:hover {
      color: #0e2f4a;
      border-bottom-color: #f6b26b;
      text-decoration: none;
    }

    /* premium badges & status pills */
    .bg-warning, .bg-success, .bg-danger {
      padding: 0.3rem 0.8rem !important;
      border-radius: 40px !important;
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.3px;
      display: inline-block;
      text-transform: capitalize;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .bg-warning {
      background: linear-gradient(110deg, #ffb347, #ff8c00) !important;
      color: #2c1a00;
    }
    .bg-success {
      background: linear-gradient(110deg, #11998e, #38ef7d) !important;
      color: #003d33;
    }
    .bg-danger {
      background: linear-gradient(110deg, #ff416c, #ff4b2b) !important;
      color: white;
    }

    /* View button premium */
    a.view {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      background: linear-gradient(135deg, #2b5876, #1e3c72);
      color: white !important;
      border-radius: 40px !important;
      padding: 6px 16px !important;
      font-weight: 600;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      transition: all 0.25s;
      border: none;
    }
    a.view:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg, #1e3c72, #0f2b4f);
      box-shadow: 0 8px 18px rgba(0,0,0,0.1);
      color: #fff;
    }
    a.view:before {
      content: "\f06e";
      font-family: "Font Awesome 6 Free";
      font-weight: 400;
      font-size: 0.7rem;
    }

    /* DataTables premium restyling */
    .dataTables_wrapper .dt-buttons .btn {
      background: white;
      border-radius: 30px;
      border: 1px solid #dee2e6;
      margin: 0 2px;
      padding: 0.3rem 1rem;
      font-weight: 500;
      font-size: 0.75rem;
      transition: all 0.2s;
      box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .dataTables_wrapper .dt-buttons .btn:hover {
      background: var(--primary-gradient);
      color: white;
      border-color: transparent;
      transform: translateY(-1px);
    }
    .dataTables_filter input {
      border-radius: 40px !important;
      border: 1px solid #cfdfed;
      padding: 0.4rem 1rem 0.4rem 2rem;
      font-size: 0.8rem;
      background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"><circle cx="10" cy="10" r="7"/><line x1="21" y1="21" x2="15" y2="15"/></svg>');
      background-repeat: no-repeat;
      background-position: 12px center;
      background-size: 14px;
    }
    .dataTables_length select {
      border-radius: 30px;
      padding: 0.25rem 1.5rem 0.25rem 0.75rem;
    }
    .page-item.active .page-link {
      background: var(--primary-gradient);
      border-color: #2a5298;
      border-radius: 30px;
    }
    .page-link {
      border-radius: 30px;
      margin: 0 3px;
      color: #1e4663;
    }

    /* footer premium */
    .main-footer {
      background: #fff;
      border-top: 1px solid rgba(0,0,0,0.05);
      box-shadow: 0 -2px 10px rgba(0,0,0,0.02);
      font-size: 0.8rem;
    }

    /* scrollbar modern */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: #eef2f5;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
      background: #b9cadb;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #8aaec0;
    }

    /* breadcrumb / page header spacing */
    .content-header h1, .breadcrumb {
      font-weight: 600;
    }
    .breadcrumb-item + .breadcrumb-item::before {
      content: "›";
      font-size: 1.2rem;
    }
    .scrollme {
      overflow-x: auto;
      scrollbar-width: thin;
    }

    /* premium responsiveness */
    @media (max-width: 768px) {
      .table thead th {
        font-size: 0.7rem;
        padding: 10px 6px;
      }
      .table td {
        font-size: 0.75rem;
        padding: 8px 6px;
      }
      a.view {
        padding: 4px 12px !important;
        font-size: 0.65rem;
      }
    }
    /* gradient text for titles */
    .text-center h3, .text-center .text-secondary1 {
      background: linear-gradient(120deg, #FFD194, #70E1F5);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    .card-header .text-secondary1 {
      font-size: 1.8rem;
      font-weight: 700;
    }
    /* extra glam for badges inside tables */
    .table td span[class*="bg-"] {
      font-weight: 600;
      backdrop-filter: blur(2px);
    }
    /* enhanced hover on rows */
    .table tbody tr {
      transition: all 0.2s ease;
    }
    .dataTables_wrapper .dt-buttons .btn {
    color: navy;
}
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar (preserved dynamic load) -->
  <?php $this->load->view($dep_name.'/nav.php'); ?>
  <style>
    .card, table {
    background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
}
  </style>
  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="text-left p-2">
                <!-- filter section preserved empty (no logic change) -->
              </div>

              <?php 
                $formatted_string = ucwords(str_replace('_', ' ', $rtype));
              ?>

              <div class="card-header">
                <div class="text-center bg-primary p-2" style="background: rgba(255,255,240,0.1) !important; border-radius: 60px;">
                  <h3>✨ Annual Review Report - <?= $fyear; ?> ✨</h3>
                </div>
                <br>
                <h3 class="text-center m-3 text-secondary1"><?=$formatted_string;?></h3>
                <h4 class="text-center m-3 text-secondary1" style="font-weight: 500;"><?=$tar_name?></h4>
              </div>
              <?php $fyear = date("Y", strtotime($financial_Year['start_date'])); ?>
              
              <div class="card-body">
                <div class="container-fluid body-content">
                  <div class="page-header">
                    <div class="table-responsive text-nowrap scrollme">
                      
                      <?php if($rtype == 'total_count' || $rtype == 'pending_review'){ ?>
                        <!-- simplified table for pending/total -->
                        <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                          <thead class="thead-dark">
                            <tr>
                              <th>S.No</th>
                              <th>CID</th>
                              <th>Companies Name</th>
                              <th>BD Name</th>
                              <th>Company Current Status</th>
                              <th>Review Status</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $i=1; ?>
                            <?php foreach($annualDatasCompanyLists as $companyList){ ?>
                            <tr>
                              <td><?=$i?></td>
                              <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><i class="fas fa-building me-1"></i> <?=$companyList->cid;?></a></td>
                              <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->compname;?></a></td>
                              <td><?=$companyList->mainbdname;?></td>
                              <td><?=$companyList->current_status;?></td>
                              <td>
                                <?php if($companyList->amr_status == 'pending'){?> 
                                  <span class="bg-warning p-1"><i class="fas fa-clock"></i> <?=$companyList->amr_status;?></span>
                                <?php }else{?>
                                  <span class="bg-success p-1"><i class="fas fa-check-circle"></i> <?=$companyList->amr_status;?></span>
                                <?php } ?>
                              </td>
                            </tr>
                            <?php $i++; } ?>
                          </tbody>
                        </table>
                      <?php }else{ ?>
                        <!-- full annual review report table with all premium columns -->
                        <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                          <thead class="thead-dark">
                            <tr>
                              <th>S.No</th>
                              <th>CID</th>
                              <th>Companies Name</th>
                              <th>Main BD Name</th>
                              <th>Review Done By</th>
                              <th>Company Current Status</th>
                              <th>Review Time Status</th>
                              <th>Financial Year</th>
                              <th>Review Status</th>
                              <th>BD Marked Current Year Focus Funnel</th>
                              <th>BD Marked Q1 Closure</th>
                              <th>BD Marked To Be Nurture in Q1</th>
                              <th>Keep Company</th>
                              <th>Keep Remarks</th>
                              <th>Any Vmeeting</th>
                              <th>Annual Focus Positive</th>
                              <th>Annual Topspender</th>
                              <th>Annual Upsell</th>
                              <th>Annual Revenue</th>
                              <th>Transfer The Funnel</th>
                              <th>Transfer Locations</th>
                              <th>Review Date</th>
                              <th>Review Final Remarks</th>
                              <th>SC Agree Or Not</th>
                              <th>Agree Or DisAgree BY</th>
                              <th>SC Remarks</th>
                              <th>View Report</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $i=1; ?>
                            <?php foreach($annualDatasCompanyLists as $companyList){ ?>
                            <tr>
                              <td><?=$i?></td>
                              <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><i class="fas fa-fingerprint"></i> <?=$companyList->cid;?></a></td>
                              <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->compname;?></a></td>
                              <td><?=$companyList->mainbdname;?></td>
                              <td><?=$companyList->review_done_by;?></td>
                              <td><?=$companyList->current_status;?></td>
                              <td><?=$companyList->review_time_status_name;?></td>
                              <td><?=$companyList->financial_year;?></td>
                              <td>
                                <?php if($companyList->amr_status == 'pending'){?> 
                                  <span class="bg-warning p-1"><?=$companyList->amr_status;?></span>
                                <?php }else{?>
                                  <span class="bg-success p-1"><?=$companyList->amr_status;?></span>
                                <?php } ?>
                              </td>
                              <td><?=$companyList->current_year_focus_funnel;?></td>
                              <td><?=$companyList->q1_closure;?></td>
                              <td><?=$companyList->to_be_Nurture_in_q1;?></td>
                              <td><?=$companyList->keep_company;?></td>
                              <td><?=$companyList->keepremark;?></td>
                              <td><?=$companyList->vmeeting;?></td>
                              <td><?=$companyList->annaul_focuspositive;?></td>
                              <td><?=$companyList->annaul_topspender;?></td>
                              <td><?=$companyList->annaul_upsell;?></td>
                              <td><?=$companyList->annaul_revenue;?></td>
                              <td><?=$companyList->transfer_the_funnel;?></td>
                              <td><?=$companyList->transfer_location;?></td>
                              <td><?=$companyList->created_at;?></td>
                              <td><?=$companyList->remarks;?></td>
                              <td>
                                <?php if($companyList->sc_agree_or_not == 'Agree'){?> 
                                  <span class="bg-success p-1"><?=$companyList->sc_agree_or_not;?></span>
                                <?php }else if($companyList->sc_agree_or_not == 'Disagree'){?>
                                  <span class="bg-danger p-1"><?=$companyList->sc_agree_or_not;?></span>
                                <?php }else{ ?>
                                  <span class="bg-warning p-1"><?=$companyList->sc_agree_or_not;?></span>
                                <?php } ?>
                              </td>
                              <td>
                                <?php if($companyList->review_apr_by == ''){?> 
                                  <span class="bg-warning p-1">Pending</span>
                                <?php }else{ ?>
                                  <?=$companyList->review_apr_by_name;?>
                                <?php } ?>
                              </td>
                              <td>
                                <?php if($companyList->review_apr_remarks == ''){?> 
                                  <span class="bg-warning p-1">Pending</span>
                                <?php }else{ ?>
                                  <?=$companyList->review_apr_remarks;?>
                                <?php } ?>
                              </td>
                              <td><a class="view" href="<?=base_url().'Menu/CheckAnnualReviewDetailsReport/'.$companyList->amr_id?>">Report</a></td>
                            </tr>
                            <?php $i++; } ?>
                          </tbody>
                        </table>
                      <?php } ?>
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

  <?php
    $startYear = date("Y");
    $endYear = $startYear + 1;
  ?>

  <footer class="main-footer">
    <strong>Copyright &copy; <?php echo "$startYear-$endYear"; ?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 2.0 <i class="fas fa-gem"></i>
    </div>
  </footer>
  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<!-- scripts (fully preserved) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url();?>assets/js/Chart.min.js"></script>
<script src="<?=base_url();?>assets/js/sparkline.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
<script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>assets/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url();?>assets/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url();?>assets/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/js/jszip.min.js"></script>
<script src="<?=base_url();?>assets/js/pdfmake.min.js"></script>
<script src="<?=base_url();?>assets/js/vfs_fonts.js"></script>
<script src="<?=base_url();?>assets/js/buttons.html5.min.js"></script>
<script src="<?=base_url();?>assets/js/buttons.print.min.js"></script>
<script src="<?=base_url();?>assets/js/buttons.colVis.min.js"></script>
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
  $(document).ready(function() {
    // enhanced DataTable with premium config (keep same export buttons)
    var table = $("#example1").DataTable({
      "responsive": false, 
      "lengthChange": true,
      "autoWidth": false,
      "pageLength": 10,
      "language": {
        "search": "🔍 Filter:",
        "lengthMenu": "Show _MENU_ entries",
        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
        "paginate": {
          "previous": "‹",
          "next": "›"
        }
      },
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    });
    table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    
    // add subtle hover effect for rows (already in css but double ensure)
    $('#example1 tbody tr').css('transition', 'all 0.2s');
  });
</script>
</body>
</html>