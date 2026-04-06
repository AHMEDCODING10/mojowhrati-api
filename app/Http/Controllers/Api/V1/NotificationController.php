<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->success([]);
        }

        // Support both custom user_id and Laravel standard notifiable_id
        $notifications = \App\Models\Notification::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function($q) use ($user) {
                          $q->where('notifiable_id', $user->id)
                            ->where('notifiable_type', get_class($user));
                      });
            })
            ->latest()
            ->limit(50) // Increased limit for better history
            ->get();
            
        return $this->success($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = \App\Models\Notification::where('id', $id)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('notifiable_id', $user->id);
            })
            ->first();
            
        if ($notification) {
            $notification->markAsRead();
        }
        return $this->success(null, 'تم تحديد الإشعار كمقروء');
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        \App\Models\Notification::whereNull('read_at')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('notifiable_id', $user->id);
            })
            ->update(['read_at' => now()]);
            
        return $this->success(null, 'تم تحديد جميع الإشعارات كمقروءة');
    }
}
