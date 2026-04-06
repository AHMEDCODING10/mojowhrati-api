<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    class="{{ session('theme', 'light') == 'dark' ? 'dark' : '' }}" x-data="{ 
    darkMode: {{ session('theme', 'light') == 'dark' ? 'true' : 'false' }},
    sidebarOpen: false,
}" :class="darkMode ? 'dark' : ''">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy"
        content="default-src * 'self' 'unsafe-inline' 'unsafe-eval' data: gap: content:">

    <title>{{ config('app.name', 'Mojawharati') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function toggleThemePersistent(isDark) {
            fetch('{{ route('settings.theme') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ theme: isDark ? 'dark' : 'light' })
            }).catch(err => console.error('Theme sync failed:', err));
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Tailwind JIT fallback for immediate styling without NPM -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { gold: '#C5A059', onyx: '#151515' },
                    fontFamily: { sans: ['Almarai', 'sans-serif'] }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .dashboard-card {
                @apply rounded-[2rem] border transition-all duration-300;
                background-color: #FFFFFF;
                border-color: rgba(197, 160, 89, 0.2);
                box-shadow: 0 10px 40px -10px rgba(197, 160, 89, 0.15);
            }
            .dark .dashboard-card {
                background: linear-gradient(180deg, #2A2A2A 0%, #1A1A1A 100%);
                border-color: rgba(255, 255, 255, 0.05);
                border-top-color: rgba(255, 255, 255, 0.1);
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
            }
            .sidebar-bg { background: linear-gradient(180deg, #EAD192 0%, #D4AE52 50%, #B08535 100%); }
            .dark .sidebar-bg { background: linear-gradient(180deg, #1C1914 0%, #15120E 100%); }
            .header-bg { background: linear-gradient(90deg, #C9A24C 0%, #DFB967 50%, #BB913D 100%); border-bottom: 1px solid rgba(255,255,255,0.2); }
            .dark .header-bg { background: linear-gradient(90deg, #1A1713 0%, #171410 100%); border-bottom: 1px solid rgba(197, 160, 89, 0.1); }
            .nav-item {
                @apply flex items-center justify-between px-6 py-4 rounded-[1.2rem] transition-all duration-300;
                color: #151515;
            }
            .nav-item:hover { background: rgba(0, 0, 0, 0.05); }
            .dark .nav-item { color: rgba(255, 255, 255, 0.7); }
            .dark .nav-item:hover { background: rgba(197, 160, 89, 0.1); color: #C5A059; }
            .nav-item-active {
                @apply flex items-center justify-between px-6 py-4 rounded-[1.2rem] transition-all duration-300;
                background-color: #151515; color: #E8D095; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }
            .dark .nav-item-active {
                background-color: #111; border: 1px solid #C5A059; color: #E8D095; box-shadow: 0 0 20px rgba(197, 160, 89, 0.25);
            }
            .card-icon-wrapper {
                @apply p-3 rounded-2xl transition-transform;
                background: rgba(197, 160, 89, 0.1); color: #C5A059;
            }
            .dark .card-icon-wrapper {
                background: transparent; border: 1px solid #C5A059; color: #C5A059;
            }
            .bg-main {
                @apply bg-[#FCF8F2] dark:bg-[#0E0C0A];
            }
            .text-gold-dark { color: #A88135; }
            .text-gold-main { color: #C5A059; }
        }
        @layer base {
            /* Standard Dark Mode Text Overrides */
            .dark body { @apply text-white/90; }
            .dark h1, .dark h2, .dark h3, .dark h4 { @apply text-white !important; }
            .dark p, .dark span, .dark label { @apply text-white/70; }
            
            /* Force out any hardcoded black/dark text */
            .dark .text-black, 
            .dark .text-[#151515], 
            .dark .text-[#1A1A1A],
            .dark .text-[#333],
            .dark .text-[#555],
            .dark .text-onyx,
            .dark .text-main { 
                @apply text-white !important; 
            }

            /* Luxury Gold Accents */
            .dark .text-gold, .dark .text-[#C5A059] { @apply text-[#E8D095] !important; }

            .text-primary { @apply text-gray-900 dark:text-gray-100; }
            .text-secondary { @apply text-gray-600 dark:text-gray-400; }
        }
    </style>
</head>

<body class="bg-main text-[#333] dark:text-white/90 antialiased h-screen overflow-hidden transition-colors duration-500"
    style="font-family: 'Almarai', sans-serif; @if(app()->getLocale() == 'ar') direction: rtl; @endif"
    x-data="{ sidebarOpen: false }">

    <div class="flex h-screen w-full overflow-hidden">
        <!-- Sidebar Navigation (Premium Redesign) -->
        <aside x-cloak
            :class="sidebarOpen ? 'translate-x-0' : (document.dir == 'rtl' ? 'translate-x-[100%]' : '-translate-x-full') + ' lg:translate-x-0'"
            class="fixed inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} z-[60] w-64 md:w-72 flex flex-col transition-all duration-500 lg:relative lg:translate-x-0 lg:flex-shrink-0 lg:h-screen shadow-2xl lg:shadow-none sidebar-bg dark:border-white/5 border-none overflow-hidden">
            @include('layouts.partials.sidebar-content')
        </aside>

        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md lg:hidden"></div>

        <!-- Main Workspace -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative">
            <!-- Global Header Redesigned -->
            <header
                class="h-20 lg:h-24 flex-shrink-0 z-40 flex items-center justify-between px-6 md:px-8 lg:px-12 header-bg shadow-lg shadow-[#BB913D]/20 dark:shadow-none">
                <!-- Search Bar -->
                <div class="flex items-center gap-4 lg:gap-6">
                    <button @click="sidebarOpen = true"
                        class="lg:hidden p-2 bg-black/5 dark:bg-white/10 rounded-lg text-black dark:text-[#E8D095] hover:bg-black/10 transition-all">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <form action="{{ route('search') }}" method="GET" class="hidden md:flex items-center gap-3 px-4 lg:px-6 py-2.5 lg:py-3 rounded-[1rem] w-64 lg:w-96 pointer-events-auto relative group transition-all duration-300
                                 bg-black/10 dark:bg-black/60
                                 border border-[#B88F3D]/40 dark:border-[#C5A059]/40
                                 hover:border-[#C5A059]/80 dark:hover:border-[#C5A059]/80
                                 shadow-[0_2px_15px_rgba(185,145,62,0.1)] dark:shadow-[0_2px_20px_rgba(197,160,89,0.15)]
                                 focus-within:border-[#C5A059] focus-within:shadow-[0_0_20px_rgba(197,160,89,0.25)]">
                        <!-- Gold icon -->
                        <i data-lucide="search"
                            class="w-4 h-4 flex-shrink-0 text-[#8B6914] dark:text-[#C5A059] transition-colors"></i>
                        <input type="text" name="q" placeholder="{{ __('بحث مخصص...') }}" class="bg-transparent border-none text-sm focus:ring-0 w-full 
                                      placeholder:text-[#8B6914]/60 dark:placeholder:text-[#E8D095]/40 
                                      font-black text-[#151515] dark:text-[#E8D095] 
                                      outline-none overflow-hidden text-right"
                            style="font-family: 'Almarai', sans-serif;">
                        <!-- Subtle gold shimmer line at bottom -->
                        <div
                            class="absolute bottom-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-[#C5A059]/40 to-transparent opacity-0 group-focus-within:opacity-100 transition-opacity duration-300 rounded-full">
                        </div>
                    </form>
                </div>

                <!-- User Profile & Notifications (Now on the Left in RTL) -->
                <div class="flex items-center gap-4 h-10">
                    <a href="{{ route('profile.edit') }}"
                        class="text-left hidden sm:block hover:opacity-80 transition-opacity">
                        <p class="text-sm font-black text-[#151515] dark:text-[#E8D095] leading-tight"
                            style="font-family: 'Almarai', sans-serif;">{{ Auth::user()->name }}</p>
                        <span
                            class="text-[10px] uppercase font-black text-black/70 dark:text-[#E8D095]/70 tracking-tighter">{{ Auth::user()->role == 'admin' ? __('المدير العام') : __('عضو') }}</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-white/40 dark:bg-black/50 flex items-center justify-center text-[#151515] dark:text-[#E8D095] border border-black/10 dark:border-[#C5A059] shadow-sm dark:shadow-[0_0_15px_rgba(197,160,89,0.3)] ml-2 transition-all hover:scale-105 active:scale-95 overflow-hidden">
                        <img src="{{ Auth::user()->profile_image_url }}" class="w-full h-full object-cover">
                    </a>
                    @php
                        $unreadCount = \App\Models\Notification::unread()->count();
                    @endphp
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode; toggleThemePersistent(darkMode);"
                        class="relative p-2.5 rounded-xl border border-black/10 dark:border-[#C5A059] bg-white/40 dark:bg-black/50 text-[#151515] dark:text-[#E8D095] hover:bg-white/60 dark:hover:bg-[#111] shadow-sm dark:shadow-[0_0_15px_rgba(197,160,89,0.3)] transition-all mr-2 group">
                        <div x-show="!darkMode">
                            <i data-lucide="moon" class="w-5 h-5 transition-transform group-hover:-rotate-12"></i>
                        </div>
                        <div x-show="darkMode" style="display: none;">
                            <i data-lucide="sun"
                                class="w-5 h-5 transition-transform group-hover:rotate-45 text-gold"></i>
                        </div>
                    </button>
                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}"
                        class="relative p-2.5 rounded-xl border border-black/10 dark:border-[#C5A059] bg-white/40 dark:bg-black/50 text-[#151515] dark:text-[#E8D095] hover:bg-white/60 dark:hover:bg-[#111] shadow-sm dark:shadow-[0_0_15px_rgba(197,160,89,0.3)] transition-all mr-2 group cursor-pointer">
                        <i data-lucide="bell" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                        @if($unreadCount > 0)
                            <span
                                class="notification-badge absolute -top-1 -right-1 w-4 h-4 bg-white dark:bg-black rounded-full border border-[#B88F3D] dark:border-[#C5A059] flex items-center justify-center text-[8px] font-black text-[#B88F3D] dark:text-[#E8D095] group-hover:animate-bounce">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </div>
            </header>

            <!-- Active View Slot (Optimized Padding) -->
            <main
                class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-12 w-full animate-in fade-in slide-in-from-bottom-4 duration-700 h-full relative z-10">
                <div class="w-full max-w-[1600px] mx-auto min-h-full">
                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                            class="mb-8 p-6 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-3xl font-black text-sm flex items-center justify-between animate-bounce">
                            <div class="flex items-center gap-3">
                                <i data-lucide="shield-alert" class="w-5 h-5"></i>
                                {{ session('error') }}
                            </div>
                            <button @click="show = false"><i data-lucide="x" class="w-4 h-4"></i></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                            class="mb-8 p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                {{ session('success') }}
                            </div>
                            <button @click="show = false"><i data-lucide="x" class="w-4 h-4"></i></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Live Gold Price Footer -->
            <footer class="flex-shrink-0 h-10 relative z-20 overflow-hidden flex items-center
                           bg-gradient-to-r from-[#C9A24C] via-[#DFB967] to-[#BB913D]
                           dark:bg-none dark:bg-[#0E0C08]
                           border-t border-white/20 dark:border-[#C5A059]/30
                           shadow-[0_-4px_20px_rgba(187,145,61,0.25)] dark:shadow-[0_-4px_30px_rgba(197,160,89,0.15)]">
                <!-- Dark mode top glow line -->
                <div
                    class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#C5A059] to-transparent dark:opacity-100 opacity-0">
                </div>
                @php
                    $gp24 = \App\Models\GoldPrice::where('purity', 24)->first();
                    $gp21 = \App\Models\GoldPrice::where('purity', 21)->first();
                    $usdEgp = \App\Models\Setting::get('usd_egp_rate', '48.50');
                @endphp
                <!-- Ticker content -->
                <div class="flex items-center gap-16 animate-marquee whitespace-nowrap px-8 w-full">
                    <span
                        class="text-[10px] font-black flex items-center gap-2.5 text-[#151515]/80 dark:text-[#E8D095]">
                        <i data-lucide="gem" class="w-3 h-3 text-[#151515]/60 dark:text-[#C5A059]"></i>
                        {{ __('أسعار الذهب المباشرة:') }}
                    </span>
                    <span
                        class="text-[10px] font-black flex items-center gap-2.5 text-[#151515]/80 dark:text-[#E8D095]">
                        <i data-lucide="trending-up" class="w-3 h-3 text-emerald-700 dark:text-emerald-400"></i>
                        24K: $<span id="gold-price-24k">{{ number_format($gp24?->price_per_gram_usd ?? 0, 2) }}</span>
                        <span class="text-emerald-700 dark:text-emerald-400" id="gold-trend-24k">(+0.12%)</span>
                    </span>
                    <span class="text-[10px] font-black text-[#151515]/70 dark:text-[#E8D095]/80">|</span>
                    <span class="text-[10px] font-black text-[#151515]/80 dark:text-[#E8D095]">
                        21K: $<span id="gold-price-21k">{{ number_format($gp21?->price_per_gram_usd ?? 0, 2) }}</span>
                    </span>
                    <span class="text-[10px] font-black text-[#151515]/70 dark:text-[#E8D095]/80">|</span>
                    <span class="text-[10px] font-black text-[#151515]/80 dark:text-[#E8D095]">
                        USD/EGP <span id="usd-egp-rate">{{ number_format($usdEgp, 2) }}</span>
                    </span>
                    <span
                        class="text-[10px] font-black flex items-center gap-2.5 text-[#151515]/60 dark:text-[#C5A059]/70">
                        <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                        {{ __('تحديث تلقائي كل 5 دقائق • المصدر: جولد برايس') }}
                    </span>
                </div>
            </footer>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>

    <!-- Real-time WebSockets with Reverb -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        (function () {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ env('REVERB_APP_KEY') }}',
                wsHost: '{{ env('REVERB_HOST') }}',
                wsPort: {{ env('REVERB_PORT', 8080) }},
                wssPort: {{ env('REVERB_PORT', 8080) }},
                forceTLS: {{ env('REVERB_SCHEME') == 'https' ? 'true' : 'false' }},
                enabledTransports: ['ws', 'wss'],
            });

            // Global Notification Badge Listener
            @auth
                window.Echo.private('App.Models.User.{{ Auth::id() }}')
                    .listen('.App\\Events\\NewNotificationEvent', (e) => {
                        console.log('New notification received:', e);

                        // Update badge count
                        const badge = document.querySelector('.notification-badge');
                        if (badge) {
                            let current = parseInt(badge.textContent || '0');
                            badge.textContent = current + 1;
                            badge.classList.remove('hidden');
                            badge.classList.add('animate-bounce');
                        } else {
                            // Create badge if not exists
                            const bell = document.querySelector('a[href*="notifications"]');
                            if (bell) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'notification-badge absolute -top-1 -right-1 w-4 h-4 bg-white dark:bg-black rounded-full border border-[#B88F3D] dark:border-[#C5A059] flex items-center justify-center text-[8px] font-black text-[#B88F3D] dark:text-[#E8D095] animate-bounce';
                                newBadge.textContent = '1';
                                bell.appendChild(newBadge);
                            }
                        }

                        // Native Browser Notification (Optional)
                        if (Notification.permission === "granted") {
                            new Notification(e.payload.title, { body: e.payload.message });
                        }
                    });

                // 🟡 Real-time Gold Price Listener
                window.Echo.channel('gold-prices')
                    .listen('.gold.updated', (e) => {
                        console.log('Gold prices update received:', e);

                        const el24 = document.getElementById('gold-price-24k');
                        const el21 = document.getElementById('gold-price-21k');
                        const elCurrency = document.getElementById('usd-egp-rate');

                        if (el24 && e.prices[24]) el24.textContent = parseFloat(e.prices[24]).toFixed(2);
                        if (el21 && e.prices[21]) el21.textContent = parseFloat(e.prices[21]).toFixed(2);
                        if (elCurrency && e.usd_egp) elCurrency.textContent = parseFloat(e.usd_egp).toFixed(2);

                        // Optional visual feedback
                        [el24, el21, elCurrency].forEach(el => {
                            if (el) {
                                el.classList.add('text-white', 'scale-110');
                                setTimeout(() => el.classList.remove('text-white', 'scale-110'), 1000);
                            }
                        });
                    });
            @endauth
        })();
    </script>
    @stack('scripts')
</body>

</html>