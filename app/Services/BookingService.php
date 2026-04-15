<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

use App\Events\NewNotificationEvent;
use App\Notifications\NewBookingNotification;
use App\Notifications\BookingStatusNotification;
use Illuminate\Support\Facades\Notification;

use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Lock the product row for update to prevent concurrent stock checks
            $product = Product::with('merchant.user')
                ->where('id', $data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // For managed stock, we only count 'pending' bookings because 'confirmed' bookings 
            // have already decremented the stock_quantity in the DB.
            $pendingBookingsCount = Booking::where('product_id', $product->id)
                ->where('status', 'pending')
                ->count();

            if ($product->manage_stock) {
                // Compare pending reservations against currently available stock
                if ($pendingBookingsCount >= $product->stock_quantity) {
                    throw ValidationException::withMessages([
                        'product_id' => ['عذراً، هذا المنتج محجوز بالكامل حالياً.'],
                    ]);
                }
            } else {
                // For unmanaged stock (unique items), any active booking (pending or confirmed) blocks it
                $activeBookingsCount = Booking::where('product_id', $product->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();
                    
                if ($activeBookingsCount > 0) {
                    throw ValidationException::withMessages([
                        'product_id' => ['هذا المنتج محجوز حالياً.'],
                    ]);
                }
            }

            $booking = Booking::create([
                'product_id'     => $product->id,
                'customer_id'    => $data['customer_id'],
                'merchant_id'    => $product->merchant_id,
                'status'         => 'pending',
                'expires_at'     => Carbon::now()->addHours(24),
                'customer_notes' => $data['customer_notes'] ?? null,
                'total_price'    => 0,
                'paid_amount'    => 0,
            ]);

            // Notify Merchant via database + WebSocket
            if ($product->merchant && $product->merchant->user) {
                $merchantUser = $product->merchant->user;
                $merchantUser->notify(new NewBookingNotification($booking));

                // 🔴 Real-time push via Reverb
                broadcast(new NewNotificationEvent($merchantUser->id, [
                    'title'      => 'حجز جديد 🛍️',
                    'message'    => "منتج \"{$product->title}\" تم حجزه",
                    'type'       => 'booking_new',
                    'booking_id' => $booking->id,
                ]));
            }

            return $booking;
        });
    }

    public function updateStatus(Booking $booking, string $status, ?string $reason = null)
    {
        return DB::transaction(function () use ($booking, $status, $reason) {
            $booking->status = $status;

            if ($status === 'confirmed') {
                $booking->confirmed_at = Carbon::now();
                if ($booking->product && $booking->product->manage_stock) {
                    $productService = app(\App\Services\ProductService::class);
                    $productService->deductStock($booking->product, 1);
                }
            } elseif ($status === 'rejected') {
                $booking->rejected_at    = Carbon::now();
                $booking->rejection_reason = $reason;
            } elseif ($status === 'completed') {
                $booking->completed_at = Carbon::now();
            }

            $booking->save();

            // Notify Customer via database + WebSocket
            if (in_array($status, ['confirmed', 'rejected', 'cancelled'])) {
                $customer = $booking->customer;
                $customer->notify(new BookingStatusNotification($booking));

                // 🔴 Real-time push via Reverb
                $statusLabels = [
                    'confirmed' => 'تم تأكيد حجزك ✅',
                    'rejected'  => 'تم رفض الحجز ❌',
                    'cancelled' => 'تم إلغاء الحجز',
                ];
                broadcast(new NewNotificationEvent($customer->id, [
                    'title'      => $statusLabels[$status] ?? 'تحديث الحجز',
                    'message'    => "تم تغيير حالة حجزك للمنتج {$booking->product->title}.",
                    'type'       => 'booking_status',
                    'booking_id' => $booking->id,
                    'status'     => $status,
                ]));
            }

            return $booking;
        });
    }

    public function getCustomerBookings($customerId)
    {
        $bookings = Booking::with(['product.merchant.user', 'merchant.user'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();

        $bookings->each(function ($booking) {
            if ($booking->product && $booking->product->merchant) {
                $booking->product->merchant->makeHidden(['email', 'documents', 'commercial_register', 'tax_number']);
            }
            if ($booking->merchant) {
                $booking->merchant->makeHidden(['email', 'documents', 'commercial_register', 'tax_number']);
            }
        });

        return $bookings;
    }

    public function getMerchantBookings($merchantId)
    {
        return Booking::with(['product.images', 'product.merchant', 'customer'])
            ->where('merchant_id', $merchantId)
            ->latest()
            ->get();
    }
}
