@extends('layouts.admin')

@section('title', __('إضافة حملة ترويجية جديدة'))

@section('content')
<div class="space-y-8 pb-20 max-w-4xl mx-auto" dir="rtl">
    <!-- Header -->
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

    <form action="{{ route('promotions.store') }}" method="POST" class="luxury-card p-8 border border-main/10 bg-main/5 rounded-3xl space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Merchant Selection -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">التاجر المستفيد (اختیاري)</label>
                <select name="merchant_id" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
                    <option value="">-- بدون تاجر محدد (حملة عامة) --</option>
                    @foreach($merchants as $merchant)
                    <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>
                        {{ $merchant->store_name }} ({{ $merchant->user?->name }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Promotion Type -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">نوع الترويج *</label>
                <select name="type" id="promotionType" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required onchange="toggleTargetFields()">
                    <option value="category" {{ old('type') == 'category' ? 'selected' : '' }}>ترويج صنف / قسم معين</option>
                    <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>ترويج منتج محدد</option>
                    <option value="merchant_store" {{ old('type') == 'merchant_store' ? 'selected' : '' }}>ترويج المتجر كاملاً</option>
                    <option value="banner" {{ old('type') == 'banner' ? 'selected' : '' }}>بنر ترويجي خاص</option>
                </select>
            </div>

            <!-- Category Select (Conditional) -->
            <div id="categoryContainer">
                <label class="block text-xs font-black uppercase text-muted mb-2">اختر الصنف / القسم المستهدف</label>
                <select name="target_id_category" id="categorySelect" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
                    <option value="">-- اختر القسم --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Select (Conditional) -->
            <div id="productContainer" class="hidden">
                <label class="block text-xs font-black uppercase text-muted mb-2">اختر المنتج المستهدف</label>
                <select name="target_id_product" id="productSelect" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
                    <option value="">-- اختر المنتج --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->title }} ({{ $product->merchant?->store_name }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">عنوان الحملة (عنوان داخلي)</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: ترويج قسم الخواتم - متجر الخليج" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
            </div>

            <!-- Placement -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">مكان الظهور الأولي *</label>
                <select name="placement" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
                    <option value="HOME_TOP">أعلى الشاشة الرئيسية (Home Top)</option>
                    <option value="CATEGORY_TOP">أعلى قائمة القسم (Category Top)</option>
                    <option value="FEATURED_SLIDER">شريط المنتجات المميزة (Featured Slider)</option>
                    <option value="SEARCH_BOOST">أول نتائج البحث (Search Boost)</option>
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">تاريخ البداية (فوري إذا تُرك فارغاً)</label>
                <input type="datetime-local" name="start_at" value="{{ old('start_at') }}" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">تاريخ النهاية (المدة الترويجية)</label>
                <input type="datetime-local" name="end_at" value="{{ old('end_at') }}" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
            </div>

            <!-- Priority -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">درجة الأولوية (Priority Score) *</label>
                <input type="number" name="priority" value="{{ old('priority', 10) }}" min="0" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
                <p class="text-[10px] text-muted/60 mt-1">الرقم الأعلى يعطي أولوية في الظهور قبل باقي المعروضات</p>
            </div>

            <!-- Fee Amount -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">رسوم الترويج المستقطعة ($/ر.س) *</label>
                <input type="number" step="0.01" name="fee_amount" value="{{ old('fee_amount', '50.00') }}" min="0" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
            </div>

            <!-- Payment Status -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">حالة الدفع *</label>
                <select name="payment_status" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع بالكامل</option>
                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>غير مدفوع (معلق)</option>
                    <option value="waived" {{ old('payment_status') == 'waived' ? 'selected' : '' }}>مجاني / ترويج تجريبي</option>
                </select>
            </div>
        </div>

        <input type="hidden" name="target_id" id="targetIdHidden" value="">

        <!-- Notes -->
        <div>
            <label class="block text-xs font-black uppercase text-muted mb-2">ملاحظات إضافية (ملاحظات للأدمن)</label>
            <textarea name="notes" rows="3" placeholder="أية تفاصيل إضافية حول التخفيض أو الاتفاق مع التاجر..." class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold"></textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-main/10">
            <a href="{{ route('promotions.index') }}" class="px-6 py-3 bg-main/10 text-muted rounded-xl font-bold text-xs hover:bg-main/20 transition-all">إلغاء</a>
            <button type="submit" onclick="prepareTargetId()" class="px-8 py-3 bg-gold text-onyx rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-gold/20 hover:scale-105 transition-all">حفظ وتفعيل الترويج</button>
        </div>
    </form>
</div>

<script>
function toggleTargetFields() {
    const type = document.getElementById('promotionType').value;
    const catContainer = document.getElementById('categoryContainer');
    const prodContainer = document.getElementById('productContainer');

    if (type === 'category') {
        catContainer.classList.remove('hidden');
        prodContainer.classList.add('hidden');
    } else if (type === 'product') {
        catContainer.classList.add('hidden');
        prodContainer.classList.remove('hidden');
    } else {
        catContainer.classList.add('hidden');
        prodContainer.classList.add('hidden');
    }
}

function prepareTargetId() {
    const type = document.getElementById('promotionType').value;
    const hidden = document.getElementById('targetIdHidden');
    if (type === 'category') {
        hidden.value = document.getElementById('categorySelect').value;
    } else if (type === 'product') {
        hidden.value = document.getElementById('productSelect').value;
    } else {
        hidden.value = '';
    }
}

document.addEventListener('DOMContentLoaded', toggleTargetFields);
</script>
@endsection
