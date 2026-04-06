@extends('layouts.admin')

@section('title', __('الملف الشخصي'))

@section('content')
<div class="space-y-12 pb-20 mt-8" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 px-2">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('إعدادات الحساب') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                {{ __('العودة للرئيسية') }}
            </a>
        </div>
    </div>

    @if(session('status') === 'profile-updated')
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center">
        {{ __('تم تحديث بياناتك بنجاح') }}
    </div>
    @endif

    <!-- Unified Form for Profile Info & Image -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Sidebar: Profile Picture & Identity -->
            <div class="lg:col-span-1 space-y-8">
                <div class="luxury-card p-12 border border-main/10 bg-main/5 space-y-10 text-center relative group overflow-hidden h-fit">
                    <div class="relative w-40 h-40 mx-auto">
                        <!-- Preview Image -->
                        <div class="w-full h-full rounded-full border-4 border-gold/20 flex items-center justify-center bg-gold/5 overflow-hidden shadow-2xl relative z-10">
                            @if($user->profile_image)
                                <img id="preview" src="{{ Storage::disk('public')->url($user->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                <div id="placeholder" class="text-6xl font-black text-gold/30 uppercase flex items-center justify-center w-full h-full">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Upload Overlay -->
                        <label for="profile_image" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-500 rounded-full cursor-pointer group-hover:scale-105">
                            <i data-lucide="camera" class="w-8 h-8 text-gold mb-2"></i>
                            <span class="text-[10px] text-white font-black uppercase tracking-tight">{{ __('تحديث الصورة') }}</span>
                            <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*" onchange="previewProfileImage(this)">
                        </label>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-2xl font-black text-main tracking-widest uppercase">{{ $user->name }}</h4>
                            <p class="text-[11px] text-muted/40 font-black uppercase tracking-widest mt-2">{{ __($user->role) }}</p>
                        </div>
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">{{ __('محقق') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content: Basic Info -->
            <div class="lg:col-span-2 space-y-12">
                <div class="luxury-card p-10 border border-main/10 bg-main/5 relative h-fit">
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-gold text-onyx rounded-xl flex items-center justify-center shadow-2xl border border-white/10">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>

                    <h3 class="text-xl font-black text-main mb-12 uppercase tracking-[0.2em] flex items-center gap-4">
                        {{ __('المعلومات الشخصية') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-[0.1em] mr-1">{{ __('الاسم الكامل') }}</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-luxury w-full py-5 text-sm" placeholder="{{ __('أدخل اسمك الكامل...') }}" required>
                            @error('name') <p class="text-[9px] text-rose-500 font-black mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-[0.1em] mr-1">{{ __('البريد الإلكتروني') }}</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-luxury w-full py-5 text-sm" placeholder="email@example.com" required>
                            @error('email') <p class="text-[9px] text-rose-500 font-black mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-[0.1em] mr-1">{{ __('رقم الهاتف') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-luxury w-full py-5 text-sm" placeholder="+967..." required>
                            @error('phone') <p class="text-[9px] text-rose-500 font-black mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-12">
                        <button type="submit" class="px-20 py-6 bg-gold text-onyx rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl shadow-gold/30 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            {{ __('حفظ كافة التعديلات') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Separate Form for Password Change (Security) -->
    <div class="max-w-4xl mx-auto">
        <div class="luxury-card p-10 border border-main/10 bg-main/5 relative">
            <div class="absolute -top-4 -right-4 w-12 h-12 bg-onyx text-gold rounded-xl flex items-center justify-center shadow-2xl border border-gold/20">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>

            <h3 class="text-xl font-black text-main mb-12 uppercase tracking-[0.2em] flex items-center gap-4">
                {{ __('تغيير كلمة المرور الشخصية') }}
            </h3>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-10">
                @csrf
                @method('put')

                <div class="space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4 md:col-span-2">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-[0.1em] mr-1">{{ __('كلمة المرور الحالية') }}</label>
                            <input type="password" name="current_password" class="input-luxury w-full py-5 text-sm" placeholder="••••••••" required>
                            @error('current_password') <p class="text-[9px] text-rose-500 font-black mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-[0.1em] mr-1">{{ __('كلمة المرور الجديدة') }}</label>
                            <input type="password" name="password" class="input-luxury w-full py-5 text-sm" placeholder="••••••••" required>
                            @error('password') <p class="text-[9px] text-rose-500 font-black mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-[0.1em] mr-1">{{ __('تأكيد كلمة المرور الجديدة') }}</label>
                            <input type="password" name="password_confirmation" class="input-luxury w-full py-5 text-sm" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit" class="px-16 py-5 bg-card border border-main text-muted/40 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:text-gold transition-all">
                        {{ __('تحديث كلمة المرور') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview');
            let placeholder = document.getElementById('placeholder');
            
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                // Remove placeholder and add image
                let parent = placeholder.parentElement;
                parent.innerHTML = `<img id="preview" src="${e.target.result}" class="w-full h-full object-cover">`;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
