# Javad-Aemeh-Magazine

This document lists key fixes applied and how to run the project.

## How to run locally

1. Copy `.env.example` to `.env`, configure DB (SQLite supported). For SQLite use:
   - `DB_CONNECTION=sqlite`
   - Create `database/database.sqlite`
2. Install dependencies:
   - `composer install`
   - `php artisan key:generate`
   - `php artisan migrate --seed`
3. Build frontend (optional):
   - `npm install && npm run build`
4. Storage symlink:
   - `php artisan storage:link`
5. Run server:
   - `php artisan serve`

## Security and Bug Fixes (CHANGED markers in code)

All edits are tagged with `CHANGED:` comments in code. Highlights:

- Auth
  - AuthController: fixed register validation, logout redirect, and reset password validation.
  - Requests: `RegisterRequest` and `Auth\ChangePasswordRequest` authorize for guests.

- Comments
  - Unified field to `body` across migration, model, requests, controllers, and views.
  - Views now post `body` and render `comment->body`.

- Routes and Controllers
  - Download route points to `DownloadController` and checks `storage/public` correctly.
  - User delete changed to HTTP DELETE instead of GET.
  - Admin role check fixed to use `hasAnyRole(['admin','super admin'])`.
  - Admin `createModel` uses correct table name, captcha field, and added `createLink`, `indexReports` stub, `approveAllPosts` bulk approve.

- Writer flow
  - Fixed magazine article creation (file inputs and `body` field).
  - Guarded optional image in news creation.

- Search
  - `SearchRequest` accepts `Khabar` instead of `New`.
  - Search view shows morph categories correctly.

- Views and assets
  - All images saved to public disk now use `asset('storage/...')`.
  - Fixed dynamic route building in ArticleShow.
  - Fixed broken links in admin/panel headers and removed non-existing `report` link.

## Notes

- Ensure `php artisan storage:link` so images render via `asset('storage/...')`.
- reCAPTCHA posts `g-recaptcha-response`; all validations updated to match.


