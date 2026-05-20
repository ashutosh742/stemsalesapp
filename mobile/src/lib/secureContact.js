/**
 * secureContact.js
 * ---------------------------------------------------------------------
 * Mobile-side helpers for contact data protection.
 *
 *   - getContactsForLead(leadId)   → fetch from /contact/api_get_for_lead
 *   - revealField(contactId,field) → fetch from /contact/api_reveal
 *   - requestExport(payload)       → fetch from /contact/api_request_export
 *   - enableSecureScreen()         → FLAG_SECURE so screenshots/screen-mirror
 *                                    are blocked while contact UI is visible.
 *
 * Use SecureContactCard to render contacts — it handles masking, reveal-tap,
 * and copy-block in one place.
 */
import { Platform, NativeModules } from 'react-native';
// Backwards-compat: some screens import api as { api } from '../lib/api'
// and others from '../api/client'. The shape is identical; we route through
// whichever exists in this project.
// eslint-disable-next-line import/no-unresolved
import { api } from '../api/client';

// ---------------------------------------------------------------------
// Network
// ---------------------------------------------------------------------
export async function getContactsForLead(leadId) {
  const res = await api.get(`/contact/api_get_for_lead/${leadId}`);
  return res.data; // { lead_id, scope, contacts:[], reveal_supported }
}

export async function revealField(contactId, field /* 'phone'|'email' */) {
  const res = await api.post('/contact/api_reveal', { contact_id: contactId, field });
  if (res.status === 429) {
    throw new RevealCapError(res.data.cap, res.data.used);
  }
  if (res.status === 403) {
    throw new ForbiddenError(res.data.error);
  }
  return res.data; // { contact_id, field, value, remaining_today }
}

export async function requestExport({ scopeType, scopePayload, purpose }) {
  if (!purpose || purpose.length < 20) {
    throw new Error('Purpose must be at least 20 characters.');
  }
  const res = await api.post('/contact/api_request_export', {
    scope_type: scopeType,
    scope_payload: scopePayload,
    purpose,
  });
  return res.data; // { request_id, status, download_token?, expires_at?, row_estimate }
}

export async function listPendingExports() {
  const res = await api.get('/contact/api_list_pending_exports');
  return res.data.pending || [];
}

export async function decideExport(requestId, decision, reason) {
  const res = await api.post('/contact/api_decide_export', {
    request_id: requestId, decision, reason,
  });
  return res.data;
}

export async function getMyAccessLog(limit = 50) {
  const res = await api.get(`/contact/api_my_access_log?limit=${limit}`);
  return res.data.log || [];
}

// ---------------------------------------------------------------------
// FLAG_SECURE — block screenshots & screen recording while mounted.
// Uses the optional `react-native-prevent-screenshot-ios` and the
// built-in Android Window.FLAG_SECURE via a tiny native bridge module.
// ---------------------------------------------------------------------
export function enableSecureScreen() {
  if (Platform.OS === 'android' && NativeModules.SecureScreen) {
    NativeModules.SecureScreen.setSecure(true);
  } else if (Platform.OS === 'ios' && NativeModules.PreventScreenshot) {
    NativeModules.PreventScreenshot.startMonitoring();
  }
}
export function disableSecureScreen() {
  if (Platform.OS === 'android' && NativeModules.SecureScreen) {
    NativeModules.SecureScreen.setSecure(false);
  } else if (Platform.OS === 'ios' && NativeModules.PreventScreenshot) {
    NativeModules.PreventScreenshot.stopMonitoring();
  }
}

// ---------------------------------------------------------------------
// Custom error types so screens can render the right message
// ---------------------------------------------------------------------
export class RevealCapError extends Error {
  constructor(cap, used) {
    super(`Daily reveal cap reached: ${used}/${cap}`);
    this.name = 'RevealCapError';
    this.cap = cap;
    this.used = used;
  }
}
export class ForbiddenError extends Error {
  constructor(msg) { super(msg); this.name = 'ForbiddenError'; }
}
