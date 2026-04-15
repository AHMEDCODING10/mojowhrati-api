<?php

namespace App\Notifications;

use App\Models\CustomDesignOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CustomDesignStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(CustomDesignOrder $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return [\App\Channels\MarketplaceChannel::class];
    }

    public function toMarketplace(object $notifiable): array
    {
        $statusAr = [
            'reviewed' => 'تمت مراجعة طلب التصميم الخاص بك.',
            'contacted' => 'سيتم التواصل معك بخصوص طلب التصميم.',
            'completed' => 'تم إكمال طلب التصميم الخاص بك! 🎉',
            'rejected' => 'عذراً، تم تعديل حالة طلب التصميم.',
        ];

        $title = 'تحديث على طلب التصميم الخاص';
        $message = $statusAr[$this->order->status] ?? "تغيرت حالة طلب التصميم الخاص بك إلى: {$this->order->status}";

        return [
            'order_id' => $this->order->id,
            'title' => $title,
            'message' => $message,
            'status' => $this->order->status,
            'type' => 'custom_design_status',
        ];
    }
}
