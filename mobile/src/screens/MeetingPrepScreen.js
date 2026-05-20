// MeetingPrepScreen — Migration 042 corporate brief viewer.
//
// Shows the full briefing pack inline (talking points, alignment, DM profile,
// timeline, red flags) plus action buttons for the 3 artifacts:
//   - Open PDF        (1-2 page brief)
//   - Open PPT        (6-slide corporate-centric deck)
//   - Share WhatsApp  (cheat sheet under 300 chars)
//
// Route params:
//   eventId   tblcallevents.event_id
//   leadId    init_call.cid_id (optional, for back-nav context)
//   artifact  pre-loaded artifact payload (optional). If absent, screen fetches
//             via GET /api/meeting_prep/artifact?event_id=N.
//
// Backend (real): GET /api/meeting_prep/artifact?event_id=N
//                 returns { status, run_id, brief: {...}, pdf_url, pptx_url,
//                           whatsapp_text, enrichment_status, corporate_name,
//                           dm_name, scheduled_at, bd_name }.

import React, { useEffect, useState } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Platform,
  ActivityIndicator, Linking, Share, Alert,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { USE_MOCKS, tools } from '../api/client';

const HEADER_GRADIENT = ['#0F766E', '#14B8A6']; // teal pair, matches corporate / serious tone

const MOCK_BRIEF = {
  status: 'ready',
  run_id: 17,
  event_id: 9001,
  corporate_name: 'Acme Industries Pvt Ltd',
  dm_name: 'Mr. Rakesh Bhandari',
  dm_designation: 'General Manager, CSR',
  bd_name: 'Priya Menon',
  scheduled_at: '2026-05-20 11:00',
  generated_at: '2026-05-20 06:42',
  pdf_url: '/var/www/stem-meeting-prep-artifacts/2026/05/20/brief_9001.pdf',
  pptx_url: '/var/www/stem-meeting-prep-artifacts/2026/05/20/deck_9001.pptx',
  whatsapp_text:
    'Meeting Acme today 11 AM. Their CSR: STEM education in Tier-2. We have run 7 Mini Science Centres similar. Proposing 12-school pilot in their districts. Ask: intro to 3 reference schools.',
  brief: {
    csr_thesis:
      'Acme has spent Rs 18 crore over 3 years on STEM education in Tier-2 cities (FY23-FY25). Focus districts: Pune rural, Aurangabad, Nashik. Stated theme: hands-on labs over digital content. Board approved Rs 8 crore CSR budget for FY26.',
    csr_projects: [
      { year: 'FY25', title: 'Mini Science Centre rollout - 14 schools, Pune rural', spend_rs: '4.2 crore' },
      { year: 'FY24', title: 'Teacher training program - 60 govt schools', spend_rs: '2.8 crore' },
      { year: 'FY23', title: 'Lab equipment seed - 8 schools, Aurangabad',     spend_rs: '1.6 crore' },
    ],
    alignment_angles: [
      { their: 'Hands-on labs over digital', ours: 'STEM Mini Science Centres are physical labs, not tablets.' },
      { their: 'Tier-2 district focus',      ours: 'STEM has run 7 MSCs in Pune rural / Aurangabad / Nashik.' },
      { their: 'Reference-school model',     ours: 'STEM provides 3-year measurement reports per school.' },
    ],
    talking_points: [
      'Open with their FY25 Pune rollout - acknowledge the 14-school number and the teacher training that followed. They are proud of this.',
      'Position STEM as an implementation partner, not a competing program. Their Pune rollout used a different vendor; we slot in as the next 12 schools, not a replacement.',
      'Reference our DAV Nagpur outcome - 38 percent improvement in 8th-grade science scores over 2 years (data on slide 4).',
      'Acknowledge the elephant: Rs 18 cr spent already. The question is what changes in FY26 to make the next Rs 8 cr produce 2x outcomes. We have a measurement framework.',
      'Ask gently for the FY26 CSR plan timeline. Most corporates lock allocations by mid-June. If they have a Q1 deadline, we need to show a 12-school pilot proposal in 10 days.',
    ],
    proposed_ask:
      'Introduction to 3 reference schools from their FY25 cohort. We will visit those schools in week of 26 May, write a one-page comparison brief, and come back on 5 June with a 12-school pilot proposal sized at Rs 4.8 crore for FY26.',
    proposed_size_rs: '4.8 crore (12 schools at Rs 40 lakh each)',
    timeline: [
      { when: '20 May', what: 'Today\'s meeting - introduction + reference school ask' },
      { when: '26-30 May', what: 'Visit 3 reference schools, write comparison brief' },
      { when: '5 Jun', what: '12-school pilot proposal, Rs 4.8 crore' },
      { when: '15 Jun', what: 'Target: CSR committee signoff before Q1 FY26 lock' },
    ],
    decision_maker: {
      name: 'Mr. Rakesh Bhandari',
      designation: 'General Manager, CSR',
      tenure_years: 4,
      previous_role: 'CSR Head, MahaTech Industries (2019-2022)',
      education: 'IIM Lucknow MBA 2008, Mechanical Engg COEP Pune 2003',
      reports_to: 'Mrs. Sunita Acme, Director CSR & ESG',
      signals: [
        'Active on LinkedIn, posts about hands-on STEM weekly.',
        'Quoted in 2 industry interviews emphasising "measurement, not just disbursement".',
        'Travelled to 6 of 14 Pune schools personally last year - hands-on operator.',
      ],
      red_flags: [
        'Has publicly criticised "vendor-driven CSR" - lead with our role as partner, not vendor.',
      ],
    },
    red_flags: [
      'Last vendor relationship ended in early FY25 with a complaint about post-installation absenteeism. Be ready with our 36-month service contract.',
      'CFO Mr. Vipul Mehra is on the CSR committee and is known to push back on "soft outcome" metrics. Lead with quantified scores.',
    ],
  },
  enrichment_status: {
    internal: 'ok', cache: 'hit',
    linkedin: 'ok', apollo: 'skipped (cached)',
    political: 'ok', csr_project: 'ok',
  },
};

export default function MeetingPrepScreen({ route, navigation }) {
  const eventId = route && route.params && route.params.eventId ? route.params.eventId : 9001;
  const preloaded = route && route.params && route.params.artifact ? route.params.artifact : null;

  const [data, setData] = useState(preloaded || null);
  const [loading, setLoading] = useState(!preloaded);

  useEffect(() => {
    if (data) return;
    let cancelled = false;
    async function load() {
      try {
        if (USE_MOCKS) {
          setTimeout(() => { if (!cancelled) { setData(MOCK_BRIEF); setLoading(false); } }, 600);
          return;
        }
        const res = await tools.runTool('meeting_prep_artifact', { event_id: eventId });
        if (!cancelled) { setData(res); setLoading(false); }
      } catch (e) {
        if (!cancelled) { setLoading(false); Alert.alert('Load failed', String(e && e.message ? e.message : e)); }
      }
    }
    load();
    return () => { cancelled = true; };
  }, [eventId, data]);

  async function openPdf() {
    if (!data || !data.pdf_url) return;
    try {
      await Linking.openURL('https://stemapp.in' + data.pdf_url.replace('/var/www', ''));
    } catch (_) {
      Alert.alert('PDF', 'Open from web: ' + data.pdf_url);
    }
  }

  async function openPpt() {
    if (!data || !data.pptx_url) return;
    try {
      await Linking.openURL('https://stemapp.in' + data.pptx_url.replace('/var/www', ''));
    } catch (_) {
      Alert.alert('PPT', 'Open from web: ' + data.pptx_url);
    }
  }

  async function shareWhatsApp() {
    if (!data || !data.whatsapp_text) return;
    try {
      await Share.share({ message: data.whatsapp_text });
    } catch (_) {}
  }

  if (loading || !data) {
    return (
      <View style={[styles.root, { alignItems: 'center', justifyContent: 'center' }]}>
        <ActivityIndicator size="large" color={colors.btnFrom} />
        <Text style={{ marginTop: 12, color: colors.textMuted }}>Loading brief.</Text>
      </View>
    );
  }

  const brief = data.brief || {};

  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" />

      <LinearGradient colors={HEADER_GRADIENT} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.header}>
        <Pressable onPress={() => navigation && navigation.goBack && navigation.goBack()} hitSlop={12}>
          <Ionicons name="chevron-back" size={26} color="#fff" />
        </Pressable>
        <View style={styles.headerIcon}>
          <Ionicons name="briefcase" size={20} color="#fff" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>{data.corporate_name}</Text>
          <Text style={styles.headerSub}>
            {data.scheduled_at} . With {data.dm_name}
          </Text>
        </View>
      </LinearGradient>

      <ScrollView contentContainerStyle={{ padding: 16, paddingBottom: 40 }} showsVerticalScrollIndicator={false}>

        {/* ===== Artifact actions ===== */}
        <View style={styles.actionRow}>
          <Pressable onPress={openPdf} style={[styles.actionBtn, { backgroundColor: HEADER_GRADIENT[1] }]}>
            <Ionicons name="document-text" size={16} color="#fff" />
            <Text style={styles.actionBtnText}>Open PDF</Text>
          </Pressable>
          <Pressable onPress={openPpt} style={[styles.actionBtn, { backgroundColor: '#7C3AED' }]}>
            <Ionicons name="easel" size={16} color="#fff" />
            <Text style={styles.actionBtnText}>Open PPT</Text>
          </Pressable>
          <Pressable onPress={shareWhatsApp} style={[styles.actionBtn, { backgroundColor: '#22C55E' }]}>
            <Ionicons name="logo-whatsapp" size={16} color="#fff" />
            <Text style={styles.actionBtnText}>WhatsApp</Text>
          </Pressable>
        </View>

        <Text style={styles.runMeta}>
          Run {data.run_id} . Generated {data.generated_at} . BD {data.bd_name}
        </Text>

        {/* ===== CSR thesis ===== */}
        <Section label="Their CSR thesis">
          <Text style={styles.body}>{brief.csr_thesis}</Text>
        </Section>

        {/* ===== Recent CSR projects ===== */}
        {brief.csr_projects && brief.csr_projects.length > 0 && (
          <Section label="Recent CSR projects">
            {brief.csr_projects.map((p, i) => (
              <View key={i} style={styles.csrRow}>
                <View style={styles.yearChip}><Text style={styles.yearChipText}>{p.year}</Text></View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.csrTitle}>{p.title}</Text>
                  <Text style={styles.csrSpend}>Rs {p.spend_rs}</Text>
                </View>
              </View>
            ))}
          </Section>
        )}

        {/* ===== Alignment ===== */}
        {brief.alignment_angles && brief.alignment_angles.length > 0 && (
          <Section label="Where the alignment is">
            {brief.alignment_angles.map((a, i) => (
              <View key={i} style={styles.alignRow}>
                <View style={styles.alignCol}>
                  <Text style={styles.alignKicker}>Their priority</Text>
                  <Text style={styles.alignText}>{a.their}</Text>
                </View>
                <Ionicons name="arrow-forward" size={14} color={colors.textMuted} style={{ marginHorizontal: 8 }} />
                <View style={styles.alignCol}>
                  <Text style={styles.alignKicker}>STEM offering</Text>
                  <Text style={styles.alignText}>{a.ours}</Text>
                </View>
              </View>
            ))}
          </Section>
        )}

        {/* ===== Talking points ===== */}
        {brief.talking_points && brief.talking_points.length > 0 && (
          <Section label="Talking points (in order)">
            {brief.talking_points.map((t, i) => (
              <View key={i} style={styles.tpRow}>
                <View style={styles.tpNum}><Text style={styles.tpNumText}>{i + 1}</Text></View>
                <Text style={styles.tpText}>{t}</Text>
              </View>
            ))}
          </Section>
        )}

        {/* ===== Today's ask ===== */}
        {brief.proposed_ask && (
          <Section label="Today's ask">
            <Text style={styles.body}>{brief.proposed_ask}</Text>
            {brief.proposed_size_rs && (
              <View style={styles.sizeChip}>
                <Ionicons name="cash" size={13} color={colors.success} />
                <Text style={styles.sizeChipText}>Proposed size: Rs {brief.proposed_size_rs}</Text>
              </View>
            )}
          </Section>
        )}

        {/* ===== Timeline ===== */}
        {brief.timeline && brief.timeline.length > 0 && (
          <Section label="Suggested timeline">
            {brief.timeline.map((t, i) => (
              <View key={i} style={styles.timelineRow}>
                <View style={styles.timelineDot} />
                <View style={{ flex: 1 }}>
                  <Text style={styles.timelineWhen}>{t.when}</Text>
                  <Text style={styles.timelineWhat}>{t.what}</Text>
                </View>
              </View>
            ))}
          </Section>
        )}

        {/* ===== Decision maker ===== */}
        {brief.decision_maker && (
          <Section label="Decision maker">
            <Text style={styles.dmName}>{brief.decision_maker.name}</Text>
            <Text style={styles.dmDes}>{brief.decision_maker.designation}</Text>
            <View style={{ marginTop: 8, gap: 4 }}>
              {brief.decision_maker.tenure_years && (
                <DmFact k="Tenure" v={brief.decision_maker.tenure_years + ' years in role'} />
              )}
              {brief.decision_maker.previous_role && (
                <DmFact k="Previous" v={brief.decision_maker.previous_role} />
              )}
              {brief.decision_maker.education && (
                <DmFact k="Education" v={brief.decision_maker.education} />
              )}
              {brief.decision_maker.reports_to && (
                <DmFact k="Reports to" v={brief.decision_maker.reports_to} />
              )}
            </View>
            {brief.decision_maker.signals && brief.decision_maker.signals.length > 0 && (
              <View style={{ marginTop: 10 }}>
                <Text style={styles.miniLabel}>Signals to read</Text>
                {brief.decision_maker.signals.map((s, i) => (
                  <View key={i} style={styles.signalRow}>
                    <Ionicons name="ellipse" size={5} color={colors.textMuted} style={{ marginTop: 7 }} />
                    <Text style={styles.signalText}>{s}</Text>
                  </View>
                ))}
              </View>
            )}
            {brief.decision_maker.red_flags && brief.decision_maker.red_flags.length > 0 && (
              <View style={{ marginTop: 10 }}>
                <Text style={[styles.miniLabel, { color: colors.danger }]}>Watch out</Text>
                {brief.decision_maker.red_flags.map((s, i) => (
                  <View key={i} style={styles.signalRow}>
                    <Ionicons name="warning" size={11} color={colors.danger} style={{ marginTop: 3 }} />
                    <Text style={styles.signalText}>{s}</Text>
                  </View>
                ))}
              </View>
            )}
          </Section>
        )}

        {/* ===== Red flags ===== */}
        {brief.red_flags && brief.red_flags.length > 0 && (
          <View style={styles.flagsCard}>
            <View style={styles.flagsHeader}>
              <Ionicons name="warning" size={14} color={colors.danger} />
              <Text style={styles.flagsTitle}>Red flags before you walk in</Text>
            </View>
            {brief.red_flags.map((f, i) => (
              <View key={i} style={styles.flagRow}>
                <Text style={styles.flagBullet}>.</Text>
                <Text style={styles.flagText}>{f}</Text>
              </View>
            ))}
          </View>
        )}

        {/* ===== WhatsApp preview ===== */}
        {data.whatsapp_text && (
          <Section label="WhatsApp cheat sheet (tap above to share)">
            <View style={styles.waBox}>
              <Text style={styles.waText}>{data.whatsapp_text}</Text>
              <Text style={styles.waLen}>{data.whatsapp_text.length} / 300 chars</Text>
            </View>
          </Section>
        )}

        {/* ===== Enrichment status footer ===== */}
        {data.enrichment_status && (
          <View style={styles.enrichCard}>
            <Text style={styles.enrichTitle}>Enrichment status</Text>
            <View style={styles.enrichGrid}>
              {Object.entries(data.enrichment_status).map(([k, v]) => (
                <View key={k} style={styles.enrichCell}>
                  <Text style={styles.enrichK}>{k}</Text>
                  <Text style={[
                    styles.enrichV,
                    v === 'ok'   ? { color: colors.success } :
                    v === 'hit'  ? { color: colors.success } :
                    String(v).startsWith('skipped') ? { color: colors.textMuted } :
                    { color: colors.warning },
                  ]}>{String(v)}</Text>
                </View>
              ))}
            </View>
          </View>
        )}

      </ScrollView>
    </View>
  );
}

function Section({ label, children }) {
  return (
    <View style={styles.section}>
      <Text style={styles.sectionLabel}>{label}</Text>
      {children}
    </View>
  );
}

function DmFact({ k, v }) {
  return (
    <View style={styles.dmFactRow}>
      <Text style={styles.dmFactK}>{k}</Text>
      <Text style={styles.dmFactV}>{v}</Text>
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
  headerTitle: { color: '#fff', fontSize: 16, fontWeight: '700' },
  headerSub:   { color: 'rgba(255,255,255,0.85)', fontSize: 11.5, marginTop: 1 },

  actionRow: { flexDirection: 'row', gap: 8 },
  actionBtn: {
    flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    paddingVertical: 11, borderRadius: 10,
  },
  actionBtnText: { color: '#fff', fontSize: 12.5, fontWeight: '700' },
  runMeta: { color: colors.textMuted, fontSize: 11, marginTop: 10, marginBottom: 4, textAlign: 'center' },

  section: {
    backgroundColor: colors.card, padding: 14, borderRadius: 14,
    borderWidth: 1, borderColor: colors.border, marginTop: 12,
  },
  sectionLabel: {
    color: colors.textMuted, fontSize: 11, fontWeight: '700',
    textTransform: 'uppercase', letterSpacing: 0.5, marginBottom: 10,
  },
  body: { color: colors.text, fontSize: 13.5, lineHeight: 20 },

  csrRow: { flexDirection: 'row', alignItems: 'flex-start', gap: 10, paddingVertical: 6 },
  yearChip: {
    paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6,
    backgroundColor: colors.btnFrom + '15',
  },
  yearChipText: { color: colors.btnFrom, fontSize: 10.5, fontWeight: '700' },
  csrTitle: { color: colors.text, fontSize: 13, fontWeight: '600' },
  csrSpend: { color: colors.textMuted, fontSize: 11.5, marginTop: 2 },

  alignRow: {
    flexDirection: 'row', alignItems: 'stretch',
    backgroundColor: colors.cardAlt, borderRadius: 10, padding: 10, marginBottom: 8,
  },
  alignCol: { flex: 1 },
  alignKicker: {
    color: colors.textMuted, fontSize: 9.5, fontWeight: '700',
    textTransform: 'uppercase', letterSpacing: 0.4, marginBottom: 3,
  },
  alignText: { color: colors.text, fontSize: 12, lineHeight: 16 },

  tpRow: { flexDirection: 'row', alignItems: 'flex-start', gap: 10, paddingVertical: 7 },
  tpNum: {
    width: 22, height: 22, borderRadius: 11, backgroundColor: '#14B8A6',
    alignItems: 'center', justifyContent: 'center',
  },
  tpNumText: { color: '#fff', fontSize: 11, fontWeight: '700' },
  tpText: { flex: 1, color: colors.text, fontSize: 12.5, lineHeight: 18 },

  sizeChip: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    marginTop: 10, paddingVertical: 7, paddingHorizontal: 10,
    backgroundColor: colors.success + '15', borderRadius: 8, alignSelf: 'flex-start',
  },
  sizeChipText: { color: colors.success, fontSize: 12, fontWeight: '700' },

  timelineRow: { flexDirection: 'row', alignItems: 'flex-start', gap: 10, paddingVertical: 6 },
  timelineDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: '#14B8A6', marginTop: 6 },
  timelineWhen: { color: colors.text, fontSize: 12, fontWeight: '700' },
  timelineWhat: { color: colors.textMuted, fontSize: 11.5, marginTop: 1, lineHeight: 16 },

  dmName: { color: colors.text, fontSize: 15, fontWeight: '700' },
  dmDes:  { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  dmFactRow: { flexDirection: 'row', gap: 8 },
  dmFactK:   { width: 80, color: colors.textMuted, fontSize: 11.5, fontWeight: '600' },
  dmFactV:   { flex: 1, color: colors.text, fontSize: 12, lineHeight: 17 },
  miniLabel: {
    color: colors.textMuted, fontSize: 10.5, fontWeight: '700',
    textTransform: 'uppercase', letterSpacing: 0.4, marginBottom: 4,
  },
  signalRow: { flexDirection: 'row', alignItems: 'flex-start', gap: 6, paddingVertical: 3 },
  signalText: { flex: 1, color: colors.text, fontSize: 12, lineHeight: 17 },

  flagsCard: {
    marginTop: 12, backgroundColor: '#FEE2E2', borderRadius: 12,
    borderWidth: 1, borderColor: '#FCA5A5', padding: 12,
  },
  flagsHeader: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 6 },
  flagsTitle: { color: '#991B1B', fontSize: 12.5, fontWeight: '700' },
  flagRow: { flexDirection: 'row', alignItems: 'flex-start', gap: 6, paddingVertical: 3 },
  flagBullet: { color: '#991B1B', fontSize: 14, fontWeight: '700', lineHeight: 16 },
  flagText: { flex: 1, color: '#7F1D1D', fontSize: 12, lineHeight: 17 },

  waBox: {
    backgroundColor: '#DCFCE7', borderRadius: 10, padding: 10,
    borderWidth: 1, borderColor: '#86EFAC',
  },
  waText: { color: '#14532D', fontSize: 12.5, lineHeight: 18 },
  waLen: { color: '#166534', fontSize: 10.5, fontWeight: '600', marginTop: 6, textAlign: 'right' },

  enrichCard: {
    marginTop: 12, backgroundColor: colors.card, borderRadius: 12,
    borderWidth: 1, borderColor: colors.border, padding: 12,
  },
  enrichTitle: {
    color: colors.textMuted, fontSize: 10.5, fontWeight: '700',
    textTransform: 'uppercase', letterSpacing: 0.4, marginBottom: 8,
  },
  enrichGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  enrichCell: {
    width: '31%', backgroundColor: colors.cardAlt, padding: 8, borderRadius: 8,
  },
  enrichK: { color: colors.textMuted, fontSize: 10, fontWeight: '600', textTransform: 'uppercase' },
  enrichV: { color: colors.text, fontSize: 11.5, fontWeight: '600', marginTop: 2 },
});
