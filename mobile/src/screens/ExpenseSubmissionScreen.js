// ExpenseSubmissionScreen.js
// Multi-meeting expense submission screen for BDs.
// Mirrors the production web flow exactly:
//   stemapp.in -> Menu/UpdateTodaysMeetingsDetails (view)
//                -> Menu/AddCashSpentInMeetings (POST handler)
//
// Production behaviour we are matching, one to one:
//   - Loads every closed meeting today that still has expense pending
//   - Renders a timeline card per meeting (company, start, close, remarks, time spent)
//   - Per card the BD enters: amount, expense remarks, one or more bill files
//   - One shared travel_expense_type multi-select on top (Bus / Train / Auto / Cab / Bike / Toll / Fuel / Other)
//   - One Submit button at the bottom that batches all meetings in a single multipart POST
//
// Cash flow on submit follows production Menu_model::Addexpensecash:
//   1. If expense <= tblcallevents.cash_allot -> refund the leftover to user.ucash
//      and stamp tblcallevents.cash_expense + cash_refund. Insert cash_expense + cash_log.
//   2. If expense > cash_allot -> deduct the excess from user.ucash (must have balance).
//   3. Variance over plus or minus 20 percent against planned_cost flags requires_dual_approval.
//   4. Receipt is mandatory on every row.
//
// Endpoints:
//   GET  /api/discipline/expense/pending_meetings    list today's pending meetings for this BD
//   POST /api/discipline/expense/submit_batch        multipart, batched per the rules above

import React, { useState, useEffect, useMemo } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity,
  TextInput, Image, ActivityIndicator, Alert, RefreshControl
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { api, apiUpload } from '../lib/api';
import { CURRENT_USER } from '../data/roles';

const COLORS = {
  bg: '#e3f2e1',          // production page background
  card: '#f4f9f2',         // production card surface
  header: '#01434d',       // production header teal
  text: '#1a1a1a',
  primary: '#1f5fbf',
  red: '#d0021b',
  amber: '#f6a623',
  green: '#1f9d55',
  muted: '#555',
  cat: ['#fde2e2','#e2f0fd','#fdf0e2','#e8e2fd','#e2fdec','#fde2f1','#f4e2fd','#fdf9e2'],
};

const TRAVEL_TYPES = ['Bus','Train','Auto','Cab','Bike','Toll','Fuel','Hotel','Food','Other'];

function timeBetween(startISO, closeISO){
  if(!startISO || !closeISO) return '';
  const ms = new Date(closeISO) - new Date(startISO);
  if(!isFinite(ms) || ms < 0) return '';
  const h = Math.floor(ms / 3600000);
  const m = Math.floor((ms % 3600000) / 60000);
  const s = Math.floor((ms % 60000) / 1000);
  return `${h} hours, ${m} minutes, ${s} seconds`;
}

export default function ExpenseSubmissionScreen({ navigation }) {
  const user = CURRENT_USER;
  const [loading, setLoading]       = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [meetings, setMeetings]     = useState([]);
  const [rowState, setRowState]     = useState({}); // { [meetid]: { amount, remarks, photos:[{uri,name,type}] } }
  const [travelTypes, setTravelTypes] = useState([]);
  const [submitting, setSubmitting] = useState(false);

  const load = async () => {
    setLoading(true);
    try {
      const r = await api.get('/api/discipline/expense/pending_meetings');
      const list = r.meetings || [];
      setMeetings(list);
      // initialize empty row state for any meeting we have not touched yet
      setRowState(prev => {
        const next = { ...prev };
        list.forEach(m => {
          if(!next[m.meetid]) next[m.meetid] = { amount:'', remarks:'', photos:[] };
        });
        return next;
      });
    } catch (e) {
      console.warn('pending_meetings failed', e);
    } finally { setLoading(false); setRefreshing(false); }
  };
  useEffect(() => { load(); }, []);

  const toggleTravelType = (t) => {
    setTravelTypes(arr => arr.includes(t) ? arr.filter(x => x !== t) : [...arr, t]);
  };

  const updateRow = (meetid, patch) => {
    setRowState(prev => ({ ...prev, [meetid]: { ...prev[meetid], ...patch } }));
  };

  const pickPhotos = async (meetid) => {
    const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!perm.granted) { Alert.alert('Photo access needed for bill upload'); return; }
    const r = await ImagePicker.launchImageLibraryAsync({
      allowsMultipleSelection: true, quality: 0.6, mediaTypes: ImagePicker.MediaTypeOptions.Images,
    });
    if (r.canceled) return;
    const assets = (r.assets || []).map((a, i) => ({
      uri: a.uri, name: `bill_${meetid}_${Date.now()}_${i}.jpg`, type: 'image/jpeg',
    }));
    updateRow(meetid, { photos: [ ...(rowState[meetid]?.photos || []), ...assets ] });
  };

  const captureBill = async (meetid) => {
    const perm = await ImagePicker.requestCameraPermissionsAsync();
    if (!perm.granted) { Alert.alert('Camera access needed for bill capture'); return; }
    const r = await ImagePicker.launchCameraAsync({ quality: 0.6, base64: false });
    if (r.canceled || !r.assets?.[0]) return;
    const a = r.assets[0];
    const asset = { uri: a.uri, name: `bill_${meetid}_${Date.now()}.jpg`, type: 'image/jpeg' };
    updateRow(meetid, { photos: [ ...(rowState[meetid]?.photos || []), asset ] });
  };

  const removePhoto = (meetid, idx) => {
    const photos = [ ...(rowState[meetid]?.photos || []) ];
    photos.splice(idx, 1);
    updateRow(meetid, { photos });
  };

  // Variance helper per row
  const varianceFor = (m) => {
    const a = parseInt(rowState[m.meetid]?.amount || '0');
    const planned = m.planned_cost || m.cash_allot || 500;
    if (!a) return { pct: 0, dual: false };
    const pct = Math.round(((a - planned) / planned) * 100);
    return { pct, dual: Math.abs(pct) > 20 };
  };

  const flaggedCount = useMemo(() =>
    meetings.filter(m => varianceFor(m).dual).length
  , [meetings, rowState]);

  const submitBatch = async () => {
    if (!meetings.length) return;
    if (!travelTypes.length) {
      Alert.alert('Select at least one travel expense type');
      return;
    }
    // Validate every row
    for (const m of meetings) {
      const r = rowState[m.meetid] || {};
      if (!r.amount || parseInt(r.amount) < 0) {
        Alert.alert(`Enter expense amount for ${m.company_name}`);
        return;
      }
      if (!r.remarks?.trim()) {
        Alert.alert(`Enter expense remarks for ${m.company_name}`);
        return;
      }
      if (!r.photos?.length) {
        Alert.alert(`At least one bill is mandatory for ${m.company_name}`);
        return;
      }
    }
    setSubmitting(true);
    try {
      const form = new FormData();
      form.append('travel_expense_type', travelTypes.join(','));
      meetings.forEach((m, idx) => {
        const r = rowState[m.meetid];
        form.append('meetingid[]', String(m.meetid));
        form.append('expensecash[]', String(parseInt(r.amount)));
        form.append('expense_remarks[]', r.remarks);
        // Production uses images1[], images2[], ... one bucket per meeting
        r.photos.forEach((p) => {
          form.append(`images${idx + 1}[]`, p);
        });
      });
      const res = await apiUpload('/api/discipline/expense/submit_batch', form);
      if (!res.ok) { Alert.alert('Submit failed', res.error || 'Server error'); return; }
      const lines = [
        `${res.submitted || meetings.length} meeting expense rows recorded.`,
        res.dual_approval_count ? `${res.dual_approval_count} flagged for CM + Accounts Officer dual approval.` : '',
        res.cash_refunded ? `Rs ${res.cash_refunded} refunded to your wallet from unused allotments.` : '',
        res.cash_deducted ? `Rs ${res.cash_deducted} deducted from your wallet for overspend.` : '',
      ].filter(Boolean).join('\n');
      Alert.alert('Submitted', lines);
      // reset and reload
      setRowState({}); setTravelTypes([]);
      load();
    } catch (e) { Alert.alert('Network error', String(e)); }
    finally { setSubmitting(false); }
  };

  if (loading) return <ActivityIndicator style={{ flex: 1 }} size="large" />;

  return (
    <ScrollView
      style={s.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      <View style={s.headerCard}>
        <Text style={s.headerTitle}>Update Meetings Expense Details</Text>
      </View>

      {meetings.length === 0 ? (
        <View style={s.noRecord}>
          <Text style={s.noRecordTxt}>No Meetings Found</Text>
          <Text style={s.muted}>All actuals are submitted. Tomorrow's plan is unblocked.</Text>
        </View>
      ) : (
        <>
          <View style={s.banner}>
            <Text style={s.bannerTitle}>Total {meetings.length} Meetings Expense Pending</Text>
            {flaggedCount > 0 && (
              <Text style={s.bannerWarn}>{flaggedCount} row(s) above 20 percent variance will need dual approval.</Text>
            )}
          </View>

          {/* Shared travel expense type multi-select. Production requires at least one. */}
          <View style={s.card}>
            <Text style={s.label}>Travel Expense Type (select all that apply)</Text>
            <View style={s.chipsRow}>
              {TRAVEL_TYPES.map(t => (
                <TouchableOpacity
                  key={t}
                  style={[s.chip, travelTypes.includes(t) && s.chipOn]}
                  onPress={() => toggleTravelType(t)}
                >
                  <Text style={[s.chipTxt, travelTypes.includes(t) && s.chipTxtOn]}>{t}</Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>

          {meetings.map((m, idx) => {
            const r = rowState[m.meetid] || { amount:'', remarks:'', photos:[] };
            const v = varianceFor(m);
            const catBg = COLORS.cat[idx % COLORS.cat.length];
            return (
              <View key={m.meetid} style={[s.meetCard, { backgroundColor: catBg }]}>
                <View style={s.meetHeader}>
                  <Text style={s.meetNum}>{idx + 1}</Text>
                  <Text style={s.meetTitle} numberOfLines={2}>{m.company_name}</Text>
                </View>

                <View style={s.meetBody}>
                  <Text style={s.metaLine}><Text style={s.metaLabel}>Start Meeting: </Text>{m.startm}</Text>
                  <Text style={s.metaLine}><Text style={s.metaLabel}>Close Meeting: </Text>{m.closem}</Text>
                  <Text style={s.metaLine}><Text style={s.metaLabel}>Close Remarks: </Text>{m.remarks || '-'}</Text>
                  <Text style={s.metaLine}><Text style={s.metaLabel}>Time Spent: </Text>{timeBetween(m.startm, m.closem)}</Text>
                  <Text style={s.metaLine}><Text style={s.metaLabel}>Allotted: </Text>Rs {m.cash_allot || 0} | <Text style={s.metaLabel}>Planned: </Text>Rs {m.planned_cost || m.cash_allot || 500}</Text>

                  <Text style={[s.fieldLabel, { marginTop: 14 }]}>Enter Expense Amount</Text>
                  <TextInput
                    style={s.input}
                    placeholder="Rs Enter Expense Amount"
                    keyboardType="number-pad"
                    value={r.amount}
                    onChangeText={t => updateRow(m.meetid, { amount: t.replace(/[^0-9]/g,'') })}
                  />

                  {r.amount ? (
                    <View style={[s.varianceBox, { backgroundColor: v.dual ? '#ffe5e5' : '#e6f4ea' }]}>
                      <Text style={[s.varianceTxt, { color: v.dual ? COLORS.red : COLORS.green }]}>
                        Variance {v.pct > 0 ? '+' : ''}{v.pct}%  {v.dual ? '(dual approval needed)' : '(within 20 percent)'}
                      </Text>
                    </View>
                  ) : null}

                  <Text style={s.fieldLabel}>Expense Remarks</Text>
                  <TextInput
                    style={[s.input, { height: 70 }]}
                    placeholder="Expense Remarks"
                    multiline
                    value={r.remarks}
                    onChangeText={t => updateRow(m.meetid, { remarks: t })}
                  />

                  <Text style={s.fieldLabel}>Upload bills (mandatory)</Text>
                  <View style={s.row}>
                    <TouchableOpacity style={s.smallBtn} onPress={() => captureBill(m.meetid)}>
                      <Text style={s.smallBtnTxt}>Capture</Text>
                    </TouchableOpacity>
                    <TouchableOpacity style={[s.smallBtn, { marginLeft: 8, backgroundColor: '#5b6cb0' }]} onPress={() => pickPhotos(m.meetid)}>
                      <Text style={s.smallBtnTxt}>Pick from gallery</Text>
                    </TouchableOpacity>
                  </View>

                  {r.photos?.length > 0 && (
                    <ScrollView horizontal style={{ marginTop: 8 }}>
                      {r.photos.map((p, i) => (
                        <TouchableOpacity key={i} onLongPress={() => removePhoto(m.meetid, i)}>
                          <Image source={{ uri: p.uri }} style={s.thumb} />
                        </TouchableOpacity>
                      ))}
                    </ScrollView>
                  )}
                  {r.photos?.length > 0 && (
                    <Text style={s.tip}>Long press a bill to remove. {r.photos.length} file(s) attached.</Text>
                  )}

                  <TouchableOpacity
                    style={s.cancelLink}
                    onPress={() => navigation.navigate('CancellationAdvanceAuditScreen', { prefill_event_id: m.event_id })}
                  >
                    <Text style={{ color: COLORS.red }}>Meeting did not happen? Cancel it instead</Text>
                  </TouchableOpacity>
                </View>
              </View>
            );
          })}

          <TouchableOpacity
            style={[s.submitBtn, { opacity: submitting ? 0.5 : 1 }]}
            disabled={submitting}
            onPress={submitBatch}
          >
            <Text style={s.submitTxt}>{submitting ? 'Submitting...' : `Submit all ${meetings.length} expense rows`}</Text>
          </TouchableOpacity>
        </>
      )}

      <View style={{ height: 30 }} />
    </ScrollView>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.bg },
  headerCard: { backgroundColor: COLORS.header, padding: 14, marginBottom: 8 },
  headerTitle: { color: '#fff', textAlign: 'center', fontSize: 16, fontWeight: '700', letterSpacing: 0.5 },
  banner: { backgroundColor: '#fff5e6', padding: 12, marginHorizontal: 10, borderRadius: 8, marginBottom: 10 },
  bannerTitle: { fontWeight: '700', color: '#ff007a', textAlign: 'center', fontSize: 15 },
  bannerWarn: { color: '#a36500', textAlign: 'center', marginTop: 4, fontSize: 12 },
  noRecord: { backgroundColor: '#fff', padding: 30, margin: 14, borderRadius: 12, alignItems: 'center' },
  noRecordTxt: { fontSize: 18, fontWeight: '600', color: COLORS.green, marginBottom: 6 },
  muted: { color: COLORS.muted, fontSize: 13, textAlign: 'center' },

  card: { backgroundColor: '#fff', padding: 12, marginHorizontal: 10, borderRadius: 10, marginBottom: 10 },
  label: { color: COLORS.muted, fontSize: 12, marginBottom: 6 },
  chipsRow: { flexDirection: 'row', flexWrap: 'wrap' },
  chip: { borderWidth: 1, borderColor: '#ccc', borderRadius: 16, paddingVertical: 6, paddingHorizontal: 10, marginRight: 6, marginBottom: 6, backgroundColor: '#fff' },
  chipOn: { backgroundColor: COLORS.header, borderColor: COLORS.header },
  chipTxt: { color: '#444', fontSize: 12 },
  chipTxtOn: { color: '#fff', fontWeight: '600' },

  meetCard: { marginHorizontal: 10, marginBottom: 12, borderRadius: 14, overflow: 'hidden', borderWidth: 1, borderColor: 'rgba(0,0,0,0.06)' },
  meetHeader: { flexDirection: 'row', alignItems: 'center', backgroundColor: COLORS.header, padding: 10 },
  meetNum: { color: '#fff', fontWeight: '700', backgroundColor: 'rgba(255,255,255,0.18)', borderRadius: 16, width: 26, height: 26, textAlign: 'center', lineHeight: 26, marginRight: 8 },
  meetTitle: { color: '#fff', fontWeight: '700', fontSize: 15, flex: 1 },
  meetBody: { padding: 12 },
  metaLine: { fontSize: 13, color: '#222', marginTop: 2 },
  metaLabel: { color: '#417602', fontWeight: '600' },

  fieldLabel: { marginTop: 10, color: '#b8312c', fontWeight: '600', fontSize: 12 },
  input: { borderWidth: 1, borderColor: '#ccc', borderRadius: 6, padding: 9, marginTop: 4, fontSize: 15, backgroundColor: '#fff' },
  row: { flexDirection: 'row', marginTop: 6 },
  smallBtn: { backgroundColor: COLORS.primary, paddingVertical: 9, paddingHorizontal: 14, borderRadius: 6 },
  smallBtnTxt: { color: '#fff', fontWeight: '600', fontSize: 13 },
  thumb: { width: 70, height: 70, borderRadius: 6, marginRight: 6, backgroundColor: '#ddd' },
  tip: { fontSize: 11, color: COLORS.muted, marginTop: 4 },

  varianceBox: { padding: 8, borderRadius: 6, marginTop: 6 },
  varianceTxt: { fontWeight: '600', fontSize: 13 },

  cancelLink: { marginTop: 12, alignItems: 'center' },

  submitBtn: { backgroundColor: COLORS.green, padding: 14, borderRadius: 8, alignItems: 'center', marginHorizontal: 10, marginTop: 6 },
  submitTxt: { color: '#fff', fontSize: 16, fontWeight: '700' },
});
