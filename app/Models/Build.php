<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Build extends Model
{
    protected $fillable = [
        'player_id',
        'tft_match_id',
        'placement',
        'level',
        'gold_left',
        'total_damage_to_players',
        'win',
        'composition_name',
        'description',
        'win_rate',
        'pick_rate',
        'avg_placement',
    ];

    protected $casts = [
        'placement' => 'integer',
        'level' => 'integer',
        'gold_left' => 'integer',
        'total_damage_to_players' => 'integer',
        'win' => 'boolean',
        'win_rate' => 'decimal:2',
        'pick_rate' => 'decimal:2',
        'avg_placement' => 'decimal:2',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function tftMatch(): BelongsTo
    {
        return $this->belongsTo(TftMatch::class);
    }

    public function champions(): BelongsToMany
    {
        return $this->belongsToMany(Champion::class, 'build_champions')
            ->withPivot('star_level', 'position')
            ->withTimestamps();
    }

    public function augments(): BelongsToMany
    {
        return $this->belongsToMany(Augment::class, 'build_augments')
            ->withTimestamps();
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'build_items')
            ->withPivot('champion_id')
            ->withTimestamps();
    }
}
