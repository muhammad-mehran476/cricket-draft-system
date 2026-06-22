<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DraftRound extends Model
{
    protected $fillable = [
        'draft_session_id', 'category_id', 'round_number', 'team_order', 'status', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'team_order'   => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function draftSession(): BelongsTo { return $this->belongsTo(DraftSession::class); }
    public function category(): BelongsTo     { return $this->belongsTo(Category::class); }
    public function picks(): HasMany          { return $this->hasMany(DraftPick::class); }
    public function queue(): HasMany          { return $this->hasMany(DraftQueue::class); }

    public function isActive(): bool    { return $this->status === 'active'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
}
