<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Match as MatchModel;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'team_name', 'captain_name', 'email', 'phone',
        'address', 'team_logo', 'captain_image', 'team_banner',
        'payment_slip', 'status', 'rejection_reason',
        'draft_order', 'total_players_drafted',
    ];

    // ── Relationships ────────────────────────────────────────
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }

    public function players(): HasMany  { return $this->hasMany(Player::class); }

    public function draftPicks(): HasMany
    {
        return $this->hasMany(DraftPick::class);
    }

  public function matches(): HasMany
{
    return $this->hasMany(CricketMatch::class, 'home_team_id');
}

public function awayMatches(): HasMany
{
    return $this->hasMany(CricketMatch::class, 'away_team_id');
}

    public function draftQueue(): HasMany
    {
        return $this->hasMany(DraftQueue::class);
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopePending($q)  { return $q->where('status', 'pending'); }

    // ── Helpers ──────────────────────────────────────────────
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isPending(): bool  { return $this->status === 'pending'; }

    public function getLogoUrlAttribute(): string
    {
        return $this->team_logo
            ? asset('storage/' . $this->team_logo)
            : asset('images/default-team.png');
    }

    public function getPlayerCountByCategory(): array
    {
        return $this->players()
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map->count()
            ->toArray();
    }

    public function getMatchRecord(): array
    {
        $all     = $this->matches;
        $wins    = $all->where('result', 'win')->count();
        $losses  = $all->where('result', 'loss')->count();
        $draws   = $all->where('result', 'draw')->count();
        return compact('wins', 'losses', 'draws');
    }

    public function canPickMorePlayers(): bool
    {
        return $this->total_players_drafted < 17;
    }

    public function hasMinimumPlayers(): bool
    {
        return $this->total_players_drafted >= 16;
    }
}
