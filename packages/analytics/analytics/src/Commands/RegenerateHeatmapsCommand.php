<?php

namespace UltraClarity\Analytics\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use UltraClarity\Analytics\Models\BehaviorEvent;
use UltraClarity\Analytics\Models\ClickEvent;
use UltraClarity\Analytics\Models\HeatmapData;
use UltraClarity\Analytics\Models\PageView;
use UltraClarity\Analytics\Services\AiInsightService;

class RegenerateHeatmapsCommand extends Command
{
    protected $signature = 'ultraclarity:heatmaps {--path=}';

    protected $description = 'Regenerate click and scroll heatmap aggregates with AI-assisted hotspots.';

    public function handle(AiInsightService $ai): int
    {
        $paths = PageView::query()
            ->when($this->option('path'), fn ($query, $path) => $query->where('path', $path))
            ->distinct()
            ->pluck('path');

        foreach ($paths as $path) {
            $clicks = ClickEvent::where('path', $path)->get(['x', 'y'])->map(fn ($click) => [
                'x' => $click->x,
                'y' => $click->y,
                'weight' => 1,
            ])->all();

            $scroll = PageView::where('path', $path)
                ->select('scroll_depth', DB::raw('count(*) as total'))
                ->groupBy('scroll_depth')
                ->get()
                ->map(fn ($row) => ['x' => 0, 'y' => (int) $row->scroll_depth, 'weight' => (int) $row->total])
                ->all();
            $movement = BehaviorEvent::where('path', $path)
                ->where('type', 'move')
                ->whereNotNull('x')
                ->whereNotNull('y')
                ->get(['x', 'y'])
                ->map(fn ($event) => ['x' => $event->x, 'y' => $event->y, 'weight' => 1])
                ->all();

            foreach (['click' => $clicks, 'scroll' => $scroll, 'movement' => $movement] as $type => $points) {
                $hotspots = $ai->clusterPoints($points);
                HeatmapData::updateOrCreate(
                    ['path' => $path, 'type' => $type],
                    [
                        'points' => $points,
                        'hotspots' => $hotspots,
                        'ai_insights' => $ai->explain($hotspots, count($points)),
                        'sample_size' => count($points),
                        'generated_at' => now(),
                    ]
                );
            }
        }

        $this->info('Heatmaps regenerated for ' . $paths->count() . ' page(s).');

        return self::SUCCESS;
    }
}

