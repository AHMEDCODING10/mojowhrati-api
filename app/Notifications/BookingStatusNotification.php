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
            'expired' => 'انتهت صلاحية الحجز.',
            'completed' => 'تم إكمال الطلب.',
        ];

        $title = $statusAr[$this->booking->status] ?? 'تحديث على حالة الحجز';
        $itemTitle = $this->booking->product->title;
        $qty = $this->booking->quantity;
        
        $message = "تم تغيير حالة حجزك للمنتج $itemTitle (الكمية: $qty).";
        
        if ($this->booking->status === 'confirmed') {
            $message = "تم تأكيد حجزك لعدد ($qty) قطعة من $itemTitle. يمكنك الآن التواصل مع التاجر.";
        } elseif ($this->booking->status === 'rejected') {
            $message = "عذراً، تم رفض حجزك لـ $itemTitle. السبب: " . ($this->booking->rejection_reason ?? 'غير محدد');
        } elseif ($this->booking->status === 'expired') {
            $message = "انتهت صلاحية حجزك للمنتج $itemTitle لعدم التأكيد خلال 24 ساعة.";
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
