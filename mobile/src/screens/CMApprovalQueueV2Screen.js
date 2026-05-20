/**
 * CMApprovalQueueV2Screen.js
 *
 * Unified CM approval inbox for STEM CRM Mobile (CM role + AO type_id=27).
 *
 * Replaces the legacy PlanApprovalScreen one-shot plan approve flow with a
 * 3-type queue powered by daymanagementapprovalrequest.request_type (mig 017_3):
 *
 *   1. same_day_plan       - BD submitted today's plan today, needs CM nod
 *   2. meeting_delete      - BD wants to delete a planned tblcallevents row
 *                            (AO co-signs if cash_allot or advance attached)
 *   3. pending_task_carry  - BD wants to carry yesterday's miss into tomorrow
 *
 * Legacy plan_approval rows still render at the bottom so the CM doesn't lose
 * the existing 19:00 IST cutoff flow.
 *
 * API contract:
 *   GET  /api/planner/v2/cm_queue?cm_uid={uid}        - rows from v_planner_approval_queue
 *   GET  /api/planner/v2/cm_queue?group_by=bd         - rows from v_cm_queue_by_bd (mig 017_5)
 *   POST /api/planner/v2/resolve_request              - {request_id, decision, note}
 *                                                       -> sp_resolve_approval_request
 *   POST /api/planner/v2/bulk_resolve_carry           - {bd_uid, plan_date, action, reject_reason}
 *                                                       -> sp_bulk_resolve_carry (mig 017_5)
 *
 * Cutoff rules:
 *   - CM 19:00 IST same day for plan_approval (existing dmar.approval_by_cutoff)
 *   - same_day_plan: no cutoff (approve any time today)
 *   - meeting_delete: 24h SLA, otherwise dmar.approval_sla_breach_minutes ticks up
 *   - pending_task_carry: 24h SLA
 *
 * AO badge appears when requires_ao=1 and current user is type_id 27.
 *
 * Last updated: migration 017_3 wiring (2026-05-15)
 */

import React, { useState, useEffect, useMemo } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  Modal, TextInput, Alert, RefreshControl
} from 'react-native';

const REQUEST_TYPE_LABELS = {
  same_day_plan:      { label: 'Same-day plan',      color: '#cf222e', icon: 'SD' },
  meeting_delete:     { label: 'Meeting delete',     color: '#d4a72c', icon: 'MD' },
  pending_task_carry: { label: 'Pending task carry', color: '#0969da', icon: 'PT' },
  plan_approval:      { label: 'Plan approval',      color: '#57606a', icon: 'PA' },
};

export default function CMApprovalQueueV2Screen({ navigation, route }) {
  const [queue, setQueue]               = useState([]);
  const [bdGroups, setBdGroups]         = useState([]);
  const [loading, setLoading]           = useState(true);
  const [refreshing, setRefreshing]     = useState(false);
  const [actionModal, setActionModal]   = useState(null); // {req, decision}
  const [bulkModal, setBulkModal]       = useState(null); // {group, action}
  const [filter, setFilter]             = useState('all'); // all|same_day|meeting_delete|pending|plan
  const [groupByBd, setGroupByBd]       = useState(true); // mig 017_5: default ON for pending filter
  const [actorRole, setActorRole]       = useState('cm');

  useEffect(() => { loadQueue(); }, []);

  async function loadQueue() {
    setLoading(true);
    try {
      const res = await fetch('/api/planner/v2/cm_queue');
      const d   = await res.json();
      setQueue(d.requests || []);
      setActorRole(d.actor_role || 'cm');
    } catch (e) {
      Alert.alert('Load failed', e.message);
    }
    // Also fetch BD-grouped pending_task_carry from v_cm_queue_by_bd (mig 017_5)
    try {
      const res2 = await fetch('/api/planner/v2/cm_queue?group_by=bd');
      const d2   = await res2.json();
      setBdGroups(d2.bd_groups || []);
    } catch (e) {
      // non-fatal: per-row view still works
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }

  async function bulkResolve(group, action, rejectReason) {
    try {
      const res = await fetch('/api/planner/v2/bulk_resolve_carry', {
        method: 'POST',
        body: JSON.stringify({
          bd_uid:        group.bd_user_id,
          plan_date:     group.plan_date,
          action,
          reject_reason: rejectReason || '',
        }),
      });
      const r = await res.json();
      if (r.ok) {
        Alert.alert(
          action === 'approve' ? 'Bulk approved' : 'Bulk rejected',
          `${r.affected_count || group.carry_count} rows resolved for BD ${group.bd_name}`
        );
        loadQueue();
      } else {
        Alert.alert('Bulk failed', r.error || 'unknown');
      }
    } catch (e) {
      Alert.alert('Bulk failed', e.message);
    }
    setBulkModal(null);
  }

  const filtered = useMemo(() => {
    if (filter === 'all') return queue;
    const map = {
      same_day:        'same_day_plan',
      meeting_delete:  'meeting_delete',
      pending:         'pending_task_carry',
      plan:            'plan_approval',
    };
    return queue.filter(q => q.request_type === map[filter]);
  }, [queue, filter]);

  const counts = useMemo(() => ({
    same_day:       queue.filter(q => q.request_type === 'same_day_plan'      && q.status === 'pending').length,
    meeting_delete: queue.filter(q => q.request_type === 'meeting_delete'     && q.status === 'pending').length,
    pending:        queue.filter(q => q.request_type === 'pending_task_carry' && q.status === 'pending').length,
    plan:           queue.filter(q => q.request_type === 'plan_approval'      && q.status === 'pending').length,
  }), [queue]);

  async function resolve(req, decision, note) {
    try {
      const res = await fetch('/api/planner/v2/resolve_request', {
        method: 'POST',
        body: JSON.stringify({ request_id: req.request_id, decision, note }),
      });
      const r = await res.json();
      if (r.ok) {
        Alert.alert(decision === 'approved' ? 'Approved' : 'Rejected', '');
        loadQueue();
      } else {
        Alert.alert('Failed', r.error || 'unknown');
      }
    } catch (e) {
      Alert.alert('Failed', e.message);
    }
    setActionModal(null);
  }

  return (
    <ScrollView
      style={s.root}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); loadQueue(); }} />}
    >
      <View style={s.header}>
        <Text style={s.headerTitle}>Approval Queue</Text>
        <Text style={s.headerSubtitle}>
          {actorRole === 'ao' ? 'AO view - Accounts Officer' : 'CM view'} - {queue.filter(q => q.status === 'pending').length} pending
        </Text>
      </View>

      {/* Filter chips */}
      <View style={s.filterRow}>
        <FilterChip label="All" count={queue.length} active={filter === 'all'} onPress={() => setFilter('all')} />
        <FilterChip label="Same-day"    count={counts.same_day}       active={filter === 'same_day'}        onPress={() => setFilter('same_day')}        color="#cf222e" />
        <FilterChip label="Delete"      count={counts.meeting_delete} active={filter === 'meeting_delete'}  onPress={() => setFilter('meeting_delete')}  color="#d4a72c" />
        <FilterChip label="Pending"     count={counts.pending}        active={filter === 'pending'}         onPress={() => setFilter('pending')}         color="#0969da" />
        <FilterChip label="Plan"        count={counts.plan}           active={filter === 'plan'}            onPress={() => setFilter('plan')}            color="#57606a" />
      </View>

      {/* Group-by-BD toggle (mig 017_5) - only shown on pending or all filter */}
      {(filter === 'pending' || filter === 'all') && bdGroups.length > 0 && (
        <View style={s.groupToggleRow}>
          <Text style={s.groupToggleLabel}>
            {bdGroups.length} BDs have pending carries
          </Text>
          <TouchableOpacity
            style={[s.groupToggleBtn, groupByBd && s.groupToggleBtnOn]}
            onPress={() => setGroupByBd(!groupByBd)}
          >
            <Text style={[s.groupToggleBtnText, groupByBd && s.groupToggleBtnTextOn]}>
              {groupByBd ? 'Grouped by BD' : 'Show per-row'}
            </Text>
          </TouchableOpacity>
        </View>
      )}

      {/* BD-grouped pending_task_carry cards (mig 017_5) */}
      {!loading && groupByBd && (filter === 'pending' || filter === 'all') && bdGroups.map(group => (
        <BdGroupCard
          key={`${group.bd_user_id}_${group.plan_date}`}
          group={group}
          onApproveAll={() => setBulkModal({ group, action: 'approve' })}
          onRejectAll={()  => setBulkModal({ group, action: 'reject' })}
        />
      ))}

      {loading && <Text style={s.loadingText}>Loading...</Text>}

      {!loading && filtered.length === 0 && (
        <View style={s.empty}>
          <Text style={s.emptyTitle}>Queue clear</Text>
          <Text style={s.emptyBody}>No pending requests in this filter.</Text>
        </View>
      )}

      {/* Per-row cards: hide pending_task_carry when grouped view is on */}
      {filtered
        .filter(req => !(groupByBd && req.request_type === 'pending_task_carry'))
        .map(req => (
          <RequestCard
            key={req.request_id}
            req={req}
            actorRole={actorRole}
            onApprove={() => setActionModal({ req, decision: 'approved' })}
            onReject={()  => setActionModal({ req, decision: 'rejected' })}
          />
        ))}

      <ActionModal
        visible={!!actionModal}
        data={actionModal}
        onClose={() => setActionModal(null)}
        onConfirm={(note) => resolve(actionModal.req, actionModal.decision, note)}
      />

      <BulkActionModal
        visible={!!bulkModal}
        data={bulkModal}
        onClose={() => setBulkModal(null)}
        onConfirm={(reason) => bulkResolve(bulkModal.group, bulkModal.action, reason)}
      />
    </ScrollView>
  );
}

// ---------------------------------------------------------------------------
// Components
// ---------------------------------------------------------------------------

function BdGroupCard({ group, onApproveAll, onRejectAll }) {
  // group fields from v_cm_queue_by_bd (mig 017_5):
  //   bd_user_id, bd_name, cluster, plan_date, carry_count,
  //   yesterday_miss_count, stale_lead_count, school_list (comma sep),
  //   oldest_age_minutes, total_pending_aging_days
  const stuck = group.oldest_age_minutes > 12 * 60;
  const schools = (group.school_list || '').split(',').slice(0, 3).join(', ');
  const more    = (group.school_list || '').split(',').length - 3;

  return (
    <View style={[s.bdGroupCard, stuck && s.cardStuck]}>
      <View style={s.bdGroupHeader}>
        <View style={s.bdGroupAvatar}>
          <Text style={s.bdGroupAvatarText}>{(group.bd_name || '?').charAt(0)}</Text>
        </View>
        <View style={{ flex: 1, marginLeft: 10 }}>
          <Text style={s.bdGroupTitle}>{group.bd_name}</Text>
          <Text style={s.bdGroupSub}>
            {group.cluster || ''} - plan date {group.plan_date}
          </Text>
        </View>
        <View style={s.bdGroupCountChip}>
          <Text style={s.bdGroupCountText}>{group.carry_count}</Text>
          <Text style={s.bdGroupCountLabel}>carries</Text>
        </View>
      </View>

      <View style={s.bdGroupSplit}>
        {group.yesterday_miss_count > 0 && (
          <View style={s.bdGroupSplitItem}>
            <Text style={s.bdGroupSplitNum}>{group.yesterday_miss_count}</Text>
            <Text style={s.bdGroupSplitLabel}>missed yesterday</Text>
          </View>
        )}
        {group.stale_lead_count > 0 && (
          <View style={s.bdGroupSplitItem}>
            <Text style={s.bdGroupSplitNum}>{group.stale_lead_count}</Text>
            <Text style={s.bdGroupSplitLabel}>stale 5d plus</Text>
          </View>
        )}
        <View style={s.bdGroupSplitItem}>
          <Text style={s.bdGroupSplitNum}>{formatAge(group.oldest_age_minutes)}</Text>
          <Text style={s.bdGroupSplitLabel}>oldest</Text>
        </View>
      </View>

      {schools && (
        <View style={s.bdGroupSchools}>
          <Text style={s.bdGroupSchoolsText}>
            {schools}{more > 0 ? ` and ${more} more` : ''}
          </Text>
        </View>
      )}

      <View style={s.cardActions}>
        <TouchableOpacity style={[s.actBtn, s.actReject]} onPress={onRejectAll}>
          <Text style={s.actBtnText}>Reject all {group.carry_count}</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[s.actBtn, s.actApprove]} onPress={onApproveAll}>
          <Text style={s.actBtnText}>Approve all {group.carry_count}</Text>
        </TouchableOpacity>
      </View>

      {stuck && (
        <Text style={s.ageWarn}>STUCK over 12h - escalate</Text>
      )}
    </View>
  );
}

function BulkActionModal({ visible, data, onClose, onConfirm }) {
  const [reason, setReason] = useState('');
  if (!visible || !data) return null;
  const isReject = data.action === 'reject';
  return (
    <Modal transparent visible={visible} onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>
            {isReject
              ? `Reject ${data.group.carry_count} carries`
              : `Approve ${data.group.carry_count} carries`}
          </Text>
          <Text style={s.modalBody}>
            BD {data.group.bd_name} - plan date {data.group.plan_date}
            {'\n'}This will resolve every pending task carry in one shot.
          </Text>
          <TextInput
            style={s.input}
            placeholder={isReject
              ? 'Reason for bulk rejection (mandatory)'
              : 'Optional bulk note'}
            value={reason}
            onChangeText={setReason}
            multiline
          />
          <View style={s.modalActions}>
            <TouchableOpacity style={[s.mBtn, s.mBtnCancel]} onPress={onClose}>
              <Text style={s.mBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[s.mBtn, isReject ? s.mBtnReject : s.mBtnGo,
                      isReject && !reason && s.mBtnDis]}
              disabled={isReject && !reason}
              onPress={() => onConfirm(reason)}
            >
              <Text style={s.mBtnText}>
                {isReject ? `Reject ${data.group.carry_count}` : `Approve ${data.group.carry_count}`}
              </Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

function FilterChip({ label, count, active, onPress, color }) {
  return (
    <TouchableOpacity
      style={[s.chip, active && s.chipActive, active && color && { backgroundColor: color, borderColor: color }]}
      onPress={onPress}
    >
      <Text style={[s.chipText, active && s.chipTextActive]}>{label}</Text>
      {count > 0 && <Text style={[s.chipCount, active && s.chipCountActive]}>{count}</Text>}
    </TouchableOpacity>
  );
}

function RequestCard({ req, actorRole, onApprove, onReject }) {
  const meta   = REQUEST_TYPE_LABELS[req.request_type] || REQUEST_TYPE_LABELS.plan_approval;
  const isOld  = req.age_minutes > 60;
  const stuck  = req.age_minutes > 12 * 60;

  const showAoBadge = req.requires_ao === 1;
  const aoNeeded    = req.requires_ao === 1 && !req.ao_approved_at;
  const cmNeeded    = !req.approved_at && req.status === 'pending';

  return (
    <View style={[s.card, stuck && s.cardStuck]}>
      <View style={s.cardHeader}>
        <View style={[s.typeChip, { backgroundColor: meta.color }]}>
          <Text style={s.typeChipText}>{meta.icon}</Text>
        </View>
        <View style={{ flex: 1, marginLeft: 10 }}>
          <Text style={s.cardTitle}>{meta.label}</Text>
          <Text style={s.cardSub}>
            BD {req.bd_name} - {req.plan_date}
            {req.age_minutes > 0 && ` - ${formatAge(req.age_minutes)} ago`}
          </Text>
        </View>
        {showAoBadge && (
          <View style={[s.aoBadge, aoNeeded ? s.aoNeeded : s.aoDone]}>
            <Text style={s.aoBadgeText}>{aoNeeded ? 'AO needed' : 'AO done'}</Text>
          </View>
        )}
      </View>

      {/* Type-specific body */}
      {req.request_type === 'same_day_plan' && (
        <View style={s.cardBody}>
          <Text style={s.bodyText}>
            BD submitted today's plan today. Per migration 017_2 this is RED
            unless you approve. Override only if there's a valid same-day reason.
          </Text>
        </View>
      )}

      {req.request_type === 'meeting_delete' && (
        <View style={s.cardBody}>
          <Text style={s.bodyText}>
            Lead {req.event_lead_id} - {actionLabel(req.event_actiontype_id)} on {req.event_date}
          </Text>
          {req.event_cash_allot > 0 && (
            <Text style={s.bodyMeta}>Cash allot Rs {req.event_cash_allot}</Text>
          )}
          {req.event_advance_status && req.event_advance_status !== 'none' && (
            <Text style={s.bodyMeta}>Advance status: {req.event_advance_status}</Text>
          )}
          <Text style={s.bodyReason}>Reason: {req.reason_text || '(none)'}</Text>
        </View>
      )}

      {req.request_type === 'pending_task_carry' && (
        <View style={s.cardBody}>
          <Text style={s.bodyText}>
            {req.pending_school || `Lead ${req.event_lead_id}`} - {sourceLabel(req.pending_source_type)}
          </Text>
          <Text style={s.bodyMeta}>
            Aging {req.pending_aging_days}d - BD wants to carry to next plan
          </Text>
        </View>
      )}

      {req.request_type === 'plan_approval' && (
        <View style={s.cardBody}>
          <Text style={s.bodyText}>
            Tomorrow's plan submitted at {req.created_at}. Cutoff 19:00 IST today.
          </Text>
        </View>
      )}

      <View style={s.cardActions}>
        <TouchableOpacity style={[s.actBtn, s.actReject]} onPress={onReject}>
          <Text style={s.actBtnText}>Reject</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[s.actBtn, s.actApprove]} onPress={onApprove}>
          <Text style={s.actBtnText}>
            {actorRole === 'ao' ? 'AO approve' : aoNeeded ? 'CM approve (AO next)' : 'Approve'}
          </Text>
        </TouchableOpacity>
      </View>

      {isOld && (
        <Text style={s.ageWarn}>
          {stuck ? 'STUCK over 12h' : 'aging over 1h'}
        </Text>
      )}
    </View>
  );
}

function ActionModal({ visible, data, onClose, onConfirm }) {
  const [note, setNote] = useState('');
  if (!visible || !data) return null;
  const isReject = data.decision === 'rejected';
  return (
    <Modal transparent visible={visible} onRequestClose={onClose}>
      <View style={s.modalOverlay}>
        <View style={s.modalCard}>
          <Text style={s.modalTitle}>
            {isReject ? 'Reject request' : 'Approve request'}
          </Text>
          <Text style={s.modalBody}>
            BD {data.req.bd_name} - {REQUEST_TYPE_LABELS[data.req.request_type]?.label}
          </Text>
          <TextInput
            style={s.input}
            placeholder={isReject ? 'Reason for rejection (mandatory)' : 'Optional note'}
            value={note}
            onChangeText={setNote}
            multiline
          />
          <View style={s.modalActions}>
            <TouchableOpacity style={[s.mBtn, s.mBtnCancel]} onPress={onClose}>
              <Text style={s.mBtnText}>Cancel</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[s.mBtn, isReject ? s.mBtnReject : s.mBtnGo,
                      isReject && !note && s.mBtnDis]}
              disabled={isReject && !note}
              onPress={() => onConfirm(note)}
            >
              <Text style={s.mBtnText}>{isReject ? 'Reject' : 'Approve'}</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------
function formatAge(min) {
  if (min < 60) return `${min}m`;
  if (min < 60 * 24) return `${Math.floor(min / 60)}h`;
  return `${Math.floor(min / 60 / 24)}d`;
}

function actionLabel(id) {
  const map = {
    1: 'Call', 2: 'Email', 3: 'Scheduled Meeting', 4: 'Barg in Meeting',
    5: 'WhatsApp', 6: 'Write MOM', 7: 'Write Proposal',
    10: 'Research', 11: 'documentation', 12: 'Review',
  };
  return map[id] || `Activity ${id}`;
}

function sourceLabel(src) {
  return src === 'yesterday_miss' ? 'Missed yesterday' : 'Stale 5d plus in pipeline';
}

// ---------------------------------------------------------------------------
// Styles
// ---------------------------------------------------------------------------
const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#f5f7fa' },
  header: { padding: 14, backgroundColor: '#ffffff', borderBottomWidth: 1, borderColor: '#eaeef2' },
  headerTitle: { fontSize: 18, fontWeight: '600', color: '#1f2328' },
  headerSubtitle: { fontSize: 11, color: '#57606a', marginTop: 2 },

  filterRow: { flexDirection: 'row', flexWrap: 'wrap', padding: 8, backgroundColor: '#ffffff', borderBottomWidth: 1, borderColor: '#eaeef2' },
  chip: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f6f8fa',
          borderColor: '#d0d7de', borderWidth: 1, borderRadius: 14,
          paddingHorizontal: 10, paddingVertical: 5, margin: 3 },
  chipActive: { backgroundColor: '#0969da', borderColor: '#0969da' },
  chipText: { fontSize: 11, color: '#1f2328' },
  chipTextActive: { color: '#ffffff', fontWeight: '600' },
  chipCount: { fontSize: 10, color: '#57606a', marginLeft: 4, backgroundColor: '#eaeef2',
               paddingHorizontal: 5, borderRadius: 8 },
  chipCountActive: { color: '#0969da', backgroundColor: '#ffffff' },

  empty: { padding: 30, alignItems: 'center' },
  emptyTitle: { fontSize: 16, fontWeight: '600', color: '#2da44e', marginBottom: 6 },
  emptyBody: { fontSize: 12, color: '#57606a' },
  loadingText: { textAlign: 'center', padding: 20, color: '#57606a' },

  card: { backgroundColor: '#ffffff', margin: 10, borderRadius: 8,
          borderColor: '#eaeef2', borderWidth: 1, padding: 12,
          shadowColor: '#1f2328', shadowOpacity: 0.04, shadowRadius: 4,
          shadowOffset: { width: 0, height: 1 } },
  cardStuck: { borderColor: '#cf222e', borderWidth: 2 },
  cardHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 8 },
  typeChip: { width: 30, height: 30, borderRadius: 15, alignItems: 'center', justifyContent: 'center' },
  typeChipText: { color: '#ffffff', fontWeight: '700', fontSize: 11 },
  cardTitle: { fontSize: 14, fontWeight: '600', color: '#1f2328' },
  cardSub: { fontSize: 10, color: '#57606a', marginTop: 1 },

  aoBadge: { paddingHorizontal: 8, paddingVertical: 3, borderRadius: 4 },
  aoNeeded: { backgroundColor: '#cf222e' },
  aoDone:   { backgroundColor: '#2da44e' },
  aoBadgeText: { color: '#ffffff', fontSize: 9, fontWeight: '700' },

  cardBody: { backgroundColor: '#f6f8fa', padding: 8, borderRadius: 6, marginBottom: 8 },
  bodyText: { fontSize: 12, color: '#1f2328', lineHeight: 16 },
  bodyMeta: { fontSize: 11, color: '#57606a', marginTop: 3 },
  bodyReason: { fontSize: 11, color: '#1f2328', marginTop: 4, fontStyle: 'italic' },

  cardActions: { flexDirection: 'row', gap: 8 },
  actBtn: { flex: 1, padding: 10, borderRadius: 6, alignItems: 'center' },
  actReject: { backgroundColor: '#cf222e' },
  actApprove: { backgroundColor: '#2da44e' },
  actBtnText: { color: '#ffffff', fontSize: 12, fontWeight: '600' },

  ageWarn: { fontSize: 10, color: '#cf222e', marginTop: 6, textAlign: 'center', fontWeight: '600' },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(31,35,40,0.4)', justifyContent: 'flex-end' },
  modalCard: { backgroundColor: '#ffffff', borderTopLeftRadius: 14, borderTopRightRadius: 14, padding: 16 },
  modalTitle: { fontSize: 16, fontWeight: '600', color: '#1f2328', marginBottom: 6 },
  modalBody:  { fontSize: 12, color: '#57606a', lineHeight: 16, marginBottom: 10 },
  modalActions: { flexDirection: 'row', gap: 8 },
  input: { borderWidth: 1, borderColor: '#d0d7de', borderRadius: 6, padding: 8,
           minHeight: 60, marginBottom: 10, fontSize: 12, color: '#1f2328' },
  mBtn: { flex: 1, padding: 10, borderRadius: 6, alignItems: 'center' },
  mBtnCancel: { backgroundColor: '#57606a' },
  mBtnGo:     { backgroundColor: '#2da44e' },
  mBtnReject: { backgroundColor: '#cf222e' },
  mBtnDis:    { backgroundColor: '#eaeef2' },
  mBtnText:   { color: '#ffffff', fontSize: 12, fontWeight: '600' },

  // Bulk-resolve UI (mig 017_5)
  groupToggleRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
                    paddingHorizontal: 12, paddingVertical: 8, backgroundColor: '#fff8e7',
                    borderBottomWidth: 1, borderColor: '#eaeef2' },
  groupToggleLabel: { fontSize: 11, color: '#57606a' },
  groupToggleBtn: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12,
                    backgroundColor: '#ffffff', borderWidth: 1, borderColor: '#d0d7de' },
  groupToggleBtnOn: { backgroundColor: '#0969da', borderColor: '#0969da' },
  groupToggleBtnText: { fontSize: 11, color: '#1f2328' },
  groupToggleBtnTextOn: { color: '#ffffff', fontWeight: '600' },

  bdGroupCard: { backgroundColor: '#ffffff', margin: 10, borderRadius: 8,
                 borderColor: '#0969da', borderWidth: 1, padding: 12 },
  bdGroupHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 10 },
  bdGroupAvatar: { width: 36, height: 36, borderRadius: 18, backgroundColor: '#0969da',
                   alignItems: 'center', justifyContent: 'center' },
  bdGroupAvatarText: { color: '#ffffff', fontWeight: '700', fontSize: 15 },
  bdGroupTitle: { fontSize: 15, fontWeight: '600', color: '#1f2328' },
  bdGroupSub: { fontSize: 11, color: '#57606a', marginTop: 1 },
  bdGroupCountChip: { backgroundColor: '#0969da', paddingHorizontal: 10, paddingVertical: 4,
                      borderRadius: 6, alignItems: 'center' },
  bdGroupCountText: { color: '#ffffff', fontSize: 16, fontWeight: '700' },
  bdGroupCountLabel: { color: '#ffffff', fontSize: 9 },

  bdGroupSplit: { flexDirection: 'row', backgroundColor: '#f6f8fa', borderRadius: 6,
                  padding: 8, marginBottom: 8 },
  bdGroupSplitItem: { flex: 1, alignItems: 'center' },
  bdGroupSplitNum: { fontSize: 14, fontWeight: '700', color: '#1f2328' },
  bdGroupSplitLabel: { fontSize: 9, color: '#57606a', marginTop: 1 },

  bdGroupSchools: { backgroundColor: '#f6f8fa', padding: 8, borderRadius: 6, marginBottom: 8 },
  bdGroupSchoolsText: { fontSize: 11, color: '#1f2328', fontStyle: 'italic' },
});
