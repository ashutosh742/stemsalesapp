// DisciplineScoreScreen.js
// BD view of their daily / weekly discipline score.
// Surfaces: plan-on-time, same-day flag, task-on-time%, advance settlement.
import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, RefreshControl, StyleSheet,
  TouchableOpacity, ActivityIndicator
} from 'react-native';
import { api } from '../api';
import { useAuth } from '../auth';

const FLAG_COLORS = { green: '#1f9d55', amber: '#f6a623', red: '#d0021b' };

export default function DisciplineScoreScreen({ navigation }) {
  const { user, token } = useAuth();
  const [loading, setLoading] = useState(true);
  const [score, setScore] = useState(null);
  const [narrative, setNarrative] = useState('');
  const [refreshing, setRefreshing] = useState(false);
  const today = new Date().toISOString().slice(0, 10);

  const load = async () => {
    try {
      setLoading(true);
      const s = await api.get(`/api/discipline/bd_score?user_id=${user.id}&date=${today}`, token);
      setScore(s);
      const n = await api.get(`/api/discipline/narrative?user_id=${user.id}&date=${today}`, token);
      setNarrative(n.narrative || '');
    } catch (e) {
      console.warn('discipline load failed', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => { load(); }, []);

  if (loading) return <ActivityIndicator style={{ flex: 1 }} size="large" />;
  if (!score) return <View style={s.center}><Text>No score yet for today.</Text></View>;

  const flagColor = FLAG_COLORS[score.flag] || '#888';
  const b = score.breakdown || {};

  return (
    <ScrollView
      style={s.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      <View style={[s.scoreCard, { borderLeftColor: flagColor }]}>
        <Text style={s.label}>Today's discipline score</Text>
        <Text style={[s.score, { color: flagColor }]}>{score.score}<Text style={s.outOf}>/100</Text></Text>
        <Text style={[s.flag, { color: flagColor }]}>{score.flag.toUpperCase()}</Text>
      </View>

      {!!narrative && (
        <View style={s.narrativeBox}>
          <Text style={s.narrativeLabel}>Coach note</Text>
          <Text style={s.narrativeText}>{narrative}</Text>
        </View>
      )}

      <Text style={s.sectionTitle}>Breakdown</Text>

      <Row label="Plan submitted by 6:30 PM yesterday" value={b.plan_on_time ? 'Yes' : 'No'} good={b.plan_on_time} />
      <Row label="Same-day planning (RED flag)" value={b.plan_same_day ? 'Yes' : 'No'} good={!b.plan_same_day} />
      <Row label="Plan missing" value={b.plan_missed ? 'Yes' : 'No'} good={!b.plan_missed} />
      <Row label="Tasks completed on time" value={`${b.tasks_on_time_pct || 0}%`} good={(b.tasks_on_time_pct || 0) >= 80} />
      <Row label="Tasks overdue" value={b.tasks_overdue || 0} good={(b.tasks_overdue || 0) === 0} />
      <Row label="Cancellations today" value={b.cancellations || 0} good={(b.cancellations || 0) === 0} />
      <Row label="Advances pending return" value={b.advances_pending || 0} good={(b.advances_pending || 0) === 0} />

      <View style={s.rules}>
        <Text style={s.rulesTitle}>The rules</Text>
        <Text style={s.rule}>1. Submit tomorrow's plan by 6:30 PM today.</Text>
        <Text style={s.rule}>2. CM must approve by 7:00 PM.</Text>
        <Text style={s.rule}>3. Same-day planning = RED flag.</Text>
        <Text style={s.rule}>4. Plan blocked = 500 rupees auto-allocated, RED flag.</Text>
        <Text style={s.rule}>5. Cancelled meeting + advance taken = must return or roll over.</Text>
        <Text style={s.rule}>6. Every expense must link to a meeting.</Text>
        <Text style={s.rule}>7. Task past planned end-time = RED.</Text>
      </View>
    </ScrollView>
  );
}

function Row({ label, value, good }) {
  return (
    <View style={s.row}>
      <Text style={s.rowLabel}>{label}</Text>
      <Text style={[s.rowValue, { color: good ? '#1f9d55' : '#d0021b' }]}>{value}</Text>
    </View>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f7f8fa', padding: 16 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  scoreCard: {
    backgroundColor: '#fff', padding: 20, borderRadius: 12,
    borderLeftWidth: 6, marginBottom: 16, elevation: 2
  },
  label: { fontSize: 13, color: '#666', marginBottom: 4 },
  score: { fontSize: 48, fontWeight: '700' },
  outOf: { fontSize: 20, color: '#999' },
  flag: { fontSize: 14, fontWeight: '600', marginTop: 4, letterSpacing: 1 },
  narrativeBox: { backgroundColor: '#fffbe8', padding: 14, borderRadius: 10, marginBottom: 16 },
  narrativeLabel: { fontSize: 11, color: '#a8801a', fontWeight: '600', marginBottom: 4, letterSpacing: 0.5 },
  narrativeText: { fontSize: 14, color: '#5a4c0a', lineHeight: 20 },
  sectionTitle: { fontSize: 16, fontWeight: '600', marginVertical: 12, color: '#222' },
  row: {
    flexDirection: 'row', justifyContent: 'space-between',
    backgroundColor: '#fff', padding: 12, borderRadius: 8, marginBottom: 6
  },
  rowLabel: { color: '#444', flex: 1, fontSize: 14 },
  rowValue: { fontWeight: '600', fontSize: 14 },
  rules: { marginTop: 20, padding: 14, backgroundColor: '#f0f2f5', borderRadius: 10 },
  rulesTitle: { fontSize: 13, fontWeight: '700', marginBottom: 8, color: '#333' },
  rule: { fontSize: 12, color: '#555', marginBottom: 4, lineHeight: 18 }
});
