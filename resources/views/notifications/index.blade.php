@extends('layouts.admin')

@section('title', __('مركز الإشعارات'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl" x-data="{ showBroadcast: false }">

    {{-- ══════════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-14 h-14 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-lg shadow-gold/10">
                    <i data-lucide="bell" class="w-7 h-7 text-gold"></i>
                </div>
                <div>
                    <h2 class="text-4xl font-black text-main uppercase tracking-widest">{{ __('مركز الإشعارات') }}</h2>
                    <div class="h-1 w-20 bg-gold rounded-full mt-2 shadow-[0_0_12px_rgba(212,175,55,0.5)]"></div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button @click="showBroadcast = !showBroadcast" 
                class="px-8 py-4 bg-gold text-onyx rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all flex items-center gap-3 shadow-lg shadow-gold/20">
                <i data-lucide="plus" class="w-4 h-4" x-show="!showBroadcast"></i>
                <i data-lucide="x" class="w-4 h-4" x-show="showBroadcast" style="display: none;"></i>
                {{ __('إشعار جديد') }}
            </button>

            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="px-8 py-4 bg-gold/10 border border-gold/20 text-gold rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gold hover:text-onyx transition-all flex items-center gap-3 shadow-md">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                    {{ __('تحديد الكل كمقروء') }}
                </button>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         SUCCESS / ERROR FLASH
    ══════════════════════════════════════════════════════ --}}
    @if(session('success'))
    <div class="px-8 py-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl font-black text-sm text-center shadow-lg animate-in fade-in slide-in-from-top-4 duration-700 flex items-center justify-center gap-3">
        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         BROADCAST PANEL — SEND A NOTIFICATION
    ══════════════════════════════════════════════════════ --}}
    <div x-show="showBroadcast" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 -translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-8"
         class="luxury-card border border-gold/15 bg-gradient-to-br from-white/60 to-gold/5 dark:from-[#1A1A1A]/80 dark:to-gold/5 backdrop-blur-md rounded-[2.5rem] shadow-2xl shadow-gold/10 overflow-hidden"
         style="display: none;">

        {{-- Panel Header --}}
        <div class="w-full flex items-center justify-between px-10 py-8 border-b border-gold/10 group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-gold shadow-lg shadow-gold/30 flex items-center justify-center group-hover:rotate-12 transition-transform duration-500">
                    <i data-lucide="megaphone" class="w-7 h-7 text-onyx"></i>
                </div>
                <div class="text-right">
                    <div class="text-xl font-black text-main tracking-tight">{{ __('إرسال إشعار جديد') }}</div>
                    <div class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-1">{{ __('بث رسالة فورية أو مجدولة لفئة مستهدفة') }}</div>
                </div>
            </div>
        </div>

        {{-- Panel Body --}}
        <div>
            <form action="{{ route('notifications.broadcast') }}" method="POST" class="p-10 space-y-8">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Title --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-muted/60 flex items-center gap-2">
                            <i data-lucide="type" class="w-3.5 h-3.5 text-gold/60"></i>
                            {{ __('عنوان الإشعار') }}
                        </label>
                        <input type="text" name="title" required placeholder="{{ __('مثال: تحديث مهم في النظام…') }}"
                               class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-base font-bold text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all placeholder:text-muted/30 placeholder:font-normal">
                    </div>

                    {{-- Target Audience --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-muted/60 flex items-center gap-2">
                            <i data-lucide="target" class="w-3.5 h-3.5 text-gold/60"></i>
                            {{ __('الجمهور المستهدف') }}
                        </label>
                        <div x-data="{ target: 'all' }" class="grid grid-cols-4 gap-3">
                            @foreach([
                                ['value' => 'all',       'label' => 'الكل',      'icon' => 'users',       'color' => 'gold'],
                                ['value' => 'merchants', 'label' => 'التجار',    'icon' => 'store',       'color' => 'indigo'],
                                ['value' => 'customers', 'label' => 'العملاء',   'icon' => 'user',        'color' => 'emerald'],
                                ['value' => 'staff',     'label' => 'الإدارة',   'icon' => 'shield',      'color' => 'amber'],
                            ] as $t)
                            <label class="cursor-pointer">
                                <input type="radio" name="target" value="{{ $t['value'] }}"
                                       x-model="target" class="sr-only"
                                       {{ $t['value'] === 'all' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center gap-2 px-3 py-4 rounded-2xl border-2 text-center transition-all duration-300"
                                     :class="target === '{{ $t['value'] }}' ? 'border-gold bg-gold/10 text-gold shadow-lg shadow-gold/20' : 'border-main/10 bg-white/50 dark:bg-black/20 text-muted/40 hover:border-gold/30'">
                                    <i data-lucide="{{ $t['icon'] }}" class="w-5 h-5"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">{{ __($t['label']) }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Message --}}
                    <div class="space-y-3 lg:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-muted/60 flex items-center gap-2">
                            <i data-lucide="message-square" class="w-3.5 h-3.5 text-gold/60"></i>
                            {{ __('نص الرسالة') }}
                        </label>
                        <textarea name="message" required rows="3" placeholder="{{ __('اكتب نص الإشعار هنا…') }}"
                                  class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all resize-none placeholder:text-muted/30 leading-relaxed"></textarea>
                    </div>

                    {{-- Link (optional) --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-muted/60 flex items-center gap-2">
                            <i data-lucide="link" class="w-3.5 h-3.5 text-gold/60"></i>
                            {{ __('رابط (اختياري)') }}
                        </label>
                        <input type="text" name="link" placeholder="{{ __('https://…') }}"
                               class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all placeholder:text-muted/30">
                    </div>

                    {{-- Scheduled At --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-muted/60 flex items-center gap-2">
                            <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-gold/60"></i>
                            {{ __('جدولة الإرسال (اتركه فارغاً للإرسال الفوري)') }}
                        </label>
                        <input type="datetime-local" name="scheduled_at"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-6 py-4 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all">
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="group relative px-12 py-5 bg-gold text-onyx font-black text-sm uppercase tracking-widest rounded-2xl shadow-2xl shadow-gold/30 hover:shadow-gold/50 hover:scale-105 transition-all duration-500 flex items-center gap-4 overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 rounded-2xl"></div>
                        <i data-lucide="send" class="w-5 h-5 relative z-10 group-hover:-translate-x-1 transition-transform"></i>
                        <span class="relative z-10">{{ __('إرسال الإشعار') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         NOTIFICATIONS LIST
    ══════════════════════════════════════════════════════ --}}
    <div class="space-y-4">

        {{-- Section Header --}}
        <div class="flex items-center gap-4 px-2">
            <h3 class="text-[11px] font-black uppercase tracking-[0.25em] text-muted/40">{{ __('سجل الإشعارات') }}</h3>
            <div class="flex-1 h-px bg-gold/10"></div>
            <span class="text-[9px] font-black uppercase tracking-widest text-gold/60 bg-gold/5 border border-gold/10 px-4 py-1.5 rounded-full">
                {{ $notifications->total() }} {{ __('إشعار') }}
            </span>
        </div>

        <div id="notifications-container" class="space-y-4">
            @forelse($notifications as $notification)
                @include('notifications.partials.item', ['notification' => $notification])
            @empty
                <div id="empty-notifications" class="luxury-card p-24 text-center space-y-8 border border-dashed border-gold/10 bg-gold/5 rounded-[3rem]">
                    <div class="w-32 h-32 bg-gold/5 rounded-full flex items-center justify-center mx-auto border border-gold/5 opacity-30">
                        <i data-lucide="bell-off" class="w-16 h-16 text-gold/50"></i>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-3xl font-black text-main tracking-tight italic opacity-40">{{ __('لا توجد إشعارات بعد') }}</h3>
                        <p class="text-muted/30 text-[10px] font-black uppercase tracking-widest">{{ __('استخدم لوحة الإرسال أعلاه لبدء التواصل مع مستخدميك.') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="luxury-card p-8 border border-gold/10 bg-main/5 text-right rounded-3xl">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.private('App.Models.User.{{ Auth::id() }}')
                .listen('.App\\Events\\NewNotificationEvent', (e) => {
                    console.log('Real-time notification update for list:', e);
                    
                    // Fetch the latest notification HTML
                    fetch("{{ route('notifications.index') }}?latest=1", {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.html) {
                            const container = document.getElementById('notifications-container');
                            const emptyState = document.getElementById('empty-notifications');
                            
                            if (emptyState) emptyState.remove();
                            
                            // Check if already exists
                            if (document.getElementById(`notification-${data.id}`)) return;

                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = data.html;
                            const firstItem = wrapper.firstElementChild;
                            
                            container.prepend(firstItem);
                            lucide.createIcons(); // Re-initialize icons for new item
                        }
                    })
                    .catch(err => console.error('Failed to fetch new notification:', err));
                });
        }
    });
</script>
@endpush
@endsection
