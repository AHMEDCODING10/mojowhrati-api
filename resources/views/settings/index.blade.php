@extends('layouts.admin')

@section('title', __('تخصيص النظام'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-10 pt-4 pb-8 border-b border-gold/10 relative">
        <div class="absolute right-0 top-0 w-32 h-32 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative">
            <h2 class="text-4xl lg:text-5xl font-black text-main uppercase tracking-[0.2em] leading-tight flex items-center gap-4">
                {{ __('تخصيص النظام') }}
                <div class="w-12 h-12 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-lg shadow-gold/10">
                    <i data-lucide="settings-2" class="w-6 h-6 text-gold animate-spin-slow"></i>
                </div>
            </h2>
            <div class="flex items-center gap-3 mt-4">
                <div class="h-1.5 w-20 bg-gold shadow-[0_0_20px_rgba(212,175,55,0.6)] rounded-full"></div>
                <div class="h-1.5 w-6 bg-gold/30 rounded-full"></div>
                <p class="text-[10px] text-muted/60 font-black uppercase tracking-[0.3em] mr-2">{{ __('إعدادات وتفضيلات الواجهة') }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="px-8 py-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl font-black text-sm text-center shadow-lg animate-in fade-in slide-in-from-top-4 duration-700 flex items-center justify-center gap-3">
        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" class="space-y-8 relative z-10">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <!-- Sidebar: Preferences & Appearance -->
            <div class="xl:col-span-1 space-y-8">
                
                <!-- System Appearance & Language -->
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-gold/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-gold to-[#E8D095] text-onyx flex items-center justify-center shadow-lg shadow-gold/20">
                            <i data-lucide="palette" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-main tracking-tight">{{ __('المظهر والتفضيلات') }}</h4>
                            <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest mt-1">{{ __('اللغة والوضع الليلي') }}</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- Theme Toggle (Visual representation via Javascript in Alpine) -->
                        <div class="space-y-4">
                            <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                <i data-lucide="moon" class="w-3.5 h-3.5 text-gold/60"></i>
                                {{ __('سمة الواجهة') }}
                            </label>
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-black/40 border border-gold/10 justify-between">
                                <div class="flex items-center gap-3 text-sm font-bold text-main">
                                    <template x-if="!darkMode">
                                        <i data-lucide="sun" class="w-5 h-5 text-gold"></i>
                                    </template>
                                    <template x-if="darkMode">
                                        <i data-lucide="moon" class="w-5 h-5 text-gold"></i>
                                    </template>
                                    <span x-text="darkMode ? '{{ __('الوضع المظلم') }}' : '{{ __('الوضع المضيء') }}'"></span>
                                </div>
                                <button type="button" @click="darkMode = !darkMode; toggleThemePersistent(darkMode);" class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none" :class="darkMode ? 'bg-gold' : 'bg-main/20 border border-main/10'">
                                    <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform" :class="darkMode ? '-translate-x-7' : '-translate-x-1'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Language -->
                        <div class="space-y-4 pt-6 border-t border-gold/10">
                            <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                <i data-lucide="languages" class="w-3.5 h-3.5 text-gold/60"></i>
                                {{ __('لغة النظام الافتراضية') }}
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('settings.lang', 'ar') }}" class="py-4 border rounded-2xl text-[10px] font-black uppercase tracking-widest text-center transition-all duration-300 {{ app()->getLocale() == 'ar' ? 'bg-gold/10 border-gold shadow-lg shadow-gold/20 text-gold' : 'border-gold/10 bg-white dark:bg-black/20 text-muted/40 hover:border-gold/40 hover:text-gold' }} flex flex-col items-center gap-2">
                                    <span class="text-xl">🇦🇪</span>
                                    <span>{{ __('العربية') }}</span>
                                </a>
                                <a href="{{ route('settings.lang', 'en') }}" class="py-4 border rounded-2xl text-[10px] font-black uppercase tracking-widest text-center transition-all duration-300 {{ app()->getLocale() == 'en' ? 'bg-gold/10 border-gold shadow-lg shadow-gold/20 text-gold' : 'border-gold/10 bg-white dark:bg-black/20 text-muted/40 hover:border-gold/40 hover:text-gold' }} flex flex-col items-center gap-2">
                                    <span class="text-xl">🇺🇸</span>
                                    <span>English</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Content: General & Permissions -->
            <div class="xl:col-span-2 space-y-8">
                
                <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden group/card">
                    <!-- Glow effect -->
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-gold/5 rounded-full blur-3xl group-hover/card:bg-gold/10 transition-colors duration-700 pointer-events-none"></div>

                    <!-- General Settings -->
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-xl bg-gold/10 text-gold border border-gold/20 flex items-center justify-center shadow-lg group-hover/card:bg-gold group-hover/card:text-onyx transition-all duration-500">
                            <i data-lucide="globe" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-black text-main tracking-tight">{{ __('إعدادات الموقع العام') }}</h4>
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-1">{{ __('معلومات التطبيق الأساسية') }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                <i data-lucide="store" class="w-3.5 h-3.5 text-gold/60"></i>
                                {{ __('اسم المنصة') }}
                            </label>
                            <input type="text" name="app_name" value="{{ config('app.name') }}" required 
                                   class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-base font-black text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all placeholder:text-muted/30 placeholder:font-normal" 
                                   placeholder="{{ __('أدخل اسم الموقع...') }}">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                <i data-lucide="mail-check" class="w-3.5 h-3.5 text-gold/60"></i>
                                {{ __('البريد الإلكتروني المرجعي') }}
                            </label>
                            <input type="email" name="contact_email" value="{{ \App\Models\Setting::get('contact_email', 'admin@mojawharati.pro') }}" required 
                                   class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-base font-black text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all placeholder:text-muted/30 placeholder:font-normal text-left" 
                                   dir="ltr" placeholder="admin@example.com">
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gold/10 to-transparent my-10"></div>

                    <!-- System Permissions -->
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-xl bg-gold/10 text-gold border border-gold/20 flex items-center justify-center shadow-lg group-hover/card:bg-gold group-hover/card:text-onyx transition-all duration-500">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-black text-main tracking-tight">{{ __('صلاحيات النظام') }}</h4>
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-1">{{ __('قواعد التسجيل والوصول') }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="flex items-start justify-between p-6 bg-white dark:bg-black/20 rounded-2xl border border-gold/10 hover:border-gold/30 transition-all duration-300 shadow-sm cursor-pointer" onclick="document.getElementById('allow_registration').click()">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                                    </div>
                                    <p class="text-base font-black text-main">{{ __('تسجيل الأعضاء الجدد') }}</p>
                                </div>
                                <p class="text-[10px] text-muted/40 font-black tracking-widest leading-relaxed">{{ __('السماح للزوار بإنشاء حسابات جديدة تلقائياً من خلال التطبيق أو الموقع.') }}</p>
                            </div>
                            <!-- Custom Checkbox/Toggle -->
                            <div x-data="{ checked: {{ \App\Models\Setting::get('allow_registration', true) ? 'true' : 'false' }} }" class="mt-1 flex-shrink-0 ml-4">
                                <input type="checkbox" name="allow_registration" id="allow_registration" value="1" x-model="checked" class="hidden">
                                <button type="button" @click.stop="checked = !checked; document.getElementById('allow_registration').checked = checked" class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-gold/30 focus:ring-offset-2 focus:ring-offset-transparent shadow-inner" :class="checked ? 'bg-emerald-500' : 'bg-main/20 border border-main/10'">
                                    <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform shadow-md" :class="checked ? '-translate-x-9' : '-translate-x-1'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-2">
                    <button type="submit" class="group relative px-14 py-5 bg-gold text-onyx font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl shadow-gold/20 hover:shadow-gold/40 hover:-translate-y-1 transition-all duration-500 flex items-center gap-4 overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 rounded-2xl"></div>
                        <i data-lucide="check-circle-2" class="w-5 h-5 relative z-10"></i>
                        <span class="relative z-10">{{ __('حفظ التغييرات') }}</span>
                    </button>
                </div>
                
            </div>
            
        </div>
    </form>
</div>
@endsection
