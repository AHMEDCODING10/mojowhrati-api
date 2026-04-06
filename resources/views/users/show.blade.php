@extends('layouts.admin')

@section('title', __('تفاصيل المستخدم'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('الملف الشخصي للمستخدم') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('users.edit', $user->id) }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest hover:text-gold transition-all flex items-center gap-3">
                <i data-lucide="user-cog" class="w-4 h-4"></i>
                {{ __('تعديل البيانات') }}
            </a>
            <a href="{{ route('users.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للقائمة') }}
            </a>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-8">
            <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-10">
                <div class="flex flex-col items-center gap-6">
                    <div class="w-24 h-24 rounded-full bg-gold/5 border border-gold/10 flex items-center justify-center text-3xl font-black text-gold shadow-2xl uppercase">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="text-center">
                        <h4 class="text-2xl font-black text-main tracking-widest uppercase">{{ $user->name }}</h4>
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-2">{{ __($user->role) }}</p>
                    </div>
                </div>

                <div class="space-y-4 pt-8 border-t border-main/10">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('الحالة') }}</span>
                        <span class="px-3 py-1 bg-{{ $user->status === 'active' ? 'emerald' : 'rose' }}-500/10 border border-{{ $user->status === 'active' ? 'emerald' : 'rose' }}-500/20 text-{{ $user->status === 'active' ? 'emerald' : 'rose' }}-500 text-[9px] font-black uppercase rounded-full">
                            {{ $user->status == 'active' ? __('نشط') : __('موقوف') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('تاريخ الانضمام') }}</span>
                        <span class="text-xs font-bold text-main">{{ $user->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-6">
                <h4 class="text-sm font-black text-main uppercase tracking-widest flex items-center gap-3">
                    <i data-lucide="phone" class="w-4 h-4 text-gold"></i>
                    {{ __('بيانات التواصل') }}
                </h4>
                <div class="space-y-4">
                    <div class="space-y-1">
                        <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('البريد الإلكتروني') }}</p>
                        <p class="text-sm font-bold text-main">{{ $user->email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('رقم الهاتف') }}</p>
                        <p class="text-sm font-bold text-main">{{ $user->phone ?? __('غير مسجل') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Activity -->
        <div class="lg:col-span-2 space-y-8">
            <div class="luxury-card p-10 border border-main/10 min-h-[500px]">
                <h4 class="text-xl font-black text-main mb-12 uppercase tracking-widest flex items-center gap-4">
                    <i data-lucide="activity" class="w-5 h-5 text-gold"></i>
                    {{ __('نشاط المستخدم الأخير') }}
                </h4>
                
                <div class="space-y-10">
                    <!-- Example Activity Timeline item -->
                    <div class="relative flex items-center gap-8 group">
                        <div class="w-12 h-12 rounded-xl bg-gold/5 flex items-center justify-center text-gold border border-gold/10 group-hover:scale-105 transition-transform">
                            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-1 space-y-1">
                            <p class="text-sm font-bold text-main">{{ __('حجز منتج جديد') }}: <span class="text-gold">#{{ rand(1000, 9999) }}</span></p>
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ now()->subHours(5)->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    <div class="relative flex items-center gap-8 group opacity-60">
                        <div class="w-12 h-12 rounded-xl bg-main/5 flex items-center justify-center text-muted/40 border border-main/10">
                            <i data-lucide="log-in" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-1 space-y-1">
                            <p class="text-sm font-bold text-main">{{ __('تسجيل دخول للنظام') }}</p>
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ now()->subDays(1)->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
