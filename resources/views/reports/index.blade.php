@extends('layouts.admin')

@section('title', __('التقارير والإحصائيات'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-10 pt-4 pb-8 border-b border-gold/10 relative">
        <div class="absolute right-0 top-0 w-32 h-32 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative">
            <h2 class="text-4xl lg:text-5xl font-black text-main uppercase tracking-[0.2em] leading-tight flex items-center gap-4">
                {{ __('التقارير والبيانات') }}
                <div class="w-12 h-12 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-lg shadow-gold/10">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-gold animate-pulse"></i>
                </div>
            </h2>
            <div class="flex items-center gap-3 mt-4">
                <div class="h-1.5 w-20 bg-gold shadow-[0_0_20px_rgba(212,175,55,0.6)] rounded-full"></div>
                <div class="h-1.5 w-6 bg-gold/30 rounded-full"></div>
                <p class="text-[10px] text-muted/60 font-black uppercase tracking-[0.3em] mr-2">{{ __('تصدير وطباعة التحليلات') }}</p>
            </div>
        </div>
    </div>

    <!-- Report Generator Panel -->
    <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/20 rounded-[2.5rem] p-8 lg:p-10 shadow-2xl shadow-gold/5 relative overflow-hidden group mb-16">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="flex items-center gap-4 mb-8 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-xl font-black text-main tracking-tight">{{ __('مولد التقارير المتقدم') }}</h4>
                <p class="text-[9px] text-muted/50 font-black uppercase tracking-widest mt-1">{{ __('حدد نوع البيانات واستخرجها') }}</p>
            </div>
        </div>

        <form action="{{ route('reports.generate') }}" method="GET" class="relative z-10 space-y-8" target="_blank" x-data="{ actionType: 'print' }" @submit="if(actionType === 'excel') { $event.preventDefault(); window.location.href = $el.action + '?' + new URLSearchParams(new FormData($el)).toString(); }">
            <input type="hidden" name="action_type" x-model="actionType">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Data Source -->
                <div class="space-y-3">
                    <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                        <i data-lucide="database" class="w-3.5 h-3.5 text-gold/80"></i>
                        {{ __('نوع البيانات') }}
                    </label>
                    <div class="relative">
                        <select name="data_source" required class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-sm font-black text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all appearance-none cursor-pointer">
                            <option value="users">{{ __('المستخدمين والعملاء') }}</option>
                            <option value="merchants">{{ __('التجار والمتاجر') }}</option>
                            <option value="products">{{ __('المنتجات والمخزون') }}</option>
                            <option value="bookings">{{ __('الطلبات والمبيعات') }}</option>
                            <option value="banners">{{ __('الإعلانات والحملات') }}</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 absolute left-6 top-1/2 -translate-y-1/2 text-muted/40 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Format -->
                <div class="space-y-3">
                    <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                        <i data-lucide="layout-list" class="w-3.5 h-3.5 text-gold/80"></i>
                        {{ __('صيغة التقرير') }}
                    </label>
                    <div class="relative">
                        <select name="format" required class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-sm font-black text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all appearance-none cursor-pointer">
                            <option value="detailed">{{ __('تفصيلي (جميع السجلات)') }}</option>
                            <option value="summary">{{ __('ملخص (إحصائي)') }}</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 absolute left-6 top-1/2 -translate-y-1/2 text-muted/40 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Date From -->
                <div class="space-y-3">
                    <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gold/80"></i>
                        {{ __('من تاريخ') }}
                    </label>
                    <input type="date" name="date_from" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-3.5 text-sm font-black text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all cursor-pointer text-left" dir="ltr">
                </div>

                <!-- Date To -->
                <div class="space-y-3">
                    <label class="text-[10px] text-muted/60 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-gold/80"></i>
                        {{ __('إلى تاريخ') }}
                    </label>
                    <input type="date" name="date_to" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-3.5 text-sm font-black text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all cursor-pointer text-left" dir="ltr">
                </div>
            </div>

            <div class="w-full h-px bg-gradient-to-r from-transparent via-gold/10 to-transparent my-6"></div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-4">
                <button type="submit" @click="actionType = 'excel'" class="w-full sm:w-auto px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10 hover:border-emerald-500/50 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    {{ __('تصدير Excel') }}
                </button>
                
                <button type="submit" @click="actionType = 'print'" class="w-full sm:w-auto px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest bg-gold text-onyx shadow-lg shadow-gold/20 hover:shadow-gold/40 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 group/btn">
                    <i data-lucide="printer" class="w-4 h-4 transition-transform group-hover/btn:-translate-y-0.5"></i>
                    {{ __('طباعة المخرجات / PDF') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Dashboard Context Header -->
    <div class="flex items-center gap-4 mb-6 px-2">
        <h4 class="text-2xl font-black text-main tracking-tight">{{ __('نظرة عامة سريعة') }}</h4>
        <div class="h-px w-20 bg-main/10 flex-1"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="luxury-card p-8 border border-main/10 bg-main/5">
            <div class="flex items-center justify-between mb-4">
                <i data-lucide="shopping-bag" class="w-8 h-8 text-gold opacity-30"></i>
                <span class="text-emerald-500 text-[10px] font-black uppercase tracking-widest">+12.5%</span>
            </div>
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-1">{{ __('إجمالي المبيعات') }}</p>
            <h3 class="text-3xl font-black text-main">{{ number_format($totalSales ?? 0) }}</h3>
        </div>
        
        <div class="luxury-card p-8 border border-main/10 bg-main/5">
            <div class="flex items-center justify-between mb-4">
                <i data-lucide="users" class="w-8 h-8 text-gold opacity-30"></i>
                <span class="text-emerald-500 text-[10px] font-black uppercase tracking-widest">+8.2%</span>
            </div>
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-1">{{ __('عملاء جدد') }}</p>
            <h3 class="text-3xl font-black text-main">{{ number_format($newUsersCount ?? 0) }}</h3>
        </div>

        <div class="luxury-card p-8 border border-main/10 bg-main/5">
            <div class="flex items-center justify-between mb-4">
                <i data-lucide="trending-up" class="w-8 h-8 text-gold opacity-30"></i>
                <span class="text-emerald-500 text-[10px] font-black uppercase tracking-widest">+15.7%</span>
            </div>
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-1">{{ __('متوسط قيمة الطلب') }}</p>
            <h3 class="text-3xl font-black text-main">{{ number_format($avgOrderValue ?? 0) }}</h3>
        </div>

        <div class="luxury-card p-8 border border-main/10 bg-main/5">
            <div class="flex items-center justify-between mb-4">
                <i data-lucide="clock" class="w-8 h-8 text-gold opacity-30"></i>
                <span class="text-emerald-500 text-[10px] font-black uppercase tracking-widest">مستقر</span>
            </div>
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-1">{{ __('زيارات اليوم') }}</p>
            <h3 class="text-3xl font-black text-main">{{ number_format($dailyVisits ?? 0) }}</h3>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="luxury-card p-10 border border-main/10">
            <h4 class="text-xl font-black text-main mb-8 uppercase tracking-widest flex items-center gap-4">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-gold"></i>
                {{ __('نمو الإيرادات') }}
            </h4>
            <div class="h-80 w-full bg-main/5 rounded-3xl flex items-center justify-center italic text-muted/20">
                [ Revenue Chart Context ]
            </div>
        </div>
        
        <div class="luxury-card p-10 border border-main/10">
            <h4 class="text-xl font-black text-main mb-8 uppercase tracking-widest flex items-center gap-4">
                <i data-lucide="pie-chart" class="w-5 h-5 text-gold"></i>
                {{ __('توزيع المنتجات ب العيار') }}
            </h4>
            <div class="h-80 w-full bg-main/5 rounded-3xl flex items-center justify-center italic text-muted/20">
                [ Distribution Chart Context ]
            </div>
        </div>
    </div>
</div>
@endsection
