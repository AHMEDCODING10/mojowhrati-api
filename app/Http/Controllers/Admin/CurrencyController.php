<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $usd_to_yer = Setting::get('usd_to_yer', 530);
        $sar_to_yer = Setting::get('sar_to_yer', 140);
        $usd_to_sar = Setting::get('usd_to_sar', 3.75);

        return view('admin.currencies.index', compact('usd_to_yer', 'sar_to_yer', 'usd_to_sar'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'usd_to_yer' => 'required|numeric|min:0',
            'sar_to_yer' => 'required|numeric|min:0',
            'usd_to_sar' => 'required|numeric|min:0',
        ]);

        Setting::set('usd_to_yer', $request->usd_to_yer, 'currency');
        Setting::set('sar_to_yer', $request->sar_to_yer, 'currency');
        Setting::set('usd_to_sar', $request->usd_to_sar, 'currency');

        // Also update the general rate used in GoldPricesController for compatibility
        Setting::set('usd_to_yer_rate', $request->usd_to_yer, 'currency');

        return back()->with('success', 'تم تحديث أسعار العملات بنجاح.');
    }
}
