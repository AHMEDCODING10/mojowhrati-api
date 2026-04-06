<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldPrice;
use App\Models\Material;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;

class GoldPricesController extends Controller
{
    protected $goldPriceService;

    public function __construct(GoldPriceService $goldPriceService)
    {
        $this->goldPriceService = $goldPriceService;
    }

    public function index()
    {
        $ouncePrice = \App\Models\Setting::get('gold_ounce_price_usd', 2000);
        $exchangeRate = \App\Models\Setting::get('usd_to_yer_rate', 530);

        $carats = ['24', '22', '21', '18'];
        $prices = [];
        
        foreach ($carats as $carat) {
            $latest = GoldPrice::where('purity', $carat)->latest()->first();
            $prices[$carat . 'K'] = $latest ? round($latest->price_per_gram_usd * $exchangeRate, 0) : 0;
        }

        $lastUpdate = GoldPrice::latest()->first()?->created_at ?? now();

        return view('gold-prices.index', compact('prices', 'lastUpdate', 'ouncePrice', 'exchangeRate'));
    }

    /**
     * Trigger manual sync from GoldAPI.io
     */
    public function syncGlobal()
    {
        try {
            $this->goldPriceService->updatePricesFromApi();
            return back()->with('success', 'تم تحديث الأسعار العالمية بنجاح من GoldAPI.io');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل تحديث الأسعار: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'carat' => 'required',
            'price_usd' => 'required|numeric',
        ]);

        GoldPrice::create([
            'purity' => $request->carat,
            'price_per_gram_usd' => $request->price_usd,
        ]);

        return back()->with('success', 'تم تحديث سعر الذهب يدوياً (بالدولار)');
    }

    public function updateGlobalSettings(Request $request)
    {
        $request->validate([
            'exchange_rate' => 'required|numeric|min:1',
        ]);

        \App\Models\Setting::set('usd_to_yer_rate', $request->exchange_rate);

        return back()->with('success', 'تم تحديث سعر صرف العملة بنجاح.');
    }
}
