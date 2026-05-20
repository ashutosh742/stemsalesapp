// MyTasksScreen — BD self-view of today's planned tasks with 1-tap outcome chips.
// After each task fires, the BD taps ✅ Met / ⚠️ Partial / ❌ Missed.
// That tag is the highest-priority signal feeding the Task Efficiency Agent.
//
// Backend mapping:
//   GET  /efficiency/api_get_bd_score?days=7   — pulls today's tasks + recent history
//   POST /efficiency/api_tag_outcome           — { planner_id, outcome, notes? }

import React, { useState, useMemo } from 'react';
import { View, Text, ScrollView, Pressable, StyleSheet } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { MY_TASKS_TODAY, MY_EFFICIENCY_TODAY } from '../data/cm';

const TASK_META = {
  sales_call: { icon: 'call',           color: '#3498DB', label: 'Call' },
  email:      { icon: 'mail',           color: '#9B59B6', label: 'Email' },
  visit:      { icon: 'walk',           color: '#F39C12', label: 'Visit' },
  meeting:    { icon: 'people',         color: '#14B8A6', label: 'Meeting' },
};

const OUTCOME_META = {
  met:     { icon: 'checkmark-circle',   color: '#10B981', label: 'Met',     fg: '#fff' },
  partial: { icon: 'alert-circle',       color: '#F59E0B', label: 'Partial', fg: '#fff' },
  missed:  { icon: 'close-circle',       color: '#EF4444', label: 'Missed',  fg: '#fff' },
};

export default function MyTasksScreen({ navigation }) {
  // local state mirroring outcome_tag; in prod, POSTs to /efficiency/api_tag_outcome
  const [tasks, setTasks] = useState(MY_TASKS_TODAY);
  const score = MY_EFFICIENCY_TODAY;

  const tag = (planner_id, outcome) => {
    setTasks(ts => ts.map(t => t.planner_id === planner_id ? { ...t, outcome_tag: outcome } : t));
    // TODO: fetch('/efficiency/api_tag_outcome', { method: 'POST', body: JSON.stringify({ planner_id, outcome }) })
  };

  const done = tasks.filter(t => t.is_done).length;
  const tagged = tasks.filter(t => t.outcome_tag).length;

  return (
    <View style={styles.container}>
      <LinearGradient colors={['#1F2A6E', '#3E21FB']} style={styles.header}>
        {navigation && navigation.goBack && (
          <Pressable onPress={() => navigation.goBack()} style={styles.backBtn}>
            <Ionicons name="chevron-back" size={26} color="#fff" />
          </Pressable>
        )}
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>My tasks today</Text>
          <Text style={styles.headerSub}>{done}/{tasks.length} done · {tagged} tagged</Text>
        </View>
        <View style={styles.scoreBadge}>
          <Text style={styles.scoreNum}>{Math.round(score.efficiency_score)}</Text>
          <Text style={styles.scoreLabel}>today</Text>
        </View>
      </LinearGradient>

      <View style={styles.miniScoreRow}>
        <Mini label="Yesterday"   value={score.yesterday_score.toFixed(0)} />
        <Mini label="7-day avg"   value={score.last_7_avg.toFixed(0)} />
        <Mini label="Done"        value={`${score.done}/${score.planned}`} />
        <Mini label="On purpose"  value={`${score.purpose_pct}%`} />
      </View>

      <ScrollView style={styles.scroll} contentContainerStyle={{ paddingBottom: 32 }}>
        {tasks.map(t => {
          const meta = TASK_META[t.task_type];
          const tagged_meta = t.outcome_tag ? OUTCOME_META[t.outcome_tag] : null;
          return (
            <View key={t.planner_id} style={styles.taskCard}>
              <View style={styles.taskHeader}>
                <View style={[styles.taskIcon, { backgroundColor: meta.color }]}>
                  <Ionicons name={meta.icon} size={16} color="#fff" />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.taskTitle}>{meta.label} · {t.lead_name}</Text>
                  <Text style={styles.taskPurpose}>{t.purpose}</Text>
                </View>
                <View style={styles.taskTimeBox}>
                  <Text style={styles.taskTime}>{t.scheduled_at}</Text>
                  {t.is_done ? (
                    <Text style={[styles.taskStatus, { color: '#10B981' }]}>
                      done {t.executed_at}
                    </Text>
                  ) : (
                    <Text style={[styles.taskStatus, { color: '#9CA3AF' }]}>upcoming</Text>
                  )}
                </View>
              </View>

              {t.is_done && (
                <View style={styles.chipRow}>
                  {tagged_meta ? (
                    <View style={[styles.taggedBox, { backgroundColor: tagged_meta.color }]}>
                      <Ionicons name={tagged_meta.icon} size={14} color="#fff" />
                      <Text style={styles.taggedText}>Tagged: {tagged_meta.label}</Text>
                      <Pressable onPress={() => tag(t.planner_id, null)} style={styles.taggedRetag}>
                        <Text style={styles.taggedRetagText}>change</Text>
                      </Pressable>
                    </View>
                  ) : (
                    <>
                      <Text style={styles.chipQ}>Purpose?</Text>
                      {['met','partial','missed'].map(o => {
                        const om = OUTCOME_META[o];
                        return (
                          <Pressable key={o} style={[styles.chip, { borderColor: om.color }]}
                                     onPress={() => tag(t.planner_id, o)}>
                            <Ionicons name={om.icon} size={14} color={om.color} />
                            <Text style={[styles.chipText, { color: om.color }]}>{om.label}</Text>
                          </Pressable>
                        );
                      })}
                    </>
                  )}
                  {t.inferred_purpose && !tagged_meta && (
                    <Text style={styles.inferredHint}>AI infers: {t.inferred_purpose}</Text>
                  )}
                </View>
              )}
            </View>
          );
        })}

        <Text style={styles.footnote}>
          Tag each completed task to make the daily efficiency score accurate. When you don't tag, the agent falls back to AI inference from your call/MoM notes, then funnel movement.
        </Text>
      </ScrollView>
    </View>
  );
}

function Mini({ label, value }) {
  return (
    <View style={styles.miniCard}>
      <Text style={styles.miniValue}>{value}</Text>
      <Text style={styles.miniLabel}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#F7F8FB' },
  header: { paddingTop: 48, paddingBottom: 20, paddingHorizontal: 16, flexDirection: 'row', alignItems: 'center', gap: 12 },
  backBtn: { padding: 4 },
  headerTitle: { color: '#fff', fontSize: 22, fontWeight: '700' },
  headerSub: { color: '#C9CFFF', fontSize: 12, marginTop: 2 },
  scoreBadge: { backgroundColor: 'rgba(255,255,255,0.18)', borderRadius: 12, paddingHorizontal: 12, paddingVertical: 8, alignItems: 'center' },
  scoreNum: { color: '#fff', fontSize: 22, fontWeight: '800' },
  scoreLabel: { color: '#C9CFFF', fontSize: 10, marginTop: -2 },

  miniScoreRow: { flexDirection: 'row', gap: 6, paddingHorizontal: 12, paddingTop: 12 },
  miniCard: { flex: 1, backgroundColor: '#fff', borderRadius: 10, paddingVertical: 8, alignItems: 'center', shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 4 },
  miniValue: { fontSize: 16, fontWeight: '800', color: '#111827' },
  miniLabel: { fontSize: 10, color: '#6B7280', marginTop: 2 },

  scroll: { flex: 1, paddingHorizontal: 12, paddingTop: 12 },
  taskCard: { backgroundColor: '#fff', borderRadius: 12, padding: 12, marginBottom: 10, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 4 },
  taskHeader: { flexDirection: 'row', alignItems: 'flex-start', gap: 10 },
  taskIcon: { width: 30, height: 30, borderRadius: 15, alignItems: 'center', justifyContent: 'center' },
  taskTitle: { fontSize: 14, fontWeight: '700', color: '#111827' },
  taskPurpose: { fontSize: 12, color: '#6B7280', marginTop: 2 },
  taskTimeBox: { alignItems: 'flex-end' },
  taskTime: { fontSize: 12, fontWeight: '700', color: '#374151' },
  taskStatus: { fontSize: 10, marginTop: 1 },

  chipRow: { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 10, flexWrap: 'wrap' },
  chipQ: { fontSize: 12, color: '#6B7280', marginRight: 4 },
  chip: { flexDirection: 'row', alignItems: 'center', gap: 4, paddingHorizontal: 10, paddingVertical: 6, borderRadius: 14, borderWidth: 1, backgroundColor: '#fff' },
  chipText: { fontSize: 12, fontWeight: '600' },

  taggedBox: { flexDirection: 'row', alignItems: 'center', gap: 6, paddingHorizontal: 10, paddingVertical: 6, borderRadius: 14 },
  taggedText: { color: '#fff', fontSize: 12, fontWeight: '700' },
  taggedRetag: { marginLeft: 6, paddingHorizontal: 6, paddingVertical: 2, borderRadius: 8, backgroundColor: 'rgba(255,255,255,0.25)' },
  taggedRetagText: { color: '#fff', fontSize: 10 },

  inferredHint: { fontSize: 11, color: '#9CA3AF', marginLeft: 8 },
  footnote: { fontSize: 11, color: '#9CA3AF', textAlign: 'center', marginTop: 12, marginHorizontal: 8, lineHeight: 16 },
});
