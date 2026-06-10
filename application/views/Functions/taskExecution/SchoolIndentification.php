<style>
      .project_color{
    color: #ca00ff;
}
.task_color{
    color:rgb(13, 0, 255);
}
.school_color{
    color:rgb(255, 0, 132);
}
.teacher-block {
      border: 2px solid #ddd;
      padding: 15px;
      border-radius: 12px;
      margin-bottom: 15px;
      background: #fdfdfd;
      box-shadow: 0 4px 8px rgba(0,0,0,0.08);
      position: relative;
    }
    .teacher-actions {
      position: absolute;
      top: 10px;
      right: 10px;
    }
    .btn-action {
      border: none;
      padding: 6px 10px;
      margin-left: 4px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
    }
    .btn-add { background: #2ecc71; color: #fff; }
    .btn-remove { background: #e74c3c; color: #fff; }
    label {
      font-weight: bold; 
      color: #2c3e50;
      margin-top: 10px;
    }
</style>

<?php
    $getAllTaskActions = $this->Menu_model->GetTaskActionDetails($tasktypeid);
    $checkTaskCurrentStages = $this->Menu_model->CheckTaskCurrentStages($tasktypeid, $taskId);
    $stageCount = sizeof($checkTaskCurrentStages);

    usort($checkTaskCurrentStages, function($a, $b) {
        return $a->id <=> $b->id; // spaceship operator for comparison
    });
    $stageData = $checkTaskCurrentStages[0];

    $is_final_stage = ($stageCount == 1) ? "Yes" : "No";

    $taskDetailsData    = $this->Menu_model->GetTBLTaskDetailsByTaskId($taskId);
    
    
    $compname           = $taskDetailsData[0]->compname;
    $cid                = $taskDetailsData[0]->cid;
    $current_status     = $taskDetailsData[0]->current_status;


    $appointmentdatetime     = $taskDetailsData[0]->appointmentdatetime;
    $bd_request_id           = $taskDetailsData[0]->bd_request_id;
    $bd_request_id           = !empty($bd_request_id) ? $bd_request_id  : 0;

    $main_task_id       = $stageData->id;
    $taskperformedby    = $stageData->taskperformedby;
    $tasktype           = $stageData->tasktype;
    $taskname           = $stageData->taskname;
    $taskdetails        = $stageData->taskdetails;
    $taskaction         = $stageData->taskaction;

    $type_text          = $stageData->type_text;
    $type_checkbox      = $stageData->type_checkbox;
    $type_radiobutton   = $stageData->type_radiobutton;
    $type_select        = $stageData->type_select;
    $type_date          = $stageData->type_date;
    $type_textarea      = $stageData->type_textarea;
    $type_file          = $stageData->type_file;
    $type_rating        = $stageData->type_rating;

    $tasktime           = $stageData->tasktime;
    $taskstatus         = $stageData->taskstatus;

    $rsid               = $taskDetailsData[0]->request_sid;

    // echo $rsid;
    if(!empty($rsid)){
        $spdRequestDetails    = $this->Menu_model->GetRequestSchoolDetailsByRequestSID($rsid);

        // echo sizeof($spdRequestDetails);
       if (!empty($spdRequestDetails)) {

        $spdRequestDetail = $spdRequestDetails[0];

        $project_code     = $spdRequestDetail->project_code ?? '';
        $school_name      = $spdRequestDetail->sname ?? '';
        $saddress         = $spdRequestDetail->saddress ?? '';
        $sdistrict        = $spdRequestDetail->sdistrict ?? '';
        $tehshil          = $spdRequestDetail->tehshil ?? '';
        $scity            = $spdRequestDetail->scity ?? '';
        $sstate           = $spdRequestDetail->sstate ?? '';
        $slocation        = $spdRequestDetail->slocation ?? '';
        $slanguage        = $spdRequestDetail->slanguage ?? '';
        $spincode         = $spdRequestDetail->spincode ?? '';
        $snoyear          = $spdRequestDetail->snoyear ?? '';
        $sayear           = $spdRequestDetail->sayear ?? '';
        $std              = $spdRequestDetail->std ?? '';
        $boys             = $spdRequestDetail->boys ?? '';
        $girls            = $spdRequestDetail->girls ?? '';
        $total_students   = $spdRequestDetail->total_students ?? '';
        $total_teachers   = $spdRequestDetail->total_teachers ?? '';
        $timing           = $spdRequestDetail->timing ?? '';
        $website          = $spdRequestDetail->website ?? '';

    } else {

        $project_code     = '';
        $school_name      = '';
        $saddress         = '';
        $sdistrict        = '';
        $tehshil          = '';
        $scity            = '';
        $sstate           = '';
        $slocation        = '';
        $slanguage        = '';
        $spincode         = '';
        $snoyear          = '';
        $sayear           = '';
        $std              = '';
        $boys             = '';
        $girls            = '';
        $total_students   = '';
        $total_teachers   = '';
        $timing           = '';
        $website          = '';
    }

    }
    

    $bd_request_remark  = "";
    if($bd_request_id !=0){
        $taskDetailsData    = $this->Menu_model->get_BDRequestbyrid($bd_request_id);
        if(sizeof($taskDetailsData) > 0){
            $bd_request_remark      ="<hr> <strong>BD Request Remarks :</strong> ".$taskDetailsData[0]->remark;
            $bd_request_remark     .= "<hr> <strong>Location :</strong> ".$taskDetailsData[0]->vlocation;


        }
    }
    
    $schoolZones    = $this->Menu_model->GetOperationSchoolZone();
  ?>
<style>
        .modal-dialog {
                max-width: 100% !important;
        }
        .modal-body {
    background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
}
</style>
<div class="container mt-4" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%); padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <div class="container-fluid">

        <div class="text-center">
            <h3 class="task_color"><?= $taskname ?></h3>
            <h5 class="project_color"><a target='_blank' href="<?= base_url();?>Menu/CompanyDetails/<?= $cid; ?>"><?= $compname; ?> (<?= $current_status ?>) </a></h5>
            <h6 class="school_color">
                <a target='_blank' href="<?= base_url();?>Menu/CompanyDetails/<?= $cid; ?>">
                 CID : <?= $cid; ?>
                </a>
                <hr>
                 <?= $appointmentdatetime; ?>
                 <?= $bd_request_remark ?>
            </h6>
        </div>
       
         <hr>
        <form id="SubmitCALLOnBDRequest" action="<?= base_url()?>Menu/SchoolIndentificationViewSubmit" method="POST" enctype="multipart/form-data" style="font-size: 15px;">
            <input type="hidden" name="taskId" value="<?= $taskId; ?>"/>
            <input type="hidden" name="taskType" value="<?= $taskType; ?>"/>
            <input type="hidden" name="main_task_id[]" value="<?= $tasktypeid; ?>"/>    
            


            <?php  
                $usersstates    =  $this->Menu_model->GetState();
            ?>


<div id="schoolDats" style="background:#f9f9f9;">
    
    <div class="col" style="margin-bottom:15px;">
        <label for="userslsctstate" class="form-label" style="font-weight:bold;">🌍 State</label>

        <select class="select2 form-select" name="state" id="userslsctstate" required 
                style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
            <option value="">Select State</option>
            <?php foreach($usersstates as $usersstate) { ?>
                <option value="<?= $usersstate->state_title ?>"><?= $usersstate->state_title ?></option>
            <?php } ?>
        </select>
        
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="select_district" class="form-label" style="font-weight:bold;">🏙️ District</label>
        <select class="select2 form-select" id="select_district" name="district" required
                style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
            <option value="">Select District</option>
        </select>
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="select_city" class="form-label" style="font-weight:bold;">🌆 City</label>
        <select class="select2 form-select" id="select_city" name="city" required
                style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
            <option value="">Select City</option>
        </select>
    </div>
    <div class="col" style="margin-bottom:15px;">
        <label for="select_city" class="form-label" style="font-weight:bold;">🌆 Zone</label>
        <select class="select2 form-select" id="school_zone" name="school_zone" required
                style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
                <option value="" selected disabled>-- Select --</option>
                <?php foreach($schoolZones as $schoolZone): ?>
                    <option value="<?= $schoolZone->id ?>"><?= $schoolZone->name ?></option>
                <?php endforeach; ?>
        </select>
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="school_name" class="form-label" style="font-weight:bold;">🏫 School Name</label>
        <textarea id="school_name" name="school_name" class="form-control" placeholder="Enter School Name" required
                  style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc; resize:none;"><?= $school_name ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-4">
            <label class="form-label" style="font-weight:bold;">👩‍🎓 Total Students</label>
            <input class="form-control" value="<?= $total_student ?>" type="number" required name="total_student" placeholder="Enter total students"
                   style="border-radius:8px; border:1px solid #ccc;">
        </div>
        <div class="col-md-4">
            <label class="form-label" style="font-weight:bold;">👦 Boys</label>
            <input class="form-control" value="<?= $boys ?>" type="number" required name="total_boys" placeholder="Enter total boys"
                   style="border-radius:8px; border:1px solid #ccc;">
        </div>
        <div class="col-md-4">
            <label class="form-label" style="font-weight:bold;">👧 Girls</label>
            <input class="form-control" value="<?= $girls ?>" type="number" required name="total_girls" placeholder="Enter total girls"
                   style="border-radius:8px; border:1px solid #ccc;">
        </div>
        <div class="col-md-6">
            <label class="form-label" style="font-weight:bold;">👩‍🎓 Total Teacher</label>
            <input class="form-control" value="<?= $total_teachers ?>" type="number" required name="total_teacher" placeholder="Enter total teacher"
                   style="border-radius:8px; border:1px solid #ccc;">
        </div>
        <div class="col-md-6">
            <label class="form-label" style="font-weight:bold;">⏰ School Timing</label>
            <input class="form-control" value="<?= $timing ?>" type="text" required name="school_timing" placeholder="Enter School timing"
                   style="border-radius:8px; border:1px solid #ccc;">
        </div>
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="school_standard" class="form-label" style="font-weight:bold;">📚 School Standard</label>
        <input class="form-control" value="<?= $std ?>" type="text" required name="school_standard" placeholder="e.g., Primary, Secondary, Higher Secondary"
               style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
    </div>


    <div class="col" style="margin-bottom:15px;">
        <label for="school_address" class="form-label" style="font-weight:bold;">📍 School Address</label>
        <textarea id="school_address" name="address" required class="form-control" placeholder="Enter full address" required
                  style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc; resize:none;"><?= $saddress; ?></textarea>
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="school_location" class="form-label" style="font-weight:bold;">📍 School Location</label>
        <textarea id="school_location" name="school_location" required class="form-control" placeholder="Enter google map link" required
                  style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc; resize:none;"><?= $slocation; ?></textarea>
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="school_language" class="form-label" style="font-weight:bold;">📚 School Language</label>
        <textarea id="school_language" name="school_language" required class="form-control" placeholder="Enter School Language" required
                  style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc; resize:none;"><?= $slanguage; ?></textarea>
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="school_pincode" class="form-label" style="font-weight:bold;">📚 School Pincode</label>
        <input class="form-control" value="<?= $spincode ?>" type="text" required name="school_pincode" placeholder="Enter School Pincode"
                   style="border-radius:8px; border:1px solid #ccc;">
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="school_year" class="form-label" style="font-weight:bold;">📚 School Acedmic Year</label>
        <input class="form-control" type="text" value="<?= $sayear ?>" required name="school_year" placeholder="Enter School Acedmic Year"
                   style="border-radius:8px; border:1px solid #ccc;">
    </div>

    <div class="col" style="margin-bottom:15px;">
        <label for="school_address" class="form-label" style="font-weight:bold;">📍 Research Remarks</label>
        <textarea id="research_remarks" name="research_remarks" required class="form-control" placeholder="Write Remarks....." required
                  style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc; resize:none;"></textarea>
    </div>


    <div class="col" style="margin-bottom:15px;">
        <?php $travelClustersLists = $this->Menu_model->GetAllTravelClusterDetailsData(); ?>
        <label for="select_city" class="form-label" style="font-weight:bold;">🌆 Select Travel Cluster</label>
        <select class="form-control correction-input" name="school_cluster_zone" id="travelClusterSelect" required >
                <option value="" selected disabled>-- Select --</option>
                <?php foreach($travelClustersLists as $travelClustersList): ?>
                    <option value="<?= $travelClustersList->id ?>"><?= $travelClustersList->clustername ?></option>
                <?php endforeach; ?>
        </select>
        <div id="clusterDetailsDiv" class="col-md-12" style="margin-top: 15px;"></div>
    </div>

</div>


        <hr>

            <!-- Action Completed -->
            <div id="teacher-container">
                <div class="teacher-block">
                    <div class="teacher-actions">
                    <button type="button" class="btn-action btn-add">➕</button>
                    <button type="button" class="btn-action btn-remove">➖</button>
                    </div>
                    
                    <label>👨‍🏫 Teacher Name</label>
                    <input class="form-control" type="text" name="teacher_name[]" >

                    <label>💼 Designation</label>
                    <input class="form-control" type="text" name="designation_name[]" >

                    <label>📱 Mobile Number</label>
                    <input class="form-control" type="number" name="mobile_name[]" >

                    <label>📧 Email Address</label>
                    <input class="form-control" type="email" name="email_name[]" >
                </div>
                </div>
    
            <hr>
            <!-- Submit -->
           <div class="text-center">
             <button type="submit" class="btn btn-primary" style="border-radius: 30px; padding: 10px 25px; font-weight: bold; background: linear-gradient(to right, #3498db, #2980b9); border: none; box-shadow: 0 3px 6px rgba(0,0,0,0.2);">
                🚀 Submit
            </button>
           </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
  // Add new block
  $(document).on('click', '.btn-add', function(){
    let block = $(this).closest('.teacher-block').clone();
    block.find("input").val(""); // clear inputs
    $("#teacher-container").append(block);
  });

  // Remove block
  $(document).on('click', '.btn-remove', function(){
    if ($(".teacher-block").length > 1) {
      $(this).closest('.teacher-block').remove();
    } else {
      alert("At least one teacher block is required!");
    }
  });



    $('#userslsctstate').on('change', function() {
                  var userslsctstate = $(this).val();
                  $.ajax({
                          url:'<?=base_url();?>Menu/GetDistrictINState',
                          type: "POST",
                          data: {
                              userslsctstate: userslsctstate
                          },
                          cache: false,
                          success: function a(result){
                              $('#select_district').html(result);
                          }
                          });
                  });
                  $('#select_district').on('change', function() {
                  var selectdistrict = $(this).val();
                  $.ajax({
                          url:'<?=base_url();?>Menu/GetCityInDistrict',
                          type: "POST",
                          data: {
                              selectdistrict: selectdistrict
                          },
                          cache: false,
                          success: function a(result){
                              $('#select_city').html(result);
                          }
                          });
                  });
  });

</script>
<script>
    document.getElementById('travelClusterSelect').addEventListener('change', function() {
        const selectedValue = this.value;
        const detailsDiv = document.getElementById('clusterDetailsDiv');
        
        // Clear the div if no cluster is selected
        if (!selectedValue) {
            detailsDiv.innerHTML = '';
            return;
        }
        
        // Build the URL using the selected cluster ID
        const url = `https://stemapp.in/Menu/TravelClusterDetailsByID/${selectedValue}`;
        
        // Create an anchor tag with id and insert into the div
        detailsDiv.innerHTML = `
            <a target="_blank" id="viewClusterLink" href="${url}" target="_blank" class="btn btn-primary">
                View Cluster Details
            </a>
        `;
    });
</script>