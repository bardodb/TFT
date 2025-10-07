<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TftMatch extends Model
{
    protected $fillable = [
        'match_id',
        'game_datetime',
        'game_length',
        'game_variation',
        'game_version',
        'queue_id',
        'tft_set_number',
        'data_version',
    ];

    protected $casts = [
        'game_datetime' => 'datetime',
        'game_length' => 'float',
        'game_variation' => 'string',
        'game_version' => 'string',
        'queue_id' => 'integer',
        'tft_set_number' => 'integer',
        'data_version' => 'string',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class);
    }
}
