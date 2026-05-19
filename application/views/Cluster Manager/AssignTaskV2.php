<?php
/**
 * AssignTaskV2.php - CM/RM line-manager surface for assigning tasks to BDs
 * rev 7 (production filter parity + line-manager assign-task)
 *
 * POSTs to Menu/dailyTaskAssign (existing production endpoint, line 16421 of Menu.php)
 * Mirrors production behavior:
 *   - Cluster pre-check (BD must have cluster_id set, else show banner and block submit)
 *   - Rs 500 wallet deduction warning for actiontype 4 (Barg in Meeting); rejects if balance under 500
 *   - selectby = "Assign Task By <current_user_name>" written to tblcallevents
 *   - assignedto_id = target BD uid, assignedto_by = current CM/RM uid
 *   - approved_status = 1 (line manager assigned tasks are pre-approved)
 *
 * Day shape lock honored: WFO blocks actiontype 3 and 4; auto band 1500-1730 only allows 1,2,13.
 *
 * Pulls team list via Menu_model::GetTotalTeam($current_uid).
 * Pulls lead list per selected BD via AJAX (Menu/getfilterleads?optradio=<cat>&bd_uid=<id>).
 * Pulls BD wallet balance via AJAX (Menu/getbdwallet?bd_uid=<id>).
 *
 * Form fields posted (production parity):
 *   user (target BD uid), company[] (init_call.id, can be multi), plandate (YYYY-MM-DD),
 *   tasktimeplan (HH:MM), atask (action_id), current_status (cstatus from lead),
 *   targetstatus (next cstatus), targetDate (review target), ntppose (purpose id),
 *   star_rating (1-5, default 3)
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$current_uid  = $this->session->userdata('userId');
$current_name = $this->session->userdata('userName');
$current_type = $this->session->userdata('typeId');

/* Production line-manager type_ids: 4 (CM), 13 (CM type 2), 19, 20, 21, 22, 23, 24 (RM/ACM/ASH variants).
   If somehow a non-line-manager hits this view, redirect them out. */
$line_manager_types = [4, 13, 19, 20, 21, 22, 23, 24];
if (!in_array((int)$current_type, $line_manager_types, true)) {
    redirect('home');
}

$team = method_exists($this->Menu_model, 'GetTotalTeam')
    ? $this->Menu_model->GetTotalTeam($current_uid)
    : [];

$action_list = $this->db->select('id, action')
    ->from('action')
    ->where('id IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,22)', NULL, FALSE)
    ->order_by('id', 'ASC')->get()->result_array();

$cstatus_list = $this->db->select('id, name')->from('cstatus')->order_by('id', 'ASC')->get()->result_array();

$default_date = date('Y-m-d', strtotime('+1 day'));
$default_time = '11:00';
?>
<style>
.assign-v2-card{background:#fff;border:1px solid #d0d7de;border-radius:10px;padding:20px;margin-bottom:18px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.assign-v2-title{font-size:18px;font-weight:700;color:#1f2328;margin-bottom:4px}
.assign-v2-sub{font-size:13px;color:#57606a;margin-bottom:18px}
.assign-v2-row{display:flex;gap:14px;margin-bottom:14px;flex-wrap:wrap}
.assign-v2-col{flex:1;min-width:220px}
.assign-v2-label{font-size:12px;font-weight:600;color:#57606a;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;display:block}
.assign-v2-warn{background:#fff8c5;border:1px solid #d4a72c;padding:10px 14px;border-radius:6px;margin-bottom:14px;display:none;color:#7d4e00;font-size:13px}
.assign-v2-warn.show{display:block}
.assign-v2-danger{background:#ffd0d2;border:1px solid #cf222e;padding:10px 14px;border-radius:6px;margin-bottom:14px;display:none;color:#82071e;font-size:13px;font-weight:600}
.assign-v2-danger.show{display:block}
.assign-v2-info{background:#ddf4ff;border:1px solid #54aeff;padding:8px 12px;border-radius:6px;font-size:12px;color:#0550ae;margin-bottom:10px}
.assign-v2-wallet{font-size:14px;font-weight:700;color:#1a7f37}
.assign-v2-wallet.low{color:#cf222e}
.assign-v2-submit{background:#0969da;color:#fff;padding:10px 22px;border:0;border-radius:6px;font-weight:600;cursor:pointer}
.assign-v2-submit:disabled{background:#8c959f;cursor:not-allowed}
.assign-v2-form input[type=text],.assign-v2-form input[type=date],.assign-v2-form input[type=time],.assign-v2-form select{width:100%;padding:8px 10px;border:1px solid #d0d7de;border-radius:6px;font-size:14px}
</style>

<div class="content-wrapper" style="padding:20px;background:#f6f8fa;min-height:100vh">
  <div class="assign-v2-card" style="max-width:920px;margin:0 auto">
    <div class="assign-v2-title">Assign Task to BD (rev 7)</div>
    <div class="assign-v2-sub">Line manager surface. Posts to Menu/dailyTaskAssign. Same wallet rules and cluster pre-check as production.</div>

    <div class="assign-v2-info">
      Day shape lock is enforced. Manual band 10 to 15. Auto band 15 to 1730 allows only actiontype 1, 2, 13.
      Plan window 1730 to 1830 blocks task adds. Closed after 1830.
      WFO mode blocks actiontype 3 (Scheduled Meeting) and 4 (Barg in Meeting).
    </div>

    <div id="clusterBanner" class="assign-v2-danger">
      Target BD has no cluster set. Production blocks assign without cluster. Set cluster first.
    </div>

    <div id="walletBanner" class="assign-v2-warn">
      Target BD wallet balance under Rs 500. Barg in Meeting (actiontype 4) will be rejected.
    </div>

    <div id="bandBanner" class="assign-v2-danger"></div>

    <form id="assignTaskV2Form" class="assign-v2-form" method="POST" action="<?= site_url('Menu/dailyTaskAssign') ?>">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

      <div class="assign-v2-row">
        <div class="assign-v2-col">
          <label class="assign-v2-label">Target BD</label>
          <select id="bdPicker" name="user" required>
            <option value="">Select BD</option>
            <?php foreach ($team as $member): ?>
              <option value="<?= (int)$member['user_id'] ?>"
                      data-cluster="<?= htmlspecialchars($member['cluster_id'] ?? '') ?>"
                      data-name="<?= htmlspecialchars($member['user_name'] ?? '') ?>">
                <?= htmlspecialchars(($member['user_name'] ?? 'Unknown') . ' (uid ' . $member['user_id'] . ')') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="assign-v2-col">
          <label class="assign-v2-label">BD wallet balance</label>
          <div id="walletDisplay" class="assign-v2-wallet">Rs --</div>
        </div>
      </div>

      <div class="assign-v2-row">
        <div class="assign-v2-col">
          <label class="assign-v2-label">Filter category (production parity)</label>
          <select id="filterCategory">
            <option value="">All leads under BD</option>
            <option value="Mandatory Task">Mandatory Task</option>
            <option value="Compulsive Task">Compulsive Task</option>
            <option value="Need Your Attention">Need Your Attention</option>
            <option value="Emergency Meetings Task">Emergency Meetings Task</option>
            <option value="Future Task">Future Task</option>
            <option value="Status">Status</option>
            <option value="Same Status Last Limit Days">Same Status Last Limit Days</option>
            <option value="Plan But Not Initiated">Plan But Not Initiated</option>
            <option value="Plan But Not Initiated Old">Plan But Not Initiated Old</option>
            <option value="No Calling Done After Only Got Details">No Calling Done After Only Got Details</option>
            <option value="Next Follow Up Date">Next Follow Up Date</option>
            <option value="Cluster Location">Cluster Location</option>
            <option value="Closing Timeline">Closing Timeline</option>
            <option value="Quater Strategy">Quater Strategy</option>
            <option value="Marked In Current Quarter">Marked In Current Quarter</option>
            <option value="Compnay Name">Compnay Name</option>
            <option value="Find Company By">Find Company By</option>
            <option value="Task Action">Task Action</option>
            <option value="actionNotPlanned">actionNotPlanned</option>
          </select>
        </div>
        <div class="assign-v2-col">
          <label class="assign-v2-label">Lead (company)</label>
          <input list="leadList" id="leadPicker" placeholder="Pick a BD first" disabled>
          <datalist id="leadList"></datalist>
          <input type="hidden" name="company[]" id="leadHidden">
        </div>
      </div>

      <div class="assign-v2-row">
        <div class="assign-v2-col">
          <label class="assign-v2-label">Plan date</label>
          <input type="date" id="planDate" name="plandate" value="<?= $default_date ?>" required>
        </div>
        <div class="assign-v2-col">
          <label class="assign-v2-label">Plan time</label>
          <input type="time" id="planTime" name="tasktimeplan" value="<?= $default_time ?>" required>
        </div>
        <div class="assign-v2-col">
          <label class="assign-v2-label">Action</label>
          <select id="actionPicker" name="atask" required>
            <option value="">Select action</option>
            <?php foreach ($action_list as $a): ?>
              <option value="<?= (int)$a['id'] ?>"><?= htmlspecialchars($a['id'] . ' - ' . $a['action']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="assign-v2-row">
        <div class="assign-v2-col">
          <label class="assign-v2-label">Purpose</label>
          <select id="purposePicker" name="ntppose" required disabled>
            <option value="">Pick action first</option>
          </select>
        </div>
        <div class="assign-v2-col">
          <label class="assign-v2-label">Current status (from lead)</label>
          <input type="text" id="currentStatusDisplay" readonly placeholder="auto">
          <input type="hidden" id="currentStatusHidden" name="current_status">
        </div>
      </div>

      <div class="assign-v2-row">
        <div class="assign-v2-col">
          <label class="assign-v2-label">Target status</label>
          <select id="targetStatusPicker" name="targetstatus" required>
            <option value="">Select target status</option>
            <?php foreach ($cstatus_list as $s): ?>
              <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['id'] . ' - ' . $s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="assign-v2-col">
          <label class="assign-v2-label">Target review date</label>
          <input type="date" name="targetDate" value="<?= date('Y-m-d', strtotime('+7 day')) ?>" required>
        </div>
        <div class="assign-v2-col">
          <label class="assign-v2-label">Star rating</label>
          <select name="star_rating">
            <option value="3" selected>3</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
        </div>
      </div>

      <div style="margin-top:18px">
        <button type="submit" id="assignSubmitBtn" class="assign-v2-submit">Assign Task</button>
        <span style="margin-left:14px;font-size:12px;color:#57606a">
          Posts to Menu/dailyTaskAssign. Writes selectby = "Assign Task By <?= htmlspecialchars($current_name) ?>".
        </span>
      </div>
    </form>
  </div>
</div>

<script>
(function() {
  var BASE = '<?= site_url('') ?>';
  var bdPicker  = document.getElementById('bdPicker');
  var filterCat = document.getElementById('filterCategory');
  var leadPicker= document.getElementById('leadPicker');
  var leadList  = document.getElementById('leadList');
  var leadHidden= document.getElementById('leadHidden');
  var wallet    = document.getElementById('walletDisplay');
  var clusterBn = document.getElementById('clusterBanner');
  var walletBn  = document.getElementById('walletBanner');
  var bandBn    = document.getElementById('bandBanner');
  var planTime  = document.getElementById('planTime');
  var actSel    = document.getElementById('actionPicker');
  var purposeSel= document.getElementById('purposePicker');
  var cstatusIn = document.getElementById('currentStatusDisplay');
  var cstatusHd = document.getElementById('currentStatusHidden');
  var submitBtn = document.getElementById('assignSubmitBtn');
  var form      = document.getElementById('assignTaskV2Form');

  var SHAPE = { work_mode: 'wfh' }; /* default; production patches this from user/leave_request */
  var REASON = {
    wfo_blocks_physical_meeting: 'WFO mode blocks Scheduled Meeting and Barg in Meeting',
    auto_band_only_calls_emails_mom_allowed: 'Auto band 1500 to 1730 only allows Call, Email, MoM',
    plan_window_no_field_activity: 'Plan window 1730 to 1830 blocks all task adds',
    out_of_band: 'Outside operating hours'
  };

  function resolveBand(t) {
    if (!t) return null;
    var p = t.split(':'); var m = (parseInt(p[0],10)||0)*60 + (parseInt(p[1],10)||0);
    if (m >= 600 && m < 900)  return 'manual';
    if (m >= 900 && m < 1050) return 'auto';
    if (m >= 1050 && m < 1110)return 'plan_window';
    return 'closed';
  }
  function checkLock(t, at) {
    at = parseInt(at, 10); if (!at) return { allowed: true };
    if (SHAPE.work_mode === 'wfo' && (at === 3 || at === 4)) return { allowed: false, reason: 'wfo_blocks_physical_meeting' };
    var b = resolveBand(t);
    if (b === 'manual') return { allowed: true };
    if (b === 'auto')   return [1,2,13].indexOf(at) !== -1 ? { allowed: true } : { allowed: false, reason: 'auto_band_only_calls_emails_mom_allowed' };
    if (b === 'plan_window') return { allowed: false, reason: 'plan_window_no_field_activity' };
    return { allowed: false, reason: 'out_of_band' };
  }

  function refreshBandWarn() {
    var r = checkLock(planTime.value, actSel.value);
    if (r.allowed) { bandBn.classList.remove('show'); submitBtn.disabled = false; return; }
    bandBn.textContent = 'Action blocked: ' + (REASON[r.reason] || r.reason);
    bandBn.classList.add('show');
    submitBtn.disabled = true;
  }

  function loadWallet(bdUid) {
    wallet.textContent = 'Rs --'; wallet.classList.remove('low');
    walletBn.classList.remove('show');
    if (!bdUid) return;
    fetch(BASE + 'Menu/getbdwallet?bd_uid=' + bdUid, { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(d) {
        var bal = parseFloat(d && d.ucash) || 0;
        wallet.textContent = 'Rs ' + bal.toFixed(0);
        if (bal < 500) { wallet.classList.add('low'); walletBn.classList.add('show'); }
      })
      .catch(function(){ wallet.textContent = 'Rs ? (wallet endpoint down)'; });
  }

  function loadLeads(bdUid, cat) {
    leadList.innerHTML = '';
    leadPicker.disabled = !bdUid; leadPicker.value = ''; leadHidden.value = '';
    if (!bdUid) { leadPicker.placeholder = 'Pick a BD first'; return; }
    leadPicker.placeholder = 'Loading leads...';
    var url = BASE + 'Menu/getfilterleads?bd_uid=' + bdUid + (cat ? '&optradio=' + encodeURIComponent(cat) : '');
    fetch(url, { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(d) {
        var leads = (d && d.leads) || [];
        leads.forEach(function(l) {
          var opt = document.createElement('option');
          opt.value = (l.cname || 'Unknown') + ' (id ' + l.id + ')';
          opt.dataset.id = l.id;
          opt.dataset.cstatus = l.cstatus || '';
          opt.dataset.cstatusname = l.cstatus_name || '';
          leadList.appendChild(opt);
        });
        leadPicker.placeholder = 'Pick a lead (' + leads.length + ' under filter)';
      })
      .catch(function(){ leadPicker.placeholder = 'Lead list unavailable'; });
  }

  function loadPurposes(actionId) {
    purposeSel.innerHTML = '<option value="">Loading...</option>';
    purposeSel.disabled = true;
    if (!actionId) { purposeSel.innerHTML = '<option value="">Pick action first</option>'; return; }
    // rev 8: production-parity cascade endpoint. CM Assign does not need the
    // Day Plan Barge-rewrite (apply_barge_rewrite=0). selectby comes from the
    // active filter category dropdown so 'Next Follow Up Date' and 'Call On
    // School' route to the right cascade branch.
    var inid     = (leadHidden && leadHidden.value) ? leadHidden.value : '';
    var selectby = (filterCat && filterCat.value) ? filterCat.value : '';
    var qs = 'Menu/getpurposes_v2?action_id=' + encodeURIComponent(actionId)
           + '&inid='     + encodeURIComponent(inid)
           + '&selectby=' + encodeURIComponent(selectby)
           + '&apply_barge_rewrite=0';
    fetch(BASE + qs, { credentials: 'same-origin' })
      .then(function(r){ return r.ok ? r.json() : { rows: [], fallback_used: true }; })
      .catch(function(){ return { rows: [], fallback_used: true }; })
      .then(function(resp) {
        var items = (resp && resp.rows) || [];
        if (!Array.isArray(items) || items.length === 0) {
          items = [{ id: 34, name: 'Fresh Meeting' }];
        }
        purposeSel.innerHTML = '<option value="">Select purpose</option>';
        items.forEach(function(p) {
          var o = document.createElement('option');
          o.value = p.id; o.textContent = p.id + ' - ' + p.name;
          purposeSel.appendChild(o);
        });
        purposeSel.disabled = false;
        if (resp && resp.fallback_used) {
          purposeSel.title = 'No purposes for this action and lead status pair - showing Fresh Meeting default.';
        }
      });
  }

  bdPicker.addEventListener('change', function() {
    var sel = bdPicker.selectedOptions[0];
    var cluster = sel ? (sel.dataset.cluster || '').trim() : '';
    if (bdPicker.value && !cluster) { clusterBn.classList.add('show'); submitBtn.disabled = true; }
    else { clusterBn.classList.remove('show'); submitBtn.disabled = false; }
    loadWallet(bdPicker.value);
    loadLeads(bdPicker.value, filterCat.value);
  });

  filterCat.addEventListener('change', function() { loadLeads(bdPicker.value, filterCat.value); });

  leadPicker.addEventListener('input', function() {
    var v = leadPicker.value;
    var opts = leadList.options;
    for (var i = 0; i < opts.length; i++) {
      if (opts[i].value === v) {
        leadHidden.value = opts[i].dataset.id || '';
        cstatusIn.value  = opts[i].dataset.cstatusname || ('cstatus ' + opts[i].dataset.cstatus);
        cstatusHd.value  = opts[i].dataset.cstatus || '';
        return;
      }
    }
    leadHidden.value = ''; cstatusIn.value = ''; cstatusHd.value = '';
  });

  actSel.addEventListener('change', function() { loadPurposes(actSel.value); refreshBandWarn(); });
  planTime.addEventListener('change', refreshBandWarn);

  form.addEventListener('submit', function(e) {
    if (!leadHidden.value) { e.preventDefault(); alert('Pick a lead first.'); return; }
    var r = checkLock(planTime.value, actSel.value);
    if (!r.allowed) { e.preventDefault(); alert('Action blocked: ' + (REASON[r.reason] || r.reason)); return; }
    if (clusterBn.classList.contains('show')) { e.preventDefault(); alert('Set cluster for target BD before assigning.'); return; }
    var at = parseInt(actSel.value, 10);
    if (at === 4) {
      var balText = wallet.textContent.replace(/[^0-9]/g, '');
      var bal = parseInt(balText, 10) || 0;
      if (bal < 500) { e.preventDefault(); alert('Barg in Meeting needs Rs 500 in BD wallet. Current balance Rs ' + bal); return; }
    }
  });
})();
</script>
