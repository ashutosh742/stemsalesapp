// ActivityScreen — chronological feed of sales activities (calls, visits, MoMs, status changes).
// Filterable by activity type. Today / This week / Older sections.

import React, { useMemo, useState } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';

// Mock sales activity feed
const activities = [
  { id: 'a1', type: 'call',    icon: 'call',           color: '#3498DB', who: 'You',       title: 'Called Mr. Bhandari (KV Andheri)', body: 'No answer · left voicemail', lead: 'L-1042', when: '10 min ago', bucket: 'today' },
  { id: 'a2', type: 'mom',     icon: 'document-text',  color: '#9B59B6', who: 'You',       title: 'MoM filed for Govt. HS Pune visit', body: 'Quality 86 · next action: send revised timeline', lead: 'L-1041', when: '1h ago', bucket: 'today' },
  { id: 'a3', type: 'visit',   icon: 'walk',           color: '#F39C12', who: 'You',       title: 'Site visit · Govt. HS Pune', body: '45 min · met principal + DM', lead: 'L-1041', when: '4h ago', bucket: 'today' },
  { id: 'a4', type: 'status',  icon: 'arrow-forward',  color: '#2ECC71', who: 'Anita S.',  title: 'L-1040 moved to Negotiation', body: 'Z.P. School Nashik · Astronomy Lab · ₹3.1L', lead: 'L-1040', when: '6h ago', bucket: 'today' },
  { id: 'a5', type: 'won',     icon: 'trophy',         color: '#10B981', who: 'Ravi K.',   title: 'Closed L-1036 · ₹1.8L', body: 'Zilla Parishad Aurangabad · DIY Programs', lead: 'L-1036', when: 'Yesterday', bucket: 'week' },
  { id: 'a6', type: 'visit',   icon: 'walk',           color: '#F39C12', who: 'Vikram T.', title: 'Site visit · DAV Public Nagpur', body: '60 min · proposal walkthrough', lead: 'L-1038', when: '2 days ago', bucket: 'week' },
  { id: 'a7', type: 'mom',     icon: 'document-text',  color: '#9B59B6', who: 'Priya M.',  title: 'MoM filed for Sarvodaya Vidyalaya', body: 'Quality 74 · CM flagged for review', lead: 'L-1037', when: '2 days ago', bucket: 'week' },
  { id: 'a8', type: 'call',    icon: 'call',           color: '#3498DB', who: 'Anita S.',  title: 'Called Z.P. Nashik principal',     body: '12 min · sent revised quote', lead: 'L-1040', when: '3 days ago', bucket: 'week' },
  { id: 'a9', type: 'risk',    icon: 'warning',        color: '#EF4444', who: 'System',    title: 'L-1042 stalled 4 days',           body: 'No DM contact since proposal sent', lead: 'L-1042', when: '4 days ago', bucket: 'older' },
  { id: 'a10', type: 'status', icon: 'arrow-forward',  color: '#2ECC71', who: 'Priya M.',  title: 'L-1039 moved to New',             body: 'Municipal School Borivali · ESG Lab · ₹5.4L', lead: 'L-1039', when: '5 days ago', bucket: 'older' },
];

const FILTERS = [
  { id: 'all',    label: 'All' },
  { id: 'call',   label: 'Calls' },
  { id: 'visit',  label: 'Visits' },
  { id: 'mom',    label: 'MoMs' },
  { id: 'status', label: 'Status' },
];

const BUCKETS = [
  { id: 'today', label: 'Today' },
  { id: 'week',  label: 'This week' },
  { id: 'older', label: 'Older' },
];

export default function ActivityScreen() {
  const [filter, setFilter] = useState('all');

  const filtered = useMemo(() => {
    if (filter === 'all') return activities;
    return activities.filter((a) => a.type === filter || (filter === 'status' && (a.type === 'won' || a.type === 'risk' || a.type === 'status')));
  }, [filter]);

  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" />

      <LinearGradient
        colors={[colors.spaceTop, colors.spaceBottom]}
        start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
        style={styles.header}
      >
        <Text style={styles.headerKicker}>Activity</Text>
        <Text style={styles.headerTitle}>What happened recently</Text>
        <Text style={styles.headerSub}>Calls · visits · MoMs · status changes across your cluster</Text>
      </LinearGradient>

      {/* Filter chips */}
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterRow}>
        {FILTERS.map((f) => {
          const active = filter === f.id;
          return (
            <Pressable
              key={f.id}
              onPress={() => setFilter(f.id)}
              style={[styles.chip, active && styles.chipActive]}
            >
              <Text style={[styles.chipText, active && styles.chipTextActive]}>{f.label}</Text>
            </Pressable>
          );
        })}
      </ScrollView>

      <ScrollView contentContainerStyle={{ paddingBottom: 32 }} showsVerticalScrollIndicator={false}>
        {BUCKETS.map((b) => {
          const items = filtered.filter((a) => a.bucket === b.id);
          if (items.length === 0) return null;
          return (
            <View key={b.id} style={styles.bucket}>
              <Text style={styles.bucketLabel}>{b.label}</Text>
              {items.map((a, idx) => (
                <View key={a.id} style={styles.row}>
                  {/* Timeline line + dot */}
                  <View style={styles.timeline}>
                    <View style={[styles.iconCircle, { backgroundColor: a.color + '22' }]}>
                      <Ionicons name={a.icon} size={14} color={a.color} />
                    </View>
                    {idx < items.length - 1 && <View style={styles.line} />}
                  </View>
                  {/* Card */}
                  <View style={styles.card}>
                    <View style={styles.cardTop}>
                      <Text style={styles.cardTitle} numberOfLines={1}>{a.title}</Text>
                      <Text style={styles.cardWhen}>{a.when}</Text>
                    </View>
                    <Text style={styles.cardBody} numberOfLines={2}>{a.body}</Text>
                    <View style={styles.cardMeta}>
                      <View style={styles.whoChip}>
                        <Text style={styles.whoText}>{a.who}</Text>
                      </View>
                      <Text style={styles.leadChip}>{a.lead}</Text>
                    </View>
                  </View>
                </View>
              ))}
            </View>
          );
        })}

        {filtered.length === 0 && (
          <View style={styles.empty}>
            <Ionicons name="filter" size={28} color={colors.textMuted} />
            <Text style={styles.emptyText}>No activity matches this filter</Text>
          </View>
        )}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: {
    paddingTop: Platform.OS === 'ios' ? 58 : 38,
    paddingHorizontal: 20, paddingBottom: 20,
    borderBottomLeftRadius: 24, borderBottomRightRadius: 24,
  },
  headerKicker: { color: 'rgba(255,255,255,0.7)', fontSize: 12, fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.6 },
  headerTitle: { color: '#fff', fontSize: 22, fontWeight: '700', marginTop: 4 },
  headerSub: { color: 'rgba(255,255,255,0.65)', fontSize: 12, marginTop: 4 },

  filterRow: { paddingHorizontal: 14, paddingVertical: 12, gap: 8 },
  chip: {
    paddingHorizontal: 14, paddingVertical: 7, borderRadius: 18,
    backgroundColor: colors.card, borderWidth: 1, borderColor: colors.border,
  },
  chipActive: { backgroundColor: colors.btnFrom, borderColor: colors.btnFrom },
  chipText: { color: colors.text, fontSize: 12.5, fontWeight: '600' },
  chipTextActive: { color: '#fff' },

  bucket: { marginTop: 6 },
  bucketLabel: {
    color: colors.textMuted, fontSize: 11, fontWeight: '700',
    textTransform: 'uppercase', letterSpacing: 0.5,
    paddingHorizontal: 20, paddingVertical: 8,
  },

  row: { flexDirection: 'row', paddingHorizontal: 16, gap: 12 },
  timeline: { alignItems: 'center', width: 28 },
  iconCircle: {
    width: 28, height: 28, borderRadius: 14,
    alignItems: 'center', justifyContent: 'center',
  },
  line: { flex: 1, width: 2, backgroundColor: colors.border, marginTop: 4 },

  card: {
    flex: 1, backgroundColor: colors.card,
    borderRadius: 12, padding: 12, marginBottom: 10,
    borderWidth: 1, borderColor: colors.border,
  },
  cardTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', gap: 8 },
  cardTitle: { color: colors.text, fontWeight: '700', fontSize: 13, flex: 1 },
  cardWhen: { color: colors.textMuted, fontSize: 10.5, fontWeight: '500' },
  cardBody: { color: colors.textMuted, fontSize: 12, marginTop: 4, lineHeight: 16.5 },
  cardMeta: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 8 },
  whoChip: { backgroundColor: colors.cardAlt, paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6 },
  whoText: { color: colors.text, fontSize: 10.5, fontWeight: '600' },
  leadChip: { color: colors.btnFrom, fontSize: 10.5, fontWeight: '700', letterSpacing: 0.3 },

  empty: { alignItems: 'center', paddingVertical: 40, gap: 8 },
  emptyText: { color: colors.textMuted, fontSize: 13 },
});
