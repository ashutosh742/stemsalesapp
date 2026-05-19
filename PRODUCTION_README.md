# Production Snapshot — STEM Sales App

**Snapshot date:** 2026-05-19  
**Branch:** `production`  
**Status:** READ-ONLY

---

## Purpose

This branch is a one-time, read-only snapshot of the production CodeIgniter codebase as of **2026-05-19**. It is intended for backup and drift detection only.

## Rules

- **Do NOT commit changes to this branch.**
- **Do NOT use this branch for active development.**
- All staging and feature work must go through feature branches off `main`.
- This branch has an orphan history (no shared commits with `main`) to keep production and staging histories isolated.

## Key Locations

| Path | Description |
|------|-------------|
| `application/models/AIAgents/` | The 22 AIAgent model classes that power the sales app's AI features |
| `application/controllers/` | CodeIgniter controllers |
| `application/views/` | View templates |
| `application/models/` | All models (including the 22 AIAgents) |

## Cross-Reference: Staging

The `ChatAI` cache wrapper introduced in staging lives in:
- `staging/application/models/ChatAI_model.php` on the `main` branch

It is **not** part of this production snapshot (it has not been deployed to production yet).

## Drift Detection

To compare production against staging changes:
```bash
git diff main..production -- application/
```

---

*Maintained by STEM ops. For questions, contact stemlearning@gmail.com.*
