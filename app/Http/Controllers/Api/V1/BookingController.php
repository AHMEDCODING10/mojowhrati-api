<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            
            // Transform product images so full URLs are returned (same as customer branch)
            $productService = app(\App\Services\ProductService::class);
            $bookings->transform(function ($booking) use ($productService) {
                if ($booking->product) {
                    $productService->transformProductImages($booking->product);
                }
                return $booking;
            });
        } else {
            $bookings = $this->bookingService->getCustomerBookings($user->id);
            
            // Privacy and Image transformation
            $productService = app(\App\Services\ProductService::class);
            $bookings->transform(function ($booking) use ($productService) {
                if ($booking->product) {
                    $productService->transformProductImages($booking->product);
                }
                
                if ($booking->status !== 'confirmed' && $booking->status !== 'completed') {
                    if ($booking->merchant) {
                        $booking->merchant->makeHidden(['contact_number', 'whatsapp_number', 'email', 'documents', 'commercial_register', 'tax_number']);
                    }
                    if ($booking->product && $booking->product->merchant) {
                        $booking->product->merchant->makeHidden(['contact_number', 'whatsapp_number', 'email', 'documents', 'commercial_register', 'tax_number']);
                    }
                }
                return $booking;
            });
        }
        
        return $this->success($bookings);
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::with(['product', 'merchant', 'customer'])->findOrFail($id);
        
        // Authorization
        $user = $request->user();
        if ($booking->customer_id !== $user->id && $booking->merchant->user_id !== $user->id) {
            return $this->error('Unauthorized', 403);
        }

        // Privacy Logic
        if ($user->role !== 'merchant' && $booking->status !== 'confirmed') {
            if ($booking->merchant) {
                $booking->merchant->makeHidden(['contact_number', 'whatsapp_number', 'email']);
            }
            if ($booking->product && $booking->product->merchant) {
                $booking->product->merchant->makeHidden(['contact_number', 'whatsapp_number', 'email']);
            }
        }

        // Transform product images
        if ($booking->product) {
            app(\App\Services\ProductService::class)->transformProductImages($booking->product);
        }

        return $this->success($booking);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'customer_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::info('Booking Validation Failed', [
                'request' => $request->all(),
                'errors' => $validator->errors()
            ]);
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
            return $this->success($booking, 'تم الحجز بنجاح. بانتظار تأكيد التاجر.', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $booking = Booking::with(['product', 'customer'])->findOrFail($id);

        // Authorization: resolve merchant ID reliably using DB fallback
        // This avoids 403 errors on real devices where the eager-loaded relationship may not be fresh
        $userMerchantId = $user->merchant?->id
            ?? DB::table('merchants')->where('user_id', $user->id)->value('id');

        if ($user->role !== 'merchant' || !$userMerchantId || (int)$userMerchantId !== (int)$booking->merchant_id) {
            Log::warning('Unauthorized booking update attempt', [
                'user_id'             => $user->id,
                'user_role'           => $user->role,
                'resolved_merchant_id'=> $userMerchantId,
                'booking_merchant_id' => $booking->merchant_id,
            ]);
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

            // Refresh to guarantee rejection_reason is included in the serialized response
            $booking->refresh();

            return $this->success($booking, 'تم تحديث حالة الحجز بنجاح');
        } catch (\Exception $e) {
            Log::error('Booking update error', [
                'booking_id' => $id,
                'error'      => $e->getMessage(),
            ]);
            return $this->error('حدث خطأ أثناء تحديث الحجز: ' . $e->getMessage(), 500);
        }
    }
}
