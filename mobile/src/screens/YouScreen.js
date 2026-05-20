// YouScreen — profile, discipline streak, settings, logout.

import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform, Alert,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { clearSession } from '../api/client';
import { ROLES } from '../data/roles';
import { getRole, setRole, subscribe } from '../state/session';

const PROFILE = {
  name: 'Priya Menon',
  role: 'BD Executive',
  cluster: 'Mumbai',
  email: 'priya.menon@stemlearning.in',
  streak: 5,
  momsThisWeek: 9,
  visitsThisWeek: 5,
  pipeline: '₹12.4L',
};

const STREAK_DAYS = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
const STREAK_STATE = [true, true, true, true, true, false, false]; // 5 days done, Sat/Sun pending

const ROLE_ORDER = ['BD', 'CM', 'RM', 'FOUNDER'];

function RoleSwitcher() {
  const [current, setCurrent] = useState(getRole());
  useEffect(() => subscribe(setCurrent), []);
  return (
    <View style={styles.roleCard}>
      <View style={styles.roleHead}>
        <Ionicons name="swap-horizontal" size={14} color={colors.btnFrom} />
        <Text style={styles.roleTitle}>Demo mode · switch perspective</Text>
      </View>
      <Text style={styles.roleSub}>
        Toggle to see what this BD's CM, RM, or founder sees in real time.
      </Text>
      <View style={styles.roleRow}>
        {ROLE_ORDER.map(r => {
          const cfg = ROLES[r];
          const active = r === current;
          return (
            <Pressable
              key={r}
              onPress={() => setRole(r)}
              style={[styles.rolePill, active && { backgroundColor: cfg.color, borderColor: cfg.color }]}
            >
              <Text style={[styles.rolePillText, active && { color: '#fff' }]}>{cfg.short}</Text>
            </Pressable>
          );
        })}
      </View>
      <Text style={styles.roleHint}>
        Now viewing as <Text style={{ color: colors.text, fontWeight: '700' }}>{ROLES[current].label}</Text> · tabs update below.
      </Text>
    </View>
  );
}

export default function YouScreen() {
  async function handleLogout() {
    Alert.alert('Sign out?', 'You will need to sign in again.', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Sign out', style: 'destructive', onPress: async () => { try { await clearSession(); } catch {} } },
    ]);
  }

  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" />
      <ScrollView contentContainerStyle={{ paddingBottom: 40 }} showsVerticalScrollIndicator={false}>
        {/* Header */}
        <LinearGradient
          colors={[colors.spaceTop, colors.spaceBottom]}
          start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
          style={styles.header}
        >
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>PM</Text>
          </View>
          <Text style={styles.name}>{PROFILE.name}</Text>
          <Text style={styles.role}>{PROFILE.role} · {PROFILE.cluster} cluster</Text>
          <Text style={styles.email}>{PROFILE.email}</Text>
        </LinearGradient>

        {/* Demo role switcher */}
        <RoleSwitcher />

        {/* Streak card */}
        <View style={styles.streakCard}>
          <View style={styles.streakHead}>
            <View>
              <Text style={styles.streakKicker}>Day-start streak</Text>
              <View style={styles.streakRow}>
                <Text style={styles.streakNum}>{PROFILE.streak}</Text>
                <Text style={styles.streakUnit}>days</Text>
                <Ionicons name="flame" size={22} color={colors.warning} style={{ marginLeft: 4 }} />
              </View>
            </View>
            <View style={styles.streakBadge}>
              <Ionicons name="trophy" size={12} color={colors.warning} />
              <Text style={styles.streakBadgeText}>On fire</Text>
            </View>
          </View>
          <View style={styles.weekRow}>
            {STREAK_DAYS.map((d, i) => (
              <View key={i} style={[styles.dayPill, STREAK_STATE[i] && styles.dayPillActive]}>
                <Text style={[styles.dayText, STREAK_STATE[i] && styles.dayTextActive]}>{d}</Text>
              </View>
            ))}
          </View>
        </View>

        {/* This week stats */}
        <View style={styles.statsGrid}>
          <Stat label="Pipeline"      value={PROFILE.pipeline} icon="trending-up" color={colors.btnFrom} />
          <Stat label="MoMs filed"    value={PROFILE.momsThisWeek} icon="document-text" color={colors.brandPurple} />
          <Stat label="Site visits"   value={PROFILE.visitsThisWeek} icon="walk" color={colors.warning} />
          <Stat label="Plan today"    value="✓" icon="checkmark-circle" color={colors.success} />
        </View>

        {/* Settings */}
        <View style={styles.section}>
          <Text style={styles.sectionLabel}>Account</Text>
          <Row icon="person" label="Edit profile" />
          <Row icon="notifications" label="Notifications" subtitle="Push · Email · In-app" />
          <Row icon="lock-closed" label="Privacy & permissions" />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionLabel}>Agents</Text>
          <Row icon="cash" label="Spend caps" subtitle="₹3/day Anaya · ₹2/MoM · ₹100/day War Room" />
          <Row icon="construct" label="Tool permissions" subtitle="READ_OWN + WRITE_OWN" />
          <Row icon="link" label="Connected to stemapp.in" subtitle="Session active · v2.0.0" rightDot />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionLabel}>Help</Text>
          <Row icon="help-circle" label="How agents work" />
          <Row icon="bug" label="Report an issue" />
          <Row icon="information-circle" label="About" subtitle="STEM CRMApp · 2.0.0" />
        </View>

        <Pressable onPress={handleLogout} style={styles.logoutBtn}>
          <Ionicons name="log-out" size={18} color={colors.danger} />
          <Text style={styles.logoutText}>Sign out</Text>
        </Pressable>
      </ScrollView>
    </View>
  );
}

function Stat({ label, value, icon, color }) {
  return (
    <View style={styles.statCell}>
      <View style={[styles.statIcon, { backgroundColor: color + '22' }]}>
        <Ionicons name={icon} size={16} color={color} />
      </View>
      <Text style={styles.statValue}>{value}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );
}

function Row({ icon, label, subtitle, rightDot }) {
  return (
    <Pressable style={styles.row}>
      <View style={styles.rowIcon}>
        <Ionicons name={icon} size={18} color={colors.textMuted} />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={styles.rowLabel}>{label}</Text>
        {subtitle && <Text style={styles.rowSubtitle}>{subtitle}</Text>}
      </View>
      {rightDot && <View style={styles.connDot} />}
      <Ionicons name="chevron-forward" size={16} color={colors.textMuted} />
    </Pressable>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },

  header: {
    paddingTop: Platform.OS === 'ios' ? 58 : 38,
    paddingBottom: 28, alignItems: 'center',
    borderBottomLeftRadius: 24, borderBottomRightRadius: 24,
  },
  avatar: {
    width: 76, height: 76, borderRadius: 38,
    backgroundColor: 'rgba(255,255,255,0.18)',
    alignItems: 'center', justifyContent: 'center',
    borderWidth: 2, borderColor: 'rgba(255,255,255,0.3)',
  },
  avatarText: { color: '#fff', fontWeight: '700', fontSize: 24 },
  name: { color: '#fff', fontSize: 20, fontWeight: '700', marginTop: 10 },
  role: { color: 'rgba(255,255,255,0.8)', fontSize: 12.5, marginTop: 3 },
  email: { color: 'rgba(255,255,255,0.6)', fontSize: 11.5, marginTop: 2 },

  streakCard: {
    marginHorizontal: 16, marginTop: 0,
    backgroundColor: colors.card, padding: 16, borderRadius: 14,
    borderWidth: 1, borderColor: colors.border,
    shadowColor: '#000', shadowOpacity: 0.08, shadowRadius: 10, shadowOffset: { width: 0, height: 4 }, elevation: 4,
  },
  streakHead: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start' },
  streakKicker: { color: colors.textMuted, fontSize: 11, fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.5 },
  streakRow: { flexDirection: 'row', alignItems: 'baseline', gap: 4, marginTop: 4 },
  streakNum: { color: colors.text, fontSize: 32, fontWeight: '800' },
  streakUnit: { color: colors.textMuted, fontSize: 14 },
  streakBadge: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    backgroundColor: '#FEF3C7', paddingHorizontal: 10, paddingVertical: 5, borderRadius: 8,
  },
  streakBadgeText: { color: '#92400E', fontSize: 11, fontWeight: '700' },
  weekRow: { flexDirection: 'row', gap: 6, marginTop: 14 },
  dayPill: {
    flex: 1, aspectRatio: 1, borderRadius: 8,
    alignItems: 'center', justifyContent: 'center',
    backgroundColor: colors.cardAlt,
  },
  dayPillActive: { backgroundColor: colors.warning },
  dayText: { color: colors.textMuted, fontSize: 11, fontWeight: '700' },
  dayTextActive: { color: '#fff' },

  statsGrid: {
    flexDirection: 'row', flexWrap: 'wrap', gap: 8,
    paddingHorizontal: 16, marginTop: 14,
  },
  statCell: {
    width: '48.5%', backgroundColor: colors.card,
    padding: 14, borderRadius: 12,
    borderWidth: 1, borderColor: colors.border,
  },
  statIcon: { width: 30, height: 30, borderRadius: 9, alignItems: 'center', justifyContent: 'center', marginBottom: 8 },
  statValue: { color: colors.text, fontSize: 18, fontWeight: '700' },
  statLabel: { color: colors.textMuted, fontSize: 11.5, marginTop: 2 },

  section: { marginTop: 20 },
  sectionLabel: {
    color: colors.textMuted, fontSize: 11, fontWeight: '700',
    textTransform: 'uppercase', letterSpacing: 0.5,
    paddingHorizontal: 20, paddingBottom: 8,
  },
  row: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    backgroundColor: colors.card,
    paddingHorizontal: 16, paddingVertical: 12,
    borderTopWidth: 1, borderColor: colors.border,
  },
  rowIcon: {
    width: 32, height: 32, borderRadius: 9,
    backgroundColor: colors.cardAlt,
    alignItems: 'center', justifyContent: 'center',
  },
  rowLabel: { color: colors.text, fontSize: 13.5, fontWeight: '600' },
  rowSubtitle: { color: colors.textMuted, fontSize: 11.5, marginTop: 2 },
  connDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: colors.success, marginRight: 6 },

  logoutBtn: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
    marginHorizontal: 16, marginTop: 24, paddingVertical: 14,
    backgroundColor: colors.card, borderRadius: 12,
    borderWidth: 1, borderColor: '#FEE2E2',
  },
  logoutText: { color: colors.danger, fontWeight: '700', fontSize: 14 },

  roleCard: {
    marginHorizontal: 16, marginTop: -16,
    backgroundColor: colors.card, padding: 14, borderRadius: 14,
    borderWidth: 1, borderColor: colors.btnFrom + '33',
    shadowColor: '#000', shadowOpacity: 0.06, shadowRadius: 8, shadowOffset: { width: 0, height: 4 }, elevation: 3,
    marginBottom: 12,
  },
  roleHead: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  roleTitle: { color: colors.text, fontWeight: '800', fontSize: 12, letterSpacing: 0.3 },
  roleSub: { color: colors.textMuted, fontSize: 11, marginTop: 4, lineHeight: 15 },
  roleRow: { flexDirection: 'row', gap: 6, marginTop: 10 },
  rolePill: { flex: 1, paddingVertical: 8, borderRadius: 9, alignItems: 'center', borderWidth: 1, borderColor: colors.border, backgroundColor: colors.cardAlt },
  rolePillText: { color: colors.text, fontWeight: '700', fontSize: 12 },
  roleHint: { color: colors.textMuted, fontSize: 10, marginTop: 8 },
});
