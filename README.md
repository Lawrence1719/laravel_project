# Laravel + Vue Dynamic Demo (Render-ready)

This repository contains a **deployment scaffold** that builds a Laravel app during Render build and applies dynamic Vue-based pages.

## What it does

- Creates a fresh Laravel project into `app/` during build.
- Adds a dynamic dashboard page (`/`) powered by Vue 3.
- Adds a simple Vue auth page (`/auth`) with login and register.
- Exposes API endpoints:
  - `GET /api/stats`
  - `POST /api/track-visit`
  - `POST /api/register`
  - `POST /api/login`
  - `POST /api/logout`
  - `GET /api/user`
- Stores simple dynamic counters in `storage/app/dashboard_stats.json`.

## Deploy to Render

1. Push this repo to GitHub.
2. In Render, create a **Web Service** from this repo.
3. Render auto-detects `render.yaml` and uses:
   - Build: `bash scripts/render-build.sh`
   - Start: `bash scripts/render-start.sh`

## Local run (requires internet for first setup)

```bash
bash scripts/render-build.sh
bash scripts/render-start.sh
```

Then open:
- `http://127.0.0.1:8000/` (dynamic dashboard)
- `http://127.0.0.1:8000/auth` (login/register page)
