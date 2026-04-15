<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\Api\V1\BookingResource;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'merchant') {
            $merchant = $user->merchant;
            if (!$merchant) {
                return $this->success([], 'بانتظار إعداد الملف الشخصي للتاجر');
            }
            $bookings = $this->bookingService->getMerchantBookings($merchant->id);
        } else {
            $bookings = $this->bookingService->getCustomerBookings($user->id);
        }
        
        return $this->success(BookingResource::collection($bookings));
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::with(['product', 'merchant', 'customer'])->findOrFail($id);
        
        // Authorization
        $user = $request->user();
        if ($booking->customer_id !== $user->id && $booking->merchant->user_id !== $user->id) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success(new BookingResource($booking));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'customer_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('بيانات غير صالحة', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['customer_id'] = $request->user()->id;

            // 1. Fetch Product and check stock
            $product = \App\Models\Product::find($data['product_id']);
            if (!$product || ($product->stock_quantity ?? 0) <= 0) {
                return $this->error('غير متوفر حالياً', 422);
            }

            // 2. Prevent duplicate bookings for the same product by the same user
            $existingBooking = Booking::where('customer_id', $data['customer_id'])
                ->where('product_id', $data['product_id'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($existingBooking) {
                return $this->error('لقد قمت بحجز هذا المنتج بالفعل', 422);
            }

            $booking = $this->bookingService->createBooking($data);
            return $this->success(new BookingResource($booking), 'تم الحجز بنجاح. بانتظار تأكيد التاجر.', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $booking = Booking::with(['product', 'customer'])->findOrFail($id);

        $userMerchantId = $user->merchant?->id
            ?? DB::table('merchants')->where('user_id', $user->id)->value('id');

        if ($user->role !== 'merchant' || !$userMerchantId || (int)$userMerchantId !== (int)$booking->merchant_id) {
            return $this->error('غير مصرح لك بتعديل هذا الحجز', 403);
        }

        $validator = Validator::make($request->all(), [
            'status'           => 'required|in:confirmed,rejected,completed,cancelled',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
        ], [
            'rejection_reason.required_if' => 'سبب الرفض مطلوب عند رفض الحجز',
            'status.in'                    => 'الحالة المحددة غير صالحة',
        ]);

        if ($validator->fails()) {
            return $this->error('بيانات غير صالحة', 422, $validator->errors());
        }

        try {
            $booking = $this->bookingService->updateStatus(
                $booking,
                $request->status,
                $request->rejection_reason
            );

            $booking->refresh();

            return $this->success(new BookingResource($booking), 'تم تحديث حالة الحجز بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء تحديث الحجز: ' . $e->getMessage(), 500);
        }
    }
}
