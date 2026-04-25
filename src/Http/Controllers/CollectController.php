<?php

namespace PrismPath\Analytics\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PrismPath\Analytics\Services\AnalyticsIngestor;

class CollectController
{
    public function __construct(private readonly AnalyticsIngestor $ingestor)
    {
    }

    public function store(Request $request): JsonResponse
    {
        if (! config('ultraclarity.enabled')) {
            return response()->json(['ok' => false], 403);
        }

        $session = $this->ingestor->ingest($request);

        return response()->json(['ok' => true, 'session' => $session->session_uuid]);
    }

    public function custom(Request $request): JsonResponse
    {
        $event = $this->ingestor->custom($request);

        return response()->json(['ok' => true, 'event' => $event->id]);
    }
}

