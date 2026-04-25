<?php

namespace PrismPath\Analytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ProcessAnalyticsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly string $path)
    {
        $this->onQueue('analytics');
    }

    public function handle(): void
    {
        if (config('ultraclarity.features.heatmaps')) {
            Artisan::call('ultraclarity:heatmaps', ['--path' => $this->path]);
        }
    }
}

