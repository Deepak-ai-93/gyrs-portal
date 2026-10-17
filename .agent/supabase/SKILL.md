---
name: supabase
description: Design, implement, and review Supabase database, authentication, storage, realtime, and Edge Function integrations.
---

# Supabase

Use this skill when the task involves Supabase or Postgres-backed application services.

## Workflow

- Inspect the existing Supabase client setup, migrations, generated types, auth flow, and environment variable names before editing.
- Model data with explicit Postgres constraints, indexes, timestamps, and foreign keys rather than relying only on client-side checks.
- Treat Row Level Security as mandatory for exposed tables. Write policies around the authenticated user and role model, and test both allowed and denied access.
- Keep service-role keys server-side only. Browser code may use the publishable/anonymous key but must remain safe under RLS.
- Use migrations for schema and policy changes; keep generated types synchronized when the project uses them.
- For Storage, define bucket privacy and object policies deliberately. For Realtime, subscribe only to the tables/events the UI needs.

## Verification

- Apply or validate migrations in the project’s supported local/CI workflow.
- Test authentication, RLS, and storage access with at least one positive and one negative case.
- Check that secrets are not exposed in client bundles or committed files.
- Avoid destructive database operations unless explicitly requested and the target is confirmed.
