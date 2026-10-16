@extends('layouts.admin')

@section('title', __('إضافة حملة ترويجية جديدة'))

@section('content')
<div class="space-y-8 pb-20 max-w-4xl mx-auto" dir="rtl">

    {{-- Header --}}
    <div class="pt-6">
        <h2 class="text-3xl font-black text-main uppercase tracking-widest mb-2">{{ __('إضافة حملة ترويجية جديدة') }}</h2>
        <p class="text-xs text-muted/60 font-bold">{{ __('تخصيص الظهور الأولي لأقسام أو منتجات تاجر في التطبيق وتحديد الرسوم والمدة') }}</p>
        <div class="h-1.5 w-20 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full mt-3"></div>
    </div>

    @if ($errors->any())
    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-500 text-sm font-bold space-y-1">
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('promotions.store') }}" method="POST"
          class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-8 shadow-xl space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Merchant Selection --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">التاجر المستفيد (اختياري)</label>
                <select name="merchant_id" class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all appearance-none cursor-pointer">
                    <option value="" class="bg-white dark:bg-neutral-900">-- بدون تاجر محدد (حملة عامة) --</option>
                    @foreach($merchants as $merchant)
                    <option value="{{ $merchant->id }}"
                            {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}
                            class="bg-white dark:bg-neutral-900">
                        {{ $merchant->store_name }} ({{ $merchant->user?->name }})
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Promotion Type --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">نوع الترويج <span class="text-rose-500">*</span></label>
                <select name="type" id="promotionType" class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all appearance-none cursor-pointer"
                        required onchange="toggleTargetFields()">
                    <option value="category"       {{ old('type') == 'category'       ? 'selected' : '' }} class="bg-white dark:bg-neutral-900">ترويج صنف / قسم معين</option>
                    <option value="product"         {{ old('type') == 'product'         ? 'selected' : '' }} class="bg-white dark:bg-neutral-900">ترويج منتج محدد</option>
                    <option value="merchant_store"  {{ old('type') == 'merchant_store'  ? 'selected' : '' }} class="bg-white dark:bg-neutral-900">ترويج المتجر كاملاً</option>
                    <option value="banner"          {{ old('type') == 'banner'          ? 'selected' : '' }} class="bg-white dark:bg-neutral-900">بنر ترويجي خاص</option>
                </select>
            </div>

            {{-- Category Select (Conditional) --}}
            <div id="categoryContainer">
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">اختر الصنف / القسم المستهدف</label>
                <select name="target_id_category" id="categorySelect" class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all appearance-none cursor-pointer">
                    <option value="" class="bg-white dark:bg-neutral-900">-- اختر القسم --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" class="bg-white dark:bg-neutral-900">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Product Select (Conditional) --}}
            <div id="productContainer" class="hidden">
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">اختر المنتج المستهدف</label>
                <select name="target_id_product" id="productSelect" class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all appearance-none cursor-pointer">
                    <option value="" class="bg-white dark:bg-neutral-900">-- اختر المنتج --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" class="bg-white dark:bg-neutral-900">
                        {{ $product->title }} ({{ $product->merchant?->store_name }})
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">عنوان الحملة (عنوان داخلي)</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       placeholder="مثال: ترويج قسم الخواتم - متجر الخليج"
                       class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all placeholder-muted/40">
            </div>

            {{-- Placement --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">مكان الظهور الأولي <span class="text-rose-500">*</span></label>
                <select name="placement" class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all appearance-none cursor-pointer" required>
                    <option value="HOME_TOP"        class="bg-white dark:bg-neutral-900">أعلى الشاشة الرئيسية (Home Top)</option>
                    <option value="CATEGORY_TOP"    class="bg-white dark:bg-neutral-900">أعلى قائمة القسم (Category Top)</option>
                    <option value="FEATURED_SLIDER" class="bg-white dark:bg-neutral-900">شريط المنتجات المميزة (Featured Slider)</option>
                    <option value="SEARCH_BOOST"    class="bg-white dark:bg-neutral-900">أول نتائج البحث (Search Boost)</option>
                </select>
            </div>

            {{-- Start Date --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">تاريخ البداية (فوري إذا تُرك فارغاً)</label>
                <input type="datetime-local" name="start_at" value="{{ old('start_at') }}"
                       class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
            </div>

            {{-- End Date --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">تاريخ النهاية (المدة الترويجية) <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="end_at" value="{{ old('end_at') }}"
                       class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
            </div>

            {{-- Priority --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">درجة الأولوية (PRIORITY SCORE) <span class="text-rose-500">*</span></label>
                <input type="number" name="priority" value="{{ old('priority', 10) }}" min="0"
                       class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all" required>
                <p class="text-[10px] text-muted/50 font-bold mt-2">الرقم الأعلى يعطي أولوية في الظهور قبل باقي المعروضات</p>
            </div>

            {{-- Fee Amount --}}
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">رسوم الترويج المستقطعة ($/ر.س) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" name="fee_amount" value="{{ old('fee_amount', '50.00') }}" min="0"
                       class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all" required dir="ltr">
            </div>

            {{-- Payment Status --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-3">حالة الدفع <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    {{-- Paid --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_status" value="paid"
                               {{ old('payment_status', 'paid') === 'paid' ? 'checked' : '' }}
                               class="hidden payment-radio">
                        <div data-val="paid"
                             class="payment-opt p-4 rounded-2xl border-2 text-center font-black text-xs transition-all duration-200 cursor-pointer
                                    {{ old('payment_status', 'paid') === 'paid'
                                        ? 'border-emerald-500 bg-emerald-500/10 text-emerald-500'
                                        : 'border-gold/15 bg-white dark:bg-black/30 text-muted/60 hover:border-emerald-500/40' }}">
                            مدفوع بالكامل
                        </div>
                    </label>
                    {{-- Unpaid --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_status" value="unpaid"
                               {{ old('payment_status') === 'unpaid' ? 'checked' : '' }}
                               class="hidden payment-radio">
                        <div data-val="unpaid"
                             class="payment-opt p-4 rounded-2xl border-2 text-center font-black text-xs transition-all duration-200 cursor-pointer
                                    {{ old('payment_status') === 'unpaid'
                                        ? 'border-rose-500 bg-rose-500/10 text-rose-500'
                                        : 'border-gold/15 bg-white dark:bg-black/30 text-muted/60 hover:border-rose-500/40' }}">
                            غير مدفوع (معلق)
                        </div>
                    </label>
                    {{-- Waived --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_status" value="waived"
                               {{ old('payment_status') === 'waived' ? 'checked' : '' }}
                               class="hidden payment-radio">
                        <div data-val="waived"
                             class="payment-opt p-4 rounded-2xl border-2 text-center font-black text-xs transition-all duration-200 cursor-pointer
                                    {{ old('payment_status') === 'waived'
                                        ? 'border-amber-500 bg-amber-500/10 text-amber-500'
                                        : 'border-gold/15 bg-white dark:bg-black/30 text-muted/60 hover:border-amber-500/40' }}">
                            مجاني / تجريبي
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <input type="hidden" name="target_id" id="targetIdHidden" value="">

        {{-- Notes --}}
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-muted/60 dark:text-muted/50 mb-2">ملاحظات إضافية (للأدمن)</label>
            <textarea name="notes" rows="3"
                      placeholder="أية تفاصيل إضافية حول التخفيض أو الاتفاق مع التاجر..."
                      class="w-full bg-white dark:bg-black/40 border border-gold/20 dark:border-gold/15 focus:border-gold focus:ring-2 focus:ring-gold/20 rounded-2xl px-4 py-3.5 text-main dark:text-white font-bold text-sm outline-none transition-all resize-none placeholder-muted/40"></textarea>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gold/10">
            <a href="{{ route('promotions.index') }}"
               class="px-6 py-3 bg-white/50 dark:bg-black/30 border border-gold/15 text-muted/60 rounded-xl font-bold text-xs hover:border-gold/30 transition-all">
                إلغاء
            </a>
            <button type="submit" onclick="prepareTargetId()"
                    class="px-8 py-3 bg-gradient-to-tr from-[#C5A059] to-[#E8D095] text-onyx rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-gold/30 hover:shadow-gold/50 hover:scale-105 active:scale-95 transition-all">
                حفظ وتفعيل الترويج
            </button>
        </div>
    </form>
</div>

<script>
function toggleTargetFields() {
    const type = document.getElementById('promotionType').value;
    document.getElementById('categoryContainer').classList.toggle('hidden', type !== 'category');
    document.getElementById('productContainer').classList.toggle('hidden', type !== 'product');
}

function prepareTargetId() {
    const type   = document.getElementById('promotionType').value;
    const hidden = document.getElementById('targetIdHidden');
    hidden.value = type === 'category'
        ? document.getElementById('categorySelect').value
        : (type === 'product' ? document.getElementById('productSelect').value : '');
}

// Payment radio visual toggle
const colorMap = { paid: 'emerald-500', unpaid: 'rose-500', waived: 'amber-500' };

document.querySelectorAll('.payment-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.payment-opt').forEach(opt => {
            const val  = opt.dataset.val;
            const col  = colorMap[val];
            const active = val === radio.value;

            opt.classList.remove(
                'border-emerald-500','bg-emerald-500/10','text-emerald-500',
                'border-rose-500','bg-rose-500/10','text-rose-500',
                'border-amber-500','bg-amber-500/10','text-amber-500',
                'border-gold/15','text-muted/60'
            );

            if (active) {
                opt.classList.add(`border-${col}`, `bg-${col}/10`, `text-${col}`);
            } else {
                opt.classList.add('border-gold/15', 'text-muted/60');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', toggleTargetFields);
</script>
@endsection
