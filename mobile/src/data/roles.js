// Roles model — drives screen visibility, approvals, and agent permissions.
// Mirrors the role table in your CodeIgniter app (Menu_model → user.role_id).

export const ROLES = {
  BD: {
    id: 'BD',
    label: 'Business Development',
    short: 'BD',
    color: '#3E21FB',
    can: {
      planDay: true,
      submitPlan: true,
      approvePlans: false,
      viewAllLeads: false,        // only own + team-shared
      moveLeadStage: true,        // own leads only
      fileMom: true,
      viewTeamPlans: false,
      viewClusterPipeline: false, // own pipeline only
      escalate: true,
    },
    sees: ['Agents', 'Plan', 'Leads', 'Pipeline', 'Activity', 'You'],
    agents: ['anaya', 'mom', 'dump'],
  },
  CM: {
    id: 'CM',
    label: 'Cluster Manager',
    short: 'CM',
    color: '#9B59B6',
    can: {
      planDay: true,
      submitPlan: true,
      approvePlans: true,         // approves BD plans in cluster
      viewAllLeads: true,         // all leads in cluster
      moveLeadStage: true,
      fileMom: false,
      viewTeamPlans: true,
      viewClusterPipeline: true,
      escalate: true,
    },
    sees: ['Agents', 'Approvals', 'Team', 'Pipeline', 'Activity', 'You'],
    agents: ['anaya', 'copilot', 'cadence', 'warroom'],
  },
  RM: {
    id: 'RM',
    label: 'Regional Manager',
    short: 'RM',
    color: '#1E90FF',
    can: {
      planDay: false,
      submitPlan: false,
      approvePlans: true,         // approves CM plans + escalations
      viewAllLeads: true,         // all leads in region (multi-cluster)
      moveLeadStage: true,
      fileMom: false,
      viewTeamPlans: true,
      viewClusterPipeline: true,
      escalate: true,
    },
    sees: ['Agents', 'Approvals', 'Team', 'Pipeline', 'Activity', 'You'],
    agents: ['warroom', 'cadence', 'copilot'],
  },
  SC: {
    id: 'SC',
    label: 'Senior Coordinator',
    short: 'SC',
    color: '#F59E0B',
    can: {
      planDay: false,
      submitPlan: false,
      approvePlans: false,
      viewAllLeads: true,         // for dormant lead redistribution
      moveLeadStage: false,
      fileMom: false,
      viewTeamPlans: false,
      viewClusterPipeline: true,
      escalate: true,
    },
    sees: ['Agents', 'Dormant', 'Leads', 'Activity', 'You'],
    agents: ['dump', 'copilot'],
  },
  FOUNDER: {
    id: 'FOUNDER',
    label: 'Founder',
    short: 'Founder',
    color: '#E52E71',
    can: {
      planDay: false,
      submitPlan: false,
      approvePlans: false,
      viewAllLeads: true,         // all clusters, all leads
      moveLeadStage: false,
      fileMom: false,
      viewTeamPlans: true,
      viewClusterPipeline: true,
      escalate: false,
    },
    sees: ['Agents', 'War Room', 'Pipeline', 'Activity', 'You'],
    agents: ['warroom', 'cadence'],
  },
};

// Current "logged in" user (drives demo). Can be switched from YouScreen.
export const CURRENT_USER = {
  id: 42,
  name: 'Priya Menon',
  initials: 'PM',
  email: 'priya.menon@stemlearning.in',
  role: 'BD',
  cluster: 'Mumbai',
  region: 'West',
  reports_to: { id: 12, name: 'Anjali Rao', role: 'CM' },
};
