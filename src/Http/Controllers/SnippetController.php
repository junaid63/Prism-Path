<?php

namespace PrismPath\Analytics\Http\Controllers;

use Illuminate\Http\Response;

class SnippetController
{
    public function __invoke(): Response
    {
        $path = __DIR__ . '/../../../resources/js/tracker.js';

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}

