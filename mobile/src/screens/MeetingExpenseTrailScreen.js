// MeetingExpenseTrailScreen.js
// Drill-down: every expense line for one meeting, advance status, total cost.
import React, { useState, useEffect } from 'react';
import { View, Text, ScrollView, StyleSheet, ActivityIndicator } from 'react-native';
import { api } from '../api';
import { useAuth } from '../auth';

export default function MeetingExpenseTrailScreen({ route }) {
  const { eventId } = route.params;
  const { token } = useAuth();
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const r = await api.get(`/api/discipline/meeting_expense_trail?event_id=${eventId}`, token);
        setData(r);
      } catch (e) { console.warn(e); }
      finally { setLoading(false); }
    })();
  }, [eventId]);

  if (loading) return <ActivityIndicator style={{ flex: 1 }} size="large" />;
  if (!data) return <View style={s.center}><Text>Not found.</Text></View>;

  const t = data.totals || {};
  const unsettled = (t.advance_unsettled || 0) > 0;

  return (
    <ScrollView style={s.container} contentContainerStyle={{ padding: 16 }}>
      <Text style={s.title}>{data.event.bd_name}</Text>
      <Text style={s.subtitle}>{data.event.event_date}  |  Event #{data.event.id}</Text>

      <View style={s.totalsCard}>
        <Row label="Cash allot (baseline)" value={`Rs ${t.cash_allot_baseline || 0}`} />
        <Row label="Travel advance taken" value={`Rs ${t.travel_advance || 0}`} />
        <Row label="Expenses logged" value={`Rs ${t.expense_drawn || 0}`} />
        <View style={s.divider} />
        <Row label="TOTAL MEETING COST" value={`Rs ${t.total_meeting_cost || 0}`} bold />
        {data.advance && (
          <Row
            label="Advance unsettled"
            value={`Rs ${t.advance_unsettled || 0}`}
            color={unsettled ? '#d0021b' : '#1f9d55'}
            bold
          />
        )}
      </View>

      <Text style={s.sectionTitle}>Expense lines</Text>
      {(data.expenses || []).length === 0 ? (
        <Text style={s.empty}>No expense lines for this meeting.</Text>
      ) : (
        (data.expenses || []).map((e, i) => (
          <View key={i} style={s.expenseRow}>
            <Text style={s.expCategory}>{e.expense_category || 'other'}</Text>
            <Text style={s.expAmount}>Rs {e.amount}</Text>
          </View>
        ))
      )}

      {data.advance && (
        <View style={s.advanceBlock}>
          <Text style={s.sectionTitle}>Advance details</Text>
          <Row label="Amount" value={`Rs ${data.advance.amount}`} />
          <Row label="Created" value={data.advance.created_at} />
          <Row label="Settled" value={data.advance.is_settled ? 'Yes' : 'No'} color={data.advance.is_settled ? '#1f9d55' : '#d0021b'} />
          <Row label="Settlement type" value={data.advance.settlement_type || '—'} />
          {data.advance.aging_days > 0 && (
            <Row label="Aging" value={`${data.advance.aging_days} days`} color="#d0021b" />
          )}
        </View>
      )}
    </ScrollView>
  );
}

function Row({ label, value, color, bold }) {
  return (
    <View style={s.row}>
      <Text style={[s.label, bold && { fontWeight: '700' }]}>{label}</Text>
      <Text style={[s.value, bold && { fontWeight: '700' }, color && { color }]}>{value}</Text>
    </View>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f7f8fa' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 20, fontWeight: '700', color: '#222' },
  subtitle: { fontSize: 13, color: '#888', marginBottom: 16 },
  totalsCard: { backgroundColor: '#fff', padding: 14, borderRadius: 12, marginBottom: 20 },
  row: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 6 },
  label: { color: '#555', fontSize: 14 },
  value: { color: '#222', fontSize: 14 },
  divider: { height: 1, backgroundColor: '#eee', marginVertical: 8 },
  sectionTitle: { fontSize: 14, fontWeight: '700', marginTop: 12, marginBottom: 8, color: '#333' },
  expenseRow: {
    flexDirection: 'row', justifyContent: 'space-between',
    backgroundColor: '#fff', padding: 12, borderRadius: 8, marginBottom: 6
  },
  expCategory: { fontSize: 13, color: '#444', textTransform: 'capitalize' },
  expAmount: { fontSize: 14, fontWeight: '600' },
  advanceBlock: { marginTop: 20, backgroundColor: '#fff', padding: 14, borderRadius: 12 },
  empty: { textAlign: 'center', color: '#888', marginVertical: 16 }
});
