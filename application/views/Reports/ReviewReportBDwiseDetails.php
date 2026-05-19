<?php
// ========== EXISTING DATA PREPARATION ==========
$metricsTemplate = [
    'total_funnel' => ['label' => 'Total Funnel', 'link' => "Reports/FunnelDetails/total/{user_id}/userwise"],
    'total_funnel_complete_rp_meeting' => ['label' => 'Funnel Complete RP Meeting', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting/{user_id}/{sdate}/{edate}/userwise"],
    'total__funnel_pending_for_rp_meeting' => ['label' => 'Funnel Pending for RP Meeting', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_pending_for_rp_meeting/{user_id}/{sdate}/{edate}/userwise"],
    'total_funnel_complete_rp_meeting_by_other_bd' => ['label' => 'Funnel Complete by Other BD', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_other_bd/{user_id}/{sdate}/{edate}/userwise"],
    'total_funnel_complete_rp_meeting_by_line_manager' => ['label' => 'Funnel Complete by Line Manager', 'link' => "Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_line_manager/{user_id}/{sdate}/{edate}/userwise"],
    'total_call_connected_after_rp_meeting' => ['label' => 'Calls Connected (after RP)', 'link' => "Reports/ClosurePipeLineDetails/total_call_connected_after_rp_meeting/{user_id}/{sdate}/{edate}/userwise"],
    'total_line_manager_call_connected_after_rp_meeting' => ['label' => 'Line Manager Calls (after RP)', 'link' => "Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_rp_meeting/{user_id}/{sdate}/{edate}/userwise"],
    'total_call_not_connected_after_rp_meeting' => ['label' => 'Calls Not Connected (after RP)', 'link' => "Reports/ClosurePipeLineDetails/total_call_not_connected_after_rp_meeting/{user_id}/{sdate}/{edate}/userwise"],
    'total_proposal_sent' => ['label' => 'Proposal Sent', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_pending_for_proposal_sent' => ['label' => 'Pending Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_pending_for_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_proposal_sent_without_meeting' => ['label' => 'Proposal Sent without Meeting', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_without_meeting/{user_id}/{sdate}/{edate}/userwise"],
    'total_proposal_sent_remeeting_done' => ['label' => 'Proposal Sent – Re-meeting Done', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_done/{user_id}/{sdate}/{edate}/userwise"],
    'total_proposal_sent_remeeting_not_done' => ['label' => 'Proposal Sent – Re-meeting Not Done', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_not_done/{user_id}/{sdate}/{edate}/userwise"],
    'total_proposal_sent_by_current_bd' => ['label' => 'Proposal Sent by Current BD', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_by_current_bd/{user_id}/{sdate}/{edate}/userwise"],
    'total_proposal_sent_by_other_bd' => ['label' => 'Proposal Sent by Other BD', 'link' => "Reports/ClosurePipeLineDetails/total_proposal_sent_by_other_bd/{user_id}/{sdate}/{edate}/userwise"],
    'total_closure' => ['label' => 'Total Closure', 'link' => "Reports/ClosurePipeLineDetails/total_closure/{user_id}/{sdate}/{edate}/userwise"],
    'total_direct_closure_without_proposal_sent' => ['label' => 'Direct Closure (no proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_direct_closure_without_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_closure_after_proposal_sent' => ['label' => 'Closure after Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_closure_after_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_not_closure_after_proposal_sent' => ['label' => 'Not Closure after Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_not_closure_after_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_budget_where_proposal_sent' => ['label' => 'Budget (Proposal Sent)', 'link' => "Reports/ClosurePipeLineDetails/total_budget_where_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_closure_budget_where__proposal_sent' => ['label' => 'Closure Budget (Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_closure_budget_where__proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_not_closure_budget_where__proposal_sent' => ['label' => 'Not Closure Budget (Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_not_closure_budget_where__proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_wdl_or_ni_after_proposal_sent' => ['label' => 'WDL/NI after Proposal', 'link' => "Reports/ClosurePipeLineDetails/total_wdl_or_ni_after_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_call_connected_after_proposal_sent' => ['label' => 'Calls Connected (after Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_line_manager_call_connected_after_proposal_sent' => ['label' => 'Line Manager Calls (after Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_call_not_connected_after_proposal_sent' => ['label' => 'Calls Not Connected (after Proposal)', 'link' => "Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent/{user_id}/{sdate}/{edate}/userwise"],
    'total_call_connected_after_proposal_sent_without_meeting' => ['label' => 'Calls Connected (Proposal w/o Meeting)', 'link' => "Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent_without_meeting/{user_id}/{sdate}/{edate}/userwise"],
    'total_call_not_connected_after_proposal_sent_without_meeting' => ['label' => 'Calls Not Connected (Proposal w/o Meeting)', 'link' => "Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent_without_meeting/{user_id}/{sdate}/{edate}/userwise"]
];

$reviewInfo = isset($mdata['review_informations'][0]) ? $mdata['review_informations'][0] : null;
$reviewDetails = isset($mdata['review_details']) ? $mdata['review_details'] : [];
$reviewClusterMetrics = isset($mdata['review_cluster_metrics']) ? $mdata['review_cluster_metrics'] : [];

$groupedByUser = [];
foreach ($reviewDetails as $detail) {
    $userName = $detail->to_name ?? 'Unknown';
    if (!isset($groupedByUser[$userName])) $groupedByUser[$userName] = [];
    $groupedByUser[$userName][] = $detail;
}

// Group cluster metrics by user, then by cluster_id
$clusterGroupedByUser = [];
foreach ($reviewClusterMetrics as $clusterMetric) {
    $userName = $clusterMetric->to_name ?? 'Unknown';
    $clusterId = $clusterMetric->cluster_id ?? 0;
    if (!isset($clusterGroupedByUser[$userName])) {
        $clusterGroupedByUser[$userName] = [];
    }
    if (!isset($clusterGroupedByUser[$userName][$clusterId])) {
        $clusterGroupedByUser[$userName][$clusterId] = [
            'clustername' => $clusterMetric->clustername ?? 'Unknown Cluster',
            'metrics' => []
        ];
    }
    $clusterGroupedByUser[$userName][$clusterId]['metrics'][] = $clusterMetric;
}

// Helper functions
function prettyMetricClean($key) {
    $clean = str_replace(['_', 'rp', 'bd', 'ni', 'wdl'], [' ', 'RP', 'BD', 'NI', 'WDL'], $key);
    return ucwords(str_replace('_', ' ', $clean));
}

function renderStarsModern($rating) {
    $fullStars = floor($rating);
    $half = ($rating - $fullStars) >= 0.5;
    $html = '<span class="star-rating-modern">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) $html .= '<i class="fas fa-star" style="color:#f5a623; font-size:0.85rem;"></i>';
        elseif ($half && $i == $fullStars+1) $html .= '<i class="fas fa-star-half-alt" style="color:#f5a623; font-size:0.85rem;"></i>';
        else $html .= '<i class="far fa-star" style="color:#cbd5e1; font-size:0.85rem;"></i>';
    }
    $html .= '<span class="ms-1 fw-semibold" style="font-size:0.8rem;">'.$rating.'/5</span></span>';
    return $html;
}

function ratingPillClassModern($rating) {
    if ($rating >= 4) return 'rating-high';
    if ($rating >= 2.5) return 'rating-medium';
    return 'rating-low';
}

function ratingText($rating) {
    if ($rating >= 4) return 'Excellent';
    if ($rating >= 2.5) return 'Improving';
    return 'Needs focus';
}

$allRatings = [];
foreach ($reviewDetails as $det) if (isset($det->rating)) $allRatings[] = (float)$det->rating;
$globalAvg = count($allRatings) ? round(array_sum($allRatings)/count($allRatings), 1) : 0;

function buildMetricLink($metricKey, $userId, $sdate, $edate, $template) {
    if (!isset($template[$metricKey]['link'])) return '#';
    $link = $template[$metricKey]['link'];
    $link = str_replace('{user_id}', $userId, $link);
    $link = str_replace('{sdate}', $sdate, $link);
    $link = str_replace('{edate}', $edate, $link);
    return base_url($link);
}

// NEW: Build link for cluster metrics using the provided template
$clusterMetricsTemplate = [
    'total' => ['label' => 'Total Company', 'link' => "Reports/FunnelDetailsWithClusterID/total/{user_id}/{cluster_id}/userwise"],
    'base_location' => ['label' => 'Base Location', 'link' => "Reports/FunnelDetailsWithClusterID/base_location/{user_id}/{cluster_id}/userwise"],
    'outStation_location' => ['label' => 'OutStation Location', 'link' => "Reports/FunnelDetailsWithClusterID/outStation_location/{user_id}/{cluster_id}/userwise"],
    'total_planned_meeting' => ['label' => 'Planned Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_planned_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_complete_meeting' => ['label' => 'Completed Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_pending_for_meeting' => ['label' => 'Pending Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_pending_for_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_rp_complete_meeting' => ['label' => 'RP Complete Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_rp_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_no_rp_complete_meeting' => ['label' => 'No RP Complete Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_no_rp_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_ogt_complete_meeting' => ['label' => 'OGT Complete Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_ogt_complete_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_deleted_meeting' => ['label' => 'Deleted Meeting in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_deleted_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_complete_planned_sheduled_meeting' => ['label' => 'Complete Planned Scheduled in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_planned_sheduled_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_complete_planned_barg_in_meeting' => ['label' => 'Complete Planned Barg‑in in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_planned_barg_in_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_complete_planned_virtual_meeting' => ['label' => 'Complete Planned Virtual in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_complete_planned_virtual_meeting/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_proposal_sent' => ['label' => 'Proposal Sent in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_proposal_sent/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
    'total_clouser' => ['label' => 'Closure in Cluster', 'link' => "Reports/FunnelDetailsWithClusterIDList/total_clouser/{user_id}/{cluster_id}/userwise/{sdate}/{edate}"],
];

function buildClusterMetricLink($metricKey, $userId, $clusterId, $sdate, $edate, $template) {
    if (!isset($template[$metricKey]['link'])) return '#';
    $link = $template[$metricKey]['link'];
    $link = str_replace('{user_id}', $userId, $link);
    $link = str_replace('{cluster_id}', $clusterId, $link);
    $link = str_replace('{sdate}', $sdate, $link);
    $link = str_replace('{edate}', $edate, $link);
    return base_url($link);
}

// ========== NEW CALCULATIONS FOR SPOTLIGHT & KPI ==========
$firstReview = $reviewInfo;
$totalReviews = isset($mdata['review_informations']) ? count($mdata['review_informations']) : 0;

$totalSessionSeconds = 0;
if (isset($mdata['review_informations'])) {
    foreach ($mdata['review_informations'] as $ri) {
        $totalSessionSeconds += (int)($ri->review_session_time ?? 0);
    }
}
$hours = floor($totalSessionSeconds / 60);
$minutes = $totalSessionSeconds % 60;
$totalSessionFormatted = ($hours > 0 ? $hours . 'h ' : '') . $minutes . 'm';

$closedCount = 0;
if (isset($mdata['review_informations'])) {
    foreach ($mdata['review_informations'] as $ri) {
        if (strtolower(trim($ri->review_status ?? '')) === 'closed') $closedCount++;
    }
}

$uniqueRevieweeIds = [];
foreach ($reviewDetails as $det) {
    if (!empty($det->user_id)) $uniqueRevieweeIds[$det->user_id] = true;
}
$countallteams = count($uniqueRevieweeIds);

$revieweeNames = [];
foreach ($reviewDetails as $det) {
    if (!empty($det->to_name)) $revieweeNames[$det->to_name] = true;
}
$revieweeNames = array_keys($revieweeNames);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>BD Wise Review | STEM Performance Hub</title>
  <!-- Google Fonts: Inter + Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <!-- AdminLTE & plugin styles -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">

  <style>
    :root {
      --bg-gradient-start: #f4f7fd;
      --bg-gradient-end: #eef2fa;
      --glass-bg: rgba(255, 255, 255, 0.88);
      --glass-border: rgba(255, 255, 255, 0.6);
      --shadow-sm: 0 12px 28px -8px rgba(0, 20, 40, 0.08);
      --shadow-hover: 0 24px 40px -12px rgba(0, 32, 64, 0.14);
      --primary-accent: #2c3e8f;
      --primary-gradient: linear-gradient(125deg, #1e2b6e, #2c3e8f);
      --text-dark: #1a2a4f;
      --cluster-card-bg: rgba(255,255,255,0.7);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
      color: var(--text-dark);
    }

    .content-wrapper {
      background: transparent !important;
    }

    .glass-card {
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      border-radius: 32px;
      border: 1px solid var(--glass-border);
      box-shadow: var(--shadow-sm);
      transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
    }
    .glass-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-hover);
      border-color: rgba(255,255,255,0.8);
    }

    .glass-premium {
      background: rgba(255,255,255,0.92);
      backdrop-filter: blur(12px);
      border-radius: 2rem;
      border: 1px solid rgba(255,255,255,0.8);
      box-shadow: 0 20px 35px -12px rgba(0,0,0,0.1);
    }

    /* Cluster Card Design */
    .cluster-card {
      background: var(--cluster-card-bg);
      backdrop-filter: blur(8px);
      border-radius: 28px;
      border: 1px solid rgba(255,255,255,0.6);
      box-shadow: 0 8px 20px -6px rgba(0,0,0,0.05);
      transition: all 0.2s ease;
      overflow: hidden;
      margin-bottom: 1.5rem;
    }
    .cluster-card-header {
      background: rgba(44, 62, 143, 0.08);
      padding: 1rem 1.5rem;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-weight: 600;
      color: #1e2b6e;
    }
    .cluster-card-header i { font-size: 1.2rem; }
    .cluster-card-header h4 {
      font-size: 1rem;
      margin: 0;
      font-weight: 600;
    }
    .cluster-card-body {
      padding: 1.2rem 1.5rem;
    }

    /* Spotlight Styles */
    .badge-spotlight-mini {
      background: linear-gradient(135deg, #f5b042, #f59e0b);
      padding: 0.25rem 0.9rem;
      border-radius: 30px;
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: white;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .spotlight-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    .spotlight-card {
      background: rgba(255,255,255,0.75);
      backdrop-filter: blur(8px);
      border-radius: 28px;
      border: 1px solid rgba(255,255,255,0.6);
      box-shadow: 0 8px 20px -6px rgba(0,0,0,0.05);
      transition: all 0.2s ease;
      overflow: hidden;
    }
    .card-header-mini {
      background: rgba(44, 62, 143, 0.08);
      padding: 1rem 1.5rem;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-weight: 600;
      color: #1e2b6e;
    }
    .card-header-mini i { font-size: 1.2rem; }
    .card-header-mini h4 {
      font-size: 1rem;
      margin: 0;
      font-weight: 600;
    }
    .card-body-premium {
      padding: 1.2rem 1.5rem;
    }
    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      margin-bottom: 0.85rem;
      font-size: 0.85rem;
      border-bottom: 1px dashed #e2e8f0;
      padding-bottom: 0.5rem;
    }
    .info-label {
      font-weight: 500;
      color: #4a5b7a;
    }
    .info-value {
      font-weight: 600;
      color: #1a2a4f;
      text-align: right;
      max-width: 60%;
      word-break: break-word;
    }
    .badge-closed-premium, .badge-started-premium {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #e9f7ef;
      color: #0b6e41;
      padding: 0.2rem 1rem;
      border-radius: 30px;
      font-weight: 600;
      font-size: 0.75rem;
    }
    .badge-started-premium {
      background: #eef2ff;
      color: #2c3e8f;
    }
    .reviewee-chip-premium {
      background: #f1f5f9;
      padding: 0.2rem 0.7rem;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 500;
      display: inline-block;
      margin: 0.2rem;
    }

    /* User Card Styles */
    .user-card-header {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(4px);
      border-bottom: 1px solid rgba(0,0,0,0.05);
      padding: 1.2rem 2rem;
      border-radius: 32px 32px 0 0;
    }
    .user-avatar {
      width: 56px;
      height: 56px;
      background: linear-gradient(125deg, #3b82f6, #1e3a8a);
      border-radius: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.5rem;
      color: white;
    }
    .user-name {
      font-size: 1.7rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      background: linear-gradient(115deg, #16234e, #2c3e8f);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
    }
    .modern-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: transparent;
    }
    .modern-table th {
      font-weight: 600;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #4f5d7c;
      padding: 1.2rem 1.5rem;
      background: #f8faff;
      border-bottom: 1px solid #e9edf5;
    }
    .modern-table td {
      padding: 1.2rem 1.5rem;
      vertical-align: middle;
      border-bottom: 1px solid #eff2fa;
      font-weight: 500;
      color: #1f2a48;
    }
    .modern-table tr:hover td {
      background-color: rgba(59, 130, 246, 0.02);
    }
    .star-rating-modern {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ffffffcc;
      backdrop-filter: blur(2px);
      padding: 0.25rem 0.9rem;
      border-radius: 40px;
      font-weight: 600;
      font-size: 0.85rem;
    }
    .rating-pill {
      display: inline-block;
      padding: 0.2rem 1rem;
      border-radius: 40px;
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 0.3px;
    }
    .rating-high { background: #dff9e6; color: #0b6e41; }
    .rating-medium { background: #fff0db; color: #b45309; }
    .rating-low { background: #ffe9e8; color: #b91c1c; }
    .remark-chip {
      background: #ffffffdd;
      backdrop-filter: blur(2px);
      padding: 0.5rem 1rem;
      border-radius: 28px;
      font-size: 0.8rem;
      display: inline-block;
      max-width: 320px;
      color: #2d3a6b;
      border: 1px solid rgba(0,0,0,0.02);
    }
    .soft-progress {
      height: 6px;
      background: #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
    }
    .soft-progress-bar {
      background: linear-gradient(90deg, #2c3e8f, #4f6fcf);
      width: 0%;
      height: 100%;
      border-radius: 8px;
    }
    .value-link {
      font-weight: 600;
      color: #2c3e8f;
      text-decoration: none;
      transition: 0.2s;
      display: inline-block;
      padding: 0.2rem 0.5rem;
      border-radius: 20px;
      background: rgba(44, 62, 143, 0.05);
    }
    .value-link:hover {
      text-decoration: underline;
      color: #1e2b6e;
      background: rgba(44, 62, 143, 0.1);
    }
    .cluster-section {
      margin-top: 1.5rem;
      padding-top: 1rem;
    }
    @media (max-width: 768px) {
      .user-name { font-size: 1.3rem; }
      .modern-table th, .modern-table td { padding: 0.9rem 1rem; }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar (dynamic from department) -->
  <?php $this->load->view($dep_name.'/nav.php');?>

  <div class="content-wrapper" style="background: transparent;">
    <div class="container-fluid px-4 py-4">

      <!-- Glass Premium Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="glass-premium p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
              <div>
                <h1 class="display-6 fw-bold" style="background: linear-gradient(125deg, #1e293b, #3b4b7a); -webkit-background-clip: text; background-clip: text; color: transparent;">
                  BD Wise Review Report - <?= $firstReview ? htmlspecialchars($firstReview->review_types_name) : 'Overview'; ?>
                </h1>
                <p class="text-muted mt-2">Comprehensive review orchestration & performance insights</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PREMIUM SPOTLIGHT SECTION -->
      <?php if ($firstReview): ?>
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex align-items-center gap-3 mb-3">
            <i class="fas fa-crown text-warning fa-lg"></i>
            <span class="badge-spotlight-mini"><i class="fas fa-star-of-life me-1"></i> PREMIUM SPOTLIGHT</span>
            <h3 class="fw-bold mb-0" style="font-size: 1.3rem;">Review #<?= $firstReview->review_id ?? '—'; ?></h3>
          </div>
        </div>
      </div>

      <div class="spotlight-grid">
        <!-- CARD 1: IDENTITY & TYPE -->
        <div class="spotlight-card">
          <div class="card-header-mini"><i class="fas fa-id-card"></i><h4>Identity & Classification</h4></div>
          <div class="card-body-premium">
            <div class="info-row"><div class="info-label">Review ID</div><div class="info-value">#<?= $firstReview->review_id ?? '—'; ?></div></div>
            <div class="info-row"><div class="info-label">Review Type</div><div class="info-value"><?= htmlspecialchars($firstReview->review_types_name ?? 'General'); ?></div></div>
            <div class="info-row"><div class="info-label">Reviewed By</div><div class="info-value"><?= htmlspecialchars($firstReview->by_name ?? '—'); ?></div></div>
            <div class="info-row"><div class="info-label">Created</div><div class="info-value"><?= isset($firstReview->review_created_at) ? date('Y-m-d H:i', strtotime($firstReview->review_created_at)) : '—'; ?></div></div>
          </div>
        </div>
        <!-- CARD 2: SCHEDULE & TIMING -->
        <div class="spotlight-card">
          <div class="card-header-mini"><i class="fas fa-calendar-alt"></i><h4>Schedule & Duration</h4></div>
          <div class="card-body-premium">
            <div class="info-row"><div class="info-label">Planned Date</div><div class="info-value"><?= (isset($firstReview->review_planned_date) && $firstReview->review_planned_date != '0000-00-00') ? date('Y-m-d', strtotime($firstReview->review_planned_date)) : '—'; ?></div></div>
            <div class="info-row"><div class="info-label">Start Date</div><div class="info-value"><?= (isset($firstReview->review_start_date) && $firstReview->review_start_date != '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime($firstReview->review_start_date)) : '—'; ?></div></div>
            <div class="info-row"><div class="info-label">End Date</div><div class="info-value"><?= (isset($firstReview->review_end_date) && !empty($firstReview->review_end_date) && $firstReview->review_end_date != '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime($firstReview->review_end_date)) : '—'; ?></div></div>
            
          </div>
        </div>
        <!-- CARD 3: STATUS & REMARKS -->
        <div class="spotlight-card">
          <div class="card-header-mini"><i class="fas fa-clipboard-list"></i><h4>Status & Resolution</h4></div>
          <div class="card-body-premium">
            <div class="info-row"><div class="info-label">Current Status</div><div class="info-value"><?php if(strtolower(trim($firstReview->review_status ?? 'Started')) == 'closed'): ?><span class="badge-closed-premium"><i class="fas fa-check-circle"></i> Closed</span><?php else: ?><span class="badge-started-premium"><i class="fas fa-play-circle"></i> <?= htmlspecialchars($firstReview->review_status ?? 'Started'); ?></span><?php endif; ?></div></div>
            <div class="info-row"><div class="info-label">Close Remarks</div><div class="info-value"><?= (!empty($firstReview->review_close_reamrks) ? nl2br(htmlspecialchars(mb_strimwidth($firstReview->review_close_reamrks,0,120,'…'))) : (!empty($firstReview->review_close_remarks) ? nl2br(htmlspecialchars(mb_strimwidth($firstReview->review_close_remarks,0,120,'…'))) : '—')); ?></div></div>
            <div class="info-row"><div class="info-label">Comments</div><div class="info-value"><?= (!empty($firstReview->review_comments) ? htmlspecialchars($firstReview->review_comments) : '—'); ?></div></div>
          </div>
        </div>
        <!-- CARD 4: PARTICIPANTS & META -->
        <div class="spotlight-card">
          <div class="card-header-mini"><i class="fas fa-users-viewfinder"></i><h4>Participants & Meta</h4></div>
          <div class="card-body-premium">
            <div class="info-row"><div class="info-label">Reviewee(s)</div><div class="info-value"><?php if(!empty($revieweeNames)): foreach($revieweeNames as $revName): ?><span class="reviewee-chip-premium"><?= htmlspecialchars($revName); ?></span> <?php endforeach; else: ?>—<?php endif; ?></div></div>
            <div class="info-row"><div class="info-label">Last updated</div><div class="info-value"><?= isset($firstReview->updated_at) ? date('Y-m-d H:i', strtotime($firstReview->updated_at)) : '—'; ?></div></div>
          </div>
        </div>
      </div>
      <?php else: ?>
      <div class="alert alert-info rounded-4 shadow-sm mb-4">No review information available for spotlight.</div>
      <?php endif; ?>

      <!-- ========== BD CARDS WITH CLUSTER CARDS ========== -->
      <?php if (empty($groupedByUser)): ?>
        <div class="glass-card p-5 text-center">
          <i class="fas fa-inbox fa-3x text-secondary mb-3 opacity-50"></i>
          <h5 class="fw-semibold">No review data found</h5>
          <p class="text-secondary">BD performance metrics will appear here.</p>
        </div>
      <?php else: ?>
        <?php
        $tableCounter = 0;
        $cardsData = [];
        foreach ($groupedByUser as $bdPerson => $metrics): 
            $firstMetric = $metrics[0] ?? null;
            $periodStart = $firstMetric ? date('d M Y', strtotime($firstMetric->sdate)) : '—';
            $periodEnd = $firstMetric ? date('d M Y', strtotime($firstMetric->edate)) : '—';
            $reviewer = $firstMetric->by_name ?? ($reviewInfo->by_name ?? 'Line Manager');
            $initials = strtoupper(substr($bdPerson, 0, 2));
            
            $userRatings = array_filter(array_map(function($m) { return isset($m->rating) ? (float)$m->rating : null; }, $metrics));
            $pipelineAvgRating = count($userRatings) ? round(array_sum($userRatings)/count($userRatings), 1) : 0;
            $tableCounter++;
            $tableId = "bdTable_" . $tableCounter;

            $to_uid = $firstMetric->user_id;
            $sdate  = $firstMetric->sdate;
            $edate  = $firstMetric->edate;

            if(empty($sdate)){
                $edate = date('Y-m-d');
                $sdate = date('Y-m-d', strtotime($edate . ' -7 days'));
            }

            // Fetch performance data (same as before)
            $performanceData = null;
            try {
                if (isset($this->Report_model) && method_exists($this->Report_model, 'getAllCompanyBYRollesMaiingClosurePipeLine')) {
                    $worksDatas = $this->Report_model->getAllCompanyBYRollesMaiingClosurePipeLine($to_uid, $to_uid, $to_uid, $sdate, $edate);
                    if (isset($worksDatas['totalClosurePipeLineDatasByuser'][0])) {
                        $performanceData = $worksDatas['totalClosurePipeLineDatasByuser'][0];
                    }
                }
            } catch (Exception $e) {
                $performanceData = null;
            }

            // Build main metrics array
            $metricsData = [];
            foreach ($metrics as $m) {
                $metricKey = $m->metric_key;
                $actualValue = '-';
                if ($performanceData && property_exists($performanceData, $metricKey)) {
                    $val = $performanceData->$metricKey;
                    if (is_numeric($val) && $val !== '') {
                        $actualValue = number_format($val);
                    } elseif ($val !== '' && $val !== null) {
                        $actualValue = $val;
                    }
                }
                $metricsData[] = [
                    'title' => prettyMetricClean($metricKey),
                    'actual_value' => $actualValue,
                    'rating' => (float)($m->rating ?? 0),
                    'rating_text' => ratingText((float)($m->rating ?? 0)),
                    'rating_class' => ratingPillClassModern((float)($m->rating ?? 0)),
                    'remarks' => trim($m->remarks ?? '') !== '-' ? trim($m->remarks ?? '') : '',
                    'link' => buildMetricLink($metricKey, $to_uid, $sdate, $edate, $metricsTemplate)
                ];
            }

            // ========== Gather cluster metrics grouped by cluster ==========
            $clusterGroups = [];
            $clusterRatings = [];
            if (isset($clusterGroupedByUser[$bdPerson])) {
                foreach ($clusterGroupedByUser[$bdPerson] as $clusterId => $clusterData) {
                    $clusterName = $clusterData['clustername'];
                    $metricsGroup = [];
                    $clusterRatingSum = 0;
                    $clusterRatingCount = 0;
                    foreach ($clusterData['metrics'] as $cm) {
                        $metricKey = $cm->metric_key;
                        $metricValue = $cm->metric_value ?? '-';
                        $rating = (float)($cm->rating ?? 0);
                        $remarks = trim($cm->remarks ?? '');
                        $link = buildClusterMetricLink($metricKey, $to_uid, $clusterId, $sdate, $edate, $clusterMetricsTemplate);
                        $metricsGroup[] = [
                            'metric_key' => $metricKey,
                            'metric_label' => isset($clusterMetricsTemplate[$metricKey]['label']) ? $clusterMetricsTemplate[$metricKey]['label'] : prettyMetricClean($metricKey),
                            'metric_value' => $metricValue,
                            'rating' => $rating,
                            'rating_text' => ratingText($rating),
                            'rating_class' => ratingPillClassModern($rating),
                            'remarks' => $remarks,
                            'link' => $link
                        ];
                        if ($rating > 0) {
                            $clusterRatingSum += $rating;
                            $clusterRatingCount++;
                        }
                    }
                    $clusterAvg = $clusterRatingCount > 0 ? round($clusterRatingSum / $clusterRatingCount, 1) : 0;
                    $clusterGroups[] = [
                        'cluster_id' => $clusterId,
                        'cluster_name' => $clusterName,
                        'metrics' => $metricsGroup,
                        'avg_rating' => $clusterAvg
                    ];
                    $clusterRatings[] = $clusterAvg;
                }
            }
            $overallClusterAvg = count($clusterRatings) > 0 ? round(array_sum($clusterRatings) / count($clusterRatings), 1) : 0;

            // Prepare card data for download
            $cardsData[] = [
                'id' => $tableCounter,
                'name' => $bdPerson,
                'initials' => $initials,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'reviewer' => $reviewer,
                'pipeline_avg_rating' => $pipelineAvgRating,
                'cluster_avg_rating' => $overallClusterAvg,
                'metrics' => $metricsData,
                'cluster_groups' => $clusterGroups,
                'footer_text' => "Evaluation cycle: " . date('d M Y', strtotime($firstMetric->review_date ?? 'now')) . " | Funnel & closure performance",
                // Include review info for download
                'review_info' => $firstReview ? [
                    'review_id' => $firstReview->review_id,
                    'review_type' => $firstReview->review_types_name,
                    'reviewed_by' => $firstReview->by_name,
                    'planned_date' => $firstReview->review_planned_date,
                    'start_date' => $firstReview->review_start_date,
                    'end_date' => $firstReview->review_end_date,
                    'session_time' => $firstReview->review_session_time,
                    'status' => $firstReview->review_status,
                    'close_remarks' => $firstReview->review_close_remarks ?? $firstReview->review_close_reamrks,
                    'comments' => $firstReview->review_comments,
                    'created_at' => $firstReview->review_created_at,
                ] : null
            ];
        ?>
        <div class="glass-card mb-5 overflow-hidden" id="card_<?= $tableCounter ?>">
          <div class="user-card-header d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-4">
              <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
              <div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                  <h2 class="user-name mb-0"><?= htmlspecialchars($bdPerson) ?></h2>
                  <span class="rating-pill <?= ratingPillClassModern($pipelineAvgRating) ?>">
                    <i class="fas fa-star fa-xs me-1"></i> Pipeline: <?= $pipelineAvgRating ?> / 5
                  </span>
                  <?php if ($overallClusterAvg > 0): ?>
                  <span class="rating-pill <?= ratingPillClassModern($overallClusterAvg) ?>">
                    <i class="fas fa-layer-group me-1"></i> Cluster: <?= $overallClusterAvg ?> / 5
                  </span>
                  <?php endif; ?>
                </div>
                <div class="mt-2 d-flex flex-wrap gap-2">
                  <span class="badge bg-light text-dark px-3 py-1 rounded-pill">
                    <i class="far fa-calendar-range me-1"></i> <?= $periodStart ?> – <?= $periodEnd ?>
                  </span>
                  <span class="badge bg-light text-dark px-3 py-1 rounded-pill">
                    <i class="fas fa-user-check me-1"></i> Reviewed by <?= htmlspecialchars($reviewer) ?>
                  </span>
                  <span class="badge bg-light text-dark px-3 py-1 rounded-pill">
                    <i class="fas fa-chart-line me-1"></i> <?= count($metrics) ?> metrics
                  </span>
                </div>
              </div>
            </div>
            <div>
              <button class="btn btn-sm btn-outline-primary rounded-pill download-card-btn" data-card-id="<?= $tableCounter ?>">
                <i class="fas fa-download"></i> Download Report
              </button>
            </div>
          </div>
          
          <div class="p-0">
            <!-- Main metrics table -->
            <div class="table-responsive">
              <table class="modern-table" id="<?= $tableId ?>">
                <thead>
                  <tr><th>Performance Metric</th><th style="width: 100px">Actual Value</th><th style="width: 180px">Rating & Evaluation</th><th>Remarks & Actionable Feedback</th> </thead>
                <tbody>
                  <?php foreach ($metrics as $m): 
                    $ratingVal = (float)($m->rating ?? 0);
                    $remarkText = trim($m->remarks ?? '');
                    $metricTitle = prettyMetricClean($m->metric_key);
                    $metricKey = $m->metric_key;
                    $actualValue = '-';
                    if ($performanceData && property_exists($performanceData, $metricKey)) {
                        $val = $performanceData->$metricKey;
                        if (is_numeric($val) && $val !== '') $actualValue = number_format($val);
                        elseif ($val !== '' && $val !== null) $actualValue = $val;
                    }
                    $link = buildMetricLink($metricKey, $to_uid, $sdate, $edate, $metricsTemplate);
                  ?>
                  <tr>
                    <td class="fw-semibold"><i class="fas fa-chart-simple me-2 text-primary opacity-70"></i> <?= htmlspecialchars($metricTitle) ?></td>
                    <td><?php if ($actualValue !== '-' && $link !== '#'): ?><a href="<?= $link ?>" class="value-link" target="_blank"><?= htmlspecialchars($actualValue) ?></a><?php else: ?><span class="text-muted"><?= htmlspecialchars($actualValue) ?></span><?php endif; ?></td>
                    <td><div class="mb-1"><?= renderStarsModern($ratingVal) ?></div><span class="rating-pill <?= ratingPillClassModern($ratingVal) ?> mt-1"><?= ratingText($ratingVal) ?></span></td>
                    <td><?php if (!empty($remarkText) && $remarkText !== '-'): ?><div class="remark-chip"><i class="fas fa-quote-left me-1 fs-6 opacity-60"></i> <?= htmlspecialchars($remarkText) ?></div><?php else: ?><span class="text-muted fst-italic small">— No remarks</span><?php endif; ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- NEW: Cluster Cards (beautiful design) -->
            <?php if (!empty($clusterGroups)): ?>
              <div class="cluster-section">
                <h4 class="fw-semibold mb-3 px-3"><i class="fas fa-map-marker-alt me-2"></i>Cluster Performance</h4>
                <div class="row">
                  <?php foreach ($clusterGroups as $cluster): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                      <div class="cluster-card h-100">
                        <div class="cluster-card-header">
                          <i class="fas fa-building"></i>
                          <h4><?= htmlspecialchars($cluster['cluster_name']) ?></h4>
                          <span class="rating-pill <?= ratingPillClassModern($cluster['avg_rating']) ?> ms-auto">
                            <?= $cluster['avg_rating'] ?> / 5
                          </span>
                        </div>
                        <div class="cluster-card-body">
                          <div class="table-responsive">
                            <table class="modern-table" style="font-size:0.85rem;">
                              <thead>
                                <tr><th>Metric</th><th>Value</th><th>Rating</th><th>Remarks</th> </thead>
                              <tbody>
                                <?php foreach ($cluster['metrics'] as $cm): ?>
                                <tr>
                                  <td class="fw-semibold"><?= htmlspecialchars($cm['metric_label']) ?></td>
                                  <td>
                                    <?php if ($cm['link'] !== '#' && $cm['metric_value'] !== '-'): ?>
                                      <a href="<?= $cm['link'] ?>" class="value-link" target="_blank"><?= htmlspecialchars($cm['metric_value']) ?></a>
                                    <?php else: ?>
                                      <span class="text-muted"><?= htmlspecialchars($cm['metric_value']) ?></span>
                                    <?php endif; ?>
                                  </td>
                                  <td>
                                    <div class="mb-1"><?= renderStarsModern($cm['rating']) ?></div>
                                    <span class="rating-pill <?= $cm['rating_class'] ?>"><?= $cm['rating_text'] ?></span>
                                  </td>
                                  <td>
                                    <?php if (!empty($cm['remarks'])): ?>
                                      <div class="remark-chip small"><?= htmlspecialchars($cm['remarks']) ?></div>
                                    <?php else: ?>
                                      <span class="text-muted fst-italic small">—</span>
                                    <?php endif; ?>
                                  </td>
                                </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <div class="bg-white bg-opacity-40 px-4 py-2 border-top d-flex justify-content-between small text-secondary">
            <span><i class="far fa-clock me-1"></i> Evaluation cycle: <?= date('d M Y', strtotime($firstMetric->review_date ?? 'now')) ?></span>
            <span><i class="fas fa-chart-line me-1"></i> Funnel & closure performance</span>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
      
      <!-- Insight Footer -->
      <div class="row mt-4">
        <div class="col-md-6 mb-4">
          <div class="glass-card p-4 h-100 d-flex align-items-center">
            <div class="icon-circle mr-3 flex-shrink-0"><i class="fas fa-chart-pie fa-2x"></i></div>
            <div class="flex-grow-1"><h5 class="fw-bold mb-1">Review snapshot</h5><p class="mb-1 text-secondary">Total metrics: <?= count($reviewDetails) ?> | BD members: <?= count($groupedByUser) ?></p><div class="soft-progress mt-2"><div class="soft-progress-bar" style="width: <?= ($globalAvg/5)*100 ?>%;"></div></div><div class="d-flex justify-content-between mt-1"><small class="text-muted">Global performance score</small><small class="fw-bold"><?= $globalAvg ?>/5</small></div></div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="glass-card p-4 h-100 d-flex align-items-center">
            <div class="icon-warning-circle mr-3 flex-shrink-0"><i class="fas fa-lightbulb fa-2x"></i></div>
            <div><h5 class="fw-bold">Modern coaching approach</h5><p class="mb-1 text-secondary small">Fortnightly BD reviews improve proposal excellence & funnel velocity.</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer mt-4 border-0 bg-transparent text-center py-3">
    <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>" class="text-decoration-none">Stemlearning</a>.</strong> All rights reserved.
    <div class="float-right d-none d-sm-inline-block"><b>Version</b> 3.1 · Modern Review Suite</div>
  </footer>
</div>

<!-- Scripts -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
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
  window.cardsData = <?= json_encode($cardsData) ?>;
  $('table.modern-table[id^="bdTable_"]').each(function() {
    var tableId = $(this).attr('id');
    if (!$.fn.DataTable.isDataTable('#' + tableId)) {
      $('#' + tableId).DataTable({
        responsive: true,
        lengthChange: true,
        pageLength: 10,
        autoWidth: false,
        language: { search: "Filter metrics:", lengthMenu: "Show _MENU_ entries", info: "Showing _START_ to _END_ of _TOTAL_ metrics" },
        dom: '<"d-flex justify-content-between align-items-center flex-wrap"Bf>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        buttons: [
          { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-light border rounded-pill me-1' },
          { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-light border rounded-pill me-1' },
          { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-light border rounded-pill' }
        ]
      });
    }
  });

  $('.download-card-btn').on('click', function() {
    var cardId = parseInt($(this).data('card-id'));
    var cardData = window.cardsData.find(c => c.id === cardId);
    if (!cardData) return;

    var printWindow = window.open('', '_blank');
    printWindow.document.write('<!DOCTYPE html><html><head><title>BD Performance Report - ' + cardData.name + '</title>');
    printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">');
    printWindow.document.write('<style>');
    printWindow.document.write(`
      body { font-family: 'Inter', sans-serif; margin: 20px; background: white; color: #1a2a4f; }
      .print-card { max-width: 1200px; margin: 0 auto; }
      .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #2c3e8f; }
      .header h1 { color: #1e2b6e; margin-bottom: 5px; }
      .review-info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 25px; background: #f8faff; padding: 15px; border-radius: 20px; }
      .info-item { font-size: 0.9rem; }
      .info-label { font-weight: 600; color: #4a5b7a; }
      .rating-summary { display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap; }
      .rating-box { background: linear-gradient(135deg, #f5f7ff, #ffffff); padding: 12px 20px; border-radius: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
      .user-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; border-bottom: 2px solid #2c3e8f; padding-bottom: 15px; }
      .avatar { width: 56px; height: 56px; background: linear-gradient(125deg, #3b82f6, #1e3a8a); border-radius: 28px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.5rem; color: white; }
      .user-name { font-size: 1.7rem; font-weight: 700; margin: 0; color: #1e2b6e; }
      .badge { background: #f0f0f0; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; display: inline-block; margin-right: 10px; margin-bottom: 5px; }
      table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 25px; }
      th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: top; }
      th { background: #f5f5f5; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
      .rating-pill { display: inline-block; padding: 0.2rem 1rem; border-radius: 40px; font-size: 0.7rem; font-weight: 700; }
      .rating-high { background: #dff9e6; color: #0b6e41; }
      .rating-medium { background: #fff0db; color: #b45309; }
      .rating-low { background: #ffe9e8; color: #b91c1c; }
      .star-rating { display: inline-flex; align-items: center; gap: 4px; background: #f9f9f9; padding: 0.25rem 0.9rem; border-radius: 40px; }
      .remark-chip { background: #f9f9f9; padding: 0.5rem 1rem; border-radius: 28px; font-size: 0.8rem; display: inline-block; }
      .footer { margin-top: 30px; font-size: 0.7rem; color: #666; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
      .value-link { color: #2c3e8f; text-decoration: none; font-weight: 600; }
      .cluster-card { margin-bottom: 25px; background: #fafcff; border-radius: 20px; padding: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
      .cluster-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 12px; color: #1e2b6e; border-left: 4px solid #2c3e8f; padding-left: 12px; }
      @media print { body { margin: 0; } }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="print-card">');
    
    // Review Information Header
    if (cardData.review_info) {
      var ri = cardData.review_info;
      printWindow.document.write(`
        <div class="header">
          <h1>BD Performance Review Report</h1>
          <p>Review #${ri.review_id} | ${ri.review_type || 'Annual Review'}</p>
        </div>
        <div class="review-info-grid">
          <div class="info-item"><span class="info-label">Reviewed By:</span> ${escapeHtml(ri.reviewed_by || '—')}</div>
          <div class="info-item"><span class="info-label">Planned Date:</span> ${ri.planned_date && ri.planned_date !== '0000-00-00' ? ri.planned_date : '—'}</div>
          <div class="info-item"><span class="info-label">Start Date:</span> ${ri.start_date ? new Date(ri.start_date).toLocaleString() : '—'}</div>
          <div class="info-item"><span class="info-label">End Date:</span> ${ri.end_date ? new Date(ri.end_date).toLocaleString() : '—'}</div>
    
          <div class="info-item"><span class="info-label">Status:</span> ${ri.status ? (ri.status.toLowerCase() === 'closed' ? 'Closed' : ri.status) : 'Started'}</div>
          <div class="info-item"><span class="info-label">Close Remarks:</span> ${ri.close_remarks ? escapeHtml(ri.close_remarks.substring(0,150)) : '—'}</div>
          <div class="info-item"><span class="info-label">Comments:</span> ${ri.comments ? escapeHtml(ri.comments) : '—'}</div>
        </div>
      `);
    }
    
    // Overall Ratings
    printWindow.document.write(`
      <div class="rating-summary">
        <div class="rating-box"><strong>Closure Pipeline Rating:</strong> ${generateStars(cardData.pipeline_avg_rating)}</div>
        <div class="rating-box"><strong>Cluster Wise Rating:</strong> ${generateStars(cardData.cluster_avg_rating)}</div>
      </div>
    `);
    
    // User Header
    printWindow.document.write(`
      <div class="user-header">
        <div class="avatar">${escapeHtml(cardData.initials)}</div>
        <div>
          <h2 class="user-name">${escapeHtml(cardData.name)}</h2>
          <div>
            <span class="badge">${cardData.period_start} – ${cardData.period_end}</span>
            <span class="badge">Reviewed by ${escapeHtml(cardData.reviewer)}</span>
          </div>
        </div>
      </div>
    `);
    
    // Main Metrics Table
    printWindow.document.write('<h3>Closure Pipeline Metrics</h3>');
    printWindow.document.write('<table><thead><tr><th>Performance Metric</th><th>Actual Value</th><th>Rating & Evaluation</th><th>Remarks</th></thead><tbody>');
    cardData.metrics.forEach(m => {
      let stars = generateStars(m.rating);
      printWindow.document.write(`
        <tr>
          <td>${escapeHtml(m.title)}</td>
          <td>${m.link !== '#' ? '<a href="' + m.link + '" class="value-link" target="_blank">' + escapeHtml(m.actual_value) + '</a>' : escapeHtml(m.actual_value)}</td>
          <td>${stars}<br><span class="rating-pill ${m.rating_class}">${m.rating_text}</span></td>
          <td>${m.remarks ? '<div class="remark-chip">' + escapeHtml(m.remarks) + '</div>' : '—'}</td>
        </tr>
      `);
    });
    printWindow.document.write('</tbody></table>');
    
    // Cluster Metrics Tables
    if (cardData.cluster_groups && cardData.cluster_groups.length > 0) {
      printWindow.document.write('<h3>Cluster-wise Performance</h3>');
      cardData.cluster_groups.forEach(cluster => {
        printWindow.document.write(`<div class="cluster-card"><div class="cluster-title">📊 ${escapeHtml(cluster.cluster_name)}</div>`);
        printWindow.document.write('<table><thead><tr><th>Metric</th><th>Value</th><th>Rating & Evaluation</th><th>Remarks</th></thead><tbody>');
        cluster.metrics.forEach(cm => {
          let stars = generateStars(cm.rating);
          printWindow.document.write(`
            <tr>
              <td>${escapeHtml(cm.metric_label)}</td>
              <td>${cm.link !== '#' ? '<a href="' + cm.link + '" class="value-link" target="_blank">' + escapeHtml(cm.metric_value) + '</a>' : escapeHtml(cm.metric_value)}</td>
              <td>${stars}<br><span class="rating-pill ${cm.rating_class}">${cm.rating_text}</span></td>
              <td>${cm.remarks ? '<div class="remark-chip">' + escapeHtml(cm.remarks) + '</div>' : '—'}</td>
            </tr>
          `);
        });
        printWindow.document.write('</tbody></table></div>');
      });
    }
    
    printWindow.document.write(`<div class="footer">${escapeHtml(cardData.footer_text)}<br>Generated on ${new Date().toLocaleString()}</div>`);
    printWindow.document.write('</div></body></html>');
    printWindow.document.close();
    printWindow.print();

    function escapeHtml(str) {
      if (!str) return '';
      return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
      });
    }
    function generateStars(rating) {
      let full = Math.floor(rating);
      let half = (rating - full) >= 0.5;
      let stars = '';
      for (let i = 1; i <= 5; i++) {
        if (i <= full) stars += '<i class="fas fa-star" style="color:#f5a623;"></i>';
        else if (half && i === full+1) stars += '<i class="fas fa-star-half-alt" style="color:#f5a623;"></i>';
        else stars += '<i class="far fa-star" style="color:#cbd5e1;"></i>';
      }
      return `<span class="star-rating">${stars} <span style="font-weight:600;">${rating}/5</span></span>`;
    }
  });
});
</script>
</body>
</html>