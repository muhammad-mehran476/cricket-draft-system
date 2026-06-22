<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DraftSession extends Model
{
    protected $fillable = [
        'title', 'status', 'current_category_id', 'current_round',
        'current_team_turn_id', 'timer_seconds', 'timer_started_at',
        'started_at', 'completed_at', 'created_by',
    ];

    protected $casts = [
        'timer_started_at' => 'datetime',
        'started_at'       => 'datetime',
        'completed_at'     => 'datetime',
    ];

    public function currentCategory(): BelongsTo { return $this->belongsTo(Category::class, 'current_category_id'); }
    public function currentTeam(): BelongsTo     { return $this->belongsTo(Team::class, 'current_team_turn_id'); }
    public function createdBy(): BelongsTo       { return $this->belongsTo(User::class, 'created_by'); }
    public function rounds(): HasMany            { return $this->hasMany(DraftRound::class); }
    public function picks(): HasMany             { return $this->hasMany(DraftPick::class); }
    public function queue(): HasMany             { return $this->hasMany(DraftQueue::class); }

    public function isActive(): bool    { return $this->status === 'active'; }
    public function isPaused(): bool    { return $this->status === 'paused'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }

    public function getTimerRemainingAttribute(): int
    {
        if (!$this->timer_started_at) return $this->timer_seconds;
        $elapsed = now()->diffInSeconds($this->timer_started_at);
        return max(0, $this->timer_seconds - $elapsed);
    }

    public function activeRound()
    {
        return $this->rounds()->where('status', 'active')->with(['category', 'queue.team'])->first();
    }

    public function currentQueueEntry()
    {
        return $this->queue()->where('status', 'active')->with('team')->first();
    }
}
