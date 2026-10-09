@extends('layouts.admin')

@section('title', 'إضافة قطعة جديدة')

@section('content')
<div class="space-y-12 w-full" dir="rtl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('إضافة قطعة جديدة') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        <a href="{{ route('products.index') }}" class="px-8 py-4 bg-card border border-main text-muted rounded-2xl text-[13px] font-black uppercase tracking-widest hover:text-gold transition-all">{{ __('العودة للمخزون') }}</a>
    </div>

    <x-luxury.card title="{{ __('تفاصيل القطعة') }}" subtitle="Product Details">
        <div class="p-10 text-center space-y-6">
            <div class="w-20 h-20 rounded-full bg-gold/10 flex items-center justify-center mx-auto text-gold">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <p class="text-xl text-muted/60">{{ __('هذه الصفحة قيد التطوير. سيتم إضافة نموذج إدخال المنتجات هنا قريباً.') }}</p>
        </div>
    </x-luxury.card>
</div>
@endsection
