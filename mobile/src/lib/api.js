// API client for stemapp.in
// Centralizes:
//   - base URL switching (dev / staging / prod)
//   - JWT storage + retrieval (AsyncStorage)
//   - automatic Authorization header
//   - JSON request/response handling
//   - graceful demo-mode fallback when no token / no network
//
// Switch screens to live data by importing from './lib/api' instead of
// '../data/cm' and calling the endpoints below.

import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform } from 'react-native';

// ─── Config ───────────────────────────────────────────────────────
const ENV = {
  prod:    { base: 'https://stemapp.in',          name: 'production'  },
  staging: { base: 'https://stagingstemopp.in',   name: 'staging'     },
  dev:     { base: Platform.OS === 'android' ? 'http://10.0.2.2:8080' : 'http://localhost:8080', name: 'dev' },
};
export const ACTIVE_ENV = ENV.staging;  // change for staging/dev
const BASE = ACTIVE_ENV.base;

// ─── Token storage ────────────────────────────────────────────────
const TOKEN_KEY = '@stem.jwt';
const USER_KEY  = '@stem.user';

export const saveSession = async (token, user) => {
  await AsyncStorage.multiSet([[TOKEN_KEY, token], [USER_KEY, JSON.stringify(user)]]);
};
export const clearSession = async () => {
  await AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY]);
};
export const getToken = async () => AsyncStorage.getItem(TOKEN_KEY);
export const getUser  = async () => {
  const raw = await AsyncStorage.getItem(USER_KEY);
  return raw ? JSON.parse(raw) : null;
};

// ─── Low-level fetch ──────────────────────────────────────────────
class ApiError extends Error {
  constructor(message, status, body) { super(message); this.status = status; this.body = body; }
}

const request = async (method, path, { body, query, timeoutMs = 15000 } = {}) => {
  const token = await getToken();
  const headers = { 'Content-Type': 'application/json' };
  if (token) headers.Authorization = `Bearer ${token}`;

  let url = BASE + path;
  if (query && Object.keys(query).length) {
    url += '?' + Object.entries(query).map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`).join('&');
  }

  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), timeoutMs);
  try {
    const res = await fetch(url, {
      method, headers,
      body: body ? JSON.stringify(body) : undefined,
      signal: controller.signal,
    });
    const text = await res.text();
    const json = text ? safeParse(text) : null;
    if (!res.ok) throw new ApiError(json?.error || `HTTP ${res.status}`, res.status, json);
    return json;
  } finally {
    clearTimeout(timer);
  }
};

const safeParse = (t) => { try { return JSON.parse(t); } catch { return null; } };

// ─── Endpoints ────────────────────────────────────────────────────
// Auth
export const requestOtp = (mobile)        => request('POST', '/auth/api_request_otp', { body: { mobile } });
export const login      = (mobile, otp)   => request('POST', '/auth/api_login',       { body: { mobile, otp } });
export const me         = ()              => request('GET',  '/auth/api_me');

// Task Efficiency
export const getMyEfficiency       = (days = 7) => request('GET', '/efficiency/api_get_bd_score', { query: { days } });
export const getClusterEfficiency  = (date)     => request('GET', '/efficiency/api_get_cluster_rollup', { query: { date } });
export const tagTaskOutcome        = (planner_id, outcome, notes) =>
  request('POST', '/efficiency/api_tag_outcome', { body: { planner_id, outcome, notes } });

// Research candidates (NewLead inbox)
export const getCandidates        = (limit = 10)     => request('GET',  '/research/api_candidates', { query: { limit } });
export const acceptCandidate      = (id, overrides)  => request('POST', '/research/api_accept',     { body: { candidate_id: id, overrides } });
export const dismissCandidate     = (id, reason)     => request('POST', '/research/api_dismiss',    { body: { candidate_id: id, reason } });

// Funnel + MoM (existing endpoints)
export const getBdFunnel          = (bd_id) => request('GET', '/agent/anaya/funnel', { query: { bd_id } });
export const draftMom             = (lead_id, transcript, template) =>
  request('POST', '/agent/mom/draft', { body: { lead_id, transcript, template } });

// ─── Discipline + Expense Accountability (migration 008 + 009) ────
export const getBdDisciplineScore = (user_id, date) =>
  request('GET', '/api/discipline/bd_score', { query: { user_id, date } });
export const getDisciplineNarrative = (user_id, date) =>
  request('GET', '/api/discipline/narrative', { query: { user_id, date } });

// Cancellation
export const getCancellationCategories = () => request('GET', '/api/discipline/cancel/categories');
export const getCancellationAudit      = (days = 7) => request('GET', '/api/discipline/cancel/audit', { query: { days } });
export const getUnreturnedAdvances     = (days = 7) => request('GET', '/api/discipline/cancel/unreturned_advances', { query: { days } });
export const cancelMeeting             = (payload) => request('POST', '/api/discipline/cancel/meeting', { body: payload });

// Expense actuals + dual approval
export const getCmExpenseQueue         = () => request('GET',  '/api/discipline/expense/cm_queue');
export const getAoExpenseQueue         = () => request('GET',  '/api/discipline/expense/ao_queue');
export const cmApproveExpense          = (log_id, remarks) => request('POST', '/api/discipline/expense/cm_approve', { body: { log_id, remarks } });
export const aoApproveExpense          = (log_id, remarks) => request('POST', '/api/discipline/expense/ao_approve', { body: { log_id, remarks } });
export const getExpenseGateCheck       = (plan_date) => request('GET', '/api/discipline/expense/gate_check', { query: plan_date ? { plan_date } : {} });
export const submitExpenseActuals      = async (form) => {
  const token = await getToken();
  const headers = {};
  if (token) headers.Authorization = `Bearer ${token}`;
  const res = await fetch(BASE + '/api/discipline/expense/submit', { method: 'POST', headers, body: form });
  const text = await res.text();
  return text ? safeParse(text) : null;
};

// Generic helpers used by the new screens (api.get/api.post)
export const api = {
  get:  (path) => request('GET',  path),
  post: (path, body) => request('POST', path, { body }),
};
export const apiUpload = async (path, formData) => {
  const token = await getToken();
  const headers = {};
  if (token) headers.Authorization = `Bearer ${token}`;
  const res = await fetch(BASE + path, { method: 'POST', headers, body: formData });
  const text = await res.text();
  return text ? safeParse(text) : null;
};

// ─── Advance Management (migration 009 + Mumbai pilot) ───────────
export const requestAdvance      = (event_id, amount, purpose) =>
  request('POST', '/api/discipline/advance/request', { body: { event_id, amount, purpose } });
export const approveAdvance      = (advance_id, role, action, remarks) =>
  request('POST', '/api/discipline/advance/approve', { body: { advance_id, role, action, remarks } });
export const consumeAdvance      = (advance_id, actual_spent) =>
  request('POST', '/api/discipline/advance/consume', { body: { advance_id, actual_spent } });
export const returnAdvance       = (advance_id, reason) =>
  request('POST', '/api/discipline/advance/return',  { body: { advance_id, reason } });
export const getMyAdvances       = (status = 'all', days = 30) =>
  request('GET',  '/api/discipline/advance/my', { query: { status, days } });
export const getAdvanceQueue     = (role = 'cluster') =>
  request('GET',  '/api/discipline/advance/queue', { query: { role } });

// Advance settlement (migration 009.2 - BD submits actual spend + bills against disbursed advance)
export const getUnsettledAdvances = () => request('GET', '/api/discipline/advance/unsettled');
export const settleAdvance        = async (form) => {
  const token = await getToken();
  const headers = {};
  if (token) headers.Authorization = `Bearer ${token}`;
  const res = await fetch(BASE + '/api/discipline/advance/settle', { method: 'POST', headers, body: form });
  const text = await res.text();
  return text ? safeParse(text) : null;
};

// ─── Demo-mode fallback ───────────────────────────────────────────
// When token is missing or network is dead, screens can call this helper
// to keep working with the bundled demo data.
export const tryLiveOrDemo = async (liveFn, demoData) => {
  try {
    const token = await getToken();
    if (!token) return { source: 'demo', data: demoData };
    const data = await liveFn();
    return { source: 'live', data };
  } catch (err) {
    console.warn('[api] falling back to demo:', err.message);
    return { source: 'demo', data: demoData, error: err };
  }
};
