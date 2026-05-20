// AgentsHubScreen — the home screen for the agent-first app.
// Gradient header, KPI tiles, Anaya morning briefing teaser, 6 agent cards,
// and a small "Connected to stemapp.in" trust banner at the bottom.

import React from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { agents } from '../data/agents';
import { kpis } from '../data/mock';

const ICON_MAP = {
  sparkles: 'sparkles',
  mic: 'mic',
  compass: 'compass',
  flame: 'flame',
  wand: 'color-wand',
  trophy: 'trophy',
};

export default function AgentsHubScreen({ navigation }) {
  const anaya = agents.find((a) => a.id === 'anaya');

  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" />
      <ScrollView
        contentContainerStyle={{ paddingBottom: 32 }}
        showsVerticalScrollIndicator={false}
      >
        {/* Header */}
        <LinearGradient
          colors={[colors.spaceTop, colors.spaceBottom]}
          start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
          style={styles.header}
        >
          <View style={styles.headerTop}>
            <View>
              <Text style={styles.helloMuted}>Good morning</Text>
              <Text style={styles.helloName}>Priya Menon</Text>
              <Text style={styles.helloRole}>BD · Mumbai cluster · 5-day streak</Text>
            </View>
            <Pressable style={styles.avatar}>
              <Text style={styles.avatarText}>PM</Text>
            </Pressable>
          </View>

          {/* KPI row */}
          <View style={styles.kpiRow}>
            {kpis.map((k) => (
              <View key={k.id} style={styles.kpiTile}>
                <Text style={styles.kpiValue}>{k.value}</Text>
                <Text style={styles.kpiLabel}>{k.label}</Text>
              </View>
            ))}
          </View>
        </LinearGradient>

        {/* Anaya teaser */}
        <Pressable
          onPress={() => navigation.navigate('AgentChat', { agentId: 'anaya' })}
          style={({ pressed }) => [styles.teaserWrap, pressed && { opacity: 0.9 }]}
        >
          <LinearGradient
            colors={anaya.gradient}
            start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
            style={styles.teaser}
          >
            <View style={styles.teaserIconWrap}>
              <Ionicons name="sparkles" size={22} color="#fff" />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.teaserKicker}>Anaya · your day-start</Text>
              <Text style={styles.teaserTitle}>3 things to focus on today</Text>
              <Text style={styles.teaserBody}>
                Tata Steel proposal is day 4 — call Mr. Bhandari before Friday.
                2 site visits in Pune. MoM still pending for L-1041.
              </Text>
              <View style={styles.teaserCta}>
                <Text style={styles.teaserCtaText}>Open briefing</Text>
                <Ionicons name="arrow-forward" size={14} color="#fff" />
              </View>
            </View>
          </LinearGradient>
        </Pressable>

        {/* Discipline score quick-card */}
        <Pressable
          onPress={() => navigation.navigate('DisciplineScore')}
          style={{ marginHorizontal: 16, marginTop: 12, padding: 14, borderRadius: 12, backgroundColor: '#1f9d55', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View style={{ flex: 1 }}>
            <Text style={{ color: '#fff', fontWeight: '700', fontSize: 15 }}>My discipline score</Text>
            <Text style={{ color: '#d4edda', fontSize: 12, marginTop: 2 }}>Plan-on-time, tasks-on-time, advances clean</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#fff" />
        </Pressable>

        {/* End-of-day expense submission quick-card */}
        <Pressable
          onPress={() => navigation.navigate('ExpenseSubmission')}
          style={{ marginHorizontal: 16, marginTop: 10, padding: 14, borderRadius: 12, backgroundColor: '#f6a623', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View style={{ flex: 1 }}>
            <Text style={{ color: '#fff', fontWeight: '700', fontSize: 15 }}>Submit today actuals</Text>
            <Text style={{ color: '#fff5e0', fontSize: 12, marginTop: 2 }}>Before 18:30 or tomorrow plan stays blocked</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#fff" />
        </Pressable>

        {/* Advance management quick-card */}
        <Pressable
          onPress={() => navigation.navigate('AdvanceManagement')}
          style={{ marginHorizontal: 16, marginTop: 10, padding: 14, borderRadius: 12, backgroundColor: '#1E90FF', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View style={{ flex: 1 }}>
            <Text style={{ color: '#fff', fontWeight: '700', fontSize: 15 }}>Advance management</Text>
            <Text style={{ color: '#e6f2ff', fontSize: 12, marginTop: 2 }}>Request, approve, consume, return</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#fff" />
        </Pressable>

        {/* Settle advance quick-card (submit actual spend with bills against a disbursed advance) */}
        <Pressable
          onPress={() => navigation.navigate('AdvanceSettlement')}
          style={{ marginHorizontal: 16, marginTop: 10, padding: 14, borderRadius: 12, backgroundColor: '#7b3aa3', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View style={{ flex: 1 }}>
            <Text style={{ color: '#fff', fontWeight: '700', fontSize: 15 }}>Settle advance with bills</Text>
            <Text style={{ color: '#efe5f7', fontSize: 12, marginTop: 2 }}>Actual spend + receipts against disbursed advance</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#fff" />
        </Pressable>

        {/* BD Performance quick-card - opens performance dashboard with Meeting Report tab default */}
        <Pressable
          onPress={() => navigation.navigate('BDPerformance', { tab: 'meeting' })}
          style={{ marginHorizontal: 16, marginTop: 10, padding: 14, borderRadius: 12, backgroundColor: '#0F766E', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View style={{ flex: 1 }}>
            <Text style={{ color: '#fff', fontWeight: '700', fontSize: 15 }}>My performance dashboard</Text>
            <Text style={{ color: '#d0f0ec', fontSize: 12, marginTop: 2 }}>Funnel . Activity . Meeting report . Discipline . Expense</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#fff" />
        </Pressable>

        {/* Meeting Economics quick-card - opens the agent screen directly */}
        <Pressable
          onPress={() => navigation.navigate('MeetingEconomics')}
          style={{ marginHorizontal: 16, marginTop: 10, padding: 14, borderRadius: 12, backgroundColor: '#B45309', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View style={{ flex: 1 }}>
            <Text style={{ color: '#fff', fontWeight: '700', fontSize: 15 }}>Meeting economics</Text>
            <Text style={{ color: '#fde4cc', fontSize: 12, marginTop: 2 }}>Fresh vs RP vs NO RP . photo, GPS, MoM capture</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#fff" />
        </Pressable>

        {/* Section heading */}
        <View style={styles.sectionHead}>
          <Text style={styles.sectionTitle}>Your AI agents</Text>
          <Text style={styles.sectionSub}>6 agents, permissioned to your role</Text>
        </View>

        {/* Agent cards */}
        <View style={styles.cardsWrap}>
          {agents.map((a) => (
            <Pressable
              key={a.id}
              onPress={() => navigation.navigate('AgentChat', { agentId: a.id })}
              style={({ pressed }) => [styles.agentCard, pressed && { opacity: 0.92 }]}
            >
              <LinearGradient
                colors={a.gradient}
                start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
                style={styles.agentIcon}
              >
                <Ionicons name={ICON_MAP[a.icon] || 'sparkles'} size={22} color="#fff" />
              </LinearGradient>
              <View style={{ flex: 1 }}>
                <View style={styles.agentTopRow}>
                  <Text style={styles.agentName}>{a.name}</Text>
                  <Text style={styles.agentRole}>{a.role}</Text>
                </View>
                <Text style={styles.agentDesc} numberOfLines={2}>{a.desc}</Text>
                <View style={styles.agentMetaRow}>
                  <View style={styles.permBadge}>
                    <Ionicons name="lock-closed" size={10} color={colors.textMuted} />
                    <Text style={styles.permText}>{a.permission}</Text>
                  </View>
                  <Text style={styles.capText}>cap {a.cap}</Text>
                </View>
              </View>
              <Ionicons name="chevron-forward" size={18} color={colors.textMuted} />
            </Pressable>
          ))}
        </View>

        {/* Connection banner */}
        <View style={styles.connBanner}>
          <View style={styles.connDot} />
          <View style={{ flex: 1 }}>
            <Text style={styles.connTitle}>Connected to stemapp.in</Text>
            <Text style={styles.connSub}>
              Tools run on your CodeIgniter backend · 19 controllers · 22 AIAgents models
            </Text>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },

  header: {
    paddingTop: Platform.OS === 'ios' ? 58 : 38,
    paddingHorizontal: 20,
    paddingBottom: 22,
    borderBottomLeftRadius: 28,
    borderBottomRightRadius: 28,
  },
  headerTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start' },
  helloMuted: { color: 'rgba(255,255,255,0.6)', fontSize: 13, marginBottom: 2 },
  helloName: { color: '#fff', fontSize: 24, fontWeight: '700', letterSpacing: 0.2 },
  helloRole: { color: 'rgba(255,255,255,0.7)', fontSize: 12, marginTop: 4 },
  avatar: {
    width: 44, height: 44, borderRadius: 22,
    backgroundColor: 'rgba(255,255,255,0.18)',
    alignItems: 'center', justifyContent: 'center',
    borderWidth: 1, borderColor: 'rgba(255,255,255,0.25)',
  },
  avatarText: { color: '#fff', fontWeight: '700', fontSize: 14 },

  kpiRow: { flexDirection: 'row', gap: 8, marginTop: 18 },
  kpiTile: {
    flex: 1,
    backgroundColor: 'rgba(255,255,255,0.08)',
    borderRadius: 12,
    paddingVertical: 10, paddingHorizontal: 8,
    borderWidth: 1, borderColor: 'rgba(255,255,255,0.12)',
  },
  kpiValue: { color: '#fff', fontSize: 18, fontWeight: '700' },
  kpiLabel: { color: 'rgba(255,255,255,0.65)', fontSize: 10, marginTop: 2 },

  teaserWrap: { marginHorizontal: 16, marginTop: -14 },
  teaser: {
    borderRadius: 18, padding: 16,
    flexDirection: 'row', gap: 12,
    shadowColor: '#000', shadowOpacity: 0.18, shadowRadius: 14, shadowOffset: { width: 0, height: 6 },
    elevation: 6,
  },
  teaserIconWrap: {
    width: 38, height: 38, borderRadius: 12,
    backgroundColor: 'rgba(255,255,255,0.22)',
    alignItems: 'center', justifyContent: 'center',
  },
  teaserKicker: { color: 'rgba(255,255,255,0.8)', fontSize: 11, fontWeight: '600', letterSpacing: 0.4, textTransform: 'uppercase' },
  teaserTitle: { color: '#fff', fontSize: 17, fontWeight: '700', marginTop: 2 },
  teaserBody: { color: 'rgba(255,255,255,0.9)', fontSize: 12.5, lineHeight: 17, marginTop: 6 },
  teaserCta: { flexDirection: 'row', alignItems: 'center', gap: 4, marginTop: 10 },
  teaserCtaText: { color: '#fff', fontWeight: '600', fontSize: 12 },

  sectionHead: { paddingHorizontal: 20, marginTop: 22, marginBottom: 10 },
  sectionTitle: { color: colors.text, fontSize: 17, fontWeight: '700' },
  sectionSub: { color: colors.textMuted, fontSize: 12, marginTop: 2 },

  cardsWrap: { paddingHorizontal: 16, gap: 10 },
  agentCard: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: colors.card,
    borderRadius: 16, padding: 14, gap: 12,
    borderWidth: 1, borderColor: colors.border,
  },
  agentIcon: {
    width: 44, height: 44, borderRadius: 13,
    alignItems: 'center', justifyContent: 'center',
  },
  agentTopRow: { flexDirection: 'row', alignItems: 'baseline', gap: 8, flexWrap: 'wrap' },
  agentName: { color: colors.text, fontWeight: '700', fontSize: 15 },
  agentRole: { color: colors.textMuted, fontSize: 11.5, fontWeight: '500' },
  agentDesc: { color: colors.textMuted, fontSize: 12.5, marginTop: 4, lineHeight: 17 },
  agentMetaRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 8, flexWrap: 'wrap' },
  permBadge: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    backgroundColor: colors.cardAlt,
    paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6,
  },
  permText: { color: colors.textMuted, fontSize: 10, fontWeight: '500' },
  capText: { color: colors.textMuted, fontSize: 10.5 },

  connBanner: {
    flexDirection: 'row', alignItems: 'center', gap: 10,
    marginHorizontal: 20, marginTop: 20,
    paddingVertical: 12, paddingHorizontal: 14,
    backgroundColor: colors.card,
    borderRadius: 12, borderWidth: 1, borderColor: colors.border,
  },
  connDot: {
    width: 8, height: 8, borderRadius: 4, backgroundColor: colors.success,
    shadowColor: colors.success, shadowOpacity: 0.6, shadowRadius: 6,
  },
  connTitle: { color: colors.text, fontWeight: '600', fontSize: 12.5 },
  connSub: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
});
