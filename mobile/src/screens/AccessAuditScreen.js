/**
 * AccessAuditScreen
 * ---------------------------------------------------------------------
 * CEO + Admin view of the contact_access_log. Filterable by user, event
 * type, and date range. Anyone else hitting this route gets a 403 from
 * /contact/api_admin_access_log; non-CEO/Admin should not see the menu
 * entry in App.js to begin with.
 *
 * For BDs and CMs, route 'AccessAuditMine' uses /contact/api_my_access_log
 * (read-only, only their own events) — same screen, scopeMine=true.
 */
import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, FlatList, Pressable, StyleSheet, RefreshControl,
  TextInput, Alert,
} from 'react-native';
import { api } from '../api/client';

const EVENT_LABELS = {
  view:     { label: 'Viewed',   color: '#2E90FA' },
  reveal:   { label: 'Revealed', color: '#F79009' },
  export_request:  { label: 'Export request',  color: '#7A5AF8' },
  export_approve:  { label: 'Export approved', color: '#12B76A' },
  export_reject:   { label: 'Export rejected', color: '#F04438' },
  export_download: { label: 'Export downloaded', color: '#7A5AF8' },
  cap_breach:      { label: 'Cap breach',  color: '#F04438' },
};

export default function AccessAuditScreen({ route, navigation }) {
  const scopeMine = route?.params?.scopeMine === true;

  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [userFilter, setUserFilter] = useState('');
  const [eventFilter, setEventFilter] = useState(''); // '' | 'reveal' | 'export_*' etc.

  const load = useCallback(async () => {
    try {
      const path = scopeMine
        ? '/contact/api_my_access_log'
        : '/contact/api_admin_access_log';
      const params = {};
      if (!scopeMine && userFilter.trim()) params.user_q = userFilter.trim();
      if (!scopeMine && eventFilter)       params.event = eventFilter;
      const res = await api.get(path, { params });
      setRows(res.data.entries || []);
    } catch (e) {
      Alert.alert('Could not load log', e.message || 'Network error');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [scopeMine, userFilter, eventFilter]);

  useEffect(() => { load(); }, [load]);

  const onRefresh = () => { setRefreshing(true); load(); };

  const renderRow = ({ item }) => {
    const meta = EVENT_LABELS[item.event_type] || { label: item.event_type, color: '#667085' };
    return (
      <View style={styles.row}>
        <View style={[styles.dot, { backgroundColor: meta.color }]} />
        <View style={{ flex: 1 }}>
          <View style={styles.line}>
            <Text style={styles.who} selectable={false}>
              {scopeMine ? 'You' : item.user_name}
            </Text>
            <Text style={[styles.evt, { color: meta.color }]} selectable={false}>
              {meta.label}
            </Text>
          </View>
          <Text style={styles.sub} selectable={false}>
            {item.target_label}
          </Text>
          <Text style={styles.when} selectable={false}>
            {item.created_at_human} · {item.ip || '—'}
          </Text>
          {item.note ? (
            <Text style={styles.note} selectable={false}>{item.note}</Text>
          ) : null}
        </View>
      </View>
    );
  };

  return (
    <View style={styles.screen}>
      <View style={styles.header}>
        <Text style={styles.title} selectable={false}>
          {scopeMine ? 'My access log' : 'Contact access audit'}
        </Text>
        <Text style={styles.subDim} selectable={false}>
          {rows.length} entries
        </Text>
      </View>

      {!scopeMine && (
        <View style={styles.filters}>
          <TextInput
            style={styles.input}
            placeholder="Filter by user name / id"
            value={userFilter}
            onChangeText={setUserFilter}
            onSubmitEditing={load}
            returnKeyType="search"
          />
          <View style={styles.chips}>
            {['', 'view', 'reveal', 'export_request', 'export_download', 'cap_breach'].map(e => (
              <Pressable
                key={e || 'all'}
                style={[styles.chip, eventFilter === e && styles.chipOn]}
                onPress={() => setEventFilter(e)}
              >
                <Text style={eventFilter === e ? styles.chipOnTxt : styles.chipTxt}>
                  {e ? (EVENT_LABELS[e]?.label || e) : 'All'}
                </Text>
              </Pressable>
            ))}
          </View>
        </View>
      )}

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
            : <Text style={styles.dim}>No log entries match.</Text>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#F7F8FA' },
  header: { padding: 16, borderBottomWidth: 1, borderBottomColor: '#E6E8EE' },
  title: { fontSize: 22, fontWeight: '700', color: '#101828' },
  subDim: { color: '#667085', marginTop: 2 },
  filters: { padding: 12, borderBottomWidth: 1, borderBottomColor: '#E6E8EE', backgroundColor: '#fff' },
  input: {
    borderWidth: 1, borderColor: '#D0D5DD', borderRadius: 8,
    paddingHorizontal: 12, paddingVertical: 8, color: '#101828',
  },
  chips: { flexDirection: 'row', flexWrap: 'wrap', marginTop: 8, gap: 6 },
  chip: { paddingHorizontal: 10, paddingVertical: 6, borderRadius: 16, borderWidth: 1, borderColor: '#D0D5DD' },
  chipOn: { backgroundColor: '#101828', borderColor: '#101828' },
  chipTxt: { color: '#475467', fontSize: 12 },
  chipOnTxt: { color: '#fff', fontSize: 12, fontWeight: '600' },

  empty: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 32 },
  dim: { color: '#667085', textAlign: 'center' },

  row: {
    flexDirection: 'row', alignItems: 'flex-start',
    padding: 12, borderBottomWidth: 1, borderBottomColor: '#EFF1F5',
    backgroundColor: '#fff',
  },
  dot: { width: 8, height: 8, borderRadius: 4, marginTop: 6, marginRight: 10 },
  line: { flexDirection: 'row', justifyContent: 'space-between' },
  who: { fontWeight: '700', color: '#101828' },
  evt: { fontWeight: '700', fontSize: 13 },
  sub: { color: '#475467', marginTop: 2, fontSize: 13 },
  when: { color: '#98A2B3', marginTop: 2, fontSize: 12 },
  note: { color: '#475467', marginTop: 4, fontSize: 12, fontStyle: 'italic' },
});
