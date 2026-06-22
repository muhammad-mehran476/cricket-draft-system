<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'max_players', 'draft_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function players(): HasMany     { return $this->hasMany(Player::class); }
    public function draftRounds(): HasMany { return $this->hasMany(DraftRound::class); }
    public function draftPicks(): HasMany  { return $this->hasMany(DraftPick::class); }

    public function getAvailablePlayersCount(): int
    {
        return $this->players()->where('status', 'approved')->count();
    }

    public function getDraftedPlayersCount(): int
    {
        return $this->players()->where('status', 'drafted')->count();
    }

    public function scopeOrdered($q) { return $q->orderBy('draft_order'); }
    public function scopeActive($q)  { return $q->where('is_active', true); }
}
