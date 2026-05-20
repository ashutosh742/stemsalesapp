// DayPlanDisciplineScreen.js
//
// The Day Plan Discipline screen is the audit and coaching view that
// sits next to the Day Plan submit screen. The Day Plan screen lets a
// BD build tomorrow's plan. This screen rates how disciplined that
// plan was - across 8 dimensions - and shows where the BD slipped.
//
// Endpoint: GET /api/planner_analytics/full_card?date=YYYY-MM-DD&uid=<uid>
// Org view (CM, RM): GET /api/planner_analytics/snapshot?date=YYYY-MM-DD
//
// Migration 015 surface. Staging only until pilot 25 May 2026.

import React, { useEffect, useMemo, useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { apiGet } from '../api/client';
import { useAuth } from '../auth/AuthProvider';

const TABS = [
  { key: 'time',       label: 'Time' },
  { key: 'activity',   label: 'Activities' },
  { key: 'stage',      label: 'Stages' },
  { key: 'cluster',    label: 'Cluster' },
  { key: 'filter',     label: 'Filters' },
  { key: 'category',   label: 'Category' },
  { key: 'mandatory',  label: 'Mandatory' },
];

const GRADE_COLOR = {
  'A+': '#1b7f3a',
  'A':  '#2e9e4c',
  'B':  '#c8b21a',
  'C':  '#e07a2e',
  'D':  '#c43c2c',
};

const DIM_LABEL = {
  time:       'Time on planning',
  volume:     'Volume of tasks',
  action:     'Action diversity',
  stage:      'Stage diversity',
  cluster:    'Cluster focus',
  filter:     'Filter diversity',
  category:   'Category spread',
  mandatory:  'Mandatory health',
};

function todayIso() {
  const d = new Date();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${d.getFullYear()}-${m}-${day}`;
}

function Bar({ value, max, avg }) {
  // value 0..max as filled bar. avg drawn as a thin marker.
  const pct = Math.max(0, Math.min(100, (value / max) * 100));
  const avgPct = avg != null ? Math.max(0, Math.min(100, (avg / max) * 100)) : null;
  return (
    <View style={styles.barTrack}>
      <View style={[styles.barFill, { width: `${pct}%` }]} />
      {avgPct != null && (
        <View style={[styles.barAvgMark, { left: `${avgPct}%` }]} />
      )}
    </View>
  );
}

function DimensionRow({ label, score, max, avg }) {
  return (
    <View style={styles.dimRow}>
      <Text style={styles.dimLabel}>{label}</Text>
      <View style={{ flex: 1, paddingHorizontal: 10 }}>
        <Bar value={score} max={max} avg={avg} />
      </View>
      <Text style={styles.dimScore}>{score} / {max}</Text>
    </View>
  );
}

function KeyVal({ k, v }) {
  return (
    <View style={styles.kvRow}>
      <Text style={styles.kvKey}>{k}</Text>
      <Text style={styles.kvVal}>{v == null || v === '' ? '0' : String(v)}</Text>
    </View>
  );
}

export default function DayPlanDisciplineScreen({ route, navigation }) {
  const { user } = useAuth();
  const targetUid = route?.params?.uid || user?.uid;
  const targetDate = route?.params?.date || todayIso();

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [data, setData] = useState(null);
  const [tab, setTab] = useState('time');
  const [error, setError] = useState(null);

  const fetchCard = async () => {
    setError(null);
    try {
      const resp = await apiGet(
        `/api/planner_analytics/full_card?date=${targetDate}&uid=${targetUid}`
      );
      setData(resp || {});
    } catch (e) {
      setError(e.message || 'Failed to load planner analytics');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchCard();
  }, [targetUid, targetDate]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchCard();
  };

  const score = data?.discipline_score || {};
  const time = data?.time || {};
  const act = data?.activity_mix || {};
  const stage = data?.cstatus_mix || {};
  const cluster = data?.cluster_split || {};
  const filter = data?.filter_usage || {};
  const cat = data?.category_mix || {};
  const mand = data?.mandatory || {};
  const avg = data?.rolling_avg_7d || {};

  const grade = score.grade || 'D';
  const totalScore = score.discipline_score || 0;
  const weakest = score.weakest_dimension || 'time';

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.center}>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryBtn} onPress={fetchCard}>
          <Text style={styles.retryText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <ScrollView
      style={styles.scroll}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
    >
      <View style={styles.header}>
        <Text style={styles.headerDate}>{targetDate}</Text>
        <Text style={styles.headerTitle}>Day Plan Discipline</Text>
      </View>

      <View style={styles.gradeCard}>
        <View style={[styles.gradeBadge, { backgroundColor: GRADE_COLOR[grade] || '#888' }]}>
          <Text style={styles.gradeText}>{grade}</Text>
        </View>
        <View style={{ flex: 1, marginLeft: 16 }}>
          <Text style={styles.scoreLine}>Discipline score {totalScore} of 100</Text>
          <Text style={styles.weakLine}>
            Weakest area: {DIM_LABEL[weakest] || weakest}
          </Text>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Dimensions today vs 7 day average</Text>
        <DimensionRow label="Time"      score={score.time_score             || 0} max={15} avg={avg.avg_time} />
        <DimensionRow label="Volume"    score={score.volume_score           || 0} max={15} avg={avg.avg_volume} />
        <DimensionRow label="Actions"   score={score.action_diversity_score || 0} max={15} avg={avg.avg_action} />
        <DimensionRow label="Stages"    score={score.stage_diversity_score  || 0} max={15} avg={avg.avg_stage} />
        <DimensionRow label="Cluster"   score={score.cluster_focus_score    || 0} max={10} avg={avg.avg_cluster} />
        <DimensionRow label="Filters"   score={score.filter_diversity_score || 0} max={10} avg={avg.avg_filter} />
        <DimensionRow label="Category"  score={score.category_spread_score  || 0} max={10} avg={avg.avg_category} />
        <DimensionRow label="Mandatory" score={score.mandatory_health_score || 0} max={10} avg={avg.avg_mandatory} />
      </View>

      <View style={styles.tabBar}>
        {TABS.map(t => (
          <TouchableOpacity
            key={t.key}
            style={[styles.tab, tab === t.key && styles.tabActive]}
            onPress={() => setTab(t.key)}
          >
            <Text style={[styles.tabText, tab === t.key && styles.tabTextActive]}>
              {t.label}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      <View style={styles.drilldown}>
        {tab === 'time' && (
          <>
            <KeyVal k="Sessions opened"   v={time.session_count} />
            <KeyVal k="Total minutes"     v={time.total_minutes} />
            <KeyVal k="Longest session m" v={time.longest_session} />
            <KeyVal k="Average minutes"   v={time.avg_session_minutes} />
            <KeyVal k="First open"        v={time.first_open_at} />
            <KeyVal k="Last submit"       v={time.last_submit_at} />
          </>
        )}

        {tab === 'activity' && (
          <>
            <KeyVal k="Total tasks planned"  v={act.total_tasks} />
            <KeyVal k="Calls"                v={act.calls} />
            <KeyVal k="Emails"               v={act.emails} />
            <KeyVal k="Sched meetings"       v={act.sched_meetings} />
            <KeyVal k="Barge meetings"       v={act.barge_meetings} />
            <KeyVal k="WhatsApps"            v={act.whatsapps} />
            <KeyVal k="MoMs"                 v={act.moms} />
            <KeyVal k="Proposals"            v={act.proposals} />
            <KeyVal k="Research tasks"       v={act.research_tasks} />
            <KeyVal k="Documentation"        v={act.documentation} />
            <KeyVal k="Join meetings"        v={act.join_meetings} />
            <KeyVal k="BD requests"          v={act.bd_requests} />
            <KeyVal k="Proposal checks"      v={act.proposal_checks} />
          </>
        )}

        {tab === 'stage' && (
          <>
            <KeyVal k="Open"          v={stage.plan_open} />
            <KeyVal k="Reachout"      v={stage.plan_reachout} />
            <KeyVal k="Tentative"     v={stage.plan_tentative} />
            <KeyVal k="Positive"      v={stage.plan_positive} />
            <KeyVal k="Proposal sent" v={stage.plan_proposal} />
            <KeyVal k="Open RPEM"     v={stage.plan_open_rpem} />
            <KeyVal k="Very Positive" v={stage.plan_very_pos} />
            <KeyVal k="Won"           v={stage.plan_won} />
            <KeyVal k="Lost"          v={stage.plan_lost} />
            <KeyVal k="Stages touched" v={stage.stage_diversity_count} />
          </>
        )}

        {tab === 'cluster' && (
          <>
            <KeyVal k="Home cluster"        v={cluster.home_cluster_id} />
            <KeyVal k="Inside home"         v={cluster.inside_home} />
            <KeyVal k="Outside home"        v={cluster.outside_home} />
            <KeyVal k="Cluster unknown"     v={cluster.cluster_unknown} />
            <KeyVal k="Inside percent"      v={(cluster.inside_pct || 0) + ' percent'} />
            <KeyVal k="Outside clusters"    v={cluster.outside_clusters_csv} />
          </>
        )}

        {tab === 'filter' && (
          <>
            <KeyVal k="Distinct filters used" v={filter.distinct_filters_used} />
            <KeyVal k="Top filter"            v={filter.top_filter} />
            <KeyVal k="All"                   v={filter.filter_all} />
            <KeyVal k="New lead"              v={filter.filter_new_lead} />
            <KeyVal k="Positive"              v={filter.filter_positive} />
            <KeyVal k="Tentative"             v={filter.filter_tentative} />
            <KeyVal k="Open"                  v={filter.filter_open} />
            <KeyVal k="Reachout"              v={filter.filter_reachout} />
            <KeyVal k="Open RPEM"             v={filter.filter_open_rpem} />
            <KeyVal k="Very positive"         v={filter.filter_very_positive} />
            <KeyVal k="Won"                   v={filter.filter_won} />
            <KeyVal k="Lost"                  v={filter.filter_lost} />
            <KeyVal k="Today meetings"        v={filter.filter_today_meetings} />
            <KeyVal k="RPEM due"              v={filter.filter_rpem_due} />
            <KeyVal k="MoM pending"           v={filter.filter_mom_pending} />
            <KeyVal k="High value"            v={filter.filter_high_value} />
            <KeyVal k="Low activity"          v={filter.filter_low_activity} />
            <KeyVal k="Cluster focus"         v={filter.filter_cluster_focus} />
          </>
        )}

        {tab === 'category' && (
          <>
            <KeyVal k="Categories touched" v={cat.distinct_cats_used} />
            <KeyVal k="Upsell"             v={cat.cat_upsell} />
            <KeyVal k="Focus"              v={cat.cat_focus} />
            <KeyVal k="Key"                v={cat.cat_key} />
            <KeyVal k="Potential"          v={cat.cat_potential} />
            <KeyVal k="Other"              v={cat.cat_other} />
            <KeyVal k="Uncategorised"      v={cat.cat_uncategorised} />
          </>
        )}

        {tab === 'mandatory' && (
          <>
            <KeyVal k="Mandatory assigned" v={mand.mandatory_assigned} />
            <KeyVal k="Planned"            v={mand.planned} />
            <KeyVal k="Initiated"          v={mand.initiated} />
            <KeyVal k="Completed"          v={mand.completed} />
            <KeyVal k="Skipped"            v={mand.skipped} />
            <KeyVal k="Completion percent" v={(mand.completion_pct || 0) + ' percent'} />
            <KeyVal k="Skipped event ids"  v={mand.skipped_event_ids} />
          </>
        )}
      </View>

      <View style={styles.footer}>
        <Text style={styles.footerText}>
          Migration 015 planner analytics. Staging only until 25 May 2026.
        </Text>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  scroll:        { flex: 1, backgroundColor: '#f5f6f8' },
  center:        { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 20 },

  header:        { padding: 16, backgroundColor: '#1f3a5f' },
  headerDate:    { color: '#cfd8e3', fontSize: 13 },
  headerTitle:   { color: '#fff', fontSize: 20, fontWeight: '700', marginTop: 4 },

  gradeCard:     {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff',
    margin: 12,
    padding: 16,
    borderRadius: 8,
    elevation: 2,
  },
  gradeBadge:    {
    width: 64, height: 64, borderRadius: 32,
    alignItems: 'center', justifyContent: 'center',
  },
  gradeText:     { color: '#fff', fontSize: 28, fontWeight: '700' },
  scoreLine:     { fontSize: 16, fontWeight: '600', color: '#222' },
  weakLine:      { fontSize: 13, color: '#666', marginTop: 4 },

  section:       { backgroundColor: '#fff', marginHorizontal: 12, padding: 16, borderRadius: 8, elevation: 1 },
  sectionTitle:  { fontSize: 14, fontWeight: '600', color: '#333', marginBottom: 12 },

  dimRow:        { flexDirection: 'row', alignItems: 'center', marginVertical: 6 },
  dimLabel:      { width: 80, fontSize: 12, color: '#333' },
  dimScore:      { width: 56, fontSize: 12, color: '#555', textAlign: 'right' },

  barTrack:      { height: 10, backgroundColor: '#e6e8ee', borderRadius: 5, overflow: 'hidden', position: 'relative' },
  barFill:       { position: 'absolute', left: 0, top: 0, bottom: 0, backgroundColor: '#3a7bd5' },
  barAvgMark:    { position: 'absolute', top: -2, bottom: -2, width: 2, backgroundColor: '#222' },

  tabBar:        { flexDirection: 'row', flexWrap: 'wrap', marginHorizontal: 12, marginTop: 12 },
  tab:           { paddingVertical: 8, paddingHorizontal: 12, marginRight: 6, marginBottom: 6, borderRadius: 16, backgroundColor: '#e6e8ee' },
  tabActive:     { backgroundColor: '#1f3a5f' },
  tabText:       { fontSize: 12, color: '#333' },
  tabTextActive: { color: '#fff', fontWeight: '600' },

  drilldown:     { backgroundColor: '#fff', margin: 12, padding: 16, borderRadius: 8, elevation: 1 },

  kvRow:         { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 6, borderBottomWidth: 1, borderBottomColor: '#f0f0f3' },
  kvKey:         { fontSize: 13, color: '#555' },
  kvVal:         { fontSize: 13, color: '#222', fontWeight: '600' },

  footer:        { padding: 16, alignItems: 'center' },
  footerText:    { fontSize: 11, color: '#888' },

  errorText:     { color: '#c43c2c', textAlign: 'center', marginBottom: 16 },
  retryBtn:      { padding: 12, backgroundColor: '#1f3a5f', borderRadius: 6 },
  retryText:     { color: '#fff', fontWeight: '600' },
});
