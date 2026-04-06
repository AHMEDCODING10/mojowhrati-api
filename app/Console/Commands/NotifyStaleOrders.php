<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\CustomDesignOrder;
use App\Services\NotificationService;
use Carbon\Carbon;

class NotifyStaleOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-stale-orders';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Notify admins about bookings and custom designs pending for more than 7 days';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        // 1. Check Stale Bookings
        $staleBookings = Booking::whereIn('status', ['pending', 'awaiting_response'])
            ->where('created_at', '<=', $sevenDaysAgo)
            ->count();

        if ($staleBookings > 0) {
            $notificationService->notifyAdmins(
                'stale_bookings',
                'حجوزات معلقة لفترة طويلة',
                "يوجد {$staleBookings} حجوزات مر عليها أكثر من أسبوع دون رد.",
                ['icon' => 'alert-triangle', 'stale_count' => $staleBookings],
                true // Consolidate
            );
        }

        // 2. Check Stale Custom Designs
        $staleDesigns = CustomDesignOrder::where('status', 'pending')
            ->where('created_at', '<=', $sevenDaysAgo)
            ->count();

        if ($staleDesigns > 0) {
            $notificationService->notifyAdmins(
                'stale_custom_designs',
                'طلبات تصميم خاصة معلقة',
                "يوجد {$staleDesigns} طلبات تصميم خاصة مر عليها أكثر من أسبوع دون رد.",
                ['icon' => 'alert-circle', 'stale_count' => $staleDesigns],
                true // Consolidate
            );
        }

        $this->info("Checked for stale orders. Found {$staleBookings} bookings and {$staleDesigns} designs.");
    }
}
