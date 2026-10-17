# GYRS Portal — Gujarat Youth Rojgar Setu

Official employment portal for the Directorate of Employment & Training,
Government of Gujarat. Laravel 13 + Supabase Postgres.

## Stack

- **Laravel 13** (PHP 8.4) — routing, validation, authorization, Blade views, services.
- **Vite** — CSS/JS build (`resources/css/app.css`, `resources/js/app.js`).
- **Supabase Postgres** in production; **SQLite** for local development.
- **Vercel** serverless deployment via `vercel-php`.

The original static HTML/CSS/JS prototype is preserved under `prototype/` for
feature/design reference only and is not part of the running application.

## Project structure

```
app/            Controllers (Public/Auth/Candidate/Admin), Models, Requests,
                Policies, Services, Middleware
database/       Migrations, seeders, factories
resources/      Blade views + design-system CSS/JS
routes/web.php  Public, candidate, and admin route groups
api/index.php   Vercel serverless entry point (auto-provisions /tmp SQLite demo DB)
prototype/      Legacy static prototype (reference only)
.agent/         Design system, migration plan, TODO, build contract
docs/           Product documentation
```

## Local development

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

Dev credentials come from `GYRS_DEV_PASSWORD` (see `config/gyrs.php`); there are
no hardcoded demo credentials or OTP backdoors in the application code.

## Production (Supabase Postgres)

Set the following environment variables (never commit them):

```
DB_CONNECTION=pgsql
DB_HOST=db.<project-ref>.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=...
DB_SSLMODE=require
SUPABASE_URL=...
SUPABASE_ANON_KEY=...
SUPABASE_SERVICE_ROLE_KEY=...
APP_KEY=base64:...
```

`SUPABASE_SERVICE_ROLE_KEY` is server-side only and must never reach the browser.

## Deploy on Vercel

```bash
vercel env add APP_KEY production
vercel deploy --prod
```

`vercel.json` + `api/index.php` run Laravel as a serverless function. Without a
configured database the entry point provisions a demo SQLite database in `/tmp`
and seeds it on cold start; configure Supabase Postgres above for durable data.

> Deployment Protection: disable **Vercel Authentication** in
> Project → Settings → Deployment Protection to make the site publicly reachable.
