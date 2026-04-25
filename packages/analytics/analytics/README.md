# PrismPath

`aetherpulse/prismpath` is an installable Laravel analytics package for live visitors, session replay, heatmaps, page analytics, click and scroll tracking, custom events, funnels, exports, scheduled reports, and AI-assisted engagement insights.

It is designed to feel familiar to teams who use Google Analytics, Microsoft Clarity, or Salesforce dashboards, while staying understandable to Laravel developers.

## Quick Start

Install the package:

```bash
composer require aetherpulse/prismpath
```

Publish package files and migrate:

```bash
php artisan vendor:publish --tag=ultraclarity-config
php artisan vendor:publish --tag=ultraclarity-migrations
php artisan migrate
php artisan ultraclarity:seed
```

Add the tracker to your main Blade layout before `</body>`:

```blade
@prismpath
```

Open the dashboard:

```text
/prismpath
/ultraclarity
/ultraclarity/dashboard
```

Default demo credentials:

```text
Email: admin@prismpath.test
Password: password
```

## Empty Folder Laravel 10 Setup

From an empty folder:

```bash
composer create-project laravel/laravel:^10.0 .
composer require aetherpulse/prismpath
cp .env.example .env
php artisan key:generate
```

SQLite example:

```bash
touch database/database.sqlite
```

Set this in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

Then run:

```bash
php artisan vendor:publish --tag=ultraclarity-config
php artisan vendor:publish --tag=ultraclarity-migrations
php artisan migrate
php artisan ultraclarity:seed
php artisan serve
```

## Dashboard Panels

PrismPath keeps every major feature within two or three clicks:

- **Overview**: live users, visitors, sessions, page views, bounce rate, scroll depth, movement samples, and heatmap intensity.
- **Live Users**: active sessions with current page, activity, device, OS, browser, city, country, clicks, scroll depth, and replay access.
- **Sessions & Timelines**: searchable, sortable, paginated session table with expandable page details and embedded replay controls.
- **Page Views**: top URLs, views, average duration, exit rate, scroll depth, and click volume.
- **Clicks & Scrolls**: element-level click analytics and page behavior charts.
- **Heatmaps**: click, movement, and scroll overlays with AI hotspot summaries.
- **Events & Conversions**: custom event counts and funnel conversion progress.
- **Exports & Reports**: JSON, CSV, timeline, heatmap, live user, session, and print/PDF report exports.

## Tracking Snippet

Use the Blade directive:

```blade
@prismpath
```

Or render manually:

```blade
{!! PrismPath::script() !!}
```

The snippet batches and tracks:

- Page views and navigation paths
- Clicks and element selectors
- Scroll depth
- Mouse movement density
- Form submissions
- Video plays
- Typing/input activity metadata
- Custom events
- GDPR opt-out state

Custom event example:

```html
<script>
window.PrismPath.event('signup_started', {
    plan: 'pro',
    source: 'pricing_page'
});
</script>
```

Privacy controls:

```html
<script>
window.PrismPath.optOut();
window.PrismPath.optIn();
</script>
```

## Configuration

Publish config:

```bash
php artisan vendor:publish --tag=ultraclarity-config
```

Important `.env` values:

```env
ULTRACLARITY_ENABLED=true
ULTRACLARITY_ROUTE_PREFIX=ultraclarity
ULTRACLARITY_DASHBOARD_AUTH=true
ULTRACLARITY_DASHBOARD_EMAIL=admin@prismpath.test
ULTRACLARITY_DASHBOARD_PASSWORD=password

ULTRACLARITY_SESSIONS=true
ULTRACLARITY_HEATMAPS=true
ULTRACLARITY_CLICKS=true
ULTRACLARITY_AI_INSIGHTS=true
ULTRACLARITY_ECHO=false

ULTRACLARITY_RAW_RETENTION_DAYS=90
ULTRACLARITY_RECORDING_RETENTION_DAYS=30
ULTRACLARITY_AGGREGATE_RETENTION_DAYS=365

ULTRACLARITY_STORAGE_DRIVER=database
ULTRACLARITY_STORAGE_DISK=local
ULTRACLARITY_REDIS_CONNECTION=default
ULTRACLARITY_SAMPLE_RATE=1.0
```

## Real-Time Updates

The dashboard polls live users every few seconds by default. If your app configures Laravel Echo and broadcasts the `ultraclarity.live` channel, PrismPath listens for `.session.updated` events and updates the live user panel immediately.

PrismPath accepts matching `PRISMPATH_*` environment keys, for example
`PRISMPATH_ENABLED=true` and `PRISMPATH_DASHBOARD_EMAIL=admin@example.com`.
The legacy `ULTRACLARITY_*` keys, `@ultraclarity` directive, and `UltraClarity`
facade remain as compatibility aliases for existing installations.

## Commands

```bash
php artisan ultraclarity:seed
php artisan ultraclarity:seed --fresh
php artisan ultraclarity:heatmaps
php artisan ultraclarity:cleanup
php artisan ultraclarity:report daily
php artisan ultraclarity:report weekly
php artisan ultraclarity:report monthly --email
```

## Documentation

- [Installation](docs/installation.md)
- [Usage Guide](docs/usage.md)
- [Configuration](docs/configuration.md)
- [Developer Notes](docs/developer.md)

## Package Structure

```text
config/                     Package configuration
database/migrations/        Analytics tables
database/seeders/           Demo analytics data
resources/js/               Tracker and dashboard assets
resources/views/            Dashboard, report, components
routes/web.php              Dashboard, exports, replay APIs
routes/api.php              Collection endpoint
src/Commands/               Cleanup, seed, reports, heatmaps
src/Events/                 Live dashboard events
src/Http/Controllers/       Collection and dashboard APIs
src/Models/                 Visitor, Session, PageView, events, heatmaps
src/Repositories/           Dashboard query layer
src/Services/               Ingestion, compression, AI insights
```

## Production Notes

- Keep dashboard authentication enabled or replace the middleware with your app's admin auth.
- Use queue workers for heavier analytics processing.
- Use Redis cache in high-traffic apps.
- Review privacy requirements for IP storage, input masking, consent, and retention.
- Set `ULTRACLARITY_SAMPLE_RATE` below `1.0` for very high traffic sites.

