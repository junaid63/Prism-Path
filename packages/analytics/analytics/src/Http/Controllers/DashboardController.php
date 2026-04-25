<?php

namespace UltraClarity\Analytics\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UltraClarity\Analytics\Models\ClickEvent;
use UltraClarity\Analytics\Models\CustomEvent;
use UltraClarity\Analytics\Models\PageView;
use UltraClarity\Analytics\Models\Session;
use UltraClarity\Analytics\Repositories\AnalyticsRepository;

class DashboardController
{
    private const FILTER_KEYS = ['from', 'to', 'device', 'browser', 'os', 'country', 'city', 'page', 'authenticated', 'hour'];

    public function __construct(private readonly AnalyticsRepository $analytics)
    {
    }

    public function index(): Response
    {
        return response()->view('ultraclarity::dashboard', [
            'payload' => $this->analytics->dashboard($this->filters(request())),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return $this->safeJson(fn () => $this->analytics->dashboard($this->filters($request)));
    }

    public function section(string $section, Request $request): JsonResponse
    {
        return $this->safeJson(fn () => $this->analytics->section($section, $this->filters($request)));
    }

    public function docs(): JsonResponse
    {
        return $this->safeJson(fn () => $this->analytics->documentation());
    }

    public function live(Request $request): JsonResponse
    {
        return $this->safeJson(fn () => [
            'users' => $this->analytics->liveUsers($this->filters($request)),
            'generatedAt' => now()->toIso8601String(),
        ]);
    }

    public function replay(Session $session): JsonResponse
    {
        return response()->json($this->analytics->replay($session->id));
    }

    public function export(string $type, Request $request): StreamedResponse|JsonResponse|Response
    {
        $map = [
            'pageviews' => PageView::query(),
            'clicks' => ClickEvent::query(),
            'events' => CustomEvent::query(),
        ];

        if ($type === 'json') {
            return response()->json($this->analytics->dashboard($this->filters($request)));
        }

        if ($type === 'pdf') {
            return $this->report('pdf', $request);
        }

        if (in_array($type, ['heatmaps', 'sessions', 'live', 'elements', 'timelines'], true)) {
            $payload = $this->analytics->dashboard($this->filters($request));
            $rows = match ($type) {
                'heatmaps' => collect($payload['heatmaps'])->map(fn ($row) => [
                    'path' => $row->path,
                    'type' => $row->type,
                    'sample_size' => $row->sample_size,
                    'summary' => $row->ai_insights['summary'] ?? '',
                ]),
                'sessions' => collect($payload['sessions']),
                'live' => collect($payload['liveUsers']),
                'elements' => collect($payload['elements']),
                'timelines' => collect($payload['sessions'])->flatMap(fn ($session) => collect($session['timeline'])->map(fn ($page) => $page + ['session_id' => $session['id']])),
            };

            return response()->streamDownload(function () use ($rows): void {
                $handle = fopen('php://output', 'w');
                if ($rows->isNotEmpty()) {
                    fputcsv($handle, array_keys((array) $rows->first()));
                }
                foreach ($rows as $row) {
                    fputcsv($handle, array_map(fn ($value) => is_array($value) || is_object($value) ? json_encode($value) : $value, (array) $row));
                }
                fclose($handle);
            }, 'ultraclarity-' . $type . '.csv');
        }


        abort_unless(isset($map[$type]), 404);

        return response()->streamDownload(function () use ($map, $type): void {
            $handle = fopen('php://output', 'w');
            $rows = $map[$type]->latest('id')->limit(5000)->get();
            if ($rows->isNotEmpty()) {
                fputcsv($handle, array_keys($rows->first()->getAttributes()));
            }
            foreach ($rows as $row) {
                fputcsv($handle, $row->getAttributes());
            }
            fclose($handle);
        }, 'ultraclarity-' . $type . '.csv');
    }

    public function report(string $format, Request $request): JsonResponse|Response|StreamedResponse
    {
        $payload = $this->analytics->dashboard($this->filters($request));

        if ($format === 'json') {
            return response()->json($payload);
        }

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($payload): void {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Metric', 'Value']);
                foreach ($payload['stats'] as $metric => $value) {
                    fputcsv($handle, [$metric, $value]);
                }
                fclose($handle);
            }, 'ultraclarity-report.csv');
        }

        abort_unless($format === 'pdf', 404);

        return response()->view('ultraclarity::report', ['payload' => $payload], 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="ultraclarity-report.html"',
        ]);
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'device' => ['nullable', 'string', 'max:80'],
            'browser' => ['nullable', 'string', 'max:120'],
            'os' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:5'],
            'city' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'string', 'max:500'],
            'authenticated' => ['nullable', 'in:0,1,true,false'],
            'hour' => ['nullable', 'integer', 'between:0,23'],
        ]);

        return array_intersect_key($validated, array_flip(self::FILTER_KEYS));
    }

    private function safeJson(callable $callback): JsonResponse
    {
        try {
            return response()->json($callback());
        } catch (\Throwable $exception) {
            Log::error('PrismPath dashboard API failed.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'PrismPath could not load this analytics section. Check logs for details.',
            ], 500);
        }
    }
}

