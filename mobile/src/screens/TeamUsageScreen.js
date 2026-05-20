/**
 * TeamUsageScreen — read-only mobile tile for CMs and RMs.
 *
 * No chat. No agent UI. Just a daily snapshot of how the team is
 * spending time on the app. Calls UsageController endpoints.
 */

import React, { useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, RefreshControl, FlatList,
  TouchableOpacity, ActivityIndicator,
} from 'react-native';
import { api } from '../lib/api';

const C = {
  bg: '#F7F6F2', surface: '#FFFFFF', border: '#E6E3DC',
  ink: '#1B1A18', muted: '#7A7974', faint: '#BAB9B4',
  accent: '#01696F', accentSoft: '#E0F0F1', warn: '#964219',
  bar: '#01696F', barBg: '#E6E3DC',
};

function fmtMin(sec) {
  if (!sec) return '0m';
  const m = Math.round(sec / 60);
  if (m < 60) return `${m}m`;
  return `${Math.floor(m/60)}h ${m%60}m`;
}

function Row({ name, role, total, planning, leads, mom, review, actions, latency }) {
  const max = Math.max(planning, leads, mom, review, 60);
  const seg = (v, color) => ({
    flex: Math.max(v, 1),
    backgroundColor: color,
    height: 6,
  });
  return (
    <View style={styles.row}>
      <View style={styles.rowHead}>
        <View style={{flex:1}}>
          <Text style={styles.name}>{name}</Text>
          <Text style={styles.sub}>{role} . {fmtMin(total)} total . {actions || 0} actions</Text>
        </View>
        <Text style={styles.latency}>{latency ? `${Math.round(latency/60)}m` : '-'}</Text>
      </View>
      <View style={styles.barRow}>
        <View style={seg(planning, '#7A39BB')} />
        <View style={seg(leads,    '#006494')} />
        <View style={seg(mom,      '#964219')} />
        <View style={seg(review,   '#437A22')} />
      </View>
      <View style={styles.legendRow}>
        <Text style={styles.legend}>Plan {fmtMin(planning)}</Text>
        <Text style={styles.legend}>Leads {fmtMin(leads)}</Text>
        <Text style={styles.legend}>MoM {fmtMin(mom)}</Text>
        <Text style={styles.legend}>Review {fmtMin(review)}</Text>
      </View>
    </View>
  );
}

export default function TeamUsageScreen() {
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [presence, setPresence] = useState([]);
  const [rows, setRows] = useState([]);
  const [tab, setTab] = useState('today'); // today | 7d

  async function load() {
    try {
      const today = new Date().toISOString().slice(0, 10);
      const p = await api.get('/usage/api_live_presence');
      const d = await api.get(`/usage/api_daily_summary?date=${today}`);
      setPresence(p && p.presence ? p.presence : []);
      setRows(d && d.rows ? d.rows : []);
    } catch (e) {
      // demo fallback
      setPresence(DEMO_PRESENCE);
      setRows(DEMO_ROWS);
    } finally { setLoading(false); setRefreshing(false); }
  }

  useEffect(() => { load(); }, []);

  if (loading) return (
    <View style={[styles.container, {alignItems:'center', justifyContent:'center'}]}>
      <ActivityIndicator color={C.accent}/>
    </View>
  );

  const totalActive = presence.length;
  const teamTotal = rows.reduce((a,r)=>a + (r.total_time_sec||0), 0);

  return (
    <ScrollView style={styles.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={()=>{setRefreshing(true);load();}} tintColor={C.accent}/>}>
      <View style={styles.header}>
        <Text style={styles.h1}>Team usage</Text>
        <Text style={styles.h1sub}>Silent tracker . minute-bucketed</Text>
      </View>

      {/* live counters */}
      <View style={styles.kpiRow}>
        <View style={styles.kpi}><Text style={styles.kpiV}>{totalActive}</Text><Text style={styles.kpiL}>on app now</Text></View>
        <View style={styles.kpi}><Text style={styles.kpiV}>{rows.length}</Text><Text style={styles.kpiL}>active today</Text></View>
        <View style={styles.kpi}><Text style={styles.kpiV}>{fmtMin(teamTotal)}</Text><Text style={styles.kpiL}>team time</Text></View>
      </View>

      {/* live presence */}
      <Text style={styles.sectionH}>On app right now</Text>
      {presence.length === 0 ? (
        <Text style={styles.empty}>Nobody on app in the last 2 minutes.</Text>
      ) : (
        <View style={styles.presenceWrap}>
          {presence.slice(0, 8).map(p => (
            <View key={p.user_id} style={styles.chip}>
              <View style={styles.dot}/>
              <Text style={styles.chipText}>{p.name || `User ${p.user_id}`}</Text>
              <Text style={styles.chipSub}>{p.current_screen || 'idle'}</Text>
            </View>
          ))}
        </View>
      )}

      {/* daily rows */}
      <Text style={styles.sectionH}>Today by user</Text>
      <FlatList
        data={rows}
        scrollEnabled={false}
        keyExtractor={r => String(r.user_id)}
        renderItem={({item:r})=> (
          <Row name={r.name || `User ${r.user_id}`} role={r.role}
               total={r.total_time_sec}
               planning={r.time_planning_sec} leads={r.time_leads_sec}
               mom={r.time_mom_sec} review={r.time_review_sec}
               actions={r.actions_count} latency={r.avg_task_latency_s}/>
        )}
      />
      <View style={{height: 40}}/>
    </ScrollView>
  );
}

const DEMO_PRESENCE = [
  { user_id: 42, name: 'Priya Menon',  role: 'BD', current_screen: 'DayPlan' },
  { user_id: 43, name: 'Ravi Kumar',   role: 'BD', current_screen: 'MoMDrafter' },
  { user_id: 12, name: 'Anjali Rao',   role: 'CM', current_screen: 'PlanApproval' },
];
const DEMO_ROWS = [
  { user_id: 42, name: 'Priya Menon', role:'BD',
    total_time_sec: 4380, time_planning_sec: 720, time_leads_sec: 1500,
    time_mom_sec: 1200, time_review_sec: 540, actions_count: 38, avg_task_latency_s: 240 },
  { user_id: 43, name: 'Ravi Kumar', role:'BD',
    total_time_sec: 3120, time_planning_sec: 600, time_leads_sec: 1080,
    time_mom_sec: 660, time_review_sec: 480, actions_count: 27, avg_task_latency_s: 360 },
  { user_id: 44, name: 'Anita Sharma', role:'BD',
    total_time_sec: 1860, time_planning_sec: 240, time_leads_sec: 720,
    time_mom_sec: 240, time_review_sec: 360, actions_count: 14, avg_task_latency_s: 540 },
  { user_id: 45, name: 'Vikram Tyagi', role:'BD',
    total_time_sec: 2640, time_planning_sec: 420, time_leads_sec: 960,
    time_mom_sec: 780, time_review_sec: 240, actions_count: 22, avg_task_latency_s: 300 },
  { user_id: 46, name: 'Sneha Iyer', role:'BD',
    total_time_sec: 3780, time_planning_sec: 540, time_leads_sec: 1320,
    time_mom_sec: 1080, time_review_sec: 540, actions_count: 31, avg_task_latency_s: 270 },
  { user_id: 12, name: 'Anjali Rao', role:'CM',
    total_time_sec: 5220, time_planning_sec: 1620, time_leads_sec: 480,
    time_mom_sec: 360, time_review_sec: 2580, actions_count: 47, avg_task_latency_s: 180 },
];

const styles = StyleSheet.create({
  container: { flex:1, backgroundColor: C.bg },
  header: { paddingHorizontal: 18, paddingTop: 26, paddingBottom: 12 },
  h1: { fontSize: 26, fontWeight: '700', color: C.ink },
  h1sub: { fontSize: 12, color: C.muted, marginTop: 4 },
  kpiRow: { flexDirection:'row', paddingHorizontal: 14, gap: 10, marginBottom: 14 },
  kpi: { flex:1, backgroundColor: C.surface, borderRadius: 12, padding: 12,
         borderWidth: 1, borderColor: C.border },
  kpiV: { fontSize: 22, fontWeight:'700', color: C.ink },
  kpiL: { fontSize: 11, color: C.muted, marginTop: 4 },
  sectionH: { fontSize: 13, fontWeight: '600', color: C.muted, textTransform: 'uppercase',
              letterSpacing: 1, paddingHorizontal: 18, marginTop: 12, marginBottom: 8 },
  empty: { paddingHorizontal: 18, color: C.faint, fontSize: 13 },
  presenceWrap: { paddingHorizontal: 14, flexDirection:'row', flexWrap:'wrap', gap: 8 },
  chip: { backgroundColor: C.accentSoft, borderRadius: 999, paddingHorizontal: 12, paddingVertical: 6,
          flexDirection:'row', alignItems:'center', gap: 6 },
  dot: { width:8, height:8, borderRadius:4, backgroundColor: C.accent },
  chipText: { color: C.ink, fontSize: 12, fontWeight: '600' },
  chipSub: { color: C.muted, fontSize: 11 },
  row: { marginHorizontal: 14, backgroundColor: C.surface, borderRadius: 12, padding: 14,
         borderWidth: 1, borderColor: C.border, marginBottom: 10 },
  rowHead: { flexDirection:'row', alignItems:'center' },
  name: { fontSize: 15, fontWeight:'600', color: C.ink },
  sub:  { fontSize: 11, color: C.muted, marginTop: 2 },
  latency: { fontSize: 13, color: C.accent, fontWeight: '600' },
  barRow: { flexDirection:'row', height: 6, borderRadius: 3, overflow:'hidden',
            backgroundColor: C.barBg, marginTop: 10, gap: 1 },
  legendRow: { flexDirection:'row', justifyContent:'space-between', marginTop: 8 },
  legend: { fontSize: 10, color: C.muted },
});
