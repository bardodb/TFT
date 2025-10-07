<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $fillable = [
        'puuid',
        'summoner_id',
        'game_name',
        'tag_line',
        'tier',
        'rank',
        'league_points',
        'wins',
        'losses',
        'region',
    ];

    protected $casts = [
        'league_points' => 'integer',
        'wins' => 'integer',
        'losses' => 'integer',
    ];

    public function matchParticipants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class);
    }

    public function builds(): HasMany
    {
        return $this->hasMany(Build::class);
    }
}
