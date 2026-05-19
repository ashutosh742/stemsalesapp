<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Premium Review Intelligence</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <!-- AdminLTE & Plugins -->
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
    * { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --primary-electric: #4361ee;
      --primary-glow: #3b37ff;
      --secondary-lavender: #9b5de5;
      --success-emerald: #06d6a0;
      --amber-warning: #ff9f1c;
      --slate-dark: #0f172a;
      --slate-soft: #1e293b;
      --card-radius: 32px;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f0f4fe;
    }

    .content-wrapper {
      background: linear-gradient(145deg, #f4f9ff 0%, #eef2fa 100%);
    }

    /* Premium glass card base */
    .glass-premium {
      background: rgba(255, 255, 255, 0.96);
      border-radius: var(--card-radius);
      box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.02);
      border: 1px solid rgba(255, 255, 255, 0.6);
    }

    /* ========== SPOTLIGHT CARDS (4-COLUMN GRID) ========== */
    .spotlight-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    .spotlight-card {
      flex: 1 1 calc(25% - 1.5rem);
      min-width: 220px;
      background: rgba(255, 255, 255, 0.98);
      border-radius: 28px;
      backdrop-filter: blur(2px);
      box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(67, 97, 238, 0.2);
      transition: all 0.25s ease;
      position: relative;
      overflow: hidden;
    }
    .spotlight-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 28px 40px -16px rgba(67, 97, 238, 0.25);
      border-color: rgba(67, 97, 238, 0.4);
    }
    .spotlight-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #4361ee, #9b5de5, #06d6a0);
    }
    .card-header-mini {
      padding: 1rem 1.25rem 0.5rem 1.25rem;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .card-header-mini i {
      font-size: 1.4rem;
      background: linear-gradient(135deg, #4361ee, #7c4dff);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    .card-header-mini h4 {
      font-size: 1rem;
      font-weight: 700;
      margin: 0;
      letter-spacing: -0.2px;
      color: #1e293b;
    }
    .card-body-premium {
      padding: 1rem 1.25rem 1.25rem 1.25rem;
    }
    .info-row {
      display: flex;
      margin-bottom: 0.85rem;
      align-items: flex-start;
      font-size: 0.8rem;
    }
    .info-label {
      width: 100px;
      font-weight: 600;
      color: #4b556b;
      text-transform: uppercase;
      font-size: 0.7rem;
      letter-spacing: 0.3px;
    }
    .info-value {
      flex: 1;
      color: #0f172a;
      font-weight: 500;
      word-break: break-word;
    }
    .badge-spotlight-mini {
      background: linear-gradient(95deg, #4361ee, #7c4dff);
      color: white;
      padding: 0.2rem 0.8rem;
      border-radius: 40px;
      font-size: 0.65rem;
      font-weight: 600;
      display: inline-block;
    }
    .reviewee-chip-premium {
      background: #eef2ff;
      border-radius: 40px;
      padding: 0.2rem 0.8rem;
      display: inline-block;
      margin: 0.2rem 0.3rem;
      font-size: 0.7rem;
      font-weight: 600;
      color: #2c3e66;
    }
    .badge-closed-premium {
      background: #e6faf2;
      color: #0b5e42;
      padding: 0.3rem 0.9rem;
      border-radius: 60px;
      font-weight: 700;
      font-size: 0.7rem;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .badge-started-premium {
      background: #fff1e0;
      color: #c2410c;
      padding: 0.3rem 0.9rem;
      border-radius: 60px;
      font-weight: 700;
      font-size: 0.7rem;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    /* KPI cards */
    .kpi-neo {
      background: white;
      border-radius: 28px;
      padding: 1.25rem 1rem;
      box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(67, 97, 238, 0.12);
      position: relative;
      overflow: hidden;
    }
    .kpi-neo::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 6px;
      height: 100%;
      background: linear-gradient(to bottom, var(--primary-electric), var(--secondary-lavender));
      border-radius: 28px 0 0 28px;
    }
    .kpi-number {
      font-size: 2.4rem;
      font-weight: 800;
      background: linear-gradient(135deg, #1e2a5e, #2d3a6e);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    @media (max-width: 992px) {
      .spotlight-card { flex: 1 1 calc(50% - 1.5rem); }
    }
    @media (max-width: 576px) {
      .spotlight-card { flex: 1 1 100%; }
    }

    /* ========== IMPROVED PRINT STYLES (for main page print) ========== */
    @media print {
      .no-print, .main-footer, .control-sidebar, .btn, .dataTables_filter, 
      .dataTables_length, .dt-buttons, #premiumReviewTable_wrapper .dt-buttons,
      #premiumReviewTable_filter, #premiumReviewTable_length {
        display: none !important;
      }
      
      body, .content-wrapper {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      
      .glass-premium, .spotlight-card, .kpi-neo {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
        background: white !important;
        break-inside: avoid;
        page-break-inside: avoid;
      }
      
      .kpi-number, .card-header-mini i, .badge-spotlight-mini {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
      
      .kpi-neo {
        padding: 1rem !important;
        margin: 0.5rem !important;
      }
      .kpi-number {
        font-size: 2rem !important;
        color: #1e2a5e !important;
        background: none !important;
        -webkit-background-clip: unset !important;
        background-clip: unset !important;
      }
      
      .spotlight-card {
        background: white !important;
        border: 1px solid #ddd !important;
      }
      .info-label {
        color: #333 !important;
      }
      .info-value {
        color: #000 !important;
      }
      
      .table, table {
        width: 100% !important;
        border-collapse: collapse !important;
      }
      .table th, .table td, table th, table td {
        border: 1px solid #aaa !important;
        padding: 8px !important;
        background: white !important;
        color: black !important;
      }
      .table th, table th {
        background: #f0f0f0 !important;
        font-weight: bold !important;
      }
      
      .badge-closed-premium, .badge-started-premium {
        background: #e6faf2 !important;
        border: 1px solid #0b5e42 !important;
        color: #0b5e42 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .badge-started-premium {
        background: #fff1e0 !important;
        color: #c2410c !important;
        border-color: #c2410c !important;
      }
      
      .progress {
        border: 1px solid #ccc !important;
        background: #eee !important;
      }
      .progress-bar {
        background: #06d6a0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      
      .spotlight-card, .kpi-neo, .glass-premium {
        break-inside: avoid;
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php $this->load->view($dep_name.'/nav.php');?>

  <div class="content-wrapper" style="background: #f0f4fe;">
    <section class="content">
      <div class="container-fluid py-3">
        <!-- Hero Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="glass-premium p-4 p-lg-5">
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                  <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-2"><i class="fas fa-chart-line me-1"></i> LIVE ANALYTICS</span>
                  <h1 class="display-6 fw-bold" style="background: linear-gradient(125deg, #1e293b, #3b4b7a); -webkit-background-clip: text; background-clip: text; color: transparent;">BD Wise Review Intelligence -  <?= $mdata['review_informations'][0]->review_types_name ?></h1>
                  <p class="text-muted mt-2">Comprehensive review orchestration & performance insights</p>
                </div>
                <div class="mt-3 mt-lg-0 d-flex gap-3">
                  <div class="d-flex align-items-center gap-2 bg-white px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-calendar-alt text-primary"></i>
                    <span class="fw-semibold"><?= date('F j, Y'); ?></span>
                  </div>
                  <button id="downloadFullReportBtn" class="btn btn-primary rounded-pill px-4 shadow-sm no-print" style="background: linear-gradient(95deg, #4361ee, #7c4dff); border: none;">
                    <i class="fas fa-download me-2"></i> Download Full Review Report
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php
          // ---------- DATA EXTRACTION ----------
          $reviewsList = [];
          if(isset($mdata['review_informations']) && is_array($mdata['review_informations'])) {
              $reviewsList = $mdata['review_informations'];
          } elseif(is_array($mdata) && !isset($mdata['review_informations'])) {
              $reviewsList = $mdata;
          }
          
          $detailsByReview = [];
          if(isset($mdata['review_complete_details']) && is_array($mdata['review_complete_details'])) {
              foreach($mdata['review_complete_details'] as $detail) {
                  $rid = $detail->review_id ?? 0;
                  if(!isset($detailsByReview[$rid])) $detailsByReview[$rid] = [];
                  $detailsByReview[$rid][] = $detail;
              }
          }
          
          // KPI Calculations
          $totalReviews = count($reviewsList);
          $closedCount = count($mdata['review_complete_details']); // each detail = one closed review entry
          $totalSessionMinutes = 0;
          foreach($reviewsList as $r) {
              if(isset($r->session_time)) $totalSessionMinutes += (int)$r->session_time;
          }
          $totalHours = floor($totalSessionMinutes / 60);
          $totalMins = $totalSessionMinutes % 60;
          $totalSessionFormatted = ($totalHours > 0 ? $totalHours.'h ' : '').($totalMins > 0 ? $totalMins.'m' : '0m');
          
          // Spotlight data: first review + its details
          $firstReview = !empty($reviewsList) ? $reviewsList[0] : null;
          $firstReviewDetails = [];
          if($firstReview && isset($firstReview->id)) {
              $firstReviewDetails = isset($detailsByReview[$firstReview->id]) ? $detailsByReview[$firstReview->id] : [];
          }
          $reviewTypeMap = [];
          if(isset($review_types) && is_array($review_types)){
              foreach($review_types as $rt){ $reviewTypeMap[$rt->id] = $rt->name; }
          }
          
          $allteams = $this->Report_model->GetAllActiveTeamByLineManager($mdata['review_informations'][0]->by_uid);
          $countallteams = count($allteams); 
        ?>

        <!-- ========== PREMIUM SPOTLIGHT ========== -->
        <?php if($firstReview): ?>
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex align-items-center gap-3 mb-3">
              <i class="fas fa-crown text-warning fa-lg"></i>
              <span class="badge-spotlight-mini"><i class="fas fa-star-of-life me-1"></i> PREMIUM SPOTLIGHT</span>
              <h3 class="fw-bold mb-0" style="font-size: 1.3rem;">Review #<?= $firstReview->id ?? '—'; ?></h3>
            </div>
          </div>
        </div>
        <div class="spotlight-grid">
          <!-- CARD 1: IDENTITY & TYPE -->
          <div class="spotlight-card">
            <div class="card-header-mini"><i class="fas fa-id-card"></i><h4>Identity & Classification</h4></div>
            <div class="card-body-premium">
              <div class="info-row"><div class="info-label">Review ID</div><div class="info-value">#<?= $firstReview->id ?? '—'; ?></div></div>
              <div class="info-row"><div class="info-label">Review Type</div><div class="info-value"><?= $reviewTypeMap[$firstReview->review_type_id ?? 0] ?? 'General' ?></div></div>
              <div class="info-row"><div class="info-label">Reviewed By</div><div class="info-value"><?= htmlspecialchars($firstReview->by_name ?? '—'); ?></div></div>
              <div class="info-row"><div class="info-label">By UID</div><div class="info-value"><?= htmlspecialchars($firstReview->by_uid ?? '—'); ?></div></div>
              <div class="info-row"><div class="info-label">Created</div><div class="info-value"><?= isset($firstReview->created_at) ? date('Y-m-d H:i', strtotime($firstReview->created_at)) : '—'; ?></div></div>
            </div>
          </div>
          <!-- CARD 2: SCHEDULE & TIMING -->
          <div class="spotlight-card">
            <div class="card-header-mini"><i class="fas fa-calendar-alt"></i><h4>Schedule & Duration</h4></div>
            <div class="card-body-premium">
              <div class="info-row"><div class="info-label">Planned Date</div><div class="info-value"><?= (isset($firstReview->planned_date) && $firstReview->planned_date != '0000-00-00') ? date('Y-m-d', strtotime($firstReview->planned_date)) : '—'; ?></div></div>
              <div class="info-row"><div class="info-label">Start Date</div><div class="info-value"><?= (isset($firstReview->start_date) && $firstReview->start_date != '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime($firstReview->start_date)) : '—'; ?></div></div>
              <div class="info-row"><div class="info-label">End Date</div><div class="info-value"><?= (isset($firstReview->end_date) && !empty($firstReview->end_date) && $firstReview->end_date != '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime($firstReview->end_date)) : '—'; ?></div></div>
              <div class="info-row"><div class="info-label">Session Time</div><div class="info-value"><?php $mins = (int)($firstReview->session_time ?? 0); $formatted = floor($mins/60) > 0 ? floor($mins/60).'h '.($mins%60).'m' : ($mins > 0 ? $mins.'m' : '0m'); echo $formatted; ?></div></div>
            </div>
          </div>
          <!-- CARD 3: STATUS & REMARKS -->
          <div class="spotlight-card">
            <div class="card-header-mini"><i class="fas fa-clipboard-list"></i><h4>Status & Resolution</h4></div>
            <div class="card-body-premium">
              <div class="info-row"><div class="info-label">Current Status</div><div class="info-value"><?php if(($firstReview->review_status ?? 'Started') == 'Closed'): ?><span class="badge-closed-premium"><i class="fas fa-check-circle"></i> Closed</span><?php else: ?><span class="badge-started-premium"><i class="fas fa-play-circle"></i> <?= htmlspecialchars($firstReview->review_status ?? 'Started'); ?></span><?php endif; ?></div></div>
              <div class="info-row"><div class="info-label">Close Remarks</div><div class="info-value"><?= (!empty($firstReview->close_reamrks) ? nl2br(htmlspecialchars(mb_strimwidth($firstReview->close_reamrks,0,120,'…'))) : (!empty($firstReview->close_remarks) ? nl2br(htmlspecialchars(mb_strimwidth($firstReview->close_remarks,0,120,'…'))) : '—')); ?></div></div>
              <div class="info-row"><div class="info-label">Comments</div><div class="info-value"><?= (!empty($firstReview->comments) ? $firstReview->comments : '—'); ?></div></div>
            </div>
          </div>
          <!-- CARD 4: REVIEWEES & METADATA -->
          <div class="spotlight-card">
            <div class="card-header-mini"><i class="fas fa-users-viewfinder"></i><h4>Participants & Meta</h4></div>
            <div class="card-body-premium">
              <div class="info-row"><div class="info-label">Reviewee(s)</div><div class="info-value"><?php if(!empty($firstReviewDetails)): foreach($firstReviewDetails as $det): ?><span class="reviewee-chip-premium"><?= htmlspecialchars($det->reviewed_name ?? '—'); ?></span> <?php endforeach; else: ?>—<?php endif; ?></div></div>
              <div class="info-row"><div class="info-label">Last updated</div><div class="info-value"><?= isset($firstReview->updated_at) ? date('Y-m-d H:i', strtotime($firstReview->updated_at)) : '—'; ?></div></div>
            </div>
          </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info rounded-4 shadow-sm mb-4">No review available for spotlight.</div>
        <?php endif; ?>

        <!-- KPI Cards Row -->
        <div class="row g-4 mb-5">
          <div class="col-md-4 col-6">
            <div class="kpi-neo text-center">
              <i class="fas fa-chalkboard-user fa-2x" style="color: #4361ee; opacity: 0.7;"></i>
              <div class="kpi-number mt-2"><?= $totalReviews; ?></div>
              <div class="text-muted text-uppercase small fw-semibold">Total Reviews</div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="kpi-neo text-center">
              <i class="fas fa-clock fa-2x" style="color: #ff9f1c; opacity: 0.7;"></i>
              <div class="kpi-number mt-2"><?= $totalSessionFormatted; ?></div>
              <div class="text-muted text-uppercase small fw-semibold">Total Review Time</div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="kpi-neo text-center">
              <i class="fas fa-chart-pie fa-2x" style="color: #9b5de5; opacity: 0.7;"></i>
              <div class="kpi-number mt-2"><?= $closedCount; ?><span style="font-size: 1rem;">/<?= $countallteams; ?></span></div>
              <div class="text-muted text-uppercase small fw-semibold">Closed Reviews</div>
              <div class="progress mt-2" style="height: 4px; border-radius: 10px;">
                <div class="progress-bar bg-success" style="width: <?= $totalReviews ? ($closedCount/$countallteams)*100 : 0; ?>%; border-radius: 10px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Table Section (using review_complete_details) -->
        <div class="glass-premium p-0 overflow-hidden">
          <div class="px-4 pt-4 pb-2 d-flex flex-wrap justify-content-between align-items-center border-bottom border-light">
            <div>
              <h5 class="fw-bold mb-0"><i class="fas fa-table-list me-2 text-primary"></i>All Reviews Registry</h5>
              <p class="text-muted small">Complete session data & participant overview</p>
            </div>
          </div>
          <div class="p-3 p-md-4">
            <div class="table-responsive">
              <table id="premiumReviewTable" class="table table-striped" style="white-space: nowrap;">
                <thead class="thead-dark">
                  <tr><th>#</th><th>Review ID</th><th>Review Type</th><th>Reviewed By</th><th>Reviewee(s)</th><th>Review Closed Date</th><th>Status</th><th>Report</th></tr>
                </thead>
                <tbody>
                <?php 
                  $counter = 1;
                  foreach($mdata['review_complete_details'] as $review):
                    $reviewId = $review->review_id;
                ?>
                <tr>
                  <td class="fw-bold"><?= $counter++; ?></td>
                  <td><a target="_blank" href="<?= base_url()?>Reports/ReviewReportBDwiseDetails/<?= $reviewId; ?>/<?= $review->reviewed_user_id; ?>" class="review-link-glow text-decoration-none fw-semibold"><i class="fas fa-link me-1"></i> #<?= $reviewId; ?></a></td>
                  <td><span class="badge bg-light text-dark rounded-pill px-3 py-1"><?= htmlspecialchars($review->review_types_name); ?></span></td>
                  <td><i class="fas fa-user-astronaut me-1 text-primary"></i> <?= htmlspecialchars($review->by_name); ?></td>
                  <td><?= htmlspecialchars($review->reviewed_name); ?></td>
                  <td class="text-nowrap"><?= $review->review_created_at; ?></td>
                  <td><span class="badge bg-success text-white rounded-pill px-3 py-1">Closed</span></td>
                  <td><a target="_blank" href="<?= base_url()?>Reports/ReviewReportBDwiseDetails/<?= $reviewId; ?>/<?= $review->reviewed_user_id; ?>" class="btn-ghost-report text-decoration-none"><i class="fas fa-chart-line"></i> Insights</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer" style="background: white; border-top: 1px solid #eef2ff;">
    <strong>Copyright &copy; 2021-2025 <a href="<?=base_url();?>">Stemlearning</a>.</strong> Premium Intelligence Suite.
    <div class="float-right d-none d-sm-inline-block"><b>v3.0</b> | Atlas Core</div>
  </footer>
  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<!-- Scripts -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script>$.widget.bridge('uibutton', $.ui.button)</script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url();?>assets/js/Chart.min.js"></script>
<script src="<?=base_url();?>assets/js/sparkline.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
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

<script>
$(document).ready(function() {
  var premiumTable = $('#premiumReviewTable').DataTable({
    responsive: true, lengthChange: true, autoWidth: false, pageLength: 12,
    language: { search: '<i class="fas fa-search me-1"></i> Filter records', searchPlaceholder: "Search...", lengthMenu: "Show _MENU_ entries", info: "Showing _START_ to _END_ of _TOTAL_ reviews" },
    buttons: [{ extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn-sm rounded-pill' },{ extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-sm rounded-pill' },{ extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-sm rounded-pill' },{ extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn-sm rounded-pill' }],
    order: [[1, 'desc']], columnDefs: [{ orderable: false, targets: [7] }]
  });
  premiumTable.buttons().container().appendTo('#premiumReviewTable_wrapper .col-md-6:eq(0)');
  $('#premiumReviewTable_wrapper .dataTables_filter input').addClass('rounded-pill ps-4');
  $('[data-toggle="tooltip"]').tooltip();
});

// Prepare full report data from PHP (using same source as table: review_complete_details)
var fullReportData = {
  kpi: {
    totalReviews: <?= json_encode($totalReviews); ?>,
    totalSessionFormatted: <?= json_encode($totalSessionFormatted); ?>,
    closedCount: <?= json_encode($closedCount); ?>,
    totalTeams: <?= json_encode($countallteams); ?>
  },
  spotlight: <?php if($firstReview): echo json_encode([
    'id' => $firstReview->id ?? null,
    'review_type' => $reviewTypeMap[$firstReview->review_type_id ?? 0] ?? 'General',
    'by_name' => $firstReview->by_name ?? '—',
    'by_uid' => $firstReview->by_uid ?? '—',
    'created_at' => isset($firstReview->created_at) ? date('Y-m-d H:i', strtotime($firstReview->created_at)) : '—',
    'planned_date' => (isset($firstReview->planned_date) && $firstReview->planned_date != '0000-00-00') ? date('Y-m-d', strtotime($firstReview->planned_date)) : '—',
    'start_date' => (isset($firstReview->start_date) && $firstReview->start_date != '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime($firstReview->start_date)) : '—',
    'end_date' => (isset($firstReview->end_date) && !empty($firstReview->end_date) && $firstReview->end_date != '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime($firstReview->end_date)) : '—',
    'session_time' => $firstReview->session_time ?? 0,
    'status' => $firstReview->review_status ?? 'Started',
    'close_remarks' => (!empty($firstReview->close_reamrks) ? $firstReview->close_reamrks : ($firstReview->close_remarks ?? '—')),
    'comments' => $firstReview->comments ?? '—',
    'reviewees' => array_map(function($det) { return $det->reviewed_name ?? '—'; }, $firstReviewDetails),
    'updated_at' => isset($firstReview->updated_at) ? date('Y-m-d H:i', strtotime($firstReview->updated_at)) : '—'
  ]); else: echo json_encode(null); endif; ?>,
  reviews: <?php 
    $reviewsForReport = [];
    foreach($mdata['review_complete_details'] as $review) {
      $reviewsForReport[] = [
        'id' => $review->review_id,
        'review_type' => $review->review_types_name ?? 'Review',
        'reviewed_by' => $review->by_name ?? '—',
        'reviewee' => $review->reviewed_name ?? '—',
        'review_date' => $review->review_created_at ?? '—',
        'status' => 'Closed',
        'insights_link' => base_url('Reports/ReviewReportBDwiseDetails/'.$review->review_id.'/'.$review->reviewed_user_id)
      ];
    }
    echo json_encode($reviewsForReport);
  ?>
};

// Generate full report print window
$('#downloadFullReportBtn').on('click', function() {
  var printWindow = window.open('', '_blank');
  printWindow.document.write('<!DOCTYPE html><html><head><title>Full Review Report - STEM Performance</title>');
  printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">');
  printWindow.document.write('<style>');
  printWindow.document.write(`
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; margin: 20px; background: white; color: #1e293b; }
    .report-container { max-width: 1280px; margin: 0 auto; }
    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4361ee; padding-bottom: 20px; }
    .header h1 { font-size: 28px; color: #1e293b; }
    .header p { color: #4b556b; margin-top: 8px; }
    .kpi-grid { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; justify-content: center; }
    .kpi-card { flex: 1; min-width: 180px; background: #f8fafc; border-radius: 24px; padding: 20px; text-align: center; border: 1px solid #cbd5e1; }
    .kpi-number { font-size: 32px; font-weight: 800; color: #1e2a5e; }
    .section-title { font-size: 22px; font-weight: 700; margin: 25px 0 15px 0; color: #1e293b; border-left: 5px solid #4361ee; padding-left: 15px; }
    .spotlight-grid-print { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
    .spotlight-card-print { flex: 1; min-width: 220px; background: #f8fafc; border-radius: 20px; padding: 15px; border: 1px solid #cbd5e1; }
    .spotlight-card-print h4 { font-size: 16px; margin-bottom: 12px; color: #4361ee; }
    .info-row-print { display: flex; margin-bottom: 8px; font-size: 13px; }
    .info-label-print { width: 100px; font-weight: 600; color: #4b556b; }
    .info-value-print { flex: 1; color: #0f172a; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; vertical-align: top; font-size: 12px; }
    th { background: #f1f5f9; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
    .badge-print-closed { background: #dff9e6; color: #0b5e42; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; border: 1px solid #0b5e42; }
    .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    @media print {
      body { margin: 0; padding: 15px; }
      .kpi-card, .spotlight-card-print { break-inside: avoid; page-break-inside: avoid; }
      .badge-print-closed { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
  `);
  printWindow.document.write('</style></head><body>');
  printWindow.document.write('<div class="report-container">');
  
  // Header
  printWindow.document.write('<div class="header"><h1>📊 Full Review Report - BD Wise Intelligence</h1><p>Generated on ' + new Date().toLocaleString() + '</p></div>');
  
  // KPI Section
  printWindow.document.write('<div class="kpi-grid">');
  printWindow.document.write('<div class="kpi-card"><div class="kpi-number">' + fullReportData.kpi.totalReviews + '</div><div>Total Reviews</div></div>');
  printWindow.document.write('<div class="kpi-card"><div class="kpi-number">' + fullReportData.kpi.totalSessionFormatted + '</div><div>Total Review Time</div></div>');
  printWindow.document.write('<div class="kpi-card"><div class="kpi-number">' + fullReportData.kpi.closedCount + '/' + fullReportData.kpi.totalTeams + '</div><div>Closed Reviews</div></div>');
  printWindow.document.write('</div>');
  
  // Spotlight Section
  if (fullReportData.spotlight) {
    printWindow.document.write('<div class="section-title">⭐ Premium Spotlight — Review #' + fullReportData.spotlight.id + '</div>');
    printWindow.document.write('<div class="spotlight-grid-print">');
    // Identity
    printWindow.document.write('<div class="spotlight-card-print"><h4>Identity & Classification</h4>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Review ID</div><div class="info-value-print">#' + fullReportData.spotlight.id + '</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Type</div><div class="info-value-print">' + escapeHtml(fullReportData.spotlight.review_type) + '</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Reviewed By</div><div class="info-value-print">' + escapeHtml(fullReportData.spotlight.by_name) + '</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Created</div><div class="info-value-print">' + fullReportData.spotlight.created_at + '</div></div></div>');
    // Schedule
    printWindow.document.write('<div class="spotlight-card-print"><h4>Schedule & Duration</h4>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Planned</div><div class="info-value-print">' + fullReportData.spotlight.planned_date + '</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Start</div><div class="info-value-print">' + fullReportData.spotlight.start_date + '</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">End</div><div class="info-value-print">' + fullReportData.spotlight.end_date + '</div></div>');
    let sessionFormatted = Math.floor(fullReportData.spotlight.session_time/60) > 0 ? Math.floor(fullReportData.spotlight.session_time/60)+'h '+(fullReportData.spotlight.session_time%60)+'m' : (fullReportData.spotlight.session_time > 0 ? fullReportData.spotlight.session_time+'m' : '0m');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Session</div><div class="info-value-print">' + sessionFormatted + '</div></div></div>');
    // Status
    printWindow.document.write('<div class="spotlight-card-print"><h4>Status & Resolution</h4>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Status</div><div class="info-value-print">' + (fullReportData.spotlight.status == 'Closed' ? '<span class="badge-print-closed">Closed</span>' : fullReportData.spotlight.status) + '</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Close Remarks</div><div class="info-value-print">' + escapeHtml(fullReportData.spotlight.close_remarks) + '</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Comments</div><div class="info-value-print">' + escapeHtml(fullReportData.spotlight.comments) + '</div></div></div>');
    // Reviewees
    printWindow.document.write('<div class="spotlight-card-print"><h4>Participants & Meta</h4>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Reviewee(s)</div><div class="info-value-print">');
    if (fullReportData.spotlight.reviewees.length) {
      fullReportData.spotlight.reviewees.forEach(function(r) { printWindow.document.write('<span style="background:#eef2ff; border-radius:20px; padding:2px 10px; margin:2px; display:inline-block;">' + escapeHtml(r) + '</span> '); });
    } else { printWindow.document.write('—'); }
    printWindow.document.write('</div></div>');
    printWindow.document.write('<div class="info-row-print"><div class="info-label-print">Last updated</div><div class="info-value-print">' + fullReportData.spotlight.updated_at + '</div></div></div>');
    printWindow.document.write('</div>');
  }
  
  // All Reviews Table (matches on-screen columns)
  printWindow.document.write('<div class="section-title">📋 Complete Reviews Registry</div>');
  printWindow.document.write('<table>');
  printWindow.document.write('<thead><tr><th>#</th><th>Review ID</th><th>Review Type</th><th>Reviewed By</th><th>Reviewee(s)</th><th>Review Closed Date</th><th>Status</th></tr></thead><tbody>');
  var idx = 1;
  fullReportData.reviews.forEach(function(rev) {
    printWindow.document.write('<tr>');
    printWindow.document.write('<td>' + idx++ + '</td>');
    printWindow.document.write('<td>#' + rev.id + '</td>');
    printWindow.document.write('<td>' + escapeHtml(rev.review_type) + '</td>');
    printWindow.document.write('<td>' + escapeHtml(rev.reviewed_by) + '</td>');
    printWindow.document.write('<td>' + escapeHtml(rev.reviewee) + '</td>');
    printWindow.document.write('<td>' + rev.review_date + '</td>');
    printWindow.document.write('<td><span class="badge-print-closed">Closed</span></td>');
    printWindow.document.write('</tr>');
  });
  printWindow.document.write('</tbody></table>');
  printWindow.document.write('<div class="footer">© STEM Performance Intelligence Suite — Confidential Report</div>');
  printWindow.document.write('</div></body></html>');
  printWindow.document.close();
  printWindow.print();
  
  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
      if (m === '&') return '&amp;';
      if (m === '<') return '&lt;';
      if (m === '>') return '&gt;';
      return m;
    });
  }
});
</script>
</body>
</html>