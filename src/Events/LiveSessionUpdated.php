<?php

namespace PrismPath\Analytics\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use PrismPath\Analytics\Models\Session;

class LiveSessionUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Session $session)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('ultraclarity.live');
    }

    public function broadcastAs(): string
    {
        return 'session.updated';
    }

    public function broadcastWith(): array
    {
        $visitor = $this->session->visitor;

        return [
            'id' => $this->session->id,
            'session_uuid' => $this->session->session_uuid,
            'visitor_uuid' => $visitor?->visitor_uuid,
            'current_url' => $this->session->current_url,
            'current_path' => $this->session->current_path,
            'duration' => $this->session->duration_seconds,
            'clicks' => $this->session->click_count,
            'scroll' => $this->session->last_scroll_depth,
            'activity' => $this->session->last_scroll_depth > 80 ? 'Deep scrolling' : 'Active',
            'device' => $visitor?->device,
            'browser' => $visitor?->browser,
            'os' => $visitor?->os,
            'city' => $visitor?->city,
            'country' => $visitor?->country,
            'last_activity' => optional($this->session->last_activity_at)->toIso8601String(),
        ];
    }
}

