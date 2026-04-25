<?php

namespace UltraClarity\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BehaviorEvent extends Model
{
    protected $table = 'uc_behavior_events';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function pageView(): BelongsTo
    {
        return $this->belongsTo(PageView::class);
    }
}

