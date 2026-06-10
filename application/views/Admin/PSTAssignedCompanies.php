<?php include("header.php");?>
<div class="card-body">
    <div class="container-fluid body-content">

        <div class="page-header">
            <div class="form-group">
                <fieldset>
                <div class="col-md-4"> 
                    <div class="dateform text-left">
                        <form class="p-3" method="POST" action="<?=base_url(); ?>/Menu/PSTassignedByDate/<?=$apst?>">
                            <input type="date" name="sdate" class="mr-2" value="<?=$sd ?>">
                            <input type="date" name="edate" class="mr-2" value="<?=$ed ?>">
                            <button type="submit" class="bg-primary text-light">Filter</button>
                        </form>
                    </div>
                </div>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>MainBD</th>
                                            <th>Company ID</th>
                                            <th>Company Name</th>
                                            <th>Task Logs Done By PST</th>
                                            <th>Task Logs Done By Others</th>
                                            <th>Conversions By PST</th>
                                            <th>Conversions By Others</th>
                                            <th>Created Date</th>
                                            <th>Assigned Date</th>
                                            <th>Potential</th>
                                            <th>Top Spender</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalConversionLogs =0;
                                        foreach($PSTWorkcompanies as $key=>$val){
                                                $totalConversionLogs  = $this->Menu_model->getTotalPSTConversionList($apst,$sd,$ed,$val->cmpid_id);
                                                $totallogs            = count($totalConversionLogs);
                                                $totalPSTTasksLogs    = $this->Menu_model->getTotalPSTTaskList($apst,$sd,$ed,$val->cmpid_id);
                                                $totaltasklogs        = count($totalPSTTasksLogs);

                                                $totalConversionLogsForOthers  = $this->Menu_model->getTotalPSTConversionListForOthers($apst,$sd,$ed,$val->cmpid_id);
                                                $totallogsForOthers            = count($totalConversionLogsForOthers);

                                                $totalPSTTasksLogsForOthers    = $this->Menu_model->getTotalPSTTaskListForOthers($apst,$sd,$ed,$val->cmpid_id);
                                                $totaltasklogsForOthers       = count($totalPSTTasksLogsForOthers);

                                                ?>
                                            <tr>
                                                <td><?php echo $key+1;?></td>
                                                <td><?php echo getUserNameById($val->mainbd);?></td>
                                                <td><?php echo $val->cmpid_id;?></td>
                                                <td><a target ="_blank" href="<?php echo base_url()?>Menu/CompanyDetails/<?php echo $val->apst; ?>"><?php echo getCompanyNameByCmpid($val->cmpid_id);?></a></td>
                                                <!-- <td><?php // echo getCStatusBystatusId($val->cstatus);?></th>
                                                <td><?php // echo getCStatusBystatusId($val->lstatus);?></th> -->
                                                <?php if($totaltasklogs >0){
                                                 ?>
                                                   <td><a target="_blank" href="<?php echo base_url()?>Menu/PSTTasksList/<?php echo $apst;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"> <?php echo $totaltasklogs;;?></a></td>
                                                 <?php
                                                }
                                                else{ ?>
                                                 <td>0</td>
                                                <?php 
                                                }
                                                ?>
                                                <td><a target="_blank" href="<?php echo base_url()?>Menu/PSTTasksListByOthers/<?php echo $apst;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"> <?php echo $totaltasklogsForOthers;;?></a></td>
                                                <?php
                                                if($totallogs >0){
                                                 ?>
                                                   <td><a target="_blank" href="<?php echo base_url()?>Menu/PSTConversionsList/<?php echo $apst;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"> <?php echo $totallogs;;?></a></td>
                                                 <?php
                                                }
                                                else{ ?>
                                                 <td>0</td>
                                               <?php 
                                               }?>
                                                   <td><a target="_blank" href="<?php echo base_url()?>Menu/PSTConversionsListByOthers/<?php echo $apst;?>/<?php echo $val->cmpid_id;?>/<?php echo $sd;?>/<?php echo $ed;?>"> <?php echo $totallogsForOthers;;?></a></td>

                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->compcreateddate)); ?></td>
                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->intupdateddate)); ?></td>
                                                <td><?php echo $val->potential;?></td>
                                                <td><?php echo $val->topspender;?></td>
                                            </tr>
                                            <?php }?>
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