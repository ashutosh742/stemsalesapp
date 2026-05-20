// AutoTasksScreen — system-captured emails (template engine) and calls (Knowlarity dialer).
// Backend tables: tblcallevents (atid=1 call, atid=2 email), AIAgents/EmailLogs_model,
// AIAgents/CallLogs_model.

import React, { useState, useMemo } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { AUTO_TASKS_TODAY } from '../data/plans';

function fmtSec(s) {
  if (!s) return '—';
  const m = Math.floor(s / 60);
  const r = s % 60;
  return `${m}m ${r.toString().padStart(2, '0')}s`;
}

function EmailRow({ row }) {
  const opened = row.opens > 0;
  return (
    <View style={s.row}>
      <View style={[s.icon, { backgroundColor: '#9B59B618' }]}>
        <Ionicons name="mail" size={16} color="#9B59B6" />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={s.subject} numberOfLines={1}>{row.subject}</Text>
        <Text style={s.meta}>to {row.to} · {row.lead}</Text>
        <View style={s.statBar}>
          <View style={[s.statChip, { backgroundColor: '#10B98118', borderColor: '#10B98155' }]}>
            <Ionicons name="checkmark-done" size={10} color={colors.success} />
            <Text style={[s.statTxt, { color: colors.success }]}>delivered</Text>
          </View>
          <View style={[s.statChip, opened && { backgroundColor: '#3498DB18', borderColor: '#3498DB55' }]}>
            <Ionicons name="eye-outline" size={10} color={opened ? colors.info : colors.textMuted} />
            <Text style={[s.statTxt, { color: opened ? colors.info : colors.textMuted }]}>{row.opens} open{row.opens === 1 ? '' : 's'}</Text>
          </View>
          <View style={[s.statChip, row.clicks > 0 && { backgroundColor: '#F59E0B18', borderColor: '#F59E0B55' }]}>
            <Ionicons name="hand-right-outline" size={10} color={row.clicks > 0 ? colors.warning : colors.textMuted} />
            <Text style={[s.statTxt, { color: row.clicks > 0 ? colors.warning : colors.textMuted }]}>{row.clicks} click{row.clicks === 1 ? '' : 's'}</Text>
          </View>
        </View>
        <Text style={s.tplMeta}>template <Text style={s.tplCode}>{row.template}</Text></Text>
      </View>
      <Text style={s.when}>{row.when}</Text>
    </View>
  );
}

function CallRow({ row }) {
  const connected = row.outcome === 'connected';
  return (
    <View style={s.row}>
      <View style={[s.icon, { backgroundColor: '#3498DB18' }]}>
        <Ionicons name="call" size={16} color={colors.info} />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={s.subject}>{row.number}</Text>
        <Text style={s.meta}>{row.lead} · via {row.dialer}</Text>
        <View style={s.statBar}>
          <View style={[s.statChip, connected ? { backgroundColor: '#10B98118', borderColor: '#10B98155' } : { backgroundColor: '#EF444418', borderColor: '#EF444455' }]}>
            <Ionicons name={connected ? 'checkmark-circle' : 'close-circle'} size={10} color={connected ? colors.success : colors.danger} />
            <Text style={[s.statTxt, { color: connected ? colors.success : colors.danger }]}>{row.outcome.replace('_', ' ')}</Text>
          </View>
          <View style={s.statChip}>
            <Ionicons name="time-outline" size={10} color={colors.textMuted} />
            <Text style={[s.statTxt, { color: colors.textMuted }]}>{fmtSec(row.duration_s)}</Text>
          </View>
          {row.recording && (
            <Pressable style={[s.statChip, { backgroundColor: colors.btnFrom + '18', borderColor: colors.btnFrom + '55' }]}>
              <Ionicons name="play" size={10} color={colors.btnFrom} />
              <Text style={[s.statTxt, { color: colors.btnFrom }]}>play</Text>
            </Pressable>
          )}
        </View>
      </View>
      <Text style={s.when}>{row.when}</Text>
    </View>
  );
}

export default function AutoTasksScreen({ navigation }) {
  const [tab, setTab] = useState('email');

  const filtered = useMemo(
    () => AUTO_TASKS_TODAY.filter(r => r.kind === tab),
    [tab]
  );

  const emailCount = AUTO_TASKS_TODAY.filter(r => r.kind === 'email').length;
  const callCount  = AUTO_TASKS_TODAY.filter(r => r.kind === 'call').length;

  return (
    <View style={s.root}>
      <StatusBar barStyle="light-content" />
      <ScrollView contentContainerStyle={{ paddingBottom: 32 }} showsVerticalScrollIndicator={false}>
        <LinearGradient
          colors={['#0F1B4C', '#3E21FB']}
          start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
          style={s.header}
        >
          <View style={s.headerTop}>
            <Pressable onPress={() => navigation && navigation.goBack && navigation.goBack()} style={s.back}>
              <Ionicons name="chevron-back" size={22} color="#fff" />
            </Pressable>
            <Text style={s.kicker}>AUTO-CAPTURED TODAY</Text>
            <View style={{ width: 22 }} />
          </View>
          <Text style={s.title}>Auto tasks</Text>
          <Text style={s.subtitle}>System-logged · no manual entry needed</Text>

          <View style={s.tabRow}>
            <Pressable
              style={[s.tab, tab === 'email' && s.tabActive]}
              onPress={() => setTab('email')}
            >
              <Ionicons name="mail" size={14} color={tab === 'email' ? colors.btnFrom : 'rgba(255,255,255,0.85)'} />
              <Text style={[s.tabText, tab === 'email' && { color: colors.btnFrom }]}>
                Emails · {emailCount}
              </Text>
            </Pressable>
            <Pressable
              style={[s.tab, tab === 'call' && s.tabActive]}
              onPress={() => setTab('call')}
            >
              <Ionicons name="call" size={14} color={tab === 'call' ? colors.btnFrom : 'rgba(255,255,255,0.85)'} />
              <Text style={[s.tabText, tab === 'call' && { color: colors.btnFrom }]}>
                Calls · {callCount}
              </Text>
            </Pressable>
          </View>
        </LinearGradient>

        <View style={s.banner}>
          <Ionicons name="information-circle" size={14} color={colors.info} />
          <Text style={s.bannerText}>
            Every send + dial logs to tblcallevents. Numbers below match init_call.{tab === 'email' ? 'emails_sent' : 'calls_made'}.
          </Text>
        </View>

        <View style={s.listWrap}>
          {filtered.map(row => (
            <View key={row.id}>
              {row.kind === 'email' ? <EmailRow row={row} /> : <CallRow row={row} />}
            </View>
          ))}
        </View>

        <Text style={s.footnote}>
          Source: AIAgents/EmailLogs_model + AIAgents/CallLogs_model · refreshed every 60s
        </Text>
      </ScrollView>
    </View>
  );
}

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: { paddingTop: 54, paddingHorizontal: 18, paddingBottom: 16, borderBottomLeftRadius: 24, borderBottomRightRadius: 24 },
  headerTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 10 },
  back: { width: 32, height: 32, borderRadius: 16, alignItems: 'center', justifyContent: 'center', backgroundColor: 'rgba(255,255,255,0.18)' },
  kicker: { color: 'rgba(255,255,255,0.85)', fontSize: 11, letterSpacing: 1.4, fontWeight: '700' },
  title: { color: '#fff', fontSize: 26, fontWeight: '800', marginTop: 2 },
  subtitle: { color: 'rgba(255,255,255,0.85)', fontSize: 13, marginTop: 4 },
  tabRow: { flexDirection: 'row', backgroundColor: 'rgba(0,0,0,0.18)', borderRadius: 12, padding: 4, marginTop: 14 },
  tab: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6, paddingVertical: 8, borderRadius: 10 },
  tabActive: { backgroundColor: '#fff' },
  tabText: { color: 'rgba(255,255,255,0.85)', fontWeight: '700', fontSize: 12 },
  banner: { flexDirection: 'row', alignItems: 'center', gap: 8, marginHorizontal: 16, marginTop: 14, padding: 10, borderRadius: 10, backgroundColor: '#EFF6FF', borderWidth: 1, borderColor: '#3498DB33' },
  bannerText: { color: '#1E3A8A', fontSize: 11, flex: 1, lineHeight: 15 },
  listWrap: { backgroundColor: colors.card, marginHorizontal: 16, marginTop: 12, borderRadius: 14, borderWidth: 1, borderColor: colors.border, overflow: 'hidden' },
  row: { flexDirection: 'row', padding: 12, borderBottomWidth: 1, borderBottomColor: colors.border, gap: 10 },
  icon: { width: 32, height: 32, borderRadius: 10, alignItems: 'center', justifyContent: 'center' },
  subject: { color: colors.text, fontWeight: '700', fontSize: 13 },
  meta: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
  statBar: { flexDirection: 'row', flexWrap: 'wrap', gap: 5, marginTop: 8 },
  statChip: { flexDirection: 'row', alignItems: 'center', gap: 3, paddingHorizontal: 7, paddingVertical: 3, borderRadius: 999, borderWidth: 1, borderColor: colors.border, backgroundColor: colors.cardAlt },
  statTxt: { fontSize: 10, fontWeight: '700' },
  tplMeta: { color: colors.textMuted, fontSize: 10, marginTop: 6 },
  tplCode: { fontFamily: 'Courier', color: colors.btnFrom, fontWeight: '700' },
  when: { color: colors.textMuted, fontSize: 11, fontWeight: '600' },
  footnote: { color: colors.textMuted, fontSize: 10, textAlign: 'center', marginTop: 14, marginHorizontal: 24, lineHeight: 14 },
});
