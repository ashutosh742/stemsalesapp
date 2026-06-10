<!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>STEM APP| WebAPP</title>

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
            <section class="content">
            <div class="container-fluid">
            <div class="row p-3">
            <form  action="<?=base_url();?>Menu/annualAnalysisDetails" method="post">   
            <div class="col-sm col-md-12 col-lg-12 m-auto">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        
                            <center> 
                                <h3>Analysis of 
                                    <div class="text-success">    
                                       <a href="<?=base_url();?>Menu/CompanyDetails/<?=$company->id?>"> <?=$company->compname?> &nbsp;/&nbsp; <?=$company->id?></a>
                                    </div>
                                </h3> 
                            </center>
                            <hr>
                            <?php
                              $uid = $user['user_id'];
        
                            ?>
                            <div class="was-validated">
                                <input type="hidden" value="<?=$user['user_id']?>" name="user">
                                <input type="hidden" id="timerInput" readonly name="timer">
                                <input type="hidden" value="<?=$company->id?>" name="company">
                                <div class="card p-2">
                                    <div class="form-control">Current Status : 
                                        <?=$this->Menu_model->get_statusbyid($init_data->cstatus)[0]->name?>
                                    </div>  
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this status is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="card p-2">
                                    <div class="form-control">Location : 
                                        <?=$company->address?>
                                    </div>  
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Location is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="card p-2">
                                    <div class="form-control">Website : 
                                        <?=$company->website?>
                                    </div>  
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Website is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                    </div>
                                </div>
                                <hr>
                                <?php if(sizeof($ccd)>0){$contactperson=$ccd[0]->contactperson;$phoneno=$ccd[0]->phoneno;$emailid=$ccd[0]->emailid;$designation=$ccd[0]->designation;}
                                else{$contactperson="";$phoneno="";$emailid="";$designation="";}?>
                                <div class="card p-2">
                                    <div class="form-control">Primary Contact Details:</div>
                                    <div class="p-2">
                                        Contact Person : <?=$contactperson?><br>
                                        Contact Number : <?=$phoneno?><br>
                                        Email id : <?=$emailid?><br>
                                        Designation : <?=$designation?>
                                    </div> 
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Primary Contact information is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                    </div>
                                </div>
                                <hr>
                                <?php if(sizeof($alternate)>0){$contactperson=$alternate[0]->contactperson;$phoneno=$alternate[0]->phoneno;$emailid=$alternate[0]->emailid;$designation=$alternate[0]->designation;}
                                else{$contactperson="";$phoneno="";$emailid="";$designation="";}?>
                                <div class="card p-2">
                                    <div class="form-control">Secondary Contact Details:</div>
                                    <div class="p-2">
                                        Contact Person : <?=$contactperson?><br>
                                        Contact Number : <?=$phoneno?><br>
                                        Email id : <?=$emailid?><br>
                                        Designation : <?=$designation?>
                                    </div> 
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Secondary Contact information is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="card p-2">
                                    <div class="form-control">Partner Type : 
                                        <?=$this->Menu_model->get_partnerbyid($company->partnerType_id)[0]->name?>
                                    </div>  
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Partner Type is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="card p-2">
                                    <div class="form-control">Category : 
                                        <?php if($init_data->focus_funnel == "yes"){
                                            echo "Foucs Funnel";
                                        }if($init_data->upsell_client == "yes"){echo "Upsell Client";}
                                        if($init_data->keycompany == "yes"){echo "Key Company";}
                                        ?>
                                        
                                    </div>  
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Category is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="card p-2">
                                    <div class="form-control">Potential Client : 
                                        <?=$init_data->potential?>
                                    </div>  
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Potential client information is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                    </div>
                                </div>
                                <hr>
                                
                                <div class="card p-2">
                                    <div class="form-control">Total Logs : 
                                        <?=sizeof($tblc)?>
                                    </div>  
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="Is this Logs count is Right?" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                    </div>
                                </div>  
                                <hr>
                                <div class="card p-2">
                                    <div class='form-control'>Calls done :      <span id="calldone" style="background: yellow">
                                    </span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>							
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count = []; $datact = 0; foreach($tblc as $tb){
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 1){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1=$usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$i?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php $i++;}} ?>
                                                <input type="hidden" id="callbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">

                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php 
                                        $val="Is this call Logs Details are Right?";
                                        if($datact == 0){
                                            $val="Do you agree this was not done ?";
                                        }
                                    ?>
                                    <div class="form-group p-2">
                                        <input type="text" name="que[]" value="<?$val?>" readonly class="form-control"><br>
                                        <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                    </div>
                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Emails done :
                                        <span id="emaildone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[];$datact = 0; foreach($tblc as $tb){
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 2){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1=$usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$i?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }else if($i==1){ ?>
                                                    <!--<tr>-->
                                                    <!--<td colspan="10" class="text-danger">NO Emails Done</td>-->
                                                    <!--</tr>-->
                                                <?php }$i++;}?>
                                                <input type="hidden" id="emailbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Email Logs are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Scheduled Meeting done :
                                        <span id="scheduledmeetingdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[]; $datact = 0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 3){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1=$usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$i?></td>
                                                    <!--<td><?=$tb->mom?></td>-->
                                                    <td>
                                                        <?php $tbldata = $this->Menu_model->get_tbldata($tb->nextCFID); ?>
                                                        <?=$tbldata[0]->mom?>
                                                    </td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }?>
                                                    
                                                <?php $i++;}?>
                                                <input type="hidden" id="scheduledmeeting" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Scheduled Meeting Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Barg in Meeting done :
                                        <span id="bargindone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; 
                                                $log_count=[];$datact=0;
                                                foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 4){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                        $j++;
                                                    $datact++;
                                                    
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <!--<td><?=$tb->mom?></td>-->
                                                    <td>
                                                        <?php $tbldata = $this->Menu_model->get_tbldata($tb->nextCFID); ?>
                                                        <?=$tbldata[0]->mom?>
                                                    </td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}
                                                if($j=0){?>
                                                    <tr>
                                                    <td colspan="10" class="text-danger">NO Barg in Meeting</td>
                                                    </tr>
                                                <?php }?>
                                                <input type="hidden" id="barginbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Barg in Meeting Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Whatsapp Activity done :
                                        <span id="whatsappdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[];$datact=0; foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 5){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}
                                                if($j=0){?>
                                                <tr>
                                                    <td colspan="10" class="text-danger">NO Whatsapp Activity Done</td>
                                                    </tr>
                                                <?php } ?>
                                                <input type="hidden" id="whatsappbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Whatsapp Activity Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>write MOM done :
                                        <span id="momdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[];$datact=0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 6){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}
                                                if($j=0){?>
                                                <tr>
                                                    <td colspan="10" class="text-danger">NO MOM Done</td>
                                                    </tr>
                                                <?php }?>
                                                <input type="hidden" id="mombystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this MOM Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>write Proposal done :
                                        <span id="proposaldone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>							<th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count = [];$datact=0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 7){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}
                                                if($j=0){?>
                                                <tr>
                                                    <td colspan="10" class="text-danger">NO Right Proposal Done</td>
                                                    </tr>
                                                <?php }?>
                                                <input type="hidden" id="proposalbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Proposal Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Review done :
                                        <span id="reviewdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[];$datact = 0;foreach($tblc as $tb){
                                                    $j =0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 8){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}
                                                if($j=0){?>
                                                <tr>
                                                    <td colspan="10" class="text-danger">NO Review Done</td>
                                                    </tr>
                                                <?php } ?>
                                                <input type="hidden" id="reviewbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Review Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Roster Calling done :
                                        <span id="rostercalldone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[];$datact = 0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 9){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}
                                                if($j=0){?>
                                                <tr>
                                                    <td colspan="10" class="text-danger">NO Roster Calling Done</td>
                                                    </tr>
                                                    <?php }?>
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Roster Calling Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <input type="hidden" id="rosterbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Research done :
                                        <span id="researchdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[]; $datact=0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 10){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}if($j=0){?>
                                                <tr>
                                                    <td colspan="10" class="text-danger">NO Research Done</td>
                                                    </tr>
                                                    <?php } ?>
                                                    <input type="hidden" id="researchbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Research Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....'required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Mail Check done :
                                        <span id="mailcheckdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[]; $datact=0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 11){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}if($j=0){?>
                                                <tr>
                                                    <td colspan="10" class="text-danger">NO Mail Check Done</td>
                                                </tr>
                                                <?php }?>
                                                <input type="hidden" id="mailcheckbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Mail Check Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Documentation done :
                                        <span id="documentationdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[];$datact=0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 12){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}if($j=0){?>
                                                    <tr>
                                                    <td colspan="10" class="text-danger">NO documentation Done</td>
                                                    </tr>
                                                <?php } ?>
                                                <input type="hidden" id="documentationbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Documentation Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Social Networking done :
                                        <span id="socialnetworkingdone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>								
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; $log_count=[];$datact=0;foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 13){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}if($j=0){?>
                                                    <tr>
                                                    <td colspan="10" class="text-danger">NO Social Networking Done</td>
                                                    </tr>
                                                <?php } ?>
                                                <input type="hidden" id="socialnetworkbystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Social Networking Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Social Activity done :
                                        <span id="socialactivitydone" style="background: yellow"></span>
                                    </div><br>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>							
                                                        <th>Sr Number</th>
                                                        <th>MOM</th>
                                                        <th>Remarks</th>
                                                        <th>Current Action</th>
                                                        <th>Action Taken</th>
                                                        <th>Purpose Achieved</th>
                                                        <th>Updated Date</th>
                                                        <th>Updated By</th>
                                                        <th>Last Status</th>
                                                        <th>Updated Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1;$datact=0; foreach($tblc as $tb){
                                                    $j=0;
                                                    if($tb->nextCFID!='0' && $tb->nstatus_id!='' && $tb->actiontype_id == 14){
                                                    $tid = $tb->id;
                                                    $uid = $tb->user_id;
                                                    $aid = $tb->actiontype_id;
                                                    $lastsid = $tb->status_id;
                                                    $upsid = $tb->nstatus_id;
                                                    $ui=$this->Menu_model->get_userbyid($uid);
                                                    $ai=$this->Menu_model->get_actionbyid($aid);
                                                    $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                                    $usi=$this->Menu_model->get_statusbyid($upsid);
                                                    $status1 = $usi[0]->name;
                                                    if (isset($log_count[$status1])) {
                                                        $log_count[$status1]++;
                                                        $sameStatus[$status1]=$tb->updateddate;
                                                    } else {
                                                        $log_count[$status1] = 1;
                                                    }
                                                    $j++;
                                                    $datact++;
                                                ?>
                                                <tr>
                                                    <td><?=$j?></td>
                                                    <td><?=$tb->mom?></td>
                                                    <td><?=$tb->remarks?></td>
                                                    <td><?=$ai[0]->name?></td>
                                                    <td><?=$tb->actontaken?></td>
                                                    <td><?=$tb->purpose_achieved?></td>
                                                    <td><?=$tb->updateddate?></td>
                                                    <td><?=$ui[0]->name?></td>
                                                    <td><?=$lsi[0]->name;?></td>
                                                    <td><?=$usi[0]->name;?></td>
                                                </tr>
                                                <?php }$i++;}if($j=0){?>
                                                <tr>
                                                <td colspan="10" class="text-danger">NO Social Activity Done</td>
                                                </tr>
                                                <?php } ?>
                                                <input type="hidden" id="socialactivitybystatus" value="<?php echo htmlspecialchars(json_encode($log_count)); ?>">
                                            </tbody>
                                            </table>
                                            <?php 
                                                $val="Is this Social Activity Logs Details are Right?";
                                                if($datact == 0){
                                                    $val="Do you agree this was not done ?";
                                                }
                                            ?>
                                            <div class="form-group p-2">
                                                <input type="text" name="que[]" value="<?=$val?>" readonly class="form-control"><br>
                                                <textarea name='remark[]' class='form-control' rows='3' placeholder='Remark....' required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card p-2">
                                    <div class='form-control'>Status Conversion :
                                    <span id="statusconversiondone" style="background: yellow"></span>
                                    </div><br>
                                    <?php $task_id_counts = [];
                                    $sameStatus = [];
                                    $sdate = [];
                                    $ldate = [];
                                    $action_ct = [];
                                      foreach($tblc as $tb){ 
                                        $aid = $tb->actiontype_id;
                                        $task_id = $tb->status_id;

                                        if(!isset($sdate[$task_id])){
                                            $sdate[$task_id] = $tb->updateddate;
                                        }else{
                                            $ldate[$task_id] = $tb->updateddate;
                                        }
                                        if (isset($task_id_counts[$task_id])) {
                                            $task_id_counts[$task_id]++;
                                            $sameStatus[$task_id]=$tb->updateddate;
                                        } else {
                                            $task_id_counts[$task_id] = 1;
                                        }

                                        if (isset($action_ct[$task_id][$aid])) {
                                            $action_ct[$task_id][$aid]++;
                                        } else {
                                            $action_ct[$task_id][$aid] = 1;
                                        }
                                        
                                    }
                                    // var_dump($action_ct);
                                    ?>
                                    <?php $sd = '2023-04-01';
                                          $ed = '2024-03-31';
                                          
                                          
                                          
                                          $scname=$this->Menu_model->final_scon1_new($uid,$sd,$ed,0);
                                          
                                    ?>
                                    <div class="col-sm col-lg-12">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <!--<thead>-->
                                                <!--    <tr>-->
                                                <!--        <th>Conversions</th>-->
                                                <!--        <th></th>-->
                                                <!--        <th>Remarks</th>-->
                                                <!--    </tr>-->
                                                   
                                                <!--</thead>-->
                                                <tbody>
                                                    <?php
                                                   
                                                        foreach($scname as $scid){
                                                        $string = $scid->scname;
                                                        $parts = explode(" -to- ", $string);
                                                        $firstS = $parts[0];
                                                        $secondS = $parts[1];
                                                        
                                                            $firstS = $this->Menu_model->get_statusbyname($firstS);
                                                            $fsid = $firstS[0]->id;
                                                            $secondS = $this->Menu_model->get_statusbyname($secondS);
                                                            $ssid = $secondS[0]->id;
                                                    ?>
                                                    <tr>
                                                        <td><?=$scid->scname?></td>
                                                        <td>
                                                            <!--<a href="FinalSConversion/<?=$uid?>/<?=$sd?>/<?=$ed?>/<?=$fsid?>/<?=$ssid?>">-->
                                                            <?=$this->Menu_model->final_scon2($uid,$sd,$ed,0,$fsid,$ssid);?>
                                                            <!--</a>-->
                                                        </td>   
                                                        <td>
                                                            <textarea name="que[]" cols="1" rows="3" class="form-control" readonly>Is the time taken for status conversion right?
Is the task planned and frequency correct?</textarea>
                                                        <br>
                                                            <textarea name="remark[]" id="" cols="30" rows="3" class="form-control" required></textarea>
                                                        </td>
                                                    </tr>
                                                    <?php } ?> 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!--<div class="col-sm col-lg-12">-->
                                    <!--    <div class="table-responsive">-->
                                            <!--<table id="" class="table table-striped table-bordered" cellspacing="0" width="">-->
                                                <!--<thead>-->
                                                <!--    <tr>							-->
                                                <!--        <th width="10%">Sr No</th>-->
                                                <!--        <th>Status</th>-->
                                                <!--        <th>Count</th>-->
                                                <!--        <th>From Date</th>-->
                                                <!--        <th>To Date</th>-->
                                                <!--        <th width="10%">Same status for no of days</th>-->
                                                <!--        <th width="33%">Remark</th>-->
                                                <!--    </tr>-->
                                                <!--</thead>-->
                                                <?php //$k =1;foreach($status as $st){
                                                    // $status = $st->name;
                                                    // $color = $st->color;
                                                    // $interval=0;
                                                    // $interval1='';
                                                    // $start = '';
                                                    // $end = '';
                                                    // if(isset($sdate[$st->id]) && isset($sameStatus[$st->id])) {
                                                    //     $start = $sdate[$st->id];
                                                    //     $date1 = DateTime::createFromFormat('Y-m-d H:i:s', $sdate[$st->id]);
                                                    //     $date2 = DateTime::createFromFormat('Y-m-d H:i:s', $sameStatus[$st->id]);
                                                    //     if ($date1 !== false && $date2 !== false) {
                                                    //         $end = $ldate[$st->id];
                                                    //         $interval = $date1->diff($date2);
                                                    //         $interval1 = abs($interval->format('%R%a days')).' days';
                                                            
                                                    //     }
                                                   // }?>
<!--                                                    <tr class="<?=$color?>">-->
<!--                                                        <td><?=$k?></td>-->
<!--                                                        <td><?=$status?></td>-->
<!--                                                        <td>-->
<!--                                                        
<!--                                                        //echo $task_id_counts[$st->id].'</br>';
<!--                                                        //foreach($action_ct[$st->id] as $a=>$v){
<!--                                                           // echo '('.$this->Menu_model->get_actionbyid($a)[0]->name.'-'.$v.')<br>';-->
<!--                                                       // }-->
<!--                                                        ?></td>-->
<!--                                                        <td><?=$start?></td>-->
<!--                                                        <td><?=$end?></td>-->
<!--                                                        <td><?=$interval1?>-->
<!--                                                        </td>-->
<!--                                                        <td>-->
<!--                                                        <textarea name="que[]" cols="1" rows="3" class="form-control" readonly>Is the time taken for status conversion right?-->
<!--Is the task planned and frequency correct?</textarea>-->
<!--                                                        <br>-->
<!--                                                            <textarea name="remark[]" id="" cols="30" rows="3" class="form-control" required></textarea>-->
<!--                                                    </tr>-->
                                                <?php //$k++; } ?>
                                            <!--    <tbody>-->
                                            <!--    </tbody>-->
                                            <!--</table>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    
                                </div>
                                <hr>
                                <div class="card p-2">
                                    <div class='form-control'>Number of Task Performed Against Each Status:</div><br>  
                                    <div class="col-sm col-lg-12">
                                        <div class="table-responsive">
                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                <thead>
                                                    <tr>							
                                                        <th width="10%">Sr No</th>
                                                        <th>Status</th>
                                                        <th>Task Performed</th>
                                                        <!--<th>From Date</th>-->
                                                        <!--<th>To Date</th>-->
                                                        <!--<th width="10%">Same status for no of days</th>-->
                                                        <th width="33%">Remark</th>
                                                    </tr>
                                                    <?php $k =1;foreach($status as $st){
                                                    $status = $st->name;
                                                    $color = $st->color;
                                                    $interval=0;
                                                    $interval1='';
                                                    $start = '';
                                                    $end = '';
                                                    if(isset($sdate[$st->id]) && isset($sameStatus[$st->id])) {
                                                        $start = $sdate[$st->id];
                                                        $date1 = DateTime::createFromFormat('Y-m-d H:i:s', $sdate[$st->id]);
                                                        $date2 = DateTime::createFromFormat('Y-m-d H:i:s', $sameStatus[$st->id]);
                                                        if ($date1 !== false && $date2 !== false) {
                                                            $end = $ldate[$st->id];
                                                            $interval = $date1->diff($date2);
                                                            $interval1 = abs($interval->format('%R%a days')).' days';
                                                            
                                                        }
                                                    }?>
                                                    <tr class="">
                                                        <td><?=$k?></td>
                                                        <td><?=$status?></td>
                                                        <td>
                                                        <?php
                                                        echo $task_id_counts[$st->id].'</br>';
                                                        foreach($action_ct[$st->id] as $a=>$v){
                                                            echo '('.$this->Menu_model->get_actionbyid($a)[0]->name.'-'.$v.')<br>';
                                                        }
                                                        ?></td>
                                                        <!--<td><?=$start?></td>-->
                                                        <!--<td><?=$end?></td>-->
                                                        <!--<td><?=$interval1?>-->
                                                        </td>
                                                        <td>
                                                        <textarea name="que[]" cols="1" rows="3" class="form-control" readonly>Is the time taken for status conversion right?
Is the task planned and frequency correct?</textarea>
                                                        <br>
                                                            <textarea name="remark[]" id="" cols="30" rows="3" class="form-control" required></textarea>
                                                    </tr>
                                                <?php $k++; } ?>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                
                            </div>
                            <hr>
                            <div class="card p-2">
                                <div class="card-header">
                                    <center><h2>
                                    For Year 2024-25</h2>
                                    </center>
                
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="fyear"value="2024-2025">
                                    <div class="form-group">
                                        <label>Will be keeping this company with you?</label>
                                        <select name="kcompany" id="kcompany" class="form-control">
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                    <div id="showkeycmp">
                                        <div class="form-group">
                                            <label>If NO, then Remarks</label>
                                            <select name="keepremark" id="keepremark" class="form-control">
                                                <option value="">Select Option </option>
                                                <option value="This company is not under CSR mandate">This company is not under CSR mandate</option>
                                                <option value="This company is not present in my region">This company is not present in my region</option>
                                                <option value="This is duplicate lead">This is duplicate lead</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="hideoncompanykeep">
                                        <div class="form-group">
                                            <label>Did you have any face to face or virtual meeting with client?</label>
                                            <select name="vmeeting" id="vmeeting" class="form-control">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Current year Status  <span class="text-danger">(Previous Year Status : <?=$this->Menu_model->get_statusbyid($init_data->cstatus)[0]->name?>)  You Must Have To Select Status For Current Year</span></label>
                                            <select class="form-control" id="statusid" name="cstatusid" onchange="generateTable()">
                                                <option value="">Select Status</option>
                                                <?php// $status = $this->Menu_model->get_status($uid);
                                                //foreach($status as $st){
                                                ?>
                                                <!--<option value="<?=$st->id?>"><?=$st->name?></option>-->
                                                <?php //} ?>
                                                <option value="1">Open</option>
                                                <option value="8">OPEN RPEM</option>
                                            </select>
                                        </div>
                                        
                                        
                                        <table class="table table-striped table-bordered" cellspacing="0" width="" id="statustablehide">
                                            <thead>
                                                <tr>
                                                    <th>From Status</th>
                                                    <th>To Status</th>
                                                    <th>Target Date</th>
                                                </tr>
                                            </thead>
                                            <tbody id="statusTable" >
    
                                            </tbody>
                                        </table>
                                        
                                        <div class="form-group">
                                            <label>Is the location of this company is Right?<span class="text-danger">(Previous Location - <?=$company->address?>)</span></label>
                                            <select class="form-control" id="location" name="clocation" required>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select><br>
                                            <div class="newLocation">
                                                <label>Enter Updated Location(Address)</label>
                                                <input type="text" name="change_location" id="change_location" class="form-control">
                                            </div>
                                            <div class="newLocation">
                                                <label>Enter Updated Location(District)</label>
                                                <input type="text" name="district_location" id="change_location" class="form-control">
                                            </div>
                                            <div class="newLocation">
                                                <label>Enter Updated Location(State)</label>
                                                <input type="text" name="state_location" id="change_location" class="form-control">
                                            </div>
                                        </div>
                                        
                                        <?php 
                                        // echo "<pre>";
                                        // print_r($user['type_id']);
                                        // die;
                                        
                                        if($user['type_id'] != 4){ ?> 
                                        
                                        
                                         <div class="from-group">
                                            <b><lable>Select travel Cluster</lable></b>
                                        <select class="form-control" name="selectCluster">
                                            <option value='0'>Select Cluster</option>
                                             <?php 
                                             foreach($cluData as $cld){ ?>
                                                 <option value="<?= $cld->id; ?>"><?= $cld->clustername; ?></option>
                                          
                                           <?php } ?>
                                           
                                        </select>
                                        <br/>
                                      </div>
                                        <?php } ?>
                                        
                                        
                                        <div class="form-group">
                                            <label>Focus And Positive Key Client For FY 2024-25</label>
                                            <select name="focuspositive" id="focuspositive" class="form-control">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Focus Lead For this Quarter 1?</label>
                                            <select name="focusfunnel" id="focusfunnel" class="form-control">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div id="flead">
                                            <div class="form-group">
                                                <label>Select Focus lead for Quarter</label>
                                                <select name="focusqu" id="focusqu" class="form-control">
                                                    <option value="Focus for Quarter 2">Focus for Quarter 2</option>
                                                    <option value="Focus for Quarter 3">Focus for Quarter 3</option>
                                                    <option value="Focus for Quarter 4">Focus for Quarter 4</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Top Spender For this Year?<span class="text-danger">(Whose CSR Budget Is More Than 2 Crore)</span></label>
                                            <select name="topspender" id="topspender" class="form-control">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Is This Upsell Client?<span class=""></span></label>
                                            <select name="upsell" id="upsell" class="form-control">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Is partnership type right?<span class="text-danger">(Previous Partner Type : <?=$this->Menu_model->get_partnerbyid($company->partnerType_id)[0]->name?>)</span></label>
                                            <select name="cpartnership" id="cpartnership" class="form-control">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group updateptype">
                                            <label>Update the Partnership Type</label>
                                            <select name="partnership" id="partnership" class="form-control">
                                                <?php $ptype = $this->Menu_model->get_partner();
                                                foreach($ptype as $p){?>
                                                <option value="<?=$p->id?>"><?=$p->name?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Do You Need Any Intervention or Support From ?</label>
                                            <!--<input type="text" class="form-control" name="intervntion" id="intervntion"required>-->
                                            <select class="form-control" name="intervention" id="intervention">
                                                <?php foreach($admin as $ad){?>
                                                <option value="<?=$ad->user_id?>"><?=$ad->name?></option>
                                                <?php } ?>
                                                    
                                                    
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>What Support You Required?</label>
                                            <input type="text" class="form-control" name="support" id="support" >
                                        </div>
                                        <div class="form-group">
                                            <label for="noschool">NO of School</label>
                                            <input type="number" class="form-control" name="noschool" id="noschool" placeholder="NO of School">
                                        </div>
                                        <div class="form-group">
                                            <label>Expected Revenue</label>
                                            <input type="number" class="form-control" name="revenue" id="revenue">
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        
                        
                    
                        
                        <center><button type="submit" class="btn btn-success" id="submitButton">SUBMIT</button><br></center>
                    </form>  
                </div>
            </div>
        </section>
        
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>

$(document).ready(function(){
    $('.newLocation').hide();
    $('.updateptype').hide();
    $('#showkeycmp').hide();
    $('#flead').hide();
// Timer variables
var timerInterval;
var startTime;
var runningTime = 0;

// Timer element
var timerInput = document.getElementById('timerInput');

// Function to update timer display
function updateTimer() {
    var elapsedTime = Date.now() - startTime;
    var hours = Math.floor(elapsedTime / 3600000);
    var minutes = Math.floor((elapsedTime % 3600000) / 60000);
    var seconds = Math.floor((elapsedTime % 60000) / 1000);

    // Format time with leading zeros
    var formattedTime = 
        (hours < 10 ? '0' + hours : hours) + ':' +
        (minutes < 10 ? '0' + minutes : minutes) + ':' +
        (seconds < 10 ? '0' + seconds : seconds);

    // Update timer input value
    timerInput.value = formattedTime;

    // Update runningTime for accuracy when starting again after pause
    runningTime = elapsedTime;
}

// Function to start the timer
function startTimer() {
    // Start timer if not already running
    if (!timerInterval) {
        startTime = Date.now() - runningTime;
        timerInterval = setInterval(updateTimer, 1000);
    }
}

// Start the timer when the page loads
window.addEventListener('load', startTimer);


    $('#statustablehide').hide();
    var callbystatus = document.getElementById("callbystatus").value;
    var statusValues = JSON.parse(callbystatus);
    var call = "";
    $.each(statusValues, function(key, value) {
        call += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#calldone').html(call);

    var emailbystatus = document.getElementById("emailbystatus").value;
    var statusValuese = JSON.parse(emailbystatus);
    var email = "";
    $.each(statusValuese, function(key, value) {
        email += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#emaildone').html(email);


    var scheduledmeeting = document.getElementById("scheduledmeeting").value;
    var statusValuessm = JSON.parse(scheduledmeeting);
    var sm = "";
    $.each(statusValuessm, function(key, value) {
        sm += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#scheduledmeetingdone').html(sm);


    var barginbystatus = document.getElementById("barginbystatus").value;
    var statusValuebbs = JSON.parse(barginbystatus);
    var bbs = "";
    $.each(statusValuebbs, function(key, value) {
        bbs += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#bargindone').html(bbs);

    var whatsappbystatus = document.getElementById("whatsappbystatus").value;
    var statusValuewbs = JSON.parse(whatsappbystatus);
    var wbs = "";
    $.each(statusValuewbs, function(key, value) {
        wbs += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#whatsappdone').html(wbs);

    var mombystatus = document.getElementById("mombystatus").value;
    var statusValuemom = JSON.parse(mombystatus);
    var mom = "";
    $.each(statusValuemom, function(key, value) {
        mom += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#momdone').html(mom);

    var proposalbystatus = document.getElementById("proposalbystatus").value;
    var statusValuepbs = JSON.parse(proposalbystatus);
    var pbs = "";
    $.each(statusValuepbs, function(key, value) {
        pbs += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#proposaldone').html(pbs);

    var reviewbystatus = document.getElementById("reviewbystatus").value;
    var statusValuerbs = JSON.parse(reviewbystatus);
    var rbs = "";
    $.each(statusValuerbs, function(key, value) {
        rbs += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#reviewdone').html(rbs);

    var rosterbystatus = document.getElementById("rosterbystatus").value;
    var statusValueroster = JSON.parse(rosterbystatus);
    var roster = "";
    $.each(statusValueroster, function(key, value) {
        roster += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#rostercalldone').html(roster);

    var researchbystatus = document.getElementById("researchbystatus").value;
    var statusValueresearch = JSON.parse(researchbystatus);
    var reseatch = "";
    $.each(statusValueresearch, function(key, value) {
        reseatch += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#researchdone').html(reseatch);

    var mailcheckbystatus = document.getElementById("mailcheckbystatus").value;
    var statusValuemail = JSON.parse(mailcheckbystatus);
    var mail = "";
    $.each(statusValuemail, function(key, value) {
        mail += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#mailcheckdone').html(mail);

    var documentationbystatus = document.getElementById("documentationbystatus").value;
    var statusValuedoc = JSON.parse(documentationbystatus);
    var doc = "";
    $.each(statusValuedoc, function(key, value) {
        doc += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#documentationdone').html(doc);

    var socialnetworkbystatus = document.getElementById("socialnetworkbystatus").value;
    var statusValuesnbs = JSON.parse(socialnetworkbystatus);
    var snbs = "";
    $.each(statusValuesnbs, function(key, value) {
        snbs += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#socialnetworkingdone').html(snbs);

    var socialactivitybystatus = document.getElementById("socialactivitybystatus").value;
    var statusValuesabs = JSON.parse(socialactivitybystatus);
    var sabs = "";
    $.each(statusValuesabs, function(key, value) {
        sabs += key+' logs count - <span class="text-success">'+value+' </span> ';
    });
    $('#socialactivitydone').html(sabs);
});
function generateTable() {
    $('#statustablehide').show();
    var selectedOption = document.getElementById("statusid").value;
    var table = document.getElementById("statusTable");
    table.innerHTML = ""; // Clear previous table content

    // Define mappings based on selected option
    var mappings = {
      "1": ["Open","Open RPEM", "Reachout", "Tentative", "Positive NAP",  "Positive", "Very Positive", "Closure"],
      "8": ["Open RPEM", "Reachout", "Tentative", "Positive NAP",  "Positive", "Very Positive", "Closure"],
    //   "2": ["Reachout", "Tentative", "Positive", "Positive NAP",  "Very Positive", "Closure"],
    //   "3": ["Tentative", "Positive", "Positive NAP",  "Very Positive", "Closure"],
    //   "6": ["Positive", "Positive NAP", "Very Positive", "Closure"],
    //   "12": ["Positive NAP", "Very Positive", "Closure"],
    //   "9": ["Very Positive", "Closure"],
      // Add mappings for other options as needed
    };

    // Generate table rows based on mappings
    var statusList = mappings[selectedOption];
    
    if (statusList) {
      for (var i = 0; i < statusList.length - 1; i++) { 
        var sname = statusList[i + 1].toString().toLowerCase().replace(/\s+/g, '');
        var row = table.insertRow();
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        cell1.innerHTML = statusList[i];
        cell2.innerHTML = statusList[i + 1];
        cell3.innerHTML = '<input type="date" class="form-control" name="' + sname + '" id="stat_'+i+'" >';
        
    }

      document.getElementById("stat_0").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_1");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });
      document.getElementById("stat_1").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_2");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });
      document.getElementById("stat_2").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_3");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });
      document.getElementById("stat_3").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_4");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });
      document.getElementById("stat_4").addEventListener("change", function() {
        var stat0Value = new Date(this.value);
        var stat1Field = document.getElementById("stat_5");
        var maxDate = new Date(stat0Value.getFullYear(), stat0Value.getMonth(), stat0Value.getDate() + 15);
        var maxDateString = maxDate.toISOString().split('T')[0]; // Convert date to string in YYYY-MM-DD format
        stat1Field.setAttribute("max", maxDateString);
    });


    } else {
      table.innerHTML = "<tr><td colspan='3'>No mapping found for selected option</td></tr>";
    }
  }

    $('#location').on('change',function(){
        var loc = document.getElementById("location").value;
        if(loc == 'yes'){
            $('.newLocation').hide();
            $('#change_location').prop('required', false);
        }else{
            $('.newLocation').show();
            $('#change_location').prop('required', true);
        }
    });
    $('#cpartnership').on('change',function(){
        var loc = document.getElementById("cpartnership").value;
        if(loc == 'yes'){
            $('.updateptype').hide();
            $('#partnership').prop('required', false);
        }else{
            $('.updateptype').show();
            $('#partnership').prop('required', true);
        }
    });
    
    $('#kcompany').on('change',function(){
        var kc = document.getElementById("kcompany").value;
        if(kc == 'yes'){
            $('#hideoncompanykeep').show();
            $('#showkeycmp').hide();
            $('#statusid').prop('required', true);
            $('#support').prop('required', true);
            $('#noschool').prop('required', true);
             $('#revenue').prop('required', true);
             $('#stat_0','#stat_1','#stat_2','#stat_3','#stat_4','#stat_5').prop('required', true);
        }else{
            $('#hideoncompanykeep').hide();
            $('#showkeycmp').show();
            $('#statusid').prop('required', false);
            $('#support').prop('required', false);
            $('#noschool').prop('required', false);
             $('#revenue').prop('required', false);
             $('#stat_0','#stat_1','#stat_2','#stat_3','#stat_4','#stat_5').prop('required', false);
        }
    });
    
    $('#focusfunnel').on('change',function(){
        var ff = document.getElementById("focusfunnel").value;
        if(ff == 'yes'){
            $('#flead').hide();
        }else{
            $('#flead').show();
        }
    });
//   $('#statusid').on('change', function () {
        var sid = document.getElementById("statusid").value;
    
    // function setMaxEndDate() {
    //     alert("data");
    //     // Get the selected start date
    //     var startDate = new Date(document.getElementById("stat_0").value);
    //     var endDateInput = document.getElementById("stat_1");
        
    //     // If the start date is valid
    //     if (!isNaN(startDate.getTime())) {
    //         // Calculate the end date as 15 days ahead of the start date
    //         var endDate = new Date(startDate);
    //         endDate.setDate(startDate.getDate() + 15);
            
    //         // Format endDate as yyyy-mm-dd
    //         var endDateFormatted = endDate.toISOString().split('T')[0];
            
    //         // Set the max attribute of the end date input field
    //         endDateInput.setAttribute("max", endDateFormatted);
    //     }
    // }

    // document.addEventListener("DOMContentLoaded", function() {
    //     // Your code here
    //     document.getElementById('stat_0').addEventListener('change', function() {
    //         alert(1);
    //         console.log("Change event triggered on stat_0");
    //         setMaxEndDate();
    //     });
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
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        </script>
        </body>
        </html>