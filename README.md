# PrismPath Analytics

PrismPath is a Laravel 10 application plus an installable Composer package at `packages/analytics`. It provides session replay capture, click and scroll heatmaps, custom events, cached dashboard metrics, exports, retention cleanup, demo seed data, and AI-assisted hotspot analysis.

## Quick Start

```bash
composer install
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000`, interact with the page, then open `/prismpath` or `/analytics`.

Dashboard basic auth demo account:

```text
admin@prismpath.test
password
```

The app defaults to SQLite. To use MySQL, edit `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clarity
DB_USERNAME=root
DB_PASSWORD=
```

## Package Installation

The root project uses a Composer path repository:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/analytics",
      "options": { "symlink": true }
    }
  ],
  "require": {
    "aetherpulse/prismpath": "dev-main"
  }
}
```

For Packagist, publish `packages/ultraclarity/analytics` as `aetherpulse/prismpath`. The package is PSR-4 compliant and auto-discovers `UltraClarity\Analytics\UltraClarityServiceProvider` on Laravel 8+.

## Publishables

```bash
php artisan vendor:publish --tag=ultraclarity-config
php artisan vendor:publish --tag=ultraclarity-assets
php artisan migrate
```

## Blade Snippet

Add the tracker before `</body>`:

```blade
@prismpath
```

Or render manually:

```blade
{!! PrismPath::script() !!}
```

The browser snippet captures page views, clicks, throttled mouse movement, scroll depth, navigation duration, compact replay events, and custom events. GDPR opt-out is available:

```js
window.PrismPath.optOut();
window.PrismPath.optIn();
window.PrismPath.event('plan_selected', { plan: 'pro', value: 49 });
```

## Dashboard

Visit `/ultraclarity` to view:

- Real-time visitor counters and page-view totals
- AI-highlighted click, scroll, and mouse movement hotspots
- Chart.js line, bar, doughnut, and pie charts for visitors, pages, devices, browsers, and operating systems
- Fixed-size chart frames that stay stable during live updates
- Google Analytics-style sidebar navigation for Overview, Live Users, Sessions & Timelines, Page Views, Clicks & Scrolls, Heatmaps, Events & Conversions, and Exports & Reports
- Live user list with current page, duration, clicks, scroll depth, device, browser, OS, IP, city, and country
- Live activity labels for active, idle, typing, deep scrolling, and high-click sessions
- Per-user horizontal timelines with page duration bars, click counts, movement density, scroll depth, and timestamps
- Filtered analytics by date range, hour, page, device, browser, OS, country, city, and login state
- Conversion funnels and event frequency panels
- Session replay playback with pause, slow, fast, and skip-idle controls
- Recent replay sessions with device, duration, event, and scroll metadata
- Top pages by view count and scroll depth
- CSV export for `pageviews`, `clicks`, and `events`
- JSON export for the full dashboard payload
- Print-friendly report export via `/ultraclarity/report/pdf`
- CSV exports for `heatmaps`, `sessions`, `live`, `elements`, and `timelines`

Live updates work immediately through a 5-second polling fallback. If the host app configures Laravel Echo/Pusher and exposes `window.Echo`, PrismPath also broadcasts `session.updated` events on the `ultraclarity.live` channel.

Section APIs are available for lightweight integrations:

```text
/ultraclarity/section/live
/ultraclarity/section/sessions
/ultraclarity/section/pages
/ultraclarity/section/behavior
/ultraclarity/section/heatmaps
/ultraclarity/section/events
/ultraclarity/section/exports
```

Embed compact live stats anywhere in Blade:

```blade
@prismpathStats
```

## Configuration

`config/ultraclarity.php` controls:

- Feature flags for sessions, heatmaps, clicks, AI insights, and optional Echo updates
- Retention windows for raw events, recordings, and aggregates
- Database, Redis, or disk/S3-oriented storage settings
- JS snippet options including async/defer, sample rate, masking, and GDPR behavior
- Route prefix, dashboard middleware, and API middleware

## Artisan Commands

```bash
php artisan ultraclarity:seed --fresh
php artisan ultraclarity:heatmaps
php artisan ultraclarity:heatmaps --path=/pricing
php artisan ultraclarity:cleanup
php artisan ultraclarity:report daily --email
```

`ultraclarity:seed` creates demo visitors, sessions, page views, clicks, custom events, replay payloads, and heatmaps. `ultraclarity:cleanup` enforces configured retention periods.

## Architecture

Core namespace: `UltraClarity\Analytics`.

Important folders:

- `src/Models`: `Visitor`, `Session`, `PageView`, `ClickEvent`, `CustomEvent`, `HeatmapData`
- `src/Services`: ingestion, replay compression, script rendering, AI hotspot clustering
- `src/Repositories`: cached dashboard queries
- `src/Commands`: cleanup, demo seeding, heatmap regeneration
- `routes/web.php` and `routes/api.php`: dashboard, snippet, collection endpoints
- `resources/views`: Blade dashboard and components
- `resources/js`: lightweight tracker and Vue dashboard source
- `database/migrations`: installable package tables

Replay payloads are gzipped and base64 encoded before storage. Heatmap AI is implemented as deterministic local clustering so it works without external services; teams can extend `AiInsightService` to call a model provider or queue deeper analysis.
