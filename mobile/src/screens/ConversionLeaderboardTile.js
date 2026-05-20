// ConversionLeaderboardTile - shows top BDs by positive conversions and RP meetings.
// Two toggles: Today | This week. Endpoints:
//   GET /api/leaderboard/daily?day=<today>
//   GET /api/leaderboard/weekly?from=<mon>&to=<today>
//   GET /api/leaderboard/rp?from=<7d ago>&to=<today>
//
// Visibility: all roles. BD sees their own row highlighted. CM sees full cluster.

import React, { useState } from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CURRENT_USER } from '../data/roles';

const DEMO_DAILY = [
  { bd_uid: 42, bd_name: 'Priya Menon',  positive_conversions: 3, negative_conversions: 0, won_count: 1, won_value_rs: 380000, rp_meetings: 2, net_positive: 3 },
  { bd_uid: 46, bd_name: 'Sneha Iyer',   positive_conversions: 2, negative_conversions: 0, won_count: 0, won_value_rs: 0,      rp_meetings: 3, net_positive: 2 },
  { bd_uid: 43, bd_name: 'Ravi Kumar',   positive_conversions: 2, negative_conversions: 1, won_count: 0, won_value_rs: 0,      rp_meetings: 1, net_positive: 1 },
  { bd_uid: 44, bd_name: 'Anita Sharma', positive_conversions: 1, negative_conversions: 0, won_count: 0, won_value_rs: 0,      rp_meetings: 2, net_positive: 1 },
  { bd_uid: 45, bd_name: 'Vikram Tyagi', positive_conversions: 0, negative_conversions: 2, won_count: 0, won_value_rs: 0,      rp_meetings: 0, net_positive: -2 },
];

function formatRs(n) {
  if (!n) return '';
  if (n >= 100000) return 'Rs ' + (n / 100000).toFixed(1) + ' lakh';
  if (n >= 1000)   return 'Rs ' + Math.round(n / 1000) + 'k';
  return 'Rs ' + n;
}

function rankBadge(i) {
  if (i === 0) return { color: '#f59e0b', label: '1' };
  if (i === 1) return { color: '#94a3b8', label: '2' };
  if (i === 2) return { color: '#b45309', label: '3' };
  return { color: colors.textMuted, label: String(i + 1) };
}

export default function ConversionLeaderboardTile({
  daily = DEMO_DAILY,
  weekly = null,
  onPressRow,
}) {
  const [tab, setTab] = useState('today');
  const rows = tab === 'today' ? daily : (weekly || daily);

  // Sort by positive_conversions desc, won_value_rs as tiebreak
  const sorted = [...rows].sort((a, b) => {
    if (b.positive_conversions !== a.positive_conversions) {
      return b.positive_conversions - a.positive_conversions;
    }
    return (b.won_value_rs || 0) - (a.won_value_rs || 0);
  });

  return (
    <View style={s.tile}>
      <View style={s.header}>
        <View style={s.headerLeft}>
          <Ionicons name="trophy-outline" size={16} color="#f59e0b" />
          <Text style={s.title}>Conversion leaderboard</Text>
        </View>
        <View style={s.tabs}>
          <Pressable
            onPress={() => setTab('today')}
            style={[s.tab, tab === 'today' && s.tabActive]}
          >
            <Text style={[s.tabText, tab === 'today' && s.tabTextActive]}>Today</Text>
          </Pressable>
          <Pressable
            onPress={() => setTab('week')}
            style={[s.tab, tab === 'week' && s.tabActive]}
          >
            <Text style={[s.tabText, tab === 'week' && s.tabTextActive]}>Week</Text>
          </Pressable>
        </View>
      </View>
      <Text style={s.subtitle}>
        Positive conversions are jumps to Positive, Very Positive, or Won. RP meetings are barge closes that captured the decision maker.
      </Text>

      <View style={s.columns}>
        <Text style={[s.colHead, { flex: 0.4 }]}>#</Text>
        <Text style={[s.colHead, { flex: 1.8 }]}>BD</Text>
        <Text style={[s.colHead, s.colNum]}>Pos</Text>
        <Text style={[s.colHead, s.colNum]}>Neg</Text>
        <Text style={[s.colHead, s.colNum]}>RP</Text>
        <Text style={[s.colHead, { flex: 1.1, textAlign: 'right' }]}>Won Rs</Text>
      </View>

      {sorted.slice(0, 5).map((r, i) => {
        const isMe = r.bd_uid === CURRENT_USER.id;
        const rank = rankBadge(i);
        return (
          <Pressable
            key={r.bd_uid}
            onPress={() => onPressRow && onPressRow(r)}
            style={[s.row, isMe && s.rowMe]}
          >
            <View style={[s.rankPill, { backgroundColor: rank.color + '22', borderColor: rank.color }]}>
              <Text style={[s.rankText, { color: rank.color }]}>{rank.label}</Text>
            </View>
            <Text
              style={[s.bd, isMe && s.bdMe]}
              numberOfLines={1}
            >
              {r.bd_name}{isMe ? ' (you)' : ''}
            </Text>
            <Text style={[s.numCell, { color: '#10b981' }]}>{r.positive_conversions}</Text>
            <Text style={[s.numCell, { color: r.negative_conversions > 0 ? '#ef4444' : colors.textMuted }]}>
              {r.negative_conversions}
            </Text>
            <Text style={s.numCell}>{r.rp_meetings}</Text>
            <Text style={[s.wonCell, r.won_value_rs > 0 && { color: '#10b981', fontWeight: '700' }]}>
              {r.won_value_rs > 0 ? formatRs(r.won_value_rs) : '-'}
            </Text>
          </Pressable>
        );
      })}

      <Text style={s.footnote}>
        Stage-weighted credit. Sources: lead_progression_log, conversion_attribution
      </Text>
    </View>
  );
}

const s = StyleSheet.create({
  tile: {
    backgroundColor: colors.card,
    marginHorizontal: 16,
    marginTop: 14,
    padding: 14,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
  },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  headerLeft: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  title: { color: colors.text, fontWeight: '700', fontSize: 14 },
  subtitle: { color: colors.textMuted, fontSize: 11, marginTop: 4, lineHeight: 15 },
  tabs: { flexDirection: 'row', backgroundColor: colors.cardAlt, borderRadius: 8, padding: 2 },
  tab: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 6 },
  tabActive: { backgroundColor: colors.card },
  tabText: { color: colors.textMuted, fontSize: 11, fontWeight: '700' },
  tabTextActive: { color: colors.text },
  columns: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 12,
    paddingBottom: 6,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    gap: 4,
  },
  colHead: { color: colors.textMuted, fontSize: 10, fontWeight: '800', letterSpacing: 0.5 },
  colNum: { flex: 0.5, textAlign: 'center' },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 9,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    gap: 4,
  },
  rowMe: { backgroundColor: colors.btnFrom + '12' },
  rankPill: {
    flex: 0.4,
    paddingHorizontal: 4,
    paddingVertical: 2,
    borderRadius: 6,
    borderWidth: 1,
    alignItems: 'center',
    maxWidth: 26,
  },
  rankText: { fontSize: 10, fontWeight: '800' },
  bd: { flex: 1.8, color: colors.text, fontSize: 12, fontWeight: '600' },
  bdMe: { color: colors.btnFrom, fontWeight: '800' },
  numCell: { flex: 0.5, color: colors.text, fontSize: 13, fontWeight: '700', textAlign: 'center' },
  wonCell: { flex: 1.1, color: colors.textMuted, fontSize: 12, textAlign: 'right' },
  footnote: { color: colors.textMuted, fontSize: 10, marginTop: 10, textAlign: 'right' },
});
