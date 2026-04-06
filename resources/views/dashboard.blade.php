@extends('layouts.admin')

@section('title', 'نظرة عامة')

@section('content')
<div class="space-y-8 w-full pb-20 fade-in" dir="rtl">
    <!-- Premium Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-2">
        <div class="px-2">
            <h1 class="text-3xl font-black text-[#1A1A1A] dark:text-gray-100 tracking-tight mb-2" style="font-family: 'Almarai', sans-serif;">{{ __('نظرة عامة') }}</h1>
            <p class="text-[#555] dark:text-gray-400 font-bold text-sm" style="font-family: 'Almarai', sans-serif;">
                {{ __('مرحباً بك مجدداً في لوحة التحكم') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/40 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-white/20 text-xs font-bold text-secondary">
                <i data-lucide="cpu" class="w-4 h-4 text-[#C5A059]"></i>
            </div>
            <button onclick="window.location.reload()" class="bg-white border border-[#C5A059]/30 dark:bg-transparent dark:border-[#C5A059] px-6 py-2.5 rounded-xl font-black text-sm text-[#A88135] dark:text-[#E8D095] transition-all shadow-sm hover:bg-[#FCF8F2] dark:shadow-[0_0_15px_rgba(197,160,89,0.3)] select-none active:scale-95">{{ __('تحديث') }}</button>
            <a href="{{ route('reports.index') }}" class="bg-[#FCF8F2] dark:bg-gradient-to-r dark:from-[#DFB967] dark:to-[#B08535] text-[#A88135] dark:text-[#111] px-8 py-2.5 rounded-xl font-black text-sm transition-all shadow-[0_4px_15px_rgba(200,165,90,0.3)] hover:shadow-[0_6px_20px_rgba(200,165,90,0.4)] dark:shadow-[0_0_20px_rgba(197,160,89,0.4)] block select-none active:scale-95">{{ __('التقارير التفصيلية') }}</a>
        </div>
    </div>

    <!-- Stat Cards (Matching Image) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-12">
        @php
            $dashboardStats = [
                ['label' => 'المنتجات', 'value' => $stats['total_products'], 'icon' => 'gem', 'trend' => '+4.5%', 'color' => '#C5A059', 'chart_id' => 'chart1', 'link' => route('products.index')],
                ['label' => 'التجار الموثقون', 'value' => $stats['total_merchants'] , 'icon' => 'store', 'trend' => '+12%', 'color' => '#C5A059', 'chart_id' => 'chart2', 'link' => route('merchants.index')],
                ['label' => 'عدد المستخدمين', 'value' => $stats['total_users'], 'icon' => 'users', 'trend' => '+2.4%', 'color' => '#C5A059', 'chart_id' => 'chart3', 'link' => route('users.index')],
                ['label' => 'الزوار (ضيوف)', 'value' => $stats['total_visitors'], 'icon' => 'eye', 'trend' => '+8.1%', 'color' => '#C5A059', 'chart_id' => 'chart4', 'link' => '#'],
                ['label' => 'انتظار التوثيق', 'value' => $stats['pending_merchants'], 'icon' => 'clock', 'trend' => '-1.2%', 'color' => '#C5A059', 'chart_id' => 'chart5', 'link' => route('merchants.verify')],
            ];
        @endphp

        @foreach($dashboardStats as $stat)
            <a href="{{ $stat['link'] }}" class="dashboard-card p-6 md:p-8 flex flex-col justify-between shadow-[0_8px_30px_-10px_rgba(200,165,90,0.2)] hover:scale-[1.03] hover:shadow-[0_15px_40px_-5px_rgba(200,165,90,0.3)] hover:border-gold/30 group transition-all duration-300 border border-transparent">
                <div class="flex items-center justify-between mb-8">
                    <span class="text-right text-sm md:text-base font-black text-[#333] dark:text-white/60 group-hover:text-gold transition-colors" style="font-family: 'Almarai', sans-serif;">{{ $stat['label'] }}</span>
                    <div class="p-2 border-[1.5px] border-[#C5A059]/60 rounded-full flex items-center justify-center bg-transparent group-hover:bg-gold group-hover:border-gold transition-all duration-300">
                        <i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5 text-[#B08535] group-hover:text-onyx transition-colors"></i>
                    </div>
                </div>

                <div class="flex items-end justify-between w-full overflow-hidden">
                    <div class="space-y-1">
                        <h3 class="text-5xl font-black text-[#A88135] dark:text-[#E6C687]" style="font-family: 'Almarai', sans-serif;">
                            <span id="stat-{{ Str::slug($stat['label'], '-') }}">{{ $stat['value'] }}</span>
                        </h3>
                        <div class="flex items-center gap-1.5 {{ str_contains($stat['trend'], '+') ? 'text-emerald-500' : 'text-rose-500' }} text-[10px] font-black">
                            <i data-lucide="{{ str_contains($stat['trend'], '+') ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                            <span>{{ $stat['trend'] }}</span>
                        </div>
                    </div>
                    <div dir="ltr" class="w-24 h-12 overflow-hidden flex-shrink-0">
                        <div id="{{ $stat['chart_id'] }}" class="w-full h-full"></div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sales Growth Chart (Matching Image) -->
        <div class="lg:col-span-2 dashboard-card p-8 lg:p-10 relative">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-2xl font-black text-primary pb-2" style="font-family: 'Almarai', sans-serif;">{{ __('تتبع نمو الحجوزات') }}</h3>
                    <p class="text-sm text-secondary" style="font-family: 'Almarai', sans-serif;">{{ __('تتبع نمو الحجوزات خلال العام الحالي') }}</p>
                </div>
                <div class="bg-gradient-to-r from-[#DFB967] to-[#B08535] shadow-[0_8px_20px_rgba(180,140,60,0.3)] px-6 py-2 rounded-xl text-white font-black text-xs">
                    2026
                </div>
            </div>
            <div dir="ltr" class="w-full h-80 overflow-hidden">
                <div id="salesChart" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Product Distribution Chart -->
        <div class="dashboard-card p-8 lg:p-10 w-full overflow-hidden">
            <h3 class="text-2xl font-black text-primary mb-8" style="font-family: 'Almarai', sans-serif;">{{ __('توزيع المنتجات') }}</h3>
            <div dir="ltr" class="h-80 relative w-full overflow-hidden">
                <div id="distributionChart" class="w-full h-full"></div>
                <!-- Center Text Overlay for Donut -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none z-10 mt-2">
                    <span id="stat-total-donut" class="text-4xl font-black text-[#A88135]">{{ $stats['total_products'] }}</span>
                    <span class="text-[10px] font-black opacity-40 uppercase tracking-widest text-[#555] mt-1 pr-1">{{ __('إجمالي') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Feed & Table Row -->
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 mt-4">
        <!-- Activity Table -->
        <div class="xl:col-span-3 dashboard-card p-0 flex flex-col shadow-[0_8px_30px_-10px_rgba(200,165,90,0.15)]">
            <div class="p-6 border-b border-[#C5A059]/10 flex items-center justify-between">
                <h3 class="text-lg font-black text-primary" style="font-family: 'Almarai', sans-serif;">{{ __('آخر العمليات') }}</h3>
                <a href="{{ route('bookings.index') }}" class="text-[#B08535] text-xs font-black hover:underline underline-offset-4 uppercase tracking-wider">{{ __('السجل الكامل') }}</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-right border-0">
                    <thead>
                        <tr class="bg-gray-50/20 dark:bg-white/[0.02] text-secondary/50 text-[10px] font-black uppercase tracking-widest">
                            <th class="py-4 px-8 text-right">{{ __('العميل / العملية') }}</th>
                            <th class="py-4 px-8 text-center">{{ __('التاريخ') }}</th>
                            <th class="py-4 px-8 text-left">{{ __('الحالة') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($latestBookings as $booking)
                            @php
                                $statusMap = [
                                    'pending'   => ['label' => 'قيد الانتظار', 'color' => 'gold'],
                                    'confirmed' => ['label' => 'مؤكد',        'color' => 'emerald'],
                                    'approved'  => ['label' => 'مقبول',       'color' => 'emerald'],
                                    'rejected'  => ['label' => 'مرفوض',       'color' => 'rose'],
                                    'completed' => ['label' => 'مكتمل',       'color' => 'indigo'],
                                ];
                                $st = $statusMap[$booking->status] ?? $statusMap['pending'];
                            @endphp
                            <tr class="group hover:bg-gold/[0.02] transition-colors border-b border-white/5 last:border-0">
                                <td class="py-5 px-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-onyx to-gray-800 dark:from-white dark:to-gray-200 text-gold dark:text-onyx flex items-center justify-center font-black text-sm border border-white/5 shadow-sm">
                                            {{ mb_substr($booking->customer->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-primary text-sm tracking-tight">{{ $booking->customer->name ?? '---' }}</p>
                                            <p class="text-[10px] text-secondary/60 font-medium">{{ $booking->product->title ?? 'منتج غير معروف' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-8 text-center">
                                    <span class="text-xs text-secondary font-bold opacity-60">{{ $booking->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="py-5 px-8 text-left">
                                    <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                        {{ $st['color'] === 'gold' ? 'bg-gold/10 text-gold border-gold/20' : 
                                           ($st['color'] === 'emerald' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 
                                           ($st['color'] === 'rose' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 
                                           'bg-indigo-500/10 text-indigo-400 border-indigo-500/20')) }}
                                        flex items-center gap-1.5 w-max mr-auto">
                                        <span class="w-1 h-1 rounded-full bg-current"></span>
                                        {{ __($st['label']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Activity & Logs -->
        <div class="xl:col-span-2 space-y-8">
            <!-- System Stats Card -->
            <div class="dashboard-card p-6 md:p-8 bg-gradient-to-br from-[#DFB967]/10 via-transparent to-transparent shadow-[0_8px_30px_-10px_rgba(200,165,90,0.15)]">
                <h3 class="text-lg font-black text-primary mb-6 flex items-center gap-3" style="font-family: 'Almarai', sans-serif;">
                    <i data-lucide="activity" class="w-5 h-5 text-[#B08535]"></i>
                    {{ __('نشاط النظام') }}
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-white/40 dark:bg-white/5 border border-white/20">
                        <p class="text-[10px] font-black uppercase text-secondary/60 mb-1">{{ __('الحالة') }}</p>
                        <p class="text-lg font-black text-primary">{{ $systemStats['server_status'] }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/40 dark:bg-white/5 border border-white/20">
                        <p class="text-[10px] font-black uppercase text-secondary/60 mb-1">{{ __('البيئة') }}</p>
                        <p class="text-lg font-black text-primary">PHP {{ round(floatval(PHP_VERSION), 1) }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/40 dark:bg-white/5 border border-white/20">
                        <p class="text-[10px] font-black uppercase text-secondary/60 mb-1">{{ __('الذاكرة (App)') }}</p>
                        <p id="system-memory" class="text-lg font-black text-primary">{{ $systemStats['memory'] }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/40 dark:bg-white/5 border border-white/20">
                        <p class="text-[10px] font-black uppercase text-secondary/60 mb-1">{{ __('المساحة المتاحة') }}</p>
                        <p id="system-disk" class="text-lg font-black text-primary">{{ $systemStats['disk'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="dashboard-card p-6 shadow-[0_8px_30px_-10px_rgba(200,165,90,0.15)]">
                <div class="flex items-center justify-between mb-6 px-2">
                    <h3 class="text-md font-black text-primary" style="font-family: 'Almarai', sans-serif;">{{ __('الإشعارات') }}</h3>
                    <div class="w-2 h-2 rounded-full bg-red-500 shadow-lg shadow-red-500/50"></div>
                </div>
                <div class="space-y-4 max-h-[320px] overflow-y-auto custom-scrollbar pr-2 h-full">
                    @forelse($latestNotifications as $notification)
                        <div class="p-4 rounded-2xl bg-gray-50/50 dark:bg-white/5 border border-white/10 hover:border-gold/30 transition-all group flex items-start gap-4 h-max">
                            <div class="mt-1 w-2 h-2 rounded-full bg-gold animate-pulse"></div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-primary line-clamp-1 group-hover:text-gold transition-colors">{{ $notification->title }}</p>
                                <p class="text-[10px] text-secondary opacity-70 line-clamp-1">{{ $notification->message }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center opacity-40">{{ __('لا توجد تنبيهات') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const goldMain = '#C5A059';
        const greenSpark = '#4caf50';

        const sparklineData = @json($sparklineData);

        // Sparkline Options Template
        const sparklineOptions = (color, data) => ({
            series: [{ data: data }],
            chart: { type: 'area', height: 48, sparkline: { enabled: true }, animations: { enabled: true } },
            stroke: { curve: 'smooth', width: 2 },
            fill: { opacity: 0.3, gradient: { shade: 'light', type: "vertical", opacityFrom: 0.5, opacityTo: 0 } },
            colors: [color],
            tooltip: { enabled: false }
        });

        // Initialize Sparklines
        const charts = ['chart1', 'chart2', 'chart3', 'chart4', 'chart5'].map((id, index) => {
            const container = document.querySelector(`#${id}`);
            if (!container) return null;
            const c = new ApexCharts(container, sparklineOptions(greenSpark, sparklineData[index]));
            c.render();
            return c;
        }).filter(c => c !== null);

        // Main Bookings Chart (Area Chart)
        var salesOptions = {
            series: [{
                name: "{{ __('الحجوزات') }}",
                data: @json($lineChartData)
            }],
            chart: {
                height: 320,
                type: 'area',
                toolbar: { show: false },
                background: 'transparent'
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 4, colors: [goldMain] },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            grid: { show: false },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                labels: { style: { colors: isDark ? '#fff' : '#000', opacity: 0.4, fontSize: '10px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { show: false },
            markers: { size: 5, colors: [goldMain], strokeWidth: 0, hover: { size: 8 } },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        };
        const salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
        salesChart.render();

        // Distribution Donut Chart
        var distOptions = {
            series: @json($distValues),
            chart: { type: 'donut', height: 320 },
            labels: @json($distLabels),
            colors: [goldMain, '#BA9447', '#967B47', '#333333', '#8e7135', '#614d24'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '85%',
                        background: 'transparent'
                    }
                }
            },
            stroke: { show: false },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        };
        const distChart = new ApexCharts(document.querySelector("#distributionChart"), distOptions);
        distChart.render();

        // ══════════════════════════════════════════════════════
        // REAL-TIME DASHBOARD UPDATER
        // ══════════════════════════════════════════════════════
        function refreshDashboard(event) {
            console.log('Refreshing dashboard due to event:', event);
            
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                // Update Numeric Stats
                const mapping = {
                    'stat-almntjat': data.stats.total_products,
                    'stat-altjar': data.stats.total_merchants,
                    'stat-3dd-almstkhdmyn': data.stats.total_users,
                    'stat-alzuar-dywf': data.stats.total_visitors,
                    'stat-tht-alantthar': data.stats.pending_actions,
                    'stat-total-donut': data.stats.total_products,
                    'system-memory': data.systemStats.memory,
                    'system-disk': data.systemStats.disk
                };

                for (const [id, val] of Object.entries(mapping)) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.classList.add('scale-110', 'text-gold');
                        el.textContent = val;
                        setTimeout(() => el.classList.remove('scale-110', 'text-gold'), 1000);
                    }
                }

                // Update Main Chart
                salesChart.updateSeries([{ data: data.lineChartData }]);

                // Update Donut
                distChart.updateOptions({
                    series: data.distValues,
                    labels: data.distLabels
                });

                // Update Sparklines
                charts.forEach((c, i) => {
                    c.updateSeries([{ data: data.sparklineData[i] }]);
                });
            })
            .catch(err => console.error('Dashboard refresh failed:', err));
        }

        // Listen for Global App Sync Events
        if (window.Echo) {
            window.Echo.channel('public.sync')
                .listen('.AppSyncEvent', (e) => refreshDashboard(e));
        }
    });
</script>
@endpush
@endsection
