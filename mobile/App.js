import React, { useState, useEffect } from 'react';
import { StatusBar } from 'expo-status-bar';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';

import LoginScreen from './src/screens/LoginScreen';
import AgentsHubScreen from './src/screens/AgentsHubScreen';
import AgentChatScreen from './src/screens/AgentChatScreen';
import MomDrafterScreen from './src/screens/MomDrafterScreen';
import LeadsScreen from './src/screens/LeadsScreen';
import PipelineScreen from './src/screens/PipelineScreen';
import ActivityScreen from './src/screens/ActivityScreen';
import YouScreen from './src/screens/YouScreen';
import DayPlanScreen from './src/screens/DayPlanScreen';
import PlanApprovalScreen from './src/screens/PlanApprovalScreen';
import ExecutionTrackerScreen from './src/screens/ExecutionTrackerScreen';
import AutoTasksScreen from './src/screens/AutoTasksScreen';
import LeadDetailScreen from './src/screens/LeadDetailScreen';
import CMCallsScreen from './src/screens/CMCallsScreen';
import CMActivitiesScreen from './src/screens/CMActivitiesScreen';
import MomApprovalQueueScreen from './src/screens/MomApprovalQueueScreen';
import MeetingEconomicsScreen from './src/screens/MeetingEconomicsScreen';
import BDPerformanceScreen from './src/screens/BDPerformanceScreen';
import DisciplineScoreScreen from './src/screens/DisciplineScoreScreen';
import CancellationAdvanceAuditScreen from './src/screens/CancellationAdvanceAuditScreen';
import MeetingExpenseTrailScreen from './src/screens/MeetingExpenseTrailScreen';
import ExpenseSubmissionScreen from './src/screens/ExpenseSubmissionScreen';
import AccountsOfficerQueueScreen from './src/screens/AccountsOfficerQueueScreen';
import AdvanceManagementScreen from './src/screens/AdvanceManagementScreen';
import AdvanceSettlementScreen from './src/screens/AdvanceSettlementScreen';
import NewLeadScreen from './src/screens/NewLeadScreen';
import StartMomScreen from './src/screens/StartMomScreen';
import ExportRequestScreen from './src/screens/ExportRequestScreen';
import ExportApprovalQueueScreen from './src/screens/ExportApprovalQueueScreen';
import AccessAuditScreen from './src/screens/AccessAuditScreen';
import MeetingPrepScreen from './src/screens/MeetingPrepScreen';
import MeetingPrepRunsScreen from './src/screens/MeetingPrepRunsScreen';
import { colors } from './src/theme/colors';
import { getRole, subscribe } from './src/state/session';

const Tab = createBottomTabNavigator();
const AgentsStack = createNativeStackNavigator();
const PlanStack = createNativeStackNavigator();
const LeadsStack = createNativeStackNavigator();
const TeamStack = createNativeStackNavigator();

function AgentsStackNav() {
  return (
    <AgentsStack.Navigator screenOptions={{ headerShown: false }}>
      <AgentsStack.Screen name="AgentsHub"        component={AgentsHubScreen} />
      <AgentsStack.Screen name="AgentChat"        component={AgentChatScreen} />
      <AgentsStack.Screen name="MomDrafter"       component={MomDrafterScreen} />
      <AgentsStack.Screen name="StartMom"         component={StartMomScreen} />
      <AgentsStack.Screen name="ExecutionTracker" component={ExecutionTrackerScreen} />
      <AgentsStack.Screen name="AutoTasks"        component={AutoTasksScreen} />
      <AgentsStack.Screen name="LeadDetail"       component={LeadDetailScreen} />
      <AgentsStack.Screen name="DisciplineScore"  component={DisciplineScoreScreen}
        options={{ headerShown: true, title: 'My Discipline Score' }} />
      <AgentsStack.Screen name="MeetingExpenseTrail" component={MeetingExpenseTrailScreen}
        options={{ headerShown: true, title: 'Meeting Cost Trail' }} />
      <AgentsStack.Screen name="ExpenseSubmission" component={ExpenseSubmissionScreen}
        options={{ headerShown: true, title: 'Submit Today Actuals' }} />
      <AgentsStack.Screen name="CancellationAdvanceAuditScreen" component={CancellationAdvanceAuditScreen}
        options={{ headerShown: true, title: 'Cancel Meeting' }} />
      <AgentsStack.Screen name="AccountsOfficerQueue" component={AccountsOfficerQueueScreen}
        options={{ headerShown: true, title: 'Accounts Officer Queue' }} />
      <AgentsStack.Screen name="AdvanceManagement" component={AdvanceManagementScreen}
        options={{ headerShown: true, title: 'Advance Management' }} />
      <AgentsStack.Screen name="AdvanceSettlement" component={AdvanceSettlementScreen}
        options={{ headerShown: true, title: 'Settle Advance' }} />
      <AgentsStack.Screen name="MeetingEconomics" component={MeetingEconomicsScreen}
        options={{ title: 'Meeting Economics' }} />
      <AgentsStack.Screen name="BDPerformance" component={BDPerformanceScreen}
        options={{ headerShown: true, title: 'My Performance' }} />
      <AgentsStack.Screen name="MeetingPrep" component={MeetingPrepScreen}
        options={{ headerShown: true, title: 'Meeting Prep' }} />
      <AgentsStack.Screen name="MeetingPrepRuns" component={MeetingPrepRunsScreen}
        options={{ headerShown: true, title: 'Meeting Prep Runs' }} />
    </AgentsStack.Navigator>
  );
}

function PlanStackNav() {
  return (
    <PlanStack.Navigator screenOptions={{ headerShown: false }}>
      <PlanStack.Screen name="DayPlan"          component={DayPlanScreen} />
      <PlanStack.Screen name="ExecutionTracker" component={ExecutionTrackerScreen} />
      <PlanStack.Screen name="AutoTasks"        component={AutoTasksScreen} />
      <PlanStack.Screen name="StartMom"         component={StartMomScreen} />
    </PlanStack.Navigator>
  );
}

function LeadsStackNav() {
  return (
    <LeadsStack.Navigator screenOptions={{ headerShown: false }}>
      <LeadsStack.Screen name="LeadsList"  component={LeadsScreen} />
      <LeadsStack.Screen name="LeadDetail" component={LeadDetailScreen} />
      <LeadsStack.Screen name="NewLead"    component={NewLeadScreen} />
      <LeadsStack.Screen name="StartMom"   component={StartMomScreen} />
      <LeadsStack.Screen name="ExportRequest" component={ExportRequestScreen} />
      <LeadsStack.Screen name="AccessAuditMine"
                          component={AccessAuditScreen}
                          initialParams={{ scopeMine: true }} />
    </LeadsStack.Navigator>
  );
}

// CM-specific stack: Approvals (root) + Calls + Activities + lead detail
function TeamStackNav() {
  return (
    <TeamStack.Navigator screenOptions={{ headerShown: false }}>
      <TeamStack.Screen name="Approvals"   component={PlanApprovalScreen} />
      <TeamStack.Screen name="MomApprovalQueue" component={MomApprovalQueueScreen}
        options={{ title: 'MoM Approvals' }} />
      <TeamStack.Screen name="CancellationAdvanceAudit" component={CancellationAdvanceAuditScreen}
        options={{ headerShown: true, title: 'Cancelled Meetings + Advances' }} />
      <TeamStack.Screen name="MeetingExpenseTrail" component={MeetingExpenseTrailScreen}
        options={{ headerShown: true, title: 'Meeting Cost Trail' }} />
      <TeamStack.Screen name="AccountsOfficerQueue" component={AccountsOfficerQueueScreen}
        options={{ headerShown: true, title: 'Accounts Officer Queue' }} />
      <TeamStack.Screen name="AdvanceManagement" component={AdvanceManagementScreen}
        options={{ headerShown: true, title: 'Advance Management' }} />
      <TeamStack.Screen name="AdvanceSettlement" component={AdvanceSettlementScreen}
        options={{ headerShown: true, title: 'Settle Advance' }} />
      <TeamStack.Screen name="MeetingEconomicsTeam" component={MeetingEconomicsScreen}
        initialParams={{ tab: 'team' }}
        options={{ title: 'Team Economics' }} />
      <TeamStack.Screen name="BDPerformance" component={BDPerformanceScreen}
        options={{ headerShown: true, title: 'BD Performance' }} />
      <TeamStack.Screen name="CMCalls"     component={CMCallsScreen} />
      <TeamStack.Screen name="CMActivities" component={CMActivitiesScreen} />
      <TeamStack.Screen name="LeadDetail"  component={LeadDetailScreen} />
      <TeamStack.Screen name="NewLead"     component={NewLeadScreen} />
      <TeamStack.Screen name="ExportApprovalQueue" component={ExportApprovalQueueScreen} />
      <TeamStack.Screen name="AccessAudit"  component={AccessAuditScreen} />
      <TeamStack.Screen name="ExportRequest" component={ExportRequestScreen} />
      <TeamStack.Screen name="MeetingPrep" component={MeetingPrepScreen}
        options={{ headerShown: true, title: 'Meeting Prep' }} />
      <TeamStack.Screen name="MeetingPrepRuns" component={MeetingPrepRunsScreen}
        options={{ headerShown: true, title: 'Meeting Prep Runs' }} />
    </TeamStack.Navigator>
  );
}

const ICON_MAP = {
  Agents:    'sparkles',
  Plan:      'calendar',
  Team:      'people',
  Calls:     'call',
  Leads:     'flash',
  Pipeline:  'git-branch',
  Activity:  'pulse',
  You:       'person-circle',
};

function MainTabs() {
  const [role, setRoleState] = useState(getRole());
  useEffect(() => subscribe(setRoleState), []);

  const isBD       = role === 'BD';
  const isCM       = role === 'CM';

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: colors.btnFrom,
        tabBarInactiveTintColor: colors.textMuted,
        tabBarStyle: { paddingTop: 6, height: 62, paddingBottom: 8, borderTopColor: colors.border },
        tabBarLabelStyle: { fontSize: 11, fontWeight: '600' },
        tabBarIcon: ({ color, size }) => (
          <Ionicons name={ICON_MAP[route.name] || 'ellipse'} size={size} color={color} />
        ),
      })}
    >
      <Tab.Screen name="Agents" component={AgentsStackNav} />

      {isBD && <Tab.Screen name="Plan"  component={PlanStackNav} />}
      {isCM && <Tab.Screen name="Team"  component={TeamStackNav} />}
      {isCM && <Tab.Screen name="Calls" component={CMCallsScreen} />}

      <Tab.Screen name="Leads"    component={LeadsStackNav} />
      <Tab.Screen name="Pipeline" component={PipelineScreen} />
      <Tab.Screen name="Activity" component={isCM ? CMActivitiesScreen : ActivityScreen} />
      <Tab.Screen name="You"      component={YouScreen} />
    </Tab.Navigator>
  );
}

export default function App() {
  const [signedIn, setSignedIn] = useState(false);
  return (
    <NavigationContainer>
      <StatusBar style="light" />
      {signedIn ? <MainTabs /> : <LoginScreen onLogin={() => setSignedIn(true)} />}
    </NavigationContainer>
  );
}
