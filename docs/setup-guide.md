# AGIT Portal - Setup Guide

This guide explains how to set up the project on:

- Local machine (XAMPP/LAMP)
- cPanel (subdomain or subfolder)

---

## 1) Requirements

- PHP 7.4+ (8.x recommended)
- MySQL / MariaDB
- Apache with `mod_rewrite` enabled
- PHP extensions:
  - `pdo`
  - `pdo_mysql`
  - `session`
  - `json`
  - `openssl`

---

## 2) Project structure (important paths)

- Entry point: `index.php`
- Web rewrite rules: `.htaccess`
- App config: `config/app.php`
- DB config: `config/database.php`
- Main SQL: `sql/schema.sql`
- Uploads: `uploads/`

---

## 3) Local setup (XAMPP)

## Step 1: Place project

Put project in your web root, e.g.:

- `C:\xampp\htdocs\agit-portal`

## Step 2: Create database

Create a database (example):

- name: `agit_aams`
- charset/collation: `utf8mb4`

## Step 3: Import SQL

Import:

1. `sql/schema.sql`
2. Optional migration SQL files only if needed for existing/older DB
3. If assignment creation fails on older MySQL/MariaDB, run: `sql/003_assignments_compatibility.sql`

## Step 4: Configure DB connection

Edit `config/database.php`:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

## Step 5: Configure URL and rewrite

For local `/agit-portal` path:

- `.htaccess` -> `RewriteBase /agit-portal/`

Set APP_URL in `.htaccess` (recommended):

```apache
SetEnv APP_URL http://localhost/agit-portal
```

## Step 6: Permissions

Ensure these are writable by Apache/PHP:

- `uploads/profiles`
- `uploads/materials`
- `uploads/assignments`

## Step 7: Test

Open:

- `http://localhost/agit-portal/`

Default admin (from schema seed):

- Email: `admin@agit.edu`
- Password: `password`

Change this password immediately.

---

## 4) cPanel deployment setup

## Step 1: Upload/clone project

Clone into your target directory (subdomain root or subfolder).

## Step 2: Create DB and import SQL

- Create database + DB user
- Grant full privileges
- Import `sql/schema.sql`

## Step 3: Update DB credentials

In `config/database.php`, set production DB values.

## Step 4: Set correct APP_URL

In `.htaccess`, set:

```apache
SetEnv APP_URL https://your-domain-or-subdomain/path-if-any
```

Examples:

- Subfolder deployment:
  - `https://example.com/agit-portal`
- Subdomain root deployment:
  - `https://portal.example.com`

## Step 5: Set correct RewriteBase

In `.htaccess`:

- If deployed in subfolder `/agit-portal`:
  - `RewriteBase /agit-portal/`
- If deployed at subdomain root:
  - `RewriteBase /`

## Step 6: Upload folder permissions

Make `uploads` and subfolders writable.

## Step 7: Clear cache/opcache

After deploy:

- hard refresh browser (`Ctrl + F5`)
- clear OPcache/restart PHP handler from cPanel if available

---

## 5) Common issues and fixes

## Issue: Assets load from localhost in production

Symptoms:

- mixed content warnings
- CORS loopback errors
- logo/css missing

Fix:

1. Set correct `SetEnv APP_URL ...` in `.htaccess`
2. Ensure it is `https://...` on HTTPS sites
3. Refresh cache

## Issue: Login redirects to localhost

Fix:

- same as above: wrong `APP_URL`

## Issue: 404 for app routes

Fix:

1. Verify Apache rewrite is enabled
2. Ensure `.htaccess` is read (`AllowOverride All`)
3. Ensure `RewriteBase` matches deployment path

## Issue: Uploads fail

Fix:

1. check folder permissions
2. confirm PHP upload limits
3. confirm file type/size is allowed by app rules

---

## 6) Optional: Tailwind production build

The app currently uses Tailwind CDN in pages. It works, but browser console warns for production.

If you want to build static CSS on server:

```bash
cd /path/to/agit-portal
npm init -y
npm i -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
printf '@tailwind base;\n@tailwind components;\n@tailwind utilities;\n' > assets/css/tailwind.input.css
npx tailwindcss -i ./assets/css/tailwind.input.css -o ./assets/css/tailwind.generated.css --minify
```

Then include `assets/css/tailwind.generated.css` in layouts/pages.

---

## 7) Security checklist (recommended)

- Change default admin password
- Use strong DB credentials
- Force HTTPS
- Keep `APP_URL` on HTTPS
- Disable PHP execution in `uploads/` via server config
- Restrict direct DB/admin panel exposure
