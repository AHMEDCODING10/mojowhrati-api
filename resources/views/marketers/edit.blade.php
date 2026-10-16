@extends('layouts.admin')

@section('title', __('تعديل المسوق'))

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
                    {{ __('تعديل المسوق') }}: {{ $marketer->name }}
                </h2>
                <p class="text-xs text-muted/50 font-bold mt-1">{{ __('رمز الإحالة') }}: <code class="text-gold font-black tracking-widest">{{ $marketer->code }}</code></p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('marketers.update', $marketer->id) }}"
          x-data="{ targetType: '{{ old('target_type', $marketer->target_type) }}' }"
          class="space-y-8">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            <div class="xl:col-span-2 space-y-8">
                {{-- Personal Info --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-8 shadow-xl">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-gold to-[#E8D095] flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-onyx"></i>
                        </div>
                        <h4 class="text-lg font-black text-main">{{ __('البيانات الشخصية') }}</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest">{{ __('اسم المسوق') }} <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $marketer->name) }}" required
                                   class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest">{{ __('رقم الهاتف') }} <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $marketer->phone) }}" required dir="ltr"
                                   class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm">
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest">{{ __('البريد الإلكتروني') }}</label>
                            <input type="email" name="email" value="{{ old('email', $marketer->email) }}" dir="ltr"
                                   class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm">
                        </div>
                    </div>
                </div>

                {{-- Commission Settings --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-8 shadow-xl">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-gold to-[#E8D095] flex items-center justify-center">
                            <i data-lucide="coins" class="w-5 h-5 text-onyx"></i>
                        </div>
                        <h4 class="text-lg font-black text-main">{{ __('إعدادات العمولة') }}</h4>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(['merchant' => ['تجار فقط','store','blue-500'], 'customer' => ['عملاء فقط','users','emerald-500'], 'both' => ['التجار والعملاء','users-2','gold']] as $val => [$lbl, $ico, $col])
                            <label class="cursor-pointer">
                                <input type="radio" name="target_type" value="{{ $val }}" x-model="targetType" class="hidden">
                                <div :class="targetType === '{{ $val }}' ? 'border-{{ $col }} bg-{{ $col }}/10 text-{{ $col }}' : 'border-gold/15 bg-white/40 dark:bg-black/20 text-muted/60'"
                                     class="p-4 rounded-2xl border-2 text-center font-black text-xs transition-all">
                                    <i data-lucide="{{ $ico }}" class="w-6 h-6 mx-auto mb-2"></i>{{ $lbl }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest">{{ __('عملة العمولة') }}</label>
                                <select name="currency" class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold outline-none text-main font-bold text-sm">
                                    @foreach(['SAR','USD','AED','EGP','KWD','OMR'] as $code)
                                        <option value="{{ $code }}" {{ old('currency', $marketer->currency) === $code ? 'selected' : '' }}>{{ $code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2" x-show="targetType !== 'customer'">
                                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest">{{ __('عمولة كل تاجر') }}</label>
                                <input type="number" name="commission_per_merchant" value="{{ old('commission_per_merchant', $marketer->commission_per_merchant) }}" min="0" step="0.01" dir="ltr"
                                       class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold outline-none text-main font-bold text-sm">
                            </div>
                            <div class="space-y-2" x-show="targetType !== 'merchant'">
                                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest">{{ __('عمولة كل عميل') }}</label>
                                <input type="number" name="commission_per_customer" value="{{ old('commission_per_customer', $marketer->commission_per_customer) }}" min="0" step="0.01" dir="ltr"
                                       class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold outline-none text-main font-bold text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-1 space-y-6">
                {{-- Referral Code --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-6 shadow-xl">
                    <p class="text-[10px] font-black text-muted/60 uppercase tracking-widest mb-3">{{ __('رمز الإحالة') }}</p>
                    <code class="block text-center text-xl font-black text-gold tracking-[0.3em] bg-gold/10 border border-gold/20 rounded-2xl py-4 mb-4">{{ $marketer->code }}</code>
                    <form method="POST" action="{{ route('marketers.regenerate-code', $marketer->id) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('سيتم تغيير رمز الإحالة القديم. هل أنت متأكد؟')"
                                class="w-full py-2.5 text-xs font-black text-amber-600 dark:text-amber-400 border border-amber-500/30 rounded-xl hover:bg-amber-500/10 transition-all">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 inline ml-1"></i>
                            {{ __('تجديد الرمز') }}
                        </button>
                    </form>
                </div>

                {{-- Status --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-6 shadow-xl space-y-3">
                    <h4 class="text-base font-black text-main mb-4">{{ __('الحالة') }}</h4>
                    @foreach(['active' => ['نشط','emerald-500'], 'inactive' => ['موقوف','rose-500']] as $val => [$lbl, $col])
                    <label class="cursor-pointer flex items-center gap-3 p-4 rounded-2xl border-2 transition-all {{ old('status', $marketer->status) === $val ? 'border-'.$col.' bg-'.$col.'/10' : 'border-gold/10' }}">
                        <input type="radio" name="status" value="{{ $val }}" {{ old('status', $marketer->status) === $val ? 'checked' : '' }} class="accent-gold">
                        <span class="text-xs font-black text-main">{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Notes --}}
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-6 shadow-xl">
                    <h4 class="text-base font-black text-main mb-4">{{ __('ملاحظات') }}</h4>
                    <textarea name="notes" rows="4"
                              class="w-full px-4 py-3 rounded-2xl bg-white dark:bg-black/40 border border-gold/20 focus:border-gold outline-none text-main font-bold text-sm resize-none">{{ old('notes', $marketer->notes) }}</textarea>
                </div>

                <button type="submit"
                        class="w-full py-4 bg-gradient-to-tr from-[#C5A059] to-[#E8D095] text-onyx font-black rounded-2xl shadow-lg hover:shadow-gold/40 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    {{ __('حفظ التعديلات') }}
                </button>
                <a href="{{ route('marketers.index') }}" class="block w-full py-3 text-center text-xs font-black text-muted/50 hover:text-muted transition-colors">
                    {{ __('إلغاء') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
