/**
 * ExportRequestScreen
 * ---------------------------------------------------------------------
 * Anyone in the org can ask to export contacts. CEO/Admin are auto-approved
 * and shown the download token immediately. Everyone else sees a pending
 * state until the CEO decides on it.
 *
 * Route: Drawer > Tools > Request contact export
 *        (also opened from LeadDetailScreen > "Export this lead's contacts")
 */
import React, { useState } from 'react';
import {
  View, Text, ScrollView, TextInput, Pressable, StyleSheet, Alert,
} from 'react-native';
import { requestExport } from '../lib/secureContact';

const SCOPES = [
  { key: 'single_lead',    label: 'A single lead' },
  { key: 'single_company', label: 'A single school' },
  { key: 'cluster',        label: 'My cluster' },
  { key: 'region',         label: 'My region' },
  { key: 'date_range',     label: 'A date range' },
  { key: 'custom_filter',  label: 'A custom filter' },
];

export default function ExportRequestScreen({ route, navigation }) {
  const [scopeType, setScopeType] = useState(route?.params?.scopeType || 'single_lead');
  const [scopePayloadStr, setScopePayloadStr] = useState(
    JSON.stringify(route?.params?.scopePayload || { lead_id: null }, null, 2)
  );
  const [purpose, setPurpose] = useState('');
  const [result, setResult]   = useState(null);
  const [busy, setBusy]       = useState(false);

  const submit = async () => {
    if (purpose.trim().length < 20) {
      Alert.alert('Purpose too short',
        'Please describe why you need this export (at least 20 characters). ' +
        'This text is shown to the CEO at approval time.');
      return;
    }
    let payload;
    try {
      payload = JSON.parse(scopePayloadStr);
    } catch (e) {
      Alert.alert('Invalid scope payload', 'Scope must be valid JSON.');
      return;
    }
    setBusy(true);
    try {
      const out = await requestExport({ scopeType, scopePayload: payload, purpose });
      setResult(out);
    } catch (e) {
      Alert.alert('Submit failed', e.message || 'Network error');
    } finally {
      setBusy(false);
    }
  };

  if (result?.status === 'approved' && result.download_token) {
    return (
      <View style={styles.container}>
        <Text style={styles.h1}>Auto-approved (CEO/Admin)</Text>
        <Text style={styles.p}>
          Your download is ready. Token expires at {result.expires_at}.
        </Text>
        <Text style={styles.code}>{result.download_token}</Text>
        <Text style={styles.warn}>
          ⚠ Single-use link. Treat this file as confidential. The IP that
          downloads it is logged.
        </Text>
        <Text style={styles.meta}>Estimated rows: {result.row_estimate || '?'}</Text>
      </View>
    );
  }

  if (result?.status === 'pending') {
    return (
      <View style={styles.container}>
        <Text style={styles.h1}>Submitted to CEO</Text>
        <Text style={styles.p}>
          Request #{result.request_id} is now in the CEO's approval queue.
          You will be notified when a decision is made.
        </Text>
        <Text style={styles.meta}>Estimated rows: {result.row_estimate || '?'}</Text>
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.h1}>Request contact export</Text>
      <Text style={styles.p}>
        Contact details are protected. Exports require CEO approval (BD, CM,
        RM) or are auto-approved (Admin, CEO). All exports are watermarked
        with your user ID and traceable.
      </Text>

      <Text style={styles.label}>Scope</Text>
      <View style={styles.row}>
        {SCOPES.map(s => (
          <Pressable
            key={s.key}
            onPress={() => setScopeType(s.key)}
            style={[styles.chip, scopeType === s.key && styles.chipOn]}>
            <Text style={[styles.chipTxt, scopeType === s.key && styles.chipTxtOn]}>{s.label}</Text>
          </Pressable>
        ))}
      </View>

      <Text style={styles.label}>Scope details (JSON)</Text>
      <TextInput
        style={styles.input}
        value={scopePayloadStr}
        onChangeText={setScopePayloadStr}
        multiline
        numberOfLines={4}
      />
      <Text style={styles.hint}>
        e.g. {`{"lead_id":123}`} or {`{"cluster_id":3}`} or {`{"from":"2026-05-01","to":"2026-05-15"}`}
      </Text>

      <Text style={styles.label}>Why do you need this? *</Text>
      <TextInput
        style={[styles.input, { minHeight: 80 }]}
        value={purpose}
        onChangeText={setPurpose}
        multiline
        placeholder="Min 20 characters. Shown to the CEO. Example: 'Cluster handover from BD Priya to BD Anita next week, need contact list for transition meetings.'"
      />
      <Text style={styles.hint}>{purpose.length} / 20 minimum</Text>

      <Pressable
        onPress={submit}
        disabled={busy}
        style={[styles.cta, busy && { opacity: 0.5 }]}>
        <Text style={styles.ctaTxt}>{busy ? 'Submitting…' : 'Submit request'}</Text>
      </Pressable>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: 16, backgroundColor: '#F8FAFC', flexGrow: 1 },
  h1:    { fontSize: 20, fontWeight: '700', color: '#0F172A', marginBottom: 8 },
  p:     { fontSize: 14, color: '#475569', marginBottom: 16, lineHeight: 20 },
  label: { fontSize: 13, fontWeight: '600', color: '#0F172A', marginTop: 12, marginBottom: 6 },
  input: { backgroundColor: '#fff', borderWidth: 1, borderColor: '#E2E8F0',
           borderRadius: 8, padding: 10, fontSize: 14, color: '#0F172A' },
  hint:  { fontSize: 11, color: '#94A3B8', marginTop: 4 },
  row:   { flexDirection: 'row', flexWrap: 'wrap', gap: 6 },
  chip:  { paddingHorizontal: 12, paddingVertical: 6, borderRadius: 16,
           backgroundColor: '#E2E8F0', marginRight: 6, marginBottom: 6 },
  chipOn:{ backgroundColor: '#0F172A' },
  chipTxt:   { fontSize: 12, color: '#475569' },
  chipTxtOn: { color: '#fff', fontWeight: '600' },
  cta:   { backgroundColor: '#0F172A', borderRadius: 10, padding: 14, marginTop: 24, alignItems: 'center' },
  ctaTxt:{ color: '#fff', fontWeight: '700', fontSize: 15 },
  code:  { fontFamily: 'monospace', fontSize: 12, color: '#0F172A',
           backgroundColor: '#F1F5F9', padding: 10, borderRadius: 6, marginVertical: 12 },
  warn:  { color: '#B45309', fontSize: 13, marginVertical: 8 },
  meta:  { fontSize: 12, color: '#64748B', marginTop: 8 },
});
