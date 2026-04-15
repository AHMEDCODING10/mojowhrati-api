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
        $expiredBookings = Booking::where('status', 'pending')
            ->where('expires_at', '<', $now)
            ->get();

        $count = $expiredBookings->count();

        if ($count > 0) {
            foreach ($expiredBookings as $booking) {
                $booking->update([
                    'status' => 'expired',
                    'rejection_reason' => 'انتهت صلاحية الحجز تلقائياً لعدم التأكيد خلال الفترة المحددة.'
                ]);
                
                $this->info("Booking #{$booking->id} expired.");
            }
            
            $this->info("Successfully expired {$count} bookings.");
        } else {
            $this->info("No expired bookings found.");
        }

        return Command::SUCCESS;
    }
}
