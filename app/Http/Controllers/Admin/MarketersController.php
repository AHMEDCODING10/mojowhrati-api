<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use App\Models\MarketerCommission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketersController extends Controller
{
    // ─── Index ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Marketer::withCount('commissions');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $marketers = $query->latest()->paginate(15)->withQueryString();
        return view('marketers.index', compact('marketers'));
    }

    // ─── Create ─────────────────────────────────────────────
    public function create()
    {
        return view('marketers.create');
    }

    // ─── Store ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:255',
            'phone'                   => 'required|string|max:20|unique:marketers,phone',
            'email'                   => 'nullable|email|unique:marketers,email',
            'target_type'             => 'required|in:merchant,customer,both',
            'currency'                => 'required|string|max:10',
            'commission_per_merchant' => 'nullable|numeric|min:0',
            'commission_per_customer' => 'nullable|numeric|min:0',
            'status'                  => 'required|in:active,inactive',
            'notes'                   => 'nullable|string',
        ]);

        $marketer = Marketer::create([
            'name'                    => $request->name,
            'phone'                   => $request->phone,
            'email'                   => $request->email,
            'target_type'             => $request->target_type,
            'currency'                => $request->currency,
            'commission_per_merchant' => $request->commission_per_merchant ?? 0,
            'commission_per_customer' => $request->commission_per_customer ?? 0,
            'status'                  => $request->status,
            'notes'                   => $request->notes,
        ]);

        return redirect()->route('marketers.index')
                         ->with('success', "تم إضافة المسوق «{$marketer->name}» بنجاح — رمز الإحالة: {$marketer->code}");
    }

    // ─── Show ────────────────────────────────────────────────
    public function show($id)
    {
        $marketer    = Marketer::withCount('commissions')->findOrFail($id);
        $commissions = MarketerCommission::where('marketer_id', $id)
                            ->with('user')
                            ->latest()
                            ->paginate(20);
        $users       = User::where('marketer_id', $id)->latest()->get();
        return view('marketers.show', compact('marketer', 'commissions', 'users'));
    }

    // ─── Edit ────────────────────────────────────────────────
    public function edit($id)
    {
        $marketer = Marketer::findOrFail($id);
        return view('marketers.edit', compact('marketer'));
    }

    // ─── Update ──────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $marketer = Marketer::findOrFail($id);

        $request->validate([
            'name'                    => 'required|string|max:255',
            'phone'                   => 'required|string|max:20|unique:marketers,phone,' . $id,
            'email'                   => 'nullable|email|unique:marketers,email,' . $id,
            'target_type'             => 'required|in:merchant,customer,both',
            'currency'                => 'required|string|max:10',
            'commission_per_merchant' => 'nullable|numeric|min:0',
            'commission_per_customer' => 'nullable|numeric|min:0',
            'status'                  => 'required|in:active,inactive',
            'notes'                   => 'nullable|string',
        ]);

        $marketer->update([
            'name'                    => $request->name,
            'phone'                   => $request->phone,
            'email'                   => $request->email,
            'target_type'             => $request->target_type,
            'currency'                => $request->currency,
            'commission_per_merchant' => $request->commission_per_merchant ?? 0,
            'commission_per_customer' => $request->commission_per_customer ?? 0,
            'status'                  => $request->status,
            'notes'                   => $request->notes,
        ]);

        return redirect()->route('marketers.index')->with('success', 'تم تحديث بيانات المسوق بنجاح');
    }

    // ─── Destroy ─────────────────────────────────────────────
    public function destroy($id)
    {
        $marketer = Marketer::findOrFail($id);
        $marketer->delete();
        return redirect()->route('marketers.index')->with('success', 'تم حذف المسوق بنجاح');
    }

    // ─── Toggle Status ───────────────────────────────────────
    public function toggleStatus($id)
    {
        $marketer = Marketer::findOrFail($id);
        $marketer->update(['status' => $marketer->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'تم تغيير حالة المسوق');
    }

    // ─── Mark commission as paid ─────────────────────────────
    public function markPaid(Request $request, $id)
    {
        $marketer = Marketer::findOrFail($id);
        $amount   = $marketer->commissions()->where('status', 'pending')->sum('amount');
        $marketer->commissions()->where('status', 'pending')->update(['status' => 'paid']);
        $marketer->increment('total_paid', $amount);
        return back()->with('success', 'تم تسجيل دفع العمولات المعلقة بنجاح');
    }

    // ─── Regenerate referral code ─────────────────────────────
    public function regenerateCode($id)
    {
        $marketer = Marketer::findOrFail($id);
        do {
            $code = strtoupper(Str::random(8));
        } while (Marketer::where('code', $code)->exists());
        $marketer->update(['code' => $code]);
        return back()->with('success', 'تم تجديد رمز الإحالة: ' . $code);
    }
}
