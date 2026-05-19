<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artwork Management Dashboard | STEM APP | WebAPP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    <style>
      .scrollme {
      overflow-x: auto;
      }
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
      }
    
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid">
               <?php if ($this->session->flashdata('pending_message')): ?>
                    <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= $this->session->flashdata('pending_message'); ?>
                </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success_message')): ?>
                    <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= $this->session->flashdata('success_message'); ?>
                </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('errors_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('errors_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <?php endif; ?>

                <hr>









<!-- Modern Artwork Details Dashboard -->
<div class="modern-artwork-dashboard">
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      --warning-gradient: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
      --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
      --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #4a6491 100%);
      --card-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
      --hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    .modern-artwork-dashboard {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    /* Header Styling */
    .dashboard-header {
      background: var(--primary-gradient);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      color: white;
      position: relative;
      overflow: hidden;
      box-shadow: var(--card-shadow);
    }
    
    .dashboard-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
      background-size: 30px 30px;
      transform: rotate(30deg);
      opacity: 0.2;
    }
    
    .dashboard-header h2 {
      font-weight: 700;
      font-size: 2.2rem;
      margin-bottom: 0.5rem;
      position: relative;
    }
    
    .dashboard-header .date-badge {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 50px;
      padding: 0.5rem 1.5rem;
      display: inline-block;
      font-size: 0.9rem;
      font-weight: 500;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    /* Table Styling */
    /* .artwork-table-container {
      background: white;
      border-radius: 20px;
      padding: 1.5rem;
      box-shadow: var(--card-shadow);
      overflow: hidden;
      margin-bottom: 2rem;
    }
    
    .artwork-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    } */
    
    /* .artwork-table thead th {
      background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
      color: #2c3e50;
      font-weight: 600;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 1.2rem 0.8rem;
      border: none;
      position: sticky;
      top: 0;
      z-index: 10;
      white-space: nowrap;
      border-bottom: 2px solid #e2e8f0;
    }
    
    .artwork-table tbody tr {
      transition: all 0.3s ease;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .artwork-table tbody tr:hover {
      background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      transform: translateX(5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .artwork-table tbody td {
      padding: 1rem 0.8rem;
      vertical-align: middle;
      border: none;
      font-size: 0.85rem;
      color: #4a5568;
    }
     */
    /* Status Badges */
    .status-badge {
      padding: 0.4rem 0.8rem;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.3px;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      text-transform: uppercase;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      white-space: nowrap;
    }
    
    .status-badge.approved {
      background: var(--success-gradient);
      color: white;
    }
    
    .status-badge.rejected {
      background: var(--danger-gradient);
      color: white;
    }
    
    .status-badge.pending {
      background: var(--warning-gradient);
      color: white;
    }
    
    /* Image Preview Cards */
    .image-preview-card {
      background: white;
      border-radius: 10px;
      padding: 0.6rem;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: 1px solid #e2e8f0;
      max-width: 100px;
    }
    
    .image-preview-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      border-color: #667eea;
    }
    
    .image-preview-card img {
      width: 45px;
      height: 45px;
      object-fit: cover;
      border-radius: 6px;
      display: block;
      margin: 0 auto 0.4rem;
    }
    
    .image-preview-card .filename {
      font-size: 0.65rem;
      color: #718096;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    /* Date Cells */
    .date-cell {
      background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
      border-radius: 8px;
      padding: 0.6rem;
      text-align: center;
      min-width: 100px;
    }
    
    .date-cell .date-main {
      font-weight: 600;
      color: #2d3748;
      font-size: 0.8rem;
    }
    
    .date-cell .date-time {
      font-size: 0.65rem;
      color: #718096;
      margin-top: 0.1rem;
    }
    
    /* Remarks Cells */
    .remarks-cell {
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      border-radius: 8px;
      padding: 0.6rem;
      max-width: 150px;
    }
    
    .remarks-text {
      font-size: 0.75rem;
      color: #475569;
      line-height: 1.3;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    /* Client Info */
    .client-info {
      background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
      border-radius: 8px;
      padding: 0.6rem;
      min-width: 120px;
    }
    
    .client-info .client-name {
      font-weight: 600;
      font-size: 0.8rem;
      color: #2d3748;
    }
    
    .client-info .client-id {
      font-size: 0.65rem;
      color: #718096;
      margin-top: 0.1rem;
    }
    
    /* Project Code */
    .project-code {
      background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
      border-radius: 8px;
      padding: 0.6rem;
      font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
      font-size: 0.7rem;
      color: #92400e;
      /* max-width: 180px; */
    }
    
    .project-code-text {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      line-height: 1.3;
    }
    
    /* Action Button */
    .action-btn {
      background: var(--primary-gradient);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 0.6rem 1rem;
      font-weight: 600;
      font-size: 0.75rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
      white-space: nowrap;
    }
    
    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }
    
    /* Serial Number */
    .serial-number {
      background: var(--dark-gradient);
      color: white;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 0.8rem;
      margin: 0 auto;
    }
    
    /* ID Badge */
    .id-badge {
      background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
      color: white;
      padding: 0.3rem 0.6rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
    }
    
    /* Handover ID */
    .handover-badge {
      background: linear-gradient(135deg, #805ad5 0%, #9f7aea 100%);
      color: white;
      padding: 0.3rem 0.6rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
    }
    
    /* Empty States */
    .empty-state {
      color: #a0aec0;
      font-style: italic;
      font-size: 0.75rem;
      background: #f7fafc;
      padding: 0.4rem;
      border-radius: 6px;
      text-align: center;
    }
    
    /* Column Icons */
    .column-icon {
      font-size: 0.9rem;
      margin-right: 0.3rem;
    }

  </style>

<hr>
  <!-- Dashboard Header -->
  <div class="dashboard-header text-center text-white">
    <h2 class='text-white'>🎨 Artwork Management Dashboard</h2>
    <h5>Artwork Pending For Approved</h5>
    <div class="date-badge">
      <i class="fas fa-calendar-alt me-2"></i>
      <?= date('F j, Y • l') ?>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if ($this->session->flashdata('success_message')): ?>
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 15px; border: none; box-shadow: var(--card-shadow);">
    <i class="fas fa-check-circle me-2"></i>
    <?= $this->session->flashdata('success_message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php endif; ?>

  <?php if ($this->session->flashdata('error_message')): ?>
  <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="border-radius: 15px; border: none; box-shadow: var(--card-shadow);">
    <i class="fas fa-exclamation-circle me-2"></i>
    <?= $this->session->flashdata('error_message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php endif; ?>

  <?php if ($this->session->flashdata('pending_message')): ?>
  <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert" style="border-radius: 15px; border: none; box-shadow: var(--card-shadow);">
    <i class="fas fa-clock me-2"></i>
    <?= $this->session->flashdata('pending_message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php endif; ?>

  <!-- Table Container -->
  <div class="">
    <div class="table-responsive text-nowrap">
       <table id="example1" class="table table-striped" cellspacing="0" width="100%">
        <thead class='thead-dark'>
          <tr>
            <th><span class="column-icon">#️⃣</span> Sr</th>
            <th><span class="column-icon">🆔</span> ID</th>
            <th><span class="column-icon">📋</span> Handover ID</th>

            <th><span class="column-icon">👤</span> Client Name</th>
            <th><span class="column-icon">🆔</span> Client ID</th>
            <th><span class="column-icon">📁</span> Project Code</th>

            <th><span class="column-icon">🎨</span> Design</th>
            <th><span class="column-icon">✏️</span> Designed</th>
            <th><span class="column-icon">🖼️</span> Backdrop</th>
            <th><span class="column-icon">✅</span> FM Status</th>
            <th><span class="column-icon">📅</span> FM Date</th>
            <th><span class="column-icon">💬</span> FM Remarks</th>
            <th><span class="column-icon">📊</span> Pro Status</th>
            <th><span class="column-icon">📅</span> Pro Date</th>
            <th><span class="column-icon">💬</span> Pro Remarks</th>

            <th><span class="column-icon">📊</span> BD Status</th>
            <th><span class="column-icon">📅</span> BD Date</th>
            <th><span class="column-icon">💬</span> BD Remarks</th>

            <th><span class="column-icon">🕐</span> Created At</th>
            <th><span class="column-icon">🔄</span> Updated At</th>

            <th><span class="column-icon">⚡</span> Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $j = 1; foreach($getAllArtworks as $data): ?>
          <tr>
            <!-- Serial Number -->
            <td>
              <div class="serial-number"><?= $j ?></div>
            </td>
            
            <!-- ID -->
            <td>
              <span class="id-badge"><?= $data->id ?></span>
            </td>
            
            <!-- Handover ID -->
            <td>
              <a href="https://stemoppapp.in//Menu/ProjectProfileDetails/<?= $data->handover_id ?>" target="_blank"><span class="handover-badge"><?= $data->handover_id ?></span></a>
            </td>

             <!-- Client Name -->
            <td>
              <div class="client-info">
                <div class="client-name"><?= $data->client_name ?></div>
              </div>
            </td>
            
            <!-- Client ID -->
            <td>
              <div class="client-info">
                <div class="client-id">ID: <?= $data->client_id ?></div>
              </div>
            </td>
            
            <!-- Project Code -->
            <td>
              <div class="project-code">
                <div class="project-code-textdf" title="<?= $data->projectcode ?>">
                  <?= $data->projectcode ?>
                </div>
              </div>
            </td>
            
            <!-- Design -->
            <td>
              <?php if(!empty($data->design)): ?>
                <div class="image-preview-card">
                  <a href="https://stemfactory.in/<?= $data->design ?>" target="_blank" title="<?= basename($data->design) ?>">
                    <img src="https://stemfactory.in/<?= $data->design ?>" alt="Design">
                  </a>
                  <div class="filename"><?= basename($data->design) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">No Design</span>
              <?php endif; ?>
            </td>
            
            <!-- Designed -->
            <td>
              <?php if(!empty($data->designed)): ?>
                <div class="image-preview-card">
                  <a href="https://stemfactory.in/<?= $data->designed ?>" target="_blank" title="<?= basename($data->designed) ?>">
                    <img src="https://stemfactory.in/<?= $data->designed ?>" alt="Designed">
                  </a>
                  <div class="filename"><?= basename($data->designed) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">No Design</span>
              <?php endif; ?>
            </td>
            
            <!-- Backdrop -->
            <td>
              <?php if(!empty($data->backdrop)): ?>
                <div class="image-preview-card">
                  <a href="https://stemfactory.in/<?= $data->backdrop ?>" target="_blank" title="<?= basename($data->backdrop) ?>">
                    <img src="https://stemfactory.in/<?= $data->backdrop ?>" alt="Backdrop">
                  </a>
                  <div class="filename"><?= basename($data->backdrop) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">No Backdrop</span>
              <?php endif; ?>
            </td>
            
            <!-- FM Status -->
            <td>
              <span class="status-badge <?= $data->fm_status == 1 ? 'approved' : ($data->fm_status == 0 ? 'rejected' : 'pending') ?>">
                <i class="fas fa-<?= $data->fm_status == 1 ? 'check' : ($data->fm_status == 0 ? 'times' : 'clock') ?>"></i>
                <?= $data->fm_status == 1 ? 'Approved' : ($data->fm_status == 0 ? 'Rejected' : 'Pending') ?>
              </span>
            </td>
            
            <!-- FM Date -->
            <td>
              <?php if(!empty($data->fm_date)): ?>
                <div class="date-cell">
                  <div class="date-main"><?= date('d M Y', strtotime($data->fm_date)) ?></div>
                  <div class="date-time"><?= date('H:i', strtotime($data->fm_date)) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">-</span>
              <?php endif; ?>
            </td>
            
            <!-- FM Remarks -->
            <td>
              <div class="remarks-cell">
                <div class="remarks-text" title="<?= $data->fm_remarks ?>">
                  <?= !empty($data->fm_remarks) ? $data->fm_remarks : 'No remarks' ?>
                </div>
              </div>
            </td>
            
            <!-- Pro Status -->
            <td>
              <?php if($data->pro_status == 1): ?>
                <span class="status-badge approved">
                  <i class="fas fa-check"></i> Approved
                </span>
              <?php elseif($data->pro_status == 0): ?>
                <span class="status-badge rejected">
                  <i class="fas fa-times"></i> Rejected
                </span>
              <?php else: ?>
                <span class="status-badge pending">
                  <i class="fas fa-clock"></i> Pending
                </span>
              <?php endif; ?>
            </td>
            
            <!-- Pro Date -->
            <td>
              <?php if(!empty($data->pro_date)): ?>
                <div class="date-cell">
                  <div class="date-main"><?= date('d M Y', strtotime($data->pro_date)) ?></div>
                  <div class="date-time"><?= date('H:i', strtotime($data->pro_date)) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">-</span>
              <?php endif; ?>
            </td>
            
            <!-- Pro Remarks -->
            <td>
              <div class="remarks-cell">
                <div class="remarks-text" title="<?= $data->pro_remarks ?>">
                  <?= !empty($data->pro_remarks) ? $data->pro_remarks : 'No remarks' ?>
                </div>
              </div>
            </td>
            

            <!-- Pro Status -->
            <td>
              <?php if($data->bd_status == 1): ?>
                <span class="status-badge approved">
                  <i class="fas fa-check"></i> Approved
                </span>
              <?php elseif($data->bd_status == 0): ?>
                <span class="status-badge rejected">
                  <i class="fas fa-times"></i> Rejected
                </span>
              <?php else: ?>
                <span class="status-badge pending">
                  <i class="fas fa-clock"></i> Pending
                </span>
              <?php endif; ?>
            </td>
            
            <!-- Pro Date -->
            <td>
              <?php if(!empty($data->bd_date)): ?>
                <div class="date-cell">
                  <div class="date-main"><?= date('d M Y', strtotime($data->bd_date)) ?></div>
                  <div class="date-time"><?= date('H:i', strtotime($data->bd_date)) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">-</span>
              <?php endif; ?>
            </td>
            
            <!-- Pro Remarks -->
            <td>
              <div class="remarks-cell">
                <div class="remarks-text" title="<?= $data->bd_remarks ?>">
                  <?= !empty($data->bd_remarks) ? $data->bd_remarks : 'No remarks' ?>
                </div>
              </div>
            </td>
            
            
            <!-- Created At -->
            <td>
              <?php if(!empty($data->created_at)): ?>
                <div class="date-cell">
                  <div class="date-main"><?= date('d M Y', strtotime($data->created_at)) ?></div>
                  <div class="date-time"><?= date('H:i', strtotime($data->created_at)) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">-</span>
              <?php endif; ?>
            </td>
            
            <!-- Updated At -->
            <td>
              <?php if(!empty($data->updated_at)): ?>
                <div class="date-cell">
                  <div class="date-main"><?= date('d M Y', strtotime($data->updated_at)) ?></div>
                  <div class="date-time"><?= date('H:i', strtotime($data->updated_at)) ?></div>
                </div>
              <?php else: ?>
                <span class="empty-state">-</span>
              <?php endif; ?>
            </td>
            

            

            
            <!-- Actions -->
            <td>
              <?php if($data->bd_status == ''): ?>
               <?php if(in_array($user['type_id'],[13,4,15,21,22,23])){?>
                <button type="button" class="action-btn" onclick="Reject(<?= $j ?>,<?= $data->id?>,'Reject')">
                  <i class="fas fa-bolt"></i>
                  Action
                </button>
               <?php }else{?> 
               <span class="status-badge pendingd">
                  <i class="fas fa-times"></i>
                  Pending By LM Approved
                </span>
               <?php } ?>
              <?php elseif($data->bd_status == 1): ?>
                <span class="status-badge approved">
                  <i class="fas fa-check"></i>
                  Approved
                </span>
              <?php else: ?>
                <span class="status-badge rejected">
                  <i class="fas fa-times"></i>
                  Rejected
                </span>
              <?php endif; ?>
            </td>
          </tr>
          <?php $j++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modern Modal -->
<div class="modal fade modern-modal" id="plannerRequestmodalCenter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background: var(--primary-gradient);">
        <h5 class="modal-title text-white">🎯 Artwork Action Center</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-7">
            <form action="<?=base_url();?>Menu/ArtworksHandlingApproved" method="post">
              <input type="hidden" id="request_id" name="request_id" value="" required />
              
              <div class="mb-4">
                <label class="form-label fw-bold">📋 Select Action</label>
                <select class="form-select" name="request_status" required 
                        style="background: var(--primary-gradient); color: white; border: none; border-radius: 10px; padding: 0.8rem;">
                  <option value="">Choose an action...</option>
                  <option value="1" style="background: white; color: #38a169;">✅ Approve</option>
                  <option value="0" style="background: white; color: #e53e3e;">❌ Reject</option>
                  <!-- <option value="3" style="background: white; color: #d69e2e;">⏸️ Hold</option>
                  <option value="4" style="background: white; color: #3182ce;">📝 Send for Revision</option> -->
                </select>
              </div>
              
              <div class="mb-4">
                <label class="form-label fw-bold">💬 Remarks</label>
                <textarea class="form-control" name="remarks" rows="4" 
                          placeholder="Add your comments here..." 
                          style="border-radius: 10px; border: 2px solid #e2e8f0; resize: vertical;"></textarea>
              </div>
              
              <div class="d-grid">
                <button type="submit" class="btn btn-lg action-btn" id="plannerRequestbtn">
                  <i class="fas fa-paper-plane me-2"></i>
                  Submit Action
                </button>
              </div>
            </form>
          </div>
          <div class="col-lg-5 d-none d-lg-block">
            <div class="text-center p-4">
              <img src="https://cdn-icons-png.flaticon.com/512/3067/3067256.png" 
                   alt="Artwork Review" 
                   class="img-fluid"
                   style="max-height: 200px;">
              <h6 class="mt-3 fw-bold" style="color: #2c3e50;">Artwork Review Process</h6>
              <p class="small text-muted">Select appropriate action and add remarks for the artwork review process</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function Reject(mid, id, val) {
    $('#plannerRequestmodalCenter').modal('show');
    $('#request_id').val(id);
  }
  
  $(document).ready(function () {
    $("select[name='request_status']").change(function () {
      var selectedValue = $(this).val();
      var btn = $("#plannerRequestbtn");
      var icon = btn.find('i');
      
      // Remove all icon classes
      icon.removeClass().addClass('fas me-2');
      
      // Set button style and text based on selection
      switch(selectedValue) {
        case "1":
          btn.removeClass('btn-danger btn-warning btn-info btn-secondary')
             .addClass('btn-success')
             .html('<i class="fas fa-check me-2"></i>Approve Action');
          break;
        case "2":
          btn.removeClass('btn-success btn-warning btn-info btn-secondary')
             .addClass('btn-danger')
             .html('<i class="fas fa-times me-2"></i>Reject Artwork');
          break;
        case "3":
          btn.removeClass('btn-success btn-danger btn-info btn-secondary')
             .addClass('btn-warning')
             .html('<i class="fas fa-pause me-2"></i>Place on Hold');
          break;
        case "4":
          btn.removeClass('btn-success btn-danger btn-warning btn-secondary')
             .addClass('btn-info')
             .html('<i class="fas fa-redo me-2"></i>Send for Revision');
          break;
        default:
          btn.removeClass('btn-success btn-danger btn-warning btn-info')
             .addClass('btn-secondary')
             .html('<i class="fas fa-paper-plane me-2"></i>Submit Action');
      }
    });
  });
</script>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">








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

    <style>
      .modal-content {
    background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
    transition: all 0.3s ease-in-out;
}
    </style>

    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script>
         function BDRequestClosed(id,val){
                $('#exampleModalCenterdata').modal('show');
                $('#bd_request_id').val(id);
            }
         function BDApprovedRequestClosed(id,val,bywhom){

          $("#selectTaskPerformByDiv").hide();
          $("#performByProjectCoordinatorDiv").hide();

                if(val == 'Approve'){
                    $('#lm_apr_status option[value="Approve"]').prop('selected', true);
                    $('#lm_apr_status option[value="Reject"]').prop('disabled', true);
                }else if(val == 'Reject'){
                    $('#lm_apr_status option[value="Reject"]').prop('selected', true);
                    $('#lm_apr_status option[value="Approve"]').prop('disabled', true);
                }
                $('#approvedTextMessagetop').text(val);
                $('#approvedTextMessage').text(val);
                $('#bd_request_id_bylm').val(id);
                $('#bd_request_text_message_bylm').val("");
                $('#bd_request_text_message_bylm').val(val);
                if(bywhom == 'LineManager'){
                    $("#selectTaskPerformByDiv").hide();
                    $('#approve_by_admin_types').prop('required', false);
                }else if(bywhom == 'Admin'){
                    $("#selectTaskPerformByDiv").show();
                    $('#approve_by_admin_types').prop('required', true);
                }
                $('#bd_request_apr_status_by_whome').val(bywhom);

                $('#exampleModalCenterdata_ApprovedByLineManager').modal('show');
                
            }

            $('#approve_by_admin_types').on('change', function () {
                let val = $(this).val();
                if(val === "Project Coordinator") {
                    $("#performByProjectCoordinatorDiv").show();
                    $('#perform_by_project_coordinator').prop('required', true);
                } else {
                    $("#performByProjectCoordinatorDiv").hide();
                    $('#perform_by_project_coordinator').prop('required', false);
                }
                console.log(val); 
            });
    </script>
    
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
      </div>
    </footer>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

    <!-- Bootstrap 4 -->
    <script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?=base_url();?>assets/js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?=base_url();?>assets/js/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
    <script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?=base_url();?>assets/js/moment.min.js"></script>
    <script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
    <!-- DataTables  & Plugins -->
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
    <!-- AdminLTE App -->
    <script src="<?=base_url();?>assets/js/adminlte.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?=base_url();?>assets/js/dashboard.js"></script>
    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>