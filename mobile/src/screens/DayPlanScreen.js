// DayPlanScreen v2 - post-approval execution screen with locked day shape.
// rev 10 - read-only execution view. Plan WRITE path is NextDayPlannerV2Screen (POST /api/planner/v2/submit). Do not add task creation here.
//
// Day shape (migration 017_4):
//   10:00 to 15:00 = MANUAL band - BD field activities (includes lunch)
//   15:00 to 17:30 = AUTO-TASK band - system-seeded calls / emails / MoM
//   17:30 to 18:30 = NEXT-DAY PLAN band - BD plans tomorrow (auto-redirect)
//   18:30          = HARD CUTOFF
//
// Hard locks (sp_check_band_lock):
//   - No actiontype 3/4 (physical/barge) when WFO
//   - In auto band only 1/2/13 (call/email/mom) allowed
//   - In plan_window no field activity, only plan submission
//   - Outside 10:00-18:30 -> all action blocked
//
// Reuses production minute math: 1/5/8/9/10/15=5m, 2/6=10m, 3/4/12=30m, 7=15m, 11/13/14=2m

import React, { useState, useMemo, useEffect } from 'react';
import {
  View, Text, ScrollView, Pressable, StyleSheet, StatusBar, Alert,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { colors } from '../theme/colors';
import { TODAY_PLAN, PLAN_STATUS, TASK_TYPES } from '../data/plans';
import { CURRENT_USER } from '../data/roles';
import ProgressionScorecardTile from './ProgressionScorecardTile';
import StuckLeadsTile from './StuckLeadsTile';
import MoMBlockersTile from './MoMBlockersTile';
import ConversionLeaderboardTile from './ConversionLeaderboardTile';
import ApplauseBanner from './ApplauseBanner';

// ---- band config (matches autotask_time defaults from migration 017_4) ----
const BANDS = {
  manual:      { start: '10:00', end: '15:00', label: 'Field activities',  color: '#0969da', icon: 'walk' },
  auto:        { start: '15:00', end: '17:30', label: 'Auto-tasks',        color: '#8250df', icon: 'flash' },
  plan_window: { start: '17:30', end: '18:30', label: 'Plan tomorrow',     color: '#bf8700', icon: 'calendar' },
};
const DAY_CUTOFF = '18:30';

// minute budget per actiontype - mirrors Menu.php addplantask12
const ACTION_MIN = {
  1: 5, 5: 5, 8: 5, 9: 5, 10: 5, 15: 5,
  2: 10, 6: 10,
  3: 30, 4: 30, 12: 30,
  7: 15,
  11: 2, 13: 2, 14: 2,
};
function actionMin(t) { return ACTION_MIN[t] || 5; }

// pick current band based on wall clock
function currentBand(now = new Date()) {
  const t = now.toTimeString().slice(0, 5);
  if (t < BANDS.manual.start) return 'before_day';
  if (t < BANDS.manual.end)   return 'manual';
  if (t < BANDS.auto.end)     return 'auto';
  if (t < BANDS.plan_window.end) return 'plan_window';
  return 'after_day';
}

// check if a tap is allowed - mirrors sp_check_band_lock
function bandLockCheck({ workMode, currentBand: band, actionTypeId }) {
  if (workMode === 'wfo' && [3, 4].includes(actionTypeId)) {
    return { allowed: false, reason: 'WFO mode blocks physical and barge meetings. Switch to virtual call (actiontype 12).' };
  }
  if (band === 'manual') return { allowed: true };
  if (band === 'auto') {
    if (![1, 2, 13].includes(actionTypeId)) {
      return { allowed: false, reason: 'Auto-task band (3 PM to 5:30 PM) only allows calls, emails, and MoM. Field meetings cannot be logged here.' };
    }
    return { allowed: true };
  }
  if (band === 'plan_window') {
    return { allowed: false, reason: 'Plan window (5:30 PM to 6:30 PM) is for next-day planning. No field activity allowed.' };
  }
  return { allowed: false, reason: 'Day is closed (after 6:30 PM). Submit tomorrows plan in the BD audit dashboard.' };
}

function StatusPill({ status }) {
  const meta = PLAN_STATUS[status] || PLAN_STATUS.DRAFT;
  return (
    <View style={[s.pill, { backgroundColor: meta.color + '22', borderColor: meta.color }]}>
      <View style={[s.pillDot, { backgroundColor: meta.color }]} />
      <Text style={[s.pillText, { color: meta.color }]}>{meta.label.toUpperCase()}</Text>
    </View>
  );
}

function BandStrip({ band, manualUsed, manualBudget, autoCount, autoBudget, planSubmitted }) {
  const isCurrent = (b) => band === b;
  const pct = (used, total) => total > 0 ? Math.min(100, Math.round((used / total) * 100)) : 0;
  return (
    <View style={s.bandStrip}>
      <Text style={s.bandStripTitle}>Day shape (locked by CM approval)</Text>
      {/* manual */}
      <View style={[s.bandRow, isCurrent('manual') && s.bandRowLive]}>
        <View style={[s.bandDot, { backgroundColor: BANDS.manual.color }]} />
        <View style={{ flex: 1 }}>
          <View style={s.bandHead}>
            <Text style={s.bandLabel}>{BANDS.manual.start} to {BANDS.manual.end}  {BANDS.manual.label}</Text>
            {isCurrent('manual') && <Text style={s.liveTag}>LIVE</Text>}
          </View>
          <View style={s.bandBar}>
            <View style={[s.bandFill, { width: pct(manualUsed, manualBudget) + '%', backgroundColor: BANDS.manual.color }]} />
          </View>
          <Text style={s.bandSub}>{manualUsed} of {manualBudget} min used</Text>
        </View>
      </View>
      {/* auto */}
      <View style={[s.bandRow, isCurrent('auto') && s.bandRowLive]}>
        <View style={[s.bandDot, { backgroundColor: BANDS.auto.color }]} />
        <View style={{ flex: 1 }}>
          <View style={s.bandHead}>
            <Text style={s.bandLabel}>{BANDS.auto.start} to {BANDS.auto.end}  {BANDS.auto.label}</Text>
            {isCurrent('auto') && <Text style={s.liveTag}>LIVE</Text>}
          </View>
          <View style={s.bandBar}>
            <View style={[s.bandFill, { width: pct(autoCount * 5, autoBudget) + '%', backgroundColor: BANDS.auto.color }]} />
          </View>
          <Text style={s.bandSub}>{autoCount} auto-tasks seeded</Text>
        </View>
      </View>
      {/* plan window */}
      <View style={[s.bandRow, isCurrent('plan_window') && s.bandRowLive]}>
        <View style={[s.bandDot, { backgroundColor: BANDS.plan_window.color }]} />
        <View style={{ flex: 1 }}>
          <View style={s.bandHead}>
            <Text style={s.bandLabel}>{BANDS.plan_window.start} to {BANDS.plan_window.end}  {BANDS.plan_window.label}</Text>
            {isCurrent('plan_window') && <Text style={s.liveTag}>LIVE</Text>}
          </View>
          {planSubmitted ? (
            <Text style={[s.bandSub, { color: '#2da44e' }]}>Tomorrows plan submitted</Text>
          ) : (
            <Text style={[s.bandSub, { color: isCurrent('plan_window') ? '#cf222e' : '#57606a' }]}>
              {isCurrent('plan_window') ? 'Submit tomorrows plan now' : 'Submit by 6:30 PM'}
            </Text>
          )}
        </View>
      </View>
    </View>
  );
}

function TaskRow({ task, idx, currentBandKey, workMode, onTap }) {
  const meta = TASK_TYPES[task.type];
  const isDone = task.status === 'done';
  const isLive = task.status === 'in_progress';
  const taskBand = task.time < BANDS.manual.end ? 'manual'
                  : task.time < BANDS.auto.end ? 'auto'
                  : 'plan_window';
  const blocked = taskBand !== currentBandKey && !isDone;

  return (
    <Pressable
      style={[s.taskRow, blocked && s.taskRowBlocked, isLive && s.taskRowLive]}
      onPress={() => {
        const check = bandLockCheck({ workMode, currentBand: currentBandKey, actionTypeId: task.actionTypeId || 1 });
        if (!check.allowed) {
          Alert.alert('Action blocked', check.reason);
          return;
        }
        onTap && onTap(task);
      }}
    >
      <View style={s.taskTime}>
        <Text style={[s.taskTimeText, blocked && s.dimText]}>{task.time}</Text>
        <Text style={[s.taskDur, blocked && s.dimText]}>{task.dur}m</Text>
      </View>
      <View style={[s.taskIcon, { backgroundColor: meta.color + '18' }]}>
        <Ionicons name={meta.icon} size={18} color={meta.color} />
      </View>
      <View style={{ flex: 1 }}>
        <View style={s.taskTopLine}>
          <Text style={[s.taskTitle, blocked && s.dimText]} numberOfLines={1}>{task.title}</Text>
          {task.auto && (
            <View style={s.autoBadge}>
              <Ionicons name="flash" size={9} color="#8250df" />
              <Text style={s.autoBadgeText}>AUTO</Text>
            </View>
          )}
          {blocked && (
            <View style={s.blockedBadge}>
              <Ionicons name="lock-closed" size={9} color="#cf222e" />
              <Text style={s.blockedBadgeText}>LOCKED</Text>
            </View>
          )}
        </View>
        <Text style={[s.taskSub, blocked && s.dimText]} numberOfLines={1}>
          {meta.label} - {taskBand} band
        </Text>
      </View>
      <View style={s.taskStatus}>
        {isDone && <Ionicons name="checkmark-circle" size={20} color="#2da44e" />}
        {isLive && <View style={s.liveDot} />}
      </View>
    </Pressable>
  );
}

function PlanWindowPrompt({ visible, onSubmit }) {
  if (!visible) return null;
  return (
    <View style={s.planPrompt}>
      <LinearGradient colors={['#fff4d6', '#fffaf0']} style={s.planPromptGrad}>
        <View style={s.planPromptHead}>
          <Ionicons name="calendar" size={22} color="#bf8700" />
          <Text style={s.planPromptTitle}>It is 5:30 PM. Plan tomorrow now.</Text>
        </View>
        <Text style={s.planPromptBody}>
          Field activities are closed for the day. You have until 6:30 PM to submit tomorrows plan or it goes to CM as same-day next-day-late.
        </Text>
        <Pressable style={s.planPromptBtn} onPress={onSubmit}>
          <Text style={s.planPromptBtnText}>Open next-day planner</Text>
          <Ionicons name="arrow-forward" size={16} color="#fff" />
        </Pressable>
      </LinearGradient>
    </View>
  );
}

export default function DayPlanScreen({ navigation }) {
  const [now] = useState(new Date());
  const band = currentBand(now);
  const workMode = TODAY_PLAN.workMode || 'wfo'; // wfo | wffo | leave

  // compute band usage from tasks
  const manualBudget = 5 * 60; // 10-15
  const autoBudget   = 150;    // 15-17:30
  const manualUsed   = useMemo(() => (
    TODAY_PLAN.tasks
      .filter(t => t.time < BANDS.manual.end && !t.auto)
      .reduce((sum, t) => sum + (t.dur || actionMin(t.actionTypeId || 1)), 0)
  ), []);
  const autoCount = TODAY_PLAN.tasks.filter(t => t.auto).length;

  const showPlanPrompt = band === 'plan_window' && !TODAY_PLAN.tomorrowSubmitted;

  return (
    <View style={s.root}>
      <StatusBar barStyle="dark-content" />
      <LinearGradient colors={['#f5f7fa', '#eaeef2']} style={s.header}>
        <View style={s.headerTop}>
          <Pressable onPress={() => navigation.goBack()}>
            <Ionicons name="arrow-back" size={22} color="#1f2328" />
          </Pressable>
          <View style={{ flex: 1, marginLeft: 12 }}>
            <Text style={s.headerTitle}>Today {TODAY_PLAN.date}</Text>
            <Text style={s.headerSub}>{CURRENT_USER.name} - {workMode.toUpperCase()}</Text>
          </View>
          <StatusPill status={TODAY_PLAN.status} />
        </View>
      </LinearGradient>

      <ScrollView style={s.body} contentContainerStyle={{ paddingBottom: 40 }}>
        <PlanWindowPrompt
          visible={showPlanPrompt}
          onSubmit={() => navigation.navigate('NextDayPlannerV2')}
        />

        <BandStrip
          band={band}
          manualUsed={manualUsed}
          manualBudget={manualBudget}
          autoCount={autoCount}
          autoBudget={autoBudget}
          planSubmitted={TODAY_PLAN.tomorrowSubmitted}
        />

        {workMode === 'wfo' && (
          <View style={s.wfoBanner}>
            <Ionicons name="business" size={16} color="#cf222e" />
            <Text style={s.wfoBannerText}>
              WFO mode: no physical or barge meetings today. Virtual calls allowed.
            </Text>
          </View>
        )}

        {workMode === 'leave' && (
          <View style={s.leaveBanner}>
            <Ionicons name="bed" size={16} color="#8250df" />
            <Text style={s.leaveBannerText}>
              Approved leave today. Day shape locks are off. No next-day plan required.
            </Text>
          </View>
        )}

        <Text style={s.sectionTitle}>Scheduled tasks</Text>
        {TODAY_PLAN.tasks.map((t, i) => (
          <TaskRow
            key={t.id}
            task={t}
            idx={i}
            currentBandKey={band}
            workMode={workMode}
            onTap={(task) => navigation.navigate('LeadDetail', { leadId: task.leadId })}
          />
        ))}

        <Text style={s.sectionTitle}>Sales pulse</Text>
        <ApplauseBanner />
        <ProgressionScorecardTile />
        <StuckLeadsTile />
        <MoMBlockersTile />
        <ConversionLeaderboardTile />
      </ScrollView>
    </View>
  );
}

const s = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#f5f7fa' },
  header: { paddingTop: 50, paddingHorizontal: 16, paddingBottom: 16 },
  headerTop: { flexDirection: 'row', alignItems: 'center' },
  headerTitle: { fontSize: 18, fontWeight: '700', color: '#1f2328' },
  headerSub: { fontSize: 12, color: '#57606a', marginTop: 2 },
  pill: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 10, paddingVertical: 5, borderRadius: 12, borderWidth: 1 },
  pillDot: { width: 6, height: 6, borderRadius: 3, marginRight: 6 },
  pillText: { fontSize: 10, fontWeight: '700' },
  body: { flex: 1, paddingHorizontal: 12 },

  // band strip
  bandStrip: { backgroundColor: '#fff', borderRadius: 10, padding: 14, marginTop: 12, borderWidth: 1, borderColor: '#d0d7de' },
  bandStripTitle: { fontSize: 11, fontWeight: '700', color: '#57606a', textTransform: 'uppercase', marginBottom: 10, letterSpacing: 0.5 },
  bandRow: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: 12 },
  bandRowLive: { backgroundColor: '#f6f8fa', marginHorizontal: -8, paddingHorizontal: 8, paddingVertical: 6, borderRadius: 6 },
  bandDot: { width: 8, height: 8, borderRadius: 4, marginTop: 4, marginRight: 10 },
  bandHead: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  bandLabel: { fontSize: 13, fontWeight: '600', color: '#1f2328', flex: 1 },
  liveTag: { fontSize: 9, fontWeight: '700', color: '#cf222e', backgroundColor: '#ffebe9', paddingHorizontal: 6, paddingVertical: 2, borderRadius: 4 },
  bandBar: { height: 4, backgroundColor: '#eaeef2', borderRadius: 2, marginTop: 6, overflow: 'hidden' },
  bandFill: { height: '100%' },
  bandSub: { fontSize: 11, color: '#57606a', marginTop: 4 },

  // banners
  wfoBanner: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#ffebe9', borderRadius: 8, padding: 10, marginTop: 10, borderWidth: 1, borderColor: '#ffcecb' },
  wfoBannerText: { fontSize: 12, color: '#cf222e', marginLeft: 8, flex: 1 },
  leaveBanner: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fbefff', borderRadius: 8, padding: 10, marginTop: 10, borderWidth: 1, borderColor: '#e8d4ff' },
  leaveBannerText: { fontSize: 12, color: '#8250df', marginLeft: 8, flex: 1 },

  // plan prompt
  planPrompt: { marginTop: 14, borderRadius: 10, overflow: 'hidden' },
  planPromptGrad: { padding: 14, borderRadius: 10, borderWidth: 1, borderColor: '#d4a72c' },
  planPromptHead: { flexDirection: 'row', alignItems: 'center', marginBottom: 6 },
  planPromptTitle: { fontSize: 14, fontWeight: '700', color: '#bf8700', marginLeft: 8 },
  planPromptBody: { fontSize: 12, color: '#57606a', lineHeight: 17, marginBottom: 10 },
  planPromptBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#bf8700', paddingVertical: 9, borderRadius: 6, gap: 6 },
  planPromptBtnText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  // task rows
  sectionTitle: { fontSize: 11, fontWeight: '700', color: '#57606a', textTransform: 'uppercase', marginTop: 18, marginBottom: 8, letterSpacing: 0.5, paddingHorizontal: 4 },
  taskRow: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', borderRadius: 8, padding: 12, marginBottom: 6, borderWidth: 1, borderColor: '#d0d7de' },
  taskRowLive: { borderColor: '#0969da', backgroundColor: '#ddf4ff' },
  taskRowBlocked: { opacity: 0.55, backgroundColor: '#f6f8fa' },
  taskTime: { width: 50, alignItems: 'center' },
  taskTimeText: { fontSize: 12, fontWeight: '700', color: '#1f2328' },
  taskDur: { fontSize: 10, color: '#57606a', marginTop: 1 },
  taskIcon: { width: 32, height: 32, borderRadius: 16, alignItems: 'center', justifyContent: 'center', marginHorizontal: 10 },
  taskTopLine: { flexDirection: 'row', alignItems: 'center' },
  taskTitle: { fontSize: 13, fontWeight: '600', color: '#1f2328', flex: 1 },
  taskSub: { fontSize: 11, color: '#57606a', marginTop: 2 },
  dimText: { color: '#8c959f' },
  autoBadge: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fbefff', paddingHorizontal: 5, paddingVertical: 2, borderRadius: 3, marginLeft: 6, gap: 3 },
  autoBadgeText: { fontSize: 8, fontWeight: '700', color: '#8250df' },
  blockedBadge: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#ffebe9', paddingHorizontal: 5, paddingVertical: 2, borderRadius: 3, marginLeft: 6, gap: 3 },
  blockedBadgeText: { fontSize: 8, fontWeight: '700', color: '#cf222e' },
  taskStatus: { width: 24, alignItems: 'center' },
  liveDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: '#0969da' },
});
