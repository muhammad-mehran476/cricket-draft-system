<?php

namespace App\Events;

use App\Models\Category;
use App\Models\DraftSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Category $category, public DraftSession $session) {}

    public function broadcastOn(): array
    {
        return [new Channel('draft.' . $this->session->id)];
    }

    public function broadcastAs(): string { return 'category.changed'; }

    public function broadcastWith(): array
    {
        return [
            'category_id'   => $this->category->id,
            'category_name' => $this->category->name,
            'round'         => $this->session->current_round,
            'message'       => "Now drafting: {$this->category->name}",
        ];
    }
}
