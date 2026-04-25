<?php

namespace PrismPath\Analytics\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PrismPath\Analytics\Events\LiveSessionUpdated;
use PrismPath\Analytics\Jobs\ProcessAnalyticsJob;
use PrismPath\Analytics\Models\BehaviorEvent;
use PrismPath\Analytics\Models\ClickEvent;
use PrismPath\Analytics\Models\CustomEvent;
use PrismPath\Analytics\Models\PageView;
use PrismPath\Analytics\Models\Session;
use PrismPath\Analytics\Models\Visitor;

class AnalyticsIngestor
{
    public function __construct(private readonly RecordingCompressor $compressor)
    {
    }

    public function ingest(Request $request): Session
    {
        $data = $request->validate([
            'siteId' => ['nullable', 'string', 'max:80'],
            'visitorId' => ['required', 'string', 'max:80'],
            'sessionId' => ['required', 'string', 'max:80'],
            'page' => ['required', 'array'],
            'events' => ['array'],
            'recording' => ['array'],
            'device' => ['array'],
            'auth' => ['array'],
        ]);

        $visitor = Visitor::updateOrCreate(
            ['visitor_uuid' => $data['visitorId']],
            [
                'site_id' => $data['siteId'] ?? config('ultraclarity.site_id'),
                'ip_hash' => hash('sha256', (string) $request->ip()),
                'ip_address' => config('ultraclarity.privacy.store_ip') ? $request->ip() : null,
                'country' => $request->header('CF-IPCountry') ?: ($request->header('X-PrismPath-Country') ?: null),
                'city' => $request->header('X-PrismPath-City'),
                'device' => $data['device']['type'] ?? 'desktop',
                'browser' => $data['device']['browserName'] ?? $data['device']['browser'] ?? $request->userAgent(),
                'os' => $data['device']['os'] ?? null,
                'is_authenticated' => (bool) ($data['auth']['loggedIn'] ?? false),
                'user_identifier' => $data['auth']['id'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        $page = $data['page'];
        $path = parse_url($page['url'] ?? '/', PHP_URL_PATH) ?: '/';
        $session = Session::firstOrCreate(
            ['session_uuid' => $data['sessionId']],
            [
                'visitor_id' => $visitor->id,
                'landing_page' => $path,
                'started_at' => now(),
            ]
        );

        $existingRecording = $this->compressor->decompress($session->recording_payload);
        $recording = array_slice(array_merge($existingRecording, $data['recording'] ?? []), -1200);
        $clickCount = collect($data['events'] ?? [])->where('type', 'click')->count();
        $movementCount = collect($data['recording'] ?? [])->where('type', 'move')->count();

        $session->fill([
            'exit_page' => $path,
            'current_url' => $page['url'] ?? url('/'),
            'current_path' => $path,
            'source' => $this->sourceFromReferrer($page['referrer'] ?? null),
            'max_scroll_depth' => max((int) $session->max_scroll_depth, (int) ($page['scrollDepth'] ?? 0)),
            'event_count' => $session->event_count + count($data['events'] ?? []),
            'click_count' => $session->click_count + $clickCount,
            'movement_count' => $session->movement_count + $movementCount,
            'last_scroll_depth' => (int) ($page['scrollDepth'] ?? 0),
            'duration_seconds' => max((int) $session->duration_seconds, (int) ($page['timeOnPage'] ?? 0)),
            'recording_payload' => $this->compressor->compress($recording),
            'ended_at' => now(),
            'last_activity_at' => now(),
        ])->save();

        $sequence = PageView::where('session_id', $session->id)->max('sequence') + 1;
        $pageView = PageView::create([
            'session_id' => $session->id,
            'sequence' => $sequence,
            'url' => $page['url'] ?? url('/'),
            'path' => $path,
            'title' => $page['title'] ?? null,
            'referrer' => $page['referrer'] ?? null,
            'viewport_width' => $page['viewport']['w'] ?? null,
            'viewport_height' => $page['viewport']['h'] ?? null,
            'time_on_page' => (int) ($page['timeOnPage'] ?? 0),
            'scroll_depth' => (int) ($page['scrollDepth'] ?? 0),
            'viewed_at' => now(),
        ]);

        foreach ($data['events'] ?? [] as $event) {
            $type = (string) ($event['type'] ?? 'event');
            BehaviorEvent::create([
                'session_id' => $session->id,
                'page_view_id' => $pageView->id,
                'type' => $type,
                'path' => $path,
                'x' => isset($event['x']) ? (int) $event['x'] : null,
                'y' => isset($event['y']) ? (int) $event['y'] : null,
                'scroll_depth' => isset($event['scroll']) ? (int) $event['scroll'] : null,
                'viewport_width' => $page['viewport']['w'] ?? null,
                'viewport_height' => $page['viewport']['h'] ?? null,
                'metadata' => collect($event)->except(['x', 'y', 'scroll'])->all(),
                'occurred_ms' => (int) ($event['t'] ?? 0),
                'occurred_at' => now(),
            ]);

            if (($event['type'] ?? null) === 'click' && config('ultraclarity.features.clicks')) {
                ClickEvent::create([
                    'session_id' => $session->id,
                    'page_view_id' => $pageView->id,
                    'path' => $path,
                    'x' => (int) ($event['x'] ?? 0),
                    'y' => (int) ($event['y'] ?? 0),
                    'viewport_width' => $page['viewport']['w'] ?? null,
                    'viewport_height' => $page['viewport']['h'] ?? null,
                    'selector' => $event['selector'] ?? null,
                    'text' => isset($event['text']) ? mb_substr((string) $event['text'], 0, 190) : null,
                    'clicked_at' => now(),
                ]);
            }
        }

        foreach (array_slice($data['recording'] ?? [], -200) as $event) {
            BehaviorEvent::create([
                'session_id' => $session->id,
                'page_view_id' => $pageView->id,
                'type' => (string) ($event['type'] ?? 'recording'),
                'path' => $path,
                'x' => isset($event['x']) ? (int) $event['x'] : null,
                'y' => isset($event['y']) ? (int) $event['y'] : null,
                'scroll_depth' => isset($event['scroll']) ? (int) $event['scroll'] : null,
                'viewport_width' => $page['viewport']['w'] ?? null,
                'viewport_height' => $page['viewport']['h'] ?? null,
                'metadata' => collect($event)->except(['x', 'y', 'scroll'])->all(),
                'occurred_ms' => (int) ($event['t'] ?? 0),
                'occurred_at' => now(),
            ]);
        }

        Cache::flush();
        event(new LiveSessionUpdated($session->fresh('visitor')));
        ProcessAnalyticsJob::dispatch($path)->afterCommit();

        return $session;
    }

    public function custom(Request $request): CustomEvent
    {
        $data = $request->validate([
            'sessionId' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:120'],
            'path' => ['nullable', 'string', 'max:500'],
            'properties' => ['nullable', 'array'],
        ]);

        $session = isset($data['sessionId']) ? Session::where('session_uuid', $data['sessionId'])->first() : null;

        return CustomEvent::create([
            'session_id' => $session?->id,
            'name' => $data['name'],
            'path' => $data['path'] ?? null,
            'properties' => $data['properties'] ?? [],
            'occurred_at' => now(),
        ]);
    }

    private function sourceFromReferrer(?string $referrer): ?string
    {
        if (! $referrer) {
            return 'direct';
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        return $host ?: 'unknown';
    }
}

