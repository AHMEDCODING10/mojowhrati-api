@extends('layouts.admin')

@section('title', __('إدارة أسعار العملات'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 animate-fade-in-down">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">
                {{ __('إدارة أسعار العملات') }}
            </h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-4">Currency Exchange Management & Global Rates</p>
        </div>
        
        <div class="flex items-center gap-4 bg-white/5 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">النظام متصل • تحديث فوري</span>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="max-w-4xl p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center flex items-center justify-center gap-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="max-w-5xl">
        <form action="{{ route('currencies.update') }}" method="POST" class="space-y-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- USD to YER card -->
                <div class="luxury-card p-10 group hover:border-gold/30 transition-all duration-700 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-gold/10 transition-all"></div>
                    
                    <div class="flex items-center gap-6 mb-10 relative">
                        <div class="w-16 h-16 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-700">
                            <i data-lucide="dollar-sign" class="w-8 h-8 text-gold drop-shadow-[0_0_8px_rgba(212,175,55,0.4)]"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-main tracking-widest uppercase">الدولار الأمريكي</h3>
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-1">USD (United States Dollar)</p>
                        </div>
                    </div>

                    <div class="space-y-4 relative">
                        <label class="block text-[10px] font-black uppercase text-muted/40 tracking-widest mr-1">سعر الصرف مقابل الريال اليمني (USD to YER)</label>
                        <div class="relative group">
                            <input type="number" step="0.01" name="usd_to_yer" value="{{ $usd_to_yer }}" 
                                class="input-luxury w-full py-6 pr-32 text-2xl font-black focus:scale-[1.01] transition-transform duration-500">
                            <div class="absolute right-8 top-1/2 -translate-y-1/2 flex items-center gap-3">
                                <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
                                <span class="text-xs font-black text-gold uppercase tracking-widest">YER</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SAR to YER card -->
                <div class="luxury-card p-10 group hover:border-gold/30 transition-all duration-700 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-gold/10 transition-all"></div>
                    
                    <div class="flex items-center gap-6 mb-10 relative">
                        <div class="w-16 h-16 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-700">
                            <i data-lucide="coins" class="w-8 h-8 text-gold drop-shadow-[0_0_8px_rgba(212,175,55,0.4)]"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-main tracking-widest uppercase">الريال السعودي</h3>
                            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-1">SAR (Saudi Riyal)</p>
                        </div>
                    </div>

                    <div class="space-y-4 relative">
                        <label class="block text-[10px] font-black uppercase text-muted/40 tracking-widest mr-1">سعر الصرف مقابل الريال اليمني (SAR to YER)</label>
                        <div class="relative group">
                            <input type="number" step="0.01" name="sar_to_yer" value="{{ $sar_to_yer }}" 
                                class="input-luxury w-full py-6 pr-32 text-2xl font-black focus:scale-[1.01] transition-transform duration-500">
                            <div class="absolute right-8 top-1/2 -translate-y-1/2 flex items-center gap-3">
                                <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
                                <span class="text-xs font-black text-gold uppercase tracking-widest">YER</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cross calculations -->
                <div class="luxury-card p-10 md:col-span-2 group hover:border-gold/30 transition-all duration-700 relative overflow-hidden">
                   <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-inner">
                                <i data-lucide="refresh-cw" class="w-8 h-8 text-gold"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-main tracking-widest uppercase">التحويل البيني</h3>
                                <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-1">USD to SAR Cross Rate (Fixed Calculations)</p>
                            </div>
                        </div>

                        <div class="flex-1 max-w-md w-full">
                            <div class="relative group">
                                <input type="number" step="0.01" name="usd_to_sar" value="{{ $usd_to_sar }}" 
                                    class="input-luxury w-full py-6 pr-52 text-2xl font-black focus:scale-[1.01] transition-transform duration-500">
                                <div class="absolute right-8 top-1/2 -translate-y-1/2 flex items-center gap-3">
                                    <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
                                    <span class="text-[10px] font-black text-gold uppercase tracking-widest">SAR per 1 USD</span>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="group relative px-16 py-6 rounded-2xl bg-gold text-onyx font-black text-sm uppercase tracking-[0.3em] overflow-hidden shadow-[0_20px_40px_rgba(212,175,55,0.3)] hover:shadow-[0_25px_60px_rgba(212,175,55,0.5)] hover:scale-105 active:scale-95 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <span class="relative z-10 flex items-center gap-4">
                        {{ __('حفظ وتحديث الأسعار') }}
                        <i data-lucide="save" class="w-5 h-5 transition-transform group-hover:rotate-12 duration-500"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
