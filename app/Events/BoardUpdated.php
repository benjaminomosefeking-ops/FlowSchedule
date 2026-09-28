<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Board;

class BoardUpdated implements ShouldBroadcastNow
{
    /**
     * Get the connection name for the broadcast.
     *
     * @return string|null
     */
    public function connection()
    {
        return 'log';
    }
}
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $board;

    /**
     * Create a new event instance.
     */
    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('board.' . $this->board->id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'board.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->board->id,
            'content' => $this->board->content,
            'updated_at' => $this->board->updated_at->toISOString(),
        ];
    }
}