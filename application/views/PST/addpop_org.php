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
                                        Last Task Remark : <lable id="cremarks"></lable><br>
                                        Task Suggestions : <lable id="taskcomments"></lable>
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
                                <div class="card row m-2" id="taskbox">
                                  <div class="col-12 col-md-12">
                                    <div class="card-header bg-info"></div>
                                    <div class="card-body">
                                        <form action="<?=base_url();?>Menu/submittask1" method="post" enctype="multipart/form-data">
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
                                                <div class="row md-12 p-3" id="test3" style="display: none;">
                                                    <label>Attach Meeting Photo</label>
                                                    <input type="file" class="form-control-file" required>
                                                </div>
                                                <div class="row md-12 p-3" id="test2" style="display: none;">
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
                                                <!-- <div class="row md-12 p-3" id="test5" style="display: none;">
                                                    <label>Write MOM</label>
                                                    <textarea type="text" id="editor" class="form-control" name="rpmmom" required=""></textarea>
                                                </div> -->




                                                 <!-- Start Meeting Form -->

                  <div class="p-3 write_mom_section" id="test5" style="display: none;">
                 
                 <div class="text-center momhbox">
                   <div> <label> <i>MINUTES OF MEETING (MoM)</i> </label>
                   <!-- <hr style="width:200px;"> -->
                 </div>
                 </div>
                 
                 <label>Meeting done with Initiator or influencer and decision maker of the company</label>
                   <select class="form-control" name="meetingdonewinitiator">
                       <option value="Initiator">Initiator</option>
                       <option value="Influencer">Influencer</option>
                       <option value="Decision maker">Decision maker</option>
                   </select>
                   <hr>
                   <label>Presentation and pitching is done for which offering :</label>
                   <select class="form-control" name="presentation[]" multiple >
                       <option value="MSC">MSC</option>
                       <option value="Tinkering">Tinkering</option>
                       <option value="Bala">Bala</option>
                       <option value="Astronomy">Astronomy</option>
                       <option value="DIY">DIY</option>
                       <option value="NSP">NSP</option>
                       <option value="Science Lab">Science Lab</option>
                       <option value="Smart Class">Smart Class</option>
                   </select>
                   <hr>
                   <input type="hidden" name="momdata" value="momdata">

                   <div>
                     <label>What is the client's thematic Area for Project Intervention in the current financial Year</label>
                     <select class="form-control" id="project_intervention_select" name="project_intervention_select">
                         <option value="Education">Education</option>
                         <option value="Health">Health</option>
                         <option value="Nutrition">Nutrition</option>
                         <option value="Others">Others</option>
                     </select>
                     <br>
                     <input type="text" class="form-control" name="project_intervention" id="project_interventionText" placeholder="Please add client's thematic Area for Project Intervention in the current financial Year">
                   </div>
                   <hr>
                   <div>
                     <label>Does the client has adopted any schools ?</label>
                     <select class="form-control" id="client_has_adopted_select" required name="client_has_adopted_select">
                       <option value="no">No</option>
                       <option value="yes">Yes</option>
                     </select>
                     <br>
                     <textarea type="text" class="form-control" name="client_has_adopted" placeholder="Please specify details of the schools that client has adopted"  id="client_has_adoptedText" rows="3"></textarea> 
                   </div>

                   <hr>
                   <div>
                     <label>Who are the approving autorities of the proposal ?</label>
                     <textarea required class="form-control" name="approving_autorities" placeholder="Please type name and designation of the officer approving the proposal"></textarea>
                   </div>

                   <hr>
                   <div>
                     <label>What is the left over budget for the current financial year ?</label>
                     <input type="number" required class="form-control" name="budget_for_cfyear" placeholder="Please Type budget for the current financial year">
                   </div>

                   <hr>
                   <div>
                     <label>what is the fund sanction limit at their level ?</label>
                     <input type="number" required class="form-control" name="fund_sanstion_limit" placeholder="Please Type fund sanction limit at their level">
                   </div>

                   <hr>
                   <div>
                     <label>Any other specific remarks regards to the meeting ?</label>
                     <textarea type="text" required class="form-control" name="other_specific_remarks" placeholder="specific remarks regards to the meeting"  rows="3"></textarea> 
                   </div>

                   <hr>
                   <div>
                   <label>Do we need to submit the proposal ?</label>
                   <select class="form-control" required name="submit_proposal" id="submit_proposal_select" >
                       <option value="no">No</option>
                       <option value="yes">Yes</option>
                   </select>
                   <br>
                   <!-- <input type="file" required class="form-control" id="submit_proposal_file" name="submit_proposal_file" placeholder="submit the proposal">
                   <small class="text-danger" id="smallProposaltext" > <i>* Proposal should be submitted through NGO/STEM/Govt Body (No of Schools/Location/Budget)</i></small> -->

                   <div id="submit_proposal_file" class="identify_school_box">
                   <input type="text" required class="form-control"name="proposal_no_of_school" placeholder="Proposed number of schools"> <br>
                   <input type="text" required class="form-control"name="proposal_of_budget" placeholder="Proposed budget"><br>
                   <input type="text" required class="form-control"name="proposal_of_location" placeholder="Proposed location"><br>
                
                   </div>

                   </div>

                    <hr>
                   <div>
                   <label>Do we need to identify school ?</label>
                   <select class="form-control" required name="identify_school" id="identify_school_select" >
                       <option value="no">No</option>
                       <option value="yes">Yes</option>
                   </select>
                   <br>
                   <div id="identify_school_box" class="identify_school_box">
                     <div class="text-right mb-2">
                     <span id="add_field" class="p-2 bg-primary" >+</span>
                     </div>
                   <input type="text" required class="form-control"name="identify_school_state[]" placeholder="Enter Name of State"> <br>
                   <input type="text" required class="form-control"name="identify_school_district[]" placeholder="Enter Name of District"><br>
                   <input type="text" required class="form-control"name="no_of_school[]" placeholder="Enter No of School">
                  
                   </div>
                   </div>
        
                   <hr>
                   <div>
                   <label>School permission letter required ?</label>
                   <select class="form-control" required name="permission_letter" id="permission_letter_select" >
                       <option value="no">No</option>
                       <option value="yes">Yes</option>
                   </select>
                   </div>
               
                   <hr>
                   <div>
                    
                   <div id="permission_letter_box" class="identify_school_box" >
                   <label>Letter should be address to whom in the organization, along with Name and designation and Location</label>

                   <select class="form-control" required name="permission_letter_rech" >
                       <option value="NGO">NGO</option>
                       <option value="STEM">STEM</option>
                   </select>
                   <br>

                   <input type="text" required class="form-control"name="Letter_organization_name" placeholder="Add Concern person name"> <br>
                   <input type="text" required class="form-control"name="Letter_organization_designation" placeholder="Enter Name of Designation"><br>
                   <input type="text" required class="form-control"name="Letter_organization_location" placeholder="Enter Location">
                   </div>  
                   </div>

                   <hr>
                   <div>
                   <label>Client is interested for School Visit ?</label>
                   <select class="form-control" required name="client_int_school_visit" id="client_int_school_select">
                       <option value="no">No</option>
                       <option value="yes">Yes</option>
                   </select>
                   <br>
                   <div id="client_int_school_box" class="identify_school_box">

                   <!-- <input type="text" required class="form-control"name="client_int_type_project" placeholder="Add type of project"> -->
                   <select class="form-control" name="client_int_type_project">
                     <option selected disabled>Select Type of project</option>
                     <option value="MSC">MSC</option>
                     <option value="Tinkering">Tinkering</option>
                     <option value="Bala">Bala</option>
                     <option value="Astronomy">Astronomy</option>
                     <option value="DIY">DIY</option>
                     <option value="NSP">NSP</option>
                     <option value="Science Lab">Science Lab</option>
                     <option value="Smart Class">Smart Class</option>
                   </select>
                   <br>
                   <input type="date" required class="form-control"name="client_int_school_date" placeholder="Select Date"> <br>
                   <input type="text" required class="form-control"name="client_int_school_state" placeholder="Enter State"> <br>
                   <input type="text" required class="form-control"name="client_int_school_district" placeholder="Enter Name of District"><br>
                   <input type="number" required class="form-control"name="client_int_no_of_school" placeholder="Enter no of School">
                   </div>

                   </div>
                   <hr>
                   <div>
                   <label>Do you need intervention from Cluster/PST/ Sales Head ?</label>
                   <select class="form-control" required name="intervention_cm_pst_sh" id="client_int_school_select" >
                       <option value="Cluster">Cluster</option>
                       <option value="PST">PST</option>
                       <option value="Sales Head">Sales Head</option>
                   </select>
                   </div> 

                   <hr>
                   <label>Write Short MOM Remarks</label>
                     <textarea type="text" class="form-control" id="rpmmom" name="rpmmom" rows="3" required></textarea> 
                 
                   </div>
                 <!-- End Meeting Form -->













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
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-12 mb-12">
                                            <label for="remark_msg">Remarks</label>
                                            <textarea type="text" class="form-control" name="yremark_msg" id="remark_msg"  required=""></textarea>
                                            <div class="invalid-feedback">.</div>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>

                                        <div class="col-12 col-md-12 mb-3" id="next_folloup_have_date_card">
                      <label for="next_folloup_have_date">Do You Have next Follow up Date ?</label>
                        <select type="text" class="form-control" name="next_folloup_have_date" id="next_folloup_have_date" required>
                          <option value="" selected>Select</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                        </select>
                        <div id="selectnextfollowdate">
                          <label for="next_folloup_date">Next Follow up Date</label>
                          <input type="datetime-local" class="form-control" min="<?= date('Y-m-d H:i:s'); ?>" name="next_folloup_date"/>
                        </div>
                        <hr>
                    </div>

                  <div class="col-12 col-md-12 mb-3" id="special_question_card">  
                   <div id="special_question_tentive">
                      <?php 
                    $qtype      = 'Tentative';
                    $questions  = $this->Menu_model->GetQuestionsSet($qtype);
                    if(sizeof($questions) > 0){
                            foreach($questions as $question){
                                echo '<div class="mb-3">';
                                echo '<input type="hidden" name="tquestions[]" value="'.$question->question.'" />';
                                echo '<label class="form-label">'.$question->question.'</label>';
                                echo ' <textarea class="form-control" name="tanswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';
                            }
                            echo  '<hr><input type="hidden" name="tquestions[]" value="Propose or Clarify Timeline" />';
                                echo  '<label>Propose or Clarify Timeline</label>';
                                echo  '<select class="form-control" name="tanswers[]">';
                                echo "<option value='Propose	Day 6 to 8'>Propose	Day 6 to 8</option>";
                                echo "<option value='Clarify	Day 8 to 12'>Clarify	Day 8 to 12</option>";
                                echo "<option value='Nudge Day 12 to 14'>Nudge Day 12 to 14</option>";
                                echo "<option value='Support	Day 15 to 18'>Support	Day 15 to 18</option>";
                                echo '</select>';
                    }
                      ?>
                  </div>
                   <div id="special_question_positive">
                      <?php 
                        $pqtype      = 'Positive';
                        $pquestions  = $this->Menu_model->GetQuestionsSet($pqtype);
                        if(sizeof($pquestions) > 0){

                           echo '<div class="form-group">';

                                echo '<div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Have all stakeholders reviewed the proposal internally (Decision Readiness)?" />';
                                echo '<label class="form-label">Have all stakeholders reviewed the proposal internally (Decision Readiness)?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Is it now in your legal/finance process or does it require board sign-off (Approval Stage) ?" />';
                                echo '<label class="form-label">Is it now in your legal/finance process or does it require board sign-off (Approval Stage) ?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Is your CSR disbursal planned this quarter or in the next cycle (Funding Window) ?" />';
                                echo '<label class="form-label">Is your CSR disbursal planned this quarter or in the next cycle (Funding Window) ?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Are we one of the 2 to 3 partners you are finalizing this quarter (Partner Prioritization)?" />';
                                echo '<label class="form-label">Are we one of the 2 to 3 partners you are finalizing this quarter (Partner Prioritization)?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';
                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="What is a realistic timeframe for an MoU or funding commitment (Next Steps Clarity)?" />';
                                echo '<label class="form-label">What is a realistic timeframe for an MoU or funding commitment (Next Steps Clarity)?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo  '<hr><input type="hidden" name="pquestions[]" value="Level Categorization for Closure Timeline - Post-Manager Call" />';
                                echo '<label>Level Categorization for Closure Timeline (Post-Manager Call)</label>';
                                echo  '<select class="form-control" name="panswers[]">';
                                foreach($pquestions as $pquestion){
                                  echo "<option value=\"$pquestion->question\">$pquestion->question</option>";
                                }
                                echo '</select>';

                                echo  '<hr><input type="hidden" name="pquestions[]" value="Closing Timeline" />';
                                echo '<label>Closing Timeline</label>';
                                echo  '<select class="form-control" name="panswers[]">';
                                echo "<option value='In 8 Days'>In 8 Days</option>";
                                echo "<option value='In 8 to 15 Days'>In 8 to 15 Days</option>";
                                echo "<option value='In 15 to 30 Days'>In 15 to 30 Days</option>";
                                echo "<option value='More Than 30 Days'>More Than 30 Days</option>";
                                echo '</select>';
                                
                            echo '</div>';
                        }
                      ?>
                  </div>
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
                                            <div class="col-12 col-md-12 mb-3" id="next_folloup_have_date_card">
                                                  <label for="next_folloup_have_date">Do You Have next Follow up Date ?</label>
                                                    <select type="text" class="form-control" name="next_folloup_have_date" id="next_folloup_have_date" required>
                                                      <option value="" selected>Select</option>
                                                      <option value="yes">Yes</option>
                                                      <option value="no">No</option>
                                                    </select>
                                                    <div id="selectnextfollowdate">
                                                      <label for="next_folloup_date">Next Follow up Date</label>
                                                      <input type="datetime-local" class="form-control" min="<?= date('Y-m-d H:i:s'); ?>" name="next_folloup_date"/>
                                                    </div>
                                                    <hr>
                                                </div>

                                                <div class="col-12 col-md-12 mb-3" id="special_question_card">  
                   <div id="special_question_tentive">
                      <?php 
                    $qtype      = 'Tentative';
                    $questions  = $this->Menu_model->GetQuestionsSet($qtype);
                    if(sizeof($questions) > 0){
                            foreach($questions as $question){
                                echo '<div class="mb-3">';
                                echo '<input type="hidden" name="tquestions[]" value="'.$question->question.'" />';
                                echo '<label class="form-label">'.$question->question.'</label>';
                                echo ' <textarea class="form-control" name="tanswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';
                            }

                            echo  '<hr><input type="hidden" name="tquestions[]" value="Propose or Clarify Timeline" />';
                                echo  '<label>Propose or Clarify Timeline</label>';
                                echo  '<select class="form-control" name="tanswers[]">';
                                echo "<option value='Propose	Day 6 to 8'>Propose	Day 6 to 8</option>";
                                echo "<option value='Clarify	Day 8 to 12'>Clarify	Day 8 to 12</option>";
                                echo "<option value='Nudge Day 12 to 14'>Nudge Day 12 to 14</option>";
                                echo "<option value='Support	Day 15 to 18'>Support	Day 15 to 18</option>";
                                echo '</select>';
                    }
                      ?>
                  </div>
                   <div id="special_question_positive">
                      <?php 
                        $pqtype      = 'Positive';
                        $pquestions  = $this->Menu_model->GetQuestionsSet($pqtype);
                        if(sizeof($pquestions) > 0){
                           echo '<div class="form-group">';
                             echo '<div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Have all stakeholders reviewed the proposal internally (Decision Readiness)?" />';
                                echo '<label class="form-label">Have all stakeholders reviewed the proposal internally (Decision Readiness)?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Is it now in your legal/finance process or does it require board sign-off (Approval Stage) ?" />';
                                echo '<label class="form-label">Is it now in your legal/finance process or does it require board sign-off (Approval Stage) ?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Is your CSR disbursal planned this quarter or in the next cycle (Funding Window) ?" />';
                                echo '<label class="form-label">Is your CSR disbursal planned this quarter or in the next cycle (Funding Window) ?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="Are we one of the 2 to 3 partners you are finalizing this quarter (Partner Prioritization)?" />';
                                echo '<label class="form-label">Are we one of the 2 to 3 partners you are finalizing this quarter (Partner Prioritization)?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';
                                echo '<hr><div class="mb-3">';
                                echo '<input type="hidden" name="pquestions[]" value="What is a realistic timeframe for an MoU or funding commitment (Next Steps Clarity)?" />';
                                echo '<label class="form-label">What is a realistic timeframe for an MoU or funding commitment (Next Steps Clarity)?</label>';
                                echo ' <textarea class="form-control" name="panswers[]" rows="3" placeholder="Write remarks..."></textarea>';
                                echo '</div>';

                                echo  '<hr><input type="hidden" name="pquestions[]" value="Level Categorization for Closure Timeline - Post-Manager Call" />';
                                echo '<label>Level Categorization for Closure Timeline (Post-Manager Call)</label>';
                                echo  '<select class="form-control" name="panswers[]">';
                                foreach($pquestions as $pquestion){
                                  echo "<option value=\"$pquestion->question\">$pquestion->question</option>";
                                }
                                echo '</select>';

                                echo  '<hr><input type="hidden" name="pquestions[]" value="Closing Timeline" />';
                                echo '<label>Closing Timeline</label>';
                                echo  '<select class="form-control" name="panswers[]">';
                                echo "<option value='In 8 Days'>In 8 Days</option>";
                                echo "<option value='In 8 to 15 Days'>In 8 to 15 Days</option>";
                                echo "<option value='In 15 to 30 Days'>In 15 to 30 Days</option>";
                                echo "<option value='More Than 30 Days'>More Than 30 Days</option>";
                                echo '</select>';
                              
                            echo '</div>';
                        }
                      ?>
                  </div>
                  </div>

                                        </div>
                                        <div class="col-12 col-md-12 mb-12" id="late_remarks_message_box">
                  <br><textarea type="text" class="form-control" id="late_remarks_message" name="late_remarks_message" required placeholder="Type reason why you updating late."></textarea> <br>
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
                
             <!-- Initiate Meeting....!!!!!!!  -->

                  <div id="initiate_Meeting" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                                  
                                  <div>
                                    <center><h5>Initiate Meeting</h5></center><hr>
                                    <?php date_default_timezone_set("Asia/Kolkata");?>
                                    <form action="<?=base_url();?>Menu/rpminitiate" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="uid" value="<?=$uid?>">
                                        <input type="hidden" name="smid" value="" id="initmid">
                                        <input type="hidden" name="bscid" value="" id="bscid">
                                        <input type="hidden" id="inilat" name="lat">
                                        <input type="hidden" id="initlng" name="lng">
                                        <input type="hidden" name="startm" value="<?=date('Y-m-d H:i:s')?>">
                                        <center>Meeting Initiated at <?=date('H:i:s')?></center>
                                        <input type="text" name="company_name" class="form-control p-3 mt-2" id="bmcname1">
                                        <br>
                                       <input type="file" id="initiatephoto" name="cphoto" accept="image/*" required class="form-control p-3 mt-2" capture="camera"> 
                                      
                                        <div id="location">
                                            <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
                                                <iframe style="width:100%;height:200px;" id="mylocationinitiate" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
                                            </div>
                                        </div>

                                      <button type="submit" id="rpmsClick1" class="btn btn-primary mt-3">Submit</button>
                                   
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
   <input type="text" name="company_name" class="form-control p-3 mt-2" id="company_name" placeholder="Comapny Name">
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
   <center><h5 class="bg-info p-2">Close Meeting</h5></center><hr>
   <?=form_open('Menu/rpmclose')?>
   <input type="hidden" name="uid" value="<?=$uid?>">
   <input type="hidden" name="cmid" value="" id="closemid">
   <input type="hidden" id="clat" name="lat">
   <input type="hidden" id="clng" name="lng">
   <input type="hidden" class="form-control" name="closem" value="<?=date('Y-m-d H:i:s')?>">
   <center>Meeting Closed at <?=date('H:i:s')?></center> <hr>
    <select name="type" id="RPMorN" class="form-control" required>
        <option value="">Select Type of Meeting</option>
        <option value="Close">No RP Meeting</option>
        <option value="RPClose">RP Meeting</option>
    </select>
    <hr>
    <div id="RPMbox" style="display:none">
        <input type="text" name="caddress" class="form-control p-3 mt-2" placeholder="Comapny Address">
        <input type="text" name="cpname" class="form-control p-3 mt-2" placeholder="Contact Person Name">
        <input type="text" name="cpdes" class="form-control p-3 mt-2" placeholder="Contact Person Designation">
        <input type="text" name="cpno" class="form-control p-3 mt-2" placeholder="Contact Person Mobile No">
        <input type="text" name="cpemail" class="form-control p-3 mt-2" placeholder="Contact Person Email ID">
        <!-- <hr>
        <select id="priority" name="priority" class="form-control" required>
        <option value="">Select Priority</option>
        <option value="no">Non Priority (Will not give business)</option>
        <option value="yes">Priority (Definitely Will give business)</option>
        </select> -->

         <hr>
          <select id="potentional_client" name="potentional_client" class="form-control" required>
            <option value="">Select Meetings is </option>
            <option value="yes">Potential (Definitely Will give business)</option>
            <option value="no">Non Potential (Will not give business)</option>
          </select>
          <hr>
    </div>

  <select id="company_as" class="form-control" name="company_as" required>
          <option value="">Select About Company</option>
          <option value="Good Company">Good Company</option>
          <option value="Not a Big Company">Not a Big Company</option>
          <option value="other">Other</option>
    </select>
        <div id="aboutCompany">
          <hr><textarea name="company_descri" id="company_descri" class="form-control" placeholder="Write about the company"></textarea><hr>
        </div>
    <hr>

    <hr>
    <lable id="letmeetingsremarks">
      <span class="text-danger">* Please provide details as to why it took you more than 30 minutes.</span>
      <input type="text" id="remarksInput" placeholder="* Please provide details as to why it took you more than 30 minutes." name="letmeetingsremarks" class="form-control p-3 mt-2">
    </lable>
    <hr>
   
   <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
   </form>
  </div>
  
  </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
                
</div></div></div>
 
  

<div class="modal fade" id="exampleModalCenterdataDelayed" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="background: #ffffbc !important;">
                          <div class="modal-header">
                            <strong><p class="modal-title" id="delaypartext" style="font-size: 16px; text-align: center;"></p></strong>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                          <form id="submitDelayedForm" method="post">
                              <input type="hidden" id="delayedid" value="" name="delayedid">
                              <div class="mb-3 mt-3">
                                <textarea id="delay_remarks" name="delay_remarks" cols="30" placeholder="Add Remark " class="form-control" required  rows="4"></textarea>
                              </div>
                              <div class="form-group text-center">
                                <button type="submit" class="btn btn-success mt-2">Submit</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    <style> .modal { overflow-y: auto !important;}</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
 $(document).ready(function() {

  $('#late_remarks_message_box').hide();
  $('#late_remarks_message').hide();
  $('#next_folloup_have_date_card').hide();    
  $('#special_question_card').hide();             
    });
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
                $("#test2").show();
                $("#test3").hide();
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
                $("#test4").show();
                $("#test5").hide();
                $("#test6").hide();
                $("#test7").hide();
               }
               if(ab=="6"){
                $("#test1").hide();
                $("#test2").hide();
                $("#test3").hide();
                $("#test5").show();
                $("#test6").hide();
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
    if(message =='undefined'){
      result.textContent = message;
    }
    
});


$('#RPMorN').on('change', function b() {
    var a =this.value;
    if(a=='RPClose'){$("#RPMbox").show();}
    else{$("#RPMbox").hide();}
});


 // Start About Company
  $("#aboutCompany").hide();
  $('#company_as').on('change', function b() {
    var company_as =this.value;
    if( company_as == 'other'){
      $("#aboutCompany").show();
    }else{
      $("#aboutCompany").hide();
    }
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
                var cstatus = document.getElementById("cstatus").value;
                $.ajax({
                url:'<?=base_url();?>Menu/getstatus_new_inPST',
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
               // alert(status_id);
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
    if(message =='undefined'){
      resul.textContent = message;
    }
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
                               
                                // Start Meeting 30 minute Conditions
                                var startm = response[0].startm;
                                var pastDate = new Date(startm.replace(' ', 'T'));
                                var currentDate = new Date();
                                var timeDifference = currentDate - pastDate;
                                var minutesDifference = Math.floor(timeDifference / (1000 * 60));
                                
                                if(minutesDifference > 30){
                                  $('#letmeetingsremarks').show();
                                  $('#remarksInput').attr('required', true);
                                }else{
                                  $('#letmeetingsremarks').hide();
                                }
                                // End Meeting 30 minute Conditions
                                }
                                }
                                });
                                });














  

$('[id^="add_act"]').on('click', function() {
    // $('#add_note').modal('show');
    var tid = this.value;
        $.ajax({
         url:'<?=base_url();?>Menu/cctd',
         method: 'post',
         data: {tid: tid},
         dataType: 'json',
         success: function(response){
           var len = response.length;
           $('#cname,#ctname,#bdname').text('');
           console.log(response+"==");
           if(len > 0){
              //  console.log('len:'+len);
              //  console.log(response);
             var cstatus = response[0].cstatus;   
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
            //  var cpurpose = response[0].cpurpose;
             var cpurpose = response[0].purpose_id;
             var action_id = response[0].actiontype_id;
             var nstatus = response[0].status_id;
             var cmpid = response[0].cid_id;
             var tidd = response[0].id;
             var bdname = response[0].bdname;
             var mmom = response[0].mmom;

            var assignedBy = response[0].assignedBy;
            var assignedTo = response[0].assignedTo;
            var initiateddt = response[0].initiateddt;
            var actiontype_id = response[0].actiontype_id;

            var task_time_cstatus = response[0].status_id;
        
            
             $.ajax({
              url: '<?=base_url();?>Menu/GetTaskComments',
              type: 'POST',
              data: { taskid: tidd},
              success: function(comments_message) {
                  $("#taskcomments").html(comments_message);
              }
          });

             var cpurpose_name = '';
            if(cpurpose !==''){
                $.ajax({
                  url: '<?=base_url();?>Menu/cctd_prupose',
                  type: 'POST',
                  data: { purposeId: cpurpose},
                  success: function(response) {
                      // console.log(response);
                    cpurpose_name = response;
                    document.getElementById("cpurpose").innerHTML = cpurpose_name;
                  }
              });
            }else{
              document.getElementById("cpurpose").innerHTML = cpurpose;
            }

             let show_cnt = 0;
                                $('#add_note').on('shown.bs.modal', function () {
                                  show_cnt = 1;
                                  // console.log(initiateddt);
                                  if (initiateddt === null) {
                                  
                                  if (actiontype_id == 1) {



                                    // let callClicked = false;
                                    // $("#clink").one("click", function () {
                                    //   callClicked = true;
                                    // });
                                    // if (show_cnt === 1) {
                                    //   var actionclick = 1;
                                    //   $("input[name='actontaken']").off("click").on("click", function (e) {
                                    //     if (!callClicked && actionclick === 1) {
                                    //       alert("* Please click on the call icon before initiating the call.");
                                    //       e.preventDefault();
                                    //     }
                                    //   });
                                    // }


                                    var callClicked = false;

                                    function isMobileDevice() {
                                      return /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
                                    }

                                    if (isMobileDevice()) {
                                      // Allow click on mobile
                                      $("#clink").one("click", function () {
                                        callClicked = true;
                                        console.log("📞 Call clicked on mobile");
                                      });
                                    } else {
                                      // Block click on non-mobile
                                      $("#clink").on("click", function (e) {
                                        e.preventDefault(); // completely block the click
                                        e.stopImmediatePropagation(); // stop any attached handlers
                                        // console.log("🚫 Call blocked on non-mobile device");
                                        alert("📞 Calling is only supported on mobile devices.");
                                        return false;
                                      });
                                    }




                                  }

                                  if (actiontype_id == 2){
                                    let mailClicked = false;
                                    $("#glink").one("click", function () {
                                      mailClicked = true;
                                    });
                                    if (show_cnt === 1) {
                                      var actionclick = 1;
                                      $("input[name='actontaken']").off("click").on("click", function (e) {
                                        if (!mailClicked && actionclick === 1) {
                                          alert("* Please click on the Gmail icon before initiated the Mail.");
                                          e.preventDefault();
                                        }
                                      });
                                    }
                                  }

                                  if (actiontype_id == 5){
                                    let wlinkClicked = false;
                                    $("#wlink").one("click", function () {
                                      wlinkClicked = true;
                                    });
                                    if (show_cnt === 1) {
                                      var actionclick = 1;
                                      $("input[name='actontaken']").off("click").on("click", function (e) {
                                        if (!wlinkClicked && actionclick === 1) {
                                          alert("Please click on the Whatsapp icon before initiated the Whatsapp Activity .");
                                          e.preventDefault();
                                        }
                                      });
                                    }
                                  }
                                }
                                });
                                $('#add_note').on('hidden.bs.modal', function () {
                                  show_cnt = 0;
                                  $("input[name='actontaken']").off("click");
                                });


            if(actiontype_id == 1 || actiontype_id == 2 || actiontype_id == 5){
                           
                           $('input[name="purpose"]').on('click', function() {
                            let selectedValue = $(this).val();

                            $('#next_folloup_have_date_card').show();
                            // alert(selectedValue);
                            if(selectedValue == 'no'){
                              $('#special_question_card').hide();
                                  $.ajax({
                                  url:'<?=base_url();?>Menu/cctd',
                                  method: 'post',
                                  data: {tid: tid},
                                  dataType: 'json',
                                  success: function(response){
                                      var initiateddt = response[0].initiateddt;
                                        if(initiateddt !== ''){
                                        var givenTime_init = initiateddt;
                                        var time1_init = new Date(givenTime_init);
                                        var currentTime_init = new Date();
                                        // if (currentTime_init > time1_init) {
                                        //   alert(1);
                                        // }else if (currentTime_init < time1_init) {
                                        //   alert(2);
                                        // }
                                        var givenDate_init = new Date(givenTime_init.replace(" ", "T"));
                                        var currentDate_init = new Date();
                                        var timeDifference_init = currentDate_init - givenDate_init;
                                        var differenceInMinutes_init = Math.floor(timeDifference_init / (1000 * 60));
                                          if(differenceInMinutes_init > 2){
                                            var appon_diffInHours_init = Math.floor(timeDifference_init / (1000 * 60 * 60));
                                            var appon_diffInMinutes_init = Math.floor((timeDifference_init % (1000 * 60 * 60)) / (1000 * 60));
                                            // Type reason why you updating late.
                                            var appon_late_remakrs_init = "* Please provide a reason for the delayed update. Your initial time was "+initiateddt+" . The time difference is "+appon_diffInHours_init+" hour and "+ appon_diffInMinutes_init +" minutes.";
                                            $('#late_remarks_message').attr('placeholder', appon_late_remakrs_init);
                                            $('#late_remarks_message_box').show();
                                            $('#late_remarks_message').show();
                                            $("#late_remarks_message").css("border-color", "red");
                                          }else{
                                            $('#late_remarks_message_box').hide();
                                            $('#late_remarks_message').hide();
                                          }
                                      }
                                    }
                                });
                            
                            }else if(selectedValue == 'yes'){
                                      if(actiontype_id == 1){
                                        if ([3].includes(Number(task_time_cstatus))) {
                                            $('#special_question_card').show();  
                                            $('#special_question_tentive').show();
                                            $('#special_question_positive').hide();
                                          }else  if ([6].includes(Number(task_time_cstatus))) {
                                            $('#special_question_card').show();
                                            $('#special_question_tentive').hide();
                                            $('#special_question_positive').show();
                                          
                                          }else {
                                             $('#special_question_card').hide();
                                          }
                                      }else{
                                         $('#special_question_card').hide();
                                      }
                               }
                        });

                        var appon_time1 = response[0].appointmentdatetime;
                       
                        var time1 = new Date(appon_time1);
                        var currentTime = new Date();
                        
                        if (currentTime > time1) {
                      
                        var current_time2 = new Date();
                        var appon_date1 = new Date(appon_time1);
                        var appon_date2 = new Date(current_time2);
                        var appon_diffInMilliseconds = appon_date2 - appon_date1;
                      
                        var appon_diffInMinutes = Math.floor(appon_diffInMilliseconds / (1000 * 60));
                          if(appon_diffInMinutes > 5){
                            var appon_diffInHours = Math.floor(appon_diffInMilliseconds / (1000 * 60 * 60));
                            var appon_diffInMinutes = Math.floor((appon_diffInMilliseconds % (1000 * 60 * 60)) / (1000 * 60));
                            // Type reason why you updating late.
                            var appon_late_remakrs = "* Please provide a reason for the delayed update. Your appointment time was scheduled for "+ appon_time1 +". The time difference is " + appon_diffInHours + " hours and " + appon_diffInMinutes +" minutes.";
                          
                            $('#exampleModalCenterdataDelayed').modal('show');
                            $("#delaypartext").text(appon_late_remakrs).css("color", "red");
                              
                            $('#delayedid').val(tidd);
                            $("#submitDelayedForm").on("submit", function(e) {
                                  e.preventDefault(); 
                                  var formData = $(this).serialize();
                                  $.ajax({
                                      url: '<?=base_url();?>Menu/UpdatetaskDelayOrBeforeRemarks', 
                                      type: "POST", 
                                      data: formData,
                                      success: function(response) {
                                        // console.log(response);
                                        $('#exampleModalCenterdataDelayed').modal('hide');
                                        $('#add_note').modal('show');
                                      }
                                  });
                              });
                          }else{
                            $('#add_note').modal('show');
                          }
                        } else if (currentTime < time1) {
                         console.log('appon_time2' + appon_time1);
                        var current_time2 = new Date();
                        var appon_date1 = new Date(appon_time1);
                        var appon_date2 = new Date(current_time2);
                        var appon_diffInMilliseconds = appon_date1 - appon_date2;
                        var appon_diffInMinutes = Math.floor(appon_diffInMilliseconds / (1000 * 60));
                        var appon_diffInHours = Math.floor(appon_diffInMilliseconds / (1000 * 60 * 60));
                        var appon_diffInMinutes = Math.floor((appon_diffInMilliseconds % (1000 * 60 * 60)) / (1000 * 60));
                            // Type reason why you updating late.
                        var appon_late_remakrs = "* Please provide a reason for the update Before appointment. Your appointment time was scheduled for "+ appon_time1 +". The time difference is " + appon_diffInHours + " hours and " + appon_diffInMinutes +" minutes.";
                            
                            $('#exampleModalCenterdataDelayed').modal('show');
                            $("#delaypartext").text(appon_late_remakrs).css("color", "red");
                              
                            $('#delayedid').val(tidd);
                            $("#submitDelayedForm").on("submit", function(e) {
                                  e.preventDefault(); 
                                  var formData = $(this).serialize();
                                  $.ajax({
                                      url: '<?=base_url();?>Menu/UpdatetaskDelayOrBeforeRemarks', 
                                      type: "POST", 
                                      data: formData,
                                      success: function(response) {
                                        console.log(response);
                                        $('#exampleModalCenterdataDelayed').modal('hide');
                                        $('#add_note').modal('show');
                                      }
                                  });
                              });
                        }else{
                          //alert("okay");
                          $('#add_note').modal('show');
                        }
                      }else{
                        $('#add_note').modal('show');
                      }




             document.getElementById("cstatus").value = cstatus;
             document.getElementById("cname").innerHTML = cname;
             document.getElementById("ctname").innerHTML = ctname;
             document.getElementById("clsname").innerHTML = clsname;
             document.getElementById("cremarks").innerHTML = cremarks;
             document.getElementById("cp").innerHTML = cp;
             document.getElementById("emailid").innerHTML = emailid;
             document.getElementById("phoneno").innerHTML = phoneno;
             document.getElementById("designation").innerHTML = designation;
            //  document.getElementById("cpurpose").innerHTML = cpurpose;
             document.getElementById("action_id").value = action_id;
             document.getElementById("yaction_id").value = action_id;
             document.getElementById("cmpid").value = cmpid;
             document.getElementById("tidd").value = tidd;
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
             document.getElementById("clink").href = "";
             document.getElementById("wlink").href = "";
             document.getElementById("glink").href = "";
             document.getElementById("bdname").innerHTML = "";
             document.getElementById("mmom").innerHTML = "";
             $.ajax({
                                        url:'<?=base_url();?>Menu/GetCompanyPrimaryCheck',
                                        method: 'post',
                                        data: {tid: tid},
                                        success: function(response){
                                            console.log(response);
                                            Swal.fire({
                                              title: "<strong>UPDATE <u>PRIMARY CONTACT</u></strong>",
                                              icon: "info",
                                              html: response,
                                              showCloseButton: true,
                                              showCancelButton: true,
                                              focusConfirm: false         
                                            });
                                        }
              });
           }
           if(ctname=='Call'){
                 $("#taskbox").show();
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

    $.ajax({
         url:'<?=base_url();?>Menu/GetMeetCompanyInfo',
         method: 'post',
         data: {mid: id},
         dataType: 'json',
         success: function(response){
          console.log(response);
          var compname = response[0].company_name;
          $('#company_name').val(compname);
          if(compname == 'Unknown'){
            document.getElementById('bmcname').readOnly = false;
            }else{
                document.getElementById('bmcname').readOnly =true;
            }
         }
       });


  });
  
   // initiate meeting
                                $('[id^="initm"]').on('click', function() {
                                $('#initiate_Meeting').modal('show');

                                // $("#initiatephoto").change(function() {
                                var x_init = document.getElementById("inilat");
                                var y_init = document.getElementById("initlng");
                                var z_init = document.getElementById("mylocationinitiate");
                                $(document).ready(function(){
                                    if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(showPosition);
                                  } else { 
                                    x_init.value = "Geolocation is not supported by this browser.";
                                  }
                                });
                                function showPosition(position) {
                                  x_init.value = position.coords.latitude; 
                                  y_init.value = position.coords.longitude;
                                  var a_init = position.coords.latitude;
                                  var b_init = position.coords.longitude;
                                  // document.getElementById("mylocationinitiate").style.display = "block";
                                  z_init.src = "https://maps.google.com/?q="+a_init+","+b_init+"&t=k&z=13&ie=UTF8&iwloc=&output=embed";
                                
                                }
                                // });

                             

                                
                                var id = this.value;
                                document.getElementById("initmid").value=id;
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
                                 // console.log(cid);
                                // localStorage.setItem('startMeetformSubmitted', 'true');
                                let myObjectData = { compname: compname, cid: cid,bmid: id };
                                localStorage.setItem("myObjectKey", JSON.stringify(myObjectData));

                                document.getElementById("bmcname1").value = compname;
                                if(compname == 'Unknown'){
                                    document.getElementById('bmcname1').readOnly = false;
                                }else{
                                    document.getElementById('bmcname1').readOnly =true;
                                }
                                document.getElementById("bscid").value = cid;

                                // $("#initiatephoto").change(function() {
                                //       alert("File selected: " + this.files[0].name);
                                //   });


                                }



                                }
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
        if(message =='undefined'){
          result.textContent = message;
        }
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
        if(message =='undefined'){
            result.textContent = message;
          }
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


<script>
        $(document).ready(function() {
          $('#smallProposaltext').hide();
          $('#identify_school_box').hide();
          $('#permission_letter_box').hide();

            $('#project_intervention_select').change(function() {
                if ($(this).val() === 'Others') {
                    $('#project_interventionText').show();
                } else {
                    $('#project_interventionText').hide();
                }
            });

            $('#client_has_adopted_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#client_has_adoptedText').show();
                } else {
                    $('#client_has_adoptedText').hide();

                }
            });

            $('#submit_proposal_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#submit_proposal_file').show();
                    $('#smallProposaltext').show();
                } else {
                    $('#submit_proposal_file').hide();
                    $('#smallProposaltext').hide();
                }
            });
            $('#identify_school_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#identify_school_box').show();
                } else {
                    $('#identify_school_box').hide();
                }
            });
            $('#client_int_school_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#client_int_school_box').show();
                } else {
                    $('#client_int_school_box').hide();
                }
            });
            
            $('#permission_letter_select').change(function() {
                if ($(this).val() === 'yes') {
                    $('#permission_letter_box').show();
                } else {
                    $('#permission_letter_box').hide();
                }
            });

            // Trigger the change event on page load in case "Others" is preselected
            $('#project_intervention_select').trigger('change');
            $('#client_has_adopted_select').trigger('change');
            $('#submit_proposal_select').trigger('change');
            $('#identify_school_select').trigger('change');
            $('#client_int_school_select').trigger('change');
            $('#permission_letter_select').trigger('change');

            $('#selectnextfollowdate').hide();
            $('#next_folloup_have_date').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'yes') {
                    $('#selectnextfollowdate').show();
                } else {
                    $('#selectnextfollowdate').hide();
                }
            });

        });
    </script>


<script>
  $("#rpmsClick").click(function(){
var val = $("#bmcname").val();
  if(val == 'Unknown' || val == ''){
    alert("* Please Enter Valid Company Name");
    $('#bmcname').css('border', '1px solid red');
    return false;
  }
});






</script>