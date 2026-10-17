# GYRS Build Contract (frozen interface)

Shared source of truth for the two build agents. **Backend agent owns** DB/models/routes/controllers/requests/policies/middleware/config/tests. **Frontend agent owns** `resources/css/app.css`, `resources/js/app.js`, and everything under `resources/views`. Neither edits the other's files.

Stack: Laravel 13 (PHP 8.4), Vite, Tailwind-free custom CSS using the tokens in `.agent/DESIGN.md`. DB: SQLite locally, Supabase Postgres in production (`pgsql`). Auth: Laravel session auth with a `role` column on `users` (`candidate` | `admin`). No demo credentials or OTP in production code paths.

## 1. Routes (backend owns `routes/web.php`)

Public (no auth):
| Method | URI | Action | Name | View |
|---|---|---|---|---|
| GET | `/` | `Public\JobController@index` | `home` | `public.home` |
| GET | `/jobs/{job:slug}` | `Public\JobController@show` | `jobs.show` | `public.jobs.show` |
| GET | `/health` | closure → `{"status":"ok"}` | `health` | — |

Candidate (`prefix candidate`, middleware `guest` where login/register):
| Method | URI | Action | Name | View |
|---|---|---|---|---|
| GET | `/candidate/login` | `Auth\CandidateLoginController@create` | `candidate.login` | `candidate.auth.login` |
| POST | `/candidate/login` | `Auth\CandidateLoginController@store` | — | — |
| GET | `/candidate/register` | `Auth\CandidateRegisterController@create` | `candidate.register` | `candidate.auth.register` |
| POST | `/candidate/register` | `Auth\CandidateRegisterController@store` | — | — |
| POST | `/candidate/logout` | `Auth\CandidateLoginController@destroy` | `candidate.logout` | — |
| GET | `/candidate/dashboard` | `Candidate\DashboardController@index` | `candidate.dashboard` | `candidate.dashboard` |
| PATCH | `/candidate/profile` | `Candidate\ProfileController@update` | `candidate.profile.update` | — |
| GET | `/candidate/applications` | `Candidate\ApplicationController@index` | `candidate.applications.index` | `candidate.applications.index` |
| POST | `/jobs/{job}/applications` | `Candidate\ApplicationController@store` | `jobs.applications.store` | — |

Admin (`prefix admin`, name `admin.`, middleware `auth` + `role:admin`; login uses `guest`):
| Method | URI | Action | Name | View |
|---|---|---|---|---|
| GET | `/admin/login` | `Auth\AdminLoginController@create` | `admin.login` | `admin.auth.login` |
| POST | `/admin/login` | `Auth\AdminLoginController@store` | — | — |
| POST | `/admin/logout` | `Auth\AdminLoginController@destroy` | `admin.logout` | — |
| GET | `/admin/dashboard` | `Admin\DashboardController@index` | `admin.dashboard` | `admin.dashboard` |
| — | `/admin/jobs` | `Route::resource('jobs', Admin\JobController::class)` | `admin.jobs.*` | `admin.jobs.index/create/edit/show` |
| GET | `/admin/candidates` | `Admin\CandidateController@index` | `admin.candidates.index` | `admin.candidates.index` |
| GET | `/admin/candidates/{candidate}` | `Admin\CandidateController@show` | `admin.candidates.show` | `admin.candidates.show` |
| PATCH | `/admin/candidates/{candidate}/status` | `Admin\CandidateController@updateStatus` | `admin.candidates.status` | — |
| GET | `/admin/applications` | `Admin\ApplicationController@index` | `admin.applications.index` | `admin.applications.index` |
| PATCH | `/admin/applications/{application}/status` | `Admin\ApplicationController@updateStatus` | `admin.applications.status` | — |
| GET | `/admin/exports/candidates.csv` | `Admin\ExportController@candidates` | `admin.exports.candidates` | — |

Route bindings: `{job:slug}` for public, `{job}` id for admin. `POST /jobs/{job}/applications` requires `auth` + candidate role.

## 2. Tables & models

UUID primary keys (`HasUuids`) everywhere except pivot tables. All have `timestamps()`.

- `users` — extends default: `name`, `email` (unique, nullable), `phone` (unique, nullable), `password`, `role` (`candidate|admin`), `district_id` (nullable →districts), `enrollment_id` (unique nullable), `qualification`, `cgpa`, `status` (`pending|verified|under_review|shortlisted|placed|rejected`), `approved_at` (nullable). Model `User`; `isAdmin()`, `isCandidate()`.
- `districts` — `name`, `slug` (unique), `region`, `sort_order`. Model `District`.
- `skills` — `name`, `slug` (unique). Model `Skill`.
- `jobs` — `slug` (unique), `title`, `department`, `district_id`, `location`, `salary_text`, `qualification`, `employment_type`, `description` (text), `match_score` (unsigned tinyint, default 0), `advertisement_number`, `total_posts` (unsigned int), `posted_at` (date), `last_date` (date), `age_limit`, `fees`, `vacancy_breakup`, `official_website_url`, `apply_url`, `notification_pdf_path` (nullable), `status` (`draft|published|closed|archived`, default `draft`), `created_by` (→users nullable). Model `Job`; scopes `published()`, `open()`, `search($term, $filters)`, `isExpired()`.
- `job_skills` — `job_id`, `skill_id` (unique pair).
- `job_eligibility` / `job_responsibilities` / `job_selection_steps` / `job_application_steps` — `id`, `job_id`, `body` (text), `sort_order` (unsigned int).
- `applications` — `candidate_id` (→users), `job_id`, `status` (`applied|under_review|shortlisted|interview_call|placed|rejected`, default `applied`), `applied_at`, `status_changed_at`, `notes`, `external_reference`, `external_url`. Unique (`candidate_id`,`job_id`). Model `Application`; `statusLabel()`, `statuses()`.
- `application_status_history` — `application_id`, `from_status`, `to_status`, `actor_id` (nullable →users), `notes`, `created_at`.
- `audit_logs` — `actor_id` (nullable), `action`, `entity_type`, `entity_id` (string), `metadata` (json), `created_at`. Model `AuditLog`.
- `rozgar_melas` — `title`, `district_id`, `venue`, `starts_at`, `ends_at`, `registration_url`, `status`. Model `RozgarMela`.
- `notifications` — `channel`, `recipient`, `template`, `payload` (json), `status`, `sent_at`. Model `Notification`.

Relationships: `Job` hasMany detail rows, belongsToMany `skills`, belongsTo `district`/`creator`, hasMany `applications`. `User` hasMany `applications`, belongsToMany `skills` (pivot `profile_skills`). `Application` belongsTo `candidate`/`job`, hasMany `histories`.

Seeders (idempotent, no plaintext prod passwords; dev passwords come from `GYRS_DEV_PASSWORD` env, default only in local): `DistrictSeeder` (all 33 Gujarat districts), `SkillSeeder` (~20), `JobSeeder` (6 jobs from `js/app.js` data incl. detail sections), `UserSeeder` (1 admin + 8 candidates, role-assigned), `RozgarMelaSeeder` (~4).

## 3. View paths & components (frontend owns)

Layouts: `layouts/public`, `layouts/candidate`, `layouts/admin`, plus `layouts/app` (simple). Each uses `@vite(['resources/css/app.css','resources/js/app.js'])`, includes a `<x-alert>` flash slot, skip-link, single `<h1>`.

Anonymous components (`resources/views/components/`):
`button` (props: `variant` = outline|ghost|danger|success|link default outline, `type`, `href`, `size`), `card` (`title`, `meta`, `footer` slots), `badge` (`variant`, `slot`), `status-badge` (`status`), `alert` (`variant` = success|warning|danger|info, dismissible), `input` (`name`,`label`,`type`,`value`,`required`,`hint`,`error`), `select` (`name`,`label`,`options` array`,`value`), `textarea`, `empty-state` (`title`,`message`,`action` slot), `modal` (`name`,`title`), `pagination` (wraps `$paginator->links()`).

Views: `public/home`, `public/jobs/show`, `candidate/auth/login`, `candidate/auth/register`, `candidate/dashboard`, `candidate/applications/index`, `admin/auth/login`, `admin/dashboard`, `admin/jobs/index|create|edit|show`, `admin/candidates/index|show`, `admin/applications/index`, `errors/403|404|419|500`.

Data passed to views (frozen):
- `public.home`: `$jobs` (paginator of published open jobs, each with `district`, `skills`, `match_score`, `salary_text`, `last_date`, `employment_type`), `$districts`, `$employmentTypes` (array), `$filters` (array: keyword, district, type), `$jobCount` (int).
- `public.jobs.show`: `$job` (with `skills`, `district`, detail sections), `$relatedJobs` (Collection).
- `candidate.dashboard`: `$user` (with `applications.job`), `$profileCompleteness` (int 0-100), `$recommendedJobs` (Collection), `$kpis` (array: applications, verified, shortlisted, interview_call), `$melas` (Collection).
- `candidate.applications.index`: `$applications` (paginator with `job`).
- `admin.dashboard`: `$kpis` (array: students, verified, active_jobs, applications), `$recentApplications` (Collection), `$recentJobs` (Collection).
- `admin.jobs.index`: `$jobs` (paginator with `district`), `$filters`, `$districts`.
- `admin.jobs.create`/`edit`: `$job` (edit), `$districts`, `$allSkills`.
- `admin.candidates.index`: `$candidates` (paginator), `$districts`, `$filters`.
- `admin.candidates.show`: `$candidate` (with `applications.job`, `skills`).
- `admin.applications.index`: `$applications` (paginator with `candidate`,`job`), `$filters`, `$statuses`.

## 4. Conventions

- Validation in Form Requests under `app/Http/Requests/{Candidate,Admin}`; authorization in policies `JobPolicy`, `ApplicationPolicy` (admin-only mutations; candidates own applications).
- Middleware `App\Http\Middleware\EnsureUserHasRole` registered as alias `role` in `bootstrap/app.php`.
- Services in `app/Services`: `JobSearchService`, `ApplicationService` (create + duplicate protection + history + audit), `ProfileCompletenessService`, `JobMatchService`, `NotificationTemplateService` (WhatsApp/email wording from `docs/NOTIFICATION_TEMPLATES.md`), `CandidateExportService` (CSV).
- Views must escape all dynamic output with `{{ }}`. Never render raw user HTML.
- All colors/spacing from `.agent/DESIGN.md` tokens; body text `#747574`; outline-first buttons; one dominant action per section.
- Include loading/empty/error/forbidden/success states where the screen is server-backed.
- Skills/persona assets: no CDN required; keep CSS self-contained.

## 5. Verification

- `php artisan test` (feature tests: job visibility/filters, duplicate application prevention, admin authorization, candidate ownership).
- `vendor/bin/pint --test` for formatting.
- `npm run build` must succeed.
