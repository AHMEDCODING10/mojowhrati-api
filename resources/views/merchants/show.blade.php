@extends('layouts.admin')

@section('title', __('تفاصيل التاجر'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('الملف التعريفي للمتجر') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            @if($merchant->approved)
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="px-8 py-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all flex items-center gap-3 shadow-xl hover:shadow-rose-500/20">
                        <i data-lucide="shield-off" class="w-4 h-4"></i>
                        {{ __('إلغاء التوثيق') }}
                    </button>
                    <!-- Unverify Reason Form -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full right-0 mt-4 w-80 p-6 bg-card border border-main/10 rounded-3xl shadow-2xl z-50 space-y-4">
                        <h5 class="text-xs font-black text-main uppercase tracking-widest">{{ __('سبب إلغاء التوثيق') }}</h5>
                        <form action="{{ route('merchants.unapprove', $merchant->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <textarea name="notes" required placeholder="{{ __('اكتب السبب هنا للتاجر...') }}" class="w-full bg-main/5 border border-main/10 rounded-2xl p-4 text-xs font-bold text-main placeholder:text-muted/20 focus:ring-0 focus:border-gold transition-all" rows="3"></textarea>
                            <button type="submit" class="w-full py-4 bg-rose-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg transition-all">
                                {{ __('تأكيد الإلغاء') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            <a href="{{ route('merchants.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للقائمة') }}
            </a>
        </div>
    </div>

    <!-- Merchant Profile Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Store Identity -->
        <div class="lg:col-span-1 space-y-8">
            <div class="luxury-card p-0 overflow-hidden border border-main/10 bg-main/5">
                <div class="h-32 bg-main/10 relative">
                    <div class="absolute -bottom-12 right-1/2 translate-x-1/2 w-32 h-32 rounded-3xl bg-card border-4 border-[#121212] flex items-center justify-center text-4xl shadow-2xl overflow-hidden">
                        @if($merchant->logo_url)
                            <img src="{{ $merchant->logo_url }}" class="w-full h-full object-cover">
                        @else
                            <span class="opacity-20 text-gold">🏪</span>
                        @endif
                    </div>
                </div>

                <div class="p-10 pt-16 text-center space-y-4">
                    <h4 class="text-2xl font-black text-main flex items-center justify-center gap-3">
                        {{ $merchant->store_name }}
                        @if($merchant->approved)
                            <i data-lucide="badge-check" class="w-6 h-6 text-emerald-500"></i>
                        @endif
                    </h4>
                    <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ $merchant->user->name ?? '-' }}</p>
                    
                    <div class="pt-6 grid grid-cols-2 gap-4 border-t border-main/10">
                        <div class="space-y-1">
                            <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('المنتجات') }}</p>
                            <p class="text-lg font-black text-main">{{ $merchant->products_count ?? 0 }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('المبيعات') }}</p>
                            <p class="text-lg font-black text-main">{{ $merchant->bookings_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-6">
                <h4 class="text-sm font-black text-main uppercase tracking-widest flex items-center gap-3">
                    <i data-lucide="map-pin" class="w-4 h-4 text-gold"></i>
                    {{ __('الموقع والتواصل') }}
                </h4>
                <div class="space-y-4 pt-2">
                    <div class="space-y-1">
                        <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('المدينة') }}</p>
                        <p class="text-sm font-bold text-main">{{ $merchant->city ?? __('غير محدد') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('رقم الهاتف') }}</p>
                        <p class="text-sm font-bold text-main">{{ $merchant->user->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: Store Performance & Documents -->
        <div class="lg:col-span-2 space-y-8">
            <div class="luxury-card p-10 border border-main/10 min-h-[400px]">
                <h4 class="text-xl font-black text-main mb-12 uppercase tracking-widest flex items-center gap-4">
                    <i data-lucide="file-text" class="w-5 h-5 text-gold"></i>
                    {{ __('بيانات السجل التجاري') }}
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-2">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('رقم السجل') }}</p>
                        <p class="text-lg font-bold text-main">CR-{{ $merchant->commercial_registration ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-4">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('وثيقة السجل') }}</p>
                        @if($merchant->logo) {{-- Check if specialized doc field exists or fallback to general --}}
                             <a href="{{ $merchant->logo_url }}" target="_blank" class="flex items-center gap-4 p-4 bg-indigo-500/5 border border-indigo-500/10 rounded-2xl hover:bg-gold/5 hover:border-gold/20 transition-all group">
                                <i data-lucide="external-link" class="w-5 h-5 text-indigo-500 group-hover:text-gold transition-colors"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-main">{{ __('تحميل الوثيقة') }}</span>
                            </a>
                        @else
                            <p class="text-xs text-muted/20 italic">{{ __('لم يتم رفع الوثيقة بعد') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="luxury-card p-10 border border-main/10">
                <h4 class="text-xl font-black text-main mb-8 uppercase tracking-widest flex items-center gap-4">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-gold"></i>
                    {{ __('أحدث المنتجات المعروضة') }}
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($merchant->products()->latest()->limit(6)->get() as $product)
                        <div class="p-4 bg-main/5 border border-main/10 rounded-2xl flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-gold/5 flex items-center justify-center text-gold border border-gold/10 overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs opacity-20">💎</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-main truncate">{{ $product->title }}</p>
                                <p class="text-[9px] text-gold font-black">{{ number_format($product->final_price) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-center py-10 text-muted/20 text-[10px] uppercase font-black tracking-widest">{{ __('لا توجد منتجات حالياً') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
