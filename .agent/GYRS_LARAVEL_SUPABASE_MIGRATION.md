# GYRS Portal: Laravel + Supabase Migration Plan

## 1. Purpose

GYRS (Gujarat Youth Rojgar Setu) is a Government of Gujarat employment portal prototype for public vacancy discovery, candidate registration and login, job applications, application tracking, candidate skill matching, and Directorate administration.

This document is the source of truth for converting the current static HTML/CSS/JavaScript prototype into a production-ready Laravel + Supabase application while retaining the current product scope and moving the UI to a light, neutral visual system.

## 2. Current project audit

### Current stack

- Static HTML pages with no application framework or build pipeline.
- One shared stylesheet: `css/styles.css`.
- One large vanilla JavaScript file: `js/app.js`.
- Bootstrap 5.3.2, Font Awesome, Google Fonts, and CDN dependencies.
- `serve` is used for local development; Vercel serves the static site.
- Browser persistence through `localStorage`; there is no server, database, real authentication, authorization, file storage, or API.

### Current pages and responsibility

| Page | Current responsibility | Laravel target |
|---|---|---|
| `index.html` | Public job board, search, filters, job cards, FAQs, departments | Public vacancy routes/views/components |
| `job-details.html` | Full vacancy details, notification link, apply and sharing actions | Job detail route and application actions |
| `student-login.html` | OTP demo, password demo, candidate registration | Supabase Auth candidate flow plus Laravel profile setup |
| `student-dashboard.html` | Profile, applications, recommendations, skill upgrade | Authenticated candidate dashboard |
| `admin-login.html` | Demo admin username/password | Supabase Auth + admin role/policy |
| `admin-dashboard.html` | Job CRUD, candidate roster, status changes, audit table, CSV export | Protected admin dashboard and API/actions |
| `notification-preview.html` | WhatsApp/email template preview and sharing | Admin/candidate notification preview service/view |

### Existing data and behavior

- Six seeded jobs cover software, data entry, renewable energy, textile, apprenticeship, and cyber-security roles.
- Eight seeded candidate records contain profile, district, qualification, skills, status, and applied job IDs.
- Job fields include department, district, salary, type, qualification, skills, match score, official URLs, notification PDF, advertisement number, post count, dates, age, fees, vacancy breakup, eligibility, responsibilities, selection process, application steps, and description.
- Candidate flow supports OTP demo code `4826`, username/password demo login, registration, profile editing, application tracking, recommended jobs, resume text/file skill detection, and adding skills to the profile.
- Admin flow supports job create/edit/delete, candidate search/filter/edit/status changes/delete, candidate dossier view, CSV export, and application audit display.
- Notification flow builds WhatsApp, email, and copy-to-clipboard messages from job data.
- Existing browser storage keys are `gyrs_students_roster_v2`, `gyrs_auth_session_v2`, and `gyrs_jobs_data_v3`.

### Important prototype limitations

- Demo usernames, passwords, OTP, and candidate password values are stored in frontend code and must never be carried into production.
- Any visitor can inspect or modify `localStorage`; all current authorization is cosmetic.
- Job applications are only tracked locally and are not a durable server record.
- The notification PDF file input is frontend-only; uploaded files are not reliably persisted.
- The “AI” skill analyzer is deterministic keyword matching, not an AI service.
- Dates and some data are presentation strings rather than normalized database values.
- Data is rendered with template strings; the Laravel version must escape user-controlled output to prevent XSS.
- The current stylesheet is heavily tied to Bootstrap utility classes and a blue Naukri-inspired palette.

## 3. Target architecture

### Recommended responsibility split

```text
Browser
  -> Laravel web routes / Blade views / JS components
  -> Laravel controllers, Form Requests, policies, services
  -> Supabase Postgres (data, constraints, RLS)
  -> Supabase Auth (candidate/admin identity and sessions)
  -> Supabase Storage (notification PDFs and candidate documents)
```

Laravel is the application boundary: routing, validation, authorization orchestration, server-rendered pages or JSON endpoints, notifications, audit logging, and exports. Supabase is the managed backend: PostgreSQL, Auth, Storage, and optional Realtime. Do not expose the Supabase service-role key to the browser.

### Suggested Laravel structure

- `routes/web.php`: public pages and authenticated web flows.
- `routes/api.php`: only when a browser/API client needs JSON endpoints.
- `app/Models`: `Job`, `CandidateProfile`, `Application`, `Notification`, `RozgarMela`, `AuditLog`.
- `app/Http/Controllers/Public`: jobs and public content.
- `app/Http/Controllers/Candidate`: profile, applications, recommendations, skill analysis.
- `app/Http/Controllers/Admin`: jobs, candidates, applications, exports.
- `app/Http/Requests`: validated create/update requests.
- `app/Policies`: job management, candidate records, applications, documents.
- `app/Services`: matching, application tracking, notification templates, CSV export, storage.
- `resources/views`: layouts, public pages, candidate pages, admin pages, components.
- `resources/js`: small progressive-enhancement modules or the selected frontend adapter.
- `database/migrations` and `database/seeders`: schema, roles, seed data, and safe development fixtures.

## 4. Supabase data model

Use UUID primary keys where practical and retain the legacy IDs in a nullable `legacy_id` column during migration.

### `profiles`

Maps authenticated users to application roles.

- `id` UUID, references `auth.users.id`.
- `role`: `candidate` or `admin`.
- `full_name`, `email`, `phone`.
- `district_id` or normalized district value.
- `status`: `pending`, `verified`, `under_review`, `shortlisted`, `placed`, or `rejected`.
- `enrollment_id`, unique for candidates.
- `qualification`, `cgpa`, `registered_at`, timestamps.

### `profile_skills`

- `profile_id`, `skill_id`.
- Unique composite key to prevent duplicate skills.

### `skills`

- `name`, normalized slug, optional course/resource metadata.

### `jobs`

- `title`, `department`, `district`, `location`, `salary_text`.
- `qualification`, `employment_type`, `description`, `match_score`.
- `advertisement_number`, `total_posts`, `posted_at`, `last_date`.
- `age_limit`, `fees`, `vacancy_breakup`.
- `official_website_url`, `apply_url`, `notification_pdf_path`.
- `status`: `draft`, `published`, `closed`, or `archived`.
- `created_by`, timestamps.

### Job detail tables

- `job_skills(job_id, skill_id)`.
- `job_eligibility(job_id, body, sort_order)`.
- `job_responsibilities(job_id, body, sort_order)`.
- `job_selection_steps(job_id, body, sort_order)`.
- `job_application_steps(job_id, body, sort_order)`.

### `applications`

- `candidate_id`, `job_id`, unique pair.
- `status`: `applied`, `under_review`, `shortlisted`, `interview_call`, `placed`, `rejected`.
- `applied_at`, `status_changed_at`, `notes`.
- Optional external application reference and external portal URL.

### Supporting tables

- `application_status_history`: immutable status changes and actor.
- `audit_logs`: admin actions with actor, action, entity, metadata, and timestamp.
- `rozgar_melas`: title, district, venue, dates, registration URL, status.
- `notifications`: channel, recipient, template data, delivery status, sent timestamp.
- `districts`: canonical Gujarat district list; avoid free-text variants.

## 5. Authentication, authorization, and security

- Use Supabase Auth for candidate and admin identity. Decide whether Laravel uses Supabase JWT verification or a server-managed session bridge before implementation.
- Candidate OTP must use a real configured provider or Supabase-supported phone auth in production. Keep demo OTP only in local development fixtures.
- Passwords must never be stored in `profiles` or seeded as plaintext.
- Admin access must require a server-validated role, not a hidden link or frontend flag.
- Enable RLS on every Supabase table exposed to a client key.
- Candidates may read published jobs, manage only their own profile, read/create their own applications, and read only their own documents and notifications.
- Admins may manage jobs, candidate verification, applications, uploads, and audit views according to explicit policies.
- Validate URLs, dates, upload MIME type, upload size, and allowed file extensions server-side.
- Escape all dynamic output in Blade and sanitize any rich text intentionally allowed.
- Add rate limiting to OTP requests, login, registration, exports, and public search where needed.

## 6. UI and design system migration

The detailed cross-project UI/UX rules live in [DESIGN.md](./DESIGN.md). Every new page and component must follow that document.

### Required visual direction

- Light theme only for the first migration.
- Primary body text and neutral emphasis: `#747574`.
- Use white and very light neutral surfaces with clear borders and restrained shadows.
- Buttons are outline-first: transparent/light background, visible border, `#747574` or semantic accent text, and a subtle hover fill. Avoid filled primary buttons as the default.
- Keep filled treatments only for exceptional status indicators, destructive confirmation, or a clearly documented high-priority action.
- Preserve responsive behavior across mobile, tablet, and desktop.

### Proposed tokens

```css
:root {
  --color-text: #747574;
  --color-heading: #4f504f;
  --color-muted: #969896;
  --color-bg: #f7f8f7;
  --color-surface: #ffffff;
  --color-border: #dfe2df;
  --color-border-strong: #bfc4bf;
  --color-accent: #747574;
  --color-accent-hover: #5f605f;
  --color-focus: #8d9a91;
  --color-success: #547a61;
  --color-warning: #9a7840;
  --color-danger: #9a5d5d;
}
```

Define one reusable `.btn-outline` component and semantic variants. Replace scattered Bootstrap `btn-primary`, `btn-success`, `btn-danger`, and inline colors gradually. Keep status colors distinguishable and do not rely on color alone.

## 7. Route and feature inventory

### Public

- `GET /`: published job search and filters.
- `GET /jobs/{job}`: job details.
- `GET /notifications/preview`: notification preview for authorized preview use.
- `GET /jobs/{job}/notification`: secure PDF access or signed Supabase Storage URL.

### Candidate

- `GET /candidate/login`, `POST /candidate/otp/request`, `POST /candidate/otp/verify`.
- `GET /candidate/register`, `POST /candidate/register`.
- `GET /candidate/dashboard`.
- `PATCH /candidate/profile`.
- `POST /jobs/{job}/applications`.
- `GET /candidate/applications`.
- `POST /candidate/skills/analyze`.
- `POST /candidate/skills`.

### Admin

- `GET /admin/dashboard`.
- Resource actions for jobs: list, create, update, publish/close, archive.
- Candidate roster: list, filter, inspect, verify/status update.
- Applications audit: list, status update, history.
- `GET /admin/exports/candidates.csv` with authorization and audit log.
- Notification template preview/send actions with rate limits and delivery logging.

## 8. Migration strategy

1. Freeze the prototype data contract and export the three localStorage collections from a representative browser session.
2. Create the Laravel shell and Supabase project configuration without deleting the static prototype.
3. Build migrations, RLS policies, seeders, and an import script for six jobs and eight candidate fixtures.
4. Implement authentication and role-based authorization before admin or candidate mutations.
5. Migrate public job browsing and job details first.
6. Migrate candidate profile and applications, then admin job and candidate management.
7. Move PDFs/documents to Supabase Storage and replace local notification behavior with Laravel services.
8. Apply the light design tokens and outline-button system across all views.
9. Run a parallel acceptance pass against the static prototype, then switch deployment from static Vercel hosting to the agreed Laravel host.

## 9. Definition of done

- No production authentication, user data, jobs, applications, or files depend on `localStorage`.
- Candidate and admin roles are enforced server-side and by Supabase RLS.
- Public users can search published jobs and view complete details without signing in.
- Candidates can authenticate, manage their profile, apply once per job, and track status history.
- Admins can manage jobs, candidates, applications, notification assets, and CSV exports.
- PDFs are stored securely with controlled access and validated uploads.
- All dynamic data is validated, escaped, and auditable.
- The interface is light, readable, responsive, uses `#747574` as the primary text direction, and uses outline-style buttons by default.
- Automated tests cover authentication, authorization, RLS, job visibility, duplicate applications, status transitions, uploads, and exports.
- A production deployment checklist covers environment variables, backups, logging, rate limits, email/SMS provider configuration, and rollback.

## 10. Existing references

- Cross-project UI/UX system: `.agent/DESIGN.md`.
- Product reference: `docs/PROJECT_DOCUMENTATION.md`.
- Notification wording: `docs/NOTIFICATION_TEMPLATES.md`.
- Client review notes: `docs/WHATSAPP_CLIENT_REVIEW.md`.
- Current implementation: `index.html`, `job-details.html`, `student-login.html`, `student-dashboard.html`, `admin-login.html`, `admin-dashboard.html`, `notification-preview.html`, `css/styles.css`, and `js/app.js`.
