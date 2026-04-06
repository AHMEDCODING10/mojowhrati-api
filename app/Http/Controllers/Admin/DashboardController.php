<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => \App\Models\User::where('role', '!=', 'merchant')->count(),
            'total_merchants' => \App\Models\Merchant::where('approved', true)->count(), // Verified only
            'pending_merchants' => \App\Models\Merchant::where('approved', false)->count(), // Awaiting verification
            'total_products' => \App\Models\Product::count(),
            'total_visitors' => \App\Models\Visitor::where('last_active_at', '>=', now()->subMinutes(5))->count(),
            'pending_actions' => \App\Models\Merchant::where('approved', false)->count() + 
                                \App\Models\Product::where('status', 'pending')->count(),
        ];

        // 1. Monthly Bookings (Line Chart)
        $driver = \DB::getDriverName();
        $monthField = $driver === 'sqlite' ? "strftime('%m', created_at)" : "MONTH(created_at)";
        
        $monthlyBookings = \App\Models\Booking::selectRaw("COUNT(*) as count, $monthField as month")
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        $lineChartData = array_fill(1, 12, 0);
        foreach ($monthlyBookings as $month => $count) {
            $monthIndex = (int)$month;
            if ($monthIndex >= 1 && $monthIndex <= 12) {
                $lineChartData[$monthIndex] = $count;
            }
        }
        
        // Convert to zero-indexed array for JS
        $lineChartData = array_values($lineChartData);

        // 2. Products by Category (Donut Chart)
        $categoryDistribution = \App\Models\Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, COUNT(*) as count')
            ->groupBy('category_name')
            ->get();
        
        $distLabels = $categoryDistribution->pluck('category_name')->toArray();
        $distValues = $categoryDistribution->pluck('count')->toArray();

        // 3. User Activity (Placeholder Sparklines - 7 days)
        $sparklineData = [];
        for ($i = 0; $i < 5; $i++) {
            $sparklineData[] = [rand(10, 50), rand(10, 50), rand(10, 50), rand(10, 50), rand(10, 50), rand(10, 50), rand(10, 50)];
        }

        // 4. System Stats
        $systemStats = [
            'memory' => round(memory_get_usage(true) / 1024 / 1024, 1) . ' MB',
            'disk' => round(disk_free_space(base_path()) / 1024 / 1024 / 1024, 1) . ' GB',
            'php_version' => PHP_VERSION,
            'server_status' => 'متصل'
        ];

        // Tables
        $latestBookings = \App\Models\Booking::with(['product', 'customer'])->latest()->take(5)->get();
        $latestProducts = \App\Models\Product::with('merchant')->latest()->take(5)->get();
        $latestNotifications = \App\Models\Notification::latest()->take(5)->get();

        if (request()->ajax()) {
            return response()->json([
                'stats' => $stats,
                'lineChartData' => $lineChartData,
                'distLabels' => $distLabels,
                'distValues' => $distValues,
                'sparklineData' => $sparklineData,
                'systemStats' => $systemStats,
            ]);
        }

        return view('dashboard', compact(
            'stats', 
            'lineChartData', 
            'distLabels',
            'distValues',
            'sparklineData',
            'systemStats',
            'latestBookings',
            'latestProducts',
            'latestNotifications'
        ));
    }
}

