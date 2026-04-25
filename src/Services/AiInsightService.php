<?php

namespace PrismPath\Analytics\Services;

class AiInsightService
{
    public function clusterPoints(array $points): array
    {
        $buckets = [];

        foreach ($points as $point) {
            $key = floor(($point['x'] ?? 0) / 80) . ':' . floor(($point['y'] ?? 0) / 80);
            $buckets[$key]['count'] = ($buckets[$key]['count'] ?? 0) + (int) ($point['weight'] ?? 1);
            $buckets[$key]['x'] = (($buckets[$key]['x'] ?? 0) + (int) ($point['x'] ?? 0));
            $buckets[$key]['y'] = (($buckets[$key]['y'] ?? 0) + (int) ($point['y'] ?? 0));
        }

        $clusters = collect($buckets)->map(function (array $bucket): array {
            $count = max($bucket['count'], 1);

            return [
                'x' => (int) round($bucket['x'] / $count),
                'y' => (int) round($bucket['y'] / $count),
                'intensity' => $count,
                'label' => $count > 12 ? 'High intent hotspot' : 'Emerging interaction pattern',
            ];
        })->sortByDesc('intensity')->take(8)->values()->all();

        return $clusters;
    }

    public function explain(array $hotspots, int $sampleSize): array
    {
        $top = $hotspots[0]['intensity'] ?? 0;

        return [
            'summary' => $sampleSize === 0
                ? 'Not enough engagement data yet.'
                : 'Detected ' . count($hotspots) . ' repeated behavior clusters across ' . $sampleSize . ' samples.',
            'recommendations' => [
                $top > 20 ? 'Move primary calls to action closer to the hottest cluster.' : 'Collect more traffic before making layout changes.',
                'Compare scroll depth against click intensity to find content that is seen but not acted on.',
                'Replay sessions around the top hotspot before changing conversion-critical UI.',
            ],
        ];
    }
}

