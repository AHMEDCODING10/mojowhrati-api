<?php

namespace App\Services;

use App\Models\GoldPrice;
use App\Models\Setting;
use App\Models\Material;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    /**
     * Fetch global gold price and update internal rates.
     */
    public function updatePricesFromApi()
    {
        $apiKey = config('services.gold_api.key');
        if (!$apiKey) {
            Log::warning('Gold API Key not found in configuration.');
            return false;
        }

        try {
            // Using GoldAPI.io for precise real-time data
            $response = Http::withHeaders([
                'x-access-token' => $apiKey,
                'Content-Type' => 'application/json'
            ])->get("https://www.goldapi.io/api/XAU/USD");

            if ($response->successful()) {
                $data = $response->json();
                
                $ouncePriceUsd = $data['price'] ?? null;
                
                if ($ouncePriceUsd) {
                    // Update global settings
                    Setting::set('gold_ounce_price_usd', round($ouncePriceUsd, 2), 'gold');
                    
                    // Use their direct gram prices for maximum accuracy
                    $prices = [
                        24 => $data['price_gram_24k'],
                        22 => $data['price_gram_22k'],
                        21 => $data['price_gram_21k'],
                        18 => $data['price_gram_18k'],
                    ];

                    // 🟢 Fetch USD/EGP also
                    $usdEgp = null;
                    $currencyResponse = Http::withHeaders([
                        'x-access-token' => $apiKey,
                        'Content-Type' => 'application/json'
                    ])->get("https://www.goldapi.io/api/USD/EGP");

                    if ($currencyResponse->successful()) {
                        $currencyData = $currencyResponse->json();
                        $usdEgp = $currencyData['price'] ?? null;
                        if ($usdEgp) {
                            Setting::set('usd_egp_rate', round($usdEgp, 2), 'gold');
                        }
                    }

                    $this->recalculateCaratPrices($ouncePriceUsd, $prices);
                    
                    // 🔴 Broadcast real-time update to all clients
                    broadcast(new \App\Events\GoldPriceUpdatedEvent($prices, $ouncePriceUsd, $usdEgp));
                    
                    return true;
                }
            }
            
            Log::error('Gold API Error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Gold Price Service Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate gram prices for all supported purities.
     */
    public function recalculateCaratPrices($ouncePriceUsd, $forcedPrices = [])
    {
        $gramPrice24K = $ouncePriceUsd / 31.1034768;
        
        $purities = [
            24 => 1.0,
            22 => 22/24,
            21 => 21/24,
            18 => 18/24
        ];

        foreach ($purities as $purity => $ratio) {
            $pricePerGram = isset($forcedPrices[$purity]) ? $forcedPrices[$purity] : ($gramPrice24K * $ratio);
            
            // Update GoldPrice model (for live bar)
            GoldPrice::updateOrCreate(
                ['purity' => $purity],
                [
                    'price_per_gram_usd' => round($pricePerGram, 4),
                    'currency_code' => 'USD',
                    'source' => 'GoldAPI.io',
                    'last_updated' => now(),
                    'is_active' => true,
                ]
            );

            // Update Material model (for product pricing)
            $materialName = "ذهب عيار $purity";
            Material::where('name', 'LIKE', "%$purity%")->update([
                'current_rate' => round($pricePerGram, 4)
            ]);
        }
    }
}
