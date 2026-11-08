# RecordRoom

RecordRoom is a Laravel 11 document and records management application.

## Current features

### Phase 1 — Foundation
- Login and logout
- Active-account check
- User roles foundation
- Responsive application shell

### Phase 2 — Record management
- Create, list, view and edit records
- Generated unique reference numbers (`RR-YYYYMMDD-XXXXX`)
- Incoming, outgoing, internal and other document types
- Categories, department and document date
- Search by reference, title and department
- Filter by type and active / archived status
- Archive and restore records
- Dashboard counts and recently added records

## Requirements
- PHP 8.2+
- Composer
- A supported database (MySQL/MariaDB recommended)

## Setup
1. Install dependencies: `composer install`
2. Copy `.env.example` to `.env` if `.env` does not exist.
3. Configure database connection values in `.env`.
4. Generate an application key only if `APP_KEY` is empty: `php artisan key:generate`
5. Run additive migrations: `php artisan migrate`
6. Run automated tests: `php artisan test`
7. Start local server: `php artisan serve`

## Important data-safety note
Do not run `php artisan migrate:fresh`, `db:wipe`, or truncate tables on a database containing important data. Back up the database before applying migrations.

## Current limitations
- File attachments, advanced permissions, category administration, audit logs and reports are planned for later phases.
- Automated Laravel tests require Composer dependencies and a configured test database. PHP syntax checks have been run, but runtime/database tests have not yet been executed in this packaged environment.

## Phase 3: Categories and user administration

- Admin and Manager roles can manage categories.
- Admin role can create users, change roles, update profile details, and activate/deactivate accounts.
- Available roles: `admin`, `manager`, `staff`, `viewer`.
- User administration routes are protected server-side by the `role:admin` middleware; category routes use `role:admin,manager`.
- Users are deactivated rather than deleted so record ownership/history is retained.
- Categories referenced by records cannot be deleted; deactivate them instead.

Run the additive migrations with `php artisan migrate` after taking a database backup. Do not use `migrate:fresh` on an existing database.

### Initial administrator setup

For security, the application does not ship with a public default administrator password. After creating your own account through the normal registration process or using a trusted existing account, promote that account from the server terminal:

```bash
php artisan tinker
```

Then run (replace the email with the account you control):

```php
App\\Models\\User::where('email', 'you@example.com')->update(['role' => 'admin', 'is_active' => true]);
```

Exit Tinker with `exit`. Do not expose or share administrator credentials. The seeded `test@example.com` account is for local development only; do not use it in production.


## Phase 4 — Private Record Attachments

- Upload up to 10 files per request, maximum 10 MB each.
- Allowed formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG and PNG.
- Files are stored on Laravel's private `local` disk, not in the public web directory.
- Downloads are served through authenticated routes and checked against the parent record.
- Admin, Manager and Staff can upload/delete attachments; Viewer can download and view.
- Archived records are read-only for attachment changes.
- Additive migration: `2026_11_08_000003_create_record_attachments_table.php`.

Run after taking a database backup:

```bash
php artisan migrate
php artisan test
```

Do not use `migrate:fresh` on a database containing records. Configure PHP's `upload_max_filesize` and `post_max_size` to at least 10M and 110M respectively (or higher) if you want to upload multiple files in one request. For production, also configure web-server request limits, malware scanning, backups, and an appropriate retention policy.


## Phase 5 — Audit Logs

The administrator-only Activity Logs page is available at `/activity-logs`.
It records successful sign-in/sign-out, record create/update/archive/restore,
attachment upload/download/delete, category create/update/delete, and user
create/update/activate/deactivate actions. Each entry stores the actor (when
authenticated), action, description, related model, IP address, user agent,
and timestamp. Logs are intended as an audit trail and should not be edited
through normal application screens.

Run the additive migration with `php artisan migrate` after taking a database
backup. Do not use `migrate:fresh` on a database containing records.

## Phase 6 — Reports & Analytics

- Admins and managers can open `/reports` to view filtered record summaries, document-type and category breakdowns, monthly activity, and matching records.
- Reports can be filtered by document date range, document type, status, category, and department.
- The CSV export is Excel-compatible and includes a UTF-8 BOM for Unicode text. Spreadsheet formula-leading values are neutralized on export.
- Use **Print / Save PDF** on the reports page and choose **Save as PDF** in the browser print dialog to create a PDF without adding a server-side PDF dependency.
- Report routes are restricted to the `admin` and `manager` roles.

### Phase 6 validation

Feature tests were added for manager report access, viewer authorization, and CSV export. Run `php artisan test` after installing Composer dependencies and configuring the database. The project package in this phase has been PHP syntax-checked; live database execution is not claimed unless tests are run in an installed Laravel environment.

## Phase 7 — Notifications & Reminders

- Every signed-in user has a private notification centre at `/notifications`, with unread counts, pagination, mark-as-read, and mark-all-as-read actions.
- Creating a record sends an in-app database notification to the creator and active Admin/Manager users.
- Records may optionally have a due date. Active records due today or within the next three days generate reminders for the record creator and active Admin/Manager users.
- The `records:send-due-reminders` command is safe to run repeatedly on the same day; it avoids sending a duplicate reminder for the same record and recipient on that day.
- The scheduler is configured to run the reminder command daily at 08:00 using the application timezone.

After backing up the database, run `php artisan migrate`. For local development, run `php artisan schedule:work` in a second terminal. On a production Linux server, configure Laravel's scheduler cron entry:

```cron
* * * * * cd /path-to-recordroom && php artisan schedule:run >> /dev/null 2>&1
```

You can manually test the reminder command with `php artisan records:send-due-reminders`. Configure `APP_TIMEZONE`/`config/app.php` to the organisation's timezone before relying on the 08:00 reminder schedule. Notifications are stored in the database; configure database backups and retention as part of operations.

### Phase 7 validation

PHP syntax checks and ZIP integrity checks should be run after changes. Run `php artisan test` and the reminder command in a configured Laravel environment; this source package does not claim a live database test when Composer dependencies and a configured database are absent.


## Phase 8 — Final UI/UX, Search & Security Hardening

- Added authenticated global search across record reference, title, description, department and category. Search terms are validated (2–100 characters), results are paginated, and output is escaped by Blade.
- Added responsive global search to the application header, visible keyboard focus states, reduced-motion support, and mobile layout refinements.
- Added baseline security response headers: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, and `Cross-Origin-Opener-Policy`. HSTS is sent only on HTTPS requests.
- Notification click-through now accepts only a safe application-local path, rather than redirecting to an arbitrary stored external URL.
- Added indexes for common record status, category/status, and creator/date access patterns.
- Fixed the Record model mass-assignment allowlist so the Phase 7 `due_at` field can be persisted from validated record forms.
- Added feature tests for global search, input validation, and security response headers.

### Phase 8 validation and deployment checklist

PHP syntax validation has been run across the application source. Automated Laravel tests and live database/browser testing still need to be run in an environment with Composer dependencies installed and a configured test database:

```bash
composer install
php artisan test
php artisan route:list
php artisan migrate --pretend
```

Before applying migrations to an existing installation, take and verify a database backup, then run `php artisan migrate`. Do not use `migrate:fresh`, `db:wipe`, or truncate production tables. For deployment, use HTTPS, set `APP_ENV=production` and `APP_DEBUG=false`, run `php artisan config:cache` and `php artisan route:cache`, configure the scheduler and queue strategy, and ensure `storage` and `bootstrap/cache` are writable by the web process. Review the hosting provider's PHP and web-server upload limits, private file storage permissions, backup retention, malware scanning, and log retention. Security headers are baseline protections and do not replace a full infrastructure/security review.
