@extends('layouts.admin')

@section('title', __('تعديل المنتج'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('تعديل بيانات المنتج') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('products.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للمنتجات') }}
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="p-6 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-3xl font-black text-sm text-center">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Image & Quick Actions -->
            <div class="lg:col-span-1 space-y-8">
                <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-8">
                    <div class="text-center space-y-6">
                        <div class="relative w-full aspect-square mx-auto group">
                            <div class="w-full h-full rounded-3xl bg-gold/5 border border-gold/10 flex items-center justify-center text-4xl overflow-hidden shadow-2xl">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" id="image-preview" class="w-full h-full object-cover">
                                @else
                                    <span id="placeholder-icon" class="opacity-20 text-gold text-5xl">💎</span>
                                    <img id="image-preview" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <label class="absolute -bottom-4 left-1/2 -translate-x-1/2 p-4 bg-gold text-onyx rounded-full shadow-xl hover:scale-110 cursor-pointer transition-all">
                                <i data-lucide="camera" class="w-6 h-6"></i>
                                <input type="file" name="image" class="hidden" onchange="previewImage(this)">
                            </label>
                        </div>
                        <h4 class="text-sm font-black text-main tracking-widest uppercase">{{ __('صورة المنتج الأساسية') }}</h4>
                    </div>

                    <div class="pt-6 border-t border-gold/10 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('منتج مميز') }}</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" class="sr-only peer" {{ $product->is_featured ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-main/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('إدارة المخزون') }}</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="manage_stock" value="1" class="sr-only peer" {{ $product->manage_stock ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-main/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main: Product Details -->
            <div class="lg:col-span-2 space-y-8">
                <div class="luxury-card p-10 border border-main/10 bg-card">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Basic Info -->
                        <div class="md:col-span-2 space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('عنوان المنتج') }}</label>
                            <input type="text" name="title" value="{{ $product->title }}" class="input-luxury w-full py-4 px-6 text-sm" placeholder="{{ __('أدخل عنوان المنتج...') }}" required>
                        </div>

                        <div class="md:col-span-2 space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('وصف المنتج') }}</label>
                            <textarea name="description" rows="4" class="input-luxury w-full py-4 px-6 text-sm resize-none" placeholder="{{ __('اكتب تفاصيل المنتج هنا...') }}">{{ $product->description }}</textarea>
                        </div>

                        <!-- Categorization -->
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('القسم') }}</label>
                            <select name="category_id" class="input-luxury w-full py-4 px-6 text-sm appearance-none" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('التاجر') }}</label>
                            <select name="merchant_id" class="input-luxury w-full py-4 px-6 text-sm appearance-none" required>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ $product->merchant_id == $merchant->id ? 'selected' : '' }}>{{ $merchant->store_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Physical Stats -->
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('الوزن (جرام)') }}</label>
                            <input type="number" step="0.01" name="weight" value="{{ $product->weight }}" class="input-luxury w-full py-4 px-6 text-sm" placeholder="0.00" required>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('العيار / المادة') }}</label>
                            <select name="material_id" class="input-luxury w-full py-4 px-6 text-sm appearance-none" required>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" {{ $product->material_id == $material->id ? 'selected' : '' }}>{{ $material->name }} ({{ $material->karat }}K)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Inventory & Others -->
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('الكمية المتوفرة') }}</label>
                            <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="input-luxury w-full py-4 px-6 text-sm" placeholder="1">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('المصنع / العلامة التجارية') }}</label>
                            <input type="text" name="manufacturer" value="{{ $product->manufacturer }}" class="input-luxury w-full py-4 px-6 text-sm" placeholder="{{ __('مثال: لازوردي') }}">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('الحالة') }}</label>
                            <select name="status" class="input-luxury w-full py-4 px-6 text-sm appearance-none" required>
                                <option value="pending" {{ $product->status == 'pending' ? 'selected' : '' }}>{{ __('قيد المراجعة') }}</option>
                                <option value="published" {{ $product->status == 'published' ? 'selected' : '' }}>{{ __('منشور') }}</option>
                                <option value="out_of_stock" {{ $product->status == 'out_of_stock' ? 'selected' : '' }}>{{ __('نفدت الكمية') }}</option>
                                <option value="rejected" {{ $product->status == 'rejected' ? 'selected' : '' }}>{{ __('مرفوض') }}</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('رسوم الخدمة (اختياري)') }}</label>
                            <input type="number" name="service_fee" value="{{ $product->service_fee }}" class="input-luxury w-full py-4 px-6 text-sm" placeholder="0">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-20 py-5 bg-gold text-onyx rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl shadow-gold/30 hover:scale-105 active:scale-95 transition-all">
                        {{ __('حفظ التغييرات') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
                var placeholder = document.getElementById('placeholder-icon');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
