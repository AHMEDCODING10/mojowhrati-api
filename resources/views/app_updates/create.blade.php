@extends('layouts.admin')

@section('title', __('إضافة تحديث جديد للتطبيق'))

@section('content')
<div class="space-y-12 pb-20 max-w-4xl mx-auto" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pt-6 border-b border-main/10 pb-8">
        <div>
            <h2 class="text-3xl font-black text-main uppercase tracking-widest mb-2">{{ __('إصدار وتعميم تحديث جديد') }}</h2>
            <p class="text-xs text-muted/50 font-bold">{{ __('قم بتعبئة بيانات الإصدار الجديد وإرساله للمستخدمين والتطبيق فورياً') }}</p>
        </div>
        <a href="{{ route('banners.index') }}" class="px-6 py-3 bg-main/5 border border-main/10 text-main rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gold hover:text-onyx transition-all">
            {{ __('إلغاء والعودة') }}
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('app-updates.store') }}" method="POST" enctype="multipart/form-data" class="luxury-card p-8 border border-main/10 space-y-8 bg-main/5 rounded-3xl">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Version Number -->
            <div class="space-y-3">
                <label class="block text-xs font-black text-main uppercase tracking-widest">{{ __('رقم الإصدار (Version Number)') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="version_number" required placeholder="مثال: v1.2.0 أو 2.0.1" value="{{ old('version_number', 'v1.0.1') }}" class="input-luxury w-full py-4 text-sm font-bold">
                <p class="text-[9px] text-muted/40 font-bold">{{ __('يجب أن يكون أعلى من رقم الإصدار الحالي للتطبيق') }}</p>
            </div>

            <!-- Target Audience -->
            <div class="space-y-3">
                <label class="block text-xs font-black text-main uppercase tracking-widest">{{ __('الفئة المستهدفة بالتحديث') }} <span class="text-rose-500">*</span></label>
                <select name="target_audience" required class="input-luxury w-full py-4 text-sm font-bold bg-card">
                    <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>{{ __('الكل (العملاء والتجار)') }}</option>
                    <option value="customer" {{ old('target_audience') == 'customer' ? 'selected' : '' }}>{{ __('العملاء فقط') }}</option>
                    <option value="merchant" {{ old('target_audience') == 'merchant' ? 'selected' : '' }}>{{ __('التجار فقط') }}</option>
                </select>
            </div>
        </div>

        <!-- Release Notes / New Features -->
        <div class="space-y-3">
            <label class="block text-xs font-black text-main uppercase tracking-widest">{{ __('الميزات الجديدة والتحسينات (Release Notes)') }} <span class="text-rose-500">*</span></label>
            <textarea name="release_notes" rows="5" required placeholder="اكتب الميزات الجديدة والتحسينات في هذا الإصدار (كل ميزة في سطر)..." class="input-luxury w-full p-4 text-sm font-bold leading-relaxed">{{ old('release_notes') }}</textarea>
        </div>

        <!-- Download Sources (Platform APK & Play Store) -->
        <div class="p-6 bg-card border border-gold/20 rounded-3xl space-y-6">
            <h4 class="text-sm font-black text-gold uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i> {{ __('مصادر التحميل والتنزيل') }}
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Upload APK File -->
                <div class="space-y-3">
                    <label class="block text-xs font-black text-main uppercase tracking-widest">{{ __('رفع ملف التطبيق المباشر (APK File)') }}</label>
                    <input type="file" name="apk_file" accept=".apk,.bin" class="input-luxury w-full py-3 text-xs">
                    <p class="text-[9px] text-muted/40 font-bold">{{ __('ملف APK ليتم تحميله مباشرة من سيرفر المنصة') }}</p>
                </div>

                <!-- Or External APK URL -->
                <div class="space-y-3">
                    <label class="block text-xs font-black text-main uppercase tracking-widest">{{ __('أو رابط APK مباشر خارجي') }}</label>
                    <input type="url" name="apk_url" placeholder="https://example.com/builds/app.apk" value="{{ old('apk_url') }}" class="input-luxury w-full py-4 text-xs font-bold">
                </div>
            </div>

            <!-- Google Play Store URL -->
            <div class="space-y-3 pt-4 border-t border-main/5">
                <label class="block text-xs font-black text-main uppercase tracking-widest">{{ __('رابط صفحة التطبيق على سوق بلاي (Google Play Store URL)') }}</label>
                <input type="url" name="play_store_url" placeholder="https://play.google.com/store/apps/details?id=com.mojohrti.pro" value="{{ old('play_store_url') }}" class="input-luxury w-full py-4 text-xs font-bold">
            </div>
        </div>

        <!-- Schedule & Force Update Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
            <!-- Scheduled Date/Time -->
            <div class="space-y-3">
                <label class="block text-xs font-black text-main uppercase tracking-widest">{{ __('تاريخ ووقت الإرسال والتعميم') }}</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', now()->format('Y-m-d\TH:i')) }}" class="input-luxury w-full py-4 text-xs font-bold">
                <p class="text-[9px] text-muted/40 font-bold">{{ __('اتركه كما هو للإرسال المباشر والفوري الآن') }}</p>
            </div>

            <!-- Force Update Checkbox -->
            <div class="space-y-3 flex flex-col justify-center">
                <label class="flex items-center gap-3 cursor-pointer p-4 bg-card border border-main/10 rounded-2xl">
                    <input type="checkbox" name="is_force_update" value="1" {{ old('is_force_update') ? 'checked' : '' }} class="w-5 h-5 rounded text-gold focus:ring-gold border-main/20">
                    <div>
                        <span class="text-xs font-black text-main block">{{ __('تحديث إجباري (Force Update)') }}</span>
                        <span class="text-[9px] text-muted/40 font-bold">{{ __('يلزم المستخدم بالتحديث قبل استكمال استخدام التطبيق') }}</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-main/10 flex justify-end">
            <button type="submit" class="px-12 py-5 bg-gradient-to-tr from-gold to-[#E8D095] text-black font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-gold/20 hover:scale-105 transition-all flex items-center gap-3">
                <i data-lucide="send" class="w-4 h-4"></i>
                {{ __('إرسال وبث التحديث الآن') }}
            </button>
        </div>
    </form>
</div>
@endsection
