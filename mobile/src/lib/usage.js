/**
 * usage.js — silent UsageAnalytics client
 *
 * No UI. Plug into App.js once. Captures:
 *   - session open/close on app foreground/background
 *   - heartbeat every 30s while foregrounded
 *   - screen open/close on every navigation event
 *   - discrete action events (lead_open, mom_draft, plan_submit, etc.)
 *
 * Privacy: timestamps are sent at second resolution; the backend's
 * minute_bucket generated column enforces 1-minute storage.
 *
 * Offline tolerance: failed POSTs are queued in AsyncStorage and
 * drained on next successful network call.
 */

import AsyncStorage from '@react-native-async-storage/async-storage';
import { AppState, Platform } from 'react-native';
import { api } from './api';

const QUEUE_KEY = '@stem.usage.queue';
let SESSION_ID = null;
let HEARTBEAT_T = null;
let CURRENT_VIEW_ID = null;
let CURRENT_SCREEN = null;

const HEARTBEAT_INTERVAL_MS = 30 * 1000;

// -------------------------------------------------------------
// Queue (offline tolerance)
// -------------------------------------------------------------
async function enqueue(call) {
  try {
    const raw = await AsyncStorage.getItem(QUEUE_KEY);
    const q = raw ? JSON.parse(raw) : [];
    q.push({ ...call, at: Date.now() });
    await AsyncStorage.setItem(QUEUE_KEY, JSON.stringify(q.slice(-500)));
  } catch (_) {}
}

async function drain() {
  try {
    const raw = await AsyncStorage.getItem(QUEUE_KEY);
    if (!raw) return;
    const q = JSON.parse(raw);
    if (!q.length) return;
    const remaining = [];
    for (const item of q) {
      try { await api.post(item.path, item.body); }
      catch (_) { remaining.push(item); }
    }
    await AsyncStorage.setItem(QUEUE_KEY, JSON.stringify(remaining));
  } catch (_) {}
}

async function safePost(path, body) {
  try { return await api.post(path, body); }
  catch (e) { await enqueue({ path, body }); return null; }
}

// -------------------------------------------------------------
// Session
// -------------------------------------------------------------
export async function startSession({ appVersion = '2.0.0', deviceId = null } = {}) {
  if (SESSION_ID) return SESSION_ID;
  const r = await safePost('/usage/api_open_session', {
    platform: Platform.OS, app_version: appVersion, device_id: deviceId,
  });
  if (r && r.session_id) {
    SESSION_ID = r.session_id;
    _startHeartbeat();
  }
  drain(); // opportunistic flush
  return SESSION_ID;
}

export async function endSession() {
  if (!SESSION_ID) return;
  _stopHeartbeat();
  if (CURRENT_VIEW_ID) await closeScreen();
  await safePost('/usage/api_close_session', { session_id: SESSION_ID });
  SESSION_ID = null;
}

function _startHeartbeat() {
  _stopHeartbeat();
  HEARTBEAT_T = setInterval(() => {
    if (SESSION_ID) safePost('/usage/api_heartbeat', { session_id: SESSION_ID });
  }, HEARTBEAT_INTERVAL_MS);
}

function _stopHeartbeat() {
  if (HEARTBEAT_T) { clearInterval(HEARTBEAT_T); HEARTBEAT_T = null; }
}

// -------------------------------------------------------------
// Screen views
// -------------------------------------------------------------
export async function openScreen(screen, agent = null) {
  if (!SESSION_ID) await startSession();
  if (CURRENT_VIEW_ID) await closeScreen();
  CURRENT_SCREEN = screen;
  const r = await safePost('/usage/api_screen_open', {
    session_id: SESSION_ID, screen, agent,
  });
  CURRENT_VIEW_ID = r && r.view_id ? r.view_id : null;
}

export async function closeScreen() {
  if (!CURRENT_VIEW_ID) return;
  await safePost('/usage/api_screen_close', { view_id: CURRENT_VIEW_ID });
  CURRENT_VIEW_ID = null;
  CURRENT_SCREEN = null;
}

// -------------------------------------------------------------
// Actions
// -------------------------------------------------------------
export async function logAction(action, { targetType = null, targetId = null, meta = null } = {}) {
  await safePost('/usage/api_record_action', {
    action, target_type: targetType, target_id: targetId, meta,
  });
}

// -------------------------------------------------------------
// AppState wiring — call wireAppState() once at app boot
// -------------------------------------------------------------
export function wireAppState() {
  AppState.addEventListener('change', (next) => {
    if (next === 'active')  startSession();
    if (next === 'background' || next === 'inactive') endSession();
  });
}

// -------------------------------------------------------------
// React Navigation hook — attach to NavigationContainer onStateChange
// -------------------------------------------------------------
export function makeNavListener() {
  return (state) => {
    if (!state) return;
    const route = state.routes[state.index];
    if (route && route.name && route.name !== CURRENT_SCREEN) {
      openScreen(route.name);
    }
  };
}

export default { startSession, endSession, openScreen, closeScreen,
                 logAction, wireAppState, makeNavListener };
