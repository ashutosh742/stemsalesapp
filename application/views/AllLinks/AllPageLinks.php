<style>
.mainBlock div h5,.mainBlock div>ul>li>a{text-transform:capitalize}.sectionTitle{margin:0 auto;padding:0 0 10px;text-align:center;font-weight:500;position:relative}.sectionContent{color:#555;font-size:14px;display:block;width:100%;max-width:600px;margin:0 auto 20px;text-align:center}.mainBlock{margin-top:10px;padding:10px;border-radius:10px!important;min-height:230px;min-width:300px;display:flex;align-items:center;justify-content:center;transition:.5s;position:relative}.cat1::after{background:0 0;border:1px solid #1ab6a9}.cat1:hover::after,.cat2:hover::after,.cat3:hover::after,.cat4:hover::after,.cat5:hover::after,.cat6:hover::after,.cat7:hover::after,.cat8:hover::after{border-color:#fff;color:#fff}.cat1:hover{background:#1ab6a9!important}.cat1{background:#effaf8!important}.cat1 a{color:#1ab6a9}.cat2::after{background:0 0;border:1px solid #f06378}.cat2:hover{background:#f06378!important}.cat2,.cat7{background:#fef2f4!important}.cat2 a{color:#f06378}.cat3::after{background:0 0;border:1px solid #f8b81f}.cat3:hover{background:#f8b81f!important}.cat3{background:#fffaef!important}.cat3 a{color:#f8b81f}.cat4::after{background:0 0;border:1px solid #0ecd73}.cat4:hover{background:#0ecd73!important}.cat4{background:#eefbf5!important}.cat4 a{color:#0ecd73}.cat5::after{background:0 0;border:1px solid #8e56ff}.cat5:hover{background:#8e56ff!important}.cat5{background:#f7f3ff!important}.cat5 a{color:#8e56ff}.cat6::after{background:0 0;border:1px solid #f8941f}.cat6:hover,.cat7:hover,.cat8:hover{background:#f8941f!important}.cat6{background:#fff7ef!important}.cat6 a{color:#f8941f}.cat7::after{background:0 0;border:1px solid #25f981}.cat7:hover::after:hover,.cat8:hover{background:#f92596!important}.cat8{background:#fbfef2!important}.cat7 a{color:#3af925}.cat8::after{background:0 0;border:1px solid #25e4f9}.cat8 a{color:#f92596}.gradient-animated-text{background:linear-gradient(270deg,#ff6ec4,#7873f5,#1fd1f9,#ff6ec4);background-size:800% 800%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;animation:8s infinite animatedGradient;font-weight:700}@keyframes animatedGradient{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.mainBlock div h5{font-size:18px;margin:0}.mainBlock:hover>div>h5,.mainBlock:hover>div>ul>li>a{color:#fff!important}.mainBlock :hover>,.mainBlock div>ul>li{text-align:left;list-style-type:disclosure-closed}.mainBlock div>ul>li::marker{color:#ff6ec4}.mainBlock:hover ul>li::marker{color:#fff}.mainBlock div>ul>li>a :hover{color:#000!important}.shadow{box-shadow:rgba(0,0,0,.15) 0 2px 8px}
.mainBlock ul li { text-align: left;}
/* .mainBlock { width: max-content;} */
.mainBlock { min-height: 350px;}
.live-badge{display:inline-flex;align-items:center;background-color:red;color:#fff;font-weight:700;border-radius:20px;font-size:14px;font-family:Arial,sans-serif;animation:1s infinite pulse;box-shadow:0 0 5px red}.live-dot{width:8px;height:8px;background-color:#fff;border-radius:50%;margin-right:6px;animation:1s infinite blink}@keyframes blink{0%,100%,50%{opacity:1}25%,75%{opacity:0}}@keyframes pulse{0%,100%{box-shadow:0 0 5px red}50%{box-shadow:0 0 20px red}}
.custom-btn{color:#fff;border-radius:5px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden;width:fit-content}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}.btn-11.partnearray{background:navy!important}.btn-11.categoryarray{background:#ff007f!important}
.cat8 {
    background: linear-gradient(to right, #fff8e1, #ffffff)!important;
    /* background-image: var(--af-modal-bg-gradient, linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #fff 28.3%, #fff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%))!important;; */
}
</style>
<div class="container-fluid newSection">
<?php 
if(!in_array($user['type_id'],[16])){
if(!in_array($user['type_id'],[15,1])){
$targetDatss = $this->Menu_model->GetCurrentQuarterQuarterTarget($user['user_id']);
$$targetDatsscnt = sizeof($targetDatss);
// $$targetDatsscnt = 0;
if($$targetDatsscnt > 0){
    $target_id = $targetDatss[0]->id;
    $no_of_meeting            = $targetDatss[0]->no_of_meeting;
    $currentQuarter           = $targetDatss[0]->currentQuarter;
    $twetenty_closure_funnel  = $targetDatss[0]->twetenty_closure_funnel;
    $potential_funnel_for_fy  = $targetDatss[0]->potential_funnel_for_fy;
    $to_be_nurtured_for_fy    = $targetDatss[0]->to_be_nurtured_for_fy;
    $fifity_new_lead_funnel   = $targetDatss[0]->fifity_new_lead_funnel;
    $anchor_client_meeting    = $targetDatss[0]->anchor_client_meeting;
    $this_reviewtype          = $targetDatss[0]->reviewtype;
    $tstart_date              = $targetDatss[0]->start_date;
    $tend_date                = $targetDatss[0]->end_date;
    $areview_id               = $targetDatss[0]->review_id;
    $proposal_send                          = $targetDatss[0]->proposal_send;
    $bd_potentional_client_proposal_send    = $targetDatss[0]->bd_potentional_client_proposal_send;
    $proposals_sent_and_closure             = $targetDatss[0]->proposals_sent_and_closure;

    $proposal_to_be_sent_review             = $targetDatss[0]->proposal_to_be_sent_review;
    $proposal_to_be_sent_target             = $targetDatss[0]->proposal_to_be_sent_target;
    $closure_pipeline_in_october            = $targetDatss[0]->closure_pipeline;

    $allMeeting               = $this->Menu_model->GetMeetingBetweenDate($user['user_id'],$tstart_date,$tend_date);
    $allMeetingcnt            = sizeof($allMeeting);
  
    $max_check_date = '2025-02-14';
    
    // if($user['user_id'] == 100070){
    //     echo "target_id - "+$target_id;
    //     echo "<pre>";
    //     print_r($targetDatss);
    // }


   


    $reviewsDatas       = $this->Menu_model->getReviewByRID($areview_id);
    $review_start_date  = $reviewsDatas[0]->startt;
    $getPastQuarterStartDate =  $this->Menu_model->getPastQuarterStartDate($review_start_date);
    $review_start_date =    $max_check_date;   
    // $review_start_date =    $getPastQuarterStartDate;   
    $previousQuarterDate                  = $this->Menu_model->getPreviousQuarterStartDate();
    $meetingDone                          = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNot($user['user_id'],$review_start_date,$tend_date,$user['user_id'],
    $user['user_id']);

    $total_meetings                             = $meetingDone[0]->total_meetings;
    $proposal_sent                              = $meetingDone[0]->proposal_sent;
    $proposal_not_sent                          = $meetingDone[0]->proposal_not_sent;
    $proposal_sent_but_closure_not_done         = $meetingDone[0]->proposal_sent_but_closure_not_done;
    $bd_potentional_client_proposal_not_sent    = $meetingDone[0]->bd_potentional_client_proposal_not_sent;
    $proposal_send                              = $targetDatss[0]->proposal_send;
    $bd_potentional_client_proposal_send        = $targetDatss[0]->bd_potentional_client_proposal_send;
    $proposals_sent_and_closure                 = $targetDatss[0]->proposals_sent_and_closure;
    $no_of_prospective                          = $targetDatss[0]->no_of_prospective;
 
    $check_review_start_date  = $reviewsDatas[0]->startt;
    $check_review_start_date = date('Y-m-d', strtotime($check_review_start_date));
    
    $meetingDoneAFR                          = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNotTarget($user['user_id'],$review_start_date,$tend_date,$user['user_id'],$user['user_id'],$check_review_start_date);

    $proposal_sent_after_review                       = $meetingDoneAFR[0]->proposal_sent_after_review;
    $bd_potentional_client_proposal_sent_after_review = $meetingDoneAFR[0]->bd_potentional_client_proposal_sent_after_review;
    $proposal_sent_closure_done_after_review          = $meetingDoneAFR[0]->proposal_sent_closure_done_after_review;
    if($proposal_send > $proposal_sent_after_review){
        $proposal_send_rem       = $proposal_send - $proposal_sent_after_review;
        $proposal_send_rem_text  = "<span class='bg-danger p-2'>NO</span>";
    }else{
        $proposal_send_rem       = 0;
        $proposal_send_rem_text  = "<span class='bg-success p-2'>YES</span>";
    }
    if($bd_potentional_client_proposal_send > $bd_potentional_client_proposal_sent_after_review){
        $bd_potentional_client_proposal_send_rem       = $bd_potentional_client_proposal_send - $bd_potentional_client_proposal_sent_after_review;
        $bd_potentional_client_proposal_send_rem_text  = "<span class='bg-danger p-2'>NO</span>";
    }else{
        $bd_potentional_client_proposal_send_rem       = 0;
        $bd_potentional_client_proposal_send_rem_text  = "<span class='bg-success p-2'>YES</span>";
    }
    if($proposals_sent_and_closure > $proposal_sent_closure_done_after_review){
        $proposals_sent_and_closure_send_rem       = $proposals_sent_and_closure - $proposal_sent_closure_done_after_review;
        $proposals_sent_and_closure_send_rem_text  = "<span class='bg-danger p-2'>NO</span>";
    }else{
        $proposals_sent_and_closure_send_rem       = 0;
        $proposals_sent_and_closure_send_rem_text  = "<span class='bg-success p-2'>YES</span>";
    }



    

    ?>
<div class="card shadow-sm mb-3" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
  <div class="card-body" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
    <div class="text-center">
            <h5 style="color: navy;">🎯 Target Summary of this <?=$this_reviewtype?> 📋</h5>
            <h6 style="color: navy;">🗓️ <?=$tstart_date?> To <?=$tend_date?></h6>
            <mark class="p-1">🔖 <?=$this_reviewtype?></mark>
    </div>
    <hr>
    <div class="row text-center">
      <div class="col mb-2">
        <strong>🎯 Target</strong><hr>
        <span>RP Meetings</span>
      </div>
      <div class="col mb-2">
        <strong>📊 Total Target</strong><hr>
        <span><?=$no_of_meeting?></span>
      </div>
      <div class="col mb-2">
        <strong>✅ Completed</strong><hr>
        <span> <a href="<?=base_url()."Menu/TargetMeetingDetails/total_meetings/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$allMeetingcnt</span>"; ?></a></span>
      </div>
      <div class="col mb-2">
        <strong>📉 Remaining</strong><hr>
        <?php 
         if($no_of_meeting > $allMeetingcnt){
            $pendingmeeting           = $no_of_meeting - $allMeetingcnt;
            echo $pendingmeeting_text = "<span class='bg-warning p-2'>$pendingmeeting</span>";
            }else{
                echo $pendingmeeting_text = "<span class='bg-warning p-2'>0</span>";
            }
        ?>
      </div>
      <div class="col mb-2">
        <strong>🏆 Achieved</strong><hr>
        <?php 
         if($no_of_meeting > $allMeetingcnt){
            echo $pendingmeeting_text = "<span class='bg-danger p-2'> NO</span>";
        }else{
            echo $pendingmeeting_text = "<span class='bg-success p-2'>✔ Yes</span>";
        }
        ?>
      </div>
    </div>
    <hr>
     <div class="text-center mb-2 p-2" style="background-color: rgba(224, 221, 175,.3); color: hsl(334, 61%, 37%);">
        <h5 style="color: navy;">📅 Meeting Done || 📄 Proposal Overview || ✅ Closure Status</h5> 
    </div>
    <hr>
     <div class="table-responsive text-nowrap">
        <table class="table" style="box-shadow: none;">
                            <thead>
                                  <tr class="text-center">
                                    <th scope="col">🎯 Target Name</th>
                                    <th scope="col">📊 In Total </th>
                                    <th scope="col">📊 Total Target</th>
                                    <th scope="col">✅ Complete</th>
                                    <th scope="col">📉 Remaining</th>
                                    <th scope="col">🏆 Achieved</th>
                                  </tr>
                            </thead>
                            <tbody>
                              <tr class="text-center">
                                <td> 📤 Total Proposals Not Sent</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_not_sent/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>" target="_BLANK">
                                     <?= $proposal_not_sent?>
                                  </a>
                                </td>
                                <td><?= $proposal_send?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/proposal_sent_after_review/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$proposal_sent_after_review?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$proposal_send_rem</span>"; ?></td>
                                <td><?= $proposal_send_rem_text ?></td>
                              </tr>
                              <tr class="text-center">
                                <td> 👥 BD Potential Proposals Not Sent</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/bd_potentional_client_proposal_not_sent/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>" target="_BLANK">
                                     <?= $bd_potentional_client_proposal_not_sent?>
                                  </a>
                                </td>
                                <td><?= $bd_potentional_client_proposal_send?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/bd_potentional_client_proposal_sent_after_review/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$bd_potentional_client_proposal_sent_after_review?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$bd_potentional_client_proposal_send_rem</span>"; ?></td>
                                <td><?= $bd_potentional_client_proposal_send_rem_text ?></td>
                              </tr>
                              
                              <tr class="text-center">
                                <td> ⏳ Proposals Sent, Closure Pending</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent_but_closure_not_done/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>" target="_BLANK">
                                     <?= $proposal_sent_but_closure_not_done?>
                                  </a>
                                </td>
                                <td><?= $proposals_sent_and_closure?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/proposal_sent_closure_done_after_review/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$proposal_sent_closure_done_after_review?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$proposals_sent_and_closure_send_rem</span>"; ?></td>
                                <td><?= $proposals_sent_and_closure_send_rem_text ?></td>
                              </tr>

                              <?php 
                              
                              if($currentQuarter == 'Q3'){

                                $cqInitdatas                        = $this->Menu_model->GetAllInitCallinCurrentQuarter($uid,$currentQuarter);
                                
                                $total_prospecting_funnel           = $cqInitdatas[0]->total_prospecting_funnel;
                                $total_proposal_to_be_sent_review   = $cqInitdatas[0]->total_proposal_to_be_sent_review;
                                $total_proposal_to_be_sent_target_init   = $cqInitdatas[0]->total_proposal_to_be_sent_target;
                                $total_closure_pipeline_init        = $cqInitdatas[0]->total_closure_pipeline;


                                
                                $cmTargetDatas                       = $this->Menu_model->GetAllCompleteTargetinCurrentQuarter($uid,$currentQuarter,$review_start_date,$tend_date);
                                $total_complete_prospecting_funnel   = $cmTargetDatas[0]->total_complete_prospecting_funnel;

                                if ($total_complete_prospecting_funnel >= $no_of_prospective) {
                                    $no_of_prospective_rem      = 0;
                                    $no_of_prospective_rem_text = "<span class='bg-success text-white p-2'>✔ Yes</span>";
                                } else {
                                    $no_of_prospective_rem      = $no_of_prospective - $total_complete_prospecting_funnel;
                                    $no_of_prospective_rem_text = "<span class='bg-danger text-white p-2'>No</span>";
                                }

                            
                                ?>
                                
                            <tr class="text-center">
                                <td> Prospecting Funnel</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/FunnelDetails/total_prospecting_funnel/<?=$user['user_id']?>/userwise" target="_BLANK">
                                     <?= $total_prospecting_funnel?>
                                  </a>
                                </td>
                                <td><?= $no_of_prospective?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/total_prospecting_funnel_in_current_quarter/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                 
                                   <?= $total_complete_prospecting_funnel;?>

                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$no_of_prospective_rem</span>"; ?></td>
                                <td><?= $no_of_prospective_rem_text ?></td>
                            </tr>

                            <?php 

                            $cmPropTargetDatas                              = $this->Menu_model->GetAllCompletePraposalTargetinCurrentQuarter($uid,$currentQuarter,$review_start_date,$tend_date);
                            $total_complete_proposal_to_be_sent_review      = $cmPropTargetDatas[0]->total_complete_proposal_to_be_sent_review;
                            $total_complete_proposal_to_be_sent_target      = $cmPropTargetDatas[0]->total_complete_proposal_to_be_sent_target;

                            if ($total_complete_proposal_to_be_sent_review >= $proposal_to_be_sent_review) {
                                $proposal_to_be_sent_review_rem      = 0;
                                $proposal_to_be_sent_review_rem_text = "<span class='bg-success text-white p-2'>✔ Yes</span>";
                            } else {
                                $proposal_to_be_sent_review_rem      = $proposal_to_be_sent_review - $total_complete_proposal_to_be_sent_review;
                                $proposal_to_be_sent_review_rem_text = "<span class='bg-danger text-white p-2'>No</span>";
                            }

                            ?>

                            <tr class="text-center">
                                <td> Proposal to Be Sent (Review)</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/FunnelDetails/total_proposal_to_be_sent_review/<?=$user['user_id']?>/userwise" target="_BLANK">
                                     <?= $total_proposal_to_be_sent_review?>
                                  </a>
                                </td>
                                <td><?= $proposal_to_be_sent_review?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/total_complete_proposal_to_be_sent_review/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$total_complete_proposal_to_be_sent_review?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$proposal_to_be_sent_review_rem</span>"; ?></td>
                                <td><?= $proposals_sent_and_closure_send_rem_text ?></td>
                            </tr>

                            <?php 
                           
                            if ($total_complete_proposal_to_be_sent_target >= $total_proposal_to_be_sent_target_init) {
                                $total_proposal_to_be_sent_target_rem      = 0;
                                $total_proposal_to_be_sent_target_rem_text = "<span class='bg-success text-white p-2'>✔ Yes</span>";
                            } else {
                                $total_proposal_to_be_sent_target_rem      = $proposal_to_be_sent_target - $total_complete_proposal_to_be_sent_target;
                                $total_proposal_to_be_sent_target_rem_text = "<span class='bg-danger text-white p-2'>No</span>";
                            }

                            ?>

                            <tr class="text-center">
                                <td> Proposal to Be Sent - Target</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/FunnelDetails/total_proposal_to_be_sent_target/<?=$user['user_id']?>/userwise" target="_BLANK">
                                     <?= $total_proposal_to_be_sent_target_init?>
                                  </a>
                                </td>
                                <td><?= $proposal_to_be_sent_target?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/total_complete_proposal_to_be_sent_target/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$total_complete_proposal_to_be_sent_target?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$total_proposal_to_be_sent_target_rem</span>"; ?></td>
                                <td><?= $total_proposal_to_be_sent_target_rem_text ?></td>
                            </tr>


                            <?php 

                            $cmCloserPipeLineTargetDatas     = $this->Menu_model->GetAllCompleteCloserPipelineTargetinCurrentQuarter($uid,$currentQuarter,$review_start_date,$tend_date);
                            $total_complete_closure_pipeline      = $cmCloserPipeLineTargetDatas[0]->total_complete_closure_pipeline;
  
                            if ($total_complete_closure_pipeline >= $closure_pipeline_in_october) {
                                $total_complete_closure_pipeline_rem      = 0;
                                $total_complete_closure_pipeline_rem_text = "<span class='bg-success text-white p-2'>✔ Yes</span>";
                            } else {
                                $total_complete_closure_pipeline_rem      = $closure_pipeline_in_october - $total_complete_closure_pipeline;
                                $total_complete_closure_pipeline_rem_text = "<span class='bg-danger text-white p-2'>No</span>";
                            }

                            ?>

                            <tr class="text-center">
                                <td> Closure Pipeline</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/FunnelDetails/total_closure_pipeline/<?=$user['user_id']?>/userwise" target="_BLANK">
                                     <?= $total_closure_pipeline_init?>
                                  </a>
                                </td>
                                <td><?= $closure_pipeline_in_october?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/total_complete_closure_pipeline/<?=$user['user_id']?>/<?=$review_start_date?>/<?=$tend_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$total_complete_closure_pipeline?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$total_complete_closure_pipeline_rem</span>"; ?></td>
                                <td><?= $total_complete_closure_pipeline_rem_text ?></td>
                            </tr>
                                
                                <?php 
                                }
                                
                                ?>

                            </tbody>
                          </table>
</div>
                        <div class="text-center">
                            <hr>
                            <a class="bg-primary p-2" target="_blank" href="<?=base_url()?>Menu/target_vs_achievement/<?=$target_id?>">View Details</a>
                        </div>
  </div>
</div>
<?php }else{ ?>
    
     <div class="card text-center" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <div class="text-danger">
                          <h5>⚠️ * No Target Setting in this Quarter</h5>
                        </div>
                    </div>
    <?php }} ?>
    <hr>
   
                        <div class="card mt-2 text-center" id="totdaysanalysisCard" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; padding: 20px;">
                       
                            <h5>📈 🌼 Todays Analysis 🚀 <hr style="width: 10%;">
                               <small><b><?= $tdate ?></b></small>
                               <small><b><?= date("H:i:s"); ?></b></small>
                               <hr style="width: 40%;">
                            </h5>
                          
                    
                        <?php 
                        $totalMeetingsUserByDatas   = $this->Menu_model->TotalMeetingDetailsByRolesUid($uid,$tdate,$tdate,$uid,'all');
                        
                        
                        $todaysMeetDatas                         = $totalMeetingsUserByDatas[0];
                        $total_plan_meetings                     = $todaysMeetDatas->total_plan_meetings;
                        $complete_meetings                       = $todaysMeetDatas->complete_meetings;
                        $not_complete_meetings                   = $todaysMeetDatas->not_complete_meetings;
                        $total_scheduled_meetings                = $todaysMeetDatas->total_sheduled_meetings;
                        $total_complete_scheduled_meetings       = $todaysMeetDatas->total_complete_sheduled_meetings;
                        $not_complete_scheduled_meetings         = $todaysMeetDatas->not_complete_sheduled_meetings;
                        $total_barg_meetings                     = $todaysMeetDatas->total_barg_meetings;
                        $total_complete_barg_meetings            = $todaysMeetDatas->total_complete_barg_meetings;
                        $not_complete_barg_meetings              = $todaysMeetDatas->not_complete_barg_meetings;
                        $total_join_meetings                     = $todaysMeetDatas->total_join_meetings;
                        $total_complete_join_meetings            = $todaysMeetDatas->total_complete_join_meetings;
                        $not_complete_join_meetings              = $data->not_complete_join_meetings;
                        $total_RP_meetings                       = $todaysMeetDatas->total_RP_meetings;
                        $total_NO_RP_meetings                    = $todaysMeetDatas->total_NO_RP_meetings;
                        $total_Only_Got_details_meetings         = $todaysMeetDatas->total_Only_Got_details_meetings;
                        $total_fresh_meetings                    = $todaysMeetDatas->total_fresh_meetings;
                        $total_re_meetings                       = $todaysMeetDatas->total_re_meetings;
                        $total_anchor_clients_meetings           = $todaysMeetDatas->total_anchor_clients_meetings;
                        $total_complete_anchor_clients_meetings  = $todaysMeetDatas->total_complete_anchor_clients_meetings;
                        $total_pending_anchor_clients_meetings   = $todaysMeetDatas->total_pending_anchor_clients_meetings;
                        $toalNoRPMeetDatas          = $this->Menu_model->NoRPMeetingDetailsByRolesUid($uid,$tdate,$tdate,$uid,'all');
                        $totalNewBargNORPCount      = $toalNoRPMeetDatas['result1'];
                        $total_barg_no_rp_meetings  = $totalNewBargNORPCount[0]->total_barg_no_rp_meetings;
                        
                        ?>
                        <div class="table-responsive text-nowrap">
                            <div class="text-danger p-3">
                                <h6>
                                <mark>🤝 Todays Meetings </mark>
                                <hr style="width: 10%;">
                                </h6>
                            </div>
                            <table class="table" style="box-shadow: none;">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                        <th scope="col">📝 Planned</th>
                                        <th scope="col">⏳ Pending</th>
                                        <th scope="col">✅ Complete</th>
                                        <th scope="col">📌 RP</th>
                                        <th scope="col">🚫 No RP</th>
                                        <th scope="col">🚫 No RP (New Barg)</th>
                                        <th scope="col">📄 Got Details</th>
                                        <th scope="col">🌱 Fresh</th>
                                        <th scope="col">🔄 Re-Meetings</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-primary p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_plan_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $total_plan_meetings ?>
                                            </a>
                                        </td>
                                       
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-warning p-2" href="<?=base_url()?>Reports/MeetingsDatas/not_complete_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $not_complete_meetings ?>
                                            </a>
                                        </td>
                                         <td>
                                            <a target="_BLANK" class="badge badge-pill badge-success p-2" href="<?=base_url()?>Reports/MeetingsDatas/complete_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $complete_meetings ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-info p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_RP_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $total_RP_meetings ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-danger p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_NO_RP_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $total_NO_RP_meetings ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-danger p-2" href="<?=base_url()?>Reports/MeetingsDatasNewBargMeetingNORP/total_new_barg_no_rp_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $total_barg_no_rp_meetings ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-secondary p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_Only_Got_details_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $total_Only_Got_details_meetings ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-success p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_fresh_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $total_fresh_meetings ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-dark p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_re_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                <?= $total_re_meetings ?>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                <table class="table" style="box-shadow: none;">
                                    <thead class="thead-dark">
                                        <tr class="text-center">
                                            <th scope="col">🏢 Total Anchor Clients</th>
                                            <th scope="col">✅ Completed Anchor Clients</th>
                                            <th scope="col">⏳ Pending Anchor Clients</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-primary p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_anchor_clients_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                    <?= $total_anchor_clients_meetings ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-success p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_complete_anchor_clients_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                    <?= $total_complete_anchor_clients_meetings ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-warning p-2" href="<?=base_url()?>Reports/MeetingsDatas/total_pending_anchor_clients_meetings/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">
                                                    <?= $total_pending_anchor_clients_meetings ?>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <hr>
                             <div class="text-danger">
                                <h6>
                                <mark>📃 Todays Proposal Details 🌼</mark>
                                <hr style="width: 10%;">
                                </h6>
                            </div>
                             <?php 
                            $totalProposalUserByDatas                       = $this->Report_model->GetProposalDetailbyDateByRoles($uid,$tdate,$tdate,7,$uid,'all');
                            $totalProposalTasks                             = $totalProposalUserByDatas['totalProposalTasks'];
                            $totalProposalTasks                             = $totalProposalTasks[0];
                            
                            $total_proposal_task                            = $totalProposalTasks->total_proposal_task;
                            $complete_proposal_task                         = $totalProposalTasks->complete_proposal_task;
                            $pending_proposal_task                          = $totalProposalTasks->pending_proposal_task;
                            $proposal_approved                              = $totalProposalTasks->proposal_approved;
                            $proposal_reject                                = $totalProposalTasks->proposal_reject;
                            $pending_for_approved                           = $totalProposalTasks->pending_for_approved;
                             ?>       
                            <table class="table" style="box-shadow: none;">
                                <thead class="thead-dark">
                                <tr class="text-center">
                                        <th scope="col">📑 Planned Proposal</th>
                                        <th scope="col">✅ Complete Task</th>
                                        <th scope="col">⏳ Pending Task</th>
                                        <th scope="col">👍 Proposal Approved</th>
                                        <th scope="col">❌ Proposal Reject</th>
                                        <th scope="col">🕒 Pending For Approved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                            href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/7">
                                            <?= $total_proposal_task ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-success p-2" 
                                            href="<?=base_url()?>Reports/ProposalDetailsData/complete_proposal_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/7">
                                            <?= $complete_proposal_task ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-warning p-2" 
                                            href="<?=base_url()?>Reports/ProposalDetailsData/pending_proposal_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/7">
                                            <?= $pending_proposal_task ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-info p-2" 
                                            href="<?=base_url()?>Reports/ProposalDetailsData/proposal_approved/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/7">
                                            <?= $proposal_approved ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-danger p-2" 
                                            href="<?=base_url()?>Reports/ProposalDetailsData/proposal_reject/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/7">
                                            <?= $proposal_reject ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a target="_BLANK" class="badge badge-pill badge-secondary p-2" 
                                            href="<?=base_url()?>Reports/ProposalDetailsData/pending_for_approved/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/7">
                                            <?= $pending_for_approved ?>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="text-danger">
                                <h6>
                                <mark>🌟 Todays Task Details 🌼</mark>
                                <hr style="width: 10%;">
                                </h6>
                            </div>
                    
                            <?php 
                            $getTotalTasks                     = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTask($uid,$tdate,$tdate,'all',$uid,'all');
                           
                            $total_task                                 = $getTotalTasks[0]->total_task;
                            $total_complete_task                        = $getTotalTasks[0]->total_complete_task;
                            $total_pending_task                         = $getTotalTasks[0]->total_pending_task;
                            $total_status_change_task                   = $getTotalTasks[0]->total_status_change_task;
                            
                            
                            $total_own_funnel_task                      = $getTotalTasks[0]->total_own_funnel_task;
                            $total_own_funnel_complete_task             = $getTotalTasks[0]->total_own_funnel_complete_task;
                            $total_own_funnel_pending_task              = $getTotalTasks[0]->total_own_funnel_pending_task;
                            $total_own_funnel_status_change_task        = $getTotalTasks[0]->total_own_funnel_status_change_task;
                            $total_team_funnel_task                     = $getTotalTasks[0]->total_team_funnel_task;
                            $total_team_funnel_complete_task            = $getTotalTasks[0]->total_team_funnel_complete_task;
                            $total_team_funnel_pending_task             = $getTotalTasks[0]->total_team_funnel_pending_task;
                            $total_team_funnel_status_change_task       = $getTotalTasks[0]->total_team_funnel_status_change_task;
                            $getTotalTasksStatus   = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTaskBYStatus($uid,$tdate,$tdate,'all',$uid,'all');
                            $total_conversions     = $getTotalTasksStatus['total_conversions'];   
         
                            $all_conversions_data  = $total_conversions[0]->total_conversions;    
                            $positive_conversions  = $total_conversions[0]->positive_conversions;    
                            $negative_conversions  = $total_conversions[0]->negative_conversions;    
                            $other_conversions     = $total_conversions[0]->other_conversions;    
                            ?>
                                <table class="table" style="box-shadow: none;">
                                    <thead class="thead-dark">
                                        <tr class="text-center">
                                            <th scope="col">📝 Total Task</th>
                                            <th scope="col">✅ Complete Task</th>
                                            <th scope="col">⏳ Pending Task</th>
                                            <th scope="col">🔄 Status Change</th>
                                            <th scope="col">🔄 Positive Conversions</th>
                                            <th scope="col">🔄 Negative Conversions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                <?= $total_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-success p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_complete_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                <?= $total_complete_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-warning p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_pending_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                <?= $total_pending_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-info p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_status_change_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                <?= $total_status_change_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-info p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/positive_conversions/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                <?= $positive_conversions ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-info p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/negative_conversions/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                <?= $negative_conversions ?>
                                                </a>
                                            </td>
                                     
                                      
                                        </tr>
                                    </tbody>
                                </table>
                               <?php if(!in_array($user['type_id'],[3])){ ?>
                                <hr>
                                 <div class="text-danger">
                                    <h6>
                                     <mark>🙋‍♂️ Todays Own Funnel Task 🌼</mark>
                                    <hr style="width: 10%;">
                                    </h6>
                                </div>
                                <table class="table" style="box-shadow: none;">
                                    <thead class="thead-dark">
                                        <tr class="text-center">
                                            <th scope="col">📝 Total Task</th>
                                            <th scope="col">✅ Complete Task</th>
                                            <th scope="col">⏳ Pending Task</th>
                                            <th scope="col">🔄 Status Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_own_funnel_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-success p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_complete_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_own_funnel_complete_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-warning p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_pending_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_own_funnel_pending_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-info p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_status_change_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_own_funnel_status_change_task ?>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <hr>
                                 <div class="text-danger">
                                    <h6>
                                    <mark>👥 Todays Team Funnel Task 🌼</mark>
                                    <hr style="width: 10%;">
                                    </h6>
                                </div>
                                <table class="table" style="box-shadow: none;">
                                    <thead class="thead-dark">
                                        <tr class="text-center">
                                            <th scope="col">📝 Total Task</th>
                                            <th scope="col">✅ Complete Task</th>
                                            <th scope="col">⏳ Pending Task</th>
                                            <th scope="col">🔄 Status Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_team_funnel_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-success p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_complete_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_team_funnel_complete_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-warning p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_pending_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_team_funnel_pending_task ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_BLANK" class="badge badge-pill badge-info p-2" 
                                                href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_status_change_task/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/all">
                                                    <?= $total_team_funnel_status_change_task ?>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php } ?>
                               <?php 
                                                              
                                $bdconversions = $this->Menu_model->GetTodaysConversionDatas($uid,$tdate,$tdate);
                               
                                ?>
                                 <hr>
                                 <div class="text-danger">
                                    <h6>
                                    <mark>🔄 Today's Our & Team Status Conversion 🌼</mark>
                                    <hr style="width: 10%;">
                                    </h6>
                                </div>
                                <table class="table" style="box-shadow: none;">
                                    <thead class="thead-dark">
                                        <tr class="text-center">
                                            <th scope="col">🔄 Status Change</th>
                                            <th scope="col">✅ Total Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                         if(sizeof($bdconversions) > 0):
                                            foreach($bdconversions as $bdconversion): ?>
                                                <tr class="text-center">
                                                    <td>
                                                        <a target="_BLANK" class="p-2" style="color:navy; font-weight: 500;"
                                                        href="<?=base_url()?>Reports/TodaysConversionDatas/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/<?= $bdconversion->status_change_id;?>">
                                                            <?= $bdconversion->status_change ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                                        href="<?=base_url()?>Reports/TodaysConversionDatas/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/<?= $bdconversion->status_change_id;?>">
                                                            <?= $bdconversion->total_changes ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        endif;
                                        ?>
                                    </tbody>
                                </table>
                   
                            <hr>
                            <?php 
                            $todaysAnalysis_name = preg_replace("/[^A-Za-z0-9]/", "_",$user['name'].' '.$tdate);
                             ?>
                                <div class="text-center">
                                    <button id="downloadTodaysAnalysisPDF" class="custom-btn btn-11 p-2">📥 Download Today's Analysis PDF</button>
                                </div>
                            </div>
                        </div>
                
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
                    <script>
                    document.getElementById("downloadTodaysAnalysisPDF").addEventListener("click", function () {
                        const button = this; 
                        button.disabled = true; 
                        button.innerText = "⏳ Please Wait...";
                        const { jsPDF } = window.jspdf;
                        const input = document.getElementById("totdaysanalysisCard");
                        html2canvas(input, {
                            scale: 2,
                            useCORS: true
                        }).then((canvas) => {
                            const imgData = canvas.toDataURL("image/png");
                            const pdf = new jsPDF("p", "mm", "a4");
                            const imgWidth = 210; 
                            const pageHeight = 297; 
                            const imgHeight = (canvas.height * imgWidth) / canvas.width;
                            let heightLeft = imgHeight;
                            let position = 0;
                            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                            while (heightLeft > 0) {
                                position = heightLeft - imgHeight;
                                pdf.addPage();
                                pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                                heightLeft -= pageHeight;
                            }
                            pdf.save("<?=$todaysAnalysis_name?>.pdf");
                            // Reset button text
                            button.disabled = false;
                            button.innerText = "📥 Download PDF";
                        });
                    });
                    </script>
          <hr>
            <h3 class="sectionTitle ">
              📊 <span class="themeText gradient-animated-text">Data Analysis </span> 🔍
            </h3>
            <p class="sectionContent">
              Data Analysis is the process of inspecting, cleaning, transforming, and modeling data to discover meaningful patterns, trends, and insights. 📈 It helps in making informed decisions by turning raw data into actionable knowledge. 💡
            </p>
          <hr>
          <div class="container-data">
            <div class="row">
            
              <div class="col">
                  <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                      <div>
                          <h5>👥 <?php if($user['type_id'] == 3){ ?> User <?php }else{ ?> Team <?php } ?> Detail</h5><hr>
                          <ul>
                              <li>
                                  <a href="<?=base_url()?>Reports/BDDetails">
                                      📋 <?php if($user['type_id'] == 3){ ?> User <?php }else{ ?> Team <?php } ?> Details
                                  </a>
                              </li>
                          </ul>
                      </div>
                  </div>
              </div>
              <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🗂️ Task Report</h5>
                            <hr>
                            <ul>
                                <li><a href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTask">📝 Task Details</a></li>
                                <li><a href="<?=base_url()?>Reports/MeetingDetailNew">📅 Meeting Details</a></li>
                                <li><a href="<?=base_url()?>Reports/ProposalDetails">📨 Proposal Details</a></li>
                                <li><a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatus">📊 Meeting v/s Proposal Summary</a></li>
                                <li><a href="<?=base_url()?>Reports/FutureTask">🔮 Future Task Details</a></li>
                                <li><a href="<?=base_url()?>Reports/TeamConversionBetweenDatesData">🔄 Task Conversion Summary</a></li>
                                <li><a href="<?=base_url()?>Menu/PendingForWriteMomMeetingList">📝 Pending MoM Write After RP Meetings</a></li>
                                
                                <?php if($user['type_id'] !== 3){ ?>    
                                <!-- <li><a href="<?=base_url()?>Menu/UserWorkOnSelfAndOtherFunnel">🗂️ Work On Self And Other Funnel</a></li> -->
                                <?php } ?>
                                <?php if(in_array($user['type_id'],[2,1])){ ?>    
                                <li><a href="<?=base_url()?>Reports/AdminRemarksLeadsCurrentFY">🗂️ RM/Admin Remarks Leads</a></li>
                                <!-- <li><a href="<?=base_url()?>Reports/LineManagerCallingonRPLeads">🗂️ Line Manager Calling on RP Leads</a></li> -->
                                
                                <?php } ?>
                               <li><a href="<?=base_url()?>Reports/LineManagerCallingonRPLeads">🗂️ Line Manager Calling on RP Leads</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
             <div class="col">
                  <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                      <div>
                          <h5>🔁 Funnels Report</h5>
                          <hr>
                          <ul>
                              <li><a href="<?=base_url()?>Reports/OurTeamFunnal">📊 Funnel Details</a></li>
                              <?php if($user['type_id'] == 2){ ?> 
                              <li><a href="<?=base_url().'Menu/BDTOBDCTF'?>">🔄 Company TF BD to Other BD</a></li>
                              <?php } ?>
                              <li><a href="<?=base_url()?>Menu/GetAllCompaniesThatDoNotHavePrimaryContactDetailsData">⚠️ Companies without primary contact details</a></li>
                              <li> <a href="<?=base_url()?>Menu/ClosingTimeLineTargetAfterQuarterReview"> 🏷️ Closing Timeline Target</a></li>
                              
                            <li>
                                <a href="<?=base_url()?>Reports/SameStatusSinceDays">
                                   ⏳ Same Status Since (n) Days
                                </a>
                            </li>
                            <?php if($user['type_id'] !== 3){ ?>       
                                <li>
                                    <a href="<?=base_url()?>Reports/AfterAssignSameStatusSinceDays">
                                    ⏳ After Assign Same Status Since (n) Days
                                    </a>
                                </li>
                            <?php } ?>    
                            
                            <li><a href="<?=base_url()?>Reports/AfterAssignLineManagerSameStatusSinceDays">⏳ After RP Meetings Assign Same Status</a></li>
                            <li><a href="<?=base_url()?>Reports/AfterAssignLineManagerSameStatusSinceDaysTaskLog">⏳ Same Status With Task Count (On RP Leads)</a></li>
                            
                            <li>
                                <a href="<?=base_url()?>Menu/CheckCompanyCreateBetweenDate">
                                    🏢 Company Created Between Dates
                                </a>
                            </li>
                            <li> <a href="<?=base_url()?>Reports/CheckRemoveCompanyLogsBetweenDate"> 🚫 Deleted Company Between Dates</a></li>
                           <li><a href="<?=base_url()?>Reports/FunnelTransferDetails">🔄 Funnel Transfer Logs</a></li>  
                           
                           <?php if($user['type_id'] == 2){ ?>     
                                <!-- <li>
                                    <a href="<?=base_url()?>Menu/DeleteCMP">
                                        🗑️ Company Delete Request
                                    </a>
                                </li> -->
                           <?php } ?>  
                            <li>
                                <a href="<?=base_url()?>Reports/AllStatusChangedRequiredRequest">
                                  🔄 Status Changed Request
                                </a>
                            </li>     
                            <li>
                                <a href="<?=base_url()?>Reports/ClosurePipeLine">
                                    🎯 Closure PipeLine
                                </a>
                            </li>     
                           <li>
                                <a href="<?=base_url()?>Reports/TeamVisitInTravelCluster">
                                    🚀 Team Visit in Travel Cluster  
                                    <span style="font-size: 0.9rem; color: #555;">
                                        (RP • Proposal • Closure)
                                    </span>
                                </a>
                            </li>  
                           <li>
                                <a href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDone">
                                    🚫 More Than N Days No Activity Done By Main BD
                                </a>
                            </li>  
                             <?php  if(!in_array($user['type_id'],[3])){ ?> 
                                <li>
                                    <a href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManager">
                                        🚫 More Than N Days No Activity Done By Line Manager
                                    </a>
                                </li>  
                                <li>
                                    <a href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerWhereRPMeetingDone">
                                        🚫 More Than N Days No Activity Done By Line Manager Where the RP Meeting Done
                                    </a>
                                </li>  
                                 <li>
                                    <a href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerWhereProposalSent">
                                        🚫 More Than N Days No Activity Done By Line Manager Where the proposal was sent
                                    </a>
                                </li>  
                            <?php  } ?> 
                            <li>
                                    <a href="<?=base_url()?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_Report">
                                        Calling Outcome & RP Proposal Conversion Report
                                    </a>
                                </li>  
                          </ul>
                      </div>
                  </div>
              </div>
             <?php if($user['type_id'] == 2){ ?> 
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>👤 User Management</h5>
                            <hr>
                            <ul>
                                <li><a href="<?=base_url().'Menu/UserRegistrationNew'?>">➕ New User</a></li>
                                <li><a href="<?=base_url().'Reports/BDDetails'?>">✏️ Edit User</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>
           <div class="col">
                  <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                      <div>
                          <h5>📝 Review Report</h5>
                          <hr>
                          <ul>
                              <li><a href="<?=base_url().'Menu/ReviewDataReport'?>">📄 Review Data Report</a></li>
                              <?php  if(!in_array($user['type_id'],[2])){ ?> 
                              <li><a href="<?=base_url().'Menu/NewAnnualReviewReport'?>">📆 Annual Review Report</a></li>
                              <?php } ?> 
                              <li><a href="<?=base_url().'Reports/ReviewReportBDwise'?>">📄 BD Wise Review Report</a></li>
                          </ul>
                      </div>
                  </div>
              </div>
              <?php  if(in_array($user['type_id'],[2])){ ?> 
                    <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                       <div>
                            <h5>📝 Annual Review Report</h5>
                            <hr>
                            <ul>
                                <li>
                                    <a href="<?=base_url().'Menu/NewAnnualReviewReport'?>">
                                        📆 Annual Review Report
                                    </a>
                                </li>
                                <li>
                                    <a href="<?=base_url()?>Menu/NewAnnualReviewReportWithLastYearMeetingsDetails">
                                        📅 Annual Review Report With Last Year Meeting Details
                                    </a>
                                </li>
                                <li>
                                    <a href="<?=base_url()?>Menu/GetUserMarkedCurrentYearFocusFunnels">
                                        🎯 User Marked Current Year Focus Funnels
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php  } ?> 
           <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                    <div>
                        <h5>🗓️ Planner Report</h5>
                        <hr>
                        <ul>
                            <li><a href="<?=base_url();?>Menu/GetAllCompulsiveAndNeedYourAttentionLog">⚠️ Today's Compulsive & Needs Your Attention</a></li>
                            <li><a href="<?=base_url();?>Menu/GetAllPlannerLogPlannedByUsers">⚠️ Todays Replanned Task </a></li>
                            <li><a href="<?=base_url()?>/Reports/TeamPlannerReport">🗓️ Team Planner Reports</a></li>
                        </ul>
                    </div>
                </div>
            </div>
          <!-- <div class="col">
              <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                  <div>
                      <h5>🗓️ Planner Control</h5>
                      <hr>
                      <ul>
                          <li><a href="<?=base_url();?>Menu/GetAllCompulsiveAndNeedYourAttentionLog">⚙️ Task Execution Request By SC</a></li>
                          <li><a href="<?=base_url()?>">📌 Report 2</a></li>
                      </ul>
                  </div>
              </div>
          </div> -->
              <?php /*
            <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                     <div>
                        <h5>App Control</h5>
                     <hr>
                    <ul>
                        <!-- <li><a href="<?=base_url()?>Menu/GetAllTaskExecutionPermissionsbySC"> Task Execution Request By SC</a></li> -->
                        <!-- <li><a href="<?=base_url()?>Menu/DynamicTaskFormForSC">Dynamic Task Form For SC</a></li> -->
                        <li><a href="<?=base_url()?>">Control1</a></li>
                        <li><a href="<?=base_url()?>">Control2</a></li>
                    </ul>
                     </div>
                </div>
              </div>
                */ ?>
              <div class="col">
                  <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                      <div>
                          <h5>🕒 User App Usage Time</h5>
                          <hr>
                          <ul>
                              <li><a href="<?=base_url()?>Menu/TodaysUserTotalTimeSpentByUrlPath">⏱️ Time Spent By User on App</a></li>
                              <!-- <li><a href="<?=base_url()?>"> Report2</a></li> -->
                          </ul>
                      </div>
                  </div>
              </div>
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🏖️ Leave Management</h5>
                            <hr>
                            <?php 
                             if(in_array($user['type_id'],[1,2])){
                                $lh_message = 'Leave / Holiday';
                            }else{
                                $lh_message = 'Leave';
                            }
                            ?>
                            <ul>
                                <!-- <li><a href="<?=base_url()?>Management/HolidayList">📅 Manage Holiday List</a></li> -->
                                <li><a href="<?=base_url()?>Management/LeaveManagement">📅 <?=$lh_message?> Management</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php if($user['type_id'] !== 3){ ?> 
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🌴 Day Management</h5>
                            <hr>
                            <ul>
                                <li><a href="<?=base_url()?>Reports/DayStartLive"> 🌄 Day Start</a></li>
                                <li><a href="<?=base_url()?>Reports/DayEndLive"> 🌅 Day End</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>

            <?php if($user['type_id'] !== 3){ ?> 
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🌴 Insides Sales Request</h5>
                            <hr>
                            <ul>
                                <li><a href="<?=base_url()?>Menu/BDAssignRequestBYInsidesSales"> BD Assign Request BY Insides Sales</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>
            <?php if($user['type_id'] !== 3){ ?> 
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🌴 Check Management</h5>
                            <hr>
                            <ul>
                                 <?php if($user['type_id'] != 3){ ?>   
                                <li><a href="<?=base_url()?>Reports/DayManagementChecking">🌄 Day Check Management</a></li>
                                <li><a href="<?=base_url()?>Menu/TeamTaskCheck">📝 Team Task Check</a></li>
                                <li><a href="<?=base_url()?>Menu/TeamStatusChangeTaskCheck">📝 Team Status Change Task Check</a></li>
                                <li><a href="<?=base_url()?>Menu/TeamEmailTaskCheckNew">📧 Team Email Task Validate</a></li>
                                <?php } ?>   
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🌟 Star Rating Report</h5>
                            <hr>
                            <ul>
                                <li><a href="<?=base_url()?>Reports/DayManagementStarRating">🌞 Day Check Star Rating Report</a></li>
                                <li><a href="<?=base_url()?>Reports/TaskCheckingManagementStarRating">📋 Team Task Check Star Rating Report</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
           <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                    <div>
                        <h5>📍 Location</h5>
                        <hr>
                        <ul>
                            <li>
                                <a href="<?=base_url()?>Reports/IndiaLocationDetails">
                                    <?php if($user['type_id'] == 2){ ?>🛠️ Manage <?php } ?>📌 Location
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
           <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                    <div>
                        <h5>✅ MoM Approval Status </h5>
                         <hr>
                        <ul>
                            <li>
                                <a href="<?=base_url()?>Management/MoMApprovedStatus">
                                   MoM Status
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url()?>Menu/momdetail">
                                   MOM Detail
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php if($user['type_id'] !== 3){ ?> 
           <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                    <div>
                        <h5>📝 Special Remarks </h5>
                         <hr>            
                        <ul>
                            <li>
                                <a href="<?=base_url()?>Reports/SpecialRemarksLeadsCurrentFY">
                                  📝 Special Remarks Leads
                                </a>
                            </li>
                    </div>
                </div>
            </div>
             <?php } ?>
          
           <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                    <div>
                        <h5>📝 Quater Strategy </h5>
                         <hr>            
                        <ul>
                            <li>
                                <a href="<?=base_url()?>Menu/QuaterStrategyDeatils">
                                  📝 Quater Strategy Deatils
                                </a>
                            </li>
                    </div>
                </div>
            </div>
             
<style>
  .live-blink {
    animation: blink 1s infinite;
    color: red; /* Optional: To highlight the icon */
  }
  @keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0; }
    100% { opacity: 1; }
  }
</style>
               
             <div class="col">
                  <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                      <div>
                          <h5>🟢 Live Task & Planner Management</h5>
                          <hr>
                          <ul>
                              <!-- <li><a href="<?=base_url()?>Menu/LiveMeetingTask">📡 <span class="live-badge">
                                 <span class="live-dot"></span>
                                </span> Live Meeting</a>
                                </li> -->
                   
                        <li><a href="<?=base_url()?>Menu/LiveMeetingTask"><span class="live-blink">📡</span> 📅 Live Meeting</a></li>
                        <li><a href="<?=base_url()?>Menu/LiveCallTask"><span class="live-blink">📡</span> 📞 Live Call</a></li>
                        <li><a href="<?=base_url()?>Menu/LiveEmailTask"><span class="live-blink">📡</span> 📧 Live Email (AutoTask)</a></li>
                        <li><a href="<?=base_url()?>Menu/LiveProposalTask"><span class="live-blink">📡</span> 📝 Live Proposal</a></li>
                        <?php if($user['type_id'] != 3){ ?> 
                        <li><a href="<?=base_url()?>Menu/LiveMomCheckTask"><span class="live-blink">📡</span> 📄 Live Mom Check</a></li>
                        <li><a href="<?=base_url()?>Menu/LiveProposalCheckTask"><span class="live-blink">📡</span> 📋 Live Proposal Check</a></li>
                        <li><a href="<?=base_url()?>Menu/LiveReviewCheck"><span class="live-blink">📡</span> 📝 Live Review Check</a></li>
                        <li><a href="<?=base_url()?>Reports/TodaysTaskPlannerLive"><span class="live-blink">📡</span> 📅 Live Todays Task Planner  </a></li>
                        <li><a href="<?=base_url()?>Reports/TaskPlannerLive"><span class="live-blink">📡</span> 📅 Live Next Days Task Planner  </a></li>
                        <li><a href="<?=base_url()?>Menu/DayCheckLive"><span class="live-blink">📡</span> 🌈 Live Day Check </a></li> 
                        <li><a href="<?=base_url()?>Menu/TaskCheckLive"><span class="live-blink">📡</span> 🏷️ Live Task Check </a></li>
                        <?php } ?> 
                            <?php if($user['type_id'] == 1){ ?> 
                              <!--<li><a href="<?=base_url()?>Menu/UserDayEnd"><span class="live-blink">📡</span> Live User Day End</a></li> -->
                            <?php } ?>
                          </ul>
                      </div>
                  </div>
              </div>
 
            <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                    <div>
                        <h5>🧳 Travel Advance</h5>
                        <hr>
                        <h6>📌 Request</h6>
                        <ul>
                            <li>✈️ <a href="<?=base_url()?>Menu/OurTeamTravelAdvanceRequest">Team Travel Advance Request</a></li>
                            <li>💰 <a href="<?=base_url()?>Menu/CashExpenseReport">Team Cash Expense Request</a></li>
                        </ul>
                        <hr>
                        <h5>📊 Report</h5>
                        <hr>
                        <ul>
                            <li>📑 <a href="<?=base_url()?>Menu/OurTeamTravelAdvanceReport"> <?php if($user['type_id'] == 3){echo "Our";}else{echo "Team";} ?> Travel Advance Report</a></li>
                            <li>🧾 <a href="<?=base_url()?>Menu/OurTeamCashExpenseReport"><?php if($user['type_id'] == 3){echo "Our";}else{echo "Team";} ?> Cash Expense Report</a></li>
                        </ul>
                        <?php if(in_array($user['type_id'],[3,4,13,15,24,18,19,20,21,22])){ ?> 
                        <hr>
                        <h6>👥 Our Travel Advance</h6>
                        <hr>
                        <ul>
                            <li>💵 <a href="<?=base_url()?>Menu/OurTravelAdvanceRequest"> Our Cash Advance Request</a></li>
                            <li>📝 <a href="<?=base_url()?>Menu/OurCashExpenseReport">Our Cash Expense Request</a></li>
                        </ul> 
                        <hr>
                        <h6>👥 ADD Meetings Expense</h6>
                        <hr>
                        <ul>
                            <li>📝 <a href="<?=base_url()?>Menu/UpdateTodaysMeetingsDetails">Update Meetings Expense Details</a></li>
                        </ul> 
                        <?php } ?> 


                        <hr>
                        <h5>💰 Advance vs 🤝 Meeting</h5>
                        <hr>
                        <ul>
                            <li>📑 <a href="<?=base_url()?>Reports/AdvanceVSRPMeetingCountWithNextNewAdvance"> Travel Advance – Meeting Overview</a></li>
                        </ul>

                         

                    </div>
                </div>
            </div>
  
              <!-- <div class="col">
                <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                     <div>
                        <h5>All BD Request</h5>
                     <hr>
                    <ul>
                        <li><a href="<?=base_url()?>">Report1</a></li>
                        <li><a href="<?=base_url()?>">Report2</a></li>
                    </ul>
                     </div>
                </div>
              </div> -->
              <div class="col">
                  <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                      <div>
                          <h5>🎯 Target <span>V/S</span> 🏆 Achievement</h5>
                          <hr>
                          <!-- <li><a href="<?=base_url()?>Menu/AddTarget">Add Target</a></li> -->
                          <ul>
                              <li><a href="<?=base_url()?>Menu/AddTargetListByUser">📋 Target List</a></li>
                          </ul>
                      </div>
                  </div>
              </div>
          <div class="col">
              <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                  <div>
                      <h5>📈 Graph Analysis</h5>
                      <hr>
                      <ul>
                          <li><a href="<?=base_url()?>SalesGraph/Dashboard">💹 Sales Graph</a></li>
                      </ul>
                  </div>
              </div>
          </div>
        
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🧭 Travel Cluster</h5>
                            <hr>
                            <ul>
                                <?php if(in_array($user['type_id'],[1,2,4,13,15,19,20,21,22,23,24])){ ?> 
                                    <li><a href="<?=base_url()?>Menu/TeamTravelClusterDetails">👥 Team Travel Cluster Details</a></li>
                                <?php } ?>
                                <?php if(in_array($user['type_id'],[13,4,22,23])){ ?> 
                                    <li><a href="<?=base_url()?>Menu/CreateTravelCluster">🆕 New Travel Cluster</a></li>
                                <?php } ?>
                                <?php if(in_array($user['type_id'],[3,24,13,4])){ ?> 
                                    <li><a href="<?=base_url()?>Menu/TravelClusterDetails">🌍 Our Travel Cluster Details</a></li>
                                <?php } ?>
                                <?php if(in_array($user['type_id'],[1,2,4,13,24,19,20,21,22,23])){ ?> 
                                    <li><a href="<?=base_url()?>Menu/TeamTravelClusterEditRequest">✏️ Team Travel Cluster Edit Request</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>




                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>📈 Handover & BD Request</h5>
                            <hr>

                            <?php 
                            if(in_array($user['type_id'],[3])){
                                $textMessage = 'Our';
                            }else{
                                $textMessage = 'Our & Team';
                            }
                            
                            ?>

                            <ul>
                                <li><a href="<?=base_url()?>Menu/HandoverDetails">🤝 Handover Details</a></li>
                                <li><a href="<?=base_url()?>Menu/BDRequestDetails">🤝 BD Request Details</a></li>
                                <li><a href="<?=base_url()?>Menu/ProgramTimelineDetails">🤝 Program Timeline Details</a></li>
                                <li><a href="<?=base_url()?>Menu/HandoverTimelineDetails">🤝 Handover Timeline Details</a></li>
                                <li><a href="<?=base_url()?>Reports/TodaysOperationTeamTaskPlannedByOurTeamProject">🤝 Operation Work on <?= $textMessage; ?> Project</a></li>
                            </ul>
                        </div>
                    </div>
                </div>



            <?php if(in_array($user['type_id'],[1,2])){ ?> 
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🏆 Upsell Client </h5>
                            <hr>
                           <ul>
                                <li><a href="<?=base_url()?>Menu/handoverapr">📑 Handover Approval</a></li>
                                <li><a href="<?=base_url()?>Menu/ArtworksHandling">🕒 Artwork Approval Pending</a></li>
                                <!-- https://stemapp.in/Menu/ArtworksHandling -->
                                <li><a href="<?=base_url()?>Menu/ArtworksHandlingDone">✅ Artwork Approval Done</a></li>
                                <li><a href="<?=base_url()?>Menu/ProgramDetail">📦 Total Handover</a></li>
                                <!-- <li><a href="javascript:void(0)">➕ Get New Utilisation</a></li>
                                <li><a href="javascript:void(0)">📊 Get Report</a></li>
                                <li><a href="javascript:void(0)">📝 Total Request Approval</a></li> -->
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>
          
            <?php  if(in_array($user['type_id'],[3,13,24])){ ?> 
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🏆 On Boarded Client </h5>
                            <hr>
                           <ul>
                                <li><a href="<?=base_url()?>Menu/HandoverCompany">🤝 Handover to Operation</a></li>
                                <?php  if(in_array($user['type_id'],[13,24])): ?> 
                                  <li><a href="<?=base_url()?>Menu/HandBIND">🏗️ Handover to Installation</a></li>
                                <?php endif; ?>     
                                <li><a href="<?=base_url()?>Menu/artworkapr">🎨 Artwork Apr</a></li>
                                <li><a href="<?=base_url()?>Menu/ProgramDetail">📊 Total Handover</a></li>
                              
                                <li><a href="<?=base_url()?>Menu/HandoverDetail">📋 On Boarded Client Detail</a></li>
                            </ul>
                        </div>
                    </div>
                </div>



                <?php /*
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🆕 New Client Request</h5>
                            <hr>
                             <?php
                                $cont=0;$pass=0;$tass=0;$ini=0;$pend=0;$close=0;
                                    $bdallrequest=$this->Menu_model->bdrequest($uid);
                                    foreach($bdallrequest as $bdar){
                                        $cont = $cont + $bdar->cont;
                                        $pass = $pass + $bdar->pass;
                                        $ini = $ini + $bdar->ini;
                                        $pend = $pend + $bdar->pend;
                                        $close = $close + $bdar->close;
                                    }
                                ?>
                        <ul>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/1">📩 Total Request - <b><?=$cont;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/2">⏳ Pending Apr - <b><?=$pass;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/3">✅ Total Apr - <b><?=$cont-$pass;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/5">🕒 Total Request Pending - <b><?=$pend;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/6">🎯 Total Request Completed - <b><?=$close;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/CreateRequest">📝 Create BD Request</a></li>
                        </ul>
                        </div>
                    </div>
                </div>

                */ ?>
                


                <?php } ?>
                 <?php  if(in_array($user['type_id'],[1,2])){ ?> 
                    <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>📋 Vendor Approvel Request</h5>
                            <hr>
                           <ul>
                                <li><a href="<?=base_url()?>Menu/VendorRequestDetails">📋 Vendor Request</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                 <?php } ?> 
                 <?php  if(in_array($user['type_id'],[4,19,20,21,22,23])){ ?> 
                    <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🏆 On Boarded Client </h5>
                            <hr>
                           <ul>
                                <li><a href="<?=base_url()?>Menu/artworkaprinPST">🎨 Artwork Apr Pending</a></li>
                                <li><a href="<?=base_url()?>Menu/artworkaprinPST">🎨 Artwork Apr Done</a></li>
                                <li><a href="<?=base_url()?>Menu/ProgramDetailinPST">📊 Total Handover</a></li>
                            </ul>
                        </div>
                    </div>
                </div>



                <?php /*
                  <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🆕 New Team Client Request</h5>
                            <hr>
                             <?php
                               $ourTeamBDS = $this->Menu_model->get_userbyaaid($uid);
                               $cont_tr = 0; $pass_tr = 0; $tass_tr = 0; $ini_tr = 0; $pend_tr = 0; $close_tr = 0;
                               
                                foreach ($ourTeamBDS as $ourTeamBD){
                                $team_uid           = $ourTeamBD->user_id;
                                $bdallrequests = $this->Menu_model->bdrequestinPSTTEAM($team_uid);
                                                        
                                foreach($bdallrequests as $team_bdar){
                                $cont_tr   = $cont_tr + $team_bdar->cont;
                                $pass_tr   = $pass_tr + $team_bdar->pass;
                                $ini_tr    = $ini_tr + $team_bdar->ini;
                                $pend_tr   = $pend_tr + $team_bdar->pend;
                                $close_tr  = $close_tr + $team_bdar->close;
                                }
                                }
                                ?>
                        <ul>
                            <li><a href="<?=base_url()?>Menu/TBDRequest/1">📩 Total Request - <b><?=$cont_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalRequest/2">⏳ Pending Apr - <b><?=$pass_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/3">✅ Total Apr - <b><?=$cont_tr-$pass_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequestinPST/5">🕒 Total Request Pending - <b><?=$pend_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequestinPST/6">🎯 Total Request Completed - <b><?=$close_tr;?></b></a></li>
                            <!-- <li><a href="<?=base_url()?>Menu/CreateRequest">📝 Create BD Request</a></li> -->
                        </ul>
                        </div>
                    </div>
                </div>
            
                */ ?>

             <?php  } ?> 


               <?php 
                $cmonth_number = date("n"); // 1 to 12
               if(in_array($user['type_id'],[1])){
                 ?> 
                <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>⟲ Funnel Reset Management</h5>
                            <hr>
                           <ul>
                                <li>
                                    <a href="<?=base_url()?>MasterReset/">⟲ Reset (Funnel)</a> <br>
                                </li>
                            </ul>
                            <p>
                                <small class="text-danger">
                                    <strong>Warning!</strong> This action will permanently reset all funnel data in <strong>Open / Open RPEM</strong> along with related configurations for the selected zone / BD. This process is <strong>irreversible</strong>. Please ensure that a complete backup has been taken before proceeding. This page is accessible only during the month of April.
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <?php } ?>




            <?php  if(in_array($user['type_id'],[15])){ ?> 

                

                <?php /*
                 <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🆕 New Team Client Request</h5>
                            <hr>
                             <?php
                               $ourTeamBDS = $this->Menu_model->get_userbyaSCid($uid);
                               $cont_tr = 0; $pass_tr = 0; $tass_tr = 0; $ini_tr = 0; $pend_tr = 0; $close_tr = 0;
                               
                                foreach ($ourTeamBDS as $ourTeamBD){
                                $team_uid           = $ourTeamBD->user_id;
                                $bdallrequests = $this->Menu_model->bdrequest($team_uid);
                                                        
                                foreach($bdallrequests as $team_bdar){
                                $cont_tr   = $cont_tr + $team_bdar->cont;
                                $pass_tr   = $pass_tr + $team_bdar->pass;
                                $ini_tr    = $ini_tr + $team_bdar->ini;
                                $pend_tr   = $pend_tr + $team_bdar->pend;
                                $close_tr  = $close_tr + $team_bdar->close;
                                }
                                }
                                ?>
                        <ul>
                            <li><a href="<?=base_url()?>Menu/TBDRequest/1">📩 Total Request - <b><?=$cont_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalRequest/2">⏳ Pending Apr - <b><?=$pass_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/3">✅ Total Apr - <b><?=$cont_tr-$pass_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequestinPST/5">🕒 Total Request Pending - <b><?=$pend_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequestinPST/6">🎯 Total Request Completed - <b><?=$close_tr;?></b></a></li>
                        </ul>
                        </div>
                    </div>
                </div>

                */ ?>
            <?php  } ?> 
            <?php  if(in_array($user['type_id'],[1,2])){ ?> 


                <?php /*
                 <div class="col">
                    <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                        <div>
                            <h5>🆕 New Team Client Request</h5>
                            <hr>
                             <?php
                               $ourTeamBDS = $this->Menu_model->get_userbyaid($uid);
                               $cont_tr = 0; $pass_tr = 0; $tass_tr = 0; $ini_tr = 0; $pend_tr = 0; $close_tr = 0;
                               
                                foreach ($ourTeamBDS as $ourTeamBD){
                                $team_uid           = $ourTeamBD->user_id;
                                $bdallrequests = $this->Menu_model->bdrequest($team_uid);
                                                        
                                foreach($bdallrequests as $team_bdar){
                                $cont_tr   = $cont_tr + $team_bdar->cont;
                                $pass_tr   = $pass_tr + $team_bdar->pass;
                                $ini_tr    = $ini_tr + $team_bdar->ini;
                                $pend_tr   = $pend_tr + $team_bdar->pend;
                                $close_tr  = $close_tr + $team_bdar->close;
                                }
                                }
                                ?>
                        <ul>
                            <li><a href="<?=base_url()?>Menu/TBDRequest/1">📩 Total Request - <b><?=$cont_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalRequest/2">⏳ Pending Apr - <b><?=$pass_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/3">✅ Total Apr - <b><?=$cont_tr-$pass_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/5">🕒 Total Request Pending - <b><?=$pend_tr;?></b></a></li>
                            <li><a href="<?=base_url()?>Menu/TotalBDRequest/6">🎯 Total Request Completed - <b><?=$close_tr;?></b></a></li>
                            <li>
                                <a href="<?=base_url()?>Menu/CreateRequest">
                                    ✨ <b> Create New BD Request</b>
                                </a>
                            </li>
                        </ul>
                        </div>
                    </div>
                </div>
                */ ?>

            <?php  } ?> 
            </div>
             <hr>
          </div>


          <?php }else if(in_array($user['type_id'],[16])){ ?> 
            
             <hr>
            <h3 class="sectionTitle ">
              📊 <span class="themeText gradient-animated-text">Data Analysis </span> 🔍
            </h3>
            <p class="sectionContent">
              Data Analysis is the process of inspecting, cleaning, transforming, and modeling data to discover meaningful patterns, trends, and insights. 📈 It helps in making informed decisions by turning raw data into actionable knowledge. 💡
            </p>
          <hr>
            <div class="container-data">
                <div class="row">
                       <div class="col">
                            <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                                <div>
                                    <h5>🗂️ Task Report</h5>
                                    <hr>
                                    <ul>
                                        <li><a href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTask">📝 Task Details</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                      <div class="col">
                        <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                            <div>
                                <h5>🗓️ Planner Report</h5>
                                <hr>
                                <ul>
                                    <li><a href="<?=base_url();?>Menu/GetAllPlannerLogPlannedByUsers">⚠️ Todays Replanned Task </a></li>
                                    <li><a href="<?=base_url()?>/Reports/TeamPlannerReport">🗓️ Team Planner Reports</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>


                    <!-- <div class="col">
                        <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                            <div>
                                <h5>🏫 School Detail</h5><hr>
                                <ul>
                                    <li>
                                        <a href="<?=base_url()?>Reports/SchoolDetails">
                                            📋 School Details
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div> -->


                    <div class="col">
                        <div class="col text-center mainBlock cat8 shadow p-3 mb-3 bg-white rounded">
                            <div>
                                    <h6>👥 Our Travel Advance</h6>
                                    <hr>
                                    <ul>
                                        <li>💵 <a href="<?=base_url()?>Menu/OurTravelAdvanceRequest"> Our Cash Advance Request</a></li>
                                        <li>📝 <a href="<?=base_url()?>Menu/OurCashExpenseReport">Our Cash Expense Request</a></li>
                                    </ul> 
                                    <!-- <hr>
                                    <h6>👥 ADD Meetings Expense</h6>
                                    <hr>
                                    <ul>
                                        <li>📝 <a href="<?=base_url()?>Menu/UpdateTodaysMeetingsDetails">Update Meetings Expense Details</a></li>
                                    </ul>  -->
                            </div>
                        </div>
                    </div>




                </div>
            </div>
            
            <?php } ?>

        </div>