<!-- jQuery FIRST -->
<!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script> -->

<!-- Bootstrap 3 JS -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->

<style>
    body {
        font-family: "Segoe UI", sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 20px;
        color: #2c3e50;
    }

    .main-container {
        max-width: 1000px;
        margin: 20px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #1f3c88;
    }

    .section {
        margin-bottom: 25px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
    }

    .section-title {
        font-weight: 600;
        margin-bottom: 12px;
        color: #34495e;
        font-size: 16px;
    }

    label {
        display: block;
        margin: 6px 0;
        cursor: pointer;
    }

    input[type="text"], 
    input[type="number"], 
    textarea {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #dcdde1;
        margin-top: 6px;
        font-size: 14px;
    }

    textarea {
        resize: vertical;
        min-height: 90px;
    }

    .inline-group label {
        display: inline-block;
        margin-right: 20px;
    }

    .hidden {
        display: none;
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg,#1f3c88,#3a6cf4);
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
    }

    .btn-submit:hover {
        opacity: 0.9;
    }

    .footer-note {
        text-align: center;
        margin-top: 15px;
        font-size: 13px;
        color: #7f8c8d;
    }

    .call-status-section {
        background: #e8f4fc;
        border: 2px solid #3498db;
        padding: 25px;
        margin-bottom: 30px;
        border-radius: 10px;
    }

    .status-title {
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 18px;
    }

    .form-check-inline {
        margin-right: 20px !important;
    }
    .modal-dialog {
        max-width: 700px !important;
    }
    .project-details {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    overflow: hidden;
    max-width: 700px;
}

.project-details li {
    display: flex;
    gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    color: #333;
}

.project-details li:last-child {
    border-bottom: none;
}

.project-details li strong {
    min-width: 170px;
    color: #2c3e50;
    font-weight: 600;
}

.project-details li:nth-child(even) {
    background: #fafbfc;
}

.project-details li:hover {
    background: #f3f7ff;
}
.project-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #1f2d3d;
}

/* Infrastructure options styling */
.infrastructure-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.infrastructure-options label {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background: white;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    transition: all 0.2s;
}

.infrastructure-options label:hover {
    background: #f1f3f4;
    border-color: #3498db;
}

.infrastructure-options input[type="checkbox"] {
    margin-right: 10px;
}

@media (max-width: 768px) {
    .infrastructure-options {
        grid-template-columns: 1fr;
    }
}

</style>
<?php 
$taskDetails        = $this->Menu_model->getTBLTaskBySingleID($taskId);
$sid                = $taskDetails[0]->sid;
$task_user_id       = $taskDetails[0]->user_id;
$schoolDetails      = $this->Menu_model->get_spd_with_by_id($sid);
$school_name        = $schoolDetails[0]->sname;
// echo "<pre>";
// print_r($schoolDetails);
?>



<!-- Your existing HTML code above remains the same until the form -->
<!-- Your existing HTML code above remains the same until the form -->

<div class="main-container">
    <div class="text-center">
        <h1>📞 Daily Calling Questionnaires</h1>
        <hr>
        <h5>School Name : <span style="color:navy;"><?= $school_name; ?> (<?= $sid; ?>)</span></h5>
        <a target='_blank' href="https://stemoppapp.in/Menu/SchoolProfileDetails/<?= $sid ?>/<?=$task_user_id;?>">Click here for School Profile</a>
        <hr>
    </div>

    <!-- Your existing school overview section -->

    <form id="questionnaireForm" action="<?= base_url()?>Menu/SubmitCALLOnSchool" method="POST">
        <!-- Hidden input to store JSON data -->
        <input type="hidden" name="questionnaire_json" id="questionnaireJson">
        
        <!-- Call Status Section -->
        <div class="call-status-section">
            <div class="status-title">📞 CALL On School</div>
            
            <!-- Hidden inputs for task info -->
            <input type="hidden" name="taskId" value="<?= $taskId; ?>"/>
            <input type="hidden" name="taskType" value="<?= $taskType; ?>"/>
            <input type="hidden" name="main_task_id" value="<?= $tasktypeid; ?>"/>    
            
            <!-- Question 0.1: Action Completed -->
            <div class="mb-3" data-question="0.1">
                <label class="form-label" style="font-weight: bold; color: #34495e;">⚡ Action Completed?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="action_completed" value="yes" id="action_yes" required>
                    <label class="form-check-label" for="action_yes">✅ Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="action_completed" value="no" id="action_no">
                    <label class="form-check-label" for="action_no">❌ No</label>
                </div>
            </div>
            
            <!-- Question 0.2: Purpose Completed -->
            <div id="purposeSection" class="mb-3 hidden" data-question="0.2">
                <label class="form-label" style="font-weight: bold; color: #34495e;">🎯 Purpose Completed?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="purpose_completed" value="yes" id="purpose_yes">
                    <label class="form-check-label" for="purpose_yes">✅ Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="purpose_completed" value="no" id="purpose_no">
                    <label class="form-check-label" for="purpose_no">❌ No</label>
                </div>
            </div>
            
            <!-- Question 0.3: Final Remarks -->
            <div id="remarksSection" class="mb-3 hidden" data-question="0.3">
                <label class="form-label" style="font-weight: bold; color: #34495e;">📝 Final Remarks</label>
                <textarea class="form-control" name="main_task_remarks" placeholder="Write observations, discussion points, and remarks..." style="border-radius: 10px; border: 1px solid #ccc; padding: 10px; min-height: 80px;"></textarea>
            </div>
        </div>

        <!-- MSC Questionnaire Section -->
        <div id="questionnaireSection" class="hidden">
            <!-- Question 1 -->
            <div class="section" data-question="1">
                <div class="section-title">1. SPOC Verification</div>
                
                <label>Are the SPOC contact details entered in the SPD correct?</label>
                <div class="inline-group">
                    <label><input type="radio" name="spoc_correct" value="Correct"> ✔ Correct</label>
                    <label><input type="radio" name="spoc_correct" value="Incorrect"> ✖ Incorrect</label>
                </div>
                
                <div id="spocUpdate" class="hidden">
                    <br>
                    <input type="text" name="spoc_name" placeholder="SPOC Name">
                    <input type="text" name="spoc_mobile" placeholder="Mobile Number">
                    <input type="text" name="spoc_email" placeholder="Email ID">
                    
                    <br><br>
                    <label>SPOC Designation:</label>
                    <div class="inline-group">
                        <label><input type="radio" name="spoc_designation" value="Principal"> Principal</label>
                        <label><input type="radio" name="spoc_designation" value="Teacher"> Teacher</label>
                        <label><input type="radio" name="spoc_designation" value="Coordinator"> Coordinator</label>
                        <label><input type="radio" name="spoc_designation" value="Other"> Other</label>
                    </div>
                    <input type="text" name="spoc_other" placeholder="If Other, specify">
                </div>
            </div>
            
            <!-- Question 2 -->
            <div class="section" data-question="2">
                <div class="section-title">2. MSC Awareness</div>
                <div class="inline-group">
                    <label><input type="radio" name="msc_aware" value="Yes"> Yes</label>
                    <label><input type="radio" name="msc_aware" value="No"> No</label>
                </div>
                
                <div id="mscYears" class="hidden">
                    <br>
                    <label>When was the MSC established?</label>
                    <label><input type="radio" name="msc_year" value="<1"> Less than 1 year ago</label>
                    <label><input type="radio" name="msc_year" value="2"> 2 years ago</label>
                    <label><input type="radio" name="msc_year" value="3"> 3 years ago</label>
                    <label><input type="radio" name="msc_year" value="4"> 4 years ago</label>
                    <label><input type="radio" name="msc_year" value="Other"> Other</label>
                    <input type="text" name="msc_year_other" placeholder="If Other, specify">
                </div>
            </div>
            
            <!-- Question 3 -->
            <div class="section" data-question="3">
                <div class="section-title">3. MSC Usage in Teaching</div>
                <div class="inline-group">
                    <label><input type="radio" name="msc_usage" value="Yes"> Yes</label>
                    <label><input type="radio" name="msc_usage" value="No"> No</label>
                </div>
            </div>
            
            <!-- Question 4 -->
            <div class="section" data-question="4">
                <div class="section-title">4. WhatsApp Media Sharing Frequency</div>
                <label><input type="radio" name="media_share" value="Daily"> Daily</label>
                <label><input type="radio" name="media_share" value="Weekly"> Weekly</label>
                <label><input type="radio" name="media_share" value="Monthly"> Monthly</label>
                <label><input type="radio" name="media_share" value="Rarely"> Rarely</label>
                <label><input type="radio" name="media_share" value="Not Shared"> Not shared</label>
            </div>
            
            <!-- Question 5 -->
            <div class="section" data-question="5">
                <div class="section-title">5. Number of Utilizations Received</div>
                <input type="number" name="utilization_count" placeholder="Enter number">
            </div>
            
            <!-- Question 6 - UPDATED SECTION -->
            <div class="section" data-question="6">
                <div class="section-title">6. Infrastructure Availability</div>
                <div class="inline-group">
                    <label><input type="radio" name="separate_room" value="Yes" id="infra_yes"> Yes</label>
                    <label><input type="radio" name="separate_room" value="No" id="infra_no"> No</label>
                </div>
                
                <!-- Infrastructure Options (shown only when Yes is selected) -->
                <div id="infrastructureOptions" class="infrastructure-options hidden">
                    <label><input type="checkbox" name="infrastructure[]" value="Mini Science Centre"> Mini Science Centre</label>
                    <label><input type="checkbox" name="infrastructure[]" value="Tinker Lab"> Tinker Lab</label>
                    <label><input type="checkbox" name="infrastructure[]" value="Science Lab"> Science Lab</label>
                    <!-- <label><input type="checkbox" name="infrastructure[]" value="DIY(Do It Yourself)"> DIY(Do It Yourself)</label> -->
                    <label><input type="checkbox" name="infrastructure[]" value="Astronomy Lab"> Astronomy Lab</label>
                    <label><input type="checkbox" name="infrastructure[]" value="BALA"> BALA</label>
                    <label><input type="checkbox" name="infrastructure[]" value="Just Learning"> Just Learning</label>
                    <label><input type="checkbox" name="infrastructure[]" value="Employee Engagement"> Employee Engagement</label>
                    <label><input type="checkbox" name="infrastructure[]" value="Teacher Training Program"> Teacher Training Program</label>
                    <label><input type="checkbox" name="infrastructure[]" value="MSC Model"> MSC Model</label>
                </div>
            </div>
            
            <!-- Question 7 -->
            <div class="section" data-question="7">
                <div class="section-title">7. Current Condition of MSC</div>
                <label><input type="radio" name="msc_condition" value="Good"> Good</label>
                <label><input type="radio" name="msc_condition" value="Average"> Average</label>
                <label><input type="radio" name="msc_condition" value="Poor"> Poor</label>
            </div>
            
            <!-- Question 8 -->
            <div class="section" data-question="8">
                <div class="section-title">8. Maintenance Requirement</div>
                <div class="inline-group">
                    <label><input type="radio" name="maintenance" value="Yes"> Yes</label>
                    <label><input type="radio" name="maintenance" value="No"> No</label>
                </div>
            </div>
            
            <!-- Question 9 -->
            <div class="section" data-question="9">
                <div class="section-title">9. Teacher Training Requirement</div>
                <div class="inline-group">
                    <label><input type="radio" name="training" value="Yes"> Yes</label>
                    <label><input type="radio" name="training" value="No"> No</label>
                </div>
            </div>
            
            <!-- Question 10 -->
            <div class="section" data-question="10">
                <div class="section-title">10. Project Coordinator's Final Remarks</div>
                <textarea name="final_remarks" placeholder="Write observations, discussion points, and remarks..."></textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit">💾 Submit All Data</button>
    </form>
    
    <div class="footer-note">
        Built for structured data collection and monitoring.
    </div>
</div>

<script>
$(document).ready(function(){
    // Call Status Logic
    $('input[name="action_completed"]').change(function(){
        if($(this).val() === 'yes'){
            $('#purposeSection').removeClass('hidden');
            $('#remarksSection').addClass('hidden');
            $('#questionnaireSection').addClass('hidden');
        } else {
            $('#purposeSection').addClass('hidden');
            $('#remarksSection').removeClass('hidden');
            $('#questionnaireSection').addClass('hidden');
        }
    });
    
    $('input[name="purpose_completed"]').change(function(){
        if($('#action_yes').is(':checked') && $(this).val() === 'yes'){
            $('#questionnaireSection').removeClass('hidden');
            $('#remarksSection').addClass('hidden');
        } else if($('#action_yes').is(':checked') && $(this).val() === 'no'){
            $('#remarksSection').removeClass('hidden');
            $('#questionnaireSection').addClass('hidden');
        }
    });
    
    // MSC Form Logic
    
    // SPOC Verification Logic
    $('input[name="spoc_correct"]').change(function(){
        if($(this).val() === 'Incorrect'){
            $('#spocUpdate').slideDown();
        } else {
            $('#spocUpdate').slideUp();
        }
    });

    // MSC Awareness Logic
    $('input[name="msc_aware"]').change(function(){
        if($(this).val() === 'Yes'){
            $('#mscYears').slideDown();
        } else {
            $('#mscYears').slideUp();
        }
    });
    
    // Infrastructure Availability Logic
    $('input[name="separate_room"]').change(function(){
        if($(this).val() === 'Yes'){
            $('#infrastructureOptions').slideDown();
        } else {
            $('#infrastructureOptions').slideUp();
            // Uncheck all checkboxes when No is selected
            $('input[name="infrastructure[]"]').prop('checked', false);
        }
    });
    
    // Form submit handler to create JSON with ALL questions
    $('#questionnaireForm').submit(function(e){
        // Prevent form submission temporarily
        e.preventDefault();
        
        // Create JSON object with ALL questions and their text
        var questionnaireData = {
            // Call Status Questions
            call_status: {
                questions: [
                    {
                        question_id: "0.1",
                        question_text: "Action Completed?",
                        answer: $('input[name="action_completed"]:checked').val() || ''
                    },
                    {
                        question_id: "0.2",
                        question_text: "Purpose Completed?",
                        answer: $('input[name="purpose_completed"]:checked').val() || ''
                    },
                    {
                        question_id: "0.3",
                        question_text: "Final Remarks",
                        answer: $('textarea[name="main_task_remarks"]').val() || ''
                    }
                ]
            },
            
            // MSC Questionnaire Questions
            msc_questionnaire: {
                questions: []
            }
        };
        
        // Only collect MSC questions if the section is visible
        if(!$('#questionnaireSection').hasClass('hidden')){
            // Question 1: SPOC Verification
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "1",
                question_text: "Are the SPOC contact details entered in the SPD correct?",
                answer: $('input[name="spoc_correct"]:checked').val() || '',
                follow_up_questions: [
                    {
                        question_id: "1.1",
                        question_text: "SPOC Name",
                        answer: $('input[name="spoc_name"]').val() || ''
                    },
                    {
                        question_id: "1.2",
                        question_text: "Mobile Number",
                        answer: $('input[name="spoc_mobile"]').val() || ''
                    },
                    {
                        question_id: "1.3",
                        question_text: "Email ID",
                        answer: $('input[name="spoc_email"]').val() || ''
                    },
                    {
                        question_id: "1.4",
                        question_text: "SPOC Designation",
                        answer: $('input[name="spoc_designation"]:checked').val() || ''
                    },
                    {
                        question_id: "1.5",
                        question_text: "Other Designation",
                        answer: $('input[name="spoc_other"]').val() || ''
                    }
                ]
            });
            
            // Question 2: MSC Awareness
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "2",
                question_text: "MSC Awareness",
                answer: $('input[name="msc_aware"]:checked').val() || '',
                follow_up_questions: [
                    {
                        question_id: "2.1",
                        question_text: "When was the MSC established?",
                        answer: $('input[name="msc_year"]:checked').val() || ''
                    },
                    {
                        question_id: "2.2",
                        question_text: "Other (Specify)",
                        answer: $('input[name="msc_year_other"]').val() || ''
                    }
                ]
            });
            
            // Question 3: MSC Usage in Teaching
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "3",
                question_text: "MSC Usage in Teaching",
                answer: $('input[name="msc_usage"]:checked').val() || ''
            });
            
            // Question 4: WhatsApp Media Sharing Frequency
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "4",
                question_text: "WhatsApp Media Sharing Frequency",
                answer: $('input[name="media_share"]:checked').val() || ''
            });
            
            // Question 5: Number of Utilizations Received
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "5",
                question_text: "Number of Utilizations Received",
                answer: $('input[name="utilization_count"]').val() || ''
            });
            
            // Question 6: Infrastructure Availability (UPDATED)
            var infrastructureArray = [];
            $('input[name="infrastructure[]"]:checked').each(function(){
                infrastructureArray.push($(this).val());
            });
            
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "6",
                question_text: "Infrastructure Availability",
                answer: $('input[name="separate_room"]:checked').val() || '',
                follow_up_questions: [
                    {
                        question_id: "6.1",
                        question_text: "Available Infrastructure",
                        answer: infrastructureArray.join(', ') || 'None selected'
                    }
                ]
            });
            
            // Question 7: Current Condition of MSC
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "7",
                question_text: "Current Condition of MSC",
                answer: $('input[name="msc_condition"]:checked').val() || ''
            });
            
            // Question 8: Maintenance Requirement
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "8",
                question_text: "Maintenance Requirement",
                answer: $('input[name="maintenance"]:checked').val() || ''
            });
            
            // Question 9: Teacher Training Requirement
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "9",
                question_text: "Teacher Training Requirement",
                answer: $('input[name="training"]:checked').val() || ''
            });
            
            // Question 10: Project Coordinator's Final Remarks
            questionnaireData.msc_questionnaire.questions.push({
                question_id: "10",
                question_text: "Project Coordinator's Final Remarks",
                answer: $('textarea[name="final_remarks"]').val() || ''
            });
        }
        
        // Add metadata
        questionnaireData.metadata = {
            school_id: "<?= $sid; ?>",
            school_name: "<?= $school_name; ?>",
            task_id: "<?= $taskId; ?>",
            submission_date: new Date().toISOString(),
            questionnaire_version: "1.1" // Updated version for infrastructure changes
        };
        
        // Convert to JSON string
        var jsonString = JSON.stringify(questionnaireData, null, 2);
        
        // Set the JSON string to hidden input
        $('#questionnaireJson').val(jsonString);
        
        // For debugging - you can see the JSON in console
        console.log("Questionnaire JSON:", jsonString);
        
        // Now submit the form
        this.submit();
    });
});
</script>