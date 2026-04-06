<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification implements ShouldQueue
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
        $statusAr = [
            'confirmed' => 'تم تأكيد حجزك! 🎉',
            'rejected' => 'عذراً، تم رفض حجزك.',
            'cancelled' => 'تم إلغاء الحجز.',
            'completed' => 'تم إكمال الطلب.',
        ];

        $title = $statusAr[$this->booking->status] ?? 'تحديث على حالة الحجز';
        $message = "تم تغيير حالة حجزك للمنتج {$this->booking->product->title}.";
        
        if ($this->booking->status === 'confirmed') {
            $message .= " يمكنك الآن التواصل مع التاجر.";
        }

        return [
            'booking_id' => $this->booking->id,
            'title' => $title,
            'message' => $message,
            'status' => $this->booking->status,
            'type' => 'booking_status',
        ];
    }
}
