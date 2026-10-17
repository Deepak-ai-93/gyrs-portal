# GYRS Laravel + Supabase Migration TODO

This is the execution checklist for converting the current static prototype into the target application described in [GYRS_LARAVEL_SUPABASE_MIGRATION.md](./GYRS_LARAVEL_SUPABASE_MIGRATION.md).

Status values: `[ ]` not started, `[~]` in progress, `[x]` complete, `[!]` blocked or requiring a decision.

## Phase 0 — Decisions and baseline

- [ ] Confirm Laravel version, PHP version, Node/build choice, hosting target, and Supabase project.
- [ ] Confirm whether Laravel renders Blade pages, exposes an API, or uses a hybrid approach.
- [ ] Confirm Supabase Auth integration strategy: Laravel session bridge or verified Supabase JWT.
- [ ] Confirm real SMS/OTP provider and transactional email provider.
- [ ] Confirm admin roles, approval workflow, and who can publish/close a job.
- [ ] Record the current prototype as the visual and functional baseline.
- [ ] Export and preserve representative localStorage data before migration.

## Phase 1 — Laravel and Supabase foundation

- [ ] Create Laravel application and environment configuration.
- [ ] Add Supabase URL, publishable key, server key, and database connection secrets to environment configuration.
- [ ] Add `.env.example` entries without real credentials.
- [ ] Configure logging, error handling, queue strategy, mail, storage, and cache.
- [ ] Create base layout, navigation, flash/toast messages, pagination, and form error components.
- [ ] Add route groups for public, candidate, and admin areas.
- [ ] Add CI checks for formatting, static analysis, tests, and build.

## Phase 2 — Database, RLS, and seed data

- [ ] Create district and skill reference tables.
- [ ] Create profiles and role model linked to Supabase Auth users.
- [ ] Create jobs and normalized job detail tables.
- [ ] Create applications and application status history.
- [ ] Create rozgar melas, notifications, and audit logs.
- [ ] Add indexes for job status, district, type, last date, application candidate/job, and search fields.
- [ ] Add foreign keys, unique constraints, check constraints, and timestamps.
- [ ] Write RLS policies for public jobs, candidate-owned records, and admin records.
- [ ] Seed six current jobs and eight development candidate fixtures without plaintext production passwords.
- [ ] Build and test a one-time importer for legacy localStorage JSON.

## Phase 3 — Authentication and authorization

- [ ] Implement candidate registration with validated phone, profile, district, qualification, and skills.
- [ ] Implement production OTP request, verification, expiry, resend limits, and rate limiting.
- [ ] Implement password/session login only if required by the final product decision.
- [ ] Implement admin login through the same trusted identity system with role checks.
- [ ] Add candidate and admin middleware/policies.
- [ ] Add logout, session expiry, unauthorized redirects, and audit events.
- [ ] Remove all demo credentials and OTP behavior from production code.

## Phase 4 — Public job experience

- [ ] Rebuild the home route from `index.html` as a Laravel view/component.
- [ ] Load only published, non-expired jobs by default.
- [ ] Implement keyword, district, employment type, skill, pagination, and reset filters.
- [ ] Rebuild job cards with safe escaped data and empty/loading/error states.
- [ ] Rebuild the complete job details view, including eligibility, vacancy, selection, steps, notification, and external apply link.
- [ ] Add secure notification PDF access through Supabase Storage.
- [ ] Preserve related/similar job behavior with database queries.
- [ ] Migrate FAQs, departments, industries, and government contact content.

## Phase 5 — Candidate workspace

- [ ] Rebuild candidate dashboard and profile dossier.
- [ ] Implement profile edit validation and persistence.
- [ ] Implement create-application action with duplicate protection and audit entry.
- [ ] Implement application status pipeline and status history.
- [ ] Implement recommended jobs based on district, skills, qualification, and published status.
- [ ] Port deterministic resume/skill matching as a service; document it as rule-based unless a real AI provider is approved.
- [ ] Add candidate skill management and course/resource references.
- [ ] Add Rozgar Mela data and registration links.

## Phase 6 — Admin workspace

- [ ] Rebuild admin dashboard KPIs from database queries.
- [ ] Implement job create/edit validation with draft/publish/close states.
- [ ] Implement notification PDF URL/file upload to Supabase Storage.
- [ ] Validate PDF type and size; define replacement and cleanup behavior.
- [ ] Implement candidate roster search, district/status filters, dossier view, and status changes.
- [ ] Implement application audit list and status transitions with history.
- [ ] Implement CSV export with authorization, stable columns, and audit logging.
- [ ] Add admin action confirmation for delete/archive and other irreversible operations.

## Phase 7 — Notifications and integrations

- [ ] Move WhatsApp and email templates into a Laravel notification/template service.
- [ ] Preserve the approved wording in `docs/NOTIFICATION_TEMPLATES.md`.
- [ ] Add escaped links, consistent sender identity, and delivery/error logging.
- [ ] Decide whether messages are user-triggered only or can be queued automatically.
- [ ] Add queue/retry behavior for email or SMS where applicable.
- [ ] Add notification preview using a selected real job record.

## Phase 8 — Light visual system and accessibility

Follow [DESIGN.md](./DESIGN.md) for every visual decision and component implementation.

- [ ] Replace blue-first tokens in `css/styles.css` with the neutral light token system.
- [ ] Make `#747574` the primary body text color and define darker heading contrast.
- [ ] Convert primary actions to transparent outline buttons with hover/focus states.
- [ ] Keep semantic success, warning, and danger variants distinguishable without relying on color alone.
- [ ] Remove dark login surfaces and inconsistent inline colors.
- [ ] Standardize borders, radii, shadows, focus rings, spacing, and typography.
- [ ] Test keyboard navigation, focus order, labels, errors, contrast, reduced motion, and screen-reader landmarks.
- [ ] Test mobile, tablet, desktop, print/export, and long job content layouts.

### Design system implementation tasks

- [ ] Create shared tokens for color, spacing, typography, radii, shadows, and focus states.
- [ ] Create shared layout shells for public, candidate, and admin experiences.
- [ ] Build reusable button, link, card, form, badge, alert, table, modal, pagination, and empty-state components.
- [ ] Add loading/skeleton, validation-error, network-error, forbidden, closed, and success states to every server-backed screen.
- [ ] Refactor inline styles and scattered Bootstrap color utilities into shared components/tokens.
- [ ] Make all modals keyboard-safe with focus trap, accessible names, and focus restoration.
- [ ] Review public home, job details, login, candidate dashboard, admin dashboard, and notification preview against `DESIGN.md`.
- [ ] Complete a visual QA pass at 320px, 768px, 1024px, wide desktop, and 200% zoom.

## Phase 9 — Testing and data validation

- [ ] Unit-test matching, date rules, application status transitions, and notification formatting.
- [ ] Feature-test public job visibility and filter combinations.
- [ ] Feature-test candidate ownership and duplicate application prevention.
- [ ] Feature-test admin authorization, CRUD, upload validation, and exports.
- [ ] Test Supabase RLS with allowed and denied candidate/admin scenarios.
- [ ] Test OTP expiry, rate limits, invalid codes, resend behavior, and logout.
- [ ] Run browser/end-to-end tests for all primary journeys.
- [ ] Validate imported counts and field mappings against the six jobs and eight candidate fixtures.

## Phase 10 — Deployment and launch

- [ ] Configure production Supabase project, database backups, RLS, storage buckets, and retention rules.
- [ ] Configure Laravel host, HTTPS, environment variables, queues, cron, cache, and log monitoring.
- [ ] Configure trusted domains, CORS/CSRF, email/SMS providers, and external recruitment links.
- [ ] Add health checks, error reporting, audit retention, and operational runbook.
- [ ] Perform security review for secrets, authorization, file uploads, XSS, CSRF, rate limits, and data exposure.
- [ ] Run staging acceptance with public, candidate, and admin accounts.
- [ ] Import approved production data and verify counts.
- [ ] Publish launch checklist and rollback plan.
- [ ] Retire static localStorage mutation code only after production acceptance is complete.

## Current blockers / decisions

- [ ] Choose Laravel hosting and deployment model.
- [ ] Choose Laravel–Supabase authentication/session approach.
- [ ] Confirm whether the frontend remains Blade/Bootstrap or moves to a JavaScript framework.
- [ ] Confirm real OTP, email, and WhatsApp delivery requirements.
- [ ] Confirm production data ownership, admin roles, and retention requirements.
