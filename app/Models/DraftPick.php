<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftPick extends Model
{
    protected $fillable = [
        'draft_session_id', 'draft_round_id', 'team_id', 'player_id',
        'category_id', 'pick_number', 'time_taken_seconds', 'is_auto_pick', 'picked_at',
    ];

    protected $casts = [
        'picked_at'    => 'datetime',
        'is_auto_pick' => 'boolean',
    ];

    public function team(): BelongsTo         { return $this->belongsTo(Team::class); }
    public function player(): BelongsTo       { return $this->belongsTo(Player::class); }
    public function category(): BelongsTo     { return $this->belongsTo(Category::class); }
    public function draftSession(): BelongsTo { return $this->belongsTo(DraftSession::class); }
    public function draftRound(): BelongsTo   { return $this->belongsTo(DraftRound::class); }
}
