<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast a notification to a specific user's private channel.
 * Triggered from NotificationController or BookingService.
 *
 * Usage:
 *   broadcast(new NewNotificationEvent($userId, [
 *       'title'   => 'حجز جديد',
 *       'message' => 'لديك حجز جديد',
 *       'type'    => 'booking',
 *   ]));
 */
class NewNotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int   $userId,
        public readonly array $payload
    ) {}

    /**
     * The channel to broadcast on.
     * Must match Flutter's channel name: private-user.{userId}
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->userId}"),
        ];
    }

    /**
     * Event name received by Flutter's WebSocket listener.
     */
    public function broadcastAs(): string
    {
        return 'notification.new';
    }

    /**
     * Data sent to the Flutter client.
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
