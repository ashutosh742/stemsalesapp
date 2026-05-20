// Day-plan data — the contract between BD and Line Manager.
// Backend mapping (CodeIgniter):
//   - Plan submission:  Menu_model::saveDayPlan
//   - Plan approval:    Management_model::approveDayPlan
//   - Auto-tasks log:   AIAgents/CallLogs_model, AIAgents/EmailLogs_model
//   - Execution diff:   AnayaAgent::compute_bd_discipline

export const PLAN_STATUS = {
  DRAFT:     { label: 'Draft',     color: '#9CA3AF' },
  PENDING:   { label: 'Pending',   color: '#F59E0B' },
  APPROVED:  { label: 'Approved',  color: '#10B981' },
  REJECTED:  { label: 'Rejected',  color: '#EF4444' },
  EXECUTING: { label: 'In flight', color: '#3E21FB' },
  DONE:      { label: 'Closed',    color: '#6B7280' },
};

export const TASK_TYPES = {
  visit:    { label: 'Site visit',     icon: 'walk',          color: '#F39C12', cap: 90 },
  call:     { label: 'Sales call',     icon: 'call',          color: '#3498DB', cap: 20 },
  email:    { label: 'Email',          icon: 'mail',          color: '#9B59B6', cap: 10 },
  meeting:  { label: 'Internal sync',  icon: 'people',        color: '#14B8A6', cap: 30 },
  followup: { label: 'Follow-up',      icon: 'time',          color: '#EAB308', cap: 15 },
  travel:   { label: 'Travel',         icon: 'car',           color: '#6B7280', cap: 60 },
};

// Today's plan for the current BD (Priya). Already approved by Anjali (CM).
export const TODAY_PLAN = {
  id: 'PLN-2026-05-14-042',
  date: '2026-05-14',
  user: { id: 42, name: 'Priya Menon', role: 'BD', cluster: 'Mumbai' },
  status: 'EXECUTING',
  submitted_at: '2026-05-14T07:42:00+05:30',
  approved_by: { id: 12, name: 'Anjali Rao', role: 'CM' },
  approved_at: '2026-05-14T07:58:00+05:30',
  approver_note: 'Good plan. Push for closure on L-1042 today.',
  tasks: [
    { id: 't1', type: 'call',     time: '08:30', dur: 15, lead: 'L-1042', title: 'Call Mr. Bhandari (KV Andheri)', auto: false,
      status: 'done',    actual_at: '08:34', outcome: 'no_answer', note: 'Left voicemail' },
    { id: 't2', type: 'email',    time: '09:00', dur: 10, lead: 'L-1042', title: 'Send revised timeline doc', auto: true,
      status: 'done',    actual_at: '09:02', outcome: 'sent', note: 'Auto-sent via template t_proposal_revision' },
    { id: 't3', type: 'travel',   time: '09:30', dur: 90, lead: null,     title: 'Travel to Pune (3.5h drive)', auto: false,
      status: 'in_progress' },
    { id: 't4', type: 'visit',    time: '11:00', dur: 60, lead: 'L-1041', title: 'Site visit · Govt. HS Pune', auto: false,
      status: 'planned' },
    { id: 't5', type: 'email',    time: '13:00', dur: 10, lead: 'L-1041', title: 'MoM auto-send to principal', auto: true,
      status: 'planned' },
    { id: 't6', type: 'call',     time: '14:30', dur: 20, lead: 'L-1040', title: 'Negotiation call · Z.P. Nashik', auto: false,
      status: 'planned' },
    { id: 't7', type: 'visit',    time: '16:00', dur: 75, lead: 'L-1038', title: 'Site visit · DAV Nagpur', auto: false,
      status: 'planned' },
    { id: 't8', type: 'followup', time: '18:00', dur: 15, lead: 'L-1037', title: 'Follow-up · Sarvodaya Vidyalaya', auto: false,
      status: 'planned' },
  ],
};

// Plans waiting for Anjali (CM) to approve — used by PlanApprovalScreen
export const PENDING_PLANS = [
  {
    id: 'PLN-2026-05-15-043',
    date: '2026-05-15',
    user: { id: 43, name: 'Ravi Kumar',  initials: 'RK', role: 'BD' },
    submitted_at: '2026-05-14T19:12:00+05:30',
    task_count: 7,
    visits: 2, calls: 3, emails: 2,
    pipeline_today: '₹8.4L',
    note: 'Two visits in Pune cluster + follow-ups on stuck deals.',
    flags: ['No buffer between 13:00 and 16:00 visits in different cities'],
  },
  {
    id: 'PLN-2026-05-15-044',
    date: '2026-05-15',
    user: { id: 44, name: 'Anita Sharma', initials: 'AS', role: 'BD' },
    submitted_at: '2026-05-14T19:38:00+05:30',
    task_count: 5,
    visits: 1, calls: 3, emails: 1,
    pipeline_today: '₹3.1L',
    note: 'Negotiation call on L-1040 + 2 cold calls.',
    flags: [],
  },
  {
    id: 'PLN-2026-05-15-045',
    date: '2026-05-15',
    user: { id: 45, name: 'Vikram Tyagi', initials: 'VT', role: 'BD' },
    submitted_at: '2026-05-14T18:55:00+05:30',
    task_count: 4,
    visits: 1, calls: 2, emails: 1,
    pipeline_today: '₹4.2L',
    note: 'DAV Nagpur follow-up + 2 new outreach calls.',
    flags: ['Light day — only 4 tasks · 2 below cluster average'],
  },
];

// Auto-task feed (emails + calls captured by the system, not user-entered)
export const AUTO_TASKS_TODAY = [
  { id: 'a1', kind: 'email', when: '09:02', lead: 'L-1042', subject: 'Revised Mini Science Centre timeline — KV Andheri', template: 't_proposal_revision', to: 'bhandari@tatasteel.com', status: 'delivered', opens: 2, clicks: 1 },
  { id: 'a2', kind: 'call',  when: '08:34', lead: 'L-1042', number: '+91 98xxx xxx12', dialer: 'Knowlarity', duration_s: 0,   outcome: 'no_answer',  recording: null },
  { id: 'a3', kind: 'email', when: '07:15', lead: 'L-1039', subject: 'Auto-reminder: discovery call agenda',         template: 't_discovery_followup', to: 'principal@municipal-borivali.in', status: 'opened', opens: 1, clicks: 0 },
  { id: 'a4', kind: 'call',  when: '07:08', lead: 'L-1040', number: '+91 99xxx xxx40', dialer: 'Knowlarity', duration_s: 312, outcome: 'connected',  recording: 'rec_8123.mp3' },
  { id: 'a5', kind: 'email', when: '06:30', lead: 'L-1041', subject: 'Site visit confirmation — Govt. HS Pune',     template: 't_visit_confirm',       to: 'principal@ghs-pune.gov.in',     status: 'delivered', opens: 1, clicks: 0 },
];

// 13-Status journey (Stemapp canonical) — from the Full Analysis deck (slides 26-29)
// Each status maps to phase + color used by LeadDetailScreen and PipelineScreen
export const STATUSES = [
  { id: 1,  name: 'Open',         phase: 'Prospecting', color: '#1F3A60' },
  { id: 2,  name: 'Open RPEM',    phase: 'Prospecting', color: '#1F3A60' },
  { id: 3,  name: 'Reachout',     phase: 'Prospecting', color: '#1F3A60' },
  { id: 4,  name: 'TTD-Reachout', phase: 'Prospecting', color: '#1F3A60' },
  { id: 5,  name: 'WNO-Reachout', phase: 'Prospecting', color: '#1F3A60' },
  { id: 6,  name: 'Tentative',    phase: 'Engagement',  color: '#C9A227' },
  { id: 7,  name: 'Positive-NAP', phase: 'Engagement',  color: '#C9A227' },
  { id: 8,  name: 'Positive',     phase: 'Engagement',  color: '#C9A227' },
  { id: 9,  name: 'V Positive-NAP', phase: 'Engagement', color: '#C9A227' },
  { id: 10, name: 'Very Positive', phase: 'Engagement', color: '#C9A227' },
  { id: 11, name: 'Closure',      phase: 'Outcome',     color: '#10B981' },
  { id: 12, name: 'Not Interested', phase: 'Outcome',   color: '#EF4444' },
  { id: 13, name: 'Will-do-Later',  phase: 'Outcome',   color: '#F59E0B' },
];

// Lead progression — the full transition log for L-1042 (used by LeadDetailScreen)
export const LEAD_TIMELINE = {
  lead_id: 'L-1042',
  school: 'Kendriya Vidyalaya, Andheri',
  city: 'Mumbai',
  program: 'Mini Science Centre',
  value: '₹4.2L',
  current_stage: 'Tentative',          // status #6 in canonical 13-status journey
  current_stage_id: 6,
  days_in_stage: 4,
  next_stages: ['Positive', 'Positive-NAP', 'Not Interested'],
  owner: { id: 42, name: 'Priya Menon', role: 'BD' },
  decision_maker: { name: 'Mr. Bhandari', title: 'GM-CSR, Tata Steel', email: 'bhandari@tatasteel.com', phone: '+91 98xxx xxx12' },
  stuck_flag: 'No DM contact in 4 days · win-rate drops after day 5',
  events: [
    { at: '2026-04-22T10:00:00+05:30', kind: 'created',  by: 'Priya M.',  text: 'Lead created from web inquiry · Tata Steel CSR' },
    { at: '2026-04-23T11:30:00+05:30', kind: 'call',     by: 'Priya M.',  text: 'Discovery call · 12 min · interested in MSC + Tinker Lab' },
    { at: '2026-04-25T15:00:00+05:30', kind: 'stage',    by: 'Priya M.',  text: 'Moved Open → Reachout', from: 'Open', to: 'Reachout' },
    { at: '2026-04-26T11:00:00+05:30', kind: 'visit',    by: 'Priya M.',  text: 'Site visit · 60 min · met principal + Mr. Bhandari' },
    { at: '2026-04-26T18:00:00+05:30', kind: 'mom',      by: 'Priya M.',  text: 'MoM filed · quality 91 · CM approved' },
    { at: '2026-04-28T10:00:00+05:30', kind: 'email',    by: 'System',    text: 'Auto-email: proposal sent (t_proposal_v1)' },
    { at: '2026-04-28T10:01:00+05:30', kind: 'stage',    by: 'Priya M.',  text: 'Moved Reachout → Tentative', from: 'Reachout', to: 'Tentative' },
    { at: '2026-05-02T09:00:00+05:30', kind: 'email',    by: 'System',    text: 'Proposal email opened 2x by DM' },
    { at: '2026-05-08T08:00:00+05:30', kind: 'agent',    by: 'Anaya',     text: 'Surfaced as high-impact priority · pattern: DM silence > 4 days' },
    { at: '2026-05-14T08:34:00+05:30', kind: 'call',     by: 'Priya M.',  text: 'Call attempted · no answer · voicemail left' },
    { at: '2026-05-14T09:02:00+05:30', kind: 'email',    by: 'System',    text: 'Auto-email: revised timeline (t_proposal_revision) · 1 open, 0 clicks' },
  ],
};
