# Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=ultraclarity-config
```

The file is published to:

```text
config/ultraclarity.php
```

## Core Settings

```env
ULTRACLARITY_ENABLED=true
ULTRACLARITY_ROUTE_PREFIX=ultraclarity
ULTRACLARITY_SITE_ID=default
```

`ULTRACLARITY_ROUTE_PREFIX` controls the dashboard URL. The default dashboard URLs are:

```text
/ultraclarity
/ultraclarity/dashboard
```

## Authentication

```env
ULTRACLARITY_DASHBOARD_AUTH=true
PRISMPATH_DASHBOARD_EMAIL=admin@prismpath.test
PRISMPATH_DASHBOARD_PASSWORD=password
ULTRACLARITY_DASHBOARD_PASSWORD=password
```

For a production app, replace the package middleware with your own admin middleware:

```php
'middleware' => ['web', 'auth', 'can:view-analytics'],
```

## Feature Flags

```env
ULTRACLARITY_SESSIONS=true
ULTRACLARITY_HEATMAPS=true
ULTRACLARITY_CLICKS=true
ULTRACLARITY_AI_INSIGHTS=true
ULTRACLARITY_ECHO=false
```

Use these flags to disable expensive or privacy-sensitive modules.

## Retention

```env
ULTRACLARITY_RAW_RETENTION_DAYS=90
ULTRACLARITY_RECORDING_RETENTION_DAYS=30
ULTRACLARITY_AGGREGATE_RETENTION_DAYS=365
```

Run cleanup manually:

```bash
php artisan ultraclarity:cleanup
```

Or schedule it:

```php
$schedule->command('ultraclarity:cleanup')->daily();
```

## Storage

```env
ULTRACLARITY_STORAGE_DRIVER=database
ULTRACLARITY_STORAGE_DISK=local
ULTRACLARITY_REDIS_CONNECTION=default
```

Supported storage modes in the package configuration:

- `database`: store analytics and compressed recordings in database tables.
- `redis`: optional high-throughput cache/queue support.
- `s3`: optional disk for large recording exports when configured in Laravel filesystems.

## Snippet Options

```php
'snippet' => [
    'async' => true,
    'defer' => true,
    'gdpr' => true,
    'sample_rate' => 1.0,
    'mask_inputs' => true,
    'endpoint' => '/api/ultraclarity/collect',
],
```

Recommended production defaults:

- Keep `async` and `defer` enabled.
- Keep `mask_inputs` enabled.
- Use `sample_rate` below `1.0` for very high traffic sites.
- Use consent tooling before enabling tracking in regions that require it.

## Funnels

Default funnel:

```php
'funnels' => [
    'default' => ['/', 'signup_started', 'plan_selected', '/checkout'],
],
```

Steps can be page paths or custom event names.

## Reports

```env
ULTRACLARITY_REPORT_RECIPIENTS=founder@example.com,growth@example.com
```

Configured schedules:

```php
'reports' => [
    'schedules' => [
        ['name' => 'Executive daily', 'frequency' => 'daily', 'format' => 'pdf'],
        ['name' => 'Growth weekly', 'frequency' => 'weekly', 'format' => 'csv'],
        ['name' => 'Product monthly', 'frequency' => 'monthly', 'format' => 'json'],
    ],
],
```

Generate:

```bash
php artisan ultraclarity:report weekly --email
```

