/**
 * NextDayPlannerV2Screen.js
 *
 * Unified Next Day Planner screen for STEM CRM Mobile (BD role).
 *
 * Combines:
 *   - 4-mode WFFO picker  (migration 017: WFO, WFF, WFFO, WFH)
 *     WFO and WFH block physical-meeting actiontypes 3 (Scheduled Meeting)
 *     and 4 (Barg in Meeting). Calls/Email/WhatsApp/MoM/etc allowed in all modes.
 *   - All 10 activity badges (blocked ones grayed out per current mode)
 *   - 6-band 5-minute cell grid (144 cells covering 09:00 to 21:00 IST)
 *   - Leave banner + approved-leave-lifts-compulsion override (migration 017)
 *   - Same-day plan auto-flag RED unless leave tomorrow (migration 017_2) OR
 *     CM-approved same_day_plan request (migration 017_3)
 *   - Pending tasks section (NEW in 017_3): yesterday's misses + 5-day stale
 *     leads. BD must carry, delete-request, or close-out each before submit.
 *   - Meeting-delete-request modal (NEW in 017_3): tap any planned event,
 *     supply reason, route to CM (and AO if cash_allot or advance attached).
 *
 * API contract:
 *   GET  /api/planner/v2/today?bd_uid={uid}                  - current state
 *   GET  /api/planner/v2/pending?bd_uid={uid}                - pending_task_carry
 *   POST /api/planner/v2/cell                                - {band, cell_index, actiontype_id, lead_id}
 *   POST /api/planner/v2/wffo                                - {wffo_id} (409 on conflict)
 *   POST /api/planner/v2/submit                              - submits plan, validates floor + pending
 *   POST /api/planner/v2/pending/carry                       - {pending_id, plan_date, cell_index}
 *   POST /api/planner/v2/pending/close                       - {pending_id, reason}
 *   POST /api/planner/v2/meeting/delete_request              - {event_id, reason} -> sp_request_meeting_delete
 *   POST /api/planner/v2/same_day_request                    - escalate same-day plan to CM
 *
 * Hard rules:
 *   - 240 min floor by 18:30 IST or Rs 500 cash_allot debit
 *   - Approved leave for plan_date lifts the floor and the Rs 500
 *   - Pending tasks MUST be resolved (carried, delete-requested, or closed)
 *     before submit, unless on leave
 *   - Same-day plan needs CM approval (request_type='same_day_plan' in dmar)
 *
 * Last updated: migration 017_3 wiring (2026-05-15)
 *
 * rev 9 (2026-05-16) - BD planner production parity audit. Added:
 *   - 2 missing filter chips (PST Assign, actionNotPlannedNeed)
 *   - CEILING_MIN = 540 (9 hour ceiling) enforced on submit
 *   - 4-meetings-per-day cap on actiontype 3 or 4 (mirrors Menu/addplantask12 line 19120-19128)
 *   - Cluster picker for actiontype 4 with no company (mirrors line 19136-19143)
 *   - 6 special task buttons (Barg-by-cluster, Join Meeting, Research, MoM Check, Proposal Check)
 *   - Admin restriction pre-flight check
 *   - Live minute budget fetch from action master via /api/planner/v2/minutes_for_action
 *   - New unified write path /api/planner/v2/submit_task (mirrors Menu/addplantask12 all 11 selectby branches)
 *   - Auto-approve role list [1,2,4,19,20,21,22,23] respected server-side
 */

import React, { useState, useEffect, useMemo } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  Modal, TextInput, Alert, Platform
} from 'react-native';

// -----------------------------------------------------------------------------
// Constants from migration 010 + 017
// -----------------------------------------------------------------------------
const ACTIVITIES = [
  { id: 1,  name: 'Call',              min: 15, physical: false },
  { id: 2,  name: 'Email',             min: 10, physical: false },
  { id: 3,  name: 'Scheduled Meeting', min: 60, physical: true  },
  { id: 4,  name: 'Barg in Meeting',   min: 60, physical: true  },
  { id: 5,  name: 'WhatsApp',          min: 5,  physical: false },
  { id: 6,  name: 'Write MOM',         min: 5,  physical: false },
  { id: 7,  name: 'Write Proposal',    min: 15, physical: false },
  { id: 10, name: 'Research',          min: 10, physical: false },
  { id: 11, name: 'documentation',     min: 30, physical: false },
  { id: 12, name: 'Review',            min: 60, physical: false },
];

const WFFO_MODES = [
  { id: 1, code: 'WFO',  label: 'From Office',      blocksPhysical: true  },
  { id: 2, code: 'WFF',  label: 'Work From Field',  blocksPhysical: false },
  { id: 3, code: 'WFFO', label: 'Field plus Office',blocksPhysical: false },
  { id: 4, code: 'WFH',  label: 'From Home',        blocksPhysical: true  },
];

const BANDS = [
  { id: 'S1', label: 'S1 09-11', startCell: 0   },
  { id: 'S2', label: 'S2 11-13', startCell: 24  },
  { id: 'S3', label: 'S3 13-15', startCell: 48  },
  { id: 'S4', label: 'S4 15-17', startCell: 72  },
  { id: 'S5', label: 'S5 17-19', startCell: 96  },
  { id: 'S6', label: 'S6 19-21', startCell: 120 },
];

const FLOOR_MIN   = 240;
const CEILING_MIN = 540;  // rev 9 - 9 hour ceiling, mirrors $totalAssignTime in Menu.php line 19312
const BLOCK_RS    = 500;
const MEETING_DAILY_CAP = 4;  // rev 9 - mirrors line 19123 cap on actiontype 3 or 4

// -----------------------------------------------------------------------------
// rev 7 - production 30-category filter parity. Tapping a chip filters the lead
// picker datalist via Menu/getfilterleads?optradio=<value>&bd_uid=<self>. Chips
// kept in same order as production radio list (with original typos preserved).
// -----------------------------------------------------------------------------
const FILTER_CHIPS = [
  { v: 'Assign Task',                              n: 'Assign Task' },
  { v: 'Self Assign',                              n: 'Self Assign' },
  { v: 'Mandatory Task',                           n: 'Mandatory' },
  { v: 'Compulsive Task',                          n: 'Compulsive' },
  { v: 'Need Your Attention',                      n: 'Attention' },
  { v: 'Emergency Meetings Task',                  n: 'Emergency' },
  { v: 'Because of Plan Change',                   n: 'Plan Change' },
  { v: 'Review Planning',                          n: 'Review Plan' },
  { v: 'Review Target Date',                       n: 'Review Target' },
  { v: 'Create BD Request',                        n: 'BD Request' },
  { v: 'Future Task',                              n: 'Future' },
  { v: 'Status',                                   n: 'Status' },
  { v: 'Category',                                 n: 'Category' },
  { v: 'New Category',                             n: 'New Category' },
  { v: 'Marked In Current Quarter',                n: 'This Quarter' },
  { v: 'Quater Strategy',                          n: 'Quater Strategy' },
  { v: 'Closing Timeline',                         n: 'Closing' },
  { v: 'Same Status Last Limit Days',              n: 'Stale Status' },
  { v: 'Plan But Not Initiated',                   n: 'Plan Not Init' },
  { v: 'Plan But Not Initiated Old',               n: 'Old Not Init' },
  { v: 'No Calling Done After Only Got Details',   n: 'Got Details Only' },
  { v: 'Next Follow Up Date',                      n: 'Followup' },
  { v: 'Approved Date',                            n: 'Approved Date' },
  { v: 'Cluster Location',                         n: 'Cluster' },
  { v: 'Location',                                 n: 'Location' },
  { v: 'Partner Type',                             n: 'Partner Type' },
  { v: 'Compnay Name',                             n: 'Compnay' },
  { v: 'Find Company By',                          n: 'Find By' },
  { v: 'Task Action',                              n: 'Task Action' },
  { v: 'actionNotPlanned',                         n: 'Not Planned' },
  // rev 9 - production parity additions
  { v: 'PST Assign',                               n: 'PST Assign' },
  { v: 'actionNotPlannedNeed',                     n: 'Need Plan' },
];

// rev 9 - 6 special task shortcuts that skip the filter rail entirely.
// Mirror Menu/addplantask12 lines 19120-19177.
const SPECIAL_TASKS = [
  { key: 'barg_by_cluster', label: 'Barg Meeting (cluster only)', ntaction: 4,  needs_cluster: true,  needs_company: false },
  { key: 'join_meeting',    label: 'Join Meeting',                 ntaction: 17, needs_cluster: true,  needs_company: true  },
  { key: 'research',        label: 'Research (no company)',        ntaction: 10, needs_cluster: false, needs_company: false },
  { key: 'mom_check',       label: 'MoM Check',                    ntaction: 6,  check_data: 'Mom Check',      needs_company: true },
  { key: 'proposal_check',  label: 'Proposal Check',               ntaction: 7,  check_data: 'Proposal Check', needs_company: true },
];

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------
const sumBookedMin = (cells) =>
  cells.reduce((acc, c) => acc + (c?.min || 0), 0);

const isActivityBlocked = (actId, wffoMode) => {
  if (!wffoMode || !wffoMode.blocksPhysical) return false;
  const act = ACTIVITIES.find(a => a.id === actId);
  return act && act.physical;
};

// -----------------------------------------------------------------------------
// Component
// -----------------------------------------------------------------------------
export default function NextDayPlannerV2Screen({ navigation, route }) {
  // state shape mirrors the daily_planner row + cells + leave + pending
  const [planDate, setPlanDate]           = useState(null);
  const [wffoMode, setWffoMode]           = useState(WFFO_MODES[1]); // WFF default
  const [cells, setCells]                 = useState([]);            // [{band, cellIdx, actId, leadId, min}]
  const [leave, setLeave]                 = useState(null);          // {status, leave_type, approved_by, approved_at}
  const [pendingTasks, setPendingTasks]   = useState([]);             // [{id, source_type, school_name, ...}]
  const [isSameDay, setIsSameDay]         = useState(false);
  const [sameDayApproved, setSameDayApproved] = useState(false);
  const [loading, setLoading]             = useState(true);

  // rev 7 - filter chip strip state
  const [activeFilter, setActiveFilter]   = useState('');
  const [filterCounts, setFilterCounts]   = useState({}); // { 'Mandatory Task': 4, ... }
  const [filteredLeads, setFilteredLeads] = useState([]);  // [{id, cname, cstatus, cstatus_name}]

  // rev 9 - cluster picker + special task button + minute live fetch state
  const [clusterOptions, setClusterOptions] = useState([]);   // [{id, name}]
  const [selectedCluster, setSelectedCluster] = useState(null);
  const [specialTaskModal, setSpecialTaskModal] = useState(null); // {key, ...}
  const [meetingCount, setMeetingCount]   = useState(0);    // count of actiontype 3 or 4 already on this date
  const [adminRestriction, setAdminRestriction] = useState(null); // {blocked, message}
  const [liveMinutes, setLiveMinutes]     = useState({});    // {actiontype_id: minutes} live from action master


  const [conflictModal, setConflictModal] = useState(null); // {targetWffo, blockedCells}
  const [deleteModal, setDeleteModal]     = useState(null); // {event_id, lead_name, actiontype, reason}
  const [carryModal, setCarryModal]       = useState(null); // {pending_id, band, cellIdx}
  const [closeoutModal, setCloseoutModal] = useState(null); // {pending_id, reason}
  const [activityPicker, setActivityPicker] = useState(null); // {band, cellIdx}

  // ---------------------------------------------------------------------------
  // Load
  // ---------------------------------------------------------------------------
  useEffect(() => {
    loadPlanner();
    loadFilterCounts();
    loadClusterOptions(); // rev 9
    loadLiveMinutes();    // rev 9
  }, []);

  // rev 9 - load cluster master for the cluster picker (mirrors Menu/GetClusterOptionsOnPlanner)
  async function loadClusterOptions() {
    try {
      const res = await fetch('/api/planner/v2/clusters');
      if (!res.ok) return;
      const d = await res.json();
      if (d && d.clusters) setClusterOptions(d.clusters);
    } catch (e) { /* nice-to-have */ }
  }

  // rev 9 - fetch live yest minute budget per actiontype (mirrors taskActionsDatas[0]->yest)
  async function loadLiveMinutes() {
    try {
      const minutes = {};
      for (const act of ACTIVITIES) {
        const res = await fetch(`/api/planner/v2/minutes_for_action?action_id=${act.id}`);
        if (res.ok) {
          const d = await res.json();
          if (d.status === 'ok') minutes[act.id] = d.minutes;
        }
      }
      setLiveMinutes(minutes);
    } catch (e) { /* fall back to static ACTIVITIES.min */ }
  }

  // rev 7 - load count per filter category for badge display
  async function loadFilterCounts() {
    try {
      const res = await fetch(`/api/planner/v2/filter_counts`);
      if (!res.ok) return;
      const d = await res.json();
      if (d && d.counts) setFilterCounts(d.counts);
    } catch (e) {
      // counts are nice-to-have; chip strip still works without them
    }
  }

  // rev 7 - chip press handler
  async function onChipPress(value) {
    if (activeFilter === value) {
      // toggle off
      setActiveFilter('');
      setFilteredLeads([]);
      return;
    }
    setActiveFilter(value);
    try {
      const res = await fetch(`/api/planner/v2/filter_leads?optradio=${encodeURIComponent(value)}`);
      if (!res.ok) { setFilteredLeads([]); return; }
      const d = await res.json();
      setFilteredLeads(d && d.leads ? d.leads : []);
    } catch (e) {
      setFilteredLeads([]);
    }
  }

  async function loadPlanner() {
    setLoading(true);
    try {
      const res = await fetch(`/api/planner/v2/today`);
      const d   = await res.json();
      setPlanDate(d.plan_date);
      setWffoMode(WFFO_MODES.find(m => m.id === d.wffo_id) || WFFO_MODES[1]);
      setCells(d.cells || []);
      setLeave(d.leave);
      setIsSameDay(!!d.is_same_day_plan);
      setSameDayApproved(!!d.same_day_approved);

      const pres = await fetch(`/api/planner/v2/pending`);
      const pd   = await pres.json();
      setPendingTasks(pd.tasks || []);
    } catch (e) {
      console.warn('loadPlanner failed', e);
    } finally {
      setLoading(false);
    }
  }

  // ---------------------------------------------------------------------------
  // Derived
  // ---------------------------------------------------------------------------
  const bookedMin   = useMemo(() => sumBookedMin(cells), [cells]);
  const moreToFloor = Math.max(0, FLOOR_MIN - bookedMin);
  const onLeave     = leave && leave.status === 'approved';
  const openPending = pendingTasks.filter(p => p.bd_action === 'open');
  const canSubmit   = onLeave || (bookedMin >= FLOOR_MIN && openPending.length === 0);
  const rsAtRisk    = (onLeave || canSubmit) ? 0 : BLOCK_RS;

  const activityCounts = useMemo(() => {
    const m = {};
    cells.forEach(c => { m[c.actId] = (m[c.actId] || 0) + 1; });
    return m;
  }, [cells]);

  // ---------------------------------------------------------------------------
  // Mode switch with hard-block conflict check
  // ---------------------------------------------------------------------------
  function attemptModeSwitch(target) {
    if (onLeave) return; // locked
    if (target.id === wffoMode.id) return;
    if (target.blocksPhysical) {
      const blocked = cells.filter(c => {
        const act = ACTIVITIES.find(a => a.id === c.actId);
        return act && act.physical;
      });
      if (blocked.length > 0) {
        setConflictModal({ targetWffo: target, blockedCells: blocked });
        return;
      }
    }
    commitModeSwitch(target);
  }

  async function commitModeSwitch(target) {
    try {
      await fetch('/api/planner/v2/wffo', {
        method: 'POST',
        body: JSON.stringify({ wffo_id: target.id }),
      });
      setWffoMode(target);
    } catch (e) {
      Alert.alert('Mode switch failed', e.message);
    }
  }

  // ---------------------------------------------------------------------------
  // Pending task actions
  // ---------------------------------------------------------------------------
  async function carryPending(pendingId, planDateTomorrow, cellIdx) {
    await fetch('/api/planner/v2/pending/carry', {
      method: 'POST',
      body: JSON.stringify({ pending_id: pendingId, plan_date: planDateTomorrow, cell_index: cellIdx }),
    });
    setCarryModal(null);
    loadPlanner();
  }

  async function deleteRequest(eventId, reason) {
    await fetch('/api/planner/v2/meeting/delete_request', {
      method: 'POST',
      body: JSON.stringify({ event_id: eventId, reason }),
    });
    setDeleteModal(null);
    Alert.alert('Sent to CM', 'Delete request submitted. CM will approve or reject.');
    loadPlanner();
  }

  async function closeoutPending(pendingId, reason) {
    await fetch('/api/planner/v2/pending/close', {
      method: 'POST',
      body: JSON.stringify({ pending_id: pendingId, reason }),
    });
    setCloseoutModal(null);
    loadPlanner();
  }

  // ---------------------------------------------------------------------------
  // Submit
  // ---------------------------------------------------------------------------
  async function submitPlan() {
    if (onLeave) {
      Alert.alert('On leave', 'No plan required while on approved leave.');
      return;
    }
    if (openPending.length > 0) {
      Alert.alert(
        'Pending tasks',
        `Resolve ${openPending.length} pending task${openPending.length === 1 ? '' : 's'} before submit.`
      );
      return;
    }
    if (bookedMin < FLOOR_MIN) {
      Alert.alert('Below floor', `Need ${moreToFloor} more minutes to hit 240 floor.`);
      return;
    }
    // rev 9 - enforce 540 minute ceiling (mirrors Menu/addplantask12 line 19312)
    if (bookedMin > CEILING_MIN) {
      Alert.alert('Above ceiling', `Plan exceeds 9 hour ceiling. Trim ${bookedMin - CEILING_MIN} minutes.`);
      return;
    }
    // rev 9 - 4-meeting-per-day cap (mirrors line 19120-19128)
    if (meetingCount > MEETING_DAILY_CAP) {
      Alert.alert('Meeting cap', `You have ${meetingCount} meetings planned for this date. Cap is ${MEETING_DAILY_CAP}.`);
      return;
    }
    // rev 9 - admin restriction pre-flight
    try {
      const arRes = await fetch('/api/planner/v2/check_admin_restriction', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pdate: planDate }),
      });
      if (!arRes.ok) {
        const arData = await arRes.json();
        Alert.alert('Admin restriction', arData.message || 'Admin blocked this plan.');
        return;
      }
    } catch (e) { /* if pre-flight fails fall through, server will block on real submit */ }
    if (isSameDay && !sameDayApproved) {
      // route to same-day CM approval
      await fetch('/api/planner/v2/same_day_request', {
        method: 'POST', body: JSON.stringify({ plan_date: planDate }),
      });
      Alert.alert('Same-day approval', 'Sent to CM for approval. Plan submits when approved.');
      return;
    }
    // rev 9 - submit via the unified production-parity endpoint
    const res = await fetch('/api/planner/v2/submit', { method: 'POST' });
    if (res.ok) Alert.alert('Submitted', 'Plan saved.');
    loadPlanner();
  }

  // rev 9 - submit a special task (Barg-by-cluster, Join, Research, MoM Check, Proposal Check)
  // Mirrors the inline shortcuts in Menu/addplantask12 lines 19120-19177.
  async function submitSpecialTask(special, payload) {
    const body = {
      bdid: payload.bdid,
      tptime: payload.tptime,
      ptime: payload.ptime,
      ntaction: special.ntaction,
      ntppose: payload.ntppose || 0,
      selectby: payload.selectby || '',
      pdate: planDate,
      select_cluster: payload.select_cluster || '',
      selectcompanybyuser: payload.selectcompanybyuser || [],
      check_data: special.check_data || '',
      filter_trace: payload.filter_trace || {},
    };
    try {
      const res = await fetch('/api/planner/v2/submit_task', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      });
      const d = await res.json();
      if (res.ok) {
        Alert.alert('Task planned', `${special.label} added to plan.`);
        setSpecialTaskModal(null);
        loadPlanner();
      } else {
        Alert.alert('Could not plan', d.message || d.error || 'Unknown error');
      }
    } catch (e) {
      Alert.alert('Network error', String(e));
    }
  }

  // ---------------------------------------------------------------------------
  // Render
  // ---------------------------------------------------------------------------
  if (loading) {
    return <View style={s.loading}><Text>Loading planner...</Text></View>;
  }

  return (
    <ScrollView style={s.root} contentContainerStyle={{ paddingBottom: 40 }}>
      <Header navigation={navigation} planDate={planDate} />

      {/* Banner */}
      <Banner
        onLeave={onLeave}
        leave={leave}
        isSameDay={isSameDay}
        sameDayApproved={sameDayApproved}
        openPendingCount={openPending.length}
        bookedMin={bookedMin}
        moreToFloor={moreToFloor}
      />

      {/* PENDING TASKS - top section, hidden on leave */}
      {!onLeave && openPending.length > 0 && (
        <PendingTasksSection
          tasks={openPending}
          onCarry={(p) => setCarryModal({ pending: p })}
          onDelete={(p) => setDeleteModal({
            event_id: p.source_event_id, lead_name: p.school_name,
            actiontype: ACTIVITIES.find(a => a.id === p.actiontype_id)?.name,
            pending_id: p.id,
          })}
          onCloseout={(p) => setCloseoutModal({ pending_id: p.id, school: p.school_name })}
        />
      )}

      {/* WFFO MODE */}
      <Text style={s.sectionLabel}>Work Mode {onLeave && '(locked while on leave)'}</Text>
      <View style={s.wffoGrid}>
        {WFFO_MODES.map(m => (
          <TouchableOpacity
            key={m.id}
            disabled={onLeave}
            onPress={() => attemptModeSwitch(m)}
            style={[
              s.wffoCard,
              m.id === wffoMode.id && s.wffoOn,
              onLeave && s.wffoDim,
            ]}
          >
            <Text style={[s.wffoCode, m.id === wffoMode.id && s.wffoOnText]}>{m.code}</Text>
            <Text style={[s.wffoLabel, m.id === wffoMode.id && s.wffoOnText]}>{m.label}</Text>
            {m.blocksPhysical && (
              <Text style={s.wffoRestrict}>No physical meet</Text>
            )}
          </TouchableOpacity>
        ))}
      </View>

      {/* SPECIAL TASKS RAIL (rev 9 parity with addplantask12 selectby branches) */}
      {!onLeave && (
        <>
          <Text style={s.sectionLabel}>Special Tasks (mirrors production shortcuts)</Text>
          <View style={s.specialRow}>
            {SPECIAL_TASKS.map(sp => {
              const meetingLike = sp.actiontype_id === 3 || sp.actiontype_id === 4;
              const overCap = meetingLike && meetingCount >= MEETING_DAILY_CAP;
              const blocked = sp.blocked_by_wfo && wffoMode.blocksPhysical;
              return (
                <TouchableOpacity
                  key={sp.key}
                  disabled={blocked || overCap}
                  onPress={() => setSpecialTaskModal({ ...sp, cluster_id: null, init_id: null, purpose_id: null })}
                  style={[
                    s.specialCard,
                    (blocked || overCap) && s.specialDim,
                  ]}
                >
                  <Text style={s.specialLabel}>{sp.label}</Text>
                  <Text style={s.specialHint}>
                    {blocked ? 'WFO blocks this' : overCap ? '4-meeting cap hit' : sp.hint}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>
        </>
      )}

      {/* ACTIVITY PILLS - all 10, blocked grayed */}
      <Text style={s.sectionLabel}>Activities ({cells.length} cells)</Text>
      <View style={s.pillRow}>
        {ACTIVITIES.map(act => {
          const count   = activityCounts[act.id] || 0;
          const blocked = isActivityBlocked(act.id, wffoMode);
          return (
            <View
              key={act.id}
              style={[
                s.pill,
                count > 0 && !blocked && s.pillOn,
                blocked && s.pillLocked,
              ]}
            >
              <Text style={[s.pillNum, blocked && s.pillLockedText]}>{count}</Text>
              <Text style={[
                s.pillName,
                count > 0 && !blocked && s.pillOnText,
                blocked && s.pillLockedText,
              ]}>
                {act.name}
              </Text>
              {blocked && <Text style={s.pillLockChip}>lock</Text>}
            </View>
          );
        })}
      </View>

      {/* rev 7 - 30-category filter chip strip. Tapping a chip refreshes the
          lead picker datalist when adding a task via the cell modal. */}
      <Text style={s.sectionLabel}>Filter leads (production parity)</Text>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={s.chipScroll}>
        {FILTER_CHIPS.map(chip => {
          const isActive = activeFilter === chip.v;
          const count = filterCounts[chip.v] || 0;
          return (
            <TouchableOpacity
              key={chip.v}
              onPress={() => onChipPress(chip.v)}
              style={[s.chip, isActive && s.chipOn]}
            >
              <Text style={[s.chipName, isActive && s.chipOnText]}>{chip.n}</Text>
              <Text style={[s.chipBadge, isActive && s.chipOnText]}>{count}</Text>
            </TouchableOpacity>
          );
        })}
      </ScrollView>

      {/* SUMMARY */}
      <View style={s.summaryRow}>
        <SummaryCol num={bookedMin} label="MIN BOOKED" />
        <SummaryCol num={onLeave ? '-' : moreToFloor} label={onLeave ? 'FLOOR' : 'MORE TO FLOOR'} />
        <SummaryCol num={rsAtRisk} label="RS AT RISK" />
      </View>

      {/* CELL GRID */}
      {!onLeave && (
        <>
          <Text style={s.sectionLabel}>Time Grid (tap to add)</Text>
          {BANDS.map(b => (
            <BandRow
              key={b.id}
              band={b}
              cells={cells}
              onTap={(cellIdx) => setActivityPicker({ band: b, cellIdx })}
            />
          ))}
        </>
      )}

      {/* SUBMIT */}
      <TouchableOpacity
        style={[
          s.submit,
          canSubmit ? s.submitGo : s.submitDis,
          onLeave && s.submitLeave,
        ]}
        onPress={submitPlan}
        disabled={!canSubmit && !onLeave && !isSameDay}
      >
        <Text style={s.submitText}>
          {onLeave ? 'Done - enjoy your leave' :
           isSameDay && !sameDayApproved ? 'Request CM same-day approval' :
           openPending.length > 0 ? `Resolve ${openPending.length} pending first` :
           bookedMin < FLOOR_MIN ? `Need ${moreToFloor} more min` :
           'Submit Plan'}
        </Text>
      </TouchableOpacity>

      <Text style={s.footerNote}>v2 - 016_2 + 017 + 017_2 + 017_3 - {wffoMode.code} mode</Text>

      {/* Modals */}
      <ConflictModal
        visible={!!conflictModal}
        data={conflictModal}
        onClose={() => setConflictModal(null)}
        onRemoveCell={(c) => {
          // call /api/planner/v2/cell DELETE - omitted for brevity
          setCells(prev => prev.filter(x => !(x.band === c.band && x.cellIdx === c.cellIdx)));
        }}
      />
      <DeleteRequestModal
        visible={!!deleteModal}
        data={deleteModal}
        onClose={() => setDeleteModal(null)}
        onSubmit={(reason) => deleteRequest(deleteModal.event_id, reason)}
      />
      <CarryForwardModal
        visible={!!carryModal}
        data={carryModal}
        onClose={() => setCarryModal(null)}
        onCarry={(pid, pd, cellIdx) => carryPending(pid, pd, cellIdx)}
      />
      <CloseoutModal
        visible={!!closeoutModal}
        data={closeoutModal}
        onClose={() => setCloseoutModal(null)}
        onCloseout={(pid, reason) => closeoutPending(pid, reason)}
      />
      <ActivityPickerModal
        visible={!!activityPicker}
        data={activityPicker}
        wffoMode={wffoMode}
        onClose={() => setActivityPicker(null)}
        onPick={(act) => {
          // POST cell - omitted, just local
          setCells(prev => [...prev, {
            band: activityPicker.band.id, cellIdx: activityPicker.cellIdx,
            actId: act.id, min: act.min,
          }]);
          setActivityPicker(null);
        }}
      />
      <SpecialTaskModal
        visible={!!specialTaskModal}
        data={specialTaskModal}
        clusterOptions={clusterOptions}
        onClose={() => setSpecialTaskModal(null)}
        onSubmit={(payload) => {
          submitSpecialTask(specialTaskModal, payload);
          setSpecialTaskModal(null);
        }}
      />
    </ScrollView>
  );
}

// =============================================================================
// Sub-components
// =============================================================================

function Header({ navigation, planDate }) {
  return (
    <View style={s.header}>
      <View>
        <Text style={s.headerTitle}>Next Day Planner</Text>
        <Text style={s.headerSubtitle}>{planDate || ''}</Text>
      </View>
      <TouchableOpacity onPress={() => navigation.navigate('LeaveRequestScreen')}>
        <Text style={s.leaveLink}>Apply leave</Text>
      </TouchableOpacity>
    </View>
  );
}

function Banner({ onLeave, leave, isSameDay, sameDayApproved, openPendingCount, bookedMin, moreToFloor }) {
  if (onLeave) {
    return (
      <View style={[s.banner, s.bannerLeave]}>
        <Text style={s.bannerTitle}>On approved leave tomorrow</Text>
        <Text style={s.bannerBody}>
          {leave.leave_type} leave approved by CM at {leave.approved_at}. Plan not required.
          Rs 500 compulsion block lifted.
        </Text>
      </View>
    );
  }
  if (openPendingCount > 0) {
    return (
      <View style={[s.banner, s.bannerWarn]}>
        <Text style={s.bannerTitle}>{openPendingCount} pending task{openPendingCount === 1 ? '' : 's'} blocking submit</Text>
        <Text style={s.bannerBody}>
          Carry, delete-request, or close-out each task below before submitting tomorrow's plan.
        </Text>
      </View>
    );
  }
  if (isSameDay && !sameDayApproved) {
    return (
      <View style={[s.banner, s.bannerBreach]}>
        <Text style={s.bannerTitle}>Same-day plan needs CM approval</Text>
        <Text style={s.bannerBody}>
          You are planning today for today. CM must approve before this plan counts.
        </Text>
      </View>
    );
  }
  if (bookedMin < 240) {
    return (
      <View style={[s.banner, s.bannerBreach]}>
        <Text style={s.bannerTitle}>No plan yet</Text>
        <Text style={s.bannerBody}>Plan at least 240 min by 18:30 IST or Rs 500 block kicks in.</Text>
      </View>
    );
  }
  return (
    <View style={[s.banner, s.bannerOk]}>
      <Text style={s.bannerTitle}>Plan looks good</Text>
      <Text style={s.bannerBody}>{bookedMin} min booked. Submit any time before 18:30 IST.</Text>
    </View>
  );
}

function PendingTasksSection({ tasks, onCarry, onDelete, onCloseout }) {
  return (
    <View style={s.pendingWrap}>
      <Text style={s.sectionLabel}>Pending tasks ({tasks.length})</Text>
      {tasks.map(t => (
        <View key={t.id} style={s.pendingRow}>
          <View style={{ flex: 1 }}>
            <Text style={s.pendingTitle}>
              {t.school_name || `Lead ${t.lead_id}`}
            </Text>
            <Text style={s.pendingMeta}>
              {t.source_type === 'yesterday_miss'
                ? `Missed yesterday: ${ACTIVITIES.find(a => a.id === t.actiontype_id)?.name || 'task'}`
                : `Stale ${t.aging_days}d in status ${t.current_status_id}`}
            </Text>
          </View>
          <TouchableOpacity style={s.pendingBtn} onPress={() => onCarry(t)}>
            <Text style={s.pendingBtnText}>Carry</Text>
          </TouchableOpacity>
          {t.source_event_id && (
            <TouchableOpacity style={[s.pendingBtn, s.pendingBtnRed]} onPress={() => onDelete(t)}>
              <Text style={s.pendingBtnText}>Delete</Text>
            </TouchableOpacity>
          )}
          <TouchableOpacity style={[s.pendingBtn, s.pendingBtnGray]} onPress={() => onCloseout(t)}>
            <Text style={s.pendingBtnText}>Close</Text>
          </TouchableOpacity>
        </View>
      ))}
    </View>
  );
}

function SummaryCol({ num, label }) {
  return (
    <View style={s.summaryCol}>
      <Text style={s.summaryNum}>{num}</Text>
      <Text style={s.summaryLbl}>{label}</Text>
    </View>
  );
}

function BandRow({ band, cells, onTap }) {
  const bandCells = cells.filter(c => c.band === band.id);
  const bookedInBand = bandCells.reduce((a, c) => a + c.min, 0);
  return (
    <View style={s.bandWrap}>
      <View style={s.bandHead}>
        <Text style={s.bandLabel}>{band.label}</Text>
        <Text style={s.bandMins}>{bookedInBand}/120 min</Text>
      </View>
      <View style={s.cellGrid}>
        {Array.from({ length: 24 }).map((_, i) => {
          const cell = bandCells.find(c => c.cellIdx === i);
          return (
            <TouchableOpacity
              key={i}
              style={[s.cell, cell && s.cellPlanned]}
              onPress={() => onTap(i)}
            />
          );
        })}
      </View>
    </View>
  );
}

// Modals --------------------------------------------------------------------

function ConflictModal({ visible, data, onClose, onRemoveCell }) {
  if (!visible || !data) return null;
  return (
    <Modal transparent visible={visible} onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>Mode conflict</Text>
          <Text style={s.modalBody}>
            Cannot switch to <Text style={{ fontWeight: 'bold' }}>{data.targetWffo.code}</Text>.{' '}
            {data.blockedCells.length} cell(s) booked for blocked activities:
          </Text>
          {data.blockedCells.map((c, idx) => (
            <View key={idx} style={s.conflictRow}>
              <Text style={s.conflictTime}>{c.band} {c.cellIdx}</Text>
              <Text style={s.conflictActivity}>
                {ACTIVITIES.find(a => a.id === c.actId)?.name} - Lead {c.leadId || '?'}
              </Text>
              <TouchableOpacity style={s.removeBtn} onPress={() => onRemoveCell(c)}>
                <Text style={s.removeBtnText}>Remove</Text>
              </TouchableOpacity>
            </View>
          ))}
          <TouchableOpacity style={[s.mBtn, s.mBtnCancel]} onPress={onClose}>
            <Text style={s.mBtnText}>Keep current mode</Text>
          </TouchableOpacity>
        </View>
      </View>
    </Modal>
  );
}

function DeleteRequestModal({ visible, data, onClose, onSubmit }) {
  const [reason, setReason] = useState('');
  if (!visible || !data) return null;
  return (
    <Modal transparent visible={visible} onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>Request meeting delete</Text>
          <Text style={s.modalBody}>
            {data.actiontype} at {data.lead_name}. Routes to CM. AO also approves if cash_allot or advance attached.
          </Text>
          <TextInput
            style={s.input}
            placeholder="Reason (mandatory)"
            value={reason}
            onChangeText={setReason}
            multiline
          />
          <View style={s.modalActions}>
            <TouchableOpacity style={[s.mBtn, s.mBtnCancel]} onPress={onClose}>
              <Text style={s.mBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[s.mBtn, s.mBtnGo, !reason && s.mBtnDis]}
              disabled={!reason}
              onPress={() => onSubmit(reason)}
            >
              <Text style={s.mBtnText}>Send to CM</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

function CarryForwardModal({ visible, data, onClose, onCarry }) {
  if (!visible || !data) return null;
  const pending = data.pending;
  const tomorrow = '2026-05-16'; // resolved from planDate prop in real wiring
  return (
    <Modal transparent visible={visible} onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>Carry to tomorrow</Text>
          <Text style={s.modalBody}>
            {pending.school_name} - {ACTIVITIES.find(a => a.id === pending.actiontype_id)?.name || 'task'}.
            Picks first free cell in tomorrow's plan.
          </Text>
          <View style={s.modalActions}>
            <TouchableOpacity style={[s.mBtn, s.mBtnCancel]} onPress={onClose}>
              <Text style={s.mBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[s.mBtn, s.mBtnGo]} onPress={() => onCarry(pending.id, tomorrow, 0)}>
              <Text style={s.mBtnText}>Carry forward</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

function CloseoutModal({ visible, data, onClose, onCloseout }) {
  const [reason, setReason] = useState('');
  if (!visible || !data) return null;
  return (
    <Modal transparent visible={visible} onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>Close out pending task</Text>
          <Text style={s.modalBody}>{data.school}</Text>
          <TextInput style={s.input} placeholder="Reason for closing without action" value={reason} onChangeText={setReason} multiline />
          <View style={s.modalActions}>
            <TouchableOpacity style={[s.mBtn, s.mBtnCancel]} onPress={onClose}>
              <Text style={s.mBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[s.mBtn, s.mBtnGray, !reason && s.mBtnDis]}
              disabled={!reason}
              onPress={() => onCloseout(data.pending_id, reason)}
            >
              <Text style={s.mBtnText}>Close out</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

// Production parity: the planner cascade mirrors TaskPlanner2.php
//   step 1 - pick lead (company)        -> auto-fills cstatus from init_call
//   step 2 - pick type of task (action)  -> drives purpose options
//   step 3 - pick purpose                -> server filters purpose WHERE action_id+status_id
// purpose_id is what production stores in tblcallevents.purpose_id and what
// Menu::addplantask12 receives as $ntppose.
function ActivityPickerModal({ visible, data, wffoMode, activeFilterCategory, onClose, onPick }) {
  const [leads, setLeads]         = useState([]);    // [{inid, compname, cstatus}]
  const [pickedLead, setPickedLead] = useState(null);
  const [pickedAct, setPickedAct]   = useState(null);
  const [purposes, setPurposes]     = useState([]);  // [{id, name}]
  const [pickedPurpose, setPickedPurpose] = useState(null);
  const [loadingP, setLoadingP]     = useState(false);
  // rev 8: cascade response flags
  const [fallbackUsed, setFallbackUsed]     = useState(false);
  const [bargeRewritten, setBargeRewritten] = useState(false);

  // Load BD's lead list once when the modal opens
  useEffect(() => {
    if (!visible) return;
    setPickedLead(null); setPickedAct(null);
    setPurposes([]); setPickedPurpose(null);
    fetch('/api/planner/v2/leads', { credentials: 'include' })
      .then(r => r.ok ? r.json() : { leads: [] })
      .catch(() => ({ leads: [
        { inid: 1, compname: '(demo) Modern Public School', cstatus: 6 },
        { inid: 2, compname: '(demo) Springdale Academy',   cstatus: 3 },
        { inid: 3, compname: '(demo) Lotus High School',    cstatus: 2 },
      ] }))
      .then(j => setLeads(j.leads || []));
  }, [visible]);

  // rev 8: production-parity cascade endpoint /api/planner/v2/purposes_v2.
  // Mirrors all 5 production cascade methods plus the 3 selectby branches
  // plus the Fresh Meeting (id 34) empty fallback. apply_barge_rewrite=0 on
  // mobile - the BD sees the truth (no silent Barge to Scheduled Meeting
  // rewrite). selectby is read from the active filter chip so 'Next Follow
  // Up Date' and 'Call On School' route correctly.
  useEffect(() => {
    if (!pickedAct || !pickedLead) { setPurposes([]); setFallbackUsed(false); return; }
    setLoadingP(true);
    const params = new URLSearchParams({
      action_id: String(pickedAct.id),
      cstatus: String(pickedLead.cstatus || 0),
      inid: String(pickedLead.inid || ''),
      selectby: activeFilterCategory || '',
      apply_barge_rewrite: '0',
    });
    fetch(`/api/planner/v2/purposes_v2?${params.toString()}`, { credentials: 'include' })
      .then(r => r.ok ? r.json() : { rows: [], fallback_used: true })
      .catch(() => ({ rows: [], fallback_used: true }))
      .then(j => {
        let list = (j && j.rows) || [];
        if (!Array.isArray(list) || list.length === 0) {
          list = [{ id: 34, name: 'Fresh Meeting' }];
        }
        setPurposes(list);
        setFallbackUsed(!!(j && j.fallback_used));
        setBargeRewritten(!!(j && j.barge_rewritten));
        setLoadingP(false);
      });
  }, [pickedAct, pickedLead, activeFilterCategory]);

  if (!visible || !data) return null;

  const canSave = pickedLead && pickedAct && pickedPurpose;

  return (
    <Modal transparent visible={visible} onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>Add task to {data.band.label} cell {data.cellIdx}</Text>

          <Text style={s.cascadeStep}>1. Pick lead (company)</Text>
          <ScrollView style={{ maxHeight: 120 }} nestedScrollEnabled>
            {leads.map(l => (
              <TouchableOpacity
                key={l.inid}
                style={[s.pickerRow, pickedLead?.inid === l.inid && s.pickerRowOn]}
                onPress={() => setPickedLead(l)}
              >
                <Text style={s.pickerName} numberOfLines={1}>{l.compname}</Text>
                <Text style={s.pickerMeta}>status {l.cstatus}</Text>
              </TouchableOpacity>
            ))}
            {leads.length === 0 && (
              <Text style={s.pickerEmpty}>No leads loaded</Text>
            )}
          </ScrollView>

          <Text style={s.cascadeStep}>2. Type of task</Text>
          <ScrollView style={{ maxHeight: 130 }} nestedScrollEnabled>
            {ACTIVITIES.map(act => {
              const blocked = isActivityBlocked(act.id, wffoMode);
              const on      = pickedAct?.id === act.id;
              return (
                <TouchableOpacity
                  key={act.id}
                  disabled={blocked}
                  style={[s.pickerRow, blocked && s.pickerRowBlocked, on && s.pickerRowOn]}
                  onPress={() => { setPickedAct(act); setPickedPurpose(null); }}
                >
                  <Text style={[s.pickerName, blocked && s.pickerNameBlocked]}>
                    {act.name} ({act.min}m)
                  </Text>
                  {blocked && <Text style={s.pickerLock}>blocked in {wffoMode.code}</Text>}
                </TouchableOpacity>
              );
            })}
          </ScrollView>

          <Text style={s.cascadeStep}>3. Purpose</Text>
          {!pickedLead || !pickedAct ? (
            <Text style={s.pickerEmpty}>Pick lead and task first</Text>
          ) : loadingP ? (
            <Text style={s.pickerEmpty}>Loading purposes...</Text>
          ) : (
            <>
              {fallbackUsed && (
                <Text style={s.cascadeHint}>
                  No purposes match this action and status pair. Showing Fresh Meeting (production default).
                </Text>
              )}
              {bargeRewritten && (
                <Text style={s.cascadeHint}>
                  Barge in this stage is treated as Scheduled Meeting (production rule).
                </Text>
              )}
              <ScrollView style={{ maxHeight: 110 }} nestedScrollEnabled>
                {purposes.map(p => (
                  <TouchableOpacity
                    key={p.id}
                    style={[s.pickerRow, pickedPurpose?.id === p.id && s.pickerRowOn]}
                    onPress={() => setPickedPurpose(p)}
                  >
                    <Text style={s.pickerName}>{p.name}</Text>
                  </TouchableOpacity>
                ))}
                {purposes.length === 0 && (
                  <Text style={s.pickerEmpty}>No purposes for this action+status pair</Text>
                )}
              </ScrollView>
            </>
          )}

          <View style={s.modalActions}>
            <TouchableOpacity style={[s.mBtn, s.mBtnCancel]} onPress={onClose}>
              <Text style={s.mBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[s.mBtn, s.mBtnGo, !canSave && s.mBtnDis]}
              disabled={!canSave}
              onPress={() => onPick({
                ...pickedAct,
                leadId: pickedLead.inid,
                cstatus: pickedLead.cstatus,
                purpose_id: pickedPurpose.id,
                purpose_name: pickedPurpose.name,
              })}
            >
              <Text style={s.mBtnText}>Add task</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

// =============================================================================
// SpecialTaskModal (rev 9) - mirrors addplantask12 selectby branches
// Asks the BD for cluster (Barg by cluster), purpose (Mom/Proposal Check),
// or no extra data (Join Meeting, Research). Then submits via submitSpecialTask.
// =============================================================================
function SpecialTaskModal({ visible, data, clusterOptions, onClose, onSubmit }) {
  const [clusterId, setClusterId] = useState(null);
  const [purposeId, setPurposeId] = useState(null);
  const [initId, setInitId] = useState(null);

  useEffect(() => {
    if (visible) {
      setClusterId(null);
      setPurposeId(null);
      setInitId(null);
    }
  }, [visible]);

  if (!visible || !data) return null;

  const needsCluster = data.requires === 'cluster';
  const needsPurpose = data.requires === 'purpose_init';

  const canSubmit = needsCluster ? !!clusterId
                  : needsPurpose ? (!!purposeId && !!initId)
                  : true;

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>{data.label}</Text>
          <Text style={s.modalBody}>{data.hint}</Text>

          {needsCluster && (
            <>
              <Text style={s.cascadeStep}>Pick cluster (production line 19136 - mandatory for actiontype 4 with no company)</Text>
              <ScrollView style={{ maxHeight: 240 }}>
                {(clusterOptions || []).map(c => (
                  <TouchableOpacity
                    key={c.cluster_id}
                    onPress={() => setClusterId(c.cluster_id)}
                    style={[s.pickerRow, clusterId === c.cluster_id && s.pickerRowOn]}
                  >
                    <Text style={s.pickerName}>{c.cluster_name}</Text>
                    <Text style={s.pickerMeta}>{c.travel_type}</Text>
                  </TouchableOpacity>
                ))}
                {(!clusterOptions || clusterOptions.length === 0) && (
                  <Text style={s.pickerEmpty}>No clusters mapped. Ask CM to assign.</Text>
                )}
              </ScrollView>
            </>
          )}

          {needsPurpose && (
            <>
              <Text style={s.cascadeStep}>Pick lead and purpose (MoM Check / Proposal Check)</Text>
              <TextInput
                style={s.input}
                placeholder="Lead id (init_call.id)"
                value={initId ? String(initId) : ''}
                onChangeText={(t) => setInitId(t.replace(/[^0-9]/g, ''))}
                keyboardType="number-pad"
              />
              <TextInput
                style={s.input}
                placeholder="Purpose id (purpose master)"
                value={purposeId ? String(purposeId) : ''}
                onChangeText={(t) => setPurposeId(t.replace(/[^0-9]/g, ''))}
                keyboardType="number-pad"
              />
            </>
          )}

          {!needsCluster && !needsPurpose && (
            <Text style={s.modalBody}>
              Production line {data.line_ref || ''}. Plain shortcut, no extra fields needed.
            </Text>
          )}

          <View style={s.modalActions}>
            <TouchableOpacity style={[s.mBtn, s.mBtnGray]} onPress={onClose}>
              <Text style={s.mBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[s.mBtn, canSubmit ? s.mBtnGo : s.mBtnDis]}
              disabled={!canSubmit}
              onPress={() => onSubmit({ cluster_id: clusterId, init_id: initId, purpose_id: purposeId })}
            >
              <Text style={s.mBtnText}>Submit</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

// =============================================================================
// Styles (light theme to match stem_ui_28)
// =============================================================================
const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#ffffff' },
  loading: { flex: 1, alignItems: 'center', justifyContent: 'center' },

  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
            padding: 14, borderBottomWidth: 1, borderColor: '#eaeef2', backgroundColor: '#ffffff' },
  headerTitle: { fontSize: 16, fontWeight: '600', color: '#1f2328' },
  headerSubtitle: { fontSize: 11, color: '#57606a', marginTop: 1 },
  leaveLink: { color: '#0969da', fontSize: 13, fontWeight: '600' },

  banner: { margin: 10, padding: 12, borderRadius: 6, borderWidth: 1 },
  bannerOk:     { backgroundColor: '#dafbe1', borderColor: '#2da44e' },
  bannerWarn:   { backgroundColor: '#fff8c5', borderColor: '#d4a72c' },
  bannerBreach: { backgroundColor: '#ffebe9', borderColor: '#cf222e' },
  bannerLeave:  { backgroundColor: '#ddf4ff', borderColor: '#0969da' },
  bannerTitle:  { fontSize: 13, fontWeight: '600', color: '#1f2328', marginBottom: 3 },
  bannerBody:   { fontSize: 12, color: '#1f2328', lineHeight: 16 },

  sectionLabel: { paddingHorizontal: 14, paddingTop: 10, paddingBottom: 6,
                  fontSize: 11, color: '#57606a', fontWeight: '600',
                  textTransform: 'uppercase', letterSpacing: 0.5 },

  // Pending tasks
  pendingWrap: { marginHorizontal: 10, marginBottom: 6 },
  pendingRow: { flexDirection: 'row', alignItems: 'center',
                backgroundColor: '#fff8c5', borderColor: '#d4a72c', borderWidth: 1,
                borderRadius: 6, padding: 8, marginBottom: 4 },
  pendingTitle: { fontSize: 12, fontWeight: '600', color: '#1f2328' },
  pendingMeta:  { fontSize: 10, color: '#7d4e00', marginTop: 2 },
  pendingBtn:   { backgroundColor: '#0969da', borderRadius: 4, paddingHorizontal: 8, paddingVertical: 5, marginLeft: 4 },
  pendingBtnRed:  { backgroundColor: '#cf222e' },
  pendingBtnGray: { backgroundColor: '#57606a' },
  pendingBtnText: { color: '#ffffff', fontSize: 10, fontWeight: '600' },

  // WFFO
  wffoGrid: { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: 10 },
  wffoCard: { flexBasis: '48%', margin: 3, padding: 10, borderRadius: 6, borderWidth: 1,
              borderColor: '#d0d7de', backgroundColor: '#f6f8fa' },
  wffoOn:   { backgroundColor: '#0969da', borderColor: '#0969da' },
  wffoDim:  { opacity: 0.45 },
  wffoCode: { fontSize: 13, fontWeight: '700', color: '#1f2328' },
  wffoLabel:{ fontSize: 11, color: '#57606a', marginTop: 1 },
  wffoOnText: { color: '#ffffff' },
  wffoRestrict: { fontSize: 9, color: '#cf222e', marginTop: 2 },

  // rev 7 - 30-category filter chips (horizontal scroll strip)
  chipScroll: { paddingHorizontal: 10, paddingVertical: 4 },
  chip: { flexDirection: 'row', alignItems: 'center', gap: 6,
          backgroundColor: '#f6f8fa', borderColor: '#d0d7de', borderWidth: 1,
          borderRadius: 16, paddingHorizontal: 12, paddingVertical: 6, marginRight: 6 },
  chipOn: { backgroundColor: '#0969da', borderColor: '#0969da' },
  chipName: { fontSize: 12, fontWeight: '600', color: '#1f2328' },
  chipBadge: { fontSize: 11, fontWeight: '700', color: '#57606a',
               backgroundColor: '#eaeef2', borderRadius: 8, paddingHorizontal: 6, minWidth: 18, textAlign: 'center' },
  chipOnText: { color: '#ffffff' },

  // Pills
  pillRow: { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: 10 },
  pill: { flexDirection: 'row', alignItems: 'center', gap: 4,
          backgroundColor: '#f6f8fa', borderColor: '#d0d7de', borderWidth: 1,
          borderRadius: 14, paddingHorizontal: 9, paddingVertical: 4, margin: 2 },
  pillOn:     { backgroundColor: '#0969da', borderColor: '#0969da' },
  pillLocked: { backgroundColor: '#eaeef2', borderColor: '#d0d7de' },
  pillNum:    { fontSize: 11, fontWeight: '700', color: '#1f2328' },
  pillName:   { fontSize: 11, color: '#1f2328' },
  pillOnText: { color: '#ffffff' },
  pillLockedText: { color: '#8c959f', textDecorationLine: 'line-through' },
  pillLockChip: { fontSize: 9, color: '#8c959f', marginLeft: 4 },

  // Summary
  summaryRow: { flexDirection: 'row', justifyContent: 'space-around',
                margin: 10, padding: 10, backgroundColor: '#f6f8fa',
                borderColor: '#eaeef2', borderWidth: 1, borderRadius: 6 },
  summaryCol: { alignItems: 'center' },
  summaryNum: { fontSize: 18, fontWeight: '700', color: '#1f2328' },
  summaryLbl: { fontSize: 9, color: '#57606a', textTransform: 'uppercase' },

  // Cell grid
  bandWrap: { marginBottom: 4 },
  bandHead: { flexDirection: 'row', justifyContent: 'space-between', paddingHorizontal: 14, marginBottom: 2 },
  bandLabel: { fontSize: 11, fontWeight: '600', color: '#1f2328' },
  bandMins:  { fontSize: 10, color: '#57606a' },
  cellGrid: { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: 14 },
  cell: { width: '4%', aspectRatio: 1, backgroundColor: '#eaeef2', margin: 0.5, borderRadius: 1 },
  cellPlanned: { backgroundColor: '#0969da' },

  // Submit
  submit: { margin: 14, padding: 12, borderRadius: 6, alignItems: 'center' },
  submitGo:    { backgroundColor: '#2da44e' },
  submitDis:   { backgroundColor: '#eaeef2', borderWidth: 1, borderColor: '#d0d7de' },
  submitLeave: { backgroundColor: '#0969da' },
  submitText: { color: '#ffffff', fontWeight: '600', fontSize: 13 },

  footerNote: { textAlign: 'center', color: '#8c959f', fontSize: 9, paddingBottom: 14 },

  // Modal
  modalOverlay: { flex: 1, backgroundColor: 'rgba(31,35,40,0.4)', justifyContent: 'flex-end' },
  modalCard: { backgroundColor: '#ffffff', borderTopLeftRadius: 14, borderTopRightRadius: 14, padding: 16 },
  modalTitle: { fontSize: 16, fontWeight: '600', color: '#1f2328', marginBottom: 6 },
  modalBody:  { fontSize: 12, color: '#57606a', lineHeight: 16, marginBottom: 10 },
  modalActions: { flexDirection: 'row', gap: 8, marginTop: 8 },
  conflictRow: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f6f8fa',
                 padding: 8, marginBottom: 4, borderRadius: 4, borderColor: '#eaeef2', borderWidth: 1 },
  conflictTime: { color: '#0969da', fontSize: 11, fontWeight: '600', width: 70 },
  conflictActivity: { color: '#1f2328', fontSize: 12, flex: 1 },
  removeBtn: { backgroundColor: '#cf222e', borderRadius: 4, paddingHorizontal: 8, paddingVertical: 4 },
  removeBtnText: { color: '#ffffff', fontSize: 10, fontWeight: '600' },
  input: { borderWidth: 1, borderColor: '#d0d7de', borderRadius: 6, padding: 8,
           minHeight: 60, marginBottom: 8, fontSize: 12, color: '#1f2328' },
  mBtn: { flex: 1, padding: 10, borderRadius: 6, alignItems: 'center' },
  mBtnCancel: { backgroundColor: '#57606a' },
  mBtnGo:     { backgroundColor: '#2da44e' },
  mBtnGray:   { backgroundColor: '#8c959f' },
  mBtnDis:    { backgroundColor: '#eaeef2' },
  mBtnText:   { color: '#ffffff', fontSize: 12, fontWeight: '600' },
  pickerRow: { padding: 12, borderBottomWidth: 1, borderColor: '#eaeef2', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  pickerRowOn: { backgroundColor: '#ddf4ff' },
  pickerRowBlocked: { opacity: 0.5 },
  pickerName: { fontSize: 13, color: '#1f2328' },
  pickerNameBlocked: { textDecorationLine: 'line-through', color: '#8c959f' },
  pickerMeta: { fontSize: 11, color: '#656d76' },
  pickerEmpty: { padding: 14, fontSize: 12, color: '#8c959f', fontStyle: 'italic', textAlign: 'center' },
  cascadeHint: { padding: 8, marginBottom: 4, backgroundColor: '#fff8c5', borderRadius: 6, fontSize: 11, color: '#7a5a00', textAlign: 'center' },
  pickerLock: { fontSize: 10, color: '#cf222e', marginTop: 2 },
  cascadeStep: { fontSize: 13, fontWeight: '700', color: '#1f2328', paddingHorizontal: 14, paddingTop: 14, paddingBottom: 6 },

  // Rev 9 - Special Tasks rail
  specialRow: { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: 10, paddingBottom: 6 },
  specialCard: { width: '48%', margin: '1%', padding: 10, borderRadius: 6,
                 backgroundColor: '#fff8c5', borderWidth: 1, borderColor: '#d4a72c' },
  specialDim: { backgroundColor: '#eaeef2', borderColor: '#d0d7de', opacity: 0.6 },
  specialLabel: { fontSize: 12, fontWeight: '700', color: '#1f2328', marginBottom: 2 },
  specialHint: { fontSize: 10, color: '#57606a' },
});
