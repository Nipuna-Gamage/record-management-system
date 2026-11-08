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
