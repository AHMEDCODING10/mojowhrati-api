<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\MarketplaceChannel;

class BroadcastNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $link;

    public function __construct($title, $message, $link = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->link = $link;
    }

    public function via(object $notifiable): array
    {
        return [MarketplaceChannel::class];
    }

    public function toMarketplace(object $notifiable): array
    {
        return [
            'type' => 'broadcast',
            'title' => $this->title,
            'message' => $this->message,
            'link' => $this->link,
            'priority' => 'medium',
        ];
    }
}
