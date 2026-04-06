@props(['title', 'subtitle' => null])

<div class="luxury-card p-10 space-y-8 animate-in fade-in zoom-in-95 duration-700">
    @if($title || $subtitle)
        <div class="flex flex-col gap-2 border-b border-gray-100 dark:border-white/5 pb-10 mb-6">
            @if($title)
                <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter" style="font-family: 'Montserrat', 'Almarai', sans-serif;">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-[10px] text-gold uppercase font-black tracking-[0.4em]">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    <div class="w-full">
        {{ $slot }}
    </div>
</div>
