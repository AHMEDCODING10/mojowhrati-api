<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MerchantVerificationNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $notes = null)
    {
        $this->status = $status;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = $this->status === 'approved' ? 'تم توثيق متجرك بنجاح' : 'تم رفض طلب التوثيق';
        $message = $this->status === 'approved' 
            ? 'مبارك! تم تفعيل حسابك كتاجر في منصة مجوهراتي.' 
            : 'عذراً، تم رفض طلب التوثيق الخاص بك. ملاحظات: ' . ($this->notes ?? 'يرجى مراجعة البيانات.');

        return [
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'title' => $title,
            'message' => $message,
            'type' => 'merchant_verification',
            'status' => $this->status,
            'notes' => $this->notes,
            'action_url' => '/merchant/profile',
        ];
    }
}
