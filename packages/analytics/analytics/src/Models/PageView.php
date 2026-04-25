<?php

namespace UltraClarity\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    protected $table = 'uc_page_views';

    protected $guarded = [];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}

