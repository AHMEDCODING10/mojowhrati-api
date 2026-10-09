<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;
use App\Models\Promotion;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminPromotionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = PromotionRequest::with(['merchant.user', 'category']);

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        $pendingCount = PromotionRequest::where('status', 'pending')->count();

        return view('promotions.requests', compact('requests', 'pendingCount'));
    }

    public function approve(PromotionRequest $promotionRequest)
    {
        if ($promotionRequest->status !== 'pending') {
            return back()->with('error', 'تم التعامل مع هذا الطلب سابقاً');
        }

        $promotionRequest->update(['status' => 'approved']);

        // Create Active Promotion
        $startAt = now();
        $endAt = now()->addWeeks($promotionRequest->duration_weeks);

        if ($promotionRequest->type === 'product' && !empty($promotionRequest->product_ids)) {
            foreach ($promotionRequest->product_ids as $productId) {
                Promotion::create([
                    'merchant_id' => $promotionRequest->merchant_id,
                    'type' => 'product',
                    'target_id' => $productId,
                    'placement' => 'HOME_TOP',
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'priority' => 20,
                    'fee_amount' => $promotionRequest->fee_amount,
                    'payment_status' => 'paid',
                    'status' => 'active',
                    'notes' => 'حملة موافق عليها بناءً على الطلب #' . $promotionRequest->id,
                ]);
            }
        } elseif ($promotionRequest->type === 'category') {
            Promotion::create([
                'merchant_id' => $promotionRequest->merchant_id,
                'type' => 'category',
                'target_id' => $promotionRequest->category_id,
                'placement' => 'CATEGORY_TOP',
                'start_at' => $startAt,
                'end_at' => $endAt,
                'priority' => 15,
                'fee_amount' => $promotionRequest->fee_amount,
                'payment_status' => 'paid',
                'status' => 'active',
                'notes' => 'حملة قسم موافق عليها بناءً على الطلب #' . $promotionRequest->id,
            ]);
        } else {
            // All Categories / Full Store
            Promotion::create([
                'merchant_id' => $promotionRequest->merchant_id,
                'type' => 'merchant_store',
                'target_id' => null,
                'placement' => 'HOME_TOP',
                'start_at' => $startAt,
                'end_at' => $endAt,
                'priority' => 25,
                'fee_amount' => $promotionRequest->fee_amount,
                'payment_status' => 'paid',
                'status' => 'active',
                'notes' => 'حملة متجر شاملة موافق عليها بناءً على الطلب #' . $promotionRequest->id,
            ]);
        }

        // Send Notification to Merchant User
        if ($promotionRequest->merchant && $promotionRequest->merchant->user_id) {
            Notification::create([
                'user_id' => $promotionRequest->merchant->user_id,
                'title' => 'تمت الموافقة على طلب الترويج 🚀',
                'message' => 'تم قبول طلب الترويج الخاص بك بنجاح وتنشيط الظهور الأول لمنتجاتك لمدة ' . $promotionRequest->duration_weeks . ' أسبوع.',
                'type' => 'promotion_approved',
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'تمت الموافقة على طلب الترويج وتفعيل الحملة بنجاح');
    }

    public function reject(Request $request, PromotionRequest $promotionRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($promotionRequest->status !== 'pending') {
            return back()->with('error', 'تم التعامل مع هذا الطلب سابقاً');
        }

        $promotionRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send Notification to Merchant User
        if ($promotionRequest->merchant && $promotionRequest->merchant->user_id) {
            Notification::create([
                'user_id' => $promotionRequest->merchant->user_id,
                'title' => 'تم رفض طلب الترويج ❌',
                'message' => 'للأسف تم رفض طلب الترويج الخاص بك. سبب الرفض: ' . $request->rejection_reason,
                'type' => 'promotion_rejected',
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'تم رفض طلب الترويج وإرسال الإشعار وبث السبب للتاجر');
    }
}
