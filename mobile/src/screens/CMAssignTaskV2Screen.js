/**
 * CMAssignTaskV2Screen.js
 *
 * Mobile line-manager surface for assigning tasks to BDs (rev 7).
 * Mirrors production Menu::dailyTaskAssign behavior:
 *   - selectby = 'Assign Task By <CM/RM name>' written to tblcallevents
 *   - assignedto_id = target BD uid, assignedto_by = current CM/RM uid
 *   - approved_status = 1 (pre-approved when assigned by line manager)
 *   - Cluster pre-check (BD must have cluster_id, else block submit)
 *   - Rs 500 wallet deduction warning for actiontype 4 (Barg in Meeting)
 *   - Day shape lock honored (WFO blocks 3/4; auto band 1500-1730 only 1,2,13)
 *
 * 4-step cascade:
 *   1) Pick target BD (from GetTotalTeam under current line-manager uid)
 *   2) Pick lead (filtered by production category if chosen)
 *   3) Pick action + purpose + time + date
 *   4) Review + confirm
 *
 * Surfaces the assigned task on the BD planner via Menu_model::GetTommrowAssignedTask.
 *
 * API contract (backed by stem_planner_v2_assign_endpoint_php.php):
 *   GET  /api/planner/v2/team                                   - returns team under current CM/RM
 *   GET  /api/planner/v2/filter_leads?bd_uid=&optradio=         - filtered leads under target BD
 *   GET  /api/planner/v2/wallet?bd_uid=                         - returns BD wallet balance
 *   GET  /api/planner/v2/purposes?action_id=                    - cascade purposes for action
 *   POST /api/planner/v2/assign                                 - wraps Menu/dailyTaskAssign
 *
 * Line manager type_ids that may access: 4 (CM), 13 (CM type 2), 19, 20, 21, 22, 23, 24.
 *
 * Last updated: rev 7 (2026-05-16)
 */

import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  TextInput, Alert, ActivityIndicator
} from 'react-native';

const ACTIONS = [
  { id: 1,  name: 'Call' },
  { id: 2,  name: 'Email' },
  { id: 3,  name: 'Scheduled Meeting' },
  { id: 4,  name: 'Barg in Meeting' },
  { id: 5,  name: 'WhatsApp' },
  { id: 6,  name: 'Write MOM' },
  { id: 7,  name: 'Write Proposal' },
  { id: 10, name: 'Research' },
  { id: 11, name: 'documentation' },
  { id: 12, name: 'Review' },
  { id: 13, name: 'CM Approval' },
];

const FILTER_OPTIONS = [
  '',
  'Mandatory Task',
  'Compulsive Task',
  'Need Your Attention',
  'Emergency Meetings Task',
  'Future Task',
  'Same Status Last Limit Days',
  'Plan But Not Initiated',
  'No Calling Done After Only Got Details',
  'Closing Timeline',
  'Cluster Location',
  'Compnay Name',
  'actionNotPlanned',
];

const CSTATUS = [
  { id: 1, name: 'Open' },
  { id: 2, name: 'Reachout' },
  { id: 3, name: 'Tentative' },
  { id: 6, name: 'Positive' },
  { id: 7, name: 'Proposal Sent' },
  { id: 8, name: 'Open RPEM' },
  { id: 9, name: 'Very Positive' },
  { id: 12, name: 'Won' },
  { id: 13, name: 'Lost' },
];

const BAND_LOCK_HINT = {
  wfo_blocks_physical_meeting: 'WFO blocks Scheduled Meeting and Barg in Meeting',
  auto_band_only_calls_emails_mom_allowed: 'Auto band 1500 to 1730 only allows Call, Email, MoM',
  plan_window_no_field_activity: 'Plan window 1730 to 1830 blocks all task adds',
  out_of_band: 'Outside operating hours',
};

function resolveBand(hhmm) {
  if (!hhmm) return null;
  const [hh, mm] = hhmm.split(':').map(n => parseInt(n, 10) || 0);
  const m = hh * 60 + mm;
  if (m >= 600 && m < 900)   return 'manual';
  if (m >= 900 && m < 1050)  return 'auto';
  if (m >= 1050 && m < 1110) return 'plan_window';
  return 'closed';
}
function checkLock(time, actId, bdMode) {
  const at = parseInt(actId, 10);
  if (!at) return { allowed: true };
  if (bdMode === 'wfo' && (at === 3 || at === 4)) {
    return { allowed: false, reason: 'wfo_blocks_physical_meeting' };
  }
  const band = resolveBand(time);
  if (band === 'manual') return { allowed: true };
  if (band === 'auto')   return [1, 2, 13].indexOf(at) !== -1
                              ? { allowed: true }
                              : { allowed: false, reason: 'auto_band_only_calls_emails_mom_allowed' };
  if (band === 'plan_window') return { allowed: false, reason: 'plan_window_no_field_activity' };
  return { allowed: false, reason: 'out_of_band' };
}

export default function CMAssignTaskV2Screen({ navigation, route }) {
  const [step, setStep] = useState(1);
  const [loading, setLoading] = useState(false);

  // Step 1 - BD picker
  const [team, setTeam] = useState([]);                    // [{user_id, user_name, cluster_id, ucash}]
  const [selectedBd, setSelectedBd] = useState(null);

  // Step 2 - Lead picker
  const [filter, setFilter] = useState('');
  const [leads, setLeads] = useState([]);                  // [{id, cname, cstatus, cstatus_name}]
  const [selectedLead, setSelectedLead] = useState(null);

  // Step 3 - action/purpose/time
  const [actionId, setActionId] = useState('');
  const [purposes, setPurposes] = useState([]);            // [{id, name}]
  const [purposeId, setPurposeId] = useState('');
  // rev 8: cascade response flags
  const [fallbackUsed, setFallbackUsed]     = useState(false);
  const [bargeRewritten, setBargeRewritten] = useState(false);
  const [planDate, setPlanDate] = useState(tomorrowISO());
  const [planTime, setPlanTime] = useState('11:00');
  const [targetStatus, setTargetStatus] = useState('');
  const [targetDate, setTargetDate]   = useState(plusDaysISO(7));
  const [starRating, setStarRating]   = useState(3);

  // -- Derived banners
  const lock = checkLock(planTime, actionId, selectedBd ? selectedBd.work_mode : 'wfh');
  const walletLow = selectedBd && (parseInt(selectedBd.ucash, 10) || 0) < 500 && parseInt(actionId, 10) === 4;
  const clusterMissing = selectedBd && !selectedBd.cluster_id;

  // -- Load team on mount
  useEffect(() => { loadTeam(); }, []);
  async function loadTeam() {
    setLoading(true);
    try {
      const r = await fetch('/api/planner/v2/team');
      const d = await r.json();
      setTeam(d.team || []);
    } catch (e) {
      Alert.alert('Team load failed', String(e));
    }
    setLoading(false);
  }

  // -- Load leads when BD or filter changes
  useEffect(() => {
    if (!selectedBd) return;
    (async () => {
      setLoading(true);
      try {
        const qs = `bd_uid=${selectedBd.user_id}` + (filter ? `&optradio=${encodeURIComponent(filter)}` : '');
        const r = await fetch(`/api/planner/v2/filter_leads?${qs}`);
        const d = await r.json();
        setLeads(d.leads || []);
      } catch (e) {
        setLeads([]);
      }
      setLoading(false);
    })();
  }, [selectedBd, filter]);

  // -- rev 8: Load purposes via production-parity cascade endpoint when
  // action, lead, or filter changes. Mirrors all 5 production cascade methods
  // plus the 3 selectby branches plus the Fresh Meeting (id 34) fallback.
  // CM Assign sets apply_barge_rewrite=0 (no silent Barge rewrite).
  useEffect(() => {
    if (!actionId) {
      setPurposes([]); setPurposeId('');
      setFallbackUsed(false); setBargeRewritten(false);
      return;
    }
    (async () => {
      try {
        const params = new URLSearchParams({
          action_id: String(actionId),
          inid: selectedLead ? String(selectedLead.id) : '',
          cstatus: selectedLead ? String(selectedLead.cstatus || 0) : '',
          selectby: filter || '',
          apply_barge_rewrite: '0',
        });
        const r = await fetch(`/api/planner/v2/purposes_v2?${params.toString()}`);
        const d = await r.json();
        let list = (d && d.rows) || [];
        if (!Array.isArray(list) || list.length === 0) {
          list = [{ id: 34, name: 'Fresh Meeting' }];
        }
        setPurposes(list);
        setFallbackUsed(!!(d && d.fallback_used));
        setBargeRewritten(!!(d && d.barge_rewritten));
      } catch (e) {
        setPurposes([{ id: 34, name: 'Fresh Meeting' }]);
        setFallbackUsed(true);
        setBargeRewritten(false);
      }
    })();
  }, [actionId, selectedLead, filter]);

  async function submitAssign() {
    if (clusterMissing) {
      Alert.alert('Cluster missing', 'Target BD has no cluster set. Production blocks this assign.');
      return;
    }
    if (!lock.allowed) {
      Alert.alert('Action blocked', BAND_LOCK_HINT[lock.reason] || lock.reason);
      return;
    }
    if (walletLow) {
      Alert.alert('Wallet low',
        'Target BD has under Rs 500 in wallet. Barg in Meeting will be rejected by production.');
      return;
    }
    if (!selectedLead || !actionId || !purposeId || !targetStatus) {
      Alert.alert('Missing fields', 'Pick lead, action, purpose, and target status before submitting.');
      return;
    }
    setLoading(true);
    try {
      const body = new URLSearchParams({
        user: String(selectedBd.user_id),
        'company[]': String(selectedLead.id),
        plandate: planDate,
        tasktimeplan: planTime,
        atask: String(actionId),
        current_status: String(selectedLead.cstatus || ''),
        targetstatus: String(targetStatus),
        targetDate: targetDate,
        ntppose: String(purposeId),
        star_rating: String(starRating),
      });
      const r = await fetch('/api/planner/v2/assign', { method: 'POST', body });
      const d = await r.json();
      if (!r.ok || d.status !== 'ok') {
        Alert.alert('Assign failed', d.message || ('http ' + r.status));
      } else {
        Alert.alert('Assigned',
          `Task assigned to ${selectedBd.user_name}.\nIt will surface on the BD planner for ${planDate}.`);
        navigation.goBack();
      }
    } catch (e) {
      Alert.alert('Network error', String(e));
    }
    setLoading(false);
  }

  // ---------------------------------------------------------------------------
  // Render
  // ---------------------------------------------------------------------------
  return (
    <ScrollView style={s.root} contentContainerStyle={{ paddingBottom: 50 }}>
      <View style={s.header}>
        <Text style={s.headerTitle}>Assign Task to BD</Text>
        <Text style={s.headerSubtitle}>rev 7 - line-manager surface</Text>
      </View>

      <Stepper step={step} />

      {clusterMissing && (
        <View style={s.danger}>
          <Text style={s.dangerText}>
            Target BD has no cluster set. Production blocks assign without cluster.
            Set cluster in Team admin first.
          </Text>
        </View>
      )}
      {walletLow && (
        <View style={s.warn}>
          <Text style={s.warnText}>
            Target BD wallet is under Rs 500. Barg in Meeting (actiontype 4) will be rejected.
          </Text>
        </View>
      )}
      {!lock.allowed && (
        <View style={s.danger}>
          <Text style={s.dangerText}>Day shape lock: {BAND_LOCK_HINT[lock.reason] || lock.reason}</Text>
        </View>
      )}

      {step === 1 && (
        <View style={s.section}>
          <Text style={s.sectionLabel}>Step 1 - Pick target BD</Text>
          {loading && <ActivityIndicator size="small" />}
          {team.map(m => (
            <TouchableOpacity
              key={m.user_id}
              style={[s.row, selectedBd && selectedBd.user_id === m.user_id && s.rowOn]}
              onPress={() => setSelectedBd(m)}
            >
              <View style={{ flex: 1 }}>
                <Text style={s.rowTitle}>{m.user_name} (uid {m.user_id})</Text>
                <Text style={s.rowMeta}>
                  Cluster {m.cluster_id || 'NONE'} . Wallet Rs {m.ucash || 0} . Mode {m.work_mode || 'wfh'}
                </Text>
              </View>
            </TouchableOpacity>
          ))}
          <PrimaryBtn label="Next" disabled={!selectedBd} onPress={() => setStep(2)} />
        </View>
      )}

      {step === 2 && (
        <View style={s.section}>
          <Text style={s.sectionLabel}>Step 2 - Pick lead</Text>
          <Text style={s.metaLine}>BD: {selectedBd.user_name}</Text>

          <Text style={s.smallLabel}>Filter category (production parity)</Text>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={{ marginVertical: 6 }}>
            {FILTER_OPTIONS.map(opt => (
              <TouchableOpacity
                key={opt || 'all'}
                style={[s.chip, filter === opt && s.chipOn]}
                onPress={() => setFilter(opt)}
              >
                <Text style={[s.chipText, filter === opt && s.chipTextOn]}>{opt || 'All leads'}</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>

          {loading && <ActivityIndicator size="small" />}
          {!loading && leads.length === 0 && (
            <Text style={s.metaLine}>No leads under this filter for this BD.</Text>
          )}
          {leads.map(l => (
            <TouchableOpacity
              key={l.id}
              style={[s.row, selectedLead && selectedLead.id === l.id && s.rowOn]}
              onPress={() => setSelectedLead(l)}
            >
              <View style={{ flex: 1 }}>
                <Text style={s.rowTitle}>{l.cname || 'Unknown'}</Text>
                <Text style={s.rowMeta}>
                  Lead id {l.id} . cstatus {l.cstatus} ({l.cstatus_name || '?'}) . fbudget Rs {l.fbudget || 0}
                </Text>
              </View>
            </TouchableOpacity>
          ))}

          <View style={s.btnRow}>
            <SecondaryBtn label="Back" onPress={() => setStep(1)} />
            <PrimaryBtn label="Next" disabled={!selectedLead} onPress={() => setStep(3)} />
          </View>
        </View>
      )}

      {step === 3 && (
        <View style={s.section}>
          <Text style={s.sectionLabel}>Step 3 - Action and time</Text>
          <Text style={s.metaLine}>{selectedBd.user_name} . {selectedLead.cname}</Text>

          <Text style={s.smallLabel}>Action</Text>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={{ marginVertical: 6 }}>
            {ACTIONS.map(a => (
              <TouchableOpacity
                key={a.id}
                style={[s.chip, parseInt(actionId, 10) === a.id && s.chipOn]}
                onPress={() => setActionId(String(a.id))}
              >
                <Text style={[s.chipText, parseInt(actionId, 10) === a.id && s.chipTextOn]}>
                  {a.id} {a.name}
                </Text>
              </TouchableOpacity>
            ))}
          </ScrollView>

          <Text style={s.smallLabel}>Purpose</Text>
          {purposes.length === 0 && <Text style={s.metaLine}>Pick action first.</Text>}
          {fallbackUsed && (
            <Text style={s.cascadeHint}>
              No purposes for this action and lead status. Showing Fresh Meeting (production default).
            </Text>
          )}
          {bargeRewritten && (
            <Text style={s.cascadeHint}>
              Barge in this stage is treated as Scheduled Meeting (production rule).
            </Text>
          )}
          <View style={{ flexDirection: 'row', flexWrap: 'wrap' }}>
            {purposes.map(p => (
              <TouchableOpacity
                key={p.id}
                style={[s.chip, parseInt(purposeId, 10) === p.id && s.chipOn]}
                onPress={() => setPurposeId(String(p.id))}
              >
                <Text style={[s.chipText, parseInt(purposeId, 10) === p.id && s.chipTextOn]}>
                  {p.id} {p.name}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          <Text style={s.smallLabel}>Plan date (YYYY-MM-DD)</Text>
          <TextInput value={planDate} onChangeText={setPlanDate} style={s.input} placeholder="YYYY-MM-DD" />
          <Text style={s.smallLabel}>Plan time (HH:MM, 24h)</Text>
          <TextInput value={planTime} onChangeText={setPlanTime} style={s.input} placeholder="HH:MM" />

          <Text style={s.smallLabel}>Target status</Text>
          <View style={{ flexDirection: 'row', flexWrap: 'wrap' }}>
            {CSTATUS.map(c => (
              <TouchableOpacity
                key={c.id}
                style={[s.chip, parseInt(targetStatus, 10) === c.id && s.chipOn]}
                onPress={() => setTargetStatus(String(c.id))}
              >
                <Text style={[s.chipText, parseInt(targetStatus, 10) === c.id && s.chipTextOn]}>
                  {c.id} {c.name}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          <Text style={s.smallLabel}>Target review date</Text>
          <TextInput value={targetDate} onChangeText={setTargetDate} style={s.input} placeholder="YYYY-MM-DD" />

          <Text style={s.smallLabel}>Star rating (1-5)</Text>
          <View style={{ flexDirection: 'row' }}>
            {[1,2,3,4,5].map(n => (
              <TouchableOpacity
                key={n}
                style={[s.chip, starRating === n && s.chipOn]}
                onPress={() => setStarRating(n)}
              >
                <Text style={[s.chipText, starRating === n && s.chipTextOn]}>{n}</Text>
              </TouchableOpacity>
            ))}
          </View>

          <View style={s.btnRow}>
            <SecondaryBtn label="Back" onPress={() => setStep(2)} />
            <PrimaryBtn
              label="Review"
              disabled={!actionId || !purposeId || !targetStatus || !lock.allowed}
              onPress={() => setStep(4)}
            />
          </View>
        </View>
      )}

      {step === 4 && (
        <View style={s.section}>
          <Text style={s.sectionLabel}>Step 4 - Review and assign</Text>
          <View style={s.reviewCard}>
            <ReviewRow k="Target BD"     v={`${selectedBd.user_name} (uid ${selectedBd.user_id})`} />
            <ReviewRow k="Lead"          v={`${selectedLead.cname} (id ${selectedLead.id})`} />
            <ReviewRow k="Current cstatus" v={`${selectedLead.cstatus} (${selectedLead.cstatus_name || '?'})`} />
            <ReviewRow k="Action"        v={`${actionId} - ${ACTIONS.find(a => a.id === parseInt(actionId,10))?.name}`} />
            <ReviewRow k="Purpose"       v={`${purposeId} - ${purposes.find(p => p.id === parseInt(purposeId,10))?.name || ''}`} />
            <ReviewRow k="Plan date"     v={planDate} />
            <ReviewRow k="Plan time"     v={planTime} />
            <ReviewRow k="Target status" v={`${targetStatus} - ${CSTATUS.find(c => c.id === parseInt(targetStatus,10))?.name || ''}`} />
            <ReviewRow k="Target review" v={targetDate} />
            <ReviewRow k="Star rating"   v={String(starRating)} />
            <ReviewRow k="Wallet check"  v={walletLow ? 'BLOCKED (under Rs 500)' : 'OK'} />
            <ReviewRow k="Day shape"     v={lock.allowed ? 'allowed' : 'BLOCKED: ' + lock.reason} />
          </View>
          <View style={s.btnRow}>
            <SecondaryBtn label="Back" onPress={() => setStep(3)} />
            <PrimaryBtn
              label={loading ? 'Submitting...' : 'Assign Task'}
              disabled={loading || clusterMissing || !lock.allowed || walletLow}
              onPress={submitAssign}
            />
          </View>
          <Text style={[s.metaLine, { marginTop: 12 }]}>
            Posts to /api/planner/v2/assign which wraps Menu/dailyTaskAssign.
            Will surface on the BD planner for {planDate} via GetTommrowAssignedTask.
          </Text>
        </View>
      )}
    </ScrollView>
  );
}

// -----------------------------------------------------------------------------
// Sub components
// -----------------------------------------------------------------------------
function Stepper({ step }) {
  const labels = ['BD', 'Lead', 'Action', 'Review'];
  return (
    <View style={s.stepper}>
      {labels.map((l, i) => {
        const n = i + 1;
        const active = step === n, done = step > n;
        return (
          <View key={l} style={s.stepWrap}>
            <View style={[s.stepDot, active && s.stepDotActive, done && s.stepDotDone]}>
              <Text style={[s.stepNum, (active || done) && { color: '#ffffff' }]}>{n}</Text>
            </View>
            <Text style={[s.stepLabel, active && s.stepLabelActive]}>{l}</Text>
            {n < 4 && <View style={[s.stepLine, done && s.stepLineDone]} />}
          </View>
        );
      })}
    </View>
  );
}

function PrimaryBtn({ label, disabled, onPress }) {
  return (
    <TouchableOpacity style={[s.primaryBtn, disabled && s.btnDisabled]} disabled={disabled} onPress={onPress}>
      <Text style={s.primaryBtnText}>{label}</Text>
    </TouchableOpacity>
  );
}
function SecondaryBtn({ label, onPress }) {
  return (
    <TouchableOpacity style={s.secondaryBtn} onPress={onPress}>
      <Text style={s.secondaryBtnText}>{label}</Text>
    </TouchableOpacity>
  );
}
function ReviewRow({ k, v }) {
  return (
    <View style={s.reviewRow}>
      <Text style={s.reviewK}>{k}</Text>
      <Text style={s.reviewV}>{v}</Text>
    </View>
  );
}

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------
function tomorrowISO() {
  const d = new Date(); d.setDate(d.getDate() + 1);
  return d.toISOString().slice(0, 10);
}
function plusDaysISO(n) {
  const d = new Date(); d.setDate(d.getDate() + n);
  return d.toISOString().slice(0, 10);
}

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#ffffff' },
  header: { padding: 14, borderBottomWidth: 1, borderColor: '#eaeef2' },
  headerTitle: { fontSize: 17, fontWeight: '700', color: '#1f2328' },
  headerSubtitle: { fontSize: 11, color: '#57606a', marginTop: 2 },

  stepper: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-around', padding: 12, backgroundColor: '#f6f8fa' },
  stepWrap: { flexDirection: 'row', alignItems: 'center' },
  stepDot: { width: 26, height: 26, borderRadius: 13, backgroundColor: '#d0d7de', justifyContent: 'center', alignItems: 'center' },
  stepDotActive: { backgroundColor: '#0969da' },
  stepDotDone:   { backgroundColor: '#2da44e' },
  stepNum: { color: '#57606a', fontSize: 12, fontWeight: '700' },
  stepLabel: { marginLeft: 6, fontSize: 11, color: '#57606a' },
  stepLabelActive: { color: '#1f2328', fontWeight: '600' },
  stepLine: { width: 18, height: 2, backgroundColor: '#d0d7de', marginHorizontal: 6 },
  stepLineDone: { backgroundColor: '#2da44e' },

  section: { padding: 14 },
  sectionLabel: { fontSize: 13, fontWeight: '700', color: '#1f2328', marginBottom: 8 },
  smallLabel: { fontSize: 11, fontWeight: '600', color: '#57606a', textTransform: 'uppercase', marginTop: 10, marginBottom: 2 },
  metaLine: { fontSize: 12, color: '#57606a', marginVertical: 2 },
  cascadeHint: { padding: 6, marginVertical: 4, backgroundColor: '#fff8c5', borderRadius: 4, fontSize: 11, color: '#7a5a00' },

  row: { padding: 12, borderWidth: 1, borderColor: '#d0d7de', borderRadius: 8, marginBottom: 6, backgroundColor: '#ffffff' },
  rowOn: { borderColor: '#0969da', backgroundColor: '#ddf4ff' },
  rowTitle: { fontSize: 14, fontWeight: '600', color: '#1f2328' },
  rowMeta:  { fontSize: 11, color: '#57606a', marginTop: 2 },

  chip: { paddingHorizontal: 10, paddingVertical: 6, marginRight: 6, marginBottom: 6,
          backgroundColor: '#f6f8fa', borderColor: '#d0d7de', borderWidth: 1, borderRadius: 16 },
  chipOn: { backgroundColor: '#0969da', borderColor: '#0969da' },
  chipText: { fontSize: 12, color: '#1f2328' },
  chipTextOn: { color: '#ffffff', fontWeight: '600' },

  input: { borderWidth: 1, borderColor: '#d0d7de', borderRadius: 6, padding: 8, fontSize: 14, marginBottom: 4, backgroundColor: '#ffffff' },

  btnRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 14 },
  primaryBtn: { backgroundColor: '#0969da', paddingHorizontal: 22, paddingVertical: 10, borderRadius: 6, flex: 1, marginLeft: 6 },
  primaryBtnText: { color: '#ffffff', fontWeight: '700', textAlign: 'center' },
  secondaryBtn: { backgroundColor: '#eaeef2', paddingHorizontal: 22, paddingVertical: 10, borderRadius: 6, flex: 1, marginRight: 6 },
  secondaryBtnText: { color: '#1f2328', fontWeight: '600', textAlign: 'center' },
  btnDisabled: { opacity: 0.45 },

  warn: { backgroundColor: '#fff8c5', borderColor: '#d4a72c', borderWidth: 1, padding: 10, margin: 10, borderRadius: 6 },
  warnText: { fontSize: 12, color: '#7d4e00' },
  danger: { backgroundColor: '#ffd0d2', borderColor: '#cf222e', borderWidth: 1, padding: 10, margin: 10, borderRadius: 6 },
  dangerText: { fontSize: 12, color: '#82071e', fontWeight: '600' },

  reviewCard: { backgroundColor: '#f6f8fa', borderWidth: 1, borderColor: '#d0d7de', borderRadius: 8, padding: 12 },
  reviewRow: { flexDirection: 'row', paddingVertical: 5, borderBottomWidth: 1, borderColor: '#eaeef2' },
  reviewK: { width: 110, fontSize: 11, color: '#57606a', fontWeight: '600' },
  reviewV: { flex: 1, fontSize: 12, color: '#1f2328' },
});
