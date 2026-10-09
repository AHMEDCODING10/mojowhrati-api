<?php

namespace App\Http\Controllers\Api\V1\Merchant;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;
use App\Services\ImageKitService;
use Illuminate\Http\Request;

class PromotionRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->merchant_id) {
            return $this->error('حسابك غير مرطب بطب تاجر', 403);
        }

        $requests = PromotionRequest::where('merchant_id', $user->merchant_id)
            ->with(['category'])
            ->latest()
            ->get();

        return $this->success($requests);
    }

    public function calculateFee(Request $request)
    {
        $type = $request->input('type'); // product, category, all_categories
        $durationWeeks = max(1, (int)$request->input('duration_weeks', 1));
        $productCount = max(1, count($request->input('product_ids', [1])));

        $weeklyRate = 20.00;
        if ($type === 'product') {
            $weeklyRate = 20.00 * $productCount;
        } elseif ($type === 'category') {
            $weeklyRate = 50.00;
        } elseif ($type === 'all_categories') {
            $weeklyRate = 120.00;
        }

        $totalFee = $weeklyRate * $durationWeeks;

        return $this->success([
            'type' => $type,
            'duration_weeks' => $durationWeeks,
            'product_count' => $productCount,
            'weekly_rate' => $weeklyRate,
            'total_fee' => $totalFee,
            'currency' => 'USD',
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->merchant_id) {
            return $this->error('غير مصرح لك بطلب ترويج', 403);
        }

        $request->validate([
            'type' => 'required|string|in:product,category,all_categories',
            'category_id' => 'nullable|required_if:type,category|exists:categories,id',
            'product_ids' => 'nullable|required_if:type,product|array',
            'product_ids.*' => 'integer|exists:products,id',
            'duration_weeks' => 'required|integer|min:1',
            'receipt_image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $type = $request->type;
        $durationWeeks = max(1, (int)$request->duration_weeks);
        $productIds = $request->product_ids ?? [];
        $productCount = max(1, count($productIds));

        // Pricing calculation
        if ($type === 'product') {
            $weeklyRate = 20.00 * $productCount;
        } elseif ($type === 'category') {
            $weeklyRate = 50.00;
        } else {
            $weeklyRate = 120.00;
        }

        $totalFee = $weeklyRate * $durationWeeks;

        // Upload receipt image
        $receiptPath = app(ImageKitService::class)->upload($request->file('receipt_image'));

        $promoRequest = PromotionRequest::create([
            'merchant_id' => $user->merchant_id,
            'type' => $type,
            'category_id' => $request->category_id,
            'product_ids' => $productIds,
            'duration_weeks' => $durationWeeks,
            'fee_amount' => $totalFee,
            'payment_receipt_url' => $receiptPath,
            'status' => 'pending',
        ]);

        return $this->success($promoRequest, 'تم إرسال طلب الترويج بنجاح وسيتم مراجعته من الإدارة');
    }
}
