# RADAR KOGNITIF

RADAR KOGNITIF is a prototype dashboard for open-source intelligence (OSINT) monitoring, focused on cyber-threat reporting in Indonesia. It imports public RSS feeds, assigns keyword-based risk labels, maps locations explicitly named in report titles, and supports PDF export and optional Telegram alerts.

## What it does

- Aggregates articles from configured RSS feeds.
- Labels reports as Aman, Kuning, or Merah using a configurable keyword dictionary.
- Displays reports with a recognized location and recent threat trends.
- Supports PDF report export and optional Telegram alerts.

## Data and prototype scope

Reports are fetched from configured public RSS feeds and classified by matching their titles against a keyword dictionary. The application is not connected to social-media platforms or an AI sentiment, bot, or hoax classifier, so it does not display social-media metrics. A report is mapped only when its title names a recognized location; otherwise, its coordinates are left empty. Risk labels are heuristic and require human verification.

## Run locally

Requirements: PHP and Composer, Node.js and npm, and the PHP SQLite extension.

1. Install PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env`, then run `php artisan key:generate`.
3. Run database migrations and seed the shared tester account with `php artisan migrate --seed`.
4. Install frontend dependencies with `npm install`, then build assets with `npm run build`.
5. Start the app with `php artisan serve`.

The application uses SQLite by default. Configure Telegram credentials in the local `.env` only if you want to test Telegram alerts. Never publish `.env` or real credentials.

## Demo access

On a freshly seeded or deployed database, use this shared tester account:

- Email: `tester@radarkognitif.test`
- Password: `RadarKognitif-Tester-2026`

These credentials are intentionally public and only for the competition demo. The shared account cannot change its email, password, or profile. Do not reuse the password or store sensitive information in this account.

## Render demo deployment

The repository includes a Render Blueprint and Docker configuration. The container runs migrations and seeds the shared tester account on startup. The demo uses SQLite on the service filesystem, so changes may reset when Render replaces the instance. Do not add real credentials to the Blueprint; configure any optional secrets in the Render dashboard.

## Technology

- Laravel 13 and PHP
- SQLite
- Blade, Vite, Tailwind CSS, and Alpine.js
- Dompdf for PDF reports
