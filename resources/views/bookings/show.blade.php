@extends('layouts.admin')

@section('title', __('تفاصيل الحجز'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('عرض تفاصيل الحجز') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('bookings.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للقائمة') }}
            </a>
        </div>
    </div>

    <!-- Booking Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main: Transaction Details -->
        <div class="lg:col-span-2 space-y-8">
            <div class="luxury-card p-12 border border-main/10 bg-main/5 min-h-[400px] flex flex-col justify-between">
                <div class="space-y-10">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <h3 class="text-3xl font-black text-main uppercase tracking-widest">{{ __('رقم المرجع') }}</h3>
                            <p class="text-gold font-black">#{{ $booking->id }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-1">{{ __('حالة الطلب') }}</p>
                            @php
                                $statusColor = match($booking->status) {
                                    'completed' => 'emerald',
                                    'pending' => 'amber',
                                    'cancelled' => 'rose',
                                    default => 'indigo'
                                };
                            @endphp
                            <span class="px-6 py-2 bg-{{ $statusColor }}-500/10 border border-{{ $statusColor }}-500/20 text-{{ $statusColor }}-500 text-[11px] font-black uppercase rounded-full">
                                {{ __($booking->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 py-10 border-y border-main/10">
                        <div class="space-y-1">
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('المنتج') }}</p>
                            <p class="text-lg font-black text-main truncate">{{ $booking->product->title ?? __('منتج غير متاح') }}</p>
                        </div>
                        <div class="space-y-1 text-center">
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('الوزن') }}</p>
                            <p class="text-lg font-black text-main">{{ number_format($booking->product->weight ?? 0, 2) }} <span class="text-[10px] font-medium opacity-40">g</span></p>
                        </div>
                        <div class="space-y-1 text-left">
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('تاريخ الحجز') }}</p>
                            <p class="text-lg font-black text-main">{{ $booking->created_at->format('Y/m/d H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="space-y-1">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('المبلغ الإجمالي المسجل') }}</p>
                        <h3 class="text-5xl font-black text-gold">{{ number_format($booking->total_price) }} <span class="text-xs font-medium text-muted/20">MRU</span></h3>
                    </div>
                    
                    <form action="{{ route('bookings.updateStatus', $booking->id) }}" method="POST" class="flex items-center gap-4">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="input-luxury pr-8 pl-12 py-4 text-xs font-black uppercase tracking-widest">
                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار') }}</option>
                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>{{ __('مكتمل') }}</option>
                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>{{ __('ملغي') }}</option>
                        </select>
                        <button type="submit" class="p-4 bg-gold text-onyx rounded-xl hover:scale-105 transition-all">
                            <i data-lucide="check" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar: Client & Store -->
        <div class="lg:col-span-1 space-y-8">
            <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-8">
                <h4 class="text-sm font-black text-main uppercase tracking-widest flex items-center gap-3">
                    <i data-lucide="user" class="w-4 h-4 text-gold"></i>
                    {{ __('بيانات العميل') }}
                </h4>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gold/5 flex items-center justify-center text-lg font-black text-gold border border-gold/10">
                            {{ substr($booking->user->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-base font-black text-main">{{ $booking->user->name ?? __('ضيف') }}</p>
                            <p class="text-[10px] text-muted/40 font-medium">{{ $booking->user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-6">
                <h4 class="text-sm font-black text-main uppercase tracking-widest flex items-center gap-3">
                    <i data-lucide="store" class="w-4 h-4 text-gold"></i>
                    {{ __('المتجر المزود') }}
                </h4>
                <div class="space-y-4">
                    <div class="space-y-1">
                        <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('اسم المتجر') }}</p>
                        <p class="text-sm font-bold text-main">{{ $booking->merchant->store_name ?? __('النظام') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
