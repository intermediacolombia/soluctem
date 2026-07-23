# Repository Guidelines

## Project Structure & Module Organization

This repository contains the SOLUCTEM Cloud administrative panel, built as procedural PHP served directly by Apache. The root entry is `index.php`; the main interface lives under `admin/`.

- `admin/inc/`: shared configuration, database connection, headers, footers, and menus.
- `admin/login/`: authentication, session checks, and login UI.
- `admin/form/`: single service-form view, edit, approval, image upload, and delete handlers.
- `admin/form-list/`: DataTables listing, filters, CSV import, and bulk Excel export.
- `admin/users/`, `admin/profile/`, `admin/trash/`: users, profiles, and soft-delete workflows.
- `admin/css/`, `admin/js/`, `admin/images/`, `admin/uploads/`: assets and uploads.
- `api/`: external submission endpoints used by mobile or web clients.
- `vendor/`: Composer dependencies; do not edit generated vendor files.

## Build, Test, and Development Commands

- `composer install`: installs PHP dependencies, including PhpSpreadsheet.
- `composer update`: updates dependencies only when intentionally changing locked versions.
- `php -S 127.0.0.1:8000 -t .`: runs a local PHP server; Apache remains the production target.
- `php -l path/to/file.php`: lint a changed PHP file before committing.

There is no build pipeline. CSS, JavaScript, and PHP files are served directly.

## Coding Style & Naming Conventions

Follow the existing procedural PHP style. Use 4-space indentation in PHP blocks and keep page handlers focused on one workflow. Existing SQL uses `mysqli` through `$conn` from `admin/inc/config.php`; sanitize request values with `$conn->real_escape_string()` when matching local patterns. Prefer lowercase descriptive PHP filenames such as `get_formularios.php`.

Keep UI text consistent with the current Spanish-language admin experience.

## Testing Guidelines

No first-party automated test suite is present. Validate changes with:

- `php -l` on every modified PHP file.
- Manual browser checks for affected admin pages.
- AJAX endpoint checks for DataTables JSON: `draw`, `recordsTotal`, `recordsFiltered`, and `data`.
- Export checks when touching `excel/`, `generate-excel/`, or `form-list/export_excel.php`.

Vendor test directories under `vendor/` or bundled libraries are not project tests.

## Commit & Pull Request Guidelines

This checkout does not include Git history, so no repository-specific commit convention can be inferred. Use short, imperative commit messages, for example `Fix form image upload validation`.

Pull requests should include a summary, affected paths, manual test results, database or configuration impacts, and screenshots for visible UI changes. Link related issues when available.

## Security & Configuration Tips

Do not commit production credentials or generated logs. Treat `admin/inc/config.php`, upload handling, session checks, and token-authenticated API endpoints as sensitive. Preserve CSRF validation and role checks when changing form submissions or admin-only workflows.
