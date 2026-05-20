// NewLeadScreen — Leads are NOT created from a blank form.
// In STEM's sales motion, every init_call row comes from one of two paths:
//
//   1. ACTIVITY RESEARCH  — Anaya / Dump Mining surfaces a school based on
//      signals (referral, dormant revival, RFP scrape, web-inquiry intent).
//      BD reviews the candidate and ACCEPTS → init_call created with provenance.
//
//   2. BARGE-IN MEETING   — BD is about to meet a school that has no init_call.
//      They start a MoM recording (StartMomScreen) which creates a stub lead
//      inline; the AI enriches it from the transcript after the call.
//
// This screen shows path #1: a vetted list of candidates Anaya + Dump Mining
// surfaced, each with the signal that triggered them. The BD picks one to
// accept; the other path is reached from MoM Drafter → "Barge-in meeting".
//
// Backend mapping:
//   - List candidates:  AIAgents/LeadSourcing_model::candidates_for_bd(bd_id)
//   - Accept candidate: AIAgents/LeadSourcing_model::accept(candidate_id, overrides)
//                        → calls Menu_model::insert_init_call internally
//   - Dismiss:          AIAgents/LeadSourcing_model::dismiss(candidate_id, reason)

import React, { useState, useMemo } from 'react';
import { View, Text, ScrollView, Pressable, StyleSheet, Alert } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { RESEARCH_CANDIDATES, PROGRAMS } from '../data/cm';

const AGENT_BADGE = {
  anaya: { label: 'ANAYA',   color: '#A855F7', icon: 'sparkles' },
  dump:  { label: 'DUMP',    color: '#F59E0B', icon: 'flame' },
  warroom: { label: 'WAR ROOM', color: '#6366F1', icon: 'compass' },
};

export default function NewLeadScreen({ navigation }) {
  const [filter, setFilter] = useState('all'); // all | anaya | dump

  const candidates = useMemo(() => {
    if (filter === 'all') return RESEARCH_CANDIDATES;
    return RESEARCH_CANDIDATES.filter(c => c.source_agent === filter);
  }, [filter]);

  const handleAccept = (cand) => {
    Alert.alert(
      'Accept lead?',
      `${cand.school}\n${cand.city}\nDM: ${cand.dm_hint}\nProgram: ${PROGRAMS.find(p => p.id === cand.program_hint)?.label}\n\nThis creates init_call row at status #1 Open with provenance "${cand.source_agent}".`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Accept',
          onPress: () => Alert.alert(
            'Lead created',
            `L-1052 · ${cand.school}\nSource: ${cand.source_agent.toUpperCase()}\nStatus: Open · Phase: Prospecting`,
            [{ text: 'OK', onPress: () => navigation?.goBack?.() }]
          ),
        },
      ]
    );
  };

  const handleDismiss = (cand) => {
    Alert.alert('Dismiss candidate?', `${cand.school} will be sent back to the agent with a "not interested" signal.`, [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Dismiss', style: 'destructive' },
    ]);
  };

  return (
    <View style={{ flex: 1, backgroundColor: colors.cardAlt }}>
      <LinearGradient colors={['#A855F7', '#6366F1']} style={styles.hero}>
        <View style={styles.heroTop}>
          <Pressable onPress={() => navigation?.goBack?.()} style={styles.iconBtn}>
            <Ionicons name="close" size={22} color="#fff" />
          </Pressable>
          <Text style={styles.heroEyebrow}>LEAD SOURCING · CANDIDATES</Text>
          <View style={{ width: 36 }} />
        </View>
        <Text style={styles.heroTitle}>New lead</Text>
        <Text style={styles.heroSub}>
          {RESEARCH_CANDIDATES.length} surfaced by your agents · accept to create init_call
        </Text>

        {/* Origin explainer */}
        <View style={styles.originBox}>
          <View style={styles.originRow}>
            <Ionicons name="sparkles" size={14} color="#fff" />
            <Text style={styles.originText}>From <Text style={styles.originBold}>activity research</Text> (Anaya + Dump Mining)</Text>
          </View>
          <View style={styles.originRow}>
            <Ionicons name="mic" size={14} color="#fff" />
            <Text style={styles.originText}>Or <Text style={styles.originBold}>barge-in a meeting</Text> from MoM Drafter</Text>
          </View>
        </View>
      </LinearGradient>

      {/* Filter chips */}
      <View style={styles.filterRow}>
        {[
          { id: 'all',   label: `All · ${RESEARCH_CANDIDATES.length}` },
          { id: 'anaya', label: `Anaya · ${RESEARCH_CANDIDATES.filter(c => c.source_agent === 'anaya').length}` },
          { id: 'dump',  label: `Dump · ${RESEARCH_CANDIDATES.filter(c => c.source_agent === 'dump').length}` },
        ].map(f => {
          const active = filter === f.id;
          return (
            <Pressable key={f.id} onPress={() => setFilter(f.id)} style={[styles.chip, active && styles.chipActive]}>
              <Text style={[styles.chipText, active && styles.chipTextActive]}>{f.label}</Text>
            </Pressable>
          );
        })}
      </View>

      <ScrollView contentContainerStyle={{ padding: 16, paddingBottom: 100 }}>
        {candidates.map(c => {
          const badge = AGENT_BADGE[c.source_agent];
          const program = PROGRAMS.find(p => p.id === c.program_hint);
          return (
            <View key={c.id} style={styles.card}>
              {/* Header row */}
              <View style={styles.cardHead}>
                <View style={[styles.agentBadge, { backgroundColor: badge.color }]}>
                  <Ionicons name={badge.icon} size={12} color="#fff" />
                  <Text style={styles.agentBadgeText}>{badge.label}</Text>
                </View>
                <Text style={styles.confidence}>{Math.round(c.confidence * 100)}% match</Text>
              </View>

              {/* School */}
              <Text style={styles.school}>{c.school}</Text>
              <Text style={styles.meta}>{c.city} · {c.dm_hint}</Text>

              {/* Signal box */}
              <View style={styles.signalBox}>
                <Ionicons name="radio-outline" size={14} color={badge.color} />
                <Text style={styles.signalText}>{c.signal}</Text>
              </View>

              {/* Program + value */}
              <View style={styles.metaRow}>
                <View style={styles.pill}>
                  <Text style={styles.pillText}>{program?.label}</Text>
                </View>
                <Text style={styles.value}>{c.value_hint}</Text>
                <Text style={styles.surfaced}>{c.surfaced_at}</Text>
              </View>

              {/* Actions */}
              <View style={styles.actions}>
                <Pressable onPress={() => handleDismiss(c)} style={[styles.actionBtn, styles.dismissBtn]}>
                  <Ionicons name="close-outline" size={16} color={colors.textMuted} />
                  <Text style={styles.dismissText}>Dismiss</Text>
                </Pressable>
                <Pressable onPress={() => handleAccept(c)} style={[styles.actionBtn, styles.acceptBtn]}>
                  <Ionicons name="checkmark" size={16} color="#fff" />
                  <Text style={styles.acceptText}>Accept · create init_call</Text>
                </Pressable>
              </View>
            </View>
          );
        })}

        {/* Barge-in CTA at the bottom */}
        <Pressable
          onPress={() => navigation?.navigate?.('StartMom', { bargeIn: true })}
          style={styles.bargeCard}>
          <View style={styles.bargeIcon}>
            <Ionicons name="mic" size={20} color="#fff" />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.bargeTitle}>Barge-in meeting</Text>
            <Text style={styles.bargeSub}>Start a MoM for a school not yet in CRM · AI will draft the lead from the transcript</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color={colors.textMuted} />
        </Pressable>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  hero: { paddingTop: 56, paddingHorizontal: 20, paddingBottom: 20, borderBottomLeftRadius: 24, borderBottomRightRadius: 24 },
  heroTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 12 },
  iconBtn: { width: 36, height: 36, borderRadius: 18, backgroundColor: 'rgba(255,255,255,0.18)', alignItems: 'center', justifyContent: 'center' },
  heroEyebrow: { color: 'rgba(255,255,255,0.85)', fontSize: 11, fontWeight: '700', letterSpacing: 1.2 },
  heroTitle: { color: '#fff', fontSize: 28, fontWeight: '800', marginTop: 4 },
  heroSub: { color: 'rgba(255,255,255,0.82)', fontSize: 13, marginTop: 4 },

  originBox: { marginTop: 14, padding: 12, borderRadius: 12, backgroundColor: 'rgba(255,255,255,0.12)', gap: 6 },
  originRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  originText: { color: 'rgba(255,255,255,0.95)', fontSize: 12 },
  originBold: { fontWeight: '700', color: '#fff' },

  filterRow: { flexDirection: 'row', gap: 8, paddingHorizontal: 16, paddingTop: 14, paddingBottom: 4 },
  chip: { paddingHorizontal: 14, paddingVertical: 8, borderRadius: 999, borderWidth: 1, borderColor: colors.border, backgroundColor: '#fff' },
  chipActive: { backgroundColor: colors.btnFrom, borderColor: colors.btnFrom },
  chipText: { fontSize: 12, fontWeight: '600', color: colors.text },
  chipTextActive: { color: '#fff' },

  card: { backgroundColor: '#fff', borderRadius: 16, padding: 14, marginBottom: 12, borderWidth: 1, borderColor: colors.border },
  cardHead: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  agentBadge: { flexDirection: 'row', alignItems: 'center', gap: 4, paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6 },
  agentBadgeText: { color: '#fff', fontSize: 10, fontWeight: '800', letterSpacing: 0.8 },
  confidence: { fontSize: 11, fontWeight: '700', color: colors.textMuted },

  school: { fontSize: 16, fontWeight: '700', color: colors.text },
  meta: { fontSize: 12, color: colors.textMuted, marginTop: 2 },

  signalBox: { flexDirection: 'row', alignItems: 'flex-start', gap: 8, padding: 10, marginTop: 10, borderRadius: 10, backgroundColor: colors.cardAlt },
  signalText: { flex: 1, fontSize: 12, color: colors.text, lineHeight: 17 },

  metaRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 10 },
  pill: { paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6, backgroundColor: 'rgba(62,33,251,0.08)' },
  pillText: { fontSize: 11, color: colors.btnFrom, fontWeight: '700' },
  value: { fontSize: 12, color: colors.text, fontWeight: '600' },
  surfaced: { marginLeft: 'auto', fontSize: 11, color: colors.textMuted },

  actions: { flexDirection: 'row', gap: 8, marginTop: 12 },
  actionBtn: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6, paddingVertical: 10, borderRadius: 10 },
  dismissBtn: { backgroundColor: colors.cardAlt, borderWidth: 1, borderColor: colors.border },
  dismissText: { fontSize: 13, fontWeight: '600', color: colors.textMuted },
  acceptBtn: { backgroundColor: colors.btnFrom, flex: 1.6 },
  acceptText: { fontSize: 13, fontWeight: '700', color: '#fff' },

  bargeCard: { flexDirection: 'row', alignItems: 'center', gap: 12, padding: 14, marginTop: 8, borderRadius: 14, backgroundColor: '#fff', borderWidth: 1, borderColor: colors.border, borderStyle: 'dashed' },
  bargeIcon: { width: 40, height: 40, borderRadius: 12, backgroundColor: '#EF4444', alignItems: 'center', justifyContent: 'center' },
  bargeTitle: { fontSize: 14, fontWeight: '700', color: colors.text },
  bargeSub: { fontSize: 11, color: colors.textMuted, marginTop: 2, lineHeight: 15 },
});
