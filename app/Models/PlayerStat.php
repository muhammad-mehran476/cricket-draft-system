<?php

namespace App\Models;

use App\Models\Match as MatchModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStat extends Model
{
    protected $fillable = [
        'player_id', 'match_id', 'runs_scored', 'balls_faced', 'fours', 'sixes',
        'wickets_taken', 'overs_bowled', 'runs_conceded', 'catches', 'run_outs', 'stumpings', 'is_not_out',
    ];

    protected $casts = [
        'is_not_out' => 'boolean',
    ];

    public function player(): BelongsTo { return $this->belongsTo(Player::class); }
public function match(): BelongsTo  { return $this->belongsTo(CricketMatch::class, 'match_id'); }
    public function getStrikeRateAttribute(): float
    {
        return $this->balls_faced > 0
            ? round(($this->runs_scored / $this->balls_faced) * 100, 2)
            : 0;
    }

    public function getEconomyAttribute(): float
    {
        return $this->overs_bowled > 0
            ? round($this->runs_conceded / $this->overs_bowled, 2)
            : 0;
    }
}
