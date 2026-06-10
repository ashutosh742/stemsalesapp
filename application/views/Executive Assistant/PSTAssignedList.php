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
                                            <th>PST Name</th>
                                            <th>MainBD</th>
                                            <th>User Role</th>
                                            <th>Company ID</th>
                                            <th>Company Name</th>
                                            <th>Current Status</th>
                                            <th>Assigned Date</th>
                                            <th>Potential?</th>
                                            <th>Top Spender?</th>
                                            <th>MOM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($PSTWork as $key=>$val) {
                                    //    echo getUserNameById($val->apst);exit;
                                            ?>
                                            <tr>
                                                <td><?php echo $key+1;?></td>
                                                <td><?php echo getUserNameById($val->apst);?></td>
                                                <td><?php echo getUserNameById($val->mainbd);?></td>
                                                <td><?php echo getUserRoleById($val->type_id);?></td>
                                                <td><?php echo $val->CIN;?></td>
                                                <td><a target ="_blank" href="<?php echo base_url()?>Menu/CompanyDetails/<?php echo $val->CIN; ?>"><?php echo getCompanyNameByCmpid($val->CIN);?></a></td>
                                                <th><?php echo $val->cstatus;?></th>
                                                <td><?php echo date('d-m-Y h:i:s',strtotime($val->assigned_date)); ?></td>
                                                <td><?php echo $val->potential;?></td>
                                                <td><?php echo $val->topspender;?></td>
                                                <td><?php echo $val->rpmmom;?></td>
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