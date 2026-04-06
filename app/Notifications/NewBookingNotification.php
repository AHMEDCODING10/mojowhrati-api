<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return [\App\Channels\MarketplaceChannel::class];
    }

    public function toMarketplace(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'title' => 'طلب حجز جديد 🆕',
            'message' => "لديك طلب حجز جديد للمنتج {$this->booking->product->title} من العميل {$this->booking->customer->name}.",
            'type' => 'booking_created',
            'priority' => 'high',
        ];
    }
}
