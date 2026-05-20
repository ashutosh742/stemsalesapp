/**
 * SecureContactCard
 * ---------------------------------------------------------------------
 * Renders a single contact row with masked phone/email by default.
 * Tapping "Reveal" calls /contact/api_reveal — every tap is audit-logged
 * server-side, and the daily cap is enforced server-side too.
 *
 * Text fields are non-selectable (selectable={false}) and the parent
 * screen should call enableSecureScreen() in its focus handler to engage
 * FLAG_SECURE on Android.
 *
 * Props:
 *   contact: { id, name, designation, phone, email, phone_masked, email_masked }
 *   onReveal?: (field, value) => void   // optional callback for analytics
 */
import React, { useState, useCallback } from 'react';
import { View, Text, Pressable, StyleSheet, Alert } from 'react-native';
import { revealField, RevealCapError, ForbiddenError } from '../lib/secureContact';

export default function SecureContactCard({ contact, onReveal }) {
  const [phone, setPhone] = useState(contact.phone);
  const [email, setEmail] = useState(contact.email);
  const [phoneMasked, setPhoneMasked] = useState(contact.phone_masked);
  const [emailMasked, setEmailMasked] = useState(contact.email_masked);
  const [busy, setBusy] = useState(null);

  const doReveal = useCallback(async (field) => {
    setBusy(field);
    try {
      const out = await revealField(contact.id, field);
      if (field === 'phone') { setPhone(out.value); setPhoneMasked(false); }
      else                   { setEmail(out.value); setEmailMasked(false); }
      onReveal && onReveal(field, out.value);
      if (out.remaining_today != null && out.remaining_today <= 5) {
        Alert.alert('Reveal cap nearly reached',
          `You have ${out.remaining_today} reveals left today. The CEO is alerted at cap.`);
      }
    } catch (e) {
      if (e instanceof RevealCapError) {
        Alert.alert('Daily reveal cap reached',
          `You have revealed ${e.used} of ${e.cap} contacts today. ` +
          `Further reveals are blocked until tomorrow. The CEO has been notified.`);
      } else if (e instanceof ForbiddenError) {
        Alert.alert('Not permitted',
          'You do not have access to this contact. Only the assigned BD, ' +
          'the cluster CM, the Admin, and the CEO can view it.');
      } else {
        Alert.alert('Reveal failed', e.message || 'Network error');
      }
    } finally {
      setBusy(null);
    }
  }, [contact.id, onReveal]);

  return (
    <View style={styles.card}>
      <Text style={styles.name}  selectable={false}>{contact.name}</Text>
      <Text style={styles.desig} selectable={false}>{contact.designation || '—'}</Text>

      <FieldRow
        label="Phone"
        value={phone}
        masked={phoneMasked}
        busy={busy === 'phone'}
        onReveal={() => doReveal('phone')}
      />
      <FieldRow
        label="Email"
        value={email}
        masked={emailMasked}
        busy={busy === 'email'}
        onReveal={() => doReveal('email')}
      />
    </View>
  );
}

function FieldRow({ label, value, masked, busy, onReveal }) {
  return (
    <View style={styles.row}>
      <Text style={styles.label} selectable={false}>{label}</Text>
      <Text style={[styles.val, masked && styles.muted]} selectable={false}>
        {value || '—'}
      </Text>
      {masked && (
        <Pressable onPress={onReveal} disabled={busy} style={styles.revealBtn}>
          <Text style={styles.revealTxt}>{busy ? '...' : 'Reveal'}</Text>
        </Pressable>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff', borderRadius: 12, padding: 14, marginVertical: 6,
    borderWidth: 1, borderColor: '#E5E7EB',
  },
  name: { fontSize: 16, fontWeight: '600', color: '#0F172A' },
  desig:{ fontSize: 13, color: '#64748B', marginBottom: 8 },
  row:  { flexDirection: 'row', alignItems: 'center', paddingVertical: 4 },
  label:{ width: 60, fontSize: 13, color: '#64748B' },
  val:  { flex: 1, fontSize: 14, color: '#0F172A' },
  muted:{ color: '#94A3B8', fontStyle: 'italic' },
  revealBtn: {
    paddingHorizontal: 10, paddingVertical: 4,
    backgroundColor: '#0F172A', borderRadius: 6,
  },
  revealTxt: { color: '#fff', fontSize: 12, fontWeight: '600' },
});
