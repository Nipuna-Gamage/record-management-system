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
