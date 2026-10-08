<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class ExpireBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Expire pending bookings that have passed their expiration time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Find all pending bookings that are expired
        $expiredBookings = Booking::with('customer', 'product')
            ->where('status', 'pending')
            ->where('expires_at', '<', $now)
            ->get();

        $count = $expiredBookings->count();

        if ($count > 0) {
            foreach ($expiredBookings as $booking) {
                /** @var Booking $booking */
                $booking->update([
                    'status' => 'expired',
                    'rejection_reason' => 'انتهت صلاحية الحجز تلقائياً لعدم التأكيد خلال الفترة المحددة.'
                ]);
                
                // Notify Customer
                if ($booking->customer) {
                    $booking->customer->notify(new \App\Notifications\BookingStatusNotification($booking));
                    
                    // Real-time broadcast
                    broadcast(new \App\Events\NewNotificationEvent($booking->customer_id, [
                        'title' => 'انتهت صلاحية الحجز ⚠️',
                        'message' => "انتهت صلاحية حجزك للمنتج {$booking->product->title}.",
                        'type' => 'booking_status',
                        'booking_id' => $booking->id,
                        'status' => 'expired',
                    ]));
                }
                
                $this->info("Booking #{$booking->id} expired and customer notified.");
            }
            
            $this->info("Successfully expired {$count} bookings.");
        } else {
            $this->info("No expired bookings found.");
        }

        return self::SUCCESS;
    }
}
