// StartMomScreen — kick off a Minutes-of-Meeting capture.
// Pick template → confirm attendees + lead → start recording.
// Next: routes into MomDrafterScreen with the recording in progress.
//
// Backend mapping:
//   - Start:   MomController::api_start ({lead_id, template, attendees[]}) → mom_id
//   - Live recording is uploaded to MomController::api_chunk every 15s
//   - Finalize: MomController::api_finalize → transcript + AI draft

import React, { useState } from 'react';
import { View, Text, ScrollView, Pressable, TextInput, StyleSheet } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { MOM_TEMPLATES, CLUSTER_BDS } from '../data/cm';

const SUGGESTED_LEADS = [
  { id: 'L-1042', school: 'KV Andheri',         stage: 'Tentative',    value: '₹4.2L' },
  { id: 'L-1041', school: 'Govt. HS Pune',      stage: 'Reachout',     value: '₹3.5L' },
  { id: 'L-1040', school: 'Z.P. Nashik',        stage: 'Tentative',    value: '₹2.8L' },
];

const DEFAULT_ATTENDEES = [
  { id: 'me', name: 'You (BD)',          role: 'BD',         required: true },
];

export default function StartMomScreen({ navigation, route }) {
  const bargeIn = route?.params?.bargeIn === true;
  const [template, setTemplate] = useState('discovery');
  const [lead, setLead]         = useState(bargeIn ? null : 'L-1042');
  const [bargeSchool, setBargeSchool] = useState('');
  const [bargeCity,   setBargeCity]   = useState('');
  const [extraAttendee, setExtraAttendee] = useState('');
  const [attendees, setAttendees] = useState([
    ...DEFAULT_ATTENDEES,
    { id: 'dm', name: 'Mr. Bhandari', role: 'DM · GM-CSR', required: true },
  ]);

  const tpl = MOM_TEMPLATES.find(t => t.id === template);

  const addAttendee = () => {
    if (extraAttendee.trim().length < 2) return;
    setAttendees(a => [...a, { id: `a${a.length}`, name: extraAttendee.trim(), role: 'Attendee', required: false }]);
    setExtraAttendee('');
  };

  const canStart = bargeIn ? bargeSchool.trim().length > 1 : !!lead;

  const handleStart = () => {
    if (!canStart) return;
    if (bargeIn) {
      // Barge-in path: stub init_call inline, then start MoM.
      // Backend: AIAgents/LeadSourcing_model::create_stub_from_meeting({school, city})
      //          → init_call row (status=Open, source='meeting_barge')
      //          → MomController::api_start({lead_id: <new>, template, attendees, barge:true})
      const stubLeadId = 'L-1052'; // server-assigned in real flow
      navigation?.navigate?.('MomDrafter', {
        lead: stubLeadId,
        template,
        attendees,
        barge: { school: bargeSchool.trim(), city: bargeCity.trim() },
      });
      return;
    }
    navigation?.navigate?.('MomDrafter', { lead, template, attendees });
  };

  return (
    <View style={{ flex: 1, backgroundColor: colors.cardAlt }}>
      <LinearGradient colors={['#14B8A6', '#0F766E']} style={styles.hero}>
        <View style={styles.heroTop}>
          <Pressable onPress={() => navigation?.goBack?.()} style={styles.iconBtn}>
            <Ionicons name="close" size={22} color="#fff" />
          </Pressable>
          <Text style={styles.heroEyebrow}>MOM · NEW MEETING</Text>
          <View style={{ width: 36 }} />
        </View>
        <Text style={styles.heroTitle}>{bargeIn ? 'Barge-in MoM' : 'Start MoM'}</Text>
        <Text style={styles.heroSub}>
          {bargeIn ? 'New school · lead created from transcript' : `${tpl?.label} · ~${tpl?.expected_minutes} min · ${attendees.length} attendees`}
        </Text>
        {bargeIn && (
          <View style={styles.bargeNote}>
            <Ionicons name="flash" size={14} color="#fff" />
            <Text style={styles.bargeNoteText}>AI will draft the init_call row from the transcript when you finish recording.</Text>
          </View>
        )}
      </LinearGradient>

      <ScrollView contentContainerStyle={{ padding: 16, paddingBottom: 110 }}>
        {/* Template */}
        <Text style={styles.section}>TEMPLATE</Text>
        <View style={{ gap: 8 }}>
          {MOM_TEMPLATES.map(t => {
            const active = template === t.id;
            return (
              <Pressable key={t.id} onPress={() => setTemplate(t.id)} style={[styles.tplCard, active && styles.tplCardActive]}>
                <View style={[styles.tplRadio, active && styles.tplRadioActive]}>
                  {active && <View style={styles.tplRadioDot} />}
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={[styles.tplLabel, active && styles.tplLabelActive]}>{t.label}</Text>
                  <Text style={styles.tplAgenda} numberOfLines={1}>{t.agenda.slice(0, 3).join(' · ')}…</Text>
                </View>
                <Text style={styles.tplMin}>{t.expected_minutes}m</Text>
              </Pressable>
            );
          })}
        </View>

        {/* Agenda preview */}
        <Text style={styles.section}>AGENDA</Text>
        <View style={styles.card}>
          {tpl?.agenda.map((item, i) => (
            <View key={i} style={styles.agendaRow}>
              <View style={styles.agendaDot}><Text style={styles.agendaDotText}>{i + 1}</Text></View>
              <Text style={styles.agendaText}>{item}</Text>
            </View>
          ))}
        </View>

        {/* Lead OR new-school stub */}
        {bargeIn ? (
          <>
            <Text style={styles.section}>NEW SCHOOL (will become L-xxxx)</Text>
            <View style={styles.card}>
              <Text style={styles.bargeLabel}>School name *</Text>
              <TextInput
                value={bargeSchool} onChangeText={setBargeSchool}
                placeholder="e.g. Bombay Scottish, Mahim"
                placeholderTextColor={colors.textMuted}
                style={styles.bargeInput}
              />
              <View style={styles.bargeDivider} />
              <Text style={styles.bargeLabel}>City</Text>
              <TextInput
                value={bargeCity} onChangeText={setBargeCity}
                placeholder="e.g. Mumbai"
                placeholderTextColor={colors.textMuted}
                style={styles.bargeInput}
              />
            </View>
            <Text style={styles.bargeHelp}>Just the school name is enough — the AI will fill DM, program fit, and value range from the meeting transcript.</Text>
          </>
        ) : (
          <>
            <Text style={styles.section}>LEAD</Text>
            <View style={{ gap: 8 }}>
              {SUGGESTED_LEADS.map(l => {
                const active = lead === l.id;
                return (
                  <Pressable key={l.id} onPress={() => setLead(l.id)} style={[styles.leadCard, active && styles.leadCardActive]}>
                    <View style={[styles.leadIcon, active && { backgroundColor: colors.btnFrom }]}>
                      <Ionicons name="flash" size={16} color={active ? '#fff' : colors.btnFrom} />
                    </View>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.leadIdText}>{l.id} · {l.school}</Text>
                      <Text style={styles.leadMeta}>{l.stage} · {l.value}</Text>
                    </View>
                    <View style={[styles.radio, active && styles.radioActive]}>
                      {active && <View style={styles.radioDot} />}
                    </View>
                  </Pressable>
                );
              })}
            </View>
          </>
        )}

        {/* Attendees */}
        <Text style={styles.section}>ATTENDEES</Text>
        <View style={styles.card}>
          {attendees.map(a => (
            <View key={a.id} style={styles.attRow}>
              <View style={styles.attAvatar}>
                <Ionicons name="person" size={14} color={colors.btnFrom} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.attName}>{a.name}</Text>
                <Text style={styles.attRole}>{a.role}</Text>
              </View>
              {a.required && (
                <View style={styles.reqPill}>
                  <Text style={styles.reqText}>REQUIRED</Text>
                </View>
              )}
            </View>
          ))}
          <View style={styles.addAttRow}>
            <TextInput
              value={extraAttendee} onChangeText={setExtraAttendee}
              placeholder="Add attendee name…"
              placeholderTextColor={colors.textMuted}
              style={styles.addAttInput}
              onSubmitEditing={addAttendee}
              returnKeyType="done"
            />
            <Pressable onPress={addAttendee} style={styles.addAttBtn}>
              <Ionicons name="add" size={18} color={colors.btnFrom} />
            </Pressable>
          </View>
        </View>

        {/* Audio settings */}
        <Text style={styles.section}>RECORDING</Text>
        <View style={styles.card}>
          <View style={styles.optRow}>
            <Ionicons name="mic" size={16} color={colors.btnFrom} />
            <Text style={styles.optText}>Audio · auto-transcribe (Whisper-small)</Text>
            <View style={styles.optBadge}><Text style={styles.optBadgeText}>ON</Text></View>
          </View>
          <View style={styles.divider} />
          <View style={styles.optRow}>
            <Ionicons name="people" size={16} color={colors.btnFrom} />
            <Text style={styles.optText}>Speaker diarization</Text>
            <View style={styles.optBadge}><Text style={styles.optBadgeText}>ON</Text></View>
          </View>
          <View style={styles.divider} />
          <View style={styles.optRow}>
            <Ionicons name="document-text" size={16} color={colors.btnFrom} />
            <Text style={styles.optText}>Auto-share MoM with CM after draft</Text>
            <View style={styles.optBadge}><Text style={styles.optBadgeText}>ON</Text></View>
          </View>
        </View>
      </ScrollView>

      <View style={styles.footer}>
        <Pressable onPress={handleStart} style={styles.startBtn}>
          <View style={styles.recDot} />
          <Text style={styles.startText}>Start recording</Text>
        </Pressable>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  hero: { paddingTop: 60, paddingBottom: 22, paddingHorizontal: 16 },
  heroTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  iconBtn: { width: 36, height: 36, borderRadius: 18, backgroundColor: 'rgba(255,255,255,0.18)', alignItems: 'center', justifyContent: 'center' },
  heroEyebrow: { color: 'rgba(255,255,255,0.85)', fontSize: 11, fontWeight: '800', letterSpacing: 1 },
  heroTitle: { color: '#fff', fontSize: 28, fontWeight: '800', marginTop: 18 },
  heroSub: { color: 'rgba(255,255,255,0.88)', fontSize: 13, marginTop: 4 },
  section: { color: colors.textMuted, fontSize: 11, fontWeight: '800', letterSpacing: 1, marginTop: 18, marginBottom: 8 },
  card: { backgroundColor: '#fff', borderRadius: 14, padding: 14, borderWidth: 1, borderColor: colors.border },
  tplCard: { flexDirection: 'row', alignItems: 'center', gap: 12, backgroundColor: '#fff', borderRadius: 12, padding: 12, borderWidth: 1, borderColor: colors.border },
  tplCardActive: { borderColor: '#14B8A6', backgroundColor: 'rgba(20,184,166,0.06)' },
  tplRadio: { width: 18, height: 18, borderRadius: 9, borderWidth: 2, borderColor: colors.border, alignItems: 'center', justifyContent: 'center' },
  tplRadioActive: { borderColor: '#14B8A6' },
  tplRadioDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: '#14B8A6' },
  tplLabel: { fontSize: 13, color: colors.text, fontWeight: '700' },
  tplLabelActive: { color: '#0F766E' },
  tplAgenda: { fontSize: 11, color: colors.textMuted, marginTop: 2 },
  tplMin: { fontSize: 11, color: colors.textMuted, fontWeight: '700' },
  agendaRow: { flexDirection: 'row', alignItems: 'center', gap: 10, paddingVertical: 5 },
  agendaDot: { width: 22, height: 22, borderRadius: 11, backgroundColor: 'rgba(20,184,166,0.12)', alignItems: 'center', justifyContent: 'center' },
  agendaDotText: { color: '#0F766E', fontSize: 11, fontWeight: '800' },
  agendaText: { fontSize: 13, color: colors.text },
  bargeNote: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 12, padding: 10, borderRadius: 10, backgroundColor: 'rgba(255,255,255,0.15)' },
  bargeNoteText: { color: 'rgba(255,255,255,0.95)', fontSize: 11, flex: 1, lineHeight: 15 },
  bargeLabel: { fontSize: 11, color: colors.textMuted, fontWeight: '700', letterSpacing: 0.5 },
  bargeInput: { fontSize: 15, color: colors.text, fontWeight: '600', paddingVertical: 6 },
  bargeDivider: { height: 1, backgroundColor: colors.border, marginVertical: 8 },
  bargeHelp: { fontSize: 11, color: colors.textMuted, marginTop: 8, lineHeight: 15, fontStyle: 'italic' },
  leadCard: { flexDirection: 'row', alignItems: 'center', gap: 12, backgroundColor: '#fff', borderRadius: 12, padding: 12, borderWidth: 1, borderColor: colors.border },
  leadCardActive: { borderColor: colors.btnFrom, backgroundColor: 'rgba(62,33,251,0.04)' },
  leadIcon: { width: 32, height: 32, borderRadius: 10, alignItems: 'center', justifyContent: 'center', backgroundColor: 'rgba(62,33,251,0.10)' },
  leadIdText: { fontSize: 13, color: colors.text, fontWeight: '700' },
  leadMeta: { fontSize: 11, color: colors.textMuted, marginTop: 2 },
  radio: { width: 18, height: 18, borderRadius: 9, borderWidth: 2, borderColor: colors.border, alignItems: 'center', justifyContent: 'center' },
  radioActive: { borderColor: colors.btnFrom },
  radioDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: colors.btnFrom },
  attRow: { flexDirection: 'row', alignItems: 'center', gap: 10, paddingVertical: 6 },
  attAvatar: { width: 28, height: 28, borderRadius: 14, backgroundColor: 'rgba(62,33,251,0.10)', alignItems: 'center', justifyContent: 'center' },
  attName: { fontSize: 13, color: colors.text, fontWeight: '700' },
  attRole: { fontSize: 11, color: colors.textMuted, marginTop: 1 },
  reqPill: { backgroundColor: 'rgba(245,158,11,0.10)', borderColor: 'rgba(245,158,11,0.35)', borderWidth: 1, paddingHorizontal: 6, paddingVertical: 2, borderRadius: 999 },
  reqText: { fontSize: 9, color: '#B45309', fontWeight: '800' },
  addAttRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 8, borderTopWidth: 1, borderTopColor: colors.border, paddingTop: 10 },
  addAttInput: { flex: 1, fontSize: 13, color: colors.text },
  addAttBtn: { width: 32, height: 32, borderRadius: 16, backgroundColor: 'rgba(62,33,251,0.08)', alignItems: 'center', justifyContent: 'center' },
  optRow: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  optText: { flex: 1, fontSize: 12, color: colors.text, fontWeight: '600' },
  optBadge: { backgroundColor: 'rgba(16,185,129,0.10)', borderColor: 'rgba(16,185,129,0.35)', borderWidth: 1, paddingHorizontal: 6, paddingVertical: 2, borderRadius: 999 },
  optBadgeText: { fontSize: 9, color: '#10B981', fontWeight: '800' },
  divider: { height: 1, backgroundColor: colors.border, marginVertical: 10 },
  footer: { position: 'absolute', left: 0, right: 0, bottom: 0, padding: 14, paddingBottom: 22, backgroundColor: '#fff', borderTopWidth: 1, borderTopColor: colors.border },
  startBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 10, backgroundColor: '#EF4444', paddingVertical: 14, borderRadius: 12 },
  recDot: { width: 12, height: 12, borderRadius: 6, backgroundColor: '#fff' },
  startText: { color: '#fff', fontSize: 14, fontWeight: '800' },
});
