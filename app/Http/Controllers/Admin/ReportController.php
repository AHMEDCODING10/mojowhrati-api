<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Sales Data (Last 6 Months)
        $months = collect(range(0, 5))->reverse()->map(function($i) {
            return now()->subMonths($i)->format('M');
        });

        $salesData = [
            'labels' => $months->values()->toArray(),
            'data' => $months->map(function($month, $i) {
                return \App\Models\Booking::whereMonth('created_at', now()->subMonths(5-$i)->month)
                    ->whereYear('created_at', now()->subMonths(5-$i)->year)
                    ->where('status', 'completed')
                    ->sum('total_price');
            })->toArray()
        ];

        // 2. User Growth (Last 4 Weeks)
        $userGrowth = [
            'labels' => ['الأسبوع 4', 'الأسبوع 3', 'الأسبوع 2', 'الأسبوع الحالي'],
            'new' => collect(range(0, 3))->reverse()->map(function($i) {
                return \App\Models\User::whereBetween('created_at', [now()->subWeeks($i+1), now()->subWeeks($i)])->count();
            })->toArray(),
            'returning' => [0, 0, 0, 0] // Static for now as we don't track sessions deeply
        ];

        // 3. Merchant Performance (Real Top 5)
        $merchantPerformance = \App\Models\Merchant::withCount(['bookings' => function($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['bookings' => function($q) {
                $q->where('status', 'completed');
            }], 'total_price')
            ->orderBy('bookings_sum_total_price', 'desc')
            ->take(5)
            ->get()
            ->map(function($m) {
                return [
                    'name' => $m->store_name,
                    'sales' => $m->bookings_count,
                    'revenue' => $m->bookings_sum_total_price ?? 0,
                    'rating' => 5.0 // Placeholder until review system is re-implemented
                ];
            });

        // 4. Top Products (By Views or Sales)
        $topProducts = \App\Models\Product::orderBy('views_count', 'desc')
            ->take(5)
            ->get(['title as name', 'views_count as views', 'id'])
            ->map(function($p) {
                $p['sales'] = \App\Models\Booking::where('product_id', $p->id)->where('status', 'completed')->count();
                return $p;
            });

        // 5. Revenue Stats
        $totalRevenue = \App\Models\Booking::where('status', 'completed')->sum('total_price');
        $prevMonthRevenue = \App\Models\Booking::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_price');
        
        $growth = $prevMonthRevenue > 0 ? (($totalRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100 : 100;

        $revenueStats = [
            'total' => $totalRevenue,
            'avg_order' => \App\Models\Booking::where('status', 'completed')->avg('total_price') ?? 0,
            'growth' => round($growth, 1)
        ];

        return view('reports.index', compact(
            'salesData', 
            'userGrowth', 
            'merchantPerformance', 
            'topProducts', 
            'revenueStats'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'data_source' => 'required|in:users,merchants,products,bookings,banners',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'required|in:summary,detailed',
            'action_type' => 'required|in:print,excel'
        ]);

        $source = $request->input('data_source');
        $from = $request->input('date_from');
        $to = $request->input('date_to') ? \Carbon\Carbon::parse($request->input('date_to'))->endOfDay() : null;
        $format = $request->input('format');
        $action = $request->input('action_type');

        // Fetch Data
        $data = $this->fetchReportData($source, $from, $to, $format);

        if ($action === 'excel') {
            return $this->exportToExcel($data, $source, $format);
        }

        // Print action
        return view('reports.print', [
            'data' => $data,
            'source' => $source,
            'format' => $format,
            'from' => $from,
            'to' => $request->input('date_to'), // keep original for display
            'title' => $this->getReportTitle($source)
        ]);
    }

    private function fetchReportData($source, $from, $to, $format)
    {
        $query = match($source) {
            'users' => \App\Models\User::query(),
            'merchants' => \App\Models\Merchant::query(),
            'products' => \App\Models\Product::query()->with('merchant'),
            'bookings' => \App\Models\Booking::query()->with(['user', 'merchant']),
            'banners' => \App\Models\Banner::query(),
        };

        if ($from) $query->where('created_at', '>=', $from);
        if ($to) $query->where('created_at', '<=', $to);

        if ($format === 'summary') {
            return $this->getSummaryData($query, $source);
        }

        return $query->latest()->get();
    }

    private function getSummaryData($query, $source)
    {
        if ($source === 'users') {
             return [
                 ['label' => __('إجمالي المستخدمين'), 'value' => $query->count()],
                 ['label' => __('العملاء النشطين'), 'value' => (clone $query)->where('status', 'active')->count()],
             ];
        }
        if ($source === 'merchants') {
             return [
                 ['label' => __('إجمالي التجار'), 'value' => $query->count()],
                 ['label' => __('المتاجر المعتمدة'), 'value' => (clone $query)->where('status', 'active')->count()],
             ];
        }
        if ($source === 'products') {
             return [
                 ['label' => __('إجمالي المنتجات'), 'value' => $query->count()],
                 ['label' => __('المنتجات المتاحة'), 'value' => (clone $query)->where('status', 'active')->count()],
             ];
        }
        if ($source === 'bookings') {
             return [
                 ['label' => __('إجمالي الطلبات/الحجوزات'), 'value' => $query->count()],
                 ['label' => __('الطلبات المكتملة'), 'value' => (clone $query)->where('status', 'completed')->count()],
                 ['label' => __('إجمالي الإيرادات'), 'value' => '$' . number_format((clone $query)->where('status', 'completed')->sum('total_price'), 2)],
             ];
        }
        if ($source === 'banners') {
             return [
                 ['label' => __('إجمالي الإعلانات'), 'value' => $query->count()],
                 ['label' => __('النشطة حالياً'), 'value' => (clone $query)->where('is_active', true)->count()],
             ];
        }
        return [];
    }

    private function getReportTitle($source)
    {
        return match($source) {
            'users' => __('تقرير المستخدمين والعملاء'),
            'merchants' => __('تقرير المتاجر والتجار'),
            'products' => __('تقرير المنتجات والمخزون'),
            'bookings' => __('تقرير المبيعات والحجوزات'),
            'banners' => __('تقرير الحملات الإعلانية'),
            default => __('تقرير النظام')
        };
    }

    private function exportToExcel($data, $source, $format)
    {
        $filename = "mojawharati_report_{$source}_" . date('Y_m_d_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data, $source, $format) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            if ($format === 'summary') {
                fputcsv($file, ['المؤشر', 'القيمة']);
                foreach ($data as $row) {
                    fputcsv($file, [$row['label'], $row['value']]);
                }
            } else {
                if ($source === 'users') {
                    fputcsv($file, ['الاسم', 'البريد', 'رقم الهاتف', 'الدور', 'الحالة', 'تاريخ التسجيل']);
                    foreach ($data as $item) {
                        fputcsv($file, [$item->name ?? ($item->first_name . ' ' . $item->last_name), $item->email, $item->phone, $item->role, $item->status, $item->created_at->format('Y-m-d')]);
                    }
                } elseif ($source === 'merchants') {
                    fputcsv($file, ['اسم المتجر', 'رقم الهاتف', 'الحالة', 'تاريخ الانضمام']);
                    foreach ($data as $item) {
                        fputcsv($file, [$item->store_name, $item->phone, $item->status, $item->created_at->format('Y-m-d')]);
                    }
                } elseif ($source === 'products') {
                    fputcsv($file, ['المنتج', 'التاجر', 'السعر', 'العيار', 'الوزن', 'الحالة']);
                    foreach ($data as $item) {
                        fputcsv($file, [$item->title, $item->merchant->store_name ?? '-', $item->price, $item->gold_karat, $item->weight, $item->status]);
                    }
                } elseif ($source === 'bookings') {
                    fputcsv($file, ['رقم الطلب', 'العميل', 'التاجر', 'الإجمالي', 'الحالة', 'تاريخ الطلب']);
                    foreach ($data as $item) {
                        fputcsv($file, ['#'.$item->id, $item->user->name ?? '-', $item->merchant->store_name ?? '-', $item->total_price, $item->status, $item->created_at->format('Y-m-d')]);
                    }
                } elseif ($source === 'banners') {
                    fputcsv($file, ['موقع الإعلان', 'الرابط', 'الحالة', 'النقرات']);
                    foreach ($data as $item) {
                        fputcsv($file, [$item->position, $item->link, $item->is_active ? 'نشط' : 'غير نشط', $item->clicks_count]);
                    }
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
