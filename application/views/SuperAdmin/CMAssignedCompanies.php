<?php include("header.php");?>
<div class="card-body">
    <div class="container-fluid body-content">

        <div class="page-header">
            <div class="form-group">
                <fieldset>
                <div class="col-md-4"> 
                                    <div class="dateform text-left">
                                    <form class="p-3" method="POST" action="<?=base_url(); ?>/Menu/CMassignedByDate/<?=$clmid ?>">
                                         <input type="date" name="sdate" class="mr-2" value="<?=$sd ?>">
                                         <input type="date" name="edate" class="mr-2" value="<?=$ed ?>">
                                         <button type="submit" class="bg-primary text-light">Filter</button>
                                    </form>
                                    </div>
                                  </div>
                             </div>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive text-nowrap">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>MainBD</th>      
                                            <th>CM Name</th>              
                                            <th>Company ID</th>
                                            <th>Company Name</th>
                                            <th>Company Status</th>
                                            <th>Task Logs BY CM</th>
                                            <th>Task Logs by Others</th>
                                            <th>Conversions By CM</th>
                                            <th>Conversions By Others </th>
                                            <th>Created Date</th>
                                            <th>Assigned Date</th>
                                            <th>Potential</th>
                                            <th>Top Spender</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalConversionLogs =0;
                                    //    dd($CMWorkcompanies);
                                        foreach($CMWorkcompanies as $key=>$val){ 
                                             $totalConversionLogs = $this->Menu_model->getTotalCMConversionList($clmid,$sd,$ed,$val->cmpid_id);
                                             $totallogs           = count($totalConversionLogs);
                                             $totalCMTasksLogs    = $this->Menu_model->getTotalCMTaskList($clmid,$sd,$ed,$val->cmpid_id);
                                             $totaltasklogs       = count($totalCMTasksLogs);
                                             $totalConversionLogsByOthers = $this->Menu_model->getTotalCMConversionListByOthers($clmid,$sd,$ed,$val->cmpid_id);
                                             $totallogsByOthers           = count($totalConversionLogsByOthers);
                                             $totalCMTasksLogsByOthers    = $this->Menu_model->getTotalCMTaskListByOthers($clmid,$sd,$ed,$val->cmpid_id);
                                             $totaltasklogsByOthers       = count($totalCMTasksLogsByOthers);
                                             if($totallogs>0){
                                            ?>
                                            <tr>
                                                <td><?php echo $key+1;?></td>
                                                <td><?php echo getUserNameById($val->mainbd);?></td>
                                                <td><?php echo getUserNameById($clmid);?></td>

                                                <td><?php echo $val->cmpid_id;?></td>
                                                <td><a target ="_blank" href="<?php echo base_url()?>Menu/CompanyDetails/<?php echo $val->cmpid_id; ?>"><?php echo getCompanyNameByCmpid($val->cmpid_id);?></a></td>
                                                <td><?php echo getCStatusBystatusId($val->cstatus);?></th>
                                                <td><a target="_blank" href="<?php echo base_url()?>Menu/CMTasksList/<?php echo $clmid;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"><?php echo $totaltasklogs;?></a></td>
                                                <td><a target="_blank" href="<?php echo base_url()?>Menu/TasksListByOthers/<?php echo $clmid;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"><?php echo $totaltasklogsByOthers;?></a></td>

                                                <td><a target="_blank" href="<?php echo base_url()?>Menu/CMConversionsList/<?php echo $clmid;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"> <?php echo $totallogs;?></a></td>
                                                <td><a target="_blank" href="<?php echo base_url()?>Menu/ConversionsListByOthers/<?php echo $clmid;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"> <?php echo $totallogsByOthers;?></a></td>
                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->compcreateddate)); ?></td>
                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->intupdateddate)); ?></td>
                                                <td><?php echo $val->potential;?></td>
                                                <td><?php echo $val->topspender;?></td>
                                            </tr>
                                            <?php }
                                         } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </form>            <!--END OF FORM ^^-->
                </fieldset>
            </div>
                <hr />
        </div>
    </div>
</div>
    <!-- /.card-body -->
</div>
<?php include("footer.php"); ?>