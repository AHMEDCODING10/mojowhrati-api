@extends('layouts.admin')

@section('title', __('طلبات التحقق من التجار'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('طلبات التوثيق') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            {{-- Summary Badges --}}
            <div class="px-6 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
                <i data-lucide="file-check" class="w-4 h-4 text-emerald-500"></i>
                <span class="text-emerald-500 font-black text-sm">{{ $withDocs->count() }} {{ __('رفعوا وثائق') }}</span>
            </div>
            <div class="px-6 py-3 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex items-center gap-3">
                <i data-lucide="user-x" class="w-4 h-4 text-amber-500"></i>
                <span class="text-amber-500 font-black text-sm">{{ $withoutDocs->count() }} {{ __('لم يرفعوا وثائق') }}</span>
            </div>
            <a href="{{ route('merchants.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة لقائمة التجار') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center animate-pulse">
        {{ session('success') }}
    </div>
    @endif

    {{-- ════════════════════════════════════════════════ --}}
    {{-- SECTION 1: Merchants Who Uploaded Documents --}}
    {{-- ════════════════════════════════════════════════ --}}
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.6)]"></div>
            <h3 class="text-xl font-black text-main tracking-widest uppercase">{{ __('يستدعون المراجعة — رفعوا وثائق التوثيق') }}</h3>
        </div>
        <div class="luxury-card overflow-hidden border border-main/10 shadow-2xl bg-card/30 backdrop-blur-xl rounded-[2rem]">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-main/5 border-b border-main/10 text-muted/60 text-[10px] font-black uppercase tracking-widest">
                            <th class="px-8 py-6">{{ __('المتجر') }}</th>
                            <th class="px-8 py-6">{{ __('السجل / الضريبة') }}</th>
                            <th class="px-8 py-6 text-center">{{ __('المستندات') }}</th>
                            <th class="px-8 py-6 text-center">{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-main/5 text-sm">
                        @forelse($withDocs as $merchant)
                            <tr class="group hover:bg-main/5 transition-all">
                                <td class="px-8 py-6 text-main font-bold">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-gold/5 border border-gold/10 flex items-center justify-center text-xl overflow-hidden shadow-2xl shadow-gold/10 group-hover:scale-105 transition-transform">
                                            @if($merchant->logo_url)
                                                <img src="{{ $merchant->logo_url }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="opacity-20 text-gold text-2xl">🏪</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-lg font-black tracking-tight text-main">{{ $merchant->store_name }}</div>
                                            <div class="text-[10px] text-muted/40 font-medium tracking-wider">{{ $merchant->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] bg-main/10 px-2 py-0.5 rounded text-muted/60 font-black tracking-widest uppercase">CR</span>
                                            <p class="text-[11px] font-black text-main">{{ $merchant->commercial_register ?? __('غير متوفر') }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] bg-main/10 px-2 py-0.5 rounded text-muted/60 font-black tracking-widest uppercase">TAX</span>
                                            <p class="text-[11px] font-black text-main">{{ $merchant->tax_number ?? __('غير متوفر') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <button 
                                        onclick="openReviewModal({{ $merchant->id }})" 
                                        class="inline-flex items-center gap-2 text-emerald-500 font-black text-[10px] uppercase tracking-widest hover:text-emerald-400 transition-colors"
                                    >
                                        <i data-lucide="files" class="w-4 h-4"></i>
                                        {{ $merchant->documents_count }} {{ __('وثائق') }}
                                    </button>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-center gap-3">
                                        <button 
                                            onclick="openReviewModal({{ $merchant->id }})" 
                                            class="px-5 py-3 bg-gold/10 border border-gold/20 text-gold rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-gold hover:text-black transition-all flex items-center gap-2"
                                        >
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            {{ __('مراجعة كاملة') }}
                                        </button>
                                        <div class="h-6 w-px bg-main/10"></div>
                                        <form action="{{ route('merchants.approve', $merchant->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-xl hover:bg-emerald-500 hover:text-white transition-all shadow-lg shadow-emerald-500/10" title="{{ __('قبول') }}">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $merchant->id }}, '{{ $merchant->store_name }}')" class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-lg shadow-rose-500/10" title="{{ __('رفض') }}">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center gap-4 opacity-30">
                                        <i data-lucide="inbox" class="w-12 h-12 text-gold"></i>
                                        <p class="font-black uppercase tracking-[0.2em] italic text-xs">{{ __('لا توجد طلبات برفع وثائق بانتظار المراجعة') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: Merchants Registered but No Docs Uploaded --}}
    {{-- ════════════════════════════════════════════════════════ --}}
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <div class="w-3 h-3 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.6)]"></div>
            <h3 class="text-xl font-black text-main tracking-widest uppercase">{{ __('مسجلون — لم يرفعوا أي وثيقة بعد') }}</h3>
        </div>
        <div class="luxury-card overflow-hidden border border-amber-500/10 shadow-2xl bg-card/30 backdrop-blur-xl rounded-[2rem]">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-amber-500/5 border-b border-amber-500/10 text-muted/60 text-[10px] font-black uppercase tracking-widest">
                            <th class="px-8 py-6">{{ __('التاجر') }}</th>
                            <th class="px-8 py-6">{{ __('تاريخ التسجيل') }}</th>
                            <th class="px-8 py-6 text-center">{{ __('الحالة') }}</th>
                            <th class="px-8 py-6 text-center">{{ __('إجراء') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-500/5 text-sm">
                        @forelse($withoutDocs as $merchant)
                            <tr class="group hover:bg-amber-500/5 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500">
                                            <i data-lucide="user" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <div class="text-base font-black tracking-tight text-main">{{ $merchant->store_name }}</div>
                                            <div class="text-[10px] text-muted/40 font-medium">{{ $merchant->user->phone ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-muted/60 text-[11px] font-bold">
                                    {{ $merchant->created_at->format('Y/m/d') }}
                                    <div class="text-[9px] text-muted/30">{{ $merchant->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 border border-amber-500/20 text-amber-500 rounded-xl text-[9px] font-black uppercase tracking-widest">
                                        <i data-lucide="file-x" class="w-3 h-3"></i>
                                        {{ __('لم يرفع وثائق') }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <button onclick="openRejectModal({{ $merchant->id }}, '{{ $merchant->store_name }}')" 
                                        class="px-5 py-3 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all flex items-center gap-2 mx-auto">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        {{ __('رفض / إشعار') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3 opacity-30">
                                        <i data-lucide="check-circle" class="w-10 h-10 text-emerald-500"></i>
                                        <p class="font-black text-xs italic">{{ __('جميع التجار المسجلين رفعوا وثائقهم') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 z-[200] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-md" onclick="closeReviewModal()"></div>
    <div class="absolute top-[10%] left-1/2 -translate-x-1/2 w-full max-w-5xl max-h-[90vh] overflow-y-auto bg-main border border-gold/20 rounded-[3rem] shadow-[0_40px_100px_rgba(0,0,0,0.3)] dark:shadow-[0_0_100px_rgba(212,175,55,0.15)]" dir="rtl">
        
        <!-- Luxury Header -->
        <div class="sticky top-0 z-10 bg-white/95 dark:bg-[#0d0d0d]/95 backdrop-blur-md border-b border-gold/10 px-12 py-12 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <div id="modalLogo" class="w-20 h-20 rounded-full bg-gold/5 border-2 border-gold/20 overflow-hidden flex items-center justify-center text-4xl shadow-xl"></div>
                <div>
                    <h3 id="modalStoreName" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight mb-2"></h3>
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                        <p id="modalEmail" class="text-sm text-gray-500 dark:text-muted/40 font-black uppercase tracking-widest"></p>
                    </div>
                </div>
            </div>
            <button onclick="closeReviewModal()" class="w-12 h-12 flex items-center justify-center bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-full hover:bg-gold hover:text-white dark:hover:text-black transition-all group">
                <i data-lucide="x" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
            </button>
        </div>

        <div class="p-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Data Columns -->
                <div class="lg:col-span-12 xl:col-span-7 space-y-10">
                    
                    <!-- Contact Glass Card -->
                    <div class="p-10 bg-white dark:bg-[#161616] border border-gold/10 dark:border-white/5 rounded-[2.5rem] relative overflow-hidden group shadow-sm">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-gold/10 transition-colors"></div>
                        
                        <h4 class="text-gold text-[14px] font-black uppercase tracking-[0.3em] mb-12 flex items-center gap-4">
                            <span class="w-8 h-px bg-gold/30"></span>
                            {{ __('بيانات التواصل والارتباط') }}
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-gold/5 dark:bg-gold/10 flex items-center justify-center text-gold border border-gold/10 dark:border-gold/20"><i data-lucide="phone" class="w-6 h-6"></i></div>
                                <div>
                                    <div class="text-[13px] text-gray-400 dark:text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('رقم الهاتف') }}</div>
                                    <div id="modalPhone" class="text-lg font-bold text-gray-900 dark:text-white/90"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-500/5 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/10 dark:border-emerald-500/20"><i data-lucide="message-circle" class="w-6 h-6"></i></div>
                                <div>
                                    <div class="text-[13px] text-gray-400 dark:text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('واتساب') }}</div>
                                    <div id="modalWhatsapp" class="text-lg font-bold text-gray-900 dark:text-white/90"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-rose-500/5 dark:bg-pink-500/10 flex items-center justify-center text-rose-500 border border-rose-500/10 dark:border-pink-500/20"><i data-lucide="instagram" class="w-6 h-6"></i></div>
                                <div>
                                    <div class="text-[13px] text-gray-400 dark:text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('إنستقرام') }}</div>
                                    <div id="modalInsta" class="text-lg font-bold text-gray-900 dark:text-white/90"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location and Bio -->
                    <div class="p-10 bg-white dark:bg-[#161616] border border-gold/10 dark:border-white/5 rounded-[2.5rem] space-y-12 relative overflow-hidden group shadow-sm">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-gold/10 transition-colors"></div>

                        <div class="space-y-8">
                            <h4 class="text-gold text-[16px] font-black uppercase tracking-[0.3em] flex items-center gap-4">
                                <span class="w-8 h-px bg-gold/30"></span>
                                {{ __('الموقع والوصف') }}
                            </h4>
                            <div class="flex items-start gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-gold/5 dark:bg-main/10 flex items-center justify-center text-gold border border-gold/10 dark:border-main/20 mt-1 shrink-0"><i data-lucide="map-pin" class="w-6 h-6"></i></div>
                                <div>
                                    <div class="text-[14px] text-gray-400 dark:text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('العنوان') }}</div>
                                    <div id="modalAddress" class="text-lg font-bold text-gray-900 dark:text-white/90 leading-relaxed"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-10 border-t border-gray-100 dark:border-white/5">
                            <div class="text-[16px] text-gray-400 dark:text-muted/40 font-black uppercase tracking-widest mb-6 flex items-center gap-3">
                                <i data-lucide="text-quote" class="w-4 h-4 text-gold"></i>
                                {{ __('وصف المتجر') }}
                            </div>
                            <div id="modalDescription" class="text-lg leading-relaxed text-gray-700 dark:text-white/60 bg-gray-50 dark:bg-black/30 p-10 rounded-[2.5rem] border border-gray-100 dark:border-white/5 italic"></div>
                        </div>
                    </div>
                </div>

                <!-- Documents Space-optimized -->
                <div class="lg:col-span-12 xl:col-span-5 flex flex-col h-full">
                    <div class="flex-1 p-10 bg-white dark:bg-[#161616] border border-gold/10 dark:border-white/5 rounded-[2.5rem] flex flex-col shadow-sm">
                        <h4 class="text-gold text-[16px] font-black uppercase tracking-[0.3em] mb-12 flex items-center gap-4">
                            <span class="w-8 h-px bg-gold/30"></span>
                            {{ __('الوثائق الرسمية') }}
                        </h4>
                        
                        <div id="modalDocuments" class="grid grid-cols-2 gap-5 overflow-y-auto max-h-[600px] custom-scrollbar pr-2">
                            <!-- Documents will be injected here -->
                        </div>
                        
                        <!-- Premium Footer Note -->
                        <div class="mt-auto pt-10 text-[14px] text-gray-400 dark:text-muted/20 font-medium italic text-center">
                            {{ __('يرجى التأكد من مطابقة السجل التجاري مع اسم المتجر والبيانات المدخلة قبل الموافقة') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Luxury Actions -->
            <div class="mt-16 pt-10 border-t border-gold/10 flex flex-wrap items-center justify-between gap-8">
                <div class="flex items-center gap-4 text-gray-400 dark:text-muted/30">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                    <span class="text-[16px] uppercase font-black tracking-widest">{{ __('التحقق بصلاحيات مدير النظام') }}</span>
                </div>

                <div class="flex items-center gap-8">
                    <button onclick="closeReviewAndOpenReject()" class="px-14 py-6 bg-white dark:bg-card border border-rose-500/20 text-rose-500 rounded-2xl font-black text-sm uppercase tracking-[0.2em] hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                        {{ __('رفض مع تقديم ملاحظات') }}
                    </button>

                    <form id="modalApproveForm" method="POST">
                        @csrf
                        <button type="submit" class="px-16 py-6 bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-[0.3em] shadow-[0_20px_40px_rgba(16,185,129,0.2)] hover:bg-emerald-500 hover:scale-105 active:scale-95 transition-all">
                            {{ __('قبول وتفعيل المتجر') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-[110] hidden">
    <div class="absolute inset-0 bg-black/90 backdrop-blur-md" onclick="closeRejectModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-card border border-rose-500/20 rounded-[2rem] shadow-2xl overflow-hidden" dir="rtl">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-10 space-y-8">
                <div class="flex items-center gap-4 text-rose-500">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black">{{ __('رفض طلب التوثيق') }}</h3>
                        <p id="rejectStoreName" class="text-sm opacity-60"></p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted/40 px-2">{{ __('سبب الرفض (سيصل كإشعار للتاجر)') }}</label>
                    <textarea name="notes" required rows="5" class="w-full bg-main/5 border border-main/10 rounded-2xl p-6 text-main focus:border-rose-500/50 outline-none transition-all placeholder:text-muted/20" placeholder="{{ __('مثال: صورة السجل التجاري غير واضحة، يرجى إعادة رفع الوثائق...') }}"></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="flex-1 py-5 bg-rose-500 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-rose-500/20 hover:bg-rose-600 transition-all">
                        {{ __('تأكيد الرفض') }}
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="px-8 py-5 bg-main/10 rounded-2xl font-black text-sm text-muted/40 hover:text-main transition-colors">
                        {{ __('إلغاء') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Global registry for merchants - Safely encoded by Blade
    const pendingMerchants = @json($pendingMerchants->each->append(['logo_url', 'banner_url']));
    let currentMerchant = null;

    function openReviewModal(id) {
        try {
            // Find merchant in the safe global registry
            const merchant = pendingMerchants.find(m => m.id === id);
            if (!merchant) {
                console.error('Merchant not found in registry (ID: ' + id + ')');
                return;
            }

            currentMerchant = merchant;
            
            document.getElementById('modalStoreName').textContent = merchant.store_name || '';
            document.getElementById('modalEmail').textContent = merchant.user ? merchant.user.email : '-';
            document.getElementById('modalPhone').textContent = merchant.contact_number || (merchant.user ? merchant.user.phone : '-');
            document.getElementById('modalWhatsapp').textContent = merchant.whatsapp_number || '-';
            document.getElementById('modalInsta').textContent = merchant.instagram_handle || '-';
            document.getElementById('modalAddress').textContent = merchant.address || '-';
            document.getElementById('modalDescription').textContent = merchant.store_description || '-';

            // Logo Build
            const logoDiv = document.getElementById('modalLogo');
            if (merchant.logo_url) {
                logoDiv.innerHTML = `<img src="${merchant.logo_url}" class="w-full h-full object-cover">`;
            } else {
                logoDiv.innerHTML = `<span class="opacity-20 text-gold text-4xl">🏪</span>`;
            }

            // Documents Build
            const docsDiv = document.getElementById('modalDocuments');
            docsDiv.innerHTML = '';
            
            // Explicitly handle documents array
            let docs = [];
            if (merchant.documents) {
                if (Array.isArray(merchant.documents)) {
                    docs = merchant.documents;
                } else if (typeof merchant.documents === 'string') {
                    try { docs = JSON.parse(merchant.documents); } catch(e) { docs = [merchant.documents]; }
                }
            }

            if (docs.length > 0) {
                docs.forEach((doc) => {
                    // Safety check: ensure doc is a string
                    if (!doc || typeof doc !== 'string') {
                        // If it's an object with a path or url, use that
                        if (doc && typeof doc === 'object') {
                            doc = doc.path || doc.url || doc.file || null;
                        }
                        // If still not a string, skip it
                        if (typeof doc !== 'string') return;
                    }

                    const url = doc.startsWith('http') ? doc : `/storage/${doc}`;
                    docsDiv.innerHTML += `
                        <a href="${url.replace(/"/g, '&quot;')}" target="_blank" class="group relative aspect-square rounded-[2rem] overflow-hidden border-2 border-gray-100 dark:border-white/5 hover:border-gold/50 transition-all bg-gray-50 dark:bg-[#0a0a0a] shadow-sm">
                            <img src="${url.replace(/"/g, '&quot;')}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 flex flex-col items-center justify-end pb-8 transition-opacity">
                                <div class="w-12 h-12 rounded-full bg-gold flex items-center justify-center text-black shadow-2xl scale-75 group-hover:scale-100 transition-transform">
                                    <i data-lucide="maximize-2" class="w-5 h-5"></i>
                                </div>
                                <span class="text-[9px] font-black text-gold uppercase tracking-[0.2em] mt-3">{{ __('توسيع العرض') }}</span>
                            </div>
                        </a>
                    `;
                });
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                docsDiv.innerHTML = `<div class="col-span-2 flex flex-col items-center justify-center py-20 bg-gray-50 dark:bg-white/5 rounded-3xl border border-gray-100 dark:border-white/5">
                    <i data-lucide="files" class="w-12 h-12 text-gray-300 dark:text-muted/20 mb-4"></i>
                    <p class="text-xs text-gray-500 dark:text-muted/40 font-black italic">{{ __('لا توجد وثائق مرفقة مع الطلب') }}</p>
                </div>`;
            }

            // Dynamic Action Form
            document.getElementById('modalApproveForm').action = `/merchants/${id}/approve`;
            
            document.getElementById('reviewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

        } catch (error) {
            console.error('Error loading merchant detail:', error);
            alert('عذراً، حدث خطأ تقني في عرض بيانات التاجر.');
        }
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openRejectModal(id, storeName) {
        document.getElementById('rejectStoreName').textContent = storeName;
        document.getElementById('rejectForm').action = `/merchants/${id}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    function closeReviewAndOpenReject() {
        if (!currentMerchant) return;
        closeReviewModal();
        openRejectModal(currentMerchant.id, currentMerchant.store_name);
    }
</script>
@endpush
@endsection
