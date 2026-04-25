<?php

namespace UltraClarity\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    protected $table = 'uc_visitors';

    protected $guarded = [];

    protected $casts = [
        'traits' => 'array',
        'last_seen_at' => 'datetime',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}

