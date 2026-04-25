<?php

namespace UltraClarity\Analytics\Commands;

use Illuminate\Console\Command;
use UltraClarity\Analytics\Models\BehaviorEvent;
use UltraClarity\Analytics\Models\ClickEvent;
use UltraClarity\Analytics\Models\CustomEvent;
use UltraClarity\Analytics\Models\Session;

class CleanupCommand extends Command
{
    protected $signature = 'ultraclarity:cleanup';

    protected $description = 'Delete expired PrismPath raw events and recordings.';

    public function handle(): int
    {
        $rawBefore = now()->subDays((int) config('ultraclarity.retention.raw_events_days'));
        $recordingBefore = now()->subDays((int) config('ultraclarity.retention.recordings_days'));

        $clicks = ClickEvent::where('clicked_at', '<', $rawBefore)->delete();
        $behavior = BehaviorEvent::where('occurred_at', '<', $rawBefore)->delete();
        $events = CustomEvent::where('occurred_at', '<', $rawBefore)->delete();
        $recordings = Session::where('ended_at', '<', $recordingBefore)->update(['recording_payload' => null]);

        $this->info("Removed {$clicks} clicks, {$behavior} behavior events, {$events} custom events, and cleared {$recordings} recordings.");

        return self::SUCCESS;
    }
}

