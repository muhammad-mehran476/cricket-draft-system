<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'address', 'city',
        'profile_picture', 'role', 'skill_level', 'bowling_type',
        'batting_style', 'category_id', 'payment_slip', 'status',
        'rejection_reason', 'team_id', 'rules_accepted',
    ];

    protected $casts = [
        'rules_accepted' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function team(): BelongsTo     { return $this->belongsTo(Team::class); }

    public function draftPick(): HasOne
    {
        return $this->hasOne(DraftPick::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(PlayerStat::class);
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopeApproved($q)    { return $q->where('status', 'approved'); }
    public function scopePending($q)     { return $q->where('status', 'pending'); }
    public function scopeDrafted($q)     { return $q->where('status', 'drafted'); }
    public function scopeAvailable($q)   { return $q->where('status', 'approved'); }
    public function scopeByCategory($q, $categoryId) { return $q->where('category_id', $categoryId); }

    // ── Helpers ──────────────────────────────────────────────
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isDrafted(): bool  { return $this->status === 'drafted'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile_picture
            ? asset('storage/' . $this->profile_picture)
            : asset('images/default-player.png');
    }

    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'batsman'       => 'Batsman',
            'bowler'        => 'Bowler',
            'all_rounder'   => 'All Rounder',
            'wicket_keeper' => 'Wicket Keeper',
            default         => ucfirst($this->role),
        };
    }

    public function getSkillBadgeClassAttribute(): string
    {
        return match($this->skill_level) {
            'best'   => 'badge-danger',
            'better' => 'badge-warning',
            default  => 'badge-success',
        };
    }

    // Batting average
    public function getBattingAverageAttribute(): float
    {
        $stats = $this->stats;
        $innings = $stats->count();
        if ($innings === 0) return 0;
        $notOuts  = $stats->where('is_not_out', true)->count();
        $totalRuns = $stats->sum('runs_scored');
        $dismissals = $innings - $notOuts;
        return $dismissals > 0 ? round($totalRuns / $dismissals, 2) : $totalRuns;
    }

    // Bowling average
    public function getBowlingAverageAttribute(): float
    {
        $wickets = $this->stats->sum('wickets_taken');
        $runs    = $this->stats->sum('runs_conceded');
        return $wickets > 0 ? round($runs / $wickets, 2) : 0;
    }
}
