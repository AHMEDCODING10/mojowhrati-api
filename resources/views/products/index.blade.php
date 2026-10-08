@extends('layouts.admin')

@section('title', __('إدارة المنتجات'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('مخزون المنتجات') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3 px-6 py-3 bg-white/40 dark:bg-black/20 border border-gold/10 rounded-2xl">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase text-muted/60 tracking-widest">{{ $products->total() }} {{ __('منتج معروض') }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div class="luxury-card p-8 border border-main/10 bg-main/5 flex flex-col justify-center">
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('إجمالي القطع') }}</p>
            <h3 class="text-4xl font-black text-main">{{ $products->total() }}</h3>
        </div>
        <div class="luxury-card p-8 border border-main/10 bg-main/5 flex flex-col justify-center">
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('إجمالي الوزن') }}</p>
            <h3 class="text-4xl font-black text-gold">{{ number_format($products->sum('weight'), 2) }} <span class="text-xs text-muted/40 font-medium">{{ __('جرام') }}</span></h3>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center">
        {{ session('success') }}
    </div>
    @endif

    <!-- Search & Filter Bar (Minimalist) -->
    <div class="luxury-card p-4 border border-main/5 bg-main/5">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ابحث عن اسم المنتج، العيار أو التاجر...') }}" class="input-luxury w-full pr-12 text-sm py-4">
                <i data-lucide="search" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted/20"></i>
            </div>
            <button type="submit" class="px-8 py-4 bg-card border border-main rounded-xl text-muted/60 text-[10px] font-black uppercase tracking-widest hover:text-gold transition-all">
                {{ __('تصفية') }}
            </button>
            <a href="{{ route('products.index') }}" class="p-4 bg-card border border-main text-muted/20 rounded-xl hover:text-rose-500 transition-all" title="{{ __('إعادة تعيين') }}">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
            </a>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-8">
        @forelse($products as $product)
            <div class="luxury-card p-0 overflow-hidden group border border-main/10 hover:border-gold/30 transition-all duration-500 relative">
                <!-- Image Container -->
                <div class="relative h-72 bg-main/5 overflow-hidden">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gold/5 text-gold/10">
                            <i data-lucide="image" class="w-20 h-20"></i>
                        </div>
                    @endif
                    
                    <!-- Floating Badge -->
                    <div class="absolute top-4 right-4 px-3 py-1 bg-black/60 backdrop-blur-md border border-white/10 rounded-full text-[9px] font-black text-gold uppercase tracking-widest">
                        {{ $product->material->karat ?? '21' }}K
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 space-y-4">
                    <div class="space-y-1">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ $product->category->name ?? __('غير محدد') }}</p>
                        <h4 class="text-xl font-black text-main group-hover:text-gold transition-colors truncate">{{ $product->title }}</h4>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-main/10">
                        <div class="space-y-1">
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('الوزن') }}</p>
                            <p class="text-lg font-black text-gold">{{ number_format($product->weight, 2) }} <span class="text-[10px] text-main/60">{{ __('جرام') }}</span></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <a href="{{ route('products.edit', $product->id) }}" class="flex-1 py-3 bg-card border border-main rounded-xl text-muted/40 text-[9px] font-black uppercase tracking-widest text-center hover:text-gold transition-all duration-300">
                            {{ __('تعديل') }}
                        </a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('{{ __('حذف؟') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-card border border-main rounded-xl text-muted/40 hover:text-rose-500 transition-all duration-300">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full luxury-card p-24 text-center space-y-8 border border-dashed border-main/20 bg-main/5">
                <div class="w-24 h-24 bg-gold/5 rounded-full flex items-center justify-center mx-auto text-gold/10 border border-gold/5 animate-pulse">
                    <i data-lucide="gem" class="w-12 h-12"></i>
                </div>
                <div class="space-y-3">
                    <h3 class="text-2xl font-black text-main">{{ __('المخزن فارغ!') }}</h3>
                    <p class="text-muted/40 text-xs max-w-sm mx-auto leading-relaxed">{{ __('لم يتم العثور على أي منتجات مطابقة لعملية البحث.') }}</p>
                </div>
                <div class="flex justify-center gap-4 text-[10px] font-black text-muted/20 uppercase tracking-[0.2em]">
                    {{ __('بانتظار إضافة المتاجر لمنتجات جديدة') }}
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="luxury-card p-8 border border-main/10 bg-main/5 text-right mt-12">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
