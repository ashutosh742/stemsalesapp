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
                                            <th>Task Type</th>
                                            <th>Company ID</th>
                                            <th>Company Name</th>
                                            <th>Current Status</th>
                                            <th>ConversionLogs</th>
                                            <th>Assigned Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach($CMWorkTasks as $key=>$val) {
                                            ?>
                                            <tr>
                                                <td><?php echo $key+1;?></td>
                                                <td><?php echo getUserNameById($val->mainbd);?></td>
                                                <th><?php echo $val->tasktype;?></th>
                                                <td><?php echo $val->cmpid_id;?></td>
                                                <td><a target ="_blank" href="<?php echo base_url()?>Menu/CompanyDetails/<?php echo $val->cmpid_id; ?>"><?php echo getCompanyNameByCmpid($val->cmpid_id);?></a></td>
                                                <th><?php echo getCStatusBystatusId($val->cstatus);?></th>
                                                <td><a target="_blank" href="<?php echo base_url()?>Menu/CMConversionsList/<?php echo $apstid;?>/<?php echo $val->cmpid_id;?>"> Conversionlogs</a></td>
                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->updated_at)); ?></td>
                                            </tr>
                                            <?php 
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