<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function getMerchantStats(Request $request)
    {
        $merchant = $request->user()->merchant;
        
        if (!$merchant) {
            // Return zeroed defaults for new/unverified merchants instead of 404
            return $this->success([
                'summary' => [
                    'total_products' => 0,
                    'total_views' => 0,
                    'total_bookings' => 0,
                    'conversion_rate' => 0,
                    'growth_percentage' => 0,
                ],
                'bookings_breakdown' => [
                    'pending' => 0,
                    'confirmed' => 0,
                    'rejected' => 0,
                    'completed' => 0,
                ],
                'chart_data' => [
                    'week' => [],
                    'month' => [],
                ],
                'top_products' => [],
            ], 'بانتظار إعداد الملف الشخصي للتاجر وعرض الإحصائيات');
        }

        $merchantId = $merchant->id;

        // 1. Product Summary
        $totalProducts = Product::where('merchant_id', $merchantId)->count();
        $totalViews = Product::where('merchant_id', $merchantId)->sum('views_count');

        // 2. Booking Summary
        $bookingsGrouped = Booking::where('merchant_id', $merchantId)
            ->select('status', DB::raw('SUM(quantity) as volume'))
            ->groupBy('status')
            ->get()
            ->pluck('volume', 'status');

        $totalBookings = Booking::where('merchant_id', $merchantId)->sum('quantity');

        // 3. Top Viewed Products
        $topProducts = Product::where('merchant_id', $merchantId)
            ->where('views_count', '>', 0)
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'views_count']);

        // 4. Performance Calculation (Simple conversion rate: Bookings / Views)
        $conversionRate = $totalViews > 0 ? ($totalBookings / $totalViews) * 100 : 0;

        // 5. Time-series Stats for Chart
        $now = Carbon::now();
        
        // Weekly (Last 7 days)
        $weeklyStats = Booking::where('merchant_id', $merchantId)
            ->where('created_at', '>=', $now->copy()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(quantity) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        $chartWeek = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $chartWeek[] = [
                'label' => $now->copy()->subDays($i)->format('D'),
                'value' => $weeklyStats->get($date, 0)
            ];
        }

        // Monthly (Last 30 days)
        $monthlyStats = Booking::where('merchant_id', $merchantId)
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(quantity) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        $chartMonth = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            // Grouping by every 3 days to keep chart clean if needed, 
            // but for now let's send daily and handle on frontend or keep simple
            $chartMonth[] = [
                'label' => $now->copy()->subDays($i)->format('d/m'),
                'value' => $monthlyStats->get($date, 0)
            ];
        }

        // 6. Growth Percentage (Comparing last 30 days to previous 30 days)
        $currentPeriodBookings = Booking::where('merchant_id', $merchantId)
            ->where('created_at', '>=', $now->copy()->subDays(30)->startOfDay())
            ->sum('quantity');
            
        $previousPeriodBookings = Booking::where('merchant_id', $merchantId)
            ->where('created_at', '>=', $now->copy()->subDays(60)->startOfDay())
            ->where('created_at', '<', $now->copy()->subDays(30)->startOfDay())
            ->sum('quantity');

        $growth = 0;
        if ($previousPeriodBookings > 0) {
            $growth = (($currentPeriodBookings - $previousPeriodBookings) / $previousPeriodBookings) * 100;
        } else if ($currentPeriodBookings > 0) {
            $growth = 100;
        }

        return $this->success([
            'summary' => [
                'total_products' => $totalProducts,
                'total_views' => $totalViews,
                'total_bookings' => $totalBookings,
                'conversion_rate' => round($conversionRate, 2),
                'growth_percentage' => round($growth, 1),
            ],
            'bookings_breakdown' => [
                'pending' => $bookingsGrouped->get('pending', 0),
                'confirmed' => $bookingsGrouped->get('confirmed', 0),
                'rejected' => $bookingsGrouped->get('rejected', 0),
                'completed' => $bookingsGrouped->get('completed', 0),
            ],
            'chart_data' => [
                'week' => $chartWeek,
                'month' => $chartMonth,
            ],
            'top_products' => $topProducts,
        ]);
    }
}
