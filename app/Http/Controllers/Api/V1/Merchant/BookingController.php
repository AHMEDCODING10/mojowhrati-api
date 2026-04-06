<?php

namespace App\Http\Controllers\Api\V1\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $user     = $request->user();
        $merchant = $user->merchant;

        if (!$merchant) {
            return response()->json(['message' => 'Merchant profile not found'], 404);
        }

        $bookings = $this->bookingService->getMerchantBookings($merchant->id);

        // Transform product images
        $productService = app(\App\Services\ProductService::class);
        $bookings->transform(function ($booking) use ($productService) {
            if ($booking->product) {
                $productService->transformProductImages($booking->product);
            }
            return $booking;
        });

        return response()->json($bookings);
    }

    public function confirm(Request $request, $id)
    {
        return $this->updateStatus($request, $id, 'confirmed');
    }

    public function reject(Request $request, $id)
    {
        return $this->updateStatus($request, $id, 'rejected');
    }

    protected function updateStatus(Request $request, $id, $status)
    {
        $booking = Booking::with(['product', 'customer'])->findOrFail($id);
        $user    = $request->user();

        // Resolve merchant ID safely — avoids null-pointer on real devices
        $userMerchantId = $user->merchant?->id
            ?? DB::table('merchants')->where('user_id', $user->id)->value('id');

        if (!$userMerchantId || (int)$userMerchantId !== (int)$booking->merchant_id) {
            Log::warning('Merchant/BookingController: unauthorized update', [
                'user_id'        => $user->id,
                'merchant_id'    => $userMerchantId,
                'booking_merchant' => $booking->merchant_id,
            ]);
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا الحجز'], 403);
        }

        // Accept both 'reason' and 'rejection_reason' for compatibility
        $reason = $request->input('rejection_reason') ?? $request->input('reason');

        if ($status === 'rejected' && empty($reason)) {
            return response()->json(['message' => 'سبب الرفض مطلوب'], 422);
        }

        try {
            $updatedBooking = $this->bookingService->updateStatus($booking, $status, $reason);
            $updatedBooking->refresh();

            return response()->json([
                'message' => $status === 'confirmed' ? 'تم تأكيد الحجز بنجاح' : 'تم رفض الحجز بنجاح',
                'booking' => $updatedBooking,
            ]);
        } catch (\Exception $e) {
            Log::error('Merchant booking status update error', [
                'booking_id' => $id,
                'error'      => $e->getMessage(),
            ]);
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}

