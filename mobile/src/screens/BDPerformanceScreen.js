/**
 * BDPerformanceScreen - Single dashboard surface for a BD's performance.
 *
 * Five tabs:
 *   FUNNEL   - opportunity stages, target vs achievement
 *   ACTIVITY - tasks planned vs done, latency, MoM rate
 *   MEETING  - migration 011 surface: mix (Fresh/RP/NO RP/Only Got Detail),
 *              capture compliance (Photo, GPS, MoM, Duration), spend
 *   DISCIPLINE - plan submission, approval SLA, day-ceremony adherence
 *   EXPENSE  - cash wallet, advances, variance breaches
 *
 * Opens for own self when no route param. CM/RM passes bd_id to view a report.
 *
 * Backends:
 *   GET /api/bd_performance/snapshot?bd_id=&from=&to=
 *   GET /api/meeting_economics/scoreboard?bd_id=&from=&to=
 *   GET /api/meeting_economics/mix?bd_id=&from=&to=
 *   GET /api/meeting_economics/capture?bd_id=&from=&to=
 *   GET /api/discipline/bd_score?bd_id=&from=&to=
 *   GET /api/expense/bd_summary?bd_id=&from=&to=
 */

import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, SafeAreaView,
} from 'react-native';
import { api } from '../lib/api';

const TABS = [
  { key: 'funnel',   label: 'Funnel' },
  { key: 'activity', label: 'Activity' },
  { key: 'meeting',  label: 'Meeting' },
  { key: 'discipline', label: 'Discipline' },
  { key: 'expense',  label: 'Expense' },
];

const FMT_INR = (v) => {
  v = Number(v || 0);
  if (v >= 1e7) return `Rs ${(v / 1e7).toFixed(2)} cr`;
  if (v >= 1e5) return `Rs ${(v / 1e5).toFixed(1)} L`;
  if (v >= 1e3) return `Rs ${(v / 1e3).toFixed(1)}k`;
  return `Rs ${Math.round(v)}`;
};
const FMT_PCT = (v) => `${Math.round((v || 0) * (v <= 1 ? 100 : 1))}%`;

export default function BDPerformanceScreen({ route, navigation }) {
  const bdId    = route?.params?.bd_id || 'me';
  const bdName  = route?.params?.bd_name || 'My performance';
  const initial = route?.params?.tab || 'meeting';

  const [tab, setTab] = useState(initial);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [snap, setSnap] = useState(null);
  const [meeting, setMeeting] = useState(null);
  const [discipline, setDiscipline] = useState(null);
  const [expense, setExpense] = useState(null);

  const today = new Date();
  const to    = today.toISOString().slice(0, 10);
  const fromD = new Date(today); fromD.setDate(fromD.getDate() - 7);
  const from  = fromD.toISOString().slice(0, 10);
  const qs    = `bd_id=${bdId}&from=${from}&to=${to}`;

  const load = useCallback(async () => {
    try {
      const [s1, sb, mix, cap, disc, exp] = await Promise.all([
        api.get(`/api/bd_performance/snapshot?${qs}`),
        api.get(`/api/meeting_economics/scoreboard?${qs}`),
        api.get(`/api/meeting_economics/mix?${qs}`),
        api.get(`/api/meeting_economics/capture?${qs}`),
        api.get(`/api/discipline/bd_score?${qs}`),
        api.get(`/api/expense/bd_summary?${qs}`),
      ]);
      setSnap(s1?.ok ? s1.data : null);
      setMeeting({
        scoreboard: sb?.ok ? sb.data : null,
        mix: mix?.ok ? mix.data : null,
        capture: cap?.ok ? cap.data : null,
      });
      setDiscipline(disc?.ok ? disc.data : null);
      setExpense(exp?.ok ? exp.data : null);
    } catch (e) { console.warn(e); }
    finally { setLoading(false); setRefreshing(false); }
  }, [qs]);

  useEffect(() => { setLoading(true); load(); }, [load]);

  if (loading) return (
    <SafeAreaView style={s.center}>
      <ActivityIndicator />
      <Text style={s.muted}>Loading performance...</Text>
    </SafeAreaView>
  );

  return (
    <SafeAreaView style={s.container}>
      <View style={s.header}>
        <Text style={s.title}>{bdName}</Text>
        <Text style={s.subtitle}>BD performance - {from} to {to}</Text>
      </View>

      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={s.tabBar} contentContainerStyle={{ paddingHorizontal: 8 }}>
        {TABS.map(t => (
          <TouchableOpacity key={t.key} onPress={() => setTab(t.key)}
            style={[s.tab, tab === t.key && s.tabActive]}>
            <Text style={tab === t.key ? s.tabActiveText : s.tabText}>{t.label}</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      <ScrollView
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
        contentContainerStyle={{ paddingBottom: 80 }}>
        {tab === 'funnel'     && <FunnelTab     snap={snap} />}
        {tab === 'activity'   && <ActivityTab   snap={snap} />}
        {tab === 'meeting'    && <MeetingReport data={meeting} />}
        {tab === 'discipline' && <DisciplineTab data={discipline} />}
        {tab === 'expense'    && <ExpenseTab    data={expense} />}
      </ScrollView>
    </SafeAreaView>
  );
}

/* ---------- Funnel ---------- */
function FunnelTab({ snap }) {
  if (!snap?.funnel) return <Empty msg="No funnel data." />;
  const f = snap.funnel;
  return (
    <View>
      <View style={s.kpiRow}>
        <Kpi label="Open opps"  value={String(f.open_count || 0)} />
        <Kpi label="Pipeline"   value={FMT_INR(f.pipeline_value)} />
        <Kpi label="Won MTD"    value={FMT_INR(f.won_mtd)} />
        <Kpi label="Win rate"   value={FMT_PCT(f.win_rate)} />
      </View>
      <Section title="Stage breakdown">
        {(f.stages || []).map((st, i) => (
          <Row key={i} k={`${st.name} (${st.count})`} v={FMT_INR(st.value)} />
        ))}
      </Section>
      <Section title="Target vs achievement">
        <Row k="Target FY"     v={FMT_INR(f.target_fy)} />
        <Row k="Achieved FY"   v={FMT_INR(f.achieved_fy)} />
        <Row k="Attainment"    v={FMT_PCT(f.attainment)} />
      </Section>
    </View>
  );
}

/* ---------- Activity ---------- */
function ActivityTab({ snap }) {
  if (!snap?.activity) return <Empty msg="No activity data." />;
  const a = snap.activity;
  return (
    <View>
      <View style={s.kpiRow}>
        <Kpi label="Planned"     value={String(a.planned || 0)} />
        <Kpi label="Done"        value={String(a.done || 0)} />
        <Kpi label="Completion"  value={FMT_PCT(a.completion_rate)} />
        <Kpi label="Latency"     value={`${Math.round(a.avg_latency_min || 0)}m`} />
      </View>
      <Section title="By type">
        <Row k="Calls"    v={`${a.by_type?.call?.done || 0} of ${a.by_type?.call?.planned || 0}`} />
        <Row k="Emails"   v={`${a.by_type?.email?.done || 0} of ${a.by_type?.email?.planned || 0}`} />
        <Row k="Meetings" v={`${a.by_type?.meeting?.done || 0} of ${a.by_type?.meeting?.planned || 0}`} />
      </Section>
      <Section title="Purpose achieved">
        <Row k="Yes" v={String(a.purpose_yes || 0)} />
        <Row k="No"  v={String(a.purpose_no || 0)} />
        <Row k="Rate" v={FMT_PCT(a.purpose_rate)} />
      </Section>
    </View>
  );
}

/* ---------- MEETING REPORT (focal tab, migration 011) ---------- */
function MeetingReport({ data }) {
  if (!data || (!data.scoreboard && !data.mix && !data.capture)) {
    return <Empty msg="No meeting report yet for this BD." />;
  }
  const sb  = data.scoreboard || {};
  const mix = data.mix || {};
  const cap = data.capture || {};
  const totals = sb.totals || sb;

  const captureFlags = [];
  if ((cap.with_gps_pct || 0) < 0.80)   captureFlags.push('GPS below 80 percent');
  if ((cap.with_photo_pct || 0) < 0.80) captureFlags.push('Photo below 80 percent');
  if ((cap.with_mom_pct || 0) < 0.80)   captureFlags.push('MoM below 80 percent');

  return (
    <View>
      {/* Headline strip */}
      <View style={s.kpiRow}>
        <Kpi label="Total"  value={String(totals.total || 0)} />
        <Kpi label="Fresh"  value={String(mix.fresh || 0)} />
        <Kpi label="RP"     value={String(mix.rp || 0)} />
        <Kpi label="NO RP"  value={String(mix.no_rp || 0)} />
      </View>

      {/* Mix - key lever */}
      <Section title="Meeting mix - key lever">
        <View style={s.mixRow}>
          <MixPill label="Fresh" value={mix.fresh || 0} tone="good" />
          <MixPill label="RP" value={mix.rp || 0} tone="good" />
          <MixPill label="NO RP" value={mix.no_rp || 0} tone="bad" />
          <MixPill label="Only Got Detail" value={mix.only_got_detail || 0} tone="warn" />
        </View>
        <Row k="Fresh share"  v={FMT_PCT(mix.fresh_share)} />
        <Row k="RP share"     v={FMT_PCT(mix.rp_share)} />
        <Row k="NO RP share"  v={FMT_PCT(mix.no_rp_share)} />
        <Row k="OGD share"    v={FMT_PCT(mix.only_got_detail_share)} />
      </Section>

      {/* Capture compliance - key lever */}
      <Section title="Capture compliance - key lever" tone={captureFlags.length ? 'warn' : null}>
        <View style={s.capGrid}>
          <CapCell label="Photo"           pct={cap.with_photo_pct || 0}    threshold={80} />
          <CapCell label="GPS"             pct={cap.with_gps_pct || 0}      threshold={80} />
          <CapCell label="MoM filled"      pct={cap.with_mom_pct || 0}      threshold={80} />
          <CapCell label="Duration logged" pct={cap.with_duration_pct || 0} threshold={80} />
        </View>
        <Row k="Overall capture"   v={FMT_PCT(cap.overall_pct)} />
        <Row k="Avg real duration" v={`${Math.round(cap.avg_real_min || 0)} min`} />
        {captureFlags.length > 0 && (
          <View style={s.flagRow}>
            {captureFlags.map(f => <Text key={f} style={s.flagPill}>{f}</Text>)}
          </View>
        )}
      </Section>

      {/* Outcome */}
      <Section title="Outcome">
        <Row k="Initiated"    v={String(totals.initiated || 0)} />
        <Row k="Completed"    v={String(totals.completed || 0)} />
        <Row k="MoM approved" v={String(totals.mom_approved || 0)} />
        <Row k="Purpose rate" v={FMT_PCT(totals.purpose_rate)} />
      </Section>

      {/* Spend */}
      <Section title="Spend per meeting">
        <Row k="Total spent"      v={FMT_INR(totals.spend)} />
        <Row k="Cost per meeting" v={FMT_INR(totals.cost_per_meeting)} />
        <Row k="Unlinked expenses" v={FMT_PCT(totals.unlinked_pct)} />
      </Section>
    </View>
  );
}

/* ---------- Discipline ---------- */
function DisciplineTab({ data }) {
  if (!data) return <Empty msg="No discipline score yet." />;
  return (
    <View>
      <View style={s.kpiRow}>
        <Kpi label="Score"   value={String(data.score || 0)} />
        <Kpi label="On time" value={FMT_PCT(data.on_time_rate)} />
        <Kpi label="Same day" value={String(data.same_day_count || 0)} />
        <Kpi label="Rejects" value={String(data.rejected_count || 0)} />
      </View>
      <Section title="Day ceremony">
        <Row k="Day starts on time" v={`${data.day_start_on_time || 0} of ${data.day_start_total || 0}`} />
        <Row k="Day closes done"    v={`${data.day_close_done || 0} of ${data.day_close_total || 0}`} />
        <Row k="Plan submit cutoff" v={data.plan_cutoff_met ? 'Met' : 'Breached'} />
      </Section>
      <Section title="Approval SLA (CM)">
        <Row k="CM cutoff hits"  v={FMT_PCT(data.cm_cutoff_rate)} />
        <Row k="Avg approval m"  v={`${Math.round(data.cm_avg_min || 0)}m`} />
      </Section>
    </View>
  );
}

/* ---------- Expense ---------- */
function ExpenseTab({ data }) {
  if (!data) return <Empty msg="No expense data yet." />;
  return (
    <View>
      <View style={s.kpiRow}>
        <Kpi label="Spent"    value={FMT_INR(data.total_spent)} />
        <Kpi label="Open adv" value={FMT_INR(data.open_advances)} />
        <Kpi label="Variance" value={String(data.variance_breach_count || 0)} />
        <Kpi label="Linked"   value={FMT_PCT(data.linked_pct)} />
      </View>
      <Section title="Cash wallet">
        <Row k="Allot today"   v={FMT_INR(data.cash_allot_today)} />
        <Row k="Refunds"       v={FMT_INR(data.cash_refunds)} />
        <Row k="Plan blocked Rs 500" v={String(data.plan_blocked_count || 0)} />
      </Section>
      <Section title="Variance breaches">
        <Row k="Over 20 percent" v={String(data.variance_breach_count || 0)} />
        <Row k="Pending dual approval" v={String(data.dual_apr_pending || 0)} />
        <Row k="Stuck over 12 hours" v={String(data.dual_apr_stuck || 0)} />
      </Section>
    </View>
  );
}

/* ---------- shared components ---------- */
const Kpi = ({ label, value }) => (
  <View style={s.kpi}><Text style={s.kpiV}>{value}</Text><Text style={s.kpiL}>{label}</Text></View>
);
const MixPill = ({ label, value, tone }) => {
  const bg = tone === 'good' ? '#E6F4EA' : tone === 'bad' ? '#FCE8E6' : '#FFF3E0';
  const fg = tone === 'good' ? '#1E7F3C' : tone === 'bad' ? '#B3271B' : '#A66B00';
  return (
    <View style={[s.mixPill, { backgroundColor: bg }]}>
      <Text style={[s.mixPillV, { color: fg }]}>{value}</Text>
      <Text style={[s.mixPillL, { color: fg }]}>{label}</Text>
    </View>
  );
};
const CapCell = ({ label, pct, threshold }) => {
  const v = Number(pct || 0);
  const fail = v < (threshold / 100);
  return (
    <View style={s.capCell}>
      <Text style={[s.capV, fail && s.bad]}>{FMT_PCT(v)}</Text>
      <Text style={s.capL}>{label}</Text>
      {fail && <Text style={s.capFlag}>below {threshold}%</Text>}
    </View>
  );
};
const Row = ({ k, v }) => (
  <View style={s.row}><Text style={s.rowK}>{k}</Text><Text style={s.rowV}>{v}</Text></View>
);
const Section = ({ title, children, tone }) => (
  <View style={[s.section, tone === 'warn' && s.sectionWarn]}>
    <Text style={s.sectionTitle}>{title}</Text>
    {children}
  </View>
);
const Empty = ({ msg }) => <Text style={s.muted}>{msg}</Text>;

const s = StyleSheet.create({
  container:    { flex: 1, backgroundColor: '#F7F8FA' },
  center:       { flex: 1, alignItems: 'center', justifyContent: 'center' },
  header:       { padding: 16, backgroundColor: '#FFF', borderBottomWidth: 1, borderBottomColor: '#E5E8EB' },
  title:        { fontSize: 18, fontWeight: '700', color: '#1B2A4E' },
  subtitle:     { color: '#5B6478', marginTop: 2 },
  muted:        { color: '#7A8395', padding: 16 },
  tabBar:       { maxHeight: 48, backgroundColor: '#FFF', borderBottomWidth: 1, borderBottomColor: '#E5E8EB' },
  tab:          { paddingVertical: 10, paddingHorizontal: 14, marginRight: 6, alignSelf: 'center' },
  tabActive:    { borderBottomWidth: 3, borderBottomColor: '#1B2A4E' },
  tabText:      { color: '#5B6478', fontWeight: '600' },
  tabActiveText:{ color: '#1B2A4E', fontWeight: '800' },
  kpiRow:       { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', padding: 12 },
  kpi:          { width: '23%', backgroundColor: '#FFF', padding: 10, borderRadius: 8, alignItems: 'center', shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 3, elevation: 1 },
  kpiV:         { fontSize: 15, fontWeight: '700', color: '#1B2A4E' },
  kpiL:         { fontSize: 11, color: '#7A8395', marginTop: 2 },
  section:      { backgroundColor: '#FFF', marginHorizontal: 12, marginTop: 10, padding: 14, borderRadius: 8 },
  sectionWarn:  { borderLeftWidth: 4, borderLeftColor: '#D9A441' },
  sectionTitle: { fontSize: 13, fontWeight: '700', color: '#1B2A4E', marginBottom: 8, textTransform: 'uppercase', letterSpacing: 0.4 },
  row:          { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 4 },
  rowK:         { color: '#3A4458' },
  rowV:         { color: '#1B2A4E', fontWeight: '600' },
  mixRow:       { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  mixPill:      { flex: 1, marginRight: 6, paddingVertical: 10, borderRadius: 8, alignItems: 'center' },
  mixPillV:     { fontSize: 18, fontWeight: '800' },
  mixPillL:     { fontSize: 10, fontWeight: '600', marginTop: 2 },
  capGrid:      { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', marginBottom: 8 },
  capCell:      { width: '48%', paddingVertical: 8, paddingHorizontal: 8, marginBottom: 6, backgroundColor: '#F7F8FA', borderRadius: 6 },
  capV:         { fontSize: 18, fontWeight: '700', color: '#1B2A4E' },
  capL:         { fontSize: 11, color: '#7A8395', marginTop: 2 },
  capFlag:      { fontSize: 10, color: '#B3271B', marginTop: 2, fontWeight: '600' },
  bad:          { color: '#B3271B' },
  flagRow:      { flexDirection: 'row', flexWrap: 'wrap', marginTop: 8 },
  flagPill:     { backgroundColor: '#FCE8E6', color: '#B3271B', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 10, marginRight: 6, marginBottom: 4, fontSize: 11, fontWeight: '600' },
});
