# STEM CRMApp — Mobile (React Native / Expo)

Native iOS + Android prototype of the STEM Learning CRM, built **agent-first**:
the home screen is your 6 AI agents, and the classic CRM (leads / schools / projects /
reports) sits behind them as supporting views.

## What's inside

### Agents (the front door)
1. **Anaya** — Day-Start Planner. Morning briefing, pipeline diagnosis, "do these 3 things today".
2. **MoM Drafter** — Voice → AI MoM. Tap mic, talk, get a draft MoM + proposed next action.
3. **War Room** — Founder daily brief across clusters. Read-only.
4. **Dump Mining** — Surfaces dormant leads worth reviving with reasoning.
5. **CM Co-pilot** — Scores every BD's MoM and flags ones that need a CM's eye.
6. **Cadence + Star** — Discipline coach. Day-start streaks, plans, MoMs, DAR.

Each agent shows:
- **Allowed tools** (with permission + spend cap from the spec)
- **Tool-call cards** in chat that surface the **real PHP backend method** they hit
  (e.g. `get_bd_funnel → AnayaAgent::compute_bd_funnel`)
- **Insight cards**, **pattern cards**, and **approve / later** action rows

### CRM supporting tabs
Leads · Schools · Projects · Reports — the existing list views, mock-powered.

## Architecture

```
App.js
└── MainTabs (bottom tab navigator)
    ├── Agents (stack)
    │   ├── AgentsHubScreen      ← home: KPIs + Anaya teaser + 6 agent cards
    │   ├── AgentChatScreen      ← chat transcript w/ tool-call cards
    │   └── MomDrafterScreen     ← idle → recording → processing → draft
    ├── Leads
    ├── Schools
    ├── Projects
    └── Reports
```

### Backend wiring (`src/api/client.js`)
- Base URL: `https://stemapp.in/index.php`
- Session cookie persisted via `expo-secure-store`
- `login(userid, password)` → POST `/Menu/login` (form-encoded)
- Tool dispatcher: `tools.runTool(name, params)` → POST `/chat/api_run_tool`
- Convenience wrappers: `getBdFunnel`, `getBdDiscipline`, `findSimilarLeads`,
  `draftMom`, `transcribeAudio`, `scheduleFollowup`, `getFunnelReport`, etc.
- `USE_MOCKS = true` while the backend's JSON wrappers are being added.

### Tool → PHP map (`src/data/agents.js → TOOL_ENDPOINTS`)
Every spec tool name maps to a real method in the CodeIgniter app, e.g.:

| Spec tool | Backend |
|---|---|
| `get_bd_funnel` | `AnayaAgent::compute_bd_funnel` |
| `get_recent_moms` | `Menu_model::GetMomDataByTaskId` |
| `find_similar_leads` | `AIAgents/SameStatusSinceDays_model` |
| `draft_mom` | `MomController::api_draft` *(new)* |
| `score_mom_quality` | `ChatAI_model::scoreMoM` |

Full table lives in `../stem-mobile-preview/API_MAPPING.md`.

## Voice flow (MoM Drafter)
1. `Audio.requestPermissionsAsync()` → `Audio.Recording.createAsync(HIGH_QUALITY)`
2. Pulse animation + tabular-nums timer while recording
3. Stop → upload the m4a to `MomController::api_transcribe`
4. Drafted MoM rendered with summary · key-facts grid · proposed next action · quality score

In mock mode the audio is recorded but discarded, and a synthesized draft is shown
so the screen demos end-to-end without the backend.

## Stack
- Expo SDK 50, React Native 0.73, React 18.2
- `@react-navigation/native` 6 (bottom-tabs + native-stack)
- `expo-av` 13.10 for recording
- `expo-secure-store` 12.8 for session cookie
- `expo-linear-gradient` for brand surfaces
- `@expo/vector-icons` (Ionicons)

## Running locally

```bash
npm install
npx expo start
# press i for iOS, a for Android, or scan the QR with Expo Go
```

## Demo login (pre-filled)
- Username: `priya.menon`
- Password: `demo1234`

The login flow currently flips a local `signedIn` flag. To hit the real
`/Menu/login` endpoint, set `USE_MOCKS = false` in `src/api/client.js` and
remove the local short-circuit in `LoginScreen.js`.

## Files

```
App.js                              ← navigation root
src/api/client.js                   ← stemapp.in API client + tool dispatcher
src/data/agents.js                  ← 6 agents, TOOL_ENDPOINTS, scripted convs
src/data/mock.js                    ← leads / schools / projects / activity mocks
src/theme/colors.js                 ← brand palette from stemapp.in login
src/screens/AgentsHubScreen.js      ← agent-first home
src/screens/AgentChatScreen.js      ← chat with tool-call / insight / action cards
src/screens/MomDrafterScreen.js     ← voice → draft MoM flow
src/screens/LoginScreen.js          ← space-themed login (matches stemapp.in)
src/screens/LeadsScreen.js
src/screens/SchoolsScreen.js
src/screens/ProjectsScreen.js
src/screens/ReportsScreen.js
```

## What needs to happen on the backend
To switch off `USE_MOCKS` and run against stemapp.in:

1. Add thin JSON wrappers (return `application/json`, not full HTML pages) on:
   - `Menu/api_session` — current user info
   - `Anaya_reports/api_day_pack` — wraps `AnayaAgent::bd_daily_pack`
   - `chat/api_run_tool` — generic tool dispatcher (read existing AIAgents/* models)
   - `MomController/api_transcribe` and `MomController/api_draft` — voice + LLM
   - `Reports/api_leads`, `Management/api_schools`, `Reports/api_projects` — list endpoints
2. Allow CORS for the Expo dev origin (or run via tunnel).
3. Keep PHPSESSID auth — the app already persists the cookie.

## Related artifacts
- Live single-file preview: `../stem-mobile-preview/index.html`
- Spec ↔ backend cross-reference: `../stem-mobile-preview/API_MAPPING.md`
- Daily backend scan: `../stem-scan/` (PHP uploader + scheduled cron `5c855539`)
