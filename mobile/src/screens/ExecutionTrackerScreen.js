// ExecutionTrackerScreen — planned vs actual today. Shows what's done, in-flight,
// and at-risk. Feeds AnayaAgent::compute_bd_discipline (the score that drives
// Cadence + the morning 8 AM briefing).

import React, { useMemo } from 'react';
import {
  View, Text, ScrollView, StyleSheet, StatusBar, Pressable,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { TODAY_PLAN, TASK_TYPES } from '../data/plans';

function StatusLabel({ status }) {
  if (status === 'done')        return { label: 'Done', color: colors.success, icon: 'checkmark-circle' };
  if (status === 'in_progress') return { label: 'In flight', color: colors.btnFrom, icon: 'pulse' };
  if (status === 'missed')      return { label: 'Missed', color: colors.danger, icon: 'close-circle' };
  return { label: 'Upcoming', color: colors.textMuted, icon: 'time-outline' };
}

export default function ExecutionTrackerScreen({ navigation }) {
  const plan = TODAY_PLAN;
  const stats = useMemo(() => {
    const done = plan.tasks.filter(t => t.status === 'done').length;
    const live = plan.tasks.filter(t => t.status === 'in_progress').length;
    const missed = plan.tasks.filter(t => t.status === 'missed').length;
    const upcoming = plan.tasks.length - done - live - missed;
    const pct = Math.round((done / plan.tasks.length) * 100);
    return { done, live, missed, upcoming, pct };
  }, [plan]);

  const onTrack = stats.missed === 0;

  return (
    <View style={s.root}>
      <StatusBar barStyle="light-content" />
      <ScrollView contentContainerStyle={{ paddingBottom: 32 }} showsVerticalScrollIndicator={false}>
        <LinearGradient
          colors={onTrack ? ['#0F3D2E', '#10B981'] : ['#7F1D1D', '#EF4444']}
          start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
          style={s.header}
        >
          <View style={s.headerTop}>
            <Pressable onPress={() => navigation && navigation.goBack && navigation.goBack()} style={s.back}>
              <Ionicons name="chevron-back" size={22} color="#fff" />
            </Pressable>
            <Text style={s.kicker}>LIVE TRACKER · {plan.date}</Text>
            <View style={{ width: 22 }} />
          </View>

          <View style={s.bigRow}>
            <View style={s.ring}>
              <View style={[s.ringInner, { borderColor: 'rgba(255,255,255,0.2)' }]}>
                <Text style={s.ringPct}>{stats.pct}%</Text>
                <Text style={s.ringSub}>complete</Text>
              </View>
            </View>
            <View style={{ flex: 1, marginLeft: 16 }}>
              <Text style={s.bigTitle}>{onTrack ? 'On track' : 'At risk'}</Text>
              <Text style={s.bigSub}>
                {stats.done} done · {stats.live} in flight · {stats.upcoming} ahead
              </Text>
              <View style={s.discipline}>
                <Ionicons name="ribbon" size={14} color="#fff" />
                <Text style={s.disciplineText}>Discipline 92 · top 8% in cluster</Text>
              </View>
            </View>
          </View>
        </LinearGradient>

        {/* Vertical timeline */}
        <Text style={s.sectionTitle}>Today, hour by hour</Text>
        <View style={s.timeline}>
          {plan.tasks.map((task, i) => {
            const meta = TASK_TYPES[task.type];
            const status = StatusLabel({ status: task.status });
            const last = i === plan.tasks.length - 1;
            return (
              <View key={task.id} style={s.tlRow}>
                <View style={s.tlLeft}>
                  <Text style={s.tlTimePlanned}>{task.time}</Text>
                  {task.actual_at && (
                    <Text style={[s.tlTimeActual, task.actual_at > task.time && { color: colors.warning }]}>
                      {task.actual_at}
                    </Text>
                  )}
                </View>

                <View style={s.tlCenter}>
                  <View style={[s.tlDot, { backgroundColor: status.color, borderColor: '#fff' }]} />
                  {!last && <View style={s.tlLine} />}
                </View>

                <View style={s.tlCard}>
                  <View style={s.tlCardTop}>
                    <View style={[s.tlIcon, { backgroundColor: meta.color + '18' }]}>
                      <Ionicons name={meta.icon} size={14} color={meta.color} />
                    </View>
                    <Text style={s.tlTitle} numberOfLines={1}>{task.title}</Text>
                    <View style={[s.statusPill, { backgroundColor: status.color + '18', borderColor: status.color }]}>
                      <Text style={[s.statusText, { color: status.color }]}>{status.label}</Text>
                    </View>
                  </View>
                  <Text style={s.tlMeta}>
                    {meta.label}{task.lead ? ` · ${task.lead}` : ''} · {task.dur}m
                    {task.outcome ? ` · ${task.outcome}` : ''}
                  </Text>
                  {task.note ? <Text style={s.tlNote}>{task.note}</Text> : null}
                </View>
              </View>
            );
          })}
        </View>

        <View style={s.tipCard}>
          <Ionicons name="sparkles" size={14} color={colors.btnFrom} />
          <Text style={s.tipText}>
            Anaya is watching. If you skip a planned visit, she'll suggest a recovery slot before 7 PM.
          </Text>
        </View>

        <Text style={s.footnote}>
          Compared against daily_planner · diff feeds compute_bd_discipline()
        </Text>
      </ScrollView>
    </View>
  );
}

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: { paddingTop: 54, paddingHorizontal: 18, paddingBottom: 24, borderBottomLeftRadius: 24, borderBottomRightRadius: 24 },
  headerTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 16 },
  back: { width: 32, height: 32, borderRadius: 16, alignItems: 'center', justifyContent: 'center', backgroundColor: 'rgba(255,255,255,0.18)' },
  kicker: { color: 'rgba(255,255,255,0.85)', fontSize: 11, letterSpacing: 1.4, fontWeight: '700' },
  bigRow: { flexDirection: 'row', alignItems: 'center' },
  ring: { width: 96, height: 96, borderRadius: 48, alignItems: 'center', justifyContent: 'center', backgroundColor: 'rgba(255,255,255,0.12)' },
  ringInner: { width: 76, height: 76, borderRadius: 38, borderWidth: 4, alignItems: 'center', justifyContent: 'center' },
  ringPct: { color: '#fff', fontWeight: '800', fontSize: 22 },
  ringSub: { color: 'rgba(255,255,255,0.7)', fontSize: 9, marginTop: -2 },
  bigTitle: { color: '#fff', fontWeight: '800', fontSize: 26 },
  bigSub: { color: 'rgba(255,255,255,0.85)', fontSize: 12, marginTop: 4 },
  discipline: { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 10, alignSelf: 'flex-start', backgroundColor: 'rgba(0,0,0,0.18)', paddingHorizontal: 10, paddingVertical: 5, borderRadius: 999 },
  disciplineText: { color: '#fff', fontWeight: '700', fontSize: 11 },
  sectionTitle: { marginTop: 18, marginHorizontal: 18, marginBottom: 8, fontSize: 12, fontWeight: '800', color: colors.textMuted, letterSpacing: 1 },
  timeline: { paddingHorizontal: 12 },
  tlRow: { flexDirection: 'row', minHeight: 70 },
  tlLeft: { width: 44, alignItems: 'flex-end', paddingTop: 4, paddingRight: 6 },
  tlTimePlanned: { color: colors.text, fontWeight: '700', fontSize: 12 },
  tlTimeActual: { color: colors.success, fontSize: 10, marginTop: 1, fontWeight: '700' },
  tlCenter: { width: 22, alignItems: 'center', paddingTop: 6 },
  tlDot: { width: 12, height: 12, borderRadius: 6, borderWidth: 2 },
  tlLine: { flex: 1, width: 2, backgroundColor: colors.border, marginTop: 2 },
  tlCard: { flex: 1, backgroundColor: colors.card, borderRadius: 12, borderWidth: 1, borderColor: colors.border, padding: 10, marginBottom: 10, marginLeft: 4 },
  tlCardTop: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  tlIcon: { width: 26, height: 26, borderRadius: 8, alignItems: 'center', justifyContent: 'center' },
  tlTitle: { flex: 1, color: colors.text, fontWeight: '700', fontSize: 12 },
  statusPill: { paddingHorizontal: 7, paddingVertical: 2, borderRadius: 999, borderWidth: 1 },
  statusText: { fontSize: 9, fontWeight: '800', letterSpacing: 0.4 },
  tlMeta: { color: colors.textMuted, fontSize: 11, marginTop: 6 },
  tlNote: { color: colors.textMuted, fontStyle: 'italic', fontSize: 11, marginTop: 3 },
  tipCard: { flexDirection: 'row', alignItems: 'center', gap: 10, marginHorizontal: 16, marginTop: 6, backgroundColor: '#F4F1FF', borderColor: colors.btnFrom + '33', borderWidth: 1, padding: 12, borderRadius: 12 },
  tipText: { color: colors.text, fontSize: 12, flex: 1, lineHeight: 16 },
  footnote: { color: colors.textMuted, fontSize: 10, textAlign: 'center', marginTop: 18, marginHorizontal: 24, lineHeight: 14 },
});
