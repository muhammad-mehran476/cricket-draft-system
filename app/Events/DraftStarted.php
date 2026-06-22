<?php
// ── app/Events/DraftStarted.php ───────────────────────────

namespace App\Events;

use App\Models\DraftSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DraftStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DraftSession $session) {}

    public function broadcastOn(): array
    {
        return [new Channel('draft.' . $this->session->id)];
    }

    public function broadcastAs(): string { return 'draft.started'; }

    public function broadcastWith(): array
    {
        return [
            'session_id'       => $this->session->id,
            'status'           => $this->session->status,
            'current_category' => $this->session->currentCategory?->name,
            'message'          => 'The draft has officially started!',
        ];
    }
}
