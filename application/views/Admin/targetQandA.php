<?php //include_once('header.php');?>
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
                    <?php 
                     ?>
                    <div class="table-responsive">
                      <div class="table-responsive">
                        <div class="pdf-viwer">
                            <label>Target Data for <?php echo $username;?> For <?php echo $reviewType."  Review "; ?></label>
                                 <form class="row g-3" method="post" name="targetQandA" action="targetQandA">
                                     <div class="container">
                                      <div class="row">
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control m-2" name="sdate" value="" required="" id="sdate" min="">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control m-2" name="sdate" value="" required="" id="sdate" min="">
                                        </div>
                                         <div class="col-sm-3"> 
                                            <select class="form-select" name="partner_type">
                                                <option>Select Partner Type</option>
                                            <?php foreach($partnerType as $partnerValue){ ?>
                                                <option value="<?php echo $partnerValue->id;?>"><?php echo $partnerValue->name;?></option>
                                            <?php
                                            }?>
                                            </select>
                                        </div>
                                    </div>
                                      <div class="container">
                                    <div class="row">
                                    <div class="col-md-6">
                                        <label for="prospecting" class="form-label">Prospecting</label>
                                        <input type="number" class="form-control" name="prospecting" id="prospecting">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="revenue" class="form-label">Revenue</label>
                                        <input type="number" class="form-control" id="revenue" name="revenue">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="proposal" class="form-label">Proposal</label>
                                        <input type="number" class="form-control" id="proposal" name="proposal" placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="closure" class="form-label">Closure</label>
                                        <input type="number" class="form-control" id="closure" name="closure" placeholder="">
                                    </div>
                                    <div class="col-12">
                                         <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div></div>
                                    </div>
                                </form>
                          </div>
                        </div>
                       </div>
                    </div>
                  </div></div></div></div>
                </div>
               
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </section>
<?php //include_once('footer.php');?>
      