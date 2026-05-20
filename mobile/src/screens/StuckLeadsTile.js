// StuckLeadsTile - shows the BD their own longest-stuck leads per cstatus stage.
// Mirrors migration 012 lead_progression_log and the discipline stuck thresholds:
//   cstatus 1 over 3 days, 2 over 5, 3 over 5, 6 over 7, 7 over 14, 8 over 30, 9 over 14.
// Endpoint: GET /api/progression/stuck?top_n=5 (BD scope is auto-filtered to self by session).

import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CURRENT_USER } from '../data/roles';

const STAGE_LABELS = {
  1: 'Open',
  2: 'Reachout',
  3: 'Tentative',
  6: 'Positive',
  7: 'Proposal sent',
  8: 'Open RPEM',
  9: 'Very Positive',
};

// Demo payload - in production this is fetched from /api/progression/stuck
const DEMO_STUCK = [
  { lead_id: 8821, school: 'Don Bosco Andheri',  cstatus: 6, days: 11, fbudget: 380000 },
  { lead_id: 8794, school: 'Ryan Intl Malad',    cstatus: 3, days: 8,  fbudget: 220000 },
  { lead_id: 8755, school: 'Podar Intl Powai',   cstatus: 2, days: 7,  fbudget: 150000 },
];

function formatRs(n) {
  if (!n) return 'Rs 0';
  if (n >= 100000) return 'Rs ' + (n / 100000).toFixed(1) + ' lakh';
  if (n >= 1000) return 'Rs ' + Math.round(n / 1000) + 'k';
  return 'Rs ' + n;
}

export default function StuckLeadsTile({ data = DEMO_STUCK, onPressLead }) {
  // BD sees own stuck list. CM sees an aggregate version elsewhere.
  if (CURRENT_USER.role !== 'BD') return null;
  if (!data || data.length === 0) {
    return (
      <View style={[s.tile, s.tileEmpty]}>
        <Ionicons name="checkmark-circle" size={18} color="#10b981" />
        <Text style={s.emptyText}>No stuck leads. Pipeline is flowing.</Text>
      </View>
    );
  }

  return (
    <View style={s.tile}>
      <View style={s.header}>
        <View style={s.headerLeft}>
          <Ionicons name="time-outline" size={16} color="#f59e0b" />
          <Text style={s.title}>Leads needing a push</Text>
        </View>
        <View style={s.countPill}>
          <Text style={s.countText}>{data.length}</Text>
        </View>
      </View>
      <Text style={s.subtitle}>
        Past the stuck threshold for their current stage. Move them today.
      </Text>

      <View style={s.list}>
        {data.map((lead, i) => (
          <Pressable
            key={lead.lead_id}
            onPress={() => onPressLead && onPressLead(lead)}
            style={[s.row, i === data.length - 1 && { borderBottomWidth: 0 }]}
          >
            <View style={s.stageBadge}>
              <Text style={s.stageBadgeText}>
                {STAGE_LABELS[lead.cstatus] || ('cs ' + lead.cstatus)}
              </Text>
            </View>
            <View style={{ flex: 1 }}>
              <Text style={s.school} numberOfLines={1}>{lead.school}</Text>
              <Text style={s.meta}>
                {lead.days} days in stage . {formatRs(lead.fbudget)}
              </Text>
            </View>
            <Ionicons name="chevron-forward" size={16} color={colors.textMuted} />
          </Pressable>
        ))}
      </View>

      <Text style={s.footnote}>
        Thresholds: Open over 3, Reachout over 5, Tentative over 5, Positive over 7
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
  countPill: {
    backgroundColor: '#f59e0b22',
    borderColor: '#f59e0b',
    borderWidth: 1,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 999,
  },
  countText: { color: '#f59e0b', fontSize: 11, fontWeight: '800' },
  subtitle: { color: colors.textMuted, fontSize: 11, marginTop: 4, lineHeight: 15 },
  list: { marginTop: 10, borderTopWidth: 1, borderTopColor: colors.border },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  stageBadge: {
    backgroundColor: colors.cardAlt,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
    minWidth: 70,
    alignItems: 'center',
  },
  stageBadgeText: { color: colors.text, fontSize: 10, fontWeight: '700', letterSpacing: 0.3 },
  school: { color: colors.text, fontWeight: '600', fontSize: 13 },
  meta: { color: colors.textMuted, fontSize: 11, marginTop: 1 },
  footnote: { color: colors.textMuted, fontSize: 10, marginTop: 10, textAlign: 'right' },
});
