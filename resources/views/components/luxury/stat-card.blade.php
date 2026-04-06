@props(['label', 'value', 'trend' => null, 'trendUp' => true, 'icon' => null, 'color' => 'gold'])

<div class="luxury-card p-10 flex items-center justify-between group transition-all duration-700 w-full overflow-hidden relative border border-gray-100 dark:border-white/5 hover:scale-[1.02] active:scale-95">
    <div class="relative z-10 space-y-4">
        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500" style="font-family: 'Montserrat', sans-serif;">{{ $label }}</p>
        <div class="flex items-baseline gap-5">
            <h4 class="text-3xl font-black transition-colors tracking-tight text-gray-900 dark:text-white" style="font-family: 'Montserrat', sans-serif;">{{ $value }}</h4>
            @if($trend)
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-50 dark:bg-white/5 {{ $trendUp ? 'text-emerald-500' : 'text-rose-500' }} border border-gray-100 dark:border-white/5 shadow-sm text-[9px] font-black uppercase tracking-widest">
                    <i data-lucide="{{ $trendUp ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                    <span>{{ $trend }}</span>
                </div>
            @endif
        </div>
    </div>
    
    @if($icon)
        <div class="relative z-10 w-16 h-16 rounded-[22px] flex items-center justify-center transition-all duration-700 shadow-xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 text-gold group-hover:rotate-3 group-hover:scale-105">
            <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
        </div>
    @endif
    
    <!-- Premium Dynamic Glow -->
    <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-gold/5 blur-[100px] rounded-full group-hover:bg-gold/15 transition-all duration-1000"></div>
</div>
