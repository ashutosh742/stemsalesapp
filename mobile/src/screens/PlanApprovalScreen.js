// PlanApprovalScreen — CM view of pending BD plans. Maps to
// daymanagementapprovalrequest queue. CM can approve / reject with comment.

import React, { useState } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, TextInput,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { PENDING_PLANS } from '../data/plans';

function FlagPill({ text }) {
  return (
    <View style={s.flag}>
      <Ionicons name="warning-outline" size={11} color={colors.warning} />
      <Text style={s.flagText}>{text}</Text>
    </View>
  );
}

function PlanCard({ plan, comment, onComment, onApprove, onReject }) {
  return (
    <View style={s.card}>
      <View style={s.cardHead}>
        <View style={s.bdAvatar}><Text style={s.bdAvatarText}>{plan.user.initials}</Text></View>
        <View style={{ flex: 1 }}>
          <Text style={s.bdName}>{plan.user.name}</Text>
          <Text style={s.bdMeta}>{plan.user.role} · submitted {plan.submitted_at.slice(11, 16)}</Text>
        </View>
        <Text style={s.pipeline}>{plan.pipeline_today}</Text>
      </View>

      <View style={s.statRow}>
        <View style={s.stat}><Text style={s.statN}>{plan.task_count}</Text><Text style={s.statL}>tasks</Text></View>
        <View style={s.statDivider} />
        <View style={s.stat}><Text style={s.statN}>{plan.visits}</Text><Text style={s.statL}>visits</Text></View>
        <View style={s.statDivider} />
        <View style={s.stat}><Text style={s.statN}>{plan.calls}</Text><Text style={s.statL}>calls</Text></View>
        <View style={s.statDivider} />
        <View style={s.stat}><Text style={s.statN}>{plan.emails}</Text><Text style={s.statL}>emails</Text></View>
      </View>

      <Text style={s.note}>{plan.note}</Text>

      {plan.flags.length > 0 && (
        <View style={s.flagsWrap}>
          {plan.flags.map((f, i) => <FlagPill key={i} text={f} />)}
        </View>
      )}

      <TextInput
        style={s.commentInput}
        placeholder="Add a comment (optional)…"
        placeholderTextColor={colors.textMuted}
        value={comment}
        onChangeText={onComment}
        multiline
      />

      <View style={s.btnRow}>
        <Pressable style={[s.btn, s.btnReject]} onPress={onReject}>
          <Ionicons name="close" size={16} color={colors.danger} />
          <Text style={[s.btnText, { color: colors.danger }]}>Reject</Text>
        </Pressable>
        <Pressable style={[s.btn, s.btnApprove]} onPress={onApprove}>
          <Ionicons name="checkmark" size={16} color="#fff" />
          <Text style={[s.btnText, { color: '#fff' }]}>Approve</Text>
        </Pressable>
      </View>
    </View>
  );
}

export default function PlanApprovalScreen({ navigation }) {
  const [comments, setComments] = useState({});
  const [decisions, setDecisions] = useState({});

  const pending = PENDING_PLANS.filter(p => !decisions[p.id]);
  const approved = PENDING_PLANS.filter(p => decisions[p.id] === 'approved').length;
  const rejected = PENDING_PLANS.filter(p => decisions[p.id] === 'rejected').length;

  return (
    <View style={s.root}>
      <StatusBar barStyle="light-content" />
      <ScrollView contentContainerStyle={{ paddingBottom: 32 }} showsVerticalScrollIndicator={false}>
        <LinearGradient
          colors={['#5B2C82', '#9B59B6']}
          start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
          style={s.header}
        >
          <View style={s.headerTop}>
            <Pressable onPress={() => navigation && navigation.goBack && navigation.goBack()} style={s.back}>
              <Ionicons name="chevron-back" size={22} color="#fff" />
            </Pressable>
            <Text style={s.kicker}>CM · MUMBAI CLUSTER</Text>
            <View style={{ width: 22 }} />
          </View>
          <Text style={s.title}>Plans to approve</Text>
          <Text style={s.subtitle}>{pending.length} pending · {approved} approved · {rejected} rejected today</Text>

          <View style={s.kpiRow}>
            <View style={s.kpi}><Text style={s.kpiN}>22</Text><Text style={s.kpiL}>BDs in cluster</Text></View>
            <View style={s.kpi}><Text style={s.kpiN}>19</Text><Text style={s.kpiL}>plans in</Text></View>
            <View style={s.kpi}><Text style={[s.kpiN, { color: '#FFD93D' }]}>3</Text><Text style={s.kpiL}>missing</Text></View>
          </View>
        </LinearGradient>

        <View style={s.cmHint}>
          <Ionicons name="bulb-outline" size={14} color={colors.warning} />
          <Text style={s.cmHintText}>
            Cadence agent has pre-scored these plans. Review flags below before approving.
          </Text>
        </View>

        <Pressable
          onPress={() => navigation && navigation.navigate && navigation.navigate('MomApprovalQueue')}
          style={{ marginHorizontal: 16, marginTop: 12, padding: 14, borderRadius: 10, backgroundColor: '#1B2A4E', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View>
            <Text style={{ color: '#FFF', fontWeight: '700' }}>MoM approvals</Text>
            <Text style={{ color: '#B6C2D9', fontSize: 12, marginTop: 2 }}>Voice-drafted MoMs from your BDs, pre-tiered</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#FFF" />
        </Pressable>

        <Pressable
          onPress={() => navigation && navigation.navigate && navigation.navigate('CancellationAdvanceAudit')}
          style={{ marginHorizontal: 16, marginTop: 8, padding: 14, borderRadius: 10, backgroundColor: '#5A1A2E', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
          <View>
            <Text style={{ color: '#FFF', fontWeight: '700' }}>Cancelled meetings + advances</Text>
            <Text style={{ color: '#F5C6CB', fontSize: 12, marginTop: 2 }}>Money trail. Flag unreturned advances.</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#FFF" />
        </Pressable>

        {pending.length === 0 ? (
          <View style={s.empty}>
            <Ionicons name="checkmark-done-circle" size={42} color={colors.success} />
            <Text style={s.emptyTitle}>You're clear</Text>
            <Text style={s.emptyBody}>All pending plans handled. Star agent will route next requests here.</Text>
          </View>
        ) : (
          pending.map(p => (
            <PlanCard
              key={p.id}
              plan={p}
              comment={comments[p.id] || ''}
              onComment={(v) => setComments({ ...comments, [p.id]: v })}
              onApprove={() => setDecisions({ ...decisions, [p.id]: 'approved' })}
              onReject={() => setDecisions({ ...decisions, [p.id]: 'rejected' })}
            />
          ))
        )}

        <Text style={s.footnote}>
          Decisions write to daymanagementapprovalrequest · trigger Anaya re-score for affected BDs
        </Text>
      </ScrollView>
    </View>
  );
}

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: { paddingTop: 54, paddingHorizontal: 18, paddingBottom: 22, borderBottomLeftRadius: 24, borderBottomRightRadius: 24 },
  headerTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 12 },
  back: { width: 32, height: 32, borderRadius: 16, alignItems: 'center', justifyContent: 'center', backgroundColor: 'rgba(255,255,255,0.12)' },
  kicker: { color: 'rgba(255,255,255,0.75)', fontSize: 11, letterSpacing: 1.4, fontWeight: '700' },
  title: { color: '#fff', fontSize: 26, fontWeight: '800', marginTop: 2 },
  subtitle: { color: 'rgba(255,255,255,0.85)', fontSize: 13, marginTop: 4 },
  kpiRow: { flexDirection: 'row', gap: 8, marginTop: 16 },
  kpi: { flex: 1, backgroundColor: 'rgba(255,255,255,0.12)', borderRadius: 10, padding: 10 },
  kpiN: { color: '#fff', fontWeight: '800', fontSize: 18 },
  kpiL: { color: 'rgba(255,255,255,0.75)', fontSize: 10, marginTop: 2 },
  cmHint: { flexDirection: 'row', alignItems: 'center', gap: 8, marginHorizontal: 16, marginTop: 14, padding: 10, borderRadius: 10, backgroundColor: '#FFFBEB', borderWidth: 1, borderColor: '#F59E0B33' },
  cmHintText: { color: '#92400E', fontSize: 11, flex: 1, lineHeight: 15 },
  card: { backgroundColor: colors.card, marginHorizontal: 16, marginTop: 12, padding: 14, borderRadius: 14, borderWidth: 1, borderColor: colors.border },
  cardHead: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  bdAvatar: { width: 38, height: 38, borderRadius: 19, backgroundColor: colors.btnFrom, alignItems: 'center', justifyContent: 'center' },
  bdAvatarText: { color: '#fff', fontWeight: '800', fontSize: 13 },
  bdName: { color: colors.text, fontWeight: '700', fontSize: 14 },
  bdMeta: { color: colors.textMuted, fontSize: 11, marginTop: 1 },
  pipeline: { color: colors.btnFrom, fontWeight: '800', fontSize: 14 },
  statRow: { flexDirection: 'row', alignItems: 'center', marginTop: 12, backgroundColor: colors.cardAlt, borderRadius: 10, paddingVertical: 8 },
  stat: { flex: 1, alignItems: 'center' },
  statN: { color: colors.text, fontWeight: '800', fontSize: 15 },
  statL: { color: colors.textMuted, fontSize: 10, marginTop: 1 },
  statDivider: { width: 1, height: 20, backgroundColor: colors.border },
  note: { color: colors.text, marginTop: 10, fontSize: 12, lineHeight: 17 },
  flagsWrap: { flexDirection: 'row', flexWrap: 'wrap', gap: 6, marginTop: 8 },
  flag: { flexDirection: 'row', alignItems: 'center', gap: 4, backgroundColor: '#FFFBEB', borderColor: '#F59E0B55', borderWidth: 1, paddingHorizontal: 8, paddingVertical: 4, borderRadius: 999 },
  flagText: { color: '#92400E', fontSize: 10, fontWeight: '600' },
  commentInput: { borderWidth: 1, borderColor: colors.border, backgroundColor: colors.cardAlt, borderRadius: 10, padding: 10, marginTop: 10, color: colors.text, fontSize: 12, minHeight: 38 },
  btnRow: { flexDirection: 'row', gap: 10, marginTop: 10 },
  btn: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6, paddingVertical: 11, borderRadius: 10 },
  btnReject: { backgroundColor: '#FEE2E2', borderWidth: 1, borderColor: colors.danger + '55' },
  btnApprove: { backgroundColor: colors.success },
  btnText: { fontWeight: '700', fontSize: 13 },
  empty: { alignItems: 'center', marginTop: 32, paddingHorizontal: 40 },
  emptyTitle: { color: colors.text, fontWeight: '800', fontSize: 16, marginTop: 8 },
  emptyBody: { color: colors.textMuted, fontSize: 12, textAlign: 'center', marginTop: 4, lineHeight: 17 },
  footnote: { color: colors.textMuted, fontSize: 10, textAlign: 'center', marginTop: 18, marginHorizontal: 24, lineHeight: 14 },
});
