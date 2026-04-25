<?php

namespace PrismPath\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

class HeatmapData extends Model
{
    protected $table = 'uc_heatmap_data';

    protected $guarded = [];

    protected $casts = [
        'points' => 'array',
        'hotspots' => 'array',
        'ai_insights' => 'array',
        'generated_at' => 'datetime',
    ];
}

