// ApplauseBanner - praise surface for the logged-in BD.
// Shows up to 3 most recent applause_log rows from today, gradient banner.
// Endpoint: GET /api/applause/today?uid=me
// Animates in when a new row arrives via push (out of scope for demo).
//
// Visibility: BD sees own praise. CM sees cluster roll-up via a different layout.

import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CURRENT_USER } from '../data/roles';

const DEMO_TODAY = [
  {
    id: 1,
    event_type: 'won_closure',
    school_name: 'Don Bosco Andheri',
    closed_value_rs: 380000,
    message: 'Priya closed Don Bosco Andheri worth Rs 3,80,000',
    created_at: '2026-05-15T14:22:00',
  },
  {
    id: 2,
    event_type: 'to_very_positive',
    school_name: 'Ryan Intl Malad',
    closed_value_rs: null,
    message: 'Priya moved Ryan Intl Malad to Very Positive',
    created_at: '2026-05-15T11:05:00',
  },
];

const EVENT_META = {
  won_closure:      { icon: 'trophy',        color: '#f59e0b', label: 'WON' },
  to_positive:      { icon: 'arrow-up-circle', color: '#10b981', label: 'TO POSITIVE' },
  to_very_positive: { icon: 'rocket',        color: '#3b82f6', label: 'TO VERY POSITIVE' },
  rp_meeting_close: { icon: 'people-circle', color: '#9B59B6', label: 'RP CAPTURED' },
};

function formatRs(n) {
  if (!n) return '';
  if (n >= 100000) return 'Rs ' + (n / 100000).toFixed(1) + ' lakh';
  if (n >= 1000)   return 'Rs ' + Math.round(n / 1000) + 'k';
  return 'Rs ' + n;
}

function formatTime(iso) {
  if (!iso) return '';
  const t = iso.slice(11, 16);
  return t;
}

export default function ApplauseBanner({ rows = DEMO_TODAY }) {
  // BD only. CM has a different cluster-wide applause feed elsewhere.
  if (CURRENT_USER.role !== 'BD') return null;
  if (!rows || rows.length === 0) return null;

  const top = rows[0];
  const meta = EVENT_META[top.event_type] || EVENT_META.to_positive;
  const isWon = top.event_type === 'won_closure';

  return (
    <View style={s.wrap}>
      <LinearGradient
        colors={isWon ? ['#f59e0b', '#ef4444'] : [meta.color, meta.color + 'cc']}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
        style={s.heroBanner}
      >
        <View style={s.heroIcon}>
          <Ionicons name={meta.icon} size={26} color="#fff" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={s.heroKicker}>{meta.label} {isWon ? '. NICE WORK' : ''}</Text>
          <Text style={s.heroTitle}>
            {isWon ? 'You closed' : 'You moved'} {top.school_name}
            {top.closed_value_rs ? ' . ' + formatRs(top.closed_value_rs) : ''}
          </Text>
          <Text style={s.heroTime}>{formatTime(top.created_at)} today</Text>
        </View>
      </LinearGradient>

      {rows.length > 1 && (
        <View style={s.feed}>
          <Text style={s.feedLabel}>EARLIER TODAY</Text>
          {rows.slice(1, 3).map((row) => {
            const m = EVENT_META[row.event_type] || EVENT_META.to_positive;
            return (
              <View key={row.id} style={s.feedRow}>
                <Ionicons name={m.icon} size={14} color={m.color} />
                <Text style={s.feedText} numberOfLines={1}>{row.message}</Text>
                <Text style={s.feedTime}>{formatTime(row.created_at)}</Text>
              </View>
            );
          })}
        </View>
      )}
    </View>
  );
}

const s = StyleSheet.create({
  wrap: { marginHorizontal: 16, marginTop: 14 },
  heroBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 14,
    padding: 14,
    borderRadius: 16,
    shadowColor: '#000',
    shadowOpacity: 0.12,
    shadowRadius: 12,
    shadowOffset: { width: 0, height: 6 },
    elevation: 4,
  },
  heroIcon: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(255,255,255,0.18)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  heroKicker: {
    color: 'rgba(255,255,255,0.85)',
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 1.2,
  },
  heroTitle: { color: '#fff', fontWeight: '800', fontSize: 15, marginTop: 2 },
  heroTime: { color: 'rgba(255,255,255,0.75)', fontSize: 11, marginTop: 3 },
  feed: {
    backgroundColor: colors.card,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: colors.border,
    padding: 10,
    marginTop: 8,
  },
  feedLabel: {
    color: colors.textMuted,
    fontSize: 9,
    fontWeight: '800',
    letterSpacing: 0.8,
    marginBottom: 6,
  },
  feedRow: { flexDirection: 'row', alignItems: 'center', gap: 8, paddingVertical: 4 },
  feedText: { flex: 1, color: colors.text, fontSize: 12 },
  feedTime: { color: colors.textMuted, fontSize: 10 },
});
