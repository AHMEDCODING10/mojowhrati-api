@extends('layouts.admin')

@section('title', __('طلبات ترويج التجار'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pt-6">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('طلبات الترويج المقدمة من التجار') }}</h2>
            <p class="text-xs text-muted/60 font-bold">{{ __('مراجعة طلبات الترويج وإشعارات التحويل والموافقة أو الرفض مع سبب الرفض') }}</p>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full mt-3"></div>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('promotions.index') }}" class="px-6 py-3 bg-main/10 text-muted rounded-xl text-xs font-bold hover:bg-main/20 transition-all flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                إدارة الحملات النشطة
            </a>
        </div>
    </div>

    

    <!-- Requests Table Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-2xl font-black text-main">{{ __('قائمة الطلبات الواردة') }}</h3>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('promotions.requests.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ !request('status') ? 'bg-gold text-black' : 'bg-main/5 text-muted' }}">الكل</a>
                <a href="{{ route('promotions.requests.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'bg-main/5 text-muted' }}">معلقة ({{ $pendingCount }})</a>
                <a href="{{ route('promotions.requests.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'approved' ? 'bg-emerald-500 text-white' : 'bg-main/5 text-muted' }}">مقبولة</a>
                <a href="{{ route('promotions.requests.index', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request('status') == 'rejected' ? 'bg-rose-500 text-white' : 'bg-main/5 text-muted' }}">مرفوضة</a>
            </div>
        </div>

        <div class="luxury-card border border-main/10 rounded-3xl overflow-hidden bg-main/5">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-main/10 text-[10px] font-black uppercase tracking-widest text-muted/60 bg-main/10">
                            <th class="p-4">التاجر</th>
                            <th class="p-4">نوع الترويج المطلوب</th>
                            <th class="p-4">المدة بالأسابيع</th>
                            <th class="p-4">المبلغ المحسوب</th>
                            <th class="p-4">إشعار الدفع / التحويل</th>
                            <th class="p-4">الحالة</th>
                            <th class="p-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-main/10">
                        @forelse($requests as $req)
                        <tr class="hover:bg-main/10 transition-colors">
                            <td class="p-4 font-bold text-main">
                                <div>{{ $req->merchant?->store_name }}</div>
                                <div class="text-xs text-muted/60">{{ $req->merchant?->user?->phone }}</div>
                            </td>
                            <td class="p-4 font-bold">
                                @if($req->type == 'product')
                                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full text-xs">أصناف محددة ({{ count($req->product_ids ?? []) }})</span>
                                @elseif($req->type == 'category')
                                    <span class="px-3 py-1 bg-purple-500/10 text-purple-400 rounded-full text-xs">قسم ({{ $req->category?->name }})</span>
                                @else
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-400 rounded-full text-xs">جميع الأقسام / متجر كامل</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-main">
                                {{ $req->duration_weeks }} أسبوع ({{ $req->duration_weeks * 7 }} يوم)
                            </td>
                            <td class="p-4 font-black text-gold">
                                ${{ number_format($req->fee_amount, 2) }}
                            </td>
                            <td class="p-4">
                                @if($req->receipt_url)
                                <a href="{{ $req->receipt_url }}" target="_blank" class="px-3 py-1.5 bg-gold/10 text-gold hover:bg-gold hover:text-black rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1">
                                    <i data-lucide="image" class="w-4 h-4"></i> معاينة الإشعار
                                </a>
                                @else
                                <span class="text-xs text-muted">لا يوجد</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($req->status == 'pending')
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-xs font-bold">قيد المراجعة</span>
                                @elseif($req->status == 'approved')
                                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-xs font-bold">تمت الموافقة</span>
                                @else
                                    <span class="px-3 py-1 bg-rose-500/10 text-rose-500 rounded-full text-xs font-bold" title="{{ $req->rejection_reason }}">مرفوض</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($req->status == 'pending')
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('promotions.requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('هل تريد الموافقة على طلب الترويج وتفعيل الظهور الأول؟')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md hover:bg-emerald-600 transition-all flex items-center gap-1">
                                            <i data-lucide="check" class="w-4 h-4"></i> موافقة
                                        </button>
                                    </form>

                                    <button type="button" onclick="openRejectModal({{ $req->id }})" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-bold shadow-md hover:bg-rose-600 transition-all flex items-center gap-1">
                                        <i data-lucide="x" class="w-4 h-4"></i> رفض
                                    </button>
                                </div>
                                @else
                                <div class="text-center text-xs text-muted/60 font-bold">مكتمل</div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-muted/50 font-bold">
                                {{ __('لا توجد طلبات ترويج حالياً.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-main/10">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-main border border-main/20 p-8 rounded-3xl max-w-lg w-full space-y-6">
        <h3 class="text-2xl font-black text-main">رفض طلب الترويج</h3>
        <p class="text-xs text-muted">يرجى كتابة سبب الرفض ليتم إرساله في إشعار رسمي للتاجر:</p>

        <form id="rejectForm" action="" method="POST" class="space-y-4">
            @csrf
            <textarea name="rejection_reason" rows="4" placeholder="مثال: صورة إشعار التحويل غير واضحة، أو التنسيق غير مكتمل..." class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-rose-500" required></textarea>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-5 py-2.5 bg-main/10 text-muted rounded-xl font-bold text-xs">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 bg-rose-500 text-white rounded-xl font-bold text-xs shadow-lg">إرسال الرفض والإشعار</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/promotions/requests/${id}/reject`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
