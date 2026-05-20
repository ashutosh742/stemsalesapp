// LeadDetailScreen — full progression of a single lead through the 13-status journey.
// Backend: init_call (row) + funnel_transfer_log (status changes) + tblcallevents
// (activities) + mom_data (meeting notes) + samestatustilldate_helper (stuck days).

import React, { useEffect } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar,
} from 'react-native';
import { enableSecureScreen, disableSecureScreen } from '../lib/secureContact';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { LEAD_TIMELINE, STATUSES } from '../data/plans';
import SecureContactCard from '../components/SecureContactCard';

const KIND_META = {
  created: { icon: 'sparkles',          color: '#3E21FB' },
  call:    { icon: 'call',              color: '#3498DB' },
  email:   { icon: 'mail',              color: '#9B59B6' },
  visit:   { icon: 'walk',              color: '#F39C12' },
  mom:     { icon: 'document-text',     color: '#14B8A6' },
  stage:   { icon: 'git-branch',        color: '#1F2937' },
  agent:   { icon: 'flash',             color: '#E52E71' },
};

function fmtDate(iso) {
  const d = new Date(iso);
  const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  return `${d.getDate()} ${months[d.getMonth()]} · ${d.getHours().toString().padStart(2,'0')}:${d.getMinutes().toString().padStart(2,'0')}`;
}

export default function LeadDetailScreen({ navigation }) {
  const lead = LEAD_TIMELINE;

  // Block screenshots + screen recording while contact data is on screen.
  useEffect(() => {
    enableSecureScreen();
    return () => disableSecureScreen();
  }, []);

  return (
    <View style={s.root}>
      <StatusBar barStyle="light-content" />
      <ScrollView contentContainerStyle={{ paddingBottom: 32 }} showsVerticalScrollIndicator={false}>
        <LinearGradient
          colors={[colors.spaceTop, colors.spaceBottom]}
          start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
          style={s.header}
        >
          <View style={s.headerTop}>
            <Pressable onPress={() => navigation && navigation.goBack && navigation.goBack()} style={s.back}>
              <Ionicons name="chevron-back" size={22} color="#fff" />
            </Pressable>
            <Text style={s.kicker}>LEAD · {lead.lead_id}</Text>
            <View style={{ width: 22 }} />
          </View>
          <Text style={s.title}>{lead.school}</Text>
          <Text style={s.subtitle}>{lead.city} · {lead.program} · {lead.value}</Text>

          {/* Decision-maker contact — rendered through SecureContactCard so
              phone/email are masked by default, tap-to-reveal is rate-capped
              + audit-logged, and FLAG_SECURE is on while this view is mounted. */}
          <SecureContactCard
            leadId={lead.lead_id}
            contact={{
              id: lead.decision_maker.contact_id || 0,
              name: lead.decision_maker.name,
              designation: lead.decision_maker.title,
              phone: lead.decision_maker.phone,
              email: lead.decision_maker.email,
              is_decision_maker: true,
            }}
            // Backend tells us whether the current user can see full data for this lead
            scope={lead.contact_scope /* 'own'|'cluster'|'admin'|'ceo'|'denied' */}
            onRequestExport={() => navigation.navigate('ExportRequest', {
              scopeType: 'single_lead',
              scopePayload: { lead_id: lead.lead_id },
            })}
            theme="onDark"
          />
        </LinearGradient>

        {/* 13-status journey bar */}
        <View style={s.stagesCard}>
          <Text style={s.cardLabel}>JOURNEY · 13-STATUS LIFECYCLE</Text>
          <View style={s.stageBar}>
            {STATUSES.map((st) => {
              const active = st.id === lead.current_stage_id;
              const passed = st.id < lead.current_stage_id;
              const color = passed ? colors.success : active ? st.color : '#E5E9F2';
              return (
                <View key={st.id} style={[s.stageNode, { backgroundColor: color }]}>
                  {active && <View style={s.stagePulse} />}
                </View>
              );
            })}
          </View>
          <View style={s.stageRow}>
            <Text style={s.stageNow}>Now at <Text style={{ color: colors.text, fontWeight: '800' }}>{lead.current_stage}</Text></Text>
            <Text style={s.stageDays}>Day {lead.days_in_stage}</Text>
          </View>
          {lead.stuck_flag && (
            <View style={s.stuckBanner}>
              <Ionicons name="warning" size={14} color={colors.warning} />
              <Text style={s.stuckText}>{lead.stuck_flag}</Text>
            </View>
          )}
        </View>

        {/* Suggested next moves */}
        <Text style={s.sectionTitle}>Suggested next stage</Text>
        <View style={s.nextRow}>
          {lead.next_stages.map((n, i) => {
            const isWin = n === 'Closure' || n.startsWith('Positive') || n === 'Very Positive';
            const isLoss = n === 'Not Interested';
            return (
              <Pressable key={i} style={[
                s.nextChip,
                isWin && { borderColor: colors.success, backgroundColor: '#ECFDF5' },
                isLoss && { borderColor: colors.danger, backgroundColor: '#FEF2F2' },
              ]}>
                <Text style={[
                  s.nextChipText,
                  isWin && { color: colors.success },
                  isLoss && { color: colors.danger },
                ]}>{n}</Text>
              </Pressable>
            );
          })}
        </View>

        {/* Timeline */}
        <Text style={s.sectionTitle}>Progression timeline</Text>
        <View style={s.tl}>
          {[...lead.events].reverse().map((e, i, arr) => {
            const meta = KIND_META[e.kind] || KIND_META.created;
            const last = i === arr.length - 1;
            return (
              <View key={i} style={s.tlRow}>
                <View style={s.tlGutter}>
                  <View style={[s.tlIcon, { backgroundColor: meta.color }]}>
                    <Ionicons name={meta.icon} size={11} color="#fff" />
                  </View>
                  {!last && <View style={s.tlLine} />}
                </View>
                <View style={s.tlCard}>
                  <View style={s.tlTopRow}>
                    <Text style={s.tlKind}>{e.kind.toUpperCase()}</Text>
                    <Text style={s.tlDate}>{fmtDate(e.at)}</Text>
                  </View>
                  <Text style={s.tlText}>{e.text}</Text>
                  {e.from && e.to && (
                    <View style={s.tlStageMove}>
                      <Text style={s.tlStageMoveText}>{e.from}</Text>
                      <Ionicons name="arrow-forward" size={11} color={colors.textMuted} />
                      <Text style={[s.tlStageMoveText, { color: colors.text, fontWeight: '700' }]}>{e.to}</Text>
                    </View>
                  )}
                  <Text style={s.tlBy}>by {e.by}</Text>
                </View>
              </View>
            );
          })}
        </View>

        <Text style={s.footnote}>
          Status changes logged to funnel_transfer_log · activities to tblcallevents · MoMs to mom_data
        </Text>
      </ScrollView>
    </View>
  );
}

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: { paddingTop: 54, paddingHorizontal: 18, paddingBottom: 22, borderBottomLeftRadius: 24, borderBottomRightRadius: 24 },
  headerTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 8 },
  back: { width: 32, height: 32, borderRadius: 16, alignItems: 'center', justifyContent: 'center', backgroundColor: 'rgba(255,255,255,0.12)' },
  kicker: { color: 'rgba(255,255,255,0.75)', fontSize: 11, letterSpacing: 1.4, fontWeight: '700' },
  title: { color: '#fff', fontSize: 22, fontWeight: '800', marginTop: 4 },
  subtitle: { color: 'rgba(255,255,255,0.78)', fontSize: 12, marginTop: 4 },
  dmCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: 'rgba(255,255,255,0.08)', borderRadius: 12, padding: 10, marginTop: 16, gap: 10 },
  dmAvatar: { width: 38, height: 38, borderRadius: 19, backgroundColor: '#E52E71', alignItems: 'center', justifyContent: 'center' },
  dmAvatarText: { color: '#fff', fontWeight: '800', fontSize: 12 },
  dmName: { color: '#fff', fontWeight: '700', fontSize: 13 },
  dmTitle: { color: 'rgba(255,255,255,0.7)', fontSize: 11, marginTop: 1 },
  dmBtn: { width: 32, height: 32, borderRadius: 16, backgroundColor: colors.btnFrom, alignItems: 'center', justifyContent: 'center' },
  stagesCard: { backgroundColor: colors.card, marginHorizontal: 16, marginTop: -12, padding: 14, borderRadius: 14, borderWidth: 1, borderColor: colors.border, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 8, elevation: 2 },
  cardLabel: { color: colors.textMuted, fontSize: 10, fontWeight: '800', letterSpacing: 1 },
  stageBar: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 12, gap: 3 },
  stageNode: { flex: 1, height: 8, borderRadius: 4, position: 'relative' },
  stagePulse: { position: 'absolute', top: -3, left: -2, right: -2, bottom: -3, borderRadius: 6, borderWidth: 2, borderColor: '#C9A227' },
  stageRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 12 },
  stageNow: { color: colors.textMuted, fontSize: 12 },
  stageDays: { color: colors.warning, fontWeight: '800', fontSize: 12 },
  stuckBanner: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 10, padding: 9, borderRadius: 9, backgroundColor: '#FFFBEB', borderWidth: 1, borderColor: '#F59E0B33' },
  stuckText: { color: '#92400E', fontSize: 11, flex: 1, lineHeight: 15 },
  sectionTitle: { marginTop: 16, marginHorizontal: 16, marginBottom: 8, fontSize: 12, fontWeight: '800', color: colors.textMuted, letterSpacing: 1 },
  nextRow: { flexDirection: 'row', gap: 8, paddingHorizontal: 16, flexWrap: 'wrap' },
  nextChip: { paddingHorizontal: 12, paddingVertical: 7, borderRadius: 999, borderWidth: 1, borderColor: colors.border, backgroundColor: colors.card },
  nextChipText: { color: colors.text, fontWeight: '700', fontSize: 12 },
  tl: { paddingHorizontal: 16 },
  tlRow: { flexDirection: 'row', minHeight: 56 },
  tlGutter: { width: 28, alignItems: 'center', paddingTop: 4 },
  tlIcon: { width: 22, height: 22, borderRadius: 11, alignItems: 'center', justifyContent: 'center' },
  tlLine: { flex: 1, width: 2, backgroundColor: colors.border, marginTop: 2 },
  tlCard: { flex: 1, backgroundColor: colors.card, borderRadius: 10, borderWidth: 1, borderColor: colors.border, padding: 10, marginLeft: 6, marginBottom: 8 },
  tlTopRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  tlKind: { color: colors.textMuted, fontSize: 9, fontWeight: '800', letterSpacing: 0.6 },
  tlDate: { color: colors.textMuted, fontSize: 10 },
  tlText: { color: colors.text, fontSize: 12, marginTop: 5, lineHeight: 17 },
  tlStageMove: { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 6, padding: 6, backgroundColor: colors.cardAlt, borderRadius: 6 },
  tlStageMoveText: { color: colors.textMuted, fontSize: 11 },
  tlBy: { color: colors.textMuted, fontSize: 10, marginTop: 5, fontStyle: 'italic' },
  footnote: { color: colors.textMuted, fontSize: 10, textAlign: 'center', marginTop: 14, marginHorizontal: 24, lineHeight: 14 },
});
