@extends('layouts.admin')

@section('title', __('إدارة الحجوزات'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('سجل الحجوزات') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3 px-6 py-3 bg-white/40 dark:bg-black/20 border border-gold/10 rounded-2xl">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase text-muted/60 tracking-widest">{{ $bookings->total() }} {{ __('عملية حجز') }}</span>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="luxury-card overflow-hidden border border-gold/10 shadow-2xl bg-white/50 dark:bg-[#1A1A1A]/50 backdrop-blur-md rounded-[3rem]">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="header-bg text-onyx dark:text-gold text-[11px] font-black uppercase tracking-[0.2em]">
                        <th class="px-10 py-8 border-b border-white/20">{{ __('رقم الحجز') }}</th>
                        <th class="px-10 py-8 border-b border-white/20">{{ __('العميل') }}</th>
                        <th class="px-10 py-8 border-b border-white/20">{{ __('المتجر') }}</th>
                        <th class="px-10 py-8 border-b border-white/20">{{ __('المنتج') }}</th>
                        <th class="px-10 py-8 border-b border-white/20 text-center">{{ __('تاريخ الحجز') }}</th>
                        <th class="px-10 py-8 border-b border-white/20 text-center">{{ __('الحالة') }}</th>
                        <th class="px-10 py-8 border-b border-white/20 text-center">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gold/5">
                    @forelse($bookings as $booking)
                        <tr class="group hover:bg-gold/5 transition-all transition-duration-500">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <span class="w-1.5 h-8 bg-gold/20 rounded-full group-hover:bg-gold transition-colors"></span>
                                    <span class="text-gold font-black tracking-widest text-lg">#{{ $booking->id }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-black border border-gold/10 flex items-center justify-center text-gold group-hover:scale-110 transition-transform">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <div class="text-base font-black text-main tracking-tight">{{ $booking->customer->name ?? __('غير مسجل') }}</div>
                                        <div class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ $booking->customer->phone ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-[1rem] bg-gold/5 border border-gold/20 flex items-center justify-center overflow-hidden">
                                        @if($booking->merchant->logo_url ?? false)
                                            <img src="{{ $booking->merchant->logo_url }}" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="store" class="w-5 h-5 text-gold/30"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gold/80 hover:text-gold transition-colors underline decoration-gold/20 tracking-tight">{{ $booking->merchant->store_name ?? __('متجر غير معروف') }}</div>
                                        <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('التاجر الشريك') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-emerald-500/5 rounded-xl text-emerald-500">
                                        <i data-lucide="gem" class="w-4 h-4 text-emerald-500/50"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-main truncate max-w-[180px] tracking-tight">{{ $booking->product->title ?? __('منتج غير متوفر') }}</div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-black text-emerald-500 uppercase">{{ $booking->product->karat ?? '21' }}K</span>
                                            <span class="w-1 h-1 bg-main/10 rounded-full"></span>
                                            <span class="text-[9px] text-muted/40 font-black tracking-widest">{{ __('الوزن:') }} {{ $booking->product->weight ?? '0' }}g</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <div class="inline-block px-5 py-3 bg-white/50 dark:bg-black/20 border border-gold/10 rounded-2xl shadow-sm text-center">
                                    <div class="text-main font-black text-xs">{{ $booking->created_at->diffForHumans() }}</div>
                                    <div class="text-[8px] text-muted/20 font-black uppercase tracking-widest mt-1">{{ $booking->created_at->format('Y/m/d') }}</div>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-center">
                                @php
                                    $statusConfig = match($booking->status) {
                                        'completed' => ['color' => 'emerald', 'icon' => 'check-circle', 'label' => 'مكتمل'],
                                        'pending' => ['color' => 'amber', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                                        'cancelled' => ['color' => 'rose', 'icon' => 'x-circle', 'label' => 'ملغي'],
                                        'confirmed' => ['color' => 'indigo', 'icon' => 'star', 'label' => 'مؤكد'],
                                        default => ['color' => 'gold', 'icon' => 'help-circle', 'label' => $booking->status]
                                    };
                                @endphp
                                <div class="px-5 py-3 bg-{{ $statusConfig['color'] }}-500/10 border border-{{ $statusConfig['color'] }}-500/20 text-{{ $statusConfig['color'] }}-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl inline-flex items-center gap-3 shadow-md hover:scale-105 transition-transform">
                                    <i data-lucide="{{ $statusConfig['icon'] }}" class="w-3.5 h-3.5"></i>
                                    {{ $statusConfig['label'] }}
                                </div>
                                <p class="text-[8px] text-muted/30 font-black uppercase mt-2 tracking-widest">
                                    {{ $booking->expires_at ? __('تنتهي') . ' ' . $booking->expires_at->diffForHumans() : '' }}
                                </p>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center justify-center gap-4">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="w-12 h-12 bg-white dark:bg-black border border-gold/10 rounded-2xl text-muted/40 hover:text-gold hover:border-gold transition-all flex items-center justify-center shadow-sm" title="{{ __('عرض التفاصيل الكاملة') }}">
                                        <i data-lucide="eye" class="w-5 h-5"></i>
                                    </a>
                                    <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا الحجز من السجلات؟') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-12 h-12 bg-rose-500/5 border border-rose-500/10 rounded-2xl text-rose-500/40 hover:bg-rose-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-10 py-32 text-center">
                                <div class="w-32 h-32 bg-gold/5 rounded-full flex items-center justify-center mx-auto text-gold/20 mb-8 border border-gold/5 animate-pulse">
                                    <i data-lucide="calendar-range" class="w-16 h-16"></i>
                                </div>
                                <h3 class="text-3xl font-black text-main tracking-tight mb-3 italic opacity-40">{{ __('لا توجد حجوزات مسجلة حالياً') }}</h3>
                                <p class="text-muted/40 text-[10px] font-black uppercase tracking-widest">{{ __('بمجرد بدء العملاء بالحجز من المتاجر، ستظهر كافة البيانات هنا.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="p-8 border-t border-main/10 bg-main/5 text-right">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
