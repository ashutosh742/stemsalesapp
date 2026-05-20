// Mock CRM data — based on STEM Learning India's real programs
export const kpis = [
  { id: 'schools', label: 'Schools',  value: 5247, delta: '+12 this week', color: '#3498DB', icon: 'school' },
  { id: 'leads',   label: 'Open Leads', value: 184,  delta: '+8 today',     color: '#F39C12', icon: 'trending-up' },
  { id: 'projects',label: 'Active Projects', value: 67, delta: '4 closing soon', color: '#2ECC71', icon: 'rocket' },
  { id: 'visits',  label: 'Visits this month', value: 312, delta: '+24% vs last', color: '#9B59B6', icon: 'map' },
];

export const leads = [
  { id: 'L-1042', school: 'Kendriya Vidyalaya, Andheri', city: 'Mumbai', stage: 'Proposal Sent', program: 'Mini Science Centre', value: '₹4.2L', updated: '2h ago', owner: 'Priya M.' },
  { id: 'L-1041', school: 'Govt. Higher Secondary, Pune', city: 'Pune', stage: 'Site Visit', program: 'Tinker Lab', value: '₹6.8L', updated: '5h ago', owner: 'Ravi K.' },
  { id: 'L-1040', school: 'Z.P. School, Nashik', city: 'Nashik', stage: 'Negotiation', program: 'Astronomy Lab', value: '₹3.1L', updated: '1d ago', owner: 'Anita S.' },
  { id: 'L-1039', school: 'Municipal School, Borivali', city: 'Mumbai', stage: 'New', program: 'ESG Lab', value: '₹5.4L', updated: '1d ago', owner: 'Priya M.' },
  { id: 'L-1038', school: 'DAV Public School', city: 'Nagpur', stage: 'Proposal Sent', program: 'Mini Science Centre', value: '₹4.2L', updated: '2d ago', owner: 'Vikram T.' },
  { id: 'L-1037', school: 'Sarvodaya Vidyalaya', city: 'Delhi', stage: 'Site Visit', program: 'BALA Painting', value: '₹2.0L', updated: '2d ago', owner: 'Anita S.' },
  { id: 'L-1036', school: 'Zilla Parishad, Aurangabad', city: 'Aurangabad', stage: 'Won', program: 'DIY Programs', value: '₹1.8L', updated: '3d ago', owner: 'Ravi K.' },
];

export const schools = [
  { id: 'S-2201', name: 'Kendriya Vidyalaya, Andheri', city: 'Mumbai', state: 'Maharashtra', students: 1240, programs: ['Mini Science Centre', 'Tinker Lab'], status: 'Active' },
  { id: 'S-2202', name: 'Govt. Higher Secondary, Pune', city: 'Pune', state: 'Maharashtra', students: 980, programs: ['Tinker Lab'], status: 'Active' },
  { id: 'S-2203', name: 'Z.P. School, Nashik', city: 'Nashik', state: 'Maharashtra', students: 540, programs: ['Astronomy Lab'], status: 'Active' },
  { id: 'S-2204', name: 'Municipal School, Borivali', city: 'Mumbai', state: 'Maharashtra', students: 720, programs: ['ESG Lab'], status: 'Onboarding' },
  { id: 'S-2205', name: 'DAV Public School', city: 'Nagpur', state: 'Maharashtra', students: 1500, programs: ['Mini Science Centre', 'DIY'], status: 'Active' },
  { id: 'S-2206', name: 'Sarvodaya Vidyalaya', city: 'Delhi', state: 'Delhi', students: 860, programs: ['BALA Painting'], status: 'Active' },
];

export const projects = [
  { id: 'P-501', name: 'MSC Rollout — Mumbai Cluster', partner: 'NPCI CSR', schools: 24, progress: 78, due: 'Jun 2026', status: 'On Track' },
  { id: 'P-502', name: 'Tinker Lab — Pune District', partner: 'Brillio', schools: 12, progress: 45, due: 'Aug 2026', status: 'On Track' },
  { id: 'P-503', name: 'Astronomy Lab — Tribal Schools', partner: 'Punjab Chemicals', schools: 8, progress: 92, due: 'May 2026', status: 'At Risk' },
  { id: 'P-504', name: 'BALA Painting — Delhi NCR', partner: 'L&T Foundation', schools: 30, progress: 20, due: 'Dec 2026', status: 'On Track' },
  { id: 'P-505', name: 'ESG Lab Pilot', partner: 'TCS Foundation', schools: 5, progress: 100, due: 'Apr 2026', status: 'Completed' },
];

export const activity = [
  { id: 1, who: 'Priya M.',  what: 'closed lead', target: 'L-1036 · ₹1.8L', when: '10 min ago', color: '#2ECC71' },
  { id: 2, who: 'Ravi K.',   what: 'scheduled visit', target: 'Govt. Higher Sec., Pune', when: '1h ago', color: '#3498DB' },
  { id: 3, who: 'Anita S.',  what: 'uploaded report', target: 'Astronomy Lab — Tribal', when: '2h ago', color: '#9B59B6' },
  { id: 4, who: 'Vikram T.', what: 'added lead', target: 'DAV Public School', when: '4h ago', color: '#F39C12' },
  { id: 5, who: 'System',    what: 'flagged risk', target: 'P-503 due May 2026', when: '6h ago', color: '#E74C3C' },
];

export const stageColors = {
  'New': '#9CA3AF',
  'Site Visit': '#3498DB',
  'Proposal Sent': '#F39C12',
  'Negotiation': '#9B59B6',
  'Won': '#2ECC71',
  'Lost': '#E74C3C',
};
