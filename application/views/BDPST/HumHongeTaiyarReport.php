<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM Operation | WebAPP</title>
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
  
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.css">

  <style>
      body {
          padding: 1rem;
        }
        #contentcard {position:fixed;
        margin-right:1rem;}
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  

  <!-- Navbar -->
  <?php require('nav.php');?>
  <!-- /.navbar -->

  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="faq-tab" data-toggle="tab" href="#faq" role="tab" aria-controls="faq" aria-selected="true">FAQ</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="salesppt-tab" data-toggle="tab" href="#salesppt" role="tab" aria-controls="salesppt" aria-selected="false">Corporate PPT</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="salespitch-tab" data-toggle="tab" href="#salespitch" role="tab" aria-controls="salespitch" aria-selected="false">Sales Pitch</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="presentation-tab" data-toggle="tab" href="#presentation" role="tab" aria-controls="presentation" aria-selected="false">Presentation / Meeting Conduct</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="proposal-tab" data-toggle="tab" href="#proposal" role="tab" aria-controls="proposal" aria-selected="false">Proposal Of Unique</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email Draft</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">9 Whats App</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="regional-tab" data-toggle="tab" href="#regional" role="tab" aria-controls="regional" aria-selected="false">Regional Case Study</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="quarter-tab" data-toggle="tab" href="#quarter" role="tab" aria-controls="quarter" aria-selected="false">Quarter 1 PPT</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="annual-tab" data-toggle="tab" href="#annual" role="tab" aria-controls="annual" aria-selected="false">Annual Review</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="district-tab" data-toggle="tab" href="#district" role="tab" aria-controls="district" aria-selected="false">District Permission Letter</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="school-tab" data-toggle="tab" href="#school" role="tab" aria-controls="school" aria-selected="false">Add School Details</a>
                  </li>
                </ul>
            </div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->




<!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            
              <?php if ($this->session->flashdata('success_message')): ?>
          <div class="container-fluid">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong> 
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        <?php endif; ?>
            
            <div class="row p-3">
                <div class="col-sm col-md-12 col-lg-12">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="faq" role="tabpanel" aria-labelledby="faq-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample22" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample22">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th>  Approved</th>
                                          <th>  Rejected</th>
                                          <th>  Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'faq');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our FAQ Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>FAQ Question</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $faqcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "faq"){
                                                              $faqcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->question?></td>
                                                        
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - FAQ Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Question</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'faq');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$a->question?></td>
                                          
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <!--<p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>-->
                                                          <p><button type="button" class="btn btn-success"  onclick="ApproveFaq(<?= $j ?>,<?= $a->id?>,'Reject')">Approve</button></p>
                                                            <p><button type="button" class="btn btn-primary"  onclick="Reject(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            <?php $k++;}?>
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdata<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>  
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataApproveFaq<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Approve Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/ApproveResourceFaq" method="post" >
                                                <input type="hidden" id="approveid" value="" name="reject">
                                                <input type="hidden" value="Approve By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                        </div>
                    
                    
                    
                        <div class="tab-pane fade" id="salesppt" role="tabpanel" aria-labelledby="salesppt-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample23" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample23">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th> Approved</th>
                                          <th> Rejected</th>
                                          <th> Pending</th>
                                          
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'salesppt');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Corporate PPT Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Partner Type</th>
                                                      <th>What changes do you suggest in the PPT?</th>
                                                      <th>Please Mention The Slide Number</th>
                                                      <th>Reference</th>
                                                      <th>Download PPT</th>
                                                      <!--<th>Is Self Approved</th>-->
                                                      <!--<th>Actions</th>-->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $salespptcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "salesppt"){
                                                              $salespptcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$a->slideno?></td>
                                                        <td><?=$a->reference?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        <!--<td>-->
                                                         <?php //if($user['type_id'] == 13){
                                                        //     echo $a->cluster_approve;
                                                        // }else if($user['type_id'] == 4){
                                                        //     echo $a->pst_approve;
                                                        // }
                                                        ?>
                                                        <!--</td>-->
                                                        <!--<td>-->
                                                        <!--    <div>-->
                                                        <!--      <?php //if($revTarget->cluster_approve == ''){ ?>-->
                                                        <!--      <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>-->
                                                        <!--      <p><a href="javascript:void(0)" onclick="RejectButtonCorporate(<?= $j ?>,<?= $a->id?>,'Reject')" class="btn btn-danger">Reject</a></p>-->
                                                        <!--        <?php //}else{?>-->
                                                        <!--        <span class="p-2 bg-success mr-2">Done</span>-->
                                                        <!--        <?php //} ?>-->
                                                        <!--    </div>-->
                                                        <!-- </td>-->
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - Crporate PPT Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Partner Type</th>
                                                      <th>What changes do you suggest in the PPT?</th>
                                                      <th>Please Mention The Slide Number</th>
                                                      <th>Download PPT</th>
                                                      <th>Reference</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'salesppt');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$a->slideno?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        <td><?=$a->reference?></td>
                                          
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectCorporate(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            <?php $k++;}?>
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataCorporate<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                        </div>
                        
                        <div class="tab-pane fade" id="salespitch" role="tabpanel" aria-labelledby="salespitch-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample24" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample24">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th> Approved</th>
                                          <th> Rejected</th>
                                          <th> Pending</th>
                                          
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'salespitch');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Sales Pitch Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Partner Type</th>
                                                      <th>Pitch</th>
                                                      <th>Voice Note</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $salespitchcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "salespitch"){
                                                              $salespitchcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        <td>
                                                            <div>
                                                              <?php if($revTarget->cluster_approve == ''){ ?>
                                                              <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                              <p><a href="javascript:void(0)" onclick="RejectButton(<?= $j ?>,<?= $a->id?>,'Reject')" class="btn btn-danger">Reject</a></p>
                                                                <?php }else{?>
                                                                <span class="p-2 bg-success mr-2">Done</span>
                                                                <?php } ?>
                                                            </div>
                                                         </td>
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - Crporate PPT Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Partner Type</th>
                                                      <th>message</th>
                                                      <th>Voice Note</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'salespitch');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="Rejectsalespitch(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdatasalespitch<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                            
                        </div>
                        
                        
                        <div class="tab-pane fade" id="presentation" role="tabpanel" aria-labelledby="presentation-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample26" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample26">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th>Approved</th>
                                          <th>Rejected</th>
                                          <th>Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'presentation');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Presentation Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>What changes do you suggest in the Presentation?</th>
                                                      <th>Presentation Product Type</th>
                                                      <th>Reference</th>
                                                      <th>Play Role Video</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $presentationcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "presentation"){
                                                              $salespitchcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$a->presentationtype?></td>
                                                        <td><a href="<?=$a->reference?>"><?=$a->reference?></a></td>
                                                        <td><?php if($user['type_id'] == 13){
                                                            echo $a->cluster_approve;
                                                        }else if($user['type_id'] == 4){
                                                            echo $a->pst_approve;
                                                        }
                                                        ?>
                                                        </td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); 
                                        break; 
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> -  Presentation Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>What changes do you suggest in the Presentation?</th>
                                                      <th>Presentation Product Type</th>
                                                      <th>Reference</th>
                                                      <th>Play Role Video</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'presentation');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$a->pointscover?></td>
                                                        <td><?=$a->presentationtype?></td>
                                                        <td><a href="<?=$a->reference?>"><?=$a->reference?></a></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="Rejectpresentation(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            <?php $k++;}?>
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdatapresentation<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                        </div>
                        
                        
                        <div class="tab-pane fade" id="proposal" role="tabpanel" aria-labelledby="proposal-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample27" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample27">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th>Approved</th>
                                          <th>Rejected</th>
                                          <th>Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'proposal');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Proposal Of Unique Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Proposal Name</th>
                                                      <th>Download Proposal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $proposalcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "proposal"){
                                                              $proposalcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->proposalname?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - Proposal Of Unique Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Proposal Name</th>
                                                      <th>Proposal Link</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'proposal');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$a->proposalname?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectProposal(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataProposal<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                    
                        
                        <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample28" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample28">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th>Approved</th>
                                          <th>Rejected</th>
                                          <th>Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'email');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Email Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Email Draft</th>
                                                      <th>Download Draft Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $emailcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "email"){
                                                              $emailcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - Email Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>message</th>
                                                      <th>Download Email Draft</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'email');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                        <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectEmail(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataemail<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                        
                        <div class="tab-pane fade" id="whatsapp" role="tabpanel" aria-labelledby="whatsapp-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample29" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample29">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th>Approved</th>
                                          <th>Rejected</th>
                                          <th>Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'whatsapp');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our whatsapp Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Whats App Message Draft</th>
                                                      <th>Download Draft</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $whatsappcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "whatsapp"){
                                                              $whatsappcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> -  Whatsapp Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Whats App Message Draft</th>
                                                      <th>Download Draft</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'whatsapp');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                        <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectWhatsapp(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataWhatsapp<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                        
                        <div class="tab-pane fade" id="regional" role="tabpanel" aria-labelledby="regional-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample30" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample30">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th> Approved</th>
                                          <th> Rejected</th>
                                          <th> Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'regional-case-study');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Regional Case Study Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Regional Case Study</th>
                                                      <th>Download Case Study</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $regionalcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "regional-case-study"){
                                                              $regionalcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - Regional Case Study Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Regional Case Study</th>
                                                      <th>Download Case Study</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'regional-case-study');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$a->message?></td>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectRegional(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataRegional<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                        
                        <div class="tab-pane fade" id="quarter" role="tabpanel" aria-labelledby="quarter-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample31" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample31">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th> Approved</th>
                                          <th> Rejected</th>
                                          <th> Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'quarter1-ppt');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Quarter 1 PPT Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>PPT</th>
                                                      <th>Slide No</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $quartercount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "quarter1-ppt"){
                                                              $quartercount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><a href="<?=base_url();?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        <td><?=$a->slideno?></td>
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - Quarter 1 PPT Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>PPT</th>
                                                      <th>Slide No</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'quarter1-ppt');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><a href="<?=base_url();?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        <td><?=$a->slideno?></td>
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectQuarter(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataQuarter<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                        
                        <div class="tab-pane fade" id="annual" role="tabpanel" aria-labelledby="annual-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample32" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample32">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th> Approved</th>
                                          <th> Rejected</th>
                                          <th> Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'annualreview');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our Annual Review Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Points Cover</th>
                                                      <th>Partner Type</th>
                                                      <th>Reference</th>
                                                      <th>Slide No</th>
                                                      <th>Download PPT</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $annualreviewcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "annualreview"){
                                                              $annualreviewcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><a href="<?=$a->reference?>"><?=$a->reference?></a></td>
                                                        <td><?=$a->slideno?></td>
                                                        <td><a href="<?=base_url();?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        <td>
                                                            <div>
                                                              <?php if($revTarget->cluster_approve == ''){ ?>
                                                              <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                              <p><a href="javascript:void(0)" onclick="RejectButton(<?= $j ?>,<?= $a->id?>,'Reject')" class="btn btn-danger">Reject</a></p>
                                                                <?php }else{?>
                                                                <span class="p-2 bg-success mr-2">Done</span>
                                                                <?php } ?>
                                                            </div>
                                                         </td>
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - Annual Review Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Points Cover</th>
                                                      <th>Partner Type</th>
                                                      <th>Reference</th>
                                                      <th>Slide No</th>
                                                      <th>Download PPT</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'annualreview');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><a href="<?=$a->reference?>"><?=$a->reference?></a></td>
                                                        <td><?=$a->slideno?></td>
                                                        <td><a href="<?=base_url();?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                          
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="Rejectannualreview(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataannualreview<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                        
                        <div class="tab-pane fade" id="district" role="tabpanel" aria-labelledby="district-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample33" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample33">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th> Approved</th>
                                          <th> Rejected</th>
                                          <th> Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'district-permission');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our District Permission Letter Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Department Details</th>
                                                      <th>Department Type</th>
                                                      <th>Permission Letter</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $districtcount = 0;
                                                      foreach($revDatatar as $a){
                                                          if($a->type == "district-permission"){
                                                              $districtcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->deptdetails?></td>
                                                        <td><?=$a->depttype?></td>
                                                        <td><a href="<?=base_url();?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                        
                                                    </tr>
                                                    <?php  $j++; }}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - District Permission Letter Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Department Details</th>
                                                      <th>Department Type</th>
                                                      <th>Permission Letter</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'district-permission');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$a->deptdetails?></td>
                                                        <td><?=$a->depttype?></td>
                                                        <td><a href="<?=base_url();?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                          
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectDistrict(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdatadistrict<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                        
                        <div class="tab-pane fade" id="school" role="tabpanel" aria-labelledby="school-tab">
                            <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample33" aria-expanded="false" aria-controls="collapseExample">
                                Summary Of Report
                            </button>
                          <div class="collapse" id="collapseExample33">
                              <div class="card card-body">
                                  <?php $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                //   var_dump($getbdteam); exit;
                                  ?>
                                  <div class="table-responsive">
                                    <table id="ourreview" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                        <tr>
                                          <th>sr. no</th>
                                          <th>User Name</th>
                                          <th>Total Data</th>
                                          <th> Approved</th>
                                          <th> Rejected</th>
                                          <th> Pending</th>
                                          <th> Admin Approved</th>
                                          <th> Admin Rejected</th>
                                          <th> Admin Pending</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $j=1;
                                            foreach($getbdteam as $t){
                                            $udata = $this->Menu_model->getResourceDetailsByUser($t->user_id,'school');
                                            // echo "<pre>";
                                            // print_r($udata);
                                        ?>
                                        <tr>
                                          <td><?= $j++;?></td>
                                          <td><?=$t->name?></td>
                                          <td><?=$udata['total'][$t->user_id]?></td>
                                          <td><?=$udata['is_approve'][$t->user_id]?></td>
                                          <td><?=$udata['is_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['is_approve'][$t->user_id] + $udata['is_reject'][$t->user_id]))?></td>
                                          <td><?=$udata['admin_approve'][$t->user_id]?></td>
                                          <td><?=$udata['admin_reject'][$t->user_id]?></td>
                                          <td><?=abs($udata['total'][$t->user_id] - ($udata['admin_approve'][$t->user_id] + $udata['admin_reject'][$t->user_id]))?></td>
                                          
                                          
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                
                                  </div>
                              </div>
                            </div>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                              Check Our School Details Report: 
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Client Name</th>
                                                      <th>District</th>
                                                      <th>School Location</th>
                                                      <th>No Of Schools</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $j=1; $schoolcount = 0;
                                                      foreach($school as $a){
                                                              $schoolcount++;
                                                      ?>
                                                    <tr>
                                                        <td><?= $j; ?></td>
                                                        <td><?=$a->client_name?></td>
                                                        <td><?=$a->district?></td>
                                                        <td><?=$a->location?></td>
                                                        <td><?=$a->noofschools?></td>
                                                        
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                
                            </div>
                            <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                              <h3><i>Our Team Report</i></h3>
                            </div>
                            <?php
                                $getbdteam = $this->Menu_model->getTeamDetails($uid);
                                foreach ($getbdteam as $key => $object) {
                                    if ($object->user_id == $uid) {
                                        unset($getbdteam[$key]); // Remove the object from the array
                                        break; // Stop looping once the object is removed
                                    }
                                }
                                $bdusercount = sizeof($getbdteam);
                              
                            ?>
                            <input type="hidden" id="compcount" value="<?=$bdusercount?>">
                            <?php
                              $k=1;
                              foreach($getbdteam as $bdteam){ 
                              $bdteamid =  $bdteam->user_id;
                            ?>
                            <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                                [<?= $k ?>] - <?= $bdteam->name .' - '.$bdteamid ?> - School Details Report: 
                            </div>
                            <div class="collapse mt-3" id="collapseExample<?=$k?>">
                                <div class="card card-body" style="background: azure;"  >
                                    <div class="ApprovedStatus">
                                        <div class="table-responsive">
                                            <table id="exampledata<?=$k?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                      <th>sr. no</th>
                                                      <th>Client Name</th>
                                                      <th>District</th>
                                                      <th>School Location</th>
                                                      <th>No Of Schools</th>
                                                      <th>Approved</th>
                                                      <th>Remark</th>
                                                      <th>Is Admin Approved</th>
                                                      <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                      $j=1; 
                                                        $getbdteamData = $this->Menu_model->allResourcedatabyuid($bdteamid,'school');
                                                        foreach($getbdteamData as $a){
                                                    ?>
                                                    <tr>
                                                      <td><?= $j; ?></td>
                                                      <td><?=$a->client_name?></td>
                                                        <td><?=$a->district?></td>
                                                        <td><?=$a->location?></td>
                                                        <td><?=$a->noofschools?></td>
                                          
                                                      <td><?php 
                                                        if($a->is_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->is_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approve</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>  
                                                        <?php 
                                                          if($a->is_approve_remark == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else{ ?>
                                                        <span><?= $a->is_approve_remark ?></span>
                                                        <?php } ?>
                                                      </td>
                                                      
                                                      <td><?php
                                                        if($a->admin_approve == ''){ ?>
                                                        <span class="p-2 bg-warning mr-2">Pending</span>
                                                        <?php }else if($a->admin_approve == 'Approve'){ ?>
                                                        <span class="p-2 bg-success mr-2">Approved</span>
                                                        <?php }else{ ?>
                                                        <span class="p-2 bg-danger mr-2">Reject</span>
                                                        <?php }?>
                                                      </td>
                                                      <td>
                                                        <div>
                                                          <p><a href="<?=base_url();?>Menu/approveAllResource/<?= $a->id?>/Approve" class="btn btn-success mr-2">Approve</a></p>
                                                          <p><button type="button" class="btn btn-primary"  onclick="RejectSchool(<?= $j ?>,<?= $a->id?>,'Reject')">Reject</button></p>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <?php  $j++; }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                          
                            </div>
                            
                            <?php for($t=1;$t<=sizeof($getbdteamData);$t++){ ?>
                                <div class="modal fade" id="exampleModalCenterdataSchool<?=$t?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                           <form action="<?=base_url();?>Menu/rejectResource" method="post" >
                                                <input type="hidden" id="rejectid" value="" name="reject">
                                                <input type="hidden" value="Reject By Admin" name="action">
                                                 <div class="mb-3 mt-3">
                                                  <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                                </div>
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                                                </div>
                                           </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                            <?php $k++;}?>
                            
                        </div>
                </div>   
            </div>     
    </section>
  
  
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type='text/javascript'>
$(document).ready(function(){
    $('#v-pills-salesppt').click(function(){
        $('#v-pills-faq').hide();
    });
    
    $('.nav-reset').click(function(){
        $("#d").get(0).reset(); 
    });
    
});
//  $(document).ready(function(){
    function RejectButton(mid,id,val){
        // alert(mid);
            $('#exampleModalCenter'+mid).modal('show');
            $('#exampleModalCenter'+mid+' #rejectid').val(id);
            }
            
            function Reject(mid,id,val){
            $('#exampleModalCenterdata'+mid).modal('show');
            $('#exampleModalCenterdata'+mid+' #rejectid').val(id);
            }
            
            function ApproveFaq(mid,id,val){
            $('#exampleModalCenterdataApproveFaq'+mid).modal('show');
            $('#exampleModalCenterdataApproveFaq'+mid+' #approveid').val(id);
            }
            
            function RejectCorporate(mid,id,val){
                $('#exampleModalCenterdataCorporate'+mid).modal('show');
                $('#exampleModalCenterdataCorporate'+mid+' #rejectid').val(id);
            }
            
            function Rejectsalespitch(mid,id,val){
                $('#exampleModalCenterdatasalespitch'+mid).modal('show');
                $('#exampleModalCenterdatasalespitch'+mid+' #rejectid').val(id);
            }
            function Rejectpresentation(mid,id,val){
                $('#exampleModalCenterdatapresentation'+mid).modal('show');
                $('#exampleModalCenterdatapresentation'+mid+' #rejectid').val(id);
            }
            
            function RejectProposal(mid,id,val){
                $('#exampleModalCenterdataProposal'+mid).modal('show');
                $('#exampleModalCenterdataProposal'+mid+' #rejectid').val(id);
            }
            
            function RejectEmail(mid,id,val){
                $('#exampleModalCenterdataemail'+mid).modal('show');
                $('#exampleModalCenterdataemail'+mid+' #rejectid').val(id);
            }
            
            function RejectWhatsapp(mid,id,val){
                $('#exampleModalCenterdataWhatsapp'+mid).modal('show');
                $('#exampleModalCenterdataWhatsapp'+mid+' #rejectid').val(id);
            }
            
            function RejectRegional(mid,id,val){
                $('#exampleModalCenterdataRegional'+mid).modal('show');
                $('#exampleModalCenterdataRegional'+mid+' #rejectid').val(id);
            }
            
            function RejectQuarter(mid,id,val){
                $('#exampleModalCenterdataQuarter'+mid).modal('show');
                $('#exampleModalCenterdataQuarter'+mid+' #rejectid').val(id);
            }
            
            function Rejectannualreview(mid,id,val){
                $('#exampleModalCenterdataannualreview'+mid).modal('show');
                $('#exampleModalCenterdataannualreview'+mid+' #rejectid').val(id);
            }
            
            function RejectDistrict(mid,id,val){
                $('#exampleModalCenterdatadistrict'+mid).modal('show');
                $('#exampleModalCenterdatadistrict'+mid+' #rejectid').val(id);
            }
            
            function RejectSchool(mid,id,val){
                $('#exampleModalCenterdataSchool'+mid).modal('show');
                $('#exampleModalCenterdataSchool'+mid+' #rejectid').val(id);
            }
            
// });


</script>

          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
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
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
<!-- DataTables  & Plugins -->
    <script src="<?=base_url();?>assets/js/jquery.dataTables.min.js"></script>
    <script src="<?=base_url();?>assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?=base_url();?>assets/js/dataTables.responsive.min.js"></script>
    <script src="<?=base_url();?>assets/js/responsive.bootstrap4.min.js"></script>
    <script src="<?=base_url();?>assets/js/dataTables.buttons.min.js"></script>

<script>
 $( document ).ready(function() {
      var compcount = $("#compcount").val();
      
      for(var i = 1; i <= compcount; i++)
      {
      var str = 'exampledata'+i+'_wrapper';
      $("#exampledata"+i).DataTable({
      "responsive": true, 
      "lengthChange": true, 
      "autoWidth": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "paging": true,
      "dom": 'Bfrtip', 
      "buttons": [
      'copy', 'csv', 'excel', 'pdf', 'print'
      ]
      }).buttons().container().appendTo('#'+str+' .col-md-6:eq(0)');
      }
      
      });
      
      
      $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
      $("#examplereview1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#examplereview1_wrapper .col-md-6:eq(0)');
      
      $("#ourreview").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#ourreview_wrapper .col-md-6:eq(0)');
</script>



</body>
</html>
