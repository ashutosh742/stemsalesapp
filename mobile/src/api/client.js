// STEM CRMApp API client — wraps stemapp.in CodeIgniter endpoints
// Auth is session-cookie based (PHPSESSID set by /Menu/login).
import * as SecureStore from 'expo-secure-store';

export const API_BASE = 'https://stemapp.in/index.php';

let SESSION_COOKIE = null;

export async function loadSession() {
  if (SESSION_COOKIE) return SESSION_COOKIE;
  const v = await SecureStore.getItemAsync('stem_session');
  SESSION_COOKIE = v;
  return v;
}

export async function saveSession(cookie) {
  SESSION_COOKIE = cookie;
  await SecureStore.setItemAsync('stem_session', cookie);
}

export async function clearSession() {
  SESSION_COOKIE = null;
  await SecureStore.deleteItemAsync('stem_session');
}

async function request(path, { method = 'GET', body, headers = {} } = {}) {
  const url = `${API_BASE}${path}`;
  const opts = {
    method,
    headers: {
      Accept: 'application/json',
      ...(body && !(body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
      ...(SESSION_COOKIE ? { Cookie: SESSION_COOKIE } : {}),
      ...headers,
    },
    credentials: 'include',
  };
  if (body) opts.body = body instanceof FormData ? body : JSON.stringify(body);

  const res = await fetch(url, opts);
  const setCookie = res.headers.get('set-cookie');
  if (setCookie && setCookie.includes('PHPSESSID')) {
    await saveSession(setCookie.split(';')[0]);
  }
  if (!res.ok) throw new Error(`${method} ${path} → ${res.status}`);
  const txt = await res.text();
  try { return JSON.parse(txt); } catch { return { raw: txt }; }
}

// ===== AUTH =====
export async function login(userid, password) {
  const form = new FormData();
  form.append('userid', userid);
  form.append('password', password);
  return request('/Menu/login', { method: 'POST', body: form });
}

export async function getSession() {
  return request('/Menu/api_session'); // add this thin JSON wrapper to your CodeIgniter app
}

export async function logout() {
  await request('/Menu/logout', { method: 'POST' });
  await clearSession();
}

// ===== AGENT TOOLS (map to AIAgents/* models) =====
// Each call hits Chat::api_run_tool which dispatches to the right model.
// See stem-mobile-preview/API_MAPPING.md for the full table.
export const tools = {
  // Anaya
  getDayPack: (date) => request(`/Anaya_reports/api_day_pack?date=${date}`),

  // Generic dispatcher for everything else
  runTool: (tool, params = {}) => request('/chat/api_run_tool', { method: 'POST', body: { tool, params } }),

  // Convenience wrappers
  getBdFunnel:        (userId) => tools.runTool('get_bd_funnel', { user_id: userId }),
  getBdDiscipline:    (userId, days = 7) => tools.runTool('get_bd_discipline', { user_id: userId, days }),
  findSimilarLeads:   (initCallId, k = 3) => tools.runTool('find_similar_leads', { init_call_id: initCallId, k }),
  getRecentMoms:      (userId) => tools.runTool('get_recent_moms', { user_id: userId }),
  scheduleFollowup:   (leadId, datetime) => tools.runTool('schedule_followup', { lead_id: leadId, datetime }),
  getFunnelReport:    (params) => tools.runTool('get_funnel_report', params),
  getClosurePipeline: (params) => tools.runTool('get_closure_pipeline', params),
  findDormantLeads:   (days = 30) => tools.runTool('find_dormant_leads', { min_days: days }),

  // MoM Drafter
  draftMom:       (taskId, transcript) => request('/MomController/api_draft', { method: 'POST', body: { task_id: taskId, transcript } }),
  saveMom:        (payload) => request('/MomController/api_save', { method: 'POST', body: payload }),
  transcribeAudio:(formData) => request('/MomController/api_transcribe', { method: 'POST', body: formData }),
};

// ===== CRM DATA =====
export const crm = {
  getLeads:   (filter = {}) => request(`/Reports/api_leads?` + new URLSearchParams(filter)),
  getSchools: (filter = {}) => request(`/Management/api_schools?` + new URLSearchParams(filter)),
  getProjects:(filter = {}) => request(`/Reports/api_projects?` + new URLSearchParams(filter)),
};

// Set true while developing without the backend wired up
export const USE_MOCKS = true;

// ===== GENERIC AXIOS-LIKE WRAPPER =====
// Used by Contact Protection screens (SecureContactCard, ExportRequestScreen,
// ExportApprovalQueueScreen, AccessAuditScreen) which call rest paths
// like '/contact/api_get_for_lead/123'.
//
// Each method returns { status, data } so screens can check res.status === 429
// or res.status === 403 without needing to catch on every call.
async function safeRequest(path, opts) {
  try {
    const data = await request(path, opts);
    return { status: 200, data };
  } catch (e) {
    // Try to recover the status code from "GET /x → 429" message
    const m = String(e.message || '').match(/→\s*(\d+)/);
    const status = m ? parseInt(m[1], 10) : 500;
    return { status, data: { error: e.message } };
  }
}

export const api = {
  get:    (path, { params } = {}) => {
    const qs = params ? '?' + new URLSearchParams(params).toString() : '';
    return safeRequest(path + qs, { method: 'GET' });
  },
  post:   (path, body) => safeRequest(path, { method: 'POST', body }),
  put:    (path, body) => safeRequest(path, { method: 'PUT',  body }),
  delete: (path)       => safeRequest(path, { method: 'DELETE' }),
};
