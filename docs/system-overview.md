# AGIT Portal - System Overview

This document explains, in simple terms, how the AGIT Portal works.

## 1) What this system is

AGIT Portal is a school management web app with 3 user roles:

- **Admin**: manages students, lecturers, classes, subjects, schedules, results, settings, and reports.
- **Faculty (Lecturer)**: manages teaching activities like classes, exams, scores, materials, and assignments.
- **Student**: accesses courses, exams, schedules, materials, assignments, and results.

The app is built with:

- **Backend**: PHP (plain PHP, no Laravel/CodeIgniter framework)
- **Database**: MySQL (via PDO)
- **Frontend**: Server-rendered PHP views + Tailwind classes + vanilla JavaScript

---

## 2) High-level request flow

Every request goes through one entry point:

- `index.php`

Flow:

1. Apache rewrites requests to `index.php` (via `.htaccess`).
2. `index.php` loads configs, DB, helpers, middleware.
3. It checks if route is API (`/api/...`) or page route.
4. For API routes, it returns JSON.
5. For page routes, it renders the correct layout and view.

---

## 3) Core architecture

## Routing

- File: `index.php`
- Handles:
  - API routes (`/api/auth/...`, `/api/admin/...`, `/api/faculty/...`, `/api/student/...`)
  - View routes (`/`, `/login/*`, `/admin/*`, `/faculty/*`, `/student/*`)

## Config

- `config/app.php`
  - App constants (`APP_NAME`, `APP_URL`, paths, upload limits, session constants).
- `config/database.php`
  - PDO connection settings and DB helper.

## Helpers

- `helpers/functions.php`
  - JSON responses, validation, sanitization, pagination, uploads, formatting.
- `helpers/auth.php`
  - Login/logout/session logic, role checks, activity logging.
- `helpers/middleware.php`
  - Route guards for Admin / Faculty / Student endpoints.

## APIs

- `api/auth.php`: login/logout/register
- `api/profile.php`: profile read/update/password/picture
- `api/admin/*`: admin operations
- `api/faculty/*`: lecturer operations
- `api/student/*`: student operations
- `api/common/*`: shared endpoints (for example announcements)

## Views

- `views/auth/*`: login/register pages
- `views/admin/*`, `views/faculty/*`, `views/student/*`
  - each role has `layout.php` + page files
- `views/landing.php`: public homepage

## Assets

- `assets/css/custom.css`: custom styles and responsiveness
- `assets/js/app.js`: frontend utilities (API wrapper, toast/modal helpers, etc.)

---

## 4) Authentication and role behavior

Login flow:

1. User submits login form.
2. Frontend calls `POST /api/auth/login`.
3. Backend verifies credentials and role.
4. Session values are set.
5. User is redirected to role dashboard.

Session checks are enforced by middleware on protected routes.

---

## 5) Admin permission model

Admins can be:

- **Complete** (full access)
- **Limited** (only selected pages)

Limited access uses `admin_permissions.allowed_pages` and checks inside:

- `views/admin/layout.php`

If a limited admin tries to open a page they are not allowed to access, they are redirected to admin dashboard.

---

## 6) Database overview

Main schema is in:

- `sql/schema.sql`

It contains core tables for:

- users (`admins`, `lecturers`, `students`)
- academics (`classes`, `subjects`, assignments/mappings)
- exams/results/scores
- schedules/materials/assignments
- settings/activity logs/permissions

Extra SQL files:

- `sql/001_class_schedules.sql`
- `sql/002_add_profile_picture_lecturers.sql`

---

## 7) Important deployment behavior

The app URL should point to your real domain/subdomain path:

- `APP_URL` from environment (recommended), or auto-detected fallback

Examples:

- Subdirectory: `https://example.com/agit-portal`
- Subdomain root: `https://portal.example.com`

If `APP_URL` is wrong (for example localhost in production), assets and redirects will break.

---

## 8) Typical user journeys

### Admin

- Login -> Dashboard -> Manage records -> Publish schedules/results -> Settings.

### Faculty

- Login -> Classes/Exams/Scores -> Upload materials -> Manage assignments.

### Student

- Login -> Courses/Materials/Schedule -> Take exams -> View results.

---

## 9) Where to start if you want to modify features

- Routing changes: `index.php`
- Auth/session behavior: `helpers/auth.php`, `helpers/middleware.php`
- New API feature: `api/<role>/...` + corresponding view JS calls
- Page UI update: `views/<role>/<page>.php` + `assets/css/custom.css`
- DB changes: `sql/schema.sql` (and migration SQL files)
