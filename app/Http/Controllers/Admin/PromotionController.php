<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Merchant;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $type = $request->query('type');

        $query = Promotion::with(['merchant', 'category', 'product']);

        if ($status) {
            if ($status === 'expired') {
                $query->where('end_at', '<', now());
            } else {
                $query->where('status', $status);
            }
        }

        if ($type) {
            $query->where('type', $type);
        }

        $promotions = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_promotions' => Promotion::count(),
            'active_promotions' => Promotion::active()->count(),
            'total_revenue' => Promotion::where('payment_status', 'paid')->sum('fee_amount'),
            'expired_promotions' => Promotion::where('end_at', '<', now())->count(),
        ];

        return view('promotions.index', compact('promotions', 'stats'));
    }

    public function create()
    {
        $merchants = Merchant::where('status', 'active')->orderBy('store_name')->get();
        $categories = Category::orderBy('name')->get();
        $products = Product::where('status', 'approved')->orderBy('title')->get();

        return view('promotions.create', compact('merchants', 'categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'merchant_id' => 'nullable|exists:merchants,id',
            'type' => 'required|string|in:category,product,merchant_store,banner',
            'target_id' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'placement' => 'required|string|in:HOME_TOP,CATEGORY_TOP,SEARCH_BOOST,FEATURED_SLIDER',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'priority' => 'required|integer|min:0',
            'fee_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|string|in:paid,unpaid,waived',
            'notes' => 'nullable|string',
        ]);

        Promotion::create([
            'merchant_id' => $request->merchant_id,
            'type' => $request->type,
            'target_id' => $request->target_id,
            'title' => $request->title,
            'placement' => $request->placement,
            'start_at' => $request->start_at ?? now(),
            'end_at' => $request->end_at,
            'priority' => $request->priority,
            'fee_amount' => $request->fee_amount,
            'payment_status' => $request->payment_status,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        return redirect()->route('promotions.index')->with('success', 'تم إضافة الحملة الترويجية بنجاح');
    }

    public function edit(Promotion $promotion)
    {
        $merchants = Merchant::where('status', 'active')->orderBy('store_name')->get();
        $categories = Category::orderBy('name')->get();
        $products = Product::where('status', 'approved')->orderBy('title')->get();

        return view('promotions.edit', compact('promotion', 'merchants', 'categories', 'products'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'merchant_id' => 'nullable|exists:merchants,id',
            'type' => 'required|string|in:category,product,merchant_store,banner',
            'target_id' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'placement' => 'required|string|in:HOME_TOP,CATEGORY_TOP,SEARCH_BOOST,FEATURED_SLIDER',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'priority' => 'required|integer|min:0',
            'fee_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|string|in:paid,unpaid,waived',
            'status' => 'required|string|in:active,paused,expired',
            'notes' => 'nullable|string',
        ]);

        $promotion->update([
            'merchant_id' => $request->merchant_id,
            'type' => $request->type,
            'target_id' => $request->target_id,
            'title' => $request->title,
            'placement' => $request->placement,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'priority' => $request->priority,
            'fee_amount' => $request->fee_amount,
            'payment_status' => $request->payment_status,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('promotions.index')->with('success', 'تم تحديث بيانات الترويج بنجاح');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()->route('promotions.index')->with('success', 'تم حذف الحملة الترويجية بنجاح');
    }

    public function toggleStatus(Promotion $promotion)
    {
        $newStatus = $promotion->status === 'active' ? 'paused' : 'active';
        $promotion->update(['status' => $newStatus]);

        return back()->with('success', 'تم تغيير حالة الحملة الترويجية بنجاح');
    }
}
