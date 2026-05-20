/**
 * MeetingEconomicsScreen — Agent #7 (Meeting Economics)
 *
 * Three views in one screen, toggled by a segmented control:
 *   1. ME  — my own week (BD self-view)
 *   2. TEAM — every BD reporting to me, with flags (CM view)
 *   3. CLUSTER — cluster-level economics (RM view)
 *
 * Backend:
 *   GET /api/meeting_economics/summary?bd_id=&from=&to=
 *   GET /api/meeting_economics/team_roll_up?from=&to=
 *   GET /api/meeting_economics/cluster_view?cluster_id=&from=&to=
 */

import React, { useEffect, useState, useCallback, useMemo } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, SafeAreaView,
} from 'react-native';
import { api } from '../lib/api';

const FMT_INR = (v) => {
  v = Number(v || 0);
  if (v >= 1e7)  return `₹${(v / 1e7).toFixed(2)} cr`;
  if (v >= 1e5)  return `₹${(v / 1e5).toFixed(1)} L`;
  if (v >= 1e3)  return `₹${(v / 1e3).toFixed(1)}k`;
  return `₹${Math.round(v)}`;
};
const FMT_PCT = (v) => `${Math.round((v || 0) * 100)}%`;

const FLAG_LABELS = {
  meeting_plan_compliance_low: 'Plan compliance < 70%',
  purpose_achieved_low:        'Purpose achieved < 60%',
  travel_advance_aging:        'Travel advance ageing > 7d',
  expense_meeting_link_weak:   'Expenses unlinked to meetings',
  barge_quality_low:           'Barge meetings not shifting outcomes',
};

export default function MeetingEconomicsScreen({ navigation, route }) {
  const initialTab = route?.params?.tab || 'me';
  const [tab, setTab] = useState(initialTab);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [data, setData] = useState(null);

  const today = new Date();
  const to    = today.toISOString().slice(0, 10);
  const fromD = new Date(today); fromD.setDate(fromD.getDate() - 7);
  const from  = fromD.toISOString().slice(0, 10);

  const load = useCallback(async () => {
    try {
      let res;
      if (tab === 'me') {
        res = await api.get(`/api/meeting_economics/summary?from=${from}&to=${to}`);
      } else if (tab === 'team') {
        res = await api.get(`/api/meeting_economics/team_roll_up?from=${from}&to=${to}`);
      } else if (tab === 'cluster') {
        const cluster_id = route?.params?.cluster_id || 1;
        res = await api.get(`/api/meeting_economics/cluster_view?cluster_id=${cluster_id}&from=${from}&to=${to}`);
      }
      setData(res?.ok ? res.data : null);
    } catch (e) { console.warn(e); }
    finally { setLoading(false); setRefreshing(false); }
  }, [tab, from, to, route]);

  useEffect(() => { setLoading(true); load(); }, [load]);

  if (loading) return (
    <SafeAreaView style={s.center}><ActivityIndicator /><Text style={s.muted}>Loading…</Text></SafeAreaView>
  );

  return (
    <SafeAreaView style={s.container}>
      <View style={s.header}>
        <Text style={s.title}>Meeting Economics</Text>
        <Text style={s.subtitle}>{from} → {to}</Text>
      </View>

      <View style={s.tabs}>
        {['me', 'team', 'cluster'].map(t => (
          <TouchableOpacity key={t} onPress={() => setTab(t)}
            style={[s.tab, tab === t && s.tabActive]}>
            <Text style={tab === t ? s.tabActiveText : s.tabText}>
              {t === 'me' ? 'My week' : t === 'team' ? 'My team' : 'Cluster'}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      <ScrollView
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
        contentContainerStyle={{ paddingBottom: 60 }}>
        {tab === 'me'      && <MeView   data={data} />}
        {tab === 'team'    && <TeamView data={data} />}
        {tab === 'cluster' && <ClusterView data={data} />}
      </ScrollView>
    </SafeAreaView>
  );
}

function MeView({ data }) {
  if (!data) return <Text style={s.muted}>No data yet for this window.</Text>;
  const d = data.data || data;
  const flags = d.flags || [];
  const narrative = data.content;
  // Migration 011 surface: meeting mix + capture compliance levers
  const mix = d.mix || {};
  const cap = d.capture || {};

  return (
    <View>
      {/* Top KPI strip */}
      <View style={s.kpiRow}>
        <Kpi label="Meetings"   value={`${d.snapshot.meetings_actual}/${d.snapshot.meetings_planned}`} />
        <Kpi label="Plan %"     value={FMT_PCT(d.snapshot.plan_compliance)} />
        <Kpi label="Purpose %"  value={FMT_PCT(d.snapshot.purpose_achieved_rate)} />
        <Kpi label="Avg min"    value={`${d.snapshot.avg_meeting_min}m`} />
      </View>

      {/* KEY LEVER 1: Meeting Mix (migration 011) */}
      <Section title="Meeting mix - key lever">
        <View style={s.mixRow}>
          <MixPill label="Fresh" value={mix.fresh || 0} tone="good" />
          <MixPill label="RP" value={mix.rp || 0} tone="good" />
          <MixPill label="NO RP" value={mix.no_rp || 0} tone="bad" />
          <MixPill label="Only Got Detail" value={mix.only_got_detail || 0} tone="warn" />
        </View>
        <Text style={s.muted}>
          Fresh share {FMT_PCT(mix.fresh_share)} . RP share {FMT_PCT(mix.rp_share)} . NO RP share {FMT_PCT(mix.no_rp_share)}
        </Text>
      </Section>

      {/* KEY LEVER 2: Capture Compliance (migration 011) */}
      <Section title="Capture compliance - key lever">
        <View style={s.capGrid}>
          <CapCell label="Photo" pct={cap.with_photo_pct || 0} threshold={80} />
          <CapCell label="GPS" pct={cap.with_gps_pct || 0} threshold={80} />
          <CapCell label="MoM filled" pct={cap.with_mom_pct || 0} threshold={80} />
          <CapCell label="Duration logged" pct={cap.with_duration_pct || 0} threshold={80} />
        </View>
        <View style={s.row}>
          <Text style={s.rowK}>Overall capture</Text>
          <Text style={[s.rowV, (cap.overall_pct || 0) < 0.80 && s.bad]}>{FMT_PCT(cap.overall_pct)}</Text>
        </View>
        <View style={s.row}>
          <Text style={s.rowK}>Avg real duration</Text>
          <Text style={s.rowV}>{Math.round(cap.avg_real_min || 0)} min</Text>
        </View>
      </Section>

      <Section title="Spend">
        <Row k="Total spent"      v={FMT_INR(d.cost.total_spent)} />
        <Row k="Cost per meeting" v={FMT_INR(d.cost.cost_per_meeting)} />
        <Row k="Unlinked expenses"v={FMT_PCT(d.cost.unlinked_pct)} />
        {d.cost.expense_categories?.length > 0 && (
          <View style={{ marginTop: 6 }}>
            <Text style={s.muted}>By category:</Text>
            {d.cost.expense_categories.map((c, i) => (
              <Row key={i} k={c.category || 'uncategorised'} v={FMT_INR(c.total)} dim />
            ))}
          </View>
        )}
      </Section>

      <Section title="Travel advance">
        <Row k="Open"            v={FMT_INR(d.advance.open_advances)} />
        <Row k="Settled"         v={FMT_INR(d.advance.settled_advances)} />
        <Row k="Avg settle days" v={`${Math.round(d.advance.avg_settlement_days)}d`} />
      </Section>

      <Section title="Reachout cost (cstatus 1-5)">
        <Row k="Leads touched"   v={String(d.reachout.leads_touched)} />
        <Row k="Total spend"     v={FMT_INR(d.reachout.total_spend)} />
        <Row k="Cost per lead"   v={FMT_INR(d.reachout.cost_per_lead)} />
      </Section>

      <Section title="Barge meetings">
        <Row k="Total barges"    v={String(d.barge.total_barges)} />
        <Row k="Positive shift"  v={FMT_PCT(d.barge.positive_rate)} />
        {d.barge.by_actor_role && (
          <Text style={s.muted}>
            CM: {d.barge.by_actor_role.CM || 0} · RM: {d.barge.by_actor_role.RM || 0} · Dir: {d.barge.by_actor_role.Director || 0}
          </Text>
        )}
      </Section>

      <Section title="Cluster travel">
        {(d.cluster?.rows || []).slice(0, 6).map((c, i) => (
          <Row key={i}
            k={`${c.cluster_name} · ${c.travel_type || '—'}`}
            v={`${c.meetings_in_cluster} mtgs · ${FMT_INR(c.spend_in_cluster)}`} />
        ))}
      </Section>

      {flags.length > 0 && (
        <Section title="Flags" tone="warn">
          {flags.map(f => <Text key={f} style={s.flag}>• {FLAG_LABELS[f] || f}</Text>)}
        </Section>
      )}

      {!!narrative && (
        <Section title="Coach narrative">
          <Text style={s.narrative}>{narrative}</Text>
        </Section>
      )}
    </View>
  );
}

function TeamView({ data }) {
  if (!data || !data.team) return <Text style={s.muted}>No team data.</Text>;
  const team = data.team;
  const totals = data.totals || {};
  return (
    <View>
      <View style={s.kpiRow}>
        <Kpi label="Total meetings" value={String(totals.meetings || 0)} />
        <Kpi label="Total spend"    value={FMT_INR(totals.spend)} />
        <Kpi label="Open advances"  value={FMT_INR(totals.open_advances)} />
        <Kpi label="Leads touched"  value={String(totals.reachout_leads || 0)} />
      </View>

      {team.map((r) => (
        <View key={r.bd_id} style={s.teamCard}>
          <View style={s.teamCardHead}>
            <Text style={s.teamCardName}>BD #{r.bd_id}</Text>
            <Text style={s.muted}>{r.meetings} mtgs · {FMT_PCT(r.plan_compliance)} plan</Text>
          </View>
          <View style={s.teamCardGrid}>
            <Mini label="Purpose"   value={FMT_PCT(r.purpose_rate)} />
            <Mini label="₹/mtg"     value={FMT_INR(r.cost_per_meet)} />
            <Mini label="Reachout"  value={`${r.reachout_leads} · ${FMT_INR(r.cost_per_lead)}/lead`} />
            <Mini label="Open adv"  value={FMT_INR(r.open_advances)} />
          </View>
          {r.flags?.length > 0 && (
            <View style={s.flagRow}>
              {r.flags.map(f => (
                <Text key={f} style={s.flagPill}>{FLAG_LABELS[f] || f}</Text>
              ))}
            </View>
          )}
        </View>
      ))}
    </View>
  );
}

function ClusterView({ data }) {
  if (!data || !data.bds) return <Text style={s.muted}>No cluster data.</Text>;
  return (
    <View>
      <Text style={s.muted}>Cluster {data.cluster_id} · {data.bds.length} BDs</Text>
      {data.bds.map((b, i) => (
        <View key={i} style={s.teamCard}>
          <Text style={s.teamCardName}>BD #{b.bd_id}</Text>
          <Text style={s.muted}>
            {b.data?.snapshot?.meetings_actual || 0} mtgs · {FMT_INR(b.data?.cost?.total_spent)} · {FMT_PCT(b.data?.snapshot?.plan_compliance)} plan
          </Text>
        </View>
      ))}
    </View>
  );
}

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
const Mini = ({ label, value }) => (
  <View style={s.mini}><Text style={s.miniL}>{label}</Text><Text style={s.miniV}>{value}</Text></View>
);
const Row = ({ k, v, dim }) => (
  <View style={s.row}>
    <Text style={[s.rowK, dim && { color: '#7A8395' }]}>{k}</Text>
    <Text style={[s.rowV, dim && { color: '#7A8395' }]}>{v}</Text>
  </View>
);
const Section = ({ title, children, tone }) => (
  <View style={[s.section, tone === 'warn' && s.sectionWarn]}>
    <Text style={s.sectionTitle}>{title}</Text>
    {children}
  </View>
);

const s = StyleSheet.create({
  container:  { flex: 1, backgroundColor: '#F7F8FA' },
  center:     { flex: 1, alignItems: 'center', justifyContent: 'center' },
  header:     { padding: 16, backgroundColor: '#FFF', borderBottomWidth: 1, borderBottomColor: '#E5E8EB' },
  title:      { fontSize: 18, fontWeight: '700', color: '#1B2A4E' },
  subtitle:   { color: '#5B6478', marginTop: 2 },
  muted:      { color: '#7A8395', paddingHorizontal: 16, paddingTop: 6 },
  tabs:       { flexDirection: 'row', backgroundColor: '#FFF', paddingHorizontal: 12, paddingVertical: 8, borderBottomWidth: 1, borderBottomColor: '#E5E8EB' },
  tab:        { paddingVertical: 8, paddingHorizontal: 14, borderRadius: 8, marginRight: 8 },
  tabActive:  { backgroundColor: '#1B2A4E' },
  tabText:    { color: '#5B6478', fontWeight: '600' },
  tabActiveText:{ color: '#FFF', fontWeight: '700' },
  kpiRow:     { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', padding: 12 },
  kpi:        { width: '23%', backgroundColor: '#FFF', padding: 10, borderRadius: 8, alignItems: 'center', shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 3, elevation: 1 },
  kpiV:       { fontSize: 15, fontWeight: '700', color: '#1B2A4E' },
  kpiL:       { fontSize: 11, color: '#7A8395', marginTop: 2 },
  section:    { backgroundColor: '#FFF', marginHorizontal: 12, marginTop: 10, padding: 14, borderRadius: 8 },
  sectionWarn:{ borderLeftWidth: 4, borderLeftColor: '#D9A441' },
  sectionTitle:{ fontSize: 13, fontWeight: '700', color: '#1B2A4E', marginBottom: 8, textTransform: 'uppercase', letterSpacing: 0.4 },
  row:        { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 3 },
  rowK:       { color: '#3A4458' },
  rowV:       { color: '#1B2A4E', fontWeight: '600' },
  flag:       { color: '#A66B00', marginVertical: 2 },
  narrative:  { color: '#3A4458', lineHeight: 20 },
  teamCard:   { backgroundColor: '#FFF', marginHorizontal: 12, marginTop: 8, padding: 12, borderRadius: 8 },
  teamCardHead:{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  teamCardName:{ fontWeight: '700', color: '#1B2A4E' },
  teamCardGrid:{ flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', marginBottom: 6 },
  mini:       { width: '48%', paddingVertical: 4 },
  miniL:      { color: '#7A8395', fontSize: 11 },
  miniV:      { color: '#1B2A4E', fontWeight: '600' },
  flagRow:    { flexDirection: 'row', flexWrap: 'wrap', marginTop: 6 },
  flagPill:   { backgroundColor: '#FFF3E0', color: '#A66B00', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 10, marginRight: 6, marginBottom: 4, fontSize: 11 },
  mixRow:     { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  mixPill:    { flex: 1, marginRight: 6, paddingVertical: 10, borderRadius: 8, alignItems: 'center' },
  mixPillV:   { fontSize: 18, fontWeight: '800' },
  mixPillL:   { fontSize: 10, fontWeight: '600', marginTop: 2 },
  capGrid:    { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', marginBottom: 8 },
  capCell:    { width: '48%', paddingVertical: 8, paddingHorizontal: 8, marginBottom: 6, backgroundColor: '#F7F8FA', borderRadius: 6 },
  capV:       { fontSize: 18, fontWeight: '700', color: '#1B2A4E' },
  capL:       { fontSize: 11, color: '#7A8395', marginTop: 2 },
  capFlag:    { fontSize: 10, color: '#B3271B', marginTop: 2, fontWeight: '600' },
  bad:        { color: '#B3271B' },
});
