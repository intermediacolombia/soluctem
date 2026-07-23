# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SOLUCTEM Cloud Admin is a Spanish-language web-based administrative panel for managing technical service forms (formularios de servicio técnico), user accounts, and data exports across multiple zones/departments. It integrates with an external API (`../api/submit.php`) that receives form submissions from mobile/web clients.

## Tech Stack

- **Backend**: PHP (procedural, no framework), MySQL (`soluctem_sistema` database)
- **Frontend**: Bootstrap 4.5.2, jQuery 3.6.0, DataTables 1.11.5 — all via CDN
- **Excel Export**: PHPOffice/PhpSpreadsheet (Composer-managed)
- **Server**: Apache with PHP sessions, GD library for image compression

## Setup & Dependencies

```bash
# Install PHP dependencies
composer install
```

No build pipeline — files are served directly by Apache. Configure the database connection in `inc/config.php`. The production URL is `https://sistema.soluctem.com.co`.

## Architecture

### Request/Response Pattern

All pages are server-side rendered. The typical flow:

1. Page includes `login/sesion.php` (redirects to login if no session)
2. HTML form POSTs to a handler (e.g., `form/update.php`)
3. Handler performs MySQL query, sets a session message, and redirects
4. AJAX endpoints return JSON consumed by DataTables or custom JS

### Authentication & Authorization

- Session vars set in `login/authenticate.php`: `id_usuario`, `nombre_usuario`, `rol`, `nombre`, `apellido`, `zonas` (comma-separated department IDs)
- Two roles: **Administrador** (full access) and **Usuario** (restricted to assigned `zonas`)
- CSRF tokens are generated per session and validated on form submissions
- Role checks use `$_SESSION['rol'] === 'Administrador'`

### Key Directories

| Path | Purpose |
|------|---------|
| `inc/` | Shared config, header/footer/menu includes |
| `login/` | Auth pages and session guard (`sesion.php`) |
| `form/` | View, edit, approve, delete single forms |
| `form-list/` | Paginated form list (DataTables + AJAX), Excel bulk export |
| `users/` | User CRUD (admin only) |
| `excel/` | Single-form Excel export via PhpSpreadsheet |
| `trash/` | Soft-deleted forms — restore or permanently delete |
| `cron/` | Scheduled DB backup scripts |
| `../api/` | External API endpoint for form submissions (token-auth) |

### Core Database Tables

- **`formulario`** — Service request forms. Key fields: `estado` (0=pending, ≥1=approved), `borrado` (soft delete), `zona`/`departamento`, digital signatures, equipment info, evaluation ratings, contractor/officer data.
- **`imagenes`** — Images attached to forms (`formulario_id` FK).
- **`usuarios`** — User accounts with role, assigned zones, profile image.

### DataTables Pattern

Server-side processing is used throughout. AJAX endpoints (e.g., `form-list/get_formularios.php`) receive DataTables parameters (`draw`, `start`, `length`, `search[value]`) plus custom filter params, and return `{"draw":…, "recordsTotal":…, "recordsFiltered":…, "data":[…]}`.

### SQL Conventions

Queries use `mysqli` with `real_escape_string()` — **not** prepared statements. When adding queries, use `$conn->real_escape_string()` on all user input and follow the existing `$conn->query(...)` pattern. The connection is established in `inc/config.php` as `$conn`.

### Cron Jobs

`cron/cron.php` checks the current hour and runs `cron/bk.php` at 00:00 and 12:00 to back up the database.
