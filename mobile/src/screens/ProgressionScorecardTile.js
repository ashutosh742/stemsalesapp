// ProgressionScorecardTile - shows the logged-in BD their own progression score and
// transition counts for yesterday. Mirrors migration 012 bd_progression_daily.
// Endpoint: GET /api/progression/scorecard?uid=me (BD can only see self; CM can pass any uid).
//
// Visibility: BD always sees their own card. CM sees an aggregate strip of their cluster's
// BDs flagged C or D (driven from cron 1e989fa1 standup output).

import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { CURRENT_USER } from '../data/roles';

// Demo payload - in production this is fetched from /api/progression/scorecard
const DEMO_SCORECARD = {
  uid: 42,
  bd_name: 'Priya Menon',
  score: 68,
  grade: 'B',
  transitions: 4,
  to_positive: 1,
  to_won: 0,
  moms_pending_blocking: 0,
  score_date: '2026-05-14',
};

const GRADE_META = {
  'A+': { color: '#10b981', label: 'A plus', tone: 'Crushing it' },
  'A':  { color: '#10b981', label: 'A',      tone: 'Strong week' },
  'B':  { color: '#3b82f6', label: 'B',      tone: 'On track' },
  'C':  { color: '#f59e0b', label: 'C',      tone: 'Needs push' },
  'D':  { color: '#ef4444', label: 'D',      tone: 'Action required' },
};

export default function ProgressionScorecardTile({ data = DEMO_SCORECARD, onPress }) {
  // Only render for BDs and CMs (others see no value in the BD-grade view).
  if (CURRENT_USER.role !== 'BD' && CURRENT_USER.role !== 'CM') return null;

  const meta = GRADE_META[data.grade] || GRADE_META.C;
  const isLow = data.grade === 'C' || data.grade === 'D';
  const hasBlockers = data.moms_pending_blocking > 0;

  return (
    <Pressable onPress={onPress} style={[s.tile, isLow && s.tileLow]}>
      <View style={s.row}>
        <View style={[s.gradeBubble, { backgroundColor: meta.color + '22', borderColor: meta.color }]}>
          <Text style={[s.gradeText, { color: meta.color }]}>{meta.label}</Text>
        </View>
        <View style={{ flex: 1 }}>
          <Text style={s.title}>Your progression score</Text>
          <Text style={s.subtitle}>
            {data.score} of 100 . {meta.tone}
          </Text>
        </View>
        <Ionicons name="chevron-forward" size={18} color={colors.textMuted} />
      </View>

      <View style={s.statsRow}>
        <Stat label="Transitions" value={data.transitions} />
        <Stat label="To Positive" value={data.to_positive} color={data.to_positive > 0 ? '#10b981' : null} />
        <Stat label="To Won" value={data.to_won} color={data.to_won > 0 ? '#10b981' : null} />
        <Stat
          label="MoMs blocking"
          value={data.moms_pending_blocking}
          color={hasBlockers ? '#ef4444' : null}
        />
      </View>

      {isLow && (
        <View style={s.banner}>
          <Ionicons name="alert-circle" size={13} color="#ef4444" />
          <Text style={s.bannerText}>
            Grade {meta.label}. Focus on moving Reachout leads to Tentative this week.
          </Text>
        </View>
      )}

      {hasBlockers && (
        <View style={s.banner}>
          <Ionicons name="document-text-outline" size={13} color="#f59e0b" />
          <Text style={s.bannerText}>
            {data.moms_pending_blocking} MoM pending CM approval. Cstatus jumps blocked until cleared.
          </Text>
        </View>
      )}

      <Text style={s.footnote}>
        Score for {data.score_date} . Source: bd_progression_daily
      </Text>
    </Pressable>
  );
}

function Stat({ label, value, color }) {
  return (
    <View style={s.stat}>
      <Text style={[s.statVal, color && { color }]}>{value}</Text>
      <Text style={s.statLabel}>{label}</Text>
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
  tileLow: { borderColor: '#ef4444', borderWidth: 1.5 },
  row: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  gradeBubble: {
    width: 48,
    height: 48,
    borderRadius: 24,
    borderWidth: 1.5,
    alignItems: 'center',
    justifyContent: 'center',
  },
  gradeText: { fontSize: 16, fontWeight: '800' },
  title: { color: colors.text, fontWeight: '700', fontSize: 14 },
  subtitle: { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  statsRow: {
    flexDirection: 'row',
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: colors.border,
    justifyContent: 'space-between',
  },
  stat: { alignItems: 'center', flex: 1 },
  statVal: { color: colors.text, fontWeight: '800', fontSize: 18 },
  statLabel: { color: colors.textMuted, fontSize: 10, marginTop: 2, letterSpacing: 0.4 },
  banner: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginTop: 10,
    paddingVertical: 8,
    paddingHorizontal: 10,
    backgroundColor: colors.cardAlt,
    borderRadius: 8,
  },
  bannerText: { color: colors.text, fontSize: 11, flex: 1, lineHeight: 15 },
  footnote: { color: colors.textMuted, fontSize: 10, marginTop: 10, textAlign: 'right' },
});
