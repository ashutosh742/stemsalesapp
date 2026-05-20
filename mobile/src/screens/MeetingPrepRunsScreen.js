// MeetingPrepRunsScreen — Migration 042 CM / admin dashboard.
//
// Lists today's meeting prep runs across the team. Surfaces:
//   - Header counters: total runs today, ready, running, skipped, failed
//   - Apollo daily quota gauge (used / cap)
//   - Filter chips: All / Ready / Running / Skipped / Failed
//   - Per-row: BD, corporate, scheduled meeting time, status, artifact links
//
// Backend (real): GET /api/meeting_prep/runs_today  (optional bd_uid filter)
//                 GET /api/csr_prospect/apollo/quota_status  (or carried in runs_today payload)
//
// For CMs (type_id=13) the backend scopes rows to their BDs.
// For admins (type_id in {1,2}) it returns org-wide.
// For BDs it returns own rows only.

import React, { useEffect, useState, useMemo, useCallback } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform,
  ActivityIndicator, RefreshControl, Alert,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { USE_MOCKS, tools } from '../api/client';

const HEADER_GRADIENT = ['#0F766E', '#14B8A6'];

const MOCK_ROWS = [
  { run_id: 17, event_id: 9001, bd_name: 'Priya Menon',  corporate_name: 'Acme Industries Pvt Ltd',     scheduled_at: '11:00', status: 'ready',   has_pdf: true,  has_pptx: true,  has_wa: true,  enrichment_cache_hit: true },
  { run_id: 18, event_id: 9002, bd_name: 'Ravi Kumar',   corporate_name: 'Bharat Forge Ltd',            scheduled_at: '12:30', status: 'ready',   has_pdf: true,  has_pptx: true,  has_wa: true,  enrichment_cache_hit: false },
  { run_id: 19, event_id: 9003, bd_name: 'Anita Sharma', corporate_name: 'Mahindra Logistics',          scheduled_at: '14:00', status: 'running', has_pdf: false, has_pptx: false, has_wa: false, enrichment_cache_hit: false },
  { run_id: 20, event_id: 9004, bd_name: 'Vikram Tyagi', corporate_name: 'Tata Power Renewables',       scheduled_at: '15:30', status: 'ready',   has_pdf: true,  has_pptx: true,  has_wa: true,  enrichment_cache_hit: true },
  { run_id: 21, event_id: 9005, bd_name: 'Sneha Iyer',   corporate_name: 'L&T Infotech',                scheduled_at: '16:00', status: 'failed',  has_pdf: false, has_pptx: false, has_wa: false, enrichment_cache_hit: false, reason: 'Apollo quota exhausted before LinkedIn complete' },
  { run_id: 22, event_id: 9006, bd_name: 'Priya Menon',  corporate_name: 'Reliance Foundation',         scheduled_at: '17:00', status: 'skipped', has_pdf: false, has_pptx: false, has_wa: false, enrichment_cache_hit: false, reason: 'Lead type is not corporate' },
];

const MOCK_QUOTA = { used_today: 142, daily_quota: 250, percent: 56.8 };

const STATUS_META = {
  ready:   { label: 'Ready',   color: colors.success, bg: colors.success + '15', icon: 'checkmark-circle' },
  running: { label: 'Running', color: colors.info,    bg: colors.info + '15',    icon: 'sync' },
  skipped: { label: 'Skipped', color: colors.warning, bg: '#FEF3C7',             icon: 'remove-circle' },
  failed:  { label: 'Failed',  color: colors.danger,  bg: '#FEE2E2',             icon: 'alert-circle' },
};

export default function MeetingPrepRunsScreen({ navigation }) {
  const [rows, setRows] = useState([]);
  const [quota, setQuota] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [filter, setFilter] = useState('all');

  const load = useCallback(async () => {
    try {
      if (USE_MOCKS) {
        setTimeout(() => {
          setRows(MOCK_ROWS);
          setQuota(MOCK_QUOTA);
          setLoading(false);
          setRefreshing(false);
        }, 400);
        return;
      }
      const res = await tools.runTool('meeting_prep_runs_today', {});
      setRows((res && res.rows) || []);
      setQuota((res && res.quota) || null);
      setLoading(false);
      setRefreshing(false);
    } catch (e) {
      setLoading(false);
      setRefreshing(false);
      Alert.alert('Load failed', String(e && e.message ? e.message : e));
    }
  }, []);

  useEffect(() => { load(); }, [load]);

  const counts = useMemo(() => {
    const c = { total: rows.length, ready: 0, running: 0, skipped: 0, failed: 0 };
    rows.forEach((r) => { if (c[r.status] !== undefined) c[r.status] += 1; });
    return c;
  }, [rows]);

  const visibleRows = useMemo(() => {
    if (filter === 'all') return rows;
    return rows.filter((r) => r.status === filter);
  }, [rows, filter]);

  function openRow(row) {
    if (!navigation || !navigation.navigate) return;
    if (row.status !== 'ready') {
      if (row.reason) Alert.alert(STATUS_META[row.status].label, row.reason);
      return;
    }
    navigation.navigate('MeetingPrep', { eventId: row.event_id });
  }

  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" />

      <LinearGradient colors={HEADER_GRADIENT} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.header}>
        <Pressable onPress={() => navigation && navigation.goBack && navigation.goBack()} hitSlop={12}>
          <Ionicons name="chevron-back" size={26} color="#fff" />
        </Pressable>
        <View style={styles.headerIcon}>
          <Ionicons name="briefcase" size={20} color="#fff" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>Meeting Prep Runs</Text>
          <Text style={styles.headerSub}>Today . corporate meetings only</Text>
        </View>
      </LinearGradient>

      {loading ? (
        <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center' }}>
          <ActivityIndicator size="large" color={colors.btnFrom} />
        </View>
      ) : (
        <ScrollView
          contentContainerStyle={{ padding: 16, paddingBottom: 40 }}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} tintColor={colors.btnFrom} />}
          showsVerticalScrollIndicator={false}
        >
          {/* ===== Counter strip ===== */}
          <View style={styles.counterRow}>
            <CounterTile label="Total"   value={counts.total}   color={colors.text} />
            <CounterTile label="Ready"   value={counts.ready}   color={colors.success} />
            <CounterTile label="Running" value={counts.running} color={colors.info} />
            <CounterTile label="Skipped" value={counts.skipped} color={colors.warning} />
            <CounterTile label="Failed"  value={counts.failed}  color={colors.danger} />
          </View>

          {/* ===== Apollo quota gauge ===== */}
          {quota && (
            <View style={styles.quotaCard}>
              <View style={styles.quotaHead}>
                <Ionicons name="speedometer" size={14} color={colors.btnFrom} />
                <Text style={styles.quotaTitle}>Apollo daily quota</Text>
                <View style={{ flex: 1 }} />
                <Text style={styles.quotaVal}>
                  {quota.used_today} / {quota.daily_quota}
                </Text>
              </View>
              <View style={styles.quotaBarBg}>
                <View
                  style={[
                    styles.quotaBarFill,
                    { width: Math.min(100, quota.percent || 0) + '%',
                      backgroundColor: (quota.percent || 0) >= 90 ? colors.danger : (quota.percent || 0) >= 70 ? colors.warning : colors.success },
                  ]}
                />
              </View>
              <Text style={styles.quotaHint}>
                {(quota.percent || 0).toFixed(1)} percent used. Resets at midnight IST.
              </Text>
            </View>
          )}

          {/* ===== Filter chips ===== */}
          <View style={styles.chipsRow}>
            {['all', 'ready', 'running', 'skipped', 'failed'].map((f) => (
              <Pressable key={f} onPress={() => setFilter(f)} style={[styles.chip, filter === f && styles.chipActive]}>
                <Text style={[styles.chipText, filter === f && styles.chipTextActive]}>
                  {f === 'all' ? 'All' : STATUS_META[f].label}
                </Text>
              </Pressable>
            ))}
          </View>

          {/* ===== Rows ===== */}
          {visibleRows.length === 0 && (
            <View style={styles.emptyBox}>
              <Ionicons name="document-outline" size={28} color={colors.textMuted} />
              <Text style={styles.emptyText}>No runs in this view.</Text>
            </View>
          )}

          {visibleRows.map((row) => {
            const meta = STATUS_META[row.status] || STATUS_META.skipped;
            return (
              <Pressable key={row.run_id} onPress={() => openRow(row)} style={styles.runCard}>
                <View style={styles.runHead}>
                  <View style={[styles.statusPill, { backgroundColor: meta.bg }]}>
                    <Ionicons name={meta.icon} size={12} color={meta.color} />
                    <Text style={[styles.statusPillText, { color: meta.color }]}>{meta.label}</Text>
                  </View>
                  <View style={{ flex: 1 }} />
                  <Text style={styles.timeText}>{row.scheduled_at}</Text>
                </View>

                <Text style={styles.corporateName}>{row.corporate_name}</Text>
                <Text style={styles.bdName}>BD {row.bd_name} . Event {row.event_id}</Text>

                {row.status === 'ready' && (
                  <View style={styles.artifactsRow}>
                    {row.has_pdf  && <ArtifactChip icon="document-text" label="PDF"      color={'#14B8A6'} />}
                    {row.has_pptx && <ArtifactChip icon="easel"          label="PPT"      color={'#7C3AED'} />}
                    {row.has_wa   && <ArtifactChip icon="logo-whatsapp"  label="WhatsApp" color={'#22C55E'} />}
                    {row.enrichment_cache_hit && <ArtifactChip icon="flash" label="Cache hit" color={colors.textMuted} />}
                  </View>
                )}

                {(row.status === 'skipped' || row.status === 'failed') && row.reason && (
                  <Text style={styles.reasonText}>{row.reason}</Text>
                )}

                {row.status === 'running' && (
                  <View style={styles.runningRow}>
                    <ActivityIndicator size="small" color={colors.info} />
                    <Text style={styles.runningText}>Generating brief.</Text>
                  </View>
                )}
              </Pressable>
            );
          })}
        </ScrollView>
      )}
    </View>
  );
}

function CounterTile({ label, value, color }) {
  return (
    <View style={styles.counterTile}>
      <Text style={[styles.counterValue, { color }]}>{value}</Text>
      <Text style={styles.counterLabel}>{label}</Text>
    </View>
  );
}

function ArtifactChip({ icon, label, color }) {
  return (
    <View style={[styles.artifactChip, { backgroundColor: color + '15', borderColor: color + '40' }]}>
      <Ionicons name={icon} size={11} color={color} />
      <Text style={[styles.artifactChipText, { color }]}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: {
    flexDirection: 'row', alignItems: 'center', gap: 10,
    paddingTop: Platform.OS === 'ios' ? 54 : 36,
    paddingBottom: 14, paddingHorizontal: 14,
  },
  headerIcon: {
    width: 36, height: 36, borderRadius: 11,
    backgroundColor: 'rgba(255,255,255,0.22)',
    alignItems: 'center', justifyContent: 'center',
  },
  headerTitle: { color: '#fff', fontSize: 17, fontWeight: '700' },
  headerSub:   { color: 'rgba(255,255,255,0.85)', fontSize: 11.5, marginTop: 1 },

  counterRow: { flexDirection: 'row', gap: 6 },
  counterTile: {
    flex: 1, backgroundColor: colors.card, borderRadius: 10,
    borderWidth: 1, borderColor: colors.border,
    paddingVertical: 10, paddingHorizontal: 6, alignItems: 'center',
  },
  counterValue: { fontSize: 20, fontWeight: '800' },
  counterLabel: { color: colors.textMuted, fontSize: 10, fontWeight: '600', marginTop: 2, textTransform: 'uppercase', letterSpacing: 0.4 },

  quotaCard: {
    backgroundColor: colors.card, borderRadius: 12, borderWidth: 1, borderColor: colors.border,
    padding: 12, marginTop: 12,
  },
  quotaHead: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  quotaTitle: { color: colors.text, fontSize: 12.5, fontWeight: '700' },
  quotaVal: { color: colors.text, fontSize: 12.5, fontWeight: '700', fontVariant: ['tabular-nums'] },
  quotaBarBg: { height: 6, backgroundColor: colors.cardAlt, borderRadius: 3, marginTop: 10, overflow: 'hidden' },
  quotaBarFill: { height: '100%', borderRadius: 3 },
  quotaHint: { color: colors.textMuted, fontSize: 10.5, marginTop: 6 },

  chipsRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 6, marginTop: 14, marginBottom: 6 },
  chip: {
    paddingHorizontal: 12, paddingVertical: 6, borderRadius: 999,
    backgroundColor: colors.card, borderWidth: 1, borderColor: colors.border,
  },
  chipActive: { backgroundColor: colors.btnFrom, borderColor: colors.btnFrom },
  chipText: { color: colors.text, fontSize: 11.5, fontWeight: '600' },
  chipTextActive: { color: '#fff' },

  emptyBox: { alignItems: 'center', paddingVertical: 40, gap: 8 },
  emptyText: { color: colors.textMuted, fontSize: 12.5 },

  runCard: {
    backgroundColor: colors.card, borderRadius: 12, borderWidth: 1, borderColor: colors.border,
    padding: 12, marginTop: 10,
  },
  runHead: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  statusPill: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    paddingHorizontal: 8, paddingVertical: 3, borderRadius: 999,
  },
  statusPillText: { fontSize: 10.5, fontWeight: '700', textTransform: 'uppercase', letterSpacing: 0.3 },
  timeText: { color: colors.textMuted, fontSize: 12, fontWeight: '700', fontVariant: ['tabular-nums'] },
  corporateName: { color: colors.text, fontSize: 14, fontWeight: '700', marginTop: 8 },
  bdName: { color: colors.textMuted, fontSize: 11.5, marginTop: 2 },

  artifactsRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 6, marginTop: 10 },
  artifactChip: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    paddingHorizontal: 8, paddingVertical: 4, borderRadius: 6, borderWidth: 1,
  },
  artifactChipText: { fontSize: 10.5, fontWeight: '700' },

  reasonText: {
    color: colors.text, fontSize: 11.5, marginTop: 8, lineHeight: 16,
    backgroundColor: colors.cardAlt, padding: 8, borderRadius: 6,
  },
  runningRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 10 },
  runningText: { color: colors.textMuted, fontSize: 11.5 },
});
