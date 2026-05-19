<?php include("header.php");?>
<div class="card-body">
    <div class="container-fluid body-content">

        <div class="page-header">
            <div class="form-group">
                <fieldset>
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
                                            <th>Current Status</th>
                                            <th>Last Status</th>
                                            <th>Conversions</th>
                                            <th>Created Date</th>
                                            <th>Assigned Date</th>
                                            <th>Potential</th>
                                            <th>Top Spender</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalConversionLogs =$totallogs =0;
                                        foreach($BDWorkcompanies as $key=>$val){
                                                 $totalConversionLogs = $this->Menu_model->getTotalBDConversionList($apst,$sd,$ed,$val->cmpid_id);
                                                 $totallogs = count($totalConversionLogs);
                                                 ?>
                                            <tr>
                                                <td><?php echo $key+1;?></td>
                                                <td><?php echo getUserNameById($val->mainbd);?></td>
                                                <td><?php echo $val->cmpid_id;?></td>
                                                <td><a target ="_blank" href="<?php echo base_url()?>Menu/CompanyDetails/<?php echo $val->mainbd; ?>"><?php echo getCompanyNameByCmpid($val->cmpid_id);?></a></td>
                                                <td><?php echo getCStatusBystatusId($val->cstatus);?></th>
                                                <td><?php echo getCStatusBystatusId($val->lstatus);?></th>
                                                <?php if($totallogs >0){
                                                 ?>
                                                   <td><a target="_blank" href="<?php echo base_url()?>Menu/BDConversionsList/<?php echo $apst;?>/<?php echo $val->cmpid_id;?>"> <?php echo $totallogs;?></a></td>
                                                 <?php
                                                }
                                                else{ ?>
                                                 <td>0</td>
                                               <?php 
                                               }?>
                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->compcreateddate)); ?></td>
                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->intupdateddate)); ?></td>
                                                <td><?php echo $val->potential;?></td>
                                                <td><?php echo $val->topspender;?></td>
                                            </tr>
                                            <?php } ?>
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