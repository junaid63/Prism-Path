<?php

namespace PrismPath\Analytics\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PrismPath\Analytics\Repositories\AnalyticsRepository;

class ReportCommand extends Command
{
    protected $signature = 'ultraclarity:report {frequency=daily : daily, weekly, or monthly} {--email : Send to configured recipients}';

    protected $description = 'Generate scheduled PrismPath analytics reports and optionally email them.';

    public function handle(AnalyticsRepository $analytics): int
    {
        $frequency = (string) $this->argument('frequency');
        $payload = $analytics->dashboard($this->filtersFor($frequency));
        $path = 'ultraclarity/reports/' . now()->format('Y-m-d-His') . '-' . $frequency . '.json';

        Storage::disk(config('ultraclarity.storage.disk'))->put($path, json_encode($payload, JSON_PRETTY_PRINT));

        if ($this->option('email')) {
            foreach (config('ultraclarity.reports.recipients', []) as $recipient) {
                Mail::raw("Your PrismPath {$frequency} report is ready: {$path}", function ($message) use ($recipient, $frequency): void {
                    $message->to($recipient)->subject('PrismPath ' . ucfirst($frequency) . ' Report');
                });
            }
        }

        $this->info('Report generated at storage disk path: ' . $path);

        return self::SUCCESS;
    }

    private function filtersFor(string $frequency): array
    {
        if ($frequency === 'weekly') {
            return ['from' => now()->subWeek()->toDateString(), 'to' => now()->toDateString()];
        } elseif ($frequency === 'monthly') {
            return ['from' => now()->subMonth()->toDateString(), 'to' => now()->toDateString()];
        } else {
            return ['from' => now()->subDay()->toDateString(), 'to' => now()->toDateString()];
        }
    }
}

