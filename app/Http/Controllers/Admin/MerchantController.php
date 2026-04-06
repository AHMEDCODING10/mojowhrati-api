<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function create()
    {
        return view('merchants.create');
    }

    public function export()
    {
        return back()->with('success', 'تم بدء تصدير بيانات التجار، سيتم إشعارك عند الانتهاء');
    }

    public function index(Request $request)
    {
        // Show ONLY verified/approved merchants in the main list
        $query = \App\Models\Merchant::with('user')->where('approved', true);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('store_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($qu) use ($request) {
                      $qu->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $merchants = $query->withCount(['products', 'bookings'])->latest()->paginate(16)->withQueryString();
        $totalActive = \App\Models\Merchant::where('approved', true)->count();
        $totalPending = \App\Models\Merchant::where('approved', false)->count();

        return view('merchants.index', compact('merchants', 'totalActive', 'totalPending'));
    }

    public function show($id)
    {
        $merchant = \App\Models\Merchant::with(['user'])->withCount(['products', 'bookings'])->findOrFail($id);
        return view('merchants.show', compact('merchant'));
    }

    public function edit($id)
    {
        $merchant = \App\Models\Merchant::findOrFail($id);
        return view('merchants.edit', compact('merchant'));
    }

    public function verify()
    {
        // Get ALL non-approved merchants
        // Each has a 'has_documents' flag: true = uploaded docs, false = registered only
        $pendingMerchants = \App\Models\Merchant::with('user')
            ->where('approved', false)
            ->latest()
            ->get()
            ->map(function ($merchant) {
                $docs = [];
                if ($merchant->documents) {
                    if (is_string($merchant->documents)) {
                        $decoded = json_decode($merchant->documents, true);
                        $docs = is_array($decoded) ? $decoded : [];
                    } elseif (is_array($merchant->documents)) {
                        $docs = $merchant->documents;
                    }
                }
                $merchant->has_documents = count($docs) > 0;
                $merchant->documents_count = count($docs);
                return $merchant;
            });

        // Split into two groups for display
        $withDocs = $pendingMerchants->filter(fn($m) => $m->has_documents)->values();
        $withoutDocs = $pendingMerchants->filter(fn($m) => !$m->has_documents)->values();

        return view('merchants.verify', compact('pendingMerchants', 'withDocs', 'withoutDocs'));
    }

    public function approve($id)
    {
        $merchant = \App\Models\Merchant::with('user')->findOrFail($id);
        $merchant->update([
            'approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'store_status' => 'active'
        ]);

        if ($merchant->user) {
            \App\Models\Notification::create([
                'user_id' => $merchant->user_id,
                'notifiable_id' => $merchant->user_id,
                'notifiable_type' => \App\Models\User::class,
                'type' => 'merchant_approved',
                'title' => 'تم توثيق متجرك بنجاح',
                'message' => 'مبارك! تم تفعيل حسابك كتاجر في منصة مجوهراتي.',
                'data' => json_encode(['link' => '/merchant/profile']),
            ]);
        }

        return back()->with('success', 'تمت الموافقة على التاجر بنجاح وتنبيهه');
    }

    public function reject(Request $request, $id)
    {
        $merchant = \App\Models\Merchant::with('user')->findOrFail($id);
        $merchant->update([
            'approved' => false,
            'approval_notes' => $request->notes,
            'store_status' => 'suspended'
        ]);

        if ($merchant->user) {
            \App\Models\Notification::create([
                'user_id' => $merchant->user_id,
                'notifiable_id' => $merchant->user_id,
                'notifiable_type' => \App\Models\User::class,
                'type' => 'booking_rejected', // Using existing enum type
                'title' => 'تم رفض طلب التوثيق',
                'message' => 'عذراً، تم رفض طلب التوثيق الخاص بك. ملاحظات: ' . ($request->notes ?? 'يرجى مراجعة البيانات.'),
                'data' => json_encode(['link' => '/merchant/profile', 'notes' => $request->notes]),
            ]);
        }

        return back()->with('success', 'تم رفض طلب التاجر وإرسال السبب له');
    }

    public function unapprove(Request $request, $id)
    {
        $merchant = \App\Models\Merchant::with('user')->findOrFail($id);
        
        $merchant->update([
            'approved' => false,
            'approval_notes' => $request->notes,
            'store_status' => 'suspended'
        ]);

        if ($merchant->user) {
            \App\Models\Notification::create([
                'user_id' => $merchant->user_id,
                'notifiable_id' => $merchant->user_id,
                'notifiable_type' => \App\Models\User::class,
                'type' => 'booking_rejected',
                'title' => 'تم إلغاء توثيق متجرك',
                'message' => 'عذراً، تم إلغاء توثيق متجرك من قبل الإدارة. السبب: ' . ($request->notes ?? 'يرجى مراجعة البيانات ورفعها مرة أخرى.'),
                'data' => json_encode(['link' => '/merchant/profile', 'notes' => $request->notes]),
            ]);
        }

        return redirect()->route('merchants.index')->with('success', 'تم إلغاء توثيق التاجر بنجاح وتنبيهه');
    }

    public function destroy($id)
    {
        $merchant = \App\Models\Merchant::findOrFail($id);

        // 🔴 Check for associated products
        if ($merchant->products()->count() > 0) {
            return redirect()->back()->with('error', __('لا يمكن حذف التاجر لوجود منتجات مرتبطة به. يرجى حذف المنتجات أولاً.'));
        }

        // 🔴 Check for associated bookings
        if ($merchant->bookings()->count() > 0) {
            return redirect()->back()->with('error', __('لا يمكن حذف التاجر لوجود حجوزات مرتبطة به. يرجى مراجعة الحجوزات أولاً.'));
        }

        $merchant->delete();
        return redirect()->route('merchants.index')->with('success', 'تم حذف التاجر بنجاح');
    }
}

