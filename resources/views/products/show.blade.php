@extends('layouts.admin')

@section('title', __('تفاصيل المنتج'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('عرض القطعة') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('products.edit', $product->id) }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest hover:text-gold transition-all flex items-center gap-3">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                {{ __('تعديل المنتج') }}
            </a>
            <a href="{{ route('products.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للمخزن') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Visuals Section -->
        <div class="space-y-8">
            <div class="luxury-card p-4 border border-main/10 bg-main/5 aspect-square relative overflow-hidden group">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-110 transition-transform duration-700">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gold/5 text-gold/10">
                        <i data-lucide="image" class="w-32 h-32"></i>
                    </div>
                @endif
                
                <div class="absolute top-8 right-8 px-5 py-2 bg-black/60 backdrop-blur-md border border-white/10 rounded-full text-xs font-black text-gold uppercase tracking-widest">
                    {{ $product->material->karat ?? '21' }}K
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="space-y-8">
            <div class="luxury-card p-12 border border-main/10 bg-main/5 space-y-10">
                <div class="space-y-2">
                    <p class="text-[11px] text-muted/40 font-black uppercase tracking-widest">{{ $product->category->name ?? __('غير مصنف') }}</p>
                    <h3 class="text-4xl font-black text-main tracking-tight leading-tight">{{ $product->title }}</h3>
                </div>

                <div class="grid grid-cols-2 gap-8 py-8 border-y border-main/10">
                    <div class="space-y-1">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('الوزن الفعلي') }}</p>
                        <p class="text-3xl font-black text-main">{{ number_format($product->weight, 2) }} <span class="text-sm font-medium">{{ __('جرام') }}</span></p>
                    </div>
                    <div class="space-y-1 text-left">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('السعر التقديري') }}</p>
                        <p class="text-3xl font-black text-gold">{{ number_format($product->final_price) }} <span class="text-sm font-medium">{{ __('ريال') }}</span></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-black text-main shadow-gold flex items-center gap-3">
                        <i data-lucide="info" class="w-4 h-4 text-gold"></i>
                        {{ __('وصف القطعة') }}
                    </h4>
                    <p class="text-sm text-muted/60 leading-relaxed font-bold">{{ $product->description ?? __('لا يوجد وصف متاح لهذا المنتج حالياً.') }}</p>
                </div>

                <div class="pt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-main/10 border border-main/20 rounded-2xl flex items-center gap-4">
                        <i data-lucide="store" class="w-6 h-6 text-gold opacity-30"></i>
                        <div>
                            <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('التاجر المنتج') }}</p>
                            <p class="text-xs font-bold text-main">{{ $product->merchant->store_name ?? __('النظام') }}</p>
                        </div>
                    </div>
                    <div class="p-4 bg-main/10 border border-main/20 rounded-2xl flex items-center gap-4">
                        <i data-lucide="tag" class="w-6 h-6 text-gold opacity-30"></i>
                        <div>
                            <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('حالة المخزون') }}</p>
                            <p class="text-xs font-bold text-emerald-500">{{ __('متوفر') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
