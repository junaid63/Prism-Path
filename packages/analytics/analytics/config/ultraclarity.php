<?php

return [
    'enabled' => env('PRISMPATH_ENABLED', env('ULTRACLARITY_ENABLED', true)),
    'route_prefix' => env('PRISMPATH_ROUTE_PREFIX', env('ULTRACLARITY_ROUTE_PREFIX', 'ultraclarity')),
    'middleware' => ['web', \UltraClarity\Analytics\Http\Middleware\DashboardBasicAuth::class],
    'api_middleware' => ['api', 'throttle:120,1'],
    'dashboard_auth' => [
        'enabled' => env('PRISMPATH_DASHBOARD_AUTH', env('ULTRACLARITY_DASHBOARD_AUTH', true)),
        'email' => env('PRISMPATH_DASHBOARD_EMAIL', env('ULTRACLARITY_DASHBOARD_EMAIL', 'admin@prismpath.test')),
        'password' => env('PRISMPATH_DASHBOARD_PASSWORD', env('ULTRACLARITY_DASHBOARD_PASSWORD', 'password')),
    ],
    'site_id' => env('PRISMPATH_SITE_ID', env('ULTRACLARITY_SITE_ID', 'default')),
    'features' => [
        'sessions' => env('PRISMPATH_SESSIONS', env('ULTRACLARITY_SESSIONS', true)),
        'heatmaps' => env('PRISMPATH_HEATMAPS', env('ULTRACLARITY_HEATMAPS', true)),
        'clicks' => env('PRISMPATH_CLICKS', env('ULTRACLARITY_CLICKS', true)),
        'ai_insights' => env('PRISMPATH_AI_INSIGHTS', env('ULTRACLARITY_AI_INSIGHTS', true)),
        'echo' => env('PRISMPATH_ECHO', env('ULTRACLARITY_ECHO', false)),
    ],
    'privacy' => [
        'store_ip' => env('PRISMPATH_STORE_IP', env('ULTRACLARITY_STORE_IP', true)),
    ],
    'retention' => [
        'raw_events_days' => env('PRISMPATH_RAW_RETENTION_DAYS', env('ULTRACLARITY_RAW_RETENTION_DAYS', 90)),
        'recordings_days' => env('PRISMPATH_RECORDING_RETENTION_DAYS', env('ULTRACLARITY_RECORDING_RETENTION_DAYS', 30)),
        'aggregates_days' => env('PRISMPATH_AGGREGATE_RETENTION_DAYS', env('ULTRACLARITY_AGGREGATE_RETENTION_DAYS', 365)),
    ],
    'storage' => [
        'driver' => env('PRISMPATH_STORAGE_DRIVER', env('ULTRACLARITY_STORAGE_DRIVER', 'database')),
        'disk' => env('PRISMPATH_STORAGE_DISK', env('ULTRACLARITY_STORAGE_DISK', 'local')),
        'redis_connection' => env('PRISMPATH_REDIS_CONNECTION', env('ULTRACLARITY_REDIS_CONNECTION', 'default')),
    ],
    'snippet' => [
        'async' => true,
        'defer' => true,
        'gdpr' => true,
        'sample_rate' => (float) env('PRISMPATH_SAMPLE_RATE', env('ULTRACLARITY_SAMPLE_RATE', 1.0)),
        'mask_inputs' => true,
        'endpoint' => '/api/ultraclarity/collect',
    ],
    'cache_ttl' => env('PRISMPATH_CACHE_TTL', env('ULTRACLARITY_CACHE_TTL', 60)),
    'funnels' => [
        'default' => ['/', 'signup_started', 'plan_selected', '/checkout'],
    ],
    'reports' => [
        'recipients' => array_filter(explode(',', env('PRISMPATH_REPORT_RECIPIENTS', env('ULTRACLARITY_REPORT_RECIPIENTS', '')))),
        'schedules' => [
            ['name' => 'Executive daily', 'frequency' => 'daily', 'format' => 'pdf'],
            ['name' => 'Growth weekly', 'frequency' => 'weekly', 'format' => 'csv'],
            ['name' => 'Product monthly', 'frequency' => 'monthly', 'format' => 'json'],
        ],
    ],
];

