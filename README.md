# RecordRoom – Record Management System

A Laravel 11 application for managing organizational records and documents.

## Current development status

### Phase 1 – Foundation
- Login and logout
- Session regeneration on successful login
- Login rate limiting
- Active/inactive user account support
- Role field and role middleware (`super_admin`, `admin`, `record_keeper`, `viewer`)
- Responsive application shell and dashboard foundation

### Planned phases
1. Record registration, search, filtering and archive
2. Category and department management
3. Private document attachments and secure downloads
4. Dashboard analytics, reports and audit trail
5. Automated testing, accessibility and security review

## Requirements
- PHP 8.2+
- Composer
- MySQL 8+
- Node.js and npm (for future frontend asset compilation)

## Local setup

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Configure the MySQL connection in `.env`, then run:

```bash
php artisan migrate
php artisan serve
```

Open `http://127.0.0.1:8000/login`.

Create the first administrator using a controlled, secure account-creation process. Public registration is intentionally not enabled.

## Roles
- `super_admin`: full system administration
- `admin`: operational administration
- `record_keeper`: create and maintain records
- `viewer`: read-only access

Role middleware is registered as `role`. Example: `->middleware(['auth', 'role:super_admin,admin'])`.

## Safety
- Do not run `php artisan migrate:fresh` or `php artisan db:wipe` against a database containing real data.
- Keep `.env` and credentials out of Git.
- Uploaded documents will be private by design in the document-management phase.
- Do not enable public registration without an administrator approval workflow.

## Testing

```bash
php artisan test
```
