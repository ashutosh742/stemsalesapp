/**
 * MomApprovalQueueScreen — Card 13 (Voice MoM Drafter's CM half)
 *
 * CMs see the union of pending MoMs from BDs that report to them.
 * AI pre-tier: Tier 1 (auto-approvable) shows a bulk-approve bar,
 *              Tier 2 (review) is reviewed one-by-one,
 *              Tier 3 (likely reject) is flagged for kick-back.
 *
 * Backend: GET /api/mom/approval_queue, POST /api/mom/bulk_approve,
 *          POST /api/mom/approve, POST /api/mom/reject.
 */

import React, { useEffect, useMemo, useState, useCallback } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator,
  Alert, RefreshControl, SafeAreaView, TextInput, Modal,
} from 'react-native';
import { api } from '../lib/api';

const TIER_LABEL = {
  1: 'Auto-approvable',
  2: 'Needs review',
  3: 'Likely reject',
};
const TIER_COLOR = {
  1: '#22A06B', // green
  2: '#D9A441', // amber
  3: '#C04A3F', // red
};

export default function MomApprovalQueueScreen({ navigation }) {
  const [loading, setLoading]   = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [data, setData]         = useState(null);
  const [selected, setSelected] = useState({}); // mom_id -> true (for bulk)
  const [rejectFor, setRejectFor] = useState(null); // mom row being rejected
  const [rejectReason, setRejectReason] = useState('');

  const load = useCallback(async () => {
    try {
      const res = await api.get('/api/mom/approval_queue');
      if (res?.ok) setData(res.data);
      else Alert.alert('Queue', res?.error || 'Failed to load');
    } catch (e) {
      Alert.alert('Network', String(e?.message || e));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);

  const tier1 = data?.queue?.tier_1_auto_approvable || [];
  const tier2 = data?.queue?.tier_2_review          || [];
  const tier3 = data?.queue?.tier_3_likely_reject   || [];

  const tier1Selected = useMemo(
    () => tier1.filter(m => selected[m.id]).map(m => m.id),
    [tier1, selected]
  );

  const toggleSelect = (id) => setSelected(s => ({ ...s, [id]: !s[id] }));
  const selectAllTier1 = () => setSelected(Object.fromEntries(tier1.map(m => [m.id, true])));
  const clearSelection = () => setSelected({});

  const bulkApprove = async () => {
    if (tier1Selected.length === 0) return;
    Alert.alert(
      'Bulk approve',
      `Approve ${tier1Selected.length} Tier-1 MoMs?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Approve all',
          style: 'default',
          onPress: async () => {
            const res = await api.post('/api/mom/bulk_approve', { mom_ids: tier1Selected });
            if (res?.ok) {
              Alert.alert('Done', `${res.data.approved_count} approved`);
              clearSelection();
              load();
            } else {
              Alert.alert('Failed', res?.error || 'Bulk approve failed');
            }
          },
        },
      ]
    );
  };

  const approveOne = async (row, cascade) => {
    const res = await api.post('/api/mom/approve', { mom_id: row.id, cascade_status: !!cascade });
    if (res?.ok) load();
    else Alert.alert('Failed', res?.error || 'Approve failed');
  };

  const submitReject = async () => {
    if (!rejectReason.trim()) return;
    const res = await api.post('/api/mom/reject', { mom_id: rejectFor.id, reason: rejectReason.trim() });
    if (res?.ok) {
      setRejectFor(null);
      setRejectReason('');
      load();
    } else {
      Alert.alert('Failed', res?.error || 'Reject failed');
    }
  };

  if (loading) {
    return (
      <SafeAreaView style={styles.center}>
        <ActivityIndicator />
        <Text style={styles.muted}>Loading approval queue…</Text>
      </SafeAreaView>
    );
  }

  const renderRow = ({ item }) => {
    const selectable = item.ai_tier === 1;
    const isSelected = !!selected[item.id];
    return (
      <View style={[styles.card, { borderLeftColor: TIER_COLOR[item.ai_tier] }]}>
        <View style={styles.cardHeader}>
          <Text style={styles.bd}>{item.bd_name || `BD #${item.bd_id}`}</Text>
          <Text style={[styles.tier, { color: TIER_COLOR[item.ai_tier] }]}>
            T{item.ai_tier} · {TIER_LABEL[item.ai_tier]}
          </Text>
        </View>
        <Text style={styles.school}>{item.school_name || `Lead #${item.lead_id}`}</Text>
        <Text style={styles.line} numberOfLines={2}>Discussed: {item.discussed || '—'}</Text>
        {!!item.next_step && (
          <Text style={styles.line} numberOfLines={1}>Next: {item.next_step}</Text>
        )}
        {!!item.status_recommendation && (
          <Text style={styles.statusRec}>
            BD suggests cstatus → {item.status_recommendation}
          </Text>
        )}
        <Text style={styles.meta}>
          {item.minutes_waiting}m waiting · ₹{Math.round((item.lead_revenue || 0) / 100000)}L
        </Text>
        {!!item.ai_reasoning && (
          <Text style={styles.reasoning} numberOfLines={2}>AI: {item.ai_reasoning}</Text>
        )}

        <View style={styles.actions}>
          {selectable && (
            <TouchableOpacity
              style={[styles.btn, isSelected ? styles.btnPrimary : styles.btnGhost]}
              onPress={() => toggleSelect(item.id)}>
              <Text style={isSelected ? styles.btnPrimaryText : styles.btnGhostText}>
                {isSelected ? '✓ Selected' : 'Select for bulk'}
              </Text>
            </TouchableOpacity>
          )}
          <TouchableOpacity style={[styles.btn, styles.btnApprove]} onPress={() => approveOne(item, true)}>
            <Text style={styles.btnApproveText}>Approve + cascade</Text>
          </TouchableOpacity>
          <TouchableOpacity style={[styles.btn, styles.btnReject]} onPress={() => setRejectFor(item)}>
            <Text style={styles.btnRejectText}>Reject</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  };

  const sections = [
    ...(tier1.length ? [{ title: `Tier 1 — Auto-approvable (${tier1.length})`, data: tier1 }] : []),
    ...(tier2.length ? [{ title: `Tier 2 — Review (${tier2.length})`, data: tier2 }] : []),
    ...(tier3.length ? [{ title: `Tier 3 — Likely reject (${tier3.length})`, data: tier3 }] : []),
  ];

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>MoM Approval Queue</Text>
        <Text style={styles.subtitle}>{data?.total || 0} pending · {data?.team_size || 0} BDs in your team</Text>
      </View>

      {tier1.length > 0 && (
        <View style={styles.bulkBar}>
          <TouchableOpacity onPress={tier1Selected.length === tier1.length ? clearSelection : selectAllTier1}>
            <Text style={styles.bulkBarLink}>
              {tier1Selected.length === tier1.length ? 'Clear' : 'Select all Tier 1'}
            </Text>
          </TouchableOpacity>
          <Text style={styles.muted}>{tier1Selected.length} selected</Text>
          <TouchableOpacity
            style={[styles.btnBulk, tier1Selected.length === 0 && styles.btnBulkDisabled]}
            disabled={tier1Selected.length === 0}
            onPress={bulkApprove}>
            <Text style={styles.btnBulkText}>Bulk approve</Text>
          </TouchableOpacity>
        </View>
      )}

      <FlatList
        data={sections.flatMap(s => [{ _section: s.title }, ...s.data])}
        keyExtractor={(it, i) => it._section ? `s${i}` : `m${it.id}`}
        renderItem={({ item }) => item._section
          ? <Text style={styles.sectionHeader}>{item._section}</Text>
          : renderRow({ item })
        }
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />
        }
        ListEmptyComponent={
          <View style={styles.center}>
            <Text style={styles.muted}>Queue empty. Nicely done.</Text>
          </View>
        }
        contentContainerStyle={{ paddingBottom: 40 }}
      />

      <Modal transparent visible={!!rejectFor} animationType="slide" onRequestClose={() => setRejectFor(null)}>
        <View style={styles.modalBg}>
          <View style={styles.modal}>
            <Text style={styles.modalTitle}>Reject MoM</Text>
            <Text style={styles.muted}>BD: {rejectFor?.bd_name} · {rejectFor?.school_name}</Text>
            <TextInput
              style={styles.input}
              multiline
              placeholder="Reason (sent back to BD)"
              value={rejectReason}
              onChangeText={setRejectReason}
            />
            <View style={styles.modalActions}>
              <TouchableOpacity onPress={() => { setRejectFor(null); setRejectReason(''); }}>
                <Text style={styles.btnGhostText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.btn, styles.btnReject, !rejectReason.trim() && { opacity: 0.4 }]}
                disabled={!rejectReason.trim()}
                onPress={submitReject}>
                <Text style={styles.btnRejectText}>Send back</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container:  { flex: 1, backgroundColor: '#F7F8FA' },
  center:     { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 24 },
  header:     { padding: 16, backgroundColor: '#FFF', borderBottomWidth: 1, borderBottomColor: '#E5E8EB' },
  title:      { fontSize: 18, fontWeight: '700', color: '#1B2A4E' },
  subtitle:   { color: '#5B6478', marginTop: 2 },
  muted:      { color: '#7A8395' },
  sectionHeader: { paddingHorizontal: 16, paddingTop: 16, paddingBottom: 6, fontWeight: '700', color: '#1B2A4E', textTransform: 'uppercase', fontSize: 12, letterSpacing: 0.5 },
  card:       { backgroundColor: '#FFF', marginHorizontal: 12, marginVertical: 6, padding: 14, borderRadius: 8, borderLeftWidth: 4, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 4, elevation: 1 },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 4 },
  bd:         { fontWeight: '700', color: '#1B2A4E' },
  tier:       { fontWeight: '700', fontSize: 12 },
  school:     { color: '#1B2A4E', marginBottom: 6 },
  line:       { color: '#3A4458', fontSize: 13, marginBottom: 2 },
  statusRec:  { color: '#5B6478', fontSize: 12, fontStyle: 'italic', marginTop: 4 },
  meta:       { color: '#7A8395', fontSize: 12, marginTop: 6 },
  reasoning:  { color: '#7A8395', fontSize: 11, marginTop: 2 },
  actions:    { flexDirection: 'row', marginTop: 10, flexWrap: 'wrap', gap: 8 },
  btn:        { paddingHorizontal: 10, paddingVertical: 6, borderRadius: 6, marginRight: 6, marginTop: 4 },
  btnPrimary: { backgroundColor: '#1B2A4E' },
  btnPrimaryText: { color: '#FFF', fontWeight: '600', fontSize: 12 },
  btnGhost:   { backgroundColor: '#EEF1F6' },
  btnGhostText: { color: '#1B2A4E', fontWeight: '600', fontSize: 12 },
  btnApprove: { backgroundColor: '#22A06B' },
  btnApproveText: { color: '#FFF', fontWeight: '600', fontSize: 12 },
  btnReject:  { backgroundColor: '#C04A3F' },
  btnRejectText: { color: '#FFF', fontWeight: '600', fontSize: 12 },
  bulkBar:    { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', padding: 12, backgroundColor: '#FFF8E1', borderBottomWidth: 1, borderBottomColor: '#F3E2A8' },
  bulkBarLink:{ color: '#1B2A4E', fontWeight: '700' },
  btnBulk:    { backgroundColor: '#22A06B', paddingHorizontal: 12, paddingVertical: 8, borderRadius: 6 },
  btnBulkDisabled: { opacity: 0.4 },
  btnBulkText:{ color: '#FFF', fontWeight: '700' },
  modalBg:    { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'flex-end' },
  modal:      { backgroundColor: '#FFF', padding: 18, borderTopLeftRadius: 14, borderTopRightRadius: 14 },
  modalTitle: { fontSize: 16, fontWeight: '700', marginBottom: 6, color: '#1B2A4E' },
  input:      { borderWidth: 1, borderColor: '#D5DAE2', borderRadius: 6, padding: 10, marginTop: 12, minHeight: 90, textAlignVertical: 'top' },
  modalActions:{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 14 },
});
