// MeetingPrepTriggerButton — reusable on-demand trigger for Migration 042.
//
// Drops into LeadDetailScreen (next to the lead's upcoming meeting row) and
// into DayPlanScreen (next to each planned actiontype 3 or 4 task) and into
// MeetingEconomicsScreen (per-meeting row).
//
// States:
//   - idle (no run yet for this event)           -> shows "Prepare for this meeting"
//   - has_run (artifact ready)                   -> shows "Open prep" + small status pill
//   - running (generate in flight)               -> shows spinner + ETA
//   - skipped/failed                             -> shows reason with retry
//
// Calls Migration 042 endpoints via the standard tools dispatcher:
//   POST /api/meeting_prep/generate              {event_id}
//   GET  /api/meeting_prep/artifact?event_id=N
//
// When USE_MOCKS=true the screen demos end-to-end without backend.
//
// Props:
//   eventId   (required) tblcallevents.event_id
//   leadId    (optional) for navigation to MeetingPrep viewer
//   leadType  (optional) 'corporate' | 'school' | other. Only corporate is supported by 042;
//             other types render nothing (returns null).
//   compact   (optional) bool. Compact pill style for DayPlan rows.
//   navigation (required) for navigating to the MeetingPrepScreen viewer.

import React, { useEffect, useState } from 'react';
import {
  View, Text, Pressable, StyleSheet, ActivityIndicator, Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { USE_MOCKS, tools } from '../api/client';

const STATES = { IDLE: 'idle', RUNNING: 'running', READY: 'ready', FAILED: 'failed', SKIPPED: 'skipped' };

const MOCK_ARTIFACT_READY = {
  status: 'ready',
  run_id: 17,
  event_id: 9001,
  corporate_name: 'Acme Industries Pvt Ltd',
  generated_at: '2026-05-20 06:42',
  pdf_url: '/var/www/stem-meeting-prep-artifacts/2026/05/20/brief_9001.pdf',
  pptx_url: '/var/www/stem-meeting-prep-artifacts/2026/05/20/deck_9001.pptx',
  whatsapp_text:
    'Meeting with Acme today 11 AM. Their CSR thesis: STEM education in Tier-2 schools. We have run 7 Mini Science Centres in similar geographies. Today we propose a 12-school pilot in their target districts. Ask: introduction to 3 reference schools.',
  enrichment_status: { internal: 'ok', cache: 'hit', linkedin: 'ok', apollo: 'skipped (cached)', political: 'ok', csr_project: 'ok' },
};

export default function MeetingPrepTriggerButton({
  eventId, leadId, leadType, compact = false, navigation,
}) {
  // Migration 042 supports corporate leads only.
  if (leadType && leadType !== 'corporate') return null;

  const [state, setState] = useState(STATES.IDLE);
  const [artifact, setArtifact] = useState(null);
  const [eta, setEta] = useState(0);

  // On mount, check if an artifact already exists for this event.
  useEffect(() => {
    let cancelled = false;
    async function check() {
      try {
        if (USE_MOCKS) {
          // 60 percent chance there's already a run for demo purposes.
          if (Math.random() < 0.6) {
            if (!cancelled) {
              setArtifact(MOCK_ARTIFACT_READY);
              setState(STATES.READY);
            }
          }
          return;
        }
        const res = await tools.runTool('meeting_prep_artifact', { event_id: eventId });
        if (cancelled) return;
        if (res && res.status === 'ready') {
          setArtifact(res);
          setState(STATES.READY);
        } else if (res && res.status === 'skipped') {
          setState(STATES.SKIPPED);
          setArtifact(res);
        }
      } catch (_) { /* idle */ }
    }
    if (eventId) check();
    return () => { cancelled = true; };
  }, [eventId]);

  // Tick ETA while running.
  useEffect(() => {
    if (state !== STATES.RUNNING) return;
    setEta(0);
    const t = setInterval(() => setEta((s) => s + 1), 1000);
    return () => clearInterval(t);
  }, [state]);

  async function generate() {
    setState(STATES.RUNNING);
    try {
      if (USE_MOCKS) {
        setTimeout(() => {
          setArtifact(MOCK_ARTIFACT_READY);
          setState(STATES.READY);
        }, 2200);
        return;
      }
      const res = await tools.runTool('meeting_prep_generate', { event_id: eventId });
      if (res && res.status === 'ready') {
        setArtifact(res);
        setState(STATES.READY);
      } else if (res && res.status === 'skipped') {
        setArtifact(res);
        setState(STATES.SKIPPED);
      } else {
        setState(STATES.FAILED);
      }
    } catch (e) {
      setState(STATES.FAILED);
      Alert.alert('Prep failed', String(e && e.message ? e.message : e));
    }
  }

  function openViewer() {
    if (!navigation || !navigation.navigate) return;
    navigation.navigate('MeetingPrep', { eventId, leadId, artifact });
  }

  // ===== Rendering =====
  if (state === STATES.READY && artifact) {
    return (
      <Pressable onPress={openViewer} style={compact ? styles.pillReady : styles.cardReady}>
        <View style={styles.iconWrapReady}>
          <Ionicons name="document-text" size={compact ? 13 : 16} color={colors.success} />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={compact ? styles.pillTitle : styles.cardTitle}>Meeting prep ready</Text>
          {!compact && (
            <Text style={styles.cardSub}>
              {artifact.corporate_name} . Generated {artifact.generated_at}
            </Text>
          )}
        </View>
        <Ionicons name="chevron-forward" size={compact ? 14 : 18} color={colors.textMuted} />
      </Pressable>
    );
  }

  if (state === STATES.RUNNING) {
    return (
      <View style={compact ? styles.pillRunning : styles.cardRunning}>
        <ActivityIndicator size="small" color={colors.btnFrom} />
        <View style={{ flex: 1, marginLeft: 8 }}>
          <Text style={compact ? styles.pillTitle : styles.cardTitle}>Preparing brief</Text>
          {!compact && (
            <Text style={styles.cardSub}>
              Pulling CRM history, CSR projects, decision-maker profile . {eta}s
            </Text>
          )}
        </View>
      </View>
    );
  }

  if (state === STATES.SKIPPED) {
    return (
      <View style={compact ? styles.pillSkipped : styles.cardSkipped}>
        <Ionicons name="information-circle" size={compact ? 13 : 16} color={colors.warning} />
        <View style={{ flex: 1, marginLeft: 8 }}>
          <Text style={compact ? styles.pillTitle : styles.cardTitle}>Prep skipped</Text>
          {!compact && (
            <Text style={styles.cardSub}>
              {artifact && artifact.reason ? artifact.reason : 'Not a corporate lead, or budget exhausted.'}
            </Text>
          )}
        </View>
      </View>
    );
  }

  if (state === STATES.FAILED) {
    return (
      <Pressable onPress={generate} style={compact ? styles.pillFailed : styles.cardFailed}>
        <Ionicons name="refresh" size={compact ? 13 : 16} color={colors.danger} />
        <View style={{ flex: 1, marginLeft: 8 }}>
          <Text style={compact ? styles.pillTitle : styles.cardTitle}>Prep failed . Retry</Text>
          {!compact && <Text style={styles.cardSub}>Tap to try again.</Text>}
        </View>
      </Pressable>
    );
  }

  // Idle
  return (
    <Pressable onPress={generate} style={compact ? styles.pillIdle : styles.cardIdle}>
      <View style={styles.iconWrapIdle}>
        <Ionicons name="sparkles" size={compact ? 13 : 16} color="#fff" />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={compact ? styles.pillTitleIdle : styles.cardTitleIdle}>
          Prepare for this meeting
        </Text>
        {!compact && (
          <Text style={styles.cardSubIdle}>
            Corporate brief, talking points, DM profile, 6-slide deck.
          </Text>
        )}
      </View>
      {!compact && <Ionicons name="chevron-forward" size={18} color="rgba(255,255,255,0.8)" />}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  // ===== Full card (default) =====
  cardIdle: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    paddingVertical: 12, paddingHorizontal: 14, borderRadius: 12,
    backgroundColor: colors.btnFrom,
    shadowColor: colors.btnFrom, shadowOpacity: 0.25, shadowRadius: 8, shadowOffset: { width: 0, height: 4 }, elevation: 3,
  },
  cardReady: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    paddingVertical: 12, paddingHorizontal: 14, borderRadius: 12,
    backgroundColor: colors.card, borderWidth: 1, borderColor: colors.border,
  },
  cardRunning: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    paddingVertical: 12, paddingHorizontal: 14, borderRadius: 12,
    backgroundColor: colors.card, borderWidth: 1, borderColor: colors.border,
  },
  cardSkipped: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    paddingVertical: 12, paddingHorizontal: 14, borderRadius: 12,
    backgroundColor: '#FEF3C7', borderWidth: 1, borderColor: '#FCD34D',
  },
  cardFailed: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    paddingVertical: 12, paddingHorizontal: 14, borderRadius: 12,
    backgroundColor: '#FEE2E2', borderWidth: 1, borderColor: '#FCA5A5',
  },
  cardTitle: { color: colors.text, fontSize: 13.5, fontWeight: '700' },
  cardSub:   { color: colors.textMuted, fontSize: 11.5, marginTop: 2 },
  cardTitleIdle: { color: '#fff', fontSize: 13.5, fontWeight: '700' },
  cardSubIdle:   { color: 'rgba(255,255,255,0.85)', fontSize: 11.5, marginTop: 2 },

  iconWrapIdle: {
    width: 28, height: 28, borderRadius: 8,
    backgroundColor: 'rgba(255,255,255,0.22)',
    alignItems: 'center', justifyContent: 'center',
  },
  iconWrapReady: {
    width: 28, height: 28, borderRadius: 8,
    backgroundColor: colors.success + '20',
    alignItems: 'center', justifyContent: 'center',
  },

  // ===== Compact pill (for DayPlan rows) =====
  pillIdle: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    paddingVertical: 6, paddingHorizontal: 10, borderRadius: 999,
    backgroundColor: colors.btnFrom,
  },
  pillReady: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    paddingVertical: 6, paddingHorizontal: 10, borderRadius: 999,
    backgroundColor: colors.success + '15',
    borderWidth: 1, borderColor: colors.success + '40',
  },
  pillRunning: {
    flexDirection: 'row', alignItems: 'center',
    paddingVertical: 6, paddingHorizontal: 10, borderRadius: 999,
    backgroundColor: colors.cardAlt, borderWidth: 1, borderColor: colors.border,
  },
  pillSkipped: {
    flexDirection: 'row', alignItems: 'center',
    paddingVertical: 6, paddingHorizontal: 10, borderRadius: 999,
    backgroundColor: '#FEF3C7', borderWidth: 1, borderColor: '#FCD34D',
  },
  pillFailed: {
    flexDirection: 'row', alignItems: 'center',
    paddingVertical: 6, paddingHorizontal: 10, borderRadius: 999,
    backgroundColor: '#FEE2E2', borderWidth: 1, borderColor: '#FCA5A5',
  },
  pillTitle:     { color: colors.text, fontSize: 11.5, fontWeight: '700' },
  pillTitleIdle: { color: '#fff',      fontSize: 11.5, fontWeight: '700' },
});
