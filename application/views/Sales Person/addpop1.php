<?php $uid=$user['user_id'];
date_default_timezone_set("Asia/Kolkata");
?>
<div id="add_note" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
  
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-standard-title1"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div> <!-- // END .modal-header -->
        <div id="moshow" class="modal-body">
          <div class="card row m-2">
            <div class="col-12 col-md-12">
              <div class="card-header bg-info">Task Detail</div>
              <div class="card-body">
                Current Task : <lable id="ctname"></lable><br>
                Last Status :  <lable id="clsname"></lable><br>
                Last Task Remark : <lable id="cremarks"></lable>
              </div>
            </div>
          </div>
          <div class="card row m-2">
            <div class="col-12 col-md-12">
              <input type="hidden" value="" id="test">
              <a href="" target="_blank" id="cmplink"><div class="card-header bg-info"><lable id="cname"></lable></div></a>
              <div class="card-body">
                <div class="col-sm">Name : <lable id="cp"></lable></div>
                <div class="col-sm">Designation : <lable id="designation"></lable></div>
                <div class="col-sm">Phone No : <lable id="phoneno"></lable></div>
                <div class="col-sm">Email id : <lable id="emailid"></lable></div>
                <span>
                  <a id="clink" href="" target="_blank" style="padding:7px;border-radius:20px;">
                  <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;"></a>
                  <a id="wlink" href="" target="_blank" style="padding:7px;border-radius:20px;">
                  <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;"></a>
                  <a id="glink" href="" target="_blank" style="padding:7px;border-radius:20px;">
                  <img src="https://mailmeteor.com/logos/assets/PNG/Gmail_Logo_512px.png" style="height:20px;"></a>
                </span>
              </div>
            </div>
          </div>
          <div class="card row m-2" id="taskbox">
            <div class="col-12 col-md-12">
              <div class="card-header bg-info"></div>
              <div class="card-body">
                <form action="<?=base_url();?>Menu/submittask1" method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="ccstatus" id="cstatus">
                  <input type="hidden" name="action_id" id="action_id">
                  <input type="hidden" name="uid" value="<?=$uid?>">
                  <input type="hidden" name="cmpid" id="cmpid" value="">
                  <input type="hidden" name="tid" id="tidd" value="">
                  
                  <div class="text-center">
                    <span><b>Action Completed?</b></span>
                    <input type="radio" id="pending" name="actontaken" Value="yes" onclick="disableOtherRadioButtons('option')">
                    <label for="pending">YES</label>
                    <input type="radio" id="resolved" name="actontaken" Value="no" onclick="disableOtherRadioButtons('option')">
                    <label for="resolved">NO</label>
                  </div>
                  <div class="p-3" id="test1" style="display: none;">
                  </div>
                  <div class="p-3" id="test2" style="display: none;">
                    <span><b>Email Send?</b></span>
                    <input type="radio" id="yes" name="meeting" Value="yes">
                    <label for="yes">YES</label>
                    <input type="radio" id="no" name="meeting" Value="no">
                    <label for="no">NO</label><hr>
                  </div>
                  <div class="p-3" id="test3" style="display: none;">
                    <label>Attach Meeting Photo</label>
                  </div>
                  <div class="p-3" id="test5" style="display: none;">
                    <label>Attach Whatsapp Media</label>
                    <input type="file" class="form-control-file" name="filname1" required>
                    <textarea type="text" class="form-control" placeholder="Remark" required></textarea>
                  </div>
                  <div class="p-3" id="test6" style="display: none;">
                    <label>Write MOM</label>
                    <textarea type="text" class="form-control" id="rpmmom" name="rpmmom" rows="10" required></textarea>
                  </div>
                  
                  <div class="p-3" id="test8" style="display: none;">
                    <label>Social Networking</label>
                    <input type="text" class="form-control" name="LinkedIn" placeholder="LinkedIn Profile Link">
                    <input type="text" class="form-control" name="Facebook" placeholder="Facebook Profile Link">
                    <input type="text" class="form-control" name="YouTube" placeholder="YouTube Profile Link">
                    <input type="text" class="form-control" name="Instagram" placeholder="Instagram Profile Link">
                    <input type="text" class="form-control" name="OtherSocial" placeholder="Other Social Media Profile Link">
                  </div>
                  
                  <div class="p-3" id="test9" style="display: none;">
                    <label>Social Activity</label>
                    <label>Attach Social Media Post Screenshot</label>
                    <input type="file" class="form-control-file" name="filname2" required>
                    <textarea type="text" class="form-control" placeholder="Remark" required></textarea>
                  </div>
                  
                  <div class="p-3" id="test7" style="display: none;">
                    <label>Proposal Detail</label>
                    <select class="form-control" name="partner" required>
                      <option>NGO</option>
                      <option>STEM</option>
                      <option>Govt</option>
                    </select>
                    <input type="number" class="form-control" name="noofsc" placeholder="No of Schools" min="1" required>
                    <input type="number" class="form-control" name="pbudgetme" min="1" step="0.01" placeholder="Proposal Budget" required>
                    <label>Attach Proposal</label>
                    <input type="file" class="form-control-file" name="filname" required>
                  </div>
                  <div id="noremark" class="text-center">
                    <textarea type="text" class="form-control" id="norek" name="noremark" placeholder="Remark" required></textarea>
                  </div>
                  <div id="purpose" class="text-center">
                    Current Task Purpose : <h6 id="cpurpose"></h6>
                    <span><b>Purpose Completed?</b></span>
                    <input type="radio" id="presolved" name="purpose" Value="yes" onclick="disableOtherRadioButtons('option')">
                    <label for="pending">YES</label>
                    <input type="radio" id="pnresolved" name="purpose" Value="no" onclick="disableOtherRadioButtons('option')">
                    <label for="resolved">NO</label><hr>
                  </div>
                  
                  <div id="ifyes" style="display:none">
                    <div class="col-12 col-md-12 mb-3">
                      <label for="validationSample04">Next Status</label>
                      <select type="text" class="form-control" name="ystatus" placeholder="State" id="ystatus" required>
                      </select>
                    </div>
                    <div class="col-12 col-md-12 mb-12">
                      <label for="remark_msg">Remarks</label>
                      <textarea type="text" class="form-control" name="yremark_msg" id="remark_msg"  required></textarea>
                      <div class="invalid-feedback">.</div>
                      <div class="valid-feedback">Looks good!</div>
                    </div>
                  </div>
                  <div id="ifno" style="display:none">
                    <input type="hidden" name="yaction_id" id="yaction_id">
                    <div class="col-12 col-md-12 mb-3">
                      <label for="validationSample04">Remarks</label>
                      <select type="text" class="form-control" id="tremark" placeholder="Remarks" required>
                      </select>
                    </div>
                    <div class="col-12 col-md-12 mb-12">
                      <label for="remark_msg">Remarks</label>
                      <textarea type="text" class="form-control" id="re_mark" name="nremark_msg" readonly></textarea>
                      <div class="invalid-feedback">.</div>
                      <div class="valid-feedback">Looks good!</div>
                    </div>
                    <div class="col-12 col-md-12 mb-12">
                      <label>Next Action Date</label>
                      <input type="date" class="form-control" name='nadate' required>
                      <div class="invalid-feedback">.</div>
                      <div class="valid-feedback">Looks good!</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer" id="taskbtn">
              <button type="submit" class="btn btn-primary mt-3" id="button" onclick="this.form.submit(); this.disabled = true;">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </div> <!-- // END .modal-body -->
    
  </div></div></div>
  <!-- User details -->
  <div id="add_bim" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-standard-title1"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          </div> <!-- // END .modal-header -->
          <div class="modal-body">
            <div class="card card-form col-md-12">
              <div class="card-header bg-info text-center"><b>Create Barg in Meeting</b></div>
              <div class="row no-gutters">
                <div class="card-body">
                  <?=form_open('Menu/cbmeeting')?>
                  <select id="bcytpe" name="bcytpe" class="form-control mt-2">
                    <option value="">Select Bargin Company Type</option>
                    <option>From Funnel</option>
                    <option>Other</option>
                  </select>
                  <div id="bboxa" style="display: none;">
                    <!--<input list="bcname" name="bcid" class="form-control mt-2" type="text">-->
                    <!--<datalist id="bcname">-->
                    <!--    <option value="">Select Company Name</option>-->
                    <!--    <?php //$bdc=$this->Menu_model->get_cmpbybd($uid); -->
                    //foreach($bdc as $bc){?>
                    <!--    <option value="<?=$bc->id?>"><?=$bc->compname?></option>-->
                    <?php //}?>
                    <!--</datalist>-->
                    <br>
                    <select id="bcname" class="select2" class="form-control mt-2" name="bcid">
                      <option value="">Select Company Name</option>
                      <?php $bdc = $this->Menu_model->get_cmpbybd($uid);foreach ($bdc as $bc): ?>
                      <option value="<?= $bc->id ?>"><?=$bc->compname.'/'. $bc->id ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div id="bboxb" style="display: none;">
                    <lable>Meeting Location</lable>
                    <select id="mbolocation" class="form-control mt-2">
                      <option value="">Select Meeting Location</option>
                      <option>Base Location</option>
                      <option>Out Station</option>
                    </select>
                  </div>
                  <div id="bboxc" style="display: none;">
                    <select id="bstate" name="bstate" class="form-control mt-2">
                      <option value="">Select State</option>
                      <?php $state=$this->Menu_model->get_states();
                      foreach($state as $st){?>
                      <option value="<?=$st->id?>"><?=$st->state?></option>
                      <?php }?>
                    </select>
                    <select id="bcity" name="bcity" class="form-control mt-2">
                    </select>
                  </div>
                  <div class="form-control mt-2"><span><b>Plan Instant or Later?</b></span>
                  <input type="radio" id="Instant" name="piorl" Value="Instant">
                  <label for="Instant">Instant</label>
                  <input type="radio" id="Later" name="piorl" Value="Later">
                  <label for="Later">Later</label></div>
                  <div id="laterbox" style="display:none">
                    <input type="datetime-local" name="bmdate" class="form-control mt-2" value="<?=date('Y-m-d H:i:s')?>">
                  </div></div></div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        </div> <!-- // END .modal-body -->
      </div></div></div>
      
      <div id="Add_TPlan" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modal-standard-title1"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
              </div> <!-- // END .modal-header -->
              <div class="modal-body">
                <div class="card card-form col-md-12">
                  <div class="card-header bg-info text-center"><b>Outstation Travel Plan</b></div>
                  <div class="row no-gutters">
                    <div class="card-body">
                      <?=form_open('Menu/cbmeeting')?>
                      <input type="datetime-local" name="tpsdate" class="form-control mt-2">
                      <input type="datetime-local" name="tpedate" class="form-control mt-2">
                      <lable>How Many Location</lable>
                      <input type="number" name="tphmlocation" id="tphmlocation" class="form-control mt-2"><hr>
                      <div id="tpscity" class="p-1">
                        <div id="scload">
                          <div id="headshow"></div>
                          <select id="tpstate" name="tpstate" class="form-control mt-2">
                            <option value="">Select State</option>
                            <?php $state=$this->Menu_model->get_states();
                            foreach($state as $st){?>
                            <option value="<?=$st->id?>"><?=$st->state?></option>
                            <?php }?>
                          </select>
                          <select id="tpcity" name="tpcity" class="form-control mt-2">
                          </select>
                          <lable>How Many Bargin</lable>
                          <input type="number" name="tphmlocation" id="tphmlocation" class="form-control mt-2"><hr>
                        </div></div>
                        <div class="accordion" id="accordionExample">
                          <div class="card">
                            <div class="card-header" id="heading1">
                              <h2 class="mb-0">
                              <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#tpcollapse1" aria-expanded="true" aria-controls="tpcollapse1">
                              TYPE OF EXPENSES : Train
                              </button>
                              </h2>
                            </div>
                            
                            <div id="tpcollapse1" class="collapse show" aria-labelledby="heading1" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="heading2">
                              <h2 class="mb-0">
                              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse2" aria-expanded="false" aria-controls="tpcollapse2">
                              TYPE OF EXPENSES : Taxi
                              </button>
                              </h2>
                            </div>
                            <div id="tpcollapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                                
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="heading3">
                              <h2 class="mb-0">
                              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse3" aria-expanded="false" aria-controls="tpcollapse3">
                              CTYPE OF EXPENSES : Bus
                              </button>
                              </h2>
                            </div>
                            <div id="tpcollapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                                
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="heading4">
                              <h2 class="mb-0">
                              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse4" aria-expanded="false" aria-controls="tpcollapse4">
                              CTYPE OF EXPENSES : Tender Amount
                              </button>
                              </h2>
                            </div>
                            <div id="tpcollapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                                
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="heading5">
                              <h2 class="mb-0">
                              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse5" aria-expanded="false" aria-controls="tpcollapse5">
                              CTYPE OF EXPENSES : Ground Transportation
                              </button>
                              </h2>
                            </div>
                            <div id="tpcollapse5" class="collapse" aria-labelledby="heading5" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                                
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="heading6">
                              <h2 class="mb-0">
                              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse6" aria-expanded="false" aria-controls="tpcollapse6">
                              CTYPE OF EXPENSES : Lodging
                              </button>
                              </h2>
                            </div>
                            <div id="tpcollapse6" class="collapse" aria-labelledby="heading6" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                                
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="heading7">
                              <h2 class="mb-0">
                              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse7" aria-expanded="false" aria-controls="tpcollapse7">
                              CTYPE OF EXPENSES : Food
                              </button>
                              </h2>
                            </div>
                            <div id="tpcollapse7" class="collapse" aria-labelledby="heading7" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                                
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="heading8">
                              <h2 class="mb-0">
                              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse8" aria-expanded="false" aria-controls="tpcollapse8">
                              CTYPE OF EXPENSES : Miscellaneious
                              </button>
                              </h2>
                            </div>
                            <div id="tpcollapse8" class="collapse" aria-labelledby="heading8" data-parent="#accordionExample">
                              <div class="card-body">
                                <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                <input type="text" class="form-control" placeholder="NO OF DAYS">
                              </div>
                            </div>
                          </div>
                        </div>
                        
                      </div></div>
                      <div class="card-footer">
                        <button type="submit" class="btn btn-primary" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            </div> <!-- // END .modal-body -->
          </div></div></div>
          
          
          
          <!-- User details -->
          <div id="add_contact" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modal-standard-title1"></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                  </div> <!-- // END .modal-header -->
                  <div class="modal-body">
                    <div class="card card-form col-md-12">
                      <div class="row no-gutters">
                        <div class="col-lg-12 card-form__body card-body">
                          
                          <div >
                            <center><h5>Add Contact Detail</h5></center><hr>
                            <?=form_open('Menu/addcont')?>
                            <input type="hidden" id="sid" name="sid">
                            <input type="hidden" name="uid" value="<?=$uid?>">
                            <input type="text" name="contact_name" class="form-control p-3 mt-2" placeholder="Name">
                            <input type="text" name="designation" class="form-control  p-3  mt-2" placeholder="Designation">
                            <input type="text" name="contact_no" class="form-control  p-3  mt-2" placeholder="Contact No">
                            <input type="text" name="email" class="form-control  p-3  mt-2" placeholder="Email">
                            <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                          </form>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                  </div> <!-- // END .modal-body -->
                  
                </div></div></div>
                <!-- User details -->
                <div id="doaction" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modal-standard-title1"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        </div> <!-- // END .modal-header -->
                        <div class="modal-body">
                          <div class="card card-form col-md-12">
                            <div class="card-header bg-info">Create Plan</div>
                            <h6 class="text-center mt-1" id="cmpname"></h6>
                            <div class="col-lg-12 card-body">
                              <?php $today = date('Y-m-d H:i:s'); ?>
                              <?=form_open('Menu/dateplan')?>
                              <input type="hidden" name="uid"  id="uid" value="<?=$uid?>">
                              <input type="hidden" id="taskid" name="taskid">
                              <lable>Select Date Time</lable>
                              <input type="datetime-local" name="date" id="date" class="form-control p-3 mt-2 mb-2" placeholder="Date" min="<?=$today?>" value="<?=$today?>">
                              <div id="dateremaek"></div>
                              <button type="submit" id="planbtn" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div> <!-- // END .modal-body -->
                    
                  </div></div></div>
                  
                  
                  
                  
                  <div id="add_startm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modal-standard-title1"></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                          </div> <!-- // END .modal-header -->
                          <div class="modal-body">
                            <div class="card card-form col-md-12">
                              <div class="row no-gutters">
                                <div class="col-lg-12 card-form__body card-body">
                                  
                                  <div >
                                    <center><h5>Start Meeting</h5></center><hr>
                                    <?php date_default_timezone_set("Asia/Kolkata");?>
                                    <form action="<?=base_url();?>Menu/rpmstart" method="post" enctype="multipart/form-data">
                                      <input type="hidden" name="uid" value="<?=$uid?>">
                                      <input type="hidden" name="smid" value="" id="startmid">
                                      <input type="hidden" name="bscid" value="" id="bscid">
                                      <input type="hidden" id="slat" name="lat">
                                      <input type="hidden" id="slng" name="lng">
                                      <input type="hidden" name="startm" value="<?=date('Y-m-d H:i:s')?>">
                                      <center>Meeting Started at <?=date('H:i:s')?></center>
                                      <input type="text" name="company_name" class="form-control p-3 mt-2" id="bmcname">
                                      <input type="file" name="cphoto" accept="image/*" required class="form-control p-3 mt-2" capture="camera">
                                      <div id="location">
                                        <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
                                          <iframe style="width:100%;height:200px;" id="mylocation" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
                                        </div>
                                      </div>
                                      <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                                    </form>
                                  </div>
                                  
                                </div>
                              </div>
                            </div>
                            </div> <!-- // END .modal-body -->
                            
                          </div></div></div>
                          <div id="add_closem" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="modal-standard-title1"></h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                  </button>
                                  </div> <!-- // END .modal-header -->
                                  <div class="modal-body">
                                    <div class="card card-form col-md-12">
                                      <div class="row no-gutters">
                                        <div class="col-lg-12 card-form__body card-body">
                                          
                                          <div >
                                            <center><h5>Close Meeting</h5></center><hr>
                                            <?=form_open('Menu/rpmclose')?>
                                            <input type="hidden" name="uid" value="<?=$uid?>">
                                            <input type="hidden" name="cmid" value="" id="closemid">
                                            <input type="hidden" name="bmcid" value="" id="bmcid">
                                            <input type="hidden" name="bmccid" value="" id="bmccid">
                                            <input type="hidden" name="bminid" value="" id="bminid">
                                            <input type="hidden" name="bmtid" value="" id="bmtid">
                                            <input type="hidden" id="clat" name="lat">
                                            <input type="hidden" id="clng" name="lng">
                                            <input type="hidden" name="closem" value="<?=date('Y-m-d H:i:s')?>">
                                            <center>Meeting Closed at <?=date('H:i:s')?></center>
                                            <select name="type" id="RPMorN" class="form-control" required>
                                              <option value="NO RP">No RP Meeting</option>
                                              <option value="RP">RP Meeting</option>
                                              <option value="Only Got Detail">No RP But Got Details</option>
                                            </select>
                                            <div id="RPMbox" style="display:none">
                                              <lable>Company Address</lable>
                                              <input type="text" id="caddress" name="caddress" class="form-control p-3 mt-2">
                                              <lable>Contact Person Name</lable>
                                              <input type="text" id="cpname" name="cpname" class="form-control p-3 mt-2">
                                              <lable>Contact Person Designation</lable>
                                              <input type="text" id="cpdes" name="cpdes" class="form-control p-3 mt-2">
                                              <lable>Contact Person Mobile No</lable>
                                              <input type="text" id="cpno" name="cpno" class="form-control p-3 mt-2">
                                              <lable>Contact Person Email ID</lable>
                                              <input type="text" id="cpemail" name="cpemail" class="form-control p-3 mt-2">
                                              <select id="priority" name="priority" class="form-control" required>
                                                <option value="no">Non Priority</option>
                                                <option value="yes">Priority</option>
                                              </select>
                                            </div>
                                            
                                            <div id="location">
                                              <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
                                                <iframe style="width:100%;height:200px;" id="myclocation" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
                                              </div>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                                          </form>
                                        </div>
                                        
                                      </div>
                                    </div>
                                  </div>
                                  </div> <!-- // END .modal-body -->
                                  
                                </div></div></div>
                                
                                
                                <!-- Script -->
                                
                                <div id="alartpopup" class="modal fade in" role="dialog">
                                  <div class="modal-dialog">
                                    <div class="modal-content row border border-danger rounded">
                                      <div class="modal-header custom-modal-header">
                                        <button type="button" class="close" data-dismiss="modal">×</button>
                                      </div>
                                      <div class="modal-body">
                                        <h3 class="text-danger text-center">Alert!</h3>
                                        <div id="alsms" class="text-center"></div>
                                        <div class="m-3 text-right"><img src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" width="20%"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                
                                
                                
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
                                <script type="text/javascript">
                                $(document).ready(function() {
                                // $('.select2').select2();
                                $('#bcname').select2();
                                });
                                document.querySelector('#button').disabled = true;
                                $('#norek').on('change', function a() {
                                document.querySelector('#button').disabled = false;
                                });
                                $('#remark_msg').on('change', function a() {
                                document.querySelector('#button').disabled = false;
                                });
                                $('#re_mark').on('change', function a() {
                                document.querySelector('#button').disabled = false;
                                });
                                $('#rpmmom').on('change', function a() {
                                document.querySelector('#button').disabled = false;
                                });
                                function disableOtherRadioButtons(name) {
                                var radioButtons = document.getElementsByName(name);
                                for (var i = 0; i < radioButtons.length; i++) {
                                if (radioButtons[i].checked) {
                                continue;
                                }
                                radioButtons[i].disabled = true;
                                }
                                }
                                function mypopup() {
                                $.ajax({
                                url:'<?=base_url();?>Menu/alpopup',
                                type: "POST",
                                cache: false,
                                success: function (result){
                                var res = result;
                                if(res!=0){
                                $('#alartpopup').modal('show');
                                $("#alsms").html(result);
                                }
                                }
                                });
                                }
                                var sx = document.getElementById("slat");
                                var sy = document.getElementById("slng");
                                var cx = document.getElementById("clat");
                                var cy = document.getElementById("clng");
                                var z = document.getElementById("mylocation");
                                var y = document.getElementById("mylocation");
                                $(document).ready(function(){
                                if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(showPosition);
                                } else {
                                x.value = "Geolocation is not supported by this browser.";
                                }
                                });
                                function showPosition(position) {
                                sx.value = position.coords.latitude;
                                sy.value = position.coords.longitude;
                                cx.value = position.coords.latitude;
                                cy.value = position.coords.longitude;
                                var a = position.coords.latitude;
                                var b = position.coords.longitude;
                                mylocation.src = "https://maps.google.com/?q="+a+","+b+"&t=k&z=13&ie=UTF8&iwloc=&output=embed";
                                myclocation.src = "https://maps.google.com/?q="+a+","+b+"&t=k&z=13&ie=UTF8&iwloc=&output=embed";
                                }
                                
                                $(document).ready(function(){
                                $('#other_action').click(function(){
                                $('#doaction').modal('show');
                                var id = document.getElementById("other_action").value;
                                document.getElementById("taskid").value = id;
                                });
                                
                                });
                                
                                
                                </script>
                                
                                <script type='text/javascript'>
                                
                                $('#sel').on('change', function a() {
                                var sel = this.value;
                                if(sel=='Other'){
                                document.getElementById("remark").readOnly = false;
                                }else{
                                document.getElementById("remark").value = sel;}
                                });
                                $('#tphmlocation').on('change', function a() {
                                var tphl = this.value;
                                for(var i=1;i<tphl;i++){
                                document.getElementById("headshow").html="Day"+i;
                                var tpscity = document.getElementById("tpscity");
                                var scload = document.getElementById("scload");
                                tpscity.appendChild(scload.cloneNode(true));
                                
                                }
                                });
                                $('#date').on('change', function a() {
                                var date = this.value;
                                var uid = document.getElementById("uid").value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/setdateremark',
                                type: "POST",
                                data: {
                                date: date,
                                uid: uid
                                },
                                cache: false,
                                success: function a(result){
                                var res = result;
                                alert(res);
                                if(res==0){
                                $("#dateremaek").html("<p>Right Time To Do this</p>");
                                }else{
                                document.getElementById("date").value="";
                                $("#dateremaek").html("<b class='text-danger'>You Have Alrady Planed Some Other Task You Can Plan For Other Time<b>");
                                }
                                
                                }
                                });
                                });
                                $("#purpose").hide();
                                $("#noremark").hide();
                                let result = document.querySelector('#result');
                                document.body.addEventListener('change', function (e) {
                                let target = e.target;
                                let message;
                                switch (target.id) {
                                case 'pending':
                                
                                var ab = document.getElementById("action_id").value;
                                if(ab=="1"){
                                $("#purpose").show();
                                $("#test2").hide();
                                $("#test3").hide();
                                $("#test5").hide();
                                $("#test6").hide();
                                $("#test7").hide();
                                $("#test8").hide();
                                $("#test9").hide();
                                }
                                if(ab=="10"){
                                $("#purpose").show();
                                $("#test2").hide();
                                $("#test3").hide();
                                $("#test5").hide();
                                $("#test6").hide();
                                $("#test7").hide();
                                $("#test8").hide();
                                $("#test9").hide();
                                }
                                if(ab=="2"){
                                
                                document.querySelector('#button').disabled = false;
                                
                                $("#test1").hide();
                                $("#test2").show();
                                $("#test3").hide();
                                $("#test5").hide();
                                $("#test6").hide();
                                $("#test7").hide();
                                $("#test8").hide();
                                $("#test9").hide();
                                }
                                if(ab=="3"){
                                $("#test1").hide();
                                $("#test2").hide();
                                $("#test3").show();
                                $("#test5").hide();
                                $("#test6").hide();
                                $("#test7").hide();
                                $("#test8").hide();
                                $("#test9").hide();
                                }
                                if(ab=="5"){
                                document.querySelector('#button').disabled = false;
                                $("#test1").hide();
                                $("#test2").hide();
                                $("#test3").hide();
                                $("#test5").show();
                                $("#test6").hide();
                                $("#test7").hide();
                                $("#test8").hide();
                                $("#test9").hide();
                                }
                                if(ab=="6"){
                                $("#test1").hide();
                                $("#test2").hide();
                                $("#test3").hide();
                                $("#test5").hide();
                                $("#test6").show();
                                $("#test7").hide();
                                $("#test8").hide();
                                $("#test9").hide();
                                }
                                
                                if(ab=="13"){
                                document.querySelector('#button').disabled = false;
                                $("#test1").hide();
                                $("#test2").hide();
                                $("#test3").hide();
                                $("#test5").hide();
                                $("#test6").hide();
                                $("#test7").hide();
                                $("#test8").show();
                                $("#test9").hide();
                                }
                                if(ab=="14"){
                                document.querySelector('#button').disabled = false;
                                $("#test1").hide();
                                $("#test2").hide();
                                $("#test3").hide();
                                $("#test5").hide();
                                $("#test6").hide();
                                $("#test7").hide();
                                $("#test8").hide();
                                $("#test9").show();
                                }
                                
                                
                                
                                
                                
                                if(ab=="7"){
                                document.querySelector('#button').disabled = false;
                                $("#test1").hide();
                                $("#test2").hide();
                                $("#test3").hide();
                                $("#test5").hide();
                                $("#test6").hide();
                                $("#test7").show();
                                $("#test8").hide();
                                $("#test9").hide();
                                }
                                
                                
                                
                                $("#noremark").hide();
                                break;
                                case 'resolved':
                                $("#purpose").hide();
                                $("#ifyes").hide();
                                $("#ifno").hide();
                                $("#noremark").show();
                                break;
                                }
                                result.textContent = message;
                                });
                                $('#RPMorN').on('change', function b() {
                                var a =this.value;
                                if(a=='RP' || a=='Only Got Detail'){$("#RPMbox").show();}
                                else{$("#RPMbox").hide();}
                                });
                                $('#mbolocation').on('change', function b() {
                                var a =this.value;
                                if(a=='Base Location')
                                {
                                $("#bboxc").show();
                                }
                                else{
                                $("#bboxc").show();
                                }
                                });
                                $('#bcytpe').on('change', function b() {
                                var a =this.value;
                                if(a=='From Funnel')
                                {
                                $("#bboxa").show();
                                $("#bboxb").hide();
                                }
                                else{
                                $("#bboxb").show();
                                $("#bboxa").hide();
                                }
                                });
                                let resul = document.querySelector('#resul');
                                document.body.addEventListener('change', function (f) {
                                let target = f.target;
                                let message;
                                switch (target.id) {
                                case 'presolved':
                                $("#ifyes").show();
                                $("#ifno").hide();
                                var cstatus = document.getElementById("cstatus").value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/getstatusbd',
                                type: "POST",
                                data: {
                                cstatus: cstatus
                                },
                                cache: false,
                                success: function a(result){
                                $("#ystatus").html(result);
                                }
                                });
                                callab();
                                break;
                                case 'pnresolved':
                                $("#ifno").show();
                                $("#ifyes").hide();
                                var status_id = document.getElementById("cstatus").value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/mainremark',
                                type: "POST",
                                data: {
                                status_id: status_id
                                },
                                cache: false,
                                success: function a(result){
                                $("#tremark").html(result);
                                }
                                });
                                callab();
                                break;
                                }
                                resul.textContent = message;
                                });
                                
                                $('#testdata').on('change', function c() {
                                var ab = this.value;
                                document.getElementById("remark_msg").value = ab;
                                });
                                $('#tremark').on('change', function b() {
                                var tremark = document.getElementById("tremark").value;
                                if(tremark=='Other'){
                                document.getElementById("re_mark").value = '';
                                document.getElementById("re_mark").readOnly = false;}else{
                                document.getElementById("re_mark").readOnly = true;
                                }
                                });
                                $('#tremark').on('change', function b() {
                                var tremark = document.getElementById("tremark").value;
                                if(tremark!='Other'){document.getElementById("re_mark").value = tremark;}
                                });
                                $('#bstate').on('change', function f() {
                                var stid = this.value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/getcity',
                                type: "POST",
                                data: {
                                stid: stid
                                },
                                cache: false,
                                success: function a(result){
                                $("#bcity").html(result);
                                }
                                });
                                });
                                $('#tpstate').on('change', function f() {
                                var stid = this.value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/getcity',
                                type: "POST",
                                data: {
                                stid: stid
                                },
                                cache: false,
                                success: function a(result){
                                $("#tpcity").html(result);
                                }
                                });
                                });
                                $('#nextaction').on('change', function f() {
                                var action_id = document.getElementById("nextaction").value;
                                
                                $.ajax({
                                url:'<?=base_url();?>Menu/purpose',
                                type: "POST",
                                data: {
                                action_id: action_id
                                },
                                cache: false,
                                success: function a(result){
                                $("#nextpurpose").html(result);
                                }
                                });
                                });
                                $('#nextpurpose').on('change', function g() {
                                var purpose_id = document.getElementById("nextpurpose").value;
                                
                                $.ajax({
                                url:'<?=base_url();?>Menu/actionremark',
                                type: "POST",
                                data: {
                                purpose_id: purpose_id
                                },
                                cache: false,
                                success: function a(result){
                                $("#next_action_remark").html(result);
                                }
                                });
                                });
                                $('#next_action_remark').on('change', function c() {
                                var ab = this.value;
                                document.getElementById("next_action_remark_msg").value = ab;
                                });
                                </script>
                                <script type='text/javascript'>
                                $(document).ready(function(){
                                
                                
                                $('[id^="add_act"]').on('click', function() {
                                $('#add_note').modal('show');
                                var tid = this.value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/cctd',
                                method: 'post',
                                data: {tid: tid},
                                dataType: 'json',
                                success: function(response){
                                var len = response.length;
                                $('#cname,#ctname').text('');
                                if(len > 0){
                                
                                var cstatus = response[0].cstatus;
                                var cname = response[0].cname;
                                var ctname = response[0].ctname;
                                var clsname = response[0].clsname;
                                var cremarks = response[0].cremarks;
                                var cp = response[0].cp;
                                var emailid = response[0].emailid;
                                var phoneno = response[0].phoneno;
                                var designation = response[0].designation;
                                var cpurpose = response[0].cpurpose;
                                var actiontype_id = response[0].actiontype_id;
                                var nstatus = response[0].status_id;
                                var cmpid = response[0].cid_id;
                                var tidd = response[0].id;
                                var cmid = response[0].cmid;
                                document.getElementById("cstatus").value = cstatus;
                                document.getElementById("cname").innerHTML = cname;
                                document.getElementById("ctname").innerHTML = ctname;
                                document.getElementById("clsname").innerHTML = clsname;
                                document.getElementById("cremarks").innerHTML = cremarks;
                                document.getElementById("cp").innerHTML = cp;
                                document.getElementById("emailid").innerHTML = emailid;
                                document.getElementById("phoneno").innerHTML = phoneno;
                                document.getElementById("designation").innerHTML = designation;
                                document.getElementById("cpurpose").innerHTML = cpurpose;
                                document.getElementById("action_id").value = actiontype_id;
                                document.getElementById("yaction_id").value = actiontype_id;
                                document.getElementById("cmpid").value = cmpid;
                                document.getElementById("tidd").value = tidd;
                                document.getElementById("clink").href = "tel:+91"+phoneno;
                                document.getElementById("wlink").href = "https://wa.me/91"+phoneno;
                                document.getElementById("glink").href = "mailto:"+emailid;
                                document.getElementById("cmplink").href = "CompanyDetails/"+cmid;
                                //  alert(ctname);
                                if(ctname=='Call' || ctname=='Whatsapp Activity'){
                                //  alert(ctname);
                                //  document.getElementById("moshow").classList.add('d-lg-none');
                                //  document.getElementById("moshow").classList.add('d-sm-block');
                                //  document.getElementById("moshow").classList.add('d-md-none');
                                }
                                if(ctname=='Call'){
                                $("#taskbox").show();
                                $("#taskbtn").show();
                                }
                                }
                                }
                                });
                                });
                                
                                
                                $('[id^="add_plan"]').on('click', function() {
                                $('#doaction').modal('show');
                                var tid = this.value;
                                document.getElementById("taskid").value = tid;
                                $.ajax({
                                url:'<?=base_url();?>Menu/cctd',
                                method: 'post',
                                data: {tid: tid},
                                dataType: 'json',
                                success: function(response){
                                var len = response.length;
                                $('#cname').text('');
                                if(len > 0){
                                // Read values
                                var cname = response[0].cname;
                                document.getElementById("cmpname").innerHTML = cname;
                                }
                                }
                                });
                                });
                                $('[id^="startm"]').on('click', function() {
                                $('#add_startm').modal('show');
                                var id = this.value;
                                document.getElementById("startmid").value=id;
                                $.ajax({
                                url:'<?=base_url();?>Menu/bmtd',
                                method: 'post',
                                data: {id: id},
                                dataType: 'json',
                                success: function(response){
                                var len = response.length;
                                $('#compname,#cid').text('');
                                if(len > 0){
                                var compname = response[0].compname;
                                var cid = response[0].cid;
                                document.getElementById("bmcname").value = compname;
                                document.getElementById("bscid").value = cid;
                                }
                                }
                                });
                                });
                                $('[id^="closem"]').on('click', function() {
                                $('#add_closem').modal('show');
                                var id = this.value;
                                document.getElementById("closemid").value=id;
                                $.ajax({
                                url:'<?=base_url();?>Menu/bmtd',
                                method: 'post',
                                data: {id: id},
                                dataType: 'json',
                                success: function(response){
                                var len = response.length;
                                $('#compname,#cid,#ccid,#inid,#tid').text('');
                                
                                if(len > 0){
                                
                                var compname = response[0].compname;
                                var cid = response[0].cid;
                                var ccid = response[0].ccid;
                                var inid = response[0].inid;
                                var tid = response[0].tid;
                                var address = response[0].address;
                                var contactperson = response[0].contactperson;
                                var designation = response[0].designation;
                                var phoneno = response[0].phoneno;
                                var emailid = response[0].emailid;
                                document.getElementById("bmcname").value = compname;
                                document.getElementById("bmcid").value = cid;
                                document.getElementById("bmccid").value = ccid;
                                document.getElementById("bminid").value = inid;
                                document.getElementById("bmtid").value = tid;
                                document.getElementById("caddress").value = address;
                                document.getElementById("cpname").value = contactperson;
                                document.getElementById("cpdes").value = designation;
                                document.getElementById("cpno").value = phoneno;
                                document.getElementById("cpemail").value = emailid;
                                }
                                }
                                });
                                });
                                $('#tplan').click(function(){
                                $('#Add_TPlan').modal('show');
                                let result = document.querySelector('#result');
                                document.body.addEventListener('change', function (e) {
                                let target = e.target;
                                let message;
                                switch (target.id) {
                                case 'Instant':
                                $("#laterbox").hide();
                                break;
                                case 'Later':
                                $("#laterbox").show();
                                break;
                                }
                                result.textContent = message;
                                });
                                });
                                
                                
                                $('#cbim').click(function(){
                                $('#add_bim').modal('show');
                                let result = document.querySelector('#result');
                                document.body.addEventListener('change', function (e) {
                                let target = e.target;
                                let message;
                                switch (target.id) {
                                case 'Instant':
                                $("#laterbox").hide();
                                break;
                                case 'Later':
                                $("#laterbox").show();
                                break;
                                }
                                result.textContent = message;
                                });
                                });
                                
                                $('#wlink').click(function(){
                                var tid = document.getElementById("tidd").value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/indtime',
                                method: 'post',
                                data: {tid: tid},
                                dataType: 'json',
                                success: function(response){
                                var len = response.length;
                                
                                }
                                });
                                });
                                
                                
                                $('#glink').click(function(){
                                var tid = document.getElementById("tidd").value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/indtime',
                                method: 'post',
                                data: {tid: tid},
                                dataType: 'json',
                                success: function(response){
                                var len = response.length;
                                
                                }
                                });
                                });
                                
                                $('#clink').click(function(){
                                var tid = document.getElementById("tidd").value;
                                $.ajax({
                                url:'<?=base_url();?>Menu/indtime',
                                method: 'post',
                                data: {tid: tid},
                                dataType: 'json',
                                success: function(response){
                                var len = response.length;
                                }
                                });
                                $("#taskbox").show();
                                $("#taskbtn").show();
                                });
                                
                                });
                                
                                
                                $(function() {
                                $('.pop').on('click', function() {
                                $('.imagepreview').attr('src', $(this).find('img').attr('src'));
                                $('#imagemodal').modal('show');
                                });
                                });
                                </script>
                                
                                <script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
                                <script>
                                ClassicEditor
                                .create( document.querySelector( '#editor' ) )
                                .catch( error => {
                                console.error( error );
                                } );
                                </script>