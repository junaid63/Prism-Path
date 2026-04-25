<?php

namespace UltraClarity\Analytics\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use UltraClarity\Analytics\Models\BehaviorEvent;
use UltraClarity\Analytics\Models\ClickEvent;
use UltraClarity\Analytics\Models\CustomEvent;
use UltraClarity\Analytics\Models\HeatmapData;
use UltraClarity\Analytics\Models\PageView;
use UltraClarity\Analytics\Models\Session;
use UltraClarity\Analytics\Models\Visitor;
use UltraClarity\Analytics\Services\AiInsightService;
use UltraClarity\Analytics\Services\RecordingCompressor;

class AnalyticsRepository
{
    /**
     * Return the complete dashboard payload grouped by product area.
     */
    public function dashboard(array $filters = []): array
    {
        return [
            'filters' => $this->filterOptions(),
            'stats' => $this->stats($filters),
            'liveUsers' => $this->liveUsers($filters),
            'topPages' => $this->topPages($filters),
            'navigation' => $this->navigationPaths($filters),
            'heatmaps' => $this->heatmaps($filters),
            'segments' => $this->segments($filters),
            'events' => $this->events($filters),
            'elements' => $this->elements($filters),
            'funnels' => $this->funnels($filters),
            'trend' => $this->trend($filters),
            'sessions' => $this->sessions($filters),
            'reports' => $this->scheduledReports(),
            'sections' => [
                'live' => 'Live Users',
                'sessions' => 'Sessions & Timelines',
                'pages' => 'Page Views',
                'behavior' => 'Clicks & Scrolls',
                'heatmaps' => 'Heatmaps',
                'events' => 'Events & Conversions',
                'exports' => 'Exports & Reports',
                'docs' => 'Documentation',
            ],
            'documentation' => $this->documentation(),
        ];
    }

    /**
     * Lightweight section payloads keep large dashboards responsive.
     */
    public function section(string $section, array $filters = []): array
    {
        if ($section === 'live') {
            return ['stats' => $this->stats($filters), 'liveUsers' => $this->liveUsers($filters)];
        }
        if ($section === 'sessions') {
            return ['sessions' => $this->sessions($filters), 'navigation' => $this->navigationPaths($filters)];
        }
        if ($section === 'pages') {
            return ['stats' => $this->stats($filters), 'topPages' => $this->topPages($filters), 'trend' => $this->trend($filters)];
        }
        if ($section === 'behavior') {
            return ['elements' => $this->elements($filters), 'topPages' => $this->topPages($filters)];
        }
        if ($section === 'heatmaps') {
            return ['heatmaps' => $this->heatmaps($filters)];
        }
        if ($section === 'events') {
            return ['events' => $this->events($filters), 'funnels' => $this->funnels($filters)];
        }
        if ($section === 'exports') {
            return ['reports' => $this->scheduledReports()];
        }
        if ($section === 'docs') {
            return ['docs' => $this->documentation()];
        }

        abort(404, 'Unknown PrismPath section.');
    }

    public function documentation(): array
    {
        return [
            'version' => '1.0.0',
            'sections' => ['quickstart', 'installation', 'snippet', 'events', 'replay', 'filters', 'exports', 'config', 'faq'],
            'dashboard_url' => url(config('ultraclarity.route_prefix') . '/dashboard'),
            'collector_endpoint' => url('api/ultraclarity/collect'),
            'custom_event_endpoint' => url('api/ultraclarity/event'),
            'links' => [
                'github'    => 'https://github.com/aetherpulse/prismpath',
                'changelog' => 'https://github.com/aetherpulse/prismpath/releases',
            ],
            'config' => [
                'route_prefix'   => config('ultraclarity.route_prefix'),
                'features'       => config('ultraclarity.features'),
                'cache_ttl'      => config('ultraclarity.cache_ttl'),
                'snippet'        => config('ultraclarity.snippet'),
                'retention'      => config('ultraclarity.retention'),
            ],
        ];
    }

    public function stats(array $filters = []): array
    {
        $cacheKey = 'ultraclarity:stats:' . md5(json_encode($filters));

        return Cache::remember($cacheKey, (int) config('ultraclarity.cache_ttl'), function () use ($filters): array {
            $sessions = $this->sessionQuery($filters);
            $pageViews = $this->pageViewQuery($filters);
            $clicks = $this->clickQuery($filters);
            $events = $this->eventQuery($filters);
            $live = (clone $sessions)->where('last_activity_at', '>=', now()->subMinutes(5))->count();
            $singlePageSessions = (clone $sessions)->withCount('pageViews')->get()->where('page_views_count', '<=', 1)->count();
            $sessionCount = max((clone $sessions)->count(), 1);

            return [
                'visitors' => (clone $sessions)->distinct('visitor_id')->count('visitor_id'),
                'live' => $live,
                'sessions' => $sessionCount,
                'pageViews' => (clone $pageViews)->count(),
                'clicks' => (clone $clicks)->count(),
                'movements' => $this->behaviorQuery($filters)->where('type', 'move')->count(),
                'customEvents' => (clone $events)->count(),
                'averageScroll' => round((float) (clone $pageViews)->avg('scroll_depth'), 1),
                'averageDuration' => round((float) (clone $sessions)->avg('duration_seconds'), 1),
                'averagePageDuration' => round((float) (clone $pageViews)->avg('time_on_page'), 1),
                'bounceRate' => round(($singlePageSessions / $sessionCount) * 100, 1),
                'heatmapIntensity' => round((float) HeatmapData::avg('sample_size'), 1),
            ];
        });
    }

    public function liveUsers(array $filters = []): Collection
    {
        return $this->sessionQuery($filters)
            ->with(['visitor', 'pageViews' => fn ($query) => $query->orderBy('sequence')])
            ->where('last_activity_at', '>=', now()->subMinutes(30))
            ->latest('last_activity_at')
            ->limit(50)
            ->get()
            ->map(fn (Session $session) => [
                'id' => $session->id,
                'session_uuid' => $session->session_uuid,
                'visitor_uuid' => $session->visitor?->visitor_uuid,
                'started_at' => optional($session->started_at)->toIso8601String(),
                'duration' => $session->duration_seconds,
                'current_url' => $session->current_url,
                'current_path' => $session->current_path,
                'device' => $session->visitor?->device,
                'browser' => $session->visitor?->browser,
                'os' => $session->visitor?->os,
                'ip' => $session->visitor?->ip_address,
                'city' => $session->visitor?->city,
                'country' => $session->visitor?->country,
                'authenticated' => (bool) $session->visitor?->is_authenticated,
                'clicks' => $session->click_count,
                'movements' => $session->movement_count,
                'scroll' => $session->last_scroll_depth,
                'typing' => BehaviorEvent::where('session_id', $session->id)->where('type', 'typing')->count(),
                'activity' => $this->activityLabel($session),
                'last_activity' => optional($session->last_activity_at)->diffForHumans(),
                'path' => $session->pageViews->pluck('path')->values(),
                'timeline' => $this->pageTimeline($session),
                'source' => $session->source,
            ]);
    }

    public function topPages(array $filters = []): Collection
    {
        return $this->pageViewQuery($filters)
            ->select(
                'path',
                DB::raw('count(*) as views'),
                DB::raw('round(avg(time_on_page), 1) as duration'),
                DB::raw('round(avg(scroll_depth), 1) as scroll')
            )
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(15)
            ->get()
            ->map(function ($page) use ($filters) {
                $exits = $this->sessionQuery($filters)->where('exit_page', $page->path)->count();
                $page->exit_rate = $page->views ? round(($exits / $page->views) * 100, 1) : 0;
                $page->clicks = $this->clickQuery($filters)->where('path', $page->path)->count();

                return $page;
            });
    }

    public function heatmaps(array $filters = []): Collection
    {
        if (($filters['from'] ?? null) || ($filters['to'] ?? null) || ($filters['device'] ?? null) || ($filters['authenticated'] ?? '') !== '' || ($filters['city'] ?? null) || ($filters['country'] ?? null)) {
            return $this->filteredHeatmaps($filters);
        }

        return HeatmapData::query()
            ->when($filters['page'] ?? null, fn ($query, $page) => $query->where('path', $page))
            ->latest('generated_at')
            ->limit(20)
            ->get();
    }

    public function segments(array $filters = []): array
    {
        return [
            'devices' => $this->segmentVisitors($filters, 'device'),
            'browsers' => $this->segmentVisitors($filters, 'browser'),
            'systems' => $this->segmentVisitors($filters, 'os'),
            'countries' => $this->segmentVisitors($filters, 'country'),
        ];
    }

    public function events(array $filters = []): array
    {
        return [
            'recent' => $this->eventQuery($filters)->latest('occurred_at')->limit(25)->get(),
            'frequency' => $this->eventQuery($filters)
                ->select('name', DB::raw('count(*) as total'))
                ->groupBy('name')
                ->orderByDesc('total')
                ->limit(12)
                ->get(),
            'byPage' => $this->eventQuery($filters)
                ->select('path', DB::raw('count(*) as total'))
                ->groupBy('path')
                ->orderByDesc('total')
                ->limit(12)
                ->get(),
        ];
    }

    public function elements(array $filters = []): Collection
    {
        return $this->clickQuery($filters)
            ->select('selector', DB::raw('count(*) as clicks'))
            ->whereNotNull('selector')
            ->groupBy('selector')
            ->orderByDesc('clicks')
            ->limit(12)
            ->get();
    }

    public function funnels(array $filters = []): array
    {
        $steps = config('ultraclarity.funnels.default', ['/', 'signup_started', 'plan_selected', 'checkout']);
        $totals = [];

        foreach ($steps as $step) {
            $totals[] = [
                'step' => $step,
                'sessions' => str_starts_with($step, '/')
                    ? $this->pageViewQuery($filters)->where('path', $step)->distinct('session_id')->count('session_id')
                    : $this->eventQuery($filters)->where('name', $step)->distinct('session_id')->count('session_id'),
            ];
        }

        $first = max($totals[0]['sessions'] ?? 1, 1);

        return collect($totals)->map(fn ($row) => $row + [
            'conversion' => round(($row['sessions'] / $first) * 100, 1),
        ])->all();
    }

    public function trend(array $filters = []): Collection
    {
        return $this->pageViewQuery($filters)
            ->select(DB::raw('date(viewed_at) as label'), DB::raw('count(*) as views'), DB::raw('count(distinct session_id) as sessions'))
            ->selectRaw('round(avg(time_on_page), 1) as duration')
            ->groupBy('label')
            ->orderBy('label')
            ->get();
    }

    public function sessions(array $filters = []): Collection
    {
        return $this->sessionQuery($filters)
            ->with(['visitor', 'pageViews' => fn ($query) => $query->orderBy('sequence')])
            ->latest('started_at')
            ->limit(30)
            ->get()
            ->map(fn (Session $session) => [
                'id' => $session->id,
                'session_uuid' => $session->session_uuid,
                'visitor' => $session->visitor,
                'started_at' => optional($session->started_at)->toIso8601String(),
                'ended_at' => optional($session->ended_at)->toIso8601String(),
                'duration_seconds' => $session->duration_seconds,
                'landing_page' => $session->landing_page,
                'exit_page' => $session->exit_page,
                'current_path' => $session->current_path,
                'source' => $session->source,
                'max_scroll_depth' => $session->max_scroll_depth,
                'click_count' => $session->click_count,
                'movement_count' => $session->movement_count,
                'event_count' => $session->event_count,
                'timeline' => $this->pageTimeline($session),
            ]);
    }

    public function replay(int $sessionId): array
    {
        $session = Session::with(['visitor', 'pageViews' => fn ($query) => $query->orderBy('sequence')])->findOrFail($sessionId);
        $events = app(RecordingCompressor::class)->decompress($session->recording_payload);

        return [
            'session' => $session,
            'visitor' => $session->visitor,
            'pages' => $session->pageViews,
            'pageTimeline' => $this->pageTimeline($session),
            'events' => $events,
            'timeline' => BehaviorEvent::where('session_id', $session->id)->orderBy('occurred_ms')->limit(1000)->get(),
            'ai' => $this->replayInsights($session, $events),
        ];
    }

    public function filterOptions(): array
    {
        return [
            'pages' => PageView::distinct()->orderBy('path')->pluck('path'),
            'devices' => Visitor::whereNotNull('device')->distinct()->orderBy('device')->pluck('device'),
            'browsers' => Visitor::whereNotNull('browser')->distinct()->orderBy('browser')->pluck('browser'),
            'systems' => Visitor::whereNotNull('os')->distinct()->orderBy('os')->pluck('os'),
            'countries' => Visitor::whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
            'cities' => Visitor::whereNotNull('city')->distinct()->orderBy('city')->limit(100)->pluck('city'),
        ];
    }

    public function scheduledReports(): array
    {
        return config('ultraclarity.reports.schedules', [
            ['name' => 'Executive daily', 'frequency' => 'daily', 'format' => 'pdf'],
            ['name' => 'Growth weekly', 'frequency' => 'weekly', 'format' => 'csv'],
        ]);
    }

    private function sessionQuery(array $filters = []): Builder
    {
        return Session::query()
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->where('started_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->where('started_at', '<=', Carbon::parse($to)->endOfDay()))
            ->when($filters['page'] ?? null, fn ($query, $page) => $query->where(fn ($nested) => $nested->where('landing_page', $page)->orWhere('exit_page', $page)->orWhere('current_path', $page)))
            ->when($filters['hour'] ?? null, fn ($query, $hour) => $query->whereRaw("strftime('%H', started_at) = ?", [str_pad((string) $hour, 2, '0', STR_PAD_LEFT)]))
            ->whereHas('visitor', fn ($query) => $this->applyVisitorFilters($query, $filters));
    }

    private function pageViewQuery(array $filters = []): Builder
    {
        return PageView::query()
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->where('viewed_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->where('viewed_at', '<=', Carbon::parse($to)->endOfDay()))
            ->when($filters['page'] ?? null, fn ($query, $page) => $query->where('path', $page))
            ->whereHas('session.visitor', fn ($query) => $this->applyVisitorFilters($query, $filters));
    }

    private function clickQuery(array $filters = []): Builder
    {
        return ClickEvent::query()
            ->when($filters['page'] ?? null, fn ($query, $page) => $query->where('path', $page))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->where('clicked_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->where('clicked_at', '<=', Carbon::parse($to)->endOfDay()))
            ->whereHas('session.visitor', fn ($query) => $this->applyVisitorFilters($query, $filters));
    }

    private function eventQuery(array $filters = []): Builder
    {
        return CustomEvent::query()
            ->when($filters['page'] ?? null, fn ($query, $page) => $query->where('path', $page))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->where('occurred_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->where('occurred_at', '<=', Carbon::parse($to)->endOfDay()))
            ->when(
                ($filters['device'] ?? null) || ($filters['browser'] ?? null) || ($filters['os'] ?? null) || ($filters['country'] ?? null) || ($filters['city'] ?? null) || (($filters['authenticated'] ?? '') !== ''),
                fn ($query) => $query->whereHas('session.visitor', fn ($visitor) => $this->applyVisitorFilters($visitor, $filters))
            );
    }

    private function behaviorQuery(array $filters = []): Builder
    {
        return BehaviorEvent::query()
            ->when($filters['page'] ?? null, fn ($query, $page) => $query->where('path', $page))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->where('occurred_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->where('occurred_at', '<=', Carbon::parse($to)->endOfDay()))
            ->whereHas('session.visitor', fn ($query) => $this->applyVisitorFilters($query, $filters));
    }

    private function applyVisitorFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['device'] ?? null, fn ($q, $value) => $q->where('device', $value))
            ->when($filters['browser'] ?? null, fn ($q, $value) => $q->where('browser', 'like', '%' . $value . '%'))
            ->when($filters['os'] ?? null, fn ($q, $value) => $q->where('os', $value))
            ->when($filters['country'] ?? null, fn ($q, $value) => $q->where('country', $value))
            ->when($filters['city'] ?? null, fn ($q, $value) => $q->where('city', $value))
            ->when(isset($filters['authenticated']) && $filters['authenticated'] !== '', fn ($q) => $q->where('is_authenticated', (bool) $filters['authenticated']));
    }

    private function segmentVisitors(array $filters, string $column): Collection
    {
        return Visitor::query()
            ->select($column, DB::raw('count(*) as total'))
            ->whereNotNull($column)
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    private function navigationPaths(array $filters): Collection
    {
        return $this->sessionQuery($filters)
            ->with(['visitor', 'pageViews' => fn ($query) => $query->orderBy('sequence')])
            ->latest('started_at')
            ->limit(12)
            ->get()
            ->map(fn (Session $session) => [
                'session_id' => $session->id,
                'visitor' => $session->visitor?->visitor_uuid,
                'duration' => $session->duration_seconds,
                'path' => $session->pageViews->pluck('path')->values(),
                'timeline' => $this->pageTimeline($session),
            ]);
    }

    private function filteredHeatmaps(array $filters): Collection
    {
        $ai = app(AiInsightService::class);
        $paths = $this->pageViewQuery($filters)->distinct()->limit(5)->pluck('path');

        return $paths->flatMap(function (string $path) use ($filters, $ai) {
            $pathFilters = array_merge($filters, ['page' => $path]);
            $clicks = $this->clickQuery($pathFilters)->get(['x', 'y'])->map(fn ($click) => [
                'x' => $click->x,
                'y' => $click->y,
                'weight' => 1,
            ])->all();
            $movement = $this->behaviorQuery($pathFilters)
                ->where('type', 'move')
                ->whereNotNull('x')
                ->whereNotNull('y')
                ->get(['x', 'y'])
                ->map(fn ($event) => ['x' => $event->x, 'y' => $event->y, 'weight' => 1])
                ->all();
            $scroll = $this->pageViewQuery($pathFilters)
                ->select('scroll_depth', DB::raw('count(*) as total'))
                ->groupBy('scroll_depth')
                ->get()
                ->map(fn ($row) => ['x' => 0, 'y' => (int) $row->scroll_depth, 'weight' => (int) $row->total])
                ->all();

            return collect(['click' => $clicks, 'movement' => $movement, 'scroll' => $scroll])->map(function (array $points, string $type) use ($ai, $path) {
                $hotspots = $ai->clusterPoints($points);

                return (object) [
                    'id' => md5($path . $type),
                    'path' => $path,
                    'type' => $type,
                    'points' => $points,
                    'hotspots' => $hotspots,
                    'ai_insights' => $ai->explain($hotspots, count($points)),
                    'sample_size' => count($points),
                    'generated_at' => now()->toIso8601String(),
                ];
            });
        })->values();
    }

    private function pageTimeline(Session $session): array
    {
        return $session->pageViews->map(function (PageView $page) use ($session): array {
            $events = BehaviorEvent::where('page_view_id', $page->id);

            return [
                'id' => $page->id,
                'sequence' => $page->sequence,
                'path' => $page->path,
                'url' => $page->url,
                'title' => $page->title,
                'viewed_at' => optional($page->viewed_at)->toIso8601String(),
                'duration' => $page->time_on_page,
                'scroll' => $page->scroll_depth,
                'clicks' => ClickEvent::where('page_view_id', $page->id)->count(),
                'events' => (clone $events)->count(),
                'moves' => (clone $events)->where('type', 'move')->count(),
                'source' => $session->source,
            ];
        })->values()->all();
    }

    private function replayInsights(Session $session, array $events): array
    {
        $rageClicks = collect($events)->where('type', 'click')->groupBy(fn ($event) => floor(($event['x'] ?? 0) / 40) . ':' . floor(($event['y'] ?? 0) / 40))->filter(fn ($group) => $group->count() >= 3)->count();

        return [
            'summary' => $rageClicks > 0 ? 'Repeated clicks suggest friction in this session.' : 'No obvious friction pattern detected.',
            'signals' => [
                'rage_click_clusters' => $rageClicks,
                'high_engagement' => $session->duration_seconds > 180 || $session->event_count > 25,
                'deep_scroll' => $session->max_scroll_depth >= 75,
            ],
        ];
    }

    private function activityLabel(Session $session): string
    {
        if (BehaviorEvent::where('session_id', $session->id)->where('type', 'typing')->where('occurred_at', '>=', now()->subMinutes(2))->exists()) {
            return 'Typing';
        }

        if ($session->click_count > 20) {
            return 'High click activity';
        }

        if ($session->last_scroll_depth > 80) {
            return 'Deep scrolling';
        }

        if ($session->last_activity_at && $session->last_activity_at->lt(now()->subMinutes(3))) {
            return 'Idle';
        }

        return 'Active';
    }
}

