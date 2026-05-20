// MomDrafterScreen — record a meeting, transcribe → AI drafts MoM → review.
// States: idle → recording → processing → draft.
// Real expo-av Audio.Recording is used; when USE_MOCKS=true the transcript and
// MoM are synthesized locally so the screen demos end-to-end without a backend.

import React, { useEffect, useRef, useState } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform,
  Animated, Easing, Alert,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { Audio } from 'expo-av';

import { colors } from '../theme/colors';
import { agents, TOOL_ENDPOINTS } from '../data/agents';
import { USE_MOCKS, tools } from '../api/client';

const STATES = { IDLE: 'idle', RECORDING: 'recording', PROCESSING: 'processing', DRAFT: 'draft' };

const MOCK_DRAFT = {
  summary:
    "Met Mr. Bhandari (GM-CSR) and the principal at Kendriya Vidyalaya Andheri for a 45-minute discussion on the Mini Science Centre proposal. Budget approval is in-principle; final sign-off blocked on a clarification about installation timeline during summer break.",
  facts: [
    { k: 'Lead', v: 'L-1042 · KV Andheri' },
    { k: 'Program', v: 'Mini Science Centre' },
    { k: 'Value', v: '₹4.2L' },
    { k: 'Meeting type', v: 'Decision-maker call' },
    { k: 'Sentiment', v: 'Positive / cautious' },
    { k: 'Duration', v: '45 min' },
  ],
  nextAction: {
    text: 'Share revised installation timeline (Jun 5–25 window) and propose a 10 AM call on Friday with Mr. Bhandari.',
    when: 'Tomorrow · 10:00 AM',
    type: 'schedule_followup',
  },
  quality: 0.86,
  warnings: [
    'No competitor mentioned — confirm if other vendors are being considered.',
  ],
};

export default function MomDrafterScreen({ route, navigation }) {
  const agent = agents.find((a) => a.id === 'mom');
  const leadId = route?.params?.leadId || 'L-1042';

  const [state, setState] = useState(STATES.IDLE);
  const [recording, setRecording] = useState(null);
  const [seconds, setSeconds] = useState(0);
  const [draft, setDraft] = useState(null);
  const pulse = useRef(new Animated.Value(1)).current;
  const timerRef = useRef(null);

  // Pulse animation while recording
  useEffect(() => {
    if (state === STATES.RECORDING) {
      Animated.loop(
        Animated.sequence([
          Animated.timing(pulse, { toValue: 1.25, duration: 700, easing: Easing.out(Easing.quad), useNativeDriver: true }),
          Animated.timing(pulse, { toValue: 1.0, duration: 700, easing: Easing.in(Easing.quad), useNativeDriver: true }),
        ])
      ).start();
      timerRef.current = setInterval(() => setSeconds((s) => s + 1), 1000);
    } else {
      pulse.stopAnimation();
      pulse.setValue(1);
      if (timerRef.current) clearInterval(timerRef.current);
    }
    return () => { if (timerRef.current) clearInterval(timerRef.current); };
  }, [state]);

  async function startRecording() {
    try {
      const perm = await Audio.requestPermissionsAsync();
      if (!perm.granted) {
        Alert.alert('Microphone needed', 'Please allow microphone access to record this meeting.');
        return;
      }
      await Audio.setAudioModeAsync({ allowsRecordingIOS: true, playsInSilentModeIOS: true });
      const { recording: rec } = await Audio.Recording.createAsync(Audio.RecordingOptionsPresets.HIGH_QUALITY);
      setRecording(rec);
      setSeconds(0);
      setState(STATES.RECORDING);
    } catch (e) {
      Alert.alert('Recording failed', String(e?.message || e));
    }
  }

  async function stopAndDraft() {
    try {
      setState(STATES.PROCESSING);
      if (recording) {
        await recording.stopAndUnloadAsync();
        const uri = recording.getURI();
        setRecording(null);

        if (!USE_MOCKS && uri) {
          // Real backend path — upload audio, call MomController::api_draft.
          const form = new FormData();
          form.append('audio', { uri, name: 'meeting.m4a', type: 'audio/m4a' });
          form.append('task_id', leadId);
          const t = await tools.transcribeAudio(form);
          const d = await tools.draftMom(leadId, t.transcript);
          setDraft(d);
          setState(STATES.DRAFT);
          return;
        }
      }
      // Mock path: pretend to think for a moment
      setTimeout(() => {
        setDraft(MOCK_DRAFT);
        setState(STATES.DRAFT);
      }, 1600);
    } catch (e) {
      Alert.alert('Drafting failed', String(e?.message || e));
      setState(STATES.IDLE);
    }
  }

  function resetAll() {
    setDraft(null);
    setSeconds(0);
    setState(STATES.IDLE);
  }

  const minutes = String(Math.floor(seconds / 60)).padStart(2, '0');
  const secs = String(seconds % 60).padStart(2, '0');

  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" />

      <LinearGradient
        colors={agent.gradient}
        start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}
        style={styles.header}
      >
        <Pressable onPress={() => navigation.goBack()} hitSlop={12}>
          <Ionicons name="chevron-back" size={26} color="#fff" />
        </Pressable>
        <View style={styles.headerIcon}>
          <Ionicons name="mic" size={20} color="#fff" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>{agent.name}</Text>
          <Text style={styles.headerSub}>Lead {leadId} · voice → MoM</Text>
        </View>
      </LinearGradient>

      <ScrollView contentContainerStyle={{ padding: 20, paddingBottom: 40 }} showsVerticalScrollIndicator={false}>
        {/* Idle */}
        {state === STATES.IDLE && (
          <View style={styles.centerBlock}>
            <Animated.View style={[styles.bigMicRing, { backgroundColor: agent.gradient[0] + '22' }]}>
              <Pressable onPress={startRecording} style={[styles.bigMic, { backgroundColor: agent.gradient[1] }]}>
                <Ionicons name="mic" size={42} color="#fff" />
              </Pressable>
            </Animated.View>
            <Text style={styles.bigLabel}>Tap to start recording</Text>
            <Text style={styles.bigSub}>
              I'll transcribe the meeting, draft the MoM, and propose the next action — you just review.
            </Text>
            <View style={styles.tipBox}>
              <Ionicons name="information-circle" size={14} color={colors.info} />
              <Text style={styles.tipText}>
                Audio stays on-device until you confirm. Backend call: {TOOL_ENDPOINTS.draft_mom}
              </Text>
            </View>
            <View style={{ marginTop: 14, flexDirection: 'row', gap: 8 }}>
              <Pressable
                onPress={() => navigation?.navigate?.('StartMom')}
                style={{ flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6, backgroundColor: 'rgba(20,184,166,0.10)', borderColor: 'rgba(20,184,166,0.40)', borderWidth: 1, paddingVertical: 10, paddingHorizontal: 12, borderRadius: 999 }}
              >
                <Ionicons name="clipboard-outline" size={14} color="#0F766E" />
                <Text style={{ color: '#0F766E', fontSize: 12, fontWeight: '700' }}>Existing lead</Text>
              </Pressable>
              <Pressable
                onPress={() => navigation?.navigate?.('StartMom', { bargeIn: true })}
                style={{ flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6, backgroundColor: 'rgba(239,68,68,0.10)', borderColor: 'rgba(239,68,68,0.40)', borderWidth: 1, paddingVertical: 10, paddingHorizontal: 12, borderRadius: 999 }}
              >
                <Ionicons name="flash" size={14} color="#B91C1C" />
                <Text style={{ color: '#B91C1C', fontSize: 12, fontWeight: '700' }}>Barge-in (new school)</Text>
              </Pressable>
            </View>
          </View>
        )}

        {/* Recording */}
        {state === STATES.RECORDING && (
          <View style={styles.centerBlock}>
            <Animated.View style={[styles.bigMicRing, {
              backgroundColor: '#FCA5A5',
              transform: [{ scale: pulse }],
            }]}>
              <View style={[styles.bigMic, { backgroundColor: colors.danger }]}>
                <View style={styles.recDot} />
              </View>
            </Animated.View>
            <Text style={styles.timer}>{minutes}:{secs}</Text>
            <Text style={styles.bigSub}>Recording your meeting. Speak naturally.</Text>
            <Pressable onPress={stopAndDraft} style={styles.stopBtn}>
              <Ionicons name="stop" size={16} color="#fff" />
              <Text style={styles.stopBtnText}>Stop & draft MoM</Text>
            </Pressable>
          </View>
        )}

        {/* Processing */}
        {state === STATES.PROCESSING && (
          <View style={styles.centerBlock}>
            <View style={[styles.bigMicRing, { backgroundColor: agent.gradient[0] + '22' }]}>
              <View style={[styles.bigMic, { backgroundColor: agent.gradient[1] }]}>
                <Ionicons name="sparkles" size={36} color="#fff" />
              </View>
            </View>
            <Text style={styles.bigLabel}>Drafting your MoM…</Text>
            <View style={{ gap: 6, marginTop: 12 }}>
              <ProcessingStep label="Transcribing audio" endpoint="MomController::api_transcribe" />
              <ProcessingStep label="Drafting MoM with context" endpoint="MomController::api_draft" />
              <ProcessingStep label="Proposing next action" endpoint="schedule_followup" />
            </View>
          </View>
        )}

        {/* Draft */}
        {state === STATES.DRAFT && draft && (
          <View style={{ gap: 14 }}>
            <View style={styles.qualityCard}>
              <Text style={styles.qualityKicker}>MoM quality score</Text>
              <View style={styles.qualityRow}>
                <Text style={styles.qualityScore}>{Math.round(draft.quality * 100)}</Text>
                <Text style={styles.qualityOf}>/ 100</Text>
                <View style={{ flex: 1 }} />
                <View style={[styles.qualityBadge, { backgroundColor: colors.success + '20' }]}>
                  <Text style={[styles.qualityBadgeText, { color: colors.success }]}>Good</Text>
                </View>
              </View>
              <View style={styles.qualityBarBg}>
                <View style={[styles.qualityBarFill, { width: `${draft.quality * 100}%`, backgroundColor: agent.gradient[1] }]} />
              </View>
            </View>

            <View style={styles.section}>
              <Text style={styles.sectionLabel}>Summary</Text>
              <Text style={styles.summaryText}>{draft.summary}</Text>
            </View>

            <View style={styles.section}>
              <Text style={styles.sectionLabel}>Key facts</Text>
              <View style={styles.factsGrid}>
                {draft.facts.map((f) => (
                  <View key={f.k} style={styles.factCell}>
                    <Text style={styles.factK}>{f.k}</Text>
                    <Text style={styles.factV}>{f.v}</Text>
                  </View>
                ))}
              </View>
            </View>

            <View style={styles.section}>
              <Text style={styles.sectionLabel}>Proposed next action</Text>
              <View style={styles.actionCard}>
                <Ionicons name="calendar" size={18} color={agent.gradient[1]} />
                <View style={{ flex: 1 }}>
                  <Text style={styles.actionText}>{draft.nextAction.text}</Text>
                  <Text style={styles.actionWhen}>{draft.nextAction.when}</Text>
                </View>
              </View>
              <View style={styles.actionBtnRow}>
                <Pressable style={[styles.actionBtn, { backgroundColor: agent.gradient[1] }]}>
                  <Ionicons name="checkmark" size={14} color="#fff" />
                  <Text style={[styles.actionBtnText, { color: '#fff' }]}>Approve & schedule</Text>
                </Pressable>
                <Pressable style={[styles.actionBtn, styles.actionBtnGhost]}>
                  <Text style={[styles.actionBtnText, { color: colors.text }]}>Edit</Text>
                </Pressable>
              </View>
            </View>

            {draft.warnings?.length > 0 && (
              <View style={styles.warnCard}>
                <Ionicons name="warning" size={14} color={colors.warning} />
                <Text style={styles.warnText}>{draft.warnings[0]}</Text>
              </View>
            )}

            <Pressable onPress={resetAll} style={styles.restartBtn}>
              <Ionicons name="refresh" size={14} color={colors.textMuted} />
              <Text style={styles.restartText}>Record another meeting</Text>
            </Pressable>
          </View>
        )}
      </ScrollView>
    </View>
  );
}

function ProcessingStep({ label, endpoint }) {
  return (
    <View style={styles.procStep}>
      <View style={styles.procDot} />
      <View style={{ flex: 1 }}>
        <Text style={styles.procLabel}>{label}</Text>
        <Text style={styles.procEndpoint}>{endpoint}</Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.cardAlt },
  header: {
    flexDirection: 'row', alignItems: 'center', gap: 10,
    paddingTop: Platform.OS === 'ios' ? 54 : 36,
    paddingBottom: 14, paddingHorizontal: 14,
  },
  headerIcon: {
    width: 36, height: 36, borderRadius: 11,
    backgroundColor: 'rgba(255,255,255,0.22)',
    alignItems: 'center', justifyContent: 'center',
  },
  headerTitle: { color: '#fff', fontSize: 17, fontWeight: '700' },
  headerSub: { color: 'rgba(255,255,255,0.8)', fontSize: 11, marginTop: 1 },

  centerBlock: { alignItems: 'center', paddingTop: 28, gap: 14 },
  bigMicRing: {
    width: 180, height: 180, borderRadius: 90,
    alignItems: 'center', justifyContent: 'center',
  },
  bigMic: {
    width: 130, height: 130, borderRadius: 65,
    alignItems: 'center', justifyContent: 'center',
    shadowColor: '#000', shadowOpacity: 0.2, shadowRadius: 14, shadowOffset: { width: 0, height: 6 }, elevation: 8,
  },
  recDot: { width: 32, height: 32, borderRadius: 4, backgroundColor: '#fff' },
  bigLabel: { color: colors.text, fontSize: 17, fontWeight: '700', marginTop: 6 },
  bigSub: { color: colors.textMuted, fontSize: 13, textAlign: 'center', paddingHorizontal: 24, lineHeight: 18 },
  timer: { color: colors.text, fontSize: 38, fontWeight: '700', fontVariant: ['tabular-nums'], marginTop: 10 },
  tipBox: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 8,
    marginTop: 16, paddingHorizontal: 14, paddingVertical: 10,
    backgroundColor: colors.card, borderRadius: 10,
    borderWidth: 1, borderColor: colors.border, maxWidth: 320,
  },
  tipText: { color: colors.textMuted, fontSize: 11.5, flex: 1, lineHeight: 16 },

  stopBtn: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    backgroundColor: colors.danger,
    paddingHorizontal: 18, paddingVertical: 12, borderRadius: 12, marginTop: 8,
  },
  stopBtnText: { color: '#fff', fontWeight: '700', fontSize: 13.5 },

  procStep: {
    flexDirection: 'row', alignItems: 'center', gap: 10,
    backgroundColor: colors.card, paddingHorizontal: 14, paddingVertical: 10,
    borderRadius: 10, borderWidth: 1, borderColor: colors.border, minWidth: 280,
  },
  procDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: colors.btnFrom },
  procLabel: { color: colors.text, fontSize: 12.5, fontWeight: '600' },
  procEndpoint: { color: colors.textMuted, fontSize: 10.5, fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace', marginTop: 1 },

  qualityCard: {
    backgroundColor: colors.card, padding: 14, borderRadius: 14,
    borderWidth: 1, borderColor: colors.border,
  },
  qualityKicker: { color: colors.textMuted, fontSize: 11, fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.5 },
  qualityRow: { flexDirection: 'row', alignItems: 'baseline', gap: 4, marginTop: 6 },
  qualityScore: { color: colors.text, fontSize: 32, fontWeight: '800' },
  qualityOf: { color: colors.textMuted, fontSize: 14 },
  qualityBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8, alignSelf: 'center' },
  qualityBadgeText: { fontSize: 11, fontWeight: '700' },
  qualityBarBg: { height: 6, backgroundColor: colors.cardAlt, borderRadius: 3, marginTop: 12, overflow: 'hidden' },
  qualityBarFill: { height: '100%', borderRadius: 3 },

  section: { backgroundColor: colors.card, padding: 14, borderRadius: 14, borderWidth: 1, borderColor: colors.border },
  sectionLabel: { color: colors.textMuted, fontSize: 11, fontWeight: '700', textTransform: 'uppercase', letterSpacing: 0.5, marginBottom: 8 },
  summaryText: { color: colors.text, fontSize: 13.5, lineHeight: 20 },

  factsGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  factCell: {
    width: '48%', backgroundColor: colors.cardAlt, padding: 10, borderRadius: 9,
  },
  factK: { color: colors.textMuted, fontSize: 10.5, fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.4 },
  factV: { color: colors.text, fontSize: 13, fontWeight: '600', marginTop: 2 },

  actionCard: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 10,
    backgroundColor: colors.cardAlt, padding: 12, borderRadius: 10,
  },
  actionText: { color: colors.text, fontSize: 13, lineHeight: 18.5 },
  actionWhen: { color: colors.textMuted, fontSize: 11.5, marginTop: 4, fontWeight: '600' },
  actionBtnRow: { flexDirection: 'row', gap: 8, marginTop: 10 },
  actionBtn: {
    flexDirection: 'row', alignItems: 'center', gap: 5,
    paddingVertical: 10, paddingHorizontal: 14, borderRadius: 9,
  },
  actionBtnGhost: { backgroundColor: colors.cardAlt, borderWidth: 1, borderColor: colors.border },
  actionBtnText: { fontSize: 12.5, fontWeight: '700' },

  warnCard: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 8,
    backgroundColor: '#FEF3C7', padding: 12, borderRadius: 10,
    borderWidth: 1, borderColor: '#FCD34D',
  },
  warnText: { color: '#92400E', fontSize: 12.5, flex: 1, lineHeight: 17 },

  restartBtn: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    paddingVertical: 12, marginTop: 4,
  },
  restartText: { color: colors.textMuted, fontSize: 13, fontWeight: '600' },
});
