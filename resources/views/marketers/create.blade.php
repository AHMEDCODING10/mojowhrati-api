@extends('layouts.admin')

@section('title', __('إضافة مسوق جديد'))

@section('content')
<div class="space-y-10 pb-20" dir="rtl">

    {{-- Header --}}
    <div class="pt-4 pb-8 border-b border-gold/10">
        <div class="flex items-center gap-4">
            <a href="{{ route('marketers.index') }}" class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center hover:bg-gold/20 transition-all">
                <i data-lucide="arrow-right" class="w-5 h-5 text-gold"></i>
            </a>
            <div>
                <h2 class="text-3xl font-black text-main flex items-center gap-3">
                    {{ __('إضافة مسوق جديد') }}
                    <div class="w-10 h-10 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center">
                        <i data-lucide="megaphone" class="w-5 h-5 text-gold"></i>
                    </div>
                </h2>
                <p class="text-xs text-muted/50 font-bold mt-1">{{ __('بيانات المسوق وإعدادات العمولة') }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('marketers.store') }}"
          x-data="{ targetType: 'both' }"
          class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            {{-- ─── Left: Basic Info ────────────────────────── --}}
            <div class="xl:col-span-2 space-y-8">

                {{-- Personal Info --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-gold/3 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                    <div class="flex items-center gap-4 mb-8 relative z-10">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-gold to-[#E8D095] flex items-center justify-center shadow-lg shadow-gold/20">
                            <i data-lucide="user" class="w-5 h-5 text-onyx"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-main">{{ __('البيانات الشخصية') }}</h4>
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('معلومات المسوق الأساسية') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="user" class="w-3 h-3 text-gold/60"></i> {{ __('اسم المسوق') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all @error('name') border-rose-500 @enderror">
                            @error('name') <p class="text-rose-500 text-xs font-bold">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="phone" class="w-3 h-3 text-gold/60"></i> {{ __('رقم الهاتف') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required dir="ltr"
                                   class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all @error('phone') border-rose-500 @enderror">
                            @error('phone') <p class="text-rose-500 text-xs font-bold">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="mail" class="w-3 h-3 text-gold/60"></i> {{ __('البريد الإلكتروني') }}
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" dir="ltr"
                                   class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all">
                        </div>
                    </div>
                </div>

                {{-- Commission Settings --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-gold/3 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                    <div class="flex items-center gap-4 mb-8 relative z-10">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-gold to-[#E8D095] flex items-center justify-center shadow-lg shadow-gold/20">
                            <i data-lucide="coins" class="w-5 h-5 text-onyx"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-main">{{ __('إعدادات العمولة') }}</h4>
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest">{{ __('نوع الحسابات والعمولة لكل حساب') }}</p>
                        </div>
                    </div>
                    <div class="space-y-6 relative z-10">

                        {{-- Target Type --}}
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="target" class="w-3 h-3 text-gold/60"></i> {{ __('نوع الحسابات التي يضيفها المسوق') }} <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['merchant' => ['تجار فقط','store','blue-500'], 'customer' => ['عملاء فقط','users','emerald-500'], 'both' => ['التجار والعملاء','users-2','gold']] as $val => [$lbl, $ico, $col])
                                <label class="cursor-pointer">
                                    <input type="radio" name="target_type" value="{{ $val }}"
                                           x-model="targetType"
                                           {{ old('target_type', 'both') === $val ? 'checked' : '' }}
                                           class="hidden">
                                    <div :class="targetType === '{{ $val }}'
                                                ? 'border-{{ $col }} bg-{{ $col }}/10 text-{{ $col }}'
                                                : 'border-gold/15 bg-white/40 dark:bg-black/20 text-muted/60'"
                                         class="p-4 rounded-2xl border-2 text-center font-black text-xs transition-all duration-200 hover:border-{{ $col }}/50">
                                        <i data-lucide="{{ $ico }}" class="w-6 h-6 mx-auto mb-2"></i>
                                        {{ $lbl }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2">
                            {{-- Currency --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="banknote" class="w-3 h-3 text-gold/60"></i> {{ __('عملة العمولة') }} <span class="text-rose-500">*</span>
                                </label>
                                <select name="currency" class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all appearance-none">
                                    @foreach(['SAR' => 'ريال سعودي (SAR)', 'USD' => 'دولار أمريكي (USD)', 'AED' => 'درهم إماراتي (AED)', 'EGP' => 'جنيه مصري (EGP)', 'KWD' => 'دينار كويتي (KWD)', 'OMR' => 'ريال عُماني (OMR)'] as $code => $name)
                                        <option value="{{ $code }}" {{ old('currency', 'SAR') === $code ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Commission per Merchant --}}
                            <div class="space-y-2" x-show="targetType !== 'customer'">
                                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="store" class="w-3 h-3 text-blue-500/60"></i> {{ __('عمولة كل تاجر') }}
                                </label>
                                <input type="number" name="commission_per_merchant" value="{{ old('commission_per_merchant', 0) }}" min="0" step="0.01"
                                       class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all" dir="ltr">
                            </div>

                            {{-- Commission per Customer --}}
                            <div class="space-y-2" x-show="targetType !== 'merchant'">
                                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="users" class="w-3 h-3 text-emerald-500/60"></i> {{ __('عمولة كل عميل') }}
                                </label>
                                <input type="number" name="commission_per_customer" value="{{ old('commission_per_customer', 0) }}" min="0" step="0.01"
                                       class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all" dir="ltr">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Right: Status & Notes ───────────────────── --}}
            <div class="xl:col-span-1 space-y-6">

                {{-- Status --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-7 shadow-xl">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-gold to-[#E8D095] flex items-center justify-center shadow-lg shadow-gold/20">
                            <i data-lucide="toggle-right" class="w-5 h-5 text-onyx"></i>
                        </div>
                        <h4 class="text-base font-black text-main">{{ __('الحالة') }}</h4>
                    </div>
                    <div class="space-y-3">
                        @foreach(['active' => ['نشط — يعمل ويكسب عمولات', 'emerald-500'], 'inactive' => ['موقوف — لا يُحتسب له عمولة', 'rose-500']] as $val => [$lbl, $col])
                        <label class="cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 transition-all hover:border-{{ $col }}/50
                            {{ old('status', 'active') === $val ? 'border-'.$col.' bg-'.$col.'/10' : 'border-gold/10 bg-white/40 dark:bg-black/20' }}">
                            <input type="radio" name="status" value="{{ $val }}" {{ old('status', 'active') === $val ? 'checked' : '' }} class="mt-0.5 accent-gold">
                            <span class="text-xs font-black text-main">{{ $lbl }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-7 shadow-xl">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-gold to-[#E8D095] flex items-center justify-center shadow-lg shadow-gold/20">
                            <i data-lucide="notebook" class="w-5 h-5 text-onyx"></i>
                        </div>
                        <h4 class="text-base font-black text-main">{{ __('ملاحظات') }}</h4>
                    </div>
                    <textarea name="notes" rows="5"
                              class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all resize-none"
                              placeholder="{{ __('أي ملاحظات إضافية...') }}">{{ old('notes') }}</textarea>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-4 bg-gradient-to-tr from-[#C5A059] to-[#E8D095] text-onyx font-black rounded-2xl shadow-lg shadow-gold/30 hover:shadow-gold/50 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    {{ __('حفظ المسوق') }}
                </button>
                <a href="{{ route('marketers.index') }}"
                   class="block w-full py-3 text-center text-xs font-black text-muted/50 hover:text-muted transition-colors">
                    {{ __('إلغاء') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
