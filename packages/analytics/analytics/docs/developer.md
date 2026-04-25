# Developer Notes

PrismPath is structured as a normal Laravel package. It uses PSR-4 autoloading under `UltraClarity\Analytics`.

## File Structure

```text
src/
  Commands/
  Events/
  Facades/
  Http/Controllers/
  Http/Middleware/
  Jobs/
  Models/
  Repositories/
  Services/
config/
database/migrations/
database/seeders/
resources/js/
resources/views/
routes/
```

## Main Models

- `Visitor`: browser/device/location identity.
- `Session`: visit lifecycle, current page, source, replay data.
- `PageView`: per-page duration, path, title, referrer, scroll.
- `ClickEvent`: click coordinates and element selectors.
- `CustomEvent`: named business/product events.
- `BehaviorEvent`: scroll, mouse, typing, form, video, navigation data.
- `HeatmapData`: aggregated click, movement, and scroll hotspots.

## Query Layer

Dashboard queries live in:

```text
src/Repositories/AnalyticsRepository.php
```

Use the repository when adding dashboard panels so filters, caching, and section payloads stay consistent.

Common payload sections:

- `stats`
- `liveUsers`
- `topPages`
- `navigation`
- `heatmaps`
- `segments`
- `events`
- `elements`
- `funnels`
- `trend`
- `sessions`
- `reports`

## API Endpoints

Dashboard:

```text
GET /ultraclarity
GET /ultraclarity/dashboard
GET /ultraclarity/data
GET /ultraclarity/section/{section}
GET /ultraclarity/live
GET /ultraclarity/replay/{session}
GET /ultraclarity/export/{type}
GET /ultraclarity/report/{format}
```

Collector:

```text
POST /api/ultraclarity/collect
```

## Custom Events

Frontend:

```js
window.PrismPath.event('pricing_cta_clicked', {
    location: 'hero',
    plan: 'business'
});
```

Backend events are stored as `CustomEvent` records and appear in the Events & Conversions panel.

## Extending Metrics

1. Add or update model fields with a migration.
2. Update `AnalyticsIngestor` if data arrives from the tracker.
3. Add filtered queries to `AnalyticsRepository`.
4. Expose the metric in `dashboardPayload()` or `section()`.
5. Add a Vue panel/card/chart in `resources/views/dashboard.blade.php`.
6. Add demo rows in `UltraClarityDemoSeeder`.
7. Document the feature.

## Real-Time Updates

The package dispatches:

```php
UltraClarity\Analytics\Events\LiveSessionUpdated
```

It broadcasts on:

```text
ultraclarity.live
```

Event name:

```text
.session.updated
```

When Laravel Echo exists on `window.Echo`, the dashboard updates live sessions over websockets. Without Echo, it falls back to polling.

## Compression

Session replay payloads are compressed by:

```text
src/Services/RecordingCompressor.php
```

The current implementation stores compressed JSON using gzip plus base64 so it remains database-safe and portable.

## Coding Standards

- PHP code follows PSR-12 conventions.
- Keep controller methods thin.
- Put dashboard query composition in repositories.
- Put ingestion/transformation in services.
- Keep frontend panels fixed-height and scroll internal overflow.
- Keep tracking lightweight and batched.
- Do not store sensitive input values; record metadata only.

## Demo Data

Seeder:

```text
database/seeders/UltraClarityDemoSeeder.php
```

Command:

```bash
php artisan ultraclarity:seed --fresh
```

The seeder creates realistic live sessions, page paths, events, replay samples, and heatmap records for client demos.

