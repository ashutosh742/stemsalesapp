
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
<title>STEM APP | WebAPP</title>
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/icheck-bootstrap.min.css">
<!-- JQVMap -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/jqvmap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/adminlte.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/OverlayScrollbars.min.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/daterangepicker.css">
<!-- summernote -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/summernote-bs4.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/buttons.bootstrap4.min.css">
<style>
.scrollme {
overflow-x: auto;
}
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<!-- Preloader -->

<!-- Navbar -->
<?php require ('nav.php'); ?>
<!-- /.navbar -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
</div><!-- /.col -->
<div class="col-sm-6">
<ol class="breadcrumb float-sm-right">
<h4></h4>
</ol>
</div><!-- /.col -->
</div><!-- /.row -->
</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<form class="p-3" method="POST" action="<?=base_url(); ?>/Menu/MeetingDetail/1/<?=$uid ?>">
    <input type="date" name="sdate" class="mr-2" value="<?=$sd ?>">
    <input type="date" name="edate" class="mr-2" value="<?=$ed ?>">
    <button type="submit" class="bg-primary text-light">Filter</button>
</form>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
    <div class="row">
    <div class="col-12">
    <div class="card">
    <div class="card-header">
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    <div class="container-fluid body-content">
    <div class="page-header">
    <fieldset>

    <div class="table-responsive">
    <div class="table-responsive">
    <div class="pdf-viwer">
    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>S.No.</th>
            <th>BD Name</th>
            <th>Total meeting</th>
            <th>Total RP</th>
            <th>Fresh Meeting</th>
            <th>Total Re-meeting</th>
            <th>Total Barge Meeting</th>
            <th>Total Schedule Meeting</th>
            <th>Total Join Meeting</th>
            <th>Total Potential Meeting</th>
            <th>Total Meetings with Top Spender</th>
            <th>Total Business Potential Companies</th>
            <th>MOM pending in app</th>
            <th>MOM Not Approved By CM</th>
            <th>Remarks</th>
            <th>Meeting Conversion Ratio</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = 1;
    $trpm = 0;
    $tfmeet = 0;
    $trmeet = 0;
    $ttt = 0;
    $tnorpm = 0;
    $startTimeStamp = strtotime($sd);
    $endTimeStamp = strtotime($ed);
    $timeDiff = abs($endTimeStamp - $startTimeStamp);
    $numberDays = $timeDiff / 86400;
    $numberDays = intval($numberDays);
    $bd = $this->Menu_model->get_userbyaid($uid);
    //echo "<pre>";print_r($bd);exit;
    foreach ($bd as $bd) {
        $bdname = $bd->name;
        $bdid = $bd->user_id;
        $mdata = $this->Menu_model->get_tbmd('1', $bdid, $bdid, $sd, $ed);
        // echo $this->db->last_query();die;
        $tt = 0;
        $trp = 0;
        $tfm = 0;
        $trm = 0;
        $pmom = 0;
        $fmeet = 0;
        $rmeet = 0;
        $ymom = 0;
        $nmom = 0;
        $rpm = 0;
        $norps = 0;
        $bargmeeting = 0;
        $sheduledMeeting = 0;
        $joinMeeting = 0;
        $potentialMeeting = 0;
        $topspenderMeeting = 0;$norapprovedmom=0;
        $barg = count($this->Menu_model->get_all_bd_bargemeeting_data_by_user($uid,$sd,$ed,$bdid));
        $sheduled = count($this->Menu_model->get_all_bd_schedulemeeting_data_by_user($uid,$sd,$ed,$bdid));
        $joinMeetings = count($this->Menu_model->get_all_bd_join_meetings_data_by_user($uid,$sd,$ed,$bdid));
        $potentialMeetings = count($this->Menu_model->get_all_bd_Potential_Meeting_data_by_user($uid,$sd,$ed,$bdid));
        $topspenderMeetings = count($this->Menu_model->get_all_bd_TopSpender_meeting_data_by_user($uid,$sd,$ed,$bdid));
        $norapprovedmom = count($this->Menu_model->getMomNotApprovedData($uid,$sd,$ed,$bdid));
        $buisenessPotential = count($this->Menu_model->getBuisenessPotentialData($uid,$sd,$ed,$bdid));

        foreach ($mdata as $dt) {
           
            // var_dump($dt);die;
            $tt++;
            $cmpid = $dt->cmpid;
            $cid = $dt->cid;
            $tid = $dt->tid;
            $rp = $this->Menu_model->get_checkrpbytid($tid);
            $norp = $this->Menu_model->get_checkNorpbytid($tid);
            
            if ($rp) {
                $comp = $this->Menu_model->get_bargdetailcid($cmpid);
                $cmprp = $this->Menu_model->get_meetfr($tid, $cid);
                $cmrp = $cmprp[0]->cont;
                $comp = sizeof($comp);
               $momc = $this->Menu_model->get_momyn($cid, $tid);
            //   $rpm++;
               // $trpm + $rpm
               // $momc = count($this->Menu_model->get_momyn($cid, $tid));
// echo $this->db->last_query()."<br/>";
            // echo "==momc"; 
            // print_r($momc);
                // echo $momc;
                if ($momc) {
                    $momc = 'yes';
                    $ymom++;
                    $rpm++;
                    if ($cmrp == 0) {
                        $fmeet++;
                    }
                    if ($cmrp > 0) {
                        $rmeet++;
                    }
                } else {
                    $momc = 'no';
                    $pmom++;
 		/* if($bdid == '100190'){
                        echo $cid."==".$tid."==";
                        echo "<br>";
                        echo $dt->nextCFID;
                        echo "<br>";
                     } */
		if($cid != 0){
                        $nmom++;
                    }
                   // $nmom++;
                }

            }

            if (!empty($norp)) {
                $norpm++;
            }
            if ($numberDays < 6) {
                if ($fmeet == 1 * $numberDays) {
                    $remark = 'Bad performance';
                    $color = 'text-danger';
                } elseif ($fmeet <= $numberDays) {
                    $remark = 'Average Performance';
                    $color = 'text-warning';
                } else {
                    $remark = 'Good Performance';
                    $color = 'text-success';
                }
            }
        }
        if ($numberDays > 5 && $numberDays < 8) {
            if ($fmeet < 3) {
                $remark = 'Bad performance';
                $color = 'text-danger';
            } elseif ($fmeet < 5) {
                $remark = 'Average Performance';
                $color = 'text-warning';
            } else {
                $remark = 'Good Performance';
                $color = 'text-success';
            }
        }
        if ($numberDays > 20 && $numberDays < 32) {
            if ($fmeet < 13) {
                $remark = 'Bad performance';
                $color = 'text-danger';
            } elseif ($fmeet < 20) {
                $remark = 'Average Performance';
                $color = 'text-warning';
            } else {
                $remark = 'Good Performance';
                $color = 'text-success';
            }
        }
        // if($numberDays>20 && $numberDays<32){
        //     if($fmeet<13){$remark='Bad performance';$color = 'text-danger';}elseif($fmeet<20){$remark='Average Performance';$color = 'text-warning';}else{$remark='Good Performance';$color = 'text-success';}
        // }
        if ($tt > 0) {
            $a = ($fmeet / $tt) * 100;
            $ratio = sprintf("%.2f", $a) . ' %';
            $total_meeting_data_per_bd = $this->Menu_model->get_tbmd(4, $bdid, $bdid, $sd, $ed);
            //  echo count($total_meeting_data_per_bd);
            
    ?>
                                        
                                        
                                    <tr>
                                        <td><?=$i ?></td>
                                        <td><?=$bdname ?></td>
                                        <td><a href='<?=base_url(); ?>/Menu/TBMD/4/<?=$bdid ?>/<?=$sd ?>/<?=$ed ?>'><?=$tt; ?> <?php $ttt = $ttt + $tt; ?></a></td>
                                        <td><a href='<?=base_url(); ?>/Menu/TBMDFRP/5/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'><?=$rpm ?> <?php $trpm = $trpm + $rpm; ?></a></td>       
                                        <td><a href='<?=base_url(); ?>/Menu/TBMDFRP/10/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'><?=$fmeet ?> <?php $tfmeet = $tfmeet + $fmeet; ?></a></td>
                                        <td><a href='<?=base_url(); ?>/Menu/TBMDFRP/11/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'><?=$rmeet ?> <?php $trmeet = $trmeet + $rmeet; ?></a></td>
                                        <td>
                                            <a href='<?=base_url(); ?>/Menu/Meetings/bargMeeting/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'>
                                                <?= $barg; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href='<?=base_url(); ?>/Menu/Meetings/sheduleMeeting/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'>
                                                <?= $sheduled; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href='<?=base_url(); ?>/Menu/Meetings/joinMeeting/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'>
                                                <?= $joinMeetings; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href='<?=base_url(); ?>/Menu/Meetings/potentialMeeting/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'>
                                                <?= $potentialMeetings; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href='<?=base_url(); ?>/Menu/Meetings/topSpenderMeeting/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'>
                                                <?= $topspenderMeetings; ?>
                                            </a>
                                        </td>
 					<td>
                                            <a href='<?=base_url(); ?>Menu/total_Buiseness_potential_Company/<?=$uid ?>/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'>
                                                <?= $buisenessPotential; ?>
                                            </a>
                                        </td>  
                                        <td><a href='<?=base_url(); ?>/Menu/TBMDFRP/12/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'><?=$nmom ?></a></td>
                                        <td><a href='<?=base_url(); ?>/Menu/MomNotApproved/<?=$uid ?>/<?=$sd ?>/<?=$ed ?>/<?=$bdid ?>'><?=$norapprovedmom ?></a></td>


                                        <td class="<?=$color ?>"><?=$remark ?></td>
                                        <td><?=$ratio ?></td>
                                    </tr>
                                    <?php $i++;
        }
    } ?>
                                </tbody>
                            </table>
    
                    <?php
    $fresh_rp_meetings = $this->Menu_model->get_all_bd_Fresh_RP_meetings($uid, $sd, $ed);
    $fresh_rp_meetings_count = count($fresh_rp_meetings);
    $total_team_meeeting = $this->Menu_model->get_all_bd_meetings($uid, $sd, $ed);
    $mdata = $this->Menu_model->get_all_bd_No_RP_meetings($uid, $sd, $ed);
    $tnorpm = count($mdata);
    $total_bargemeeting                         = $this->Menu_model->get_all_bd_bargemeeting_data($uid,$sd,$ed);
    $total_schedulemeeting                      = $this->Menu_model->get_all_bd_schedulemeeting_data($uid,$sd,$ed);
    $total_join_meeting                         = $this->Menu_model->get_all_bd_join_meetings_data($uid,$sd,$ed);
    $total_potential_meeting                    = $this->Menu_model->get_all_bd_Potential_meeting_data($uid,$sd,$ed);
    $total_topspender_meeting                   = $this->Menu_model->get_all_bd_TopSpender_meeting_data($uid,$sd,$ed);
    $total_meeting_only_got_details                    = $this->Menu_model->get_only_got_details_meeting_data($uid,$sd,$ed);
    $total_reject_mom                   = $this->Menu_model->get_reject_mom_data($uid,$sd,$ed);

    ?>                  
                        <b>Total Meeting : <a href="<?=base_url();?>/Menu/Total_Team_Meetings/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?=$ttt;?></a> </b><br>
                        <b>Total RP Meeting : <a href="<?=base_url();?>/Menu/Total_Team_RP_Meeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?=$trpm?></a></b><br>
                        <b>Total No RP Meeting : <a href="<?=base_url();?>/Menu/Total_Team_No_RP_Meeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo $tnorpm;?></a></b><br>
                        <!-- <b>Total No RP But Got Details :<a href="<?=base_url();?>/Menu/Total_Team_No_RP_Meeting_Got_Details/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_all_bd_meetings_No_RP_Got_Details);?></a></b><br> -->
                        <b>Total Fresh : <a href="<?=base_url();?>/Menu/Total_Team_Fresh_Meeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo $tfmeet;?></a></b><br>
                        <b>Total Re-meeting : <a href="<?=base_url();?>/Menu/Total_Team_Re_Meeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?=$trmeet?></a></b><br>
                        <b>Total Barge Meeting : <a href="<?=base_url();?>/Menu/Total_Team_BargeMeeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_bargemeeting);?></a></b><br>
                        <b>Total Schedule Meeting : <a href="<?=base_url();?>/Menu/Total_Team_ScheduleMeeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_schedulemeeting);?></a></b><br>
                        <b>Total Join Meeting : <a href="<?=base_url();?>/Menu/Total_Team_Join_Meeting_data/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_join_meeting);?></a></b><br>
                        <b>Total Potential Meeting : <a href="<?=base_url();?>/Menu/Total_Team_Potential_Meeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_potential_meeting);?></a></b><br>
                        <b>Total Meetings with Top Spender : <a href="<?=base_url();?>/Menu/Total_Team_TopSpender_Meeting/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_topspender_meeting);?></a></b><br>
			<b>Total No RP Meeting with Only Got Details : <a href="<?=base_url();?>/Menu/Total_Meeting_Only_Got_Details/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_meeting_only_got_details);?></a></b><br>
                        <b>Total Rejected MOM  : <a href="<?=base_url();?>/Menu/Total_reject_mom_data/<?=$uid ?>/<?=$sd?>/<?=$ed?>"><?php echo count($total_reject_mom);?></a></b><br>
                   

                    </div>
                </div>
    </form>            <!--END OF FORM ^^-->
    </fieldset>

    </div>
    </div></div></div></div>
    </div>
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


<footer class="main-footer">
<strong>Copyright &copy; 2021-2022 <a href="<?=base_url(); ?>">Stemlearning</a>.</strong>
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
<script src="<?=base_url(); ?>assets/js/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=base_url(); ?>assets/js/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?=base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?=base_url(); ?>assets/js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?=base_url(); ?>assets/js/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?=base_url(); ?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url(); ?>assets/js/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?=base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?=base_url(); ?>assets/js/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?=base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?=base_url(); ?>assets/js/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=base_url(); ?>assets/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="<?=base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url(); ?>assets/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/jszip.min.js"></script>
<script src="<?=base_url(); ?>assets/js/pdfmake.min.js"></script>
<script src="<?=base_url(); ?>assets/js/vfs_fonts.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.html5.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.print.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url(); ?>assets/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url(); ?>assets/js/dashboard.js"></script>
<script>
$("#example1").DataTable({
"responsive": false, "lengthChange": false, "autoWidth": false,
"buttons": ["csv", "excel", "pdf","colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
