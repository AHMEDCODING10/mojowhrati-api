<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GoldPrice;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

use App\Services\GoldPriceService;

class GoldPriceController extends Controller
{
    use ApiResponse;

    public function index(GoldPriceService $service)
    {
        // Try to update if data is old (e.g., > 1 hour)
        $latest = GoldPrice::latest()->first();
        if (!$latest || $latest->updated_at->diffInHours(now()) >= 1) {
            $service->updatePricesFromApi();
        }

        $prices = GoldPrice::orderBy('purity', 'desc')->get();
        
        $ouncePrice = Setting::get('gold_ounce_price_usd', 2150.0);
        $exchangeRate = Setting::get('usd_to_yer_rate', 530); 
        $exchangeRateSar = Setting::get('usd_to_sar_rate', 3.75);

        return $this->success([
            'prices' => $prices,
            'meta' => [
                'global_ounce_price_usd' => $ouncePrice,
                'exchange_rates' => [
                    'YER' => $exchangeRate,
                    'SAR' => $exchangeRateSar,
                    'USD' => 1
                ],
                'last_updated' => $prices->first() ? $prices->first()->updated_at : null
            ]
        ]);
    }
}
