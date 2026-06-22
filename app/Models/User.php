<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ────────────────────────────────────────
    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    public function team(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Role helpers ─────────────────────────────────────────
    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isPlayer(): bool       { return $this->role === 'player'; }
    public function isTeamCaptain(): bool  { return $this->role === 'team_captain'; }
    public function isActive(): bool       { return $this->status === 'active'; }

    // ── Scopes ───────────────────────────────────────────────
    public function scopeAdmins($q)        { return $q->where('role', 'admin'); }
    public function scopePlayers($q)       { return $q->where('role', 'player'); }
    public function scopeTeamCaptains($q)  { return $q->where('role', 'team_captain'); }
}
