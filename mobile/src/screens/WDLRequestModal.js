// WDLRequestModal - opens when a BD tries to set cstatus 4 (Will Do Later).
// Replaces the silent direct-write path. Submits a wdl_request and waits for admin.
// Endpoint: POST /api/wdl/submit  body { init_call_id, uid, from_cstatus, reason, next_followup }
//
// Visibility: BD-only modal launched from LeadDetail or any place that previously offered WDL.

import React, { useState } from 'react';
import {
  Modal, View, Text, TextInput, Pressable, StyleSheet, ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';

const STAGE_LABEL = {
  1: 'Open', 2: 'Reachout', 3: 'Tentative', 6: 'Positive',
  7: 'Proposal Sent', 8: 'Open RPEM', 9: 'Very Positive',
};

export default function WDLRequestModal({
  visible,
  onClose,
  onSubmitted,
  leadId,
  schoolName,
  fromCstatus,
}) {
  const [reason, setReason] = useState('');
  const [nextFollowup, setNextFollowup] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [done, setDone] = useState(false);
  const [requestId, setRequestId] = useState(null);

  const canSubmit = reason.trim().length >= 20 && !submitting;

  async function handleSubmit() {
    if (!canSubmit) return;
    setSubmitting(true);
    // In production this hits POST /api/wdl/submit
    // For demo we simulate the response
    setTimeout(() => {
      setSubmitting(false);
      setDone(true);
      const id = Math.floor(Math.random() * 1000) + 100;
      setRequestId(id);
      onSubmitted && onSubmitted({ request_id: id });
    }, 600);
  }

  function handleClose() {
    setReason('');
    setNextFollowup('');
    setDone(false);
    setRequestId(null);
    onClose && onClose();
  }

  return (
    <Modal visible={visible} animationType="slide" transparent onRequestClose={handleClose}>
      <View style={s.backdrop}>
        <View style={s.sheet}>
          <View style={s.head}>
            <Ionicons name="lock-closed" size={18} color="#f59e0b" />
            <Text style={s.title}>Request Will Do Later</Text>
            <Pressable onPress={handleClose} style={s.closeBtn} hitSlop={8}>
              <Ionicons name="close" size={20} color={colors.text} />
            </Pressable>
          </View>

          {!done && (
            <ScrollView style={{ maxHeight: 420 }}>
              <View style={s.warnCard}>
                <Ionicons name="information-circle" size={16} color="#f59e0b" />
                <Text style={s.warnText}>
                  WDL is not a direct action. An admin must approve every WDL request.
                  Approval typically takes a few hours. Until approved, the lead stays at its current stage.
                </Text>
              </View>

              <View style={s.field}>
                <Text style={s.fieldLabel}>LEAD</Text>
                <Text style={s.fieldVal}>{schoolName} (id {leadId})</Text>
              </View>
              <View style={s.field}>
                <Text style={s.fieldLabel}>CURRENT STAGE</Text>
                <Text style={s.fieldVal}>{STAGE_LABEL[fromCstatus] || ('cs ' + fromCstatus)}</Text>
              </View>

              <View style={s.field}>
                <Text style={s.fieldLabel}>WHY CAN THIS LEAD NOT BE WORKED NOW</Text>
                <TextInput
                  value={reason}
                  onChangeText={setReason}
                  placeholder="Minimum 20 characters. Be specific about the blocker."
                  placeholderTextColor={colors.textMuted}
                  multiline
                  style={s.input}
                />
                <Text style={s.hint}>{reason.length} / 20 minimum</Text>
              </View>

              <View style={s.field}>
                <Text style={s.fieldLabel}>NEXT FOLLOWUP DATE (OPTIONAL)</Text>
                <TextInput
                  value={nextFollowup}
                  onChangeText={setNextFollowup}
                  placeholder="YYYY-MM-DD"
                  placeholderTextColor={colors.textMuted}
                  style={[s.input, { minHeight: 36 }]}
                />
              </View>

              <Pressable
                onPress={handleSubmit}
                disabled={!canSubmit}
                style={[s.submitBtn, !canSubmit && s.submitBtnDisabled]}
              >
                <Text style={s.submitText}>
                  {submitting ? 'Submitting...' : 'Send request to admin'}
                </Text>
              </Pressable>
            </ScrollView>
          )}

          {done && (
            <View style={s.doneWrap}>
              <Ionicons name="checkmark-circle" size={42} color="#10b981" />
              <Text style={s.doneTitle}>Request submitted</Text>
              <Text style={s.doneText}>
                Request id {requestId} is pending admin review. You will receive a notification when it is approved or rejected.
                The lead stays at {STAGE_LABEL[fromCstatus]} until then.
              </Text>
              <Pressable onPress={handleClose} style={[s.submitBtn, { marginTop: 18 }]}>
                <Text style={s.submitText}>Got it</Text>
              </Pressable>
            </View>
          )}
        </View>
      </View>
    </Modal>
  );
}

const s = StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.55)',
    justifyContent: 'flex-end',
  },
  sheet: {
    backgroundColor: colors.card,
    borderTopLeftRadius: 22,
    borderTopRightRadius: 22,
    padding: 18,
    paddingBottom: 32,
  },
  head: { flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 12 },
  title: { color: colors.text, fontWeight: '800', fontSize: 16, flex: 1 },
  closeBtn: { padding: 4 },
  warnCard: {
    flexDirection: 'row',
    gap: 8,
    backgroundColor: '#f59e0b14',
    borderColor: '#f59e0b',
    borderWidth: 1,
    borderRadius: 10,
    padding: 10,
    marginBottom: 14,
  },
  warnText: { color: colors.text, fontSize: 12, flex: 1, lineHeight: 16 },
  field: { marginBottom: 14 },
  fieldLabel: {
    color: colors.textMuted,
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 0.8,
    marginBottom: 4,
  },
  fieldVal: { color: colors.text, fontSize: 14, fontWeight: '600' },
  input: {
    color: colors.text,
    backgroundColor: colors.cardAlt,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 10,
    padding: 10,
    minHeight: 72,
    fontSize: 13,
    textAlignVertical: 'top',
  },
  hint: { color: colors.textMuted, fontSize: 10, marginTop: 4, textAlign: 'right' },
  submitBtn: {
    backgroundColor: colors.btnFrom,
    paddingVertical: 13,
    borderRadius: 10,
    alignItems: 'center',
    marginTop: 4,
  },
  submitBtnDisabled: { backgroundColor: colors.border },
  submitText: { color: '#fff', fontWeight: '700', fontSize: 14 },
  doneWrap: { alignItems: 'center', paddingVertical: 20 },
  doneTitle: { color: colors.text, fontWeight: '800', fontSize: 16, marginTop: 12 },
  doneText: { color: colors.textMuted, fontSize: 13, textAlign: 'center', marginTop: 8, lineHeight: 18, paddingHorizontal: 12 },
});
