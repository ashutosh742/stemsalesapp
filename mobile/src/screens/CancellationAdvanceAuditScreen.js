// CancellationAdvanceAuditScreen.js
// Two modes:
//  - BD mode (default + when called with {prefill_event_id}): cancel a meeting,
//    pick category + disposition, optionally pick a future event to roll the advance to.
//  - CM/Audit mode (when navigated from PlanApprovalScreen): list of cancellations
//    in the last 7 days + unreturned travel advances. Acknowledge button.
//
// Endpoints used:
//   GET  /api/discipline/cancel/categories
//   GET  /api/discipline/cancel/audit?days=7
//   GET  /api/discipline/cancel/unreturned_advances?days=7
//   POST /api/discipline/cancel/meeting
//
import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity,
  TextInput, ActivityIndicator, Alert, RefreshControl
} from 'react-native';
import { api } from '../lib/api';
import { CURRENT_USER } from '../data/roles';

const COLORS = {
  bg: '#f6f7f9', card: '#fff', text: '#1a1a1a',
  primary: '#1f5fbf', red: '#d0021b', amber: '#f6a623', green: '#1f9d55', muted: '#666',
};

const DISPOSITIONS = [
  { code: 'return',            label: 'Return advance to wallet now' },
  { code: 'roll_next_meeting', label: 'Roll advance to next meeting' },
  { code: 'absorb',            label: 'Absorb (no refund, needs justification)' },
];

export default function CancellationAdvanceAuditScreen({ route, navigation }) {
  const user = CURRENT_USER;
  const prefill = route?.params?.prefill_event_id;
  const auditMode = route?.params?.audit_mode || !prefill;

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  // BD cancel form state
  const [categories, setCategories] = useState([]);
  const [eventId, setEventId] = useState(prefill ? String(prefill) : '');
  const [reason, setReason] = useState('');
  const [category, setCategory] = useState('client_postpone');
  const [disposition, setDisposition] = useState('return');
  const [rolledTo, setRolledTo] = useState('');
  const [submitting, setSubmitting] = useState(false);

  // Audit mode state
  const [cancels, setCancels] = useState([]);
  const [advances, setAdvances] = useState([]);

  const load = async () => {
    setLoading(true);
    try {
      const cats = await api.get('/api/discipline/cancel/categories');
      setCategories(cats.categories || []);
      if (auditMode) {
        const a = await api.get('/api/discipline/cancel/audit?days=7');
        setCancels(a.rows || []);
        const u = await api.get('/api/discipline/cancel/unreturned_advances?days=7');
        setAdvances(u.rows || []);
      }
    } catch (e) { console.warn(e); }
    finally { setLoading(false); setRefreshing(false); }
  };
  useEffect(() => { load(); }, []);

  // When BD picks a category, suggest default disposition from ref table
  useEffect(() => {
    const c = categories.find(x => x.code === category);
    if (c?.default_disposition) setDisposition(c.default_disposition);
  }, [category, categories]);

  const submitCancel = async () => {
    if (!eventId) { Alert.alert('Pick a meeting to cancel'); return; }
    if (!reason || reason.length < 5) { Alert.alert('Reason needed (5+ chars)'); return; }
    if (disposition === 'roll_next_meeting' && !rolledTo) {
      Alert.alert('Pick the event you want to roll the advance to');
      return;
    }
    setSubmitting(true);
    try {
      const r = await api.post('/api/discipline/cancel/meeting', {
        event_id: parseInt(eventId),
        reason, category, disposition,
        rolled_to_event_id: rolledTo ? parseInt(rolledTo) : null,
      });
      if (!r.ok) { Alert.alert('Cancel failed', r.error); return; }
      Alert.alert(
        'Cancelled',
        `Rs ${r.refunded} refunded to wallet. Audit id ${r.audit_id}.`
      );
      if (navigation?.goBack) navigation.goBack();
    } catch (e) { Alert.alert('Network error', String(e)); }
    finally { setSubmitting(false); }
  };

  if (loading) return <ActivityIndicator style={{ flex: 1 }} size="large" />;

  // === BD CANCEL FORM ===
  if (!auditMode || prefill) {
    return (
      <ScrollView style={s.container}>
        <View style={s.banner}>
          <Text style={s.bannerTitle}>Cancel meeting</Text>
          <Text style={s.bannerSub}>
            Rs 500 baseline refunds to your wallet automatically. Travel advance disposition depends on the choice below.
          </Text>
        </View>

        <View style={s.card}>
          <Text style={s.label}>Meeting id</Text>
          <TextInput
            style={s.input} keyboardType="number-pad"
            value={eventId} onChangeText={setEventId}
            editable={!prefill}
          />

          <Text style={s.label}>Category</Text>
          {categories.map(c => (
            <TouchableOpacity
              key={c.code}
              style={[s.choice, category === c.code && s.choiceActive]}
              onPress={() => setCategory(c.code)}
            >
              <Text style={category === c.code ? s.choiceTxtActive : s.choiceTxt}>
                {c.label}
              </Text>
            </TouchableOpacity>
          ))}

          <Text style={s.label}>Reason (BD note)</Text>
          <TextInput
            style={[s.input, { minHeight: 70 }]} multiline
            value={reason} onChangeText={setReason}
            placeholder="What happened?"
          />

          <Text style={s.label}>Advance disposition</Text>
          {DISPOSITIONS.map(d => (
            <TouchableOpacity
              key={d.code}
              style={[s.choice, disposition === d.code && s.choiceActive]}
              onPress={() => setDisposition(d.code)}
            >
              <Text style={disposition === d.code ? s.choiceTxtActive : s.choiceTxt}>
                {d.label}
              </Text>
            </TouchableOpacity>
          ))}

          {disposition === 'roll_next_meeting' ? (
            <>
              <Text style={s.label}>Roll to event id</Text>
              <TextInput
                style={s.input} keyboardType="number-pad"
                value={rolledTo} onChangeText={setRolledTo}
              />
            </>
          ) : null}

          <TouchableOpacity
            style={[s.submitBtn, { opacity: submitting ? 0.5 : 1 }]}
            disabled={submitting} onPress={submitCancel}
          >
            <Text style={s.submitTxt}>{submitting ? 'Submitting...' : 'Submit cancellation'}</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    );
  }

  // === CM AUDIT MODE ===
  return (
    <ScrollView
      style={s.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      <Text style={s.h1}>Cancellations (last 7 days)</Text>
      {cancels.length === 0 ? (
        <View style={s.card}><Text style={{ color: COLORS.green }}>No cancellations.</Text></View>
      ) : cancels.map(c => (
        <View key={c.id} style={s.rowCard}>
          <Text style={s.rowTitle}>Event {c.event_id} . BD {c.bd_uid}</Text>
          <Text style={s.muted}>{c.cancellation_category} . {c.advance_disposition}</Text>
          <Text style={s.muted}>Refunded Rs {c.cash_allot_refunded}{c.travel_advance_amount ? ` + advance Rs ${c.travel_advance_amount}` : ''}</Text>
          <Text style={s.muted}>{new Date(c.cancelled_at).toLocaleString()}</Text>
        </View>
      ))}

      <Text style={s.h1}>Unreturned advances</Text>
      {advances.length === 0 ? (
        <View style={s.card}><Text style={{ color: COLORS.green }}>None.</Text></View>
      ) : advances.map(a => (
        <View key={a.advance_id} style={[s.rowCard, { borderLeftColor: COLORS.red }]}>
          <Text style={s.rowTitle}>{a.bd_name} . Rs {a.amount}</Text>
          <Text style={s.muted}>Event {a.event_id} . aging {a.aging_days} days . status {a.consumed_status}</Text>
        </View>
      ))}
    </ScrollView>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.bg, padding: 12 },
  banner: { backgroundColor: '#fff5e6', padding: 12, borderRadius: 8, marginBottom: 12 },
  bannerTitle: { fontWeight: '600', fontSize: 15, color: '#a36500' },
  bannerSub: { color: '#7a4f00', marginTop: 4 },
  card: { backgroundColor: COLORS.card, padding: 14, borderRadius: 10, marginBottom: 10 },
  rowCard: { backgroundColor: COLORS.card, padding: 12, borderRadius: 8, marginBottom: 8, borderLeftWidth: 4, borderLeftColor: COLORS.amber },
  rowTitle: { fontSize: 15, fontWeight: '600' },
  h1: { fontSize: 16, fontWeight: '600', marginTop: 14, marginBottom: 6 },
  muted: { color: COLORS.muted, marginTop: 2, fontSize: 12 },
  label: { marginTop: 12, color: COLORS.muted, fontSize: 12 },
  input: { borderWidth: 1, borderColor: '#ddd', borderRadius: 6, padding: 8, marginTop: 4, fontSize: 16 },
  choice: { padding: 10, borderWidth: 1, borderColor: '#ddd', borderRadius: 6, marginTop: 6 },
  choiceActive: { backgroundColor: COLORS.primary, borderColor: COLORS.primary },
  choiceTxt: { color: COLORS.text },
  choiceTxtActive: { color: '#fff', fontWeight: '600' },
  submitBtn: { backgroundColor: COLORS.red, padding: 14, borderRadius: 8, alignItems: 'center', marginTop: 16 },
  submitTxt: { color: '#fff', fontSize: 16, fontWeight: '600' },
});
