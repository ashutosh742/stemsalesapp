
<?php include('header.php') ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Status Conversion Detail</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                    <thead>
                                    <tr>
                                            <th>S.No.</th>
                                            <th>User Name</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Initiated Time</th>
                                            <th>Plan and Initiated Time Diff</th>
                                            <th>Completed Time</th>
                                            <th>Plan and Completed Time Diff</th>
                                            <th>Initiated and Completed Time Diff</th>
                                            <th>Last_Task Date</th>
                                            <th>Current_Task Date</th>
                                            <th>Current task planned after time difference</th>
                                            <th>Last_Task_Activity</th>
                                            <th>Current_Task_Activity</th>
                                            <th>Last_Task_Remarks</th>
                                            <th>Current_Task_Remarks</th>
                                            <th>Last_Status</th>
                                            <th>Current_Status</th>
                                            <th>Action Taken</th>
                                            <th>Purpose Achieved</th>
                                            <th>Review Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=1;
                                        foreach($taskList as $md){
                                            $bd = $md->user_id;
                                            $bdname = $this->Menu_model->get_userbyid($bd);
                                            
                                            $tid = $md->id;
                                            $ltid = $md->ltid;
                                            
                                            $inid  = $md->cid_id;
                                            $inid = $this->Menu_model->get_initbyid($inid);
                                            $mtd = $this->Menu_model->get_ccitblall($tid);
                                            
                                            $lsid = $mtd[0]->status_id;
                                            $csid = $mtd[0]->nstatus_id;
                                            
                                            $s1 = $this->Menu_model->get_statusbyid($lsid);
                                            if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                            $s2 = $this->Menu_model->get_statusbyid($csid);
                                            if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                            
                                            if($ltid!=''){
                                                
                                            $mltd = $this->Menu_model->get_ccitblall($ltid);
                                            $ltime = $mltd[0]->updateddate;
                                            $ctime = $mtd[0]->updateddate;
                                            
                                            $nltime = date('d-m-Y  h:i A', strtotime($ltime));
                                            $nctime = date('d-m-Y  h:i A', strtotime($ctime));
                                                
                                            }else{$mltd='';$nltime='';$nctime='';$ltime='';$ctime='';}
                                            
                                        ?>
                                  <tr>
                                        <td><?=$i?></td>
                                        <td><?=$bdname[0]->name?></td>
                                        <td><a href="<?=base_url();?>/Menu/CompanyDetails/<?=$inid[0]->cmpid_id?>"><?=$mtd[0]->compname?></a></td>
                                        <td><?=date('d-m-Y h:i A', strtotime($pltime = $md->appointmentdatetime));?></td>
                                        <td><?=date('d-m-Y h:i A', strtotime($intime = $md->initiateddt));?></td>
                                        <td><?=$this->Menu_model->timediff($pltime,$intime)?></td>
                                        <td><?=date('d-m-Y h:i A', strtotime($uptime = $md->updateddate));?></td>
                                        <td><?=$this->Menu_model->timediff($pltime,$uptime)?></td>
                                        <td><?=$this->Menu_model->timediff($intime,$uptime)?></td>
                                        <td><?=$nltime?></td>
                                        <td><?=$nctime?></td>
                                        <td><?php if($ctime!=''){echo $this->Menu_model->timediff($ltime,$ctime);}?></td>
                                        <td><?php if($mltd!=''){echo $mltd[0]->current_action_type;}?></td>
                                        <td><?=$mtd[0]->current_action_type?></td>
                                        <td><?php if($mltd!=''){echo $mltd[0]->remarks;}?></td>
                                        <td><?=$md->remarks?><?=$md->mom?></td>
                                        <td><?=$s1?></td>
                                        <td><?=$s2?></td>
                                        <td><?=$mtd[0]->actontaken?></td>
                                        <td><?=$mtd[0]->purpose_achieved?></td>
                                        <td><?=$md->rremark?><hr><?=$md->star?> Star</td>
                                          </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
                </fieldset>
            </div>
            <hr />
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
    
    <?php include('footer.php') ?>