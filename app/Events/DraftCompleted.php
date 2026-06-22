<?php

namespace App\Events;

use App\Models\DraftSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DraftCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DraftSession $session) {}

    public function broadcastOn(): array
    {
        return [new Channel('draft.' . $this->session->id)];
    }

    public function broadcastAs(): string { return 'draft.completed'; }

    public function broadcastWith(): array
    {
        return [
            'session_id'   => $this->session->id,
            'completed_at' => $this->session->completed_at->toISOString(),
            'message'      => 'The draft ceremony has been completed!',
            'total_picks'  => $this->session->picks()->count(),
        ];
    }
}
