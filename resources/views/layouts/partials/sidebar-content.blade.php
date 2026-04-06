<div class="flex flex-col h-full relative overflow-hidden">
    <!-- Premium Mesh Background Layer -->
    <div class="absolute inset-0 z-0 opacity-40 dark:opacity-100 pointer-events-none transition-opacity duration-500">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#C5A059]/10 dark:bg-[#C5A059]/30 blur-[80px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-[#C5A059]/5 dark:bg-[#C5A059]/20 blur-[60px] rounded-full translate-y-1/3 -translate-x-1/4"></div>
    </div>

    <div class="flex flex-col h-full relative z-10 overflow-y-auto custom-scrollbar"
         x-data="{ init() { this.$el.scrollTop = sessionStorage.getItem('sidebarScroll') || 0; } }" 
         @scroll.debounce.100ms="sessionStorage.setItem('sidebarScroll', $el.scrollTop)">
        <!-- Premium Logo Section -->
        <div class="pt-10 pb-8 flex flex-col items-center">
            <div class="relative group mb-5">
                <!-- Outer glow ring -->
                <div class="absolute -inset-1 rounded-full bg-gradient-to-tr from-[#C5A059] via-[#E8D095] to-[#8B6914] opacity-60 dark:opacity-80 blur-sm group-hover:blur-md group-hover:opacity-90 transition-all duration-500"></div>
                <!-- Inner gold ring -->
                <div class="absolute -inset-0.5 rounded-full bg-gradient-to-br from-[#E8D095] to-[#8B6914] opacity-90"></div>
                <!-- Logo container – fully circular, no white gaps -->
                <div class="relative w-24 h-24 rounded-full overflow-hidden shadow-[0_0_30px_rgba(197,160,89,0.5)] dark:shadow-[0_0_40px_rgba(197,160,89,0.6)] transition-all duration-500 group-hover:scale-105">
                    <img src="/images/logo.jpg" alt="Logo" class="w-full h-full object-cover object-center rounded-full">
                </div>
            </div>
            
            <div class="text-center">
                <h1 class="font-black text-2xl tracking-tighter text-black dark:text-[#C5A059]" style="font-family: 'Almarai', sans-serif;">
                    MOJAWHARATI
                    <span class="block text-sm opacity-60 tracking-[0.5em] -mt-1">PRO</span>
                </h1>
                <p class="text-[10px] font-black text-black/50 dark:text-white/40 mt-2 uppercase tracking-widest" style="font-family: 'Almarai', sans-serif;">{{ __('مقر مرا الخر') }}</p>
            </div>
        </div>

        <!-- Enhanced Navigation -->
        <nav class="flex-1 px-4 space-y-1.5 mt-2">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'label' => __('الرئيسية'), 'icon' => 'layout-dashboard', 'screen' => 'dashboard'],
                    ['route' => 'categories.index', 'label' => __('الأقسام'), 'icon' => 'layers', 'screen' => 'categories'],
                    ['route' => 'banners.index', 'label' => __('إدارة الإعلانات'), 'icon' => 'image', 'screen' => 'banners'],
                    ['route' => 'merchants.index', 'label' => __('التجار'), 'icon' => 'store', 'screen' => 'merchants'],
                    ['route' => 'bookings.index', 'label' => __('الحجوزات'), 'icon' => 'calendar-check', 'screen' => 'bookings'],
                    ['route' => 'custom_designs.index', 'label' => __('طلبات التصميم الخاص'), 'icon' => 'pen-tool', 'screen' => 'custom_designs'],
                    ['route' => 'products.index', 'label' => __('إدارة المنتجات'), 'icon' => 'gem', 'screen' => 'products'],
                    ['route' => 'users.index', 'label' => __('إدارة المستخدمين'), 'icon' => 'users', 'screen' => 'users'],
                    ['route' => 'currencies.index', 'label' => __('إدارة أسعار العملات'), 'icon' => 'banknote', 'screen' => 'currencies'],
                    ['route' => 'notifications.index', 'label' => __('الإشعارات'), 'icon' => 'bell', 'screen' => 'notifications'],
                    ['route' => 'contacts.index', 'label' => __('إدارة التواصل'), 'icon' => 'phone-call', 'screen' => 'contacts'],
                    ['route' => 'gold-prices.index', 'label' => __('أسعار الذهب'), 'icon' => 'coins', 'screen' => 'gold_prices'],
                    ['route' => 'settings.index', 'label' => __('الإعدادات'), 'icon' => 'settings', 'screen' => 'settings'],
                    ['route' => 'reports.index', 'label' => __('التقارير'), 'icon' => 'bar-chart-3', 'screen' => 'reports'],
                ];

                $navItems = array_filter($navItems, function($item) {
                    return auth()->user()->hasPermission($item['screen'], 'view');
                });
            @endphp

            @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}" class="{{ $isActive ? 'nav-item-active' : 'nav-item' }}">
                    <div class="flex items-center gap-4">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                        <span class="text-xs font-black tracking-wide" style="font-family: 'Almarai', sans-serif;">
                            {{ $item['label'] }}
                        </span>
                    </div>

                    @if($isActive)
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    @endif
                </a>
            @endforeach
        </nav>

        <!-- Premium Footer -->
        <div class="px-6 pb-6 mt-8">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="relative group w-full flex items-center justify-center gap-3 py-4 rounded-[1.2rem] bg-gradient-to-tr from-white to-gray-50 dark:from-[#0E0C0A] dark:to-[#15120E] border border-rose-100 dark:border-rose-900/30 hover:border-rose-300 dark:hover:border-rose-500/50 overflow-hidden transition-all duration-500 shadow-sm hover:shadow-[0_0_20px_rgba(244,63,94,0.1)] dark:hover:shadow-[0_0_20px_rgba(244,63,94,0.15)]">
                    <!-- Subtle hover fill -->
                    <div class="absolute inset-0 bg-gradient-to-r from-rose-500/0 via-rose-500/0 to-rose-500/0 group-hover:via-rose-500/5 transition-colors duration-700"></div>
                    
                    <i data-lucide="power" class="w-4 h-4 text-rose-500 transition-all duration-500 group-hover:scale-110 group-hover:text-rose-600 dark:group-hover:text-rose-400 relative z-10"></i>
                    <span class="text-[11px] font-black uppercase tracking-[0.25em] text-[#151515] dark:text-[#E8D095]/80 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors duration-500 relative z-10">
                        {{ __('تسجيل الخروج') }}
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
