<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomDesignOrder;

class CustomDesignOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomDesignOrder::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $designs = $query->latest()->paginate(15);
        return view('custom_designs.index', compact('designs'));
    }

    public function show($id)
    {
        $design = CustomDesignOrder::with('user')->findOrFail($id);
        return view('custom_designs.show', compact('design'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = CustomDesignOrder::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    public function destroy($id)
    {
        $order = CustomDesignOrder::findOrFail($id);
        $order->delete();
        
        return redirect()->route('custom_designs.index')->with('success', 'تم حذف طلب التصميم بنجاح');
    }
}
