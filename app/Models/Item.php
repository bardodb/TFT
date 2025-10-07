<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $fillable = [
        'item_id',
        'name',
        'description',
        'icon_url',
        'from_items',
        'into_items',
        'gold_cost',
        'category',
        'pick_rate',
        'win_rate',
        'avg_placement',
    ];

    protected $casts = [
        'from_items' => 'array',
        'into_items' => 'array',
        'gold_cost' => 'integer',
        'pick_rate' => 'decimal:2',
        'win_rate' => 'decimal:2',
        'avg_placement' => 'decimal:2',
    ];

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class, 'build_items')
            ->withPivot('champion_id')
            ->withTimestamps();
    }
}
