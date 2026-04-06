@extends('layouts.admin')

@section('title', __('إدارة أسعار الذهب'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('تحديث أسعار الذهب اليومية') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <form action="{{ route('gold-prices.sync') }}" method="POST">
                @csrf
                <button type="submit" class="px-8 py-4 bg-gold text-onyx rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gold/20 hover:scale-105 transition-all flex items-center gap-3">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    {{ __('تحديث تلقائي من API') }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Live Ticker Info -->
        <div class="lg:col-span-1 space-y-8">
            <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-8 text-center">
                <div class="w-24 h-24 rounded-full bg-gold/5 border border-gold/10 flex items-center justify-center text-4xl text-gold mx-auto shadow-2xl">
                    <i data-lucide="trending-up" class="w-10 h-10"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black text-main tracking-widest uppercase">{{ __('البورصة العالمية') }}</h4>
                    <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest leading-relaxed mt-2">{{ __('يتم جلب هذه الأسعار بشكل لحظي وتؤثر على جميع حسابات النظام') }}</p>
                </div>
            </div>
        </div>

        <!-- Price Update Form -->
        <div class="lg:col-span-2 space-y-8">
            <div class="luxury-card p-10 border border-main/10">
                <form action="{{ route('gold-prices.update-global') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach(['24K', '22K', '21K', '18K'] as $karat)
                            <div class="space-y-3">
                                <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('سعر جرام الذهب عيار') }} {{ $karat }}</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="prices[{{ $karat }}]" value="{{ $prices[$karat] ?? 0 }}" class="input-luxury w-full py-4 pr-12 text-sm font-black" required>
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] text-muted/20 font-black">MRU</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end pt-10">
                        <button type="submit" class="px-16 py-5 bg-gold text-onyx rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl shadow-gold/30 hover:scale-105 active:scale-95 transition-all">
                            {{ __('تحديث الأسعار يدوياً') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
