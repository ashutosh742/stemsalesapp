// AdvanceSettlementScreen.js
// BD submits ACTUAL EXPENSE against a previously DISBURSED travel advance.
// This is the missing reconciliation link in production stemapp:
//   travel_advance row gets cluster_apr + admin_apr + account_apr = 1,
//   cash is credited to user.ucash (disbursed), and then... nothing forces the BD
//   to come back with bills. Migration 009.2 closes that by adding
//   cash_expense.travel_advance_id, expense_actuals_log.travel_advance_id, and
//   the new /api/discipline/advance/settle endpoint that writes both rows and
//   marks the advance consumed in one transaction.
//
// Flow:
//   1. GET /api/discipline/advance/unsettled  -> list disbursed but unconsumed advances
//   2. Tap a card -> enter actual_spent, remarks, pick travel_expense_type chips, upload bills
//   3. POST multipart /api/discipline/advance/settle
//   4. Backend computes:
//        leftover = max(0, advance - actual)  -> stays in BD ucash (already credited at disbursement)
//        overflow = max(0, actual - advance)  -> debited from BD ucash (requires balance)
//        variance_pct = (actual - advance)/advance * 100
//        requires_dual_approval = abs(variance_pct) > 20
//      Writes cash_expense + expense_actuals_log + updates travel_advance.consumed_status='consumed'

import React, { useEffect, useMemo, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity,
  TextInput, Image, ActivityIndicator, Alert, RefreshControl,
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { api, getUnsettledAdvances, settleAdvance } from '../lib/api';
import { CURRENT_USER } from '../data/roles';

const COLORS = {
  bg: '#e3f2e1', card: '#ffffff', header: '#01434d',
  primary: '#1f5fbf', red: '#d0021b', amber: '#f6a623', green: '#1f9d55',
  muted: '#555',
};

const TRAVEL_TYPES = ['Bus','Train','Auto','Cab','Bike','Toll','Fuel','Hotel','Food','Other'];

function daysSince(iso) {
  if (!iso) return 0;
  const ms = Date.now() - new Date(iso).getTime();
  return Math.max(0, Math.floor(ms / 86400000));
}

export default function AdvanceSettlementScreen({ navigation }) {
  const user = CURRENT_USER;
  const [loading, setLoading]       = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [advances, setAdvances]     = useState([]);

  // Selected advance + form state
  const [active, setActive]         = useState(null);
  const [actual, setActual]         = useState('');
  const [remarks, setRemarks]       = useState('');
  const [travelTypes, setTravelTypes] = useState([]);
  const [photos, setPhotos]         = useState([]);
  const [submitting, setSubmitting] = useState(false);

  const load = async () => {
    setLoading(true);
    try {
      const r = await getUnsettledAdvances();
      setAdvances(r?.advances || []);
    } catch (e) { console.warn('unsettled load failed', e); }
    finally { setLoading(false); setRefreshing(false); }
  };
  useEffect(() => { load(); }, []);

  const startSettle = (adv) => {
    setActive(adv);
    setActual(''); setRemarks(''); setTravelTypes([]); setPhotos([]);
  };

  const toggleType = (t) =>
    setTravelTypes(arr => arr.includes(t) ? arr.filter(x => x !== t) : [...arr, t]);

  const captureBill = async () => {
    const perm = await ImagePicker.requestCameraPermissionsAsync();
    if (!perm.granted) { Alert.alert('Camera access needed for bill capture'); return; }
    const r = await ImagePicker.launchCameraAsync({ quality: 0.6, base64: false });
    if (r.canceled || !r.assets?.[0]) return;
    const a = r.assets[0];
    setPhotos(prev => [...prev, {
      uri: a.uri, name: `bill_settle_${active.id}_${Date.now()}.jpg`, type: 'image/jpeg',
    }]);
  };
  const pickBills = async () => {
    const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!perm.granted) { Alert.alert('Photo access needed'); return; }
    const r = await ImagePicker.launchImageLibraryAsync({
      allowsMultipleSelection: true, quality: 0.6,
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
    });
    if (r.canceled) return;
    const next = (r.assets || []).map((a, i) => ({
      uri: a.uri, name: `bill_settle_${active.id}_${Date.now()}_${i}.jpg`, type: 'image/jpeg',
    }));
    setPhotos(prev => [...prev, ...next]);
  };

  // Variance + leftover/overflow preview
  const preview = useMemo(() => {
    if (!active || !actual) return null;
    const a = parseInt(actual) || 0;
    const adv = Number(active.advance_amount) || 0;
    const variance = adv > 0 ? Math.round(((a - adv) / adv) * 100) : 0;
    const leftover = Math.max(0, adv - a);
    const overflow = Math.max(0, a - adv);
    return { a, adv, variance, leftover, overflow, dual: Math.abs(variance) > 20 };
  }, [active, actual]);

  const submit = async () => {
    if (!active) return;
    const a = parseInt(actual) || 0;
    if (a < 0) { Alert.alert('Enter a valid amount'); return; }
    if (!remarks.trim()) { Alert.alert('Expense remarks are required'); return; }
    if (!travelTypes.length) { Alert.alert('Pick at least one travel expense type'); return; }
    if (!photos.length) { Alert.alert('At least one bill photo is mandatory'); return; }
    setSubmitting(true);
    try {
      const form = new FormData();
      form.append('advance_id', String(active.id));
      form.append('actual_spent', String(a));
      form.append('expense_remarks', remarks);
      form.append('travel_expense_type', travelTypes.join(','));
      photos.forEach(p => form.append('bills[]', p));
      const r = await settleAdvance(form);
      if (!r?.ok) {
        Alert.alert('Settlement failed', r?.error || 'Server error');
        return;
      }
      const lines = [
        `Advance Rs ${r.advance_amount} settled with actual Rs ${r.actual_spent}.`,
        r.leftover_in_wallet ? `Rs ${r.leftover_in_wallet} leftover stays in your wallet.` : '',
        r.overflow_debited ? `Rs ${r.overflow_debited} overflow debited from your wallet.` : '',
        `Variance ${r.variance_pct}%.`,
        r.requires_dual_approval
          ? 'Flagged for CM + Accounts Officer dual approval (over 20 percent).'
          : 'Goes to CM for approval.',
      ].filter(Boolean).join('\n');
      Alert.alert('Submitted', lines);
      setActive(null);
      load();
    } catch (e) {
      Alert.alert('Network error', String(e));
    } finally { setSubmitting(false); }
  };

  if (loading) return <ActivityIndicator style={{ flex: 1 }} size="large" />;

  // Detail view
  if (active) {
    const aging = daysSince(active.disbursed_at);
    return (
      <ScrollView style={s.container}>
        <View style={s.headerBar}>
          <Text style={s.headerTxt}>Settle advance #{active.id}</Text>
        </View>

        <View style={s.card}>
          <Text style={s.lbl}>Advance amount</Text>
          <Text style={s.bigVal}>Rs {active.advance_amount}</Text>
          <Text style={s.muted}>Disbursed {aging} day(s) ago. Purpose: {active.purpose || '-'}</Text>
          {active.company_name ? (
            <Text style={s.muted}>For meeting: {active.company_name}</Text>
          ) : null}

          <Text style={[s.lbl, { marginTop: 14 }]}>Actual amount spent</Text>
          <TextInput
            style={s.input} keyboardType="number-pad"
            value={actual} onChangeText={t => setActual(t.replace(/[^0-9]/g,''))}
            placeholder="Rs 0"
          />

          {preview ? (
            <View style={[s.varBox, { backgroundColor: preview.dual ? '#ffe5e5' : '#e6f4ea' }]}>
              <Text style={[s.varTxt, { color: preview.dual ? COLORS.red : COLORS.green }]}>
                Variance {preview.variance > 0 ? '+' : ''}{preview.variance}%
                {preview.dual ? '  (needs CM + Accounts Officer dual approval)' : '  (within 20 percent)'}
              </Text>
              {preview.leftover > 0 && (
                <Text style={s.varSub}>Rs {preview.leftover} stays in your wallet (leftover).</Text>
              )}
              {preview.overflow > 0 && (
                <Text style={[s.varSub, { color: COLORS.red }]}>
                  Rs {preview.overflow} will be debited from your wallet (overflow). You must have balance.
                </Text>
              )}
            </View>
          ) : null}

          <Text style={s.lbl}>Travel expense type (select all that apply)</Text>
          <View style={s.chipsRow}>
            {TRAVEL_TYPES.map(t => (
              <TouchableOpacity
                key={t}
                style={[s.chip, travelTypes.includes(t) && s.chipOn]}
                onPress={() => toggleType(t)}
              >
                <Text style={[s.chipTxt, travelTypes.includes(t) && s.chipTxtOn]}>{t}</Text>
              </TouchableOpacity>
            ))}
          </View>

          <Text style={s.lbl}>Expense remarks</Text>
          <TextInput
            style={[s.input, { height: 80 }]} multiline
            placeholder="Where the money went, briefly"
            value={remarks} onChangeText={setRemarks}
          />

          <Text style={s.lbl}>Upload bills (mandatory)</Text>
          <View style={{ flexDirection: 'row' }}>
            <TouchableOpacity style={s.smallBtn} onPress={captureBill}>
              <Text style={s.smallBtnTxt}>Capture</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[s.smallBtn, { marginLeft: 8, backgroundColor: '#5b6cb0' }]} onPress={pickBills}>
              <Text style={s.smallBtnTxt}>Pick from gallery</Text>
            </TouchableOpacity>
          </View>
          {photos.length > 0 && (
            <ScrollView horizontal style={{ marginTop: 8 }}>
              {photos.map((p, i) => (
                <TouchableOpacity key={i} onLongPress={() => setPhotos(arr => arr.filter((_, j) => j !== i))}>
                  <Image source={{ uri: p.uri }} style={s.thumb} />
                </TouchableOpacity>
              ))}
            </ScrollView>
          )}
          {photos.length > 0 && (
            <Text style={s.tip}>Long press to remove. {photos.length} bill(s) attached.</Text>
          )}

          <TouchableOpacity
            style={[s.submitBtn, { opacity: submitting ? 0.5 : 1 }]}
            disabled={submitting}
            onPress={submit}
          >
            <Text style={s.submitTxt}>{submitting ? 'Submitting...' : 'Submit actuals against advance'}</Text>
          </TouchableOpacity>

          <TouchableOpacity style={{ marginTop: 14, alignItems: 'center' }} onPress={() => setActive(null)}>
            <Text style={{ color: COLORS.primary }}>Back to list</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={{ marginTop: 10, alignItems: 'center' }}
            onPress={() => navigation.navigate('AdvanceManagement')}
          >
            <Text style={{ color: COLORS.red }}>Meeting did not happen? Return advance instead</Text>
          </TouchableOpacity>
        </View>

        <View style={{ height: 30 }} />
      </ScrollView>
    );
  }

  // List view
  return (
    <ScrollView
      style={s.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} />}
    >
      <View style={s.headerBar}>
        <Text style={s.headerTxt}>Submit actuals against advance</Text>
      </View>

      <View style={s.banner}>
        <Text style={s.bannerTxt}>
          Every disbursed advance must end with bills and an actual-spend submission.
          Leftover stays in your wallet; overspend is debited from your wallet.
        </Text>
      </View>

      {advances.length === 0 ? (
        <View style={s.noRec}>
          <Text style={s.noRecTxt}>No unsettled advances</Text>
          <Text style={s.muted}>You are clear. New advances appear here right after disbursement.</Text>
        </View>
      ) : (
        advances.map(a => {
          const aging = daysSince(a.disbursed_at);
          const old = aging >= 3;
          return (
            <TouchableOpacity key={a.id} style={[s.advCard, old && s.advCardOld]} onPress={() => startSettle(a)}>
              <View style={{ flex: 1 }}>
                <Text style={s.advTitle}>Rs {a.advance_amount}  ·  #{a.id}</Text>
                <Text style={s.muted} numberOfLines={1}>{a.purpose || 'Meeting advance'}</Text>
                {a.company_name && (
                  <Text style={s.muted} numberOfLines={1}>For: {a.company_name}</Text>
                )}
                <Text style={[s.muted, { color: old ? COLORS.red : COLORS.amber, marginTop: 4 }]}>
                  Disbursed {aging} day(s) ago{old ? '  ·  OVERDUE' : ''}
                </Text>
              </View>
              <Text style={s.advArrow}>›</Text>
            </TouchableOpacity>
          );
        })
      )}
      <View style={{ height: 30 }} />
    </ScrollView>
  );
}

const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.bg },
  headerBar: { backgroundColor: COLORS.header, padding: 14 },
  headerTxt: { color: '#fff', fontSize: 16, fontWeight: '700', textAlign: 'center' },
  banner: { backgroundColor: '#fff5e6', padding: 12, margin: 10, borderRadius: 8 },
  bannerTxt: { color: '#7a4f00', fontSize: 13 },
  card: { backgroundColor: COLORS.card, padding: 14, margin: 10, borderRadius: 10 },

  noRec: { backgroundColor: '#fff', padding: 30, margin: 14, borderRadius: 12, alignItems: 'center' },
  noRecTxt: { fontSize: 18, fontWeight: '600', color: COLORS.green, marginBottom: 6 },
  muted: { color: COLORS.muted, fontSize: 13 },

  lbl: { color: COLORS.muted, fontSize: 12, marginTop: 10 },
  bigVal: { fontSize: 22, fontWeight: '700', color: '#222', marginTop: 2 },
  input: { borderWidth: 1, borderColor: '#ccc', borderRadius: 6, padding: 9, marginTop: 4, fontSize: 16, backgroundColor: '#fff' },

  varBox: { padding: 10, borderRadius: 8, marginTop: 10 },
  varTxt: { fontWeight: '700', fontSize: 14 },
  varSub: { fontSize: 12, marginTop: 4, color: COLORS.muted },

  chipsRow: { flexDirection: 'row', flexWrap: 'wrap', marginTop: 4 },
  chip: { borderWidth: 1, borderColor: '#ccc', borderRadius: 16, paddingVertical: 6, paddingHorizontal: 10, marginRight: 6, marginBottom: 6, backgroundColor: '#fff' },
  chipOn: { backgroundColor: COLORS.header, borderColor: COLORS.header },
  chipTxt: { color: '#444', fontSize: 12 },
  chipTxtOn: { color: '#fff', fontWeight: '600' },

  smallBtn: { backgroundColor: COLORS.primary, paddingVertical: 9, paddingHorizontal: 14, borderRadius: 6, marginTop: 6 },
  smallBtnTxt: { color: '#fff', fontWeight: '600', fontSize: 13 },
  thumb: { width: 70, height: 70, borderRadius: 6, marginRight: 6, backgroundColor: '#ddd' },
  tip: { fontSize: 11, color: COLORS.muted, marginTop: 4 },

  submitBtn: { backgroundColor: COLORS.green, padding: 14, borderRadius: 8, alignItems: 'center', marginTop: 16 },
  submitTxt: { color: '#fff', fontSize: 16, fontWeight: '700' },

  advCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', padding: 14, marginHorizontal: 10, marginBottom: 8, borderRadius: 10, borderLeftWidth: 4, borderLeftColor: COLORS.amber },
  advCardOld: { borderLeftColor: COLORS.red },
  advTitle: { fontSize: 16, fontWeight: '700', color: '#1a1a1a' },
  advArrow: { fontSize: 28, color: '#aaa' },
});
