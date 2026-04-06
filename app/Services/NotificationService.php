<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NewNotificationEvent;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Notify administrators about a system event.
     * 
     * @param string $type The notification type (e.g. 'product_added', 'new_user')
     * @param string $title
     * @param string $message
     * @param array $extraData
     * @param bool $consolidate Whether to merge with existing unread notifications of same type
     */
    public function notifyAdmins(string $type, string $title, string $message, array $extraData = [], bool $consolidate = true)
    {
        // Find all administrators
        $admins = User::whereIn('role', ['admin', 'super_admin', 'moderator', 'support'])->get();

        foreach ($admins as $admin) {
            $notification = null;

            if ($consolidate) {
                // Look for an existing unread notification of the same type for this admin
                $query = Notification::where('user_id', $admin->id)
                    ->where('type', $type)
                    ->whereNull('read_at');

                // For product additions, consolidate by merchant_id
                if ($type === 'product_added' && isset($extraData['merchant_id'])) {
                    $query->where('data->merchant_id', $extraData['merchant_id']);
                }

                // For user registrations, consolidate by role (optional, or just combine all)
                if ($type === 'new_user') {
                    // Just one "New Users" notification per admin
                }

                $notification = $query->latest()->first();
            }

            if ($notification) {
                // Update existing notification
                $data = $notification->data ?? [];
                $count = ($data['count'] ?? 1) + 1;
                $data['count'] = $count;
                
                // Update message based on count
                if ($type === 'product_added') {
                    $merchantName = $extraData['merchant_name'] ?? 'تاجر';
                    $message = "قام التاجر {$merchantName} بإضافة {$count} منتجات جديدة للتو.";
                    $title = "إضافة منتجات متعددة";
                }

                if ($type === 'new_user') {
                    $message = "انضم {$count} أعضاء جدد إلى المنصة اليوم.";
                    $title = "تسجيلات جديدة متكررة";
                }

                if ($type === 'stale_bookings' || $type === 'stale_custom_designs') {
                    $prefix = $type === 'stale_bookings' ? 'حجزاً معلقاً' : 'طلباً معلقاً';
                    $message = "يوجد حالياً {$count} {$prefix} مر عليها أكثر من 7 أيام.";
                }

                $notification->update([
                    'message' => $message,
                    'title' => $title,
                    'data' => $data,
                    'created_at' => now(), // Move to top of list
                ]);
            } else {
                // Create new notification
                $notification = Notification::create([
                    'user_id' => $admin->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => array_merge($extraData, ['count' => 1]),
                ]);
            }

            // Broadcast for real-time
            $this->broadcastToAdmin($admin->id, $notification);
        }
    }

    /**
     * Notify a specific user about an event.
     */
    public function notifyUser(int $userId, string $type, string $title, string $message, array $extraData = [])
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => array_merge($extraData, ['count' => 1]),
        ]);

        // Broadcast for real-time
        $this->broadcastToUser($userId, $notification);
    }

    protected function broadcastToUser($userId, $notification)
    {
        $payload = [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
            'created_at' => $notification->created_at->toIso8601String(),
            'data' => $notification->data,
        ];

        broadcast(new NewNotificationEvent($userId, $payload));
    }

    protected function broadcastToAdmin($adminId, $notification)
    {
        $this->broadcastToUser($adminId, $notification);
    }
}
