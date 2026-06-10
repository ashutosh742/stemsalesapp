<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STEM APP | BD WISE Master Reset | WebAPP</title>
    <!-- Google Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE & Premium Bootstrap -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    <!-- Toastr (modern notifications) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Premium gradient backgrounds */
        .bg-premium-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.12), 0 10px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
        }
        .card-header {
            border-radius: 20px 20px 0 0 !important;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: none;
        }
        .card-header .card-title {
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }
        .card-header .card-tools .btn-tool {
            color: white;
            opacity: 0.9;
        }
        .card-header .card-tools .btn-tool:hover {
            opacity: 1;
        }
        .card-body {
            padding: 1.5rem;
            background: #f8fafd;
            border-radius: 0 0 20px 20px;
        }
        /* Modern table styling */
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #495057;
            background-color: #f1f4f8;
        }
        .table tbody tr {
            transition: background 0.2s;
        }
        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        .btn-reset {
            background: linear-gradient(135deg, #ff6f61, #ff9068);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(255, 111, 97, 0.3);
            transition: all 0.3s;
        }
        .btn-reset:hover {
            background: linear-gradient(135deg, #ff9068, #ff6f61);
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(255, 111, 97, 0.4);
        }
        .btn-reset:disabled {
            opacity: 0.6;
            filter: grayscale(0.3);
            transform: none;
        }
        .progress-inline {
            width: 100px;
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
        }
        .progress-inline .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            border-radius: 10px;
            width: 0%;
            transition: width 0.3s ease;
        }
        .progress-inline .progress-count {
            position: absolute;
            top: -18px;
            right: 0;
            font-size: 10px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            line-height: 1;
            white-space: nowrap;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .card-header .card-title {
                font-size: 1.1rem;
            }
        }
           tr,td{
            font-weight: bold;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <?php $this->load->view($dep_name.'/nav.php');?>
    <!-- /.navbar -->
<hr>
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

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

                <!-- CSRF Token for CI3 -->
                <?php if($this->config->item('csrf_protection')): ?>
                    <meta name="csrf-token-name" content="<?=$this->security->get_csrf_token_name();?>">
                    <meta name="csrf-token-value" content="<?=$this->security->get_csrf_hash();?>">
                <?php endif; ?>

                <!-- BD Wise Table Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i> BD Wise Summary</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                             <div class="row">
                                <div class="col-12 mt-4">
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Warning!</strong> These reset actions are <strong>irreversible</strong>. Please ensure you have a complete backup before proceeding.
                                    </div>
                                </div>
                            </div>
                        <table id="bdWiseTable" class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>BD Name</th>
                                    <th>Department Name</th>
                                    <th>Total Funnel</th>
                                    <th>Funnel Current Status</th>
                                    <th>Funnel Reset Status</th>
                                    <th style="width: 180px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($funnelDatas['bd_wise']) && is_array($funnelDatas['bd_wise'])): ?>
                                    <?php $k=1; foreach($funnelDatas['bd_wise'] as $bd):
                                        
                                        $r = rand(150, 255);
                                        $g = rand(150, 255);
                                        $b = rand(150, 255);
                                        $backgroundColor = "rgba($r, $g, $b,.2)";
                                        $hue        = rand(0, 360); // Full color wheel
                                        $saturation = rand(60, 100); // High saturation for rich colors
                                        $lightness  = rand(30, 45); // Low lightness for a deeper tone
                                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                        
                                        ?>
                                        <?php if(empty($bd)) continue; ?>
                                        <tr id="bd-row-<?=$bd->user_id?>" style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                                            <td><?= $k; ?></td>
                                            <td><?=htmlspecialchars($bd->bd_name)?></td>
                                            <td><?=htmlspecialchars($bd->department_name)?></td>
                                            <td class="total-funnel"><?=$bd->total?></td>
                                            <td>
                                                <?php 
                                                $funnelStatusDatas = $this->MasterReset_model->GetBDFunnelsStatusDatasByBDID($bd->user_id);
                                                $badgeColors = ['primary','success','danger','warning','info','secondary','dark'];
                                                $i = 0;
                                                foreach($funnelStatusDatas as $funnelStatusData):
                                                    $color = $badgeColors[$i % count($badgeColors)];
                                                ?>
                                                    <span class="badge bg-<?= $color ?> me-1 p-2 m-1">
                                                        <?= $funnelStatusData->status_name ?> (<?= $funnelStatusData->total ?>)
                                                    </span>
                                                <?php 
                                                    $i++;
                                                endforeach;
                                                ?>
                                                </td>
                                                <td class="total-funnel">
                                                <?php 
                                                if($bd->annual_reset_funnel_status == 1){
                                                    echo '<span class="badge bg-success me-1 p-2 m-1">Completed - '.$bd->annual_reset_fyear.' - '.$bd->annual_reset_created_at.'</span>';
                                                }else{
                                                    echo '<span class="badge bg-danger me-1 p-2 m-1">Pending</span>';
                                                }
                                                ?>
                                                </td>
                                            <td>
                                            <?php if(empty($bd->annual_reset_funnel_status)){ ?>  
                                                <button class="btn btn-sm btn-reset" 
                                                        data-type="bd" 
                                                        data-id="<?=$bd->user_id?>" 
                                                        data-url="<?=base_url('MasterReset/RestFunnel')?>">
                                                    <i class="fas fa-sync-alt"></i> Reset
                                                </button>
                                                <div class="progress-inline d-none" id="progress-bd-<?=$bd->user_id?>">
                                                    <div class="progress-bar" style="width:0%"></div>
                                                    <span class="progress-count">0/0</span>
                                                </div>
                                            <?php }else{ ?> <span class="badge bg-success me-1 p-2 m-1">Completed </span> <?php } ?> 
                                            </td>
                                        </tr>
                                    <?php $k++; endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline-block"><b>Version</b> 1.0</div>
    </footer>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
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
<!-- AdminLTE -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Moment (for datetime) -->
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>

<!-- Custom AJAX & DataTables Initialization -->
<script>
$(document).ready(function() {
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Initialize DataTables with export buttons
    var bdTable = $('#bdWiseTable').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        dom: 'Bfrtip',
        pageLength: 500,
        order: [[0, 'asc']]
    });
    bdTable.buttons().container().appendTo('#bdWiseTable_wrapper .col-md-6:eq(0)');

    // Reset button click handler with confirmation
    $('.btn-reset').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var type = $btn.data('type');
        var id = $btn.data('id');
        var url = $btn.data('url');

        // Determine display name
        var typeDisplay = (type === 'zone') ? 'Zone' : 'BD';

        // Show confirmation
        if (!confirm('Are you sure you want to reset the funnel for this ' + typeDisplay + '? This action cannot be undone.')) {
            return;
        }

        var $progress = $('#progress-' + type + '-' + id);
        var $row = $btn.closest('tr');
        var totalFunnel = parseInt($row.find('.total-funnel').text()) || 0; // Get current total from table

        // Disable button, show spinner, show progress bar with initial count
        $btn.prop('disabled', true);
        $btn.find('i').removeClass('fa-sync-alt').addClass('fa-spinner fa-spin');
        $progress.removeClass('d-none');
        $progress.find('.progress-bar').css('width', '0%');
        $progress.find('.progress-count').text('0/' + totalFunnel);

        // Prepare CSRF data if enabled
        var csrfName = $('meta[name="csrf-token-name"]').attr('content');
        var csrfValue = $('meta[name="csrf-token-value"]').attr('content');
        var postData = {
            type: type,
            id: id
        };
        if (csrfName && csrfValue) {
            postData[csrfName] = csrfValue;
        }

        // AJAX POST to reset endpoint
        $.ajax({
            url: url,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update progress bar to show completed count
                    var processed = response.processed_count || totalFunnel;
                    $progress.find('.progress-bar').css('width', '100%');
                    $progress.find('.progress-count').text(processed + '/' + (response.total_count || totalFunnel));

                    toastr.success(response.message || 'Reset successful');

                    // Refresh page after a short delay to show updated data
                    setTimeout(function() {
                        location.reload();
                    }, 1500);

                } else {
                    toastr.error(response.message || 'Reset failed');
                    resetButtonState($btn, $progress);
                }
            },
            error: function(xhr) {
                var msg = 'Reset failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
                resetButtonState($btn, $progress);
            }
        });
    });

    function resetButtonState($btn, $progress) {
        $btn.prop('disabled', false);
        $btn.find('i').removeClass('fa-spinner fa-spin').addClass('fa-sync-alt');
        $progress.addClass('d-none');
        $progress.find('.progress-bar').css('width', '0%');
        $progress.find('.progress-count').text('0/0');
    }
});
</script>
</body>
</html>