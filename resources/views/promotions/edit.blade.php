@extends('layouts.admin')

@section('title', __('تعديل الحملة الترويجية'))

@section('content')
<div class="space-y-8 pb-20 max-w-4xl mx-auto" dir="rtl">
    <!-- Header -->
    <div class="pt-6">
        <h2 class="text-3xl font-black text-main uppercase tracking-widest mb-2">{{ __('تعديل الحملة الترويجية') }}</h2>
        <p class="text-xs text-muted/60 font-bold">{{ __('تعديل بيانات وأولوية وتاريخ انتهاء الحملة الترويجية') }}</p>
        <div class="h-1.5 w-20 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full mt-3"></div>
    </div>

    @if ($errors->any())
    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-500 text-sm font-bold space-y-1">
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('promotions.update', $promotion->id) }}" method="POST" class="luxury-card p-8 border border-main/10 bg-main/5 rounded-3xl space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Merchant Selection -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">التاجر المستفيد (اختیاري)</label>
                <select name="merchant_id" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
                    <option value="">-- بدون تاجر محدد (حملة عامة) --</option>
                    @foreach($merchants as $merchant)
                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $promotion->merchant_id) == $merchant->id ? 'selected' : '' }}>
                        {{ $merchant->store_name }} ({{ $merchant->user?->name }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Promotion Type -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">نوع الترويج *</label>
                <select name="type" id="promotionType" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required onchange="toggleTargetFields()">
                    <option value="category" {{ old('type', $promotion->type) == 'category' ? 'selected' : '' }}>ترويج صنف / قسم معين</option>
                    <option value="product" {{ old('type', $promotion->type) == 'product' ? 'selected' : '' }}>ترويج منتج محدد</option>
                    <option value="merchant_store" {{ old('type', $promotion->type) == 'merchant_store' ? 'selected' : '' }}>ترويج المتجر كاملاً</option>
                    <option value="banner" {{ old('type', $promotion->type) == 'banner' ? 'selected' : '' }}>بنر ترويجي خاص</option>
                </select>
            </div>

            <!-- Category Select (Conditional) -->
            <div id="categoryContainer">
                <label class="block text-xs font-black uppercase text-muted mb-2">اختر الصنف / القسم المستهدف</label>
                <select name="target_id_category" id="categorySelect" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
                    <option value="">-- اختر القسم --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $promotion->type == 'category' && $promotion->target_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Select (Conditional) -->
            <div id="productContainer" class="hidden">
                <label class="block text-xs font-black uppercase text-muted mb-2">اختر المنتج المستهدف</label>
                <select name="target_id_product" id="productSelect" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
                    <option value="">-- اختر المنتج --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ $promotion->type == 'product' && $promotion->target_id == $product->id ? 'selected' : '' }}>{{ $product->title }} ({{ $product->merchant?->store_name }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">عنوان الحملة (عنوان داخلي)</label>
                <input type="text" name="title" value="{{ old('title', $promotion->title) }}" placeholder="مثال: ترويج قسم الخواتم" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
            </div>

            <!-- Placement -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">مكان الظهور الأولي *</label>
                <select name="placement" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
                    <option value="HOME_TOP" {{ old('placement', $promotion->placement) == 'HOME_TOP' ? 'selected' : '' }}>أعلى الشاشة الرئيسية (Home Top)</option>
                    <option value="CATEGORY_TOP" {{ old('placement', $promotion->placement) == 'CATEGORY_TOP' ? 'selected' : '' }}>أعلى قائمة القسم (Category Top)</option>
                    <option value="FEATURED_SLIDER" {{ old('placement', $promotion->placement) == 'FEATURED_SLIDER' ? 'selected' : '' }}>شريط المنتجات المميزة (Featured Slider)</option>
                    <option value="SEARCH_BOOST" {{ old('placement', $promotion->placement) == 'SEARCH_BOOST' ? 'selected' : '' }}>أول نتائج البحث (Search Boost)</option>
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">تاريخ البداية</label>
                <input type="datetime-local" name="start_at" value="{{ old('start_at', $promotion->start_at?->format('Y-m-d\TH:i')) }}" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">تاريخ النهاية</label>
                <input type="datetime-local" name="end_at" value="{{ old('end_at', $promotion->end_at?->format('Y-m-d\TH:i')) }}" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">
            </div>

            <!-- Priority -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">درجة الأولوية (Priority Score) *</label>
                <input type="number" name="priority" value="{{ old('priority', $promotion->priority) }}" min="0" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
            </div>

            <!-- Fee Amount -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">رسوم الترويج المستقطعة ($/ر.س) *</label>
                <input type="number" step="0.01" name="fee_amount" value="{{ old('fee_amount', $promotion->fee_amount) }}" min="0" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
            </div>

            <!-- Payment Status -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">حالة الدفع *</label>
                <select name="payment_status" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
                    <option value="paid" {{ old('payment_status', $promotion->payment_status) == 'paid' ? 'selected' : '' }}>مدفوع بالكامل</option>
                    <option value="unpaid" {{ old('payment_status', $promotion->payment_status) == 'unpaid' ? 'selected' : '' }}>غير مدفوع (معلق)</option>
                    <option value="waived" {{ old('payment_status', $promotion->payment_status) == 'waived' ? 'selected' : '' }}>مجاني / ترويج تجريبي</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-black uppercase text-muted mb-2">حالة الترويج *</label>
                <select name="status" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold" required>
                    <option value="active" {{ old('status', $promotion->status) == 'active' ? 'selected' : '' }}>نشط (فعال)</option>
                    <option value="paused" {{ old('status', $promotion->status) == 'paused' ? 'selected' : '' }}>موقوف مؤقتاً</option>
                    <option value="expired" {{ old('status', $promotion->status) == 'expired' ? 'selected' : '' }}>منتهي</option>
                </select>
            </div>
        </div>

        <input type="hidden" name="target_id" id="targetIdHidden" value="{{ $promotion->target_id }}">

        <!-- Notes -->
        <div>
            <label class="block text-xs font-black uppercase text-muted mb-2">ملاحظات إضافية</label>
            <textarea name="notes" rows="3" class="w-full bg-main/10 border border-main/20 rounded-xl p-3 text-main font-bold outline-none focus:border-gold">{{ old('notes', $promotion->notes) }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-main/10">
            <a href="{{ route('promotions.index') }}" class="px-6 py-3 bg-main/10 text-muted rounded-xl font-bold text-xs hover:bg-main/20 transition-all">إلغاء</a>
            <button type="submit" onclick="prepareTargetId()" class="px-8 py-3 bg-gold text-onyx rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-gold/20 hover:scale-105 transition-all">تحديث الترويج</button>
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
    }
}

document.addEventListener('DOMContentLoaded', toggleTargetFields);
</script>
@endsection
