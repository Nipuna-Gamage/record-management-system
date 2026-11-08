# RecordRoom

RecordRoom is a secure, modern document and records management system built with **Laravel 11**, **Blade**, and **Tailwind CSS**. It provides complete lifecycle record tracking, role-based access control, private file attachments, an immutable activity audit trail, advanced reports with CSV exports, global search, and automated due-date reminders.

---

## Table of Contents

- [Key Features](#key-features)
- [Role-Based Access Control (RBAC)](#role-based-access-control-rbac)
- [System Requirements](#system-requirements)
- [Installation & Setup](#installation--setup)
- [Initial Administrator Account](#initial-administrator-account)
- [Task Scheduling & Due Date Reminders](#task-scheduling--due-date-reminders)
- [File Attachment Storage & Upload Limits](#file-attachment-storage--upload-limits)
- [Security Features](#security-features)
- [Automated Testing](#automated-testing)
- [Production Deployment Checklist](#production-deployment-checklist)
- [Directory Structure](#directory-structure)
- [License](#license)

---

## Key Features

### 📁 Record Lifecycle Management
- **Automatic Reference Generation**: Formats unique reference numbers automatically (`RR-YYYYMMDD-XXXXX`).
- **Document Categorization**: Classifies documents by type (`Incoming`, `Outgoing`, `Internal`, `Other`), category, and department.
- **Date Tracking**: Tracks document issue dates and optional compliance due dates (`due_at`).
- **Status Lifecycle**: Supports active records and soft archiving/restoration workflows with full audit logging.
- **Interactive Dashboard**: Displays real-time counts for active and archived records alongside recent additions.

### 📎 Secure Private Attachments
- **Multi-File Uploads**: Upload up to 10 files per request (up to 10 MB per file).
- **Supported File Formats**: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, and PNG.
- **Private Storage**: Files are stored on Laravel's private `local` disk (`storage/app/record-attachments/`) outside the public web root.
- **Protected Downloads**: Files are streamed through authenticated routes with ownership and access validation.
- **Archive Protection**: Archived records lock attachment upload and deletion operations to prevent accidental tampering.

### 🔍 Global & Granular Search
- **Header Global Search**: Quick cross-field search across reference numbers, titles, descriptions, departments, and category names.
- **Input Validation**: Safe query length validation (2–100 characters) and Blade-escaped rendering to prevent XSS.
- **Granular Filters**: Filter record listings by document type, active/archived status, department, and category.

### 📊 Reports & Analytics
- **Summary Metrics**: View total, active, and archived volume breakdowns.
- **Visual Distributions**: Review document-type distribution, category distribution, and a 6-month historical activity timeline.
- **Excel-Compatible CSV Export**: Streams CSV exports with a UTF-8 BOM (ensuring proper rendering of Unicode characters like Sinhala) and sanitizes formula-triggering characters (`=`, `+`, `-`, `@`) to prevent CSV injection.
- **Print / PDF Ready**: Styled print layout enabling instantaneous browser-native "Save as PDF" reports without heavy third-party PDF dependencies.

### 🛡️ Activity Logs & Audit Trail
- **Comprehensive Audit Trail**: Automatically logs key events:
  - **Authentication**: Successful logins, failed login attempts, and logouts.
  - **Records**: Creation, updates, archiving, and restoration.
  - **Attachments**: Uploads, authenticated downloads, and deletions.
  - **Categories**: Creation, updates, and deletions.
  - **User Administration**: Creation, profile updates, and activation/deactivation.
- **Audit Detail**: Records actor ID/name, action type, description, affected model ID, client IP address, user agent, and timestamp.
- **Tamper-Resistant**: Logs are restricted to system administrators and cannot be altered or deleted from the user interface.

### 🔔 Notifications & Automated Reminders
- **In-App Notification Center**: Accessible at `/notifications` with unread badges, pagination, single mark-as-read, and mark-all-as-read capabilities.
- **Creation Notifications**: Notifies the creator and active Administrators/Managers whenever a new record is added.
- **Due Date Alerts**: Automatically scans active records and notifies assignees and managers when a record is due today or within the next 3 days.
- **Safe Navigation**: Notification links validate target destinations against an internal route allowlist to guard against open redirect exploits.

---

## Role-Based Access Control (RBAC)

RecordRoom implements strict server-side authorization via the `EnsureUserHasRole` middleware. Four roles are supported:

| Feature / Action | Admin | Manager | Staff | Viewer |
| :--- | :---: | :---: | :---: | :---: |
| **View Dashboard & Records** | ✅ | ✅ | ✅ | ✅ |
| **Download Attachments** | ✅ | ✅ | ✅ | ✅ |
| **Create & Edit Records** | ✅ | ✅ | ✅ | ❌ |
| **Upload & Delete Attachments** | ✅ | ✅ | ✅ | ❌ |
| **Archive & Restore Records** | ✅ | ✅ | ❌ | ❌ |
| **View Reports & Export CSV** | ✅ | ✅ | ❌ | ❌ |
| **Manage Categories** | ✅ | ✅ | ❌ | ❌ |
| **Manage Users & Role Assignment** | ✅ | ❌ | ❌ | ❌ |
| **View Audit Trail (`/activity-logs`)** | ✅ | ❌ | ❌ | ❌ |

> [!NOTE]
> - Users are **deactivated** rather than deleted to ensure complete audit integrity and retain historical record ownership.
> - An administrator cannot deactivate their own account or remove their own administrative privileges. At least one active administrator must remain in the system.
> - Categories that are currently assigned to records cannot be deleted; they must be deactivated or reassigned.

---

## System Requirements

- **PHP**: `^8.2`
  - Required Extensions: `pdo`, `pdo_mysql` (or `pdo_sqlite`), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- **Package Managers**: Composer 2+ and Node.js (v18+ recommended) with npm
- **Database**: MySQL 8.0+ / MariaDB 10.4+ (recommended for production) or SQLite 3.35+ (development and testing)
- **Web Server**: Nginx, Apache, or Caddy with URL rewriting enabled

---

## Installation & Setup

Follow these steps to set up the project locally:

### 1. Clone & Enter Repository
```bash
git clone <repository-url>
cd record-management-system
```

### 2. Install Dependencies
Install PHP dependencies via Composer:
```bash
composer install
```

Install frontend dependencies and compile assets:
```bash
npm install
npm run build
```

### 3. Configure Environment
Copy the environment template:
```bash
cp .env.example .env
```

Generate the unique application key:
```bash
php artisan key:generate
```

Configure your database connection in `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recordroom
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

*(Alternatively, for SQLite development, set `DB_CONNECTION=sqlite` and create `database/database.sqlite`.)*

### 4. Run Migrations
Run additive database migrations:
```bash
php artisan migrate
```

> [!CAUTION]
> **Data Safety Notice**: Never run `php artisan migrate:fresh`, `db:wipe`, or truncate tables on an existing environment containing live data. Always back up your database before running migrations.

### 5. Start the Application
Start the local development server:
```bash
php artisan serve
```
For active frontend development, you can run the Vite dev server concurrently:
```bash
npm run dev
```

Visit the application at `http://127.0.0.1:8000`.

---

## Initial Administrator Account

Because RecordRoom restricts user creation to administrators and does not expose a public registration form, you should provision your initial administrator via Laravel Tinker:

1. Open Tinker in your terminal:
   ```bash
   php artisan tinker
   ```

2. Create a new Administrator user:
   ```php
   App\Models\User::create([
       'name' => 'System Administrator',
       'email' => 'admin@example.com',
       'password' => bcrypt('SecurePasswordHere123!'),
       'role' => 'admin',
       'is_active' => true,
   ]);
   ```

   *(Or promote an existing user by email):*
   ```php
   App\Models\User::where('email', 'admin@example.com')->update(['role' => 'admin', 'is_active' => true]);
   ```

3. Type `exit` to exit Tinker. You can now log in at `/login`.

---

## Task Scheduling & Due Date Reminders

RecordRoom automatically schedules document due date reminders. The command `records:send-due-reminders` evaluates active records due within 3 days and dispatches in-app notifications to assignees and administrators. The command is idempotent and safe to run multiple times per day.

### Local Development
Run the artisan scheduler worker:
```bash
php artisan schedule:work
```

You can also execute the reminder command manually for testing:
```bash
php artisan records:send-due-reminders
```

### Production Crontab Setup
Add a single cron entry to your server (typically via `crontab -e` for the `www-data` or deployment user):
```cron
* * * * * cd /path-to-recordroom && php artisan schedule:run >> /dev/null 2>&1
```

> [!TIP]
> Verify that `APP_TIMEZONE` in your `.env` or `config/app.php` matches your organization's timezone to ensure the scheduled 08:00 daily run fires at the desired local time.

---

## File Attachment Storage & Upload Limits

### Storage Architecture
- Record files are stored securely in `storage/app/record-attachments/{record_id}/`.
- They are not placed in `public/` and are never accessible via static web server URLs.
- Downloads are served through `RecordAttachmentController@download`, ensuring authentication and authorization before file streaming.

### Recommended PHP Configuration
To accommodate batch uploads of multiple 10 MB files in a single request, update your `php.ini` settings:
```ini
upload_max_filesize = 10M
post_max_size = 110M
memory_limit = 256M
max_execution_time = 60
```
For web servers such as Nginx, also ensure `client_max_body_size` is configured accordingly:
```nginx
client_max_body_size 110M;
```

---

## Security Features

- **HTTP Security Headers**: Injected via global `SecurityHeaders` middleware:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: camera=(), microphone=(), geolocation=()`
  - `Cross-Origin-Opener-Policy: same-origin`
  - `Strict-Transport-Security: max-age=31536000; includeSubDomains` *(enforced on HTTPS requests)*
- **Rate Limiting**: Brute-force protection applied on the login route (`throttle:6,1`).
- **SQL & Mass Assignment Protection**: Protected through Eloquent ORM parameter bindings and explicit `$fillable` model attributes.
- **XSS & CSV Injection Protection**: Blade automatic escaping across views; formula neutralizing prefix applied during CSV report exports.
- **Safe Redirects**: Local destination validation on notification click-throughs to eliminate open-redirect vulnerabilities.

---

## Automated Testing

RecordRoom includes a comprehensive test suite covering authentication, authorization, CRUD operations, attachments, reports, search, and security headers:

```bash
php artisan test
```

### Key Test Suites:
- `tests/Feature/AuthenticationTest.php`: Login rate limiting, session invalidation, inactive account prevention.
- `tests/Feature/RecordManagementTest.php`: Reference generation, record creation, updates, soft archiving, and restoration.
- `tests/Feature/RecordAttachmentTest.php`: File upload validation, MIME type enforcement, download security, archive immutability.
- `tests/Feature/ReportsTest.php`: Filtering logic, role authorization, CSV formatting, and formula neutralization.
- `tests/Feature/GlobalSearchAndSecurityTest.php`: Search query bounds, multi-field matching, and HTTP security response headers.

---

## Production Deployment Checklist

1. **Environment Configuration**:
   ```ini
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://records.yourdomain.com
   ```
2. **Optimize Caches**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. **Storage & Directory Permissions**:
   Ensure `storage/` and `bootstrap/cache/` are writable by the web server user:
   ```bash
   chown -R www-data:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```
4. **Database Migration**:
   Back up your database, then run:
   ```bash
   php artisan migrate --force
   ```
5. **Configure Cron & Queue Workers**:
   Ensure the Laravel scheduler cron entry is registered and active.
6. **HTTPS & TLS**:
   Serve exclusively over HTTPS with an active SSL/TLS certificate to enable HSTS headers and secure session cookies.

---

## Directory Structure

```plaintext
app/
├── Console/Commands/        # Custom Artisan commands (SendRecordDueReminders)
├── Http/
│   ├── Controllers/         # Application controllers (Records, Attachments, Reports, Users, etc.)
│   └── Middleware/          # Role enforcement (EnsureUserHasRole) and SecurityHeaders
├── Models/                  # Eloquent models (Record, RecordAttachment, Category, Log, User)
├── Notifications/           # Notification classes (RecordCreated, RecordDueReminder)
└── Support/                 # ActivityLogger audit service
database/
├── migrations/              # Database schema migrations
└── seeders/                 # Database seeders
resources/
└── views/                   # Blade templates (dashboard, records, reports, logs, users, search)
routes/
├── console.php              # Console route definitions & task schedule
└── web.php                  # Web application routes & role middleware definitions
storage/app/                 # Private disk storage for record attachments
tests/                       # Feature and unit test suites
```

---

## License

This application is open-sourced software licensed under the [MIT license](LICENSE).
