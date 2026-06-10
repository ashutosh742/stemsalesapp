<?php date_default_timezone_set("Asia/Calcutta"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Premium Cash Expense Report | STEM APP</title>
    <!-- Google Fonts: Inter for modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 (modern) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- AdminLTE & Plugins (core structure) -->
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
    <style>
        /* ---------- PREMIUM MODERN REDESIGN ---------- */
        :root {
            --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --accent-gradient: linear-gradient(135deg, #11998e, #38ef7d);
            --danger-gradient: linear-gradient(135deg, #cb2d3e, #ef473a);
            --warning-gradient: linear-gradient(135deg, #f2994a, #f2c94c);
            --card-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.02);
            --hover-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.15);
            --border-radius-xl: 1.5rem;
            --border-radius-lg: 1rem;
            --border-radius-md: 0.75rem;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            letter-spacing: -0.01em;
        }

        /* modern card styling */
        .card-premium {
            background: #ffffff;
            border: none;
            border-radius: var(--border-radius-xl);
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .card-premium:hover {
            box-shadow: var(--hover-shadow);
        }

        .card-header-premium {
            background: var(--primary-gradient);
            padding: 1.25rem 1.8rem;
            border-bottom: none;
        }

        .card-header-premium h2 {
            font-weight: 700;
            letter-spacing: -0.02em;
            font-size: 1.75rem;
        }

        .card-header-premium p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* stats cards */
        .stat-card {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 1.25rem;
            transition: all 0.25s;
            box-shadow: 0 5px 12px rgba(0,0,0,0.03), 0 1px 2px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.03);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -12px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: rgba(0,0,0,0.02);
        }

        /* modern table design */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .modern-table thead th {
            background: #f8fafd;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 1rem 1rem;
            border: none;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table tbody tr {
            background: white;
            border-radius: var(--border-radius-md);
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.03);
        }

        .modern-table tbody tr:hover {
            transform: scale(1.01);
            box-shadow: 0 12px 20px -12px rgba(0, 0, 0, 0.12);
            background: #ffffff;
        }

        .modern-table td {
            background: inherit;
            padding: 1rem 1rem;
            vertical-align: middle;
            border: none;
            border-top: 1px solid #f0f2f5;
            font-size: 0.9rem;
            color: #1e293b;
        }

        /* badge modern */
        .badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.35rem 0.9rem;
            border-radius: 2rem;
            font-weight: 500;
            font-size: 0.7rem;
            letter-spacing: 0.01em;
            background: #f1f5f9;
            color: #1e293b;
        }

        .badge-success {
            background: linear-gradient(135deg, #10b98120, #10b98110);
            color: #047857;
            border-left: 2px solid #10b981;
        }

        .badge-warning {
            background: linear-gradient(135deg, #f59e0b20, #f59e0b10);
            color: #b45309;
            border-left: 2px solid #f59e0b;
        }

        .badge-danger {
            background: linear-gradient(135deg, #ef444420, #ef444410);
            color: #b91c1c;
            border-left: 2px solid #ef4444;
        }

        .badge-info {
            background: linear-gradient(135deg, #3b82f620, #3b82f610);
            color: #1e40af;
            border-left: 2px solid #3b82f6;
        }

        .btn-premium {
            border-radius: 2rem;
            padding: 0.35rem 1.2rem;
            font-weight: 500;
            font-size: 0.75rem;
            transition: all 0.2s;
        }

        .btn-outline-modern {
            border: 1px solid #cbd5e1;
            background: white;
            color: #334155;
        }

        .btn-outline-modern:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        /* bills chip */
        .bill-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            padding: 0.3rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 500;
            color: #334155;
            transition: all 0.2s;
            text-decoration: none;
        }

        .bill-chip i {
            font-size: 0.75rem;
        }

        .bill-chip:hover {
            background: #eef2ff;
            color: #1e40af;
        }

        .status-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .mini-badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.6rem;
            border-radius: 2rem;
            background: #f1f5f9;
        }

        /* modal premium */
        .modal-premium .modal-content {
            border-radius: var(--border-radius-lg);
            border: none;
            box-shadow: 0 30px 50px rgba(0,0,0,0.2);
        }

        .table-responsive-custom {
            border-radius: var(--border-radius-lg);
            overflow-x: auto;
        }

        /* footer */
        .modern-footer {
            border-top: 1px solid #eef2f6;
            background: white;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php $this->load->view($dep_name.'/nav.php'); ?>
    <div class="content-wrapper" style="background: #f5f7fb;">
        <div class="content-header">
            <div class="container-fluid">
                <?php if ($this->session->flashdata('success_message')): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-pill shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $this->session->flashdata('success_message'); ?>
                        <button type="button" class="close" data-dismiss="alert">×</button>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error_message')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-pill shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $this->session->flashdata('error_message'); ?>
                        <button type="button" class="close" data-dismiss="alert">×</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Stats Cards -->
                <?php 
                    $totalExpenses = 0;
                    $totalRequests = count($cashexpenseData);
                    $pendingManager = 0;
                    $approvedManager = 0;
                    foreach($cashexpenseData as $d) {
                        $totalExpenses += (float)$d->expense;
                        if($d->verify == 0) $pendingManager++;
                        if($d->verify == 1) $approvedManager++;
                    }
                ?>


              <?php /*
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-6">
                        <div class="stat-card d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fs-6 fw-semibold" style="font-size:0.7rem;">Total Requests</span>
                                <h3 class="mt-1 mb-0 fw-bold"><?= $totalRequests ?></h3>
                            </div>
                            <div class="stat-icon" style="background:#eef2ff; color:#2563eb;"><i class="fas fa-receipt"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase" style="font-size:0.7rem;">💰 Total Expense</span>
                                <h3 class="mt-1 mb-0 fw-bold">₹ <?= number_format($totalExpenses, 2); ?></h3>
                            </div>
                            <div class="stat-icon" style="background:#ecfdf5; color:#059669;"><i class="fas fa-rupee-sign"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase" style="font-size:0.7rem;">⏳ Pending Manager</span>
                                <h3 class="mt-1 mb-0 fw-bold"><?= $pendingManager ?></h3>
                            </div>
                            <div class="stat-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase" style="font-size:0.7rem;">✅ Approved</span>
                                <h3 class="mt-1 mb-0 fw-bold"><?= $approvedManager ?></h3>
                            </div>
                            <div class="stat-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-check-double"></i></div>
                        </div>
                    </div>
                </div>
                */ ?>










                <!-- Main Card with Table -->
                <div class="card-premium">
                    <div class="card-header-premium text-white">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h2 class="mb-1"><i class="fas fa-coins me-2"></i> Cash Expense Requests</h2>
                                <p class="mb-0">Transparent tracking • Instant approvals • Smart insights</p>
                            </div>
                            <div class="mt-2 mt-md-0">
                                <span class="badge badge-light bg-white text-dark rounded-pill px-3 py-2"><i class="fas fa-chart-line me-1"></i> Real-time</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 p-lg-3">
                        <div class="table-responsive-custom text-nowrap">
                            <table id="exampledata" class="modern-table table w-100">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#️⃣</th>
                                        <th>👤 By User</th>
                                        <th>📅 Meeting Date</th>
                                        <th>🏢 Company</th>
                                        <th>💳 Expense Type</th>
                                        <th>💰 Expense Amount (₹)</th>
                                        <th>📝 Remarks</th>
                                        <th>📎 Bills</th>
                                        <th>⚡ Approval Flow</th>
                                        <th>🛠️ Actions</th>
                                        <th>🔍 Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $j = 1; foreach($cashexpenseData as $data): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= $j ?></td>
                                        <td><?= htmlspecialchars($data->name) ?></td>
                                        <td><?= date('d M Y', strtotime($data->created_at)) ?></td>
                                        <td><?= htmlspecialchars($data->compname) ?></td>
                                        <td><span class="badge-modern"><i class="fas fa-tag"></i> <?= htmlspecialchars($data->expense_type) ?></span></td>
                                        <td class="fw-bold text-dark">₹ <?= number_format($data->expense, 2) ?></td>
                                        <td style="max-width:180px;"><?= htmlspecialchars($data->expense_remarks) ?: '—' ?></td>
                                        <td>
                                            <?php 
                                            $bills = json_decode($data->bills, true);
                                            if(is_array($bills) && count($bills) > 0):
                                                foreach($bills as $bill): 
                                                    $fileUrl = base_url().$bill;
                                                    $ext = pathinfo($fileUrl, PATHINFO_EXTENSION);
                                                    $icon = in_array($ext, ['jpg','jpeg','png','gif']) ? 'fa-image' : ($ext === 'pdf' ? 'fa-file-pdf' : 'fa-file');
                                            ?>
                                                    <a target="_BLANK" href="<?= $fileUrl; ?>" class="bill-chip me-1 mb-1 text-decoration-none">
                                                        <i class="fas <?= $icon ?>"></i> View
                                                    </a>
                                            <?php 
                                                endforeach;
                                            else: echo '<span class="text-muted">—</span>';
                                            endif; 
                                            ?>
                                        </td>



                                        
                                        <!-- Approval Flow: manager + admin + account status -->
                                        <td>
                                            <div class="status-group">
                                                <?php 
                                                    $verifyStatus = $data->verify;
                                                    $adminStatus = $data->admin_apr;
                                                    $accountStatus = $data->account_apr;
                                                ?>
                                                <span class="mini-badge <?= $verifyStatus == 1 ? 'badge-success' : ($verifyStatus == 2 ? 'badge-danger' : 'badge-warning') ?>">
                                                    <i class="fas fa-user-check"></i> M: <?= $verifyStatus == 1 ? 'Approved' : ($verifyStatus == 2 ? 'Rejected' : 'Pending') ?>
                                                </span>
                                                <span class="mini-badge <?= $adminStatus == 1 ? 'badge-success' : ($adminStatus == 2 ? 'badge-danger' : ($adminStatus == 3 ? 'badge-danger' : 'badge-warning')) ?>">
                                                    <i class="fas fa-shield-alt"></i> A: <?= $adminStatus == 1 ? 'Approved' : ($adminStatus == 2 ? 'Rejected' : ($adminStatus == 3 ? 'Susp.' : 'Pending')) ?>
                                                </span>
                                                <span class="mini-badge <?= $accountStatus == 1 ? 'badge-success' : ($accountStatus == 2 ? 'badge-danger' : ($accountStatus == 3 ? 'badge-danger' : 'badge-warning')) ?>">
                                                    <i class="fas fa-credit-card"></i> Acc: <?= $accountStatus == 1 ? 'Approved' : ($accountStatus == 2 ? 'Rejected' : ($accountStatus == 3 ? 'Susp.' : 'Pending')) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <!-- Actions: Approve / Reject (manager only when verify == 0) -->
                                        <td>
                                           
                                            <?php if($data->verify == 0): ?>
                                                 <?php if (in_array($user['type_id'], [13, 4, 15, 24,19,20,21,22,23])) { ?>
                                                <div class="d-flex gap-2">
                                                    <a href="<?= base_url();?>Menu/CashExpenseApproved/<?= $data->id?>" class="btn btn-sm btn-premium btn-success" onclick="return confirm('Approve this request?');">
                                                        <i class="fas fa-check-circle"></i> Approve
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-premium btn-outline-danger" onclick="Reject(<?= $j ?>,<?= $data->id?>,'Reject')">
                                                        <i class="fas fa-times-circle"></i> Reject
                                                    </button>
                                                </div>
                                                <?php }else{ ?> 
                                                    <span class="badge-modern badge-warning"><i class="fas fa-ban"></i> Pending</span>
                                               <?php } ?>
                                            <?php elseif($data->verify == 1): ?>
                                                <span class="badge-modern badge-success"><i class="fas fa-check"></i> Manager Approved</span>
                                            <?php elseif($data->verify == 2): ?>
                                                <span class="badge-modern badge-danger"><i class="fas fa-ban"></i> Rejected</span>
                                            <?php endif; ?>
                                               
                                        </td>

                                    
                                        <td>
                                            <a href="<?= base_url().'Menu/TravelCashExpenseAdvancedDetails/'.$data->id; ?>" class="btn btn-sm btn-outline-modern rounded-pill px-3">
                                                <i class="fas fa-external-link-alt"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $j++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modals (Premium) -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="billModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-premium">
                <div class="modal-header border-0 bg-light rounded-top">
                    <h5 class="modal-title fw-bold" id="billModalLabel"><i class="fas fa-receipt me-2"></i>Bill Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-4">
                    <a href="#" id="viewbillimageatag" target="_blank" class="d-inline-block">
                        <img src="" id="viewbillimage" class="img-fluid rounded shadow-sm" style="max-height: 70vh; display: none;" alt="Bill preview">
                        <iframe src="" id="pdfViewer" class="billesimage w-100" style="height: 70vh; border: none; display: none;"></iframe>
                    </a>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-premium border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title"><i class="fas fa-comment-dots me-2"></i>Rejection Reason</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>
                <form action="<?=base_url();?>Menu/CashExpenseReject" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="rejectid" name="reject" value="">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks *</label>
                            <textarea name="rejectreamrk" id="rejectreamrk" rows="3" class="form-control rounded-3" placeholder="Provide reason for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="main-footer modern-footer">
        <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline-block"><b>Version</b> 2.0 (Premium)</div>
    </footer>
    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<!-- SCRIPTS -->
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
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
    function ViewBills(rid, fileUrl){
        var fileExtension = fileUrl.split('.').pop().toLowerCase();
        $('#viewbillimage').hide(); $('#pdfViewer').hide();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            $('#viewbillimage').attr('src', fileUrl).show();
            $('#viewbillimageatag').attr('href', fileUrl).show();
        } else if (fileExtension === 'pdf') {
            $('#pdfViewer').attr('src', fileUrl).show();
            $('#viewbillimageatag').attr('href', fileUrl).show();
        } else {
            alert('Unsupported file type');
        }
        $('#exampleModalCenter').modal('show');
    }
    
    function Reject(mid, id, val){
        $('#rejectid').val(id);
        $('#exampleModalCenterdata').modal('show');
    }

    $(document).ready(function(){
        $("#exampledata").DataTable({
            responsive: false, 
            lengthChange: false, 
            autoWidth: false,
            buttons: ["excel", "pdf"],
            dom: '<"d-flex justify-content-between align-items-center mb-3"B><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { search: "Filter:", lengthMenu: "Show _MENU_ entries" }
        }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');
    });
</script>
</body>
</html>