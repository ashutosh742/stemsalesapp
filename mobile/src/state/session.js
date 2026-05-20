// Lightweight in-memory session for the demo. Avoids context boilerplate.
// In production this would be a SecureStore-backed React Context.

import { ROLES, CURRENT_USER } from '../data/roles';

let _currentRole = CURRENT_USER.role; // default BD
const _listeners = new Set();

export function getRole() {
  return _currentRole;
}

export function getRoleConfig() {
  return ROLES[_currentRole];
}

export function setRole(roleId) {
  if (!ROLES[roleId]) return;
  _currentRole = roleId;
  _listeners.forEach(fn => fn(roleId));
}

export function subscribe(fn) {
  _listeners.add(fn);
  return () => _listeners.delete(fn);
}
