<?php

namespace UltraClarity\Analytics\Services;

class RecordingCompressor
{
    public function compress(array $events): string
    {
        return base64_encode(gzencode(json_encode($events, JSON_THROW_ON_ERROR), 6));
    }

    public function decompress(?string $payload): array
    {
        if (! $payload) {
            return [];
        }

        $json = gzdecode(base64_decode($payload, true) ?: '') ?: '[]';

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}

