@extends('layouts.admin')

@section('title', __('تعديل الإعلان'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('تعديل بنر إعلاني') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('banners.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للإعلانات') }}
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

    <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Media & Type -->
            <div class="lg:col-span-1 space-y-8">
                <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-8">
                    <div class="space-y-4">
                        <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('نوع الإعلان') }}</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="image" class="hidden peer" {{ $banner->type == 'image' ? 'checked' : '' }} onchange="toggleMediaType('image')">
                                <div class="py-3 px-4 border border-main/10 rounded-xl text-[10px] font-black uppercase text-center peer-checked:bg-gold peer-checked:text-onyx peer-checked:border-gold transition-all">
                                    {{ __('صورة ثابتة') }}
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="video" class="hidden peer" {{ $banner->type == 'video' ? 'checked' : '' }} onchange="toggleMediaType('video')">
                                <div class="py-3 px-4 border border-main/10 rounded-xl text-[10px] font-black uppercase text-center peer-checked:bg-gold peer-checked:text-onyx peer-checked:border-gold transition-all">
                                    {{ __('فيديو (رابط)') }}
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Image Section -->
                    <div id="image-section" class="{{ $banner->type == 'image' ? '' : 'hidden' }} space-y-6 text-center">
                        <div class="relative w-full aspect-[16/9] rounded-3xl bg-gold/5 border border-gold/10 flex items-center justify-center text-4xl overflow-hidden shadow-2xl">
                            @if($banner->image_url && $banner->type == 'image')
                                <img id="image-preview" src="{{ $banner->image_url }}" class="w-full h-full object-cover">
                                <span id="placeholder-icon" class="opacity-20 text-gold text-5xl hidden">🖼️</span>
                            @else
                                <span id="placeholder-icon" class="opacity-20 text-gold text-5xl">🖼️</span>
                                <img id="image-preview" class="w-full h-full object-cover hidden">
                            @endif
                        </div>
                        <label class="w-full py-4 bg-gold/10 border border-gold/20 text-gold rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gold hover:text-onyx cursor-pointer transition-all inline-flex items-center justify-center gap-3">
                            <i data-lucide="camera" class="w-4 h-4"></i>
                            {{ __('تحديث الصورة') }}
                            <input type="file" name="image" class="hidden" onchange="previewImage(this)">
                        </label>
                    </div>

                    <!-- Video Section -->
                    <div id="video-section" class="{{ $banner->type == 'video' ? '' : 'hidden' }} space-y-6">
                        <div class="relative w-full aspect-[16/9] rounded-3xl bg-onyx border border-gold/10 flex flex-col items-center justify-center text-center p-6 space-y-4 shadow-2xl">
                            <i data-lucide="play-circle" class="w-12 h-12 text-gold opacity-80 {{ $banner->type == 'video' ? 'animate-pulse' : '' }}"></i>
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('رابط الفيديو الحالي') }}</p>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('رابط الفيديو (YouTube/Vimeo)') }}</label>
                            <input type="text" name="video_url" value="{{ $banner->video_url }}" class="input-luxury w-full py-4 text-xs" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main: Ads Settings -->
            <div class="lg:col-span-2 space-y-8">
                <div class="luxury-card p-10 border border-main/10 shadow-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3 col-span-2">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('عنوان الإعلان') }}</label>
                            <input type="text" name="title" value="{{ $banner->title }}" class="input-luxury w-full py-4 text-sm" placeholder="{{ __('أدخل عنواناً جذاباً...') }}" required>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('الجمهور المستهدف') }}</label>
                            <select name="target" class="input-luxury w-full py-4 text-sm bg-onyx">
                                <option value="all" {{ $banner->target == 'all' ? 'selected' : '' }}>{{ __('الجميع (عام)') }}</option>
                                <option value="customer" {{ $banner->target == 'customer' ? 'selected' : '' }}>{{ __('العملاء فقط') }}</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('مكان الظهور') }}</label>
                            <select name="placement" class="input-luxury w-full py-4 text-sm bg-onyx">
                                <option value="HOME_TOP" {{ $banner->placement == 'HOME_TOP' ? 'selected' : '' }}>{{ __('أعلى الشاشة الرئيسية') }}</option>
                                <option value="HOME_SLIDER" {{ $banner->placement == 'HOME_SLIDER' ? 'selected' : '' }}>{{ __('سلايدر العروض الرئيسي') }}</option>
                                <option value="HOME_MIDDLE" {{ $banner->placement == 'HOME_MIDDLE' ? 'selected' : '' }}>{{ __('منتصف الشاشة (بين الأقسام)') }}</option>
                                <option value="PRODUCTS_CATALOG" {{ $banner->placement == 'PRODUCTS_CATALOG' ? 'selected' : '' }}>{{ __('رأس قائمة المنتجات') }}</option>
                                <option value="SEARCH_VIEW" {{ $banner->placement == 'SEARCH_VIEW' ? 'selected' : '' }}>{{ __('فوق واجهة البحث') }}</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('رابط التوجيه (اختياري)') }}</label>
                            <input type="text" name="link" value="{{ $banner->link }}" class="input-luxury w-full py-4 text-sm" placeholder="https://...">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('ترتيب الظهور') }}</label>
                            <input type="number" name="position" value="{{ $banner->position }}" class="input-luxury w-full py-4 text-sm">
                        </div>

                        <!-- Scheduling -->
                        <div class="pt-8 col-span-2 border-t border-main/5">
                            <h4 class="text-xs font-black text-main mb-6 uppercase tracking-widest flex items-center gap-3">
                                <i data-lucide="calendar" class="w-4 h-4 text-gold"></i>
                                {{ __('الجدولة الزمنية') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('تاريخ البدء') }}</label>
                                    <input type="datetime-local" name="start_at" value="{{ $banner->start_at ? $banner->start_at->format('Y-m-d\TH:i') : '' }}" class="input-luxury w-full py-4 text-sm">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('تاريخ الانتهاء') }}</label>
                                    <input type="datetime-local" name="end_at" value="{{ $banner->end_at ? $banner->end_at->format('Y-m-d\TH:i') : '' }}" class="input-luxury w-full py-4 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-20 py-6 bg-gradient-to-tr from-gold to-[#E8D095] text-onyx rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-[0_20px_40px_rgba(212,175,55,0.3)] hover:scale-105 active:scale-95 transition-all">
                        {{ __('تحديث الإعلان الملكي') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleMediaType(type) {
        const imageSec = document.getElementById('image-section');
        const videoSec = document.getElementById('video-section');
        
        if (type === 'video') {
            imageSec.classList.add('hidden');
            videoSec.classList.remove('hidden');
        } else {
            imageSec.classList.remove('hidden');
            videoSec.classList.add('hidden');
        }
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
                document.getElementById('placeholder-icon').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
