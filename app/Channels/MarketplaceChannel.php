<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Models\Notification as CustomNotification;

class MarketplaceChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toMarketplace')) {
            return;
        }

        $data = $notification->toMarketplace($notifiable);

        $newNotification = CustomNotification::create([
            'user_id' => $notifiable->id,
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'type' => $data['type'] ?? 'system',
            'title' => $data['title'] ?? 'إشعار جديد',
            'message' => $data['message'] ?? '',
            'link' => $data['link'] ?? null,
            'data' => $data['extra_data'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
        ]);

        // Broadcast for real-time popup and badge update
        $payload = [
            'id' => $newNotification->id,
            'title' => $newNotification->title,
            'message' => $newNotification->message,
            'type' => $newNotification->type,
            'created_at' => $newNotification->created_at->toIso8601String(),
            'data' => $newNotification->data,
        ];
        
        broadcast(new \App\Events\NewNotificationEvent($notifiable->id, $payload));
    }
}
