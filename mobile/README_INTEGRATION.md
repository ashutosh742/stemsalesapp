# STEM CRM Mobile Client

React Native client for the STEM CRM backend. Lives in the same repo as the backend for sync.

## Folder map

- `App.js` - root navigator
- `src/screens/` - 49 screens covering BD, CM, marketing flows
- `src/state/session.js` - auth + mobile_session token storage
- `src/theme/colors.js` - shared design tokens
- `android/` - native Android shell
- `ios/` - native iOS shell

## Backend integration

The client talks to the backend via 5 mobile endpoints landed in PR #9 (migration 043):

| Endpoint | Purpose |
|---|---|
| POST /api/login | Mints mobile_session token (no bearer) |
| GET /api/session | Validates token (dual: digest or session) |
| GET /api/day_pack | BD day pack |
| POST /api/draft | MoM draft |
| POST /api/run_tool | 7-tool whitelist dispatcher |

When migration 044 (marketing module) lands, 38 additional endpoints become available under `/api/marketing/*`.

## Build steps (staging)

```bash
cd mobile
npm install
# Android
npx react-native run-android
# iOS
cd ios && pod install && cd .. && npx react-native run-ios
```

## Auth flow

1. App calls `/api/login` with `{uid, device_id}` (no bearer)
2. Backend returns `{token, expires_at}` (64-char mobile_session)
3. App stores token in src/state/session.js
4. All subsequent calls use `Authorization: Bearer <token>`
5. On 401, app re-runs login flow

## Source of truth

This folder is the live mobile codebase. The old standalone repo `ashutosh742/stemsalesapp-mobile` is deprecated; do not push there.
