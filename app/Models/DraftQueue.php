<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftQueue extends Model
{
    protected $table = 'draft_queue';

    protected $fillable = [
        'draft_session_id', 'draft_round_id', 'team_id', 'pick_position', 'status', 'timer_expires_at',
    ];

    protected $casts = [
        'timer_expires_at' => 'datetime',
    ];

    public function team(): BelongsTo         { return $this->belongsTo(Team::class); }
    public function draftSession(): BelongsTo { return $this->belongsTo(DraftSession::class); }
    public function draftRound(): BelongsTo   { return $this->belongsTo(DraftRound::class); }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isDone(): bool   { return $this->status === 'done'; }

    public function getSecondsRemainingAttribute(): int
    {
        if (!$this->timer_expires_at) return 0;
        return max(0, now()->diffInSeconds($this->timer_expires_at, false));
    }
}
