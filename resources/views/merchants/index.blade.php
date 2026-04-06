@extends('layouts.admin')

@section('title', __('إدارة التجار'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 py-4">
        <div>
            <h2 class="text-5xl font-black text-main uppercase tracking-[0.2em] mb-4">{{ __('شبكة التجار') }}</h2>
            <div class="h-1.5 w-32 bg-gradient-to-r from-gold to-transparent shadow-[0_0_20px_rgba(197,160,89,0.3)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-3 px-6 py-3 bg-white/40 dark:bg-black/20 border border-gold/10 rounded-2xl">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase text-muted/60 tracking-widest">{{ $totalActive }} {{ __('تاجر موثق') }}</span>
            </div>
            <a href="{{ route('merchants.verify') }}" class="px-10 py-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-emerald-500 hover:text-white transition-all duration-300 flex items-center gap-4 group shadow-xl hover:shadow-emerald-500/20">
                <i data-lucide="shield-check" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                {{ __('طلبات التحقق') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center animate-in fade-in slide-in-from-top-4 duration-700">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="p-6 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-3xl font-black text-sm text-center animate-in fade-in slide-in-from-top-4 duration-700">
        {{ session('error') }}
    </div>
    @endif

    <!-- Merchants Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10" id="merchants-grid">
        @forelse($merchants as $merchant)
            <div class="luxury-card p-0 overflow-hidden group border border-gold/10 hover:border-gold/30 transition-all duration-700 relative bg-gradient-to-br from-white to-[#FCF8F2] dark:from-[#2A2A2A] dark:to-[#1A1A1A] hover:shadow-[0_30px_60px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_30px_60px_rgba(0,0,0,0.4)] hover:-translate-y-2">
                
                <!-- Cover/Hero Section -->
                <div class="h-40 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-gold/10 via-gold/5 to-transparent group-hover:scale-110 transition-transform duration-700"></div>
                    <div class="absolute top-4 left-4 z-10">
                        @if($merchant->approved)
                            <div class="px-4 py-2 bg-emerald-500/10 backdrop-blur-md border border-emerald-500/20 text-emerald-500 rounded-full text-[8px] font-black uppercase tracking-widest flex items-center gap-2 shadow-sm">
                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                {{ __('موثق') }}
                            </div>
                        @else
                            <div class="px-4 py-2 bg-amber-500/10 backdrop-blur-md border border-amber-500/20 text-amber-500 rounded-full text-[8px] font-black uppercase tracking-widest flex items-center gap-2 shadow-sm">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                {{ __('قيد المراجعة') }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Premium Squared Logo (Full bleed design) -->
                    <div class="absolute -bottom-10 right-10 w-24 h-24 rounded-3xl bg-white dark:bg-[#121212] border-4 border-white dark:border-gold/10 flex items-center justify-center text-4xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] overflow-hidden group-hover:scale-105 transition-all duration-500">
                        @if($merchant->logo_url)
                            <img src="{{ $merchant->logo_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gold/5 flex items-center justify-center text-gold/30">
                                <i data-lucide="store" class="w-10 h-10"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="p-10 pt-16 space-y-8">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-2xl font-black text-main group-hover:text-gold transition-colors truncate tracking-tight">{{ $merchant->store_name }}</h4>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-muted/40"></i>
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-[0.1em]">{{ $merchant->user->name ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Live Stats Row -->
                    <div class="grid grid-cols-2 gap-4 py-6 border-y border-gold/5 relative">
                        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-px h-10 bg-gradient-to-b from-transparent via-gold/10 to-transparent"></div>
                        <div class="space-y-1 text-center group/stat">
                            <p class="text-[9px] text-muted/30 font-black uppercase tracking-widest group-hover/stat:text-gold transition-colors">{{ __('المنتجات') }}</p>
                            <div class="flex items-center justify-center gap-3">
                                <i data-lucide="package" class="w-4 h-4 text-emerald-500/50"></i>
                                <p class="text-2xl font-black text-main animate-live-count">{{ $merchant->products_count ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="space-y-1 text-center group/stat">
                            <p class="text-[9px] text-muted/30 font-black uppercase tracking-widest group-hover/stat:text-gold transition-colors">{{ __('الحجوزات') }}</p>
                            <div class="flex items-center justify-center gap-3">
                                <i data-lucide="calendar-check" class="w-4 h-4 text-gold/50"></i>
                                <p class="text-2xl font-black text-main animate-live-count">{{ $merchant->bookings_count ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex items-center gap-4 pt-4">
                        <a href="{{ route('merchants.show', $merchant->id) }}" class="flex-1 py-4 bg-white/50 dark:bg-black/20 border border-gold/10 rounded-2xl text-main text-[10px] font-black uppercase tracking-widest text-center hover:bg-gold hover:text-onyx transition-all duration-500 flex items-center justify-center gap-3 group/btn overflow-hidden relative">
                            <div class="absolute inset-0 bg-gold translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                            <i data-lucide="eye" class="w-4 h-4 relative z-10 group-hover/btn:rotate-6 transition-transform"></i>
                            <span class="relative z-10">{{ __('التفاصيل الكاملة') }}</span>
                        </a>
                        <form action="{{ route('merchants.destroy', $merchant->id) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('{{ __('حظر التاجر؟') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-4 bg-rose-500/5 border border-rose-500/10 rounded-2xl text-rose-500/40 hover:bg-rose-500 hover:text-white transition-all duration-300 shadow-sm hover:shadow-rose-500/20">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full luxury-card p-32 text-center space-y-10 border border-dashed border-gold/20 bg-gold/5 rounded-[4rem]">
                <div class="w-32 h-32 bg-gold/10 rounded-full flex items-center justify-center mx-auto text-gold border border-gold/20 shadow-2xl animate-bounce">
                    <i data-lucide="store" class="w-16 h-16"></i>
                </div>
                <div class="space-y-4">
                    <h3 class="text-4xl font-black text-main tracking-tight">{{ __('لا يوجد تجار!') }}</h3>
                    <p class="text-muted/40 text-sm max-w-md mx-auto leading-relaxed">{{ __('بانتظار انضمام أول شركاء النجاح إلى شبكة التجار الخاصة بك.') }}</p>
                </div>
                <div class="pt-6">
                    <a href="{{ route('merchants.verify') }}" class="px-12 py-5 bg-gold text-onyx rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-transform shadow-xl shadow-gold/20">
                        {{ __('مراجعة الطلبات المعلقة') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($merchants->hasPages())
        <div class="luxury-card p-8 border border-main/10 bg-main/5 text-right mt-12">
            {{ $merchants->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Live Synchronizer for Merchant Data
    window.addEventListener('DOMContentLoaded', () => {
        if (window.Echo) {
            window.Echo.channel('public.sync')
                .listen('.app.sync', (e) => {
                    console.log('Merchant Grid Live Update:', e);
                    if (e.model === 'product' || e.model === 'booking') {
                        // Silent Page Refresh to update all counts and positions
                        window.location.reload(); 
                    }
                });
        }
    });
</script>
@endpush

@endsection
