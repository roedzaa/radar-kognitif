# RADAR KOGNITIF

RADAR KOGNITIF is a prototype dashboard for open-source intelligence (OSINT) monitoring, focused on cyber threats and digital narratives in Indonesia. It brings public news feeds, risk labels, regional mapping, cognitive metrics, and alert notifications into one interface.

## What it does

- Aggregates articles from configured RSS feeds.
- Labels reports as Aman, Kuning, or Merah using a configurable keyword dictionary.
- Displays report locations and recent threat trends on a dashboard.
- Provides cognitive metrics, sentiment summaries, and trending-issue visualizations.
- Supports PDF report export and optional Telegram alerts.

## Data and prototype scope

RSS articles are fetched from their configured public feeds. Social-media signals and cognitive metrics shown by the current prototype are synthetic/demo data; they are not live measurements from social platforms and should not be treated as verified intelligence.

## Run locally

Requirements: PHP and Composer, Node.js and npm, and the PHP SQLite extension.

1. Install PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env`, then run `php artisan key:generate`.
3. Run database migrations and demo seeders with `php artisan migrate --seed`.
4. Install frontend dependencies with `npm install`, then build assets with `npm run build`.
5. Start the app with `php artisan serve`.

The application uses SQLite by default. Configure Telegram credentials in the local `.env` only if you want to test Telegram alerts. Never publish `.env` or real credentials.

## Demo access

On a fresh seeded database, use this shared demo account:

- Email: `admin@radarkognitif.mil.id`
- Password: `password123`

This account is only for the competition prototype. The dashboard's social signals and cognitive metrics are demo data. Do not reuse this password for any other account.

## Render demo deployment

The repository includes a Render Blueprint and Docker configuration. The demo uses SQLite on the service filesystem, so changes may reset when Render replaces the instance. Do not add real credentials to the Blueprint; configure any optional secrets in the Render dashboard.

## Technology

- Laravel 13 and PHP
- SQLite
- Blade, Vite, Tailwind CSS, and Alpine.js
- Dompdf for PDF reports
