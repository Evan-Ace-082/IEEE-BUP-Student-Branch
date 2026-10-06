# BUP IEEE Student Branch

Database-driven website and management platform for the BUP IEEE Student Branch at Bangladesh University of Professionals.

The public site covers the branch, executive committee, events, announcements, achievements, member directory, resources, gallery, membership guidance, and contact. Members, admins, and a super admin each have a dashboard. Permissions are enforced on the server.

Tagline: Empowering Students. Inspiring Innovation. Connecting Futures.

## Stack

- PHP 8.3 and Laravel
- MySQL or MariaDB
- Blade, Bootstrap 5, and a small custom stylesheet
- Laravel mail, notifications, and queues

XAMPP (Apache, MySQL, phpMyAdmin) is a suitable local stack on Windows. This repository also runs with PHP’s built-in server.

## Features

- Public pages driven by the database, with editable copy in the admin panel
- Registration, login, logout, email verification, and password reset
- New accounts stay pending until an admin approves them
- Roles: Member, Admin, Super Admin
- Member profile, event registration, and achievement submission
- Admin CRUD for events, announcements, achievements, gallery, resources, members, committees, messages, and reports
- Super Admin management of administrator accounts and system settings
- Activity log, in-app notifications, and email notifications
- Upload checks for images and documents
- Rate limits that keep campus-wide browsing and event registration usable while slowing credential stuffing and form spam

## Requirements

- PHP 8.3+ with extensions: `mbstring`, `xml`, `curl`, `zip`, `pdo_mysql`, `gd`, `intl`, `tokenizer`
- Composer
- MySQL 8 or MariaDB 10.6+
- A mail transport for production (SMTP). Local development can use the `log` mailer.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create a database and set `DB_*` in `.env`.

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Start the site:

```bash
php artisan serve --host=127.0.0.1 --port=8420
```

For mail and in-app fan-out (announcements to many members), run a queue worker in another terminal:

```bash
php artisan queue:work
```

With `MAIL_MAILER=log`, messages are written to `storage/logs/laravel.log`, including verification and password-reset links.

## XAMPP

1. Start Apache and MySQL.
2. Create a database named `ieee_bup` in phpMyAdmin.
3. Point the site’s document root at the `public` directory, or visit `http://localhost/ieee-bup/public` if the project lives in `htdocs`.
4. Set `DB_USERNAME` and `DB_PASSWORD` to the MySQL account you use (often `root` with an empty password on a fresh XAMPP install).
5. Set `APP_URL` to the URL you actually open.

## Development accounts

These accounts are created only when you run `php artisan db:seed` outside production. Change them before any public launch. Do not seed them on a server that strangers can reach. Production seeding skips demo accounts unless `SEED_ALLOW=true`.

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | superadmin@bupieee.test | ChangeMe!Super2026 |
| Admin | admin@bupieee.test | ChangeMe!Admin2026 |
| Member | member@bupieee.test | ChangeMe!Member2026 |
| Pending member | pending@bupieee.test | ChangeMe!Pending2026 |

A second sample member is `rafiul@bupieee.test` with the same member password.

## Roles

- **Member:** own profile, own event registrations, achievement submissions, member-only resources. No admin tools.
- **Admin:** operational content and member approval. An admin cannot create, edit, suspend, or delete administrators, and cannot become Super Admin.
- **Super Admin:** everything an admin can do, plus administrator accounts and system settings (name, tagline, logo, join link).

There is one seeded Super Admin. The admin form cannot create another.

## Security and a large audience

The app is built so a normal campus crowd can read pages and register for events without the database doing unnecessary work:

- Homepage data is cached for 60 seconds and cleared when editors save.
- Lists are paginated. Queries use indexes and eager loading.
- Uploaded images are checked with the real file contents, resized, and stored under generated names. Documents stay outside the public folder and are downloaded through an authorized route.
- Passwords are hashed. Forms use CSRF protection. Output is escaped. SQL uses parameter binding.
- Login lockout is per account, so one shared campus network is not locked out together. Event registration is limited per email, with a high per-network ceiling so a real rush can still get through.
- Public forms include a small arithmetic check and a hidden field that blocks simple bots.
- Suspended administrators are signed out, including database sessions.
- Set `APP_DEBUG=false` and `APP_ENV=production` on a public server. Put the site behind PHP-FPM with OPcache, and a reverse proxy such as Cloudflare or nginx, if you expect thousands of visitors or need protection from volumetric floods. Application code cannot absorb a network-level flood by itself.
- Set `TRUSTED_PROXIES` only when a proxy you control sits in front of PHP, so rate limits see the visitor address.

Official IEEE fees, grades, and eligibility should be edited in Website content or taken from IEEE’s own join page. The seeded text is starter copy for the committee to replace.

## Tests

```bash
php artisan test
```

## Troubleshooting

- **Images do not appear:** run `php artisan storage:link`.
- **Verification email never arrives:** with the log mailer, open `storage/logs/laravel.log`. For real delivery, set `MAIL_*` to your SMTP server.
- **Queue notifications sit unsent:** run `php artisan queue:work`.
- **Admin sees 403 on admin management:** that area is Super Admin only.
- **New member cannot open the dashboard:** verify the email, then approve the account under Admin → Members.
- **`SQLSTATE` connection errors:** check that MySQL is running and `.env` matches the database name, user, and password.
