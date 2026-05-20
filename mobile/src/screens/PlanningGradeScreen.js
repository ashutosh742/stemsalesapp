// PlanningGradeScreen.js
// Migration 013 mobile surface. BD sees their grade, streak, points, payout,
// idle-morning waste warning, and a green/red "Plan Tomorrow" button.
// CM/Director sees the team leaderboard.
//
// Routes used:
//   GET /api/planning/grade/today
//   GET /api/planning/leaderboard
//   GET /api/planning/incentive_ledger
//
// Wire into App.js stack as PlanningGrade. Add a tile on DayPlanScreen that
// pushes to this screen.

import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity, RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { apiGet } from '../api/client';
import { getRole, getUid, getName } from '../session';

const GRADE_COLOR = {
  'A+': '#1f8b3a',
  'A':  '#42a161',
  'B':  '#c9a227',
  'C':  '#d97706',
  'D':  '#c0382b',
};

const APPRECIATION = {
  'A+': 'Excellent! Plan was filed well ahead of cutoff.',
  'A':  'Strong discipline. Plan came in early.',
  'B':  'On time but tight. Aim earlier tomorrow.',
  'C':  'Same-day planning costs the morning. Plan tonight by 18:00 IST.',
  'D':  'No plan on file. Plan tonight to recover.',
};

export default function PlanningGradeScreen() {
  const nav = useNavigation();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [role, setRole] = useState('BD');
  const [grade, setGrade] = useState(null);
  const [leaderboard, setLeaderboard] = useState([]);
  const [ledger, setLedger] = useState(null);

  const isCutoffPassed = () => {
    const now = new Date();
    return now.getHours() >= 18; // 18:00 IST
  };

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const r = await getRole();
      setRole(r);
      const uid = await getUid();

      const g = await apiGet('/api/planning/grade/today');
      setGrade(g);

      if (r === 'CM' || r === 'Director' || r === 'RM') {
        const lb = await apiGet(
          '/api/planning/leaderboard?from=' + getMondayISO() + '&to=' + todayISO()
        );
        setLeaderboard(lb.leaderboard || []);
      }

      const l = await apiGet('/api/planning/incentive_ledger?user_id=' + uid);
      setLedger(l && l.ledger !== null ? l : null);
    } catch (e) {
      console.warn('PlanningGrade load failed', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);

  if (loading) {
    return (
      <View style={styles.loadingWrap}>
        <ActivityIndicator size="large" color="#1f4f8b" />
      </View>
    );
  }

  return (
    <ScrollView
      style={styles.scroll}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      <Text style={styles.header}>Planning Grade</Text>

      {/* GRADE BIG CARD */}
      {grade && grade.grade && (
        <View style={[styles.gradeCard, { borderLeftColor: GRADE_COLOR[grade.grade] || '#999' }]}>
          <View style={styles.gradeRow}>
            <Text style={[styles.gradeLetter, { color: GRADE_COLOR[grade.grade] }]}>
              {grade.grade}
            </Text>
            <View style={styles.gradeMeta}>
              <Text style={styles.gradeMetaLine}>Hours ahead: {grade.hours_ahead}</Text>
              <Text style={styles.gradeMetaLine}>Points today: {grade.points}</Text>
              <Text style={styles.gradeMetaLine}>Engagement: {grade.engagement_minutes} min</Text>
            </View>
          </View>
          <Text style={styles.appreciation}>{APPRECIATION[grade.grade] || ''}</Text>
        </View>
      )}

      {!grade?.grade && (
        <View style={styles.warnCard}>
          <Text style={styles.warnTitle}>No plan on file for today</Text>
          <Text style={styles.warnBody}>
            Tap the button below to plan tomorrow now and earn an A+ grade.
          </Text>
        </View>
      )}

      {/* IDLE MORNING WARNING */}
      {grade && grade.idle_flag === 1 && (
        <View style={styles.alertCard}>
          <Text style={styles.alertTitle}>3-hour-waste warning</Text>
          <Text style={styles.alertBody}>
            {grade.idle_morning_minutes} idle minutes between day start and first task today.
            Plan tonight to start tomorrow strong.
          </Text>
        </View>
      )}

      {/* PLAN TOMORROW BUTTON */}
      <TouchableOpacity
        style={[styles.planBtn, { backgroundColor: isCutoffPassed() ? '#c0382b' : '#1f8b3a' }]}
        onPress={() => nav.navigate('DayPlan', { planForTomorrow: true })}
      >
        <Text style={styles.planBtnText}>
          {isCutoffPassed() ? 'Plan Tomorrow (LATE - past 18:00 IST)' : 'Plan Tomorrow Now'}
        </Text>
        <Text style={styles.planBtnSub}>
          {isCutoffPassed()
            ? 'You will earn at most a C grade. Plan now to recover.'
            : 'Submit before 18:00 IST to lock an A or A+ grade for tomorrow.'}
        </Text>
      </TouchableOpacity>

      {/* WEEK LEDGER */}
      {ledger && (
        <View style={styles.ledgerCard}>
          <Text style={styles.sectionTitle}>This Week</Text>
          <View style={styles.ledgerRow}>
            <LedgerCell label="Points" value={ledger.total_points} />
            <LedgerCell label="A+ days" value={ledger.a_plus_count} />
            <LedgerCell label="Streak" value={ledger.streak_days + ' d'} />
            <LedgerCell label="Payout" value={'Rs ' + (ledger.incentive_payout_rs || 0)} />
          </View>
          {ledger.badge_earned && (
            <Text style={styles.badge}>Badge earned: {ledger.badge_earned}</Text>
          )}
        </View>
      )}

      {/* LEADERBOARD - CM/Director only */}
      {(role === 'CM' || role === 'Director' || role === 'RM') && leaderboard.length > 0 && (
        <View style={styles.lbCard}>
          <Text style={styles.sectionTitle}>Team Leaderboard (this week)</Text>
          {leaderboard.map((bd, idx) => (
            <View key={bd.user_id} style={styles.lbRow}>
              <Text style={styles.lbRank}>{idx + 1}</Text>
              <Text style={styles.lbName}>{bd.bd_name}</Text>
              <Text style={styles.lbPoints}>{bd.total_points} pts</Text>
              <Text style={styles.lbAplus}>{bd.a_plus_count} A+</Text>
            </View>
          ))}
        </View>
      )}

      <Text style={styles.footer}>
        Source: AnayaAgent + PlanningGrade_model. Migration 013.
      </Text>
    </ScrollView>
  );
}

function LedgerCell({ label, value }) {
  return (
    <View style={styles.ledgerCell}>
      <Text style={styles.ledgerValue}>{value}</Text>
      <Text style={styles.ledgerLabel}>{label}</Text>
    </View>
  );
}

function todayISO() {
  return new Date().toISOString().slice(0, 10);
}
function getMondayISO() {
  const d = new Date();
  const day = d.getDay();
  const diff = d.getDate() - day + (day === 0 ? -6 : 1);
  d.setDate(diff);
  return d.toISOString().slice(0, 10);
}

const styles = StyleSheet.create({
  scroll: { flex: 1, backgroundColor: '#f6f7fa' },
  loadingWrap: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { fontSize: 22, fontWeight: '700', padding: 16, color: '#1f2d3d' },
  gradeCard: {
    backgroundColor: '#fff', marginHorizontal: 16, marginBottom: 12,
    padding: 16, borderRadius: 10, borderLeftWidth: 5,
    shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  gradeRow: { flexDirection: 'row', alignItems: 'center' },
  gradeLetter: { fontSize: 56, fontWeight: '800', width: 90 },
  gradeMeta: { flex: 1, paddingLeft: 8 },
  gradeMetaLine: { fontSize: 14, color: '#4a5568', marginVertical: 1 },
  appreciation: { marginTop: 12, color: '#2d3748', fontStyle: 'italic' },
  warnCard: {
    backgroundColor: '#fff5f5', marginHorizontal: 16, marginBottom: 12,
    padding: 16, borderRadius: 10, borderLeftWidth: 5, borderLeftColor: '#c0382b',
  },
  warnTitle: { color: '#c0382b', fontWeight: '700', fontSize: 16 },
  warnBody: { color: '#742a2a', marginTop: 6 },
  alertCard: {
    backgroundColor: '#fff8e1', marginHorizontal: 16, marginBottom: 12,
    padding: 14, borderRadius: 10, borderLeftWidth: 5, borderLeftColor: '#d97706',
  },
  alertTitle: { color: '#92400e', fontWeight: '700' },
  alertBody: { color: '#78350f', marginTop: 4 },
  planBtn: {
    marginHorizontal: 16, marginBottom: 16, padding: 18, borderRadius: 12,
  },
  planBtnText: { color: '#fff', fontWeight: '700', fontSize: 17, textAlign: 'center' },
  planBtnSub: { color: '#fff', textAlign: 'center', marginTop: 4, opacity: 0.95 },
  ledgerCard: {
    backgroundColor: '#fff', marginHorizontal: 16, marginBottom: 12,
    padding: 14, borderRadius: 10,
  },
  sectionTitle: { fontWeight: '700', color: '#1f2d3d', fontSize: 15, marginBottom: 10 },
  ledgerRow: { flexDirection: 'row', justifyContent: 'space-around' },
  ledgerCell: { alignItems: 'center', flex: 1 },
  ledgerValue: { fontSize: 18, fontWeight: '700', color: '#1f4f8b' },
  ledgerLabel: { fontSize: 11, color: '#718096', marginTop: 2 },
  badge: { marginTop: 10, color: '#1f8b3a', fontWeight: '600', textAlign: 'center' },
  lbCard: {
    backgroundColor: '#fff', marginHorizontal: 16, marginBottom: 12,
    padding: 14, borderRadius: 10,
  },
  lbRow: {
    flexDirection: 'row', alignItems: 'center', paddingVertical: 6,
    borderBottomWidth: 1, borderBottomColor: '#edf2f7',
  },
  lbRank: { width: 24, fontWeight: '700', color: '#1f4f8b' },
  lbName: { flex: 1, color: '#2d3748' },
  lbPoints: { width: 70, textAlign: 'right', fontWeight: '600', color: '#1f8b3a' },
  lbAplus: { width: 60, textAlign: 'right', color: '#718096', fontSize: 12 },
  footer: { textAlign: 'center', color: '#a0aec0', fontSize: 11, padding: 16 },
});
