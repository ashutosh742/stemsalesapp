// CM (Cluster Manager) data — team-level rollups across BDs in the cluster.
// Backend mapping (CodeIgniter):
//   - Team calls roster:    AIAgents/CallLogs_model::cluster_calls
//   - Team activity feed:   AIAgents/ActivityFeed_model::cluster_feed
//   - New lead create:      Menu_model::insert_init_call (init_call table)
//   - MoM kickoff:          MomController::api_start (new) → mom_data row

// All cluster BDs for the current CM (Anjali Rao, Mumbai cluster, id 12)
export const CLUSTER_BDS = [
  { id: 42, name: 'Priya Menon',  initials: 'PM', color: '#3E21FB', city: 'Mumbai',  status: 'in_field',  last_seen: '2 min ago' },
  { id: 43, name: 'Ravi Kumar',   initials: 'RK', color: '#10B981', city: 'Pune',    status: 'on_call',   last_seen: 'now' },
  { id: 44, name: 'Anita Sharma', initials: 'AS', color: '#F59E0B', city: 'Mumbai',  status: 'office',    last_seen: '8 min ago' },
  { id: 45, name: 'Vikram Tyagi', initials: 'VT', color: '#9B59B6', city: 'Nagpur',  status: 'in_field',  last_seen: '14 min ago' },
  { id: 46, name: 'Sneha Iyer',   initials: 'SI', color: '#EF4444', city: 'Nashik',  status: 'offline',   last_seen: '2h ago' },
];

// Live calls feed across the cluster (tblcallevents, atid=1) — last 24h
export const CM_CALLS_FEED = [
  { id: 'c1', bd: { id: 42, name: 'Priya Menon',  initials: 'PM', color: '#3E21FB' }, lead: { id: 'L-1042', name: 'KV Andheri' },              dialer: 'Knowlarity', when: '08:34', duration_s: 0,    outcome: 'no_answer', recording: null,           note: 'Left voicemail',                  sentiment: null,        live: false },
  { id: 'c2', bd: { id: 43, name: 'Ravi Kumar',   initials: 'RK', color: '#10B981' }, lead: { id: 'L-1051', name: 'DAV Pune' },                dialer: 'Knowlarity', when: 'live',  duration_s: 184,  outcome: 'in_progress', recording: 'live',       note: 'Discovery — talking to principal', sentiment: 'positive',  live: true  },
  { id: 'c3', bd: { id: 44, name: 'Anita Sharma', initials: 'AS', color: '#F59E0B' }, lead: { id: 'L-1040', name: 'Z.P. Nashik' },             dialer: 'Knowlarity', when: '07:08', duration_s: 312,  outcome: 'connected', recording: 'rec_8123.mp3', note: 'Pricing pushback · revised quote needed', sentiment: 'neutral', live: false },
  { id: 'c4', bd: { id: 45, name: 'Vikram Tyagi', initials: 'VT', color: '#9B59B6' }, lead: { id: 'L-1038', name: 'DAV Nagpur' },              dialer: 'Knowlarity', when: '06:55', duration_s: 0,    outcome: 'busy',      recording: null,           note: 'Auto-retry queued for 15:00',     sentiment: null,        live: false },
  { id: 'c5', bd: { id: 42, name: 'Priya Menon',  initials: 'PM', color: '#3E21FB' }, lead: { id: 'L-1037', name: 'Sarvodaya Vidyalaya' },    dialer: 'Knowlarity', when: 'Y 18:22', duration_s: 420, outcome: 'connected', recording: 'rec_8019.mp3', note: 'Asked for proposal · sent same day', sentiment: 'positive', live: false },
  { id: 'c6', bd: { id: 44, name: 'Anita Sharma', initials: 'AS', color: '#F59E0B' }, lead: { id: 'L-1033', name: 'Govt. HS Borivali' },      dialer: 'Knowlarity', when: 'Y 16:10', duration_s: 95,   outcome: 'connected', recording: 'rec_8011.mp3', note: 'Principal busy — callback Friday',  sentiment: 'neutral',   live: false },
];

// Activities feed (all kinds: calls/emails/visits/MoMs/stage changes) — last 24h
export const CM_ACTIVITY_FEED = [
  { id: 'a01', kind: 'call',  bd: { id: 43, name: 'Ravi Kumar',   initials: 'RK', color: '#10B981' }, lead: 'L-1051', school: 'DAV Pune',           when: 'live · 3m',  detail: 'Discovery call · principal on line',           tag: 'LIVE',     tagColor: '#EF4444' },
  { id: 'a02', kind: 'visit', bd: { id: 42, name: 'Priya Menon',  initials: 'PM', color: '#3E21FB' }, lead: 'L-1041', school: 'Govt. HS Pune',      when: '11:00',      detail: 'Site visit started · auto-clock in',           tag: 'IN PROGRESS', tagColor: '#3E21FB' },
  { id: 'a03', kind: 'mom',   bd: { id: 42, name: 'Priya Menon',  initials: 'PM', color: '#3E21FB' }, lead: 'L-1041', school: 'Govt. HS Pune',      when: '11:48',      detail: 'MoM filed · quality 87 · awaiting CM review',  tag: 'REVIEW',   tagColor: '#F59E0B' },
  { id: 'a04', kind: 'email', bd: { id: 42, name: 'Priya Menon',  initials: 'PM', color: '#3E21FB' }, lead: 'L-1042', school: 'KV Andheri',         when: '09:02',      detail: 'Auto-email · revised timeline · 2 opens · 1 click', tag: 'AUTO', tagColor: '#9B59B6' },
  { id: 'a05', kind: 'stage', bd: { id: 44, name: 'Anita Sharma', initials: 'AS', color: '#F59E0B' }, lead: 'L-1040', school: 'Z.P. Nashik',        when: '08:15',      detail: 'Moved Reachout → Tentative',                   tag: '+STAGE',   tagColor: '#10B981' },
  { id: 'a06', kind: 'call',  bd: { id: 42, name: 'Priya Menon',  initials: 'PM', color: '#3E21FB' }, lead: 'L-1042', school: 'KV Andheri',         when: '08:34',      detail: 'Call attempted · no answer · VM left',         tag: 'MISS',     tagColor: '#9CA3AF' },
  { id: 'a07', kind: 'plan',  bd: { id: 45, name: 'Vikram Tyagi', initials: 'VT', color: '#9B59B6' }, lead: null,     school: null,                  when: '07:55',     detail: 'Submitted day plan · 4 tasks · ₹4.2L pipeline',  tag: 'APPROVED', tagColor: '#10B981' },
  { id: 'a08', kind: 'email', bd: { id: 46, name: 'Sneha Iyer',   initials: 'SI', color: '#EF4444' }, lead: 'L-1029', school: 'St. Xaviers Nashik', when: 'Y 19:40',    detail: 'Manual email · proposal v2 attached',          tag: '',         tagColor: '#6B7280' },
];

// Lead-source presets for the new-lead form (init_call.source)
export const LEAD_SOURCES = [
  { id: 'web',       label: 'Web inquiry',      icon: 'globe-outline' },
  { id: 'referral',  label: 'Referral',         icon: 'people-outline' },
  { id: 'event',     label: 'Event / expo',     icon: 'megaphone-outline' },
  { id: 'cold',      label: 'Cold outreach',    icon: 'snow-outline' },
  { id: 'partner',   label: 'Channel partner',  icon: 'git-network-outline' },
  { id: 'rfp',       label: 'RFP / tender',     icon: 'document-text-outline' },
];

// Program / offering catalogue
export const PROGRAMS = [
  { id: 'msc',    label: 'Mini Science Centre',  budget: '₹4–6L' },
  { id: 'tinker', label: 'Tinkering Lab',        budget: '₹6–9L' },
  { id: 'atal',   label: 'Atal Tinkering Lab',   budget: '₹12L' },
  { id: 'robo',   label: 'Robotics Curriculum',  budget: '₹2–4L' },
  { id: 'stem',   label: 'STEM Kit Bundle',      budget: '₹1–2L' },
];

// Research-sourced lead candidates — Anaya (activity research) + Dump Mining surface these
// nightly. BD reviews and either ACCEPTS (creates init_call) or DISMISSES.
// Backend: AIAgents/LeadSourcing_model::candidates_for_bd(bd_id)
//   - source_agent: which AI agent found this signal (anaya | dump | warroom)
//   - signal:       short reason this surfaced (referral, dormant revival, RFP scrape, etc.)
//   - confidence:   model score 0–1
//   - status:       'pending' (default) | 'accepted' | 'dismissed'
export const RESEARCH_CANDIDATES = [
  {
    id: 'rc1',
    school: 'Bombay Scottish, Mahim',
    city: 'Mumbai',
    dm_hint: 'Mrs. Sunita Pillai · Principal',
    phone_hint: '+91 22 24445***',
    program_hint: 'msc',
    value_hint: '₹4–6L',
    source_agent: 'anaya',
    signal: 'Referral from KV Andheri principal · MoM L-1042',
    confidence: 0.92,
    surfaced_at: 'Today 06:15',
  },
  {
    id: 'rc2',
    school: 'St. Marys, Kalyan',
    city: 'Mumbai',
    dm_hint: 'Fr. George Mathew · Director',
    phone_hint: '+91 251 220***',
    program_hint: 'tinker',
    value_hint: '₹6–9L',
    source_agent: 'dump',
    signal: 'Dormant 14 mo · CSR budget refresh detected · 87% similar to won L-0921',
    confidence: 0.81,
    surfaced_at: 'Today 02:30',
  },
  {
    id: 'rc3',
    school: 'Govt. ITI Bhandup',
    city: 'Mumbai',
    dm_hint: 'Shri R.K. Deshmukh · Vocational head',
    phone_hint: '+91 22 25664***',
    program_hint: 'atal',
    value_hint: '₹12L',
    source_agent: 'anaya',
    signal: 'New ATL guideline tender on MHRD portal · matches your patch',
    confidence: 0.88,
    surfaced_at: 'Today 07:02',
  },
  {
    id: 'rc4',
    school: 'Pawar Public School, Hadapsar',
    city: 'Pune',
    dm_hint: 'Dr. Meera Karandikar',
    phone_hint: '+91 20 2698****',
    program_hint: 'robo',
    value_hint: '₹2–4L',
    source_agent: 'dump',
    signal: 'Visited expo booth Feb-24 · no follow-up · re-engaging window open',
    confidence: 0.73,
    surfaced_at: 'Yesterday 23:48',
  },
  {
    id: 'rc5',
    school: 'Sanskar Vidyalaya, Nagpur',
    city: 'Nagpur',
    dm_hint: 'Mrs. Anjana Wagh',
    phone_hint: '+91 712 224****',
    program_hint: 'stem',
    value_hint: '₹1–2L',
    source_agent: 'anaya',
    signal: 'Web inquiry form · downloaded brochure twice · revisited pricing page',
    confidence: 0.84,
    surfaced_at: 'Today 09:11',
  },
];

// MoM templates — preset agendas the BD can choose when starting a meeting
export const MOM_TEMPLATES = [
  { id: 'discovery',   label: 'Discovery call',       agenda: ['Introductions', 'School profile + scale', 'Pain points', 'Budget cycle', 'Decision process'], expected_minutes: 30 },
  { id: 'site_visit',  label: 'Site visit',           agenda: ['Walk-through', 'Existing infra audit', 'Space measurement', 'Stakeholder intros', 'Next steps'], expected_minutes: 60 },
  { id: 'demo',        label: 'Product demo',         agenda: ['Live demo', 'Q&A', 'Use-case mapping', 'Pricing walk-through', 'Pilot proposal'], expected_minutes: 45 },
  { id: 'negotiation', label: 'Negotiation',          agenda: ['Recap of proposal', 'Price/terms', 'Concessions', 'Timeline lock', 'Close'], expected_minutes: 45 },
  { id: 'closure',     label: 'Closure / sign-off',   agenda: ['Final scope review', 'Payment terms', 'Delivery schedule', 'PO sign-off', 'Handover'], expected_minutes: 30 },
];

// Task Efficiency — per-BD daily scores (mirrors task_efficiency_scores table).
// Backend: AIAgents/TaskEfficiencyAgent_model::cluster_rollup(cm_id, score_date)
// Score = 0.30*completion + 0.20*timeliness + 0.20*action + 0.30*purpose
export const CLUSTER_EFFICIENCY = {
  score_date: '2026-05-14',  // yesterday at compute time
  bds: [
    { bd_id: 42, bd_name: 'Priya Menon',  color: '#3E21FB', planned: 6, done: 6, ontime: 5, action_yes: 6, purpose_met: 5, completion_pct: 100, timeliness_pct: 83, action_pct: 100, purpose_pct: 83, efficiency_score: 89.6, trend: 'up',   signal_breakdown: { bd_tag: 4, ai_inference: 1, funnel_movement: 0 } },
    { bd_id: 43, bd_name: 'Ravi Kumar',   color: '#10B981', planned: 5, done: 5, ontime: 5, action_yes: 5, purpose_met: 4, completion_pct: 100, timeliness_pct: 100, action_pct: 100, purpose_pct: 80, efficiency_score: 92.0, trend: 'up',  signal_breakdown: { bd_tag: 3, ai_inference: 1, funnel_movement: 0 } },
    { bd_id: 44, bd_name: 'Anita Sharma', color: '#F59E0B', planned: 7, done: 5, ontime: 3, action_yes: 4, purpose_met: 2, completion_pct: 71,  timeliness_pct: 60,  action_pct: 80,  purpose_pct: 50, efficiency_score: 63.8, trend: 'flat', signal_breakdown: { bd_tag: 1, ai_inference: 1, funnel_movement: 0 } },
    { bd_id: 45, bd_name: 'Vikram Tyagi', color: '#9B59B6', planned: 4, done: 4, ontime: 2, action_yes: 3, purpose_met: 2, completion_pct: 100, timeliness_pct: 50,  action_pct: 75,  purpose_pct: 67, efficiency_score: 75.2, trend: 'down', signal_breakdown: { bd_tag: 2, ai_inference: 0, funnel_movement: 0 } },
    { bd_id: 46, bd_name: 'Sneha Iyer',   color: '#EF4444', planned: 5, done: 2, ontime: 1, action_yes: 1, purpose_met: 0, completion_pct: 40,  timeliness_pct: 50,  action_pct: 50,  purpose_pct: 0,  efficiency_score: 32.0, trend: 'down', signal_breakdown: { bd_tag: 0, ai_inference: 0, funnel_movement: 0 } },
  ],
};

// Today's tasks for the logged-in BD (used by ActivityScreen chips + YouScreen card)
export const MY_TASKS_TODAY = [
  { planner_id: 9001, task_type: 'sales_call', lead_id: 'L-1042', lead_name: 'KV Andheri',         purpose: 'Get DM appointment confirmed',     scheduled_at: '09:00', executed_at: '09:04', is_done: true,  outcome_tag: null,      action_taken: true,  inferred_purpose: null },
  { planner_id: 9002, task_type: 'email',     lead_id: 'L-1037', lead_name: 'Sarvodaya Vidyalaya', purpose: 'Send revised proposal v2',         scheduled_at: '10:30', executed_at: '10:32', is_done: true,  outcome_tag: 'met',     action_taken: true,  inferred_purpose: null },
  { planner_id: 9003, task_type: 'visit',     lead_id: 'L-1041', lead_name: 'Govt. HS Pune',       purpose: 'Walk-through + space measurement', scheduled_at: '11:00', executed_at: '11:15', is_done: true,  outcome_tag: 'partial', action_taken: true,  inferred_purpose: 'met' },
  { planner_id: 9004, task_type: 'sales_call', lead_id: 'L-1051', lead_name: 'DAV Pune',            purpose: 'Pricing pushback resolution',      scheduled_at: '15:00', executed_at: null,   is_done: false, outcome_tag: null,      action_taken: false, inferred_purpose: null },
  { planner_id: 9005, task_type: 'meeting',   lead_id: 'L-1040', lead_name: 'Z.P. Nashik',         purpose: 'Demo + Q&A with principal',        scheduled_at: '16:30', executed_at: null,   is_done: false, outcome_tag: null,      action_taken: false, inferred_purpose: null },
];

// My own daily score (BD self-view on YouScreen)
export const MY_EFFICIENCY_TODAY = {
  bd_id: 42, bd_name: 'Priya Menon',
  score_date: '2026-05-15',
  planned: 5, done: 3, ontime: 2, action_yes: 3, purpose_met: 2,
  completion_pct: 60, timeliness_pct: 67, action_pct: 100, purpose_pct: 67,
  efficiency_score: 71.7,
  yesterday_score: 89.6,
  last_7_avg: 78.2,
};
