<?php $uid=$user['user_id'];?>

<div id="add_note" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title1"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <div id="noprc"></div>
                                <div class="card row m-2" id="dv1">
                                  <div class="col-12 col-md-12">
                                    <div class="card-header bg-info">Task Detail</div>
                                    <div class="card-body">
                                        Current Task : <lable id="ctname"></lable><br>
                                        Last Status :  <lable id="clsname"></lable><br>
                                        Last Task Remark : <lable id="cremarks"></lable>
                                    </div>
                                  </div>
                                </div>
                                <div class="card row m-2" id="dv2">
                                  <div class="col-12 col-md-12">
                                    <div class="card-header bg-info"><a id="cnlink" target="_blank" href=""><lable id="cname"></lable></a></div>
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
                                <div class="card row m-2">
                                  <div class="col-12 col-md-12">
                                    <div class="card-header bg-info">MOM Detail</div>
                                    <div class="card-body">
                                        Meeting Date :  <lable id="ltdate"></lable><br>
                                        BD Name : <lable id="bdname"></lable><br>
                                        MOM :  <lable id="mmom"></lable><br>
                                    </div>
                                  </div>
                                </div>
                                <div class="card row m-2">
                                  <div class="col-12 col-md-12">
                                    <div class="card-header bg-info"></div>
                                    <div class="card-body">
                                        <form action="<?=base_url();?>Menu/submittask" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="ccstatus" id="cstatus">
                                        <input type="hidden" name="action_id" id="action_id">
                                         <input type="hidden" name="uid" value="<?=$uid?>">
                                        <input type="hidden" name="cmpid" id="cmpid" value="">
                                        <input type="hidden" name="tid" id="tidd" value="">
                                        <div class="text-center">
                                        <span><b>Action Completed?</b></span>
                                        <input type="radio" id="pending" name="actontaken" Value="yes"> 
                                        <label for="pending">YES</label>
                                        <input type="radio" id="resolved" name="actontaken" Value="no"> 
                                        <label for="resolved">NO</label>
                                        </div>
                                        <div id="noremark" class="text-center">
                                            <textarea type="text" class="form-control" name="noremark" placeholder="Remark"></textarea>
                                        </div>
                                        <div id="purpose" class="text-center">
                                        Current Task Purpose : <h6 id="cpurpose"></h6>
                                        <span><b>Purpose Completed?</b></span>
                                        <input type="radio" id="presolved" name="purpose" Value="yes"> 
                                        <label for="pending">YES</label>
                                        <input type="radio" id="pnresolved" name="purpose" Value="no"> 
                                        <label for="resolved">NO</label><hr>
                                        </div>
                                        <div class="row md-12 p-3" id="test1" style="display: none;">
                                                </div>
                                                <div class="row md-12 p-3" id="test2" style="display: none;">
                                                    <label>Attach Meeting Photo</label>
                                                    <input type="file" class="form-control-file" required>
                                                </div>
                                                <div class="row md-12 p-3" id="test3" style="display: none;">
                                                    <span><b>Email Send?</b></span>
                                                    <input type="radio" id="yes" name="meeting" Value="yes"> 
                                                    <label for="yes">YES</label>
                                                    <input type="radio" id="no" name="meeting" Value="no"> 
                                                    <label for="no">NO</label><hr>
                                                </div>
                                                <div class="row md-12 p-3" id="test4" style="display: none;">
                                                    <label>Attach Whatsapp Media</label>
                                                    <input type="file" class="form-control-file" required>
                                                </div>
                                                <div class="row md-12 p-3" id="test5" style="display: none;">
                                                    <label>Write MOM</label>
                                                    <textarea type="text" id="editor" class="form-control" name="rpmmom" required></textarea>
                                                </div>
                                                <div class="row md-12 p-3" id="test6" style="display: none;">
                                                    <label>Attach Whatsapp Media</label>
                                                    <input type="file" class="form-control-file" required>
                                                </div>
                                                <div class="row md-12 p-3" id="test7" style="display: none;">
                                                    <label>Attach Proposal</label>
                                                    <input type="file" class="form-control-file" name="filname">
                                                    <section class="progress-area"></section>
                                                    <section class="uploaded-area"></section>
                                                </div>
                                         
                                        <div id="ifyes" style="display:none">
                                            
                                            
                                        <div class="col-12 col-md-12 mb-3">
                                            <label for="validationSample04">Next Status</label>
                                            <select type="text" class="form-control" name="ystatus" placeholder="State" id="ystatus" required>
                                                <option value="">Select Status</option>
                                                <?php $allst=$this->Menu_model->get_status();
                                                foreach($allst as $als){?>
                                                    <option value="<?=$als->id?>"><?=$als->name?></option>
                                                    <?php }?>
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
                                            <input type="hidden" name="nstatus" id="nstatus">
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
                                            
                                        </div>
                                        </div>
                                        
                                        </div>
                                        
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                                        </div>

                                </form>
                                </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
                
</div></div></div>




<div id="task_comp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title1"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <div id="noprc"></div>
                                <div class="card row m-2" id="dv1">
                                  <div class="col-12 col-md-12">
                                    <div class="card-header bg-info">Completed Task Detail</div>
                                    <div class="card-body">
                                        <b>Current name : </b><lable id="ccname"></lable><br>
                                        <b>Task By : </b><lable id="ctby"></lable><br>
                                        <b>Current Task : </b><lable id="cctname"></lable><br>
                                        <b>Current Status :  </b><lable id="cclsname"></lable><br>
                                        <b>Current Task Remark : </b><lable id="ccremarks"></lable>
                                    </div>
                                  </div>
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
                        <div class="card-header">Create Barg in Meeting</div>
                        <div class="row no-gutters">
                           <div class="col-lg-12 card-form__body card-body">
                            <?=form_open('Menu/cbmeeting')?>
                                <div>
                                    <lable>Meeting Location</lable>
                                    <select id="mbolocation" class="form-control">
                                        <option value="">Select Meeting Location</option>
                                        <option>Base Location</option>
                                        <option>Out Station</option>
                                    </select>
                                </div>
                                <input type= "text" id="mlocation" name="mlocation" class="form-control" readonly>
                                <div><span><b>Plan Instant or Later?</b></span>
                                <input type="radio" id="Instant" name="piorl" Value="Instant"> 
                                <label for="Instant">Instant</label>
                                <input type="radio" id="Later" name="piorl" Value="Later"> 
                                <label for="Later">Later</label></div>
                                <div id="laterbox" style="display:none">
                                    <input type="datetime-local" name="bmdate" class="form-control">
                                    <input type="number" name="bmno" placeholder="How May Barg in Meeting" class="form-control">
                                </div></div>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                           </div>
                        </div>
                    </div>
                </div>
            </div> <!-- // END .modal-body -->
            </div></div></div>
                
                
                
                
<!-- User details -->
<div id="add_crt" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                        <div class="card-header">Create Review Task</div>
                        <div class="row no-gutters">
                           <div class="col-lg-12 card-form__body card-body">
                            <?=form_open('Menu/creatert')?>
                                <div>
                                    <?php 
                                    date_default_timezone_set("Asia/Calcutta");
                                    $rtdt=date('Y-m-d H:m:s')?>
                                    <lable>Select Date</lable>
                                    <input type= "datetime-local" id="rtdatet" name="rtdatet" class="form-control" value="<?=$rtdt?>">
                                    <lable>Task Type</lable>
                                    <select name="tasktype" class="form-control">
                                        <option value="">Review Task</option>
                                        <option value="">Roster Task</option>
                                    </select>
                                    <lable>Select BD Name</lable>
                                    <select name="bdid" class="form-control">
                                        <option value="">Select BD Name</option>
                                        <?php
                                        $u=45;
                                        $bdd = $this->Menu_model->get_userbyaid($u);
                                        foreach ($bdd as $bdd){?>
                                        <option value="<?=$bdd->user_id?>"><?=$bdd->name?></option>
                                        <?php } ?>
                                        <lable>Meeting Link</lable>
                                        <input type="text" name="meetinglink" class="form-control">
                                        <input type="hidden" name="pstid" value="<?=$uid?>">
                                    </select>
                                </div>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
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
                           <input type="hidden" id="taskid" name="taskid">
                           <lable>Select Date Time</lable>
                           <input type="datetime-local" name="date" class="form-control p-3 mt-2 mb-2" placeholder="Date" value="<?=$today?>">
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
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
   <?=form_open('Menu/rpmstart')?>
   <input type="hidden" name="uid" value="<?=$uid?>">
   <input type="hidden" name="smid" value="" id="startmid">
   <input type="hidden" id="slat" name="lat">
   <input type="hidden" id="slng" name="lng">
   <input type="hidden" name="startm" value="<?=date('Y-m-d H:i:s')?>">
   <center>Meeting Started at <?=date('H:i:s')?></center>
   <input type="text" name="company_name" class="form-control p-3 mt-2" placeholder="Comapny Name">
   <input type="file" name="cphoto" class="form-control  p-3  mt-2">
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
   <input type="text" name="uid" value="<?=$uid?>">
   <input type="text" name="cmid" value="" id="closemid">
   <input type="text" id="clat" name="lat">
   <input type="text" id="clng" name="lng">
   <input type="text" name="closem" value="<?=date('Y-m-d H:i:s')?>">
   <center>Meeting Closed at <?=date('H:i:s')?></center>
    <select name="type" id="RPMorN" class="form-control" required>
        <option value="">Select Type of Meeting</option>
        <option value="Close">No RP Meeting</option>
        <option value="RPClose">RP Meeting</option>
    </select>
    <div id="RPMbox" style="display:none">
        <input type="text" name="caddress" class="form-control p-3 mt-2" placeholder="Comapny Address">
        <input type="text" name="cpname" class="form-control p-3 mt-2" placeholder="Contact Person Name">
        <input type="text" name="cpdes" class="form-control p-3 mt-2" placeholder="Contact Person Designation">
        <input type="text" name="cpno" class="form-control p-3 mt-2" placeholder="Contact Person Mobile No">
        <input type="text" name="cpemail" class="form-control p-3 mt-2" placeholder="Contact Person Email ID">
        <select id="priority" name="priority" class="form-control" required>
        <option value="">Select Priority</option>
        <option value="yes">Priority</option>
        <option value="no">Non Priority</option>
        </select>
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
  



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">

var sx = document.getElementById("slat");
var sy = document.getElementById("slng");
var cx = document.getElementById("clat");
var cy = document.getElementById("clng");
$(document).ready(function(){
    if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
    sx.value = "Geolocation is not supported by this browser.";
  }
});
function showPosition(position) {
  sx.value = position.coords.latitude; 
  sy.value = position.coords.longitude;
  cx.value = position.coords.latitude; 
  cy.value = position.coords.longitude;
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
               }
               if(ab=="2"){
                $("#test1").hide();
                $("#test2").hide();
                $("#test3").show();
                $("#test5").hide();
                $("#test6").hide();
                $("#test7").hide();
               }
               if(ab=="3"){
                $("#test1").hide();
                $("#test2").hide();
                $("#test3").show();
                $("#test5").hide();
                $("#test6").hide();
                $("#test7").hide();
               }
               if(ab=="5"){
                $("#test1").hide();
                $("#test2").hide();
                $("#test3").hide();
                $("#test5").show();
                $("#test6").hide();
                $("#test7").hide();
               }
               if(ab=="6"){
                $("#test1").hide();
                $("#test2").hide();
                $("#test3").hide();
                $("#test5").hide();
                $("#test6").show();
                $("#test7").hide();
               }
               if(ab=="7"){
                $("#test1").hide();
                $("#test2").hide();
                $("#test3").hide();
                $("#test5").hide();
                $("#test6").hide();
                $("#test7").show();
               }
            $("#noremark").hide();
            break;
        case 'resolved':
            $("#purpose").hide();
                $("#test1").hide();
                $("#test2").hide();
                $("#test3").hide();
                $("#test5").hide();
                $("#test6").hide();
                $("#test7").hide();
                $("#ifyes").hide();
            $("#noremark").show();
            break;
    }
    result.textContent = message;
});


$('#RPMorN').on('change', function b() {
    var a =this.value;
    if(a=='RPClose'){$("#RPMbox").show();}
    else{$("#RPMbox").hide();}
});


$('#mbolocation').on('change', function b() {
    var a =this.value;
    if(a=='Base Location')
    {document.getElementById("mlocation").readOnly = true;
        document.getElementById("mlocation").value=a;
    }
    else{document.getElementById("mlocation").value = "";
        document.getElementById("mlocation").readOnly = false;}
});


let resul = document.querySelector('#resul');
document.body.addEventListener('change', function (f) {
    let target = f.target;
    let message;
    switch (target.id) {
        case 'presolved':
            $("#ifyes").show();
            $("#ifno").hide();
            callab();
            break;
        case 'pnresolved':
            $("#ifno").show();
            $("#ifyes").hide();
                var status_id = document.getElementById("nstatus").value;
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



function callab(){

}
   
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
   $('#add_cont').click(function(){
    $('#add_contact').modal('show');
    var id = document.getElementById("add_cont").value;
    $.ajax({
     url:'<?=base_url();?>Menu/sd',
     method: 'post',
     data: {id: id},
     dataType: 'json',
     success: function(response){
       var len = response.length;
       $('#id,#suname,#sname,#semail').text('');
       if(len > 0){
         // Read values
         var id = response[0].id;
         var uname = response[0].sname;
         var name = response[0].slocation;
         var email = response[0].email;
 
         document.getElementById("sid").value = id;
         document.getElementById("suname").value = uname;
         document.getElementById("sname").value = name;
         document.getElementById("semail").value = email;
 
       }
 
     }
   });
  });
  
  
$('[id^="comp_task"]').on('click', function() {
    $('#task_comp').modal('show');
    var tid = this.value;
        $.ajax({
         url:'<?=base_url();?>Menu/cctd',
         method: 'post',
         data: {tid: tid},
         dataType: 'json',
         success: function(response){
           var len = response.length;
           $('#cname,#ctname,#bdname').text('');
           if(len > 0){
             var ccname = response[0].cname;
             var cctname = response[0].ctname;
             var cclsname = response[0].clsname;
             var ccremarks = response[0].cremarks;
             var ctby = response[0].cremarks;
             document.getElementById("ccname").innerHTML = ccname;
             document.getElementById("cctname").innerHTML = cctname;
             document.getElementById("cclsname").innerHTML = cclsname;
             document.getElementById("ccremarks").innerHTML = ccremarks;
             document.getElementById("ctby").innerHTML = ccremarks;
           }
         }
       });
  });
  

  

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
           $('#cname,#ctname,#bdname').text('');
           if(len > 0){
             var ltdate = response[0].ltdate;  
             var comid = response[0].comid;
             var cname = response[0].cname;
             var ctname = response[0].ctname;
             var clsname = response[0].clsname;
             var cremarks = response[0].cremarks;
             var cp = response[0].cp;
             var emailid = response[0].emailid;
             var phoneno = response[0].phoneno;
             var designation = response[0].designation;
             var cpurpose = response[0].cpurpose;
             var action_id = response[0].actiontype_id;
             var nstatus = response[0].status_id;
             var cmpid = response[0].cid_id;
             var tidd = response[0].id;
             var bdname = response[0].bdname;
             var mmom = response[0].mmom;
             var cstatus = response[0].cstatus;
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
             document.getElementById("action_id").value = action_id;
             document.getElementById("yaction_id").value = action_id;
             document.getElementById("cmpid").value = cmpid;
             document.getElementById("tidd").value = tidd;
             document.getElementById("nstatus").value = nstatus;
             document.getElementById("clink").href = "tel:+91"+phoneno;
             document.getElementById("wlink").href = "https://wa.me/91"+phoneno;
             document.getElementById("glink").href = "mailto:"+emailid;
             document.getElementById("bdname").innerHTML = bdname;
             document.getElementById("mmom").innerHTML = mmom;
             document.getElementById("cnlink").href = "CompanyDetails/"+comid;
             document.getElementById("ltdate").innerHTML = ltdate;
           }else{
             document.getElementById("cstatus").innerHTML = "";
             document.getElementById("cname").innerHTML = "";
             document.getElementById("ctname").innerHTML = "";
             document.getElementById("clsname").innerHTML = "";
             document.getElementById("cremarks").innerHTML = "";
             document.getElementById("cp").innerHTML = "";
             document.getElementById("emailid").innerHTML = "";
             document.getElementById("phoneno").innerHTML = "";
             document.getElementById("designation").innerHTML = "";
             document.getElementById("cpurpose").innerHTML = "";
             document.getElementById("action_id").value = "";
             document.getElementById("yaction_id").value = "";
             document.getElementById("cmpid").value = "";
             document.getElementById("tidd").value = "";
             document.getElementById("nstatus").value = "";
             document.getElementById("clink").href = "";
             document.getElementById("wlink").href = "";
             document.getElementById("glink").href = "";
             document.getElementById("bdname").innerHTML = "";
             document.getElementById("mmom").innerHTML = "";
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
  
  
  $('#startm').click(function(){
    $('#add_startm').modal('show');
    var id = document.getElementById("startm").value;
    document.getElementById("startmid").value=id;
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
  });
  
  
  $('#closem').click(function(){
    $('#add_closem').modal('show');
    var id = document.getElementById("closem").value;
    document.getElementById("closemid").value=id;
  });
  
  
  $('#crt').click(function(){
    $('#add_crt').modal('show');
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
  
  
  $('#add_wag').click(function(){
    $('#add_whatsapp').modal('show');
    var id = document.getElementById("add_wag").value;
    $.ajax({
     url:'<?=base_url();?>Menu/sd',
     method: 'post',
     data: {id: id},
     dataType: 'json',
     success: function(response){
       var len = response.length;
       $('#id,#suname,#sname,#semail').text('');
       if(len > 0){
         // Read values
         var id = response[0].id;
         var uname = response[0].sname;
         var name = response[0].slocation;
         var email = response[0].email;
 
         document.getElementById("wsid").value = id;
         document.getElementById("suname").value = uname;
         document.getElementById("sname").value = name;
         document.getElementById("semail").value = email;
 
       }
 
     }
   });
  });
  

 });
 
 
 $(function() {
		$('.pop').on('click', function() {
			$('.imagepreview').attr('src', $(this).find('img').attr('src'));
			$('#imagemodal').modal('show');   
		});		
});



const form = document.querySelector("form"),
fileInput = document.querySelector(".file-input"),
progressArea = document.querySelector(".progress-area"),
uploadedArea = document.querySelector(".uploaded-area");



fileInput.onchange = ({target})=>{
  let file = target.files[0];
  if(file){
    let fileName = file.name;
    if(fileName.length >= 12){
      let splitName = fileName.split('.');
      fileName = splitName[0].substring(0, 13) + "... ." + splitName[1];
    }
    uploadFile(fileName);
  }
}

function uploadFile(name){
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/upload.php");
  xhr.upload.addEventListener("progress", ({loaded, total}) =>{
    let fileLoaded = Math.floor((loaded / total) * 100);
    let fileTotal = Math.floor(total / 1000);
    let fileSize;
    (fileTotal < 1024) ? fileSize = fileTotal + " KB" : fileSize = (loaded / (1024*1024)).toFixed(2) + " MB";
    let progressHTML = `<li class="row">
                          <i class="fas fa-file-alt"></i>
                          <div class="content">
                            <div class="details">
                              <span class="name">${name} • Uploading</span>
                              <span class="percent">${fileLoaded}%</span>
                            </div>
                            <div class="progress-bar">
                              <div class="progress" style="width: ${fileLoaded}%"></div>
                            </div>
                          </div>
                        </li>`;
    uploadedArea.classList.add("onprogress");
    progressArea.innerHTML = progressHTML;
    if(loaded == total){
      progressArea.innerHTML = "";
      let uploadedHTML = `<li class="row">
                            <div class="content upload">
                              <i class="fas fa-file-alt"></i>
                              <div class="details">
                                <span class="name">${name} • Uploaded</span>
                                <span class="size">${fileSize}</span>
                              </div>
                            </div>
                            <i class="fas fa-check"></i>
                          </li>`;
      uploadedArea.classList.remove("onprogress");
      uploadedArea.insertAdjacentHTML("afterbegin", uploadedHTML);
    }
  });
  let data = new FormData(form);
  xhr.send(data);
}
 </script>
 
 
 
 <script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
 <script>
    ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .catch( error => {
            console.error( error );
        } );
</script>