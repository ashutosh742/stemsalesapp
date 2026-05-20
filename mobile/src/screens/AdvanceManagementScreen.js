// AdvanceManagementScreen.js
// Travel advance lifecycle for a BD (and approvers).
//
// Three tabs, gated by role:
//   - BD view:        My advances + new request button + consume/return actions
//   - CM view:        Cluster approval queue (cluster_apr stage)
//   - Admin view:     Admin approval queue (admin_apr stage)
//   - Accounts view:  Final disbursement queue (account_apr stage, type_id=27)
//
// Endpoints (migration 009 advance/* surface):
//   GET  /api/discipline/advance/my?status=&days=
//   GET  /api/discipline/advance/queue?role=cluster|admin|account
//   POST /api/discipline/advance/request   {event_id, amount, purpose}
//   POST /api/discipline/advance/approve   {advance_id, role, action, remarks}
//   POST /api/discipline/advance/consume   {advance_id, actual_spent}
//   POST /api/discipline/advance/return    {advance_id, reason}
//
// Production schema reused: travel_advance.cluster_apr/admin_apr/account_apr
//   0=pending, 1=approved, 2=rejected, 3=suspect
// Migration 009 added: consumed_status, linked_event_id, linked_cancellation_event_id.

import React, { useState, useEffect, useMemo } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, TextInput,
  ActivityIndicator, Alert, RefreshControl, Modal,
} from 'react-native';
import {
  getMyAdvances, requestAdvance, consumeAdvance, returnAdvance,
  getAdvanceQueue, approveAdvance,
} from '../lib/api';
import { CURRENT_USER } from '../data/roles';

const COLORS = {
  bg: '#f6f7f9', card: '#fff', line: '#e5e9f2', text: '#0F172A',
  primary: '#1f5fbf', red: '#d0021b', amber: '#f6a623', green: '#1f9d55',
  blue: '#1E90FF', muted: '#64748B', tint: '#eef3fb',
};

// Stage labels we render. Backend returns these as approval_stage.
const STAGE_META = {
  awaiting_cm:       { label: 'Awaiting CM',       color: COLORS.amber },
  awaiting_admin:    { label: 'Awaiting Admin',    color: COLORS.amber },
  awaiting_accounts: { label: 'Awaiting Accounts', color: COLORS.blue  },
  approved:          { label: 'Approved',          color: COLORS.green },
  rejected:          { label: 'Rejected',          color: COLORS.red   },
};

const CONSUMED_META = {
  pending:   { label: 'Open',     color: COLORS.amber },
  consumed:  { label: 'Consumed', color: COLORS.muted },
  returned:  { label: 'Returned', color: COLORS.green },
  rolled:    { label: 'Rolled',   color: COLORS.blue  },
  absorbed:  { label: 'Absorbed', color: COLORS.muted },
};

export default function AdvanceManagementScreen() {
  const user = CURRENT_USER || {};
  // Determine which tabs to expose. BDs only see 'my'. CM sees my+cluster. AO sees my+account.
  const tabs = useMemo(() => {
    const out = [{ key: 'my', label: 'My advances' }];
    if (user.role === 'cm' || user.role === 'manager') out.push({ key: 'cluster', label: 'CM queue' });
    if (user.type_id === 27 || user.role === 'accounts') out.push({ key: 'account', label: 'AO queue' });
    if (user.role === 'admin' || user.role === 'rm')     out.push({ key: 'admin',   label: 'Admin queue' });
    return out;
  }, [user]);
  const [tab, setTab] = useState('my');

  return (
    <View style={s.root}>
      <View style={s.header}>
        <Text style={s.title}>Advance management</Text>
        <Text style={s.sub}>Track every rupee from request to return.</Text>
      </View>
      <View style={s.tabbar}>
        {tabs.map(t => (
          <TouchableOpacity key={t.key} onPress={() => setTab(t.key)}
            style={[s.tab, tab === t.key && s.tabActive]}>
            <Text style={[s.tabText, tab === t.key && s.tabTextActive]}>{t.label}</Text>
          </TouchableOpacity>
        ))}
      </View>
      {tab === 'my'      && <MyAdvances />}
      {tab === 'cluster' && <ApprovalQueue role="cluster" />}
      {tab === 'admin'   && <ApprovalQueue role="admin"   />}
      {tab === 'account' && <ApprovalQueue role="account" />}
    </View>
  );
}

// ──────────────────────────────────────────────────────────────────
// BD: my advances + request modal
// ──────────────────────────────────────────────────────────────────
function MyAdvances() {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [filter, setFilter] = useState('all');
  const [requestOpen, setRequestOpen] = useState(false);
  const [actionOn, setActionOn] = useState(null); // {row, mode: 'consume'|'return'}
  const [error, setError] = useState('');

  const load = async (status = filter) => {
    setError('');
    try {
      const r = await getMyAdvances(status, 30);
      setRows(r?.rows || []);
    } catch (e) {
      setError(e.message || 'Unable to load');
      setRows([]);
    } finally { setLoading(false); setRefreshing(false); }
  };
  useEffect(() => { setLoading(true); load(filter); }, [filter]);

  const filters = [
    { key: 'all', label: 'All' },
    { key: 'pending', label: 'Pending' },
    { key: 'open', label: 'Open' },
    { key: 'closed', label: 'Closed' },
  ];

  if (loading) return <ActivityIndicator size="large" style={{ marginTop: 40 }} />;

  return (
    <ScrollView
      style={s.body}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      <View style={s.filterRow}>
        {filters.map(f => (
          <TouchableOpacity key={f.key} onPress={() => setFilter(f.key)}
            style={[s.chip, filter === f.key && s.chipActive]}>
            <Text style={[s.chipText, filter === f.key && s.chipTextActive]}>{f.label}</Text>
          </TouchableOpacity>
        ))}
      </View>

      <TouchableOpacity style={s.primaryBtn} onPress={() => setRequestOpen(true)}>
        <Text style={s.primaryBtnText}>+ Request new advance</Text>
      </TouchableOpacity>

      {error ? <Text style={s.error}>{error}</Text> : null}
      {!error && rows.length === 0 && (
        <View style={s.emptyCard}>
          <Text style={s.emptyTitle}>No advances yet</Text>
          <Text style={s.emptySub}>Raise one against a planned meeting to get a cash advance.</Text>
        </View>
      )}

      {rows.map(r => <AdvanceCard key={r.id} row={r} onAction={(mode) => setActionOn({ row: r, mode })} />)}

      <View style={{ height: 40 }} />

      <RequestAdvanceModal
        visible={requestOpen}
        onClose={() => setRequestOpen(false)}
        onDone={() => { setRequestOpen(false); load(); }}
      />
      <ActionModal
        action={actionOn}
        onClose={() => setActionOn(null)}
        onDone={() => { setActionOn(null); load(); }}
      />
    </ScrollView>
  );
}

function AdvanceCard({ row, onAction }) {
  const stage = STAGE_META[row.approval_stage] || { label: row.approval_stage, color: COLORS.muted };
  const consumed = CONSUMED_META[row.consumed_status] || null;
  const meetingDate = row.meeting_at ? String(row.meeting_at).slice(0, 10) : null;
  const canConsume = row.approval_stage === 'approved' && row.consumed_status === 'pending';
  const canReturn  = row.approval_stage === 'approved' && row.consumed_status === 'pending';

  return (
    <View style={s.card}>
      <View style={s.cardHead}>
        <Text style={s.amount}>Rs {Number(row.cash || row.amount).toLocaleString('en-IN')}</Text>
        <View style={[s.pill, { backgroundColor: stage.color + '22', borderColor: stage.color }]}>
          <Text style={[s.pillText, { color: stage.color }]}>{stage.label}</Text>
        </View>
      </View>
      <Text style={s.cardSub}>{row.purpose || 'Meeting advance'}</Text>

      <View style={s.metaRow}>
        <Meta k="Advance #"    v={`#${row.id}`} />
        <Meta k="Meeting"      v={row.meeting_subject ? row.meeting_subject.slice(0, 24) : 'No event'} />
        {meetingDate && <Meta k="Date" v={meetingDate} />}
      </View>

      {/* Approval ladder */}
      <View style={s.ladder}>
        <LadderStep label="CM"       state={row.cluster_apr} by={row.cluster_remarks} />
        <LadderStep label="Admin"    state={row.admin_apr}   by={row.admin_remarks}   />
        <LadderStep label="Accounts" state={row.account_apr} by={row.account_remarks} />
      </View>

      {consumed && (
        <Text style={[s.consumedTag, { color: consumed.color }]}>
          Status: {consumed.label}
          {row.actual_spent != null && row.consumed_status === 'consumed'
            ? `  ·  Spent Rs ${Number(row.actual_spent).toLocaleString('en-IN')}`
            : ''}
          {row.leftover_returned > 0
            ? `  ·  Refund Rs ${Number(row.leftover_returned).toLocaleString('en-IN')}`
            : ''}
        </Text>
      )}

      {row.cancelled_at && (
        <View style={s.warnBox}>
          <Text style={s.warnText}>
            Meeting cancelled ({row.cancellation_category || 'reason unknown'}). Settle this advance.
          </Text>
        </View>
      )}

      {(canConsume || canReturn) && (
        <View style={s.actionRow}>
          {canConsume && (
            <TouchableOpacity style={s.secondaryBtn} onPress={() => onAction('consume')}>
              <Text style={s.secondaryBtnText}>Mark consumed</Text>
            </TouchableOpacity>
          )}
          {canReturn && (
            <TouchableOpacity style={[s.secondaryBtn, { backgroundColor: '#fff5e6', borderColor: COLORS.amber }]}
              onPress={() => onAction('return')}>
              <Text style={[s.secondaryBtnText, { color: COLORS.amber }]}>Return full</Text>
            </TouchableOpacity>
          )}
        </View>
      )}
    </View>
  );
}

function LadderStep({ label, state, by }) {
  const map = { 0: COLORS.muted, 1: COLORS.green, 2: COLORS.red, 3: COLORS.amber };
  const symbol = { 0: '·', 1: '✓', 2: '✕', 3: '?' };
  const color = map[Number(state) ?? 0];
  return (
    <View style={s.ladderStep}>
      <View style={[s.ladderDot, { backgroundColor: color }]}>
        <Text style={s.ladderDotText}>{symbol[Number(state) ?? 0]}</Text>
      </View>
      <Text style={s.ladderLabel}>{label}</Text>
    </View>
  );
}

function Meta({ k, v }) {
  return (
    <View style={{ marginRight: 16 }}>
      <Text style={s.metaK}>{k}</Text>
      <Text style={s.metaV}>{v}</Text>
    </View>
  );
}

// ──────────────────────────────────────────────────────────────────
// Request modal
// ──────────────────────────────────────────────────────────────────
function RequestAdvanceModal({ visible, onClose, onDone }) {
  const [eventId, setEventId] = useState('');
  const [amount, setAmount] = useState('');
  const [purpose, setPurpose] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const submit = async () => {
    if (!eventId || !amount) return Alert.alert('Missing', 'Pick a meeting and enter amount.');
    setSubmitting(true);
    try {
      const r = await requestAdvance(Number(eventId), Number(amount), purpose);
      if (!r?.ok) {
        Alert.alert('Failed', r?.error === 'already_requested'
          ? 'You already raised an advance for this meeting (#' + r.advance_id + ').'
          : (r?.error || 'Unknown error'));
        return;
      }
      Alert.alert('Submitted', 'Advance #' + r.advance_id + ' sent to CM for approval.');
      setEventId(''); setAmount(''); setPurpose('');
      onDone();
    } catch (e) { Alert.alert('Network error', e.message); }
    finally { setSubmitting(false); }
  };

  return (
    <Modal visible={visible} animationType="slide" transparent>
      <View style={s.modalBg}>
        <View style={s.modalCard}>
          <Text style={s.h2}>New advance request</Text>
          <Text style={s.muted}>Link to a planned meeting. CM, Admin, then Accounts must approve before cash hits your wallet.</Text>

          <Text style={s.label}>Meeting (event id)</Text>
          <TextInput style={s.input} value={eventId} onChangeText={setEventId}
            keyboardType="number-pad" placeholder="e.g. 41281" />

          <Text style={s.label}>Amount (Rs)</Text>
          <TextInput style={s.input} value={amount} onChangeText={setAmount}
            keyboardType="number-pad" placeholder="e.g. 1500" />

          <Text style={s.label}>Purpose</Text>
          <TextInput style={[s.input, { height: 70 }]} value={purpose} onChangeText={setPurpose}
            multiline placeholder="e.g. Travel + sample kit for DPS Bandra demo" />

          <View style={s.modalActions}>
            <TouchableOpacity style={s.ghostBtn} onPress={onClose}>
              <Text style={s.ghostBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[s.primaryBtn, { flex: 1, marginLeft: 12 }]} onPress={submit} disabled={submitting}>
              <Text style={s.primaryBtnText}>{submitting ? 'Submitting...' : 'Submit request'}</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

// ──────────────────────────────────────────────────────────────────
// Consume / Return modal (one-shot)
// ──────────────────────────────────────────────────────────────────
function ActionModal({ action, onClose, onDone }) {
  const [val, setVal] = useState('');
  const [submitting, setSubmitting] = useState(false);
  useEffect(() => { setVal(''); }, [action]);
  if (!action) return null;
  const { row, mode } = action;

  const submit = async () => {
    setSubmitting(true);
    try {
      let r;
      if (mode === 'consume') {
        if (!val) return Alert.alert('Missing', 'Enter actual amount spent.');
        r = await consumeAdvance(row.id, Number(val));
      } else {
        r = await returnAdvance(row.id, val);
      }
      if (!r?.ok) return Alert.alert('Failed', r?.error || 'Unknown');
      if (mode === 'consume' && r.leftover_returned > 0) {
        Alert.alert('Done', `Rs ${r.leftover_returned} returned to your wallet.`);
      } else {
        Alert.alert('Done', mode === 'return' ? 'Advance returned to wallet.' : 'Advance closed.');
      }
      onDone();
    } catch (e) { Alert.alert('Network error', e.message); }
    finally { setSubmitting(false); }
  };

  return (
    <Modal visible={!!action} animationType="slide" transparent>
      <View style={s.modalBg}>
        <View style={s.modalCard}>
          <Text style={s.h2}>{mode === 'consume' ? 'Mark advance consumed' : 'Return advance'}</Text>
          <Text style={s.muted}>
            Advance #{row.id}  ·  Rs {Number(row.cash || row.amount).toLocaleString('en-IN')}
          </Text>
          {mode === 'consume' ? (
            <>
              <Text style={s.label}>Actual amount spent (Rs)</Text>
              <TextInput style={s.input} value={val} onChangeText={setVal}
                keyboardType="number-pad" placeholder="0" />
              <Text style={s.helper}>Any leftover is auto-credited to your wallet.</Text>
            </>
          ) : (
            <>
              <Text style={s.label}>Reason (optional)</Text>
              <TextInput style={[s.input, { height: 70 }]} value={val} onChangeText={setVal}
                multiline placeholder="e.g. Meeting moved to next week" />
              <Text style={s.helper}>Full Rs {Number(row.cash || row.amount).toLocaleString('en-IN')} goes back to your wallet.</Text>
            </>
          )}
          <View style={s.modalActions}>
            <TouchableOpacity style={s.ghostBtn} onPress={onClose}>
              <Text style={s.ghostBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[s.primaryBtn, { flex: 1, marginLeft: 12 }]} onPress={submit} disabled={submitting}>
              <Text style={s.primaryBtnText}>{submitting ? 'Saving...' : 'Confirm'}</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

// ──────────────────────────────────────────────────────────────────
// Approver: CM / Admin / Accounts queue
// ──────────────────────────────────────────────────────────────────
function ApprovalQueue({ role }) {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [active, setActive] = useState(null);
  const [remarks, setRemarks] = useState('');
  const [error, setError] = useState('');

  const load = async () => {
    setError('');
    try {
      const r = await getAdvanceQueue(role);
      setRows(r?.rows || []);
    } catch (e) {
      setError(e.message);
      if (e.status === 403 || (e.body && e.body.error === 'not_accounts_officer')) {
        setError('This queue is for Accounts Officers (type 27) only.');
      }
    } finally { setLoading(false); setRefreshing(false); }
  };
  useEffect(() => { setLoading(true); load(); }, [role]);

  const decide = async (action) => {
    try {
      const r = await approveAdvance(active.id, role, action, remarks);
      if (!r?.ok) return Alert.alert('Failed', r?.error || 'Unknown');
      Alert.alert(action === 1 ? 'Approved' : 'Rejected', `Advance #${active.id} updated.`);
      setActive(null); setRemarks(''); load();
    } catch (e) { Alert.alert('Network error', e.message); }
  };

  if (loading) return <ActivityIndicator size="large" style={{ marginTop: 40 }} />;
  if (error)   return <View style={s.center}><Text style={{ color: COLORS.red }}>{error}</Text></View>;

  if (active) {
    return (
      <ScrollView style={s.body}>
        <TouchableOpacity onPress={() => setActive(null)} style={s.backBtn}>
          <Text style={{ color: COLORS.primary }}>← Back to queue</Text>
        </TouchableOpacity>
        <View style={s.card}>
          <Text style={s.h2}>Advance #{active.id}</Text>
          <Text style={s.muted}>BD {active.bd_name}  ·  {active.aging_days}d old</Text>
          <Text style={s.label}>Amount</Text>
          <Text style={s.amount}>Rs {Number(active.cash || active.amount).toLocaleString('en-IN')}</Text>
          <Text style={s.label}>Purpose</Text>
          <Text style={s.value}>{active.purpose || '(none)'}</Text>
          <Text style={s.label}>Meeting</Text>
          <Text style={s.value}>{active.meeting_subject || 'No event linked'} {active.meeting_at ? '· ' + String(active.meeting_at).slice(0, 16) : ''}</Text>

          <Text style={s.label}>Remarks (optional)</Text>
          <TextInput style={[s.input, { height: 70 }]} value={remarks} onChangeText={setRemarks}
            multiline placeholder="Add a note for the BD" />

          <View style={s.actionRow}>
            <TouchableOpacity style={[s.secondaryBtn, { backgroundColor: '#ffeded', borderColor: COLORS.red }]}
              onPress={() => decide(2)}>
              <Text style={[s.secondaryBtnText, { color: COLORS.red }]}>Reject</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[s.primaryBtn, { flex: 1, marginLeft: 12 }]} onPress={() => decide(1)}>
              <Text style={s.primaryBtnText}>
                {role === 'account' ? 'Approve and disburse' : 'Approve'}
              </Text>
            </TouchableOpacity>
          </View>
          {role === 'account' && (
            <Text style={s.helper}>Approving here credits the BD wallet immediately.</Text>
          )}
        </View>
      </ScrollView>
    );
  }

  return (
    <ScrollView style={s.body}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}>
      <Text style={s.queueTitle}>
        {role === 'cluster' && 'CM approval queue'}
        {role === 'admin'   && 'Admin approval queue'}
        {role === 'account' && 'Accounts Officer disbursement queue'}
      </Text>
      <Text style={s.muted}>{rows.length} pending</Text>

      {rows.length === 0 && (
        <View style={s.emptyCard}>
          <Text style={s.emptyTitle}>Queue clear</Text>
          <Text style={s.emptySub}>No advances waiting on you right now.</Text>
        </View>
      )}

      {rows.map(r => (
        <TouchableOpacity key={r.id} style={s.card} onPress={() => setActive(r)}>
          <View style={s.cardHead}>
            <Text style={s.amount}>Rs {Number(r.cash || r.amount).toLocaleString('en-IN')}</Text>
            <Text style={s.muted}>{r.aging_days}d</Text>
          </View>
          <Text style={s.cardSub}>{r.bd_name}  ·  #{r.id}</Text>
          <Text style={s.muted}>{r.purpose || 'Meeting advance'}</Text>
          {r.meeting_subject && (
            <Text style={[s.muted, { marginTop: 4 }]}>Meeting: {r.meeting_subject}</Text>
          )}
        </TouchableOpacity>
      ))}
      <View style={{ height: 40 }} />
    </ScrollView>
  );
}

// ──────────────────────────────────────────────────────────────────
const s = StyleSheet.create({
  root:   { flex: 1, backgroundColor: COLORS.bg },
  header: { paddingHorizontal: 16, paddingTop: 16, paddingBottom: 8 },
  title:  { fontSize: 20, fontWeight: '700', color: COLORS.text },
  sub:    { fontSize: 13, color: COLORS.muted, marginTop: 2 },
  tabbar: { flexDirection: 'row', paddingHorizontal: 8, borderBottomWidth: 1, borderBottomColor: COLORS.line, backgroundColor: COLORS.card },
  tab:    { paddingHorizontal: 14, paddingVertical: 12 },
  tabActive: { borderBottomWidth: 2, borderBottomColor: COLORS.primary },
  tabText: { color: COLORS.muted, fontSize: 14 },
  tabTextActive: { color: COLORS.primary, fontWeight: '600' },
  body:   { flex: 1, paddingHorizontal: 12 },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 24 },

  filterRow: { flexDirection: 'row', marginTop: 12, marginBottom: 8, flexWrap: 'wrap' },
  chip:    { paddingHorizontal: 12, paddingVertical: 6, borderRadius: 16, backgroundColor: COLORS.card, borderWidth: 1, borderColor: COLORS.line, marginRight: 8, marginBottom: 8 },
  chipActive: { backgroundColor: COLORS.primary, borderColor: COLORS.primary },
  chipText: { color: COLORS.muted, fontSize: 13 },
  chipTextActive: { color: '#fff', fontWeight: '600' },

  primaryBtn: { backgroundColor: COLORS.primary, paddingVertical: 12, borderRadius: 10, alignItems: 'center', marginBottom: 12 },
  primaryBtnText: { color: '#fff', fontWeight: '600', fontSize: 14 },
  ghostBtn: { paddingVertical: 12, paddingHorizontal: 16, borderRadius: 10, borderWidth: 1, borderColor: COLORS.line, backgroundColor: COLORS.card },
  ghostBtnText: { color: COLORS.muted, fontSize: 14 },
  secondaryBtn: { paddingVertical: 10, paddingHorizontal: 14, borderRadius: 8, borderWidth: 1, borderColor: COLORS.primary, backgroundColor: COLORS.tint, marginRight: 8 },
  secondaryBtnText: { color: COLORS.primary, fontWeight: '600', fontSize: 13 },

  card: { backgroundColor: COLORS.card, borderRadius: 12, padding: 14, marginBottom: 10, borderWidth: 1, borderColor: COLORS.line },
  cardHead: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 },
  cardSub: { color: COLORS.text, fontSize: 14, marginTop: 2 },
  amount: { fontSize: 18, fontWeight: '700', color: COLORS.text },
  pill: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12, borderWidth: 1 },
  pillText: { fontSize: 11, fontWeight: '600' },

  metaRow: { flexDirection: 'row', flexWrap: 'wrap', marginTop: 10 },
  metaK: { fontSize: 11, color: COLORS.muted, textTransform: 'uppercase' },
  metaV: { fontSize: 13, color: COLORS.text, marginTop: 1 },

  ladder: { flexDirection: 'row', marginTop: 14, paddingTop: 10, borderTopWidth: 1, borderTopColor: COLORS.line },
  ladderStep: { flex: 1, alignItems: 'center' },
  ladderDot: { width: 22, height: 22, borderRadius: 11, alignItems: 'center', justifyContent: 'center' },
  ladderDotText: { color: '#fff', fontSize: 12, fontWeight: '700' },
  ladderLabel: { fontSize: 11, color: COLORS.muted, marginTop: 4 },

  consumedTag: { marginTop: 10, fontSize: 12, fontWeight: '600' },
  warnBox: { marginTop: 10, padding: 10, borderRadius: 8, backgroundColor: '#fff5e6', borderWidth: 1, borderColor: COLORS.amber },
  warnText: { color: '#7a5500', fontSize: 12 },
  actionRow: { flexDirection: 'row', marginTop: 14, alignItems: 'center' },

  error: { color: COLORS.red, padding: 12 },
  emptyCard: { padding: 24, alignItems: 'center', backgroundColor: COLORS.card, borderRadius: 12, borderWidth: 1, borderColor: COLORS.line, marginVertical: 12 },
  emptyTitle: { color: COLORS.text, fontWeight: '600', fontSize: 15 },
  emptySub: { color: COLORS.muted, fontSize: 13, marginTop: 4, textAlign: 'center' },

  queueTitle: { fontSize: 17, fontWeight: '700', color: COLORS.text, marginTop: 12 },
  backBtn: { paddingVertical: 12 },
  h2: { fontSize: 17, fontWeight: '700', color: COLORS.text, marginBottom: 6 },
  muted: { color: COLORS.muted, fontSize: 13 },
  label: { fontSize: 12, color: COLORS.muted, marginTop: 12, textTransform: 'uppercase' },
  value: { fontSize: 14, color: COLORS.text, marginTop: 2 },
  input: { marginTop: 6, borderWidth: 1, borderColor: COLORS.line, borderRadius: 8, paddingHorizontal: 12, paddingVertical: 10, fontSize: 14, color: COLORS.text, backgroundColor: '#fff' },
  helper: { fontSize: 12, color: COLORS.muted, marginTop: 6 },

  modalBg: { flex: 1, backgroundColor: 'rgba(15,23,42,0.5)', justifyContent: 'flex-end' },
  modalCard: { backgroundColor: '#fff', borderTopLeftRadius: 18, borderTopRightRadius: 18, padding: 18 },
  modalActions: { flexDirection: 'row', marginTop: 18, alignItems: 'center' },
});
