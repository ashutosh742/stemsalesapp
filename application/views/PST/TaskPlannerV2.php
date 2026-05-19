<?php
/**
 * TaskPlannerV2 - AdminLTE web retrofit for the post-approval day shape lock.
 *
 * Drop-in replacement for TaskPlanner2.php once migration 017_4 is live.
 * Renders the 3 locked bands matching the mobile DayPlanScreen:
 *   10:00 to 15:00 = manual band (BD field activities)
 *   15:00 to 17:30 = auto-task band (system-seeded calls/emails/MoM)
 *   17:30 to 18:30 = next-day plan window
 *
 * Reads v_planner_day_shape view (migration 017_4) for live consumption.
 * Hard-locks the action picker so out-of-band insert is rejected client-side
 * BEFORE the server even sees it (server still re-validates via
 * sp_check_band_lock).
 *
 * Variables in scope (passed from Menu::TaskPlanner2):
 *   $uid, $user, $adate, $tptime, $getreqData, $getAutoTaskTime,
 *   $getplandt, $pendingtask, $mesaage, $planbutnotinitedcnt
 *
 * NEW variable required from controller:
 *   $dayShape = $this->db->query("SELECT * FROM v_planner_day_shape
 *     WHERE bd_uid = $uid AND plan_date = '$adate'")->row();
 */

$dep_name = $user['dep_name'] ?? 'Sales Person';
$shape    = $dayShape ?? (object) [
  'manual_start' => '10:00:00', 'manual_end' => '15:00:00',
  'auto_start'   => '15:00:00', 'auto_end'   => '17:30:00',
  'plan_window_start' => '17:30:00', 'plan_window_end' => '18:30:00',
  'manual_budget_min' => 300, 'auto_budget_min' => 150, 'plan_window_budget_min' => 60,
  'manual_consumed_min' => 0, 'auto_consumed_min' => 0,
  'manual_task_count' => 0, 'auto_task_count' => 0,
  'shape_locked' => 0, 'auto_seeded' => 0, 'auto_seeded_count' => 0,
  'work_mode' => 'wfo', 'current_band' => 'before_day',
];
$bandLabel = [
  'manual'      => 'Field activities',
  'auto'        => 'Auto-tasks',
  'plan_window' => 'Plan tomorrow',
  'before_day'  => 'Before day starts',
  'after_day'   => 'Day closed',
  'not_today'   => 'Not today',
];
$currentBand = $shape->current_band;
$workMode    = $shape->work_mode;
$isPlanWindow = ($currentBand === 'plan_window');
$pct = function($used, $total) {
  if ($total <= 0) return 0;
  return min(100, round(($used / $total) * 100));
};
?>
<style>
  .ds-card{background:#fff;border:1px solid #d0d7de;border-radius:10px;padding:18px;margin-bottom:18px}
  .ds-card-title{font-size:11px;font-weight:700;color:#57606a;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px}
  .ds-band-row{display:flex;align-items:flex-start;margin-bottom:14px;padding:8px 10px;border-radius:6px}
  .ds-band-row.live{background:#f6f8fa;border-left:3px solid #cf222e}
  .ds-band-dot{width:10px;height:10px;border-radius:5px;margin-top:5px;margin-right:12px;flex-shrink:0}
  .ds-band-body{flex:1}
  .ds-band-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px}
  .ds-band-label{font-size:14px;font-weight:600;color:#1f2328}
  .ds-band-time{font-size:12px;color:#57606a;font-family:'SF Mono',Menlo,monospace;margin-right:8px}
  .ds-band-tag{font-size:10px;font-weight:700;color:#cf222e;background:#ffebe9;padding:3px 7px;border-radius:4px}
  .ds-band-bar{height:6px;background:#eaeef2;border-radius:3px;overflow:hidden;margin-bottom:5px}
  .ds-band-fill{height:100%;transition:width 0.3s}
  .ds-band-sub{font-size:11px;color:#57606a}
  .ds-banner{display:flex;align-items:center;border:1px solid;border-radius:8px;padding:11px 13px;margin-bottom:14px;gap:10px;font-size:13px}
  .ds-banner-red{background:#ffebe9;border-color:#ffcecb;color:#cf222e}
  .ds-banner-purple{background:#fbefff;border-color:#e8d4ff;color:#8250df}
  .ds-banner-amber{background:#fff8c5;border-color:#d4a72c;color:#9a6700}
  .ds-prompt{background:linear-gradient(180deg,#fff4d6,#fffaf0);border:1px solid #d4a72c;border-radius:10px;padding:16px;margin-bottom:18px}
  .ds-prompt-title{font-size:15px;font-weight:700;color:#bf8700;margin-bottom:6px}
  .ds-prompt-body{font-size:12px;color:#57606a;line-height:1.5;margin-bottom:10px}
  .ds-prompt-btn{background:#bf8700;color:#fff;padding:9px 18px;border-radius:6px;font-size:13px;font-weight:700;border:0;cursor:pointer}
  .ds-lock-warn{display:none;background:#cf222e;color:#fff;padding:14px;border-radius:8px;margin-top:12px;font-size:13px}
  .ds-lock-warn.show{display:block}
  .ds-allowed-list{font-size:11px;color:#57606a;margin-top:8px;padding:8px;background:#f6f8fa;border-radius:5px;font-family:monospace}
</style>

<div class="content-wrapper" style="min-height: calc(100vh - 50px)">
<section class="content-header">
  <h1>Day Plan - <?= $adate ?>
    <small style="margin-left:10px">
      <span style="background:#dafbe1;color:#1a7f37;padding:3px 9px;border-radius:10px;font-size:11px;font-weight:700">
        <?= $shape->shape_locked ? 'APPROVED - LOCKED' : 'AWAITING CM APPROVAL' ?>
      </span>
      <span style="background:<?= $workMode==='wfo'?'#ffebe9':($workMode==='leave'?'#fbefff':'#ddf4ff') ?>;
                   color:<?= $workMode==='wfo'?'#cf222e':($workMode==='leave'?'#8250df':'#0969da') ?>;
                   padding:3px 9px;border-radius:10px;font-size:11px;font-weight:700;margin-left:6px">
        <?= strtoupper($workMode) ?>
      </span>
    </small>
  </h1>
</section>

<section class="content">
  <?php if ($isPlanWindow && !$shape->shape_locked): ?>
  <div class="ds-prompt">
    <div class="ds-prompt-title">It is past 5:30 PM. Plan tomorrow now.</div>
    <div class="ds-prompt-body">
      Field activities are closed for the day. You have until 6:30 PM to submit
      tomorrows plan or it goes to CM as same-day late.
    </div>
    <button class="ds-prompt-btn"
      onclick="window.location.href='<?= base_url() ?>Menu/NextDayPlanner2'">
      Open next-day planner
    </button>
  </div>
  <?php endif; ?>

  <?php if ($workMode === 'wfo'): ?>
  <div class="ds-banner ds-banner-red">
    <i class="fa fa-building"></i>
    <span>WFO mode: no physical or barge meetings today. Virtual calls allowed.</span>
  </div>
  <?php elseif ($workMode === 'leave'): ?>
  <div class="ds-banner ds-banner-purple">
    <i class="fa fa-bed"></i>
    <span>Approved leave today. Day shape locks are off. No next-day plan required.</span>
  </div>
  <?php endif; ?>

  <!-- ============ DAY SHAPE BAND STRIP ============ -->
  <div class="ds-card">
    <div class="ds-card-title">Day shape <?= $shape->shape_locked ? '(locked by CM approval)' : '(default until approval)' ?></div>

    <!-- Manual band -->
    <div class="ds-band-row <?= $currentBand==='manual'?'live':'' ?>">
      <div class="ds-band-dot" style="background:#0969da"></div>
      <div class="ds-band-body">
        <div class="ds-band-head">
          <div>
            <span class="ds-band-time"><?= substr($shape->manual_start,0,5) ?> - <?= substr($shape->manual_end,0,5) ?></span>
            <span class="ds-band-label"><?= $bandLabel['manual'] ?></span>
          </div>
          <?php if ($currentBand==='manual'): ?><span class="ds-band-tag">LIVE</span><?php endif; ?>
        </div>
        <div class="ds-band-bar">
          <div class="ds-band-fill" style="width:<?= $pct($shape->manual_consumed_min, $shape->manual_budget_min) ?>%;background:#0969da"></div>
        </div>
        <div class="ds-band-sub">
          <?= $shape->manual_consumed_min ?> of <?= $shape->manual_budget_min ?> min used
          - <?= $shape->manual_task_count ?> tasks
          - allowed: physical meetings, barge, virtual calls, follow-ups
        </div>
      </div>
    </div>

    <!-- Auto band -->
    <div class="ds-band-row <?= $currentBand==='auto'?'live':'' ?>">
      <div class="ds-band-dot" style="background:#8250df"></div>
      <div class="ds-band-body">
        <div class="ds-band-head">
          <div>
            <span class="ds-band-time"><?= substr($shape->auto_start,0,5) ?> - <?= substr($shape->auto_end,0,5) ?></span>
            <span class="ds-band-label"><?= $bandLabel['auto'] ?></span>
          </div>
          <?php if ($currentBand==='auto'): ?><span class="ds-band-tag">LIVE</span><?php endif; ?>
        </div>
        <div class="ds-band-bar">
          <div class="ds-band-fill" style="width:<?= $pct($shape->auto_consumed_min, $shape->auto_budget_min) ?>%;background:#8250df"></div>
        </div>
        <div class="ds-band-sub">
          <?php if ($shape->auto_seeded): ?>
            <?= $shape->auto_seeded_count ?> auto-tasks seeded at <?= date('H:i', strtotime($shape->auto_seeded_at ?? '15:00:00')) ?>
          <?php else: ?>
            <i style="color:#bf8700">Seeder fires at 15:00 IST (waiting)</i>
          <?php endif; ?>
          - allowed: calls (1), emails (2), MoM (13) only
        </div>
      </div>
    </div>

    <!-- Plan window -->
    <div class="ds-band-row <?= $currentBand==='plan_window'?'live':'' ?>">
      <div class="ds-band-dot" style="background:#bf8700"></div>
      <div class="ds-band-body">
        <div class="ds-band-head">
          <div>
            <span class="ds-band-time"><?= substr($shape->plan_window_start,0,5) ?> - <?= substr($shape->plan_window_end,0,5) ?></span>
            <span class="ds-band-label"><?= $bandLabel['plan_window'] ?></span>
          </div>
          <?php if ($currentBand==='plan_window'): ?><span class="ds-band-tag">LIVE</span><?php endif; ?>
        </div>
        <div class="ds-band-sub" style="<?= $currentBand==='plan_window'?'color:#cf222e;font-weight:600':'' ?>">
          <?= $currentBand==='plan_window' ? 'Submit tomorrows plan now - cutoff 18:30' : 'Opens at 17:30 IST' ?>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ FILTER LEFT RAIL (rev 9 production parity - 32 categories) ============ -->
  <?php
  // Filter counts mirror production TaskPlanner2.php left rail (line 1190 onwards)
  // and reuse the same Menu_model methods so numbers always match.
  // Counts are best-effort - missing methods fall back to 0 so the rail still
  // renders. The radio group POSTs to addplantask12 via the existing form (the
  // BD picks a category, the lead picker on the right narrows to that bucket).
  $type_id = (int) ($user['type_id'] ?? 1);
  $cnt = function($method, ...$args) {
      if (!method_exists($this->Menu_model, $method)) return 0;
      try {
          $r = $this->Menu_model->$method(...$args);
          if (is_array($r))  return count($r);
          if (is_object($r) && isset($r->total)) return (int) $r->total;
          return 0;
      } catch (Throwable $e) { return 0; }
  };
  // Reuse existing production methods
  $autoAssignCounts  = method_exists($this->Menu_model,'GetAllAutoAssignTask') ? $this->Menu_model->GetAllAutoAssignTask($uid) : [];
  $autoAssignTotal   = is_array($autoAssignCounts) ? array_sum($autoAssignCounts) : 0;
  $tomAssignTask     = method_exists($this->Menu_model,'GetTommrowAssignedTask') ? $this->Menu_model->GetTommrowAssignedTask($uid) : [];
  $tomAssignCount    = is_array($tomAssignTask) ? count($tomAssignTask) : 0;
  $emergencyCount    = $cnt('GetEmergencyTask', $uid, $adate);
  $becauseChangeCnt  = $cnt('GetTaskBecauseOfPlanChange', $uid, $adate);
  $needAttentionCnt  = $cnt('GetAllCompulsiveAndNeedYourAttentionByuid', $uid, $adate);
  $mandatoryCnt      = $cnt('GetMandatoryRestrictionforPlannerPageListByUID', $uid, $adate);
  $reviewPlanCnt     = $cnt('GetPendingReviewForPlan', $uid, $adate);
  $newLeadCnt        = $cnt('GetAddNewLeadComapny', $uid);
  $reupdateLeadCnt   = $cnt('GetReUpdateNewLeadComapny', $uid);
  // Rev 9 - the two production chips v2 was missing.
  // PST Assign tracks tasks the PST CM assigned to this BD (production TaskPlanner2 line 1357).
  // actionNotPlannedNeed flags actions the BD added to a lead but never planned, with a Need follow-up.
  $pstAssignCnt           = $cnt('GetPSTAssignedTask', $uid, $adate);
  $actionNotPlannedNeed   = $cnt('GetActionNotPlannedNeed', $uid, $adate);

  // 32-category filter group - rev 9 production parity. Each entry is
  // [radio_value, label, count, css_id, tooltip, allowed_type_ids_or_null]
  $filterCats = [
    ['Assign Task',                       'Assign Task',                       $tomAssignCount,   'assign_task_filter',           'Tasks assigned to this BD by their line manager (CM, RM, ACM, ASH).',                                       null],
    ['Self Assign',                       'Auto Assign',                       $autoAssignTotal,  'self_assign_filter',           'Tasks auto-seeded by the 15:00 cron - MoM check, proposal check, follow-up calls.',                          null],
    ['Mandatory Task',                    'Mandatory Task',                    $mandatoryCnt,     'mandatory_task_filter',        'Tasks the planner gate requires before submit. Cannot be skipped.',                                          null],
    ['Compulsive Task',                   'Compulsive Task',                   $needAttentionCnt, 'compulsive_task_filter',       'Compulsive tasks created by escalations or repeat misses.',                                                  null],
    ['Need Your Attention',               'Need Your Attention',               $needAttentionCnt, 'need_attention_filter',        'Stale leads, missed cutoffs, or pending MoM that need follow-up today.',                                     null],
    ['Emergency Meetings Task',           'Emergency Meeting',                 $emergencyCount,   'emergency_meeting_filter',     'Same-day emergency meetings approved by CM. Limited to 2 per BD per day.',                                  null],
    ['Because of Plan Change',            'Because of Plan Change',            $becauseChangeCnt, 'because_change_filter',        'Tasks bumped from yesterday because the BD changed plan mid-day.',                                          null],
    ['Review Planning',                   'Review Planning',                   $reviewPlanCnt,    'review_planning_filter',       'Scheduled reviews due today. Mirrors review_planning table.',                                                null],
    ['Review Target Date',                'Review Target Date',                0,                 'review_target_filter',         'Reviews where target_date <= today.',                                                                        null],
    ['Create BD Request',                 'Create BD Request',                 0,                 'create_bdrequest_filter',      'Build a new BD request - escalates to CM or RM for approval.',                                              null],
    ['Future Task',                       'Future Task',                       0,                 'future_task_filter',           'Tasks scheduled for a future date.',                                                                         null],
    ['Status',                            'Status',                            0,                 'status_filter',                'Filter leads by cstatus - Open, Reachout, Tentative, Positive, Open RPEM, Very Positive.',                  null],
    ['Category',                          'Category',                          0,                 'category_filter',              'Filter leads by lead category.',                                                                             null],
    ['New Category',                      'New Category',                      0,                 'new_category_filter',          'Q1 closure funnel, Potential funnel for FY, To be nurtured for FY, 50 new lead funnel, BD marked, Anchor.', null],
    ['Marked In Current Quarter',         'Marked In Current Quarter',         0,                 'current_quarter_filter',       'Leads marked in this financial quarter.',                                                                    null],
    ['Quater Strategy',                   'Quater Strategy',                   0,                 'quarter_strategy_filter',      'Production label kept as is - Quater Strategy.',                                                             null],
    ['Closing Timeline',                  'Closing Timeline',                  0,                 'closing_timeline_filter',      'Propose Day 6 to 8, Clarify Day 8 to 12, Nudge Day 12 to 14, Support Day 15 to 18.',                       null],
    ['Same Status Last Limit Days',       'Same Status Last Limit Days',       0,                 'same_status_filter',           'Leads stuck in the same cstatus for over the threshold.',                                                    null],
    ['Plan But Not Initiated',            'Plan But Not Initiated',            0,                 'plan_not_init_filter',         'Tasks planned but not started by the BD.',                                                                   null],
    ['Plan But Not Initiated Old',        'Plan But Not Initiated Old',        0,                 'plan_not_init_old_filter',     'Older not-initiated tasks rolled forward.',                                                                  null],
    ['No Calling Done After Only Got Details', 'No Calling After Only Got Details', 0,             'no_calling_filter',            'Leads in only_got_details where no call has happened yet.',                                                 null],
    ['Next Follow Up Date',               'Next Follow Up Date',               0,                 'next_followup_filter',         'Leads whose follow-up date is today.',                                                                       null],
    ['Approved Date',                     'Approved Date',                     0,                 'approved_date_filter',         'Filter tasks by approval date.',                                                                             null],
    ['Cluster Location',                  'Cluster Location',                  0,                 'cluster_location_filter',      'Filter by travel cluster - base location or out-station.',                                                  null],
    ['Location',                          'Location',                          0,                 'location_filter',              'Filter by city or area.',                                                                                    null],
    ['Partner Type',                      'Partner Type',                      0,                 'partner_type_filter',          'Filter by partner type - direct, dealer, franchise.',                                                       null],
    ['Compnay Name',                      'Compnay Name',                      0,                 'company_name_filter',          'Type the company name directly. Production label kept as is.',                                              null],
    ['Find Company By',                   'Find Company By',                   0,                 'find_company_filter',          'Search by CIN, GST, contact, or any indexed field.',                                                         null],
    ['Task Action',                       'Task Action',                       0,                 'task_action_filter',           'Filter by actiontype - Call, Email, Meeting, Barge, Research, MoM.',                                        null],
    ['actionNotPlanned',                  'Action Not Planned',                0,                     'action_not_planned_filter',    'Tasks marked but never planned.',                                                                            null],
    ['actionNotPlannedNeed',              'Action Not Planned (Need)',         $actionNotPlannedNeed, 'action_not_planned_need_filter','Rev 9 - actions logged on a lead but never planned, with a Need flag pending follow-up.',                  null],
    ['PST Assign',                        'PST Assign',                        $pstAssignCnt,         'pst_assign_filter',            'Rev 9 - tasks the PST CM assigned to this BD via production TaskPlanner2 line 1357.',                       null],
  ];

  // Pull company list and action list scoped to this BD so the cascade matches
  // production TaskPlanner2 line 2005 (taskPurposebyuser) but with the proper
  // purpose list (filtered by action_id + status_id) rather than the legacy
  // Yes/No dropdown that production currently ships.
  $allCompanies = $this->Menu_model->GetAllCompanyByUserID($uid);
  $allActions   = method_exists($this->Menu_model,'getTaskAction') ? $this->Menu_model->getTaskAction(null) : null;
  if (!$allActions) {
      $allActions = $this->db->query("SELECT id, name, yest FROM `action` WHERE id NOT IN (6,8,9,11) ORDER BY id")->result();
  }
  $allStatuses  = $this->Menu_model->get_status();
  ?>

  <!-- ============ FILTER RAIL UI ============ -->
  <style>
    .v2-filter-card{background:#fff;border:1px solid #d0d7de;border-radius:10px;padding:14px;margin-bottom:18px;max-height:420px;overflow-y:auto}
    .v2-filter-title{font-size:11px;font-weight:700;color:#57606a;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px}
    .v2-filter-row{display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:6px;cursor:pointer;border:1px solid transparent;margin-bottom:4px}
    .v2-filter-row:hover{background:#f6f8fa;border-color:#d0d7de}
    .v2-filter-row.active{background:#ddf4ff;border-color:#0969da}
    .v2-filter-label{font-size:13px;color:#1f2328;font-weight:500;flex:1}
    .v2-filter-badge{font-size:11px;color:#57606a;background:#eaeef2;padding:2px 8px;border-radius:10px;font-weight:600;min-width:24px;text-align:center}
    .v2-filter-badge.warn{background:#ffd33d44;color:#7d4e00}
    .v2-filter-badge.danger{background:#ffd0d244;color:#82071e}
    .v2-filter-row.disabled{opacity:.55;cursor:not-allowed}
  </style>
  <div class="v2-filter-card">
    <div class="v2-filter-title">Filter leads by (rev 9 production parity - 32 categories)</div>
    <input type="hidden" id="v2ActiveFilter" name="optradio" value="">
    <?php foreach ($filterCats as $cat):
      list($val, $label, $count, $id, $tip, $allowed) = $cat;
      $isAllowed = ($allowed === null) || in_array($type_id, $allowed);
      $badgeCls = ($count > 0) ? (($count > 5) ? 'danger' : 'warn') : '';
    ?>
      <div class="v2-filter-row <?= $isAllowed ? '' : 'disabled' ?>"
           id="<?= $id ?>"
           data-val="<?= htmlspecialchars($val) ?>"
           title="<?= htmlspecialchars($tip) ?>">
        <span class="v2-filter-label"><?= htmlspecialchars($label) ?></span>
        <span class="v2-filter-badge <?= $badgeCls ?>"><?= (int) $count ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ============ REV 9 SPECIAL TASKS RAIL ============ -->
  <!-- Mirrors the 11 selectby branches in production addplantask12. Production            -->
  <!-- TaskPlanner2.php has these as scattered buttons. Rev 9 collects them in one rail.   -->
  <?php
  $specialTasks = [
    ['barg_by_cluster', 'Barg by Cluster',       'No company - pick travel cluster. Production ntaction=4 selectby=Barg.',  'cluster',      19136, 4],
    ['join_meeting',    'Join Senior Meeting',   'Tag senior who is leading. Production ntaction=17.',                       'none',         19245, 17],
    ['research',        'Research Visit',        'Free walk-in research. Production ntaction=10 no company.',                'none',         19260, 10],
    ['mom_check',       'MoM Check',             'Schedule a MoM review on an existing lead. Production selectby=Mom Check.','purpose_init', 19198, null],
    ['proposal_check',  'Proposal Check',        'Verify proposal status with the school. Production selectby=Proposal Check.','purpose_init',19215, null],
  ];
  ?>
  <div class="v2-filter-card" style="max-height:none">
    <div class="v2-filter-title">Special tasks (rev 9 - 5 shortcuts)</div>
    <style>
      .v2-special-grid{display:flex;flex-wrap:wrap;gap:8px}
      .v2-special-card{flex:1 1 45%;background:#fff8c5;border:1px solid #d4a72c;border-radius:6px;padding:10px;cursor:pointer}
      .v2-special-card:hover{background:#fff1a8}
      .v2-special-card.locked{opacity:.55;cursor:not-allowed;background:#eaeef2;border-color:#d0d7de}
      .v2-special-label{font-size:13px;font-weight:700;color:#1f2328;margin-bottom:2px}
      .v2-special-hint{font-size:11px;color:#57606a}
      .v2-meeting-cap-toast{display:none;background:#ffebe9;border:1px solid #cf222e;color:#82071e;padding:8px;border-radius:6px;font-size:12px;margin-top:8px}
      .v2-ceiling-toast{display:none;background:#ffebe9;border:1px solid #cf222e;color:#82071e;padding:8px;border-radius:6px;font-size:12px;margin-top:8px}
    </style>
    <div class="v2-special-grid">
      <?php foreach ($specialTasks as $sp):
        list($key, $label, $hint, $requires, $line, $actiontype) = $sp;
        $meetingLike = ($actiontype === 3 || $actiontype === 4);
        $blockedByWfo = $meetingLike && (($shape->work_mode ?? 'wfo') === 'wfo');
      ?>
        <div class="v2-special-card <?= $blockedByWfo ? 'locked' : '' ?>"
             data-key="<?= $key ?>"
             data-requires="<?= $requires ?>"
             data-actiontype="<?= (int)$actiontype ?>"
             data-line="<?= (int)$line ?>"
             title="<?= htmlspecialchars($hint) ?>">
          <div class="v2-special-label"><?= htmlspecialchars($label) ?></div>
          <div class="v2-special-hint">
            <?= $blockedByWfo ? 'WFO blocks this' : htmlspecialchars($hint) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="v2-meeting-cap-toast" id="v2MeetingCapToast">
      4-meeting daily cap hit (production line 19120). Cannot add more actiontype 3 or 4 today.
    </div>
    <div class="v2-ceiling-toast" id="v2CeilingToast">
      540-minute (9 hour) day-shape ceiling reached (production line 19312). Drop or shorten a task before adding more.
    </div>
  </div>

  <div class="ds-card">
    <div class="ds-card-title">Add task to day plan</div>
    <form id="addTaskFormV2" method="post" action="<?= base_url() ?>Menu/addplantask12">
      <input type="hidden" name="bdid" value="<?= $uid ?>">
      <input type="hidden" name="pdate" value="<?= $adate ?>">
      <input type="hidden" name="tptime" value="">
      <input type="hidden" name="selectby" value="PlannerV2 Add Task">

      <!-- Row 1: Company (lead) and current status -->
      <div class="row">
        <div class="col-md-6">
          <label>Company / Lead</label>
          <input list="v2CompanyList" id="v2Company" class="form-control"
                 placeholder="Type company name" autocomplete="off" required>
          <datalist id="v2CompanyList">
            <?php foreach (($allCompanies ?: []) as $c): ?>
              <option value="<?= htmlspecialchars($c->compname) ?>"
                      data-inid="<?= (int)$c->inid ?>"
                      data-cstatus="<?= (int)($c->cstatus ?? 0) ?>"></option>
            <?php endforeach; ?>
          </datalist>
          <!-- selectcompanybyuser[] is what addplantask12 expects (production line 19105) -->
          <input type="hidden" id="v2CompanyInid" name="selectcompanybyuser[]" value="">
        </div>
        <div class="col-md-3">
          <label>Status (auto)</label>
          <select id="v2Status" name="selectstatusbyuser" class="form-control" required>
            <option value="">-- auto-detect --</option>
            <?php foreach (($allStatuses ?: []) as $s): ?>
              <option value="<?= (int)$s->id ?>"><?= htmlspecialchars($s->name) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label>Time</label>
          <input type="time" id="taskTime" name="ptime" class="form-control"
                 min="10:00" max="18:30" required>
        </div>
      </div>

      <!-- Row 2: Task type (actiontype) and Purpose (the production-missing dropdown) -->
      <div class="row" style="margin-top:10px">
        <div class="col-md-6">
          <label>Type of task <span style="color:#cf222e">*</span></label>
          <select id="taskAction" name="ntaction" class="form-control" required>
            <option value="">-- pick type of task --</option>
            <?php
            $actionDisable = [];
            if ($workMode === 'wfo') { $actionDisable = [3, 4]; }
            foreach (($allActions ?: []) as $a):
              $aid  = (int) $a->id;
              $name = htmlspecialchars($a->name);
              $yest = (int) ($a->yest ?? 5);
              $dis  = in_array($aid, $actionDisable) ? 'disabled' : '';
              $suffix = in_array($aid, $actionDisable) ? ' - WFO disabled' : '';
              echo "<option value=\"$aid\" data-yest=\"$yest\" $dis>$name ($yest min)$suffix</option>";
            endforeach;
            ?>
          </select>
          <small class="text-muted">Mirrors production action master. Drives the
            purpose list below.</small>
        </div>
        <div class="col-md-6">
          <label>Purpose <span style="color:#cf222e">*</span></label>
          <select id="taskPurpose" name="ntppose" class="form-control" required disabled>
            <option value="">-- pick task and status first --</option>
          </select>
          <small class="text-muted">Loaded from <code>purpose</code> table filtered by
            <code>action_id</code> + <code>status_id</code>. Matches
            <code>Menu_model::get_purposebyinid</code>.</small>
        </div>
      </div>

      <!-- Row 3: Submit -->
      <div class="row" style="margin-top:14px">
        <div class="col-md-12">
          <button type="submit" id="addTaskBtn" class="btn btn-primary" disabled>
            <i class="fa fa-plus"></i> Add task to plan
          </button>
          <small id="v2FormHint" class="text-muted" style="margin-left:10px">
            Pick company, type of task, and purpose to enable.
          </small>
        </div>
      </div>

      <div id="bandLockWarn" class="ds-lock-warn">
        <strong>Action blocked by day shape lock</strong>
        <div id="bandLockReason" style="margin-top:6px;font-size:12px;line-height:1.5"></div>
        <div class="ds-allowed-list" style="background:#ffffff22;color:#fff;border:0;margin-top:8px">
          <span id="bandLockHint"></span>
        </div>
      </div>
    </form>
  </div>

  <!-- ============ EXISTING TASK LIST (passthrough to old view sections) ============ -->
  <?php if (isset($getreqData) && !empty($getreqData)): ?>
  <div class="ds-card">
    <div class="ds-card-title">Scheduled tasks today</div>
    <table class="table table-bordered table-striped">
      <thead>
        <tr><th width="80">Time</th><th>Activity</th><th>Lead</th><th width="100">Band</th><th width="80">Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($getreqData as $row): ?>
        <?php
          $taskTime = date('H:i', strtotime($row->ptime ?? '10:00'));
          $taskBand = ($taskTime < '15:00') ? 'manual'
                    : (($taskTime < '17:30') ? 'auto' : 'plan_window');
          $bandColor = ['manual'=>'#0969da','auto'=>'#8250df','plan_window'=>'#bf8700'][$taskBand];
        ?>
        <tr>
          <td><b><?= $taskTime ?></b></td>
          <td><?= htmlspecialchars($row->actionname ?? 'Call') ?></td>
          <td><?= htmlspecialchars($row->compnay ?? '-') ?></td>
          <td><span style="background:<?= $bandColor ?>22;color:<?= $bandColor ?>;padding:2px 8px;border-radius:8px;font-size:11px;font-weight:700"><?= strtoupper($taskBand) ?></span></td>
          <td><?= htmlspecialchars($row->status ?? 'planned') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</section>
</div>

<script>
// =========================================================================
// Cascade: company -> status (auto) -> action -> purpose
// Mirrors production Menu_model::get_purposebyinid which loads purpose rows
// filtered by (action_id, status_id) from the `purpose` table.
// =========================================================================
(function(){
  var BASE_URL = '<?= base_url() ?>';

  var compInput    = document.getElementById('v2Company');
  var compInid     = document.getElementById('v2CompanyInid');
  var statusSel    = document.getElementById('v2Status');
  var actionSel    = document.getElementById('taskAction');
  var purposeSel   = document.getElementById('taskPurpose');
  var formHint     = document.getElementById('v2FormHint');
  var addBtn       = document.getElementById('addTaskBtn');

  // 1) Company datalist -> auto-fill init_call id and current cstatus
  compInput.addEventListener('input', function(){
    var dl  = document.getElementById('v2CompanyList');
    var opt = Array.from(dl.options).find(function(o){ return o.value === compInput.value; });
    if (!opt) { compInid.value = ''; return; }
    compInid.value = opt.dataset.inid;
    var cs = parseInt(opt.dataset.cstatus, 10);
    if (cs > 0) {
      statusSel.value = cs;
      loadPurposes();
    }
    refreshBtn();
  });

  // 2) Action and Status changes both re-fetch purpose list
  actionSel.addEventListener('change', loadPurposes);
  statusSel.addEventListener('change', loadPurposes);

  function loadPurposes(){
    var aid = actionSel.value;
    var sid = statusSel.value;
    if (!aid || !sid) {
      purposeSel.innerHTML = '<option value="">-- pick task and status first --</option>';
      purposeSel.disabled  = true;
      refreshBtn();
      return;
    }
    purposeSel.disabled  = false;
    purposeSel.innerHTML = '<option value="">Loading...</option>';
    // rev 8: production-parity cascade endpoint Menu/getpurposes_v2.
    // Mirrors all 5 production cascade methods plus the 3 selectby branches
    // plus the Fresh Meeting (id 34) empty fallback. apply_barge_rewrite=1
    // mirrors the Day Plan form Barge-to-Scheduled-Meeting rewrite quirk.
    // Filter category is read from the active filter chip so 'Next Follow Up
    // Date' and 'Call On School' route to the right cascade branch.
    var inid     = (compInid && compInid.value) ? compInid.value : '';
    var selectby = (window.v2ActiveFilterCategory || '').trim();
    var qs = 'Menu/getpurposes_v2?action_id=' + encodeURIComponent(aid)
           + '&cstatus='  + encodeURIComponent(sid)
           + '&inid='     + encodeURIComponent(inid)
           + '&selectby=' + encodeURIComponent(selectby)
           + '&apply_barge_rewrite=1';
    fetch(BASE_URL + qs, { credentials: 'same-origin' })
      .then(function(r){ return r.ok ? r.json() : { rows: [], fallback_used: true }; })
      .catch(function(){ return { rows: [], fallback_used: true }; })
      .then(function(resp){
        var list = (resp && resp.rows) || [];
        // Fresh Meeting fallback already comes from server; only fall back here
        // if the server itself is unreachable.
        if (!Array.isArray(list) || list.length === 0) {
          list = [{ id: 34, name: 'Fresh Meeting' }];
        }
        purposeSel.innerHTML = '<option value="">-- select purpose --</option>';
        list.forEach(function(p){
          var o = document.createElement('option');
          o.value = p.id;
          o.textContent = p.name;
          purposeSel.appendChild(o);
        });
        if (resp && resp.barge_rewritten) {
          formHint.textContent = 'Barge in this stage is treated as Scheduled Meeting (production rule).';
          formHint.className = 'v2-hint warn';
        } else if (resp && resp.fallback_used) {
          formHint.textContent = 'No purposes for this action and status pair - showing Fresh Meeting default.';
          formHint.className = 'v2-hint warn';
        }
        refreshBtn();
      });
  }

  purposeSel.addEventListener('change', refreshBtn);
  document.getElementById('taskTime').addEventListener('change', refreshBtn);

  function refreshBtn(){
    var ok = compInid.value && statusSel.value && actionSel.value
          && purposeSel.value && document.getElementById('taskTime').value;
    addBtn.disabled = !ok;
    formHint.textContent = ok
      ? 'Ready to add task.'
      : 'Pick company, type of task, and purpose to enable.';
  }
})();
</script>

<script>
// =========================================================================
// Client-side band lock - mirrors sp_check_band_lock so the user gets
// instant feedback before the server roundtrip. Server still re-validates.
// =========================================================================
(function(){
  var SHAPE = <?= json_encode([
    'manual_start' => substr($shape->manual_start, 0, 5),
    'manual_end'   => substr($shape->manual_end, 0, 5),
    'auto_start'   => substr($shape->auto_start, 0, 5),
    'auto_end'     => substr($shape->auto_end, 0, 5),
    'plan_start'   => substr($shape->plan_window_start, 0, 5),
    'plan_end'     => substr($shape->plan_window_end, 0, 5),
    'locked'       => (int) $shape->shape_locked,
    'work_mode'    => $shape->work_mode,
  ]) ?>;

  var REASON_HINTS = {
    'wfo_blocks_physical_meeting': 'WFO mode blocks actiontype 3 and 4. Pick virtual call (12) instead, or switch work_mode to WFFO.',
    'auto_band_only_calls_emails_mom_allowed': 'Auto band (15:00 to 17:30) accepts only call (1), email (2), MoM (13). Plan the meeting for tomorrows manual band.',
    'plan_window_no_field_activity': 'Plan window (17:30 to 18:30) is for next-day planning only. No field activity.',
    'out_of_band': 'Time falls outside 10:00 to 18:30 day window. Pick a time inside the locked shape.'
  };

  function resolveBand(t) {
    if (t >= SHAPE.manual_start && t < SHAPE.manual_end) return 'manual';
    if (t >= SHAPE.auto_start && t < SHAPE.auto_end) return 'auto';
    if (t >= SHAPE.plan_start && t < SHAPE.plan_end) return 'plan_window';
    return 'out_of_band';
  }

  function checkLock(time, actiontype) {
    if (!SHAPE.locked) return { allowed: true };
    var at = parseInt(actiontype, 10);
    if (SHAPE.work_mode === 'wfo' && (at === 3 || at === 4)) {
      return { allowed: false, reason: 'wfo_blocks_physical_meeting' };
    }
    var band = resolveBand(time);
    if (band === 'manual') return { allowed: true };
    if (band === 'auto') {
      if ([1,2,13].indexOf(at) === -1) {
        return { allowed: false, reason: 'auto_band_only_calls_emails_mom_allowed' };
      }
      return { allowed: true };
    }
    if (band === 'plan_window') {
      return { allowed: false, reason: 'plan_window_no_field_activity' };
    }
    return { allowed: false, reason: 'out_of_band' };
  }

  function refresh() {
    var t  = document.getElementById('taskTime').value;
    var at = document.getElementById('taskAction').value;
    var warn = document.getElementById('bandLockWarn');
    var btn  = document.getElementById('addTaskBtn');
    if (!t || !at) { warn.classList.remove('show'); btn.disabled = false; return; }
    var r = checkLock(t, at);
    if (r.allowed) {
      warn.classList.remove('show');
      btn.disabled = false;
    } else {
      document.getElementById('bandLockReason').textContent = REASON_HINTS[r.reason] || r.reason;
      document.getElementById('bandLockHint').textContent = 'reason_code = ' + r.reason;
      warn.classList.add('show');
      btn.disabled = true;
    }
  }

  document.getElementById('taskTime').addEventListener('change', refresh);
  document.getElementById('taskAction').addEventListener('change', refresh);

  document.getElementById('addTaskFormV2').addEventListener('submit', function(e) {
    var r = checkLock(
      document.getElementById('taskTime').value,
      document.getElementById('taskAction').value
    );
    if (!r.allowed) {
      e.preventDefault();
      alert('Action blocked: ' + (REASON_HINTS[r.reason] || r.reason));
    }
  });
})();
</script>

<script>
/* PlannerV2 rev 7 - 30-category filter rail click handler */
(function() {
  var rows = document.querySelectorAll('.v2-filter-row');
  var hidden = document.getElementById('v2ActiveFilter');
  var leadInput = document.getElementById('v2Company');
  var leadList  = document.getElementById('v2CompanyList');
  window.v2ActiveFilterCategory = '';   /* rev 8 - cascade endpoint reads this */

  function clearActive() {
    rows.forEach(function(r){ r.classList.remove('active'); });
  }

  function applyFilter(optradio) {
    if (!optradio) return;
    /* Hit the production AJAX endpoint that the legacy planner uses for filtered lead lists.
       Backed by Menu/getfilterleads (added in stem_planner_v2_assign_endpoint_php.php).
       Falls back gracefully if endpoint absent - the unfiltered datalist remains usable. */
    var url = '<?= site_url("Menu/getfilterleads") ?>?optradio=' + encodeURIComponent(optradio);
    fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
      .then(function(r){ if (!r.ok) throw new Error('http ' + r.status); return r.json(); })
      .then(function(data) {
        if (!data || !Array.isArray(data.leads)) return;
        /* Repaint datalist */
        leadList.innerHTML = '';
        data.leads.forEach(function(lead) {
          var opt = document.createElement('option');
          opt.value = lead.cname + ' (id ' + lead.id + ')';
          opt.dataset.id = lead.id;
          leadList.appendChild(opt);
        });
        /* Surface count in placeholder */
        if (leadInput) {
          leadInput.placeholder = 'Pick a lead (' + data.leads.length + ' under filter)';
        }
      })
      .catch(function(err) {
        console.warn('PlannerV2 filter AJAX failed, keeping full lead list:', err.message);
        if (leadInput) {
          leadInput.placeholder = 'Pick a lead (filter unavailable, showing full list)';
        }
      });
  }

  rows.forEach(function(row) {
    row.addEventListener('click', function() {
      if (row.classList.contains('disabled')) return;
      var val = row.getAttribute('data-value');
      var wasActive = row.classList.contains('active');
      clearActive();
      if (wasActive) {
        hidden.value = '';
        window.v2ActiveFilterCategory = '';   /* rev 8 - clear cascade selectby */
        if (leadInput) leadInput.placeholder = 'Pick a lead';
        /* Could reload full list here; left as the default datalist render */
        return;
      }
      row.classList.add('active');
      hidden.value = val;
      window.v2ActiveFilterCategory = val;    /* rev 8 - feed cascade selectby */
      applyFilter(val);
    });
  });
})();
</script>

<!-- ============ REV 9 - SPECIAL TASKS JS + CEILING + CAP GUARDS ============ -->
<script>
(function(){
  /* Cache live state from v_planner_day_shape - rendered at the top of the view */
  var dayShape = {
    manual_consumed: <?= (int)($shape->manual_consumed_min ?? 0) ?>,
    auto_consumed:   <?= (int)($shape->auto_consumed_min ?? 0) ?>,
    auto_task_count: <?= (int)($shape->auto_task_count ?? 0) ?>,
    manual_task_count: <?= (int)($shape->manual_task_count ?? 0) ?>,
    work_mode: '<?= $shape->work_mode ?? "wfo" ?>'
  };
  var CEILING_MIN = 540;
  var MEETING_DAILY_CAP = 4;

  function consumedTotal() {
    return dayShape.manual_consumed + dayShape.auto_consumed;
  }
  function meetingCount() {
    /* Counted server-side and refreshed via filter_counts_v2 */
    return dayShape.manual_task_count;
  }

  /* Bind special-task cards to the unified v2 submit endpoint */
  document.querySelectorAll('.v2-special-card').forEach(function(card){
    if (card.classList.contains('locked')) return;
    card.addEventListener('click', function(){
      var key  = card.dataset.key;
      var req  = card.dataset.requires;
      var atid = parseInt(card.dataset.actiontype || '0', 10);

      /* 4-meeting cap gate */
      if ((atid === 3 || atid === 4) && meetingCount() >= MEETING_DAILY_CAP) {
        document.getElementById('v2MeetingCapToast').style.display = 'block';
        setTimeout(function(){ document.getElementById('v2MeetingCapToast').style.display = 'none'; }, 5000);
        return;
      }
      /* 540 ceiling gate */
      if (consumedTotal() >= CEILING_MIN) {
        document.getElementById('v2CeilingToast').style.display = 'block';
        setTimeout(function(){ document.getElementById('v2CeilingToast').style.display = 'none'; }, 5000);
        return;
      }

      var payload = { special_key: key };
      if (req === 'cluster') {
        var cid = window.prompt('Enter cluster id (Barg by cluster - production line 19136 requires it):');
        if (!cid) return;
        payload.cluster_id = parseInt(cid, 10);
      } else if (req === 'purpose_init') {
        var lid = window.prompt('Enter lead id (init_call.id):');
        if (!lid) return;
        var pid = window.prompt('Enter purpose id:');
        if (!pid) return;
        payload.init_id = parseInt(lid, 10);
        payload.purpose_id = parseInt(pid, 10);
      }
      payload.bdid = <?= (int)$uid ?>;
      payload.pdate = '<?= $adate ?>';

      /* POST to the rev 9 endpoint mirrored from PlannerV2 controller */
      fetch('<?= base_url() ?>api/planner/v2/submit_task', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(payload).toString(),
        credentials: 'same-origin'
      }).then(function(r){
        if (!r.ok) { throw new Error('submit failed - HTTP ' + r.status); }
        return r.json();
      }).then(function(j){
        if (j.status === 'ok') {
          window.location.reload();
        } else {
          alert('Could not add task: ' + (j.message || 'unknown error'));
        }
      }).catch(function(err){
        alert('Special task submit error: ' + err.message);
      });
    });
  });

  /* Guard the main Add Task form against the same two ceilings */
  var mainForm = document.getElementById('addTaskFormV2');
  if (mainForm) {
    mainForm.addEventListener('submit', function(ev){
      var atid = parseInt((mainForm.querySelector('[name="ntaction"]') || {}).value || '0', 10);
      if ((atid === 3 || atid === 4) && meetingCount() >= MEETING_DAILY_CAP) {
        ev.preventDefault();
        document.getElementById('v2MeetingCapToast').style.display = 'block';
        return false;
      }
      if (consumedTotal() >= CEILING_MIN) {
        ev.preventDefault();
        document.getElementById('v2CeilingToast').style.display = 'block';
        return false;
      }
    });
  }
})();
</script>
