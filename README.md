# GYRS Portal — Gujarat Youth Rojgar Setu

Official AI-powered career portal UI (static site: HTML + CSS + JS, no build step).
Directorate of Employment & Training, Government of Gujarat.

## Project structure

```
.
├── index.html               # Public job board (home)
├── student-login.html       # Candidate login / OTP
├── student-dashboard.html   # Candidate dashboard
├── admin-login.html         # Admin login
├── admin-dashboard.html     # Admin panel
├── css/styles.css           # All styling
├── js/app.js                # App logic (jobs, auth, dashboards)
├── vercel.json              # Vercel static config
├── package.json             # Dev server (serve)
├── docs/                    # Project documentation (not deployed as pages)
└── tools/                   # Doc-generation scripts (not deployed as pages)
```

## Run locally

```bash
# any static server works:
npx serve . -l 8000
# or
python -m http.server 8000
```

Open http://localhost:8000/index.html

## Deploy on Vercel

1. Push this folder to a **new empty GitHub repo**.
2. Go to https://vercel.com/new → Import that repo.
3. Framework Preset: **Other**. Build Command: _(empty)_. Output Directory: _(empty / `.`)_.
4. Deploy. Every push to `main` redeploys automatically.

Demo credentials (frontend-only, localStorage):
- Candidate → `student` / `student123` (or mobile OTP `4826`)
- Admin → `admin` / `admin123`
