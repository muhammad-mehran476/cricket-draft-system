<?php

namespace App\Events;

use App\Models\DraftPick;
use App\Models\DraftSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerPicked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DraftPick $pick, public DraftSession $session) {}

    public function broadcastOn(): array
    {
        return [new Channel('draft.' . $this->session->id)];
    }

    public function broadcastAs(): string { return 'player.picked'; }

    public function broadcastWith(): array
    {
        return [
            'pick_id'        => $this->pick->id,
            'pick_number'    => $this->pick->pick_number,
            'player'         => [
                'id'    => $this->pick->player->id,
                'name'  => $this->pick->player->name,
                'role'  => $this->pick->player->role,
                'skill' => $this->pick->player->skill_level,
                'photo' => $this->pick->player->profile_picture_url,
            ],
            'team'           => [
                'id'   => $this->pick->team->id,
                'name' => $this->pick->team->team_name,
                'logo' => $this->pick->team->logo_url,
            ],
            'category'       => $this->pick->category->name,
            'picked_at'      => $this->pick->picked_at->toISOString(),
            'next_team'      => $this->session->currentTeam?->team_name,
            'timer_seconds'  => $this->session->timer_seconds,
        ];
    }
}
