// LeadProgressionForceScreen.js
// Migration 016 + 016_2 surface.
//
// What this screen does:
//   1. SLA banner: shows lead state (soft_warn / hard_block / cm_escalated
//      / rm_escalated / director_escalated) with days-in-stage and the
//      012 threshold breached.
//   2. Manager actions row: Engaged, Reassign, Pause.  Kill is REMOVED.
//      Mark Lost is a two-step flow (raise -> approver -> 24h cooling).
//   3. Force Placement panel: manager picks an activity type, screen
//      calls /find_open_slot to surface the first contiguous free run
//      of 5-minute cells, then posts /force_placement which writes the
//      tblcallevents row + paints the cells + audits.
//   4. Slot grid panel: 6 bands (S1..S6) each rendered as 24 5-min
//      cells, free vs booked vs committed vs released, color-coded.
//      Tapping a free cell pre-fills the placement form with that
//      cell_start as earliest_time.
//   5. Mark Lost modal: BD or CM raises with reason_code + note >= 20
//      chars + optional evidence_event_id; approver (RM/Director) sees
//      it in their queue, approves or rejects; 24h cooling banner with
//      countdown and Reverse button for the BD.
//   6. Accountability feed: severity-colored timeline of every gate
//      decision, mark-lost step, force placement, SLA breach.

import React, {useEffect, useState, useMemo} from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, TextInput, Modal,
  StyleSheet, ActivityIndicator, RefreshControl, Alert,
} from 'react-native';

const API = 'https://stemapp.in/api/progression_compulsion_v2';
const API_016 = 'https://stemapp.in/api/progression_compulsion';
const TOKEN_KEY = 'STEM_DIGEST_TOKEN';

const REASON_CODES = [
  {code: 'budget_dropped',          label: 'Budget dropped'},
  {code: 'competitor_won',          label: 'Competitor won'},
  {code: 'school_closed',           label: 'School closed'},
  {code: 'no_response_60d',         label: 'No response 60 days'},
  {code: 'wrong_segment',           label: 'Wrong segment'},
  {code: 'duplicate_lead',          label: 'Duplicate lead'},
  {code: 'school_decision_against', label: 'School decision against'},
  {code: 'other',                   label: 'Other'},
];

const ACTIONS = [
  {actiontype_id: 1,  label: 'Call',          purpose_id: 1},
  {actiontype_id: 3,  label: 'Meeting',       purpose_id: 3},
  {actiontype_id: 4,  label: 'Barge meeting', purpose_id: 66},
  {actiontype_id: 7,  label: 'WhatsApp',      purpose_id: 7},
  {actiontype_id: 10, label: 'Research',      purpose_id: 94},
];

const STATE_COLORS = {
  free:      '#e8f0ea',
  planned:   '#f4d35e',
  committed: '#2e8b57',
  released:  '#bdbdbd',
};

export default function LeadProgressionForceScreen({route, navigation}) {
  const {lead_id, bd_uid, bd_name, viewer_uid, viewer_role, token} = route.params || {};

  const [sla, setSla]               = useState(null);
  const [slots, setSlots]           = useState([]);
  const [cells, setCells]           = useState([]);
  const [feed, setFeed]             = useState([]);
  const [mlqueue, setMlqueue]       = useState([]);
  const [planDate, setPlanDate]     = useState(tomorrowISO());
  const [loading, setLoading]       = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [showMl, setShowMl]         = useState(false);
  const [pickedCell, setPickedCell] = useState(null);  // {cell_no, cell_start, slot_no}
  const [pickedAction, setPickedAction] = useState(ACTIONS[0]);

  // ----- fetch -----
  const headers = useMemo(() => ({
    'Authorization': 'Bearer ' + token,
    'Content-Type':  'application/json',
  }), [token]);

  async function loadAll() {
    try {
      const [s1, s2, s3, s4, s5] = await Promise.all([
        fetch(`${API_016}/lead_sla?lead_id=${lead_id}`, {headers}).then(r => r.json()).catch(() => null),
        fetch(`${API}/slot_status?uid=${bd_uid}&date=${planDate}`, {headers}).then(r => r.json()).catch(() => ({slots: []})),
        fetch(`${API}/cell_grid?uid=${bd_uid}&date=${planDate}`,  {headers}).then(r => r.json()).catch(() => ({busy: []})),
        fetch(`${API}/accountability_feed?uid=${bd_uid}&days=7`,  {headers}).then(r => r.json()).catch(() => []),
        fetch(`${API}/mark_lost_queue?role=${viewer_role}&uid=${viewer_uid}`, {headers}).then(r => r.json()).catch(() => []),
      ]);
      setSla(s1);
      setSlots(s2.slots || []);
      setCells(buildFullGrid(s3.busy || []));
      setFeed(s4 || []);
      setMlqueue(s5 || []);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }

  useEffect(() => { loadAll(); /* eslint-disable-next-line */ }, [planDate]);

  // ----- mark lost flow -----
  const [mlReason, setMlReason] = useState(REASON_CODES[0].code);
  const [mlNote, setMlNote]     = useState('');

  async function submitMarkLost() {
    if (mlNote.trim().length < 20) {
      return Alert.alert('Reason too short', 'Note must be at least 20 characters.');
    }
    const body = {
      lead_id, bd_uid,
      requested_by_uid: viewer_uid,
      requested_by_role: viewer_role,  // bd or cm
      reason_code: mlReason,
      reason_note: mlNote.trim(),
    };
    const r = await fetch(`${API}/mark_lost_request`, {method: 'POST', headers, body: JSON.stringify(body)});
    const j = await r.json();
    if (!j.ok) return Alert.alert('Mark Lost blocked', j.reason || 'unknown');
    setShowMl(false); setMlNote('');
    Alert.alert('Mark Lost raised', 'Awaiting RM or Director approval. 24h cooling kicks in once approved.');
    loadAll();
  }

  async function approveMl(request_id) {
    const r = await fetch(`${API}/mark_lost_approve`, {
      method: 'POST', headers,
      body: JSON.stringify({request_id, approver_uid: viewer_uid, approver_role: viewer_role}),
    });
    const j = await r.json();
    if (!j.ok) return Alert.alert('Approve blocked', j.reason);
    Alert.alert('Approved', 'Cooling ends ' + j.cooling_ends_at + '. Reverse window open.');
    loadAll();
  }
  async function rejectMl(request_id) {
    const r = await fetch(`${API}/mark_lost_reject`, {
      method: 'POST', headers,
      body: JSON.stringify({request_id, approver_uid: viewer_uid, approver_role: viewer_role, note: 'rejected from app'}),
    });
    const j = await r.json();
    if (!j.ok) return Alert.alert('Reject blocked', j.reason);
    loadAll();
  }
  async function reverseMl(request_id) {
    const r = await fetch(`${API}/mark_lost_reverse`, {
      method: 'POST', headers,
      body: JSON.stringify({request_id, actor_uid: viewer_uid, actor_role: viewer_role, note: 'reversed within cooling'}),
    });
    const j = await r.json();
    if (!j.ok) return Alert.alert('Reverse blocked', j.reason);
    loadAll();
  }

  // ----- force placement -----
  async function forcePlace() {
    if (!['cm','rm','director','ai_compulsion'].includes(viewer_role)) {
      return Alert.alert('Not permitted', 'Only CM, RM, Director can force-place.');
    }
    const body = {
      bd_uid, lead_id,
      actor_uid: viewer_uid, actor_role: viewer_role,
      plan_date: planDate,
      actiontype_id: pickedAction.actiontype_id,
      purpose_id:    pickedAction.purpose_id,
      earliest_time: pickedCell ? pickedCell.cell_start : null,
      note: 'forced by ' + viewer_role + ' from mobile',
    };
    const r = await fetch(`${API}/force_placement`, {method: 'POST', headers, body: JSON.stringify(body)});
    const j = await r.json();
    if (!j.ok) return Alert.alert('Placement blocked', j.reason);
    Alert.alert('Placed',
      `Slot S${j.slot_no} starting ${j.cell_start}. ${j.minutes} minutes booked on BD calendar.`);
    setPickedCell(null);
    loadAll();
  }

  // ----- render -----
  if (loading) {
    return <View style={s.center}><ActivityIndicator /></View>;
  }

  return (
    <ScrollView style={s.scr}
      refreshControl={<RefreshControl refreshing={refreshing}
        onRefresh={() => { setRefreshing(true); loadAll(); }} />}>

      {/* Header */}
      <View style={s.header}>
        <Text style={s.h1}>Force progression  -  Lead {lead_id}</Text>
        <Text style={s.h2}>BD {bd_name}  -  Plan date {planDate}</Text>
      </View>

      {/* SLA banner */}
      <SlaBanner sla={sla} />

      {/* Actions row -- NO KILL */}
      <View style={s.actions}>
        <ActionBtn label="Engaged"   color="#2e8b57" onPress={() => Alert.alert('Engaged','use the 016 manager_action endpoint')} />
        <ActionBtn label="Reassign"  color="#3a7bd5" onPress={() => Alert.alert('Reassign','use the 016 manager_action endpoint')} />
        <ActionBtn label="Pause"     color="#b8860b" onPress={() => Alert.alert('Pause','use the 016 manager_action endpoint')} />
        <ActionBtn label="Mark Lost" color="#c0392b" onPress={() => setShowMl(true)} />
      </View>
      <Text style={s.helper}>Kill is removed. Lost is two-step  -  raise then approver then 24 hours cooling.</Text>

      {/* Slot grid panel  -- 6 bands of 24 cells each */}
      <Text style={s.section}>Next day calendar  -  6 bands of 24 cells (5 min each)</Text>
      {[1,2,3,4,5,6].map(sno => {
        const band  = slots.find(x => Number(x.slot_no) === sno);
        const cellsInBand = cells.filter(c => c.slot_no === sno);
        return <SlotBand key={sno} band={band} cells={cellsInBand}
                         onCellPick={(c) => setPickedCell(c)}
                         pickedCellNo={pickedCell ? pickedCell.cell_no : -1} />;
      })}

      {/* Force placement form */}
      {['cm','rm','director','ai_compulsion'].includes(viewer_role) && (
        <View style={s.placebox}>
          <Text style={s.section}>Force placement on BD calendar</Text>
          <Text style={s.helper}>
            Picked start: {pickedCell ? `S${pickedCell.slot_no} ${pickedCell.cell_start}` : 'first free run will be auto-picked'}
          </Text>
          <View style={s.actrow}>
            {ACTIONS.map(a => (
              <TouchableOpacity key={a.actiontype_id}
                style={[s.actpill, pickedAction.actiontype_id === a.actiontype_id && s.actpillSel]}
                onPress={() => setPickedAction(a)}>
                <Text style={[s.actpilltxt, pickedAction.actiontype_id === a.actiontype_id && {color: '#fff'}]}>
                  {a.label}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
          <TouchableOpacity style={s.bigBtn} onPress={forcePlace}>
            <Text style={s.bigBtnTxt}>Book on BD calendar</Text>
          </TouchableOpacity>
        </View>
      )}

      {/* Mark Lost queue panel  -- approver view */}
      {['rm','director'].includes(viewer_role) && mlqueue.length > 0 && (
        <View style={s.queue}>
          <Text style={s.section}>Mark Lost queue  -  approver view</Text>
          {mlqueue.map(q => (
            <View key={q.id} style={s.mlcard}>
              <Text style={s.mlhead}>Req {q.id}  -  Lead {q.lead_id}  -  BD {q.bd_uid}</Text>
              <Text style={s.mlbody}>{q.reason_code}  -  {q.reason_note}</Text>
              <Text style={s.mlmeta}>state {q.status}{q.cooling_ends_at ? '  cooling ends '+q.cooling_ends_at : ''}</Text>
              {q.status === 'awaiting_approver' && (
                <View style={s.mlbtns}>
                  <TouchableOpacity style={[s.smbtn,{backgroundColor:'#2e8b57'}]} onPress={() => approveMl(q.id)}>
                    <Text style={s.smbtnTxt}>Approve</Text></TouchableOpacity>
                  <TouchableOpacity style={[s.smbtn,{backgroundColor:'#c0392b'}]} onPress={() => rejectMl(q.id)}>
                    <Text style={s.smbtnTxt}>Reject</Text></TouchableOpacity>
                </View>
              )}
              {q.status === 'approved_cooling' && viewer_role === 'bd' && (
                <TouchableOpacity style={[s.smbtn,{backgroundColor:'#b8860b'}]} onPress={() => reverseMl(q.id)}>
                  <Text style={s.smbtnTxt}>Reverse within cooling</Text></TouchableOpacity>
              )}
            </View>
          ))}
        </View>
      )}

      {/* Accountability feed */}
      <Text style={s.section}>Accountability feed  -  last 7 days</Text>
      {feed.length === 0 && <Text style={s.helper}>No accountability events.</Text>}
      {feed.map(f => (
        <View key={f.id} style={[s.feedrow,
          f.severity === 'red'  && {borderLeftColor:'#c0392b'},
          f.severity === 'warn' && {borderLeftColor:'#b8860b'},
          f.severity === 'info' && {borderLeftColor:'#2e8b57'}]}>
          <Text style={s.feedhead}>{f.event_type}  -  {f.actor_role}</Text>
          <Text style={s.feedbody}>{f.message}</Text>
          <Text style={s.feedmeta}>{f.created_at}</Text>
        </View>
      ))}

      {/* Mark Lost modal */}
      <Modal visible={showMl} animationType="slide" transparent>
        <View style={s.modalBg}>
          <View style={s.modal}>
            <Text style={s.h1}>Raise Mark Lost</Text>
            <Text style={s.helper}>Pick a reason code. Write at least 20 characters of context. Approver gets it in their queue. 24 hours cooling after approval.</Text>
            <View style={s.actrow}>
              {REASON_CODES.map(r => (
                <TouchableOpacity key={r.code}
                  style={[s.actpill, mlReason === r.code && s.actpillSel]}
                  onPress={() => setMlReason(r.code)}>
                  <Text style={[s.actpilltxt, mlReason === r.code && {color:'#fff'}]}>{r.label}</Text>
                </TouchableOpacity>
              ))}
            </View>
            <TextInput style={s.input} placeholder="Reason note (min 20 chars)"
              multiline value={mlNote} onChangeText={setMlNote} />
            <View style={s.actrow}>
              <TouchableOpacity style={[s.bigBtn,{flex:1,backgroundColor:'#888'}]} onPress={() => setShowMl(false)}>
                <Text style={s.bigBtnTxt}>Cancel</Text></TouchableOpacity>
              <TouchableOpacity style={[s.bigBtn,{flex:1}]} onPress={submitMarkLost}>
                <Text style={s.bigBtnTxt}>Raise request</Text></TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </ScrollView>
  );
}

// =====================================================================
// helpers
// =====================================================================

function tomorrowISO() {
  const d = new Date(); d.setDate(d.getDate() + 1);
  return d.toISOString().slice(0,10);
}

function buildFullGrid(busy) {
  // Returns a 144-cell array. Free unless busy[] has an entry.
  const byCell = {};
  (busy || []).forEach(b => { byCell[b.cell_no] = b; });
  const out = [];
  for (let c = 0; c < 144; c++) {
    const slot_no = Math.floor(c / 24) + 1;
    const minutes_from_window = c * 5;
    const hh = String(9 + Math.floor(minutes_from_window / 60)).padStart(2,'0');
    const mm = String(minutes_from_window % 60).padStart(2,'0');
    const cell_start = `${hh}:${mm}:00`;
    out.push(byCell[c] ? {...byCell[c], slot_no, cell_no: c, cell_start}
                       : {cell_no: c, slot_no, cell_start, state: 'free'});
  }
  return out;
}

function SlotBand({band, cells, onCellPick, pickedCellNo}) {
  const state = band ? band.state : 'empty';
  const free  = band ? band.free_cells : 24;
  return (
    <View style={s.band}>
      <View style={s.bandhead}>
        <Text style={s.bandTitle}>
          S{cells[0]?.slot_no}  {cells[0]?.cell_start?.slice(0,5)} to {addMin(cells[0]?.cell_start, 120)?.slice(0,5)}
        </Text>
        <Text style={s.bandMeta}>{free} of 24 cells free  -  {state}</Text>
      </View>
      <View style={s.cellrow}>
        {cells.map(c => (
          <TouchableOpacity key={c.cell_no}
            disabled={c.state !== 'free'}
            onPress={() => onCellPick(c)}
            style={[s.cell,
              {backgroundColor: STATE_COLORS[c.state] || '#eee'},
              pickedCellNo === c.cell_no && s.cellPicked,
              c.forced_by_role && s.cellForced]}>
            <Text style={s.cellTxt}>{c.cell_start.slice(3,5)}</Text>
          </TouchableOpacity>
        ))}
      </View>
    </View>
  );
}

function addMin(hhmmss, mins) {
  if (!hhmmss) return '';
  const [h,m] = hhmmss.split(':').map(Number);
  const total = h*60 + m + mins;
  const H = String(Math.floor(total/60)).padStart(2,'0');
  const M = String(total%60).padStart(2,'0');
  return `${H}:${M}:00`;
}

function SlaBanner({sla}) {
  if (!sla || !sla.sla_state) {
    return <View style={[s.sla,{backgroundColor:'#e8f0ea'}]}>
      <Text style={s.slaTxt}>No SLA breach. Lead is within stage thresholds.</Text></View>;
  }
  const colorMap = {
    soft_warn:'#f4d35e', hard_block:'#c0392b',
    cm_escalated:'#c0392b', rm_escalated:'#9b1b1b', director_escalated:'#7d0d0d',
  };
  const bg = colorMap[sla.sla_state] || '#bdbdbd';
  return (
    <View style={[s.sla,{backgroundColor: bg}]}>
      <Text style={s.slaTxt}>SLA  -  {sla.sla_state}  -  {sla.days_in_stage} days in cstatus {sla.current_cstatus}</Text>
      {sla.recommended_action && <Text style={s.slaTxt}>Recommended  -  {sla.recommended_action}</Text>}
    </View>
  );
}

function ActionBtn({label, color, onPress}) {
  return (
    <TouchableOpacity style={[s.actbtn,{backgroundColor: color}]} onPress={onPress}>
      <Text style={s.actbtnTxt}>{label}</Text>
    </TouchableOpacity>
  );
}

// =====================================================================
const s = StyleSheet.create({
  scr:        {flex:1, backgroundColor:'#f7f7f5'},
  center:     {flex:1, alignItems:'center', justifyContent:'center'},
  header:     {padding:14, backgroundColor:'#1f2c3a'},
  h1:         {color:'#fff', fontSize:18, fontWeight:'700'},
  h2:         {color:'#cfd6dc', fontSize:13, marginTop:2},
  section:    {fontSize:15, fontWeight:'700', color:'#1f2c3a', marginTop:14, marginHorizontal:14, marginBottom:6},
  helper:     {fontSize:12, color:'#666', marginHorizontal:14, marginBottom:6, fontStyle:'italic'},

  sla:        {margin:14, padding:10, borderRadius:6},
  slaTxt:     {color:'#fff', fontWeight:'600', fontSize:13},

  actions:    {flexDirection:'row', flexWrap:'wrap', paddingHorizontal:10, marginTop:8},
  actbtn:     {flex:1, margin:4, paddingVertical:11, borderRadius:6, alignItems:'center', minWidth:80},
  actbtnTxt:  {color:'#fff', fontWeight:'700', fontSize:13},

  band:       {marginHorizontal:14, marginBottom:8, backgroundColor:'#fff', borderRadius:6, padding:8,
               borderWidth:1, borderColor:'#e7e4dd'},
  bandhead:   {flexDirection:'row', justifyContent:'space-between', marginBottom:4},
  bandTitle:  {fontWeight:'700', fontSize:13, color:'#1f2c3a'},
  bandMeta:   {fontSize:11, color:'#666'},
  cellrow:    {flexDirection:'row', flexWrap:'wrap'},
  cell:       {width:'8%', aspectRatio:1, margin:1, alignItems:'center', justifyContent:'center', borderRadius:2},
  cellPicked: {borderWidth:2, borderColor:'#1f2c3a'},
  cellForced: {borderWidth:1, borderColor:'#9b59b6', borderStyle:'dashed'},
  cellTxt:    {fontSize:8, color:'#1f2c3a'},

  placebox:   {margin:14, padding:10, backgroundColor:'#fff', borderRadius:6,
               borderWidth:1, borderColor:'#e7e4dd'},
  actrow:     {flexDirection:'row', flexWrap:'wrap', marginVertical:6},
  actpill:    {paddingVertical:6, paddingHorizontal:10, borderRadius:14, backgroundColor:'#eee', marginRight:6, marginBottom:6},
  actpillSel: {backgroundColor:'#1f2c3a'},
  actpilltxt: {fontSize:12, color:'#1f2c3a'},
  bigBtn:     {backgroundColor:'#1f2c3a', padding:12, borderRadius:6, alignItems:'center', marginTop:6, marginHorizontal:4},
  bigBtnTxt:  {color:'#fff', fontWeight:'700'},

  queue:      {margin:14, padding:10, backgroundColor:'#fff', borderRadius:6, borderWidth:1, borderColor:'#e7e4dd'},
  mlcard:     {padding:8, marginVertical:6, borderRadius:4, backgroundColor:'#fff7e6', borderLeftWidth:3, borderLeftColor:'#b8860b'},
  mlhead:     {fontWeight:'700', fontSize:13, color:'#1f2c3a'},
  mlbody:     {fontSize:12, color:'#333', marginVertical:4},
  mlmeta:     {fontSize:11, color:'#666'},
  mlbtns:     {flexDirection:'row', marginTop:6},
  smbtn:      {flex:1, padding:8, borderRadius:4, alignItems:'center', margin:3},
  smbtnTxt:   {color:'#fff', fontWeight:'700', fontSize:12},

  feedrow:    {marginHorizontal:14, marginVertical:3, padding:8, backgroundColor:'#fff',
               borderRadius:4, borderLeftWidth:3, borderLeftColor:'#bdbdbd'},
  feedhead:   {fontWeight:'700', fontSize:12, color:'#1f2c3a'},
  feedbody:   {fontSize:12, color:'#333', marginTop:2},
  feedmeta:   {fontSize:10, color:'#888', marginTop:2},

  modalBg:    {flex:1, backgroundColor:'rgba(0,0,0,0.4)', justifyContent:'center', padding:14},
  modal:      {backgroundColor:'#fff', borderRadius:8, padding:14},
  input:      {borderWidth:1, borderColor:'#ccc', borderRadius:6, padding:8, minHeight:70, marginVertical:8, textAlignVertical:'top'},
});
