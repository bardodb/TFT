<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Champion extends Model
{
    protected $fillable = [
        'champion_id',
        'name',
        'cost',
        'traits',
        'stats',
        'ability',
        'image_url',
    ];

    protected $casts = [
        'traits' => 'array',
        'stats' => 'array',
        'ability' => 'array',
    ];

    public function builds(): BelongsToMany
    {
        return $this->belongsToMany(Build::class, 'build_champions')
            ->withPivot('star_level', 'position')
            ->withTimestamps();
    }
}
