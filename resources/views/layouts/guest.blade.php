<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="rtl"
      class="{{ session('theme', 'light') == 'dark' ? 'dark' : '' }}" 
      x-data="{ 
          darkMode: {{ session('theme', 'light') == 'dark' ? 'true' : 'false' }},
      }" 
      :class="darkMode ? 'dark' : ''">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'مجوهراتي') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Montserrat:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>

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

        <style>
            .mesh-light-gold {
                background: radial-gradient(circle at 10% 20%, rgba(245, 234, 212, 1) 0%, rgba(253, 251, 247, 1) 45%, rgba(248, 242, 228, 1) 90%);
            }
            .mesh-dark-gold {
                background: radial-gradient(circle at 10% 20%, rgba(26, 22, 16, 1) 0%, rgba(14, 12, 10, 1) 50%, rgba(10, 9, 7, 1) 90%);
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-white min-h-screen flex items-center justify-center relative overflow-x-hidden selection:bg-gold/30 selection:text-onyx transition-colors duration-500 :class="darkMode ? 'mesh-dark-gold' : 'mesh-light-gold'">

        <!-- High-End Background Mesh Glow Elements -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
             <div class="absolute top-[5%] right-[10%] w-[600px] h-[600px] bg-[#C5A059]/15 dark:bg-[#C5A059]/25 rounded-full blur-[140px] animate-pulse"></div>
             <div class="absolute bottom-[5%] left-[10%] w-[500px] h-[500px] bg-[#C5A059]/10 dark:bg-[#C5A059]/20 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2.5s;"></div>
             <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-[#E8D095]/10 dark:bg-[#C5A059]/10 rounded-full blur-[180px]"></div>
        </div>

        <!-- Top Header Navigation & Theme Toggle Button -->
        <div class="fixed top-6 left-6 z-50 flex items-center gap-4">
            <button @click="darkMode = !darkMode; toggleThemePersistent(darkMode);"
                class="relative p-3.5 rounded-2xl border border-[#C5A059]/40 bg-white/70 dark:bg-black/60 backdrop-blur-md text-[#151515] dark:text-[#E8D095] hover:bg-white dark:hover:bg-black/80 shadow-lg shadow-[#C5A059]/10 transition-all duration-300 group cursor-pointer">
                <div x-show="!darkMode" class="flex items-center gap-2">
                    <i data-lucide="moon" class="w-5 h-5 text-[#8B6914] transition-transform group-hover:-rotate-12"></i>
                    <span class="text-xs font-black text-[#151515]">الوضع الليلي</span>
                </div>
                <div x-show="darkMode" style="display: none;" class="flex items-center gap-2">
                    <i data-lucide="sun" class="w-5 h-5 text-[#E8D095] transition-transform group-hover:rotate-45"></i>
                    <span class="text-xs font-black text-[#E8D095]">الوضع النهار</span>
                </div>
            </button>
        </div>

        <div class="w-full relative z-10 px-4 py-10">
            <div class="max-w-screen-xl mx-auto flex flex-col items-center justify-center min-h-[85vh]">
                {{ $slot }}

                <!-- Global Footer -->
                <div class="mt-12 text-center">
                    <p class="text-[11px] text-[#8B6914]/70 dark:text-[#E8D095]/60 font-black uppercase tracking-[0.4em]">
                        &copy; {{ date('Y') }} {{ config('app.name', 'MOJAWHARATI') }} PRO. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>

        <script>
            lucide.createIcons();
        </script>
    </body>
</html>
