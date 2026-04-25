<?php

namespace UltraClarity\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClickEvent extends Model
{
    protected $table = 'uc_click_events';

    protected $guarded = [];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}

