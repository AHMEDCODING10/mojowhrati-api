@extends('layouts.admin')

@section('title', __('تعديل القسم'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('تعديل بيانات القسم') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('categories.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للأقسام') }}
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

    <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Image Upload -->
            <div class="lg:col-span-1 space-y-8">
                <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-8">
                    <div class="text-center space-y-6">
                        <div class="relative w-48 h-48 mx-auto group">
                            <div class="w-full h-full rounded-3xl bg-gold/5 border border-gold/10 flex items-center justify-center text-4xl overflow-hidden shadow-2xl">
                                @if($category->image_url)
                                    <img src="{{ $category->image_url }}" id="image-preview" class="w-full h-full object-cover">
                                @else
                                    <span id="placeholder-icon" class="opacity-20 text-gold text-5xl">📁</span>
                                    <img id="image-preview" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <label class="absolute -bottom-4 left-1/2 -translate-x-1/2 p-3 bg-gold text-onyx rounded-full shadow-xl hover:scale-110 cursor-pointer transition-all">
                                <i data-lucide="camera" class="w-5 h-5"></i>
                                <input type="file" name="image" class="hidden" onchange="previewImage(this)">
                            </label>
                        </div>
                        <h4 class="text-sm font-black text-main tracking-widest uppercase">{{ __('صورة القسم') }}</h4>
                        <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest leading-relaxed">{{ __('يفضل استخدام صور شفافة بدقة عالية للتنسيق الفاخر') }}</p>
                    </div>
                </div>
            </div>

            <!-- Main: Category Details -->
            <div class="lg:col-span-2 space-y-8">
                <div class="luxury-card p-10 border border-main/10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('اسم القسم (بالعربية)') }}</label>
                            <input type="text" name="name" value="{{ $category->name }}" class="input-luxury w-full py-4 text-sm" placeholder="{{ __('أدخل اسم القسم...') }}" required>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('القسم الرئيسي') }}</label>
                            <select name="parent_id" class="input-luxury w-full py-4 text-sm">
                                <option value="">{{ __('لا يوجد (قسم رئيسي)') }}</option>
                                @foreach($parentCategories as $parent)
                                    @if($parent->id != $category->id)
                                        <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('ترتيب العرض') }}</label>
                            <input type="number" name="display_order" value="{{ $category->display_order }}" class="input-luxury w-full py-4 text-sm" placeholder="10">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 gap-4">
                    <button type="submit" class="px-16 py-5 bg-gold text-onyx rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl shadow-gold/30 hover:scale-105 active:scale-95 transition-all">
                        {{ __('حفظ التعديلات') }}
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
