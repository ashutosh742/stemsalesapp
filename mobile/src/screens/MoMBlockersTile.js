// MoMBlockersTile - CM-only view of MoMs that are gating cstatus jumps in their cluster.
// Mirrors migration 012 mom_blocker rule: cstatus 3 to 6 and 8 to 9 jumps require approved MoM.
// Endpoint: GET /api/progression/mom_blockers?days=7
//
// Two sub-buckets:
//   pending_approval - mom_data row exists but approved_status IS NULL
//   not_written      - RP meeting over 1 day ago AND no mom_data row at all

import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CURRENT_USER } from '../data/roles';

const DEMO_BLOCKERS = {
  pending_approval: [
    { bd_name: 'Ravi Kumar',  school: 'Don Bosco Andheri', days_pending: 3, action_type: 4 },
    { bd_name: 'Sneha Iyer',  school: 'Podar Intl Powai',  days_pending: 2, action_type: 3 },
  ],
  not_written: [
    { bd_name: 'Vikram Tyagi', school: 'Ryan Intl Malad',  days_since: 4, action_type: 3 },
  ],
};

const ACTION_LABEL = { 3: 'Scheduled', 4: 'Barge' };

export default function MoMBlockersTile({ data = DEMO_BLOCKERS, onPressMom }) {
  // CM only - they hold the approval queue. RM sees a roll-up elsewhere.
  if (CURRENT_USER.role !== 'CM') return null;

  const pending = data.pending_approval || [];
  const notWritten = data.not_written || [];
  const total = pending.length + notWritten.length;

  if (total === 0) {
    return (
      <View style={[s.tile, s.tileEmpty]}>
        <Ionicons name="checkmark-circle" size={18} color="#10b981" />
        <Text style={s.emptyText}>No MoM blockers. Cluster pipeline is clean.</Text>
      </View>
    );
  }

  const isRed = pending.length > 5;

  return (
    <View style={[s.tile, isRed && s.tileRed]}>
      <View style={s.header}>
        <View style={s.headerLeft}>
          <Ionicons
            name="document-text-outline"
            size={16}
            color={isRed ? '#ef4444' : '#f59e0b'}
          />
          <Text style={s.title}>MoM blockers in your cluster</Text>
        </View>
        <View style={[s.countPill, isRed && s.countPillRed]}>
          <Text style={[s.countText, isRed && s.countTextRed]}>{total}</Text>
        </View>
      </View>
      <Text style={s.subtitle}>
        Cstatus jumps blocked until MoM is approved. Clear these to unblock BD progression.
      </Text>

      {pending.length > 0 && (
        <View style={s.section}>
          <Text style={s.sectionTitle}>PENDING YOUR APPROVAL</Text>
          {pending.map((row, i) => (
            <Pressable
              key={'p' + i}
              onPress={() => onPressMom && onPressMom(row)}
              style={s.row}
            >
              <View style={{ flex: 1 }}>
                <Text style={s.bdName}>{row.bd_name}</Text>
                <Text style={s.meta} numberOfLines={1}>
                  {row.school} . {ACTION_LABEL[row.action_type] || 'Meeting'}
                </Text>
              </View>
              <View style={s.daysBadge}>
                <Text style={s.daysText}>{row.days_pending}d</Text>
              </View>
              <Ionicons name="chevron-forward" size={16} color={colors.textMuted} />
            </Pressable>
          ))}
        </View>
      )}

      {notWritten.length > 0 && (
        <View style={s.section}>
          <Text style={s.sectionTitle}>MOM NOT WRITTEN YET</Text>
          {notWritten.map((row, i) => (
            <View key={'n' + i} style={s.row}>
              <View style={{ flex: 1 }}>
                <Text style={s.bdName}>{row.bd_name}</Text>
                <Text style={s.meta} numberOfLines={1}>
                  {row.school} . {ACTION_LABEL[row.action_type] || 'Meeting'}
                </Text>
              </View>
              <View style={[s.daysBadge, s.daysBadgeWarn]}>
                <Text style={[s.daysText, s.daysTextWarn]}>{row.days_since}d</Text>
              </View>
            </View>
          ))}
        </View>
      )}

      <Text style={s.footnote}>
        Source: mom_data, cstatus jump rules from migration 012
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
  tileRed: { borderColor: '#ef4444', borderWidth: 1.5 },
  tileEmpty: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingVertical: 12,
  },
  emptyText: { color: colors.text, fontSize: 12 },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  headerLeft: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  title: { color: colors.text, fontWeight: '700', fontSize: 14 },
  subtitle: { color: colors.textMuted, fontSize: 11, marginTop: 4, lineHeight: 15 },
  countPill: {
    backgroundColor: '#f59e0b22',
    borderColor: '#f59e0b',
    borderWidth: 1,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 999,
  },
  countPillRed: { backgroundColor: '#ef444422', borderColor: '#ef4444' },
  countText: { color: '#f59e0b', fontSize: 11, fontWeight: '800' },
  countTextRed: { color: '#ef4444' },
  section: { marginTop: 12 },
  sectionTitle: {
    color: colors.textMuted,
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 0.8,
    marginBottom: 6,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    paddingVertical: 8,
    borderTopWidth: 1,
    borderTopColor: colors.border,
  },
  bdName: { color: colors.text, fontWeight: '600', fontSize: 13 },
  meta: { color: colors.textMuted, fontSize: 11, marginTop: 1 },
  daysBadge: {
    backgroundColor: '#ef444422',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  daysBadgeWarn: { backgroundColor: '#f59e0b22' },
  daysText: { color: '#ef4444', fontSize: 11, fontWeight: '800' },
  daysTextWarn: { color: '#f59e0b' },
  footnote: { color: colors.textMuted, fontSize: 10, marginTop: 10, textAlign: 'right' },
});
