<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = \App\Models\Booking::with(['product', 'customer', 'merchant'])
            ->latest()
            ->paginate(15);
        return view('bookings.index', compact('bookings'));
    }

    public function export()
    {
        return back()->with('success', 'سيتم تجهيز ملف تقارير الحجوزات وتنزيله آلياً');
    }

    public function show($id)
    {
        $booking = \App\Models\Booking::with(['product', 'customer', 'merchant'])->findOrFail($id);
        return view('bookings.show', compact('booking'));
    }

    public function confirm($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'expires_at' => now()->addDays(2), // Extend expiry on confirmation if needed
        ]);

        return back()->with('success', 'تم تأكيد الحجز بنجاح');
    }

    public function reject(Request $request, $id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'تم رفض الحجز');
    }

    public function complete($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'تم إكمال الحجز بنجاح');
    }

    public function create()
    {
        return view('bookings.create');
    }

    public function edit($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $products = \App\Models\Product::all();
        $merchants = \App\Models\Merchant::all();
        $customers = \App\Models\User::where('role', 'customer')->get();
        return view('bookings.edit', compact('booking', 'products', 'merchants', 'customers'));
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    public function destroy($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'تم حذف الحجز بنجاح');
    }
}

