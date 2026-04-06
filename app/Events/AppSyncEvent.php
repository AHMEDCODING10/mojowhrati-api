<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppSyncEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $model;
    public $action;

    /**
     * Create a new event instance.
     *
     * @param string $model - e.g., 'product', 'booking', 'custom_design'
     * @param string $action - e.g., 'created', 'updated', 'deleted'
     */
    public function __construct(string $model, string $action)
    {
        $this->model = $model;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast on a public channel so all connected clients receive UI updates silently.
        return [
            new Channel('public.sync'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'app.sync';
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'model' => $this->model,
            'action' => $this->action,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
