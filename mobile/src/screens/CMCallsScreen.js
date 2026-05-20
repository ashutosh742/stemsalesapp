// CMCallsScreen — Cluster Manager view of all calls across BDs in cluster.
// Live calls pulse · recordings playable · filter by BD chip.
//
// Backend mapping:
//   - List:      AIAgents/CallLogs_model::cluster_calls(cm_id, since)
//   - Live state: tblcallevents.live=1 rows pushed via WebSocket fallback to poll(15s)
//   - Recording: CallLogs_model::recording_url(call_id) → signed S3

import React, { useMemo, useRef, useEffect, useState } from 'react';
import { View, Text, ScrollView, Pressable, StyleSheet, Animated, Easing } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CM_CALLS_FEED, CLUSTER_BDS } from '../data/cm';

function LivePulse() {
  const v = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    Animated.loop(Animated.sequence([
      Animated.timing(v, { toValue: 1, duration: 800, easing: Easing.out(Easing.ease), useNativeDriver: true }),
      Animated.timing(v, { toValue: 0, duration: 800, easing: Easing.in(Easing.ease),  useNativeDriver: true }),
    ])).start();
  }, []);
  const scale = v.interpolate({ inputRange: [0, 1], outputRange: [1, 1.6] });
  const opacity = v.interpolate({ inputRange: [0, 1], outputRange: [0.9, 0] });
  return (
    <View style={{ width: 14, height: 14, alignItems: 'center', justifyContent: 'center' }}>
      <Animated.View style={{ position: 'absolute', width: 14, height: 14, borderRadius: 7, backgroundColor: '#EF4444', opacity, transform: [{ scale }] }} />
      <View style={{ width: 8, height: 8, borderRadius: 4, backgroundColor: '#EF4444' }} />
    </View>
  );
}

function formatDur(s) {
  if (s === 0) return '—';
  const m = Math.floor(s / 60);
  const sec = s % 60;
  return `${m}m ${String(sec).padStart(2, '0')}s`;
}

const OUTCOME_STYLE = {
  in_progress: { label: 'On call',    bg: 'rgba(239,68,68,0.12)',  border: 'rgba(239,68,68,0.35)',  fg: '#EF4444' },
  connected:   { label: 'Connected',  bg: 'rgba(16,185,129,0.10)', border: 'rgba(16,185,129,0.35)', fg: '#10B981' },
  no_answer:   { label: 'No answer',  bg: 'rgba(156,163,175,0.12)', border: 'rgba(156,163,175,0.40)', fg: '#6B7280' },
  busy:        { label: 'Busy',       bg: 'rgba(245,158,11,0.10)', border: 'rgba(245,158,11,0.35)', fg: '#F59E0B' },
};

export default function CMCallsScreen({ navigation }) {
  const [bdFilter, setBdFilter] = useState('all');
  const calls = useMemo(() =>
    bdFilter === 'all' ? CM_CALLS_FEED : CM_CALLS_FEED.filter(c => c.bd.id === bdFilter),
    [bdFilter]
  );

  const live = CM_CALLS_FEED.filter(c => c.live).length;
  const today = CM_CALLS_FEED.filter(c => !c.when.startsWith('Y')).length;
  const connected = CM_CALLS_FEED.filter(c => c.outcome === 'connected').length;
  const connectRate = Math.round((connected / today) * 100) || 0;

  return (
    <View style={{ flex: 1, backgroundColor: colors.cardAlt }}>
      <LinearGradient colors={['#0F172A', '#1E293B']} style={styles.hero}>
        <View style={styles.heroTop}>
          <Pressable onPress={() => navigation?.goBack?.()} style={styles.iconBtn}>
            <Ionicons name="chevron-back" size={20} color="#fff" />
          </Pressable>
          <Text style={styles.heroEyebrow}>CM · MUMBAI CLUSTER</Text>
          <View style={{ width: 36 }} />
        </View>
        <Text style={styles.heroTitle}>Calls</Text>
        <Text style={styles.heroSub}>Live across {CLUSTER_BDS.length} BDs · {today} today · {connectRate}% connect</Text>

        <View style={styles.statsRow}>
          <View style={styles.statCard}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
              <LivePulse />
              <Text style={styles.statNum}>{live}</Text>
            </View>
            <Text style={styles.statLabel}>on call</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNum}>{today}</Text>
            <Text style={styles.statLabel}>today</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={[styles.statNum, { color: '#10B981' }]}>{connectRate}%</Text>
            <Text style={styles.statLabel}>connect</Text>
          </View>
        </View>
      </LinearGradient>

      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.chipRow}>
        <Pressable onPress={() => setBdFilter('all')} style={[styles.chip, bdFilter === 'all' && styles.chipActive]}>
          <Text style={[styles.chipText, bdFilter === 'all' && styles.chipTextActive]}>All BDs</Text>
        </Pressable>
        {CLUSTER_BDS.map(bd => (
          <Pressable key={bd.id} onPress={() => setBdFilter(bd.id)} style={[styles.chip, bdFilter === bd.id && styles.chipActive]}>
            <View style={[styles.chipAvatar, { backgroundColor: bd.color }]}><Text style={styles.chipAvatarText}>{bd.initials}</Text></View>
            <Text style={[styles.chipText, bdFilter === bd.id && styles.chipTextActive]}>{bd.name.split(' ')[0]}</Text>
          </Pressable>
        ))}
      </ScrollView>

      <ScrollView contentContainerStyle={{ padding: 12, paddingBottom: 24 }}>
        {calls.map(c => {
          const o = OUTCOME_STYLE[c.outcome] || OUTCOME_STYLE.no_answer;
          return (
            <Pressable key={c.id} style={styles.row} onPress={() => navigation?.navigate?.('LeadDetail', { leadId: c.lead.id })}>
              <View style={[styles.avatar, { backgroundColor: c.bd.color }]}>
                <Text style={styles.avatarText}>{c.bd.initials}</Text>
              </View>
              <View style={{ flex: 1 }}>
                <View style={{ flexDirection: 'row', justifyContent: 'space-between' }}>
                  <Text style={styles.bdName}>{c.bd.name}</Text>
                  <Text style={styles.when}>{c.when}</Text>
                </View>
                <Text style={styles.leadLine}>
                  <Ionicons name="flash" size={11} color={colors.btnFrom} />  {c.lead.id} · {c.lead.name}
                </Text>
                <View style={styles.metaRow}>
                  <View style={[styles.pill, { backgroundColor: o.bg, borderColor: o.border }]}>
                    {c.live && <LivePulse />}
                    <Text style={[styles.pillText, { color: o.fg, marginLeft: c.live ? 6 : 0 }]}>{o.label}</Text>
                  </View>
                  <Text style={styles.metaText}>{formatDur(c.duration_s)}</Text>
                  {c.recording && c.recording !== 'live' && (
                    <View style={styles.recPill}>
                      <Ionicons name="play" size={10} color={colors.btnFrom} />
                      <Text style={styles.recText}>recording</Text>
                    </View>
                  )}
                  {c.sentiment === 'positive' && <Text style={styles.sentPos}>● positive</Text>}
                  {c.sentiment === 'neutral' && <Text style={styles.sentNeu}>● neutral</Text>}
                </View>
                {c.note && <Text style={styles.note}>{c.note}</Text>}
              </View>
            </Pressable>
          );
        })}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  hero: { paddingTop: 60, paddingBottom: 22, paddingHorizontal: 16 },
  heroTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  iconBtn: { width: 36, height: 36, borderRadius: 18, backgroundColor: 'rgba(255,255,255,0.12)', alignItems: 'center', justifyContent: 'center' },
  heroEyebrow: { color: 'rgba(255,255,255,0.7)', fontSize: 11, fontWeight: '800', letterSpacing: 1 },
  heroTitle: { color: '#fff', fontSize: 30, fontWeight: '800', marginTop: 18 },
  heroSub: { color: 'rgba(255,255,255,0.78)', fontSize: 13, marginTop: 6 },
  statsRow: { flexDirection: 'row', gap: 10, marginTop: 18 },
  statCard: { flex: 1, backgroundColor: 'rgba(255,255,255,0.08)', borderRadius: 12, padding: 12, borderWidth: 1, borderColor: 'rgba(255,255,255,0.08)' },
  statNum: { color: '#fff', fontSize: 22, fontWeight: '800' },
  statLabel: { color: 'rgba(255,255,255,0.7)', fontSize: 11, marginTop: 2, fontWeight: '600' },
  chipRow: { paddingHorizontal: 12, paddingTop: 12, paddingBottom: 4, gap: 8 },
  chip: { flexDirection: 'row', alignItems: 'center', gap: 6, backgroundColor: '#fff', borderColor: colors.border, borderWidth: 1, paddingHorizontal: 10, paddingVertical: 6, borderRadius: 999 },
  chipActive: { backgroundColor: colors.btnFrom, borderColor: colors.btnFrom },
  chipText: { fontSize: 12, color: colors.text, fontWeight: '600' },
  chipTextActive: { color: '#fff' },
  chipAvatar: { width: 18, height: 18, borderRadius: 9, alignItems: 'center', justifyContent: 'center' },
  chipAvatarText: { color: '#fff', fontSize: 9, fontWeight: '800' },
  row: { flexDirection: 'row', gap: 12, backgroundColor: '#fff', borderRadius: 14, padding: 12, marginTop: 10, borderWidth: 1, borderColor: colors.border },
  avatar: { width: 40, height: 40, borderRadius: 20, alignItems: 'center', justifyContent: 'center' },
  avatarText: { color: '#fff', fontWeight: '800', fontSize: 13 },
  bdName: { fontSize: 14, fontWeight: '700', color: colors.text },
  when: { fontSize: 11, color: colors.textMuted, fontWeight: '600' },
  leadLine: { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  metaRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 6, flexWrap: 'wrap' },
  pill: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 999, borderWidth: 1 },
  pillText: { fontSize: 10, fontWeight: '800' },
  metaText: { fontSize: 11, color: colors.textMuted, fontWeight: '700' },
  recPill: { flexDirection: 'row', alignItems: 'center', gap: 3, backgroundColor: 'rgba(62,33,251,0.08)', paddingHorizontal: 6, paddingVertical: 3, borderRadius: 999 },
  recText: { fontSize: 10, color: colors.btnFrom, fontWeight: '700' },
  sentPos: { fontSize: 10, color: '#10B981', fontWeight: '700' },
  sentNeu: { fontSize: 10, color: '#6B7280', fontWeight: '700' },
  note: { fontSize: 12, color: colors.text, marginTop: 6, fontStyle: 'italic' },
});
