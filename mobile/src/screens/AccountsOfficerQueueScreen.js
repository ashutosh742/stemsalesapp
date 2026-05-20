// AccountsOfficerQueueScreen.js
// Role-gated: only type_id = 27 (Accounts Officer) sees this screen.
// Shows expenses where requires_dual_approval=1 AND cm_approved=1 AND ao_approved=0.
// AO can approve / reject. After AO approval, tblcallevents.accounts_apr=1.
//
// Endpoints:
//   GET  /api/discipline/expense/ao_queue
//   POST /api/discipline/expense/ao_approve
//
import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity,
  TextInput, ActivityIndicator, Alert, RefreshControl, Image
} from 'react-native';
import { api } from '../lib/api';
import { CURRENT_USER } from '../data/roles';

const COLORS = {
  bg: '#f6f7f9', card: '#fff', text: '#1a1a1a',
  primary: '#1f5fbf', red: '#d0021b', amber: '#f6a623', green: '#1f9d55', muted: '#666',
};

export default function AccountsOfficerQueueScreen() {
  const user = CURRENT_USER;
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [rows, setRows] = useState([]);
  const [active, setActive] = useState(null);
  const [remarks, setRemarks] = useState('');
  const [error, setError] = useState('');

  const load = async () => {
    setLoading(true); setError('');
    try {
      const r = await api.get('/api/discipline/expense/ao_queue');
      if (r.error === 'not_accounts_officer') {
        setError('This queue is for Accounts Officers only (role 27).');
      } else {
        setRows(r.rows || []);
      }
    } catch (e) { setError(String(e)); }
    finally { setLoading(false); setRefreshing(false); }
  };
  useEffect(() => { load(); }, []);

  const approve = async (log_id) => {
    try {
      const r = await api.post('/api/discipline/expense/ao_approve', { log_id, remarks });
      if (!r.ok) { Alert.alert('Failed', r.error); return; }
      Alert.alert('Approved', `Expense log ${log_id} approved.`);
      setActive(null); setRemarks('');
      load();
    } catch (e) { Alert.alert('Network error', String(e)); }
  };

  if (loading) return <ActivityIndicator style={{ flex: 1 }} size="large" />;
  if (error) return <View style={s.center}><Text style={{ color: COLORS.red }}>{error}</Text></View>;

  if (active) {
    const dir = active.variance_pct > 0 ? 'over' : 'under';
    return (
      <ScrollView style={s.container}>
        <TouchableOpacity onPress={() => setActive(null)} style={s.backBtn}>
          <Text style={{ color: COLORS.primary }}>Back to queue</Text>
        </TouchableOpacity>
        <View style={s.card}>
          <Text style={s.h2}>Dual approval needed</Text>
          <Text style={s.muted}>BD {active.bd_name} . CM {active.cm_name || '—'}</Text>

          <Text style={s.label}>Planned vs actual</Text>
          <Text style={s.value}>Rs {active.planned_cost} planned, Rs {active.actual_cost} actual</Text>
          <View style={[s.varianceBox, { backgroundColor: '#ffe5e5' }]}>
            <Text style={{ color: COLORS.red, fontSize: 16, fontWeight: '600' }}>
              {active.variance_pct > 0 ? '+' : ''}{active.variance_pct}% {dir}
            </Text>
          </View>

          <Text style={s.label}>CM remark</Text>
          <Text style={s.value}>{active.cm_remarks || '(none)'}</Text>

          <Text style={s.label}>Breakdown</Text>
          <Text style={s.value}>{active.expense_breakdown_json || '(none)'}</Text>

          {active.receipt_filename ? (
            <>
              <Text style={s.label}>Receipt</Text>
              <Image source={{ uri: `https://stemapp.in/${active.receipt_filename}` }} style={s.receipt} />
            </>
          ) : null}

          <Text style={s.label}>Your remarks</Text>
          <TextInput
            style={s.input} value={remarks} onChangeText={setRemarks}
            placeholder="Approval note" multiline
          />

          <TouchableOpacity style={s.approveBtn} onPress={() => approve(active.id)}>
            <Text style={s.btnTxt}>Approve as Accounts Officer</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    );
  }

  return (
    <ScrollView
      style={s.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      <View style={s.banner}>
        <Text style={s.bannerTitle}>Dual approval queue</Text>
        <Text style={s.bannerSub}>
          {rows.length} expense{rows.length === 1 ? '' : 's'} pending AO approval.
          Variance over 20 percent. CM has cleared. Your sign off finalises.
        </Text>
      </View>

      {rows.length === 0 ? (
        <View style={s.card}>
          <Text style={{ color: COLORS.green }}>Queue is clear. Nothing pending.</Text>
        </View>
      ) : rows.map(r => (
        <TouchableOpacity key={r.id} style={s.rowCard} onPress={() => setActive(r)}>
          <Text style={s.rowTitle}>{r.bd_name} . Rs {r.actual_cost}</Text>
          <Text style={[s.varTag, { color: COLORS.red }]}>
            {r.variance_pct > 0 ? '+' : ''}{r.variance_pct}% variance
          </Text>
          <Text style={s.muted}>Submitted {new Date(r.submitted_at).toLocaleString()}</Text>
        </TouchableOpacity>
      ))}
    </ScrollView>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.bg, padding: 12 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 20 },
  banner: { backgroundColor: '#fff5e6', padding: 12, borderRadius: 8, marginBottom: 12 },
  bannerTitle: { fontWeight: '600', fontSize: 15, color: '#a36500' },
  bannerSub: { color: '#7a4f00', marginTop: 4 },
  card: { backgroundColor: COLORS.card, padding: 14, borderRadius: 10, marginBottom: 10 },
  rowCard: { backgroundColor: COLORS.card, padding: 12, borderRadius: 8, marginBottom: 8, borderLeftWidth: 4, borderLeftColor: COLORS.amber },
  rowTitle: { fontSize: 15, fontWeight: '600' },
  varTag: { marginTop: 2, fontWeight: '600' },
  muted: { color: COLORS.muted, marginTop: 4, fontSize: 12 },
  h2: { fontSize: 18, fontWeight: '600', marginBottom: 6 },
  label: { marginTop: 12, color: COLORS.muted, fontSize: 12 },
  value: { fontSize: 16 },
  input: { borderWidth: 1, borderColor: '#ddd', borderRadius: 6, padding: 8, marginTop: 4, fontSize: 16, minHeight: 60 },
  varianceBox: { padding: 10, borderRadius: 8, marginTop: 6 },
  receipt: { width: '100%', height: 200, resizeMode: 'cover', borderRadius: 8, marginTop: 6 },
  approveBtn: { backgroundColor: COLORS.green, padding: 14, borderRadius: 8, alignItems: 'center', marginTop: 16 },
  btnTxt: { color: '#fff', fontSize: 16, fontWeight: '600' },
  backBtn: { padding: 8 },
});
