// TaskEfficiencyScreen — CM view of yesterday's efficiency across the cluster.
// Each BD row shows: efficiency score (donut), 4 sub-metrics, signal breakdown.
// Tap a BD to drill into their item-level breakdown.
//
// Backend mapping:
//   GET /efficiency/api_get_cluster_rollup?date=YYYY-MM-DD
//   → AIAgents/TaskEfficiencyAgent_model::cluster_rollup(cm_id, score_date)
//
// Score = 0.30*completion + 0.20*timeliness + 0.20*action + 0.30*purpose

import React, { useMemo, useState } from 'react';
import { View, Text, ScrollView, Pressable, StyleSheet } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CLUSTER_EFFICIENCY } from '../data/cm';

const scoreColor = (s) => s >= 80 ? '#10B981' : s >= 60 ? '#F59E0B' : '#EF4444';
const scoreLabel = (s) => s >= 80 ? 'On track' : s >= 60 ? 'Watch' : 'At risk';

export default function TaskEfficiencyScreen({ navigation }) {
  const [sort, setSort] = useState('score'); // 'score' | 'purpose' | 'completion'

  const sorted = useMemo(() => {
    const arr = [...CLUSTER_EFFICIENCY.bds];
    if (sort === 'score')      arr.sort((a, b) => b.efficiency_score - a.efficiency_score);
    if (sort === 'purpose')    arr.sort((a, b) => b.purpose_pct - a.purpose_pct);
    if (sort === 'completion') arr.sort((a, b) => b.completion_pct - a.completion_pct);
    return arr;
  }, [sort]);

  const summary = useMemo(() => {
    const bds = CLUSTER_EFFICIENCY.bds;
    const avg = (k) => bds.reduce((s, b) => s + b[k], 0) / bds.length;
    return {
      cluster_score: avg('efficiency_score').toFixed(1),
      at_risk: bds.filter(b => b.efficiency_score < 60).length,
      on_track: bds.filter(b => b.efficiency_score >= 80).length,
      total: bds.length,
    };
  }, []);

  return (
    <View style={styles.container}>
      <LinearGradient colors={['#1F2A6E', '#3E21FB']} style={styles.header}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Ionicons name="chevron-back" size={26} color="#fff" />
        </Pressable>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>Task Efficiency</Text>
          <Text style={styles.headerSub}>Cluster · {CLUSTER_EFFICIENCY.score_date}</Text>
        </View>
        <View style={styles.clusterScoreBadge}>
          <Text style={styles.clusterScoreNum}>{summary.cluster_score}</Text>
          <Text style={styles.clusterScoreLabel}>cluster</Text>
        </View>
      </LinearGradient>

      <View style={styles.statRow}>
        <View style={[styles.statCard, { backgroundColor: '#10B98115' }]}>
          <Text style={[styles.statValue, { color: '#10B981' }]}>{summary.on_track}</Text>
          <Text style={styles.statLabel}>On track</Text>
        </View>
        <View style={[styles.statCard, { backgroundColor: '#F59E0B15' }]}>
          <Text style={[styles.statValue, { color: '#F59E0B' }]}>{summary.total - summary.on_track - summary.at_risk}</Text>
          <Text style={styles.statLabel}>Watch</Text>
        </View>
        <View style={[styles.statCard, { backgroundColor: '#EF444415' }]}>
          <Text style={[styles.statValue, { color: '#EF4444' }]}>{summary.at_risk}</Text>
          <Text style={styles.statLabel}>At risk</Text>
        </View>
      </View>

      <View style={styles.sortRow}>
        <Text style={styles.sortLabel}>Sort:</Text>
        {[
          { id: 'score',      label: 'Score' },
          { id: 'purpose',    label: 'Purpose %' },
          { id: 'completion', label: 'Completion %' },
        ].map(s => (
          <Pressable key={s.id} onPress={() => setSort(s.id)}
                     style={[styles.sortChip, sort === s.id && styles.sortChipActive]}>
            <Text style={[styles.sortChipText, sort === s.id && styles.sortChipTextActive]}>{s.label}</Text>
          </Pressable>
        ))}
      </View>

      <ScrollView style={styles.scroll} contentContainerStyle={{ paddingBottom: 32 }}>
        {sorted.map(bd => (
          <Pressable key={bd.bd_id} style={styles.bdCard}
                     onPress={() => navigation && navigation.navigate && navigation.navigate('ActivityScreen', { bdFilter: bd.bd_id })}>
            <View style={styles.bdCardLeft}>
              <View style={[styles.donut, { borderColor: scoreColor(bd.efficiency_score) }]}>
                <Text style={[styles.donutNum, { color: scoreColor(bd.efficiency_score) }]}>
                  {Math.round(bd.efficiency_score)}
                </Text>
              </View>
            </View>
            <View style={{ flex: 1 }}>
              <View style={styles.bdHeaderRow}>
                <Text style={styles.bdName}>{bd.bd_name}</Text>
                <View style={[styles.statusPill, { backgroundColor: scoreColor(bd.efficiency_score) + '20' }]}>
                  <Text style={[styles.statusPillText, { color: scoreColor(bd.efficiency_score) }]}>
                    {scoreLabel(bd.efficiency_score)}
                  </Text>
                </View>
                <Ionicons
                  name={bd.trend === 'up' ? 'trending-up' : bd.trend === 'down' ? 'trending-down' : 'remove'}
                  size={16}
                  color={bd.trend === 'up' ? '#10B981' : bd.trend === 'down' ? '#EF4444' : '#9CA3AF'}
                  style={{ marginLeft: 6 }}
                />
              </View>

              <View style={styles.metricRow}>
                <Metric label="Done"     value={`${bd.done}/${bd.planned}`} pct={bd.completion_pct} />
                <Metric label="On time"  value={`${bd.timeliness_pct}%`}    pct={bd.timeliness_pct} />
                <Metric label="Action"   value={`${bd.action_pct}%`}        pct={bd.action_pct} />
                <Metric label="Purpose"  value={`${bd.purpose_pct}%`}       pct={bd.purpose_pct} />
              </View>

              <View style={styles.signalRow}>
                <Ionicons name="information-circle-outline" size={12} color="#6B7280" />
                <Text style={styles.signalText}>
                  Purpose signal: {bd.signal_breakdown.bd_tag} BD-tag · {bd.signal_breakdown.ai_inference} AI · {bd.signal_breakdown.funnel_movement} funnel
                </Text>
              </View>
            </View>
          </Pressable>
        ))}

        <Text style={styles.footnote}>
          Score = 0.30 · completion + 0.20 · timeliness + 0.20 · action + 0.30 · purpose. Purpose blends BD self-tag, AI inference from notes, and funnel movement (priority in that order).
        </Text>
      </ScrollView>
    </View>
  );
}

function Metric({ label, value, pct }) {
  const c = pct >= 80 ? '#10B981' : pct >= 60 ? '#F59E0B' : '#EF4444';
  return (
    <View style={styles.metric}>
      <Text style={styles.metricLabel}>{label}</Text>
      <Text style={[styles.metricValue, { color: c }]}>{value}</Text>
      <View style={styles.metricBarBg}><View style={[styles.metricBar, { width: `${Math.min(100, pct)}%`, backgroundColor: c }]} /></View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#F7F8FB' },
  header: { paddingTop: 48, paddingBottom: 20, paddingHorizontal: 16, flexDirection: 'row', alignItems: 'center', gap: 12 },
  backBtn: { padding: 4 },
  headerTitle: { color: '#fff', fontSize: 22, fontWeight: '700' },
  headerSub: { color: '#C9CFFF', fontSize: 12, marginTop: 2 },
  clusterScoreBadge: { backgroundColor: 'rgba(255,255,255,0.18)', borderRadius: 12, paddingHorizontal: 12, paddingVertical: 8, alignItems: 'center' },
  clusterScoreNum: { color: '#fff', fontSize: 22, fontWeight: '800' },
  clusterScoreLabel: { color: '#C9CFFF', fontSize: 10, marginTop: -2 },

  statRow: { flexDirection: 'row', gap: 8, paddingHorizontal: 16, paddingTop: 12 },
  statCard: { flex: 1, borderRadius: 12, paddingVertical: 10, alignItems: 'center' },
  statValue: { fontSize: 22, fontWeight: '800' },
  statLabel: { fontSize: 11, color: '#6B7280', marginTop: 2 },

  sortRow: { flexDirection: 'row', alignItems: 'center', gap: 8, paddingHorizontal: 16, paddingTop: 12, paddingBottom: 8 },
  sortLabel: { fontSize: 12, color: '#6B7280' },
  sortChip: { paddingHorizontal: 10, paddingVertical: 6, borderRadius: 14, backgroundColor: '#fff', borderWidth: 1, borderColor: '#E5E7EB' },
  sortChipActive: { backgroundColor: '#3E21FB', borderColor: '#3E21FB' },
  sortChipText: { fontSize: 12, color: '#374151' },
  sortChipTextActive: { color: '#fff', fontWeight: '600' },

  scroll: { flex: 1, paddingHorizontal: 16 },
  bdCard: { flexDirection: 'row', backgroundColor: '#fff', borderRadius: 14, padding: 12, marginTop: 10, gap: 12, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 6, shadowOffset: { width: 0, height: 2 } },
  bdCardLeft: { justifyContent: 'center' },
  donut: { width: 56, height: 56, borderRadius: 28, borderWidth: 4, alignItems: 'center', justifyContent: 'center', backgroundColor: '#fff' },
  donutNum: { fontSize: 18, fontWeight: '800' },

  bdHeaderRow: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  bdName: { fontSize: 15, fontWeight: '700', color: '#111827', flex: 1 },
  statusPill: { paddingHorizontal: 8, paddingVertical: 2, borderRadius: 10 },
  statusPillText: { fontSize: 10, fontWeight: '700' },

  metricRow: { flexDirection: 'row', gap: 8, marginTop: 8 },
  metric: { flex: 1 },
  metricLabel: { fontSize: 10, color: '#6B7280' },
  metricValue: { fontSize: 13, fontWeight: '700', marginTop: 1 },
  metricBarBg: { height: 3, backgroundColor: '#F3F4F6', borderRadius: 2, marginTop: 3, overflow: 'hidden' },
  metricBar: { height: 3, borderRadius: 2 },

  signalRow: { flexDirection: 'row', alignItems: 'center', gap: 4, marginTop: 8 },
  signalText: { fontSize: 10, color: '#6B7280' },

  footnote: { fontSize: 11, color: '#9CA3AF', textAlign: 'center', marginTop: 16, marginHorizontal: 8, lineHeight: 16 },
});
