// LeaveRequestScreen.js
// BD: apply for a leave + view my leaves
// CM: pending approvals + 14-day team calendar
// Endpoints (migration 017 + LeaveController):
//   POST /api/leave/apply
//   POST /api/leave/decide
//   POST /api/leave/cancel
//   GET  /api/leave/my?bd_uid=&days=
//   GET  /api/leave/team_pending?cm_uid=
//   GET  /api/leave/team_calendar?cm_uid=&days=

import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity, TextInput, Modal,
  ActivityIndicator, RefreshControl, Alert,
} from 'react-native';
import { useAuth } from '../context/AuthContext';
import { apiGet, apiPost } from '../services/api';

const LEAVE_TYPES = [
  { code: 'casual',        label: 'Casual'        },
  { code: 'sick',          label: 'Sick'          },
  { code: 'planned',       label: 'Planned'       },
  { code: 'emergency',     label: 'Emergency'     },
  { code: 'wfh_to_leave',  label: 'WFH to Leave'  },
];

const STATUS_COLOR = {
  pending:   '#f0ad4e',
  approved:  '#28a745',
  rejected:  '#dc3545',
  cancelled: '#6c757d',
};

export default function LeaveRequestScreen({ navigation }) {
  const { user } = useAuth();
  const isCM = user?.type_id === 13;

  const [tab, setTab] = useState(isCM ? 'pending' : 'mine');
  const [loading, setLoading] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [mine, setMine] = useState([]);
  const [pending, setPending] = useState([]);
  const [calendar, setCalendar] = useState([]);

  // Apply modal state
  const [applyOpen, setApplyOpen] = useState(false);
  const [applyDate, setApplyDate] = useState(tomorrowIso());
  const [applyType, setApplyType] = useState('casual');
  const [applyReason, setApplyReason] = useState('');

  // Decide modal state
  const [decideRow, setDecideRow] = useState(null);
  const [decideRemarks, setDecideRemarks] = useState('');

  const load = useCallback(async () => {
    setLoading(true);
    try {
      if (isCM) {
        const pr = await apiGet(`/api/leave/team_pending?cm_uid=${user.uid}`);
        setPending(pr?.pending || []);
        const cal = await apiGet(`/api/leave/team_calendar?cm_uid=${user.uid}&days=14`);
        setCalendar(cal?.calendar || []);
      } else {
        const my = await apiGet(`/api/leave/my?bd_uid=${user.uid}&days=60`);
        setMine(my?.leaves || []);
      }
    } catch (e) {
      Alert.alert('Load failed', String(e));
    }
    setLoading(false);
  }, [isCM, user?.uid]);

  useEffect(() => { load(); }, [load]);

  const onRefresh = async () => {
    setRefreshing(true);
    await load();
    setRefreshing(false);
  };

  const submitApply = async () => {
    if (!applyReason.trim()) {
      Alert.alert('Reason needed', 'Please type a reason for the leave.');
      return;
    }
    const cm_uid = user?.cm_uid || user?.manager_uid;
    if (!cm_uid) {
      Alert.alert('CM missing', 'Cannot identify your Cluster Manager.');
      return;
    }
    const r = await apiPost('/api/leave/apply', {
      bd_uid: user.uid, cm_uid,
      leave_date: applyDate,
      leave_type: applyType,
      reason: applyReason.trim(),
    });
    if (!r?.ok) { Alert.alert('Apply failed', r?.error || 'Unknown'); return; }
    setApplyOpen(false);
    setApplyReason('');
    await load();
    Alert.alert('Applied', 'Leave sent to CM for approval.');
  };

  const submitDecide = async (action) => {
    if (!decideRow) return;
    const r = await apiPost('/api/leave/decide', {
      leave_id: decideRow.leave_id,
      decided_by_uid: user.uid,
      action,
      remarks: decideRemarks.trim(),
    });
    if (!r?.ok) { Alert.alert('Decide failed', r?.error || 'Unknown'); return; }
    setDecideRow(null);
    setDecideRemarks('');
    await load();
  };

  const cancelLeave = async (row) => {
    Alert.alert('Cancel leave?', `Cancel pending leave for ${row.leave_date}?`, [
      { text: 'No' },
      { text: 'Yes', onPress: async () => {
          const r = await apiPost('/api/leave/cancel', { leave_id: row.leave_id, bd_uid: user.uid });
          if (!r?.ok) Alert.alert('Cancel failed', r?.error || 'Unknown');
          await load();
      }},
    ]);
  };

  return (
    <View style={s.root}>
      <View style={s.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={s.back}>{'<'} Back</Text>
        </TouchableOpacity>
        <Text style={s.title}>Leave</Text>
        {!isCM && (
          <TouchableOpacity style={s.applyBtn} onPress={() => setApplyOpen(true)}>
            <Text style={s.applyBtnText}>+ Apply</Text>
          </TouchableOpacity>
        )}
      </View>

      {/* Tabs */}
      <View style={s.tabs}>
        {isCM ? (
          <>
            <Tab label="Pending"  active={tab==='pending'}  onPress={() => setTab('pending')} />
            <Tab label="Calendar" active={tab==='calendar'} onPress={() => setTab('calendar')} />
          </>
        ) : (
          <Tab label="My Leaves" active={tab==='mine'} onPress={() => setTab('mine')} />
        )}
      </View>

      {loading ? <ActivityIndicator style={{marginTop: 24}} /> : (
        <ScrollView
          style={{flex:1}}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        >
          {!isCM && tab === 'mine' && (
            <View style={s.section}>
              {mine.length === 0 && <Text style={s.empty}>No leaves applied in the last 60 days.</Text>}
              {mine.map(row => (
                <LeaveRow
                  key={row.leave_id}
                  row={row}
                  showCancel={row.status === 'pending'}
                  onCancel={() => cancelLeave(row)}
                />
              ))}
            </View>
          )}

          {isCM && tab === 'pending' && (
            <View style={s.section}>
              {pending.length === 0 && <Text style={s.empty}>No leaves awaiting your approval.</Text>}
              {pending.map(row => (
                <View key={row.leave_id} style={s.card}>
                  <View style={s.cardHead}>
                    <Text style={s.bdName}>{row.bd_name}</Text>
                    <Text style={s.dateBadge}>{row.leave_date}</Text>
                  </View>
                  <Text style={s.reason}>{row.leave_type.toUpperCase()}: {row.reason}</Text>
                  <Text style={s.meta}>Applied {row.applied_at}</Text>
                  <View style={s.cardActions}>
                    <TouchableOpacity style={[s.btn, s.btnApprove]}
                      onPress={() => { setDecideRow(row); setDecideRemarks(''); }}>
                      <Text style={s.btnText}>Approve</Text>
                    </TouchableOpacity>
                    <TouchableOpacity style={[s.btn, s.btnReject]}
                      onPress={() => { setDecideRow({...row, _reject:true}); setDecideRemarks(''); }}>
                      <Text style={s.btnText}>Reject</Text>
                    </TouchableOpacity>
                  </View>
                </View>
              ))}
            </View>
          )}

          {isCM && tab === 'calendar' && (
            <View style={s.section}>
              {calendar.length === 0 && <Text style={s.empty}>No leaves in next 14 days.</Text>}
              {calendar.map(row => (
                <View key={row.leave_id} style={s.calRow}>
                  <Text style={s.calDate}>{row.leave_date}</Text>
                  <Text style={s.calBd}>{row.bd_name}</Text>
                  <Text style={[s.calStatus, {backgroundColor: STATUS_COLOR[row.status]}]}>
                    {row.status}
                  </Text>
                </View>
              ))}
            </View>
          )}
        </ScrollView>
      )}

      {/* Apply modal */}
      <Modal visible={applyOpen} transparent animationType="slide">
        <View style={s.modalBackdrop}>
          <View style={s.modalCard}>
            <Text style={s.modalTitle}>Apply for Leave</Text>

            <Text style={s.label}>Date</Text>
            <TextInput style={s.input} value={applyDate} onChangeText={setApplyDate}
              placeholder="YYYY-MM-DD" />

            <Text style={s.label}>Type</Text>
            <View style={s.pillRow}>
              {LEAVE_TYPES.map(t => (
                <TouchableOpacity key={t.code}
                  style={[s.pill, applyType===t.code && s.pillOn]}
                  onPress={() => setApplyType(t.code)}>
                  <Text style={[s.pillText, applyType===t.code && s.pillTextOn]}>{t.label}</Text>
                </TouchableOpacity>
              ))}
            </View>

            <Text style={s.label}>Reason</Text>
            <TextInput style={[s.input, {height: 80}]} multiline
              value={applyReason} onChangeText={setApplyReason}
              placeholder="Why are you taking this leave?" />

            <View style={s.modalActions}>
              <TouchableOpacity style={[s.btn, s.btnCancel]}
                onPress={() => setApplyOpen(false)}>
                <Text style={s.btnText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[s.btn, s.btnApprove]} onPress={submitApply}>
                <Text style={s.btnText}>Send to CM</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* Decide modal */}
      <Modal visible={!!decideRow} transparent animationType="slide">
        <View style={s.modalBackdrop}>
          <View style={s.modalCard}>
            <Text style={s.modalTitle}>
              {decideRow?._reject ? 'Reject Leave' : 'Approve Leave'}
            </Text>
            <Text style={s.meta}>BD: {decideRow?.bd_name}</Text>
            <Text style={s.meta}>Date: {decideRow?.leave_date}</Text>
            <Text style={s.meta}>Reason: {decideRow?.reason}</Text>

            <Text style={s.label}>Remarks</Text>
            <TextInput style={[s.input,{height:60}]} multiline
              value={decideRemarks} onChangeText={setDecideRemarks}
              placeholder="Optional note for the BD" />

            <View style={s.modalActions}>
              <TouchableOpacity style={[s.btn, s.btnCancel]}
                onPress={() => setDecideRow(null)}>
                <Text style={s.btnText}>Cancel</Text>
              </TouchableOpacity>
              {decideRow?._reject ? (
                <TouchableOpacity style={[s.btn, s.btnReject]}
                  onPress={() => submitDecide('rejected')}>
                  <Text style={s.btnText}>Confirm Reject</Text>
                </TouchableOpacity>
              ) : (
                <TouchableOpacity style={[s.btn, s.btnApprove]}
                  onPress={() => submitDecide('approved')}>
                  <Text style={s.btnText}>Confirm Approve</Text>
                </TouchableOpacity>
              )}
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

function Tab({ label, active, onPress }) {
  return (
    <TouchableOpacity style={[s.tab, active && s.tabOn]} onPress={onPress}>
      <Text style={[s.tabText, active && s.tabTextOn]}>{label}</Text>
    </TouchableOpacity>
  );
}

function LeaveRow({ row, showCancel, onCancel }) {
  return (
    <View style={s.card}>
      <View style={s.cardHead}>
        <Text style={s.dateBadge}>{row.leave_date}</Text>
        <Text style={[s.statusBadge, {backgroundColor: STATUS_COLOR[row.status]}]}>
          {row.status}
        </Text>
      </View>
      <Text style={s.reason}>{row.leave_type.toUpperCase()}: {row.reason}</Text>
      <Text style={s.meta}>Applied {row.applied_at}</Text>
      {row.decided_at && <Text style={s.meta}>Decided {row.decided_at} {row.decision_remarks ? '- ' + row.decision_remarks : ''}</Text>}
      {showCancel && (
        <TouchableOpacity style={[s.btn, s.btnCancelInline]} onPress={onCancel}>
          <Text style={s.btnText}>Cancel Request</Text>
        </TouchableOpacity>
      )}
    </View>
  );
}

function tomorrowIso() {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  return d.toISOString().slice(0,10);
}

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#0e1116' },
  header: { flexDirection: 'row', alignItems: 'center', padding: 12, backgroundColor: '#161b22', borderBottomWidth: 1, borderBottomColor: '#262b33' },
  back: { color: '#58a6ff', fontSize: 14 },
  title: { color: '#fff', fontSize: 18, fontWeight: '600', marginLeft: 12, flex: 1 },
  applyBtn: { backgroundColor: '#238636', paddingVertical: 6, paddingHorizontal: 12, borderRadius: 6 },
  applyBtnText: { color: '#fff', fontWeight: '600' },

  tabs: { flexDirection: 'row', padding: 8, gap: 8 },
  tab: { paddingVertical: 8, paddingHorizontal: 16, borderRadius: 6, borderWidth: 1, borderColor: '#30363d' },
  tabOn: { backgroundColor: '#1f6feb', borderColor: '#1f6feb' },
  tabText: { color: '#8b949e' },
  tabTextOn: { color: '#fff', fontWeight: '600' },

  section: { padding: 12 },
  empty: { color: '#6e7681', textAlign: 'center', marginTop: 32 },

  card: { backgroundColor: '#161b22', borderRadius: 8, padding: 12, marginBottom: 10, borderWidth: 1, borderColor: '#262b33' },
  cardHead: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 6 },
  bdName: { color: '#fff', fontSize: 16, fontWeight: '600' },
  dateBadge: { color: '#58a6ff', fontWeight: '600' },
  statusBadge: { color: '#fff', fontSize: 11, paddingVertical: 2, paddingHorizontal: 8, borderRadius: 4, overflow: 'hidden' },
  reason: { color: '#c9d1d9', fontSize: 14, marginBottom: 4 },
  meta: { color: '#8b949e', fontSize: 12, marginBottom: 2 },
  cardActions: { flexDirection: 'row', gap: 8, marginTop: 8 },

  btn: { paddingVertical: 8, paddingHorizontal: 14, borderRadius: 6, flex: 1, alignItems: 'center' },
  btnApprove: { backgroundColor: '#238636' },
  btnReject:  { backgroundColor: '#da3633' },
  btnCancel:  { backgroundColor: '#6e7681' },
  btnCancelInline: { backgroundColor: '#6e7681', marginTop: 8, alignSelf: 'flex-start', paddingHorizontal: 18 },
  btnText: { color: '#fff', fontWeight: '600' },

  calRow: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#161b22', padding: 10, marginBottom: 6, borderRadius: 6, borderWidth: 1, borderColor: '#262b33' },
  calDate: { color: '#58a6ff', width: 100, fontWeight: '600' },
  calBd: { color: '#fff', flex: 1 },
  calStatus: { color: '#fff', fontSize: 11, paddingVertical: 2, paddingHorizontal: 8, borderRadius: 4, textTransform: 'uppercase', overflow: 'hidden' },

  modalBackdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.6)', justifyContent: 'flex-end' },
  modalCard: { backgroundColor: '#0e1116', padding: 16, borderTopLeftRadius: 16, borderTopRightRadius: 16, borderTopWidth: 1, borderTopColor: '#262b33' },
  modalTitle: { color: '#fff', fontSize: 18, fontWeight: '600', marginBottom: 12 },
  label: { color: '#8b949e', fontSize: 12, marginTop: 8, marginBottom: 4, textTransform: 'uppercase' },
  input: { backgroundColor: '#161b22', color: '#fff', borderWidth: 1, borderColor: '#262b33', borderRadius: 6, padding: 10 },
  pillRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 6 },
  pill: { paddingVertical: 6, paddingHorizontal: 12, borderRadius: 16, borderWidth: 1, borderColor: '#30363d', backgroundColor: '#161b22' },
  pillOn: { backgroundColor: '#1f6feb', borderColor: '#1f6feb' },
  pillText: { color: '#8b949e' },
  pillTextOn: { color: '#fff', fontWeight: '600' },
  modalActions: { flexDirection: 'row', gap: 8, marginTop: 16 },
});
