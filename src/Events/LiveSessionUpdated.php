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

    /**
     * @var Session
     */
    public $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
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
            'visitor_uuid' => $visitor ? $visitor->visitor_uuid : null,
            'current_url' => $this->session->current_url,
            'current_path' => $this->session->current_path,
            'duration' => $this->session->duration_seconds,
            'clicks' => $this->session->click_count,
            'scroll' => $this->session->last_scroll_depth,
            'activity' => $this->session->last_scroll_depth > 80 ? 'Deep scrolling' : 'Active',
            'device' => $visitor ? $visitor->device : null,
            'browser' => $visitor ? $visitor->browser : null,
            'os' => $visitor ? $visitor->os : null,
            'city' => $visitor ? $visitor->city : null,
            'country' => $visitor ? $visitor->country : null,
            'last_activity' => optional($this->session->last_activity_at)->toIso8601String(),
        ];
    }
}

