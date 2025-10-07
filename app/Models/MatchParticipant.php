<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchParticipant extends Model
{
    protected $fillable = [
        'tft_match_id',
        'player_id',
        'puuid',
        'placement',
        'level',
        'gold_left',
        'total_damage_to_players',
        'win',
        'last_round',
        'time_eliminated',
        'companion',
        'traits',
        'units',
        'augments',
    ];

    protected $casts = [
        'placement' => 'integer',
        'level' => 'integer',
        'gold_left' => 'integer',
        'total_damage_to_players' => 'integer',
        'win' => 'boolean',
        'last_round' => 'integer',
        'time_eliminated' => 'float',
        'traits' => 'array',
        'units' => 'array',
        'augments' => 'array',
    ];

    public function tftMatch(): BelongsTo
    {
        return $this->belongsTo(TftMatch::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function build(): BelongsTo
    {
        return $this->belongsTo(Build::class);
    }
}
