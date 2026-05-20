/**
 * ExportApprovalQueueScreen
 * ---------------------------------------------------------------------
 * CEO-only inbox of pending contact-export requests. Each row shows the
 * requester, scope, purpose, estimated row count, and Approve / Reject
 * buttons. Decisions flow through /contact/api_decide_export and emit
 * to contact_access_log via the controller.
 *
 * Route: Drawer > CEO Tools > Export approvals
 * Guard: only visible when user.is_ceo == 1 (App.js routes around it).
 */
import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, FlatList, Pressable, StyleSheet, RefreshControl,
  Alert, TextInput, Modal,
} from 'react-native';
import { api } from '../api/client';

export default function ExportApprovalQueueScreen({ navigation }) {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [activeRow, setActiveRow] = useState(null);   // row being decided
  const [decision, setDecision] = useState(null);     // 'approve' | 'reject'
  const [comment, setComment] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const load = useCallback(async () => {
    try {
      const res = await api.get('/contact/api_list_pending_exports');
      setRows(res.data.requests || []);
    } catch (e) {
      Alert.alert('Could not load queue', e.message || 'Network error');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);

  const onRefresh = () => { setRefreshing(true); load(); };

  const openDecision = (row, action) => {
    setActiveRow(row);
    setDecision(action);
    setComment('');
  };

  const submitDecision = async () => {
    if (decision === 'reject' && comment.trim().length < 5) {
      Alert.alert('Reason required', 'Tell the requester why (min 5 chars).');
      return;
    }
    setSubmitting(true);
    try {
      await api.post('/contact/api_decide_export', {
        request_id: activeRow.id,
        decision,           // 'approve' | 'reject'
        comment: comment.trim(),
      });
      setActiveRow(null);
      setDecision(null);
      setComment('');
      load();
    } catch (e) {
      Alert.alert('Could not submit', e.message || 'Try again.');
    } finally {
      setSubmitting(false);
    }
  };

  const renderRow = ({ item }) => (
    <View style={styles.card}>
      <View style={styles.cardHead}>
        <Text style={styles.who} selectable={false}>{item.requester_name}</Text>
        <Text style={styles.when} selectable={false}>{item.requested_at_human}</Text>
      </View>
      <Text style={styles.role} selectable={false}>
        {item.requester_role} · {item.requester_cluster || '—'}
      </Text>

      <View style={styles.kv}>
        <Text style={styles.k}>Scope</Text>
        <Text style={styles.v} selectable={false}>{item.scope_label}</Text>
      </View>
      <View style={styles.kv}>
        <Text style={styles.k}>Est. rows</Text>
        <Text style={styles.v} selectable={false}>
          {item.estimated_rows ?? '—'}
        </Text>
      </View>
      <View style={styles.kvBlock}>
        <Text style={styles.k}>Purpose</Text>
        <Text style={styles.purpose} selectable={false}>{item.purpose}</Text>
      </View>

      <View style={styles.row}>
        <Pressable
          style={[styles.btn, styles.reject]}
          onPress={() => openDecision(item, 'reject')}
        >
          <Text style={styles.rejectTxt}>Reject</Text>
        </Pressable>
        <Pressable
          style={[styles.btn, styles.approve]}
          onPress={() => openDecision(item, 'approve')}
        >
          <Text style={styles.approveTxt}>Approve</Text>
        </Pressable>
      </View>
    </View>
  );

  return (
    <View style={styles.screen}>
      <View style={styles.header}>
        <Text style={styles.title} selectable={false}>Export approvals</Text>
        <Text style={styles.sub} selectable={false}>
          {rows.length} pending
        </Text>
      </View>

      <FlatList
        data={rows}
        keyExtractor={(r) => String(r.id)}
        renderItem={renderRow}
        contentContainerStyle={rows.length ? null : styles.empty}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        ListEmptyComponent={
          loading
            ? <Text style={styles.dim}>Loading…</Text>
            : <Text style={styles.dim}>Inbox zero. Nothing pending.</Text>
        }
      />

      <Modal
        visible={!!activeRow}
        animationType="slide"
        transparent
        onRequestClose={() => setActiveRow(null)}
      >
        <View style={styles.modalWrap}>
          <View style={styles.modal}>
            <Text style={styles.modalTitle} selectable={false}>
              {decision === 'approve' ? 'Approve export' : 'Reject export'}
            </Text>
            <Text style={styles.dim} selectable={false}>
              {activeRow?.requester_name} · {activeRow?.scope_label}
            </Text>
            <TextInput
              style={styles.input}
              placeholder={
                decision === 'approve'
                  ? 'Optional note (visible to requester)'
                  : 'Reason for rejection (required)'
              }
              value={comment}
              onChangeText={setComment}
              multiline
              maxLength={300}
            />
            <View style={styles.row}>
              <Pressable
                style={[styles.btn, styles.cancel]}
                onPress={() => setActiveRow(null)}
              >
                <Text>Cancel</Text>
              </Pressable>
              <Pressable
                style={[styles.btn, decision === 'approve' ? styles.approve : styles.reject]}
                onPress={submitDecision}
                disabled={submitting}
              >
                <Text style={decision === 'approve' ? styles.approveTxt : styles.rejectTxt}>
                  {submitting ? 'Submitting…' : (decision === 'approve' ? 'Approve' : 'Reject')}
                </Text>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#F7F8FA' },
  header: { padding: 16, borderBottomWidth: 1, borderBottomColor: '#E6E8EE' },
  title: { fontSize: 22, fontWeight: '700', color: '#101828' },
  sub: { color: '#667085', marginTop: 2 },
  empty: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 32 },
  dim: { color: '#667085', textAlign: 'center' },

  card: { backgroundColor: '#fff', margin: 12, padding: 14, borderRadius: 12, borderWidth: 1, borderColor: '#E6E8EE' },
  cardHead: { flexDirection: 'row', justifyContent: 'space-between' },
  who: { fontSize: 16, fontWeight: '700', color: '#101828' },
  when: { color: '#667085', fontSize: 12 },
  role: { color: '#475467', marginBottom: 8, fontSize: 13 },

  kv: { flexDirection: 'row', justifyContent: 'space-between', marginVertical: 2 },
  kvBlock: { marginTop: 6 },
  k: { color: '#667085', fontSize: 13 },
  v: { color: '#101828', fontSize: 13, fontWeight: '600' },
  purpose: { color: '#101828', fontSize: 14, marginTop: 4 },

  row: { flexDirection: 'row', marginTop: 12, gap: 8 },
  btn: { flex: 1, paddingVertical: 10, alignItems: 'center', borderRadius: 8, borderWidth: 1 },
  approve: { backgroundColor: '#12B76A', borderColor: '#12B76A' },
  approveTxt: { color: '#fff', fontWeight: '700' },
  reject: { backgroundColor: '#fff', borderColor: '#F04438' },
  rejectTxt: { color: '#F04438', fontWeight: '700' },
  cancel: { backgroundColor: '#fff', borderColor: '#D0D5DD' },

  modalWrap: { flex: 1, backgroundColor: 'rgba(16,24,40,0.45)', justifyContent: 'flex-end' },
  modal: { backgroundColor: '#fff', padding: 16, borderTopLeftRadius: 16, borderTopRightRadius: 16 },
  modalTitle: { fontSize: 18, fontWeight: '700', marginBottom: 4, color: '#101828' },
  input: {
    borderWidth: 1, borderColor: '#D0D5DD', borderRadius: 8, padding: 10,
    marginTop: 10, minHeight: 80, textAlignVertical: 'top', color: '#101828',
  },
});
