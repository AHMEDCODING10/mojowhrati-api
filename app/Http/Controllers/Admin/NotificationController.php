<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Pseudo-scheduling: Dispatch any pending scheduled master broadcasts
        $pendingBroadcasts = Notification::where('is_dispatched', false)
            ->where('scheduled_at', '<=', now())
            ->where('data->is_master', true)
            ->get();

        foreach ($pendingBroadcasts as $broadcast) {
            $this->dispatchBroadcast($broadcast->target, $broadcast->title, $broadcast->message, $broadcast->link);
            /** @var Notification $broadcast */
            $broadcast->update(['is_dispatched' => true]);
        }

        $userId = auth()->id();
        $notifications = Notification::where(function ($q) use ($userId) {
            // Admin's own personal notifications
            $q->where('user_id', $userId)
                ->whereNull('data->is_master');
        })
            ->orWhere(function ($q) {
                // ALL Master broadcasts (sent by any admin)
                $q->where('data->is_master', true);
            })
            ->latest()
            ->paginate(20);

        if (request()->ajax() && request()->has('latest')) {
            $latest = $notifications->first();
            if ($latest) {
                return response()->json([
                    'id' => $latest->id,
                    'html' => view('notifications.partials.item', ['notification' => $latest])->render()
                ]);
            }
            return response()->json(['html' => '']);
        }

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'تم تحديد التنبيه كمقروء');
    }

    public function readAll()
    {
        Notification::query()->update(['read_at' => now()]);
        return back()->with('success', 'تم تحديد جميع التنبيهات كمقروءة');
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف الإشعار بنجاح');
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|in:all,merchants,customers,staff',
            'link' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after_or_equal:now',
        ]);

        $isScheduled = !empty($request->scheduled_at);

        // Save Master Broadcast Record
        $master = Notification::create([
            'user_id' => auth()->id(),
            'type' => 'system_announcement',
            'target' => $request->target,
            'title' => $request->title,
            'message' => $request->message,
            'link' => $request->link,
            'scheduled_at' => $isScheduled ? $request->scheduled_at : now(),
            'is_dispatched' => !$isScheduled,
            'data' => ['is_master' => true, 'icon' => 'megaphone']
        ]);

        if ($isScheduled) {
            return back()->with('success', 'تمت جدولة الإشعار الجماعي بنجاح');
        }

        // Send immediately - Phase 1: Real-time Global Event (Instant for all active users)
        broadcast(new \App\Events\GlobalNotificationEvent([
            'title' => $request->title,
            'message' => $request->message,
            'link' => $request->link,
            'type' => 'system_announcement',
            'icon' => 'megaphone'
        ]));

        // Send immediately - Phase 2: Per-user database record loop (for persistence)
        $count = $this->dispatchBroadcast($request->target, $request->title, $request->message, $request->link);

        return back()->with('success', 'تم إرسال الإشعار الجماعي بنجاح إلى ' . $count . ' مستخدم');
    }

    private function dispatchBroadcast($target, $title, $message, $link)
    {
        $query = \App\Models\User::query();
        if ($target === 'merchants') {
            $query->where('role', 'merchant');
        } elseif ($target === 'customers') {
            $query->where('role', 'customer');
        } elseif ($target === 'staff') {
            $query->whereIn('role', ['admin', 'super_admin', 'moderator', 'support']);
        }

        $users = $query->get();

        foreach ($users as $user) {
            // Using BroadcastNotification (will go through MarketplaceChannel)
            /** @var \App\Models\User $user */
            $user->notify(new \App\Notifications\BroadcastNotification($title, $message, $link));
        }

        return $users->count();
    }

    // Static helper to create notifications from anywhere
    public static function notify($type, $title, $message, $link = null, $userId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link
        ]);
    }

}
