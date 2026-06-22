<?php

namespace App\Events;

use App\Models\DraftSession;
use App\Models\Team;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DraftTimerExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DraftSession $session, public Team $team) {}

    public function broadcastOn(): array
    {
        return [new Channel('draft.' . $this->session->id)];
    }

    public function broadcastAs(): string { return 'timer.expired'; }

    public function broadcastWith(): array
    {
        return [
            'session_id'   => $this->session->id,
            'skipped_team' => $this->team->team_name,
            'next_team'    => $this->session->currentTeam?->team_name,
            'message'      => "{$this->team->team_name}'s turn was skipped (timer expired).",
        ];
    }
}
