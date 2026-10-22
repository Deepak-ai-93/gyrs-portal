# GYRS Career Assistant — AI Résumé Analysis

Feature: candidates upload a résumé, receive a résumé snapshot, best-fit job
suggestions, and a 30/60/90-day skill upgradation plan.

The feature works without an AI key using a deterministic local analyser. Add
`AI_API_KEY` later to switch on the LLM provider — no code changes required.

## 1. Foundation

- [x] Add `TODO.md` feature checklist
- [x] Add `config/ai.php` with provider, model, limits, retention and prompt version
- [x] Add AI environment keys to `.env.example` and `.env`
- [x] Install server-side PDF text extraction (`smalot/pdfparser`)
- [x] Add migrations: `resume_analyses`, `resume_job_matches`,
      `candidate_skill_plans`, `learning_resources`
- [x] Add models, casts, relationships and encrypted résumé fields
- [x] Add `ResumeAnalysisPolicy` for owner-only access
- [x] Add rate limiter for résumé analysis

## 2. Résumé pipeline

- [x] `ResumeTextExtractor`: PDF, DOCX and pasted text
- [x] File validation: 3 MB cap, MIME/magic-byte checks, reject encrypted PDFs
- [x] Scanned-PDF detection with a "paste text instead" fallback
- [x] PII redaction before any LLM call
- [x] `LlmClient` adapter: Gemini, OpenAI-compatible and OpenRouter
- [x] `ResumeAnalyzer`: structured JSON extraction + validation + retry/repair
- [x] Deterministic fallback analyser when no AI key is configured
- [x] Prompt-injection resistant prompt and output sanitation

## 3. Job suggestions

- [x] Extend `JobMatchService` with a match breakdown and reasons
- [x] Filter to open jobs; weight skills, qualification, district and recency
- [x] Let AI explain only database-provided job IDs (no invented vacancies)
- [x] Save top matches in `resume_job_matches`
- [x] Show matched skills, gaps and "why this fits"
- [x] Save-as-interested action from a suggested job

## 4. Skill upgradation plan

- [x] Gap aggregation across top matched jobs
- [x] Priority, effort, 30/60/90-day roadmap
- [x] Curated `learning_resources` seed data (free India-friendly providers)
- [x] Practice project + proof-of-skill per recommended skill
- [x] Start / complete progress tracking
- [x] "Add to my profile" with candidate confirmation and match refresh

## 5. Candidate UI

- [x] Dashboard Career Assistant card with analysis status
- [x] `/candidate/career` upload, paste, consent and target-role form
- [x] Processing state with accessible progress steps
- [x] Results: Résumé snapshot, Best-fit jobs, Skill plan
- [x] Review modal before applying suggestions to the profile
- [x] Delete analysis and re-analyse actions
- [x] Empty, error, rate-limit and AI-unavailable states
- [x] Candidate navigation link + mobile-friendly layout
- [x] Use only existing `--color-*` tokens and shared components

## 6. Trust, privacy and operations

- [x] Consent recorded per analysis with prompt version
- [x] Raw uploaded file discarded after extraction
- [x] Structured results encrypted at rest
- [x] Retention cleanup command + schedule
- [x] Audit events for start, complete, fail, delete and apply
- [x] Daily/monthly rate limits and global monthly spend cap
- [x] "AI guidance only — verify official notification" disclaimer
- [x] Admin usage/cost summary (may follow in a later phase)

## 7. Verification

- [x] `php artisan migrate` on local SQLite (verified on an isolated temporary SQLite database)
- [x] Manual upload: text PDF, DOCX, pasted text (covered by feature tests)
- [x] Manual failure: scanned PDF, oversized file, unsupported type (covered by feature tests)
- [x] Confirm no AI key still produces a useful demo analysis
- [x] Confirm configured key calls the provider and validates JSON (provider calls mocked; no live API spend)
- [x] Confirm candidate cannot open another candidate's analysis
- [x] Confirm match/skill-plan actions update the profile only after confirmation
- [x] Rebuild Vite assets before any production push

## 8. Supabase PostgreSQL deployment

- [x] Configure Laravel's PostgreSQL connection to read `DB_URL` (or `DATABASE_URL`)
- [x] Add `composer migrate:supabase` to run the complete Laravel migration set
- [ ] Set a Supabase connection URL in local `.env` and apply/verify remote migrations

Laravel migrations are PHP files, so use `composer migrate:supabase` for this app.
Supabase CLI `db push` only applies SQL files under `supabase/migrations` and does
not execute Laravel migrations.
