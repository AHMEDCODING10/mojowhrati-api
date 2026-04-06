<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
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
        
        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body class="font-sans antialiased text-gray-900 min-h-screen flex items-center justify-center relative overflow-hidden selection:bg-gold/30 selection:text-onyx {{ session('theme', 'dark') == 'dark' ? 'mesh-dark' : 'mesh-light' }}">

        <!-- High-End Background Elements (Triple Layer) -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
             <div class="absolute top-[10%] right-[15%] w-[600px] h-[600px] bg-gold/5 rounded-full blur-[150px] animate-pulse"></div>
             <div class="absolute bottom-[10%] left-[15%] w-[500px] h-[500px] bg-gold/5 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
             <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gold/3 rounded-full blur-[180px]"></div>
        </div>

        <div class="w-full relative z-10 px-4 py-12">
            <div class="max-w-screen-xl mx-auto flex flex-col items-center justify-center min-h-[80vh]">
                {{ $slot }}

                <!-- Global Footer -->
                <div class="mt-16 text-center">
                    <p class="text-[10px] text-gray-400 dark:text-gray-600 font-black uppercase tracking-[0.5em]">
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
