// CMActivitiesScreen — unified activity feed across the cluster.
// All BD events: calls, emails, visits, MoMs, stage transitions, plan submissions.
//
// Backend mapping:
//   - Feed:      AIAgents/ActivityFeed_model::cluster_feed(cm_id, since, kinds[])
//   - Filters:   ?kind=call|email|visit|mom|stage|plan & ?bd=<id>
//   - Open lead: navigates to LeadDetail screen

import React, { useState, useMemo } from 'react';
import { View, Text, ScrollView, Pressable, StyleSheet } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CM_ACTIVITY_FEED, CLUSTER_BDS } from '../data/cm';

const KIND_META = {
  call:  { icon: 'call',           bg: '#3498DB' },
  email: { icon: 'mail',           bg: '#9B59B6' },
  visit: { icon: 'walk',           bg: '#F39C12' },
  mom:   { icon: 'document-text',  bg: '#14B8A6' },
  stage: { icon: 'git-branch',     bg: '#10B981' },
  plan:  { icon: 'calendar',       bg: '#3E21FB' },
};

const FILTERS = [
  { id: 'all',   label: 'All' },
  { id: 'call',  label: 'Calls' },
  { id: 'email', label: 'Emails' },
  { id: 'visit', label: 'Visits' },
  { id: 'mom',   label: 'MoMs' },
  { id: 'stage', label: 'Stage moves' },
];

export default function CMActivitiesScreen({ navigation }) {
  const [filter, setFilter] = useState('all');
  const [bdFilter, setBdFilter] = useState('all');

  const items = useMemo(() => CM_ACTIVITY_FEED.filter(a => {
    if (filter !== 'all' && a.kind !== filter) return false;
    if (bdFilter !== 'all' && a.bd.id !== bdFilter) return false;
    return true;
  }), [filter, bdFilter]);

  const counts = useMemo(() => ({
    total: CM_ACTIVITY_FEED.length,
    live:  CM_ACTIVITY_FEED.filter(a => a.tag === 'LIVE').length,
    review: CM_ACTIVITY_FEED.filter(a => a.tag === 'REVIEW').length,
  }), []);

  return (
    <View style={{ flex: 1, backgroundColor: colors.cardAlt }}>
      <LinearGradient colors={['#1F1147', '#3E21FB']} style={styles.hero}>
        <View style={styles.heroTop}>
          <Pressable onPress={() => navigation?.goBack?.()} style={styles.iconBtn}>
            <Ionicons name="chevron-back" size={20} color="#fff" />
          </Pressable>
          <Text style={styles.heroEyebrow}>CLUSTER ACTIVITY · LIVE</Text>
          <View style={{ width: 36 }} />
        </View>
        <Text style={styles.heroTitle}>Activities</Text>
        <Text style={styles.heroSub}>{counts.total} events today across {CLUSTER_BDS.length} BDs</Text>

        <View style={styles.statsRow}>
          <View style={styles.statCard}>
            <Text style={[styles.statNum, { color: '#FCA5A5' }]}>{counts.live}</Text>
            <Text style={styles.statLabel}>live now</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={[styles.statNum, { color: '#FCD34D' }]}>{counts.review}</Text>
            <Text style={styles.statLabel}>need review</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNum}>{counts.total}</Text>
            <Text style={styles.statLabel}>total 24h</Text>
          </View>
        </View>
      </LinearGradient>

      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.chipRow}>
        {FILTERS.map(f => (
          <Pressable key={f.id} onPress={() => setFilter(f.id)} style={[styles.chip, filter === f.id && styles.chipActive]}>
            <Text style={[styles.chipText, filter === f.id && styles.chipTextActive]}>{f.label}</Text>
          </Pressable>
        ))}
      </ScrollView>

      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={[styles.chipRow, { paddingTop: 0 }]}>
        <Pressable onPress={() => setBdFilter('all')} style={[styles.chip, bdFilter === 'all' && styles.chipActiveBD]}>
          <Text style={[styles.chipText, bdFilter === 'all' && styles.chipTextActive]}>All BDs</Text>
        </Pressable>
        {CLUSTER_BDS.map(bd => (
          <Pressable key={bd.id} onPress={() => setBdFilter(bd.id)} style={[styles.chip, bdFilter === bd.id && styles.chipActiveBD]}>
            <View style={[styles.chipAvatar, { backgroundColor: bd.color }]}><Text style={styles.chipAvatarText}>{bd.initials}</Text></View>
            <Text style={[styles.chipText, bdFilter === bd.id && styles.chipTextActive]}>{bd.name.split(' ')[0]}</Text>
          </Pressable>
        ))}
      </ScrollView>

      <ScrollView contentContainerStyle={{ padding: 12, paddingBottom: 24 }}>
        {items.length === 0 && (
          <View style={styles.empty}>
            <Ionicons name="leaf-outline" size={28} color={colors.textMuted} />
            <Text style={styles.emptyText}>No matching activity</Text>
          </View>
        )}
        {items.map(a => {
          const m = KIND_META[a.kind] || KIND_META.call;
          return (
            <Pressable
              key={a.id}
              style={styles.row}
              onPress={() => a.lead && navigation?.navigate?.('LeadDetail', { leadId: a.lead })}
            >
              <View style={[styles.kindIcon, { backgroundColor: m.bg }]}>
                <Ionicons name={m.icon} size={16} color="#fff" />
              </View>
              <View style={{ flex: 1 }}>
                <View style={styles.rowTop}>
                  <View style={styles.rowTopLeft}>
                    <View style={[styles.miniAv, { backgroundColor: a.bd.color }]}><Text style={styles.miniAvText}>{a.bd.initials}</Text></View>
                    <Text style={styles.bdName}>{a.bd.name}</Text>
                  </View>
                  <Text style={styles.when}>{a.when}</Text>
                </View>
                <Text style={styles.detail}>{a.detail}</Text>
                <View style={styles.rowBottom}>
                  {a.lead && (
                    <View style={styles.leadChip}>
                      <Ionicons name="flash" size={10} color={colors.btnFrom} />
                      <Text style={styles.leadChipText}>{a.lead} · {a.school}</Text>
                    </View>
                  )}
                  {a.tag !== '' && (
                    <View style={[styles.tag, { backgroundColor: `${a.tagColor}1A`, borderColor: `${a.tagColor}55` }]}>
                      <Text style={[styles.tagText, { color: a.tagColor }]}>{a.tag}</Text>
                    </View>
                  )}
                </View>
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
  heroEyebrow: { color: 'rgba(255,255,255,0.78)', fontSize: 11, fontWeight: '800', letterSpacing: 1 },
  heroTitle: { color: '#fff', fontSize: 30, fontWeight: '800', marginTop: 18 },
  heroSub: { color: 'rgba(255,255,255,0.82)', fontSize: 13, marginTop: 6 },
  statsRow: { flexDirection: 'row', gap: 10, marginTop: 18 },
  statCard: { flex: 1, backgroundColor: 'rgba(255,255,255,0.10)', borderRadius: 12, padding: 12, borderWidth: 1, borderColor: 'rgba(255,255,255,0.10)' },
  statNum: { color: '#fff', fontSize: 22, fontWeight: '800' },
  statLabel: { color: 'rgba(255,255,255,0.78)', fontSize: 11, marginTop: 2, fontWeight: '600' },
  chipRow: { paddingHorizontal: 12, paddingTop: 12, paddingBottom: 4, gap: 8 },
  chip: { flexDirection: 'row', alignItems: 'center', gap: 6, backgroundColor: '#fff', borderColor: colors.border, borderWidth: 1, paddingHorizontal: 10, paddingVertical: 6, borderRadius: 999 },
  chipActive: { backgroundColor: colors.btnFrom, borderColor: colors.btnFrom },
  chipActiveBD: { backgroundColor: '#1F2937', borderColor: '#1F2937' },
  chipText: { fontSize: 12, color: colors.text, fontWeight: '600' },
  chipTextActive: { color: '#fff' },
  chipAvatar: { width: 18, height: 18, borderRadius: 9, alignItems: 'center', justifyContent: 'center' },
  chipAvatarText: { color: '#fff', fontSize: 9, fontWeight: '800' },
  empty: { alignItems: 'center', padding: 32, gap: 8 },
  emptyText: { color: colors.textMuted, fontSize: 13 },
  row: { flexDirection: 'row', gap: 12, backgroundColor: '#fff', borderRadius: 14, padding: 12, marginTop: 10, borderWidth: 1, borderColor: colors.border },
  kindIcon: { width: 36, height: 36, borderRadius: 12, alignItems: 'center', justifyContent: 'center' },
  rowTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  rowTopLeft: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  miniAv: { width: 18, height: 18, borderRadius: 9, alignItems: 'center', justifyContent: 'center' },
  miniAvText: { color: '#fff', fontSize: 9, fontWeight: '800' },
  bdName: { fontSize: 13, fontWeight: '700', color: colors.text },
  when: { fontSize: 11, color: colors.textMuted, fontWeight: '600' },
  detail: { fontSize: 13, color: colors.text, marginTop: 4 },
  rowBottom: { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 6, flexWrap: 'wrap' },
  leadChip: { flexDirection: 'row', alignItems: 'center', gap: 4, backgroundColor: 'rgba(62,33,251,0.06)', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 999, borderWidth: 1, borderColor: 'rgba(62,33,251,0.20)' },
  leadChipText: { color: colors.btnFrom, fontSize: 10, fontWeight: '700' },
  tag: { paddingHorizontal: 8, paddingVertical: 3, borderRadius: 999, borderWidth: 1 },
  tagText: { fontSize: 9, fontWeight: '800', letterSpacing: 0.5 },
});
