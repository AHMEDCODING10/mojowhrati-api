@extends('layouts.admin')

@section('title', __('إدارة الترويج والظهور المميز'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pt-6">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('إدارة الترويج والظهور المميز') }}</h2>
            <p class="text-xs text-muted/60 font-bold">{{ __('التحكم بأولوية ظهور الأصناف والمنتجات والمتاجر للعملاء وتتبع إيرادات الترويج') }}</p>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full mt-3"></div>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('promotions.create') }}" class="px-8 py-4 bg-gold text-onyx rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gold/20 hover:scale-105 transition-all flex items-center gap-3 text-center">
                <i data-lucide="plus" class="w-4 h-4"></i>
                {{ __('إضافة ترويج جديد') }}
            </a>
        </div>
    </div>

    

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="luxury-card p-6 border border-main/10 bg-main/5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-muted/50">
                <span class="text-[10px] font-black uppercase tracking-widest">{{ __('إجمالي الحملات') }}</span>
                <i data-lucide="sparkles" class="w-5 h-5 text-gold"></i>
            </div>
            <p class="text-3xl font-black text-main">{{ $stats['total_promotions'] }}</p>
        </div>

        <div class="luxury-card p-6 border border-emerald-500/20 bg-emerald-500/5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-emerald-500">
                <span class="text-[10px] font-black uppercase tracking-widest">{{ __('حملات نشطة حالياً') }}</span>
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <p class="text-3xl font-black text-emerald-500">{{ $stats['active_promotions'] }}</p>
        </div>

        <div class="luxury-card p-6 border border-gold/20 bg-gold/5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-gold">
                <span class="text-[10px] font-black uppercase tracking-widest">{{ __('إجمالي إيرادات الترويج') }}</span>
                <i data-lucide="coins" class="w-5 h-5"></i>
            </div>
            <p class="text-3xl font-black text-gold">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>

        <div class="luxury-card p-6 border border-rose-500/20 bg-rose-500/5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-rose-500">
                <span class="text-[10px] font-black uppercase tracking-widest">{{ __('حملات منتهية') }}</span>
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <p class="text-3xl font-black text-rose-500">{{ $stats['expired_promotions'] }}</p>
        </div>
    </div>

    <!-- Promotions Table Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-2xl font-black text-main">{{ __('سجل الحملات الترويجية والظهور الأول') }}</h3>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('promotions.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ !request('status') ? 'bg-gold text-black' : 'bg-main/5 text-muted' }}">الكل</a>
                <a href="{{ route('promotions.index', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'active' ? 'bg-emerald-500 text-white' : 'bg-main/5 text-muted' }}">نشط</a>
                <a href="{{ route('promotions.index', ['status' => 'expired']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'expired' ? 'bg-rose-500 text-white' : 'bg-main/5 text-muted' }}">منتهي</a>
            </div>
        </div>

        <div class="luxury-card border border-main/10 rounded-3xl overflow-hidden bg-main/5">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-main/10 text-[10px] font-black uppercase tracking-widest text-muted/60 bg-main/10">
                            <th class="p-4">التاجر / المتجر</th>
                            <th class="p-4">نوع الترويج</th>
                            <th class="p-4">الهدف المُروّج</th>
                            <th class="p-4">مكان الظهور</th>
                            <th class="p-4">تاريخ البداية والنهاية</th>
                            <th class="p-4">الأولوية والرسوم</th>
                            <th class="p-4">الحالة</th>
                            <th class="p-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-main/10">
                        @forelse($promotions as $promo)
                        <tr class="hover:bg-main/10 transition-colors">
                            <td class="p-4 font-bold text-main">
                                {{ $promo->merchant?->store_name ?? 'عام (منشأ من الأدمن)' }}
                            </td>
                            <td class="p-4 font-bold">
                                @if($promo->type == 'category')
                                    <span class="px-3 py-1 bg-purple-500/10 text-purple-400 rounded-full text-xs">صنف/قسم</span>
                                @elseif($promo->type == 'product')
                                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full text-xs">منتج محدد</span>
                                @elseif($promo->type == 'merchant_store')
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-400 rounded-full text-xs">متجر كامل</span>
                                @else
                                    <span class="px-3 py-1 bg-teal-500/10 text-teal-400 rounded-full text-xs">بنر ترويجي</span>
                                @endif
                            </td>
                            <td class="p-4 font-black text-gold">
                                {{ $promo->target_name }}
                            </td>
                            <td class="p-4 text-xs font-bold text-muted">
                                {{ $promo->placement }}
                            </td>
                            <td class="p-4 text-xs font-bold text-muted">
                                <div>من: {{ $promo->start_at ? $promo->start_at->format('Y-m-d') : 'فوري' }}</div>
                                <div>إلى: {{ $promo->end_at ? $promo->end_at->format('Y-m-d') : 'غير محدد' }}</div>
                            </td>
                            <td class="p-4 font-bold">
                                <div class="text-main">الأولوية: #{{ $promo->priority }}</div>
                                <div class="text-gold text-xs">${{ number_format($promo->fee_amount, 2) }}</div>
                            </td>
                            <td class="p-4">
                                @if($promo->is_expired)
                                    <span class="px-3 py-1 bg-rose-500/10 text-rose-500 rounded-full text-xs font-bold">منتهي</span>
                                @elseif($promo->status == 'active')
                                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-xs font-bold">نشط</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-500/10 text-gray-400 rounded-full text-xs font-bold">موقوف</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('promotions.toggle', $promo->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-2 text-muted hover:text-gold transition-colors" title="تعديل الحالة">
                                            <i data-lucide="{{ $promo->status == 'active' ? 'pause-circle' : 'play-circle' }}" class="w-5 h-5"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('promotions.edit', $promo->id) }}" class="p-2 text-muted hover:text-gold transition-colors" title="تعديل">
                                        <i data-lucide="edit" class="w-5 h-5"></i>
                                    </a>
                                    <form action="{{ route('promotions.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('هل انت متاكد من حذف هذه الحملة الترويجية؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-muted hover:text-rose-500 transition-colors" title="حذف">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-muted/50 font-bold">
                                {{ __('لا توجد حملات ترويجية حالياً. اضغط على "إضافة ترويج جديد" لإنشاء أول حملة.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-main/10">
                {{ $promotions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
