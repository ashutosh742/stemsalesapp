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
                    <div class="card card-form col-md-12">
                            <div class="row no-gutters">
                                <div class="col-12 col-md-12 mb-12 m-3">
                                    <center><h6>Task Detail</h6>
                                        <?php $ttbl=$this->Menu_model->get_tbldata($tid);
                                            $tsid = $ttbl[0]->status_id;
                                            $taid = $ttbl[0]->actiontype_id;
                                            $tstatus=$this->Menu_model->get_statusbyid($tsid);
                                            $taction=$this->Menu_model->get_actionbyid($taid);
                                        ?>
                                        <h6>Current Task : <?=$taction[0]->name?></h6>
                                        <h6>Last Status : <?=$tstatus[0]->name?></h6>
                                    <h6>Last Task Remark : <?=$ttbl[0]->remarks?></h6></center><hr>
                                    <h6><?=$cd[0]->compname?></h6>
                <?php foreach($ccd as $d){$contactperson=$d->contactperson;$phoneno=$d->phoneno;$emailid=$d->emailid;$designation=$d->designation;?>
                    <div class="col-sm">Name : <?=$contactperson?></div>
                    <div class="col-sm">Designation : <?=$designation?></div>
                    <div class="col-sm">Email id : <?=$emailid?></div>
                    <div class="col-sm">Phone No : <?=$phoneno?>
                        <span>
                            <a href="tel:+91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                            <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;"></a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                            <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;"></a>
                        </span>
                    </div>
                    <?php }?>
                                </div>
                                <input type="hidden" name="action_id" id="action_id" value="<?=$ttbl[0]->actiontype_id?>">
                                <div class="col-lg-12 card-form__body card-body">
                                        <?=form_open('Menu/submittask')?>
                                         <input type="hidden" name="uid" value="<?=$uid?>">
                                        <input type="hidden" name="cmpid" value="<?=$cd[0]->id?>">
                                        <input type="hidden" name="tid" value="<?=$tid?>">
                                        <div class="text-center">
                                        <span><b>Action Completed?</b></span>
                                        <input type="radio" id="pending" name="actontaken" Value="yes"> 
                                        <label for="pending">YES</label>
                                        <input type="radio" id="resolved" name="actontaken" Value="no"> 
                                        <label for="resolved">NO</label>
                                        </div>
                                        <?php $pid=$ttbl[0]->purpose_id;
                                        $purpose=$this->Menu_model->get_purposebyid($pid);
                                        ?>
                                        <div id="noremark" class="text-center">
                                            <textarea type="text" class="form-control" name="noremark" placeholder="Remark"></textarea>
                                        </div>
                                        <div id="purpose" class="text-center">
                                        <h6>Current Task Purpose : <?=$purpose[0]->name?></h6>
                                        <span><b>Purpose Completed?</b></span>
                                        <input type="radio" id="presolved" name="purpose" Value="yes"> 
                                        <label for="pending">YES</label>
                                        <input type="radio" id="pnresolved" name="purpose" Value="no"> 
                                        <label for="resolved">NO</label><hr>
                                        </div>
                                         
                                        <div id="ifyes" style="display:none">
                                            <div class="col-12 col-md-12 mb-12">
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
                                                    <input type="file" class="form-control-file" name="filname" required>
                                                </div>
                                                <div class="row md-12 p-3" id="test5" style="display: none;">
                                                    <label>Write MOM</label>
                                                    <textarea type="text" class="form-control" name="rpmmom" required=""></textarea>
                                                </div>
                                            </div>
                                        <div class="col-12 col-md-12 mb-3">
                                            <label for="validationSample04">Current Status</label>
                                            <select type="text" class="form-control" name="ystatus" placeholder="State" id="ystatus" required>
                                                <option>Select Status</option>
                                                    <option value="1">Open</option>
                                                    <option value="8">OPEN [ RPEM ]</option>
                                                    <option value="2">Reachout</option>
                                                    <option value="3">Tentative</option>
                                                    <option value="4">Will do Later</option>
                                                    <option value="5">Not Interested</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-12 mb-12">
                                            <label for="remark_msg">Remarks</label>
                                            <textarea type="text" class="form-control" name="yremark_msg" id="remark_msg"  required=""></textarea>
                                            <div class="invalid-feedback">.</div>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                        
                                        </div>
                                        <div id="ifno" style="display:none">
                                            <input type="hidden" name="yaction_id" value="<?=$ttbl[0]->actiontype_id?>">
                                            <input type="hidden" name="nstatus" id="nstatus" value="<?=$ttbl[0]->status_id?>">
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

<!-- User details -->
<div id="add_whatsapp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
   <center><h5>Add Whatsapp Activity</h5></center><hr>
   <form action="<?=base_url();?>Menu/addwag" method="post" enctype="multipart/form-data">
   <input type="hidden" id="wsid" name="sid">
   <select class="form-control  p-3  mt-2" name="year">
       <option>Select Year</option>
       <option>2022-23</option>
       <option>2022-24</option>
   </select>
   <input type="Date" id="wsid" name="sid">
   <label>Attach Whatsapp Media</label>
   <input type="file" class="form-control-file" name="waimage" id="waimage" multiple required>
   <select id="sel" class="form-control  p-3  mt-2">
        <option>Peer to Peer Teaching & Learning</option>
        <option>MSC exhibits brought to Classroom</option>
        <option>Students taken to MSC</option>
        <option>Student Learning concepts Independently with MSC exhibits</option>
        <option>Project Created with the help of MSC exhibits</option>
        <option>Teacher to teacher Knowledge Transfer</option>
        <option>Exhibits outside Classroom and MSC</option>
        <option>Other</option>
   </select>
   <textarea id="remark" name="remark" class="form-control  p-3  mt-2" placeholder="Remark" readonly></textarea>
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
   <input type="hidden" id="cid" name="cid">
   <input type="hidden" name="cdate" value="<?=date('Y-m-d');?>">
   <input type="text" name="contactperson" class="form-control p-3 mt-2" placeholder="Name">
   <input type="text" name="designation" class="form-control  p-3  mt-2" placeholder="Designation">
   <input type="number" name="phoneno" class="form-control  p-3  mt-2" placeholder="Contact No">
   <input type="email" name="emailid" class="form-control  p-3  mt-2" placeholder="Email">
   <span><b>Set Contact Type?</b></span><br>
    <input type="radio" name="primary" Value="primary" checked> 
    <label>Primary</label>
    <input type="radio" name="primary" Value="alternate"> 
    <label>Alternate</label><br>
   <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
   </form>
  </div>


  
  </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
                
                </div></div></div>
                
                
                <!-- User details -->
  <div id="create_act" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title1"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <div class="card row m-2">
                      <div class="col-12 col-md-12">
                        <div class="card-header bg-info"><?=$cd[0]->compname?></div>
                        <div class="card-body">
                            Current Status : <lable><?=$cstatus[0]->name?></lable><br>
                            Last Task :  <lable></lable><br>
                            Last Task Remark : <lable></lable>
                        </div>
                      </div>
                    </div>
                    <div class="card card-form col-md-12">
                            <div class="row no-gutters">
<div class="col-lg-12 card-form__body card-body">
   <center><h5>Create Task</h5></center><hr>
   <?=form_open('Menu/addtask')?>
   <input type="hidden" name="ntuid" value="<?=$uid?>">
   <input type="hidden" name="ntinid" value="<?=$init[0]->id?>">
   
   
   
   
   
   
   
   
   
   <?php $date = date('Y-m-d h:i:s');?>
   <input type="datetime-local" name="ntdate" value="<?=$date?>" class="form-control">
   <lable>Current Status</lable>
   <input type="hidden" id="ntstatus" name="ntstatus" value="<?=$cstatus[0]->id?>">
   <lable>Select Action</lable>
   <select id="ntaction" name="ntaction" class="form-control">
       <option value="">Select Action</option>
       <?php $action = $this->Menu_model->get_action();
       foreach($action as $a){?>
           <option value="<?=$a->id?>"><?=$a->name?></option>
       <?php } ?>
   </select>
   <lable>Select Purpose</lable>
   <select id="ntppose" class="form-control" name="ntppose">
   </select>
   <lable>Select Next Action</lable>
   <select id="nextaction" class="form-control" name="ntnextaction">
   </select>
   
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
                            <div class="row no-gutters">
                                <div class="col-lg-12 card-form__body card-body">  
   <div >
   <center><h5>Create Plan</h5></center><hr>
           
   <?php
     $today = date('Y-m-d H:i:s');
     $today = $today."T10:00";
   ?>
   <?=form_open('Menu/dateplan')?>
   <input type="hidden" id="taskid" name="taskid">
   <input type="datetime-local" name="date" class="form-control p-3 mt-2 mb-2" placeholder="Date" min="<?=$today?>">
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
   <input type="hidden" id="lat" name="lat">
   <input type="hidden" id="lng" name="lng">
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
   <input type="hidden" name="uid" value="<?=$uid?>">
   <input type="hidden" id="lat" name="lat">
   <input type="hidden" id="lng" name="lng">
   <input type="hidden" name="closem" value="<?=date('Y-m-d H:i:s')?>">
   <center>Meeting Closed at <?=date('H:i:s')?></center>
   <input type="text" name="caddress" class="form-control p-3 mt-2" placeholder="Comapny Address">
    <input type="text" name="cpname" class="form-control p-3 mt-2" placeholder="Contact Person Name">
    <input type="text" name="cpdes" class="form-control p-3 mt-2" placeholder="Contact Person Designation">
    <input type="text" name="cpno" class="form-control p-3 mt-2" placeholder="Contact Person Mobile No">
    <input type="text" name="cpemail" class="form-control p-3 mt-2" placeholder="Contact Person Email ID">
    <input type="radio" id="html" name="type" value="No Lead">
    <label for="html">No RP Meeting</label><br>
    <input type="radio" id="css" name="type" value="Get Lead">
    <label for="css">RP Meeting</label><br>
   
   
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

var x = document.getElementById("lat");
var y = document.getElementById("lng");
$(document).ready(function(){
    if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
    x.value = "Geolocation is not supported by this browser.";
  }
});
function showPosition(position) {
  x.value = position.coords.latitude; 
  y.value = position.coords.longitude;
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
            $("#purpose").show();
            $("#noremark").hide();
            break;
        case 'resolved':
            $("#purpose").hide();
            $("#noremark").show();
            break;
    }
    result.textContent = message;
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
var ab = document.getElementById("action_id").value;
   if(ab=="1"){
    $("#test1").show();
    $("#test2").hide();
    $("#test3").hide();
    $("#test4").hide();
   }
   if(ab=="4"){
    $("#test1").hide();
    $("#test2").show();
    $("#test3").hide();
    $("#test4").hide();
   }
   if(ab=="2"){
    $("#test1").hide();
    $("#test2").hide();
    $("#test3").show();
    $("#test4").hide();
   }
   if(ab=="5"){
    $("#test1").hide();
    $("#test2").hide();
    $("#test3").hide();
    $("#test4").show();
   }
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



$('#ntaction').on('change', function f() {
    var sid = document.getElementById("ntstatus").value;
    var aid = document.getElementById("ntaction").value;
        $.ajax({
        url:'<?=base_url();?>Menu/getpurpose',
        type: "POST",
        data: {
        sid: sid,
        aid: aid
        },
        cache: false,
        success: function a(result){
        $("#ntppose").html(result);
        }
        });
});

$('#ppose').on('change', function f() {
    var pid = document.getElementById("ppose").value;
$.ajax({
url:'<?=base_url();?>Menu/getnextaction',
type: "POST",
data: {
pid: pid
},
cache: false,
success: function a(result){
$("#nextaction").html(result);
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
   $('#add_cont').click(function(){
    $('#add_contact').modal('show');
    var id = this.value;
    document.getElementById("cid").value = id;
  });
  
  
  $('#add_act').click(function(){
    $('#add_note').modal('show');
    var tid = document.getElementById("tid").value;
        $.ajax({
         url:'<?=base_url();?>Menu/cctd',
         method: 'post',
         data: {tid: tid},
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
  
  $('#add_createact').click(function(){
    $('#create_act').modal('show');
  });
  
  
  $('#startm').click(function(){
    $('#add_startm').modal('show');
  });
  
  $('#closem').click(function(){
    $('#add_closem').modal('show');
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
 </script>