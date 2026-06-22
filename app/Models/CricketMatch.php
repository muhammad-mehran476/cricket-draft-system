<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CricketMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'home_team_id', 'away_team_id', 'opponent_name', 'match_date', 'venue',
        'match_type', 'home_runs', 'home_wickets', 'home_overs',
        'away_runs', 'away_wickets', 'away_overs', 'result', 'notes', 'created_by',
    ];

    protected $casts = [
        'match_date' => 'date',
    ];

    public function homeTeam(): BelongsTo  { return $this->belongsTo(Team::class, 'home_team_id'); }
    public function awayTeam(): BelongsTo  { return $this->belongsTo(Team::class, 'away_team_id'); }
    public function playerStats(): HasMany { return $this->hasMany(PlayerStat::class, 'match_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function getScorelineAttribute(): string
    {
        $h = $this->home_runs !== null ? "{$this->home_runs}/{$this->home_wickets}" : '-';
        $a = $this->away_runs !== null ? "{$this->away_runs}/{$this->away_wickets}" : '-';
        return "{$h} vs {$a}";
    }
}