<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Augment extends Model
{
    protected $fillable = [
        'augment_id',
        'name',
        'description',
        'icon_url',
        'tier',
        'category',
        'pick_rate',
        'win_rate',
        'avg_placement',
    ];

    protected $casts = [
        'tier' => 'integer',
        'pick_rate' => 'decimal:2',
        'win_rate' => 'decimal:2',
        'avg_placement' => 'decimal:2',
    ];

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class, 'build_augments')
            ->withTimestamps();
    }
}
